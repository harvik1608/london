<?php
    namespace App\Controllers;

    use App\Models\WeekendDiscount;
    use App\Models\ServiceModel;
    use App\Models\SubServiceModel;

    class Discounts extends BaseController
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
                if(check_permission("weekend_discount")) {
                    $model = new WeekendDiscount;
                    $data["discounts"] = $model->where("company_id",static_company_id())->orderBy("id","desc")->get()->getResultArray();
                    return view('admin/discount/list',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function new()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("weekend_discount")) {
                    $data["discount"] = [];
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
                    return view('admin/discount/add_edit',$data);
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

                $post["week_days"] = "";
                if(isset($post["week_day"])) {
                    $post["week_days"] = implode(",", $post["week_day"]);
                }
                $is_all_service_checked = 0;
                if(isset($post["all_service"])) {
                    $is_all_service_checked = 1;
                }
                $service_group_ids = "";
                if(isset($post["service_group"]) && $post["service_group"] != "") {
                    $service_group_ids = implode(",",$post["service_group"]);
                }
                $service_ids = "";
                if(isset($post["service"]) && $post["service"] != "") {
                    $service_ids = implode(",",$post["service"]);
                }
                $post['company_id'] = static_company_id();
                $post['is_all_service_checked'] = $is_all_service_checked;
                $post['service_group_ids'] = $service_group_ids;
                $post['service_ids'] = $service_ids;
                $post["is_active"]  = $post["is_active"];
                $post['created_by'] = $createdBy["id"];
                $post['updated_by'] = $createdBy["id"];
                $post['created_at'] = format_date(5);
                $post['updated_at'] = format_date(5);

                $model = new WeekendDiscount();
                $model->insert($post);
                if($model->getInsertID() > 0)
                {
                    $session->setFlashData('success','Discount added successfully');
                    $ret_arr = array("status" => 1);
                } else {
                    $ret_arr = array("status" => 0,"message" => "Error");
                }
                echo json_encode($ret_arr);
                exit;
            }
        }

        public function edit($id)
        {
            $session = session();
            if($session->get('userdata'))
            {
                if(check_permission("weekend_discount")) {
                    $model = new WeekendDiscount();
                    $data['discount'] = $model->where('id',$id)->first();
                    if($data['discount']) {
                        $model = new ServiceModel;
                        $data["service_groups"] = $model->select("id,name")->where("is_active",'1')->get()->getResultArray();
                        if(!empty($data["service_groups"])) {
                            $model = new SubServiceModel;
                            foreach($data["service_groups"] as $key => $val) {
                                $services = $model->select("id,name")->where("service_group_id",$val["id"])->get()->getResultArray();
                                if(!empty($services)) {
                                    $data["service_groups"][$key]["services"] = $services;
                                } else {
                                    $data["service_groups"][$key]["services"] = [];
                                }
                            }
                        }
                        return view('admin/discount/add_edit',$data);
                    } else { 
                        return redirect()->route('discounts');
                    }
                } else {
                    return redirect()->route('dashboard');
                }
            } else {
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

                $is_all_service_checked = 0;
                if(isset($post["all_service"])) {
                    $is_all_service_checked = 1;
                }
                $service_group_ids = "";
                if(isset($post["service_group"]) && $post["service_group"] != "") {
                    $service_group_ids = implode(",",$post["service_group"]);
                }
                $service_ids = "";
                if(isset($post["service"]) && $post["service"] != "") {
                    $service_ids = implode(",",$post["service"]);
                }

                $post["week_days"] = "";
                if(isset($post["week_day"])) {
                    $post["week_days"] = implode(",", $post["week_day"]);
                }
                $post['is_all_service_checked'] = $is_all_service_checked;
                $post['service_group_ids'] = $service_group_ids;
                $post['service_ids'] = $service_ids;
                $post["is_active"]  = $post["is_active"];                
                $post['updated_by'] = $createdBy["id"];
                $post['updated_at'] = format_date(5);
                $model = new WeekendDiscount();
                $data = $model->update($id,$post);
                if($data)
                {
                    $session->setFlashData('success','Discount edited successfully');
                    $ret_arr = array("status" => 1);
                } else
                    $ret_arr = array("status" => 0,"message" => "Error");
                
                echo json_encode($ret_arr);
                exit;
            }
        }

        public function delete($id)
        {
            $model = New WeekendDiscount;
            $model->delete($id);

            echo json_encode(array("status" => 200));
            exit;
        }
    }
