<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterSource extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(2);

		if($functionName != "uploadLogoSource" && $_SERVER['REQUEST_METHOD'] === 'POST'){
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
	
	public function getDataTable(){

		$this->load->model('Master/ModelMasterSource');
		$this->load->model('MainOperation');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$keywordSearch	=	validatePostVar($this->postVar, 'keywordSearch', false);
		$idLevelUser	=	$this->MainOperation->getLevelUser($this->newToken);
		$dataTable		=	$this->ModelMasterSource->getDataSource(array("keywordSearch" => $keywordSearch), $page);
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
	public function insertData(){
		
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterSource');
		
		$data					=	array();
		$idInsert				=	0;
		$sourceName				=	validatePostVar($this->postVar, 'sourceName', true);
		$upsellingType			=	validatePostVar($this->postVar, 'upsellingType', true);
		$reviewBonusPunishment	=	validatePostVar($this->postVar, 'reviewBonusPunishment', true);
		$defaultCurrency		=	validatePostVar($this->postVar, 'defaultCurrency', true);
		$review5StarPoint		=	validatePostVar($this->postVar, 'review5StarPoint', true);
		$logoSourceName			=	$this->postVar['logoSourceName'] == "" ? "default.png" : $this->postVar['logoSourceName'];
		$msg					=	"";
		
		$checkDataExists=	$this->ModelMasterSource->checkDataExists($sourceName);
		$arrInsertUpdate=	array(
								"SOURCENAME"			=>	$sourceName,
								"UPSELLINGTYPE"			=>	$upsellingType,
								"REVIEW5STARPOINT"		=>	$review5StarPoint,
								"CALCULATEBONUSREVIEW"	=>	$reviewBonusPunishment,
								"DEFAULTCURRENCY"		=>	$defaultCurrency,
								"LOGO"					=>	$logoSourceName
							);

		if($checkDataExists){
			$msg						=	"Source data is already exists. Existing data is updated";
			$idInsert					=	$checkDataExists['idData'];
			$arrInsertUpdate['STATUS']	=	1;
			$updateResult				=	$this->MainOperation->updateData("m_source", $arrInsertUpdate, "IDSOURCE", $idInsert);
		} else {
			$msg						=	"New data has been added";
			$insertResult				=	$this->MainOperation->addData("m_source", $arrInsertUpdate);
		}

		if($idInsert == 0){
			if(!$insertResult['status']) switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
			$idInsert		=	$insertResult['insertID'];		
		}
		
		$this->curlCalculateReviewBonusData();
		$optionHelper	=	$this->getArrDataOptionHelper();
		setResponseOk(array("token"=>$this->newToken, "msg"=> $msg, "idInsert"=>$idInsert, "optionHelper"=>$optionHelper));
	}
	
	public function detailData(){

		$this->load->model('Master/ModelMasterSource');
		$idData			=	validatePostVar($this->postVar, 'idData', true);
		$dataDetail		=	$this->ModelMasterSource->getDataSourceById($idData);
		
		setResponseOk(array("token"=>$this->newToken, "data"=>$dataDetail));
	
	}
	
	public function updateData(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterSource');
		
		$idSource				=	validatePostVar($this->postVar, 'idData', true);
		$sourceName				=	validatePostVar($this->postVar, 'sourceName', true);
		$upsellingType			=	validatePostVar($this->postVar, 'upsellingType', true);
		$reviewBonusPunishment	=	validatePostVar($this->postVar, 'reviewBonusPunishment', true);
		$defaultCurrency		=	validatePostVar($this->postVar, 'defaultCurrency', true);
		$review5StarPoint		=	validatePostVar($this->postVar, 'review5StarPoint', true);
		$logoSourceName			=	$this->postVar['logoSourceName'] == "" ? "default.png" : $this->postVar['logoSourceName'];
		
		$checkDataExists=	$this->ModelMasterSource->checkDataExists($sourceName, $idSource);
		if($checkDataExists && $checkDataExists['STATUS'] != 1){
			$msg		=	"Data with source name ".$sourceName." is already exists. The old data has been restored";
			$idSource	=	$checkDataExists['idData'];
		} else if($checkDataExists && $checkDataExists['STATUS'] == 1) {
			$msg		=	"Data with source name ".$sourceName." is already exists. This operation is forbidden";
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$msg));
		} else {
			$msg		=	"Data has been updated";
		}
		
		$arrUpdate		=	array(
								"STATUS"				=>	1,
								"SOURCENAME"			=>	$sourceName,
								"UPSELLINGTYPE"			=>	$upsellingType,
								"REVIEW5STARPOINT"		=>	$review5StarPoint,
								"CALCULATEBONUSREVIEW"	=>	$reviewBonusPunishment,
								"DEFAULTCURRENCY"		=>	$defaultCurrency,
								"LOGO"					=>	$logoSourceName
							);
		$updateResult	=	$this->MainOperation->updateData("m_source", $arrUpdate, "IDSOURCE", $idSource);
		
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		
		$this->curlCalculateReviewBonusData();
		$optionHelper	=	$this->getArrDataOptionHelper();
		setResponseOk(array("token"=>$this->newToken, "msg"=>$msg, "optionHelper"=>$optionHelper));	
	}
	
	private function checkInputData(){
		
		$arrVarValidate	=	array(array("sourceName","text","Source Name"), array("upsellingType","option","Upselling Type"));
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		}
		
		return true;
		
	}
	
	public function deleteData(){
		$this->load->model('MainOperation');
		$data			=	array();
		
		$idSource		=	validatePostVar($this->postVar, 'idData', true);
		$arrUpdate		=	array("STATUS" => -2);
		$updateResult	=	$this->MainOperation->updateData("m_source", $arrUpdate, "IDSOURCE", $idSource);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		$this->curlCalculateReviewBonusData();
		$optionHelper	=	$this->getArrDataOptionHelper();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data has been deleted", "optionHelper"=>$optionHelper));
	}
	
	public function uploadLogoSource($idSource){

		$this->load->model('MainOperation');
		
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
		
		$image_info		= getimagesize($_FILES["file"]["tmp_name"]);
		$image_width	= $image_info[0];
		$image_height	= $image_info[1];
		
		if($image_width <> $image_height || $image_width > 200 || $image_height > 200){
			setResponseInternalServerError(array("msg"=>"Length and width should not be more than 200 pixel and image must has a square shape."));
		}

		$dir		=	PATH_SOURCE_LOGO;
		$extension	=	pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		$namaFile	=	"LogoSource"."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$namaFile);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "urlLogoSource"=>URL_SOURCE_LOGO.$namaFile, "logoSourceName"=>$namaFile, "defaultHeight"=>$image_height."px"));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}
		
	}
	
	private function getArrDataOptionHelper(){

		$this->load->model('ModelOptionHelper');
		$optionHelper	=	$this->ModelOptionHelper->getDataOptionHelperSource();
		
		return array($optionHelper);

	}
	
	private function curlCalculateReviewBonusData(){
		$this->load->model('MainOperation');

		$arrIdDriverReviewBonusPeriod	=	$this->MainOperation->getArrIdDriverReviewBonusPeriod();
		$arrData						=	["arrIdDriverReviewBonusPeriod" => $arrIdDriverReviewBonusPeriod];
		$base64JsonData					=	base64_encode(json_encode($arrData));
		$urlAPICalculateReview			=	BASE_URL."schedule/driverRatingPoint/apiCalculateBonusPunishmentReview/".$base64JsonData;

		try {
			$resAPICronCalculateReview	=	json_decode(trim(curl_get_file_contents($urlAPICalculateReview)));
		} catch(Exception $e) {
		}
		
		return true;
	}
	
}