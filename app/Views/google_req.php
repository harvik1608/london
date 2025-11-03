<?php 
	session_start();

	$baseURL = substr(APPPATH,0,strlen(APPPATH)-4);
	$jsonfile = $baseURL."public/google/client_secret_884669104429-u9c89c9ondkv4876ffi7akpksas05ffa.apps.googleusercontent.com.json";

	require 'vendor/vendor/autoload.php';

	$client = new Google_Client();
	$client->setAuthConfig($jsonfile);
	$client->addScope('https://www.googleapis.com/auth/contacts');
	$client->setRedirectUri('https://insightqera.com/beauty/google');
	$client->setAccessType('offline');

	$authUrl = $client->createAuthUrl();
	header('Location: ' . $authUrl);
	exit();
