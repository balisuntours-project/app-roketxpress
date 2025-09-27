<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReservationDetail extends CI_controller {
	
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
	
	public function getDataReservationDetail(){

		$this->load->model('Report/ModelReservationDetail');
		$this->load->model('MainOperation');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$reservationType=	validatePostVar($this->postVar, 'reservationType', false);
		$startDate		=	validatePostVar($this->postVar, 'startDate', true);
		$endDate		=	validatePostVar($this->postVar, 'endDate', true);
		$startDateDT	=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate		=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate		=	$startDate->format('Y-m-d');
		$endDateDT		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate		=	$endDate->format('Y-m-d');
		$arrDates		=	getArrDateBetween($startDateDT, $endDateDT);
		$dataTable		=	$this->ModelReservationDetail->getDataReservationDetail($page, 25, $reservationType, $arrDates, $startDate, $endDate);
		
		if($dataTable['dataTotal'] > 0){
			foreach($dataTable['data'] as $keyDataTable){
				$idReservation		=	$keyDataTable->IDRESERVATION;
				$dataCarSchedule	=	$this->ModelReservationDetail->getDataCarSchedule($idReservation);
				$dataDriverSchedule	=	$this->ModelReservationDetail->getDataDriverSchedule($idReservation);
				$dataTicketList		=	$this->ModelReservationDetail->getDataTicketList($idReservation);
				
				if($dataCarSchedule) $keyDataTable->CARSCHEDULE			=	$dataCarSchedule;
				if($dataDriverSchedule) $keyDataTable->DRIVERSCHEDULE	=	$dataDriverSchedule;
				if($dataTicketList) $keyDataTable->TICKETLIST			=	$dataTicketList;
			}
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
}