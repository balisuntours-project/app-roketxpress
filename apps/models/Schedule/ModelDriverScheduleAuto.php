<?php
class ModelDriverScheduleAuto extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function getDataScheduleAuto($scheduleDate, $jobType, $idArea){
		$con_jobType=	isset($jobType) && $jobType != "" ? "A.IDDRIVERTYPE = ".$jobType : "1=1";
		$con_idArea	=	isset($idArea) && $idArea != "" ? "C.IDAREA = ".$idArea : "1=1";
		$baseQuery	=	sprintf(
							"SELECT A.IDRESERVATIONDETAILS, LEFT(C.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART, C.RESERVATIONTITLE, A.PRODUCTNAME,
									E.DRIVERTYPE, A.JOBTYPE, A.JOBRATE, C.NUMBEROFADULT, C.NUMBEROFCHILD, C.NUMBEROFINFANT, C.HOTELNAME, C.PICKUPLOCATION, D.AREANAME, 
									A.NOTES, C.REMARK, C.IDAREA, A.IDDRIVERTYPE, (C.NUMBEROFADULT + C.NUMBEROFCHILD + C.NUMBEROFINFANT) AS TOTALPAX,
									C.CUSTOMERNAME, C.TOURPLAN
							FROM t_reservationdetails A
							LEFT JOIN m_producttype B ON A.IDPRODUCTTYPE = B.IDPRODUCTTYPE
							LEFT JOIN t_reservation C ON A.IDRESERVATION = C.IDRESERVATION
							LEFT JOIN m_area D ON C.IDAREA = D.IDAREA
							LEFT JOIN m_drivertype E ON A.IDDRIVERTYPE = E.IDDRIVERTYPE
							LEFT JOIN t_scheduledriver F ON A.IDRESERVATIONDETAILS = F.IDRESERVATIONDETAILS
							WHERE ".$con_jobType." AND ".$con_idArea." AND A.SCHEDULEDATE = '".$scheduleDate."' AND A.IDDRIVERTYPE != 0 AND A.STATUS = 1 AND A.SCHEDULETYPE = 1 AND
								  F.IDRESERVATIONDETAILS IS NULL AND A.JOBTYPE > 0 AND C.IDAREA > 0
							GROUP BY A.IDRESERVATIONDETAILS
							ORDER BY FIELD(A.IDDRIVERTYPE, 2, 3, 1, 0), A.JOBRATE DESC, D.AREANAME, A.PRODUCTRANK, TOTALPAX"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;
	}
	
