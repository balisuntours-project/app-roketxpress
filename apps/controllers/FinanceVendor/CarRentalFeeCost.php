<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class CarRentalFeeCost extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(3);
		if($_SERVER['REQUEST_METHOD'] === 'POST' && $functionName != "uploadCostReceipt"){
			$this->postVar	=	decodeJsonPost();
			$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid submission data"));
			$this->newToken	=	isLoggedIn($this->token, true);
		} else {
			if($functionName != "excelRecapCarRentalCostFee" && $functionName != "excelDetailCarRentalFee" && $functionName != "excelDetailCarRentalCost" && $functionName != "uploadCostReceipt"){
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
	
	public function getDataAllCarVendor(){
		$this->load->model('FinanceVendor/ModelCarRentalFeeCost');
		$dataAllCarVendor	=	$this->ModelCarRentalFeeCost->getDataAllCar();

		if(!$dataAllCarVendor) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		setResponseOk(
			array(
				"token"				=>	$this->newToken,
				"dataAllCarVendor"	=>	$dataAllCarVendor
			)
		);
	}
	
	public function getRecapCarRentalCostFee(){
		$this->load->model('FinanceVendor/ModelCarRentalFeeCost');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$idVendorCar	=	validatePostVar($this->postVar, 'idVendorCar', false);
		$month			=	validatePostVar($this->postVar, 'month', true);
		$year			=	validatePostVar($this->postVar, 'year', true);
		$yearMonth		=	$year."-".$month;
		$searchKeyword	=	validatePostVar($this->postVar, 'searchKeyword', false);
		$dataTable		=	$this->ModelCarRentalFeeCost->getDataRecapCarRentalCostFee($page, 25, $idVendorCar, $yearMonth, $searchKeyword);
		$urlExcelRecap	=	"";
		
		if(count($dataTable['data']) > 0){
			$base64ParamExcelRecap	=	base64_encode(encodeStringKeyFunction($idVendorCar."|".$yearMonth."|".$searchKeyword, DEFAULT_KEY_ENCRYPTION));
			$urlExcelRecap			=	BASE_URL."financeVendor/carRentalFeeCost/excelRecapCarRentalCostFee/".$base64ParamExcelRecap."/token?token=".$this->newToken;
		}	
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelRecap"=>$urlExcelRecap));
	}
	
	public function excelRecapCarRentalCostFee($encryptedVar){
		$this->load->model('MainOperation');
		$this->load->model('FinanceVendor/ModelCarRentalFeeCost');
		$this->load->library('encryption');
		
		$arrDates		=	array();
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$idVendorCar	=	$expDecryptedVar[0];
		$yearMonth		=	$expDecryptedVar[1];
		$searchKeyword	=	$expDecryptedVar[2];
		$vendorCarStr	=	isset($idVendorCar) && $idVendorCar != "" && $idVendorCar != 0 ? $this->MainOperation->getVendorNameById($idVendorCar) : "All Vendor";
		$dataTable		=	$this->ModelCarRentalFeeCost->getDataRecapCarRentalCostFee(1, 999999, $idVendorCar, $yearMonth, $searchKeyword);
		
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
		$sheet->setCellValue('A2', 'Recap Fee/Cost Car Rental');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:H1');
		$sheet->mergeCells('A2:H2');
		
		$sheet->setCellValue('A4', 'Vendor : '.$vendorCarStr);
		$sheet->setCellValue('A5', 'Period : '.$yearMonth);
		$sheet->setCellValue('A6', 'Search Keyword : '.$searchKeyword);
		$sheet->mergeCells('A4:H4');
		$sheet->mergeCells('A5:H5');
		$sheet->mergeCells('A6:H6');
		
		$sheet->setCellValue('A8', 'Vendor Name');
		$sheet->setCellValue('B8', 'Default Driver');
		$sheet->setCellValue('C8', 'Car Detail');
		$sheet->setCellValue('D8', 'Car Description');
		$sheet->setCellValue('E8', 'Total Schedule');
		$sheet->setCellValue('F8', 'Total Cost');
		$sheet->setCellValue('G8', 'Total Fee');
		$sheet->setCellValue('H8', 'Grand Total');
		$sheet->getStyle('A8:H8')->getFont()->setBold( true );
		$sheet->getStyle('A8:H8')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A8:H8')->getAlignment()->setVertical('center');

		$rowNumber			=	$firstRowNumber	=	9;
		$grandTotalSchedule	=	$grandTotalFee	=	$grandTotalCost	=	$grandTotalCostFee	=	0;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		foreach($dataTable['data'] as $data){
			$totalNominalFee	=	$data->TOTALNOMINALFEE;
			$totalNominalCost	=	$data->TOTALNOMINALCOST;
			$totalNominalCostFee=	$totalNominalFee - $totalNominalCost;
			
			$sheet->setCellValue('A'.$rowNumber, $data->VENDORNAME);
			$sheet->setCellValue('B'.$rowNumber, $data->DRIVERNAME);
			$sheet->setCellValue('C'.$rowNumber, $data->PLATNUMBER." - ".$data->CARDETAIL);
			$sheet->setCellValue('D'.$rowNumber, $data->DESCRIPTION);
			$sheet->setCellValue('E'.$rowNumber, $data->TOTALCARSCHEDULE);
			$sheet->setCellValue('F'.$rowNumber, $data->TOTALNOMINALFEE);
			$sheet->setCellValue('G'.$rowNumber, $data->TOTALNOMINALCOST);
			$sheet->setCellValue('H'.$rowNumber, $totalNominalCostFee);
			
			$grandTotalSchedule	+=	$data->TOTALCARSCHEDULE;
			$grandTotalFee		+=	$totalNominalFee;
			$grandTotalCost		+=	$totalNominalCost;
			$grandTotalCostFee	+=	$totalNominalCostFee;
			$rowNumber++;
		}
				
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':D'.$rowNumber);

		$sheet->setCellValue('E'.$rowNumber, $grandTotalSchedule);	$sheet->getStyle('E'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('E'.$rowNumber)->getFont()->setBold(true);
		$sheet->setCellValue('F'.$rowNumber, $totalNominalFee);		$sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('F'.$rowNumber)->getFont()->setBold(true);
		$sheet->setCellValue('G'.$rowNumber, $totalNominalCost);	$sheet->getStyle('G'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('G'.$rowNumber)->getFont()->setBold(true);
		$sheet->setCellValue('H'.$rowNumber, $grandTotalCostFee);	$sheet->getStyle('H'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('H'.$rowNumber)->getFont()->setBold(true);

		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold(true);
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('E'.$firstRowNumber.':E'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('F'.$firstRowNumber.':F'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('G'.$firstRowNumber.':G'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('H'.$firstRowNumber.':H'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A8:H'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('H'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(17);
		$sheet->getColumnDimension('B')->setWidth(17);
		$sheet->getColumnDimension('C')->setWidth(36);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(15);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelRecapCarRentalCost_'.$vendorCarStr.'_'.$yearMonth.' - '.$searchKeyword;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
	
	public function getDetailCarRentalFee(){
		$this->load->model('FinanceVendor/ModelCarRentalFeeCost');

		$page			=	validatePostVar($this->postVar, 'page', true);
		$idVendorCar	=	validatePostVar($this->postVar, 'idVendorCar', false);
		$startDate		=	validatePostVar($this->postVar, 'startDate', true);
		$endDate		=	validatePostVar($this->postVar, 'endDate', true);
		$startDate		=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate		=	$startDate->format('Y-m-d');
		$endDate		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate		=	$endDate->format('Y-m-d');
		$searchKeyword	=	validatePostVar($this->postVar, 'searchKeyword', false);
		$dataTable		=	$this->ModelCarRentalFeeCost->getDataDetailCarRentalFee($page, 25, $idVendorCar, $startDate, $endDate, $searchKeyword);
		$urlExcelDetail	=	"";
		
		if(count($dataTable['data']) > 0){
			$base64ParamExcel	=	base64_encode(encodeStringKeyFunction($idVendorCar."|".$startDate."|".$endDate."|".$searchKeyword, DEFAULT_KEY_ENCRYPTION));
			$urlExcelDetail		=	BASE_URL."financeVendor/carRentalFeeCost/excelDetailCarRentalFee/".$base64ParamExcel."/token?token=".$this->newToken;
		}	
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelDetail"=>$urlExcelDetail));
	}
	
	public function excelDetailCarRentalFee($encryptedVar){
		$this->load->model('MainOperation');
		$this->load->model('FinanceVendor/ModelCarRentalFeeCost');
		$this->load->library('encryption');
		
		$arrDates		=	array();
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$idVendorCar	=	$expDecryptedVar[0];
		$startDate		=	$expDecryptedVar[1];
		$endDate		=	$expDecryptedVar[2];
		$searchKeyword	=	$expDecryptedVar[3];
		$vendorCarStr	=	isset($idVendorCar) && $idVendorCar != "" && $idVendorCar != 0 ? $this->MainOperation->getVendorNameById($idVendorCar) : "All Vendor";
		$dataTable		=	$this->ModelCarRentalFeeCost->getDataDetailCarRentalFee(1, 999999, $idVendorCar, $startDate, $endDate, $searchKeyword);
		
		if(count($dataTable['data']) <= 0){
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
		$sheet->setCellValue('A2', 'Detail Fee Car Rental');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:J1');
		$sheet->mergeCells('A2:J2');
		
		$sheet->setCellValue('A4', 'Vendor : '.$vendorCarStr);
		$sheet->setCellValue('A5', 'Period : '.$startDate.' - '.$endDate);
		$sheet->setCellValue('A6', 'Search Keyword : '.$searchKeyword);
		$sheet->mergeCells('A4:J4');
		$sheet->mergeCells('A5:J5');
		$sheet->mergeCells('A6:J6');
		
		$sheet->setCellValue('A8', 'Vendor Name');
		$sheet->setCellValue('B8', 'Default Driver');
		$sheet->setCellValue('C8', 'Date');
		$sheet->setCellValue('D8', 'Car Detail');
		$sheet->setCellValue('E8', 'Reservation Source');
		$sheet->setCellValue('F8', 'Reservation Title');
		$sheet->setCellValue('G8', 'Customer Name');
		$sheet->setCellValue('H8', 'Schedule Title');
		$sheet->setCellValue('I8', 'Schedule Notes');
		$sheet->setCellValue('J8', 'Nominal Fee');
		$sheet->getStyle('A8:J8')->getFont()->setBold( true );
		$sheet->getStyle('A8:J8')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A8:J8')->getAlignment()->setVertical('center');

		$rowNumber		=	$firstRowNumber	=	9;
		$grandTotalFee	=	0;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		foreach($dataTable['data'] as $data){
			$inputType	=	'';
			switch($data->INPUTTYPE){
				case "1"	:	$inputType	=	'Mailbox'; break;
				case "2"	:	$inputType	=	'Manual'; break;
			}
			
			$sheet->setCellValue('A'.$rowNumber, $data->VENDORNAME);
			$sheet->setCellValue('B'.$rowNumber, $data->DRIVERNAME);
			$sheet->setCellValue('C'.$rowNumber, $data->SCHEDULEDATE);
			$sheet->setCellValue('D'.$rowNumber, str_replace("<br/>", "\n", $data->CARDETAILS));
			$sheet->setCellValue('E'.$rowNumber, "[".$inputType."] ".$data->SOURCENAME);
			$sheet->setCellValue('F'.$rowNumber, $data->BOOKINGCODE." - ".$data->RESERVATIONTITLE);
			$sheet->setCellValue('G'.$rowNumber, $data->CUSTOMERNAME);
			$sheet->setCellValue('H'.$rowNumber, $data->PRODUCTNAME);
			$sheet->setCellValue('I'.$rowNumber, $data->NOTES);
			$sheet->setCellValue('J'.$rowNumber, $data->NOMINAL);
			
			$grandTotalFee	+=	$data->NOMINAL;
			$rowNumber++;			
		}
				
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':I'.$rowNumber);
		$sheet->setCellValue('J'.$rowNumber, $grandTotalFee);	$sheet->getStyle('J'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('J'.$rowNumber)->getFont()->setBold( true );

		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold(true);
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A'.$rowNumber.':F'.$rowNumber)->getFont()->setBold(true);
		$sheet->getStyle('A8:J'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('J'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(16);
		$sheet->getColumnDimension('B')->setWidth(16);
		$sheet->getColumnDimension('C')->setWidth(12);
		$sheet->getColumnDimension('D')->setWidth(25);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(30);
		$sheet->getColumnDimension('G')->setWidth(18);
		$sheet->getColumnDimension('H')->setWidth(18);
		$sheet->getColumnDimension('I')->setWidth(30);
		$sheet->getColumnDimension('J')->setWidth(15);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDetailCarRentalFee_'.$vendorCarStr.'_'.$startDate.'_'.$endDate.' - '.$searchKeyword;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}
	
	public function getDetailCarRentalCost(){
		$this->load->model('FinanceVendor/ModelCarRentalFeeCost');

		$page			=	validatePostVar($this->postVar, 'page', true);
		$idVendorCar	=	validatePostVar($this->postVar, 'idVendorCar', false);
		$recognitionDate=	validatePostVar($this->postVar, 'recognitionDate', false);
		$searchKeyword	=	validatePostVar($this->postVar, 'searchKeyword', false);
	
		if(isset($recognitionDate) && $recognitionDate != ''){
			$recognitionDate=	DateTime::createFromFormat('d-m-Y', $recognitionDate);
			$recognitionDate=	$recognitionDate->format('Y-m-d');
		}

		$dataTable		=	$this->ModelCarRentalFeeCost->getDataDetailCarRentalCost($page, 25, $idVendorCar, $recognitionDate, $searchKeyword);
		$urlExcelDetail	=	"";
		
		if(count($dataTable['data']) > 0){
			$base64ParamExcel	=	base64_encode(encodeStringKeyFunction($idVendorCar."|".$recognitionDate."|".$searchKeyword, DEFAULT_KEY_ENCRYPTION));
			$urlExcelDetail		=	BASE_URL."financeVendor/carRentalFeeCost/excelDetailCarRentalCost/".$base64ParamExcel."/token?token=".$this->newToken;
		}	
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelDetail"=>$urlExcelDetail));
	}
	
	public function excelDetailCarRentalCost($encryptedVar){
		$this->load->model('MainOperation');
		$this->load->model('FinanceVendor/ModelCarRentalFeeCost');
		$this->load->library('encryption');
		
		$arrDates			=	array();
		$decryptedVar		=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar	=	explode("|", $decryptedVar);
		$idVendorCar		=	$expDecryptedVar[0];
		$recognitionDate	=	$expDecryptedVar[1];
		$searchKeyword		=	$expDecryptedVar[2];
		$vendorCarStr		=	isset($idVendorCar) && $idVendorCar != "" && $idVendorCar != 0 ? $this->MainOperation->getVendorNameById($idVendorCar) : "All Vendor";
		$dataTable			=	$this->ModelCarRentalFeeCost->getDataDetailCarRentalCost(1, 200, $idVendorCar, $recognitionDate, $searchKeyword);
		$recognitionDateStr	=	'-';
		
		if(isset($recognitionDate) && $recognitionDate != ''){
			$recognitionDateStr	=	DateTime::createFromFormat('Y-m-d', $recognitionDate);
			$recognitionDateStr	=	$recognitionDateStr->format('d M Y');
		}
		
		if(count($dataTable['data']) <= 0){
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
		$sheet->setCellValue('A2', 'Detail Cost Car Rental');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:L1');
		$sheet->mergeCells('A2:L2');
		
		$sheet->setCellValue('A4', 'Vendor : '.$vendorCarStr);
		$sheet->setCellValue('A5', 'Date Recognition : '.$recognitionDateStr);
		$sheet->setCellValue('A6', 'Search Keyword : '.$searchKeyword);
		$sheet->mergeCells('A4:L4');
		$sheet->mergeCells('A5:L5');
		$sheet->mergeCells('A6:L6');
		
		$sheet->setCellValue('A8', 'Vendor Name');
		$sheet->setCellValue('B8', 'Car Detail');
		$sheet->setCellValue('C8', 'Cost Type');
		$sheet->setCellValue('D8', 'Recognition Date');
		$sheet->setCellValue('E8', 'Description');
		$sheet->setCellValue('F8', 'Status');
		$sheet->setCellValue('G8', 'Input Details');
		$sheet->setCellValue('H8', 'Approval Details');
		$sheet->setCellValue('I8', 'Off Duration');
		$sheet->setCellValue('J8', 'Off Start');
		$sheet->setCellValue('K8', 'Off End');
		$sheet->setCellValue('L8', 'Nominal');
		$sheet->getStyle('A8:L8')->getFont()->setBold( true );
		$sheet->getStyle('A8:L8')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A8:L8')->getAlignment()->setVertical('center');

		$rowNumber		=	$firstRowNumber	=	9;
		$grandTotalCost	=	0;
		
		$styleArray	=	[
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		foreach($dataTable['data'] as $data){
			$dataCarDetails	=	str_replace(['<b>', '</b>'], ['', ''], $data->CARDETAILS);
			$dataCarDetails	=	explode("<br/>", $dataCarDetails);
			$vendorName		=	$dataCarDetails[0];
			$platNumber		=	$dataCarDetails[1];
			$carDetails		=	$dataCarDetails[2];
			$statusApproval	=	'-';
			
			switch(intval($data->STATUSAPPROVAL)){
				case -1	:	$statusApproval	=	'Rejected'; break;
				case 0	:	$statusApproval	=	'Waiting'; break;
				case 1	:	$statusApproval	=	'Approved'; break;
			}
			
			$sheet->setCellValue('A'.$rowNumber, $vendorName);
			$sheet->setCellValue('B'.$rowNumber, $platNumber."\n".$carDetails);
			$sheet->setCellValue('C'.$rowNumber, $data->CARCOSTTYPE);
			$sheet->setCellValue('D'.$rowNumber, $data->DATECOSTRECOGNITION);
			$sheet->setCellValue('E'.$rowNumber, $data->DESCRIPTION);
			$sheet->setCellValue('F'.$rowNumber, $statusApproval);
			$sheet->setCellValue('G'.$rowNumber, $data->USERINPUT."\n".$data->DATETIMEINPUT);
			$sheet->setCellValue('H'.$rowNumber, $data->USERAPPROVAL."\n".$data->DATETIMEAPPROVAL);
			$sheet->setCellValue('I'.$rowNumber, $data->DURATIONHOUROFF." Hours");
			$sheet->setCellValue('J'.$rowNumber, $data->DATETIMEOFFSTART);
			$sheet->setCellValue('K'.$rowNumber, $data->DATETIMEOFFEND);
			$sheet->setCellValue('L'.$rowNumber, $data->NOMINAL);
			
			$grandTotalCost	+=	$data->NOMINAL;
			$rowNumber++;			
		}
				
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':K'.$rowNumber);
		$sheet->setCellValue('L'.$rowNumber, $grandTotalCost);	$sheet->getStyle('L'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('L'.$rowNumber)->getFont()->setBold( true );

		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold(true);
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A'.$rowNumber.':L'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A8:L'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('L'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(16);
		$sheet->getColumnDimension('B')->setWidth(25);
		$sheet->getColumnDimension('C')->setWidth(25);
		$sheet->getColumnDimension('D')->setWidth(18);
		$sheet->getColumnDimension('E')->setWidth(35);
		$sheet->getColumnDimension('F')->setWidth(12);
		$sheet->getColumnDimension('G')->setWidth(25);
		$sheet->getColumnDimension('H')->setWidth(25);
		$sheet->getColumnDimension('I')->setWidth(16);
		$sheet->getColumnDimension('J')->setWidth(18);
		$sheet->getColumnDimension('K')->setWidth(18);
		$sheet->getColumnDimension('L')->setWidth(10);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer		=	new Xlsx($spreadsheet);
		$filename	=	'ExcelDetailCarRentalCost_'.$vendorCarStr.'_'.$recognitionDate.' - '.$searchKeyword;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}
	
	public function uploadCostReceipt(){
		if((($_FILES["file"]["type"] == "image/jpeg")
			|| ($_FILES["file"]["type"] == "image/jpg")
			|| ($_FILES["file"]["type"] == "image/png"))
			&& ($_FILES["file"]["size"] <= 500000)){
			if ($_FILES["file"]["error"] > 0) {
				setResponseInternalServerError(array("msg"=>"Failed to upload this file. File is broken"));
			}
			
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. This file type is not allowed (".$_FILES["file"]["type"].") or file size is too big (".$_FILES["file"]["size"].")"));
		}
		
		$dir		=	PATH_CAR_COST_RECEIPT;
		$extension	=	pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		$namaFile	=	"CarCostReceipt"."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$namaFile);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "urlCostReceipt"=>URL_CAR_COST_RECEIPT.$namaFile, "costReceiptFileName"=>$namaFile));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}
	}
	
	public function getDetailCarRentalCostById(){
		$this->load->model('FinanceVendor/ModelCarRentalFeeCost');

		$idCarCost		=	validatePostVar($this->postVar, 'idCarCost', true);
		$detailCarCost	=	$this->ModelCarRentalFeeCost->getDetailCarCostById($idCarCost);
		
		if(!$detailCarCost) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No cost detail found for selected data"));
		setResponseOk(array("token"=>$this->newToken, "detailCarCost"=>$detailCarCost));
	}	
	
	public function saveCarCost(){
		$this->load->model('MainOperation');
		
		$idCarVendor		=	validatePostVar($this->postVar, 'idCarVendor', true);
		$recognitionDate	=	validatePostVar($this->postVar, 'recognitionDate', true);
		$recognitionDate	=	DateTime::createFromFormat('d-m-Y', $recognitionDate);
		$recognitionDate	=	$recognitionDate->format('Y-m-d');
		$idCostType			=	validatePostVar($this->postVar, 'idCostType', true);
		$nominal			=	validatePostVar($this->postVar, 'nominal', true);
		$nominal			=	preg_replace("/[^0-9]/", "", $nominal);
		$description		=	validatePostVar($this->postVar, 'description', true);
		$approvalStatus		=	validatePostVar($this->postVar, 'approvalStatus', false);
		$costReceiptFileName=	validatePostVar($this->postVar, 'costReceiptFileName', true);
		$idCarCost			=	validatePostVar($this->postVar, 'idCarCost', false);
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$dataUserAdmin['NAME'];
		
		if($nominal <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please input valid nominal"));
		if(!isset($idCarVendor) || $idCarVendor == ""  || $idCarVendor == 0 || !isset($idCostType) || $idCostType == ""  || $idCostType == 0 || !isset($costReceiptFileName) || $costReceiptFileName == ""){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
		}
		
		$arrInsertUpdate	=	array(
			"IDCARVENDOR"			=>	$idCarVendor,
			"IDCARCOSTTYPE"			=>	$idCostType,
			"DESCRIPTION"			=>	$description,
			"NOMINAL"				=>	$nominal,
			"IMAGERECEIPT"			=>	$costReceiptFileName,
			"DATECOSTRECOGNITION"	=>	$recognitionDate,
			"STATUSAPPROVAL"		=>	$approvalStatus
		);
		
		if($idCarCost == 0){
			$arrInsertUpdate['USERINPUT']		=	$userAdminName;
			$arrInsertUpdate['DATETIMEINPUT']	=	date('Y-m-d H:i:s');
		}
		
		if($idCarCost != 0 && $approvalStatus != 0){
			$arrInsertUpdate['USERAPPROVAL']	=	$userAdminName;
			$arrInsertUpdate['DATETIMEAPPROVAL']=	date('Y-m-d H:i:s');
		}
		
		
		$procInsertUpdate	=	$idCarCost == 0 ? $this->MainOperation->addData('t_carcost', $arrInsertUpdate) : $this->MainOperation->updateData('t_carcost', $arrInsertUpdate, 'IDCARCOST', $idCarCost);
		
		if(!$procInsertUpdate['status']){
			switchMySQLErrorCode($procInsertUpdate['errCode'], $this->newToken);
		}
		
		$msgResponse	=	$idCarCost == 0 ? "New car cost data has been added" : "Car cost data has been updated";
		setResponseOk(array("token"=>$this->newToken, "msg"=>$msgResponse));
	}
	
	public function getDataCarRentalAdditionalCost(){
		$this->load->model('FinanceVendor/ModelCarRentalFeeCost');

		$page			=	validatePostVar($this->postVar, 'page', true);
		$idVendorCar	=	validatePostVar($this->postVar, 'idVendorCar', false);
		$idDriver		=	validatePostVar($this->postVar, 'idDriver', false);
		$startDate		=	validatePostVar($this->postVar, 'startDate', true);
		$startDate		=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate		=	$startDate->format('Y-m-d');
		$endDate		=	validatePostVar($this->postVar, 'endDate', true);
		$endDate		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate		=	$endDate->format('Y-m-d');
		$searchKeyword	=	validatePostVar($this->postVar, 'searchKeyword', false);
		$viewRequestOnly=	validatePostVar($this->postVar, 'viewRequestOnly', false);
		$dataTable		=	$this->ModelCarRentalFeeCost->getDataCarRentalAdditionalCost($page, 25, $idVendorCar, $idDriver, $startDate, $endDate, $searchKeyword, $viewRequestOnly);

		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	}
	
	public function getDataScheduleAdditionalCost(){
		$this->load->model('FinanceVendor/ModelCarRentalFeeCost');
		
		$idDriver		=	validatePostVar($this->postVar, 'idDriver', false);
		$idJobType		=	validatePostVar($this->postVar, 'idJobType', false);
		$scheduleDate	=	validatePostVar($this->postVar, 'scheduleDate', false);
		$scheduleDate	=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$scheduleDate	=	$scheduleDate->format('Y-m-d');
		$keyword		=	validatePostVar($this->postVar, 'keyword', false);		
		$scheduleList	=	$this->ModelCarRentalFeeCost->getListScheduleAdditionalCost($idDriver, $idJobType, $scheduleDate, $keyword);
		
		if(!$scheduleList) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		setResponseOk(
			array(
				"token"			=>	$this->newToken,
				"scheduleList"	=>	$scheduleList
			)
		);
	}
}