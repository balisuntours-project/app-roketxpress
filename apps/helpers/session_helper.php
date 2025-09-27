<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function isLoggedIn($token, $callBackResponse = true){

	$ci	=& get_instance();
	$ci->load->model('ModelLogin');

	if(isset($token)){
		
		$lastActivity	=	$ci->ModelLogin->CheckLastActivity($token);
		if($lastActivity){

			$lastActivitySec=	strtotime($lastActivity);
			$timeNowSec		=	strtotime(date('Y-m-d H:i:s'));
			$timeDiff		=	$timeNowSec - $lastActivitySec;

			if($timeDiff <= MAX_LOGIN_LIFETIME){
				$tokenExpired	=	isTokenExpired($token);
				$newToken		=	$tokenExpired ? generateNewToken(LOGIN_TOKEN_LENGTH) : $token;
				
				if($tokenExpired){
					$ci->ModelLogin->UpdateTokenExpired($token, $tokenExpired);
					if($callBackResponse == true) $ci->ModelLogin->UpdateLastActivityNewToken($token, $newToken);
				} else {
					if($callBackResponse == true) $ci->ModelLogin->UpdateLastActivity($token);
				}

				$newestToken	=	$ci->ModelLogin->GetNewestToken($newToken);
				setUserNotifSignal($newestToken);
				if($callBackResponse == false){
					setResponseOk(array("token"=>$newestToken, "interval"=>LOGIN_TOKEN_MAXAGE_SECONDS));
				}
				return $newestToken;
				
			}
			setResponseExpiresSession(array("msg"=>"your session has expired, please re-login"));
		}
		setResponseExpiresSession(array("msg"=>"Your session was not found / you have logged in on another device.<br/><br/>Please re-login"));
	}
	setResponseForbidden();
	
}

function isTokenExpired($token){
	
	$ci	=& get_instance();
	$ci->load->model('ModelLogin');
	$tokenExpiredTime	=	$ci->ModelLogin->GetTokenExpired($token);
	
	$tokenExpiredSec	=	strtotime($tokenExpiredTime);
	$timeNowSec			=	strtotime(date('Y-m-d H:i:s'));
	$timeDiff			=	$tokenExpiredSec - $timeNowSec;

	if(($timeDiff * 1) <= 1){
		return date("Y-m-d H:i:s", time() + LOGIN_TOKEN_MAXAGE_SECONDS);
	}
	
	return false;
}

function generateNewToken($length){
	
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
	
}

function setUserNotifSignal($token){
	
	$ci	=& get_instance();
	$ci->load->model('ModelLogin');
	$ci->load->model('MainOperation');

	$postVar			=	decodeJsonPost();
	
	if(isset($postVar['OSUserId'])){
		$OSUserId			=	$postVar['OSUserId'];
		$userOS				=	isset($postVar['userOS']) ? $postVar['userOS'] : "";
		$userAgent			=	$postVar['userAgent'];
		$idUserAdmin		=	$ci->ModelLogin->GetIDUserByToken($token);
		$notifSignalExist	=	$ci->ModelLogin->isNotifSignalExist($idUserAdmin, $OSUserId);
		
		if($notifSignalExist){
			$idUserNotifSignal	=	$notifSignalExist;
			$arrUpdate			=	array(
										"USERIP"		=>	$_SERVER['REMOTE_ADDR'],
										"LASTACTIVITY"	=>	date('Y-m-d H:i:s')
									);
			$ci->MainOperation->updateData("t_usernotifsignal", $arrUpdate, "IDUSERNOTIFSIGNAL", $idUserNotifSignal);
		} else {
			$arrInsert			=	array(
										"IDUSERADMIN"	=>	$idUserAdmin,
										"OSUSERID"		=>	$OSUserId,
										"USEROS"		=>	$userOS,
										"USERAGENT"		=>	$userAgent,
										"USERIP"		=>	$_SERVER['REMOTE_ADDR'],
										"LASTACTIVITY"	=>	date('Y-m-d H:i:s')
									);
			$ci->MainOperation->addData("t_usernotifsignal", $arrInsert);
		}
	}
	
	return true;
	
}