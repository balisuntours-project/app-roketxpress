<?php

require_once(FCPATH."vendor/phpmailer/phpmailer/src/Exception.php");
require_once(FCPATH."vendor/phpmailer/phpmailer/src/PHPMailer.php");
require_once(FCPATH."vendor/phpmailer/phpmailer/src/SMTP.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

defined('BASEPATH') OR exit('No direct script access allowed');

class ReservationInvoice extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$functionName	=	$this->uri->segment(3);

		if($functionName != "viewMailContentDevel" && $_SERVER['REQUEST_METHOD'] === 'POST'){
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
	
	public function searchDataReservation(){
		$this->load->model('Finance/ModelReservationInvoice');
		$this->load->model('MainOperation');
		
		$nameBookingCode=	validatePostVar($this->postVar, 'nameBookingCode', false);
		$month			=	validatePostVar($this->postVar, 'month', false);
		$year			=	validatePostVar($this->postVar, 'year', false);
		
		if((!isset($nameBookingCode) || $nameBookingCode == "") && (!isset($month) || $month == "") && (!isset($year) || $year == "")){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please submit a valid data search"));
		}
		
		$dataSearch		=	$this->ModelReservationInvoice->searchDataReservation($nameBookingCode, $month, $year);
		
		if(!$dataSearch) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		setResponseOk(array("token"=>$this->newToken, "data"=>$dataSearch));	
	}
	
	public function getInvoiceNumberDetailCost(){
		$this->load->model('Finance/ModelReservationInvoice');
		$this->load->model('MainOperation');
		
		$idReservation		=	validatePostVar($this->postVar, 'idReservation', true);
		$invoiceNumber		=	$this->ModelReservationInvoice->getNewInvoiceNumber();
		$listInvoice		=	$this->ModelReservationInvoice->getListReservationInvoice($idReservation);
		$listDriverCost		=	$this->ModelReservationInvoice->getListReservationDriverCost($idReservation);
		$listCarCost		=	$this->ModelReservationInvoice->getListReservationCarCost($idReservation);
		$listTicketCost		=	$this->ModelReservationInvoice->getListReservationTicketCost($idReservation);
		$listAdditionalCost	=	$this->ModelReservationInvoice->getListReservationAdditionalCost($idReservation);
		
		setResponseOk(
			array(
				"token"				=>$this->newToken,
				"invoiceNumber"		=>$invoiceNumber,
				"listInvoice"		=>$listInvoice,
				"listDriverCost"	=>$listDriverCost,
				"listCarCost"		=>$listCarCost,
				"listTicketCost"	=>$listTicketCost,
				"listAdditionalCost"=>$listAdditionalCost
			)
		);
	}
	
	public function submitReservationInvoice(){
		$this->checkInputData();
		$this->load->model('Finance/ModelReservationInvoice');
		$this->load->model('MainOperation');
		
		$invoiceNumber		=	$this->ModelReservationInvoice->getNewInvoiceNumber();
		$idReservation		=	validatePostVar($this->postVar, 'idReservation', true);
		$invoiceType		=	validatePostVar($this->postVar, 'invoiceType', true);
		$invoiceDate		=	validatePostVar($this->postVar, 'invoiceDate', true);
		$invoiceDate		=	DateTime::createFromFormat('d-m-Y', $invoiceDate);
		$invoiceDateStr		=	$invoiceDate->format('d M Y');
		$invoiceDate		=	$invoiceDate->format('Y-m-d');
		$customerName		=	validatePostVar($this->postVar, 'customerName', true);
		$customerContact	=	validatePostVar($this->postVar, 'customerContact', true);
		$customerEmail		=	validatePostVar($this->postVar, 'customerEmail', true);
		$detailItemInvoice	=	validatePostVar($this->postVar, 'detailItemInvoice', true);
		$totalBalance		=	validatePostVar($this->postVar, 'totalBalance', false);
		$totalBalance		=	str_replace(",", "", $totalBalance) * 1;
		$cekMailPattern		=	checkMailPattern($customerEmail);
		
		if(!$cekMailPattern) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please enter a valid customer email"));
		if(!is_array($detailItemInvoice) || count($detailItemInvoice) <= 0) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please input invoice item first (at least one)"));
		
		$totalItemAmount	=	0;
		foreach($detailItemInvoice as $keyItemInvoice){
			$totalItemAmount	+=	$keyItemInvoice[4] * 1;
		}

		$totalInvoiceAmount	=	$totalItemAmount - $totalBalance;
		$detailUserAdmin	=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName		=	$detailUserAdmin['NAME'];
		$prefixFileName		=	PRODUCTION_URL ? "" : "DEVEL-";
		$invoiceFileName	=	$prefixFileName.$invoiceNumber.".html";
		$arrInsertRecap		=	array(
									"IDRESERVATION"			=>	$idReservation,
									"INVOICENUMBER"			=>	$invoiceNumber,
									"INVOICETYPE"			=>	$invoiceType,
									"INVOICEDATE"			=>	$invoiceDate,
									"INVOICEFILE"			=>	$invoiceFileName,
									"CUSTOMERNAME"			=>	$customerName,
									"CUSTOMERCONTACT"		=>	$customerContact,
									"CUSTOMEREMAIL"			=>	$customerEmail,
									"TOTALITEMAMAOUNT"		=>	$totalItemAmount,
									"TOTALBALANCEAMOUNT"	=>	$totalBalance,
									"TOTALINVOICEAMOUNT"	=>	$totalInvoiceAmount,
									"USERINPUT"				=>	$userAdminName,
									"DATETIMEINPUT"			=>	date('Y-m-d H:i:s')
								);
		$procInsertRecap	=	$this->MainOperation->addData("t_reservationinvoicerecap", $arrInsertRecap);
		
		if(!$procInsertRecap['status']) switchMySQLErrorCode($procUpdate['errCode'], $this->newToken);

		$idInvoiceRecap		=	$procInsertRecap['insertID'];
		$itemInvoiceContent	=	$itemInvoiceEmail	=	"";
		foreach($detailItemInvoice as $keyItemInvoice){
			$description	=	$keyItemInvoice[0];
			$subDescription	=	$keyItemInvoice[1];
			$rate			=	$keyItemInvoice[2];
			$quantity		=	$keyItemInvoice[3];
			$totalAmount	=	$keyItemInvoice[4];
			
			$arrInsertDetail=	array(
				"IDRESERVATIONINVOICERECAP"	=>	$idInvoiceRecap,
				"DESCRIPTION"				=>	$description,
				"SUBDESCRIPTION"			=>	$subDescription,
				"RATE"						=>	$rate,
				"QUANTITY"					=>	$quantity,
				"TOTALAMOUNT"				=>	$totalAmount
			);
			$this->MainOperation->addData("t_reservationinvoicedetail", $arrInsertDetail);
				
			$itemInvoiceContent	.=	"<tr class='py-2 previewFont' style='border-bottom: 1px solid #e0e0e0;'>";
			$itemInvoiceContent	.=	"	<td>";
			$itemInvoiceContent	.=			$description."<br/>";
			$itemInvoiceContent	.=	"		<small style='margin-bottom: 10px'>".$subDescription."</small>";
			$itemInvoiceContent	.=	"		<p style='margin-top: 10px'>".number_format($quantity, 0, ".", ",")." Pax (price per person) x @".number_format($rate, 0, ".", ",")."</p>";
			$itemInvoiceContent	.=	"	</td>";
			$itemInvoiceContent	.=	"	<td valign='bottom' align='right'>".number_format($totalAmount, 0, ".", ",")."</td>";
			$itemInvoiceContent	.=	"</tr>";

			$itemInvoiceEmail	.=	"<tr class='py-2 previewFont' style='border-bottom: 1px solid #e0e0e0;'>";
			$itemInvoiceEmail	.=	"	<td style='border-bottom: 1px solid #e0e0e0;'>";
			$itemInvoiceEmail	.=			$description."<br/>";
			$itemInvoiceEmail	.=	"		<small style='margin-bottom: 10px'>".$subDescription."</small><br/>";
			$itemInvoiceEmail	.=	"		<p>".number_format($quantity, 0, ".", ",")." Pax (price per person) x @".number_format($rate, 0, ".", ",")."</p>";
			$itemInvoiceEmail	.=	"	</td>";
			$itemInvoiceEmail	.=	"	<td valign='bottom' style='border-bottom: 1px solid #e0e0e0;' align='right'>".number_format($totalAmount, 0, ".", ",")."</td>";
			$itemInvoiceEmail	.=	"</tr>";
		}
		
		$arrInvoiceContent	=	array(
			"invoiceNumber"			=>	$invoiceNumber,
			"invoiceDateStr"		=>	$invoiceDateStr,
			"customerName"			=>	$customerName,
			"customerContact"		=>	$customerContact,
			"itemInvoiceContent"	=>	$itemInvoiceContent,
			"itemInvoiceEmail"		=>	$itemInvoiceEmail,
			"totalItemAmount"		=>	number_format($totalItemAmount, 0, ".", ","),
			"totalBalance"			=>	number_format($totalBalance, 0, ".", ","),
			"totalInvoiceAmount"	=>	number_format($totalInvoiceAmount, 0, ".", ",")
		);
		$invoiceFileContent	=	$this->load->view('mail/invoiceFile', $arrInvoiceContent, true);
		$invoiceEmailContent=	$this->load->view('mail/invoiceEmail', $arrInvoiceContent, true);
		file_put_contents(PATH_INVOICE_HTML_FILE.$invoiceFileName, $invoiceFileContent);
		$this->sendInvoiceToEmail($customerEmail, $customerName, $invoiceNumber, $invoiceEmailContent);
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New customer invoice has been created"));
	}
	
	public function viewMailContentDevel(){
		$itemInvoiceContent	=	"<tr class='py-2 previewFont' style='border-bottom: 1px solid #e0e0e0;'>";
		$itemInvoiceContent	.=	"	<td>";
		$itemInvoiceContent	.=	"		Ubud Full Tour Package<br/>";
		$itemInvoiceContent	.=	"		<small style='margin-bottom: 10px'>Ubud Tour: Rice Terrace, Monkey Forest, Elephant Cave, Batuan Temple, Waterfall</small>";
		$itemInvoiceContent	.=	"		<p style='margin-top: 10px'>".number_format(4, 0, ".", ",")." Pax (price per person) x @".number_format(500000, 0, ".", ",")."</p>";
		$itemInvoiceContent	.=	"	</td>";
		$itemInvoiceContent	.=	"	<td valign='bottom' align='right'>".number_format(2000000, 0, ".", ",")."</td>";
		$itemInvoiceContent	.=	"</tr>";

		$itemInvoiceEmail	=	"<tr class='py-2 previewFont' style='border-bottom: 1px solid #e0e0e0;'>";
		$itemInvoiceEmail	.=	"	<td style='border-bottom: 1px solid #e0e0e0;'>";
		$itemInvoiceEmail	.=	"		Ubud Full Tour Package<br/>";
		$itemInvoiceEmail	.=	"		<small style='margin-bottom: 10px'>Ubud Tour: Rice Terrace, Monkey Forest, Elephant Cave, Batuan Temple, Waterfall</small><br/>";
		$itemInvoiceEmail	.=	"		<p>".number_format(4, 0, ".", ",")." Pax (price per person) x @".number_format(500000, 0, ".", ",")."</p>";
		$itemInvoiceEmail	.=	"	</td>";
		$itemInvoiceEmail	.=	"	<td valign='bottom' style='border-bottom: 1px solid #e0e0e0;' align='right'>".number_format(2000000, 0, ".", ",")."</td>";
		$itemInvoiceEmail	.=	"</tr>";

		$arrInvoiceContent	=	array(
			"invoiceNumber"			=>	"INV000007",
			"invoiceDateStr"		=>	"08 Mar 2025",
			"customerName"			=>	"Rachael Curry",
			"customerContact"		=>	"+628970444360",
			"itemInvoiceContent"	=>	$itemInvoiceContent,
			"itemInvoiceEmail"		=>	$itemInvoiceEmail,
			"totalItemAmount"		=>	number_format(2000000, 0, ".", ","),
			"totalBalance"			=>	number_format(0, 0, ".", ","),
			"totalInvoiceAmount"	=>	number_format(2000000, 0, ".", ",")
		);
		
		$invoiceEmailContent	=	$this->load->view('mail/invoiceEmail', $arrInvoiceContent, true);
		$invoiceFileContent		=	$this->load->view('mail/invoiceFile', $arrInvoiceContent, true);
		//$this->sendInvoiceToEmail('20111501003@undhirabali.ac.id', "Agus Adiyasa", "DEVEL-INV000004", $invoiceEmailContent);
		
		echo $invoiceEmailContent;
	}
	
	private function checkInputData(){
		$arrVarValidate	=	array(
			array("customerName","text","Customer Name"),
			array("customerContact","text","Customer Phone Number"),
			array("customerEmail","text","Customer Email"),
		);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;
	}
	
	private function sendInvoiceToEmail($customerEmail, $customerName, $invoiceNumber, $invoiceEmailContent){
		$mail	=	new PHPMailer(true);

		try {
			$mail->isSMTP();
			$mail->Host			= MAIL_HOST;
			$mail->SMTPAuth		= true;
			$mail->Username		= MAIL_USERNAME;
			$mail->Password		= MAIL_PASSWORD;
			$mail->SMTPSecure	= PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port			= MAIL_SMTPPORT;

			$mail->setFrom(MAIL_USERNAME, MAIL_NAME);
			$mail->setFrom(MAIL_FROMADDRESS, MAIL_NAME);
			$mail->addAddress($customerEmail, $customerName);
			$mail->addReplyTo(MAIL_USERNAME, MAIL_NAME);
			$mail->addReplyTo(MAIL_FROMADDRESS, MAIL_NAME);

			$mail->Subject	=	"Invoice for your reservation, invoice number : ".$invoiceNumber;
			$mail->Body   	=	$invoiceEmailContent;
			$mail->isHTML(true);
			$mail->send();
			
			return true;
			
		} catch (Exception $e) {
			return true;
		}
	}
	
	public function getDataInvoiceHistory(){
		$this->load->model('Finance/ModelReservationInvoice');
		$this->load->model('MainOperation');
		
		$page		=	validatePostVar($this->postVar, 'page', true);
		$keyword	=	validatePostVar($this->postVar, 'keyword', false);
		$startDate	=	validatePostVar($this->postVar, 'startDate', true);
		$endDate	=	validatePostVar($this->postVar, 'endDate', true);
		$startDate	=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate	=	$startDate->format('Y-m-d');
		$endDate	=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate	=	$endDate->format('Y-m-d');
		$dataTable	=	$this->ModelReservationInvoice->getDataInvoiceHistory($page, 25, $keyword, $startDate, $endDate);
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	}
}