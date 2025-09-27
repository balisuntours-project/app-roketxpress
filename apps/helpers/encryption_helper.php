<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function encodeStringKeyFunction($plainString, $completeKey, $lengthKey = 0, $indexSliceStart = 0){
	
	$lengthKey	=	$lengthKey == 0 ? strlen($completeKey) : $lengthKey;
	$key		=	substr($completeKey, $indexSliceStart, $lengthKey);
	$ci 		=&	get_instance();
    $ci->load->library('encryption');
	$ci->encryption->initialize(
		array(
				'cipher'	=> 'aes-128',
				'mode'		=> 'ctr',
				'key'		=> $key
		)
	);
	
	$encodeString	=	base64_encode($ci->encryption->encrypt($plainString));
	
	return $encodeString;
}

function decodeStringKeyFunction($plainString, $completeKey, $lengthKey = 0, $indexSliceStart = 0){
	
	$lengthKey	=	$lengthKey == 0 ? strlen($completeKey) : $lengthKey;
	$key		=	substr($completeKey, $indexSliceStart, $lengthKey);
	$ci 		=&	get_instance();
    $ci->load->library('encryption');
	$ci->encryption->initialize(
		array(
				'cipher'	=> 'aes-128',
				'mode'		=> 'ctr',
				'key'		=> $key
		)
	);
	
	$encodeString	=	$ci->encryption->decrypt(base64_decode($plainString));
	
	return $encodeString;
}