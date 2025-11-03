<?php
    namespace App\Controllers;

    use App\Models\Avatar;

    class Photos extends BaseController
    {
        protected $helpers = ["custom"];
        
        public function __construct()
        {
            $session = session();
            if($session->get('userdata')) {
                $this->userdata = $session->get('userdata');
            }
            $this->path = "public/uploads/gallery";
        }

        public function index()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("gallery")) {
                    $model = new Avatar;
                    $data["photos"] = $model->where("company_id",static_company_id())->orderBy("id","asc")->get()->getResultArray();
                    $data["img_path"] = $this->path;

                    return view('admin/gallery/list',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function new()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("gallery")) {
                    $data["gallery"] = [];
                    return view('admin/gallery/add_edit',$data);
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

                $post["name"] = "";
                if($_FILES['avatar']['name'] != "" && isset($_FILES['avatar']['name']))
                {
                    $img = $this->request->getFile('avatar');
                    $img->move($this->path,$img->getRandomName());
                    $post["name"] = $img->getName();
                }

                $model = new Avatar();
                $position = $model->select("position")->where("company_id",static_company_id())->orderBy("id","desc")->first();
                if($position)
                    $position_no = $position['position']+1;
                else 
                    $position_no = 1;

                $post['company_id'] = static_company_id();
                $post['position'] = $position_no;
                $post['created_by'] = $createdBy["id"];
                $post['updated_by'] = $createdBy["id"];
                $post['created_at'] = format_date(5);
                $post['updated_at'] = format_date(5);

                $model = new Avatar();
                $model->insert($post);
                if($model->getInsertID() > 0)
                {
                    $session->setFlashData('success','Photo added successfully');
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
                $model = new PaymentTypeModel();
                $data['payment_type'] = $model->where('id',$id)->first();
                if($data['payment_type']) {
                    return view('admin/payment_type/add_edit',$data);
                } else 
                    return redirect()->route('payment_types');
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

                $post['company_id'] = static_company_id();
                $post['updated_by'] = $createdBy["id"];
                $post['updated_at'] = format_date(5);

                $model = new PaymentTypeModel();
                $data = $model->update($id,$post);
                if($data)
                {
                    $session->setFlashData('success','Payment Type edited successfully');
                    $ret_arr = array("status" => 1);
                } else
                    $ret_arr = array("status" => 0,"message" => "Error");
                
                echo json_encode($ret_arr);
                exit;
            }
        }

        public function delete($id)
        {
            $model = New Avatar;
            $photo = $model->select("name")->where("id",$id)->first();
            $avatar = $photo["name"];
            if($model->delete($id)) {
                if($avatar != "" && file_exists($this->path."/".$avatar)) {
                    unlink($this->path."/".$avatar);
                }
            }
            echo json_encode(array("status" => 200));
            exit;
        }
    }
