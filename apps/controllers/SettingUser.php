<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SettingUser extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
		$this->postVar	=	decodeJsonPost();
		$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array());
		$this->newToken	=	isLoggedIn($this->token, true);
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
		
	public function detailSetting(){		
		$this->load->model('ModelSettingUser');
		$data	=	$this->ModelSettingUser->getDataSetting($this->newToken);

		setResponseOk(array("token"=>$this->newToken, "data"=>$data));
	}
	
	public function saveSetting(){
		
		$this->load->model('ModelSettingUser');
		$this->load->model('MainOperation');
		
		$name			=	validatePostVar($this->postVar, 'name', true);
		$email			=	validatePostVar($this->postVar, 'email', true);
		$username		=	validatePostVar($this->postVar, 'username', true);
		$oldPassword	=	validatePostVar($this->postVar, 'oldPassword', false);
		$newPassword	=	validatePostVar($this->postVar, 'newPassword', false);
		$repeatPassword	=	validatePostVar($this->postVar, 'repeatPassword', false);
		$arrUpdate		=	array();
		$urlLogout		=	'';
		
		if($oldPassword != "" || $newPassword != "" || $repeatPassword != ""){
			
			if($oldPassword == ""){
				setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please enter the old password (your active password)"));
			}

			if($newPassword == ""){
				setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please enter a new password"));
			}

			if($repeatPassword == ""){
				setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please enter a new password reset"));
			}
			
			if($newPassword != $repeatPassword){
				setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"The repetition of the new password does not match"));
			}
			
			$oldPasswordCheck	=	$this->ModelSettingUser->oldPasswordCheck($oldPassword, $this->newToken);
			
			if(!$oldPasswordCheck){
				setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"The old password you entered is incorrect"));
			}
			
			$arrUpdate['PASSWORD']	=	createMD5Encode($newPassword);
			$urlLogout				=	base_url()."/logout/".$this->newToken;
			
		}
		
		$idUserAdmin			=	$this->MainOperation->getIDUserAdmin($this->newToken);
		$arrUpdate["NAME"]		=	$name;
		$arrUpdate["EMAIL"]		=	$email;
		$arrUpdate["USERNAME"]	=	$username;
		$arrCondition			=	array("IDUSERADMIN" => $idUserAdmin);
		$execInsertUpdate		=	$this->MainOperation->updateData('m_useradmin', $arrUpdate, $arrCondition);

		if(!is_null($execInsertUpdate) && !$execInsertUpdate['status']){
			switchMySQLErrorCode($execInsertUpdate['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"User data has been updated", "urlLogout"=>$urlLogout, "name"=>$name, "email"=>$email));
		
	}
	
	public function detailUserProfileSetting(){		
		$this->load->model('ModelSettingUser');
		$detailProfile	=	$this->ModelSettingUser->getDataSetting($this->newToken);
		
		if(!$detailProfile) setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Your account details not found. Try again later"));
		
		$idUserAdmin		=	$detailProfile['IDUSERADMIN'];
		$dataMailTemplate	=	$this->ModelSettingUser->getDataMailTemplate($idUserAdmin);
		$initialName		=	preg_match_all('/\b\w/', $detailProfile['NAME'], $matches);
		$initialName		=	implode('', $matches[0]);
		
		$detailProfile['INITIALNAME']	=	strtoupper($initialName);
		unset($detailProfile['IDUSERADMIN']);
		setResponseOk(
			array(
				"token"				=>	$this->newToken,
				"detailProfile"		=>	$detailProfile,
				"dataMailTemplate"	=>	$dataMailTemplate
			)
		);
	}
	
	public function insertMailTemplate(){
		
		$this->checkInputDataMailTemplate();
		$this->load->model('MainOperation');
		$this->load->model('ModelSettingUser');
		
		$templateType		=	validatePostVar($this->postVar, 'templateType', false);
		$templateName		=	validatePostVar($this->postVar, 'templateName', true);
		$templateContent	=	validatePostVar($this->postVar, 'templateContent', true);
		$dataUserAdmin		=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$idUserAdmin		=	$dataUserAdmin['IDUSERADMIN'];
		$userAdminName		=	$dataUserAdmin['NAME'];
		$checkDataExists	=	$this->ModelSettingUser->checkDataExistsMailTemplate($idUserAdmin, $templateName);
		
		if($checkDataExists) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"The template name you entered already exists, please enter another name"));
		
		$idInsert				=	0;
		$checkSignatureExists	=	$templateType == 0 ? false : $this->ModelSettingUser->checkDataExistsMailSignature($idUserAdmin);
		$arrInsertUpdate		=	array(
									"IDUSERADMIN"		=>	$idUserAdmin,
									"LABEL"				=>	$templateName,
									"STATUSSIGNATURE"	=>	$templateType,
									"CONTENT"			=>	$templateContent
								);
		
		if($checkSignatureExists){
			$idInsert		=	$checkSignatureExists['idData'];
			$updateResult	=	$this->MainOperation->updateData("t_mailmessagetemplate", $arrInsertUpdate, "IDMAILMESSAGETEMPLATE", $idInsert);
			$msg			=	"The signature data already exists. Saved data update";
		} else {
			$insertResult	=	$this->MainOperation->addData("t_mailmessagetemplate", $arrInsertUpdate);
			$newData		=	1;
			$msg			=	"New template/signature data saved";
		}
		
		if($idInsert == 0){
			if(!$insertResult['status']){
				switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
			}
			$idInsert		=	$insertResult['insertID'];		
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=> $msg, "idInsert"=>$idInsert));
	
	}

	public function detailMailMessageTemplate(){

		$this->load->model('ModelSettingUser');
		$idMailMessageTemplate	=	validatePostVar($this->postVar, 'idMailMessageTemplate', true);
		$dataDetail				=	$this->ModelSettingUser->getDetailMailMessageTemplate($idMailMessageTemplate);
		
		if(!$dataDetail){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No detail found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$dataDetail));
	
	}

	public function updateMailTemplate(){

		$this->checkInputDataMailTemplate();
		$this->load->model('MainOperation');
		$this->load->model('ModelSettingUser');
		
		$idMailMessageTemplate	=	validatePostVar($this->postVar, 'idMailMessageTemplate', true);
		$templateType			=	validatePostVar($this->postVar, 'templateType', false);
		$templateName			=	validatePostVar($this->postVar, 'templateName', true);
		$templateContent		=	validatePostVar($this->postVar, 'templateContent', true);
		$dataUserAdmin			=	$this->MainOperation->getDataUserAdmin($this->newToken);
		$idUserAdmin			=	$dataUserAdmin['IDUSERADMIN'];
		$userAdminName			=	$dataUserAdmin['NAME'];
		
		$checkDataExists		=	$this->ModelSettingUser->checkDataExistsMailTemplate($idUserAdmin, $templateName, $idMailMessageTemplate);
		if($checkDataExists) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"The template name you entered already exists, please enter another name"));

		$checkSignatureExists	=	$templateType == 0 ? false : $this->ModelSettingUser->checkDataExistsMailSignature($idUserAdmin, $idMailMessageTemplate);
		if($checkSignatureExists) setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Your signature template already exists. Please edit the existing signature template"));

		$arrUpdate				=	array(
										"IDUSERADMIN"		=>	$idUserAdmin,
										"LABEL"				=>	$templateName,
										"STATUSSIGNATURE"	=>	$templateType,
										"CONTENT"			=>	$templateContent
									);
		$idData					=
		$updateResult			=	$this->MainOperation->updateData("t_mailmessagetemplate", $arrUpdate, "IDMAILMESSAGETEMPLATE", $idMailMessageTemplate);

		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Mail template data has been updated"));
	
	}
	
	public function deleteMailTemplate(){

		$this->load->model('MainOperation');
		
		$idMailMessageTemplate	=	validatePostVar($this->postVar, 'idData', true);
		$arrWhere				=	array("IDMAILMESSAGETEMPLATE" => $idMailMessageTemplate);
		$deleteResult			=	$this->MainOperation->deleteData("t_mailmessagetemplate", $arrWhere);
		
		if(!$deleteResult['status']){
			switchMySQLErrorCode($deleteResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Your template data has been deleted"));
	
	}

	private function checkInputDataMailTemplate(){
		
		$arrVarValidate	=	array(
								array("templateType", "option", "Template Type (Signature / Template)"),
								array("templateName", "text", "Template Name"),
								array("templateContent", "text", "Template Content")
							);
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		}
		
		return true;
		
	}
}