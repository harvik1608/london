<?php
    namespace App\Controllers;

    use App\Models\CompanyModel;
    use App\Models\AppointmentModel;
    use App\Models\CartModel;
    use App\Models\EntryModel;
    use App\Models\WebsiteEntry;
    use App\Models\ConfirmationMessage;

    class CronController extends BaseController
    {
        protected $helpers = ["custom"];

        public function send_reminder()
        {
            echo "<pre>";

            // $model = new ConfirmationMessage;
            // $model->where(["DATE(date) <" => date("Y-m-d",strtotime("-7 days"))])->delete();
            
            $logPath = WRITEPATH . 'logs/';
            $deleteBefore = strtotime(date('Y-m-d')); // delete older than this date
            
            $files = glob($logPath . 'log-*.log');
            foreach ($files as $file) {
                if (preg_match('/log-(\d{4}-\d{2}-\d{2})\.log$/', $file, $matches)) {
                    $fileDate = strtotime($matches[1]);
            
                    if ($fileDate < $deleteBefore) {
                        @unlink($file);
                    }
                }
            }
            
            $model = new AppointmentModel;
            $model->where(["subTotal" => 0])->delete();

            $model = new EntryModel;
            $model->where(["appointment_id" => 0,"date <" => date("Y-m-d",strtotime("-7 days"))])->delete();

            $model = new WebsiteEntry;
            $model->where(["DATE(datetime) <" => date("Y-m-d",strtotime("-1 days"))])->delete();

            $today = date("Y-m-d");
            $model = db_connect();

            // Embellish Salon
            $company = $model->table("companies");
            $company_details = $company->select("id,company_name,company_email,company_address,from_email,smtp_password,from_name,currency")->where("id",1)->get()->getRowArray();

            $result = $model->table("appointments a");
            $result = $result->join("customers c","c.id=a.customerId");
            $result = $result->select("a.id as appointment_id,c.name,c.email");
            $result = $result->where(["a.companyId" => $company_details["id"],"a.status" => 1,"DATE(a.bookingDate)" => $today,"c.isConfirmationEmailSend" => 1]);
            $result = $result->whereIn("bookedFrom",[1,3]);
            $data["embellish_appointments"] = $result->get()->getResultArray();
            if($data["embellish_appointments"]) {
                foreach($data["embellish_appointments"] as $appointment) {
                    $items = $model->table("carts c");
                    $items = $items->select("c.id,c.stime,c.etime,c.serviceNm,c.amount,c.duration,c.amount");
                    $items = $items->where(["c.appointmentId" => $appointment["appointment_id"]]);
                    $services = $items->get()->getResultArray();
                    
                    if(trim($appointment["email"]) != "") {
                        $emaildata["company_name"] = $company_details["company_name"];
                        $emaildata["company_address"] = $company_details["company_address"];
                        $emaildata["customer_name"] = ucwords(strtolower($appointment["name"]));
                        $emaildata["start_time"] = isset($services[0]["stime"]) ? date("h:i A",strtotime($services[0]["stime"])) : "";
                        $emaildata["items"] = $services;
                        $emaildata["currency"] = $company_details["currency"];

                        $html = view("template/reminder",$emaildata);
                        send_email($appointment["email"],"Appointment Reminder",$html,$company_details);
                    }
                }
            }

            // Elm Salon
            $company = $model->table("companies");
            $company_details = $company->select("id,company_name,company_email,company_address,from_email,smtp_password,from_name,currency")->where("id",2)->get()->getRowArray();

            $result = $model->table("appointments a");
            $result = $result->join("customers c","c.id=a.customerId");
            $result = $result->select("a.id as appointment_id,c.name,c.email");
            $result = $result->where(["a.companyId" => $company_details["id"],"a.status" => 1,"DATE(a.bookingDate)" => $today,"c.isConfirmationEmailSend" => 1]);
            $result = $result->whereIn("bookedFrom",[1,3]);
            $data["elm_appointments"] = $result->get()->getResultArray();
            if($data["elm_appointments"]) {
                foreach($data["elm_appointments"] as $appointment) {
                    $items = $model->table("carts c");
                    $items = $items->select("c.id,c.stime,c.etime,c.serviceNm,c.amount,c.duration,c.amount");
                    $items = $items->where(["c.appointmentId" => $appointment["appointment_id"]]);
                    $services = $items->get()->getResultArray();
                    
                    if(!empty($services) && trim($appointment["email"]) != "") {
                        $emaildata["company_name"] = "Elms Hair and Beauty";
                        $emaildata["company_address"] = $company_details["company_address"];
                        $emaildata["customer_name"] = ucwords(strtolower($appointment["name"]));
                        $emaildata["start_time"] = isset($services[0]["stime"]) ? date("h:i A",strtotime($services[0]["stime"])) : "";
                        $emaildata["items"] = $services;
                        $emaildata["currency"] = $company_details["currency"];

                        $html = view("template/reminder",$emaildata);
                        send_email($appointment["email"],"Appointment Reminder",$html,$company_details);
                    }
                }
            }

            // Elsa Salon
            $company = $model->table("companies");
            $company_details = $company->select("id,company_name,company_email,company_address,from_email,smtp_password,from_name,currency")->where("id",3)->get()->getRowArray();

            $result = $model->table("appointments a");
            $result = $result->join("customers c","c.id=a.customerId");
            $result = $result->select("a.id as appointment_id,c.name,c.email");
            $result = $result->where(["a.companyId" => $company_details["id"],"a.status" => 1,"DATE(a.bookingDate)" => $today,"c.isConfirmationEmailSend" => 1]);
            $result = $result->whereIn("bookedFrom",[1,3]);
            $data["elsa_appointments"] = $result->get()->getResultArray();
            if($data["elsa_appointments"]) {
                foreach($data["elsa_appointments"] as $appointment) {
                    $items = $model->table("carts c");
                    $items = $items->select("c.id,c.stime,c.etime,c.serviceNm,c.amount,c.duration,c.amount");
                    $items = $items->where(["c.appointmentId" => $appointment["appointment_id"]]);
                    $services = $items->get()->getResultArray();
                    
                    if(!empty($services) && trim($appointment["email"]) != "") {
                        $emaildata["company_name"] = $company_details["company_name"];
                        $emaildata["company_address"] = $company_details["company_address"];
                        $emaildata["customer_name"] = ucwords(strtolower($appointment["name"]));
                        $emaildata["start_time"] = isset($services[0]["stime"]) ? date("h:i A",strtotime($services[0]["stime"])) : "";
                        $emaildata["items"] = $services;
                        $emaildata["currency"] = $company_details["currency"];

                        $html = view("template/reminder",$emaildata);
                        send_email($appointment["email"],"Appointment Reminder",$html,$company_details);
                    }
                }
            }

            // Embrace Salon
            $company = $model->table("companies");
            $company_details = $company->select("id,company_name,company_email,company_address,from_email,smtp_password,from_name,currency")->where("id",4)->get()->getRowArray();

            $result = $model->table("appointments a");
            $result = $result->join("customers c","c.id=a.customerId");
            $result = $result->select("a.id as appointment_id,c.name,c.email");
            $result = $result->where(["a.companyId" => $company_details["id"],"a.status" => 1,"DATE(a.bookingDate)" => $today,"c.isConfirmationEmailSend" => 1]);
            $result = $result->whereIn("bookedFrom",[1,3]);
            $data["embrace_appointments"] = $result->get()->getResultArray();
            if($data["embrace_appointments"]) {
                foreach($data["embrace_appointments"] as $appointment) {
                    $items = $model->table("carts c");
                    $items = $items->select("c.id,c.stime,c.etime,c.serviceNm,c.amount,c.duration,c.amount");
                    $items = $items->where(["c.appointmentId" => $appointment["appointment_id"]]);
                    $services = $items->get()->getResultArray();
                    
                    if(!empty($services) && trim($appointment["email"]) != "") {
                        $emaildata["company_name"] = $company_details["company_name"];
                        $emaildata["company_address"] = $company_details["company_address"];
                        $emaildata["customer_name"] = ucwords(strtolower($appointment["name"]));
                        $emaildata["start_time"] = isset($services[0]["stime"]) ? date("h:i A",strtotime($services[0]["stime"])) : "";
                        $emaildata["items"] = $services;
                        $emaildata["currency"] = $company_details["currency"];

                        $html = view("template/reminder",$emaildata);
                        send_email($appointment["email"],"Appointment Reminder",$html,$company_details);
                    }
                }
            }
        }

        public function send_whatsapp_reminder()
        {
            // Embellish
            $this->salon_whatsapp(1);

            // Elsa
            $this->salon_whatsapp(3);
        }

        public function salon_whatsapp($company_id)
        {
            $model = db_connect();
            $today = date("Y-m-d");

            $result = $model->table("appointments a");
            $result = $result->join("customers c","c.id=a.customerId");
            $result = $result->select("a.id as appointment_id,c.name,c.phone");
            $result = $result->where(["a.companyId" => $company_id,"a.status" => 1,"DATE(a.bookingDate)" => $today]);
            $result = $result->whereIn("bookedFrom",[1,3]);
            $appointments = $result->get()->getResultArray();
            if($appointments) {
                foreach($appointments as $appointment) {
                    $items = $model->table("carts c");
                    $items = $items->select("c.id,c.appointmentId,c.stime,c.etime,c.serviceNm,c.amount,c.duration,c.amount");
                    $items = $items->where(["c.appointmentId" => $appointment["appointment_id"]]);
                    $services = $items->get()->getResultArray();
                    if(!empty($services) && trim($appointment["phone"]) != "") {
                        $times = array_column($services, 'stime');
                        $earliest = min($times);
                        $cart_str = "";
                        foreach($services as $service) {
                            $cart_str .= $service["serviceNm"]." - £".$service["amount"].", ";
                        }
                        $cart_str = substr($cart_str,0,strlen($cart_str)-2);
                        $cart_str = strip_tags($cart_str);
                        
                        $body = array();
                        $body[] = $appointment["name"];
                        $body[] = date('h:i A',strtotime($earliest));
                        $body[] = $cart_str;
                        $this->callReminderWhatsapp($appointment["phone"],$body,$company_id);
                    }
                }
            }
        }

        function callReminderWhatsapp($to, $body, $companyId)
        {
            $model = new CompanyModel();
            $company = $model->select("wa_phone_id,wa_token")->where("id",$companyId)->first();
            if ($company == false) {
                return false;
            }
            $whatsappPhoneId = $company['wa_phone_id'];
            $whatsappToken = $company['wa_token'];
            
            $model = new ConfirmationMessage;
            $model->insert(["sent_to" => $to,"message" => json_encode($body),"type" => 2,"company_id" => $companyId,"isReminderMsg" => 1,"date" => date("Y-m-d H:i:s")]);
            $messageId = $model->getInsertID();
            if ($whatsappPhoneId && $whatsappToken) {
                $data = [
                    "messaging_product" => "whatsapp",
                    "to" => $to,
                    "type" => "template",
                    "template" => [
                        "name" => "todayreminder",
                        "language" => [
                            "code" => "en_GB"
                        ],
                        "components" => [
                            [
                                "type" => "body",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => $body[0]
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => $body[1]
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => $body[2]
                                    ]
                                ]
                            ]
        
                        ]
                    ]
                ];
                return apiCall($whatsappPhoneId, $whatsappToken, $data, $messageId);
            }
            return false;
        }
        
        function delete_logs()
        {
            $logPath = WRITEPATH . 'logs/';
            $deleteBefore = strtotime(date('Y-m-d')); // delete older than this date
            
            $files = glob($logPath . 'log-*.log');
            foreach ($files as $file) {
                if (preg_match('/log-(\d{4}-\d{2}-\d{2})\.log$/', $file, $matches)) {
                    $fileDate = strtotime($matches[1]);
            
                    if ($fileDate < $deleteBefore) {
                        @unlink($file);
                    }
                }
            }
        }
    }