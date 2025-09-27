<?php
class ModelMasterProduct extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExists($productName, $idProduct = 0){
		
		$idProduct	=	$idProduct == "" ? 0 : $idProduct;
		$query		=	$this->db->query("SELECT IDPRODUCT AS idData, STATUS FROM m_product
										  WHERE PRODUCTNAME = '".$productName."' AND IDPRODUCT <> ".$idProduct."
										  LIMIT 1");
		$row		=	$query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
		
	public function getDataProductType(){
		
		$baseQuery	=	"SELECT IDPRODUCTTYPE, PRODUCTTYPE FROM m_producttype
						ORDER BY PRODUCTTYPE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
		
	public function getDataProduct($arrCondition, $page, $dataPerPage=	20){
		
		$ci			=& get_instance();
		$ci->load->model('MainOperation');

		$condition	=	"1=1 AND ";
		$startid	=	($page * 1 - 1) * $dataPerPage;
		foreach($arrCondition as $key=>$value){
			switch($key){
				case "keywordSearch":	$condition	.=	"(A.PRODUCTNAME LIKE '%".$value."%' OR A.DESCRIPTION LIKE '%".$value."%' OR C.PRODUCTTYPE LIKE '%".$value."%') AND ";
										break;
				default				:	$condition	.=	"1=1 AND ";
										break;
			}
		}
		
		$condition	=	substr($condition, 0, -4);
		$baseQuery	=	"SELECT A.IDPRODUCT AS IDDATA, A.PRODUCTNAME, GROUP_CONCAT(C.PRODUCTTYPE SEPARATOR '|') AS ARRPRODUCTTYPE,
								A.DURATIONHOUR, A.DESCRIPTION
						FROM m_product A
						LEFT JOIN m_productdetailtype B ON A.IDPRODUCT = B.IDPRODUCT
						LEFT JOIN m_producttype C ON B.IDPRODUCTTYPE = C.IDPRODUCTTYPE
						WHERE A.STATUS = 1 AND ".$condition."
						GROUP BY A.IDPRODUCT
						ORDER BY A.PRODUCTNAME";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDDATA", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}
		
	public function getDataProductById($idData){
		
		$baseQuery	=	"SELECT A.IDPRODUCT AS IDDATA, A.PRODUCTNAME, GROUP_CONCAT(B.IDPRODUCTTYPE SEPARATOR '|') AS ARRIDPRODUCTTYPE,
								A.DURATIONHOUR, A.DESCRIPTION
						FROM m_product A
						LEFT JOIN m_productdetailtype B ON A.IDPRODUCT = B.IDPRODUCT
						WHERE A.STATUS = 1 AND A.IDPRODUCT = ".$idData."
						GROUP BY A.IDPRODUCT
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)){
			return $row;
		}
		
		return array("IDDATA"=>0, "PRODUCTNAME"=>"", "DESCRIPTION"=> "");
		
	}
	
	public function getDataDetailProductTypeById($idProduct){
		
		$baseQuery	=	"SELECT IDPRODUCTDETAILTYPE, IDPRODUCTTYPE FROM m_productdetailtype
						WHERE IDPRODUCT = ".$idProduct;
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
}