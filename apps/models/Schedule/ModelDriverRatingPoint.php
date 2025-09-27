<?php
class ModelDriverRatingPoint extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataDriverRatingPoint($page, $keyword, $dataPerPage= 20){
		
		$ci			=& get_instance();
		$ci->load->model('MainOperation');

		$startid	=	($page * 1 - 1) * $dataPerPage;
		$condition	=	isset($keyword) && $keyword != "" && $keyword != null ?
						"(A.NAME LIKE '%".$keyword."%' OR A.ADDRESS LIKE '%".$keyword."%' OR A.PHONE LIKE '%".$keyword."%' OR A.EMAIL LIKE '%".$keyword."%')" : "1=1";
		$baseQuery	=	"SELECT A.IDDRIVER, B.DRIVERTYPE, A.NAME, A.ADDRESS, A.PHONE, A.EMAIL, A.SCHEDULETYPE, A.RANKNUMBER, A.TOTALPOINT, A.BASICPOINT,
								IFNULL(C.SOURCENAME, '-') AS SOURCENAME, IFNULL(C.DATERATINGPOINT, '-') AS DATERATINGPOINT, IFNULL(C.RATING, '-') AS RATING,
								IFNULL(C.POINT, '-') AS POINT, IFNULL(C.USERINPUT, '-') AS USERINPUT, IFNULL(C.DATETIMEINPUT, '-') AS DATETIMEINPUT,
								A.PARTNERSHIPTYPE, IF(A.PARTNERSHIPTYPE = 1, A.IDDRIVERTYPE, 0) AS ORDERFIELD
						 FROM m_driver A
						 LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
						 LEFT JOIN (SELECT CB.IDDRIVER, IFNULL(CC.SOURCENAME, '-') AS SOURCENAME, DATE_FORMAT(CB.DATERATINGPOINT, '%d %b %Y') AS DATERATINGPOINT,
										   CB.RATING, CB.POINT, CB.USERINPUT, DATE_FORMAT(CB.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT
									FROM (SELECT MAX(IDDRIVERRATINGPOINT) AS IDDRIVERRATINGPOINT
										  FROM t_driverratingpoint
										  GROUP BY IDDRIVER) AS CA
									LEFT JOIN t_driverratingpoint CB ON CA.IDDRIVERRATINGPOINT = CB.IDDRIVERRATINGPOINT
									LEFT JOIN m_source CC ON CB.IDSOURCE = CC.IDSOURCE
									GROUP BY CB.IDDRIVER
									ORDER BY CB.DATETIMEINPUT DESC
									) AS C ON A.IDDRIVER = C.IDDRIVER
						 WHERE A.STATUS = 1 AND ".$condition."
						 GROUP BY A.IDDRIVER
						 ORDER BY FIELD(A.PARTNERSHIPTYPE, 4, 1, 2, 3), FIELD(ORDERFIELD, 0, 1, 3, 2), A.RANKNUMBER, A.NAME";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDDRIVER", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
	
	}
	
	public function getDataHistoryRatingPoint($idDriver, $dateStart){
		
		$baseQuery	=	"SELECT A.IDDRIVER, IFNULL(B.SOURCENAME, '-') AS SOURCENAME, A.BOOKINGCODE, DATE_FORMAT(A.DATERATINGPOINT, '%d %b %Y') AS DATERATINGPOINT,
							   A.RATING, A.POINT, A.REVIEWTITLE, A.REVIEWCONTENT, A.USERINPUT, DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT,
							   C.RESERVATIONTITLE
						FROM t_driverratingpoint A
						LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
					    LEFT JOIN t_reservation C ON A.BOOKINGCODE = C.BOOKINGCODE
						WHERE A.IDDRIVER = ".$idDriver."
						ORDER BY A.DATERATINGPOINT DESC
						LIMIT 25";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result){
			return false;
		}
		
