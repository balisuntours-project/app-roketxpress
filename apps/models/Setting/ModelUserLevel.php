<?php
class ModelUserLevel extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExists($levelName, $idUserLevel){
		$addCond=	$idUserLevel == 0 ? "1=1" : "IDUSERLEVEL != '".$idUserLevel."'";
		$query	=	$this->db->query("
						SELECT IDUSERLEVEL AS idData FROM m_userlevel
						WHERE LEVELNAME = '".$levelName."' AND ".$addCond." LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function getDataUserLevel(){
		$query	=	$this->db->query(
						"SELECT IDUSERLEVEL AS idUserLevel, LEVELNAME AS levelName, NOTES AS notes,
								NOTIFMAIL AS notifMailValue, IF(NOTIFMAIL = 1, 'Yes', 'No') AS notifMailText,
								NOTIFRESERVATION AS notifReservationValue, IF(NOTIFRESERVATION = 1, 'Yes', 'No') AS notifReservationText,
								NOTIFSCHEDULEDRIVER AS notifScheduleDriverValue, IF(NOTIFSCHEDULEDRIVER = 1, 'Yes', 'No') AS notifScheduleDriverText,
								NOTIFSCHEDULEVENDOR AS notifScheduleCarValue, IF(NOTIFSCHEDULEVENDOR = 1, 'Yes', 'No') AS notifScheduleCarText,
								NOTIFADDITIONALCOST AS notifAdditionalCostValue, IF(NOTIFADDITIONALCOST = 1, 'Yes', 'No') AS notifAdditionalCostText,
								NOTIFADDITIONALINCOME AS notifAdditionalIncomeValue, IF(NOTIFADDITIONALINCOME = 1, 'Yes', 'No') AS notifAdditionalIncomeText,
								NOTIFFINANCE AS notifFinanceValue, IF(NOTIFFINANCE = 1, 'Yes', 'No') AS notifFinanceText, PMSADDDRIVERSCHEDULE,
								PMSDELETEDRIVERSCHEDULE
						FROM m_userlevel
						ORDER BY LEVELNAME"
					);
		$result	=	$query->result();

		if (isset($result)) return $result;
		return array();		
	}
	
}