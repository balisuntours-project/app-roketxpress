<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DriverScheduleAuto extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	var $maxTotalConsecutiveNoJob = 5;
	
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
	
	public function getDataAutoScheduleSetting(){
		$this->load->model('MainOperation');
		$dataSetting	=	$this->MainOperation->getDataAutoScheduleSetting("1,2,3,4,8,9");
	
		if(!$dataSetting) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		setResponseOk(array("token"=>$this->newToken, "dataSetting"=>$dataSetting));
	}
	
	public function getDataScheduleAuto(){
		$this->load->model('Schedule/ModelDriverScheduleAuto');
		
		$jobType		=	validatePostVar($this->postVar, 'jobType', false);
		$idArea			=	validatePostVar($this->postVar, 'idArea', false);
		$scheduleDate	=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDate	=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$scheduleDateStr=	$scheduleDate->format('d M Y');
		$scheduleDate	=	$scheduleDate->format('Y-m-d');
		$dataTable		=	$this->ModelDriverScheduleAuto->getDataScheduleAuto($scheduleDate, $jobType, $idArea);
	
		if(!$dataTable) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "scheduleDateStr"=>$scheduleDateStr));
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "scheduleDateStr"=>$scheduleDateStr));	
	}
	
	public function getDataDriverList(){
		$this->load->model('Schedule/ModelDriverScheduleAuto');
		
		$scheduleDate			=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDateYtd		=	date('Y-m-d', strtotime('-1 days', strtotime($scheduleDate)));
		$scheduleDateConsNoJob	=	date('Y-m-d', strtotime('-'.$this->maxTotalConsecutiveNoJob.' days', strtotime($scheduleDate)));
		$scheduleDate			=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$scheduleDate			=	$scheduleDate->format('Y-m-d');
		$idArea					=	validatePostVar($this->postVar, 'idArea', false);
		$driverType				=	validatePostVar($this->postVar, 'driverType', false);
		$dataTable				=	$this->ModelDriverScheduleAuto->getDataDriverList($scheduleDate, $scheduleDateYtd, $driverType, $idArea);
		$totalDriverTour		=	$this->ModelDriverScheduleAuto->getTotalDriver(2);
		$totalDriverCharter		=	$this->ModelDriverScheduleAuto->getTotalDriver(3);
		$totalDriverShuttle		=	$this->ModelDriverScheduleAuto->getTotalDriver(1);
		$totalArea				=	$this->ModelDriverScheduleAuto->getTotalArea();
		$arrMaxCapacity			=	$this->ModelDriverScheduleAuto->getArrMaxCapacity();
	
		if(!$dataTable) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "totalDriverTour"=>0, "totalDriverShuttle"=>0, "totalArea"=>$totalArea, "arrMaxCapacity"=>$arrMaxCapacity));
		foreach($dataTable as $keyTable){
			$idDriver						=	$keyTable->IDDRIVER;
			$strArrLastJobRate				=	$keyTable->LASTJOBRATE;
			$arrLastJobRate					=	explode(",", $strArrLastJobRate);
			$arrLastJobRate					=	array_reverse($arrLastJobRate);
			$keyTable->TOTALCONSECUTIVENOJOB=	$this->getTotalConsecutiveNoJobDriver($idDriver, $scheduleDate, $scheduleDateConsNoJob);
			$keyTable->LASTJOBRATE			=	$arrLastJobRate;
		}
		
		setResponseOk(
			array(
				"token"				=>	$this->newToken,
				"result"			=>	$dataTable,
				"totalDriverTour"	=>	$totalDriverTour,
				"totalDriverCharter"=>	$totalDriverCharter,
				"totalDriverShuttle"=>	$totalDriverShuttle,
				"totalArea"			=>	$totalArea,
				"arrMaxCapacity"	=>	$arrMaxCapacity
			)
		);
	}
	
	private function getTotalConsecutiveNoJobDriver($idDriver, $scheduleDate, $minScheduleDate){
		$this->load->model('Schedule/ModelDriverScheduleAuto');
		
		$dataLastJobsDriver	=	$this->ModelDriverScheduleAuto->getDataDriverJobHistoryList($idDriver);
		
		if(!$dataLastJobsDriver){
			return $this->maxTotalConsecutiveNoJob;
		} else {
			$dateScheduleArray	=	[];
			$currentDateCheck	=	$minScheduleDate;

			while (strtotime($currentDateCheck) <= strtotime($scheduleDate)) {
				$dateScheduleArray[]	=	$currentDateCheck;
				$currentDateCheck		=	date("Y-m-d", strtotime($currentDateCheck . " +1 day"));
			}
			
			foreach($dataLastJobsDriver as $keyLastJobsDriver){
				$dateJobDriver			=	$keyLastJobsDriver->SCHEDULEDATEDB;
				$indexInScheduleArray	=	array_search($dateJobDriver, $dateScheduleArray);
				if ($indexInScheduleArray !== false) $dateScheduleArray[$indexInScheduleArray] = true;
			}
			
			$dateScheduleArray			=	array_reverse($dateScheduleArray);
			$totalConsecutiveNoJobDriver=	0;
			
			foreach($dateScheduleArray as $dateSchedule){
				if($dateSchedule !== true) {
					$isDayOffDriverExist	=	$this->ModelDriverScheduleAuto->isDayOffDriverExist($idDriver, $dateSchedule);
					if(!$isDayOffDriverExist)  $totalConsecutiveNoJobDriver++;
					if($isDayOffDriverExist)  break;
				} else {
					break;
				}
			}
			
			return $totalConsecutiveNoJobDriver - 1;
		}
	}
	
	public function getDataScheduleManual(){
	
		$this->load->model('Schedule/ModelDriverScheduleAuto');
		
		$scheduleDate	=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDate	=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$scheduleDateStr=	$scheduleDate->format('d M Y');
		$scheduleDate	=	$scheduleDate->format('Y-m-d');
		$dataTable		=	$this->ModelDriverScheduleAuto->getDataScheduleManual($scheduleDate);
	
		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
	public function moveScheduleToManual(){
	
		$this->load->model('MainOperation');
		
		$idReservationDetails	=	validatePostVar($this->postVar, 'idReservationDetails', true);
		$procUpdateSchedule		=	$this->MainOperation->updateData("t_reservationdetails", array("SCHEDULETYPE"=>2), "IDRESERVATIONDETAILS", $idReservationDetails);
	
		if(!$procUpdateSchedule){
			switchMySQLErrorCode($procUpdateSchedule['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken));
	
	}
	
	public function moveScheduleToAutomatic(){
	
		$this->load->model('Schedule/ModelDriverScheduleAuto');
		$this->load->model('MainOperation');
		
		$idReservationDetails	=	validatePostVar($this->postVar, 'idReservationDetails', true);
		$detailReservation		=	$this->ModelDriverScheduleAuto->getDetailReservation($idReservationDetails);
		
		if(!$detailReservation){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
		}
		
		$durationDay			=	$detailReservation['DURATIONOFDAY'];
		$totalPax				=	$detailReservation['NUMBEROFADULT'];
		$specialRequest			=	$detailReservation['SPECIALREQUEST'];
		$scheduleTypeDetails	=	$detailReservation['SCHEDULETYPE'];
		$crossSellingType		=	$detailReservation['UPSELLINGTYPE'];
		
		if($durationDay > 1){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Can't process this data. This reservation has <b>".$durationDay." days duration</b>"));
		}
		
		if($totalPax > 6){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Can't process this data. This reservation has <b>more than 6 pax</b>"));
		}
		
		if($specialRequest != "" && $specialRequest != "-"){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Can't process this data. This reservation has <b>special request : ".$specialRequest."</b>"));
		}
		
		if($crossSellingType == 1){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Can't process this data. This reservation comes from <b>cross selling</b>"));
		}
		
		$procUpdateSchedule		=	$this->MainOperation->updateData("t_reservationdetails", array("SCHEDULETYPE"=>1), "IDRESERVATIONDETAILS", $idReservationDetails);
	
		if(!$procUpdateSchedule){
			switchMySQLErrorCode($procUpdateSchedule['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken));
	
	}

	public function saveAutoSchedule(){
	
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverScheduleAuto');
		
		$arrReservationDriver	=	validatePostVar($this->postVar, 'arrReservationDriver', true);
		
		if(!is_array($arrReservationDriver)){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
		}
		
		$totalData	=	$totalSuccess	=	$totalFailed	=	0;
		foreach($arrReservationDriver as $dataReservationDriver){
			$idReservationDetails	=	$dataReservationDriver[0];
			$idDriver				=	$dataReservationDriver[1];
			$isScheduleExist		=	$this->ModelDriverScheduleAuto->isScheduleExist($idReservationDetails);
			
			if($isScheduleExist){
				$totalFailed++;
			} else {
				$statusCode			=	$this->saveDriverScheduleAPI($idReservationDetails, $idDriver, $this->newToken);
				switch($statusCode){
					case 200	:	
					case "200"	:	$totalSuccess++; break;
					default		:	$totalFailed++; break;
				}
				
			}
			
			$totalData++;
			
		}		
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Process complete. Total data : ".$totalData."<br/>Success : ".$totalSuccess."<br/>Failed : ".$totalFailed));
	
	}
	
	public function getDataDriverListManual(){
	
		$this->load->model('Schedule/ModelDriverScheduleAuto');
		
		$scheduleDate			=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDate			=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$scheduleDateStr		=	$scheduleDate->format('d M Y');
		$scheduleDate			=	$scheduleDate->format('Y-m-d');
		$idDriverType			=	validatePostVar($this->postVar, 'idDriverType', true);
		$idReservationDetails	=	validatePostVar($this->postVar, 'idReservationDetails', true);
		$detailReservation		=	$this->ModelDriverScheduleAuto->getDetailReservation($idReservationDetails);
		$dataTable				=	$this->ModelDriverScheduleAuto->getDataDriverListManual($scheduleDate, $idDriverType);
	
		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "scheduleDateStr"=>$scheduleDateStr));
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "scheduleDateStr"=>$scheduleDateStr, "detailReservation"=>$detailReservation));
	
	}
	
	public function getDataDriverJobHistoryList(){
	
		$this->load->model('Schedule/ModelDriverScheduleAuto');
		
		$idDriver		=	validatePostVar($this->postVar, 'idDriver', true);
		$dataHistory	=	$this->ModelDriverScheduleAuto->getDataDriverJobHistoryList($idDriver);
	
		if(!$dataHistory){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No job history found for this driver"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "dataHistory"=>reverseQueryResult($dataHistory)));
	
	}
	
}