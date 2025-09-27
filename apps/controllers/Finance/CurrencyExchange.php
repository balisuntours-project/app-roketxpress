<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CurrencyExchange extends CI_controller {
	
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
	
	public function getDataCurrencyExchange(){
		$this->load->model('Finance/ModelCurrencyExchange');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$currency		=	validatePostVar($this->postVar, 'currency', true);
		$dataTable		=	$this->ModelCurrencyExchange->getDataCurrencyExchange($currency, $page);
		$currentExchange=	$this->ModelCurrencyExchange->getCurrentCurrencyExchange($currency);
		
		setResponseOk(
			array(
				"token"				=>	$this->newToken,
				"result"			=>	$dataTable,
				"currentExchange"	=>	$currentExchange
			)
		);
	}
	
	public function addDataCurrencyExchange(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelCurrencyExchange');
		
		$currency		=	validatePostVar($this->postVar, 'optionCurrencyEditor', true);
		$dateStart		=	validatePostVar($this->postVar, 'dateStart', true);
		$dateStart		=	DateTime::createFromFormat('d-m-Y', $dateStart);
		$dateStart		=	$dateStart->format('Y-m-d');
		$exchangeValue	=	str_replace(",", "", validatePostVar($this->postVar, 'exchangeValue', true)) * 1;
		$checkDataExists=	$this->ModelCurrencyExchange->checkDataExists($currency, $dateStart);
		$arrInsert		=	array(
								"CURRENCY"		=>	$currency,
								"DATESTART"		=>	$dateStart,
								"EXCHANGEVALUE"	=>	$exchangeValue
							);

		if($checkDataExists){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Currency exchange data is already exists. Please change date start"));
		}

		$insertResult	=	$this->MainOperation->addData("t_currencyexchange", $arrInsert);
		if(!$insertResult['status']){
			switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
		}

		$this->updateRecordExchangeCurrencyByPeriod($currency, $dateStart, $exchangeValue);
		$this->updateHelperExchangeCurrency($currency, $dateStart, $exchangeValue);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New data has been added"));
	}
	
	public function updateCurrencyExchange(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelCurrencyExchange');
		
		$idCurrencyExchange	=	validatePostVar($this->postVar, 'idCurrencyExchange', true);
		$currency			=	validatePostVar($this->postVar, 'currency', true);
		$originDateStart	=	validatePostVar($this->postVar, 'originDateStart', false);
		$originDateStart	=	DateTime::createFromFormat('d-m-Y', $originDateStart);
		$originDateStart	=	$originDateStart->format('Y-m-d');
		$dateStart			=	validatePostVar($this->postVar, 'dateStart', true);
		$dateStart			=	DateTime::createFromFormat('d-m-Y', $dateStart);
		$dateStart			=	$dateStart->format('Y-m-d');
		$exchangeValue		=	str_replace(",", "", validatePostVar($this->postVar, 'exchangeValue', true)) * 1;
		$checkDataExists	=	$this->ModelCurrencyExchange->checkDataExists($currency, $dateStart, $idCurrencyExchange);

		if($checkDataExists){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Currency exchange data is already exists. Please change date start"));
		}
		
		$arrUpdate		=	array(
								"DATESTART"		=>	$dateStart,
								"EXCHANGEVALUE"	=>	$exchangeValue
							);
		$updateResult	=	$this->MainOperation->updateData("t_currencyexchange", $arrUpdate, "IDCURRENCYEXCHANGE", $idCurrencyExchange);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}

		$dataCurrencyStart	=	$this->ModelCurrencyExchange->getDataCurrencyStart($currency, $originDateStart);
		if($dataCurrencyStart){
			$dateStartUpdate		=	$dataCurrencyStart['DATESTART'];
			$exchangeValueUpdate	=	$dataCurrencyStart['EXCHANGEVALUE'];
			$this->updateRecordExchangeCurrencyByPeriod($currency, $dateStartUpdate, $exchangeValueUpdate);
		}
		$this->updateRecordExchangeCurrencyByPeriod($currency, $dateStart, $exchangeValue);
		$this->updateHelperExchangeCurrency($currency, $dateStart, $exchangeValue);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data has been updated"));
	}
	
	public function deleteCurrencyExchange(){

		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelCurrencyExchange');

		$data				=	array();
		$idCurrencyExchange	=	validatePostVar($this->postVar, 'idData', true);
		$currency			=	validatePostVar($this->postVar, 'currencyName', true);
		$dateStart			=	validatePostVar($this->postVar, 'dateStart', true);
		$deleteResult		=	$this->MainOperation->deleteData("t_currencyexchange", array("IDCURRENCYEXCHANGE" => $idCurrencyExchange));
		
		if(!$deleteResult['status']){
			switchMySQLErrorCode($deleteResult['errCode'], $this->newToken);
		}
		
		$dataCurrencyStart	=	$this->ModelCurrencyExchange->getDataCurrencyStart($currency, $dateStart);
		
		if($dataCurrencyStart){
			$dateStartUpdate=	$dataCurrencyStart['DATESTART'];
			$exchangeValue	=	$dataCurrencyStart['EXCHANGEVALUE'];
			$this->updateRecordExchangeCurrencyByPeriod($currency, $dateStartUpdate, $exchangeValue);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data has been deleted"));
	
	}
	
	private function updateRecordExchangeCurrencyByPeriod($currency, $dateStart, $exchangeValue){
		
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelCurrencyExchange');
		
		$dateEnd				=	$this->ModelCurrencyExchange->getEndDateUpdateRecord($currency, $dateStart);
		$whereCustomString		=	"INCOMEAMOUNTCURRENCY = '".$currency."' AND RESERVATIONDATESTART >= '".$dateStart."'";
		$whereCustomStringPymt	=	"A.AMOUNTCURRENCY = '".$currency."' AND B.RESERVATIONDATESTART >= '".$dateStart."'";
		$arrFieldValue			=	array(
										"INCOMEEXCHANGECURRENCY"	=>	$exchangeValue,
										"INCOMEAMOUNTIDR"			=>	"FALSE-INCOMEAMOUNT * ".$exchangeValue
									);
		
		if($dateEnd){
			$whereCustomString		.=	" AND RESERVATIONDATESTART < '".$dateEnd."'";	
			$whereCustomStringPymt	.=	" AND B.RESERVATIONDATESTART < '".$dateEnd."'";	
		}
		
		$strQueryUpdatePayment	=	"UPDATE t_reservationpayment A
									 LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
									 SET A.EXCHANGECURRENCY = ".$exchangeValue.", A.AMOUNTIDR = A.AMOUNT * ".$exchangeValue."
									 WHERE ".$whereCustomStringPymt;
		log_message('info', $strQueryUpdatePayment);
		$this->MainOperation->updateDataWhereCustomString("t_reservation", $arrFieldValue, $whereCustomString);
		$this->MainOperation->customQuery($strQueryUpdatePayment);
		
		return true;
		
	}
	
	private function updateHelperExchangeCurrency($currency, $dateStart, $exchangeValue){
		
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelCurrencyExchange');

		$isLatestDateStart	=	$this->ModelCurrencyExchange->isLatestDateStart($currency, $dateStart);
		
		if($isLatestDateStart){
			$this->MainOperation->updateData("helper_exchangecurrency", array("EXCHANGETOIDR"=>$exchangeValue), "CURRENCY", $currency);
		}
		
		return true;
		
	}
	
}