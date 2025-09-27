<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterProduct extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
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
	
	public function getDataTable(){

		$this->load->model('Master/ModelMasterProduct');
		$page			=	validatePostVar($this->postVar, 'page', true);
		$keywordSearch	=	validatePostVar($this->postVar, 'keywordSearch', false);
		$dataTable		=	$this->ModelMasterProduct->getDataProduct(array("keywordSearch" => $keywordSearch), $page, 20);
		$dataProductType=	$this->ModelMasterProduct->getDataProductType();
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "dataProductType"=>$dataProductType));
	
	}
	
	public function insertData(){
		
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterProduct');
		
		$data				=	array();
		$idInsert			=	0;
		
		$productName		=	$this->postVar['productName'];
		$durationHour		=	$this->postVar['durationHour'];
		$description		=	$this->postVar['description'];
		$arrIdProductType	=	$this->postVar['arrIdProductType'];
		$msg				=	"";
		
		if(!is_array($arrIdProductType) || count($arrIdProductType) <= 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=> "Please select at least one product type detail!"));
		}
		
		$checkDataExists	=	$this->ModelMasterProduct->checkDataExists($productName);
		$arrInsertUpdate	=	array(
									"PRODUCTNAME"	=>	$productName,
									"DURATIONHOUR"	=>	$durationHour,
									"DESCRIPTION"	=>	$description
								);

		if($checkDataExists){
			$msg						=	"This product data is already exists. Existing data is updated";
			$idInsert					=	$checkDataExists['idData'];
			$arrInsertUpdate['STATUS']	=	1;
			$updateResult				=	$this->MainOperation->updateData("m_product", $arrInsertUpdate, "IDPRODUCT", $idInsert);
		} else {
			$msg						=	"New data has been added";
			$insertResult				=	$this->MainOperation->addData("m_product", $arrInsertUpdate);
			$idInsert					=	$insertResult['insertID'];
		}

		$totalProductUpd=	$this->insertUpdateProductTypeDetail($idInsert, $arrIdProductType);
		if($idInsert == 0){
			if(!$insertResult['status'] && $totalProductUpd <=0){
				switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
			}
			$idInsert		=	$insertResult['insertID'];		
		}
		
		$optionHelper	=	$this->getArrDataOptionHelper();
		setResponseOk(array("token"=>$this->newToken, "msg"=> $msg, "idInsert"=>$idInsert, "optionHelper"=>$optionHelper));
	
	}
	
	public function detailData(){

		$this->load->model('Master/ModelMasterProduct');
		$idData			=	validatePostVar($this->postVar, 'idData', true);
		$dataDetail		=	$this->ModelMasterProduct->getDataProductById($idData);
		
		setResponseOk(array("token"=>$this->newToken, "data"=>$dataDetail));
	
	}
	
	public function updateData(){

		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterProduct');
		
		$idProduct			=	$this->postVar['idData'];
		$productName		=	$this->postVar['productName'];
		$durationHour		=	$this->postVar['durationHour'];
		$description		=	$this->postVar['description'];
		$arrIdProductType	=	$this->postVar['arrIdProductType'];
		$msg				=	"";
		
		if(!is_array($arrIdProductType) || count($arrIdProductType) <= 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=> "Please select at least one product type detail!"));
		}

		$checkDataExists=	$this->ModelMasterProduct->checkDataExists($productName, $idProduct);
		if($checkDataExists && $checkDataExists['STATUS'] != 1){
			$msg		=	"Data product with name ".$productName." is already exists. The old data has been restored";
			$idProduct	=	$checkDataExists['idData'];
		} else if($checkDataExists && $checkDataExists['STATUS'] == 1) {
			$msg		=	"Data product with name ".$productName." is already exists. This operation is forbidden";
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$msg));
		} else {
			$msg		=	"Data has been updated";
		}
		
		$arrUpdate		=	array(
								"PRODUCTNAME"	=>	$productName,
								"DURATIONHOUR"	=>	$durationHour,
								"DESCRIPTION"	=>	$description
							);
		$updateResult	=	$this->MainOperation->updateData("m_product", $arrUpdate, "IDPRODUCT", $idProduct);
		$totalProductUpd=	$this->insertUpdateProductTypeDetail($idProduct, $arrIdProductType);
		
		if(!$updateResult['status'] && $totalProductUpd <= 0){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		$optionHelper	=	$this->getArrDataOptionHelper();
		setResponseOk(array("token"=>$this->newToken, "msg"=>$msg, "optionHelper"=>$optionHelper));
	
	}
	
	private function checkInputData(){
		
		$arrVarValidate	=	array(
									array("productName","text","Product Name")
							);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		}
		
		return true;
		
	}
	
	private function insertUpdateProductTypeDetail($idProduct, $arrIdProductType){
		
		$dataDetailProductType	=	$this->ModelMasterProduct->getDataDetailProductTypeById($idProduct);
		$arrIdProductTypeDB		=	array();
		$totalDataChages		=	0;
		
		if($dataDetailProductType){
			foreach($dataDetailProductType as $keyDetailProductType){
				if(!in_array($keyDetailProductType->IDPRODUCTTYPE, $arrIdProductType)){
					$this->MainOperation->deleteData("m_productdetailtype", array("IDPRODUCTDETAILTYPE"=>$keyDetailProductType->IDPRODUCTDETAILTYPE));
					$totalDataChages++;
				}
				$arrIdProductTypeDB[]	=	$keyDetailProductType->IDPRODUCTTYPE;
			}
		}
		
		foreach($arrIdProductType as $idProductType){
			
			if(!in_array($idProductType, $arrIdProductTypeDB)){
				$this->MainOperation->addData("m_productdetailtype", array("IDPRODUCT"=>$idProduct, "IDPRODUCTTYPE"=>$idProductType));
				$totalDataChages++;
			}
			
		}
		
		return $totalDataChages;
		
	}
	
	public function deleteData(){

		$this->load->model('MainOperation');
		$data			=	array();
		
		$idProduct		=	validatePostVar($this->postVar, 'idData', true);
		$arrUpdate		=	array("STATUS" => -2);
		$updateResult	=	$this->MainOperation->updateData("m_product", $arrUpdate, "IDPRODUCT", $idProduct);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		$optionHelper	=	$this->getArrDataOptionHelper();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data has been deleted", "optionHelper"=>$optionHelper));
	
	}
	
	private function getArrDataOptionHelper(){

		$this->load->model('ModelOptionHelper');
		$optionHelper	=	$this->ModelOptionHelper->getDataOptionHelperProductTicket();
		
		return array($optionHelper);

	}
	
}