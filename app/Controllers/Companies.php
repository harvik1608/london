<?php
    namespace App\Controllers;

    use App\Models\CompanyModel;
    use App\Models\ServiceModel;
    use App\Models\SubServiceModel;

    class Companies extends BaseController
    {
        protected $helpers = ["custom"];
        
        public function __construct()
        {
            $session = session();
            if($session->get('userdata')) {
                $this->userdata = $session->get('userdata');
            }
            $this->path = "public/uploads/company";
        }

        public function index()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("companies")) {
                    $model = new CompanyModel;
                    $data["companies"] = $model->orderBy("id","asc")->get()->getResultArray();
                    return view('admin/company/list',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function new()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("companies")) {
                    $data["company"] = [];

                    $model = new ServiceModel;
                    $data["service_groups"] = $model->select("id,name")->where(["is_active" => 1,"is_deleted" => 0,"is_old_data" => 0])->get()->getResultArray();
                    $model = new SubServiceModel;
                    if(!empty($data["service_groups"])) {
                        foreach($data["service_groups"] as $key => $val) {
                            $services = $model->select("id,name")->where(["is_old_data" => 0,"is_deleted" => 0,"service_group_id" => $val["id"]])->orderBy("name","asc")->get()->getResultArray();
                            if(!empty($services)) {
                                $data["service_groups"][$key]["services"] = $services;
                            } else {
                                $data["service_groups"][$key]["services"] = [];
                            }
                        }
                    }
                    return view('admin/company/add_edit',$data);
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

                $company_logo = "";
                if($_FILES['company_logo']['name'] != "" && isset($_FILES['company_logo']['name']))
                {
                    $img = $this->request->getFile('company_logo');
                    $img->move($this->path,$img->getRandomName());
                    $company_logo = $img->getName();
                }
                $banner = "";
                if($_FILES['banner']['name'] != "" && isset($_FILES['banner']['name']))
                {
                    $img = $this->request->getFile('banner');
                    $img->move($this->path,$img->getRandomName());
                    $banner = $img->getName();
                }
                $company_service_groups = "";
                if(isset($post["service_group"]) && !empty($post["service_group"])) {
                    $company_service_groups = implode(",",$post["service_group"]);
                }
                $company_services = "";
                if(isset($post["service"]) && !empty($post["service"])) {
                    $company_services = implode(",",$post["service"]);
                }
                $post["company_service_groups"] = $company_service_groups;
                $post["company_services"] = $company_services;
                $post["company_logo"] = $company_logo;
                $post["banner"] = $banner;
                $post["company_currency"] = $post["currency"];
                $post['createdBy'] = $createdBy["id"];
                $post['updatedBy'] = $createdBy["id"];
                $post['createdAt'] = format_date(5);
                $post['updatedAt'] = format_date(5);

                $model = new CompanyModel();
                $model->insert($post);
                if($model->getInsertID() > 0)
                {
                    $session->setFlashData('success','Customer added successfully');
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
                if(check_permission("companies")) {
                    $model = new CompanyModel();
                    $data['company'] = $model->where('id',$id)->first();
                    if($data['company']) {
                        $model = new ServiceModel;
                        $data["service_groups"] = $model->select("id,name")->where(["is_active" => 1,"is_deleted" => 0,"is_old_data" => 0])->get()->getResultArray();
                        $model = new SubServiceModel;
                        if(!empty($data["service_groups"])) {
                            foreach($data["service_groups"] as $key => $val) {
                                $services = $model->select("id,name")->where(["is_old_data" => 0,"is_deleted" => 0,"service_group_id" => $val["id"]])->orderBy("name","asc")->get()->getResultArray();
                                if(!empty($services)) {
                                    $data["service_groups"][$key]["services"] = $services;
                                } else {
                                    $data["service_groups"][$key]["services"] = [];
                                }
                            }
                        }
                        return view('admin/company/add_edit',$data);
                    } else { 
                        return redirect()->route('customers');
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

                $company_logo = $post["old_company_photo"];
                if($_FILES['company_logo']['name'] != "" && isset($_FILES['company_logo']['name']))
                {
                    $img = $this->request->getFile('company_logo');
                    $img->move($this->path,$img->getRandomName());
                    $company_logo = $img->getName();
                }
                $banner = $post["old_banner"];
                if($_FILES['banner']['name'] != "" && isset($_FILES['banner']['name']))
                {
                    $img = $this->request->getFile('banner');
                    $img->move($this->path,$img->getRandomName());
                    $banner = $img->getName();
                }
                $company_service_groups = "";
                if(isset($post["service_group"]) && !empty($post["service_group"])) {
                    $company_service_groups = implode(",",$post["service_group"]);
                }
                $company_services = "";
                if(isset($post["service"]) && !empty($post["service"])) {
                    $company_services = implode(",",$post["service"]);
                }
                $uploadedFiles = [];
                $model = new CompanyModel();
                $old_banners = $model->select("banners")->where("id",$id)->first();
                if($old_banners && $old_banners["banners"] != "" && !is_null($old_banners["banners"])) {
                    $old_banner_photos = json_decode($old_banners["banners"],true);
                    $uploadedFiles = array_merge($uploadedFiles, $old_banner_photos);
                }
                $files = $this->request->getFiles();
                if ($files) {
                    foreach ($files['banners'] as $file) {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $newName = $file->getRandomName(); // unique file name
                            $file->move(FCPATH . 'public/uploads/company/embellish/', $newName);
                            $uploadedFiles[] = [
                                "id"     => time(),
                                "avatar" => "uploads/company/embellish/" . $newName
                            ];
                        }
                    }
                }
                if(empty($uploadedFiles)) {
                    $uploadedFiles = "";
                } else {
                    $uploadedFiles = json_encode($uploadedFiles);
                }
                $post["company_service_groups"] = $company_service_groups;
                $post["company_services"] = $company_services;
                $post["company_logo"] = $company_logo;
                $post["banner"] = $banner;
                $post["banners"] = $uploadedFiles;
                $post["company_currency"] = $post["currency"];
                $post['updatedBy'] = $createdBy["id"];
                $post['updatedAt'] = format_date(5);

                $model = new CompanyModel();
                $model->update($id,$post);
                $session->setFlashData('success','Customer added successfully');
                $ret_arr = array("status" => 1);
                
                echo json_encode($ret_arr);
                exit;
            }
        }
    }
