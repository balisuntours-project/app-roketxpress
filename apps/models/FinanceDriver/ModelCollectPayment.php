<?php
class ModelCollectPayment extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataCollectPayment($page, $dataPerPage= 25, $idDriver, $startDate, $endDate, $collectStatus, $settlementStatus, $viewRequestOnly){
		
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$startid		=	($page * 1 - 1) * $dataPerPage;
		$con_idDriver	=	$con_date	=	$con_statusCollect	=	$con_statusSettlement	=	$con_statusRequest	=	"1=1";
		
		if(!$viewRequestOnly){
			$con_idDriver			=	!isset($idDriver) || $idDriver == "" ? "1=1" : "A.IDDRIVER = ".$idDriver;
			$con_date				=	"A.DATECOLLECT BETWEEN '".$startDate."' AND '".$endDate."'";
			$con_statusCollect		=	!isset($collectStatus) || $collectStatus == "" ? "1=1" : "A.STATUS = ".$collectStatus;
			$con_statusSettlement	=	!isset($settlementStatus) || $settlementStatus == "" ? "1=1" : "A.STATUSSETTLEMENTREQUEST = ".$settlementStatus;
		} else {
			$con_statusRequest		=	"A.STATUSSETTLEMENTREQUEST = 1";
		}
		
		$baseQuery		=	"SELECT A.IDCOLLECTPAYMENT, DATE_FORMAT(A.DATECOLLECT, '%d %b %Y') AS DATECOLLECT, IFNULL(F.DRIVERTYPE, '-') AS DRIVERTYPE, IFNULL(E.NAME, 'Not Set') AS DRIVERNAME,
									C.SOURCENAME, B.DURATIONOFDAY, B.RESERVATIONTITLE, DATE_FORMAT(B.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
									DATE_FORMAT(B.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND, B.CUSTOMERNAME, B.BOOKINGCODE, B.REMARK, D.DESCRIPTION,
									D.AMOUNTCURRENCY, D.AMOUNT, D.EXCHANGECURRENCY, D.AMOUNTIDR, A.STATUS, A.STATUSSETTLEMENTREQUEST, E.NEWFINANCESCHEME
							FROM t_collectpayment A
							LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
							LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
							LEFT JOIN t_reservationpayment D ON A.IDRESERVATIONPAYMENT = D.IDRESERVATIONPAYMENT
							LEFT JOIN m_driver E ON A.IDDRIVER = E.IDDRIVER
							LEFT JOIN m_drivertype F ON E.IDDRIVERTYPE = F.IDDRIVERTYPE
							WHERE A.IDPARTNERTYPE = 2 AND ".$con_date." AND ".$con_idDriver." AND ".$con_statusCollect." AND ".$con_statusSettlement." AND ".$con_statusRequest."
							ORDER BY DATECOLLECT, DRIVERNAME";
		$query			=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result			=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDCOLLECTPAYMENT", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}

	public function getTotalSettlementRequest(){
		
		$baseQuery	=	"SELECT IFNULL(COUNT(IDCOLLECTPAYMENT), 0) AS TOTALSETTLEMENTREQUEST FROM t_collectpayment
						WHERE IDPARTNERTYPE = 2 AND STATUSSETTLEMENTREQUEST = 1
						LIMIT 1"; 
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)){
			return $row['TOTALSETTLEMENTREQUEST'];
		}
		
		return 0;
		
	}

	public function getDetailCollectPayment($idCollectPayment){
		
		$baseQuery	=	"SELECT C.SOURCENAME, B.BOOKINGCODE, B.RESERVATIONTITLE, B.DURATIONOFDAY, DATE_FORMAT(B.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
								DATE_FORMAT(B.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND, LEFT(B.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART,
								LEFT(B.RESERVATIONTIMEEND, 5) AS RESERVATIONTIMEEND, B.CUSTOMERNAME, B.CUSTOMERCONTACT, B.CUSTOMEREMAIL, F.DRIVERTYPE,
								IFNULL(E.NAME, 'Not Set') AS DRIVERNAME, DATE_FORMAT(A.DATECOLLECT, '%d %b %Y') AS DATECOLLECT, D.AMOUNTCURRENCY, D.AMOUNT, D.EXCHANGECURRENCY,
								D.AMOUNTIDR, B.REMARK, D.DESCRIPTION, A.STATUS, A.STATUSSETTLEMENTREQUEST, E.NEWFINANCESCHEME, A.IDDRIVER, A.IDRESERVATION, 
								A.DATECOLLECT AS DATECOLLECTDB, CONCAT('".URL_COLLECT_PAYMENT_RECEIPT."', 'noimage.jpg') AS SETTLEMENTRECEIPT, A.IDRESERVATIONPAYMENT
						FROM t_collectpayment A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
						LEFT JOIN t_reservationpayment D ON A.IDRESERVATIONPAYMENT = D.IDRESERVATIONPAYMENT
						LEFT JOIN m_driver E ON A.IDDRIVER = E.IDDRIVER
						LEFT JOIN m_drivertype F ON E.IDDRIVERTYPE = F.IDDRIVERTYPE
						WHERE A.IDCOLLECTPAYMENT = ".$idCollectPayment."
						LIMIT 1"; 
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	public function getHistoryCollectPayment($idCollectPayment){
		
		$baseQuery	=	"SELECT DATE_FORMAT(DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT, DESCRIPTION, USERINPUT, STATUS,
								IF(SETTLEMENTRECEIPT = '', '', CONCAT('".URL_COLLECT_PAYMENT_RECEIPT."', SETTLEMENTRECEIPT)) AS SETTLEMENTRECEIPT,
								IDCOLLECTPAYMENTHISTORY
						FROM t_collectpaymenthistory
						WHERE IDCOLLECTPAYMENT = ".$idCollectPayment."
						ORDER BY DATE_FORMAT(DATETIMEINPUT, '%Y%m%d%H%i%s')"; 
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
}