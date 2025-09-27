<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ScheduleDriverMonitor extends CI_controller {
	
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
	
	public function getDataScheduleDriverMonitor(){

		$this->load->model('Schedule/ModelScheduleDriverMonitor');
		$this->load->model('MainOperation');
		
		$month			=	validatePostVar($this->postVar, 'month', true);
		$year			=	validatePostVar($this->postVar, 'year', true);
		$monthYear		=	$year."-".$month;
		$dataMonitor	=	$this->ModelScheduleDriverMonitor->getDataScheduleDriverMonitor($monthYear);
		$dataTable		=	array();
		$dateStart		=	DateTime::createFromFormat('Y-m-d', $monthYear."-01");
		$totalDate		=	$dateStart->format('t');
		
		for($i=0; $i<$totalDate; $i++){
			$dateStart	=	DateTime::createFromFormat('Y-m-d', $monthYear."-01");
			$iDateStr	=	$dateStart->modify('+'.$i.' day')->format('d M Y');
			$dateCheck	=	$dateStart->modify('+'.$i.' day')->format('Ymd');
			$statusDate	=	$dateCheck >= date('Ymd') ? 1 : 0;
			$dataTable[]=	array(
								"IDSCHEDULEDRIVERMONITOR"	=>	0,
								"DATESCHEDULESTR"			=>	$iDateStr,
								"TOTALSCHEDULE"				=>	0,
								"TOTALACTIVEDRIVER"			=>	0,
								"TOTALOFFDRIVER"			=>	0,
								"TOTALDAYOFFQUOTA"			=>	0,
								"STATUS"					=>	0,
								"STATUSDATE"				=>	$statusDate
							);
		}
		
		foreach($dataMonitor as $keyMonitor){
			$indexDate				=	substr($keyMonitor->DATESCHEDULE, -2) * 1 - 1;
			$dateScheduleCheck		=	DateTime::createFromFormat('Y-m-d', $keyMonitor->DATESCHEDULE);
			$dateScheduleCheck		=	$dateScheduleCheck->format('Ymd');
			$statusDate				=	$dateScheduleCheck >= date('Ymd') ? 1 : 0;
			$dataTable[$indexDate]	=	array(
											"IDSCHEDULEDRIVERMONITOR"	=>	$keyMonitor->IDSCHEDULEDRIVERMONITOR,
											"DATESCHEDULESTR"			=>	$keyMonitor->DATESCHEDULESTR,
											"TOTALSCHEDULE"				=>	$keyMonitor->TOTALSCHEDULE,
											"TOTALACTIVEDRIVER"			=>	$keyMonitor->TOTALACTIVEDRIVER,
											"TOTALOFFDRIVER"			=>	$keyMonitor->TOTALOFFDRIVER,
											"TOTALDAYOFFQUOTA"			=>	$keyMonitor->TOTALDAYOFFQUOTA,
											"STATUS"					=>	$keyMonitor->STATUS,
											"STATUSDATE"				=>	$statusDate
										);
		}	
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
	public function setDayOffQuotaPerDate(){

		$this->load->model('MainOperation');
		
		$idScheduleDriverMonitor=	validatePostVar($this->postVar, 'idScheduleDriverMonitor', true);
		$dayOffQuota			=	str_replace(",", "", validatePostVar($this->postVar, 'dayOffQuota', true));
		$arrUpdate				=	array(
											"TOTALDAYOFFQUOTA"	=>	$dayOffQuota
									);
		$procUpdate				=	$this->MainOperation->updateData("t_scheduledrivermonitor", $arrUpdate, "IDSCHEDULEDRIVERMONITOR", $idScheduleDriverMonitor);
			
		if(!$procUpdate['status']){
			switchMySQLErrorCode($procUpdate['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Day off quota has been updated"));

	}
	
}