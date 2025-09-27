<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket extends CI_controller {
	
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
	
	public function getDataTicketVendorPrice(){

		$this->load->model('ProductSetting/ModelTicket');
		
		$productName	=	validatePostVar($this->postVar, 'productName', false);
		$idVendor		=	validatePostVar($this->postVar, 'idVendor', false);
		$dataResult		=	$this->ModelTicket->getDataTicketVendorPrice($productName, $idVendor);

		if(!$dataResult){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataResult));
	
	}
	
	public function detailTicketVendorPrice(){

		$this->load->model('ProductSetting/ModelTicket');
		
		$idVendorTicketPrice	=	validatePostVar($this->postVar, 'idVendorTicketPrice', true);
		$detailData				=	$this->ModelTicket->getDetailTicketVendorPrice($idVendorTicketPrice);
		
		if(!$detailData){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$detailData));
	
	}
	
	public function addTicketVendorPrice(){
		
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelTicket');
		
		$idProduct		=	validatePostVar($this->postVar, 'optionProductEditor', true);
		$idVendor		=	validatePostVar($this->postVar, 'optionVendorEditor', true);
		$voucherStatus	=	validatePostVar($this->postVar, 'optionVoucherStatus', false);
		$paxRangeMin	=	str_replace(",", "", validatePostVar($this->postVar, 'paxRangeMin', true));
		$paxRangeMax	=	str_replace(",", "", validatePostVar($this->postVar, 'paxRangeMax', true));
		$priceAdult		=	str_replace(",", "", validatePostVar($this->postVar, 'priceAdult', true));
		$priceChild		=	str_replace(",", "", validatePostVar($this->postVar, 'priceChild', true));
		$priceInfant	=	str_replace(",", "", validatePostVar($this->postVar, 'priceInfant', true));
		$notes			=	validatePostVar($this->postVar, 'notes', false);
		$checkDataExists=	$this->ModelTicket->checkDataExists($idProduct, $idVendor, $paxRangeMin, $paxRangeMax);

		if($checkDataExists){
			$msg		=	"Ticket price data for vendor : <b>".$checkDataExists["VENDORNAME"]."</b>. Ticket : <b>".$checkDataExists["PRODUCTNAME"]." (".$paxRangeMin." - ".$paxRangeMax." pax)</b> is already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}

		$arrInsertUpdate=	array(
								"IDPRODUCT"			=>	$idProduct,
								"IDVENDOR"			=>	$idVendor,
								"VOUCHERSTATUS"		=>	$voucherStatus,
								"MINPAX"			=>	$paxRangeMin,
								"MAXPAX"			=>	$paxRangeMax,
								"PRICEADULT"		=>	$priceAdult,
								"PRICECHILD"		=>	$priceChild,
								"PRICEINFANT"		=>	$priceInfant,
								"NOTES"				=>	$notes
						   );
		$insertResult	=	$this->MainOperation->addData("t_vendorticketprice", $arrInsertUpdate);

		if(!$insertResult['status']){
			switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
		}
		$idInsert	=	$insertResult['insertID'];		
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Ticket price data saved", "idInsert"=>$idInsert));
	
	}
	
	private function checkInputData(){
		
		$arrVarValidate	=	array(
									array("optionProductEditor","option","Ticket"),
									array("optionVendorEditor","option","Vendor"),
									array("paxRangeMin","text","Minimum Pax"),
									array("paxRangeMax","text","Maximum Pax"),
									array("priceAdult","text","Adult Price"),
									array("priceChild","text","Child Price"),
									array("priceInfant","text","Infant Price")
								);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		}
		
		return true;
		
	}
	
	public function updateTicketVendorPrice(){

		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelTicket');
		
		$idVendorTicketPrice	=	validatePostVar($this->postVar, 'idVendorTicketPrice', true);
		$idProduct				=	validatePostVar($this->postVar, 'optionProductEditor', true);
		$idVendor				=	validatePostVar($this->postVar, 'optionVendorEditor', true);
		$voucherStatus			=	validatePostVar($this->postVar, 'optionVoucherStatus', false);
		$paxRangeMin			=	str_replace(",", "", validatePostVar($this->postVar, 'paxRangeMin', true));
		$paxRangeMax			=	str_replace(",", "", validatePostVar($this->postVar, 'paxRangeMax', true));
		$priceAdult				=	str_replace(",", "", validatePostVar($this->postVar, 'priceAdult', true));
		$priceChild				=	str_replace(",", "", validatePostVar($this->postVar, 'priceChild', true));
		$priceInfant			=	str_replace(",", "", validatePostVar($this->postVar, 'priceInfant', true));
		$notes					=	validatePostVar($this->postVar, 'notes', false);
		$checkDataExists		=	$this->ModelTicket->checkDataExists($idProduct, $idVendor, $paxRangeMin, $paxRangeMax, $idVendorTicketPrice);

		if($checkDataExists){
			$msg		=	"Ticket price data for vendor : <b>".$checkDataExists["VENDORNAME"]."</b>. Ticket : <b>".$checkDataExists["PRODUCTNAME"]."</b> is already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}
		
		$arrUpdate		=	array(
								"IDPRODUCT"		=>	$idProduct,
								"IDVENDOR"		=>	$idVendor,
								"VOUCHERSTATUS"	=>	$voucherStatus,
								"MINPAX"		=>	$paxRangeMin,
								"MAXPAX"		=>	$paxRangeMax,
								"PRICEADULT"	=>	$priceAdult,
								"PRICECHILD"	=>	$priceChild,
								"PRICEINFANT"	=>	$priceInfant,
								"NOTES"			=>	$notes
							);
		$updateResult	=	$this->MainOperation->updateData("t_vendorticketprice", $arrUpdate, "IDVENDORTICKETPRICE", $idVendorTicketPrice);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Ticket price data has been updated"));
	
	}
	
	public function deleteTicketVendorPrice(){

		$this->load->model('MainOperation');
		$data				=	array();
		
		$idVendorTicketPrice=	validatePostVar($this->postVar, 'idData', true);
		$arrWhere			=	array("IDVENDORTICKETPRICE" => $idVendorTicketPrice);
		$deleteResult		=	$this->MainOperation->deleteData("t_vendorticketprice", $arrWhere);
		
		if(!$deleteResult['status']){
			switchMySQLErrorCode($deleteResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Ticket price data has been deleted"));
	
	}
	
}