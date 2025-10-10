<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class View extends CI_controller {
	
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
	
	public function dashboard(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$data			=	array("thisMonth"=>$thisMonth);
		$htmlRes		=	$this->load->view('page/dashboard', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function notification(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/notification', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function userProfileSetting(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/userProfileSetting', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function dataMaster(){
		$this->load->helper('url');
		$this->load->model('MainOperation');
		
		$levelUser			=	$this->MainOperation->getLevelUser($this->newToken);
		$viewMasterSource	=	$this->load->view('page/Master/dataMasterSource', array(), TRUE);
		$viewMasterProduct	=	$this->load->view('page/Master/dataMasterProduct', array(), TRUE);
		$viewMasterDriver	=	$this->load->view('page/Master/dataMasterDriver', array(), TRUE);
		$viewMasterVendor	=	$this->load->view('page/Master/dataMasterVendor', array(), TRUE);
		$viewMasterCarType	=	$this->load->view('page/Master/dataMasterCarType', array(), TRUE);
		$data				=	array(
									"levelUser"			=>	$levelUser,
									"viewMasterSource"	=>	$viewMasterSource,
									"viewMasterProduct"=>	$viewMasterProduct,
									"viewMasterDriver"	=>	$viewMasterDriver,
									"viewMasterVendor"	=>	$viewMasterVendor,
									"viewMasterCarType"	=>	$viewMasterCarType
								);
		$htmlRes			=	$this->load->view('page/dataMaster', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function selfDriveSetting(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/ProductSetting/selfDriveSetting', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function ticketSetting(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/ProductSetting/ticketSetting', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function transportSetting(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/ProductSetting/transportSetting', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function templateAutoCost(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/ProductSetting/templateAutoCost', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function mailbox(){
		$this->load->helper('url');
		$this->load->model('MainOperation');

		$dataUserAdmin	=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$data			=	array("idReservationType" => $dataUserAdmin['IDRESERVATIONTYPE']);
		$htmlRes		=	$this->load->view('page/mailbox', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function reservation(){
		$this->load->helper('url');
		$this->load->model('MainOperation');

		$dataUserAdmin					=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$dataIdPaymentMethodUpselling	=	$this->MainOperation->getStrArrIdPaymentMethodAllowUpselling();
		$dateSetting					=	$this->MainOperation->getDataAutoScheduleSetting("5,6");
		$dateToday						=	date('d-m-Y');
		$dateTomorrow					=	date('d-m-Y', strtotime("+1 day"));
		$minHourDateFilterTomorrow		=	$maxHourDateFilterToday	=	0;

		if($dateSetting){
			foreach($dateSetting as $keyDateSetting){
				$idSystemSettingVariable=	$keyDateSetting->IDSYSTEMSETTINGVARIABLE;
				$value					=	$keyDateSetting->VALUE;
				
				switch($idSystemSettingVariable){
					case "5"	:	
					case 5		:	$minHourDateFilterTomorrow	=	$value; break;
					case "6"	:	
					case 6		:	$maxHourDateFilterToday		=	$value; break;
					default		:	break;
				}
			}
		}
		
		$data	=	array(
			"idReservationType"				=>	$dataUserAdmin['IDRESERVATIONTYPE'],
			"dataIdPaymentMethodUpselling"	=>	$dataIdPaymentMethodUpselling,
			"dateToday"						=>	$dateToday,
			"dateTomorrow"					=>	$dateTomorrow,
			"minHourDateFilterTomorrow"		=>	$minHourDateFilterTomorrow,
			"maxHourDateFilterToday"		=>	$maxHourDateFilterToday
		);
		$htmlRes=	$this->load->view('page/reservation', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function importDataOTA(){
		$this->load->helper('url');
		$data	=	array();
		$htmlRes=	$this->load->view('page/importDataOTA', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function reConfirmation(){
		$this->load->helper('url');
		$this->load->model('MainOperation');
		
		$settingDayReconfirm=	$this->MainOperation->getValueSystemSettingVariable(12);
		$defaultDateFilter	=	date('d-m-Y', strtotime("+".$settingDayReconfirm." days"));
		$thisMonth			=	date('m');
		$data				=	array("thisMonth"=>$thisMonth, "defaultDateFilter"=>$defaultDateFilter);
		$htmlRes			=	$this->load->view('page/reConfirmation', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function scheduleDriver(){
		$this->load->helper('url');
		$this->load->model('MainOperation');

		$dateSetting				=	$this->MainOperation->getDataAutoScheduleSetting("7");
		$dataUserAdmin				=	$this->MainOperation->getDataUserAdmin($this->newToken);		
		$dateToday					=	date('d-m-Y');
		$dateTomorrow				=	date('d-m-Y', strtotime("+1 day"));
		$minHourDateFilterTomorrow	=	0;
		
		if($dateSetting) $minHourDateFilterTomorrow	=	$dateSetting[0]->VALUE;

		$data			=	array(
								"dateToday"					=>	$dateToday,
								"dateTomorrow"				=>	$dateTomorrow,
								"minHourDateFilterTomorrow"	=>	$minHourDateFilterTomorrow,
								"pmsAddDriverSchedule"		=>	$dataUserAdmin['PMSADDDRIVERSCHEDULE'],
								"pmsDeleteDriverSchedule"	=>	$dataUserAdmin['PMSDELETEDRIVERSCHEDULE']
							);
		$htmlRes		=	$this->load->view('page/Schedule/scheduleDriver', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function scheduleDriverRatingPoint(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$data			=	array("thisMonth"=>$thisMonth);
		$htmlRes		=	$this->load->view('page/Schedule/scheduleDriverRatingPoint', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function scheduleDriverAuto(){
		$this->load->helper('url');
		$this->load->model('MainOperation');

		$dateSetting				=	$this->MainOperation->getDataAutoScheduleSetting("7");		
		$dateToday					=	date('d-m-Y');
		$dateTomorrow				=	date('d-m-Y', strtotime("+1 day"));
		$minHourDateFilterTomorrow	=	0;
		
		if($dateSetting){
			$minHourDateFilterTomorrow	=	$dateSetting[0]->VALUE;
		}
		
		$data			=	array(
								"dateToday"					=>	$dateToday,
								"dateTomorrow"				=>	$dateTomorrow,
								"minHourDateFilterTomorrow"	=>	$minHourDateFilterTomorrow
							);
		$htmlRes		=	$this->load->view('page/Schedule/scheduleDriverAuto', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function scheduleDriverMonitor(){
		$this->load->helper('url');
		$this->load->model('MainOperation');

		$dateToday		=	date('d-m-Y');
		$dateTomorrow	=	date('d-m-Y', strtotime("+1 day"));
		$thisMonth		=	date('m');
		$data			=	array(
								"dateToday"		=>	$dateToday,
								"dateTomorrow"	=>	$dateTomorrow,
								"thisMonth"		=>	$thisMonth
							);
		$htmlRes		=	$this->load->view('page/Schedule/scheduleDriverMonitor', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function scheduleVendor(){
		$this->load->helper('url');

		$data			=	array();
		$htmlRes		=	$this->load->view('page/Schedule/scheduleVendor', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function scheduleCar(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$dateToday		=	new DateTime(); 
		$dateToday->modify('+7 days');
		$next7DayDate	=	$dateToday->format('d-m-Y');
		$data			=	array("thisMonth"=>$thisMonth, "next7DayDate"=>$next7DayDate);
		$htmlRes		=	$this->load->view('page/Schedule/scheduleCar', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function financeDetailReservationPayment(){
		$this->load->helper('url');
		$this->load->model('MainOperation');

		$dataIdPaymentMethodUpselling	=	$this->MainOperation->getStrArrIdPaymentMethodAllowUpselling();
		$thisMonth						=	date('m');
		$data							=	array(
			"dataIdPaymentMethodUpselling"	=>	$dataIdPaymentMethodUpselling,
			"thisMonth"						=>	$thisMonth
		);
		$htmlRes						=	$this->load->view('page/Finance/detailReservationPayment', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeDetailReservationIncome(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$data			=	array("thisMonth"=>$thisMonth);
		$htmlRes		=	$this->load->view('page/Finance/detailReservationIncome', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeReservationInvoice(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$data			=	array("thisMonth"=>$thisMonth);
		$htmlRes		=	$this->load->view('page/Finance/reservationInvoice', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeReimbursement(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/Finance/reimbursement', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function financeRecapFeeProduct(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/Finance/recapFeeProduct', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function financeCharityReport(){
		$this->load->helper('url');
		
		$data			=	array("minCharityNominal"=>MIN_CHARITY_NOMINAL);
		$htmlRes		=	$this->load->view('page/Finance/charityReport', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function financeTransferList(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/Finance/transferList', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function financeCurrencyExchange(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$data			=	array("thisMonth"=>$thisMonth);
		$htmlRes		=	$this->load->view('page/Finance/currencyExchange', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeDriverRecapPerDriver(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$levelUser		=	$this->MainOperation->getLevelUser($this->newToken);
		$data			=	array("thisMonth"=>$thisMonth, "levelUser"=>$levelUser);
		$htmlRes		=	$this->load->view('page/FinanceDriver/recapPerDriver', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeDriverCostFee(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$data			=	array("thisMonth"=>$thisMonth);
		$htmlRes		=	$this->load->view('page/FinanceDriver/detailCostFee', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeDriverAdditionalCost(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$data			=	array("thisMonth"=>$thisMonth);
		$htmlRes		=	$this->load->view('page/FinanceDriver/additionalCost', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeDriverCollectPayment(){
		$this->load->helper('url');
		
		$data			=	array("defaultImage"=>URL_COLLECT_PAYMENT_RECEIPT."noimage.jpg");
		$htmlRes		=	$this->load->view('page/FinanceDriver/collectPayment', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeDriverLoanPrepaidCapital(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/FinanceDriver/loanPrepaidCapital', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeDriverReviewBonusPunishment(){
		$this->load->helper('url');
		
		$thisMonth	=	date('m');
		$data		=	array("thisMonth"=>$thisMonth);
		$htmlRes	=	$this->load->view('page/FinanceDriver/reviewBonusPunishment', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function financeDriverAdditionalIncome(){
		$this->load->helper('url');
		
		$thisMonth	=	date('m');
		$data		=	array("thisMonth"=>$thisMonth);
		$htmlRes	=	$this->load->view('page/FinanceDriver/additionalIncome', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeVendorRecapPerVendor(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$levelUser		=	$this->MainOperation->getLevelUser($this->newToken);
		$data			=	array("thisMonth"=>$thisMonth, "levelUser"=>$levelUser);
		$htmlRes		=	$this->load->view('page/FinanceVendor/recapPerVendor', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeVendorDetailFeeVendor(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$data			=	array("thisMonth"=>$thisMonth);
		$htmlRes		=	$this->load->view('page/FinanceVendor/detailFeeVendor', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeVendorCollectPayment(){
		$this->load->helper('url');
		
		$data			=	array("defaultImage"=>URL_COLLECT_PAYMENT_RECEIPT."noimage.jpg");
		$htmlRes		=	$this->load->view('page/FinanceVendor/collectPayment', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function financeVendorCarRentalFeeCost(){
		$this->load->helper('url');
		
		$thisMonth	=	date('m');
		$data		=	array("thisMonth"=>$thisMonth);
		$htmlRes	=	$this->load->view('page/FinanceVendor/carRentalFeeCost', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
		
	public function reportAgentPaymentBalance(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/Report/agentPaymentBalance', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function reportReservationDetail(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$data			=	array("thisMonth"=>$thisMonth);
		$htmlRes		=	$this->load->view('page/Report/reservationDetail', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function reportRecapPerProduct(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$data			=	array("thisMonth"=>$thisMonth);
		$htmlRes		=	$this->load->view('page/Report/recapPerProduct', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function reportRecapPerDate(){
		$this->load->helper('url');
		
		$thisMonth		=	date('m');
		$data			=	array("thisMonth"=>$thisMonth);
		$htmlRes		=	$this->load->view('page/Report/recapPerDate', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function settingUserLevel(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/Setting/userLevel', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function settingUserLevelMenu(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/Setting/userLevelMenu', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function settingUserAdmin(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/Setting/userAdmin', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function settingSystem(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/Setting/systemSeting', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function settingHelpCenter(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/Setting/helpCenter', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function settingPartnerUserLevel(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/SettingPartner/userLevel', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function settingPartnerUserLevelMenu(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/SettingPartner/userPartnerLevelMenu', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function settingUserPartner(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/SettingPartner/userPartner', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	public function knowledge(){
		$this->load->helper('url');
		
		$data			=	array();
		$htmlRes		=	$this->load->view('page/knowledge', $data, TRUE);
		
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
}