<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Spipu\Html2Pdf\Html2Pdf;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class Reservation extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->postVar	=	decodeJsonPost();
			$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid submission data"));
			$this->newToken	=	isLoggedIn($this->token, true);
		} else {
			$functionName	=	$this->uri->segment(2);
			if($functionName != "APISaveReservationDetails" && $functionName != "APIDeactivateReservationDetails" && $functionName != "excelDetail" && $functionName != "excelVendorBook" && $functionName != "dataDetails"){
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
	
	public function getOptionHelperReservationProduct(){
		$this->load->model('ModelReservation');
		
		$productSelfDriveVendor		=	$this->ModelReservation->getProductSelfDriveVendor();
		$productSelfDriveDuration	=	$this->ModelReservation->getProductSelfDriveDuration();
		$productTicketVendor		=	$this->ModelReservation->getProductTicketVendor();
		$productTransportVendor		=	$this->ModelReservation->getProductTransportVendor();
		
		setResponseOk(
			array(
				"token"						=>	$this->newToken,
				"productSelfDriveVendor"	=>	$productSelfDriveVendor,
				"productSelfDriveDuration"	=>	$productSelfDriveDuration,
				"productTicketVendor"		=>	$productTicketVendor,
				"productTransportVendor"	=>	$productTransportVendor
			)
		);
	}
	
	public function getDataReservation(){
		$this->load->model('ModelReservation');
		$this->load->model('MainOperation');
		
		$page					=	validatePostVar($this->postVar, 'page', true);
		$idReservation			=	validatePostVar($this->postVar, 'idReservation', false);
		$idReservationType		=	validatePostVar($this->postVar, 'idReservationType', false);
		$idReservationParam		=	$idReservation;
		$year					=	validatePostVar($this->postVar, 'year', false);
		$status					=	validatePostVar($this->postVar, 'status', false);
		$idSource				=	validatePostVar($this->postVar, 'idSource', false);
		$idPartner				=	validatePostVar($this->postVar, 'idPartner', false);
		$bookingCode			=	validatePostVar($this->postVar, 'bookingCode', false);
		$customerName			=	validatePostVar($this->postVar, 'customerName', false);
		$transportStatus		=	validatePostVar($this->postVar, 'transportStatus', false);
		$reservationTitle		=	validatePostVar($this->postVar, 'reservationTitle', false);
		$locationName			=	validatePostVar($this->postVar, 'locationName', false);
		$collectPaymentStatus	=	validatePostVar($this->postVar, 'collectPaymentStatus', false);
		$orderBy				=	validatePostVar($this->postVar, 'orderBy', true);
		$orderType				=	validatePostVar($this->postVar, 'orderType', true);
		$startDate				=	validatePostVar($this->postVar, 'startDate', false);
		$endDate				=	validatePostVar($this->postVar, 'endDate', false);
		$arrDates				=	array();
		$strArrIdReservation	=	"";
		$idVendorType			=	0;
		
		if($startDate == "" && $endDate != "") $startDate	=	$endDate;
		if($startDate != "" && $endDate == "") $endDate		=	$startDate;
		
		if($startDate != ""){
			$startDateDT	=	DateTime::createFromFormat('d-m-Y', $startDate);
			$startDateTS	=	$startDateDT->getTimestamp();
			$startDate		=	$startDateDT->format('Y-m-d');
			$endDateDT		=	DateTime::createFromFormat('d-m-Y', $endDate);
			$endDateTS		=	$endDateDT->getTimestamp();
			$endDate		=	$endDateDT->format('Y-m-d');
			$totalDays		=	$startDateDT->diff($endDateDT)->days;
			$arrDates		=	getArrDateBetween($startDateDT, $endDateDT);

			if($endDateTS < $startDateTS) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid date range selection"));
			if($totalDays > 31 && $reservationTitle == "") setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Maximum date range is <b>31</b> days"));
		}
		
		if($idPartner != "" && $startDate == "" && $endDate == "") setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select date or date range"));
		
		if($idPartner != ""){
			$expIdPartner		=	explode("-", $idPartner);
			$partnerType		=	$expIdPartner[0];
			
			if($partnerType == 1){
				$idVendorType			=	$expIdPartner[1];
				if($idVendorType == 1){
					$idVendorCar		=	$expIdPartner[2];
					$strArrIdReservation=	$this->ModelReservation->getStrArrIdReservationByIdVendorCar($idVendorCar, $startDate, $endDate);
				} else {
					$idVendorTicket		=	$expIdPartner[2];
					$strArrIdReservation=	$this->ModelReservation->getStrArrIdReservationByIdVendorTicket($idVendorTicket, $startDate, $endDate);
				}
			} else {
				$idDriver				=	$expIdPartner[1];
				$strArrIdReservation	=	$this->ModelReservation->getStrArrIdReservationByIdDriver($idDriver, $startDate, $endDate);
			}
		}
		
		$dataTable		=	$this->ModelReservation->getDataReservation($page, 25, $idReservation, $arrDates, $status, $idSource, $bookingCode, $customerName, $locationName, $startDate, $endDate, $strArrIdReservation, $transportStatus, $reservationTitle, $orderBy, $orderType, $idPartner, $idVendorType, $collectPaymentStatus, $year, $idReservationType);
		$dataExchange	=	$this->MainOperation->getDataExchangeCurrency();
		$fromNotif		=	isset($idReservation) && $idReservation != 0 ? true : false;
		$urlExcelDetail	=	$urlExcelVendorBook	=	"";
		
		if(count($dataTable['data']) > 0){
			foreach($dataTable['data'] as $keyData){
				$idReservation			=	$keyData->IDRESERVATION;
				$statusDataTable		=	$keyData->STATUS;
				
				if($statusDataTable > 0){
					$dataDriver					=	$this->ModelReservation->getReservationHandleDriver($idReservation);
					$dataVendorCar				=	$this->ModelReservation->getReservationHandleVendorCar($idReservation);
					$dataVendorTicket			=	$this->ModelReservation->getReservationHandleVendorTicket($idReservation);
					$totalVoucher				=	$this->ModelReservation->getTotalReservationVoucher($idReservation);
					$totalVoucherStatus			=	0;
					
					if(count($dataVendorTicket) > 0){
						foreach($dataVendorTicket as $vendorTicket){
							$totalVoucherStatus	+=	$vendorTicket->VOUCHERSTATUS;
						}
					}
					
					$keyData->PARTNERHANDLE		=	array_merge($dataDriver, $dataVendorCar, $dataVendorTicket);
					$keyData->TOTALVOUCHERSTATUS=	$totalVoucherStatus;
					$keyData->TOTALVOUCHER		=	$totalVoucher;
				}
			}
			
			if(count($arrDates) > 0){
				$idReservationParam	=	isset($idReservationParam) && is_array($idReservationParam) ? implode(",", $idReservationParam) : $idReservationParam;
				$urlExcelDetail		=	BASE_URL."reservation/excelDetail/".base64_encode(encodeStringKeyFunction($idReservationParam."|".$status."|".$idSource."|".$idPartner."|".$bookingCode."|".$customerName."|".$transportStatus."|".$reservationTitle."|".$locationName."|".$orderBy."|".$orderType."|".$startDate."|".$endDate."|".$collectPaymentStatus."|".$year."|".$idReservationType, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
				$urlExcelVendorBook	=	BASE_URL."reservation/excelVendorBook/".base64_encode(encodeStringKeyFunction($idReservationParam."|".$status."|".$idSource."|".$idPartner."|".$bookingCode."|".$customerName."|".$transportStatus."|".$reservationTitle."|".$locationName."|".$orderBy."|".$orderType."|".$startDate."|".$endDate."|".$collectPaymentStatus."|".$year."|".$idReservationType, DEFAULT_KEY_ENCRYPTION))."/token?token=".$this->newToken;
			}
		}
		
		$bookingCodeManual	=	$this->getBSTManualBookingCode();
		$dateToday			=	new DateTime();
		$dateToday->modify('-90 days');
		$dateLast90Days		=	$dateToday->format('Y-m-d');
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "dataExchange"=>$dataExchange, "fromNotif"=>$fromNotif, "bookingCodeManual"=>$bookingCodeManual, "dateLast90Days"=>$dateLast90Days, "urlExcelDetail"=>$urlExcelDetail, "urlExcelVendorBook"=>$urlExcelVendorBook));
	}
	
	private function getBSTManualBookingCode(){
		$this->load->model('MainOperation');
		$dateBookingCode=	date('ymd');
		$i				=	1;
		
		while(true){
			$orderNumber		=	str_pad($i, 3, "0", STR_PAD_LEFT);
			$bookingCode		=	"BST-".$dateBookingCode.$orderNumber;
			$isBookingCodeExist	=	$this->MainOperation->isBookingCodeExist($bookingCode);
			
			if(!$isBookingCodeExist) break;
			$i++;
		}
		
		return $bookingCode;
	}
	
	public function addReservation(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$idReservationType		=	validatePostVar($this->postVar, 'optionReservationTypeEditor', true);
		$idSource				=	validatePostVar($this->postVar, 'optionSourceEditor', true);
		$selfDriveStatus		=	validatePostVar($this->postVar, 'selfDriveStatus', true);
		$reservationTitle		=	trim(validatePostVar($this->postVar, 'reservationTitle', true));
		$productDetailsUrl		=	validatePostVar($this->postVar, 'productDetailsUrl', false);
		$durationOfDay			=	validatePostVar($this->postVar, 'durationOfDay', true);
		$reservationDate		=	validatePostVar($this->postVar, 'reservationDate', true);
		$reservationDateDT		=	DateTime::createFromFormat('d-m-Y', $reservationDate);
		$reservationDate		=	$reservationDateDT->format('Y-m-d');
		$reservationDateEnd		=	validatePostVar($this->postVar, 'reservationDateEnd', false);
		$reservationDateEndDT	=	isset($reservationDateEnd) && $reservationDateEnd != "" ? DateTime::createFromFormat('d-m-Y', $reservationDateEnd) : $reservationDateDT;
		$reservationDateEnd		=	isset($reservationDateEnd) && $reservationDateEnd != "" ? $reservationDateEndDT->format('Y-m-d') : $reservationDate;
		$reservationHour		=	validatePostVar($this->postVar, 'reservationHour', false);
		$reservationHourEnd		=	validatePostVar($this->postVar, 'reservationHourEnd', false);
		$reservationHourEnd		=	isset($reservationHourEnd) && $reservationHourEnd != "" ? $reservationHourEnd : $reservationHour;
		$reservationMinute		=	validatePostVar($this->postVar, 'reservationMinute', false);
		$reservationMinuteEnd	=	validatePostVar($this->postVar, 'reservationMinuteEnd', false);
		$reservationMinuteEnd	=	isset($reservationMinuteEnd) && $reservationMinuteEnd != "" ? $reservationMinuteEnd : $reservationMinute;
		$reservationTime		=	$reservationHour.":".$reservationMinute;
		$customerName			=	validatePostVar($this->postVar, 'customerName', true);
		$customerContact		=	validatePostVar($this->postVar, 'customerContact', true);
		$customerEmail			=	validatePostVar($this->postVar, 'customerEmail', false);
		$idArea					=	validatePostVar($this->postVar, 'optionPickUpArea', true);
		$hotelName				=	validatePostVar($this->postVar, 'hotelName', false);
		$pickUpLocation			=	validatePostVar($this->postVar, 'pickUpLocation', false);
		$pickUpLocationUrl		=	validatePostVar($this->postVar, 'pickUpLocationUrl', false);
		$dropOffLocation		=	validatePostVar($this->postVar, 'dropOffLocation', false);
		$numberOfAdult			=	validatePostVar($this->postVar, 'numberOfAdult', true);
		$numberOfChild			=	validatePostVar($this->postVar, 'numberOfChild', false);
		$numberOfInfant			=	validatePostVar($this->postVar, 'numberOfInfant', false);
		$bookingCode			=	validatePostVar($this->postVar, 'bookingCode', false);
		$reservationPriceType	=	validatePostVar($this->postVar, 'reservationPriceType', true);
		$reservationPriceInteger=	str_replace(",", "", validatePostVar($this->postVar, 'reservationPriceInteger', true));
		$reservationPriceDecimal=	validatePostVar($this->postVar, 'reservationPriceDecimal', false);
		$reservationPrice		=	$reservationPriceInteger.".".$reservationPriceDecimal;
		$reservationPrice		=	$reservationPrice * 1;
		$currencyExchange		=	str_replace(",", "", validatePostVar($this->postVar, 'currencyExchange', true));
		$reservationPriceIDR	=	str_replace(",", "", validatePostVar($this->postVar, 'reservationPriceIDR', true));
		$tourPlan				=	validatePostVar($this->postVar, 'tourPlan', false);
		$remark					=	validatePostVar($this->postVar, 'remark', false);
		$specialRequest			=	validatePostVar($this->postVar, 'specialRequest', false);
		
		if($reservationPrice <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please input a valid price"));
		if(isset($bookingCode) && $bookingCode != ""){
			$isBookingCodeExist	=	$this->MainOperation->isBookingCodeExist($bookingCode);
			
			if($isBookingCodeExist) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Booking code already exists. Please enter a different booking code"));
		}
		
		if($idArea == -1){
			if($hotelName != "" || $pickUpLocation != "" || $dropOffLocation != ""){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select a valid area. <b>Without Transfer</b> can only be selected if the <b>hotel, pick up and drop off location</b> are blank"));
			}
		}
		
		if($idArea != -1){
			if($hotelName == "" && $pickUpLocation == "" && $dropOffLocation == ""){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please enter one of the <b>hotel name, pick up or drop off location</b>"));
			}
		}
		
		if($selfDriveStatus == 0){
			$reservationDateEnd		=	$reservationDate;
			$reservationHourEnd		=	$reservationHour;
			$reservationMinuteEnd	=	$reservationMinute;
		} else {
			$daysDifference		=	$reservationDateDT->diff($reservationDateEndDT);
			$daysDifference		=	$daysDifference->days;
			if($daysDifference != $durationOfDay && ($daysDifference + 1) != $durationOfDay) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid reservation date period (date start and end)"));
		}
		
		$reservationTimeEnd	=	$reservationHourEnd.":".$reservationMinuteEnd;
		$currencyExchange	=	$reservationPriceType == "IDR" ? 1 : $this->MainOperation->getCurrencyExchangeByDate($reservationPriceType, $reservationDate);
		$reservationPriceIDR=	$reservationPrice * $currencyExchange;
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$dataUserAdmin['NAME'];

		if(strpos(strtolower($reservationTitle), "japan") !== false && strpos(strtolower($specialRequest), "japan") === false) $specialRequest	=	"Japanese Driver. ".$specialRequest;
		if(strpos(strtolower($reservationTitle), "chinese") !== false && strpos(strtolower($specialRequest), "chinese") === false) $specialRequest	=	"Chinese Driver. ".$specialRequest;
		if(strpos(strtolower($reservationTitle), "comfort") !== false && strpos(strtolower($specialRequest), "comfort") === false) $specialRequest	=	"Comfort Car. ".$specialRequest;

		$arrInsertRsv		=	array(
			"IDRESERVATIONTYPE"		=>	$idReservationType,
			"IDSOURCE"				=>	$idSource,
			"IDAREA"				=>	$idArea,
			"INPUTTYPE"				=>	2,
			"RESERVATIONTITLE"		=>	$reservationTitle,
			"DURATIONOFDAY"			=>	$durationOfDay,
			"RESERVATIONDATESTART"	=>	$reservationDate,
			"RESERVATIONDATEEND"	=>	$reservationDateEnd,
			"RESERVATIONTIMESTART"	=>	$reservationTime,
			"RESERVATIONTIMEEND"	=>	$reservationTimeEnd,
			"CUSTOMERNAME"			=>	$customerName,
			"CUSTOMERCONTACT"		=>	$customerContact,
			"CUSTOMEREMAIL"			=>	$customerEmail,
			"HOTELNAME"				=>	$hotelName,
			"PICKUPLOCATION"		=>	$pickUpLocation,
			"DROPOFFLOCATION"		=>	$dropOffLocation,
			"NUMBEROFADULT"			=>	$numberOfAdult,
			"NUMBEROFCHILD"			=>	$numberOfChild,
			"NUMBEROFINFANT"		=>	$numberOfInfant,
			"BOOKINGCODE"			=>	$bookingCode,
			"INCOMEAMOUNTCURRENCY"	=>	$reservationPriceType,
			"INCOMEAMOUNT"			=>	$reservationPrice,
			"INCOMEEXCHANGECURRENCY"=>	$currencyExchange,
			"INCOMEAMOUNTIDR"		=>	$reservationPriceIDR,
			"REMARK"				=>	$remark,
			"TOURPLAN"				=>	$tourPlan,
			"SPECIALREQUEST"		=>	$specialRequest,
			"URLDETAILPRODUCT"		=>	$productDetailsUrl,
			"URLPICKUPLOCATION"		=>	$pickUpLocationUrl,
			"STATUS"				=>	0,
			"ISSELFDRIVE"			=>	$selfDriveStatus,
			"USERINPUT"				=>	$userAdminName,
			"DATETIMEINPUT"			=>	date('Y-m-d H:i:s')
		);

		if($durationOfDay > 1 && $selfDriveStatus == 0){
			$additionalDays						=	$durationOfDay - 1;
			$dateEnd							=	date('Y-m-d', strtotime($reservationDate. ' + '.$additionalDays.' days'));
			$arrInsertRsv['RESERVATIONDATEEND']	=	$dateEnd;
		}

		$procInsertRsv	=	$this->MainOperation->addData('t_reservation', $arrInsertRsv);
		if(!$procInsertRsv['status']) switchMySQLErrorCode($procInsertRsv['errCode'], $this->newToken);
		$idReservation	=	$procInsertRsv['insertID'];
		$dataExchange	=	$this->MainOperation->getDataExchangeCurrency();
		$this->updateWebappStatisticTags();
		$this->pushApiScanCustomerContact($idReservation);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New reservation has been added", "dataExchange"=>$dataExchange));
	}
	
	private function checkInputData(){
		$arrVarValidate	=	array(
			array("reservationTitle","text","Reservation Title"),
			array("durationOfDay","text","Reservation Duration"),
			array("reservationDate","text","Reservation Date"),
			array("customerName","text","Customer Name"),
			array("customerContact","text","Customer Contact"),
			array("optionSourceEditor","option","Source"),
			array("optionPickUpArea","option","Pick Up Area"),
			array("numberOfAdult","text","Number of Adult Customer (at least one)"),
			array("reservationPriceInteger","text","Reservation Price")
		);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;
	}
	
	private function pushApiScanCustomerContact($idReservation){
		$arrData					=	["idReservation" => $idReservation];
		$base64JsonData				=	base64_encode(json_encode($arrData));
		$urlApiScanCustomerContact	=	BASE_URL."cron/apiScanCustomerContact/".$base64JsonData;
		
		try {
			json_decode(trim(curl_get_file_contents($urlApiScanCustomerContact)));
		} catch(Exception $e) {
		}
		return true;
	}
	
	public function getDetailReservation(){
		$this->load->model('ModelReservation');
		
		$idReservation	=	validatePostVar($this->postVar, 'idReservation', true);
		$detailData		=	$this->ModelReservation->getDetailReservation($idReservation);
		
		if(!$detailData) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found"));
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$detailData));	
	}
	
	public function updateReservation(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$idReservation			=	validatePostVar($this->postVar, 'idReservationEditor', true);
		$idReservationType		=	validatePostVar($this->postVar, 'optionReservationTypeEditor', true);
		$idSource				=	validatePostVar($this->postVar, 'optionSourceEditor', true);
		$selfDriveStatus		=	validatePostVar($this->postVar, 'selfDriveStatus', true);
		$reservationTitle		=	trim(validatePostVar($this->postVar, 'reservationTitle', true));
		$productDetailsUrl		=	validatePostVar($this->postVar, 'productDetailsUrl', false);
		$durationOfDay			=	validatePostVar($this->postVar, 'durationOfDay', true);
		$reservationDate		=	validatePostVar($this->postVar, 'reservationDate', true);
		$reservationDateDT		=	DateTime::createFromFormat('d-m-Y', $reservationDate);
		$reservationDate		=	$reservationDateDT->format('Y-m-d');
		$reservationDateEnd		=	validatePostVar($this->postVar, 'reservationDateEnd', false);
		$reservationDateEndDT	=	isset($reservationDateEnd) && $reservationDateEnd != "" ? DateTime::createFromFormat('d-m-Y', $reservationDateEnd) : $reservationDateDT;
		$reservationDateEnd		=	isset($reservationDateEnd) && $reservationDateEnd != "" ? $reservationDateEndDT->format('Y-m-d') : $reservationDate;
		$reservationHour		=	validatePostVar($this->postVar, 'reservationHour', true);
		$reservationHourEnd		=	validatePostVar($this->postVar, 'reservationHourEnd', false);
		$reservationHourEnd		=	isset($reservationHourEnd) && $reservationHourEnd != "" ? $reservationHourEnd : $reservationHour;
		$reservationMinute		=	validatePostVar($this->postVar, 'reservationMinute', true);
		$reservationMinuteEnd	=	validatePostVar($this->postVar, 'reservationMinuteEnd', false);
		$reservationMinuteEnd	=	isset($reservationMinuteEnd) && $reservationMinuteEnd != "" ? $reservationMinuteEnd : $reservationMinute;
		$reservationTime		=	$reservationHour.":".$reservationMinute;
		$customerName			=	validatePostVar($this->postVar, 'customerName', true);
		$customerContact		=	validatePostVar($this->postVar, 'customerContact', true);
		$customerEmail			=	validatePostVar($this->postVar, 'customerEmail', false);
		$idArea					=	validatePostVar($this->postVar, 'optionPickUpArea', true);
		$hotelName				=	validatePostVar($this->postVar, 'hotelName', false);
		$pickUpLocation			=	validatePostVar($this->postVar, 'pickUpLocation', false);
		$pickUpLocationUrl		=	validatePostVar($this->postVar, 'pickUpLocationUrl', false);
		$dropOffLocation		=	validatePostVar($this->postVar, 'dropOffLocation', false);
		$numberOfAdult			=	validatePostVar($this->postVar, 'numberOfAdult', true);
		$numberOfChild			=	validatePostVar($this->postVar, 'numberOfChild', false);
		$numberOfInfant			=	validatePostVar($this->postVar, 'numberOfInfant', false);
		$totalPax				=	$numberOfAdult + $numberOfChild + $numberOfInfant;
		$bookingCode			=	validatePostVar($this->postVar, 'bookingCode', false);
		$reservationPriceType	=	validatePostVar($this->postVar, 'reservationPriceType', true);
		$reservationPriceInteger=	str_replace(",", "", validatePostVar($this->postVar, 'reservationPriceInteger', true));
		$reservationPriceDecimal=	validatePostVar($this->postVar, 'reservationPriceDecimal', false);
		$reservationPrice		=	$reservationPriceInteger.".".$reservationPriceDecimal;
		$reservationPrice		=	$reservationPrice * 1;
		$currencyExchange		=	str_replace(",", "", validatePostVar($this->postVar, 'currencyExchange', true));
		$reservationPriceIDR	=	str_replace(",", "", validatePostVar($this->postVar, 'reservationPriceIDR', true));
		$tourPlan				=	validatePostVar($this->postVar, 'tourPlan', false);
		$remark					=	validatePostVar($this->postVar, 'remark', false);
		$specialRequest			=	validatePostVar($this->postVar, 'specialRequest', false);
		$reservationStatus		=	validatePostVar($this->postVar, 'reservationStatusEditor', false);
		$refundType				=	validatePostVar($this->postVar, 'refundTypeEditor', false);
		
		if($reservationPrice <= 0 && $refundType != -1) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please enter a valid price if the <b>payment status is not full refund</b>"));
		if(isset($bookingCode) && $bookingCode != ""){
			$isBookingCodeExist	=	$this->MainOperation->isBookingCodeExist($bookingCode, $idReservation);
			
			if($isBookingCodeExist){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Booking code already exists. Please enter a different booking code"));
			}
		}
		
		if($idArea == -1){
			if($hotelName != "" || $pickUpLocation != "" || $dropOffLocation != ""){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select a valid area!<br/><br/> <b>Without Transfer</b> can only be selected if the <b>hotel, pick up and drop off location</b> are blank"));
			}
		}
		
		if($idArea != -1){
			if($hotelName == "" && $pickUpLocation == "" && $dropOffLocation == ""){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please enter one of the <b>hotel name, pick up or drop off location</b>"));
			}
		}
		
		if($selfDriveStatus == 0){
			$reservationDateEnd		=	$reservationDate;
			$reservationHourEnd		=	$reservationHour;
			$reservationMinuteEnd	=	$reservationMinute;
		} else {
			$daysDifference		=	$reservationDateDT->diff($reservationDateEndDT);
			$daysDifference		=	$daysDifference->days;
			if($daysDifference != $durationOfDay && ($daysDifference + 1) != $durationOfDay) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid reservation date period (date start and end)"));
		}
		
		$reservationTimeEnd	=	$reservationHourEnd.":".$reservationMinuteEnd;		
		$currencyExchange	=	$reservationPriceType == "IDR" ? 1 : $this->MainOperation->getCurrencyExchangeByDate($reservationPriceType, $reservationDate);
		$reservationPriceIDR=	$reservationPrice * $currencyExchange;
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$dataUserAdmin['NAME'];

		if(strpos(strtolower($reservationTitle), "japan") !== false && strpos(strtolower($specialRequest), "japan") === false) $specialRequest	=	"Japanese Driver. ".$specialRequest;
		if(strpos(strtolower($reservationTitle), "chinese") !== false && strpos(strtolower($specialRequest), "chinese") === false) $specialRequest	=	"Chinese Driver. ".$specialRequest;

		$arrUpdateRsv		=	array(
			"IDRESERVATIONTYPE"		=>	$idReservationType,
			"IDSOURCE"				=>	$idSource,
			"IDAREA"				=>	$idArea,
			"RESERVATIONTITLE"		=>	$reservationTitle,
			"DURATIONOFDAY"			=>	$durationOfDay,
			"RESERVATIONDATESTART"	=>	$reservationDate,
			"RESERVATIONDATEEND"	=>	$reservationDateEnd,
			"RESERVATIONTIMESTART"	=>	$reservationTime,
			"RESERVATIONTIMEEND"	=>	$reservationTimeEnd,
			"CUSTOMERNAME"			=>	$customerName,
			"CUSTOMERCONTACT"		=>	$customerContact,
			"CUSTOMEREMAIL"			=>	$customerEmail,
			"HOTELNAME"				=>	$hotelName,
			"PICKUPLOCATION"		=>	$pickUpLocation,
			"DROPOFFLOCATION"		=>	$dropOffLocation,
			"NUMBEROFADULT"			=>	$numberOfAdult,
			"NUMBEROFCHILD"			=>	$numberOfChild,
			"NUMBEROFINFANT"		=>	$numberOfInfant,
			"BOOKINGCODE"			=>	$bookingCode,
			"INCOMEAMOUNTCURRENCY"	=>	$reservationPriceType,
			"INCOMEAMOUNT"			=>	$reservationPrice,
			"INCOMEEXCHANGECURRENCY"=>	$currencyExchange,
			"INCOMEAMOUNTIDR"		=>	$reservationPriceIDR,
			"REMARK"				=>	$remark,
			"TOURPLAN"				=>	$tourPlan,
			"SPECIALREQUEST"		=>	$specialRequest,
			"URLDETAILPRODUCT"		=>	$productDetailsUrl,
			"URLPICKUPLOCATION"		=>	$pickUpLocationUrl,
			"ISSELFDRIVE"			=>	$selfDriveStatus,
			"USERLASTUPDATE"		=>	$userAdminName,
			"DATETIMELASTUPDATE"	=>	date('Y-m-d H:i:s')
		);

		$dateEnd				=	$reservationDate;
		if($durationOfDay > 1 && $selfDriveStatus == 0){
			$additionalDays						=	$durationOfDay - 1;
			$dateEnd							=	date('Y-m-d', strtotime($reservationDate. ' + '.$additionalDays.' days'));
			$arrUpdateRsv['RESERVATIONDATEEND']	=	$dateEnd;
		} else {
			$dateEnd	=	$reservationDateEnd;
		}
		
		$detailSource			=	$this->MainOperation->getSourceDetailById($idSource);
		$upsellingType			=	$detailSource['UPSELLINGTYPE'];
		$dataDateReservation	=	$this->ModelReservation->getDataDateReservation($idReservation);
		$dateStartReservation	=	$dataDateReservation['RESERVATIONDATESTART'];
		$dateEndReservation		=	$dataDateReservation['RESERVATIONDATEEND'];
		$totalDetails			=	$dataDateReservation['TOTALDETAILS'];
		$oldIdArea				=	$dataDateReservation['IDAREA'];
		
		if(($dateStartReservation != $reservationDate || $dateEndReservation != $dateEnd) && $totalDetails > 0){
			setResponseForbidden(
				array(
					"token"=>$this->newToken,
					"msg"=>"Please remove all reservation/cost details before changing reservation date"
				)
			);
		}
		
		if($oldIdArea != $idArea && $oldIdArea != 0 && $totalDetails > 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please remove all reservation/cost details before changing <b>pick up area</b>"));
		}
		
		$this->MainOperation->updateData('t_reservationdetails', array("SCHEDULETYPE"=>1), array("IDRESERVATION" => $idReservation, "IDPRODUCTTYPE"=>2));		
		if($totalDetails > 0){
			if((isset($specialRequest) && $specialRequest != "" && $specialRequest != "-") || $durationOfDay > 1 || $totalPax > 6 || $upsellingType == 1){
				$this->MainOperation->updateData('t_reservationdetails', array("SCHEDULETYPE"=>2), array("IDRESERVATION" => $idReservation, "IDPRODUCTTYPE"=>2));
			}
		}
		
		$procUpdateRsv	=	$this->MainOperation->updateData('t_reservation', $arrUpdateRsv, "IDRESERVATION", $idReservation);
		
		if(!$procUpdateRsv['status']) switchMySQLErrorCode($procUpdateRsv['errCode'], $this->newToken);

		$dataExchange	=	$this->MainOperation->getDataExchangeCurrency();
		$this->updateWebappStatisticTags();
		$this->processReservationMailRating($idReservation);
		$this->pushApiScanCustomerContact($idReservation);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Reservation data has been updated", "dataExchange"=>$dataExchange));
	}
	
	public function getDetailReservationDetails(){
		$this->load->model('ModelReservation');
		
		$idReservation	=	validatePostVar($this->postVar, 'idReservation', true);
		$detailData		=	$this->ModelReservation->getDetailStrReservation($idReservation);
		
		if(!$detailData) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found, please refresh your data"));

		$reservationDetails	=	$this->ModelReservation->getListReservationDetails($idReservation);
		$durationOfDay		=	$detailData['DURATIONOFDAY'];
		$dateStartSchedule	=	$detailData['RESERVATIONDATEVALUE'];
		$selfDriveStatus	=	$detailData['ISSELFDRIVE'];
		$arrDateSchedule	=	array();
		$nDateSchedule		=	false;
		$differenceHoursOnly=	0;	
		
		if($selfDriveStatus){
			$reservationDateStart		=	$detailData['RESERVATIONDATEVALUE'];
			$reservationTimeStart		=	$detailData['RESERVATIONTIMESTART'];
			$reservationDateEnd			=	$detailData['RESERVATIONDATEENDVALUE'];
			$reservationTimeEnd			=	$detailData['RESERVATIONTIMEEND'];
			$reservationDateTimeStart	=	new DateTime($reservationDateStart.' '.$reservationTimeStart);
			$reservationDateTimeEnd		=	new DateTime($reservationDateEnd.' '.$reservationTimeEnd);
			$intervalDateTime			=	$reservationDateTimeStart->diff($reservationDateTimeEnd);
			$differenceInHours			=	($intervalDateTime->days * 24) + $intervalDateTime->h + ($intervalDateTime->i / 60);
			$differenceHoursOnly		=	$intervalDateTime->h;
			$durationOfDay				=	ceil($differenceInHours / 24);
		}
		
		for($iDay=0; $iDay<$durationOfDay; $iDay++){
			$dateSchedule		=	date('Y-m-d', strtotime($dateStartSchedule . ' +'.$iDay.' day'));
			$dateScheduleStr	=	date('d M Y', strtotime($dateStartSchedule . ' +'.$iDay.' day'));
			$arrDateSchedule[]	=	array($dateSchedule, $dateScheduleStr);
			$nDateSchedule		=	date('N', strtotime($dateStartSchedule));
		}

		$reservationTitle		=	trim($detailData['RESERVATIONTITLE']);
		$hotelName				=	trim($detailData['HOTELNAME']);
		$pickupLocation			=	trim($detailData['PICKUPLOCATION']);
		$isAutoCostKeywordExist	=	$this->ModelReservation->isAutoCostKeywordExist($reservationTitle);
		$idAutoCostKeyword		=	$isAutoCostKeywordExist != false ? $isAutoCostKeywordExist : false;
		$isAutoCostKeywordExist	=	$isAutoCostKeywordExist != false ? true : false;
		$listAutoCostTemplate	=	$this->ModelReservation->getListAutoCostTemplate();
		$titleMessageReturn		=	$warningMessageReturn	=	'';
		$arrSpecialCaseCost		=	json_decode(SEPCIAL_CASE_COST_RULES);

		foreach($arrSpecialCaseCost as $keySpecialCaseCost){
			$title			=	$keySpecialCaseCost->title;
			$warningMessage	=	$keySpecialCaseCost->warningMessage;
			$rules			=	$keySpecialCaseCost->rules;
			$minTotalPoint	=	$keySpecialCaseCost->minTotalPoint;
			$totalPoint		=	0;
			
			foreach($rules as $rule){
				$fields		=	$rule->fields;
				$condition	=	$rule->condition;
				$isFulfilled=	false;
				
				foreach($fields as $field){
					switch($field){
						case 'NRESERVATIONDATESTART'	:	
							switch($condition){
								case 'in_array'			:
									if(in_array($nDateSchedule, $rule->days)) $totalPoint++;
									break;
							}
							break;
						case 'RESERVATIONTITLE'			:	
							switch($condition){
								case 'include_string'	:
									foreach($rule->strings as $string){
										if(strpos(strtolower(str_replace(' ', '', $reservationTitle)), strtolower($string)) !== false) $isFulfilled	=	true;										
									}
									break;
							}
							break;
						case 'HOTELNAME'				:	
							switch($condition){
								case 'include_string'	:
									foreach($rule->strings as $string){
										if(strpos(strtolower(str_replace(' ', '', $hotelName)), strtolower($string)) !== false) $isFulfilled	=	true;										
									}
									break;
							}
							break;
						case 'PICKUPLOCATION'			:	
							switch($condition){
								case 'include_string'	:
									foreach($rule->strings as $string){
										if(strpos(strtolower(str_replace(' ', '', $pickupLocation)), strtolower($string)) !== false) $isFulfilled	=	true;										
									}
									break;
							}
							break;
					}
				}
				
				if($isFulfilled) $totalPoint++;
			}
			
			if($totalPoint >= $minTotalPoint && !in_array($idAutoCostKeyword, [233, 244])){
				$titleMessageReturn		=	$title;
				$warningMessageReturn	=	$warningMessage;
				break;
			}
		}
		
		setResponseOk(
			array(
				"token"					=>	$this->newToken,
				"detailData"			=>	$detailData,
				"reservationDetails"	=>	$reservationDetails,
				"arrDateSchedule"		=>	$arrDateSchedule,
				"differenceHoursOnly"	=>	$differenceHoursOnly,
				"isAutoCostKeywordExist"=>	$isAutoCostKeywordExist,
				"listAutoCostTemplate"	=>	$listAutoCostTemplate,
				"titleMessage"			=>	$titleMessageReturn,
				"warningMessage"		=>	$warningMessageReturn
			)
		);
	}
	
	public function addReconfirmationAdditionalInfo(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$idReservation		=	validatePostVar($this->postVar, 'idReservation', true);
		$textLinkType		=	validatePostVar($this->postVar, 'textLinkType', true);
		$description		=	validatePostVar($this->postVar, 'description', true);
		$informationContent	=	validatePostVar($this->postVar, 'informationContent', true);
		$detailReservation	=	$this->ModelReservation->getDetailReservation($idReservation);
		
		if(!$detailReservation) setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed to save additional info. Please try again later"));
		$additionalInfoList	=	$detailReservation['ADDITIONALINFOLIST'];
		$additionalInfoList	=	$additionalInfoList == "" ? [] : json_decode($additionalInfoList);
		
		if(count($additionalInfoList) > 0){
			foreach($additionalInfoList as $keyAdditionalInfo){
				$descriptionDB	=	$keyAdditionalInfo[0];
				if($descriptionDB == $description) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"The description you entered already exists. Please enter another description"));
			}
		}
		
		if($textLinkType == 2){
			if (!filter_var($informationContent, FILTER_VALIDATE_URL)) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"The URL/link address you entered is invalid"));
		}
		
		$informationContent		=	$textLinkType == 1 ? $informationContent : '<a href="'.$informationContent.'" target="_blank">Click Here</a>';
		$additionalInfoList[]	=	[$description, $informationContent];
			
		$this->MainOperation->updateData(
			't_reservation',
			['ADDITIONALINFOLIST'	=>	json_encode($additionalInfoList)],
			'IDRESERVATION',
			$idReservation
		);
	
		setResponseOk(
			array(
				"token"	=>	$this->newToken,
				"msg"	=>	'New additional info has been added'
			)
		);
	}
	
	public function deleteAdditionalInformation(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$idReservation		=	validatePostVar($this->postVar, 'idReservation', true);
		$description		=	validatePostVar($this->postVar, 'informationDescription', true);
		$detailReservation	=	$this->ModelReservation->getDetailStrReservation($idReservation);
		
		if(!$detailReservation) setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed to delete additional info. Please try again later"));
		$additionalInfoList		=	json_decode($detailReservation['ADDITIONALINFOLIST']);
		$idxFoundAdditionalInfo	=	false;
		$additionalInfoListNew	=	[];

		foreach($additionalInfoList as $keyAdditionalInfo){
			$descriptionDB	=	$keyAdditionalInfo[0];
			if($descriptionDB != $description) {
				$additionalInfoListNew[]	=	[
					$descriptionDB,
					$keyAdditionalInfo[1]
				];
			}
		}
		
		$additionalInfoListNew	=	count($additionalInfoListNew) <= 0 ? null : json_encode($additionalInfoListNew);
		$this->MainOperation->updateData(
			't_reservation',
			['ADDITIONALINFOLIST'	=>	$additionalInfoListNew],
			'IDRESERVATION',
			$idReservation
		);
	
		setResponseOk(
			array(
				"token"	=>	$this->newToken,
				"msg"	=>	'Additional info has been deleted'
			)
		);
	}
	
	public function addKeywordAutoCost(){
		$this->load->model('MainOperation');
		$idAutoDetailsTemplate		=	validatePostVar($this->postVar, 'idAutoDetailsTemplate', true);
		$reservationTitle			=	validatePostVar($this->postVar, 'reservationTitle', true);
		
		$arrInsertKeywordAutoCost	=	array(
			"IDAUTODETAILSTEMPLATE"	=>	$idAutoDetailsTemplate,
			"TITLEKEYWORD"			=>	$reservationTitle
		);
		$procInsert					=	$this->MainOperation->addData('t_autodetailstitlekeyword', $arrInsertKeywordAutoCost);
		
		if(!$procInsert['status']) switchMySQLErrorCode($procInsert['errCode'], $this->newToken);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New keyword has been added"));
	}
	
	public function autoAddReservationDetails(){
		$this->load->model('ModelReservation');
		
		$idReservation			=	validatePostVar($this->postVar, 'idReservation', true);
		$userAdminName			=	validatePostVar($this->postVar, 'NAME', true);
		$reservationTitle		=	validatePostVar($this->postVar, 'reservationTitle', true);
		$reservationTitle		=	html_entity_decode(preg_replace('/\s+/', ' ', $reservationTitle));
		$customerName			=	validatePostVar($this->postVar, 'customerName', true);
		$idArea					=	validatePostVar($this->postVar, 'idArea', true);
		$idSource				=	validatePostVar($this->postVar, 'idSource', true);
		$arrDateSchedule		=	validatePostVar($this->postVar, 'arrDateSchedule', true);
		$arrDateSchedule		=	json_decode($arrDateSchedule);
		$areaName				=	validatePostVar($this->postVar, 'areaName', true);
		$totalPaxAdult			=	validatePostVar($this->postVar, 'totalPaxAdult', true);
		$totalPaxChild			=	validatePostVar($this->postVar, 'totalPaxChild', true);
		$totalPaxInfant			=	validatePostVar($this->postVar, 'totalPaxInfant', true);
		$totalPax				=	$totalPaxAdult + $totalPaxChild + $totalPaxInfant;
		$totalDate				=	count($arrDateSchedule);
		$arrResultInsert		=	array();
		$totalDataProcess		=	0;
		$idAutoDetailsTemplate	=	$this->ModelReservation->getIdAutoDetailsTemplate($reservationTitle);
		$detailReservation		=	$this->ModelReservation->getDetailReservation($idReservation);
		
		if($detailReservation['STATUS'] == -1) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Automatic cost details is not possible for this reservation (Status Cancel) [E000]"));
		if(!$idAutoDetailsTemplate) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Automatic cost details is not possible for this reservation [E001]"));
		
		$itemAutoDetailsTemplate=	$this->ModelReservation->getItemAutoDetailsTemplate($idAutoDetailsTemplate);
		
		if(!$itemAutoDetailsTemplate) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Automatic cost details is not possible for this reservation [E002]"));
		if($idArea == 0 || $idArea == "0") setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Automatic costs cannot be processed because the area has not been determined [E003]"));

		$isSpecialSource	=	false;
		foreach($itemAutoDetailsTemplate as $itemAutoDetails){
			$idProductType	=	$itemAutoDetails->IDPRODUCTTYPE;
			
			if($idProductType == 2){
				$idProductFee		=	$itemAutoDetails->IDPRODUCTFEE;
				$detailCostTransport=	$this->ModelReservation->getDetailProductTransportVendor($idProductFee);
				
				if($detailCostTransport){
					$idSourceProduct	=	$detailCostTransport['IDSOURCE'];
					if($idSourceProduct == $idSource) $isSpecialSource	=	true;
				}
			}
		}
									
		foreach($itemAutoDetailsTemplate as $itemAutoDetails){
			$idProductType			=	$itemAutoDetails->IDPRODUCTTYPE;
			$idProductFee			=	$itemAutoDetails->IDPRODUCTFEE;
			$arrReservationDetails	=	array(
				"userAdminName"		=>	$userAdminName,
				"customerName"		=>	$customerName,
				"idReservation"		=>	$idReservation,
				"arrDateSchedule"	=>	$arrDateSchedule,
				"voucherStatus"		=>	0,
				"productName"		=>	"",
				"durationSelfDrive"	=>	0,
				"idCarType"			=>	0,
				"idDriverType"		=>	0,
				"idVendor"			=>	0,
				"nominalCost"		=>	0,
				"notes"				=>	"",
				"jobType"			=>	3,
				"jobRate"			=>	1,
				"productRank"		=>	999,
				"scheduleType"		=>	1,
			);
			
			if($idProductType == 2 && $idArea != -1){
				$detailCostTransport	=	$this->ModelReservation->getDetailProductTransportVendor($idProductFee);
				$totalProductTransport	=	ceil($totalPax / 6);
				
				if($detailCostTransport){
					$idAreaProduct			=	$detailCostTransport['IDAREA'];
					$idSourceProduct		=	$detailCostTransport['IDSOURCE'];
					$driverFee				=	$detailCostTransport['FEENOMINAL'];
					$costTicketType			=	$detailCostTransport['COSTTICKETTYPE'];
					$costParkingType		=	$detailCostTransport['COSTPARKINGTYPE'];
					$costMineralWaterType	=	$detailCostTransport['COSTMINERALWATERTYPE'];
					$costBreakfastType		=	$detailCostTransport['COSTBREAKFASTTYPE'];
					$costLunchType			=	$detailCostTransport['COSTLUNCHTYPE'];
					$bonusType				=	$detailCostTransport['BONUSTYPE'];
					$nominalCostTicket		=	$detailCostTransport['COSTTICKET'];
					$nominalCostParking		=	$detailCostTransport['COSTPARKING'];
					$nominalCostMineralWater=	$detailCostTransport['COSTMINERALWATER'];
					$nominalCostBreakfast	=	$detailCostTransport['COSTBREAKFAST'];
					$nominalCostLunch		=	$detailCostTransport['COSTLUNCH'];
					$nominalBonus			=	$detailCostTransport['BONUS'];
					$additionalInfo			=	$detailCostTransport['ADDITIONALINFO'];
					$nominalCost			=	$driverFee;
					$notesDriverFee			=	"Driver fee : ".number_format($driverFee, 0, '.', ',');
					$notes					=	$additionalInfo != "" ? $additionalInfo."\n\n".$notesDriverFee : $notesDriverFee;
					
					if($idAreaProduct == 0 || $idAreaProduct == $idArea){
						if(($isSpecialSource && $idSourceProduct == $idSource) || (!$isSpecialSource && $idSourceProduct == 0)){
							if($nominalCostTicket > 0){
								if($costTicketType == 1){
									$totalCostTicket	=	$nominalCostTicket;
								} else {
									$totalCostTicket	=	$totalPax * $nominalCostTicket;
								}
								
								$strCostTicketType		=	$costTicketType == 1 ? "Fixed" : "Per Pax";
								$nominalCost			+=	$totalCostTicket;
								$notesTicket			=	"Ticket : ".number_format($nominalCostTicket, 0, '.', ',')." (".$strCostTicketType.")";
								$notes					.=	$notes == "" ? $notesTicket : "\n".$notesTicket;
							}
							
							if($nominalCostParking > 0){
								if($costParkingType == 1){
									$totalCostParking	=	$nominalCostParking;
								} else {
									$totalCostParking	=	$totalPax * $nominalCostParking;
								}
								
								$strCostParkingType		=	$costParkingType == 1 ? "Fixed" : "Per Pax";
								$nominalCost			+=	$totalCostParking;
								$notesParking			=	"Parking : ".number_format($nominalCostParking, 0, '.', ',')." (".$strCostParkingType.")";
								$notes					.=	$notes == "" ? $notesParking : "\n".$notesParking;
							}
							
							if($nominalCostMineralWater > 0){
								if($costMineralWaterType == 1){
									$totalCostMineralWater	=	$nominalCostMineralWater;
								} else {
									$totalCostMineralWater	=	$totalPax * $nominalCostMineralWater;
								}
								
								$strCostMineralWaterType=	$costMineralWaterType == 1 ? "Fixed" : "Per Pax";
								$nominalCost			+=	$totalCostMineralWater;
								$notesMineralWater		=	"Mineral Water : ".number_format($nominalCostMineralWater, 0, '.', ',')." (".$strCostMineralWaterType.")";
								$notes					.=	$notes == "" ? $notesMineralWater : "\n".$notesMineralWater;
							}
							
							if($nominalCostBreakfast > 0){
								if($costBreakfastType == 1){
									$totalCostBreakfast	=	$nominalCostBreakfast;
								} else {
									$totalCostBreakfast	=	$totalPax * $nominalCostBreakfast;
								}
								
								$strCostBreakfastType	=	$costBreakfastType == 1 ? "Fixed" : "Per Pax";
								$nominalCost			+=	$totalCostBreakfast;
								$notesBreakfast			=	"Breakfast : ".number_format($nominalCostBreakfast, 0, '.', ',')." (".$strCostBreakfastType.")";
								$notes					.=	$notes == "" ? $notesBreakfast : "\n".$notesBreakfast;
							}
							
							if($nominalCostLunch > 0){
								if($costLunchType == 1){
									$totalCostLunch		=	$nominalCostLunch;
								} else {
									$totalCostLunch		=	$totalPax * $nominalCostLunch;
								}
								
								$strCostLunchType		=	$costLunchType == 1 ? "Fixed" : "Per Pax";
								$nominalCost			+=	$totalCostLunch;
								$notesLunch				=	"Lunch : ".number_format($nominalCostLunch, 0, '.', ',')." (".$strCostLunchType.")";
								$notes					.=	$notes == "" ? $notesLunch : "\n".$notesLunch;
							}
							
							if($nominalBonus > 0){
								if($bonusType == 1){
									$totalBonus		=	$nominalBonus;
								} else {
									$totalBonus		=	$totalPax * $nominalBonus;
								}
								
								$strBonusType	=	$costLunchType == 1 ? "Fixed" : "Per Pax";
								$nominalCost	+=	$totalBonus;
								$notesBonus		=	"Bonus : ".number_format($nominalBonus, 0, '.', ',')." (".$strBonusType.")";
								$notes			.=	$notes == "" ? $notesBonus : "\n".$notesBonus;
							}
							
							$arrReservationDetails['idProductType']	=	$idProductType;
							$arrReservationDetails['productName']	=	$detailCostTransport['PRODUCTNAME'];
							$arrReservationDetails['vendorName']	=	$detailCostTransport['VENDORNAME'];
							$arrReservationDetails['idDriverType']	=	$detailCostTransport['IDDRIVERTYPE'];
							$arrReservationDetails['scheduleType']	=	$detailCostTransport['SCHEDULETYPE'];
							$arrReservationDetails['jobType']		=	$detailCostTransport['JOBTYPE'];
							$arrReservationDetails['jobRate']		=	$detailCostTransport['JOBRATE'];
							$arrReservationDetails['productRank']	=	$detailCostTransport['PRODUCTRANK'];
							$arrReservationDetails['nominalCost']	=	$nominalCost;
							$arrReservationDetails['notes']			=	$notes;
							
							for($i=1; $i<=$totalProductTransport; $i++){
								$resultInsert	=	$this->saveReservationDetails($arrReservationDetails);
								if($resultInsert){
									$totalDataProcess	+=	$totalDate;
									$arrResultInsert	=	array_merge($arrResultInsert, $resultInsert);
								}
							}
						}
					}
				}
			} else if($idProductType == 1) {
				$detailCostTicket	=	$this->ModelReservation->getDetailProductTicketVendor($idProductFee, $totalPax);
				
				if($detailCostTicket){
					$nominalCostAdult						=	$detailCostTicket['PRICEADULT'];
					$nominalCostChild						=	$detailCostTicket['PRICECHILD'];
					$nominalCostInfant						=	$detailCostTicket['PRICEINFANT'];
					$totalCostTicketAdult					=	$totalPaxAdult * $nominalCostAdult;
					$totalCostTicketChild					=	$totalPaxChild * $nominalCostChild;
					$totalCostTicketInfant					=	$totalPaxInfant * $nominalCostInfant;
					$nominalCost							=	$totalCostTicketAdult + $totalCostTicketChild + $totalCostTicketInfant;
					
					$arrReservationDetails['idProductType']	=	$idProductType;
					$arrReservationDetails['idVendor']		=	$detailCostTicket['IDVENDOR'];
					$arrReservationDetails['voucherStatus']	=	$detailCostTicket['VOUCHERSTATUS'];
					$arrReservationDetails['productName']	=	$detailCostTicket['PRODUCTNAME'];
					$arrReservationDetails['vendorName']	=	$detailCostTicket['VENDORNAME'];
					$arrReservationDetails['idDriverType']	=	0;
					$arrReservationDetails['jobType']		=	0;
					$arrReservationDetails['jobRate']		=	1;
					$arrReservationDetails['productRank']	=	1;
					$arrReservationDetails['nominalCost']	=	$nominalCost;
					$arrReservationDetails['notes']			=	$detailCostTicket['NOTES'];
					
					$arrReservationDetails['ticketAdultPax']	=	$totalPaxAdult;
					$arrReservationDetails['ticketChildPax']	=	$totalPaxChild;
					$arrReservationDetails['ticketInfantPax']	=	$totalPaxInfant;
					$arrReservationDetails['pricePerPaxAdult']	=	$nominalCostAdult;
					$arrReservationDetails['pricePerPaxChild']	=	$nominalCostChild;
					$arrReservationDetails['pricePerPaxInfant']	=	$nominalCostInfant;
					$arrReservationDetails['totalPriceAdult']	=	$totalCostTicketAdult;
					$arrReservationDetails['totalPriceChild']	=	$totalCostTicketChild;
					$arrReservationDetails['totalPriceInfant']	=	$totalCostTicketInfant;
					
					$resultInsert	=	$this->saveReservationDetails($arrReservationDetails);
					if($resultInsert){
						$totalDataProcess	+=	$totalDate;
						$arrResultInsert	=	array_merge($arrResultInsert, $resultInsert);
					}
				}
			}
		}
		
		setResponseOk(
			array(
				"token"				=>	$this->newToken,
				"msg"				=>	"Process is complete. ".$totalDataProcess." detailed cost data have been added",
				"arrResultInsert"	=>	$arrResultInsert
			)
		);
	}
	
	public function APISaveReservationDetails($jsonParamBase64Encode){
		$arrReservationDetails			=	json_decode(base64_decode(rawurldecode($jsonParamBase64Encode)), TRUE);
		$resultInsertReservationDetails	=	$this->saveReservationDetails($arrReservationDetails, true);

		if($resultInsertReservationDetails['isSuccess']){
			setResponseOk(array("msg"=>"Details has been added to reservation", "arrResultInsert"=>$resultInsertReservationDetails['arrResultInsert']));
		} else {
			setResponseInternalServerError(array("msg"=>$resultInsertReservationDetails['msg']));
		}
	}
	
	//USED BY OTHER FUNCTION :: DRIVER SCHEDULE - AUTO ADD CAR SCHEDULE
	public function saveReservationDetails($arrReservationDetails = false, $isAPI = false){
		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$userAdminName		=	$arrReservationDetails ? $arrReservationDetails['userAdminName'] : validatePostVar($this->postVar, 'NAME', true);
		$customerName		=	$arrReservationDetails ? $arrReservationDetails['customerName'] : validatePostVar($this->postVar, 'customerName', false);
		$idReservation		=	$arrReservationDetails ? $arrReservationDetails['idReservation'] : validatePostVar($this->postVar, 'idReservation', true);
		$arrDateSchedule	=	$arrReservationDetails ? $arrReservationDetails['arrDateSchedule'] : validatePostVar($this->postVar, 'arrDateSchedule', true);
		$idProductType		=	$arrReservationDetails ? $arrReservationDetails['idProductType'] : validatePostVar($this->postVar, 'idProductType', true);
		$voucherStatus		=	$arrReservationDetails ? $arrReservationDetails['voucherStatus'] : validatePostVar($this->postVar, 'voucherStatus', false);
		$productName		=	$arrReservationDetails ? $arrReservationDetails['productName'] : validatePostVar($this->postVar, 'productName', true);
		$vendorName			=	$arrReservationDetails ? $arrReservationDetails['vendorName'] : validatePostVar($this->postVar, 'vendorName', true);
		$durationSelfDrive	=	$arrReservationDetails ? $arrReservationDetails['durationSelfDrive'] : validatePostVar($this->postVar, 'durationSelfDrive', false);
		$idCarType			=	$arrReservationDetails ? $arrReservationDetails['idCarType'] : validatePostVar($this->postVar, 'idCarType', false);
		$idDriverType		=	$arrReservationDetails ? $arrReservationDetails['idDriverType'] : validatePostVar($this->postVar, 'idDriverType', false);
		$idVendor			=	$arrReservationDetails ? $arrReservationDetails['idVendor'] : validatePostVar($this->postVar, 'idVendor', false);
		$nominalCost		=	$arrReservationDetails ? $arrReservationDetails['nominalCost'] : validatePostVar($this->postVar, 'nominalCost', false);
		$nominalCost		=	str_replace(",", "", $nominalCost);
		$notes				=	$arrReservationDetails ? $arrReservationDetails['notes'] : validatePostVar($this->postVar, 'notes', false);
		$jobType			=	$arrReservationDetails ? $arrReservationDetails['jobType'] : validatePostVar($this->postVar, 'jobType', false);
		$jobRate			=	$arrReservationDetails ? $arrReservationDetails['jobRate'] : validatePostVar($this->postVar, 'jobRate', false);
		$productRank		=	$arrReservationDetails ? $arrReservationDetails['productRank'] : validatePostVar($this->postVar, 'productRank', false);
		$scheduleType		=	$arrReservationDetails ? $arrReservationDetails['scheduleType'] : validatePostVar($this->postVar, 'scheduleType', false);
		$scheduleType		=	$scheduleType == 0 ? 2 : $scheduleType;
		
		if(!is_array($arrDateSchedule) || count($arrDateSchedule) <= 0){
			if(!$isAPI) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select the schedule date for the product you input!"));
			if($isAPI) return [
				"isSuccess"	=>	false,
				"msg"		=>	"Please select the schedule date for the product you input!"
			];
		}	
		
		$inputMethod		=	$arrReservationDetails ? "Auto" : "Manual";
		$arrResultInsert	=	array();
		$strProductType		=	"";
		
		switch($idProductType){
			case "1"	:	$strProductType	=	"Ticket"; break;
			case "2"	:	$strProductType	=	"Transport"; break;
			case "3"	:	$strProductType	=	"Self Drive"; break;
		}
		
		if($idProductType == 2){
			$detailReservationCheck	=	$this->ModelReservation->getDetailReservationCheck($idReservation);
			
			if($detailReservationCheck){
				$durationDays			=	$detailReservationCheck['DURATIONOFDAY'];
				$totalPax				=	$detailReservationCheck['TOTALPAX'];
				$statusUpsellingSource	=	$detailReservationCheck['UPSELLINGTYPE'];
				$specialRequest			=	$detailReservationCheck['SPECIALREQUEST'];
				
				if($durationDays > 1) $scheduleType	=	2;
				if($totalPax > 6) $scheduleType	=	2;
				if($statusUpsellingSource == 1) $scheduleType	=	2;
				if($specialRequest != "" && $specialRequest != "-" && $specialRequest != null && $specialRequest != "null") $scheduleType	=	2;
			}
		}

		if($idProductType == 3) $scheduleType	=	2;

		foreach($arrDateSchedule as $dateSchedule){
			$strDateSchedule	=	date("d M Y", strtotime($dateSchedule));
			$arrInsert			=	array(
				"IDRESERVATION"	=>	$idReservation,
				"IDPRODUCTTYPE"	=>	$idProductType,
				"IDVENDOR"		=>	$idVendor,
				"IDDRIVERTYPE"	=>	$idDriverType,
				"IDCARTYPE"		=>	$idCarType,
				"VOUCHERSTATUS"	=>	$voucherStatus,
				"PRODUCTRANK"	=>	$productRank,
				"JOBTYPE"		=>	$jobType,
				"JOBRATE"		=>	$jobRate,
				"SCHEDULETYPE"	=>	$scheduleType,
				"SCHEDULEDATE"	=>	$dateSchedule,
				"DURATION"		=>	$durationSelfDrive,
				"PRODUCTNAME"	=>	$productName,
				"NOMINAL"		=>	$nominalCost,
				"NOTES"			=>	$notes,
				"USERINPUT"		=>	$userAdminName." (".$inputMethod.")",
				"DATETIMEINPUT"	=>	date('Y-m-d H:i:s')
			);
			$procInsert			=	$this->MainOperation->addData('t_reservationdetails', $arrInsert);
			
			if($idProductType == 1){
				$ticketAdultPax		=	$arrReservationDetails ? $arrReservationDetails['ticketAdultPax'] : intval(str_replace(",", "", validatePostVar($this->postVar, 'ticketAdultPax', false))) * 1;
				$ticketChildPax		=	$arrReservationDetails ? $arrReservationDetails['ticketChildPax'] : intval(str_replace(",", "", validatePostVar($this->postVar, 'ticketChildPax', false))) * 1;
				$ticketInfantPax	=	$arrReservationDetails ? $arrReservationDetails['ticketInfantPax'] : intval(str_replace(",", "", validatePostVar($this->postVar, 'ticketInfantPax', false))) * 1;
				$pricePerPaxAdult	=	$arrReservationDetails ? $arrReservationDetails['pricePerPaxAdult'] : intval(str_replace(",", "", validatePostVar($this->postVar, 'pricePerPaxAdult', false))) * 1;
				$pricePerPaxChild	=	$arrReservationDetails ? $arrReservationDetails['pricePerPaxChild'] : intval(str_replace(",", "", validatePostVar($this->postVar, 'pricePerPaxChild', false))) * 1;
				$pricePerPaxInfant	=	$arrReservationDetails ? $arrReservationDetails['pricePerPaxInfant'] : intval(str_replace(",", "", validatePostVar($this->postVar, 'pricePerPaxInfant', false))) * 1;
				$totalPriceAdult	=	$arrReservationDetails ? $arrReservationDetails['totalPriceAdult'] : intval(str_replace(",", "", validatePostVar($this->postVar, 'totalPriceAdult', false))) * 1;
				$totalPriceChild	=	$arrReservationDetails ? $arrReservationDetails['totalPriceChild'] : intval(str_replace(",", "", validatePostVar($this->postVar, 'totalPriceChild', false))) * 1;
				$totalPriceInfant	=	$arrReservationDetails ? $arrReservationDetails['totalPriceInfant'] : intval(str_replace(",", "", validatePostVar($this->postVar, 'totalPriceInfant', false))) * 1;
				
				$arrDetailsTicketPax=	array(
					"PAXADULT"				=>	$ticketAdultPax,
					"PAXCHILD"				=>	$ticketChildPax,
					"PAXINFANT"				=>	$ticketInfantPax,
					"PRICEPERPAXADULT"		=>	$pricePerPaxAdult,
					"PRICEPERPAXCHILD"		=>	$pricePerPaxChild,
					"PRICEPERPAXINFANT"		=>	$pricePerPaxInfant,
					"PRICETOTALADULT"		=>	$totalPriceAdult,
					"PRICETOTALCHILD"		=>	$totalPriceChild,
					"PRICETOTALINFANT"		=>	$totalPriceInfant
				);
			}
			
			if(!$procInsert['status']){
				if($procInsert['errCode'] == 1062){
					$idReservationDetails	=	$this->ModelReservation->getIdReservationDetails($idReservation, $idProductType, $idVendor, $idDriverType, $idCarType, $durationSelfDrive, $dateSchedule);
					if($idReservationDetails){
						
						$arrUpdate		=	array(
							"PRODUCTNAME"	=>	$productName,
							"NOMINAL"		=>	$nominalCost,
							"NOTES"			=>	$notes,
							"USERINPUT"		=>	$userAdminName,
							"DATETIMEINPUT"	=>	date('Y-m-d H:i:s'),
							"STATUS"		=>	1
						);
						$updateResult	=	$this->MainOperation->updateData("t_reservationdetails", $arrUpdate, "IDRESERVATIONDETAILS", $idReservationDetails);
						
						if(!$updateResult['status']){
							if(!$arrReservationDetails) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
							if($arrReservationDetails) {
								if($isAPI){
									return [
										"isSuccess"	=>	false,
										"msg"		=>	"Database internal process error"
									];
								} else {
									return false;
								}
							}
						}
						
						if($idProductType == 1) $this->MainOperation->updateData("t_reservationdetailsticket", $arrDetailsTicketPax, "IDRESERVATIONDETAILS", $idReservationDetails);
						$arrResultInsert[]	=	array(
							$idReservationDetails,
							$dateSchedule,
							$strDateSchedule,
							$userAdminName,
							date('d M Y H:i:s'),
							$strProductType,
							$productName,
							$vendorName,
							number_format($nominalCost, 0, '.', ','),
							$idReservation,
							$notes
						);
					} else {
						if(!$arrReservationDetails) switchMySQLErrorCode($procInsert['errCode'], $this->newToken);
						if($arrReservationDetails) {
							if($isAPI){
								return [
									"isSuccess"	=>	false,
									"msg"		=>	"Database internal process error"
								];
							} else {
								return false;
							}
						}
					}
				} else {
					if(!$arrReservationDetails) switchMySQLErrorCode($procInsert['errCode'], $this->newToken);
					if($arrReservationDetails) {
						if($isAPI){
							return [
								"isSuccess"	=>	false,
								"msg"		=>	"Database internal process error"
							];
						} else {
							return false;
						}
					}
				}
			} else {
				$lastIDReservationDetails	=	$procInsert['insertID'];
				$idReservationDetails		=	$lastIDReservationDetails;
				
				if($idProductType == 1){
					$arrDetailsTicketPax["IDRESERVATIONDETAILS"]	=	$lastIDReservationDetails;
					$this->MainOperation->addData('t_reservationdetailsticket', $arrDetailsTicketPax);

					$detailVendor			=	$this->MainOperation->getDataVendor($idVendor);
					$newFinanceScheme		=	$detailVendor['NEWFINANCESCHEME'];
					$vendorName				=	$detailVendor['NAME'];
					$vendorTokenFCM			=	$detailVendor['TOKENFCM'];
					$collectPayment			=	$this->ModelReservation->getCollectPaymentVendorReservation($idReservation, $idVendor);
					$strCollectPayment		=	"";
					
					if($collectPayment){
						foreach($collectPayment as $dataCollect){
							$idVendorCollect	=	$dataCollect->IDVENDOR;
							if($idVendorCollect == "" || $idVendorCollect == 0){
								$idCollectPayment				=	$dataCollect->IDCOLLECTPAYMENT;
								$partnerName					=	$this->MainOperation->getVendorNameById($idVendor);
								$arrUpdateCollect				=	array("IDVENDOR"=>$idVendor);
								$arrUpdateCollectPaymentHistory	=	array("DESCRIPTION" => "Collect payment is set to ".$partnerName);
		
								if($newFinanceScheme != 1){
									$arrUpdateCollect['STATUS']						=	1;
									$arrUpdateCollect['STATUSSETTLEMENTREQUEST']	=	2;
								} else {
									$arrUpdateCollect['STATUS']						=	0;
									$arrUpdateCollect['STATUSSETTLEMENTREQUEST']	=	0;
								}
								
								$this->MainOperation->updateData("t_collectpayment", $arrUpdateCollect, "IDCOLLECTPAYMENT", $idCollectPayment);
								$this->MainOperation->updateData('t_collectpaymenthistory', $arrUpdateCollectPaymentHistory, array("IDCOLLECTPAYMENT" => $idCollectPayment, "STATUS" => 0));
							}
							
							$amountCurrency		=	$dataCollect->AMOUNTCURRENCY;
							$amount				=	$dataCollect->AMOUNT;
							$amountIDR			=	$dataCollect->AMOUNTIDR;
							$strAmountIDR		=	number_format($amount, 0, '.', ',')." IDR ";
							$strAmount			=	$amountCurrency == "IDR" ? $strAmountIDR : number_format($amount, 0, '.', ',')." ".$amountCurrency." / ".$strAmountIDR;
							$strCollectPayment	.=	$strCollectPayment == "" ? "\nCollect Payment : " : $strCollectPayment;
							$strCollectPayment	.=	$strAmount." (".$dataCollect->DATECOLLECT.")";
						}
					}
					
					if($newFinanceScheme == 1){
						$arrInsertScheduleVendor	=	array(
							"IDRESERVATIONDETAILS"	=>	$lastIDReservationDetails,
							"IDVENDOR"				=>	$idVendor,
							"USERINPUT"				=>	$userAdminName,
							"DATETIMEINPUT"			=>	date('Y-m-d H:i:s'),
							"STATUSPROCESS"			=>	0,
							"STATUS"				=>	1
						);
						$procInsertScheduleVendor	=	$this->MainOperation->addData("t_schedulevendor", $arrInsertScheduleVendor);
						
						if($procInsertScheduleVendor['status']){
							
							$dataMessageType	=	$this->MainOperation->getDataMessageType(1);
							$activityMessage	=	$dataMessageType['ACTIVITY'];
							$titleMsg			=	"New schedule for ".$strDateSchedule;
							$body				=	"Details schedule\n";
							$body				.=	"Customer Name : ".$customerName."\n";
							$body				.=	"Service : ".$productName;
							$body				.=	$strCollectPayment;
							$additionalArray	=	array(
														"activity"	=>	$activityMessage,
														"idPrimary"	=>	$lastIDReservationDetails,
													);
							
							$arrInsertMsg		=	array(
								"IDMESSAGEPARTNERTYPE"	=>	1,
								"IDPARTNERTYPE"			=>	1,
								"IDPARTNER"				=>	$idVendor,
								"IDPRIMARY"				=>	$lastIDReservationDetails,
								"TITLE"					=>	$titleMsg,
								"MESSAGE"				=>	$body,
								"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
							);
							$procInsertMsg		=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
								
							if($procInsertMsg['status']){
								if($vendorTokenFCM != "") $this->fcm->sendPushNotification($vendorTokenFCM, $titleMsg, $body, $additionalArray);
								if(PRODUCTION_URL){
									$RTDB_refCode			=	$detailVendor['RTDBREFCODE'];
									if($RTDB_refCode && $RTDB_refCode != ''){
										try {
											$factory			=	(new Factory)
																	->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
																	->withDatabaseUri(FIREBASE_RTDB_URI);
											$database			=	$factory->createDatabase();
											$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/vendor/".$RTDB_refCode."/unconfirmedReservation");
											$referencePartnerVal=	$referencePartner->getValue();
											if($referencePartnerVal != null || !is_null($referencePartnerVal)){
												$referencePartner->update([
													'cancelReservationStatus'		=>  false,
													'newReservationStatus'          =>  true,
													'timestampUpdate'               =>  gmdate('YmdHis'),
													'newReservationDateTime'        =>  $strDateSchedule,
													'newReservationJobTitle'        =>  $productName,
													'totalUnconfirmedReservation'   =>  $this->MainOperation->getTotalUnconfirmedReservationPartner(1, $idVendor)
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
				
				$arrResultInsert[]	=	array(
					$lastIDReservationDetails,
					$dateSchedule,
					$strDateSchedule,
					$userAdminName,
					date('d M Y H:i:s'),
					$strProductType,
					$productName,
					$vendorName,
					number_format($nominalCost, 0, '.', ','),
					$idReservation,
					$notes,
					$voucherStatus
				);
			}
			
			$strDateSchedule	=	date('d M Y', strtotime($dateSchedule));
			$formatDateSchedule	=	date('d-m-Y', strtotime($dateSchedule));
			$yearMonthSchedule	=	date('Y-m', strtotime($dateSchedule));
			
			if($idProductType == 3) {
				$this->sendNewCarScheduleNotif($idReservationDetails, $yearMonthSchedule, $strDateSchedule, $customerName, $productName);
			} else if($idProductType == 2) {
				$this->sendNewDriverScheduleNotif($idReservationDetails, $strDateSchedule, $customerName, $productName, $formatDateSchedule);
				$this->MainOperation->calculateScheduleDriverMonitor($dateSchedule);
			} else {
				$dataCollectPaymentVendor	=	$this->ModelReservation->getDataCollectPaymentVendor($idReservation, $dateSchedule);
				if($dataCollectPaymentVendor){
					foreach($dataCollectPaymentVendor as $collectPaymentVendor){
						$idCollectPayment				=	$collectPaymentVendor->IDCOLLECTPAYMENT;
						$arrUpdateCollectPayment		=	array("IDVENDOR"=>$idVendor);
						$arrUpdateCollectPaymentHistory	=	array("DESCRIPTION" => "Collect payment is set to ".$vendorName);
						
						$this->MainOperation->updateData('t_collectpayment', $arrUpdateCollectPayment, "IDCOLLECTPAYMENT", $idCollectPayment);
						$this->MainOperation->updateData('t_collectpaymenthistory', $arrUpdateCollectPaymentHistory, array("IDCOLLECTPAYMENT" => $idCollectPayment));
					}
				}
			}
		}
		
		//UPDATE STATUS DRIVER/TICKET/CAR T_RESERVARTION
		$fieldUpdateStatus	=	"STATUSDRIVER";
		switch($idProductType){
			case 1	:	$fieldUpdateStatus	=	"STATUSTICKET"; break;
			case 2	:	$fieldUpdateStatus	=	"STATUSDRIVER"; break;
			case 3	:	$fieldUpdateStatus	=	"STATUSCAR"; break;
			default	:	$fieldUpdateStatus	=	"STATUSDRIVER"; break;
		}
		
		$statusRsvUpdate		=	$this->getStatusScheduleReservation($idReservation);		
		$this->MainOperation->updateData("t_reservation", array("STATUS"=>$statusRsvUpdate, $fieldUpdateStatus => "1"), "IDRESERVATION", $idReservation);
		$this->updateWebappStatisticTags();
		$this->processReservationMailRating($idReservation);
		if(!$arrReservationDetails) setResponseOk(array("token"=>$this->newToken, "msg"=>"Details has been added to reservation", "arrResultInsert"=>$arrResultInsert));
		if($arrReservationDetails) {
			if($isAPI){
				return [
					"isSuccess"			=>	true,
					"arrResultInsert"	=>	$arrResultInsert
				];
			} else {
				return $arrResultInsert;
			}
		}
	}
	
	public function getReservationDetailsTicket(){
		$this->load->model('ModelReservation');
		
		$idReservationDetails	=	validatePostVar($this->postVar, 'idReservationDetails', true);
		$detailData				=	$this->ModelReservation->getReservationDetailsTicket($idReservationDetails);
		
		if(!$detailData) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found, please refresh data and try again"));
		
		$idVendor				=	$detailData['IDVENDOR'];
		$ticketDetail			=	$this->ModelReservation->getDetailReservationTicket($idReservationDetails);
		$productTicketVendor	=	$this->ModelReservation->getProductTicketVendor($idVendor);
		setResponseOk(
			array(
				"token"					=>	$this->newToken,
				"detailData"			=>	$detailData,
				"ticketDetail"			=>	$ticketDetail,
				"productTicketVendor"	=>	$productTicketVendor,
				"idVendor"				=>	$idVendor
			)
		);
	}
	
	public function getReservationDetailsTransport(){
		$this->load->model('ModelReservation');
		
		$idReservationDetails	=	validatePostVar($this->postVar, 'idReservationDetails', true);
		$detailData				=	$this->ModelReservation->getReservationDetailsTransport($idReservationDetails);
		
		if(!$detailData) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found, please refresh data and try again"));
		setResponseOk(
			array(
				"token"					=>	$this->newToken,
				"detailData"			=>	$detailData
			)
		);
	}
	
	public function updateReservationDetailsTicket(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');

		$userAdminName			=	validatePostVar($this->postVar, 'NAME', true);
		$idReservationDetails	=	validatePostVar($this->postVar, 'idReservationDetails', true);
		$idVendor				=	validatePostVar($this->postVar, 'idVendor', true);
		$productName			=	validatePostVar($this->postVar, 'productName', true);
		$vendorName				=	validatePostVar($this->postVar, 'vendorName', true);
		$nominalCost			=	validatePostVar($this->postVar, 'nominalCost', true);
		$nominalCost			=	str_replace(",", "", $nominalCost);
		$notes					=	validatePostVar($this->postVar, 'notes', false);
		$correctionNotes		=	validatePostVar($this->postVar, 'correctionNotes', false);
		$voucherStatus			=	validatePostVar($this->postVar, 'voucherStatus', false);
		$detailFee				=	$this->ModelReservation->getDetailFeeVendor($idVendor, $idReservationDetails);
		
		if($detailFee){
			$idFee				=	$detailFee['IDFEE'];
			$idWithdrawalRecap	=	$detailFee['IDWITHDRAWALRECAP'];

			if($idWithdrawalRecap != 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Data changes are not allowed because the fee has been processed with withdrawal"));
			if($correctionNotes == '') setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please enter correction notes to continue"));
				
			$arrUpdateFee		=	array(
				"JOBTITLE"			=>	$productName,
				"FEENOMINAL"		=>	$nominalCost,
				"FEENOTES"			=>	$notes,
				"CORRECTIONNOTES"	=>	$correctionNotes,
				"DATETIMEINPUT"		=>	date('Y-m-d H:i:s')
			);
			$procUpdateFee	=	$this->MainOperation->updateData("t_fee", $arrUpdateFee, "IDFEE", $idFee);

			if(!$procUpdateFee['status']) switchMySQLErrorCode($procUpdateFee['errCode'], $this->newToken);
		}
		
		if($correctionNotes != ''){
			$notes			=	$notes == "" ? "Correction notes : ".$correctionNotes : $notes.PHP_EOL."Correction notes : ".$correctionNotes;
		}
		
		$arrUpdateDetails	=	array(
			"IDVENDOR"		=>	$idVendor,
			"VOUCHERSTATUS"	=>	$voucherStatus,
			"PRODUCTNAME"	=>	$productName,
			"NOMINAL"		=>	$nominalCost,
			"NOTES"			=>	$notes,
			"USERINPUT"		=>	$userAdminName." (Correction)",
			"DATETIMEINPUT"	=>	date('Y-m-d H:i:s')
		);
		$procUpdateDetails	=	$this->MainOperation->updateData('t_reservationdetails', $arrUpdateDetails, 'IDRESERVATIONDETAILS', $idReservationDetails);
		
		if(!$procUpdateDetails['status']) switchMySQLErrorCode($procUpdateDetails['errCode'], $this->newToken);
		
		$ticketAdultPax		=	intval(str_replace(",", "", validatePostVar($this->postVar, 'ticketAdultPax', false))) * 1;
		$ticketChildPax		=	intval(str_replace(",", "", validatePostVar($this->postVar, 'ticketChildPax', false))) * 1;
		$ticketInfantPax	=	intval(str_replace(",", "", validatePostVar($this->postVar, 'ticketInfantPax', false))) * 1;
		$pricePerPaxAdult	=	intval(str_replace(",", "", validatePostVar($this->postVar, 'pricePerPaxAdult', false))) * 1;
		$pricePerPaxChild	=	intval(str_replace(",", "", validatePostVar($this->postVar, 'pricePerPaxChild', false))) * 1;
		$pricePerPaxInfant	=	intval(str_replace(",", "", validatePostVar($this->postVar, 'pricePerPaxInfant', false))) * 1;
		$totalPriceAdult	=	intval(str_replace(",", "", validatePostVar($this->postVar, 'totalPriceAdult', false))) * 1;
		$totalPriceChild	=	intval(str_replace(",", "", validatePostVar($this->postVar, 'totalPriceChild', false))) * 1;
		$totalPriceInfant	=	intval(str_replace(",", "", validatePostVar($this->postVar, 'totalPriceInfant', false))) * 1;
		
		$arrDetailsTicketPax=	array(
			"PAXADULT"				=>	$ticketAdultPax,
			"PAXCHILD"				=>	$ticketChildPax,
			"PAXINFANT"				=>	$ticketInfantPax,
			"PRICEPERPAXADULT"		=>	$pricePerPaxAdult,
			"PRICEPERPAXCHILD"		=>	$pricePerPaxChild,
			"PRICEPERPAXINFANT"		=>	$pricePerPaxInfant,
			"PRICETOTALADULT"		=>	$totalPriceAdult,
			"PRICETOTALCHILD"		=>	$totalPriceChild,
			"PRICETOTALINFANT"		=>	$totalPriceInfant
		);
		$procUpdateTicket	=	$this->MainOperation->updateData('t_reservationdetailsticket', $arrDetailsTicketPax, 'IDRESERVATIONDETAILS', $idReservationDetails);

		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data update was successful"));
	}
	
	public function updateReservationDetailsTransport(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$userAdminName			=	validatePostVar($this->postVar, 'NAME', true);
		$productName			=	validatePostVar($this->postVar, 'productName', true);
		$nominal				=	str_replace(",", "", validatePostVar($this->postVar, 'nominal', true));
		$notes					=	validatePostVar($this->postVar, 'notes', false);
		$correctionNotes		=	validatePostVar($this->postVar, 'correctionNotes', false);
		$correctionNotesLength	=	validatePostVar($this->postVar, 'correctionNotesLength', false);
		$idReservationDetails	=	validatePostVar($this->postVar, 'idReservationDetails', true);
		$detailFee				=	$this->ModelReservation->getDetailFeeDriver($idReservationDetails);
		
		if($detailFee){
			$idFee				=	$detailFee['IDFEE'];
			$idWithdrawalRecap	=	$detailFee['IDWITHDRAWALRECAP'];

			if($idWithdrawalRecap != 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Data changes are not allowed because the fee has been processed with withdrawal"));
			if($correctionNotes == '' || $correctionNotesLength < 8) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please enter correction notes to continue"));
				
			$arrUpdateFee	=	array(
				"JOBTITLE"			=>	$productName,
				"FEENOMINAL"		=>	$nominal,
				"FEENOTES"			=>	$notes,
				"CORRECTIONNOTES"	=>	$correctionNotes,
				"DATETIMEINPUT"		=>	date('Y-m-d H:i:s')
			);
			$procUpdateFee	=	$this->MainOperation->updateData("t_fee", $arrUpdateFee, "IDFEE", $idFee);

			if(!$procUpdateFee['status']) switchMySQLErrorCode($procUpdateFee['errCode'], $this->newToken);
		}
		
		if($correctionNotes != '') $notes	=	$notes == "" ? "Correction notes : ".$correctionNotes : $notes.PHP_EOL."Correction notes : ".$correctionNotes;
		$arrUpdateDetails	=	array(
			"PRODUCTNAME"	=>	$productName,
			"NOMINAL"		=>	$nominal,
			"NOTES"			=>	$notes,
			"USERINPUT"		=>	$userAdminName." (Correction)",
			"DATETIMEINPUT"	=>	date('Y-m-d H:i:s')
		);
		$procUpdateDetails	=	$this->MainOperation->updateData("t_reservationdetails", $arrUpdateDetails, "IDRESERVATIONDETAILS", $idReservationDetails);
			
		if(!$procUpdateDetails['status']) switchMySQLErrorCode($procUpdateDetails['errCode'], $this->newToken);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data update was successful"));
	}
	
	private function getStatusScheduleReservation($idReservation){
		$this->load->model('ModelReservation');
		
		$dataDetails			=	$this->ModelReservation->getTotalReservationDetails($idReservation);
		$totalDetailsTicket		=	$this->ModelReservation->getTotalDetailsTicketReservation($idReservation);
		$totalUnscheduleDriver	=	$this->ModelReservation->getTotalUnscheduleDriver($idReservation);
		$totalDetails			=	$dataDetails['TOTALDETAILS'];
		$statusReservation		=	0;
		
		if($totalDetails > 0){
			if($totalDetailsTicket > 0 && $totalUnscheduleDriver <= 0){
				$statusReservation	=	2;
			} else if($totalDetailsTicket <= 0 && $totalUnscheduleDriver <= 0) {
				$statusReservation	=	2;			
			} else {
				$statusReservation	=	1;
			}
		}
		
		return $statusReservation;
	}
	
	public function searchListReservationForVoucher(){
		$this->load->model('ModelReservation');
		
		$reservationKeyword	=	validatePostVar($this->postVar, 'reservationKeyword', false);
		if(!isset($reservationKeyword) || $reservationKeyword == "") setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));

		$reservationList	=	$this->ModelReservation->getListReservationByKeyword($reservationKeyword);
		if(!$reservationList) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));

		foreach($reservationList as $dataReservation){
			$durationOfDay		=	$dataReservation->DURATIONOFDAY;
			$dateStartSchedule	=	$dataReservation->RESERVATIONDATEVALUE;
			$arrDateSchedule	=	array();
			
			for($iDay=0; $iDay<$durationOfDay; $iDay++){
				$dateSchedule		=	date('Ymd', strtotime($dateStartSchedule . ' +'.$iDay.' day'));
				$arrDateSchedule[]	=	$dateSchedule;
			}
			
			$dataReservation->ARRDATESCHEDULE	=	implode(',', $arrDateSchedule);
		}
		
		setResponseOk(
			array(
				"token"				=>	$this->newToken,
				"reservationList"	=>	$reservationList
			)
		);
	}
	
	public function getReservationVoucherList(){
		$this->load->model('ModelReservation');
		
		$idReservation	=	validatePostVar($this->postVar, 'idReservation', true);
		$detailData		=	$this->ModelReservation->getDetailReservation($idReservation);
		
		if(!$detailData) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found"));

		$listVoucher		=	$this->ModelReservation->getListVoucherReservation($idReservation);
		$listDetails		=	$this->ModelReservation->getListReservationDetails($idReservation);
		$durationOfDay		=	$detailData['DURATIONOFDAY'];
		$dateStartSchedule	=	$detailData['RESERVATIONDATEVALUE'];
		$arrDateSchedule	=	$arrVendorVoucher	=	$arrVendorVoucherCheck	=	array();
		$serviceNameVoucher	=	"";
		
		for($iDay=0; $iDay<$durationOfDay; $iDay++){
			$dateSchedule		=	date('Y-m-d', strtotime($dateStartSchedule . ' +'.$iDay.' day'));
			$dateScheduleStr	=	date('d M Y', strtotime($dateStartSchedule . ' +'.$iDay.' day'));
			$arrDateSchedule[]	=	array($dateSchedule, $dateScheduleStr);
		}
		
		foreach($listDetails as $details){
			if($details->VOUCHERSTATUS == 1 && $details->IDVENDOR != 0){
				if(!in_array($details->IDVENDOR, $arrVendorVoucherCheck)){
					$arrVendorVoucher[]			=	array($details->IDVENDOR, $details->VENDORNAME);
					$arrVendorVoucherCheck[]	=	$details->IDVENDOR;
				}
				$serviceNameVoucher	=	$serviceNameVoucher == "" ? $details->PRODUCTNAME : $serviceNameVoucher;
			}
		}
		
		setResponseOk(
			array(
				"token"				=>	$this->newToken,
				"detailData"		=>	$detailData,
				"serviceNameVoucher"=>	$serviceNameVoucher,
				"listVoucher"		=>	$listVoucher,
				"arrDateSchedule"	=>	$arrDateSchedule,
				"arrVendorVoucher"	=>	$arrVendorVoucher
			)
		);
	}
	
	public function saveReservationVoucher(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$userAdminName	=	validatePostVar($this->postVar, 'NAME', true);
		$idReservation	=	validatePostVar($this->postVar, 'idReservationCreateVoucher', true);
		$arrDateVoucher	=	validatePostVar($this->postVar, 'arrDateVoucher', true);
		$guestName		=	validatePostVar($this->postVar, 'guestNameVoucher', true);
		$idVendor		=	validatePostVar($this->postVar, 'optionVendorVoucher', true);
		$vendorName		=	validatePostVar($this->postVar, 'vendorName', true);
		$serviceName	=	validatePostVar($this->postVar, 'serviceNameVoucher', true);
		$paxName1		=	validatePostVar($this->postVar, 'paxName1', true);
		$paxName2		=	validatePostVar($this->postVar, 'paxName2', true);
		$paxName3		=	validatePostVar($this->postVar, 'paxName3', true);
		$paxTotal1		=	validatePostVar($this->postVar, 'paxTotal1', false);
		$paxTotal2		=	validatePostVar($this->postVar, 'paxTotal2', false);
		$paxTotal3		=	validatePostVar($this->postVar, 'paxTotal3', false);
		$notes			=	validatePostVar($this->postVar, 'voucherNotes', false);
		$serviceDate	=	$yearMonth	=	"";
		$arrAdditional	=	array();
		
		if(!is_array($arrDateVoucher) || count($arrDateVoucher) <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select the schedule date for this voucher"));

		foreach($arrDateVoucher as $dateVoucher){
			$dateVoucher	=	DateTime::createFromFormat('Y-m-d', $dateVoucher);
			$dateVoucherStr	=	$dateVoucher->format('d M Y');
			$serviceDate	.=	$dateVoucherStr.", ";
			$yearMonth		=	$yearMonth == "" ? $dateVoucher->format('Y-m') : $yearMonth;
		}
		
		$serviceDate			=	substr($serviceDate, 0, -2);
		$dataVoucherCode		=	$this->ModelReservation->getDataVoucherCode($yearMonth);
		$voucherNumber			=	$dataVoucherCode['voucherNumber'];
		$voucherCode			=	$dataVoucherCode['voucherCode'];
		$arrInsert				=	array(
			"IDRESERVATION"	=>	$idReservation,
			"IDVENDOR"		=>	$idVendor,
			"YEARMONTH"		=>	$yearMonth,
			"VOUCHERNUMBER"	=>	$voucherNumber,
			"VOUCHERCODE"	=>	$voucherCode,
			"GUESTNAME"		=>	$guestName,
			"PAXNAME1"		=>	$paxName1,
			"PAXNAME2"		=>	$paxName2,
			"PAXNAME3"		=>	$paxName3,
			"PAXTOTAL1"		=>	$paxTotal1,
			"PAXTOTAL2"		=>	$paxTotal2,
			"PAXTOTAL3"		=>	$paxTotal3,
			"SERVICENAME"	=>	$serviceName,
			"SERVICEDATE"	=>	$serviceDate,
			"NOTES"			=>	$notes,
			"USERINPUT"		=>	$userAdminName,
			"DATETIMEINPUT"	=>	date('Y-m-d H:i:s')
		);
		$arrAdditional['SERVICEDATESTR']=	$dateVoucherStr;
		$arrAdditional['VENDORNAME']	=	$vendorName;
		$fileNameVoucher				=	$this->createPdfReservationVoucher($arrInsert, $arrAdditional);
		$arrInsert['FILENAME']			=	$fileNameVoucher;
		$procInsert						=	$this->MainOperation->addData('t_reservationvoucher', $arrInsert);
		
		if(!$procInsert['status']) switchMySQLErrorCode($procInsert['errCode'], $this->newToken);

		$arrInsert['VENDORNAME']			=	$vendorName;
		$arrInsert['IDRESERVATIONVOUCHER']	=	$procInsert['insertID'];
		$arrInsert['URLPDFFILEVOUCHER']		=	URL_RESEVATION_VOUCHER_FILE.$fileNameVoucher;
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New voucher have been created", "newVoucherDetail"=>array($arrInsert)));
	}
	
	public function APIDeactivateReservationDetails($jsonParamBase64Encode){
		$arrDataDeleteReservationDetails=	json_decode(base64_decode(rawurldecode($jsonParamBase64Encode)), TRUE);
		$resultDeleteReservationDetails	=	$this->deactivateReservationDetails($arrDataDeleteReservationDetails);

		if($resultDeleteReservationDetails['isSuccess']){
			setResponseOk(array("msg"=>$resultDeleteReservationDetails['msg']));
		} else {
			setResponseInternalServerError(array("msg"=>$resultDeleteReservationDetails['msg']));
		}
	}
	
	public function deactivateReservationDetails($arrDataDeleteReservationDetails = false){
		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$idReservationDetails	=	$arrDataDeleteReservationDetails ? $arrDataDeleteReservationDetails['idReservationDetails'] : validatePostVar($this->postVar, 'idData', true);
		$idReservation			=	$arrDataDeleteReservationDetails ? $arrDataDeleteReservationDetails['idReservation'] : validatePostVar($this->postVar, 'idReservation', true);
		$dateSchedule			=	$arrDataDeleteReservationDetails ? $arrDataDeleteReservationDetails['dateSchedule'] : validatePostVar($this->postVar, 'dateSchedule', false);
		$detailReservation		=	$this->ModelReservation->getDetailReservationVendor($idReservationDetails);
		$isDriverScheduleExists	=	$this->ModelReservation->isDriverScheduleExists($idReservationDetails);

		if($isDriverScheduleExists){
			if(!$arrDataDeleteReservationDetails) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"<b>Failed to delete detail!</b><br/>Please delete driver schedule before delete this detail"));
			if($arrDataDeleteReservationDetails) return [
				"isSuccess"	=>	false,
				"msg"		=>	"Failed to delete detail! Please delete driver schedule before delete this detail"
			];
		}
		
		$isCarScheduleExists	=	$this->ModelReservation->isCarScheduleExists($idReservationDetails);
		if($isCarScheduleExists){
			if(!$arrDataDeleteReservationDetails) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"<b>Failed to delete detail!</b><br/>Please delete car schedule before delete this detail"));
			if($arrDataDeleteReservationDetails) return [
				"isSuccess"	=>	false,
				"msg"		=>	"Failed to delete detail! Please delete car schedule before delete this detail"
			];
		}
		
		$deleteResult	=	$this->MainOperation->deleteData("t_reservationdetails", array("IDRESERVATIONDETAILS"=>$idReservationDetails));
		if(!$deleteResult['status']){
			if(!$arrDataDeleteReservationDetails) switchMySQLErrorCode($deleteResult['errCode'], $this->newToken);
			if($arrDataDeleteReservationDetails) {
				return [
					"isSuccess"	=>	false,
					"msg"		=>	"Database internal process error"
				];
			}
		}
		
		$this->MainOperation->deleteData("t_schedulevendor", array("IDRESERVATIONDETAILS"=>$idReservationDetails));
		
		$dataDetails			=	$this->ModelReservation->getTotalReservationDetails($idReservation);
		$statusRsvUpdate		=	$this->getStatusScheduleReservation($idReservation);		
		$arrUpdateReservation	=	array(
			"STATUS"		=>	$statusRsvUpdate,
			"STATUSTICKET"	=>	$dataDetails['TOTALTICKET'] <= 0 ? 0 : 1,
			"STATUSDRIVER"	=>	$dataDetails['TOTALDRIVER'] <= 0 ? 0 : 1,
			"STATUSCAR"		=>	$dataDetails['TOTALCAR'] <= 0 ? 0 : 1,
		);
		$this->MainOperation->updateData("t_reservation", $arrUpdateReservation, "IDRESERVATION", $idReservation);
		
		if($detailReservation){
			$idVendor			=	$detailReservation['IDVENDOR'];
			$collectPayment		=	$this->ModelReservation->getCollectPaymentVendorReservation($idReservation, $idVendor);
		
			if($collectPayment){
				foreach($collectPayment as $dataCollect){
					$idVendorCollect	=	$dataCollect->IDVENDOR;
					if($idVendorCollect == $idVendor){
						$idCollectPayment	=	$dataCollect->IDCOLLECTPAYMENT;
						$arrUpdateCollect	=	array(
							"IDVENDOR"					=>	0,
							"STATUS"					=>	0,
							"STATUSSETTLEMENTREQUEST"	=>	0
						);
						$arrUpdateCollectPaymentHistory	=	array("DESCRIPTION" => "Collect payment is set to -");
						
						$this->MainOperation->updateData("t_collectpayment", $arrUpdateCollect, "IDCOLLECTPAYMENT", $idCollectPayment);
						$this->MainOperation->updateData('t_collectpaymenthistory', $arrUpdateCollectPaymentHistory, array("IDCOLLECTPAYMENT" => $idCollectPayment, "STATUS" => 0));
					}
				}
			}
		
			if($idVendor != 0){
				$detailFee			=	$this->ModelReservation->getDetailFeeVendor($idVendor, $idReservationDetails);
				if($detailFee){
					$idFee			=	$detailFee['IDFEE'];
					$this->MainOperation->deleteData("t_fee", array("IDFEE"=>$idFee));
				}
				
				$dataMessageType	=	$this->MainOperation->getDataMessageType(3);
				$activityMessage	=	$dataMessageType['ACTIVITY'];
				$detailVendor		=	$this->MainOperation->getDataVendor($idVendor);
				$newFinanceScheme	=	$detailVendor['NEWFINANCESCHEME'];
				$vendorName			=	$detailVendor['NAME'];
				$vendorTokenFCM		=	$detailVendor['TOKENFCM'];
				
				$dateScheduleStr	=	$detailReservation['SCHEDULEDATESTR'];
				$customerName		=	$detailReservation['CUSTOMERNAME'];
				$rsvTitle			=	$detailReservation['RESERVATIONTITLE'];
				$rsvService			=	$detailReservation['PRODUCTNAME'];
				
				$titleMsg			=	"Reservation cancelation for ".$dateScheduleStr;
				$body				=	"Details schedule\n";
				$body				.=	"Customer Name : ".$customerName."\n";
				$body				.=	"Reservation Title : ".$rsvTitle."\n";
				$body				.=	"Job Title : ".$rsvService;
				$additionalArray	=	array(
					"activity"	=>	$activityMessage,
					"idPrimary"	=>	$idReservationDetails,
				);
				
				$arrInsertMsg	=	array(
					"IDMESSAGEPARTNERTYPE"	=>	3,
					"IDPARTNERTYPE"			=>	1,
					"IDPARTNER"				=>	$idVendor,
					"IDPRIMARY"				=>	$idReservationDetails,
					"TITLE"					=>	$titleMsg,
					"MESSAGE"				=>	$body,
					"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
				);
				$procInsertMsg	=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
					
				if($procInsertMsg['status']){
					if($vendorTokenFCM != "") $this->fcm->sendPushNotification($vendorTokenFCM, $titleMsg, $body, $additionalArray);
					if(PRODUCTION_URL){
						$RTDB_refCode	=	$detailVendor['RTDBREFCODE'];
						if($RTDB_refCode && $RTDB_refCode != ''){
							try {
								$factory			=	(new Factory)
														->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
														->withDatabaseUri(FIREBASE_RTDB_URI);
								$database			=	$factory->createDatabase();
								$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/vendor/".$RTDB_refCode."/unconfirmedReservation");
								$referencePartnerVal=	$referencePartner->getValue();
								if($referencePartnerVal != null || !is_null($referencePartnerVal)){
									$referencePartner->update([
										'timestampUpdate'               =>  gmdate('YmdHis'),
										'totalUnconfirmedReservation'   =>  $this->MainOperation->getTotalUnconfirmedReservationPartner(1, $idVendor),
										'newReservationStatus'          =>  false,
										'cancelReservationStatus'		=>  true,
										'cancelReservationDetails'		=>  nl2br('Schedule Cancelation!<br/>'.$body)
									]);
								}
							} catch (Exception $e) {
							}
						}
					}
				}
			}
		}
		
		if(isset($dateSchedule) && $dateSchedule != "") $this->MainOperation->calculateScheduleDriverMonitor($dateSchedule);
		$this->updateWebappStatisticTags();
		$this->processReservationMailRating($idReservation);
		
		if(!$arrDataDeleteReservationDetails) setResponseOk(array("token"=>$this->newToken, "msg"=>"Reservation details has been deleted"));
		if($arrDataDeleteReservationDetails) return [
			"isSuccess"	=>	true,
			"msg"		=>	"Reservation details has been deleted"
		];
	}
	
	public function cancelReservation(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$idReservation	=	validatePostVar($this->postVar, 'idData', true);
		$refundType		=	validatePostVar($this->postVar, 'refundType', true);
		$dataDetails	=	$this->ModelReservation->getTotalReservationDetails($idReservation);
		$totalDetails	=	$dataDetails['TOTALDETAILS'];

		if($totalDetails > 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"<b>Failed to cancel reservation!</b><br/>Please delete all reservation details before cancel this reservation"));

		$dataUserAdmin	=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName	=	$dataUserAdmin['NAME'];
		$arrUpdateRsv	=	array(	
								"USERLASTUPDATE"	=>	$userAdminName,
								"DATETIMELASTUPDATE"=>	date('Y-m-d H:i:s'),
								"STATUS"			=>	-1,
								"REFUNDTYPE"		=>	$refundType
							);
		$updateResult	=	$this->MainOperation->updateData("t_reservation", $arrUpdateRsv, "IDRESERVATION", $idReservation);
		
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);

		$dataCollectPayment	=	$this->ModelReservation->getDataCollectPaymentReservation($idReservation);
		if($dataCollectPayment){
			foreach($dataCollectPayment as $keyCollectPayment){
				$idReservationPayment	=	$keyCollectPayment->IDRESERVATIONPAYMENT;
				$this->MainOperation->deleteData('t_reservationpayment', array("IDRESERVATIONPAYMENT" => $idReservationPayment));
				$this->MainOperation->deleteData('t_collectpayment', array("IDRESERVATIONPAYMENT" => $idReservationPayment));
			}
		}
		
		if($refundType == -2){
			$this->MainOperation->updateData('t_reservationpayment', ['EDITABLE' => 1], ["IDRESERVATION" => $idReservation]);
		}
		
		if($refundType == -1){
			$this->MainOperation->updateData('t_reservationpayment', ['EDITABLE' => 0, 'STATUS' => -1], ["IDRESERVATION" => $idReservation]);
		}
		
		$this->updateWebappStatisticTags();
		$this->updateWebappStatisticTagsUnreadThreadReconfirmation();
		$this->processReservationMailRating($idReservation);
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Reservation has been canceled"));
	}
	
	public function updateRefundTypeReservation(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$idReservation	=	validatePostVar($this->postVar, 'idReservation', true);
		$refundType		=	validatePostVar($this->postVar, 'refundType', true);
		$userAdminName	=	validatePostVar($this->postVar, 'NAME', true);
		$arrUpdateRsv	=	array(	
								"USERLASTUPDATE"	=>	$userAdminName,
								"DATETIMELASTUPDATE"=>	date('Y-m-d H:i:s'),
								"REFUNDTYPE"		=>	$refundType
							);
		$updateResult	=	$this->MainOperation->updateData("t_reservation", $arrUpdateRsv, "IDRESERVATION", $idReservation);
		
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		
		$dataReservationPayment	=	$this->ModelReservation->getReservationPaymentList($idReservation);
		
		if(isset($dataReservationPayment) && count($dataReservationPayment) > 0)
		foreach($dataReservationPayment as $keyReservationPayment){
			$idReservationPayment		=	$keyReservationPayment->IDRESERVATIONPAYMENT;
			$reservationPaymentStatus	=	$keyReservationPayment->STATUS;
			
			if($refundType == -2){
				$arrUpdatePayment	=	['EDITABLE' => 1];
				if($reservationPaymentStatus == -1) $arrUpdatePayment['STATUS']	=	0;
				$this->MainOperation->updateData('t_reservationpayment', $arrUpdatePayment, ["IDRESERVATIONPAYMENT" => $idReservationPayment]);
			}
			
			if($refundType == -1){
				$this->MainOperation->updateData('t_reservationpayment', ['EDITABLE' => 0, 'STATUS' => -1], ["IDRESERVATIONPAYMENT" => $idReservationPayment]);
			}
			
			if($refundType == 0){
				$arrUpdatePayment	=	['EDITABLE' => 0];
				if($reservationPaymentStatus == -1) $arrUpdatePayment['STATUS']	=	0;
				$this->MainOperation->updateData('t_reservationpayment', $arrUpdatePayment, ["IDRESERVATIONPAYMENT" => $idReservationPayment]);
			}
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Reservation refund type has been updated"));
	}
	
	private function sendNewCarScheduleNotif($idReservationDetails, $yearMonthSchedule, $strDateSchedule, $customerName, $productName){
		$dataPlayerId	=	$this->MainOperation->getDataPlayerIdOneSignal("NOTIFSCHEDULEVENDOR");
		$splitYearMonth	=	explode("-", $yearMonthSchedule);
		$year			=	$splitYearMonth[0];
		$month			=	$splitYearMonth[1];
		
		if($dataPlayerId){
			$arrPlayerId	=	$dataPlayerId['arrOSUserId'];
			$arrIdUserAdmin	=	$dataPlayerId['arrIdUserAdmin'];
			$title			=	'New car schedule for '.$strDateSchedule;
			$message		=	$productName.' - Customer name : '.$customerName;
			$arrData		=	array(
				"type"					=>	"carschedule",
				"idReservationDetails"	=>	$idReservationDetails,
				"year"					=>	$year,
				"month"					=>	$month
			);
			$arrHeading		=	array("en" => $title);
			$arrContent		=	array("en" => $message);
			$this->MainOperation->insertAdminMessage(3, $arrIdUserAdmin, $title, $message, $arrData);
			if(PRODUCTION_URL) sendOneSignalMessage($arrPlayerId, $arrData, $arrHeading, $arrContent);
		}
		
		return true;
	}
	
	private function sendNewDriverScheduleNotif($idReservationDetails, $strDateSchedule, $customerName, $productName, $formatDateSchedule){
		$dataPlayerId	=	$this->MainOperation->getDataPlayerIdOneSignal("NOTIFSCHEDULEDRIVER");
		
		if($dataPlayerId){
			$arrPlayerId	=	$dataPlayerId['arrOSUserId'];
			$arrIdUserAdmin	=	$dataPlayerId['arrIdUserAdmin'];
			$title			=	'New driver schedule for '.$strDateSchedule;
			$message		=	$productName.' - Customer name : '.$customerName;
			$arrData		=	array(
				"type"					=>	"driverschedule",
				"idReservationDetails"	=>	$idReservationDetails,
				"date"					=>	$formatDateSchedule
			);
			$arrHeading		=	["en" => $title];
			$arrContent		=	["en" => $message];
			$this->MainOperation->insertAdminMessage(4, $arrIdUserAdmin, $title, $message, $arrData);
			if(PRODUCTION_URL) sendOneSignalMessage($arrPlayerId, $arrData, $arrHeading, $arrContent);
		}
		
		return true;
	}
	
	private function updateWebappStatisticTagsUnreadThreadReconfirmation(){
		if(PRODUCTION_URL){
			$this->load->model('MainOperation');
			$totalUnreadThreadReconfirmation	=	$this->MainOperation->getTotalUnreadThreadReconfirmation();
			
			try {
				$factory	=	(new Factory)
								->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
								->withDatabaseUri(FIREBASE_RTDB_URI);
				$database	=	$factory->createDatabase();
				$reference	=	$database->getReference(FIREBASE_RTDB_MAINREF_NAME.'unreadThreadReconfirmation')
								->set([
								   'newMailThreadStatus'		=>	false,
								   'newMailThreadName'			=>	'',
								   'newMailThreadAddress'		=>	'',
								   'unreadThreadReconfirmation'	=>	$totalUnreadThreadReconfirmation,
								   'timestampUpdate'			=>	gmdate("YmdHis")
								  ]);
			} catch (Exception $e) {
			}
		}
		return true;		
	}
	
	public function excelDetail($encryptedVar){
		$this->load->model('ModelReservation');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$arrDates				=	array();
		$decryptedVar			=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar		=	explode("|", $decryptedVar);
		$idReservation			=	$expDecryptedVar[0];
		$status					=	$expDecryptedVar[1];
		$idSource				=	$expDecryptedVar[2];
		$idPartner				=	$expDecryptedVar[3];
		$bookingCode			=	$expDecryptedVar[4];
		$customerName			=	$expDecryptedVar[5];
		$transportStatus		=	$expDecryptedVar[6];
		$reservationTitle		=	$expDecryptedVar[7];
		$locationName			=	$expDecryptedVar[8];
		$orderBy				=	$expDecryptedVar[9];
		$orderType				=	$expDecryptedVar[10];
		$startDate				=	$expDecryptedVar[11];
		$endDate				=	$expDecryptedVar[12];
		$collectPaymentStatus	=	$expDecryptedVar[13];
		$year					=	$expDecryptedVar[14];
		$idReservationType		=	$expDecryptedVar[15];
		$arrDates				=	array();
		$strArrIdReservation	=	$typePartner	=	$partnerName	=	"";
		$idVendorType			=	0;

		if($startDate == "" && $endDate != "") $startDate	=	$endDate;
		if($startDate != "" && $endDate == "") $endDate		=	$startDate;
		
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
				$idVendorType			=	$expIdPartner[1];
				if($idVendorType == 1){
					$idVendorCar		=	$expIdPartner[2];
					$strArrIdReservation=	$this->ModelReservation->getStrArrIdReservationByIdVendorCar($idVendorCar, $startDate, $endDate);
				} else {
					$idVendorTicket		=	$expIdPartner[2];
					$strArrIdReservation=	$this->ModelReservation->getStrArrIdReservationByIdVendorTicket($idVendorTicket, $startDate, $endDate);
				}
			} else {
				$idDriver				=	$expIdPartner[1];
				$strArrIdReservation	=	$this->ModelReservation->getStrArrIdReservationByIdDriver($idDriver, $startDate, $endDate);
			}
		}
		
		$statusStr			=	"All Status";
		$statusTransportStr	=	"All Transport Status";
		$partnerStr			=	$idPartner == "" || $idPartner == 0 ? "All Partner" : $typePartner." - ".$partnerName;
		$reservationTypeStr	=	isset($idReservationType) && $idReservationType != "" && $idReservationType != 0 ? $this->MainOperation->getReservationTypeById($idReservationType) : "All Reservation Type";
		$sourceStr			=	isset($idSource) && $idSource != "" && $idSource != 0 ? $this->MainOperation->getSourceNameById($idSource) : "All Source";
		$orderByStr			=	$orderBy == 1 ? "Reservation Number" : "Reservation Date";
		$dateRangeStr		=	$startDateStr." to ".$endDateStr;
		$dataTable			=	$this->ModelReservation->getDataReservation(1, 999999, $idReservation, $arrDates, $status, $idSource, $bookingCode, $customerName, $locationName, $startDate, $endDate, $strArrIdReservation, $transportStatus, $reservationTitle, $orderBy, $orderType, $idPartner, $idVendorType, $collectPaymentStatus, $year, $idReservationType);
		
		switch($status){
			case "0"	:	$statusStr	=	"Unprocessed"; break;
			case "1"	:	$statusStr	=	"Processed By Admin"; break;
			case "2"	:	$statusStr	=	"Scheduled"; break;
			case "3"	:	$statusStr	=	"On Process"; break;
			case "4"	:	$statusStr	=	"Done"; break;
			case "-1"	:	$statusStr	=	"Cancel"; break;
		}
		
		switch($transportStatus){
			case "1"	:	$statusTransportStr	=	"With Transport"; break;
			case "-1"	:	$statusTransportStr	=	"Without Transport"; break;
		}
		
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
		$sheet->setCellValue('A2', 'Detail Reservation Report');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:R1');
		$sheet->mergeCells('A2:R2');
		
		$sheet->setCellValue('A4', 'Reservation Type : '.$reservationTypeStr);
		$sheet->setCellValue('A5', 'Source : '.$sourceStr);
		$sheet->setCellValue('A6', 'Booking Code (Contains) : '.$bookingCode);
		$sheet->setCellValue('A7', 'Customer Name (Contains) : '.$customerName);
		$sheet->setCellValue('A8', 'Reservation Title (Contains) : '.$reservationTitle);
		$sheet->setCellValue('A9', 'Transport Status : '.$statusTransportStr);
		$sheet->setCellValue('A10', 'Location (Contains) : '.$locationName);
		$sheet->setCellValue('A11', 'Date Period : '.$dateRangeStr);
		$sheet->setCellValue('A12', 'Status : '.$statusStr);
		$sheet->setCellValue('A13', 'Partner : '.$partnerStr);
		$sheet->setCellValue('A14', 'Data Order By : '.$orderByStr.' '.$orderType);

		$sheet->mergeCells('A4:R4');
		$sheet->mergeCells('A5:R5');
		$sheet->mergeCells('A6:R6');
		$sheet->mergeCells('A7:R7');
		$sheet->mergeCells('A8:R8');
		$sheet->mergeCells('A9:R9');
		$sheet->mergeCells('A10:R10');
		$sheet->mergeCells('A11:R11');
		$sheet->mergeCells('A12:R12');
		$sheet->mergeCells('A13:R13');
		$sheet->mergeCells('A14:R14');
		
		$sheet->setCellValue('A16', 'Reservation Description');
		$sheet->setCellValue('G16', 'Customer Details)');
		$sheet->setCellValue('K16', 'Location');
		$sheet->setCellValue('O16', 'Additional Info');
		$sheet->mergeCells('A16:F16');
		$sheet->mergeCells('G16:J16');
		$sheet->mergeCells('K16:N16');
		$sheet->mergeCells('O16:R16');
		
		$sheet->setCellValue('A17', 'Reservation Type');
		$sheet->setCellValue('B17', 'Reservation Title');
		$sheet->setCellValue('C17', 'Source');
		$sheet->setCellValue('D17', 'Booking Code');
		$sheet->setCellValue('E17', 'Date');
		$sheet->setCellValue('F17', 'Status');
		$sheet->setCellValue('G17', 'Guest Name');
		$sheet->setCellValue('H17', 'Contact');
		$sheet->setCellValue('I17', 'Email');
		$sheet->setCellValue('J17', 'Pax');
		$sheet->setCellValue('K17', 'Zone');
		$sheet->setCellValue('L17', 'Hotel');
		$sheet->setCellValue('M17', 'Pick Up');
		$sheet->setCellValue('N17', 'Drop Off');
		$sheet->setCellValue('O17', 'Remark');
		$sheet->setCellValue('P17', 'Tour Plan');
		$sheet->setCellValue('Q17', 'Special Request');
		$sheet->setCellValue('R17', 'Handle By');
		
		$sheet->getStyle('A16:R17')->getFont()->setBold( true );
		$sheet->getStyle('A16:R17')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A16:R17')->getAlignment()->setVertical('center');
		$rowNumber	=	18;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		foreach($dataTable['data'] as $data){
			$reservationDateEnd		=	$data->RESERVATIONDATEEND != $data->RESERVATIONDATESTART ? "\nTo\n".$data->RESERVATIONDATEEND." ".$data->RESERVATIONTIMEEND : "";
			$paxStr					=	" ".$data->NUMBEROFADULT;
			$reservationStatusStr	=	$driverHandleStr		=	$ticketHandleStr	=	$carHandleStr	=	"";
			
			if($data->NUMBEROFCHILD == 0 && $data->NUMBEROFINFANT != 0){
				$paxStr	.=	"+0+".$data->NUMBEROFINFANT;
			} else if($data->NUMBEROFCHILD != 0 && $data->NUMBEROFINFANT == 0){
				$paxStr	.=	"+".$data->NUMBEROFINFANT."+0";
			} else if($data->NUMBEROFCHILD != 0 && $data->NUMBEROFINFANT != 0){
				$paxStr	.=	"+".$data->NUMBEROFINFANT."+".$data->NUMBEROFCHILD;
			}
			
			$driverHandle		=	$this->ModelReservation->getReservationHandleDriver($data->IDRESERVATION);
			$ticketHandle		=	$this->ModelReservation->getReservationHandleVendorTicket($data->IDRESERVATION);
			$carHandle			=	$this->ModelReservation->getReservationHandleVendorCar($data->IDRESERVATION);
			
			switch($data->STATUS){
				case "-1"	:	$reservationStatusStr	=	'Cancel'; break;
				case "0"	:	$reservationStatusStr	=	'Unprocessed'; break;
				case "1"	:	$reservationStatusStr	=	'Admin Processed'; break;
				case "2"	:	$reservationStatusStr	=	'Scheduled'; break;
				case "3"	:	$reservationStatusStr	=	'On Process'; break;
				case "4"	:	$reservationStatusStr	=	'Done'; break;
				default		:	$reservationStatusStr	=	'Unprocessed'; break;
			}
			
			$sheet->setCellValue('A'.$rowNumber, $data->RESERVATIONTYPE);
			$sheet->setCellValue('B'.$rowNumber, $data->RESERVATIONTITLE);
			$sheet->setCellValue('C'.$rowNumber, $data->SOURCENAME);
			$sheet->setCellValue('D'.$rowNumber, $data->BOOKINGCODE);
			$sheet->setCellValue('E'.$rowNumber, $data->RESERVATIONDATESTART." ".$data->RESERVATIONTIMESTART.$reservationDateEnd);
			$sheet->setCellValue('F'.$rowNumber, $reservationStatusStr);
			$sheet->setCellValue('G'.$rowNumber, $data->CUSTOMERNAME);
			$sheet->setCellValue('H'.$rowNumber, ''.$data->CUSTOMERCONTACT);
			$sheet->setCellValue('I'.$rowNumber, $data->CUSTOMEREMAIL);
			$sheet->setCellValue('J'.$rowNumber, $paxStr);
			$sheet->setCellValue('K'.$rowNumber, $data->AREANAME);
			$sheet->setCellValue('L'.$rowNumber, $data->HOTELNAME);
			$sheet->setCellValue('M'.$rowNumber, $data->PICKUPLOCATION);
			$sheet->setCellValue('N'.$rowNumber, $data->DROPOFFLOCATION);
			$sheet->setCellValue('O'.$rowNumber, $data->REMARK);
			$sheet->setCellValue('P'.$rowNumber, $data->TOURPLAN);
			$sheet->setCellValue('Q'.$rowNumber, $data->SPECIALREQUEST);

			if($driverHandle){
				foreach($driverHandle as $driver){
					$driverHandleStr	.=	$data->DURATIONOFDAY == 1 ? $driver->PARTNERNAME."\n" : "[".$driver->SCHEDULEDATE."] ".$driver->PARTNERNAME."\n";
				}
			}
			
			if($ticketHandle){
				foreach($ticketHandle as $ticket){
					$ticketHandleStr	.=	$data->DURATIONOFDAY == 1 ? $ticket->PARTNERNAME."\n" : "[".$ticket->SCHEDULEDATE."] ".$ticket->PARTNERNAME."\n";
				}
			}
			
			if($carHandle){
				foreach($carHandle as $car){					
					$carHandleStr		.=	$data->DURATIONOFDAY == 1 ? $car->PARTNERNAME."\n" : "[".$car->SCHEDULEDATE."] ".$car->PARTNERNAME."\n";
				}
			}
			$sheet->setCellValue('R'.$rowNumber, $driverHandleStr.$ticketHandleStr.$carHandleStr);
			$rowNumber++;
			
		}
				
		$sheet->getStyle('A16:R'.($rowNumber-1))->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('R'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(12);
		$sheet->getColumnDimension('D')->setWidth(12);
		$sheet->getColumnDimension('E')->setWidth(16);
		$sheet->getColumnDimension('F')->setWidth(12);
		$sheet->getColumnDimension('G')->setWidth(28);
		$sheet->getColumnDimension('H')->setWidth(14);
		$sheet->getColumnDimension('I')->setWidth(20);
		$sheet->getColumnDimension('J')->setWidth(15);
		$sheet->getColumnDimension('K')->setWidth(18);
		$sheet->getColumnDimension('L')->setWidth(25);
		$sheet->getColumnDimension('M')->setWidth(25);
		$sheet->getColumnDimension('N')->setWidth(25);
		$sheet->getColumnDimension('O')->setWidth(30);
		$sheet->getColumnDimension('P')->setWidth(30);
		$sheet->getColumnDimension('Q')->setWidth(30);
		$sheet->getColumnDimension('R')->setWidth(20);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelReportReservationDetail_'.$startDateStr.'_'.$endDateStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
	
	public function excelVendorBook($encryptedVar){
		$this->load->model('ModelReservation');
		$this->load->model('MainOperation');
		$this->load->library('encryption');
		
		$arrDates				=	array();
		$decryptedVar			=	decodeStringKeyFunction(base64_decode($encryptedVar), DEFAULT_KEY_ENCRYPTION);
		$expDecryptedVar		=	explode("|", $decryptedVar);
		$idReservation			=	$expDecryptedVar[0];
		$status					=	$expDecryptedVar[1];
		$idSource				=	$expDecryptedVar[2];
		$idPartner				=	$expDecryptedVar[3];
		$bookingCode			=	$expDecryptedVar[4];
		$customerName			=	$expDecryptedVar[5];
		$transportStatus		=	$expDecryptedVar[6];
		$reservationTitle		=	$expDecryptedVar[7];
		$locationName			=	$expDecryptedVar[8];
		$orderBy				=	$expDecryptedVar[9];
		$orderType				=	$expDecryptedVar[10];
		$startDate				=	$expDecryptedVar[11];
		$endDate				=	$expDecryptedVar[12];
		$collectPaymentStatus	=	$expDecryptedVar[13];
		$year					=	$expDecryptedVar[14];
		$idReservationType		=	$expDecryptedVar[15];
		$arrDates				=	array();
		$strArrIdReservation	=	$typePartner	=	$partnerName	=	"";
		$idVendorType			=	0;

		if($startDate == "" && $endDate != "") $startDate	=	$endDate;
		if($startDate != "" && $endDate == "") $endDate		=	$startDate;
		
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
		
		$statusStr			=	"All Status";
		$statusTransportStr	=	"All Transport Status";
		$partnerStr			=	$idPartner == "" || $idPartner == 0 ? "All Partner" : $typePartner." - ".$partnerName;
		$reservationTypeStr	=	isset($idReservationType) && $idReservationType != "" && $idReservationType != 0 ? $this->MainOperation->getReservationTypeById($idReservationType) : "All Reservation Type";
		$sourceStr			=	isset($idSource) && $idSource != "" && $idSource != 0 ? $this->MainOperation->getSourceNameById($idSource) : "All Source";
		$orderByStr			=	$orderBy == 1 ? "Reservation Number" : "Reservation Date";
		$dateRangeStr		=	$startDateStr." to ".$endDateStr;
		$dataTable			=	$this->ModelReservation->getDataReservation(1, 999999, $idReservation, $arrDates, $status, $idSource, $bookingCode, $customerName, $locationName, $startDate, $endDate, $strArrIdReservation, $transportStatus, $reservationTitle, $orderBy, $orderType, $idPartner, $idVendorType, $collectPaymentStatus, $year, $idReservationType);
		
		switch($status){
			case "0"	:	$statusStr	=	"Unprocessed"; break;
			case "1"	:	$statusStr	=	"Processed By Admin"; break;
			case "2"	:	$statusStr	=	"Scheduled"; break;
			case "3"	:	$statusStr	=	"On Process"; break;
			case "4"	:	$statusStr	=	"Done"; break;
			case "-1"	:	$statusStr	=	"Cancel"; break;
		}
		
		switch($transportStatus){
			case "1"	:	$statusTransportStr	=	"With Transport"; break;
			case "-1"	:	$statusTransportStr	=	"Without Transport"; break;
		}

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
		$sheet->setCellValue('A2', 'Vendor/Partner reservation details');
		$sheet->getStyle('A1:A2')->getFont()->setBold( true );
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:G1');
		$sheet->mergeCells('A2:G2');
		
		$sheet->setCellValue('A4', 'Reservation Type : '.$reservationTypeStr);
		$sheet->setCellValue('A5', 'Source : '.$sourceStr);
		$sheet->setCellValue('A6', 'Booking Code (Contains) : '.$bookingCode);
		$sheet->setCellValue('A7', 'Customer Name (Contains) : '.$customerName);
		$sheet->setCellValue('A8', 'Reservation Title (Contains) : '.$reservationTitle);
		$sheet->setCellValue('A9', 'Transport Status : '.$statusTransportStr);
		$sheet->setCellValue('A10', 'Location (Contains) : '.$locationName);
		$sheet->setCellValue('A11', 'Date Period : '.$dateRangeStr);
		$sheet->setCellValue('A12', 'Status : '.$statusStr);
		$sheet->setCellValue('A13', 'Partner : '.$partnerStr);
		$sheet->setCellValue('A14', 'Data Order By : '.$orderByStr.' '.$orderType);

		$sheet->mergeCells('A4:G4');
		$sheet->mergeCells('A5:G5');
		$sheet->mergeCells('A6:G6');
		$sheet->mergeCells('A7:G7');
		$sheet->mergeCells('A8:G8');
		$sheet->mergeCells('A9:G9');
		$sheet->mergeCells('A10:G10');
		$sheet->mergeCells('A11:G11');
		$sheet->mergeCells('A12:G12');
		$sheet->mergeCells('A13:G13');
		$sheet->mergeCells('A14:G14');
		
		$sheet->setCellValue('A16', 'Booking Code');
		$sheet->setCellValue('B16', 'Reservation Title');
		$sheet->setCellValue('C16', 'Date Time');
		$sheet->setCellValue('D16', 'Guest Name');
		$sheet->setCellValue('E16', 'Pax');
		$sheet->setCellValue('F16', 'Hotel/Pickup');
		$sheet->setCellValue('G16', 'Remark');

		$sheet->getStyle('A16:G16')->getFont()->setBold( true );
		$sheet->getStyle('A16:G16')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A16:G16')->getAlignment()->setVertical('center');
		$rowNumber	=	17;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		foreach($dataTable['data'] as $data){
			$reservationDateEnd		=	$data->RESERVATIONDATEEND != $data->RESERVATIONDATESTART ? "\nTo\n".$data->RESERVATIONDATEEND." ".$data->RESERVATIONTIMEEND : "";
			$paxStr					=	" ".$data->NUMBEROFADULT;
			
			if($data->NUMBEROFCHILD == 0 && $data->NUMBEROFINFANT != 0){
				$paxStr	.=	"+0+".$data->NUMBEROFINFANT;
			} else if($data->NUMBEROFCHILD != 0 && $data->NUMBEROFINFANT == 0){
				$paxStr	.=	"+".$data->NUMBEROFINFANT."+0";
			} else if($data->NUMBEROFCHILD != 0 && $data->NUMBEROFINFANT != 0){
				$paxStr	.=	"+".$data->NUMBEROFINFANT."+".$data->NUMBEROFCHILD;
			}
			
			$sheet->setCellValue('A'.$rowNumber, $data->BOOKINGCODE);
			$sheet->setCellValue('B'.$rowNumber, $data->RESERVATIONTITLE);
			$sheet->setCellValue('C'.$rowNumber, $data->RESERVATIONDATESTART." ".$data->RESERVATIONTIMESTART.$reservationDateEnd);
			$sheet->setCellValue('D'.$rowNumber, $data->CUSTOMERNAME);
			$sheet->setCellValue('E'.$rowNumber, $paxStr);
			$sheet->setCellValue('F'.$rowNumber, $data->HOTELNAME."\n".$data->PICKUPLOCATION);
			$sheet->setCellValue('G'.$rowNumber, $data->REMARK);
			
			$rowNumber++;
		}
				
		$sheet->getStyle('A16:G'.($rowNumber-1))->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		$sheet->setBreak('G'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
		
		$sheet->getColumnDimension('A')->setWidth(16);
		$sheet->getColumnDimension('B')->setWidth(40);
		$sheet->getColumnDimension('C')->setWidth(18);
		$sheet->getColumnDimension('D')->setWidth(25);
		$sheet->getColumnDimension('E')->setWidth(10);
		$sheet->getColumnDimension('F')->setWidth(40);
		$sheet->getColumnDimension('G')->setWidth(40);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelVendorPartnerReservationDetail_'.$startDateStr.'_'.$endDateStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
	
	private function createPdfReservationVoucher($arrDataVoucher, $arrAdditional){
		$arrDataVoucher	=	array_merge($arrDataVoucher, $arrAdditional);
		$voucherCode	=	$arrDataVoucher['VOUCHERCODE'];
		$paxDetails		=	"";
		
		if($arrDataVoucher['PAXTOTAL1'] > 0){
			$paxDetails	.=	$paxDetails == "" ? "" : ", ";
			$paxDetails	.=	$arrDataVoucher['PAXTOTAL1']." ".$arrDataVoucher['PAXNAME1'];
		}

		if($arrDataVoucher['PAXTOTAL2'] > 0){
			$paxDetails	.=	$paxDetails == "" ? "" : ", ";
			$paxDetails	.=	$arrDataVoucher['PAXTOTAL2']." ".$arrDataVoucher['PAXNAME2'];
		}

		if($arrDataVoucher['PAXTOTAL3'] > 0){
			$paxDetails	.=	$paxDetails == "" ? "" : ", ";
			$paxDetails	.=	$arrDataVoucher['PAXTOTAL3']." ".$arrDataVoucher['PAXNAME3'];
		}

		$arrDataVoucher['PAXDETAILS']	=	$paxDetails;
		if(PRODUCTION_URL){
			$fileNameVoucher	=	$voucherCode.".pdf";
		} else {
			$fileNameVoucher	=	"Development_Test.pdf";
		}
		
		$htmlBodyPdf	=	$this->load->view('pdf/reservationVoucher', $arrDataVoucher, TRUE);
		$html2pdf		=	new \Spipu\Html2Pdf\Html2Pdf('L', 'A5', 'en');
		$html2pdf->writeHTML($htmlBodyPdf);
		$html2pdf->output(PATH_VOUCHER_FILE.$fileNameVoucher, 'F');			
		return $fileNameVoucher;
	}
	
	public function deleteReservationVoucher(){
		$this->load->model('MainOperation');
		$idReservationVoucher	=	validatePostVar($this->postVar, 'idData', true);
		$arrUpdate				=	array("STATUS" => -1);
		$updateResult			=	$this->MainOperation->updateData("t_reservationvoucher", $arrUpdate, "IDRESERVATIONVOUCHER", $idReservationVoucher);
		
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Voucher has been deleted"));	
	}
	
	public function getDetailPayment(){
		$this->load->model('ModelReservation');
		
		$idReservation		=	validatePostVar($this->postVar, 'idReservation', true);
		$detailReservation	=	$this->ModelReservation->getDetailStrReservation($idReservation);
		
		if(!$detailReservation) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found, please refresh your data"));

		$durationOfDay		=	$detailReservation['DURATIONOFDAY'];
		$dateStartSchedule	=	$detailReservation['RESERVATIONDATEVALUE'];
		$arrDateSchedule	=	array();
		
		for($iDay=0; $iDay<$durationOfDay; $iDay++){
			$dateSchedule		=	date('Ymd', strtotime($dateStartSchedule . ' +'.$iDay.' day'));
			$arrDateSchedule[]	=	$dateSchedule;
		}
		
		$reservationPaymentList	=	$this->ModelReservation->getReservationPaymentList($idReservation);
		setResponseOk(array("token"=>$this->newToken, "detailReservation"=>$detailReservation, "reservationPaymentList"=>$reservationPaymentList, "arrDateSchedule"=>implode(',', $arrDateSchedule)));
	}

	public function addReservationPayment(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$idReservation		=	validatePostVar($this->postVar, 'idReservationCreatePayment', true);
		$idPaymentMethod	=	validatePostVar($this->postVar, 'optionPaymentMethod', true);
		$paymentStatus		=	validatePostVar($this->postVar, 'optionPaymentStatus', true);
		$paymentMethodName	=	validatePostVar($this->postVar, 'paymentMethodName', true);
		$description		=	validatePostVar($this->postVar, 'descriptionPayment', false);
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
		$procInsertPymt		=	$this->MainOperation->addData('t_reservationpayment', $arrInsert);
		
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
				$dataDriverSchedule	=	$this->ModelReservation->checkDataDriverSchedule($idReservation, $dateCollect);
				
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
					$partnerName		=	$dataDriverSchedule['PARTNERNAME'];					
				}
			}
			
			if($idPartnerType == 1 && $idVendorCollect == 0){
				$dataVendorSchedule	=	$this->ModelReservation->checkDataVendorSchedule($idReservation);
				if($dataVendorSchedule){
					$idVendorCollect	=	$dataVendorSchedule['IDVENDOR'];
					$partnerName		=	$dataVendorSchedule['PARTNERNAME'];
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
				"STATUS"				=>	0,
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
		} else {
			$idVendorCollect	=	$idDriverCollect	=	0;
			$dateCollect		=	'';
		}
		
		$insertUpdateData		=	array(
			"IDRESERVATIONPAYMENT"	=>	$idReservationPayment,
			"IDPAYMENTMETHOD"		=>	$idPaymentMethod,
			"PAYMENTMETHODNAME"		=>	$paymentMethodName,
			"DESCRIPTION"			=>	$description,
			"AMOUNTCURRENCY"		=>	$paymentCurrency,
			"AMOUNT"				=>	$pricePayment,
			"EXCHANGECURRENCY"		=>	$nomExchangeCurr,
			"AMOUNTIDR"				=>	$amountIDR,
			"ISUPSELLING"			=>	$isUpsellingPayment,
			"STATUS"				=>	$paymentStatus,
			"USERINPUT"				=>	$userAdminName,
			"DATETIMEINPUT"			=>	date('d M Y H:i'),
			"EDITABLE"				=>	1,
			"DELETABLE"				=>	1,
			"IDVENDORCOLLECT"		=>	$idVendorCollect,
			"IDDRIVERCOLLECT"		=>	$idDriverCollect,
			"DATECOLLECT"			=>	$dateCollect
		);
		setResponseOk(array("token"=>$this->newToken, "insertUpdateData"=>$insertUpdateData, "msg"=>"Reservation payment data has been saved"));
	}
	
	public function updateReservationPayment(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$idReservationPymnt	=	validatePostVar($this->postVar, 'idReservationPayment', true);
		$idPaymentMethod	=	validatePostVar($this->postVar, 'optionPaymentMethod', true);
		$paymentStatus		=	validatePostVar($this->postVar, 'optionPaymentStatus', true);
		$paymentMethodName	=	validatePostVar($this->postVar, 'paymentMethodName', true);
		$description		=	validatePostVar($this->postVar, 'descriptionPayment', false);
		$isUpsellingPayment	=	validatePostVar($this->postVar, 'checkboxUpsellingPayment', false);
		$idDriverCollect	=	validatePostVar($this->postVar, 'optionDriverCollect', false);
		$idDriverCollect	=	!isset($idDriverCollect) || $idDriverCollect == "" ? 0 : $idDriverCollect;
		$idVendorCollect	=	validatePostVar($this->postVar, 'optionVendorCollect', false);
		$idVendorCollect	=	!isset($idVendorCollect) || $idVendorCollect == "" ? 0 : $idVendorCollect;
		$dateCollect		=	validatePostVar($this->postVar, 'optionDateCollect', false);
		$paymentCurrency	=	validatePostVar($this->postVar, 'paymentCurrency', true);
		$editablePayment	=	validatePostVar($this->postVar, 'editablePayment', false);
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
		$detailReservationPayment	=	$this->ModelReservation->getDetailReservationPayment($idReservationPymnt);
		
		if(!$detailReservationPayment) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Failed to delete data. The data you selected no longer exists"));
		
		$deletablePayment	=	$detailReservationPayment['DELETABLE'];

		if($deletablePayment == 0){
			$arrUpdate		=	array(
				"AMOUNTCURRENCY"	=>	$paymentCurrency,
				"AMOUNT"			=>	$pricePayment,
				"AMOUNTIDR"			=>	$amountIDR,
				"USERUPDATE"		=>	$userAdminName,
				"DATETIMEUPDATE"	=>	date('Y-m-d H:i:s')
			);
		} else {
			$arrUpdate		=	array(
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
			if(!isset($dateCollect) || $dateCollect == "" || !validateDate($dateCollect)){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select a valid date to create collect payment"));
			}

			$idPartnerType		=	$idPaymentMethod == 2 ? 2 : 1;
			$idVendorCollect	=	$idPaymentMethod == 2 ? 0 : $idVendorCollect;
			$idDriverCollect	=	$idPaymentMethod == 2 ? $idDriverCollect : 0;
			$idReservation		=	$detailReservationPayment['IDRESERVATION'];
			$statusCollected	=	0;
			$dateStatusCollected=	false;
			$partnerName		=	$idPaymentMethod == 2 ? $this->MainOperation->getDriverNameById($idDriverCollect) : $this->MainOperation->getVendorNameById($idVendorCollect);
			
			if($idPartnerType == 2){
				$dataDriverSchedule	=	$this->ModelReservation->checkDataDriverSchedule($idReservation, $dateCollect);
				
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
				$dataVendorSchedule	=	$this->ModelReservation->checkDataVendorSchedule($idReservation);
				if($dataVendorSchedule){
					$idVendorCollect	=	$dataVendorSchedule['IDVENDOR'];
					$partnerName		=	$dataVendorSchedule['PARTNERNAME'];
				}
			}
			
			$newFinanceScheme	=	$idPaymentMethod == 2 ? $this->MainOperation->getNewFinanceSchemeDriver($idDriverCollect) : $this->MainOperation->getNewFinanceSchemeVendor($idVendorCollect);
			$isCollectExist		=	$this->ModelReservation->isCollectPaymentExist($idReservationPymnt);
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
				
				$idCollectPayment				=	$this->ModelReservation->getIdCollectPaymentByIdReservationPayment($idReservationPymnt);
				$arrUpdateCollectPaymentHistory	=	array("DESCRIPTION" => "Collect payment is set to ".$partnerName);
				$this->MainOperation->updateData("t_collectpayment", $arrInsUpdCollect, "IDRESERVATIONPAYMENT", $idReservationPymnt);
				$this->MainOperation->updateData('t_collectpaymenthistory', $arrUpdateCollectPaymentHistory, array("IDCOLLECTPAYMENT" => $idCollectPayment, "STATUS" => 0));
			}
		} else {
			$idVendorCollect=	$idDriverCollect	=	0;
			$dateCollect	=	'';
			$procDelete		=	$this->MainOperation->deleteData('t_collectpayment', array("IDRESERVATIONPAYMENT" => $idReservationPymnt));
		}
		
		$procUpdatePymt		=	$this->MainOperation->updateData('t_reservationpayment', $arrUpdate, "IDRESERVATIONPAYMENT", $idReservationPymnt);		
		if(!$procUpdatePymt['status']) switchMySQLErrorCode($procUpdatePymt['errCode'], $this->newToken);
		
		$insertUpdateData	=	array(
			"IDRESERVATIONPAYMENT"	=>	$idReservationPymnt,
			"IDPAYMENTMETHOD"		=>	$idPaymentMethod,
			"PAYMENTMETHODNAME"		=>	$paymentMethodName,
			"DESCRIPTION"			=>	$description,
			"AMOUNTCURRENCY"		=>	$paymentCurrency,
			"AMOUNT"				=>	$pricePayment,
			"EXCHANGECURRENCY"		=>	$nomExchangeCurr,
			"AMOUNTIDR"				=>	$amountIDR,
			"ISUPSELLING"			=>	$isUpsellingPayment,
			"STATUS"				=>	$paymentStatus,
			"USERINPUT"				=>	$userAdminName,
			"DATETIMEINPUT"			=>	date('d M Y H:i'),
			"EDITABLE"				=>	$editablePayment,
			"DELETABLE"				=>	$deletablePayment,
			"IDVENDORCOLLECT"		=>	$idVendorCollect,
			"IDDRIVERCOLLECT"		=>	$idDriverCollect,
			"DATECOLLECT"			=>	$dateCollect
		);
		
		setResponseOk(array("token"=>$this->newToken, "insertUpdateData"=>$insertUpdateData, "msg"=>"Reservation payment data has been updated"));
	}
	
	public function deleteReservationPayment(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$idReservationPayment		=	validatePostVar($this->postVar, 'idData', true);
		$detailReservationPayment	=	$this->ModelReservation->getDetailReservationPayment($idReservationPayment);
		
		if(!$detailReservationPayment) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Failed to delete data. The data you selected no longer exists"));
		
		$deletableStatus			=	$detailReservationPayment['DELETABLE'];
		$idPaymentMethod			=	$detailReservationPayment['IDPAYMENTMETHOD'];
		$idCollectPayment			=	$detailReservationPayment['IDCOLLECTPAYMENT'];
		$newFinanceScheme			=	$detailReservationPayment['NEWFINANCESCHEME'];

		if($deletableStatus != 1) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Failed to delete data. This data is not allowed to be deleted"));
		
		if($idPaymentMethod == 2 || $idPaymentMethod == 7){
			$detailCollectPayment	=	$this->ModelReservation->getDetailCollectPayment($idCollectPayment);
			$statusSettlementCollect=	$detailCollectPayment['STATUSSETTLEMENTREQUEST'];
			
			if($statusSettlementCollect == 2 && $newFinanceScheme == 1){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Failed to delete data. This payment (Collect Payment) has been completed"));
			}
		}

		$procDelete	=	$this->MainOperation->deleteData('t_reservationpayment', array("IDRESERVATIONPAYMENT" => $idReservationPayment));
		
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
	
	private function updateWebappStatisticTags(){
		if(PRODUCTION_URL){
			$this->load->model('MainOperation');

			$totalUnprocessedReservation=	$this->MainOperation->getTotalUnprocessedReservation();
			$totalUndeterminedSchedule	=	$this->MainOperation->getTotalUndeterminedSchedule();
			try {
				$factory				=	(new Factory)
											->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
											->withDatabaseUri(FIREBASE_RTDB_URI);
				$database				=	$factory->createDatabase();
				$database->getReference(FIREBASE_RTDB_MAINREF_NAME."unprocessedReservation")->set($totalUnprocessedReservation);
				$database->getReference(FIREBASE_RTDB_MAINREF_NAME."undeterminedSchedule")->set($totalUndeterminedSchedule); 
			} catch (Exception $e) {
			}
		}
		
		return true;
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
						if($partnerTokenFCM != "") $this->fcm->sendPushNotification($partnerTokenFCM, $titleMsg, $body, $additionalArray);
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
	
	public function fixScheduleMailRating(){
		$arrIdReservation	=	array(
			41813,40853,41737,41736,41735,41123,41557,41733,41732,41731,41360,41630,40037,41866,41754,41753,41752,41626,41629,41742,41382,39931,39874,31617,39201,41180,41678,41677,39356,41674,39493,39488,41286,41019,38853,41714,31697,41239,38676,38722,41006,38741,41287,41806,35666,41726,41487,41488,36511,40513,41792,40619,40607,41803,41542,41807,36735,41808,41871,41812,41801,40674,41392,41768,41767,41764,38234,40797,41872,40242,35940,40319,40816,40337,41601,41761,41646,38181,41552,41129,41394,39259,41481,37369,41570,41375,38488,41472,41566,34853,41656,33996,40590,41771,40238,41763,41759,41746,40956,41875,41930,41784,41874,40454,40588,40587,40585,40538,40536,40514,40659,41799,41698,40998,41863,41684,39732,32467,41091,41125,37543,39151,41020,41490,39056,37479,39311,41522,40467,37006,39513,39402,39482,41249,40430,41468,40975,39816,40966,38745,39815,40900,40895,41260,39814,39576,38196,40214,39775,41401,37803,40304,40709,41652,20798,34501,41757,36398,34849,40600,36635,35787,26148,41647,41755,23857,32121,40855,41478,39999,41955,29648,41062,41693,40962,41131,39763,39258,41201,41366,38696,39257,41377,41383,38747,41625,41258,34848,41247,41631,39064,41224,35907,41204,38617,40934,36590,41529,40738,40711,40515,41773,40707,40465,40699,40265,41514,41774,40504,41512,40209,37488,40810,40822,40520,40231,41800,41635,41651,34683,38856,41370,40532,41943,41942,41648,40516,41341,41854,41248,40661,41156,39498,41065,41769,41691,41692,32415,40875,41770,40248,40250,41241,34594,40698,40229,41221,41213,40453,40251,40907,41565,38246,38381,41496,37333,37751,41376,38123,41986,38245,37909,41390,38141,41572,38255,40441,41243,40001,41232,41598,41508,39652,41765,39495,40206,41177,40236,40820,36539,37614,41395,41511,38038,39131,41484,39085,41124,38849,41357,38343,41548,41356,39767,39848,40542,21396,41372,36746,38506,36740,41389,40964,41025,38241,38821,40650,39636,40541,40898,41787,40154,41371,40459,40194,41132,40794,41485,41670,37903,33945,40997,40368,40446,41715,38753,39687,38072,41393,40955,39118,41525,41802,41216,22597,38086,40089,41347,41009,41669,40951,33724,37666,41111,40182,36234,40169,39665,35960,38503,41620,39129,37236,38073,38965,38128,39189,38846,40654,41796,40521,38758,41644,41640,38508,35751,39206,41417,39455,41492,39360,39343,40234,40793,37448,40366,40792,40503,37620,41497,19555,31726,39836,41477,40199,40056,39912,25416,30773,40791,41772,35632,29455,41471,34763,41196,37973,37981,39481,41284,41185,41058,41061,41672,38793,39589,41219,40145,37826,34279,39251,38481,41407,41037,41469,38505,41396,32602,34280,39806,35277,41663,41623,36328,41231,24320,38848,39502,37305,40057,37480,41178,40802,37486,40954,41675,40985,39744,39277,39703,41584,38609,39762,41809,41276,37825,31922,41624,40463,40502,41070,37446,38397,38479,38913,38971,40402,40624,41545,38024,41544,41543,41426,17519,39504,39503,40148,41031,40000,40800,41069,40009,40069,40909,41661,38723,40856,41486,41785,41081,38909,29542,29981,35888,41109,41597,40266,41143,41240,39782,40610,28178,41583,39875,40676,39071,41650,32139,40573,39867,36873,40565,40673,24647,41234,37524,41509,29237,34147,37487,38177,40566,40535,40567,25187,41373,39866,41117,36523,40618,40433,40432,39998,38779,41011,41002,40778,41482,41788,40572,37523,28257,28803,38328,34360,37735,40023,38264,37312,40537,39492,41751,41560,40540,38784,37163,40897,41238,41119,40988,40423,23333,37531,41662,39511,40967,40210,39458,41523,37206,37927,41422,41556,36874,41540,40976,40969,41367,33374,40180,41786,41795,40564,41368,41369,33574,38743,26821,39232,40993,41574,39233,38652,40958,41149,40965,41098,38142,38813,41526,41246,40710,40803,39746,41358,39138,38914,39409,37304,39390,38656,41524,41007,41110,41004,41340,39846,41504,35736,38966,40706,41655,39294,22035,35041,40389,38851,40170,41783,41245,41046,35401,40178,41596,37891,41282,39515,40678,41491,23541,38383,40230,40625,39638,41080,41579,40677,38807,41152,36760,41499,41154,41386,33689,39079,39480,41142,41515,39547,40774,41029,41791,40059,41550,41030,41032,37655,41115,40176,41549,37737,41233,41087,40136,38501,38873,41716,41645,41717,36763,39615,23976,41555,41346,39805,40006,40122,37685,37807,41553,31212,40063,33605,41104,41093,41235,27789,40725,41155,39704,38740,39070,40701,40869,39043,41521,36551,37661,39044,39872,39996,41573,41657,40147,38785,39303,39833,33938,41776,36667,36757,38912,41126,39535,37928,40166,39988,39796,40404,41381,41116,34457,37979,39989,41617,39306,39987,39990,31074,40862,41385,38025,38523,40204,40278,41108,39227,40308,41003,39440,40114,33664,29992,40649,41679,39991,33537,41018,40702,41192,37532,41306,32894,39094,41476,33456,40660,36039,38900,41636,41641,39611,40311,40628,38680,37724,35952,36575,38055,40090,38777,37667,21370,40374,39237,41782,41079,27581,41718,40396,40395,39512,40973,41148,13223,40655,39514,40506,41090,26311,34852,41026,40531,41365,40193,31913,41218,41619,38518,31718,41387,15971,37578,37525,41262,40450,38336,38521,41141,37855,30406,40232,37022,35770,38871,38869,27732,41217,21948,35761,23801,37687,39290,24250,40247,24346,41642,41402,31671,36899,30040,30969,29832,40680,18084,41780,21488,40534,41236,34037,30219,40682,28413,33873,39338,41558,39501,39629,39758,41789,41342,35719,36907,41024,40143,35217,40996,35071,35548,35552,37675,27560,21769,40652,39663,41539,35660,41199,40228,36211,40708,35460,33447,39074,26211,40268,37264,24579,31093,38855,36662,37968,28247,28812,28414,38649,41638,35596,41214,30841,36902,40658,36092,27134,39626,40505,22753,29320,41667,35011,40722,41227,40937,41945,41944,41226,26226,37167,37174,37166,36156,37164,39150,39152,38054,28383,15668,35652,39585,15669,30206,41391,38860,25127,28103,41139,41114,40686,40010,40392,40617,38619,39839,39314,38207,19974,37691,27135,32554,21555,33169,27084,15256,31530,32775,29543,31534,24105,30975,22533,31980,27939,28026,27550,29468,26672,29469,18006,21200,4856,17401,4787,18571,19243,20371,2522,20867,21054,21120,21178,6520,18339,28151,30599,32918,31270,41827,41826,41825,41822,41820,41817,41814,41830,41828,41816,41831,41833,41824,41823,41821,41819,41818,41815,41835,41834,41836,41837,41838,41839,41840,41841,41842,41933,41843,41845,41853,41852,41851,41850,41907,41847,41846,41855,41856,41857,41861,41860,41859,41766,36624,33426,41778,21943,41779,41910,41912,41921,41922,41923,41906,41952,41920,41919,41917,41916,41915,41911,41909,41908,41905,41904,41902,41901,41900,41899,41898,41897,41896,41895,41894,41892,41890,41889,41888,41886,41885,41884,41883,41882,41881,41880,41879,41877,41876,41873,41870,41869,41868,41867,41864,41862,33093,41927,41926,41931,41941,41940,41939,41938,41937,41936,41935,41934,41947,41932,41929,41928,41865,41948,41949,41950,41951,41962,41954,41953,41290,9701,41960,41959,41958,41957,41956,41963,41964,41925,41965,41969,41968,41966,41970,41981,41980,41979,41978,41976,41985,41974,41972,41983,41984
		);
		
		foreach($arrIdReservation as $idReservation){
			$this->processReservationMailRating($idReservation);
		}
		
		echo "OK";
	}
	
	private function processReservationMailRating($idReservation){
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		
		$detailReservationMailRating	=	$this->ModelReservation->getDetailReservationMailRating($idReservation);
		
		if($detailReservationMailRating){
			$idSource					=	$detailReservationMailRating['IDSOURCE'];
			$customerEmail				=	$detailReservationMailRating['CUSTOMEREMAIL'];
			$totalReservationDetails	=	$detailReservationMailRating['TOTALRESERVATIONDETAILS'];
			$maxDurationHour			=	$detailReservationMailRating['MAXDURATIONHOUR'];
			$arrProductUrlReview		=	explode(',', $detailReservationMailRating['ARRPRODUCTURL']);
			
			if($totalReservationDetails <= 0 || $maxDurationHour <= 0 || count($arrProductUrlReview) <= 0 || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)){
				$this->MainOperation->deleteData('t_reservationmailreview', array('IDRESERVATION' => $idReservation));
			} else {
				$reservationDateTimeStart	=	$detailReservationMailRating['RESERVATIONDATETIMESTART'];
				$dateTimeScheduleReview		=	date('Y-m-d H:i:s', strtotime($reservationDateTimeStart. ' + '.(substr($reservationDateTimeStart, 11, 5) == '00:00' ? 12 : $maxDurationHour).' hours'));
				$arrDurationHours			=	explode(',', $detailReservationMailRating['ARRDURATIONHOUR']);
				$arrProductTitle			=	explode('|', $detailReservationMailRating['ARRPRODUCTTITLE']);
				$idUnique					=	base64_encode(encodeStringKeyFunction($idReservation, DEFAULT_KEY_ENCRYPTION));
				$indexArraySearch			=	array_search($maxDurationHour, $arrDurationHours);
				$productTitle				=	$arrProductTitle[$indexArraySearch];
				$productUrlReview			=	"";
				
				if($idSource == 1){
					$productUrlReview	=	REVIEW_URL_DEFAULT_KLOOK;
				} else {
					foreach($arrProductUrlReview as $strProductUrlReview){
						if($strProductUrlReview != "" && $strProductUrlReview != "-")	$productUrlReview	=	$strProductUrlReview;
					}
				}
				
				$productUrlReview	=	$productUrlReview == "" ? REVIEW_URL_DEFAULT_VIATOR : $productUrlReview;
				$arrInsertUpdate	=	array(
					"IDRESERVATION"		=>	$idReservation,
					"IDUNIQUE"			=>	$idUnique,
					"DATETIMESTART"		=>	$reservationDateTimeStart,
					"DATETIMESCHEDULE"	=>	$dateTimeScheduleReview,
					"PRODUCTTITLE"		=>	$productTitle,
					"URLREVIEW"			=>	$productUrlReview,
					"STATUSSEND"		=>	0
				);
				
				if(strtotime($dateTimeScheduleReview) > strtotime("now") && $productUrlReview != '-'){
					$isDataScheduleReviewExist	=	$this->ModelReservation->isDataScheduleReviewExist($idReservation);
					$productUrlReview			=	$productUrlReview == "" || $productUrlReview == "-" ? MAILREVIEW_URL_DEFAULT : $productUrlReview;
					if(!$isDataScheduleReviewExist){
						$this->MainOperation->addData('t_reservationmailreview', $arrInsertUpdate);
					} else {
						$statusSendSchedule		=	$isDataScheduleReviewExist['STATUSSEND'];
						if($statusSendSchedule == 0){
							$this->MainOperation->updateData('t_reservationmailreview', $arrInsertUpdate, 'IDRESERVATION', $idReservation);
						}
					}
				}				
			}
		}
		return true;
	}

	public function dataDetails($bookingCode = false){
		$this->load->model('ModelReservation');
		
		header('Content-Type: application/json');
		if (!$bookingCode) {
            http_response_code(400);
            echo json_encode([
                'status'	=>	'error',
                'message'	=>	'Booking code is required'
            ]);
            return;
        }
		
		$dataDetails	=	$this->ModelReservation->getDetailReservationByBookingCode($bookingCode);

        if (!$dataDetails) {
            http_response_code(404);
            echo json_encode([
                'status'	=>	'error',
                'message'	=>	'No data found'
            ]);
            return;
        }
		
		$idReservation			=	$dataDetails['IDRESERVATION'];
		$dataHandleDriver		=	$this->ModelReservation->getReservationHandleDriver($idReservation);
		$dataHandleVendorTicket	=	$this->ModelReservation->getReservationHandleVendorTicket($idReservation);
		$dataReservationDetails	=	$this->ModelReservation->getListReservationDetails($idReservation);
		$arrDataHandleDriver	=	$arrDataHandleVendorTicket	=	[];
		$transportType			=	'';
		
		if($dataHandleDriver){
			foreach($dataHandleDriver as $keyHandleDriver){
				$arrDataHandleDriver[]	=	[
					'scheduleDate'		=>	$keyHandleDriver->SCHEDULEDATE,
					'driverName'		=>	$keyHandleDriver->PARTNERNAME,
					'driverPhoneNumber'	=>	$keyHandleDriver->DRIVERPHONENUMBER,
					'carBrandModel'		=>	$keyHandleDriver->CARBRANDMODEL,
					'carNumberPlate'	=>	$keyHandleDriver->CARNUMBERPLATE
				];
			}
		}
		
		if($dataHandleVendorTicket){
			foreach($dataHandleVendorTicket as $keyHandleVendorTicket){
				$arrDataHandleVendorTicket[]	=	[
					'scheduleDate'		=>	$keyHandleVendorTicket->SCHEDULEDATE,
					'vendorName'		=>	$keyHandleVendorTicket->PARTNERNAME,
					'vendorAddress'		=>	$keyHandleVendorTicket->ADDRESS
				];
			}
		}
		
		if($dataReservationDetails){
			foreach($dataReservationDetails as $keyReservationDetails){
				if(intval($keyReservationDetails->IDDRIVERTYPE) != 0){
					$transportType	=	$keyReservationDetails->DRIVERTYPE;
					break;
				}
			}
		}
		
		$dataDetails['durationOfDay']		=	intval($dataDetails['durationOfDay']);
		$dataDetails['numberOfAdult']		=	intval($dataDetails['numberOfAdult']);
		$dataDetails['numberOfChild']		=	intval($dataDetails['numberOfChild']);
		$dataDetails['numberOfInfant']		=	intval($dataDetails['numberOfInfant']);
		$dataDetails['handleDriver']		=	$arrDataHandleDriver;
		$dataDetails['handleVendorTicket']	=	$arrDataHandleVendorTicket;
		$dataDetails['transportStatus']		=	intval($dataDetails['transportStatus']);
		$dataDetails['transportType']		=	$transportType;
		unset($dataDetails['IDRESERVATION']);
        echo json_encode([
            'status'	=>	'success',
            'data'		=>	$dataDetails
        ]);
        http_response_code(200);
	}
}