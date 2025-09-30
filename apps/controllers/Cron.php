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

class Cron extends CI_controller {
	
    private $connection;
	private $arrReservationType;
	private $arraySource;
	private $arrayDefaultCurrency;
	private $dom;
	
	public function __construct(){
		parent::__construct();
		$functionName	=	$this->uri->segment(2);

		if($functionName == 'readKlikBCAPayroll' || $functionName == 'readMailbox' || $functionName == 'readMailboxCS' || $functionName == 'readMailboxBookingCodeParam' || $functionName == 'readKlookBadReviewMail' || $functionName == 'readMailReconfirmation' || $functionName == 'readMailReconfirmationDevel'){
			libxml_use_internal_errors(false);
			$this->dom						=	new DOMDocument();
			$this->dom->strictErrorChecking	=	false;
			$this->dom->recover				=	true;
			$this->dom->preserveWhiteSpace	=	false;
		}
		
		if($functionName == 'readKlikBCAPayroll' || $functionName == 'readMailbox' || $functionName == 'readMailboxBookingCodeParam' || $functionName == 'readKlookBadReviewMail'){
			$username			= MAILBOX_USERNAME;
			$password			= MAILBOX_PASSWORD;
			$server				= new Server('imap.gmail.com/ssl/NoValidate-Cert');
			$this->connection	= $server->authenticate($username, $password);
			$this->getDataEmailSenderSource();
		} else if($functionName == 'readMailboxCS' || $functionName == 'readMailboxBookingCodeParamCS') {
			$username			= MAIL_USERNAME;
			$password			= MAIL_PASSWORD;
			$server				= new Server('imap.gmail.com/ssl/NoValidate-Cert');
			$this->connection	= $server->authenticate($username, $password);
			$this->getDataEmailSenderSource();
		}
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
							"DATETIMEUPDATE"	=>	date('Y-m-d H:i:s'),
							"USERCONFIRM"		=>	'Auto System'
						);
						$procUpdateRquest		=	$this->MainOperation->updateData("t_loandriverrequest", $arrUpdateRequest, "IDLOANDRIVERREQUEST", $idLoanDriverRequest);
						$isLoanRecordExist		=	$this->ModelCron->isLoanRecordExist($idLoanDriverRequest);
						$detailLoanRequest		=	$this->ModelCron->getDetailLoanRequest($idLoanDriverRequest);
						$idDriver				=	$detailLoanRequest['IDDRIVER'];
						$idLoanType				=	$detailLoanRequest['IDLOANTYPE'];
						$statusLoanCapital		=	$detailLoanRequest['STATUSLOANCAPITAL'];
						$notes					=	$detailLoanRequest['NOTES'];
						$bankName				=	$detailLoanRequest['BANKNAME'];
						$accountNumber			=	$detailLoanRequest['ACCOUNTNUMBER'];
						$accountHolderName		=	$detailLoanRequest['ACCOUNTHOLDERNAME'];
						$userUpdate				=	$detailLoanRequest['USERUPDATE'];
						$loanNominalPrincipal	=	$detailLoanRequest['LOANNOMINALPRINCIPAL'];
						$loanNominalInterest	=	$detailLoanRequest['LOANNOMINALINTEREST'];
						$loanNominalTotal		=	$statusLoanCapital == 1 ? $detailLoanRequest['LOANNOMINALTOTAL'] : $detailLoanRequest['AMOUNT'];
						$loanDurationMonth		=	$detailLoanRequest['LOANDURATIONMONTH'];
						$loanInterestPerAnnum	=	$detailLoanRequest['LOANINTERESTPERANNUM'];
						$loanInstallmentPerMonth=	$detailLoanRequest['LOANINSTALLMENTPERMONTH'];
						$loanDateTimeInput		=	$detailLoanRequest['DATETIMEINPUT'];
						$strLoanType			=	$statusLoanCapital == 1 ? "Loan (".$detailLoanRequest['LOANTYPE'].")" : "Prepaid Capital";
						$additionalDescription	=	$statusLoanCapital == 1 ? 
													"Nominal Principal Rp. ".number_format($loanNominalPrincipal, 0, ',', '.')." + Nominal Interest Rp. ".number_format($loanNominalInterest, 0, ',', '.')." (".$loanInterestPerAnnum."% p.a - ".$loanDurationMonth." Months) \n".
													"Total Loan Rp. ".number_format($loanNominalTotal, 0, ',', '.')." | Monthly installment Rp. ".number_format($loanInstallmentPerMonth, 0, ',', '.').". \n"
													: "";
						
						if($procUpdateRquest['status'] && !$isLoanRecordExist){
							$arrInsertRecord	=	array(
								"IDDRIVER"				=>	$idDriver,
								"IDLOANTYPE"			=>	$idLoanType,
								"IDLOANDRIVERREQUEST"	=>	$idLoanDriverRequest,
								"TYPE"					=>	'D',
								"DESCRIPTION"			=>	"Fund for ".$strLoanType." (".$notes."). \n".$additionalDescription.
															"Transferred to ".$bankName." - ".$accountNumber." - ".$accountHolderName.". \n".
															"Input by : ".$userUpdate,
								"AMOUNT"				=>	$loanNominalTotal,
								"DATETIMEINPUT"			=>	date('Y-m-d H:i:s'),
								"USERINPUT"				=>	$userUpdate
							);
							$this->MainOperation->addData("t_loandriverrecord", $arrInsertRecord);
						}
						
						if($statusLoanCapital == 1) $this->MainOperation->updateData('t_loandriverrecap', ['LOANDATEDISBURSEMENT' => date('Y-m-d'), 'LOANSTATUS' => 1], ['IDDRIVER' => $idDriver, 'IDLOANTYPE' => $idLoanType, 'LOANSTATUS' => 0, 'LOANNOMINALPRINCIPAL' => $loanNominalPrincipal]);
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
						
						$arrUpdateWithdrawal			=	array("STATUSWITHDRAWAL"	=>	2);
						$procUpdateWithdrawal			=	$this->MainOperation->updateData("t_withdrawalrecap", $arrUpdateWithdrawal, "IDWITHDRAWALRECAP", $idWithdrawal);
						$detailWithdrawal				=	$this->ModelCron->getDetailWithdrawalRequest($idWithdrawal);
						$idDriver						=	$detailWithdrawal['IDDRIVER'];
						$idVendor						=	$detailWithdrawal['IDVENDOR'];
						$totalLoanCarInstallment		=	$detailWithdrawal['TOTALLOANCARINSTALLMENT'];
						$totalLoanPersonalInstallment	=	$detailWithdrawal['TOTALLOANPERSONALINSTALLMENT'];
						$withdrawMonthYear				=	$detailWithdrawal['WITHDRAWMONTHYEAR'];
						$totalCharityNominal			=	$detailWithdrawal['TOTALCHARITY'];
						$totalAdditionalIncomeNominal	=	$detailWithdrawal['TOTALADDITIONALINCOME'];
						$dateWithdrawalRequest			=	$detailWithdrawal['DATETIMEREQUEST'];
						$dateWithdrawalRequestDB		=	$detailWithdrawal['DATETIMEREQUESTDB'];
						$messageWithdrawalRequest		=	$detailWithdrawal['MESSAGE'];
						$totalAmountWithdrawal			=	number_format($detailWithdrawal['TOTALWITHDRAWAL'], 0, '.', ',');
						
						$dataPartner		=	$idPartnerType == 1 ? $this->MainOperation->getDataVendor($idPartner) : $this->MainOperation->getDataDriver($idPartner);
						$dataMessageType	=	$this->MainOperation->getDataMessageType(8);
						$partnerTokenFCM	=	$dataPartner['TOKENFCM'];
						$activityMessage	=	$dataMessageType['ACTIVITY'];
						
						if($totalLoanCarInstallment > 0) $this->calculateDriverLoanRecap($idDriver, 1, $totalLoanCarInstallment);						
						if($totalLoanPersonalInstallment > 0) $this->calculateDriverLoanRecap($idDriver, 2, $totalLoanPersonalInstallment);
						
						if($totalCharityNominal > 0){
							$charityName	=	$dataPartner['NAME'];
							$arrInsertCharityData	=	[
								"IDDRIVER"			=>	$idDriver,
								"IDVENDOR"			=>	$idVendor,
								"IDWITHDRAWALRECAP"	=>	$idWithdrawal,
								"CONTRIBUTORTYPE"	=>	1,
								"NAME"				=>	$charityName,
								"DESCRIPTION"		=>	"Charity through withdrawal disbursement",
								"NOMINAL"			=>	$totalCharityNominal,
								"DATETIME"			=>	date('Y-m-d H:i:s'),
								"INPUTTYPE"			=>	1,
								"INPUTBYNAME"		=>	"Auto System",
								"INPUTDATETIME"		=>	date('Y-m-d H:i:s'),
								"STATUS"			=>	0
							];
							$this->MainOperation->addData("t_charity", $arrInsertCharityData);
						}
						
						if($totalAdditionalIncomeNominal > 0){
							$arrInsertAdditionalIncome	=	[
								"IDDRIVER"				=>	$idDriver,
								"IDWITHDRAWALRECAP"		=>	$idDriver,
								"DESCRIPTION"			=>	"Additional income payment within withdrawal",
								"IMAGERECEIPT"			=>	"noimage.jpg",
								"INCOMENOMINAL"			=>	$totalAdditionalIncomeNominal,
								"INCOMEDATE"			=>	$dateWithdrawalRequestDB,
								"INPUTTYPE"				=>	3,
								"INPUTUSER"				=>	'Auto System',
								"INPUTDATETIME"			=>	date('Y-m-d H:i:s'),
								"APPROVALUSER"			=>	'Auto System',
								"APPROVALDATETIME"		=>	date('Y-m-d H:i:s'),
								"APPROVALSTATUS"		=>	1
							];
							$this->MainOperation->addData("t_additionalincome", $arrInsertAdditionalIncome);
							
							$arrData							=	["idDriver" => $idDriver, "userAdminName" => "Auto System"];
							$base64JsonData						=	base64_encode(json_encode($arrData));
							$urlAPICalculateRatingPointDriver	=	BASE_URL."financeDriver/additionalIncome/apiCalculateRatingPointDriver/".$base64JsonData;
							
							try {
								json_decode(trim(curl_get_file_contents($urlAPICalculateRatingPointDriver)));
							} catch(Exception $e) {
							}
						}

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
												'newWithdrawalNotifStatus'	=>  2,
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
	
	private function calculateDriverLoanRecap($idDriver, $idLoanType, $installmentNominal){
		$this->load->model('MainOperation');
		$this->load->model('FinanceDriver/ModelLoanPrepaidCapital');
		
		while($installmentNominal > 0){
			$dataLoanRecap	=	$this->ModelLoanPrepaidCapital->getDataRecapSaldoLoanDriver($idDriver, $idLoanType);
			
			if($dataLoanRecap){
				foreach($dataLoanRecap as $keyLoanRecap){
					$idLoanDriverRecap			=	$keyLoanRecap->IDLOANDRIVERRECAP;
					$loanNominalSaldo			=	$keyLoanRecap->LOANNOMINALSALDO;
					$loanInstallmentPerMonth	=	$keyLoanRecap->LOANINSTALLMENTPERMONTH;
					$loanInstallmentLastPeriod	=	$keyLoanRecap->LOANINSTALLMENTLASTPERIOD;
					$reductionAmount			=	$loanInstallmentPerMonth <= $loanNominalSaldo ? $loanInstallmentPerMonth : $loanNominalSaldo;
					$reductionAmount			=	$reductionAmount <= $installmentNominal ? $reductionAmount : $installmentNominal;
					$loanNominalSaldoFinal		=	$loanNominalSaldo - $reductionAmount;
					$installmentPeriodDT		=	DateTime::createFromFormat('Y-m', $loanInstallmentLastPeriod);
					$installmentPeriodDT		=	$installmentPeriodDT->modify('+1 month');
					$installmentPeriod			=	$installmentPeriodDT->format('Y-m');
					
					$arrUpdateRecap	=	[
						"LOANNOMINALSALDO"			=>	$loanNominalSaldoFinal,
						"LOANINSTALLMENTLASTPERIOD"	=>	$installmentPeriod
					];
					
					if($loanNominalSaldoFinal <= 0) $arrUpdateRecap['LOANSTATUS']	=	2;
					if($installmentNominal > 0) $this->MainOperation->updateData('t_loandriverrecap', $arrUpdateRecap, 'IDLOANDRIVERRECAP', $idLoanDriverRecap);
					
					$arrInsertInstallmentHistory=	[
						"IDLOANDRIVERRECAP"		=>	$idLoanDriverRecap,
						"DESCRIPTION"			=>	'Installment record through withdrawal deduction',
						"INSTALLMENTPERIOD"		=>	$installmentPeriod,
						"TRANSACTIONDATE"		=>	date('Y-m-d'),
						"NOMINALINSTALLMENT"	=>	$reductionAmount,
						"NOMINALSALDO"			=>	$loanNominalSaldoFinal,
						"INPUTUSER"				=>	'Auto System',
						"INPUTDATETIME"			=>	date('Y-m-d H:i:s')
					];
					if($installmentNominal > 0) $this->MainOperation->addData('t_loandriverinstallmenthistory', $arrInsertInstallmentHistory);
					$installmentNominal		-=	$reductionAmount;
				}
			} else {
				break;
				return true;
			}
		}
		
		return true;
	}
	
