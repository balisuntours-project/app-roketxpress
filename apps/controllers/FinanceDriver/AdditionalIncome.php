<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class AdditionalIncome extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(3);

		if($functionName != "uploadTransferReceipt" && $_SERVER['REQUEST_METHOD'] === 'POST'){
			if($functionName != "apiCalculateRatingPointDriver"){
				$this->postVar	=	decodeJsonPost();
				$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid submission data"));
				$this->newToken	=	isLoggedIn($this->token, true);
			}
		} else {
			$functionName	=	$this->uri->segment(3);
			if($functionName != "excelDetailAdditionalIncome" && $functionName != "apiCalculateRatingPointDriver"){
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
	
	public function getDataAdditionalIncomeRecap(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelAdditionalIncome');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$month			=	validatePostVar($this->postVar, 'month', true);
		$year			=	validatePostVar($this->postVar, 'year', true);
		$searchKeyword	=	validatePostVar($this->postVar, 'searchKeyword', false);
		$dataRecap		=	$this->ModelAdditionalIncome->getDataAdditionalIncomeRecap($page, 25, $month, $year, $searchKeyword);
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataRecap));
	}
	
	public function getDataAdditionalIncomeAndPointRateSetting(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelAdditionalIncome');
		
		$page					=	validatePostVar($this->postVar, 'page', true);
		$startDate				=	validatePostVar($this->postVar, 'startDate', true);
		$endDate				=	validatePostVar($this->postVar, 'endDate', true);
		$startDateDT			=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDateStr			=	$startDateDT->format('d M Y');
		$startDate				=	$startDateDT->format('Y-m-d');
		$endDateDT				=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDateStr				=	$endDateDT->format('d M Y');
		$endDate				=	$endDateDT->format('Y-m-d');
		$searchKeyword			=	validatePostVar($this->postVar, 'searchKeyword', false);
		$viewRequestOnly		=	validatePostVar($this->postVar, 'viewRequestOnly', false);
		$dataPerPage			=	$viewRequestOnly ? 9999 : 25;
		$dataTable				=	$this->ModelAdditionalIncome->getDataAdditionalIncome($page, $dataPerPage, $startDate, $endDate, $searchKeyword, $viewRequestOnly);
		$dataPointRate			=	$this->ModelAdditionalIncome->getDataAdditionalIncomePointRate();
		$urlExcelAdditonalIncome=	"";
		
		if(count($dataTable['data']) > 0) {
			$arrParamExcel			=	[
				"startDate"			=>	$startDate,
				"startDateStr"		=>	$startDateStr,
				"endDate"			=>	$endDate,
				"endDateStr"		=>	$endDateStr,
				"searchKeyword"		=>	$searchKeyword,
				"viewRequestOnly"	=>	$viewRequestOnly,
			];
			$urlExcelAdditonalIncome=	BASE_URL."financeDriver/additionalIncome/excelDetailAdditionalIncome/".base64_encode(encodeStringKeyFunction(json_encode($arrParamExcel), DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "dataPointRate"=>$dataPointRate, "urlExcelAdditonalIncome"=>$urlExcelAdditonalIncome));
	}
	
	public function uploadTransferReceipt($idAdditionalIncome){
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
		
		$dir		=	PATH_STORAGE_ADDITIONAL_INCOME_IMAGE;
		$extension	=	pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		$namaFile	=	"AdditionalIncome"."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$namaFile);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "urlTransferReceipt"=>URL_ADDITIONAL_INCOME_IMAGE.$namaFile, "transferReceiptFileName"=>$namaFile, "defaultHeight"=>"150px"));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}
	}

	public function insertUpdateAdditionalIncome(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelAdditionalIncome');

		$idDriver				=	validatePostVar($this->postVar, 'idDriver', true);
		$idAdditionalIncome		=	validatePostVar($this->postVar, 'idAdditionalIncome', false);
		$date					=	validatePostVar($this->postVar, 'date', true);
		$date					=	DateTime::createFromFormat('d-m-Y', $date);
		$date					=	$date->format('Y-m-d');
		$nominal				=	validatePostVar($this->postVar, 'nominal', true);
		$nominal				=	preg_replace('/\D/', '', $nominal);
		$description			=	validatePostVar($this->postVar, 'description', true);
		$transferReceiptFileName=	validatePostVar($this->postVar, 'transferReceiptFileName', true);
		$userAdminName			=	validatePostVar($this->postVar, 'NAME', false);

		$arrInsertUpdateAdditionalIncome	=	[
			"IDDRIVER"				=>	$idDriver,
			"DESCRIPTION"			=>	$description,
			"IMAGERECEIPT"			=>	$transferReceiptFileName,
			"INCOMENOMINAL"			=>	$nominal,
			"INCOMEDATE"			=>	$date,
			"INPUTTYPE"				=>	1,
			"INPUTUSER"				=>	$userAdminName,
			"INPUTDATETIME"			=>	date('Y-m-d H:i:s'),
			"APPROVALUSER"			=>	$userAdminName,
			"APPROVALDATETIME"		=>	date('Y-m-d H:i:s'),
			"APPROVALSTATUS"		=>	1
		];
		$procInsertUpdateAdditionalIncome	=	$idAdditionalIncome == 0 ? 
												$this->MainOperation->addData('t_additionalincome', $arrInsertUpdateAdditionalIncome) :
												$this->MainOperation->updateData('t_additionalincome', $arrInsertUpdateAdditionalIncome, 'IDADDITIONALINCOME', $idAdditionalIncome);
		
		if(!$procInsertUpdateAdditionalIncome['status'])  switchMySQLErrorCode($procInsertUpdateAdditionalIncome['errCode'], $this->newToken);
		$this->calculateRatingPointDriver($idDriver, $userAdminName);
		$msgResponse	=	$idAdditionalIncome == 0 ? "Data additional income has been added" : "Data additional income has been updated";
		setResponseOk(array("token"=>$this->newToken, "msg"=>$msgResponse));
	}
	
	public function submitApprovalAdditionalIncome(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelAdditionalIncome');
		
		$idAdditionalIncome	=	validatePostVar($this->postVar, 'idAdditionalIncome', true);
		$idDriver			=	validatePostVar($this->postVar, 'idDriver', true);
		$status				=	validatePostVar($this->postVar, 'status', true);
		$userAdminName		=	validatePostVar($this->postVar, 'NAME', true);
		$strStatus			=	$status == "1" ? "Approved" : "Rejected";
		$arrUpdate			=	array(
									"APPROVALUSER"		=>	$userAdminName,
									"APPROVALDATETIME"	=>	date('Y-m-d H:i:s'),
									"APPROVALSTATUS"	=>	$status
								);
		$procUpdate			=	$this->MainOperation->updateData('t_additionalincome', $arrUpdate, 'IDADDITIONALINCOME', $idAdditionalIncome);
		
		if($procUpdate['status']){
			$this->calculateRatingPointDriver($idDriver, $userAdminName);
			if(PRODUCTION_URL) $this->calculateAdditionalIncomeApproval();

			setResponseOk(
				array(
					"token"				=>	$this->newToken,
					"msg"				=>	"Additional income request has been ".$strStatus,
					"approvalUser"		=>	$userAdminName,
					"approvalDateTime"	=>	date('d M Y H:i'),
					"idAdditionalIncome"=>	$idAdditionalIncome,
					"statusApproval"	=>	$status
				)
			);
		} else {
			switchMySQLErrorCode($procUpdate['errCode'], $this->newToken);
		}
	}
	
	public function apiCalculateRatingPointDriver($base64JsonData){
		$jsonData		=	base64_decode($base64JsonData);
		$arrData		=	json_decode($jsonData);
		$idDriver		=	isset($arrData->idDriver) ? $arrData->idDriver : 0;
		$userAdminName	=	isset($arrData->userAdminName) ? $arrData->userAdminName : "";
		
		try {
			$this->calculateRatingPointDriver($idDriver, $userAdminName);
			setResponseOk(array("token"=>$this->newToken, "msg"=>"Done proccess for calculate driver rating point"));
		} catch(Exception $e) {
			setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed. Try again later"));
		}
	}
	
	private function calculateRatingPointDriver($idDriver, $userAdminName){
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelAdditionalIncome');
		$dataAdditionalIncomeDriver	=	$this->ModelAdditionalIncome->getDataAdditionalIncomeByIdDriver($idDriver);
		
		if($dataAdditionalIncomeDriver){
			foreach($dataAdditionalIncomeDriver as $keyAdditionalIncomeDriver){
				$numberOfAdditionalIncome	=	$keyAdditionalIncomeDriver->NUMBEROFADDITIONALINCOME;
				$yearMonth					=	$keyAdditionalIncomeDriver->YEARMONTH;
				$totalIncomeNominal			=	$keyAdditionalIncomeDriver->TOTALINCOMENOMINAL;
				$strArrIdAdditionalIncome	=	$keyAdditionalIncomeDriver->STRARRIDADDITIONALINCOME;
				$lastDateAdditionalIncome	=	$keyAdditionalIncomeDriver->MAXINCOMEDATE;
				$dataPointReview			=	$this->ModelAdditionalIncome->getPointReviewAdditionalIncome($totalIncomeNominal);
				$idAdditionalIncomeRate		=	$dataPointReview['IDADDITIONALINCOMERATE'];
				$pointReview				=	$dataPointReview['REVIEWPOINT'];
				$idDriverRatingPoint		=	$this->ModelAdditionalIncome->getIdDriverRatingPoint($idDriver, $yearMonth);
				$dateRatingPoint			=	date("Y-m-t", strtotime($yearMonth));
				$arrInsertUpdateRatingPoint	=	[
					"IDDRIVER"				=>	$idDriver,
					"IDSOURCE"				=>	20,
					"IDDRIVERREVIEWBONUS"	=>	-1,
					"DATERATINGPOINT"		=>	$dateRatingPoint,
					"RATING"				=>	5,
					"POINT"					=>	$pointReview,
					"INPUTTYPE"				=>	2,
					"USERINPUT"				=>	$userAdminName,
					"DATETIMEINPUT"			=>	date('Y-m-d H:i:s'),
					"STATUSADDITIONALINCOME"=>	1
				];
				
				if($yearMonth != date('Y-m')){
					$procInsertUpdateRatingPoint=	$idDriverRatingPoint == 0 ? 
													$this->MainOperation->addData('t_driverratingpoint', $arrInsertUpdateRatingPoint) :
													$this->MainOperation->updateData('t_driverratingpoint', $arrInsertUpdateRatingPoint, 'IDDRIVERRATINGPOINT', $idDriverRatingPoint);
					if($procInsertUpdateRatingPoint['status'])  {
						$idDriverRatingPoint	=	$idDriverRatingPoint == 0 ? $procInsertUpdateRatingPoint['insertID'] : $idDriverRatingPoint;
						$arrIdAdditionalIncome	=	explode(',', $strArrIdAdditionalIncome);
						$this->MainOperation->updateDataIn('t_additionalincome', ['IDDRIVERRATINGPOINT' => $idDriverRatingPoint], 'IDADDITIONALINCOME', $arrIdAdditionalIncome);
					}
				}
				
				$arrUpdateDataRecap		=	[
					"IDDRIVERRATINGPOINT"	=>	$idDriverRatingPoint,
					"NUMBEROFPAYMENT"		=>	$numberOfAdditionalIncome,
					"NOMINAL"				=>	$totalIncomeNominal,
					"DATELASTPAYMENT"		=>	$lastDateAdditionalIncome
				];
				
				$this->MainOperation->updateData('t_additionalincomerecap', $arrUpdateDataRecap, ['IDDRIVER' => $idDriver, 'PERIOD' => $yearMonth]);
			}
		}
		
		$urlAPISetPointRankDriver	=	BASE_URL."schedule/driverRatingPoint/apiSetPointRankDriver";
		try {
			json_decode(trim(curl_get_file_contents($urlAPISetPointRankDriver)));
		} catch(Exception $e) {
		}
	
		return true;
	}
	
	public function calculateAdditionalIncomeApproval(){
		$this->load->model('FinanceDriver/ModelAdditionalIncome');
		$totalAdditionalIncomeApproval	=	$this->ModelAdditionalIncome->getTotalAdditionalIncomeApproval();
		try {
			$factory	=	(new Factory)
							->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
							->withDatabaseUri(FIREBASE_RTDB_URI);
			$database	=	$factory->createDatabase();
			$reference	=	$database->getReference(FIREBASE_RTDB_MAINREF_NAME."unprocessedFinanceDriver/additionalIncome")
							->set([
								'newAdditionalIncomeStatus'	=>	false,
								'newAdditionalIncomeTotal'	=>	$totalAdditionalIncomeApproval,
								'timestampUpdate'			=>	gmdate("YmdHis")
							]);
		} catch (Exception $e) {
		}
	}

	public function deleteAdditionalIncome(){
		$this->load->model('MainOperation');
		
		$idAdditionalIncome	=	validatePostVar($this->postVar, 'idData', true);
		$idDriver			=	validatePostVar($this->postVar, 'idDriver', true);
		$userAdminName		=	validatePostVar($this->postVar, 'NAME', false);
		$this->MainOperation->deleteData('t_additionalincome', ["IDADDITIONALINCOME" => $idAdditionalIncome]);
		$this->calculateRatingPointDriver($idDriver, $userAdminName);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data additional income has been deleted"));
	}
	
	public function insertUpdatePointRate(){
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelAdditionalIncome');

		$idAdditionalIncomeRate	=	validatePostVar($this->postVar, 'idAdditionalIncomeRate', false);
		$nominalMin				=	validatePostVar($this->postVar, 'nominalMin', false);
		$nominalMax				=	validatePostVar($this->postVar, 'nominalMax', true);
		$point					=	validatePostVar($this->postVar, 'point', true);

		$arrInsertUpdateAdditionalIncomeRate	=	[
			"NOMINALMIN"	=>	$nominalMin,
			"NOMINALMAX"	=>	$nominalMax,
			"REVIEWPOINT"	=>	$point
		];
		$procInsertUpdateAdditionalIncomeRate	=	$idAdditionalIncomeRate == 0 ? 
													$this->MainOperation->addData('t_additionalincomerate', $arrInsertUpdateAdditionalIncomeRate) :
													$this->MainOperation->updateData('t_additionalincomerate', $arrInsertUpdateAdditionalIncomeRate, 'IDADDITIONALINCOMERATE', $idAdditionalIncomeRate);
		
		if(!$procInsertUpdateAdditionalIncomeRate['status']) switchMySQLErrorCode($procInsertUpdateAdditionalIncomeRate['errCode'], $this->newToken);
		
		$msgResponse	=	$idAdditionalIncomeRate == 0 ? "Data Setting Point Rate has been added" : "Data Setting Point Rate has been updated";
		setResponseOk(array("token"=>$this->newToken, "msg"=>$msgResponse));
	}
	
	public function deleteAdditionalIncomeSettingPointRate(){
		$this->load->model('MainOperation');
		
		$idAdditionalIncomeRate	=	validatePostVar($this->postVar, 'idData', true);
		$this->MainOperation->deleteData('t_additionalincomerate', ["IDADDITIONALINCOMERATE" => $idAdditionalIncomeRate]);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data Setting Point Rate has been deleted"));
	}

	public function excelDetailAdditionalIncome($encryptedVar){
		$this->load->model('FinanceDriver/ModelAdditionalIncome');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar		=	json_decode(decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION));
		$startDate			=	$decryptedVar->startDate;
		$startDateStr		=	$decryptedVar->startDateStr;
		$endDate			=	$decryptedVar->endDate;
		$endDateStr			=	$decryptedVar->endDateStr;
		$searchKeyword		=	$decryptedVar->searchKeyword;
		$viewRequestOnly	=	$decryptedVar->viewRequestOnly;
		$viewRequestOnlyStr	=	$viewRequestOnly ? "Yes" : "No";
		$dataTable			=	$this->ModelAdditionalIncome->getDataAdditionalIncome(1, 999999, $startDate, $endDate, $searchKeyword, $viewRequestOnly);
		
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
		$sheet->setCellValue('A2', 'Report Additional Income');
		$sheet->setCellValue('A3', 'Period : '.$startDateStr.' to '.$endDateStr);
		$sheet->getStyle('A1:A3')->getFont()->setBold( true );
		$sheet->mergeCells('A1:H1');
		$sheet->mergeCells('A2:H2');
		$sheet->mergeCells('A3:H3');

		$sheet->setCellValue('A5', 'Search Keyword'); $sheet->mergeCells('A5:B5');
		$sheet->setCellValue('C5', ': '.$searchKeyword); $sheet->mergeCells('C5:H5');
		
		$sheet->setCellValue('A6', 'View Request Only'); $sheet->mergeCells('A6:B6');
		$sheet->setCellValue('C6', ': '.$viewRequestOnlyStr); $sheet->mergeCells('C6:H6');

		$sheet->setCellValue('A8', 'No.');
		$sheet->setCellValue('B8', 'Date');
		$sheet->setCellValue('C8', 'Driver Name');
		$sheet->setCellValue('D8', 'Description');
		$sheet->setCellValue('E8', 'Nominal');
		$sheet->setCellValue('F8', 'Input Detail');
		$sheet->setCellValue('G8', 'Approval Detail');
		$sheet->setCellValue('H8', 'Approval Status');
		$sheet->getStyle('A8:H8')->getFont()->setBold( true );
		
		$sheet->getStyle('E8')->getAlignment()->setHorizontal('right');
		$sheet->getStyle('A8:H8')->getAlignment()->setVertical('center');
		$rowNumber	=	9;
		
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
			$statusApprovalStr	=	'-';
			switch($data->APPROVALSTATUS){
				case "1"	:	$statusApprovalStr	=	'Approved'; break;
				case "-1"	:	$statusApprovalStr	=	'Rejected'; break;
				case "0"	:
				default		:	$statusApprovalStr	=	'Waiting'; break;
			}

			$sheet->setCellValue('A'.$rowNumber, $number);
			$sheet->setCellValue('B'.$rowNumber, $data->INCOMEDATESTR);
			$sheet->setCellValue('C'.$rowNumber, $data->DRIVERNAME);
			$sheet->setCellValue('D'.$rowNumber, $data->DESCRIPTION);
			$sheet->setCellValue('E'.$rowNumber, $data->INCOMENOMINAL);
			$sheet->setCellValue('F'.$rowNumber, $data->INPUTUSER."\n".$data->INPUTDATETIME);
			$sheet->setCellValue('G'.$rowNumber, $data->APPROVALUSER."\n".$data->APPROVALDATETIME);
			$sheet->setCellValue('H'.$rowNumber, $statusApprovalStr);
			
			$grandTotalNominal	+=	$data->INCOMENOMINAL;
			$rowNumber++;
			$number++;
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL'); $sheet->mergeCells('A'.$rowNumber.':'.'D'.$rowNumber);
		$sheet->setCellValue('E'.$rowNumber, $grandTotalNominal);
		$sheet->getStyle('A'.$rowNumber.':'.'E'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('F'.$rowNumber.':'.'H'.$rowNumber);

		$sheet->getStyle('A8:H'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('E'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(6);
		$sheet->getColumnDimension('B')->setWidth(16);
		$sheet->getColumnDimension('C')->setWidth(18);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(18);
		$sheet->getColumnDimension('F')->setWidth(18);
		$sheet->getColumnDimension('G')->setWidth(18);
		$sheet->getColumnDimension('H')->setWidth(16);

		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ReportAdditionalIncome_'.$startDateStr.'_'.$endDateStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
}