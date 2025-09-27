<?php
defined('BASEPATH') OR exit('No direct script access allowed');
	
	function sendPushNotification($clientToken, $title, $body, $additionalArray = array(), $test = false){
		
		$data			=	array("to"					=> $clientToken,
								  "notification"		=> array_merge(
																	array(
																		"title"			=> $title,
																		"body"			=> $body,
																		"click_action"	=> "FLUTTER_NOTIFICATION_CLICK",
																		"sound"			=> "siren.caf"
																	),
																	$additionalArray
														   ),
								  "data"				=> array_merge(
																	array(
																		"title"	=> $title,
																		"body"	=> $body,
																	),
																	$additionalArray
														   ),
								  "priority"			=>	"high",
								  "content_available"	=>	true
							);
		$data_string	=	json_encode($data);
		$headers		=	array(
								 'Authorization: key=' . FB_API_ACCESS_KEY, 
								 'Content-Type: application/json'
							);																							 
		$ch				=	curl_init();

		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true);
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers);
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt( $ch,CURLOPT_POSTFIELDS, $data_string);                                                                  
																															 
		$result			=	curl_exec($ch);
		curl_close ($ch);
		
		if($test) return $result;
		return true;
		
	}