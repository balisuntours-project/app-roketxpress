<?php
class ModelReservationInvoice extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function searchDataReservation($nameBookingCode, $month, $year){
		$con_bookingCode	=	!isset($nameBookingCode) || $nameBookingCode == "" ? "1=1" : "A.BOOKINGCODE LIKE '%".$nameBookingCode."%'";
		$con_customerName	=	!isset($nameBookingCode) || $nameBookingCode == "" ? "1=1" : "A.CUSTOMERNAME LIKE '%".$nameBookingCode."%'";
		$con_monthYear		=	isset($month) && $month != "" && isset($year) && $year != "" ? "LEFT(A.RESERVATIONDATESTART, 7) = '".$year."-".$month."'" : "1=1";
		$con_month			=	isset($month) && $month != "" ? "SUBSTRING(A.RESERVATIONDATESTART, 6, 2) = '".$month."'" : "1=1";
		$con_year			=	isset($year) && $year != "" ? "SUBSTRING(A.RESERVATIONDATESTART, 1, 4) = '".$year."'" : "1=1";
		$baseQuery			=	"SELECT C.RESERVATIONTYPE, B.SOURCENAME, A.INPUTTYPE, A.RESERVATIONTITLE, A.DURATIONOFDAY, 
										DATE_FORMAT(A.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
										DATE_FORMAT(A.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND,
										LEFT(A.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART, LEFT(A.RESERVATIONTIMEEND, 5) AS RESERVATIONTIMEEND,
										A.CUSTOMERNAME, A.CUSTOMERCONTACT, A.CUSTOMEREMAIL, A.HOTELNAME, A.PICKUPLOCATION, A.DROPOFFLOCATION,
										A.NUMBEROFADULT, A.NUMBEROFCHILD, A.NUMBEROFINFANT, A.BOOKINGCODE, A.IDRESERVATIONTYPE,
										A.IDRESERVATION
								FROM t_reservation A
								LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
								LEFT JOIN m_reservationtype C ON A.IDRESERVATIONTYPE = C.IDRESERVATIONTYPE
								WHERE (".$con_bookingCode." OR ".$con_customerName.") AND ".$con_monthYear." AND ".$con_month." AND ".$con_year."
								ORDER BY A.RESERVATIONDATESTART, A.CUSTOMERNAME, A.RESERVATIONTITLE";
		$query				=	$this->db->query($baseQuery);
		$result				=	$query->result();
		
		if(isset($result)) return $result;
		return false;		
	}
	
	public function getNewInvoiceNumber(){
		$baseQuery	=	"SELECT IDRESERVATIONINVOICERECAP + 1 AS LASTINVOICENUMBER
						FROM t_reservationinvoicerecap
						ORDER BY IDRESERVATIONINVOICERECAP DESC";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return "INV".str_pad($row['LASTINVOICENUMBER'], 6, "0", STR_PAD_LEFT);
		return "INV000001";	
	}

	public function getListReservationInvoice($idReservation){
		$baseQuery	=	"SELECT IDRESERVATIONINVOICERECAP, INVOICENUMBER,
								DATE_FORMAT(INVOICEDATE, '%d %b %Y') AS INVOICEDATE,
								CONCAT('".URL_RESEVATION_INVOICE_FILE."', INVOICEFILE) AS URLINVOICE
						FROM t_reservationinvoicerecap
						WHERE IDRESERVATION = ".$idReservation."
						ORDER BY INVOICENUMBER";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return array();		
	}
		
	public function getListReservationCarCost($idReservation){
		$baseQuery	=	"SELECT C.NAME, DATE_FORMAT(D.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATE,
								CONCAT(B.BRAND, ' ', B.MODEL, ' [', B.PLATNUMBER, ']') AS CARDETAILS,
								D.PRODUCTNAME
						FROM t_schedulecar A
						LEFT JOIN t_carvendor B ON A.IDCARVENDOR = B.IDCARVENDOR
						LEFT JOIN m_vendor C ON B.IDVENDOR = C.IDVENDOR
						LEFT JOIN t_reservationdetails D ON A.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS
						WHERE D.IDRESERVATION = ".$idReservation." AND D.STATUS = 1
						ORDER BY D.SCHEDULEDATE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!isset($result)) return false;
		return $result;		
	}
		
	public function getListReservationDriverCost($idReservation){
		$baseQuery	=	"SELECT D.DRIVERTYPE, B.NAME, DATE_FORMAT(C.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATE, C.PRODUCTNAME
						FROM t_scheduledriver A
						LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
						LEFT JOIN t_reservationdetails C ON A.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
						LEFT JOIN m_drivertype D ON B.IDDRIVERTYPE = D.IDDRIVERTYPE
						WHERE C.IDRESERVATION = ".$idReservation." AND C.STATUS = 1
						ORDER BY C.SCHEDULEDATE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!isset($result)) return false;
		return $result;		
	}
		
