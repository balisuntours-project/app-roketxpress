<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';
require_once(FCPATH."vendor/phpmailer/phpmailer/src/Exception.php");
require_once(FCPATH."vendor/phpmailer/phpmailer/src/PHPMailer.php");
require_once(FCPATH."vendor/phpmailer/phpmailer/src/SMTP.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Ddeboer\Imap\Server;
use Ddeboer\Imap\Search;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Search\Email\From;
use Ddeboer\Imap\Search\Flag\Unseen;
use Ddeboer\Imap\Search\Text\Subject;
use Ddeboer\Imap\Search\Text\Keyword;
use Ddeboer\Imap\Search\Text\Body;
use Ddeboer\Imap\Search\LogicalOperator\OrConditions;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class CronTest extends CI_controller {
	
    private $connection;
	private $arraySource;
	private $arrayDefaultCurrency;
	private $dom;
	
	public function __construct(){
     
		parent::__construct();
		libxml_use_internal_errors(false);
		$this->dom						=	new DOMDocument();
		$this->dom->strictErrorChecking	=	false;
		$this->dom->recover				=	true;
		$this->dom->preserveWhiteSpace	=	false;
		
		$username			= MAILBOX_USERNAME;
		$password			= MAILBOX_PASSWORD;
		$server				= new Server('imap.gmail.com/ssl/NoValidate-Cert');
		$this->connection	= $server->authenticate($username, $password);
		$this->getDataEmailSenderSource();
		
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function readKlikBCAPayroll(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		$search		= new SearchExpression();
		$search->addCondition(new From(MAIL_KLIKBCAPAYROLL_ADDRESS));
		$search->addCondition(new Unseen());

		$inbox		=	$this->connection->getMailbox('INBOX');
		$messages	=	$inbox->getMessages($search);

		foreach ($messages as $message) {
	
			$message->markAsSeen();
			
			$subject			=	$message->getSubject();
			$dateTimeMail		=	$message->getDate();
			$dateTimeMail		=	DateTime::createFromFormat(DateTimeInterface::ATOM, $dateTimeMail->format(DateTimeInterface::ATOM));
			$dateTimeMail->setTimeZone(new DateTimeZone('GMT+7'));
			$dateTimeMail		=	$dateTimeMail->format('Y-m-d H:i:s');
			$htmlFileName		=	$this->saveHTMLFileKlikBCAPayroll($message);
			
			if($subject == 'Beneficiary Information'){
				
				$messageHTML	=	$message->getBodyHTML();
				@$this->dom->loadHTML($messageHTML);
				$table			= 	$this->dom->getElementsByTagName('table')->item(0);
				$rows			=	$table->getElementsByTagName('tr');
				$accountNumber	=	$remarkTransfer	=	$partnerCode	=	"";
				$nominalTransfer=	0;
		
				foreach($rows as $row) {
					
					$cols		=	$row->getElementsByTagName('td');					
					switch(trim(preg_replace('/\t+/', '', $cols[0]->textContent))){
						case "Rekening Tujuan"	:	$accountNumber	=	trim(preg_replace('/\t+/', '', $cols[2]->textContent));
													break;
						case "Nominal"			:	$numberFormatter=	new NumberFormatter("en_EN", NumberFormatter::DECIMAL);
													$strNominal		=	trim(preg_replace('/\t+/', '', $cols[2]->textContent));
													$nominalTransfer=	str_replace("Rp ", "", $strNominal);
													$nominalTransfer=	$numberFormatter->parse($nominalTransfer, NumberFormatter::TYPE_INT32);
													break;
						case "Berita"			:	$partnerCode	=	trim(preg_replace('/\t+/', '', $cols[2]->textContent));
													break;
						case ""					:	$remarkTransfer	=	trim(preg_replace('/\t+/', '', $cols[2]->textContent));
													break;
						default					:	break;
					}
					
				}
				
				$detailTransferList	=	$this->ModelCron->getDetailTransferList($accountNumber, $nominalTransfer, $partnerCode, $remarkTransfer);

				if($detailTransferList){
					
					$idTransferList			=	$detailTransferList['IDTRANSFERLIST'];
					$idPartnerType			=	$detailTransferList['IDPARTNERTYPE'];
					$idPartner				=	$detailTransferList['IDPARTNER'];
					$idLoanDriverRequest	=	$detailTransferList['IDLOANDRIVERREQUEST'];
					$idWithdrawal			=	$detailTransferList['IDWITHDRAWAL'];
					$arrUpdateTransferList	=	array(
													"RECEIPTFILE"	=>	$htmlFileName,
													"STATUSDATETIME"=>	date('Y-m-d H:i:s'),
													"STATUS"		=>	2
												);
					$this->MainOperation->updateData("t_transferlist", $arrUpdateTransferList, "IDTRANSFERLIST", $idTransferList);
					
					if($idLoanDriverRequest != 0){
						
						$arrUpdateRequest	=	array(
													"STATUS"			=>	2,
													"DATETIMECONFIRM"	=>	date('Y-m-d H:i:s'),
													"USERCONFIRM"		=>	'Auto System'
												);
						$procUpdateRquest	=	$this->MainOperation->updateData("t_loandriverrequest", $arrUpdateRequest, "IDLOANDRIVERREQUEST", $idLoanDriverRequest);
						
						if($procUpdateRquest['status']){
							$detailLoanRequest	=	$this->ModelCron->getDetailLoanRequest($idLoanDriverRequest);
							$statusLoanCapital	=	$detailLoanRequest['STATUSLOANCAPITAL'];
							$strLoanType		=	$statusLoanCapital == 1 ? "Loan (".$detailLoanRequest['LOANTYPE'].")" : "Prepaid Capital";
							$arrInsertRecord	=	array(
														"IDDRIVER"				=>	$detailLoanRequest['IDDRIVER'],
														"IDLOANTYPE"			=>	$detailLoanRequest['IDLOANTYPE'],
														"IDLOANDRIVERREQUEST"	=>	$idLoanDriverRequest,
														"TYPE"					=>	'D',
														"DESCRIPTION"			=>	"Fund for ".$strLoanType." (".$detailLoanRequest['NOTES']."). Transferred to ".$detailLoanRequest['BANKNAME']." - ".$detailLoanRequest['ACCOUNTNUMBER']." - ".$detailLoanRequest['ACCOUNTHOLDERNAME'].". Input by : ".$detailLoanRequest['USERUPDATE'],
														"AMOUNT"				=>	$detailLoanRequest['AMOUNT'],
														"DATETIMEINPUT"			=>	date('Y-m-d H:i:s'),
														"USERINPUT"				=>	$detailLoanRequest['USERUPDATE']
													);
							$this->MainOperation->addData("t_loandriverrecord", $arrInsertRecord);
						}
						
						$dataMessageType	=	$this->MainOperation->getDataMessageType(6);
						$activityMessage	=	$dataMessageType['ACTIVITY'];
						$title				=	"Loan funds / prepaid capital have been transferred";
						$body				=	"Your ".$strLoanType." [".number_format($nominalTransfer, 0, '.', ',')."] has been transferred";
						$additionalArray	=	array(
													"activity"	=>	$activityMessage,
													"idPrimary"	=>	$idLoanDriverRequest,
												);
						$arrInsertMsg		=	array(
														"IDMESSAGEPARTNERTYPE"	=>	6,
														"IDPARTNERTYPE"			=>	$idPartnerType,
														"IDPARTNER"				=>	$idPartner,
														"IDPRIMARY"				=>	$idLoanDriverRequest,
														"TITLE"					=>	$title,
														"MESSAGE"				=>	$body,
														"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
												);
						$procInsertMsg		=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
							
						if($procInsertMsg['status']){
							$dataDriver			=	$this->MainOperation->getDataDriver($idPartner);
							$driverTokenFCM		=	$dataDriver['TOKENFCM'];
							if($driverTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($driverTokenFCM, $title, $body, $additionalArray);
						}
						
					} else if($idWithdrawal != 0){
						
						$arrUpdateWithdrawal		=	array("STATUSWITHDRAWAL"	=>	2);
						$procUpdateWithdrawal		=	$this->MainOperation->updateData("t_withdrawalrecap", $arrUpdateWithdrawal, "IDWITHDRAWALRECAP", $idWithdrawal);
						$detailWithdrawal			=	$this->ModelCron->getDetailWithdrawalRequest($idWithdrawal);
						$dateWithdrawalRequest		=	$detailWithdrawal['DATETIMEREQUEST'];
						$messageWithdrawalRequest	=	$detailWithdrawal['MESSAGE'];
						$totalAmountWithdrawal		=	number_format($detailWithdrawal['TOTALWITHDRAWAL'], 0, '.', ',');
						
						$dataPartner		=	$idPartnerType == 1 ? $this->MainOperation->getDataVendor($idPartner) : $this->MainOperation->getDataDriver($idPartner);
						$dataMessageType	=	$this->MainOperation->getDataMessageType(8);
						$partnerTokenFCM	=	$dataPartner['TOKENFCM'];
						$activityMessage	=	$dataMessageType['ACTIVITY'];

						$titleDB			=	"Your withdrawal has been transferred";
						$titleMsg			=	$titleDB;
						$body				=	"Details Withdrawal \n";
						$body				.=	"Date Request : ".$dateWithdrawalRequest."\n";
						$body				.=	"Total Amount : IDR ".$totalAmountWithdrawal."\n";
						$body				.=	"Message : ".$messageWithdrawalRequest;
						$additionalArray	=	array(
													"activity"	=>	$activityMessage,
													"idPrimary"	=>	$idWithdrawal
												);
					
						$arrInsertMsg		=	array(
														"IDMESSAGEPARTNERTYPE"	=>	8,
														"IDPARTNERTYPE"			=>	$idPartnerType,
														"IDPARTNER"				=>	$idPartner,
														"IDPRIMARY"				=>	$idWithdrawal,
														"TITLE"					=>	$titleDB,
														"MESSAGE"				=>	$body,
														"DATETIMEINSERT"		=>	date('Y-m-d H:i:s')
												);
						$procInsertMsg		=	$this->MainOperation->addData("t_messagepartner", $arrInsertMsg);
							
						if($procInsertMsg['status']){
							if($partnerTokenFCM != "" && PRODUCTION_URL) $this->fcm->sendPushNotification($partnerTokenFCM, $titleMsg, $body, $additionalArray);
							if(PRODUCTION_URL){
								$RTDB_refCode			=	$dataPartner['RTDBREFCODE'];
								$RTDB_partnerTypeStr	=	$idPartnerType == 1 ? "vendor" : "driver";
								if($RTDB_refCode && $RTDB_refCode != ''){
									try {
										$factory			=	(new Factory)
																->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
																->withDatabaseUri(FIREBASE_RTDB_URI);
										$database			=	$factory->createDatabase();
										$referencePartner   =	$database->getReference(FIREBASE_RTDB_MAINREF_NAME_PARTNER."/".$RTDB_partnerTypeStr."/".$RTDB_refCode."/activeWithdrawal");
										$referencePartnerVal=	$referencePartner->getValue();
										if($referencePartnerVal != null || !is_null($referencePartnerVal)){
											$referencePartner->update([
												'newWithdrawalNotif'		=>  true,
												'newWithdrawalNotifDetail'	=>  nl2br($body),
												'newWithdrawalNotifStatus'	=>  $status,
												'timestampUpdate'			=>  gmdate('YmdHis'),
												'totalActiveWithdrawal'		=>  $this->MainOperation->getTotalActiveWithdrawalPartner($idPartnerType, $idPartner)
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
			
		}
		
		echo "End klik BCA payroll - ".date("d M Y H:i");
		
	}
	
	private function getDataEmailSenderSource(){
		
		$this->load->model('MainOperation');
		$dataSenderEmail	=	$this->MainOperation->getDataSenderEmailSource();
		$returnArr			=	$defaultCurrencyArray	=	array();
		
		if($dataSenderEmail){
			
			foreach($dataSenderEmail as $keySenderEmail){
				
				$arrSenderEmail	=	array();
				
				if($keySenderEmail->EMAILSENDER1 != '') $arrSenderEmail[]	=	$keySenderEmail->EMAILSENDER1;
				if($keySenderEmail->EMAILSENDER2 != '') $arrSenderEmail[]	=	$keySenderEmail->EMAILSENDER2;
				if($keySenderEmail->EMAILSENDER3 != '') $arrSenderEmail[]	=	$keySenderEmail->EMAILSENDER3;
				
				if(count($arrSenderEmail) > 0){
					$returnArr[$keySenderEmail->IDSOURCE]	=	$arrSenderEmail;
				}
				
				$defaultCurrencyArray[$keySenderEmail->IDSOURCE]	=	$keySenderEmail->DEFAULTCURRENCY;
					
			}
			
		}
		
		$this->arraySource		=	$returnArr;
		$this->defaultCurrency	=	$defaultCurrencyArray;
		
	}
	
	public function calculateScheduleDriverMonitor(){
		
		$this->load->model('MainOperation');

		echo "Start - Calculate Schedule Driver Monitor - ".date('Y-m-d H:i:s')."<br/>";

		for($i=0; $i<=30; $i++){
			$dateSchedule	=	date('Y-m-d', strtotime("+$i days"));
			$this->MainOperation->calculateScheduleDriverMonitor($dateSchedule);
			echo $dateSchedule." - OK <br/>";
		}
		
		echo "End - Calculate Schedule Driver Monitor - ".date('Y-m-d H:i:s')."<br/>";
		
	}
	
	public function readMailboxBookingCode(){
		
		$this->load->model('MainOperation');
		$dataBookingCode	=	$this->MainOperation->getBookingCode();

		if($dataBookingCode){
			$bookingCode		=	$dataBookingCode['BOOKINGCODE'];			
			if($bookingCode){
				$htmlFileName	=	$this->readMailbox($bookingCode);
				$arrUpdate		=	array(
										"FILEHTML"			=>	$htmlFileName,
										"STATUS"			=>	1,
										"DATETIMEPROCESS"	=>	date('Y-m-d H:i:s')
									);
				$this->MainOperation->updateData("temp_bookingcode", $arrUpdate, "BOOKINGCODE", $bookingCode);
			}
			echo $bookingCode."<br/>";
			echo $htmlFileName."<br/>";
		}
		
		echo "end";
		die();
		
	}
	
	public function readMailboxBookingCodeParam($bookingCode){
		
		$htmlFileName	=	$this->readMailbox($bookingCode);
		echo $bookingCode."<br/>";
		echo $htmlFileName."<br/>";
		echo "end";
		die();
		
	}
	
	public function readMailboxCorrection(){
		
		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		$dataCorrection	=	$this->ModelCron->getDataCorrection(100);
		
		if($dataCorrection){
			foreach($dataCorrection as $keyCorrection){
				$idIncomeCorrection	=	$keyCorrection->IDINCOMECORRECTION;
				$htmlFileName		=	$keyCorrection->HTMLFILENAME;
				$exchangeCurrency	=	$keyCorrection->EXCHANGECURRENCY;
				$pathHtmlFile		=	PATH_EMAIL_HTML_FILE.$htmlFileName;

				if(file_exists($pathHtmlFile)){
					$htmlContent	=	file_get_contents($pathHtmlFile);
					$splitHtml		=	explode('USD $', $htmlContent);
					$splitHtml		=	explode('<', $splitHtml[1]);
					$nominalUSD		=	$splitHtml[0];
					$nominalIDR		=	$nominalUSD * $exchangeCurrency;
					$this->MainOperation->updateData('a_incomecorrection', ['STATUS' => 1, 'AMOUNTCORRECTION' => $nominalUSD, 'AMOUNTIDR' => $nominalIDR], 'IDINCOMECORRECTION', $idIncomeCorrection);
					echo $nominalUSD."<br/>";
				} else {
					$this->MainOperation->updateData('a_incomecorrection', ['STATUS' => -1], 'IDINCOMECORRECTION', $idIncomeCorrection);
				}
			}
		}
		
	}
	
	public function readMailbox($bookingCodeParam = false){

		$this->load->model('MainOperation');
		$arraySource		=	$this->arraySource;
		$bookingCodeParam	=	'RZ230927XW2998';
		
		foreach($arraySource as $idSource=>$arrSenderEmail){
			
			foreach($arrSenderEmail as $senderEmail){
				
				$search		= new SearchExpression();
				$search->addCondition(new From($senderEmail));
				if(!$bookingCodeParam) $search->addCondition(new Unseen());
				if($bookingCodeParam) $search->addCondition(new Body($bookingCodeParam));

				$inbox		=	$this->connection->getMailbox('INBOX');
				$messages	=	$inbox->getMessages($search);

				foreach ($messages as $message) {
			
					$message->markAsSeen();
					
					$subject			=	$message->getSubject();
					$dateTimeMail		=	$message->getDate();
					$dateTimeMail		=	DateTime::createFromFormat(DateTimeInterface::ATOM, $dateTimeMail->format(DateTimeInterface::ATOM));
					$dateTimeMail->setTimeZone(new DateTimeZone('GMT+7'));
					$dateTimeMail		=	$dateTimeMail->format('Y-m-d H:i:s');
					$htmlFileName		=	$this->saveHTMLFile($message);
					$sourceName			=	"-";
					
					if(substr($subject, 0, 12) == 'New booking:'){
						$arrDataProcess	=	$this->processBokunMail($message);
						$bookingCode	=	explode("booking ref:", $subject);
						$bookingCode	=	trim($bookingCode[1]);
						$arrDataProcess['BOOKINGCODE']	=	$bookingCode;
						$sourceName		=	"Bokun";
					}
					
					if(substr($subject, 0, 21) == 'Klook Order Confirmed'){
						$arrDataProcess	=	$this->processKlookMail($subject, $message);
						$sourceName		=	"Klook";
					}
					
					if(substr($subject, 0, 15) == 'New Booking for'){
						$arrDataProcess	=	$this->processViatorMail($message);
						$sourceName		=	"Viator";
					}
					
					if(substr($subject, 0, 10) == 'Booking - '){
						$arrDataProcess	=	$this->processGetYourGuideMail($message);
						$sourceName		=	"GetYourGuide";
					}
					
					if(substr($subject, 0, 33) == '[Bali Sun Tours] New booking for '){
						$arrDataProcess	=	$this->processEBookingMail($message);
						$sourceName		=	"eBooking";
					}
					
					if(substr($subject, 0, 30) == '[Bali SUN Tours] from KKdayMkp'){
						$arrDataProcess	=	$this->processKKDayMail($message);
						$sourceName		=	"KKdayMkp";
					}
					
					if(substr($subject, 0, 20) == 'Booking Confirmed - '){
						$arrDataProcess	=	$this->processPelagoMail($message);
						$sourceName		=	"Pelago";
					}
					
					if(substr($subject, 0, 27) == 'Important: Request for New ' || substr($subject, 0, 31) == 'Urgent: Second Request for New ' || substr($subject, 0, 30) == 'Urgent: Third Request for New '){
						$arrDataProcess	=	$this->processShoreExcursionsMail($message);
						$sourceName		=	"Pelago";
					}
					
					if($sourceName != "" && $sourceName != "-"){
						$defaultCurrency						=	$this->defaultCurrency[$idSource];
						$pickUpLocation							=	$arrDataProcess['PICKUPLOCATION'];
						$hotelName								=	$arrDataProcess['HOTELNAME'];
						$idAreaPickUp							=	0;
						$areaKeywordCheck						=	"";
						
						if(isset($pickUpLocation) && $pickUpLocation != ""){
							$areaKeywordCheck	=	$pickUpLocation;
						} else if (isset($hotelName) && $hotelName != ""){
							$areaKeywordCheck	=	$hotelName;
						}
						
						if($areaKeywordCheck != ""){
							$idAreaPickUp		=	$this->getIdAreaPickUpByKeyword($areaKeywordCheck);
						}
						
						$arrDataProcess['IDRESERVATIONTYPE']	=	1;
						$arrDataProcess['IDSOURCE']				=	$idSource;
						$arrDataProcess['IDAREA']				=	$idAreaPickUp;
						$arrDataProcess['MAILSUBJECT']			=	$subject;
						$arrDataProcess['INCOMEAMOUNTCURRENCY']	=	$defaultCurrency;
						$arrDataProcess['HTMLFILENAME']			=	$htmlFileName;
						$arrDataProcess['DATETIMEMAIL']			=	date('Y-m-d H:i:s');
						$arrDataProcess['DATETIMEMAILREAD']		=	date('Y-m-d H:i:s');
						
						if($bookingCodeParam != false){
							echo json_encode($arrDataProcess);
							die();
						}

						$procInsert	=	$this->MainOperation->addData('t_mailbox', $arrDataProcess);
						if($procInsert['status']){
							$idMailbox			=	$procInsert['insertID'];
							$strReservationDate	=	date('d M Y', strtotime($arrDataProcess['RESERVATIONDATE']));
							$this->sendNewMailNotif($idMailbox, $arrDataProcess['MAILSUBJECT'], $strReservationDate, $sourceName);
							$this->updateWebappStatisticTags($sourceName, $subject);
						}
						
						if(!$bookingCodeParam) echo $htmlFileName."<br/>";
						if($bookingCodeParam) return $htmlFileName;
					} else {
						echo $subject." - Non Comfirmed Order<br/>";
					}
					
				}
				
			}
			
		}
		
		$this->calculateScheduleDriverMonitor();
		echo "End - ".date("d M Y H:i");
		
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
	
	private function updateWebappStatisticTags($newMailSourceName, $newMailSubject){
  
		if(PRODUCTION_URL){
			$this->load->model('MainOperation');
			$totalUnprocessedReservationMail=	$this->MainOperation->getTotalUnprocessedReservationMail();
			
			try {
				$factory	=	(new Factory)
								->withServiceAccount(FIREBASE_PRIVATE_KEY_PATH)
								->withDatabaseUri(FIREBASE_RTDB_URI);
				$database	=	$factory->createDatabase();
				$reference	=	$database->getReference(FIREBASE_RTDB_MAILREF_NAME)
								->set([
									   'newMailSourceName'		=>	$newMailSourceName,
									   'newMailStatus'			=>	true,
									   'newMailSubject'			=>	$newMailSubject,
									   'totalUnprocessedMail'	=>	$totalUnprocessedReservationMail,
									   'timestampUpdate'		=>	gmdate("YmdHis")
									  ]);
			} catch (Exception $e) {
			}
		}
		return true;		
  
	}
	
	private function saveHTMLFileKlikBCAPayroll($message){
		
		$messageNumber	=	$message->getNumber();
		$messageHTML	=	$message->getBodyHTML();
		$fileName		=	$messageNumber.".html";
		
		file_put_contents(PATH_HTML_TRANSFER_RECEIPT.$fileName, $messageHTML);
		return $fileName;
		
	}
	
	private function saveHTMLFile($message){
		
		$messageNumber	=	$message->getNumber();
		$messageHTML	=	$message->getBodyHTML();
		$fileName		=	$messageNumber.".html";
		
		file_put_contents(PATH_EMAIL_HTML_FILE.$fileName, $messageHTML);
		return $fileName;
		
	}
	
	private function processBokunMail($message){
		
		$messageHTML	=	$message->getBodyHTML();
		@$this->dom->loadHTML($messageHTML);

		$remark			=	$customerEmail = '-';
		$customerName	=	$reservationTitle = $hotelName = $customerContact = $source	=	'';
		$numberOfAdult	=	$numberOfChild = $numberOfInfant = $incomeAmount = 0;
		$reservationDate=	"0000-00-00";
		$reservationTime=	"00:00";
		
		$table			= 	$this->dom->getElementsByTagName('table')->item(0);
		$rows			=	$table->getElementsByTagName('tr');
		
		foreach ($rows as $row) {
			$cols		=	$row->getElementsByTagName('td');
			switch($cols[0]->textContent){
				case "Product"			:	$reservationTitle	=	trim(preg_replace('/\t+/', '', $cols[1]->textContent));
											$reservationTitle	=	explode("-", $reservationTitle);
											$reservationTitle	=	trim($reservationTitle[1]);
											break;
				case "Customer"			:	$customerName		=	trim($cols[1]->textContent);
											break;
				case "Customer phone"	:	$customerContact	=	trim($cols[1]->textContent);
											$customerContact	=	$customerContact != "" && strlen($customerContact) > 6 ? "+".preg_replace("/[^0-9]/", "", $customerContact) : $customerContact;
											break;
				case "Date"				:	$reservationDate	=	explode("@", $cols[1]->textContent);
											$reservationTime	=	trim($reservationDate[1]);
											$reservationDate	=	str_replace("'", "20", str_replace(".", " ", trim($reservationDate[0])));
											$reservationDate	=	DateTime::createFromFormat("d M Y" , substr($reservationDate, 4));
											$reservationDate	=	$reservationDate->format('Y-m-d');
											break;
				case "PAX"				:	$pax				=	explode("Adult", trim($cols[1]->textContent));
											$numberOfAdult		=	preg_replace('/[^0-9]/', '', $pax[0]);
											break;
				case "Pick-up"			:	$hotelName			=	trim($cols[1]->textContent);
											break;
				case "Sold by"			:	$source				=	trim($cols[1]->textContent);
											break;
				case "Notes"			:	if($source == "Viator.com"){
												$divElem		=	$cols[1]->getElementsByTagName('div');
												$divCount		=	$cols[1]->getElementsByTagName('div')->length;
												$textAmount		=	trim($divElem[$divCount-1]->textContent);
												$incomeAmount	=	preg_replace("/[^0-9.]+/", "", $textAmount);
											}
											break;
				default					:	break;
			}
			
		}
		
		return array(
					"RESERVATIONTITLE"		=>	$reservationTitle,
					"RESERVATIONDATE"		=>	$reservationDate,
					"RESERVATIONTIME"		=>	$reservationTime,
					"CUSTOMERNAME"			=>	$customerName,
					"CUSTOMERCONTACT"		=>	$customerContact,
					"CUSTOMEREMAIL"			=>	$customerEmail,
					"HOTELNAME"				=>	$hotelName,
					"NUMBEROFADULT"			=>	$numberOfAdult,
					"NUMBEROFCHILD"			=>	$numberOfChild,
					"NUMBEROFINFANT"		=>	$numberOfInfant,
					"INCOMEAMOUNT"			=>	$incomeAmount,
					"REMARK"				=>	$remark
				);

	}
	
	private function processKlookMail($subject, $message){

		$messageHTML	=	$message->getBodyHTML();
		@$this->dom->loadHTML($messageHTML);

		$remark			=	$tourPlan = $customerEmail = '-';
		$customerName	=	$bookingCode =	$reservationTitle = $hotelName = $pickUpLocation = $dropOffLocation = $customerContact = $source =	'';
		$numberOfAdult	=	$numberOfChild = $numberOfInfant = $incomeAmount = 0;
		$reservationDate=	"0000-00-00";
		$reservationTime=	"00:00";

		$table			= 	$this->dom->getElementsByTagName('table')->item(0);				
		$data			=	$table->textContent;
		$split_1		=	explode("See order details below for your record.", $data);
		$split_2		=	explode(" Any questions", $split_1[1]);
		$dataSplit		=	preg_split('/\r\n|\r|\n/', $split_2[0]);
		$resultSplit	=	array();
		
		foreach($dataSplit as $keyData){
			$keyData		=	preg_replace('/\s+/', ' ',$keyData);
			if($keyData <> "" && $keyData <> " "){
				$resultSplit[]	=	ltrim($keyData);
			}
		}
		
		$totalArray		=	count($resultSplit)-1;
		if(strpos($subject, 'Airport') !== false) {

			$lastMsgType=	"";
			$iLoop		=	0;
			
			foreach($resultSplit as $dataMessage){
				
				$dataMessage	=	preg_replace('/[^\00-\255]+/u', '', $dataMessage);
				
				if(strpos($dataMessage, 'Booking Reference ID:') !== false || strpos($dataMessage, 'Booking reference ID:') !== false){
					
					$explBookingCode	=	explode(":", $dataMessage);
					$bookingCode		=	$explBookingCode[1];
					
				} else {

					switch($lastMsgType){
						case "Booking reference ID:"	:	
						case "Booking Reference ID:"	:	
						case "Booking Reference ID: "	:	
						case "Booking Reference ID: :"	:	
															$bookingCode		=	$dataMessage;
															break;
						case "Pick Up Date & Time: :"	:	
						case "Pick-up date & time::"	:	
						case "Pick Up Date & Time::"	:	
															$reservationDate	=	substr($dataMessage, 0, 10);
															$reservationTime	=	substr($dataMessage, 11, 5);
															break;
						case "Date Request:"			:	$reservationDate	=	$dataMessage;
															break;
						case "Pick Up Time:"			:	$reservationTime	=	str_replace(".", ":", $dataMessage);
															break;
						case "Lead participant:"		:	
						case "Lead person name:"		:	
															$customerName		=	$dataMessage;
															break;
						case "No. of passengers::"		:	
						case "No. of Passenger(s): :"	:	
															$numberOfAdult		=	$dataMessage * 1;
															break;
						case "From: :"					:	$pickUpLocation		=	$dataMessage;
															break;
						case "To: :"					:	$dropOffLocation	=	$dataMessage;
															break;
						case "Special Requirements:"	:	$remark				=	$dataMessage;
															break;
						case "Lead person mobile:"		:	$customerContact	=	$dataMessage;
															$customerContact	=	$customerContact != "" && strlen($customerContact) > 6 ? "+".preg_replace("/[^0-9]/", "", $customerContact) : $customerContact;
															break;
						case "Lead person email:"		:	$customerEmail		=	$dataMessage;
															break;
						default							:	break;
					}
					$lastMsgType	=	$dataMessage;
					
				}
				
				if($iLoop == 0 && trim($reservationTitle) == "") {
					$reservationTitle		=	$dataMessage;
				}
				$iLoop++;
				
			}
		
		} else {

			$minusIdxNum		=	0;
			if($resultSplit[11] == "Lead person email:"){
				$minusIdxNum	=	1;
			}

			$reservationTitle	=	str_replace("Package:", "", $resultSplit[1]);
			$reservationTitle	=	$resultSplit[0]." - ".substr(ltrim($reservationTitle),2,strlen($reservationTitle)-2);
			$bookingCode		=	$resultSplit[3];
			$customerName		=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[9]);
			$reservationDate	=	$resultSplit[5];
			$reservationTime	=	$resultSplit[7];
			$customerContact	=	$resultSplit[(15-$minusIdxNum)];
			$customerContact	=	$customerContact != "" && strlen($customerContact) > 6 ? "+".preg_replace("/[^0-9]/", "", $customerContact) : $customerContact;
			$customerEmail		=	$resultSplit[(13-$minusIdxNum)];

			if(strpos($resultSplit[1], "Medium MPV") !== false || strpos($resultSplit[1], "Bali Private Car Charter") !== false){
				$rawUnit			=	explode("x", $resultSplit[(17-$minusIdxNum)]);
				$numberOfAdult		=	preg_replace('/[^0-9]/', '', $rawUnit[0]);
			} else {
				$explodeParticipant	=	explode(" x ", $resultSplit[(17-$minusIdxNum)]);
				$numberOfAdult		=	$explodeParticipant[0];
			}

			if(isset($resultSplit[(21-$minusIdxNum)]) && $resultSplit[(21-$minusIdxNum)] == "Pick Up From::"){
				$hotelName		=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(24-$minusIdxNum)]);
			} else if(isset($resultSplit[(25-$minusIdxNum)]) && $resultSplit[(25-$minusIdxNum)] == "Location (Name and Address):") {
				$pickUpLocation	=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(26-$minusIdxNum)]);
			} else if(isset($resultSplit[(23-$minusIdxNum)]) && $resultSplit[(23-$minusIdxNum)] == "Location (Name and Address):") {
				$pickUpLocation	=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(24-$minusIdxNum)]);
			} else if(isset($resultSplit[(29-$minusIdxNum)]) && $resultSplit[(29-$minusIdxNum)] == "Location (Name and Address):") {
				$hotelName		=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(30-$minusIdxNum)]);
			} else {
				if(isset($resultSplit[(22-$minusIdxNum)])){
					$hotelName		=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(22-$minusIdxNum)]);
					if(strlen($hotelName) < 10) $hotelName	=	"";
				}
			}
			
			for($i=(19-$minusIdxNum); $i<=$totalArray; $i++){
				
				$textArray	=	preg_replace('/[^\00-\255]+/u', '', $resultSplit[$i]);
				
				if($textArray == "Participant1 Full Name:"){
					$customerName	=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[$i]);
				}
				
				if($textArray == "Notes:"){
					$remark	=	$resultSplit[$i+1];
				}
				
				if($textArray == "Planned Areas to Visit:"){
					for($iTourplan = $i+1; $iTourplan <= $totalArray; $iTourplan++){
						if(strpos($resultSplit[$iTourplan], ":") === false){
							$tourPlan	.=	$resultSplit[$iTourplan]."\n";
						} else {
							break;
						}
					}
				}
				
				if($textArray == "Number of Passengers:" && strpos($resultSplit[1], "Medium MPV") === false && strpos($resultSplit[1], "Bali Private Car Charter") === false){
					$numberOfAdult		=	preg_replace('/[^0-9]/', '', $resultSplit[$i+1]);
				}
				
				if($textArray == "Planned Itinerary:"){
					for($iTourplan = $i+1; $iTourplan <= $totalArray; $iTourplan++){
						if(strpos($resultSplit[$iTourplan], ":") === false){
							$tourPlan	.=	$resultSplit[$iTourplan]."\n";
						} else {
							break;
						}
					}
				}

				if($textArray == "Pick Up Time:"){
					$reservationTime=	$resultSplit[$i+1];
					$reservationTime=	preg_replace("/[^0-9:.]/", "", $reservationTime);
					if(strlen($reservationTime) <= 2){
						$reservationTime=	str_pad($reservationTime, 2, "0", STR_PAD_LEFT).":00";
					}
					$reservationTime	=	str_pad(str_replace(".", ":", $reservationTime), 5, "0", STR_PAD_LEFT).":00";
				}

				if($textArray == "Number of Passengers:"){
					
					$splitPassengers	=	explode(" ", $resultSplit[$i+1]);
					$iPassengers		=	0;
					
					foreach($splitPassengers as $keyPassangers){
						if($keyPassangers == "Adults" || $keyPassangers == "adults" || $keyPassangers == "Adult"){
							$numberOfAdult	=	$splitPassengers[$iPassengers - 1];
						}
						
						if($keyPassangers == "Childs" || $keyPassangers == "Child"){
							$numberOfChild	=	$splitPassengers[$iPassengers - 1];
						}
						
						if($keyPassangers == "Infants" || $keyPassangers == "Infant"){
							$numberOfInfant	=	$splitPassengers[$iPassengers - 1];
						}
						$iPassengers++;
					}

				}
			
				if(substr($resultSplit[0], 0, 35) == "Bali Private Car Rental with Driver" && $textArray == "Contact Method and Details (WhatsApp/LINE/WeChat):"){
					$customerContact	=	$resultSplit[$i+1];
				}

			}

			if(strpos(strtolower($reservationTitle), "group of") !== false && strpos(strtolower($reservationTitle), "car") !== false){
		
				$pointExplode	=	explode("·", $reservationTitle);
				if(substr($pointExplode[0], -6) == "Hours "){
					foreach($pointExplode as $valueExplode){
						if(strpos($valueExplode, "Group of") !== false){
							if(strpos($valueExplode, "-") !== false){
								$dashExplode	=	explode("-", $valueExplode);
								$numberOfAdult	=	preg_replace('/[^0-9]/', '', end($dashExplode)) * 1;
							} else {
								$numberOfAdult	=	preg_replace('/[^0-9]/', '', $valueExplode) * 1;
							}
						}
					}
				} else {
					$dashExplode	=	explode("-", $reservationTitle);
					$numberOfAdult	=	preg_replace('/[^0-9]/', '', end($dashExplode)) * 1;
				}
			}

			if($customerName == "" || substr($resultSplit[$i-2], 0, 12) === "Participant1"){
				$customerName	=	$resultSplit[$i-1];
			}

		}
		
		// if($bookingCode == 'FMJ004242'){
			// var_dump($numberOfAdult);
			// die();
		// }

		$returnArr	=	 array(
							"RESERVATIONTITLE"		=>	$reservationTitle,
							"RESERVATIONDATE"		=>	$reservationDate,
							"RESERVATIONTIME"		=>	$reservationTime,
							"CUSTOMERNAME"			=>	$customerName,
							"CUSTOMERCONTACT"		=>	$customerContact,
							"CUSTOMEREMAIL"			=>	$customerEmail,
							"HOTELNAME"				=>	$hotelName,
							"PICKUPLOCATION"		=>	$pickUpLocation,
							"DROPOFFLOCATION"		=>	$dropOffLocation,
							"NUMBEROFADULT"			=>	$numberOfAdult,
							"NUMBEROFCHILD"			=>	$numberOfChild,
							"NUMBEROFINFANT"		=>	$numberOfInfant,
							"INCOMEAMOUNT"			=>	$incomeAmount,
							"REMARK"				=>	$remark,
							"TOURPLAN"				=>	$tourPlan,
							"BOOKINGCODE"			=>	$bookingCode
						);
		return $returnArr;

	}
	
	private function processViatorMail($message){
		
		$messageHTML	=	$message->getBodyHTML();
		@$this->dom->loadHTML($messageHTML);

		$tourPlan		=	$customerEmail = '-';
		$remark			=	$customerName =	$bookingCode =	$reservationTitle = $hotelName = $pickUpLocation = $dropOffLocation = $customerContact = $source =	'';
		$numberOfAdult	=	$numberOfChild = $numberOfInfant = $incomeAmount = 0;
		$reservationDate=	"0000-00-00";
		$reservationTime=	"00:00";
		
		$table		= 	$this->dom->getElementsByTagName('table')->item(5);
		$data		=	$table->nodeValue;
		$dataSplit	=	preg_split('/\r\n|\r|\n/', $data);
		
		foreach($dataSplit as $keyData){
			
			if($keyData <> ""){
				
				if (\strpos($keyData, ':') !== false) {
					
					$explodeKey	=	explode(":", $keyData);
					$keyName	=	trim(preg_replace('/\t+/', '', $explodeKey[0]));
					$keyValue	=	trim(preg_replace('/\t+/', '', $explodeKey[1]));
					$keyValue	=	preg_replace("/[^a-zA-Z0-9 .\-\(\)\@\,\/\;\:\=\+\'\"]+/", "", $keyValue);
					
					switch($keyName){
						case "Booking Reference"		:	$bookingCode		=	$keyValue;
															break;
						case "Tour Name"				:	$reservationTitle	=	$keyValue;
															break;
						case "Travel Date"				:	$reservationDate	=	$keyValue;
															$reservationDate	=	DateTime::createFromFormat('D, M d, Y', $reservationDate);
															$reservationDate	=	$reservationDate->format('Y-m-d');
															break;
						case "Lead Traveler Name"		:	$customerName		=	$keyValue;
															break;
						case "Travelers"				:	$explodePax			=	explode(",", $keyValue);
															foreach($explodePax as $keyPax){
																$explkeyPax		=	explode(" ", trim(preg_replace('/\t+/', '', $keyPax)));
																switch($explkeyPax[1]){
																	case "Adult"	:	
																	case "Adults"	:	
																						$numberOfAdult	=	$explkeyPax[0]; break;
																	case "Child"	:	
																	case "Children"	:	
																						$numberOfChild	=	$explkeyPax[0]; break;
																	case "Infant"	:	
																	case "Infants"	:	
																						$numberOfInfant	=	$explkeyPax[0]; break;
																	default			:	break;
																}
															}
															break;
						case "Tour Grade"				:	$remark				.=	trim(strip_tags(html_entity_decode(preg_replace('/\t+/', '', $keyData))))."\n\n";
															break;
						case "Tour Grade Code"			:	$arrReservationTime	=	explode("~", trim(strip_tags(html_entity_decode(preg_replace('/\t+/', '', $keyData)))));
															$reservationTime	=	$arrReservationTime[1];
															break;
						case "Tour Grade Description"	:	$remark				.=	trim(strip_tags(html_entity_decode(preg_replace('/\t+/', '', $keyData))))."\n\n";
															break;
						case "Package booked"			:	$remark				.=	trim(strip_tags(html_entity_decode(preg_replace('/\t+/', '', $keyData))))."\n\n";
															break;
						case "Time Request"				:	$remark				.=	trim(strip_tags(html_entity_decode(preg_replace('/\t+/', '', $keyData))))."\n\n";
															break;
						case "Net Rate"					:	$expl_rate			=	explode(" ", $keyValue);
															$incomeAmount		=	str_replace("$", "", $expl_rate[1]);
															break;
						case "Hotel Pick Up"			:	$hotelName			=	$keyValue == "My hotel is not listed" ? trim(preg_replace('/\t+/', '', $explodeKey[2])) : $keyValue;
															break;
						case "Phone"					:	$customerContact	=	strip_tags($keyValue);
															$customerContact	=	rtrim(str_replace(array("Send the customer a message.", "(Alternate Phone)"), "", $customerContact));
															$customerContact	=	$customerContact != "" && strlen($customerContact) > 6 ? "+".preg_replace("/[^0-9]/", "", $customerContact) : $customerContact;
															break;
						case "Special Requirements"		:	$remark				.=	trim(strip_tags(html_entity_decode(preg_replace('/\t+/', '', $keyData))))."\n\n";
															break;
						default							:	break;
					}
					
				}
				
			}
			
		}

		return array(
					"RESERVATIONTITLE"		=>	$reservationTitle,
					"RESERVATIONDATE"		=>	$reservationDate,
					"RESERVATIONTIME"		=>	$reservationTime,
					"CUSTOMERNAME"			=>	$customerName,
					"CUSTOMERCONTACT"		=>	$customerContact,
					"CUSTOMEREMAIL"			=>	$customerEmail,
					"HOTELNAME"				=>	$hotelName,
					"PICKUPLOCATION"		=>	$pickUpLocation,
					"DROPOFFLOCATION"		=>	$dropOffLocation,
					"NUMBEROFADULT"			=>	$numberOfAdult,
					"NUMBEROFCHILD"			=>	$numberOfChild,
					"NUMBEROFINFANT"		=>	$numberOfInfant,
					"INCOMEAMOUNT"			=>	$incomeAmount,
					"REMARK"				=>	$remark,
					"TOURPLAN"				=>	$tourPlan,
					"BOOKINGCODE"			=>	$bookingCode
				);
	
	}
	
	private function processGetYourGuideMail($message){
		
		$messageHTML	=	$message->getBodyHTML();
		@$this->dom->loadHTML($messageHTML);

		$tourPlan		=	$customerEmail = '-';
		$remark			=	$customerName =	$bookingCode =	$reservationTitle = $hotelName = $pickUpLocation = $dropOffLocation = $customerContact = $source =	'';
		$numberOfAdult	=	$numberOfChild = $numberOfInfant = $incomeAmount = 0;
		$reservationDate=	"0000-00-00";
		$reservationTime=	"00:00";
		
		$paragraphs		= 	$this->dom->getElementsByTagName('p');
		$iParagraph		=	0;
		
		foreach($paragraphs as $node){
		   foreach($node->childNodes as $child) {
			  switch($iParagraph){
				  case 12	:	$reservationTitle	=	$child->nodeValue; break;
				  case 16	:	$fullDate			=	$child->nodeValue;
								$explodeFullDate	=	explode(",", $fullDate);
								$reservationDate	=	$explodeFullDate[0];
								$reservationDate	=	DateTime::createFromFormat('d F Y', $reservationDate);
								$reservationDate	=	$reservationDate->format('Y-m-d');
								$fullReservationTime=	$explodeFullDate[1];
								$expReservationTime	=	explode("(", $fullReservationTime);
								$reservationTime	=	trim($expReservationTime[0]);
								break;
				  case 19	:	$incomeAmount		=	$child->nodeValue;
								$incomeAmount		=	str_replace(array("Rp ", ".00", ","), "", $incomeAmount);
								$incomeAmount		=	number_format($incomeAmount * 7 / 100, 0, ',', '');
								break;
				  case 24	:	$numberOfAdult		=	$child->nodeValue;
								$numberOfAdult		=	str_replace(array(" x"), "", $numberOfAdult);
								break;
				  case 31	:	$bookingCode		=	$child->nodeValue; break;
				  case 35	:	$customerName		=	trim($child->nodeValue); break;
				  case 45	:	$customerContact	=	trim($child->nodeValue);
								$customerContact	=	str_replace(array("Phone: "), "", $customerContact);
								break;
				  case 51	:	$hotelName			=	trim($child->nodeValue);
								$hotelName			=	str_replace(array("Customer hotel:"), "", $hotelName);
								break;
			  }
			  $iParagraph++;
		   }
		}
		
		return array(
					"RESERVATIONTITLE"		=>	$reservationTitle,
					"RESERVATIONDATE"		=>	$reservationDate,
					"RESERVATIONTIME"		=>	$reservationTime,
					"CUSTOMERNAME"			=>	$customerName,
					"CUSTOMERCONTACT"		=>	$customerContact,
					"CUSTOMEREMAIL"			=>	$customerEmail,
					"HOTELNAME"				=>	$hotelName,
					"PICKUPLOCATION"		=>	$pickUpLocation,
					"DROPOFFLOCATION"		=>	$dropOffLocation,
					"NUMBEROFADULT"			=>	$numberOfAdult,
					"NUMBEROFCHILD"			=>	$numberOfChild,
					"NUMBEROFINFANT"		=>	$numberOfInfant,
					"INCOMEAMOUNT"			=>	$incomeAmount,
					"REMARK"				=>	$remark,
					"TOURPLAN"				=>	$tourPlan,
					"BOOKINGCODE"			=>	$bookingCode
				);
	
	}
	
	private function processEBookingMail($message){
		
		$messageHTML	=	$message->getBodyHTML();
		@$this->dom->loadHTML($messageHTML);

		$tourPlan		=	$customerEmail = '-';
		$remark			=	$customerName =	$reservationTitle = $hotelName = $pickUpLocation = $dropOffLocation = $customerContact = $source =	'';
		$numberOfAdult	=	$numberOfChild = $numberOfInfant = $incomeAmount = 0;
		$reservationDate=	"0000-00-00";
		$reservationTime=	"00:00";
		
		$table		= 	$this->dom->getElementsByTagName('table')->item(3);	
		$data		=	$table->nodeValue;
		$dataSplit	=	preg_split('/\r\n|\r|\n/', $data);
		$iDataSplit	=	0;

		foreach($dataSplit as $keyData){
			
			switch($iDataSplit){
				  case 16	:	$reservationTitle		=	trim($keyData); break;
				  case 24	:	$bookingCode			=	trim($keyData); break;
				  case 28	:	$reservationDateStart	=	trim($keyData);
								$reservationDateStartF	=	DateTime::createFromFormat('F d, Y', $reservationDateStart);
								$reservationDate		=	$reservationDateStartF->format('Y-m-d');
								break;
				  case 32	:	$reservationDateEnd		=	trim($keyData);
								$reservationDateEndF	=	DateTime::createFromFormat('F d, Y', $reservationDateEnd);
								break;
				  case 36	:	$numberOfAdult			=	trim($keyData); break;
				  case 41	:	$customerName			=	trim($keyData); break;
				  case 42	:	$customerContact		=	trim($keyData); break;
				  case 43	:	$customerEmail			=	trim($keyData); break;
			  }
			  $iDataSplit++;
			
		}
		
		$durationOfDay	=	$reservationDateEndF->diff($reservationDateStartF)->format("%a");
		$durationOfDay	=	$durationOfDay * 1 + 1;
		return array(
					"RESERVATIONTITLE"		=>	$reservationTitle,
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
					"INCOMEAMOUNT"			=>	$incomeAmount,
					"REMARK"				=>	$remark,
					"TOURPLAN"				=>	$tourPlan,
					"BOOKINGCODE"			=>	$bookingCode
				);
	
	}
	
	private function processKKDayMail($message){
		
		$messageHTML	=	$message->getBodyHTML();
		@$this->dom->loadHTML($messageHTML);

		$tourPlan		=	$customerEmail = '-';
		$remark			=	$customerName =	$reservationTitle = $hotelName = $pickUpLocation = $dropOffLocation = $customerContact = $source =	'';
		$numberOfAdult	=	$numberOfChild = $numberOfInfant = $incomeAmount = 0;
		$reservationDate=	"0000-00-00";
		$reservationTime=	"00:00";
		
		$table		= 	$this->dom->getElementsByTagName('table')->item(1);
		$data		=	$table->nodeValue;
		$dataSplit	=	preg_split('/\r\n|\r|\n/', $data);
		$iDataSplit	=	0;

		foreach($dataSplit as $keyData){
			switch($iDataSplit){
				  case 20	:	$bookingCode			=	trim($keyData); break;
				  case 24	:	$reservationTitle		=	trim($keyData); break;
				  case 32	:	$numberOfAdult			=	trim($keyData); break;
				  case 36	:	$reservationDateStart	=	trim($keyData);
								$reservationDateStart	=	substr($keyData, 0, 10);
								$reservationDateStartF	=	DateTime::createFromFormat('d/m/Y', $reservationDateStart);
								$reservationDate		=	$reservationDateStartF->format('Y-m-d');
								break;
				  case 32	:	$reservationDateEnd		=	$reservationDate;
								break;
			  }
			  $iDataSplit++;
		}
		
		$durationOfDay	=	1;
		return array(
					"RESERVATIONTITLE"		=>	$reservationTitle,
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
					"INCOMEAMOUNT"			=>	$incomeAmount,
					"REMARK"				=>	$remark,
					"TOURPLAN"				=>	$tourPlan,
					"BOOKINGCODE"			=>	$bookingCode
				);
	
	}
	
	private function processPelagoMail($message){
		
		$messageHTML	=	$message->getBodyHTML();
		@$this->dom->loadHTML($messageHTML);

		$tourPlan		=	$customerEmail = '-';
		$remark			=	$customerName =	$reservationTitle = $hotelName = $pickUpLocation = $dropOffLocation = $customerContact = $source =	'';
		$numberOfAdult	=	$numberOfChild = $numberOfInfant = $incomeAmount = 0;
		$reservationDate=	"0000-00-00";
		$reservationTime=	"00:00";
		
		$table		= 	$this->dom->getElementsByTagName('table')->item(8);	
		$data		=	$table->nodeValue;
		$dataSplit	=	preg_split('/\r\n|\r|\n/', $data);
		$iDataSplit	=	0;

		foreach($dataSplit as $keyData){
			
			switch($iDataSplit){
				  case 5	:	$bookingCode		=	str_replace("Booking ID: ", "", trim($keyData)); break;
				  case 6	:	$reservationTitle	=	trim($keyData); break;
				  case 8	:	$numberOfAdult		=	str_replace(" x Person", "", trim($keyData)); break;
				  case 10	:	$reservationDate	=	trim($keyData);
								$reservationDate	=	DateTime::createFromFormat('M d, Y', $reservationDate);
								$reservationDate	=	$reservationDate->format('Y-m-d');
								break;
				  case 12	:	$reservationTime	=	str_replace(array("AM", "PM"), "", trim($keyData));
								$reservationTime	=	str_pad($reservationTime, 5, "0", STR_PAD_LEFT);
								break;
			  }
			  $iDataSplit++;
			
		}
		
		return array(
					"RESERVATIONTITLE"		=>	$reservationTitle,
					"RESERVATIONDATE"		=>	$reservationDate,
					"RESERVATIONTIME"		=>	$reservationTime,
					"CUSTOMERNAME"			=>	$customerName,
					"CUSTOMERCONTACT"		=>	$customerContact,
					"CUSTOMEREMAIL"			=>	$customerEmail,
					"HOTELNAME"				=>	$hotelName,
					"PICKUPLOCATION"		=>	$pickUpLocation,
					"DROPOFFLOCATION"		=>	$dropOffLocation,
					"NUMBEROFADULT"			=>	$numberOfAdult,
					"NUMBEROFCHILD"			=>	$numberOfChild,
					"NUMBEROFINFANT"		=>	$numberOfInfant,
					"INCOMEAMOUNT"			=>	$incomeAmount,
					"REMARK"				=>	$remark,
					"TOURPLAN"				=>	$tourPlan,
					"BOOKINGCODE"			=>	$bookingCode
				);
	
	}
	
	private function processShoreExcursionsMail($message){
		
		$messageHTML	=	$message->getBodyHTML();
		@$this->dom->loadHTML($messageHTML);

		$tourPlan		=	$customerEmail = '-';
		$remark			=	$customerName =	$reservationTitle = $hotelName = $pickUpLocation = $dropOffLocation = $customerContact = $source =	'';
		$numberOfAdult	=	$numberOfChild = $numberOfInfant = $incomeAmount = 0;
		$reservationDate=	"0000-00-00";
		$reservationTime=	"00:00";
		
		$paragraphs		= 	$this->dom->getElementsByTagName('p');
		$iParagraph		=	0;
		
		foreach($paragraphs as $node){
		   foreach($node->childNodes as $child) {
			  switch($iParagraph){
				 case 4		:	$remark				=	trim($child->nodeValue); break;
				 case 20	:	$reservationTitle	=	trim($child->nodeValue); break;
				 case 25	:	$reservationDate	=	trim($child->nodeValue);
								$reservationDate	=	DateTime::createFromFormat('F d, Y', $reservationDate);
								$reservationDate	=	$reservationDate->format('Y-m-d');
								break;
				  case 35	:	$numberOfAdult		=	trim($child->nodeValue); break;
				  case 40	:	$pickUpLocation		=	trim($child->nodeValue); break;
				  case 45	:	$customerName		=	trim($child->nodeValue); break;
				  case 50	:	$incomeAmount		=	trim($child->nodeValue); break;
				  case 55	:	$bookingCode		=	trim($child->nodeValue); break;
			  }
			  $iParagraph++;
		   }
		}
		
		return array(
					"RESERVATIONTITLE"		=>	$reservationTitle,
					"RESERVATIONDATE"		=>	$reservationDate,
					"RESERVATIONTIME"		=>	$reservationTime,
					"CUSTOMERNAME"			=>	$customerName,
					"CUSTOMERCONTACT"		=>	$customerContact,
					"CUSTOMEREMAIL"			=>	$customerEmail,
					"HOTELNAME"				=>	$hotelName,
					"PICKUPLOCATION"		=>	$pickUpLocation,
					"DROPOFFLOCATION"		=>	$dropOffLocation,
					"NUMBEROFADULT"			=>	$numberOfAdult,
					"NUMBEROFCHILD"			=>	$numberOfChild,
					"NUMBEROFINFANT"		=>	$numberOfInfant,
					"INCOMEAMOUNT"			=>	$incomeAmount,
					"REMARK"				=>	$remark,
					"TOURPLAN"				=>	$tourPlan,
					"BOOKINGCODE"			=>	$bookingCode
				);
	
	}
	
	public function bulkNotifMailbox(){
		$this->load->model('MainOperation');
		$this->sendNewMailNotif(22, "Klook Order Confirmed - Airport transfer 3(DO NOT EDIT) - 2022-01-06 14:00:00 - Sudjatmiko Tjokro - WJC445962", "2022-01-06", "Viator");
		echo "ok";
		die();
	}
	
	public function bulkSendPushNotification(){
		$this->load->library('fcm');

		$partnerTokenFCM=	"ePjs8NljQMOVoFATpl9RPp:APA91bHDbBNxnNHAlYbRYqMj6tFo5kZjIKcxMI0E51Dq3UDWtnc9X9O2bixNSYRgnnNdsU9LTWIMrLVXpy1qb9oyhNkYV47bcic9nUNE9v53c6jrHF60akJRn4LM4LO-pggTL1aOhQmn";
		$titleMsg		=	"Your withdrawal has been transferred";
		$body			=	"Details Withdrawal\n";
		$body			.=	"Date Request : 04 Oct 2024 22:27\n";
		$body			.=	"Message : tes\n";
		$body			.=	"Total Amount : IDR 10,711,000";
		$additionalArray=	array(
			"activity"	=>	"Withdrawal",
			"idPrimary"	=>	"1863"
		);

		var_dump($this->fcm->sendPushNotification($partnerTokenFCM, $titleMsg, $body, $additionalArray, true));
		echo "ok";
		die();
	}
	
	private function sendNewMailNotif($idMailbox, $mailTitle, $dateReservation, $sourceName){
		
		$dataPlayerId	=	$this->MainOperation->getDataPlayerIdOneSignal("NOTIFMAIL");
		
		if($dataPlayerId){
			$arrPlayerId	=	$dataPlayerId['arrOSUserId'];
			$arrIdUserAdmin	=	$dataPlayerId['arrIdUserAdmin'];
			$title			=	'New mail from '.$sourceName;
			$message		=	'Title : '.$mailTitle.'. Reservation date : '.$dateReservation;
			$arrData		=	array(
									"type"		=>	"mailbox",
									"idMailbox"	=>	$idMailbox
								);
			$arrHeading		=	array(
									"en" => $title
								);
			$arrContent		=	array(
									"en" => $message
								);
			$this->MainOperation->insertAdminMessage(1, $arrIdUserAdmin, $title, $message, $arrData);
			if(PRODUCTION_URL) sendOneSignalMessage($arrPlayerId, $arrData, $arrHeading, $arrContent);
		}
		
		return true;
		
	}
	
	public function autoRejectDayOffRequestYesterday(){

		$this->load->library('fcm');
		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		$dateYesterday		=	date('Y-m-d', strtotime("-1 days"));
		$dataDayOffRequest	=	$this->ModelCron->getDataDayOffRequestYesterday($dateYesterday);
		
		if(!$dataDayOffRequest){
			echo "No day off request for : ".$dateYesterday;
			die();
		}
		
		foreach($dataDayOffRequest as $keyDayOffRequest){
			
			$idDayoffRequest	=	$keyDayOffRequest->IDDAYOFFREQUEST;
			$arrUpdateRequest	=	array(
										"STATUS"			=>	-1,
										"DATETIMEAPPROVAL"	=>	date('Y-m-d H:i:s'),
										"USERAPPROVAL"		=>	'Auto System'
									);
			$procUpdateRquest	=	$this->MainOperation->updateData("t_dayoffrequest", $arrUpdateRequest, "IDDAYOFFREQUEST", $idDayoffRequest);			
			$dayOffDetails		=	$this->ModelCron->getDetailDayOffRequest($idDayoffRequest);
			
			if($dayOffDetails){
				
				$dateDayOff		=	DateTime::createFromFormat('Y-m-d', $dayOffDetails['DATEDAYOFF']);
				$dataDriver		=	$this->MainOperation->getDataDriver($dayOffDetails['IDDRIVER']);
				$dataMessageType=	$this->MainOperation->getDataMessageType(5);
				$dateDayOffStr	=	$dateDayOff->format('d M Y');
				$driverTokenFCM	=	$dataDriver['TOKENFCM'];
				$activityMessage=	$dataMessageType['ACTIVITY'];
				$title			=	"Day off request has been rejected [Auto System]";
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
					if($driverTokenFCM != "") $this->fcm->sendPushNotification($driverTokenFCM, $title, $body, $additionalArray);
				}
				
			}
			
			echo $idDayoffRequest." - OK<br/>";
		
		}
			
		die();

	}
	
	public function autoCreateDayOffFreelanceDriver(){

		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		$dateNext7Days		=	date('Y-m-d', strtotime("+7 days"));
		$dataDriverFreelance=	$this->ModelCron->getDataDriverFreelance();
		
		if(!$dataDriverFreelance){
			echo "No freelance driver found";
			die();
		}
		
		foreach($dataDriverFreelance as $keyDriverFreelance){
			
			$idDriverFreelance	=	$keyDriverFreelance->IDDRIVER;
			$arrInsertDayOff	=	array(
										"IDDRIVER"		=>	$idDriverFreelance,
										"DATEDAYOFF"	=>	$dateNext7Days,
										"REASON"		=>	'Default Off',
										"DATETIMEINPUT"	=>	date('Y-m-d H:i:s')
									);
			$procInsertDayOff	=	$this->MainOperation->addData("t_dayoff", $arrInsertDayOff);

			echo $idDriverFreelance." - OK<br/>";
		
		}
			
		die();

	}
	
	//UNUSED
	public function autoConfirmDriverLoanTransaction(){

		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		$dateTime			=	new DateTime();
		$dateTime->modify('-'.MAX_TIME_CONFIRM_LOAN_TRANS.' hours');
		$maxDateTime		=	$dateTime->format("Y-m-d H:i:s");
		$dataAutoConfirm	=	$this->ModelCron->getDataAutoConfirmDriverLoanTransaction($maxDateTime);
		
		if(!$dataAutoConfirm){
			echo "No data loan transaction for max date time : ".$maxDateTime;
			die();
		}
		
		foreach($dataAutoConfirm as $keyAutoConfirm){
			
			$idLoanDriverRequest=	$keyAutoConfirm->IDLOANDRIVERREQUEST;
			$arrUpdateRequest	=	array(
										"STATUS"			=>	2,
										"DATETIMECONFIRM"	=>	date('Y-m-d H:i:s'),
										"USERCONFIRM"		=>	'Auto System'
									);
			$procUpdateRquest	=	$this->MainOperation->updateData("t_loandriverrequest", $arrUpdateRequest, "IDLOANDRIVERREQUEST", $idLoanDriverRequest);
			
			if($procUpdateRquest['status']){
				$detailLoanRequest	=	$this->ModelCron->getDetailLoanRequest($idLoanDriverRequest);
				$statusLoanCapital	=	$detailLoanRequest['STATUSLOANCAPITAL'];
				$strLoanType		=	$statusLoanCapital == 1 ? "Loan (".$detailLoanRequest['LOANTYPE'].")" : "Prepaid Capital";
				$arrInsertRecord	=	array(
											"IDDRIVER"		=>	$detailLoanRequest['IDDRIVER'],
											"IDLOANTYPE"	=>	$detailLoanRequest['IDLOANTYPE'],
											"TYPE"			=>	'D',
											"DESCRIPTION"	=>	"Fund for ".$strLoanType." (".$detailLoanRequest['NOTES']."). Transferred to ".$detailLoanRequest['BANKNAME']." - ".$detailLoanRequest['ACCOUNTNUMBER']." - ".$detailLoanRequest['ACCOUNTHOLDERNAME'].". Input by : ".$detailLoanRequest['USERUPDATE'],
											"AMOUNT"		=>	$detailLoanRequest['AMOUNT'],
											"DATETIMEINPUT"	=>	date('Y-m-d H:i:s'),
											"USERINPUT"		=>	$detailLoanRequest['USERUPDATE']
										);
				$this->MainOperation->addData("t_loandriverrecord", $arrInsertRecord);
			}
			
			echo $idLoanDriverRequest." - OK<br/>";
		
		}
			
		die();

	}
	
	public function sendMailReviewCustomer(){
		
		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		echo "Start send mail review customer - ".date("d M Y H:i:s")."<br/>";
		$dataMailReview	=	$this->ModelCron->getDataCronMailReview(10);
		
		if($dataMailReview){
			foreach($dataMailReview as $keyMailReview){
				$idReservationMailRating	=	$keyMailReview->IDRESERVATIONMAILRATING;
				$idUnique					=	$keyMailReview->IDUNIQUE;
				$customerName				=	$keyMailReview->CUSTOMERNAME;
				$sourceName					=	$keyMailReview->SOURCENAME;
				$bookingCode				=	$keyMailReview->BOOKINGCODE;
				$urlReview					=	$keyMailReview->URLREVIEW;
				$customerEmail				=	$keyMailReview->CUSTOMEREMAIL;
				$arrReviewContent			=	array(
													"idUnique"		=>	$idUnique,
													"customerName"	=>	$customerName,
													"sourceName"	=>	$sourceName,
													"bookingCode"	=>	$bookingCode,
													"urlReview"		=>	$urlReview
												);
				$emailContent				=	$this->load->view('mail/bookingReview', $arrReviewContent, true);
				$mail 						=	new PHPMailer(true);

				try {
					$mail->isSMTP();
					$mail->Host			= MAIL_HOST;
					$mail->SMTPAuth		= true;
					$mail->Username		= MAIL_USERNAME;
					$mail->Password		= MAIL_PASSWORD;
					$mail->SMTPSecure	= PHPMailer::ENCRYPTION_SMTPS;
					$mail->Port			= MAIL_SMTPPORT;

					$mail->setFrom(MAIL_FROMADDRESS, MAIL_NAME);
					$mail->addAddress($customerEmail, $customerName);
					$mail->addReplyTo(MAIL_FROMADDRESS, MAIL_NAME);

					$mail->Subject	=	$customerName.", a special thanks from Bali SUN Tours!";
					$mail->Body   	=	$emailContent;
					$mail->isHTML(true);
					$mail->send();
					
					$this->MainOperation->updateData('t_reservationmailreview', ['DATETIMESEND' => date('Y-m-d H:i:s'), 'STATUSSEND' => 1], 'IDRESERVATIONMAILRATING', $idReservationMailRating);
				} catch (Exception $e) {
				}
				
				echo "Done proc :: ".$bookingCode."<br/>";
			}
		}
		
		echo "End send mail review customer - ".date("d M Y H:i:s");
		
	}
	
	public function cronScanCustomerContact(){
		$this->load->model('MainOperation');
		$this->load->model('ModelCronTest');
		$this->load->model('ModelCron');
		
		$arrDataCountryCode			=	$this->MainOperation->getDataCountryCode();
		$arrDataNameTitle			=	$this->MainOperation->getDataNameTitle();
		$dataReservationNoContact	=	$this->ModelCronTest->getDataReservationNoContact();
		
		if($dataReservationNoContact){
			foreach($dataReservationNoContact as $keyReservationNoContact){
				$idReservation	=	$keyReservationNoContact->IDORDER;
				$customerName	=	trim($keyReservationNoContact->NAMATAMU);
				$idNameTitle	=	$this->cleanUpFullName($customerName, $arrDataNameTitle, false);
				$customerName	=	$this->cleanUpFullName($customerName, $arrDataNameTitle);
				$customerContact=	preg_replace('/[^0-9]/', '', $keyReservationNoContact->CONTACT);
				$customerEmail	=	sanitize_email($keyReservationNoContact->EMAIL);
				$customerEmail	=	$customerEmail == "-" ? "" : $customerEmail;
				$isNumberExist	=	true;
				
				echo $idReservation." | ".$customerName."-".$idNameTitle."-".$customerName." | ".$customerContact."-".$customerEmail."<br/>";
				
				if(strlen($customerContact) > 8){
					$idCountry		=	$this->getIdCountryPhoneNumber($customerContact, $arrDataCountryCode);
					$phoneNumberBase=	$this->getCustomerPhoneNumberBase($customerContact, $idCountry, $arrDataCountryCode);
					$isNumberExist	=	$this->ModelCron->isNumberExist($phoneNumberBase, $idCountry);

					if($isNumberExist){
						$idContact	=	$isNumberExist['IDCONTACT'];
						$fullName	=	$this->cleanUpFullName($isNumberExist['NAMEFULL'], $arrDataNameTitle);
						
						if($customerName != $fullName){
							$arrInsertAliasContact	=	[
								"IDCONTACT"		=>	$idContact,
								"NAMEFULLALIAS"	=>	$customerName
							];
							$this->MainOperation->addData(APP_WHATSAPP_DATABASE_NAME.'.t_contactnamealias', $arrInsertAliasContact);
							echo $customerName." (".$customerContact.") - Contact exist, added name alias<br/>";
						} else {
							echo $customerName." (".$customerContact.") - Contact exist<br/>";
						}
						
						$this->MainOperation->updateData(APP_OLD_DATABASE_NAME.'.t_orderolah', ["IDCONTACT" => $idContact], "IDORDER", $idReservation);
					} else {
						$arrInsertContact	=	[
							"IDCOUNTRY"			=>	$idCountry,
							"IDNAMETITLE"		=>	$idNameTitle,
							"NAMEFULL"			=>	$customerName,
							"PHONENUMBER"		=>	$customerContact,
							"PHONENUMBERBASE"	=>	$phoneNumberBase,
							"EMAILS"			=>	$customerEmail,
							"DATETIMEINSERT"	=>	date('Y-m-d H:i:s')
						];
						
						if($customerName != ""){
							$procInsertContact	=	$this->MainOperation->addData(APP_WHATSAPP_DATABASE_NAME.'.t_contact', $arrInsertContact);
							
							if($procInsertContact['status']){
								$idContactNew	=	$procInsertContact['insertID'];
								$this->MainOperation->updateData(APP_OLD_DATABASE_NAME.'.t_orderolah', ["IDCONTACT" => $idContactNew], "IDORDER", $idReservation);
							} else {
								$this->updateInvalidIdContactReservation($idReservation);
							}
						} else {
							$this->updateInvalidIdContactReservation($idReservation);
						}
						echo $customerName." (".$customerContact.") - New number added<br/>";
					}
				} else {
					$this->updateInvalidIdContactReservation($idReservation);
					echo $customerName." (".$customerContact.") - Invalid number<br/>";
				}
			}
		}

		if(!$idReservation) echo "Done";
		if($idReservation) return true;
	}
	
	private function cleanUpFullName($fullName, $arrDataNameTitle, $cleanUpName = true){
		$fullName			=	strtolower($fullName);
		$cleanFullName		=	$fullName;
		$idNameTitleReturn	=	0;
		$whileProcess		=	true;
		
		if($arrDataNameTitle){
			foreach($arrDataNameTitle as $keyDataNameTitle){
				$idNameTitle		=	$keyDataNameTitle->IDNAMETITLE;
				$keyWordSearch		=	$keyDataNameTitle->KEYWORDSEARCH;
				$keyWordSearchArr	=	explode(",", $keyWordSearch);
				
				foreach($keyWordSearchArr as $keyword){
					$keyword	=	strtolower(trim($keyword));
					$lenKeyword	=	strlen($keyword);
					
					if(substr($fullName, 0, $lenKeyword) == $keyword) {
						if($whileProcess == true){
							$cleanFullName		=	substr($fullName, $lenKeyword, strlen($fullName) - $lenKeyword);
							$cleanFullName		=	preg_replace('/^[^a-zA-Z]+/', '', $cleanFullName);
							$whileProcess		=	false;
							$idNameTitleReturn	=	$idNameTitle;
							break;
						}
					}
				}

				if(!$whileProcess) break;
			}
		}
		
		return $cleanUpName ? ucwords(trim($cleanFullName)) : $idNameTitleReturn;
	}
	
	private function getIdCountryPhoneNumber($phoneNumber, $arrDataCountryCode){
		$phoneNumber	=	preg_replace('/[^0-9]/', '', $phoneNumber);
		$whileProcess	=	true;
		$idCountryReturn=	0;
		
		if($arrDataCountryCode){
			foreach($arrDataCountryCode as $keyDataCountryCode){
				$idCountry		=	$keyDataCountryCode->IDCOUNTRY;
				$countryCode	=	$keyDataCountryCode->COUNTRYPHONECODE;
				$countryCodeLen	=	strlen($countryCode);
				
				if(substr($phoneNumber, 0, $countryCodeLen) == $countryCode){
					if($whileProcess == true){
						$idCountryReturn=	$idCountry;
						$whileProcess	=	false;
						break;
					}
				}
				
				if(!$whileProcess) break;
			}
		}
		
		return $idCountryReturn;
	}
	
	private function getCustomerPhoneNumberBase($phoneNumber, $idCountry, $arrDataCountryCode){
		$phoneNumber		=	preg_replace('/[^0-9]/', '', $phoneNumber);
		$filterIdCountry	=	array_filter($arrDataCountryCode, fn($item) => $item->IDCOUNTRY == $idCountry);
		$countryPhoneCode	=	reset($filterIdCountry)->COUNTRYPHONECODE ?? '';
		$phoneNumberBase	=	substr($phoneNumber, strlen($countryPhoneCode)) * 1;
		
		return $phoneNumberBase;
	}
	
	private function updateInvalidIdContactReservation($idReservation){
		$this->MainOperation->updateData(APP_OLD_DATABASE_NAME.'.t_orderolah', ["IDCONTACT" => -1], "IDORDER", $idReservation);
	}
	
}