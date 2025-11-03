<?php
    namespace App\Controllers;
    
    use App\Models\CustomerModel;
    use App\Models\AppointmentModel;
    use App\Models\CartModel;
    use App\Models\EntryModel;
    use App\Models\Staff;

    class Home extends BaseController
    {
        protected $helpers = ["custom"];

        public function index()
        {
            return redirect()->route('admin');
            $api_data = array("key" => APP_KEY,"tag" => "home","company_id" => COMPANY_ID);
            $response = callApi(API_BASE_URL."api/home",$api_data);
            if(isset($response["status"]) && $response["status"] == RESPONSE_FLAG_SUCCESS)
            {
                $data["company"] = $response["data"];
                return view('index',$data);
            }
        }

        public function about_us()
        {
            return redirect()->route('admin');
            $response = company("about_company,isActive,company_logo,banner");
            $data["privacy_policy"] = $response["data"]["about_company"];
            $data["title"] = "About Us";

            return view('privacy_policy',$data);
        }

        public function treatments()
        {
            return redirect()->route('admin');
            $api_data = array("key" => APP_KEY,"tag" => "treatments","company_id" => COMPANY_ID);
            $response = callApi(API_BASE_URL."api/treatments",$api_data);
            if(isset($response["status"]) && $response["status"] == RESPONSE_FLAG_SUCCESS)
            {
                $data["treatments"] = $response["data"];
                return view('treatments',$data);
            }
        }

        public function treatment($id = "")
        {
            return redirect()->route('admin');
            if($id != "")
            {
                $api_data = array("key" => APP_KEY,"tag" => "treatment","company_id" => COMPANY_ID,'treatment_id' => $id);
                $response = callApi(API_BASE_URL."api/treatment",$api_data);
                if(isset($response["status"]) && $response["status"] == RESPONSE_FLAG_SUCCESS)
                {
                    $data["treatments"] = $response["data"];

                    $data["other_treatments"] = array();
                    $api_data = array("key" => APP_KEY,"tag" => "treatments","company_id" => COMPANY_ID);
                    $response = callApi(API_BASE_URL."api/treatments",$api_data);
                    if(isset($response["status"]) && $response["status"] == RESPONSE_FLAG_SUCCESS)
                        $data["other_treatments"] = $response["data"];

                    return view('treatment_new',$data);
                    // return view('treatment',$data);
                } else 
                    return redirect()->route('treatments');
            } else 
                return redirect()->route('treatments');
        }

        public function offers()
        {
            return redirect()->route('admin');
            return view('offers');
        }

        public function gallery()
        {
            return redirect()->route('admin');
            $data["photos"] = array();
            $api_data = array("key" => APP_KEY,"tag" => "photos","company_id" => COMPANY_ID);
            $response = callApi(API_BASE_URL."api/photos",$api_data);
            if(isset($response["status"]) && $response["status"] == RESPONSE_FLAG_SUCCESS)
                $data["photos"] = $response["data"];

            return view('gallery',$data);
        }

        public function contact_us()
        {
            return redirect()->route('admin');
            $data["company"] = array();
            $api_data = array("key" => APP_KEY,"tag" => "company","company_id" => COMPANY_ID,"columns" => "company_address,company_phone,company_email,isActive,company_logo,banner,facebook_link,google_link,instagram_link,code");
            $response = callApi(API_BASE_URL."api/company",$api_data);
            if(isset($response["status"]) && $response["status"] == RESPONSE_FLAG_SUCCESS)
                $data["company"] = $response["data"];

            return view('contact_us',$data);
        }

        public function send_inquiry()
        {
            $session = session();
            $post = $this->request->getVar();
            $post['key'] = APP_KEY;
            $post['tag'] = "send_inquiry";
            $post['company_id'] = COMPANY_ID;
            $response = callApi(API_BASE_URL."api/send_inquiry",$post);
            if(isset($response["status"]) && $response["status"] == RESPONSE_FLAG_SUCCESS)
                $session->setFlashData("success",$response["message"]);
            else 
                $session->setFlashData("error",$response["message"]);

            return redirect()->route('contact-us');
        }

        public function privacy_policy()
        {
            return redirect()->route('admin');
            $response = company("privacy_policy,isActive,company_logo,banner");
            $data["privacy_policy"] = $response["data"]["privacy_policy"];
            $data["title"] = "Privacy Policy";
            return view('privacy_policy',$data);
        }

        public function parking_instructions()
        {
            return redirect()->route('admin');
            $response = company("parking_instructions,isActive,company_logo,banner");
            $data["privacy_policy"] = $response["data"]["parking_instructions"];
            $data["title"] = "Parking Instructions";
            return view('privacy_policy',$data);
        }

        public function all_sub_services()
        {
            $post = $this->request->getVar();

            $data['services'] = array();
            $data['currency'] = "";

            $api_data = array("key" => APP_KEY,"tag" => "sub_treatments","company_id" => COMPANY_ID,"service_id" => $post["serviceId"]);
            $response = callApi(API_BASE_URL."api/sub_treatments",$api_data);
            if(isset($response["status"]) && $response["status"] == RESPONSE_FLAG_SUCCESS)
            {
                $data['services'] = $response["data"]["sub_treatments"];
                $data['currency'] = $response["data"]["currency"];
            }
            $data['service_name'] = $post['serviceNm'];
            $data['flag'] = $post['flag'];

            $html = view('sub_service_list',$data);
            echo json_encode(array("status" => 1,"content" => $html));
            exit;
        }

        public function add_service_in_cart()
        {
            $post = $this->request->getVar();
            $message = "";
            $date = format_date(15);
            $html = "";
            $post["appointmentDate"] = $date;
            $api_data = array("key" => APP_KEY,"tag" => "check_staff","company_id" => COMPANY_ID,"date" => $date,"service_id" => $post["serviceId"],"serviceNm" => $post["serviceNm"],"appointmentDate" => $post["appointmentDate"],"stime" => $post["stime"],"duration" => $post["duration"]);
            $response = callApi(API_BASE_URL."api/check_staff",$api_data);
            if(isset($response["status"]) && $response["status"] == RESPONSE_FLAG_SUCCESS)
            {
                $data['staffs']         = $response["data"]["staffs"];
                $data['service_name']   = $post['serviceNm'];
                $data['service_id']     = $post['serviceId'];
                $data['service_sub_id'] = $post['serviceSubId'];
                $data['caption']        = $post['caption'];
                $data['price']          = $post['price'];
                $data['stime']          = $post["stime"];
                $data['duration']       = $post["duration"];
                $data['no']             = $post['no'];
                $data['ntime']          = $post['ntime'];
                $data['etime']          = $response['data']['etime'];
                $data['staffId']        = 0;
                $data['flag']           = $post['flag'];
                $data['currency']       = $response['data']['currency'];

                $html = view('add_service_in_cart',$data);

                if((int) $post['no'] == 0)
                    $_times = timepicker("15","19");
                else 
                    $_times = "";

                $status = 1;
            } else {
                $status = 0;
                $message = isset($response["message"]) ? $response["message"] : "Oops! Something went wrong."; 
                $_times = "";
            }
            $available_dates = [];
            $api_data = array("key" => APP_KEY,"tag" => "available_dates","company_id" => COMPANY_ID);
            $response = callApi(API_BASE_URL."api/available_dates",$api_data);
            if(isset($response["status"]) && $response["status"] == RESPONSE_FLAG_SUCCESS)
            {
                if(isset($response["data"]) && count($response["data"]) > 0) {
                    $available_dates = $response["data"];
                }
            }
            echo json_encode(array("status" => $status,"times" => $_times,"content" => $html,"message" => $message,"available_dates" => $available_dates));
            exit;
        }

        public function book_appointment_from_website()
        {
            $post = $this->request->getVar();
            $post["uniq_id"] = strtotime(date("Y-m-d"));
            
            $api_data = array(
                "key" => APP_KEY,
                "tag" => "book_appointment_from_website",
                "company_id" => COMPANY_ID,
                "uniq_id" => $post["uniq_id"],
                "customer_phone" => $post["customer_phone"],
                "customer_email" => $post["customer_email"],
                "customer_name" => $post["customer_name"],
                "customer_note" => $post["customer_note"],
                "available_staffs" => $post["staff_ids"],
                "appointment_date" => $post["appointment_date"],
                "appointment_time" => $post["appointment_time"],
                "service_item" => json_encode($post["service_item"]),
                "service_duration" => json_encode($post["service_duration"]),
                "service_amount" => json_encode($post["service_amount"]),
                "sub_service_name" => json_encode($post["sub_service_name"]),
                "service_name" => json_encode($post["service_name"]),
                "service_sub_item" => json_encode($post["service_sub_item"]),
            );
            $response = callApi(API_BASE_URL."api/book_appointment_from_website",$api_data);
            if(isset($response["status"]) && $response["status"] == RESPONSE_FLAG_SUCCESS)
            {
                $ret_arr["status"] = 1;
                $ret_arr['message'] = "Appointment booked successfully.";
            } else {
                $ret_arr["status"] = 0;
                $ret_arr['message'] = "Something went wrong please try again later.";   
            }
            echo json_encode($ret_arr);
            exit;    
        }
        
        public function fetch_slots()
        {
            $post = $this->request->getVar();
            $post["date"] = date("Y-m-d",strtotime($post['date']));
        
            $busy_slots = [];
            $free_slots = [];
            $available_staff_ids = "";
            $status = 200;
            
            $db = db_connect();
            $query = $db->table("staff_timings st");
            $query = $query->select("st.staffId");
            $query = $query->where("st.date", $post["date"]);
            $result = $query->get()->getResultArray();
            if ($result) {
                $staff_ids = array_column($result, "staffId");
                $available_staff_ids = implode(",", $staff_ids);
                
                // Query to get staff services
                $query = $db->table("staff_services ss");
                $query = $query->where("ss.service_id", $post["service_id"])->where("company_id", 1);
                $query = $query->whereIn("ss.staff_id", $staff_ids);
                $result = $query->get()->getResultArray();
                
                if ($result) {
                    $staff_ids = array_column($result, "staff_id");
                    $staff_ids = array_unique($staff_ids);
                    
                    // Query to get busy slots
                    if ($staff_ids) {
                        $query = $db->table("carts c");
                        $query = $query->select("c.stime, c.etime, c.staffId");
                        $query = $query->whereIn("c.staffId", $staff_ids);
                        $query = $query->where("c.date", $post["date"]);
                        $result = $query->get()->getResultArray();
                        
                        if ($result) {
                            foreach ($result as $row) {
                                $busy_slots[] = array(
                                    "stime" => $row['stime'],
                                    "etime" => $row['etime'],
                                    "staffId" => $row['staffId']
                                );
                            }
                        }
                    }
                }
            } else {
                $status = 201;
            }
            
            // If all checks are successful, find the free slots
            if ($status == 200) {
                $stime = "09:00:00";
                $etime = "20:00:00";
            
                $s_timestamp = strtotime($stime);
                $e_timestamp = strtotime($etime);
                $duration_in_seconds = $post["duration"] * 60;
            
                for ($current_timestamp = $s_timestamp; $current_timestamp < $e_timestamp; $current_timestamp += 300) {
                    $slot_start = date("H:i:s", $current_timestamp);
                    $slot_end = date("H:i:s", $current_timestamp + $duration_in_seconds);
            
                    // Flag to track if this slot is fully occupied by all staff
                    $isSlotOccupied = false;
            
                    foreach ($staff_ids as $staff_id) {
                        // Check if the current staff member is busy during the slot
                        $isOverlapping = false;
                        foreach ($busy_slots as $busy_slot) {
                            // If the current staff member's busy slot overlaps with the current free slot, mark as overlapping
                            if ($busy_slot['staffId'] == $staff_id) {
                                if (($slot_start >= $busy_slot["stime"] && $slot_start < $busy_slot["etime"]) || 
                                    ($slot_end > $busy_slot["stime"] && $slot_end <= $busy_slot["etime"]) || 
                                    ($slot_start <= $busy_slot["stime"] && $slot_end >= $busy_slot["etime"])) 
                                {
                                    $isOverlapping = true;
                                    break;
                                }
                            }
                        }
                        // If any staff member is free, we can mark the slot as available for that staff member
                        if (!$isOverlapping) {
                            $isSlotOccupied = false;  // At least one staff is free during this time
                            break;
                        } else {
                            $isSlotOccupied = true;  // All staff are occupied during this time
                        }
                    }
            
                    // If the slot is not fully occupied by all staff members, add it as a free slot
                    if (!$isSlotOccupied) {
                        $free_slots[] = ["stime" => $slot_start, "etime" => $slot_end];
                    }
                }
            }
            echo json_encode(array("status" => $status,"slots" => $free_slots,"staff_ids" => $available_staff_ids));
            exit;
        }
    }
