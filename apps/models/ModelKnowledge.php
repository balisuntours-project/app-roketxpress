<?php
class ModelKnowledge extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataKnowledge(){
		$baseQuery	=	"SELECT NAMEGROUP, NAMEDETAIL, DESCRIPTION, CONCAT('".URL_KNOWLEDGE_FILE."', FILEPDF) AS URLFILEPDF
						FROM m_knowledge
						ORDER BY NAMEGROUP, NAMEDETAIL";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}	
}