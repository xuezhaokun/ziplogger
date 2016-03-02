<?php


// Enter the path that the oauth library is in relation to the php file
require_once ('lib/OAuth.php');



if($_SERVER["REQUEST_METHOD"] == "GET"){
	
    //echo $_GET['latitude']."</br>";
    //echo $_GET['longitude']."</br>";

	$mylatitude = $_GET['latitude']; //mysql_real_escape_string( $_GET['latitude']);
	$mylongitude = $_GET['longitude']; //mysql_real_escape_string( $_GET['longitude']);
	$myTab = $_GET['tab'];
	
	//$myTab = $_GET['currentTab'];
	$searchTerm = "music";
	if($myTab == "Venue"){
		$searchTerm = "music+venue";
	}else if($myTab == "Store/Company"){
		$searchTerm = "music+store";
	}
	$unsigned_url = "http://api.yelp.com/v2/search?term=". $searchTerm ."+venue&sort=1&ll=".$mylatitude.",".$mylongitude;

	// Set your keys here
	$consumer_key = "Lm4aA_FIpbBPG1QcmcnTGA";
	$consumer_secret = "wvCpy5LLQ4lpm6jrfhbMZXaoIF0";
	$token = "8gp5Fq08_k8krvs7yERlDdlIwBby8DDj";
	$token_secret = "aZ736qVDP8gOl5GM14QEPG9M0Yk";

	// Token object built using the OAuth library
	$token = new OAuthToken($token, $token_secret);

	// Consumer object built using the OAuth library
	$consumer = new OAuthConsumer($consumer_key, $consumer_secret);

	// Yelp uses HMAC SHA1 encoding
	$signature_method = new OAuthSignatureMethod_HMAC_SHA1();

	// Build OAuth Request using the OAuth PHP library. Uses the consumer and token object created above.
	$oauthrequest = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url);
	// Sign the request
	$oauthrequest->sign_request($signature_method, $consumer, $token);

	// Get the signed URL
	$signed_url = $oauthrequest->to_url();
	// Send Yelp API Call
	$ch = curl_init($signed_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$data = curl_exec($ch); // Yelp response
	curl_close($ch);
	echo json_encode($data);
}

?>