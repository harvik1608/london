<?php
    use App\Models\CompanyModel;
    use App\Models\ServiceModel;
    use App\Models\DiscountTypeModel;
    use App\Models\PaymentTypeModel;
    use App\Models\EmpModel;
    use App\Models\ServicePriceModel;
    use App\Models\WeekendDiscount;
    use App\Models\EntryModel;
    use App\Models\ConfirmationMessage;

    function preview($data)
    {
        echo "<pre>";
        print_r ($data);
        exit;
    }

    function get_companies()
    {
        $model = new CompanyModel;
        $companies = $model->select('id,company_name')->where("isActive",'1')->get()->getResultArray();
        return $companies;
    }

    function format_date($flag,$date = "")
    {
        switch($flag)
        {
            case 1:
            $date = date("Y-m-d H:i:s");
            break;

            case 2:
            $date = date("d/m/Y",strtotime($date));
            break;

            case 3:
            $date = date("d/m/Y h:i A",strtotime($date));
            break;

            case 4:
            $date = date("M d, Y",strtotime($date));
            break;

            case 5:
            $date = time();
            break;

            case 6:
            $date = date("Y-m-d",strtotime($date));
            break;

            case 7:
            $date = strtotime($date);
            break;

            case 8:
            $date = strtotime(date("Y-m-d"));
            break;

            case 9:
            $date = date("H",strtotime($date));
            break;

            case 10:
            $date = date("H:i:s",strtotime($date));
            break;

            case 11:
            $date = date("h:i A",strtotime($date));
            break;

            case 12:
            $date = strtotime(date("H:i:s"));
            break;

            case 13:
            $date = date("d M, Y",strtotime($date));
            break;

            case 14:
            $date = $date == "" ? date("H:i:s") : date("H:i:s",strtotime($date));
            break;

            case 15:
            $date = date("Y-m-d");
            break;
        }
        return $date;
    }

    function format_text($flag,$text = "")
    {
        switch($flag)
        {
            case 1:
            $text = ucwords(strtolower($text));
            break;

            case 2:
            $text = strtoupper($text);
            break;

            case 3:
            $text = str_replace("/", "-", $text);
            break;

            case 4:
            $text = trim($text);
            break;            
        }
        return $text;
    }

    function short_str($str,$length)
    {
        $strlen = strlen($str);
        if($length > $strlen)
            return substr($str,0,$length);
        else {
            $substr = substr($str,0,$length)."...";
            return $substr;
        }
    }

    function remove_file($file)
    {
        if($file != "" && file_exists("public/upload/".$file))
            unlink("public/upload/".$file);
    }

    function timezone($from_timezone,$to_timezone,$datetime)
    {
        if($datetime != "")
        {
            $utc_date = DateTime::createFromFormat(
                "Y-m-d H:i:s",
                $datetime,
                new DateTimeZone($from_timezone)
            );
            $acst_date = clone $utc_date;
            $acst_date->setTimeZone(new DateTimeZone($to_timezone));

            return $acst_date->format("Y-m-d H:i:s");
        } else {
            return "";
        }
    }

    function setting($key = "")
    {
        if($key != "")
        {
            $model = new SettingModel();
            $data = $model->select('setting_val')->where('setting_key',$key)->first();
            return $data['setting_val'];
        }
    }

    function callApi($url,$params)
    {
        error_reporting(0);
        $cURLConnection = curl_init();

        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_POST, 1);
        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $params);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));

        $apiResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);
        $response = json_decode($apiResponse,true);
        return $response;
    }

    function ParamValidation($paramarray,$data)
    {
        $NovalueParam = array();
        foreach($paramarray as $val)
        {
            if(!isset($data[$val]) || $data[$val] == '')
            {
                $NovalueParam[] = $val;
            }
        }
        $returnArr = array();
        if(is_array($NovalueParam) && count($NovalueParam)>0)
        {
            $returnArr[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL;
            $returnArr[RESPONSE_MESSAGE] = 'Sorry, You missed '.implode(',',$NovalueParam).' parameters';
        } else {
            $returnArr[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
        }
        return $returnArr;
    }

    function paramMobileValidation($paramarray,$data)
    {
        $NovalueParam = array();
        foreach($paramarray as $val)
        {
            if(!isset($data[$val]) || $data[$val] == '')
            {
                $NovalueParam[] = $val;
            }
        }
        $returnArr = array();
        if(is_array($NovalueParam) && count($NovalueParam)>0)
        {
            $returnArr[RESPONSE_STATUS] = RESPONSE_FLAG_FAIL_MOBILE;
            $returnArr[RESPONSE_MESSAGE] = 'Sorry, You missed '.implode(',',$NovalueParam).' parameters';
        } else {
            $returnArr[RESPONSE_STATUS] = RESPONSE_FLAG_SUCCESS;
        }
        return $returnArr;
    }

    function services()
    {
        $model = new ServiceModel;
        $services = $model->select("id,slug,name")->where("is_active","1")->orderBy("id","desc")->limit(5)->get()->getResultArray();
        return $services;
    }

    function slug($string, $spaceRepl = "-")
    {
        $string = str_replace("&", "and", $string);

        $string = preg_replace("/[^a-zA-Z0-9 _-]/", "", $string);

        $string = strtolower($string);

        $string = preg_replace("/[ ]+/", " ", $string);

        $string = str_replace(" ", $spaceRepl, $string);

        return $string;
    }

    function month()
    {
        $month = array(array("id" => 1,"name" => "January"),array("id" => 2,"name" => "February"),array("id" => 3,"name" => "March"),array("id" => 4,"name" => "April"),array("id" => 5,"name" => "May"),array("id" => 6,"name" => "June"),array("id" => 7,"name" => "July"),array("id" => 8,"name" => "August"),array("id" => 9,"name" => "September"),array("id" => 10,"name" => "October"),array("id" => 11,"name" => "November"),array("id" => 12,"name" => "December"));
        return $month;
    }

    function send_notification($message)
    {
        $model = new SurveyModel;
        $users = $model->select('id')->where('usertype','1')->where('is_active','1')->where('is_email_verified','1')->where('is_account_verified','1')->get()->getResultArray();
        if($users)
        {
            foreach($users as $key => $val)
            {
                $model = new SurveyModel();
                $post = array();
                $post['user_id'] = $val['id'];
                $post['message'] = $message;
                $post['isRead'] = '0';
                $post['createdAt'] = format_date(1);
                $post['updatedAt'] = format_date(1);
                $model->insert($post);
            }
        }
    }

    function companies()
    {
        $model = new CompanyModel();
        $companies = $model->select('id,company_name')->where('isActive','1')->get()->getResultArray();
        return $companies;
    }

    function static_company_id()
    {
        $session = session();
        return $session->get('companyId');
    }

    function static_company_currency()
    {
        $model = new CompanyModel;
        $company = $model->select("currency")->where('id',static_company_id())->first();
        if(!empty($company) && $company["currency"] != "") {
            return $company["currency"];
        } else {
            return "£";
        }
    }

    function static_company_timezone()
    {
        $model = new CompanyModel;
        $company = $model->select("timezone")->where('id',static_company_id())->first();
        if(!empty($company) && $company["timezone"] != "") {
            return $company["timezone"];
        } else {
            return "Asia/Kolkata";
        }
    }

    function company_info($id = "",$column = "")
    {
        if($id == "") {
            $id = static_company_id();
        }
        $model = new CompanyModel();
        if($column == "") {
            $company = $model->where('id',$id)->first();
            return $company;
        } else {
            $company = $model->select($column)->where('id',$id)->first();
            return $company[$column];
        }
    }

    function get_service_groups($company_id = 1)
    {
        $ids = array();
        $model = new CompanyModel;
        $group = $model->select("company_service_groups")->where("id",$company_id)->first();
        if(!empty($group)) {
            $ids = explode(",",$group["company_service_groups"]);
        }
        if(!empty($ids)) {
            $model = new ServiceModel();
            $services = $model->select('id,name,avatar,created_at')->whereIn('id',$ids)->where(array("is_active" => 1,"is_deleted" => 0))->orderBy("position","asc")->get()->getResultArray();
            return $services;
        } else {
            return $ids;
        }
    }

    function timepicker($start = 0,$end = 23,$duration = 5)
    {
        $start  = (int) $start;
        $end    = (int) $end;
        $str    = "";
        $_no    = 0;
        for($i = $start;$i <= $end; $i ++)
        {
            for($j = 0;$j <=59; $j = $j+$duration)
            {
                $_no++;
                $show_time = date("h:i A",strtotime($i.":".$j.":00"));
                $hidden_time = date("H:i:s",strtotime($i.":".$j.":00"));
                $hidden_time_m = date("H:i",strtotime($i.":".$j));
                $str .= "<option value=".$hidden_time." name='".$_no."'>".$show_time."</option>";
            }
        }
        return $str;
    }

    function getDatesFromRange($start, $end, $format = 'Y-m-d') 
    { 
        $array = array(); 
        $interval = new DateInterval('P1D'); 
      
        $realEnd = new DateTime($end); 
        $realEnd->add($interval); 
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 
        foreach($period as $date) {                  
            $array[] = $date->format($format);  
        } 
        return $array; 
    }

    function company_timing($company_id)
    {
        $model = new CompanyModel;
        $company = $model->where("id",$company_id)->first();
        return $company;
    }

    function getDiscountList($company_id)
    {
        $model = new DiscountTypeModel();
        $discount_types = $model->select("id,name,discount_type,discount_value")->where("is_active",'1')->where("company_id",$company_id)->orderBy("position","asc")->get()->getResultArray();
        return $discount_types;
    }

    function getPaymentList($company_id)
    {
        $model = new PaymentTypeModel();
        $payment_types = $model->select("id,name")->where("is_active",'1')->where('is_deleted',0)->where("company_id",$company_id)->orderBy("position","asc")->get()->getResultArray();
        return $payment_types;
    }

    function company($columns = "")
    {
        $api_data = array("key" => APP_KEY,"tag" => "company","company_id" => COMPANY_ID,"columns" => $columns);
        $response = callApi(API_BASE_URL."api/company",$api_data);
        return $response;
    }

    function company_treatments()
    {
        $treatments = array();
        $api_data = array("key" => APP_KEY,"tag" => "treatments","company_id" => COMPANY_ID);
        $response = callApi(API_BASE_URL."api/treatments",$api_data);
        if(isset($response["status"]) && $response["status"] == RESPONSE_FLAG_SUCCESS)
            $treatments = $response["data"];
        
        return $treatments;
    }

    function check_permission($module)
    {
        $session = session();
        $udata = $session->get("userdata");
        if(isset($udata["user_type"])) {
            if($udata["user_type"] == 0) {
                return 1;
            } else {
                $model = new EmpModel;
                $staff = $model->select("roles")->where("id",$udata["id"])->first();
                if(!empty($staff)) {
                    if($staff["roles"] != "") {
                        $roles = explode(",",$staff["roles"]);
                        if(in_array($module,$roles)) {
                            return 1;
                        } else {
                            return 0;    
                        }
                    } else {
                        return 0;    
                    }
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }

    function add_google_contact($custdata)
    {
        $resource_id = "";
        try {
            $model = new CompanyModel;
            $company = $model->select("credential_file")->where("id",static_company_id())->first();

            $baseURL = substr(APPPATH,0,strlen(APPPATH)-4);
            $jsonfile = $baseURL."public/google/".$company["credential_file"];

            $client = new \Google_Client();
            $client->setAuthConfig($jsonfile);
            $client->addScope('https://www.googleapis.com/auth/contacts.readonly');
            $client->setRedirectUri('https://insightqera.com/beauty/google');
            $client->setAccessType('offline');
            $service = new \Google_Service_PeopleService($client);

            $company = $model->select("google_code")->where("id",static_company_id())->first();
            if(!empty($company)) {
                if($company["google_code"] != "") {
                    $access_token = json_decode($company["google_code"],true);
                    $client->setAccessToken($access_token);

                    $newContact = new \Google_Service_PeopleService_Person();
                    $newContact->setNames([new \Google_Service_PeopleService_Name([
                        'givenName' => $custdata["name"]
                    ])]);
                    $newContact->setPhoneNumbers([new \Google_Service_PeopleService_PhoneNumber([
                        'value' => $custdata["phone"],
                        'type' => 'mobile'
                    ])]);
                    $newContact->setEmailAddresses([new \Google_Service_PeopleService_EmailAddress([
                        'value' => $custdata["email"]
                    ])]);
                    $result = $service->people->createContact($newContact);
                    $resource_id = $result->getResourceName();
                }
            }
            return $resource_id;
        } catch (Exception $e) {
            return $resource_id;
        }
    }

    function update_google_contact($resourceId)
    {
        $resource_id = 201;
        try {
            $model = new CompanyModel;
            $company = $model->select("credential_file")->where("id",static_company_id())->first();

            $baseURL = substr(APPPATH,0,strlen(APPPATH)-4);
            $jsonfile = $baseURL."public/google/".$company["credential_file"];

            $client = new \Google_Client();
            $client->setAuthConfig($jsonfile);
            $client->addScope('https://www.googleapis.com/auth/contacts.readonly');
            $client->setRedirectUri('https://insightqera.com/beauty/google');
            $client->setAccessType('offline');
            $service = new \Google_Service_PeopleService($client);

            $company = $model->select("google_code")->where("id",static_company_id())->first();
            if(!empty($company)) {
                if($company["google_code"] != "") {
                    $access_token = json_decode($company["google_code"],true);
                    $client->setAccessToken($access_token);

                    $personId = $resourceId; // Replace with the actual person ID
                    $optParams = array('personFields' => 'names,emailAddresses'); // Specify fields you need
                    $person = $service->people->get($personId, $optParams);
                    $etag = $person->getEtag();                    
                    echo $etag;
                    
                    $updatedContact = new Google_Service_PeopleService_Person();
                    $updatedContact->setNames([new Google_Service_PeopleService_Name([
                        'givenName' => 'Bhavna',
                        'familyName' => 'Kahar GJ 1'
                    ])]);
                    $updatedContact->setEmailAddresses([new Google_Service_PeopleService_EmailAddress([
                        'value' => 'bhavikakahar23@gmail.com'
                    ])]);

                    // Update the contact (assuming you have the resource name)
                    $contactResourceName = $resourceId;
                    // $client->setHeaders(array(
                    //     'If-Match' => $etag
                    // ));
                    $result = $service->people->updateContact($contactResourceName, $updatedContact, array('updatePersonFields' => 'names,emailAddresses,phoneNumbers'));
                }
            }
            return $resource_id;
        } catch (Exception $e) {
            return $e;
        }
    }

    function delete_google_contact($resourceId)
    {
        $resource_id = 201;
        try {
            $model = new CompanyModel;
            $company = $model->select("credential_file")->where("id",static_company_id())->first();

            $baseURL = substr(APPPATH,0,strlen(APPPATH)-4);
            $jsonfile = $baseURL."public/google/".$company["credential_file"];

            $client = new \Google_Client();
            $client->setAuthConfig($jsonfile);
            $client->addScope('https://www.googleapis.com/auth/contacts.readonly');
            $client->setRedirectUri('https://insightqera.com/beauty/google');
            $client->setAccessType('offline');
            $service = new \Google_Service_PeopleService($client);

            $company = $model->select("google_code")->where("id",static_company_id())->first();
            if(!empty($company)) {
                if($company["google_code"] != "") {
                    $access_token = json_decode($company["google_code"],true);
                    $client->setAccessToken($access_token);

                    $contactResourceName = $resourceId;
                    $service->people->deleteContact($contactResourceName);
                    $resource_id = 200;
                }
            }
            return $resource_id;
        } catch (Exception $e) {
            return $resource_id;
        }
    }

    function calc_hours($date1,$date2)
    {
        $datetime_1 = $date1; 
        $datetime_2 = $date2; 
         
        $start_datetime = new DateTime($datetime_1); 
        $diff = $start_datetime->diff(new DateTime($datetime_2));
        return $diff->h;
    }
    
    function get_service_prices($service_id,$company_id = 0,$date = "",$uniq_id = '',$service_group_id = 0,$appointment_id = 0)
    {
        if($company_id == 0) {
            $company_id = static_company_id(); 
        }
        $json = $updated_price = [];
        $model = new ServicePriceModel;
        $price = $model->select("json")->where(array("service_id" => $service_id,"company_id" => $company_id))->where("bookedFrom","Y")->first();
        if($price) {
            if($price["json"] != "" && !is_null($price["json"])) {
                $json = json_decode($price["json"],true);

                // Calculate Discount
                $model = new WeekendDiscount;
                $discounts = $model->select("id,sdate,edate,week_days,percentage,service_ids")->where("sdate <=",$date)->where("edate >=",$date)->where("company_id",$company_id)->get()->getResultArray();
                if($discounts) {
                    foreach($discounts as $discount) {
                        $service_ids = [];
                        if($discount["service_ids"] != "") {
                            $service_ids = explode(",",$discount["service_ids"]);
                        }
                        $model = new EntryModel;
                        if(in_array($service_id,$service_ids)) {
                            if($discount["week_days"] != "") {
                                $days = explode(",",$discount["week_days"]);
                                if(in_array(trim(strtolower(date("D",strtotime($date)))),$days)) {
                                    $updated_price = $json;
                                    if(!empty($json)) {
                                        foreach($updated_price as $k => $v) {
                                            $is_added_in_cart = 0;
                                            if(isset($v['caption'])) {
                                                if($appointment_id == 0 || $appointment_id == "") {
                                                    $is_added_in_cart = $model->where(["uniq_id" => $uniq_id,"service_id" => $service_id,"is_removed_from_cart" => 0,'caption' => trim($v['caption'])])->get()->getNumRows();
                                                } else {
                                                    $is_added_in_cart = $model->where(["appointment_id" => $appointment_id,"uniq_id" => $uniq_id,"service_id" => $service_id,"is_removed_from_cart" => 0,'caption' => trim($v['caption'])])->get()->getNumRows();
                                                }
                                            }
                                            if($v["retail_price"] != "") {
                                                $new_retail_price = ($v["retail_price"]*$discount["percentage"])/100;
                                                $updated_price[$k]["special_price"] = $v["retail_price"]-$new_retail_price;
                                                // $new_retail_price = $v["retail_price"]-$discount["percentage"];
                                                // $updated_price[$k]["special_price"] = $new_retail_price;
                                            }
                                            $updated_price[$k]["is_added_in_cart"] = $is_added_in_cart;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } 
        }
        if(empty($updated_price)) {
            $updated_price = $json;
            if(!empty($json)) {
                $model = new EntryModel;
                foreach($updated_price as $k => $v) {
                    $is_added_in_cart = 0;
                    if(isset($v['caption'])) {
                        if($appointment_id == 0 || $appointment_id == "") {
                            $is_added_in_cart = $model->where(["uniq_id" => $uniq_id,"service_id" => $service_id,"is_removed_from_cart" => 0,'caption' => trim($v['caption'])])->get()->getNumRows();
                        } else {
                            $is_added_in_cart = $model->where(["appointment_id" => $appointment_id,"uniq_id" => $uniq_id,"service_id" => $service_id,"is_removed_from_cart" => 0,'caption' => trim($v['caption'])])->get()->getNumRows();
                        }
                    }
                    $updated_price[$k]["is_added_in_cart"] = $is_added_in_cart;
                }
            }
            return $updated_price;
            // return $json;
        } else {
            return $updated_price;
        }
    }

    function check_null_blank($val,$default_val = "")
    {
        if(!is_null($val) && $val != "") {
            return $val;
        } else {
            return $default_val;
        }
    }

    function check_null_value($val)
    {
        if(is_null($val)) {
            return "";
        } else {
            return $val;
        }
    }
    
    function send_email($to,$subject,$message,$company,$ip = "")
	{
	    $config = [
            'protocol'    => 'smtp',
            'SMTPHost'    => 'smtp.gmail.com',
            'SMTPUser'    => $company["from_email"],
            'SMTPPass'    => $company['smtp_password'],
            'SMTPPort'    => 587,
            'SMTPCrypto'  => 'tls',
            'mailType'    => 'html',
            'charset'     => 'utf-8',
        ];
        $email = \Config\Services::email();
        $email->initialize($config);
        $email->setFrom($company["from_email"], $company["from_name"]);
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($message);
        if ($email->send()) {
            $model = new ConfirmationMessage; 
            $model->insert(["sent_to" => $to,"message" => $message,"type" => 1,"company_id" => isset($company["id"]) ? $company["id"] : 0,"date" => date("Y-m-d H:i:s"),"is_sent" => 1,'ip' => $ip]); 
            $messageId = $model->getInsertID();
            return 200;
        } else {
            return $email->printDebugger();
        }
        $email->clear(true);
	}
	
	function callWhatsapp($to, $body, $companId)
    {
        $model = new CompanyModel();
        $company = $model->select("wa_phone_id,wa_token")->where("id",$companId)->first();
        if ($company == false) {
            return false;
        }
        $whatsappPhoneId = $company['wa_phone_id'];
        $whatsappToken = $company['wa_token'];
        
        $model = new ConfirmationMessage;
        $model->insert(["sent_to" => $to,"message" => json_encode($body),"type" => 2,"company_id" => $companId,"date" => date("Y-m-d H:i:s")]);
        $messageId = $model->getInsertID();
        
    
        if ($whatsappPhoneId && $whatsappToken) {
            $data = [
                "messaging_product" => "whatsapp",
                "to" => $to,
                "type" => "template",
                "template" => [
                    "name" => "appointment_booking",
                    "language" => [
                        "code" => "en_GB"
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $body[0]
                                ],
                                [
                                    "type" => "text",
                                    "text" => $body[1]
                                ],
                                [
                                    "type" => "text",
                                    "text" => $body[2]
                                ],
                                [
                                    "type" => "text",
                                    "text" => $body[3]
                                ]
                            ]
                        ]
    
                    ]
                ]
            ];
            return apiCall($whatsappPhoneId, $whatsappToken, $data, $messageId);
        }
    
        return false;
    }
    
    function apiCall($whatsappPhoneId, $whatsappToken, $data, $messageId = 0)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v17.0/'.$whatsappPhoneId.'/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$whatsappToken
            ),
        ));
        $response = curl_exec($curl);
        
        curl_close($curl);
        $data = json_decode($response, 1);
        if (isset($data['messages']) && isset($data['messages'][0]) && $data['messages'][0]['id'] != '') {
            if($messageId != 0) {
                $model = new ConfirmationMessage;
                $model->update($messageId,["is_sent" => 1]);
            }
            return true;
        }
        return false;
    }
    
    function format_datetime($datetime,$flag) // 1 - date, 2 - time
    {
        if($flag == 1) {
            return date("d/m/Y",strtotime($datetime));
        } else if($flag == 2) {
            return date("h:i A",strtotime($datetime));
        } else {
            return date("d/m/Y h:i A",strtotime($datetime));
        }
    }
    
    function send_whatsapp_msg($appointment_id)
    {
        $model = db_connect();
        $result = $model->table("appointments a");
        $result = $result->join("customers c","c.id=a.customerId");
        $result = $result->select("a.companyId,a.bookingDate,c.name,c.email,c.phone");
        $result = $result->where(["a.id" => $appointment_id]);
        $appointment = $result->get()->getRowArray();
        if($appointment) {
            $items = $model->table("carts c");
            $items = $items->select("c.id,c.stime,c.etime,c.serviceNm,c.amount,c.duration");
            $items = $items->where(["c.appointmentId" => $appointment_id]);
            $services = $items->get()->getResultArray();
            if($services) {
                $times = array_column($services, 'stime');
                $earliest = min($times);
                $cart_str = "";
                foreach($services as $service) {
                    $cart_str .= $service["serviceNm"]." - £".$service["amount"].", ";
                }
                $cart_str = substr($cart_str,0,strlen($cart_str)-2);
                $cart_str = strip_tags($cart_str);
                if(in_array($appointment["companyId"],[1,3])) {
                    $datetime = $appointment["bookingDate"];
                    $timestamp = strtotime($datetime);
                    $formatted = date("l, jS F Y", $timestamp);
                        
                    $body = [];
                    $body[] = $appointment["name"];
                    $body[] = $formatted;
                    if($appointment["companyId"] == 3) {
                        $body[] = date('h:i A',strtotime($earliest));
                        $body[] = ": ".$cart_str;
                    } else {
                        $body[] = $cart_str;
                        $body[] = date('h:i A',strtotime($earliest));
                    }
                    callWhatsapp($appointment["phone"],$body,$appointment["companyId"]);
                }   
            }
        }
    }

    function getClientIp()
    {
        $request = \Config\Services::request();

        // Cloudflare header (most reliable if you're on Cloudflare)
        $cfIp = $request->getServer('HTTP_CF_CONNECTING_IP');
        if (!empty($cfIp)) {
            return $cfIp;
        }

        // Generic proxy header
        $xff = $request->getServer('HTTP_X_FORWARDED_FOR');
        if (!empty($xff)) {
            // Usually contains a comma-separated list
            $ips = explode(',', $xff);
            return trim($ips[0]);
        }

        // X-Real-IP header
        $xRealIp = $request->getServer('HTTP_X_REAL_IP');
        if (!empty($xRealIp)) {
            return $xRealIp;
        }

        // Default fallback (direct connection)
        return $request->getIPAddress();
    }