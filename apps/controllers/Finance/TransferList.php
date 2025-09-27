<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class TransferList extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(3);

		if($functionName != "uploadTransferReceipt" && $_SERVER['REQUEST_METHOD'] === 'POST'){
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
	
	public function getDataUnprocessed(){
		$this->load->model('Finance/ModelTransferList');
		$dataTable		=	$this->ModelTransferList->getDataUnprocessed();

		if(!$dataTable) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	}
	
	public function cancelTransferList(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelTransferList');
		$idTransferList		=	validatePostVar($this->postVar, 'idData', true);
		$detailTransferList	=	$this->ModelTransferList->getDetailTransferList($idTransferList);

		if(!$detailTransferList) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Invalid data. No detail found"));

		$statusTransfer		=	$detailTransferList['STATUS'];
		if($statusTransfer > 1) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Cannot process data transfer that has been successful"));

		$arrUpdate			=	array(
			"RECEIPTFILE"	=>	"",
			"PAYROLLFILE"	=>	"",
			"STATUSDATETIME"=>	date('Y-m-d H:i:s'),
			"STATUS"		=>	-1
		);
		$procUpdate			=	$this->MainOperation->updateData("t_transferlist", $arrUpdate, "IDTRANSFERLIST", $idTransferList);
		
		if(!$procUpdate['status']) switchMySQLErrorCode($procUpdate['errCode'], $this->newToken);
		setResponseOk(
			array(
				"token"				=>	$this->newToken,
				"detailTransferList"=>	$detailTransferList,
				"msg"				=>	"Transfer list has been cancelled"
			)
		);
	
	}
	
	public function createExcelPayrollTransferList(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelTransferList');

		$arrIdTransferList		=	validatePostVar($this->postVar, 'arrIdTransferList', true);
		$arrIdTransferList		=	explode(",", $arrIdTransferList);
		
		if(!is_array($arrIdTransferList) || count($arrIdTransferList) <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data sent"));
		
		$strArrIdTransferList	=	implode(",", $arrIdTransferList);
		$dataTransferList		=	$this->ModelTransferList->getDataExcelTransferList($strArrIdTransferList);
		$dataUserAdmin			=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName			=	$dataUserAdmin['NAME'];
		
		if(!$dataTransferList) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found. The transfer list you selected may have been processed or cancelled"));
		
		$spreadsheet	=	new Spreadsheet();
		$sheet			=	$spreadsheet->getActiveSheet();
		
		$sheet->getPageSetup()->setOrientation(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(0.25);
		$sheet->getPageMargins()->setRight(0.2);
		$sheet->getPageMargins()->setLeft(0.2);
		$sheet->getPageMargins()->setBottom(0.25);
		
		$sheet->setCellValue('A1', 'No');
		$sheet->setCellValue('B1', 'Transaction ID');
		$sheet->setCellValue('C1', 'Transfer Type');
		$sheet->setCellValue('D1', 'Beneficiary ID');
		$sheet->setCellValue('E1', 'Credited Account');
		$sheet->setCellValue('F1', 'Receiver Name');
		$sheet->setCellValue('G1', 'Amount');
		$sheet->setCellValue('H1', 'NIP');
		$sheet->setCellValue('I1', 'Remark');
		$sheet->setCellValue('J1', 'Beneficiary Email');
		$sheet->getStyle('A1:J1')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A1:J1')->getAlignment()->setVertical('center');
		$sheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('000000');
		$number		=	1;
		$rowNumber	=	2;
		
		$styleArrayFontWhiteBold = [
			'font' => [
				'bold' => true,
				'color' => [
					'rgb' => 'FFFFFF'
				]
			 ]
		];
		$sheet->getStyle('A1:J1')->applyFromArray($styleArrayFontWhiteBold);
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		foreach($dataTransferList as $data){
			
			$bankCode	=	$data->IDBANK == 1 ? "BCA" : "LLG";

			$sheet->setCellValue('A'.$rowNumber, $number);
			$sheet->setCellValue('B'.$rowNumber, $data->TRANSACTIONCODE);
			$sheet->setCellValue('C'.$rowNumber, $bankCode);
			$sheet->setCellValue('D'.$rowNumber, "");
			$sheet->setCellValue('E'.$rowNumber, $data->ACCOUNTNUMBER);
			$sheet->setCellValue('F'.$rowNumber, $data->ACCOUNTHOLDERNAME);
			$sheet->setCellValue('G'.$rowNumber, $data->AMOUNT);
			$sheet->getStyle('G'.$rowNumber)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$sheet->setCellValue('H'.$rowNumber, $data->PARTNERCODE);
			$sheet->setCellValue('I'.$rowNumber, $data->REMARK);
			$sheet->setCellValue('J'.$rowNumber, $data->EMAILLIST);
			$number++;
			$rowNumber++;
			
		}
		
		$sheet->getStyle('A1:J'.($rowNumber-1))->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.($rowNumber-1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('J'.($rowNumber-1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(8);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(18);
		$sheet->getColumnDimension('F')->setWidth(40);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(14);
		$sheet->getColumnDimension('I')->setWidth(22);
		$sheet->getColumnDimension('J')->setWidth(50);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'XLSX_TL_'.date('YmdHis').'_'.str_replace(" ", "_", $userAdminName).'.xlsx';
		$fullPathFile	=	PATH_EXCEL_TRANSFER_LIST_FILE.$filename;
		$writer->save($fullPathFile);
		
		if(file_exists($fullPathFile)){
			
			$arrUpdateTransferList		=	array(
												"PAYROLLFILE"		=>	$filename,
												"DOWNLOADUSER"		=>	$userAdminName,
												"DOWNLOADDATETIME"	=>	date('Y-m-d H:i:s'),
												"STATUSDATETIME"	=>	date('Y-m-d H:i:s'),
												"STATUS"			=>	1
											);
			$this->MainOperation->updateDataIn("t_transferlist", $arrUpdateTransferList, "IDTRANSFERLIST", $arrIdTransferList);
			$urlDownloadExcelFile		=	URL_EXCEL_TRANSFER_LIST_FILE.$filename;
			$elemBtnDownloadExcelFile	=	'<a class="button button-primary button-sm" target="_blank" href="'.$urlDownloadExcelFile.'"><span><i class="fa fa-file-excel-o"></i>Download Excel Payroll</span></a>';
			setResponseOk(
				array(
					"token"	=>	$this->newToken,
					"msg"	=>	"Process has been completed. To download the payroll excel file, please click the download button below<br/><br/><center>".$elemBtnDownloadExcelFile."</center>"
				)
			);
			
		} else {
			setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed when creating excel file. Please try again later"));
		}
	}
	
	public function getDataOngoing(){
		$this->load->model('Finance/ModelTransferList');
		
		$startDate	=	validatePostVar($this->postVar, 'dateStart', true);
		$endDate	=	validatePostVar($this->postVar, 'dateEnd', true);
		$startDate	=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate	=	$startDate->format('Y-m-d');
		$endDate	=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate	=	$endDate->format('Y-m-d');
		$dataTable	=	$this->ModelTransferList->getDataOngoing($startDate, $endDate);

		if(!$dataTable) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		
		foreach($dataTable as $data){
			$payrollFileName	=	$data->PAYROLLFILE;
			$transferList		=	$this->ModelTransferList->getDataOnGoingTransferByFileName($payrollFileName);
			$data->TRANSFERLIST	=	$transferList;
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	}
	
	public function uploadTransferReceipt($idTransferList){
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
		$namaFile	=	"TransferReceipt"."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$namaFile);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "urlTransferReceipt"=>URL_TRANSFER_RECEIPT.$namaFile, "transferReceiptFileName"=>$namaFile, "defaultHeight"=>"150px"));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}
	}
	
	public function saveManualTransfer(){
		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		$this->load->model('Finance/ModelTransferList');
		
		$idTransferList			=	validatePostVar($this->postVar, 'idTransferList', true);
		$dateTransfer			=	validatePostVar($this->postVar, 'dateTransfer', true);
		$dateTransfer			=	DateTime::createFromFormat('d-m-Y', $dateTransfer);
		$dateTransfer			=	$dateTransfer->format('Y-m-d');
		$transferReceiptFileName=	validatePostVar($this->postVar, 'transferReceiptFileName', true);
		$detailTransferList		=	$this->ModelTransferList->getDetailTransferList($idTransferList);
		
		if(!$detailTransferList) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Invalid submission data. Transfer detail not found"));

		$htmlTransferReceipt	=	$this->load->view('utils/transfer_receipt', array('urlTransferReceipt'=>URL_TRANSFER_RECEIPT.$transferReceiptFileName), TRUE);
		$htmlFileName			=	date('YmdHis').'.html';
		
		if(!file_put_contents(PATH_HTML_TRANSFER_RECEIPT.$htmlFileName, $htmlTransferReceipt)) setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Internal server error. Please try again later"));
		
		$idPartnerType			=	$detailTransferList['IDPARTNERTYPE'];
		$idPartner				=	$detailTransferList['IDPARTNER'];
		$idLoanDriverRequest	=	$detailTransferList['IDLOANDRIVERREQUEST'];
		$idWithdrawal			=	$detailTransferList['IDWITHDRAWAL'];
		$nominalTransfer		=	$detailTransferList['AMOUNT'];
		$arrUpdateTransferList	=	array(
			"RECEIPTFILE"	=>	$htmlFileName,
			"STATUSDATETIME"=>	date('Y-m-d H:i:s'),
			"STATUS"		=>	2
		);
		$this->MainOperation->updateData("t_transferlist", $arrUpdateTransferList, "IDTRANSFERLIST", $idTransferList);

		if($idLoanDriverRequest != 0){
			$isLoanRecordExist		=	$this->ModelCron->isLoanRecordExist($idLoanDriverRequest);
			$detailLoanRequest		=	$this->ModelCron->getDetailLoanRequest($idLoanDriverRequest);
			$idDriver				=	$detailLoanRequest['IDDRIVER'];
			$idLoanType				=	$detailLoanRequest['IDLOANTYPE'];
			$statusLoanCapital		=	$detailLoanRequest['STATUSLOANCAPITAL'];
			$notes					=	$detailLoanRequest['NOTES'];
			$bankName				=	$detailLoanRequest['BANKNAME'];
			$accountNumber			=	$detailLoanRequest['ACCOUNTNUMBER'];
			$accountHolderName		=	$detailLoanRequest['ACCOUNTHOLDERNAME'];
			$userUpdate				=	$detailLoanRequest['USERUPDATE'];
			$loanNominalPrincipal	=	$detailLoanRequest['LOANNOMINALPRINCIPAL'];
			$loanNominalInterest	=	$detailLoanRequest['LOANNOMINALINTEREST'];
			$loanNominalTotal		=	$statusLoanCapital == 1 ? $detailLoanRequest['LOANNOMINALTOTAL'] : $detailLoanRequest['AMOUNT'];
			$loanDurationMonth		=	$detailLoanRequest['LOANDURATIONMONTH'];
			$loanInterestPerAnnum	=	$detailLoanRequest['LOANINTERESTPERANNUM'];
			$loanInstallmentPerMonth=	$detailLoanRequest['LOANINSTALLMENTPERMONTH'];
			$loanDateTimeInput		=	$detailLoanRequest['DATETIMEINPUT'];
			$strLoanType			=	$statusLoanCapital == 1 ? "Loan (".$detailLoanRequest['LOANTYPE'].")" : "Prepaid Capital";
			$additionalDescription	=	$statusLoanCapital == 1 ? 
										"Nominal Principal Rp. ".number_format($loanNominalPrincipal, 0, ',', '.')." + Nominal Interest Rp. ".number_format($loanNominalInterest, 0, ',', '.')." (".$loanInterestPerAnnum."% p.a - ".$loanDurationMonth." Months) \n".
										"Total Loan Rp. ".number_format($loanNominalTotal, 0, ',', '.')." | Monthly installment Rp. ".number_format($loanInstallmentPerMonth, 0, ',', '.').". \n"
										: "";

			$arrUpdateRequest		=	array(
				"FILETRANSFERRECEIPT"	=>	$transferReceiptFileName,
				"STATUS"				=>	2,
				"DATETIMECONFIRM"		=>	date('Y-m-d H:i:s'),
				"USERCONFIRM"			=>	'Auto System'
			);
			$procUpdateRquest	=	$this->MainOperation->updateData("t_loandriverrequest", $arrUpdateRequest, "IDLOANDRIVERREQUEST", $idLoanDriverRequest);
			
			if($procUpdateRquest['status'] && !$isLoanRecordExist){
				$arrInsertRecord	=	array(
					"IDDRIVER"				=>	$idDriver,
					"IDLOANTYPE"			=>	$idLoanType,
					"IDLOANDRIVERREQUEST"	=>	$idLoanDriverRequest,
					"TYPE"					=>	'D',
					"DESCRIPTION"			=>	"Fund for ".$strLoanType." (".$notes."). \n".$additionalDescription.
												"Transferred to ".$bankName." - ".$accountNumber." - ".$accountHolderName.". \n".
												"Input by : ".$userUpdate,
					"AMOUNT"				=>	$loanNominalTotal,
					"DATETIMEINPUT"			=>	$dateTransfer." ".date('H:i:s'),
					"USERINPUT"				=>	$detailLoanRequest['USERUPDATE']
				);
				$this->MainOperation->addData("t_loandriverrecord", $arrInsertRecord);
			}
			
			if($statusLoanCapital == 1) $this->MainOperation->updateData('t_loandriverrecap', ['LOANDATEDISBURSEMENT' => $dateTransfer, 'LOANSTATUS' => 1], ['IDDRIVER' => $idDriver, 'IDLOANTYPE' => $idLoanType, 'LOANSTATUS' => 0, 'LOANNOMINALPRINCIPAL' => $loanNominalPrincipal]);
			$dataMessageType	=	$this->MainOperation->getDataMessageType(6);
			$activityMessage	=	$dataMessageType['ACTIVITY'];
			$title				=	"Loan funds / prepaid capital have been transferred";
			$body				=	"Your ".$strLoanType." [".number_format($nominalTransfer, 0, '.', ',')."] has been transferred";
			$additionalArray	=	array(
				"activity"	=>	$activityMessage,
				"idPrimary"	=>	$idLoanDriverRequest,
			);

			$arrInsertMsg		=	array(
				"IDMESSAGEPARTNERTYPE"	=>	6,
				"IDPARTNERTYPE"			=>	$idPartnerType,
				"IDPARTNER"				=>	$idPartner,
				"IDPRIMARY"				=>	$idLoanDriverRequest,
				"TITLE"					=>	$title,
				"MESSAGE"				=>	$body,
				"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
			);
			$procInsertMsg		=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
				
			if($procInsertMsg['status']){
				$dataDriver			=	$this->MainOperation->getDataDriver($idPartner);
				$driverTokenFCM		=	$dataDriver['TOKENFCM'];
				if($driverTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($driverTokenFCM, $title, $body, $additionalArray);
			}
			
		} else if($idWithdrawal != 0){
			
			$arrUpdateWithdrawal			=	array("STATUSWITHDRAWAL"	=>	2);
			$procUpdateWithdrawal			=	$this->MainOperation->updateData("t_withdrawalrecap", $arrUpdateWithdrawal, "IDWITHDRAWALRECAP", $idWithdrawal);
			$detailWithdrawal				=	$this->ModelCron->getDetailWithdrawalRequest($idWithdrawal);
			$idDriver						=	$detailWithdrawal['IDDRIVER'];
			$idVendor						=	$detailWithdrawal['IDVENDOR'];
			$totalLoanCarInstallment		=	$detailWithdrawal['TOTALLOANCARINSTALLMENT'];
			$totalLoanPersonalInstallment	=	$detailWithdrawal['TOTALLOANPERSONALINSTALLMENT'];
			$withdrawMonthYear				=	$detailWithdrawal['WITHDRAWMONTHYEAR'];
			$totalCharityNominal			=	$detailWithdrawal['TOTALCHARITY'];
			$totalAdditionalIncomeNominal	=	$detailWithdrawal['TOTALADDITIONALINCOME'];
			$dateWithdrawalRequest			=	$detailWithdrawal['DATETIMEREQUEST'];
			$dateWithdrawalRequestDB		=	$detailWithdrawal['DATETIMEREQUESTDB'];
			$messageWithdrawalRequest		=	$detailWithdrawal['MESSAGE'];
			$totalAmountWithdrawal			=	number_format($detailWithdrawal['TOTALWITHDRAWAL'], 0, '.', ',');
			
			$dataPartner		=	$idPartnerType == 1 ? $this->MainOperation->getDataVendor($idPartner) : $this->MainOperation->getDataDriver($idPartner);
			$dataMessageType	=	$this->MainOperation->getDataMessageType(8);
			$partnerTokenFCM	=	$dataPartner['TOKENFCM'];
			$activityMessage	=	$dataMessageType['ACTIVITY'];

			if($totalLoanCarInstallment > 0) $this->calculateDriverLoanRecap($idDriver, 1, $totalLoanCarInstallment);						
			if($totalLoanPersonalInstallment > 0) $this->calculateDriverLoanRecap($idDriver, 2, $totalLoanPersonalInstallment);
			
			if($totalCharityNominal > 0){
				$charityName	=	$dataPartner['NAME'];
				$arrInsertCharityData	=	[
					"IDDRIVER"			=>	$idDriver,
					"IDVENDOR"			=>	$idVendor,
					"IDWITHDRAWALRECAP"	=>	$idWithdrawal,
					"CONTRIBUTORTYPE"	=>	1,
					"NAME"				=>	$charityName,
					"DESCRIPTION"		=>	"Charity through withdrawal disbursement",
					"NOMINAL"			=>	$totalCharityNominal,
					"DATETIME"			=>	date('Y-m-d H:i:s'),
					"INPUTTYPE"			=>	1,
					"INPUTBYNAME"		=>	"Auto System",
					"INPUTDATETIME"		=>	date('Y-m-d H:i:s'),
					"STATUS"			=>	0
				];
				$this->MainOperation->addData("t_charity", $arrInsertCharityData);
			}
			
			if($totalAdditionalIncomeNominal > 0){
				$arrInsertAdditionalIncome	=	[
					"IDDRIVER"				=>	$idDriver,
					"IDWITHDRAWALRECAP"		=>	$idWithdrawal,
					"DESCRIPTION"			=>	"Additional income payment within withdrawal",
					"IMAGERECEIPT"			=>	"noimage.jpg",
					"INCOMENOMINAL"			=>	$totalAdditionalIncomeNominal,
					"INCOMEDATE"			=>	$dateWithdrawalRequestDB,
					"INPUTTYPE"				=>	3,
					"INPUTUSER"				=>	'Auto System',
					"INPUTDATETIME"			=>	date('Y-m-d H:i:s'),
					"APPROVALUSER"			=>	'Auto System',
					"APPROVALDATETIME"		=>	date('Y-m-d H:i:s'),
					"APPROVALSTATUS"		=>	1
				];
				$this->MainOperation->addData("t_additionalincome", $arrInsertAdditionalIncome);
				
				$arrData							=	["idDriver" => $idDriver, "userAdminName" => "Auto System"];
				$base64JsonData						=	base64_encode(json_encode($arrData));
				$urlAPICalculateRatingPointDriver	=	BASE_URL."financeDriver/additionalIncome/apiCalculateRatingPointDriver/".$base64JsonData;
				
				try {
					json_decode(trim(curl_get_file_contents($urlAPICalculateRatingPointDriver)));
				} catch(Exception $e) {
				}
			}

			$titleDB			=	"Your withdrawal has been transferred";
			$titleMsg			=	$titleDB;
			$body				=	"Details Withdrawal \n";
			$body				.=	"Date Request : ".$dateWithdrawalRequest."\n";
			$body				.=	"Total Amount : IDR ".$totalAmountWithdrawal."\n";
			$body				.=	"Message : ".$messageWithdrawalRequest;
			$additionalArray	=	array(
				"activity"	=>	$activityMessage,
				"idPrimary"	=>	$idWithdrawal
			);
		
			$arrInsertMsg		=	array(
					"IDMESSAGEPARTNERTYPE"	=>	8,
					"IDPARTNERTYPE"			=>	2,
					"IDPARTNER"				=>	$idPartner,
					"IDPRIMARY"				=>	$idWithdrawal,
					"TITLE"					=>	$titleDB,
					"MESSAGE"				=>	$body,
					"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
			);
			$procInsertMsg		=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
				
			if($procInsertMsg['status']){
				if($partnerTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($partnerTokenFCM, $titleMsg, $body, $additionalArray);
			}
		}
		setResponseOk(array("token"=>$this->newToken, "msg"=>"The manual transfer approve process has been completed"));
	}
	
	private function calculateDriverLoanRecap($idDriver, $idLoanType, $installmentNominal){
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		while($installmentNominal > 0){
			$dataLoanRecap	=	$this->ModelLoanPrepaidCapital->getDataRecapSaldoLoanDriver($idDriver, $idLoanType);
			
			if($dataLoanRecap){
				foreach($dataLoanRecap as $keyLoanRecap){
					$idLoanDriverRecap			=	$keyLoanRecap->IDLOANDRIVERRECAP;
					$loanNominalSaldo			=	$keyLoanRecap->LOANNOMINALSALDO;
					$loanInstallmentPerMonth	=	$keyLoanRecap->LOANINSTALLMENTPERMONTH;
					$loanInstallmentLastPeriod	=	$keyLoanRecap->LOANINSTALLMENTLASTPERIOD;
					$reductionAmount			=	$loanInstallmentPerMonth <= $loanNominalSaldo ? $loanInstallmentPerMonth : $loanNominalSaldo;
					$reductionAmount			=	$reductionAmount <= $installmentNominal ? $reductionAmount : $installmentNominal;
					$loanNominalSaldoFinal		=	$loanNominalSaldo - $reductionAmount;
					$installmentPeriodDT		=	DateTime::createFromFormat('Y-m', $loanInstallmentLastPeriod);
					$installmentPeriodDT		=	$installmentPeriodDT->modify('+1 month');
					$installmentPeriod			=	$installmentPeriodDT->format('Y-m');
					
					$arrUpdateRecap	=	[
						"LOANNOMINALSALDO"			=>	$loanNominalSaldoFinal,
						"LOANINSTALLMENTLASTPERIOD"	=>	$installmentPeriod
					];
					
					if($loanNominalSaldoFinal <= 0) $arrUpdateRecap['LOANSTATUS']	=	2;
					if($installmentNominal > 0) $this->MainOperation->updateData('t_loandriverrecap', $arrUpdateRecap, 'IDLOANDRIVERRECAP', $idLoanDriverRecap);
					
					$arrInsertInstallmentHistory=	[
						"IDLOANDRIVERRECAP"		=>	$idLoanDriverRecap,
						"DESCRIPTION"			=>	'Installment record through withdrawal deduction',
						"INSTALLMENTPERIOD"		=>	$installmentPeriod,
						"TRANSACTIONDATE"		=>	date('Y-m-d'),
						"NOMINALINSTALLMENT"	=>	$reductionAmount,
						"NOMINALSALDO"			=>	$loanNominalSaldoFinal,
						"INPUTUSER"				=>	'Auto System',
						"INPUTDATETIME"			=>	date('Y-m-d H:i:s')
					];
					if($installmentNominal > 0) $this->MainOperation->addData('t_loandriverinstallmenthistory', $arrInsertInstallmentHistory);
					$installmentNominal		-=	$reductionAmount;
				}
			} else {
				break;
				return true;
			}
		}
		
		return true;
	}
	
	public function getDataFinished(){

		$this->load->model('Finance/ModelTransferList');
		
		$startDate	=	validatePostVar($this->postVar, 'dateStart', true);
		$endDate	=	validatePostVar($this->postVar, 'dateEnd', true);
		$startDate	=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate	=	$startDate->format('Y-m-d');
		$endDate	=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate	=	$endDate->format('Y-m-d');
		$dataTable	=	$this->ModelTransferList->getDataFinished($startDate, $endDate);

		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		}
		
		foreach($dataTable as $data){
			$payrollFileName	=	$data->PAYROLLFILE;
			$transferList		=	$this->ModelTransferList->getDataFinishedTransferByFileName($payrollFileName);
			$data->TRANSFERLIST	=	$transferList;
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
}