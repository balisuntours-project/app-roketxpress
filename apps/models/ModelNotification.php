<?php
class ModelNotification extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataNotification($page, $dataPerPage= 100, $status, $idUserAdmin, $idMessageType, $keywordSearch){
		
		$ci			=& get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_idMessageType	=	isset($idMessageType) && $idMessageType != "" ? "A.IDMESSAGEADMINTYPE = ".$idMessageType : "1=1";
		$con_keywordSearch	=	isset($keywordSearch) && $keywordSearch != "" ? "(A.TITLE LIKE '%".$keywordSearch."%' OR A.MESSAGE LIKE '%".$keywordSearch."%')" : "1=1";
		$baseQuery			=	"SELECT A.IDMESSAGEADMIN, A.IDMESSAGEADMINTYPE, B.MESSAGEADMINTYPE, B.ICON, A.TITLE, A.MESSAGE, A.PARAMLIST,
										DATE_FORMAT(A.DATETIMEINSERT, '%d %b %Y %H:%i') AS DATETIMEINSERT
								FROM t_messageadmin A
								LEFT JOIN m_messageadmintype B ON A.IDMESSAGEADMINTYPE = B.IDMESSAGEADMINTYPE
								WHERE ".$con_idMessageType." AND ".$con_keywordSearch." AND A.IDUSERADMIN = ".$idUserAdmin." AND A.STATUS = ".$status."
								ORDER BY A.DATETIMEINSERT DESC";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDMESSAGEADMIN", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}
	
}