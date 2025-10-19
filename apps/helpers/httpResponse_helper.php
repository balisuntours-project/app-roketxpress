<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function returnHttpResponse($httpResponseCode, $message, $arrData = []){
	header('Content-Type: application/json');
	http_response_code($httpResponseCode);
	echo json_encode([
		'message'	=>	$message,
		'data'		=>	$arrData
	]);
	exit();
}