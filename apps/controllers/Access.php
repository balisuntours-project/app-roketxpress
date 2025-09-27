<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access extends CI_controller {
	
	var $postVar;
	var $token;
	
	public function __construct(){
        parent::__construct();
		$this->postVar	=	decodeJsonPost();
		$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array());
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function accessCheck(){
		isLoggedIn($this->token, false);		
	}
	
	public function userLogin(){
		
		$this->load->model('ModelLogin');

		$username	=	isset($this->postVar['username']) ? $this->postVar['username'] : setResponseBadRequest(array());
		$password	=	isset($this->postVar['password']) ? $this->postVar['password'] : setResponseBadRequest(array());
		$loginResult=	$this->ModelLogin->UserLogin($username, $password);

		if(!$loginResult){
			setResponseNotFound(array());
		}
		
		$idUserAdmin=	$loginResult['IDUSERADMIN'];
		$newToken	=	generateNewToken(LOGIN_TOKEN_LENGTH);
		$this->ModelLogin->UpdateLastActivityNewToken($loginResult['TOKEN'], $newToken, true, $idUserAdmin);
		unset($loginResult['TOKEN']);
		setResponseOk(array("token"=>$newToken, "userData"=>$loginResult));
		
	}

	public function changePasswordUser(){

		$this->load->model('ModelLogin');
		$this->load->model('MainOperation');

		$idUser			=	$this->MainOperation->getIDUserAdmin($this->token);
		$oldPassword	=	$this->postVar['oldPassword'];
		$newPassword	=	$this->postVar['newPassword'];
		$isOldPwdCorrect=	$this->ModelLogin->isOldPasswordCorrect($idUser, $oldPassword);
		
		if(!$isOldPwdCorrect){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Your old password is incorrect"));
		}

		$this->MainOperation->updateData('m_useradmin',
										  array("PASSWORD"	=> md5($newPassword)),
										  'IDUSERADMIN',
										  $idUser
										);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Your password was changed"));
		
	}
	
	public function unreadNotificationList(){

		$this->load->model('ModelLogin');
		$this->load->model('MainOperation');

		$idUserAdmin			=	$this->MainOperation->getIDUserAdmin($this->token);
		$unreadNotificationList	=	$this->ModelLogin->getUnreadNotificationList($idUserAdmin);
		$totalUnreadNotification=	0;
		$unreadNotificationArray=	array();
		
		if($unreadNotificationList){
		
			foreach($unreadNotificationList as $unreadNotificationData){
				if(count($unreadNotificationArray) < 10){
					$unreadNotificationArray[]	=	$unreadNotificationData;
				}
				$totalUnreadNotification++;
			}
		
		}

		setResponseOk(array("token"=>$this->token, "totalUnreadNotification"=>$totalUnreadNotification, "unreadNotificationArray"=>$unreadNotificationArray));
		
	}

}