<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TemplateAutoCost extends CI_controller {
	
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
	
	public function getDataTemplateAutoCost(){

		$this->load->model('ProductSetting/ModelTemplateAutoCost');
		
		$keyword		=	validatePostVar($this->postVar, 'keyword', false);
		$dataResult		=	$this->ModelTemplateAutoCost->getDataTemplateAutoCost($keyword);

		if(!$dataResult){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataResult));
	
	}
	
	public function getDataProductTicketTransport(){

		$this->load->model('ProductSetting/ModelTemplateAutoCost');
		
		$arrExceptionTicket		=	validatePostVar($this->postVar, 'arrExceptionTicket', false);
		$arrExceptionTicket		=	is_array($arrExceptionTicket) ? "" : $arrExceptionTicket;
		$arrExceptionTransport	=	validatePostVar($this->postVar, 'arrExceptionTransport', false);
		$arrExceptionTransport	=	is_array($arrExceptionTransport) ? "" : $arrExceptionTransport;
		$productTicket			=	$this->ModelTemplateAutoCost->getProductTicket($arrExceptionTicket);
		$productTransport		=	$this->ModelTemplateAutoCost->getProductTransport($arrExceptionTransport);
		
		setResponseOk(
						array(
								"token"				=>	$this->newToken,
								"productTicket"		=>	$productTicket,
								"productTransport"	=>	$productTransport
						)
		);
	
	}
	
	public function insertTemplateAutoCostDetail(){
		
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelTemplateAutoCost');
		
		$idAutoDetailsTemplate	=	validatePostVar($this->postVar, 'idAutoDetailsTemplate', true);
		$idCostType				=	validatePostVar($this->postVar, 'idCostType', true);
		$idTicketProduct		=	validatePostVar($this->postVar, 'idTicketProduct', true);
		$idTranportProduct		=	validatePostVar($this->postVar, 'idTranportProduct', true);
		$checkDataExists		=	$this->ModelTemplateAutoCost->checkDataDetailCostExists($idAutoDetailsTemplate, $idCostType, $idTicketProduct, $idTranportProduct);

		if($checkDataExists){
			$msg		=	"The product you choose is already in the template. Please choose another product";
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}

		$idProductFee	=	isset($idCostType) && $idCostType == 1 ? $idTicketProduct : $idTranportProduct;
		$arrInsert		=	array(
								"IDAUTODETAILSTEMPLATE"	=>	$idAutoDetailsTemplate,
								"IDPRODUCTTYPE"			=>	$idCostType,
								"IDPRODUCTFEE"			=>	$idProductFee
							);
		$insertResult	=	$this->MainOperation->addData("t_autodetailstemplateitem", $arrInsert);

		if(!$insertResult['status']){
			switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
		}
		$idInsert	=	$insertResult['insertID'];		
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"The product has been saved in the template", "idInsert"=>$idInsert));
	
	}
	
	public function insertTemplateAutoCost(){
		
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelTemplateAutoCost');
		
		$templateAutoCostName		=	validatePostVar($this->postVar, 'templateAutoCostName', true);
		$arrTemplateKeyword			=	validatePostVar($this->postVar, 'arrTemplateKeyword', true);
		$arrTemplateKeyword			=	array_unique($arrTemplateKeyword);
		$arrTemplateProduct			=	validatePostVar($this->postVar, 'arrTemplateProduct', true);
		$arrTemplateProductInsert	=	array();
		$checkDataExistsName		=	$this->ModelTemplateAutoCost->checkDataTemplateNameExists($templateAutoCostName);
		
		if($checkDataExistsName){
			$msg		=	"This template cannot be saved because the template name already exists";			
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}
		
		foreach($arrTemplateProduct as $templateProduct){
			if(!in_array($templateProduct, $arrTemplateProductInsert)){
				$arrTemplateProductInsert[]	=	$templateProduct;
			}
		}
		
		foreach($arrTemplateKeyword as $keyword){
			$checkDataExistsKeyword	=	$this->ModelTemplateAutoCost->checkDataKeywordExists($keyword);
			if($checkDataExistsKeyword){
				$autoDetailsTemplateNameDB	=	$checkDataExistsKeyword['AUTODETAILSTEMPLATENAME'];
				$msg						=	"Keyword <b>".$keyword."</b> cannot be added to the new template because already exists in another template</b>";
				
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
			}
		}

		$arrInsertTemplate		=	array("AUTODETAILSTEMPLATENAME"	=>	$templateAutoCostName);
		$insertResultTemplate	=	$this->MainOperation->addData("t_autodetailstemplate", $arrInsertTemplate);

		if(!$insertResultTemplate['status']){
			switchMySQLErrorCode($insertResultTemplate['errCode'], $this->newToken);
		}
		$idTemplateAutoCost		=	$insertResultTemplate['insertID'];

		foreach($arrTemplateKeyword as $keyword){
			$arrInsertKeyword	=	array(
										"IDAUTODETAILSTEMPLATE"	=>	$idTemplateAutoCost,
										"TITLEKEYWORD"			=>	$keyword
									);
			$this->MainOperation->addData("t_autodetailstitlekeyword", $arrInsertKeyword);
		}

		foreach($arrTemplateProductInsert as $templateProduct){
			$idCostType			=	$templateProduct[0];
			$idProductFee		=	$templateProduct[1];
			$arrInsertProduct	=	array(
										"IDAUTODETAILSTEMPLATE"	=>	$idTemplateAutoCost,
										"IDPRODUCTTYPE"			=>	$idCostType,
										"IDPRODUCTFEE"			=>	$idProductFee
									);
			$this->MainOperation->addData("t_autodetailstemplateitem", $arrInsertProduct);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New template data has been saved"));
	
	}
	
	public function updateTemplateAutoCostName(){
		
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelTemplateAutoCost');
		
		$idAutoDetailsTemplate	=	validatePostVar($this->postVar, 'idAutoDetailsTemplate', true);
		$templateAutoCostName	=	validatePostVar($this->postVar, 'templateAutoCostName', true);
		$checkDataExists		=	$this->ModelTemplateAutoCost->checkDataTemplateNameExists($templateAutoCostName);

		if($checkDataExists){
			$msg		=	"This template cannot be saved because it already exists";			
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
		}

		$arrUpdate		=	array(
								"AUTODETAILSTEMPLATENAME"	=>	$templateAutoCostName
							);
		$updateResult	=	$this->MainOperation->updateData("t_autodetailstemplate", $arrUpdate, "IDAUTODETAILSTEMPLATE", $idAutoDetailsTemplate);

		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New template name has been saved"));
	
	}

	public function insertTemplateAutoCostKeyword(){
		
		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelTemplateAutoCost');
		
		$idAutoDetailsTemplate	=	validatePostVar($this->postVar, 'idAutoDetailsTemplate', true);
		$keyword				=	validatePostVar($this->postVar, 'keyword', true);
		$checkDataExists		=	$this->ModelTemplateAutoCost->checkDataKeywordExists($keyword);

		if($checkDataExists){
			
			$idAutoDetailsTemplateDB	=	$checkDataExists['IDAUTODETAILSTEMPLATE'];
			$autoDetailsTemplateNameDB	=	$checkDataExists['AUTODETAILSTEMPLATENAME'];
			
			if($idAutoDetailsTemplateDB == $idAutoDetailsTemplate){
				$msg		=	"This keyword cannot be added to the template because it already exists";
			} else {
				$msg		=	"This keyword cannot be added to the template because already exists in another template : <b>".$autoDetailsTemplateNameDB."</b>";
			}
			
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>$msg));
			
		}

		$arrInsertUpdate=	array(
								"IDAUTODETAILSTEMPLATE"	=>	$idAutoDetailsTemplate,
								"TITLEKEYWORD"			=>	$keyword,
						   );
		$insertResult	=	$this->MainOperation->addData("t_autodetailstitlekeyword", $arrInsertUpdate);

		if(!$insertResult['status']){
			switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
		}
		$idInsert	=	$insertResult['insertID'];		
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Keyword has been added to the template", "idInsert"=>$idInsert));
	
	}
	
	public function deleteTemplateAutoCostItem(){

		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelTemplateAutoCost');
		
		$idAutoDetailsTemplate		=	validatePostVar($this->postVar, 'idAutoDetailsTemplate', true);
		$idAutoDetailsItem			=	validatePostVar($this->postVar, 'idData', true);
		$totalItemTemplate			=	$this->ModelTemplateAutoCost->getTotalTemplateAutoCostItem($idAutoDetailsTemplate);
		
		if($totalItemTemplate <= 1){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Cannot delete data. There must be at least one (1) cost detail for a template"));
		}
		
		$arrWhere					=	array("IDAUTODETAILSTEMPLATEITEM" => $idAutoDetailsItem);
		$deleteResult				=	$this->MainOperation->deleteData("t_autodetailstemplateitem", $arrWhere);
		
		if(!$deleteResult['status']){
			switchMySQLErrorCode($deleteResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Cost detail data has been deleted"));
	
	}
	
	public function deleteTemplateAutoCostKeyword(){

		$this->load->model('MainOperation');
		$this->load->model('ProductSetting/ModelTemplateAutoCost');
		
		$idAutoDetailsTemplate		=	validatePostVar($this->postVar, 'idAutoDetailsTemplate', true);
		$idAutoDetailsTitleKeyword	=	validatePostVar($this->postVar, 'idData', true);
		$totalKeywordTemplate		=	$this->ModelTemplateAutoCost->getTotalKeywordTemplate($idAutoDetailsTemplate);
		
		if($totalKeywordTemplate <= 1){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Cannot delete data. There must be at least one (1) keyword for a template"));
		}
		
		$arrWhere					=	array("IDAUTODETAILSTITLEKEYWORD" => $idAutoDetailsTitleKeyword);
		$deleteResult				=	$this->MainOperation->deleteData("t_autodetailstitlekeyword", $arrWhere);
		
		if(!$deleteResult['status']){
			switchMySQLErrorCode($deleteResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Keyword data has been deleted"));
	
	}
	
	public function deleteTemplateAutoCost(){

		$this->load->model('MainOperation');
		$idAutoDetailsTemplate	=	validatePostVar($this->postVar, 'idData', true);
		$arrWhere				=	array("IDAUTODETAILSTEMPLATE" => $idAutoDetailsTemplate);
		$deleteResult			=	$this->MainOperation->deleteData("t_autodetailstemplate", $arrWhere);
		
		if(!$deleteResult['status']){
			switchMySQLErrorCode($deleteResult['errCode'], $this->newToken);
		}
		
		$this->MainOperation->deleteData("t_autodetailstitlekeyword", $arrWhere);
		$this->MainOperation->deleteData("t_autodetailstemplateitem", $arrWhere);
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Cost template data has been deleted"));
	
	}
	
}