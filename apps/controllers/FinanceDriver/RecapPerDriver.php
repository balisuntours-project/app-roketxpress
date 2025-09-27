<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class RecapPerDriver extends CI_controller {
	
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
	
	public function getDataAllDriverRecap(){

		$this->load->model('FinanceDriver/ModelRecapPerDriver');
		$this->load->model('MainOperation');
		
		$page					=	validatePostVar($this->postVar, 'page', true);
		$idDriverType			=	validatePostVar($this->postVar, 'idDriverType', false);
		$idDriver				=	validatePostVar($this->postVar, 'idDriver', false);
		$dataTable				=	$this->ModelRecapPerDriver->getDataAllDriverRecap($page, 25, $idDriverType, $idDriver);
		$urlexcelAllDriverRecap	=	BASE_URL."financeDriver/recapPerDriver/excelAllDriverRecap/".base64_encode(encodeStringKeyFunction($idDriverType."|".$idDriver, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlexcelAllDriverRecap"=>$urlexcelAllDriverRecap));
	
	}
	
	public function excelAllDriverRecap($encryptedVar){

		$this->load->model('FinanceDriver/ModelRecapPerDriver');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$idDriverType	=	$expDecryptedVar[0];
		$idDriver		=	$expDecryptedVar[1];

		$driverType		=	isset($idDriverType) && $idDriverType != "" && $idDriverType != 0 ? $this->MainOperation->getDriverTypeById($idDriverType) : "All Driver Type";
		$driverName		=	isset($idDriver) && $idDriver != "" && $idDriver != 0 ? $this->MainOperation->getDriverNameById($idDriver) : "All Driver";
		$dataTable		=	$this->ModelRecapPerDriver->getDataAllDriverRecap(1, 999999, $idDriverType, $idDriver);
		
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
		$sheet->setCellValue('A2', 'All Driver Finance Recap Report');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:J1');
		$sheet->mergeCells('A2:J2');
		
		$sheet->setCellValue('A4', 'Driver Type : '.$driverType);
		$sheet->setCellValue('A5', 'Driver Name : '.$driverName);
		
		$sheet->setCellValue('A7', 'Driver Type');
		$sheet->setCellValue('B7', 'Driver Name');
		$sheet->setCellValue('C7', 'Schedule');
		$sheet->setCellValue('D7', 'Fee');
		$sheet->setCellValue('E7', 'Additional Cost');
		$sheet->setCellValue('F7', 'Reimbursement');
		$sheet->setCellValue('G7', 'Review Bonus Punishment');
		$sheet->setCellValue('H7', 'Collect Payment');
		$sheet->setCellValue('I7', 'Prepaid Capital');
		$sheet->setCellValue('J7', 'Grand Total');
		$sheet->getStyle('A7:J7')->getFont()->setBold( true );
		$sheet->getStyle('A7:J7')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A7:J7')->getAlignment()->setVertical('center');
		$rowNumber	=	8;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$grandTotalSchedule = $grandTotalAdditionalCost = $grandTotalReimbursement = $grandTotalReviewBonusPunishment = $grandTotalFee = $grandTotalCollectPayment = $grandTotalPrepaidCapital = $grandTotalAll	=	0;
		foreach($dataTable['data'] as $data){
			
			$grandTotalPerRow				=	$data->TOTALADDITIONALCOST + $data->TOTALFEE + $data->TOTALREIMBURSEMENT + $data->TOTALREVIEWBONUSPUNISHMENT - $data->TOTALCOLLECTPAYMENT - $data->TOTALPREPAIDCAPITAL;
			$grandTotalSchedule				+=	$data->TOTALSCHEDULE;
			$grandTotalAdditionalCost		+=	$data->TOTALADDITIONALCOST;
			$grandTotalReimbursement		+=	$data->TOTALREIMBURSEMENT;
			$grandTotalReviewBonusPunishment+=	$data->TOTALREVIEWBONUSPUNISHMENT;
			$grandTotalFee					+=	$data->TOTALFEE;
			$grandTotalCollectPayment		+=	$data->TOTALCOLLECTPAYMENT;
			$grandTotalPrepaidCapital		+=	$data->TOTALPREPAIDCAPITAL;
			$grandTotalAll					+=	$grandTotalPerRow;
			
			$sheet->setCellValue('A'.$rowNumber, $data->DRIVERTYPE);
			$sheet->setCellValue('B'.$rowNumber, $data->DRIVERNAME);
			$sheet->setCellValue('C'.$rowNumber, $data->TOTALSCHEDULE);				$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('D'.$rowNumber, $data->TOTALFEE);					$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('E'.$rowNumber, $data->TOTALADDITIONALCOST);		$sheet->getStyle('E'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('F'.$rowNumber, $data->TOTALREIMBURSEMENT);		$sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('G'.$rowNumber, $data->TOTALREVIEWBONUSPUNISHMENT);$sheet->getStyle('G'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('H'.$rowNumber, $data->TOTALCOLLECTPAYMENT);		$sheet->getStyle('H'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('I'.$rowNumber, $data->TOTALPREPAIDCAPITAL);		$sheet->getStyle('I'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('J'.$rowNumber, $grandTotalPerRow);				$sheet->getStyle('J'.$rowNumber)->getAlignment()->setHorizontal('right');
			$rowNumber++;
			
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':B'.$rowNumber);
		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('C'.$rowNumber, $grandTotalSchedule);				$sheet->getStyle('C'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('C'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('D'.$rowNumber, $grandTotalFee);					$sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('D'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('E'.$rowNumber, $grandTotalAdditionalCost);		$sheet->getStyle('E'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('E'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('F'.$rowNumber, $grandTotalReimbursement);			$sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('F'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('G'.$rowNumber, $grandTotalReviewBonusPunishment);	$sheet->getStyle('G'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('G'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('H'.$rowNumber, $grandTotalCollectPayment);		$sheet->getStyle('H'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('H'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('I'.$rowNumber, $grandTotalPrepaidCapital);		$sheet->getStyle('I'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('I'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('J'.$rowNumber, $grandTotalAll);					$sheet->getStyle('J'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('J'.$rowNumber)->getFont()->setBold( true );
		
		$sheet->getStyle('A7:J'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('J'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(18);
		$sheet->getColumnDimension('B')->setWidth(24);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(15);
		$sheet->getColumnDimension('I')->setWidth(15);
		$sheet->getColumnDimension('J')->setWidth(15);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDriverFinanceRecap_'.$driverType.'_'.$driverName;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}
	
	public function getDataFeePerPeriod(){

		$this->load->model('FinanceDriver/ModelRecapPerDriver');
		$this->load->model('MainOperation');
		
		$page					=	validatePostVar($this->postVar, 'page', true);
		$startDate				=	validatePostVar($this->postVar, 'startDate', true);
		$endDate				=	validatePostVar($this->postVar, 'endDate', true);
		$startDate				=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate				=	$startDate->format('Y-m-d');
		$endDate				=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate				=	$endDate->format('Y-m-d');
		$dataTable				=	$this->ModelRecapPerDriver->getDataFeePerPeriod($page, 25, $startDate, $endDate);
		$urlExcelFeePerPeriod	=	BASE_URL."financeDriver/recapPerDriver/excelDataFeePerPeriod/".base64_encode(encodeStringKeyFunction($startDate."|".$endDate, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelFeePerPeriod"=>$urlExcelFeePerPeriod));
	
	}
	
	public function excelDataFeePerPeriod($encryptedVar){

		$this->load->model('FinanceDriver/ModelRecapPerDriver');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	explode("|", $decryptedVar);
		$startDate		=	$expDecryptedVar[0];
		$endDate		=	$expDecryptedVar[1];

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
		$dateRangeStr	=	$startDateStr." to ".$endDateStr;
		$dataTable		=	$this->ModelRecapPerDriver->getDataFeePerPeriod(1, 999999, $startDate, $endDate);
		
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
		$sheet->setCellValue('A2', 'Driver Fee per Period');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:D1');
		$sheet->mergeCells('A2:D2');
		
		$sheet->setCellValue('A4', 'Date Period : '.$dateRangeStr);
		$sheet->setCellValue('A6', 'Driver Type');
		$sheet->setCellValue('B6', 'Driver Name');
		$sheet->setCellValue('C6', 'Total Schedule');
		$sheet->setCellValue('D6', 'Total Fee');
		$sheet->getStyle('A6:D6')->getFont()->setBold( true );
		$sheet->getStyle('A6:D6')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A6:D6')->getAlignment()->setVertical('center');
		$rowNumber	=	7;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$grandTotalSchedule = $grandTotalFee =	0;
		foreach($dataTable['data'] as $data){
			
			$grandTotalSchedule			+=	$data->TOTALSCHEDULE;
			$grandTotalFee				+=	$data->TOTALFEE;
			
			$sheet->setCellValue('A'.$rowNumber, $data->DRIVERTYPE);
			$sheet->setCellValue('B'.$rowNumber, $data->DRIVERNAME);
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
		
		$sheet->getStyle('A6:D'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('D'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(18);
		$sheet->getColumnDimension('B')->setWidth(24);
		$sheet->getColumnDimension('C')->setWidth(12);
		$sheet->getColumnDimension('D')->setWidth(12);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDataFeePerPeriod_'.$startDateStr.'_'.$endDateStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}
	
	public function getDataPerDriverRecap(){

		$this->load->model('FinanceDriver/ModelRecapPerDriver');
		
		$idDriver				=	validatePostVar($this->postVar, 'idDriver', true);
		$detailDriver			=	$this->ModelRecapPerDriver->getDetailDriver($idDriver);
		
		if(!$detailDriver){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Driver details not found!"));
		}
		
		$dataBankAccount				=	$this->ModelRecapPerDriver->getDataActiveBankAccountDriver($idDriver);
		$dataRecapPerDriver				=	$this->ModelRecapPerDriver->getDataRecapPerDriver($idDriver);
		$dataListFee					=	$this->ModelRecapPerDriver->getDataListFee($idDriver);
		$dataListAdditionalCost			=	$this->ModelRecapPerDriver->getDataListAdditionalCost($idDriver);
		$dataListReimbursement			=	$this->ModelRecapPerDriver->getDataListReimbursement($idDriver);
		$dataListReviewBonusPunishment	=	$this->ModelRecapPerDriver->getDataListReviewBonusPunishment($idDriver);
		$dataListCollectPayment			=	$this->ModelRecapPerDriver->getDataListCollectPayment($idDriver);
		$dataLoanHistory				=	$this->ModelRecapPerDriver->getDataLoanPrepaidCapitalHistory($idDriver, 1);
		$dataPrepaidCapitalHistory		=	$this->ModelRecapPerDriver->getDataLoanPrepaidCapitalHistory($idDriver, 2);
		$initialName					=	preg_match_all('/\b\w/', $detailDriver['NAME'], $matches);
		$initialName					=	implode('', $matches[0]);
		$detailDriver['INITIALNAME']	=	strtoupper($initialName);

		if($dataPrepaidCapitalHistory){
			$dataPrepaidCapitalHistory	=	array_reverse($dataPrepaidCapitalHistory);
			$currentBalance				=	0;
			foreach($dataPrepaidCapitalHistory as $keyHistory){
				$transactionType	=	$keyHistory->TYPE;
				$amount				=	$keyHistory->AMOUNT;
				$currentBalance		=	$transactionType == "K" ? $currentBalance - $amount : $currentBalance + $amount;
				$keyHistory->SALDO	=	$currentBalance;
			}
		}

		if($dataLoanHistory){
			$dataLoanHistory	=	array_reverse($dataLoanHistory);
			$currentBalance				=	0;
			foreach($dataLoanHistory as $keyHistory){
				$transactionType	=	$keyHistory->TYPE;
				$amount				=	$keyHistory->AMOUNT;
				$currentBalance		=	$transactionType == "K" ? $currentBalance - $amount : $currentBalance + $amount;
				$keyHistory->SALDO	=	$currentBalance;
			}
		}

		setResponseOk(
			array(
				"token"							=>	$this->newToken,
				"detailDriver"					=>	$detailDriver,
				"dataBankAccount"				=>	$dataBankAccount,
				"dataRecapPerDriver"			=>	$dataRecapPerDriver,
				"dataListFee"					=>	$dataListFee,
				"dataListAdditionalCost"		=>	$dataListAdditionalCost,
				"dataListReimbursement"			=>	$dataListReimbursement,
				"dataListReviewBonusPunishment"	=>	$dataListReviewBonusPunishment,
				"dataListCollectPayment"		=>	$dataListCollectPayment,
				"dataLoanHistory"				=>	$dataLoanHistory,
				"dataPrepaidCapitalHistory"		=>	$dataPrepaidCapitalHistory
			)
		);
	
	}
	
	public function submitManualWithdrawal(){

		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelRecapPerDriver');
		
		$idDriver					=	validatePostVar($this->postVar, 'idDriver', true);
		$additionalIncome			=	validatePostVar($this->postVar, 'additionalIncome', false);
		$loanCarInstallment			=	validatePostVar($this->postVar, 'loanCar', false);
		$loanPersonalInstallment	=	validatePostVar($this->postVar, 'loanPersonal', false);
		$charityNominal				=	validatePostVar($this->postVar, 'charity', true);
		$withdrawalNotes			=	validatePostVar($this->postVar, 'withdrawalNotes', true);
		$userAdminName				=	validatePostVar($this->postVar, 'NAME', true);
		$dataBankAccount			=	$this->ModelRecapPerDriver->getDataActiveBankAccountDriver($idDriver);
		$dataRecapPerDriver			=	$this->ModelRecapPerDriver->getDataRecapPerDriver($idDriver);
		$totalFee					=	$dataRecapPerDriver['TOTALFEE'];
		$totalAdditionalCost		=	$dataRecapPerDriver['TOTALADDITIONALCOST'];
		$totalReimbursement			=	$dataRecapPerDriver['TOTALREIMBURSEMENT'];
		$totalReviewBonusPunishment	=	$dataRecapPerDriver['TOTALREVIEWBONUSPUNISHMENT'];
		$totalCollectPayment		=	$dataRecapPerDriver['TOTALCOLLECTPAYMENT'];
		$totalPrepaidCapital		=	$dataRecapPerDriver['TOTALPREPAIDCAPITAL'];
		$totalLoanCar				=	$dataRecapPerDriver['TOTALLOANCAR'];
		$totalLoanPersonal			=	$dataRecapPerDriver['TOTALLOANPERSONAL'];
		$totalWithdrawal			=	$totalFee + $totalAdditionalCost + $totalReimbursement + $totalReviewBonusPunishment - $totalCollectPayment - $additionalIncome - $totalPrepaidCapital - $loanCarInstallment - $loanPersonalInstallment - $charityNominal;

		if($totalWithdrawal < 0){
			setResponseForbidden(array("token" => $this->newToken, "msg" => "The withdrawal balance is less than or equal to zero"));
		}
		
		if($loanCarInstallment > $totalLoanCar){
			setResponseForbidden(array("token" => $this->newToken, "msg" => "Car loan installment payments should not be more than IDR ".number_format($totalLoanCar, 0, '.', ',')));
		}
		
		if($loanPersonalInstallment > $totalLoanPersonal){
			setResponseForbidden(array("token" => $this->newToken, "msg" => "Personal loan installment payments should not be more than IDR ".number_format($totalLoanPersonal, 0, '.', ',')));
		}
		
		if($charityNominal < 1000){
			setResponseForbidden(array("token" => $this->newToken, "msg" => "Please enter the nominal charity"));
		}
		
		if($dataBankAccount['ACCOUNTNUMBER'] == '-'){
			setResponseForbidden(array("token" => $this->newToken, "msg" => "withdrawal request cannot be processed. Please ask driver to set bank account data first"));
		}
		
		$idBankPartner				=	$dataBankAccount['IDBANK'];
		$accountNumberPartner		=	$dataBankAccount['ACCOUNTNUMBER'];
		$accountHolderNamePartner	=	$dataBankAccount['ACCOUNTHOLDERNAME'];
		$arrInsertWithdrawal		=	array(
											"IDDRIVER"						=>	$idDriver,
											"IDBANK"						=>	$idBankPartner,
											"TOTALFEE"						=>	$totalFee,
											"TOTALADDITIONALCOST"			=>	$totalAdditionalCost,
											"TOTALADDITIONALINCOME"			=>	$additionalIncome,
											"TOTALREIMBURSEMENT"			=>	$totalReimbursement,
											"TOTALREVIEWBONUSPUNISHMENT"	=>	$totalReviewBonusPunishment,
											"TOTALCOLLECTPAYMENT"			=>	$totalCollectPayment,
											"TOTALPREPAIDCAPITAL"			=>	$totalPrepaidCapital,
											"TOTALLOANCARINSTALLMENT"		=>	$loanCarInstallment,
											"TOTALLOANPERSONALINSTALLMENT"	=>	$loanPersonalInstallment,
											"TOTALCHARITY"					=>	$charityNominal,
											"TOTALWITHDRAWAL"				=>	$totalWithdrawal,
											"MESSAGE"						=>	"Manual withdrawal (Input by ".$userAdminName."). ".$withdrawalNotes,
											"ACCOUNTNUMBER"					=>	$accountNumberPartner,
											"ACCOUNTHOLDERNAME"				=>	$accountHolderNamePartner,
											"DATELASTPERIOD"				=>	date('Y-m-d'),
											"DATETIMEREQUEST"				=>	date('Y-m-d H:i:s')
										);
		$procInsertWithdrawal		=	$this->MainOperation->addData('t_withdrawalrecap', $arrInsertWithdrawal);
		
		if(!$procInsertWithdrawal['status']) switchMySQLErrorCode($procInsertWithdrawal['errCode'], $this->newToken);
		
		$idWithdrawalRecap					=	$procInsertWithdrawal['insertID'];
		$dataFeeWithdrawal					=	$this->ModelRecapPerDriver->getDataListFee($idDriver);
		$dataAdditionalCostWithdrawal		=	$this->ModelRecapPerDriver->getDataListAdditionalCost($idDriver);
		$dataReimbursementWithdrawal		=	$this->ModelRecapPerDriver->getDataListReimbursement($idDriver);
		$dataReviewBonusPunishmentWithdrawal=	$this->ModelRecapPerDriver->getDataListReviewBonusPunishment($idDriver);
		$dataCollectPaymentWithdrawal		=	$this->ModelRecapPerDriver->getDataListCollectPayment($idDriver);
		$dataPrepaidCapital					=	$this->ModelRecapPerDriver->getDataLoanPrepaidCapitalHistory($idDriver, 2);
		
		//fee
		if($dataFeeWithdrawal){
			foreach($dataFeeWithdrawal as $keyFeeWithdrawal){
				$idFee	=	$keyFeeWithdrawal->IDFEE;
				$this->MainOperation->updateData("t_fee", array("IDWITHDRAWALRECAP" => $idWithdrawalRecap), "IDFEE", $idFee);
			}
		}
		
		//additional cost
		if($dataAdditionalCostWithdrawal){
			foreach($dataAdditionalCostWithdrawal as $keyAdditionalCostWithdrawal){
				$idAdditionalCost	=	$keyAdditionalCostWithdrawal->IDRESERVATIONADDITIONALCOST;
				$this->MainOperation->updateData("t_reservationadditionalcost", array("IDWITHDRAWALRECAP" => $idWithdrawalRecap), "IDRESERVATIONADDITIONALCOST", $idAdditionalCost);
			}
		}
		
		//reimbursement
		if($dataReimbursementWithdrawal){
			foreach($dataReimbursementWithdrawal as $keyReimbursementWithdrawal){
				$idReimbursement	=	$keyReimbursementWithdrawal->IDREIMBURSEMENT;
				$this->MainOperation->updateData("t_reimbursement", array("IDWITHDRAWALRECAP" => $idWithdrawalRecap), "IDREIMBURSEMENT", $idReimbursement);
			}
		}
		
		//review bonus punishment
		if($dataReviewBonusPunishmentWithdrawal){
			foreach($dataReviewBonusPunishmentWithdrawal as $keyReviewBonusPunishmentWithdrawal){
				$idReviewBonusPunishment	=	$keyReviewBonusPunishmentWithdrawal->IDDRIVERREVIEWBONUS;
				$this->MainOperation->updateData("t_driverreviewbonus", array("IDWITHDRAWALRECAP" => $idWithdrawalRecap), "IDDRIVERREVIEWBONUS", $idReviewBonusPunishment);
			}
		}
		
		//collect payment
		if($dataCollectPaymentWithdrawal){
			foreach($dataCollectPaymentWithdrawal as $keyCollectPaymentWithdrawal){
				$idCollectPayment	=	$keyCollectPaymentWithdrawal->IDCOLLECTPAYMENT;
				$this->MainOperation->updateData("t_collectpayment", array("IDWITHDRAWALRECAP" => $idWithdrawalRecap), "IDCOLLECTPAYMENT", $idCollectPayment);
			}
		}
		
		//prepaid capital
		if($dataPrepaidCapital){
			foreach($dataPrepaidCapital as $keyPrepaidCapital){
				if($keyPrepaidCapital->IDWITHDRAWALRECAP == 0 && $keyPrepaidCapital->TYPE == 'D' && $keyPrepaidCapital->IDLOANTYPE == 3){
					$this->MainOperation->updateData("t_loandriverrecord", array("IDWITHDRAWALRECAP" => $idWithdrawalRecap), "IDLOANDRIVERRECORD", $keyPrepaidCapital->IDLOANDRIVERRECORD);
				}
			}
		}			
		
		if(PRODUCTION_URL) $this->calculateWithdrawalRequest();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"This manual withdrawal request has been created"));
	
	}
	
	public function getDataWithdrawalRequest(){

		$this->load->model('FinanceDriver/ModelRecapPerDriver');
		
		$idDriver				=	validatePostVar($this->postVar, 'idDriver', false);
		$statusWithdrawal		=	validatePostVar($this->postVar, 'statusWithdrawal', false);
		$viewRequestOnly		=	validatePostVar($this->postVar, 'viewRequestOnly', false);
		$startDate				=	validatePostVar($this->postVar, 'startDate', true);
		$startDate				=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate				=	$startDate->format('Y-m-d');
		$endDate				=	validatePostVar($this->postVar, 'endDate', true);
		$endDate				=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate				=	$endDate->format('Y-m-d');
		$dataWithdrawalRequest	=	$this->ModelRecapPerDriver->getDataWithdrawalRequest($startDate, $endDate, $idDriver, $statusWithdrawal, $viewRequestOnly);
		
		if(!$dataWithdrawalRequest){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data withdrawal request found!"));
		}

		setResponseOk(
			array(
				"token"					=>	$this->newToken,
				"dataWithdrawalRequest"	=>	$dataWithdrawalRequest
			)
		);
	
	}
	
	public function getDetailWithdrawalRequest(){

		$this->load->model('FinanceDriver/ModelRecapPerDriver');
		
		$idWithdrawalRecap		=	validatePostVar($this->postVar, 'idWithdrawalRecap', true);
		$detailWithdrawalRequest=	$this->ModelRecapPerDriver->getDetailWithdrawalRequest($idWithdrawalRecap);
		
		if(!$detailWithdrawalRequest){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail withdrawal request not found!"));
		}

		$listDetailWithdrawal	=	$this->ModelRecapPerDriver->getListDetailWithdrawal($idWithdrawalRecap);
		setResponseOk(
			array(
				"token"						=>	$this->newToken,
				"detailWithdrawalRequest"	=>	$detailWithdrawalRequest,
				"listDetailWithdrawal"		=>	$listDetailWithdrawal
			)
		);
	
	}
	
	public function approveRejectWithdrawal(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelRecapPerDriver');
		
		$idWithdrawalRecap		=	validatePostVar($this->postVar, 'idWithdrawalRecap', true);
		$status					=	validatePostVar($this->postVar, 'status', true);
		$strStatus				=	$status == 1 ? "approved" : "rejected";
		$detailWithdrawal		=	$this->ModelRecapPerDriver->getDetailWithdrawalRequest($idWithdrawalRecap);
		$dataUserAdmin			=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName			=	$dataUserAdmin['NAME']." (Admin)";
		$arrUpdateWithdrawal	=	array(
										"DATETIMEAPPROVAL"	=>	date('Y-m-d H:i:s'),
										"STATUSWITHDRAWAL"	=>	$status,
										"USERAPPROVAL"		=>	$userAdminName
									);
		$procUpdateWithdrawal	=	$this->MainOperation->updateData("t_withdrawalrecap", $arrUpdateWithdrawal, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
		
		if(!$procUpdateWithdrawal['status']){
			switchMySQLErrorCode($procUpdateWithdrawal['errCode'], $this->newToken);
		}
		
		$dataFeeWithdrawal					=	$this->ModelRecapPerDriver->getDataFeeWithdrawal($idWithdrawalRecap);
		$dataAdditionalCostWithdrawal		=	$this->ModelRecapPerDriver->getDataAdditionalCostWithdrawal($idWithdrawalRecap);
		$dataReimbursementWithdrawal		=	$this->ModelRecapPerDriver->getDataReimbursementWithdrawal($idWithdrawalRecap);
		$dataReviewBonusPunishmentWithdrawal=	$this->ModelRecapPerDriver->getDataReviewBonusPunishmentWithdrawal($idWithdrawalRecap);
		$dataCollectPaymentWithdrawal		=	$this->ModelRecapPerDriver->getDataCollectPaymentWithdrawal($idWithdrawalRecap);
		$dataPrepaidCapitalWithdrawal		=	$this->ModelRecapPerDriver->getDataPrepaidCapitalWithdrawal($idWithdrawalRecap);
		$idDriver							=	$detailWithdrawal['IDDRIVER'];
		$dateWithdrawalRequest				=	$detailWithdrawal['DATETIMEREQUEST'];
		$messageWithdrawalRequest			=	$detailWithdrawal['MESSAGE'];
		$totalLoanCarInstallment			=	$detailWithdrawal['TOTALLOANCARINSTALLMENT'];
		$totalLoanPersonalInstallment		=	$detailWithdrawal['TOTALLOANPERSONALINSTALLMENT'];
		$totalAmountWithdrawal				=	number_format($detailWithdrawal['TOTALWITHDRAWAL'], 0, '.', ',');
		$totalAmountWithdrawalDB			=	$detailWithdrawal['TOTALWITHDRAWAL'];
		
		if($status == -1){

			if($dataFeeWithdrawal){
				$arrUpdateFee	=	array(
					"IDWITHDRAWALRECAP"	=>	0,
					"WITHDRAWSTATUS"	=>	0
				);
				$this->MainOperation->updateData("t_fee", $arrUpdateFee, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
			}
			
			if($dataAdditionalCostWithdrawal){
				$arrUpdateAdditionalCost	=	array(
					"IDWITHDRAWALRECAP"	=>	0
				);
				$this->MainOperation->updateData("t_reservationadditionalcost", $arrUpdateAdditionalCost, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
			}
			
			if($dataReimbursementWithdrawal){
				$arrUpdateReimbursement	=	array(
					"IDWITHDRAWALRECAP"	=>	0
				);
				$this->MainOperation->updateData("t_reimbursement", $arrUpdateReimbursement, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
			}
			
			if($dataReviewBonusPunishmentWithdrawal){
				$arrUpdateReviewBonusPunishmentWithdrawal	=	array(
					"IDWITHDRAWALRECAP"	=>	0
				);
				$this->MainOperation->updateData("t_driverreviewbonus", $arrUpdateReviewBonusPunishmentWithdrawal, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
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

			$this->MainOperation->deleteData('t_loandriverrecord', ['IDWITHDRAWALRECAP' => $idWithdrawalRecap, 'IDDRIVER' => $idDriver, 'IDLOANDRIVERINSTALLMENTREQUEST' => 0, 'TYPE' => 'K']);
			if($dataPrepaidCapitalWithdrawal){
				$arrUpdatePrepaidCapital	=	array("IDWITHDRAWALRECAP"	=>	0);
				$this->MainOperation->updateData("t_loandriverrecord", $arrUpdatePrepaidCapital, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
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
			
			if($dataAdditionalCostWithdrawal){
				$arrUpdateAdditionalCost=	array(
												"USERAPPROVAL"		=>	$userAdminName,
												"DATETIMEAPPROVAL"	=>	date('Y-m-d H:i:s'),
												"STATUSAPPROVAL"	=>	1
											);
				$this->MainOperation->updateData('t_reservationadditionalcost', $arrUpdateAdditionalCost, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
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
		
			if($dataPrepaidCapitalWithdrawal){
				foreach($dataPrepaidCapitalWithdrawal as $keyPrepaidCapitalWithdrawal){
					$idLoanDriverRecord			=	$keyPrepaidCapitalWithdrawal->IDLOANDRIVERRECORD;
					$prepaidCapitalAmount		=	$keyPrepaidCapitalWithdrawal->AMOUNT;
					$arrInsertLoanDriverRecord	=	array(
														"IDDRIVER"			=>	$idDriver,
														"IDLOANTYPE"		=>	3,
														"IDWITHDRAWALRECAP"	=>	$idWithdrawalRecap,
														"TYPE"				=>	'K',
														"DESCRIPTION"		=>	'Prepaid capital installment (paid within withdrawal)',
														"AMOUNT"			=>	$prepaidCapitalAmount,
														"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
														"USERINPUT"			=>	$userAdminName
													);
					$this->MainOperation->addData("t_loandriverrecord", $arrInsertLoanDriverRecord);
				}
			}
			
			if($totalLoanCarInstallment > 0){
				$arrInsertLoanDriverRecord	=	array(
													"IDDRIVER"			=>	$idDriver,
													"IDLOANTYPE"		=>	1,
													"IDWITHDRAWALRECAP"	=>	$idWithdrawalRecap,
													"TYPE"				=>	'K',
													"DESCRIPTION"		=>	'Loan - car installment (paid within withdrawal)',
													"AMOUNT"			=>	$totalLoanCarInstallment,
													"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
													"USERINPUT"			=>	$userAdminName
												);
				$this->MainOperation->addData("t_loandriverrecord", $arrInsertLoanDriverRecord);				
			}
			
			if($totalLoanPersonalInstallment > 0){
				$arrInsertLoanDriverRecord	=	array(
													"IDDRIVER"			=>	$idDriver,
													"IDLOANTYPE"		=>	2,
													"IDWITHDRAWALRECAP"	=>	$idWithdrawalRecap,
													"TYPE"				=>	'K',
													"DESCRIPTION"		=>	'Loan - personal installment (paid within withdrawal)',
													"AMOUNT"			=>	$totalLoanPersonalInstallment,
													"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
													"USERINPUT"			=>	$userAdminName
												);
				$this->MainOperation->addData("t_loandriverrecord", $arrInsertLoanDriverRecord);				
			}
			
			if($totalAmountWithdrawalDB > 0){
				$idBankTransfer			=	$detailWithdrawal['IDBANK'];
				$bankAccountNumber		=	$detailWithdrawal['ACCOUNTNUMBER'];
				$bankAccountHolderName	=	$detailWithdrawal['ACCOUNTHOLDERNAME'];
				$driverEmail			=	$detailWithdrawal['DRIVEREMAIL'];
				$transactioCode			=	"WD".date("dmyHi").str_pad($idDriver, 4, "0", STR_PAD_LEFT);
				$partnerCode			=	$this->MainOperation->getPartnerCode(2, $idDriver);
				$transferRemark			=	strtoupper("WITHDRAW ".date("d M y"));
				$emailList				=	MAILBOX_USERNAME.",".$driverEmail;
				$arrInsertTransferList	=	array(
												"IDPARTNERTYPE"		=>	2,
												"IDPARTNER"			=>	$idDriver,
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

		$dataPartner		=	$this->MainOperation->getDataDriver($idDriver);
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
										"IDPARTNERTYPE"			=>	2,
										"IDPARTNER"				=>	$idDriver,
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
						$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/driver/".$RTDB_refCode."/activeWithdrawal");
						$referencePartnerVal=	$referencePartner->getValue();
						if($referencePartnerVal != null || !is_null($referencePartnerVal)){
							$referencePartner->update([
								'newWithdrawalNotif'		=>  false,
								'newWithdrawalNotifDetail'	=>  nl2br($body),
								'newWithdrawalNotifStatus'	=>  $status,
								'timestampUpdate'			=>  gmdate('YmdHis'),
								'totalActiveWithdrawal'		=>  $this->MainOperation->getTotalActiveWithdrawalPartner(2, $idDriver)
							]);
						}
					} catch (Exception $e) {
					}
				}
			}
		}
		
		if(PRODUCTION_URL) $this->calculateWithdrawalRequest();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"This withdrawal request has been ".$strStatus));
	
	}
	
	public function calculateWithdrawalRequest(){
		$this->load->model('FinanceDriver/ModelRecapPerDriver');
		$totalWithdrawalRequest	=	$this->ModelRecapPerDriver->getTotalWithdrawalRequest(2);

		try {
			$factory	=	(new Factory)
							->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
							->withDatabaseUri(FIREBASE_RTDB_URI);
			$database	=	$factory->createDatabase();
			$reference	=	$database->getReference(FIREBASE_RTDB_MAINREF_NAME."unprocessedFinanceDriver/withdrawalRequest")
							->set([
								'newWithdrawalRequestStatus'	=>	false,
								'newWithdrawalRequestTotal'		=>	$totalWithdrawalRequest,
								'timestampUpdate'				=>	gmdate("YmdHis")
							]);
		} catch (Exception $e) {
		}
		return true;
	}
	
	public function cancelWithdrawal(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelRecapPerDriver');
		
		$idWithdrawalRecap		=	validatePostVar($this->postVar, 'idWithdrawalRecap', true);
		$detailWithdrawal		=	$this->ModelRecapPerDriver->getDetailWithdrawalRequest($idWithdrawalRecap);
		$dataUserAdmin			=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName			=	$dataUserAdmin['NAME']." (Admin)";
		$arrUpdateWithdrawal	=	array(
										"DATETIMEAPPROVAL"	=>	date('Y-m-d H:i:s'),
										"STATUSWITHDRAWAL"	=>	-2,
										"USERAPPROVAL"		=>	$userAdminName
									);
		$procUpdateWithdrawal	=	$this->MainOperation->updateData("t_withdrawalrecap", $arrUpdateWithdrawal, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
		if(!$procUpdateWithdrawal['status']) switchMySQLErrorCode($procUpdateWithdrawal['errCode'], $this->newToken);
		$procUpdateTransferList	=	$this->MainOperation->updateData("t_transferlist", ['STATUS' => -1], "IDWITHDRAWAL", $idWithdrawalRecap);
		
		$dataFeeWithdrawal					=	$this->ModelRecapPerDriver->getDataFeeWithdrawal($idWithdrawalRecap);
		$dataAdditionalCostWithdrawal		=	$this->ModelRecapPerDriver->getDataAdditionalCostWithdrawal($idWithdrawalRecap);
		$dataReimbursementWithdrawal		=	$this->ModelRecapPerDriver->getDataReimbursementWithdrawal($idWithdrawalRecap);
		$dataReviewBonusPunishmentWithdrawal=	$this->ModelRecapPerDriver->getDataReviewBonusPunishmentWithdrawal($idWithdrawalRecap);
		$dataCollectPaymentWithdrawal		=	$this->ModelRecapPerDriver->getDataCollectPaymentWithdrawal($idWithdrawalRecap);
		$dataPrepaidCapitalWithdrawal		=	$this->ModelRecapPerDriver->getDataPrepaidCapitalWithdrawal($idWithdrawalRecap);
		$idDriver							=	$detailWithdrawal['IDDRIVER'];
		$dateWithdrawalRequest				=	$detailWithdrawal['DATETIMEREQUEST'];
		$messageWithdrawalRequest			=	$detailWithdrawal['MESSAGE'];
		$totalLoanCarInstallment			=	$detailWithdrawal['TOTALLOANCARINSTALLMENT'];
		$totalLoanPersonalInstallment		=	$detailWithdrawal['TOTALLOANPERSONALINSTALLMENT'];
		$totalAmountWithdrawal				=	number_format($detailWithdrawal['TOTALWITHDRAWAL'], 0, '.', ',');
		$totalAmountWithdrawalDB			=	$detailWithdrawal['TOTALWITHDRAWAL'];
		
		if($dataFeeWithdrawal){
			$arrUpdateFee	=	array(
				"IDWITHDRAWALRECAP"	=>	0,
				"WITHDRAWSTATUS"	=>	0,
				"USERAPPROVAL"		=>	'',
				"DATETIMEAPPROVAL"	=>	'0000-00-00 00:00:00'
			);
			$this->MainOperation->updateData("t_fee", $arrUpdateFee, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
		}

		if($dataAdditionalCostWithdrawal){
			$arrUpdateAdditionalCost	=	array(
				"IDWITHDRAWALRECAP"	=>	0,
				"USERAPPROVAL"		=>	'',
				"DATETIMEAPPROVAL"	=>	'0000-00-00 00:00:00',
				"STATUSAPPROVAL"	=>	0
			);
			$this->MainOperation->updateData("t_reservationadditionalcost", $arrUpdateAdditionalCost, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
		}
		
		if($dataReimbursementWithdrawal){
			$arrUpdateReimbursement	=	array(
				"IDWITHDRAWALRECAP"	=>	0
			);
			$this->MainOperation->updateData("t_reimbursement", $arrUpdateReimbursement, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
		}
		
		if($dataReviewBonusPunishmentWithdrawal){
			$arrUpdateReviewBonusPunishmentWithdrawal	=	array(
				"IDWITHDRAWALRECAP"	=>	0
			);
			$this->MainOperation->updateData("t_driverreviewbonus", $arrUpdateReviewBonusPunishmentWithdrawal, "IDWITHDRAWALRECAP", $idWithdrawalRecap);
		}

		if($dataCollectPaymentWithdrawal){
			$arrUpdateCollectPayment	=	array(
				"IDWITHDRAWALRECAP"			=>	0,
				"STATUSSETTLEMENTREQUEST"	=>	0,
				"DATETIMESTATUS"			=>	date('Y-m-d H:i:s'),
				"LASTUSERINPUT"				=>	$userAdminName
			);
			$this->MainOperation->updateData("t_collectpayment", $arrUpdateCollectPayment, "IDWITHDRAWALRECAP", $idWithdrawalRecap);

			foreach($dataCollectPaymentWithdrawal as $keyCollectPaymentWithdrawal){
				$idCollectPayment			=	$keyCollectPaymentWithdrawal->IDCOLLECTPAYMENT;
				$idReservationPayment		=	$keyCollectPaymentWithdrawal->IDRESERVATIONPAYMENT;
				$arrInsertCollectHistory	=	array(
													"IDCOLLECTPAYMENT"	=>	$idCollectPayment,
													"DESCRIPTION"		=>	"Settlement within withdrawal has been cancelled",
													"USERINPUT"			=>	$userAdminName,
													"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
													"STATUS"			=>	-1
												);
				$this->MainOperation->addData("t_collectpaymenthistory", $arrInsertCollectHistory);
				
				$arrUpdatePayment	=	array(
											"STATUS"		=>	0,
											"DATETIMEUPDATE"=>	date('Y-m-d H:i:s'),
											"USERUPDATE"	=>	$userAdminName,
											"EDITABLE"		=>	1,
											"DELETABLE"		=>	1
										);
				$this->MainOperation->updateData("t_reservationpayment", $arrUpdatePayment, 'IDRESERVATIONPAYMENT', $idReservationPayment);
			}
		}
		
		$this->MainOperation->deleteData('t_loandriverrecord', ['IDWITHDRAWALRECAP' => $idWithdrawalRecap, 'IDDRIVER' => $idDriver, 'IDLOANDRIVERINSTALLMENTREQUEST' => 0, 'TYPE' => 'K']);
		if($dataPrepaidCapitalWithdrawal){
			$arrUpdatePrepaidCapital	=	array("IDWITHDRAWALRECAP"	=>	0);
			$this->MainOperation->updateData("t_loandriverrecord", $arrUpdatePrepaidCapital, ["IDWITHDRAWALRECAP" => $idWithdrawalRecap, 'TYPE' => 'D']);
		}
		
		$dataPartner		=	$this->MainOperation->getDataDriver($idDriver);
		$dataMessageType	=	$this->MainOperation->getDataMessageType(8);
		$partnerTokenFCM	=	$dataPartner['TOKENFCM'];
		$activityMessage	=	$dataMessageType['ACTIVITY'];

		$titleDB			=	"Your withdrawal request has been cancelled";
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
										"IDPARTNERTYPE"			=>	2,
										"IDPARTNER"				=>	$idDriver,
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
						$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/driver/".$RTDB_refCode."/activeWithdrawal");
						$referencePartnerVal=	$referencePartner->getValue();
						
						if($referencePartnerVal != null || !is_null($referencePartnerVal)){
							$referencePartner->update([
								'newWithdrawalNotif'		=>  false,
								'timestampUpdate'			=>  gmdate('YmdHis'),
								'totalActiveWithdrawal'		=>  $this->MainOperation->getTotalActiveWithdrawalPartner(2, $idDriver)
							]);
						}
					} catch (Exception $e) {
					}
				}
			}
		}
		
		if(PRODUCTION_URL) $this->calculateWithdrawalRequest();		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"This withdrawal request has been cancelled"));
	
	}
	
}