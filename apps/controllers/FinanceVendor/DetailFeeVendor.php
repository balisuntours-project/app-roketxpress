<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class DetailFeeVendor extends CI_controller {
	
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
	
	public function getDetailFeeVendorCar(){

		$this->load->model('FinanceVendor/ModelDetailFeeVendor');
		$this->load->model('MainOperation');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$idVendorCar	=	validatePostVar($this->postVar, 'idVendorCar', false);
		$month			=	validatePostVar($this->postVar, 'month', true);
		$year			=	validatePostVar($this->postVar, 'year', true);
		$yearMonth		=	$year."-".$month;
		$dataTable		=	$this->ModelDetailFeeVendor->getDetailFeeVendorCar($page, 25, $idVendorCar, $yearMonth);
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
	public function getDetailFeeVendorTicket(){

		$this->load->model('FinanceVendor/ModelDetailFeeVendor');
		$this->load->model('MainOperation');
		
		$page							=	validatePostVar($this->postVar, 'page', true);
		$idVendorTicket					=	validatePostVar($this->postVar, 'idVendorTicket', false);
		$month							=	validatePostVar($this->postVar, 'month', true);
		$year							=	validatePostVar($this->postVar, 'year', true);
		$yearMonth						=	$year."-".$month;
		$startDate						=	$yearMonth."-01";
		$endDate						=	date("Y-m-t", strtotime($startDate));
		$dataTable						=	$this->ModelDetailFeeVendor->getDetailFeeVendorTicket($page, 25, $idVendorTicket, $startDate, $endDate);
		$urlExcelDetailFeeVendorTicket	=	"";
		
		if(count($dataTable['data']) > 0){
			$urlExcelDetailFeeVendorTicket	=	BASE_URL."financeVendor/detailFeeVendor/excelDetailFeeVendorTicket/".base64_encode(encodeStringKeyFunction($idVendorTicket."|".$startDate."|".$endDate, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		}		
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelDetailFeeVendorTicket"=>$urlExcelDetailFeeVendorTicket));
	
	}
	
	public function excelDetailFeeVendorTicket($encryptedVar){

		$this->load->model('MainOperation');
		$this->load->model('FinanceVendor/ModelDetailFeeVendor');
		$this->load->library('encryption');
		
		$arrDates			=	array();
		$decryptedVar		=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar	=	explode("|", $decryptedVar);
		$idVendorTicket		=	$expDecryptedVar[0];
		$startDate			=	$expDecryptedVar[1];
		$endDate			=	$expDecryptedVar[2];
		$vendorTicketStr	=	isset($idVendorTicket) && $idVendorTicket != "" && $idVendorTicket != 0 ? $this->MainOperation->getVendorNameById($idVendorTicket) : "All Vendor";
		$dataTable			=	$this->ModelDetailFeeVendor->getDetailFeeVendorTicket(1, 999999, $idVendorTicket, $startDate, $endDate);
		
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
		$sheet->setCellValue('A2', 'Detail Fee Ticket Vendor');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:R1');
		$sheet->mergeCells('A2:R2');
		
		$sheet->setCellValue('A4', 'Vendor : '.$vendorTicketStr);
		$sheet->setCellValue('A5', 'Period : '.$startDate.' - '.$endDate);
		$sheet->mergeCells('A4:R4');
		$sheet->mergeCells('A5:R5');
		
		$sheet->setCellValue('A7', 'Vendor Name');
		$sheet->setCellValue('B7', 'Date');
		$sheet->setCellValue('C7', 'Reservation Description');
		$sheet->setCellValue('F7', 'Schedule Details');
		$sheet->setCellValue('I7', 'Adult');
		$sheet->setCellValue('L7', 'Child');
		$sheet->setCellValue('O7', 'Infant');
		$sheet->setCellValue('R7', 'Fee');
		$sheet->mergeCells('A7:A8');
		$sheet->mergeCells('B7:B8');
		$sheet->mergeCells('C7:E7');
		$sheet->mergeCells('F7:H7');
		$sheet->mergeCells('I7:K7');
		$sheet->mergeCells('L7:N7');
		$sheet->mergeCells('O7:Q7');
		$sheet->mergeCells('R7:R8');
		
		$sheet->setCellValue('C8', 'Source');
		$sheet->setCellValue('D8', 'Title');
		$sheet->setCellValue('E8', 'Guest Name');
		$sheet->setCellValue('F8', 'Product');
		$sheet->setCellValue('G8', 'User Input');
		$sheet->setCellValue('H8', 'Notes');
		$sheet->setCellValue('I8', 'Pax');
		$sheet->setCellValue('J8', 'Price Per Pax');
		$sheet->setCellValue('K8', 'Total Price');
		$sheet->setCellValue('L8', 'Pax');
		$sheet->setCellValue('M8', 'Price Per Pax');
		$sheet->setCellValue('N8', 'Total Price');
		$sheet->setCellValue('O8', 'Pax');
		$sheet->setCellValue('P8', 'Price Per Pax');
		$sheet->setCellValue('Q8', 'Total Price');
		$sheet->setCellValue('R8', 'Total Ticket Price');
		
		$sheet->getStyle('A7:R8')->getFont()->setBold( true );
		$sheet->getStyle('A7:R8')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A7:R8')->getAlignment()->setVertical('center');
		$rowNumber			=	$firstRowNumber	=	9;
		$grandTotalAdultPax	=	$grandTotalAdultPrice	=	$grandTotalChildPax	=	$grandTotalChildPrice	=	$grandTotalInfantPax	=	$grandTotalInfantPrice	=	$grandTotalTicketPrice	=	0;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		foreach($dataTable['data'] as $data){
						
			$sheet->setCellValue('A'.$rowNumber, $data->NAME);
			$sheet->setCellValue('B'.$rowNumber, $data->SCHEDULEDATE);
			$sheet->setCellValue('C'.$rowNumber, $data->SOURCENAME);
			$sheet->setCellValue('D'.$rowNumber, $data->RESERVATIONTITLE);
			$sheet->setCellValue('E'.$rowNumber, $data->CUSTOMERNAME);
			$sheet->setCellValue('F'.$rowNumber, $data->PRODUCTNAME);
			$sheet->setCellValue('G'.$rowNumber, $data->USERINPUT);
			$sheet->setCellValue('H'.$rowNumber, $data->NOTES);
			$sheet->setCellValue('I'.$rowNumber, $data->PAXADULT);
			$sheet->setCellValue('J'.$rowNumber, $data->PRICEPERPAXADULT);
			$sheet->setCellValue('K'.$rowNumber, $data->PRICETOTALADULT);
			$sheet->setCellValue('L'.$rowNumber, $data->PAXCHILD);
			$sheet->setCellValue('M'.$rowNumber, $data->PRICEPERPAXCHILD);
			$sheet->setCellValue('N'.$rowNumber, $data->PRICETOTALCHILD);
			$sheet->setCellValue('O'.$rowNumber, $data->PAXINFANT);
			$sheet->setCellValue('P'.$rowNumber, $data->PRICEPERPAXINFANT);
			$sheet->setCellValue('Q'.$rowNumber, $data->PRICETOTALINFANT);
			$sheet->setCellValue('R'.$rowNumber, $data->NOMINAL);
			
			$grandTotalAdultPax		+=	$data->PAXADULT;
			$grandTotalChildPax		+=	$data->PAXCHILD;
			$grandTotalInfantPax	+=	$data->PAXINFANT;
			$grandTotalAdultPrice	+=	$data->PRICETOTALADULT;
			$grandTotalChildPrice	+=	$data->PRICETOTALCHILD;
			$grandTotalInfantPrice	+=	$data->PRICETOTALINFANT;
			$grandTotalTicketPrice	+=	$data->NOMINAL;
			$rowNumber++;
			
		}
				
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':H'.$rowNumber);

		$sheet->setCellValue('I'.$rowNumber, $grandTotalAdultPax);		$sheet->getStyle('I'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('I'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('K'.$rowNumber, $grandTotalAdultPrice);	$sheet->getStyle('K'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('K'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('L'.$rowNumber, $grandTotalChildPax);		$sheet->getStyle('L'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('L'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('N'.$rowNumber, $grandTotalChildPrice);	$sheet->getStyle('N'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('N'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('O'.$rowNumber, $grandTotalInfantPax);		$sheet->getStyle('O'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('O'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('Q'.$rowNumber, $grandTotalInfantPrice);	$sheet->getStyle('Q'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('Q'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('R'.$rowNumber, $grandTotalTicketPrice);	$sheet->getStyle('R'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('R'.$rowNumber)->getFont()->setBold( true );

		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('K'.$firstRowNumber.':K'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('N'.$firstRowNumber.':N'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('Q'.$firstRowNumber.':Q'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('R'.$firstRowNumber.':R'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A7:R'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('R'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(12);
		$sheet->getColumnDimension('B')->setWidth(12);
		$sheet->getColumnDimension('C')->setWidth(12);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(25);
		$sheet->getColumnDimension('F')->setWidth(18);
		$sheet->getColumnDimension('G')->setWidth(14);
		$sheet->getColumnDimension('H')->setWidth(20);
		$sheet->getColumnDimension('I')->setWidth(8);
		$sheet->getColumnDimension('J')->setWidth(12);
		$sheet->getColumnDimension('K')->setWidth(12);
		$sheet->getColumnDimension('L')->setWidth(8);
		$sheet->getColumnDimension('M')->setWidth(12);
		$sheet->getColumnDimension('N')->setWidth(12);
		$sheet->getColumnDimension('O')->setWidth(8);
		$sheet->getColumnDimension('P')->setWidth(12);
		$sheet->getColumnDimension('Q')->setWidth(12);
		$sheet->getColumnDimension('R')->setWidth(12);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDetailFeeVendorTicket_'.$vendorTicketStr.'_'.$startDate.' - '.$endDate;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}
	
}