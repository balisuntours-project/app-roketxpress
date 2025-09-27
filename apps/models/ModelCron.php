<?php
class ModelCron extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function getDataDayOffRequestYesterday($dateYesterday){
		$query	=	$this->db->query(
						"SELECT IDDAYOFFREQUEST FROM t_dayoffrequest
						WHERE STATUS = 0 AND DATEDAYOFF = '".$dateYesterday."'"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return false;
	}
	
	public function getDetailDayOffRequest($idDayoffRequest){
		$baseQuery	=	sprintf(
							"SELECT IDDRIVER, IDCARVENDOR, DATEDAYOFF, REASON, DATETIMEINPUT
							FROM t_dayoffrequest
							WHERE IDDAYOFFREQUEST = '".$idDayoffRequest."'
							LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;
	}
	
	public function getDataAutoConfirmDriverLoanTransaction($maxDateTime){
		$query	=	$this->db->query(
						"SELECT IDLOANDRIVERREQUEST FROM t_loandriverrequest
						WHERE STATUS = 1 AND DATETIMEUPDATE <= '".$maxDateTime."'"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return false;
	}
	
	public function getDetailLoanRequest($idLoanDriverRequest){
		$baseQuery	=	sprintf(
							"SELECT A.IDDRIVER, A.IDLOANTYPE, A.AMOUNT, A.USERUPDATE, B.STATUSLOANCAPITAL, B.LOANTYPE, A.NOTES, C.ACCOUNTNUMBER, C.ACCOUNTHOLDERNAME, D.BANKNAME,
									E.LOANNOMINALPRINCIPAL, E.LOANNOMINALINTEREST, E.LOANNOMINALTOTAL, E.LOANDURATIONMONTH, E.LOANINTERESTPERANNUM, E.LOANINSTALLMENTPERMONTH,
									A.DATETIMEINPUT
							FROM t_loandriverrequest A
							LEFT JOIN m_loantype B ON A.IDLOANTYPE = B.IDLOANTYPE
							LEFT JOIN t_bankaccountpartner C ON A.IDBANKACCOUNTPARTNER = C.IDBANKACCOUNTPARTNER
							LEFT JOIN m_bank D ON C.IDBANK = D.IDBANK
							LEFT JOIN t_loandriverrecap E ON A.IDLOANDRIVERREQUEST = E.IDLOANDRIVERREQUEST
							WHERE A.IDLOANDRIVERREQUEST = '".$idLoanDriverRequest."'
							LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;
	}
	
	public function isLoanRecordExist($idLoanDriverRequest){
		$baseQuery	=	sprintf(
							"SELECT IDLOANDRIVERRECORD FROM t_loandriverrecord
							WHERE IDLOANDRIVERREQUEST = '".$idLoanDriverRequest."' AND TYPE = 'D'
							LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return true;
	}
	
	public function getDetailTransferList($accountNumber, $nominalTransfer, $partnerCode, $remarkTransfer){
		$baseQuery	=	sprintf(
							"SELECT IDTRANSFERLIST, IDPARTNERTYPE, IDPARTNER, IDLOANDRIVERREQUEST, IDWITHDRAWAL
							FROM t_transferlist
							WHERE ACCOUNTNUMBER = '".$accountNumber."' AND AMOUNT = ".$nominalTransfer." AND
								  PARTNERCODE = '".$partnerCode."' AND REMARK = '".$remarkTransfer."' AND STATUS = 1
							LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;
	}
	
	public function getDetailWithdrawalRequest($idWithdrawalRecap){
		$baseQuery	=	"SELECT CONCAT('[', C.DRIVERTYPE, ' Driver] ', B.NAME) AS DRIVERNAME, DATE_FORMAT(A.DATETIMEREQUEST, '%d %b %Y %H:%i') AS DATETIMEREQUEST,
								DATE_FORMAT(A.DATETIMEREQUEST, '%Y-%m') AS WITHDRAWMONTHYEAR, DATE_FORMAT(A.DATETIMEREQUEST, '%Y-%m-%d') AS DATETIMEREQUESTDB,
								A.MESSAGE, CONCAT('".URL_BANK_LOGO."', D.BANKLOGO) AS BANKLOGO, D.BANKNAME, A.ACCOUNTNUMBER, A.ACCOUNTHOLDERNAME, A.TOTALFEE,
								A.TOTALADDITIONALCOST, A.TOTALADDITIONALINCOME, A.TOTALCOLLECTPAYMENT, A.TOTALPREPAIDCAPITAL, A.TOTALLOANCARINSTALLMENT, 
								A.TOTALLOANPERSONALINSTALLMENT, A.TOTALCHARITY, A.TOTALWITHDRAWAL, A.STATUSWITHDRAWAL, A.IDDRIVER, A.IDVENDOR,
								IF(A.DATETIMEAPPROVAL = '0000-00-00 00:00:00', '-', DATE_FORMAT(A.DATETIMEAPPROVAL, '%d %b %Y %H:%i')) AS DATETIMEAPPROVAL,
								IF(A.USERAPPROVAL IS NULL OR A.USERAPPROVAL = '', '-', A.USERAPPROVAL) AS USERAPPROVAL, A.IDBANK, B.EMAIL AS DRIVEREMAIL
						 FROM t_withdrawalrecap A
						 LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
						 LEFT JOIN m_drivertype C ON B.IDDRIVERTYPE = C.IDDRIVERTYPE
						 LEFT JOIN m_bank D ON A.IDBANK = D.IDBANK
						 WHERE A.IDWITHDRAWALRECAP = ".$idWithdrawalRecap."
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
	
	public function getDataDriverFreelance(){
		$baseQuery	=	sprintf(
							"SELECT IDDRIVER FROM m_driver
							WHERE PARTNERSHIPTYPE = 2"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}
		
	public function getDataCorrection($limit){
		$baseQuery	=	"SELECT IDINCOMECORRECTION, HTMLFILENAME, EXCHANGECURRENCY FROM a_incomecorrection
						WHERE STATUS = 0
						LIMIT 0,".$limit;
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;
	}
	
	public function getDataCronMailReview($limit){
		$baseQuery	=	"SELECT A.IDRESERVATIONMAILRATING, A.IDUNIQUE, B.IDSOURCE, B.CUSTOMERNAME, C.SOURCENAME, B.BOOKINGCODE, A.URLREVIEW, B.CUSTOMEREMAIL
						FROM t_reservationmailreview A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
						WHERE A.STATUSSEND = 0 AND A.DATETIMESCHEDULE <= NOW()
						LIMIT 0,".$limit;
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;
	}
	
	//OK
	public function getDataReservationReconfirmation($dateDaysToCome, $dateYesterday){
		$baseQuery	=	"SELECT A.IDRESERVATION, IFNULL(A.IDCONTACT, 0) AS IDCONTACT, C.PHONENUMBER, A.CUSTOMERCONTACT, A.CUSTOMEREMAIL, A.RESERVATIONDATESTART, A.RESERVATIONTIMESTART, A.ADDITIONALINFOLIST
						FROM t_reservation A
						LEFT JOIN t_reservationreconfirmation B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN ".APP_WHATSAPP_DATABASE_NAME.".t_contact C ON A.IDCONTACT = C.IDCONTACT
						WHERE DATE(A.RESERVATIONDATESTART) > '".$dateYesterday."' AND B.IDRESERVATION IS NULL AND A.STATUS != -1 AND A.STATUS < 3 AND 
							  CONCAT(A.RESERVATIONDATESTART, ' ', A.RESERVATIONTIMESTART) <= '".$dateDaysToCome." ".date('H:i:s')."' AND ((A.CUSTOMEREMAIL != '' AND
							  A.CUSTOMEREMAIL IS NOT NULL) OR (C.IDCONTACT IS NOT NULL AND C.IDCONTACT > 0))
						ORDER BY A.RESERVATIONDATESTART DESC, A.RESERVATIONTIMESTART
						LIMIT 20";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;
	}
	
	public function getDataMailReconfirmation($numberLimit, $idReservationReconfirmation = false){
		$condition	=	$idReservationReconfirmation ?
						"A.IDRESERVATIONRECONFIRMATION = ".$idReservationReconfirmation :
						"A.DATETIMESCHEDULE != '0000-00-00 00:00:00' AND A.SENDINGMETHOD = 1 AND A.DATETIMESCHEDULE <= '".date('Y-m-d H:i:s')."'";
		$baseQuery	=	"SELECT A.IDRESERVATIONRECONFIRMATION, B.BOOKINGCODE, B.RESERVATIONTITLE, B.CUSTOMERNAME, A.CONTACT AS EMAILADDRESS, B.STATUS
						FROM t_reservationreconfirmation A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						WHERE ".$condition." AND A.STATUS = 0 AND A.PLATFORM = 'EMAIL'
						ORDER BY A.DATETIMESCHEDULE
						LIMIT ".$numberLimit;
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;
	}
	
	public function getDetailMailReconfirmation($idReservationReconfirmation){
		$baseQuery	=	"SELECT B.BOOKINGCODE, B.CUSTOMERNAME, B.NUMBEROFADULT, B.NUMBEROFCHILD, B.NUMBEROFINFANT, B.RESERVATIONTITLE,
								DATE_FORMAT(B.RESERVATIONDATESTART, '%a, %d %b %Y') AS RESERVATIONDATESTART, DATE_FORMAT(B.RESERVATIONDATEEND, '%a, %d %b %Y') AS RESERVATIONDATEEND,
								B.RESERVATIONTIMESTART, B.PICKUPLOCATION, B.HOTELNAME, A.ADDITIONALINFOLIST, B.IDAREA
						FROM t_reservationreconfirmation A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
						WHERE A.IDRESERVATIONRECONFIRMATION = ".$idReservationReconfirmation."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;
	}
	
	public function isReconfirmExist($bookingCode){
		$baseQuery	=	"SELECT A.IDRESERVATIONRECONFIRMATION, A.STATUS, B.STATUS AS STATUSRESERVATION, A.MAILTHREADARRNAME, A.MAILTHREADFILE, B.CUSTOMERNAME
						FROM t_reservationreconfirmation A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						WHERE A.PLATFORM = 'EMAIL' AND B.BOOKINGCODE = '".$bookingCode."'
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;
	}

	public function getLastReviewPeriod(){
		$baseQuery	=	"SELECT IDDRIVERREVIEWBONUSPERIOD, PERIODMONTHYEAR, PERIODDATEEND
						FROM t_driverreviewbonusperiod
						ORDER BY PERIODDATEEND DESC
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;
	}
	
	public function isReviewPeriodExist($monthYear){
		$baseQuery	=	"SELECT IDDRIVERREVIEWBONUSPERIOD FROM t_driverreviewbonusperiod
						WHERE PERIODMONTHYEAR = '".$monthYear."'
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return true;
	}
	
	public function getDataReservationNoContact($idReservation){
		$con_idReservation	=	$idReservation == false ? "1=1" : "IDRESERVATION = ".$idReservation;
		$baseQuery			=	"SELECT IDRESERVATION, CUSTOMERNAME, CUSTOMERCONTACT, CUSTOMEREMAIL
								FROM t_reservation
								WHERE ".$con_idReservation." AND IDCONTACT = 0
								ORDER BY IDRESERVATION
								LIMIT 100";
		$query				=	$this->db->query($baseQuery);
		$result				=	$query->result();
		
		if($result) return $result;
		return false;
	}
	
	public function isNumberExist($phoneNumberBase, $idCountry){
		$baseQuery	=	"SELECT IDCONTACT, NAMEFULL FROM ".APP_WHATSAPP_DATABASE_NAME.".t_contact
						WHERE PHONENUMBERBASE = '".$phoneNumberBase."' AND IDCOUNTRY = ".$idCountry."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if($row) return $row;
		return false;
	}
	
	public function getDataAdditionalIncomePerIdDriver(){
		$dateTime		=	new DateTime();
		$dateTime->modify('-3 months');
		$monthYearStart	=	$dateTime->format('Y-m');
		$baseQuery		=	"SELECT IDDRIVER, LEFT (INCOMEDATE, 7) AS YEARMONTH, GROUP_CONCAT(IDADDITIONALINCOME) AS STRARRIDADDITIONALINCOME,
									COUNT(IDADDITIONALINCOME) AS NUMBEROFADDITIONALINCOME, SUM(INCOMENOMINAL) AS TOTALINCOMENOMINAL,
									IFNULL(MAX(INCOMEDATE), '0000-00-00') AS MAXINCOMEDATE
							 FROM t_additionalincome
							 WHERE INCOMEDATE >= '".$monthYearStart."-01' AND LEFT(INCOMEDATE, 7) != '".date('Y-m')."' AND APPROVALSTATUS = 1
							 GROUP BY IDDRIVER, LEFT (INCOMEDATE, 7)";
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getDataActiveDriverAdditionalIncome(){
		$baseQuery	=	"SELECT IDDRIVER FROM m_driver
						 WHERE STATUS = 1 AND PARTNERSHIPTYPE != 2
						 ORDER BY IDDRIVER";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function isAdditionalIncomeRecapExist($period, $idDriverAdditionalIncome){
		$baseQuery	=	"SELECT IDADDITIONALINCOMERECAP FROM t_additionalincomerecap
						 WHERE IDDRIVER = ".$idDriverAdditionalIncome." AND PERIOD = '".$period."'
						 LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return true;
		return false;
	}
	
	public function getIdContactReservation($idReservation){
		$baseQuery	=	sprintf(
							"SELECT IDCONTACT FROM t_reservation
							WHERE IDRESERVATION = '".$idReservation."'
							LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return 0;
		return $row['IDCONTACT'];
	}
	
	public function getArrIdReservationType($idContact){
		$baseQuery	=	sprintf(
							"SELECT CONCAT('[', GROUP_CONCAT(DISTINCT(IDRESERVATIONTYPE)), ']') AS ARRIDRESERVATIONTYPE
							FROM t_reservation
							WHERE IDCONTACT = '".$idContact."'
							GROUP BY IDCONTACT
							LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return '';
		return $row['ARRIDRESERVATIONTYPE'];
	}
	
	public function getDataEBookingEarnCoin($dateTimeMinus12Hours, $dateTimeMinus18Hours){
		$baseQuery	=	sprintf(
							"SELECT A.IDRESERVATION, A.BOOKINGCODE FROM t_reservation A
							LEFT JOIN t_ebookingcoin B ON A.IDRESERVATION = B.IDRESERVATION
							WHERE CONCAT(A.RESERVATIONDATESTART, ' ', A.RESERVATIONTIMESTART) BETWEEN '".$dateTimeMinus18Hours."' AND '".$dateTimeMinus12Hours."' AND
								  A.IDSOURCE = 4 AND (A.IDAREA = -1 OR A.STATUSDRIVER = 0) AND A.STATUS > 0 AND B.IDRESERVATION IS NULL
							ORDER BY A.RESERVATIONTIMEEND ASC"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getDataExecuteEBookingCoinEarned(){
		$baseQuery	=	sprintf(
							"SELECT A.IDEBOOKINGCOIN, B.BOOKINGCODE FROM t_ebookingcoin A
							LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
							WHERE A.STATUS IN (0,-1)
							ORDER BY A.DATETIMEINSERT ASC"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}

	public function isDriverHandleOwnCarByBookingCode($bookingCode){
		$baseQuery	=	sprintf(
							"SELECT D.IDCARVENDOR FROM t_reservation A
							LEFT JOIN t_reservationdetails B ON A.IDRESERVATION = B.IDRESERVATION AND B.IDDRIVERTYPE != 0
							LEFT JOIN t_scheduledriver C ON B.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
							LEFT JOIN t_carvendor D ON C.IDDRIVER = D.IDDRIVER
							WHERE A.BOOKINGCODE = '".$bookingCode."'
							LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return is_null($row['IDCARVENDOR']) || $row['IDCARVENDOR'] == 0 ? false : true;
		return false;
	}
}