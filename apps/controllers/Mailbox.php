<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class Mailbox extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(2);

		if($functionName != "getPreviewMail" && $_SERVER['REQUEST_METHOD'] === 'POST'){
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
	
	public function getDataMailbox(){

		$this->load->model('ModelMailbox');
		$this->load->model('MainOperation');

		$page				=	validatePostVar($this->postVar, 'page', true);
		$status				=	validatePostVar($this->postVar, 'status', false);
		$idReservationType	=	validatePostVar($this->postVar, 'idReservationType', false);
		$idSource			=	validatePostVar($this->postVar, 'idSource', false);
		$startDate			=	validatePostVar($this->postVar, 'startDate', true);
		$startDate			=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate			=	$startDate->format('Y-m-d');
		$endDate			=	validatePostVar($this->postVar, 'endDate', true);
		$endDate			=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate			=	$endDate->format('Y-m-d');
		$reservationDate	=	validatePostVar($this->postVar, 'reservationDate', false);
		$searchKeyword		=	validatePostVar($this->postVar, 'searchKeyword', false);

		if(isset($reservationDate) && $reservationDate != ""){
			$reservationDate=	DateTime::createFromFormat('d-m-Y', $reservationDate);
			$reservationDate=	$reservationDate->format('Y-m-d');
		}

		$dataTable		=	$this->ModelMailbox->getDataMailbox($page, 25, $status, $idReservationType, $idSource, $startDate, $endDate, $reservationDate, $searchKeyword);
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
	public function getDetailMailbox(){
		$this->load->model('ModelMailbox');
		
		$idMailbox		=	validatePostVar($this->postVar, 'idMailbox', true);
		$detailData		=	$this->ModelMailbox->getDetailMailbox($idMailbox);
		
		if(!$detailData) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found"));

		$specialCasePrice	=	"";
		switch($detailData['IDSOURCE']){
			case 22	:	$specialCasePrice	=	"Nett price for Get Your Guide is <b>70%</b> of the price shown in the email"; break;
			default	:	break;
		}

		setResponseOk(array("token"=>$this->newToken, "detailData"=>$detailData, "specialCasePrice"=>$specialCasePrice));	
	}
	
	public function getPreviewMail($fileName){
		$fileContent		=	file_get_contents(PATH_EMAIL_HTML_FILE.$fileName);
		echo $fileContent;
	}
	
	public function saveReservation(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('ModelMailbox');
		
		$idMailbox				=	validatePostVar($this->postVar, 'idMailbox', true);
		$idSource				=	validatePostVar($this->postVar, 'idSource', true);
		$reservationType		=	validatePostVar($this->postVar, 'reservationType', true);
		$selfDriveStatus		=	validatePostVar($this->postVar, 'selfDriveStatus', true);
		$reservationTitle		=	validatePostVar($this->postVar, 'reservationTitle', true);
		$detailsProductURL		=	validatePostVar($this->postVar, 'detailsProductURL', false);
		$duplicateNumber		=	validatePostVar($this->postVar, 'duplicateNumber', true);
		$durationOfDay			=	validatePostVar($this->postVar, 'durationOfDay', true);
		$reservationDate		=	validatePostVar($this->postVar, 'reservationDate', true);
		$reservationDateDT		=	DateTime::createFromFormat('d-m-Y', $reservationDate);
		$reservationDateStr		=	$reservationDateDT->format('d M Y');
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
		$hotelName				=	validatePostVar($this->postVar, 'hotelName', false);
		$idArea					=	validatePostVar($this->postVar, 'optionPickUpArea', true);
		$pickUpLocation			=	validatePostVar($this->postVar, 'pickUpLocation', false);
		$pickUpLocationUrl		=	validatePostVar($this->postVar, 'pickUpLocationUrl', false);
		$dropOffLocation		=	validatePostVar($this->postVar, 'dropOffLocation', false);
		$numberOfAdult			=	validatePostVar($this->postVar, 'numberOfAdult', true);
		$numberOfChild			=	validatePostVar($this->postVar, 'numberOfChild', false);
		$numberOfInfant			=	validatePostVar($this->postVar, 'numberOfInfant', false);
		$bookingCode			=	validatePostVar($this->postVar, 'bookingCode', true);
		$reservationPriceType	=	validatePostVar($this->postVar, 'reservationPriceType', true);
		$reservationPriceInteger=	str_replace(",", "", validatePostVar($this->postVar, 'reservationPriceInteger', true));
		$reservationPriceDecimal=	validatePostVar($this->postVar, 'reservationPriceDecimal', false);
		$reservationPrice		=	intval($reservationPriceInteger).".".intval($reservationPriceDecimal);
		$tourPlan				=	validatePostVar($this->postVar, 'tourPlan', false);
		$remark					=	validatePostVar($this->postVar, 'remark', false);
		$specialRequest			=	validatePostVar($this->postVar, 'specialRequest', false);
		$additionalInfo			=	validatePostVar($this->postVar, 'additionalInfo', false);
		$isMailValidated		=	$this->ModelMailbox->isMailValidated($idMailbox);
		$totalInsertReservation	=	0;
		
		if($reservationDate == '0000-00-00' || $reservationDate == '00-00-0000') setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid reservation date!"));
		
		if($selfDriveStatus == 0){
			$reservationDateEnd		=	$reservationDate;
			$reservationHourEnd		=	$reservationHour;
			$reservationMinuteEnd	=	$reservationMinute;
		} else {
			$daysDifference		=	$reservationDateDT->diff($reservationDateEndDT);
			$daysDifference		=	$daysDifference->days;
			if($daysDifference != $durationOfDay && ($daysDifference + 1) != $durationOfDay) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid reservation date period (date start and end)"));
		}
		
		$reservationTimeEnd		=	$reservationHourEnd.":".$reservationMinuteEnd;		

		if($isMailValidated){
			$userValidator		=	$isMailValidated['USEREDITOR'];
			$dateTimeValidator	=	$isMailValidated['DATETIMEVALIDATION'];
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"This mail has been validated by <b>".$userValidator."</b> on <b>".$dateTimeValidator."</b>, please refresh your data"));
		}
		
		if($reservationPrice <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please input a valid price"));
		if($duplicateNumber > 1 && $durationOfDay > 1) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Duplication and duration cannot be more than one (1) at the same time"));
		
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
		
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$dataUserAdmin['NAME'];
		$currencyExchange	=	1;
		$dataExchange		=	$this->MainOperation->getDataExchangeCurrency();
		
		foreach($dataExchange as $keyExchange){
			if($keyExchange->CURRENCY == $reservationPriceType){
				$currencyExchange	=	$keyExchange->EXCHANGETOIDR;
			}
		}
		
		if(strpos(strtolower($reservationTitle), "japan") !== false) $specialRequest	=	"Japanese Driver. ".$specialRequest;
		if(strpos(strtolower($reservationTitle), "chinese") !== false) $specialRequest	=	"Chinese Driver. ".$specialRequest;
		if(strpos(strtolower($reservationTitle), "comfort") !== false) $specialRequest	=	"Comfort Car. ".$specialRequest;
		
		$reservationPriceIDR=	$reservationPrice * $currencyExchange;
		$additionalInfoJson	=	null;
		
		if(count($additionalInfo) > 0){
			$arrAdditionalInfo	=	[];
			foreach($additionalInfo as $keyAdditionalInfo){
				$textLinkType		=	$keyAdditionalInfo[0];
				$descriptionInfo	=	$keyAdditionalInfo[1];
				$contentInfo		=	$keyAdditionalInfo[2];
				$contentInfo		=	$textLinkType == 1 ? $contentInfo : '<a href="'.$contentInfo.'" target="_blank">Click Here</a>';
				$arrAdditionalInfo[]=	[$descriptionInfo, $contentInfo];
			}
			$additionalInfoJson	=	json_encode($arrAdditionalInfo);
		}
		
		$arrUpdateMail		=	array(
			"IDRESERVATIONTYPE"		=>	$reservationType,
			"IDAREA"				=>	$idArea,
			"DUPLICATENUMBER"		=>	$duplicateNumber,
			"RESERVATIONTITLE"		=>	trim($reservationTitle),
			"URLDETAILPRODUCT"		=>	$detailsProductURL,
			"RESERVATIONDATE"		=>	$reservationDate,
			"RESERVATIONDATEEND"	=>	$reservationDateEnd,
			"RESERVATIONTIME"		=>	$reservationTime,
			"RESERVATIONTIMEEND"	=>	$reservationTimeEnd,
			"DURATIONOFDAY"			=>	$durationOfDay,
			"CUSTOMERNAME"			=>	$customerName,
			"CUSTOMERCONTACT"		=>	$customerContact,
			"CUSTOMEREMAIL"			=>	$customerEmail,
			"HOTELNAME"				=>	$hotelName,
			"PICKUPLOCATION"		=>	$pickUpLocation,
			"URLPICKUPLOCATION"		=>	$pickUpLocationUrl,
			"DROPOFFLOCATION"		=>	$dropOffLocation,
			"NUMBEROFADULT"			=>	$numberOfAdult,
			"NUMBEROFCHILD"			=>	$numberOfChild,
			"NUMBEROFINFANT"		=>	$numberOfInfant,
			"BOOKINGCODE"			=>	$bookingCode,
			"INCOMEAMOUNTCURRENCY"	=>	$reservationPriceType,
			"INCOMEAMOUNT"			=>	$reservationPrice,
			"REMARK"				=>	$remark,
			"TOURPLAN"				=>	$tourPlan,
			"ADDITIONALINFOLIST"	=>	$additionalInfoJson,
			"STATUS"				=>	1,
			"ISSELFDRIVE"			=>	$selfDriveStatus,
			"USEREDITOR"			=>	$userAdminName,
			"DATETIMEVALIDATION"	=>	date('Y-m-d H:i:s')
		);
		$procUpdateMail		=	$this->MainOperation->updateData('t_mailbox', $arrUpdateMail, 'IDMAILBOX', $idMailbox);
		
		if(!$procUpdateMail['status']) switchMySQLErrorCode($procUpdateMail['errCode'], $this->newToken);

		$arrInsertRsv							=	$arrUpdateMail;
		$arrInsertRsv['IDSOURCE']				=	$idSource;
		$arrInsertRsv['INPUTTYPE']				=	1;
		$arrInsertRsv['STATUS']					=	0;
		$arrInsertRsv['USERINPUT']				=	$userAdminName;
		$arrInsertRsv['DATETIMEINPUT']			=	date('Y-m-d H:i:s');
		$arrInsertRsv['INCOMEEXCHANGECURRENCY']	=	$currencyExchange;
		$arrInsertRsv['INCOMEAMOUNTIDR']		=	$currencyExchange * $reservationPrice;
		$arrInsertRsv['RESERVATIONDATESTART']	=	$reservationDate;
		$arrInsertRsv['RESERVATIONTIMESTART']	=	$reservationTime;
		$arrInsertRsv['RESERVATIONDATEEND']		=	$reservationDateEnd;
		$arrInsertRsv['RESERVATIONTIMEEND']		=	$reservationTimeEnd;
		$arrInsertRsv['SPECIALREQUEST']			=	$specialRequest;
		
		unset($arrInsertRsv['USEREDITOR']);
		unset($arrInsertRsv['DATETIMEVALIDATION']);
		unset($arrInsertRsv['RESERVATIONDATE']);
		unset($arrInsertRsv['RESERVATIONTIME']);
		unset($arrInsertRsv['DUPLICATENUMBER']);
		
		if($durationOfDay > 1 && $selfDriveStatus == 0){
			$additionalDays						=	$durationOfDay - 1;
			$dateEnd							=	date('Y-m-d', strtotime($reservationDate. ' + '.$additionalDays.' days'));
			$arrInsertRsv['RESERVATIONDATEEND']	=	$dateEnd;
		}
		
		if($duplicateNumber > 1){
			$reservationPrice					=	number_format($reservationPrice/$duplicateNumber, 2, '.', '');
			$reservationPriceIDR				=	number_format($reservationPriceIDR/$duplicateNumber, 0, '.', '');
			
			$arrInsertRsv['INCOMEAMOUNT']		=	$reservationPrice;
			$arrInsertRsv['INCOMEAMOUNTIDR']	=	$reservationPriceIDR;
		}
		
		$arrIdReservation	=	array();
		for($i=0; $i<$duplicateNumber; $i++){
			
			if($duplicateNumber > 1){
				$reservationDate						=	date('Y-m-d', strtotime($reservationDate. ' + '.$i.' days'));
				$arrInsertRsv['RESERVATIONDATESTART']	=	$reservationDate;
				$arrInsertRsv['RESERVATIONDATEEND']		=	$reservationDate;
			}
			
			$procInsertRsv						=	$this->MainOperation->addData('t_reservation', $arrInsertRsv);
			
			if(!$procInsertRsv['status']){
				switchMySQLErrorCode($procInsertRsv['errCode'], $this->newToken);
			}
			
			$idReservation		=	$procInsertRsv['insertID'];
			$arrIdReservation[]	=	$idReservation;
			$totalInsertReservation++;
			
			$arrInsertPayment	=	array(
				"IDRESERVATION"		=>	$idReservation,
				"IDPAYMENTMETHOD"	=>	1,
				"DESCRIPTION"		=>	"Agent Order",
				"AMOUNTCURRENCY"	=>	$reservationPriceType,
				"AMOUNT"			=>	$reservationPrice,
				"EXCHANGECURRENCY"	=>	$currencyExchange,
				"AMOUNTIDR"			=>	$currencyExchange * $reservationPrice,
				"USERINPUT"			=>	$userAdminName,
				"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
				"EDITABLE"			=>	1,
				"DELETABLE"			=>	0
			);
			$this->MainOperation->addData('t_reservationpayment', $arrInsertPayment);
			$this->pushApiScanCustomerContact($idReservation);
			
		}
		
		$this->sendNewReservationNotif($arrIdReservation, $customerName, $reservationTitle, $reservationDateStr);
		$this->updateWebappStatisticTags();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Mail has been validated"));
	}
	
	private function updateWebappStatisticTags(){
		if(PRODUCTION_URL){
			$this->load->model('MainOperation');

			$totalUnprocessedReservationMail=	$this->MainOperation->getTotalUnprocessedReservationMail();
			$totalUnprocessedReservation	=	$this->MainOperation->getTotalUnprocessedReservation();
			try {
				$factory					=	(new Factory)
													->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
													->withDatabaseUri(FIREBASE_RTDB_URI);
				$database					=	$factory->createDatabase();
				$database->getReference(FIREBASE_RTDB_MAILREF_NAME)
				->set([
					   'newMailStatus'			=>	false,
					   'totalUnprocessedMail'	=>	$totalUnprocessedReservationMail,
					   'timestampUpdate'		=>	gmdate("YmdHis")
					  ]);

				$database->getReference(FIREBASE_RTDB_MAINREF_NAME."unprocessedReservation")->set($totalUnprocessedReservation);
			} catch (Exception $e) {
				return true;
			}
		}
		return true;
	}
	
	private function checkInputData(){
		$arrVarValidate	=	array(
			array("reservationType","option","Reservation Type"),
			array("durationOfDay","text","Duration (Day) at least one"),
			array("duplicateNumber","text","Duplication (Times) at least one"),
			array("reservationTitle","text","Reservation Title"),
			array("reservationDate","text","Reservation Date"),
			array("customerName","text","Customer Name"),
			array("customerContact","text","Customer Contact"),
			array("optionPickUpArea","option","Pick Up Area"),
			array("numberOfAdult","text","Number of Adult Customer (at least one)"),
			array("bookingCode","text","Booking Code"),
			array("reservationPriceInteger","text","Reservation Price")
		);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;
	}
	
	public function bulkNotifReservation(){
		$this->load->model('MainOperation');
		$this->sendNewReservationNotif(array(10), "Lai, Cherry", "Bali Water Sports Package Including Transfers", "24 Jan 2022");
		echo "ok";
		die();
	}
	
	private function sendNewReservationNotif($arrIdReservation, $customerName, $reservationTitle, $reservationDateStr){
		$dataPlayerId	=	$this->MainOperation->getDataPlayerIdOneSignal("NOTIFRESERVATION");
		
		if($dataPlayerId){
			$arrPlayerId	=	$dataPlayerId['arrOSUserId'];
			$arrIdUserAdmin	=	$dataPlayerId['arrIdUserAdmin'];
			$title			=	'New reservation for '.$reservationDateStr;
			$message		=	'Customer Name : '.$customerName.'. Reservation Title : '.$reservationTitle;
			$arrData		=	array(
									"type"			=>	"reservation",
									"idReservation"	=>	$arrIdReservation
								);
			$arrHeading		=	array(
									"en" => $title
								);
			$arrContent		=	array(
									"en" => $message
								);
			$this->MainOperation->insertAdminMessage(2, $arrIdUserAdmin, $title, $message, $arrData);
			if(PRODUCTION_URL) sendOneSignalMessage($arrPlayerId, $arrData, $arrHeading, $arrContent);
		}
		
		return true;
	}
	
	public function getTotalUnreadMail(){
		$this->load->model('ModelMailbox');
		$totalUnreadMail		=	$this->ModelMailbox->getTotalUnreadMail();
		
		setResponseOk(array("token"=>$this->newToken, "totalUnreadMail"=>$totalUnreadMail));
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
	
}