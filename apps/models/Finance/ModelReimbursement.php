<?php
class ModelReimbursement extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function getDataReimbursement($page, $dataPerPage= 25, $startDate, $endDate, $keywordSearch, $viewRequestOnly){
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_receiptDate	=	$con_keywordSearch	=	"1=1";
		$con_status			=	"A.STATUS = 0";
		
		if(!$viewRequestOnly){
			$con_receiptDate	=	"DATE(A.RECEIPTDATE) BETWEEN '".$startDate."' AND '".$endDate."'";
			$con_keywordSearch	=	!isset($keywordSearch) || $keywordSearch == "" 
									? "1=1" 
									: ("A.DESCRIPTION LIKE '%".$keywordSearch."%' OR C.NAME LIKE '%".$keywordSearch."%' OR D.NAME LIKE '%".$keywordSearch."%' OR A.REQUESTBYNAME LIKE '%".$keywordSearch."%'
										OR A.INPUTBYNAME LIKE '%".$keywordSearch."%' OR A.APPROVALBYNAME LIKE '%".$keywordSearch."%' OR A.NOTES LIKE '%".$keywordSearch."%'");
			$con_status			=	"1=1";
		}
		
		$baseQuery	=	"SELECT A.IDREIMBURSEMENT, DATE_FORMAT(A.RECEIPTDATE, '%d %b %Y') AS DATERECEIPT, A.REQUESTBY, IFNULL(B.PARTNERTYPE, 'Other') AS REQUESTBYTYPE,
								CASE
									WHEN A.REQUESTBY = 1 THEN C.NAME
									WHEN A.REQUESTBY = 1 THEN D.NAME
									ELSE A.REQUESTBYNAME
								END AS REQUESTBYNAME,
								A.DESCRIPTION, A.INPUTBYNAME, DATE_FORMAT(A.INPUTDATETIME, '%d %b %Y %H:%i') AS INPUTDATETIME,
								IFNULL(A.APPROVALBYNAME, '-') AS APPROVALBYNAME, IFNULL(DATE_FORMAT(A.APPROVALDATETIME, '%d %b %Y %H:%i'), '-') AS APPROVALDATETIME, A.NOTES,
								A.NOMINAL, CONCAT('".URL_REIMBURSEMENT_IMAGE."', A.RECEIPTIMAGE) AS RECEIPTIMAGE, A.STATUS, A.INPUTMETHOD, A.IDWITHDRAWALRECAP
						 FROM t_reimbursement A
						 LEFT JOIN m_partnertype B ON A.REQUESTBY = B.IDPARTNERTYPE
						 LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
						 LEFT JOIN m_driver D ON A.IDDRIVER = D.IDDRIVER
						 WHERE ".$con_receiptDate." AND ".$con_keywordSearch."  AND ".$con_status." 
						 ORDER BY A.RECEIPTDATE ASC";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDREIMBURSEMENT", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}
	
	public function getDetailReimbursement($idReimbursement){		
		$query	=	$this->db->query(
						"SELECT DATE_FORMAT(RECEIPTDATE, '%d-%m-%Y') AS DATERECEIPT, REQUESTBY, IDVENDOR, IDDRIVER, REQUESTBYNAME, DESCRIPTION, NOMINAL, 
								CONCAT('".URL_REIMBURSEMENT_IMAGE."', RECEIPTIMAGE) AS RECEIPTIMAGEURL, RECEIPTIMAGE, IDWITHDRAWALRECAP, INPUTMETHOD, STATUS, NOTES
						FROM t_reimbursement
						WHERE IDREIMBURSEMENT = '".$idReimbursement."'
						LIMIT 1");
		$row	=	$query->row_array();

		if(isset($row)) return $row;		
		return false;		
	}
	
	public function getTotalReimbursementRequest(){
		$query	=	$this->db->query("SELECT COUNT(IDREIMBURSEMENT) AS TOTALREIMBURSEMENTREQUEST
									  FROM t_reimbursement
									  WHERE STATUS = 0
									  LIMIT 1");
		$row	=	$query->row_array();

		if(isset($row)) return $row['TOTALREIMBURSEMENTREQUEST'];
		return 0;
	}

}