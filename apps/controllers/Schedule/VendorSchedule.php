<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class VendorSchedule extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$this->postVar	=	decodeJsonPost();
		$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid submission data"));
		$this->newToken	=	isLoggedIn($this->token, true);
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataVendorSchedule(){

		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelVendorSchedule');
		
		$confirmationStatus	=	validatePostVar($this->postVar, 'confirmationStatus', false);
		$idVendor			=	validatePostVar($this->postVar, 'idVendor', false);
		$scheduleDate		=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDate		=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$scheduleDateStr	=	$scheduleDate->format('d M Y');
		$scheduleDate		=	$scheduleDate->format('Y-m-d');
		$dataVendorSchedule	=	$this->ModelVendorSchedule->getDataVendorSchedule($scheduleDate, $idVendor, $confirmationStatus);

		if(!$dataVendorSchedule){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found for selected filter"));
		}
		
		$arrIdVendor		=	array();
		foreach($dataVendorSchedule as $keyVendorSchedule){
			$arrIdVendor[]	=	$keyVendorSchedule->IDVENDOR;
		}
		$strArrIdVendor		=	implode(",", $arrIdVendor);
		$dataActiveVendor	=	$this->ModelVendorSchedule->getDataActiveVendorSchedule($strArrIdVendor);
		
		foreach($dataActiveVendor as $keyVendor){
			$idActiveVendor				=	$keyVendor->IDVENDOR;
			$keyVendor->ARRRESERVATION	=	array();
			foreach($dataVendorSchedule as $keyVendorSchedule){
				$idVendorSchedule	=	$keyVendorSchedule->IDVENDOR;
				if($idActiveVendor == $idVendorSchedule){
					$arrPackageList					=	array();
					$explodePackageList				=	explode("~", $keyVendorSchedule->PACKAGELIST);
					
					foreach($explodePackageList as $keyPackageList){
						$explodePackage				=	explode('|', $keyPackageList);
						$arrPackageList[]			=	array(
															"PACKAGENAME"			=>	$explodePackage[0],
															"PAXADULT"				=>	$explodePackage[1],
															"PAXCHILD"				=>	$explodePackage[2],
															"PAXINFANT"				=>	$explodePackage[3],
															"STATUSCONFIRM"			=>	$explodePackage[4],
															"STATUSPROCESSNAME"		=>	$explodePackage[5],
															"STATUSPROCESS"			=>	$explodePackage[6],
															"IDRESERVATIONDETAILS"	=>	$explodePackage[7],
															"TIMEPICKUP"			=>	$explodePackage[8],
															"TIMEBOOKING"			=>	$explodePackage[9],
															"TIMESCHEDULE"			=>	$explodePackage[10],
															"IDSCHEDULEVENDOR"		=>	$explodePackage[11],
														);
					}
					
					$keyVendor->ARRRESERVATION[]	=	array(
															"IDRESERVATION"			=>	$keyVendorSchedule->IDRESERVATION,
															"RESERVATIONTITLE"		=>	$keyVendorSchedule->RESERVATIONTITLE,
															"RESERVATIONTIMESTART"	=>	$keyVendorSchedule->RESERVATIONTIMESTART,
															"CUSTOMERNAME"			=>	$keyVendorSchedule->CUSTOMERNAME,
															"SOURCENAME"			=>	$keyVendorSchedule->SOURCENAME,
															"BOOKINGCODE"			=>	$keyVendorSchedule->BOOKINGCODE,
															"HOTELNAME"				=>	$keyVendorSchedule->HOTELNAME,
															"PICKUPLOCATION"		=>	$keyVendorSchedule->PICKUPLOCATION,
															"DROPOFFLOCATION"		=>	$keyVendorSchedule->DROPOFFLOCATION,
															"PACKAGELIST"			=>	$arrPackageList
														);
					$keyVendor->TOTALRESERVATION++;
					unset($keyVendorSchedule);
				}
			}
		}

		setResponseOk(array("token"=>$this->newToken, "dataActiveVendor"=>$dataActiveVendor));
	
	}
	
	public function getDetailReservationDetailsProduct(){

		$this->load->model('ModelReservation');
		$this->load->model('Schedule/ModelVendorSchedule');
		
		$idReservationDetails	=	validatePostVar($this->postVar, 'idReservationDetails', true);
		$detailData				=	$this->ModelVendorSchedule->getDetailReservationDetails($idReservationDetails);
		
		if(!$detailData){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found, please refresh data and try again"));
		}
		
		$idVendor				=	$detailData['IDVENDOR'];
		$ticketDetail			=	$this->ModelVendorSchedule->getDetailReservationTicket($idReservationDetails);
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
	
	public function updateSlotTimeBooking(){
		$this->load->model('MainOperation');

		$idScheduleVendor	=	validatePostVar($this->postVar, 'idScheduleVendor', true);
		$slotTime			=	validatePostVar($this->postVar, 'slotTime', true);
		$arrUpdateSchedule	=	array("TIMEBOOKING"	=>	$slotTime);
		$procUpdateSchedule	=	$this->MainOperation->updateData("t_schedulevendor", $arrUpdateSchedule, "IDSCHEDULEVENDOR", $idScheduleVendor);

		if(!$procUpdateSchedule['status']){
			switchMySQLErrorCode($procUpdateSchedule['errCode'], $this->newToken);
		}

		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data update was successful"));
	}
	
	public function saveReservationDetails(){

		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelVendorSchedule');

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
		$detailFee				=	$this->ModelVendorSchedule->getDetailFeeVendor($idVendor, $idReservationDetails);
		
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

			if(!$procUpdateFee['status']){
				switchMySQLErrorCode($procUpdateFee['errCode'], $this->newToken);
			}
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
		
		if(!$procUpdateDetails['status']){
			switchMySQLErrorCode($procUpdateDetails['errCode'], $this->newToken);
		}
		
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
	
	public function getDetailReservation(){

		$this->load->model('Schedule/ModelVendorSchedule');
		
		$idReservation	=	validatePostVar($this->postVar, 'idReservation', false);
		$detailData		=	$this->ModelVendorSchedule->getDetailReservation($idReservation);
		
		if(!$detailData){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$detailData));
	
	}
	
	public function resendScheduleNotification(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelVendorSchedule');
		
		$idReservationDetails	=	validatePostVar($this->postVar, 'idData', true);
		
		if($idReservationDetails <= 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
		}
		
		$detailNotification	=	$this->ModelVendorSchedule->getDetailNotificationSchedule($idReservationDetails);
		
		if(!$detailNotification){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Failed to resend notification. Maybe the schedule you chose is no longer available. Please try again later"));
		}
		
		if($detailNotification['MESSAGE'] == ""){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Failed to resend notification. Please try again later"));
		}
		
		$idVendor			=	$detailNotification['IDVENDOR'];
		$dataMessageType	=	$this->MainOperation->getDataMessageType(1);
		$dataVendor			=	$this->MainOperation->getDataVendor($idVendor);
		$activityMessage	=	$dataMessageType['ACTIVITY'];
		$titleMsg			=	$detailNotification['TITLE'];
		$body				=	$detailNotification['MESSAGE'];
		$vendorTokenFCM		=	$dataVendor['TOKENFCM'];
		$additionalArray	=	array(
									"activity"	=>	$activityMessage,
									"idPrimary"	=>	$idReservationDetails,
								);
		if($vendorTokenFCM != "") $this->fcm->sendPushNotification($vendorTokenFCM, $titleMsg, $body, $additionalArray);
		if(PRODUCTION_URL){
			$RTDB_refCode			=	$dataVendor['RTDBREFCODE'];
			if($RTDB_refCode && $RTDB_refCode != ''){
				try {
					$factory			=	(new Factory)
											->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
											->withDatabaseUri(FIREBASE_RTDB_URI);
					$database			=	$factory->createDatabase();
					$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/vendor/".$RTDB_refCode."/unconfirmedReservation");
					$referencePartnerVal=	$referencePartner->getValue();
					$strDateSchedule	=	$detailNotification['SCHEDULEDATE'];
					$rsvService			=	$detailNotification['PRODUCTNAME'];
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
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Schedule notification sent successfully"));

	}
	
}