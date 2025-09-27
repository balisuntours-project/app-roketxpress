<?php
class ModelRecapPerDriver extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataAllDriverRecap($page, $dataPerPage= 25, $idDriverType, $idDriver){
		
		$ci					=&	get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_idDriverType	=	!isset($idDriverType) || $idDriverType == "" ? "1=1" : "A.IDDRIVERTYPE = ".$idDriverType;
		$con_idDriver		=	!isset($idDriver) || $idDriver == "" ? "1=1" : "A.IDDRIVER = ".$idDriver;
		$baseQuery			=	"SELECT B.DRIVERTYPE, A.NAME AS DRIVERNAME, COUNT(C.IDFEE) AS TOTALSCHEDULE, F.TOTALADDITIONALCOST, IFNULL(G.TOTALREIMBURSEMENT, 0) AS TOTALREIMBURSEMENT,
										IFNULL(H.TOTALREVIEWBONUSPUNISHMENT, 0) AS TOTALREVIEWBONUSPUNISHMENT, SUM(C.FEENOMINAL) AS TOTALFEE, D.TOTALPREPAIDCAPITAL,
										IFNULL(E.TOTALCOLLECTPAYMENT, 0) AS TOTALCOLLECTPAYMENT, A.IDDRIVER
								FROM m_driver A
								LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
								LEFT JOIN t_fee C ON A.IDDRIVER = C.IDDRIVER AND C.WITHDRAWSTATUS = 0 AND C.IDWITHDRAWALRECAP = 0
								LEFT JOIN (SELECT DA.IDDRIVER, SUM(IF(DB.STATUSLOANCAPITAL = 2, IF(DA.TYPE = 'D', DA.AMOUNT, DA.AMOUNT * -1), 0)) AS TOTALPREPAIDCAPITAL
										  FROM t_loandriverrecord DA
										  LEFT JOIN m_loantype DB ON DA.IDLOANTYPE = DB.IDLOANTYPE
										  GROUP BY DA.IDDRIVER
										  ) AS D ON A.IDDRIVER = D.IDDRIVER
								LEFT JOIN (SELECT EA.IDDRIVER, SUM(EB.AMOUNTIDR) AS TOTALCOLLECTPAYMENT
										  FROM t_collectpayment EA
										  LEFT JOIN t_reservationpayment EB ON EA.IDRESERVATIONPAYMENT = EB.IDRESERVATIONPAYMENT
										  WHERE EA.IDDRIVER != 0 AND EA.STATUSSETTLEMENTREQUEST != 2
										  GROUP BY EA.IDDRIVER
										  ) AS E ON A.IDDRIVER = E.IDDRIVER
								LEFT JOIN (SELECT IDDRIVER, SUM(NOMINAL) AS TOTALADDITIONALCOST
										  FROM t_reservationadditionalcost
										  WHERE IDWITHDRAWALRECAP = 0 AND STATUSAPPROVAL = 1
										  GROUP BY IDDRIVER) F ON A.IDDRIVER = F.IDDRIVER
								LEFT JOIN (SELECT IDDRIVER, SUM(NOMINAL) AS TOTALREIMBURSEMENT
										  FROM t_reimbursement
										  WHERE IDWITHDRAWALRECAP = 0 AND STATUS = 1 AND IDDRIVER != 0
										  GROUP BY IDDRIVER) G ON A.IDDRIVER = G.IDDRIVER
								LEFT JOIN (SELECT HA.IDDRIVER, SUM(HA.NOMINALRESULT) AS TOTALREVIEWBONUSPUNISHMENT
										  FROM t_driverreviewbonus HA
										  LEFT JOIN t_driverreviewbonusperiod HB ON HA.IDDRIVERREVIEWBONUSPERIOD = HB.IDDRIVERREVIEWBONUSPERIOD
										  WHERE HA.IDWITHDRAWALRECAP = 0 AND HB.PERIODDATEEND <= '".date('Y-m-d')."'
										  GROUP BY HA.IDDRIVER) H ON A.IDDRIVER = H.IDDRIVER
								WHERE ".$con_idDriverType." AND ".$con_idDriver." AND A.NEWFINANCESCHEME = 1
								GROUP BY A.IDDRIVER
								ORDER BY B.DRIVERTYPE, A.NAME";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDDRIVER", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}

	public function getDataFeePerPeriod($page, $dataPerPage= 25, $startDate, $endDate){
		
		$ci					=&	get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$baseQuery			=	"SELECT B.DRIVERTYPE, A.NAME AS DRIVERNAME, COUNT(D.IDRESERVATIONDETAILS) AS TOTALSCHEDULE,
										SUM(D.NOMINAL) AS TOTALFEE, A.IDDRIVER
								FROM m_driver A
								LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
								LEFT JOIN t_scheduledriver C ON A.IDDRIVER = C.IDDRIVER
								LEFT JOIN t_reservationdetails D ON C.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS AND
										  D.SCHEDULEDATE BETWEEN '".$startDate."' AND '".$endDate."' AND D.STATUS = 1
								WHERE A.STATUS = 1
								GROUP BY A.IDDRIVER
								ORDER BY B.DRIVERTYPE, A.NAME"; 
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDDRIVER", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}
	
	public function getDetailDriver($idDriver){
		
		$query		=	$this->db->query("SELECT B.DRIVERTYPE, '' AS INITIALNAME, A.NAME, A.ADDRESS, A.PHONE, A.EMAIL,
												 IFNULL(CONCAT(C.MINCAPACITY, ' - ', C.MAXCAPACITY), '-') AS CARCAPACITYDETAIL,
												 IFNULL(C.CARCAPACITYNAME, '-') AS CARCAPACITYNAME
										  FROM m_driver A
										  LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
										  LEFT JOIN m_carcapacity C ON A.IDCARCAPACITY = C.IDCARCAPACITY
										  WHERE A.IDDRIVER = ".$idDriver."
										  LIMIT 1"
										);
		$row		=	$query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return array(
					"DRIVERTYPE"			=>	"-",
					"INITIALNAME"			=>	"-",
					"NAME"					=>	"-",
					"ADDRESS"				=>	"-",
					"PHONE"					=>	"-",
					"EMAIL"					=>	"-",
					"CARCAPACITYDETAIL"		=>	"-",
					"CARCAPACITYNAME"		=>	"-"
				);
		
	}
	
	public function getDataActiveBankAccountDriver($idDriver){
		
		$query	=	$this->db->query("SELECT A.IDBANK, A.ACCOUNTNUMBER, A.ACCOUNTHOLDERNAME, B.BANKNAME, CONCAT('".URL_BANK_LOGO."', B.BANKLOGO) AS BANKLOGO
									  FROM t_bankaccountpartner A
									  LEFT JOIN m_bank B ON A.IDBANK = B.IDBANK
									  WHERE A.IDPARTNERTYPE = 2 AND A.IDPARTNER = ".$idDriver." AND A.STATUS = 1
									  LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return array(
					"IDBANK"			=>	0,
					"ACCOUNTNUMBER"		=>	"-",
					"ACCOUNTHOLDERNAME"	=>	"-",
					"BANKNAME"			=>	"-",
					"BANKLOGO"			=>	URL_BANK_LOGO."default.png"
				);
		
	}
	
	public function getDataRecapPerDriver($idDriver){
		$baseQuery	=	"SELECT COUNT(C.IDFEE) AS TOTALSCHEDULE, IFNULL(SUM(C.FEENOMINAL), 0) AS TOTALFEE, IFNULL(F.TOTALADDITIONALCOST, 0) AS TOTALADDITIONALCOST,
								IFNULL(F.TOTALSCHEDULEWITHCOST, 0) AS TOTALSCHEDULEWITHCOST, IFNULL(G.TOTALREIMBURSEMENT, 0) AS TOTALREIMBURSEMENT,
								IFNULL(G.TOTALDATAREIMBURSEMENT, 0) AS TOTALDATAREIMBURSEMENT, IFNULL(H.TOTALREVIEWBONUSPUNISHMENT, 0) AS TOTALREVIEWBONUSPUNISHMENT,
								IFNULL(H.TOTALREVIEWBONUSPERIOD, 0) AS TOTALREVIEWBONUSPERIOD, IFNULL(D.TOTALPREPAIDCAPITAL, 0) AS TOTALPREPAIDCAPITAL, D.MAXDATEPREPAIDCAPITAL,
								IFNULL(E.TOTALCOLLECTPAYMENT, 0) AS TOTALCOLLECTPAYMENT, IFNULL(E.TOTALSCHEDULEWITHCOLLECTPAYMENT, 0) AS TOTALSCHEDULEWITHCOLLECTPAYMENT,
								IFNULL(D.TOTALLOAN, 0) AS TOTALLOAN, IFNULL(D.TOTALLOANCAR, 0) AS TOTALLOANCAR, IFNULL(D.TOTALLOANPERSONAL, 0) AS TOTALLOANPERSONAL,
								IFNULL(DATE_FORMAT(I.LASTWITHDRAWALDATE, '%d %b %Y'), '-') AS LASTWITHDRAWALDATE
						FROM m_driver A
						LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
						LEFT JOIN t_fee C ON A.IDDRIVER = C.IDDRIVER AND C.WITHDRAWSTATUS = 0 AND C.IDWITHDRAWALRECAP = 0
						LEFT JOIN (SELECT DA.IDDRIVER, SUM(IF(DB.STATUSLOANCAPITAL = 2, IF(DA.TYPE = 'D', DA.AMOUNT, DA.AMOUNT * -1), 0)) AS TOTALPREPAIDCAPITAL,
										  SUM(IF(DB.STATUSLOANCAPITAL = 1, IF(DA.TYPE = 'D', DA.AMOUNT, DA.AMOUNT * -1), 0)) AS TOTALLOAN,
										  SUM(IF(DB.STATUSLOANCAPITAL = 1 AND DB.IDLOANTYPE = 1, IF(DA.TYPE = 'D', DA.AMOUNT, DA.AMOUNT * -1), 0)) AS TOTALLOANCAR,
										  SUM(IF(DB.STATUSLOANCAPITAL = 1 AND DB.IDLOANTYPE = 2, IF(DA.TYPE = 'D', DA.AMOUNT, DA.AMOUNT * -1), 0)) AS TOTALLOANPERSONAL,
										  IFNULL(DATE_FORMAT(MAX(IF(DB.STATUSLOANCAPITAL = 2, DA.DATETIMEINPUT, '')), '%d %b %Y'), '-') AS MAXDATEPREPAIDCAPITAL
								  FROM t_loandriverrecord DA
								  LEFT JOIN m_loantype DB ON DA.IDLOANTYPE = DB.IDLOANTYPE
								  WHERE DA.IDDRIVER = ".$idDriver."
								  GROUP BY DA.IDDRIVER
								  ) AS D ON A.IDDRIVER = D.IDDRIVER
						LEFT JOIN (SELECT EA.IDDRIVER, SUM(EB.AMOUNTIDR) AS TOTALCOLLECTPAYMENT, COUNT(EA.IDCOLLECTPAYMENT) AS TOTALSCHEDULEWITHCOLLECTPAYMENT
								  FROM t_collectpayment EA
								  LEFT JOIN t_reservationpayment EB ON EA.IDRESERVATIONPAYMENT = EB.IDRESERVATIONPAYMENT
								  WHERE EA.IDDRIVER = ".$idDriver." AND EA.STATUSSETTLEMENTREQUEST != 2
								  GROUP BY EA.IDDRIVER
								  ) AS E ON A.IDDRIVER = E.IDDRIVER
						LEFT JOIN (SELECT IDDRIVER, SUM(NOMINAL) AS TOTALADDITIONALCOST, COUNT(IDRESERVATIONADDITIONALCOST) AS TOTALSCHEDULEWITHCOST
								  FROM t_reservationadditionalcost
								  WHERE IDDRIVER = ".$idDriver." AND IDWITHDRAWALRECAP = 0 AND STATUSAPPROVAL = 1
								  GROUP BY IDDRIVER) F ON A.IDDRIVER = F.IDDRIVER
						LEFT JOIN (SELECT IDDRIVER, SUM(NOMINAL) AS TOTALREIMBURSEMENT, COUNT(IDREIMBURSEMENT) AS TOTALDATAREIMBURSEMENT
								  FROM t_reimbursement
								  WHERE IDDRIVER = ".$idDriver." AND IDWITHDRAWALRECAP = 0 AND STATUS = 1
								  GROUP BY IDDRIVER) G ON A.IDDRIVER = G.IDDRIVER
						LEFT JOIN (SELECT HA.IDDRIVER, SUM(HA.NOMINALRESULT) AS TOTALREVIEWBONUSPUNISHMENT, COUNT(IDDRIVERREVIEWBONUS) AS TOTALREVIEWBONUSPERIOD
								  FROM t_driverreviewbonus HA
								  LEFT JOIN t_driverreviewbonusperiod HB ON HA.IDDRIVERREVIEWBONUSPERIOD = HB.IDDRIVERREVIEWBONUSPERIOD
								  WHERE HA.IDDRIVER = ".$idDriver." AND HA.IDWITHDRAWALRECAP = 0 AND HB.PERIODDATEEND <= '".date('Y-m-d')."'
								  GROUP BY HA.IDDRIVER) H ON A.IDDRIVER = H.IDDRIVER
						LEFT JOIN (SELECT IDDRIVER, MAX(DATETIMEREQUEST) AS LASTWITHDRAWALDATE
								  FROM t_withdrawalrecap
								  WHERE IDDRIVER = ".$idDriver." AND STATUSWITHDRAWAL = 2
								  GROUP BY IDDRIVER) I ON A.IDDRIVER = I.IDDRIVER
						WHERE A.IDDRIVER = ".$idDriver."
						GROUP BY A.IDDRIVER
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row;
		return array(
			"TOTALSCHEDULE"						=>	0,
			"TOTALFEE"							=>	0,
			"TOTALADDITIONALCOST"				=>	0,
			"TOTALREIMBURSEMENT"				=>	0,
			"TOTALDATAREIMBURSEMENT"			=>	0,
			"TOTALREVIEWBONUSPUNISHMENT"		=>	0,
			"TOTALREVIEWBONUSPERIOD"			=>	0,
			"TOTALCOLLECTPAYMENT"				=>	0,
			"TOTALSCHEDULEWITHCOLLECTPAYMENT"	=>	0,
			"TOTALPREPAIDCAPITAL"				=>	0,
			"MAXDATEPREPAIDCAPITAL"				=>	"-",
			"TOTALLOAN"							=>	0,
			"TOTALLOANCAR"						=>	0,
			"TOTALLOANPERSONAL"					=>	0,
			"LASTWITHDRAWALDATE"				=>	"-"
		);
	}
	
	public function getDataListFee($idDriver){
		
		$baseQuery	=	"SELECT DATE_FORMAT(A.DATESCHEDULE, '%d %b %Y') AS SCHEDULEDATE, LEFT(B.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART,
							   C.SOURCENAME, B.INPUTTYPE, B.BOOKINGCODE, B.CUSTOMERNAME, A.RESERVATIONTITLE, A.JOBTITLE AS PRODUCTNAME,
							   A.FEENOMINAL AS NOMINAL, A.IDFEE
						FROM t_fee A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
						WHERE A.IDDRIVER = ".$idDriver." AND A.WITHDRAWSTATUS = 0 AND A.IDWITHDRAWALRECAP = 0
						ORDER BY A.DATESCHEDULE DESC";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataListAdditionalCost($idDriver){
		
		$baseQuery	=	"SELECT DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUTSTR, D.SOURCENAME, C.INPUTTYPE, C.BOOKINGCODE, C.CUSTOMERNAME,
								C.RESERVATIONTITLE, B.PRODUCTNAME, E.ADDITIONALCOSTTYPE, A.DESCRIPTION, A.NOMINAL, A.IDRESERVATIONADDITIONALCOST
						FROM t_reservationadditionalcost A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						LEFT JOIN t_reservation C ON B.IDRESERVATION = C.IDRESERVATION
						LEFT JOIN m_source D ON C.IDSOURCE = D.IDSOURCE
						LEFT JOIN m_additionalcosttype E ON A.IDADDITIONALCOSTTYPE = E.IDADDITIONALCOSTTYPE
						WHERE A.IDDRIVER = ".$idDriver." AND A.IDWITHDRAWALRECAP = 0 AND A.STATUSAPPROVAL = 1
						ORDER BY A.DATETIMEINPUT DESC";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataListReimbursement($idDriver){
		$baseQuery	=	"SELECT DATE_FORMAT(A.RECEIPTDATE, '%d %b %Y') AS DATERECEIPT, IFNULL(B.PARTNERTYPE, 'Other') AS REQUESTBYTYPE,
								A.DESCRIPTION, A.NOTES, A.NOMINAL, A.IDREIMBURSEMENT
						 FROM t_reimbursement A
						 LEFT JOIN m_partnertype B ON A.REQUESTBY = B.IDPARTNERTYPE
						 WHERE IDWITHDRAWALRECAP = 0 AND STATUS = 1 AND IDDRIVER = ".$idDriver."
						 ORDER BY A.RECEIPTDATE ASC";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;		
	}
	
	public function getDataListReviewBonusPunishment($idDriver){
		$baseQuery	=	"SELECT DATE_FORMAT(CONCAT(B.PERIODMONTHYEAR, '-01'), '%M %Y') AS PERIODNAME, DATE_FORMAT(B.PERIODDATESTART, '%d %b %Y') AS PERIODDATESTART,
								DATE_FORMAT(B.PERIODDATEEND, '%d %b %Y') AS PERIODDATEEND, B.TOTALTARGET, A.TOTALREVIEWPOINT, B.BONUSRATE, A.NOMINALBONUS, A.NOMINALPUNISHMENT,
								A.NOMINALRESULT, A.IDDRIVERREVIEWBONUS
						FROM t_driverreviewbonus A
						LEFT JOIN t_driverreviewbonusperiod B ON A.IDDRIVERREVIEWBONUSPERIOD = B.IDDRIVERREVIEWBONUSPERIOD
						WHERE A.IDWITHDRAWALRECAP = 0 AND B.PERIODDATEEND <= '".date('Y-m-d')."' AND A.IDDRIVER = ".$idDriver."
						ORDER BY B.PERIODMONTHYEAR";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;		
	}
	
	public function getDataListCollectPayment($idDriver){
		
		$baseQuery	=	"SELECT DATE_FORMAT(A.DATECOLLECT, '%d %b %Y') AS DATECOLLECT, B.INPUTTYPE, C.SOURCENAME, B.BOOKINGCODE, B.CUSTOMERNAME,
								B.RESERVATIONTITLE, B.REMARK, D.DESCRIPTION, D.AMOUNTCURRENCY, D.AMOUNT, D.AMOUNTIDR, A.IDCOLLECTPAYMENT
						FROM t_collectpayment A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
						LEFT JOIN t_reservationpayment D ON A.IDRESERVATIONPAYMENT = D.IDRESERVATIONPAYMENT
						WHERE A.IDPARTNERTYPE = 2 AND A.IDDRIVER = ".$idDriver." AND A.STATUSSETTLEMENTREQUEST != 2
						ORDER BY DATECOLLECT DESC"; 
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataLoanPrepaidCapitalHistory($idDriver, $typeLoanCapital){
		
		$baseQuery	=	"SELECT DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT, B.LOANTYPE, A.DESCRIPTION,
								A.TYPE, A.AMOUNT, 0 AS SALDO, A.IDLOANDRIVERRECORD, A.IDWITHDRAWALRECAP, A.IDLOANTYPE
						 FROM t_loandriverrecord A
						 LEFT JOIN m_loantype B ON A.IDLOANTYPE = B.IDLOANTYPE
						 WHERE A.IDDRIVER = ".$idDriver." AND B.STATUSLOANCAPITAL = ".$typeLoanCapital."
						 ORDER BY A.DATETIMEINPUT DESC"; 
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataWithdrawalRequest($startDate, $endDate, $idDriver, $statusWithdrawal, $viewRequestOnly){
		
		$con_idDriver	=	$con_date	=	$con_statusWithdrawal	=	$con_statusRequest	=	"1=1";
		
		if($viewRequestOnly == false || !isset($viewRequestOnly) || $viewRequestOnly == ""){
			$con_idDriver			=	!isset($idDriver) || $idDriver == "" ? "A.IDDRIVER != 0" : "A.IDDRIVER = ".$idDriver;
			$con_date				=	"DATE(A.DATETIMEREQUEST) BETWEEN '".$startDate."' AND '".$endDate."'";
			$con_statusWithdrawal	=	!isset($statusWithdrawal) || $statusWithdrawal == "" ? "1=1" : "A.STATUSWITHDRAWAL = ".$statusWithdrawal;
		} else {
			$con_idDriver			=	"A.IDDRIVER != 0";
			$con_statusRequest		=	"A.STATUSWITHDRAWAL = 0";
		}

		$baseQuery	=	"SELECT A.IDWITHDRAWALRECAP, CONCAT('[', C.DRIVERTYPE, ' Driver] ', B.NAME) AS DRIVERNAME, DATE_FORMAT(A.DATETIMEREQUEST, '%d %b %Y %H:%i') AS DATETIMEREQUEST,
								DATE_FORMAT(A.DATELASTPERIOD, '%d %b %Y') AS DATELASTPERIOD, A.MESSAGE, CONCAT('".URL_BANK_LOGO."', D.BANKLOGO) AS BANKLOGO, D.BANKNAME, A.ACCOUNTNUMBER,
								A.ACCOUNTHOLDERNAME, A.TOTALFEE, A.TOTALADDITIONALCOST, A.TOTALADDITIONALINCOME, A.TOTALREIMBURSEMENT, A.TOTALREVIEWBONUSPUNISHMENT, A.TOTALCOLLECTPAYMENT,
								A.TOTALPREPAIDCAPITAL, A.TOTALLOANCARINSTALLMENT, A.TOTALLOANPERSONALINSTALLMENT, A.TOTALCHARITY, A.TOTALWITHDRAWAL, A.STATUSWITHDRAWAL
						 FROM t_withdrawalrecap A
						 LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
						 LEFT JOIN m_drivertype C ON B.IDDRIVERTYPE = C.IDDRIVERTYPE
						 LEFT JOIN m_bank D ON A.IDBANK = D.IDBANK
						 WHERE ".$con_idDriver." AND ".$con_date." AND ".$con_statusWithdrawal." AND ".$con_statusRequest."
						 ORDER BY A.DATETIMEREQUEST DESC";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDetailWithdrawalRequest($idWithdrawalRecap){
		
		$baseQuery	=	"SELECT CONCAT('[', C.DRIVERTYPE, ' Driver] ', B.NAME) AS DRIVERNAME, DATE_FORMAT(A.DATETIMEREQUEST, '%d %b %Y %H:%i') AS DATETIMEREQUEST,
								DATE_FORMAT(A.DATELASTPERIOD, '%d %b %Y') AS DATELASTPERIOD, A.MESSAGE, CONCAT('".URL_BANK_LOGO."', D.BANKLOGO) AS BANKLOGO, D.BANKNAME,
								A.ACCOUNTNUMBER, A.ACCOUNTHOLDERNAME, A.TOTALFEE, A.TOTALADDITIONALCOST, A.TOTALADDITIONALINCOME, A.TOTALREIMBURSEMENT, A.TOTALREVIEWBONUSPUNISHMENT,
								A.TOTALCOLLECTPAYMENT, A.TOTALPREPAIDCAPITAL, A.TOTALLOANCARINSTALLMENT, A.TOTALLOANPERSONALINSTALLMENT, A.TOTALCHARITY, A.TOTALWITHDRAWAL,
								IF(A.DATETIMEAPPROVAL = '0000-00-00 00:00:00', '-', DATE_FORMAT(A.DATETIMEAPPROVAL, '%d %b %Y %H:%i')) AS DATETIMEAPPROVAL,
								IF(A.USERAPPROVAL IS NULL OR A.USERAPPROVAL = '', '-', A.USERAPPROVAL) AS USERAPPROVAL, A.STATUSWITHDRAWAL, A.IDDRIVER, A.IDBANK,
								B.EMAIL AS DRIVEREMAIL, IF(E.RECEIPTFILE IS NOT NULL AND E.RECEIPTFILE != '', CONCAT('".URL_HTML_TRANSFER_RECEIPT."', E.RECEIPTFILE), '') AS RECEIPTFILE
						 FROM t_withdrawalrecap A
						 LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
						 LEFT JOIN m_drivertype C ON B.IDDRIVERTYPE = C.IDDRIVERTYPE
						 LEFT JOIN m_bank D ON A.IDBANK = D.IDBANK
						 LEFT JOIN t_transferlist E ON A.IDWITHDRAWALRECAP = E.IDWITHDRAWAL
						 WHERE A.IDWITHDRAWALRECAP = ".$idWithdrawalRecap."
						 LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	public function getListDetailWithdrawal($idWithdrawalRecap){
		
		$baseQuery	=	"SELECT DATESTR, TYPESTR, BOOKINGCODE, DESCRIPTION, NOMINAL FROM (
							SELECT 1 AS TYPE, 'Fee' AS TYPESTR, AA.DATESCHEDULE AS DATEDB, DATE_FORMAT(AA.DATESCHEDULE, '%d %b %Y') AS DATESTR,
								AB.BOOKINGCODE, AA.JOBTITLE AS DESCRIPTION, AA.FEENOMINAL AS NOMINAL
							FROM t_fee AA
							LEFT JOIN t_reservation AB ON AA.IDRESERVATION = AB.IDRESERVATION
							WHERE AA.IDWITHDRAWALRECAP = ".$idWithdrawalRecap."
							UNION ALL
							SELECT 2 AS TYPE, 'Additional Cost' AS TYPESTR, DATE(BB.DATETIMEINPUT) AS DATEDB, DATE_FORMAT(BA.DATETIMEINPUT, '%d %b %Y') AS DATESTR,
								BC.BOOKINGCODE, IFNULL(BA.DESCRIPTION, '-') AS DESCRIPTION, BA.NOMINAL
							FROM t_reservationadditionalcost BA
							LEFT JOIN t_reservationdetails BB ON BA.IDRESERVATIONDETAILS = BB.IDRESERVATIONDETAILS
							LEFT JOIN t_reservation BC ON BB.IDRESERVATION = BC.IDRESERVATION
							WHERE BA.IDWITHDRAWALRECAP = ".$idWithdrawalRecap."
							UNION ALL
							SELECT 3 AS TYPE, 'Collect Payment' AS TYPESTR, DATE(CA.DATECOLLECT) AS DATEDB, DATE_FORMAT(CA.DATECOLLECT, '%d %b %Y') AS DATESTR,
								CC.BOOKINGCODE, IFNULL(CB.DESCRIPTION, '-') AS DESCRIPTION, CB.AMOUNTIDR * -1 AS NOMINAL
							FROM t_collectpayment CA
							LEFT JOIN t_reservationpayment CB ON CA.IDRESERVATIONPAYMENT = CB.IDRESERVATIONPAYMENT
							LEFT JOIN t_reservation CC ON CB.IDRESERVATION = CC.IDRESERVATION
							WHERE CA.IDWITHDRAWALRECAP = ".$idWithdrawalRecap."
							UNION ALL
							SELECT 4 AS TYPE, 'Prepaid Capital' AS TYPESTR, DATE(DATETIMEREQUEST) AS DATEDB, DATE_FORMAT(DATETIMEREQUEST, '%d %b %Y') AS DATESTR,
								'' AS BOOKINGCODE, 'Prepaid capital installment' AS DESCRIPTION, TOTALPREPAIDCAPITAL * -1 AS NOMINAL
							FROM t_withdrawalrecap
							WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap." AND TOTALPREPAIDCAPITAL > 0
							UNION ALL
							SELECT 5 AS TYPE, 'Loan Installment' AS TYPESTR, DATE(DATETIMEREQUEST) AS DATEDB, DATE_FORMAT(DATETIMEREQUEST, '%d %b %Y') AS DATESTR,
								'' AS BOOKINGCODE, 'Car loan installment' AS DESCRIPTION, TOTALLOANCARINSTALLMENT * -1 AS NOMINAL
							FROM t_withdrawalrecap
							WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap." AND TOTALLOANCARINSTALLMENT > 0
							UNION ALL
							SELECT 6 AS TYPE, 'Loan Installment' AS TYPESTR, DATE(DATETIMEREQUEST) AS DATEDB, DATE_FORMAT(DATETIMEREQUEST, '%d %b %Y') AS DATESTR,
								'' AS BOOKINGCODE, 'Personal loan installment' AS DESCRIPTION, TOTALLOANPERSONALINSTALLMENT * -1 AS NOMINAL
							FROM t_withdrawalrecap
							WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap." AND TOTALLOANPERSONALINSTALLMENT > 0
							UNION ALL
							SELECT 7 AS TYPE, 'Reimbursement' AS TYPESTR, DATE(RECEIPTDATE) AS DATEDB, DATE_FORMAT(RECEIPTDATE, '%d %b %Y') AS DATESTR,
								'' AS BOOKINGCODE, DESCRIPTION, NOMINAL
							FROM t_reimbursement
							WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap."
							UNION ALL
							SELECT 8 AS TYPE, 'Review Bonus Punishment' AS TYPESTR, DATE(HB.PERIODDATEEND) AS DATEDB, DATE_FORMAT(HB.PERIODDATEEND, '%d %b %Y') AS DATESTR,
								'' AS BOOKINGCODE, CONCAT('Review Bonus/Punishment - ', DATE_FORMAT(CONCAT(HB.PERIODMONTHYEAR, '-01'), '%M %Y')) AS DESCRIPTION, HA.NOMINALRESULT AS NOMINAL
							FROM t_driverreviewbonus HA
							LEFT JOIN t_driverreviewbonusperiod HB ON HA.IDDRIVERREVIEWBONUSPERIOD = HB.IDDRIVERREVIEWBONUSPERIOD
							WHERE HA.IDWITHDRAWALRECAP = ".$idWithdrawalRecap."
							UNION ALL
							SELECT 9 AS TYPE, 'Charity Program' AS TYPESTR, DATE(DATETIMEREQUEST) AS DATEDB, DATE_FORMAT(DATETIMEREQUEST, '%d %b %Y') AS DATESTR,
								 '' AS BOOKINGCODE, 'Charity program' AS DESCRIPTION, TOTALCHARITY * -1 AS NOMINAL
							FROM t_withdrawalrecap
							WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap." AND TOTALCHARITY > 0
							UNION ALL
							SELECT 10 AS TYPE, 'Additional Income (SS)' AS TYPESTR, DATE(DATETIMEREQUEST) AS DATEDB, DATE_FORMAT(DATETIMEREQUEST, '%d %b %Y') AS DATESTR,
								 '' AS BOOKINGCODE, 'Additional Income (SS)' AS DESCRIPTION, TOTALADDITIONALINCOME * -1 AS NOMINAL
							FROM t_withdrawalrecap
							WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap." AND TOTALADDITIONALINCOME > 0
						  ) AS A
						  ORDER BY DATEDB, BOOKINGCODE, TYPE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataFeeWithdrawal($idWithdrawalRecap){
		
		$baseQuery	=	"SELECT IDFEE FROM t_fee
						 WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap;
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataAdditionalCostWithdrawal($idWithdrawalRecap){
		
		$baseQuery	=	"SELECT IDRESERVATIONADDITIONALCOST FROM t_reservationadditionalcost
						 WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap;
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataReimbursementWithdrawal($idWithdrawalRecap){
		
		$baseQuery	=	"SELECT IDREIMBURSEMENT FROM t_reimbursement
						 WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap;
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataReviewBonusPunishmentWithdrawal($idWithdrawalRecap){
		
		$baseQuery	=	"SELECT IDDRIVERREVIEWBONUS FROM t_driverreviewbonus
						 WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap;
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataCollectPaymentWithdrawal($idWithdrawalRecap){
		
		$baseQuery	=	"SELECT IDCOLLECTPAYMENT, IDRESERVATIONPAYMENT FROM t_collectpayment
						 WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap;
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataPrepaidCapitalWithdrawal($idWithdrawalRecap){
		
		$baseQuery	=	"SELECT IDLOANDRIVERRECORD, AMOUNT FROM t_loandriverrecord
						 WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap;
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getTotalWithdrawalRequest($idPartnerType = 2){
		
		$whereField	=	$idPartnerType == 1 ? "IDVENDOR" : "IDDRIVER";
		$query		=	$this->db->query("SELECT COUNT(IDWITHDRAWALRECAP) AS TOTALWITHDRAWALREQUEST
										  FROM t_withdrawalrecap
										  WHERE STATUSWITHDRAWAL = 0 AND ".$whereField." != 0
										  LIMIT 1"
						);
		$row		=	$query->row_array();

		if(isset($row)){
			return $row['TOTALWITHDRAWALREQUEST'];
		}
		
		return 0;
		
	}
	
}