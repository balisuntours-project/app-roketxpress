<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class DetailReservationIncome extends CI_controller {
	
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
	
	public function getDataDetailReservationIncome(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelDetailReservationIncome');
		
		$page					=	validatePostVar($this->postVar, 'page', true);
		$idReservationType		=	validatePostVar($this->postVar, 'idReservationType', false);
		$idSource				=	validatePostVar($this->postVar, 'idSource', false);
		$startDate				=	validatePostVar($this->postVar, 'startDate', true);
		$endDate				=	validatePostVar($this->postVar, 'endDate', true);
		$startDateDT			=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate				=	$startDateDT->format('Y-m-d');
		$endDateDT				=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate				=	$endDateDT->format('Y-m-d');
		$keywordSearch			=	validatePostVar($this->postVar, 'keywordSearch', false);
		$includeCollectPayment	=	validatePostVar($this->postVar, 'includeCollectPayment', false);
		$includeAdditionalCost	=	validatePostVar($this->postVar, 'includeAdditionalCost', false);
		$totalDaysPeriod		=	$startDateDT->diff($endDateDT)->days;
		$dataTable				=	$this->ModelDetailReservationIncome->getDataDetailReservationIncome($page, 25, $startDate, $endDate, $idSource, $idReservationType, $keywordSearch, $includeCollectPayment, $includeAdditionalCost);
		$urlExcelDetail			=	"";
		
		if(count($dataTable['data']) > 0){
			$urlExcelDetail	=	BASE_URL."finance/detailReservationIncome/excelDetail/".base64_encode(encodeStringKeyFunction($startDate."|".$endDate."|".$idSource."|".$idReservationType."|".$keywordSearch."|".$includeCollectPayment."|".$includeAdditionalCost, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelDetail"=>$urlExcelDetail));
	}
	
	public function excelDetail($encryptedVar){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelDetailReservationIncome');
		$this->load->library('encryption');
		
		$arrDates					=	array();
		$decryptedVar				=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar			=	explode("|", $decryptedVar);
		$startDate					=	$expDecryptedVar[0];
		$endDate					=	$expDecryptedVar[1];
		$idSource					=	$expDecryptedVar[2];
		$idReservationType			=	$expDecryptedVar[3];
		$keywordSearch				=	$expDecryptedVar[4];
		$includeCollectPayment		=	$expDecryptedVar[5];
		$includeAdditionalCost		=	$expDecryptedVar[6];
		$reservationType			=	isset($idReservationType) && $idReservationType != "" && $idReservationType != 0 ? $this->MainOperation->getReservationTypeById($idReservationType) : "All Reservation Type";
		$sourceName					=	isset($idSource) && $idSource != "" && $idSource != 0 ? $this->MainOperation->getSourceNameById($idSource) : "All Source";
		$includeCollectPaymentStr	=	isset($includeCollectPayment) && $includeCollectPayment == true ? "Yes" : "No";
		$includeAdditionalCostStr	=	isset($includeAdditionalCost) && $includeAdditionalCost == true ? "Yes" : "No";

		if($startDate == "" && $endDate != "") $startDate	=	$endDate;
		if($startDate != "" && $endDate == "") $endDate		=	$startDate;
		
		if($startDate != ""){
			$startDateDT	=	DateTime::createFromFormat('Y-m-d', $startDate);
			$startDateStr	=	$startDateDT->format('d M Y');
			$endDateDT		=	DateTime::createFromFormat('Y-m-d', $endDate);
			$endDateStr		=	$endDateDT->format('d M Y');
		}
		
		$dateRangeStr		=	$startDateStr." to ".$endDateStr;
		$dataTable			=	$this->ModelDetailReservationIncome->getDataDetailReservationIncome(1, 999999, $startDate, $endDate, $idSource, $idReservationType, $keywordSearch, $includeCollectPayment, $includeAdditionalCost);
		
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
		$sheet->setCellValue('A2', 'Detail Reservation Income');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:O1');
		$sheet->mergeCells('A2:O2');
		$sheet->setCellValue('A4', 'Reservation Type');			$sheet->setCellValue('B4', ': '.$reservationType);			$sheet->mergeCells('B4:U4');
		$sheet->setCellValue('A5', 'Source');					$sheet->setCellValue('B5', ': '.$sourceName);				$sheet->mergeCells('B5:U5');
		$sheet->setCellValue('A6', 'Date Period');				$sheet->setCellValue('B6', ': '.$dateRangeStr);				$sheet->mergeCells('B6:U6');
		$sheet->setCellValue('A7', 'Keyword');					$sheet->setCellValue('B7', ': '.$keywordSearch);			$sheet->mergeCells('B7:U7');
		$sheet->setCellValue('A8', 'Include Collect Payment');	$sheet->setCellValue('B8', ': '.$includeCollectPaymentStr);	$sheet->mergeCells('B8:U8');
		$sheet->setCellValue('A9', 'Include Additional Cost');	$sheet->setCellValue('B9', ': '.$includeAdditionalCostStr);	$sheet->mergeCells('B9:U9');
		
		$sheet
		->setCellValue('A11', 'Reservation Description')
		->setCellValue('G11', 'Customer Details)')
		->setCellValue('J11', 'Total Income'.PHP_EOL.'(Reservation)')
		->setCellValue('K11', 'Detail Income (Finance)')
		->setCellValue('N11', 'Total Income (Finance)')
		->setCellValue('O11', 'Cost Details')
		->setCellValue('T11', 'Total Costs')
		->setCellValue('U11', 'Margins');
		
		$sheet
		->mergeCells('A11:F11')
		->mergeCells('G11:I11')
		->mergeCells('J11:J12')
		->mergeCells('K11:M11')
		->mergeCells('N11:N12')
		->mergeCells('O11:S11')
		->mergeCells('T11:T12')
		->mergeCells('U11:U12');
		
		$sheet->setCellValue('A12', 'Reservation Type')
		->setCellValue('B12', 'Reservation Title')
		->setCellValue('C12', 'Source')
		->setCellValue('D12', 'Booking Code')
		->setCellValue('E12', 'Date')
		->setCellValue('F12', 'Status')
		->setCellValue('G12', 'Guest Name')
		->setCellValue('H12', 'Contact')
		->setCellValue('I12', 'Email')
		->setCellValue('K12', 'Payment Method')
		->setCellValue('L12', 'Status')
		->setCellValue('M12', 'Payment Nominal')
		->setCellValue('O12', 'Date')
		->setCellValue('P12', 'Cost Type')
		->setCellValue('Q12', 'Vendor')
		->setCellValue('R12', 'Product')
		->setCellValue('S12', 'Cost Nominal');
		
		$sheet->getStyle('A11:U12')->getFont()->setBold( true );
		$sheet->getStyle('A11:U12')->getAlignment()->setHorizontal('center')->setVertical('center');
		$rowNumber			=	13;
		$grandTotalIncome	=	$grandTotalPaymentFinance	=	$grandTotalCost	=	$grandTotalMargin	=	0;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		foreach($dataTable['data'] as $data){
			$reservationDateEnd		=	$data->RESERVATIONDATEEND != $data->RESERVATIONDATESTART ? "\nTo\n".$data->RESERVATIONDATEEND." ".$data->RESERVATIONTIMEEND : "";
			$reservationStatusStr	=	$driverHandleStr		=	$ticketHandleStr	=	$carHandleStr	=	"";
			
			switch($data->STATUS){
				case "-1"	:	$reservationStatusStr	=	'Cancel'; break;
				case "0"	:	$reservationStatusStr	=	'Unprocessed'; break;
				case "1"	:	$reservationStatusStr	=	'Admin Processed'; break;
				case "2"	:	$reservationStatusStr	=	'Scheduled'; break;
				case "3"	:	$reservationStatusStr	=	'On Process'; break;
				case "4"	:	$reservationStatusStr	=	'Done'; break;
				default		:	$reservationStatusStr	=	'Unprocessed'; break;
			}
			
			$sheet
			->setCellValue('A'. $rowNumber, $data->RESERVATIONTYPE)
			->setCellValue('B'. $rowNumber, $data->RESERVATIONTITLE)
			->setCellValue('C'. $rowNumber, $data->SOURCENAME)
			->setCellValue('D'. $rowNumber, $data->BOOKINGCODE)
			->setCellValue('E'. $rowNumber, $data->RESERVATIONDATESTART . " " . $data->RESERVATIONTIMESTART . $reservationDateEnd)
			->setCellValue('F'. $rowNumber, $reservationStatusStr)
			->setCellValue('G'. $rowNumber, $data->CUSTOMERNAME)
			->setCellValue('H'. $rowNumber, ' ' . $data->CUSTOMERCONTACT)
			->setCellValue('I'. $rowNumber, $data->CUSTOMEREMAIL)
			->setCellValue('J'. $rowNumber, $data->INCOMEAMOUNTIDR);
			
			$rowSpanNumber			=	$rowSpanNumberFinance	=	$rowSpanNumberCost	=	0;
			$rowNumberMergeStart	=	$rowNumberFinance	=	$rowNumberCost	=	$rowNumber;
			$incomeMargin			=	$data->INCOMEAMOUNTIDR;
			$grandTotalIncome		+=	$data->INCOMEAMOUNTIDR;
			$totalPaymentFinance	=	$totalCost	=	0;
			
			if(isset($data->DETAILSPAYMENTFINANCE) && $data->DETAILSPAYMENTFINANCE != ""){
				$splitFinancePayment	=	explode("|", $data->DETAILSPAYMENTFINANCE);
				$rowSpanNumberFinance	=	count($splitFinancePayment);
				$iSplit					=	0;
				
				foreach($splitFinancePayment as $financePayment){
					$dataPayment		=	explode("]", $splitFinancePayment[$iSplit]);
					$paymentStatus		=	$dataPayment[0];
					$paymentMethod		=	$dataPayment[1];
					$paymentNominal		=	$dataPayment[2];
					$paymentStatusStr	=	'-';
					
					switch($paymentStatus){
						case "-1"	:	$paymentStatusStr	=	'Cancel'; break;
						case "0"	:	$paymentStatusStr	=	'Unpaid'; break;
						case "1"	:	$paymentStatusStr	=	'Paid'; break;
						default		:	$paymentStatusStr	=	'Not Set'; break;
					}
					
					$sheet
					->setCellValue('K'.$rowNumberFinance, $paymentMethod)
					->setCellValue('L'.$rowNumberFinance, $paymentStatusStr)
					->setCellValue('M'.$rowNumberFinance, $paymentNominal);
					
					$totalPaymentFinance		+=	$paymentNominal;
					$grandTotalPaymentFinance	+=	$paymentNominal;

					if($rowSpanNumberFinance != $iSplit + 1) $rowNumberFinance++;
					$iSplit++;
				}
			}
			
			if(isset($data->DETAILSPRODUCTTYPE) && $data->DETAILSPRODUCTTYPE != ""){
				$splitProductType			=	explode("|", $data->DETAILSPRODUCTTYPE);
				$splitProductVendorDriver	=	explode("|", $data->DETAILSPRODUCTVENDORDRIVER);
				$splitProductName			=	explode("|", $data->DETAILSPRODUCTNAME);
				$splitProductDate			=	explode("|", $data->DETAILSPRODUCTDATE);
				$splitProductCost			=	explode("|", $data->DETAILSPRODUCTCOST);
				$rowSpanNumberCost			=	count($splitProductType);
				$iSplit						=	0;
				
				foreach($splitProductType as $productType){
					$productDate		=	explode("=", $splitProductDate[$iSplit])[0];
					$productType		=	explode("=", $splitProductType[$iSplit])[0];
					$productVendorDriver=	explode("=", $splitProductVendorDriver[$iSplit])[0];
					$productName		=	explode("=", $splitProductName[$iSplit])[0];
					$productCost		=	explode("=", $splitProductCost[$iSplit])[0];					
					$incomeMargin		-=	$productCost * 1;
					$grandTotalCost		+=	$productCost * 1;
					$totalCost			+=	$productCost * 1;

					$sheet
					->setCellValue('O'. $rowNumber, $productDate)
					->setCellValue('P'. $rowNumber, $productType)
					->setCellValue('Q'. $rowNumber, $productVendorDriver)
					->setCellValue('R'. $rowNumber, $productName)
					->setCellValue('S'. $rowNumber, $productCost);
					
					if($rowSpanNumberCost != $iSplit + 1) $rowNumberCost++;
					$iSplit++;
				}
			}
			
			$rowNumber		=	$rowNumberFinance > $rowNumberCost ? $rowNumberFinance : $rowNumberCost;
			$rowSpanNumber	=	$rowSpanNumberCost > $rowSpanNumberFinance ? $rowSpanNumberCost : $rowSpanNumberFinance;
			
			if($rowNumberFinance > $rowNumberCost){
				$sheet
				->mergeCells('O'.$rowNumberCost.':O'.$rowNumberFinance)
				->mergeCells('P'.$rowNumberCost.':P'.$rowNumberFinance)
				->mergeCells('Q'.$rowNumberCost.':Q'.$rowNumberFinance)
				->mergeCells('R'.$rowNumberCost.':R'.$rowNumberFinance)
				->mergeCells('S'.$rowNumberCost.':S'.$rowNumberFinance);
			}
			
			if($rowNumberCost > $rowNumberFinance){
				$sheet
				->mergeCells('K'.$rowNumberFinance.':K'.$rowNumberCost)
				->mergeCells('L'.$rowNumberFinance.':L'.$rowNumberCost)
				->mergeCells('M'.$rowNumberFinance.':M'.$rowNumberCost);
			}
			
			$sheet
			->setCellValue('N'.$rowNumberMergeStart, $totalPaymentFinance)
			->setCellValue('T'.$rowNumberMergeStart, $totalCost)
			->setCellValue('U'.$rowNumberMergeStart, $incomeMargin);
			$grandTotalMargin	+=	$incomeMargin;
					
			if($rowSpanNumber > 1){
				$sheet
				->mergeCells('A'.$rowNumberMergeStart.':A'.$rowNumber)
				->mergeCells('B'.$rowNumberMergeStart.':B'.$rowNumber)
				->mergeCells('C'.$rowNumberMergeStart.':C'.$rowNumber)
				->mergeCells('D'.$rowNumberMergeStart.':D'.$rowNumber)
				->mergeCells('E'.$rowNumberMergeStart.':E'.$rowNumber)
				->mergeCells('F'.$rowNumberMergeStart.':F'.$rowNumber)
				->mergeCells('G'.$rowNumberMergeStart.':G'.$rowNumber)
				->mergeCells('H'.$rowNumberMergeStart.':H'.$rowNumber)
				->mergeCells('I'.$rowNumberMergeStart.':I'.$rowNumber)
				->mergeCells('J'.$rowNumberMergeStart.':J'.$rowNumber)
				->mergeCells('N'.$rowNumberMergeStart.':N'.$rowNumber)
				->mergeCells('T'.$rowNumberMergeStart.':T'.$rowNumber)
				->mergeCells('U'.$rowNumberMergeStart.':U'.$rowNumber);
			}
			
			$sheet->getStyle('J'.$rowNumberMergeStart)->getFont()->setBold( true );
			$sheet->getStyle('N'.$rowNumberMergeStart)->getFont()->setBold( true );
			$sheet->getStyle('T'.$rowNumberMergeStart)->getFont()->setBold( true );
			$sheet->getStyle('U'.$rowNumberMergeStart)->getFont()->setBold( true );
			
			if($data->INCOMEAMOUNTIDR != $totalPaymentFinance){
				$sheet->getStyle('J'.$rowNumberMergeStart)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('e37452');
				$sheet->getStyle('N'.$rowNumberMergeStart)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('e37452');
			}
			
			$rowNumber++;
		}
				
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':I'.$rowNumber);
		$sheet->mergeCells('K'.$rowNumber.':M'.$rowNumber);
		$sheet->mergeCells('O'.$rowNumber.':S'.$rowNumber);

		$sheet->setCellValue('J'.$rowNumber, $grandTotalIncome);		$sheet->getStyle('J'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('J'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('N'.$rowNumber, $grandTotalPaymentFinance);$sheet->getStyle('N'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('N'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('T'.$rowNumber, $grandTotalCost);			$sheet->getStyle('T'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('T'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('U'.$rowNumber, $grandTotalMargin);		$sheet->getStyle('U'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('U'.$rowNumber)->getFont()->setBold( true );

		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A11:U'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('U'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		if($grandTotalIncome != $grandTotalPaymentFinance){
			$sheet->getStyle('J'.$rowNumber)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('e37452');
			$sheet->getStyle('N'.$rowNumber)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('e37452');
		}
		
		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(12);
		$sheet->getColumnDimension('D')->setWidth(16);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(12);
		$sheet->getColumnDimension('G')->setWidth(28);
		$sheet->getColumnDimension('H')->setWidth(16);
		$sheet->getColumnDimension('I')->setWidth(24);
		$sheet->getColumnDimension('J')->setWidth(15);
		$sheet->getColumnDimension('K')->setWidth(24);
		$sheet->getColumnDimension('L')->setWidth(12);
		$sheet->getColumnDimension('M')->setWidth(15);
		$sheet->getColumnDimension('N')->setWidth(15);
		$sheet->getColumnDimension('O')->setWidth(16);
		$sheet->getColumnDimension('P')->setWidth(12);
		$sheet->getColumnDimension('Q')->setWidth(20);
		$sheet->getColumnDimension('R')->setWidth(30);
		$sheet->getColumnDimension('S')->setWidth(15);
		$sheet->getColumnDimension('T')->setWidth(15);
		$sheet->getColumnDimension('U')->setWidth(15);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelReportReservationIncome_'.$startDateStr.'_'.$endDateStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
	
	public function getDataRecapReservationIncome(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelDetailReservationIncome');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$reportType		=	validatePostVar($this->postVar, 'reportType', true);
		$idSource		=	validatePostVar($this->postVar, 'idSource', false);
		$startDate		=	validatePostVar($this->postVar, 'startDate', true);
		$endDate		=	validatePostVar($this->postVar, 'endDate', true);
		$dataTable		=	$this->ModelDetailReservationIncome->getDataRecapReservationIncome($page, 25, $reportType, $idSource, $startDate, $endDate);
		$urlExcelRecap	=	"";
		
		if(count($dataTable['data']) > 0){
			$urlExcelRecap	=	BASE_URL."finance/detailReservationIncome/excelRecap/".base64_encode(encodeStringKeyFunction($reportType."|".$idSource."|".$startDate."|".$endDate, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelRecap"=>$urlExcelRecap));
	}
	
	public function excelRecap($encryptedVar){
		$this->load->model('Finance/ModelDetailReservationIncome');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$reportType		=	$expDecryptedVar[0];
		$idSource		=	$expDecryptedVar[1];
		$startDate		=	$expDecryptedVar[2];
		$endDate		=	$expDecryptedVar[3];

		if($startDate == "" && $endDate != ""){
			$startDate	=	$endDate;
		}
		
		if($startDate != "" && $endDate == ""){
			$endDate	=	$startDate;
		}
		
		$dataTable		=	$this->ModelDetailReservationIncome->getDataRecapReservationIncome(1, 999999, $reportType, $idSource, $startDate, $endDate);

		if(!$dataTable){
			echo "No data found!";
			die();
		}
		
		$spreadsheet	=	new Spreadsheet();
		$sheet			=	$spreadsheet->getActiveSheet();
		$reportTypeStr	=	'-';
		$sourceNameStr	=	isset($idSource) && $idSource != '' && $idSource != 0 ? $this->MainOperation->getSourceNameById($idSource) : "All Source";
		$startDateDT	=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDateStr	=	$startDateDT->format('d M Y');
		$endDateDT		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDateStr		=	$endDateDT->format('d M Y');
		
		switch($reportType){
			case 1	:	$reportTypeStr	=	'Recap Per Source'; break;
			case 2	:	$reportTypeStr	=	'Recap Per Month & Source'; break;
			default	:	$reportTypeStr	=	'-';
		}
		
		$sheet->getPageSetup()->setOrientation(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		$sheet->getPageSetup()->setPaperSize(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(0.25);
		$sheet->getPageMargins()->setRight(0.2);
		$sheet->getPageMargins()->setLeft(0.2);
		$sheet->getPageMargins()->setBottom(0.25);
		
		$sheet->setCellValue('A1', 'Bali Sun Tours');			$sheet->mergeCells('A1:D1');
		$sheet->setCellValue('A2', 'Recap Income Per Source');	$sheet->mergeCells('A2:D2');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		
		$sheet->setCellValue('A4', 'Report Type');	$sheet->setCellValue('B4', ': '.$reportTypeStr);					$sheet->mergeCells('B4:D4');
		$sheet->setCellValue('A5', 'Source');		$sheet->setCellValue('B5', ': '.$sourceNameStr);					$sheet->mergeCells('B5:D5');
		$sheet->setCellValue('A6', 'Period');		$sheet->setCellValue('B6', ': '.$startDateStr." - ".$endDateStr);	$sheet->mergeCells('B6:D6');
		
		$sheet->setCellValue('A8', 'Period');
		$sheet->setCellValue('B8', 'Source');
		$sheet->setCellValue('C8', 'Total Reservatiion');
		$sheet->setCellValue('D8', 'Total Income');
		$sheet->getStyle('A8:D8')->getFont()->setBold( true );
		$sheet->getStyle('A8:D8')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A8:D8')->getAlignment()->setVertical('center');
		$rowNumber	=	9;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$grandTotalReservation = $grandTotalIncome =	0;
		foreach($dataTable['data'] as $data){
			$grandTotalReservation	+=	$data->TOTALRESERVATION;
			$grandTotalIncome		+=	$data->TOTALINCOME;
			
			$sheet->setCellValue('A'.$rowNumber, $data->PERIOD);
			$sheet->setCellValue('B'.$rowNumber, $data->SOURCENAME);
			$sheet->setCellValue('C'.$rowNumber, $data->TOTALRESERVATION);	$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('D'.$rowNumber, $data->TOTALINCOME);		$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');
			$rowNumber++;
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':B'.$rowNumber);
		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('C'.$rowNumber, $grandTotalReservation);	$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('C'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('D'.$rowNumber, $grandTotalIncome);		$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('D'.$rowNumber)->getFont()->setBold( true );
		
		$sheet->getStyle('A8:D'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('E'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(30);
		$sheet->getColumnDimension('B')->setWidth(24);
		$sheet->getColumnDimension('C')->setWidth(16);
		$sheet->getColumnDimension('D')->setWidth(16);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelRecapReservationIncome_'.$reportTypeStr.'_'.$sourceNameStr.'_'.$startDateStr.'_'.$endDateStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
	
	public function getDataRecapPerYear(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelDetailReservationIncome');
		
		$year			=	validatePostVar($this->postVar, 'year', true);
		$idSource		=	validatePostVar($this->postVar, 'idSource', false);
		$dataTable		=	$this->ModelDetailReservationIncome->getDataRecapPerYear($year, $idSource);
		$urlExcelRecap	=	"";
		
		if($dataTable){
			$urlExcelRecap	=	BASE_URL."finance/detailReservationIncome/excelRecapPerYear/".base64_encode(encodeStringKeyFunction($year."|".$idSource, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelRecap"=>$urlExcelRecap));
	}
	
	public function excelRecapPerYear($encryptedVar){
		$this->load->model('Finance/ModelDetailReservationIncome');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$year			=	$expDecryptedVar[0];
		$idSource		=	$expDecryptedVar[1];
		$dataTable		=	$this->ModelDetailReservationIncome->getDataRecapPerYear($year, $idSource);

		if(!$dataTable){
			echo "No data found!";
			die();
		}
		
		$spreadsheet	=	new Spreadsheet();
		$sheet			=	$spreadsheet->getActiveSheet();
		$sourceNameStr	=	isset($idSource) && $idSource != '' && $idSource != 0 ? $this->MainOperation->getSourceNameById($idSource) : "All Source";
		
		$sheet->getPageSetup()->setOrientation(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		$sheet->getPageSetup()->setPaperSize(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(0.25);
		$sheet->getPageMargins()->setRight(0.2);
		$sheet->getPageMargins()->setLeft(0.2);
		$sheet->getPageMargins()->setBottom(0.25);
		
		$sheet->setCellValue('A1', 'Bali Sun Tours');							$sheet->mergeCells('A1:F1');
		$sheet->setCellValue('A2', 'Recap Income, Cost and Margin Per Year');	$sheet->mergeCells('A2:F2');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		
		$sheet->setCellValue('A4', 'Year Period');	$sheet->setCellValue('B4', ': '.$year);			$sheet->mergeCells('B4:F4');
		$sheet->setCellValue('A5', 'Source');		$sheet->setCellValue('B5', ': '.$sourceNameStr);$sheet->mergeCells('B5:F5');
		
		$sheet->setCellValue('A7', 'Month Year');
		$sheet->setCellValue('B7', 'Total Active Reservation');
		$sheet->setCellValue('C7', 'Total Income (Reservation)');
		$sheet->setCellValue('D7', 'Total Income (Finance)');
		$sheet->setCellValue('E7', 'Total Cost');
		$sheet->setCellValue('F7', 'Total Margin');
		$sheet->getStyle('A7:F7')->getFont()->setBold( true );
		$sheet->getStyle('A7:F7')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A7:F7')->getAlignment()->setVertical('center');
		$rowNumber	=	8;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$totalPeriod	=	0;
		$grandTotalActiveReservation = $grandTotalIncomeReservation =	$grandTotalIncomeFinance =	$grandTotalCost =	$grandTotalMargin =	0;
		foreach($dataTable as $data){
			$totalMargin					=	$data->TOTALINCOMEFINANCE - $data->TOTALCOST;
			$grandTotalActiveReservation	+=	$data->TOTALRESERVATION;
			$grandTotalIncomeReservation	+=	$data->TOTALINCOMERESERVATION;
			$grandTotalIncomeFinance		+=	$data->TOTALINCOMEFINANCE;
			$grandTotalCost					+=	$data->TOTALCOST;
			$grandTotalMargin				+=	$totalMargin;
			
			$sheet->setCellValue('A'.$rowNumber, $data->PERIOD);
			$sheet->setCellValue('B'.$rowNumber, $data->TOTALRESERVATION);			$sheet->getStyle('B'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('C'.$rowNumber, $data->TOTALINCOMERESERVATION);	$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('D'.$rowNumber, $data->TOTALINCOMEFINANCE);		$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('E'.$rowNumber, $data->TOTALCOST);					$sheet->getStyle('E'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('F'.$rowNumber, $totalMargin);						$sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');
			$rowNumber++;
			$totalPeriod++;
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('B'.$rowNumber, $grandTotalActiveReservation);	$sheet->getStyle('B'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('B'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('C'.$rowNumber, $grandTotalIncomeReservation);	$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('C'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('D'.$rowNumber, $grandTotalIncomeFinance);		$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('D'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('E'.$rowNumber, $grandTotalCost);				$sheet->getStyle('E'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('E'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('F'.$rowNumber, $grandTotalMargin);			$sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('F'.$rowNumber)->getFont()->setBold( true );

		$rowNumber++;
		$sheet->setCellValue('A'.$rowNumber, 'AVERAGE');
		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('B'.$rowNumber, $grandTotalActiveReservation / $totalPeriod);	$sheet->getStyle('B'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('B'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('C'.$rowNumber, $grandTotalIncomeReservation / $totalPeriod);	$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('C'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('D'.$rowNumber, $grandTotalIncomeFinance / $totalPeriod);		$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('D'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('E'.$rowNumber, $grandTotalCost / $totalPeriod);				$sheet->getStyle('E'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('E'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('F'.$rowNumber, $grandTotalMargin / $totalPeriod);				$sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('F'.$rowNumber)->getFont()->setBold( true );
		
		$sheet->getStyle('A7:F'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('G'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->getColumnDimension('B')->setWidth(16);
		$sheet->getColumnDimension('C')->setWidth(16);
		$sheet->getColumnDimension('D')->setWidth(16);
		$sheet->getColumnDimension('E')->setWidth(16);
		$sheet->getColumnDimension('F')->setWidth(16);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelRecapReservationIncomeCostMargin_'.$year.'_'.$sourceNameStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
	
}