<?php
class ModelUserPartnerLevelMenu extends CI_Model {	
	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
		
	public function getDataLevelMenu($idUserPartnerLevel){
		
		$query	= $this->db->query("SELECT A.GROUPNAME, A.DISPLAYNAME, A.IDMENUPARTNER, IFNULL(B.OPEN, 0) AS OPEN
									FROM m_menupartner A
									LEFT JOIN m_menulevelpartner B ON A.IDMENUPARTNER = B.IDMENUPARTNER AND B.IDUSERLEVELPARTNER = ".$idUserPartnerLevel."
									ORDER BY A.ORDERGROUP, A.ORDERMENU");
		$result	= $query->result();

		if (isset($result)){
			return $result;
		}
		
		return array();
		
	}
	
	public function checkMenuIsExists($idMenu, $userLevel){
		$query	= $this->db->query("SELECT IDMENULEVELPARTNER FROM m_menulevelpartner
									WHERE IDMENUPARTNER = '".$idMenu."' AND IDUSERLEVELPARTNER = ".$userLevel."
									LIMIT 1");
		$row	= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
	}	
}