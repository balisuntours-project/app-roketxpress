<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RecapPerProduct extends CI_controller {
	
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
	
	public function getDataRecapPerProduct(){

		$this->load->model('Report/ModelRecapPerProduct');
		$this->load->model('MainOperation');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$productName	=	validatePostVar($this->postVar, 'productName', false);
		$idSource		=	validatePostVar($this->postVar, 'idSource', false);
		$startDate		=	validatePostVar($this->postVar, 'startDate', true);
		$endDate		=	validatePostVar($this->postVar, 'endDate', true);
		$startDate		=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate		=	$startDate->format('Y-m-d');
		$endDate		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate		=	$endDate->format('Y-m-d');
		$dataTable		=	$this->ModelRecapPerProduct->getDataRecapPerProduct($page, 25, $productName, $idSource, $startDate, $endDate);
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
}