<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class MasterVendor extends CI_controller {
	
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
		$this->load->model('Master/ModelMasterVendor');
		$page			=	validatePostVar($this->postVar, 'page', true);
		$keywordSearch	=	validatePostVar($this->postVar, 'keywordSearch', false);
		$dataTable		=	$this->ModelMasterVendor->getDataVendor(array("keywordSearch" => $keywordSearch), $page);
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	}
	
	public function insertData(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterVendor');
		
		$idVendorType				=	$this->postVar['optionVendorType'];
		$transportService			=	validatePostVar($this->postVar, 'optionTransportService', false);
		$vendorName					=	validatePostVar($this->postVar, 'vendorName', true);
		$address					=	validatePostVar($this->postVar, 'address', false);
		$phone						=	numberValidator(removeNonNumericValue(validatePostVar($this->postVar, 'vendorPhone', false)));
		$vendorEmail				=	validatePostVar($this->postVar, 'vendorEmail', true);
		$cekMailPattern				=	checkMailPattern($vendorEmail);
		$autoReduceCollectPayment	=	validatePostVar($this->postVar, 'autoReduceCollectPayment', false);
		$autoReduceCollectPayment	=	!isset($autoReduceCollectPayment) || $autoReduceCollectPayment == '' ? 0 : $autoReduceCollectPayment;
		$msg						=	"";
		
		if($phone == "+62") setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please enter a valid phone number"));
		if(!$cekMailPattern) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please enter a valid email"));

		$checkMailDriverVendor	=	$this->MainOperation->checkMailDriverVendor($vendorEmail);
		if($checkMailDriverVendor) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Email address already exist. Please enter another email address"));

		$checkDataExists	=	$this->ModelMasterVendor->checkDataExists($vendorName, $phone, $vendorEmail);
		$arrInsertUpdate	=	array(
			"IDVENDORTYPE"				=>	$idVendorType,
			"NAME"						=>	$vendorName,
			"ADDRESS"					=>	$address,
			"PHONE"						=>	$phone,
			"EMAIL"						=>	$vendorEmail,
			"TRANSPORTSERVICE"			=>	$transportService,
			"AUTOREDUCECOLLECTPAYMENT"	=>	$autoReduceCollectPayment,
			"STATUS"					=>	1
	   );

		if($checkDataExists){
			$msg		=	"Vendor data with the <b>".$checkDataExists[0]." : ".$checkDataExists[1]."</b> already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}

		$insertResult	=	$this->MainOperation->addData("m_vendor", $arrInsertUpdate);

		if(!$insertResult['status']) switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
		$idInsert		=	$insertResult['insertID'];
		$optionHelper	=	$this->getArrDataOptionHelper();
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New vendor data saved", "idInsert"=>$idInsert, "optionHelper"=>$optionHelper));
	}
	
	public function detailData(){
		$this->load->model('Master/ModelMasterVendor');
		$idData			=	validatePostVar($this->postVar, 'idData', true);
		$dataDetail		=	$this->ModelMasterVendor->getDataVendorById($idData);
		
		setResponseOk(array("token"=>$this->newToken, "data"=>$dataDetail));
	}
	
	public function updateData(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterVendor');
		
		$idVendor					=	validatePostVar($this->postVar, 'idData', true);
		$transportService			=	validatePostVar($this->postVar, 'optionTransportService', false);
		$idVendorType				=	$this->postVar['optionVendorType'];
		$vendorName					=	validatePostVar($this->postVar, 'vendorName', true);
		$address					=	validatePostVar($this->postVar, 'address', false);
		$phone						=	numberValidator(removeNonNumericValue(validatePostVar($this->postVar, 'vendorPhone', false)));
		$vendorEmail				=	validatePostVar($this->postVar, 'vendorEmail', true);
		$cekMailPattern				=	checkMailPattern($vendorEmail);
		$autoReduceCollectPayment	=	validatePostVar($this->postVar, 'autoReduceCollectPayment', false);
		$autoReduceCollectPayment	=	!isset($autoReduceCollectPayment) || $autoReduceCollectPayment == '' ? 0 : $autoReduceCollectPayment;
		$msg						=	"";
		
		if($phone == "+62") setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please enter a valid phone number"));
		if(!$cekMailPattern) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please enter a valid email"));

		$checkMailDriverVendor	=	$this->MainOperation->checkMailDriverVendor($vendorEmail, $idVendor);
		if($checkMailDriverVendor) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Email address already exist. Please enter another email address"));

		$checkDataExists	=	$this->ModelMasterVendor->checkDataExists($vendorName, $phone, $vendorEmail, $idVendor);
		$arrUpdate			=	array(
			"IDVENDORTYPE"				=>	$idVendorType,
			"NAME"						=>	$vendorName,
			"ADDRESS"					=>	$address,
			"PHONE"						=>	$phone,
			"EMAIL"						=>	$vendorEmail,
			"TRANSPORTSERVICE"			=>	$transportService,
			"AUTOREDUCECOLLECTPAYMENT"	=>	$autoReduceCollectPayment,
			"STATUS"					=>	1
		);

		if($checkDataExists){
			$msg		=	"Vendor data with the <b>".$checkDataExists[0]." : ".$checkDataExists[1]."</b> already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}
		
		$updateResult	=	$this->MainOperation->updateData("m_vendor", $arrUpdate, "IDVENDOR", $idVendor);
		
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Vendor data has been updated"));	
	}
	
	private function checkInputData(){
		$arrVarValidate	=	array(
			array("vendorName","text","Vendor Name"),
			array("vendorPhone","text","Phone"),
			array("vendorEmail","text","Email"),
			array("optionVendorType","option","Partner Type")
		);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;		
	}
	
	public function updateStatus(){
		$this->load->model('MainOperation');
		$data			=	array();
		
		$idVendor		=	validatePostVar($this->postVar, 'idData', true);
		$status			=	validatePostVar($this->postVar, 'status', true);
		
		if($status == 1){
			$arrUpdate		=	array("STATUS" => 1);
			$strStatus		=	"reactivated";
		} else {
			$arrUpdate		=	array("STATUS"	=> -1);
			$strStatus		=	"deactivated";
		}
		$updateResult	=	$this->MainOperation->updateData("m_vendor", $arrUpdate, "IDVENDOR", $idVendor);
		
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Vendor status has been ".$strStatus));	
	}
	
	private function getArrDataOptionHelper(){
		$this->load->model('ModelOptionHelper');
		$optionHelperC	=	$this->ModelOptionHelper->getDataOptionHelperVendorCar();
		$optionHelperT	=	$this->ModelOptionHelper->getDataOptionHelperVendorTicket();
		
		return array($optionHelperC, $optionHelperT);
	}

	public function resetVendorSecretPin(){
		$this->load->model('MainOperation');
		$idVendor		=	validatePostVar($this->postVar, 'idPartner', true);
		$arrUpdatePin	=	array(
			"SECRETPIN"				=>	DEFAULT_VENDOR_PIN,
			"SECRETPINSTATUS"		=>	1,
			"SECRETPINLASTUPDATE"	=>	date('Y-m-d H:i:s')
		);
		$updateResult	=	$this->MainOperation->updateData("m_vendor", $arrUpdatePin, "IDVENDOR", $idVendor);
		
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Vendor secret PIN has been reset"));	
	}
	
	public function setPartnerNewFinanceScheme(){
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterVendor');

		$idVendor				=	validatePostVar($this->postVar, 'idPartner', true);
		$financeSchemeType		=	validatePostVar($this->postVar, 'financeSchemeType', true);
		$lastDepositBalance		=	intval(validatePostVar($this->postVar, 'lastDepositBalance', false));
		$lastScheduleWithdrawal	=	validatePostVar($this->postVar, 'lastScheduleWithdrawal', true);
		$lastScheduleWithdrawal	=	DateTime::createFromFormat('d-m-Y', $lastScheduleWithdrawal);
		$lastScheduleWithdrawal	=	$lastScheduleWithdrawal->format('Y-m-d');
		$arrUpdateScheme		=	array(
			"FINANCESCHEMETYPE"		=>	$financeSchemeType,
			"NEWFINANCESCHEME"		=>	1,
			"NEWFINANCESCHEMESTART"	=>	date('Y-m-d H:i:s')
		);
		$updateResult			=	$this->MainOperation->updateData("m_vendor", $arrUpdateScheme, "IDVENDOR", $idVendor);
		
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);

		$dataInsertSchedule		=	$this->ModelMasterVendor->getDataScheduleVendor($idVendor, $lastScheduleWithdrawal);
		if($dataInsertSchedule){
			foreach($dataInsertSchedule as $keyInsertSchedule){
				$idReservationDetails	=	$keyInsertSchedule->IDRESERVATIONDETAILS;
				$arrInsertSchedule		=	array(
					"IDRESERVATIONDETAILS"	=>	$idReservationDetails,
					"IDVENDOR"				=>	$idVendor,
					"USERINPUT"				=>	'Auto System',
					"DATETIMEINPUT"			=>	date('Y-m-d H:i:s'),
					"STATUSPROCESS"			=>	0,
					"STATUS"				=>	1
				);
				$this->MainOperation->addData("t_schedulevendor", $arrInsertSchedule);
			}
		}
		
		$dataCollectPaymentVendor	=	$this->ModelMasterVendor->getDataCollectPaymentVendor($idVendor, $lastScheduleWithdrawal);
		if($dataCollectPaymentVendor){
			foreach($dataCollectPaymentVendor as $keyCollectPaymentVendor){
				$idCollectPayment	=	$keyCollectPaymentVendor->IDCOLLECTPAYMENT;
				$arrUpdateCollect	=	array(
					"STATUS"					=>	0,
					"STATUSSETTLEMENTREQUEST"	=>	0
				);
				$this->MainOperation->updateData("t_collectpayment", $arrUpdateCollect, "IDCOLLECTPAYMENT", $idCollectPayment);
			}
		}
		
		if($financeSchemeType == 2 && isset($lastDepositBalance) && $lastDepositBalance > 0){
			$arrInsertDepositRecord	=	array(
				"IDVENDOR"			=>	$idVendor,
				"DESCRIPTION"		=>	"Saldo awal deposit Bali SUN Tours",
				"AMOUNT"			=>	$lastDepositBalance,
				"TRANSFERRECEIPT"	=>	"",
				"USERINPUT"			=>	"Super Admin",
				"DATETIMEINPUT"		=>	date('Y-m-d H:i:s')
			);
			$this->MainOperation->addData("t_depositvendorrecord", $arrInsertDepositRecord);
		}

		setResponseOk(array("token"=>$this->newToken, "msg"=>"Vendor new finance scheme has been set"));
	}

	public function updateLastWithdrawVendor(){
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterVendor');

		$idVendor			=	validatePostVar($this->postVar, 'idVendor', true);
		$lastDateWithdrawal	=	validatePostVar($this->postVar, 'lastDateWithdrawal', true);
		$lastDateWithdrawal	=	DateTime::createFromFormat('d-m-Y', $lastDateWithdrawal);
		$lastDateWithdrawal	=	$lastDateWithdrawal->format('Y-m-d');
		$dataDetailVendor	=	$this->ModelMasterVendor->getDataVendorById($idVendor);
		$idVendorCheck		=	$dataDetailVendor['IDDATA'];
		$newFinanceScheme	=	$dataDetailVendor['NEWFINANCESCHEME'];
		$financeSchemeType	=	$dataDetailVendor['FINANCESCHEMETYPE'];
		
		if($idVendorCheck == "" || $idVendorCheck == 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data. Please clear app data and try again"));
		if($newFinanceScheme == 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"The new finance scheme has not yet been implemented for the selected vendor."));
		if($financeSchemeType != 1) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"The process for updating the <b>Last Scheduled Withdrawal</b> data is only applicable to vendors with a withdrawal scheme."));

		$dataReservationVendor	=	$this->ModelMasterVendor->getDataLastReservationVendor($idVendor, $lastDateWithdrawal);
		
		if($dataReservationVendor){
			foreach($dataReservationVendor as $keyReservationVendor){
				$idScheduleVendor			=	$keyReservationVendor->IDSCHEDULEVENDOR;
				$idFee						=	$keyReservationVendor->IDFEE;
				$idWithdrawalRecapFee		=	$keyReservationVendor->IDWITHDRAWALRECAPFEE;
				$idCollectPayment			=	$keyReservationVendor->IDCOLLECTPAYMENT;
				$idWithdrawalCollectPayment	=	$keyReservationVendor->IDWITHDRAWALRECAPCOLLECTPAYMENT;
				
				if($idWithdrawalCollectPayment == 0){
					$arrUpdateCollectPayment	=	[
						'STATUS'					=>	1,
						'STATUSSETTLEMENTREQUEST'	=>	2,
					];
					$this->MainOperation->updateData('t_collectpayment', $arrUpdateCollectPayment, 'IDCOLLECTPAYMENT', $idCollectPayment);
				}
				
				if($idWithdrawalRecapFee == 0){
					$this->MainOperation->deleteData('t_fee', ['IDFEE' => $idFee]);
					$this->MainOperation->deleteData("t_schedulevendor", array("IDSCHEDULEVENDOR" => $idScheduleVendor));
				}
			}
			
		}
		
		$this->updatePartnerRTDBStatisticVendorReservation($idVendor);
		$this->updatePartnerRTDBStatisticVendorCollectPayment($idVendor);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Last withdraw date has been updated"));
	}
	
	private function updatePartnerRTDBStatisticVendorReservation($idVendor){
		if(PRODUCTION_URL){
			$this->load->library('fcm');
			$this->load->model('MainOperation');
			$detailVendor			=	$this->MainOperation->getDataVendor($idVendor);
			$RTDB_refCode			=	$detailVendor['RTDBREFCODE'];
			if($RTDB_refCode && $RTDB_refCode != ''){
				try {
					$factory			=	(new Factory)
											->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
											->withDatabaseUri(FIREBASE_RTDB_URI);
					$database			=	$factory->createDatabase();
					$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/vendor/".$RTDB_refCode."/unconfirmedReservation");
					$referencePartnerVal=	$referencePartner->getValue();
					if($referencePartnerVal != null || !is_null($referencePartnerVal)){
						$referencePartner->update([
							'cancelReservationStatus'		=>  false,
							'newReservationStatus'          =>  false,
							'timestampUpdate'               =>  gmdate('YmdHis'),
							'totalUnconfirmedReservation'   =>  $this->MainOperation->getTotalUnconfirmedReservationPartner(1, $idVendor)
						]);
					}
				} catch (Exception $e) {
				}
			}
		}
		return true;
	}
		
	private function updatePartnerRTDBStatisticVendorCollectPayment($idVendor){
		if(PRODUCTION_URL){
			$this->load->model('MainOperation');
			$detailVendor	=	$this->MainOperation->getDataVendor($idVendor);
			$RTDB_refCode	=	$detailVendor['RTDBREFCODE'];
			if($RTDB_refCode && $RTDB_refCode != ''){
				try {
					$factory			=	(new Factory)
											->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
											->withDatabaseUri(FIREBASE_RTDB_URI);
					$database			=	$factory->createDatabase();
					$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/vendor/".$RTDB_refCode."/activeCollectPayment");
					$referencePartnerVal=	$referencePartner->getValue();
					if($referencePartnerVal != null || !is_null($referencePartnerVal)){
						$referencePartner->update([
							'newCollectPaymentDetail'	=>  '',
							'newCollectPaymentStatus'	=>  false,
							'timestampUpdate'			=>  gmdate('YmdHis'),
							'totalActiveCollectPayment'	=>  $this->MainOperation->getTotalActiveCollectPayment(1, $idVendor)
						]);
					}
				} catch (Exception $e) {
				}
			}
		}
		return true;
	}
}