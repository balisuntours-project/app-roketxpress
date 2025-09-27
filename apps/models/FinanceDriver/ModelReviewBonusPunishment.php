<?php
class ModelReviewBonusPunishment extends CI_Model {
	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataAllDriverReport($page, $dataPerPage= 25, $idDriverType, $idDriver, $month, $year){
		$ci					=&	get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_idDriverType	=	isset($idDriverType) && $idDriverType != "" ? "C.IDDRIVERTYPE = ".$idDriverType : "1=1";
		$con_idDriver		=	isset($idDriver) && $idDriver != "" ? "A.IDDRIVER = ".$idDriver : "1=1";
		$baseQuery			=	"SELECT D.DRIVERTYPE, C.NAME AS DRIVERNAME, DATE_FORMAT(B.PERIODDATESTART, '%d %b %Y') AS PERIODDATESTART,
										DATE_FORMAT(B.PERIODDATEEND, '%d %b %Y') AS PERIODDATEEND, IF(A.TARGETEXCEPTION != -1, A.TARGETEXCEPTION, B.TOTALTARGET) AS TOTALTARGET,
										A.TARGETEXCEPTION, A.TOTALREVIEWPOINT, B.BONUSRATE, A.NOMINALBONUS, A.NOMINALPUNISHMENT, A.NOMINALRESULT, IF(A.IDWITHDRAWALRECAP != 0, 1, 0) AS STATUSWITHDRAWAL,
										A.IDDRIVERREVIEWBONUS
								FROM t_driverreviewbonus A
								LEFT JOIN t_driverreviewbonusperiod B ON A.IDDRIVERREVIEWBONUSPERIOD = B.IDDRIVERREVIEWBONUSPERIOD
								LEFT JOIN m_driver C ON A.IDDRIVER = C.IDDRIVER
								LEFT JOIN m_drivertype D ON C.IDDRIVERTYPE = D.IDDRIVERTYPE
								WHERE B.PERIODMONTHYEAR = '".$year."-".$month."' AND ".$con_idDriverType." AND ".$con_idDriver."
								ORDER BY D.DRIVERTYPE, C.NAME";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDDRIVERREVIEWBONUS", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}

	public function getReviewBonusPeriodTarget($idDriverReviewBonus){
		$baseQuery	=	"SELECT A.IDDRIVERREVIEWBONUSPERIOD, IFNULL(B.TOTALTARGET, 0) AS TOTALTARGET, A.TOTALREVIEWPOINT, B.BONUSRATE FROM t_driverreviewbonus A
						LEFT JOIN t_driverreviewbonusperiod B ON A.IDDRIVERREVIEWBONUSPERIOD = B.IDDRIVERREVIEWBONUSPERIOD
						WHERE A.IDDRIVERREVIEWBONUS = '".$idDriverReviewBonus."'
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row;
		return [
			'IDDRIVERREVIEWBONUSPERIOD'	=>	0,
			'TOTALTARGET'				=>	0
		];
	}

	public function getDataPeriodTargetRate($page, $dataPerPage= 25, $year){
		$ci			=&	get_instance();
		$ci->load->model('MainOperation');

		$startid	=	($page * 1 - 1) * $dataPerPage;
		$baseQuery	=	"SELECT A.IDDRIVERREVIEWBONUSPERIOD, A.PERIODMONTHYEAR, DATE_FORMAT(CONCAT(A.PERIODMONTHYEAR, '-01'), '%M %Y') AS PERIODMONTHYEARSTR,
								DATE_FORMAT(A.PERIODDATESTART, '%d %b %Y') AS PERIODDATESTART, DATE_FORMAT(A.PERIODDATESTART, '%d-%m-%Y') AS PERIODDATESTARTVAL,
								DATE_FORMAT(A.PERIODDATEEND, '%d %b %Y') AS PERIODDATEEND, DATE_FORMAT(A.PERIODDATEEND, '%d-%m-%Y') AS PERIODDATEENDVAL,
								A.BONUSRATE, A.TOTALTARGET, IFNULL(COUNT(B.IDWITHDRAWALRECAP), 0) AS TOTALDRIVERBONUSPUNISHMENT,
								IFNULL(SUM(IF(B.IDWITHDRAWALRECAP != 0, 1, 0)), 0) AS TOTALREVIEWBONUSWITHDRAWN, IFNULL(SUM(B.NOMINALBONUS), 0) AS TOTALBONUS,
								IFNULL(SUM(B.NOMINALPUNISHMENT), 0) AS TOTALPUNISHMENT, IFNULL(SUM(B.NOMINALRESULT), 0) AS TOTALRESULT
						FROM t_driverreviewbonusperiod A
						LEFT JOIN t_driverreviewbonus B ON A.IDDRIVERREVIEWBONUSPERIOD = B.IDDRIVERREVIEWBONUSPERIOD
						WHERE LEFT(A.PERIODMONTHYEAR, 4) = '".$year."'
						GROUP BY A.IDDRIVERREVIEWBONUSPERIOD
						ORDER BY A.PERIODMONTHYEAR";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDDRIVERREVIEWBONUSPERIOD", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}

	public function isReviewBonusWithdrawn($idDriverReviewBonusPeriod){
		$baseQuery	=	"SELECT IFNULL(SUM(IF(IDWITHDRAWALRECAP != 0, 1, 0)), 0) AS TOTALREVIEWBONUSWITHDRAWN FROM t_driverreviewbonus
						WHERE IDDRIVERREVIEWBONUSPERIOD = '".$idDriverReviewBonusPeriod."'
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) {
			$totalReviewBonusWithdrawn	=	$row['TOTALREVIEWBONUSWITHDRAWN'];
			
			if($totalReviewBonusWithdrawn > 0){
				return true;
			} else {
				return false;
			}
		}
		return false;
	}

	public function getDataNextDriverReviewBonusPeriod($periodMonthYear){
		$periodMonthYear=	str_replace("-", "", $periodMonthYear);
		$baseQuery		=	"SELECT IDDRIVERREVIEWBONUSPERIOD FROM t_driverreviewbonusperiod
							WHERE REPLACE(PERIODMONTHYEAR, '-', '') > ".$periodMonthYear."
							LIMIT 1";
		$query			=	$this->db->query($baseQuery);
		$row			=	$query->row_array();
		
		if(isset($row)) return $row;
		return false;
	}	
}