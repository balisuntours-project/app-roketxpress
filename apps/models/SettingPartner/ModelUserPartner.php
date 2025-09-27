<?php
class ModelUserPartner extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExists($username, $idUser = 0){
		
		$addCond= $idUser == 0 ? "1=1" : "IDUSERPARTNER != '".$idUser."'";
		$query	= $this->db->query("SELECT IDUSERPARTNER AS idData
									FROM m_userpartner
									WHERE USERNAME = '".$username."' AND ".$addCond."
									LIMIT 1");
		$row	= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	
	public function checkLastPassword($idUser, $oldPassword){
		
		$query	= $this->db->query("SELECT IDUSERPARTNER AS idData
									FROM m_userpartner
									WHERE IDUSERPARTNER = '".$idUser."' AND PASSWORD = '".$oldPassword."'
									LIMIT 1");
		$row	= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	public function getDataUserPartner(){
		
		$query	= $this->db->query("SELECT A.IDUSERPARTNER AS idUserPartner, B.IDUSERLEVELPARTNER AS idUserLevel, CONCAT(A.IDPARTNERTYPE, '-', IF(A.IDPARTNERTYPE = 1, A.IDVENDOR, A.IDDRIVER)) AS idPartner,
										   IF(A.IDPARTNERTYPE = 1, CONCAT('[Vendor] ', C.NAME), CONCAT('[Driver] ', D.NAME)) AS partnerName, A.NAME AS nameUser, A.EMAIL AS email, B.LEVELNAME AS level,
										   A.USERNAME AS username
									FROM m_userpartner A
									LEFT JOIN m_userlevelpartner B ON A.IDUSERLEVELPARTNER = B.IDUSERLEVELPARTNER
									LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
									LEFT JOIN m_driver D ON A.IDDRIVER = D.IDDRIVER
									WHERE A.STATUS = 1
									ORDER BY A.IDPARTNERTYPE, partnerName, A.NAME");
		$result	= $query->result();

		if (isset($result)){
			return $result;
		}
		
		return array();
		
	}
	
}