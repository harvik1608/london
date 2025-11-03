<?php 
    namespace App\Controllers;

    use CodeIgniter\RESTful\ResourceController;
    use CodeIgniter\API\ResponseTrait;
    use App\Models\Staff;
    use App\Models\DiscountTypeModel;
    use App\Models\CompanyModel;
    use App\Models\CustomerModel;
    use App\Models\Avatar;
    use App\Models\WeekendDiscount;
    use App\Models\ServiceModel;
    use App\Models\SubServiceModel;
    use App\Models\ServicePriceModel;

    class Api_mobile extends ResourceController
    {
        use ResponseTrait;
        protected $helpers = ["custom"];

        public function sign_in()
        {
            $post = $this->request->getVar();
            $input_parameter = array('email','password');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new Staff;
                $staff = $model->where("email",trim($post["email"]))->first();
                if($staff) {
                    if($staff["is_active"] == 1) {
                        if(md5(trim($post["password"])) == $staff["password"]) {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "Account found.";
                            $response[RESPONSE_DATA] = $staff;
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Password is incorrect.";
                            $response[RESPONSE_DATA] = (object) array();
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Your account is not active.";
                        $response[RESPONSE_DATA] = (object) array();    
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Email not found.";
                    $response[RESPONSE_DATA] = (object) array();
                }
                return $this->respond($response);
            }
        }

        public function forget_password()
        {
            $post = $this->request->getVar();
            $input_parameter = array('email');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new Staff;
                $staff = $model->where("email",trim($post["email"]))->first();
                if($staff) {
                    $code = 1234;
                    $model->update($staff["id"],["code" => $code]);

                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Code has been sent to ".$post["email"];
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Email not found.";
                }
                return $this->respond($response);
            }
        }

        public function reset_password()
        {
            $post = $this->request->getVar();
            $input_parameter = array('code','new_password');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new Staff;
                $staff = $model->where("code",trim($post["code"]))->first();
                if($staff) {
                    $model->update($staff["id"],["password" => md5(trim($post["new_password"])),"code" => "0000"]);

                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Password has been changed.";
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Code is incorrect.";
                }
                return $this->respond($response);
            }
        }

        public function discount_types()
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
                    $model = new DiscountTypeModel;
                    $totalRecords = $model->where("is_deleted", 0)->where("company_id",$post["salon_id"])->countAllResults();
                    $totalPages = ceil($totalRecords/$limit);

                    $discount_types = $model->select("id,name,company_id as salon_id,discount_type,discount_value,position,is_active")->where("is_deleted", 0)->where("company_id",$post["salon_id"])->limit($limit, $offset)->orderBy("id","desc")->get()->getResultArray();
                    
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Total ".$totalRecords." discount type(s) found.";
                    $response[TOTAL_COUNT] = $totalRecords;
                    $response[CURRENT_PAGE] = (int) $page;
                    if((int) $page == $totalPages || $totalPages == 0) {
                        $response[NEXT_PAGE] = 0;
                    } else {
                        $response[NEXT_PAGE] = (int) $page + 1;
                    }
                    $response[TOTAL_PAGE] = $totalPages;
                    $response[LIMIT_WORD] = (int) $limit;
                    $response[RESPONSE_DATA] = $discount_types;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function create_discount_type()
        {
            $post = $this->request->getVar();
            $input_parameter = ["user_id","salon_id","name","discount_type","discount_value","is_active"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new DiscountTypeModel;
                    $count = $model->where(["company_id" => $post["salon_id"],"is_deleted" => 0,"name" => trim($post["name"]),"discount_type" => $post["discount_type"]])->get()->getNumRows();
                    if($count == 0) {
                        $position = $model->select("position")->where("company_id",$post["salon_id"])->orderBy("id","desc")->first();
                        if($position) {
                            $position_no = $position['position']+1;
                        } else { 
                            $position_no = 1;
                        }
                        $insert_data = array(
                            "name" => $post["name"],
                            "discount_type" => $post["discount_type"],
                            "discount_value" => $post["discount_value"],
                            "position" => $position_no,
                            "is_active" => $post["is_active"],
                            "company_id" => $post["salon_id"],
                            "created_by" => $post["user_id"],
                            "created_at" => strtotime(date("Y-m-d H:i:s")),
                        );
                        if($model->insert($insert_data)) {
                            $discount_type_id = $model->getInsertID();

                            $discount_type = $model->select("id,name,company_id as salon_id,discount_type,discount_value,position,is_active")
                            ->where("is_deleted", 0)
                            ->where("company_id",$post["salon_id"])
                            ->where("id",$discount_type_id)
                            ->first();

                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "Discount added successfully.";
                            $response[RESPONSE_DATA] = $discount_type;
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                            $response[RESPONSE_DATA] = (object) array();    
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Discount type is already added.";
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

        public function delete_discount_type()
        {
            $post = $this->request->getVar();
            $input_parameter = ["discount_type_id","user_id","salon_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new DiscountTypeModel;
                    $count = $model->where(["company_id" => $post["salon_id"],"id" => $post["discount_type_id"],"is_deleted" => 0])->get()->getNumRows();
                    if($count > 0) {
                        $model->update($post["discount_type_id"],["is_deleted" => 1,"updated_by" => $post["user_id"],"updated_at" => date("Y-m-d H:i:s")]);
                        
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Discount type deleted successfully.";
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Discount type not found.";
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                }
                return $this->respond($response);
            }
        }

        public function view_discount_type()
        {
            $post = $this->request->getVar();
            $input_parameter = ["salon_id","user_id","discount_type_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new DiscountTypeModel;
                    $payment_type = $model->select("id,name,company_id as salon_id,discount_type,discount_value,position,is_active")->where(["company_id" => $post["salon_id"],"id" => $post["discount_type_id"],"is_deleted" => 0])->first();
                    if($payment_type) {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Discount type found."; 
                        $response[RESPONSE_DATA] = $payment_type;                  
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Discount type not found.";
                        $response[RESPONSE_DATA] = (object) array();
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                }
                return $this->respond($response);
            }
        }

        public function update_discount_type()
        {
            $post = $this->request->getVar();
            $input_parameter = ["discount_type_id","salon_id","user_id","name","is_active","discount_type","discount_value"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new DiscountTypeModel;
                    $count = $model->where("is_deleted",0)->where("id",$post["discount_type_id"])->get()->getNumRows();
                    if($count > 0) {
                        $count = $model->where(["company_id" => $post["salon_id"],"name" => trim($post["name"]),"discount_type" => $post["discount_type"],"id !=" => $post["discount_type_id"]])->get()->getNumRows();
                        if($count == 0) {
                            $update_data = array(
                                "name" => $post["name"],
                                "discount_type" => $post["discount_type"],
                                "discount_value" => $post["discount_value"],
                                "is_active" => $post["is_active"],
                                "company_id" => $post["salon_id"],
                                "updated_by" => $post["user_id"],
                                "updated_at" => strtotime(date("Y-m-d H:i:s")),
                            );
                            if($model->update($post["discount_type_id"],$update_data)) {
                                $discount_type_id = $post["discount_type_id"];

                                $model = new DiscountTypeModel;
                                $discount_type = $model->select("id,name,company_id as salon_id,discount_type,discount_value,position,is_active")
                                ->where(["company_id" => $post["salon_id"],"id" => $post["discount_type_id"],"is_deleted" => 0])
                                ->first();

                                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                                $response[RESPONSE_MESSAGE] = "Discount type edited successfully.";
                                $response[RESPONSE_DATA] = $discount_type;
                            } else {
                                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                                $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                                $response[RESPONSE_DATA] = (object) array();    
                            }
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Discount type is already added.";
                            $response[RESPONSE_DATA] = (object) array();
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Discount type not found.";
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

        public function customers()
        {
            $post = $this->request->getVar();
            $input_parameter = ['salon_id','page'];
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
                    $model = new CustomerModel;
                    $totalRecords = $model->where("is_deleted", 0)->where("companyId",$post["salon_id"])->countAllResults();
                    $totalPages = ceil($totalRecords/$limit);
                    $customers = $model->where("is_deleted", 0)->where("companyId",$post["salon_id"])->limit($limit, $offset)->orderBy("id","desc")->get()->getResultArray();
                    if($customers) {
                        foreach ($customers as $key => $val) {
                            $customers[$key]["resource_id"] = check_null_value($val["resource_id"]);
                            $customers[$key]["password"] = check_null_value($val["password"]);
                            $customers[$key]["json"] = check_null_value($val["json"]);
                            $customers[$key]["url"] = check_null_value($val["url"]);
                        }
                    }

                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Total ".$totalRecords." customer(s) found.";
                    $response[TOTAL_COUNT] = $totalRecords;
                    $response[CURRENT_PAGE] = (int) $page;
                    if((int) $page == $totalPages || $totalPages == 0) {
                        $response[NEXT_PAGE] = 0;
                    } else {
                        $response[NEXT_PAGE] = (int) $page + 1;
                    }
                    $response[TOTAL_PAGE] = $totalPages;
                    $response[LIMIT_WORD] = (int) $limit;
                    $response[RESPONSE_DATA] = $customers;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function create_customer()
        {
            $post = $this->request->getVar();
            $input_parameter = ["user_id","salon_id","name","phone"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $marketing_email = "N";
                    if(isset($post["marketing_email"]) & $post["marketing_email"] != "") {
                        $marketing_email = $post["marketing_email"];
                    }
                    $insert_data = array(
                        "name" => $post["name"],
                        "phone" => $post["phone"],
                        "email" => $post["email"],
                        "marketing_email" => $marketing_email,
                        "note" => $post["note"],
                        "companyId" => $post["salon_id"],
                        "addedBy" => $post["user_id"],
                        "createdAt" => strtotime(date("Y-m-d H:i:s")),
                        "updatedAt" => strtotime(date("Y-m-d H:i:s")),
                    );
                    if(isset($post["password"]) & $post["password"] != "") {
                        $insert_data["password"] = md5($post["password"]);
                    }
                    $model = new CustomerModel;
                    if($model->insert($insert_data)) {
                        $customer_id = $model->getInsertID();
                        $customer = $model->where("id",$customer_id)->first();
                        $customer["resource_id"] = check_null_value($customer["resource_id"]);
                        $customer["password"] = check_null_value($customer["password"]);
                        $customer["json"] = check_null_value($customer["json"]);
                        $customer["url"] = check_null_value($customer["url"]);

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Customer added successfully.";
                        $response[RESPONSE_DATA] = $customer;
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
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

        public function delete_customer()
        {
            $post = $this->request->getVar();
            $input_parameter = ["customer_id","user_id","salon_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new CustomerModel;
                    $count = $model->where(["companyId" => $post["salon_id"],"id" => $post["customer_id"],"is_deleted" => 0])->get()->getNumRows();
                    if($count > 0) {
                        $model->update($post["customer_id"],["is_deleted" => 1,"updatedBy" => $post["user_id"],"updatedAt" => date("Y-m-d H:i:s")]);
                        
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Customer deleted successfully.";
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Customer not found.";
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                }
                return $this->respond($response);
            }
        }

        public function view_customer()
        {
            $post = $this->request->getVar();
            $input_parameter = ["salon_id","user_id","customer_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new CustomerModel;
                    $customer = $model->where(["companyId" => $post["salon_id"],"id" => $post["customer_id"],"is_deleted" => 0])->first();
                    if($customer) {
                        $customer["resource_id"] = check_null_value($customer["resource_id"]);
                        $customer["password"] = check_null_value($customer["password"]);
                        $customer["json"] = check_null_value($customer["json"]);
                        $customer["url"] = check_null_value($customer["url"]);

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Customer found."; 
                        $response[RESPONSE_DATA] = $customer;                  
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Customer not found.";
                        $response[RESPONSE_DATA] = (object) array();
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                }
                return $this->respond($response);
            }
        }

        public function update_customer()
        {
            $post = $this->request->getVar();
            $input_parameter = ["salon_id","name","phone","customer_id","user_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new CustomerModel;
                    $cust = $model->where("is_deleted",0)->where("id",$post["customer_id"])->where("companyId",$post["salon_id"])->first();
                    if($cust) {
                        $marketing_email = $cust["marketing_email"];
                        if(isset($post["marketing_email"]) & $post["marketing_email"] != "") {
                            $marketing_email = $post["marketing_email"];
                        }
                        $update_data = array(
                            "name" => $post["name"],
                            "phone" => $post["phone"],
                            "email" => $post["email"],
                            "marketing_email" => $marketing_email,
                            "note" => $post["note"],
                            "companyId" => $post["salon_id"],
                            "updatedAt" => strtotime(date("Y-m-d H:i:s")),
                        );
                        if($model->update($post["customer_id"],$update_data)) {
                            $customer_id = $post["customer_id"];
                            $customer = $model->where(["companyId" => $post["salon_id"],"id" => $post["customer_id"],"is_deleted" => 0])->first();
                            if($customer) {
                                $customer["resource_id"] = check_null_value($customer["resource_id"]);
                                $customer["password"] = check_null_value($customer["password"]);
                                $customer["json"] = check_null_value($customer["json"]);
                                $customer["url"] = check_null_value($customer["url"]);
                            }
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "Customer edited successfully.";
                            $response[RESPONSE_DATA] = $customer;
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                            $response[RESPONSE_DATA] = (object) array();    
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Customer not found.";
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

        public function photos()
        {
            $post = $this->request->getVar();
            $input_parameter = ['salon_id','page'];
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
                    $model = new Avatar;
                    $totalRecords = $model->where("company_id",$post["salon_id"])->countAllResults();
                    $totalPages = ceil($totalRecords/$limit);
                    $photos = $model->select("id,name AS avatar,position,is_active,company_id")->where("company_id",$post["salon_id"])->limit($limit, $offset)->orderBy("id","desc")->get()->getResultArray();
                    if($photos) {
                        foreach ($photos as $key => $val) {
                            $photos[$key]["avatar"] = base_url("public/uploads/gallery/".$val["avatar"]);
                        }
                    }
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Total ".$totalRecords." photo(s) found.";
                    $response[TOTAL_COUNT] = $totalRecords;
                    $response[CURRENT_PAGE] = (int) $page;
                    if((int) $page == $totalPages || $totalPages == 0) {
                        $response[NEXT_PAGE] = 0;
                    } else {
                        $response[NEXT_PAGE] = (int) $page + 1;
                    }
                    $response[TOTAL_PAGE] = $totalPages;
                    $response[LIMIT_WORD] = (int) $limit;
                    $response[RESPONSE_DATA] = $photos;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function upload_photo()
        {
            $post = $this->request->getVar();
            $input_parameter = ["user_id","salon_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $avatar = "";
                    if($_FILES['avatar']['name'] != "" && isset($_FILES['avatar']['name']))
                    {
                        $img = $this->request->getFile('avatar');
                        $img->move("public/uploads/gallery",$img->getRandomName());
                        $avatar = $img->getName();
                    }

                    $model = new Avatar;
                    $position = $model->select("position")->where("company_id",$post["salon_id"])->orderBy("id","desc")->first();
                    if($position)
                        $position_no = $position['position']+1;
                    else 
                        $position_no = 1;

                    $insert_data = array(
                        "name" => $avatar,
                        "company_id" => $post["salon_id"],
                        "position" => $position_no,
                        "created_by" => $post["user_id"],
                        "updated_by" => 0,
                        "created_at" => strtotime(date("Y-m-d H:i:s")),
                        "updated_at" => strtotime(date("Y-m-d H:i:s")),
                    );
                    $model = new Avatar;
                    if($model->insert($insert_data)) {
                        $avatar_id = $model->getInsertID();
                        $photo = $model->select("id,name AS avatar,position,is_active,company_id")->where("company_id",$post["salon_id"])->first();
                        $photo["avatar"] = base_url("public/uploads/gallery/".$photo["avatar"]);

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Photo uploaded successfully.";
                        $response[RESPONSE_DATA] = $photo;
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
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

        public function delete_photo()
        {
            $post = $this->request->getVar();
            $input_parameter = ["photo_id","user_id","salon_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new Avatar;
                    $photo = $model->where(["company_id" => $post["salon_id"],"id" => $post["photo_id"]])->first();
                    if($photo) {
                        if($model->delete($post["photo_id"])) {
                            if($photo["name"] != "" && file_exists("public/uploads/gallery"."/".$photo["name"])) {
                                unlink("public/uploads/gallery"."/".$photo["name"]);
                            }
                        }
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Photo deleted successfully.";
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Photo not found.";
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                }
                return $this->respond($response);
            }
        }

        public function discounts()
        {
            $post = $this->request->getVar();
            $input_parameter = ['salon_id','page'];
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
                    $model = new WeekendDiscount;
                    $totalRecords = $model->where("company_id",$post["salon_id"])->countAllResults();
                    $totalPages = ceil($totalRecords/$limit);
                    $discounts = $model->where("company_id",$post["salon_id"])->limit($limit, $offset)->orderBy("id","desc")->get()->getResultArray();

                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "Total ".$totalRecords." discount(s) found.";
                    $response[TOTAL_COUNT] = $totalRecords;
                    $response[CURRENT_PAGE] = (int) $page;
                    if((int) $page == $totalPages || $totalPages == 0) {
                        $response[NEXT_PAGE] = 0;
                    } else {
                        $response[NEXT_PAGE] = (int) $page + 1;
                    }
                    $response[TOTAL_PAGE] = $totalPages;
                    $response[LIMIT_WORD] = (int) $limit;
                    $response[RESPONSE_DATA] = $discounts;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }

        public function create_discount()
        {
            $post = $this->request->getVar();
            $input_parameter = ["user_id","salon_id","name","sdate","edate","percentage","week_days","is_active"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $is_all_service_checked = 0;
                    if(isset($post["is_all_service_checked"]) && $post["is_all_service_checked"] == 1) {
                        $is_all_service_checked = 1;
                    }
                    $service_group_ids = "";
                    if(isset($post["service_group_ids"]) && $post["service_group_ids"] != "") {
                        $service_group_ids = $post["service_group_ids"];
                    }
                    $service_ids = "";
                    if(isset($post["service_ids"]) && $post["service_ids"] != "") {
                        $service_ids = $post["service_ids"];
                    }
                    $insert_data["name"] = $post["name"];
                    $insert_data["sdate"] = $post["sdate"];
                    $insert_data["edate"] = $post["edate"];
                    $insert_data["percentage"] = $post["percentage"];
                    $insert_data["week_days"] = $post["week_days"];
                    $insert_data["week_days"] = $post["week_days"];
                    $insert_data['company_id'] = $post["salon_id"];
                    // $insert_data['is_all_service_checked'] = $is_all_service_checked;
                    $insert_data['service_group_ids'] = $service_group_ids;
                    $insert_data['service_ids'] = $service_ids;
                    $insert_data["is_active"]  = $post["is_active"];
                    $insert_data['created_by'] = $post["user_id"];
                    $insert_data['updated_by'] = $post["user_id"];
                    $insert_data['created_at'] = format_date(5);
                    $insert_data['updated_at'] = format_date(5);
                    $model = new WeekendDiscount;
                    if($model->insert($insert_data)) {
                        $discount_id = $model->getInsertID();
                        $discount = $model->where("id",$discount_id)->first();

                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Discount added successfully.";
                        $response[RESPONSE_DATA] = $discount;
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
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

        public function update_discount()
        {
            $post = $this->request->getVar();
            $input_parameter = ["user_id","salon_id","name","sdate","edate","percentage","week_days","is_active","discount_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new WeekendDiscount;
                    $weekday = $model->where("company_id",$post["salon_id"])->where("id",$post["discount_id"])->first();
                    if($weekday) {
                        $is_all_service_checked = 0;
                        if(isset($post["is_all_service_checked"]) && $post["is_all_service_checked"] == 1) {
                            $is_all_service_checked = 1;
                        }
                        $service_group_ids = $weekday["service_group_ids"];
                        if(isset($post["service_group_ids"]) && $post["service_group_ids"] != "") {
                            $service_group_ids = $post["service_group_ids"];
                        }
                        $service_ids = $weekday["service_ids"];
                        if(isset($post["service_ids"]) && $post["service_ids"] != "") {
                            $service_ids = $post["service_ids"];
                        }
                        $update_data["name"] = $post["name"];
                        $update_data["sdate"] = $post["sdate"];
                        $update_data["edate"] = $post["edate"];
                        $update_data["percentage"] = $post["percentage"];
                        $update_data["week_days"] = $post["week_days"];
                        $update_data["week_days"] = $post["week_days"];
                        $update_data['company_id'] = $post["salon_id"];
                        // $insert_data['is_all_service_checked'] = $is_all_service_checked;
                        $update_data['service_group_ids'] = $service_group_ids;
                        $update_data['service_ids'] = $service_ids;
                        $update_data["is_active"]  = $post["is_active"];
                        $update_data['created_by'] = $post["user_id"];
                        $update_data['updated_by'] = $post["user_id"];
                        $update_data['created_at'] = format_date(5);
                        $update_data['updated_at'] = format_date(5);
                        $model = new WeekendDiscount;
                        if($model->update($post["discount_id"],$update_data)) {
                            $discount_id = $post["discount_id"];
                            $discount = $model->where("id",$discount_id)->first();

                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "Discount updated successfully.";
                            $response[RESPONSE_DATA] = $discount;
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                            $response[RESPONSE_DATA] = (object) array();    
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Discount not found.";
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

        public function delete_discount()
        {
            $post = $this->request->getVar();
            $input_parameter = ["user_id","salon_id","discount_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new WeekendDiscount;
                    $weekday = $model->where("company_id",$post["salon_id"])->where("id",$post["discount_id"])->first();
                    if($weekday) {
                        if($model->delete($post["discount_id"])) {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                            $response[RESPONSE_MESSAGE] = "Discount deleted successfully.";
                        } else {
                            $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                            $response[RESPONSE_MESSAGE] = "Oops something went wrong please try again later.";
                            $response[RESPONSE_DATA] = (object) array();    
                        }
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Discount not found.";
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

        public function view_discount()
        {
            $post = $this->request->getVar();
            $input_parameter = ["salon_id","user_id","discount_id"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $model = new CompanyModel;
                $count = $model->where(["id" => $post["salon_id"],"isActive" => '1'])->get()->getNumRows();
                if($count > 0) {
                    $model = new WeekendDiscount;
                    $discount = $model->where(["company_id" => $post["salon_id"],"id" => $post["discount_id"]])->first();
                    if($discount) {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Discount found."; 
                        $response[RESPONSE_DATA] = $discount;                  
                    } else {
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                        $response[RESPONSE_MESSAGE] = "Discount not found.";
                        $response[RESPONSE_DATA] = (object) array();
                    }
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
                    $response[RESPONSE_MESSAGE] = "Salon not found.";
                }
                return $this->respond($response);
            }
        }

        public function add_service_price()
        {
            $post = $this->request->getVar();
            $input_parameter = ["salon_id","user_id","price_duration","service_id","price_type","extra_time_type","duration"];
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $price_duration = json_decode($post["price_duration"],true);
                if($price_duration) {
                    $json = array();
                    for($i = 0; $i < count($price_duration); $i ++) {
                        $json[] = array(
                            "id" => (string) ($i+1),
                            "duration" => $price_duration[$i]['duration'],
                            "retail_price" => $price_duration[$i]['rprice'],
                            "special_price" => $price_duration[$i]['sprice'],
                            "caption" => $price_duration[$i]['caption']
                        );
                    }
                    if(!empty($json)) {
                        $jsondata = json_encode($json);
                    } else {
                        $jsondata = "";
                    }
                    $model = new ServicePriceModel;
                    $check = $model->select("id")->where("service_id",$post["service_id"])->where("company_id",$post["salon_id"])->first();
                    if($check) {
                        $update_data = array(
                            "service_id" => $post["service_id"],
                            "price_type" => $post["price_type"],
                            "extra_time_type" => $post["extra_time_type"],
                            "duration" => $post["duration"],
                            "bookedFrom" => $post["bookedFrom"] == "" ? "Y" : $post["bookedFrom"],
                            "note" => $post["note"],
                            "json" => $jsondata,
                            "company_id" => $post["salon_id"]
                        );
                        $model->update($check["id"],$update_data);
                        $price_id = $check["id"];
                    } else {
                        $insert_data = array(
                            "service_id" => $post["service_id"],
                            "price_type" => $post["price_type"],
                            "extra_time_type" => $post["extra_time_type"],
                            "duration" => $post["duration"],
                            "bookedFrom" => $post["bookedFrom"],
                            "note" => $post["note"],
                            "json" => $jsondata,
                            "company_id" => $post["salon_id"]
                        );
                        $model->insert($insert_data);
                        $price_id = $model->getInsertID();
                    }
                    $service_price = $model->where(["company_id" => $post["salon_id"],"id" => $price_id])->first();
                    if($service_price) {
                        $json = array();
                        if($service_price["json"] != "") {
                            $json = json_decode($service_price["json"],true);
                        } 
                        $service_price["json"] = $json;
                        
                        $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                        $response[RESPONSE_MESSAGE] = "Price added successfully."; 
                        $response[RESPONSE_DATA] = $service_price;                  
                    } 
                }
                return $this->respond($response);
            }
        }

        public function logout()
        {
            $post = $this->request->getVar();
            $input_parameter = array('user_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Logout successfully.";
                return $this->respond($response);
            }
        }
    }