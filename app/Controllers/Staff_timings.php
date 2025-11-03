<?php
    namespace App\Controllers;

    use App\Models\Staff;
    use App\Models\StaffTimingModel;
    use App\Models\CompanyModel;
    use App\Models\CartModel;

    class Staff_timings extends BaseController
    {
        protected $helpers = ["custom"];

        public function __construct()
        {
            $session = session();
            if($session->get('userdata')) {
                $this->userdata = $session->get('userdata');
            }
        }

        public function index()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("staff_timing")) {
                    $model = new CompanyModel;
                    $data["company"] = $model->select("company_stime,company_etime")->where("id",static_company_id())->first();
                    return view('admin/staff/timing',$data);
                } else {
                    return redirect("profile");
                }
            } else {
                return redirect("admin");
            }
        }

        public function get_timing_grid()
        {
            $session = session();
            $post = $this->request->getVar();

            $sign = $post['sign'] == "next" ? "+" : "-";
            if($post['addDay'] == 1)
                $sdate = $post['sdate'] == "" ? date('Y-m-d',strtotime("this week")) : $post['sdate'];
            else 
                $sdate = $post['sdate'] == "" ? date('Y-m-d', strtotime("this week")) : date("Y-m-d",strtotime($post['sdate']."".$sign."1 day"));

            $tdate = $sdate;
            $edate = date("Y-m-d",strtotime($sdate."".$sign."6 days"));
            if($sign == "-")
            {
                $sdate = $edate;
                $edate = $tdate;
            }
            $model = new Staff();
            $staffs = $model->where("user_type !=",0)->where('is_active','1')->where('is_deleted',0)->get()->getResultArray();

            $timingModel = new StaffTimingModel();
            $timings = $timingModel->select("id, staffId, date, stime, etime, isRepeat")->where("companyId", static_company_id())->whereIn("date", getDatesFromRange($sdate, $edate))->findAll();

            $timingMap = [];
            foreach ($timings as $t) {
                $timingMap[$t['staffId']][$t['date']] = $t;
            }
            foreach ($staffs as &$staff) {
                $dates = [];
                $times = [];

                if (isset($timingMap[$staff['id']])) {
                    foreach ($timingMap[$staff['id']] as $date => $t) {
                        $dates[] = $date;
                        $times[$date] = $t['id'] . "_" .date("H:i", strtotime($t['stime'])) ."<br>To<br>" .date("H:i", strtotime($t['etime'])) ."_" . $t['isRepeat'];
                    }
                }

                $staff['date'] = $dates;
                $staff['time'] = $times;
            }
            unset($staff);


            // $model = new Staff;
            // $staffs = $model->where("user_type !=",0)->where('is_active','1')->where('is_deleted',0)->get()->getResultArray();
            // $timing_arr = getDatesFromRange($sdate,$edate);
            // if($staffs)
            // {
            //     $model = new StaffTimingModel();
            //     foreach($staffs as $key => $val)
            //     {
            //         $dates = $times = array();
            //         foreach($timing_arr as $k => $v)
            //         {
            //             $timing = $model->select("id,date,stime,etime,isRepeat")->where('staffId',$val['id'])->where("companyId",static_company_id())->where("date",$v)->first();
            //             if($timing)
            //             {
            //                 $dates[] = $timing['date'];
            //                 $times[$timing['date']] = $timing['id']."_".date("H:i",strtotime($timing['stime']))."<br>To<br>".date("H:i",strtotime($timing['etime']))."_".$timing['isRepeat'];
            //             }
            //         }
            //         $staffs[$key]['date'] = $dates;
            //         $staffs[$key]['time'] = $times;
            //     }
            // }
            $data['sdate'] = $sdate;
            $data['edate'] = $edate;
            $data['staffs']= $staffs;
            
            return view('admin/staff/get_timing_grid',$data);
        }

        public function remove_staff_timing()
        {
            $post = $this->request->getVar();

            $model = new StaffTimingModel();
            $timing = $model->select("staffId,date")->where("id",$post["timing_id"])->first();
            if($timing) {
                $model = new CartModel;
                $count = $model->where(array("date" => $timing["date"],"staffId" => $timing["staffId"],"is_cancelled" => 0,"companyId" => static_company_id()))->get()->getNumRows();
                if($count == 0) {
                    $model = new StaffTimingModel();
                    if($model->where("id",$post["timing_id"])->delete())
                    {
                        $ret_arr["status"] = 1;
                        $ret_arr["message"] = "Timing removed successfully";
                    } else {
                        $ret_arr["status"] = 0;
                        $ret_arr["message"] = "Oops something went wrong.";
                    }
                } else {
                    $ret_arr["status"] = 0;
                    $ret_arr["message"] = "Staff has appointment on ".date('d M, Y',strtotime($timing["date"])).". You can't delete.";
                }
            }
            echo json_encode($ret_arr);
            exit;
        }

        public function new_timing()
        {
            $session = session();
            $post = $this->request->getVar();
            $createdBy = $session->get('userdata');

            $dayNo = date("w",strtotime($post['staff_timing_dt']));

            $sstime = format_date(10,$post['shift_stime']);
            $eetime = format_date(10,$post['shift_etime']);
            $stime_timestamp = format_date(7,$post["staff_timing_dt"]." ".$sstime);
            $etime_timestamp = format_date(7,$post["staff_timing_dt"]." ".$eetime);
            if($stime_timestamp >= $etime_timestamp)
            {
                $ret_arr['status'] = 0;
                $ret_arr['message'] = "Shift start time must be less than shift end time.";
            } else {
                $params['staffId']  = $post['staff_timing_id'];
                $params['date']     = $post['staff_timing_dt'];
                $params['stime']    = format_date(14,$post['shift_stime']);
                $params['etime']    = format_date(14,$post['shift_etime']);
                $params['updatedAt']= format_date(5);
                if($post['shift_repeat'] == "N" && $post['timing_uid'] == "0")
                {
                    // $model = new StaffTimingModel;
                    // $model->where(array("staffId" => $post['staff_timing_id'],"date" => $post['staff_timing_dt'],"companyId !=" => static_company_id()))->delete();

                    $params['isRepeat'] = $post['shift_repeat'];
                    $params['updatedBy']= 0;
                    $params['updatedAt']= "";
                    if($post['timing_uid'] == "0")
                    {
                        $params['addedBy']  = $createdBy['id'];
                        $params['companyId']= static_company_id();
                        $params['createdAt']= format_date(5);

                        $model = new StaffTimingModel;
                        $model->insert($params);
                        $response = 1;
                        $ret_arr['message'] = "Staff time added successfully.";
                    } else {
                        $model = new StaffTimingModel;
                        $model->update($post['timing_uid'],$params);
                        $response = 1;
                        $ret_arr['message'] = "Staff time edited successfully.";
                    }
                } else {
                    if($post['timing_uid'] == "0")
                    {
                        $model = new StaffTimingModel;
                        $model->where(array("staffId" => $post['staff_timing_id'],"date" => $post['staff_timing_dt'],"companyId !=" => static_company_id()))->delete();

                        $weekActuaDate = strtotime($post['staff_timing_dt']);
                        $weekStartDate = date('Y-m-d',strtotime("last Monday", $weekActuaDate));
                        $weekStartDate = $dayNo == 1 ? $post['staff_timing_dt'] : $weekStartDate;
                        $timing_arr = getDatesFromRange($weekStartDate,date("Y-m-d",strtotime($weekStartDate." +6 days")));
                        $model = new StaffTimingModel;
                        foreach($timing_arr as $arr)
                        {
                            $model = new StaffTimingModel;
                            $checkTime = $model->where("staffId",$post['staff_timing_id'])->where("date",$arr)->where("companyId",static_company_id())->get()->getNumRows();
                            if($checkTime == 0)
                            {
                                $params['staffId']  = $post['staff_timing_id'];
                                $params['date']     = $arr;
                                $params['stime']    = format_date(14,$post['shift_stime']);
                                $params['etime']    = format_date(14,$post['shift_etime']);
                                $params['isRepeat'] = $post['shift_repeat'];
                                $params['companyId']= static_company_id();
                                $params['addedBy']  = $createdBy['id'];
                                $params['createdAt']= format_date(5);
                                $params['updatedBy']= format_date(5);
                                $params['updatedAt']= "";   
                                $model = new StaffTimingModel;
                                $model->insert($params);
                                $response = 1;
                            }
                        }
                        $ret_arr['message'] = "Staff time added successfully.";
                    } else {
                        $model = new StaffTimingModel;
                        $model->where(array("staffId" => $post['staff_timing_id'],"date" => $post['staff_timing_dt'],"companyId !=" => static_company_id()))->delete();

                        $params['staffId']  = $post['staff_timing_id'];
                        $params['date']     = $post['staff_timing_dt'];
                        $params['stime']    = format_date(14,$post['shift_stime']);
                        $params['etime']    = format_date(14,$post['shift_etime']);
                        $params['updatedAt']= format_date(5);
                        $model = new StaffTimingModel;
                        $model->update($post['timing_uid'],$params);
                        
                        $response = 1;
                        $ret_arr['message'] = "Staff time edited successfully.";
                    }
                }
                if($response > 0) {
                    $ret_arr['status'] = 1;
                } else {
                    $ret_arr['status'] = 0;
                    $ret_arr['message'] = "Oops something went wrong.";
                }   
            }
            echo json_encode($ret_arr);
            exit;
        }

        public function new()
        {
            if(isset($this->userdata["id"])) {
                $data["staff"] = [];
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
                $data["staff_services"] = "";
                return view('admin/staff/add_edit',$data);
            }
        }

        public function create()
        {
            $session = session();
            if($session->get('userdata'))
            {
                $createdBy = $session->get('userdata');
                $post = $this->request->getVar();

                $roles = "";
                if(isset($post['roles']))
                {
                    $roles = implode(",",$post['roles']);
                }
                $post['roles'] = $roles;
                $is_all_service = "N";
                if(isset($post["all_service"])) {
                    $is_all_service = "Y";
                }
                $post["password"] = md5($post["password"]);
                $post['user_type'] = 1;
                $post['is_all_service'] = $is_all_service;
                $post['company_id'] = static_company_id();
                $post['created_by'] = $createdBy["id"];
                $post['updated_by'] = $createdBy["id"];
                $post['created_at'] = format_date(5);
                $post['updated_at'] = format_date(5);
                unset($post['is_all_selected']);
                unset($post['cpassword']);
                unset($post['selected_services']);

                $model = new Staff;
                $model->insert($post);
                if($model->getInsertID() > 0)
                {
                    $staff_id = $model->getInsertID();
                    if(isset($post["service_group"]) && $post["service_group"] != "")
                    {
                        $service_arr    = $post["service_group"];
                        $unique_arr     = array();
                        if(count($service_arr) > 0)
                            $unique_arr = array_unique($service_arr);

                        for($i = 0; $i < count($unique_arr); $i ++)
                        {
                            $service_param[] = array(
                                "staff_id"   => $staff_id,
                                "service_id" => "service_group_".$unique_arr[$i],
                                "company_id" => static_company_id(),
                                "created_by"   => $createdBy["id"],
                                "updated_by" => $createdBy["id"],
                                "created_at" => format_date(5),
                                "updated_at" => format_date(5)
                            );
                        }
                        $model = new StaffServiceModel;
                        $model->insertBatch($service_param);
                    }
                    if(isset($post["service"]) && $post["service"] != "")
                    {
                        $service_arr    = $post["service"];
                        $unique_arr     = array();
                        if(count($service_arr) > 0)
                            $unique_arr = array_unique($service_arr);

                        for($i = 0; $i < count($unique_arr); $i ++)
                        {
                            $service_param[] = array(
                                "staff_id"   => $staff_id,
                                "service_id" => $unique_arr[$i],
                                "company_id" => static_company_id(),
                                "created_by"   => $createdBy["id"],
                                "updated_by" => $createdBy["id"],
                                "created_at" => format_date(5),
                                "updated_at" => format_date(5)
                            );
                        }
                        $model = new StaffServiceModel;
                        $model->insertBatch($service_param);
                    }
                    $session->setFlashData('success','Staff added successfully');
                    $ret_arr = array("status" => 1);
                } else {
                    $ret_arr = array("status" => 0,"message" => "Oops something went wrong.");
                }
                echo json_encode($ret_arr);
                exit;
            }
        }

        public function edit($id = null)
        {   
            $session = session();
            if($session->get('userdata'))
            {
                $model = new Staff();
                $data['staff'] = $model->where('id',$id)->first();
                if($data['staff'])
                {
                    $data['document_title'] = "Edit Staff";

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
                $session->set('lastVisitUrl',base_url('sub_services'));
                return redirect()->route('admin');
            } 
        }

        public function update($id)
        {
            $session = session();
            if($session->get('userdata'))
            {
                $createdBy = $session->get('userdata');
                $post = $this->request->getVar();
                // $selected_services = $post['selected_services'];

                $roles = "";
                if(isset($post['roles']) && !empty($post['roles'])) {
                    $roles = implode(",",$post['roles']);
                }
                $post['roles'] = $roles;
                $is_all_service = "N";
                if(isset($post["all_service"])) {
                    $is_all_service = "Y";
                }
                $post['user_type'] = 1;
                $post['is_all_service'] = $is_all_service;
                $post['company_id'] = static_company_id();
                $post['updated_by'] = $createdBy["id"];
                $post['updated_at'] = format_date(5);
                unset($post['is_all_selected']);
                unset($post['cpassword']);
                // unset($post['selected_services']);

                $model = new Staff();
                $data = $model->update($id,$post);
                if($data)
                {
                    if(isset($post["service_group"]) && $post["service_group"] != "")
                    {
                        $model = new StaffServiceModel();
                        $model->where("staff_id",$id)->delete();

                        $service_arr    = $post["service_group"];
                        $unique_arr     = array();
                        if(count($service_arr) > 0)
                            $unique_arr = array_unique($service_arr);

                        for($i = 0; $i < count($unique_arr); $i ++)
                        {
                            $service_param[] = array(
                                "staff_id"   => $id,
                                "service_id" => "service_group_".$unique_arr[$i],
                                "company_id" => static_company_id(),
                                "created_by"   => $createdBy["id"],
                                "updated_by" => $createdBy["id"],
                                "created_at" => format_date(5),
                                "updated_at" => format_date(5)
                            );
                        }
                        $model = new StaffServiceModel;
                        $model->insertBatch($service_param);
                    }
                    if(isset($post["service"]) && $post["service"] != "")
                    {
                        $service_arr    = $post["service"];
                        $unique_arr     = array();
                        if(count($service_arr) > 0)
                            $unique_arr = array_unique($service_arr);

                        for($i = 0; $i < count($unique_arr); $i ++)
                        {
                            $service_param[] = array(
                                "staff_id"   => $id,
                                "service_id" => $unique_arr[$i],
                                "company_id" => static_company_id(),
                                "created_by" => $createdBy["id"],
                                "updated_by" => $createdBy["id"],
                                "created_at" => format_date(5),
                                "updated_at" => format_date(5)
                            );
                        }
                        $model = new StaffServiceModel;
                        $model->insertBatch($service_param);
                    }
                    $session->setFlashData('success','Staff edited successfully');
                    $ret_arr = array("status" => 1);
                } else {
                    $ret_arr = array("status" => 0,"message" => ERROR_MESSAGE);
                }
                echo json_encode($ret_arr);
                exit;
            }
        }

        public function delete($id)
        {
            $model = New Staff;
            $model->delete($id);

            $model = new StaffServiceModel();
            $model->where("staff_id",$id)->delete();

            $model = new StaffTimingModel();
            $model->where("staffId",$id)->delete();

            echo json_encode(array("status" => 200));
            exit;
        }

        public function get_weekly_time_report()
        {
            $data = $this->request->getVar();

            $db = db_connect();
            foreach($data["dates"] as $key => $val) {
                $timing = $db->table('staff_timings st');
                $timing->select("st.id,st.staffId,s.fname,s.lname,st.date,st.stime,st.etime");
                $timing->join("staffs s","st.staffId=s.id");
                $timing->where('st.companyId',static_company_id());
                $timing->where('st.date',$val["date"]);
                $timings = $timing->get()->getResultArray();
                if(!empty($timings)) {
                    $data["dates"][$key]["staffs"] = $timings;
                } else {
                    $data["dates"][$key]["staffs"] = [];
                }
            }
            return view('admin/staff/ajax_weekly_time_report',$data);
        }
    }
