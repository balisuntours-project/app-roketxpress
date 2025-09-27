<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RecapFeePerProduct extends CI_controller {
	
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
	
	public function getDataRecapFeePerProduct(){

		$this->load->model('Finance/ModelRecapFeePerProduct');
		$this->load->model('MainOperation');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$productName	=	validatePostVar($this->postVar, 'productName', false);
		$idProductType	=	validatePostVar($this->postVar, 'idProductType', false);
		$startDate		=	validatePostVar($this->postVar, 'startDate', true);
		$endDate		=	validatePostVar($this->postVar, 'endDate', true);
		$startDate		=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate		=	$startDate->format('Y-m-d');
		$endDate		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate		=	$endDate->format('Y-m-d');
		$dataTable		=	$this->ModelRecapFeePerProduct->getDataRecapFeePerProduct($page, 25, $productName, $idProductType, $startDate, $endDate);
		$urlExcelRecap	=	"";
		
		if(count($dataTable['data']) > 0){
			$urlExcelRecap	=	BASE_URL."finance/recapFeePerProduct/excelRecapFeePerProduct/".base64_encode(encodeStringKeyFunction($productName."|".$idProductType."|".$startDate."|".$endDate, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		}		
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelRecap"=>$urlExcelRecap));
	
	}
	
	public function excelRecapFeePerProduct($encryptedVar){

		$this->load->model('Finance/ModelRecapFeePerProduct');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$arrDates		=	array();
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$productName	=	$expDecryptedVar[0];
		$idProductType	=	$expDecryptedVar[1];
		$startDate		=	$expDecryptedVar[2];
		$endDate		=	$expDecryptedVar[3];
		$startDateDT	=	DateTime::createFromFormat('Y-m-d', $startDate);
		$startDateStr	=	$startDateDT->format('d M Y');
		$endDateDT		=	DateTime::createFromFormat('Y-m-d', $endDate);
		$endDateStr		=	$endDateDT->format('d M Y');
		$productType	=	$idProductType == "" || $idProductType == 0 ? "All Product Type" : $this->MainOperation->getProductTypeById($idProductType);
		$dataTable		=	$this->ModelRecapFeePerProduct->getDataRecapFeePerProduct(1, 99999, $productName, $idProductType, $startDate, $endDate);
		
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
		$sheet->setCellValue('A2', 'Report - Recap Fee Per Product');
		$sheet->setCellValue('A3', 'Product Type : '.$productType);
		$sheet->setCellValue('A4', 'Product Name Like : '.$productName);
		$sheet->setCellValue('A5', 'Period : '.$startDateStr." to ".$endDateStr);
		$sheet->getStyle('A1:A5')->getFont()->setBold( true );
		$sheet->mergeCells('A1:D1');
		$sheet->mergeCells('A2:D2');
		$sheet->mergeCells('A3:D3');
		$sheet->mergeCells('A4:D4');
		$sheet->mergeCells('A5:D5');

		$sheet->setCellValue('A7', 'Product Type');
		$sheet->setCellValue('B7', 'Product Name');
		$sheet->setCellValue('C7', 'Total Reservation');
		$sheet->setCellValue('D7', 'Total Fee');
		$sheet->getStyle('A7:D7')->getFont()->setBold( true );
		$sheet->getStyle('A7:D7')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A7:D7')->getAlignment()->setVertical('center');
		$rowNumber	=	8;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$grandTotalReservarion	=	$grandTotalFee	=	0;
		foreach($dataTable['data'] as $data){
			
			$sheet->setCellValue('A'.$rowNumber, $data->PRODUCTTYPE);
			$sheet->setCellValue('B'.$rowNumber, $data->PRODUCTNAME);
			$sheet->setCellValue('C'.$rowNumber, $data->TOTALRESERVATION);
			$sheet->setCellValue('D'.$rowNumber, $data->TOTALFEE);
			
			$grandTotalReservarion	+=	$data->TOTALRESERVATION;
			$grandTotalFee			+=	$data->TOTALFEE;
			$rowNumber++;
			
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL'); $sheet->mergeCells('A'.$rowNumber.':B'.$rowNumber);
		$sheet->setCellValue('C'.$rowNumber, $grandTotalReservarion);
		$sheet->setCellValue('D'.$rowNumber, $grandTotalFee);
		$sheet->getStyle('A'.$rowNumber.':'.'D'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->getStyle('A7:D'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('D'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(15);
		$sheet->getColumnDimension('B')->setWidth(45);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(20);

		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelReportRecapFeePerPorduct_'.$productType.'_'.$startDate.'_'.$endDate;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}
	
}