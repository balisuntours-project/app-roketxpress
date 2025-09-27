<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HelpCenter extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$this->postVar	=	decodeJsonPost();
		$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid submission data"));
		$this->newToken	=	isLoggedIn($this->token, true);
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataHelpCenterContentList(){

		$this->load->model('Setting/ModelHelpCenter');
		
		$idPartnerType	=	validatePostVar($this->postVar, 'idPartnerType', true);
		$searchKeyword	=	validatePostVar($this->postVar, 'searchKeyword', false);
		$dataTable		=	$this->ModelHelpCenter->getDataCategoryHelpCenter($idPartnerType, $searchKeyword);

		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		}
		
		foreach($dataTable as $data){
			$idHelpCenterCategory	=	$data->IDHELPCENTERCATEGORY;
			$articleList			=	$this->ModelHelpCenter->getArticleListByCategory($idHelpCenterCategory);
			$data->ARTICLELIST		=	$articleList;
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
	public function insertHelpCenterCategory(){
		
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Setting/ModelHelpCenter');
		
		$idPartnerType		=	validatePostVar($this->postVar, 'optionPartnerTypeEditor', true);
		$iconCode			=	validatePostVar($this->postVar, 'iconCode', true);
		$categoryName		=	validatePostVar($this->postVar, 'categoryName', true);
		$description		=	validatePostVar($this->postVar, 'description', false);
		$idInsert			=	0;
		
		$checkDataExists	=	$this->ModelHelpCenter->checkDataExists($idPartnerType, $categoryName);
		$arrInsertUpdate	=	array(
									"IDPARTNERTYPE"	=>	$idPartnerType,
									"ICON"			=>	$iconCode,
									"CATEGORYNAME"	=>	$categoryName,
									"DESCRIPTION"	=>	$description
								);
		
		if($checkDataExists){
			$idInsert		=	$checkDataExists['idData'];
			$updateResult	=	$this->MainOperation->updateData("w_helpcentercategory", $arrInsertUpdate, "IDHELPCENTERCATEGORY", $idInsert);
			$msg			=	"The data already exists. Saved data update";
		} else {
			$insertResult	=	$this->MainOperation->addData("w_helpcentercategory", $arrInsertUpdate);
			$newData		=	1;
			$msg			=	"New data saved";
		}
		
		if($idInsert == 0){
			if(!$insertResult['status']){
				switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
			}
			$idInsert		=	$insertResult['insertID'];		
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=> $msg, "idInsert"=>$idInsert));
	
	}
	
	public function getDetailHelpCenterCategory(){

		$this->load->model('Setting/ModelHelpCenter');
		$idData			=	validatePostVar($this->postVar, 'idHelpCenterCategory', true);
		$dataDetail		=	$this->ModelHelpCenter->getDataHelpCenterCategoryById($idData);
		
		if(!$dataDetail){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No detail found"));
		}

		setResponseOk(array("token"=>$this->newToken, "detailData"=>$dataDetail));
	
	}
	
	public function updateHelpCenterCategory(){

		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Setting/ModelHelpCenter');
		
		$idData			=	validatePostVar($this->postVar, 'idData', true);
		$idPartnerType	=	validatePostVar($this->postVar, 'optionPartnerTypeEditor', true);
		$iconCode		=	validatePostVar($this->postVar, 'iconCode', true);
		$categoryName	=	validatePostVar($this->postVar, 'categoryName', true);
		$description	=	validatePostVar($this->postVar, 'description', false);
		
		$checkDataExists=	$this->ModelHelpCenter->checkDataExists($idPartnerType, $categoryName, $idData);
		$arrUpdate		=	array(
									"IDPARTNERTYPE"	=>	$idPartnerType,
									"ICON"			=>	$iconCode,
									"CATEGORYNAME"	=>	$categoryName,
									"DESCRIPTION"	=>	$description
								);

		if($checkDataExists){
			$msg		=	"Category data with name : <b>".$checkDataExists['CATEGORYNAME']."</b> for partner type : <b>".$checkDataExists['PARTNERTYPE']."</b> already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}
		
		$updateResult	=	$this->MainOperation->updateData("w_helpcentercategory", $arrUpdate, "IDHELPCENTERCATEGORY", $idData);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Category data has been updated"));
	
	}
	
	private function checkInputData(){
		
		$arrVarValidate	=	array(
								array("optionPartnerTypeEditor", "option", "Partner Type"),
								array("iconCode", "text", "Icon Code"),
								array("categoryName", "text", "Category Name")
							);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		}
		
		return true;
		
	}
	
	public function deleteHelpCenterCategory(){

		$this->load->model('MainOperation');
		
		$idHelpCenterCategory	=	validatePostVar($this->postVar, 'idData', true);
		$arrWhere				=	array("IDHELPCENTERCATEGORY" => $idHelpCenterCategory);
		$deleteResult			=	$this->MainOperation->deleteData("w_helpcentercategory", $arrWhere);
		
		if(!$deleteResult['status']){
			switchMySQLErrorCode($deleteResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data help center category has been deleted"));
	
	}
	
	public function insertHelpCenterArticle(){
		
		$this->checkInputDataArticle();
		$this->load->model('MainOperation');
		$this->load->model('Setting/ModelHelpCenter');
		
		$idHelpCenterCategory	=	validatePostVar($this->postVar, 'idHelpCenterCategory', true);
		$articleTitle			=	validatePostVar($this->postVar, 'articleTitle', true);
		$articleContent			=	validatePostVar($this->postVar, 'articleContent', true);
		$dataUserAdmin			=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName			=	$dataUserAdmin['NAME'];
		$idInsert				=	0;
		
		$checkDataExists	=	$this->ModelHelpCenter->checkDataExistsArticle($idHelpCenterCategory, $articleTitle);
		$arrInsertUpdate	=	array(
									"IDHELPCENTERCATEGORY"	=>	$idHelpCenterCategory,
									"ARTICLETITLE"			=>	$articleTitle,
									"ARTICLECONTENT"		=>	$articleContent,
									"INPUTUSER"				=>	$userAdminName,
									"INPUTDATETIME"			=>	date('Y-m-d H:i:s')
								);
		
		if($checkDataExists){
			$idInsert		=	$checkDataExists['idData'];
			$updateResult	=	$this->MainOperation->updateData("w_helpcenterarticle", $arrInsertUpdate, "IDHELPCENTERARTICLE", $idInsert);
			$msg			=	"The article data already exists. Saved data update";
		} else {
			$insertResult	=	$this->MainOperation->addData("w_helpcenterarticle", $arrInsertUpdate);
			$newData		=	1;
			$msg			=	"New article data saved";
		}
		
		if($idInsert == 0){
			if(!$insertResult['status']){
				switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
			}
			$idInsert		=	$insertResult['insertID'];		
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=> $msg, "idInsert"=>$idInsert));
	
	}

	public function getDetailHelpCenterArticle(){

		$this->load->model('Setting/ModelHelpCenter');
		$idData			=	validatePostVar($this->postVar, 'idHelpCenterArticle', true);
		$dataDetail		=	$this->ModelHelpCenter->getDataHelpCenterArticleById($idData);
		
		if(!$dataDetail){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No detail found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$dataDetail));
	
	}

	public function updateHelpCenterArticle(){

		$this->checkInputDataArticle();
		$this->load->model('MainOperation');
		$this->load->model('Setting/ModelHelpCenter');
		
		$idData					=	validatePostVar($this->postVar, 'idHelpCenterArticle', true);
		$idHelpCenterCategory	=	validatePostVar($this->postVar, 'idHelpCenterCategory', true);
		$articleTitle			=	validatePostVar($this->postVar, 'articleTitle', true);
		$articleContent			=	validatePostVar($this->postVar, 'articleContent', true);
		$dataUserAdmin			=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$userAdminName			=	$dataUserAdmin['NAME'];
		
		$checkDataExists=	$this->ModelHelpCenter->checkDataExistsArticle($idHelpCenterCategory, $articleTitle, $idData);
		$arrUpdate		=	array(
									"ARTICLETITLE"			=>	$articleTitle,
									"ARTICLECONTENT"		=>	$articleContent,
									"INPUTUSER"				=>	$userAdminName,
									"INPUTDATETIME"			=>	date('Y-m-d H:i:s')
								);

		if($checkDataExists){
			$msg		=	"Article data with category : <b>".$checkDataExists['CATEGORYNAME']."</b> and title : <b>".$checkDataExists['ARTICLETITLE']."</b> already exists. Please enter different data";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}
		
		$updateResult	=	$this->MainOperation->updateData("w_helpcenterarticle", $arrUpdate, "IDHELPCENTERARTICLE", $idData);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Article data has been updated"));
	
	}
	
	public function deleteHelpCenterArticle(){

		$this->load->model('MainOperation');
		
		$idHelpCenterArticle	=	validatePostVar($this->postVar, 'idData', true);
		$arrWhere				=	array("IDHELPCENTERARTICLE" => $idHelpCenterArticle);
		$deleteResult			=	$this->MainOperation->deleteData("w_helpcenterarticle", $arrWhere);
		
		if(!$deleteResult['status']){
			switchMySQLErrorCode($deleteResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data help center article has been deleted"));
	
	}

	private function checkInputDataArticle(){
		
		$arrVarValidate	=	array(
								array("articleTitle", "text", "Article Title"),
								array("articleContent", "text", "Article Content")
							);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		}
		
		return true;
		
	}
	
}