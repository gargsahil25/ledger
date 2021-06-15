<?php
// If you are using Composer
require '../vendor/autoload.php';

function sendEmail($user, $msg, $sql, $resp) {
	$subject = "Ledger - {$user['userName']} - {$msg}";
	$body = "SQL: {$sql}<br/>Resp: {$resp}";
	$request_body = json_decode('{
	  "personalizations": [
	    {
	      "to": [
	        {	
	        	"name": "Sahil Garg",
	          	"email": "garg.sahil25@gmail.com"
	        }
	      ],
	      "subject": "'.$subject.'"
	    }
	  ]
	  "content": [
	    {
	      "type": "text/html",
	      "value": "'.$body.'"
	    }
	  ]
	}');
	echo $request_body;
	$apiKey = getenv('SENDGRID_API_KEY');
	$sg = new \SendGrid($apiKey);

	$response = $sg->client->mail()->send()->post($request_body);
	return $response;
}

?>