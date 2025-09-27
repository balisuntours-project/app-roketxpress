<?php
class ModelUserAdmin extends CI_Model {
	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExists($username, $idUser = 0){
		$addCond=	$idUser == 0 ? "1=1" : "IDUSERADMIN != '".$idUser."'";
		$query	=	$this->db->query(
						"SELECT IDUSERADMIN AS idData FROM m_useradmin
						WHERE USERNAME = '".$username."' AND ".$addCond."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	
	public function checkLastPassword($idUser, $oldPassword){
		$query	=	$this->db->query(
						"SELECT IDUSERADMIN AS idData
						FROM m_useradmin
						WHERE IDUSERADMIN = '".$idUser."' AND PASSWORD = '".$oldPassword."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function getDataUserAdmin(){
		$query	=	$this->db->query(
						"SELECT A.IDUSERADMIN AS idUser, B.IDUSERLEVEL AS idUserLevel, A.IDRESERVATIONTYPE AS idReservationType, A.NAME AS nameUser, A.EMAIL AS email, B.LEVELNAME AS level,
								IFNULL(C.RESERVATIONTYPE, 'All Type') AS reservationType, A.USERNAME AS username, IF(A.STATUSPARTNERCONTACT = 1, 'Yes', 'No') AS partnerContact,
								PARTNERCONTACTNUMBER AS partnerContactNumber, STATUSPARTNERCONTACT AS statusPartnerContact
						FROM m_useradmin A
						LEFT JOIN m_userlevel B ON A.LEVEL = B.IDUSERLEVEL
						LEFT JOIN m_reservationtype C ON A.IDRESERVATIONTYPE = C.IDRESERVATIONTYPE
						WHERE A.STATUS = 1
						ORDER BY A.NAME"
					);
		$result	=	$query->result();

		if (isset($result)) return $result;
		return [];
	}
}