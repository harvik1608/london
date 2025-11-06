<?php 
    namespace App\Controllers;

    use CodeIgniter\RESTful\ResourceController;
    use CodeIgniter\API\ResponseTrait;
    use App\Models\CompanyModel;
    use App\Models\WeekendDiscount;
    use App\Models\ServiceModel;
    use App\Models\SubServiceModel;
    use App\Models\Review;
    use App\Models\AppointmentModel;
    use App\Models\CustomerModel;
    use App\Models\OrderPaymentModel;
    use App\Models\DiscountTypeModel;
    use App\Models\PaymentTypeModel;
    use App\Models\EntryModel;
    use App\Models\CartModel;
    use App\Models\StaffTimingModel;
    use App\Models\StaffServiceModel;
    use App\Models\WebsiteEntry;

    class Api_other extends ResourceController
    {
        use ResponseTrait;
        protected $helpers = ["custom"];

        public function offers()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "offers") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY."sdsdsd";
                return $this->respond($response);
            } else {
                $offers = [];
                $model = new WeekendDiscount;
                $data['offers'] = $model->select("id,name,sdate,edate,percentage,service_group_ids,service_ids")->where('is_active','1')->where("company_id",$post["company_id"])->orderBy("id","DESC")->get()->getResultArray();
                if($data['offers']) {
                    foreach($data['offers'] as $key => $val) {
                        if(strtotime(date("Y-m-d")) <= strtotime($val['edate'])) {
                            if($val["service_group_ids"] != "") {
                                $service_group_ids = explode(",",$val['service_group_ids']);
                                $model = new ServiceModel;
                                $groups = $model->select("id,name")->whereIn("id",$service_group_ids)->get()->getResultArray();
                                $data["offers"][$key]["service_groups"] = $groups;
                            }
                            if($val["service_ids"] != "") {
                                $service_ids = explode(",",$val['service_ids']);
                                $model = new SubServiceModel;
                                $services = $model->select("id,name,service_group_id")->whereIn("id",$service_ids)->get()->getResultArray();
                                $data["offers"][$key]["sub_services"] = $services;
                                $grouped = [];
                                foreach ($services as $sub) {
                                    $grouped[$sub['service_group_id']][] = $sub;
                                }
                                $data["offers"][$key]["formatted"] = $grouped;
                            }
                            $offers = $data["offers"];
                        }
                    }
                }
                $data["offers"] = $offers;
                
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Total ".count($data['offers'])." found.";
                $response[RESPONSE_DATA] = $data;
                return $this->respond($response);
            }
        }

        public function reviews()
        {
            $post = $this->request->getVar();
            $input_parameter = ["salon_id","page"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->where("id",$post["salon_id"])->first();
                if($salon) {
                    $page = $post['page'] ?? 1;
                    $limit = $post['limit'] ?? LIMIT;
                    $offset = ($page - 1) * $limit;

                    $model = new Review;
                    $totalRecords = $model->where("deleted_at IS NULL")->countAllResults();
                    $totalPages = ceil($totalRecords/$limit);
                    $db = db_connect();
                    $review = $db->table("reviews r");
                    $review = $review->join("customers c","c.id=r.given_by");
                    $review = $review->select("r.id,r.star,r.comment,c.name as given_by,r.is_approved,r.created_at");
                    $review = $review->where("r.deleted_at IS NULL");
                    $review = $review->orderBy("r.id","desc");
                    $review = $review->limit($limit, $offset);
                    $reviews = $review->get()->getResultArray();

                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Total ".$totalRecords." review(s) found.";
                    $response[TOTAL_COUNT] = $totalRecords;
                    $response[CURRENT_PAGE] = (int) $page;
                    if((int) $page == $totalPages) {
                        $response[NEXT_PAGE] = 0;
                    } else {
                        $response[NEXT_PAGE] = (int) $page + 1;
                    }
                    $response[TOTAL_PAGE] = $totalPages;
                    $response[LIMIT_WORD] = (int) $limit;
                    $response[RESPONSE_DATA] = $reviews;
                    return $this->respond($response);
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function approve_review()
        {
            $post = $this->request->getVar();
            $input_parameter = ["salon_id","review_id","user_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->where("id",$post["salon_id"])->first();
                if($salon) {
                    $model = new Review;
                    $count = $model->where("id",$post["review_id"])->where("is_approved",0)->get()->getNumRows();
                    if($count > 0) {
                        if($model->update($post["review_id"],["is_approved" => 1,"updated_at" => date("Y-m-d H:i:s")])) {
                            $db = db_connect();
                            $review = $db->table("reviews r");
                            $review = $review->join("customers c","c.id=r.given_by");
                            $review = $review->select("r.id,r.star,r.comment,c.name as given_by,r.is_approved,r.created_at");
                            $review = $review->where("r.id",$post["review_id"]);
                            $review = $review->get()->getRowArray();

                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "Review approved successfully.";
                            $response[RESPONSE_DATA] = $review;
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Review not found.";
                        $response[RESPONSE_DATA] = (object) array();
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function appointments()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id','salon_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                // $model = db_connect();
                // $appointment = $model->table("appointments a");
                // $appointment = $appointment->join("customers c","c.id=a.customerId");
                // $appointment = $appointment->select("a.*,c.name as customer_name");
                // $appointment = $appointment->where('a.companyId',$post["salon_id"]);
                // $appointment = $appointment->where('DATE(a.bookingDate) >=',$post["sdate"]);
                // $appointment = $appointment->where('DATE(a.bookingDate) <=',$post["edate"]);
                // if(isset($post["customer_id"]) && $post["customer_id"] != "") {
                //     $appointment = $appointment->where('a.customerId',$post['customer_id']);
                // }
                // if(isset($post["is_checkout"]) && $post["is_checkout"] != "") {
                //     $appointment = $appointment->where('a.status',2);
                // }
                // if(isset($post["is_walkin"]) && $post["is_walkin"] != "") {
                //     $appointment = $appointment->where('a.type',"N");
                // }
                // $appointment = $appointment->orderBy("a.id","DESC");
                // $appointments = $appointment->get()->getResultArray();
                // if($appointments) {
                //     foreach ($appointments as $key => $val) {
                //         $start_time = "";
                //         $duration = 0;
                //         $services = array();
                //         $staffs = array();
                //         $cart = $model->table("carts c");
                //         $cart = $cart->join("staffs s","c.staffId=s.id");
                //         $cart = $cart->select("c.*,CONCAT(s.fname,' ',s.lname) as staff_name");
                //         $cart = $cart->where("c.appointmentId",$val['id']);
                //         $carts = $cart->get()->getResultArray();
                //         if($carts) {
                //             $no = 0;
                //             foreach($carts as $cart) {
                //                 $no++;
                //                 if($no == 1) {
                //                     $start_time = $cart["stime"];
                //                 }
                //                 $duration += $cart["duration"];
                //                 $services[] = strip_tags($cart["serviceNm"]);
                //                 $staffs[] = strip_tags($cart["staff_name"]);
                //             }
                //             $appointments[$key]["items"] = $carts;
                //         } else {
                //             $appointments[$key]["items"] = [];
                //         }
                //         if($start_time != "" && $duration != 0) {
                //             $stime = $start_time;
                //             $etime = date("H:i:s",strtotime($stime."+".$duration." minutes"));
                //             $appointments[$key]["start_time"] = date("Y-m-d",strtotime($val['bookingDate']))."T".date("H:i",strtotime($stime));
                //             $appointments[$key]["end_time"] = date("Y-m-d",strtotime($val['bookingDate']))."T".date("H:i",strtotime($etime));   
                //         } else {
                //             $appointments[$key]["start_time"] = "";
                //             $appointments[$key]["end_time"] = "";   
                //         }
                //         if(empty($services)) {
                //             $appointments[$key]["services"] = "";
                //         } else {
                //             $appointments[$key]["services"] = implode(",",$services);
                //         }
                //         if(empty($staffs)) {
                //             $appointments[$key]["staffs"] = "";
                //         } else {
                //             $staffs = array_unique($staffs);
                //             $appointments[$key]["staffs"] = implode(",",$staffs);
                //         }
                //         $appointments[$key]["salon_note"] = is_null($val['salon_note']) ? "" : $val['salon_note'];
                //     }
                // }
                $model = db_connect();
                $appointment = $model->table("carts c");
                $appointment = $appointment->join("appointments a","a.id=c.appointmentId");
                $appointment = $appointment->join("customers c1","c1.id=a.customerId");
                $appointment = $appointment->join("staffs s","s.id=c.staffId");
                $appointment = $appointment->select("a.id,a.customerId,a.subTotal,a.finalAmt,a.bookingDate,a.status,a.type,a.flag,a.companyId,a.addedBy,c.id AS itemId,c.appointmentId,c.date,c.stime,c.etime,c.duration,c.staffId,c.serviceId,c.serviceNm,c.caption,c.serviceSubId,c.amount,c.message,c.isComplete,c.color,CONCAT(s.fname,' ',s.lname) AS staff_name,c1.name as customerName");
                $appointment = $appointment->where('a.companyId',$post["salon_id"]);
                $appointment = $appointment->where('DATE(a.bookingDate) >=',$post["sdate"]);
                $appointment = $appointment->where('DATE(a.bookingDate) <=',$post["edate"]);
                $appointments = $appointment->get()->getResultArray();
                if($appointments) {
                    foreach($appointments as $key => $val) {
                        $appointments[$key]["caption"] = is_null($val["caption"]) ? "" : $val["caption"];
                        $appointments[$key]["serviceNm"] = strip_tags($val["serviceNm"]);
                        $appointments[$key]["format_stime"] = date("Y-m-d",strtotime($val['bookingDate']))."T".date("H:i",strtotime($val["stime"]));
                        $appointments[$key]["format_etime"] = date("Y-m-d",strtotime($val['bookingDate']))."T".date("H:i",strtotime($val["etime"]));
                    }
                }
                
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Total ".count($appointments)." found.";
                $response[RESPONSE_DATA] = $appointments;
                return $this->respond($response);
            }
        }

        public function view_appointment()
        {
            $post = $this->request->getVar();
            $input_parameter = array('appointment_id','user_id','salon_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->select("timezone")->where("id",$post["salon_id"])->first();
                if($salon) {
                    $model = new AppointmentModel;
                    $appointment = $model->select("id,customerId,bookingDate,subTotal,extra_discount,extra_discount,discountAmt,finalAmt,addedDate,bookedFrom,status")->where("id",$post["appointment_id"])->first();
                    if($appointment) {
                        $appointment["bookedBy"] = "Super Admin";
                        $appointment["total_discount"] = (string) ($appointment["extra_discount"]+$appointment["discountAmt"]);
                        $appointment["final_amount"] = (string) ($appointment["finalAmt"]-$appointment["total_discount"]);
                        date_default_timezone_set($salon["timezone"]);
                        $appointment['addedDate'] = timezone("UTC",$salon["timezone"],$appointment['addedDate']);

                        $model = new CustomerModel;
                        $customer = $model->select("id,name,phone,email,note")->where("id",$appointment["customerId"])->first();
                        $appointment['customer'] = $customer; 

                        $db = db_connect();
                        $master = $db->table('carts cr');
                        $master->select("cr.stime,cr.duration,cr.amount,cr.serviceId,cr.serviceSubId,cr.staffId,cr.isStaffBusy,ss.name AS service_name,ss.json,CONCAT_WS(' ',s.fname,s.lname) AS staff_name,cr.color,cr.serviceNm,cr.caption");
                        $master->join("services ss","cr.serviceSubId=ss.id","left");
                        $master->join("staffs s","cr.staffId=s.id","left");
                        $master->where('cr.appointmentId',$post["appointment_id"]);
                        $master->orderBy("cr.id","asc");
                        $carts = $master->get()->getResultArray();
                        $appointment['item'] = $carts;
                        $appointment["bookingTime"] = isset($carts[0]["stime"]) ? $carts[0]["stime"] : "00:00:00";

                        $model = $db->table("entries e");
                        $entry = $model->join("services s","e.service_id=s.id","left");
                        $entry = $model->select("e.*,s.name AS service_name");
                        $entry = $model->where("appointment_id",$post["appointment_id"]);
                        $entry = $model->where("is_removed_from_cart",0);
                        $entry = $model->orderBy("e.id","asc");
                        $entries = $entry->get()->getResultArray();
                        if($entries) {
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
                                    $staff->groupBy("s.id");
                                    $staffs = $staff->get()->getResultArray();
                                } else {
                                    $staff = $db->table('staffs s');
                                    $staff->select("s.id,CONCAT_WS(' ',s.fname,s.lname) AS name,st.stime,st.etime,s.color");
                                    $staff->join("staff_timings st","s.id=st.staffId");
                                    $staff->where('st.date',$adate);
                                    $staffs = $staff->get()->getResultArray();
                                }
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
                                $entries[$key]["staffs"] = $staffs;
                            }
                            $appointment["carts"] = $entries;
                        } else {
                            $appointment["carts"] = [];
                        }

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Appointment found.";
                        $response[RESPONSE_DATA] = $appointment;
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Appointment not found.";
                        $response[RESPONSE_DATA] = (object) array();
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function checkout_appointment()
        {
            $post = $this->request->getVar();
            $input_parameter = array('salon_id','user_id','appointment_id','payment_types','total_amount');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->select("timezone")->where("id",$post["salon_id"])->first();
                if($salon) {
                    $model = new AppointmentModel;
                    $appointment = $model->select("*")->where("id",$post["appointment_id"])->where("status",1)->first();
                    if($appointment) {
                        $total_amount = 0;
                        $payments = json_decode($post["payment_types"],true);
                        if($payments) {
                            foreach($payments as $payment) {
                                $total_amount += $payment["amount"];
                            }
                        }
                        if(($total_amount+$post["extra_discount"]+$post["discount_amount"]) == $post["total_amount"]) {
                            $param['status'] = 2;
                            $param['discountAmt'] = $post['discount_amount'];
                            $param['discountId'] = $post['discount_type_id'];
                            $param['salon_note'] = $post['salon_note'];
                            $param['updatedBy'] = $post["user_id"];
                            $param['updatedAt'] = date("Y-m-d H:i:s");
                            $param["extra_discount"] = $post["extra_discount"];
                            $model = new AppointmentModel;
                            if($model->update($post["appointment_id"],$param)) {
                                $db = db_connect();
                                $cart = $db->table('carts');
                                $cart->where("appointmentId",$post["appointment_id"])->update(array("isComplete" => "Y","is_cancelled" => 2));

                                for($i = 0; $i < count($payments); $i ++) {
                                    $payment_arr[] = array(
                                        'appointmentId' => $post["appointment_id"],
                                        'paymentMethod' => $payments[$i]['id'],
                                        'paymentAmount' => $payments[$i]['amount'],
                                        'companyId' => $post["salon_id"],
                                        'addedBy' => $post["user_id"],
                                        'updatedBy' => 0,
                                        'createdAt' => date("Y-m-d H:i:s"),
                                        'updatedAt' => "",       
                                    );
                                }
                                $model = new OrderPaymentModel;
                                $model->insertBatch($payment_arr);
                            }
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "Appointment checkout successfully.";
                            $response[RESPONSE_DATA] = (object) array();
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Remaining amount must be 0.";
                            $response[RESPONSE_DATA] = (object) array();
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Appointment not found.";
                        $response[RESPONSE_DATA] = (object) array();
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function all_discount_types()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id','salon_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->select("timezone")->where("id",$post["salon_id"])->first();
                if($salon) {
                    $model = new DiscountTypeModel;
                    $discount_types = $model->select("id,name,discount_type,discount_value")->where("company_id",$post["salon_id"])->where("is_active",'1')->where("is_deleted",0)->get()->getResultArray();

                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Total ".count($discount_types)." discount type(s) found.";
                    $response[RESPONSE_DATA] = $discount_types;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function all_payment_types()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id','salon_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->select("timezone")->where("id",$post["salon_id"])->first();
                if($salon) {
                    $model = new PaymentTypeModel;
                    $discount_types = $model->select("id,name")->where("company_id",$post["salon_id"])->where("is_active",'1')->where("is_deleted",0)->orderBy("position","asc")->get()->getResultArray();

                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Total ".count($discount_types)." payment type(s) found.";
                    $response[RESPONSE_DATA] = $discount_types;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function add_appointment()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id','salon_id','appointment_date','appointment_time','customer_phone','customer_name','bookedFrom','service_item','service_amount','service_name','sub_service_name','selected_staff_name','service_stime','service_duration','service_etime','service_staff','service_sub_item','is_busy_staff','selected_staff_color','service_nm');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $post["service_item"] = json_decode($post["service_item"],true);
                $post["service_amount"] = json_decode($post["service_amount"],true);
                $post["service_name"] = json_decode($post["service_name"],true);
                $post["sub_service_name"] = json_decode($post["sub_service_name"],true);
                $post["selected_staff_name"] = json_decode($post["selected_staff_name"],true);
                $post["service_stime"] = json_decode($post["service_stime"],true);
                $post["service_duration"] = json_decode($post["service_duration"],true);
                $post["service_etime"] = json_decode($post["service_etime"],true);
                $post["service_staff"] = json_decode($post["service_staff"],true);
                $post["service_sub_item"] = json_decode($post["service_sub_item"],true);
                $post["is_busy_staff"] = json_decode($post["is_busy_staff"],true);
                $post["selected_staff_color"] = json_decode($post["selected_staff_color"],true);
                $post["service_nm"] = json_decode($post["service_nm"],true);
                
                $post["uniq_id"] = md5(time());
                $appointment_date = format_text(3,$post['appointment_date']);

                $model = new CompanyModel;
                $salon = $model->select("timezone")->where("id",$post["salon_id"])->first();
                if($salon) {
                    $phone = format_text(4,$post['customer_phone']);
                    $name = $post['customer_name'];

                    $model = new CustomerModel;
                    $customer = $model->select('id')->where('phone',$phone)->where("companyId",$post["salon_id"])->first();
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
                        $cparam['is_sync_with_google'] = 0;
                        $cparam['addedBy'] = $post['user_id'];
                        $cparam['companyId'] = $post["salon_id"];
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
                    $params['addedBy']      = $post['user_id'];
                    $params['companyId']    = $post["salon_id"];
                    $params['updatedBy']    = 0;
                    $params['createdAt']    = format_date(5);
                    $params['updatedAt']    = "";
                    $model = new AppointmentModel;
                    $model->insert($params);
                    $appointment_id = $model->getInsertID();

                    $amount = 0;
                    for($i = 0; $i < count($post['service_item']); $i ++)
                    {
                        $amount = $amount + $post['service_amount'][$i];
                        if($post['sub_service_name'] != "")
                            $message = $phone." - ".$name."\n".$post['service_name'][$i]."-\n".$post['sub_service_name'][$i];
                        else 
                            $message = $phone." - ".$name."\n".$post['service_name'];
    
                        $message = $message."\n".$post['selected_staff_name'][$i];
                        $carts = array(
                            'uniq_id' => $post["uniq_id"],
                            'appointmentId' => $appointment_id,
                            'date' => format_date(6,$appointment_date),
                            'stime' => $post['service_stime'][$i],
                            'duration' => $post['service_duration'][$i],
                            'etime' => $post['service_etime'][$i],
                            'staffId' => $post['service_staff'][$i] == "" ? $post['service_busy_staff'][$i] : $post['service_staff'][$i],
                            'serviceId' => $post['service_item'][$i],
                            'serviceNm' => $post['service_nm'][$i],
                            'serviceSubId' => $post['service_sub_item'][$i],
                            'amount' => $post['service_amount'][$i],
                            'message' => $message,
                            'isStaffBusy' => $post["is_busy_staff"][$i],
                            'addedBy' => $post["user_id"],
                            'companyId' => $post["salon_id"],
                            'color' => $post['selected_staff_color'][$i],
                            'updatedBy' => 0,
                            'createdAt' => format_date(5),
                            'updatedAt' => "",
                        );
                        $model = new CartModel;
                        $model->insert($carts);
                        $cart_id = $model->getInsertID();

                        $insert_data = array(
                            "uniq_id" => $post["uniq_id"],
                            "appointment_id" => $appointment_id,
                            "date" => format_date(6,$appointment_date),
                            "stime" => $post['service_stime'][$i], 
                            "duration" => $post['service_duration'][$i], 
                            "etime" => $post['service_etime'][$i],
                            "staff_id" => $post['service_staff'][$i] == "" ? $post['service_busy_staff'][$i] : $post['service_staff'][$i],
                            "service_id" => $post["service_sub_item"][$i],
                            "service_group_id" => $post["service_item"][$i],                            
                            "price" => $post['service_amount'][$i],
                            "company_id" => $post["salon_id"],
                            "showbusystaff" => 0,
                            "flag" => 0,
                            "resource_id" => 0,
                            "caption" => $post['sub_service_name'][$i],
                            "cart_id" => $cart_id,
                            "created_at" => date("Y-m-d H:i:s")
                        );
                        $model = new EntryModel;
                        $model->insert($insert_data);
                    }               
                    $model = new AppointmentModel;
                    $model->update($appointment_id,array("subTotal" => $amount,"finalAmt" => $amount));

                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Appointment added successfully.";
                    $response[RESPONSE_DATA] = (object) array();
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function edit_appointment()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id','salon_id','appointment_date','appointment_time','customer_phone','customer_name','bookedFrom','service_item','service_amount','service_name','sub_service_name','selected_staff_name','service_stime','service_duration','service_etime','service_staff','service_sub_item','is_busy_staff','selected_staff_color','service_nm','appointment_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->select("timezone")->where("id",$post["salon_id"])->first();
                if($salon) {
                    $model = new AppointmentModel;
                    $appointment = $model->select("uniq_id")->where("id",$post["appointment_id"])->first();
                    if($appointment) {
                        $post["uniq_id"] = $appointment["uniq_id"];
                        $post["service_item"] = json_decode($post["service_item"],true);
                        $post["service_amount"] = json_decode($post["service_amount"],true);
                        $post["service_name"] = json_decode($post["service_name"],true);
                        $post["sub_service_name"] = json_decode($post["sub_service_name"],true);
                        $post["selected_staff_name"] = json_decode($post["selected_staff_name"],true);
                        $post["service_stime"] = json_decode($post["service_stime"],true);
                        $post["service_duration"] = json_decode($post["service_duration"],true);
                        $post["service_etime"] = json_decode($post["service_etime"],true);
                        $post["service_staff"] = json_decode($post["service_staff"],true);
                        $post["service_sub_item"] = json_decode($post["service_sub_item"],true);
                        $post["is_busy_staff"] = json_decode($post["is_busy_staff"],true);
                        $post["selected_staff_color"] = json_decode($post["selected_staff_color"],true);
                        $post["service_nm"] = json_decode($post["service_nm"],true);
                        $appointment_date = format_text(3,$post['appointment_date']);
                        $phone = format_text(4,$post['customer_phone']);
                        $name = $post['customer_name'];

                        $model = new CartModel;
                        $model->where(array("uniq_id" => $post["uniq_id"],"appointmentId" => $post["appointment_id"]))->delete();

                        $model = new EntryModel;
                        $model->where(array("uniq_id" => $post["uniq_id"],"appointment_id" => $post["appointment_id"]))->delete();

                        $model = new CustomerModel;
                        $customer = $model->select('id')->where('phone',$phone)->where("companyId",$post["salon_id"])->first();
                        if($customer) {
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
                            $cparam['is_sync_with_google'] = 0;
                            $cparam['addedBy'] = $post['user_id'];
                            $cparam['companyId'] = $post["salon_id"];
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
                        $params['companyId']    = $post["salon_id"];
                        $params['updatedBy']    = $post["user_id"];
                        $params['updatedAt']    = format_date(5);
                        $model = new AppointmentModel;
                        $model->update($post["appointment_id"],$params);
                        $appointment_id = $post["appointment_id"];

                        $amount = 0;
                        for($i = 0; $i < count($post['service_item']); $i ++)
                        {
                            $amount = $amount + $post['service_amount'][$i];
                            if($post['sub_service_name'] != "")
                                $message = $phone." - ".$name."\n".$post['service_name'][$i]."-\n".$post['sub_service_name'][$i];
                            else 
                                $message = $phone." - ".$name."\n".$post['service_name'];
        
                            $message = $message."\n".$post['selected_staff_name'][$i];
                            $carts = array(
                                'uniq_id' => $post["uniq_id"],
                                'appointmentId' => $appointment_id,
                                'date' => format_date(6,$appointment_date),
                                'stime' => $post['service_stime'][$i],
                                'duration' => $post['service_duration'][$i],
                                'etime' => $post['service_etime'][$i],
                                'staffId' => $post['service_staff'][$i] == "" ? $post['service_busy_staff'][$i] : $post['service_staff'][$i],
                                'serviceId' => $post['service_item'][$i],
                                'serviceNm' => $post['service_nm'][$i],
                                'serviceSubId' => $post['service_sub_item'][$i],
                                'amount' => $post['service_amount'][$i],
                                'message' => $message,
                                'isStaffBusy' => $post["is_busy_staff"][$i],
                                'addedBy' => $post["user_id"],
                                'companyId' => $post["salon_id"],
                                'color' => $post['selected_staff_color'][$i],
                                'updatedBy' => 0,
                                'createdAt' => format_date(5),
                                'updatedAt' => "",
                            );
                            $model = new CartModel;
                            $model->insert($carts);
                            $cart_id = $model->getInsertID();

                            $insert_data = array(
                                "uniq_id" => $post["uniq_id"],
                                "appointment_id" => $appointment_id,
                                "date" => format_date(6,$appointment_date),
                                "stime" => $post['service_stime'][$i], 
                                "duration" => $post['service_duration'][$i], 
                                "etime" => $post['service_etime'][$i],
                                "staff_id" => $post['service_staff'][$i] == "" ? $post['service_busy_staff'][$i] : $post['service_staff'][$i],
                                "service_id" => $post["service_sub_item"][$i],
                                "service_group_id" => $post["service_item"][$i],                            
                                "price" => $post['service_amount'][$i],
                                "company_id" => $post["salon_id"],
                                "showbusystaff" => 0,
                                "flag" => 0,
                                "resource_id" => 0,
                                "caption" => $post['sub_service_name'][$i],
                                "cart_id" => $cart_id,
                                "created_at" => date("Y-m-d H:i:s")
                            );
                            $model = new EntryModel;
                            $model->insert($insert_data);
                        }               
                        $model = new AppointmentModel;
                        $model->update($appointment_id,array("subTotal" => $amount,"finalAmt" => $amount));

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Appointment edited successfully.";
                        $response[RESPONSE_DATA] = (object) array();
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Appointment not found.";
                        $response[RESPONSE_DATA] = array();
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function add_walkin_appointment()
        {
            $post = $this->request->getVar();
            $input_parameter = array('salon_id','user_id','payment_types','total_amount','customer_phone','customer_name','walkin_date','walkin_time');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->select("timezone")->where("id",$post["salon_id"])->first();
                if($salon) {
                    $post["walkin_uniq_id"] = md5(time());

                    $post["service_item"] = json_decode($post["service_item"],true);
                    $post["service_amount"] = json_decode($post["service_amount"],true);
                    $post["service_name"] = json_decode($post["service_name"],true);
                    $post["sub_service_name"] = json_decode($post["sub_service_name"],true);
                    $post["selected_staff_name"] = json_decode($post["selected_staff_name"],true);
                    $post["service_stime"] = json_decode($post["service_stime"],true);
                    $post["service_duration"] = json_decode($post["service_duration"],true);
                    $post["service_etime"] = json_decode($post["service_etime"],true);
                    $post["service_staff"] = json_decode($post["service_staff"],true);
                    $post["service_sub_item"] = json_decode($post["service_sub_item"],true);
                    $post["is_busy_staff"] = json_decode($post["is_busy_staff"],true);
                    $post["selected_staff_color"] = json_decode($post["selected_staff_color"],true);
                    $post["service_nm"] = json_decode($post["service_nm"],true);

                    $phone = $post['customer_phone'];
                    $name  = format_text(1,$post['customer_name']);
                    $appointment_date = $post['walkin_date'];

                    $model = new CustomerModel;
                    $customer = $model->select('id')->where('phone',$phone)->first();
                    if($customer)
                    {
                        $customer_id = $customer['id'];
                        $customer_params['name'] = $name;
                        $customer_params['email']= isset($post['customer_email']) ? $post['customer_email'] : "";
                        $model = new CustomerModel;
                        $model->update($customer_id,$customer_params);
                    } else {
                        $customer_params['name'] = $name;
                        $customer_params['phone'] = $phone;
                        $customer_params['email'] = $post['customer_email'];
                        $customer_params['marketing_email'] = "N";
                        $customer_params['note'] = "";
                        $customer_params['is_sync_with_google'] = 0;
                        $customer_params['companyId'] = $post["salon_id"];
                        $customer_params['addedBy'] = $post["user_id"];
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
                    $params['extra_discount'] = $post["extra_discount"];
                    $params['discountAmt']  = 0;
                    $params['finalAmt']     = 0;
                    $params['bookingDate']  = format_date(6,$appointment_date);
                    $params['status']       = 2;
                    $params['bookedFrom']   = 3;
                    $params['note']         = isset($post['customer_note']) ? $post['customer_note'] : "";
                    $params['type']         = "N";
                    $params['flag']         = "Y";
                    $params['addedDate']    = format_date(1);
                    $params['companyId']    = $post["salon_id"];
                    $params['addedBy']      = $post["user_id"];
                    $params['updatedBy']    = 0;
                    $params['createdAt']    = format_date(5);
                    $params['updatedAt']    = "";
                    $model = new AppointmentModel;
                    $model->insert($params);
                    $appointment_id = $model->getInsertID();
                    if($appointment_id > 0)
                    {
                        $amount = 0;
                        for($i = 0; $i < count($post['service_item']); $i ++)
                        {
                            $amount = $amount + $post['service_amount'][$i];
                            if($post['sub_service_name'] != "")
                                $message = "WALKIN \n-".$phone." - ".$name."\n".$post['service_name'][$i]."-\n".$post['sub_service_name'][$i];
                            else 
                                $message = "WALKIN \n-".$phone." - ".$name."\n".$post['service_name'];
        
                            $message = $message."\n".$post['selected_staff_name'][$i];
                            $carts = array(
                                'uniq_id' => $post["walkin_uniq_id"],
                                'appointmentId' => $appointment_id,
                                'date' => format_date(6,$appointment_date),
                                'stime' => $post['service_stime'][$i],
                                'duration' => $post['service_duration'][$i],
                                'etime' => $post['service_etime'][$i],
                                'staffId' => $post['service_staff'][$i] == "" ? $post['service_busy_staff'][$i] : $post['service_staff'][$i],
                                'serviceId' => $post['service_item'][$i],
                                'serviceNm' => $post['service_nm'][$i],
                                'serviceSubId' => $post['service_sub_item'][$i],
                                'amount' => $post['service_amount'][$i],
                                'message' => $message,
                                'isStaffBusy' => $post["is_busy_staff"][$i],
                                'addedBy' => $post["user_id"],
                                'companyId' => $post["salon_id"],
                                'color' => $post['selected_staff_color'][$i],
                                'updatedBy' => 0,
                                'createdAt' => format_date(5),
                                'updatedAt' => "",
                            );
                            $model = new CartModel;
                            $model->insert($carts);
                            $cart_id = $model->getInsertID();

                            $insert_data = array(
                                "uniq_id" => $post["walkin_uniq_id"],
                                "appointment_id" => $appointment_id,
                                "date" => format_date(6,$appointment_date),
                                "stime" => $post['service_stime'][$i], 
                                "duration" => $post['service_duration'][$i], 
                                "etime" => $post['service_etime'][$i],
                                "staff_id" => $post['service_staff'][$i] == "" ? $post['service_busy_staff'][$i] : $post['service_staff'][$i],
                                "service_id" => $post["service_sub_item"][$i],
                                "service_group_id" => $post["service_item"][$i],                            
                                "price" => $post['service_amount'][$i],
                                "company_id" => $post["salon_id"],
                                "showbusystaff" => 0,
                                "flag" => 0,
                                "resource_id" => 0,
                                "caption" => $post['sub_service_name'][$i],
                                "cart_id" => $cart_id,
                                "created_at" => date("Y-m-d H:i:s")
                            );
                            $model = new EntryModel;
                            $model->insert($insert_data);
                        }
                        $model = new AppointmentModel;
                        $model->update($appointment_id,array("subTotal" => $amount,"discount_amount" => $post['discount_amount'],"extra_discount" => $post["extra_discount"],"finalAmt" => $amount - $post['discount_amount']));
                        $payments = json_decode($post["payment_types"],true);
                        for($i = 0; $i < count($payments); $i ++) {
                            $payment_arr[] = array(
                                'appointmentId' => $appointment_id,
                                'paymentMethod' => $payments[$i]['id'],
                                'paymentAmount' => $payments[$i]['amount'],
                                'companyId' => $post["salon_id"],
                                'addedBy' => $post["user_id"],
                                'updatedBy' => 0,
                                'createdAt' => date("Y-m-d H:i:s"),
                                'updatedAt' => "",       
                            );
                        }
                        $model = new OrderPaymentModel;
                        $model->insertBatch($payment_arr);
                    }
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Walkin appointment added successfully.";
                    $response[RESPONSE_DATA] = (object) array();
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function present_staffs()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id','date');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $db = db_connect();
                $staff = $db->table('staff_timings st');
                $staff->join("staffs s","s.id=st.staffId");
                $staff->select("st.id,s.fname,s.lname,s.color,st.staffId,st.date,st.stime,st.etime");
                $staff->where("st.date",$post["date"]);
                $present_staffs = $staff->get()->getResultArray();
                // if($present_staffs) {
                //     $model = new StaffServiceModel;
                //     foreach($present_staffs as $key => $val)  {
                //         $services = $model->select("id,service_id")->where("staff_id",$val["staffId"])->get()->getResultArray();
                //         $present_staffs[$key]["services"] = $services;
                //     }
                // }

                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Total ".count($present_staffs)." staff(s) available.";
                $response[RESPONSE_DATA] = $present_staffs;
                return $this->respond($response);
            }
        }

        public function daily_report()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id','salon_id','sdate','edate');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->select("timezone")->where("id",$post["salon_id"])->first();
                if($salon) {
                    $total_price = $total_discount_amt = $total_final_amt = 0;

                    $where = 'DATE(a.bookingDate) >= "'.$post['sdate'].'" AND DATE(a.bookingDate) <= "'.$post['edate'].'" AND status = 2';
                    $db = db_connect();
                    $appointment = $db->table('appointments a');
                    $appointment->select("a.id,c.name AS customer_name,c.phone AS customer_phone,a.type,a.bookingDate,a.subTotal,a.discountAmt,a.finalAmt,a.extra_discount");
                    $appointment->join("customers c","c.id=a.customerId","left");
                    $appointment->where("a.companyId",$post["salon_id"]);
                    $appointment->where($where);
                    $appointment->orderBy("a.id","desc");
                    $appointments = $appointment->get()->getResultArray();
                    if($appointments) {
                        $db = db_connect();
                        foreach($appointments as $key => $val) {
                            $model = new OrderPaymentModel;
                            $payments = $model->select("id,paymentAmount,paymentMethod")->where("appointmentId",$val["id"])->get()->getResultArray();
                            if(!empty($payments)) {
                                $appointments[$key]["payments"] = $payments;
                            } else {
                                $appointments[$key]["payments"] = [];
                            }

                            $cart = $db->table('carts c');
                            $cart->select("c.id,c.amount,c.serviceSubId,s.name AS service_name,s1.fname,s1.lname,c.stime,c.etime");
                            $cart->join("services s","s.id=c.serviceSubId","left");
                            $cart->join("staffs s1","s1.id=c.staffId","left");
                            $cart->where("c.appointmentId",$val["id"]);
                            $cart->orderBy("c.id","asc");
                            $carts = $cart->get()->getResultArray();
                            if(!empty($carts)) {
                                $appointments[$key]["carts"] = $carts;
                            } else {
                                $appointments[$key]["carts"] = [];
                            }


                            $total_discount = number_format($val["discountAmt"]+$val["extra_discount"],2);
                            $final_discount = number_format($val["subTotal"]-($val['discountAmt']+$val['extra_discount']),2);
                            $appointments[$key]["bookingDate"] = date("d-m-Y",strtotime($val["bookingDate"]));
                            $appointments[$key]["total_discount"] = (string) ($total_discount);
                            $appointments[$key]["final_discount"] = (string) ($final_discount);
                            $total_price += $val['subTotal'];
                            $total_discount_amt += $val['discountAmt']+$val['extra_discount'];
                            $total_final_amt += $val['subTotal']-($val['discountAmt']+$val['extra_discount']);
                            unset($appointments[$key]["finalAmt"]);
                            unset($appointments[$key]["discountAmt"]);
                            unset($appointments[$key]["extra_discount"]);
                        }
                    }
                    $payment_summary = [];
                    $model = new PaymentTypeModel;
                    $payment_methods = $model->select("id,name")->where(["is_deleted" => 0,"company_id" => $post["salon_id"]])->get()->getResultArray();
                    $total_amt = 0;
                    if(!empty($payment_methods) && !empty($appointments)) {
                        foreach($payment_methods as $payment_method) {
                            $total = 0;
                            foreach($appointments as $appointment) {
                                if(isset($appointment["payments"]) && !empty($appointment["payments"])) {
                                    foreach($appointment["payments"] as $payment) {
                                        if($payment["paymentMethod"] == $payment_method["id"]) {
                                            $total = $total + $payment["paymentAmount"];
                                        }
                                    }
                                }
                            }
                            $total_amt = $total_amt + $total;
                            $payment_summary[] = array("id" => $payment_method["id"],"name" => $payment_method["name"],"amount" => number_format($total,2));
                        }
                    }

                    $fill_service_groups = array();
                    $model = new ServiceModel;
                    $service_groups = $model->select("id,name")->where("is_active",'1')->get()->getResultArray();
                    if(!empty($service_groups)) {
                        $model = db_connect();
                        foreach($service_groups as $key => $val) {
                            $services = $model->table("services s");
                            $services = $services->select("s.id,s.name,SUM(c.amount) AS amount");
                            $services = $services->join("carts c","c.serviceSubId=s.id","left");
                            $services = $services->join("appointments a","a.id=c.appointmentId","left");
                            $services = $services->where("s.service_group_id = '".$val['id']."' AND a.status = 2 AND a.bookingDate >= '".$post['sdate']."' AND a.bookingDate <= '".$post['edate']."'");
                            $services = $services->groupBy("s.id");
                            $services = $services->get()->getResultArray();    
                            if(!empty($services)) {
                                $service_groups[$key]["item"] = $services;
                            } else {
                                $service_groups[$key]["item"] = [];
                            }
                        }
                    } else {
                        foreach($service_groups as $key => $val) {
                            $service_groups[$key]["item"] = [];
                        }
                    }
                    foreach($service_groups as $key => $val) {
                        if(!empty($val["item"])) {
                            array_push($fill_service_groups, $val);
                        }
                    }
                    $data = array("appointments" => $appointments,"total_amount" => ['total_amount' => (string) $total_price,"total_discount" => (string) $total_discount_amt,"total_net_amount" => (string) $total_final_amt],"payment_type" => $payment_summary,"services" => $fill_service_groups);
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "";
                    $response[RESPONSE_DATA] = $data;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function add_cart()
        {
            $post = $this->request->getVar();
            $input_parameter = array('uniq_id','appointment_id','start_time','appointment_date','service_group_id','service_id','service_name','amount','duration','salon_id','user_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->select("timezone")->where("id",$post["salon_id"])->first();
                if($salon) {
                    $model = new EntryModel;
                    $entries = $model->where("uniq_id",$post["uniq_id"])
                    ->where("company_id",$post["salon_id"])
                    ->where('is_removed_from_cart',0)
                    ->orderBy('id','desc')
                    ->limit(1)
                    ->get()
                    ->getResultArray();
                    if(empty($entries)) {
                        $etime = date("H:i:s",strtotime("+".$post["duration"]." minutes",strtotime($post["start_time"])));
                        $insert_data = array(
                            "service_group_id" => $post["service_group_id"],
                            "uniq_id" => $post["uniq_id"],
                            "appointment_id" => $post["appointment_id"],
                            "service_id" => $post["service_id"],
                            "date" => date("Y-m-d",strtotime($post["appointment_date"])), 
                            "stime" => $post["start_time"], 
                            "duration" => $post["duration"], 
                            "etime" => $etime,
                            "price" => $post["amount"],
                            "showbusystaff" => 0,
                            "flag" => 0,
                            "resource_id" => 0,
                            "company_id" => $post["salon_id"],
                            "caption" => $post["caption"],
                            "created_at" => date("Y-m-d H:i:s")
                        );
                        $model->insert($insert_data);
                    } else {
                        foreach($entries as $entry) {
                            $stime = $entry["etime"];
                            $etime = date("H:i:s",strtotime("+".$post["duration"]." minutes",strtotime($stime)));
                            $insert_data = array(
                                "service_group_id" => $post["service_group_id"],
                                "uniq_id" => $post["uniq_id"],
                                "appointment_id" => $post["appointment_id"],
                                "service_id" => $post["service_id"],
                                "date" => date("Y-m-d",strtotime($post["appointment_date"])), 
                                "stime" => $stime, 
                                "duration" => $post["duration"], 
                                "etime" => $etime,
                                "price" => $post["amount"],
                                "showbusystaff" => 0,
                                "flag" => 0,
                                "resource_id" => 0,
                                "company_id" => $post["salon_id"],
                                "caption" => $post["caption"],
                                "created_at" => date("Y-m-d H:i:s")
                            );
                            $model->insert($insert_data);
                        }
                    }
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Service added successfully.";
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                }
                return $this->respond($response);
            }
        }

        public function view_cart()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id','uniq_id','salon_id','appointment_date');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->select("timezone")->where("id",$post["salon_id"])->first();
                if($salon) {
                    $db = db_connect();
                    $model = $db->table("entries e");
                    $entry = $model->join("services s","e.service_id=s.id","left");
                    $entry = $model->select("e.*,s.name AS service_name");
                    $entry = $model->where("uniq_id",$post["uniq_id"]);
                    $entry = $model->where("e.company_id",$post["salon_id"]);
                    $entry = $model->where("is_removed_from_cart",0);
                    $entry = $model->orderBy("e.id","asc");
                    $items = $entry->get()->getResultArray();
                    foreach($items as $key => $val) {
                        $adate = $post["appointment_date"];
                        $stime = $val['stime'];
                        $etime = $val['etime'];
                        if($val['showbusystaff'] == 1 || $val['flag'] == 1)
                        {
                            $staff = $db->table('staffs s');
                            $staff->select("s.id,CONCAT_WS(' ',s.fname,s.lname) AS name,st.stime,st.etime,s.color");
                            $staff->join("staff_timings st","s.id=st.staffId");
                            $staff->where('st.date',$adate);
                            $staff->groupBy("s.id");
                            $staffs = $staff->get()->getResultArray();
                        } else {
                            $staff = $db->table('staffs s');
                            $staff->select("s.id,CONCAT_WS(' ',s.fname,s.lname) AS name,st.stime,st.etime,s.color");
                            $staff->join("staff_timings st","s.id=st.staffId");
                            $staff->where('st.date',$adate);
                            $staffs = $staff->get()->getResultArray();
                        }
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
                        $items[$key]["staffs"] = $staffs;
                    }

                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Total ".count($items)." item(s) found.";
                    $response[RESPONSE_DATA] = $items;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = [];
                }
                return $this->respond($response);
            }
        }

        public function remove_cart()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id','entry_id','salon_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->select("timezone")->where("id",$post["salon_id"])->first();
                if($salon) {
                    $model = new EntryModel;
                    $count = $model->where("id",$post["entry_id"])->get()->getNumRows();    
                    if($count > 0) {
                        $model->where('id',$post["entry_id"])->set('is_removed_from_cart',1)->update();

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Item removed successfully.";
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Item not found.";    
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                }
                return $this->respond($response);
            }
        }

        public function drop_appointment()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id','salon_id','cart_id','stime','etime');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CartModel;
                $_cart = $model->select("appointmentId,staffId,message")->where("id",$post["cart_id"])->first();
                if($_cart) {
                    $model = new AppointmentModel;
                    $count = $model->where("id",$_cart["appointmentId"])->where("status",1)->get()->getNumRows();
                    if($count > 0) {
                        $model = new CartModel;
                        $model->update($post["cart_id"],array("stime" => $post["stime"],"etime" => $post["etime"]));

                        $model = new EntryModel;
                        $model->where("cart_id",$post["cart_id"])->set(array("stime" => $post["stime"],"etime" => $post["etime"]))->update();

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Service moved successfully.";
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "You can't drop checkout appointment.";    
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Cart not found.";
                }
                return $this->respond($response);
            }
        }

        public function check_discount()
        {
            $post = $this->request->getVar();
            $input_parameter = array('date','company_id','customer_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $date = date("Y-m-d",strtotime($post["date"]));
                $shortTimestamp = strtotime($date);
                $shortDay = strtolower(date("D", $shortTimestamp));

                $date_15 = date("Y-m-d H:i:s",strtotime("-15 minutes"));
                $where = ["company_id" => $post["company_id"],"customer_id" => $post["customer_id"]];
                
                $model = new WeekendDiscount;
                $discounts = $model->select("id,sdate,edate,week_days,percentage,service_ids")->where("sdate <=",$date)->where("edate >=",$date)->where("company_id",$post["company_id"])->get()->getResultArray();
                if($discounts) {
                    $model = new WebsiteEntry;
                    $entries = $model->where($where)->where("datetime >=",$date_15)->get()->getResultArray();
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
                                        // $discount_amount = $entry["amount"]-$discount["percentage"];
                                        // $model->update($entry["id"],["discount_amount" => $discount_amount]);
                                    }
                                } else {
                                    $model->update($entry["id"],["discount_amount" => 0]);
                                }
                            }
                        }
                    }
                } else {
                    $model = new WebsiteEntry;
                    $entries = $model->where($where)->where("datetime >=",$date_15)->get()->getResultArray();
                    foreach($entries as $entry) {
                        $model->update($entry["id"],["discount_amount" => 0]);
                    }
                }
                $model = new WebsiteEntry;
                $entries = $model->where($where)->where("datetime >=",$date_15)->get()->getResultArray();
                if($entries) {
                    foreach($entries as $entry) {
                        $is_service_available = 0;

                        $model = db_connect();
                        $check_staff = $model->table("staff_services ss");
                        $check_staff = $check_staff->join("staffs s","s.id=ss.staff_id");
                        $check_staff = $check_staff->select("ss.staff_id");
                        $check_staff = $check_staff->where("ss.service_id",$entry["service_id"]);
                        $check_staff = $check_staff->where(['s.user_type' => 1,"s.is_active" => 1,"s.is_deleted" => 0]);
                        $staff = $check_staff->get()->getResultArray();
                    
                        // $model = new StaffServiceModel;
                        // $staff = $model->select("staff_id")->where("service_id",$entry["service_id"])->get()->getResultArray();
                        if($staff) {
                            $staff_ids = array_column($staff,"staff_id");
                            $model = new StaffTimingModel;
                            $count = $model->whereIn("staffId",$staff_ids)->where("date",$date)->get()->getNumRows();
                            if($count > 0) {
                                $is_service_available = 1;
                            }
                        }
                        $model = new WebsiteEntry;
                        $model->update($entry["id"],["is_final" => $is_service_available]);
                    }   
                }
                $final_data = array(); 
                $model = new WebsiteEntry; 
                $carts = $model->where($where)->where("datetime >=",$date_15)->where("is_final",1)->get()->getResultArray();
                if($carts) {
                	$currency = "";
                	$company_stime = "09:00:00";
                	$company_etime = "20:00:00";
                	$model = new CompanyModel;
                	$company = $model->select("company_stime,company_etime,company_sunday_stime,company_sunday_etime,currency")->where("id",$post["company_id"])->first();
                	if($company) {
                		$currency = $company["currency"];
                		if(date("l",strtotime($date)) == "Sunday") {
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
                		$post["duration"] += $this->ceilToNextFive($val["duration"]);
                	}
                	
                	// Check date's availability
                	$busy_slots = [];
                	$free_slots = [];
                	$available_staff_ids = "";
                	$status = 200;
                    $staff_end_times = [];
                	
                	$db = db_connect();
                    $query = $db->table("staff_timings st");
                    $query = $query->join("staffs s","s.id=st.staffId");
                    $query = $query->select("st.staffId,st.etime");
                    $query = $query->where("st.date",$date);
                    $query = $query->where("st.companyId",$post["company_id"]);
                    $query = $query->where(['s.user_type' => 1,"s.is_active" => 1,"s.is_deleted" => 0]);
                    $result = $query->get()->getResultArray();
                    
                	// $query = $db->table("staff_timings st");
                	// $query = $query->select("st.staffId,st.etime");
                	// $query = $query->where("st.date", $date);
                	// $query = $query->where("st.companyId", $post["company_id"]); // extra con.
                	// $result = $query->get()->getResultArray();
                	if ($result) {
                        $staff_end_times = array_column($result, "etime");
                		$staff_ids = array_column($result, "staffId");
                		$available_staff_ids = implode(",", $staff_ids);
                		
                		// Query to get staff services
                		$query = $db->table("staff_services ss");
                		$query = $query->whereIn("ss.service_id", $service_ids);
                		$query = $query->whereIn("ss.staff_id", $staff_ids);
                		// $query = $query->where("company_id", $post["company_id"]); // extra con.
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
                				$query = $query->where("c.companyId", $post["company_id"]);
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

                		if(!empty($staff_end_times)) {
                            $timestamps = array_map('strtotime', $staff_end_times);
                            $maxTimestamp = max($timestamps);
                            $company_etime = date("H:i:s", $maxTimestamp);
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
                		// $duration_in_seconds = $this->ceilToNextFive($post["duration"]) * 60;
                	
                		for ($current_timestamp = $s_timestamp; $current_timestamp < $e_timestamp; $current_timestamp += 300) {
                			$slot_start = date("H:i:s", $current_timestamp);
                			$slot_end = date("H:i:s", $current_timestamp + $duration_in_seconds);

                            if (($current_timestamp + $duration_in_seconds) > $e_timestamp) {
                                continue;
                            }
                			if(date("Y-m-d") == $date) {
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
                $response["data"] = $final_data;
                return $this->respond($response);
            }
        }
        
        public function ceilToNextFive($number) {
            return floor($number / 5) * 5;
            // if ($number % 2 == 0) {
            //     return $number;
            // } else {
            //     return ceil($number / 5) * 5;
            // }
        }
    }