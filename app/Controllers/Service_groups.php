<?php
    namespace App\Controllers;

    use App\Models\Service_group;
    use App\Models\SubServiceModel;
    use App\Models\CompanyModel;

    class Service_groups extends BaseController
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
                if(check_permission("groups")) {
                    $model = new Service_group;
                    $data["service_groups"] = $model->where('is_deleted',0)->where('is_old_data',0)->orderBy("id","desc")->get()->getResultArray();
                    return view('admin/service_group/list',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function new()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("groups")) {
                    $data["service_group"] = [];
                    return view('admin/service_group/add_edit',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function create()
        {
            $session = session();
            $post = $this->request->getVar();
            
            $avatar = "";
            if($_FILES['avatar']['name'] != "" && isset($_FILES['avatar']['name']))
            {
                $img = $this->request->getFile('avatar');
                $img->move($this->path,$img->getRandomName());
                $avatar = $img->getName();
            }

            $position = 1;
            $model = New Service_group;
            $last_position = $model->select("position")->orderBy("id","desc")->get()->getRowArray();
            if($last_position) {
                $position = $last_position["position"]+1;
            }

            $insert_data = array(
                "name" => $post["name"],
                "slug" => slug($post["name"]),
                "color" => $post["color"],
                "note" => $post["note"],
                "avatar" => $avatar,
                "position" => $position,
                "is_active" => $post["is_active"],
                "created_by" => $this->userdata["id"],
                "updated_by" => $this->userdata["id"],
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            );
            $model->save($insert_data);

            echo json_encode(array("status" => 1));
            exit;
        }

        public function edit($id)
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("groups")) {
                    $model = New Service_group;
                    $data["service_group"] = $model->where("id",$id)->where("is_deleted",0)->get()->getRowArray();
                    if($data["service_group"]) {
                        return view('admin/service_group/add_edit',$data);
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
            $post = $this->request->getVar();
            
            $avatar = $post["old_avatar"];
            if($_FILES['avatar']['name'] != "" && isset($_FILES['avatar']['name']))
            {
                $img = $this->request->getFile('avatar');
                $img->move($this->path,$img->getRandomName());
                $avatar = $img->getName();
                if($post['old_avatar'] != "" && file_exists($this->path."/".$post['old_avatar'])) {
                    unlink($this->path."/".$post["old_avatar"]);
                }
            }

            $model = New Service_group;
            $update_data = array(
                "name" => $post["name"],
                "slug" => slug($post["name"]),
                "color" => $post["color"],
                "note" => $post["note"],
                "avatar" => $avatar,
                "position" => $post["position"],
                "is_active" => $post["is_active"],
                "updated_by" => $this->userdata["id"],
                "updated_at" => date("Y-m-d H:i:s")
            );
            $model->update($id,$update_data);

            echo json_encode(array("status" => 1));
            exit;
        }

        public function delete($id)
        {
            $model = New Service_group;
            $model->update($id,array("is_deleted" => 1));

            echo json_encode(array("status" => 200));
            exit;
        }

        public function show($id)
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("groups")) {
                    $model = New Service_group;
                    $data["service_group"] = $model->where("id",$id)->where("is_deleted",0)->get()->getRowArray();
                    if($data["service_group"]) {
                        $orderByField = $this->fetch_salon_field();

                        $ids = [];
                        $model = new CompanyModel;
                        $services = $model->select("company_services")->where("id",static_company_id())->first();
                        if(!empty($services)) {
                            $ids = explode(",",$services["company_services"]);
                        }
                        $model = new SubServiceModel;
                        $builder = $model->select("id,name")->where(["service_group_id" => $id,"is_active" => '1',"is_deleted" => 0]);
                        if(!empty($ids)) {
                            $builder->whereIn('id', $ids);
                        }
                        $data["services"] = $builder->orderBy($orderByField,'asc')->get()->getResultArray();

                        return view('admin/service_group/view',$data);
                    } else {
                        return redirect("service_groups");
                    }
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function update_service_order()
        {
            $post = $this->request->getVar();

            $update_data = [];
            foreach($post["order"] as $key => $val) {
                if(static_company_id() == 1) {
                    $update_data[] = array(
                        'id' => $val,
                        "embellish_position" => ($key+1)
                    );
                } else if(static_company_id() == 2) {
                    $update_data[] = array(
                        'id' => $val,
                        "elm_position" => ($key+1)
                    );
                } else if(static_company_id() == 3) {
                    $update_data[] = array(
                        'id' => $val,
                        "elsa_position" => ($key+1)
                    );
                } else {
                    $update_data[] = array(
                        'id' => $val,
                        "embrace_position" => ($key+1)
                    );
                }
            }
            $db = \Config\Database::connect();
            $builder = $db->table('services');
            $builder->updateBatch($update_data, 'id');

            echo json_encode(["status" => 200,"message" => "Sequence changed successfully."]);
            exit;
        }

        public function fetch_salon_field()
        {
            switch(static_company_id()) {
                case 1:
                    $orderByField = "embellish_position";
                    break;

                case 2:
                    $orderByField = "elm_position";
                    break;

                case 3:
                    $orderByField = "elsa_position";
                    break;

                default:
                    $orderByField = "embrace_position";
                    break;
            }
            return $orderByField;
        }
    }
