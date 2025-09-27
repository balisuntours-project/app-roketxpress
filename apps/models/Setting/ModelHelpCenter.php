<?php
class ModelHelpCenter extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExists($idPartnerType, $categoryName, $idHelpCenterCategory = 0){
		
		$con_id		= isset($idHelpCenterCategory) && $idHelpCenterCategory != "" && $idHelpCenterCategory != 0 ? "A.IDHELPCENTERCATEGORY <> ".$idHelpCenterCategory : "1=1";
		$query		= $this->db->query("SELECT A.IDHELPCENTERCATEGORY AS idData, A.CATEGORYNAME, B.PARTNERTYPE
										FROM w_helpcentercategory A
										LEFT JOIN m_partnertype B ON A.IDPARTNERTYPE = B.IDPARTNERTYPE
										WHERE A.IDPARTNERTYPE = '".$idPartnerType."' AND A.CATEGORYNAME = '".$categoryName."' AND ".$con_id."
										LIMIT 1");
		$row		= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	public function getDataCategoryHelpCenter($idPartnerType, $searchKeyword){
		
		$con_keyword=	isset($searchKeyword) && $searchKeyword != "" ? "(A.CATEGORYNAME LIKE '%".$searchKeyword."%' OR A.DESCRIPTION LIKE '%".$searchKeyword."%')" : "1=1";
		$baseQuery	=	"SELECT A.IDHELPCENTERCATEGORY, A.IDPARTNERTYPE, A.ICON, A.CATEGORYNAME, A.DESCRIPTION, '' AS ARTICLELIST,
								COUNT(B.IDHELPCENTERARTICLE) AS TOTALARTICLE
						 FROM w_helpcentercategory A
						 LEFT JOIN w_helpcenterarticle B ON A.IDHELPCENTERCATEGORY = B.IDHELPCENTERCATEGORY
						 WHERE A.IDPARTNERTYPE = ".$idPartnerType." AND ".$con_keyword."
						 GROUP BY  A.IDHELPCENTERCATEGORY
						 ORDER BY A.CATEGORYNAME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
	
	}
	
	public function getArticleListByCategory($idHelpCenterCategory){
		
		$baseQuery		=	"SELECT IDHELPCENTERARTICLE, ARTICLETITLE, INPUTUSER,
									DATE_FORMAT(INPUTDATETIME, '%d %b %Y %H:%i') AS INPUTDATETIME,
									STATUSVIEW
							 FROM w_helpcenterarticle
							 WHERE IDHELPCENTERCATEGORY = '".$idHelpCenterCategory."'
							 ORDER BY ARTICLETITLE";
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
	
	}
		
	public function getDataHelpCenterCategoryById($idData){
		
		$baseQuery	=	"SELECT IDPARTNERTYPE, ICON, CATEGORYNAME, DESCRIPTION
						FROM w_helpcentercategory
						WHERE IDHELPCENTERCATEGORY = ".$idData."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)){
			return $row;
		}
		
		return array(
					"IDPARTNERTYPE"			=>	1,
					"ICON"					=>	"",
					"CATEGORYNAME"			=>	"",
					"DESCRIPTION"			=>	""
				);
		
	}
	
	public function checkDataExistsArticle($idHelpCenterCategory, $articleTitle, $idHelpCenterArticle = 0){
		
		$con_id		= isset($idHelpCenterArticle) && $idHelpCenterArticle != "" && $idHelpCenterArticle != 0 ? "A.IDHELPCENTERARTICLE <> ".$idHelpCenterArticle : "1=1";
		$query		= $this->db->query("SELECT A.IDHELPCENTERARTICLE AS idData, A.ARTICLETITLE, B.CATEGORYNAME
										FROM w_helpcenterarticle A
										LEFT JOIN w_helpcentercategory B ON A.IDHELPCENTERCATEGORY = B.IDHELPCENTERCATEGORY
										WHERE A.IDHELPCENTERCATEGORY = '".$idHelpCenterCategory."' AND A.ARTICLETITLE = '".$articleTitle."' AND ".$con_id."
										LIMIT 1");
		$row		= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	public function getDataHelpCenterArticleById($idData){
		
		$baseQuery	=	"SELECT A.IDHELPCENTERCATEGORY, C.PARTNERTYPE, B.CATEGORYNAME, A.ARTICLETITLE, A.ARTICLECONTENT, A.STATUSVIEW
						 FROM w_helpcenterarticle A
						 LEFT JOIN w_helpcentercategory B ON A.IDHELPCENTERCATEGORY = B.IDHELPCENTERCATEGORY
						 LEFT JOIN m_partnertype C ON B.IDPARTNERTYPE = C.IDPARTNERTYPE
						 WHERE A.IDHELPCENTERARTICLE = '".$idData."'
						 LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
}