<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Ddeboer\Imap\Server;
use Ddeboer\Imap\Search;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Search\Flag\Unseen;
use Ddeboer\Imap\Search\Text\Subject;

class Reconfirmation extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(2);

		if($functionName != "getPreviewReconfirmationMail" && $_SERVER['REQUEST_METHOD'] === 'POST'){
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
	
	public function getDataReconfirmation(){
		$this->load->model('ModelReconfirmation');
		
		$scheduleDate		=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDate		=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$scheduleDate		=	$scheduleDate->format('Y-m-d');
		$idSource			=	validatePostVar($this->postVar, 'idSource', false);
		$sendingMethod		=	validatePostVar($this->postVar, 'sendingMethod', false);
		$bookingCode		=	validatePostVar($this->postVar, 'bookingCode', false);
		$customerName		=	validatePostVar($this->postVar, 'customerName', false);
		$reservationTitle	=	validatePostVar($this->postVar, 'reservationTitle', false);
		$viewUnreadThread	=	validatePostVar($this->postVar, 'viewUnreadThread', false);
		$statusReconfirm	=	validatePostVar($this->postVar, 'statusReconfirm', false);
		$dataTable			=	$this->ModelReconfirmation->getDataReconfirmation($scheduleDate, $idSource, $sendingMethod, $bookingCode, $customerName, $reservationTitle, $viewUnreadThread, $statusReconfirm);
		
		if(!$dataTable){
			setResponseNotFound(
				array(
					"token"	=>	$this->newToken,
					"msg"	=>	'No data found'
				)
			);
		}
		
		$totalStatusAll	=	$totalStatusScheduled	=	$totalStatusSent	=	$totalStatusConfirmed	=	$totalStatusUpdate	=	0;
		foreach($dataTable as $keyData){
			$totalStatusAll++;
			$objReconfirmation	=	json_decode($keyData->OBJRECONFIRMATION);
			
			foreach($objReconfirmation as $keyReconfirmation){
				if(isset($keyReconfirmation->STATUS)){
					$statusReconfirmation	=	$keyReconfirmation->STATUS;
					switch($statusReconfirmation){
						case 0	:	if($keyReconfirmation->SENDINGMETHOD == 1) $totalStatusScheduled++; break;
						case 1	:	$totalStatusSent++; break;
						case 2	:	$totalStatusConfirmed++; break;
						case 3	:	$totalStatusUpdate++; break;
						default	:	break;
					}
				}
			}
		}
		
		setResponseOk(
			array(
				"token"					=>	$this->newToken,
				"dataTable"				=>	$dataTable,
				"totalStatusAll"		=>	$totalStatusAll,
				"totalStatusScheduled"	=>	$totalStatusScheduled,
				"totalStatusSent"		=>	$totalStatusSent,
				"totalStatusConfirmed"	=>	$totalStatusConfirmed,
				"totalStatusUpdate"		=>	$totalStatusUpdate
			)
		);
	}
	
	public function getDetailReconfirmation(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReconfirmation');
		
		$idReservation	=	validatePostVar($this->postVar, 'idReservation', true);
		$detailData		=	$this->ModelReconfirmation->getDetailStrReservation($idReservation);
		
		if(!$detailData) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found, please refresh your data"));
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$idUserAdmin		=	$dataUserAdmin['IDUSERADMIN'];
		$mailTemplateData	=	$this->ModelReconfirmation->getMailTemplateData($idUserAdmin);
		$mailAddress		=	$detailData['CUSTOMEREMAIL'];
		$isValidMailAddress	=	filter_var($mailAddress, FILTER_VALIDATE_EMAIL);
		$objReconfirmation	=	$detailData['OBJRECONFIRMATION'];
		
		if($objReconfirmation != ""){
			$objReconfirmation	=	json_decode($objReconfirmation);
			if(count($objReconfirmation) > 0 && !empty($objReconfirmation)){
				foreach($objReconfirmation as $keyReconfirmation){
					if(isset($keyReconfirmation->PLATFORM) && $keyReconfirmation->PLATFORM == 'EMAIL'){
						$procUpdate =	$this->MainOperation->updateData('t_reservationreconfirmation', ['STATUSREADTHREAD'	=> 1], 'IDRESERVATIONRECONFIRMATION', $keyReconfirmation->IDRESERVATIONRECONFIRMATION);
						if($procUpdate['status']) $this->updateWebappStatisticTagsUnreadThreadReconfirmation();
					}
				}
			}
		}
		
		$dataHandleDriver	=	$this->ModelReconfirmation->getReservationHandleDriver($idReservation);
		setResponseOk(
			array(
				"token"					=>	$this->newToken,
				"detailData"			=>	$detailData,
				"isValidMailAddress"	=>	$isValidMailAddress,
				"mailTemplateData"		=>	$mailTemplateData,
				"urlMailDraftPreview"	=>	URL_MAIL_RECONFIRMATION_DRAFT_PREVIEW,
				"urlMailReconfirmation"	=>	URL_MAIL_RECONFIRMATION_PREVIEW,
				"dataHandleDriver"		=>	$dataHandleDriver
			)
		);
	}

	public function updateWebappStatisticTagsUnreadThreadReconfirmation(){
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
	
	public function getPreviewReconfirmationMail($fileName){
		$fileContent		=	file_get_contents(PATH_EMAIL_RECONFIRMATION_FILE.$fileName);
		echo $fileContent;
	}
	
	public function getMailThreadDetails(){
		$this->load->model('ModelReconfirmation');
		
		$mailThreadFile			=	validatePostVar($this->postVar, 'mailThreadFile', true);
		$arrMailThreadFile		=	explode(',', $mailThreadFile);
		$idReconfirmationMail	=	validatePostVar($this->postVar, 'idReconfirmationMail', true);
		$detailReconfirmation	=	$this->ModelReconfirmation->getDetailReconfirmation($idReconfirmationMail);
		
		if(!$detailReconfirmation) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Failed to read the conversation. Try again later"));

		$customerName			=	$detailReconfirmation['CUSTOMERNAME'];
		$threadArrName			=	$detailReconfirmation['MAILTHREADARRNAME'];
		$threadArrName			=	explode(',', $threadArrName);
		$iThread				=	0;
		$mailThreadDetails		=	[];
		
		foreach($arrMailThreadFile as $threadFileName){
			$position			=	'R';
			$contentHeader		=	$contentText	=	'';
			
			try {
				$contentText	=	file_get_contents(PATH_EMAIL_RECONFIRMATION_THREAD.$threadFileName);
				$isContentHtml	=	$contentText != strip_tags($contentText);
				$nameSender		=	$threadArrName[$iThread];
				$position		=	$nameSender == 'Customer' ? 'R' : 'L';
				$arrFileName	=	explode('-', $threadFileName);
				$dateTimeFile	=	DateTime::createFromFormat('YmdHis', end($arrFileName));
				$dateTimeFile	=	$dateTimeFile->format('D, d M Y H:i');
				$contentHeader	=	($nameSender == 'Customer' ? $customerName : $nameSender).' On '.$dateTimeFile;
			} catch (Exception $e) {
				setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Failed to read the conversation. Try again later"));
			}
			
			$mailThreadDetails[]	=	[
				'position'		=>	$position,
				'contentHeader'	=>	$contentHeader,
				'contentText'	=>	$isContentHtml ? iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $contentText) : nl2br(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $contentText))
			];
			$iThread++;
		}
		
		setResponseOk(
			array(
				"token"				=>	$this->newToken,
				"mailThreadDetails"	=>	$mailThreadDetails
			)
		);
	}
	
	public function replyMailConfirmation(){
		$this->load->model('ModelReconfirmation');
		
		$idReconfirmationMail	=	validatePostVar($this->postVar, 'idReconfirmationMail', true);
		$mailReplyContent		=	validatePostVar($this->postVar, 'mailReplyContent', true);
		$adminName				=	validatePostVar($this->postVar, 'NAME', true);
		$detailReconfirmation	=	$this->ModelReconfirmation->getDetailReconfirmation($idReconfirmationMail);
		
		if(!$detailReconfirmation) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Reconfirmation detail not found. Please try again later"));
		
		$customerMail			=	$detailReconfirmation['CONTACT'];
		$customerName			=	$detailReconfirmation['CUSTOMERNAME'];
		$bookingCode			=	$detailReconfirmation['BOOKINGCODE'];
		$mailMessageId			=	$detailReconfirmation['MAILMESSAGEID'];
		$mailThreadArrName		=	$detailReconfirmation['MAILTHREADARRNAME'];
		$mailThreadArrName		=	$mailThreadArrName == '' ? $adminName : $mailThreadArrName.','.$adminName;
		$mailThreadFileNameNew	=	str_pad($idReconfirmationMail, 6, '0', STR_PAD_LEFT)."-".date('YmdHis');
		$mailThreadFile			=	$detailReconfirmation['MAILTHREADFILE'];
		$mailThreadFile			=	$mailThreadFile == '' ? $mailThreadFileNameNew : $mailThreadFile.','.$mailThreadFileNameNew;
		$mailSubject			=	$detailReconfirmation['MAILSUBJECT'];
		$mailSubject			=	$mailSubject == "" ? 'Bali SUN Tours - Reconfirm Reservation : '.$bookingCode : 'Re: '.$mailSubject;
		$mail 					=	new PHPMailer(true);

		try {
			$mail->isSMTP();
			$mail->Host			= MAIL_HOST;
			$mail->SMTPAuth		= true;
			$mail->Username		= MAIL_USERNAME;
			$mail->Password		= MAIL_PASSWORD;
			$mail->SMTPSecure	= PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port			= MAIL_SMTPPORT;

			$mail->setFrom(MAIL_FROMADDRESS, MAIL_NAME);
			$mail->addAddress($customerMail, $customerName);
			$mail->addReplyTo(MAIL_FROMADDRESS, MAIL_NAME);
			if($mailMessageId != "") $mail->addCustomHeader('References', $mailMessageId);
			if($mailMessageId != "") $mail->addCustomHeader('In-Reply-To', $mailMessageId);
			
			$mail->Subject	=	$mailSubject;
			$mail->Body   	=	$mailReplyContent;
			$mail->isHTML(true);
			$mail->send();
			
			$arrUpdate		=	[
				'MAILTHREADARRNAME'	=> $mailThreadArrName,
				'MAILTHREADFILE'	=> $mailThreadFile,
				'STATUSREADTHREAD'	=> 1
			];

			file_put_contents(PATH_EMAIL_RECONFIRMATION_THREAD.$mailThreadFileNameNew, $mailReplyContent);
			$this->MainOperation->updateData('t_reservationreconfirmation', $arrUpdate, 'IDRESERVATIONRECONFIRMATION', $idReconfirmationMail);
		
			setResponseOk(
				array(
					"token"			=>	$this->newToken,
					"idReservation"	=>	$detailReconfirmation['IDRESERVATION'],
					"msg"			=>	'The reply email has been successfully sent'
				)
			);
		} catch (Exception $e) {
			setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed to send a reply email. Please try again later"));
		}
	}
	
	public function addMailConfirmationAdditionalInfo(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReconfirmation');
		
		$idReconfirmationMail	=	validatePostVar($this->postVar, 'idReconfirmationMail', true);
		$textLinkType			=	validatePostVar($this->postVar, 'textLinkType', true);
		$description			=	validatePostVar($this->postVar, 'description', true);
		$informationContent		=	validatePostVar($this->postVar, 'informationContent', true);
		$detailReconfirmation	=	$this->ModelReconfirmation->getDetailReconfirmation($idReconfirmationMail);
		
		if(!$detailReconfirmation) setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed to save additional info. Please try again later"));
		$additionalInfoList		=	$detailReconfirmation['ADDITIONALINFOLIST'];
		$additionalInfoList		=	$additionalInfoList == "" ? [] : json_decode($additionalInfoList);
		
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
			't_reservationreconfirmation',
			['ADDITIONALINFOLIST'	=>	json_encode($additionalInfoList)],
			'IDRESERVATIONRECONFIRMATION',
			$idReconfirmationMail
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
		$this->load->model('ModelReconfirmation');
		
		$idReconfirmationMail	=	validatePostVar($this->postVar, 'idReconfirmationMail', true);
		$description			=	validatePostVar($this->postVar, 'informationDescription', true);
		$detailReconfirmation	=	$this->ModelReconfirmation->getDetailReconfirmation($idReconfirmationMail);
		
		if(!$detailReconfirmation) setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed to delete additional info. Please try again later"));
		$additionalInfoList		=	json_decode($detailReconfirmation['ADDITIONALINFOLIST']);
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
			't_reservationreconfirmation',
			['ADDITIONALINFOLIST'	=>	$additionalInfoListNew],
			'IDRESERVATIONRECONFIRMATION',
			$idReconfirmationMail
		);
	
		setResponseOk(
			array(
				"token"	=>	$this->newToken,
				"msg"	=>	'Additional info has been deleted'
			)
		);
	}

	public function setToManualReconfirmation(){
		$this->load->model('MainOperation');
		$this->load->model('ModelReconfirmation');
		
		$idReservation		=	validatePostVar($this->postVar, 'idReservation', true);
		$customerEmail		=	validatePostVar($this->postVar, 'customerEmail', true);
		$additionalInfoList	=	validatePostVar($this->postVar, 'additionalInfoList', false);
		$contact			=	!filter_var($customerEmail, FILTER_VALIDATE_EMAIL) ? false : $customerEmail;
		$platform			=	!filter_var($customerEmail, FILTER_VALIDATE_EMAIL) ? false : 'EMAIL';
		
		if($contact){
			$additionalInfoList	=	$additionalInfoList == '' ? null : $additionalInfoList;
			$arrInsertData		=	array(
				'IDRESERVATION'		=>	$idReservation,
				'PLATFORM'			=>	$platform,
				'SENDINGMETHOD'		=>	1,
				'CONTACT'			=>	$contact,
				'ADDITIONALINFOLIST'=>	$additionalInfoList,
				'DATETIMESCHEDULE'	=>	'0000-00-00 00:00:00'
			);
			
			$this->MainOperation->addData('t_reservationreconfirmation', $arrInsertData);
	
			setResponseOk(
				array(
					"token"	=>	$this->newToken,
					"msg"	=>	'This reservation confirmation has been set to <b>manual method</b>'
				)
			);
		} else {
			setResponseInternalServerError(
				array(
					"token"	=>	$this->newToken,
					"msg"	=>	'The customer`s email address is invalid. Please update data and try again later'
				)
			);
		}
	}

	public function sendManualReconfirmation(){
		
		$idReconfirmationMail		=	validatePostVar($this->postVar, 'idReconfirmationMail', true);
		$idReconfirmationMail		=	base64_encode(encodeStringKeyFunction($idReconfirmationMail, DEFAULT_KEY_ENCRYPTION));
		$urlCronSendReconfirmation	=	URL_MAIL_RECONFIRMATION_CRON_SEND.$idReconfirmationMail;
		$resAPICronReconfirmation	=	json_decode(trim(curl_get_file_contents($urlCronSendReconfirmation)));
		$procCode					=	$resAPICronReconfirmation->procCode;
		$msg						=	$resAPICronReconfirmation->msg;
		
		switch($procCode){
			case "200"	:	setResponseOk(
								array(
									"token"	=>	$this->newToken,
									"msg"	=>	$msg
								)
							);
							break;
			default		:	setResponseInternalServerError(
								array(
									"token"	=>	$this->newToken,
									"msg"	=>	$msg
								)
							);
							break;
		}
	}

	public function sendMessageWhatsappSetToken(){
		$this->load->model('MainOperation');

		$idUserAdmin	=	$this->MainOperation->getIDUserAdmin($this->newToken);
		$idUserAdminWA	=	$this->MainOperation->getIDUserAdminWhatsapp($idUserAdmin);
		$idContact		=	validatePostVar($this->postVar, 'idContact', true);
		$phoneNumber	=	validatePostVar($this->postVar, 'phoneNumber', true);
		$statusWhatsapp	=	validatePostVar($this->postVar, 'statusWhatsapp', false);
		$randomToken	=	createRandomString(20);
		
		if($idUserAdminWA == 0){
			setResponseNotFound(
				array(
					"token"	=>	$this->newToken,
					"msg"	=>	'Unable to proceed to the WhatsApp application. Your user account is not registered in the WhatsApp system.'
				)
			);
		}
		
		$procUpdateUser	=	$this->MainOperation->updateData(APP_WHATSAPP_DATABASE_NAME.'.m_useradmin', ['REDIRECTTOKEN' => $randomToken], 'IDUSERADMIN', $idUserAdminWA);
		
		if(!$procUpdateUser['status']){
			setResponseInternalServerError(
				array(
					"token"	=>	$this->newToken,
					"msg"	=>	'Failed to proceed, Please try again later.'
				)
			);
		}
		
		$destinationMenu=	'CHT';
		switch($statusWhatsapp){
			case "0":
			case 0	:	$destinationMenu=	'CNCT'; break;
			default	:	$destinationMenu=	'CHT'; break;
		}
		
		$arrParam		=	[
			'redirectToken'		=>	$randomToken,
			'destinationMenu'	=>	$destinationMenu,
			'parameters'		=>	[
				'idContact'		=>	$idContact,
				'phoneNumber'	=>	$phoneNumber
			]
		];
		
		$arrParamJsonEncrypted		=	base64_encode(json_encode($arrParam));
		$urlRedirectWhatsappSystem	=	BASE_URL_WHATSAPP_REDIRECT.$arrParamJsonEncrypted;
		setResponseOk(
			array(
				"token"						=>	$this->newToken,
				"urlRedirectWhatsappSystem"	=>	$urlRedirectWhatsappSystem
			)
		);
	}
}