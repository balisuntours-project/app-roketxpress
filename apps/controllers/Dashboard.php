<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_controller {
	
	public function __construct(){
        parent::__construct();
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->postVar	=	decodeJsonPost();
			$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array());
			$this->newToken	=	isLoggedIn($this->token, true);
		}
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataDashboard(){
		
		$this->load->model('ModelDashboard');
		$this->load->model('MainOperation');
		
		$month				=	validatePostVar($this->postVar, 'month', true);
		$year				=	validatePostVar($this->postVar, 'year', true);
		$yearMonth			=	$year."-".$month;
		$firstDateYearMonth	=	$year."-".$month."-01";
		$lastDateYearMonth	=	date('Y-m-t', strtotime($firstDateYearMonth));
		$lastYearMonth		=	date("Y-m", strtotime('-1 month', strtotime($firstDateYearMonth)));
		$minReservationDate	=	"2022-04-01";
		$dataReservation	=	$this->ModelDashboard->getDataTotalReservation($yearMonth, $lastYearMonth);
		
		if($dataReservation){
			$dataReservation['PERCENTAGETHISMONTH']		=	0;
			$dataReservation['PERCENTAGETHISMONTHSTYLE']=	0;
			$dataReservation['PERCENTAGETODAY']			=	0;
			$dataReservation['PERCENTAGETOMORROW']		=	0;
			$totalReservationThisMonth					=	$dataReservation['TOTALRESERVATIONTHISMONTH'];
		}
		
		if($totalReservationThisMonth > 0){
			$dataReservation['PERCENTAGETHISMONTH']		=	$dataReservation['TOTALRESERVATIONLASTMONTH'] == 0 ? 0 : number_format($totalReservationThisMonth / $dataReservation['TOTALRESERVATIONLASTMONTH'] * 100, 0, '.', ',');
			$dataReservation['PERCENTAGETHISMONTHSTYLE']=	$dataReservation['PERCENTAGETHISMONTH'] > 100 ? 100 : $dataReservation['PERCENTAGETHISMONTH'];
			$dataReservation['PERCENTAGETODAY']			=	$totalReservationThisMonth == 0 ? 0 : number_format($dataReservation['TOTALRESERVATIONTODAY'] / $totalReservationThisMonth * 100, 0, '.', ',');
			$dataReservation['PERCENTAGETOMORROW']		=	$totalReservationThisMonth == 0 ? 0 : number_format($dataReservation['TOTALRESERVATIONTOMORROW'] / $totalReservationThisMonth * 100, 0, '.', ',');
			$minReservationDate							=	$dataReservation['MINRESERVATIONDATE'];
		}

		$year1				=	date('Y', strtotime($minReservationDate));
		$year2				=	date('Y', strtotime($firstDateYearMonth));
		$month1				=	date('m', strtotime($minReservationDate));
		$month2				=	date('m', strtotime($firstDateYearMonth));
		$totalMonth			=	(($year2 - $year1) * 12) + ($month2 - $month1);
		$dataSource			=	$this->ModelDashboard->getDataSourceReservation($yearMonth, $totalMonth, $lastDateYearMonth);
		$dataStatistic		=	$this->getDataStatistic($yearMonth);
		
		setResponseOk(
			array(
				"token"				=>	$this->newToken,
				"lastYearMonth"		=>	$lastYearMonth,
				"dataReservation"	=>	$dataReservation,
				"dataSource"		=>	$dataSource,
				"dataStatistic"		=>	$dataStatistic,
				"minReservationDate"=>	$minReservationDate,
				"totalMonth"		=>	$totalMonth
			)
		);
		
	}
	
	private function getDataStatistic($yearMonth){
		
		$firstDate			=	$yearMonth."-01";
		$lastDate			=	date("Y-m-t", strtotime($firstDate));
		$totalDays			=	date("t", strtotime($firstDate));
		$dataProductType	=	$this->ModelDashboard->getDataProductType();
		$dataGraphProduct	=	$this->ModelDashboard->getDataGraphProduct($yearMonth);
		$arrDates			=	$arrDatesCheck	=	$arrDetailData	=	$arrDataPerProduct	=	array();
		$colors				=	array(	'#4dc9f6',
										'#f67019',
										'#f53794',
										'#537bc4',
										'#acc236',
										'#166a8f',
										'#00a950',
										'#58595b',
										'#8549ba');
		
		for($i=0; $i<$totalDays; $i++){
			$dateCheck		=	date('Y-m-d', strtotime('+'.$i.' day', strtotime($firstDate)));
			$dateStr		=	date('d', strtotime('+'.$i.' day', strtotime($firstDate)));
			
			$arrDates[]		=	$dateStr;
			$arrDatesCheck[]=	$dateCheck;
			
			foreach($dataProductType as $keyProductType){
				$arrDataPerProduct[$keyProductType->IDPRODUCTTYPE][]	=	0;
			}
			
		}
		
		if($dataGraphProduct){
			
			foreach($dataGraphProduct as $keyGraphProduct){
				$dateCheckDB=	$keyGraphProduct->RESERVATIONDATESTART;
				$index		=	array_search($dateCheckDB, $arrDatesCheck);
				
				$arrDataPerProduct[$keyGraphProduct->IDPRODUCTTYPE][$index]	=	$keyGraphProduct->TOTALRESERVATION;
			}
			
		}
		
		$idxColors		=	0;
		foreach($dataProductType as $keyProductType){
			
			$arrDetailData[]	=	array(
											"label"			=>	$keyProductType->PRODUCTTYPE,
											"data"			=>	$arrDataPerProduct[$keyProductType->IDPRODUCTTYPE],
											"borderColor"	=>	$colors[$idxColors],
											"borderWidth"	=>	3,
											"fill"			=>	false,
											"lineTension"	=>	0.3
										);
			$idxColors++;
			
		}
		
		return array(
						"arrDates"		=>	$arrDates,
						"arrDetailData"	=>	$arrDetailData
					);
		
	}
	
}