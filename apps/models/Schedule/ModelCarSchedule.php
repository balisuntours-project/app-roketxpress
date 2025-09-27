<?php
class ModelCarSchedule extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataCar($idVendorCar, $idCarType, $searchKeyword){
		$con_idVendorCar	=	isset($idVendorCar) && $idVendorCar != "" ? "A.IDVENDOR = ".$idVendorCar : "1=1";
		$con_idCarType		=	isset($idCarType) && $idCarType != "" ? "A.IDCARTYPE = ".$idCarType : "1=1";
		$con_searchKeyword	=	isset($searchKeyword) && $searchKeyword != "" ? "(A.BRAND LIKE '%".$searchKeyword."%' OR A.MODEL LIKE '%".$searchKeyword."%' OR A.PLATNUMBER LIKE '%".$searchKeyword."%' OR A.COLOR LIKE '%".$searchKeyword."%')" : "1=1";
		$baseQuery			=	"SELECT A.IDVENDOR, B.NAME AS VENDORNAME, C.CARTYPE, A.BRAND, A.MODEL, A.PLATNUMBER, IFNULL(D.NAME, '-') AS DRIVERNAME, A.IDCARTYPE,
										IF(A.TRANSMISSION = 1, 'Manual', 'Matic') AS TRANSMISSION, A.IDCARVENDOR
								 FROM t_carvendor A
								 LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
								 LEFT JOIN m_cartype C ON A.IDCARTYPE = C.IDCARTYPE
								 LEFT JOIN m_driver D ON A.IDDRIVER = D.IDDRIVER
								 WHERE A.STATUS = 1 AND ".$con_idVendorCar." AND ".$con_idCarType." AND ".$con_searchKeyword."
								 ORDER BY B.NAME, C.CARTYPE, A.BRAND, A.MODEL, A.PLATNUMBER";
		$query				=	$this->db->query($baseQuery);
		$result				=	$query->result();
		
