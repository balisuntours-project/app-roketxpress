<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class CharityReport extends CI_controller {
	
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
		} else {
			$functionName	=	$this->uri->segment(3);
			if($functionName != "excelDataCharityReport"){
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
	
	public function getDataCharityReport(){
		$this->load->model('Finance/ModelCharityReport');
		
		$page				=	validatePostVar($this->postVar, 'page', true);
		$startDate			=	validatePostVar($this->postVar, 'startDate', true);
		$startDateDT		=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate			=	$startDateDT->format('Y-m-d');
		$startDateStr		=	$startDateDT->format('d M Y');
		$endDate			=	validatePostVar($this->postVar, 'endDate', true);
		$endDateDT			=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate			=	$endDateDT->format('Y-m-d');
		$endDateStr			=	$endDateDT->format('d M Y');
		$searchKeyword		=	validatePostVar($this->postVar, 'searchKeyword', false);
		$viewUnprocessed	=	validatePostVar($this->postVar, 'viewUnprocessed', false);
		$viewUnprocessedNum	=	$viewUnprocessed ? "1" : "0";
		$dataTable			=	$this->ModelCharityReport->getDataCharityReport($page, 25, $startDate, $endDate, $viewUnprocessed, $searchKeyword);
		$disburseCharity	=	true;
		
		if(count($dataTable['data']) <= 0 || !$viewUnprocessed) $disburseCharity	=	false;
		$dataLastTransferCharity=	$this->ModelCharityReport->getDataLastTransferCharity();
		setResponseOk(
			array(
				"token"						=>	$this->newToken,
				"result"					=>	$dataTable,
				"disburseCharity"			=>	$disburseCharity,
				"dataLastTransferCharity"	=>	$dataLastTransferCharity
			)
		);
	}
	
	public function processDisburseCharity(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelCharityReport');

		$idBank				=	validatePostVar($this->postVar, 'idBank', true);
		$accountNumber		=	validatePostVar($this->postVar, 'accountNumber', true);
		$accountHolderName	=	validatePostVar($this->postVar, 'accountHolderName', true);
		$emailNotification	=	validatePostVar($this->postVar, 'emailNotification', true);
		$charityCode		=	validatePostVar($this->postVar, 'charityCode', true);
		$charityCodeNumber	=	preg_replace('/[^0-9]/', '', $charityCode);
		$charityCodeNumber	=	intval($charityCodeNumber) + 1;
		$charityCode		=	"CHR".str_pad($charityCodeNumber, 4, '0', STR_PAD_LEFT);;
		$userAdminName		=	validatePostVar($this->postVar, 'NAME', true);
		$dataTable			=	$this->ModelCharityReport->getDataCharityReport(1, 9999, "", "", true);
		$totalCharity		=	$totalNominal	=	0;
		$dateStart			=	$dateEnd	=	"0000-00-00";
		
		foreach($dataTable['data'] as $index => $data){
			if($index == 0) $dateStart	=	$data->DATECHARITY;
			$dateEnd		=	$data->DATECHARITY;
			$totalNominal	+=	$data->NOMINAL;
			$totalCharity++;
		}
		
		$arrInsertCharityRecap	=	[
			"DATEPERIODSTART"		=>	$dateStart,
			"DATEPERIODEND"			=>	$dateEnd,
			"TOTALCHARITY"			=>	$totalCharity,
			"TOTALCHARITYNOMINAL"	=>	$totalNominal,
			"PROCESSDATE"			=>	date('Y-m-d H:i:s'),
			"PROCESSUSER"			=>	$userAdminName
		];
		$procInsertCharityRecap	=	$this->MainOperation->addData('t_charityrecapprocess', $arrInsertCharityRecap);
		
		if(!$procInsertCharityRecap['status']) {
			setResponseNotModified(array("token"=>$this->newToken, "msg"=>"Failed to process data, please try again later"));
		}
		
		$idCharityRecapDownload	=	$procInsertCharityRecap['insertID'];
		$transactioCode			=	"CHR".date("dmyHi");
		$transferRemark			=	strtoupper("CHARITY ".date("d M y"));
		$emailList				=	MAILBOX_USERNAME.",".$emailNotification;
		$arrInsertTransferList	=	[
			"IDBANK"				=>	$idBank,
			"IDCHARITYRECAPPROCESS"	=>	$idCharityRecapDownload,
			"TRANSACTIONCODE"		=>	$transactioCode,
			"ACCOUNTNUMBER"			=>	$accountNumber,
			"ACCOUNTHOLDERNAME"		=>	$accountHolderName,
			"AMOUNT"				=>	$totalNominal,
			"PARTNERCODE"			=>	$charityCode,
			"REMARK"				=>	$transferRemark,
			"EMAILLIST"				=>	$emailList,
			"STATUSDATETIME"		=>	date("Y-m-d H:i:s"),
			"STATUS"				=>	0
		];
		$this->MainOperation->addData("t_transferlist", $arrInsertTransferList);
		
		foreach($dataTable['data'] as $data){
			$idCharity	=	$data->IDCHARITY;
			$arrUpdateCharity	=	["IDCHARITYRECAPPROCESS" => $idCharityRecapDownload];
			$this->MainOperation->updateData('t_charity', $arrUpdateCharity, 'IDCHARITY', $idCharity);
		}
		setResponseOk(array("token"=>$this->newToken, "msg"=>"The charity disbursement process has been completed"));
	}
	
	public function excelDataCharityReport($encryptedVar){
		$this->load->model('Finance/ModelCharityReport');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$arrDates				=	array();
		$decryptedVar			=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar		=	explode("|", $decryptedVar);
		$startDate				=	$expDecryptedVar[0];
		$endDate				=	$expDecryptedVar[1];
		$startDateStr			=	$expDecryptedVar[2];
		$endDateStr				=	$expDecryptedVar[3];
		$idCharityRecapProcess	=	$expDecryptedVar[4];
		$dataTable				=	$this->ModelCharityReport->getDataCharityReport(1, 9999, $startDate, $endDate, false, '', $idCharityRecapProcess);
		
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
		$sheet->setCellValue('A2', 'Report charity detail');
		$sheet->setCellValue('A3', 'Period : '.$startDateStr.' to '.$endDateStr);
		$sheet->getStyle('A1:A3')->getFont()->setBold( true );
		$sheet->mergeCells('A1:E1');
		$sheet->mergeCells('A2:E2');
		$sheet->mergeCells('A3:E3');

		$sheet->setCellValue('A5', 'No.');
		$sheet->setCellValue('B5', 'Date Time');
		$sheet->setCellValue('C5', 'Name');
		$sheet->setCellValue('D5', 'Description');
		$sheet->setCellValue('E5', 'Nominal');
		$sheet->getStyle('A5:E5')->getFont()->setBold( true );
		$sheet->getStyle('A5')->getAlignment()->setHorizontal('right');
		$sheet->getStyle('E5')->getAlignment()->setHorizontal('right');
		$sheet->getStyle('A5:E5')->getAlignment()->setVertical('center');
		$rowNumber	=	6;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$grandTotalNominal		=	0;
		$number					=	1;
		
		foreach($dataTable['data'] as $data){
			$idCharity	=	$data->IDCHARITY;
			$sheet->setCellValue('A'.$rowNumber, $number);
			$sheet->setCellValue('B'.$rowNumber, $data->DATETIMESTR);
			$sheet->setCellValue('C'.$rowNumber, $data->NAME);
			$sheet->setCellValue('D'.$rowNumber, $data->DESCRIPTION);
			$sheet->setCellValue('E'.$rowNumber, $data->NOMINAL);
			
			$grandTotalNominal	+=	$data->NOMINAL;
			$rowNumber++;
			$number++;
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL'); $sheet->mergeCells('A'.$rowNumber.':'.'D'.$rowNumber);
		$sheet->setCellValue('E'.$rowNumber, $grandTotalNominal);
		$sheet->getStyle('A'.$rowNumber.':'.'E'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->getStyle('A5:E'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('E'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(6);
		$sheet->getColumnDimension('B')->setWidth(16);
		$sheet->getColumnDimension('C')->setWidth(18);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(18);

		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelReportCharity_'.$startDateStr.'_'.$endDateStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}
	
	public function getDetailManualCharity(){
		$this->load->model('Finance/ModelCharityReport');
		
		$idCharity	=	validatePostVar($this->postVar, 'idCharity', true);
		$detailData	=	$this->ModelCharityReport->getDetailManualCharity($idCharity);
		
		if(!$detailData) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found"));
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$detailData));	
	}
	
	public function addDataManualCharity(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelCharityReport');
		
		$charityDate		=	validatePostVar($this->postVar, 'charityDate', true);
		$charityDate		=	DateTime::createFromFormat('d-m-Y', $charityDate);
		$charityDate		=	$charityDate->format('Y-m-d');
		$contributorType	=	validatePostVar($this->postVar, 'optionContributorType', true);
		$contributorName	=	validatePostVar($this->postVar, 'contributorName', true);
		$charityNominal		=	str_replace(",", "", validatePostVar($this->postVar, 'charityNominal', true)) * 1;
		$charityDescription	=	validatePostVar($this->postVar, 'charityDescription', true);
		$userAdminName		=	validatePostVar($this->postVar, 'NAME', false);
		$checkDataExists	=	$this->ModelCharityReport->checkDataExists($charityDate	, $contributorType, $contributorName, $charityNominal);

		if($checkDataExists) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Charity data is already exists. Please change your input data"));
		$arrInsert		=	array(
			"CONTRIBUTORTYPE"	=>	$contributorType,
			"NAME"				=>	$contributorName,
			"DESCRIPTION"		=>	$charityDescription,
			"NOMINAL"			=>	$charityNominal,
			"DATETIME"			=>	$charityDate." ".date('H:i:s'),
			"INPUTTYPE"			=>	2,
			"INPUTBYNAME"		=>	$userAdminName,
			"INPUTDATETIME"		=>	date('Y-m-d H:i:s')
		);

		$insertResult	=	$this->MainOperation->addData("t_charity", $arrInsert);
		if(!$insertResult['status']) switchMySQLErrorCode($insertResult['errCode'], $this->newToken);

		setResponseOk(array("token"=>$this->newToken, "msg"=>"New charity data has been added"));
	}
	
	public function updateDataManualCharity(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelCharityReport');
		
		$idCharity			=	validatePostVar($this->postVar, 'idCharity', true);
		$charityDate		=	validatePostVar($this->postVar, 'charityDate', true);
		$charityDate		=	DateTime::createFromFormat('d-m-Y', $charityDate);
		$charityDate		=	$charityDate->format('Y-m-d');
		$contributorType	=	validatePostVar($this->postVar, 'optionContributorType', true);
		$contributorName	=	validatePostVar($this->postVar, 'contributorName', true);
		$charityNominal		=	str_replace(",", "", validatePostVar($this->postVar, 'charityNominal', true)) * 1;
		$charityDescription	=	validatePostVar($this->postVar, 'charityDescription', true);
		$userAdminName		=	validatePostVar($this->postVar, 'NAME', false);
		$checkDataExists	=	$this->ModelCharityReport->checkDataExists($charityDate	, $contributorType, $contributorName, $charityNominal, $idCharity);

		if($checkDataExists) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Charity data is already exists. Please change your input data"));

		$arrUpdate		=	array(
			"CONTRIBUTORTYPE"	=>	$contributorType,
			"NAME"				=>	$contributorName,
			"DESCRIPTION"		=>	$charityDescription,
			"NOMINAL"			=>	$charityNominal,
			"DATETIME"			=>	$charityDate." ".date('H:i:s'),
			"INPUTTYPE"			=>	2,
			"INPUTBYNAME"		=>	$userAdminName." (Correction)",
			"INPUTDATETIME"		=>	date('Y-m-d H:i:s')
		);

		$updateResult	=	$this->MainOperation->updateData("t_charity", $arrUpdate, "IDCHARITY", $idCharity);
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);

		setResponseOk(array("token"=>$this->newToken, "msg"=>"New charity data has been added"));
	}
	
	private function checkInputData(){
		$arrVarValidate	=	array(
			array("charityDate","text","Charity Date"),
			array("optionContributorType","option","Contributor Type"),
			array("contributorName","text","Contributor Name"),
			array("charityNominal","text","Charity Nominal"),
			array("charityDescription","text","Charity Notes")
		);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;
	}
	
	public function deleteDataManualCharity(){
		$this->load->model('MainOperation');
		
		$idCharity		=	validatePostVar($this->postVar, 'idData', true);
		$userAdminName	=	validatePostVar($this->postVar, 'NAME', false);
		$arrUpdate		=	array(
			"STATUS"		=>	-1,
			"INPUTBYNAME"	=>	$userAdminName." (Delete)",
			"INPUTDATETIME"	=>	date('Y-m-d H:i:s')
		);
		$updateResult	=	$this->MainOperation->updateData("t_charity", $arrUpdate, "IDCHARITY", $idCharity);
		
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Charity data has been deleted"));
	}
	
	public function getDataCharityProcessTransfer(){
		$this->load->model('Finance/ModelCharityReport');
		
		$page		=	validatePostVar($this->postVar, 'page', true);
		$dataTable	=	$this->ModelCharityReport->getDataCharityProcessTransfer($page, 10);
		
		if(count($dataTable['data']) > 0){
			foreach($dataTable['data'] as $data){
				$startDate				=	$data->DATEPERIODSTART;
				$endDate				=	$data->DATEPERIODEND;
				$startDateStr			=	$data->DATEPERIODSTARTSTR;
				$endDateStr				=	$data->DATEPERIODENDSTR;
				$idCharityRecapProcess	=	$data->IDCHARITYRECAPPROCESS;
				$urlExcelCharityReport	=	BASE_URL."finance/charityReport/excelDataCharityReport/".base64_encode(encodeStringKeyFunction($startDate."|".$endDate."|".$startDateStr."|".$endDateStr."|".$idCharityRecapProcess, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
				$data->URLEXCELREPORT	=	$urlExcelCharityReport;
			}
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	}
	
	public function cancelCharityTransferProcess(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelCharityReport');

		$idCharityRecapProcess	=	validatePostVar($this->postVar, 'idData', true);
		$this->MainOperation->deleteData('t_charityrecapprocess', ["IDCHARITYRECAPPROCESS" => $idCharityRecapProcess]);
		$this->MainOperation->updateData('t_charity', ["IDCHARITYRECAPPROCESS" => 0], ["IDCHARITYRECAPPROCESS" => $idCharityRecapProcess]);
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"The charity disbursement transfer has been cancelled"));
	}
	
}