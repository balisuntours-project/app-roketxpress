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

class ImportDataOTA extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(2);

		if($functionName != "uploadExcelReservationOTA" && $_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->postVar	=	decodeJsonPost();
			$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid submission data".$functionName));
			$this->newToken	=	isLoggedIn($this->token, true);
		} else if($functionName == "uploadExcelReservationOTA") {
			$this->uploadExcelReservationOTA();
		} else {
			$this->index();
		}
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
		
	public function uploadExcelReservationOTA(){
		if((($_FILES["file"]["type"] == "application/vnd.ms-excel")
			|| ($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"))
			&& ($_FILES["file"]["size"] <= 800000)){
			if ($_FILES["file"]["error"] > 0) {
				setResponseInternalServerError(array("msg"=>"Failed to upload this file. File is broken"));
			}
			
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. This file type is not allowed (".$_FILES["file"]["type"].") or file size is too big (".$_FILES["file"]["size"].")"));
		}
		
		$dir		=	PATH_TMP_FILE;
		$extension	=	pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		$fileName	=	"ImportDataOTA"."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$fileName);
		
		if($move){
			setResponseOk(array("msg"=>"File has been uploaded", "fileName"=>$fileName, "extension"=>$extension));
		} else {
			setResponseInternalServerError(array("msg"=>"Failed to upload this file. Please try again later"));
		}		
	}
	
	public function scanExcelReservationOTA(){
		$this->load->model('MainOperation');
		$this->load->model('ModelImportDataOTA');
		
		$idSource			=	validatePostVar($this->postVar, 'idSource', true);
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
			$arrDataProccess	=	[];
			
			try {
				foreach($excelData as $data){
					if($idSource == 1){
						try{
							$bookingCode	=	$data[2];
							if(strlen($bookingCode) == 9 && ctype_alpha(substr($bookingCode, 0, 3)) && preg_match("/^\d+$/", substr($bookingCode, 3, 6))){
								$reservationDateTime	=	$data[1];
								$activityName			=	$data[4];
								$packageName			=	$data[5];
								$customerName			=	$data[6];
								$extraInfo				=	$data[7];
								$unitName				=	$data[8];
								$numberOfUnit			=	$data[10];
								$reservationPrice		=	$data[11];
								$status					=	$data[13];
								$notes					=	$data[14];
								$isReservationExist		=	$this->ModelImportDataOTA->isReservationExist($bookingCode, $idSource);
								
								if(!$isReservationExist){
									$detailMailBox		=	$this->ModelImportDataOTA->getDetailReservationMailBox($bookingCode, $idSource);
									$reservationTitle	=	$activityName." ".$packageName;
									$reservationDate	=	'0000-00-00';
									$reservationTime	=	'00:00:00';
									$durationOfDay		=	1;
									$customerContact	=	$customerEmail	=	$hotelName	=	$pickUpLocation	=	$dropOffLocation	=	'-';
									$numberOfAdult		=	$numberOfChild	=	$numberOfInfant	=	0;
									$idMailbox			=	0;
									
									if($detailMailBox){
										$idMailbox		=	$detailMailBox['IDMAILBOX'];
										$customerEmail	=	$detailMailBox['CUSTOMEREMAIL'];
									}
									
									try{
										$reservationDateTime=	DateTime::createFromFormat('Y-m-d h:i:s', $reservationDateTime);
										$reservationDate	=	$reservationDateTime->format('Y-m-d');
										$reservationTime	=	$reservationDateTime->format('h:i:s');
									} catch (Exception $e) {
									}
									
									switch($keyInfoName){
										case "Per Day"					:
										case "Per Day (Car + Driver)"	:	
										case "Per 6 Hours/day"			:	
										case "Per 8 Hours/day"			:	
										case "Per 10 Hours/day"			:	
										case "Per 12 Hours/day"			:	$durationOfDay	=	$numberOfUnit;
																			break;
									}
									
									if(isset($extraInfo) && $extraInfo != ""){
										$arrExtraInfo	=	explode("\n", $extraInfo);
										
										foreach($arrExtraInfo as $keyExtraInfo){
											$arrLineExtraInfo	=	explode(": ", $keyExtraInfo);
											$keyInfoName		=	$arrLineExtraInfo[0];
											$keyInfoValue		=	$arrLineExtraInfo[1];
											
											switch($keyInfoName){
												case "Preferred time"													:
												case "Pick Up Time"														:	
												case "Pick-up time"														:	
												case "Preferred Start Time"												:	
												case "Preferred Pick-Up Time"											:	
												case "What time do you prefer to participate?"							:	
												case "Preferred Meet Up Time at Rafting Location"						:
												case "Flight Departure/Arrival Time"									:
												case "Preferred Hotel Pick Up Time"										:
												case "Preferred Hotel Pick-Up Time"										:
												case "Preferred Meet Up Time at ATV Location"							:
												case "Preferred Spa Time"												:
												case "Preferred Start Time"												:
												case "Preferred Tee Time"												:
												case "Hotel Pick-Up Time"												:
													$reservationTime	=	$reservationTime == '00:00:00' ? $this->getValidTime($keyInfoValue) : $reservationTime;
													break;
												case "Contact Details (WhatsApp/Line/Wechat)"							:
												case "Contact Method & Details"											:
												case "Contact Method & Details (Phone Number/WhatsApp)"					:
												case "Contact Method & Details (WhatsApp)"								:
												case "Contact Method & Details (WhatsApp/Line/Wechat)"					:
												case "Contact Method and Details (Text Message/WhatsApp/Wechat/Email)"	:
												case "Contact Method and Details (WhatsApp/LINE/WeChat)"				:
												case "Contact Method and Details (WhatsApp/Wechat/Line)"				:
												case "Contact Number"													:
												case "Other Contact Information"										:
												case "Phone Number"														:
												case "WhatsApp"															:
												case "Whatsapp Number"													:
													$phoneNumber		=	preg_replace("/[^0-9]/", "", $keyInfoValue);
													if(strlen($phoneNumber) > 6) $customerContact=	$customerContact == '' ? $phoneNumber : $customerContact
													break;
												case "E-mail"															:
												case "Email"															:
													$isValidMailAddress	=	checkMailPattern($keyInfoValue);
													$customerEmail		=	($customerEmail == "" || $customerEmail == "-") && $isValidMailAddress ? $keyInfoValue : $customerEmail;
													break;
												case "hotel"															:
												case "Hotel (Ubud)"														:
												case "Hotel & Address"													:
												case "Hotel Google Maps URL"											:
												case "Hotel Name"														:
													$hotelName			=	$hotelName == "" || $hotelName == "-" ? $keyInfoValue : $hotelName;
													break;
												case "Departure location"												:
												case "Departure Location (Map Selection)"								:
												case "Location (Name and Address)"										:
												case "Pick Up"															:
												case "From"																:
												case "Pick-up Location (Map Selection)"									:
												case "Pick-up location & map"											:
												case "Location (Name and Address)"										:
													$pickUpLocation		=	$pickUpLocation == "" || $pickUpLocation == "-" ? $keyInfoValue : $pickUpLocation;
													break;
												case "To"																:
													$dropOffLocation	=	$dropOffLocation == "" || $dropOffLocation == "-" ? $keyInfoValue : $dropOffLocation;
													break;
												case "No. of Passenger"													:
												case "Number of Passengers"												:
													$clearInfoValue		=	preg_replace('/[^\00-\255]+/u', '', $keyInfoValue);
													$intInfoValue		=	preg_replace("/[^0-9]/", "", $keyInfoValue);
													if(strlen($clearInfoValue) > 0 &&  strlen($clearInfoValue) <= 2 && is_int($clearInfoValue) && ($clearInfoValue * 1) > 0){
														$numberOfAdult	=	$clearInfoValue * 1;
													} else if(strlen($intInfoValue) == 1 && is_int($intInfoValue) && ($intInfoValue * 1) > 0){
														$numberOfAdult	=	$intInfoValue * 1;
													} else {
														$arrExplodeInfo	=	explode(" ", $clearInfoValue);
														$idxInfo		=	0;
														foreach($arrExplodeInfo as $explodeInfo){
															switch(strtolower($explodeInfo)){
																case "adult"		:
																case "adults"		:
																case "person"		:
																case "ppl"			:
																case "adultes"		:	
																case "adlut"		:	
																case "passengers"	:	
																case "people"		:	
																case "peoples"		:	
																case "adukts"		:	
																	$idxData		=	$idxInfo - 1;
																	if($idxInfo == 0) $idxData	=	$idxInfo + 1;
																	$numberOfAdult	=	preg_replace("/[^0-9]/", "", $arrExplodeInfo[$idxData]) * 1;
																break;
															}
															$idxInfo++;
														}
													}
													break;
												default																	:
													break;
											}
										}
									}
									
									$areaKeywordCheck	=	"";
									if(isset($pickUpLocation) && $pickUpLocation != ""){
										$areaKeywordCheck	=	$pickUpLocation;
									} else if (isset($hotelName) && $hotelName != ""){
										$areaKeywordCheck	=	$hotelName;
									}
									
									$idArea					=	$this->getIdAreaPickUpByKeyword($areaKeywordCheck);
									$numberOfAdult			=	$numberOfAdult == 0 ? 1 : $numberOfAdult;
									$arrUpdateMailboxData	=	[
										"IDRESERVATIONTYPE"		=>	1,
										"IDAREA"				=>	$idArea,
										"DUPLICATENUMBER"		=>	1,
										"RESERVATIONTITLE"		=>	trim($reservationTitle),
										"RESERVATIONDATE"		=>	$reservationDate,
										"RESERVATIONTIME"		=>	$reservationTime,
										"DURATIONOFDAY"			=>	$durationOfDay,
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
										"REMARK"				=>	$remark,
										"TOURPLAN"				=>	$tourPlan,
										"ADDITIONALINFOLIST"	=>	$additionalInfoJson,
										"STATUS"				=>	1,
										"USEREDITOR"			=>	$userAdminName,
										"DATETIMEVALIDATION"	=>	date('Y-m-d H:i:s')
									];
								} else {
									
								}
								
							}
						} catch (Exception $e) {
							$this->sendErrorExcelFileReservation();
						}
					} else if($idSource == 2) {
						try{
						} catch (Exception $e) {
							$this->sendErrorExcelFileReservation();
						}
					}
					
					$arrDataProccess[]	=	[
						'bookingCode'		=>	$bookingCode,
						'isValidCode'		=>	$isValidCode,
						'settlementStatus'	=>	$settlementStatus,
						'currency'			=>	$currency,
						'amount'			=>	$amount
					];
					$totalDataProcess++;
				}
			} catch (Exception $e) {
				$this->sendErrorExcelFileReservation();
			}
			
		}
		
		if($totalDataProcess <= 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"File has been uploaded. No data match found"));
		} else {
			setResponseOk(array("token"=>$this->newToken, "msg"=>"File has been uploaded (".$totalDataProcess." records). Please check scan results", "arrDataProccess"=>$arrDataProccess));
		}

	}
	
	private function sendErrorExcelFileReservation(){
		setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed. Please make sure the uploaded file is valid and the source selection and format type are correct"));
	}
	
	private function getValidTime($strTimeValue){
		$clearTimeValue		=	preg_replace("/[^0-9:.]/", "", $strTimeValue);
		$lenClearTimeValue	=	strlen($clearTimeValue);
		$resultTime			=	'00:00:00';
		
		switch($lenClearTimeValue){
			case 1	:	$resultTime	=	str_pad($clearTimeValue, 2, "0", STR_PAD_LEFT).":00:00"; break;
			case 2	:	if($clearTimeValue * 1 < 24) $resultTime	=	$clearTimeValue.":00:00";
						break;
			case 4	:	if(strpos($clearTimeValue, '.') !== false) $clearTimeValue	=	str_replace(",", ":", $clearTimeValue);
						$resultTime	=	str_pad($clearTimeValue, 5, "0", STR_PAD_LEFT).":00"; break;
						break;
			case 5	:	if(strpos($clearTimeValue, '.') !== false) $clearTimeValue	=	str_replace(",", ":", $clearTimeValue);
						$resultTime	=	$clearTimeValue.":00"; break;
						break;
			default	:	break;
		}
		
		if (strpos($strTimeValue, 'pm') !== false || strpos($strTimeValue, 'PM') !== false || strpos($strTimeValue, 'pM') !== false || strpos($strTimeValue, 'Pm') !== false) {
			$resultTime	=	DateTime::createFromFormat('h:i:s', $resultTime);
			$resultTime	=	$resultTime->modify("+12 hours");
			$resultTime	=	$resultTime->format('h:i:s');
		}
		
		return $resultTime;
	}
	
	private function getIdAreaPickUpByKeyword($areaKeywordCheck){
		$dataAreaTags		=	$this->MainOperation->getDataAreaTags();
		$selectedIdArea		=	0;
		if($dataAreaTags){
			foreach($dataAreaTags as $keyAreaTags){
				if($selectedIdArea == 0){
					$idArea		=	$keyAreaTags->IDAREA;
					$areaTagsDB	=	$keyAreaTags->AREATAGS;
					$arrAreaTags=	explode(", ", $areaTagsDB);
					
					foreach($arrAreaTags as $areaTag){
						if(strpos(strtolower($areaKeywordCheck), strtolower($areaTag)) !== false){
							$selectedIdArea	=	$idArea;
							break;
						}
					}
				}
			}
		}
		
		return $selectedIdArea;
	}
}