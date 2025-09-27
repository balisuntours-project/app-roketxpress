<?php
class ModelRecapPerDate extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataRecapPerDate($page, $dataPerPage= 25, $monthYear){
		
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$startid	=	($page * 1 - 1) * $dataPerPage;
		$baseQuery	=	"SELECT DATE_FORMAT(A.RESERVATIONDATESTART, '%d %b %Y') AS DATERESERVATION, COUNT(DISTINCT(A.IDRESERVATION)) AS TOTALRESERVATION,
								COUNT(DISTINCT(IF(A.STATUS != -1, A.IDRESERVATION, NULL))) AS TOTALACTIVERESERVATION,
								COUNT(DISTINCT(IF(A.STATUS = -1, A.IDRESERVATION, NULL))) AS TOTALCANCELRESERVATION,
								COUNT(DISTINCT(IF(B.IDDRIVERTYPE != 0, A.IDRESERVATION, NULL))) AS TOTALHANDLEBYDRIVER,
								COUNT(DISTINCT(IF(B.IDVENDOR != 0, A.IDRESERVATION, NULL))) AS TOTALHANDLEBYVENDOR
						FROM t_reservation A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATION = B.IDRESERVATION
						WHERE LEFT(A.RESERVATIONDATESTART, 7) = '".$monthYear."'
						GROUP BY A.RESERVATIONDATESTART
						ORDER BY A.RESERVATIONDATESTART";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "DATERESERVATION", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}
	
}