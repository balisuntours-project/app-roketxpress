<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transport extends CI_controller {
	
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
	
	public function getDataDriverFees(){
		$this->load->model('ProductSetting/ModelTransport');
		
		$driverType		=	validatePostVar($this->postVar, 'driverType', false);
		$searchKeyword	=	validatePostVar($this->postVar, 'searchKeyword', false);		
		$dataTable		=	$this->ModelTransport->getDataDriverFee($driverType, $searchKeyword);
		
		if(!$dataTable) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No Data Found"));
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	}
	
	public function getOptionTransportProduct(){
		$this->load->model('ProductSetting/ModelTransport');
		$dataProduct	=	$this->ModelTransport->getDataTransportProduct();
		
		setResponseOk(array("token"=>$this->newToken, "dataProduct"=>$dataProduct));	
	}
	
	public function getDetailDriverFee(){
		$this->load->model('ProductSetting/ModelTransport');
		
		$idDriverFee	=	validatePostVar($this->postVar, 'idDriverFee', true);
		$detailData		=	$this->ModelTransport->getDetailDriverFee($idDriverFee);
		
		if(!$detailData) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found"));
		
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$detailData));
	}
	
	public function saveDriverFee(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelTransport');
		
		$idDriverFee			=	validatePostVar($this->postVar, 'idDriverFee', false);
		$idProduct				=	validatePostVar($this->postVar, 'optionProductTransport', true);
		$idDriverType			=	validatePostVar($this->postVar, 'optionDriverTypeEditor', true);
		$idSource				=	validatePostVar($this->postVar, 'optionSourceEditor', true);
		$jobType				=	validatePostVar($this->postVar, 'optionJobType', true);
		$jobRate				=	validatePostVar($this->postVar, 'optionJobRate', true);
		$lastJobrate			=	validatePostVar($this->postVar, 'lastJobrate', false);
		$scheduleType			=	validatePostVar($this->postVar, 'optionScheduleType', true);
		$idArea					=	validatePostVar($this->postVar, 'optionArea', false);
		$costTicketType			=	validatePostVar($this->postVar, 'optionCostTicketType', false);
		$costTicketType			=	!isset($costTicketType) || $costTicketType == "" ? 2 : $costTicketType;
		$costParkingType		=	validatePostVar($this->postVar, 'optionCostParkingType', false);
		$costParkingType		=	!isset($costParkingType) || $costParkingType == "" ? 1 : $costParkingType;
		$costMineralWaterType	=	validatePostVar($this->postVar, 'optionCostMineralWaterType', false);
		$costMineralWaterType	=	!isset($costMineralWaterType) || $costMineralWaterType == "" ? 2 : $costMineralWaterType;
		$costBreakfastType		=	validatePostVar($this->postVar, 'optionCostBreakfastType', false);
		$costBreakfastType		=	!isset($costBreakfastType) || $costBreakfastType == "" ? 2 : $costBreakfastType;
		$costLunchType			=	validatePostVar($this->postVar, 'optionCostLunchType', false);
		$costLunchType			=	!isset($costLunchType) || $costLunchType == "" ? 2 : $costLunchType;
		$bonusType				=	validatePostVar($this->postVar, 'optionBonusType', false);
		$bonusType				=	!isset($bonusType) || $bonusType == "" ? 2 : $bonusType;
		$costTicketNominal		=	preg_replace("/[^0-9]/", "", validatePostVar($this->postVar, 'costTicketNominal', false)) * 1;
		$costParkingNominal		=	preg_replace("/[^0-9]/", "", validatePostVar($this->postVar, 'costParkingNominal', false)) * 1;
		$costMineralWaterNominal=	preg_replace("/[^0-9]/", "", validatePostVar($this->postVar, 'costMineralWaterNominal', false)) * 1;
		$costBreakfastNominal	=	preg_replace("/[^0-9]/", "", validatePostVar($this->postVar, 'costBreakfastNominal', false)) * 1;
		$costLunchNominal		=	preg_replace("/[^0-9]/", "", validatePostVar($this->postVar, 'costLunchNominal', false)) * 1;
		$bonusNominal			=	preg_replace("/[^0-9]/", "", validatePostVar($this->postVar, 'bonusNominal', false)) * 1;
		$feeNominal				=	preg_replace("/[^0-9]/", "", validatePostVar($this->postVar, 'feeNominal', false)) * 1;
		$notes					=	validatePostVar($this->postVar, 'notes', false);
		
		if(!isset($idProduct) || $idProduct == "" || $idProduct <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select transport product!"));
		if(!isset($idDriverType) || $idDriverType == "" || $idDriverType <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select driver type!"));
		
		$isDriverFeeExist	=	$this->ModelTransport->isDriverFeeExist($idProduct, $idDriverType, $idSource);
		
		if($feeNominal <= 0){
			$arrDelete		=	array(
				"IDPRODUCT"		=>	$idProduct,
				"IDDRIVERTYPE"	=>	$idDriverType,
				"IDSOURCE"		=>	$idSource
			);
			$procDelete		=	$this->MainOperation->deleteData('t_driverfee', $arrDelete);
		
			if(!$procDelete['status']) switchMySQLErrorCode($procDelete['errCode'], $this->newToken);
			setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver fee has been deleted"));
		}
		
		$arrInsertUpdate=	array(
			"IDPRODUCT"				=>	$idProduct,
			"IDDRIVERTYPE"			=>	$idDriverType,
			"IDSOURCE"				=>	$idSource,
			"IDAREA"				=>	$idArea,
			"PRODUCTRANK"			=>	$this->ModelTransport->getRankDriverFee($idProduct),
			"JOBTYPE"				=>	$jobType,
			"JOBRATE"				=>	$jobRate,
			"SCHEDULETYPE"			=>	$scheduleType,
			"COSTTICKETTYPE"		=>	$costTicketType,
			"COSTPARKINGTYPE"		=>	$costParkingType,
			"COSTMINERALWATERTYPE"	=>	$costMineralWaterType,
			"COSTBREAKFASTTYPE"		=>	$costBreakfastType,
			"COSTLUNCHTYPE"			=>	$costLunchType,
			"BONUSTYPE"				=>	$bonusType,
			"COSTTICKET"			=>	$costTicketNominal,
			"COSTPARKING"			=>	$costParkingNominal,
			"COSTMINERALWATER"		=>	$costMineralWaterNominal,
			"COSTBREAKFAST"			=>	$costBreakfastNominal,
			"COSTLUNCH"				=>	$costLunchNominal,
			"BONUS"					=>	$bonusNominal,
			"FEENOMINAL"			=>	$feeNominal,
			"ADDITIONALINFO"		=>	$notes
		);
		
		if($isDriverFeeExist){
			if($idDriverFee == 0){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"The driver fee data you entered already exists, please search and edit to update the data"));
			}
		}
		
		if($idDriverFee <= 0){
			$procInsertUpdate	=	$this->MainOperation->addData('t_driverfee', $arrInsertUpdate);
		} else {
			$procInsertUpdate	=	$this->MainOperation->updateData('t_driverfee', $arrInsertUpdate, 'IDDRIVERFEE', $idDriverFee);
		}

		if($procInsertUpdate != null && $procInsertUpdate != 'null' && !$procInsertUpdate['status']){
			switchMySQLErrorCode($procInsertUpdate['errCode'], $this->newToken);
		}
		
		if($lastJobrate != $jobRate) {
			$this->updateLastJobRateDriver();
		}
		
		$this->updateNextScheduleDetail($idProduct, $idDriverType, $jobType, $jobRate, $scheduleType);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver fee has been set"));
	}
	
	private function checkInputData(){
		$arrVarValidate	=	array(
			array("optionJobType","option","Job Type"),
			array("optionJobRate","option","Job Rate"),
			array("optionScheduleType","option","Schedule Type")
		);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;		
	}

	private function updateLastJobRateDriver(){
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelTransport');
		
		$listDriver		=	$this->MainOperation->getAllDriverList();
		if($listDriver){
			foreach($listDriver as $keyDriver){
				$idDriver		=	$keyDriver->IDDRIVER;
				$strArrJobRate	=	$this->ModelTransport->getStrArrJobRateDriver($idDriver);
				
				if($strArrJobRate && $strArrJobRate != ""){
					$expStrArrJobRate	=	explode(",", $strArrJobRate);
					$totalArrJobRate	=	count($expStrArrJobRate);
					
					if($totalArrJobRate < 5){
						while($totalArrJobRate <= 5) {
							$expStrArrJobRate[]	=	1;
							$totalArrJobRate++;
						}
						$strArrJobRate	=	implode(',', $expStrArrJobRate);
					}
					
					$this->MainOperation->updateData("m_driver", array("LASTJOBRATE"=>$strArrJobRate), "IDDRIVER", $idDriver);
				}
			}
		}
		
		return true;
	}
	
	private function updateNextScheduleDetail($idProduct, $idDriverType, $jobType, $jobRate, $scheduleType){
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelTransport');
		
		$strArrReservationDetails	=	$this->ModelTransport->getNextScheduleListByProduct($idProduct);
		if($strArrReservationDetails){
			$arrReservationDetails	=	explode(",", $strArrReservationDetails);
			$this->MainOperation->updateDataIn(
				"t_reservationdetails",
				array(
					"IDDRIVERTYPE"	=>	$idDriverType,
					"JOBTYPE"		=>	$jobType,
					"JOBRATE"		=>	$jobRate,
					"SCHEDULETYPE"	=>	$scheduleType,
				),
				"IDRESERVATIONDETAILS",
				$arrReservationDetails
			);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver fee has been set", "strArrReservationDetails"=>$strArrReservationDetails));
		return true;
	}

	public function deleteDriverFee(){
		$this->load->model('MainOperation');
		$data			=	array();
		
		$idDriverFee	=	validatePostVar($this->postVar, 'idData', true);
		$arrWhere		=	array("IDDRIVERFEE" => $idDriverFee);
		$deleteResult	=	$this->MainOperation->deleteData("t_driverfee", $arrWhere);
		
		if(!$deleteResult['status']){
			switchMySQLErrorCode($deleteResult['errCode'], $this->newToken);
		}
		
		$this->MainOperation->deleteData("t_autodetailstemplateitem", ["IDPRODUCTTYPE" => 2, "IDPRODUCTFEE" => $idDriverFee]);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Transport fee for driver has been deleted"));
	}
	
	public function getTransportProductRank(){
		$this->load->model('ProductSetting/ModelTransport');
		
		$transportProductRank	=	$this->ModelTransport->getTransportProductRank();
		if(!$transportProductRank){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Transport product rank not found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "transportProductRank"=>$transportProductRank));
	}
	
	public function saveTransportProductRank(){
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelTransport');
		$arrProductRank		=	validatePostVar($this->postVar, 'arrProductRank', true);
		$iRank				=	1;
		
		foreach($arrProductRank as $idProduct){
			$this->MainOperation->updateData("t_driverfee", array("PRODUCTRANK"=>$iRank), "IDPRODUCT", $idProduct);
			$dataReservationDetailsProduct	=	$this->ModelTransport->getDataReservationDetailsProduct($idProduct);
			
			if($dataReservationDetailsProduct){
				$arrReservationDetailsProduct	=	explode(",", $dataReservationDetailsProduct);
				$this->MainOperation->updateDataIn("t_reservationdetails", array("PRODUCTRANK"=>$iRank), "IDRESERVATIONDETAILS", $arrReservationDetailsProduct);
			}
			
			$iRank++;
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Transport product rank has been updated"));
	}
}