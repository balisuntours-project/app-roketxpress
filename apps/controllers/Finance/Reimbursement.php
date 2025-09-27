<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Reimbursement extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(3);

		if($functionName != "uploadReimbursementReceipt" && $functionName != "uploadReimbursementReceipt" && $_SERVER['REQUEST_METHOD'] === 'POST'){
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
	
	public function getDataReimbursement(){
		$this->load->model('Finance/ModelReimbursement');
		
		$page			=	validatePostVar($this->postVar, 'page', true);
		$startDate		=	validatePostVar($this->postVar, 'startDate', true);
		$endDate		=	validatePostVar($this->postVar, 'endDate', true);
		$keywordSearch	=	validatePostVar($this->postVar, 'keywordSearch', false);
		$viewRequestOnly=	validatePostVar($this->postVar, 'viewRequestOnly', false);
		$startDate		=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate		=	$startDate->format('Y-m-d');
		$endDate		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate		=	$endDate->format('Y-m-d');
		$dataTable		=	$this->ModelReimbursement->getDataReimbursement($page, 25, $startDate, $endDate, $keywordSearch, $viewRequestOnly);
		$urlExcelDetail	=	"";
		
		if(!$dataTable) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No Data Found"));
		$dataParams		=	[
			"startDate"			=>	$startDate,
			"endDate"			=>	$endDate,
			"keywordSearch"		=>	$keywordSearch,
			"viewRequestOnly"	=>	$viewRequestOnly
		];
		$urlExcelDetail	=	BASE_URL."finance/reimbursement/excelDetail/".base64_encode(encodeStringKeyFunction(json_encode($dataParams), DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelDetail"=>$urlExcelDetail));
	}
	
	public function getDetailReimbursement(){
		$this->load->model('Finance/ModelReimbursement');
		
		$idReimbursement=	validatePostVar($this->postVar, 'idReimbursement', true);
		$detailData		=	$this->ModelReimbursement->getDetailReimbursement($idReimbursement);
		
		if(!$detailData) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found"));
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$detailData));
	}
	
	public function submitValidateReimbursement(){
		$this->load->model('MainOperation');
		
		$idReimbursement=	validatePostVar($this->postVar, 'idReimbursement', true);
		$status			=	validatePostVar($this->postVar, 'status', true);
		$detailUser		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName	=	$detailUser['NAME'];
		$strStatus		=	$status == "1" ? "Approved" : "Rejected";
		$arrUpdate		=	array(
								"APPROVALBYNAME"	=>	$userAdminName,
								"APPROVALDATETIME"	=>	date('Y-m-d H:i:s'),
								"STATUS"			=>	$status
							);
		$procUpdate		=	$this->MainOperation->updateData('t_reimbursement', $arrUpdate, 'IDREIMBURSEMENT', $idReimbursement);
		
		if($procUpdate['status']){
			if(PRODUCTION_URL) $this->calculateReimbursementRequest();
			setResponseOk(
				array(
					"token"				=>	$this->newToken,
					"msg"				=>	"Reimbursement request has been ".$strStatus,
					"strDateTime"		=>	date('d M Y H:i'),
					"userValidate"		=>	$userAdminName,
					"idReimbursement"	=>	$idReimbursement,
					"statusApproval"	=>	$status
				)
			);
		} else {
			switchMySQLErrorCode($procUpdate['errCode'], $this->newToken);
		}
	}
	
	public function calculateReimbursementRequest(){
		$this->load->model('Finance/ModelReimbursement');
		$totalReimbursementRequest	=	$this->ModelReimbursement->getTotalReimbursementRequest();
		try {
			$factory	=	(new Factory)
							->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
							->withDatabaseUri(FIREBASE_RTDB_URI);
			$database	=	$factory->createDatabase();
			$reference	=	$database->getReference(FIREBASE_RTDB_MAINREF_NAME."unprocessedFinance/reimbursement")
							->set([
								'newReimbursementStatus'	=>	false,
								'newReimbursementTotal'		=>	$totalReimbursementRequest,
								'timestampUpdate'			=>	gmdate("YmdHis")
							]);
		} catch (Exception $e) {
		}
	}
	
	public function uploadReimbursementReceipt(){
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
		
		$dir		=	PATH_REIMBURSEMENT_RECEIPT;
		$extension	=	pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		$namaFile	=	"ReimbursementManual"."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$namaFile);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "urlReimbursementReceipt"=>URL_REIMBURSEMENT_IMAGE.$namaFile, "reimbursementReceiptFileName"=>$namaFile));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}		
	}	
	
	public function insertUpdateReimbursement(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		
		$idReimbursement			=	validatePostVar($this->postVar, 'idReimbursement', false);
		$reimbursementDate			=	validatePostVar($this->postVar, 'reimbursementDate', true);
		$reimbursementDate			=	DateTime::createFromFormat('d-m-Y', $reimbursementDate);
		$reimbursementDate			=	$reimbursementDate->format('Y-m-d');
		$requestByType				=	validatePostVar($this->postVar, 'requestByType', true);
		$idDriverVendor				=	validatePostVar($this->postVar, 'idDriverVendor', true);
		$idVendor					=	$requestByType == 1 ? $idDriverVendor : 0;
		$idDriver					=	$requestByType == 2 ? $idDriverVendor : 0;
		$requesterName				=	validatePostVar($this->postVar, 'requesterName', true);
		$reimbursementNominal		=	str_replace(",", "", validatePostVar($this->postVar, 'reimbursementNominal', true));
		$reimbursementDescription	=	validatePostVar($this->postVar, 'reimbursementDescription', true);
		$reimbursementReceipt		=	validatePostVar($this->postVar, 'reimbursementReceipt', true);
		$userAdminName				=	validatePostVar($this->postVar, 'NAME', true);
		$msgInsertUpdate			=	"";
		
		if($reimbursementNominal <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please input valid nominal"));
		$arrInsertUpdate	=	array(
			"IDVENDOR"			=>	$idVendor,
			"IDDRIVER"			=>	$idDriver,
			"REQUESTBY"			=>	$requestByType,
			"REQUESTBYNAME"		=>	$requesterName,
			"DESCRIPTION"		=>	$reimbursementDescription,
			"NOMINAL"			=>	$reimbursementNominal,
			"RECEIPTDATE"		=>	$reimbursementDate,
			"RECEIPTIMAGE"		=>	$reimbursementReceipt,
			"INPUTMETHOD"		=>	2,
			"INPUTBYNAME"		=>	$userAdminName,
			"INPUTDATETIME"		=>	date('Y-m-d H:i:s'),
			"APPROVALBYNAME"	=>	$userAdminName,
			"APPROVALDATETIME"	=>	date('Y-m-d H:i:s'),
			"STATUS"			=>	1
		);
		
		if(isset($idReimbursement) && $idReimbursement != 0 && $idReimbursement != ""){
			$procInsertUpdate	=	$this->MainOperation->updateData('t_reimbursement', $arrInsertUpdate, 'IDREIMBURSEMENT', $idReimbursement);
			$msgInsertUpdate	=	"Reimbursement data has been updated";
		} else {
			$procInsertUpdate	=	$this->MainOperation->addData('t_reimbursement', $arrInsertUpdate);
			$msgInsertUpdate	=	"Reimbursement data has been added";
		}
		
		if(!$procInsertUpdate['status']) switchMySQLErrorCode($procInsertUpdate['errCode'], $this->newToken);
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New additional cost data has been saved"));
	}
	
	private function checkInputData(){
		$arrVarValidate	=	array(
			array("reimbursementDate","option","Reimbursement Date"),
			array("requestByType","option","Request By"),
			array("idDriverVendor","option","Driver/Vendor Name"),
			array("requesterName","text","Requester Name"),
			array("reimbursementNominal","text","Reimbursement Nominal"),
			array("reimbursementDescription","text","Reimbursement Description"),
			array("reimbursementReceipt","option","Reimbursement Receipt")
		);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;
		
	}

	public function cancelReimbursement(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelReimbursement');
		
		$idReimbursement	=	validatePostVar($this->postVar, 'idData', true);
		$cancellationReason	=	validatePostVar($this->postVar, 'cancellationReason', true);
		$userAdminName		=	validatePostVar($this->postVar, 'NAME', true);
		$dataDetails		=	$this->ModelReimbursement->getDetailReimbursement($idReimbursement);

		if(!$dataDetails) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"<b>Failed to cancel reimbursement!</b><br/>Please refresh data and try again"));
		$idWithdrawalRecap	=	$dataDetails['IDWITHDRAWALRECAP'];
		$inputMethod		=	$dataDetails['INPUTMETHOD'];
		$statusReimbursement=	$dataDetails['STATUS'];
		$notes				=	$dataDetails['NOTES'];
		$notes				=	$notes == "" ? "" : $notes."\n";
		
		if($idWithdrawalRecap != 0 || $inputMethod != 2 || $statusReimbursement != 1){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"<b>Failed to cancel reimbursement!</b><br/>This reimbursement has been withdrawn or is not allowed to be cancelled"));
		}

		$arrUpdate		=	array(	
			"NOTES"		=>	$notes."Cancelled by : ".$userAdminName." at ".date('d M Y H:i').". Reason : ".$cancellationReason,
			"STATUS"	=>	-2
		);

		$updateResult	=	$this->MainOperation->updateData("t_reimbursement", $arrUpdate, "IDREIMBURSEMENT", $idReimbursement);
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);

		setResponseOk(array("token"=>$this->newToken, "msg"=>"Reimbursement has been canceled"));
	}	

	public function excelDetail($encryptedVar){
		$this->load->model('Finance/ModelReimbursement');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$decryptedVar	=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar=	json_decode($decryptedVar);
		$startDate		=	$expDecryptedVar->startDate;
		$endDate		=	$expDecryptedVar->endDate;
		$keywordSearch	=	$expDecryptedVar->keywordSearch;
		$viewRequestOnly=	$expDecryptedVar->viewRequestOnly;
		
		if($startDate == "" && $endDate != "") $startDate	=	$endDate;
		if($startDate != "" && $endDate == "") $endDate	=	$startDate;
		
		$dataTable		=	$this->ModelReimbursement->getDataReimbursement(1, 999999, $startDate, $endDate, $keywordSearch, $viewRequestOnly);

		if(!$dataTable){
			echo "No data found!";
			die();
		}
		$spreadsheet	=	new Spreadsheet();
		$sheet			=	$spreadsheet->getActiveSheet();
		$reportTypeStr	=	'-';
		$sourceNameStr	=	isset($idSource) && $idSource != '' && $idSource != 0 ? $this->MainOperation->getSourceNameById($idSource) : "All Source";
		$startDateDT	=	DateTime::createFromFormat('Y-m-d', $startDate);
		$startDateStr	=	$startDateDT->format('d M Y');
		$endDateDT		=	DateTime::createFromFormat('Y-m-d', $endDate);
		$endDateStr		=	$endDateDT->format('d M Y');
		
		$sheet->getPageSetup()->setOrientation(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		$sheet->getPageSetup()->setPaperSize(PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(0.25);
		$sheet->getPageMargins()->setRight(0.2);
		$sheet->getPageMargins()->setLeft(0.2);
		$sheet->getPageMargins()->setBottom(0.25);
		
		$sheet->setCellValue('A1', 'Bali Sun Tours');		$sheet->mergeCells('A1:H1');
		$sheet->setCellValue('A2', 'Detail Reimbursement');	$sheet->mergeCells('A2:H2');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		
		$viewRequestOnlyStr	=	$viewRequestOnly ? 'Yes' : 'No';
		$sheet->setCellValue('A4', 'Period');		$sheet->setCellValue('B4', ': '.$startDateStr." - ".$endDateStr);	$sheet->mergeCells('B4:H4');
		$sheet->setCellValue('A5', 'Report Type');	$sheet->setCellValue('B5', ': '.$keywordSearch);					$sheet->mergeCells('B5:H5');
		$sheet->setCellValue('A6', 'Request Only');	$sheet->setCellValue('B6', ': '.$viewRequestOnlyStr);				$sheet->mergeCells('B6:H6');
		
		$sheet->setCellValue('A8', 'Date');
		$sheet->setCellValue('B8', 'Status');
		$sheet->setCellValue('C8', 'Request By');
		$sheet->setCellValue('D8', 'Description');
		$sheet->setCellValue('E8', 'Input Detail');
		$sheet->setCellValue('F8', 'Approval Detail');
		$sheet->setCellValue('G8', 'Notes');
		$sheet->setCellValue('H8', 'Nominal');
		$sheet->getStyle('A8:H8')->getFont()->setBold( true );
		$sheet->getStyle('A8:H8')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A8:H8')->getAlignment()->setVertical('center');
		$rowNumber	=	9;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$grandTotalReimbursement =	0;
		foreach($dataTable['data'] as $data){
			$grandTotalReimbursement+=	$data->NOMINAL;
			$strStatus				=	'-';
			
			switch(intval($data->STATUS)){
				case "0"	:	$strStatus	=	'Requested'; break;
				case "1"	:	$strStatus	=	'Approved'; break;
				case "-1"	:	$strStatus	=	'Rejected'; break;
				case "-2"	:	$strStatus	=	'Cancelled'; break;
				default		:	$strStatus	=	'-'; break;
			}
			
			$sheet->setCellValue('A'.$rowNumber, $data->DATERECEIPT);
			$sheet->setCellValue('B'.$rowNumber, $strStatus);
			$sheet->setCellValue('C'.$rowNumber, '['.$data->REQUESTBYTYPE.'] '.$data->REQUESTBYNAME);
			$sheet->setCellValue('D'.$rowNumber, $data->DESCRIPTION);
			$sheet->setCellValue('E'.$rowNumber, $data->INPUTBYNAME.PHP_EOL.$data->INPUTDATETIME);
			$sheet->setCellValue('F'.$rowNumber, $data->APPROVALBYNAME.PHP_EOL.$data->APPROVALDATETIME);
			$sheet->setCellValue('G'.$rowNumber, $data->NOTES);
			$sheet->setCellValue('H'.$rowNumber, $data->NOMINAL);
			$rowNumber++;
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':G'.$rowNumber);
		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('H'.$rowNumber, $grandTotalReimbursement);	$sheet->getStyle('H'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('H'.$rowNumber)->getFont()->setBold(true);
		
		$sheet->getStyle('A8:H'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('I'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(15);
		$sheet->getColumnDimension('B')->setWidth(16);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(40);
		$sheet->getColumnDimension('E')->setWidth(18);
		$sheet->getColumnDimension('F')->setWidth(18);
		$sheet->getColumnDimension('G')->setWidth(30);
		$sheet->getColumnDimension('H')->setWidth(16);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDetailReimbursement_'.$startDateStr.'_'.$endDateStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
}