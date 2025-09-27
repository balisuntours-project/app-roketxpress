<?php
class ModelReservationDetail extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataReservationDetail($page, $dataPerPage= 25, $reservationType, $arrDates, $startDate, $endDate){
		
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_or_date		=	"";
		$con_reservationType=	!isset($reservationType) || $reservationType == "" ? "1=1" : "A.IDRESERVATIONTYPE = ".$reservationType;
		
		foreach($arrDates as $date){
			$con_or_date	.=	" OR '".$date."' BETWEEN A.RESERVATIONDATESTART AND A.RESERVATIONDATEEND";
		}
		
		$con_date			=	"(
								   A.RESERVATIONDATESTART BETWEEN '".$startDate."' AND '".$endDate."' OR
								   A.RESERVATIONDATEEND BETWEEN '".$startDate."' AND '".$endDate."'
								   ".$con_or_date."
								  )";
		
		$baseQuery			=	"SELECT A.IDRESERVATION, A.IDRESERVATIONTYPE, C.RESERVATIONTYPE, B.SOURCENAME, A.INPUTTYPE,
										A.RESERVATIONTITLE, A.DURATIONOFDAY, DATE_FORMAT(A.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
										DATE_FORMAT(A.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND, LEFT(A.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART,
										LEFT(A.RESERVATIONTIMEEND, 5) AS RESERVATIONTIMEEND, A.CUSTOMERNAME, A.BOOKINGCODE,
										'' AS CARSCHEDULE, '' AS DRIVERSCHEDULE, '' AS TICKETLIST
								FROM t_reservation A
								LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
								LEFT JOIN m_reservationtype C ON A.IDRESERVATIONTYPE = C.IDRESERVATIONTYPE
								WHERE ".$con_reservationType." AND ".$con_date."
								ORDER BY C.RESERVATIONTYPE, A.RESERVATIONDATESTART";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDRESERVATION", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
	}
		
	public function getDataCarSchedule($idReservation){
		
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
		
		if(!isset($result)){
			return false;
		}
		
		return $result;
		
	}
		
	public function getDataDriverSchedule($idReservation){
		
		$baseQuery	=	"SELECT D.DRIVERTYPE, B.NAME, DATE_FORMAT(C.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATE, C.PRODUCTNAME
						FROM t_scheduledriver A
						LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
						LEFT JOIN t_reservationdetails C ON A.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
						LEFT JOIN m_drivertype D ON B.IDDRIVERTYPE = D.IDDRIVERTYPE
						WHERE C.IDRESERVATION = ".$idReservation." AND C.STATUS = 1
						ORDER BY C.SCHEDULEDATE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!isset($result)){
			return false;
		}
		
		return $result;
		
	}
		
	public function getDataTicketList($idReservation){
		
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
		
		if(!isset($result)){
			return false;
		}
		
		return $result;
		
	}
	
}