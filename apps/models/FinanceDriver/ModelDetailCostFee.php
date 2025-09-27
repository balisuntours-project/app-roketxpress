<?php
class ModelDetailCostFee extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataDetailCostFee($page, $dataPerPage= 25, $idDriverType, $idDriver, $arrDates, $startDate, $endDate){
		$ci				=& get_instance();
		$ci->load->model('MainOperation');
	
		$year				=	substr($startDate, 0, 4);
		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_idDriverType	=	!isset($idDriverType) || $idDriverType == "" ? "1=1" : "F.IDDRIVERTYPE = ".$idDriverType;
		$con_idDriver		=	!isset($idDriver) || $idDriver == "" ? "1=1" : "E.IDDRIVER = ".$idDriver;
		$con_or_date		=	"";
		
		foreach($arrDates as $date){
			$con_or_date	.=	" OR '".$date."' BETWEEN AA.RESERVATIONDATESTART AND AA.RESERVATIONDATEEND";
		}
		
		$con_date	=	"(
						   AA.RESERVATIONDATESTART BETWEEN '".$startDate."' AND '".$endDate."' OR
						   AA.RESERVATIONDATEEND BETWEEN '".$startDate."' AND '".$endDate."'
						   ".$con_or_date."
						)";
		$baseQuery	=	"SELECT A.IDRESERVATION, E.IDDRIVER, IFNULL(F.DRIVERTYPE, '-') AS DRIVERTYPE, IFNULL(F.NAME, '-') AS DRIVERNAME, F.NEWFINANCESCHEME, A.IDRESERVATIONTYPE,
								A.RESERVATIONTYPE, A.SOURCENAME, A.INPUTTYPE, A.RESERVATIONTITLE, A.DURATIONOFDAY, DATE_FORMAT(A.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
								DATE_FORMAT(A.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND, LEFT(A.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART,
								LEFT(A.RESERVATIONTIMEEND, 5) AS RESERVATIONTIMEEND, A.CUSTOMERNAME, A.BOOKINGCODE,
								GROUP_CONCAT(
									JSON_ARRAY(
										DATE_FORMAT(D.SCHEDULEDATE, '%d %b %Y'),
										D.PRODUCTNAME,
										CONVERT(FORMAT(D.NOMINAL, 0) USING latin1),
										IFNULL(H.WITHDRAWSTATUS, -1)
									) ORDER BY D.SCHEDULEDATE
								) AS JOBDETAILS,
								SUM(D.NOMINAL) AS TOTALFEE, '' AS COSTDETAIL, 0 AS TOTALCOST, A.STATUS
						FROM (
							SELECT AA.IDRESERVATION, AA.IDRESERVATIONTYPE, AA.INPUTTYPE, AA.RESERVATIONTITLE, AA.DURATIONOFDAY, AA.RESERVATIONDATESTART, AA.RESERVATIONTIMESTART,
									AA.RESERVATIONDATEEND, AA.RESERVATIONTIMEEND, AA.CUSTOMERNAME, AA.BOOKINGCODE, AB.SOURCENAME, AA.STATUS, AC.RESERVATIONTYPE
							FROM t_reservation PARTITION (p_".$year.") AA
							LEFT JOIN m_source AB ON AA.IDSOURCE = AB.IDSOURCE
							LEFT JOIN m_reservationtype AC ON AA.IDRESERVATIONTYPE = AC.IDRESERVATIONTYPE
							WHERE ".$con_date."
						) AS A
						LEFT JOIN t_reservationdetails D ON A.IDRESERVATION = D.IDRESERVATION AND D.IDDRIVERTYPE != 0
						LEFT JOIN t_scheduledriver E ON D.IDRESERVATIONDETAILS = E.IDRESERVATIONDETAILS
						LEFT JOIN (
							SELECT FA.IDDRIVER, FA.IDDRIVERTYPE, FA.NAME, FB.DRIVERTYPE, FA.NEWFINANCESCHEME
							FROM m_driver FA
							LEFT JOIN m_drivertype FB ON FA.IDDRIVERTYPE = FB.IDDRIVERTYPE
						) AS F ON E.IDDRIVER = F.IDDRIVER
						LEFT JOIN t_fee H ON D.IDRESERVATIONDETAILS = H.IDRESERVATIONDETAILS
						WHERE ".$con_idDriverType." AND ".$con_idDriver." AND D.STATUS = 1 AND D.IDRESERVATIONDETAILS IS NOT NULL
						GROUP BY A.IDRESERVATION, E.IDDRIVER
						ORDER BY A.RESERVATIONDATESTART, F.DRIVERTYPE, F.NAME";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDRESERVATION", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}
	
	public function getDetailCost($idReservation, $idDriver){
		$baseQuery	=	"SELECT DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y') AS DATETIMEINPUT, B.ADDITIONALCOSTTYPE, A.DESCRIPTION, A.NOMINAL
						FROM t_reservationadditionalcost A
						LEFT JOIN m_additionalcosttype B ON A.IDADDITIONALCOSTTYPE = B.IDADDITIONALCOSTTYPE
						LEFT JOIN t_reservationdetails C ON A.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
						LEFT JOIN t_scheduledriver D ON C.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS
						WHERE C.IDRESERVATION = ".$idReservation." AND A.STATUSAPPROVAL IN (0,1) AND D.IDDRIVER = ".$idDriver."
						ORDER BY A.DATETIMEINPUT";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!isset($result)) return false;
		return $result;		
	}
}