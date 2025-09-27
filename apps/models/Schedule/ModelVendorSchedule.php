<?php
class ModelVendorSchedule extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataVendorSchedule($scheduleDate, $idVendor, $confirmationStatus){
		
		$con_vendor			=	isset($idVendor) && $idVendor != 0 ? "A.IDVENDOR = ".$idVendor : "1=1";
		$con_confirmStatus	=	isset($confirmationStatus) && $confirmationStatus != "" ? "B.STATUSCONFIRM = ".$confirmationStatus : "1=1";
		$baseQuery			=	sprintf("SELECT A.IDVENDOR, C.IDRESERVATION, C.RESERVATIONTITLE, C.CUSTOMERNAME, D.SOURCENAME, C.BOOKINGCODE, C.RESERVATIONTIMESTART,
												C.HOTELNAME, IF(C.PICKUPLOCATION IS NULL OR C.PICKUPLOCATION = '', '-', C.PICKUPLOCATION) AS PICKUPLOCATION,
												IF(C.DROPOFFLOCATION IS NULL OR C.DROPOFFLOCATION = '', '-', C.DROPOFFLOCATION) AS DROPOFFLOCATION,
												GROUP_CONCAT(A.PRODUCTNAME, '|', E.PAXADULT, '|', E.PAXCHILD, '|', E.PAXINFANT, '|', F.STATUSCONFIRM, '|',
												IFNULL(G.STATUSPROCESSNAME, '-'), '|', F.STATUSPROCESS, '|', B.IDRESERVATIONDETAILS, '|', LEFT(C.RESERVATIONTIMESTART, 5), '|',
												LEFT(B.TIMEBOOKING, 5), '|', LEFT(B.TIMESCHEDULE, 5), '|', F.IDSCHEDULEVENDOR SEPARATOR '~') AS PACKAGELIST
										 FROM t_reservationdetails A
										 LEFT JOIN t_schedulevendor B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
										 LEFT JOIN t_reservation C ON A.IDRESERVATION = C.IDRESERVATION
										 LEFT JOIN m_source D ON C.IDSOURCE = D.IDSOURCE
										 LEFT JOIN t_reservationdetailsticket E ON A.IDRESERVATIONDETAILS = E.IDRESERVATIONDETAILS
										 LEFT JOIN t_schedulevendor F ON A.IDRESERVATIONDETAILS = F.IDRESERVATIONDETAILS AND F.IDSCHEDULEVENDOR IS NOT NULL
										 LEFT JOIN m_statusprocessvendor G ON F.STATUSPROCESS = G.IDSTATUSPROCESSVENDOR
										 WHERE A.IDVENDOR != 0 AND A.IDPRODUCTTYPE = 1 AND ".$con_vendor." AND A.SCHEDULEDATE = '".$scheduleDate."' AND ".$con_confirmStatus." AND F.IDRESERVATIONDETAILS IS NOT NULL
										 GROUP BY A.IDRESERVATION, A.IDVENDOR
										 ORDER BY A.IDVENDOR, C.RESERVATIONTIMESTART"
								);
		$query				=	$this->db->query($baseQuery);
		$result				=	$query->result();
		
		if(!$result){
			return false;
		}
		
