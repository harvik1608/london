<?php 
    namespace App\Controllers;

    use CodeIgniter\RESTful\ResourceController;
    use CodeIgniter\API\ResponseTrait;
    use App\Models\CompanyModel;
    use App\Models\ServiceModel;
    use App\Models\Service_group;
    use App\Models\SubServiceModel;
    use App\Models\PaymentTypeModel;
    use App\Models\ServicePriceModel;
    use App\Models\WebsiteEntry;
    use App\Models\StaffTimingModel;

    class Api_salon extends ResourceController
    {
        use ResponseTrait;
        protected $helpers = ["custom"];

        public function all_services()
        {
            $post = $this->request->getVar();
            $input_parameter = ["user_id","salon_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new Service_group;
                $service_groups = $model->select("id,name,avatar")->where("is_deleted",0)->where("is_active",1)->orderBy("position","asc")->get()->getResultArray();
                if($service_groups) {
                    $model = new SubServiceModel;
                    foreach ($service_groups as $key => $val) {
                        if($val["avatar"] != "") {
                            $service_groups[$key]["avatar"] = base_url("public/uploads/service_group/".$val["avatar"]);
                        }
                        $services = $model->select("id,name")->where(["service_group_id" => $val["id"],"is_deleted" => 0])->get()->getResultArray();
                        if($services) {
                            $service_groups[$key]["items"] = $services;
                        } else {
                            $service_groups[$key]["items"] = [];
                        }
                    }
                    $model = new ServicePriceModel;
                    foreach ($service_groups as $key => $val) {
                        if(!empty($val["items"])) {
                            foreach ($val["items"] as $k => $v) {
                                $price = $model->where("company_id",$post["salon_id"])->where("service_id",$v["id"])->first();
                                if($price) {
                                    $price["jsondata"] = json_decode($price["json"]);
                                } else {
                                    $price["jsondata"] = [];
                                }
                                unset($price["json"]);
                                $val["items"][$k]["price"] = $price;
                            }
                            $service_groups[$key]["items"] = $val["items"];
                        }
                    }
                }
                $data = $service_groups;
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "";
                $response[RESPONSE_DATA] = $data;
                return $this->respond($response);
            }
        }

        public function salons()
        {
            $post = $this->request->getVar();
            $input_parameter = ["page"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $page = $post['page'] ?? 1;
                $limit = $post['limit'] ?? LIMIT;
                $offset = ($page - 1) * $limit;

                $model = new CompanyModel;
                $totalRecords = $model->countAllResults();
                $totalPages = ceil($totalRecords/$limit);

                $salons = $model->limit($limit, $offset)->orderBy("id","desc")->get()->getResultArray();
                if($salons) {
                    foreach ($salons as $key => $val) {
                        $salons[$key]["company_logo"] = base_url("public/uploads/company/".$val["company_logo"]);
                        $salons[$key]["banner"] = base_url("public/uploads/company/".$val["banner"]);
                        $salons[$key]["credential_file"] = is_null($val["credential_file"]) ? "" : $val["credential_file"];
                        $salons[$key]["google_code"] = is_null($val["google_code"]) ? "" : $val["google_code"];
                    }
                }
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Total ".$totalRecords." salon(s) found.";
                $response[TOTAL_COUNT] = $totalRecords;
                $response[CURRENT_PAGE] = (int) $page;
                if((int) $page == $totalPages) {
                    $response[NEXT_PAGE] = 0;
                } else {
                    $response[NEXT_PAGE] = (int) $page + 1;
                }
                $response[TOTAL_PAGE] = $totalPages;
                $response[LIMIT_WORD] = (int) $limit;
                $response[RESPONSE_DATA] = $salons;
                return $this->respond($response);
            }
        }

        public function create_salon()
        {
            $post = $this->request->getVar();
            $input_parameter = ["company_name","company_email","company_phone","company_address","company_stime","company_etime","company_sunday_stime","company_sunday_etime","user_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $is_salon_exist = $model->where("company_name",$post["company_name"])->get()->getNumRows();
                if($is_salon_exist == 0) {
                    $company_logo = "";
                    if(isset($_FILES['company_logo']['name'])) {
                        if($_FILES['company_logo']['name'] != "") {
                            $img = $this->request->getFile('company_logo');
                            $img->move("public/uploads/company",$img->getRandomName());
                            $company_logo = $img->getName();
                        }
                    }
                    $banner = "";
                    if(isset($_FILES['banner']['name'])) {
                        if($_FILES['banner']['name'] != "") {
                            $img = $this->request->getFile('banner');
                            $img->move("public/uploads/company",$img->getRandomName());
                            $banner = $img->getName();
                        }
                    }
                    $company_service_groups = "";
                    $company_services = "";
                    if(isset($post["is_all_service_checked"]) && $post["is_all_service_checked"] == 1) {
                        $model = new Service_group;
                        $service_groups = $model->select("id")->where(["is_deleted" => 0,"is_active" => 1])->get()->getResultArray();
                        $company_service_groups = implode(",", array_column($service_groups, "id"));

                        $model = new SubServiceModel;
                        $services = $model->select("id")->where(["is_deleted" => 0,"is_active" => '1'])->get()->getResultArray();
                        $company_services = implode(",", array_column($services, "id"));
                    } else {
                        if(isset($post["company_service_groups"]) && $post["company_service_groups"] != "") {
                            $company_service_groups = $post["company_service_groups"];
                        }
                        if(isset($post["company_services"]) && $post["company_services"] != "") {
                            $company_services = $post["company_services"];
                        }
                    }
                    $insert_data = array(
                        "company_name" => $post["company_name"],
                        "company_email" => $post["company_email"],
                        "company_phone" => $post["company_phone"],
                        "company_desc" => check_null_blank($post["company_desc"]),
                        "company_address" => $post["company_address"],
                        "company_logo" => $company_logo,
                        "banner" => $banner,
                        "isActive" => check_null_blank($post["isActive"],'1'),
                        "currency" => check_null_blank($post["currency"],"£"),
                        "company_stime" => $post["company_stime"],
                        "company_etime" => $post["company_etime"],
                        "company_sunday_stime" => $post["company_sunday_stime"],
                        "company_sunday_etime" => $post["company_sunday_etime"],
                        "about_company" => check_null_blank($post["about_company"]),
                        "smtp_email" => check_null_blank($post["smtp_email"]),
                        "smtp_password" => check_null_blank($post["smtp_password"]),
                        "smtp_host" => check_null_blank($post["smtp_host"]),
                        "smtp_port" => check_null_blank($post["smtp_port"]),
                        "from_email" => check_null_blank($post["from_email"]),
                        "from_name" => check_null_blank($post["from_name"]),
                        "website_url" => check_null_blank($post["website_url"]),
                        "code" => check_null_blank($post["code"],"#000000"),
                        "google_contact" => check_null_blank($post["google_contact"],"0"),
                        "google_calendar" => check_null_blank($post["google_calendar"],"0"),
                        "json" => check_null_blank($post["json"]),
                        "facebook_link" => check_null_blank($post["facebook_link"]),
                        "google_link" => check_null_blank($post["google_link"]),
                        "instagram_link" => check_null_blank($post["instagram_link"]),
                        "company_currency" => check_null_blank($post["currency"],"£"),
                        "company_service_groups" => $company_service_groups,
                        "company_services" => $company_services,
                        "timezone" => check_null_blank($post["timezone"],"Europe/London"),
                        "privacy_policy" => check_null_blank($post["privacy_policy"]),
                        "parking_instructions" => check_null_blank($post["parking_instructions"]),
                        "credential_file" => check_null_blank($post["credential_file"]),
                        "google_code" => check_null_blank($post["google_code"]),
                        "is_all_service_checked" => isset($post["is_all_service_checked"]) ? $post["is_all_service_checked"] : "0",
                        "createdBy" => $post["user_id"],
                        "updatedBy" => 0,
                        "createdAt" => strtotime(date("Y-m-d H:i:s")),
                        "updatedAt" => strtotime(date("Y-m-d H:i:s"))
                    );
                    if($model->insert($insert_data)) {
                        $salon_id = $model->getInsertID();
                        $salon = $model->where("id",$salon_id)->first();
                        $salon["company_logo"] = base_url("public/uploads/company/".$salon["company_logo"]);
                        $salon["banner"] = base_url("public/uploads/company/".$salon["banner"]);

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Salon added successfully.";
                        $response[RESPONSE_DATA] = $salon;
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                        $response[RESPONSE_DATA] = (object) array();    
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon already added.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function view_salon()
        {
            $post = $this->request->getVar();
            $input_parameter = ["salon_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->where("id",$post["salon_id"])->first();
                if($salon) {
                    $salon["company_logo"] = base_url("public/uploads/company/".$salon["company_logo"]);
                    $salon["banner"] = base_url("public/uploads/company/".$salon["banner"]);
                    
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Salon found.";
                    $response[RESPONSE_DATA] = $salon;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function update_salon()
        {
            $post = $this->request->getVar();
            $input_parameter = ["company_name","company_email","company_phone","company_address","company_stime","company_etime","company_sunday_stime","company_sunday_etime","user_id","salon_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $salon = $model->where("id",$post["salon_id"])->first();
                if($salon) {
                    $is_salon_exist = $model->where("company_name",$post["company_name"])->where("id !=",$post["salon_id"])->get()->getNumRows();
                    if($is_salon_exist == 0) {
                        $company_logo = $salon["company_logo"];
                        if(isset($_FILES['company_logo']['name'])) {
                            if($_FILES['company_logo']['name'] != "") {
                                $img = $this->request->getFile('company_logo');
                                $img->move("public/uploads/company",$img->getRandomName());
                                $company_logo = $img->getName();
                                if($salon["company_logo"] != "" && file_exists("public/uploads/company/".$salon["company_logo"])) {
                                    unlink("public/uploads/company/".$salon["company_logo"]);
                                }
                            }
                        }
                        $banner = $salon["banner"];
                        if(isset($_FILES['banner']['name'])) {
                            if($_FILES['banner']['name'] != "") {
                                $img = $this->request->getFile('banner');
                                $img->move("public/uploads/company",$img->getRandomName());
                                $banner = $img->getName();
                                if($salon["banner"] != "" && file_exists("public/uploads/company/".$salon["banner"])) {
                                    unlink("public/uploads/company/".$salon["banner"]);
                                }
                            }
                        }
                        $company_service_groups = $salon["company_service_groups"];
                        $company_services = $salon["company_services"];
                        if(isset($post["is_all_service_checked"]) && $post["is_all_service_checked"] == 1) {
                            $model = new Service_group;
                            $service_groups = $model->select("id")->where(["is_deleted" => 0,"is_active" => 1])->get()->getResultArray();
                            $company_service_groups = implode(",", array_column($service_groups, "id"));

                            $model = new SubServiceModel;
                            $services = $model->select("id")->where(["is_deleted" => 0,"is_active" => '1'])->get()->getResultArray();
                            $company_services = implode(",", array_column($services, "id"));
                        } else {
                            if(isset($post["company_service_groups"]) && $post["company_service_groups"] != "") {
                                $company_service_groups = $post["company_service_groups"];
                            }
                            if(isset($post["company_services"]) && $post["company_services"] != "") {
                                $company_services = $post["company_services"];
                            }
                        }
                        $update_data = array(
                            "company_name" => $post["company_name"],
                            "company_email" => $post["company_email"],
                            "company_phone" => $post["company_phone"],
                            "company_desc" => check_null_blank($post["company_desc"]),
                            "company_address" => $post["company_address"],
                            "company_logo" => $company_logo,
                            "banner" => $banner,
                            "isActive" => check_null_blank($post["isActive"],'1'),
                            "currency" => check_null_blank($post["currency"],"£"),
                            "company_stime" => $post["company_stime"],
                            "company_etime" => $post["company_etime"],
                            "company_sunday_stime" => $post["company_sunday_stime"],
                            "company_sunday_etime" => $post["company_sunday_etime"],
                            "about_company" => check_null_blank($post["about_company"]),
                            "smtp_email" => check_null_blank($post["smtp_email"]),
                            "smtp_password" => check_null_blank($post["smtp_password"]),
                            "smtp_host" => check_null_blank($post["smtp_host"]),
                            "smtp_port" => check_null_blank($post["smtp_port"]),
                            "from_email" => check_null_blank($post["from_email"]),
                            "from_name" => check_null_blank($post["from_name"]),
                            "website_url" => check_null_blank($post["website_url"]),
                            "code" => check_null_blank($post["code"],"#000000"),
                            "google_contact" => check_null_blank($post["google_contact"],"0"),
                            "google_calendar" => check_null_blank($post["google_calendar"],"0"),
                            "json" => check_null_blank($post["json"]),
                            "facebook_link" => check_null_blank($post["facebook_link"]),
                            "google_link" => check_null_blank($post["google_link"]),
                            "instagram_link" => check_null_blank($post["instagram_link"]),
                            "company_currency" => check_null_blank($post["currency"],"£"),
                            "company_service_groups" => $company_service_groups,
                            "company_services" => $company_services,
                            "timezone" => check_null_blank($post["timezone"],"Europe/London"),
                            "privacy_policy" => check_null_blank($post["privacy_policy"]),
                            "parking_instructions" => check_null_blank($post["parking_instructions"]),
                            "credential_file" => check_null_blank($post["credential_file"]),
                            "google_code" => check_null_blank($post["google_code"]),
                            "is_all_service_checked" => isset($post["is_all_service_checked"]) ? $post["is_all_service_checked"] : "0",
                            "updatedBy" => $post["user_id"],
                            "updatedAt" => strtotime(date("Y-m-d H:i:s"))
                        );
                        $model = new CompanyModel;
                        if($model->update($post["salon_id"],$update_data)) {
                            $salon = $model->where("id",$post["salon_id"])->first();
                            $salon["company_logo"] = base_url("public/uploads/company/".$salon["company_logo"]);
                            $salon["banner"] = base_url("public/uploads/company/".$salon["banner"]);

                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "Salon updated successfully.";
                            $response[RESPONSE_DATA] = $salon;
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                            $response[RESPONSE_DATA] = (object) array();    
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Salon already added.";
                        $response[RESPONSE_DATA] = (object) array();
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function service_groups()
        {
            $post = $this->request->getVar();
            $input_parameter = ['user_id'];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $page = $post['page'] ?? 1;
                $limit = $post['limit'] ?? LIMIT;
                $offset = ($page - 1) * $limit;

                $model = new Service_group;
                $totalRecords = $model->where("is_deleted", 0)->countAllResults();
                $totalPages = ceil($totalRecords/$limit);

                $service_groups = $model->where("is_deleted", 0)->limit($limit, $offset)->orderBy("id","desc")->get()->getResultArray();
                if($service_groups) {
                    foreach($service_groups as $key => $val) {
                        if($val["avatar"] != "" && file_exists("public/uploads/service_group/".$val["avatar"])) {
                            $service_groups[$key]["avatar"] = base_url("public/uploads/service_group/".$val["avatar"]);
                        }
                    }
                }
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Total ".$totalRecords." service group(s) found.";
                $response[TOTAL_COUNT] = $totalRecords;
                $response[CURRENT_PAGE] = (int) $page;
                if((int) $page == $totalPages) {
                    $response[NEXT_PAGE] = 0;
                } else {
                    $response[NEXT_PAGE] = (int) $page + 1;
                }
                $response[TOTAL_PAGE] = $totalPages;
                $response[LIMIT_WORD] = (int) $limit;
                $response[RESPONSE_DATA] = $service_groups;
                return $this->respond($response);
            }
        }

        public function create_service_group()
        {
            $post = $this->request->getVar();
            $input_parameter = ["user_id","name","is_active"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new Service_group;
                $count = $model->where("name",$post["name"])->where("is_deleted",0)->get()->getNumRows();
                if($count == 0) {
                    $avatar = "";
                    if(isset($_FILES['avatar'])) {
                        if($_FILES['avatar']['name'] != "" && isset($_FILES['avatar']['name'])) {
                            $img = $this->request->getFile('avatar');
                            $img->move("public/uploads/service_group",$img->getRandomName());
                            $avatar = $img->getName();
                        }
                    }
                    $position = 1;
                    $model = New Service_group;
                    $last_position = $model->select("position")->orderBy("id","desc")->get()->getRowArray();
                    if($last_position) {
                        $position = $last_position["position"]+1;
                    }
                    $color = "#000000";
                    if(isset($post["color"])) {
                        $color = check_null_blank($post["color"],"#000000");
                    }
                    $note = "";
                    if(isset($post["note"])) {
                        $note = check_null_blank($post["note"]);
                    }
                    $is_active = "1";
                    if(isset($post["is_active"])) {
                        $is_active = check_null_blank($post["is_active"],"1");
                    }
                    $company_id = "1";
                    if(isset($post["company_id"])) {
                        $company_id = check_null_blank($post["company_id"],0);
                    }
                    $insert_data = array(
                        "name" => $post["name"],
                        "color" => $color,
                        "note" => $note,
                        "is_active" => $is_active,
                        "avatar" => $avatar,
                        "position" => $position,
                        "company_id" => $company_id,
                        "createdBy" => $post["user_id"],
                        "updatedBy" => 0,
                        "created_at" => strtotime(date("Y-m-d H:i:s")),
                        "updated_at" => strtotime(date("Y-m-d H:i:s"))
                    );
                    if($model->insert($insert_data)) {
                        $service_group_id = $model->getInsertID();
                        $service_group = $model->where("id",$service_group_id)->first();
                        if($service_group["avatar"] != "" && file_exists("public/uploads/service_group/".$service_group["avatar"])) {
                            $service_group["avatar"] = base_url("public/uploads/service_group/".$service_group["avatar"]);
                        }
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Service group added successfully.";
                        $response[RESPONSE_DATA] = $service_group;
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                        $response[RESPONSE_DATA] = (object) array();    
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Service group already added.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function delete_service_group()
        {
            $post = $this->request->getVar();
            $input_parameter = ["service_group_id","user_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new Service_group;
                $service_group = $model->where("id",$post["service_group_id"])->first();
                if($service_group) {
                    if($service_group["is_deleted"] == 0) {
                        $model->update($post["service_group_id"],["is_deleted" => 1,"updated_by" => $post["user_id"],"updated_at" => date("Y-m-d H:i:s")]);
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Service group deleted successfully.";
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Service group already deleted.";    
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Service group not found.";
                }
                return $this->respond($response);
            }
        }

        public function view_service_group()
        {
            $post = $this->request->getVar();
            $input_parameter = ["service_group_id","user_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new Service_group;
                $service_group = $model->where("id",$post["service_group_id"])->where("is_deleted",0)->first();
                if($service_group) {
                    if($service_group["avatar"] != "" && file_exists("public/uploads/service_group/".$service_group["avatar"])) {
                        $service_group["avatar"] = base_url("public/uploads/service_group/".$service_group["avatar"]);
                    }
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Service group found."; 
                    $response[RESPONSE_DATA] = $service_group;                  
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Service group not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function update_service_group()
        {
            $post = $this->request->getVar();
            $input_parameter = ["user_id","name","is_active","service_group_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new Service_group;
                $service_group = $model->where("id",$post["service_group_id"])->where("is_deleted",0)->first();
                if($service_group) {
                    $avatar = $service_group["avatar"];
                    if(isset($_FILES['avatar'])) {
                        if($_FILES['avatar']['name'] != "" && isset($_FILES['avatar']['name'])) {
                            $img = $this->request->getFile('avatar');
                            $img->move("public/uploads/service_group",$img->getRandomName());
                            $avatar = $img->getName();
                        }
                    }
                    $color =$service_group["color"];
                    if(isset($post["color"])) {
                        $color = check_null_blank($post["color"],"#000000");
                    }
                    $note = $service_group["note"];
                    if(isset($post["note"])) {
                        $note = check_null_blank($post["note"]);
                    }
                    $company_id = $service_group["company_id"];
                    if(isset($post["company_id"])) {
                        $company_id = check_null_blank($post["company_id"],0);
                    }
                    $update_data = array(
                        "name" => $post["name"],
                        "color" => $color,
                        "note" => $note,
                        "is_active" => $post['is_active'],
                        "avatar" => $avatar,
                        "company_id" => $company_id,
                        "updatedBy" => $post["user_id"],
                        "updated_at" => strtotime(date("Y-m-d H:i:s"))
                    );
                    if($model->update($post["service_group_id"],$update_data)) {
                        $service_group = $model->where("id",$post["service_group_id"])->first();
                        if($service_group["avatar"] != "" && file_exists("public/uploads/service_group/".$service_group["avatar"])) {
                            $service_group["avatar"] = base_url("public/uploads/service_group/".$service_group["avatar"]);
                        }
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Service group updated successfully."; 
                        $response[RESPONSE_DATA] = $service_group;
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Oops something went wrong.";
                        $response[RESPONSE_DATA] = (object) array();        
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Service group not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function services()
        {
            $post = $this->request->getVar();
            $input_parameter = ['page'];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $page = $post['page'] ?? 1;
                $limit = $post['limit'] ?? LIMIT;
                $offset = ($page - 1) * $limit;

                $db = db_connect();
                $service = $db->table('services s');
                $service->select("s.id,s.service_group_id,sg.name AS service_group_name,s.name,s.is_active");
                $service->join("service_groups sg","sg.id=s.service_group_id");
                $service->where('s.is_deleted',0);
                $service->where('sg.is_deleted',0);
                if(isset($post["service_group_id"]) && $post["service_group_id"] != "") {
                    $service->where('s.service_group_id',$post["service_group_id"]);
                }
                $totalRecords = $service->countAllResults();
                $totalPages = ceil($totalRecords / $limit);

                $service = $db->table('services s');
                $service->select("s.id,s.service_group_id,sg.name AS service_group_name,s.name,s.is_active");
                $service->join("service_groups sg","sg.id=s.service_group_id");
                $service->where('s.is_deleted',0);
                $service->where('sg.is_deleted',0);
                if(isset($post["service_group_id"]) && $post["service_group_id"] != "") {
                    $service->where('s.service_group_id',$post["service_group_id"]);
                }
                $service->limit($limit, $offset);
                $service->orderBy("s.id","desc");
                $services = $service->get()->getResultArray();

                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Total ".$totalRecords." service(s) found.";
                $response[TOTAL_COUNT] = $totalRecords;
                $response[CURRENT_PAGE] = (int) $page;
                if((int) $page == $totalPages) {
                    $response[NEXT_PAGE] = 0;
                } else {
                    $response[NEXT_PAGE] = (int) $page + 1;
                }
                $response[TOTAL_PAGE] = $totalPages;
                $response[LIMIT_WORD] = (int) $limit;
                $response[RESPONSE_DATA] = $services;
                return $this->respond($response);
            }
        }

        public function create_service()
        {
            $post = $this->request->getVar();
            $input_parameter = ["user_id","service_group_id","name"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new Service_group;
                $check_service = $model->select("is_deleted")->where("id",$post["service_group_id"])->first();
                if($check_service) {
                    if($check_service["is_deleted"] == 0) {
                        $model = new SubServiceModel;
                        $count = $model->where(["service_group_id" => $post["service_group_id"],"name" => trim($post["name"])])->get()->getNumRows();
                        if($count == 0) {
                            $insert_data = array(
                                "service_group_id" => $post["service_group_id"],
                                "name" => $post["name"],
                                "created_by" => $post["user_id"],
                                "created_at" => strtotime(date("Y-m-d H:i:s")),
                            );
                            if($model->insert($insert_data)) {
                                $service_id = $model->getInsertID();

                                $db = db_connect();
                                $service = $db->table('services s');
                                $service->select("s.id,s.service_group_id,sg.name AS service_group_name,s.name,s.is_active");
                                $service->join("service_groups sg","sg.id=s.service_group_id");
                                $service->where(["s.is_deleted" => 0,"sg.is_deleted" => 0,"s.id" => $service_id]);
                                $service = $service->get()->getRowArray();

                                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                                $response[RESPONSE_MESSAGE] = "Service added successfully.";
                                $response[RESPONSE_DATA] = $service;
                            } else {
                                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                                $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                                $response[RESPONSE_DATA] = (object) array();    
                            }
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Service is already added.";
                            $response[RESPONSE_DATA] = (object) array();
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Service group is deleted.";
                        $response[RESPONSE_DATA] = (object) array();    
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Service group not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function delete_service()
        {
            $post = $this->request->getVar();
            $input_parameter = ["service_id","user_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new SubServiceModel;
                $service = $model->select("is_deleted")->where("id",$post["service_id"])->first();
                if($service) {
                    if($service["is_deleted"] == 0) {
                        $model->update($post["service_id"],["is_deleted" => 1,"updated_by" => $post["user_id"],"updated_at" => date("Y-m-d H:i:s")]);

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Service deleted successfully.";
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Service already deleted.";    
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Service not found.";
                }
                return $this->respond($response);
            }
        }

        public function view_service()
        {
            $post = $this->request->getVar();
            $input_parameter = ["service_id","user_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new SubServiceModel;
                $count = $model->where("id",$post["service_id"])->where("is_deleted",0)->get()->getNumRows();
                if($count > 0) {
                    $db = db_connect();
                    $service = $db->table('services s');
                    $service->select("s.id,s.service_group_id,sg.name AS service_group_name,s.name,s.is_active");
                    $service->join("service_groups sg","sg.id=s.service_group_id");
                    $service->where(["s.is_deleted" => 0,"sg.is_deleted" => 0,"s.id" => $post["service_id"]]);
                    $service = $service->get()->getRowArray();

                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Service found."; 
                    $response[RESPONSE_DATA] = $service;                  
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Service not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function update_service()
        {
            $post = $this->request->getVar();
            $input_parameter = ["user_id","service_group_id","name","service_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new Service_group;
                $check_service = $model->select("is_deleted")->where("id",$post["service_group_id"])->first();
                if($check_service) {
                    if($check_service["is_deleted"] == 0) {
                        $model = new SubServiceModel;
                        $count = $model->where(["service_group_id" => $post["service_group_id"],"name" => trim($post["name"]),"id !=" => $post["service_id"]])->get()->getNumRows();
                        if($count == 0) {
                            $update_data = array(
                                "service_group_id" => $post["service_group_id"],
                                "name" => $post["name"],
                                "updated_by" => $post["user_id"],
                                "updated_at" => strtotime(date("Y-m-d H:i:s")),
                            );
                            if($model->update($post["service_id"],$update_data)) {
                                $service_id = $post["service_id"];

                                $db = db_connect();
                                $service = $db->table('services s');
                                $service->select("s.id,s.service_group_id,sg.name AS service_group_name,s.name,s.is_active");
                                $service->join("service_groups sg","sg.id=s.service_group_id");
                                $service->where(["s.is_deleted" => 0,"sg.is_deleted" => 0,"s.id" => $service_id]);
                                $service = $service->get()->getRowArray();

                                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                                $response[RESPONSE_MESSAGE] = "Service edited successfully.";
                                $response[RESPONSE_DATA] = $service;
                            } else {
                                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                                $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                                $response[RESPONSE_DATA] = (object) array();    
                            }
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Service is already added.";
                            $response[RESPONSE_DATA] = (object) array();
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Service group is deleted.";
                        $response[RESPONSE_DATA] = (object) array();    
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Service group not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function payment_types()
        {
            $post = $this->request->getVar();
            $input_parameter = ['page','salon_id'];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $page = $post['page'] ?? 1;
                $limit = $post['limit'] ?? LIMIT;
                $offset = ($page - 1) * $limit;

                $model = new CompanyModel;
                $count = $model->where("id",$post["salon_id"])->where("isActive",'1')->get()->getNumRows();
                if($count > 0) {
                    $model = new PaymentTypeModel;
                    $totalRecords = $model->where("is_deleted", 0)->where("company_id",$post["salon_id"])->countAllResults();
                    $totalPages = ceil($totalRecords/$limit);

                    $payment_types = $model->select("id,name,company_id as salon_id,position,is_active")->where("is_deleted", 0)->where("company_id",$post["salon_id"])->limit($limit, $offset)->orderBy("id","desc")->get()->getResultArray();
                    
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Total ".$totalRecords." payment type(s) found.";
                    $response[TOTAL_COUNT] = $totalRecords;
                    $response[CURRENT_PAGE] = (int) $page;
                    if((int) $page == $totalPages || $totalPages == 0) {
                        $response[NEXT_PAGE] = 0;
                    } else {
                        $response[NEXT_PAGE] = (int) $page + 1;
                    }
                    $response[TOTAL_PAGE] = $totalPages;
                    $response[LIMIT_WORD] = (int) $limit;
                    $response[RESPONSE_DATA] = $payment_types;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function create_payment_type()
        {
            $post = $this->request->getVar();
            $input_parameter = ["user_id","salon_id","name","is_active"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new PaymentTypeModel;
                    $count = $model->where(["company_id" => $post["salon_id"],"is_deleted" => 0,"name" => trim($post["name"])])->get()->getNumRows();
                    if($count == 0) {
                        $position = $model->select("position")->where("company_id",$post["salon_id"])->orderBy("id","desc")->first();
                        if($position) {
                            $position_no = $position['position']+1;
                        } else { 
                            $position_no = 1;
                        }
                        $insert_data = array(
                            "name" => $post["name"],
                            "position" => $position_no,
                            "is_active" => $post["is_active"],
                            "company_id" => $post["salon_id"],
                            "created_by" => $post["user_id"],
                            "created_at" => strtotime(date("Y-m-d H:i:s")),
                        );
                        if($model->insert($insert_data)) {
                            $payment_type_id = $model->getInsertID();

                            $payment_type = $model->select("id,name,company_id as salon_id,position,is_active")
                            ->where("is_deleted", 0)
                            ->where("company_id",$post["salon_id"])
                            ->where("id",$payment_type_id)
                            ->first();

                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "Payment added successfully.";
                            $response[RESPONSE_DATA] = $payment_type;
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                            $response[RESPONSE_DATA] = (object) array();    
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Payment type is already added.";
                        $response[RESPONSE_DATA] = (object) array();
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function delete_payment_type()
        {
            $post = $this->request->getVar();
            $input_parameter = ["payment_type_id","user_id","salon_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new PaymentTypeModel;
                    $count = $model->where(["company_id" => $post["salon_id"],"id" => $post["payment_type_id"],"is_deleted" => 0])->get()->getNumRows();
                    if($count > 0) {
                        $model->update($post["payment_type_id"],["is_deleted" => 1,"updated_by" => $post["user_id"],"updated_at" => date("Y-m-d H:i:s")]);
                        
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Payment type deleted successfully.";
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Payment type not found.";
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                }
                return $this->respond($response);
            }
        }

        public function view_payment_type()
        {
            $post = $this->request->getVar();
            $input_parameter = ["salon_id","user_id","payment_type_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new PaymentTypeModel;
                    $payment_type = $model->select("id,name,company_id as salon_id,position,is_active")->where(["company_id" => $post["salon_id"],"id" => $post["payment_type_id"],"is_deleted" => 0])->first();
                    if($payment_type) {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Payment type found."; 
                        $response[RESPONSE_DATA] = $payment_type;                  
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Payment type not found.";
                        $response[RESPONSE_DATA] = (object) array();
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                }
                return $this->respond($response);
            }
        }

        public function update_payment_type()
        {
            $post = $this->request->getVar();
            $input_parameter = ["payment_type_id","salon_id","user_id","name","is_active"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new PaymentTypeModel;
                    $count = $model->where("is_deleted",0)->where("id",$post["payment_type_id"])->get()->getNumRows();
                    if($count > 0) {
                        $count = $model->where(["company_id" => $post["salon_id"],"name" => trim($post["name"]),"id !=" => $post["payment_type_id"]])->get()->getNumRows();
                        if($count == 0) {
                            $update_data = array(
                                "name" => $post["name"],
                                "is_active" => $post["is_active"],
                                "company_id" => $post["salon_id"],
                                "updated_by" => $post["user_id"],
                                "updated_at" => strtotime(date("Y-m-d H:i:s")),
                            );
                            if($model->update($post["payment_type_id"],$update_data)) {
                                $payment_type_id = $post["payment_type_id"];

                                $model = new PaymentTypeModel;
                                $payment_type = $model->select("id,name,company_id as salon_id,position,is_active")
                                ->where(["company_id" => $post["salon_id"],"id" => $post["payment_type_id"],"is_deleted" => 0])
                                ->first();
                                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                                $response[RESPONSE_MESSAGE] = "Payment type edited successfully.";
                                $response[RESPONSE_DATA] = $payment_type;
                            } else {
                                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                                $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                                $response[RESPONSE_DATA] = (object) array();    
                            }
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Payment type is already added.";
                            $response[RESPONSE_DATA] = (object) array();
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Payment type not found.";
                        $response[RESPONSE_DATA] = (object) array();
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function check_staff_time()
        {
            $post = $this->request->getVar();
            $input_parameter = ["company_id","time","date"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $post["date"] = date("Y-m-d",strtotime($post["date"]));

                $model = new StaffTimingModel;
                $staff_dates = $model->select("staffId")->where(["companyId" => $post["company_id"],"date" => $post["date"]])->get()->getResultArray();
                $available_staff_ids = array_column($staff_dates, 'staffId');

                $model = new WebsiteEntry;
                $items = $model->select("service_id,duration")->where(["company_id" => $post["company_id"],"customer_id" => $post["customer_id"]])->get()->getResultArray();

                $services = array_column($items, 'service_id');
                $staffIds = $available_staff_ids;
                if($items) {
                    $duration = array_column($items, 'duration');
                    $totalDuration = array_sum($duration);
                } else {
                    $totalDuration = 0;
                }
                $serviceEnd = date("H:i:s", strtotime($post["time"]) + ($totalDuration * 60));

                $db = db_connect();
                $builder = $db->table('staff_timings st');
                $builder->select('st.staffId');
                $builder->whereIn('st.staffId', $staffIds);
                $builder->where('st.date', $post["date"]);
                $builder->where('st.stime <=', $post["time"]);
                $builder->where('st.etime >=', $serviceEnd);
                $availableStaff = $builder->get()->getResultArray();
                $availableIds   = array_column($availableStaff, 'staffId');
                if (!empty($availableIds)) {
                    $builder = $db->table('staff_services');
                    $builder->select('service_id, GROUP_CONCAT(DISTINCT staff_id) as staff_available');
                    $builder->whereIn('staff_id', $availableIds);   
                    $builder->whereIn('service_id', $services);
                    $builder->groupBy('service_id');
                    $result = $builder->get()->getResultArray();
                    if($result) {
                        $allStaff = [];
                        foreach ($result as $row) {
                            $ids = explode(",", $row['staff_available']);
                            $allStaff = array_merge($allStaff, $ids);
                        }
                        $staff_ids = array_values(array_unique($allStaff));
                        if (!empty($staff_ids)) {
                            // get already booked staff at given date/time
                            $builder = $db->table('carts c');
                            $builder->distinct();
                            $builder->select('c.staffId');
                            $builder->join('appointments a', 'a.id = c.appointmentId');
                            $builder->where('c.date', $post["date"]);
                            $builder->where('c.stime <=', $post["time"]);
                            $builder->where('c.etime >', $post["time"]);
                            $builder->whereIn('c.staffId', $staff_ids);
                            $builder->where('a.status', 1);

                            $bookedStaff = $builder->get()->getResultArray();
                            $bookedIds = array_column($bookedStaff, 'staffId');

                            // remove booked staff from available staff
                            $available_staff_ids = array_values(array_diff($staff_ids, $bookedIds));
                            $available_staff_ids = implode(",",$available_staff_ids);
                        }
                    } else {
                        $available_staff_ids = "";
                    }
                } else {
                    $available_staff_ids = "";
                }
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "";
                $response[RESPONSE_DATA] = $available_staff_ids;
                return $this->respond($response);
            }
        }
    }