<?php
    namespace App\Controllers;

    require APPPATH.'Views/vendor/vendor/autoload.php';

    use App\Models\CompanyModel;
    use App\Models\CustomerModel;

    class Google extends BaseController
    {
        protected $helpers = ["custom"];

        public function req()
        {
            $model = new CompanyModel;
            $company = $model->select("credential_file")->where("id",static_company_id())->first();
            if(!empty($company)) {
                if($company["credential_file"] != "") {
                    $baseURL = substr(APPPATH,0,strlen(APPPATH)-4);
                    $jsonfile = $baseURL."public/google/".$company["credential_file"];

                    $client = new \Google_Client();
                    $client->setAuthConfig($jsonfile);
                    // $client->addScope('https://www.googleapis.com/auth/contacts');
                    $client->addScope(\Google_Service_PeopleService::CONTACTS);
                    $client->addScope(\Google_Service_Calendar::CALENDAR);
                    $client->setRedirectUri('https://insightqera.com/beauty/google');
                    $client->setAccessType('offline');

                    $authUrl = $client->createAuthUrl();
                    header('Location: ' . $authUrl);
                    exit();
                }
            }
        }

        public function res()
        {
            $model = new CompanyModel;
            $company = $model->select("credential_file")->where("id",static_company_id())->first();

            $baseURL = substr(APPPATH,0,strlen(APPPATH)-4);
            $jsonfile = $baseURL."public/google/".$company["credential_file"];
            
            $client = new \Google_Client();
            $client->setAuthConfig($jsonfile);
            // $client->addScope('https://www.googleapis.com/auth/contacts.readonly');
            $client->addScope(\Google_Service_PeopleService::CONTACTS);
            $client->addScope(\Google_Service_Calendar::CALENDAR);
            $client->setRedirectUri('https://insightqera.com/beauty/google');
            $client->setAccessType('offline');
            $service = new \Google_Service_PeopleService($client);

            if(isset($_GET['code'])) {
                $client->fetchAccessTokenWithAuthCode($_GET['code']);

                $model = new CompanyModel;
                $model->update(static_company_id(),array("google_code" => json_encode($client->getAccessToken())));
            }
            $model = new CompanyModel;
            $company = $model->select("google_code")->where("id",static_company_id())->first();
            if(!empty($company)) {
                if($company["google_code"] != "") {
                    $access_token = json_decode($company["google_code"],true);
                    $client->setAccessToken($access_token);

                    $optParams = array('personFields' => 'names,emailAddresses,phoneNumbers');
                    $results = $service->people_connections->listPeopleConnections('people/me', $optParams);

                    $model = new CustomerModel;
                    foreach ($results->getConnections() as $person) {
                        $resource_id = $person->getResourceName();
                        $names = $person->getNames();
                        $emails = $person->getEmailAddresses();
                        $phones = $person->getphoneNumbers();

                        $cust = $model->select("id")->where("resource_id",$resource_id)->first();
                        if(empty($cust)) {
                            $insert_data = array(
                                "resource_id" => $resource_id,
                                "name" => $names ? $names[0]->getDisplayName() : '',
                                "email" => $emails ? $emails[0]->getValue() : '',
                                "phone" => $phones ? $phones[0]->getValue() : '',
                                "companyId" => static_company_id(),
                                "addedBy" => 0,
                                "updatedBy" => 0,
                                "createdAt" => date("Y-m-d H:i:s"),
                                "updatedAt" => date("Y-m-d H:i:s"),
                            );
                            $model->insert($insert_data);
                        } else {
                            $update_data = array(
                                "name" => $names ? $names[0]->getDisplayName() : '',
                                "email" => $emails ? $emails[0]->getValue() : '',
                                "phone" => $phones ? $phones[0]->getValue() : '',
                                "companyId" => static_company_id(),
                                "updatedBy" => 0,
                                "updatedAt" => date("Y-m-d H:i:s"),
                            );
                            $model->update($cust["id"],$update_data);
                        }
                    }
                }
            }
        }

        public function embellish_contacts($company_id)
        {
            $model = new CompanyModel;
            $company = $model->select("credential_file")->where("id",$company_id)->first();

            $baseURL = substr(APPPATH,0,strlen(APPPATH)-4);
            $jsonfile = $baseURL."public/google/".$company["credential_file"];
            
            $client = new \Google_Client();
            $client->setAuthConfig($jsonfile);
            $client->addScope(\Google_Service_PeopleService::CONTACTS);
            $client->addScope(\Google_Service_Calendar::CALENDAR);
            $client->setRedirectUri('https://insightqera.com/beauty/google');
            $client->setAccessType('offline');
            $service = new \Google_Service_PeopleService($client);

            if(isset($_GET['code'])) {
                $client->fetchAccessTokenWithAuthCode($_GET['code']);

                $model = new CompanyModel;
                $model->update($company_id,array("google_code" => json_encode($client->getAccessToken())));
            }
            // Google Contact to Website
            $model = new CompanyModel;
            $company = $model->select("google_code")->where("id",$company_id)->first();
            if(!empty($company)) {
                if($company["google_code"] != "") {
                    $access_token = json_decode($company["google_code"],true);
                    $client->setAccessToken($access_token);

                    $optParams = array('personFields' => 'names,emailAddresses,phoneNumbers');
                    $results = $service->people_connections->listPeopleConnections('people/me', $optParams);

                    $model = new CustomerModel;
                    foreach ($results->getConnections() as $person) {
                        $resource_id = $person->getResourceName();
                        $names = $person->getNames();
                        $emails = $person->getEmailAddresses();
                        $phones = $person->getphoneNumbers();

                        $cust = $model->select("id")->where("resource_id",$resource_id)->first();
                        if(empty($cust)) {
                            $insert_data = array(
                                "resource_id" => $resource_id,
                                "name" => $names ? $names[0]->getDisplayName() : '',
                                "email" => $emails ? $emails[0]->getValue() : '',
                                "phone" => $phones ? $phones[0]->getValue() : '',
                                "companyId" => $company_id,
                                "addedBy" => 0,
                                "updatedBy" => 0,
                                "createdAt" => date("Y-m-d H:i:s"),
                                "updatedAt" => date("Y-m-d H:i:s"),
                            );
                            $model->insert($insert_data);
                        } else {
                            $update_data = array(
                                "name" => $names ? $names[0]->getDisplayName() : '',
                                "email" => $emails ? $emails[0]->getValue() : '',
                                "phone" => $phones ? $phones[0]->getValue() : '',
                                "companyId" => $company_id,
                                "updatedBy" => 0,
                                "updatedAt" => date("Y-m-d H:i:s"),
                            );
                            $model->update($cust["id"],$update_data);
                        }
                    }
                }
            }

            // Website to Google Contact
            $model = new CustomerModel;
            $customers = $model->select("id,name,phone,email")->where("is_sync_with_google",0)->get()->getResultArray();
            if(!empty($customers)) {
                foreach($customers as $customer) {
                    $resource_id = add_google_contact($customer);
                    if($resource_id != "") {
                        $model = new CustomerModel();
                        $model->update($customer["id"],array("resource_id" => $resource_id,"is_sync_with_google" => 1));
                    }
                }
            }
        }

        public function calendar()
        {
            $model = new CompanyModel;
            $company = $model->select("credential_file,google_code,timezone")->where("id",static_company_id())->first();

            $baseURL = substr(APPPATH,0,strlen(APPPATH)-4);
            $jsonfile = $baseURL."public/google/".$company["credential_file"];

            $client = new \Google_Client();
            $client->setAuthConfig($jsonfile); // Path to your OAuth 2.0 credentials
            $client->addScope(\Google_Service_Calendar::CALENDAR);
            $client->setAccessToken($company["google_code"]);
            $service = new \Google_Service_Calendar($client);

            $event = new \Google_Service_Calendar_Event(array(
                'summary' => 'Google I/O 2024',
                'location' => '800 Howard St., San Francisco, CA 94103',
                'description' => 'A chance to hear more about Google\'s developer products.',
                'start' => array(
                    'dateTime' => '2024-08-11T13:00:00-07:00', // Start date and time in RFC3339 format
                    'timeZone' => $company["timezone"],
                ),
                'end' => array(
                    'dateTime' => '2024-08-11T13:30:00-07:00', // End date and time in RFC3339 format
                    'timeZone' => $company["timezone"],
                ),
                'attendees' => array(
                    array('email' => 'lpage@example.com'),
                    array('email' => 'sbrin@example.com'),
                ),
                'reminders' => array(
                    'useDefault' => FALSE,
                    'overrides' => array(
                        array('method' => 'email', 'minutes' => 24 * 60),
                        array('method' => 'popup', 'minutes' => 10),
                    ),
                ),
            ));

            // Insert the event
            $calendarId = 'primary'; // Use 'primary' for the user's primary calendar
            $event = $service->events->insert($calendarId, $event);

            echo 'Event created: ' . $event->htmlLink;
        }
    }