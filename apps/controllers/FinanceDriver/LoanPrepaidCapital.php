<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class LoanPrepaidCapital extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	var $subtract30Days;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(3);

		if($functionName != "uploadTransferReceiptInstallment" && $functionName != "excelLoanRecap" && $_SERVER['REQUEST_METHOD'] === 'POST'){
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
	
	public function getDataLoanPrepaidCapital(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		$page				=	validatePostVar($this->postVar, 'page', true);
		$keyword			=	validatePostVar($this->postVar, 'keyword', false);
		$viewRequestOnly	=	validatePostVar($this->postVar, 'viewRequestOnly', false);
		$dataTotalRequest	=	$this->ModelLoanPrepaidCapital->getDataTotalLoanPrepaidCapitalRequest();
		$totalRequest		=	$dataTotalRequest['TOTALLOANPREPAIDCAPITALREQUEST'];
		$strArrIdDriver		=	"";
		$msg				=	"OK";
		
		if($viewRequestOnly){
			$strArrIdDriver	=	$dataTotalRequest['STRARRIDDRIVERLOANPREPAIDCAPITALREQUEST'];
			$strArrIdDriver	=	$strArrIdDriver == "" ? "99999999999" : $strArrIdDriver;
		}
		
		$dataTable			=	$this->ModelLoanPrepaidCapital->getDataLoanPrepaidCapital($page, $keyword, 20, $strArrIdDriver);
		
		if(!$dataTable['data']) $msg	=	"Please change your keyword input to search data";
		
		foreach($dataTable['data'] as $keyData){
			$idDriver							=	$keyData->IDDRIVER;
			$keyData->LOANREQUESTDATA			=	$this->ModelLoanPrepaidCapital->getLoanRequestData($idDriver);
			$keyData->PREPAIDCAPITALREQUESTDATA	=	$this->ModelLoanPrepaidCapital->getPrepaidCapitalRequestData($idDriver);
		}
		
		$urlExcelLoanRecap	=	BASE_URL."financeDriver/loanPrepaidCapital/excelLoanRecap/".base64_encode(encodeStringKeyFunction($keyword, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		setResponseOk(array("token"=>$this->newToken, "msg"=>$msg, "result"=>$dataTable, "totalRequest"=>$totalRequest, "urlExcelLoanRecap"=>$urlExcelLoanRecap));
	}
	
	public function getListBankAccountDriver(){
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		$idDriver		=	validatePostVar($this->postVar, 'idDriver', true);
		$dataBankAccount=	$this->ModelLoanPrepaidCapital->getListBankAccountDriver($idDriver);
		
		if(!$dataBankAccount) setResponseNotFound(array("token"=>$this->newToken));
		setResponseOk(array("token"=>$this->newToken, "dataBankAccount"=>$dataBankAccount));	
	}

	public function saveNewLoanRecord(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		$userAdminName			=	validatePostVar($this->postVar, 'NAME', true);
		$idDriver				=	validatePostVar($this->postVar, 'optionDriver', true);
		$idLoanType				=	validatePostVar($this->postVar, 'optionLoanType', true);
		$loanTypeStr			=	validatePostVar($this->postVar, 'loanTypeStr', true);
		$loanDescription		=	validatePostVar($this->postVar, 'loanDescription', true);
		$driverNote				=	validatePostVar($this->postVar, 'driverNote', false);
		$idBankAccount			=	validatePostVar($this->postVar, 'radioBankAccount', false);
		$idBank					=	validatePostVar($this->postVar, 'optionBank', false);
		$accountNumber			=	validatePostVar($this->postVar, 'accountNumber', false);
		$accountHolderName		=	validatePostVar($this->postVar, 'accountHolderName', false);
		$dateRecord				=	validatePostVar($this->postVar, 'dateRecord', true);
		$dateRecord				=	DateTime::createFromFormat('d-m-Y', $dateRecord);
		$dateRecord				=	$dateRecord->format('Y-m-d');
		$loanPrincipalNominal	=	intval(str_replace(",", "", validatePostVar($this->postVar, 'loanPrincipalNominal', true)));
		$interestPerAnnumInteger=	intval(str_replace(",", "", validatePostVar($this->postVar, 'interestPerAnnumInteger', false)));
		$interestPerAnnumDecimal=	intval(str_replace(",", "", validatePostVar($this->postVar, 'interestPerAnnumDecimal', false)));
		$totalPeriodMonth		=	intval(str_replace(",", "", validatePostVar($this->postVar, 'totalPeriodMonth', true)));
		$interestPerAnnumDecimal=	$interestPerAnnumDecimal > 9 ? $interestPerAnnumDecimal / 100 : $interestPerAnnumDecimal / 10;
		$interestPerAnnum		=	$interestPerAnnumInteger + $interestPerAnnumDecimal;
		
		if($idBankAccount == 0) $this->checkInputDataBankAccount();
		if($loanPrincipalNominal <= 0) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Invalid loan principal nominal"));
		
		$interestPerMonth		=	$interestPerAnnum / 12;
		$interestTotal			=	$interestPerMonth * $totalPeriodMonth;
		$interestNominalTotal	=	$loanPrincipalNominal * $interestTotal / 100;
		$interestNominalTotal	=	number_format($interestNominalTotal, 0, '', '');
		$loanTotalNominal		=	$loanPrincipalNominal + $interestNominalTotal;
		$monthlyInstallment		=	$loanTotalNominal / $totalPeriodMonth;
		$monthlyInstallment		=	number_format($monthlyInstallment, 0, '', '');

		if($idBankAccount == 0){
			$statusAccount		=	$this->ModelLoanPrepaidCapital->getListBankAccountDriver($idDriver, 1);
			$statusInsert		=	$statusAccount ? 1 : 0;
			$arrInsertAccount	=	array(
				"IDBANK"			=>	$idBank,
				"IDPARTNERTYPE"		=>	2,
				"IDPARTNER"			=>	$idDriver,
				"ACCOUNTNUMBER"		=>	$accountNumber,
				"ACCOUNTHOLDERNAME"	=>	$accountHolderName,
				"STATUS"			=>	$statusInsert
			);
			$procInsertAccount	=	$this->MainOperation->addData('t_bankaccountpartner', $arrInsertAccount);
			
			if(!$procInsertAccount['status']) switchMySQLErrorCode($procInsertAccount['errCode'], $this->newToken);
			$idBankAccount		=	$procInsertAccount['insertID'];
		}
		
		$arrInsertLoanRequest	=	array(
			"IDDRIVER"				=>	$idDriver,
			"IDBANKACCOUNTPARTNER"	=>	$idBankAccount,
			"IDLOANTYPE"			=>	$idLoanType,
			"NOTES"					=>	$driverNote,
			"AMOUNT"				=>	$loanPrincipalNominal,
			"STATUS"				=>	1,
			"DATETIMEINPUT"			=>	$dateRecord." ".date('H:i:s'),
			"USERUPDATE"			=>	$userAdminName
		);
		$procInsertLoanRequest	=	$this->MainOperation->addData('t_loandriverrequest', $arrInsertLoanRequest);
			
		if(!$procInsertLoanRequest['status']) switchMySQLErrorCode($procInsertLoanRequest['errCode'], $this->newToken);
		
		$idLoanDriverRequest	=	$procInsertLoanRequest['insertID'];
		$detailBankAccount		=	$this->ModelLoanPrepaidCapital->getDetailBankAccountDriver($idBankAccount);
		$idBank					=	$detailBankAccount['IDBANK'];
		$accountNumber			=	$detailBankAccount['ACCOUNTNUMBER'];
		$accountHolderName		=	$detailBankAccount['ACCOUNTHOLDERNAME'];
		$partnerEmail			=	$detailBankAccount['EMAIL'];
		
		if($idLoanType != 3){
			$arrInsertLoanRecap		=	array(
				"IDDRIVER"					=>	$idDriver,
				"IDLOANTYPE"				=>	$idLoanType,
				"IDLOANDRIVERREQUEST"		=>	$idLoanDriverRequest,
				"LOANDATEDISBURSEMENT"		=>	$dateRecord,
				"LOANNOMINALPRINCIPAL"		=>	$loanPrincipalNominal,
				"LOANNOMINALINTEREST"		=>	$interestNominalTotal,
				"LOANNOMINALTOTAL"			=>	$loanTotalNominal,
				"LOANNOMINALSALDO"			=>	$loanTotalNominal,
				"LOANDURATIONMONTH"			=>	$totalPeriodMonth,
				"LOANINTERESTPERANNUM"		=>	$interestPerAnnum,
				"LOANINSTALLMENTPERMONTH"	=>	$monthlyInstallment,
				"LOANINSTALLMENTLASTPERIOD"	=>	substr($dateRecord, 0, 7),
				"LOANSTATUS"				=>	0
			);
			$this->MainOperation->addData('t_loandriverrecap', $arrInsertLoanRecap);
		}
		
		$transferRemark			=	"";
		switch($idLoanType){
			case "1"	:	$transferRemark	=	"LOAN CAR"; break;
			case "2"	:	$transferRemark	=	"LOAN PSL"; break;
			case "3"	:	$transferRemark	=	"PREPCAPT"; break;
			default		:	$transferRemark	=	"OTHERS"; break;
		}
		
		$transferRemark			=	strtoupper($transferRemark." ".date("d M y"));
		$transactioCode			=	"LF".date("dmyHi").str_pad($idDriver, 4, "0", STR_PAD_LEFT);
		$partnerCode			=	$this->MainOperation->getPartnerCode(2, $idDriver);
		$emailList				=	MAILBOX_USERNAME.",".$partnerEmail;
		$arrInsertTransferList	=	array(
			"IDPARTNERTYPE"			=>	2,
			"IDPARTNER"				=>	$idDriver,
			"IDBANK"				=>	$idBank,
			"IDLOANDRIVERREQUEST"	=>	$idLoanDriverRequest,
			"TRANSACTIONCODE"		=>	$transactioCode,
			"ACCOUNTNUMBER"			=>	$accountNumber,
			"ACCOUNTHOLDERNAME"		=>	$accountHolderName,
			"AMOUNT"				=>	$loanPrincipalNominal,
			"PARTNERCODE"			=>	$partnerCode,
			"REMARK"				=>	$transferRemark,
			"EMAILLIST"				=>	$emailList,
			"STATUSDATETIME"		=>	date("Y-m-d H:i:s"),
			"STATUS"				=>	0
		);
		$this->MainOperation->addData("t_transferlist", $arrInsertTransferList);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New driver loan record has been saved"));	
	}
	
	private function checkInputData(){
		$arrVarValidate	=	array(
									array("optionDriver","option","Driver"),
									array("optionLoanType","option","Loan Type"),
									array("dateRecord","text","Record Date"),
									array("loanPrincipalNominal","text","Loan Principal Nominal"),
									array("loanDescription","text","Loan Description")
							);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;		
	}
	
	private function checkInputDataBankAccount(){
		$arrVarValidate	=	array(
			array("optionBank", "option", "Bank"),
			array("accountNumber", "option", "Account Number"),
			array("accountHolderName", "text", "Account Holder Name")
		);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;		
	}

	public function excelLoanRecap($encryptedVar){
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$keyword		=	$expDecryptedVar[0];
		$dataTable		=	$this->ModelLoanPrepaidCapital->getDataLoanPrepaidCapitalExcel($keyword);
		
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
		$sheet->setCellValue('A2', 'All Driver Loan Recap Report');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:F1');
		$sheet->mergeCells('A2:F2');
		
		$sheet->setCellValue('A4', 'Keyword Search : '.$keyword);
		$sheet->setCellValue('A6', 'Driver Type');
		$sheet->setCellValue('B6', 'Driver Name');
		$sheet->setCellValue('C6', 'Loan Car');
		$sheet->setCellValue('D6', 'Loan Personal');
		$sheet->setCellValue('E6', 'Prepaid Capital');
		$sheet->setCellValue('F6', 'Grand Total');
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
		
		$grandTotalLoanCar = $grandTotalLoanPersonal = $grandTotalPrepaidCapital = $grandTotalLoan	=	0;
		foreach($dataTable as $data){
			
			$grandTotalLoanPerRow		=	$data->TOTALLOANCAR + $data->TOTALLOANPERSONAL + $data->TOTALPREPAIDCAPITAL;
			$grandTotalLoanCar			+=	$data->TOTALLOANCAR;
			$grandTotalLoanPersonal		+=	$data->TOTALLOANPERSONAL;
			$grandTotalPrepaidCapital	+=	$data->TOTALPREPAIDCAPITAL;
			$grandTotalLoan				+=	$grandTotalLoanPerRow;
			
			$sheet->setCellValue('A'.$rowNumber, $data->DRIVERTYPE);
			$sheet->setCellValue('B'.$rowNumber, $data->NAME);
			$sheet->setCellValue('C'.$rowNumber, $data->TOTALLOANCAR);			$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('D'.$rowNumber, $data->TOTALLOANPERSONAL);		$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('E'.$rowNumber, $data->TOTALPREPAIDCAPITAL);	$sheet->getStyle('E'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('F'.$rowNumber, $grandTotalLoanPerRow);		$sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');
			$rowNumber++;
			
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'GRAND TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':B'.$rowNumber);
		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('C'.$rowNumber, $grandTotalLoanCar);		$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('C'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('D'.$rowNumber, $grandTotalLoanPersonal);	$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('D'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('E'.$rowNumber, $grandTotalPrepaidCapital);$sheet->getStyle('E'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('E'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('F'.$rowNumber, $grandTotalLoan);			$sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('F'.$rowNumber)->getFont()->setBold( true );
		
		$sheet->getStyle('A6:F'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('F'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(18);
		$sheet->getColumnDimension('B')->setWidth(24);
		$sheet->getColumnDimension('C')->setWidth(16);
		$sheet->getColumnDimension('D')->setWidth(16);
		$sheet->getColumnDimension('E')->setWidth(16);
		$sheet->getColumnDimension('F')->setWidth(16);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDriverLoanRecap_'.date('YmdHis');
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
	
	public function getDetailLoanPrepaidCapitalRequest(){
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		$idLoanDriverRequest=	validatePostVar($this->postVar, 'idLoanDriverRequest', true);
		$detailLoanRequest	=	$this->ModelLoanPrepaidCapital->getDetailLoanPrepaidCapitalRequest($idLoanDriverRequest);
		
		if(!$detailLoanRequest) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Loan / prepaid capital request not found"));
		setResponseOk(array("token"=>$this->newToken, "detailLoanRequest"=>$detailLoanRequest));	
	}
		
	public function approveRejectLoanPrepaidCapitalRequest(){
		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		$idLoanDriverRequest	=	validatePostVar($this->postVar, 'idLoanDriverRequest', true);
		$status					=	validatePostVar($this->postVar, 'status', true);
		$strStatus				=	$status == 1 ? "approved" : "rejected";
		$userAdminName			=	validatePostVar($this->postVar, 'NAME', true);
		$interestPerAnnumInteger=	$status == 1 ? intval(str_replace(",", "", validatePostVar($this->postVar, 'interestPerAnnumInteger', false))) : 0;
		$interestPerAnnumDecimal=	$status == 1 ? intval(str_replace(",", "", validatePostVar($this->postVar, 'interestPerAnnumDecimal', false))) : 0;
		$totalPeriodMonth		=	$status == 1 ? intval(str_replace(",", "", validatePostVar($this->postVar, 'totalPeriodMonth', true))) : 0;
		$interestPerAnnumDecimal=	$interestPerAnnumDecimal > 9 ? $interestPerAnnumDecimal / 100 : $interestPerAnnumDecimal / 10;
		$interestPerAnnum		=	$interestPerAnnumInteger + $interestPerAnnumDecimal;
		$detailLoanRequest		=	$this->ModelLoanPrepaidCapital->getDetailLoanPrepaidCapitalRequest($idLoanDriverRequest);
		
		if(!$detailLoanRequest) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Loan / prepaid capital request not found. Please try again later"));
		
		$idDriver			=	$detailLoanRequest['IDDRIVER'];
		$loanAmount			=	$detailLoanRequest['AMOUNT'];
		$statusLoanCapital	=	$detailLoanRequest['STATUSLOANCAPITAL'];
		$idLoanType			=	$detailLoanRequest['IDLOANTYPE'];
		$strLoanType		=	$statusLoanCapital == 1 ? "Loan (".$detailLoanRequest['LOANTYPE'].")" : "Prepaid Capital";
		
		if($interestPerAnnum <= 0 && $idLoanType != 3 && $status == 1) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Invalid interest per annum percentage value"));
		
		$arrUpdateRequest	=	array(
			"STATUS"			=>	$status,
			"DATETIMEUPDATE"	=>	date('Y-m-d H:i:s'),
			"USERUPDATE"		=>	$userAdminName
		);
		$procUpdateRequest	=	$this->MainOperation->updateData("t_loandriverrequest", $arrUpdateRequest, "IDLOANDRIVERREQUEST", $idLoanDriverRequest);
		
		if(!$procUpdateRequest['status']) switchMySQLErrorCode($procUpdateRequest['errCode'], $this->newToken);
		$dataMessageType	=	$this->MainOperation->getDataMessageType(6);
		$activityMessage	=	$dataMessageType['ACTIVITY'];
		$title				=	"Loan / prepaid capital request has been ".$strStatus;
		$body				=	"Your ".$strLoanType." [".number_format($loanAmount, 0, '.', ',')."] request has been ".$strStatus." by ".$userAdminName.".";
		$additionalArray	=	array(
			"activity"	=>	$activityMessage,
			"idPrimary"	=>	$idLoanDriverRequest,
		);
			
		$arrInsertMsg	=	array(
				"IDMESSAGEPARTNERTYPE"	=>	6,
				"IDPARTNERTYPE"			=>	2,
				"IDPARTNER"				=>	$idDriver,
				"IDPRIMARY"				=>	$idLoanDriverRequest,
				"TITLE"					=>	$title,
				"MESSAGE"				=>	$body,
				"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
		);
		$procInsertMsg	=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
			
		if($procInsertMsg['status']){
			$dataDriver			=	$this->MainOperation->getDataDriver($idDriver);
			$driverTokenFCM		=	$dataDriver['TOKENFCM'];
			if($driverTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($driverTokenFCM, $title, $body, $additionalArray);
		}
		
		if($status == 1){
			$interestPerMonth		=	$interestPerAnnum / 12;
			$interestTotal			=	$interestPerMonth * $totalPeriodMonth;
			$interestNominalTotal	=	$loanAmount * $interestTotal / 100;
			$interestNominalTotal	=	number_format($interestNominalTotal, 0, '', '');
			$loanTotalNominal		=	$loanAmount + $interestNominalTotal;
			$monthlyInstallment		=	$loanTotalNominal / $totalPeriodMonth;
			$monthlyInstallment		=	number_format($monthlyInstallment, 0, '', '');

			if($idLoanType != 3){
				$arrInsertLoanRecap		=	array(
					"IDDRIVER"					=>	$idDriver,
					"IDLOANTYPE"				=>	$idLoanType,
					"IDLOANDRIVERREQUEST"		=>	$idLoanDriverRequest,
					"LOANNOMINALPRINCIPAL"		=>	$loanAmount,
					"LOANNOMINALINTEREST"		=>	$interestNominalTotal,
					"LOANNOMINALTOTAL"			=>	$loanTotalNominal,
					"LOANNOMINALSALDO"			=>	$loanTotalNominal,
					"LOANDURATIONMONTH"			=>	$totalPeriodMonth,
					"LOANINTERESTPERANNUM"		=>	$interestPerAnnum,
					"LOANINSTALLMENTPERMONTH"	=>	$monthlyInstallment,
					"LOANINSTALLMENTLASTPERIOD"	=>	date('Y-m'),
					"LOANSTATUS"				=>	0
				);
				$this->MainOperation->addData('t_loandriverrecap', $arrInsertLoanRecap);
			}

			$idLoanType				=	$detailLoanRequest['IDLOANTYPE'];
			$idBankTransfer			=	$detailLoanRequest['IDBANK'];
			$bankAccountNumber		=	$detailLoanRequest['ACCOUNTNUMBER'];
			$bankAccountHolderName	=	$detailLoanRequest['ACCOUNTHOLDERNAME'];
			$driverEmail			=	$detailLoanRequest['DRIVEREMAIL'];
			$transactioCode			=	"LF".date("dmyHi").str_pad($idDriver, 4, "0", STR_PAD_LEFT);
			$partnerCode			=	$this->MainOperation->getPartnerCode(2, $idDriver);
			$transferRemark			=	"";
			
			switch($idLoanType){
				case "1"	:	$transferRemark	=	"LOAN CAR"; break;
				case "2"	:	$transferRemark	=	"LOAN PSL"; break;
				case "3"	:	$transferRemark	=	"PREPCAPT"; break;
				default		:	$transferRemark	=	"OTHERS"; break;
			}
			
			$transferRemark			=	strtoupper($transferRemark." ".date("d M y"));
			$emailList				=	MAILBOX_USERNAME.",".$driverEmail;
			$arrInsertTransferList	=	array(
				"IDPARTNERTYPE"			=>	2,
				"IDPARTNER"				=>	$idDriver,
				"IDBANK"				=>	$idBankTransfer,
				"IDLOANDRIVERREQUEST"	=>	$idLoanDriverRequest,
				"TRANSACTIONCODE"		=>	$transactioCode,
				"ACCOUNTNUMBER"			=>	$bankAccountNumber,
				"ACCOUNTHOLDERNAME"		=>	$bankAccountHolderName,
				"AMOUNT"				=>	$loanAmount,
				"PARTNERCODE"			=>	$partnerCode,
				"REMARK"				=>	$transferRemark,
				"EMAILLIST"				=>	$emailList,
				"STATUSDATETIME"		=>	date("Y-m-d H:i:s"),
				"STATUS"				=>	0
			);
			$this->MainOperation->addData("t_transferlist", $arrInsertTransferList);
		} else {
			$this->MainOperation->deleteData('t_loandriverrecap', ['IDLOANDRIVERREQUEST' => $idLoanDriverRequest]);
		}
		
		$this->updateWebappStatisticTags();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver loan request has been <b>".$strStatus."</b>"));
	}
	
	public function updateWebappStatisticTags(){		
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		if(PRODUCTION_URL){
			$totalLoanPrepaidCapitalRequest	=	$this->ModelLoanPrepaidCapital->getTotalLoanPrepaidCapitalRequest();
			$totalLoanInstallmentRequest	=	$this->ModelLoanPrepaidCapital->getTotalLoanInstallmentRequest();
			$totalAllRequest				=	$totalLoanPrepaidCapitalRequest + $totalLoanInstallmentRequest;
			try {
				$factory	=	(new Factory)
								->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
								->withDatabaseUri(FIREBASE_RTDB_URI);
				$database	=	$factory->createDatabase();
				$reference	=	$database->getReference(FIREBASE_RTDB_MAINREF_NAME."unprocessedFinanceDriver/loanPrepaidCapital")
								->set([
									'newLoanPrepaidCapitalStatus'	=>	false,
									'newLoanPrepaidCapitalTotal'	=>	$totalAllRequest,
									'timestampUpdate'				=>	gmdate("YmdHis")
								]);
			} catch (Exception $e) {
			}
		}
		
		return true;
	}
	
	public function getDetailHistoryLoanPrepaidCapital(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		$idDriver			=	validatePostVar($this->postVar, 'idDriver', true);
		$typeLoanCapital	=	validatePostVar($this->postVar, 'typeLoanCapital', true);
		$detailDriver		=	$this->MainOperation->getDataDriver($idDriver);
		
		if(!$detailDriver){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Details not found for this driver. Invalid data"));
		}
		
		$dataHistory		=	$this->ModelLoanPrepaidCapital->getHistoryLoanPrepaidCapital($idDriver, $typeLoanCapital, 1);
		$loanPrepaidBalance	=	$this->ModelLoanPrepaidCapital->getLoanPrepaidCapitalBalance($idDriver, $typeLoanCapital, false, false);
		
		if($dataHistory){
			$dataHistory	=	array_reverse($dataHistory);
			$currentBalance	=	0;
			foreach($dataHistory as $keyHistory){
				$transactionType	=	$keyHistory->TYPE;
				$amount				=	$keyHistory->AMOUNT;
				$currentBalance		=	$transactionType == "K" ? $currentBalance - $amount : $currentBalance + $amount;
				$keyHistory->SALDO	=	$currentBalance;
			}
		}
		
		setResponseOk(array("token"=>$this->newToken, "detailDriver"=>$detailDriver, "dataHistory"=>$dataHistory, "loanPrepaidBalance"=>$loanPrepaidBalance));
	}

	public function getDataLoanPerDriver(){
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		$idDriver			=	validatePostVar($this->postVar, 'idDriver', false);
		$idLoanType			=	validatePostVar($this->postVar, 'idLoanType', false);
		$startDate			=	validatePostVar($this->postVar, 'startDate', true);
		$endDate			=	validatePostVar($this->postVar, 'endDate', true);
		$startDate			=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate			=	$startDate->format('Y-m-d');
		$endDate			=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate			=	$endDate->format('Y-m-d');
		$urlExcelPerDriver	=	"";
		$dataTable			=	$this->ModelLoanPrepaidCapital->getDataLoanPerDriver($idDriver, $idLoanType, $startDate, $endDate);
		
		$urlExcelPerDriver		=	BASE_URL."financeDriver/loanPrepaidCapital/excelLoanPerDriver/".base64_encode(encodeStringKeyFunction($idDriver."|".$idLoanType."|".$startDate."|".$endDate, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		$saldoLoanCar			=	$this->ModelLoanPrepaidCapital->getLoanPrepaidCapitalBalance($idDriver, false, 1, false);
		$saldoLoanPersonal		=	$this->ModelLoanPrepaidCapital->getLoanPrepaidCapitalBalance($idDriver, false, 2, false);
		$saldoLoanPrepaidCapital=	$this->ModelLoanPrepaidCapital->getLoanPrepaidCapitalBalance($idDriver, false, 3, false);
		$dataActiveLoanRecap	=	$this->ModelLoanPrepaidCapital->getDataActiveLoanRecap($idDriver);
		
		if(count($dataActiveLoanRecap) > 0){
			foreach($dataActiveLoanRecap as $keyActiveLoanRecap){
				$idLoanDriverRecap		=	$keyActiveLoanRecap->IDLOANDRIVERRECAP;
				$dataHistoryInstallment	=	$this->ModelLoanPrepaidCapital->getDataHistoryLoanInstallment($idLoanDriverRecap);
				$keyActiveLoanRecap->DATAHISTORYLOANINSTALLMENT	=	$dataHistoryInstallment;
			}
		}
		
		setResponseOk(
			array(
				"token"						=>	$this->newToken,
				"dataTable"					=>	$dataTable,
				"urlExcelPerDriver"			=>	$urlExcelPerDriver,
				"saldoLoanCar"				=>	$saldoLoanCar,
				"saldoLoanPersonal"			=>	$saldoLoanPersonal,
				"saldoLoanPrepaidCapital"	=>	$saldoLoanPrepaidCapital,
				"dataActiveLoanRecap"		=>	$dataActiveLoanRecap
			)
		);
	}
	
	public function uploadTransferReceiptInstallment($idDriver){
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
		$namaFile	=	"manualRecordInstallment"."_".$idDriver."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$namaFile);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "urlTransferReceipt"=>URL_TRANSFER_RECEIPT.$namaFile, "transferReceiptFileName"=>$namaFile));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}
	}
	
	public function saveNewInstallmentRecord(){
		$this->checkInputDataInstallment();
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		$userAdminName			=	validatePostVar($this->postVar, 'NAME', true);
		$idDriver				=	validatePostVar($this->postVar, 'optionDriverInstallment', true);
		$idLoanType				=	validatePostVar($this->postVar, 'optionInstallmentLoanType', true);
		$loanTypeStr			=	validatePostVar($this->postVar, 'loanTypeStr', true);
		$driverName				=	validatePostVar($this->postVar, 'driverName', true);
		$dateRecord				=	validatePostVar($this->postVar, 'dateRecordInstallment', true);
		$dateRecordDT			=	DateTime::createFromFormat('d-m-Y', $dateRecord);
		$dateRecord				=	$dateRecordDT->format('Y-m-d');
		$installmentNominal		=	intval(str_replace(",", "", validatePostVar($this->postVar, 'loanInstallmentNominal', true)));
		$installmentDescription	=	validatePostVar($this->postVar, 'loanInstallmentDescription', true);
		$transferReceiptFileName=	validatePostVar($this->postVar, 'transferReceiptInstallmentFileName', false);

		if($installmentNominal <= 0) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Invalid installment nominal"));
		
		$loanBalance			=	$this->ModelLoanPrepaidCapital->getLoanPrepaidCapitalBalance($idDriver, false, $idLoanType, false);
		if($installmentNominal > $loanBalance) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Installment payment should nor be more than IDR ".number_format($loanBalance, 0, '.', ',')));
		
		$arrInsertInstallmentRequest	=	array(
			"IDDRIVER"				=>	$idDriver,
			"IDLOANTYPE"			=>	$idLoanType,
			"AMOUNT"				=>	$installmentNominal,
			"NOTES"					=>	"Manual add installment record",
			"FILETRANSFERRECEIPT"	=>	$transferReceiptFileName,
			"DATETIMEINPUT"			=>	date('Y-m-d H:i:s'),
			"DATETIMEAPPROVE"		=>	date('Y-m-d H:i:s'),
			"USERAPPROVE"			=>	$userAdminName,
			"STATUS"				=>	1
		);
		$procInsertInstallmentRequest	=	$this->MainOperation->addData('t_loandriverinstallmentrequest', $arrInsertInstallmentRequest);
			
		if(!$procInsertInstallmentRequest['status']) switchMySQLErrorCode($procInsertInstallmentRequest['errCode'], $this->newToken);

		$idInstallmentRequest	=	$procInsertInstallmentRequest['insertID'];
		$loanTypeStr			=	$loanTypeStr == "Prepaid Capital" ? $loanTypeStr : "Loan ".$loanTypeStr;
		$installmentDescription	=	"Installment for ".$loanTypeStr." (".$installmentDescription."). Manual input by : ".$userAdminName;
		$arrInsertLoanRecord	=	array(
			"IDDRIVER"						=>	$idDriver,
			"IDLOANTYPE"					=>	$idLoanType,
			"IDLOANDRIVERINSTALLMENTREQUEST"=>	$idInstallmentRequest,
			"TYPE"							=>	"K",
			"DESCRIPTION"					=>	$installmentDescription,
			"AMOUNT"						=>	$installmentNominal,
			"DATETIMEINPUT"					=>	$dateRecord." ".date('H:i:s'),
			"USERINPUT"						=>	$userAdminName
		);
		$procInsertLoanRecord	=	$this->MainOperation->addData('t_loandriverrecord', $arrInsertLoanRecord);
			
		if(!$procInsertLoanRecord['status']) switchMySQLErrorCode($procInsertLoanRecord['errCode'], $this->newToken);
		$this->calculateDriverLoanRecap($idDriver, $idLoanType, $installmentNominal, $userAdminName, 'Manual installment record', $dateRecord);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New driver installment record has been saved"));	
	}
	
	private function checkInputDataInstallment(){
		$arrVarValidate	=	array(
			array("optionDriverInstallment","option","Driver"),
			array("optionInstallmentLoanType","option","Loan Type"),
			array("dateRecordInstallment","text","Record Date"),
			array("loanInstallmentNominal","text","Installment Nominal"),
			array("loanInstallmentDescription","text","Installment Description"),
			array("transferReceiptInstallmentFileName","option","Transfer Receipt")
		);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;		
	}
	
	public function excelLoanPerDriver($encryptedVar){
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$idDriver		=	$expDecryptedVar[0];
		$idLoanType		=	$expDecryptedVar[1];
		$startDate		=	$expDecryptedVar[2];
		$endDate		=	$expDecryptedVar[3];
		$dataTable		=	$this->ModelLoanPrepaidCapital->getDataLoanPerDriver($idDriver, $idLoanType, $startDate, $endDate);
		
		if(!$dataTable){
			echo "No data found!";
			die();
		}
		
		$driverData				=	$this->MainOperation->getDataDriver($idDriver);
		$driverName				=	$driverData['NAME'];
		$saldoLoanCar			=	$this->ModelLoanPrepaidCapital->getLoanPrepaidCapitalBalance($idDriver, false, 1, false);
		$saldoLoanPersonal		=	$this->ModelLoanPrepaidCapital->getLoanPrepaidCapitalBalance($idDriver, false, 2, false);
		$saldoLoanPrepaidCapital=	$this->ModelLoanPrepaidCapital->getLoanPrepaidCapitalBalance($idDriver, false, 3, false);
		$spreadsheet			=	new Spreadsheet();
		$sheet					=	$spreadsheet->getActiveSheet();
		
		$sheet->getPageSetup()->setOrientation(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		$sheet->getPageSetup()->setPaperSize(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(0.25);
		$sheet->getPageMargins()->setRight(0.2);
		$sheet->getPageMargins()->setLeft(0.2);
		$sheet->getPageMargins()->setBottom(0.25);
		
		$sheet->setCellValue('A1', 'Bali Sun Tours');
		$sheet->setCellValue('A2', 'Data Driver Loan Report');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:F1');
		$sheet->mergeCells('A2:F2');
		
		$sheet->setCellValue('A4', 'Driver Name : '.$driverName);
		$sheet->setCellValue('A5', 'Date Period : '.$startDate." - ".$endDate);
		$sheet->setCellValue('A6', 'Saldo Car Loan : '.$saldoLoanCar);
		$sheet->setCellValue('A7', 'Saldo Personal Loan : '.$saldoLoanPersonal);
		$sheet->setCellValue('A8', 'Saldo Prepaid Capital : '.$saldoLoanPrepaidCapital);
		
		$sheet->setCellValue('A10', 'No.');
		$sheet->setCellValue('B10', 'Date Time');
		$sheet->setCellValue('C10', 'Loan Type');
		$sheet->setCellValue('D10', 'Description');
		$sheet->setCellValue('E10', 'DB/CR');
		$sheet->setCellValue('F10', 'Amount');
		$sheet->getStyle('A10:F10')->getFont()->setBold( true );
		$sheet->getStyle('A10:F10')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A10:F10')->getAlignment()->setVertical('center');
		$rowNumber	=	11;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$number		=	1;
		foreach($dataTable as $data){
			$dbcrType	=	$data->TYPE == "K" ? "CR" : "DB";
			$amount		=	$data->TYPE == "K" ? $data->AMOUNT * -1 : $data->AMOUNT;
			$sheet->setCellValue('A'.$rowNumber, $number);
			$sheet->setCellValue('B'.$rowNumber, $data->DATETIMEINPUTSTR);
			$sheet->setCellValue('C'.$rowNumber, $data->LOANTYPE);
			$sheet->setCellValue('D'.$rowNumber, $data->DESCRIPTION);
			$sheet->setCellValue('E'.$rowNumber, $dbcrType);
			$sheet->setCellValue('F'.$rowNumber, $amount);	$sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');
			$number++;
			$rowNumber++;
		}
		
		$sheet->getStyle('A10:F'.($rowNumber-1))->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.($rowNumber-1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('F'.($rowNumber-1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(17);
		$sheet->getColumnDimension('C')->setWidth(16);
		$sheet->getColumnDimension('D')->setWidth(48);
		$sheet->getColumnDimension('E')->setWidth(8);
		$sheet->getColumnDimension('F')->setWidth(16);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDataLoanPerDriver_'.date('YmdHis');
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}
	
	public function getDataLoanInstallmentRequest(){
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		$idDriverType	=	validatePostVar($this->postVar, 'idDriverType', false);
		$idDriver		=	validatePostVar($this->postVar, 'idDriver', false);
		$startDate		=	validatePostVar($this->postVar, 'startDate', true);
		$endDate		=	validatePostVar($this->postVar, 'endDate', true);
		$startDate		=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate		=	$startDate->format('Y-m-d');
		$endDate		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate		=	$endDate->format('Y-m-d');
		$viewRequestOnly=	validatePostVar($this->postVar, 'viewRequestOnly', false);
		$dataTable		=	$this->ModelLoanPrepaidCapital->getDataLoanInstallmentRequest($idDriverType, $idDriver, $startDate, $endDate, $viewRequestOnly);
		
		if(!$dataTable) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No installment request data found"));
		setResponseOk(array("token"=>$this->newToken, "dataTable"=>$dataTable));	
	}
	
	public function getDetailLoanInstallmentRequest(){
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		$idLoanInstallmentRequest	=	validatePostVar($this->postVar, 'idLoanInstallmentRequest', true);
		$detailInstallmentRequest	=	$this->ModelLoanPrepaidCapital->getDetailLoanInstallmentRequest($idLoanInstallmentRequest);
		
		if(!$detailInstallmentRequest) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail installment request data found"));
		setResponseOk(array("token"=>$this->newToken, "detailInstallmentRequest"=>$detailInstallmentRequest));	
	}
	
	public function approveRejectInstallmentRequest(){
		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		$idLoanInstallmentRequest	=	validatePostVar($this->postVar, 'idLoanInstallmentRequest', true);
		$status						=	validatePostVar($this->postVar, 'status', true);
		$strStatus					=	$status == 1 ? "approved" : "rejected";
		$userAdminName				=	validatePostVar($this->postVar, 'NAME', true);
		$detailInstallmentRequest	=	$this->ModelLoanPrepaidCapital->getDetailLoanInstallmentRequest($idLoanInstallmentRequest);
		
		if(!$detailInstallmentRequest) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Loan installment request not found. Please try again later"));

		$idDriver			=	$detailInstallmentRequest['IDDRIVER'];
		$installmentAmount	=	$detailInstallmentRequest['AMOUNT'];
		$statusLoanCapital	=	$detailInstallmentRequest['STATUSLOANCAPITAL'];
		$idLoanType			=	$detailInstallmentRequest['IDLOANTYPE'];
		$amount				=	$detailInstallmentRequest['AMOUNT'];
		$notes				=	$detailInstallmentRequest['NOTES'];
		$strLoanType		=	$statusLoanCapital == 1 ? "Loan (".$detailInstallmentRequest['LOANTYPE'].")" : "Prepaid Capital";
		$arrUpdateRequest	=	array(
									"STATUS"			=>	$status,
									"DATETIMEAPPROVE"	=>	date('Y-m-d H:i:s'),
									"USERAPPROVE"		=>	$userAdminName
								);
		$procUpdateRequest	=	$this->MainOperation->updateData("t_loandriverinstallmentrequest", $arrUpdateRequest, "IDLOANDRIVERINSTALLMENTREQUEST", $idLoanInstallmentRequest);
		
		if(!$procUpdateRequest['status']) switchMySQLErrorCode($procUpdateRequest['errCode'], $this->newToken);
		
		$dataMessageType	=	$this->MainOperation->getDataMessageType(9);
		$activityMessage	=	$dataMessageType['ACTIVITY'];
		$title				=	"Loan installment has been ".$strStatus;
		$body				=	"Your ".$strLoanType." [".number_format($amount, 0, '.', ',')."] installment has been ".$strStatus." by ".$userAdminName.".";
		$additionalArray	=	array(
									"activity"	=>	$activityMessage,
									"idPrimary"	=>	$idLoanInstallmentRequest,
								);
			
		$arrInsertMsg	=	array(
			"IDMESSAGEPARTNERTYPE"	=>	9,
			"IDPARTNERTYPE"			=>	2,
			"IDPARTNER"				=>	$idDriver,
			"IDPRIMARY"				=>	$idLoanInstallmentRequest,
			"TITLE"					=>	$title,
			"MESSAGE"				=>	$body,
			"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
		);
		$procInsertMsg	=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
			
		if($procInsertMsg['status']){
			$dataDriver			=	$this->MainOperation->getDataDriver($idDriver);
			$driverTokenFCM		=	$dataDriver['TOKENFCM'];
			if($driverTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($driverTokenFCM, $title, $body, $additionalArray);
		}
		
		if($status == 1){
			$arrInsertLoanRecord	=	array(
				"IDDRIVER"						=>	$idDriver,
				"IDLOANTYPE"					=>	$idLoanType,
				"IDLOANDRIVERINSTALLMENTREQUEST"=>	$idLoanInstallmentRequest,
				"TYPE"							=>	'K',
				"DESCRIPTION"					=>	'Installment for '.$strLoanType.'. Request by driver. Note : '.$notes,
				"AMOUNT"						=>	$amount,
				"DATETIMEINPUT"					=>	date("Y-m-d H:i:s"),
				"USERINPUT"						=>	$userAdminName
			);
			$this->MainOperation->addData("t_loandriverrecord", $arrInsertLoanRecord);
		}
		
		$this->updateWebappStatisticTags();
		$this->calculateDriverLoanRecap($idDriver, $idLoanType, $amount, $userAdminName, 'Installment record through admin approval', date("Y-m-d"));
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver loan installment has been <b>".$strStatus."</b>"));
	}

	private function calculateDriverLoanRecap($idDriver, $idLoanType, $installmentNominal, $userAdminName, $installmentDescription, $installmentDate){
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
						"DESCRIPTION"			=>	$installmentDescription,
						"INSTALLMENTPERIOD"		=>	$installmentPeriod,
						"TRANSACTIONDATE"		=>	$installmentDate,
						"NOMINALINSTALLMENT"	=>	$reductionAmount,
						"NOMINALSALDO"			=>	$loanNominalSaldoFinal,
						"INPUTUSER"				=>	$userAdminName,
						"INPUTDATETIME"			=>	date('Y-m-d H:i:s')
					];
					if($installmentNominal > 0) $this->MainOperation->addData('t_loandriverinstallmenthistory', $arrInsertInstallmentHistory);
					
					$installmentNominal		-=	$reductionAmount;
					
					log_message('debug', 'reductionAmount :: '.$reductionAmount.', loanNominalSaldoFinal :: '.$loanNominalSaldoFinal.',  installmentNominal :: '.$installmentNominal);
				}
			} else {
				break;
				return true;
			}
		}
		
		return true;
	}
}