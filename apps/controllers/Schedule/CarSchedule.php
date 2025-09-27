<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CarSchedule extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->postVar	=	decodeJsonPost();
			$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid submission data"));
			$this->newToken	=	isLoggedIn($this->token, true);
		} else {
			$functionName	=	$this->uri->segment(3);
			if($functionName != "APISaveCarSchedule" && $functionName != "APIDeleteCarSchedule"){
				header('HTTP/1.0 403 Forbidden');
				die();
			}
		}
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataCarSchedule(){
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelCarSchedule');
		
		$month				=	validatePostVar($this->postVar, 'month', true);
		$year				=	validatePostVar($this->postVar, 'year', true);
		$idVendorCar		=	validatePostVar($this->postVar, 'idVendorCar', false);
		$idCarType			=	validatePostVar($this->postVar, 'idCarType', false);
		$searchKeyword		=	validatePostVar($this->postVar, 'searchKeyword', false);
		$yearMonth			=	$year."-".$month;
		$firstDate			=	$yearMonth."-01";
		$firstDateStr		=	date("d-m-Y", strtotime($firstDate));
		$firstDateTimeStr	=	date("Y-m-d 00:00:00", strtotime($firstDate));
		$lastDateStr		=	date("t-m-Y", strtotime($firstDate));
		$totalDays			=	date("t", strtotime($firstDate));
		$strYearMonth		=	date("M Y", strtotime($firstDate));
		$dataCar			=	$this->ModelCarSchedule->getDataCar($idVendorCar, $idCarType, $searchKeyword);
		$dataStatistic		=	$this->ModelCarSchedule->getDataCarStatistic($yearMonth);
		$arrDates			=	$arrDataRent	=	array();
		
		for($i=1; $i<=$totalDays; $i++){
			$arrDates[]		=	$i;
			$arrDataRent[]	=	array();
		}
		
		if(!$dataCar) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "strYearMonth"=>$strYearMonth, "dataStatistic"=>$dataStatistic, "arrDates"=>$arrDates));

		foreach($dataCar as $keyCar){
			$keyCar->DATASCHEDULE	=	$arrDataRent;
		}

		$dataSchedule	=	$this->ModelCarSchedule->getDataCarSchedule($yearMonth);
		if($dataSchedule){
			foreach($dataSchedule as $keySchedule){
				$idReservationDB=	$keySchedule->IDRESERVATION * 1;
				$idCarVendorDB	=	$keySchedule->IDCARVENDOR;
				$timeScheduleDB	=	$keySchedule->RESERVATIONTIMESTART;
				$idScheduleDB	=	$keySchedule->IDSCHEDULECAR * 1;
				$dateScheduleDB	=	$keySchedule->SCHEDULEDATE * 1;
				$bookingCodeDB	=	$keySchedule->BOOKINGCODE;
				$durationDB		=	$keySchedule->DURATION;
				$dateTimeStartDB=	$keySchedule->DATETIMESTART;
				$dateTimeEndDB	=	$keySchedule->DATETIMEEND;
				$customerNameDB	=	$keySchedule->CUSTOMERNAME;
				$idxSchedule	=	$dateScheduleDB - 1;
				
				foreach($dataCar as $keyCar){
					if($keyCar->IDCARVENDOR == $idCarVendorDB){
						$keyCar->DATASCHEDULE[$idxSchedule][]	=	array($idScheduleDB, $timeScheduleDB, $idReservationDB, $bookingCodeDB, $durationDB, $dateTimeStartDB, $dateTimeEndDB, $customerNameDB);
						break;
					}
				}
			}
		}
		
		$dataDayOff		=	$this->ModelCarSchedule->getDataCarDayOff($yearMonth);
		if($dataDayOff){
			foreach($dataDayOff as $keyDayOff){
				$idCarVendorDB		=	$keyDayOff->IDCARVENDOR;
				$idDayOffDB			=	$keyDayOff->IDDAYOFF;
				$dateDayOffDB		=	$keyDayOff->DAYOFFDATE * 1;
				$timeStartDayOffDB	=	$keyDayOff->TIMESTART;
				$dayOffTypeDB		=	$keyDayOff->DAYOFFTYPE;
				$dayOffDurationDB	=	$keyDayOff->DURATIONHOUR;
				$dateTimeStartDB	=	$keyDayOff->DATETIMESTART;
				$dateTimeEndDB		=	$keyDayOff->DATETIMEEND;
				$reasonDB			=	$keyDayOff->REASON;
				$idxSchedule		=	$dateDayOffDB - 1;
				
				foreach($dataCar as $keyCar){
					if($keyCar->IDCARVENDOR == $idCarVendorDB){
						$keyCar->DATASCHEDULE[$idxSchedule][]	=	array(0, $timeStartDayOffDB, $idDayOffDB, $dayOffTypeDB, $dayOffDurationDB, $dateTimeStartDB, $dateTimeEndDB, $reasonDB);
						break;
					}
				}
			}
		}
		
		setResponseOk(
			array(
				"token"				=>	$this->newToken,
				"dataCar"			=>	$dataCar,
				"strYearMonth"		=>	$strYearMonth,
				"dataStatistic"		=>	$dataStatistic,
				"arrDates"			=>	$arrDates,
				"firstDateStr"		=>	$firstDateStr,
				"firstDateTimeStr"	=>	$firstDateTimeStr,
				"lastDateStr"		=>	$lastDateStr
			)
		);
	}
	
	public function getDataReservationSchedule(){
		$this->load->model('Schedule/ModelCarSchedule');
		
		$month			=	validatePostVar($this->postVar, 'month', true);
		$year			=	validatePostVar($this->postVar, 'year', true);
		$idSource		=	validatePostVar($this->postVar, 'idSource', false);
		$dateSchedule	=	validatePostVar($this->postVar, 'dateSchedule', false);
		$bookingCode	=	validatePostVar($this->postVar, 'bookingCode', false);
		$searchKeyword	=	validatePostVar($this->postVar, 'searchKeyword', false);
		$yearMonth		=	$year."-".$month;
		
		if(isset($dateSchedule) && $dateSchedule != ""){
			$dateSchedule	=	DateTime::createFromFormat('d-m-Y', $dateSchedule);
			$dateSchedule	=	$dateSchedule->format('Y-m-d');
		}
		
		$dataTable		=	$this->ModelCarSchedule->getDataReservationSchedule($yearMonth, $idSource, $dateSchedule, $bookingCode, $searchKeyword);
	
		if(!$dataTable) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));	
	}
	
	public function getDataUnScheduleCar(){
		$this->load->model('Schedule/ModelCarSchedule');
		
		$idVendor	=	validatePostVar($this->postVar, 'idVendor', true);
		$idCarVendor=	validatePostVar($this->postVar, 'idCarVendor', true);
		$month		=	validatePostVar($this->postVar, 'month', true);
		$year		=	validatePostVar($this->postVar, 'year', true);
		$yearMonth	=	$year."-".$month;
		$idCarType	=	validatePostVar($this->postVar, 'idCarType', true);
		$dataTable	=	$this->ModelCarSchedule->getDataUnScheduleCar($yearMonth, $idCarType, $idCarVendor, $idVendor);
	
		if(!$dataTable) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));	
	}
	
	public function getDataCarList(){
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelCarSchedule');
		
		$scheduleDate	=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDate	=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$scheduleDateStr=	$scheduleDate->format('d M Y');
		$scheduleDate	=	$scheduleDate->format('Y-m-d');
		$idCarType		=	validatePostVar($this->postVar, 'idCarType', true);
		$idVendor		=	validatePostVar($this->postVar, 'idVendor', true);
		$dataTable		=	$this->ModelCarSchedule->getDataCarList($scheduleDate, $idCarType, $idVendor);
	
		if(!$dataTable) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "scheduleDateStr"=>$scheduleDateStr));
		$vendorData		=	$this->MainOperation->getDataVendor($idVendor);
		$vendorName		=	$vendorData['NAME'];
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "scheduleDateStr"=>$scheduleDateStr, "vendorName"=>$vendorName));	
	}

	public function APISaveCarSchedule($jsonParamBase64Encode){
		$arrDataCarSchedule		=	json_decode(base64_decode(rawurldecode($jsonParamBase64Encode)), TRUE);
		$resultSaveCarSchedule	=	$this->saveCarSchedule($arrDataCarSchedule);

		if($resultSaveCarSchedule['isSuccess']){
			setResponseOk(array("msg"=>$resultSaveCarSchedule['msg']));
		} else {
			setResponseInternalServerError(array("msg"=>$resultSaveCarSchedule['msg']));
		}
	}
	
	public function saveCarSchedule($arrDataCarSchedule = false){
		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelCarSchedule');
		
		$idCarVendor		=	$arrDataCarSchedule ? $arrDataCarSchedule['idCarVendor'] : validatePostVar($this->postVar, 'idCarVendor', false);
		$idDriver			=	$arrDataCarSchedule ? $arrDataCarSchedule['idDriver'] : validatePostVar($this->postVar, 'idDriver', false);
		$reservationDetails	=	$arrDataCarSchedule ? $arrDataCarSchedule['reservationDetails'] : validatePostVar($this->postVar, 'arrIDReservationDetails', false);
		$totalInsertSchedule=	0;
		
		if(!isset($idCarVendor) || $idCarVendor == "" || $idCarVendor == 0) {
			if(!$arrDataCarSchedule) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select car for reservation schedule"));
			if($arrDataCarSchedule) return [
				"isSuccess"	=>	false,
				"msg"		=>	"Please select car for reservation schedule"
			];
		}
		
		if(!is_array($reservationDetails) || count($reservationDetails) <= 0) {
			if(!$arrDataCarSchedule) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select at least one reservation schedule"));
			if($arrDataCarSchedule) return [
				"isSuccess"	=>	false,
				"msg"		=>	"Please select at least one reservation schedule"
			];
		}
		
		$tmToday					=	new DateTime("today");
		$dataUserAdmin				=	$arrDataCarSchedule ? [] : $this->MainOperation->getDataUserAdmin($this->newToken);
		$dataVendorCar				=	$this->MainOperation->getDataVendorCar($idCarVendor);
		$dataMessageType			=	$this->MainOperation->getDataMessageType(1);
		$userAdminName				=	$arrDataCarSchedule ? $arrDataCarSchedule['userAdminName'] : $dataUserAdmin['NAME'];
		$vendorCarName				=	$dataVendorCar['BRAND']." ".$dataVendorCar['MODEL']. "[".$dataVendorCar['PLATNUMBER']."]";
		$vendorTokenFCM				=	$dataVendorCar['TOKENFCM'];
		$activityMessage			=	$dataMessageType['ACTIVITY'];
		$arrDataReservationDetails	=	[];
		$indexScheduleMain			=	-1;
		
		//Check schedule availability and collect data details including datetime
		foreach($reservationDetails as $idReservationDetails){
			$dataReservationSchedule=	$this->ModelCarSchedule->getDetailReservationSchedule($idReservationDetails);

			if(!$dataReservationSchedule) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid schedule selection, please try again later"));
			
			$idReservation					=	$dataReservationSchedule['IDRESERVATION'];
			$idVendor						=	$dataReservationSchedule['IDVENDOR'];
			$bookingCode					=	$dataReservationSchedule['BOOKINGCODE'];
			$customerName					=	$dataReservationSchedule['CUSTOMERNAME'];
			$productName					=	$dataReservationSchedule['PRODUCTNAME'];
			$allScheduleReservation			=	$this->ModelCarSchedule->getAllScheduleReservation($idReservation, $idVendor);
			
			if(!$allScheduleReservation) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid schedule selection, please try again later"));
			
			$reservationScheduleDate		=	$dataReservationSchedule['SCHEDULEDATE'];
			$reservationScheduleTimeStart	=	$dataReservationSchedule['RESERVATIONTIMESTART'];
			$dateTimeStartReservation		=	$reservationScheduleDate." ".$reservationScheduleTimeStart;
			$dateTimeEnd					=	$dateTimeStartReservation;
			
			foreach($allScheduleReservation as $indexSchedule => $rowSchedule){
				$dateTimeStart	=	$dateTimeStartReservation;
				if($indexSchedule != 0)	{
					if(isset($arrDataReservationDetails[$indexSchedule + $indexScheduleMain - 1])){
						$dateTimeStart	=	$arrDataReservationDetails[$indexSchedule + $indexScheduleMain - 1]['dateTimeEnd'].":01";
					} else if(isset($allScheduleReservation[$indexSchedule - 1]->DATETIMEEND) && $allScheduleReservation[$indexSchedule - 1]->DATETIMEEND != '') {
						$dateTimeStart	=	$allScheduleReservation[$indexSchedule - 1]->DATETIMEEND;
						$dateTimeStart	=	substr($dateTimeStart, 0, 16).":01";
					}
				}
				
				$idReservationDetailsCheck	=	$rowSchedule->IDRESERVATIONDETAILS;
				$scheduleDateCheck			=	$rowSchedule->SCHEDULEDATE;
				$durationCheck				=	intval($rowSchedule->DURATION);
				$durationCheck				=	$durationCheck <= 0 ? 12 : $durationCheck;
				$dateTimeStartDT			=	new DateTime($dateTimeStart);
				$dateTimeStartStr			=	$dateTimeStartDT->format('d M Y H:i');
				$dateTimeEndDT				=	$dateTimeStartDT->add(new DateInterval('PT'.$durationCheck.'H'));
				$dateTimeEndStr				=	$dateTimeEndDT->format('d M Y H:i');
				$dateTimeEndDT				=	$dateTimeEndDT->sub(new DateInterval('PT1M'));
				$dateTimeEnd				=	$dateTimeEndDT->format('Y-m-d H:i');
				
				if($idReservationDetailsCheck == $idReservationDetails){
					$isDayOffConflict		=	$this->ModelCarSchedule->isDayOffConflict($idCarVendor, $dateTimeStart, $dateTimeStart);
					$isScheduleAvailable	=	$this->ModelCarSchedule->isScheduleAvailable($idCarVendor, $dateTimeStart, $dateTimeEnd);
					$productDurationHour	=	$productName." (".$durationCheck." Hours)";
					
					if($isDayOffConflict){
						setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Cannot add schedule. Car <b>day off is</b> exist between <b>".$dateTimeStartStr."</b> <b>and ".$dateTimeEndStr."</b>"));
					} else if(!$isScheduleAvailable){
						$messageWarning		=	$this->generateUnaviableScheduleCar($bookingCode, $customerName, $productDurationHour, $dateTimeStartStr, $dateTimeEndStr, 'not available');
						setResponseForbidden(array("token"=>$this->newToken, "msg"=>$messageWarning));
					} else {
						$isSchedulePossible	=	$this->isSchedulePossibleInArray($arrDataReservationDetails, $dateTimeStart, $dateTimeEnd);
						if(!$isSchedulePossible){
							$messageWarning	=	$this->generateUnaviableScheduleCar($bookingCode, $customerName, $productDurationHour, $dateTimeStartStr, $dateTimeEndStr, 'conflict');
							setResponseForbidden(array("token"=>$this->newToken, "msg"=>$messageWarning));
						}
						
						$arrDataReservationDetails[]	=	[
							'idReservationDetails'	=>	$idReservationDetails,
							'dateTimeStart'			=>	$dateTimeStart,
							'dateTimeEnd'			=>	$dateTimeEnd
						];
						$indexScheduleMain++;
					}
				}
			}
		}
		
		foreach($arrDataReservationDetails as $dataReservationDetails){
			$idReservationDetails	=	$dataReservationDetails['idReservationDetails'];
			$dateTimeStart			=	$dataReservationDetails['dateTimeStart'];
			$dateTimeEnd			=	$dataReservationDetails['dateTimeEnd'];
			
			$arrInsert		=	array(
				"IDRESERVATIONDETAILS"	=>	$idReservationDetails,
				"IDCARVENDOR"			=>	$idCarVendor,
				"IDDRIVER"				=>	$idDriver,
				"DATETIMESTART"			=>	$dateTimeStart,
				"DATETIMEEND"			=>	$dateTimeEnd,
				"USERINPUT"				=>	$userAdminName,
				"DATETIMEINPUT"			=>	date("Y-m-d H:i:s"),
				"STATUS"				=>	1
			);
			$procInsert		=	$this->MainOperation->addData("t_schedulecar", $arrInsert);
			
			if($procInsert['status']){
				$idVendor			=	$this->MainOperation->getIdVendorByIdCarVendor($idCarVendor);
				$this->MainOperation->updateData("t_reservationdetails", array("IDVENDOR"=>$idVendor), "IDRESERVATIONDETAILS", $idReservationDetails);
				
				$idCarSchedule		=	$procInsert['insertID'];
				$reservationDetails	=	$this->ModelCarSchedule->getDetailSchedule($idCarSchedule, $idReservationDetails);
				
				if($reservationDetails){
					$idReservation	=	$reservationDetails['IDRESERVATION'];
					$customerName	=	$reservationDetails['CUSTOMERNAME'];
					$rsvTitle		=	$reservationDetails['RESERVATIONTITLE'];
					$rsvService		=	$reservationDetails['PRODUCTNAME'];
					$dateSchedule	=	$reservationDetails['SCHEDULEDATE'];
					$carDetails		=	$reservationDetails['CARDETAIL'];
					$tmDateSchedule	=	DateTime::createFromFormat('Y-m-d', $reservationDetails['SCHEDULEDATEDB']);
					$diffDays		=	$tmToday->diff($tmDateSchedule);
					$diffDays		=	(integer)$diffDays->format( "%R%a" );
					$strDateSchedule=	"";
					
					$this->MainOperation->updateData("t_reservation", array("STATUS"=>2), "IDRESERVATION", $idReservation);
					switch($diffDays) {
						case 0	:	$strDateSchedule	=	" (Today)"; break;
						case +1	:	$strDateSchedule	=	" (Tomorrow)"; break;
						default	:	$strDateSchedule	=	""; break;
					}

					$titleDB		=	"New schedule for ".$dateSchedule;
					$titleMsg		=	"New schedule for ".$dateSchedule.$strDateSchedule;
					$body			=	"Details schedule:\n";
					$body			.=	"Customer Name : ".$customerName."\n";
					$body			.=	"Reservation Title : ".$rsvTitle."\n";
					$body			.=	"Service : ".$rsvService."\n";
					$body			.=	"Car : ".$carDetails;
					$additionalArray=	array(
						"activity"	=>	$activityMessage,
						"idPrimary"	=>	$idCarSchedule,
					);
					
					$arrInsertMsg	=	array(
						"IDMESSAGEPARTNERTYPE"	=>	1,
						"IDPARTNERTYPE"			=>	1,
						"IDPARTNER"				=>	$idVendor,
						"IDPRIMARY"				=>	$idCarSchedule,
						"TITLE"					=>	$titleDB,
						"MESSAGE"				=>	$body,
						"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
					);
					$procInsertMsg	=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
						
					if($procInsertMsg['status']){
						if($vendorTokenFCM != "") $this->fcm->sendPushNotification($vendorTokenFCM, $titleMsg, $body, $additionalArray);
					}
				}
				$totalInsertSchedule++;
			}
		}
		
		if($totalInsertSchedule > 0){
			if(!$arrDataCarSchedule) setResponseOk(array("token"=>$this->newToken, "msg"=>$totalInsertSchedule." reservation schedule(s) have been added to the vendor car : ".$vendorCarName));
			if($arrDataCarSchedule) return [
				"isSuccess"	=>	true,
				"msg"		=>	"Schedule(s) have been added to the vendor car"
			];
		} else {
			if(!$arrDataCarSchedule) setResponseNotModified(array("token"=>$this->newToken, "msg"=>"Failed to add vendor car schedule"));
			if($arrDataCarSchedule) return [
				"isSuccess"	=>	false,
				"msg"		=>	"Failed to add vendor car schedule"
			];
		}
	}
	
	private function isSchedulePossibleInArray($arrDataReservationDetails, $dateTimeStart, $dateTimeEnd){
		if(is_array($arrDataReservationDetails) && count($arrDataReservationDetails) > 0){
			foreach($arrDataReservationDetails as $dataReservationDetails){
				$idReservationDetails	=	$dataReservationDetails['idReservationDetails'];
				$dateTimeStartCheck		=	$dataReservationDetails['dateTimeStart'];
				$dateTimeStartCheckTS	=	strtotime($dateTimeStartCheck);
				$dateTimeEndCheck		=	$dataReservationDetails['dateTimeEnd'];
				$dateTimeEndCheckTS 	=	strtotime($dateTimeEndCheck);
				$dateTimeStartTS		=	strtotime($dateTimeStart);
				$dateTimeEndTS			=	strtotime($dateTimeEnd);
				
				if(($dateTimeStartTS >= $dateTimeStartCheckTS && $dateTimeStartTS <= $dateTimeEndCheckTS) || ($dateTimeEndTS >= $dateTimeStartCheckTS && $dateTimeEndTS <= $dateTimeEndCheckTS)){
					return false;
				}
			}
		}
		
		return true;
	}
	
	private function generateUnaviableScheduleCar($bookingCode, $customerName, $productDurationHour, $dateTimeStartStr, $dateTimeEndStr, $causeStr){
		$messageWarning	=	'Car schedule '.$causeStr.' for this reservation :<br/><br/>';
		$messageWarning	.=	'<div class="order-details-customer-info">';
		$messageWarning	.=		'<ul class="ml-5">';
		$messageWarning	.=			'<li> <span>Code</span> <span id="custContactStr">'.$bookingCode.'</span> </li>';
		$messageWarning	.=			'<li> <span>Name</span> <span id="custNameStr">'.$customerName.'</span> </li>';
		$messageWarning	.=			'<li> <span>Product</span> <span id="custEmailStr">'.$productDurationHour.'</span> </li>';
		$messageWarning	.=			'<li> <span>Date Time</span> <span id="custEmailStr">'.$dateTimeStartStr.' <b>to</b> '.$dateTimeEndStr.'</span> </li>';
		$messageWarning	.=		'</ul>';
		$messageWarning	.=	'</div><br/><br/>';
		$messageWarning	.=	'the schedule slot has been filled by another booking';
		
		return $messageWarning;
	}

	public function APIDeleteCarSchedule($jsonParamBase64Encode){
		$arrDataDeleteSchedule	=	json_decode(base64_decode(rawurldecode($jsonParamBase64Encode)), TRUE);
		$resultDeleteCarSchedule=	$this->deleteCarSchedule($arrDataDeleteSchedule);

		if($resultDeleteCarSchedule['isSuccess']){
			setResponseOk(array("msg"=>$resultDeleteCarSchedule['msg']));
		} else {
			setResponseInternalServerError(array("msg"=>$resultDeleteCarSchedule['msg']));
		}
	}
	
	public function addCarDayOff(){
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelCarSchedule');
		
		$this->checkInputDataDayOff();
		$idCarVendor		=	validatePostVar($this->postVar, 'idCarVendor', true);
		$idCarDayOffType	=	validatePostVar($this->postVar, 'optionType', true);
		$dateStart			=	validatePostVar($this->postVar, 'dateStart', true);
		$dateStartDT		=	DateTime::createFromFormat('d-m-Y', $dateStart);
		$dateStart			=	$dateStartDT->format('Y-m-d');
		$hourStart			=	validatePostVar($this->postVar, 'hourStart', true);
		$minuteStart		=	validatePostVar($this->postVar, 'minuteStart', true);
		$durationHour		=	validatePostVar($this->postVar, 'durationHour', true);
		$description		=	validatePostVar($this->postVar, 'description', true);
		$isNeedCost			=	validatePostVar($this->postVar, 'isNeedCost', false);
		$dateTimeStart		=	$dateStart." ".$hourStart.":".$minuteStart;
		$dateTimeStartDT	=	new DateTime($dateTimeStart);
		$dateTimeStartStr	=	$dateTimeStartDT->format('d M Y H:i');
		$dateTimeEndDT		=	new DateTime($dateTimeStart);
		$dateTimeEndDT		=	$dateTimeEndDT->modify("+$durationHour hours");
		$dateTimeEnd		=	$dateTimeEndDT->format('Y-m-d H:i:s');
		$dateTimeEndStr		=	$dateTimeEndDT->format('d M Y H:i');
		
		$isScheduleAvailable=	$this->ModelCarSchedule->isScheduleAvailable($idCarVendor, $dateTimeStart, $dateTimeEnd);
		if(!$isScheduleAvailable) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Cannot add day off. Please check car schedule between <b>".$dateTimeStartStr."</b> <b>and ".$dateTimeEndStr."</b>"));
		
		$dataUserAdmin			=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$dataVendorCar			=	$this->MainOperation->getDataVendorCar($idCarVendor);
		$userAdminName			=	$dataUserAdmin['NAME'];
		$vendorCarName			=	$dataVendorCar['BRAND']." ".$dataVendorCar['MODEL']. "[".$dataVendorCar['PLATNUMBER']."]";
		$dateTimeStartPerDateDT	=	$dateTimeStartDT;
		$arrDataInsertDayOff	=	[];
		$totalInsertDayOff		=	0;
		$idDayOffFirst			=	0;

		while ($durationHour > 0) {
			if ($durationHour >= 24) {
				$hoursToAdd		=	24;
				$durationHour	-=	$hoursToAdd;
			} else {
				$hoursToAdd		=	$durationHour;
				$durationHour	=	0;
			}
			
			$dateTimeStartPerDate	=	$dateTimeStartPerDateDT->format('Y-m-d H:i:s');
			$dateTimeStartPerDateStr=	$dateTimeStartPerDateDT->format('d M Y H:i');
			$dateStartPerDate		=	$dateTimeStartPerDateDT->format('Y-m-d');
			$dateTimeEndPerDateDT	=	$dateTimeStartPerDateDT->modify("+$hoursToAdd hours");
			$dateTimeEndPerDate		=	$dateTimeEndPerDateDT->format('Y-m-d H:i:s');
			$dateTimeEndPerDateStr	=	$dateTimeEndPerDateDT->format('d M Y H:i');
			$dateTimeStartPerDateDT	=	$dateTimeEndPerDateDT;
			$isDayOffConflict		=	$this->ModelCarSchedule->isDayOffConflict($idCarVendor, $dateTimeStartPerDate, $dateTimeEndPerDate);

			if($isDayOffConflict) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Cannot add day off. Car day off is already exist between <b>".$dateTimeStartPerDateStr."</b> <b>and ".$dateTimeEndPerDateStr."</b>"));
			
			$arrDataInsertDayOff[]	=	[
				'hoursToAdd'			=>	$hoursToAdd,
				'dateStartPerDate'		=>	$dateStartPerDate,
				'dateTimeStartPerDate'	=>	$dateTimeStartPerDate,
				'dateTimeEndPerDate'	=>	$dateTimeEndPerDate
			];
		}
		
		foreach($arrDataInsertDayOff as $dataInsertDayOff){
			$arrInsertDayOffReq		=	[
				'IDCARVENDOR'		=>	$idCarVendor,
				'DATEDAYOFF'		=>	$dataInsertDayOff['dateStartPerDate'],
				'REASON'			=>	$description,
				'DATETIMEINPUT'		=>	date('Y-m-d H:i:s'),
				'STATUS'			=>	1,
				'DATETIMEAPPROVAL'	=>	date('Y-m-d H:i:s'),
				'USERAPPROVAL'		=>	$userAdminName
			];
			$procInsertDayOffReq	=	$this->MainOperation->addData('t_dayoffrequest', $arrInsertDayOffReq);
			
			if($procInsertDayOffReq['status']){
				$idDayOffRequest	=	$procInsertDayOffReq['insertID'];
				$arrInsertDayOff	=	[
					'IDDAYOFFREQUEST'	=>	$idDayOffRequest,
					'IDCARVENDOR'		=>	$idCarVendor,
					'DATEDAYOFF'		=>	$dataInsertDayOff['dateStartPerDate'],
					'REASON'			=>	$description,
					'DATETIMEINPUT'		=>	date('Y-m-d H:i:s'),
				];
				$procInsertDayOff	=	$this->MainOperation->addData('t_dayoff', $arrInsertDayOff);
				
				if($procInsertDayOff['status']){
					$idDayOff		=	$procInsertDayOff['insertID'];
					$idDayOffFirst	=	$idDayOffFirst == 0 ? $idDayOff : $idDayOffFirst;
					$arrInsertDayOffDetail	=	[
						'IDDAYOFF'			=>	$idDayOff,
						'IDCARDAYOFFTYPE'	=>	$idCarDayOffType,
						'DURATIONHOUR'		=>	$dataInsertDayOff['hoursToAdd'],
						'DATETIMESTART'		=>	$dataInsertDayOff['dateTimeStartPerDate'],
						'DATETIMEEND'		=>	$dataInsertDayOff['dateTimeEndPerDate']
					];
					$this->MainOperation->addData('t_dayoffcardetail', $arrInsertDayOffDetail);
				}
			
				$totalInsertDayOff++;
			}
		}
		
		if($totalInsertDayOff > 0){
			if(intval($isNeedCost) == 1){
				$idCarCostType		=	validatePostVar($this->postVar, 'optionCarCostType', true);
				$costNominal		=	validatePostVar($this->postVar, 'costNominal', true);
				$costNominal		=	preg_replace("/[^0-9]/", "", $costNominal);
				$costDescription	=	validatePostVar($this->postVar, 'costDescription', true);
				$arrInsertCarCost	=	[
					"IDCARVENDOR"	=>	$idCarVendor,
					"IDCARCOSTTYPE"	=>	$idCarCostType,
					"IDDAYOFF"		=>	$idDayOffFirst,
					"DESCRIPTION"	=>	$costDescription,
					"NOMINAL"		=>	$costNominal,
					"USERINPUT"		=>	$userAdminName,
					"DATETIMEINPUT"	=>	date('Y-m-d H:i:s')
				];
				$this->MainOperation->addData('t_carcost', $arrInsertCarCost);
			}
			
			setResponseOk(array("token"=>$this->newToken, "msg"=>$totalInsertDayOff." day off schedule(s) have been added to the vendor car : ".$vendorCarName));
		} else {
			setResponseNotModified(array("token"=>$this->newToken, "msg"=>"Failed to add vendor car day off"));
		}
	}

	private function checkInputDataDayOff(){
		$arrVarValidate	=	array(
			array("idCarVendor","option","Car Vendor Details"),
			array("optionType","option","Day Off Type"),
			array("dateStart","option","Day Off Date Start"),
			array("hourStart","option","Day Off Time"),
			array("minuteStart","option","Day Off Time"),
			array("durationHour","text","Duration (Hour)"),
			array("description","text","Description")
		);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;
	}
	
	public function deleteCarSchedule($arrDataDeleteSchedule = false){
		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		$this->load->model('Schedule/ModelCarSchedule');
		
		$idCarSchedule		=	$arrDataDeleteSchedule ? $arrDataDeleteSchedule['idCarSchedule'] : validatePostVar($this->postVar, 'idData', true);

		if($idCarSchedule <= 0) {
			if(!$arrDataDeleteSchedule) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
			if($arrDataDeleteSchedule) return [
				"isSuccess"	=>	false,	
				"msg"		=>	"Invalid parameter sent"
			];
		}
		
		$reservationDetails	=	$this->ModelCarSchedule->getDetailSchedule($idCarSchedule, 0);
		$procDelete			=	$this->MainOperation->deleteData("t_schedulecar", array("IDSCHEDULECAR"=>$idCarSchedule));
		
		if(!$procDelete){
			if(!$arrDataDeleteSchedule) switchMySQLErrorCode($procDelete['errCode'], $this->newToken);
			if($arrDataDeleteSchedule) return [
				"isSuccess"	=>	false,
				"msg"		=>	"Internal database query error"
			];
		}
		
		$idReservation		=	$reservationDetails['IDRESERVATION'];
		$driverHandle		=	$this->ModelReservation->getReservationHandleDriver($idReservation);
		$carVendorHandle	=	$this->ModelReservation->getReservationHandleVendorCar($idReservation);
		$ticketvendorHandle	=	$this->ModelReservation->getReservationHandleVendorTicket($idReservation);
		
		if(empty($driverHandle) && empty($carVendorHandle) && empty($ticketvendorHandle)){
			$this->MainOperation->updateData("t_reservation", array("STATUS"=>1), "IDRESERVATION", $idReservation);
		}
		
		$tmToday			=	new DateTime("today");
		$idCarVendor		=	$reservationDetails['IDCARVENDOR'];
		$dataVendorCar		=	$this->MainOperation->getDataVendorCar($idCarVendor);
		$dataMessageType	=	$this->MainOperation->getDataMessageType(3);
		$activityMessage	=	$dataMessageType['ACTIVITY'];
		
		if($reservationDetails){
			$idVendor		=	$reservationDetails['IDVENDOR'];
			$customerName	=	$reservationDetails['CUSTOMERNAME'];
			$rsvTitle		=	$reservationDetails['RESERVATIONTITLE'];
			$rsvService		=	$reservationDetails['PRODUCTNAME'];
			$dateSchedule	=	$reservationDetails['SCHEDULEDATE'];
			$vendorTokenFCM	=	$dataVendorCar['TOKENFCM'];
			$carDetails		=	$reservationDetails['CARDETAIL'];
			$tmDateSchedule	=	DateTime::createFromFormat('Y-m-d', $reservationDetails['SCHEDULEDATEDB']);
			$diffDays		=	$tmToday->diff($tmDateSchedule);
			$diffDays		=	(integer)$diffDays->format( "%R%a" );
			$strDateSchedule=	"";
			
			switch($diffDays) {
				case 0	:	$strDateSchedule	=	" (Today)"; break;
				case +1	:	$strDateSchedule	=	" (Tomorrow)"; break;
				default	:	$strDateSchedule	=	""; break;
			}

			$titleDB		=	"Schedule cancelation for ".$dateSchedule;
			$titleMsg		=	"Schedule cancelation for ".$dateSchedule.$strDateSchedule;
			$body			=	"Details schedule\n";
			$body			.=	"Customer Name : ".$customerName."\n";
			$body			.=	"Reservation Title : ".$rsvTitle."\n";
			$body			.=	"Service : ".$rsvService."\n";
			$body			.=	"Car : ".$carDetails;
			$additionalArray=	array(
									"activity"	=>	$activityMessage,
									"idPrimary"	=>	$idCarSchedule,
								);
			
			$arrInsertMsg	=	array(
										"IDMESSAGEPARTNERTYPE"	=>	3,
										"IDPARTNERTYPE"			=>	2,
										"IDPARTNER"				=>	$idVendor,
										"IDPRIMARY"				=>	$idCarSchedule,
										"TITLE"					=>	$titleDB,
										"MESSAGE"				=>	$body,
										"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
								);
			$procInsertMsg	=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
				
			if($procInsertMsg['status']){
				if($vendorTokenFCM != "") $this->fcm->sendPushNotification($vendorTokenFCM, $titleMsg, $body, $additionalArray);
			}
		
		}
		
		if(!$arrDataDeleteSchedule) setResponseOk(array("token"=>$this->newToken, "msg"=>"Rent car schedule has been deleted"));
		if($arrDataDeleteSchedule) return [
			"isSuccess"	=>	true,
			"msg"		=>	"Car schedule have been deleted"
		];
	}
	
	public function getDetailSchedule(){
		$this->load->model('Schedule/ModelCarSchedule');
		
		$idCarSchedule			=	validatePostVar($this->postVar, 'idCarSchedule', false);
		$idReservationDetails	=	validatePostVar($this->postVar, 'idReservationDetails', false);
		$detailData				=	$this->ModelCarSchedule->getDetailSchedule($idCarSchedule, $idReservationDetails);
		
		if(!$detailData) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found"));
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$detailData));	
	}
	
	public function getDetailDayOff(){
		$this->load->model('Schedule/ModelCarSchedule');
		
		$idDayoff	=	validatePostVar($this->postVar, 'idDayoff', false);
		$detailData	=	$this->ModelCarSchedule->getDetailDayOff($idDayoff);
		
		if(!$detailData) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found"));
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$detailData));	
	}
	
	public function deleteCarDayOff(){
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelCarSchedule');
		
		$idDayoff	=	validatePostVar($this->postVar, 'idData', true);
		$detailDayOff	=	$this->ModelCarSchedule->getDetailDayOff($idDayoff);

		if($idDayoff <= 0 || !$detailDayOff) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
		
		$idDayOffRequest=	$detailDayOff['IDDAYOFFREQUEST'];
		$procDelete		=	$this->MainOperation->deleteData("t_dayoff", array("IDDAYOFF"=>$idDayoff));
		
		if(!$procDelete){
			switchMySQLErrorCode($procDelete['errCode'], $this->newToken);
		}
		
		$this->MainOperation->deleteData("t_dayoffcardetail", array("IDDAYOFF"=>$idDayoff));
		$this->MainOperation->deleteData("t_dayoffrequest", array("IDDAYOFFREQUEST"=>$idDayOffRequest));
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Car day off has been deleted"));
	}	
}