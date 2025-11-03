<?php
    namespace App\Controllers;

    use App\Models\CustomerModel;
    use App\Models\AppointmentModel;
    use App\Models\CartModel;
    use App\Models\Staff;

    class Henisha extends BaseController
    {
        protected $helpers = ["custom"];
        
        // to import customers
        public function index($salon_id,$new_salon_id)
        {
            $filename = 'public/salon_customer.csv'; // Path to your CSV file
            $total_customer = $no = 0;
            $insert_data = array();
            echo "<pre>";
            // Open the file in read mode
            if (($handle = fopen($filename, 'r')) !== false) {
                // Loop through each line
                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    if($row[0] == $salon_id) {
                        $no++;
                        // if($no <= 10) {
                            if(trim($row[2]) == "") {
                                $name = $row[1];
                            } else {
                                $name = $row[2];
                            }
                            $insert_data[] = array(
                                'old_customer_id' => $row[9],
                                'name' => $name,
                                'phone' => $row[11],
                                'email' => $row[3],
                                'password' => md5($row[11]),
                                'gender' => $row[6],
                                'marketing_email' => $row[13],
                                'promotional_sms' => $row[14],
                                'referral_source_id' => $row[5],
                                'notification_settings' => $row[4],
                                'note' => $row[12],
                                'is_sync_with_google' => 0,
                                'companyId' => $new_salon_id,
                                "addedBy" => 1,
                                "updatedBy" => 1,
                                "createdAt" => $row[7],
                                "updatedAt" => $row[8]
                            );
                        // }
                    }
                }
                fclose($handle);

                $customerModel = new CustomerModel();

                // Only insert if there's data
                if (!empty($insert_data)) {
                    $customerModel->insertBatch($insert_data);
                    echo "Inserted " . count($insert_data) . " records.";
                } else {
                    echo "No data to insert.";
                }
            } else {
                echo "Failed to open file.";
            }
        }

        public function import_old_data()
        {
            // $model = new AppointmentModel;
            // $model->update(75594,["status" => 1]);

            // $model = new CartModel;
            // $model->where("appointmentId",75594)->set(["is_cancelled" => 0])->update();

            // $model = new CustomerModel;
            // $_cust = $model->where("phone","07552927033")->first();
            // print_r ($_cust);
            // exit;

            $model = db_connect();
            $appointment = $model->table("appointments a");
            $appointment = $appointment->join("customers c","c.id=a.customerId");
            $appointment = $appointment->select("c.name,c.email,c.phone,a.*");
            // $appointment = $appointment->where("a.customerId",12387);
            // $appointment = $appointment->orderBy("a.id","desc");
            $appointments = $appointment->where("DATE(a.bookingDate)",'2025-08-22')->get()->getResultArray();
            
            $model = new CartModel;
            $carts = $model->where("appointmentId",$appointments[0]["id"])->get()->getResultArray();
            echo "<pre>";
            print_r ($appointments);
            print_r ($carts);
            exit;
            // echo "<pre>";
            // exit;
            $filename = "public/old_db_tables/emb_booking.csv"; // path to your CSV file
            if (($handle = fopen($filename, "r")) !== false) {
                $no = 0;
                $model = new CustomerModel;             
                while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                    // if($no <= 10) {
                        if($row[0] == 9) {
                            $no++;
                            // print_r ($row);
                            $customer_id = 0;
                            $customer = $model->select("id")->where("phone",$row[4])->where("companyId",1)->first();
                            if($customer) {
                                $customer_id = $customer["id"];
                            } else {
                                $model->insert(["old_customer_id" => $row[5],"name" => $row[3],"phone" => $row[4]]);
                                $customer_id = $model->getInsertID();
                            }
                            $status = 0;
                            switch ($row[12]) {
                                case 'new':
                                    $status = 1;
                                    break;
                                
                                case 'completed':
                                    $status = 2;
                                    break;

                                case 'no_show':
                                    $status = 3;
                                    break;
                            }
                            $bookedFrom = 0;
                            switch ($row[2]) {
                                case 'Salon':
                                    $bookedFrom = 3;
                                    break;

                                case 'Treatwell':
                                    $bookedFrom = 2;
                                    break;
                                
                                case 'Online':
                                    $bookedFrom = 1;
                                    break;
                            }
                            $type = "O";
                            switch ($row[17]) {
                                case 'APPO':
                                    $type = "Y";
                                    break;
                                
                                case 'WALKIN':
                                    $type = "N";
                                    break;
                            }
                            $insert_data = array(
                                "uniq_id" => trim($row[1]),
                                "customerId" => $customer_id,
                                "subTotal" => trim($row[10]),
                                "extra_discount" => 0,
                                "discountAmt" => 0,
                                "discountId" => 0,
                                "finalAmt" => trim($row[10]),
                                "bookingDate" => date("Y-m-d H:i:s",strtotime($row[14])),
                                "status" => $status,
                                "bookedFrom" => $bookedFrom,
                                "note" => "",
                                "salon_note" => trim($row[16]),
                                "type" => $type,
                                "flag" => "Y",
                                "companyId" => 1,
                                "is_old_data" => 2,
                                "old_booking_id" => trim($row[8]),
                                "old_ap_start_date" => trim($row[14]),
                                "old_ap_end_date" => trim($row[15]),
                                "old_google_calendar_event_id" => trim($row[18]),
                                "old_wa_msg_sent" => trim($row[19]),
                                "old_status" => trim($row[12]),
                                "old_booked_from" => trim($row[2]),
                                "old_type" => trim($row[17]),
                                "addedDate" => date("Y-m-d H:i:s",strtotime($row[6])),
                                "addedBy" => 1,
                                "updatedBy" => 1,
                                "createdAt" => date("Y-m-d H:i:s",strtotime($row[6])),
                                "updatedAt" => date("Y-m-d H:i:s")
                            );
                            // print_r ($insert_data);
                            $db = db_connect();
                            $db->table("appointments")->insert($insert_data);
                            // echo $no;
                        }
                    // }
                }
                // echo $no;
                fclose($handle);
            } else {
                echo "Failed to open the file.";
            }
        }

        public function import_old_cart()
        {
            $db = db_connect();
            $staff = $db->table('appointments a');
            $staff->select("c.date,c.staffId");
            $staff->join("carts c","c.appointmentId=a.id");
            $staff->where("a.is_old_data",2);
            $staff->where("DATE(a.updatedAt)",'2025-07-12');
            $carts = $staff->get()->getResultArray();
            
            // $carts = $db->table("carts")->select("date,staffId")->where("is_old_data",2)->get()->getResultArray();
            if($carts) {
                foreach($carts as $cart) {
                    $db->table("staff_timings")->insert(["staffId" => $cart["staffId"],"date" => $cart["date"],"stime" => "09:00:00","etime" => "20:00:00","companyId" => 1,"isRepeat" => "N"]);
                }
            }
            exit;
            
            $old_appointments = [];
            $model = new AppointmentModel;
            $appointment = $model->where("is_old_data",2)->where('DATE(updatedAt) = "2025-07-12"')->get()->getResultArray();
            foreach ($appointment as $key => $val) {
                $old_appointments[$val['old_booking_id']] = array("id" => $val["id"],"uniq_id" => $val["uniq_id"]);
            }
            // print_r (count($old_appointments));
            // echo json_encode($old_appointments);
            // exit;

            $appointments = json_decode(file_get_contents("public/old_db_tables/appointments.json"),true);
            $staffs = [112 => ["id" => 2,"color" => "#e5d315"],125 => ["id" => 3,"color" => "#edb6e3"],117 => ["id" => 4,"color" => "#d71414"],111 => ["id" => 20,"color" => "#e77e7e"],113 => ["id" => 22,"color" => "#8fa8dc"],118 => ["id" => 23,"color" => "#abf28d"]];

            $db = db_connect();
            $filename = "public/old_db_tables/emb_appointment.csv"; // path to your CSV file
            if (($handle = fopen($filename, "r")) !== false) {
                $no = 0;
                while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                    $no++;
                    // if($no <= 100) {
                        $appointment_id = isset($row[15]) && isset($old_appointments[$row[15]]) ? (int) $old_appointments[$row[15]]['id'] : 0;
                        echo $appointment_id."<br>";
                        if($appointment_id > 0) {
                            $no++;  
                            $start = new \DateTime($row[3]);
                            $end = new \DateTime($row[4]);
                            $interval = $start->diff($end);
                            $duration_minutes = ($interval->h * 60) + $interval->i;

                            // to get service group name
                            $serviceId = 0;
                            $service_group = $db->table("service_groups")->select("id")->where("name",trim($row[7]))->get()->getRowArray();
                            if($service_group) {
                                $serviceId = $service_group["id"];
                            } else {
                                $serviceId = 0;
                                // $db->table("service_groups")->insert(["name" => trim($row[7]),"position" => rand(101,200),"is_old_data" => 2,"company_id" => 1]);
                                // $serviceId = $db->insertID();
                            }
                            // to get service name
                            $serviceSubId = 0;
                            $serviceSub = $db->table("services")->select("id")->where("name",trim($row[6]))->get()->getRowArray();
                            if($serviceSub) {
                                $serviceSubId = $serviceSub["id"];
                            } else {
                                $serviceSubId = 0;
                                // $db->table("services")->insert(["service_group_id" => $serviceId,"name" => trim($row[6]),"is_old_data" => 2,"company_id" => 1]);
                                // $serviceSubId = $db->insertID();
                            }
                            $isComplete = "N";
                            if(strtotime(date("Y-m-d",strtotime($row[3]))) > strtotime(date("Y-m-d"))) {
                                $isComplete = "Y";
                            }
                            $uniq_id = "";
                            if(isset($row[15]) && isset($old_appointments[$row[15]])) {
                                $uniq_id  = $old_appointments[$row[15]]['uniq_id'];
                            }
                            $insert_data = array(
                                "uniq_id" => $uniq_id,
                                "appointmentId" => $appointment_id,
                                "date" => date("Y-m-d",strtotime($row[3])),
                                "stime" => date("H:i:s",strtotime($row[3])),
                                "duration" => $duration_minutes,
                                "etime" => date("H:i:s",strtotime($row[4])),
                                // "staffId" => isset($staffs[$row[0]]['id']) ? $staffs[$row[0]]['id'] : 24,
                                "staffId" => 24,
                                "serviceId" => $serviceId,
                                "serviceNm" => trim($row[7]),
                                "caption" => "",
                                "serviceSubId" => $serviceSubId,
                                "amount" => $row[1],
                                "message" => $row[7]."-\n".$row[6],
                                "isComplete" => $isComplete,
                                "color" => isset($staffs[$row[0]]['color']) ? $staffs[$row[0]]['color'] : "#000000",
                                "isStaffBusy" => 0,
                                "is_turn" => 0,
                                "is_done" => 0,
                                "is_cancelled" => 0,
                                "is_removed_from_cart" => 0,
                                "companyId" => 1,
                                "addedBy" => 1,
                                "updatedBy" => 1,
                                "createdAt" => date("Y-m-d H:i:s",strtotime($row[10])),
                                "updatedAt" => date("Y-m-d H:i:s"),
                                "old_uid" => $row[0],
                                "is_old_data" => 2,
                                "old_staff_id" => $row[0]
                            );
                            // print_r ($insert_data);
                            $cart_id = $db->table("carts")->insert($insert_data);
                            
                            $entries = array(
                                'uniq_id' => $uniq_id,
                                'appointment_id' => $appointment_id,
                                'date' => date("Y-m-d",strtotime($row[3])),
                                'stime' => date("H:i:s",strtotime($row[3])),
                                'duration' => $duration_minutes,
                                'etime' => date("H:i:s",strtotime($row[4])),
                                'staff_id' => 24,
                                'service_id' => $serviceSubId,
                                'service_group_id' => $serviceId,
                                'price' => $row[1],
                                'company_id' => 1,
                                'created_at' => date("Y-m-d H:i:s"),
                                "cart_id" => $cart_id,
                                "caption" => ""
                            );
                            $db->table("entries")->insert($entries);
                        }
                    // }
                }
                fclose($handle);
            } else {
                echo "Failed to open the file.";
            }
        }
        
        public function remove_old_booking()
        {
            $model = new AppointmentModel;
            $items = $model->select("id")->where("customerId",3400)->get()->getResultArray();
            echo count($items);
            // $model = new AppointmentModel;
            // $appointments = $model->where('is_old_data',2)->get()->getResultArray();
            // $db = db_connect();
            // foreach($appointments as $appointment) {
            //     if(strtotime(date("Y-m-d",strtotime($appointment["bookingDate"]))) < strtotime(date("Y-m-d"))) {
            //         $db->table("appointments")->where("id",$appointment["id"])->update(["status" => 2]);
            //     }
            // }
            // echo $no;
            
            // $model = new AppointmentModel;
            // $items = $model->select("id")->where("is_old_data",0)->get()->getResultArray();
            if($items) {
                $db = db_connect();
                foreach($items as $item) {
                    $db->table("appointments")->where("id",$item["id"])->delete();
                    $db->table("carts")->where("appointmentId",$item["id"])->delete();
                    $db->table("entries")->where("appointment_id",$item["id"])->delete();
                }
            }
        }
    }