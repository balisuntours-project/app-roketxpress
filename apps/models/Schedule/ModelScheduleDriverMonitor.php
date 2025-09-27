<?php
class ModelScheduleDriverMonitor extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataScheduleDriverMonitor($monthYear){
		
		$baseQuery	=	"SELECT IDSCHEDULEDRIVERMONITOR, DATESCHEDULE, DATE_FORMAT(DATESCHEDULE, '%d %b %Y') AS DATESCHEDULESTR,
								TOTALSCHEDULE, TOTALACTIVEDRIVER, TOTALOFFDRIVER, TOTALDAYOFFQUOTA, STATUS
						FROM t_scheduledrivermonitor
						WHERE LEFT(DATESCHEDULE, 7) = '".$monthYear."'
						ORDER BY DATESCHEDULE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
}