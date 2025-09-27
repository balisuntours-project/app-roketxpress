<?php
class ModelSystemSetting extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function getDataSystemSetting(){
		
		$query	= $this->db->query("SELECT IDSYSTEMSETTINGVARIABLE, VARIABLEGROUP, VARIABLENAME, DESCRIPTION, VALUE
									FROM a_systemsettingvariable");
		$result	= $query->result();

		if (isset($result)){
			return $result;
		}
		
		return array();
		
	}
	
	public function getDataDriverScheduleMonitor(){
		
		$query	= $this->db->query("SELECT DATESCHEDULE
									FROM t_scheduledrivermonitor
									WHERE DATESCHEDULE >= '".date('Y-m-d')."'");
		$result	= $query->result();

		if (isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
}