<?php
    namespace App\Controllers;

    use App\Models\CompanyModel;
    use App\Models\AppointmentModel;
    use App\Models\CartModel;
    use App\Models\EntryModel;
    use App\Models\WebsiteEntry;
    use App\Models\ConfirmationMessage;
    use App\Models\CustomerModel;

    class Whatsapp extends BaseController
    {
        protected $helpers = ["custom"];

        public function __construct()
        {
            $session = session();
            $this->userdata = $session->get("userdata");
        }

        public function index()
        {
            if(!in_array(static_company_id(),[1,3])) {
                return redirect("dashboard");
            }
            $model = new ConfirmationMessage;
            $data["customers"] = $model->query("SELECT * FROM confimation_messages cm INNER JOIN (SELECT sent_to, MAX(id) AS max_id FROM confimation_messages WHERE company_id = ? AND type = 2 AND sent_to != '00000000000' GROUP BY sent_to) latest ON cm.id = latest.max_id ORDER BY cm.id DESC", [static_company_id()])->getResultArray();

            $unreadCounts = $model->select('sent_to, SUM(is_read = 0 AND isReply = 1) as total_unread', false)->where('type', 2)->where('sent_to !=', '00000000000')->where('company_id', static_company_id())->groupBy('sent_to')->get()->getResultArray();
            $unreadMap = [];
            foreach($unreadCounts as $row) {
                $unreadMap[$row['sent_to']] = $row['total_unread'];
            }
            foreach($data['customers'] as $key => $customer) {
                $sentTo = $customer['sent_to'];
                $data['customers'][$key]['count'] = $unreadMap[$sentTo] ?? 0;
            }
            usort($data['customers'], function($a, $b) {
                return ($b['count'] ?? 0) <=> ($a['count'] ?? 0);
            });
            return view('admin/whatsapp/whatsapp',$data);
        }

        public function get_whatsapp_history()
        {
            $model = new ConfirmationMessage;
            $data["messages"] = $model->where('sent_to',$this->request->getVar('phone'))->get()->getResultArray();
            $html = view('admin/whatsapp/whatsapp_history',$data);

            $model->set(["is_read" => 1])->where("sent_to",$this->request->getVar('phone'))->update();
            echo json_encode(["html" => $html]);
            exit;
        }

        public function send_whatsapp_message()
        {
            $post = $this->request->getVar();
            $received_from = "";
            $model = new CustomerModel;
            $_cust = $model->select("name")->where("phone",$post["phone"])->first();
            if($_cust) {
                $received_from = $_cust["name"];
            }
            $insert_data = [
                "sent_to" => $post["phone"],
                "received_from" => $received_from,
                "message" => $post["msg"],
                "type" => 2,
                "isManuallyMsg" => 1,
                "company_id" => static_company_id(),
                "date" => date("Y-m-d H:i:s"),
                "is_sent" =>  0
            ];   
            $model = new ConfirmationMessage;
            $model->insert($insert_data);
            $message_id = $model->getInsertID();

            // $data = [
            //     "messaging_product" => "whatsapp",
            //     "to" => $post["phone"],
            //     "type" => "text",
            //     "text" => [
            //         "body"=> $post["msg"]
            //     ]
            // ];
            // $model = new CompanyModel;
            // $company = $model->select("wa_phone_id,wa_token")->where("id",static_company_id())->first();
            // $this->send_whatsapp_msg($company["wa_phone_id"],$company["wa_token"],$data,$message_id);

            echo json_encode(["status" => 1,"id" => $message_id]);
            exit;
        }

        public function send_whatsapp_msg($whatsappPhoneId, $whatsappToken, $data, $isCustomMessage = 0)
        {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://graph.facebook.com/v17.0/'.$whatsappPhoneId.'/messages',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$whatsappToken
                ),
            ));
            $response = curl_exec($curl);
            preview($response);
            curl_close($curl);
            $data = json_decode($response, 1);
            if(isset($data['messages']) && isset($data['messages'][0]) && $data['messages'][0]['id'] != '') {
                if($isCustomMessage != 0) {
                    $model = new ConfirmationMessage;
                    $model->update($isCustomMessage,["is_sent" => 1]);
                }
            }
        }
        
        public function send_whatsapp()
        {
            $emaildata["customer_name"] = "Harshal Kithoriya"; 
            $emaildata["customer_email"] = "vch242516@gmail.com"; 
            $emaildata["customer_phone"] = "9714191947"; 
            $emaildata["customer_note"] = "Hi I booked";
            $emaildata["items"] = array(array("service" => "Hair","duration" => "10 Min.","price" => "20","time" => "10:00"),array("service" => "Waxing","duration" => "20 Min.","price" => "30","time" => "10:00"));
            $emaildata["currency"] = "£";
            $emaildata["total"] = 50;
            $emaildata["is_for_admin"] = 0;
            $emaildata["company_name"] = "Embellish";
            $emaildata["booking_date"] = "2025-09-09";
            $emaildata["company_phone"] = "2025-09-09";
            $emaildata["company_whatsapp"] = "2025-09-09";
            $emaildata["company_email"] = "2025-09-09";
            $emaildata["company_website_url"] = "2025-09-09";
            $emaildata["company_address"] = "2025-09-09";
            $message = view("template/book_appointment",$emaildata);

            $model = new CompanyModel;
            $company = $model->where('id',3)->first();
            $res = send_email("vch242516@gmail.com","Appointment Booking",$message,$company);
            preview($res);
            exit;
            $body = [];
            $body[] = "Kinjal Patel";
            $body[] = "Wednesday, 8th February 2025";
            $body[] = "Eyebrows - Waxing 12£ | Eyebrows Tint - 10£ | Total: 22£";
            $body[] = "01:00 PM";
            $data = [
                "messaging_product" => "whatsapp",
                "to" => "07466166261",
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
            $this->send_whatsapp_msg("243256998873572","EAAZAzi7Di9dUBO6eav5UGX6gFmZCt0oEanVJNdfRuhI18fd4Az9IhMPRQIVRfdM9hh48gor6HkkwqzBbEdcvif2TxpWSTM6NKn5guHjngkPKnktt7b7FZBnoCflovd4wq11wY06gThNfZCEWePlw1iVlMz0Njnjp3ghvTChh3hlO9SxsmRjVpH6DHDLknI3z",$data);
            // $appointment_id = 78219;
            // $model = db_connect();
            // $result = $model->table("appointments a");
            // $result = $result->join("customers c","c.id=a.customerId");
            // $result = $result->select("a.companyId,a.bookingDate,c.name,c.email,c.phone");
            // $result = $result->where(["a.id" => $appointment_id]);
            // $appointment = $result->get()->getRowArray();
            // if($appointment) {
            //     $items = $model->table("carts c");
            //     $items = $items->select("c.id,c.stime,c.etime,c.serviceNm,c.amount,c.duration");
            //     $items = $items->where(["c.appointmentId" => $appointment_id]);
            //     $services = $items->get()->getResultArray();
            //     if($services) {
            //         $times = array_column($services, 'stime');
            //         $earliest = min($times);
            //         $cart_str = "";
            //         foreach($services as $service) {
            //             $cart_str .= $service["serviceNm"]." - £".$service["amount"].", ";
            //         }
            //         $cart_str = substr($cart_str,0,strlen($cart_str)-2);
            //         $cart_str = strip_tags($cart_str);
            //         if($appointment["companyId"] == 1) {
            //             $datetime = $appointment["bookingDate"];
            //             $timestamp = strtotime($datetime);
            //             $formatted = date("l, jS F Y", $timestamp);
                            
            //             $body = [];
            //             $body[] = $appointment["name"];
            //             $body[] = $formatted;
            //             $body[] = $cart_str;
            //             $body[] = date('h:i A',strtotime($earliest));
            //             // callWhatsapp($appointment["phone"],$body,$appointment["companyId"]);
            //         }   
            //     }
            // }
            // preview($body);
            // exit;
            // $body = [];
            // $body[] = "Harshal Kithoriya";
            // $body[] = "Wednesday, 8th February 2025";
            // $body[] = "Eyebrows - Waxing 12£ | Eyebrows Tint - 10£ | Total: 22£";
            // $body[] = "01:00 PM";
            // $res = callWhatsapp("07466166261",$body,3);
            // echo "<pre>";
            // print_r ($res);
            // exit;
            
            // $model = new CompanyModel;
            // $company = $model->select("company_name,company_address,company_email,website_url")->where("id",static_company_id())->first();
            // $company_name = $company['company_name'];
            // $company_address = $company['company_address'];
            // $company_email = $company['company_email'];
            // $website_url = $company['website_url'];
            
            // $main_body = "Hello $body[0]<br><br> Thanks for booking appointment on embellish-beauty.co.uk. This is an details of your appointment.<br><br>Appointment date: .<br>
            // If you have any questions about this invoice, simply reply to this email or reach out to our support team for help $company_email<br><br>
            // Cheers,
            // $company_name Team<br>
            // $company_address<br>
            // $company_email<br>
            // $website_url";
            // echo $main_body;
            // exit;

            // $reportData['user']['first_name'] = "Vasudev Jogani";
            // $reportData['month'] = "August";
            // $reportData['totalHours'] = "20";
            // $reportData['totalWages'] = "30";
            
            // $body = "Hello ". $reportData['user']['first_name']."\n"."\n";
            // $body.= "Here is the details about monthly pay"."\n";
            // $body.="Month: ".$reportData['month']."\n";
            // $body.="Total Hours: ".$reportData['totalHours']."\n";
            // $body.="Wages: £".$reportData['totalWages']."\n";
            // callWhatsapp("+44 7767565845",$body,static_company_id());
            

            
        }

        public function remove_customer_appointments($customer_id)
        {
            $model = new AppointmentModel;
            $appointments = $model->where(["customerId" => $customer_id])->get()->getResultArray();
            if(count($appointments) > 0) {
                foreach($appointments as $appointment) {
                    $model = new CartModel;
                    $model->where(["appointmentId" => $appointment["id"]])->delete();

                    $model = new EntryModel;
                    $model->where(["appointment_id" => $appointment["id"]])->delete();
                }
            }
            $model = new AppointmentModel;
            $model->where(["customerId" => $customer_id])->delete();

            $model = new WebsiteEntry;
            $model->where(["customer_id" => $customer_id])->delete();

            echo "Total ".count($appointments)." appointments deleted.";
        }

        public function receive_whatsapp_messages()
        {
            $request = service('request');
            $verifyToken = "07cc694b9b3fc636710fa08b6922c42b";

            $hubMode = $request->getGet('hub_mode');
            $hubVerifyToken = $request->getGet('hub_verify_token');
            $hubChallenge = $request->getGet('hub_challenge');

            // Verification GET request from Meta
            if ($hubMode === 'subscribe' && $hubVerifyToken === $verifyToken) {
                return $this->response->setStatusCode(200)->setHeader('Content-Type', 'text/plain')->setBody($hubChallenge);
            }

            // Handle POST messages from WhatsApp
            if ($request->getMethod() === 'POST') {
                $data = $request->getJSON(true);
                if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
                    $messageData = $data['entry'][0]['changes'][0]['value']['messages'][0];
                    $contacts = $data['entry'][0]['changes'][0]['value']['contacts'][0] ?? [];

                    $fromNumber = $messageData['from'] ?? '';
                    $text = $messageData['text']['body'] ?? '';
                    $waId = $contacts['wa_id'] ?? '';
                    $timestamp = isset($messageData['timestamp'])
                        ? date('Y-m-d H:i:s', $messageData['timestamp'])
                        : date('Y-m-d H:i:s');
                    $number = preg_replace('/^44/', '0', $fromNumber);

                    $received_from = "";
                    $model = new CustomerModel;
                    $_cust = $model->select("name")->where("phone",$number)->first();
                    if($_cust) {
                        $received_from = $_cust["name"];
                    }
 
                    $model = new ConfirmationMessage;
                    $model->insert([
                        'sent_to' => $number,
                        'received_from' => $received_from,
                        'message' => $text,
                        'type' => 2,
                        'isManuallyMsg' => 1,
                        'isReply' => 1,
                        'company_id' => 3,
                        'date' => date("Y-m-d H:i:s"),
                        'is_sent' => 1,
                    ]);
                }
                return $this->response->setStatusCode(200)->setBody('EVENT_RECEIVED');
            }
            return $this->response->setStatusCode(400)->setBody('Invalid request');
        }

        public function receive_whatsapp_messages_embellish()
        {
            $request = service('request');
            $verifyToken = "dc1a9ec69773ff7be38086f83a6cbe20";

            $hubMode = $request->getGet('hub_mode');
            $hubVerifyToken = $request->getGet('hub_verify_token');
            $hubChallenge = $request->getGet('hub_challenge');

            // Verification GET request from Meta
            if ($hubMode === 'subscribe' && $hubVerifyToken === $verifyToken) {
                return $this->response->setStatusCode(200)->setHeader('Content-Type', 'text/plain')->setBody($hubChallenge);
            }

            // Handle POST messages from WhatsApp
            if ($request->getMethod() === 'POST') {
                $data = $request->getJSON(true);
                if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
                    $messageData = $data['entry'][0]['changes'][0]['value']['messages'][0];
                    $contacts = $data['entry'][0]['changes'][0]['value']['contacts'][0] ?? [];

                    $fromNumber = $messageData['from'] ?? '';
                    $text = $messageData['text']['body'] ?? '';
                    $waId = $contacts['wa_id'] ?? '';
                    $timestamp = isset($messageData['timestamp'])
                        ? date('Y-m-d H:i:s', $messageData['timestamp'])
                        : date('Y-m-d H:i:s');
                    $number = preg_replace('/^44/', '0', $fromNumber);

                    $received_from = "";
                    $model = new CustomerModel;
                    $_cust = $model->select("name")->where("phone",$number)->first();
                    if($_cust) {
                        $received_from = $_cust["name"];
                    }
 
                    $model = new ConfirmationMessage;
                    $model->insert([
                        'sent_to' => $number,
                        'received_from' => $received_from,
                        'message' => $text,
                        'type' => 2,
                        'isManuallyMsg' => 1,
                        'isReply' => 1,
                        'company_id' => 1,
                        'date' => date("Y-m-d H:i:s"),
                        'is_sent' => 1,
                    ]);
                }
                return $this->response->setStatusCode(200)->setBody('EVENT_RECEIVED');
            }
            return $this->response->setStatusCode(400)->setBody('Invalid request');
        }

        public function get_unread_messages_count()
        {
            $model = new ConfirmationMessage;
            $count = $model->where("company_id",static_company_id())->where(["is_read" => 0,"type" => 2,"isReply" => 1])->get()->getNumRows();   
            echo json_encode(["count" => $count]);
            exit;
        }
    }