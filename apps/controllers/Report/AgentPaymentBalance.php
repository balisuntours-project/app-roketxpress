<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

class AgentPaymentBalance extends CI_controller {
	
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
	
	public function getDataStatsAgentPayment(){
		$this->load->model('Report/ModelAgentPaymentBalance');

		$firstDateIncome	=	getLastXMonthsFirstDate(5);
		$dataIncomePerAgent	=	$this->ModelAgentPaymentBalance->getDataIncomePerAgent($firstDateIncome);
		
		setResponseOk(
			array(
				"token"					=>	$this->newToken,
				"dataIncomePerAgent"	=>	$dataIncomePerAgent
			)
		);
	}
}