<?php
class ModelRecapFeePerProduct extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataRecapFeePerProduct($page, $dataPerPage= 25, $productName, $idProductType, $startDate, $endDate){
		
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_productName	=	!isset($productName) || $productName == "" ? "1=1" : "A.PRODUCTNAME LIKE '%".$productName."%'";
		$con_idProductType	=	!isset($idProductType) || $idProductType == "" ? "1=1" : "A.IDPRODUCTTYPE = ".$idProductType;
		$baseQuery			=	"SELECT A.IDPRODUCTTYPE, B.PRODUCTTYPE, A.PRODUCTNAME, COUNT(A.IDRESERVATIONDETAILS) AS TOTALRESERVATION,
										SUM(A.NOMINAL) AS TOTALFEE
								FROM t_reservationdetails A
								LEFT JOIN m_producttype B ON A.IDPRODUCTTYPE = B.IDPRODUCTTYPE
								LEFT JOIN t_reservation C ON A.IDRESERVATION = C.IDRESERVATION
								WHERE ".$con_productName." AND ".$con_idProductType." AND
									  A.SCHEDULEDATE BETWEEN '".$startDate."' AND '".$endDate."'
								GROUP BY A.PRODUCTNAME
								ORDER BY B.PRODUCTTYPE, A.PRODUCTNAME"; 
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDPRODUCTTYPE", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}
	
}