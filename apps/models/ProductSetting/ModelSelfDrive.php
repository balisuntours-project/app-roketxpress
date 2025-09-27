<?php
class ModelSelfDrive extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExists($idVendor, $idCarType, $duration, $idCarSelfDriveFee = 0){
		
		$idCarSelfDriveFee	= $idCarSelfDriveFee == "" ? 0 : $idCarSelfDriveFee;
		$query			= $this->db->query("SELECT B.NAME AS VENDORNAME, C.CARTYPE, A.DURATION
											FROM t_carselfdrivefee A
											LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
											LEFT JOIN m_cartype C ON A.IDCARTYPE = C.IDCARTYPE
											WHERE A.IDVENDOR = ".$idVendor." AND A.IDCARTYPE = ".$idCarType." AND A.DURATION = ".$duration." AND 
												  IDCARSELFDRIVEFEE <> ".$idCarSelfDriveFee."
											LIMIT 1");
		$row			= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	public function checkDataCarExists($platNumber, $idCarVendor = 0){
		
		$idCarVendor	= $idCarVendor == "" ? 0 : $idCarVendor;
		$query			= $this->db->query("SELECT PLATNUMBER FROM t_carvendor
											WHERE PLATNUMBER = '".$platNumber."' AND IDCARVENDOR <> ".$idCarVendor."
											LIMIT 1");
		$row			= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}

	public function getDataSelfDriveFees($carType){

		$con_carType	=	!isset($carType) || $carType == "" ? "1=1" : "A.IDCARTYPE = ".$carType;
		$baseQuery		=	"SELECT B.NAME AS VENDORNAME, C.CARTYPE, A.DURATION, A.NOMINALFEE, A.NOTES, A.IDCARSELFDRIVEFEE
							FROM t_carselfdrivefee A
							LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
							LEFT JOIN m_cartype C ON A.IDCARTYPE = C.IDCARTYPE
							WHERE ".$con_carType."
							ORDER BY B.NAME, C.CARTYPE, A.DURATION";
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!isset($result)){
			return false;
		}

		return $result;

	}
		
	public function getDetailSelfDriveFee($idCarSelfDriveFee){
		
		$baseQuery	=	"SELECT IDVENDOR, IDCARTYPE, DURATION, NOMINALFEE, NOTES
						FROM t_carselfdrivefee
						WHERE IDCARSELFDRIVEFEE = ".$idCarSelfDriveFee."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)){
			return false;
		}
		
		return $row;
		
	}
		
	public function getDataVendorCar($idVendor, $keywordSearch){
		$con_idVendor	=	!isset($idVendor) || $idVendor == "" ? "1=1" : "A.IDVENDOR = ".$idVendor;
		$con_keyword	=	!isset($keywordSearch) || $keywordSearch == "" ? "1=1" : "(B.NAME LIKE '%".$keywordSearch."%' OR C.CARTYPE LIKE '%".$keywordSearch."%' OR A.BRAND LIKE '%".$keywordSearch."%' OR A.MODEL LIKE '%".$keywordSearch."%' OR A.PLATNUMBER LIKE '%".$keywordSearch."%' OR A.DESCRIPTION LIKE '%".$keywordSearch."%')";
		$baseQuery		=	"SELECT B.NAME AS VENDORNAME, IFNULL(D.NAME, 'Not Set') AS DRIVERNAME, C.CARTYPE, A.BRAND, A.MODEL, A.PLATNUMBER, A.YEAR, A.TRANSMISSION, A.COLOR, A.DESCRIPTION,
									GROUP_CONCAT(CONCAT('- ', B.NAME, ' [', E.DURATION, ' Hours]	', '=', E.NOMINALFEE) SEPARATOR '|') AS CARCOSTFEELIST, A.STATUS, A.IDCARVENDOR
							FROM t_carvendor A
							LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
							LEFT JOIN m_cartype C ON A.IDCARTYPE = C.IDCARTYPE
							LEFT JOIN m_driver D ON A.IDDRIVER = D.IDDRIVER
							LEFT JOIN t_carselfdrivefee E ON A.IDVENDOR = E.IDVENDOR AND A.IDCARTYPE = E.IDCARTYPE
							WHERE ".$con_idVendor." AND ".$con_keyword."
							GROUP BY A.IDCARVENDOR
							ORDER BY B.NAME, C.CARTYPE, A.BRAND, A.MODEL, A.PLATNUMBER";
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!isset($result)) return false;

		return $result;
	}
	
	public function getDataDriverNoCar($idDriverDefault = false){
		$con_driverDefault	=	isset($idDriverDefault) && $idDriverDefault ? "OR A.IDDRIVER = ".$idDriverDefault : " AND 1=1";
		$baseQuery			=	"SELECT A.IDDRIVER AS ID, A.NAME AS VALUE FROM m_driver A
								LEFT JOIN t_carvendor B ON A.IDDRIVER = B.IDDRIVER
								WHERE (B.IDCARVENDOR IS NULL AND A.PARTNERSHIPTYPE = 4) ".$con_driverDefault."
								ORDER BY A.NAME";
		$query				=	$this->db->query($baseQuery);
		$result				=	$query->result();
		
		if(!isset($result)) return [];
		return $result;		
	}
	
	public function getDetailVendorCar($idCarVendor){
		
		$baseQuery	=	"SELECT IDVENDOR, IDCARTYPE, IDDRIVER, BRAND, MODEL, PLATNUMBER, YEAR, TRANSMISSION, COLOR, DESCRIPTION
						FROM t_carvendor
						WHERE IDCARVENDOR = ".$idCarVendor."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)){
			return false;
		}
		
		return $row;
		
	}

}