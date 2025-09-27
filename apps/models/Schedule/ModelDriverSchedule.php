<?php
class ModelDriverSchedule extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataDriverSchedule($scheduleDate){
		$scheduleYear	=	substr($scheduleDate, 0, 4);
		$baseQuery		=	sprintf(
								"SELECT B.DRIVERTYPE, A.NAME, IFNULL(E.CARCAPACITYNAME, '-') AS CARCAPACITYNAME, IFNULL(C.ARRIDSCHEDULEDRIVER, '') AS ARRIDSCHEDULEDRIVER,
										IFNULL(C.ARRRESERVATION, '') AS ARRRESERVATION, IFNULL(C.ARRDETAILCARDRIVERPICKUP, '') AS ARRDETAILCARDRIVERPICKUP, IFNULL(C.TOTALSCHEDULE, '') AS TOTALSCHEDULE,
										A.IDDRIVERTYPE, A.IDDRIVER, IFNULL(D.IDDAYOFF, 0) AS IDDAYOFF, D.REASON, IFNULL(C.STATUSPROCESS, '') AS STATUSPROCESS, IFNULL(C.STATUSPROCESSNAME, '') AS STATUSPROCESSNAME,
										IFNULL(C.STATUSCONFIRM, '') AS STATUSCONFIRM, IFNULL(C.DATETIMECONFIRM, '') AS DATETIMECONFIRM, IFNULL(C.IDRESERVATIONDETAILS, '') AS IDRESERVATIONDETAILS, A.RANKNUMBER,
										IF(A.PARTNERSHIPTYPE = 1, A.IDDRIVERTYPE, 0) AS ORDERFIELD, A.PARTNERSHIPTYPE
								FROM m_driver A
								LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
								LEFT JOIN (
									SELECT CA.IDDRIVER, IFNULL(GROUP_CONCAT(CA.IDSCHEDULEDRIVER SEPARATOR '|'), '') AS ARRIDSCHEDULEDRIVER,
											IFNULL(COUNT(CA.IDSCHEDULEDRIVER), 1) AS TOTALSCHEDULE,
											IFNULL(
												GROUP_CONCAT(
													CONCAT(
														'<b>',
														CE.SOURCENAME,
														' - ',
														CC.BOOKINGCODE,
														'</b>',
														'<badgeStatus/>',
														'[',
														LEFT(CC.RESERVATIONTIMESTART, 5),
														'] ',
														CC.RESERVATIONTITLE,
														' - ',
														CC.CUSTOMERNAME,
														'<detailCarDriverPickup/>'
													) SEPARATOR '|'
												),
											'') AS ARRRESERVATION,
											IFNULL(GROUP_CONCAT(CONCAT(CA.DRIVERNAME, ' (', CA.DRIVERPHONENUMBER, ') - ', CA.CARBRANDMODEL, ' [', CA.CARNUMBERPLATE, ']') SEPARATOR '|'), '') AS ARRDETAILCARDRIVERPICKUP,
											IFNULL(GROUP_CONCAT(CA.STATUSPROCESS SEPARATOR '|'), '') AS STATUSPROCESS,
											IFNULL(GROUP_CONCAT(IFNULL(IF(CA.STATUSPROCESS = 0, 'Unprocessed', CD.STATUSPROCESSNAME), '-') SEPARATOR '|'), '') AS STATUSPROCESSNAME,
											IFNULL(GROUP_CONCAT(CA.STATUSCONFIRM SEPARATOR '|'), '') AS STATUSCONFIRM,
											IFNULL(GROUP_CONCAT(DATE_FORMAT(CA.DATETIMECONFIRM, '%s') SEPARATOR '|'), '') AS DATETIMECONFIRM,
											IFNULL(GROUP_CONCAT(CA.IDRESERVATIONDETAILS SEPARATOR '|'), '') AS IDRESERVATIONDETAILS
									FROM t_scheduledriver CA
									LEFT JOIN t_reservationdetails CB ON CA.IDRESERVATIONDETAILS = CB.IDRESERVATIONDETAILS
									LEFT JOIN t_reservation CC ON CB.IDRESERVATION = CC.IDRESERVATION
									LEFT JOIN m_statusprocessdriver CD ON CA.STATUSPROCESS = CD.IDSTATUSPROCESSDRIVER
									LEFT JOIN m_source CE ON CC.IDSOURCE = CE.IDSOURCE
									WHERE CB.SCHEDULEDATE = '".$scheduleDate."' AND CB.STATUS = 1
									GROUP BY CA.IDDRIVER
									ORDER BY CC.RESERVATIONTIMESTART
								) AS C ON A.IDDRIVER = C.IDDRIVER
								LEFT JOIN t_dayoff D ON A.IDDRIVER = D.IDDRIVER AND D.DATEDAYOFF = '".$scheduleDate."'
								LEFT JOIN m_carcapacity E ON A.IDCARCAPACITY = E.IDCARCAPACITY
								WHERE A.STATUS = 1
								ORDER BY FIELD(A.PARTNERSHIPTYPE, 4, 1, 2, 3), FIELD(ORDERFIELD, 2, 3, 1, 0), A.RANKNUMBER, A.NAME"
							,	'%d %b %Y %H:%i'
							);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result) return false;
		return $result;	
	}
	
	public function getTotalReservationByDate($scheduleDate){
		$baseQuery	=	sprintf("SELECT COUNT(A.IDRESERVATIONDETAILS) AS TOTALRESERVATION, SUM(IF(A.IDDRIVERTYPE = 1, 1, 0)) AS TOTALRESERVATIONSHUTTLE,
										SUM(IF(A.IDDRIVERTYPE = 3, 1, 0)) AS TOTALRESERVATIONCHARTER, SUM(IF(A.IDDRIVERTYPE = 2, 1, 0)) AS TOTALRESERVATIONTOUR
								FROM t_reservationdetails A
								LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
								LEFT JOIN t_scheduledriver C ON A.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
								WHERE A.SCHEDULEDATE = '".$scheduleDate."' AND A.IDDRIVERTYPE != 0 AND C.IDRESERVATIONDETAILS IS NULL AND A.STATUS = 1
								GROUP BY A.SCHEDULEDATE"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return array(
				"TOTALRESERVATION"			=>	0,
				"TOTALRESERVATIONSHUTTLE"	=>	0,
				"TOTALRESERVATIONCHARTER"	=>	0,
				"TOTALRESERVATIONTOUR"		=>	0
			);
		}
		
		return $row;		
	}
	
	public function getDataReservationSchedule($scheduleDate, $idDriver, $searchKeyWord){
		$con_idDriver	=	isset($idDriver) && $idDriver != "" ? "D.IDDRIVER = ".$idDriver : "1=1";
		$con_keyword	=	isset($searchKeyWord) && $searchKeyWord != "" ? "(C.RESERVATIONTITLE LIKE '%".$searchKeyWord."%' OR C.CUSTOMERNAME LIKE '%".$searchKeyWord."%' OR C.BOOKINGCODE LIKE '%".$searchKeyWord."%')" : "1=1";
		$baseQuery		=	"SELECT A.IDRESERVATIONDETAILS, LEFT(C.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART, A.IDPRODUCTTYPE, B.PRODUCTTYPE, C.RESERVATIONTITLE, A.PRODUCTNAME,
									A.NOTES, C.CUSTOMERNAME, E.DRIVERTYPE, IFNULL(F.NAME, '-') AS DRIVERNAME, IFNULL(D.IDDRIVER, 0) AS IDDRIVER, A.IDDRIVERTYPE, IFNULL(D.IDSCHEDULEDRIVER, 0) AS IDSCHEDULEDRIVER,
									C.HOTELNAME, C.PICKUPLOCATION, D.STATUSCONFIRM, DATE_FORMAT(D.DATETIMECONFIRM, '%d %b %Y %H:%i') AS DATETIMECONFIRM, D.STATUSPROCESS,
									IFNULL(IF(D.STATUSPROCESS = 0, 'Unprocessed', G.STATUSPROCESSNAME), 'Unscheduled') AS STATUSPROCESSNAME, C.NUMBEROFADULT, C.NUMBEROFCHILD, C.NUMBEROFINFANT, C.REMARK, A.NOMINAL,
									C.SPECIALREQUEST, IFNULL(D.DRIVERNAME, '') AS DRIVERNAMEDETAIL, IFNULL(D.DRIVERPHONENUMBER, '') AS DRIVERPHONENUMBER, IFNULL(D.CARBRANDMODEL, '') AS CARBRANDMODEL,
									IFNULL(D.CARNUMBERPLATE, '') AS CARNUMBERPLATE
							FROM t_reservationdetails A
							LEFT JOIN m_producttype B ON A.IDPRODUCTTYPE = B.IDPRODUCTTYPE
							LEFT JOIN t_reservation C ON A.IDRESERVATION = C.IDRESERVATION
							LEFT JOIN t_scheduledriver D ON A.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS
							LEFT JOIN m_drivertype E ON A.IDDRIVERTYPE = E.IDDRIVERTYPE
							LEFT JOIN m_driver F ON D.IDDRIVER = F.IDDRIVER
							LEFT JOIN m_statusprocessdriver G ON D.STATUSPROCESS = G.IDSTATUSPROCESSDRIVER
							WHERE A.SCHEDULEDATE = '".$scheduleDate."' AND A.IDDRIVERTYPE != 0 AND A.STATUS = 1 AND ".$con_idDriver." AND ".$con_keyword."
							GROUP BY A.IDRESERVATIONDETAILS
							ORDER BY FIELD(A.IDDRIVERTYPE, 2, 3, 1), C.RESERVATIONTIMESTART, C.CUSTOMERNAME";
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result) return false;
		return $result;
	}
	
	public function getAllDriverScheduleReservation($idReservation, $date){
		$idReservation	=	!isset($idReservation) || is_null($idReservation) || $idReservation == '' ? 0 : $idReservation;
		$baseQuery		=	sprintf("SELECT A.IDRESERVATIONDETAILS FROM t_reservationdetails A
									LEFT JOIN t_scheduledriver B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
									WHERE A.SCHEDULEDATE >= '".$date."' AND A.IDDRIVERTYPE != 0 AND A.IDRESERVATION = ".$idReservation." AND
										  B.IDRESERVATIONDETAILS IS NULL
									GROUP BY A.SCHEDULEDATE
									ORDER BY A.SCHEDULEDATE"
							);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}

	public function getDataAllDriver(){
		$baseQuery	=	sprintf("SELECT A.IDDRIVER, A.IDDRIVERTYPE, B.DRIVERTYPE, A.NAME AS DRIVERNAME, A.RANKNUMBER, A.PARTNERSHIPTYPE,
										IF(A.PARTNERSHIPTYPE = 1, A.IDDRIVERTYPE, 0) AS ORDERFIELD
								 FROM m_driver A
								 LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
								 WHERE A.STATUS = 1
								 ORDER BY FIELD(A.PARTNERSHIPTYPE, 4, 1, 2, 3), FIELD(ORDERFIELD, 2, 3, 1, 0), A.RANKNUMBER, A.NAME"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}
	
	public function getDataDriverScheduleMonth($yearMonth){
		$baseQuery	=	sprintf("SELECT A.IDDRIVER, RIGHT(B.SCHEDULEDATE, 2) AS SCHEDULEDATE, LEFT(C.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART,
										A.IDSCHEDULEDRIVER, B.IDRESERVATION, LEFT(D.DRIVERTYPE, 1) AS DRIVERTYPE
								FROM t_scheduledriver A
								LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
								LEFT JOIN t_reservation C ON B.IDRESERVATION = C.IDRESERVATION
								LEFT JOIN m_drivertype D ON B.IDDRIVERTYPE = D.IDDRIVERTYPE
								WHERE LEFT(B.SCHEDULEDATE, 7) = '".$yearMonth."' AND B.STATUS = 1
								ORDER BY A.IDDRIVER, B.SCHEDULEDATE, B.IDRESERVATION"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}
	
	public function getDataDayOffRequest($yearMonth, $idDayoffRequest = false, $dayOffDate = '', $dayOffStatus = '', $searchKeyword = ''){
		$conditionMain	=	'1=1';
		if(isset($idDayoffRequest) && $idDayoffRequest != null && $idDayoffRequest != false){
			$conditionMain	=	"A.IDDAYOFFREQUEST = ".$idDayoffRequest;
		} else {
			if($dayOffDate == ''){
				$conditionMain	=	"LEFT(A.DATEDAYOFF, 7) = '".$yearMonth."'";
			} else {
				$dayOffDate		=	DateTime::createFromFormat('d-m-Y', $dayOffDate);
				$dayOffDate		=	$dayOffDate->format('Y-m-d');
				$conditionMain	=	"A.DATEDAYOFF = '".$dayOffDate."'";
			}
		}
		
		$conditionStatus	=	isset($dayOffStatus) && $dayOffStatus != '' ? 'A.STATUS = '.$dayOffStatus : '1=1';
		$conditionKeyword	=	isset($searchKeyword) && $searchKeyword != '' ? "(B.NAME LIKE '%".$searchKeyword."% OR A.REASON LIKE '%".$searchKeyword."%' OR A.USERAPPROVAL LIKE '%".$searchKeyword."%')" : '1=1';
		
		$baseQuery	=	sprintf("SELECT A.IDDAYOFFREQUEST, B.NAME AS DRIVERNAME, C.DRIVERTYPE, DATE_FORMAT(A.DATEDAYOFF, '%s') AS DATEDAYOFF,
										A.REASON, DATE_FORMAT(A.DATETIMEINPUT, '%s') AS DATETIMEINPUT, A.STATUS,
										IF(A.DATETIMEAPPROVAL = '0000-00-00 00:00:00', '-', DATE_FORMAT(A.DATETIMEAPPROVAL, '%s')) AS DATETIMEAPPROVAL,
										IFNULL(A.USERAPPROVAL, '-') AS USERAPPROVAL
								FROM t_dayoffrequest A
								LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
								LEFT JOIN m_drivertype C ON B.IDDRIVERTYPE = C.IDDRIVERTYPE
								WHERE ".$conditionMain." AND ".$conditionStatus." AND ".$conditionKeyword." AND A.IDDRIVER != 0
								ORDER BY A.DATEDAYOFF, B.NAME"
								, '%d %b %Y'
								, '%d %b %Y %H:%i'
								, '%d %b %Y %H:%i'
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;	
	}
	
	public function getDetailDayOffRequest($idDayoffRequest){
		$baseQuery	=	sprintf("SELECT A.IDDRIVER, A.IDCARVENDOR, A.DATEDAYOFF, A.REASON, A.DATETIMEINPUT,
										IF(A.IDDRIVER != 0, B.PARTNERSHIPTYPE, 2) AS PARTNERSHIPTYPE
								FROM t_dayoffrequest A
								LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
								WHERE A.IDDAYOFFREQUEST = '".$idDayoffRequest."'
								LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;
	}

	public function getDataDriverDayOff($yearMonth){
		$baseQuery	=	sprintf("SELECT IDDRIVER, RIGHT(DATEDAYOFF, 2) AS DAYOFFDATE, IDDAYOFF
								FROM t_dayoff
								WHERE LEFT(DATEDAYOFF, 7) = '".$yearMonth."' AND IDDRIVER != 0
								ORDER BY IDDRIVER, DATEDAYOFF"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}
	
	public function getDataReservationList($monthly, $scheduleDate, $scheduleMonth, $idDriverType, $idDriver){
		$con_period		=	$monthly == true ? "LEFT(A.SCHEDULEDATE, 7) = '".$scheduleMonth."'" : "A.SCHEDULEDATE = '".$scheduleDate."'";
		$con_driverType	=	"1=1";
		switch($idDriverType){
			case 2	:	$con_driverType	=	"1=1"; break;
			case 3	:	$con_driverType	=	"A.IDDRIVERTYPE IN (1,3)"; break;
			case 1	:	
			default	:	$con_driverType	=	"A.IDDRIVERTYPE = 1"; break;
		}
		$baseQuery		=	sprintf("SELECT A.IDRESERVATIONDETAILS, DATE_FORMAT(A.SCHEDULEDATE, '%s') AS SCHEDULEDATE, LEFT(C.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART,
											C.BOOKINGCODE, A.IDPRODUCTTYPE, B.PRODUCTTYPE, A.PRODUCTNAME, A.NOTES, C.CUSTOMERNAME, IFNULL(E.IDDAYOFF, 0) AS IDDAYOFF,
											E.REASON, F.DRIVERTYPE, C.HOTELNAME, C.PICKUPLOCATION, C.NUMBEROFADULT, C.NUMBEROFCHILD, C.NUMBEROFINFANT, C.REMARK
									FROM t_reservationdetails A
									LEFT JOIN m_producttype B ON A.IDPRODUCTTYPE = B.IDPRODUCTTYPE
									LEFT JOIN t_reservation C ON A.IDRESERVATION = C.IDRESERVATION
									LEFT JOIN t_scheduledriver D ON A.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS
									LEFT JOIN t_dayoff E ON A.SCHEDULEDATE = E.DATEDAYOFF AND E.IDDRIVER = ".$idDriver."
									LEFT JOIN m_drivertype F ON A.IDDRIVERTYPE = F.IDDRIVERTYPE
									WHERE ".$con_period." AND ".$con_driverType." AND D.IDRESERVATIONDETAILS IS NULL AND A.STATUS = 1 AND A.IDPRODUCTTYPE = 2
									GROUP BY A.IDRESERVATIONDETAILS
									ORDER BY A.SCHEDULEDATE, C.RESERVATIONTIMESTART, C.RESERVATIONTIMESTART, C.CUSTOMERNAME"
									, '%d %b %Y'
							);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}

	public function getDataDriverList($scheduleDate, $idDriverType){
		$con_driverType	=	"1=1";
		switch($idDriverType){
			case 2	:	$con_driverType	=	"A.IDDRIVERTYPE = 2"; break;
			case 3	:	$con_driverType	=	"A.IDDRIVERTYPE IN (2,3)"; break;
			case 1	:	
			default	:	$con_driverType	=	"1=1"; break;
		}
		
		$baseQuery	=	sprintf(
							"SELECT A.IDDRIVER, A.NAME AS DRIVERNAME, 0 AS TOTALSCHEDULE, 0 AS IDDAYOFF, '' AS REASON, B.DRIVERTYPE, A.RANKNUMBER,
									A.PARTNERSHIPTYPE, IF(A.PARTNERSHIPTYPE = 1, A.IDDRIVERTYPE, 0) AS ORDERFIELD
							 FROM m_driver A
							 LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
							 WHERE ".$con_driverType." AND A.STATUS = 1
							 GROUP BY A.IDDRIVER
							 ORDER BY FIELD(A.PARTNERSHIPTYPE, 4, 1, 2, 3), FIELD(ORDERFIELD, 2, 3, 1, 0), A.NAME"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}
	
	public function getTotalScheduleDriver($idDriver, $scheduleDate){
		$query	=	$this->db->query(
						"SELECT COUNT(A.IDSCHEDULEDRIVER) AS TOTALSCHEDULEDRIVER FROM t_scheduledriver A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						WHERE A.IDDRIVER = '".$idDriver."' AND B.SCHEDULEDATE = '".$scheduleDate."' AND B.STATUS = 1
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['TOTALSCHEDULEDRIVER'];		
		return 0;
	}
	
	public function getDataDayOffDriver($idDriver, $scheduleDate){
		$query	=	$this->db->query(
						"SELECT IDDAYOFF, REASON
						FROM t_dayoff
						WHERE IDDRIVER = ".$idDriver." AND DATEDAYOFF = '".$scheduleDate."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;		
		return [
			"IDDAYOFF"	=>	0,
			"REASON"	=>	"-"
		];
	}
	
	public function isFeeWithdrawn($idDriverSchedule){
		$query	=	$this->db->query(
						"SELECT B.IDFEE FROM t_scheduledriver A
						LEFT JOIN t_fee B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS AND A.IDDRIVER = B.IDDRIVER
						WHERE A.IDSCHEDULEDRIVER = ".$idDriverSchedule." AND B.IDWITHDRAWALRECAP != 0
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return true;		
		return false;
	}
	
	public function getDetailReservation($idDriverSchedule, $idReservationDetails){
		if((!isset($idDriverSchedule) || $idDriverSchedule == "" || $idDriverSchedule == 0) && (!isset($idReservationDetails) || $idReservationDetails == "" || $idReservationDetails == 0)){
			return false;
		}
		
		$condition	=	"1=1";
		if(isset($idDriverSchedule) && $idDriverSchedule != "" && $idDriverSchedule != 0){
			$condition	=	"C.IDSCHEDULEDRIVER = '".$idDriverSchedule."'";
		} else if(isset($idReservationDetails) && $idReservationDetails != "" && $idReservationDetails != 0) {
			$condition	=	"B.IDRESERVATIONDETAILS = ".$idReservationDetails;
		}
		
		$query	=	$this->db->query(
						"SELECT D.SOURCENAME, A.RESERVATIONTITLE, B.PRODUCTNAME, DATE_FORMAT(B.SCHEDULEDATE, '%d %M %Y') AS SCHEDULEDATE, B.SCHEDULEDATE AS SCHEDULEDATEDB,
								SUBSTRING(A.RESERVATIONTIMESTART, 1, 5) AS RESERVATIONTIMESTART, A.CUSTOMERNAME, A.CUSTOMERCONTACT, A.CUSTOMEREMAIL, A.HOTELNAME, A.NUMBEROFADULT,
								A.NUMBEROFCHILD, A.NUMBEROFINFANT, IF(A.PICKUPLOCATION IS NULL OR A.PICKUPLOCATION = '', '-', A.PICKUPLOCATION) AS PICKUPLOCATION,
								IF(A.DROPOFFLOCATION IS NULL OR A.DROPOFFLOCATION = '', '-', A.DROPOFFLOCATION) AS DROPOFFLOCATION, IF(A.REMARK IS NULL OR A.REMARK = '', '-', A.REMARK) AS REMARK,
								IF(A.TOURPLAN IS NULL OR A.TOURPLAN = '', '-', A.TOURPLAN) AS TOURPLAN, C.IDDRIVER, A.IDRESERVATION, B.IDRESERVATIONDETAILS, E.IDFEE,
								IFNULL(F.DURATIONHOUR, 6) AS DURATIONHOUR, IFNULL(C.DRIVERNAME, '') AS DRIVERNAME, IFNULL(C.DRIVERPHONENUMBER, '') AS DRIVERPHONENUMBER,
								IFNULL(C.CARBRANDMODEL, '') AS CARBRANDMODEL, IFNULL(C.CARNUMBERPLATE, '') AS CARNUMBERPLATE
						FROM t_reservation A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN t_scheduledriver C ON B.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
						LEFT JOIN m_source D ON A.IDSOURCE = D.IDSOURCE
						LEFT JOIN t_fee E ON B.IDRESERVATIONDETAILS = E.IDRESERVATIONDETAILS
						LEFT JOIN m_product F ON B.PRODUCTNAME = F.PRODUCTNAME
						WHERE ".$condition."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}

	public function getListDetailScheduleActivity($idReservation){
		$query	=	$this->db->query(
						"SELECT C.NAME AS VENDORNAME, IFNULL(LEFT(B.TIMESCHEDULE, 5), '') AS TIMESCHEDULE, IFNULL(B.STATUSCONFIRM, -1) AS STATUSCONFIRM
						FROM t_reservationdetails A
						LEFT JOIN t_schedulevendor B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
						WHERE A.IDRESERVATION = '".$idReservation."' AND A.IDVENDOR != 0 AND A.STATUS = 1
						ORDER BY B.TIMESCHEDULE ASC"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return [];		
	}

	public function getDetailDayOff($idDayoff){
		$query	=	$this->db->query(
						"SELECT A.IDDAYOFFREQUEST, C.DRIVERTYPE, B.NAME, DATE_FORMAT(A.DATEDAYOFF, '%d %b %Y') AS DATEDAYOFF,
								DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT, A.REASON, A.DATEDAYOFF AS DATEDAYOFFRAW,
								A.IDDRIVER
						FROM t_dayoff A
						LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
						LEFT JOIN m_drivertype C ON B.IDDRIVERTYPE = C.IDDRIVERTYPE
						WHERE A.IDDAYOFF = '".$idDayoff."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}

	public function getTotalDayOffInDate($date){
		$query	=	$this->db->query(
						"SELECT COUNT(IDDAYOFF) AS TOTALDAYOFF FROM t_dayoff
						WHERE DATEDAYOFF = '".$date."'
						GROUP BY DATEDAYOFF
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['TOTALDAYOFF'];
		return 0;		
	}
	
	public function getTotalDayOffDriverInMonth($idDriver, $yearMonth){
		$query	=	$this->db->query(
						"SELECT COUNT(IDDAYOFF) AS TOTALDAYOFF FROM t_dayoff
						WHERE IDDRIVER = ".$idDriver." AND LEFT(DATEDAYOFF, 7) = '".$yearMonth."'
						GROUP BY IDDRIVER
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['TOTALDAYOFF'];
		return 0;		
	}

	public function getDetailMessage($idMessage){
		$query	=	$this->db->query(
						"SELECT A.IDPRIMARY, A.TITLE, A.MESSAGE, B.TOKENFCM
						FROM t_messagepartner A
						LEFT JOIN m_usermobile B ON A.IDPARTNERTYPE = B.IDPARTNERTYPE AND A.IDPARTNER = B.IDPARTNER
						WHERE A.IDMESSAGEPARTNER = '".$idMessage."' AND B.TOKENFCM != ''
						ORDER BY B.LASTACTIVITY DESC
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}

	public function getDetailNotificationSchedule($idDriverSchedule){
		$query	=	$this->db->query(
						"SELECT A.IDRESERVATIONDETAILS, A.IDDRIVER, B.TITLE, B.MESSAGE, DATE_FORMAT(C.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATE, C.PRODUCTNAME
						FROM t_scheduledriver A
						LEFT JOIN t_messagepartner B ON A.IDRESERVATIONDETAILS = B.IDPRIMARY AND A.IDDRIVER = B.IDPARTNER AND B.IDPARTNERTYPE = 2 AND IDMESSAGEPARTNERTYPE = 1
						LEFT JOIN t_reservationdetails C ON A.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
						WHERE A.IDSCHEDULEDRIVER = '".$idDriverSchedule."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}

	public function getCollectPaymentReservation($idReservation, $idDriver){
		$baseQuery	=	"SELECT B.IDCOLLECTPAYMENT, B.IDDRIVER, DATE_FORMAT(B.DATECOLLECT, '%d %b %Y') AS DATECOLLECT,
								A.AMOUNTCURRENCY, A.AMOUNT, A.AMOUNTIDR
						FROM t_reservationpayment A
						LEFT JOIN t_collectpayment B ON A.IDRESERVATIONPAYMENT = B.IDRESERVATIONPAYMENT
						WHERE A.IDRESERVATION = ".$idReservation." AND B.IDPARTNERTYPE = 2 AND
							  (B.IDDRIVER = 0 OR B.IDDRIVER = ".$idDriver.") AND A.IDPAYMENTMETHOD = 2
						ORDER BY B.DATECOLLECT";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}
	
	public function getStrArrJobRateDriver($idDriver){
		$query	=	$this->db->query(
						"SELECT SUBSTRING_INDEX(GROUP_CONCAT(D.JOBRATE ORDER BY B.SCHEDULEDATE DESC, E.RESERVATIONTIMESTART DESC SEPARATOR ','), ',', 5) AS JOBRATE
						FROM t_scheduledriver A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						LEFT JOIN m_product C ON B.PRODUCTNAME = C.PRODUCTNAME
						LEFT JOIN t_driverfee D ON C.IDPRODUCT = D.IDPRODUCT
						LEFT JOIN t_reservation E ON B.IDRESERVATION = E.IDRESERVATION
						WHERE B.STATUS = 1 AND A.IDDRIVER = '".$idDriver."'
						GROUP BY A.IDDRIVER"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['JOBRATE'];
		return false;		
	}

	public function getDetailCarByDriver($idDriver){
		$query	=	$this->db->query(
						"SELECT A.IDCARVENDOR, A.IDCARTYPE, A.IDVENDOR, B.NAME AS VENDORNAME FROM t_carvendor A
						LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
						WHERE A.IDDRIVER = '".$idDriver."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;
	}
	
	public function getDataSelfDriveCost($idVendor, $idCarType){
		$query	=	$this->db->query(
						"SELECT A.DURATION, CONCAT(B.CARTYPE, ' - ', A.DURATION, ' Hours') AS PRODUCTNAME, A.NOMINALFEE, A.NOTES
						FROM t_carselfdrivefee A
						LEFT JOIN m_cartype B ON A.IDCARTYPE = B.IDCARTYPE
						WHERE A.IDVENDOR = '".$idVendor."' AND A.IDCARTYPE = ".$idCarType."
						ORDER BY A.DURATION");
		$result	=	$query->result();

		if(isset($result)) return $result;
		return false;
	}
	
	public function getDetailScheduleCarDriver($idDriver, $idReservation, $dateScheduleDB){
		$query	=	$this->db->query(
						"SELECT A.IDSCHEDULECAR, A.IDRESERVATIONDETAILS FROM t_schedulecar A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						LEFT JOIN t_carvendor C ON A.IDCARVENDOR = C.IDCARVENDOR
						WHERE C.IDDRIVER = '".$idDriver."' AND B.IDRESERVATION = '".$idReservation."' AND B.SCHEDULEDATE = '".$dateScheduleDB."'
						LIMIT 1");
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;
	}
}