<?php
    namespace App\Controllers;

    use App\Models\ServiceModel;
    use App\Models\SubServiceModel;
    use App\Models\ServicePriceModel;

    class Services extends BaseController
    {
        protected $helpers = ["custom"];
        
        public function __construct()
        {
            $session = session();
            if($session->get('userdata')) {
                $this->userdata = $session->get('userdata');
            }
            $this->path = "public/uploads/service_group";
        }

        public function index()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("sub_services")) {
                    $db = db_connect();
                    $service = $db->table('services ss');
                    $service->select("ss.id,ss.name,s.name AS service_name,ss.price_type,ss.is_active");
                    $service->join("service_groups s","ss.service_group_id=s.id");
                    $service->where('ss.is_deleted',0);
                    $service->where('s.is_deleted',0);
                    $service->where('s.is_old_data',0);
                    $service->orderBy("ss.id","desc");
                    $data["services"] = $service->get()->getResultArray();

                    $model = new ServiceModel;
                    $data["service_groups"] = $model->select("id,name")->where("is_active",'1')->where('is_old_data',0)->where('is_deleted',0)->get()->getResultArray();

                    $data["service_group_id"] = 0;
                    return view('admin/service/list',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function show($id)
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("sub_services")) {
                    $db = db_connect();
                    $service = $db->table('services ss');
                    $service->select("ss.id,ss.name,s.name AS service_name,ss.price_type,ss.is_active");
                    $service->join("service_groups s","ss.service_group_id=s.id");
                    $service->where('ss.service_group_id',$id);
                    $service->where('ss.is_deleted',0);
                    $service->where('s.is_deleted',0);
                    $service->orderBy("ss.id","desc");
                    $data["services"] = $service->get()->getResultArray();

                    $model = new ServiceModel;
                    $data["service_groups"] = $model->select("id,name")->where("is_active",'1')->where('is_deleted',0)->get()->getResultArray();

                    $data["service_group_id"] = $id;
                    
                    return view('admin/service/list',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function new()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("sub_services")) {
                    $data["service"] = [];
                    $model = new ServiceModel;
                    $data["service_groups"] = $model->select("id,name")->where("is_active",'1')->where('is_old_data',0)->where('is_deleted',0)->get()->getResultArray();
                    return view('admin/service/add_edit_master',$data);
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
                $model = new SubServiceModel();
                $position = $model->get()->getNumRows();
                $createdBy = $session->get('userdata');

                $post = $this->request->getVar();

                $json = array();
                if(isset($post['price_duration'])) {
                    for($i = 0; $i < count($post['price_duration']); $i ++)
                    {
                        if($post['price_duration'][$i] != "") {
                            $json[] = array(
                                "id" => (string) ($i+1),
                                "duration" => $post['price_duration'][$i],
                                "retail_price" => $post['rprice'][$i],
                                "special_price" => $post['sprice'][$i],
                                "caption" => $post['caption'][$i]
                            );
                        }
                    }
                }
                if(!empty($json)) {
                    $jsondata = json_encode($json);
                } else {
                    $jsondata = "";
                }
                unset($post['price_duration']);
                unset($post['rprice']);
                unset($post['sprice']);
                unset($post['caption']);
                $post['company_id'] = static_company_id();
                $post['position']  = $position+1;
                $post['json'] = $jsondata;
                $post['created_by'] = $createdBy['id'];
                $post['updated_by'] = $createdBy['id'];
                $post['created_at'] = format_date(5);
                $post['updated_at'] = format_date(5);

                $model = new SubServiceModel();
                $model->insert(["service_group_id" => $post["service_group_id"],"name" => $post["name"],"created_by" => $createdBy['id'],"created_at" => format_date(5)]);
                if($model->getInsertID() > 0)
                {
                    $session->setFlashData('success','Sub Service added successfully');
                    $ret_arr = array("status" => 1);
                } else {
                    $ret_arr = array("status" => 0,"message" => "Oops something went wrong.");
                }
                echo json_encode($ret_arr);
                exit;
            }
        }

        public function edit($id)
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("sub_services")) {
                    $model = New SubServiceModel;
                    $data["service"] = $model->where("id",$id)->get()->getRowArray();
                    if($data["service"]) {
                        $model = new ServiceModel;
                        $data["service_groups"] = $model->select("id,name")->where("is_active",'1')->where('is_old_data',0)->where('is_deleted',0)->get()->getResultArray();
                        return view('admin/service/add_edit_master',$data);
                    } else {
                        return redirect("service_groups");
                    }
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function update($id)
        {
            $session = session();
            if($session->get('userdata'))
            {
                $createdBy = $session->get('userdata');

                $post = $this->request->getVar();
                $json = [];
                if(isset($post['price_duration'])) {
                    for($i = 0; $i < count($post['price_duration']); $i ++) {
                        $json[] = array(
                            "id" => (string) ($i+1),
                            "duration" => $post['price_duration'][$i],
                            "retail_price" => $post['rprice'][$i],
                            "special_price" => $post['sprice'][$i],
                            "caption" => $post['caption'][$i]
                        );
                    }
                }
                unset($post['price_duration']);
                unset($post['rprice']);
                unset($post['sprice']);
                unset($post['caption']);
                $post['company_id'] = static_company_id();
                $post['json'] = empty($json) ? "" : json_encode($json);
                $post['updated_by'] = $createdBy['id'];
                $post['updated_at'] = format_date(5);

                $model = new SubServiceModel();
                $data = $model->update($id,["service_group_id" => $post["service_group_id"],"name" => $post["name"],"updated_by" => $createdBy['id'],"updated_at" => format_date(5)]);
                if($data)
                {
                    $session->setFlashData('success','Sub Service edited successfully');
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
            $model = New SubServiceModel;
            $model->update($id,['is_deleted' => 1]);

            echo json_encode(array("status" => 200));
            exit;
        }

        // For pricing 
        public function add_service_price($id)
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("sub_services")) {
                    if(isset($id) && $id != "") {
                        $model = new SubServiceModel();
                        $__row = $model->where("id",$id)->first();
                        if($__row) {
                            $db = db_connect();
                            $service = $db->table('service_prices sp');
                            $service->select("sp.*");
                            $service->join("services s","sp.service_id=s.id");
                            $service->where('sp.service_id',$id);
                            $service->where('sp.company_id',static_company_id());
                            $service->orderBy("sp.id","desc");
                            $data["price"] = $service->get()->getRowArray();
                            
                            $data["service_id"] = $id;
                            $data["service_name"] = $__row["name"];

                            return view('admin/service/price_list',$data);
                        } else {
                            return redirect("services");
                        }
                    }
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function new_service_price($id)
        {
            $post = $this->request->getVar();
            $json = array();
            if(isset($post['price_duration'])) {
                for($i = 0; $i < count($post['price_duration']); $i ++) {
                    if($post['price_duration'][$i] != "") {
                        $json[] = array(
                            "id" => (string) ($i+1),
                            "duration" => $post['price_duration'][$i],
                            "retail_price" => $post['rprice'][$i],
                            "special_price" => $post['sprice'][$i],
                            "caption" => $post['caption'][$i]
                        );
                    }
                }
            }
            if(!empty($json)) {
                $jsondata = json_encode($json);
            } else {
                $jsondata = "";
            }
            $model = new ServicePriceModel;
            $check = $model->select("id")->where("service_id",$id)->where("company_id",static_company_id())->first();
            if($check) {
                $update_data = array(
                    "service_id" => $id,
                    "price_type" => $post["price_type"],
                    "extra_time_type" => $post["extra_time_type"],
                    "duration" => $post["duration"],
                    "bookedFrom" => $post["bookedFrom"],
                    "note" => $post["note"],
                    "json" => $jsondata,
                    "company_id" => static_company_id()
                );
                $model->update($check["id"],$update_data);
            } else {
                $insert_data = array(
                    "service_id" => $id,
                    "price_type" => $post["price_type"],
                    "extra_time_type" => $post["extra_time_type"],
                    "duration" => $post["duration"],
                    "bookedFrom" => $post["bookedFrom"],
                    "note" => $post["note"],
                    "json" => $jsondata,
                    "company_id" => static_company_id()
                );
                $model->insert($insert_data);
            }
            $model = new SubServiceModel();
            $group = $model->select("service_group_id")->where("id",$id)->first();
            header("Location: ".base_url("services/".$group["service_group_id"]));
        }   
    }
