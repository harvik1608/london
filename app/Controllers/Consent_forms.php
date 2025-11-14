<?php
    namespace App\Controllers;

    use App\Models\ConsertFormModel;
    use App\Models\ConsertFormQuestionModel;
    use App\Models\ConsertFormQuestionAnswerModel;

    class Consent_forms extends BaseController
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
                if(check_permission("consent_forms")) {
                    $model = new ConsertFormModel;
                    $data["consent_forms"] = $model->where("company_id",static_company_id())->where("deleted_by",0)->orderBy("id","desc")->get()->getResultArray();
                    return view('admin/consent_form/list',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function new()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("consent_forms")) {
                    $data["consent_form"] = [];
                    return view('admin/consent_form/add_edit',$data);
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

                $form_data = array();
                $form_data["title"] = rawurlencode($post["title"]);
                $form_data["description"] = $post["description"];
                $form_data["duration"] = $post["duration"];
                $form_data["company_id"] = static_company_id();
                $form_data["is_active"] = $post["is_active"];
                $form_data["created_by"] = $createdBy["id"];
                $form_data["created_at"] = date("Y-m-d H:i:s");
                $model = new ConsertFormModel;
                $model->insert($form_data);
                $consent_form_id = $model->getInsertID();

                $consent_question_data = array();
                for($i = 0; $i < count($post["no"]); $i ++) {
                    $model = new ConsertFormQuestionModel;
                    $model->insert([
                        "consert_form_id" => $consent_form_id,
                        "title" => rawurlencode($post["question_title"][$i]),
                        "company_id" => static_company_id(),
                        "is_active" => $post["is_active"],
                        "created_by" => $createdBy["id"],
                        "created_at" => date("Y-m-d H:i:s")
                    ]);
                    $consent_form_question_id = $model->getInsertID();

                    $model = new ConsertFormQuestionAnswerModel;
                    for($j = 0; $j < count($post["question"]); $j ++) {
                        if(!empty($post["question"][$post["no"][$i]])) {
                            for($k = 0; $k < count($post["question"][$post["no"][$i]]); $k ++) {
                                $model->insert([
                                    "consert_form_id" => $consent_form_id,
                                    "consent_form_question_id" => $consent_form_question_id,
                                    "question" => rawurlencode($post["question"][$post["no"][$i]][$k]),
                                    "answer_type" => rawurlencode($post["answer_type"][$post["no"][$i]][$k]),
                                    "option" => rawurlencode($post["option"][$post["no"][$i]][$k]),
                                    "company_id" => static_company_id(),
                                    "is_active" => $post["is_active"],
                                    "created_by" => $createdBy["id"],
                                    "created_at" => date("Y-m-d H:i:s")
                                ]); 
                            }
                        }
                    }
                }
                $session->setFlashData('success','Consent Form added successfully');
                $ret_arr = array("status" => 1);
                echo json_encode($ret_arr);
                exit;
            }
        }

        public function edit($id)
        {
            $session = session();
            if($session->get('userdata'))
            {
                if(check_permission("consent_forms")) {
                    $model = new ConsertFormModel();
                    $data['consent_form'] = $model->where('id',$id)->first();
                    if($data['consent_form']) {
                        $model = new ConsertFormQuestionModel();
                        $data['consent_form_questions'] = $model->select("id,title")->where('consert_form_id',$id)->get()->getResultArray();
                        if($data['consent_form_questions']) {
                            $model = new ConsertFormQuestionAnswerModel;
                            foreach($data['consent_form_questions'] as $key => $val) {
                                $items = $model->select("id,question,answer_type,option")->where("consent_form_question_id",$val["id"])->get()->getResultArray();
                                $data['consent_form_questions'][$key]["items"] = $items;
                            }
                        }
                        return view('admin/consent_form/add_edit',$data);
                    } else {
                        return redirect()->route('consent_forms');
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

                $form_data = array();
                $form_data["title"] = rawurlencode($post["title"]);
                $form_data["description"] = $post["description"];
                $form_data["duration"] = $post["duration"];
                $form_data["company_id"] = static_company_id();
                $form_data["is_active"] = $post["is_active"];
                $form_data["updated_by"] = $createdBy["id"];
                $form_data["updated_at"] = date("Y-m-d H:i:s");
                $model = new ConsertFormModel;
                $data = $model->update($id,$form_data);
                if($data) {
                    if(isset($post["question_title"]) && !empty($post["question_title"])) {
                        for($i = 0; $i < count($post["question_title"]); $i ++) {
                            $model = new ConsertFormQuestionModel;
                            if(isset($post["consert_form_question_id"][$i]) && $post["consert_form_question_id"][$i] != "") {
                                $form_question_data = array();
                                $form_question_data["title"] = rawurlencode($post["question_title"][$i]);
                                $form_question_data["company_id"] = static_company_id();
                                $form_question_data["updated_by"] = $createdBy["id"];
                                $form_question_data["updated_at"] = date("Y-m-d H:i:s");
                                $data = $model->update($post["consert_form_question_id"][$i],$form_question_data);
                                $consent_form_question_id = $post["consert_form_question_id"][$i];
                            } else {
                                $model->insert([
                                    "consert_form_id" => $id,
                                    "title" => rawurlencode($post["question_title"][$i]),
                                    "company_id" => static_company_id(),
                                    "is_active" => $post["is_active"],
                                    "created_by" => $createdBy["id"],
                                    "created_at" => date("Y-m-d H:i:s")
                                ]);
                                $consent_form_question_id = $model->getInsertID();   
                            }

                            $model = new ConsertFormQuestionAnswerModel;
                            for($j = 0; $j < count($post["question"]); $j ++) {
                                if(!empty($post["question"][$post["no"][$i]])) {
                                    for($k = 0; $k < count($post["question"][$post["no"][$i]]); $k ++) {
                                        if(isset($post["consent_form_question_answer_id"][$post["no"][$i]][$k]) && $post["consent_form_question_answer_id"][$post["no"][$i]][$k] != "") {
                                            $form_question_answer_data = array();
                                            $form_question_answer_data["question"] = rawurlencode($post["question"][$post["no"][$i]][$k]);
                                            $form_question_answer_data["answer_type"] = rawurlencode($post["answer_type"][$post["no"][$i]][$k]);
                                            $form_question_answer_data["option"] = rawurlencode($post["option"][$post["no"][$i]][$k]);
                                            $form_question_answer_data["company_id"] = static_company_id();
                                            $form_question_answer_data["updated_by"] = $createdBy["id"];
                                            $form_question_answer_data["updated_at"] = date("Y-m-d H:i:s");
                                            $data = $model->update($post["consent_form_question_answer_id"][$post["no"][$i]][$k],$form_question_answer_data);   
                                        } else {
                                            $model->insert([
                                                "consert_form_id" => $id,
                                                "consent_form_question_id" => $consent_form_question_id,
                                                "question" => rawurlencode($post["question"][$post["no"][$i]][$k]),
                                                "answer_type" => rawurlencode($post["answer_type"][$post["no"][$i]][$k]),
                                                "option" => rawurlencode($post["option"][$post["no"][$i]][$k]),
                                                "company_id" => static_company_id(),
                                                "is_active" => $post["is_active"],
                                                "created_by" => $createdBy["id"],
                                                "created_at" => date("Y-m-d H:i:s")
                                            ]); 
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $session->setFlashData('success','Consent Form edited successfully');
                    $ret_arr = array("status" => 1);
                } else {
                    $ret_arr = array("status" => 0,"message" => "Error");
                }
                $ret_arr = array("status" => 1);
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

        public function add_more_row()
        {
            $data["no"] = time();
            $html = view('admin/consent_form/add_more_row',$data);
            return json_encode(["html" => $html]);
        }

        public function add_more_option()
        {
            $data["no"] = "row-".time();
            $data["row_no"] = $this->request->getVar("row_no");
            $html = view('admin/consent_form/add_more_option',$data);
            return json_encode(["html" => $html]);
        }

        public function delete_more_option()
        {
            $post = $this->request->getVar();
            
            $model = new ConsertFormQuestionAnswerModel;
            $model->where("id",$post["id"])->delete();

            echo json_encode(["status" => 1]);
            exit;
        }

        public function delete_more_question()
        {
            $post = $this->request->getVar();
            
            $model = new ConsertFormQuestionModel;
            $model->where("id",$post["id"])->delete();

            $model = new ConsertFormQuestionAnswerModel;
            $model->where("consent_form_question_id",$post["id"])->delete();

            echo json_encode(["status" => 1]);
            exit;
        }
    }
