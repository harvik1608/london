<?php
    namespace App\Controllers;

    use App\Models\DiscountTypeModel;

    class Discount_types extends BaseController
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
                if(check_permission("discount_types")) {
                    $model = new DiscountTypeModel;
                    $data["discount_types"] = $model->where("company_id",static_company_id())->where("is_deleted",0)->orderBy("id","desc")->get()->getResultArray();
                    return view('admin/discount_type/list',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function new()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("discount_types")) {
                    $data["discount_type"] = [];
                    return view('admin/discount_type/add_edit',$data);
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

                $model = new DiscountTypeModel();
                $position = $model->select("position")->where("company_id",static_company_id())->orderBy("id","desc")->first();
                if($position)
                    $position_no = $position['position']+1;
                else 
                    $position_no = 0;

                $post['company_id'] = static_company_id();
                $post['position'] = $position_no;
                $post['created_by'] = $createdBy["id"];
                $post['updated_by'] = $createdBy["id"];
                $post['created_at'] = format_date(5);
                $post['updated_at'] = format_date(5);

                $model = new DiscountTypeModel();
                $model->insert($post);
                if($model->getInsertID() > 0)
                {
                    $session->setFlashData('success','Discount Type added successfully');
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
                if(check_permission("discount_types")) {
                    $model = new DiscountTypeModel();
                    $data['discount_type'] = $model->where('id',$id)->first();
                    if($data['discount_type']) {
                        return view('admin/discount_type/add_edit',$data);
                    } else {
                        return redirect()->route('discount_types');
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

                $post['company_id'] = static_company_id();
                $post['updated_by'] = $createdBy["id"];
                $post['updated_at'] = format_date(5);

                $model = new DiscountTypeModel();
                $data = $model->update($id,$post);
                if($data)
                {
                    $session->setFlashData('success','Discount Type edited successfully');
                    $ret_arr = array("status" => 1);
                } else
                    $ret_arr = array("status" => 0,"message" => "Error");
                
                echo json_encode($ret_arr);
                exit;
            }
        }

        public function delete($id)
        {
            $model = New DiscountTypeModel;
            $model->update($id,["is_deleted" => 1]);

            echo json_encode(array("status" => 200));
            exit;
        }
    }
