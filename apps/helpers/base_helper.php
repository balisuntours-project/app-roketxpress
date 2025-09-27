<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function decodeJsonPost(){
	return json_decode(trim(file_get_contents('php://input')), true);
}

function validateVar($postVar, $arrVarValidate){
	
	$msg	=	false;
	foreach($arrVarValidate as $varValidate){
		$validateName	=	$varValidate[0];
		$validateType	=	$varValidate[1];
		$validateText	=	$varValidate[2];

		if(!isset($postVar[$validateName])){
			switch($validateType){
				case "text"		:	$msg	=	"Please enter ".$validateText;
									break;
				case "number"	:	$msg	=	"Please enter value ".$validateText." more than 0 (zero)";
									break;
				case "option"	:	$msg	=	"Please choose ".$validateText;
									break;
				default			:	$msg	=	"Please enter ".$validateText;
									break;
			}

		}

	}
	
	foreach($postVar as $varName=>$value){
		foreach($arrVarValidate as $varValidate){
			$validateName	=	$varValidate[0];
			$validateType	=	$varValidate[1];
			$validateText	=	$varValidate[2];
			
			if($varName == $validateName){
				
				switch($validateType){
					case "text"		:	if($value == ""){
											$msg	=	"Please enter ".$validateText;
										}
										break;
					case "number"	:	if($value == "" || $value <= 0){
											$msg	=	"Please enter value ".$validateText." more than 0 (zero)";
										}
										break;
					case "option"	:	if($value == "" || (is_int($value) && $value == 0)){
											$msg	=	"Please choose ".$validateText;
										}
										break;
					default			:	if($value == ""){
											$msg	=	"Please enter ".$validateText;
										}
										break;
				}
				
			}
		}
	}
	
	return $msg;
	
}
function generateOptYear(){
	
	$tahunawal	=	2018;
	$tahunakhir	=	date('Y');
	$opttahun	=	"";
	for($i=$tahunawal; $i<=$tahunakhir; $i++){
		$selected	=	$i == date('Y') ? "selected" : "";
		$opttahun	.=	"<option value='".$i."' ".$selected.">".$i."</option>";
	}
	
	return $opttahun;
	
}

function generateOptJam(){
	
	$optjam	=	"";
	for($j=0; $j<=23; $j++){
		$padval	=	str_pad($j, 2, "0", STR_PAD_LEFT);
		$optjam	.=	"<option value='".$padval."'>".$padval."</option>";
	}
	
	return $optjam;
	
}

function switchBulanRomawi($bulan){
	
	$bulanRomawi	=	"I";
	switch($bulan){
		case "01"	:	$bulanRomawi	=	"I"; break;
		case "02"	:	$bulanRomawi	=	"II"; break;
		case "03"	:	$bulanRomawi	=	"III"; break;
		case "04"	:	$bulanRomawi	=	"IV"; break;
		case "05"	:	$bulanRomawi	=	"V"; break;
		case "06"	:	$bulanRomawi	=	"VI"; break;
		case "07"	:	$bulanRomawi	=	"VII"; break;
		case "08"	:	$bulanRomawi	=	"VIII"; break;
		case "09"	:	$bulanRomawi	=	"IX"; break;
		case "10"	:	$bulanRomawi	=	"X"; break;
		case "11"	:	$bulanRomawi	=	"XI"; break;
		case "12"	:	$bulanRomawi	=	"XII"; break;
		default		:	$bulanRomawi	=	"I"; break;
	}
	
	return $bulanRomawi;
	
}

function getPageProperties($page, $dataperpage){

	$startid	=	($page * 1 - 1) * $dataperpage;
	$datastart	=	$startid + 1;
	$dataend	=	$datastart + $dataperpage - 1;
	
	return array($startid, $datastart, $dataend);
	
}

function validatePostVar($postArr, $varName, $badReqResponse = false){
	
	if($badReqResponse) isset($postArr[$varName]) && $postArr[$varName] <> "" ? $postArr[$varName] : setResponseBadRequest(array("varName"=>$varName));
	return isset($postArr[$varName]) ? str_replace("'", "`", $postArr[$varName]) : "";
	
}

function validatePostVarBody($varName, $badReqResponse = false){
	
	$ci	=& get_instance();
	if($badReqResponse) null !== $ci->input->post($varName) && $ci->input->post($varName) <> "" ? $ci->input->post($varName) : setResponseBadRequest(array());
	return null !== $ci->input->post($varName) ? $ci->input->post($varName) : "";
	
}
		
function generateOptionMonth(){
	
	$optMonth	=	"";
	$monthActive=	date('m') * 1;
	$selected	=	"";
	
	for($i=1; $i<=12; $i++){
		
		$selected	=	$monthActive == $i ? "selected" : "";
		$month		=	str_pad($i, 2, "0", STR_PAD_LEFT);
		$optMonth	.=	"<option value='".$month."' ".$selected.">".$month."</option>";
		
	}
	
	return $optMonth;
	
}

function removeNonNumericValue($value){
	
	return preg_replace("/[^0-9]/", "", $value);
	
}

function createBatchNumber($idTipeProduk){
	
	$ci	=& get_instance();
	$ci->load->model('MainOperation');

	$isSet		=	false;
	$iUrut		=	1;
	
	while($isSet == false) {
		$batchNumber=	date('Ymd').str_pad($iUrut, 2, "0", STR_PAD_LEFT).str_pad($idTipeProduk, 4, "0", STR_PAD_LEFT);
		$isExists	=	$ci->MainOperation->isDataBatchNumberExists($idTipeProduk, $batchNumber);
		if(!$isExists){
			$isSet	=	true;
		}
		$iUrut++;
	}

	return $batchNumber;
		
	
}

function createRandomString($length = 4){
	
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
	
}

function checkMailPattern($str) {
	return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
}

function sumArrTimeHHmm($times){
	
	$all_seconds	=	0;
    foreach ($times as $time) {
        list($hour, $minute, $second) = explode(':', $time);
        $all_seconds += $hour * 3600;
        $all_seconds += $minute * 60; $all_seconds += $second;

    }

    $total_minutes = floor($all_seconds/60); $seconds = $all_seconds % 60;  $hours = floor($total_minutes / 60); $minutes = $total_minutes % 60;

    return sprintf('%02d:%02d', $hours, $minutes);
}

function getArrDateBetween($startDate, $endDate){
	
	$endDate	=	$endDate->modify( '+1 day' );
	$interval	=	new DateInterval('P1D');
	$daterange	=	new DatePeriod($startDate, $interval ,$endDate);
	$arrReturn	=	array();

	foreach($daterange as $date){
		$arrReturn[]	=	$date->format("Y-m-d");
	}
	
	return $arrReturn;

}

function writeInfoLog($msg){
	
	$now	=	date("m-d-Y H:i:s.u");
	log_message('info', $now.' - '.$msg);

	return true;
	
}

function unescapejs($source) {
    $source = str_replace(array('%0B'), array(''), $source);
    $s= preg_replace('/%u(....)/', '&#x$1;', $source);
    $s= preg_replace('/%(..)/', '&#x$1;', $s);
    return html_entity_decode($s, ENT_QUOTES, 'UTF-8');
}

function curl_get_file_contents($URL)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents) return $contents;
    else return FALSE;
}

function sanitize_email($email) {
	return preg_replace('/[^a-zA-Z0-9._+\@-]/', '', $email);
}