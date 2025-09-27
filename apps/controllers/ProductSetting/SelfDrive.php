<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SelfDrive extends CI_controller {
	
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
	
	public function getDataSelfDriveFees(){

		$this->load->model('ProductSetting/ModelSelfDrive');
		
		$carType	=	validatePostVar($this->postVar, 'carType', false);
		$dataResult	=	$this->ModelSelfDrive->getDataSelfDriveFees($carType);
		
		if(!$dataResult){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataResult));
	
	}
	
	public function addSelfDriveFee(){
		
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelSelfDrive');
		
		$idVendor		=	validatePostVar($this->postVar, 'optionVendorFeeEditor', true);
		$idCarType		=	validatePostVar($this->postVar, 'optionCarTypeFeeEditor', true);
		$duration		=	validatePostVar($this->postVar, 'optionDurationFeeEditor', true);
		$nominalFee		=	str_replace(",", "", validatePostVar($this->postVar, 'nominalFee', true));
		$notes			=	validatePostVar($this->postVar, 'notes', false);
		$checkDataExists=	$this->ModelSelfDrive->checkDataExists($idVendor, $idCarType, $duration);

		if($checkDataExists){
			$msg		=	"Car fee data for vendor <b>".$checkDataExists["VENDORNAME"]." (".$checkDataExists["CARTYPE"]." : ".$checkDataExists["DURATION"]." Hours)</b> is already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}

		$arrInsertUpdate=	array("IDVENDOR"	=>	$idVendor,
								   "IDCARTYPE"	=>	$idCarType,
								   "DURATION"	=>	$duration,
								   "NOMINALFEE"	=>	$nominalFee,
								   "NOTES"		=>	$notes
						   );
		$insertResult	=	$this->MainOperation->addData("t_carselfdrivefee", $arrInsertUpdate);

		if(!$insertResult['status']){
			switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
		}
		$idInsert	=	$insertResult['insertID'];		
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New rent car fee data saved", "idInsert"=>$idInsert));
	
	}
	
	private function checkInputData(){
		
		$arrVarValidate	=	array(
									array("optionVendorFeeEditor","option","Vendor"),
									array("optionCarTypeFeeEditor","option","Car Type"),
									array("optionDurationFeeEditor","option","Duration"),
									array("nominalFee","text","Nominal Fee")
								);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		}
		
		return true;
		
	}
	
	public function detailSelfDriveFee(){

		$this->load->model('ProductSetting/ModelSelfDrive');
		$idCarSelfDriveFee	=	validatePostVar($this->postVar, 'idCarSelfDriveFee', true);
		$dataDetail			=	$this->ModelSelfDrive->getDetailSelfDriveFee($idCarSelfDriveFee);
		
		if(!$dataDetail){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No detail found. Please refresh your data"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "dataDetail"=>$dataDetail));
	
	}
	
	public function updateSelfDriveFee(){

		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelSelfDrive');
		
		$idCarSelfDriveFee	=	validatePostVar($this->postVar, 'idCarSelfDriveFee', true);
		$idVendor			=	validatePostVar($this->postVar, 'optionVendorFeeEditor', true);
		$idCarType			=	validatePostVar($this->postVar, 'optionCarTypeFeeEditor', true);
		$duration			=	validatePostVar($this->postVar, 'optionDurationFeeEditor', true);
		$nominalFee			=	str_replace(",", "", validatePostVar($this->postVar, 'nominalFee', true));
		$notes				=	validatePostVar($this->postVar, 'notes', false);
		$checkDataExists	=	$this->ModelSelfDrive->checkDataExists($idVendor, $idCarType, $duration, $idCarSelfDriveFee);

		if($checkDataExists){
			$msg		=	"Car fee data for vendor <b>".$checkDataExists["VENDORNAME"]." (".$checkDataExists["CARTYPE"]." : ".$checkDataExists["DURATION"]." Hours)</b> is already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}
		
		$arrUpdate		=	array("IDVENDOR"	=>	$idVendor,
								   "IDCARTYPE"	=>	$idCarType,
								   "DURATION"	=>	$duration,
								   "NOMINALFEE"	=>	$nominalFee,
								   "NOTES"		=>	$notes
							);
		$updateResult	=	$this->MainOperation->updateData("t_carselfdrivefee", $arrUpdate, "IDCARSELFDRIVEFEE", $idCarSelfDriveFee);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Rent Car Fee data has been updated"));
	
	}
	
	public function deleteSelfDriveFee(){

		$this->load->model('MainOperation');
		$data				=	array();
		
		$idCarSelfDriveFee	=	validatePostVar($this->postVar, 'idData', true);
		$arrWhere			=	array("IDCARSELFDRIVEFEE" => $idCarSelfDriveFee);
		$deleteResult		=	$this->MainOperation->deleteData("t_carselfdrivefee", $arrWhere);
		
		if(!$deleteResult['status']){
			switchMySQLErrorCode($deleteResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Rent car fee data has been deleted"));
	
	}
	
	public function getDataVendorCar(){
		$this->load->model('ProductSetting/ModelSelfDrive');
		
		$idVendor			=	validatePostVar($this->postVar, 'idVendor', false);
		$keywordSearch		=	validatePostVar($this->postVar, 'keywordSearch', false);
		$dataResult			=	$this->ModelSelfDrive->getDataVendorCar($idVendor, $keywordSearch);
		$dataDriverNoCar	=	$this->ModelSelfDrive->getDataDriverNoCar();
		
		if(!$dataResult) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "dataDriverNoCar"=>$dataDriverNoCar));
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataResult, "dataDriverNoCar"=>$dataDriverNoCar));
	}
	
	public function addVendorCar(){
		
		$this->checkInputDataVendorCar();
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelSelfDrive');
		
		$idVendor		=	validatePostVar($this->postVar, 'optionVendorCarEditor', true);
		$idCarType		=	validatePostVar($this->postVar, 'optionCarTypeEditor', true);
		$brandName		=	validatePostVar($this->postVar, 'brandVendorCar', true);
		$carModel		=	validatePostVar($this->postVar, 'modelVendorCar', true);
		$transmission	=	validatePostVar($this->postVar, 'optionTransmissionVendorCar', true);
		$carYear		=	validatePostVar($this->postVar, 'yearVendorCar', true);
		$platNumber		=	validatePostVar($this->postVar, 'platNumberVendorCar', true);
		$color			=	validatePostVar($this->postVar, 'colorVendorCar', false);
		$idDriverDefault=	validatePostVar($this->postVar, 'optionDriverCarEditor', false);
		$description	=	validatePostVar($this->postVar, 'description', false);
		$checkDataExists=	$this->ModelSelfDrive->checkDataCarExists($platNumber);

		if($checkDataExists){
			$msg		=	"Vendor car with plat number <b>".$checkDataExists["PLATNUMBER"]."</b> is already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}

		$arrInsertUpdate=	array(
			"IDVENDOR"		=>	$idVendor,
			"IDDRIVER"		=>	$idDriverDefault,
			"IDCARTYPE"		=>	$idCarType,
			"BRAND"			=>	$brandName,
			"MODEL"			=>	$carModel,
			"PLATNUMBER"	=>	$platNumber,
			"YEAR"			=>	$carYear,
			"TRANSMISSION"	=>	$transmission,
			"COLOR"			=>	$color,
			"DESCRIPTION"	=>	$description,
			"STATUS"			=>	1
		);
		$insertResult	=	$this->MainOperation->addData("t_carvendor", $arrInsertUpdate);

		if(!$insertResult['status']){
			switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
		}
		$idInsert	=	$insertResult['insertID'];		
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New vendor car data saved", "idInsert"=>$idInsert));
	
	}
	
	private function checkInputDataVendorCar(){
		
		$arrVarValidate	=	array(
									array("platNumberVendorCar","text","Plat Number"),
									array("yearVendorCar","text","Car Year"),
									array("modelVendorCar","text","Car Model"),
									array("brandVendorCar","text","Brand Name"),
									array("optionTransmissionVendorCar","option","Car Transmission"),
									array("optionCarTypeEditor","option","Car Type"),
									array("optionVendorCarEditor","option","Vendor")
								);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		}
		
		return true;
		
	}
	
	public function detailVendorCar(){

		$this->load->model('ProductSetting/ModelSelfDrive');
		$idCarVendor	=	validatePostVar($this->postVar, 'idCarVendor', true);
		$dataDetail		=	$this->ModelSelfDrive->getDetailVendorCar($idCarVendor);
		
		if(!$dataDetail){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No detail found. Please refresh your data"));
		}
		
		$idDriverDefault=	$dataDetail['IDDRIVER'];
		$dataDriverNoCar=	$this->ModelSelfDrive->getDataDriverNoCar($idDriverDefault);
		setResponseOk(array("token"=>$this->newToken, "dataDetail"=>$dataDetail, "dataDriverNoCar"=>$dataDriverNoCar));
	
	}
	
	public function updateVendorCar(){

		$this->checkInputDataVendorCar();
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelSelfDrive');
		
		$idCarVendor	=	$this->postVar['idCarVendor'];
		$idVendor		=	validatePostVar($this->postVar, 'optionVendorCarEditor', true);
		$idCarType		=	validatePostVar($this->postVar, 'optionCarTypeEditor', true);
		$brandName		=	validatePostVar($this->postVar, 'brandVendorCar', true);
		$carModel		=	validatePostVar($this->postVar, 'modelVendorCar', true);
		$transmission	=	validatePostVar($this->postVar, 'optionTransmissionVendorCar', true);
		$carYear		=	validatePostVar($this->postVar, 'yearVendorCar', true);
		$platNumber		=	validatePostVar($this->postVar, 'platNumberVendorCar', true);
		$color			=	validatePostVar($this->postVar, 'colorVendorCar', false);
		$idDriverDefault=	validatePostVar($this->postVar, 'optionDriverCarEditor', false);
		$description	=	validatePostVar($this->postVar, 'description', false);
		$checkDataExists=	$this->ModelSelfDrive->checkDataCarExists($platNumber, $idCarVendor);

		if($checkDataExists){
			$msg		=	"Vendor car with plat number <b>".$checkDataExists["PLATNUMBER"]."</b> is already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}
		
		$arrUpdate		=	array(
			"IDVENDOR"		=>	$idVendor,
			"IDDRIVER"		=>	$idDriverDefault,
			"IDCARTYPE"		=>	$idCarType,
			"BRAND"			=>	$brandName,
			"MODEL"			=>	$carModel,
			"PLATNUMBER"	=>	$platNumber,
			"YEAR"			=>	$carYear,
			"TRANSMISSION"	=>	$transmission,
			"COLOR"			=>	$color,
			"DESCRIPTION"	=>	$description,
			"STATUS"		=>	1
		);
		$updateResult	=	$this->MainOperation->updateData("t_carvendor", $arrUpdate, "IDCARVENDOR", $idCarVendor);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Vendor Car data has been updated"));
	
	}
	
	public function updateStatusVendorCar(){

		$this->load->model('MainOperation');
		$data			=	array();
		
		$dataSend		=	validatePostVar($this->postVar, 'idData', true);
		$dataSendExplode=	explode("|", $dataSend);
		$idCarVendor	=	$dataSendExplode[0];
		$status			=	$dataSendExplode[1];
		
		if($status == 1){
			$arrUpdate		=	array("STATUS" => 1);
			$strStatus		=	"reactivated";
		} else {
			$arrUpdate		=	array("STATUS"	=> -1);
			$strStatus		=	"deactivated";
		}
		$updateResult	=	$this->MainOperation->updateData("t_carvendor", $arrUpdate, "IDCARVENDOR", $idCarVendor);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Vendor car status has been ".$strStatus));
	
	}
	
}