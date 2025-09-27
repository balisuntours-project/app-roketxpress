<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class AdditionalCost extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(3);

		if($functionName != "uploadTransferReceipt" && $_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->postVar	=	decodeJsonPost();
			$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid submission data"));
			$this->newToken	=	isLoggedIn($this->token, true);
		}
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataAdditionalCostApproval(){

		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelAdditionalCost');
		
		$idDriverType	=	validatePostVar($this->postVar, 'idDriverType', false);
		$idDriver		=	validatePostVar($this->postVar, 'idDriver', false);
		$startDate		=	validatePostVar($this->postVar, 'startDate', true);
		$endDate		=	validatePostVar($this->postVar, 'endDate', true);
		$startDate		=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate		=	$startDate->format('Y-m-d');
		$endDate		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate		=	$endDate->format('Y-m-d');
		$viewRequestOnly=	validatePostVar($this->postVar, 'viewRequestOnly', false);
		$dataTable		=	$this->ModelAdditionalCost->getDataAdditionalCostApproval($idDriverType, $idDriver, $startDate, $endDate, $viewRequestOnly);
		
		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No Data Found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
	public function submitValidateAdditionalCost(){
		
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelAdditionalCost');
		
		$idAdditionalCost	=	validatePostVar($this->postVar, 'idAdditionalCost', true);
		$status				=	validatePostVar($this->postVar, 'status', true);
		$detailUser			=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$detailUser['NAME'];
		$strStatus			=	$status == "1" ? "Approved" : "Rejected";
		
		$arrUpdate			=	array(
										"USERAPPROVAL"		=>	$userAdminName,
										"DATETIMEAPPROVAL"	=>	date('Y-m-d H:i:s'),
										"STATUSAPPROVAL"	=>	$status
								);
		$procUpdate			=	$this->MainOperation->updateData('t_reservationadditionalcost', $arrUpdate, 'IDRESERVATIONADDITIONALCOST', $idAdditionalCost);
		
		if($procUpdate['status']){
		
			if(PRODUCTION_URL){
				$this->calculateAdditionalCostRequest();
			}

			setResponseOk(
							array(
									"token"				=>	$this->newToken,
									"msg"				=>	"Additional cost has been ".$strStatus,
									"strStatus"			=>	$strStatus,
									"userValidate"		=>	$userAdminName,
									"idAdditionalCost"	=>	$idAdditionalCost,
									"statusApproval"	=>	$status
							)
			);

		} else {
			switchMySQLErrorCode($procUpdate['errCode'], $this->newToken);
		}		
		
	}
	
	public function calculateAdditionalCostRequest(){
		$this->load->model('FinanceDriver/ModelAdditionalCost');
		$totalAdditionalCostRequest	=	$this->ModelAdditionalCost->getTotalAdditionalCostRequest();
		try {
			$factory	=	(new Factory)
							->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
							->withDatabaseUri(FIREBASE_RTDB_URI);
			$database	=	$factory->createDatabase();
			$reference	=	$database->getReference(FIREBASE_RTDB_MAINREF_NAME."unprocessedFinanceDriver/additionalCost")
							->set([
								'newAdditionalCostStatus'	=>	false,
								'newAdditionalCostTotal'	=>	$totalAdditionalCostRequest,
								'timestampUpdate'			=>	gmdate("YmdHis")
							]);
		} catch (Exception $e) {
		}
	}
	
	public function getDataAdditionalCostHistory(){

		$this->load->model('FinanceDriver/ModelAdditionalCost');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$idDriverType	=	validatePostVar($this->postVar, 'idDriverType', false);
		$idDriver		=	validatePostVar($this->postVar, 'idDriver', false);
		$startDate		=	validatePostVar($this->postVar, 'startDate', true);
		$endDate		=	validatePostVar($this->postVar, 'endDate', true);
		$startDate		=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate		=	$startDate->format('Y-m-d');
		$endDate		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate		=	$endDate->format('Y-m-d');
		$dataTable		=	$this->ModelAdditionalCost->getDataAdditionalCostHistory($page, 25, $idDriverType, $idDriver, $startDate, $endDate);
		
		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No Data Found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
	public function searchListReservationForAdditionalCost(){

		$this->load->model('FinanceDriver/ModelAdditionalCost');
		
		$idDriver			=	validatePostVar($this->postVar, 'idDriver', false);
		$reservationDate	=	validatePostVar($this->postVar, 'reservationDate', false);
		$reservationDate	=	DateTime::createFromFormat('d-m-Y', $reservationDate);
		$reservationDate	=	$reservationDate->format('Y-m-d');
		$reservationKeyword	=	validatePostVar($this->postVar, 'reservationKeyword', false);		
		$reservationList	=	$this->ModelAdditionalCost->getListReservationByKeywordAndDate($idDriver, $reservationDate, $reservationKeyword);
		
		if(!$reservationList){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		}
		
		setResponseOk(
			array(
				"token"				=>	$this->newToken,
				"reservationList"	=>	$reservationList
			)
		);
	
	}
	
	public function uploadTransferReceipt($idTransferList){

		if((($_FILES["file"]["type"] == "image/jpeg")
			|| ($_FILES["file"]["type"] == "image/jpg")
			|| ($_FILES["file"]["type"] == "image/png"))
			&& ($_FILES["file"]["size"] <= 500000)){
			if ($_FILES["file"]["error"] > 0) {
				setResponseInternalServerError(array("msg"=>"Failed to upload this file. File is broken"));
			}
			
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. This file type is not allowed (".$_FILES["file"]["type"].") or file size is too big (".$_FILES["file"]["size"].")"));
		}
		
		$dir		=	PATH_STORAGE_ADDITIONAL_COST_IMAGE;
		$extension	=	pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		$namaFile	=	"AdditionalCostManual"."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$namaFile);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "urlTransferReceipt"=>URL_ADDITIONAL_COST_IMAGE.$namaFile, "transferReceiptFileName"=>$namaFile));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}
		
	}
	
	public function saveNewAdditionalCost(){

		$this->load->model('MainOperation');
		
		$idDriver			=	validatePostVar($this->postVar, 'idDriver', false);
		$idReservationDetail=	validatePostVar($this->postVar, 'idReservationDetail', false);
		$idCostType			=	validatePostVar($this->postVar, 'idCostType', false);
		$nominal			=	validatePostVar($this->postVar, 'nominal', false);
		$nominal			=	preg_replace("/[^0-9]/", "", $nominal);
		$description		=	validatePostVar($this->postVar, 'description', true);
		$receiptFileName	=	validatePostVar($this->postVar, 'receiptFileName', true);
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$dataUserAdmin['NAME'];
		
		if($nominal <= 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please input valid nominal"));
		}

		if(!isset($idDriver) || $idDriver == ""  || $idDriver == 0 || !isset($idReservationDetail) || $idReservationDetail == ""  || $idReservationDetail == 0 || !isset($idCostType) || $idCostType == ""  || $idCostType == 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
		}
		
		$arrInsert	=	array(
							"IDRESERVATIONDETAILS"	=>	$idReservationDetail,
							"IDDRIVER"				=>	$idDriver,
							"IDADDITIONALCOSTTYPE"	=>	$idCostType,
							"DESCRIPTION"			=>	$description,
							"NOMINAL"				=>	$nominal,
							"IMAGERECEIPT"			=>	$receiptFileName,
							"DATETIMEINPUT"			=>	date('Y-m-d H:i:s'),
							"DATETIMEAPPROVAL"		=>	date('Y-m-d H:i:s'),
							"USERAPPROVAL"			=>	$userAdminName,
							"STATUSAPPROVAL"		=>	1
						);
		$procInsert	=	$this->MainOperation->addData('t_reservationadditionalcost', $arrInsert);
		
		if(!$procInsert['status']){
			switchMySQLErrorCode($procInsert['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New additional cost data has been saved"));
	
	}
	
}