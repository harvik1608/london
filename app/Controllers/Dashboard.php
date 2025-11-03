<?php
    namespace App\Controllers;

    use App\Models\Staff;
    use App\Models\ServiceModel;
    use App\Models\SubServiceModel;
    use App\Models\CartModel;
    use App\Models\EntryModel;
    use App\Models\EmpModel;
    use App\Models\CustomerModel;
    use App\Models\AppointmentModel;
    use App\Models\PaymentTypeModel;
    use App\Models\DiscountTypeModel;
    use App\Models\OrderPaymentModel;
    use App\Models\CompanyModel;
    use App\Models\StaffServiceModel;
    use App\Models\WeekendDiscount;
    use App\Models\Review;

    class Dashboard extends BaseController
    {
        protected $helpers = ["custom"];

        public function __construct()
        {
            $session = session();
            $this->userdata = $session->get("userdata");
        }

        public function index()
        {
            if(isset($this->userdata["id"])) {
                // if(check_permission("appointments")) {
                    $data["default_date"] = date("Y-m-d");
                    $session = session();
                    if($session->getFlashData("default_date") == true) {
                        $data["default_date"] = $session->getFlashData("default_date");
                    }
                    // $data["default_date"] = "2025-08-28";

                    $data['company'] = company_info(static_company_id());
                    $shortDay = strtolower(date("D"));
                    // if($shortDay == "sun") {
                    //     $data['company']["company_stime"] = $data['company']['company_sunday_stime'];
                    //     $data['company']["company_etime"] = $data['company']['company_sunday_etime'];
                    // }
                    // $model = new PaymentTypeModel;
                    // $data["payment_types"] = $model->select("id,name")->where(["is_active" => "1","company_id" => static_company_id(),"is_deleted" => 0])->orderBy("position","asc")->get()->getResultArray();
                    $data["currency"] = static_company_currency();

                    // $model = new CustomerModel;
                    // $data["customers"] = $model->select("id,name,phone,email")->where("companyId",static_company_id())->get()->getResultArray();

                    if(isset($_GET["is_check"])) {
                        $data["checkout_appointment"] = $_GET["is_check"];
                    } else {
                        $data["checkout_appointment"] = 0;
                    }
                    if(isset($_GET["pending_appointment"])) {
                        $data["pending_appointment"] = $_GET["pending_appointment"];
                    } else {
                        $data["pending_appointment"] = 0;
                    }
                    return view('admin/dashboard',$data);
                // } else {
                //     return redirect("profile");    
                // }
            } else {
                return redirect("admin");
            }
        }

        public function get_customer_info()
        {
            $post = $this->request->getVar();
            
            // $model = new CustomerModel;
            // if($post["column"] == "phone") {
            //     $customers = $model->select("id,name,phone")->where("phone LIKE '".$post['phone']."%'")->where("companyId",static_company_id())->orderBy("name","asc")->get()->getResultArray();
            //     if($customers) {
            //         foreach($customers as $key => $val) {
            //             $customers[$key]["text"] = ucwords(strtolower($val["phone"]));
            //         }
            //     }   
            // } else {
            //     $customers = $model->select("id,name,phone")->where("name LIKE '".$post['name']."%'")->where("companyId",static_company_id())->orderBy("name","asc")->get()->getResultArray();
            //     if($customers) {
            //         foreach($customers as $key => $val) {
            //             $customers[$key]["text"] = ucwords(strtolower($val["name"]));
            //         }
            //     }
            // }
            
            // echo json_encode(array("items" => $customers));
            // exit;
            // =========================================================
            $data['flag'] = $post['flag'];

            $model = new CustomerModel;
            if($post["text"] == "phone") {
                $data["customers"] = $model->select("name,phone,email,note")->where("phone LIKE '".$post['phone']."%'")->where("companyId",static_company_id())->get()->getResultArray();
            } else {
                $data["customers"] = $model->select("name,phone,email,note")->where("name LIKE '%".$post['phone']."%'")->where("companyId",static_company_id())->get()->getResultArray();
            }
            $ret_arr['content']= view('admin/appointment/get_customer_info',$data);
            echo json_encode($ret_arr);
            exit;   
        }
        
        public function get_customer_info_by_id()
        {
            $post = $this->request->getVar();
            $total = 0;
            $total_no_show = 0;
            
            $model = new CustomerModel;
            $customer = $model->select("id,name,email,phone")->where("id",$post["customer_id"])->where("companyId",static_company_id())->first();
            if($customer) {
                $model = new AppointmentModel;
                $total = $model->where("customerId",$customer["id"])->where("status",2)->get()->getNumRows();
                $total_no_show = $model->where("customerId",$customer["id"])->where("status",3)->get()->getNumRows();   
            }
            echo json_encode(array("customer" => $customer,"total" => $total,"total_no_show" => $total_no_show));
            exit;
        }

        public function set_company_info()
        {
            $post = $this->request->getVar();

            $session = session();
            $session->set('companyId',$post['company_id']);
            $company = company_info(static_company_id());
            $session->set('company',$company);
        }

        public function appointments()
        {
            $session = session();
            $post = $this->request->getVar();

            $events = array();
            $db = db_connect();
            // if($this->userdata["user_type"] == 0) {
                $is_gone = 0;
                $appointment = $db->table('carts c');
                $appointment->select("c.id,c.date,c.stime,c.etime,c.message,c.staffId,c.color,cm.name,cm.phone");
                $appointment->join("appointments a","c.appointmentId=a.id");
                $appointment->join("customers cm","a.customerId=cm.id");
                if(isset($_GET["is_check"]) && $_GET["is_check"] == 1 && isset($_GET["pending_appointment"]) && $_GET["pending_appointment"] == 1) {
                    $is_gone = 1;
                    $appointment->where('a.status !=',3);
                } else if(isset($_GET["is_check"]) && $_GET["is_check"] == 0 && isset($_GET["pending_appointment"]) && $_GET["pending_appointment"] == 1) {
                    $is_gone = 1;
                    $appointment->where('a.status',1);
                } else if(isset($_GET["is_check"]) && $_GET["is_check"] == 1 && isset($_GET["pending_appointment"]) && $_GET["pending_appointment"] == 0) {
                    $is_gone = 1;
                    $appointment->where('a.status',2);
                } else if(!isset($_GET["is_check"]) && isset($_GET["pending_appointment"]) && $_GET["pending_appointment"] == 1) {
                    $is_gone = 1;
                    $appointment->where('a.status',1);
                } else if(!isset($_GET["pending_appointment"]) && isset($_GET["is_check"]) && $_GET["is_check"] == 1) {
                    $is_gone = 1;
                    $appointment->where('a.status',2);
                }
                if($is_gone == 0) {
                    $appointment->where('a.status !=',3);
                }
                $appointment->where('a.type !=','N');
                $appointment->where('c.date >=',$post['start']);
                $appointment->where('c.date <=',$post['end']);
                $appointment->where('c.companyId',static_company_id());
                $appointment->orderBy('c.id','ASC');
                $appointments = $appointment->get()->getResultArray();
                if($appointments) {
                    foreach ($appointments as $key => $val) {
                        $title = trim(str_replace($val['phone']." - ".$val['name'], '', $val['message']));
                        
                        $e['id'] = $val['id'];
                        $e['title'] = $title;
                        // $e['title'] = $val['message'];
                        $e['start'] = $val['date']."T".$val['stime'];
                        $e['end']   = $val['date']."T".$val['etime'];
                        // $e['color'] = $val['color'];
                        $e['resourceId'] = $val['staffId'];
                        $e['customer_name'] = $val['name'];
                        $e['customer_phone'] = $val['phone'];
                        array_push($events, $e);
                    }
                }
            // } else {
            //     $appointment = $db->table('carts c');
            //     $appointment->select("c.id,c.date,c.stime,c.etime,c.message,c.staffId,c.color");
            //     $appointment->join("appointments a","c.appointmentId=a.id");
            //     $appointment->where('a.status !=',3);
            //     $appointment->where('a.type !=','N');
            //     $appointment->where('c.staffId',$this->userdata["id"]);
            //     $appointment->where('c.companyId',static_company_id());
            //     $appointment->orderBy('c.id','ASC');
            //     $appointments = $appointment->get()->getResultArray();
            //     if($appointments) {
            //         foreach ($appointments as $key => $val) {
            //             $e['id'] = $val['id'];
            //             $e['title'] = $val['message'];
            //             $e['start'] = $val['date']."T".$val['stime'];
            //             $e['end']   = $val['date']."T".$val['etime'];
            //             // $e['color'] = $val['color'];
            //             $e['resourceId'] = $val['staffId'];
            //             array_push($events, $e);
            //         }
            //     }
            // }
            echo json_encode($events);
            exit;
        }

        public function today_employees()
        {
            $session = session();
            $post = $this->request->getVar();
            $date = format_date(6,$post['date']);

            // if($this->userdata["user_type"] == 0) {
                $db = db_connect();
                $staff = $db->table('staffs s');
                $staff->select("DISTINCT(s.id),CONCAT_WS(' ',s.fname,s.lname) AS title,s.color,st.stime,st.etime");
                $staff->join("staff_timings st","s.id=st.staffId");
                $staff->where('st.date',$date);
                $staff->where(['s.user_type' => 1,"s.is_active" => 1,"s.is_deleted" => 0]);
                $staff->where('st.companyId',static_company_id());
                $staffs = $staff->get()->getResultArray();
                if($staffs){
                    foreach($staffs as $key => $val) {
                        $stime = date("H:i A",strtotime($val['stime']));
                        $etime = date("H:i A",strtotime($val['etime']));
                        $staffs[$key]['title'] = ucwords(strtolower($val['title']))." \n(".$stime." To ".$etime.")";
                        $staffs[$key]['eventColor'] = $val["color"];
                        $staffs[$key]['eventBackgroundColor'] = $val["color"];
                    }
                } else {
                    $staffs = array("id" => "","title" => "","eventColor" => "","eventBackgroundColor" => "");
                }
            // } else {
            //     $db = db_connect();
            //     $staff = $db->table('staffs s');
            //     $staff->select("DISTINCT(s.id),CONCAT_WS(' ',s.fname,s.lname) AS title,s.color");
            //     $staff->join("staff_timings st","s.id=st.staffId");
            //     $staff->where('st.date',$date);
            //     $staff->where('s.id',$this->userdata["id"]);
            //     // $staff->where('s.company_id',static_company_id());
            //     $staffs = $staff->get()->getRowArray();

            //     $staffs = array("id" => $staffs["id"],"title" => $staffs["title"],"eventColor" => $staffs["color"],"eventBackgroundColor" => $staffs["color"]);
            // }
            echo json_encode($staffs);
            exit;
        }

        public function check_past_appointment()
        {
            $post = $this->request->getVar();

            $ret_arr['status'] = 1;
            $ret_arr["uniq_id"] = md5(date('Y-m-d H:i:s'));
            if(format_date(7,$post['adate']) < format_date(8))
                $ret_arr['status'] = 0;

            if(format_date(7,$post['adate']) <= format_date(8))
            {
                if(strtotime($post['atime'].":00") < strtotime(date("H:i:s")))
                    $ret_arr['status'] = 0;
            }
            $model = new CustomerModel;
            $ret_arr["customers"] = $model->select("id,name,phone,email")->where("companyId",static_company_id())->get()->getResultArray();

            echo json_encode($ret_arr);
            exit;   
        }

        public function get_sub_services()
        {
            $session = session();
            $post = $this->request->getVar();
            $ids = $services = array();

            $data["appointment_date"] = isset($post["appointment_date"]) ? $post["appointment_date"] : "";
            $data["service_name"] = $post['serviceNm'];
            $data["flag"] = $post['flag'];

            $model = new CompanyModel;
            $group = $model->select("company_services")->where("id",static_company_id())->first();
            if(!empty($group)) {
                $ids = explode(",",$group["company_services"]);
            }

            $model = new SubServiceModel();
            $model->where("service_group_id",$post['serviceId']);
            $model->where("is_deleted",0);
            if(!empty($ids)) {
                $model->whereIn("id",$ids);
            }
            $model->orderBy('position','asc');
            $services = $model->get()->getResultArray();
            $data["services"] = $services;

            $data["service_group_id"] = $post['serviceId'];
            $data["uniq_id"] = isset($post['uniq_id']) ? $post['uniq_id'] : '';
            $data["appointment_id"] = isset($post['appointment_id']) ? $post['appointment_id'] : '';

            $html = view('admin/appointment/sub_service_list',$data);
            // $html = view('admin/appointment/new_sub_service_list',$data);
            echo json_encode(array("status" => 1,"content" => $html));
            exit;
        }

        public function add_to_cart()
        {
            $session = session();
            $post = $this->request->getVar();

            $model = new EntryModel;
            if($post["appointment_id"] == 0 || $post["appointment_id"] == "") {
                $entries = $model->where("uniq_id",$post["uniq_id"])->where('is_removed_from_cart',0)->orderBy('id','desc')->limit(1)->get()->getResultArray();
            } else {
                $entries = $model->where("appointment_id",$post["appointment_id"])->where("uniq_id",$post["uniq_id"])->where('is_removed_from_cart',0)->orderBy('id','desc')->limit(1)->get()->getResultArray();
            }
            if(empty($entries)) {
                $etime = date("H:i:s",strtotime("+".$post["duration"]." minutes",strtotime($post["stime"])));
                $insert_data = array(
                    "service_group_id" => $post["serviceSubId"],
                    "uniq_id" => $post["uniq_id"],
                    "appointment_id" => $post["appointment_id"],
                    "service_id" => $post["serviceId"],
                    "date" => date("Y-m-d",strtotime($post["appointmentDate"])), 
                    "stime" => $post["stime"], 
                    "duration" => $post["duration"], 
                    "etime" => $etime,
                    "price" => $post["price"],
                    "actual_price" => isset($post["actual_price"]) ? $post["actual_price"] : 0,
                    "showbusystaff" => $post["showbusystaff"],
                    "flag" => $post["flag"],
                    "resource_id" => $post["resourceID"],
                    "company_id" => static_company_id(),
                    "caption" => $post["caption"],
                    "created_at" => date("Y-m-d H:i:s")
                );
                $model->insert($insert_data);
            } else {
                foreach($entries as $entry) {
                    $stime = $entry["etime"];
                    $etime = date("H:i:s",strtotime("+".$post["duration"]." minutes",strtotime($stime)));
                    $insert_data = array(
                        "service_group_id" => $post["serviceSubId"],
                        "uniq_id" => $post["uniq_id"],
                        "appointment_id" => $post["appointment_id"],
                        "service_id" => $post["serviceId"],
                        "date" => date("Y-m-d",strtotime($post["appointmentDate"])), 
                        "stime" => $stime, 
                        "duration" => $post["duration"], 
                        "etime" => $etime,
                        "price" => $post["price"],
                        "actual_price" => isset($post["actual_price"]) ? $post["actual_price"] : 0,
                        "showbusystaff" => $post["showbusystaff"],
                        "flag" => $post["flag"],
                        "resource_id" => $post["resourceID"],
                        "company_id" => static_company_id(),
                        "caption" => $post["caption"],
                        "created_at" => date("Y-m-d H:i:s")
                    );
                    $model->insert($insert_data);
                }
            }
            echo json_encode(array("status" => 1));
            exit;
        }

        public function get_cart_items()
        {
            $session = session();
            $post = $this->request->getVar();
            $cart_data = array();

            $db = db_connect();
            $model = $db->table("entries e");
            $entry = $model->join("services s","e.service_id=s.id","left");
            $entry = $model->select("e.*,s.name AS service_name");
            if($post["appointment_id"] == 0 || $post["appointment_id"] == "") {
                $entry = $model->where("uniq_id",$post["uniq_id"]);
            } else {
                $entry = $model->where("uniq_id",$post["uniq_id"]);
                $entry = $model->where("appointment_id",$post["appointment_id"]);
            }
            $entry = $model->where("is_removed_from_cart",0);
            $entry = $model->orderBy("e.id","asc");
            $entries = $entry->get()->getResultArray();
            foreach($entries as $key => $val) {
                $adate = $post["appointment_date"];
                $stime = $val['stime'];
                $etime = $val['etime'];
                if($val['showbusystaff'] == 1 || $val['flag'] == 1)
                {
                    $staff = $db->table('staffs s');
                    $staff->select("s.id,CONCAT_WS(' ',s.fname,s.lname) AS name,st.stime,st.etime,s.color");
                    $staff->join("staff_timings st","s.id=st.staffId");
                    $staff->where('st.date',$adate);
                    $staff->where('st.companyId',static_company_id());
                    $staff->groupBy("s.id");
                    $staffs = $staff->get()->getResultArray();
                } else {
                    $staff = $db->table('staffs s');
                    $staff->select("s.id,CONCAT_WS(' ',s.fname,s.lname) AS name,st.stime,st.etime,s.color");
                    $staff->join("staff_timings st","s.id=st.staffId");
                    $staff->where('st.date',$adate);
                    $staff->where('st.companyId',static_company_id());
                    $staffs = $staff->get()->getResultArray();
                }
                // 1 = available, 2 = present but time over, 3 = present but not free, 4 = not give service
                $busy_staffs = array();
                $flag = 1;
                if($val["flag"] == 0) {
                    if($val['showbusystaff'] == 0 || $val['flag'] != 1) {
                        if($staffs) {
                            foreach($staffs as $skey => $sval) {
                                if(format_date(7,$adate." ".$stime) >= format_date(7,$adate." ".$sval['stime']) && format_date(7,$adate." ".$etime) <= format_date(7,$adate." ".$sval['etime']))
                                    $staffs[$skey]['status'] = 1;
                                else
                                    $staffs[$skey]['status'] = 2;
                            }
                            // =====================================================================================
                            $model = new CartModel;
                            foreach($staffs as $skey => $sval) {
                                if($sval['status'] == 1) {
                                    $carts = $model->select("staffId,stime,etime,isComplete")->where('is_cancelled',0)->where('isComplete','N')->where("staffId",$sval["id"])->where("date",$adate)->get()->getResultArray();
                                    if(!empty($carts)) {
                                        foreach($carts as $staffKey => $sVal) {
                                            $stime1 = format_date(7,$adate." ".$stime);
                                            $etime1 = format_date(7,$adate." ".$etime);
                                            $bStime = format_date(7,$adate." ".$sVal['stime']);
                                            $bEtime = format_date(7,$adate." ".$sVal['etime']);
                                            if($sVal["isComplete"] == "N" && ($stime1 >= $bStime && $stime1 <= $bEtime || $etime1 >= $bStime && $etime1 <= $bEtime)) {
                                                $staffs[$skey]['status'] = 3;
                                            }
                                            // if($sVal["isComplete"] == "N" && ($stime1 > $bStime && $stime1 < $bEtime || $etime1 > $bStime && $etime1 < $bEtime)) {
                                            //     $staffs[$skey]['status'] = 3;
                                            // }
                                        }
                                    }   
                                }
                            }
                            // =====================================================================================
                        }
                    } else {
                        if(!empty($staffs)) {
                            foreach($staffs as $skey => $sval) {
                                $staffs[$skey]['status'] = 1;
                            }
                        }
                    }
                } else {
                    if(!empty($staffs)) {
                        foreach($staffs as $skey => $sval) {
                            $staffs[$skey]['status'] = 1;
                        }
                    }
                }
                if(!empty($staffs)) {
                    $model = new StaffServiceModel;
                    foreach($staffs as $skey => $sval) {
                        if($sval['status'] == 1) {
                            $count = $model->where("staff_id",$sval['id'])->where("service_id",$val['service_id'])->get()->getNumRows();
                            if($count == 0) {
                                $staffs[$skey]["status"] = 4;
                            }
                        }
                    }
                }
                if($post["appointment_id"] == "") {
                    $resource_id = $val['resource_id'];
                } else {
                    $resource_id = $val['staff_id'];
                }
                $model = new EmpModel;
                $staff = $model->select("CONCAT_WS(' ',fname,lname) AS name,color")->where('id',$resource_id)->first();

                $data["is_busy_staff"]  = "N"; 
                $data["entry_id"]       = $val["id"]; 
                $data['staffs']         = $staffs;
                $data['busy_staffs']    = $busy_staffs;
                $data['service_name']   = $val['service_name'];
                $data['service_id']     = $val['service_group_id'];
                $data['service_sub_id'] = $val['service_id'];
                $data['caption']        = $val['caption'];
                $data['sub_caption']    = isset($val['sub_caption']) ? $val['sub_caption'] : '';
                $data['price']          = $val['price'];
                $data['actual_price']          = $val['actual_price'];
                $data['stime']          = $stime;
                $data['duration']       = $val['duration'];
                $data['no']             = 0;
                $data['ntime']          = "";
                $data['etime']          = $etime;
                $data['staffId']        = $resource_id;
                $data['flag']           = $val['flag'];
                $data['uniq_id']        = $val['uniq_id'];
                if($staff)
                {
                    $data['staff_name']     = $staff["name"];
                    $data['staff_color']    = $staff["color"];
                } else {
                    $data['staff_name']     = "Robot";
                    $data['staff_color']    = "#000";
                }
                $data['is_all_staff_busy'] = 0;
                $cart_data[] = $data;
            } 
            $load_data["cart_data"] = $cart_data;
            $html = view('admin/appointment/add_to_cart',$load_data);
            echo json_encode(array("status" => 1,"content" => $html));
            exit;
        }

        public function remove_from_cart()
        {
            $post = $this->request->getVar();
            
            $model = new EntryModel;
            if(isset($post["action"]) && $post["action"] == "remove_cart") {
                $entry = $model->select("cart_id")->where("id",$post["entry_id"])->first();
                if($entry && $entry["cart_id"] != 0) {
                    $model = new CartModel;
                    $model->where('id',$entry["cart_id"])->set('is_removed_from_cart',1)->update();
                }
                $model = new EntryModel;
                $model->where('id',$post["entry_id"])->set('is_removed_from_cart',1)->update();
            }

            $entries = $model->where("uniq_id",$post["uniq_id"])->where('is_removed_from_cart',0)->orderBy("id","asc")->get()->getResultArray();
            if(!empty($entries)) {
                $no = 0;
                $model = new EntryModel;
                foreach($entries as $key => $val) {
                    $no++;
                    if($no == 1) {
                        $stime = $post["date"]." ".$post["time"];
                        $etime = date("H:i:s",strtotime("+".$val["duration"]." minutes",strtotime($stime)));
                        $update_data = array(
                            'stime' => date("H:i:s",strtotime($stime)),
                            'etime' => $etime,
                            'is_turn' => 1
                        );
                        $model->update($val["id"],$update_data);
                    } else {
                        $row = $model->where("uniq_id",$post["uniq_id"])->where("is_turn",1)->orderBy("id","desc")->first();
                        if(!empty($row)) {
                            $stime = $post["date"]." ".$row["etime"];
                            $etime = date("H:i:s",strtotime("+".$val["duration"]." minutes",strtotime($stime)));
                            $update_data = array(
                                'stime' => date("H:i:s",strtotime($stime)),
                                'etime' => $etime,
                                'is_turn' => 1
                            );
                            $model->update($val["id"],$update_data);
                        }
                    }
                }
                $model->set("is_turn",0)->where("uniq_id",$post["uniq_id"])->update();
            }
        }

        public function add_appointment()
        {
            $session = session();
            $userdata = $session->get('userdata');
            $post = $this->request->getVar();
            $customer_name = $customer_email = $customer_phone = $customer_note = "";

            $appointment_date = format_text(3,$post['appointment_date']);
            $flag = 1;
            
            if($post['appointmentID'] == "" && format_date(7,$appointment_date) < format_date(8))
                $flag = 0;

            if($post['appointmentID'] == "" && format_date(7,$appointment_date) <= format_date(8))
            {
                if(format_date(7,$post['appointment_time']) < format_date(12))
                    $flag = 0;
            }            
            if($flag == 1)
            {
                $calendar_id = "";
                $phone  = format_text(4,$post['customer_phone']);
                $name   = $post['customer_name'];
                if($post['appointmentID'] == "")
                {
                    $isConfirmationEmailSend = 1;
                    $model = new CustomerModel;
                    $customer = $model->select('id,isConfirmationEmailSend')->where('phone',$phone)->where("companyId",static_company_id())->first();
                    if($customer)
                    {
                        $customer_name = $name;
                        $customer_email = $post["customer_email"];
                        $customer_phone = $phone;
                        $customer_note = $post["customer_note"];

                        $customer_id = $customer['id'];
                        $cparam['name']    = format_text(4,$name);
                        $cparam['email']   = format_text(4,$post['customer_email']);
                        // $cparam['note']    = format_text(4,$post['customer_note']);

                        $model = new CustomerModel;
                        $model->update($customer_id,$cparam);
                        
                        $isConfirmationEmailSend = $customer['isConfirmationEmailSend'];
                    } else {
                        $customer_name = $name;
                        $customer_email = $post["customer_email"];
                        $customer_phone = $phone;
                        $customer_note = $post["customer_note"];

                        $cparam['name']    = format_text(4,$name);
                        $cparam['phone']   = format_text(4,$phone);
                        $cparam['email']   = format_text(4,$post['customer_email']);
                        // $cparam['note']    = format_text(4,$post['customer_note']);
                        $cparam['marketing_email'] = "N";
                        $cparam['note']    = "";
                        $cparam['is_sync_with_google']    = 0;
                        $cparam['addedBy'] = $userdata['id'];
                        $cparam['companyId'] = static_company_id();
                        $cparam['updatedBy'] = 0;
                        $cparam['createdAt'] = format_date(5);
                        $cparam['updatedAt'] = "";

                        $model = new CustomerModel;
                        $model->insert($cparam);
                        $customer_id = $model->getInsertID();
                    }
                    $params["uniq_id"] = $post["uniq_id"];
                    $params['customerId']   = $customer_id;
                    $params['subTotal']     = 0;
                    $params['discountAmt']  = 0;
                    $params['finalAmt']     = 0;
                    $params['bookingDate']  = format_date(6,$appointment_date);
                    $params['status']       = 1;
                    $params['bookedFrom']   = $post["bookedFrom"];
                    $params['note']         = format_text(4,$post['customer_note']);
                    $params['type']         = "Y";
                    $params['flag']         = "Y";
                    $params['addedDate']    = format_date(1);
                    $params['addedBy']      = $userdata['id'];
                    $params['companyId']    = static_company_id();
                    $params['updatedBy']    = 0;
                    $params['createdAt']    = format_date(5);
                    $params['updatedAt']    = "";

                    $model = new AppointmentModel;
                    $model->insert($params);
                    $appointment_id = $model->getInsertID();
    
                    $amount = 0;
                    for($i = 0; $i < count($_POST['service_item']); $i ++)
                    {
                        $amount = $amount + $_POST['service_amount'][$i];
                        $email_message = "";
                        if($post['sub_service_name'] != "") {
                            $message = $phone." - ".$name."\n".$_POST['service_name'][$i]."-\n".$_POST['sub_service_name'][$i];
                            $email_message = $_POST['service_name'][$i]."-\n".$_POST['sub_service_name'][$i];
                        } else { 
                            $message = $phone." - ".$name."\n".$post['service_name'];
                            $email_message = $post['service_name'];
                        }
                        $booked_items[] = array("service" => $email_message,"duration" => $_POST['service_duration'][$i],"price" => $_POST['service_amount'][$i],"time" => format_datetime($_POST['service_stime'][$i],2)." To ".format_datetime($_POST['service_etime'][$i],2));
    
                        $message = $message."\n".$_POST['selected_staff_name'][$i];
                        $carts = array(
                            'uniq_id' => $post["uniq_id"],
                            'appointmentId' => $appointment_id,
                            'date' => format_date(6,$appointment_date),
                            'stime' => $_POST['service_stime'][$i],
                            'duration' => $_POST['service_duration'][$i],
                            'etime' => $_POST['service_etime'][$i],
                            'staffId' => $_POST['service_staff'][$i] == "" ? $_POST['service_busy_staff'][$i] : $_POST['service_staff'][$i],
                            'serviceId' => $_POST['service_item'][$i],
                            'serviceNm' => $_POST['service_nm'][$i],
                            'serviceSubId' => $_POST['service_sub_item'][$i],
                            'amount' => $_POST['service_amount'][$i],
                            'actual_amount' => isset($_POST['service_actual_amount'][$i]) ? $_POST['service_actual_amount'][$i] : 0,
                            'message' => $message,
                            'isStaffBusy' => $_POST["is_busy_staff"][$i],
                            'addedBy' => $userdata['id'],
                            'companyId' => static_company_id(),
                            'color' => $_POST['selected_staff_color'][$i],
                            'updatedBy' => 0,
                            'createdAt' => format_date(5),
                            'updatedAt' => "",
                        );
                        $model = new CartModel;
                        $model->insert($carts);
                        $cart_id = $model->getInsertID();

                        $model = new EntryModel;
                        $model->update($_POST['entry_id'][$i],array("appointment_id" => $appointment_id,"cart_id" => $cart_id,"staff_id" => $_POST['service_staff'][$i] == "" ? $_POST['service_busy_staff'][$i] : $_POST['service_staff'][$i]));
                    }
                    $model = new AppointmentModel;
                    $model->update($appointment_id,array("subTotal" => $amount,"finalAmt" => $amount));
                    $calendar_id = $appointment_id;


                    if($post["bookedFrom"] != 2) {
                        // Sending confirmation email
                        $model = new CompanyModel;
                        $company = $model->where('id',static_company_id())->first();
                        $emaildata["customer_name"] = $customer_name; 
                        $emaildata["customer_email"] = $customer_email; 
                        $emaildata["customer_phone"] = $customer_phone; 
                        $emaildata["customer_note"] = $customer_note;
                        $emaildata["items"] = $booked_items;
                        $emaildata["currency"] = "£";
                        $emaildata["total"] = $amount;
                        $emaildata["company_name"] = $company["company_name"];
                        $emaildata["company_phone"] = $company["company_phone"];
                        $emaildata["company_whatsapp"] = $company["company_whatsapp_phone"];
                        $emaildata["company_email"] = $company["company_email"];
                        $emaildata["company_address"] = $company["company_address"];
                        $emaildata["company_website_url"] = $company["website_url"];
                        $emaildata["booking_date"] = format_datetime($appointment_date,1);
                        if($customer_email != "" && $customer_email != "vch242516@gmail.com") {
                            $emaildata["is_for_admin"] = 1;
                            $html = view("template/book_appointment",$emaildata);
                            send_email($company["company_email"],"New Appointment Booked",$html,$company);
                        }
                        if($customer_email != "" && $isConfirmationEmailSend == 1) {
                            $emaildata["is_for_admin"] = 0;
                            $html = view("template/book_appointment",$emaildata);
                            send_email($customer_email,"New Appointment Booked",$html,$company);
                        }
                        send_whatsapp_msg($appointment_id);   
                    }                    
                } else {
                    $model = new CartModel;
                    $model->where(array("uniq_id" => $post["uniq_id"],"appointmentId" => $post["appointmentID"]))->delete();

                    $model = new EntryModel;
                    $model->where(array("uniq_id" => $post["uniq_id"],"appointment_id" => $post["appointmentID"]))->delete();

                    $model = new CustomerModel;
                    $customer = $model->select('id')->where('phone',$phone)->where("companyId",static_company_id())->first();
                    if($customer)
                    {
                        $customer_id = $customer['id'];
                        $cparam['name']    = format_text(4,$name);
                        $cparam['email']   = format_text(4,$post['customer_email']);
                        $cparam['note']    = format_text(4,$post['customer_note']);

                        $model = new CustomerModel;
                        $model->update($customer_id,$cparam);
                    } else {
                        $cparam['name']    = format_text(4,$name);
                        $cparam['phone']   = format_text(4,$phone);
                        $cparam['email']   = format_text(4,$post['customer_email']);
                        $cparam['note']    = format_text(4,$post['customer_note']);
                        $cparam['marketing_email'] = "N";
                        $cparam['note']    = "";
                        $cparam['addedBy'] = $userdata['id'];
                        $cparam['companyId'] = static_company_id();
                        $cparam['updatedBy'] = 0;
                        $cparam['createdAt'] = format_date(5);
                        $cparam['updatedAt'] = "";

                        $model = new CustomerModel;
                        $model->insert($cparam);
                        $customer_id = $model->getInsertID();
                    }
                    $params['customerId']   = $customer_id;
                    $params['bookedFrom']   = $post["bookedFrom"];
                    $params['bookingDate']  = format_date(6,$appointment_date);
                    $params['note']         = format_text(4,$post['customer_note']);
                    $params['companyId']    = static_company_id();
                    $params['updatedBy']    = $userdata['id'];
                    $params['updatedAt']    = format_date(5);

                    $model = new AppointmentModel;
                    $model->update($post["appointmentID"],$params);
                    $appointment_id = $post["appointmentID"];
    
                    $amount = 0;
                    for($i = 0; $i < count($_POST['service_item']); $i ++)
                    {
                        $amount = $amount + $_POST['service_amount'][$i];
                        if($post['sub_service_name'] != "")
                            $message = $phone." - ".$name."\n".$_POST['service_name'][$i]."-\n".$_POST['sub_service_name'][$i];
                        else 
                            $message = $phone." - ".$name."\n".$post['service_name'];
    
                        $message = $message."\n".$_POST['selected_staff_name'][$i];
                        $carts = array(
                            'uniq_id' => $post["uniq_id"],
                            'appointmentId' => $appointment_id,
                            'date' => format_date(6,$appointment_date),
                            'stime' => $_POST['service_stime'][$i],
                            'duration' => $_POST['service_duration'][$i],
                            'etime' => $_POST['service_etime'][$i],
                            'staffId' => $_POST['service_staff'][$i] == "" ? $_POST['service_busy_staff'][$i] : $_POST['service_staff'][$i],
                            'serviceId' => $_POST['service_item'][$i],
                            'serviceNm' => $_POST['service_nm'][$i],
                            'serviceSubId' => $_POST['service_sub_item'][$i],
                            'amount' => $_POST['service_amount'][$i],
                            'actual_amount' => isset($_POST['service_actual_amount'][$i]) ? $_POST['service_actual_amount'][$i] : 0,
                            'message' => $message,
                            'isStaffBusy' => $_POST["is_busy_staff"][$i],
                            'addedBy' => $userdata['id'],
                            'companyId' => static_company_id(),
                            'color' => $_POST['selected_staff_color'][$i],
                            'updatedBy' => 0,
                            'createdAt' => format_date(5),
                            'updatedAt' => "",
                            'caption' => isset($_POST['service_caption'][$i]) ? $_POST['service_caption'][$i] : ""
                        );
                        $model = new CartModel;
                        $model->insert($carts);
                        $cart_id = $model->getInsertID();

                        $entries = array(
                            'uniq_id' => $post["uniq_id"],
                            'appointment_id' => $appointment_id,
                            'date' => format_date(6,$appointment_date),
                            'stime' => $_POST['service_stime'][$i],
                            'duration' => $_POST['service_duration'][$i],
                            'etime' => $_POST['service_etime'][$i],
                            'staff_id' => $_POST['service_staff'][$i] == "" ? $_POST['service_busy_staff'][$i] : $_POST['service_staff'][$i],
                            'service_id' => $_POST['service_sub_item'][$i],
                            'service_group_id' => $_POST['service_item'][$i],
                            'price' => $_POST['service_amount'][$i],
                            'actual_price' => isset($_POST['service_actual_amount'][$i]) ? $_POST['service_actual_amount'][$i] : 0,
                            'company_id' => static_company_id(),
                            'created_at' => format_date(5),
                            "cart_id" => $cart_id,
                            "caption" => $_POST["sub_service_name"][$i],
                            'sub_caption' => isset($_POST['service_caption'][$i]) ? $_POST['service_caption'][$i] : ""
                        );
                        $model = new EntryModel;
                        $model->insert($entries);
                    }
                    $model = new AppointmentModel;
                    $model->update($appointment_id,array("subTotal" => $amount,"finalAmt" => $amount));
                    $calendar_id = $appointment_id;
                }
                $session->setFlashData("default_date",$appointment_date);
                $status = 1;
                $msg = "";      
            } else {
                $status = 0;
                $msg = "You can not do appointment in past time";    
            }
            echo json_encode(array("status" => $status,"message" => $msg));
            exit;
        }

        public function view_appointment()
        {
            $session = session();
            $post = $this->request->getVar();

            $model = new CartModel;
            $appointment = $model->select("appointmentId")->where("id",$post['appointmentId'])->first();
            // $appointment = $model->select("appointmentId")->where("id",$post['appointmentId'])->first();   
            if($appointment)
            {
                $db = db_connect();
                $master = $db->table('appointments a');
                $master->select("c.name AS customer_name,c.phone AS customer_phone,c.email AS customer_email,a.id,a.bookingDate,a.status,a.bookedFrom,a.addedDate,a.subTotal,a.discountAmt,a.finalAmt,a.note,a.type,CONCAT_WS(' ',s.fname,s.lname) AS staff_name,a.salon_note,a.extra_discount");
                $master->join("customers c","a.customerId=c.id","left");
                $master->join("staffs s","a.addedBy=s.id","left");
                $master->where('a.id',$appointment['appointmentId']);
                $data["moredatainfo"] = $master->get()->getRowArray();
                
                $master = $db->table('carts cr');
                $master->select("cr.stime,cr.duration,cr.amount,cr.serviceId,cr.serviceSubId,cr.staffId,cr.isStaffBusy,ss.name AS service_name,ss.json,CONCAT_WS(' ',s.fname,s.lname) AS staff_name,cr.color,cr.serviceNm,cr.caption,cr.actual_amount");
                $master->join("services ss","cr.serviceSubId=ss.id","left");
                $master->join("staffs s","cr.staffId=s.id","left");
                $master->where('cr.appointmentId',$appointment['appointmentId']);
                $master->orderBy("cr.id","asc");
                $data["appointments"] = $master->get()->getResultArray();

                $ret_arr['status'] = 1;
                $ret_arr['message'] = "";
                $ret_arr['appointment_status'] = $data["moredatainfo"]["status"];
                $ret_arr['isWalkin'] = $data["moredatainfo"]["type"];
                $ret_arr['salon_note'] = $data["moredatainfo"]["salon_note"];
                $ret_arr['appointmentId'] = $appointment['appointmentId'];
                $data['currency'] = static_company_currency();
                $data['timezone'] = static_company_timezone();
                $ret_arr['html'] = view('admin/appointment/view_appointment',$data);
            } else {
                $ret_arr['status']  = 0;
                $ret_arr['message'] = "Appointment info not found.";
            }
            echo json_encode($ret_arr);
            exit;
        }

        public function edit_appointment()
        {
            $session = session();
            $userdata = $session->get('userdata');
            $post = $this->request->getVar();

            $appointment = $post['appointmentId'];
            $data['status'] = 1;
            $db = db_connect();
            $master = $db->table('appointments a');
            $master->select("c.name AS customer_name,c.phone AS customer_phone,c.email AS customer_email,a.id,a.bookingDate,a.status,a.bookedFrom,a.addedDate,a.subTotal,a.discountAmt,a.finalAmt,a.note,a.type,CONCAT_WS(' ',s.fname,s.lname) AS staff_name,a.uniq_id,a.customerId");
            $master->join("customers c","a.customerId=c.id","left");
            $master->join("staffs s","a.addedBy=s.id","left");
            $master->where('a.id',$appointment);
            $data["moredatainfo"] = $master->get()->getRowArray();
            $data["moredatainfo"]["bookingDate"] = date("Y-m-d",strtotime($data["moredatainfo"]["bookingDate"]));
            
            $master = $db->table('carts cr');
            $master->select("cr.stime,cr.duration,cr.amount,cr.serviceId,cr.serviceSubId,cr.staffId,cr.isStaffBusy,ss.name AS service_name,ss.json,CONCAT_WS(' ',s.fname,s.lname) AS staff_name,cr.color,cr.actual_amount");
            $master->join("services ss","cr.serviceId=ss.id","left");
            $master->join("staffs s","cr.staffId=s.id","left");
            $master->where('cr.appointmentId',$appointment);
            $master->orderBy("cr.id","asc");
            $data["appointments"] = $master->get()->getResultArray();            
            $flag = 1;
            $html = "";
            $load_data = $cart_data = [];
            if($data['appointments'])
            {
                $model = new EntryModel;
                $entries = $model->select("id,date,stime,duration,etime")->where(["uniq_id" => $data["moredatainfo"]["uniq_id"],"appointment_id >" => 0])->get()->getResultArray();
                // $entries = $model->select("id,date,stime,duration,etime")->where(["appointment_id" => $appointment])->get()->getResultArray();
                if($entries) {
                    for($i = 0; $i < count($entries); $i ++) {
                        if($i == 0) {
                            $stime = $entries[$i]["stime"];
                            $etime = date("H:i:s",strtotime("+".$entries[$i]["duration"]." minutes",strtotime($stime)));
                        } else {
                            $stime = $etime;
                            $etime = date("H:i:s",strtotime("+".$entries[$i]["duration"]." minutes",strtotime($stime)));
                        }
                        $model->update($entries[$i]["id"],["stime" => $stime,"etime" => $etime,"is_removed_from_cart" => 0]);
                    }
                }
                $db = db_connect();
                $model = $db->table("entries e");
                $entry = $model->join("services s","e.service_id=s.id","left");
                $entry = $model->select("e.*,s.name AS service_name");
                $entry = $model->where("uniq_id",$data["moredatainfo"]["uniq_id"]);
                $entry = $model->where("appointment_id",$data["moredatainfo"]["id"]);
                $entry = $model->where("is_removed_from_cart",0);
                $entry = $model->orderBy("e.id","asc");
                $entries = $entry->get()->getResultArray();
                foreach($entries as $key => $val) {
                    $adate = $val["date"];
                    $stime = $val['stime'];
                    $etime = $val['etime'];
                    if($val['showbusystaff'] == 1 || $val['flag'] == 1)
                    {
                        $staff = $db->table('staffs s');
                        $staff->select("s.id,CONCAT_WS(' ',s.fname,s.lname) AS name,st.stime,st.etime,s.color");
                        $staff->join("staff_timings st","s.id=st.staffId");
                        $staff->where('st.date',$adate);
                        $staff->where('st.companyId',static_company_id());
                        $staff->groupBy("s.id");
                        $staffs = $staff->get()->getResultArray();
                    } else {
                        $staff = $db->table('staffs s');
                        $staff->select("s.id,CONCAT_WS(' ',s.fname,s.lname) AS name,st.stime,st.etime,s.color");
                        // $staff->join("staff_services ss","s.id=ss.staff_id");
                        $staff->join("staff_timings st","s.id=st.staffId");
                        // $staff->where('ss.service_id',$val['service_id']);
                        $staff->where('st.companyId',$session->get('companyId'));
                        $staff->where('st.date',$adate);
                        $staff->groupBy("s.id");
                        $staffs = $staff->get()->getResultArray();
                    }
                    $busy_staffs = array();
                    $flag = 1;
                    if($val['showbusystaff'] == 0 || $val['flag'] != 1) 
                    {
                        if($staffs)
                        {                            
                            foreach($staffs as $skey => $sval)
                            {
                                if(format_date(7,$stime) >= format_date(7,$sval['stime']) && format_date(7,$etime) <= format_date(7,$sval['etime']))
                                    $staffs[$skey]['status'] = 1;
                                else
                                    $staffs[$skey]['status'] = 0;
                            }
                            $model = new CartModel;
                            foreach($staffs as $skey => $sval)
                            {
                                if($sval['status'] == 1)
                                {
                                    $carts = $model->select("staffId,stime,etime,isComplete")->where("staffId",$sval["id"])->where("date",$adate)->get()->getResultArray();
                                    if(!empty($carts))
                                    {
                                        foreach($carts as $staffKey => $sVal)
                                        {
                                            $stime1 = format_date(7,$adate." ".$stime);
                                            $etime1 = format_date(7,$adate." ".$etime);
                                            $bStime = format_date(7,$adate." ".$sVal['stime']);
                                            $bEtime = format_date(7,$adate." ".$sVal['etime']);
                                            // echo $stime1." | ".$etime1." | ".$bStime." | ".$bEtime."<br>";
                                            if($sVal["isComplete"] == "N" && ($stime1 > $bStime && $stime1 < $bEtime || $etime1 > $bStime && $etime1 < $bEtime))
                                            {
                                                $staffs[$skey]['status'] = 0;
                                            }
                                        }
                                    }   
                                }
                            }
                        }
                    } else {
                        if(!empty($staffs))
                        {
                            foreach($staffs as $skey => $sval)
                            {
                                $staffs[$skey]['status'] = 1;
                            }
                        }
                    }
                    $model = new EmpModel;
                    $staff = $model->select("CONCAT_WS(' ',fname,lname) AS name,color")->where('id',$val['staff_id'])->first();

                    $data["is_busy_staff"]  = "N"; 
                    $data["entry_id"]       = $val["id"]; 
                    $data['staffs']         = $staffs;
                    $data['busy_staffs']    = $busy_staffs;
                    $data['service_name']   = $val['service_name'];
                    $data['service_id']     = $val['service_group_id'];
                    $data['service_sub_id'] = $val['service_id'];
                    $data['caption']        = $val['caption'];
                    $data['sub_caption']    = isset($val['sub_caption']) ? $val['sub_caption'] : '';
                    $data['price']          = $val['price'];
                    $data['actual_price']   = $val['actual_price'];
                    $data['stime']          = $stime;
                    $data['duration']       = $val['duration'];
                    $data['no']             = 0;
                    $data['ntime']          = "";
                    $data['etime']          = $etime;
                    $data['staffId']        = $val['staff_id'];
                    $data['flag']           = $val['flag'];
                    $data['uniq_id']        = $val['uniq_id'];
                    if($staff)
                    {
                        $data['staff_name']     = $staff["name"];
                        $data['staff_color']    = $staff["color"];
                    } else {
                        $data['staff_name']     = "Robot";
                        $data['staff_color']    = "#000";
                    }
                    $data['is_all_staff_busy'] = 0;
                    $cart_data[] = $data;
                } 
                $load_data["cart_data"] = $cart_data;
            }
            $html = view('admin/appointment/add_to_cart',$load_data);
            $data['html'] = $html;
            
            $total = 0;
            $total_no_show = 0;
            $model = new CustomerModel;
            $customer = $model->select("id")->where("phone",$data["moredatainfo"]["customer_phone"])->where("companyId",static_company_id())->first();
            if(!empty($customer)) {
                $model = new AppointmentModel;
                $total = $model->where("customerId",$data["moredatainfo"]["customerId"])->where("status",2)->get()->getNumRows();
                $total_no_show = $model->where("customerId",$data["moredatainfo"]["customerId"])->where("status",3)->get()->getNumRows();
            }
            $data["total_appointments"] = $total;
            $data["total_no_show"] = $total_no_show;
            echo json_encode($data);
            exit;
        }
        
        public function clear_carts()
        {
            $post = $this->request->getVar();

            if(isset($post["appointment_id"]) && $post["appointment_id"] != "") {
                $model = new EntryModel;
                $model->where(array("cart_id" => 0,"appointment_id" => $post["appointment_id"]))->delete();
            } else {
                $model = new EntryModel;
                $model->where(array("uniq_id" => $post["uniq_id"],"appointment_id" => 0))->delete();
            }
            echo json_encode(array("status" => 200));
            exit;
        }

        public function remove_appointment()
        {
            $post = $this->request->getVar();

            $model = new AppointmentModel;
            if($model->update($post['id'],["status" => 3]))
            {
                $model = new CartModel;
                $model->where("appointmentId",$post["id"])->set(["isComplete" => "Y","is_cancelled" => 1])->update();

                // $model = new EntryModel;
                // $model->where("appointment_id",$post["id"])->delete();
                
                $ret_arr['status']  = 1;
                $ret_arr['message'] = "";
            } else {
                $ret_arr['status']  = 0;
                $ret_arr['message'] = "Oops something went wrong please try again later.";
            }
            echo json_encode($ret_arr);
            exit;
        }

        public function checkout_appointment()
        {
            $session = session();
            $post = $this->request->getVar();

            $db = db_connect();
            $master = $db->table('appointments a');
            $master->select("c.name AS customer_name,c.phone AS customer_phone,c.email AS customer_email,a.id,a.bookingDate,a.status,a.bookedFrom,a.addedDate,a.subTotal,a.discountAmt,a.finalAmt,a.note,a.type,CONCAT_WS(' ',s.fname,s.lname) AS staff_name");
            $master->join("customers c","a.customerId=c.id","left");
            $master->join("staffs s","a.addedBy=s.id","left");
            $master->where('a.id',$post["appointmentId"]);
            $data["moredatainfo"] = $master->get()->getRowArray();
            
            $master = $db->table('carts cr');
            $master->select("cr.id,cr.stime,cr.duration,cr.amount,cr.serviceId,cr.serviceSubId,cr.staffId,cr.isStaffBusy,ss.name AS service_name,ss.json,CONCAT_WS(' ',s.fname,s.lname) AS staff_name,cr.color,cr.serviceNm,cr.caption,cr.actual_amount");
            $master->join("services ss","cr.serviceSubId=ss.id","left");
            $master->join("staffs s","cr.staffId=s.id","left");
            $master->where('cr.appointmentId',$post["appointmentId"]);
            $master->orderBy("cr.id","asc");
            $data["appointments"] = $master->get()->getResultArray();

            $model = new PaymentTypeModel();
            $data["payment_types"] = $model->select("id,name")->where("is_active",'1')->where('is_deleted',0)->where("company_id",static_company_id())->get()->getResultArray();

            $model = new DiscountTypeModel();
            $data["discount_types"] = $model->select("id,name,discount_type,discount_value")->where("is_active",'1')->where("company_id",static_company_id())->get()->getResultArray();

            $ret_arr['status'] = 1;
            $ret_arr['message'] = "";
            $ret_arr['appointmentId'] = $post["appointmentId"];
            $data['currency'] = static_company_currency();
            $data['timezone'] = static_company_timezone();
            $ret_arr['html'] = view('admin/appointment/checkout_appointment',$data);
            echo json_encode($ret_arr);
            exit;
        }

        public function hide_appointment()
        {
            $post = $this->request->getVar();

            $model = new AppointmentModel;
            $data  = $model->update($post['appointmentId'],array("status" => 3));
            if($data)
            {
                $db = db_connect();
                $cart = $db->table('carts');
                $cart->where("appointmentId",$post["appointmentId"])->update(array("isComplete" => "Y","is_cancelled" => 1));
                $ret_arr['status']  = 1;
                $ret_arr['message'] = "";
            } else {
                $ret_arr['status'] = 0;
                $ret_arr['message'] = "Oops something went wrong";
            }
            echo json_encode($ret_arr);
            exit;
        }

        public function complete_appointment()
        {
            $session = session();
            $post = $this->request->getVar();

            $param['status'] = 2;
            $param['discountAmt'] = $post['discountAmt'];
            $param['discountId'] = $post['discount_id'];
            $param['salon_note'] = $post['salon_note'];
            $param['updatedBy'] = $session->get('id');
            $param['updatedAt'] = format_date(5);
            $param["extra_discount"] = $post["extra_discount"];
            $model = new AppointmentModel;
            $data  = $model->update($post["appointmentId"],$param);  
            if($data)
            {
                $db = db_connect();
                $cart = $db->table('carts');
                $cart->where("appointmentId",$post["appointmentId"])->update(array("isComplete" => "Y","is_cancelled" => 2));

                if(count($_POST['payments']) > 0)
                {
                    for($i = 0; $i < count($_POST['payments']); $i ++)
                    {
                        $payment_arr[] = array(
                            'appointmentId' => $post["appointmentId"],
                            'paymentMethod' => $_POST['payments'][$i]['payment_id'],
                            'paymentAmount' => $_POST['payments'][$i]['amount'],
                            'companyId' => static_company_id(),
                            'addedBy' => $session->get('userdata')['id'],
                            'updatedBy' => 0,
                            'createdAt' => format_date(5),
                            'updatedAt' => "",       
                        );
                    }
                    $model = new OrderPaymentModel;
                    $model->insertBatch($payment_arr);

                    $ret_arr['status']  = 1;
                    $ret_arr['message'] = "";
                } else {
                    $ret_arr['status'] = 0;
                    $ret_arr['message'] = "Oops something went wrong.";      
                }
            } else {
                $ret_arr['status'] = 0;
                $ret_arr['message'] = "Oops something went wrong.";  
            }
            echo json_encode($ret_arr);
            exit;
        }

        public function close_appointment()
        {
            $session = session();
            $post = $this->request->getVar();

            if(isset($post["appointmentId"]) && $post["appointmentId"] != "") {
                $param['salon_note'] = $post['salon_note'];
                $param['updatedBy'] = $session->get('id');
                $param['updatedAt'] = format_date(5);

                $model = new AppointmentModel;
                $data  = $model->update($post["appointmentId"],$param);
            }
            echo json_encode(array("status" => 200));
            exit;
        }

        public function add_walkin()
        {
            $session = session();
            $post = $this->request->getVar();
            // preview($post);

            $phone = $post['walkin_phone'];
            $name  = format_text(1,$post['walkin_name']);
            $appointment_date = str_replace("/", "-", $post['walkin_date']);

            $model = new CustomerModel;
            $customer = $model->select('id')->where('phone',$phone)->where("companyId",static_company_id())->first();
            if($customer)
            {
                $customer_id = $customer['id'];
                $customer_params['name'] = $name;
                $customer_params['email']= $post['walkin_email'];
                $model = new CustomerModel;
                $model->update($customer_id,$customer_params);
            } else {
                $customer_params['name'] = $name;
                $customer_params['phone'] = $phone;
                $customer_params['email'] = $post['walkin_email'];
                $customer_params['marketing_email'] = "N";
                $customer_params['note'] = "";
                $customer_params['is_sync_with_google'] = 0;
                $customer_params['companyId'] = static_company_id();
                $customer_params['addedBy'] = $session->get('userdata')['id'];
                $customer_params['updatedBy'] = 0;
                $customer_params['createdAt'] = format_date(5);
                $customer_params['updatedAt'] = "";
                $model = new CustomerModel;
                $model->insert($customer_params);
                $customer_id = $model->getInsertID();   
            }
            $params['uniq_id']      = $post["walkin_uniq_id"];
            $params['customerId']   = $customer_id;
            $params['subTotal']     = 0;
            $params['extra_discount'] = $post["walkin_extra_discount"];
            $params['discountAmt']  = 0;
            $params['finalAmt']     = 0;
            $params['bookingDate']  = format_date(6,$appointment_date);
            $params['status']       = 2;
            $params['bookedFrom']   = 3;
            $params['note']         = $post['walkin_note'];
            $params['type']         = "N";
            $params['flag']         = "Y";
            $params['addedDate']    = format_date(1);
            $params['companyId']    = static_company_id();
            $params['addedBy']      = $session->get('userdata')['id'];
            $params['updatedBy']    = 0;
            $params['createdAt']    = format_date(5);
            $params['updatedAt']    = "";
            $model = new AppointmentModel;
            $model->insert($params);
            $appointment_id = $model->getInsertID();
            if($appointment_id > 0)
            {
                $amount = 0;
                for($i = 0; $i < count($_POST['service_item']); $i ++)
                {
                    $amount = $amount + $_POST['service_amount'][$i];
                    if($post['sub_service_name'] != "")
                        $message = "WALKIN \n-".$phone." - ".$name."\n".$_POST['service_name'][$i]."-\n".$_POST['sub_service_name'][$i];
                    else 
                        $message = "WALKIN \n-".$phone." - ".$name."\n".$post['service_name'];

                    $message = $message.$_POST['selected_staff_name'][$i];
                    $carts[] = array(
                        'uniq_id' => $post["walkin_uniq_id"],
                        'appointmentId' => $appointment_id,
                        'date' => format_date(6,$appointment_date),
                        'stime' => $_POST['service_stime'][$i],
                        'duration' => $_POST['service_duration'][$i],
                        'etime' => $_POST['service_etime'][$i],
                        'staffId' => $_POST['service_staff'][$i],
                        'serviceId' => $_POST['service_item'][$i],
                        'serviceSubId' => $_POST['service_sub_item'][$i],
                        'serviceNm' => $_POST['service_name'][$i],
                        'amount' => $_POST['service_amount'][$i],
                        'message' => $message,
                        'companyId' => static_company_id(),
                        'isStaffBusy' => $_POST["is_busy_staff"][$i],
                        'color' => $_POST['selected_staff_color'][$i],
                        'addedBy' => $session->get('userdata')['id'],
                        "isComplete" => "Y",
                        'updatedBy' => 0,
                        'createdAt' => format_date(5),
                        'updatedAt' => "",
                    );

                    $model = new EntryModel;
                    $model->update($_POST['entry_id'][$i],array("appointment_id" => $appointment_id,"staff_id" => $_POST['service_staff'][$i] == "" ? $_POST['service_busy_staff'][$i] : $_POST['service_staff'][$i]));
                }
                $model = new CartModel;
                $model->insertBatch($carts);

                // $discount_amt = (double) $amount - (double) $post['walkin_discounted_amt'];
                $discount_amt = (double) $post['walkin_discounted_amt'];
                $model = new AppointmentModel;
                $model->update($appointment_id,array("subTotal" => $amount,"discountAmt" => $discount_amt,"finalAmt" => $amount - $discount_amt));
                if(count($_POST['walkin_payment_type_ids']) > 0)
                {
                    for($i = 0; $i < count($_POST['walkin_payment_type_ids']); $i ++)
                    {
                        if((double) $_POST['walkin_payment_type_amt'][$i] > 0)
                        {
                            $payment_arr[] = array(
                                'appointmentId' => $appointment_id,
                                'paymentMethod' => $_POST['walkin_payment_type_ids'][$i],
                                'paymentAmount' => $_POST['walkin_payment_type_amt'][$i],
                                'addedBy' => $session->get('userdata')['id'],
                                'companyId' => static_company_id(),
                                'updatedBy' => 0,
                                'createdAt' => format_date(5),
                                'updatedAt' => "",       
                            );
                        }
                    }
                    $model = new OrderPaymentModel;
                    $model->insertBatch($payment_arr);
                }
                $success = 1;
            } else {
                $success = 0;
            }
            echo json_encode(array("status" => $success));
            exit;          
        }

        public function get_customer_history()
        {
            $session = session();
            $post = $this->request->getVar();

            $cust_id = 0;
            $model = new CustomerModel;
            $customer = $model->select("id")->where("phone",$post["phone"])->where('companyId',static_company_id())->first();
            if(!empty($customer)) {
                $cust_id = $customer["id"];
            }

            $model = new AppointmentModel;
            $data["appointments"] = $model->select("id,bookingDate,note,finalAmt,status,bookedFrom,note,is_old_data")->where("customerId",$cust_id)->where("finalAmt >",0)->orderBy("id","desc")->get()->getResultArray();
            if(!empty($data["appointments"])) {
                $db = db_connect();
                foreach($data["appointments"] as $key => $val) {
                    $master = $db->table('carts cr');
                    $master->select("cr.id,cr.stime,cr.duration,CONCAT_WS(' ',s.fname,s.lname) AS staff_name,ss.name AS service_name,cr.serviceNm");
                    $master->join("services ss","cr.serviceSubId=ss.id","left");
                    $master->join("staffs s","cr.staffId=s.id","left");
                    $master->where('cr.appointmentId',$val["id"]);
                    $master->orderBy("cr.id","asc");
                    $carts = $master->get()->getResultArray();
                    if(!empty($carts)) {
                        $data["appointments"][$key]["items"] = $carts;
                    } else {
                        $data["appointments"][$key]["items"] = [];
                    }
                }
            }
            $html = view('admin/appointment/custmer_history',$data);
            echo json_encode(array("status" => 1,"html" => $html));
            exit;
        }

        public function open_walkin()
        {
            $post = $this->request->getVar();
            $company_id = static_company_id();

            $model = new CompanyModel;
            $timezone = $model->select("timezone")->where("id",$company_id)->first();
            if(!empty($timezone)) {
                date_default_timezone_set($timezone['timezone']);
            } else {
                date_default_timezone_set("Asia/Kolkata");
            }
            $ret_arr['date'] = date("Y-m-d");
            $ret_arr['time'] = date("h:i A");
            $ret_arr['format_time'] = date("H:i").":00";
            $ret_arr["uniq_id"] = md5(date('Y-m-d H:i:s'));
            echo json_encode($ret_arr);
            exit;
        }

        public function daily_reports()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("daily_reports")) {
                    return view('admin/daily_reports');
                } else {
                    return redirect("profile");
                }
            } else {
                return redirect("admin");
            }
        }

        public function fetch_daily_report()
        {
            $post = $this->request->getVar();

            if(strtotime($post["edate"]) < strtotime($post["sdate"])) {
                $html = "";
                echo json_encode(array("status" => 0,"html" => $html,"message" => "End Date must be greater than Start Date or equal to Start Date."));
                exit;
            } else {
                $where = 'DATE(a.bookingDate) >= "'.$post['sdate'].'" AND DATE(a.bookingDate) <= "'.$post['edate'].'" AND status = 2';

                $db = db_connect();
                $appointment = $db->table('appointments a');
                $appointment->select("a.id,c.name AS customer_name,c.phone AS customer_phone,a.type,a.bookingDate,a.subTotal,a.discountAmt,a.finalAmt,a.extra_discount");
                $appointment->join("customers c","c.id=a.customerId","left");
                $appointment->where("a.companyId",static_company_id());
                $appointment->where($where);
                // $appointment->where($where1);
                $appointment->orderBy("a.id","desc");
                $data["appointments"] = $appointment->get()->getResultArray();
                if(!empty($data["appointments"])) {
                    
                    foreach($data["appointments"] as $key => $val) {
                        $model = new OrderPaymentModel;
                        $payments = $model->select("id,paymentAmount,paymentMethod")->where("appointmentId",$val["id"])->get()->getResultArray();
                        if(!empty($payments)) {
                            $data["appointments"][$key]["payments"] = $payments;
                        } else {
                            $data["appointments"][$key]["payments"] = [];
                        }

                        // cart
                        $db = db_connect();
                        $cart = $db->table('carts c');
                        $cart->select("c.id,c.amount,c.serviceSubId,s.name AS service_name,s1.fname,s1.lname,c.stime,c.etime");
                        $cart->join("services s","s.id=c.serviceSubId","left");
                        $cart->join("staffs s1","s1.id=c.staffId","left");
                        $cart->where("c.appointmentId",$val["id"]);
                        $cart->orderBy("c.id","asc");
                        $carts = $cart->get()->getResultArray();
                        if(!empty($carts)) {
                            $data["appointments"][$key]["carts"] = $carts;
                        } else {
                            $data["appointments"][$key]["carts"] = [];
                        }
                        $list = $db->table('order_payments op');
                        $list->select("op.id,pt.name,op.paymentAmount");
                        $list->join("payment_types pt","pt.id=op.paymentMethod","left");
                        $list->where("op.appointmentId",$val["id"]);
                        $list->where("pt.is_deleted",0);
                        $list->orderBy("op.id","desc");
                        $payment_list = $list->get()->getResultArray();
                        if(!empty($payment_list)) {
                            $data["appointments"][$key]["payment_list"] = $payment_list;
                        } else {
                            $data["appointments"][$key]["payment_list"] = [];
                        }
                    }
                } else {
                    foreach($data["appointments"] as $key => $val) {
                        $data["appointments"][$key]["payments"] = [];
                    }
                    foreach($data["appointments"] as $key => $val) {
                        $data["appointments"][$key]["carts"] = [];
                    }
                    foreach($data["appointments"] as $key => $val) {
                        $data["appointments"][$key]["payment_list"] = [];
                    }
                }
                $model = new PaymentTypeModel;
                $data["payment_methods"] = $model->select("id,name")->where(["is_deleted" => 0,"company_id" => static_company_id()])->get()->getResultArray();

                $model = new ServiceModel;
                $data["service_groups"] = $model->select("id,name")->where("is_active",'1')->where('is_old_data',0)->where('is_deleted',0)->get()->getResultArray();
                $model = new SubServiceModel;
                if(!empty($data["service_groups"])) {
                    foreach($data["service_groups"] as $key => $val) {
                        $services = $model->select("id,name")->where("service_group_id",$val["id"])->get()->getResultArray();
                        if(!empty($services)) {
                            $data["service_groups"][$key]["services"] = $services;
                        } else {
                            $data["service_groups"][$key]["services"] = [];
                        }
                    }
                } else {
                    foreach($data["service_groups"] as $key => $val) {
                        $data["service_groups"][$key]["services"] = [];
                    }
                }
                $html = view('admin/appointment/daily_report',$data);
                echo json_encode(array("status" => 1,"html" => $html,"message" => ""));
                exit;
            }
        }

        public function profile()
        {
            if(isset($this->userdata["id"])) {
                $model = new Staff();
                $data['staff'] = $model->where('id',$this->userdata["id"])->first();
                if($data['staff'])
                {
                    $model = new ServiceModel;
                    $data["service_groups"] = $model->select("id,name")->where("is_active",'1')->get()->getResultArray();
                    $model = new SubServiceModel;
                    if(!empty($data["service_groups"])) {
                        foreach($data["service_groups"] as $key => $val) {
                            $services = $model->select("id,name")->where("service_group_id",$val["id"])->get()->getResultArray();
                            if(!empty($services)) {
                                $data["service_groups"][$key]["services"] = $services;
                            } else {
                                $data["service_groups"][$key]["services"] = [];
                            }
                        }
                    }

                    $model = new StaffServiceModel;
                    $staff_services = $model->where('staff_id',$data['staff']['id'])->get()->getResultArray();

                    $data["staff_services"] = "";
                    if($staff_services)
                    {
                        $arr = array();
                        foreach($staff_services as $key => $val)
                        {
                            array_push($arr, $val['service_id']);
                        }
                        $str = implode(",",$arr);
                        $data["staff_services"] = $str;
                    }
                    return view('admin/staff/add_edit',$data);
                } else 
                    return redirect()->route('staffs');
            } else {
                return redirect("admin");
            }
        }

        public function get_customer_appointments()
        {
            $post = $this->request->getVar();

            $total = 0;
            $total_no_show = 0;

            $model = new CustomerModel;
            $customer = $model->select("id")->where("phone",$post["phone"])->where("companyId",static_company_id())->first();
            if(!empty($customer)) {
                $model = new AppointmentModel;
                $total = $model->where("customerId",$customer["id"])->where("status",2)->get()->getNumRows();
                $total_no_show = $model->where("customerId",$customer["id"])->where("status",3)->get()->getNumRows();
            }
            echo json_encode(array("total" => $total,"total_no_show" => $total_no_show));
            exit;
        }

        public function drop_appointment()
        {
            $post = $this->request->getVar();

            $model = new CartModel;
            $_cart = $model->select("appointmentId,staffId,message")->where("id",$post["cart_id"])->first();
            if($_cart) {
                $model = new AppointmentModel;
                $count = $model->where("id",$_cart["appointmentId"])->where("status",1)->get()->getNumRows();
                if($count > 0) {
                    $message = "";
                    $color = "#000000";
                    $model = new Staff;
                    $staff = $model->select("fname,lname,color")->where("id",$post["staff_id"])->first();
                    if(!empty($staff)) {
                        $color = $staff["color"];
                    }

                    $message = $_cart["message"];
                    $model = new Staff;
                    $old_staff = $model->select("fname,lname")->where("id",$_cart["staffId"])->first();
                    if(!empty($old_staff)) {
                        $message = str_replace($old_staff["fname"]." ".$old_staff["lname"],$staff["fname"]." ".$staff["lname"],$_cart["message"]);
                    }
                    $model = new CartModel;
                    $model->update($post["cart_id"],array("message" => $message,"staffId" => $post["staff_id"],"color" => $color,"stime" => $post["new_stime"].":00","etime" => $post["new_etime"].":00"));

                    $model = new EntryModel;
                    $model->where("cart_id",$post["cart_id"])->set(array("staff_id" => $post["staff_id"],"stime" => $post["new_stime"].":00","etime" => $post["new_etime"].":00"))->update();

                    echo json_encode(array("status" => 200,"message" => ""));
                } else {
                    echo json_encode(array("status" => 400,"message" => "You can't drop checkout appointment."));
                }
            }
        }

        public function get_available_staff_time()
        {
            $date = $this->request->getVar('date');
            $date = date("Y-m-d",strtotime($date));
            $response = [];

            $db = db_connect();
            $staff = $db->table('staffs s');
            $staff->select("DISTINCT(s.id),CONCAT_WS(' ',s.fname,s.lname) AS title,s.color,st.stime,st.etime");
            $staff->join("staff_timings st","s.id=st.staffId");
            $staff->where('st.date',$date);
            $staff->where(['s.user_type' => 1,"s.is_active" => 1,"s.is_deleted" => 0]);
            $staff->where('st.companyId',static_company_id());
            $staffs = $staff->get()->getResultArray();
            if($staffs){
                $model = new CompanyModel;
                $company = $model->select("company_etime")->where('id',static_company_id())->first();
                foreach($staffs as $staff) {
                    $isAvailable = ($staff['stime'] !== null && $staff['etime'] !== null) ? true : false;
                    $eventColor = $isAvailable ? "#000000" : "#ffcccb";
                    if ($isAvailable) {
                        $response[] = [
                            "staff_id"   => $staff['id'],
                            "start_time" => $staff['stime'],
                            "end_time"   => $staff['etime'],
                        ];
                    } else {
                        // exit;
                        $response[] = [
                            "staff_id"   => $staff['id'],
                            "start_time" => $date."T".$staff['etime'],  // Example: "2025-02-19T09:00:00"
                            "end_time"   => $date."T".$company["company_etime"],    // Example: "2025-02-19T15:00:00"
                            "color"      => "#d7d7d7", // Assign default color if none,
                            "isAvailable"=> $isAvailable 
                        ];
                    }
                }
            }
            return $this->response->setJSON($response);
        }

        public function reviews()
        {
            if(check_permission("review")) {
                $db = db_connect();
                $review = $db->table("reviews r");
                $review = $review->join("customers c","c.id=r.given_by");
                $review = $review->select("r.id,r.star,r.comment,r.is_approved,r.created_at,c.name as given_by");
                $review = $review->where("r.deleted_at IS NULL");
                $review = $review->where("r.company_id",static_company_id());
                $review = $review->orderBy("r.id","desc");
                $data["reviews"] = $review->get()->getResultArray();
                return view('admin/reviews',$data);
            } else {
                return redirect("profile");    
            }
        }

        public function remove_review($id)
        {
            $model = new Review();
            if($model->update($id,["deleted_at" => date("Y-m-d H:i:s")])) {
                return redirect("reviews");
            }
        }

        public function approve_review($id)
        {
            $model = new Review();
            if($model->update($id,["is_approved" => 1,"updated_at" => date("Y-m-d H:i:s")])) {
                return redirect("reviews");
            }
        }

        public function get_salon_timing()
        {
            $post = $this->request->getVar();

            $model = new CompanyModel;
            $company = $model->select("company_stime,company_sunday_stime,company_etime,company_sunday_etime")->where("id",static_company_id())->first();
            
            $date = date("Y-m-d",strtotime($post["date"]));
            $shortTimestamp = strtotime($date);
            $shortDay = strtolower(date("D", $shortTimestamp));
            if(in_array($shortDay, ["sun"])) {
                echo json_encode(["stime" => $company["company_sunday_stime"],"etime" => $company["company_sunday_etime"]]);
            } else { 
                echo json_encode(["stime" => $company["company_stime"],"etime" => $company["company_etime"]]);
            }
            exit;
        }
    }
