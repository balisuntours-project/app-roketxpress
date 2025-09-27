<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RecapPerDate extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			parent::__construct();
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
	
	public function getDataRecapPerDate(){

		$this->load->model('Report/ModelRecapPerDate');
		$this->load->model('MainOperation');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$month			=	validatePostVar($this->postVar, 'month', true);
		$year			=	validatePostVar($this->postVar, 'year', true);
		$monthYear		=	$year."-".$month;
		$dataTable		=	$this->ModelRecapPerDate->getDataRecapPerDate($page, 31, $monthYear);
		$urlExcelReport	=	BASE_URL."report/recapPerDate/excelRecapPerDate/".base64_encode(encodeStringKeyFunction($month."|".$year, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		
		if(count($dataTable['data']) <= 0){
			$urlExcelReport	=	"";
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelReport"=>$urlExcelReport));
	
	}
	
	public function excelRecapPerDate($encryptedVar){

		$this->load->model('Report/ModelRecapPerDate');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$arrDates		=	array();
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$month			=	$expDecryptedVar[0];
		$year			=	$expDecryptedVar[1];
		$monthYear		=	$year."-".$month;
		$dataTable		=	$this->ModelRecapPerDate->getDataRecapPerDate(1, 9999, $monthYear);
		
		if(count($dataTable['data']) <= 0){
			echo "No data found!";
			die();
		}
		
		$spreadsheet	=	new Spreadsheet();
		$sheet			=	$spreadsheet->getActiveSheet();
		
		$sheet->getPageSetup()->setOrientation(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		$sheet->getPageSetup()->setPaperSize(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(0.25);
		$sheet->getPageMargins()->setRight(0.2);
		$sheet->getPageMargins()->setLeft(0.2);
		$sheet->getPageMargins()->setBottom(0.25);
	
		$sheet->setCellValue('A1', 'Bali Sun Tours');
		$sheet->setCellValue('A2', 'Report - Recap Reservation per Date');
		$sheet->setCellValue('A3', 'Month : '.$month);
		$sheet->setCellValue('A4', 'Year : '.$year);
		$sheet->getStyle('A1:A4')->getFont()->setBold( true );
		$sheet->mergeCells('A1:F1');
		$sheet->mergeCells('A2:F2');
		$sheet->mergeCells('A3:F3');
		$sheet->mergeCells('A4:F4');

		$sheet->setCellValue('A6', 'Date');
		$sheet->setCellValue('B6', 'Total Reservation');
		$sheet->setCellValue('C6', 'Total Active/Done');
		$sheet->setCellValue('D6', 'Total Cancel');
		$sheet->setCellValue('E6', 'Handle By Driver');
		$sheet->setCellValue('F6', 'Handle By Vendor');
		$sheet->getStyle('A6:F6')->getFont()->setBold( true );
		$sheet->getStyle('A6:F6')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A6:F6')->getAlignment()->setVertical('center');
		$rowNumber	=	7;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$grandTotalReservarion	=	$grandTotalActiveDone	=	$grandTotalCancel	=	$grandTotalHandleByDriver	=	$grandTotalHandleByVendor	=	0;
		foreach($dataTable['data'] as $data){
			
			$sheet->setCellValue('A'.$rowNumber, $data->DATERESERVATION); $sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');
			$sheet->setCellValue('B'.$rowNumber, $data->TOTALRESERVATION);
			$sheet->setCellValue('C'.$rowNumber, $data->TOTALACTIVERESERVATION);
			$sheet->setCellValue('D'.$rowNumber, $data->TOTALCANCELRESERVATION);
			$sheet->setCellValue('E'.$rowNumber, $data->TOTALHANDLEBYDRIVER);
			$sheet->setCellValue('F'.$rowNumber, $data->TOTALHANDLEBYVENDOR);
			
			$grandTotalReservarion		+=	$data->TOTALRESERVATION;
			$grandTotalActiveDone		+=	$data->TOTALACTIVERESERVATION;
			$grandTotalCancel			+=	$data->TOTALCANCELRESERVATION;
			$grandTotalHandleByDriver	+=	$data->TOTALHANDLEBYDRIVER;
			$grandTotalHandleByVendor	+=	$data->TOTALHANDLEBYVENDOR;
			$rowNumber++;
			
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->setCellValue('B'.$rowNumber, $grandTotalReservarion);
		$sheet->setCellValue('C'.$rowNumber, $grandTotalActiveDone);
		$sheet->setCellValue('D'.$rowNumber, $grandTotalCancel);
		$sheet->setCellValue('E'.$rowNumber, $grandTotalHandleByDriver);
		$sheet->setCellValue('F'.$rowNumber, $grandTotalHandleByVendor);
		$sheet->getStyle('A'.$rowNumber.':'.'F'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->getStyle('A6:F'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('F'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(15);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);

		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelReportRecapReservationPerDate_'.$month.'_'.$year;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}
	
}