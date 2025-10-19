<?php
class ModelAIAgentService extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function getDataEBookingEarnCoin($dateTimeMinus12Hours, $dateTimeMinus18Hours){
		$baseQuery	=	sprintf(
							"SELECT A.IDRESERVATION, A.BOOKINGCODE FROM t_reservation A
							LEFT JOIN t_ebookingcoin B ON A.IDRESERVATION = B.IDRESERVATION
							WHERE CONCAT(A.RESERVATIONDATESTART, ' ', A.RESERVATIONTIMESTART) BETWEEN '".$dateTimeMinus18Hours."' AND '".$dateTimeMinus12Hours."' AND
								  A.IDSOURCE = 4 AND (A.IDAREA = -1 OR A.STATUSDRIVER = 0) AND A.STATUS > 0 AND B.IDRESERVATION IS NULL
							ORDER BY A.RESERVATIONTIMEEND ASC"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getDataExecuteEBookingCoinEarned(){
		$baseQuery	=	sprintf(
							"SELECT A.IDEBOOKINGCOIN, B.BOOKINGCODE FROM t_ebookingcoin A
							LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
							WHERE A.STATUS IN (0,-1)
							ORDER BY A.DATETIMEINSERT ASC"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}

	public function isDriverHandleOwnCarByBookingCode($bookingCode){
		$baseQuery	=	sprintf(
							"SELECT D.IDCARVENDOR FROM t_reservation A
							LEFT JOIN t_reservationdetails B ON A.IDRESERVATION = B.IDRESERVATION AND B.IDDRIVERTYPE != 0
							LEFT JOIN t_scheduledriver C ON B.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
							LEFT JOIN t_carvendor D ON C.IDDRIVER = D.IDDRIVER
							WHERE A.BOOKINGCODE = '".$bookingCode."'
							LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return is_null($row['IDCARVENDOR']) || $row['IDCARVENDOR'] == 0 ? true : false;
		return true;
	}
}