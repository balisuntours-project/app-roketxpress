<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReviewBonusPunishment extends CI_controller {
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->postVar	=	decodeJsonPost();
			$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid submission data"));
			$this->newToken	=	isLoggedIn($this->token, true);
		}
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataAllDriverReport(){
		$this->load->model('FinanceDriver/ModelReviewBonusPunishment');

		$page			=	validatePostVar($this->postVar, 'page', true);
		$idDriverType	=	validatePostVar($this->postVar, 'idDriverType', false);
		$idDriver		=	validatePostVar($this->postVar, 'idDriver', false);
		$month			=	validatePostVar($this->postVar, 'month', true);
		$year			=	validatePostVar($this->postVar, 'year', true);
		$dataTable		=	$this->ModelReviewBonusPunishment->getDataAllDriverReport($page, 25, $idDriverType, $idDriver, $month, $year);
		$urlExcelReport	=	"";
		
		if($dataTable['dataTotal'] > 0){
			$urlExcelReport	=	BASE_URL."financeDriver/reviewBonusPunishment/excelDataAllDriverReport/".base64_encode(encodeStringKeyFunction($idDriverType."|".$idDriver."|".$month."|".$year, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelReport"=>$urlExcelReport));
	}
	
	public function excelDataAllDriverReport($encryptedVar){
		$this->load->model('FinanceDriver/ModelReviewBonusPunishment');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$idDriverType	=	$expDecryptedVar[0];
		$idDriver		=	$expDecryptedVar[1];
		$month			=	$expDecryptedVar[2];
		$year			=	$expDecryptedVar[3];
		$driverType		=	isset($idDriverType) && $idDriverType != "" && $idDriverType != 0 ? $this->MainOperation->getDriverTypeById($idDriverType) : "All Driver Type";
		$driverName		=	isset($idDriver) && $idDriver != "" && $idDriver != 0 ? $this->MainOperation->getDriverNameById($idDriver) : "All Driver";
		$dataTable		=	$this->ModelReviewBonusPunishment->getDataAllDriverReport(1, 999999, $idDriverType, $idDriver, $month, $year);
		
		if(!$dataTable){
			echo "No data found!";
			die();
		}
		
		$spreadsheet	=	new Spreadsheet();
		$sheet			=	$spreadsheet->getActiveSheet();
		
		$sheet->getPageSetup()->setOrientation(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(0.25);
		$sheet->getPageMargins()->setRight(0.2);
		$sheet->getPageMargins()->setLeft(0.2);
		$sheet->getPageMargins()->setBottom(0.25);
		
		$sheet->setCellValue('A1', 'Bali Sun Tours');
		$sheet->setCellValue('A2', 'Review Bonus & Punishment Recap');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:K1');
		$sheet->mergeCells('A2:K2');
		
		$sheet->setCellValue('A4', 'Driver Type : '.$driverType);	$sheet->mergeCells('A4:K4');
		$sheet->setCellValue('A5', 'Driver Name : '.$driverName);	$sheet->mergeCells('A5:K5');
		$sheet->setCellValue('A6', 'Period : '.$month.' - '.$year);	$sheet->mergeCells('A6:K6');
		
		$sheet->setCellValue('A8', 'Driver Type');
		$sheet->setCellValue('B8', 'Driver Name');
		$sheet->setCellValue('C8', 'Period Start');
		$sheet->setCellValue('D8', 'Period End');
		$sheet->setCellValue('E8', 'Target');		$sheet->getStyle('E8')->getAlignment()->setHorizontal('right');
		$sheet->setCellValue('F8', 'Review Point');	$sheet->getStyle('F8')->getAlignment()->setHorizontal('right');
		$sheet->setCellValue('G8', 'Rate');			$sheet->getStyle('G8')->getAlignment()->setHorizontal('right');
		$sheet->setCellValue('H8', 'Bonus');		$sheet->getStyle('H8')->getAlignment()->setHorizontal('right');
		$sheet->setCellValue('I8', 'Punishment');	$sheet->getStyle('I8')->getAlignment()->setHorizontal('right');
		$sheet->setCellValue('J8', 'Result');		$sheet->getStyle('J8')->getAlignment()->setHorizontal('right');
		$sheet->setCellValue('K8', 'Status WD');
		
		$sheet->getStyle('A8:K8')->getFont()->setBold( true );
		$rowNumber	=	9;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$grandTotalTarget = $grandTotalReviewPoint = $grandTotalBonus=	$grandTotalPunishment	=	$grandTotalResult	=	0;
		foreach($dataTable['data'] as $data){
			$statusWithdrawal	=	'';
			switch($data->STATUSWITHDRAWAL){
				case "0"	:	$statusWithdrawal	=	'Hold'; break;
				case "1"	:	$statusWithdrawal	=	'Withdrawn'; break;
				default		:	$statusWithdrawal	=	'-'; break;
			}
					
			$sheet->setCellValue('A'.$rowNumber, $data->DRIVERTYPE);
			$sheet->setCellValue('B'.$rowNumber, $data->DRIVERNAME);
			$sheet->setCellValue('C'.$rowNumber, $data->PERIODDATESTART);
			$sheet->setCellValue('D'.$rowNumber, $data->PERIODDATEEND);
			$sheet->setCellValue('E'.$rowNumber, $data->TOTALTARGET);
			$sheet->setCellValue('F'.$rowNumber, $data->TOTALREVIEWPOINT);
			$sheet->setCellValue('G'.$rowNumber, $data->BONUSRATE);
			$sheet->setCellValue('H'.$rowNumber, $data->NOMINALBONUS);
			$sheet->setCellValue('I'.$rowNumber, $data->NOMINALPUNISHMENT);
			$sheet->setCellValue('J'.$rowNumber, $data->NOMINALRESULT);
			$sheet->setCellValue('K'.$rowNumber, $statusWithdrawal);
			
			$grandTotalTarget		+=	$data->TOTALTARGET;
			$grandTotalReviewPoint	+=	$data->TOTALREVIEWPOINT;
			$grandTotalBonus		+=	$data->NOMINALBONUS;
			$grandTotalPunishment	+=	$data->NOMINALPUNISHMENT;
			$grandTotalResult		+=	$data->NOMINALRESULT;
			$rowNumber++;
			
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL'); $sheet->mergeCells('A'.$rowNumber.':D'.$rowNumber);
		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('E'.$rowNumber, $grandTotalTarget);		$sheet->getStyle('E'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('E'.$rowNumber)->getFont()->setBold(true);
		$sheet->setCellValue('F'.$rowNumber, $grandTotalReviewPoint);	$sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('F'.$rowNumber)->getFont()->setBold(true);
		$sheet->setCellValue('H'.$rowNumber, $grandTotalBonus);			$sheet->getStyle('H'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('H'.$rowNumber)->getFont()->setBold(true);
		$sheet->setCellValue('I'.$rowNumber, $grandTotalPunishment);	$sheet->getStyle('I'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('I'.$rowNumber)->getFont()->setBold(true);
		$sheet->setCellValue('J'.$rowNumber, $grandTotalResult);		$sheet->getStyle('J'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('J'.$rowNumber)->getFont()->setBold(true);
		
		$sheet->getStyle('A8:K'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('K'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(14);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(16);
		$sheet->getColumnDimension('D')->setWidth(16);
		$sheet->getColumnDimension('E')->setWidth(12);
		$sheet->getColumnDimension('F')->setWidth(12);
		$sheet->getColumnDimension('G')->setWidth(12);
		$sheet->getColumnDimension('H')->setWidth(14);
		$sheet->getColumnDimension('I')->setWidth(14);
		$sheet->getColumnDimension('J')->setWidth(14);
		$sheet->getColumnDimension('K')->setWidth(13);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelReviewBonusPunishment_'.$driverType.'_'.$driverName.'_'.$year.'_'.$month;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
	
	public function updateTargetReviewPointDriver(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelReviewBonusPunishment');

		$idDriverReviewBonus	=	validatePostVar($this->postVar, 'idDriverReviewBonus', true);
		$totalTarget			=	validatePostVar($this->postVar, 'totalTarget', false);
		$dataBonusPeriodTarget	=	$this->ModelReviewBonusPunishment->getReviewBonusPeriodTarget($idDriverReviewBonus);
		$reviewBonusPeriodTarget=	$dataBonusPeriodTarget['TOTALTARGET'];
		$targetException		=	-1;
		
		if($totalTarget != $reviewBonusPeriodTarget) $targetException	=	$totalTarget;
		$procUpdateReviewBonus	=	$this->MainOperation->updateData('t_driverreviewbonus', ['TARGETEXCEPTION' => $targetException], 'IDDRIVERREVIEWBONUS', $idDriverReviewBonus);
		
		if(!$procUpdateReviewBonus['status']) setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed to update driver review point target. Please try again later"));
		
		$targetBonusPeriodFinal		=	$targetException != -1 ? $totalTarget : $reviewBonusPeriodTarget;
		$totalReviewPoint			=	$dataBonusPeriodTarget['TOTALREVIEWPOINT'];
		$rateBonusPeriod			=	$dataBonusPeriodTarget['BONUSRATE'];
		$nominalBonus				=	$totalReviewPoint * $rateBonusPeriod;
		$totalTargetPunishment		=	$targetBonusPeriodFinal - $totalReviewPoint;
		$nominalPunishment			=	$totalTargetPunishment <= 0 ? 0 : $totalTargetPunishment * $rateBonusPeriod;
		$arrUpdateDriverReviewBonus	=	[
			"NOMINALBONUS"		=>	$nominalBonus,
			"NOMINALPUNISHMENT"	=>	$nominalPunishment,
			"NOMINALRESULT"		=>	($nominalBonus - $nominalPunishment)
		];
		
		$this->MainOperation->updateData('t_driverreviewbonus', $arrUpdateDriverReviewBonus, 'IDDRIVERREVIEWBONUS', $idDriverReviewBonus);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data target review point driver has been updated"));
	}	

	public function getDataPeriodTargetRate(){
		$this->load->model('FinanceDriver/ModelReviewBonusPunishment');

		$year		=	validatePostVar($this->postVar, 'year', true);
		$dataTable	=	$this->ModelReviewBonusPunishment->getDataPeriodTargetRate(1, 25, $year);
		$totalData	=	0;
		
		if(count($dataTable['data']) > 0){
			foreach($dataTable['data'] as $data){
				$totalData++;
			}
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "totalData"=>$totalData));
	}
	
	public function savePeriodTargetRate(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelReviewBonusPunishment');

		$idDriverReviewBonusPeriod	=	validatePostVar($this->postVar, 'formPeriodTargetRate-idDriverReviewBonusPeriod', true);
		$periodMonthYear			=	validatePostVar($this->postVar, 'formPeriodTargetRate-periodMonthYear', true);
		$isLastPeriod				=	validatePostVar($this->postVar, 'formPeriodTargetRate-isLastPeriod', false);
		$isLastPeriod				=	$isLastPeriod == "false" ? false : true;
		$datePeriodEnd				=	validatePostVar($this->postVar, 'formPeriodTargetRate-datePeriodEnd', true);
		$originDatePeriodEnd		=	validatePostVar($this->postVar, 'formPeriodTargetRate-originDatePeriodEnd', true);
		$totalTarget				=	validatePostVar($this->postVar, 'formPeriodTargetRate-totalTarget', true);
		$rateBonusPunishment		=	validatePostVar($this->postVar, 'formPeriodTargetRate-rateBonusPunishment', true);
		$rateBonusPunishment		=	str_replace(",", "", $rateBonusPunishment) * 1;
		$isReviewBonusWithdrawn		=	$this->ModelReviewBonusPunishment->isReviewBonusWithdrawn($idDriverReviewBonusPeriod);
		
		if($isReviewBonusWithdrawn){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Failed. Cannot change Period - Target - Rate data that has been withdrawn by the driver"));
		} else {
			$datePeriodEnd				=	DateTime::createFromFormat('d-m-Y', $datePeriodEnd);
			$datePeriodEnd				=	$datePeriodEnd->format('Y-m-d');
			$originDatePeriodEnd		=	DateTime::createFromFormat('d-m-Y', $originDatePeriodEnd);
			$originDatePeriodEnd		=	$originDatePeriodEnd->format('Y-m-d');
			$arrUpdateReviewBonusPeriod	=	[
				"PERIODDATEEND"		=>	$datePeriodEnd,
				"BONUSRATE"			=>	$rateBonusPunishment,
				"TOTALTARGET"		=>	$totalTarget
			];
			
			$procUpdateBonusPeriod	=	$this->MainOperation->updateData('t_driverreviewbonusperiod', $arrUpdateReviewBonusPeriod, 'IDDRIVERREVIEWBONUSPERIOD', $idDriverReviewBonusPeriod);
			if(!$procUpdateBonusPeriod['status']){
				switchMySQLErrorCode($procUpdateBonusPeriod['errCode'], $this->newToken);
			}
			
			$arrIdDriverReviewBonusPeriod	=	[$idDriverReviewBonusPeriod];
			$dataNextDriverReviewBonusPeriod=	$this->ModelReviewBonusPunishment->getDataNextDriverReviewBonusPeriod($periodMonthYear);
			$nextIdDriverReviewBonusPeriod	=	0;
			
			if($dataNextDriverReviewBonusPeriod){
				$nextIdDriverReviewBonusPeriod	=	$dataNextDriverReviewBonusPeriod['IDDRIVERREVIEWBONUSPERIOD'];
				$arrIdDriverReviewBonusPeriod[]	=	$nextIdDriverReviewBonusPeriod;
			}
			
			if($originDatePeriodEnd != $datePeriodEnd && !$isLastPeriod){
				$nextPeriodDatePeriodStart	=	DateTime::createFromFormat('Y-m-d', $datePeriodEnd);
				$nextPeriodDatePeriodStart	=	$nextPeriodDatePeriodStart->modify('+1 day')->format('Y-m-d');
				$arrUpdateNextPeriod		=	["PERIODDATESTART" => $nextPeriodDatePeriodStart];
				
				$this->MainOperation->updateData('t_driverreviewbonusperiod', $arrUpdateNextPeriod, 'IDDRIVERREVIEWBONUSPERIOD', $nextIdDriverReviewBonusPeriod);
			}
			
			$arrData				=	["arrIdDriverReviewBonusPeriod" => $arrIdDriverReviewBonusPeriod];
			$base64JsonData			=	base64_encode(json_encode($arrData));
			$urlAPICalculateReview	=	BASE_URL."schedule/driverRatingPoint/apiCalculateBonusPunishmentReview/".$base64JsonData;
			
			try {
				$resAPICronCalculateReview	=	json_decode(trim(curl_get_file_contents($urlAPICalculateReview)));
				setResponseOk(array("token"=>$this->newToken, "msg"=>"Data Period - Target - Rate has been updated"));
			} catch(Exception $e) {
				setResponseOk(array("token"=>$this->newToken, "msg"=>"Data Period - Target - Rate has been updated. Please refresh data review bonus & punishment"));
			}
		}
	}	
}