		return $result;
	
	}

	public function getDataActiveVendorSchedule($strArrIdVendor){
		
		$baseQuery	=	sprintf("SELECT IDVENDOR, NAME, 0 AS TOTALRESERVATION, '' AS ARRRESERVATION
								 FROM m_vendor
								 WHERE IDVENDOR IN (".$strArrIdVendor.")
								 ORDER BY NAME"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result){
			return false;
		}
		
		return $result;
	
	}
	
	public function getDetailReservation($idReservation){

		$query	=	$this->db->query("SELECT C.SOURCENAME, A.RESERVATIONTITLE, B.PRODUCTNAME, DATE_FORMAT(B.SCHEDULEDATE, '%d %M %Y') AS SCHEDULEDATE,
											B.SCHEDULEDATE AS SCHEDULEDATEDB, SUBSTRING(A.RESERVATIONTIMESTART, 1, 5) AS RESERVATIONTIMESTART, A.CUSTOMERNAME, A.CUSTOMERCONTACT,
											A.CUSTOMEREMAIL, A.HOTELNAME, IF(A.PICKUPLOCATION IS NULL OR A.PICKUPLOCATION = '', '-', A.PICKUPLOCATION) AS PICKUPLOCATION,
											IF(A.DROPOFFLOCATION IS NULL OR A.DROPOFFLOCATION = '', '-', A.DROPOFFLOCATION) AS DROPOFFLOCATION,
											IF(A.REMARK IS NULL OR A.REMARK = '', '-', A.REMARK) AS REMARK,
											GROUP_CONCAT(
												B.PRODUCTNAME, '|', D.PAXADULT, '|', D.PAXCHILD, '|', D.PAXINFANT, '|', E.STATUSCONFIRM, '|', IFNULL(F.STATUSPROCESSNAME, '-'), '|',
												E.STATUSPROCESS, '|', LEFT(E.TIMESCHEDULE, 5), '|', B.IDRESERVATIONDETAILS, '|', LEFT(E.TIMEBOOKING, 5), '|', LEFT(E.IDSCHEDULEVENDOR, 5)
												SEPARATOR '~'
											) AS PACKAGELIST
									FROM t_reservation A
									LEFT JOIN t_reservationdetails B ON A.IDRESERVATION = B.IDRESERVATION
									LEFT JOIN m_source C ON A.IDSOURCE = C.IDSOURCE
									LEFT JOIN t_reservationdetailsticket D ON B.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS
									LEFT JOIN t_schedulevendor E ON B.IDRESERVATIONDETAILS = E.IDRESERVATIONDETAILS AND E.IDSCHEDULEVENDOR IS NOT NULL
									LEFT JOIN m_statusprocessvendor F ON E.STATUSPROCESS = F.IDSTATUSPROCESSVENDOR
									WHERE A.IDRESERVATION = ".$idReservation."
									GROUP BY A.IDRESERVATION
									LIMIT 1");
		$row	=	$query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	public function getDetailReservationDetails($idReservationDetails){

		$query	=	$this->db->query("SELECT B.IDVENDOR, C.SOURCENAME, A.BOOKINGCODE, A.RESERVATIONTITLE, A.CUSTOMERNAME, A.CUSTOMERCONTACT, A.CUSTOMEREMAIL,
											 A.NUMBEROFADULT, A.NUMBEROFCHILD, A.NUMBEROFINFANT, DATE_FORMAT(B.SCHEDULEDATE, '%d %M %Y') AS SCHEDULEDATE, B.PRODUCTNAME,
											 B.NOMINAL, B.NOTES, IFNULL(D.CORRECTIONNOTES, '') AS CORRECTIONNOTES
									FROM t_reservation A
									LEFT JOIN t_reservationdetails B ON A.IDRESERVATION = B.IDRESERVATION
									LEFT JOIN m_source C ON A.IDSOURCE = C.IDSOURCE
									LEFT JOIN t_fee D ON B.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS
									WHERE B.IDVENDOR != 0 AND B.IDRESERVATIONDETAILS = ".$idReservationDetails."
									LIMIT 1");
		$row	=	$query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	public function getDetailReservationTicket($idReservationDetails){

		$query	=	$this->db->query("SELECT PAXADULT, PAXCHILD, PAXINFANT, PRICEPERPAXADULT, PRICEPERPAXCHILD, PRICEPERPAXINFANT, PRICETOTALADULT, PRICETOTALCHILD, PRICETOTALINFANT
									FROM t_reservationdetailsticket
									WHERE IDRESERVATIONDETAILS = ".$idReservationDetails."
									LIMIT 1");
		$row	=	$query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return array(
			"PAXADULT"			=>	0,
			"PAXCHILD"			=>	0,
			"PAXINFANT"			=>	0,
			"PRICEPERPAXADULT"	=>	0,
			"PRICEPERPAXCHILD"	=>	0,
			"PRICEPERPAXINFANT"	=>	0,
			"PRICETOTALADULT"	=>	0,
			"PRICETOTALCHILD"	=>	0,
			"PRICETOTALINFANT"	=>	0
		);
		
	}

	public function getDetailFeeVendor($idVendor, $idReservationDetails){
		
		$baseQuery	=	"SELECT IDFEE, IDWITHDRAWALRECAP FROM t_fee
						WHERE IDRESERVATIONDETAILS = ".$idReservationDetails." AND IDVENDOR = ".$idVendor."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return false;
		}
		
		return $row;
		
	}

	public function getDetailNotificationSchedule($idReservationDetails){
		
		$query	= $this->db->query("SELECT A.IDVENDOR, B.TITLE, B.MESSAGE, DATE_FORMAT(C.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATE, C.PRODUCTNAME
									FROM t_schedulevendor A
									LEFT JOIN t_messagepartner B ON A.IDRESERVATIONDETAILS = B.IDPRIMARY AND A.IDVENDOR = B.IDPARTNER AND B.IDPARTNERTYPE = 1 AND IDMESSAGEPARTNERTYPE = 1
									LEFT JOIN t_reservationdetails C ON A.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
									WHERE A.IDRESERVATIONDETAILS = '".$idReservationDetails."'
									LIMIT 1");
		$row	= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
}