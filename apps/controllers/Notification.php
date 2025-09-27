<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_controller {
	
	public function __construct(){
        parent::__construct();
		$this->postVar	=	decodeJsonPost();
		$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array());
		$this->newToken	=	isLoggedIn($this->token, true);
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataNotification(){

		$this->load->model('ModelNotification');
		$this->load->model('MainOperation');

		$page			=	validatePostVar($this->postVar, 'page', true);
		$status			=	validatePostVar($this->postVar, 'status', false);
		$idMessageType	=	validatePostVar($this->postVar, 'idMessageType', false);
		$keywordSearch	=	validatePostVar($this->postVar, 'keywordSearch', false);
		$idUserAdmin	=	$this->MainOperation->getIDUserAdmin($this->token);
		$dataTable		=	$this->ModelNotification->getDataNotification($page, 25, $status, $idUserAdmin, $idMessageType, $keywordSearch);
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
	public function dismissNotification(){

		$this->load->model('MainOperation');

		$idMessageAdmin			=	$this->postVar['idMessageAdmin'];
		$arrUpdateNotification	=	array(
										"DATETIMEREAD"	=>	date('Y-m-d H:i:s'),
										"STATUS"		=>	1
									);
		$this->MainOperation->updateData("t_messageadmin", $arrUpdateNotification, "IDMESSAGEADMIN", $idMessageAdmin);
		setResponseOk(array("token"=>$this->token));
		
	}
	
	public function dismissAllNotification(){

		$this->load->model('MainOperation');

		$idUserAdmin			=	$this->MainOperation->getIDUserAdmin($this->token);
		$arrUpdateNotification	=	array(
										"DATETIMEREAD"	=>	date('Y-m-d H:i:s'),
										"STATUS"		=>	1
									);
		$this->MainOperation->updateData("t_messageadmin", $arrUpdateNotification, "IDUSERADMIN", $idUserAdmin);
		setResponseOk(array("token"=>$this->token));
		
	}
	
}