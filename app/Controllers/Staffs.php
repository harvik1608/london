<?php
    namespace App\Controllers;

    use App\Models\ServiceModel;
    use App\Models\SubServiceModel;
    use App\Models\Staff;
    use App\Models\StaffServiceModel;
    use App\Models\StaffTimingModel;

    class Staffs extends BaseController
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
                if(check_permission("staffs")) {
                    $model = new Staff;
                    $data["staffs"] = $model->where("user_type",1)->where("is_deleted",0)->orderBy("id","desc")->get()->getResultArray();
                    return view('admin/staff/list',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function new()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("staffs")) {
                    $data["staff"] = [];
                    $model = new ServiceModel;
                    $data["service_groups"] = $model->select("id,name")->where(["is_active" => 1,"is_old_data" => 0,"is_deleted" => 0])->get()->getResultArray();
                    $model = new SubServiceModel;
                    if(!empty($data["service_groups"])) {
                        foreach($data["service_groups"] as $key => $val) {
                            $services = $model->select("id,name")->where(["is_deleted" => 0,"is_active" => '1',"service_group_id" => $val["id"],"is_old_data" => 0])->get()->getResultArray();
                            if(!empty($services)) {
                                $data["service_groups"][$key]["services"] = $services;
                            } else {
                                $data["service_groups"][$key]["services"] = [];
                            }
                        }
                    }
                    $data["staff_services"] = "";
                    return view('admin/staff/add_edit_copy',$data);
                } else {
                    return redirect("dashboard");
                }
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

                $designations = "";
                if(isset($post['designations']))
                {
                    $designations = implode(",",$post['designations']);
                }
                $post['designation'] = $designations;

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
                if(check_permission("staffs")) {
                    $model = new Staff();
                    $data['staff'] = $model->where('id',$id)->first();
                    if($data['staff'])
                    {
                        $data['document_title'] = "Edit Staff";

                        $model = new ServiceModel;
                        $data["service_groups"] = $model->select("id,name")->where(["is_active" => 1,"is_old_data" => 0,"is_deleted" => 0])->get()->getResultArray();
                        $model = new SubServiceModel;
                        if(!empty($data["service_groups"])) {
                            foreach($data["service_groups"] as $key => $val) {
                                $services = $model->select("id,name")->where(["is_deleted" => 0,"is_active" => '1',"service_group_id" => $val["id"],"is_old_data" => 0])->get()->getResultArray();
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
                        return view('admin/staff/add_edit_copy',$data);
                    } else { 
                        return redirect()->route('staffs');
                    }
                } else {
                    return redirect()->route('dashboard');
                }
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

                $designations = $session->get('userdata')["user_type"] == 0 ? "" : $post["old_designations"];
                if(isset($post['designations']))
                {
                    $designations = implode(",",$post['designations']);
                }
                $post['designation'] = $designations;

                $roles = $session->get('userdata')["user_type"] == 0 ? "" : $post["old_roles"];
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
            $model->update($id,["is_deleted" => 1,"updated_at" => date("Y-m-d H:i:s")]);

            // $model = new StaffServiceModel();
            // $model->where("staff_id",$id)->delete();

            // $model = new StaffTimingModel();
            // $model->where("staffId",$id)->delete();

            echo json_encode(array("status" => 200));
            exit;
        }
    }
