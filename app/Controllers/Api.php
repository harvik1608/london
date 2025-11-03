<?php 
    namespace App\Controllers;

    use CodeIgniter\RESTful\ResourceController;
    use CodeIgniter\API\ResponseTrait;
    use App\Models\CompanyModel;
    use App\Models\ServiceModel;
    use App\Models\SubServiceModel;
    use App\Models\CustomerModel;
    use App\Models\EmpModel;
    use App\Models\GalleryModel;
    use App\Models\Avatar;
    use App\Models\InquiryModel;
    use App\Models\CartModel;
    use App\Models\AppointmentModel;
    use App\Models\Staff;
    use App\Models\EntryModel;
    use App\Models\ServicePriceModel;
    use App\Models\StaffServiceModel;
    use App\Models\WebsiteEntry;
    use App\Models\Review;
    use App\Models\StaffTimingModel;
    use App\Models\WeekendDiscount;
    use App\Models\WhatsappModel;

    class Api extends ResourceController
    {
        use ResponseTrait;
        protected $helpers = ["custom"];

        public function home()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "home") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY."sdsdsd";
                return $this->respond($response);
            } else {
                $model = new CompanyModel;
                $company = $model->select("isActive,company_name,banner,banners,code,company_service_groups,company_services")->where("id",$post["company_id"])->first();
                if($company)
                {
                    if($company["isActive"] == '1')
                    {
                        $company["banner"] = base_url("public/uploads/company/".$company['banner']);
                        $banners = [];
                        if($company["banners"] != "") {
                            $banners = json_decode($company["banners"],true);
                            foreach($banners as $key => $val) {
                                $banners[$key]["avatar"] = base_url("public/".$val["avatar"]);
                            }
                        }
                        $company["banners"] = $banners;
                        $company_service_groups = explode(",",$company["company_service_groups"]);
                        $company_services = explode(",",$company["company_services"]);
                        
                        $model = new ServiceModel;
                        $company["groups"] = $model->select("id,name,avatar,slug")->where('is_active','1')->where('is_deleted','0')->whereIn('id',$company_service_groups)->orderBy("position","asc")->get()->getResultArray();
                        $company["total_treatment"] = count($company["groups"]);

                        $model = new CustomerModel;
                        $company["total_customer"] = $model->where('companyId',$post["company_id"])->get()->getNumRows();
                        
                        $model = new EmpModel;
                        $company["staffs"] = $model->select('id,fname,lname,designation,avatar')->where('user_type',1)->where('is_shown_on_website',1)->where("is_active",'1')->get()->getResultArray();
                        $company["salon_staffs"] = $model->where('user_type',1)->where("is_active",'1')->get()->getNumRows();
                        $company["total_staff"] = $company["salon_staffs"];

                        $model = new SubServiceModel;
                        $company["total_sub_treatment"] = $model->whereIn("id",$company_services)->where('is_active','1')->get()->getNumRows();
                        if($company["groups"])
                        {
                            foreach($company["groups"] as $key => $val)
                            {
                                if($val["avatar"] == "")
                                    $company["groups"][$key]['avatar'] = base_url("public/uploads/service/default.png");        
                                else 
                                    $company["groups"][$key]['avatar'] = base_url("public/uploads/service_group/".$val['avatar']);        
                            }
                        }
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Info found";
                        $response[RESPONSE_DATA] = $company;
                    } else {
                        $site["banner"] = base_url("public/closed.png");

                        $response[RESPONSE_STATUS] = RESPONSE_CLOSE;
                        $response[RESPONSE_MESSAGE] = "This parlour is temporary closed.";
                        $response[RESPONSE_DATA] = $site;
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "Parlour not found";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function treatments()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "treatments") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $ids1 = $service_ids = array();
                
                $model = new CompanyModel;
                $company = $model->select("company_service_groups,company_services")->where("id",$post["company_id"])->first();
                if($company && !is_null($company["company_service_groups"]) && $company["company_service_groups"] != "") {
                    $ids1 = explode(",",$company["company_service_groups"]);
                }
                if($company && !is_null($company["company_services"]) && $company["company_services"] != "") {
                    $service_ids = explode(",",$company["company_services"]);
                }
                if($ids1) {
                    $model = new ServiceModel;
                    $treatments = $model->select("id,name,slug,avatar")->where(["is_active" => '1','is_deleted' => 0])->whereIn('id',$ids1)->orderBy("position","asc")->get()->getResultArray();
                    if($treatments) {
                        $model = new SubServiceModel;
                        foreach($treatments as $key => $val) {
                            $total_services = $model->where('service_group_id',$val["id"])->where("is_deleted",0)->get()->getNumRows();
                            $treatments[$key]['total_services'] = $total_services;
                            if($val["avatar"] == "") {
                                $treatments[$key]['avatar'] = base_url("public/uploads/service/default.png");
                            } else {
                                $treatments[$key]['avatar'] = base_url("public/uploads/service_group/".$val['avatar']); 
                            }
                        }
                    }
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Total ".count($treatments)." treatment(s) found";
                    $response[RESPONSE_DATA] = $treatments;
                }
                return $this->respond($response);
            }
        }
        
        public function fetch_services()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','service_group_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "fetch_services") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $service_ids = array();
                
                $model = new CompanyModel;
                $company = $model->select("company_services")->where("id",$post["company_id"])->first();
                if($company && !is_null($company["company_services"]) && $company["company_services"] != "") {
                    $service_ids = explode(",",$company["company_services"]);
                }
                $treatments = array();
                $model = new SubServiceModel;
                $result = $model->select("id,name")->where(["is_old_data" => 0,"is_deleted" => 0])->where('service_group_id',$post["service_group_id"])->get()->getResultArray();
                if($result) {
                    foreach($result as $row) {
                        if(in_array($row["id"],$service_ids)) {
                            $treatments[] = $row;
                        }
                    }
                }
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Total ".count($treatments)." treatment(s) found";
                $response[RESPONSE_DATA] = $treatments;
                return $this->respond($response);
            }
        }

        public function treatment()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','slug');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "treatment") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CompanyModel;
                $company = $model->select("currency,code,company_services")->where("id",$post["company_id"])->first();

                $model = new ServiceModel;
                $service = $model->select("id,name,avatar,note")->where('is_active','1')->where('is_deleted','0')->where("slug",$post["slug"])->first();
                if($service)
                {
                    $company_services = explode(",",$company["company_services"]);
                    $model = new SubServiceModel;
                    $treatments = $model->select("id,name,json")->where(["is_old_data" => 0,"is_deleted" => 0])->where('is_active','1')->whereIn('id',$company_services)->where('service_group_id',$service['id'])->orderBy("position","asc")->get()->getResultArray();
                    if($treatments)
                    {
                        $data['treatment_id'] = $service['id'];
                        $data['treatment_name'] = $service['name'];
                        $data['treatment_note'] = $service['note'];
                        $data['avatar'] = $service['avatar'] == "" ? base_url("public/upload/service/default.png") : base_url("public/uploads/service_group/".$service['avatar']);
                        $data['currency_sign'] = $company["currency"];
                        $data['code'] = $company["code"];
                        $data['sub_treatments'] = $treatments;

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Total ".count($treatments)." sub treatment(s) found";
                        $response[RESPONSE_DATA] = $data;
                    } else {
                        $data['treatment_name'] = $service['name'];
                        $data['treatment_note'] = $service['note'];
                        $data['currency_sign'] = $company["currency"];
                        $data['code'] = $company["code"];
                        $data['avatar'] = $service['avatar'] == "" ? base_url("public/upload/service/default.png") : base_url("public/uploads/service_group/".$service['avatar']);
                        $data['sub_treatments'] = array();

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "No sub treatment found";
                        $response[RESPONSE_DATA] = $data;
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "No sub treatment found";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function photos()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "photos") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new Avatar;
                $photos = $model->select("id,name")->where('company_id',$post['company_id'])->get()->getResultArray();
                if($photos)
                {
                    foreach($photos as $key => $val)
                    {
                        $photos[$key]['name'] = base_url("public/uploads/gallery/".$val['name']); 
                    }
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Total ".count($photos)." photo(s) found";
                    $response[RESPONSE_DATA] = $photos;
                    $model = new CompanyModel;
                    
                    $banner = "";
                    $company = $model->select("banners")->where("id",$post["company_id"])->first();
                    if($company["banners"] != "") {
                        $banner = json_decode($company["banners"],true);
                        $banner = base_url("public/".$banner[0]['avatar']);
                    }
                    $response["company"] = $banner;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "No photo found";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function send_inquiry()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','name','email','phone','message');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "send_inquiry") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CompanyModel;
                $company = $model->select("*")->where("id",$post["company_id"])->first();
                if($company) {
                    $ip = getClientIp();
                    $post["company_name"] = $company["company_name"];
                    $post["website_url"] = $company["website_url"];
                    $post["company_email"] = $company["company_email"];
                    $message = view("template/contact_us",$post);
                    $code = send_email($post["email"],"Thank You for Contacting Us – We have Received Your Message",$message,$company,$ip);   
                    if($code == 200) {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Your inquiry has been sent.";
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                        $response[RESPONSE_MESSAGE] = "Sorry Email can not send.";
                    }
                }
                return $this->respond($response);
                
                // $model = new CompanyModel;
                // $company = $model->select("timezone")->where("id",$post["company_id"])->first();
                // if($company)
                // {
                //     $companyId = $post['company_id'];
                //     date_default_timezone_set($company['timezone']);

                //     unset($post['key']);
                //     unset($post['tag']);
                //     unset($post['company_id']);
                //     $post['companyId'] = $companyId;
                //     $post['addedBy'] = 0;
                //     $post['updatedBy'] = 0;
                //     $post['createdAt'] = format_date(5);
                //     $post['updatedAt'] = format_date(5);

                //     $model = new InquiryModel;
                //     $model->insert($post);
                //     if($model->getInsertID() > 0)
                //     {
                //         $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                //         $response[RESPONSE_MESSAGE] = "Your inquiry has been sent. We will revert you shortly.";
                //     } else {
                //         $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                //         $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                //         $response[RESPONSE_DATA] = array();
                //     }
                //     return $this->respond($response);
                // }
            }
        }

        public function sub_treatments()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','service_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "sub_treatments") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CompanyModel;
                $company = $model->select("currency,company_service_groups,company_services")->where("id",$post["company_id"])->first();
                if($company)
                {
                    $company_services = explode(",",$company["company_services"]);
                    $model = new SubServiceModel;
                    $sub_treatments = $model->where(["is_old_data" => 0,"is_deleted" => 0])->where("service_group_id",$post["service_id"])->whereIn("id",$company_services)->orderBy("id","asc")->get()->getResultArray();
                    if($sub_treatments)
                    {
                        foreach($sub_treatments as $key => $val) {
                            $sub_treatments[$key]["price_json"] = get_service_prices($val["id"],$post["company_id"],$post["date"]);
                        }
                        $data["currency"] = $company['currency'];
                        $data["sub_treatments"] = $sub_treatments;

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Total ".count($sub_treatments)." sub-treatments found";
                        $response[RESPONSE_DATA] = $data;    
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                        $response[RESPONSE_MESSAGE] = "No sub-treatments found";
                        $response[RESPONSE_DATA] = array();    
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "Parlour not found";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function check_staff()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "check_staff") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CompanyModel;
                $company = $model->select("currency")->where("id",$post["company_id"])->first();
                if($company)
                {
                    $db = db_connect();
                    // $staff = $db->table('staffs s');
                    // $staff->join("staff_timings st","s.id=st.staffId");
                    // $staff->join("staff_services ss","s.id=ss.staff_id");
                    // $staff->where('st.companyId',$post['company_id']);
                    // $staff->where('st.date',$post['date']);
                    // $staff->where('ss.service_id',$post['service_id']);
                    // $count = $staff->get()->getNumRows();
                    // if($count <= 0)
                    // {
                    //     $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    //     $response[RESPONSE_MESSAGE] = "Sorry staff not available for ".$post['serviceNm']." on ".$post["date"];
                    //     $response[RESPONSE_DATA] = array();
                    // } else {
                        $adate = format_date(17,$post['appointmentDate']);   
                        $stime = $post['stime'];
                        $tslot = $post['duration'];
                        $etime = date("H:i:s",strtotime($stime." +".$tslot." minutes"));

                        $staff = $db->table('staffs s');
                        $staff->select("s.id,CONCAT_WS(' ',s.fname,s.lname) AS name,st.stime,st.etime,s.color");
                        $staff->join("staff_services ss","s.id=ss.staff_id");
                        $staff->join("staff_timings st","s.id=st.staffId");
                        $staff->where('ss.service_id',$post['service_id']);
                        // $staff->where('s.companyId',$post['company_id']);
                        $staff->where('st.date',$adate);
                        $staff->groupBy("s.id");
                        $staffs = $staff->get()->getResultArray();

                        if($staffs)
                        {
                            $model = new CartModel;
                            foreach($staffs as $key => $val)
                            {
                                $carts = $model->select("date,stime,etime")->where('staffId',$val["id"])->where("date",$adate)->get()->getResultArray();
                                if($carts)
                                    $staffs[$key]['appointments'] = $carts;
                                else 
                                    $staffs[$key]['appointments'] = array();
                            }
                            $appDateStime = $adate." ".$stime;
                            $appDateEtime = $adate." ".$etime;
                            foreach($staffs as $key => $val)
                            {
                                $staffs[$key]["status"] = 1;
                                if(!empty($val["appointments"]))
                                {
                                    $flag = 1;
                                    foreach($val["appointments"] as $k => $v)
                                    {
                                        if(strtotime($appDateStime) > strtotime($adate.' '.$v['stime']) && strtotime($appDateStime) < strtotime($adate.' '.$v['etime']))
                                        {
                                            $flag = 0;
                                        } else if(strtotime($appDateEtime) > strtotime($adate.' '.$v['stime']) && strtotime($appDateEtime) < strtotime($adate.' '.$v['etime'])) {
                                            $flag = 0;
                                        } else if(strtotime($appDateStime) > strtotime($adate.' '.$v['stime']) && strtotime($appDateEtime) > strtotime($adate.' '.$v['stime']) && strtotime($appDateStime) < strtotime($adate.' '.$v['etime']) && strtotime($appDateEtime) < strtotime($adate.' '.$v['etime'])) {
                                            $flag = 0;
                                        } else if(strtotime($adate.' '.$v['stime']) > strtotime($appDateStime) && strtotime($adate.' '.$v['etime']) < strtotime($appDateEtime)) {
                                            $flag = 0;
                                        }
                                    }
                                    $staffs[$key]["status"] = $flag;
                                }
                            }
                        }
                        $res_data["etime"] = $etime;
                        $res_data["staffs"] = $staffs;
                        $res_data["currency"] = $company["currency"];
                        
                        // add cart
                        // $model = new WebsiteEntry;
                        // $entry = $model->insert([
                        //     'uniq_id'       => md5($post["uniq_id"]),
                        //     'date'          => $post["appointmentDate"],
                        //     'service_id'    => $post["service_id"],
                        //     'service_nm'    => $post["serviceNm"],
                        //     'duration'      => $post["duration"],
                        //     'salon_id'      => $post["company_id"]
                        // ]);

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Total ".count($staffs)." staff(s) found.";
                        $response[RESPONSE_DATA] = $res_data;
                    // }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "Parlour not found";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function book_appointment()
        {
            try {
                // error_reporting(E_ALL);
                // ini_set('display_errors', 1);
                $post = $this->request->getVar();
                $input_parameter = array('key','tag','company_id');
                $validation = ParamValidation($input_parameter, $post);
    
                if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
                {
                    return $this->respond($validation);
                } else if($post['key'] != APP_KEY || $post['tag'] != "book_appointment") {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                    return $this->respond($response);
                } else {
                    $isConfirmationEmailSend = 1;
                    $date_15 = date("Y-m-d H:i:s",strtotime("-15 minutes"));
                    $booked_date = date('Y-m-d',strtotime($post["appointment_date"]));

                    $model = new WebsiteEntry;
                    $items = $model->where(["company_id" => $post["company_id"],"customer_id" => $post["customer_id"]])->where("datetime >=",$date_15)->where("is_final",1)->get()->getResultArray();
                    $customer_name = $customer_email = $customer_phone = $customer_note = "";
                    if(!empty($items)) {
                        $post["uniq_id"] = md5(time());
                        $phone = $post["customer_phone"];
                        $name = $post["customer_name"];
                        
                        $post["service_item"]               = array_column($items, 'service_group_id');
                        $post["service_duration"]           = array_column($items, 'duration');
                        $post["service_discount_amount"]    = array_column($items, 'discount_amount');
                        $post["service_amount"]             = array_column($items, 'amount');
                        $post["sub_service_name"]           = array_column($items, 'caption');
                        $post["service_name"]               = array_column($items, 'service_name');
                        $post["service_sub_item"]           = array_column($items, 'service_id');
                        // $post["caption"] = array_column($items, 'caption');
                        $model = new CustomerModel;
                        $customer = $model->select('id,isConfirmationEmailSend')->where('phone',$post["customer_phone"])->where("companyId",$post["company_id"])->first();
                        if($customer) {
                            $customer_name = $post["customer_name"];
                            $customer_email = $post["customer_email"];
                            $customer_phone = $post["customer_phone"];
                            $customer_note = $post["customer_note"];

                            $customer_id = $customer['id'];
                            $cparam['name']    = $post["customer_name"];
                            $cparam['email']   = $post['customer_email'];
                            // $cparam['note']    = $post['customer_note'];
                            
                            $model = new CustomerModel;
                            $model->update($customer_id,$cparam);
                            
                            $isConfirmationEmailSend = $customer['isConfirmationEmailSend'];
                        } else {
                            $customer_name = $post["customer_name"];
                            $customer_email = $post["customer_email"];
                            $customer_phone = $post["customer_phone"];
                            $customer_note = $post["customer_note"];

                            $cparam['name']    = $post["customer_name"];
                            $cparam['phone']   = $post["customer_phone"];
                            $cparam['email']   = $post["customer_email"];
                            // $cparam['note']    = $post["customer_note"];
                            $cparam['marketing_email'] = "N";
                            $cparam['note']    = "";
                            $cparam['is_sync_with_google']    = 0;
                            $cparam['addedBy'] = 0;
                            $cparam['companyId'] = $post["company_id"];
                            $cparam['updatedBy'] = 0;
                            $cparam['createdAt'] = format_date(5);
                            $cparam['updatedAt'] = "";
                    
                            $model = new CustomerModel;
                            $model->insert($cparam);
                            $customer_id = $model->getInsertID();
                        }
                        $params["uniq_id"]      = $post["uniq_id"];
                        $params['customerId']   = $customer_id;
                        $params['subTotal']     = 0;
                        $params['discountAmt']  = 0;
                        $params['finalAmt']     = 0;
                        $params['bookingDate']  = $booked_date;
                        $params['status']       = 1;
                        $params['bookedFrom']   = 1;
                        $params['note']         = $post['customer_note'];
                        $params['type']         = "Y";
                        $params['flag']         = "Y";
                        $params['addedDate']    = format_date(1);
                        $params['addedBy']      = 0;
                        $params['companyId']    = $post["company_id"];
                        $params['is_booked_from_website'] = 1;
                        $params['updatedBy']    = 0;
                        $params['createdAt']    = format_date(5);
                        $params['updatedAt']    = "";
                        $model = new AppointmentModel;
                        $model->insert($params);
                        $appointment_id = $model->getInsertID();
                        if($appointment_id > 0) {

                            $booked_items = [];
                            $available_staffs = explode(",",$post["available_staffs"]);
                            $amount = 0;
                            for($i = 0; $i < count($post['service_item']); $i++) {
                                // Set appointment time and calculate the end time
                                if ($i == 0) {
                                    $date = new \DateTime($booked_date . ' ' . $post["appointment_time"]);
                                    $date->modify('+' . $post["service_duration"][$i] . ' minutes');
                                    $stime = $post["appointment_time"];
                                    $etime = $date->format('H:i:s');
                                } else {
                                    $date = new \DateTime($booked_date . ' ' . $etime);
                                    $date->modify('+' . $post["service_duration"][$i] . ' minutes');
                                    $stime = $etime;
                                    $etime = $date->format('H:i:s');
                                }
                                // Loop through available staff and check for availability
                                $staff_assigned_id = 0;
                                $isStaffAvailable = false;
                                // Loop through the available staff
                                foreach($available_staffs as $staff_id) {
                                    $db = db_connect();
                                    $query = $db->table("staff_timings st");
                                    $query = $query->where(["st.companyId" => $post["company_id"],"st.staffId" => $staff_id,"st.date" => $booked_date]);
                                    $staff_available = $query->get()->getNumRows();
                                    if($staff_available > 0) {
                                        // Check if the staff member is free during the time slot
                                        $model = new StaffServiceModel;
                                        $staff_given_service = $model->where("staff_id",$staff_id)->where("service_id",$post['service_sub_item'][$i])->get()->getNumRows();
                                        if($staff_given_service > 0) {
                                            $model = db_connect();
                                            $query = $model->table("carts c")
                                            ->where("c.staffId", $staff_id)
                                            ->where("c.date", $booked_date)
                                            ->where("c.isComplete", "N")
                                            ->where("c.is_cancelled", 0)
                                            ->where("c.companyId", $post["company_id"])
                                            ->where("((c.stime >= '".$stime."' AND c.stime < '".$etime."') OR (c.etime > '".$stime."' AND c.etime <= '".$etime."') OR (c.stime <= '".$stime."' AND c.etime >= '".$etime."'))");
                                            $busySlots = $query->get()->getResultArray();
                                    
                                            // If no busy slots were found, the staff member is available
                                            if (empty($busySlots)) {
                                                $staff_assigned_id = $staff_id;
                                                $isStaffAvailable = true;
                                                break;  // Exit the loop once we find a free staff member
                                            }
                                        }
                                    }
                                }
                                // If no available staff was found, handle the situation (e.g., show an error or skip)
                                if (!$isStaffAvailable) {
                                    // Optionally, you can return an error message or set a flag
                                    // Example: echo "No staff available for the requested time slot.";
                                    continue;  // Skip this iteration and move to the next service
                                }
                                // Get staff details
                                $model = new Staff;
                                $staff = $model->select("fname, lname, color")->where("id", $staff_assigned_id)->first();
                                if ($staff) {
                                    $staff_name = $staff["fname"] . " " . $staff["lname"];
                                    $staffcolor = $staff["color"];
                                } else {
                                    $staff_name = "";
                                    $staffcolor = "";
                                }
                            
                                // Calculate the amount
                                if($post["service_discount_amount"][$i] > 0) {
                                    $actual_amt = $post["service_discount_amount"][$i];
                                } else {
                                    $actual_amt = $post['service_amount'][$i];
                                }
                                $amount = $amount + $actual_amt;
                            
                                // Create the message based on the service details
                                $email_message = "";
                                if ($post['sub_service_name'][$i] != "") {
                                    $message = $phone . " - " . $name . "\n" . $post['service_name'][$i] . "-\n" . $post['sub_service_name'][$i];
                                    $email_message = $post['service_name'][$i] . "-\n" . $post['sub_service_name'][$i];
                                } else {
                                    $message = $phone . " - " . $name . "\n" . $post['service_name'][$i];
                                    $email_message = $post['service_name'][$i];
                                }
                                $booked_items[] = array("service" => $email_message,"duration" => $post['service_duration'][$i],"price" => $post['service_amount'][$i],"time" => format_datetime($stime,2)." To ".format_datetime($etime,2));
                                // Add the staff name and color to the message
                                $message = $message . "\n" . $staff_name;
                            
                                
                            
                                // Insert the service details into the carts table
                                $carts = array(
                                    'uniq_id' => $post["uniq_id"],
                                    'appointmentId' => $appointment_id,
                                    'date' => $booked_date,
                                    'stime' => $stime,
                                    'duration' => $post['service_duration'][$i],
                                    'etime' => $etime,
                                    'staffId' => $staff_assigned_id,
                                    'serviceId' => $post['service_item'][$i],
                                    'serviceNm' => $post['service_name'][$i],
                                    'caption' => $post['sub_service_name'][$i],
                                    'serviceSubId' => $post['service_sub_item'][$i],
                                    'actual_amount' => $post['service_amount'][$i],
                                    'amount' => $post['service_discount_amount'][$i] > 0 ? $post['service_discount_amount'][$i] : $post['service_amount'][$i],
                                    'message' => $message,
                                    'isStaffBusy' => 0,
                                    'addedBy' => 0,
                                    'companyId' => $post["company_id"],
                                    'color' => $staffcolor,
                                    'updatedBy' => 0,
                                    'createdAt' => format_date(5),
                                    'updatedAt' => "",
                                );
                                $model = new CartModel;
                                $model->insert($carts);
                                $cart_id = $model->getInsertID();
                            
                                // Insert entry details into the entry model
                                $entries = array(
                                    'uniq_id' => $post["uniq_id"],
                                    'appointment_id' => $appointment_id,
                                    'date' => $booked_date,
                                    'stime' => $stime,
                                    'duration' => $post['service_duration'][$i],
                                    'etime' => $etime,
                                    'staff_id' => $staff_assigned_id,
                                    'service_group_id' => $post['service_item'][$i],
                                    'service_id' => $post['service_sub_item'][$i],
                                    'price' => $post['service_discount_amount'][$i] > 0 ? $post['service_discount_amount'][$i] : $post['service_amount'][$i],
                                    'company_id' => $post["company_id"],
                                    'showbusystaff' => 0,
                                    'flag' => 0,
                                    'resource_id' => $staff_assigned_id,
                                    'caption' => "",
                                    'sub_caption' => $post['sub_service_name'][$i],
                                    'is_turn' => 0,
                                    'cart_id' => $cart_id,
                                    'created_at' => date('Y-m-d H:i:s'),
                                );
                                // 'caption' => $post["service_name"][$i],
                                $model = new EntryModel;
                                $model->insert($entries);
                            }
                            $model = new AppointmentModel;
                            $model->update($appointment_id,array("subTotal" => $amount,"finalAmt" => $amount));
                            
                            $model = new WebsiteEntry;
                            $model->where(["customer_id" => $post["customer_id"],"company_id" => $post["company_id"]])->delete();

                            $model = new CompanyModel;
                            $company = $model->where('id',$post["company_id"])->first();
                            
                            $model = new AppointmentModel;
                            $appointment = $model->select("subTotal")->where("id",$appointment_id)->first();
                            if($appointment["subTotal"] > 0) {
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
                                $emaildata["booking_date"] = format_datetime($booked_date,1);
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
                            $status = RESPONSE_FLAG_SUCCESS;
                            $message = "Appointment booked successfully.";
                        } else {
                            $status = RESPONSE_FLAG_FAIL;
                            $message = "Oops! Something went wrong.";
                        }
                    } else {
                        $status = RESPONSE_FLAG_FAIL;
                        $message = "Items not found.";
                    }
                    $response[RESPONSE_STATUS] = $status;
                    $response[RESPONSE_MESSAGE] = $message;
                    return $this->respond($response);
                }
            } catch(\Throwable $e) {
                return $this->respond([
                    RESPONSE_STATUS => RESPONSE_FLAG_FAIL,
                    RESPONSE_MESSAGE => "Internal Server Error: " . $e->getMessage(),
                ]);
            }
        }
        
        

        public function book_appointment_backup()
        {
            try {
                $post = $this->request->getVar();
                $input_parameter = array('key','tag','company_id');
                $validation = ParamValidation($input_parameter, $post);
    
                if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
                {
                    return $this->respond($validation);
                } else if($post['key'] != APP_KEY || $post['tag'] != "book_appointment") {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                    return $this->respond($response);
                } else {
                    $booked_date = date('Y-m-d',strtotime($post["appointment_date"]));

                    $model = new WebsiteEntry;
                    $items = $model->where(["company_id" => $post["company_id"],"customer_id" => $post["customer_id"]])->get()->getResultArray();
                    if(!empty($items)) {
                        $post["uniq_id"] = md5(time());
                        $phone = $post["customer_phone"];
    		            $name = $post["customer_name"];
                        
                        $post["service_item"] = array_column($items, 'service_group_id');
                        $post["service_duration"] = array_column($items, 'duration');
                        $post["service_amount"] = array_column($items, 'amount');
                        $post["sub_service_name"] = array_column($items, 'caption');
                        $post["service_name"] = array_column($items, 'service_name');
                        $post["service_sub_item"] = array_column($items, 'service_id');
                        // $post["caption"] = array_column($items, 'caption');
                        $model = new CustomerModel;
                		$customer = $model->select('id')->where('phone',$post["customer_phone"])->first();
                		if($customer) {
                			$customer_id = $customer['id'];
                			$cparam['name']    = $post["customer_name"];
                			$cparam['email']   = $post['customer_email'];
                			$cparam['note']    = $post['customer_note'];
                			
                			$model = new CustomerModel;
                			$model->update($customer_id,$cparam);
                		} else {
                			$cparam['name']    = $post["customer_name"];
                			$cparam['phone']   = $post["customer_phone"];
                			$cparam['email']   = $post["customer_email"];
                			$cparam['note']    = $post["customer_note"];
                			$cparam['marketing_email'] = "N";
                			$cparam['note']    = "";
                			$cparam['is_sync_with_google']    = 0;
                			$cparam['addedBy'] = 0;
                			$cparam['companyId'] = $post["company_id"];
                			$cparam['updatedBy'] = 0;
                			$cparam['createdAt'] = format_date(5);
                			$cparam['updatedAt'] = "";
                	
                			$model = new CustomerModel;
                			$model->insert($cparam);
                			$customer_id = $model->getInsertID();
                		}
                		$params["uniq_id"]      = $post["uniq_id"];
                		$params['customerId']   = $customer_id;
                		$params['subTotal']     = 0;
                		$params['discountAmt']  = 0;
                		$params['finalAmt']     = 0;
                		$params['bookingDate']  = $booked_date;
                		$params['status']       = 1;
                		$params['bookedFrom']   = 1;
                		$params['note']         = $post['customer_note'];
                		$params['type']         = "Y";
                		$params['flag']         = "Y";
                		$params['addedDate']    = format_date(1);
                		$params['addedBy']      = 0;
                		$params['companyId']    = $post["company_id"];
                		$params['updatedBy']    = 0;
                		$params['createdAt']    = format_date(5);
                		$params['updatedAt']    = "";
                		$model = new AppointmentModel;
                		$model->insert($params);
                		$appointment_id = $model->getInsertID();
                		if($appointment_id > 0) {
                		    $available_staffs = explode(",",$post["available_staffs"]);
                		    $amount = 0;
                		    for($i = 0; $i < count($post['service_item']); $i++) {
                    			// Set appointment time and calculate the end time
                    			if ($i == 0) {
                    				$date = new \DateTime($booked_date . ' ' . $post["appointment_time"]);
                    				$date->modify('+' . $post["service_duration"][$i] . ' minutes');
                    				$stime = $post["appointment_time"];
                    				$etime = $date->format('H:i:s');
                    			} else {
                    				$date = new \DateTime($booked_date . ' ' . $etime);
                    				$date->modify('+' . $post["service_duration"][$i] . ' minutes');
                    				$stime = $etime;
                    				$etime = $date->format('H:i:s');
                    			}
                    			// Loop through available staff and check for availability
                    			$staff_assigned_id = 0;
                    			$isStaffAvailable = false;
                    		
                    			// Loop through the available staff
                    			foreach($available_staffs as $staff_id) {
                                    $db = db_connect();
                                    $query = $db->table("staff_timings st");
                                    $query = $query->where(["st.companyId" => $post["company_id"],"st.staffId" => $staff_id,"st.date" => $booked_date]);
                                    $staff_available = $query->get()->getNumRows();
                                    if($staff_available > 0) {
                                        // Check if the staff member is free during the time slot
                                        $model = new StaffServiceModel;
                                        $staff_given_service = $model->where("staff_id",$staff_id)->where("service_id",$post['service_sub_item'][$i])->get()->getNumRows();
                                        if($staff_given_service > 0) {
                                            $model = db_connect();
                                            $query = $model->table("carts c")
                                            ->where("c.staffId", $staff_id)
                                            ->where("c.date", $booked_date)
                                            ->where("((c.stime >= '".$stime."' AND c.stime < '".$etime."') OR (c.etime > '".$stime."' AND c.etime <= '".$etime."') OR (c.stime <= '".$stime."' AND c.etime >= '".$etime."'))");
                                            $busySlots = $query->get()->getResultArray();
                                    
                                            // If no busy slots were found, the staff member is available
                                            if (empty($busySlots)) {
                                                $staff_assigned_id = $staff_id;
                                                $isStaffAvailable = true;
                                                break;  // Exit the loop once we find a free staff member
                                            }
                                        }
                                    }
                    			}
                    			// If no available staff was found, handle the situation (e.g., show an error or skip)
                    			if (!$isStaffAvailable) {
                    				// Optionally, you can return an error message or set a flag
                    				// Example: echo "No staff available for the requested time slot.";
                    				continue;  // Skip this iteration and move to the next service
                    			}
                    		
                    			// Get staff details
                    			$model = new Staff;
                    			$staff = $model->select("fname, lname, color")->where("id", $staff_assigned_id)->first();
                    			if ($staff) {
                    				$staff_name = $staff["fname"] . " " . $staff["lname"];
                    				$staffcolor = $staff["color"];
                    			} else {
                    				$staff_name = "";
                    				$staffcolor = "";
                    			}
                    		
                    			// Calculate the amount
                    			$amount += $post['service_amount'][$i];
                    		
                    			// Create the message based on the service details
                    			if ($post['sub_service_name'][$i] != "") {
                    				$message = $phone . " - " . $name . "\n" . $post['service_name'][$i] . "-\n" . $post['sub_service_name'][$i];
                    			} else {
                    				$message = $phone . " - " . $name . "\n" . $post['service_name'][$i];
                    			}
                    		
                    			// Add the staff name and color to the message
                    			$message = $message . "\n" . $staff_name;
                    		
                    			
                    		
                    			// Insert the service details into the carts table
                    			$carts = array(
                    				'uniq_id' => $post["uniq_id"],
                    				'appointmentId' => $appointment_id,
                    				'date' => $booked_date,
                    				'stime' => $stime,
                    				'duration' => $post['service_duration'][$i],
                    				'etime' => $etime,
                    				'staffId' => $staff_assigned_id,
                    				'serviceId' => $post['service_item'][$i],
                    				'serviceNm' => $post['service_name'][$i],
                    				'caption' => $post['sub_service_name'][$i],
                    				'serviceSubId' => $post['service_sub_item'][$i],
                    				'amount' => $post['service_amount'][$i],
                    				'message' => $message,
                    				'isStaffBusy' => 0,
                    				'addedBy' => 0,
                    				'companyId' => $post["company_id"],
                    				'color' => $staffcolor,
                    				'updatedBy' => 0,
                    				'createdAt' => format_date(5),
                    				'updatedAt' => "",
                    			);
                    			$model = new CartModel;
                    			$model->insert($carts);
                    			$cart_id = $model->getInsertID();
                    		
                    			// Insert entry details into the entry model
                    			$entries = array(
                    				'uniq_id' => $post["uniq_id"],
                    				'appointment_id' => $appointment_id,
                    				'date' => $booked_date,
                    				'stime' => $stime,
                    				'duration' => $post['service_duration'][$i],
                    				'etime' => $etime,
                    				'staff_id' => $staff_assigned_id,
                    				'service_group_id' => $post['service_sub_item'][$i],
                    				'service_id' => $post['service_item'][$i],
                    				'price' => $post['service_amount'][$i],
                    				'company_id' => $post["company_id"],
                    				'showbusystaff' => 0,
                    				'flag' => 0,
                    				'resource_id' => $staff_assigned_id,
                    				'caption' => $post["service_name"][$i],
                    				'is_turn' => 0,
                    				'cart_id' => $cart_id,
                    				'created_at' => date('Y-m-d H:i:s'),
                    			);
                    			$model = new EntryModel;
                    			$model->insert($entries);
                    		}
                    		$model = new AppointmentModel;
    		                $model->update($appointment_id,array("subTotal" => $amount,"finalAmt" => $amount));
    		                
    		                $model = new WebsiteEntry;
    		                $model->where(["customer_id" => $post["customer_id"],"company_id" => $post["company_id"]])->delete();
    		                
    		                $status = RESPONSE_FLAG_SUCCESS;
    		                $message = "Appointment booked successfully.";
                		} else {
                		    $status = RESPONSE_FLAG_FAIL;
    		                $message = "Oops! Something went wrong.";
                		}
                    } else {
                        $status = RESPONSE_FLAG_FAIL;
    		            $message = "Items not found.";
                    }
                    $response[RESPONSE_STATUS] = $status;
                    $response[RESPONSE_MESSAGE] = $message;
                    return $this->respond($response);
                }
            } catch(\Throwable $e) {
                return $this->respond([
                    RESPONSE_STATUS => RESPONSE_FLAG_FAIL,
                    RESPONSE_MESSAGE => "Internal Server Error: " . $e->getMessage(),
                ]);
            }
        }

        public function company()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "company") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CompanyModel;
                if($post['columns'] != "")
                {
                    $company = $model->select($post['columns'])->where("id",$post["company_id"])->first();
                } else {
                    $company = $model->where("id",$post["company_id"])->first();
                }
                if($company)
                {
                    if($company["isActive"] == '1')
                    {
                        $company["company_logo"] = base_url("public/uploads/company/".$company['company_logo']);
                        $company["banner"] = base_url("public/uploads/company/".$company['banner']);
                        $banners = [];
                        if($company["banners"] != "") {
                            $banners = json_decode($company["banners"],true);
                            foreach($banners as $key => $val) {
                                $banners[$key]["avatar"] = base_url("public/".$val["avatar"]);
                            }
                        }
                        $company["banners"] = $banners;

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Parlour found";
                        $response[RESPONSE_DATA] = $company;
                    } else {
                        $site["banner"] = base_url("public/closed.png");

                        $response[RESPONSE_STATUS] = RESPONSE_CLOSE;
                        $response[RESPONSE_MESSAGE] = "This parlour is temporary closed.";
                        $response[RESPONSE_DATA] = $site;
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "Parlour not found";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }
        
        
        
        public function book_appointment_from_website()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "book_appointment_from_website") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $phone = $post["customer_phone"];
                $name = $post["customer_name"];
                $post["service_item"] = json_decode($post["service_item"]);
                $post["service_duration"] = json_decode($post["service_duration"]);
                $post["service_amount"] = json_decode($post["service_amount"]);
                $post["sub_service_name"] = json_decode($post["sub_service_name"]);
                $post["service_name"] = json_decode($post["service_name"]);
                $post["service_sub_item"] = json_decode($post["service_sub_item"]);
                $model = new CustomerModel;
                $customer = $model->select('id')->where('phone',$post["customer_phone"])->first();
                if($customer)
                {
                    $customer_id = $customer['id'];
                    $cparam['name']    = $post["customer_name"];
                    $cparam['email']   = $post['customer_email'];
                    $cparam['note']    = $post['customer_note'];
            
                    $model = new CustomerModel;
                    $model->update($customer_id,$cparam);
                } else {
                    $cparam['name']    = $post["customer_name"];
                    $cparam['phone']   = $post["customer_phone"];
                    $cparam['email']   = $post["customer_email"];
                    $cparam['note']    = $post["customer_note"];
                    $cparam['marketing_email'] = "N";
                    $cparam['note']    = "";
                    $cparam['is_sync_with_google']    = 0;
                    $cparam['addedBy'] = 0;
                    $cparam['companyId'] = $post["company_id"];
                    $cparam['updatedBy'] = 0;
                    $cparam['createdAt'] = format_date(5);
                    $cparam['updatedAt'] = "";
            
                    $model = new CustomerModel;
                    $model->insert($cparam);
                    $customer_id = $model->getInsertID();
                }
                $params["uniq_id"]      = $post["uniq_id"];
                $params['customerId']   = $customer_id;
                $params['subTotal']     = 0;
                $params['discountAmt']  = 0;
                $params['finalAmt']     = 0;
                $params['bookingDate']  = date('Y-m-d',strtotime($post["appointment_date"]));
                $params['status']       = 1;
                $params['bookedFrom']   = 1;
                $params['note']         = $post['customer_note'];
                $params['type']         = "Y";
                $params['flag']         = "Y";
                $params['addedDate']    = format_date(1);
                $params['addedBy']      = 0;
                $params['companyId']    = $post["company_id"];
                $params['updatedBy']    = 0;
                $params['createdAt']    = format_date(5);
                $params['updatedAt']    = "";
                $model = new AppointmentModel;
                $model->insert($params);
                $appointment_id = $model->getInsertID();
                
                $available_staffs = explode(",",$post["available_staffs"]);
                $amount = 0;
                $items = array(); 
                for($i = 0; $i < count($post['service_item']); $i++) {
                    // Set appointment time and calculate the end time
                    if ($i == 0) {
                        $date = new \DateTime($post["appointment_date"] . ' ' . $post["appointment_time"]);
                        $date->modify('+' . $post["service_duration"][$i] . ' minutes');
                        $stime = $post["appointment_time"];
                        $etime = $date->format('H:i:s');
                    } else {
                        $date = new \DateTime($post["appointment_date"] . ' ' . $etime);
                        $date->modify('+' . $post["service_duration"][$i] . ' minutes');
                        $stime = $etime;
                        $etime = $date->format('H:i:s');
                    }
                    // Loop through available staff and check for availability
                    $staff_assigned_id = 0;
                    $isStaffAvailable = false;
                
                    // Loop through the available staff
                    foreach($available_staffs as $staff_id) {
                        // Check if the staff member is free during the time slot
                        $model = new StaffServiceModel;
                        $staff_given_service = $model->where("staff_id",$staff_id)->where("service_id",$post['service_item'][$i])->get()->getNumRows();
                        if($staff_given_service > 0) {
                            $model = db_connect();
                            $query = $model->table("carts c")
                            ->where("c.staffId", $staff_id)
                            ->where("c.date", $post["appointment_date"])
                            ->where("((c.stime >= '".$stime."' AND c.stime < '".$etime."') OR (c.etime > '".$stime."' AND c.etime <= '".$etime."') OR (c.stime <= '".$stime."' AND c.etime >= '".$etime."'))");
                            $busySlots = $query->get()->getResultArray();
                    
                            // If no busy slots were found, the staff member is available
                            if (empty($busySlots)) {
                                $staff_assigned_id = $staff_id;
                                $isStaffAvailable = true;
                                break;  // Exit the loop once we find a free staff member
                            }
                        } 
                    }
                
                    // If no available staff was found, handle the situation (e.g., show an error or skip)
                    if (!$isStaffAvailable) {
                        // Optionally, you can return an error message or set a flag
                        // Example: echo "No staff available for the requested time slot.";
                        continue;  // Skip this iteration and move to the next service
                    }
                
                    // Get staff details
                    $model = new Staff;
                    $staff = $model->select("fname, lname, color")->where("id", $staff_assigned_id)->first();
                    if ($staff) {
                        $staff_name = $staff["fname"] . " " . $staff["lname"];
                        $staffcolor = $staff["color"];
                    } else {
                        $staff_name = "";
                        $staffcolor = "";
                    }
                
                    // Calculate the amount
                    $amount += $post['service_amount'][$i];
                
                    // Create the message based on the service details
                    if ($post['sub_service_name'][$i] != "") {
                        $message = $phone . " - " . $name . "\n" . $post['service_name'][$i] . "-\n" . $post['sub_service_name'][$i];
                    } else {
                        $message = $phone . " - " . $name . "\n" . $post['service_name'][$i];
                    }
                    $items[] = array("service" => $message,"duration" => $post['service_duration'][$i],"price" => $post['service_amount'][$i]);
                    // Add the staff name and color to the message
                    $message = $message . "\n" . $staff_name;
                
                    
                
                    // Insert the service details into the carts table
                    $carts = array(
                        'uniq_id' => $post["uniq_id"],
                        'appointmentId' => $appointment_id,
                        'date' => date('Y-m-d',strtotime($post["appointment_date"])),
                        'stime' => $stime,
                        'duration' => $post['service_duration'][$i],
                        'etime' => $etime,
                        'staffId' => $staff_assigned_id,
                        'serviceId' => $post['service_item'][$i],
                        'serviceNm' => $post['service_name'][$i],
                        'serviceSubId' => $post['service_sub_item'][$i],
                        'amount' => $post['service_amount'][$i],
                        'message' => $message,
                        'isStaffBusy' => 0,
                        'addedBy' => 0,
                        'companyId' => $post["company_id"],
                        'color' => $staffcolor,
                        'updatedBy' => 0,
                        'createdAt' => format_date(5),
                        'updatedAt' => "",
                    );
                    $model = new CartModel;
                    $model->insert($carts);
                    $cart_id = $model->getInsertID();
                
                    // Insert entry details into the entry model
                    $entries = array(
                        'uniq_id' => $post["uniq_id"],
                        'appointment_id' => $appointment_id,
                        'date' => date('Y-m-d',strtotime($post["appointment_date"])),
                        'stime' => $stime,
                        'duration' => $post['service_duration'][$i],
                        'etime' => $etime,
                        'staff_id' => $staff_assigned_id,
                        'service_group_id' => $post['service_sub_item'][$i],
                        'service_id' => $post['service_item'][$i],
                        'price' => $post['service_amount'][$i],
                        'company_id' => $post["company_id"],
                        'showbusystaff' => 0,
                        'flag' => 0,
                        'resource_id' => $staff_assigned_id,
                        'caption' => $post["service_name"][$i],
                        'is_turn' => 0,
                        'cart_id' => $cart_id,
                        'created_at' => date('Y-m-d H:i:s'),
                    );
                    $model = new EntryModel;
                    $model->insert($entries);
                }
                $model = new AppointmentModel;
                $model->update($appointment_id,array("subTotal" => $amount,"finalAmt" => $amount));
                
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Appointment booked successfully.";
                return $this->respond($response);
            }
        }

        public function get_service_price()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','service_id','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "get_service_price") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $json = [];
                $model = new ServicePriceModel;
                $price = $model->select("json")->where(array("service_id" => $post["service_id"],"company_id" => $post["company_id"]))->first();
                if($price) {
                    if($price["json"] != "" && !is_null($price["json"])) {
                        $json = json_decode($price["json"],true);   
                    } 
                }
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "";
                $response[RESPONSE_DATA] = $json;
                return $this->respond($response);
            }
        }

        public function fetch_slots()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','service_id','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "fetch_slots") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
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
                    $query = $query->where("ss.service_id", $post["service_id"])->where("company_id", $post["company_id"]);
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
                        if(date("Y-m-d") == date("Y-m-d",strtotime($post["date"]))) {
                            if(strtotime($slot_start) > strtotime(date("H:i:s"))) {
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
                        } else {
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
                }
                $final_data = array(
                    "slots" => $free_slots,
                    "staff_ids" => $available_staff_ids
                );
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "";
                $response[RESPONSE_DATA] = $final_data;
                return $this->respond($response);
            }
        }
        
        public function add_to_cart()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','customer_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "add_to_cart") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                // $is_service_available = 0;
                // $date = date("Y-m-d",strtotime($post["date"]));
                
                // $model = new StaffServiceModel;
                // $staff = $model->select("staff_id")->where("service_id",$post["service_id"])->get()->getResultArray();
                // if($staff) {
                //     $staff_ids = array_column($staff,"staff_id");
                //     $model = new StaffTimingModel;
                //     $count = $model->whereIn("staffId",$staff_ids)->where("date",$date)->get()->getNumRows();
                //     if($count > 0) {
                //         $is_service_available = 1;
                //     }
                // }
                $is_service_available = 1;
                if($is_service_available == 1) {
                    $date_15 = date("Y-m-d H:i:s",strtotime("-15 minutes"));
                    
                    $model = new WebsiteEntry;
                    $model->insert($post);
                    $count = $model->where("company_id",$post["company_id"])->where("customer_id",$post["customer_id"])->where("datetime >=",$date_15)->get()->getNumRows();   
                    
                    $status = RESPONSE_FLAG_SUCCESS;
                    $message = "Service added in your cart.";
                } else {
                    $status = RESPONSE_FLAG_FAIL;
                    $message = "Staff not available of this service on ".date('d M, Y',strtotime($post["date"]));
                    $count = 0;
                }
                $response[RESPONSE_STATUS] = $status;
                $response[RESPONSE_MESSAGE] = $message;
                $response[RESPONSE_DATA] = $count;
                return $this->respond($response);
            }
        }
        
        public function get_cart_items()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','customer_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "get_cart_items") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $currency = "$";
                $model = new CompanyModel;
                $company = $model->select("company_stime,company_etime,company_sunday_stime,company_sunday_etime,currency")->where("id",$post["company_id"])->first();
                if($company) {
                    $currency = $company["currency"];
                }
                
                $date_15 = date("Y-m-d H:i:s",strtotime("-15 minutes"));
                $model = new WebsiteEntry;
                $carts = $model->where(["company_id" => $post["company_id"],"customer_id" => $post["customer_id"],"datetime >=" => $date_15])->get()->getResultArray();
                if($post["date"] != "") {
                    $date = date("Y-m-d",strtotime($post["date"]));
                    $shortTimestamp = strtotime($date);
                    $shortDay = strtolower(date("D", $shortTimestamp));

                    $model = new WeekendDiscount;
                    $discounts = $model->select("id,sdate,edate,week_days,percentage,service_ids")->where("sdate <=",$date)->where("edate >=",$date)->where("company_id",$post["company_id"])->get()->getResultArray();
                    if($discounts) {
                        $model = new WebsiteEntry;
                        $entries = $model->where("customer_id",$post["customer_id"])->get()->getResultArray();
                        if($entries) {
                            foreach($discounts as $discount) {
                                $string = $discount["service_ids"];
                                $numbers = explode(",", $string);
                                $week_days = [];
                                if($discount["week_days"] != "") {
                                    $week_days = explode(",",$discount["week_days"]);
                                }
                                foreach($entries as $entry) {
                                    if(in_array($shortDay,$week_days)) {
                                        if(in_array($entry["service_id"], $numbers)) {
                                            $discount_amount = ($entry["amount"]*$discount["percentage"])/100;
                                            $model->update($entry["id"],["discount_amount" => $entry["amount"]-$discount_amount]);
                                        }
                                    } else {
                                        $model->update($entry["id"],["discount_amount" => 0]);
                                    }
                                }
                            }
                        }
                    }
                    $final_data = array();
                    if($carts) {
                        $company_stime = "09:00:00";
                        $company_etime = "20:00:00";
                        if($company) {
                            if(date("l",strtotime($post["date"])) == "Sunday") {
                                $company_stime = $company["company_sunday_stime"]; 
                                $company_etime = $company["company_sunday_etime"];
                            } else {
                                $company_stime = $company["company_stime"]; 
                                $company_etime = $company["company_etime"];
                            }
                        }
                    
                        $post["duration"] = 0;
                        $service_ids = [];
                        foreach($carts as $key => $val) {
                            $carts[$key]["currency"] = $currency;
                            $service_ids[] = $val["service_id"];
                            $post["duration"] += $val["duration"];
                        }
                        
                        // Check date's availability
                        $busy_slots = [];
                        $free_slots = [];
                        $available_staff_ids = "";
                        $status = 200;
                        
                        $db = db_connect();
                        $query = $db->table("staff_timings st");
                        $query = $query->select("st.staffId");
                        $query = $query->where("st.date", $date);
                        $result = $query->get()->getResultArray();
                        if ($result) {
                            $staff_ids = array_column($result, "staffId");
                            $available_staff_ids = implode(",", $staff_ids);
                            
                            // Query to get staff services
                            $query = $db->table("staff_services ss");
                            $query = $query->whereIn("ss.service_id", $service_ids)->where("company_id", $post["company_id"]);
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
                                    $query = $query->where("c.date", $date);
                                    $query = $query->where("c.isComplete", "N");
                                    $query = $query->where("c.is_cancelled", 0);
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
                        if ($status == 200) {
                            $stime = $company_stime;
                            $etime = $company_etime;
                        
                            $s_timestamp = strtotime($stime);
                            $e_timestamp = strtotime($etime);
                            $duration_in_seconds = $post["duration"] * 60;
                        
                            for ($current_timestamp = $s_timestamp; $current_timestamp < $e_timestamp; $current_timestamp += 300) {
                                $slot_start = date("H:i:s", $current_timestamp);
                                $slot_end = date("H:i:s", $current_timestamp + $duration_in_seconds);
                                if(date("Y-m-d") == date("Y-m-d",strtotime($post["date"]))) {
                                    if(strtotime($slot_start) > strtotime(date("H:i:s"))) {
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
                                } else {
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
                        }
                        $final_data = array(
                            "slots" => $free_slots,
                            "staff_ids" => $available_staff_ids
                        );
                    }
                    $data = array("carts" => $carts,"final_data" => $final_data);   
                } else {
                    foreach($carts as $key => $val) {
                        $carts[$key]["currency"] = $currency;
                    }
                    $data = array("carts" => $carts,"final_data" => []);  
                }
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "";
                $response[RESPONSE_DATA] = $data;
                return $this->respond($response);
            }
        }
        
        public function sign_in()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','username','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "sign_in") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CustomerModel;
                $customer = $model->where("(email = '".$post['username']."' OR phone = '".$post['username']."')")->where('companyId',$post['company_id'])->first();
                if($customer) {
                    if($customer["is_deleted"] == 0) {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "";
                        $response[RESPONSE_DATA] = $customer;
                        // if(md5($post["password"]) == $customer["password"]) {
                        //     $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        //     $response[RESPONSE_MESSAGE] = "";
                        //     $response[RESPONSE_DATA] = $customer;      
                        // } else {
                        //     $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                        //     $response[RESPONSE_MESSAGE] = "Password is wrong.";
                        //     $response[RESPONSE_DATA] = (object) array();          
                        // }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                        $response[RESPONSE_MESSAGE] = "Your account is deleted by Salon.";
                        $response[RESPONSE_DATA] = (object) array();      
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "Mobile no. or email not found.";
                    $response[RESPONSE_DATA] = (object) array();   
                }
                return $this->respond($response);
            }
        }
        
        public function sign_up()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','name','phone','password','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "sign_up") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CustomerModel;
                $count = 0;
                if($post["email"] != "") {
                    $count = $model->where("email",$post["email"])->where('companyId',$post['company_id'])->first();   
                }
                if($count == 0) {
                    $count = $model->where("phone",$post["phone"])->where('companyId',$post['company_id'])->first();
                    if($count == 0) {
                        $insert_data = array(
                            'name' => $post["name"],
                            'phone' => $post["phone"],
                            'email' => $post["email"],
                            'password' => md5(trim($post["password"])),
                            'is_sync_with_google' => 0,
                            'gender' => isset($post["gender"]) ? $post["gender"] : "F",
                            'companyId' => $post["company_id"],
                            "createdAt" => strtotime(date("Y-m-d"))
                        );
                        if($model->insert($insert_data)) {
                            $customer_id = $model->getInsertID();
                            $model->update($customer_id,["addedBy" => $customer_id]);
                            $customer = $model->where("id",$customer_id)->first();
                            
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "Account created successfully.";
                            $response[RESPONSE_DATA] = $customer;
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                        $response[RESPONSE_MESSAGE] = "Mobile no. already registered.";
                        $response[RESPONSE_DATA] = (object) array();
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "Email already registered.";
                    $response[RESPONSE_DATA] = (object) array();   
                }
                return $this->respond($response);
            }
        }
        
        public function customer()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','customer_id','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "customer") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CustomerModel;
                $customer = $model->where("id",$post["customer_id"])->where('companyId',$post['company_id'])->first();
                if($customer) {
                    if($customer["is_deleted"] == 0) {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "";
                        $response[RESPONSE_DATA] = $customer;
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                        $response[RESPONSE_MESSAGE] = "Your account is deleted by Salon.";
                        $response[RESPONSE_DATA] = (object) array();      
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "Account not found.";
                    $response[RESPONSE_DATA] = (object) array();   
                }
                return $this->respond($response);
            }
        }
        
        public function edit_profile()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','customer_id','company_id','name','phone','email');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "edit_profile") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CustomerModel;
                $customer = $model->where("id",$post["customer_id"])->where('companyId',$post['company_id'])->first();
                if($customer) {
                    if($customer["is_deleted"] == 0) {
                        $count = $model->where("email",$post["email"])->where('id !=',$post["customer_id"])->where('companyId',$post['company_id'])->get()->getNumRows();
                        if($count == 0) {
                            $count = $model->where("phone",$post["phone"])->where('id !=',$post["customer_id"])->where('companyId',$post['company_id'])->get()->getNumRows();
                            if($count == 0) {
                                $update_data = array(
                                    'name' => $post["name"],
                                    'phone' => $post["phone"],
                                    'email' => $post["email"],
                                    'updatedBy' => $post["customer_id"],
                                    "updatedAt" => strtotime(date("Y-m-d"))
                                );
                                $model->update($post["customer_id"],$update_data);
                                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                                $response[RESPONSE_MESSAGE] = "Personal details updated successfully.";
                                $response[RESPONSE_DATA] = (object) array();       
                            } else {
                                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                                $response[RESPONSE_MESSAGE] = "Mobile no. already used.";
                                $response[RESPONSE_DATA] = (object) array(); 
                            }
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                            $response[RESPONSE_MESSAGE] = "Email already used.";
                            $response[RESPONSE_DATA] = (object) array(); 
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                        $response[RESPONSE_MESSAGE] = "Your account is deleted by Salon.";
                        $response[RESPONSE_DATA] = (object) array();      
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "Account not found.";
                    $response[RESPONSE_DATA] = (object) array();   
                }
                return $this->respond($response);
            }
        }
        
        public function update_password()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','customer_id','company_id','old_password','new_password');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "update_password") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CustomerModel;
                $customer = $model->where("id",$post["customer_id"])->where('companyId',$post['company_id'])->first();
                if($customer) {
                    if($customer["is_deleted"] == 0) {
                        if(md5($post["old_password"]) == $customer["password"]) {
                            $model->update($post["customer_id"],["password" => md5($post["new_password"])]);
                            
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "Password changed successfully.";
                            $response[RESPONSE_DATA] = (object) array();
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                            $response[RESPONSE_MESSAGE] = "Current password is wrong.";
                            $response[RESPONSE_DATA] = (object) array(); 
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                        $response[RESPONSE_MESSAGE] = "Your account is deleted by Salon.";
                        $response[RESPONSE_DATA] = (object) array();      
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "Account not found.";
                    $response[RESPONSE_DATA] = (object) array();   
                }
                return $this->respond($response);
            }
        }
        
        public function remove_from_cart()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','customer_id','cart_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "remove_from_cart") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $date_15 = date("Y-m-d H:i:s",strtotime("-15 minutes"));
                
                $model = new WebsiteEntry;
                $model->where(["id" => $post["cart_id"],"customer_id" => $post["customer_id"],"company_id" => $post["company_id"]])->delete();
                $count = $model->where("company_id",$post["company_id"])->where("customer_id",$post["customer_id"])->where("datetime >=",$date_15)->get()->getNumRows();
                
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Service removed from your cart.";
                $response[RESPONSE_DATA] = $count;
                return $this->respond($response);
            }
        }
        
        public function my_appointments()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','customer_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "my_appointments") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new AppointmentModel;
                $appointments = $model->where(["customerId" => $post["customer_id"],"companyId" => $post["company_id"]])->orderBy("id","desc")->get()->getResultArray();
                if($appointments) {
                    $model = new CartModel;
                    foreach ($appointments as $key => $val) {
                        $carts = $model->where("appointmentId",$val["id"])->get()->getNumRows();
                        $appointments[$key]["carts"] = $carts;
                    }
                }
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = count($appointments) > 1 ? count($appointments)." appointments." : count($appointments)." appointment.";
                $response[RESPONSE_DATA] = $appointments;
                return $this->respond($response);
            }
        }
        
        public function view_appointment()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','customer_id','appointment_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "view_appointment") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CartModel;
                $carts = $model->where("appointmentId",$post["appointment_id"])->get()->getResultArray();
                
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "";
                $response[RESPONSE_DATA] = $carts;
                return $this->respond($response);
            }
        }
        
        public function submit_review()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','customer_id','comment','rate');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "submit_review") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new Review;
                $review = $model->where("company_id",$post["company_id"])->where("given_by",$post["customer_id"])->first();
                if($review) {
                    $update_data = array(
                        'star' => $post["rate"],
                        "given_by" => $post["customer_id"],
                        "comment" => $post["comment"],
                        "company_id" => $post["company_id"],
                        "is_approved" => 0,
                        "updated_at" => date("Y-m-d H:i:s")
                    );
                    $model->update($review["id"],$update_data);
                    $message = "Review edited successfully.";
                } else {
                    $insert_data = array(
                        'star' => $post["rate"],
                        "given_by" => $post["customer_id"],
                        "comment" => $post["comment"],
                        "company_id" => $post["company_id"],
                        "created_at" => date("Y-m-d H:i:s")
                    );
                    $model->insert($insert_data);
                    $message = "Review added successfully.";
                }
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = $message;
                return $this->respond($response);
            }
        }
        
        public function my_review()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','customer_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "my_review") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new Review;
                $review = $model->where("company_id",$post["company_id"])->where("given_by",$post["customer_id"])->first();
                
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "";
                $response[RESPONSE_DATA] = $review;
                return $this->respond($response);
            }
        }
        
        public function our_reviews()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "our_reviews") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $db = db_connect();
                $res = $db->table('reviews r');
                $res = $res->select("r.*,c.name");
                $res = $res->join("customers c","c.id=r.given_by");
                $res = $res->where(["company_id" => $post["company_id"],"is_approved" => 1]);
                $res = $res->where("r.deleted_at Is NULL");
                $res = $res->orderBy("r.id","desc");
                $reviews = $res->get()->getResultArray();
                
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "";
                $response[RESPONSE_DATA] = $reviews;
                return $this->respond($response);
            }
        }
        
        public function get_total_item_from_cart()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','customer_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "get_total_item_from_cart") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $date_15 = date("Y-m-d H:i:s",strtotime("-15 minutes"));
                
                $model = new WebsiteEntry;
                $count = $model->where("company_id",$post["company_id"])->where("customer_id",$post["customer_id"])->where("datetime >=",$date_15)->get()->getNumRows();
                
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "";
                $response[RESPONSE_DATA] = $count;
                return $this->respond($response);
            }
        }
        
        public function forgot_password()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','username','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "forgot_password") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CustomerModel;
                $customer = $model->select("id,name,is_deleted")->where("email",$post['username'])->where('companyId',$post['company_id'])->first();
                if($customer) {
                    if($customer["is_deleted"] == 0) {
                        $model = new CompanyModel;
                        $company = $model->where('id',$post['company_id'])->first();
                        
                        $code = md5(time());
                        $model = new CustomerModel;
                        $model->update($customer["id"],["code" => $code,"code_sentAt" => date("Y-m-d H:i:s")]);
                        
                        $post["customer_name"]  = $customer["name"];
                        $post["reset_link"]     = $company["website_url"]."reset-password?code=".$code; 
                        $post["company_name"]   = $company["company_name"];
                        $message = view("template/forgot_password",$post);
                        $mailcode = send_email($post["username"],"Reset Your Password",$message,$company);   
                        if($mailcode == 200) {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "We’ve sent reset link to your email.";
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                            $response[RESPONSE_MESSAGE] = "Sorry Email can not send.";
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                        $response[RESPONSE_MESSAGE] = "Your account is deleted by Salon.";
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "Email not found.";
                }
                return $this->respond($response);
            }
        }
        
        public function reset_password()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','code','new_password','confirm_password');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "reset_password") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CustomerModel;
                $customer = $model->select("id,name,companyId,is_deleted")->where("code",$post['code'])->first();
                if($customer) {
                    if($customer["is_deleted"] == 0) {
                        if($post["new_password"] == $post["confirm_password"]) {
                            $model->update($customer["id"],["password" => md5(trim($post["new_password"])),"code" => "","code_sentAt" => null]);
                            
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "New password has been changed successfully.";
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                            $response[RESPONSE_MESSAGE] = "New password & confirm password must be same.";
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                        $response[RESPONSE_MESSAGE] = "Your account is deleted by Salon.";
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "Email not found.";
                }
                return $this->respond($response);
            }
        }
        
        public function check_code_exist()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','code');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "check_code_exist") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $model = new CustomerModel;
                $customer = $model->where("code",$post['code'])->get()->getNumRows();
                if($customer) {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                }
                return $this->respond($response);
            }
        }
    }