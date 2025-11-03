<?php
    namespace App\Controllers;

    use App\Models\Service_group;

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
    }