	public function getListReservationTicketCost($idReservation){
		$baseQuery	=	"SELECT B.NAME, DATE_FORMAT(A.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATE, A.PRODUCTNAME,
								C.PAXADULT, C.PAXCHILD, C.PAXINFANT, C.PRICEPERPAXADULT, C.PRICEPERPAXCHILD,
								C.PRICEPERPAXINFANT, C.PRICETOTALADULT, C.PRICETOTALCHILD, C.PRICETOTALINFANT
						FROM t_reservationdetails A
						LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
						LEFT JOIN t_reservationdetailsticket C ON A.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
						WHERE A.IDRESERVATION = ".$idReservation." AND A.STATUS = 1 AND C.IDRESERVATIONDETAILS IS NOT NULL
						ORDER BY A.SCHEDULEDATE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!isset($result)) return false;
		return $result;		
	}

	public function getListReservationAdditionalCost($idReservation){
		$baseQuery	=	"SELECT A.IDRESERVATIONADDITIONALCOST, B.PRODUCTNAME, C.ADDITIONALCOSTTYPE, A.DESCRIPTION, A.NOMINAL,
								D.NAME AS DRIVERNAME, CONCAT('".URL_ADDITIONAL_COST_IMAGE."', A.IMAGERECEIPT) AS IMAGERECEIPT,
								DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT,
								CASE
									WHEN A.STATUSAPPROVAL = 0 THEN 'Waiting for approval'
									WHEN A.STATUSAPPROVAL = 1 THEN 'Approved'
									WHEN A.STATUSAPPROVAL = -1 THEN 'Rejected'
									ELSE '-'
								END AS STRSTATUSAPPROVAL,
								A.STATUSAPPROVAL
						 FROM t_reservationadditionalcost A
						 LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						 LEFT JOIN m_additionalcosttype C ON A.IDADDITIONALCOSTTYPE = C.IDADDITIONALCOSTTYPE
						 LEFT JOIN m_driver D ON A.IDDRIVER = D.IDDRIVER
						 WHERE B.IDRESERVATION = '".$idReservation."' AND A.STATUSAPPROVAL IN (0,1)
						 ORDER BY A.DATETIMEINPUT ASC";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return array();		
	}

	public function getDataInvoiceHistory($page, $dataPerPage= 25, $keyword, $startDate, $endDate){
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$startid	=	($page * 1 - 1) * $dataPerPage;
		$con_keyword=	!isset($keyword) || $keyword == "" ? "1=1" : "(B.CUSTOMERNAME LIKE '%".$keyword."%' OR B.BOOKINGCODE LIKE '%".$keyword."%')";
		$baseQuery	=	"SELECT A.IDRESERVATIONINVOICERECAP, D.RESERVATIONTYPE, C.SOURCENAME, B.INPUTTYPE, B.RESERVATIONTITLE, B.DURATIONOFDAY, 
								DATE_FORMAT(B.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART, DATE_FORMAT(B.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND,
								LEFT(B.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART, LEFT(B.RESERVATIONTIMEEND, 5) AS RESERVATIONTIMEEND,
								B.CUSTOMERNAME, B.CUSTOMERCONTACT, B.CUSTOMEREMAIL, B.HOTELNAME, B.PICKUPLOCATION, B.DROPOFFLOCATION,
								B.NUMBEROFADULT, B.NUMBEROFCHILD, B.NUMBEROFINFANT, B.BOOKINGCODE, B.IDRESERVATIONTYPE,
								B.IDRESERVATION, A.INVOICENUMBER, DATE_FORMAT(A.INVOICEDATE, '%d %b %Y') AS INVOICEDATE,
								A.TOTALINVOICEAMOUNT, CONCAT('".URL_RESEVATION_INVOICE_FILE."', A.INVOICEFILE) AS URLINVOICE
						FROM t_reservationinvoicerecap A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
						LEFT JOIN m_reservationtype D ON B.IDRESERVATIONTYPE = D.IDRESERVATIONTYPE
						WHERE ".$con_keyword." AND A.INVOICEDATE BETWEEN '".$startDate."' AND '".$endDate."'
						ORDER BY B.RESERVATIONDATESTART, B.CUSTOMERNAME, B.RESERVATIONTITLE";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDRESERVATIONINVOICERECAP", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();		
	}
}