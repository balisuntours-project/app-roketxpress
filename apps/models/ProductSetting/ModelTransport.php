<?php
class ModelTransport extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataDriverFee($driverType, $searchKeyword){
		$con_driverType		=	!isset($driverType) || $driverType == "" ? "1=1" : "C.IDDRIVERTYPE = ".$driverType;
		$con_searchKeyword	=	!isset($searchKeyword) || $searchKeyword == "" ? "1=1" : "(B.PRODUCTNAME LIKE '%".$searchKeyword."%' OR C.ADDITIONALINFO LIKE '%".$searchKeyword."%')";
		$baseQuery			=	"SELECT C.IDDRIVERFEE, B.PRODUCTNAME, C.ADDITIONALINFO, IFNULL(C.JOBTYPE, 0) AS JOBTYPE, IFNULL(C.JOBRATE, 1) AS JOBRATE, IFNULL(E.AREANAME, '-') AS AREANAME, C.SCHEDULETYPE,
										C.IDDRIVERTYPE, D.DRIVERTYPE, C.IDSOURCE, IFNULL(F.SOURCENAME, 'Not Set') AS SOURCENAME, C.FEENOMINAL, C.COSTTICKETTYPE, C.COSTPARKINGTYPE, C.COSTMINERALWATERTYPE,
										C.COSTBREAKFASTTYPE, C.COSTLUNCHTYPE, C.BONUSTYPE, C.COSTTICKET, C.COSTPARKING, C.COSTMINERALWATER, C.COSTBREAKFAST, C.COSTLUNCH, C.BONUS
								 FROM m_productdetailtype A
								 LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
								 LEFT JOIN t_driverfee C ON B.IDPRODUCT = C.IDPRODUCT
								 LEFT JOIN m_drivertype D ON C.IDDRIVERTYPE = D.IDDRIVERTYPE
								 LEFT JOIN m_area E ON C.IDAREA = E.IDAREA AND E.NONAREA = 0
								 LEFT JOIN m_source F ON C.IDSOURCE = F.IDSOURCE
								 WHERE ".$con_driverType." AND ".$con_searchKeyword." AND C.IDDRIVERTYPE != 0 AND A.IDPRODUCTTYPE = 2 AND B.STATUS = 1 AND C.IDDRIVERFEE IS NOT NULL
								 ORDER BY B.PRODUCTNAME, D.DRIVERTYPE ASC, E.AREANAME"; 
		$query				=	$this->db->query($baseQuery);
		$result				=	$query->result();
		
