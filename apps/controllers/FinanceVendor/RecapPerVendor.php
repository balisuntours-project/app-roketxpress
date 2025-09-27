<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class RecapPerVendor extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(3);

		if($functionName != "uploadTransferReceiptDeposit" && $functionName != "uploadDocumentInvoiceManualWithdraw" && $functionName != "uploadExcelInvoice" && $_SERVER['REQUEST_METHOD'] === 'POST'){
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
	
	public function getDataAllVendorReport(){

		$this->load->model('FinanceVendor/ModelRecapPerVendor');

		$page					=	validatePostVar($this->postVar, 'page', true);
		$idVendorType			=	validatePostVar($this->postVar, 'idVendorType', false);
		$idVendor				=	validatePostVar($this->postVar, 'idVendor', false);
		$dataTable				=	$this->ModelRecapPerVendor->getDataAllVendorReport($page, 25, $idVendorType, $idVendor);
		$urlexcelAllVendorReport=	BASE_URL."financeVendor/recapPerVendor/excelAllVendorReport/".base64_encode(encodeStringKeyFunction($idVendorType."|".$idVendor, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlexcelAllVendorReport"=>$urlexcelAllVendorReport));
	
	}
	
	public function excelAllVendorReport($encryptedVar){

		$this->load->model('FinanceVendor/ModelRecapPerVendor');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$idVendorType	=	$expDecryptedVar[0];
		$idVendor		=	$expDecryptedVar[1];

		$vendorType		=	isset($idVendorType) && $idVendorType != "" && $idVendorType != 0 ? $this->MainOperation->getVendorTypeById($idVendorType) : "All Vendor Type";
		$vendorName		=	isset($idVendor) && $idVendor != "" && $idVendor != 0 ? $this->MainOperation->getVendorNameById($idVendor) : "All Vendor";
		$dataTable		=	$this->ModelRecapPerVendor->getDataAllVendorReport(1, 999999, $idVendorType, $idVendor);
		
		if(!$dataTable){
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
		$sheet->setCellValue('A2', 'All Vendor Finance Recap Report');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:H1');
		$sheet->mergeCells('A2:H2');
		
		$sheet->setCellValue('A4', 'Vendor Type : '.$vendorType);
		$sheet->setCellValue('A5', 'Vendor Name : '.$vendorName);
		
		$sheet->setCellValue('A7', 'Vendor Type');
		$sheet->setCellValue('B7', 'Vendor Name');
		$sheet->setCellValue('C7', 'Schedule');
		$sheet->setCellValue('D7', 'Fee');
		$sheet->setCellValue('E7', 'Collect Payment');
		$sheet->setCellValue('F7', 'Grand Total');
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
		
		$grandTotalSchedule = $grandTotalFee = $grandTotalCollectPayment = $grandTotalAll	=	0;
		foreach($dataTable['data'] as $data){
			
			$grandTotalPerRow			=	$data->TOTALFEE - $data->TOTALCOLLECTPAYMENT;
			$grandTotalSchedule			+=	$data->TOTALSCHEDULE;
			$grandTotalFee				+=	$data->TOTALFEE;
			$grandTotalCollectPayment	+=	$data->TOTALCOLLECTPAYMENT;
			$grandTotalAll				+=	$grandTotalPerRow;
			
			$sheet->setCellValue('A'.$rowNumber, $data->VENDORTYPE);
			$sheet->setCellValue('B'.$rowNumber, $data->VENDORNAME);
			$sheet->setCellValue('C'.$rowNumber, $data->TOTALSCHEDULE);			$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('D'.$rowNumber, $data->TOTALFEE);				$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('E'.$rowNumber, $data->TOTALCOLLECTPAYMENT);	$sheet->getStyle('E'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('F'.$rowNumber, $grandTotalPerRow);			$sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');
			$rowNumber++;
			
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':B'.$rowNumber);
		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('C'.$rowNumber, $grandTotalSchedule);		$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('C'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('D'.$rowNumber, $grandTotalFee);			$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('D'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('E'.$rowNumber, $grandTotalCollectPayment);$sheet->getStyle('E'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('E'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('F'.$rowNumber, $grandTotalAll);			$sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('F'.$rowNumber)->getFont()->setBold( true );
		
		$sheet->getStyle('A7:F'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('F'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(18);
		$sheet->getColumnDimension('B')->setWidth(24);
		$sheet->getColumnDimension('C')->setWidth(12);
		$sheet->getColumnDimension('D')->setWidth(12);
		$sheet->getColumnDimension('E')->setWidth(12);
		$sheet->getColumnDimension('F')->setWidth(12);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelVendorFinanceRecap_'.$vendorType.'_'.$vendorName;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}
	
	public function getDataRecapPerVendor(){

		$this->load->model('FinanceVendor/ModelRecapPerVendor');

		$page					=	validatePostVar($this->postVar, 'page', true);
		$idVendorType			=	validatePostVar($this->postVar, 'idVendorType', false);
		$idVendor				=	validatePostVar($this->postVar, 'idVendor', false);
		$startDate				=	validatePostVar($this->postVar, 'startDate', true);
		$endDate				=	validatePostVar($this->postVar, 'endDate', true);
		$startDate				=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate				=	$startDate->format('Y-m-d');
		$endDate				=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate				=	$endDate->format('Y-m-d');
		$dataTable				=	$this->ModelRecapPerVendor->getDataRecapPerVendor($page, 25, $idVendorType, $idVendor, $startDate, $endDate);
		$urlExcelRecapPerVendor	=	BASE_URL."financeVendor/recapPerVendor/excelRecapPerVendor/".base64_encode(encodeStringKeyFunction($idVendorType."|".$idVendor."|".$startDate."|".$endDate, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		
		if(count($dataTable['data']) > 0){
			foreach($dataTable['data'] as $data){
				if($data->TOTALSCHEDULE > 0){
					$idVendorTicket			=	$data->IDVENDOR;
					$data->URLEXCELDETAILFEE=	BASE_URL."financeVendor/detailFeeVendor/excelDetailFeeVendorTicket/".base64_encode(encodeStringKeyFunction($idVendorTicket."|".$startDate."|".$endDate, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
				}
			}
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelRecapPerVendor"=>$urlExcelRecapPerVendor));
	
	}
	
	public function excelRecapPerVendor($encryptedVar){

		$this->load->model('FinanceVendor/ModelRecapPerVendor');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$idVendorType	=	$expDecryptedVar[0];
		$idVendor		=	$expDecryptedVar[1];
		$startDate		=	$expDecryptedVar[2];
		$endDate		=	$expDecryptedVar[3];

		if($startDate == "" && $endDate != ""){
			$startDate	=	$endDate;
		}
		
		if($startDate != "" && $endDate == ""){
			$endDate	=	$startDate;
		}
		
		$startDateDT	=	DateTime::createFromFormat('Y-m-d', $startDate);
		$startDateStr	=	$startDateDT->format('d M Y');
		$endDateDT		=	DateTime::createFromFormat('Y-m-d', $endDate);
		$endDateStr		=	$endDateDT->format('d M Y');
		$vendorType		=	isset($idVendorType) && $idVendorType != "" && $idVendorType != 0 ? $this->MainOperation->getVendorTypeById($idVendorType) : "All Vendor Type";
		$vendorName		=	isset($idVendor) && $idVendor != "" && $idVendor != 0 ? $this->MainOperation->getVendorNameById($idVendor) : "All Vendor";
		$dateRangeStr	=	$startDateStr." to ".$endDateStr;
		$dataTable		=	$this->ModelRecapPerVendor->getDataRecapPerVendor(1, 999999, $idVendorType, $idVendor, $startDate, $endDate);
		
		if(!$dataTable){
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
		$sheet->setCellValue('A2', 'Vendor Finance Report - Recap');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:F1');
		$sheet->mergeCells('A2:F2');
		
		$sheet->setCellValue('A4', 'Vendor Type : '.$vendorType);
		$sheet->setCellValue('A5', 'Vendor Name : '.$vendorName);
		$sheet->setCellValue('A6', 'Date Period : '.$dateRangeStr);
		
		$sheet->setCellValue('A8', 'Vendor Type');
		$sheet->setCellValue('B8', 'Vendor Name');
		$sheet->setCellValue('C8', 'Total Schedule');
		$sheet->setCellValue('D8', 'Total Fee');
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
		
		$grandTotalSchedule = $grandTotalFee = $grandTotalAll	=	0;
		foreach($dataTable['data'] as $data){
			
			$grandTotalSchedule			+=	$data->TOTALSCHEDULE;
			$grandTotalFee				+=	$data->TOTALFEE;
			
			$sheet->setCellValue('A'.$rowNumber, $data->VENDORTYPE);
			$sheet->setCellValue('B'.$rowNumber, $data->VENDORNAME);
			$sheet->setCellValue('C'.$rowNumber, $data->TOTALSCHEDULE);	$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('D'.$rowNumber, $data->TOTALFEE);		$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');
			$rowNumber++;
			
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':B'.$rowNumber);
		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('C'.$rowNumber, $grandTotalSchedule);	$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('C'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('D'.$rowNumber, $grandTotalFee);		$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('D'.$rowNumber)->getFont()->setBold( true );
		
		$sheet->getStyle('A8:D'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('F'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(18);
		$sheet->getColumnDimension('B')->setWidth(24);
		$sheet->getColumnDimension('C')->setWidth(12);
		$sheet->getColumnDimension('D')->setWidth(12);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelVendorFinanceRecap_'.$vendorType.'_'.$vendorName.'_'.$startDateStr.'_'.$endDateStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}
	
	public function getDataPerVendorRecap(){

		$this->load->model('FinanceVendor/ModelRecapPerVendor');
		
		$idVendor			=	validatePostVar($this->postVar, 'idVendor', true);
		$startDateDeposit	=	validatePostVar($this->postVar, 'startDateDeposit', true);
		$endDateDeposit		=	validatePostVar($this->postVar, 'endDateDeposit', true);
		$startDateDeposit	=	DateTime::createFromFormat('d-m-Y', $startDateDeposit);
		$startDateDeposit	=	$startDateDeposit->format('Y-m-d');
		$endDateDeposit		=	DateTime::createFromFormat('d-m-Y', $endDateDeposit);
		$endDateDeposit		=	$endDateDeposit->format('Y-m-d');
		$detailVendor		=	$this->ModelRecapPerVendor->getDetailVendor($idVendor);
		
		if(!$detailVendor){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Vendor details not found!"));
		}
		
		$dataBankAccount				=	$this->ModelRecapPerVendor->getDataActiveBankAccountVendor($idVendor);
		$dataRecapPerVendor				=	$this->ModelRecapPerVendor->getDataRecapPerVendorDetail($idVendor);
		$dataListFee					=	$this->ModelRecapPerVendor->getDataListFee($idVendor);
		$dataListCollectPayment			=	$this->ModelRecapPerVendor->getDataListCollectPayment($idVendor);
		$dataListDepositHistory			=	$this->ModelRecapPerVendor->getDataListDepositHistory($idVendor, $startDateDeposit, $endDateDeposit);
		$initialName					=	preg_match_all('/\b\w/', $detailVendor['NAME'], $matches);
		$initialName					=	implode('', $matches[0]);
		$detailVendor['INITIALNAME']	=	strtoupper($initialName);

		setResponseOk(
			array(
				"token"						=>	$this->newToken,
				"detailVendor"				=>	$detailVendor,
				"dataBankAccount"			=>	$dataBankAccount,
				"dataRecapPerVendor"		=>	$dataRecapPerVendor,
				"dataListFee"				=>	$dataListFee,
				"dataListCollectPayment"	=>	$dataListCollectPayment,
				"dataListDepositHistory"	=>	$dataListDepositHistory
			)
		);
	
	}
	
	public function getDetailManualWithdraw(){
		$this->load->model('FinanceVendor/ModelRecapPerVendor');
		
		$idVendor		=	validatePostVar($this->postVar, 'idVendor', true);
		$detailVendor	=	$this->ModelRecapPerVendor->getDetailVendor($idVendor);
		
		if(!$detailVendor) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Vendor details not found!"));

		$dataBankAccount				=	$this->ModelRecapPerVendor->getDataActiveBankAccountVendor($idVendor);
		$listBankAccountVendor			=	$this->ModelRecapPerVendor->getDataBankAccountVendor($idVendor);
		$dataFeeCollectPayment			=	$this->ModelRecapPerVendor->getDataFeeCollectPaymentSchedule($idVendor);
		$initialName					=	preg_match_all('/\b\w/', $detailVendor['NAME'], $matches);
		$initialName					=	implode('', $matches[0]);
		$detailVendor['INITIALNAME']	=	strtoupper($initialName);
		$firstDateFeeCollectPayment		=	date('d-m-Y');
		
		if(count($dataFeeCollectPayment) > 0){
			$firstDateFeeCollectPayment	=	$dataFeeCollectPayment[0]->DATESTR;
			$firstDateFeeCollectPayment	=	DateTime::createFromFormat('d M Y', $firstDateFeeCollectPayment);
			$firstDateFeeCollectPayment	=	$firstDateFeeCollectPayment->format('d-m-Y');
		}

		setResponseOk(
			array(
				"token"						=>	$this->newToken,
				"detailVendor"				=>	$detailVendor,
				"dataBankAccount"			=>	$dataBankAccount,
				"listBankAccountVendor"		=>	$listBankAccountVendor,
				"dataFeeCollectPayment"		=>	$dataFeeCollectPayment,
				"firstDateFeeCollectPayment"=>	$firstDateFeeCollectPayment
			)
		);
	}
	
	public function saveNewBankAccountVendor(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceVendor/ModelRecapPerVendor');
		
		$idVendor				=	validatePostVar($this->postVar, 'idVendor', true);
		$idBank					=	validatePostVar($this->postVar, 'idBank', true);
		$bankName				=	validatePostVar($this->postVar, 'bankName', true);
		$accountNumber			=	validatePostVar($this->postVar, 'accountNumber', true);
		$accountHolderName		=	validatePostVar($this->postVar, 'accountHolderName', true);
		$isBankAccountExist		=	$this->ModelRecapPerVendor->isBankAccountExist($idBank, $accountNumber);
		$listBankAccountVendor	=	$this->ModelRecapPerVendor->getDataBankAccountVendor($idVendor);
		
		if($isBankAccountExist) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"The <b>".$bankName."</b> account - <b>".$accountNumber."</b> is already registered, please enter different data"));

		$arrInsert	=	array(
							"IDBANK"			=>	$idBank,
							"IDPARTNERTYPE"		=>	1,
							"IDPARTNER"			=>	$idVendor,
							"ACCOUNTNUMBER"		=>	$accountNumber,
							"ACCOUNTHOLDERNAME"	=>	$accountHolderName,
							"STATUS"			=>	1
						);
		$procInsert	=	$this->MainOperation->addData('t_bankaccountpartner', $arrInsert);
		
		if(!$procInsert['status']) switchMySQLErrorCode($procInsert['errCode'], $this->newToken);
		$idBankAccountPartner	=	$procInsert['insertID'];
		$detailBank				=	$this->MainOperation->getDetailBank($idBank);
		
		if($listBankAccountVendor){
			foreach($listBankAccountVendor as $keyBankAccountVendor){
				$idBankAccountPartnerUpdate	=	$keyBankAccountVendor->IDBANKACCOUNTPARTNER;
				$this->MainOperation->updateData('t_bankaccountpartner', ['STATUS' => 0], 'IDBANKACCOUNTPARTNER', $idBankAccountPartnerUpdate);
			}
		}
		
		setResponseOk(
			array(
				"token"					=>	$this->newToken,
				"idBankAccountPartner"	=>	$idBankAccountPartner,
				"detailBank"			=>	$detailBank,
				"msg"					=>	"New bank account vendor has been saved"
			)
		);
	}
		
	public function uploadDocumentInvoiceManualWithdraw($idVendor){
		if((($_FILES["file"]["type"] == "application/vnd.ms-excel")
			|| ($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
			|| ($_FILES["file"]["type"] == "application/msword")
			|| ($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document")
			|| ($_FILES["file"]["type"] == "application/pdf")
			|| ($_FILES["file"]["type"] == "image/jpeg")
			|| ($_FILES["file"]["type"] == "image/jpg")
			|| ($_FILES["file"]["type"] == "image/png"))
			&& ($_FILES["file"]["size"] <= 800000)){
			if ($_FILES["file"]["error"] > 0) {
				setResponseInternalServerError(array("msg"=>"Failed to upload this file. File is broken"));
			}
			
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. This file type is not allowed (".$_FILES["file"]["type"].") or file size is too big (".$_FILES["file"]["size"].")"));
		}
		
		$dir			=	PATH_MANUAL_WITHDRAW_VENDOR_DOCUMENT;
		$fileNameOrigin	=	pathinfo($_FILES["file"]["name"], PATHINFO_BASENAME);
		$extension		=	pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		$fileName		=	"manualWithdrawVendor"."_".$idVendor."_".date('YmdHis').".".$extension;
		$move			=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$fileName);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "fileName"=>$fileName, "extension"=>$extension, "fileNameOrigin"=>$fileNameOrigin));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}
	}
	
	public function uploadExcelInvoice($idVendor){
		if((($_FILES["file"]["type"] == "application/vnd.ms-excel")
			|| ($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
			|| ($_FILES["file"]["type"] == "application/msword")
			|| ($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document")
			|| ($_FILES["file"]["type"] == "application/pdf")
			|| ($_FILES["file"]["type"] == "image/jpeg")
			|| ($_FILES["file"]["type"] == "image/jpg")
			|| ($_FILES["file"]["type"] == "image/png"))
			&& ($_FILES["file"]["size"] <= 800000)){
			if ($_FILES["file"]["error"] > 0) {
				setResponseInternalServerError(array("msg"=>"Failed to upload this file. File is broken"));
			}
			
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. This file type is not allowed (".$_FILES["file"]["type"].") or file size is too big (".$_FILES["file"]["size"].")"));
		}
		
		$dir		=	PATH_TMP_FILE;
		$extension	=	pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		$fileName	=	"excelInvoiceVendor"."_".$idVendor."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$fileName);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "fileName"=>$fileName));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}
	}
	
	public function readExcelInvoiceVendor(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceVendor/ModelRecapPerVendor');
		
		$idVendor					=	validatePostVar($this->postVar, 'idVendor', true);
		$fileExcelName				=	validatePostVar($this->postVar, 'fileExcelName', true);
		$autoReduceCollectPayment	=	validatePostVar($this->postVar, 'autoReduceCollectPayment', false);
		$filePath					=	PATH_TMP_FILE.$fileExcelName;
		
		$fileType		=	\PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
		$reader			=	\PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
		$spreadsheet	=	$reader->load($filePath);
		$excelData		=	$spreadsheet->getActiveSheet()->toArray();
		$validData		=	$collectPaymentData	=	$invalidData	=	array();
		
		if(count($excelData) > 0){
			try {
				$rowNumber	=	1;
				foreach($excelData as $data){
					if($rowNumber > 1){
						$bookingCode		=	$data[0];
						$customerName		=	$data[1];
						$nominal			=	preg_replace('/\D/', '', $data[2]);
						$arrDataExcel		=	[
							"bookingCode"	=>	$bookingCode,
							"customerName"	=>	$customerName,
							"nominal"		=>	$nominal
						];

						if($bookingCode != '' && $customerName != ''){
							$detailReservation	=	$this->ModelRecapPerVendor->getDetailReservationVendor($idVendor, $bookingCode, $customerName, $nominal);
							
							if($detailReservation === false){
								$invalidData[]	=	[
									"arrDataExcel"		=>	$arrDataExcel,
									"arrDataSchedule"	=>	[],
									"errCode"			=>	-1,
									"errMessage"		=>	"Not handled by vendor"
								];
							} else {
								$idReservation			=	$detailReservation['IDRESERVATION'];
								$idReservationDetails	=	$detailReservation['IDRESERVATIONDETAILS'];
								$bookingCodeDB			=	$detailReservation['BOOKINGCODE'];
								$customerNameDB			=	$detailReservation['CUSTOMERNAME'];
								$withdrawStatus			=	$detailReservation['WITHDRAWSTATUS'];
								$dateSchedule			=	$detailReservation['SCHEDULEDATE'];
								$dateScheduleStr		=	$detailReservation['SCHEDULEDATESTR'];
								$productName			=	$detailReservation['PRODUCTNAME'];
								$nominalDB				=	$detailReservation['NOMINAL'];
								$nominalCollectPayment	=	0;
								$errCode				=	0;
								$errMessage				=	"";
								
								if(strtotime($dateSchedule) >= strtotime("today")){
									$errCode	=	-2;
									$errMessage	=	"Schedule more than/equal to today";
								}
									
								if($nominal != $nominalDB){									
									if($autoReduceCollectPayment == 1){
										$dataCollectInvoice	=	$this->ModelRecapPerVendor->getDataCollectPaymentInvoice($idReservation, $idVendor);
										
										if($dataCollectInvoice){
											$arrIdCollectPayment		=	explode(',', $dataCollectInvoice['STRARRIDCOLLECTPAYMENT']);
											$totalAmountCollectPayment	=	$dataCollectInvoice['TOTALAMOUNTCOLLECTPAYMENT'] * 1;

											if($nominal == ($nominalDB - $totalAmountCollectPayment)){
												$collectPaymentData		=	array_merge($collectPaymentData, $arrIdCollectPayment);
											} else {
												$errCode				=	-3;
												$errMessage				=	"Invalid nominal. Collect payment amount not match : Rp. ".number_format($totalAmountCollectPayment, 0, '.', ',');
												$nominalCollectPayment	=	$totalAmountCollectPayment;
											}
										} else {
											$errCode	=	-3;
											$errMessage	=	"Invalid nominal. No collect payment data";
										}
									} else {
										$errCode	=	-3;
										$errMessage	=	"Invalid nominal";
									}
								}
									
								if($withdrawStatus == 1){
									$errCode	=	-4;
									$errMessage	=	"Has been withdrawn";
								}
								
								if($errCode != 0){
									$invalidData[]	=	[
										"arrDataExcel"		=>	$arrDataExcel,
										"arrDataSchedule"	=>	[
											"idReservationDetails"	=>	$idReservationDetails,
											"bookingCodeDB"			=>	$bookingCodeDB,
											"customerNameDB"		=>	$customerNameDB,
											"withdrawStatus"		=>	$withdrawStatus,
											"dateScheduleStr"		=>	$dateScheduleStr,
											"productName"			=>	$productName,
											"nominalDB"				=>	$nominalDB,
											"nominalCollectPayment"	=>	$nominalCollectPayment,
											"nominalFinal"			=>	$nominalDB - $nominalCollectPayment
										],
										"errCode"		=>	$errCode,
										"errMessage"	=>	$errMessage
									];
								} else {
									$validData[]	=	$idReservationDetails;
								}
							}
						}
					}
					$rowNumber++;
				}
			} catch (Exception $e) {
				$this->sendErrorReadExcelInvoiceVendor();
			}
		}
		
		if(count($invalidData) > 0){
			setResponseForbidden(array("token"=>$this->newToken, "invalidData"=>$invalidData));
		} else {
			setResponseOk(array("token"=>$this->newToken, "validData"=>$validData, "collectPaymentData"=>$collectPaymentData));
		}
	}
	
	private function sendErrorReadExcelInvoiceVendor(){
		setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed. Please make sure the uploaded file is valid and the source selection and format type are correct"));
	}

	public function getDataListDepositHistory(){
		$this->load->model('FinanceVendor/ModelRecapPerVendor');
		
		$idVendor				=	validatePostVar($this->postVar, 'idVendor', true);
		$startDateDeposit		=	validatePostVar($this->postVar, 'startDateDeposit', true);
		$endDateDeposit			=	validatePostVar($this->postVar, 'endDateDeposit', true);
		$startDateDeposit		=	DateTime::createFromFormat('d-m-Y', $startDateDeposit);
		$startDateDeposit		=	$startDateDeposit->format('Y-m-d');
		$endDateDeposit			=	DateTime::createFromFormat('d-m-Y', $endDateDeposit);
		$endDateDeposit			=	$endDateDeposit->format('Y-m-d');
		$dataListDepositHistory	=	$this->ModelRecapPerVendor->getDataListDepositHistory($idVendor, $startDateDeposit, $endDateDeposit);
		
		if(!$dataListDepositHistory) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data deposit history found on selected date range!"));
		setResponseOk(
			array(
				"token"						=>	$this->newToken,
				"dataListDepositHistory"	=>	$dataListDepositHistory
			)
		);
	}
	
	public function uploadTransferReceiptDeposit($idVendor){
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
		
		$dir		=	PATH_TRANSFER_RECEIPT;
		$extension	=	pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		$namaFile	=	"depositVendor"."_".$idVendor."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$namaFile);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "urlTransferReceipt"=>URL_TRANSFER_RECEIPT.$namaFile, "transferReceiptFileName"=>$namaFile));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}
	}
	
	public function saveManualWithdrawVendor(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceVendor/ModelRecapPerVendor');
		
		$idVendor				=	validatePostVar($this->postVar, 'idVendor', true);
		$idBankAccountVendor	=	validatePostVar($this->postVar, 'idBankAccountVendor', true);
		$fileWithdrawDocument	=	validatePostVar($this->postVar, 'fileWithdrawDocument', true);
		$descriptionNotes		=	validatePostVar($this->postVar, 'descriptionNotes', true);
		$totalFee				=	validatePostVar($this->postVar, 'totalFee', false) * 1;
		$totalAdditionalCost	=	validatePostVar($this->postVar, 'totalAdditionalCost', false) * 1;
		$totalCollectPayment	=	validatePostVar($this->postVar, 'totalCollectPayment', false);
		$totalCollectPayment	=	$totalCollectPayment < 0 ? $totalCollectPayment * -1 : $totalCollectPayment;
		$totalDeduction			=	validatePostVar($this->postVar, 'totalDeduction', false);
		$totalDeduction			=	$totalDeduction < 0 ? $totalDeduction * -1 : $totalDeduction;
		$totalWithdrawalNominal	=	validatePostVar($this->postVar, 'totalWithdrawalNominal', false) * 1;
		$arrayWithdrawItem		=	validatePostVar($this->postVar, 'arrayWithdrawItem', true);
		$userAdminName			=	validatePostVar($this->postVar, 'NAME', true);
		$countWithdrawalNominal	=	$totalFee + $totalAdditionalCost - $totalCollectPayment - $totalDeduction;
		
		if($totalWithdrawalNominal != $countWithdrawalNominal) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"The submitted data is invalid. Please repeat the manual withdrawal application process"));
		$detailBankAccount		=	$this->ModelRecapPerVendor->getDetailBankAccountVendor($idBankAccountVendor);
		$idBank					=	$detailBankAccount['IDBANK'];
		$accountNumber			=	$detailBankAccount['ACCOUNTNUMBER'];
		$accountHolderName		=	$detailBankAccount['ACCOUNTHOLDERNAME'];
		$arrInsertRecap			=	array(
            "IDVENDOR"						=>	$idVendor,
            "IDDRIVER"						=>	0,
            "IDBANK"						=>	$idBank,
            "TOTALFEE"						=>	$totalFee,
            "TOTALADDITIONALCOST"			=>	$totalAdditionalCost,
            "TOTALREIMBURSEMENT"			=>	0,
            "TOTALREVIEWBONUSPUNISHMENT"	=>	0,
            "TOTALCOLLECTPAYMENT"			=>	$totalCollectPayment,
            "TOTALPREPAIDCAPITAL"			=>	0,
            "TOTALLOANCARINSTALLMENT"		=>	0,
            "TOTALLOANPERSONALINSTALLMENT"	=>	0,
            "TOTALCHARITY"					=>	0,
            "TOTALDEDUCTION"				=>	$totalDeduction,
            "TOTALWITHDRAWAL"				=>	$totalWithdrawalNominal,
            "MESSAGE"						=>	$descriptionNotes,
            "ACCOUNTNUMBER"					=>	$accountNumber,
            "ACCOUNTHOLDERNAME"				=>	$accountHolderName,
            "USERAPPROVAL"					=>	$userAdminName,
            "DATETIMEREQUEST"				=>	date('Y-m-d H:i:s'),
            "DATETIMEAPPROVAL"				=>	date('Y-m-d H:i:s'),
            "STATUSWITHDRAWAL"				=>	1
		);
		$procInsertRecap		=	$this->MainOperation->addData('t_withdrawalrecap', $arrInsertRecap);
		
		if(!$procInsertRecap['status']) switchMySQLErrorCode($procInsertRecap['errCode'], $this->newToken);
		$idWithdrawalRecap		=	$procInsertRecap['insertID'];
		
		foreach($arrayWithdrawItem as $keyWithdrawItem){
			$type		=	$keyWithdrawItem[0];
			$idData		=	$keyWithdrawItem[1];
			$nominal	=	$keyWithdrawItem[2];
			
			if($type == 1){
				$idReservationDetails	=	$idData;
				$isFeeExist				=	$this->ModelRecapPerVendor->isFeeExistByIdReservationDetail($idVendor, $idReservationDetails);
				$maxStatusProcess		=	$this->MainOperation->getMaxStatusProcess(1);
				$arrUpdateSchedule		=	[
					"STATUSPROCESS" =>  $maxStatusProcess,
					"STATUSCONFIRM" =>  1,
					"STATUS"        =>  3
				];
				$this->MainOperation->updateData('t_schedulevendor', $arrUpdateSchedule, ['IDRESERVATIONDETAILS' => $idReservationDetails, 'IDVENDOR' => $idVendor]);
				
				if(!$isFeeExist){
					$detailSchedule		=	$this->ModelRecapPerVendor->getDetailReservationSchedule($idReservationDetails);
					$idReservation		=	$detailSchedule['IDRESERVATION'];
					$dateScheduleDB		=	$detailSchedule['SCHEDULEDATE'];
					$reservationTitle	=	$detailSchedule['RESERVATIONTITLE'];
					$productName		=	$detailSchedule['PRODUCTNAME'];
					$feeNominal			=	$detailSchedule['NOMINAL'];
					$feeNotes			=	$detailSchedule['NOTES'];
					$arrInsertFee		=	[
						"IDRESERVATION"			=>	$idReservation,
						"IDRESERVATIONDETAILS"	=>	$idReservationDetails,
						"IDVENDOR"				=>	$idVendor,
						"IDDRIVER"				=>	0,
						"IDWITHDRAWALRECAP"		=>	$idWithdrawalRecap,
						"DATESCHEDULE"			=>	$dateScheduleDB,
						"RESERVATIONTITLE"		=>	$reservationTitle,
						"JOBTITLE"				=>	$productName,
						"FEENOMINAL"			=>	$feeNominal,
						"FEENOTES"				=>	$feeNotes,
						"USERAPPROVAL"			=>	$userAdminName,
						"DATETIMEINPUT"			=>	date('Y-m-d H:i:s'),
						"DATETIMEAPPROVAL"		=>	date('Y-m-d H:i:s'),
						"WITHDRAWSTATUS"		=>	1
					];
					if($idReservationDetails != 0) $this->MainOperation->addData("t_fee", $arrInsertFee);
				} else {
					$arrUpdateFee		=	[
						"IDWITHDRAWALRECAP"		=>	$idWithdrawalRecap,
						"USERAPPROVAL"			=>	$userAdminName,
						"DATETIMEAPPROVAL"		=>	date('Y-m-d H:i:s'),
						"WITHDRAWSTATUS"		=>	1
					];
					$this->MainOperation->updateData("t_fee", $arrUpdateFee, ["IDRESERVATIONDETAILS" => $idReservationDetails, "IDVENDOR" => $idVendor]);
				}
			} else if($type == 3) {
				$idCollectPayment		=	$idData;
				$idReservationPayment	=	$this->ModelRecapPerVendor->getIdReservationPaymentCollectPayment($idCollectPayment);
				$arrUpdateCollectPayment=	[
					'IDWITHDRAWALRECAP'			=>	$idWithdrawalRecap,
					'IDRESERVATIONPAYMENT'		=>	$idReservationPayment,
					"STATUS"					=>	1,
					"DATETIMESTATUS"			=>	date('Y-m-d H:i:s'),
					"STATUSSETTLEMENTREQUEST"	=>	2,
					"DATETIMESETTLEMENTREQUEST"	=>	date('Y-m-d H:i:s'),
					"LASTUSERINPUT"				=>	$userAdminName
				];
				$this->MainOperation->updateData("t_collectpayment", $arrUpdateCollectPayment, "IDCOLLECTPAYMENT", $idCollectPayment);
				
				$arrInsertCollectHistory	=	[
					"IDCOLLECTPAYMENT"	=>	$idCollectPayment,
					"DESCRIPTION"		=>	"Settlement has been approved along with the manual withdrawal",
					"USERINPUT"			=>	$userAdminName,
					"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
					"STATUS"			=>	3
				];
				$this->MainOperation->addData("t_collectpaymenthistory", $arrInsertCollectHistory);
				
				$arrUpdatePayment	=	[
					"STATUS"		=>	1,
					"DATETIMEUPDATE"=>	date('Y-m-d H:i:s'),
					"USERUPDATE"	=>	$userAdminName,
					"EDITABLE"		=>	0,
					"DELETABLE"		=>	0
				];
				$this->MainOperation->updateData("t_reservationpayment", $arrUpdatePayment, "IDRESERVATIONPAYMENT", $idReservationPayment);
			} else {
				$additionalCostDeductionDate		=	$keyWithdrawItem[3];
				$additionalCostDeductionDescription	=	$keyWithdrawItem[4];
				$costDeductionType					=	$type == 2 ? 1 : 2;
				$arrInsertAdditionalCostDeduction	=	[
					"IDWITHDRAWALRECAP"	=>	$idWithdrawalRecap,
					"COSTDEDUCTIONTYPE"	=>	$costDeductionType,
					"DATE"				=>	$additionalCostDeductionDate,
					"DESCRIPTION"		=>	$additionalCostDeductionDescription,
					"NOMINAL"			=>	$nominal
				];
				
				$this->MainOperation->addData('t_withdrawalcostdeduction', $arrInsertAdditionalCostDeduction);
			}
		}
		
		if($totalWithdrawalNominal > 0){
			$detailWithdrawal		=	$this->ModelRecapPerVendor->getDetailWithdrawalRequest($idWithdrawalRecap);
			$idBankTransfer			=	$detailWithdrawal['IDBANK'];
			$bankAccountNumber		=	$detailWithdrawal['ACCOUNTNUMBER'];
			$bankAccountHolderName	=	$detailWithdrawal['ACCOUNTHOLDERNAME'];
			$vendorEmail			=	$detailWithdrawal['VENDOREMAIL'];
			$transactioCode			=	"WD".date("dmyHi").str_pad($idVendor, 4, "0", STR_PAD_LEFT);
			$partnerCode			=	$this->MainOperation->getPartnerCode(1, $idVendor);
			$transferRemark			=	strtoupper("WITHDRAW ".date("d M y"));
			$emailList				=	MAILBOX_USERNAME.",".$vendorEmail;
			$arrInsertTransferList	=	[
				"IDPARTNERTYPE"		=>	1,
				"IDPARTNER"			=>	$idVendor,
				"IDBANK"			=>	$idBankTransfer,
				"IDWITHDRAWAL"		=>	$idWithdrawalRecap,
				"TRANSACTIONCODE"	=>	$transactioCode,
				"ACCOUNTNUMBER"		=>	$bankAccountNumber,
				"ACCOUNTHOLDERNAME"	=>	$bankAccountHolderName,
				"AMOUNT"			=>	$totalWithdrawalNominal,
				"PARTNERCODE"		=>	$partnerCode,
				"REMARK"			=>	$transferRemark,
				"EMAILLIST"			=>	$emailList,
				"STATUSDATETIME"	=>	date("Y-m-d H:i:s"),
				"STATUS"			=>	0
			];
			$this->MainOperation->addData("t_transferlist", $arrInsertTransferList);
		}	
		
		$this->sendPartnerMessageUpdateRTDB($idVendor, $idWithdrawalRecap, 1, "approved", date("Y-m-d H:i:s"), $totalWithdrawalNominal, $descriptionNotes);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Manual withdraw record has been saved"));	
	}
	
	public function saveNewDepositRecord(){
		$this->load->model('MainOperation');
		
		$idVendor			=	validatePostVar($this->postVar, 'idVendor', true);
		$vendorName			=	validatePostVar($this->postVar, 'vendorName', true);
		$dateDepositRecord	=	validatePostVar($this->postVar, 'dateDepositRecord', true);
		$dateDepositRecord	=	DateTime::createFromFormat('d-m-Y', $dateDepositRecord);
		$dateDepositRecord	=	$dateDepositRecord->format('Y-m-d');
		$depositNominal		=	validatePostVar($this->postVar, 'depositNominal', false);
		$depositNominal		=	preg_replace("/[^0-9]/", "", $depositNominal);
		$depositDescription	=	validatePostVar($this->postVar, 'depositDescription', false);
		$receiptFileName	=	validatePostVar($this->postVar, 'transferReceiptDepositFileName', false);
		$userAdminName		=	validatePostVar($this->postVar, 'NAME', true);
		
		if($depositNominal <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please input valid deposit nominal"));
		if(!isset($depositDescription) || $depositDescription == "") setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please input deposit description"));
		if(!isset($idVendor) || $idVendor == ""  || $idVendor == 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
		if(!isset($receiptFileName) || $receiptFileName == "" ) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please upload transfer receipt file"));

		$arrInsert	=	array(
							"IDVENDOR"			=>	$idVendor,
							"DESCRIPTION"		=>	$depositDescription,
							"AMOUNT"			=>	$depositNominal,
							"TRANSFERRECEIPT"	=>	$receiptFileName,
							"USERINPUT"			=>	$userAdminName,
							"DATETIMEINPUT"		=>	date('Y-m-d H:i:s')
						);
		$procInsert	=	$this->MainOperation->addData('t_depositvendorrecord', $arrInsert);
		
		if(!$procInsert['status']) switchMySQLErrorCode($procInsert['errCode'], $this->newToken);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New vendor deposit record has been saved"));
	}
	
	public function getDataWithdrawalRequest(){
		$this->load->model('FinanceVendor/ModelRecapPerVendor');
		
		$idVendor				=	validatePostVar($this->postVar, 'idVendor', false);
		$statusWithdrawal		=	validatePostVar($this->postVar, 'statusWithdrawal', false);
		$viewRequestOnly		=	validatePostVar($this->postVar, 'viewRequestOnly', false);
		$startDate				=	validatePostVar($this->postVar, 'startDate', true);
		$startDate				=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate				=	$startDate->format('Y-m-d');
		$endDate				=	validatePostVar($this->postVar, 'endDate', true);
		$endDate				=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate				=	$endDate->format('Y-m-d');
		$dataWithdrawalRequest	=	$this->ModelRecapPerVendor->getDataWithdrawalRequest($startDate, $endDate, $idVendor, $statusWithdrawal, $viewRequestOnly);
		
		if(!$dataWithdrawalRequest) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data withdrawal request found!"));
		setResponseOk(
			array(
				"token"					=>	$this->newToken,
				"dataWithdrawalRequest"	=>	$dataWithdrawalRequest
			)
		);
	}
	
	public function getDetailWithdrawalRequest(){
		$this->load->model('FinanceVendor/ModelRecapPerVendor');
		
		$idWithdrawalRecap		=	validatePostVar($this->postVar, 'idWithdrawalRecap', true);
		$detailWithdrawalRequest=	$this->ModelRecapPerVendor->getDetailWithdrawalRequest($idWithdrawalRecap);
		
		if(!$detailWithdrawalRequest) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail withdrawal request not found!"));

		$listDetailWithdrawal	=	$this->ModelRecapPerVendor->getListDetailWithdrawal($idWithdrawalRecap);
		setResponseOk(
			array(
				"token"						=>	$this->newToken,
				"detailWithdrawalRequest"	=>	$detailWithdrawalRequest,
				"listDetailWithdrawal"		=>	$listDetailWithdrawal
			)
		);
	}
	
	public function approveRejectWithdrawal(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceVendor/ModelRecapPerVendor');
		
		$idWithdrawalRecap		=	validatePostVar($this->postVar, 'idWithdrawalRecap', true);
		$status					=	validatePostVar($this->postVar, 'status', true);
		$strStatus				=	$status == 1 ? "approved" : "rejected";
		$detailWithdrawal		=	$this->ModelRecapPerVendor->getDetailWithdrawalRequest($idWithdrawalRecap);
		$dataUserAdmin			=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName			=	$dataUserAdmin['NAME']." (Admin)";
		$arrUpdateWithdrawal	=	array(
										"DATETIMEAPPROVAL"	=>	date('Y-m-d H:i:s'),
										"STATUSWITHDRAWAL"	=>	$status,
										"USERAPPROVAL"		=>	$userAdminName
									);
		$procUpdateWithdrawal	=	$this->MainOperation->updateData("t_withdrawalrecap", $arrUpdateWithdrawal, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
		
		if(!$procUpdateWithdrawal['status']) switchMySQLErrorCode($procUpdateWithdrawal['errCode'], $this->newToken);
		
		$dataFeeWithdrawal				=	$this->ModelRecapPerVendor->getDataFeeWithdrawal($idWithdrawalRecap);
		$dataCollectPaymentWithdrawal	=	$this->ModelRecapPerVendor->getDataCollectPaymentWithdrawal($idWithdrawalRecap);
		$idVendor						=	$detailWithdrawal['IDVENDOR'];
		$dateWithdrawalRequest			=	$detailWithdrawal['DATETIMEREQUEST'];
		$messageWithdrawalRequest		=	$detailWithdrawal['MESSAGE'];
		$totalAmountWithdrawal			=	number_format($detailWithdrawal['TOTALWITHDRAWAL'], 0, '.', ',');
		$totalAmountWithdrawalDB		=	$detailWithdrawal['TOTALWITHDRAWAL'];
		
		if($status == -1){

			if($dataFeeWithdrawal){
				$arrUpdateFee	=	array(
					"IDWITHDRAWALRECAP"	=>	0,
					"WITHDRAWSTATUS"	=>	0
				);
				$this->MainOperation->updateData("t_fee", $arrUpdateFee, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
			}
			
			if($dataCollectPaymentWithdrawal){
				$arrUpdateCollectPayment	=	array(
					"IDWITHDRAWALRECAP"			=>	0,
					"STATUSSETTLEMENTREQUEST"	=>	0
				);
				$this->MainOperation->updateData("t_collectpayment", $arrUpdateCollectPayment, "IDWITHDRAWALRECAP", $idWithdrawalRecap);

				foreach($dataCollectPaymentWithdrawal as $keyCollectPaymentWithdrawal){
					$idCollectPayment			=	$keyCollectPaymentWithdrawal->IDCOLLECTPAYMENT;
					$idReservationPayment		=	$keyCollectPaymentWithdrawal->IDRESERVATIONPAYMENT;
					$arrInsertCollectHistory	=	array(
														"IDCOLLECTPAYMENT"	=>	$idCollectPayment,
														"DESCRIPTION"		=>	"Settlement has been rejected",
														"USERINPUT"			=>	$userAdminName,
														"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
														"STATUS"			=>	-1
													);
					$this->MainOperation->addData("t_collectpaymenthistory", $arrInsertCollectHistory);
					$this->MainOperation->updateData("t_reservationpayment", ["STATUS" => 0], 'IDRESERVATIONPAYMENT', $idReservationPayment);
				}
			}

		} else if($status == 1){
			
			if($dataFeeWithdrawal){
				$arrUpdateFee	=	array(
										"DATETIMEAPPROVAL"	=>	date('Y-m-d H:i:s'),
										"WITHDRAWSTATUS"	=>	1,
										"USERAPPROVAL"		=>	$userAdminName
									);
				$this->MainOperation->updateData("t_fee", $arrUpdateFee, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
			}
			
			if($dataCollectPaymentWithdrawal){
				foreach($dataCollectPaymentWithdrawal as $keyCollectPaymentWithdrawal){
					$idCollectPayment			=	$keyCollectPaymentWithdrawal->IDCOLLECTPAYMENT;
					$idReservationPayment		=	$keyCollectPaymentWithdrawal->IDRESERVATIONPAYMENT;
					$arrUpdateCollectPayment	=	array(
														"DATETIMESTATUS"			=>	date('Y-m-d H:i:s'),
														"STATUSSETTLEMENTREQUEST"	=>	2,
														"LASTUSERINPUT"				=>	$userAdminName
													);
					$this->MainOperation->updateData("t_collectpayment", $arrUpdateCollectPayment, "IDCOLLECTPAYMENT", $idCollectPayment);
					$arrInsertCollectHistory	=	array(
														"IDCOLLECTPAYMENT"	=>	$idCollectPayment,
														"DESCRIPTION"		=>	"Settlement has been approved along with the withdrawal",
														"USERINPUT"			=>	$userAdminName,
														"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
														"STATUS"			=>	3
													);
					$this->MainOperation->addData("t_collectpaymenthistory", $arrInsertCollectHistory);
					$arrUpdatePayment	=	array(
												"STATUS"		=>	1,
												"DATETIMEUPDATE"=>	date('Y-m-d H:i:s'),
												"USERUPDATE"	=>	$userAdminName,
												"EDITABLE"		=>	0,
												"DELETABLE"		=>	0
											);
					$this->MainOperation->updateData("t_reservationpayment", $arrUpdatePayment, "IDRESERVATIONPAYMENT", $idReservationPayment);
				}
			}
					
			if($totalAmountWithdrawalDB > 0){
				$idBankTransfer			=	$detailWithdrawal['IDBANK'];
				$bankAccountNumber		=	$detailWithdrawal['ACCOUNTNUMBER'];
				$bankAccountHolderName	=	$detailWithdrawal['ACCOUNTHOLDERNAME'];
				$vendorEmail			=	$detailWithdrawal['VENDOREMAIL'];
				$transactioCode			=	"WD".date("dmyHi").str_pad($idVendor, 4, "0", STR_PAD_LEFT);
				$partnerCode			=	$this->MainOperation->getPartnerCode(1, $idVendor);
				$transferRemark			=	strtoupper("WITHDRAW ".date("d M y"));
				$emailList				=	MAILBOX_USERNAME.",".$vendorEmail;
				$arrInsertTransferList	=	array(
												"IDPARTNERTYPE"		=>	1,
												"IDPARTNER"			=>	$idVendor,
												"IDBANK"			=>	$idBankTransfer,
												"IDWITHDRAWAL"		=>	$idWithdrawalRecap,
												"TRANSACTIONCODE"	=>	$transactioCode,
												"ACCOUNTNUMBER"		=>	$bankAccountNumber,
												"ACCOUNTHOLDERNAME"	=>	$bankAccountHolderName,
												"AMOUNT"			=>	$totalAmountWithdrawalDB,
												"PARTNERCODE"		=>	$partnerCode,
												"REMARK"			=>	$transferRemark,
												"EMAILLIST"			=>	$emailList,
												"STATUSDATETIME"	=>	date("Y-m-d H:i:s"),
												"STATUS"			=>	0
											);
				$this->MainOperation->addData("t_transferlist", $arrInsertTransferList);
			}			
		}

		$this->sendPartnerMessageUpdateRTDB($idVendor, $idWithdrawalRecap, $status, $strStatus, $dateWithdrawalRequest, $totalAmountWithdrawal, $messageWithdrawalRequest);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"This withdrawal request has been ".$strStatus));
	}
	
	private function sendPartnerMessageUpdateRTDB($idVendor, $idWithdrawalRecap, $status, $strStatus, $dateWithdrawalRequest, $totalAmountWithdrawal, $messageWithdrawalRequest){
		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('FinanceVendor/ModelRecapPerVendor');

		$dataPartner		=	$this->MainOperation->getDataVendor($idVendor);
		$dataMessageType	=	$this->MainOperation->getDataMessageType(8);
		$partnerTokenFCM	=	$dataPartner['TOKENFCM'];
		$activityMessage	=	$dataMessageType['ACTIVITY'];

		$titleDB			=	"Your withdrawal request has been ".$strStatus;
		$titleMsg			=	$titleDB;
		$body				=	"Details Withdrawal \n";
		$body				.=	"Date Request : ".$dateWithdrawalRequest."\n";
		$body				.=	"Total Amount : IDR ".$totalAmountWithdrawal."\n";
		$body				.=	"Message : ".$messageWithdrawalRequest;
		$additionalArray	=	array(
									"activity"	=>	$activityMessage,
									"idPrimary"	=>	$idWithdrawalRecap
								);
	
		$arrInsertMsg		=	array(
										"IDMESSAGEPARTNERTYPE"	=>	8,
										"IDPARTNERTYPE"			=>	1,
										"IDPARTNER"				=>	$idVendor,
										"IDPRIMARY"				=>	$idWithdrawalRecap,
										"TITLE"					=>	$titleDB,
										"MESSAGE"				=>	$body,
										"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
								);
		$procInsertMsg		=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
			
		if($procInsertMsg['status']){
			if($partnerTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($partnerTokenFCM, $titleMsg, $body, $additionalArray);
			if(PRODUCTION_URL){
				$RTDB_refCode			=	$dataPartner['RTDBREFCODE'];
				if($RTDB_refCode && $RTDB_refCode != ''){
					try {
						$factory			=	(new Factory)
												->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
												->withDatabaseUri(FIREBASE_RTDB_URI);
						$database			=	$factory->createDatabase();
						$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/vendor/".$RTDB_refCode."/activeWithdrawal");
						$referencePartnerVal=	$referencePartner->getValue();
						if($referencePartnerVal != null || !is_null($referencePartnerVal)){
							$referencePartner->update([
								'newWithdrawalNotif'		=>  true,
								'newWithdrawalNotifDetail'	=>  nl2br($body),
								'newWithdrawalNotifStatus'	=>  $status,
								'timestampUpdate'			=>  gmdate('YmdHis'),
								'totalActiveWithdrawal'		=>  $this->MainOperation->getTotalActiveWithdrawalPartner(1, $idVendor)
							]);
						}
					} catch (Exception $e) {
					}
				}
			}
		}
		
		if(PRODUCTION_URL){
			$dataVendor				=	$this->MainOperation->getDataVendor($idVendor);
			$RTDB_idUserPartner 	=   $dataVendor['RTDBREFCODE'];
			try {
				$factory            	=	(new Factory)->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)->withDatabaseUri(FIREBASE_RTDB_URI);
				$database           	=	$factory->createDatabase();
				
				$referenceWithdrawal	=	$database->getReference(FIREBASE_RTDB_MAINREF_NAME."vendor/".$RTDB_idUserPartner."/activeWithdrawal/totalActiveWithdrawal");
				$referenceWithdrawalGet	=	$referenceWithdrawal->getValue();
				if($referenceWithdrawalGet != null || !is_null($referenceWithdrawalGet)){
					$referenceWithdrawal->update(
						[
							'newWithdrawalNotif'        =>  false,
							'newWithdrawalNotifDetail'  =>  '',
							'newWithdrawalNotifStatus'  =>  0,
							'totalActiveWithdrawal'     =>  $this->ModelRecapPerVendor->getTotalWithdrawalRequest($idVendor)
						]
					);
				}
				
				$referenceCollectPayment	=	$database->getReference(FIREBASE_RTDB_MAINREF_NAME."vendor/".$RTDB_idUserPartner."/activeCollectPayment");
				$referenceCollectPaymentGet	=	$referenceCollectPayment->getValue();
				if($referenceCollectPaymentGet != null || !is_null($referenceCollectPaymentGet)){
					$referenceCollectPayment->update(
						[
							'newCollectPaymentStatus'   =>  false,
							'totalActiveCollectPayment' =>  $this->ModelRecapPerVendor->getTotalActiveCollectPayment($idVendor)
						]
					);
				}
			} catch (\Throwable $th) {
				return true;
			}
		}
	}
}