		if(!$result) return false;		
		return $result;
	}
	
	public function getDataCarStatistic($yearMonth){
		$baseQuery	=	sprintf(
							"SELECT COUNT(A.IDRESERVATIONDETAILS) AS TOTALRESERVATION,
									SUM(IF(C.IDRESERVATIONDETAILS IS NULL, 1, 0)) AS TOTALRESERVATIONUNSCHEDULED
							FROM t_reservationdetails A
							LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
							LEFT JOIN t_schedulecar C ON A.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
							WHERE LEFT(A.SCHEDULEDATE, 7) = '".$yearMonth."' AND A.IDCARTYPE != 0
							GROUP BY LEFT(A.SCHEDULEDATE, 7)"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return array(
				"TOTALRESERVATION"				=>0,
				"TOTALRESERVATIONUNSCHEDULED"	=>0
			);
		}
		
		return $row;
	}

	public function getDataUnScheduleCar($yearMonth, $idCarType, $idCarVendor, $idVendor){
		$baseQuery	=	sprintf(
							"SELECT A.IDRESERVATIONDETAILS, LEFT(C.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART, B.PRODUCTTYPE,
									C.RESERVATIONTITLE, A.PRODUCTNAME, A.NOTES, C.CUSTOMERNAME, D.CARTYPE, A.IDCARTYPE, A.DURATION,
									DATE_FORMAT(A.SCHEDULEDATE, '%s') AS SCHEDULEDATE, DATE_FORMAT(A.SCHEDULEDATE, '%s') AS SCHEDULEDATESTR,
									C.BOOKINGCODE, F.SOURCENAME
							FROM t_reservationdetails A
							LEFT JOIN m_producttype B ON A.IDPRODUCTTYPE = B.IDPRODUCTTYPE
							LEFT JOIN t_reservation C ON A.IDRESERVATION = C.IDRESERVATION
							LEFT JOIN m_cartype D ON A.IDCARTYPE = D.IDCARTYPE
							LEFT JOIN t_schedulecar E ON A.IDRESERVATIONDETAILS = E.IDRESERVATIONDETAILS
							LEFT JOIN m_source F ON C.IDSOURCE = F.IDSOURCE
							WHERE A.IDVENDOR = ".$idVendor." AND LEFT(A.SCHEDULEDATE, 7) = '".$yearMonth."' AND A.IDCARTYPE = ".$idCarType." AND E.IDRESERVATIONDETAILS IS NULL
							GROUP BY A.IDRESERVATIONDETAILS
							ORDER BY A.SCHEDULEDATE, C.RESERVATIONTIMESTART, C.CUSTOMERNAME"
							, "%d %b %Y"
							, "%d-%m-%Y"
						); 
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}
	
	public function getDataCarList($scheduleDate, $idCarType, $idVendor){
		$baseQuery	=	sprintf(
							"SELECT A.IDCARVENDOR, IFNULL(COUNT(B.IDSCHEDULECAR), 0) AS TOTALSCHEDULE,
									CONCAT(C.NAME, ' - ', A.BRAND, ' ', A.MODEL, ' - ', A.PLATNUMBER, ' [', IF(A.TRANSMISSION = 1, 'Manual', 'Matic'), ']') AS CARNAME
							 FROM t_carvendor A
							 LEFT JOIN (SELECT BA.IDCARVENDOR, BA.IDSCHEDULECAR FROM t_schedulecar BA
										LEFT JOIN t_reservationdetails BB ON BA.IDRESERVATIONDETAILS = BB.IDRESERVATIONDETAILS
										LEFT JOIN t_reservation BC ON BB.IDRESERVATION = BC.IDRESERVATION
										WHERE BB.SCHEDULEDATE = '".$scheduleDate."'
										) AS B ON A.IDCARVENDOR = B.IDCARVENDOR
							 LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
							 WHERE A.IDCARTYPE = ".$idCarType." AND A.IDVENDOR = ".$idVendor."
							 GROUP BY A.IDCARVENDOR
							 ORDER BY NAME"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}
	
	public function getDataCarSchedule($yearMonth){
		$baseQuery	=	sprintf(
							"SELECT A.IDCARVENDOR, RIGHT(DATE(A.DATETIMESTART), 2) AS SCHEDULEDATE, LEFT(TIME(A.DATETIMESTART), 5) AS RESERVATIONTIMESTART,
									A.IDSCHEDULECAR, B.IDRESERVATION, C.BOOKINGCODE, B.DURATION, A.DATETIMESTART, A.DATETIMEEND, C.CUSTOMERNAME
							FROM t_schedulecar A
							LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
							LEFT JOIN t_reservation C ON B.IDRESERVATION = C.IDRESERVATION
							WHERE LEFT(A.DATETIMESTART, 7) = '".$yearMonth."'
							ORDER BY A.IDCARVENDOR, B.SCHEDULEDATE, A.DATETIMESTART, B.IDRESERVATION"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}
	
	public function getDataCarDayOff($yearMonth){
		$baseQuery	=	"SELECT A.IDCARVENDOR, DATE_FORMAT(B.DATETIMESTART, '%H:%i') AS TIMESTART, RIGHT(A.DATEDAYOFF, 2) AS DAYOFFDATE, A.IDDAYOFF, B.DURATIONHOUR, C.DAYOFFTYPE, B.DATETIMESTART, B.DATETIMEEND, A.REASON
						FROM t_dayoff A
						LEFT JOIN t_dayoffcardetail B ON A.IDDAYOFF = B.IDDAYOFF
						LEFT JOIN m_cardayofftype C ON B.IDCARDAYOFFTYPE = C.IDCARDAYOFFTYPE
						WHERE LEFT(A.DATEDAYOFF, 7) = '".$yearMonth."' AND A.IDCARVENDOR != 0
						ORDER BY A.IDCARVENDOR, A.DATEDAYOFF";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}
	
	public function getDataReservationSchedule($yearMonth, $idSource, $dateSchedule, $bookingCode, $searchKeyword){
		$con_idSource		=	isset($idSource) && $idSource != "" ? "C.IDSOURCE = ".$idSource : "1=1";
		$con_dateSchedule	=	isset($dateSchedule) && $dateSchedule != "" ? "A.SCHEDULEDATE = '".$dateSchedule."'" : "1=1";
		$con_bookingCode	=	isset($bookingCode) && $bookingCode != "" ? "C.BOOKINGCODE LIKE '%".$bookingCode."%'" : "1=1";
		$con_searchKeyword	=	isset($searchKeyword) && $searchKeyword != "" ? "(C.CUSTOMERNAME LIKE '%".$searchKeyword."%' OR C.RESERVATIONTITLE LIKE '%".$searchKeyword."%' OR A.PRODUCTNAME LIKE '%".$searchKeyword."%' OR A.NOTES LIKE '%".$searchKeyword."%')" : "1=1";
		$baseQuery			=	"SELECT A.IDRESERVATIONDETAILS, LEFT(C.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART, B.PRODUCTTYPE, C.RESERVATIONTITLE, A.PRODUCTNAME, A.NOTES,
										C.CUSTOMERNAME, D.CARTYPE, A.IDCARTYPE, A.DURATION, DATE_FORMAT(A.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATE, DATE_FORMAT(A.SCHEDULEDATE, '%d-%m-%Y') AS SCHEDULEDATESTR,
										IFNULL(DATE_FORMAT(E.DATETIMESTART, '%d %b %Y [%H:%i]'), '-') AS DATETIMESTARTSTR, IFNULL(DATE_FORMAT(E.DATETIMEEND, '%d %b %Y [%H:%i]'), '-') AS DATETIMEENDSTR,
										IFNULL(CONCAT(G.NAME, ' - ', F.BRAND, ' ', F.MODEL, ' - ', F.PLATNUMBER, ' [', IF(F.TRANSMISSION = 1, 'Manual', 'Matic'), ']'), '-') AS CARNAME,
										IFNULL(E.IDCARVENDOR, 0) AS IDCARVENDOR, G.NAME AS VENDORNAME, F.BRAND, F.MODEL, F.PLATNUMBER, E.IDSCHEDULECAR, C.BOOKINGCODE, H.SOURCENAME, A.IDVENDOR
								FROM t_reservationdetails A
								LEFT JOIN m_producttype B ON A.IDPRODUCTTYPE = B.IDPRODUCTTYPE
								LEFT JOIN t_reservation C ON A.IDRESERVATION = C.IDRESERVATION
								LEFT JOIN m_cartype D ON A.IDCARTYPE = D.IDCARTYPE
								LEFT JOIN t_schedulecar E ON A.IDRESERVATIONDETAILS = E.IDRESERVATIONDETAILS
								LEFT JOIN t_carvendor F ON E.IDCARVENDOR = F.IDCARVENDOR
								LEFT JOIN m_vendor G ON F.IDVENDOR = G.IDVENDOR
								LEFT JOIN m_source H ON C.IDSOURCE = H.IDSOURCE
								WHERE LEFT(A.SCHEDULEDATE, 7) = '".$yearMonth."' AND A.IDCARTYPE != 0 AND ".$con_idSource." AND ".$con_dateSchedule." AND ".$con_bookingCode." AND ".$con_searchKeyword."
								GROUP BY A.IDRESERVATIONDETAILS
								ORDER BY A.SCHEDULEDATE, C.RESERVATIONTIMESTART, C.CUSTOMERNAME";
		$query				=	$this->db->query($baseQuery);
		$result				=	$query->result();
		
		if(!$result) return false;
		return $result;
	}

	public function getDetailReservationSchedule($idReservationDetails){
		$query	=	$this->db->query(
						"SELECT A.IDRESERVATION, A.IDVENDOR, A.SCHEDULEDATE, B.RESERVATIONTIMESTART, B.BOOKINGCODE, B.CUSTOMERNAME, A.PRODUCTNAME
						FROM t_reservationdetails A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						WHERE A.IDRESERVATIONDETAILS = '".$idReservationDetails."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}

	public function getAllScheduleReservation($idReservation, $idVendor){
		$query	=	$this->db->query(
						"SELECT A.IDRESERVATIONDETAILS, A.SCHEDULEDATE, IFNULL(B.DATETIMEEND, '') AS DATETIMEEND, A.DURATION
						FROM t_reservationdetails A
						LEFT JOIN t_schedulecar B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						WHERE A.IDRESERVATION = '".$idReservation."' AND A.IDVENDOR = ".$idVendor." AND A.IDPRODUCTTYPE = 3
						GROUP BY A.SCHEDULEDATE
						ORDER BY A.SCHEDULEDATE"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return false;		
	}

	public function isScheduleAvailable($idCarVendor, $dateTimeStart, $dateTimeEnd){
		$query	=	$this->db->query(
						"SELECT IDSCHEDULECAR FROM t_schedulecar
						WHERE IDCARVENDOR = '".$idCarVendor."' AND ('".$dateTimeStart."' BETWEEN DATETIMESTART AND DATETIMEEND OR '".$dateTimeEnd."' BETWEEN DATETIMESTART AND DATETIMEEND)
						LIMIT 1"
					);
		$row	=	$query->row_array();
		
		if(isset($row)) return false;
		return true;
	}

	public function isDayOffConflict($idCarVendor, $dateTimeStart, $dateTimeEnd){
		$query	=	$this->db->query(
						"SELECT A.IDDAYOFFCARDETAIL FROM t_dayoffcardetail A
						LEFT JOIN t_dayoff B ON A.IDDAYOFF = B.IDDAYOFF
						WHERE B.IDCARVENDOR = '".$idCarVendor."' AND ('".$dateTimeStart."' BETWEEN A.DATETIMESTART AND A.DATETIMEEND OR '".$dateTimeEnd."' BETWEEN A.DATETIMESTART AND A.DATETIMEEND)
						LIMIT 1"
					);
		$row	=	$query->row_array();
		
		if(isset($row)) return true;
		return false;
	}

	public function getDetailSchedule($idCarSchedule, $idReservationDetails){
		$condition	=	isset($idCarSchedule) && $idCarSchedule != 0 && $idCarSchedule != "" ? "C.IDSCHEDULECAR = '".$idCarSchedule."'" : "B.IDRESERVATIONDETAILS = '".$idReservationDetails."'";
		$query		=	$this->db->query(
							"SELECT C.IDSCHEDULECAR, B.DURATION, B.PRODUCTNAME, B.NOTES, B.SCHEDULEDATE AS SCHEDULEDATEDB, D.SOURCENAME, A.RESERVATIONTITLE,
									DATE_FORMAT(B.SCHEDULEDATE, '%d %M %Y') AS SCHEDULEDATE, SUBSTRING(A.RESERVATIONTIMESTART, 1, 5) AS RESERVATIONTIMESTART,
									A.CUSTOMERNAME, A.CUSTOMERCONTACT, IF(A.CUSTOMEREMAIL IS NULL OR A.CUSTOMEREMAIL = '', '-', A.CUSTOMEREMAIL) AS CUSTOMEREMAIL,
									IF(A.HOTELNAME IS NULL OR A.HOTELNAME = '', '-', A.HOTELNAME) AS HOTELNAME,
									IF(A.PICKUPLOCATION IS NULL OR A.PICKUPLOCATION = '', '-', A.PICKUPLOCATION) AS PICKUPLOCATION,
									IF(A.DROPOFFLOCATION IS NULL OR A.DROPOFFLOCATION = '', '-', A.DROPOFFLOCATION) AS DROPOFFLOCATION,
									A.NUMBEROFADULT, A.NUMBEROFCHILD, A.NUMBEROFINFANT, A.REMARK, A.TOURPLAN, C.IDCARVENDOR, E.IDVENDOR,
									CONCAT('[', F.NAME, '] ', E.BRAND, ' ', E.MODEL, ' - ', E.PLATNUMBER) AS CARDETAIL, A.IDRESERVATION,
									DATE_FORMAT(C.DATETIMESTART, '%d %b %Y %H:%i') AS DATETIMESTART, DATE_FORMAT(C.DATETIMEEND, '%d %b %Y %H:%i') AS DATETIMEEND
							FROM t_reservation A
							LEFT JOIN t_reservationdetails B ON A.IDRESERVATION = B.IDRESERVATION
							LEFT JOIN t_schedulecar C ON B.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
							LEFT JOIN m_source D ON A.IDSOURCE = D.IDSOURCE
							LEFT JOIN t_carvendor E ON C.IDCARVENDOR = E.IDCARVENDOR
							LEFT JOIN m_vendor F ON E.IDVENDOR = F.IDVENDOR
							LEFT JOIN m_cartype G ON E.IDCARTYPE = G.IDCARTYPE
							WHERE ".$condition."
							LIMIT 1"
						);
		$row		=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}

	public function getDetailDayOff($idDayoff){
		$query	=	$this->db->query(
						"SELECT A.IDDAYOFFREQUEST, C.NAME AS VENDORNAME, CONCAT(B.BRAND, ' ', B.MODEL, ' - ', B.PLATNUMBER) AS CARDETAIL, E.DAYOFFTYPE,
								DATE_FORMAT(D.DATETIMESTART, '%d %b %Y %H:%i') AS DATETIMESTART, DATE_FORMAT(D.DATETIMEEND, '%d %b %Y %H:%i') AS DATETIMEEND,
								D.DURATIONHOUR, DATE_FORMAT(F.DATETIMEAPPROVAL, '%d %b %Y %H:%i') AS DATETIMEAPPROVAL, F.USERAPPROVAL,
								DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUTINPUT, A.REASON
						FROM t_dayoff A
						LEFT JOIN t_carvendor B ON A.IDCARVENDOR = B.IDCARVENDOR
						LEFT JOIN m_vendor C ON B.IDVENDOR = C.IDVENDOR
						LEFT JOIN t_dayoffcardetail D ON A.IDDAYOFF = D.IDDAYOFF
						LEFT JOIN m_cardayofftype E ON D.IDCARDAYOFFTYPE = E.IDCARDAYOFFTYPE
						LEFT JOIN t_dayoffrequest F ON A.IDDAYOFFREQUEST = F.IDDAYOFFREQUEST
						WHERE A.IDDAYOFF = '".$idDayoff."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
}