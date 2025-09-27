<?php
class ModelPartnerUserLevel extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExists($levelName, $idUserLevel){
		
		$addCond= $idUserLevel == 0 ? "1=1" : "IDUSERLEVELPARTNER != '".$idUserLevel."'";
		$query	= $this->db->query("SELECT IDUSERLEVELPARTNER AS idData FROM m_userlevelpartner
									WHERE LEVELNAME = '".$levelName."' AND ".$addCond." LIMIT 1");
		$row	= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	public function getDataUserLevel(){
		
		$query	= $this->db->query("SELECT IDUSERLEVELPARTNER AS idUserLevelPartner, LEVELNAME AS levelName,
											NOTIFSCHEDULE AS notifScheduleValue, IF(NOTIFSCHEDULE = 1, 'Yes', 'No') AS notifScheduleText,
											NOTIFFINANCE AS notifFinanceValue, IF(NOTIFFINANCE = 1, 'Yes', 'No') AS notifFinanceText
									FROM m_userlevelpartner
									ORDER BY LEVELNAME");
		$result	= $query->result();

		if (isset($result)){
			return $result;
		}
		
		return array();
		
	}
	
}