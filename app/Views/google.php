<?php 
	// session_start();

	$baseURL = substr(APPPATH,0,strlen(APPPATH)-4);
	$jsonfile = $baseURL."public/google/client_secret_884669104429-u9c89c9ondkv4876ffi7akpksas05ffa.apps.googleusercontent.com.json";

	require 'vendor/vendor/autoload.php';

	$client = new Google_Client();
	$client->setAuthConfig($jsonfile);
	$client->addScope('https://www.googleapis.com/auth/contacts.readonly');
	$client->setRedirectUri('https://insightqera.com/beauty/google');
	$client->setAccessType('offline');

	$service = new Google_Service_PeopleService($client);

	$access_token = "";
	if (isset($_GET['code'])) {
	    $client->fetchAccessTokenWithAuthCode($_GET['code']);
	    $access_token = $client->getAccessToken();
	}

	// Retrieve the access token from the session
	$client->setAccessToken($access_token);

	// Fetch contacts
	/* $optParams = array(
	    'personFields' => 'names,emailAddresses,phoneNumbers',
	);
	$results = $service->people_connections->listPeopleConnections('people/me', $optParams);

	echo "<pre>";
	foreach ($results->getConnections() as $person) {
		// print_r ($person);
	    $names = $person->getNames();
	    $emails = $person->getEmailAddresses();
	    $phones = $person->getphoneNumbers();
	    $name = $names ? $names[0]->getDisplayName() : 'No name';
	    $email = $emails ? $emails[0]->getValue() : 'No email';
	    $phone = $phones ? $phones[0]->getValue() : 'No phone';
	    // echo "Name: $name, Email: $phone<br/>";
	    $resourceName = $person->getResourceName();
	    echo $resourceName."<br>";
	} */
	// ========================================================================================================================
	// Add Contact
	/* $newContact = new Google_Service_PeopleService_Person();
	$newContact->setNames([new Google_Service_PeopleService_Name([
	    'givenName' => 'Bhavika',
	    'familyName' => 'Kahar GJ 1'
	])]);
	$newContact->setPhoneNumbers([new Google_Service_PeopleService_PhoneNumber([
	    'value' => '6355558297', // Mobile number to add
        'type' => 'mobile'
	])]);
	$newContact->setEmailAddresses([new Google_Service_PeopleService_EmailAddress([
	    'value' => 'bhavikakahar23@gmail.com'
	])]);

	// Add the contact
	$result = $service->people->createContact($newContact);

	echo 'Created contact with resource name: ' . $result->getResourceName(); */
	// ========================================================================================================================

	// Update Contact
	/* $updatedContact = new Google_Service_PeopleService_Person();
	$updatedContact->setNames([new Google_Service_PeopleService_Name([
	    'givenName' => 'Bhavna',
	    'familyName' => 'Kahar GJ 1'
	])]);
	$updatedContact->setEmailAddresses([new Google_Service_PeopleService_EmailAddress([
	    'value' => 'bhavikakahar23@gmail.com'
	])]);

	// Update the contact (assuming you have the resource name)
	$contactResourceName = 'people/c895873063704516926';
	$result = $service->people->updateContact($contactResourceName, $updatedContact);

	echo 'Updated contact with resource name: ' . $result->getResourceName(); */
	// ========================================================================================================================

	// Delete Contact
	$contactResourceName = 'people/c895873063704516926';
	$service->people->deleteContact($contactResourceName);

	echo 'Deleted contact with resource name: ' . $contactResourceName;
