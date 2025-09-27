<?php
class ModelLoanPrepaidCapital extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataLoanPrepaidCapital($page, $keyword, $dataPerPage= 20, $strArrIdDriver = ""){
		$ci			=& get_instance();
		$ci->load->model('MainOperation');

		$startid		=	($page * 1 - 1) * $dataPerPage;
		$condition		=	isset($keyword) && $keyword != "" && $keyword != null ?
							"(A.NAME LIKE '%".$keyword."%' OR A.ADDRESS LIKE '%".$keyword."%' OR A.PHONE LIKE '%".$keyword."%' OR A.EMAIL LIKE '%".$keyword."%')" : "1=1";
		$con_idDriverIn	=	isset($strArrIdDriver) && $strArrIdDriver != "" ? "A.IDDRIVER IN (".$strArrIdDriver.")" : "1=1";
		$con_totalLoan	=	isset($strArrIdDriver) && $strArrIdDriver != "" ? "1=1" : "C.TOTALLOAN + TOTALPREPAIDCAPITAL > 0";
		$baseQuery		=	"SELECT A.IDDRIVER, B.DRIVERTYPE, A.NAME, A.ADDRESS, A.PHONE, A.EMAIL, IFNULL(C.TOTALLOANPREPAIDCAPITAL, 0) AS TOTALLOANPREPAIDCAPITAL,
									IFNULL(C.TOTALLOAN, 0) AS TOTALLOAN, IFNULL(C.TOTALPREPAIDCAPITAL, 0) AS TOTALPREPAIDCAPITAL, IFNULL(C.MAXDATELOAN, '-') AS MAXDATELOAN,
									IFNULL(C.MAXDATEPREPAIDCAPITAL, '-') AS MAXDATEPREPAIDCAPITAL, '' AS LOANREQUESTDATA, '' AS PREPAIDCAPITALREQUESTDATA
							 FROM m_driver A
							 LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
							 LEFT JOIN (SELECT CA.IDDRIVER, SUM(IF(CA.TYPE = 'D', CA.AMOUNT, CA.AMOUNT * -1)) AS TOTALLOANPREPAIDCAPITAL,
											   SUM(IF(CB.STATUSLOANCAPITAL = 1, IF(CA.TYPE = 'D', CA.AMOUNT, CA.AMOUNT * -1), 0)) AS TOTALLOAN,
											   SUM(IF(CB.STATUSLOANCAPITAL = 2, IF(CA.TYPE = 'D', CA.AMOUNT, CA.AMOUNT * -1), 0)) AS TOTALPREPAIDCAPITAL,
											   DATE_FORMAT(MAX(IF(CB.STATUSLOANCAPITAL = 1, CA.DATETIMEINPUT, '')), '%d %b %Y') AS MAXDATELOAN,
											   DATE_FORMAT(MAX(IF(CB.STATUSLOANCAPITAL = 2, CA.DATETIMEINPUT, '')), '%d %b %Y') AS MAXDATEPREPAIDCAPITAL
										FROM t_loandriverrecord CA
										LEFT JOIN m_loantype CB ON CA.IDLOANTYPE = CB.IDLOANTYPE
										GROUP BY CA.IDDRIVER
										) AS C ON A.IDDRIVER = C.IDDRIVER
							 WHERE ".$condition." AND ".$con_idDriverIn." AND ".$con_totalLoan."
							 GROUP BY A.IDDRIVER
							 ORDER BY B.DRIVERTYPE DESC, A.NAME";
		$query			=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result			=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDDRIVER", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();	
	}
	
	public function getListBankAccountDriver($idDriver, $status = ''){
		$con_status	=	$status == '' ? "1=1" : "A.STATUS = ".$status;
		$baseQuery	=	"SELECT CONCAT(B.BANKNAME, ' [', A.ACCOUNTNUMBER, ' - ', A.ACCOUNTHOLDERNAME, ']') AS DETAILACCOUNT, A.IDBANKACCOUNTPARTNER
						 FROM t_bankaccountpartner A
						 LEFT JOIN m_bank B ON A.IDBANK = B.IDBANK
						 WHERE A.IDPARTNERTYPE = 2 AND A.IDPARTNER = ".$idDriver." AND ".$con_status."
						 ORDER BY B.BANKNAME, A.ACCOUNTNUMBER";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getDetailBankAccountDriver($idBankAccount){
		$baseQuery	=	"SELECT A.IDBANK, C.EMAIL, B.BANKNAME, A.ACCOUNTNUMBER, A.ACCOUNTHOLDERNAME
						 FROM t_bankaccountpartner A
						 LEFT JOIN m_bank B ON A.IDBANK = B.IDBANK
						 LEFT JOIN m_driver C ON A.IDPARTNER = C.IDDRIVER AND A.IDPARTNERTYPE = 2
						 WHERE A.IDBANKACCOUNTPARTNER = ".$idBankAccount."
						 LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row;
		return array(
			"IDBANK"			=>	0,
			"BANKNAME"			=>	'-',
			"ACCOUNTNUMBER"		=>	'-',
			"ACCOUNTHOLDERNAME"	=>	'-'
		);
	}
	
	public function getDataLoanPrepaidCapitalExcel($keyword){
		$condition		=	isset($keyword) && $keyword != "" && $keyword != null ?
							"(A.NAME LIKE '%".$keyword."%' OR A.ADDRESS LIKE '%".$keyword."%' OR A.PHONE LIKE '%".$keyword."%' OR A.EMAIL LIKE '%".$keyword."%')" : "1=1";
		$baseQuery		=	"SELECT B.DRIVERTYPE, A.NAME, IFNULL(C.TOTALLOANCAR, 0) AS TOTALLOANCAR,
									IFNULL(C.TOTALLOANPERSONAL, 0) AS TOTALLOANPERSONAL, IFNULL(C.TOTALPREPAIDCAPITAL, 0) AS TOTALPREPAIDCAPITAL
							 FROM m_driver A
							 LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
							 LEFT JOIN (SELECT IDDRIVER, SUM(IF(IDLOANTYPE = 1, IF(TYPE = 'D', AMOUNT, AMOUNT * -1), 0)) AS TOTALLOANCAR,
											   SUM(IF(IDLOANTYPE = 2, IF(TYPE = 'D', AMOUNT, AMOUNT * -1), 0)) AS TOTALLOANPERSONAL,
											   SUM(IF(IDLOANTYPE = 3, IF(TYPE = 'D', AMOUNT, AMOUNT * -1), 0)) AS TOTALPREPAIDCAPITAL
										FROM t_loandriverrecord
										GROUP BY IDDRIVER
										) AS C ON A.IDDRIVER = C.IDDRIVER
							 WHERE ".$condition."
							 GROUP BY A.IDDRIVER
							 ORDER BY B.DRIVERTYPE DESC, A.NAME";
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getDataTotalLoanPrepaidCapitalRequest(){
		$baseQuery	=	sprintf(
							"SELECT IFNULL(COUNT(IDLOANDRIVERREQUEST), 0) AS TOTALLOANPREPAIDCAPITALREQUEST,
									GROUP_CONCAT(IDDRIVER) AS STRARRIDDRIVERLOANPREPAIDCAPITALREQUEST
							 FROM t_loandriverrequest
							 WHERE STATUS = 0
							 LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return array(
				"TOTALLOANPREPAIDCAPITALREQUEST"			=>	0,
				"STRARRIDDRIVERLOANPREPAIDCAPITALREQUEST"	=>	""
		   );
		}
		
		return $row;
	}
	
	public function getLoanRequestData($idDriver){
		$baseQuery	=	sprintf(
							"SELECT A.IDLOANDRIVERREQUEST, B.LOANTYPE, A.NOTES, A.AMOUNT
							 FROM t_loandriverrequest A
							 LEFT JOIN m_loantype B ON A.IDLOANTYPE = B.IDLOANTYPE
							 WHERE A.IDDRIVER = ".$idDriver." AND A.STATUS = 0 AND B.STATUSLOANCAPITAL = 1
							 LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;		
	}
	
	public function getPrepaidCapitalRequestData($idDriver){
		$baseQuery	=	sprintf(
							"SELECT A.IDLOANDRIVERREQUEST, B.LOANTYPE, A.NOTES, A.AMOUNT
							 FROM t_loandriverrequest A
							 LEFT JOIN m_loantype B ON A.IDLOANTYPE = B.IDLOANTYPE
							 WHERE A.IDDRIVER = ".$idDriver." AND A.STATUS = 0 AND B.STATUSLOANCAPITAL = 2
							 LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;		
	}
	
	public function getDetailLoanPrepaidCapitalRequest($idLoanDriverRequest){
		$baseQuery	=	"SELECT B.STATUSLOANCAPITAL, B.LOANTYPE, A.NOTES, A.AMOUNT, C.NAME AS DRIVERNAME, DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT, D.ACCOUNTNUMBER,
								D.ACCOUNTHOLDERNAME, E.BANKNAME, CONCAT('".URL_BANK_LOGO."', E.BANKLOGO) AS BANKLOGO, C.EMAIL AS DRIVEREMAIL, A.IDDRIVER, D.IDBANK, A.IDLOANTYPE
						 FROM t_loandriverrequest A
						 LEFT JOIN m_loantype B ON A.IDLOANTYPE = B.IDLOANTYPE
						 LEFT JOIN m_driver C ON A.IDDRIVER = C.IDDRIVER
						 LEFT JOIN t_bankaccountpartner D ON A.IDBANKACCOUNTPARTNER = D.IDBANKACCOUNTPARTNER AND D.IDPARTNERTYPE = 2
						 LEFT JOIN m_bank E ON D.IDBANK = E.IDBANK
						 WHERE A.IDLOANDRIVERREQUEST = ".$idLoanDriverRequest." AND A.STATUS IN (0, 1)
						 LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;		
	}
	
	public function getHistoryLoanPrepaidCapital($idDriver, $typeLoanCapital, $page){
		$dataPerPage	=	999999;
		$startid		=	($page * 1 - 1) * $dataPerPage;
		$baseQuery		=	"SELECT DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT, B.LOANTYPE, A.DESCRIPTION, A.TYPE, A.AMOUNT, 0 AS SALDO, A.IDLOANDRIVERRECORD, 
									IF(C.RECEIPTFILE IS NOT NULL AND C.RECEIPTFILE != '', CONCAT('".URL_HTML_TRANSFER_RECEIPT."', C.RECEIPTFILE), '') AS RECEIPTFILE
							 FROM t_loandriverrecord A
							 LEFT JOIN m_loantype B ON A.IDLOANTYPE = B.IDLOANTYPE
							 LEFT JOIN t_transferlist C ON A.IDLOANDRIVERREQUEST = C.IDLOANDRIVERREQUEST AND C.IDLOANDRIVERREQUEST != 0
							 WHERE A.IDDRIVER = ".$idDriver." AND B.STATUSLOANCAPITAL = ".$typeLoanCapital."
							 ORDER BY A.DATETIMEINPUT DESC";
		$query			=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result			=	$query->result();
		
		if(isset($result)) return $result;
		return false;	
	}

	public function getLoanPrepaidCapitalBalance($idDriver, $typeLoanCapital, $idLoanType = false, $lastId = false){
		$con_lastId			=	$lastId == false ? "1=1" : "A.IDLOANDRIVERRECORD < ".$lastId;
		$con_typeLoanCapital=	$typeLoanCapital != false ? "B.STATUSLOANCAPITAL = ".$typeLoanCapital : "1=1";
		$con_idLoanType		=	$idLoanType != false ? "A.IDLOANTYPE = ".$idLoanType : "1=1";
		$baseQuery			=	sprintf(
									"SELECT SUM(IF(A.TYPE = 'D', A.AMOUNT, A.AMOUNT * -1)) AS TOTALBALANCE
									FROM t_loandriverrecord A
									LEFT JOIN m_loantype B ON A.IDLOANTYPE = B.IDLOANTYPE
									WHERE A.IDDRIVER = ".$idDriver." AND ".$con_typeLoanCapital." AND ".$con_idLoanType." AND ".$con_lastId."
									GROUP BY A.IDDRIVER"
								);
		$query				=	$this->db->query($baseQuery);
		$row				=	$query->row_array();
		
		if(!$row) return 0;
		return $row['TOTALBALANCE'];		
	}

	public function getDataActiveLoanRecap($idDriver){
		$baseQuery	=	"SELECT A.IDLOANDRIVERRECAP, B.LOANTYPE, IFNULL(DATE_FORMAT(A.LOANDATEDISBURSEMENT, '%d %b %Y'), '-') AS LOANDATEDISBURSEMENTSTR, A.LOANNOMINALPRINCIPAL,
								A.LOANNOMINALINTEREST, A.LOANNOMINALTOTAL, A.LOANNOMINALSALDO, A.LOANDURATIONMONTH, A.LOANINTERESTPERANNUM, A.LOANINSTALLMENTPERMONTH,
								DATE_FORMAT(CONCAT(A.LOANINSTALLMENTLASTPERIOD, '-01'), '%b %Y') AS LOANINSTALLMENTLASTPERIOD, '' AS DATAHISTORYLOANINSTALLMENT
						 FROM t_loandriverrecap A
						 LEFT JOIN m_loantype B ON A.IDLOANTYPE = B.IDLOANTYPE
						 WHERE A.IDDRIVER = ".$idDriver."
						 ORDER BY A.LOANDATEDISBURSEMENT";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return [];	
	}
	
	public function getDataHistoryLoanInstallment($idLoanDriverRecap){
		$baseQuery	=	"SELECT DATE_FORMAT(TRANSACTIONDATE, '%d %b %Y') AS TRANSACTIONDATE, DATE_FORMAT(CONCAT(INSTALLMENTPERIOD, '-01'), '%b %Y') AS INSTALLMENTPERIOD, DESCRIPTION, 
								NOMINALINSTALLMENT, NOMINALSALDO, INPUTUSER, DATE_FORMAT(INPUTDATETIME, '%d %b %Y') AS INPUTDATETIME
						 FROM t_loandriverinstallmenthistory
						 WHERE IDLOANDRIVERRECAP = ".$idLoanDriverRecap."
						 ORDER BY TRANSACTIONDATE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return [];	
	}
	
	public function getTotalLoanPrepaidCapitalRequest(){
		$query	=	$this->db->query(
					"SELECT COUNT(IDLOANDRIVERREQUEST) AS TOTALLOANPREPAIDCAPITALREQUEST
					  FROM t_loandriverrequest
					  WHERE STATUS = 0
					  LIMIT 1");
		$row	=	$query->row_array();

		if(isset($row)) return $row['TOTALLOANPREPAIDCAPITALREQUEST'];
		return 0;		
	}

	public function getTotalLoanInstallmentRequest(){
		$query	=	$this->db->query(
						"SELECT COUNT(IDLOANDRIVERINSTALLMENTREQUEST) AS TOTALLOANINSTALLMENTREQUEST
						FROM t_loandriverinstallmentrequest
						WHERE STATUS = 0
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['TOTALLOANINSTALLMENTREQUEST'];
		return 0;		
	}
	
	public function getDataLoanPerDriver($idDriver, $idLoanType, $startDate, $endDate){
		$con_idDriver	=	isset($idDriver) && $idDriver != "" && $idDriver != null ? "A.IDDRIVER = ".$idDriver : "1=1";
		$con_idLoanType	=	isset($idLoanType) && $idLoanType != "" && $idLoanType != null ? "A.IDLOANTYPE = ".$idLoanType : "1=1";
		$con_date		=	"DATE(A.DATETIMEINPUT) BETWEEN '".$startDate."' AND '".$endDate."'";
		
		$baseQuery		=	"SELECT DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUTSTR, 
									B.LOANTYPE, A.DESCRIPTION, A.TYPE, A.AMOUNT
							 FROM t_loandriverrecord A
							 LEFT JOIN m_loantype B ON A.IDLOANTYPE = B.IDLOANTYPE
							 WHERE ".$con_idDriver." AND ".$con_idLoanType."
							 ORDER BY A.DATETIMEINPUT";
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(isset($result)) return $result;
		return false;	
	}
	
	public function getDataLoanInstallmentRequest($idDriverType, $idDriver, $startDate, $endDate, $viewRequestOnly){
		$con_idDriverType	=	isset($idDriverType) && $idDriverType != "" && $idDriverType != null ? "B.IDDRIVERTYPE = ".$idDriverType : "1=1";
		$con_idDriver		=	isset($idDriver) && $idDriver != "" && $idDriver != null ? "A.IDDRIVER = ".$idDriver : "1=1";
		$con_date			=	"DATE(A.DATETIMEINPUT) BETWEEN '".$startDate."' AND '".$endDate."'";
		$con_viewRequestOnly=	"1=1";
		
		if($viewRequestOnly){
			$con_idDriverType	=	$con_idDriver	=	$con_date	=	"1=1";
			$con_viewRequestOnly=	"A.STATUS = 0";
		}
		
		$baseQuery	=	"SELECT DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT, C.DRIVERTYPE, B.NAME AS DRIVERNAME,
								D.LOANTYPE, A.NOTES, A.AMOUNT, A.STATUS, A.IDLOANDRIVERINSTALLMENTREQUEST
						 FROM t_loandriverinstallmentrequest A
						 LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
						 LEFT JOIN m_drivertype C ON B.IDDRIVERTYPE = C.IDDRIVERTYPE
						 LEFT JOIN m_loantype D ON A.IDLOANTYPE = D.IDLOANTYPE
						 WHERE ".$con_idDriverType." AND ".$con_idDriver." AND ".$con_date." AND ".$con_viewRequestOnly."
						 ORDER BY A.DATETIMEINPUT";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;	
	}
	
	public function getDetailLoanInstallmentRequest($idLoanInstallmentRequest){
		$baseQuery	=	"SELECT DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT, C.DRIVERTYPE, B.NAME AS DRIVERNAME,
								D.LOANTYPE, A.NOTES, A.AMOUNT, A.STATUS, DATE_FORMAT(A.DATETIMEAPPROVE, '%d %b %Y %H:%i') AS DATETIMEAPPROVE,
								IF(A.USERAPPROVE IS NULL OR A.USERAPPROVE = '', '-', A.USERAPPROVE) AS USERAPPROVE,
								CONCAT('".URL_TRANSFER_RECEIPT."', A.FILETRANSFERRECEIPT) AS FILETRANSFERRECEIPT, A.IDDRIVER, D.STATUSLOANCAPITAL,
								A.IDLOANTYPE
						 FROM t_loandriverinstallmentrequest A
						 LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
						 LEFT JOIN m_drivertype C ON B.IDDRIVERTYPE = C.IDDRIVERTYPE
						 LEFT JOIN m_loantype D ON A.IDLOANTYPE = D.IDLOANTYPE
						 WHERE A.IDLOANDRIVERINSTALLMENTREQUEST = ".$idLoanInstallmentRequest."
						 LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row;
		return false;	
	}
	
	public function getDataRecapSaldoLoanDriver($idDriver, $idLoanType){
		$baseQuery	=	"SELECT IDLOANDRIVERRECAP, LOANNOMINALSALDO, LOANINSTALLMENTPERMONTH, LOANINSTALLMENTLASTPERIOD
						FROM t_loandriverrecap
						WHERE IDDRIVER = ".$idDriver." AND LOANNOMINALSALDO > 0 AND IDLOANTYPE = ".$idLoanType." AND LOANSTATUS = 1
						GROUP BY LOANNOMINALSALDO ASC";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;
	}
}