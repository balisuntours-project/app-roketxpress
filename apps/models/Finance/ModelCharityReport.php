<?php
class ModelCharityReport extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataCharityReport($page, $dataPerPage= 25, $startDate, $endDate, $viewUnprocessed, $searchKeyword = '', $idCharityRecapProcess = false){
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_date			=	$viewUnprocessed || $idCharityRecapProcess ? "1=1" : "DATE(A.DATETIME) BETWEEN '".$startDate."' AND '".$endDate."'";
		$con_searchKeyword	=	isset($searchKeyword) && $searchKeyword != '' ? "(A.NAME LIKE '%".$searchKeyword."%' OR A.DESCRIPTION LIKE '%".$searchKeyword."%')" : "1=1";
		$con_unprocessed	=	$viewUnprocessed ? "A.IDCHARITYRECAPPROCESS = 0" : "1=1";
		$con_idRecapCharity	=	$idCharityRecapProcess ? "A.IDCHARITYRECAPPROCESS = ".$idCharityRecapProcess : "1=1";
		$baseQuery			=	"SELECT A.IDCHARITY, A.IDCHARITYRECAPPROCESS, DATE_FORMAT(A.DATETIME, '%d %b %Y %H:%i') AS DATETIMESTR, A.CONTRIBUTORTYPE, A.NAME, A.DESCRIPTION, A.NOMINAL,
										IFNULL(B.PROCESSUSER, '-') AS PROCESSUSER, IFNULL(DATE_FORMAT(B.PROCESSDATE, '%d %b %Y %H:%i'), '-') AS PROCESSDATETIME, A.INPUTTYPE,
										DATE_FORMAT(A.DATETIME, '%Y-%m-%d') AS DATECHARITY
								FROM t_charity A
								LEFT JOIN t_charityrecapprocess B ON A.IDCHARITYRECAPPROCESS = B.IDCHARITYRECAPPROCESS
								WHERE ".$con_date." AND ".$con_searchKeyword." AND ".$con_unprocessed." AND ".$con_idRecapCharity." AND A.STATUS >= 0
								ORDER BY A.DATETIME";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDCHARITY", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}
	
	public function getDataLastTransferCharity(){
		$query	=	$this->db->query(
						"SELECT IDBANK, ACCOUNTNUMBER, ACCOUNTHOLDERNAME, PARTNERCODE, REPLACE(EMAILLIST, '".MAILBOX_USERNAME.",', '') AS EMAILLIST
						FROM t_transferlist
						WHERE IDCHARITYRECAPPROCESS != 0
						ORDER BY IDTRANSFERLIST DESC
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return [
			"IDBANK"			=>	1,
			"ACCOUNTNUMBER"		=>	"",
			"ACCOUNTHOLDERNAME"	=>	"",
			"PARTNERCODE"		=>	"CHR0000",
			"EMAILLIST"			=>	""
		];
	}
	
	public function getDetailManualCharity($idCharity){
		$query	=	$this->db->query(
						"SELECT CONTRIBUTORTYPE, NAME, DESCRIPTION, NOMINAL, DATE_FORMAT(DATETIME, '%d-%m-%Y') AS DATECHARITY FROM t_charity
						WHERE IDCHARITY = '".$idCharity."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;
	}
	
	public function checkDataExists($charityDate, $contributorType, $contributorName, $charityNominal, $idCharity = false){
		$con_idCharity	=	$idCharity != false && $idCharity != 0 ? "IDCHARITY != ".$idCharity : "1=1";
		$query			=	$this->db->query(
								"SELECT IDCHARITY FROM t_charity
								WHERE LEFT(DATETIME, 10) = '".$charityDate."' AND CONTRIBUTORTYPE = '".$contributorType."' AND NAME = '".$contributorName."' AND
									  NOMINAL = '".$charityNominal."' AND ".$con_idCharity." AND STATUS >= 0
								LIMIT 1"
							);
		$row			=	$query->row_array();

		if(isset($row)) return $row;
		return false;
	}

	public function getDataCharityProcessTransfer($page, $dataPerPage= 10){
		$ci			=&	get_instance();
		$ci->load->model('MainOperation');

		$startid	=	($page * 1 - 1) * $dataPerPage;
		$baseQuery	=	"SELECT A.IDCHARITYRECAPPROCESS, IFNULL(DATE_FORMAT(A.DATEPERIODSTART, '%d %b %Y'), '-') AS DATEPERIODSTARTSTR, IFNULL(DATE_FORMAT(A.DATEPERIODEND, '%d %b %Y'), '-') AS DATEPERIODENDSTR,
								IFNULL(DATE_FORMAT(A.PROCESSDATE, '%d %b %Y %H:%i'), '-') AS PROCESSDATETIME, IFNULL(A.PROCESSUSER, '-') AS PROCESSUSER, C.BANKNAME, B.ACCOUNTNUMBER, B.ACCOUNTHOLDERNAME,
								REPLACE(B.EMAILLIST, '".MAILBOX_USERNAME.",', '') AS EMAILLIST, A.TOTALCHARITY, A.TOTALCHARITYNOMINAL, B.STATUS AS STATUSTRANSFER, A.DATEPERIODSTART, A.DATEPERIODEND, '' AS URLEXCELREPORT
						FROM t_charityrecapprocess A
						LEFT JOIN t_transferlist B ON A.IDCHARITYRECAPPROCESS = B.IDCHARITYRECAPPROCESS
						LEFT JOIN m_bank C ON B.IDBANK = C.IDBANK
						ORDER BY A.PROCESSDATE DESC";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDCHARITYRECAPPROCESS", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}
	
}