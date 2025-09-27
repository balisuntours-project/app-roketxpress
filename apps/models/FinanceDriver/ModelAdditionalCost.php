<?php
class ModelAdditionalCost extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataAdditionalCostApproval($idDriverType, $idDriver, $startDate, $endDate, $viewRequestOnly){
		
		$con_idDriverType	=	$con_idDriver	=	$con_date	=	"1=1";
		if(!$viewRequestOnly){
			$con_idDriverType	=	!isset($idDriverType) || $idDriverType == "" ? "1=1" : "E.IDDRIVERTYPE = ".$idDriverType;
			$con_idDriver		=	!isset($idDriver) || $idDriver == "" ? "1=1" : "A.IDDRIVER = ".$idDriver;
			$con_date			=	"DATE(A.DATETIMEINPUT) BETWEEN '".$startDate."' AND '".$endDate."'";
		}
		
		$baseQuery			=	sprintf("SELECT A.IDRESERVATIONADDITIONALCOST, D.RESERVATIONTITLE, B.PRODUCTNAME,
												D.CUSTOMERNAME, C.ADDITIONALCOSTTYPE, A.DESCRIPTION, A.NOMINAL, E.NAME AS DRIVERNAME,
												CONCAT('".URL_ADDITIONAL_COST_IMAGE."', A.IMAGERECEIPT) AS IMAGERECEIPT,
												DATE_FORMAT(A.DATETIMEINPUT, '%s') AS DATETIMEINPUT,
												IF(A.STATUSAPPROVAL != 0, DATE_FORMAT(A.DATETIMEAPPROVAL, '%s'), '-') AS DATETIMEAPPROVAL,
												IF(A.STATUSAPPROVAL != 0, A.USERAPPROVAL, '-') AS USERAPPROVAL,
												CASE
													WHEN A.STATUSAPPROVAL = 0 THEN 'Waiting for approval'
													WHEN A.STATUSAPPROVAL = 1 THEN 'Approved'
													WHEN A.STATUSAPPROVAL = -1 THEN 'Rejected'
													ELSE '-'
												END AS STRSTATUSAPPROVAL,
												A.STATUSAPPROVAL
										 FROM t_reservationadditionalcost A
										 LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
										 LEFT JOIN m_additionalcosttype C ON A.IDADDITIONALCOSTTYPE = C.IDADDITIONALCOSTTYPE
										 LEFT JOIN t_reservation D ON B.IDRESERVATION = D.IDRESERVATION
										 LEFT JOIN m_driver E ON A.IDDRIVER = E.IDDRIVER
										 WHERE ".$con_idDriverType." AND ".$con_idDriver." AND ".$con_date." AND A.STATUSAPPROVAL = 0
										 ORDER BY A.DATETIMEINPUT ASC"
									, "%d %b %Y %H:%i"
									, "%d %b %Y %H:%i"
							); 
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return false;
		}
		
		return $result;
		
	}

	public function getDataAdditionalCostHistory($page, $dataPerPage= 25, $idDriverType, $idDriver, $startDate, $endDate){
		
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_idDriverType	=	!isset($idDriverType) || $idDriverType == "" ? "1=1" : "E.IDDRIVERTYPE = ".$idDriverType;
		$con_idDriver		=	!isset($idDriver) || $idDriver == "" ? "1=1" : "A.IDDRIVER = ".$idDriver;
		$baseQuery			=	sprintf("SELECT A.IDRESERVATIONADDITIONALCOST, D.RESERVATIONTITLE, B.PRODUCTNAME,
												D.CUSTOMERNAME, C.ADDITIONALCOSTTYPE, A.DESCRIPTION, A.NOMINAL, E.NAME AS DRIVERNAME,
												CONCAT('".URL_ADDITIONAL_COST_IMAGE."', A.IMAGERECEIPT) AS IMAGERECEIPT,
												DATE_FORMAT(A.DATETIMEINPUT, '%s') AS DATETIMEINPUT,
												IF(A.STATUSAPPROVAL != 0, DATE_FORMAT(A.DATETIMEAPPROVAL, '%s'), '-') AS DATETIMEAPPROVAL,
												IF(A.STATUSAPPROVAL != 0, A.USERAPPROVAL, '-') AS USERAPPROVAL,
												CASE
													WHEN A.STATUSAPPROVAL = 0 THEN 'Waiting for approval'
													WHEN A.STATUSAPPROVAL = 1 THEN 'Approved'
													WHEN A.STATUSAPPROVAL = -1 THEN 'Rejected'
													ELSE '-'
												END AS STRSTATUSAPPROVAL,
												A.STATUSAPPROVAL
										 FROM t_reservationadditionalcost A
										 LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
										 LEFT JOIN m_additionalcosttype C ON A.IDADDITIONALCOSTTYPE = C.IDADDITIONALCOSTTYPE
										 LEFT JOIN t_reservation D ON B.IDRESERVATION = D.IDRESERVATION
										 LEFT JOIN m_driver E ON A.IDDRIVER = E.IDDRIVER
										 WHERE ".$con_idDriverType." AND ".$con_idDriver." AND DATE(A.DATETIMEINPUT) BETWEEN '".$startDate."' AND '".$endDate."'
										 ORDER BY A.DATETIMEINPUT ASC"
									, "%d %b %Y %H:%i"
									, "%d %b %Y %H:%i"
								);
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDRESERVATIONADDITIONALCOST", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}
	
	public function getTotalAdditionalCostRequest(){
		
		$query	=	$this->db->query("SELECT COUNT(IDRESERVATIONADDITIONALCOST) AS TOTALADDITIONALCOSTREQUEST
									  FROM t_reservationadditionalcost
									  WHERE STATUSAPPROVAL = 0
									  LIMIT 1");
		$row	=	$query->row_array();

		if(isset($row)){
			return $row['TOTALADDITIONALCOSTREQUEST'];
		}
		
		return 0;
		
	}
	
	public function getListReservationByKeywordAndDate($idDriver, $reservationDate, $reservationKeyword){
		$con_driver	=	isset($idDriver) && $idDriver != "" ? "D.IDDRIVER = ".$idDriver : "1=1";
		$minDate	=	date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
		$query		=	$this->db->query("SELECT C.SOURCENAME, B.RESERVATIONTITLE, B.DURATIONOFDAY, DATE_FORMAT(B.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
												 DATE_FORMAT(B.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND, B.CUSTOMERNAME, B.CUSTOMERCONTACT, B.CUSTOMEREMAIL,
												 B.BOOKINGCODE, A.IDRESERVATIONDETAILS, B.RESERVATIONDATESTART AS RESERVATIONDATEVALUE, E.NAME AS DRIVERNAME, D.IDDRIVER
										 FROM t_reservationdetails A
										 LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
										 LEFT JOIN m_source C ON B.IDSOURCE = C.IDSOURCE
										 LEFT JOIN t_scheduledriver D ON A.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS
										 LEFT JOIN m_driver E ON D.IDDRIVER = E.IDDRIVER
										 WHERE A.SCHEDULEDATE > '".$minDate."' AND A.SCHEDULEDATE = '".$reservationDate."' AND A.STATUS = 1 AND D.IDDRIVER IS NOT NULL AND ".$con_driver." AND
											  (B.CUSTOMERNAME LIKE '%".$reservationKeyword."%' OR B.RESERVATIONTITLE LIKE '%".$reservationKeyword."%' OR B.CUSTOMERCONTACT LIKE '%".$reservationKeyword."%'
											  OR B.CUSTOMEREMAIL LIKE '%".$reservationKeyword."%' OR B.BOOKINGCODE LIKE '%".$reservationKeyword."%' OR E.NAME LIKE '%".$reservationKeyword."%')
										 ORDER BY B.RESERVATIONDATESTART, A.IDRESERVATION");
		$result		=	$query->result();

		if(isset($result)){
			return $result;
		}
		
		return false;
	}

}