		if(!$result) return false;		
		return $result;
	}

	public function getDataTransportProduct(){
		$baseQuery	=	"SELECT B.IDPRODUCT, B.PRODUCTNAME, IF(C.IDPRODUCT IS NULL, 0, 1) AS ISPRODUCTSET FROM m_productdetailtype A
						 LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
						 LEFT JOIN t_driverfee C ON B.IDPRODUCT = C.IDPRODUCT
						 WHERE A.IDPRODUCTTYPE = 2 AND B.STATUS = 1
						 GROUP BY B.IDPRODUCT
						 ORDER BY ISPRODUCTSET, B.PRODUCTNAME"; 
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return [];		
		return $result;
	}
	
	public function getDetailDriverFee($idDriverFee){
		$baseQuery	=	"SELECT IFNULL(C.IDDRIVERFEE, 0) AS IDDRIVERFEE, B.IDPRODUCT, C.IDDRIVERTYPE, C.IDSOURCE, C.JOBTYPE, C.JOBRATE, C.SCHEDULETYPE,
								IFNULL(C.IDAREA, 0) AS IDAREA, B.PRODUCTNAME, IFNULL(C.FEENOMINAL, 0) AS FEENOMINAL, IFNULL(C.ADDITIONALINFO, '-') AS ADDITIONALINFO,
								C.COSTTICKETTYPE, C.COSTPARKINGTYPE, C.COSTMINERALWATERTYPE, C.COSTBREAKFASTTYPE, C.COSTLUNCHTYPE, C.BONUSTYPE, C.COSTTICKET, C.COSTPARKING,
								C.COSTMINERALWATER, C.COSTBREAKFAST, C.COSTLUNCH, C.BONUS
						 FROM m_productdetailtype A
						 LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
						 LEFT JOIN t_driverfee C ON B.IDPRODUCT = C.IDPRODUCT
						 WHERE C.IDDRIVERFEE = ".$idDriverFee."
						 LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)) return false;
		return $row;		
	}

	public function isDriverFeeExist($idProduct, $idDriverType, $idSource){
		$baseQuery	=	"SELECT IDDRIVERFEE FROM t_driverfee
						WHERE IDPRODUCT = ".$idProduct." AND IDDRIVERTYPE = ".$idDriverType." AND IDSOURCE = ".$idSource."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)) return false;
		return $row;		
	}
	
	public function getRankDriverFee($idProduct){
		
		$baseQuery	=	"SELECT PRODUCTRANK FROM t_driverfee
						WHERE IDPRODUCT = ".$idProduct."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)) return 999;
		
		return $row['PRODUCTRANK'];
		
	}
	
	public function getStrArrJobRateDriver($idDriver){
		
		$query	= $this->db->query("SELECT SUBSTRING_INDEX(GROUP_CONCAT(D.JOBRATE ORDER BY B.SCHEDULEDATE DESC, E.RESERVATIONTIMESTART DESC SEPARATOR ','), ',', 5) AS JOBRATE
									FROM t_scheduledriver A
									LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
									LEFT JOIN m_product C ON B.PRODUCTNAME = C.PRODUCTNAME
									LEFT JOIN t_driverfee D ON C.IDPRODUCT = D.IDPRODUCT
									LEFT JOIN t_reservation E ON B.IDRESERVATION = E.IDRESERVATION
									WHERE B.STATUS = 1 AND A.IDDRIVER = '".$idDriver."'
									GROUP BY A.IDDRIVER");
		$row	= $query->row_array();

		if(isset($row)){
			return $row['JOBRATE'];
		}
		
		return false;
		
	}
	
	public function getNextScheduleListByProduct($idProduct){
		
		$query	= $this->db->query("SELECT GROUP_CONCAT(IDRESERVATIONDETAILS) AS STRARRIDRESERVATIONDETAILS FROM t_reservationdetails A
									LEFT JOIN m_product B ON A.PRODUCTNAME = B.PRODUCTNAME
									LEFT JOIN t_driverfee C ON B.IDPRODUCT = C.IDPRODUCT
									WHERE C.IDPRODUCT = ".$idProduct." AND A.SCHEDULEDATE >= '".date('Y-m-d')."' AND A.IDDRIVERTYPE != 0");
		$row	= $query->row_array();

		if(isset($row)){
			return $row['STRARRIDRESERVATIONDETAILS'];
		}
		
		return false;
		
	}
	
	public function getTransportProductRank(){

		$baseQuery	=	"SELECT A.IDPRODUCT, B.PRODUCTNAME, A.JOBRATE, A.IDDRIVERTYPE, C.DRIVERTYPE FROM t_driverfee A
						 LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
						 LEFT JOIN m_drivertype C ON A.IDDRIVERTYPE = C.IDDRIVERTYPE
						 WHERE B.STATUS = 1
						 GROUP BY B.IDPRODUCT
						 ORDER BY A.PRODUCTRANK, B.PRODUCTNAME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!isset($result)) return false;
		return $result;

	}
	
	public function getDataReservationDetailsProduct($idProduct){

		$baseQuery	=	"SELECT GROUP_CONCAT(A.IDRESERVATIONDETAILS) AS ARRRESERVATIONDETAILS
						FROM (SELECT IDRESERVATIONDETAILS, PRODUCTNAME, IDPRODUCTTYPE FROM t_reservationdetails WHERE SCHEDULEDATE >= '".date('Y-m-d')."') A
						LEFT JOIN m_product B ON A.PRODUCTNAME = B.PRODUCTNAME
						LEFT JOIN t_scheduledriver C ON A.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
						WHERE A.IDPRODUCTTYPE = 2 AND C.IDRESERVATIONDETAILS IS NULL AND B.IDPRODUCT = ".$idProduct;
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)) return false;
		return $row['ARRRESERVATIONDETAILS'];

	}
	
}