		return $result;
		
	}
	
	public function getDataDriverRatingByDate($dateRating, $keyword){
		
		$condition	=	isset($keyword) && $keyword != "" && $keyword != null ?
						"(A.NAME LIKE '%".$keyword."%')" : "1=1";
		$baseQuery	=	"SELECT A.IDDRIVER, B.DRIVERTYPE, A.NAME, A.PARTNERSHIPTYPE, IFNULL(GROUP_CONCAT(C.DRIVERRATINGPOINT SEPARATOR '&&'), '') AS DRIVERRATINGPOINT,
								CONCAT(D.SOURCENAME, '|', D.DATERATINGPOINT, '|', D.RATING, '|', D.POINT) AS LASTINPUTDATA, IF(A.PARTNERSHIPTYPE = 1, A.IDDRIVERTYPE, 0) AS ORDERFIELD
						FROM m_driver A
						LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
						LEFT JOIN (SELECT A.IDDRIVER,
										  CONCAT(A.IDDRIVERRATINGPOINT, '|', B.SOURCENAME, '|', A.RATING, '|', A.POINT, '|',
												 A.USERINPUT, '|', DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i'), '|',
												 DATE_FORMAT(A.DATETIMEINPUT, '%Y-%m-%d'), '|', IFNULL(A.BOOKINGCODE, '-'), '|', A.INPUTTYPE, '|',
												 A.REVIEWTITLE, '|', A.REVIEWCONTENT, '|', IFNULL(C.RESERVATIONTITLE, '-')) AS DRIVERRATINGPOINT
								   FROM t_driverratingpoint A
								   LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
								   LEFT JOIN t_reservation C ON A.BOOKINGCODE = C.BOOKINGCODE
								   WHERE A.DATERATINGPOINT = '".$dateRating."') AS C ON A.IDDRIVER = C.IDDRIVER
						LEFT JOIN (SELECT DB.IDDRIVER, IFNULL(DC.SOURCENAME, '-') AS SOURCENAME, DATE_FORMAT(DB.DATERATINGPOINT, '%d %b %Y') AS DATERATINGPOINT,
										  DB.RATING, DB.POINT
								   FROM (SELECT MAX(IDDRIVERRATINGPOINT) AS IDDRIVERRATINGPOINT
										FROM t_driverratingpoint
										GROUP BY IDDRIVER) DA
								   LEFT JOIN t_driverratingpoint DB ON DA.IDDRIVERRATINGPOINT = DB.IDDRIVERRATINGPOINT
								   LEFT JOIN m_source DC ON DB.IDSOURCE = DC.IDSOURCE
								  ) AS D ON A.IDDRIVER = D.IDDRIVER
						WHERE A.STATUS = 1 AND ".$condition."
						GROUP BY A.IDDRIVER
						ORDER BY FIELD(A.PARTNERSHIPTYPE, 4, 1, 2, 3), FIELD(ORDERFIELD, 0, 1, 3, 2), A.NAME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result){
			return false;
		}
		
		return $result;
		
	}
	
	public function getDataPointDriver($dateStart){
		
		$baseQuery	=	"SELECT A.IDDRIVER, IF(A.PARTNERSHIPTYPE != 1, A.PARTNERSHIPTYPE + 2, A.IDDRIVERTYPE) AS IDDRIVERTYPE, IFNULL(SUM(B.POINT), 0) + A.BASICPOINT AS TOTALPOINT
						FROM m_driver A
						LEFT JOIN t_driverratingpoint B ON A.IDDRIVER = B.IDDRIVER AND B.DATERATINGPOINT >= '".$dateStart."'
						GROUP BY A.IDDRIVER
						ORDER BY IDDRIVERTYPE, A.STATUS DESC, TOTALPOINT DESC, A.NAME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result){
			return false;
		}
		
		return $result;
		
	}
	
	public function getDataDriverByBookingCode($bookingCode){
		
		$baseQuery	=	"SELECT A.IDDRIVER, D.NAME AS DRIVERNAME, C.RESERVATIONTITLE FROM t_scheduledriver A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						LEFT JOIN t_reservation C ON B.IDRESERVATION = C.IDRESERVATION
						LEFT JOIN m_driver D ON A.IDDRIVER = D.IDDRIVER
						WHERE C.BOOKINGCODE = '".$bookingCode."'
						ORDER BY DRIVERNAME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result){
			return false;
		}
		
		return $result;
		
	}
	
	public function getPointByRating($rating){
		
		$baseQuery	=	"SELECT A.IDDRIVER, D.NAME AS DRIVERNAME FROM t_scheduledriver A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						LEFT JOIN t_reservation C ON B.IDRESERVATION = C.IDRESERVATION
						LEFT JOIN m_driver D ON A.IDDRIVER = D.IDDRIVER
						WHERE C.BOOKINGCODE = '".$bookingCode."'
						ORDER BY DRIVERNAME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result){
			return false;
		}
		
		return $result;
		
	}
	
	public function isRatingPointExistBookingCode($bookingCode, $idDriver){
		
		$baseQuery	=	"SELECT IDDRIVERRATINGPOINT FROM t_driverratingpoint
						WHERE BOOKINGCODE = '".$bookingCode."' AND IDDRIVER = ".$idDriver."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return false;
		}
		
		return true;
		
	}
	
	public function isBookingCodeViatorExist($bookingCode){
		
		$baseQuery	=	"SELECT IDDRIVERRATINGPOINT FROM t_driverratingpoint
						WHERE BOOKINGCODE = '".$bookingCode."' AND IDSOURCE = 2
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return false;
		}
		
		return true;
		
	}

	public function getDataAllDriver($idDriverType, $idDriver, $orderField, $orderType){
		
		$con_typeDriver	=	$idDriverType != "" ? "A.IDDRIVERTYPE = ".$idDriverType : "1=1";
		$con_idDriver	=	$idDriver != "" ? "A.IDDRIVER = ".$idDriver : "1=1";
		
		switch($orderField){
			case 1	:	$orderField	=	"A.RANKNUMBER"; break;
			case 2	:	$orderField	=	"DRIVERNAME"; break;
		}
		
		$baseQuery		=	sprintf("SELECT A.IDDRIVER, A.IDDRIVERTYPE, B.DRIVERTYPE, A.NAME AS DRIVERNAME, A.RANKNUMBER, A.PARTNERSHIPTYPE,
											IF(A.PARTNERSHIPTYPE = 1, A.IDDRIVERTYPE, 0) AS ORDERFIELD
									 FROM m_driver A
									 LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
									 WHERE A.STATUS = 1 AND ".$con_typeDriver." AND ".$con_idDriver."
									 ORDER BY FIELD(A.PARTNERSHIPTYPE, 4, 1, 2, 3), FIELD(ORDERFIELD, 0, 1, 3, 2), ".$orderField." ".$orderType
							);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return false;
		}
		
		return $result;
		
	}
	
	public function getDataDriverRatingMonth($yearMonth, $strArrDriver, $idSource){
		
		$con_idSource	=	$idSource != "" ? "A.IDSOURCE = ".$idSource : "1=1";
		$baseQuery		=	sprintf("SELECT A.IDDRIVER, RIGHT(A.DATERATINGPOINT, 2) AS DATERATING, LEFT(B.SOURCENAME, 1) AS SOURCEINITIAL, A.RATING, A.POINT, A.BOOKINGCODE
									FROM t_driverratingpoint A
									LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
									WHERE LEFT(A.DATERATINGPOINT, 7) = '".$yearMonth."' AND A.IDDRIVER IN (".$strArrDriver.") AND ".$con_idSource."
									ORDER BY A.IDDRIVER, A.DATERATINGPOINT"
							);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return false;
		}
		
		return $result;
		
	}
	
	public function getDetailReviewContentByBookingCode($bookingCode){
		
		$baseQuery	=	"SELECT B.SOURCENAME, A.RATING, A.POINT, A.USERINPUT, DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT,
								IFNULL(A.BOOKINGCODE, '-') AS BOOKINGCODE, A.INPUTTYPE, A.REVIEWTITLE, A.REVIEWCONTENT,
								IFNULL(C.RESERVATIONTITLE, '-') AS RESERVATIONTITLE, D.NAME AS DRIVERNAME
						FROM t_driverratingpoint A
						LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
						LEFT JOIN t_reservation C ON A.BOOKINGCODE = C.BOOKINGCODE
						LEFT JOIN m_driver D ON A.IDDRIVER = D.IDDRIVER
						WHERE A.BOOKINGCODE = '".$bookingCode."'";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return false;
		}
		
		return $row;
		
	}
	
	public function getDataSourceBonusReview(){
		$baseQuery	=	"SELECT GROUP_CONCAT(IDSOURCE) AS STRARRIDSOURCEREVIEWBONUS
						FROM m_source
						WHERE CALCULATEBONUSREVIEW = 1 AND STATUS = 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;
		
	}
	
	public function getDataPeriodBonusReview($idDriverReviewBonusPeriod){
		$con_id		=	isset($idDriverReviewBonusPeriod) && $idDriverReviewBonusPeriod != false ? "IDDRIVERREVIEWBONUSPERIOD = ".$idDriverReviewBonusPeriod : "1=1";
		$baseQuery	=	"SELECT IDDRIVERREVIEWBONUSPERIOD, PERIODDATESTART, PERIODDATEEND, BONUSRATE, TOTALTARGET
						FROM t_driverreviewbonusperiod
						WHERE ".$con_id."
						ORDER BY PERIODDATEEND DESC
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;
		
	}
	
	public function getDataDriverBonusReview(){
		$baseQuery	=	"SELECT IDDRIVER FROM m_driver
						WHERE REVIEWBONUSPUNISHMENT = 1";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;
		
	}
	
	public function getDataBonusReview($strArrIdSourceReviewBonus, $idDriver, $dateStartBonusPeriod, $dateEndBonusPeriod, $idDriverReviewBonus){
		$con_idDriverReviewBonus=	$idDriverReviewBonus ? "OR A.IDDRIVERREVIEWBONUS = ".$idDriverReviewBonus : "";
		$baseQuery				=	"SELECT IFNULL(SUM(TOTALREVIEWPOINT), 0) AS TOTALREVIEWPOINT, GROUP_CONCAT(STRARRIDDRIVERRATINGPOINT) AS STRARRIDDRIVERRATINGPOINT
									FROM (
										SELECT COUNT(A.IDDRIVERRATINGPOINT) * B.REVIEW5STARPOINT AS TOTALREVIEWPOINT, GROUP_CONCAT(A.IDDRIVERRATINGPOINT) AS STRARRIDDRIVERRATINGPOINT
										FROM t_driverratingpoint A
										LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
										WHERE A.POINT > 0 AND A.IDSOURCE IN (".$strArrIdSourceReviewBonus.") AND A.IDDRIVER = ".$idDriver." AND 
											  (DATE(A.DATERATINGPOINT) BETWEEN '".$dateStartBonusPeriod."' AND '".$dateEndBonusPeriod."' OR
											  (DATE(A.DATERATINGPOINT) < '".$dateStartBonusPeriod."' AND A.IDDRIVERREVIEWBONUS = 0 AND A.IDDRIVERREVIEWBONUS != -1) ".$con_idDriverReviewBonus.")
										GROUP BY A.IDDRIVER, A.IDSOURCE
									) AS A
									LIMIT 1";
		$query	=	$this->db->query($baseQuery);
		$row	=	$query->row_array();
		
		if(!$row) return false;
		return $row;
		
	}
	
	public function isDriverReviewBonusExist($idDriver, $idDriverReviewBonusPeriod){
		$baseQuery	=	"SELECT IDDRIVERREVIEWBONUS, IDWITHDRAWALRECAP, TARGETEXCEPTION FROM t_driverreviewbonus
						WHERE IDDRIVERREVIEWBONUSPERIOD  = ".$idDriverReviewBonusPeriod." AND IDDRIVER = ".$idDriver."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;
		
	}
	
	public function getDataRatingPointByDateInput($dateInput){
		$baseQuery	=	"SELECT IDDRIVERRATINGPOINT, IDDRIVER, IDSOURCE, IDDRIVERREVIEWBONUS, DATETIMEINPUT FROM t_driverratingpoint
						WHERE IDSOURCE IN (1,2) AND RATING = 5 AND DATETIMEINPUT >= '".$dateInput."'";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;
		
	}
	
	public function getIdReviewBonus($idDriver, $dateTimeInput){
		$baseQuery	=	"SELECT IDDRIVERREVIEWBONUS FROM t_driverreviewbonus A
						LEFT JOIN t_driverreviewbonusperiod B ON A.IDDRIVERREVIEWBONUSPERIOD = B.IDDRIVERREVIEWBONUSPERIOD
						WHERE A.IDDRIVER  = ".$idDriver." AND '".$dateTimeInput."' BETWEEN B.PERIODDATESTART AND B.PERIODDATEEND
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row['IDDRIVERREVIEWBONUS'];
		
	}	
}