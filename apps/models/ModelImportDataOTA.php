<?php
class ModelImportDataOTA extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
		
	public function isReservationExist($bookingCode, $idSource){
		$query	=	$this->db->query(
						"SELECT IDRESERVATION, INCOMEAMOUNTCURRENCY, INCOMEAMOUNT, INCOMEEXCHANGECURRENCY, INCOMEAMOUNTIDR
						FROM t_reservation
						WHERE BOOKINGCODE = '".$bookingCode."' AND IDSOURCE = ".$idSource."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function getDetailReservationMailBox($bookingCode, $idSource){
		$query	=	$this->db->query(
						"SELECT IDMAILBOX, CUSTOMEREMAIL
						FROM t_mailbox
						WHERE BOOKINGCODE = '".$bookingCode."' AND IDSOURCE = ".$idSource."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
}