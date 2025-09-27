<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterCarType extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$this->postVar	=	decodeJsonPost();
		$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid request"));
		$this->newToken	=	isLoggedIn($this->token, true);
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataTable(){

		$this->load->model('Master/ModelMasterCarType');
		$page			=	validatePostVar($this->postVar, 'page', true);
		$keywordSearch	=	validatePostVar($this->postVar, 'keywordSearch', false);
		$dataTable		=	$this->ModelMasterCarType->getDataCarType(array("keywordSearch" => $keywordSearch), $page);
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
	public function insertData(){
		
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterCarType');
		
		$carTypeName	=	validatePostVar($this->postVar, 'carTypeName', true);
		$carTypeDesc	=	validatePostVar($this->postVar, 'carTypeDescription', false);
		$msg			=	"";
		$checkDataExists=	$this->ModelMasterCarType->checkDataExists($carTypeName);
		$arrInsertUpdate=	array(
									"CARTYPE"		=>	$carTypeName,
									"DESCRIPTION"	=>	$carTypeDesc
						   );

		if($checkDataExists){
			$msg		=	"Car type with name : <b>".$checkDataExists['CARTYPE']."</b> already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}

		$insertResult	=	$this->MainOperation->addData("m_cartype", $arrInsertUpdate);

		if(!$insertResult['status']){
			switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
		}

		$idInsert		=	$insertResult['insertID'];		
		$optionHelper	=	$this->getArrDataOptionHelper();

		setResponseOk(array("token"=>$this->newToken, "msg"=>"New car type data saved", "idInsert"=>$idInsert, "optionHelper"=>$optionHelper));
	
	}
	
	public function detailData(){

		$this->load->model('Master/ModelMasterCarType');
		$idData			=	validatePostVar($this->postVar, 'idData', true);
		$dataDetail		=	$this->ModelMasterCarType->getDataCarTypeById($idData);
		
		setResponseOk(array("token"=>$this->newToken, "data"=>$dataDetail));
	
	}
	
	public function updateData(){

		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterCarType');
		
		$idCarType		=	$this->postVar['idData'];
		$carTypeName	=	validatePostVar($this->postVar, 'carTypeName', true);
		$carTypeDesc	=	validatePostVar($this->postVar, 'carTypeDescription', false);
		$msg			=	"";
		$checkDataExists=	$this->ModelMasterCarType->checkDataExists($carTypeName, $idCarType);
		$arrUpdate		=	array(
								"CARTYPE"		=>	$carTypeName,
								"DESCRIPTION"	=>	$carTypeDesc
						   );

		if($checkDataExists){
			$msg		=	"Car type with name : <b>".$checkDataExists['CARTYPE']."</b> already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}
		
		$updateResult	=	$this->MainOperation->updateData("m_cartype", $arrUpdate, "IDCARTYPE", $idCarType);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		$optionHelper	=	$this->getArrDataOptionHelper();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Car Type data has been updated", "optionHelper"=>$optionHelper));
	
	}
	
	private function checkInputData(){
		
		$arrVarValidate	=	array(
									array("carTypeName","text","Car Type Name"),
								);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		}
		
		return true;
		
	}
	
	private function getArrDataOptionHelper(){

		$this->load->model('ModelOptionHelper');
		$optionHelper	=	$this->ModelOptionHelper->getDataOptionHelperCarType();
		
		return array($optionHelper);

	}
	
}