	public function getDataDriverList($scheduleDate, $scheduleDateYesterday, $driverType, $idArea){
		$con_idArea		=	isset($idArea) && $idArea != "" ? "DA.IDAREA = ".$idArea : "1=1";
		$con_driverType	=	isset($driverType) && $driverType != "" ? "A.IDDRIVERTYPE = ".$driverType : "1=1";
		$baseQuery		=	sprintf(
								"SELECT A.IDDRIVER, A.IDDRIVERTYPE, A.IDCARCAPACITY, IF(A.PARTNERSHIPTYPE = 1, B.DRIVERTYPE, IF(A.PARTNERSHIPTYPE = 4, 'Office', 'Freelance')) AS DRIVERTYPE, A.NAME AS DRIVERNAME,
										A.RANKNUMBER, C.MAXCAPACITY, IFNULL(CONCAT(C.CARCAPACITYNAME, ' : (', C.MINCAPACITY, '-', C.MAXCAPACITY, ')'), '-') AS CARCAPACITY,
										IFNULL(GROUP_CONCAT(E.AREANAME ORDER BY D.ORDERNUMBER SEPARATOR ' | '), '-') AS AREAPRIORITY,
										IFNULL(A.LASTJOBRATE, '') AS LASTJOBRATE, IFNULL(IFNULL(G.PRODUCTNAME, CONCAT('Day Off : ', H.REASON)), 'No Job') AS JOBYESTERDAY,
										IFNULL(G.JOBTYPE, IF(H.IDDAYOFF IS NULL, 0, -1)) AS JOBTYPEYESTERDAY, GROUP_CONCAT(D.IDAREA ORDER BY D.ORDERNUMBER) AS ARRAREA,
										G.IDDRIVERTYPE AS IDDRIVETYPE, '' AS NEXTJOBRATEPRIORITY, A.PARTNERSHIPTYPE, 0 AS TOTALCONSECUTIVENOJOB
								 FROM m_driver A
								 LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
								 LEFT JOIN m_carcapacity C ON A.IDCARCAPACITY = C.IDCARCAPACITY
								 LEFT JOIN t_driverareaorder D ON A.IDDRIVER = D.IDDRIVER
								 LEFT JOIN (SELECT IDDRIVER, IDAREA FROM t_driverareaorder WHERE ORDERNUMBER = 1 GROUP BY IDDRIVER) DA ON A.IDDRIVER = DA.IDDRIVER
								 LEFT JOIN m_area E ON D.IDAREA = E.IDAREA
								 LEFT JOIN t_dayoff F ON A.IDDRIVER = F.IDDRIVER AND F.DATEDAYOFF = '".$scheduleDate."'
								 LEFT JOIN (SELECT GA.IDDRIVER, GB.PRODUCTNAME, GB.JOBTYPE, GB.IDDRIVERTYPE
											FROM t_scheduledriver GA
											LEFT JOIN t_reservationdetails GB ON GA.IDRESERVATIONDETAILS = GB.IDRESERVATIONDETAILS
											WHERE GB.SCHEDULEDATE = '".$scheduleDateYesterday."'
											GROUP BY GA.IDDRIVER
											) G ON A.IDDRIVER = G.IDDRIVER
								 LEFT JOIN t_dayoff H ON A.IDDRIVER = H.IDDRIVER AND H.DATEDAYOFF = '".$scheduleDateYesterday."'
								 LEFT JOIN (SELECT IA.IDDRIVER FROM t_scheduledriver IA
											LEFT JOIN t_reservationdetails IB ON IA.IDRESERVATIONDETAILS = IB.IDRESERVATIONDETAILS
											WHERE IB.SCHEDULEDATE = '".$scheduleDate."'
											GROUP BY IA.IDDRIVER
											) I ON A.IDDRIVER = I.IDDRIVER
								 WHERE A.STATUS = 1 AND F.IDDAYOFF IS NULL AND I.IDDRIVER IS NULL AND A.SCHEDULETYPE = 1 AND ".$con_idArea." AND ".$con_driverType."
								 GROUP BY A.IDDRIVER
								 ORDER BY FIELD(A.PARTNERSHIPTYPE, 4, 1, 2, 3), FIELD(A.IDDRIVERTYPE, 2, 3, 1, 0), A.RANKNUMBER, A.NAME"
							);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result) return false;
		return $result;
	}
	
	public function getDataScheduleManual($scheduleDate){
		
		$baseQuery	=	sprintf("SELECT A.IDRESERVATIONDETAILS, LEFT(C.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART, C.RESERVATIONTITLE, A.PRODUCTNAME,
										E.DRIVERTYPE, A.JOBTYPE, C.NUMBEROFADULT, C.NUMBEROFCHILD, C.NUMBEROFINFANT, C.HOTELNAME, C.PICKUPLOCATION, D.AREANAME, 
										A.NOTES, C.REMARK, C.IDSOURCE, G.SOURCENAME, IFNULL(C.SPECIALREQUEST, '-') AS SPECIALREQUEST, C.DURATIONOFDAY, A.IDDRIVERTYPE,
										C.CUSTOMERNAME, C.TOURPLAN
								FROM t_reservationdetails A
								LEFT JOIN m_producttype B ON A.IDPRODUCTTYPE = B.IDPRODUCTTYPE
								LEFT JOIN t_reservation C ON A.IDRESERVATION = C.IDRESERVATION
								LEFT JOIN m_area D ON C.IDAREA = D.IDAREA
								LEFT JOIN m_drivertype E ON A.IDDRIVERTYPE = E.IDDRIVERTYPE
								LEFT JOIN t_scheduledriver F ON A.IDRESERVATIONDETAILS = F.IDRESERVATIONDETAILS
								LEFT JOIN m_source G ON C.IDSOURCE = G.IDSOURCE
								WHERE A.SCHEDULEDATE = '".$scheduleDate."' AND A.IDDRIVERTYPE != 0 AND A.STATUS = 1 AND A.SCHEDULETYPE = 2 AND
									  F.IDRESERVATIONDETAILS IS NULL
								GROUP BY A.IDRESERVATIONDETAILS
								ORDER BY FIELD(A.IDDRIVERTYPE, 2, 3, 1, 0), A.PRODUCTRANK, C.RESERVATIONTIMESTART, C.NUMBEROFADULT"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result){
			return false;
		}
		
		return $result;
		
	}
	
	public function getTotalDriver($typeDriver){
		
		$baseQuery	=	sprintf("SELECT COUNT(IDDRIVER) AS TOTALDRIVER
								FROM m_driver
								WHERE IDDRIVERTYPE = ".$typeDriver."
								GROUP BY IDDRIVERTYPE"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return 0;
		}
		
		return $row['TOTALDRIVER'];
		
	}
	
	public function getTotalArea(){
		
		$baseQuery	=	sprintf("SELECT COUNT(IDAREA) AS TOTALAREA
								FROM m_area WHERE IDAREA > 0"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return 0;
		}
		
		return $row['TOTALAREA'];
		
	}
	
	public function getArrMaxCapacity(){
		
		$baseQuery	=	sprintf("SELECT GROUP_CONCAT(MAXCAPACITY) AS ARRMAXCAPACITY
								FROM m_carcapacity ORDER BY MAXCAPACITY"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return "";
		}
		
		return $row['ARRMAXCAPACITY'];
		
	}
	
	public function getDetailReservation($idReservationDetails){
		
		$baseQuery	=	sprintf("SELECT D.SCHEDULETYPE, B.DURATIONOFDAY, B.NUMBEROFADULT, B.SPECIALREQUEST, F.UPSELLINGTYPE, G.DRIVERTYPE,
										DATE_FORMAT(A.SCHEDULEDATE, '%s') AS SCHEDULEDATE, B.RESERVATIONTIMESTART, B.RESERVATIONTITLE,
										B.CUSTOMERNAME, A.PRODUCTNAME
								FROM t_reservationdetails A
								LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
								LEFT JOIN m_product C ON A.PRODUCTNAME = C.PRODUCTNAME
								LEFT JOIN t_driverfee D ON C.IDPRODUCT = D.IDPRODUCT AND A.IDDRIVERTYPE = D.IDDRIVERTYPE
								LEFT JOIN t_scheduledriver E ON A.IDRESERVATIONDETAILS = E.IDRESERVATIONDETAILS
								LEFT JOIN m_source F ON B.IDSOURCE = B.IDSOURCE
								LEFT JOIN m_drivertype G ON A.IDDRIVERTYPE = G.IDDRIVERTYPE
								WHERE A.IDRESERVATIONDETAILS = '".$idReservationDetails."' AND A.IDDRIVERTYPE != 0 AND A.STATUS = 1 AND
									  E.IDRESERVATIONDETAILS IS NULL
								GROUP BY A.IDRESERVATIONDETAILS
								LIMIT 1"
						, '%d %b %Y'
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return false;
		}
		
		return $row;
		
	}
	
	public function isScheduleExist($idReservationDetails){
		
		$baseQuery	=	sprintf("SELECT IDRESERVATIONDETAILS FROM t_scheduledriver
								WHERE IDRESERVATIONDETAILS = '".$idReservationDetails."'
								LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return false;
		}
		
		return true;
		
	}

	public function getDataDriverListManual($scheduleDate, $idDriverType){
		$con_driverType	=	"1=1";
		switch($idDriverType){
			case 2	:	$con_driverType	=	"A.IDDRIVERTYPE = 2"; break;
			case 3	:	$con_driverType	=	"A.IDDRIVERTYPE IN (2,3)"; break;
			case 1	:	
			default	:	$con_driverType	=	"1=1"; break;
		}
		
		$baseQuery	=	sprintf(
							"SELECT A.IDDRIVER, A.NAME AS DRIVERNAME, IFNULL(B.TOTALSCHEDULE, 0) AS TOTALSCHEDULE,
									IFNULL(C.IDDAYOFF, 0) AS IDDAYOFF, C.REASON, D.DRIVERTYPE, A.RANKNUMBER, A.PARTNERSHIPTYPE,
									IF(A.PARTNERSHIPTYPE = 1, D.DRIVERTYPE, 0) AS ORDERFIELD
							 FROM m_driver A
							 LEFT JOIN (SELECT BA.IDDRIVER, COUNT(BA.IDSCHEDULEDRIVER) AS TOTALSCHEDULE FROM t_scheduledriver BA
										LEFT JOIN t_reservationdetails BB ON BA.IDRESERVATIONDETAILS = BB.IDRESERVATIONDETAILS
										LEFT JOIN t_reservation BC ON BB.IDRESERVATION = BC.IDRESERVATION
										WHERE BB.SCHEDULEDATE = '".$scheduleDate."' AND BB.STATUS = 1
										GROUP BY BA.IDDRIVER
										) AS B ON A.IDDRIVER = B.IDDRIVER
							 LEFT JOIN t_dayoff C ON A.IDDRIVER = C.IDDRIVER AND C.DATEDAYOFF = '".$scheduleDate."'
							 LEFT JOIN m_drivertype D ON A.IDDRIVERTYPE = D.IDDRIVERTYPE
							 WHERE ".$con_driverType." AND A.STATUS = 1
							 GROUP BY A.IDDRIVER
							 ORDER BY FIELD(A.PARTNERSHIPTYPE, 4, 1, 2, 3), ORDERFIELD DESC, A.NAME"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}
	
	public function getDataDriverJobHistoryList($idDriver){
		
		$baseQuery		=	"SELECT DATE_FORMAT(B.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATE,
									DATE_FORMAT(C.RESERVATIONTIMESTART, '%H:%i') AS RESERVATIONTIMESTART,
									C.CUSTOMERNAME, C.RESERVATIONTITLE, B.PRODUCTNAME, E.JOBRATE, B.SCHEDULEDATE AS SCHEDULEDATEDB
							 FROM t_scheduledriver A
							 LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
							 LEFT JOIN t_reservation C ON B.IDRESERVATION = C.IDRESERVATION
							 LEFT JOIN m_product D ON B.PRODUCTNAME = D.PRODUCTNAME
							 LEFT JOIN t_driverfee E ON D.IDPRODUCT = E.IDPRODUCT
							 WHERE A.IDDRIVER = ".$idDriver." AND B.STATUS = 1
							 ORDER BY B.SCHEDULEDATE DESC, C.RESERVATIONTIMESTART DESC
							 LIMIT 5";
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return false;
		}
		
		return $result;
		
	}
	
	public function isDayOffDriverExist($idDriver, $dateSchedule){
		$baseQuery	=	sprintf(
							"SELECT IDDAYOFF FROM t_dayoff
							WHERE IDDRIVER = ".$idDriver." AND DATEDAYOFF = '".$dateSchedule."'
							LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return true;
	}
}