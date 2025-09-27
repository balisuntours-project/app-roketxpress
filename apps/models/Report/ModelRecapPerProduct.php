<?php
class ModelRecapPerProduct extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataRecapPerProduct($page, $dataPerPage= 25, $productName, $idSource, $startDate, $endDate){
		
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_productName	=	!isset($productName) || $productName == "" ? "1=1" : "A.PRODUCTNAME LIKE '%".$productName."%'";
		$con_source			=	!isset($idSource) || $idSource == "" ? "1=1" : "A.IDSOURCE = ".$idSource;
		$baseQuery			=	"SELECT A.IDPRODUCTTYPE, B.PRODUCTTYPE, IF(A.IDVENDOR = 0, CONCAT('Driver ', E.DRIVERTYPE), D.NAME) AS PARTNERNAME,
										A.PRODUCTNAME, COUNT(A.IDRESERVATIONDETAILS) AS TOTALRESERVATION
								FROM t_reservationdetails A
								LEFT JOIN m_producttype B ON A.IDPRODUCTTYPE = B.IDPRODUCTTYPE
								LEFT JOIN t_reservation C ON A.IDRESERVATION = C.IDRESERVATION
								LEFT JOIN m_vendor D ON A.IDVENDOR = D.IDVENDOR
								LEFT JOIN m_drivertype E ON A.IDDRIVERTYPE = E.IDDRIVERTYPE
								WHERE ".$con_productName." AND ".$con_source." AND
									  A.SCHEDULEDATE BETWEEN '".$startDate."' AND '".$endDate."'
								GROUP BY A.PRODUCTNAME
								ORDER BY B.PRODUCTTYPE, PARTNERNAME, A.PRODUCTNAME";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDPRODUCTTYPE", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}
	
}