<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterDriver extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	var $subtract30Days;
	
	public function __construct(){
        parent::__construct();
		$this->postVar			=	decodeJsonPost();
		$this->token			=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid request"));
		$this->newToken			=	isLoggedIn($this->token, true);
		$this->subtract30Days	=	date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataTable(){
		$this->load->model('Master/ModelMasterDriver');
		$page			=	validatePostVar($this->postVar, 'page', true);
		$keywordSearch	=	validatePostVar($this->postVar, 'keywordSearch', false);
		$dataTable		=	$this->ModelMasterDriver->getDataDriver(array("keywordSearch" => $keywordSearch), $page);
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	}
	
	public function insertData(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterDriver');
		
		$idDriverType			=	validatePostVar($this->postVar, 'optionDriverType', true);
		$idCarCapacity			=	validatePostVar($this->postVar, 'optionCarCapacity', true);
		$partnershipType		=	validatePostVar($this->postVar, 'optionPartnershipType', true);
		$scheduleType			=	validatePostVar($this->postVar, 'optionScheduleType', true);
		$driverName				=	validatePostVar($this->postVar, 'driverName', true);
		$driverNameFull			=	validatePostVar($this->postVar, 'driverNameFull', true);
		$address				=	validatePostVar($this->postVar, 'address', false);
		$driverQuota			=	removeNonNumericValue(validatePostVar($this->postVar, 'driverQuota', true));
		$phone					=	numberValidator(removeNonNumericValue(validatePostVar($this->postVar, 'phone', false)));
		$driverEmail			=	validatePostVar($this->postVar, 'driverEmail', true);
		$reviewBonusPunishment	=	validatePostVar($this->postVar, 'checkboxReviewBonusPunishment', false);
		$password				=	validatePostVar($this->postVar, 'password', true);
		$passwordMd5			=	md5($password);
		$carNumberPlate			=	$partnershipType == 3 ? "-" : validatePostVar($this->postVar, 'carNumberPlate', true);
		$carBrand				=	$partnershipType == 3 ? "-" : validatePostVar($this->postVar, 'carBrand', true);
		$carModel				=	$partnershipType == 3 ? "-" : validatePostVar($this->postVar, 'carModel', true);
		$cekMailPattern			=	checkMailPattern($driverEmail);
		$msg					=	"";
		
		if($phone == "+62") setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please enter a valid phone number"));
		if(!$cekMailPattern) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please enter a valid email"));
		
		$checkMailDriverVendor	=	$this->MainOperation->checkMailDriverVendor($driverEmail);
		if($checkMailDriverVendor) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Email address already exist. Please enter another email address"));

		$checkDataExists=	$this->ModelMasterDriver->checkDataExists($driverName, $phone, $driverEmail);
		$arrInsertUpdate=	array(
			"IDDRIVERTYPE"			=>	$idDriverType,
			"IDCARCAPACITY"			=>	$idCarCapacity,
			"SCHEDULETYPE"			=>	$scheduleType,
			"PARTNERSHIPTYPE"		=>	$partnershipType,
			"NAME"					=>	$driverName,
			"NAMEFULL"				=>	$driverNameFull,
			"ADDRESS"				=>	$address,
			"DRIVERQUOTA"			=>	$driverQuota,
			"PHONE"					=>	$phone,
			"EMAIL"					=>	$driverEmail,
			"CARNUMBERPLATE"		=>	$carNumberPlate,
			"CARBRAND"				=>	$carBrand,
			"CARMODEL"				=>	$carModel,
			"PASSWORD"				=>	$passwordMd5,
			"PASSWORDPLAIN"			=>	$password,
			"REVIEWBONUSPUNISHMENT"	=>	$reviewBonusPunishment,
			"NEWFINANCESCHEME"		=>	1,
			"NEWFINANCESCHEMESTART"	=>	date('Y-m-d H:i:s'),
			"STATUS"				=>	1
		);

		if($checkDataExists){
			$msg		=	"Driver data with the <b>".$checkDataExists[0]." : ".$checkDataExists[1]."</b> already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}

		$insertResult	=	$this->MainOperation->addData("m_driver", $arrInsertUpdate);
		if(!$insertResult['status']) switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
		
		$this->setPointRankDriver();
		$this->updateDriverScheduleMonitor();
		$idInsert		=	$insertResult['insertID'];
		$optionHelper	=	$this->getArrDataOptionHelper();
		
		if($partnershipType == 2){
			for($i=1; $i<=7; $i++){
				$dateOff			=	date('Y-m-d', strtotime("+".$i." days"));
				$arrInsertDayOff	=	array(
											"IDDRIVER"		=>	$idInsert,
											"DATEDAYOFF"	=>	$dateOff,
											"REASON"		=>	'Default Off',
											"DATETIMEINPUT"	=>	date('Y-m-d H:i:s')
										);
				$procInsertDayOff	=	$this->MainOperation->addData("t_dayoff", $arrInsertDayOff);
			}
		}
			
		$arrData				=	[];
		$base64JsonData			=	base64_encode(json_encode($arrData));
		$urlAPICalculateReview	=	BASE_URL."schedule/driverRatingPoint/apiCalculateBonusPunishmentReview/".$base64JsonData;
		
		try {
			$resAPICronCalculateReview	=	json_decode(trim(curl_get_file_contents($urlAPICalculateReview)));
		} catch(Exception $e) {
		}
		
		$dataLoanType	=	$this->ModelMasterDriver->getDataLoanType();
		if($dataLoanType){
			foreach($dataLoanType as $keyLoanType){
				$idLoanType					=	$keyLoanType->IDLOANTYPE;
				$statusPermission			=	$keyLoanType->STATUSPERMISSION;
				$arrInsertLoanPermission	=	[
					"IDDRIVER"			=>	$idInsert,
					"IDLOANTYPE"		=>	$idLoanType,
					"STATUSPERMISSION"	=>	$statusPermission
				];
				$this->MainOperation->addData('t_driverloanpermission', $arrInsertLoanPermission);
			}
		}
		
		$dataLastAgreement	=	$this->ModelMasterDriver->getDataLastAgreementDriver();
		if($dataLastAgreement){
			$idDriverAgreementMaster=	$dataLastAgreement['IDDRIVERAGREEMENTMASTER'];
			$arrInsertAgreement		=	[
				"IDDRIVERAGREEMENTMASTER"	=>	$idDriverAgreementMaster,
				"IDDRIVER"					=>	$idInsert,
				"DATESIGNATURE"				=>	'0000-00-00 00:00:00',
				"APPROVALDATETIME"			=>	'0000-00-00 00:00:00',
				"APPROVALSTATUS"			=>	0
			];
			$this->MainOperation->addData('t_driveragreement', $arrInsertAgreement);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New driver data saved", "idInsert"=>$idInsert, "optionHelper"=>$optionHelper));
	}
	
	public function detailData(){
		$this->load->model('Master/ModelMasterDriver');
		$idData			=	validatePostVar($this->postVar, 'idData', true);
		$dataDetail		=	$this->ModelMasterDriver->getDataDriverById($idData);
		
		setResponseOk(array("token"=>$this->newToken, "data"=>$dataDetail));
	}
	
	public function updateData(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterDriver');
		
		$idDriver				=	validatePostVar($this->postVar, 'idData', true);
		$idDriverType			=	validatePostVar($this->postVar, 'optionDriverType', true);
		$idCarCapacity			=	validatePostVar($this->postVar, 'optionCarCapacity', true);
		$scheduleType			=	validatePostVar($this->postVar, 'optionScheduleType', true);
		$partnershipType		=	validatePostVar($this->postVar, 'optionPartnershipType', true);
		$driverName				=	validatePostVar($this->postVar, 'driverName', true);
		$driverNameFull			=	validatePostVar($this->postVar, 'driverNameFull', true);
		$address				=	validatePostVar($this->postVar, 'address', false);
		$driverQuota			=	removeNonNumericValue(validatePostVar($this->postVar, 'driverQuota', true));
		$phone					=	numberValidator(removeNonNumericValue(validatePostVar($this->postVar, 'phone', false)));
		$driverEmail			=	validatePostVar($this->postVar, 'driverEmail', true);
		$reviewBonusPunishment	=	validatePostVar($this->postVar, 'checkboxReviewBonusPunishment', false);
		$password				=	validatePostVar($this->postVar, 'password', true);
		$passwordMd5			=	md5($password);
		$carNumberPlate			=	$partnershipType == 3 ? "-" : validatePostVar($this->postVar, 'carNumberPlate', true);
		$carBrand				=	$partnershipType == 3 ? "-" : validatePostVar($this->postVar, 'carBrand', true);
		$carModel				=	$partnershipType == 3 ? "-" : validatePostVar($this->postVar, 'carModel', true);
		$cekMailPattern			=	checkMailPattern($driverEmail);
		$msg					=	"";
		
		if($phone == "+62") setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please enter a valid phone number"));		
		if(!$cekMailPattern) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please enter a valid email"));
		
		$checkMailDriverVendor	=	$this->MainOperation->checkMailDriverVendor($driverEmail, $idDriver);
		if($checkMailDriverVendor) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Email address already exist. Please enter another email address"));
		
		$checkDataExists=	$this->ModelMasterDriver->checkDataExists($driverName, $phone, $driverEmail, $idDriver);
		$arrUpdate		=	array(
			"IDDRIVERTYPE"			=>	$idDriverType,
			"IDCARCAPACITY"			=>	$idCarCapacity,
			"SCHEDULETYPE"			=>	$scheduleType,
			"PARTNERSHIPTYPE"		=>	$partnershipType,
			"NAME"					=>	$driverName,
			"NAMEFULL"				=>	$driverNameFull,
			"ADDRESS"				=>	$address,
			"DRIVERQUOTA"			=>	$driverQuota,
			"PHONE"					=>	$phone,
			"EMAIL"					=>	$driverEmail,
			"CARNUMBERPLATE"		=>	$carNumberPlate,
			"CARBRAND"				=>	$carBrand,
			"CARMODEL"				=>	$carModel,
			"PASSWORD"				=>	$passwordMd5,
			"PASSWORDPLAIN"			=>	$password,
			"REVIEWBONUSPUNISHMENT"	=>	$reviewBonusPunishment
	   );

		if($checkDataExists){
			$msg		=	"Driver data with the <b>".$checkDataExists[0]." : ".$checkDataExists[1]."</b> already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}
		
		$updateResult	=	$this->MainOperation->updateData("m_driver", $arrUpdate, "IDDRIVER", $idDriver);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		if($partnershipType == 2){
			for($i=1; $i<=7; $i++){
				$dateOff			=	date('Y-m-d', strtotime("+".$i." days"));
				$arrInsertDayOff	=	array(
											"IDDRIVER"		=>	$idDriver,
											"DATEDAYOFF"	=>	$dateOff,
											"REASON"		=>	'Default Off',
											"DATETIMEINPUT"	=>	date('Y-m-d H:i:s')
										);
				$procInsertDayOff	=	$this->MainOperation->addData("t_dayoff", $arrInsertDayOff);
			}
		} else {
		    $dataOffDriverFreelance =   $this->ModelMasterDriver->getDataOffDriverFreelance($idDriver);
		    if($dataOffDriverFreelance){
                $strArrIdOffDriver  =   $dataOffDriverFreelance['STRARRIDOFFDRIVER'];
                $arrIdOffDriver     =   explode(",", $strArrIdOffDriver);
                
                foreach($arrIdOffDriver as $idOffDriver){
                    $this->MainOperation->deleteData("t_dayoff", array("IDDAYOFF"=>$idOffDriver));
                }
		    }
		}
		
		$this->setPointRankDriver();
		$this->updateDriverScheduleMonitor();
		$optionHelper					=	$this->getArrDataOptionHelper();
		$arrIdDriverReviewBonusPeriod	=	$this->MainOperation->getArrIdDriverReviewBonusPeriod();
		$deleteByIdDriver				=	$reviewBonusPunishment == false ? $idDriver : false;
		$arrData						=	["arrIdDriverReviewBonusPeriod" => $arrIdDriverReviewBonusPeriod, "deleteByIdDriver" => $deleteByIdDriver];
		$base64JsonData					=	base64_encode(json_encode($arrData));
		$urlAPICalculateReview			=	BASE_URL."schedule/driverRatingPoint/apiCalculateBonusPunishmentReview/".$base64JsonData;
		
		try {
			$resAPICronCalculateReview	=	json_decode(trim(curl_get_file_contents($urlAPICalculateReview)));
		} catch(Exception $e) {
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver data has been updated", "optionHelper"=>array($optionHelper)));
	}
	
	private function checkInputData(){
		$arrVarValidate	=	array(
			array("driverName","text","Driver Name"),
			array("driverNameFull","text","Driver Full Name"),
			array("phone","text","Phone"),
			array("driverEmail","text","Email"),
			array("optionDriverType","option","Driver Type"),
			array("password","text","Password")
		);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;
	}
	
	public function updateStatus(){

		$this->load->model('MainOperation');
		$data			=	array();
		
		$idDriver		=	validatePostVar($this->postVar, 'idData', true);
		$status			=	validatePostVar($this->postVar, 'status', true);
		
		if($status == 1){
			$arrUpdate		=	array("STATUS" => 1);
			$strStatus		=	"reactivated";
		} else {
			$arrUpdate		=	array("STATUS" => -1);
			$strStatus		=	"deactivated";
		}
		$updateResult	=	$this->MainOperation->updateData("m_driver", $arrUpdate, "IDDRIVER", $idDriver);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}

		if($status == -1){
			$this->MainOperation->updateData(
				"m_usermobile",
				array(
					"TOKEN1" => "",
					"TOKEN2" => ""
				),
				array(
					"IDPARTNERTYPE" => 2,
					"IDPARTNER"		=> $idDriver 
				)
			);
		}
		
		$this->setPointRankDriver();
		$this->updateDriverScheduleMonitor();
		$optionHelper	=	$this->getArrDataOptionHelper();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver status has been ".$strStatus, "optionHelper"=>$optionHelper));
	
	}
	
	private function getArrDataOptionHelper(){

		$this->load->model('ModelOptionHelper');
		$optionHelper	=	$this->ModelOptionHelper->getDataOptionHelperDriver();
		
		return array($optionHelper);

	}
	
	public function getDriverRank(){

		$this->load->model('Master/ModelMasterDriver');
		$driverRankTour		=	$this->ModelMasterDriver->getDataDriverRank(2, 1);
		$driverRankShuttle	=	$this->ModelMasterDriver->getDataDriverRank(1, 1);
		$driverRankFreelance=	$this->ModelMasterDriver->getDataDriverRank(false, 2);
		$driverRankTeam		=	$this->ModelMasterDriver->getDataDriverRank(false, 3);
		$driverRankOffice	=	$this->ModelMasterDriver->getDataDriverRank(false, 4);
		
		setResponseOk(
			array(
				"token"					=>	$this->newToken,
				"driverRankTour"		=>	$driverRankTour,
				"driverRankShuttle"		=>	$driverRankShuttle,
				"driverRankFreelance"	=>	$driverRankFreelance,
				"driverRankTeam"		=>	$driverRankTeam,
				"driverRankOffice"		=>	$driverRankOffice
			)
		);
	
	}
	
	public function saveDriverRank(){

		$this->load->model('MainOperation');
		$arrDriverTour		=	validatePostVar($this->postVar, 'arrDriverTour', true);
		$arrDriverShuttle	=	validatePostVar($this->postVar, 'arrDriverShuttle', true);
		$arrDriverFreelance	=	validatePostVar($this->postVar, 'arrDriverFreelance', true);
		$arrDriverTeam		=	validatePostVar($this->postVar, 'arrDriverTeam', true);
		$arrDriverOffice	=	validatePostVar($this->postVar, 'arrDriverOffice', true);
		$iTour				=	$iShuttle	=	$iFreelance	=	$iTeam	=	$iOffice	=	1;
		
		foreach($arrDriverTour as $idDriverTour){
			$this->MainOperation->updateData("m_driver", array("RANKNUMBER"=>$iTour), "IDDRIVER", $idDriverTour);
			$iTour++;
		}
		
		foreach($arrDriverShuttle as $idDriverShuttle){
			$this->MainOperation->updateData("m_driver", array("RANKNUMBER"=>$iShuttle), "IDDRIVER", $idDriverShuttle);
			$iShuttle++;
		}
		
		foreach($arrDriverFreelance as $idDriverFreelance){
			$this->MainOperation->updateData("m_driver", array("RANKNUMBER"=>$iFreelance), "IDDRIVER", $idDriverFreelance);
			$iFreelance++;
		}
		
		foreach($arrDriverTeam as $idDriverTeam){
			$this->MainOperation->updateData("m_driver", array("RANKNUMBER"=>$iTeam), "IDDRIVER", $idDriverTeam);
			$iTeam++;
		}
		
		foreach($arrDriverOffice as $idDriverOffice){
			$this->MainOperation->updateData("m_driver", array("RANKNUMBER"=>$iOffice), "IDDRIVER", $idDriverOffice);
			$iOffice++;
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver rank has been updated"));
	
	}
	
	public function getDriverAreaOrder(){

		$this->load->model('Master/ModelMasterDriver');
		$idDriver		=	validatePostVar($this->postVar, 'idDriver', true);
		$driverAreaOrder=	$this->ModelMasterDriver->getDataDriverAreaOrder($idDriver);
		
		setResponseOk(array("token"=>$this->newToken, "driverAreaOrder"=>$driverAreaOrder));
	
	}
	
	public function saveDriverAreaOrder(){

		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterDriver');
		
		$idDriver			=	validatePostVar($this->postVar, 'idDriver', true);
		$arrDriverAreaOrder	=	validatePostVar($this->postVar, 'arrDriverAreaOrder', true);
		$driverAreaOrder	=	$this->ModelMasterDriver->getDataDriverAreaOrder($idDriver);
		$orderNumber		=	1;
		$totalDataUpdate	=	0;
		
		foreach($arrDriverAreaOrder as $idArea){
			$indexAreaOrder	=	0;
			foreach($driverAreaOrder as $keyAreaOrder){
				if($keyAreaOrder->IDAREA == $idArea){
					$arrInsertUpdate	=	array(
												"IDDRIVER"		=>	$idDriver,
												"IDAREA"		=>	$idArea,
												"ORDERNUMBER"	=>	$orderNumber
											);
					if($keyAreaOrder->IDDRIVERAREAORDER == 0){
						$this->MainOperation->addData("t_driverareaorder", $arrInsertUpdate);
						$totalDataUpdate++;
					} else {
						$procUpdate	=	$this->MainOperation->updateData("t_driverareaorder", $arrInsertUpdate, "IDDRIVERAREAORDER", $keyAreaOrder->IDDRIVERAREAORDER);
						if($procUpdate['status']) $totalDataUpdate++;
					}
				}
				$indexAreaOrder++;
			}
			$orderNumber++;
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver area priority has been updated. ".$totalDataUpdate." data updated"));
	
	}
	
	private function setPointRankDriver(){
		
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterDriver');
		
		$dataPointDriver	=	$this->ModelMasterDriver->getDataPointDriver($this->subtract30Days);
		
		if($dataPointDriver){
			$rankDriver		=	1;
			$typeDriver		=	0;
			foreach($dataPointDriver as $pointDriver){
				$rankDriver	=	$typeDriver != $pointDriver->IDDRIVERTYPE ? 1 : $rankDriver;
				$totalPoint	=	$pointDriver->TOTALPOINT;
				$arrUpdate	=	array(
									"TOTALPOINT" => $totalPoint * 1,
									"RANKNUMBER" => $rankDriver
								);
				$this->MainOperation->updateData("m_driver", $arrUpdate, "IDDRIVER", $pointDriver->IDDRIVER);
				$typeDriver	=	$pointDriver->IDDRIVERTYPE;
				$rankDriver++;
			}
		}
		
		return true;
	
	}

	public function resetDriverSecretPin(){

		$this->load->model('MainOperation');
		$idDriver		=	validatePostVar($this->postVar, 'idPartner', true);
		$arrUpdatePin	=	array(
								"SECRETPIN"				=>	DEFAULT_DRIVER_PIN,
								"SECRETPINSTATUS"		=>	1,
								"SECRETPINLASTUPDATE"	=>	date('Y-m-d H:i:s')
							);
		$updateResult	=	$this->MainOperation->updateData("m_driver", $arrUpdatePin, "IDDRIVER", $idDriver);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver secret PIN has been reset"));
	
	}
	
	public function setPartnerNewFinanceScheme(){

		$this->load->model('MainOperation');
		$idDriver			=	validatePostVar($this->postVar, 'idPartner', true);
		$totalLoanCar		=	validatePostVar($this->postVar, 'totalLoanCar', false);
		$totalLoanPersonal	=	validatePostVar($this->postVar, 'totalLoanPersonal', false);
		$totalPrepaidCapital=	validatePostVar($this->postVar, 'totalPrepaidCapital', false);
		$arrUpdateScheme	=	array(
									"NEWFINANCESCHEME"		=>	1,
									"NEWFINANCESCHEMESTART"	=>	date('Y-m-d H:i:s')
								);
		$updateResult		=	$this->MainOperation->updateData("m_driver", $arrUpdateScheme, "IDDRIVER", $idDriver);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$dataUserAdmin['NAME'];
		
		if(isset($totalLoanCar) && ($totalLoanCar * 1) > 0){
			$arrInsertRecord=	array(
									"IDDRIVER"		=>	$idDriver,
									"IDLOANTYPE"	=>	1,
									"TYPE"			=>	'D',
									"DESCRIPTION"	=>	"Fund for Loan - Car (Saldo Awal)",
									"AMOUNT"		=>	$totalLoanCar,
									"DATETIMEINPUT"	=>	date('Y-m-d H:i:s'),
									"USERINPUT"		=>	$userAdminName
								);
			$this->MainOperation->addData("t_loandriverrecord", $arrInsertRecord);
		}
		
		if(isset($totalLoanPersonal) && ($totalLoanPersonal * 1) > 0){
			$arrInsertRecord=	array(
									"IDDRIVER"		=>	$idDriver,
									"IDLOANTYPE"	=>	2,
									"TYPE"			=>	'D',
									"DESCRIPTION"	=>	"Fund for Loan - Personal (Saldo Awal)",
									"AMOUNT"		=>	$totalLoanPersonal,
									"DATETIMEINPUT"	=>	date('Y-m-d H:i:s'),
									"USERINPUT"		=>	$userAdminName
								);
			$this->MainOperation->addData("t_loandriverrecord", $arrInsertRecord);
		}
		
		if(isset($totalPrepaidCapital) && ($totalPrepaidCapital * 1) > 0){
			$arrInsertRecord=	array(
									"IDDRIVER"		=>	$idDriver,
									"IDLOANTYPE"	=>	3,
									"TYPE"			=>	'D',
									"DESCRIPTION"	=>	"Fund for Prepaid Capital (Saldo Awal)",
									"AMOUNT"		=>	$totalPrepaidCapital,
									"DATETIMEINPUT"	=>	date('Y-m-d H:i:s'),
									"USERINPUT"		=>	$userAdminName
								);
			$this->MainOperation->addData("t_loandriverrecord", $arrInsertRecord);
		}
		
		$this->MainOperation->updateData("t_scheduledriver", array("STATUSPROCESS"=>4, "STATUS"=>3), "IDDRIVER", $idDriver);
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver`s new finance scheme has been set"));
	
	}

	private function updateDriverScheduleMonitor(){
		
		$this->load->model('MainOperation');
		$this->load->model('Master/ModelMasterDriver');
		
		$dataDriverScheduleMonitor	=	$this->ModelMasterDriver->getDataDriverScheduleMonitor();
		if($dataDriverScheduleMonitor){
			foreach($dataDriverScheduleMonitor as $keyDriverScheduleMonitor){
				$this->MainOperation->calculateScheduleDriverMonitor($keyDriverScheduleMonitor->DATESCHEDULE);
			}
		}
		
		return true;
	
	}
}