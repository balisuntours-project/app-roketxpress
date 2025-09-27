<?php
class ModelAgentPaymentBalance extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataIncomePerAgent($firstDateIncome){
		$baseQuery	=	"SELECT A.IDPRODUCTTYPE, B.PRODUCTTYPE, IF(A.IDVENDOR = 0, CONCAT('Driver ', E.DRIVERTYPE), D.NAME) AS PARTNERNAME,
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
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
}