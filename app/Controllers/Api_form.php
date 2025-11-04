<?php 
    namespace App\Controllers;

    use CodeIgniter\RESTful\ResourceController;
    use CodeIgniter\API\ResponseTrait;
    use App\Models\CustomerConsentModel;
    use App\Models\WebsiteEntry;

    class Api_form extends ResourceController
    {
        use ResponseTrait;
        protected $helpers = ["custom"];

        public function submit_consent_form()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id','customer_id','signature');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "consent_form") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $signatureData = $post["signature"];
                
                $image = str_replace('data:image/png;base64,', '', $signatureData);
                $image = str_replace(' ', '+', $image);
                $imageData = base64_decode($image);
                
                $fileName = 'signature_' . time() . '.png';
                $uploadPath = FCPATH . 'public/uploads/signatures/';
        
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                file_put_contents($uploadPath . $fileName, $imageData);
                
                $insert_data = array(
                    'customer_id' => $post["customer_id"],
                    'company_id' => $post["company_id"],
                    'date' => $post["consent_date"],
                    'signature' => $fileName,
                );
                $model = new CustomerConsentModel;
                $model->insert($insert_data);
                
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Info found";
                $response[RESPONSE_DATA] = $uploadPath;
                return $this->respond($response);
            }
        }
        
        public function available_dates()
        {
            $post = $this->request->getVar();
            $input_parameter = array('key','tag','company_id');
            $validation = ParamValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL)
            {
                return $this->respond($validation);
            } else if($post['key'] != APP_KEY || $post['tag'] != "available_dates") {
                $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                $response[RESPONSE_MESSAGE] = RESPONSE_INVALID_KEY;
                return $this->respond($response);
            } else {
                $_date = date("Y-m-d H:i:s",strtotime("-15 minutes"));
                $where = ["company_id" => $post["company_id"],"customer_id" => $post["customer_id"]];
                $model = new WebsiteEntry;
                $entries = $model->where($where)->where("datetime >=",$_date)->get()->getResultArray();
                $serviceIds = array_column($entries, "service_id");

                $db = db_connect();
                $query = $db->table("staff_timings st");
                $query->select("st.date, st.staffId");
                $query->join("staff_services ss", "ss.staff_id = st.staffId");
                $query->where("st.companyId", $post["company_id"]);
                $query->where("st.date >=", date('Y-m-d'));
                $query->whereIn("ss.service_id", $serviceIds);
                $query->groupBy(["st.date", "st.staffId"]);
                $result = $query->get()->getResultArray();
                if ($result) {
                    $dates = array_unique(array_column($result, "date"));

                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
                    $response[RESPONSE_MESSAGE] = "";
                    $response[RESPONSE_DATA] = $dates;
                } else {
                    $response[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
                    $response[RESPONSE_MESSAGE] = "Sorry, no date available.";
                    $response[RESPONSE_DATA] = array();
                }
                return $this->respond($response);
            }
        }
    }