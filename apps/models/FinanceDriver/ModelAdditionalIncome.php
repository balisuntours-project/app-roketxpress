<?php
class ModelAdditionalIncome extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataAdditionalIncomeRecap($page, $dataPerPage= 25, $month, $year, $searchKeyword){
		$ci					=&	get_instance();
		$ci->load->model('MainOperation');
		
		$startid			=	($page * 1 - 1) * $dataPerPage;
		$monthYear			=	$year."-".$month;
		$con_monthYear		=	"A.PERIOD = '".$monthYear."'";
		$con_searchKeyword	=	isset($searchKeyword) && $searchKeyword != '' ? "(B.NAME LIKE '%".$searchKeyword."%' OR A.EXCEPTIONREASON LIKE '%".$searchKeyword."%')" : "1=1";
		$field_reviewPoint	=	$monthYear == date('Y-m') ? 0 : "C.POINT";
		$baseQuery			=	"SELECT A.IDADDITIONALINCOMERECAP, B.NAME AS DRIVERNAME, A.EXCEPTIONREASON,
										IF(A.DATELASTPAYMENT = '0000-00-00', '-', DATE_FORMAT(A.DATELASTPAYMENT, '%d %b %Y')) AS DATELASTPAYMENT,
										A.NUMBEROFPAYMENT, A.NOMINAL, ".$field_reviewPoint." AS REVIEWPOINT
								 FROM t_additionalincomerecap A
								 LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
								 LEFT JOIN t_driverratingpoint C ON A.IDDRIVERRATINGPOINT = C.IDDRIVERRATINGPOINT
								 WHERE ".$con_monthYear." AND ".$con_searchKeyword."
								 ORDER BY DRIVERNAME ASC";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDADDITIONALINCOMERECAP", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}

	public function getDataAdditionalIncome($page, $dataPerPage= 25, $startDate, $endDate, $searchKeyword, $viewRequestOnly){
		$ci					=&	get_instance();
		$ci->load->model('MainOperation');
		
		$startid	=	($page * 1 - 1) * $dataPerPage;
		$con_date	=	$con_searchKeyword	=	$con_viewRequestOnly	=	"1=1";
		
		if(!$viewRequestOnly){
			$con_date			=	"DATE(A.INCOMEDATE) BETWEEN '".$startDate."' AND '".$endDate."'";
			$con_searchKeyword	=	isset($searchKeyword) && $searchKeyword != '' ? "(B.NAME LIKE '%".$searchKeyword."%' OR A.DESCRIPTION LIKE '%".$searchKeyword."%')" : "1=1";
		} else {
			$con_viewRequestOnly=	"A.APPROVALSTATUS = 0";
		}
		
		$baseQuery	=	"SELECT A.IDADDITIONALINCOME, A.IDDRIVERRATINGPOINT, A.IDDRIVER, DATE_FORMAT(A.INCOMEDATE, '%d-%m-%Y') AS INCOMEDATE,
								DATE_FORMAT(A.INCOMEDATE, '%d %b %Y') AS INCOMEDATESTR, B.NAME AS DRIVERNAME, A.DESCRIPTION, A.INCOMENOMINAL,
								A.INPUTTYPE, A.INPUTUSER, DATE_FORMAT(A.INPUTDATETIME, '%d %b %Y %H:%i') AS INPUTDATETIME, A.IMAGERECEIPT,
								CONCAT('".URL_ADDITIONAL_INCOME_IMAGE."', A.IMAGERECEIPT) AS IMAGERECEIPTURL, A.APPROVALSTATUS,
								IF(A.APPROVALUSER IS NULL OR A.APPROVALUSER = '', '-', A.APPROVALUSER) AS APPROVALUSER,
								IF(A.APPROVALDATETIME IS NULL OR A.APPROVALDATETIME = '', '-', DATE_FORMAT(A.APPROVALDATETIME, '%d %b %Y %H:%i')) AS APPROVALDATETIME
						 FROM t_additionalincome A
						 LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
						 WHERE ".$con_date." AND ".$con_searchKeyword." AND ".$con_viewRequestOnly."
						 ORDER BY A.INCOMEDATE ASC";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDADDITIONALINCOME", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}
	
	public function getDataAdditionalIncomeByIdDriver($idDriver){
		$dateTime		=	new DateTime();
		$dateTime->modify('-3 months');
		$monthYearStart	=	$dateTime->format('Y-m');
		$baseQuery		=	"SELECT COUNT(IDADDITIONALINCOME) AS NUMBEROFADDITIONALINCOME, LEFT (INCOMEDATE, 7) AS YEARMONTH, GROUP_CONCAT(IDADDITIONALINCOME) AS STRARRIDADDITIONALINCOME,
									SUM(INCOMENOMINAL) AS TOTALINCOMENOMINAL, IFNULL(MAX(INCOMEDATE), '0000-00-00') AS MAXINCOMEDATE
							 FROM t_additionalincome
							 WHERE IDDRIVER = ".$idDriver." AND INCOMEDATE >= '".$monthYearStart."-01' AND APPROVALSTATUS = 1
							 GROUP BY LEFT (INCOMEDATE, 7)";
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getIdDriverRatingPoint($idDriver, $yearMonth){
		$baseQuery	=	"SELECT IDDRIVERRATINGPOINT FROM t_driverratingpoint
						 WHERE IDDRIVER = ".$idDriver." AND LEFT(DATERATINGPOINT, 7) = '".$yearMonth."' AND STATUSADDITIONALINCOME = 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row['IDDRIVERRATINGPOINT'];
		return 0;
	}

	public function getDataAdditionalIncomePointRate(){
		$baseQuery	=	"SELECT IDADDITIONALINCOMERATE, NOMINALMIN, NOMINALMAX, REVIEWPOINT
						 FROM t_additionalincomerate
						 ORDER BY NOMINALMIN";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;	
	}
	
	public function getPointReviewAdditionalIncome($nominal){
		$baseQuery	=	"SELECT IDADDITIONALINCOMERATE, REVIEWPOINT FROM t_additionalincomerate
						WHERE ".$nominal." >= NOMINALMIN AND ".$nominal." <= NOMINALMAX
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row;
		return [
			"IDADDITIONALINCOMERATE"=>	0,
			"REVIEWPOINT"			=>	0
		];	
	}
	
	public function getTotalAdditionalIncomeApproval(){
		$baseQuery	=	"SELECT COUNT(IDADDITIONALINCOME) AS TOTALADDITIONALINCOMEAPPROVAL FROM t_additionalincome
						WHERE APPROVALSTATUS = 0
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row['TOTALADDITIONALINCOMEAPPROVAL'];
		return 0;	
	}
}