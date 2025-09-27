<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OptionHelper extends CI_controller {
	
	public function __construct(){
        parent::__construct();
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataOption($userToken){
		
		$this->load->model('ModelOptionHelper');
		$this->load->model('MainOperation');
		
		$idUser						=	$this->MainOperation->getIDUserAdmin($userToken);
		$idLevelUser				=	$this->MainOperation->getLevelUser($userToken);
		$dataUserLevel				=	$this->ModelOptionHelper->getDataOptionHelperUserLevel();
		$dataUserPartnerLevel		=	$this->ModelOptionHelper->getDataOptionHelperUserPartnerLevel();
		$dataArea					=	$this->ModelOptionHelper->getDataOptionHelperArea();
		$dataAreaNonTBA				=	$this->ModelOptionHelper->getDataOptionHelperAreaNonTBA();
		$dataReservationType		=	$this->ModelOptionHelper->getDataOptionHelperReservationType();
		$dataProductType			=	$this->ModelOptionHelper->getDataOptionHelperProductType();
		$dataProductTicket			=	$this->ModelOptionHelper->getDataOptionHelperProductTicket();
		$dataDriverType				=	$this->ModelOptionHelper->getDataOptionHelperDriverType();
		$dataDriver					=	$this->ModelOptionHelper->getDataOptionHelperDriver();
		$dataDriverNewFinance		=	$this->ModelOptionHelper->getDataOptionHelperDriverNewFinanceScheme();
		$dataDriverReview			=	$this->ModelOptionHelper->getDataOptionHelperDriverReview();
		$dataCarType				=	$this->ModelOptionHelper->getDataOptionHelperCarType();
		$dataCarCapacity			=	$this->ModelOptionHelper->getDataOptionHelperCarCapacity();
		$dataCarDayOffType			=	$this->ModelOptionHelper->getDataOptionHelperCarDayOffType();
		$dataCarCostType			=	$this->ModelOptionHelper->getDataOptionHelperCarCostType();
		$dataVendorType				=	$this->ModelOptionHelper->getDataOptionHelperVendorType();
		$dataSource					=	$this->ModelOptionHelper->getDataOptionHelperSource();
		$dataSourceAutoRating		=	$this->ModelOptionHelper->getDataOptionHelperSourceAutoRating();
		$dataSourceAutoPayment		=	$this->ModelOptionHelper->getDataOptionHelperSourceAutoPayment();
		$dataSourceImportOTA		=	$this->ModelOptionHelper->getDataOptionHelperSourceImportOTA();
		$dataSourceOTA				=	$this->ModelOptionHelper->getDataOptionHelperSourceOTA();
		$dataVendor					=	$this->ModelOptionHelper->getDataOptionHelperVendor();
		$dataVendorNewFinance		=	$this->ModelOptionHelper->getDataOptionHelperVendorNewFinanceScheme();
		$dataVendorCar				=	$this->ModelOptionHelper->getDataOptionHelperVendorCar();
		$dataVendorTicket			=	$this->ModelOptionHelper->getDataOptionHelperVendorTicket();
		$dataVendorAndDriver		=	$this->ModelOptionHelper->getDataOptionHelperVendorAndDriver();
		$dataVendorAndDriverActive	=	$this->ModelOptionHelper->getDataOptionHelperVendorAndDriver(1);
		$dataPaymentMethod			=	$this->ModelOptionHelper->getDataOptionHelperPaymentMethod();
		$dataRatingPoint			=	$this->ModelOptionHelper->getDataOptionHelperRatingPoint();
		$dataMessageAdminType		=	$this->ModelOptionHelper->getDataOptionHelperMessageAdminType();
		$dataLoanType				=	$this->ModelOptionHelper->getDataOptionHelperLoanType();
		$dataAdditionalCostType		=	$this->ModelOptionHelper->getDataOptionHelperAdditionalCostType();
		$dataBank					=	$this->ModelOptionHelper->getDataOptionHelperBank();
		$dataPartner				=	$this->ModelOptionHelper->getDataOptionHelperPartner();
		$dataHours					=	$this->getOptionHours();
		$dataMinutes				=	$this->getOptionMinutes();
		$data						=	array(
			"dataUserLevel"				=> $dataUserLevel,
			"dataUserPartnerLevel"		=> $dataUserPartnerLevel,
			"dataArea"					=> $dataArea,
			"dataAreaNonTBA"			=> $dataAreaNonTBA,
			"dataReservationType"		=> $dataReservationType,
			"dataProductType"			=> $dataProductType,
			"dataProductTicket"			=> $dataProductTicket,
			"dataDriverType"			=> $dataDriverType,
			"dataDriver"				=> $dataDriver,
			"dataDriverNewFinance"		=> $dataDriverNewFinance,
			"dataCarType"				=> $dataCarType,
			"dataCarCapacity"			=> $dataCarCapacity,
			"dataCarDayOffType"			=> $dataCarDayOffType,
			"dataCarCostType"			=> $dataCarCostType,
			"dataVendorType"			=> $dataVendorType,
			"dataHours"					=> $dataHours,
			"dataMinutes"				=> $dataMinutes,
			"dataSource"				=> $dataSource,
			"dataSourceAutoRating"		=> $dataSourceAutoRating,
			"dataSourceAutoPayment"		=> $dataSourceAutoPayment,
			"dataSourceImportOTA"		=> $dataSourceImportOTA,
			"dataSourceOTA"				=> $dataSourceOTA,
			"dataVendor"				=> $dataVendor,
			"dataVendorNewFinance"		=> $dataVendorNewFinance,
			"dataDriverReview"			=> $dataDriverReview,
			"dataVendorCar"				=> $dataVendorCar,
			"dataVendorTicket"			=> $dataVendorTicket,
			"dataVendorAndDriver"		=> $dataVendorAndDriver,
			"dataVendorAndDriverActive"	=> $dataVendorAndDriverActive,
			"dataPaymentMethod"			=> $dataPaymentMethod,
			"dataRatingPoint"			=> $dataRatingPoint,
			"dataMessageAdminType"		=> $dataMessageAdminType,
			"dataLoanType"				=> $dataLoanType,
			"dataAdditionalCostType"	=> $dataAdditionalCostType,
			"dataBank"					=> $dataBank,
			"dataPartner"				=> $dataPartner
		);
		setResponseOk(array("data"=>$data));
		
	}
	
	private function getOptionHours(){
		
		$arrReturn	=	array();
		for($i=0; $i<24; $i++){
			$hour			=	str_pad($i, 2, "0", STR_PAD_LEFT);
			$arrReturn[]	=	array("ID"=>$hour, "VALUE"=>$hour);
		}
		
		return $arrReturn;
		
	}
	
	private function getOptionMinutes(){
		
		$arrReturn	=	array();
		for($i=0; $i<60; $i++){
			$minute			=	str_pad($i, 2, "0", STR_PAD_LEFT);
			$arrReturn[]	=	array("ID"=>$minute, "VALUE"=>$minute);
		}
		
		return $arrReturn;
		
	}
	
}