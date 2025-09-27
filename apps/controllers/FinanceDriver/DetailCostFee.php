<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class DetailCostFee extends CI_controller {
	
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
	
	public function getDataDetailCostFee(){

		$this->load->model('FinanceDriver/ModelDetailCostFee');
		$this->load->model('MainOperation');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$idDriverType	=	validatePostVar($this->postVar, 'idDriverType', false);
		$idDriver		=	validatePostVar($this->postVar, 'idDriver', false);
		$startDate		=	validatePostVar($this->postVar, 'startDate', true);
		$endDate		=	validatePostVar($this->postVar, 'endDate', true);
		$startDateDT	=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDateTS	=	$startDateDT->getTimestamp();
		$startDate		=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate		=	$startDate->format('Y-m-d');
		$endDateDT		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDateTS		=	$endDateDT->getTimestamp();
		$endDate		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate		=	$endDate->format('Y-m-d');
		$arrDates		=	getArrDateBetween($startDateDT, $endDateDT);
		$totalDays		=	$startDateDT->diff($endDateDT)->days;

		if($endDateTS < $startDateTS){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid date range selection"));
		}
		
		if($totalDays > 31){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Maximum date range is <b>31</b> days"));
		}

		$dataTable			=	$this->ModelDetailCostFee->getDataDetailCostFee($page, 25, $idDriverType, $idDriver, $arrDates, $startDate, $endDate);
		$urlExcelDetailFee	=	"";
		if($dataTable['dataTotal'] > 0){
			$urlExcelDetailFee	=	BASE_URL."financeDriver/detailCostFee/excelDetailCostFee/".base64_encode(encodeStringKeyFunction($idDriverType."|".$idDriver."|".$startDate."|".$endDate, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
			foreach($dataTable['data'] as $keyDataTable){
				$idReservation	=	$keyDataTable->IDRESERVATION;
				$idDriver		=	$keyDataTable->IDDRIVER;
				$arrJobDetails=	json_decode("[".$keyDataTable->JOBDETAILS."]");
				
				if($idDriver != 0 && $idDriver != ""){
					$detailCost		=	$this->ModelDetailCostFee->getDetailCost($idReservation, $idDriver);
					
					if($detailCost){
						$totalCost	=	0;
						
						foreach($detailCost as $keyDetailCost){
							$totalCost	+=	$keyDetailCost->NOMINAL;
						}
						
						$keyDataTable->COSTDETAIL	=	$detailCost;
						$keyDataTable->TOTALCOST	=	$totalCost;
					}
				}

				$keyDataTable->JOBDETAILS	=	$arrJobDetails;
				
			}
		}
		
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelDetailFee"=>$urlExcelDetailFee));
	
	}
	
	public function excelDetailCostFee($encryptedVar){

		$this->load->model('FinanceDriver/ModelDetailCostFee');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$idDriverType	=	$expDecryptedVar[0];
		$idDriver		=	$expDecryptedVar[1];
		$startDate		=	$expDecryptedVar[2];
		$endDate		=	$expDecryptedVar[3];
		$startDateDT	=	DateTime::createFromFormat('Y-m-d', $startDate);
		$startDateStr	=	$startDateDT->format('d M Y');
		$endDateDT		=	DateTime::createFromFormat('Y-m-d', $endDate);
		$endDateStr		=	$endDateDT->format('d M Y');
		$arrDates		=	getArrDateBetween($startDateDT, $endDateDT);
		$totalDays		=	$startDateDT->diff($endDateDT)->days;

		$driverType		=	isset($idDriverType) && $idDriverType != "" && $idDriverType != 0 ? $this->MainOperation->getDriverTypeById($idDriverType) : "All Driver Type";
		$driverName		=	isset($idDriver) && $idDriver != "" && $idDriver != 0 ? $this->MainOperation->getDriverNameById($idDriver) : "All Driver";
		$dataTable		=	$this->ModelDetailCostFee->getDataDetailCostFee(1, 999999, $idDriverType, $idDriver, $arrDates, $startDate, $endDate);
		
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
		$sheet->setCellValue('A2', 'Detail Driver Fee + Cost');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:N1');
		$sheet->mergeCells('A2:N2');
		
		$sheet->setCellValue('A4', 'Driver Type : '.$driverType);					$sheet->mergeCells('A4:N4');
		$sheet->setCellValue('A5', 'Driver Name : '.$driverName);					$sheet->mergeCells('A5:N5');
		$sheet->setCellValue('A6', 'Period : '.$startDateStr.' To '.$endDateStr);	$sheet->mergeCells('A6:N6');
		
		$sheet->setCellValue('A8', 'Driver Name');		$sheet->mergeCells('A8:A9');
		$sheet->setCellValue('B8', 'Date Reservation');	$sheet->mergeCells('B8:B9');
		$sheet->setCellValue('C8', 'Source');			$sheet->mergeCells('C8:C9');
		$sheet->setCellValue('D8', 'Customer');			$sheet->mergeCells('D8:D9');
		$sheet->setCellValue('E8', 'Booking Code');		$sheet->mergeCells('E8:E9');
		$sheet->setCellValue('F8', 'Reservation Title');$sheet->mergeCells('F8:F9');
		$sheet->setCellValue('G8', 'Job Details');		$sheet->mergeCells('G8:J8');
		$sheet->setCellValue('K8', 'Cost Details');		$sheet->mergeCells('K8:M8');
		$sheet->setCellValue('N8', 'Total Fee + Cost');	$sheet->mergeCells('N8:N9');
		
		$sheet->setCellValue('G9', 'Date');
		$sheet->setCellValue('H9', 'Job Title');
		$sheet->setCellValue('I9', 'Withdraw Status');
		$sheet->setCellValue('J9', 'Fee');
		
		$sheet->setCellValue('K9', 'Cost Type');
		$sheet->setCellValue('L9', 'Description');
		$sheet->setCellValue('M9', 'Nominal');
		
		$sheet->getStyle('A8:N9')->getFont()->setBold( true );
		$sheet->getStyle('A8:N9')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A8:N9')->getAlignment()->setVertical('center');
		$rowNumber	=	10;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$grandTotalFee = $grandTotalAdditionalCost = $grandTotalAll	=	0;
		foreach($dataTable['data'] as $data){
			
			$arrJobDetails	=	json_decode("[".$data->JOBDETAILS."]");
			$totalRowFee	=	$totalRowAdditionalCost	=	0;
			$inputType		=	'';
			switch($data->INPUTTYPE){
				case "1"	:	$inputType	=	'Mailbox'; break;
				case "2"	:	$inputType	=	'Manual'; break;
			}
			
			$sheet->setCellValue('A'.$rowNumber, $data->DRIVERTYPE." - ".$data->DRIVERNAME);
			$sheet->setCellValue('B'.$rowNumber, "[".$data->DURATIONOFDAY." Days] ".$data->RESERVATIONDATESTART);
			$sheet->setCellValue('C'.$rowNumber, "[".$inputType."] ".$data->SOURCENAME);
			$sheet->setCellValue('D'.$rowNumber, $data->CUSTOMERNAME);
			$sheet->setCellValue('E'.$rowNumber, $data->BOOKINGCODE);
			$sheet->setCellValue('F'.$rowNumber, $data->RESERVATIONTITLE);
			
			if(is_array($arrJobDetails) && count($arrJobDetails) > 0){
				$grandTotalPerRow		=	0;
				foreach($arrJobDetails as $arrJobDetail){
					
					$totalFeeDetail		=	$arrJobDetail[2];
					$withdrawStatus		=	"";
					switch($arrJobDetail[3]){
						case "0"	:	
						case 0		:	$withdrawStatus	=	"Balanced"; break;
						case "1"	:	
						case 1		:	$withdrawStatus	=	"Withdrawn"; break;
					}
					
					$sheet->setCellValue('G'.($rowNumber + $totalRowFee), $arrJobDetail[0]);$sheet->getStyle('G'.($rowNumber + $totalRowFee))->getAlignment()->setHorizontal('center');
					$sheet->setCellValue('H'.($rowNumber + $totalRowFee), $arrJobDetail[1]);
					$sheet->setCellValue('I'.($rowNumber + $totalRowFee), $withdrawStatus);
					$sheet->setCellValue('J'.($rowNumber + $totalRowFee), $totalFeeDetail);	$sheet->getStyle('J'.($rowNumber + $totalRowFee))->getAlignment()->setHorizontal('right');
					
					$grandTotalPerRow	+=	str_replace(",", "", $totalFeeDetail);
					$grandTotalFee		+=	str_replace(",", "", $totalFeeDetail);
					$totalRowFee++;
					
				}
			}
			
			$idReservation	=	$data->IDRESERVATION;
			$idDriver		=	$data->IDDRIVER;
			if($idDriver != 0 && $idDriver != ""){
				$detailCost		=	$this->ModelDetailCostFee->getDetailCost($idReservation, $idDriver);
				if($detailCost){
					foreach($detailCost as $keyDetailCost){
						$sheet->setCellValue('K'.($rowNumber + $totalRowAdditionalCost), $keyDetailCost->ADDITIONALCOSTTYPE);
						$sheet->setCellValue('L'.($rowNumber + $totalRowAdditionalCost), $keyDetailCost->DESCRIPTION);
						$sheet->setCellValue('M'.($rowNumber + $totalRowAdditionalCost), $keyDetailCost->NOMINAL);
						
						$grandTotalPerRow			+=	$keyDetailCost->NOMINAL;
						$grandTotalAdditionalCost	+=	$keyDetailCost->NOMINAL;
						$totalRowAdditionalCost++;
					}
				}
			}
			
			$sheet->setCellValue('N'.$rowNumber, $grandTotalPerRow);	$sheet->getStyle('N'.$rowNumber)->getAlignment()->setHorizontal('right');
			
			$totalMergeRow	=	$totalRowFee > $totalRowAdditionalCost ? $totalRowFee : $totalRowAdditionalCost;
			if($totalMergeRow > 1){
				$sheet->mergeCells('A'.$rowNumber.':A'.($rowNumber + $totalMergeRow - 1));
				$sheet->mergeCells('B'.$rowNumber.':B'.($rowNumber + $totalMergeRow - 1));
				$sheet->mergeCells('C'.$rowNumber.':C'.($rowNumber + $totalMergeRow - 1));
				$sheet->mergeCells('D'.$rowNumber.':D'.($rowNumber + $totalMergeRow - 1));
				$sheet->mergeCells('E'.$rowNumber.':E'.($rowNumber + $totalMergeRow - 1));
				$sheet->mergeCells('F'.$rowNumber.':F'.($rowNumber + $totalMergeRow - 1));
				$sheet->mergeCells('N'.$rowNumber.':N'.($rowNumber + $totalMergeRow - 1));
			}
			
			$grandTotalAll	+=	$grandTotalPerRow;
			$rowNumber		+=	$totalMergeRow;
			
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':F'.$rowNumber);
		$sheet->mergeCells('G'.$rowNumber.':J'.$rowNumber);
		$sheet->mergeCells('K'.$rowNumber.':M'.$rowNumber);
		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('G'.$rowNumber, $grandTotalFee);			$sheet->getStyle('G'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('G'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('K'.$rowNumber, $grandTotalAdditionalCost);$sheet->getStyle('K'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('K'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('N'.$rowNumber, $grandTotalAll);			$sheet->getStyle('N'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('N'.$rowNumber)->getFont()->setBold( true );
		
		$sheet->getStyle('A8:N'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('N'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(22);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(25);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(35);
		$sheet->getColumnDimension('G')->setWidth(12);
		$sheet->getColumnDimension('H')->setWidth(25);
		$sheet->getColumnDimension('I')->setWidth(16);
		$sheet->getColumnDimension('J')->setWidth(10);
		$sheet->getColumnDimension('K')->setWidth(20);
		$sheet->getColumnDimension('L')->setWidth(30);
		$sheet->getColumnDimension('M')->setWidth(10);
		$sheet->getColumnDimension('N')->setWidth(14);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDetailDriverFee_'.$driverType.'_'.$driverName.'_'.$startDate.'_'.$endDate;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}
	
}