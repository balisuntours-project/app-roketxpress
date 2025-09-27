<?php
class ModelMasterCarType extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExists($carTypeName, $idCarType = 0){
		
		$idCarType	= $idCarType == "" ? 0 : $idCarType;
		$query		= $this->db->query("SELECT IDCARTYPE AS idData, CARTYPE FROM m_cartype
										WHERE CARTYPE = '".$carTypeName."' AND IDCARTYPE <> ".$idCarType."
										LIMIT 1");
		$row		= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
		
	public function getDataCarType($arrCondition, $page, $dataPerPage= 20){
		
		$ci			=&	get_instance();
		$ci->load->model('MainOperation');

		$condition	=	"1=1 AND ";
		$startid	=	($page * 1 - 1) * $dataPerPage;
		foreach($arrCondition as $key=>$value){
			switch($key){
				case "keywordSearch":	$condition	.=	"(CARTYPE LIKE '%".$value."%' OR DESCRIPTION LIKE '%".$value."%') AND ";
										break;
				default				:	$condition	.=	"1=1 AND ";
										break;
			}
		}
		
		$condition	=	substr($condition, 0, -4);
		$baseQuery	=	"SELECT IDCARTYPE AS IDDATA, CARTYPE, DESCRIPTION
						FROM m_cartype
						WHERE ".$condition."
						ORDER BY 	CARTYPE";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDDATA", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}
		
	public function getDataCarTypeById($idData){
		
		$baseQuery	=	"SELECT IDCARTYPE AS IDDATA, CARTYPE, DESCRIPTION
						FROM m_cartype WHERE IDCARTYPE = ".$idData."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)){
			return $row;
		}
		
		return array("CARTYPE"=>"", "IDDATA"=>0, "DESCRIPTION"=>"");
		
	}
	
}