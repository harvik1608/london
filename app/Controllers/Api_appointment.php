<?php 
    namespace App\Controllers;

    use CodeIgniter\RESTful\ResourceController;
    use CodeIgniter\API\ResponseTrait;
    use App\Models\CompanyModel;
    use App\Models\Staff;
    use App\Models\StaffServiceModel;
    use App\Models\SubServiceModel;
    use App\Models\StaffTimingModel;
    use App\Models\AppointmentModel;
    use App\Models\CartModel;
    use App\Models\CustomerModel;

    class Api_appointment extends ResourceController
    {
        use ResponseTrait;
        protected $helpers = ["custom"];

        public function staffs()
        {
            $post = $this->request->getVar();
            $input_parameter = array('page');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $page = $post['page'] ?? 1;
                $limit = $post['limit'] ?? LIMIT;
                $offset = ($page - 1) * $limit;

                $model = new Staff;
                $totalRecords = $model->where("user_type",1)->countAllResults();
                $totalPages = ceil($totalRecords/$limit);
                $staffs = $model->where("user_type",1)->limit($limit, $offset)->orderBy("id","desc")->get()->getResultArray();
                if($staffs) {
                    $model = new StaffServiceModel;
                    foreach ($staffs as $key => $val) {
                        $result = $model->select("service_id")->where("staff_id",$val["id"])->get()->getResultArray();
                        if($result) {
                            $service_group_ids = [];
                            $service_ids = [];
                            foreach($result as $row) {
                                $group = explode("_",$row["service_id"]);
                                if(isset($group[2])) {
                                    array_push($service_group_ids,$group[2]);
                                } else {
                                    array_push($service_ids,$row["service_id"]);
                                }
                            }
                            if(!empty($service_group_ids)) {
                                $service_group_ids = implode(",", $service_group_ids);
                            } else {
                                $service_group_ids = "";
                            }
                            if(!empty($service_ids)) {
                                $service_ids = implode(",", $service_ids);
                            } else {
                                $service_ids = "";
                            }
                            $staffs[$key]["service_group_ids"] = $service_group_ids;
                            $staffs[$key]["service_ids"] = $service_ids;
                        } else {
                            $staffs[$key]["service_group_ids"] = "";
                            $staffs[$key]["service_ids"] = "";
                        }
                    }
                }
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Total ".$totalRecords." staff(s) found.";
                $response[TOTAL_COUNT] = $totalRecords;
                $response[CURRENT_PAGE] = (int) $page;
                if((int) $page == $totalPages || $totalPages == 0) {
                    $response[NEXT_PAGE] = 0;
                } else {
                    $response[NEXT_PAGE] = (int) $page + 1;
                }
                $response[TOTAL_PAGE] = $totalPages;
                $response[LIMIT_WORD] = (int) $limit;
                $response[RESPONSE_DATA] = $staffs;
                return $this->respond($response);
            }
        }

        public function view_staff()
        {
            $post = $this->request->getVar();
            $input_parameter = array('staff_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new Staff;
                $staff = $model->where("id",$post["staff_id"])->first();
                if($staff) {
                    $model = new StaffServiceModel;
                    $result = $model->select("service_id")->where("staff_id",$post["staff_id"])->get()->getResultArray();
                    if($result) {
                        $service_group_ids = [];
                        $service_ids = [];
                        foreach($result as $row) {
                            $group = explode("_",$row["service_id"]);
                            if(isset($group[2])) {
                                array_push($service_group_ids,$group[2]);
                            } else {
                                array_push($service_ids,$row["service_id"]);
                            }
                        }
                        if(!empty($service_group_ids)) {
                            $service_group_ids = implode(",", $service_group_ids);
                        } else {
                            $service_group_ids = "";
                        }
                        if(!empty($service_ids)) {
                            $service_ids = implode(",", $service_ids);
                        } else {
                            $service_ids = "";
                        }
                        $staff["service_group_ids"] = $service_group_ids;
                        $staff["service_ids"] = $service_ids;
                    } else {
                        $staff["service_group_ids"] = "";
                        $staff["service_ids"] = "";
                    }
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Staff found.";
                    $response[RESPONSE_DATA] = $staff;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Staff not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function create_staff()
        {
            $post = $this->request->getVar();
            $input_parameter = array('fname','lname','phone','password','confirm_password','salon_id','user_id','is_active');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $staff_id = 0;
                $roles = "";
                if(isset($post['roles']) && $post['roles'] != "") {
                    $roles = $post['roles'];
                }
                $designation = "";
                if(isset($post['designation']) && $post['designation'] != "") {
                    $designation = $post['designation'];
                }
                $is_all_service = "N";
                if(isset($post["is_all_service"]) && $post["is_all_service"] != "") {
                    $is_all_service = $post["is_all_service"];
                }
                $color = "#000000";
                if(isset($post["color"]) && $post["color"] != "") {
                    $color = $post["color"];
                }
                $insert_data["fname"] = $post["fname"];
                $insert_data["lname"] = $post["lname"];
                $insert_data["phone"] = $post["phone"];
                $insert_data["email"] = $post["email"];
                $insert_data["roles"] = $roles;
                $insert_data["designation"] = $designation;
                $insert_data["color"] = $color;
                $insert_data["is_active"] = $post["is_active"];
                $insert_data["password"] = md5($post["password"]);
                $insert_data['user_type'] = 1;
                $insert_data['is_all_service'] = $is_all_service;
                $insert_data['company_id'] = $post["salon_id"];
                $insert_data['created_by'] = $post["user_id"];
                $insert_data['updated_by'] = $post["user_id"];
                $insert_data['created_at'] = date("Y-m-d H:i:s");
                $insert_data['updated_at'] = date("Y-m-d H:i:s");
                $model = new Staff;
                $model->insert($insert_data);
                if($model->getInsertID() > 0)
                {
                    $staff_id = $model->getInsertID();
                    if(isset($post["service_group_ids"]) && $post["service_group_ids"] != "")
                    {
                        $service_arr    = explode(",", $post["service_group_ids"]);
                        $unique_arr     = array();
                        if(count($service_arr) > 0)
                            $unique_arr = array_unique($service_arr);

                        for($i = 0; $i < count($unique_arr); $i ++)
                        {
                            $service_param[] = array(
                                "staff_id"   => $staff_id,
                                "service_id" => "service_group_".$unique_arr[$i],
                                "company_id" => $post["salon_id"],
                                "created_by"   => $post["user_id"],
                                "updated_by" => $post["user_id"],
                                "created_at" => date("Y-m-d H:i:s"),
                                "updated_at" => date("Y-m-d H:i:s")
                            );
                        }
                        $model = new StaffServiceModel;
                        $model->insertBatch($service_param);
                    }
                    if(isset($post["service_ids"]) && $post["service_ids"] != "")
                    {
                        $service_arr    = explode(",", $post["service_ids"]);
                        $unique_arr     = array();
                        if(count($service_arr) > 0)
                            $unique_arr = array_unique($service_arr);

                        for($i = 0; $i < count($unique_arr); $i ++)
                        {
                            $service_param[] = array(
                                "staff_id"   => $staff_id,
                                "service_id" => $unique_arr[$i],
                                "company_id" => $post["salon_id"],
                                "created_by" => $post["user_id"],
                                "updated_by" => $post["user_id"],
                                "created_at" => date("Y-m-d H:i:s"),
                                "updated_at" => date("Y-m-d H:i:s")
                            );
                        }
                        $model = new StaffServiceModel;
                        $model->insertBatch($service_param);
                    }
                }
                $model = new Staff;
                $staff = $model->where("id",$staff_id)->first();
                $model = new StaffServiceModel;
                $result = $model->select("service_id")->where("staff_id",$staff_id)->get()->getResultArray();
                if($result) {
                    $service_group_ids = [];
                    $service_ids = [];
                    foreach($result as $row) {
                        $group = explode("_",$row["service_id"]);
                        if(isset($group[2])) {
                            array_push($service_group_ids,$group[2]);
                        } else {
                            array_push($service_ids,$row["service_id"]);
                        }
                    }
                    if(!empty($service_group_ids)) {
                        $service_group_ids = implode(",", $service_group_ids);
                    } else {
                        $service_group_ids = "";
                    }
                    if(!empty($service_ids)) {
                        $service_ids = implode(",", $service_ids);
                    } else {
                        $service_ids = "";
                    }
                    $staff["service_group_ids"] = $service_group_ids;
                    $staff["service_ids"] = $service_ids;
                } else {
                    $staff["service_group_ids"] = "";
                    $staff["service_ids"] = "";
                }
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Staff added successfully.";
                $response[RESPONSE_DATA] = $staff;
                return $this->respond($response);
            }
        }

        public function delete_staff()
        {
            $post = $this->request->getVar();
            $input_parameter = array('staff_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new Staff;
                $staff = $model->where("id",$post["staff_id"])->first();
                if($staff) {
                    if($model->update($post["staff_id"],["is_deleted" => 1,"updated_at" => date("Y-m-d H:i:s")])) {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Staff removed successfully.";
                    }   
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Staff not found.";
                }
                return $this->respond($response);
            }
        }

        public function update_staff()
        {
            $post = $this->request->getVar();
            $input_parameter = array('fname','lname','phone','salon_id','user_id','is_active','staff_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $staff_id = $post["staff_id"];
                $model = new Staff;
                $staff = $model->where("id",$staff_id)->first();
                if($staff) {
                    $roles = $staff["roles"];
                    if(isset($post['roles']) && $post['roles'] != "") {
                        $roles = $post['roles'];
                    }
                    $designation = $staff["designation"];
                    if(isset($post['designation']) && $post['designation'] != "") {
                        $designation = $post['designation'];
                    }
                    $is_all_service = $staff["is_all_service"];
                    if(isset($post["is_all_service"]) && $post["is_all_service"] != "") {
                        $is_all_service = $post["is_all_service"];
                    }
                    $color = $staff["color"];
                    if(isset($post["color"]) && $post["color"] != "") {
                        $color = $post["color"];
                    }
                    $update_data["fname"] = $post["fname"];
                    $update_data["lname"] = $post["lname"];
                    $update_data["phone"] = $post["phone"];
                    $update_data["email"] = $post["email"];
                    $update_data["roles"] = $roles;
                    $update_data["designation"] = $designation;
                    $update_data["color"] = $color;
                    $update_data["is_active"] = $post["is_active"];
                    $update_data['user_type'] = 1;
                    $update_data['is_all_service'] = $is_all_service;
                    $update_data['company_id'] = $post["salon_id"];
                    $update_data['updated_by'] = $post["user_id"];
                    $update_data['updated_at'] = date("Y-m-d H:i:s");
                    if($model->update($staff_id,$update_data))
                    {
                        $model = new StaffServiceModel;
                        $result = $model->where("staff_id",$staff_id)->delete();
                        if(isset($post["service_group_ids"]) && $post["service_group_ids"] != "")
                        {
                            $service_arr    = explode(",", $post["service_group_ids"]);
                            $unique_arr     = array();
                            if(count($service_arr) > 0)
                                $unique_arr = array_unique($service_arr);

                            for($i = 0; $i < count($unique_arr); $i ++)
                            {
                                $service_param[] = array(
                                    "staff_id"   => $staff_id,
                                    "service_id" => "service_group_".$unique_arr[$i],
                                    "company_id" => $post["salon_id"],
                                    "created_by"   => $post["user_id"],
                                    "updated_by" => $post["user_id"],
                                    "created_at" => date("Y-m-d H:i:s"),
                                    "updated_at" => date("Y-m-d H:i:s")
                                );
                            }
                            $model->insertBatch($service_param);
                        }
                        if(isset($post["service_ids"]) && $post["service_ids"] != "")
                        {
                            $service_arr    = explode(",", $post["service_ids"]);
                            $unique_arr     = array();
                            if(count($service_arr) > 0)
                                $unique_arr = array_unique($service_arr);

                            for($i = 0; $i < count($unique_arr); $i ++)
                            {
                                $service_param[] = array(
                                    "staff_id"   => $staff_id,
                                    "service_id" => $unique_arr[$i],
                                    "company_id" => $post["salon_id"],
                                    "created_by" => $post["user_id"],
                                    "updated_by" => $post["user_id"],
                                    "created_at" => date("Y-m-d H:i:s"),
                                    "updated_at" => date("Y-m-d H:i:s")
                                );
                            }
                            $model->insertBatch($service_param);
                        }
                    }
                    $model = new Staff;
                    $staff = $model->where("id",$staff_id)->first();
                    $model = new StaffServiceModel;
                    $result = $model->select("service_id")->where("staff_id",$staff_id)->get()->getResultArray();
                    if($result) {
                        $service_group_ids = [];
                        $service_ids = [];
                        foreach($result as $row) {
                            $group = explode("_",$row["service_id"]);
                            if(isset($group[2])) {
                                array_push($service_group_ids,$group[2]);
                            } else {
                                array_push($service_ids,$row["service_id"]);
                            }
                        }
                        if(!empty($service_group_ids)) {
                            $service_group_ids = implode(",", $service_group_ids);
                        } else {
                            $service_group_ids = "";
                        }
                        if(!empty($service_ids)) {
                            $service_ids = implode(",", $service_ids);
                        } else {
                            $service_ids = "";
                        }
                        $staff["service_group_ids"] = $service_group_ids;
                        $staff["service_ids"] = $service_ids;
                    } else {
                        $staff["service_group_ids"] = "";
                        $staff["service_ids"] = "";
                    }
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Staff edited successfully.";
                    $response[RESPONSE_DATA] = $staff;
                    return $this->respond($response);
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Staff not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function roles()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $data = array(
                    array('label' => 'reception','value' => 'Reception'),
                    array('label' => 'manager','value' => 'Manager'),
                    array('label' => 'hair_dresser','value' => 'Hair Dresser'),
                    array('label' => 'beautician','value' => 'Beautician'),
                );
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "";
                $response[RESPONSE_DATA] = $data;
                return $this->respond($response);
            }
        }

        public function positions()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $data = array(
                    array('label' => 'appointments','value' => 'Appointments'),
                    array('label' => 'groups','value' => 'Service Groups'),
                    array('label' => 'sub_services','value' => 'Services'),
                    array('label' => 'staffs','value' => 'Staffs'),
                    array('label' => 'staff_timing','value' => 'Staff Timing'),
                    array('label' => 'customers','value' => 'Customers'),
                    array('label' => 'payment_types','value' => 'Payment Types'),
                    array('label' => 'discount_types','value' => 'Discount Types'),
                    array('label' => 'weekend_discount','value' => 'Weekend Discounts'),
                    array('label' => 'daily_reports','value' => 'Daily Reports'),
                    array('label' => 'gallery','value' => 'Gallery'),
                    array('label' => 'companies','value' => 'Companies')
                );
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "";
                $response[RESPONSE_DATA] = $data;
                return $this->respond($response);
            }
        }

        public function add_staff_timing()
        {
            $post = $this->request->getVar();
            $input_parameter = array('date','shift_stime','shift_etime','staff_id','salon_id','user_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new StaffTimingModel;
                $count = $model->where(["staffId" => $post["staff_id"],"date" => $post["date"],"stime" => $post["shift_stime"],"etime" => $post["shift_etime"]])->get()->getNumRows();
                if($count == 0) {
                    $day_no = $post['date'] != "" ? date("w",strtotime($post['date'])) : date("w",strtotime(date("Y-m-d")));

                    $sstime = format_date(10,$post['shift_stime']);
                    $eetime = format_date(10,$post['shift_etime']);
                    $stime_timestamp = format_date(7,$post["date"]." ".$sstime);
                    $etime_timestamp = format_date(7,$post["date"]." ".$eetime);
                    if($stime_timestamp >= $etime_timestamp) {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Shift start time must be less than shift end time.";
                        $response[RESPONSE_DATA] = (object) array();
                    } else {
                        $isRepeat = "N";
                        if(isset($post["isRepeat"]) && $post["isRepeat"] != "") {
                            $isRepeat = $post["isRepeat"];
                        }
                        $model = new StaffTimingModel;
                        $model->where(array("staffId" => $post['staff_id'],"date" => $post['date'],"companyId !=" => $post["salon_id"]))->delete();
                        if($isRepeat == "N") {
                            $insert_data = array(
                                'staffId' => $post["staff_id"],
                                "date" => $post["date"],
                                "stime" => $post["shift_stime"],
                                "etime" => $post["shift_etime"],
                                "isRepeat" => $isRepeat,
                                "companyId" => $post["salon_id"],
                                "addedBy" => $post["user_id"],
                                "updatedBy" => 0,
                                "createdAt" => format_date(5),
                                "updatedAt" => ""
                            );
                            $model->insert($insert_data);
                        } else {
                            $weekActuaDate = strtotime($post['date']);
                            $weekStartDate = date('Y-m-d',strtotime("last Monday", $weekActuaDate));
                            $weekStartDate = $day_no == 1 ? $post['date'] : $weekStartDate;
                            $timing_arr = getDatesFromRange($weekStartDate,date("Y-m-d",strtotime($weekStartDate." +6 days")));
                            $model = new StaffTimingModel;
                            foreach($timing_arr as $arr)
                            {
                                $model = new StaffTimingModel;
                                $checkTime = $model->where("staffId",$post['staff_id'])->where("date",$arr)->where("companyId",$post["salon_id"])->get()->getNumRows();
                                if($checkTime == 0)
                                {
                                    $params['staffId']  = $post['staff_id'];
                                    $params['date']     = $arr;
                                    $params['stime']    = format_date(14,$post['shift_stime']);
                                    $params['etime']    = format_date(14,$post['shift_etime']);
                                    $params['isRepeat'] = $isRepeat;
                                    $params['companyId']= $post["salon_id"];
                                    $params['addedBy']  = $post["user_id"];
                                    $params['createdAt']= format_date(5);
                                    $params['updatedBy']= 0;
                                    $params['updatedAt']= "";   
                                    $model = new StaffTimingModel;
                                    $model->insert($params);
                                }
                            }
                        }
                        $model = new StaffTimingModel;
                        $_data = $model->select("id,staffId,date,stime,etime,isRepeat")->where(["staffId" => $post["staff_id"],"date" => $post["date"]])->first();
                        $_data["staff_id"] = $_data["staffId"];
                        $_data["date"] = $_data["date"];
                        $_data["start_time"] = $_data["stime"];
                        $_data["end_time"] = $_data["etime"];
                        $_data["isRepeat"] = $_data["isRepeat"];
                        unset($_data["staffId"]);
                        unset($_data["stime"]);
                        unset($_data["etime"]);


                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Timing added successfully.";
                        $response[RESPONSE_DATA] = $_data;
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Timing already added.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function update_staff_timing()
        {
            $post = $this->request->getVar();
            $input_parameter = array('date','shift_stime','shift_etime','staff_id','salon_id','user_id','timing_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new StaffTimingModel;
                $__row = $model->where("id",$post["timing_id"])->first();
                if($__row) {
                    $day_no = date("w",strtotime($post['date']));

                    $sstime = format_date(10,$post['shift_stime']);
                    $eetime = format_date(10,$post['shift_etime']);
                    $stime_timestamp = format_date(7,$post["date"]." ".$sstime);
                    $etime_timestamp = format_date(7,$post["date"]." ".$eetime);
                    if($stime_timestamp >= $etime_timestamp) {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Shift start time must be less than shift end time.";
                        $response[RESPONSE_DATA] = (object) array();
                    } else {
                        $isRepeat = $__row["isRepeat"];
                        if(isset($post["isRepeat"]) && $post["isRepeat"] != "") {
                            $isRepeat = $post["isRepeat"];
                        }
                        $update_data = array(
                            'staffId' => $post["staff_id"],
                            "date" => $post["date"],
                            "stime" => $post["shift_stime"],
                            "etime" => $post["shift_etime"],
                            "isRepeat" => $isRepeat,
                            "companyId" => $post["salon_id"],
                            "updatedBy" => $post["user_id"],
                            "updatedAt" => format_date(5)
                        );
                        if($model->update($post["timing_id"],$update_data)) {
                            $timing_id = $post["timing_id"];
                            $timing = $model->where("id",$timing_id)->first();

                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "Timing edited successfully.";
                            $response[RESPONSE_DATA] = $timing;
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                            $response[RESPONSE_DATA] = (object) array();
                        }
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Timing not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function view_staff_timing()
        {
            $post = $this->request->getVar();
            $input_parameter = array('timing_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new StaffTimingModel;
                $__row = $model->where("id",$post["timing_id"])->first();
                if($__row) {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Timing found.";
                    $response[RESPONSE_DATA] = $__row;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Timing not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function delete_staff_timing()
        {
            $post = $this->request->getVar();
            $input_parameter = array('timing_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new StaffTimingModel;
                $__row = $model->where("id",$post["timing_id"])->first();
                if($__row) {
                    $model = new CartModel;
                    $count = $model->where(array("date" => $__row["date"],"staffId" => $__row["staffId"],"is_cancelled" => 0))->get()->getNumRows();
                    if($count == 0) {
                        $model = new StaffTimingModel();
                        $model->where("id",$post["timing_id"])->delete();

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Timing deleted successfully.";
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Staff has appointment on ".date('d M, Y',strtotime($__row["date"])).". You can't delete.";
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Timing not found.";
                }
                return $this->respond($response);
            }
        }

        public function staff_timing_list()
        {
            $post = $this->request->getVar();
            $input_parameter = array('salon_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $total_staff_hours = 0;
                $post['addDay'] = 0;

                $model = new Staff;
                $data["staffs"] = $model->select("id,CONCAT(fname,' ',lname) AS name")->where("user_type !=",0)->where('is_active','1')->where('is_deleted',0)->get()->getResultArray();
                if($data["staffs"]) {
                    $sign = $post['action'] == 3 ? "+" : "-";
                    if($post['addDay'] == 1) {
                        $sdate = $post['last_date'] == "" ? date('Y-m-d',strtotime("this week")) : $post['last_date'];
                    } else {
                        $sdate = $post['last_date'] == "" ? date('Y-m-d', strtotime("this week")) : date("Y-m-d",strtotime($post['last_date']."".$sign."1 day"));
                    }
                    $tdate = $sdate;
                    $edate = date("Y-m-d",strtotime($sdate."".$sign."6 days"));
                    if($sign == "-")
                    {
                        $sdate = $edate;
                        $edate = $tdate;
                    }
                    $timing_arr = getDatesFromRange($sdate,$edate);
                    $model = new StaffTimingModel();
                    foreach($data["staffs"] as $key => $val) {
                        $total_hours = 0;
                        $dates = array();
                        foreach($timing_arr as $k => $v) {
                            $timing = $model->select("id,date,stime,etime,isRepeat")->where('staffId',$val['id'])->where("companyId",$post["salon_id"])->where("date",$v)->first();
                            if($timing)
                            {
                                $dates[] = $timing['date'];
                            }
                        }
                        $data["staffs"][$key]["date"] = $dates;
                    }
                    foreach($data["staffs"] as $key => $val) {
                        $items = array();
                        $total_hours = 0;
                        foreach($timing_arr as $k => $v) {
                            if(in_array($v, $val['date'])) {
                                $timing = $model->select("id,date,stime,etime,isRepeat")->where('staffId',$val['id'])->where("companyId",$post["salon_id"])->where("date",$v)->first();
                                if($timing)
                                {
                                    $total_hours = $total_hours + calc_hours($timing["date"]." ".$timing["stime"],$timing["date"]." ".$timing["etime"]);
                                    $total_staff_hours = $total_staff_hours + calc_hours($timing["date"]." ".$timing["stime"],$timing["date"]." ".$timing["etime"]);
                                    $items[] = array(
                                        'id' => $timing["id"],
                                        "staff_id" => $val["id"],
                                        "staff_name" => $val["name"],
                                        "date" => date("d M, Y",strtotime($timing["date"])),
                                        "start_time" => date("H:i:s",strtotime($timing["stime"])),
                                        "end_time" => date("H:i:s",strtotime($timing["etime"])),
                                        "isRepeat" => $timing["isRepeat"],
                                        "total_hours" => (string) calc_hours($timing["date"]." ".$timing["stime"],$timing["date"]." ".$timing["etime"])
                                    );
                                } else {
                                     $items[] = array(
                                        'id' => $timing["id"],
                                        "staff_id" => $val["id"],
                                        "staff_name" => $val["name"],
                                        "date" => "",
                                        "start_time" => "",
                                        "end_time" => "",
                                        "isRepeat" => "",
                                        "total_hours" => ""
                                    );
                                }
                            } else {
                                $items[] = array(
                                    'id' => "0",
                                    "staff_id" => $val["id"],
                                    "staff_name" => $val["name"],
                                    "date" => date("d M, Y",strtotime($v)),
                                    "start_time" => "",
                                    "end_time" => "",
                                    "isRepeat" => "",
                                    "total_hours" => "0"
                                );
                            }
                        }
                        $items[] = array(
                            'id' => "0",
                            "staff_id" => "0",
                            "staff_name" => "",
                            "date" => "TOTAL HOURS",
                            "start_time" => "",
                            "end_time" => "",
                            "isRepeat" => "",
                            "total_hours" => (string) $total_hours
                        );
                        $data["staffs"][$key]["items"] = $items;
                        unset($data["staffs"][$key]["date"]);
                    }
                }
                $data["total_staff_hours"] = $total_staff_hours;
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "";
                $response[RESPONSE_DATA] = $data;
                return $this->respond($response);
            }
        }

        public function search_customer()
        {
            $post = $this->request->getVar();
            $input_parameter = array('search_text','page');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $page = $post['page'] ?? 1;
                $limit = $post['limit'] ?? LIMIT;
                $offset = ($page - 1) * $limit;

                $model = new CustomerModel;
                $totalRecords = $model->select("id,name,email,phone,note")->where("name LIKE '".$post['search_text']."%' OR phone LIKE '".$post['search_text']."%'")->countAllResults();
                $totalPages = ceil($totalRecords/$limit);
                
                $customers = $model->select("id,name,email,phone,note")->where("name LIKE '".$post['search_text']."%' OR phone LIKE '".$post['search_text']."%'")->limit($limit, $offset)->orderBy("name","asc")->get()->getResultArray();
                
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Total ".count($customers)." found.";
                $response[TOTAL_COUNT] = $totalRecords;
                $response[CURRENT_PAGE] = (int) $page;
                if((int) $page == $totalPages || $totalPages == 0) {
                    $response[NEXT_PAGE] = 0;
                } else {
                    $response[NEXT_PAGE] = (int) $page + 1;
                }
                $response[TOTAL_PAGE] = $totalPages;
                $response[LIMIT_WORD] = (int) $limit;
                $response[RESPONSE_DATA] = $customers;
                return $this->respond($response);
            }
        }

        public function customer_history()
        {
            $post = $this->request->getVar();
            $input_parameter = array('phone','page');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $page = $post['page'] ?? 1;
                $limit = $post['limit'] ?? LIMIT;
                $offset = ($page - 1) * $limit;

                $cust_id = 0;
                $model = new CustomerModel;
                $customer = $model->select("id")->where("phone",$post["phone"])->first();
                if(!empty($customer)) {
                    $cust_id = $customer["id"];
                }

                $model = new AppointmentModel;
                $totalRecords = $model->select("id,bookingDate,note,finalAmt,status,bookedFrom,note")->where("customerId",$cust_id)->countAllResults();
                $totalPages = ceil($totalRecords/$limit);

                $appointments = $model->select("id,bookingDate,note,finalAmt,status,bookedFrom,note")
                ->where("customerId",$cust_id)
                ->limit($limit, $offset)
                ->orderBy("id","desc")
                ->get()
                ->getResultArray();
                if(!empty($appointments)) {
                    $db = db_connect();
                    foreach($appointments as $key => $val) {
                        $master = $db->table('carts cr');
                        $master->select("cr.id,cr.stime,cr.duration,CONCAT_WS(' ',s.fname,s.lname) AS staff_name,ss.name AS service_name,cr.serviceNm,cr.caption");
                        $master->join("services ss","cr.serviceSubId=ss.id","left");
                        $master->join("staffs s","cr.staffId=s.id","left");
                        $master->where('cr.appointmentId',$val["id"]);
                        $master->orderBy("cr.id","asc");
                        $carts = $master->get()->getResultArray();
                        if(!empty($carts)) {
                            $appointments[$key]["items"] = $carts;
                        } else {
                            $appointments[$key]["items"] = [];
                        }
                    }
                }
                
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Total ".$totalRecords." appointments found.";
                $response[TOTAL_COUNT] = $totalRecords;
                $response[CURRENT_PAGE] = (int) $page;
                if((int) $page == $totalPages || $totalPages == 0) {
                    $response[NEXT_PAGE] = 0;
                } else {
                    $response[NEXT_PAGE] = (int) $page + 1;
                }
                $response[TOTAL_PAGE] = $totalPages;
                $response[LIMIT_WORD] = (int) $limit;
                $response[RESPONSE_DATA] = $appointments;
                return $this->respond($response);
            }
        }

        public function cancel_appointment()
        {
            $post = $this->request->getVar();
            $input_parameter = array('appointment_id','user_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new AppointmentModel;
                $appointment = $model->where("id",$post["appointment_id"])->first();
                if($appointment) {
                    $salon_note = "";
                    if(isset($post["note"]) && $post["note"] != "") {
                        $salon_note = $post["note"];
                    }
                    if($model->update($post['appointment_id'],["status" => 3,"salon_note" => $salon_note])) {
                        $model = new CartModel;
                        $model->where("appointmentId",$post["appointment_id"])->set(["is_cancelled" => 1])->update();
                    } 
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Appointment cancelled successfully.";
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Appointment not found.";
                }
                return $this->respond($response);
            }
        }
    }