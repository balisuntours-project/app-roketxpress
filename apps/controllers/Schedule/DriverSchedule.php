<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class DriverSchedule extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(0);

		if(($functionName != "testAPINotifOrder") && $_SERVER['REQUEST_METHOD'] === 'POST'){
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
	
	public function getDataDriverSchedule(){

		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$scheduleDate			=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDate			=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$scheduleDateStr		=	$scheduleDate->format('d M Y');
		$scheduleDate			=	$scheduleDate->format('Y-m-d');
		$dataTable				=	$this->ModelDriverSchedule->getDataDriverSchedule($scheduleDate);
		$dataTotalReservation 	=	$this->ModelDriverSchedule->getTotalReservationByDate($scheduleDate);
		
		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "dataTotalReservation"=>$dataTotalReservation, "scheduleDateStr"=>$scheduleDateStr));
		}
		
		$allowDeleteSchedule	=	strtotime($scheduleDate) >= strtotime(date('Y-m-d')) ? true : false;
		$allowDeleteDayOff		=	$allowDeleteSchedule;
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "dataTotalReservation"=>$dataTotalReservation, "scheduleDateStr"=>$scheduleDateStr, "allowDeleteSchedule"=>$allowDeleteSchedule, "allowDeleteDayOff"=>$allowDeleteDayOff));
	
	}
	
	public function getDataReservationSchedule(){
	
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$scheduleDate	=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDate	=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$scheduleDateStr=	$scheduleDate->format('d M Y');
		$scheduleDate	=	$scheduleDate->format('Y-m-d');
		$idDriver		=	validatePostVar($this->postVar, 'idDriver', false);
		$searchKeyword	=	validatePostVar($this->postVar, 'searchKeyword', false);
		$dataTable		=	$this->ModelDriverSchedule->getDataReservationSchedule($scheduleDate, $idDriver, $searchKeyword);
	
		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "scheduleDateStr"=>$scheduleDateStr));
		}
		
		$allowDeleteSchedule	=	strtotime($scheduleDate) >= strtotime(date('Y-m-d')) ? true : false;
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "scheduleDateStr"=>$scheduleDateStr, "allowDeleteSchedule"=>$allowDeleteSchedule));
	
	}
	
	public function getDataDriverCalendar(){
		
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$scheduleDate	=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDate	=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$yearMonth		=	$scheduleDate->format('Y-m');
		$firstDate		=	$yearMonth."-01";
		$totalDays		=	date("t", strtotime($firstDate));
		$dataDriver		=	$this->ModelDriverSchedule->getDataAllDriver();
		$arrDates		=	$arrDataSchedule	=	array();
		
		for($i=1; $i<=$totalDays; $i++){
			$arrDates[]			=	$i;
			$arrDataSchedule[]	=	array();
		}
		
		if(!$dataDriver){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "arrDates"=>$arrDates));
		}
		
		foreach($dataDriver as $keyDriver){
			$keyDriver->DATASCHEDULE	=	$arrDataSchedule;
		}

		$dataSchedule	=	$this->ModelDriverSchedule->getDataDriverScheduleMonth($yearMonth);
		if($dataSchedule){
			
			foreach($dataSchedule as $keySchedule){
				
				$idReservationDB=	$keySchedule->IDRESERVATION * 1;
				$idDriverDB		=	$keySchedule->IDDRIVER;
				$timeScheduleDB	=	$keySchedule->RESERVATIONTIMESTART;
				$txtdriverType	=	$keySchedule->DRIVERTYPE;
				$idScheduleDB	=	$keySchedule->IDSCHEDULEDRIVER * 1;
				$dateScheduleDB	=	$keySchedule->SCHEDULEDATE * 1;
				$idxSchedule	=	$dateScheduleDB - 1;
				
				foreach($dataDriver as $keyDriver){
					if($keyDriver->IDDRIVER == $idDriverDB){
						$keyDriver->DATASCHEDULE[$idxSchedule][]	=	array($idScheduleDB, $txtdriverType, $idReservationDB);
						break;
					}
				}
				
			}
			
		}
		
		$dataDayOff		=	$this->ModelDriverSchedule->getDataDriverDayOff($yearMonth);
		if($dataDayOff){
			
			foreach($dataDayOff as $keyDayOff){
				
				$idDriverDB		=	$keyDayOff->IDDRIVER;
				$idDayOffDB		=	$keyDayOff->IDDAYOFF;
				$dateDayOffDB	=	$keyDayOff->DAYOFFDATE * 1;
				$idxSchedule	=	$dateDayOffDB - 1;
				
				foreach($dataDriver as $keyDriver){
					if($keyDriver->IDDRIVER == $idDriverDB){
						$keyDriver->DATASCHEDULE[$idxSchedule][]	=	array(0, "Off", $idDayOffDB);
						break;
					}
				}
				
			}
			
		}
		
		setResponseOk(array("token"=>$this->newToken, "dataDriver"=>$dataDriver, "arrDates"=>$arrDates));
		
	}
	
	public function getDataDayOffRequest(){
	
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$idDayOffRequest=	validatePostVar($this->postVar, 'idDayOffRequest', false);
		$dayOffDate		=	validatePostVar($this->postVar, 'dayOffDate', false);
		$dayOffStatus	=	validatePostVar($this->postVar, 'dayOffStatus', false);
		$searchKeyword	=	validatePostVar($this->postVar, 'searchKeyword', false);
		$scheduleDate	=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDate	=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$yearMonth		=	$scheduleDate->format('Y-m');
		$dataTable		=	$this->ModelDriverSchedule->getDataDayOffRequest($yearMonth, $idDayOffRequest, $dayOffDate, $dayOffStatus, $searchKeyword);
	
		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}

	public function getDataReservationList(){
	
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$monthly			=	validatePostVar($this->postVar, 'monthly', false);
		$scheduleDate		=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDate		=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$scheduleDateStr	=	$scheduleDate->format('d M Y');
		$scheduleMonthStr	=	$scheduleDate->format('M Y');
		$scheduleMonth		=	$scheduleDate->format('Y-m');
		$scheduleDate		=	$scheduleDate->format('Y-m-d');
		$idDriver			=	validatePostVar($this->postVar, 'idDriver', true);
		$idDriverType		=	validatePostVar($this->postVar, 'idDriverType', true);
		$dataTable			=	$this->ModelDriverSchedule->getDataReservationList($monthly, $scheduleDate, $scheduleMonth, $idDriverType, $idDriver);
	
		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "scheduleDateStr"=>$scheduleDateStr, "scheduleMonthStr"=>$scheduleMonthStr));
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "scheduleDateStr"=>$scheduleDateStr, "scheduleMonthStr"=>$scheduleMonthStr));
	
	}

	public function getDataDriverList(){
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$scheduleDate	=	validatePostVar($this->postVar, 'scheduleDate', true);
		$scheduleDate	=	DateTime::createFromFormat('d-m-Y', $scheduleDate);
		$scheduleDateStr=	$scheduleDate->format('d M Y');
		$scheduleDate	=	$scheduleDate->format('Y-m-d');
		$idDriverType	=	validatePostVar($this->postVar, 'idDriverType', true);
		$dataTable		=	$this->ModelDriverSchedule->getDataDriverList($scheduleDate, $idDriverType);
	
		if(!$dataTable) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "scheduleDateStr"=>$scheduleDateStr));
		
		foreach($dataTable as $keyTable){
			$idDriver			=	$keyTable->IDDRIVER;
			$totalScheduleDriver=	$this->ModelDriverSchedule->getTotalScheduleDriver($idDriver, $scheduleDate);
			$dataDayOffDriver	=	$this->ModelDriverSchedule->getDataDayOffDriver($idDriver, $scheduleDate);
			
			$keyTable->TOTALSCHEDULE=	$totalScheduleDriver;
			$keyTable->IDDAYOFF		=	$dataDayOffDriver['IDDAYOFF'];
			$keyTable->REASON		=	$dataDayOffDriver['REASON'];
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "scheduleDateStr"=>$scheduleDateStr));
	}

	public function saveDriverSchedule(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$idDriver			=	validatePostVar($this->postVar, 'idDriver', false);
		$reservationProduct	=	validatePostVar($this->postVar, 'arrIDReservationDetails', false);
		$totalInsertSchedule=	0;
		
		if(!isset($idDriver) || $idDriver == "" || $idDriver == 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select driver for reservation schedule"));
		if(!is_array($reservationProduct) || count($reservationProduct) <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select at least one reservation schedule"));
		
		$tmToday				=	new DateTime("today");
		$dataUserAdmin			=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$dataDriver				=	$this->MainOperation->getDataDriver($idDriver);
		$dataMessageType		=	$this->MainOperation->getDataMessageType(1);
		$userAdminName			=	$dataUserAdmin['NAME'];
		$driverName				=	$dataDriver['NAME'];
		$driverTokenFCM			=	$dataDriver['TOKENFCM'];
		$driverPartnershipType	=	$dataDriver['PARTNERSHIPTYPE'];
		$activityMessage		=	$dataMessageType['ACTIVITY'];
		
		foreach($reservationProduct as $idReservationDetails){
			
			$detailReservation		=	$this->ModelDriverSchedule->getDetailReservation(0, $idReservationDetails);
			$allReservationSchedule	=	$this->ModelDriverSchedule->getAllDriverScheduleReservation($detailReservation['IDRESERVATION'], $detailReservation['SCHEDULEDATEDB']);
			
			if(!$allReservationSchedule){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
			} else {
				
				foreach($allReservationSchedule as $reservationSchedule){
					$idReservationDetailsDB	=	$reservationSchedule->IDRESERVATIONDETAILS;
					$arrInsert				=	array(
						"IDRESERVATIONDETAILS"	=>	$idReservationDetailsDB,
						"IDDRIVER"				=>	$idDriver,
						"USERINPUT"				=>	$userAdminName,
						"DATETIMEINPUT"			=>	date("Y-m-d H:i:s"),
						"STATUS"				=>	1
					);
					$procInsert	=	$this->MainOperation->addData("t_scheduledriver", $arrInsert);

					if($procInsert['status']){
						
						$idDriverSchedule	=	$procInsert['insertID'];
						$reservationDetails	=	$this->ModelDriverSchedule->getDetailReservation($idDriverSchedule, $idReservationDetailsDB);
						$this->updateLastJobRateDriver($idDriver);
						
						if($reservationDetails){
							
							$idReservation	=	$reservationDetails['IDRESERVATION'];
							$customerName	=	$reservationDetails['CUSTOMERNAME'];
							$rsvTitle		=	$reservationDetails['RESERVATIONTITLE'];
							$rsvService		=	$reservationDetails['PRODUCTNAME'];
							$dateSchedule	=	$reservationDetails['SCHEDULEDATE'];
							$tmDateSchedule	=	DateTime::createFromFormat('Y-m-d', $reservationDetails['SCHEDULEDATEDB']);
							$diffDays		=	$tmToday->diff($tmDateSchedule);
							$diffDays		=	(integer)$diffDays->format( "%R%a" );
							$statusRsvUpdate=	$this->getStatusScheduleReservation($idReservation);
							$collectPayment	=	$this->ModelDriverSchedule->getCollectPaymentReservation($idReservation, $idDriver);
							$strDateSchedule=	$strCollectPayment	=	"";
							
							$this->MainOperation->updateData("t_reservation", array("STATUS"=>$statusRsvUpdate), "IDRESERVATION", $idReservation);
							switch($diffDays) {
								case 0	:	$strDateSchedule	=	" (Today)"; break;
								case +1	:	$strDateSchedule	=	" (Tomorrow)"; break;
								default	:	$strDateSchedule	=	""; break;
							}
							
							if($driverPartnershipType == 4){
								$detailCarDriver	=	$this->ModelDriverSchedule->getDetailCarByDriver($idDriver);
								$durationProduct	=	$detailReservation['DURATIONHOUR'];
								
								if($detailCarDriver){
									$idCarVendor		=	$detailCarDriver['IDCARVENDOR'];
									$idCarType			=	$detailCarDriver['IDCARTYPE'];
									$idVendor			=	$detailCarDriver['IDVENDOR'];
									$vendorName			=	$detailCarDriver['VENDORNAME'];
									$dataSelfDriveCost	=	$this->ModelDriverSchedule->getDataSelfDriveCost($idVendor, $idCarType);
									
									if($dataSelfDriveCost){
										$durationSelfDrive	=	$nominalCost	=	0;
										$productName		=	$additionalNotes=	'';
										
										foreach($dataSelfDriveCost as $keySelfDriveCost){
											if($keySelfDriveCost->DURATION <= $durationProduct){
												$durationSelfDrive	=	$keySelfDriveCost->DURATION;
												$productName		=	$keySelfDriveCost->PRODUCTNAME;
												$nominalCost		=	$keySelfDriveCost->NOMINALFEE;
												$additionalNotes	=	$keySelfDriveCost->NOTES;
											}
										}
										
										if($durationSelfDrive > 0){
											$notesInsert					=	"Auto input system, car cost office driver (".$driverName.")";
											$notesInsert					=	$additionalNotes != "" ? $notesInsert.". ".$additionalNotes : $notesInsert;
											$arrSendAPIReservationDetailsCar=	[
												"arrDateSchedule"	=>	[$detailReservation['SCHEDULEDATEDB']],
												"customerName"		=>	$detailReservation['CUSTOMERNAME'],
												"durationSelfDrive"	=>	$durationSelfDrive,
												"idCarType"			=>	$idCarType,
												"idDriverType"		=>	0,
												"idProductType"		=>	3,
												"idReservation"		=>	$detailReservation['IDRESERVATION'],
												"idVendor"			=>	$idVendor,
												"jobRate"			=>	1,
												"jobType"			=>	0,
												"userAdminName"		=>	$this->postVar['NAME'],
												"nominalCost"		=>	$nominalCost,
												"notes"				=>	$notesInsert,
												"pricePerPaxAdult"	=>	0,
												"pricePerPaxChild"	=>	0,
												"pricePerPaxInfant"	=>	0,
												"productName"		=>	$productName,
												"productRank"		=>	1,
												"scheduleType"		=>	1,
												"ticketAdultPax"	=>	0,
												"ticketChildPax"	=>	0,
												"ticketInfantPax"	=>	0,
												"totalPriceAdult"	=>	0,
												"totalPriceChild"	=>	0,
												"totalPriceInfant"	=>	0,
												"vendorName"		=>	$vendorName,
												"voucherStatus"		=>	0
											];
											
											$strArrSendAPIReservationDetails=	rawurlencode(base64_encode(json_encode($arrSendAPIReservationDetailsCar)));
											$urlAPIInsertReservationDetails	=	BASE_URL."reservation/APISaveReservationDetails/".$strArrSendAPIReservationDetails;
											$resAPIInsertReservationDetails	=	json_decode(trim(curl_get_file_contents($urlAPIInsertReservationDetails)));
											
											if(isset($resAPIInsertReservationDetails) && $resAPIInsertReservationDetails != null && $resAPIInsertReservationDetails->status == 200){
												$arrResultInsert		=	$resAPIInsertReservationDetails->arrResultInsert;
												$idReservationDetailsCar=	$arrResultInsert[0][0];
												$arrSendAPICarSchedule	=	[
													"idCarVendor"		=>	$idCarVendor,
													"idDriver"			=>	$idDriver,
													"reservationDetails"=>	[$idReservationDetailsCar],
													"userAdminName"		=>	$this->postVar['NAME']
												];
												
												$strArrSendAPIScheduleCar	=	rawurlencode(base64_encode(json_encode($arrSendAPICarSchedule)));
												$urlAPIInsertScheduleCar	=	BASE_URL."schedule/carSchedule/APISaveCarSchedule/".$strArrSendAPIScheduleCar;
												$resAPIInsertScheduleCar	=	json_decode(trim(curl_get_file_contents($urlAPIInsertScheduleCar)));
											}
										}
									}
								}
							}
							
							if($collectPayment){
								foreach($collectPayment as $dataCollect){
									$idDriverCollect	=	$dataCollect->IDDRIVER;
									if($idDriverCollect == "" || $idDriverCollect == 0){
										$newFinanceScheme				=	$this->MainOperation->getNewFinanceSchemeDriver($idDriver);
										$idCollectPayment				=	$dataCollect->IDCOLLECTPAYMENT;
										$partnerName					=	$this->MainOperation->getDriverNameById($idDriver);
										$arrUpdateCollect				=	array("IDDRIVER"=>$idDriver);
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

							$titleDB		=	"New schedule for ".$dateSchedule;
							$titleMsg		=	"New schedule for ".$dateSchedule.$strDateSchedule;
							$body			=	"Details schedule\n";
							$body			.=	"Customer Name : ".$customerName."\n";
							$body			.=	"Reservation Title : ".$rsvTitle."\n";
							$body			.=	"Service : ".$rsvService;
							$body			.=	$strCollectPayment;
							$additionalArray=	array(
													"activity"	=>	$activityMessage,
													"idPrimary"	=>	$idReservationDetailsDB,
												);
							
							$arrInsertMsg	=	array(
														"IDMESSAGEPARTNERTYPE"	=>	1,
														"IDPARTNERTYPE"			=>	2,
														"IDPARTNER"				=>	$idDriver,
														"IDPRIMARY"				=>	$idReservationDetailsDB,
														"TITLE"					=>	$titleDB,
														"MESSAGE"				=>	$body,
														"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
												);
							$procInsertMsg	=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
								
							if($procInsertMsg['status']){
								if($driverTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($driverTokenFCM, $titleMsg, $body, $additionalArray);
								if(PRODUCTION_URL){
									$RTDB_refCode			=	$dataDriver['RTDBREFCODE'];
									if($RTDB_refCode && $RTDB_refCode != ''){
										try {
											$factory			=	(new Factory)
																	->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
																	->withDatabaseUri(FIREBASE_RTDB_URI);
											$database			=	$factory->createDatabase();
											$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/driver/".$RTDB_refCode."/unconfirmedReservation");
											$referencePartnerVal=	$referencePartner->getValue();
											if($referencePartnerVal != null || !is_null($referencePartnerVal)){
												$referencePartner->update([
													'cancelReservationStatus'		=>  false,
													'newReservationStatus'          =>  true,
													'timestampUpdate'               =>  gmdate('YmdHis'),
													'newReservationDateTime'        =>  $strDateSchedule,
													'newReservationJobTitle'        =>  $rsvService,
													'totalUnconfirmedReservation'   =>  $this->MainOperation->getTotalUnconfirmedReservationPartner(2, $idDriver)
												]);
											}
										} catch (Exception $e) {
										}
									}
								}
							}
						}
						$totalInsertSchedule++;
					}					
				}
			}
		}
		
		$this->updateWebappStatisticTags();
		setResponseOk(array("token"=>$this->newToken, "msg"=>$totalInsertSchedule." reservation schedule(s) have been added to the driver : ".$driverName, "driverTokenFCM"=>$driverTokenFCM));
	}
	
	private function updateLastJobRateDriver($idDriver){
		
		$strArrJobRate	=	$this->ModelDriverSchedule->getStrArrJobRateDriver($idDriver);
		
		if($strArrJobRate && $strArrJobRate != ""){
			$expStrArrJobRate	=	explode(",", $strArrJobRate);
			$totalArrJobRate	=	count($expStrArrJobRate);
			
			if($totalArrJobRate < 5){
				while($totalArrJobRate <= 5) {
					$expStrArrJobRate[]	=	1;
					$totalArrJobRate++;
				}
				$strArrJobRate	=	implode(',', $expStrArrJobRate);
			}
			
			$this->MainOperation->updateData("m_driver", array("LASTJOBRATE"=>$strArrJobRate), "IDDRIVER", $idDriver);
		}
		
		return true;
		
	}
	
	public function saveReservationDetailsFee(){

		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$productName			=	validatePostVar($this->postVar, 'productName', true);
		$nominal				=	str_replace(",", "", validatePostVar($this->postVar, 'nominal', true));
		$notes					=	validatePostVar($this->postVar, 'notes', false);
		$idReservationDetails	=	validatePostVar($this->postVar, 'idReservationDetails', true);
		$dataUserAdmin			=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName			=	$dataUserAdmin['NAME'];
		$arrUpdateDetails		=	array(
											"PRODUCTNAME"	=>	$productName,
											"NOMINAL"		=>	$nominal,
											"NOTES"			=>	$notes,
											"USERINPUT"		=>	$userAdminName." (Correction)",
											"DATETIMEINPUT"	=>	date('Y-m-d H:i:s')
									);
		$procUpdateDetails		=	$this->MainOperation->updateData("t_reservationdetails", $arrUpdateDetails, "IDRESERVATIONDETAILS", $idReservationDetails);
			
		if(!$procUpdateDetails['status']){
			switchMySQLErrorCode($procUpdateDetails['errCode'], $this->newToken);
		}
		
		$dataReservationDetails	=	$this->ModelDriverSchedule->getDetailReservation(0, $idReservationDetails);
		if($dataReservationDetails){
			$idFee			=	$dataReservationDetails['IDFEE'];
			$arrUpdateFee	=	array(
									"JOBTITLE"		=>	$productName,
									"FEENOMINAL"	=>	$nominal,
									"FEENOTES"		=>	$notes
								);
			$this->MainOperation->updateData("t_fee", $arrUpdateFee, "IDFEE", $idFee);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Reservation details fee has been updated"));

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
	
	public function deleteDriverSchedule(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('ModelReservation');
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$idDriverSchedule	=	validatePostVar($this->postVar, 'idData', true);
		$isFeeWithdrawn		=	$this->ModelDriverSchedule->isFeeWithdrawn($idDriverSchedule);
	
		if($idDriverSchedule <= 0) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
		if($isFeeWithdrawn) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Cannot delete this schedule. The fee for this schedule <b>has been withdrawn</b> by driver."));
		
		$reservationDetails		=	$this->ModelDriverSchedule->getDetailReservation($idDriverSchedule, 0);
		$idReservationDetails	=	$reservationDetails ? $reservationDetails['IDRESERVATIONDETAILS'] : 0;
		$idDriver				=	$reservationDetails ? $reservationDetails['IDDRIVER'] : 0;
		$procDeleteSchedule		=	$this->MainOperation->deleteData("t_scheduledriver", array("IDSCHEDULEDRIVER"=>$idDriverSchedule));
		$procDeleteFee			=	$this->MainOperation->deleteData("t_fee", array("IDRESERVATIONDETAILS"=>$idReservationDetails, "IDDRIVER"=>$idDriver));

		if(!$procDeleteSchedule) switchMySQLErrorCode($procDeleteSchedule['errCode'], $this->newToken);		
		
		$idReservation		=	$reservationDetails['IDRESERVATION'];
		$idDriver			=	$reservationDetails['IDDRIVER'];
		$driverHandle		=	$this->ModelReservation->getReservationHandleDriver($idReservation);
		$carVendorHandle	=	$this->ModelReservation->getReservationHandleVendorCar($idReservation);
		$ticketvendorHandle	=	$this->ModelReservation->getReservationHandleVendorTicket($idReservation);
		$statusRsvUpdate	=	$this->getStatusScheduleReservation($idReservation);
		$this->updateLastJobRateDriver($idDriver);
		
		if(empty($driverHandle) && empty($carVendorHandle) && empty($ticketvendorHandle)){
			$this->MainOperation->updateData("t_reservation", array("STATUS"=>$statusRsvUpdate), "IDRESERVATION", $idReservation);
		}
		
		$tmToday				=	new DateTime("today");
		$idDriver				=	$reservationDetails['IDDRIVER'];
		$dataDriver				=	$this->MainOperation->getDataDriver($idDriver);
		$dataMessageType		=	$this->MainOperation->getDataMessageType(3);
		$driverPartnershipType	=	$dataDriver['PARTNERSHIPTYPE'];
		$activityMessage		=	$dataMessageType['ACTIVITY'];
		$collectPayment			=	$this->ModelDriverSchedule->getCollectPaymentReservation($idReservation, $idDriver);
		
		if($collectPayment){
			foreach($collectPayment as $dataCollect){
				$idDriverCollect	=	$dataCollect->IDDRIVER;
				if($idDriverCollect == $idDriver){
					$idCollectPayment	=	$dataCollect->IDCOLLECTPAYMENT;
					$arrUpdateCollect	=	array(
												"IDDRIVER"					=>	0,
												"STATUS"					=>	0,
												"STATUSSETTLEMENTREQUEST"	=>	0
											);
					$arrUpdateCollectPaymentHistory	=	array("DESCRIPTION" => "Collect payment is set to -");
					
					$this->MainOperation->updateData("t_collectpayment", $arrUpdateCollect, "IDCOLLECTPAYMENT", $idCollectPayment);
					$this->MainOperation->updateData('t_collectpaymenthistory', $arrUpdateCollectPaymentHistory, array("IDCOLLECTPAYMENT" => $idCollectPayment, "STATUS" => 0));
				}
			}
		}
		
		if($driverPartnershipType == 4){
			$dateScheduleDB			=	$reservationDetails['SCHEDULEDATEDB'];
			$detailDetailScheduleCar=	$this->ModelDriverSchedule->getDetailScheduleCarDriver($idDriver, $idReservation, $dateScheduleDB);
			
			if($detailDetailScheduleCar){
				$idCarSchedule				=	$detailDetailScheduleCar['IDSCHEDULECAR'];
				$idReservationDetailsCar	=	$detailDetailScheduleCar['IDRESERVATIONDETAILS'];
				$arrSendAPIDeleteCarSchedule=	[
					"idCarSchedule"		=>	$idCarSchedule
				];
				
				$strArrSendAPIDeleteScheduleCar	=	rawurlencode(base64_encode(json_encode($arrSendAPIDeleteCarSchedule)));
				$urlAPIDeleteScheduleCar		=	BASE_URL."schedule/carSchedule/APIDeleteCarSchedule/".$strArrSendAPIDeleteScheduleCar;
				$resAPIDeleteScheduleCar		=	json_decode(trim(curl_get_file_contents($urlAPIDeleteScheduleCar)));
				
				if(isset($resAPIDeleteScheduleCar) && $resAPIDeleteScheduleCar != null && $resAPIDeleteScheduleCar->status == 200){
					$arrSendAPIDeleteCarSchedule=	[
						"idReservationDetails"	=>	$idReservationDetailsCar,
						"idReservation"			=>	$idReservation,
						"dateSchedule"			=>	""
					];
					
					$strArrSendDeleteAPIScheduleCar	=	rawurlencode(base64_encode(json_encode($arrSendAPIDeleteCarSchedule)));
					$urlAPIDeleteScheduleCar		=	BASE_URL."reservation/APIDeactivateReservationDetails/".$strArrSendDeleteAPIScheduleCar;
					$resAPIDeleteScheduleCar		=	json_decode(trim(curl_get_file_contents($urlAPIDeleteScheduleCar)));
				}
			}
		}
		
		if($reservationDetails){
			$idReservationDetails	=	$reservationDetails['IDRESERVATIONDETAILS'];
			$customerName			=	$reservationDetails['CUSTOMERNAME'];
			$rsvTitle				=	$reservationDetails['RESERVATIONTITLE'];
			$rsvService				=	$reservationDetails['PRODUCTNAME'];
			$dateSchedule			=	$reservationDetails['SCHEDULEDATE'];
			$driverTokenFCM			=	$dataDriver['TOKENFCM'];
			$tmDateSchedule			=	DateTime::createFromFormat('Y-m-d', $reservationDetails['SCHEDULEDATEDB']);
			$diffDays				=	$tmToday->diff($tmDateSchedule);
			$diffDays				=	(integer)$diffDays->format( "%R%a" );
			$strDateSchedule		=	"";
			
			switch($diffDays) {
				case 0	:	$strDateSchedule	=	" (Today)"; break;
				case +1	:	$strDateSchedule	=	" (Tomorrow)"; break;
				default	:	$strDateSchedule	=	""; break;
			}

			$titleDB		=	"Schedule cancelation for ".$dateSchedule;
			$titleMsg		=	"Schedule cancelation for ".$dateSchedule.$strDateSchedule;
			$body			=	"Details schedule\n";
			$body			.=	"Customer Name : ".$customerName."\n";
			$body			.=	"Reservation Title : ".$rsvTitle."\n";
			$body			.=	"Service : ".$rsvService;
			$additionalArray=	array(
									"activity"	=>	$activityMessage,
									"idPrimary"	=>	$idReservation,
								);
			
			$arrInsertMsg	=	array(
										"IDMESSAGEPARTNERTYPE"	=>	3,
										"IDPARTNERTYPE"			=>	2,
										"IDPARTNER"				=>	$idDriver,
										"IDPRIMARY"				=>	$idReservationDetails,
										"TITLE"					=>	$titleDB,
										"MESSAGE"				=>	$body,
										"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
								);
			$procInsertMsg	=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
				
			if($procInsertMsg['status']){
				if($driverTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($driverTokenFCM, $titleMsg, $body, $additionalArray);
				if(PRODUCTION_URL){
					$RTDB_refCode			=	$dataDriver['RTDBREFCODE'];
					if($RTDB_refCode && $RTDB_refCode != ''){
						try {
							$factory			=	(new Factory)
													->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
													->withDatabaseUri(FIREBASE_RTDB_URI);
							$database			=	$factory->createDatabase();
							$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/driver/".$RTDB_refCode."/unconfirmedReservation");
							$referencePartnerVal=	$referencePartner->getValue();
							if($referencePartnerVal != null || !is_null($referencePartnerVal)){
								$referencePartner->update([
									'timestampUpdate'               =>  gmdate('YmdHis'),
									'totalUnconfirmedReservation'   =>  $this->MainOperation->getTotalUnconfirmedReservationPartner(2, $idDriver),
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
		
		$this->updateWebappStatisticTags();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Reservation schedule has been deleted", ""=>$reservationDetails));

	}
	
	public function getDetailReservation(){

		$this->load->model('Schedule/ModelDriverSchedule');
		
		$idDriverSchedule		=	validatePostVar($this->postVar, 'idDriverSchedule', false);
		$idReservationDetails	=	validatePostVar($this->postVar, 'idReservationDetails', false);
		$detailData				=	$this->ModelDriverSchedule->getDetailReservation($idDriverSchedule, $idReservationDetails);
		
		if(!$detailData){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found"));
		}
		
		$idReservation			=	$detailData['IDRESERVATION'];
		$listScheduleActivity	=	$this->ModelDriverSchedule->getListDetailScheduleActivity($idReservation);
		
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$detailData, "listScheduleActivity"=>$listScheduleActivity));
	
	}
	
	public function getDetailDayOff(){

		$this->load->model('Schedule/ModelDriverSchedule');
		
		$idDayoff	=	validatePostVar($this->postVar, 'idDayoff', false);
		$detailData	=	$this->ModelDriverSchedule->getDetailDayOff($idDayoff);
		
		if(!$detailData){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Detail not found"));
		}
		
		// $allowDeleteDayOff	=	strtotime($detailData['DATEDAYOFFRAW']) >= strtotime(date('Y-m-d')) ? true : false;		
		$allowDeleteDayOff		=	true ;
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$detailData, "allowDeleteDayOff"=>$allowDeleteDayOff));
	
	}
	
	public function testAPINotifOrder(){
		
		$this->load->library('fcm');
		$this->load->model('Schedule/ModelDriverSchedule');

		$detailMessage	=	$this->ModelDriverSchedule->getDetailMessage(184466);
		$driverTokenFCM	=	$detailMessage['TOKENFCM'];
		$titleMsg		=	$detailMessage['TITLE'];
		$body			=	$detailMessage['MESSAGE'];
		$additionalArray=	array(
								"activity"	=>	"Order",
								"idPrimary"	=>	$detailMessage['IDPRIMARY'],
							);
		$sendTest		=	"Invalid user token FCM";
		
		if($driverTokenFCM != "" && PRODUCTION_URL){
			$sendTest	=	$this->fcm->sendPushNotification($driverTokenFCM, $titleMsg, $body, $additionalArray, true);
		}
		setResponseOk(array("msg"=>"OK", "result"=>$sendTest, "fcmToken"=>$driverTokenFCM));

	}
	
	public function approveDayOffRequest(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$idDayoffRequest	=	validatePostVar($this->postVar, 'idData', true);
		$dayOffDetails		=	$this->ModelDriverSchedule->getDetailDayOffRequest($idDayoffRequest);
		
		if($idDayoffRequest <= 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
		}
		
		$idDriver			=	$dayOffDetails['IDDRIVER'];
		$partnershipType	=	$dayOffDetails['PARTNERSHIPTYPE'];
		$dayOffDate			=	$dayOffDetails['DATEDAYOFF'];
		$dateDayOff			=	DateTime::createFromFormat('Y-m-d', $dayOffDetails['DATEDAYOFF']);
		$dateDayOffStr		=	$dateDayOff->format('d M Y');
		$monthYearDayOffStr	=	$dateDayOff->format('M Y');
		$dayOffYearMonth	=	substr($dayOffDate, 0, 7);
		$dataDayOffLimit	=	$this->getDataDayOffLimit($dayOffDate);
		
		if($dataDayOffLimit['isLimited']){
			setResponseForbidden(
				array(
					"token"	=>	$this->newToken,
					"msg"	=>	"Day off input is no longer allowed.<br/>The number of days off has exceeded the limit allowed (<b>".$dataDayOffLimit['maxDayOffNumber']."</b>) for the selected date"
				)
			);
		}
		
		$dayOffLimitDriver	=	$this->ModelDriverSchedule->getTotalDayOffDriverInMonth($idDriver, $dayOffYearMonth);
		$dayOffLimitSetting	=	$this->MainOperation->getValueSystemSettingVariable(11);
		
		if($dayOffLimitDriver >= $dayOffLimitSetting && ($partnershipType == 1 || $partnershipType == 4)){
			setResponseForbidden(
				array(
					"token"	=>	$this->newToken,
					"msg"	=>	"Day off input is no longer allowed.<br/>The number of days off for this driver in <b>".$monthYearDayOffStr."</b> has reached its maximum limit <b>[".$dayOffLimitSetting."]</b>"
				)
			);
		}
		
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$dataUserAdmin['NAME'];
		$arrUpdateRequest	=	array(
									"STATUS"			=>	1,
									"DATETIMEAPPROVAL"	=>	date('Y-m-d H:i:s'),
									"USERAPPROVAL"		=>	$userAdminName
								);
		$procUpdateRquest	=	$this->MainOperation->updateData("t_dayoffrequest", $arrUpdateRequest, "IDDAYOFFREQUEST", $idDayoffRequest);
		
		if(!$procUpdateRquest['status']){
			switchMySQLErrorCode($procUpdateRquest['errCode'], $this->newToken);
		}
		
		
		if($dayOffDetails){
			
			$arrInsertDayOff	=	array(
										"IDDAYOFFREQUEST"	=>	$idDayoffRequest,
										"IDDRIVER"			=>	$dayOffDetails['IDDRIVER'],
										"IDCARVENDOR"		=>	$dayOffDetails['IDCARVENDOR'],
										"DATEDAYOFF"		=>	$dayOffDate,
										"REASON"			=>	$dayOffDetails['REASON'],
										"DATETIMEINPUT"		=>	$dayOffDetails['DATETIMEINPUT']
									);
			$procInsertDayOff	=	$this->MainOperation->addData("t_dayoff", $arrInsertDayOff);
		
			if(!$procInsertDayOff['status']) switchMySQLErrorCode($procInsertDayOff['errCode'], $this->newToken);
		
			$dataDriver		=	$this->MainOperation->getDataDriver($dayOffDetails['IDDRIVER']);
			$driverTokenFCM	=	$dataDriver['TOKENFCM'];
			$dataMessageType=	$this->MainOperation->getDataMessageType(4);
			$activityMessage=	$dataMessageType['ACTIVITY'];
			$title			=	"Day off request has been approved";
			$body			=	"Details day off\n";
			$body			=	"Date : ".$dateDayOffStr."\n";
			$body			.=	"Reason : ".$dayOffDetails['REASON'];
			$additionalArray=	array(
									"activity"	=>	$activityMessage,
									"idPrimary"	=>	$idDayoffRequest,
								);
			
			$arrInsertMsg	=	array(
										"IDMESSAGEPARTNERTYPE"	=>	4,
										"IDPARTNERTYPE"			=>	2,
										"IDPARTNER"				=>	$dayOffDetails['IDDRIVER'],
										"IDPRIMARY"				=>	$idDayoffRequest,
										"TITLE"					=>	$title,
										"MESSAGE"				=>	$body,
										"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
								);
			$procInsertMsg	=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
				
			if($procInsertMsg['status']){
				if($driverTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($driverTokenFCM, $title, $body, $additionalArray);
			}
			
			$this->MainOperation->calculateScheduleDriverMonitor($dayOffDate);
			
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Day off request has been approved"));

	}
	
	public function rejectDayOffRequest(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$idDayoffRequest	=	validatePostVar($this->postVar, 'idData', true);
		
		if($idDayoffRequest <= 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
		}
		
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$dataUserAdmin['NAME'];
		$arrUpdateRequest	=	array(
									"STATUS"			=>	-1,
									"DATETIMEAPPROVAL"	=>	date('Y-m-d H:i:s'),
									"USERAPPROVAL"		=>	$userAdminName
								);
		$procUpdateRquest	=	$this->MainOperation->updateData("t_dayoffrequest", $arrUpdateRequest, "IDDAYOFFREQUEST", $idDayoffRequest);
		
		if(!$procUpdateRquest['status']){
			switchMySQLErrorCode($procUpdateRquest['errCode'], $this->newToken);
		}
		
		$dayOffDetails		=	$this->ModelDriverSchedule->getDetailDayOffRequest($idDayoffRequest);
		
		if($dayOffDetails){
			
			$dateDayOff		=	DateTime::createFromFormat('Y-m-d', $dayOffDetails['DATEDAYOFF']);
			$dataDriver		=	$this->MainOperation->getDataDriver($dayOffDetails['IDDRIVER']);
			$dateDayOffStr	=	$dateDayOff->format('d M Y');
			$driverTokenFCM	=	$dataDriver['TOKENFCM'];
			$dataMessageType=	$this->MainOperation->getDataMessageType(5);
			$activityMessage=	$dataMessageType['ACTIVITY'];
			$title			=	"Day off request has been rejected";
			$body			=	"Details day off\n";
			$body			=	"Date : ".$dateDayOffStr."\n";
			$body			.=	"Reason : ".$dayOffDetails['REASON'];
			$additionalArray=	array(
									"activity"	=>	$activityMessage,
									"idPrimary"	=>	$idDayoffRequest,
								);
			
			$arrInsertMsg	=	array(
										"IDMESSAGEPARTNERTYPE"	=>	5,
										"IDPARTNERTYPE"			=>	2,
										"IDPARTNER"				=>	$dayOffDetails['IDDRIVER'],
										"IDPRIMARY"				=>	$idDayoffRequest,
										"TITLE"					=>	$title,
										"MESSAGE"				=>	$body,
										"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
								);
			$procInsertMsg	=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
				
			if($procInsertMsg['status']){
				if($driverTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($driverTokenFCM, $title, $body, $additionalArray);
			}
			
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Day off request has been rejected"));

	}
	
	public function deleteDayOff(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$idDayoff	=	validatePostVar($this->postVar, 'idData', true);
		
		if($idDayoff <= 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
		}
		
		$dayOffDetails		=	$this->ModelDriverSchedule->getDetailDayOff($idDayoff);
		$idDayoffRequest	=	$dayOffDetails['IDDAYOFFREQUEST'];
		$dayOffReqDetails	=	$this->ModelDriverSchedule->getDetailDayOffRequest($idDayoffRequest);
		$procDeleteDayOff	=	$this->MainOperation->deleteData("t_dayoff", array("IDDAYOFF"=>$idDayoff));
		
		if(!$procDeleteDayOff['status']){
			switchMySQLErrorCode($procDeleteDayOff['errCode'], $this->newToken);
		}
		
		
		if($dayOffReqDetails){
			
			$this->MainOperation->updateData("t_dayoffrequest", array("STATUS"=>-2), "IDDAYOFFREQUEST", $idDayoffRequest);
			
			$dateDayOff		=	DateTime::createFromFormat('Y-m-d', $dayOffReqDetails['DATEDAYOFF']);
			$dataDriver		=	$this->MainOperation->getDataDriver($dayOffReqDetails['IDDRIVER']);
			$dateDayOffStr	=	$dateDayOff->format('d M Y');
			$dateDayOff		=	$dateDayOff->format('Y-m-d');
			$driverTokenFCM	=	$dataDriver['TOKENFCM'];
			$dataMessageType=	$this->MainOperation->getDataMessageType(5);
			$activityMessage=	$dataMessageType['ACTIVITY'];
			$title			=	"Day off data has been deleted";
			$body			=	"Details day off\n";
			$body			=	"Date : ".$dateDayOffStr."\n";
			$body			.=	"Reason : ".$dayOffReqDetails['REASON'];
			$additionalArray=	array(
									"activity"	=>	$activityMessage,
									"idPrimary"	=>	$idDayoffRequest,
								);
			
			$arrInsertMsg	=	array(
										"IDMESSAGEPARTNERTYPE"	=>	5,
										"IDPARTNERTYPE"			=>	2,
										"IDPARTNER"				=>	$dayOffReqDetails['IDDRIVER'],
										"IDPRIMARY"				=>	$idDayoffRequest,
										"TITLE"					=>	$title,
										"MESSAGE"				=>	$body,
										"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
								);
			$procInsertMsg	=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
				
			if($procInsertMsg['status']){
				if($driverTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($driverTokenFCM, $title, $body, $additionalArray);
			}
			
			$this->MainOperation->calculateScheduleDriverMonitor($dateDayOff);

		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Day off data has been deleted"));

	}
	
	public function resendScheduleNotification(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$idDriverSchedule	=	validatePostVar($this->postVar, 'idData', true);
		
		if($idDriverSchedule <= 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data"));
		}
		
		$detailNotification	=	$this->ModelDriverSchedule->getDetailNotificationSchedule($idDriverSchedule);
		
		if(!$detailNotification){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Failed to resend notification. Maybe the schedule you chose is no longer available. Please try again later"));
		}
		
		$idDriver			=	$detailNotification['IDDRIVER'];
		$dataMessageType	=	$this->MainOperation->getDataMessageType(1);
		$dataDriver			=	$this->MainOperation->getDataDriver($idDriver);
		$activityMessage	=	$dataMessageType['ACTIVITY'];
		$titleMsg			=	$detailNotification['TITLE'];
		$body				=	$detailNotification['MESSAGE'];
		$driverTokenFCM		=	$dataDriver['TOKENFCM'];
		$additionalArray	=	array(
									"activity"	=>	$activityMessage,
									"idPrimary"	=>	$detailNotification['IDRESERVATIONDETAILS'],
								);
								
		$procResendNotifOrder	=	[];
		if($driverTokenFCM != "") {
			$procResendNotifOrder	=	$this->fcm->sendPushNotification($driverTokenFCM, $titleMsg, $body, $additionalArray, true);
		}
		
		if(PRODUCTION_URL){
			$RTDB_refCode			=	$dataDriver['RTDBREFCODE'];
			if($RTDB_refCode && $RTDB_refCode != ''){
				try {
					$factory			=	(new Factory)
											->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
											->withDatabaseUri(FIREBASE_RTDB_URI);
					$database			=	$factory->createDatabase();
					$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/driver/".$RTDB_refCode."/unconfirmedReservation");
					$referencePartnerVal=	$referencePartner->getValue();
					$strDateSchedule	=	$detailNotification['SCHEDULEDATE'];
					$rsvService			=	$detailNotification['PRODUCTNAME'];
					if($referencePartnerVal != null || !is_null($referencePartnerVal)){
						$referencePartner->update([
							'cancelReservationStatus'		=>  false,
							'newReservationStatus'          =>  true,
							'timestampUpdate'               =>  gmdate('YmdHis'),
							'newReservationDateTime'        =>  $strDateSchedule,
							'newReservationJobTitle'        =>  $rsvService,
							'totalUnconfirmedReservation'   =>  $this->MainOperation->getTotalUnconfirmedReservationPartner(2, $idDriver)
						]);
					}
				} catch (Exception $e) {
				}
			}
		}
		
		setResponseOk(array("token"=>$this->newToken, "procResendNotifOrder"=>$procResendNotifOrder, "msg"=>"Schedule notification sent successfully"));

	}
	
	public function saveDriverDayOff(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$level				=	validatePostVar($this->postVar, 'LEVEL', true);
		$idDriver			=	validatePostVar($this->postVar, 'idData', true);
		$params				=	validatePostVar($this->postVar, 'params', true);
		$params				=	explode("|", $params);
		$reason				=	unescapejs($params[1]);
		$dayOffDate			=	$params[0];
		$dayOffDate			=	DateTime::createFromFormat('d-m-Y', $dayOffDate);
		$monthYearDayOffStr	=	$dayOffDate->format('M Y');
		$dayOffDateStr		=	$dayOffDate->format('d M Y');
		$dayOffDate			=	$dayOffDate->format('Y-m-d');
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$dataUserAdmin['NAME'];
		
		if(strtotime($dayOffDate) < strtotime(date('Y-m-d'))){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Day off date can not be less than today"));
		}
		
		$dataDayOffLimit	=	$this->getDataDayOffLimit($dayOffDate);
		if($dataDayOffLimit['isLimited']){
			setResponseForbidden(
				array(
					"token"	=>	$this->newToken,
					"msg"	=>	"Day off input is no longer allowed.<br/>The number of days off has exceeded the limit allowed (<b>".$dataDayOffLimit['maxDayOffNumber']."</b>) for the selected date"
				)
			);
		}
		
		$dayOffYearMonth	=	substr($dayOffDate, 0, 7);
		$dayOffLimitDriver	=	$this->ModelDriverSchedule->getTotalDayOffDriverInMonth($idDriver, $dayOffYearMonth);
		$dayOffLimitSetting	=	$this->MainOperation->getValueSystemSettingVariable(11);
		$detailDriver		=	$this->MainOperation->getDataDriver($idDriver);
		$partnershipType	=	$detailDriver['PARTNERSHIPTYPE'];
		
		if($dayOffLimitDriver >= $dayOffLimitSetting && ($partnershipType == 1 || $partnershipType == 4) && $level != 1){
			setResponseForbidden(
				array(
					"token"	=>	$this->newToken,
					"msg"	=>	"Day off input is no longer allowed.<br/>The number of days off for this driver in <b>".$monthYearDayOffStr."</b> has reached its maximum limit <b>[".$dayOffLimitSetting."]</b>"
				)
			);
		}
		
		$arrInsertRequest	=	array(
									"IDDRIVER"			=>	$idDriver,
									"IDCARVENDOR"		=>	0,
									"DATEDAYOFF"		=>	$dayOffDate,
									"REASON"			=>	$reason.". Input by ".$userAdminName." (Admin)",
									"DATETIMEINPUT"		=>	date('Y-m-d H:i:s'),
									"STATUS"			=>	1,
									"DATETIMEAPPROVAL"	=>	date('Y-m-d H:i:s'),
									"USERAPPROVAL"		=>	$userAdminName
								);
		$procInsertRequest	=	$this->MainOperation->addData("t_dayoffrequest", $arrInsertRequest);
	
		if(!$procInsertRequest['status']){
			switchMySQLErrorCode($procInsertRequest['errCode'], $this->newToken);
		}
		
		$idDayoffRequest	=	$procInsertRequest['insertID'];
		$arrInsertDayOff	=	array(
									"IDDAYOFFREQUEST"	=>	$idDayoffRequest,
									"IDDRIVER"			=>	$idDriver,
									"IDCARVENDOR"		=>	0,
									"DATEDAYOFF"		=>	$dayOffDate,
									"REASON"			=>	$reason.". Input by ".$userAdminName." (Admin)",
									"DATETIMEINPUT"		=>	date('Y-m-d H:i:s')
								);
		$procInsertDayOff	=	$this->MainOperation->addData("t_dayoff", $arrInsertDayOff);
	
		if(!$procInsertDayOff['status']){
			switchMySQLErrorCode($procInsertDayOff['errCode'], $this->newToken);
		}
	
		$dataDriver		=	$this->MainOperation->getDataDriver($idDriver);
		$driverTokenFCM	=	$dataDriver['TOKENFCM'];
		$dataMessageType=	$this->MainOperation->getDataMessageType(4);
		$activityMessage=	$dataMessageType['ACTIVITY'];
		$title			=	"Day off has been added";
		$body			=	"Details day off\n";
		$body			=	"Date : ".$dayOffDateStr."\n";
		$body			.=	"Reason : ".$reason;
		$additionalArray=	array(
								"activity"	=>	$activityMessage,
								"idPrimary"	=>	$idDayoffRequest,
							);
		
		$arrInsertMsg	=	array(
									"IDMESSAGEPARTNERTYPE"	=>	4,
									"IDPARTNERTYPE"			=>	2,
									"IDPARTNER"				=>	$idDriver,
									"IDPRIMARY"				=>	$idDayoffRequest,
									"TITLE"					=>	$title,
									"MESSAGE"				=>	$body,
									"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
							);
		$procInsertMsg	=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
			
		if($procInsertMsg['status']){
			if($driverTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($driverTokenFCM, $title, $body, $additionalArray);
		}
		
		$this->MainOperation->calculateScheduleDriverMonitor($dayOffDate);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Day off data has been added"));

	}
	
	private function getDataDayOffLimit($date){
		
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverSchedule');
		
		$dataDriverMonitor	=	$this->MainOperation->getDataDriverMonitor($date);
		$maxDayOffNumber	=	$dataDriverMonitor['TOTALDAYOFFQUOTA'];
		$totalDayOffInDate	=	$dataDriverMonitor['TOTALOFFDRIVER'];
		$isLimited			=	$totalDayOffInDate >= $maxDayOffNumber;
		
		return array(
			"isLimited"			=>	$isLimited,
			"maxDayOffNumber"	=>	$maxDayOffNumber
		);
		
	}
	
	private function updateWebappStatisticTags(){
		
		if(PRODUCTION_URL){
			$this->load->model('MainOperation');
			$totalUndeterminedSchedule	=	$this->MainOperation->getTotalUndeterminedSchedule();

			try {
				$factory	=	(new Factory)
								->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
								->withDatabaseUri(FIREBASE_RTDB_URI);
				$database	=	$factory->createDatabase();
				$database->getReference(FIREBASE_RTDB_MAINREF_NAME."undeterminedSchedule")->set($totalUndeterminedSchedule);
			} catch (Exception $e) {
			}
		}
		
		return true;
		
	}
}