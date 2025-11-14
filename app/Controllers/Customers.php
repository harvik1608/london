<?php
    namespace App\Controllers;

    require APPPATH.'Views/vendor/vendor/autoload.php';

    use App\Models\CustomerModel;
    use App\Models\CompanyModel;
    use App\Models\ConsertFormModel;
    use App\Models\ConsertFormQuestionModel;
    use App\Models\ConsertFormQuestionAnswerModel;
    use App\Models\CustomerConsentModel;

    class Customers extends BaseController
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
            // $res = update_google_contact("people/c747308716880456350");
            // preview($res);

            if(isset($this->userdata["id"])) {
                if(check_permission("customers")) {
                    $model = new CustomerModel;
                    $data["customers"] = $model->where("companyId",static_company_id())->where("is_deleted",0)->orderBy("id","desc")->get()->getResultArray();
                    return view('admin/customer/list',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }
        
        public function load()
        {
            $session = session();
            $userdata = $session->get('userdata');
            $current_staff_id = $userdata['id'];
    
            $post = $this->request->getVar();
            $result = array("data" => array());
    
            // Extract DataTable parameters
            $draw = $post['draw'];
            $start = (int) $post['start'];
            $length = (int) $post['length'];
            $searchValue = $post['search']['value'];
            $orderColumn = $post['order'][0]['column'];
            $orderDir = $post['order'][0]['dir'];
            
            $columns = ['id', 'name', 'email','phone'];
            $orderBy = $columns[$orderColumn] ?? 'id';

            $model = db_connect();
            $query = $model->table("customers c");
            $query = $query->select("c.id,c.name,c.email,c.phone");
            $query = $query->where("c.companyId",static_company_id());
            $query = $query->where("c.is_deleted",0);
            if($searchValue != "") {
                $query = $query->where("(c.name LIKE '%".$searchValue."%' OR c.email LIKE '%".$searchValue."%' OR c.phone LIKE '%".$searchValue."%')");
            }
            $totalRecords = $query->countAllResults(false);
            $query = $query->orderBy($orderBy, $orderDir)->limit($length, $start);
            $entries = $query->get()->getResultArray();
            foreach ($entries as $key => $val) {
                $_editUrl = base_url("customers/" . $val["id"] . "/edit");
                $trashUrl = base_url("customers/" . $val["id"]);
                $consentFormUrl = base_url("customers/" . $val["id"]);
    
                $buttons = "";
                // if($userdata['role'] == 1) {
                    // $buttons .= '<a href="' . $_editUrl . '"><i class="bx bx-eye icon-sm"></i></a>&nbsp;';
                    $buttons .= '<a href="' . $consentFormUrl . '" class="btn btn-sm btn-success"><i class="fa fa-file text-white"></i></a>&nbsp;';
                    $buttons .= '<a href="' . $_editUrl . '" class="btn btn-sm btn-success"><i class="fa fa-edit text-white"></i></a>&nbsp;';
                    $buttons .= '<a href="javascript:;" class="btn btn-sm btn-danger" onclick=remove_row("' . $trashUrl . '",0)><i class="fa fa-trash text-white"></i></a>';
                // }
                $result['data'][$key] = [
                    "<small>".($key + 1)."</small>",
                    $val['name'],
                    $val['email'],
                    $val['phone'],
                    $buttons
                ];
            }

            // Add response metadata
            $result["draw"] = intval($draw);
            $result["recordsTotal"] = $totalRecords;
            $result["recordsFiltered"] = $totalRecords;
    
            // Output JSON
            echo json_encode($result);
            exit;
        }

        public function new()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("customers")) {
                    $data["customer"] = [];
                    return view('admin/customer/add_edit',$data);
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

                $post['companyId'] = static_company_id();
                $post['addedBy'] = $createdBy["id"];
                $post['updatedBy'] = $createdBy["id"];
                $post['createdAt'] = format_date(5);
                $post['updatedAt'] = format_date(5);

                $model = new CustomerModel();
                $model->insert($post);

                $customer_id = $model->getInsertID();
                if($customer_id > 0)
                {
                    $resource_id = add_google_contact($post);
                    if($resource_id != "") {
                        $model = new CustomerModel();
                        $model->update($customer_id,array("resource_id" => $resource_id));
                    }

                    $session->setFlashData('success','Customer added successfully');
                    $ret_arr = array("status" => 1);
                } else {
                    $ret_arr = array("status" => 0,"message" => "Error");
                }
                echo json_encode($ret_arr);
                exit;
            }
        }

        public function show($id)
        {
            $session = session();
            if($session->get('userdata')) {
                if(check_permission("customers")) {
                    $model = new CustomerModel();
                    $data['customer'] = $model->where('id',$id)->first();
                    if($data['customer']) {
                        $model = new ConsertFormModel;
                        $data["forms"] = $model->select("id,title,duration")->where("company_id",static_company_id())->where("is_active",1)->where("deleted_at",null)->get()->getResultArray();
                        if($data["forms"]) {
                            $questionModel = new ConsertFormQuestionModel;
                            $answerModel = new ConsertFormQuestionAnswerModel;
                            $customerAnswermodel = new CustomerConsentModel;

                            foreach ($data["forms"] as $key => $form) {
                                $questions = $questionModel->select("id,title")->where(["consert_form_id" => $form["id"],"company_id" => static_company_id(),"is_active" => 1])->get()->getResultArray();
                                if ($questions) {
                                    foreach ($questions as $qKey => $question) {
                                        $answers = $answerModel->select("id,question,answer_type,option")
                                            ->where([
                                                "consent_form_question_id" => $question["id"],
                                                "company_id" => static_company_id(),
                                                "is_active" => 1
                                            ])->get()->getResultArray();

                                            $customerAnswers = $customerAnswermodel->select("consent_form_question_answer_id,answer,date")
                                            ->where([
                                                "customer_id" => $id,
                                                "consent_form_question_id" => $question["id"]
                                            ])->get()->getResultArray();
                                            $customerAnswerMap = [];
                                            foreach ($customerAnswers as $ca) {
                                                $duration_in_month = date("Y-m-d",strtotime("-".$form['duration']." month"));
                                                if(strtotime($ca['date']) > strtotime($duration_in_month)) {
                                                    $customerAnswerMap[$ca['consent_form_question_answer_id']] = $ca['answer'];
                                                }
                                            }
                                            if ($answers) {
                                                foreach ($answers as $aKey => $answer) {
                                                    $answers[$aKey]['customer_answer'] = $customerAnswerMap[$answer['id']] ?? null;
                                                }
                                            }
                                        $questions[$qKey]["options"] = $answers ?: [];
                                    }
                                }
                                $data["forms"][$key]["questions"] = $questions ?: [];
                            }
                        }
                        $data["customer_id"] = $id;
                        $data["company_id"] = $data['customer']["companyId"];
                        return view('admin/customer/view',$data);
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

        public function edit($id)
        {
            $session = session();
            if($session->get('userdata'))
            {
                if(check_permission("customers")) {
                    $model = new CustomerModel();
                    $data['customer'] = $model->where('id',$id)->first();
                    if($data['customer']) {
                        return view('admin/customer/add_edit',$data);
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

                $post['companyId'] = static_company_id();
                $post['updatedBy'] = $createdBy["id"];
                $post['updatedAt'] = format_date(5);

                $model = new CustomerModel();
                $data = $model->update($id,$post);
                if($data)
                {
                    $session->setFlashData('success','Customer edited successfully');
                    $ret_arr = array("status" => 1);
                } else
                    $ret_arr = array("status" => 0,"message" => "Error");
                
                echo json_encode($ret_arr);
                exit;
            }
        }

        public function delete($id)
        {
            $model = New CustomerModel;
            $custo = $model->select("resource_id")->where("id",$id)->first();
            $resource_id = $custo["resource_id"];

            if($model->update($id,["is_deleted" => 1])) {
                if(!is_null($resource_id) && $resource_id != "") {
                    delete_google_contact($resource_id);
                }
            }
            echo json_encode(array("status" => 200));
            exit;
        }

        public function customer_consent_form($customer_id)
        {
            $post = $this->request->getVar();
            $model = new CustomerConsentModel;

            $session = session();
            $createdBy = $session->get('userdata');

            foreach ($post as $key => $value) {
                if (strpos($key, 'input_') === 0) {
                    if (is_array($value)) {
                        $answer = implode(',', $value);
                    } else {
                        $answer = $value;
                    }
                    if($answer != "") {
                        $insert_data = array();
                        $insert_data["customer_id"] = $post["customer_id"];   
                        $insert_data["consent_form_id"] = $post["consent_form_id"];   
                        $insert_data["consent_form_question_id"] = $post["consent_form_question_id"];
                        $questionId = str_replace('input_', '', $key);
                        $insert_data["consent_form_question_answer_id"] = $questionId;
                        $insert_data["answer"] = rawurlencode($answer);
                        $insert_data["date"] = date("Y-m-d");
                        $insert_data["company_id"] = $post["company_id"];
                        $is_exist = $model->select("id")->where(["customer_id" => $post["customer_id"],"consent_form_id" => $post["consent_form_id"],"consent_form_question_id" => $post["consent_form_question_id"],"consent_form_question_answer_id" => $questionId])->first();   
                        if($is_exist) {
                            $insert_data["updated_by"] = $createdBy["id"];   
                            $insert_data["updated_at"] = date("Y-m-d H:i:s");
                            $model->where("id",$is_exist["id"])->set($insert_data)->update();
                        } else {
                            $insert_data["created_by"] = $createdBy["id"];   
                            $insert_data["created_at"] = date("Y-m-d H:i:s");
                            $model->insert($insert_data);
                        }
                    }
                }
                if (strpos($key, 'radio_') === 0) {
                    $insert_data = array();
                    $insert_data["customer_id"] = $post["customer_id"];   
                    $insert_data["consent_form_id"] = $post["consent_form_id"];   
                    $insert_data["consent_form_question_id"] = $post["consent_form_question_id"];
                    $questionId = str_replace('radio_', '', $key);
                    if (is_array($value)) {
                        $answer = implode(',', $value);
                    } else {
                        $answer = $value;
                    }
                    $insert_data["consent_form_question_answer_id"] = $questionId;
                    $insert_data["answer"] = rawurlencode($answer);
                    $insert_data["date"] = date("Y-m-d");
                    $insert_data["company_id"] = $post["company_id"];   
                    $is_exist = $model->select("id")->where(["customer_id" => $post["customer_id"],"consent_form_id" => $post["consent_form_id"],"consent_form_question_id" => $post["consent_form_question_id"],"consent_form_question_answer_id" => $questionId])->first();   
                    if($is_exist) {
                        $insert_data["updated_by"] = $createdBy["id"];   
                        $insert_data["updated_at"] = date("Y-m-d H:i:s");
                        $model->where("id",$is_exist["id"])->set($insert_data)->update();
                    } else {
                        $insert_data["created_by"] = $createdBy["id"];   
                        $insert_data["created_at"] = date("Y-m-d H:i:s");
                        $model->insert($insert_data);
                    }
                }
                if (strpos($key, 'answer_') === 0) {
                    $insert_data = array();
                    $insert_data["customer_id"] = $post["customer_id"];   
                    $insert_data["consent_form_id"] = $post["consent_form_id"];   
                    $insert_data["consent_form_question_id"] = $post["consent_form_question_id"];
                    $questionId = str_replace('answer_', '', $key);
                    if (is_array($value)) {
                        $answer = implode(',', $value);
                    } else {
                        $answer = $value;
                    }
                    $insert_data["consent_form_question_answer_id"] = $questionId;
                    $insert_data["answer"] = rawurlencode($answer);
                    $insert_data["date"] = date("Y-m-d");
                    $insert_data["company_id"] = $post["company_id"];   
                    $is_exist = $model->select("id")->where(["customer_id" => $post["customer_id"],"consent_form_id" => $post["consent_form_id"],"consent_form_question_id" => $post["consent_form_question_id"],"consent_form_question_answer_id" => $questionId])->first();   
                    if($is_exist) {
                        $insert_data["updated_by"] = $createdBy["id"];   
                        $insert_data["updated_at"] = date("Y-m-d H:i:s");
                        $model->where("id",$is_exist["id"])->set($insert_data)->update();
                    } else {
                        $insert_data["created_by"] = $createdBy["id"];   
                        $insert_data["created_at"] = date("Y-m-d H:i:s");
                        $model->insert($insert_data);
                    }
                }
            }
            $model = new CustomerModel;
            $model->update($post["customer_id"],["email" => $post["cmail"]]);

            return redirect()->to('/customers/'.$post["customer_id"]);
        }

        public function get_customer_consent_form()
        {
            $phone = $this->request->getVar('phone');

            $model = new CustomerModel;
            $data["customer"] = $model->select("id,name,phone,email,companyId")->where("phone",$phone)->where("companyId",static_company_id())->first();
            if($data["customer"]) {
                $customerAnswermodel = new CustomerConsentModel;
                $forms = $customerAnswermodel->select("consent_form_id")->where("customer_id",$data["customer"]["id"])->get()->getResultArray();
                $form_ids = array_column($forms,"consent_form_id");
                if(!empty($form_ids)) {
                    $model = new ConsertFormModel;
                    $data["forms"] = $model->select("id,title,duration")->where("company_id",static_company_id())->where("is_active",1)->where("deleted_at",null)->whereIn("id",$form_ids)->get()->getResultArray();
                    if($data["forms"]) {
                        $questionModel = new ConsertFormQuestionModel;
                        $answerModel = new ConsertFormQuestionAnswerModel;

                        foreach ($data["forms"] as $key => $form) {
                            $questions = $questionModel->select("id,title")->where(["consert_form_id" => $form["id"],"company_id" => static_company_id(),"is_active" => 1])->get()->getResultArray();
                            if ($questions) {
                                foreach ($questions as $qKey => $question) {
                                    $answers = $answerModel->select("id,question,answer_type,option")
                                        ->where([
                                            "consent_form_question_id" => $question["id"],
                                            "company_id" => static_company_id(),
                                            "is_active" => 1
                                        ])->get()->getResultArray();

                                        $customerAnswers = $customerAnswermodel->select("consent_form_question_answer_id,answer,date")
                                        ->where([
                                            "customer_id" => $data["customer"]["id"],
                                            "consent_form_question_id" => $question["id"]
                                        ])->get()->getResultArray();
                                        $customerAnswerMap = [];
                                        foreach ($customerAnswers as $ca) {
                                            $duration_in_month = date("Y-m-d",strtotime("-".$form['duration']." month"));
                                            if(strtotime($ca['date']) > strtotime($duration_in_month)) {
                                                $customerAnswerMap[$ca['consent_form_question_answer_id']] = $ca['answer'];
                                            }
                                        }
                                        if ($answers) {
                                            foreach ($answers as $aKey => $answer) {
                                                $answers[$aKey]['customer_answer'] = $customerAnswerMap[$answer['id']] ?? null;
                                            }
                                        }
                                    $questions[$qKey]["options"] = $answers ?: [];
                                }
                            }
                            $data["forms"][$key]["questions"] = $questions ?: [];
                        }
                    }
                    $data["customer_id"] = $data["customer"]["id"];
                    $data["company_id"] = $data['customer']["companyId"];

                    $html = view('admin/customer/consent_form',$data);
                } else {
                    $html = "No consent form found.";    
                }
            } else {
                $html = "No consent form found.";
            }
            echo json_encode(["html" => $html]);
            exit; 
        }

        public function customer_consent_form_history($customer_id)
        {
            $session = session();
            if($session->get('userdata')) {
                if(check_permission("customers")) {
                    $data["customer_id"] = $customer_id;

                    $model = new CustomerModel;
                    $data["customer"] = $model->select("name,phone,email,companyId")->where("id",$customer_id)->where("companyId",static_company_id())->first();
                    $customerAnswermodel = new CustomerConsentModel;
                    $forms = $customerAnswermodel->select("consent_form_id")->where("customer_id",$customer_id)->get()->getResultArray();
                    $form_ids = array_column($forms,"consent_form_id");
                    if(!empty($form_ids)) {
                        $model = new ConsertFormModel;
                        $data["forms"] = $model->select("id,title,duration")->where("company_id",static_company_id())->where("is_active",1)->where("deleted_at",null)->whereIn("id",$form_ids)->get()->getResultArray();
                        if($data["forms"]) {
                            $questionModel = new ConsertFormQuestionModel;
                            $answerModel = new ConsertFormQuestionAnswerModel;

                            foreach ($data["forms"] as $key => $form) {
                                $questions = $questionModel->select("id,title")->where(["consert_form_id" => $form["id"],"company_id" => static_company_id(),"is_active" => 1])->get()->getResultArray();
                                if ($questions) {
                                    foreach ($questions as $qKey => $question) {
                                        $answers = $answerModel->select("id,question,answer_type,option")
                                            ->where([
                                                "consent_form_question_id" => $question["id"],
                                                "company_id" => static_company_id(),
                                                "is_active" => 1
                                            ])->get()->getResultArray();

                                            $customerAnswers = $customerAnswermodel->select("consent_form_question_answer_id,answer,date")
                                            ->where([
                                                "customer_id" => $customer_id,
                                                "consent_form_question_id" => $question["id"]
                                            ])->get()->getResultArray();
                                            $customerAnswerMap = [];
                                            foreach ($customerAnswers as $ca) {
                                                $duration_in_month = date("Y-m-d",strtotime("-".$form['duration']." month"));
                                                if(strtotime($ca['date']) > strtotime($duration_in_month)) {
                                                    $customerAnswerMap[$ca['consent_form_question_answer_id']] = [
                                                        "answer" => $ca["answer"],
                                                        "date"   => $ca["date"]
                                                    ];
                                                }
                                            }
                                            if ($answers) {
                                                foreach ($answers as $aKey => $answer) {
                                                    // $answers[$aKey]['customer_answer'] = $customerAnswerMap[$answer['id']] ?? null;
                                                    $answers[$aKey]['customer_answer'] = $customerAnswerMap[$answer['id']]['answer'] ?? null;
                                                    $answers[$aKey]['customer_answer_date'] = $customerAnswerMap[$answer['id']]['date'] ?? null;
                                                }
                                            }
                                        $questions[$qKey]["options"] = $answers ?: [];
                                    }
                                }
                                $data["forms"][$key]["questions"] = $questions ?: [];
                            }
                        }
                        $data["company_id"] = $data['customer']["companyId"];
                    }
                    return view('admin/customer/customer_consent_form_history',$data);
                } else {
                    return redirect()->route('dashboard');
                }
            } else {
                return redirect()->route('admin');
            }
        }
    }
