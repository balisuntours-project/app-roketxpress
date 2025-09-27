<?php
class ModelMasterSource extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExists($sourceName, $idSource = 0){
		$idSource	=	$idSource == "" ? 0 : $idSource;
		$query		=	$this->db->query(
							"SELECT IDSOURCE AS idData, STATUS FROM m_source
							WHERE SOURCENAME = '".$sourceName."' AND IDSOURCE <> ".$idSource."
							LIMIT 1"
						);
		$row		=	$query->row_array();

		if(isset($row)) return $row;
		return false;
	}
		
	public function getDataSource($arrCondition, $page, $dataPerPage= 20){
		$ci			=& get_instance();
		$ci->load->model('MainOperation');

		$condition	=	"1=1 AND ";
		$startid	=	($page * 1 - 1) * $dataPerPage;
		foreach($arrCondition as $key=>$value){
			switch($key){
				case "keywordSearch":	$condition	.=	"(SOURCENAME LIKE '%".$value."%') AND ";
										break;
				default				:	$condition	.=	"1=1 AND ";
										break;
			}
		}
		
		$condition	=	substr($condition, 0, -4);
		$baseQuery	=	"SELECT IDSOURCE AS IDDATA, SOURCENAME, UPSELLINGTYPE, REVIEW5STARPOINT, CALCULATEBONUSREVIEW, DEFAULTCURRENCY, CONCAT('".URL_SOURCE_LOGO."', LOGO) AS IMAGELOGO
						FROM m_source
						WHERE STATUS = 1 AND ".$condition."
						ORDER BY SOURCENAME";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDDATA", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}
		
	public function getDataSourceById($idData){
		$baseQuery	=	"SELECT IDSOURCE AS IDDATA, SOURCENAME, REVIEW5STARPOINT, CALCULATEBONUSREVIEW, UPSELLINGTYPE, DEFAULTCURRENCY, LOGO, 
								CONCAT('".URL_SOURCE_LOGO."', LOGO) AS URLLOGO
						FROM m_source WHERE STATUS = 1 AND IDSOURCE = ".$idData."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row;
		return array("SOURCENAME"=>"", "IDDATA"=>0, "LOGO"=>"defaut.png", "URLLOGO"=>URL_SOURCE_LOGO."defaut.png");
	}
}