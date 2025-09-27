<?php
class ModelRecapPerVendor extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataAllVendorReport($page, $dataPerPage= 25, $idVendorType, $idVendor){
		$ci					=&	get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_idVendorType	=	!isset($idVendorType) || $idVendorType == "" ? "1=1" : "A.IDVENDORTYPE = ".$idVendorType;
		$con_idVendor		=	!isset($idVendor) || $idVendor == "" ? "1=1" : "A.IDVENDOR = ".$idVendor;
		$baseQuery			=	"SELECT B.VENDORTYPE, A.NAME AS VENDORNAME, COUNT(C.IDFEE) AS TOTALSCHEDULE,
										SUM(C.FEENOMINAL) AS TOTALFEE, IFNULL(D.TOTALCOLLECTPAYMENT, 0) AS TOTALCOLLECTPAYMENT, A.IDVENDOR
								FROM m_vendor A
								LEFT JOIN m_vendortype B ON A.IDVENDORTYPE = B.IDVENDORTYPE
								LEFT JOIN t_fee C ON A.IDVENDOR = C.IDVENDOR AND C.WITHDRAWSTATUS = 0 AND C.IDWITHDRAWALRECAP = 0
								LEFT JOIN (SELECT DA.IDVENDOR, SUM(DB.AMOUNTIDR) AS TOTALCOLLECTPAYMENT
										  FROM t_collectpayment DA
										  LEFT JOIN t_reservationpayment DB ON DA.IDRESERVATIONPAYMENT = DB.IDRESERVATIONPAYMENT
										  WHERE DA.IDVENDOR != 0 AND DA.STATUSSETTLEMENTREQUEST != 2
										  GROUP BY DA.IDVENDOR
										  ) AS D ON A.IDVENDOR = D.IDVENDOR
								WHERE ".$con_idVendorType." AND ".$con_idVendor." AND A.STATUS = 1 AND A.NEWFINANCESCHEME = 1
								GROUP BY A.IDVENDOR
								ORDER BY B.VENDORTYPE, A.NAME";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDVENDOR", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}

	public function getDataRecapPerVendor($page, $dataPerPage= 25, $idVendorType, $idVendor, $startDate, $endDate){
		$ci					=&	get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_idVendorType	=	!isset($idVendorType) || $idVendorType == "" ? "1=1" : "A.IDVENDORTYPE = ".$idVendorType;
		$con_idVendor		=	!isset($idVendor) || $idVendor == "" ? "1=1" : "A.IDVENDOR = ".$idVendor;
		$baseQuery			=	"SELECT B.VENDORTYPE, A.NAME AS VENDORNAME, A.IDVENDOR,
										IFNULL(IF(A.IDVENDORTYPE = 1, C.TOTALSCHEDULE, D.TOTALSCHEDULE), 0) AS TOTALSCHEDULE,
										IFNULL(IF(A.IDVENDORTYPE = 1, C.TOTALFEE, D.TOTALFEE), 0) AS TOTALFEE,
										'' AS URLEXCELDETAILFEE
								FROM m_vendor A
								LEFT JOIN m_vendortype B ON A.IDVENDORTYPE = B.IDVENDORTYPE
								LEFT JOIN (
											SELECT CA.IDVENDOR, COUNT(CC.IDRESERVATIONDETAILS) AS TOTALSCHEDULE, SUM(CC.NOMINAL) AS TOTALFEE
											FROM t_carvendor CA
											LEFT JOIN t_schedulecar CB ON CA.IDCARVENDOR = CB.IDCARVENDOR
											LEFT JOIN t_reservationdetails CC ON CB.IDRESERVATIONDETAILS = CC.IDRESERVATIONDETAILS
											WHERE CC.SCHEDULEDATE BETWEEN '".$startDate."' AND '".$endDate."' AND CA.STATUS = 1
											GROUP BY CA.IDVENDOR
										) AS C ON A.IDVENDOR = C.IDVENDOR
								LEFT JOIN (
											SELECT IDVENDOR, COUNT(IDRESERVATIONDETAILS) AS TOTALSCHEDULE, SUM(NOMINAL) AS TOTALFEE
											FROM t_reservationdetails
											WHERE SCHEDULEDATE BETWEEN '".$startDate."' AND '".$endDate."' AND STATUS = 1 AND
												  IDVENDOR != 0 AND IDDRIVERTYPE = 0 AND IDCARTYPE = 0
											GROUP BY IDVENDOR
										) AS D ON A.IDVENDOR = D.IDVENDOR
								WHERE ".$con_idVendorType." AND ".$con_idVendor." AND A.STATUS = 1
								GROUP BY A.IDVENDOR
								ORDER BY B.VENDORTYPE, A.NAME";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDVENDOR", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}
	
	public function getDetailVendor($idVendor){
		$query		=	$this->db->query(
							"SELECT B.VENDORTYPE, '' AS INITIALNAME, A.NAME, A.ADDRESS, A.PHONE, A.EMAIL, A.FINANCESCHEMETYPE, A.AUTOREDUCECOLLECTPAYMENT
							FROM m_vendor A
							LEFT JOIN m_vendortype B ON A.IDVENDORTYPE = B.IDVENDORTYPE
							WHERE A.IDVENDOR = ".$idVendor."
							LIMIT 1"
						);
		$row		=	$query->row_array();

		if(isset($row)) return $row;
		return array(
			"VENDORTYPE"				=>	"-",
			"INITIALNAME"				=>	"-",
			"NAME"						=>	"-",
			"ADDRESS"					=>	"-",
			"PHONE"						=>	"-",
			"EMAIL"						=>	"-",
			"FINANCESCHEMETYPE"			=>	0,
			"AUTOREDUCECOLLECTPAYMENT"	=>	1
		);
	}

	public function getDataActiveBankAccountVendor($idVendor){
		$query	=	$this->db->query(
						"SELECT A.IDBANKACCOUNTPARTNER, A.ACCOUNTNUMBER, A.ACCOUNTHOLDERNAME, B.BANKNAME, CONCAT('".URL_BANK_LOGO."', B.BANKLOGO) AS BANKLOGO
						FROM t_bankaccountpartner A
						LEFT JOIN m_bank B ON A.IDBANK = B.IDBANK
						WHERE A.IDPARTNERTYPE = 1 AND A.IDPARTNER = ".$idVendor." AND A.STATUS = 1
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return array(
			"ACCOUNTNUMBER"		=>	"-",
			"ACCOUNTHOLDERNAME"	=>	"-",
			"BANKNAME"			=>	"-",
			"BANKLOGO"			=>	URL_BANK_LOGO."default.png"
		);
	}
	
	public function getDataBankAccountVendor($idVendor){
		$query	=	$this->db->query(
						"SELECT A.IDBANKACCOUNTPARTNER, A.ACCOUNTNUMBER, A.ACCOUNTHOLDERNAME, B.BANKNAME, CONCAT('".URL_BANK_LOGO."', B.BANKLOGO) AS BANKLOGO, A.STATUS
						FROM t_bankaccountpartner A
						LEFT JOIN m_bank B ON A.IDBANK = B.IDBANK
						WHERE A.IDPARTNERTYPE = 1 AND A.IDPARTNER = ".$idVendor."
						ORDER BY B.BANKNAME, A.ACCOUNTNUMBER"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return [];
	}
	
	public function isBankAccountExist($idBank, $accountNumber){
		$query	=	$this->db->query(
						"SELECT IDBANKACCOUNTPARTNER FROM t_bankaccountpartner
						WHERE IDBANK = ".$idBank." AND ACCOUNTNUMBER = '".$accountNumber."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return true;
		return false;
	}
	
	public function getDetailBankAccountVendor($idBankAccountVendor){
		$query	=	$this->db->query(
						"SELECT IDBANK, ACCOUNTNUMBER, ACCOUNTHOLDERNAME
						FROM t_bankaccountpartner
						WHERE IDBANKACCOUNTPARTNER = ".$idBankAccountVendor." AND STATUS = 1
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return array(
			"IDBANK"			=>	0,
			"ACCOUNTNUMBER"		=>	"-",
			"ACCOUNTHOLDERNAME"	=>	"-"
		);		
	}
	
	public function isFeeExistByIdReservationDetail($idVendor, $idReservationDetails){
		$query	=	$this->db->query(
						"SELECT IDFEE FROM t_fee
						WHERE IDVENDOR  = ".$idVendor." AND IDRESERVATIONDETAILS  = ".$idReservationDetails."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return true;
		return false;
	}
	
	public function getDetailReservationSchedule($idReservationDetails){
		$query	=	$this->db->query(
						"SELECT B.IDRESERVATION, B.SCHEDULEDATE, C.RESERVATIONTITLE, B.PRODUCTNAME, B.NOMINAL, B.NOTES
						FROM t_schedulevendor A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						LEFT JOIN t_reservation C ON B.IDRESERVATION = C.IDRESERVATION
						WHERE A.IDRESERVATIONDETAILS = ".$idReservationDetails."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return array(
			"IDRESERVATION"		=>	0,
			"SCHEDULEDATE"		=>	"0000-00-00",
			"RESERVATIONTITLE"	=>	"-",
			"PRODUCTNAME"		=>	"-",
			"NOMINAL"			=>	0,
			"NOTES"				=>	"-"
		);		
	}
	
	public function getDataRecapPerVendorDetail($idVendor){
		$baseQuery	=	"SELECT COUNT(C.IDFEE) AS TOTALSCHEDULE, IFNULL(SUM(G.FEESCHEDULE), 0) AS TOTALFEESCHEDULE, IFNULL(SUM(C.FEENOMINAL), 0) AS TOTALFEE,
								IFNULL(D.TOTALCOLLECTPAYMENT, 0) AS TOTALCOLLECTPAYMENT, IFNULL(D.TOTALSCHEDULEWITHCOLLECTPAYMENT, 0) AS TOTALSCHEDULEWITHCOLLECTPAYMENT,
								IFNULL(DATE_FORMAT(E.LASTWITHDRAWALDATE, '%d %b %Y'), '-') AS LASTWITHDRAWALDATE, F.DEPOSITBALANCE,
								IFNULL(DATE_FORMAT(F.LASTDEPOSITTRANSACTION, '%d %b %Y'), '-') AS LASTDEPOSITTRANSACTIONDATE
						FROM m_vendor A
						LEFT JOIN m_vendortype B ON A.IDVENDORTYPE = B.IDVENDORTYPE
						LEFT JOIN t_fee C ON A.IDVENDOR = C.IDVENDOR AND C.WITHDRAWSTATUS = 0 AND C.IDWITHDRAWALRECAP = 0
						LEFT JOIN (SELECT DA.IDVENDOR, SUM(DB.AMOUNTIDR) AS TOTALCOLLECTPAYMENT, COUNT(DA.IDCOLLECTPAYMENT) AS TOTALSCHEDULEWITHCOLLECTPAYMENT
								  FROM t_collectpayment DA
								  LEFT JOIN t_reservationpayment DB ON DA.IDRESERVATIONPAYMENT = DB.IDRESERVATIONPAYMENT
								  WHERE DA.IDVENDOR = ".$idVendor." AND DA.STATUSSETTLEMENTREQUEST != 2
								  GROUP BY DA.IDVENDOR
								  ) AS D ON A.IDVENDOR = D.IDVENDOR
						LEFT JOIN (SELECT IDVENDOR, MAX(DATETIMEREQUEST) AS LASTWITHDRAWALDATE
								  FROM t_withdrawalrecap
								  WHERE IDVENDOR = ".$idVendor." AND STATUSWITHDRAWAL = 2
								  GROUP BY IDVENDOR) E ON A.IDVENDOR = E.IDVENDOR
						LEFT JOIN (SELECT IDVENDOR, SUM(AMOUNT) AS DEPOSITBALANCE, MAX(DATETIMEINPUT) AS LASTDEPOSITTRANSACTION
								  FROM t_depositvendorrecord
								  WHERE IDVENDOR = ".$idVendor."
								  GROUP BY IDVENDOR) F ON A.IDVENDOR = F.IDVENDOR
						LEFT JOIN (SELECT IDVENDOR, SUM(NOMINAL) AS FEESCHEDULE
								  FROM t_reservationdetails
								  WHERE IDVENDOR = ".$idVendor."
								  GROUP BY IDVENDOR) G ON A.IDVENDOR = G.IDVENDOR
						WHERE A.IDVENDOR = ".$idVendor."
						GROUP BY A.IDVENDOR
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row;
		return array(
			"TOTALSCHEDULE"						=>	0,
			"TOTALFEE"							=>	0,
			"FEESCHEDULE"						=>	0,
			"TOTALCOLLECTPAYMENT"				=>	0,
			"TOTALSCHEDULEWITHCOLLECTPAYMENT"	=>	0,
			"LASTWITHDRAWALDATE"				=>	"-"
		);
	}
	
	public function getDataFeeCollectPaymentSchedule($idVendor){
		$baseQuery	=	"SELECT * FROM (
							SELECT 1 AS TYPE, IDRESERVATIONDETAILS AS IDDATA, TYPESTR, DATESTR, CONCAT('<b>', SOURCENAME, ' - ', BOOKINGCODE, '</b><br/>', CUSTOMERNAME, '<br/>', PRODUCTNAME) AS DESCRIPTION,
									NOMINAL, DATEORDER, BOOKINGCODE
							FROM (
								SELECT A.IDRESERVATIONDETAILS, IFNULL(E.IDFEE, 0) AS IDFEE, IFNULL(E.WITHDRAWSTATUS, 0) AS WITHDRAWSTATUS, 'Fee' AS TYPESTR,
										DATE_FORMAT(A.SCHEDULEDATE, '%d %b %Y') AS DATESTR, C.SOURCENAME, B.BOOKINGCODE, B.CUSTOMERNAME, A.PRODUCTNAME,
										A.NOMINAL, A.SCHEDULEDATE AS DATEORDER
								FROM t_reservationdetails A
								LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
								LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
								LEFT JOIN t_schedulevendor D ON A.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS
								LEFT JOIN t_fee E ON A.IDRESERVATIONDETAILS = E.IDRESERVATIONDETAILS
								WHERE A.IDVENDOR = ".$idVendor." AND A.SCHEDULEDATE <= '".date('Y-m-d')."' AND D.IDRESERVATIONDETAILS IS NOT NULL
							) AS A
							WHERE IDFEE = 0 OR WITHDRAWSTATUS = 0
							
							UNION ALL
							SELECT 3 AS TYPE, A.IDCOLLECTPAYMENT AS IDDATA, 'Collect Payment' AS TYPESTR, DATE_FORMAT(A.DATECOLLECT, '%d %b %Y') AS DATESTR,
									CONCAT('<b>', C.SOURCENAME, ' - ', B.BOOKINGCODE, '</b><br/>', B.CUSTOMERNAME, '<br/>', D.DESCRIPTION) AS DESCRIPTION,
									D.AMOUNTIDR * -1 AS NOMINAL, A.DATECOLLECT AS DATEORDER, B.BOOKINGCODE
							FROM t_collectpayment A
							LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
							LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
							LEFT JOIN t_reservationpayment D ON A.IDRESERVATIONPAYMENT = D.IDRESERVATIONPAYMENT
							WHERE A.IDVENDOR = ".$idVendor." AND A.DATECOLLECT <= '".date('Y-m-d')."' AND A.STATUSSETTLEMENTREQUEST = 0 AND A.IDWITHDRAWALRECAP = 0
						) AS A
						ORDER BY DATEORDER, BOOKINGCODE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return [];		
	}
	
	public function getDataListFee($idVendor){
		$baseQuery	=	"SELECT DATE_FORMAT(A.DATESCHEDULE, '%d %b %Y') AS SCHEDULEDATE, LEFT(B.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART,
							   C.SOURCENAME, B.INPUTTYPE, B.BOOKINGCODE, B.CUSTOMERNAME, A.RESERVATIONTITLE, A.JOBTITLE AS PRODUCTNAME,
							   A.FEENOMINAL AS NOMINAL
						FROM t_fee A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
						WHERE A.IDVENDOR = ".$idVendor." AND A.WITHDRAWSTATUS = 0 AND A.IDWITHDRAWALRECAP = 0
						ORDER BY A.DATESCHEDULE DESC";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getDetailReservationVendor($idVendor, $bookingCode, $customerName, $nominal){
		$totalBookingVendor	=	$this->getTotalBookingHandleByVendor($idVendor, $bookingCode);
		$conditionWhere		=	$totalBookingVendor > 0 ? "B.BOOKINGCODE = '".$bookingCode."'" : "B.CUSTOMERNAME = '".$customerName."'";
		$conditionNominal	=	$totalBookingVendor > 1 ? "A.NOMINAL = '".$nominal."'" : "1=1";
		$baseQuery			=	"SELECT A.IDRESERVATION, A.IDRESERVATIONDETAILS, B.BOOKINGCODE, B.CUSTOMERNAME, IFNULL(E.WITHDRAWSTATUS, 0) AS WITHDRAWSTATUS, A.SCHEDULEDATE,
										DATE_FORMAT(A.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATESTR, A.PRODUCTNAME, A.NOMINAL
								FROM t_reservationdetails A
								LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
								LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
								LEFT JOIN t_schedulevendor D ON A.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS
								LEFT JOIN t_fee E ON A.IDRESERVATIONDETAILS = E.IDRESERVATIONDETAILS
								WHERE A.IDVENDOR = ".$idVendor." AND ".$conditionWhere." AND ".$conditionNominal." AND D.IDRESERVATIONDETAILS IS NOT NULL";
		$query				=	$this->db->query($baseQuery);
		$row				=	$query->row_array();
		
		if(isset($row)) return $row;
		return false;
	}
	
	public function getDataCollectPaymentInvoice($idReservation, $idVendor){
		$baseQuery	=	"SELECT GROUP_CONCAT(A.IDCOLLECTPAYMENT) AS STRARRIDCOLLECTPAYMENT, SUM(B.AMOUNTIDR) AS TOTALAMOUNTCOLLECTPAYMENT
						FROM t_collectpayment A
						LEFT JOIN t_reservationpayment B ON A.IDRESERVATIONPAYMENT = B.IDRESERVATIONPAYMENT
						WHERE A.IDRESERVATION = ".$idReservation." AND A.IDVENDOR = ".$idVendor." AND A.IDWITHDRAWALRECAP = 0 AND A.STATUSSETTLEMENTREQUEST IN (0,1)
						GROUP BY A.IDRESERVATION";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row;
		return false;
	}
	
	private function getTotalBookingHandleByVendor($idVendor, $bookingCode){
		$baseQuery	=	"SELECT COUNT(A.IDRESERVATIONDETAILS) AS TOTALBOOKINGVENDOR FROM t_reservationdetails A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						WHERE A.IDVENDOR = ".$idVendor." AND B.BOOKINGCODE = '".$bookingCode."'";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row['TOTALBOOKINGVENDOR'];
		return 0;
	}
	
	public function getDataListCollectPayment($idVendor){
		$baseQuery	=	"SELECT DATE_FORMAT(A.DATECOLLECT, '%d %b %Y') AS DATECOLLECT, B.INPUTTYPE, C.SOURCENAME, B.BOOKINGCODE, B.CUSTOMERNAME,
								B.RESERVATIONTITLE, B.REMARK, D.DESCRIPTION, D.AMOUNTCURRENCY, D.AMOUNT, D.AMOUNTIDR
						FROM t_collectpayment A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
						LEFT JOIN t_reservationpayment D ON A.IDRESERVATIONPAYMENT = D.IDRESERVATIONPAYMENT
						WHERE A.IDPARTNERTYPE = 1 AND A.IDVENDOR = ".$idVendor." AND A.STATUSSETTLEMENTREQUEST != 2
						ORDER BY DATECOLLECT DESC"; 
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getDataListDepositHistory($idVendor, $startDateDeposit, $endDateDeposit){
		$baseQuery	=	"SELECT A.USERINPUT, DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUTSTR, A.DESCRIPTION, 
								IFNULL(C.INPUTTYPE, '-') AS INPUTTYPE, IFNULL(D.SOURCENAME, '-') AS SOURCENAME, IFNULL(C.BOOKINGCODE, '-') AS BOOKINGCODE,
								IFNULL(C.CUSTOMERNAME, '-') AS CUSTOMERNAME, IFNULL(C.RESERVATIONTITLE, '-') AS RESERVATIONTITLE,
								IFNULL(F.DESCRIPTION, '-') AS PAYMENTDESCRIPTION, IFNULL(F.AMOUNTCURRENCY, '-') AS PAYMENTAMOUNTCURRENCY,
								IFNULL(F.AMOUNT, '-') AS PAYMENTAMOUNT, IFNULL(F.EXCHANGECURRENCY, '-') AS PAYMENTEXCHANGECURRENCY,
								IFNULL(F.AMOUNTIDR, '-') AS PAYMENTAMOUNTIDR, IFNULL(DATE_FORMAT(E.DATETIMESTATUS, '%d %b %Y %H:%i'), '-') AS COLLECTDATETIMESTATUS,
								IFNULL(E.LASTUSERINPUT, '-') AS COLLECTUSERAPPROVE, A.AMOUNT, A.IDRESERVATIONDETAILS, A.IDCOLLECTPAYMENT,
								IFNULL(CONCAT('".URL_TRANSFER_RECEIPT."', A.TRANSFERRECEIPT), '') AS TRANSFERRECEIPT
						FROM t_depositvendorrecord A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						LEFT JOIN t_reservation C ON B.IDRESERVATION = C.IDRESERVATION
						LEFT JOIN m_source D ON C.IDSOURCE = D.IDSOURCE
						LEFT JOIN t_collectpayment E ON A.IDCOLLECTPAYMENT = E.IDCOLLECTPAYMENT
						LEFT JOIN t_reservationpayment F ON E.IDRESERVATIONPAYMENT = F.IDRESERVATIONPAYMENT
						WHERE A.IDVENDOR = ".$idVendor." AND A.DATETIMEINPUT BETWEEN '".$startDateDeposit."' AND '".$endDateDeposit."'
						ORDER BY A.DATETIMEINPUT"; 
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getDataWithdrawalRequest($startDate, $endDate, $idVendor, $statusWithdrawal, $viewRequestOnly){
		$con_idVendor	=	$con_date	=	$con_statusWithdrawal	=	$con_statusRequest	=	"1=1";
		
		if($viewRequestOnly == false || !isset($viewRequestOnly) || $viewRequestOnly == ""){
			$con_idVendor			=	!isset($idVendor) || $idVendor == "" ? "1=1" : "A.IDVENDOR = ".$idVendor;
			$con_date				=	"DATE(A.DATETIMEREQUEST) BETWEEN '".$startDate."' AND '".$endDate."'";
			$con_statusWithdrawal	=	!isset($statusWithdrawal) || $statusWithdrawal == "" ? "1=1" : "A.STATUSWITHDRAWAL = ".$statusWithdrawal;
		} else {
			$con_statusRequest		=	"A.STATUSWITHDRAWAL = 0";
		}

		$baseQuery	=	"SELECT A.IDWITHDRAWALRECAP, CONCAT('[', C.VENDORTYPE, '] ', B.NAME) AS VENDORNAME, DATE_FORMAT(A.DATETIMEREQUEST, '%d %b %Y %H:%i') AS DATETIMEREQUEST,
								A.MESSAGE, CONCAT('".URL_BANK_LOGO."', D.BANKLOGO) AS BANKLOGO, D.BANKNAME, A.ACCOUNTNUMBER, A.ACCOUNTHOLDERNAME, A.TOTALFEE, A.TOTALADDITIONALCOST,
								A.TOTALCOLLECTPAYMENT, A.TOTALDEDUCTION, A.TOTALWITHDRAWAL, A.STATUSWITHDRAWAL
						 FROM t_withdrawalrecap A
						 LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
						 LEFT JOIN m_vendortype C ON B.IDVENDORTYPE = C.IDVENDORTYPE
						 LEFT JOIN m_bank D ON A.IDBANK = D.IDBANK
						 WHERE A.IDVENDOR != 0 AND ".$con_idVendor." AND ".$con_date." AND ".$con_statusWithdrawal." AND ".$con_statusRequest."
						 ORDER BY A.DATETIMEREQUEST DESC";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getDetailWithdrawalRequest($idWithdrawalRecap){
		$baseQuery	=	"SELECT CONCAT('[', C.VENDORTYPE, '] ', B.NAME) AS VENDORNAME, DATE_FORMAT(A.DATETIMEREQUEST, '%d %b %Y %H:%i') AS DATETIMEREQUEST,
								A.MESSAGE, CONCAT('".URL_BANK_LOGO."', D.BANKLOGO) AS BANKLOGO, D.BANKNAME, A.ACCOUNTNUMBER, A.ACCOUNTHOLDERNAME, A.TOTALFEE, A.TOTALADDITIONALCOST, A.TOTALCOLLECTPAYMENT,
								A.TOTALDEDUCTION, A.TOTALWITHDRAWAL, IF(A.DATETIMEAPPROVAL = '0000-00-00 00:00:00', '-', DATE_FORMAT(A.DATETIMEAPPROVAL, '%d %b %Y %H:%i')) AS DATETIMEAPPROVAL,
								IF(A.USERAPPROVAL IS NULL OR A.USERAPPROVAL = '', '-', A.USERAPPROVAL) AS USERAPPROVAL, A.STATUSWITHDRAWAL, A.IDVENDOR, A.IDBANK,
								B.EMAIL AS VENDOREMAIL, IF(E.RECEIPTFILE IS NOT NULL AND E.RECEIPTFILE != '', CONCAT('".URL_HTML_TRANSFER_RECEIPT."', E.RECEIPTFILE), '') AS RECEIPTFILE
						 FROM t_withdrawalrecap A
						 LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
						 LEFT JOIN m_vendortype C ON B.IDVENDORTYPE = C.IDVENDORTYPE
						 LEFT JOIN m_bank D ON A.IDBANK = D.IDBANK
						 LEFT JOIN t_transferlist E ON A.IDWITHDRAWALRECAP = E.IDWITHDRAWAL
						 WHERE A.IDWITHDRAWALRECAP = ".$idWithdrawalRecap."
						 LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row;
		return false;
	}
	
	public function getListDetailWithdrawal($idWithdrawalRecap){
		$baseQuery	=	"SELECT DATESTR, TYPESTR, DESCRIPTION, NOMINAL FROM (
							  SELECT 1 AS TYPE, 'Fee' AS TYPESTR, DATESCHEDULE AS DATEDB, DATE_FORMAT(DATESCHEDULE, '%d %b %Y') AS DATESTR,
									 JOBTITLE AS DESCRIPTION, FEENOMINAL AS NOMINAL
							  FROM t_fee
							  WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap."
							  UNION ALL
							  SELECT 2 AS TYPE, 'Additional Cost' AS TYPESTR, DATE(DATE) AS DATEDB, DATE_FORMAT(DATE, '%d %b %Y') AS DATESTR,
									 IFNULL(DESCRIPTION, '-') AS DESCRIPTION, NOMINAL
							  FROM t_withdrawalcostdeduction
							  WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap." AND COSTDEDUCTIONTYPE = 1
							  UNION ALL
							  SELECT 3 AS TYPE, 'Collect Payment' AS TYPESTR, DATE(CA.DATECOLLECT) AS DATEDB, DATE_FORMAT(CA.DATECOLLECT, '%d %b %Y') AS DATESTR,
									 IFNULL(CB.DESCRIPTION, '-') AS DESCRIPTION, CB.AMOUNTIDR * -1 AS NOMINAL
							  FROM t_collectpayment CA
							  LEFT JOIN t_reservationpayment CB ON CA.IDRESERVATIONPAYMENT = CB.IDRESERVATIONPAYMENT
							  WHERE CA.IDWITHDRAWALRECAP = ".$idWithdrawalRecap."
							  UNION ALL
							  SELECT 4 AS TYPE, 'Deduction' AS TYPESTR, DATE(DATE) AS DATEDB, DATE_FORMAT(DATE, '%d %b %Y') AS DATESTR,
									 IFNULL(DESCRIPTION, '-') AS DESCRIPTION, NOMINAL
							  FROM t_withdrawalcostdeduction
							  WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap." AND COSTDEDUCTIONTYPE = 2
						  ) AS A
						  ORDER BY DATEDB, TYPE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getDataFeeWithdrawal($idWithdrawalRecap){
		$baseQuery	=	"SELECT IDFEE FROM t_fee
						 WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap;
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getDataCollectPaymentWithdrawal($idWithdrawalRecap){
		$baseQuery	=	"SELECT IDCOLLECTPAYMENT, IDRESERVATIONPAYMENT FROM t_collectpayment
						 WHERE IDWITHDRAWALRECAP = ".$idWithdrawalRecap;
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}

	public function getIdReservationPaymentCollectPayment($idCollectPayment){
		$baseQuery	=	"SELECT IDRESERVATIONPAYMENT FROM t_collectpayment
						 WHERE IDCOLLECTPAYMENT = ".$idCollectPayment."
						 LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row['IDRESERVATIONPAYMENT'];
		return 0;		
	}
	
	public function getTotalWithdrawalRequest($idVendor = false){
		$con_vendor	=	$idVendor != false && $idVendor != 0 && $idVendor != "" ? "IDVENDOR = ".$idVendor : "1=1";
		$query		=	$this->db->query(
							"SELECT COUNT(IDWITHDRAWALRECAP) AS TOTALWITHDRAWALREQUEST
							FROM t_withdrawalrecap
							WHERE STATUSWITHDRAWAL = 0 AND ".$con_vendor."
							LIMIT 1"
						);
		$row		=	$query->row_array();

		if(isset($row)) return $row['TOTALWITHDRAWALREQUEST'];
		return 0;
	}
	
	public function getTotalActiveCollectPayment($idVendor){
		$query	=	$this->db->query(
						"SELECT COUNT(IDCOLLECTPAYMENT) AS TOTALACTIVECOLLECTPAYMENT
						FROM t_collectpayment
						WHERE STATUS = 0 AND IDVENDOR = ".$idVendor."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['TOTALACTIVECOLLECTPAYMENT'];
		return 0;	
	}
	
}