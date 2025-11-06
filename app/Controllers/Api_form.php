<?php 
    namespace App\Controllers;

    use CodeIgniter\RESTful\ResourceController;
    use CodeIgniter\API\ResponseTrait;
    use App\Models\CustomerConsentModel;
    use App\Models\WebsiteEntry;
    use App\Models\WeekendDiscount;
    use App\Models\StaffTimingModel;
    use App\Models\CompanyModel;

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
                    $dates = array_values(array_unique(array_column($result, "date")));

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

        public function fetch_available_slots()
        {
            $post = $this->request->getVar();
            $input_parameter = array('date','company_id','customer_id');
            $validation = paramMobileValidation($input_parameter, $post);

            if($validation[RESPONSE_STATUS] == RESPONSE_FLAG_FAIL_MOBILE) {
                return $this->respond($validation);
            } else {
                $date = date("Y-m-d",strtotime($post["date"]));
                $shortTimestamp = strtotime($date);
                $shortDay = strtolower(date("D", $shortTimestamp));

                $date_15 = date("Y-m-d H:i:s",strtotime("-15 minutes"));
                $where = ["company_id" => $post["company_id"],"customer_id" => $post["customer_id"]];
                
                $model = new WeekendDiscount;
                $discounts = $model->select("id,sdate,edate,week_days,percentage,service_ids")->where("sdate <=",$date)->where("edate >=",$date)->where("company_id",$post["company_id"])->get()->getResultArray();
                if($discounts) {
                    $model = new WebsiteEntry;
                    $entries = $model->where($where)->where("datetime >=",$date_15)->get()->getResultArray();
                    if($entries) {
                        foreach($discounts as $discount) {
                            $string = $discount["service_ids"];
                            $numbers = explode(",", $string);
                            $week_days = [];
                            if($discount["week_days"] != "") {
                                $week_days = explode(",",$discount["week_days"]);
                            }
                            
                            foreach($entries as $entry) {
                                if(in_array($shortDay,$week_days)) {
                                    if(in_array($entry["service_id"], $numbers)) {    
                                        $discount_amount = ($entry["amount"]*$discount["percentage"])/100;
                                        $model->update($entry["id"],["discount_amount" => $entry["amount"]-$discount_amount]);
                                        // $discount_amount = $entry["amount"]-$discount["percentage"];
                                        // $model->update($entry["id"],["discount_amount" => $discount_amount]);
                                    }
                                } else {
                                    $model->update($entry["id"],["discount_amount" => 0]);
                                }
                            }
                        }
                    }
                } else {
                    $model = new WebsiteEntry;
                    $entries = $model->where($where)->where("datetime >=",$date_15)->get()->getResultArray();
                    foreach($entries as $entry) {
                        $model->update($entry["id"],["discount_amount" => 0]);
                    }
                }
                
                $model = new WebsiteEntry;
                $entries = $model->where($where)->where("datetime >=",$date_15)->get()->getResultArray();
                if($entries) {
                    foreach($entries as $entry) {
                        $is_service_available = 0;

                        $model = db_connect();
                        $check_staff = $model->table("staff_services ss");
                        $check_staff = $check_staff->join("staffs s","s.id=ss.staff_id");
                        $check_staff = $check_staff->select("ss.staff_id");
                        $check_staff = $check_staff->where("ss.service_id",$entry["service_id"]);
                        $check_staff = $check_staff->where(['s.user_type' => 1,"s.is_active" => 1,"s.is_deleted" => 0]);
                        $staff = $check_staff->get()->getResultArray();
                    
                        // $model = new StaffServiceModel;
                        // $staff = $model->select("staff_id")->where("service_id",$entry["service_id"])->get()->getResultArray();
                        if($staff) {
                            $staff_ids = array_column($staff,"staff_id");
                            $model = new StaffTimingModel;
                            $count = $model->whereIn("staffId",$staff_ids)->where("date",$date)->get()->getNumRows();
                            if($count > 0) {
                                $is_service_available = 1;
                            }
                        }
                        $model = new WebsiteEntry;
                        $model->update($entry["id"],["is_final" => $is_service_available]);
                    }   
                }
                $currency = "";
                $openingTime = strtotime("09:00:00");
                $closingTime = strtotime("21:00:00");
                $free_slots = [];
                $model = new CompanyModel;
                $company = $model->select("company_stime,company_etime,company_sunday_stime,company_sunday_etime,currency")->where("id",$post["company_id"])->first();
                if($company) {
                    $currency = $company["currency"];
                    if(date("l",strtotime($date)) == "Sunday") {
                        $openingTime = strtotime($company["company_sunday_stime"]); 
                        $closingTime = strtotime($company["company_sunday_etime"]);
                    } else {
                        $openingTime = strtotime($company["company_stime"]); 
                        $closingTime = strtotime($company["company_etime"]);
                    }
                }
                $total_duration = 0;
                $model = new WebsiteEntry;
                $entries = $model->where($where)->where("datetime >=", $date_15)->where("is_final",1)->get()->getResultArray();
                if($entries) {
                    foreach($entries as $entry) {
                        $total_duration += (int) $entry["duration"];
                    }
                }
                $serviceIds = array_column($entries, "service_id");
                if(!empty($serviceIds)) {
                    $db = db_connect();

                    $query = $db->table("staff_services ss");
                    $query->select("ss.staff_id, ss.service_id, st.date, st.stime AS staff_stime, st.etime AS staff_etime");
                    $query->join("staff_timings st", "st.staffId = ss.staff_id");
                    $query->where("st.companyId", $post["company_id"]);
                    $query->where("st.date", $date);
                    $query->whereIn("ss.service_id", $serviceIds);
                    $staffData = $query->get()->getResultArray();   
                    if(!empty($staffData)) {
                        $service_staff_map = $staff_timings = [];
                        foreach ($staffData as $row) {
                            $service_staff_map[$row["service_id"]][] = $row["staff_id"];
                            $staff_timings[$row["staff_id"]] = [
                                "stime" => $row["staff_stime"],
                                "etime" => $row["staff_etime"]
                            ];
                        }
                        $time_slots = generate_slots($openingTime,$closingTime,$total_duration);
                        
                        $busyQuery = $db->table("carts c");
                        $busyQuery = $busyQuery->select("c.stime, c.etime, c.staffId");
                        $busyQuery = $busyQuery->where("c.date", $date);
                        $busyQuery = $busyQuery->where("c.isComplete", "N");
                        $busyQuery = $busyQuery->where("c.is_cancelled", 0);
                        $busyQuery = $busyQuery->where("c.companyId", $post["company_id"]);
                        $busySlots = $busyQuery->get()->getResultArray();
                        if(!empty($busySlots)) {
                            if (!empty($staff_timings)) {
                                $openingTime = min(array_map(fn($t) => strtotime($t['stime']), $staff_timings));
                                $closingTime = max(array_map(fn($t) => strtotime($t['etime']), $staff_timings));
                            }
                            $isStaffBusy = function ($staffId, $slotStart, $slotEnd) use ($busySlots) {
                                foreach ($busySlots as $busy) {
                                    if ($busy['staffId'] == $staffId) {
                                        if (
                                            ($slotStart >= $busy['stime'] && $slotStart < $busy['etime']) ||
                                            ($slotEnd > $busy['stime'] && $slotEnd <= $busy['etime']) ||
                                            ($slotStart <= $busy['stime'] && $slotEnd >= $busy['etime'])
                                        ) {
                                            return true;
                                        }
                                    }
                                }
                                return false;
                            };

                            foreach ($time_slots as $slot) {
                                $slotStart = $slot["stime"];
                                $slotEnd = $slot["etime"];
                                $slotValid = true;

                                foreach ($serviceIds as $serviceId) {
                                    $staffs = $service_staff_map[$serviceId] ?? [];
                                    $hasAvailableStaff = false;

                                    foreach ($staffs as $sid) {
                                        // Skip if staff timing not found (safety check)
                                        if (!isset($staff_timings[$sid])) continue;

                                        $staffStart = $staff_timings[$sid]['stime'];
                                        $staffEnd = $staff_timings[$sid]['etime'];

                                        if ($slotStart < $staffStart || $slotEnd > $staffEnd) {
                                            continue;
                                        }

                                        // Skip if busy
                                        if (!$isStaffBusy($sid, $slotStart, $slotEnd)) {
                                            $hasAvailableStaff = true;
                                            break;
                                        }
                                    }

                                    if (!$hasAvailableStaff) {
                                        $slotValid = false;
                                        break;
                                    }
                                }

                                if ($slotValid) {
                                    $free_slots[] = [
                                        "stime" => $slotStart,
                                        "etime" => $slotEnd
                                    ];
                                }
                            }
                        } else {
                            $time_slots = generate_slots($openingTime,$closingTime,$total_duration);
                        }
                    }
                }
                $available_staff_ids = "";
                $db = db_connect();
                $query = $db->table("staff_timings st");
                $query = $query->join("staffs s","s.id=st.staffId");
                $query = $query->select("st.staffId,st.etime");
                $query = $query->where("st.date",$date);
                $query = $query->where("st.companyId",$post["company_id"]);
                $query = $query->where(['s.user_type' => 1,"s.is_active" => 1,"s.is_deleted" => 0]);
                $result = $query->get()->getResultArray();
                if ($result) {
                    $staff_ids = array_column($result, "staffId");
                    $available_staff_ids = implode(",", $staff_ids);
                }
                $response["data"] = array("staff_ids" => $available_staff_ids,"slots" => $free_slots);
                return $this->respond($response);
            }
        }
    }