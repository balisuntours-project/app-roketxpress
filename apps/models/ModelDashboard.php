<?php
class ModelDashboard extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function getDataTotalReservation($yearMonth, $lastYearMonth){
		
		$today		= date('Y-m-d');
		$tomorrow	= date('Y-m-d',strtotime("+1 days"));
		$query		= $this->db->query("SELECT COUNT(IDRESERVATION) AS TOTALRESERVATIONALLTIME,
												IFNULL(SUM(IF(LEFT(RESERVATIONDATESTART, 7) = '".$yearMonth."', 1, 0)), 0) AS TOTALRESERVATIONTHISMONTH, 
												IFNULL(SUM(IF(LEFT(RESERVATIONDATESTART, 7) = '".$lastYearMonth."', 1, 0)), 0) AS TOTALRESERVATIONLASTMONTH, 
												IFNULL(SUM(IF(RESERVATIONDATESTART = '".$today."', 1, 0)), 0) AS TOTALRESERVATIONTODAY, 
												IFNULL(SUM(IF(RESERVATIONDATESTART = '".$tomorrow."', 1, 0)), 0) AS TOTALRESERVATIONTOMORROW,
												MIN(RESERVATIONDATESTART) AS MINRESERVATIONDATE
										FROM t_reservation");
		$row		= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return array();
		
	}
	
	public function getDataSourceReservation($yearMonth, $totalMonth, $lastDateYearMonth){
		
		$query	= $this->db->query("SELECT CONCAT('".URL_SOURCE_LOGO."', B.LOGO) AS LOGOURL, B.SOURCENAME, 
											IFNULL(CEILING(COUNT(IDRESERVATION) / ".$totalMonth."), 0) AS AVERAGERESERVATIONPERMONTH,
											SUM(IF(LEFT(RESERVATIONDATESTART, 7) = '".$yearMonth."', 1, 0)) AS TOTALRESERVATIONOFMONTH
									FROM t_reservation A
									LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
									WHERE DATE(A.RESERVATIONDATESTART) <= '".$lastDateYearMonth."'
									GROUP BY A.IDSOURCE
									ORDER BY AVERAGERESERVATIONPERMONTH DESC, TOTALRESERVATIONOFMONTH DESC
									LIMIT 5");
		$result	= $query->result();

		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataProductType(){

		$query	= $this->db->query("SELECT 0 AS IDPRODUCTTYPE, 'Active Rsv' AS PRODUCTTYPE
									UNION ALL
									SELECT -1 AS IDPRODUCTTYPE, 'Cancel Rsv' AS PRODUCTTYPE
									UNION ALL
									SELECT IDPRODUCTTYPE, PRODUCTTYPE FROM m_producttype
									ORDER BY PRODUCTTYPE");
		$result	= $query->result();

		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataGraphProduct($yearMonth){

		$query	= $this->db->query("SELECT * FROM (
										SELECT SUM(IF(STATUS >= 0, 1, 0)) AS TOTALRESERVATION, RESERVATIONDATESTART, 0 AS IDPRODUCTTYPE
										FROM t_reservation
										WHERE LEFT(RESERVATIONDATESTART,7) = '".$yearMonth."'
										GROUP BY RESERVATIONDATESTART
										UNION ALL
										SELECT SUM(IF(STATUS = -1, 1, 0)) AS TOTALRESERVATION, RESERVATIONDATESTART, -1 AS IDPRODUCTTYPE
										FROM t_reservation
										WHERE LEFT(RESERVATIONDATESTART,7) = '".$yearMonth."'
										GROUP BY RESERVATIONDATESTART
										UNION ALL
										SELECT COUNT(A.IDRESERVATIONDETAILS) AS TOTALRESERVATION, B.RESERVATIONDATESTART, A.IDPRODUCTTYPE
										FROM t_reservationdetails A
										LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
										WHERE LEFT(B.RESERVATIONDATESTART,7) = '".$yearMonth."'
										GROUP BY A.IDPRODUCTTYPE, B.RESERVATIONDATESTART
									) AS A
									ORDER BY RESERVATIONDATESTART, IDPRODUCTTYPE");
		$result	= $query->result();

		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
}