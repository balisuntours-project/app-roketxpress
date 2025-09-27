<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Knowledge extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$this->postVar	=	decodeJsonPost();
		$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid submission data"));
		$this->newToken	=	isLoggedIn($this->token, true);
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataKnowledge(){
		$this->load->model('ModelKnowledge');

		$dataResult	=	$this->ModelKnowledge->getDataKnowledge();
		setResponseOk(array("token"=>$this->newToken, "dataResult"=>$dataResult));
	}		
}