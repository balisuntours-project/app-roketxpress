<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class CollectPayment extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(3);

		if($functionName != "uploadSettlementReceipt" && $functionName != "excelCollectPayment" && $_SERVER['REQUEST_METHOD'] === 'POST'){
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
	
	public function getDataCollectPayment(){

		$this->load->model('FinanceDriver/ModelCollectPayment');
		
		$page					=	validatePostVar($this->postVar, 'page', true);
		$idDriver				=	validatePostVar($this->postVar, 'idDriver', false);
		$startDate				=	validatePostVar($this->postVar, 'startDate', true);
		$endDate				=	validatePostVar($this->postVar, 'endDate', true);
		$startDate				=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate				=	$startDate->format('Y-m-d');
		$endDate				=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate				=	$endDate->format('Y-m-d');
		$collectStatus			=	validatePostVar($this->postVar, 'collectStatus', false);
		$settlementStatus		=	validatePostVar($this->postVar, 'settlementStatus', false);
		$viewRequestOnly		=	validatePostVar($this->postVar, 'viewRequestOnly', false);
		$dataTable				=	$this->ModelCollectPayment->getDataCollectPayment($page, 25, $idDriver, $startDate, $endDate, $collectStatus, $settlementStatus, $viewRequestOnly);
		$totalSettlementRequest	=	$this->ModelCollectPayment->getTotalSettlementRequest();
		$urlExcelCollectPayment	=	BASE_URL."financeDriver/collectPayment/excelCollectPayment/".base64_encode(encodeStringKeyFunction($idDriver."|".$startDate."|".$endDate."|".$collectStatus."|".$settlementStatus."|".$viewRequestOnly, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		
		$this->calculateSettlementRequest();
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelCollectPayment"=>$urlExcelCollectPayment, "totalSettlementRequest"=>$totalSettlementRequest));
	
	}
	
	public function getDetailCollectPayment(){

		$this->load->model('FinanceDriver/ModelCollectPayment');
		
		$idCollectPayment		=	validatePostVar($this->postVar, 'idCollectPayment', true);
		$detailCollectPayment	=	$this->ModelCollectPayment->getDetailCollectPayment($idCollectPayment);
	
		if(!$detailCollectPayment){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found, please refresh your data"));
		}
	
		$historyCollectPayment	=	$this->ModelCollectPayment->getHistoryCollectPayment($idCollectPayment);
		$statusSettlementReceipt=	false;
		$idCollectPaymentHistory=	0;
		
		if($historyCollectPayment){
			$lastSettlementReceipt	=	"";
			foreach($historyCollectPayment as $keyHistoryCollectPayment){
				if($keyHistoryCollectPayment->SETTLEMENTRECEIPT != ""){
					$lastSettlementReceipt	=	$keyHistoryCollectPayment->SETTLEMENTRECEIPT;
					$idCollectPaymentHistory=	$keyHistoryCollectPayment->IDCOLLECTPAYMENTHISTORY;
				}
			}
			if($lastSettlementReceipt != ""){
				$detailCollectPayment['SETTLEMENTRECEIPT']	=	$lastSettlementReceipt;
				$statusSettlementReceipt					=	true;
			}
		}
		
		setResponseOk(
			array(
				"token"						=>	$this->newToken,
				"detailCollectPayment"		=>	$detailCollectPayment,
				"historyCollectPayment"		=>	$historyCollectPayment,
				"idCollectPaymentHistory"	=>	$idCollectPaymentHistory,
				"statusSettlementReceipt"	=>	$statusSettlementReceipt
			)
		);
	
	}
	
	public function uploadSettlementReceipt($idCollectPayment, $idCollectPaymentHistory){

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
		
		$dir		=	PATH_STORAGE_COLLECT_PAYMENT_RECEIPT;
		$extension	=	pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		$namaFile	=	"SettlementReceipt"."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$namaFile);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "urlSettlementReceipt"=>URL_COLLECT_PAYMENT_RECEIPT.$namaFile, "settlementReceiptFileName"=>$namaFile));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}
		
	}
	
	public function approveRejectCollectPaymentSettlement(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		$this->load->model('FinanceDriver/ModelCollectPayment');
		
		$idCollectPayment			=	validatePostVar($this->postVar, 'idCollectPayment', true);
		$status						=	validatePostVar($this->postVar, 'status', true);
		$idCollectPaymentHistory	=	validatePostVar($this->postVar, 'idCollectPaymentHistory', false);
		$settlementReceiptFileName	=	validatePostVar($this->postVar, 'settlementReceiptFileName', false);
		$strStatus					=	$status == 2 ? "approved" : "rejected";
		$dataUserAdmin				=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName				=	$dataUserAdmin['NAME']." (Admin)";
		$arrUpdateCollectPayment	=	array(
											"DATETIMESTATUS"			=>	date('Y-m-d H:i:s'),
											"STATUSSETTLEMENTREQUEST"	=>	$status,
											"LASTUSERINPUT"				=>	$userAdminName
										);
		if(isset($settlementReceiptFileName) && $settlementReceiptFileName != ''){
			$arrUpdateCollectPayment['PAYMENTRECEIPTFILENAME']	=	$settlementReceiptFileName;
			$arrUpdateCollectPaymentHistory						=	array(
																		"SETTLEMENTRECEIPT"	=>	$settlementReceiptFileName
																	);
			$this->MainOperation->updateData("t_collectpaymenthistory", $arrUpdateCollectPaymentHistory, "IDCOLLECTPAYMENTHISTORY", $idCollectPaymentHistory);
		}
		$procUpdateCollectPayment	=	$this->MainOperation->updateData("t_collectpayment", $arrUpdateCollectPayment, "IDCOLLECTPAYMENT", $idCollectPayment);
		
		if(!$procUpdateCollectPayment['status']){
			switchMySQLErrorCode($procUpdateCollectPayment['errCode'], $this->newToken);
		}
		
		$arrInsertCollectHistory	=	array(
											"IDCOLLECTPAYMENT"	=>	$idCollectPayment,
											"DESCRIPTION"		=>	"Settlement has been ".$strStatus,
											"USERINPUT"			=>	$userAdminName,
											"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
											"STATUS"			=>	$status
										);
		$this->MainOperation->addData("t_collectpaymenthistory", $arrInsertCollectHistory);
		
		$detailCollectPayment	=	$this->ModelCollectPayment->getDetailCollectPayment($idCollectPayment);
		$idReservation			=	$detailCollectPayment['IDRESERVATION'];
		$idReservationPayment	=	$detailCollectPayment['IDRESERVATIONPAYMENT'];
		$idDriver				=	$detailCollectPayment['IDDRIVER'];
		$customerName			=	$detailCollectPayment['CUSTOMERNAME'];
		$reservationTitle		=	$detailCollectPayment['RESERVATIONTITLE'];
		$dateCollect			=	$detailCollectPayment['DATECOLLECT'];
		$dateCollectDB			=	$detailCollectPayment['DATECOLLECTDB'];
		$remarkCollect			=	$detailCollectPayment['DESCRIPTION'];
		$amountCollect			=	$detailCollectPayment['AMOUNT'];
		$amountCurrency			=	$detailCollectPayment['AMOUNTCURRENCY'];
		$amountCurrencyExchange	=	$detailCollectPayment['EXCHANGECURRENCY'];
		$amountCollectIDR		=	$detailCollectPayment['AMOUNTIDR'];
		$amountCollectStr		=	number_format($amountCollectIDR, 0, '.', ',')." IDR";

		$idReservationDetails	=	$this->ModelReservation->getIdReservationDetailsDriver($idReservation, $idDriver, $dateCollectDB);
		$dataPartner			=	$this->MainOperation->getDataDriver($idDriver);
		$dataMessageType		=	$this->MainOperation->getDataMessageType(7);
		$partnerTokenFCM		=	$dataPartner['TOKENFCM'];
		$activityMessage		=	$dataMessageType['ACTIVITY'];

		if($amountCurrency != "IDR"){
			$amountCollectStr	=	number_format($amountCollect, 2, '.', ',')." ".$amountCurrency." x ".number_format($amountCurrencyExchange, 0, '.', ',')." = ".$amountCollectStr;
		}

		$titleDB			=	"Settlement collect payment ".$strStatus;
		$titleMsg			=	$titleDB;
		$body				=	"Details Collect\n";
		$body				.=	"Reservation Detail : ".$customerName." - ".$reservationTitle."\n";
		$body				.=	"Date Collect : ".$dateCollect."\n";
		$body				.=	"Remark : ".$remarkCollect."\n";
		$body				.=	"Amount : ".$amountCollectStr;
		$additionalArray	=	array(
									"activity"	=>	$activityMessage,
									"idPrimary"	=>	$idCollectPayment
								);
	
		$arrInsertMsg		=	array(
										"IDMESSAGEPARTNERTYPE"	=>	7,
										"IDPARTNERTYPE"			=>	2,
										"IDPARTNER"				=>	$idDriver,
										"IDPRIMARY"				=>	$idReservationDetails,
										"TITLE"					=>	$titleDB,
										"MESSAGE"				=>	$body,
										"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
								);
		$procInsertMsg		=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
			
		if($procInsertMsg['status']){
			if($partnerTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($partnerTokenFCM, $titleMsg, $body, $additionalArray);
		}
		
		if($status == 2){
			$arrUpdatePayment	=	array(
										"STATUS"		=>	1,
										"DATETIMEUPDATE"=>	date('Y-m-d H:i:s'),
										"USERUPDATE"	=>	$userAdminName,
										"EDITABLE"		=>	0,
										"DELETABLE"		=>	0
									);
			$this->MainOperation->updateData("t_reservationpayment", $arrUpdatePayment, "IDRESERVATIONPAYMENT", $idReservationPayment);
		}
		
		if(PRODUCTION_URL){
			$this->calculateSettlementRequest();
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"This collect payment settlement has been ".$strStatus));
	
	}
	
	public function calculateSettlementRequest(){
		$this->load->model('FinanceDriver/ModelCollectPayment');
		$totalSettlementRequest	=	$this->ModelCollectPayment->getTotalSettlementRequest();

		try {
			$factory	=	(new Factory)
							->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
							->withDatabaseUri(FIREBASE_RTDB_URI);
			$database	=	$factory->createDatabase();
			$reference	=	$database->getReference(FIREBASE_RTDB_MAINREF_NAME."unprocessedFinanceDriver/collectPayment")
							->set([
								'newCollectPaymentStatus'	=>	false,
								'newCollectPaymentTotal'	=>	$totalSettlementRequest,
								'timestampUpdate'			=>	gmdate("YmdHis")
							]);
		} catch (Exception $e) {
		}
		return true;
	}
	
	public function excelCollectPayment($encryptedVar){

		$this->load->model('FinanceDriver/ModelCollectPayment');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar		=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar	=	explode("|", $decryptedVar);
		$idDriver			=	$expDecryptedVar[0];
		$startDate			=	$expDecryptedVar[1];
		$endDate			=	$expDecryptedVar[2];
		$collectStatus		=	$expDecryptedVar[3];
		$settlementStatus	=	$expDecryptedVar[4];
		$viewRequestOnly	=	$expDecryptedVar[5];

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
		$driverName		=	isset($idDriver) && $idDriver != "" && $idDriver != 0 ? $this->MainOperation->getDriverNameById($idDriver) : "All Driver";
		$dateRangeStr	=	$startDateStr." to ".$endDateStr;
		$dataTable		=	$this->ModelCollectPayment->getDataCollectPayment(1, 999999, $idDriver, $startDate, $endDate, $collectStatus, $settlementStatus, $viewRequestOnly);
		
		if(!$dataTable){
			echo "No data found!";
			die();
		}
		
		$strCollectStatus	=	$strSettlementStatus	=	"-";
		switch($collectStatus){
			case "0"	:	$strCollectStatus	=	"Uncollected"; break;
			case "1"	:	$strCollectStatus	=	"Collected"; break;
			default		:	$strCollectStatus	=	"All Collect Status"; break;
		}
		
		switch($settlementStatus){
			case "0"	:	$strSettlementStatus	=	"Unrequested"; break;
			case "1"	:	$strSettlementStatus	=	"Requested"; break;
			case "2"	:	$strSettlementStatus	=	"Approved"; break;
			case "-1"	:	$strSettlementStatus	=	"Rejected"; break;
			default		:	$strSettlementStatus	=	"All Settlement Status"; break;
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
		$sheet->setCellValue('A2', 'Driver Collect Payment Report');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:L1');
		$sheet->mergeCells('A2:L2');
		
		$sheet->setCellValue('A4', 'Driver Name : '.$driverName);
		$sheet->setCellValue('A5', 'Date Period : '.$dateRangeStr);
		$sheet->setCellValue('A6', 'Collect Status : '.$strCollectStatus);
		$sheet->setCellValue('A7', 'Settlement Status : '.$strSettlementStatus);
		
		$sheet->setCellValue('A9', 'Date');
		$sheet->setCellValue('B9', 'Driver Name');
		$sheet->setCellValue('C9', 'Customer Name');
		$sheet->setCellValue('D9', 'Reservation Title');
		$sheet->setCellValue('E9', 'Reservation Date');
		$sheet->setCellValue('F9', 'Source & Booking Code');
		$sheet->setCellValue('G9', 'Payment Description');
		$sheet->setCellValue('H9', 'Currency');
		$sheet->setCellValue('I9', 'Amount');
		$sheet->setCellValue('J9', 'Amount (IDR)');
		$sheet->setCellValue('K9', 'Status Collect');
		$sheet->setCellValue('L9', 'Status Settlement');
		$sheet->getStyle('A9:L9')->getFont()->setBold( true );
		$sheet->getStyle('A9:L9')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A9:L9')->getAlignment()->setVertical('center');
		$rowNumber	=	10;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$grandTotalAmountIDR =	0;
		foreach($dataTable['data'] as $data){
			
			$grandTotalAmountIDR	+=	$data->AMOUNTIDR;
			$reservationDateEnd		=	"";

			if($data->DURATIONOFDAY > 1){
				$reservationDateEnd	=	" - ".$data->RESERVATIONDATEEND;
			}
						
			$statusCollect	=	$statusSettlement	=	"";
			switch($data->STATUS){
				case "0"	:	$statusCollect	=	'Pending'; break;
				case "1"	:	$statusCollect	=	'Collected'; break;
				case "2"	:	$statusCollect	=	'Deposited'; break;
				default		:	$statusCollect	=	'-'; break;
			}
			
			switch($data->STATUSSETTLEMENTREQUEST){
				case "0"	:	$statusSettlement	=	"Unrequested"; break;
				case "1"	:	$statusSettlement	=	"Requested"; break;
				case "2"	:	$statusSettlement	=	"Approved"; break;
				case "-1"	:	$statusSettlement	=	"Rejected"; break;
				default		:	$statusCollect		=	'-'; break;
			}

			$sheet->setCellValue('A'.$rowNumber, $data->DATECOLLECT);
			$sheet->setCellValue('B'.$rowNumber, $data->DRIVERNAME);
			$sheet->setCellValue('C'.$rowNumber, $data->CUSTOMERNAME);
			$sheet->setCellValue('D'.$rowNumber, $data->RESERVATIONTITLE);
			$sheet->setCellValue('E'.$rowNumber, $data->RESERVATIONDATESTART.$reservationDateEnd);
			$sheet->setCellValue('F'.$rowNumber, $data->SOURCENAME."\n".$data->BOOKINGCODE);
			$sheet->setCellValue('G'.$rowNumber, $data->DESCRIPTION);
			$sheet->setCellValue('H'.$rowNumber, $data->AMOUNTCURRENCY);
			$sheet->setCellValue('I'.$rowNumber, $data->AMOUNT); $sheet->getStyle('I'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('J'.$rowNumber, $data->AMOUNTIDR);	$sheet->getStyle('J'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('K'.$rowNumber, $statusCollect);
			$sheet->setCellValue('L'.$rowNumber, $statusSettlement);
			$rowNumber++;
			
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':I'.$rowNumber);
		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');
		$sheet->setCellValue('J'.$rowNumber, $grandTotalAmountIDR);	$sheet->getStyle('J'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('J'.$rowNumber)->getFont()->setBold( true );
		
		$sheet->getStyle('A9:L'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('L'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(12);
		$sheet->getColumnDimension('B')->setWidth(16);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(16);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(25);
		$sheet->getColumnDimension('H')->setWidth(9);
		$sheet->getColumnDimension('I')->setWidth(14);
		$sheet->getColumnDimension('J')->setWidth(14);
		$sheet->getColumnDimension('K')->setWidth(12);
		$sheet->getColumnDimension('L')->setWidth(12);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDriverFinanceCollectPayment_'.$driverName.'_'.$startDateStr.'_'.$endDateStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}
	
}