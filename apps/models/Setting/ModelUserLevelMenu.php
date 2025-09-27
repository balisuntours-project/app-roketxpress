<?php
class ModelUserLevelMenu extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
		
	public function getDataLevelMenu($idUserLevel){
		
		$query	= $this->db->query("SELECT A.GROUPNAME, A.DISPLAYNAME, A.IDMENU, IFNULL(B.OPEN, 0) AS OPEN
									FROM m_menu A
									LEFT JOIN m_menulevel B ON A.IDMENU = B.IDMENU AND B.LEVEL = ".$idUserLevel."
									WHERE A.SUPERADMIN = 0
									ORDER BY A.ORDERGROUP, A.ORDERMENU");
		$result	= $query->result();

		if (isset($result)){
			return $result;
		}
		
		return array();
		
	}
	
	public function checkMenuIsExists($idMenu, $userLevel){
		
		$query	= $this->db->query("SELECT IDMENULEVEL FROM m_menulevel
									WHERE IDMENU = '".$idMenu."' AND LEVEL = ".$userLevel."
									LIMIT 1");
		$row	= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
}