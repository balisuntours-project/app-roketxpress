<?php
class ModelDetailReservationPayment extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
		
	public function getDataReservationPayment($page, $dataPerPage= 25, $arrDates, $paymentStatus, $refundType, $idSource, $strArrIdReservation, $idPaymentMethod, $keywordSearch, $startDate, $endDate, $orderBy, $orderType, $viewUnmatchPaymentOnly){
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$startid				=	($page * 1 - 1) * $dataPerPage;
		$con_or_date			=	"";
		$order_query			=	$orderBy == "1" ? "A.IDRESERVATION" : "A.RESERVATIONDATESTART, A.RESERVATIONTIMESTART";
		$con_idPaymentMethod	=	!isset($idPaymentMethod) || $idPaymentMethod == "" ? "1=1" : "D.IDPAYMENTMETHOD = ".$idPaymentMethod;
		$con_source				=	!isset($idSource) || $idSource == "" ? "1=1" : "A.IDSOURCE = ".$idSource;
		$con_arrIdReservation	=	!isset($strArrIdReservation) || $strArrIdReservation == "" ? "1=1" : "A.IDRESERVATION IN (".$strArrIdReservation.")";
		$con_keywordSearch		=	!isset($keywordSearch) || $keywordSearch == "" ? "1=1" : "(A.RESERVATIONTITLE LIKE '%".$keywordSearch."%' OR A.BOOKINGCODE LIKE '%".$keywordSearch."%' OR A.CUSTOMERNAME LIKE '%".$keywordSearch."%')";
		$con_unmatchPayment		=	isset($viewUnmatchPaymentOnly) && $viewUnmatchPaymentOnly == true ? "STATUSMATCH = 1" : "1=1";
		
		if(count($arrDates) > 0){
			foreach($arrDates as $date){
				$con_or_date	.=	" OR '".$date."' BETWEEN A.RESERVATIONDATESTART AND A.RESERVATIONDATEEND";
			}
			
			$con_date			=	"(A.RESERVATIONDATESTART BETWEEN '".$startDate."' AND '".$endDate."' OR A.RESERVATIONDATEEND BETWEEN '".$startDate."' AND '".$endDate."' ".$con_or_date.")";
		}
		
		$con_paymentStatus	=	!isset($paymentStatus) || $paymentStatus == "" ? "1=1" : $this->generateConditionPaymentStatus($paymentStatus, $con_date, $idPaymentMethod);
		$con_refundType		=	!isset($refundType) || $refundType == "" ? "1=1" : "A.REFUNDTYPE = ".$refundType;
		$baseQuery			=	"SELECT * FROM (
									SELECT C.RESERVATIONTYPE, B.SOURCENAME, A.INPUTTYPE, A.RESERVATIONTITLE, A.DURATIONOFDAY, 
											DATE_FORMAT(A.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
											DATE_FORMAT(A.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND, A.INCOMEAMOUNTIDR,
											LEFT(A.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART, LEFT(A.RESERVATIONTIMEEND, 5) AS RESERVATIONTIMEEND,
											A.CUSTOMERNAME, A.CUSTOMERCONTACT, A.CUSTOMEREMAIL, A.NUMBEROFADULT, A.NUMBEROFCHILD, A.NUMBEROFINFANT,
											A.BOOKINGCODE, A.INCOMEAMOUNTCURRENCY, A.INCOMEAMOUNT, A.INCOMEEXCHANGECURRENCY, A.STATUS, A.IDRESERVATION, A.IDRESERVATIONTYPE,
											'' AS PAYMENTDATA, '' AS ARRDATESCHEDULE, 0 AS INCOMEAMOUNTFINANCE, A.HOTELNAME, A.REMARK,
											A.RESERVATIONDATESTART AS RESERVATIONDATEVALUE, A.INCOMEAMOUNTIDR != SUM(IFNULL(D.AMOUNTIDR, 0)) AS STATUSMATCH,
											A.REFUNDTYPE
									FROM t_reservation A
									LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
									LEFT JOIN m_reservationtype C ON A.IDRESERVATIONTYPE = C.IDRESERVATIONTYPE
									LEFT JOIN t_reservationpayment D ON A.IDRESERVATION = D.IDRESERVATION AND ".$con_idPaymentMethod."
									WHERE ".$con_paymentStatus." AND ".$con_refundType." AND ".$con_source." AND ".$con_keywordSearch." AND ".$con_date." AND ".$con_arrIdReservation."
									GROUP BY A.IDRESERVATION
									ORDER BY ".$order_query." ".$orderType."
								) AS A
								WHERE ".$con_unmatchPayment;
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDRESERVATION", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();		
	}
	
	private function generateConditionPaymentStatus($paymentStatus, $con_date, $idPaymentMethod){
		$baseQuery			=	"";
		$con_idPaymentMethod=	!isset($idPaymentMethod) || $idPaymentMethod == "" ? "1=1" : "B.IDPAYMENTMETHOD = ".$idPaymentMethod;
		switch($paymentStatus){
			case "-1"	:	
			case "0"	:	
			case "1"	:	
							$baseQuery	=	"SELECT GROUP_CONCAT(DISTINCT(B.IDRESERVATION)) AS STRIDRESERVATION
											FROM t_reservation A
											LEFT JOIN t_reservationpayment B ON A.IDRESERVATION = B.IDRESERVATION
											WHERE B.STATUS = ".$paymentStatus." AND ".$con_date." AND ".$con_idPaymentMethod;
							break;
			case "-2"	:	
							$baseQuery	=	"SELECT GROUP_CONCAT(DISTINCT(A.IDRESERVATION)) AS STRIDRESERVATION
											FROM t_reservation A
											LEFT JOIN t_reservationpayment B ON A.IDRESERVATION = B.IDRESERVATION
											WHERE B.IDRESERVATION IS NULL AND ".$con_date;
							break;
		}

		if($baseQuery == "") return "1=1";

		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return "1=1";

		$strArrIdReservation	=	$row['STRIDRESERVATION'];
		return $strArrIdReservation == null || $strArrIdReservation == "" ? "A.IDRESERVATION = 0" : "A.IDRESERVATION IN (".$strArrIdReservation.")";
	}
	
	public function getPaymentData($idReservation, $idPaymentMethod){
		$con_idPaymentMethod=	!isset($idPaymentMethod) || $idPaymentMethod == "" ? "1=1" : "A.IDPAYMENTMETHOD = ".$idPaymentMethod;
		$query				=	$this->db->query(
									"SELECT A.IDRESERVATIONPAYMENT, A.IDPAYMENTMETHOD, B.PAYMENTMETHODNAME, A.DESCRIPTION,
										   A.AMOUNTCURRENCY, A.AMOUNT, A.AMOUNTIDR, A.USERINPUT, DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT,
										   A.STATUS, DATE_FORMAT(A.DATETIMEUPDATE, '%d %b %Y %H:%i') AS DATETIMEUPDATE, A.USERUPDATE, A.EDITABLE, A.DELETABLE,
										   IFNULL(C.IDVENDOR, 0) AS IDVENDORCOLLECT, IFNULL(C.IDDRIVER, 0) AS IDDRIVERCOLLECT,
										   IFNULL(C.DATECOLLECT, '') AS DATECOLLECT, A.ISUPSELLING
									FROM t_reservationpayment A
									LEFT JOIN m_paymentmethod B ON A.IDPAYMENTMETHOD = B.IDPAYMENTMETHOD
									LEFT JOIN t_collectpayment C ON A.IDRESERVATIONPAYMENT = C.IDRESERVATIONPAYMENT
									WHERE A.IDRESERVATION = '".$idReservation."' AND ".$con_idPaymentMethod."
									ORDER BY A.DATETIMEINPUT"
								);
		$result				=	$query->result();

		if(isset($result)) return $result;
		return false;		
	}
	
	public function getStrArrIdReservationByIdVendorCar($idVendorCar){
		$baseQuery	=	sprintf(
							"SELECT GROUP_CONCAT(B.IDRESERVATION SEPARATOR ',') AS STRARRIDRESERVATION
							 FROM t_schedulecar A
							 LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
							 LEFT JOIN t_carvendor C ON A.IDCARVENDOR = C.IDCARVENDOR
							 WHERE C.IDVENDOR = ".$idVendorCar."
							 GROUP BY C.IDVENDOR"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return "";
		return $row['STRARRIDRESERVATION'];	
	}
	
	public function getStrArrIdReservationByIdVendorTicket($idVendorTicket){
		$baseQuery	=	sprintf(
							"SELECT GROUP_CONCAT(IDRESERVATION SEPARATOR ',') AS STRARRIDRESERVATION
							 FROM t_reservationdetails
							 WHERE IDVENDOR = ".$idVendorTicket."
							 GROUP BY IDVENDOR"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return "";
		return $row['STRARRIDRESERVATION'];	
	}
	
	public function getStrArrIdReservationByIdDriver($idDriver){
		$baseQuery	=	sprintf(
							"SELECT GROUP_CONCAT(B.IDRESERVATION SEPARATOR ',') AS STRARRIDRESERVATION
							 FROM t_scheduledriver A
							 LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
							 WHERE A.IDDRIVER = ".$idDriver."
							 GROUP BY A.IDDRIVER"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return "";
		return $row['STRARRIDRESERVATION'];	
	}
	
	public function getIdReservationByBookingCode($bookingCode, $idSource){
		$baseQuery	=	"SELECT A.IDRESERVATION, B.IDRESERVATIONPAYMENT, A.BOOKINGCODE, B.AMOUNTCURRENCY, B.AMOUNT AS AMOUNTDB, A.CUSTOMERNAME,
								A.RESERVATIONTITLE, DATE_FORMAT(A.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATE, A.STATUS AS STATUSDB
						 FROM t_reservation A
						 LEFT JOIN t_reservationpayment B ON A.IDRESERVATION = B.IDRESERVATION AND B.IDPAYMENTMETHOD = 1
						 WHERE A.BOOKINGCODE = '".$bookingCode."' AND A.IDSOURCE = ".$idSource." AND B.STATUS = 0
						 LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;	
	}
	
	public function isDataPaymentOTAExist($idReservation, $idReservationPayment, $settlementStatus, $currency, $amount){
		$baseQuery	=	"SELECT IDRESERVATIONPAYMENTOTA FROM t_reservationpaymentota
						 WHERE IDRESERVATION  = '".$idReservation."' AND IDRESERVATIONPAYMENT = ".$idReservationPayment." AND
							   SETTLEMENTSTATUS = '".$settlementStatus."' AND CURRENCY = '".$currency."' AND AMOUNTORIGIN = '".$amount."'
						 LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row['IDRESERVATIONPAYMENTOTA'];	
	}
	
	public function getDetailReservationPayment($idReservationPayment){
		$query	=	$this->db->query(
						"SELECT A.IDRESERVATIONPAYMENT, A.IDPAYMENTMETHOD, A.DESCRIPTION, A.AMOUNTCURRENCY, A.AMOUNT, A.EXCHANGECURRENCY,
							   A.AMOUNTIDR, A.STATUS, A.EDITABLE, A.DELETABLE, A.IDRESERVATION, IFNULL(B.IDCOLLECTPAYMENT, 0) AS IDCOLLECTPAYMENT,
							   IFNULL(IF(B.IDPARTNERTYPE = 1, C.NEWFINANCESCHEME, D.NEWFINANCESCHEME), 0) AS NEWFINANCESCHEME
						FROM t_reservationpayment A
						LEFT JOIN t_collectpayment B ON A.IDRESERVATIONPAYMENT = B.IDRESERVATIONPAYMENT
						LEFT JOIN m_vendor C ON B.IDVENDOR = C.IDVENDOR
						LEFT JOIN m_driver D ON B.IDDRIVER = D.IDDRIVER
						WHERE A.IDRESERVATIONPAYMENT = '".$idReservationPayment."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function isCollectPaymentExist($idReservationPymnt){
		$query	=	$this->db->query(
						"SELECT IDCOLLECTPAYMENT FROM t_collectpayment
						WHERE IDRESERVATIONPAYMENT = '".$idReservationPymnt."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['IDCOLLECTPAYMENT'];
		return false;		
	}
	
	public function checkDataDriverSchedule($idReservation, $date){
		$query	=	$this->db->query(
						"SELECT A.IDDRIVER, C.NAME AS PARTNERNAME, A.STATUS, C.NEWFINANCESCHEME FROM t_scheduledriver A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						LEFT JOIN m_driver C ON A.IDDRIVER = C.IDDRIVER
						WHERE B.IDRESERVATION = '".$idReservation."' AND B.SCHEDULEDATE = '".$date."' AND B.STATUS = 1
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function checkDataVendorSchedule($idReservation){
		$query	=	$this->db->query(
						"SELECT A.IDVENDOR, B.NAME AS PARTNERNAME FROM t_reservationdetails A
						LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
						WHERE A.IDRESERVATION = '".$idReservation."' AND A.STATUS = 1 AND A.IDVENDOR != 0
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function getDataReservationByKeyword($idReservation, $searchKeyword, $customerName, $bookingCode){
		$condition			=	"";
		
		if($customerName == "" && $bookingCode == ""){
			$condition		=	"A.CUSTOMERNAME LIKE '%".$searchKeyword."%' OR A.BOOKINGCODE LIKE '%".$searchKeyword."%' OR A.REMARK LIKE '%".$searchKeyword."%' OR A.TOURPLAN LIKE '%".$searchKeyword."%'";
		} else {
			$condition		=	"A.CUSTOMERNAME LIKE '%".$customerName."%' OR A.BOOKINGCODE LIKE '%".$bookingCode."%'";
		}
		
		$baseQuery			=	"SELECT B.SOURCENAME, A.INPUTTYPE, A.RESERVATIONTITLE, DATE_FORMAT(A.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
										DATE_FORMAT(A.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND, LEFT(A.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART,
										LEFT(A.RESERVATIONTIMEEND, 5) AS RESERVATIONTIMEEND, A.CUSTOMERNAME, A.BOOKINGCODE, A.DURATIONOFDAY, A.REMARK, A.TOURPLAN,
										A.IDRESERVATION
								FROM t_reservation A
								LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
								WHERE A.IDRESERVATION != ".$idReservation." AND (".$condition.")
								ORDER BY A.RESERVATIONDATESTART DESC
								LIMIT 50";
		$query				=	$this->db->query($baseQuery);
		$result				=	$query->result();

		if(isset($result)) return $result;
		return false;		
	}

	public function getIdCollectPaymentByIdReservationPayment($idReservationPayment){
		$query	=	$this->db->query(
						"SELECT IDCOLLECTPAYMENT FROM t_collectpayment
						WHERE IDRESERVATIONPAYMENT = ".$idReservationPayment."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['IDCOLLECTPAYMENT'];
		return 0;		
	}
	
	public function getDetailCollectPayment($idCollectPayment){
		$query	=	$this->db->query(
						"SELECT A.IDRESERVATION, B.CUSTOMERNAME, B.RESERVATIONTITLE, C.DESCRIPTION, C.AMOUNT, C.AMOUNTCURRENCY, C.EXCHANGECURRENCY,
							   C.AMOUNTIDR, DATE_FORMAT(A.DATECOLLECT, '%d %b %Y') AS DATECOLLECTSTR, A.DATECOLLECT AS DATECOLLECTDB, A.STATUSSETTLEMENTREQUEST,
							   A.IDDRIVER, A.IDVENDOR
						FROM t_collectpayment A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN t_reservationpayment C ON A.IDRESERVATIONPAYMENT = C.IDRESERVATIONPAYMENT
						WHERE A.IDCOLLECTPAYMENT = '".$idCollectPayment."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
}