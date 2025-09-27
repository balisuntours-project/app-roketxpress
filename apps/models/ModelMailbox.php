<?php
class ModelMailbox extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataMailbox($page, $dataPerPage, $status, $idReservationType, $idSource, $startDate, $endDate, $reservationDate, $searchKeyword){
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$dataPerPage		=	!isset($dataPerPage) || $dataPerPage == 0 ? 25 : $dataPerPage;
		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_status			=	!isset($status) || $status == "" ? "1=1" : "A.STATUS = ".$status;
		$con_reservationType=	!isset($idReservationType) || $idReservationType == "" ? "1=1" : "A.IDRESERVATIONTYPE = ".$idReservationType;
		$con_source			=	!isset($idSource) || $idSource == "" ? "1=1" : "A.IDSOURCE = ".$idSource;
		$con_reservationDate=	!isset($reservationDate) || $reservationDate == "" ? "1=1" : "A.RESERVATIONDATE = '".$reservationDate."'";
		$con_mailDate		=	"DATE(A.DATETIMEMAIL) BETWEEN '".$startDate."' AND '".$endDate."'";
		$con_searchKeyword	=	!isset($searchKeyword) || $searchKeyword == "" ? "1=1" : "(A.MAILSUBJECT LIKE '%".$searchKeyword."%' OR A.RESERVATIONTITLE LIKE '%".$searchKeyword."%' OR A.CUSTOMERNAME LIKE '%".$searchKeyword."%' OR A.BOOKINGCODE LIKE '%".$searchKeyword."%')";
		
		if($status == 0 && $status != "") $con_mailDate	=	"1=1";		
		$baseQuery			=	"SELECT IFNULL(C.RESERVATIONTYPE, 'Not Set') AS RESERVATIONTYPE, B.MAILSENDERNAME AS SOURCE, DATE_FORMAT(A.DATETIMEMAIL, '%d %b %Y %H:%i') AS DATETIMEMAIL,
										DATE_FORMAT(A.RESERVATIONDATE, '%d %b %Y') AS RESERVATIONDATE, A.MAILSUBJECT, A.STATUS, A.IDMAILBOX
								FROM t_mailbox A
								LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
								LEFT JOIN m_reservationtype C ON A.IDRESERVATIONTYPE = C.IDRESERVATIONTYPE
								WHERE ".$con_status." AND ".$con_reservationType." AND ".$con_source." AND ".$con_mailDate." AND ".$con_reservationDate." AND ".$con_searchKeyword."
								ORDER BY A.DATETIMEMAIL DESC";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDMAILBOX", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}
	
	public function getDetailMailbox($idMailbox){
		$query	=	$this->db->query(
						"SELECT MAILSUBJECT, DUPLICATENUMBER, DATE_FORMAT(DATETIMEMAIL, '%d %b %Y %H:%i') AS DATETIMEMAIL,
								CONCAT('".URL_MAIL_PREVIEW."', HTMLFILENAME) AS URLPREVIEW, RESERVATIONTITLE, DURATIONOFDAY,
								DATE_FORMAT(RESERVATIONDATE, '%d-%m-%Y') AS RESERVATIONDATE, DATE_FORMAT(RESERVATIONDATEEND, '%d-%m-%Y') AS RESERVATIONDATEEND,
								SUBSTRING(RESERVATIONTIME, 1, 2) AS RESERVATIONHOUR, SUBSTRING(RESERVATIONTIME, 4, 2) AS RESERVATIONMINUTE,
								SUBSTRING(RESERVATIONTIMEEND, 1, 2) AS RESERVATIONHOUREND, SUBSTRING(RESERVATIONTIMEEND, 4, 2) AS RESERVATIONMINUTEEND,
								CUSTOMERNAME, CUSTOMERCONTACT, CUSTOMEREMAIL, IDAREA, HOTELNAME, PICKUPLOCATION, DROPOFFLOCATION,
								NUMBEROFADULT, NUMBEROFCHILD, NUMBEROFINFANT, BOOKINGCODE, INCOMEAMOUNTCURRENCY,
								SUBSTRING_INDEX(SUBSTRING_INDEX(INCOMEAMOUNT, '.', 1), '.', -1) AS INCOMEAMOUNTINTEGER,
								SUBSTRING_INDEX(SUBSTRING_INDEX(INCOMEAMOUNT, '.', 2), '.', -1) AS INCOMEAMOUNTDECIMAL,
								REMARK, TOURPLAN, ADDITIONALINFOLIST, URLDETAILPRODUCT, URLPICKUPLOCATION, STATUS, ISSELFDRIVE, USEREDITOR,
								DATE_FORMAT(DATETIMEVALIDATION, '%d %b %Y %H:%i') AS DATETIMEVALIDATION, IDMAILBOX, IDSOURCE, IDRESERVATIONTYPE
						FROM t_mailbox
						WHERE IDMAILBOX = '".$idMailbox."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function isMailValidated($idMailbox){
		$query	=	$this->db->query(
						"SELECT USEREDITOR, DATE_FORMAT(DATETIMEVALIDATION, '%d %b %Y %H:%i') AS DATETIMEVALIDATION
						FROM t_mailbox
						WHERE IDMAILBOX = '".$idMailbox."' AND STATUS = 1
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function getTotalUnreadMail(){
		$query	=	$this->db->query(
						"SELECT COUNT(IDMAILBOX) AS TOTALUNREADMAIL
						FROM t_mailbox
						WHERE STATUS = 0"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['TOTALUNREADMAIL'];
		return 0;		
	}
}