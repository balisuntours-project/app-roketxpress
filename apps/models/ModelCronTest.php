<?php
class ModelCronTest extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function getDataReservationNoContact(){
		$baseQuery	=	"SELECT IDORDER, NAMATAMU, CONTACT, EMAIL
						FROM ".APP_OLD_DATABASE_NAME.".t_orderolah
						WHERE IDCONTACT = 0
						ORDER BY IDORDER
						LIMIT 1000";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if($result) return $result;
		return false;
	}
}