<?php
class ModelTransferList extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function getDataUnprocessed(){
		$baseQuery	=	"SELECT A.IDTRANSFERLIST,
								CASE WHEN A.IDWITHDRAWAL != 0 THEN 'Withdrawal'
									 WHEN A.IDLOANDRIVERREQUEST != 0 THEN 'Loan Request'
									 WHEN A.IDCHARITYRECAPPROCESS != 0 THEN 'Charity'
									 ELSE '-'
								END AS TRANSFERTYPE,
								IFNULL(B.PARTNERTYPE, '') AS PARTNERTYPE, IFNULL(IF(A.IDPARTNERTYPE = 1, 'Ticket', E.DRIVERTYPE), '') AS SUBPARTNERTYPE,
								IFNULL(IF(A.IDPARTNERTYPE = 1, C.NAME, D.NAME), '') AS PARTNERNAME, F.BANKNAME, A.ACCOUNTNUMBER,
								A.ACCOUNTHOLDERNAME, A.AMOUNT, A.REMARK
						 FROM t_transferlist A
						 LEFT JOIN m_partnertype B ON A.IDPARTNERTYPE = B.IDPARTNERTYPE
						 LEFT JOIN m_vendor C ON A.IDPARTNER = C.IDVENDOR
						 LEFT JOIN m_driver D ON A.IDPARTNER = D.IDDRIVER
						 LEFT JOIN m_drivertype E ON D.IDDRIVERTYPE = E.IDDRIVERTYPE
						 LEFT JOIN m_bank F ON A.IDBANK = F.IDBANK
						 WHERE A.STATUS = 0
						 ORDER BY A.STATUSDATETIME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;	
	}
	
	public function getDataExcelTransferList($strArrIdTransferList){
		$baseQuery	=	"SELECT IDTRANSFERLIST, TRANSACTIONCODE, IDBANK, ACCOUNTNUMBER, ACCOUNTHOLDERNAME,
								AMOUNT, PARTNERCODE, REMARK, EMAILLIST
						 FROM t_transferlist
						 WHERE IDTRANSFERLIST IN (".$strArrIdTransferList.") AND STATUS = 0
						 ORDER BY STATUSDATETIME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;	
	}
	
	public function getDataOngoing($startDate, $endDate){
		$baseQuery	=	"SELECT COUNT(IDTRANSFERLIST) AS TOTALDATA, PAYROLLFILE, CONCAT('".URL_EXCEL_TRANSFER_LIST_FILE."', PAYROLLFILE) AS URLFILEDOWNLOAD,
								DOWNLOADUSER, DATE_FORMAT(DOWNLOADDATETIME, '%d %b %Y %H:%i') AS DOWNLOADDATETIME, '' AS TRANSFERLIST
						 FROM t_transferlist
						 WHERE STATUS = 1 AND DATE(DOWNLOADDATETIME) BETWEEN '".$startDate."' AND '".$endDate."'
						 GROUP BY PAYROLLFILE
						 ORDER BY DOWNLOADDATETIME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;	
	}
	
	public function getDataOnGoingTransferByFileName($payrollFileName){
		$baseQuery	=	"SELECT IF(A.IDWITHDRAWAL = 0, 'Loan Request', 'Withdrawal') AS TRANSFERTYPE,
								B.PARTNERTYPE, IF(A.IDPARTNERTYPE = 1, 'Ticket', E.DRIVERTYPE) AS SUBPARTNERTYPE,
								IF(A.IDPARTNERTYPE = 1, C.NAME, D.NAME) AS PARTNERNAME, F.BANKNAME, A.ACCOUNTNUMBER,
								A.ACCOUNTHOLDERNAME, A.AMOUNT, A.REMARK, A.IDTRANSFERLIST
						 FROM t_transferlist A
						 LEFT JOIN m_partnertype B ON A.IDPARTNERTYPE = B.IDPARTNERTYPE
						 LEFT JOIN m_vendor C ON A.IDPARTNER = C.IDVENDOR
						 LEFT JOIN m_driver D ON A.IDPARTNER = D.IDDRIVER
						 LEFT JOIN m_drivertype E ON D.IDDRIVERTYPE = E.IDDRIVERTYPE
						 LEFT JOIN m_bank F ON A.IDBANK = F.IDBANK
						 WHERE A.STATUS = 1 AND A.PAYROLLFILE = '".$payrollFileName."'
						 ORDER BY A.STATUSDATETIME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;	
	}
	
	public function getDetailTransferList($idTransferList){
		$baseQuery	=	sprintf(
							"SELECT IDTRANSFERLIST, IDPARTNERTYPE, IDPARTNER, IDLOANDRIVERREQUEST, IDWITHDRAWAL, IDCHARITYRECAPPROCESS, AMOUNT, STATUS
							FROM t_transferlist
							WHERE IDTRANSFERLIST = '".$idTransferList."'
							LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;
	}
	
	public function getDataFinished($startDate, $endDate){
		$baseQuery	=	"SELECT COUNT(IDTRANSFERLIST) AS TOTALDATA, PAYROLLFILE, DOWNLOADUSER, '' AS TRANSFERLIST
						 FROM t_transferlist
						 WHERE STATUS = 2 AND DATE(STATUSDATETIME) BETWEEN '".$startDate."' AND '".$endDate."'
						 GROUP BY PAYROLLFILE
						 ORDER BY STATUSDATETIME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;	
	}
	
	public function getDataFinishedTransferByFileName($payrollFileName){
		$baseQuery	=	"SELECT IF(A.IDWITHDRAWAL = 0, 'Loan Request', 'Withdrawal') AS TRANSFERTYPE,
								B.PARTNERTYPE, IF(A.IDPARTNERTYPE = 1, 'Ticket', E.DRIVERTYPE) AS SUBPARTNERTYPE,
								IF(A.IDPARTNERTYPE = 1, C.NAME, D.NAME) AS PARTNERNAME, F.BANKNAME, A.ACCOUNTNUMBER,
								A.ACCOUNTHOLDERNAME, A.AMOUNT, A.REMARK, DATE_FORMAT(STATUSDATETIME, '%d %b %Y %H:%i') AS STATUSDATETIME,
								CONCAT('".URL_HTML_TRANSFER_RECEIPT."', A.RECEIPTFILE) AS URLRECEIPTFILE
						 FROM t_transferlist A
						 LEFT JOIN m_partnertype B ON A.IDPARTNERTYPE = B.IDPARTNERTYPE
						 LEFT JOIN m_vendor C ON A.IDPARTNER = C.IDVENDOR
						 LEFT JOIN m_driver D ON A.IDPARTNER = D.IDDRIVER
						 LEFT JOIN m_drivertype E ON D.IDDRIVERTYPE = E.IDDRIVERTYPE
						 LEFT JOIN m_bank F ON A.IDBANK = F.IDBANK
						 WHERE A.STATUS = 2 AND A.PAYROLLFILE = '".$payrollFileName."'
						 ORDER BY A.STATUSDATETIME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;	
	}
	
}