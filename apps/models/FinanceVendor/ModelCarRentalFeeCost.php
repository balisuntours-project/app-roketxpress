<?php
class ModelCarRentalFeeCost extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataAllCar(){
		$baseQuery	=	"SELECT A.IDCARVENDOR, CONCAT('[', IF(A.STATUS = 1, 'Active', 'Inactive'), '] [', B.NAME, '] ', A.BRAND, ' ', A.MODEL, ' - ', A.PLATNUMBER) AS CARDETAIL
						 FROM t_carvendor A
						 LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
						 LEFT JOIN m_cartype C ON A.IDCARTYPE = C.IDCARTYPE
						 ORDER BY A.STATUS DESC, B.NAME, A.BRAND, A.MODEL, A.PLATNUMBER";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;		
		return $result;
	}

	public function getDataRecapCarRentalCostFee($page, $dataPerPage= 25, $idVendorCar, $yearMonth, $searchKeyword){
		$ci					=&	get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_idVendorCar	=	!isset($idVendorCar) || $idVendorCar == "" ? "1=1" : "A.IDVENDOR = ".$idVendorCar;
		$con_searchKeyword	=	!isset($searchKeyword) || $searchKeyword == "" 
								? "1=1" 
								: "(B.NAME LIKE '%".$searchKeyword."%' OR C.NAME LIKE '%".$searchKeyword."%' OR A.PLATNUMBER LIKE '%".$searchKeyword."%' OR A.BRAND LIKE '%".$searchKeyword."%' OR A.MODEL LIKE '%".$searchKeyword."%' OR A.DESCRIPTION LIKE '%".$searchKeyword."%')";
		$baseQuery			=	"SELECT B.NAME AS VENDORNAME, IFNULL(C.NAME, '-') AS DRIVERNAME, A.PLATNUMBER, CONCAT(A.BRAND, ' ', A.MODEL, ' ', A.YEAR) AS CARDETAIL,
										A.DESCRIPTION, IFNULL(COUNT(D.IDSCHEDULECAR), 0) AS TOTALCARSCHEDULE, IFNULL(SUM(D.NOMINAL), 0) AS TOTALNOMINALFEE,
										IFNULL(SUM(E.NOMINAL), 0) AS TOTALNOMINALCOST, A.IDCARVENDOR
								FROM t_carvendor A
								LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
								LEFT JOIN m_driver C ON A.IDDRIVER = C.IDDRIVER
								LEFT JOIN (
									SELECT DA.IDCARVENDOR, DA.IDSCHEDULECAR, DB.NOMINAL FROM t_schedulecar DA
									LEFT JOIN t_reservationdetails DB ON DA.IDRESERVATIONDETAILS = DB.IDRESERVATIONDETAILS AND LEFT(DB.SCHEDULEDATE,7) = '".$yearMonth."' 
									WHERE DB.IDRESERVATIONDETAILS IS NOT NULL
								) AS D ON A.IDCARVENDOR = D.IDCARVENDOR
								LEFT JOIN (
									SELECT IDCARVENDOR, NOMINAL FROM t_carcost
									WHERE LEFT(DATECOSTRECOGNITION,7) = '".$yearMonth."'
								) AS E ON A.IDCARVENDOR = E.IDCARVENDOR
								WHERE ".$con_idVendorCar." AND ".$con_searchKeyword."
								GROUP BY A.IDCARVENDOR
								ORDER BY VENDORNAME, DRIVERNAME";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDCARVENDOR", $page, $dataPerPage);		
		return $ci->MainOperation->generateEmptyResult();
	}

	public function getDataDetailCarRentalFee($page, $dataPerPage= 25, $idVendorCar, $startDate, $endDate, $searchKeyword){		
		$ci					=&	get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_idVendorCar	=	!isset($idVendorCar) || $idVendorCar == "" ? "1=1" : "B.IDVENDOR = ".$idVendorCar;
		$con_searchKeyword	=	!isset($searchKeyword) || $searchKeyword == "" 
								? "1=1" 
								: "(C.NAME LIKE '%".$searchKeyword."%' OR H.NAME LIKE '%".$searchKeyword."%' OR B.PLATNUMBER LIKE '%".$searchKeyword."%' OR 
									B.BRAND LIKE '%".$searchKeyword."%' OR B.MODEL LIKE '%".$searchKeyword."%' OR B.DESCRIPTION LIKE '%".$searchKeyword."%' OR
									E.BOOKINGCODE LIKE '%".$searchKeyword."%' OR E.RESERVATIONTITLE LIKE '%".$searchKeyword."%' OR E.CUSTOMERNAME LIKE '%".$searchKeyword."%' OR
									D.PRODUCTNAME LIKE '%".$searchKeyword."%' OR D.NOTES LIKE '%".$searchKeyword."%')";
		$baseQuery			=	"SELECT A.IDSCHEDULECAR, C.NAME AS VENDORNAME, IFNULL(H.NAME, '-') AS DRIVERNAME, DATE_FORMAT(D.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATE,
										CONCAT(B.PLATNUMBER, '<br/>', B.BRAND, ' ', B.MODEL, ' ', B.YEAR) AS CARDETAILS, G.SOURCENAME, E.INPUTTYPE, E.BOOKINGCODE,
										E.RESERVATIONTITLE, E.CUSTOMERNAME, D.PRODUCTNAME, A.USERINPUT, D.NOTES, D.NOMINAL
								FROM t_schedulecar A
								LEFT JOIN t_carvendor B ON A.IDCARVENDOR = B.IDCARVENDOR
								LEFT JOIN m_vendor C ON B.IDVENDOR = C.IDVENDOR
								LEFT JOIN t_reservationdetails D ON A.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS
								LEFT JOIN t_reservation E ON D.IDRESERVATION = E.IDRESERVATION
								LEFT JOIN m_reservationtype F ON E.IDRESERVATIONTYPE = F.IDRESERVATIONTYPE
								LEFT JOIN m_source G ON E.IDSOURCE = G.IDSOURCE
								LEFT JOIN m_driver H ON B.IDDRIVER = H.IDDRIVER
								WHERE D.SCHEDULEDATE BETWEEN '".$startDate."' AND '".$endDate."' AND D.STATUS = 1 AND ".$con_idVendorCar." AND ".$con_searchKeyword."
								ORDER BY C.NAME, D.SCHEDULEDATE";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDSCHEDULECAR", $page, $dataPerPage);		
		return $ci->MainOperation->generateEmptyResult();
	}
	
	public function getDataDetailCarRentalCost($page, $dataPerPage= 25, $idVendorCar, $recognitionDate, $searchKeyword){		
		$ci					=&	get_instance();
		$ci->load->model('MainOperation');

		$startid		=	($page * 1 - 1) * $dataPerPage;
		$con_idVendor	=	!isset($idVendorCar) || $idVendorCar == "" ? "1=1" : "B.IDVENDOR = ".$idVendorCar;
		$con_recogDate	=	!isset($recognitionDate) || $recognitionDate == "" ? "1=1" : "A.DATECOSTRECOGNITION = '".$recognitionDate."'";
		$con_keyword	=	!isset($searchKeyword) || $searchKeyword == "" 
							? "1=1" 
							: "(A.DESCRIPTION LIKE '%".$searchKeyword."%' OR A.USERINPUT LIKE '%".$searchKeyword."%' OR A.USERAPPROVAL LIKE '%".$searchKeyword."%' OR 
								B.PLATNUMBER LIKE '%".$searchKeyword."%' OR B.BRAND LIKE '%".$searchKeyword."%' OR B.MODEL LIKE '%".$searchKeyword."%' OR B.DESCRIPTION LIKE '%".$searchKeyword."%' OR
								C.NAME LIKE '%".$searchKeyword."%' OR D.CARCOSTTYPE LIKE '%".$searchKeyword."%')";
		$baseQuery		=	"SELECT A.IDCARCOST, CONCAT('<b>', C.NAME, '</b><br/>', B.PLATNUMBER, '<br/>', B.BRAND, ' ', B.MODEL, ' ', B.YEAR) AS CARDETAILS, IFNULL(D.CARCOSTTYPE, 'Not Set') AS CARCOSTTYPE,
									A.DESCRIPTION, IFNULL(DATE_FORMAT(A.DATECOSTRECOGNITION, '%d %b %Y'), '-') AS DATECOSTRECOGNITION, A.USERINPUT, DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT,
									A.STATUSAPPROVAL, IF(A.USERAPPROVAL IS NULL OR A.USERAPPROVAL = '', '-', A.USERAPPROVAL) AS USERAPPROVAL, IFNULL(DATE_FORMAT(A.DATETIMEAPPROVAL, '%d %b %Y %H:%i'), '-') AS DATETIMEAPPROVAL,
									CONCAT('".URL_CAR_COST_RECEIPT."', IF(A.IMAGERECEIPT IS NULL OR A.IMAGERECEIPT = '', 'noimage.jpg', A.IMAGERECEIPT)) AS IMAGERECEIPT, IFNULL(E.DURATIONHOUR, 0) AS DURATIONHOUROFF,
									IFNULL(DATE_FORMAT(E.DATETIMESTART, '%d %b %Y %H:%i'), '-') AS DATETIMEOFFSTART, IFNULL(DATE_FORMAT(E.DATETIMEEND, '%d %b %Y %H:%i'), '-') AS DATETIMEOFFEND, A.NOMINAL
							FROM t_carcost A
							LEFT JOIN t_carvendor B ON A.IDCARVENDOR = B.IDCARVENDOR
							LEFT JOIN m_vendor C ON B.IDVENDOR = C.IDVENDOR
							LEFT JOIN m_carcosttype D ON A.IDCARCOSTTYPE = D.IDCARCOSTTYPE
							LEFT JOIN t_dayoffcardetail E ON A.IDDAYOFF = E.IDDAYOFF
							WHERE ".$con_idVendor." AND ".$con_recogDate." AND ".$con_keyword."
							ORDER BY A.DATECOSTRECOGNITION DESC";
		$query			=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result			=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDCARCOST", $page, $dataPerPage);		
		return $ci->MainOperation->generateEmptyResult();
	}
	
	public function getDetailCarCostById($idCarCost){		
		$baseQuery	=	"SELECT A.IDCARVENDOR, A.IDCARCOSTTYPE, A.DESCRIPTION, A.NOMINAL, A.IMAGERECEIPT, IFNULL(DATE_FORMAT(A.DATECOSTRECOGNITION, '%d-%m-%Y'), '-') AS DATECOSTRECOGNITION,
								A.STATUSAPPROVAL, CONCAT('".URL_CAR_COST_RECEIPT."', IF(A.IMAGERECEIPT IS NULL OR A.IMAGERECEIPT = '', 'noimage.jpg', A.IMAGERECEIPT)) AS IMAGERECEIPTURL,
								IFNULL(B.DURATIONHOUR, 0) AS DAYOFFDURATIONHOUR, IFNULL(DATE_FORMAT(B.DATETIMESTART, '%d %b %Y %H:%i'), '-') AS DAYOFFDATETIMESTART,
								IFNULL(DATE_FORMAT(B.DATETIMEEND, '%d %b %Y %H:%i'), '-') AS DAYOFFDATETIMEEND
						 FROM t_carcost A
						 LEFT JOIN t_dayoffcardetail B ON A.IDDAYOFF = B.IDDAYOFF
						 WHERE A.IDCARCOST = ".$idCarCost."
						 LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;	
	}
}