<?php
class ModelSettingUser extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExistsMailTemplate($idUserAdmin, $templateName, $idMailMessageTemplate = false){
		
		$con_idData	=	!isset($idMailMessageTemplate) || $idMailMessageTemplate == "" ? "1=1" : "IDMAILMESSAGETEMPLATE != ".$idMailMessageTemplate;
		$query		=	$this->db->query("SELECT IDMAILMESSAGETEMPLATE FROM t_mailmessagetemplate
										WHERE IDUSERADMIN = '".$idUserAdmin."' AND LABEL = '".$templateName."' AND ".$con_idData."
										LIMIT 1");
		$row		=	$query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	public function checkDataExistsMailSignature($idUserAdmin, $idMailMessageTemplate = false){
		
		$con_idData	=	!isset($idMailMessageTemplate) || $idMailMessageTemplate == "" ? "1=1" : "IDMAILMESSAGETEMPLATE != ".$idMailMessageTemplate;
		$query		=	$this->db->query("SELECT IDMAILMESSAGETEMPLATE FROM t_mailmessagetemplate
										WHERE IDUSERADMIN = '".$idUserAdmin."' AND ".$con_idData." AND STATUSSIGNATURE = 1
										LIMIT 1");
		$row		=	$query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
		
	public function getDataSetting($token){
		
		$baseQuery	=	"SELECT A.IDUSERADMIN, '' AS INITIALNAME, A.NAME, B.LEVELNAME, A.EMAIL, A.USERNAME, A.PASSWORD
						FROM m_useradmin A
						LEFT JOIN m_userlevel B ON A.LEVEL = B.IDUSERLEVEL
						WHERE A.TOKEN1 = '".$token."' OR A.TOKEN2 = '".$token."'
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)) return false;
				
		return $row;
		
	}
		
	public function getDataMailTemplate($idUserAdmin){
		
		$baseQuery	=	"SELECT IDMAILMESSAGETEMPLATE, LABEL, STATUSSIGNATURE, CONTENT FROM t_mailmessagetemplate
						WHERE IDUSERADMIN = ".$idUserAdmin."
						ORDER BY STATUSSIGNATURE, LABEL";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function oldPasswordCheck($password, $token){

		$baseQuery	=	"SELECT IDUSERADMIN FROM m_useradmin
						WHERE (TOKEN1 = '".$token."' OR TOKEN2 = '".$token."') AND PASSWORD = MD5('".$password."')
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)){
			return true;
		}
				
		return false;
		
	}
	
	public function getDetailMailMessageTemplate($idMailMessageTemplate){
		
		$baseQuery	=	"SELECT LABEL, STATUSSIGNATURE, CONTENT
						FROM t_mailmessagetemplate
						WHERE IDMAILMESSAGETEMPLATE = '".$idMailMessageTemplate."'
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)) return false;
				
		return $row;
		
	}
		
}