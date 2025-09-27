<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';
use Google\Client;

class Fcm {
   private function getAccessToken() {
		$client		=	new Client();
		$client->setAuthConfig(FIREBASE_MOBILE_SERVICE_ACCOUNT_KEY_PATH);
		$client->addScope('https://www.googleapis.com/auth/firebase.messaging');
		$client->useApplicationDefaultCredentials();
		$token		=	$client->fetchAccessTokenWithAssertion();
		return $token['access_token'];
	}
	
	function sendPushNotification($clientToken, $title, $body, $additionalArray = array(), $test = false) {
		$url				=	'https://fcm.googleapis.com/v1/projects/'.FIREBASE_MOBILE_PROJECT_ID.'/messages:send';
		$accessToken		=	$this->getAccessToken();
		$additionalArraySend=	[];
		
		if(isset($additionalArray) && count($additionalArray) > 0){
			foreach($additionalArray as $key => $value){
				$additionalArraySend	=	array_merge($additionalArraySend, [$key => strval($value)]);
			}
		}
		
		$headers	=	[
			'Authorization: Bearer ' . $accessToken,
			'Content-Type: application/json',
		];
		
		$message	=	[
			'token'			=>	$clientToken,
			'notification'	=>	[
				"title"	=> $title,
				"body"	=> $body
			],
			'data'			=>	array_merge(
				array(
					"title"				=>	$title,
					"body"				=>	$body,
					"priority"			=>	"high",
					"content_available"	=>	"true",
					"click_action"		=>	"FLUTTER_NOTIFICATION_CLICK",
					"sound"				=>	"siren.caf"
				),
				$additionalArraySend
			),
			'android'		=>	[
				'notification'	=>	[
					"channel_id"	=>	'bst_channel',
					"sound"			=>	'res_siren.mp3'
				]
			],
			'apns'			=>	[
				'headers'	=>	[
					"apns-priority"	=> '10'
				],
				'payload'	=>	[
					"aps"	=> [
						"sound"		=>	"siren.caf"
					]
				]
			]
		];
		
		try {
			$ch			=	curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $message]));
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 1000);
			curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			$response	=	curl_exec($ch);
			
			if ($response === false) {
				throw new Exception('Curl error: ' . curl_error($ch));
			}
			
			curl_close($ch);
			if($test) return [$response, $accessToken];
			return true;
		} catch (Exception $e) {
		   if($test) return 'Error: ' . $e->getMessage();
		   return true;
		}
	}
}