<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class DetailReservationPayment extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(3);

		if($functionName != "excelReport" && $functionName != "uploadExcelPaymentOTA" && $_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->postVar	=	decodeJsonPost();
			$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid submission data".$functionName));
			$this->newToken	=	isLoggedIn($this->token, true);
		} 
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataReservationPayment(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelDetailReservationPayment');
		
		$page					=	validatePostVar($this->postVar, 'page', true);
		$idPaymentMethod		=	validatePostVar($this->postVar, 'idPaymentMethod', false);
		$paymentStatus			=	validatePostVar($this->postVar, 'paymentStatus', false);
		$refundType				=	validatePostVar($this->postVar, 'refundType', false);
		$idSource				=	validatePostVar($this->postVar, 'idSource', false);
		$idPartner				=	validatePostVar($this->postVar, 'idPartner', false);
		$keywordSearch			=	validatePostVar($this->postVar, 'keywordSearch', false);
		$orderBy				=	validatePostVar($this->postVar, 'orderBy', true);
		$orderType				=	validatePostVar($this->postVar, 'orderType', true);
		$startDate				=	validatePostVar($this->postVar, 'startDate', false);
		$endDate				=	validatePostVar($this->postVar, 'endDate', false);
		$viewUnmatchPaymentOnly	=	validatePostVar($this->postVar, 'viewUnmatchPaymentOnly', false);
		$arrDates				=	array();
		$strArrIdReservation	=	"";
		
		if($startDate == "" && $endDate != "") $startDate	=	$endDate;
		if($startDate != "" && $endDate == "") $endDate	=	$startDate;

		if($startDate != ""){
			$startDateDT	=	DateTime::createFromFormat('d-m-Y', $startDate);
			$startDateTS	=	$startDateDT->getTimestamp();
			$startDate		=	$startDateDT->format('Y-m-d');
			$endDateDT		=	DateTime::createFromFormat('d-m-Y', $endDate);
			$endDateTS		=	$endDateDT->getTimestamp();
			$endDate		=	$endDateDT->format('Y-m-d');
			$totalDays		=	$startDateDT->diff($endDateDT)->days;
			$arrDates		=	getArrDateBetween($startDateDT, $endDateDT);

			if($endDateTS < $startDateTS){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid date range selection"));
			}
			
			if($totalDays > 31){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Maximum date range is <b>31</b> days"));
			}
		}
		
		if($idPartner != ""){
			$expIdPartner		=	explode("-", $idPartner);
			$partnerType		=	$expIdPartner[0];
			
			if($partnerType == 1){
				$idVendorType			=	$expIdPartner[1];
				if($idVendorType == 1){
					$idVendorCar		=	$expIdPartner[2];
					$strArrIdReservation=	$this->ModelDetailReservationPayment->getStrArrIdReservationByIdVendorCar($idVendorCar);
				} else {
					$idVendorTicket		=	$expIdPartner[2];
					$strArrIdReservation=	$this->ModelDetailReservationPayment->getStrArrIdReservationByIdVendorTicket($idVendorTicket);
				}
			} else {
				$idDriver				=	$expIdPartner[1];
				$strArrIdReservation	=	$this->ModelDetailReservationPayment->getStrArrIdReservationByIdDriver($idDriver);
			}
		}
		
		$urlExcelReport		=	BASE_URL."finance/detailReservationPayment/excelReport/".base64_encode(encodeStringKeyFunction($paymentStatus."|".$idSource."|".$idPartner."|".$idPaymentMethod."|".$keywordSearch."|".$startDate."|".$endDate."|".$viewUnmatchPaymentOnly."|".$refundType, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
		$dataTable			=	$this->ModelDetailReservationPayment->getDataReservationPayment($page, 25, $arrDates, $paymentStatus, $refundType, $idSource, $strArrIdReservation, $idPaymentMethod, $keywordSearch, $startDate, $endDate, $orderBy, $orderType, $viewUnmatchPaymentOnly);

		if(count($dataTable['data']) > 0){
			foreach($dataTable['data'] as $keyData){
				$paymentData=	$this->ModelDetailReservationPayment->getPaymentData($keyData->IDRESERVATION, $idPaymentMethod);
				if($paymentData){
					$incomeAmountFinance	=	0;
					foreach($paymentData as $paymentKey){
						$incomeAmountFinance	+=	$paymentKey->AMOUNTIDR;
					}
					$keyData->PAYMENTDATA			=	$paymentData;
					$keyData->INCOMEAMOUNTFINANCE	=	$incomeAmountFinance;
				} else {
					$keyData->PAYMENTDATA			=	array();
				}
				
				$durationOfDay		=	$keyData->DURATIONOFDAY;
				$dateStartSchedule	=	$keyData->RESERVATIONDATEVALUE;
				$arrDateSchedule	=	"";
				
				for($iDay=0; $iDay<$durationOfDay; $iDay++){
					$dateSchedule			=	date('Ymd', strtotime($dateStartSchedule . ' +'.$iDay.' day'));
					$arrDateSchedule		.=	$dateSchedule.",";
				}
				
				$keyData->ARRDATESCHEDULE	=	substr($arrDateSchedule, 0, -1);
			}
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "urlExcelReport"=>$urlExcelReport));
	}
	
	public function updateRevenueReservation(){
		$this->load->model('MainOperation');
		
		$idReservation			=	validatePostVar($this->postVar, 'idReservation', true);
		$currency				=	validatePostVar($this->postVar, 'currency', true);
		$revenueInteger			=	str_replace(",", "", validatePostVar($this->postVar, 'revenueInteger', true));
		$revenueDecimal			=	validatePostVar($this->postVar, 'revenueDecimal', false);
		$reservationNominal		=	$revenueInteger.".".$revenueDecimal;
		$currencyExchange		=	str_replace(",", "", validatePostVar($this->postVar, 'currencyExchange', true));
		
		$reservationRevenueIDR	=	$reservationNominal * $currencyExchange;
		$dataUserAdmin			=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName			=	$dataUserAdmin['NAME'];
		$arrUpdateRsv			=	array(
			"INCOMEAMOUNTCURRENCY"	=>	$currency,
			"INCOMEAMOUNT"			=>	$reservationNominal,
			"INCOMEEXCHANGECURRENCY"=>	$currencyExchange,
			"INCOMEAMOUNTIDR"		=>	$reservationRevenueIDR,
			"USERLASTUPDATE"		=>	$userAdminName,
			"DATETIMELASTUPDATE"	=>	date('Y-m-d H:i:s')
		);
		
		$procUpdateRsv	=	$this->MainOperation->updateData('t_reservation', $arrUpdateRsv, "IDRESERVATION", $idReservation);
		if(!$procUpdateRsv['status']) switchMySQLErrorCode($procUpdateRsv['errCode'], $this->newToken);
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Reservation revenue data has been updated"));
	}
	
	public function addReservationPayment(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelDetailReservationPayment');
		
		$idReservation		=	validatePostVar($this->postVar, 'idReservation', true);
		$idPaymentMethod	=	validatePostVar($this->postVar, 'optionPaymentMethod', true);
		$paymentStatus		=	validatePostVar($this->postVar, 'optionPaymentStatus', true);
		$description		=	validatePostVar($this->postVar, 'description', false);
		$isUpsellingPayment	=	validatePostVar($this->postVar, 'checkboxUpsellingPayment', false);
		$idDriverCollect	=	validatePostVar($this->postVar, 'optionDriverCollect', false);
		$idDriverCollect	=	!isset($idDriverCollect) || $idDriverCollect == "" ? 0 : $idDriverCollect;
		$idVendorCollect	=	validatePostVar($this->postVar, 'optionVendorCollect', false);
		$idVendorCollect	=	!isset($idVendorCollect) || $idVendorCollect == "" ? 0 : $idVendorCollect;
		$dateCollect		=	validatePostVar($this->postVar, 'optionDateCollect', false);
		$paymentCurrency	=	validatePostVar($this->postVar, 'paymentCurrency', true);
		$priceInteger		=	str_replace(",", "", validatePostVar($this->postVar, 'paymentPriceInteger', true));
		$priceDecimal		=	validatePostVar($this->postVar, 'paymentPriceDecimal', false);
		$pricePayment		=	$priceInteger.".".$priceDecimal;
		$pricePayment		=	$pricePayment * 1;
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$dataUserAdmin['NAME'];
		$dataExchangeCurr	=	$this->MainOperation->getDataExchangeCurrency();
		
		if($pricePayment <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please input valid amount"));
		if(!isset($idReservation) || $idReservation == ""  || $idReservation == 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));

		$nomExchangeCurr	=	$paymentCurrency == "IDR" ? 1 : $this->MainOperation->getCurrencyExchangeByDate($paymentCurrency, $dateCollect);
		$amountIDR			=	$pricePayment * $nomExchangeCurr;
		$arrInsert			=	array(
									"IDRESERVATION"		=>	$idReservation,
									"IDPAYMENTMETHOD"	=>	$idPaymentMethod,
									"DESCRIPTION"		=>	$description,
									"AMOUNTCURRENCY"	=>	$paymentCurrency,
									"AMOUNT"			=>	$pricePayment,
									"EXCHANGECURRENCY"	=>	$nomExchangeCurr,
									"AMOUNTIDR"			=>	$amountIDR,
									"ISUPSELLING"		=>	$isUpsellingPayment,
									"USERINPUT"			=>	$userAdminName,
									"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
									"STATUS"			=>	$paymentStatus,
									"EDITABLE"			=>	1,
									"DELETABLE"			=>	1
								);
		$procInsertPymt	=	$this->MainOperation->addData('t_reservationpayment', $arrInsert);
		
		if(!$procInsertPymt['status']) switchMySQLErrorCode($procInsertPymt['errCode'], $this->newToken);
		$idReservationPayment	=	$procInsertPymt['insertID'];
		
		if($idPaymentMethod == 2 || $idPaymentMethod == 7){
			
			if(!isset($dateCollect) || $dateCollect == "" || !validateDate($dateCollect)){
				$this->MainOperation->deleteData('t_reservationpayment', array("IDRESERVATIONPAYMENT" => $idReservationPayment));
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select a valid date to create collect payment"));
			}

			$idPartnerType		=	$idPaymentMethod == 2 ? 2 : 1;
			$idVendorCollect	=	$idPaymentMethod == 2 ? 0 : $idVendorCollect;
			$idDriverCollect	=	$idPaymentMethod == 2 ? $idDriverCollect : 0;
			$statusCollected	=	0;
			$dateStatusCollected=	false;
			$partnerName		=	$idPaymentMethod == 2 ? $this->MainOperation->getDriverNameById($idDriverCollect) : $this->MainOperation->getVendorNameById($idVendorCollect);

			if($idPartnerType == 2){
				$dataDriverSchedule	=	$this->ModelDetailReservationPayment->checkDataDriverSchedule($idReservation, $dateCollect);
				
				if($dataDriverSchedule){
					$scheduleStatus		=	$dataDriverSchedule['STATUS'];
					$newFinanceScheme	=	$dataDriverSchedule['NEWFINANCESCHEME'];
					
					if($scheduleStatus == 3 && $paymentStatus == 0 && $newFinanceScheme == 1){
						$statusCollected	=	1;
						$dateStatusCollected=	date('Y-m-d H:i:s');
						// $this->MainOperation->deleteData('t_reservationpayment', array("IDRESERVATIONPAYMENT" => $idReservationPayment));
						// setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Failed! Cannot create new payment data, this order has been finished by driver"));
					}
				}
			}
			
			if($idPartnerType == 2 && $idDriverCollect == 0){
				if($dataDriverSchedule){
					$idDriverCollect	=	$dataDriverSchedule['IDDRIVER'];
				}
			}
			
			if($idPartnerType == 1 && $idVendorCollect == 0){
				$dataVendorSchedule	=	$this->ModelDetailReservationPayment->checkDataVendorSchedule($idReservation);
				if($dataVendorSchedule){
					$idVendorCollect	=	$dataVendorSchedule['IDVENDOR'];
				}
			}
			
			$newFinanceScheme	=	$idPaymentMethod == 2 ? $this->MainOperation->getNewFinanceSchemeDriver($idDriverCollect) : $this->MainOperation->getNewFinanceSchemeVendor($idVendorCollect);
			$arrInsertCollect	=	array(
										"IDRESERVATION"			=>	$idReservation,
										"IDRESERVATIONPAYMENT"	=>	$idReservationPayment,
										"IDPARTNERTYPE"			=>	$idPartnerType,
										"IDVENDOR"				=>	$idVendorCollect,
										"IDDRIVER"				=>	$idDriverCollect,
										"DATECOLLECT"			=>	$dateCollect,
										"DATETIMEINPUT"			=>	date('Y-m-d H:i:s'),
										"LASTUSERINPUT"			=>	$userAdminName." (Admin)"
									);
									
			if($newFinanceScheme != 1){
				$arrInsertCollect['STATUS']						=	1;
				$arrInsertCollect['STATUSSETTLEMENTREQUEST']	=	2;
			} else {
				$arrInsertCollect['STATUS']						=	$statusCollected;				
				if($dateStatusCollected){
					$arrInsertCollect['DATETIMESTATUS']			=	$dateStatusCollected;
				}
			}
			
			$procInsertCollect	=	$this->MainOperation->addData("t_collectpayment", $arrInsertCollect);
				
			if($procInsertCollect['status']){
				$idCollectPayment		=	$procInsertCollect['insertID'];
				$arrInsertCollectHistory=	array(
												"IDCOLLECTPAYMENT"	=>	$idCollectPayment,
												"DESCRIPTION"		=>	"Collect payment is set to ".$partnerName,
												"SETTLEMENTRECEIPT"	=>	"",
												"USERINPUT"			=>	$userAdminName." (Admin)",
												"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
												"STATUS"			=>	0
											);
				$this->MainOperation->addData("t_collectpaymenthistory", $arrInsertCollectHistory);
				if($newFinanceScheme == 1) $this->sendCollectPaymentNotification($idPartnerType, $idVendorCollect, $idDriverCollect, $idCollectPayment);
			}
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Reservation payment data has been saved"));
	}
	
	public function updateReservationPayment(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelDetailReservationPayment');
		
		$idReservationPymnt	=	validatePostVar($this->postVar, 'idData', true);
		$idPaymentMethod	=	validatePostVar($this->postVar, 'optionPaymentMethod', true);
		$paymentStatus		=	validatePostVar($this->postVar, 'optionPaymentStatus', true);
		$description		=	validatePostVar($this->postVar, 'description', false);
		$isUpsellingPayment	=	validatePostVar($this->postVar, 'checkboxUpsellingPayment', false);
		$idDriverCollect	=	validatePostVar($this->postVar, 'optionDriverCollect', false);
		$idDriverCollect	=	!isset($idDriverCollect) || $idDriverCollect == "" ? 0 : $idDriverCollect;
		$idVendorCollect	=	validatePostVar($this->postVar, 'optionVendorCollect', false);
		$idVendorCollect	=	!isset($idVendorCollect) || $idVendorCollect == "" ? 0 : $idVendorCollect;
		$dateCollect		=	validatePostVar($this->postVar, 'optionDateCollect', false);
		$paymentCurrency	=	validatePostVar($this->postVar, 'paymentCurrency', true);
		$priceInteger		=	str_replace(",", "", validatePostVar($this->postVar, 'paymentPriceInteger', true));
		$priceDecimal		=	validatePostVar($this->postVar, 'paymentPriceDecimal', false);
		$pricePayment		=	$priceInteger.".".$priceDecimal;
		$pricePayment		=	$pricePayment * 1;
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$dataUserAdmin['NAME'];
		$dataExchangeCurr	=	$this->MainOperation->getDataExchangeCurrency();
		
		if($pricePayment <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please input valid amount"));

		$nomExchangeCurr			=	$paymentCurrency == "IDR" ? 1 : $this->MainOperation->getCurrencyExchangeByDate($paymentCurrency, $dateCollect);
		$amountIDR					=	$pricePayment * $nomExchangeCurr;
		$detailReservationPayment	=	$this->ModelDetailReservationPayment->getDetailReservationPayment($idReservationPymnt);
		
		if(!$detailReservationPayment) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Failed to delete data. The data you selected no longer exists"));
		
		$deletablePayment		=	$detailReservationPayment['DELETABLE'];

		if($deletablePayment == 0){
			$arrUpdate			=	array(
										"AMOUNTCURRENCY"	=>	$paymentCurrency,
										"AMOUNT"			=>	$pricePayment,
										"AMOUNTIDR"			=>	$amountIDR,
										"USERUPDATE"		=>	$userAdminName,
										"DATETIMEUPDATE"	=>	date('Y-m-d H:i:s')
									);
		} else {
			$arrUpdate			=	array(
										"IDPAYMENTMETHOD"	=>	$idPaymentMethod,
										"DESCRIPTION"		=>	$description,
										"AMOUNTCURRENCY"	=>	$paymentCurrency,
										"AMOUNT"			=>	$pricePayment,
										"AMOUNTIDR"			=>	$amountIDR,
										"ISUPSELLING"		=>	$isUpsellingPayment,
										"USERUPDATE"		=>	$userAdminName,
										"DATETIMEUPDATE"	=>	date('Y-m-d H:i:s'),
										"STATUS"			=>	$paymentStatus
									);
		}
		
		if($idPaymentMethod == 2 || $idPaymentMethod == 7){	

			if(!isset($dateCollect) || $dateCollect == "" || !validateDate($dateCollect)) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select a valid date to create collect payment"));

			$idPartnerType		=	$idPaymentMethod == 2 ? 2 : 1;
			$idVendorCollect	=	$idPaymentMethod == 2 ? 0 : $idVendorCollect;
			$idDriverCollect	=	$idPaymentMethod == 2 ? $idDriverCollect : 0;
			$idReservation		=	$detailReservationPayment['IDRESERVATION'];
			$statusCollected	=	0;
			$dateStatusCollected=	false;
			$partnerName		=	$idPaymentMethod == 2 ? $this->MainOperation->getDriverNameById($idDriverCollect) : $this->MainOperation->getVendorNameById($idVendorCollect);
			
			if($idPartnerType == 2){
				$dataDriverSchedule	=	$this->ModelDetailReservationPayment->checkDataDriverSchedule($idReservation, $dateCollect);
				
				if($dataDriverSchedule){
					$scheduleStatus		=	$dataDriverSchedule['STATUS'];
					
					if($scheduleStatus == 3 && $paymentStatus == 0){
						$statusCollected	=	1;
						$dateStatusCollected=	date('Y-m-d H:i:s');
						// setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Failed! Cannot update payment data, this order has been finished by driver"));
					}
				}
			}

			if($idPartnerType == 2 && $idDriverCollect == 0){
				if($dataDriverSchedule){
					$idDriverCollect	=	$dataDriverSchedule['IDDRIVER'];
					$partnerName		=	$dataDriverSchedule['PARTNERNAME'];
				}
			}
			
			if($idPartnerType == 1 && $idVendorCollect == 0){
				$dataVendorSchedule	=	$this->ModelDetailReservationPayment->checkDataVendorSchedule($idReservation);
				if($dataVendorSchedule){
					$idVendorCollect	=	$dataVendorSchedule['IDVENDOR'];
					$partnerName		=	$dataVendorSchedule['PARTNERNAME'];
				}
			}
			
			$isCollectExist		=	$this->ModelDetailReservationPayment->isCollectPaymentExist($idReservationPymnt);
			$newFinanceScheme	=	$idPaymentMethod == 2 ? $this->MainOperation->getNewFinanceSchemeDriver($idDriverCollect) : $this->MainOperation->getNewFinanceSchemeVendor($idVendorCollect);
			$arrInsUpdCollect	=	array(
										"IDRESERVATION"			=>	$idReservation,
										"IDRESERVATIONPAYMENT"	=>	$idReservationPymnt,
										"IDPARTNERTYPE"			=>	$idPartnerType,
										"IDVENDOR"				=>	$idVendorCollect,
										"IDDRIVER"				=>	$idDriverCollect,
										"DATECOLLECT"			=>	$dateCollect,
										"DATETIMEINPUT"			=>	date('Y-m-d H:i:s'),
										"LASTUSERINPUT"			=>	$userAdminName." (Admin)"
									);
			if(!$isCollectExist){
				if($newFinanceScheme != 1){
					$arrInsUpdCollect['STATUS']						=	1;
					$arrInsUpdCollect['STATUSSETTLEMENTREQUEST']	=	2;
				} else {
					$arrInsUpdCollect['STATUS']						=	$statusCollected;				
					if($dateStatusCollected){
						$arrInsUpdCollect['DATETIMESTATUS']			=	$dateStatusCollected;
					}
				}
			
				if($dateStatusCollected){
					$arrInsertCollect['DATETIMESTATUS']				=	$dateStatusCollected;
				}

				$procInsertCollect	=	$this->MainOperation->addData("t_collectpayment", $arrInsUpdCollect);
				
				if($procInsertCollect['status']){
					$idCollectPayment		=	$procInsertCollect['insertID'];
					$arrInsertCollectHistory=	array(
													"IDCOLLECTPAYMENT"	=>	$idCollectPayment,
													"DESCRIPTION"		=>	"Collect payment is set to ".$partnerName,
													"SETTLEMENTRECEIPT"	=>	"",
													"USERINPUT"			=>	$userAdminName." (Admin)",
													"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
													"STATUS"			=>	0
												);
					$this->MainOperation->addData("t_collectpaymenthistory", $arrInsertCollectHistory);
					if($newFinanceScheme == 1) $this->sendCollectPaymentNotification($idPartnerType, $idVendorCollect, $idDriverCollect, $idCollectPayment);
				}
			} else {
				if($newFinanceScheme != 1){
					$arrInsUpdCollect['STATUS']						=	1;
					$arrInsUpdCollect['STATUSSETTLEMENTREQUEST']	=	2;
				} else {
					$arrInsUpdCollect['STATUS']						=	$statusCollected;				
					if($dateStatusCollected){
						$arrInsUpdCollect['DATETIMESTATUS']			=	$dateStatusCollected;
					}
					$arrInsUpdCollect['STATUSSETTLEMENTREQUEST']	=	0;
				}
				
				$idCollectPayment				=	$this->ModelDetailReservationPayment->getIdCollectPaymentByIdReservationPayment($idReservationPymnt);
				$arrUpdateCollectPaymentHistory	=	array("DESCRIPTION" => "Collect payment is set to ".$partnerName);
				$this->MainOperation->updateData("t_collectpayment", $arrInsUpdCollect, "IDRESERVATIONPAYMENT", $idReservationPymnt);
				$this->MainOperation->updateData('t_collectpaymenthistory', $arrUpdateCollectPaymentHistory, array("IDCOLLECTPAYMENT" => $idCollectPayment, "STATUS" => 0));
			}
		} else {
			$procDelete	=	$this->MainOperation->deleteData('t_collectpayment', array("IDRESERVATIONPAYMENT" => $idReservationPymnt));
		}
		
		$procUpdatePymt	=	$this->MainOperation->updateData('t_reservationpayment', $arrUpdate, "IDRESERVATIONPAYMENT", $idReservationPymnt);		
		if(!$procUpdatePymt['status']) switchMySQLErrorCode($procUpdatePymt['errCode'], $this->newToken);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Reservation payment data has been updated"));	
	}
	
	private function checkInputData(){
		$arrVarValidate	=	array(
			array("optionPaymentMethod", "option", "Payment Method"),
			array("optionPaymentStatus", "option", "Payment Status"),
			array("paymentCurrency", "option", "Currency"),
			array("paymentPriceInteger", "text", "Price")
		);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate, "postVar"=>$this->postVar));
		return true;
	}
	
	public function deleteReservationPayment(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelDetailReservationPayment');
		
		$idReservationPayment		=	validatePostVar($this->postVar, 'idReservationPayment', true);
		$detailReservationPayment	=	$this->ModelDetailReservationPayment->getDetailReservationPayment($idReservationPayment);

		if(!$detailReservationPayment) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Failed to delete data. The data you selected no longer exists"));

		$deletableStatus			=	$detailReservationPayment['DELETABLE'];
		$idPaymentMethod			=	$detailReservationPayment['IDPAYMENTMETHOD'];
		$idCollectPayment			=	$detailReservationPayment['IDCOLLECTPAYMENT'];
		$newFinanceScheme			=	$detailReservationPayment['NEWFINANCESCHEME'];

		if($deletableStatus != 1) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Failed to delete data. This data is not allowed to be deleted"));
		if($idPaymentMethod == 2 || $idPaymentMethod == 7){
			$detailCollectPayment	=	$this->ModelDetailReservationPayment->getDetailCollectPayment($idCollectPayment);
			$statusSettlementCollect=	$detailCollectPayment['STATUSSETTLEMENTREQUEST'];
			
			if($statusSettlementCollect == 2 && $newFinanceScheme == 1){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Failed to delete data. This payment (Collect Payment) has been completed"));
			}
		}

		$procDelete					=	$this->MainOperation->deleteData('t_reservationpayment', array("IDRESERVATIONPAYMENT" => $idReservationPayment));
		
		if(!$procDelete['status']) switchMySQLErrorCode($procDelete['errCode'], $this->newToken);
		
		$this->MainOperation->deleteData('t_collectpayment', array("IDRESERVATIONPAYMENT" => $idReservationPayment));
		
		if($idPaymentMethod == 2 || $idPaymentMethod == 7){
			if(PRODUCTION_URL){
				$idDriver		=	$detailCollectPayment['IDDRIVER'];
				$idVendor		=	$detailCollectPayment['IDVENDOR'];
				$idPartnerType	=	$idDriver == 0 ? 1 : 2;
				$idPartner		=	$idPartnerType == 2 ? $idDriver : $idVendor;
				$strPartnerType	=	$idPartnerType == 2 ? 'driver' : 'vendor';
				$dataPartner	=	$idPartnerType == 2 ? $this->MainOperation->getDataDriver($idDriver) : $this->MainOperation->getDataVendor($idVendor);
				$RTDB_refCode	=	$dataPartner['RTDBREFCODE'];
				if($RTDB_refCode && $RTDB_refCode != ''){
					try {
						$factory			=	(new Factory)
												->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
												->withDatabaseUri(FIREBASE_RTDB_URI);
						$database			=	$factory->createDatabase();
						$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/".$strPartnerType."/".$RTDB_refCode."/activeCollectPayment");
						$referencePartnerVal=	$referencePartner->getValue();
						if($referencePartnerVal != null || !is_null($referencePartnerVal)){
							$referencePartner->update([
								'newCollectPaymentDetail'	=>  '',
								'newCollectPaymentStatus'	=>  false,
								'timestampUpdate'			=>  gmdate('YmdHis'),
								'totalActiveCollectPayment'	=>  $this->MainOperation->getTotalActiveCollectPayment($idPartnerType, $idPartner)
							]);
						}
					} catch (Exception $e) {
					}
				}
			}
		}

		setResponseOk(array("token"=>$this->newToken, "msg"=>"Reservation payment data has been deleted"));
	}
	
	public function searchReservationByKeyword(){
		$this->load->model('Finance/ModelDetailReservationPayment');
		
		$idReservation	=	validatePostVar($this->postVar, 'idReservation', true);
		$searchKeyword	=	validatePostVar($this->postVar, 'searchKeyword', false);
		$customerName	=	$bookingCode	=	"";
		
		if(!isset($searchKeyword) || $searchKeyword == "") setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please enter search keyword"));
	
		if(strpos($searchKeyword, "|")){
			$explodeKeyword	=	explode("|", $searchKeyword);
			$customerName	=	rtrim($explodeKeyword[0]);
			$bookingCode	=	ltrim($explodeKeyword[1]);
		}
	
		$result			=	$this->ModelDetailReservationPayment->getDataReservationByKeyword($idReservation, $searchKeyword, $customerName, $bookingCode);
		
		if(!$result) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found. Please enter other keywords"));
		setResponseOk(array("token"=>$this->newToken, "result"=>$result));
	}
	
	public function saveTransferDepositPayment(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelDetailReservationPayment');
		
		$idReservationFrom		=	validatePostVar($this->postVar, 'idReservationFrom', true);
		$idReservationTo		=	validatePostVar($this->postVar, 'idReservationTo', true);
		$nominalTransfer		=	validatePostVar($this->postVar, 'nominalTransfer', true);
		$maxTransferredDeposit	=	validatePostVar($this->postVar, 'maxTransferredDeposit', true);
		$bookingCodeTo			=	validatePostVar($this->postVar, 'bookingCodeTo', false);
		$bookingCodeFrom		=	validatePostVar($this->postVar, 'bookingCodeFrom', false);
		$errorMsg				=	"";
		
		if(!isset($idReservationFrom) || $idReservationFrom == 0 || $idReservationFrom == ""){
			$errorMsg			=	"Invalid submission data. Please refresh the page and try this action again";
		} else if(!isset($idReservationTo) || $idReservationTo == 0 || $idReservationTo == ""){
			$errorMsg			=	"Please select the reservation to which the deposit will be transferred first";
		} else if($nominalTransfer <= 0) {
			$errorMsg			=	"Deposit amount transferred cannot be zero (0)";
		} else if($nominalTransfer > $maxTransferredDeposit) {
			$errorMsg			=	"Deposit amount transferred cannot be more than <b>".number_format($maxTransferredDeposit, 0, '.', ',')." IDR</b>";
		}
		
		if($errorMsg != ""){
			setResponseForbidden(array("token"=>$this->newToken, $errorMsg));
		} else {
			$dataUserAdmin	=	$this->MainOperation->getDataUserAdmin($this->newToken);
			$userAdminName	=	$dataUserAdmin['NAME'];
			$arrInsertFrom	=	array(
									"IDRESERVATION"		=>	$idReservationFrom,
									"IDPAYMENTMETHOD"	=>	11,
									"DESCRIPTION"		=>	"Transfer deposit payment to ".$bookingCodeTo,
									"AMOUNTCURRENCY"	=>	"IDR",
									"AMOUNT"			=>	$nominalTransfer * -1,
									"EXCHANGECURRENCY"	=>	1,
									"AMOUNTIDR"			=>	$nominalTransfer * -1,
									"USERINPUT"			=>	$userAdminName,
									"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
									"STATUS"			=>	1,
									"EDITABLE"			=>	0,
									"DELETABLE"			=>	1
								);
			$procInsertFrom	=	$this->MainOperation->addData('t_reservationpayment', $arrInsertFrom);
			
			if(!$procInsertFrom['status']) switchMySQLErrorCode($procInsertPymt['errCode'], $this->newToken);
			$arrInsertTo	=	array(
									"IDRESERVATION"		=>	$idReservationTo,
									"IDPAYMENTMETHOD"	=>	11,
									"DESCRIPTION"		=>	"Transfer deposit payment from ".$bookingCodeFrom,
									"AMOUNTCURRENCY"	=>	"IDR",
									"AMOUNT"			=>	$nominalTransfer,
									"EXCHANGECURRENCY"	=>	1,
									"AMOUNTIDR"			=>	$nominalTransfer,
									"USERINPUT"			=>	$userAdminName,
									"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
									"STATUS"			=>	1,
									"EDITABLE"			=>	0,
									"DELETABLE"			=>	1
								);
			$procInsertTo	=	$this->MainOperation->addData('t_reservationpayment', $arrInsertTo);
			
			setResponseOk(array("token"=>$this->newToken, "msg"=>"Transfer deposit payment has been successfully made to ".$bookingCodeFrom." and ".$bookingCodeTo));
		}
	}
	
	public function excelReport($encryptedVar){
		$this->load->model('Finance/ModelDetailReservationPayment');
		$this->load->model('ModelReservation');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$arrDates				=	array();
		$decryptedVar			=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar		=	explode("|", $decryptedVar);
		$paymentStatus			=	$expDecryptedVar[0];
		$idSource				=	$expDecryptedVar[1];
		$idPartner				=	$expDecryptedVar[2];
		$idPaymentMethod		=	$expDecryptedVar[3];
		$keywordSearch			=	$expDecryptedVar[4];
		$startDate				=	$expDecryptedVar[5];
		$endDate				=	$expDecryptedVar[6];
		$viewUnmatchPaymentOnly	=	$expDecryptedVar[7];
		$refundType				=	$expDecryptedVar[8];
		$strArrIdReservation	=	$typePartner	=	$partnerName	=	"";

		if($startDate == "" && $endDate != "") $startDate	=	$endDate;
		if($startDate != "" && $endDate == "") $endDate	=	$startDate;
		
		if($startDate != ""){
			$startDateDT	=	DateTime::createFromFormat('Y-m-d', $startDate);
			$startDateStr	=	$startDateDT->format('d M Y');
			$endDateDT		=	DateTime::createFromFormat('Y-m-d', $endDate);
			$endDateStr		=	$endDateDT->format('d M Y');

			$totalDays		=	$startDateDT->diff($endDateDT)->days;
			$arrDates		=	getArrDateBetween($startDateDT, $endDateDT);
		}
		
		if($idPartner != ""){
			$expIdPartner		=	explode("-", $idPartner);
			$partnerType		=	$expIdPartner[0];
			
			if($partnerType == 1){
				$idVendorType	=	$expIdPartner[1];
				$partnerName	=	$this->MainOperation->getVendorNameById($expIdPartner[2]);
				if($idVendorType == 1){
					$idVendorCar			=	$expIdPartner[2];
					$strArrIdReservation	=	$this->ModelReservation->getStrArrIdReservationByIdVendorCar($idVendorCar, $startDate, $endDate);
					$typePartner			=	"Car Vendor";
				} else {
					$idVendorTicket			=	$expIdPartner[2];
					$strArrIdReservation	=	$this->ModelReservation->getStrArrIdReservationByIdVendorTicket($idVendorTicket, $startDate, $endDate);
					$typePartner			=	"Ticket Vendor";
				}
			} else {
				$idDriver				=	$expIdPartner[1];
				$strArrIdReservation	=	$this->ModelReservation->getStrArrIdReservationByIdDriver($idDriver, $startDate, $endDate);
				$detailDriver			=	$this->MainOperation->getDataDriver($idDriver);
				$partnerName			=	$detailDriver['NAME'];
				$typePartner			=	"Driver";
			}
		}
		
		$paymentStatusStr	=	"";
		switch($paymentStatus){
			 case ""	:	$paymentStatusStr	=	"All Payment Status"; break;
			 case "-2"	:	$paymentStatusStr	=	"No Payment Data"; break;
			 case "0"	:	$paymentStatusStr	=	"Unpaid/Pending"; break;
			 case "1"	:	$paymentStatusStr	=	"Paid"; break;
			 case "-1"	:	$paymentStatusStr	=	"Cancel"; break;
			 default	:	$paymentStatusStr	=	"-"; break;
		}
		
		$refundTypeStr	=	"";
		switch($refundType){
			 case ""	:	$refundTypeStr	=	"All Refund Type"; break;
			 case "0"	:	$refundTypeStr	=	"No Refund"; break;
			 case "-1"	:	$refundTypeStr	=	"Full Refund"; break;
			 case "-2"	:	$refundTypeStr	=	"Partial Refund"; break;
			 default	:	$refundTypeStr	=	"All Refund Type"; break;
		}
		
		$paymentMethodStr	=	isset($idPaymentMethod) && $idPaymentMethod != "" && $idPaymentMethod != 0 ? $this->MainOperation->getPaymentMethodById($idPaymentMethod) : "All Payment Method";
		$rsvSourceStr		=	isset($idSource) && $idSource != "" && $idSource != 0 ? $this->MainOperation->getSourceNameById($idSource) : "All Source";
		$partnerStr			=	$idPartner == "" || $idPartner == 0 ? "All Partner" : $typePartner." - ".$partnerName;
		$dateRangeStr		=	$startDateStr." to ".$endDateStr;
		$dataReservation	=	$this->ModelDetailReservationPayment->getDataReservationPayment(1, 999999, $arrDates, $paymentStatus, $refundType, $idSource, $strArrIdReservation, $idPaymentMethod, $keywordSearch, $startDate, $endDate, 1, "DESC", $viewUnmatchPaymentOnly);
		
		if(!$dataReservation){
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
		$sheet->setCellValue('A2', 'Finance Report');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:S1');
		$sheet->mergeCells('A2:S2');
		
		$sheet->setCellValue('A4', 'Payment Method : '.$paymentMethodStr);
		$sheet->setCellValue('A5', 'Payment Status : '.$paymentStatusStr);
		$sheet->setCellValue('A6', 'Refund Type : '.$refundTypeStr);
		$sheet->setCellValue('A7', 'Source : '.$rsvSourceStr);
		$sheet->setCellValue('A8', 'Partner : '.$partnerStr);
		$sheet->setCellValue('A9', 'Keyword Search : '.$keywordSearch);
		$sheet->setCellValue('A10', 'Date Period : '.$dateRangeStr);
		
		$sheet->setCellValue('A12', 'Reservation Description');
		$sheet->setCellValue('I12', 'Total Revenue (Reservation)');
		$sheet->setCellValue('J12', 'Total Revenue (Finance)');
		$sheet->setCellValue('K12', 'Revenue Details');
		$sheet->setCellValue('N12', 'Cost Details');
		$sheet->setCellValue('T12', 'Margins');
		$sheet->mergeCells('A12:H12');
		$sheet->mergeCells('K12:M12');
		$sheet->mergeCells('N12:S12');
		$sheet->mergeCells('I12:I13');
		$sheet->mergeCells('J12:J13');
		$sheet->mergeCells('T12:T13');
		
		$sheet->setCellValue('A13', 'Date');
		$sheet->setCellValue('B13', 'Source');
		$sheet->setCellValue('C13', 'Booking Code');
		$sheet->setCellValue('D13', 'Reservation Title');
		$sheet->setCellValue('E13', 'Guest Name');
		$sheet->setCellValue('F13', 'Pax');
		$sheet->setCellValue('G13', 'Hotel');
		$sheet->setCellValue('H13', 'Remark');
		$sheet->setCellValue('K13', 'Payment Method');
		$sheet->setCellValue('L13', 'Value');
		$sheet->setCellValue('M13', 'Status');
		$sheet->setCellValue('N13', 'Date Handle');
		$sheet->setCellValue('O13', 'Handle By');
		$sheet->setCellValue('P13', 'Driver');
		$sheet->setCellValue('Q13', 'Ticket');
		$sheet->setCellValue('R13', 'Car');
		$sheet->setCellValue('S13', 'Total');
		$sheet->getStyle('A12:T13')->getFont()->setBold( true );
		$sheet->getStyle('A12:T13')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A12:T13')->getAlignment()->setVertical('center');
		$rowNumber	=	14;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		$grandTotalRevenueRsv = $grandTotalRevenue = $grandTotalPaymentMethod = $grandTotalDriverCost = $grandTotalTicketCost = $grandTotalCarCost = $grandTotalCost = $grandTotalMargin	=	0;
		foreach($dataReservation['data'] as $data){
			
			$totalCostPerReservation=	0;
			$reservationDateEnd		=	$data->RESERVATIONDATEEND != $data->RESERVATIONDATESTART ? "\nTo\n".$data->RESERVATIONDATEEND." ".$data->RESERVATIONTIMEEND : "";
			$paxStr					=	" ".$data->NUMBEROFADULT;
			
			if($data->NUMBEROFCHILD == 0 && $data->NUMBEROFINFANT != 0){
				$paxStr	.=	"+0+".$data->NUMBEROFINFANT;
			} else if($data->NUMBEROFCHILD != 0 && $data->NUMBEROFINFANT == 0){
				$paxStr	.=	"+".$data->NUMBEROFINFANT."+0";
			} else if($data->NUMBEROFCHILD != 0 && $data->NUMBEROFINFANT != 0){
				$paxStr	.=	"+".$data->NUMBEROFINFANT."+".$data->NUMBEROFCHILD;
			}
			
			$paymentData		=	$this->ModelDetailReservationPayment->getPaymentData($data->IDRESERVATION, $idPaymentMethod);
			$driverHandle		=	$this->ModelReservation->getReservationHandleDriver($data->IDRESERVATION);
			$ticketHandle		=	$this->ModelReservation->getReservationHandleVendorTicket($data->IDRESERVATION);
			$carHandle			=	$this->ModelReservation->getReservationHandleVendorCar($data->IDRESERVATION);
			$rowSpanPayment		=	$rowSpanCost	=	1;
			
			$sheet->setCellValue('A'.$rowNumber, $data->RESERVATIONDATESTART." ".$data->RESERVATIONTIMESTART.$reservationDateEnd);
			$sheet->setCellValue('B'.$rowNumber, $data->SOURCENAME);
			$sheet->setCellValue('C'.$rowNumber, $data->BOOKINGCODE);
			$sheet->setCellValue('D'.$rowNumber, $data->RESERVATIONTITLE);
			$sheet->setCellValue('E'.$rowNumber, $data->CUSTOMERNAME);
			$sheet->setCellValue('F'.$rowNumber, $paxStr);
			$sheet->setCellValue('G'.$rowNumber, $data->HOTELNAME);
			$sheet->setCellValue('H'.$rowNumber, $data->REMARK);
			$sheet->setCellValue('I'.$rowNumber, $data->INCOMEAMOUNTIDR);	$sheet->getStyle('I'.$rowNumber)->getAlignment()->setHorizontal('right');

			$totalRevenuePerReservation	=	0;
			if($paymentData){
				$iPayment		=	0;
				foreach($paymentData as $payment){
					
					if($payment->STATUS != -1){
						$statusPayment	=	$payment->STATUS == 1 ? "Paid" : "Unpaid";
						$sheet->setCellValue('K'.($rowNumber + $iPayment), $payment->PAYMENTMETHODNAME);
						$sheet->setCellValue('L'.($rowNumber + $iPayment), $payment->AMOUNTIDR);	$sheet->getStyle('L'.($rowNumber + $iPayment))->getAlignment()->setHorizontal('right');
						$sheet->setCellValue('M'.($rowNumber + $iPayment), $statusPayment);

						$grandTotalPaymentMethod +=	$payment->AMOUNTIDR;
						$sheet->getRowDimension($rowNumber + $iPayment)->setRowHeight(20);
						
						if($iPayment != 0){
							$rowSpanPayment++;
						}
						$totalRevenuePerReservation	+=	$payment->AMOUNTIDR;
						$iPayment++;
					}
				}
			} else {
				$sheet->setCellValue('K'.$rowNumber, "");
				$sheet->setCellValue('L'.$rowNumber, "");
				$sheet->setCellValue('M'.$rowNumber, "");
			}
			
			$rowNumberHandle	=	$rowNumber;
			$rowSpanCost		=	1;
			$iHandle			=	0;
			
			if($driverHandle){
				foreach($driverHandle as $driver){
					
					$sheet->setCellValue('N'.($rowNumberHandle + $iHandle), $driver->SCHEDULEDATE);
					$sheet->setCellValue('O'.($rowNumberHandle + $iHandle), $driver->PARTNERNAME);
					$sheet->setCellValue('P'.($rowNumberHandle + $iHandle), $driver->NOMINAL);	$sheet->getStyle('P'.($rowNumberHandle + $iHandle))->getAlignment()->setHorizontal('right');
					$sheet->setCellValue('Q'.($rowNumberHandle + $iHandle), 0);	$sheet->getStyle('Q'.($rowNumberHandle + $iHandle))->getAlignment()->setHorizontal('right');
					$sheet->setCellValue('R'.($rowNumberHandle + $iHandle), 0);	$sheet->getStyle('R'.($rowNumberHandle + $iHandle))->getAlignment()->setHorizontal('right');

					$grandTotalDriverCost	+=	$driver->NOMINAL;
					$grandTotalCost 		+=	$driver->NOMINAL;
					$totalCostPerReservation+=	$driver->NOMINAL;
					$sheet->getRowDimension($rowNumberHandle + $iHandle)->setRowHeight(25);
					
					if($iHandle != 0){
						$rowSpanCost++;
					}
					$iHandle++;
				}
			}
			
			if($ticketHandle){
				foreach($ticketHandle as $ticket){
					
					$sheet->setCellValue('N'.($rowNumberHandle + $iHandle), $ticket->SCHEDULEDATE);
					$sheet->setCellValue('O'.($rowNumberHandle + $iHandle), $ticket->PARTNERNAME);
					$sheet->setCellValue('P'.($rowNumberHandle + $iHandle), 0);	$sheet->getStyle('P'.($rowNumberHandle + $iHandle))->getAlignment()->setHorizontal('right');
					$sheet->setCellValue('Q'.($rowNumberHandle + $iHandle), $ticket->NOMINAL);	$sheet->getStyle('Q'.($rowNumberHandle + $iHandle))->getAlignment()->setHorizontal('right');
					$sheet->setCellValue('R'.($rowNumberHandle + $iHandle), 0);	$sheet->getStyle('R'.($rowNumberHandle + $iHandle))->getAlignment()->setHorizontal('right');

					$grandTotalTicketCost	+=	$ticket->NOMINAL;
					$grandTotalCost 		+=	$ticket->NOMINAL;
					$totalCostPerReservation+=	$ticket->NOMINAL;
					$sheet->getRowDimension($rowNumberHandle + $iHandle)->setRowHeight(25);
					
					if($iHandle != 0){
						$rowSpanCost++;
					}
					$iHandle++;
				}
			}
			
			if($carHandle){
				foreach($carHandle as $car){
					
					$sheet->setCellValue('N'.($rowNumberHandle + $iHandle), $car->SCHEDULEDATE);
					$sheet->setCellValue('O'.($rowNumberHandle + $iHandle), $car->PARTNERNAME);
					$sheet->setCellValue('P'.($rowNumberHandle + $iHandle), 0);	$sheet->getStyle('P'.($rowNumberHandle + $iHandle))->getAlignment()->setHorizontal('right');
					$sheet->setCellValue('Q'.($rowNumberHandle + $iHandle), 0);	$sheet->getStyle('Q'.($rowNumberHandle + $iHandle))->getAlignment()->setHorizontal('right');
					$sheet->setCellValue('R'.($rowNumberHandle + $iHandle), $car->NOMINAL);	$sheet->getStyle('R'.($rowNumberHandle + $iHandle))->getAlignment()->setHorizontal('right');

					$grandTotalTicketCost	+=	$car->NOMINAL;
					$grandTotalCost 		+=	$car->NOMINAL;
					$totalCostPerReservation+=	$car->NOMINAL;
					$sheet->getRowDimension($rowNumberHandle + $iHandle)->setRowHeight(25);
					
					if($iHandle != 0){
						$rowSpanCost++;
					}
					$iHandle++;
				}
			}
			
			$grandTotalRevenue			+=	$totalRevenuePerReservation;
			$grandTotalRevenueRsv		+=	$data->INCOMEAMOUNTIDR;
			$totalMarginPerReservation	=	$totalRevenuePerReservation - $totalCostPerReservation;
			$grandTotalMargin			+=	$totalMarginPerReservation;

			$sheet->setCellValue('J'.$rowNumber, $totalRevenuePerReservation);	$sheet->getStyle('J'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('S'.$rowNumber, $totalCostPerReservation);		$sheet->getStyle('S'.$rowNumber)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('T'.$rowNumber, $totalMarginPerReservation);	$sheet->getStyle('T'.$rowNumber)->getAlignment()->setHorizontal('right');
			
			if($rowSpanPayment > 1 || $rowSpanCost > 1){
				$rowSpanAdds	=	max(array($rowSpanPayment, $rowSpanCost));
				$sheet->mergeCells('A'.$rowNumber.':A'.($rowNumber+$rowSpanAdds-1));
				$sheet->mergeCells('B'.$rowNumber.':B'.($rowNumber+$rowSpanAdds-1));
				$sheet->mergeCells('C'.$rowNumber.':C'.($rowNumber+$rowSpanAdds-1));
				$sheet->mergeCells('D'.$rowNumber.':D'.($rowNumber+$rowSpanAdds-1));
				$sheet->mergeCells('E'.$rowNumber.':E'.($rowNumber+$rowSpanAdds-1));
				$sheet->mergeCells('F'.$rowNumber.':F'.($rowNumber+$rowSpanAdds-1));
				$sheet->mergeCells('G'.$rowNumber.':G'.($rowNumber+$rowSpanAdds-1));
				$sheet->mergeCells('H'.$rowNumber.':H'.($rowNumber+$rowSpanAdds-1));
				$sheet->mergeCells('I'.$rowNumber.':I'.($rowNumber+$rowSpanAdds-1));
				$sheet->mergeCells('J'.$rowNumber.':J'.($rowNumber+$rowSpanAdds-1));
				$sheet->mergeCells('S'.$rowNumber.':S'.($rowNumber+$rowSpanAdds-1));
				$sheet->mergeCells('T'.$rowNumber.':T'.($rowNumber+$rowSpanAdds-1));
				$rowNumber	+=	$rowSpanAdds;
			} else {
				$sheet->getRowDimension($rowNumber)->setRowHeight(50);
				$rowNumber++;
			}
			
			
		}
		
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':H'.$rowNumber);
		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold( true );
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('I'.$rowNumber, $grandTotalRevenueRsv);	$sheet->getStyle('I'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('I'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('J'.$rowNumber, $grandTotalRevenue);		$sheet->getStyle('J'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('J'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('L'.$rowNumber, $grandTotalPaymentMethod);	$sheet->getStyle('L'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('L'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('P'.$rowNumber, $grandTotalDriverCost);	$sheet->getStyle('P'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('P'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('Q'.$rowNumber, $grandTotalTicketCost);	$sheet->getStyle('Q'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('Q'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('R'.$rowNumber, $grandTotalCarCost);		$sheet->getStyle('R'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('R'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('S'.$rowNumber, $grandTotalCost);			$sheet->getStyle('S'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('S'.$rowNumber)->getFont()->setBold( true );
		$sheet->setCellValue('T'.$rowNumber, $grandTotalMargin);		$sheet->getStyle('T'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('T'.$rowNumber)->getFont()->setBold( true );
		
		$sheet->getStyle('A12:T'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('T'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(14);
		$sheet->getColumnDimension('B')->setWidth(10);
		$sheet->getColumnDimension('C')->setWidth(13);
		$sheet->getColumnDimension('D')->setWidth(18);
		$sheet->getColumnDimension('E')->setWidth(16);
		$sheet->getColumnDimension('F')->setWidth(8);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(18);
		$sheet->getColumnDimension('I')->setWidth(14);
		$sheet->getColumnDimension('J')->setWidth(12);
		$sheet->getColumnDimension('K')->setWidth(14);
		$sheet->getColumnDimension('L')->setWidth(12);
		$sheet->getColumnDimension('M')->setWidth(12);
		$sheet->getColumnDimension('N')->setWidth(16);
		$sheet->getColumnDimension('O')->setWidth(16);
		$sheet->getColumnDimension('P')->setWidth(12);
		$sheet->getColumnDimension('Q')->setWidth(12);
		$sheet->getColumnDimension('R')->setWidth(12);
		$sheet->getColumnDimension('S')->setWidth(12);
		$sheet->getColumnDimension('T')->setWidth(12);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelReportFinance_'.$paymentStatusStr.'_'.$rsvSourceStr.'_'.$startDateStr.'_'.$endDateStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
	
	public function uploadExcelPaymentOTA(){
		$this->load->model('MainOperation');
		
		if((($_FILES["file"]["type"] == "application/vnd.ms-excel")
			|| ($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"))
			&& ($_FILES["file"]["size"] <= 1000000)){
			if ($_FILES["file"]["error"] > 0) {
				setResponseInternalServerError(array("msg"=>"Failed to upload this file. File is broken"));
			}
			
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. This file type is not allowed (".$_FILES["file"]["type"].") or file size is too big (".$_FILES["file"]["size"].")"));
		}
		
		$dir		=	PATH_TMP_FILE;
		$extension	=	pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		$fileName	=	"PaymentOTA"."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$fileName);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "fileName"=>$fileName, "extension"=>$extension));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}
	}
	
	public function scanExcelPaymentOTA(){
		$this->load->model('MainOperation');
		$this->load->model('Finance/ModelDetailReservationPayment');
		
		$idSource			=	validatePostVar($this->postVar, 'idSource', true);
		$formatAutoPayment	=	validatePostVar($this->postVar, 'formatAutoPayment', false);
		$fileName			=	validatePostVar($this->postVar, 'fileName', true);
		$extension			=	validatePostVar($this->postVar, 'extension', true);
		$filePath			=	PATH_TMP_FILE.$fileName;
		
		$fileType			=	\PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
		$reader				=	\PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
		$spreadsheet		=	$reader->load($filePath);
		$excelData			=	$spreadsheet->getActiveSheet()->toArray();
		$totalDataProcess	=	0;
		$resScan			=	array();
		
		if(count($excelData) > 0){
			
			$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
			$userAdminName		=	$dataUserAdmin['NAME'];
			$rowNumber			=	1;
			$arrDataProccess	=	[];
			$bookingCodeTemp	=	'';
			
			try {
				foreach($excelData as $data){
					$isValidCode	=	false;
					$amountOrigin	=	$amountDiscount	=	0;
					$discountNotes	=	'';
					if($idSource == 1){
						try{
							$bookingCode	=	$data[0];
							$numberOfUnit	=	$formatAutoPayment == 3 ? $data[24] : $data[18];
							$numberOfUnit	=	is_numeric($numberOfUnit) ? $numberOfUnit * 1 : 0;
							$amountUnit		=	$formatAutoPayment == 3 ? $data[21] : $data[15];
							$amountUnit		=	is_numeric($amountUnit) ? $amountUnit * 1 : 0;
							$amount			=	intval($numberOfUnit * $amountUnit);
							$amountDiscount	=	$formatAutoPayment == 3 ? $data[25] : 0;
							$amountDiscount	=	is_numeric($amountDiscount) ? $amountDiscount * 1 : 0;
							$amountOrigin	=	intval($amount - $amountDiscount);
							$discountNotes	=	$formatAutoPayment == 3 ? $data[26] : $data[22];
							
							if(strlen($bookingCode) == 9 && ctype_alpha(substr($bookingCode, 0, 3)) && preg_match("/^\d+$/", substr($bookingCode, 3, 6))){
								$isValidCode		=	true;
								$settlementStatus	=	$data[1];
								$currency			=	"IDR";
								$bookingCodeTemp	=	$bookingCode;
							} else {
								if($amount != 0){
									$index = array_search($bookingCodeTemp, array_column($arrDataProccess, 'bookingCode'));
									if ($index !== false) {
										$arrDataProccess[$index]['amount']			+=	$amount;
										$arrDataProccess[$index]['amountOrigin']	+=	$amountOrigin;
										$arrDataProccess[$index]['amountDiscount']	+=	$amountDiscount;
										$arrDataProccess[$index]['discountNotes']	.=	$discountNotes;
									}
								}
							}
						} catch (Exception $e) {
							$this->sendErrorExcelFilePayment();
						}
					} else if($idSource == 2) {
						try{
							$bookingCodeRaw			=	$data[0];
							$bookingCode			=	"";
							if(strlen($bookingCodeRaw) >= 9 && preg_match("/^\d+$/", $bookingCodeRaw)){
								$isValidCode		=	true;
								$bookingCode		=	"BR-".$bookingCodeRaw;
								$amount				=	$formatAutoPayment == 1 ? $data[6] * 1 : $data[4] * 1;
								$settlementStatus	=	$amount <= 0 ? "Refunded" : "Paid";
								$currency			=	$formatAutoPayment == 1 ? $data[7] : $data[5];
							}
						} catch (Exception $e) {
							$this->sendErrorExcelFilePayment();
						}
					} else if($idSource == 8) {
						try{
							$bookingCode			=	$data[1];
							if(substr($bookingCode, 0, 2) == 'PG'){
								$isValidCode		=	true;
								$settlementStatus	=	$data[2] == "CONFIRMED" ? "Paid" : "Refunded";
								$amount				=	$data[7] * 1;
								$currency			=	$data[6];
							}
						} catch (Exception $e) {
							$this->sendErrorExcelFilePayment();
						}
					} else if($idSource == 24) {
						try{
							$bookingCode			=	$data[0];
							if(strlen($bookingCode) == 11 && preg_match("/^\d+$/", $bookingCode)){
								$isValidCode		=	true;
								$settlementStatus	=	"Paid";
								$amount				=	preg_replace("/[^0-9.]/", "", $data[7]) * 1;
								$currency			=	'IDR';
							}
						} catch (Exception $e) {
							$this->sendErrorExcelFilePayment();
						}
					} else if($idSource == 22) {
						try{
							$bookingCode			=	$data[3];
							if(strlen($bookingCode) == 12 && substr($bookingCode, 0, 3) == 'GYG'){
								$isValidCode		=	true;
								$settlementStatus	=	"Paid";
								$amount				=	preg_replace("/[^0-9.]/", "", $data[25]);
								$amount				=	$amount * 1;
								$currency			=	'IDR';
							}
						} catch (Exception $e) {
							$this->sendErrorExcelFilePayment();
						}
					} else if($idSource == 4) {
						try{
							$bookingCode			=	$data[3];
							$bookingCode			=	explode('-', $bookingCode);
							$bookingCode			=	end($bookingCode);
							if(strlen($bookingCode) == 5 && preg_match("/^\d+$/", $bookingCode)){
								$isValidCode		=	true;
								$cardType			=	$data[14];
								$settlementStatus	=	$data[23] == "Success" && $cardType != "" ? "Paid" : "Failed";
								$amount				=	preg_replace("/[^0-9.]/", "", $data[18]);
								$amount				=	$amount * 1;
								$currency			=	'IDR';
							}
						} catch (Exception $e) {
							$this->sendErrorExcelFilePayment();
						}
					}
					
					if($isValidCode){
						$arrDataProccess[]	=	[
							'bookingCode'		=>	$bookingCode,
							'isValidCode'		=>	$isValidCode,
							'settlementStatus'	=>	$settlementStatus,
							'currency'			=>	$currency,
							'amount'			=>	$amount,
							'amountOrigin'		=>	$amountOrigin,
							'amountDiscount'	=>	$amountDiscount,
							'discountNotes'		=>	$discountNotes
						];
					}
				}
			} catch (Exception $e) {
				$this->sendErrorExcelFilePayment();
			}
			
			$arrDataReservation		=	array();
			foreach($arrDataProccess as $dataProccess){
				$bookingCode		=	$dataProccess['bookingCode'];
				$isValidCode		=	$dataProccess['isValidCode'];
				$settlementStatus	=	$dataProccess['settlementStatus'];
				$currency			=	$dataProccess['currency'];
				$amount				=	$dataProccess['amount'];
				$amountOrigin		=	$dataProccess['amountOrigin'];
				$amountDiscount		=	$dataProccess['amountDiscount'];
				$discountNotes		=	$dataProccess['discountNotes'];
				
				if($bookingCode != "" && $bookingCode != null && $isValidCode){
					$dataReservation		=	$this->ModelDetailReservationPayment->getIdReservationByBookingCode($bookingCode, $idSource);					
					if($dataReservation){
						$arrDataReservation[]	=	$dataReservation;
						$idPaymentOTA			=	$this->ModelDetailReservationPayment->isDataPaymentOTAExist($dataReservation['IDRESERVATION'], $dataReservation['IDRESERVATIONPAYMENT'], $settlementStatus, $currency, $amountOrigin);
						$arrInsertUpdate		=	array(
							"IDRESERVATION"			=>	$dataReservation['IDRESERVATION'],
							"IDRESERVATIONPAYMENT"	=>	$dataReservation['IDRESERVATIONPAYMENT'],
							"SETTLEMENTSTATUS"		=>	$settlementStatus,
							"CURRENCY"				=>	$currency,
							"AMOUNTORIGIN"			=>	$amountOrigin,
							"AMOUNTDISCOUNT"		=>	$amountDiscount,
							"AMOUNT"				=>	$amount,
							"DISCOUNTNOTES"			=>	$discountNotes,
							"XLSFILENAME"			=>	$fileName,
							"ROWNUMBER"				=>	$rowNumber,
							"DATETIMEINPUT"			=>	date("Y-m-d H:i:s"),
							"USERINPUT"				=>	$userAdminName
						);
						if(!$idPaymentOTA) $this->MainOperation->addData("t_reservationpaymentota", $arrInsertUpdate);
						if($idPaymentOTA) $this->MainOperation->updateData("t_reservationpaymentota", $arrInsertUpdate, 'IDRESERVATIONPAYMENTOTA', $idPaymentOTA);
						
						if(array_key_exists($bookingCode, $resScan)){
							$resScan[$bookingCode]["EXCELAMOUNT"][]	=	$arrInsertUpdate;
						} else {
							$resScan[$bookingCode]					=	array_merge($dataReservation, array("EXCELAMOUNT"=>array($arrInsertUpdate)));
						}						
					}					
				}			
				$rowNumber++;			
			}
			
			if(count($resScan) > 0){
				foreach($resScan as $bookingCode=>$scanData){
					$matchStatus				=	false;
					$paymentStatus				=	0;
					$totalExcelAmount			=	$totalExcelAmountDiscount	=	$totalExcelAmountOrigin	=	0;
					$isIncludeDiscount			=	false;
					$discountNotes				=	$strPaymentStatusDiscount	=	'';
					
					foreach($resScan[$bookingCode]["EXCELAMOUNT"] as $excelAmount){
						$totalExcelAmount			+=	$excelAmount['AMOUNT'];
						$totalExcelAmountDiscount	+=	$excelAmount['AMOUNTDISCOUNT'];
						$totalExcelAmountOrigin		+=	$excelAmount['AMOUNTORIGIN'];
						$discountNotes				.=	$excelAmount['DISCOUNTNOTES'];
					}
					
					$totalExcelAmountCheck	=	$totalExcelAmount < 0 ? $totalExcelAmount * -1 : $totalExcelAmount;
					if(number_format($totalExcelAmountCheck, 2, '.', ',') == number_format($resScan[$bookingCode]['AMOUNTDB'] * 1, 2, '.', ',')){
						$matchStatus		=	true;
						$paymentStatus		=	$totalExcelAmount <= 0 ? -1 : 1;
					} else {
						if($resScan[$bookingCode]['AMOUNTDB'] == $totalExcelAmount - $totalExcelAmountDiscount){
							$matchStatus		=	true;
							$paymentStatus		=	$totalExcelAmount <= 0 ? -1 : 1;
							$isIncludeDiscount	=	true;
						} else {
							$paymentStatus		=	$totalExcelAmount == 0 && $resScan[$bookingCode]['STATUSDB'] == -1 ? -1 : 0;
						}
					}
					
					$arrUpdatePayment	=	array(
						"STATUS"		=>	$paymentStatus,
						"DATETIMEUPDATE"=>	date("Y-m-d H:i:s"),
						"USERUPDATE"	=>	$userAdminName." (Auto Upload)"
					);
					
					if($isIncludeDiscount){
						$detailReservationPayment	=	$this->ModelDetailReservationPayment->getDetailReservationPayment($resScan[$bookingCode]['IDRESERVATIONPAYMENT']);
						
						if($detailReservationPayment){
							$arrUpdatePayment['DESCRIPTION']	=	$detailReservationPayment['DESCRIPTION'].'. Origin amount : '.$totalExcelAmountOrigin.'. '.$discountNotes;
						}
						
						
						$arrUpdatePayment['AMOUNT']		=	$totalExcelAmount;
						$arrUpdatePayment['AMOUNTIDR']	=	$totalExcelAmount;
						$strPaymentStatusDiscount		=	' (Discount Included)';
					}
					
					$procUpdatePymt		=	$this->MainOperation->updateData("t_reservationpayment", $arrUpdatePayment, "IDRESERVATIONPAYMENT", $resScan[$bookingCode]['IDRESERVATIONPAYMENT']);
					$strPaymentStatus	=	"Unpaid";
					switch($paymentStatus){
						case "1"	:	$strPaymentStatus	=	"Paid".$strPaymentStatusDiscount; break;
						case "-1"	:	$strPaymentStatus	=	"Cancel/Refund"; break;
						case "0"	:	
						default		:	
										$strPaymentStatus	=	"Unpaid"; break;
					}
					
					$resScan[$bookingCode]['MATCHSTATUS']	=	$matchStatus ? "Match" : "Not Match";
					$resScan[$bookingCode]['PAYMENTSTATUS']	=	$strPaymentStatus;
					$resScan[$bookingCode]['TOTEXCELAMOUNT']=	$totalExcelAmount;
					
					if($procUpdatePymt['status']){
						$totalDataProcess++;
					} else {
						unset($resScan[$bookingCode]);
					}
				}
			}
		}
		
		if($totalDataProcess <= 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"File has been uploaded. No data match found"));
		} else {
			setResponseOk(array("token"=>$this->newToken, "msg"=>"File has been uploaded (".$totalDataProcess." records). Please check scan results", "resScan"=>$resScan));
		}
	}
	
	private function sendErrorExcelFilePayment(){
		setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed. Please make sure the uploaded file is valid and the source selection and format type are correct"));
	}
	
	private function sendCollectPaymentNotification($idPartnerType, $idVendor, $idDriver, $idCollectPayment){
		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		if(($idPartnerType == 1 && $idVendor != 0) || ($idPartnerType == 2 && $idDriver != 0)){
			$detailCollectPayment	=	$this->ModelReservation->getDetailCollectPayment($idCollectPayment);
			$idPartner				=	$idPartnerType == 2 ? $idDriver : $idVendor;
			$strPartnerType			=	$idPartnerType == 2 ? 'driver' : 'vendor';
			$dataPartner			=	$idPartnerType == 2 ? $this->MainOperation->getDataDriver($idDriver) : $this->MainOperation->getDataVendor($idVendor);
			$dataMessageType		=	$this->MainOperation->getDataMessageType(7);
			$partnerTokenFCM		=	$dataPartner['TOKENFCM'];
			$activityMessage		=	$dataMessageType['ACTIVITY'];
			
			if($detailCollectPayment){
				
				$idReservation			=	$detailCollectPayment['IDRESERVATION'];
				$customerName			=	$detailCollectPayment['CUSTOMERNAME'];
				$reservationTitle		=	$detailCollectPayment['RESERVATIONTITLE'];
				$remarkCollect			=	$detailCollectPayment['DESCRIPTION'];
				$amountCollect			=	$detailCollectPayment['AMOUNT'];
				$amountCurrency			=	$detailCollectPayment['AMOUNTCURRENCY'];
				$amountCurrencyExchange	=	$detailCollectPayment['EXCHANGECURRENCY'];
				$amountCollectIDR		=	$detailCollectPayment['AMOUNTIDR'];
				$dateCollect			=	$detailCollectPayment['DATECOLLECTSTR'];
				$dateCollectDB			=	$detailCollectPayment['DATECOLLECTDB'];
				$idReservationDetails	=	$idPartnerType == 2 ? $this->ModelReservation->getIdReservationDetailsDriver($idReservation, $idDriver, $dateCollectDB) : $this->ModelReservation->getIdReservationDetailsVendor($idReservation, $idVendor, $dateCollectDB);
				$tmToday				=	new DateTime("today");
				$tmDateCollect			=	DateTime::createFromFormat('Y-m-d', $dateCollectDB);
				$diffDays				=	$tmToday->diff($tmDateCollect);
				$diffDays				=	(integer)$diffDays->format( "%R%a" );
				$amountCollectStr		=	number_format($amountCollectIDR, 0, '.', ',')." IDR";
				$strDateCollect			=	"";
				
				if($amountCurrency != "IDR"){
					$amountCollectStr	=	number_format($amountCollect, 2, '.', ',')." ".$amountCurrency." x ".number_format($amountCurrencyExchange, 0, '.', ',')." = ".$amountCollectStr;
				}
				
				switch($diffDays) {
					case 0	:	$strDateCollect	=	" (Today)"; break;
					case +1	:	$strDateCollect	=	" (Tomorrow)"; break;
					default	:	$strDateCollect	=	""; break;
				}
				
				if($idReservationDetails){
					$titleDB			=	"Collect payment for ".$dateCollect;
					$titleMsg			=	"Collect payment for ".$dateCollect.$strDateCollect;
					$body				=	"Details Collect\n";
					$body				.=	"Reservation Detail : ".$customerName." - ".$reservationTitle."\n";
					$body				.=	"Remark : ".$remarkCollect."\n";
					$body				.=	"Amount : ".$amountCollectStr;
					$additionalArray	=	array(
												"activity"	=>	$activityMessage,
												"idPrimary"	=>	$idCollectPayment
											);
				
					$arrInsertMsg		=	array(
													"IDMESSAGEPARTNERTYPE"	=>	7,
													"IDPARTNERTYPE"			=>	$idPartnerType,
													"IDPARTNER"				=>	$idPartner,
													"IDPRIMARY"				=>	$idReservationDetails,
													"TITLE"					=>	$titleDB,
													"MESSAGE"				=>	$body,
													"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
											);
					$procInsertMsg		=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
						
					if($procInsertMsg['status']){
						if($partnerTokenFCM != "") $this->fcm->sendPushNotification($partnerTokenFCM, $titleMsg, $body, $additionalArray, true);
						if(PRODUCTION_URL){
							$RTDB_refCode			=	$dataPartner['RTDBREFCODE'];
							if($RTDB_refCode && $RTDB_refCode != ''){
								try {
									$factory			=	(new Factory)
															->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
															->withDatabaseUri(FIREBASE_RTDB_URI);
									$database			=	$factory->createDatabase();
									$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/".$strPartnerType."/".$RTDB_refCode."/activeCollectPayment");
									$referencePartnerVal=	$referencePartner->getValue();
									if($referencePartnerVal != null || !is_null($referencePartnerVal)){
										$referencePartner->update([
											'newCollectPaymentDetail'	=>  nl2br($body),
											'newCollectPaymentStatus'	=>  true,
											'timestampUpdate'			=>  gmdate('YmdHis'),
											'totalActiveCollectPayment'	=>  $this->MainOperation->getTotalActiveCollectPayment($idPartnerType, $idPartner)
										]);
									}
								} catch (Exception $e) {
								}
							}
						}
					}
				}		
			}		
		}	
		return true;						
	}
}