	private function getDataEmailSenderSource(){
		
		$this->load->model('MainOperation');
		$dataReservationType=	$this->MainOperation->getDataReservationType();
		$dataSenderEmail	=	$this->MainOperation->getDataSenderEmailSource();
		$returnArr			=	$arrReservationType	=	$defaultCurrencyArray	=	array();

		if($dataSenderEmail){
			foreach($dataSenderEmail as $keySenderEmail){
				$arrSenderEmail	=	array();
				
				if($keySenderEmail->EMAILSENDER1 != '') $arrSenderEmail[]	=	$keySenderEmail->EMAILSENDER1;
				if($keySenderEmail->EMAILSENDER2 != '') $arrSenderEmail[]	=	$keySenderEmail->EMAILSENDER2;
				if($keySenderEmail->EMAILSENDER3 != '') $arrSenderEmail[]	=	$keySenderEmail->EMAILSENDER3;
				
				if(count($arrSenderEmail) > 0) $returnArr[$keySenderEmail->IDSOURCE]	=	$arrSenderEmail;
				$defaultCurrencyArray[$keySenderEmail->IDSOURCE]	=	$keySenderEmail->DEFAULTCURRENCY;
			}
		}
		
		if($dataReservationType){
			foreach($dataReservationType as $keyReservationType){
				$titleKeywords			=	explode(',', $keyReservationType->TITLEKEYWORDS);
				$titleKeywordsExclution	=	explode(',', $keyReservationType->TITLEKEYWORDSEXCLUTION);
				$emailAddress			=	explode(',', $keyReservationType->EMAILADDRESS);
				$arrReservationType[]	=	[
					'idReservationType'		=>	$keyReservationType->IDRESERVATIONTYPE,
					'reservationType'		=>	$keyReservationType->RESERVATIONTYPE,
					'titleKeywords'			=>	$titleKeywords,
					'titleKeywordsExclution'=>	$titleKeywordsExclution,
					'emailAddress'			=>	$emailAddress,
					'isTransportIncluded'	=>	$keyReservationType->ISINCLUDETRANSPORT
				];
			}
		}

		$this->arrReservationType	=	$arrReservationType;
		$this->arraySource			=	$returnArr;
		$this->defaultCurrency		=	$defaultCurrencyArray;
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
	
	public function readMailboxBookingCodeParamCS($bookingCode){
		
		$htmlFileName	=	$this->readMailboxCS($bookingCode);
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
	
	public function readMailboxCS($bookingCodeParam = false){

		$this->load->model('MainOperation');
		$arraySource	=	$this->arraySource;
		
		foreach($arraySource as $idSource=>$arrSenderEmail){
			foreach($arrSenderEmail as $senderEmail){
				$search		= new SearchExpression();
				$search->addCondition(new From($senderEmail));
				if(!$bookingCodeParam) $search->addCondition(new Unseen());
				if($bookingCodeParam) $search->addCondition(new Body($bookingCodeParam));

				$inbox		=	$this->connection->getMailbox('INBOX');
				$messages	=	$inbox->getMessages($search);

				foreach ($messages as $message) {
					$subject			=	$message->getSubject();
					$arrToMailAddress	=	$this->getArrMailAddress($message->getTo());
					$dateTimeMail		=	$message->getDate();
					$dateTimeMail		=	DateTime::createFromFormat(DateTimeInterface::ATOM, $dateTimeMail->format(DateTimeInterface::ATOM));
					$dateTimeMail->setTimeZone(new DateTimeZone('GMT+7'));
					$dateTimeMail		=	$dateTimeMail->format('Y-m-d H:i:s');
					$htmlFileName		=	$this->saveHTMLFile($message, 'CS_');
					$sourceName			=	"-";
					
					if(substr($subject, 0, 27) == 'Important: Request for New ' || substr($subject, 0, 32) == 'Fwd: Important: Request for New ' || substr($subject, 0, 60) == 'Important: We Have Automatically Confirmed one of Your Tours' || substr($subject, 0, 39) == 'Important: Request for New Tour Booking'){
						$arrDataProcess	=	$this->processShoreExcursionsMail($message);
						$sourceName		=	"Shore";
					}
					
					if($sourceName != "" && $sourceName != "-"){
						$defaultCurrency	=	$this->defaultCurrency[$idSource];
						$pickUpLocation		=	$arrDataProcess['PICKUPLOCATION'];
						$hotelName			=	$arrDataProcess['HOTELNAME'];
						$idAreaPickUp		=	0;
						$areaKeywordCheck	=	"";
						
						if(isset($pickUpLocation) && $pickUpLocation != ""){
							$areaKeywordCheck	=	$pickUpLocation;
						} else if (isset($hotelName) && $hotelName != ""){
							$areaKeywordCheck	=	$hotelName;
						}
						
						if($areaKeywordCheck != ""){
							$idAreaPickUp		=	$this->getIdAreaPickUpByKeyword($areaKeywordCheck);
						}
						
						$arrDataProcess['IDRESERVATIONTYPE']	=	$this->detectReservationType($arrToMailAddress, $arrDataProcess['RESERVATIONTITLE']);
						$arrDataProcess['IDSOURCE']				=	$idSource;
						$arrDataProcess['IDAREA']				=	$idAreaPickUp;
						$arrDataProcess['MAILSUBJECT']			=	$sourceName == "Shore" ? $subject." - ".$arrDataProcess['BOOKINGCODE'] : $subject;
						$arrDataProcess['INCOMEAMOUNTCURRENCY']	=	$defaultCurrency;
						$arrDataProcess['HTMLFILENAME']			=	$htmlFileName;
						$arrDataProcess['DATETIMEMAIL']			=	date('Y-m-d H:i:s');
						$arrDataProcess['DATETIMEMAILREAD']		=	date('Y-m-d H:i:s');

						if($bookingCodeParam != false){
							echo json_encode($arrDataProcess);
							die();
						}

						$procInsert	=	$this->MainOperation->addData('t_mailbox', $arrDataProcess);
						if($procInsert['status'] && $bookingCodeParam == false){
							$idMailbox			=	$procInsert['insertID'];
							$strReservationDate	=	date('d M Y', strtotime($arrDataProcess['RESERVATIONDATE']));
							$this->sendNewMailNotif($idMailbox, $arrDataProcess['MAILSUBJECT'], $strReservationDate, $sourceName);
							$this->updateWebappStatisticTags($sourceName, $subject);
						}
						
						$message->markAsSeen();
						if(!$bookingCodeParam) echo $htmlFileName."<br/>";
						if($bookingCodeParam) return $htmlFileName;
					} else {
						echo $senderEmail."-".$subject." - Non Comfirmed Order<br/>";
					}
				}
			}
		}
		echo "End - ".date("d M Y H:i");
	}
	
	public function readMailbox($bookingCodeParam = false){

		$this->load->model('MainOperation');
		$arraySource	=	$this->arraySource;
		
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
					$arrToMailAddress	=	$this->getArrMailAddress($message->getTo());
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
					
					if(substr($subject, 0, 21) == 'Klook Order Confirmed' || substr($subject, 0, 26) == 'Fwd: Klook Order Confirmed'){
						$arrDataProcess	=	$this->processKlookMail($subject, $message);
						$sourceName		=	"Klook";
						
						// if(strpos($subject, 'Private Car Rental with Driver') !== false || strpos($subject, 'Bali Private Car Charter') !== false){
							// $attachments=	$message->getAttachments();
							// foreach ($attachments as $attachment) {
								// $content		=	$attachment->getDecodedContent();
								// $filename		=	$attachment->getFilename();
								// $fileInfo		=	pathinfo($filename);
								// $fileExtension	=	$fileInfo['extension'];
								
								// if($fileExtension == 'pdf'){
									// $parser					= new \Smalot\PdfParser\Parser();
									// $pdf					=	$parser->parseContent($content);
									// $text					=	$pdf->getText();
									// $textPDF				=	json_encode(htmlspecialchars($text));
									// $posCoverage			=	strpos($textPDF, 'Coverage');
									// $posSurcharge			=	strpos($textPDF, 'Surcharge table');
									// if($posCoverage && $posSurcharge && $posCoverage !== -1 && $posSurcharge !== -1){
										// $strSlicer				=	$posCoverage < $posSurcharge ? 'Coverage' : 'Surcharge table';
										// $explodeTextPDF			=	explode($strSlicer, $textPDF);
										// $textPDFData			=	$strSlicer.$explodeTextPDF[1];
									// } else {
										// $textPDFData			=	$textPDF;
									// }
									
									// $dataCoverageSurcharge	=	$this->getCoverageSurchargeData($textPDFData);
										// var_dump($dataCoverageSurcharge);
									
									// if(isset($dataCoverageSurcharge['httpCode']) && $dataCoverageSurcharge['httpCode'] == 200){
										// $responseCoverageSurcharge	=	json_decode(json_decode($dataCoverageSurcharge['response']));
										// $statusCoverageSurcharge	=	isset($responseCoverageSurcharge->status) ?? false;
										// $dataCoverageSurcharge		=	isset($responseCoverageSurcharge->data) ? $responseCoverageSurcharge->data : [];
										// var_dump($dataCoverageSurcharge);
										
										// if($statusCoverageSurcharge){
											// $coverage	=	isset($dataCoverageSurcharge->coverage_area) ? $dataCoverageSurcharge->coverage_area : '';
											// $surcharge	=	isset($dataCoverageSurcharge->surcharge) ? $dataCoverageSurcharge->surcharge : '';
										// var_dump($coverage);
										// var_dump($surcharge);

											// $arrDataProcess['COVERAGEAREA']	=	$coverage;
											// $arrDataProcess['SURCHARGELIST']=	$surcharge;
										// }
									// }
								// }
							// }
						// }
					}
					
					if(substr($subject, 0, 15) == 'New Booking for' || substr($subject, 0, 20) == 'Fwd: New Booking for'){
						$arrDataProcess	=	$this->processViatorMail($message);
						$sourceName		=	"Viator";
					}
					
					if(substr($subject, 0, 10) == 'Booking - ' || substr($subject, 0, 15) == 'Fwd: Booking - '){
						$arrDataProcess	=	$this->processGetYourGuideMail($message);
						$sourceName		=	"GetYourGuide";
					}
					
					if(substr($subject, 0, 17) == 'eBooking Order - ' || substr($subject, 0, 29) == 'Airport Transfer Paid Booking' || substr($subject, 0, 33) == '[Bali Sun Tours] New booking for ' || substr($subject, 0, 38) == 'Fwd: [Bali Sun Tours] New booking for ' || substr($subject, 0, 32) == 'Fwd: [Bali Sun Tours]: New order'){
						$arrDataProcess	=	$this->processEBookingMail($subject, $message);
						$sourceName		=	"eBooking";
					}
					
					if(substr($subject, 0, 36) == 'Paid Booking Admin Confirmation BST-' || substr($subject, 0, 41) == 'Fwd: Paid Booking Admin Confirmation BST-'){
						$arrDataProcess	=	$this->processEccommerceBooking($message);
						$sourceName		=	"eBooking";
					}
					
					if(substr($subject, 0, 30) == '[Bali SUN Tours] from KKdayMkp' && strpos($subject, 'cancel') === false && strpos($subject, 'Cancel') === false){
						$arrDataProcess	=	$this->processKKDayMail($message);
						$sourceName		=	"KKdayMkp";
					}
					
					if(substr($subject, 0, 20) == 'Booking Confirmed - ' || substr($subject, 0, 25) == 'Fwd: Booking Confirmed'){
						$arrDataProcess	=	$this->processPelagoMail($message);
						$sourceName		=	"Pelago";
					}
					
					if(substr($subject, 0, 27) == 'Important: Request for New ' || substr($subject, 0, 32) == 'Fwd: Important: Request for New ' || substr($subject, 0, 31) == 'Urgent: Second Request for New ' || substr($subject, 0, 30) == 'Urgent: Third Request for New ' || substr($subject, 0, 60) == 'Important: We Have Automatically Confirmed one of Your Tours' || substr($subject, 0, 65) == 'Fwd: Important: We Have Automatically Confirmed one of Your Tours'){
						$arrDataProcess	=	$this->processShoreExcursionsMail($message);
						$sourceName		=	"Shore";
					}
					
					if($sourceName != "" && $sourceName != "-"){
						$defaultCurrency	=	$this->defaultCurrency[$idSource];
						$pickUpLocation		=	$arrDataProcess['PICKUPLOCATION'];
						$hotelName			=	$arrDataProcess['HOTELNAME'];
						$idAreaPickUp		=	0;
						$areaKeywordCheck	=	"";
						
						if(isset($pickUpLocation) && $pickUpLocation != ""){
							$areaKeywordCheck	=	$pickUpLocation;
						} else if (isset($hotelName) && $hotelName != ""){
							$areaKeywordCheck	=	$hotelName;
						}
						
						if($areaKeywordCheck != ""){
							$idAreaPickUp		=	$this->getIdAreaPickUpByKeyword($areaKeywordCheck);
						}
						
						$arrDataProcess['IDRESERVATIONTYPE']	=	$this->detectReservationType($arrToMailAddress, $arrDataProcess['RESERVATIONTITLE']);
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
						if($procInsert['status'] && $bookingCodeParam == false){
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
		
		if($bookingCodeParam == false) $this->calculateScheduleDriverMonitor();
		echo "End - ".date("d M Y H:i");
		
	}
	
	private function getCoverageSurchargeData($textPDFData){
		$timeStamp		=	time();
        $dataJSON       =   json_encode(['timestamp'=>$timeStamp]);		
        $hmacSignature  =   hash_hmac('sha256', $dataJSON, ROKET_CS_AI_AGENT_PRIVATE_KEY);
		$dataScan		=	$this->submitPDFContentCoverageSurcharge($textPDFData, $hmacSignature, $timeStamp);
		
		return $dataScan;
	}
	
	private function submitPDFContentCoverageSurcharge($textPDFData, $hmacSignature, $timeStamp){
		$response	=	"";
		$httpCode	=	500;

		try {
			$curl	=	curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL				=>	ROKET_CS_AI_AGENT_SURCHARGE_COVERAGE_URL,
				CURLOPT_RETURNTRANSFER	=>	true,
				CURLOPT_ENCODING		=>	'',
				CURLOPT_MAXREDIRS		=>	10,
				CURLOPT_TIMEOUT			=>	0,
				CURLOPT_FOLLOWLOCATION	=>	true,
				CURLOPT_HTTP_VERSION	=>	CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST	=>	'GET',
				CURLOPT_HTTPHEADER		=>	array(
					'Accept: application/json',
					'Content-Type: application/x-www-form-urlencoded',
					'BST-Public-Key: '.ROKET_CS_AI_AGENT_PUBLIC_KEY,
					'BST-Signature: '.$hmacSignature,
					'BST-Timestamp: '.$timeStamp
				),
				CURLOPT_POSTFIELDS      =>  'text='.$textPDFData
			));

			$response	=	curl_exec($curl);
			$httpCode	=	curl_getinfo($curl, CURLINFO_HTTP_CODE);
		} catch (Exception $e) {
		}
		
		return [
			'httpCode'	=>	$httpCode,
			'response'	=>	json_encode($response)
		];
	}
	
	private function getArrMailAddress($arrObjectMailAddress){
		$arrToMailAddress	=	[];
		foreach ($arrObjectMailAddress as $objectMailAddress) {
			$arrToMailAddress[]	=	$objectMailAddress->getAddress();
        }
		
		return $arrToMailAddress;
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
	
	private function saveHTMLFile($message, $prefix=''){
		
		$messageNumber	=	$message->getNumber();
		$messageHTML	=	$message->getBodyHTML();
		$fileName		=	$prefix.$messageNumber.".html";
		
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
											$reservationDate	=	!is_bool($reservationDate) ? $reservationDate->format('Y-m-d') : '0000-00-00';
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
		$pickUpLink		=	$productLink = '';
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
				
				$dataMessageRaw	=	$dataMessage;
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
						case "Pick Up Location Link:"	:	
						case "Hotel & Address:"			:	$pickUpLink			=	filter_var($dataMessageRaw, FILTER_SANITIZE_URL);
															if(!filter_var($pickUpLink, FILTER_VALIDATE_URL)) {
																$pickUpLink		=	'';
															}
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
			
			if(isset($resultSplit[(21-$minusIdxNum)]) && $resultSplit[(21-$minusIdxNum)] == "Departure location:"){
				$pickUpLocation	=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(22-$minusIdxNum)]);
			} else if(isset($resultSplit[(21-$minusIdxNum)]) && $resultSplit[(21-$minusIdxNum)] == "Pick Up From::"){
				$hotelName		=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(24-$minusIdxNum)]);
			} else if(isset($resultSplit[(25-$minusIdxNum)]) && $resultSplit[(25-$minusIdxNum)] == "Location (Name and Address):") {
				$pickUpLocation	=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(26-$minusIdxNum)]);
			} else if(isset($resultSplit[(23-$minusIdxNum)]) && $resultSplit[(23-$minusIdxNum)] == "Location (Name and Address):") {
				$pickUpLocation	=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(24-$minusIdxNum)]);
			} else if(isset($resultSplit[(29-$minusIdxNum)]) && $resultSplit[(29-$minusIdxNum)] == "Location (Name and Address):") {
				$hotelName		=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(30-$minusIdxNum)]);
			} else if(isset($resultSplit[(34-$minusIdxNum)]) && $resultSplit[(33-$minusIdxNum)] == "Location (Name and Address):") {
				$pickUpLocation	=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(34-$minusIdxNum)]);
				$hotelName		=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(34-$minusIdxNum)]);
			} else {
				if(isset($resultSplit[(22-$minusIdxNum)])){
					$hotelName		=	preg_replace("/[^a-zA-Z0-9 ]+/", "", $resultSplit[(22-$minusIdxNum)]);
					if(strlen($hotelName) < 10) $hotelName	=	"";
				}
			}
		
			if($productLink == ''){
				$productLink	=	$resultSplit[19];
				$productLink	=	filter_var($productLink, FILTER_SANITIZE_URL);
				if(filter_var($productLink, FILTER_VALIDATE_URL) == false) {
					$productLink	=	'';
				}
			}
			
			for($i=(0); $i<=$totalArray; $i++){
				if(isset($resultSplit[$i]) && $resultSplit[$i] != ""){
					$textArray	=	preg_replace('/[^\00-\255]+/u', '', $resultSplit[$i]);
					
					if($textArray == "Activity URL:" && $productLink == ''){
						$productLink	=	$resultSplit[$i+1];
						$productLink	=	filter_var($productLink, FILTER_SANITIZE_URL);
						if(filter_var($productLink, FILTER_VALIDATE_URL) == false) {
							$productLink	=	'';
						}
					}

					if(($textArray == "Pick Up Location Link:" || $textArray == "Hotel & Address:") && $pickUpLink == ''){
						$pickUpLink	=	$resultSplit[$i+1];
						$pickUpLink	=	filter_var($pickUpLink, FILTER_SANITIZE_URL);
						if(filter_var($pickUpLink, FILTER_VALIDATE_URL) == false) {
							$pickUpLink	=	'';
						}
					}

					if(substr($textArray, 0, 21) == "Booking reference ID:" && ($bookingCode == "" || strlen($bookingCode) > 9)){
						$bookingCodeValue	=	$resultSplit[$i];
						$bookingCodeExplode	=	explode(":", $bookingCodeValue);
						$bookingCode		=	$bookingCodeExplode[1];
					}
				
					if($textArray == "Lead person email:" && ($customerEmail == "" || checkMailPattern($customerEmail) == false)){
						$customerEmail		=	$resultSplit[$i+1];
					}
				
					if($textArray == "Lead person mobile:" && ($customerContact == "" || strlen($customerContact) < 6)){
						$customerContact	=	$resultSplit[$i+1];
					}
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

				if($textArray == "Pick Up Time:" || $textArray == "Pick-up time:"){
					$reservationTime=	$resultSplit[$i+1];
					$reservationTime=	preg_replace("/[^0-9:.]/", "", $reservationTime);
					if(strlen($reservationTime) <= 2){
						$reservationTime=	str_pad($reservationTime, 2, "0", STR_PAD_LEFT).":00";
					}
					$reservationTime	=	str_pad(str_replace(".", ":", $reservationTime), 5, "0", STR_PAD_LEFT).":00";
				}

				if($textArray == "Pick-up date & time::"){
					$reservationDateTime	=	$resultSplit[$i+1];
					$reservationDateTime	=	preg_replace("/[^0-9:.\- ]/", "", $reservationDateTime);
					$reservationDateTimeDT	=	false;
								
					try {
						if(strlen($reservationDateTime) == 16) $reservationDateTimeDT	=	DateTime::createFromFormat('Y-m-d H:i', $reservationDateTime);
						if(strlen($reservationDateTime) == 19) $reservationDateTimeDT	=	DateTime::createFromFormat('Y-m-d H:i:s', $reservationDateTime);
						
						if($reservationDateTimeDT != false){
							$reservationDate	=	$reservationDateTimeDT->format('Y-m-d');
							$reservationTime	=	$reservationDateTimeDT->format('H:i:s');
						}
					} catch(Exception $e) {
					}
				}

				if($textArray == "Number of Passengers:"){
					$splitPassengers	=	explode(" ", $resultSplit[$i+1]);
					$iPassengers		=	0;
					
					foreach($splitPassengers as $keyPassangers){
						if($keyPassangers == "Adults" || $keyPassangers == "adults" || $keyPassangers == "Adult"){
							$numberOfAdult	=	isset($splitPassengers[$iPassengers - 1]) ? $splitPassengers[$iPassengers - 1] : 0;
						}
						
						if($keyPassangers == "Childs" || $keyPassangers == "Child"){
							$numberOfChild	=	isset($splitPassengers[$iPassengers - 1]) ? $splitPassengers[$iPassengers - 1] : 0;
						}
						
						if($keyPassangers == "Infants" || $keyPassangers == "Infant"){
							$numberOfInfant	=	isset($splitPassengers[$iPassengers - 1]) ? $splitPassengers[$iPassengers - 1] : 0;
						}
						$iPassengers++;
					}
				}
			
				if(substr($resultSplit[0], 0, 35) == "Bali Private Car Rental with Driver" && $textArray == "Contact Method and Details (WhatsApp/LINE/WeChat):"){
					$customerContact	=	$resultSplit[$i+1];
				}
				
				if($textArray == "Pick Up Location Link:" || $textArray == "Hotel & Address:"){
					$pickUpLink		=	$resultSplit[$i+1];
					$pickUpLink		=	filter_var($pickUpLink, FILTER_SANITIZE_URL);
					if(filter_var($pickUpLink, FILTER_VALIDATE_URL) == false) {
						$pickUpLink	=	'';
					}
				}
				
				if($textArray == "From: :" && $pickUpLocation == ""){
					$pickUpLocation	=	$resultSplit[$i+1];
				}
				
				if($textArray == "To: :" && $dropOffLocation == ""){
					$dropOffLocation=	$resultSplit[$i+1];
				}
				
				if($textArray == "Extra Services:" && ($remark == "" || $remark == "-")){
					$remark	=	$resultSplit[$i+1];
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
					if(strpos(strtolower($reservationTitle), "·") !== false){
						$dashPointExplode	=	explode("·", $reservationTitle);
					} else {
						$dashPointExplode	=	explode("-", $reservationTitle);
					}
					
					foreach($dashPointExplode as $dashPointExplodeStr){
						if(strpos(strtolower($dashPointExplodeStr), "group of") !== false){
							$rangeExplode	=	explode("-", $dashPointExplodeStr);
							$numberOfAdult	=	preg_replace('/[^0-9]/', '', end($rangeExplode));
							$numberOfAdult	=	is_null($numberOfAdult) || !isset($numberOfAdult) || $numberOfAdult == '' ? 1 : $numberOfAdult * 1;
						}
					}
				}
			}

			if($customerName == "" || substr($resultSplit[$i-2], 0, 12) === "Participant1"){
				$customerName	=	$resultSplit[$i-1];
			}
		}

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
			"BOOKINGCODE"			=>	$bookingCode,
			"URLDETAILPRODUCT"		=>	$productLink,
			"URLPICKUPLOCATION"		=>	$pickUpLink
		);
		return $returnArr;

	}
	
	private function processViatorMail($message){
		
		$messageHTML	=	$message->getBodyHTML();
		@$this->dom->loadHTML($messageHTML);

		$tourPlan		=	$customerEmail = '-';
		$remark			=	$customerName =	$bookingCode =	$reservationTitle = $hotelName = $pickUpLocation = $dropOffLocation = $customerContact = $source =	$coverage =	'';
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
															$reservationDate	=	!is_bool($reservationDate) ? $reservationDate->format('Y-m-d') : '0000-00-00';
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
						case "Tour Grade Description"	:	$lineText			=	trim(strip_tags(html_entity_decode(preg_replace('/\t+/', '', $keyData))));
															$remark				.=	$lineText."\n\n";
															if(strpos($lineText, 'coverage service area') !== false){
																$datalineText	=	explode(':', $lineText);
																$coverage		=	trim(strip_tags($datalineText[2]));
															}
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
					"COVERAGEAREA"			=>	$coverage,
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
								$reservationDate	=	!is_bool($reservationDate) ? $reservationDate->format('Y-m-d') : '0000-00-00';
								$fullReservationTime=	$explodeFullDate[1];
								$expReservationTime	=	explode("(", $fullReservationTime);
								$reservationTime	=	trim($expReservationTime[0]);
								break;
				  case 19	:	$incomeAmount		=	$child->nodeValue;
								$incomeAmount		=	preg_replace("/[^0-9.]/", "", $incomeAmount);
								$incomeAmount		=	number_format($incomeAmount * 70 / 100, 0, ',', '');
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
	
	private function processEBookingMail($subject, $message){
		
		$messageHTML	=	$message->getBodyHTML();
		@$this->dom->loadHTML($messageHTML);

		$tourPlan		=	$customerEmail = '-';
		$remark			=	$customerName =	$reservationTitle = $hotelName = $pickUpLocation = $dropOffLocation = $customerContact = $source =	'';
		$numberOfAdult	=	$numberOfChild = $numberOfInfant = $incomeAmount = 0;
		$reservationDate=	"0000-00-00";
		$reservationTime=	"00:00";
		
		if(substr($subject, 0, 29) == 'Airport Transfer Paid Booking'){
			$table			= 	$this->dom->getElementsByTagName('table')->item(1);	
			$data			=	$table->nodeValue;
			$dataSplit		=	preg_split('/\r\n|\r|\n/', $data);
			$durationOfDay	=	1;
			$isPickUpKey	=	$isDropOffKey	=	$isAdditionalService	=	false;
			
			foreach($dataSplit as $keyData){
				$plainString	=	trim(preg_replace('/\s+/', ' ', $keyData));
				
				if($plainString != ""){
					$explodeString	=	explode(": ", $plainString);
					$keyString		=	$explodeString[0];
					$valueString	=	isset($explodeString[1]) && $explodeString[1] != '' ? $explodeString[1] : '';
					
					if($isPickUpKey == true){
						$pickUpLocation	=	$keyString;
						$isPickUpKey	=	false;
					} else if($isDropOffKey == true) {
						$dropOffLocation=	$keyString;
						$isDropOffKey	=	false;
					} else if($isAdditionalService == true) {
						$remark				.=	$keyString;
						$isAdditionalService=	false;
					} else {
						switch($keyString){
							case "Booking ID"			:	$bookingCode	=	$valueString; break;
							case "Amount"				:	$valueString	=	str_replace(",00", "", $valueString);
															$incomeAmount	=	preg_replace('/\D+/', '', $valueString);
															break;
							case "Customer name"		:	$customerName	=	$valueString; break;
							case "Customer email"		:	$customerEmail	=	$valueString; break;
							case "Customer phone"		:	
							case "Customer phone:"		:	
															$customerContact=	preg_replace('/\D+/', '', $valueString); break;
							case "Transfer type"		:	$reservationTitle	=	$valueString; break;
							case "Transfer date time"	:	try {
																$dateTimeReservation=	DateTime::createFromFormat("D d M Y H:i", $valueString);
																
																if ($dateTimeReservation) {
																	$reservationDate	=	$dateTimeReservation->format("Y-m-d");
																	$reservationTime	=	$dateTimeReservation->format("H:i");
																}
															} catch (Exception $e) {}
															break;
							case "Total passanger"		:	$numberOfAdult		=	preg_replace('/\D+/', '', $valueString); break;
							case "Origin"				:	
							case "Origin:"				:	
															$isPickUpKey		=	true; break;
							case "Destination"			:	
							case "Destination:"			:	
															$isDropOffKey		=	true; break;
							case "Additional service"	:	
							case "Additional service:"	:	
															$isAdditionalService=	true; break;
							case "Additional request"	:	
							case "Additional request:"	:	
															$remark				.=	$valueString; break;
						}
					}
				}
			}
		} else {
			$durationOfDay	=	1;
			$table			= 	$this->dom->getElementsByTagName('table')->item(1);	
			$data			=	$table->nodeValue;
			$dataSplit		=	preg_split('/\r\n|\r|\n/', $data);

			foreach($dataSplit as $keyData){
				$plainString	=	trim(preg_replace('/\s+/', ' ', $keyData));
				if($plainString != ""){
					$explodeString	=	explode(": ", $plainString);
					$keyString		=	$explodeString[0];
					$valueString	=	isset($explodeString[1]) && $explodeString[1] != '' ? $explodeString[1] : '';
					
					switch($keyString){
						case "Booking ID"		:	$bookingCode	=	$valueString; break;
						case "Amount"			:	$valueString	=	str_replace(",00", "", $valueString);
														$incomeAmount	=	preg_replace('/\D+/', '', $valueString);
														break;
						case "Traveler name"	:	
						case "Customer name"	:	$customerName	=	$valueString; break;
						case "Traveler email"	:	
						case "Customer email"	:	$customerEmail	=	$valueString; break;
						case "Traveler phone"	:	
						case "Traveler phone:"	:	
						case "Customer phone"	:	
						case "Customer phone:"	:	$customerContact=	preg_replace('/\D+/', '', $valueString); break;
					}
				}
			}
			
			$tablePkg		= 	$this->dom->getElementsByTagName('table')->item(2);	
			$dataPkg		=	$tablePkg->nodeValue;
			$dataSplitPkg	=	preg_split('/\r\n|\r|\n/', $dataPkg);

			foreach($dataSplitPkg as $keyDataPkg){
				$plainStringPkg	=	trim(preg_replace('/\s+/', ' ', $keyDataPkg));
				if($plainStringPkg != ""){
					$explodeStringPkg	=	explode(": ", $plainStringPkg);
					$keyStringPkg		=	$explodeStringPkg[0];
					$valueStringPkg		=	isset($explodeStringPkg[1]) && $explodeStringPkg[1] != '' ? $explodeStringPkg[1] : '';
					
					switch($keyStringPkg){
						case "Package"					:	$reservationTitle	=	$valueStringPkg;
															if (preg_match('/(\d+)\s*Pax/', $valueStringPkg, $matches)) $numberOfAdult = $matches[1];
															break;
						case "Date of Tour-Activity"	:	try {
																$dateReservation=	DateTime::createFromFormat("D d M Y", $valueStringPkg);
																
																if ($dateReservation) $reservationDate	=	$dateReservation->format("Y-m-d");
															} catch (Exception $e) {}
															break;
						case "Pickup location"			:	$pickUpLocation	=	$valueStringPkg; break;
						case "Pickup time"				:	try {
																$timeReservation=	DateTime::createFromFormat("h:i A", $valueStringPkg);
																
																if ($timeReservation) $reservationTime	=	$timeReservation->format("H:i");
															} catch (Exception $e) {}
															break;
						case "Planned place to visit"	:	$tourPlan	=	$valueStringPkg; break;
						case "Note"						:	$remark		=	$valueStringPkg; break;
					}
				}
			}
		}

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
	
	//pickup location masih berupa link, ambil textnya dan linknya jadikan pickup url
	private function processEccommerceBooking($message){
		
		$messageHTML	=	$message->getBodyHTML();
		@$this->dom->loadHTML($messageHTML);

		$tourPlan		=	$customerEmail = '-';
		$remark			=	$customerName =	$reservationTitle = $hotelName = $pickUpLocation = $dropOffLocation = $customerContact = $source = $pickUpLink = '';
		$numberOfAdult	=	$numberOfChild = $numberOfInfant = $incomeAmount = 0;
		$reservationDate=	"0000-00-00";
		$reservationTime=	"00:00";
		$durationOfDay	=	1;
		
		$table		= 	$this->dom->getElementsByTagName('table')->item(1);
		$data		=	$table->nodeValue;
		$dataSplit	=	preg_split('/\r\n|\r|\n/', $data);

		foreach($dataSplit as $keyData){
			$dataRaw	=	trim($keyData);

			if($dataRaw != "" && strlen($dataRaw) > 2){
				$dataExplode	=	explode(": ", $dataRaw);
				$dataKey		=	isset($dataExplode[0]) ? $dataExplode[0]: "";
				$dataValue		=	isset($dataExplode[1]) ? $dataExplode[1]: "";
				
				switch($dataKey){
					case "Booking ID"		:	$bookingCode	=	trim($dataValue); break;
					case "Traveler name"	:	$customerName	=	trim($dataValue); break;
					case "Traveler email"	:	$customerEmail	=	trim($dataValue); break;
					case "Traveler phone"	:	$customerContact=	trim($dataValue); break;
					case "Amount"			:	$incomeAmount	=	str_replace(["Rp ", ".", ",00"], ["", "", ""], $dataValue); break;
				}
			}
		}
		
		$tablePckg				= 	$this->dom->getElementsByTagName('table')->item(2);
		$dataPckg				=	$tablePckg->nodeValue;
		$dataSplitPckg			=	preg_split('/\r\n|\r|\n/', $dataPckg);
		$isValuePickupLocation	=	false;
		$idxDataSplitPckg		=	0;

		foreach($dataSplitPckg as $keyDataPckg){
			$dataRawPckg=	trim($keyDataPckg);

			if($dataRawPckg != "" && strlen($dataRawPckg) > 2){
				$dataExplodePckg=	explode(": ", $dataRawPckg);
				$dataKeyPckg	=	isset($dataExplodePckg[0]) ? $dataExplodePckg[0] : "";
				$dataValuePckg	=	isset($dataExplodePckg[1]) ? $dataExplodePckg[1] : "";
				
				if($isValuePickupLocation) {
					$pickUpLocation			=	trim($dataRawPckg);
					$isValuePickupLocation	=	false;
				}
				
				switch($dataKeyPckg){
					case "Package"					:	$reservationTitle	=	trim($dataValuePckg); break;
					case "Pickup location"			:	
					case "Pickup location:"			:	$isValuePickupLocation	=	true;
														break;
					case "Planned place to visit"	:	$tourPlan			=	trim($dataValuePckg); break;
					case "Note"						:	$remark				=	trim($dataValuePckg); break;
					case "Held on"					:	$reservationDateF	=	DateTime::createFromFormat('D d M Y', $dataValuePckg);
														$reservationDate	=	$reservationDateF->format("Y-m-d");
														break;
					case "Selected pickup time"		:	
					case "Pickup time"				:	$reservationTimeF	=	DateTime::createFromFormat('h:i A', $dataValuePckg);
														$reservationTime	=	$reservationTimeF->format("H:i:s");
														break;
					case "Unit"						:	$explodeDataValuePckg	=	explode(" x ", $dataValuePckg);
														$numberOfAdult			=	preg_replace("/[^0-9]/", "", $explodeDataValuePckg[0]) * 1;
														break;
				}
			}
			
			$idxDataSplitPckg++;
		}

		$allLinkNodes	=	$this->dom->getElementsByTagName('a');
		foreach($allLinkNodes as $linkNode) {
			if($pickUpLocation != ""){
				if (strpos($linkNode->nodeValue, $pickUpLocation) !== false) { 
					$pickUpLink	=	$linkNode->getAttribute("href");
				}
			}
		}

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
			"BOOKINGCODE"			=>	$bookingCode,
			"URLPICKUPLOCATION"		=>	$pickUpLink
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
								$reservationDate		=	!is_bool($reservationDateStartF) ? $reservationDateStartF->format('Y-m-d') : '0000-00-00';
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
				  case 9	:	$numberOfAdult		=	str_replace(" x Person", "", trim($keyData)); break;
				  case 11	:	try {
									$reservationDate	=	trim($keyData);
									$reservationDate	=	DateTime::createFromFormat('M d, Y', $reservationDate);
									$reservationDate	=	!is_bool($reservationDate) ? $reservationDate->format('Y-m-d') : "0000-00-00";
								} catch(Exception $e) {
								}
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
		$remark			=	$customerName =	$reservationTitle = $hotelName = $pickUpLocation = $dropOffLocation = $customerContact = $source =	$bookingCode	=	'';
		$numberOfAdult	=	$numberOfChild = $numberOfInfant = $incomeAmount = 0;
		$reservationDate=	"0000-00-00";
		$reservationTime=	"00:00";
		
		$paragraphs		= 	$this->dom->getElementsByTagName('p');
		$iParagraph		=	0;
		$lastContext	=	'';
		
		foreach($paragraphs as $node){
		   foreach($node->childNodes as $child) {
			  switch($lastContext){
				 case 'Additional Notes:'		:	$remark				=	trim($child->nodeValue); break;
				 case 'Tour Name:'				:	$reservationTitle	=	trim($child->nodeValue); break;
				 case 'Date:'					:	$reservationDate	=	trim($child->nodeValue);
													$reservationDate	=	DateTime::createFromFormat('F d, Y', $reservationDate);
													$reservationDate	=	!is_bool($reservationDate) ? $reservationDate->format('Y-m-d') : '0000-00-00';
													break;
				 case 'Tour Meeting Time:'		:	$reservationTime	=	trim($child->nodeValue);
													$reservationTime	=	DateTime::createFromFormat('h:i A', $reservationTime);
													$reservationTime	=	!is_bool($reservationTime) ? $reservationTime->format('h:i:s') : '00:00';
													break;
				  case 'Number of Individuals:'	:	$numberOfAdult		=	trim($child->nodeValue); break;
				  case 'Port:'					:	$pickUpLocation		.=	trim($child->nodeValue); break;
				  case 'Cruise Line and Ship:'	:	$pickUpLocation		.=	" - ".trim($child->nodeValue); break;
				  case 'Customer:'				:	$customerName		=	trim($child->nodeValue); break;
				  case 'Product Cost:'			:	$incomeAmount		=	trim($child->nodeValue); break;
				  case 'Order #:'				:	$bookingCode		=	trim($child->nodeValue); break;
			  }
			  $lastContext	=	trim($child->nodeValue);
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
	
	private function detectReservationType($arrToMailAddress, $reservationTitle){
		$isTransportIncluded=	1;
		$arrReservationType	=	$this->arrReservationType;

		if(strpos(strtolower($reservationTitle), 'no transfer') !== false) $isTransportIncluded	=	0;
		
		foreach($arrReservationType as $keyReservationType){
			$idReservationType			=	$keyReservationType['idReservationType'];
			$reservationType			=	$keyReservationType['reservationType'];
			$titleKeywords				=	$keyReservationType['titleKeywords'];
			$titleKeywordsExclution		=	$keyReservationType['titleKeywordsExclution'];
			$emailAddress				=	$keyReservationType['emailAddress'];
			$isTransportIncludedCheck	=	$keyReservationType['isTransportIncluded'];
			$isExclutionKeyword			=	false;
			
			if($isTransportIncludedCheck == $isTransportIncluded){
				foreach($titleKeywordsExclution as $keywordExclution){
					if(stripos($reservationTitle, strtolower($keywordExclution)) !== false) {
						$isExclutionKeyword	=	true;
					}
				}

				foreach($arrToMailAddress as $toMailAddress){
					if(in_array($toMailAddress, $emailAddress)) return $idReservationType;
				}

				foreach($titleKeywords as $keyword){
					if(stripos($reservationTitle, strtolower($keyword)) !== false && !$isExclutionKeyword) return $idReservationType;
				}
			}
		}
		
		return 0;
	}
	
	public function bulkNotifMailbox(){
		$this->load->model('MainOperation');
		$this->sendNewMailNotif(22, "Klook Order Confirmed - Airport transfer 3(DO NOT EDIT) - 2022-01-06 14:00:00 - Sudjatmiko Tjokro - WJC445962", "2022-01-06", "Viator");
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
				$idSource					=	$keyMailReview->IDSOURCE;
				$customerName				=	$keyMailReview->CUSTOMERNAME;
				$sourceName					=	$keyMailReview->SOURCENAME;
				$bookingCode				=	$keyMailReview->BOOKINGCODE;
				$urlReview					=	$keyMailReview->URLREVIEW;
				$customerEmail				=	$keyMailReview->CUSTOMEREMAIL;
				$sourceNameStr				=	$idSource == 2 ? "TripAdvisor" : $sourceName;
				$reviewLogo					=	$idSource == 1 ? "20240513-image-6-klook.jpeg" : "20230909-image-6.jpeg";
				$arrReviewContent			=	array(
													"idUnique"		=>	$idUnique,
													"idSource"		=>	$idSource,
													"reviewLogo"	=>	$reviewLogo,
													"customerName"	=>	$customerName,
													"sourceName"	=>	$sourceName,
													"sourceNameStr"	=>	$sourceNameStr,
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
	
	//OK
	public function createScheduleReservationConfirmation(){

		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		$settingDayReconfirm	=	$this->MainOperation->getValueSystemSettingVariable(12);
		$dateDaysToCome			=	date('Y-m-d', strtotime("+".($settingDayReconfirm + 1)." days"));
		$dateYesterday			=	date('Y-m-d', strtotime("-1 days"));
		$hoursToCome			=	$settingDayReconfirm * 24;
		$dataReconfirmation		=	$this->ModelCron->getDataReservationReconfirmation($dateDaysToCome, $dateYesterday);
		$dataTemplateWhatsapp	=	$this->MainOperation->getDataSystemSettingAppWhatsapp(1);
		$idChatTemplate			=	0;
		
		if($dataTemplateWhatsapp){
			$dataSetting		=	json_decode($dataTemplateWhatsapp['DATASETTING']);
			$idChatTemplateDB	=	$dataSetting->IDCHATTEMPLATE;
			$idChatTemplate		=	isset($idChatTemplateDB) && $idChatTemplateDB != '' && !is_null($idChatTemplateDB) ? $idChatTemplateDB : 0;
		}
		
		if($dataReconfirmation){
			foreach($dataReconfirmation as $keyReconfirmation){
				$idReservation		=	$keyReconfirmation->IDRESERVATION;
				$idContact			=	$keyReconfirmation->IDCONTACT;
				$customerPhoneNumber=	$keyReconfirmation->PHONENUMBER;
				$customerContact	=	$keyReconfirmation->CUSTOMERCONTACT;
				$customerEmail		=	$keyReconfirmation->CUSTOMEREMAIL;
				$additionalInfoList	=	$keyReconfirmation->ADDITIONALINFOLIST;
				$isValidContact		=	$idContact > 0 ? true : false;
				$customerPhoneNumber=	$isValidContact && CRON_RECONFIRMATION_WHATSAPP ? $customerPhoneNumber : false;
				$contact			=	!filter_var($customerEmail, FILTER_VALIDATE_EMAIL) ? $customerPhoneNumber : $customerEmail;
				$platform			=	!filter_var($customerEmail, FILTER_VALIDATE_EMAIL) ? 'WHATSAPP' : 'EMAIL';
				
				if($contact){
					$strDTReservation	=	$keyReconfirmation->RESERVATIONDATESTART." ".$keyReconfirmation->RESERVATIONTIMESTART;
					$strDTNow			=	date('Y-m-d H:i:s');
					$dtReservation		=	new DateTime($strDTReservation);
					$difference			=	$dtReservation->diff(new DateTime($strDTNow));
					$differenceDays		=	$difference->days;
					$differenceHours	=	($differenceDays * 24) + $difference->h;
					$sendingMethod		=	$differenceHours <= 23 ? 2 : 1;
					$dateReservation	=	DateTime::createFromFormat('Y-m-d', $keyReconfirmation->RESERVATIONDATESTART);
					$dateReservation	=	$dateReservation->modify("-".$settingDayReconfirm." days");
					$dateSchedule		=	$dateReservation->format('Y-m-d');
					$dateTimeSchedule	=	$dateSchedule." ".$keyReconfirmation->RESERVATIONTIMESTART;
					
					if($sendingMethod == 2){
						$dateTimeSchedule	=	'0000-00-00 00:00:00';
					} else if($differenceHours < $hoursToCome) {
						$dateTimeSchedule	=	date('Y-m-d H:i').":00";
					}

					$additionalInfoList	=	$additionalInfoList == '' ? null : $additionalInfoList;
					$arrInsertData		=	array(
						'IDRESERVATION'		=>	$idReservation,
						'PLATFORM'			=>	$platform,
						'SENDINGMETHOD'		=>	$sendingMethod,
						'CONTACT'			=>	$contact,
						'ADDITIONALINFOLIST'=>	$additionalInfoList,
						'DATETIMESCHEDULE'	=>	$dateTimeSchedule
					);
					
					$procInsertReconfirmation	=	$this->MainOperation->addData('t_reservationreconfirmation', $arrInsertData);
					if($procInsertReconfirmation['status'] && $platform == 'WHATSAPP'){
						$idReservationReconfirmation=	$procInsertReconfirmation['insertID'];
						$arrInsertCronWhatsapp		=	[
							'IDRESERVATION'					=>	$idReservation,
							'IDRESERVATIONRECONFIRMATION'	=>	$idReservationReconfirmation,
							'IDCHATTEMPLATE'				=>	$idChatTemplate,
							'STATUS'						=>	0,
							'DATETIMESCHEDULE'				=>	$dateTimeSchedule
						];
						$this->MainOperation->addData(APP_WHATSAPP_DATABASE_NAME.'.t_chatcron', $arrInsertCronWhatsapp);
					}
					
					echo $idReservation."-".$platform."-".$contact."<br/>";
				}
			}
		}
		
		if(PRODUCTION_URL) $this->sendMailReconfirmation();
		die();

	}
	
	public function sendMailReconfirmation($idReservationReconfirmation = false){
		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		$idReservationReconfirmation=	$idReservationReconfirmation ? decodeStringKeyFunction(base64_decode($idReservationReconfirmation), DEFAULT_KEY_ENCRYPTION) : false;
		$cronStatus					=	$idReservationReconfirmation ? false : true;
		if($cronStatus) echo "Start send mail reconfirmation - ".date("d M Y H:i:s")."<br/>";
		$dataMailReconfirmation		=	$this->ModelCron->getDataMailReconfirmation(10, $idReservationReconfirmation);
		
		if($dataMailReconfirmation){
			foreach($dataMailReconfirmation as $keyMailReconfirmation){
				$idReservationReconfirmation=	$keyMailReconfirmation->IDRESERVATIONRECONFIRMATION;
				$bookingCode				=	$keyMailReconfirmation->BOOKINGCODE;
				$reservationTitle			=	$keyMailReconfirmation->RESERVATIONTITLE;
				$customerName				=	$keyMailReconfirmation->CUSTOMERNAME;
				$customerEmail				=	$keyMailReconfirmation->EMAILADDRESS;
				$reservationStatus			=	$keyMailReconfirmation->STATUS;
				
				if($reservationStatus == -1 || strpos(strtolower($reservationTitle), 'cancel') !== false){
					$arrUpdateReconfirmation	=	[
						"STATUS"			=> -1,
						"STATUSREADTHREAD"	=> 1
					];
					$this->MainOperation->updateData('t_reservationreconfirmation', $arrUpdateReconfirmation, 'IDRESERVATIONRECONFIRMATION', $idReservationReconfirmation);
					if($cronStatus) echo "Cancelation :: ".$bookingCode."<br/>";
					if(!$cronStatus) setResponseForbidden(
						array(
							"procCode"	=>	403,
							"msg"		=>	'Failed to send confirmation mail, this reservation has been cancelled.'
						)
					);
				} else {
					$mailSubject	=	"Bali SUN Tours - Reconfirm Reservation : ".$bookingCode;
					$emailContent	=	$this->previewMailReconfirmation($idReservationReconfirmation, false);
					$mail			=	new PHPMailer(true);

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
						$mail->addCustomHeader( 'In-Reply-To', '<' . MAIL_FROMADDRESS . '>' );
						
						$mail->Subject	=	$mailSubject;
						$mail->Body   	=	$emailContent;
						$mail->isHTML(true);
						$mail->send();
						
						$urlLogo		=	DOMAIN_HTTP_TYPE.":".BASE_URL_ASSETS."img/Logo_BST.png";
						$emailHTMLText	=	$this->previewMailReconfirmation($idReservationReconfirmation, false, $urlLogo);
						$fileName		=	date('Ymd').str_pad($idReservationReconfirmation, 6, "0", STR_PAD_LEFT).".html";
						file_put_contents(PATH_EMAIL_RECONFIRMATION_FILE.$fileName, $emailHTMLText);
						
						$arrUpdate		=	[
							'DATETIMESENT'		=>	date('Y-m-d H:i:s'),
							'MAILHTMLFILE'		=>	$fileName,
							'STATUS' 			=>	1,
							'STATUSREADTHREAD'	=>	1
						];
						
						if(!$cronStatus) {
							$arrUpdate['SENDINGMETHOD']		=	2;
							$arrUpdate['DATETIMESCHEDULE']	=	'0000-00-00 00:00:00';
						}
						
						$this->MainOperation->updateData('t_reservationreconfirmation', $arrUpdate, 'IDRESERVATIONRECONFIRMATION', $idReservationReconfirmation);
					} catch (Exception $e) {
					}
					
					if($cronStatus) echo "Mail Sent :: ".$bookingCode."<br/>";
					if(!$cronStatus) setResponseOk(
						array(
							"procCode"	=>	200,
							"msg"		=>	'Confirmation mail has been sent'
						)
					);
				}
			}
		}
		
		if($cronStatus) echo "End send mail reconfirmation - ".date("d M Y H:i:s");	
	}

	public function previewMailReconfirmation($idReservationReconfirmation, $preview=true, $urlLogo=''){
		$this->load->model('ModelCron');

		$detailReconfirmation	=	$this->ModelCron->getDetailMailReconfirmation($idReservationReconfirmation);
		$idUnique				=	$preview ? 'nodata' : base64_encode(encodeStringKeyFunction($idReservationReconfirmation, DEFAULT_KEY_ENCRYPTION));
		$idArea					=	$detailReconfirmation['IDAREA'];
		$bookingCode			=	$detailReconfirmation['BOOKINGCODE'];
		$customerName			=	$detailReconfirmation['CUSTOMERNAME'];
		$numberOfAdult			=	$detailReconfirmation['NUMBEROFADULT'];
		$numberOfChild			=	$detailReconfirmation['NUMBEROFCHILD'];
		$numberOfInfant			=	$detailReconfirmation['NUMBEROFINFANT'];
		$numberOfPax			=	($numberOfAdult > 0 ? $numberOfAdult." Adult" : "").($numberOfChild > 0 ? " - ".$numberOfChild." Child" : "").($numberOfInfant > 0 ? " - ".$numberOfInfant." Infant" : "");
		$reservationTitle		=	$detailReconfirmation['RESERVATIONTITLE'];
		$reservationDateStart	=	$detailReconfirmation['RESERVATIONDATESTART'];
		$reservationDateEnd		=	$detailReconfirmation['RESERVATIONDATEEND'];
		$reservationDate		=	$reservationDateStart == $reservationDateEnd ? $reservationDateStart : $reservationDateStart." - ".$reservationDateEnd;
		$pickUpActivityTime		=	substr($detailReconfirmation['RESERVATIONTIMESTART'], 0, 5);
		$timeDescriptionStr		=	$idArea == -1 ? "Activity Time" : "Pickup Time";
		$pickUpLocation			=	$detailReconfirmation['PICKUPLOCATION'];
		$hotelName				=	$detailReconfirmation['HOTELNAME'];
		$pickUpDetails			=	($pickUpLocation == "" || $pickUpLocation == "-" ? "" : $pickUpLocation).($hotelName == "" || $hotelName == "-" ? "" : "-".$hotelName);
		$pickUpDetails			=	$pickUpDetails == "" ? "-" : $pickUpDetails;
		$additionalInfoList		=	$detailReconfirmation['ADDITIONALINFOLIST'];
		$additionalInfoList		=	$additionalInfoList == "" || is_null($additionalInfoList) ? false : json_decode($additionalInfoList);
		$urlLogo				=	$urlLogo == '' ? DOMAIN_HTTP_TYPE.":".BASE_URL_ASSETSAPI."mailReconfirmation/20230909-image-11.png/".$idUnique : $urlLogo;
		$specialNotesArea		=	$specialNotesAreaReply	=	$specialNotesPickUpTime	=	$specialNotesPickUpTimeReply	=	"";
		
		if($idArea == 0){
			$specialNotesArea		=	$idArea == -1 ? "" : "- Pick up location / hotel<br/>";
			$specialNotesAreaReply	=	$idArea == -1 ? "" : "- Pick up location / hotel : [Write Here]\n";
		}

		if($pickUpActivityTime == '00:00'){
			$specialNotesPickUpTime		=	$idArea == -1 ? "" : "- Pick up time";
			$specialNotesPickUpTimeReply=	$idArea == -1 ? "" : "- Pick up time : [Write Here]";
		}
		
		$specialNotes				=	$specialNotesArea != '' || $specialNotesPickUpTime != '' ? "* Please provide the required data :<br/>".$specialNotesArea.$specialNotesPickUpTime : "-";
		$specialNotesReply			=	$specialNotesAreaReply != '' || $specialNotesPickUpTimeReply != '' ? "Required data information : \n\n".$specialNotesAreaReply.$specialNotesPickUpTimeReply : "";
		$mailSubject				=	"Bali SUN Tours - Reconfirm Reservation : ".$bookingCode;
		$arrReconfirmationContent	=	array(
											"bookingCode"		=>	$bookingCode,
											"customerName"		=>	$customerName,
											"numberOfPax"		=>	$numberOfPax,
											"reservationTitle"	=>	$reservationTitle,
											"reservationDate"	=>	$reservationDate,
											"timeDescriptionStr"=>	$timeDescriptionStr,
											"pickUpActivityTime"=>	$pickUpActivityTime,
											"pickUpDetails"		=>	$pickUpDetails,
											"specialNotes"		=>	$specialNotes,
											"additionalInfoList"=>	$additionalInfoList,
											"specialNotesReply"	=>	$specialNotesReply,
											"urlLogo"			=>	$urlLogo,
											"mailSubject"		=>	$mailSubject
										);
		$emailContent				=	$this->load->view('mail/reconfirmation', $arrReconfirmationContent, true);
		
		if(!$preview) return $emailContent;
		echo $emailContent;
		die();
	}
	
	public function readMailReconfirmation(){

		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		$server				= new Server('imap.gmail.com/ssl/NoValidate-Cert');
		$this->connection	= $server->authenticate(MAIL_USERNAME, MAIL_PASSWORD);
		$search				= new SearchExpression();
		$search->addCondition(new Unseen());
		$search->addCondition(new Subject('Bali SUN Tours - Reconfirm Reservation'));

		$inbox		=	$this->connection->getMailbox('INBOX');
		$messages	=	$inbox->getMessages($search);
		
		foreach ($messages as $message) {
			$subject			=	$message->getSubject();
			$headers			=	$message->getHeaders();
			$messageBody		=	$message->getBodyHtml();
			$isHtmlMessage		=	$messageBody == "" || is_null($messageBody) ? false : true;
			$messageBody		=	$messageBody == "" || is_null($messageBody) ? $message->getBodyText() : $messageBody;
			$messageDateTime	=	$message->getDate();
			$messageDateTime	=	$messageDateTime->setTimezone(new DateTimeZone('Asia/Makassar'));
			$messageDateTimeFile=	$messageDateTime->format('YmdHis');
			$messageDateTime	=	$messageDateTime->format('Y-m-d H:i:s');
			$fromObject			=	$headers['from'][0];
			$messageId			=	$headers['message_id'];
			$senderAddress		=	$fromObject->mailbox."@".$fromObject->host;
			$messageAttachments	=	$message->getAttachments();
			$elemAttachment		=	"";
			
			if(strpos($subject, 'Bali SUN Tours - Reconfirm Reservation') !== false){
				$expSubject			=	explode(':', $subject);
				$bookingCodeStr		=	trim(end($expSubject));
				$expBookingCode		=	explode(' ', $bookingCodeStr);
				$bookingCode		=	trim($expBookingCode[0]);
				$isReconfirmExist	=	$this->ModelCron->isReconfirmExist($bookingCode);
				$message->markAsSeen();
				
				if($isReconfirmExist && $isReconfirmExist['STATUSRESERVATION'] <= 3){
					$idReservationReconfirmation=	$isReconfirmExist['IDRESERVATIONRECONFIRMATION'];
					$customerName				=	$isReconfirmExist['CUSTOMERNAME'];
					$statusReconfirmation		=	$isReconfirmExist['STATUS'];
					$mailThreadArrName			=	$isReconfirmExist['MAILTHREADARRNAME'];
					$mailThreadArrName			=	$mailThreadArrName == '' ? 'Customer' : $mailThreadArrName.',Customer';
					$mailThreadFileName			=	$isReconfirmExist['MAILTHREADFILE'];
					$mailThreadFileNameNew		=	str_pad($idReservationReconfirmation, 6, '0', STR_PAD_LEFT)."-".$messageDateTimeFile;
					
					$arrUpdateReconfirmation	=	[
						'MAILTHREADFILE' 	=>	($mailThreadFileName == '' ? $mailThreadFileNameNew : $mailThreadFileName.','.$mailThreadFileNameNew),
						'MAILTHREADARRNAME'	=>	$mailThreadArrName,
						'MAILMESSAGEID' 	=>	$messageId,
						'DATETIMERESPONSE'	=>	date('Y-m-d H:i:s'),
						'STATUSREADTHREAD'	=>	0
					];
					
					if($statusReconfirmation == 1){
						$arrUpdateReconfirmation['MAILSUBJECT']	=	$subject;
						$arrUpdateReconfirmation['STATUS']		=	2;
						if(strpos($messageBody, 'I want to change the reservation data') !== false || strpos($messageBody, 'Required data information') !== false || strlen($messageBody) > 270) $arrUpdateReconfirmation['STATUS']	=	3;
					}
					
					if($isHtmlMessage){
						@$this->dom->loadHTML(mb_convert_encoding($messageBody, 'HTML-ENTITIES', "UTF-8"));
						$selector	=	new DOMXPath($this->dom);
						foreach($selector->query('//div[contains(attribute::class, "gmail_quote")]') as $e ) {
							$e->parentNode->removeChild($e);
						}

						$iAttachment		=	0;
						foreach($messageAttachments as $attachment) {
							$attachmentName	=	$attachment->getFilename();
							$iElem			=	0;
							file_put_contents(PATH_EMAIL_RECONFIRMATION_THREAD_ATTACHMENT.$attachmentName, $attachment->getDecodedContent());
							foreach($selector->query('//img') as $e ) {
								if($iElem == $iAttachment){
									$e->setAttribute("src", URL_MAIL_RECONFIRMATION_ATTACHMENT.$attachmentName);
								}
								$iElem++;
							}
							$iAttachment++;
							$elemAttachment	.=	'<br/><a class="badge badge-pill badge-info" href="'.URL_MAIL_RECONFIRMATION_ATTACHMENT.$attachmentName.'" target="_blank">Attachment #'.$iAttachment.'</a>';
						}
						$messageBody	=	$this->dom->saveHTML();
						$messageBody	.=	$elemAttachment;
					}
					
					file_put_contents(PATH_EMAIL_RECONFIRMATION_THREAD.$mailThreadFileNameNew, $messageBody);					
					$this->MainOperation->updateData('t_reservationreconfirmation', $arrUpdateReconfirmation, 'IDRESERVATIONRECONFIRMATION', $idReservationReconfirmation);
					if(PRODUCTION_URL) $this->updateWebappStatisticTagsUnreadThreadReconfirmation($customerName, $senderAddress);
				}
			}
		}
		echo "End read mail reconfirmation - ".date("d M Y H:i");
	}
	
	public function readMailReconfirmationDevel(){

		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		$server				= new Server('imap.gmail.com/ssl/NoValidate-Cert');
		$this->connection	= $server->authenticate(MAIL_USERNAME, MAIL_PASSWORD);
		$search				= new SearchExpression();
		$search->addCondition(new Subject('Bali SUN Tours - Reconfirm Reservation'));
		$search->addCondition(new From('chunpongfok@gmail.com'));

		$inbox		=	$this->connection->getMailbox('INBOX');
		$messages	=	$inbox->getMessages($search);
		
		foreach ($messages as $message) {
			$subject			=	$message->getSubject();
			$headers			=	$message->getHeaders();
			$messageBody		=	$message->getBodyHtml();
			$isHtmlMessage		=	$messageBody == "" || is_null($messageBody) ? false : true;
			$messageBody		=	$messageBody == "" || is_null($messageBody) ? $message->getBodyText() : $messageBody;
			$messageDateTime	=	$message->getDate();
			$messageDateTime	=	$messageDateTime->setTimezone(new DateTimeZone('Asia/Makassar'));
			$messageDateTimeFile=	$messageDateTime->format('YmdHis');
			$messageDateTime	=	$messageDateTime->format('Y-m-d H:i:s');
			$fromObject			=	$headers['from'][0];
			$messageId			=	$headers['message_id'];
			$senderAddress		=	$fromObject->mailbox."@".$fromObject->host;
			$attachments		=	$message->getAttachments();
			$elemAttachment		=	"";

			if($isHtmlMessage){
				@$this->dom->loadHTML(mb_convert_encoding($messageBody, 'HTML-ENTITIES', "UTF-8"));
				$selector	=	new DOMXPath($this->dom);
				foreach($selector->query('//div[contains(attribute::class, "gmail_quote")]') as $e ) {
					$e->parentNode->removeChild($e);
				}

				$iAttachment	=	0;
				foreach($attachments as $attachment) {
					$attachmentName	=	$attachment->getFilename();
					file_put_contents(PATH_EMAIL_RECONFIRMATION_THREAD_ATTACHMENT.$attachmentName, $attachment->getDecodedContent());
					if($isHtmlMessage){
						$iElem	=	0;
						foreach($selector->query('//img') as $e ) {
							if($iElem == $iAttachment){
								$e->setAttribute("src", URL_MAIL_RECONFIRMATION_ATTACHMENT.$attachmentName);
							}
							$iElem++;
						}
					}
					$iAttachment++;
					$elemAttachment	.=	'<br/><a class="badge badge-pill badge-info" href="'.URL_MAIL_RECONFIRMATION_ATTACHMENT.$attachmentName.'" target="_blank">Attachment #'.$iAttachment.'</a>';
				}
				$messageBody	=	$this->dom->saveHTML();
				$messageBody	.=	$elemAttachment;
			}
			echo $messageBody;
			die();
		}
		//$this->updateWebappStatisticTagsUnreadThreadReconfirmation();
		echo "End read mail reconfirmation - ".date("d M Y H:i");
	}
	
	private function updateWebappStatisticTagsUnreadThreadReconfirmation($customerName, $customerMailAddress){
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
									   'newMailThreadStatus'		=>	true,
									   'newMailThreadName'			=>	$customerName,
									   'newMailThreadAddress'		=>	$customerMailAddress,
									   'unreadThreadReconfirmation'	=>	$totalUnreadThreadReconfirmation,
									   'timestampUpdate'			=>	gmdate("YmdHis")
									  ]);
			} catch (Exception $e) {
			}
		}
		return true;		
	}
	
	public function createReviewBonusPeriodTarget(){
		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		$dataLastReviewPeriod	=	$this->ModelCron->getLastReviewPeriod();
		$maxDateReviewPeriod	=	$this->MainOperation->getValueSystemSettingVariable(13);
		$bonusPunishmentRate	=	$this->MainOperation->getValueSystemSettingVariable(14);
		$defaultReviewTarget	=	$this->MainOperation->getValueSystemSettingVariable(15);
		
		if($dataLastReviewPeriod){
			$lastMonthYear			=	$dataLastReviewPeriod['PERIODMONTHYEAR'];
			$lastDatePeriod			=	$dataLastReviewPeriod['PERIODDATEEND'];
			$lastMonthYearDateFormat=	DateTime::createFromFormat('Y-m', $lastMonthYear);
			$newMonthYearPeriod		=	$lastMonthYearDateFormat->modify("+1 months");
			$newMonthYearPeriod		=	$newMonthYearPeriod->format('Y-m');
			$isReviewPeriodExist	=	$this->ModelCron->isReviewPeriodExist($newMonthYearPeriod);
			
			if(!$isReviewPeriodExist && $lastDatePeriod < date('Y-m-d')){
				$lastDatePeriodFormat		=	DateTime::createFromFormat('Y-m-d', $lastDatePeriod);
				$newStartDatePeriodFormat	=	$lastDatePeriodFormat->modify("+1 days");
				$newStartDatePeriodFormat	=	$newStartDatePeriodFormat->format('Y-m-d');
				$newEndDatePeriodFormat		=	$newMonthYearPeriod."-".str_pad($maxDateReviewPeriod, 2, "0", STR_PAD_LEFT);
				
				try {
					DateTime::createFromFormat('Y-m-d', $newEndDatePeriodFormat);
				} catch (Exception $e) {
					$newEndDatePeriodFormat	=	DateTime::createFromFormat('Y-m-d', $newMonthYearPeriod."-01");
					$newEndDatePeriodFormat	=	$newEndDatePeriodFormat->format('Y-m-t');
				}
				
				$arrInsertReviewPeriod	=	[
					"PERIODMONTHYEAR"	=>	$newMonthYearPeriod,
					"PERIODDATESTART"	=>	$newStartDatePeriodFormat,
					"PERIODDATEEND"		=>	$newEndDatePeriodFormat,
					"BONUSRATE"			=>	$bonusPunishmentRate,
					"TOTALTARGET"		=>	$defaultReviewTarget
				];
				
				$this->MainOperation->addData('t_driverreviewbonusperiod', $arrInsertReviewPeriod);
				echo "Done new period - ".$newMonthYearPeriod."::".$bonusPunishmentRate;
				die();
			}			
		} else {
			echo "No data last period";
			die();
		}
	}
	
	public function apiScanCustomerContact($base64JsonData){
		$jsonData		=	base64_decode($base64JsonData);
		$arrData		=	json_decode($jsonData);
		$idReservation	=	isset($arrData->idReservation) ? $arrData->idReservation : false;

		if($idReservation){
			try {
				$this->cronScanCustomerContact($idReservation);
				echo "[S000] Done proccess for scan customer contact";
			} catch(Exception $e) {
				echo "[E002] Failed. Try again later";
			}
		} else {
			echo "[E001] Failed. Try again later";
		}
		
		die();
	}

	public function cronScanCustomerContact($idReservation = false){
		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		$arrDataCountryCode			=	$this->MainOperation->getDataCountryCode();
		$arrDataNameTitle			=	$this->MainOperation->getDataNameTitle();
		$dataReservationNoContact	=	$this->ModelCron->getDataReservationNoContact($idReservation);
		
		if($dataReservationNoContact){
			foreach($dataReservationNoContact as $keyReservationNoContact){
				$idReservation	=	$keyReservationNoContact->IDRESERVATION;
				$customerName	=	trim($keyReservationNoContact->CUSTOMERNAME);
				$idNameTitle	=	$this->cleanUpFullName($customerName, $arrDataNameTitle, false);
				$customerName	=	$this->cleanUpFullName($customerName, $arrDataNameTitle);
				$customerContact=	preg_replace('/[^0-9+]/', '', $keyReservationNoContact->CUSTOMERCONTACT);
				$customerEmail	=	sanitize_email($keyReservationNoContact->CUSTOMEREMAIL);
				$customerEmail	=	$customerEmail == "-" ? "" : $customerEmail;
				$isNumberExist	=	true;
				
				if(strlen($customerContact) > 8){
					$idCountry		=	$this->getIdCountryPhoneNumber($customerContact, $arrDataCountryCode);
					$phoneNumberBase=	$this->getCustomerPhoneNumberBase($customerContact, $idCountry, $arrDataCountryCode);
					$isNumberExist	=	$this->ModelCron->isNumberExist($phoneNumberBase, $idCountry);
					$idContact		=	0;	

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
						
						$this->MainOperation->updateData('t_reservation', ["IDCONTACT" => $idContact], "IDRESERVATION", $idReservation);
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
								$idContact	=	$procInsertContact['insertID'];
								$this->MainOperation->updateData('t_reservation', ["IDCONTACT" => $idContact], "IDRESERVATION", $idReservation);
							} else {
								$this->updateInvalidIdContactReservation($idReservation);
							}
						} else {
							$this->updateInvalidIdContactReservation($idReservation);
						}
						echo $customerName." (".$customerContact.") - New number added<br/>";
					}
					
					if($idContact != 0) $this->updateArrIdReservationTypeContact($idContact);
				} else {
					$this->updateInvalidIdContactReservation($idReservation);
					echo $customerName." (".$customerContact.") - Invalid number<br/>";
				}
			}
		} else {
			$idContact	=	$this->ModelCron->getIdContactReservation($idReservation);
			if($idContact != 0) $this->updateArrIdReservationTypeContact($idContact);
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
		$this->MainOperation->updateData('t_reservation', ["IDCONTACT" => -1], "IDRESERVATION", $idReservation);
	}
	
	private function updateArrIdReservationTypeContact($idContact){
		$arrIdReservationType	=	$this->ModelCron->getArrIdReservationType($idContact);
		$arrIdReservationType	=	$arrIdReservationType == "" ? "" : $arrIdReservationType;
		$this->MainOperation->updateData(APP_WHATSAPP_DATABASE_NAME.'.t_contact', ["ARRIDRESERVATIONTYPE" => $arrIdReservationType], "IDCONTACT", $idContact);
	}
	
	public function calculateRatingPointAndRecapAdditionalIncomeDriver(){
		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		$this->load->model('FinanceDriver/ModelAdditionalIncome');

		$userAdminName						=	'Auto System';
		$dataActiveDriverAddtiionalIncome	=	$this->ModelCron->getDataActiveDriverAdditionalIncome();
		$dataAdditionalIncomeDriver			=	$this->ModelCron->getDataAdditionalIncomePerIdDriver();
		
		if($dataActiveDriverAddtiionalIncome){
			$period		=	date('Y-m');
			foreach($dataActiveDriverAddtiionalIncome as $keyActiveDriverAddtiionalIncome){
				$idDriverAdditionalIncome		=	$keyActiveDriverAddtiionalIncome->IDDRIVER;
				$isAdditionalIncomeRecapExist	=	$this->ModelCron->isAdditionalIncomeRecapExist($period, $idDriverAdditionalIncome);
				
				if(!$isAdditionalIncomeRecapExist){
					$arrInsertAdditionalIncomeRecap	=	[
						"IDDRIVER"				=>	$idDriverAdditionalIncome,
						"IDDRIVERRATINGPOINT"	=>	0,
						"PERIOD"				=>	$period,
						"NUMBEROFPAYMENT"		=>	0,
						"NOMINAL"				=>	0,
						"EXCEPTIONREASON"		=>	"",
						"DATELASTPAYMENT"		=>	"0000-00-00"
					];
					$this->MainOperation->addData('t_additionalincomerecap', $arrInsertAdditionalIncomeRecap);
				}
			}
		}

		if($dataAdditionalIncomeDriver){
			foreach($dataAdditionalIncomeDriver as $keyAdditionalIncomeDriver){
				$idDriver					=	$keyAdditionalIncomeDriver->IDDRIVER;
				$yearMonth					=	$keyAdditionalIncomeDriver->YEARMONTH;
				$numberOfAdditionalIncome	=	$keyAdditionalIncomeDriver->NUMBEROFADDITIONALINCOME;
				$totalIncomeNominal			=	$keyAdditionalIncomeDriver->TOTALINCOMENOMINAL;
				$strArrIdAdditionalIncome	=	$keyAdditionalIncomeDriver->STRARRIDADDITIONALINCOME;
				$lastAdditionalIncomePayment=	$keyAdditionalIncomeDriver->MAXINCOMEDATE;
				$dataPointReview			=	$this->ModelAdditionalIncome->getPointReviewAdditionalIncome($totalIncomeNominal);
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

				$procInsertUpdateRatingPoint=	$idDriverRatingPoint == 0 ? 
												$this->MainOperation->addData('t_driverratingpoint', $arrInsertUpdateRatingPoint) :
												$this->MainOperation->updateData('t_driverratingpoint', $arrInsertUpdateRatingPoint, 'IDDRIVERRATINGPOINT', $idDriverRatingPoint);
				if($procInsertUpdateRatingPoint['status'])  {
					$idDriverRatingPoint	=	$idDriverRatingPoint == 0 ? $procInsertUpdateRatingPoint['insertID'] : $idDriverRatingPoint;
					$arrIdAdditionalIncome	=	explode(',', $strArrIdAdditionalIncome);
					$this->MainOperation->updateDataIn('t_additionalincome', ['IDDRIVERRATINGPOINT' => $idDriverRatingPoint], 'IDADDITIONALINCOME', $arrIdAdditionalIncome);
				}
				
				$arrUpdateRecap			=	[
					"IDDRIVERRATINGPOINT"	=>	$idDriverRatingPoint,
					"NUMBEROFPAYMENT"		=>	$numberOfAdditionalIncome,
					"NOMINAL"				=>	$totalIncomeNominal,
					"DATELASTPAYMENT"		=>	$lastAdditionalIncomePayment
				];
				$this->MainOperation->updateData('t_additionalincomerecap', $arrUpdateRecap, ['IDDRIVER' => $idDriver, 'PERIOD' => $yearMonth]);
			}
		}
		
		$urlAPISetPointRankDriver	=	BASE_URL."schedule/driverRatingPoint/apiSetPointRankDriver";
		try {
			json_decode(trim(curl_get_file_contents($urlAPISetPointRankDriver)));
		} catch(Exception $e) {
		}
	
		echo "Done - Proccess calculate rating point additional income";
		die();
	}
	
	public function cronEBookingCoinNonDriver(){
		$this->load->model('MainOperation');
		$this->load->model('ModelCron');
		
		$dateTimeNow 			=	new DateTime();
		$dateTimeMinus12Hours	=	clone $dateTimeNow->modify('-12 hours');
		$dateTimeMinus18Hours	=	clone $dateTimeNow->modify('-18 hours');
		$dateTimeMinus12Hours	=	$dateTimeMinus12Hours->format('Y-m-d H:i:s');
		$dateTimeMinus18Hours	=	$dateTimeMinus18Hours->format('Y-m-d H:i:s');
		
		$dataEBooking	=	$this->ModelCron->getDataEBookingEarnCoin($dateTimeMinus12Hours, $dateTimeMinus18Hours);
		if($dataEBooking){
			foreach($dataEBooking as $keyEBooking){
				$idReservation	=	$keyEBooking->IDRESERVATION;
				$bookingCode	=	$keyEBooking->BOOKINGCODE;
				$arrInsert		=	[
					'IDRESERVATION'	=>	$idReservation,
					'EXECUTETYPE'	=>	2,
					'DATETIMEINSERT'=>	date('Y-m-d H:i:s'),
					'STATUS'		=>	0
				];
				
				$this->MainOperation->addData('t_ebookingcoin', $arrInsert);
			}
		}
		
		$this->executeEBookingCoinEarned();
		echo "Done - ".date('Y-m-d H:i:s');
	}
	
	private function executeEBookingCoinEarned(){
		$dataExecuteEBooking=	$this->ModelCron->getDataExecuteEBookingCoinEarned();
		
		if($dataExecuteEBooking){
			$timeStamp		=	time();
			foreach($dataExecuteEBooking as $keyExecuteEBooking){
				$idEBookingCoin	=	$keyExecuteEBooking->IDEBOOKINGCOIN;
				$bookingCode	=	$keyExecuteEBooking->BOOKINGCODE;
				$dataJSON       =   json_encode(['booking_code'=>$bookingCode, 'timestamp'=>$timeStamp]);
				$privateKey     =   ROKET_ECOMMERCE_PRIVATE_KEY;
				$hmacSignature  =   hash_hmac('sha256', $dataJSON, $privateKey);
				$procUpdateCoin	=	$this->updateCoinBookingEcommerce($bookingCode, $hmacSignature, $timeStamp);
				$httpCode		=	$procUpdateCoin['httpCode'];
				$response		=	$procUpdateCoin['response'];
				$arrUpdateCoin	=	[
					'EXECUTETYPE'		=>	2,
					'EXECUTEBY'			=>	'Auto System',
					'DATETIMEEXECUTE'	=>	date('Y-m-d H:i:s'),
					'APIRESPONSE'		=>	$response,
					'STATUS'			=>	0
				];
				
				switch(intval($httpCode)){
					case 200	:	$arrUpdateCoin['STATUS']	=	1; break;
					case 401	:	$arrUpdateCoin['STATUS']	=	-1; break;
					case 409	:	$arrUpdateCoin['STATUS']	=	1; break;
				}
				
				$this->MainOperation->updateData('t_ebookingcoin', $arrUpdateCoin, 'IDEBOOKINGCOIN', $idEBookingCoin);
				echo $bookingCode."<br/>";
				echo $httpCode."<br/>";
				echo json_encode($response)."<br/>";
			}
		}
		
		return true;
	}
	
	private function updateCoinBookingEcommerce($bookingCode, $hmacSignature, $timeStamp){
		$response	=	"";
		$httpCode	=	500;

		try {
			$curl	=	curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL				=>	ROKET_ECOMMERCE_API_BASE_URL.'/api/customer/coin/earn-from-booking?booking_code='.$bookingCode,
			  CURLOPT_RETURNTRANSFER	=>	true,
			  CURLOPT_ENCODING			=>	'',
			  CURLOPT_MAXREDIRS			=>	10,
			  CURLOPT_TIMEOUT			=>	0,
			  CURLOPT_FOLLOWLOCATION	=>	true,
			  CURLOPT_HTTP_VERSION		=>	CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST		=>	'POST',
			  CURLOPT_HTTPHEADER		=>	array(
				'BST-Public-Key: '.ROKET_ECOMMERCE_PUBLIC_KEY,
				'BST-Signature: '.$hmacSignature,
				'BST-Timestamp: '.$timeStamp
			  ),
			));

			$response	=	curl_exec($curl);
			$httpCode	=	curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
		} catch (Exception $e) {
		}
		
		return [
			'httpCode'	=>	$httpCode,
			'response'	=>	json_encode($response)
		];
	}

	public function readKlookBadReviewMail(){
		$this->load->library('fcm');
		$this->load->model('ModelCron');
		
		$search		= new SearchExpression();
		$search->addCondition(new From(MAIL_KLOOK_BAD_REVIEW_SENDER_ADDRESS));
		$search->addCondition(new Unseen());

		$inbox		=	$this->connection->getMailbox('INBOX');
		$messages	=	$inbox->getMessages($search);

		foreach ($messages as $message) {
			$message->markAsSeen();
			
			$subject			=	$message->getSubject();
			if(strpos($subject, 'You have a new review from Klook') !== false){
				$bookingCode	=	$reviewContent	=	'';
				$reviewDateTime	=	'0000-00-00 00:00:00';
				$rating			=	1;
				$messageHTML	=	$message->getBodyHTML();
				
				@$this->dom->loadHTML($messageHTML);
				$table1			= 	$this->dom->getElementsByTagName('table')->item(0);
				$row1			=	$table1->getElementsByTagName('tr')->item(0);
				$col1			=	$row1->getElementsByTagName('td')->item(0);
				
				$table2			= 	$col1->getElementsByTagName('table')->item(0);
				$row2			=	$table2->getElementsByTagName('tr')->item(0);
				$col2			=	$row2->getElementsByTagName('td')->item(1);

				$table3			= 	$col2->getElementsByTagName('table')->item(0);
				$tableBooking	= 	$table3->getElementsByTagName('table')->item(1);
				$tableReview	= 	$table3->getElementsByTagName('table')->item(3);
				
				$rowsBooking	=	$tableBooking->getElementsByTagName('tr');
				$rowReview		=	$tableReview->getElementsByTagName('tr')->item(0);
				
				foreach($rowsBooking as $rowBooking){
					$colsBooking		=	$rowBooking->getElementsByTagName('td');
					$colsBookingTitleEl	=	$colsBooking[0]->getElementsByTagName('span');
					$colsBookingTitle	=	count($colsBookingTitleEl) > 0 ? $colsBookingTitleEl[0]->textContent : '';
					
					if($colsBookingTitle == 'Booking reference ID:'){
						$spansRemove	=	$colsBooking[0]->getElementsByTagName('span');
						for ($i = $spansRemove->length - 1; $i >= 0; $i--) {
							$spanRemove	=	$spansRemove->item($i);
							$spanRemove->parentNode->removeChild($spanRemove);
						}
						
						$bookingCode	=	trim($colsBooking[0]->textContent);
					}
				}

				$colsReview		=	$rowReview->getElementsByTagName('td')->item(1);
				$tableReview	=	$colsReview->getElementsByTagName('table')->item(0);				
				$childRowsReview=	$tableReview->getElementsByTagName('tr');
				
				foreach($childRowsReview as $childRowReview){
					$colsReview			=	$childRowReview->getElementsByTagName('td');
					$colsReviewTitleEl	=	$colsReview[0]->getElementsByTagName('span');
					$colsReviewTitle	=	count($colsReviewTitleEl) > 0 ? $colsReviewTitleEl[0]->textContent : '';
					
					if($colsReviewTitle == 'Review Date:'){
						$spansRemove	=	$colsReview[0]->getElementsByTagName('span');
						for ($i = $spansRemove->length - 1; $i >= 0; $i--) {
							$spanRemove	=	$spansRemove->item($i);
							$spanRemove->parentNode->removeChild($spanRemove);
						}
						
						$reviewDateTime	=	trim($colsReview[0]->textContent);
					}
					
					if($colsReviewTitle == 'Rating:'){
						$rating	=	$colsReview[0]->getElementsByTagName('span')->item(1)->textContent;
						$rating	=	str_replace(' / 5', '', $rating);
					}
					
					if($colsReviewTitle == ''){
						$reviewContent	=	trim($colsReview[0]->textContent);
					}
					
				}
				
				if($bookingCode != ''){
					try {
						$curl			=	curl_init();
						$timeStamp		=	time();
						$dataJSON       =   json_encode(['booking_code'=>$bookingCode, 'timestamp'=>$timeStamp]);
						$privateKey     =   ROKET_ECOMMERCE_PRIVATE_KEY;
						$hmacSignature  =   hash_hmac('sha256', $dataJSON, $privateKey);
						$isDriverOwnCar	=	$this->ModelCron->isDriverHandleOwnCarByBookingCode($bookingCode);
						$arrPostData	=	[
							'review'		=>	$reviewContent,
							'booking_code'	=>	$bookingCode,
							'review_date'	=>	substr($reviewDateTime, 0, 10),
							'review_star'	=>	$rating,
							'driver_car'	=>	$isDriverOwnCar
						];
						
						curl_setopt_array($curl,
							array(
								CURLOPT_URL				=>	ROKET_ECOMMERCE_API_BASE_URL.'/api/customer/coin/earn-from-booking?booking_code='.$bookingCode,
								CURLOPT_RETURNTRANSFER	=>	true,
								CURLOPT_ENCODING		=>	'',
								CURLOPT_MAXREDIRS		=>	10,
								CURLOPT_TIMEOUT			=>	100,
								CURLOPT_FOLLOWLOCATION	=>	true,
								CURLOPT_HTTP_VERSION	=>	CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST	=>	'POST',
								CURLOPT_POSTFIELDS      =>  http_build_query($arrPostData),
								CURLOPT_HTTPHEADER		=>	array(
									'BST-Public-Key: '.ROKET_ECOMMERCE_PUBLIC_KEY,
									'BST-Signature: '.$hmacSignature,
									'BST-Timestamp: '.$timeStamp
								)
							)
						);

						// $response	=	curl_exec($curl);
						// $httpCode	=	curl_getinfo($curl, CURLINFO_HTTP_CODE);
						// curl_close($curl);
					} catch (Exception $e) {
						log_message('error', 'readKlookBadReviewMail -> error :: '.json_encode($e));
					}
				}
			}
			
			die();
		}
		
		echo "End read bad review klook - ".date("d M Y H:i");
	}
}