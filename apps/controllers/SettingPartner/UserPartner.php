<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserPartner extends CI_controller {
	
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
		
	public function getDataUserPartner(){
		
		$this->load->model('SettingPartner/ModelUserPartner');
		$data	=	$this->ModelUserPartner->getDataUserPartner();
		$header	=	array(
						array(
							"name"		=>	"idUserPartner",
							"visible"	=>	false,
							"filterable"=>	false
						),
						array(
							"name"		=>	"idUserLevel",
							"visible"	=>	false,
							"filterable"=>	false
						),
						array(
							"name"		=>	"idPartner",
							"visible"	=>	false,
							"filterable"=>	false
						),
						array(
							"name"		=>	"partnerName",
							"title"		=>	"Partner",
							"ellipsis"	=>	true,
							"style"		=>	array(
												"text-overflow"	=>	"ellipsis",
												"word-break"	=>	"keep-all",
												"white-space"	=>	"nowrap"
											)
						),
						array(
							"name"		=>	"nameUser",
							"title"		=>	"Name",
							"ellipsis"	=>	true,
							"style"		=>	array(
												"text-overflow"	=>	"ellipsis",
												"word-break"	=>	"keep-all",
												"white-space"	=>	"nowrap",
												"overflow"		=>	"hidden"
											)
						),
						array(
							"name"		=>	"email",
							"title"		=>	"Email",
							"ellipsis"	=>	true,
							"style"		=>	array(
												"text-overflow"	=>	"ellipsis",
												"word-break"	=>	"keep-all",
												"white-space"	=>	"nowrap",
												"overflow"		=>	"hidden"
											)
						),
						array(
							"name"		=>	"level",
							"title"		=>	"Level",
							"ellipsis"	=>	true,
							"style"		=>	array(
												"text-overflow"	=>	"ellipsis",
												"word-break"	=>	"keep-all",
												"white-space"	=>	"nowrap",
												"overflow"		=>	"hidden"
											)
						),
						array(
							"name"		=>	"username",
							"title"		=>	"Username",
							"ellipsis"	=>	true,
							"style"		=>	array(
												"text-overflow"	=>	"ellipsis",
												"word-break"	=>	"keep-all",
												"white-space"	=>	"nowrap",
												"overflow"		=>	"hidden"
											)
						)
					);

		setResponseOk(array("token"=>$this->newToken, "header"=>$header, "data"=>$data));

	}
	
	public function insertDataUserPartner(){
		
		$this->checkInputData("insert");
		$this->load->model('MainOperation');
		$this->load->model('SettingPartner/ModelUserPartner');
		
		$data		=	array();
		$idInsert	=	0;
		
		$nameUser			=	$this->postVar['nameUser'];
		$userEmail			=	$this->postVar['userEmail'];
		$levelUserPartner	=	$this->postVar['levelUserPartner'];
		$username			=	$this->postVar['username'];
		$newUserPassword	=	$this->postVar['newUserPassword'];
		$repeatUserPassword	=	$this->postVar['repeatUserPassword'];
		$idPartner			=	$this->postVar['idPartner'];
		$explodeIdPartner	=	explode('-', $idPartner);
		$idPartnerType		=	$explodeIdPartner[0];
		$idVendor			=	$idPartnerType == 1 ? (isset($explodeIdPartner[1]) ? $explodeIdPartner[1] : 0) : 0;
		$idDriver			=	$idPartnerType == 2 ? (isset($explodeIdPartner[1]) ? $explodeIdPartner[1] : 0) : 0;
		$passwordEncrypt	=	password_hash($newUserPassword, PASSWORD_DEFAULT);
		
		if($newUserPassword != $repeatUserPassword){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Invalid password repetition"));
		}
		
		$checkDataExists	=	$this->ModelUserPartner->checkDataExists($username);
		$arrInsertUpdate	=	array(
									"STATUS"			=>	1,
									"IDUSERLEVELPARTNER"=>	$levelUserPartner,
									"IDPARTNERTYPE"		=>	$idPartnerType,
									"IDVENDOR"			=>	$idVendor,
									"IDDRIVER"			=>	$idDriver,
									"NAME"				=>	$nameUser,
									"EMAIL"				=>	$userEmail,
									"USERNAME"			=>	$username,
									"PASSWORD"			=>	$passwordEncrypt,
									"HWID"				=>	"",
									"LASTACTIVITY"		=>	"0000-00-00 00:00:00"
								);
		$newData			=	0;
		
		if($checkDataExists){
			$idInsert		=	$checkDataExists['idData'];
			$updateResult	=	$this->MainOperation->updateData("m_userpartner", $arrInsertUpdate, "IDUSERPARTNER", $idInsert);
			$msg			=	"The data already exists. Saved data update";
		} else {
			$insertResult	=	$this->MainOperation->addData("m_userpartner", $arrInsertUpdate);
			$newData		=	1;
			$msg			=	"New data saved";
		}
		
		if($idInsert == 0){
			if(!$insertResult['status']){
				switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
			}
			$idInsert		=	$insertResult['insertID'];		
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=> $msg, "idInsert"=>$idInsert, "newData"=>$newData));
	
	}
	
	public function updateDataUserPartner(){

		$this->checkInputData("update");
		$this->load->model('MainOperation');
		$this->load->model('SettingPartner/ModelUserPartner');
		
		$idUserPartner		=	$this->postVar['idUserPartner'];
		$nameUser			=	$this->postVar['nameUser'];
		$userEmail			=	$this->postVar['userEmail'];
		$levelUserPartner	=	$this->postVar['levelUserPartner'];
		$username			=	$this->postVar['username'];
		$oldUserPassword	=	$this->postVar['oldUserPassword'];
		$newUserPassword	=	$this->postVar['newUserPassword'];
		$repeatUserPassword	=	$this->postVar['repeatUserPassword'];
		$idPartner			=	$this->postVar['idPartner'];
		$explodeIdPartner	=	explode('-', $idPartner);
		$idPartnerType		=	$explodeIdPartner[0];
		$idVendor			=	$idPartnerType == 1 ? (isset($explodeIdPartner[1]) ? $explodeIdPartner[1] : 0) : 0;
		$idDriver			=	$idPartnerType == 2 ? (isset($explodeIdPartner[1]) ? $explodeIdPartner[1] : 0) : 0;
		$passwordEncrypt	=	password_hash($newUserPassword, PASSWORD_DEFAULT);
		$oldPasswordEncrypt	=	password_hash($oldUserPassword, PASSWORD_DEFAULT);
		$checkDataExists	=	$this->ModelUserPartner->checkDataExists($username, $idUserPartner);

		if($checkDataExists){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Failed to change. The data you enter already exists. Solution: insert data with the same value"));
		}
		
		$arrUpdate		=	array("STATUS"				=>	1,
								  "IDUSERLEVELPARTNER"	=>	$levelUserPartner,
								  "IDPARTNERTYPE"		=>	$idPartnerType,
								  "IDVENDOR"			=>	$idVendor,
								  "IDDRIVER"			=>	$idDriver,
								  "NAME"				=>	$nameUser,
								  "EMAIL"				=>	$userEmail,
								  "USERNAME"			=>	$username,
								  "HWID"				=>	"",
								  "LASTACTIVITY"		=>	"0000-00-00 00:00:00"
								  );
		
		if($oldUserPassword != ""){
			$checkLastPassword	=	$this->ModelUserPartner->checkLastPassword($idUserPartner, $oldPasswordEncrypt);
			if(!$checkLastPassword){
				setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Failed to change. The old password you entered is invalid"));
			}
			if($newUserPassword != $repeatUserPassword){
				setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"New password repetition is invalid"));
			}
			$arrUpdate['PASSWORD']	=	$passwordEncrypt;
		}

		$updateResult	=	$this->MainOperation->updateData("m_userpartner", $arrUpdate, "IDUSERPARTNER", $idUserPartner);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Saved data update"));
	
	}
	
	private function checkInputData($action = "insert"){
		
		$arrVarValidate	=	array(
									array("nameUser","text","Name"),
									array("level","text","Level User"),
									array("username","text","Username")
								);
		
		if(isset($this->postVar['oldUserPassword']) && $this->postVar['oldUserPassword'] != "" && $action == "update"){
			$arrVarValidate[]	=	array("newUserPassword","text","New Password");
			$arrVarValidate[]	=	array("repeatUserPassword","text","New Password Repetition");
		}

		if($this->postVar['newUserPassword'] != ""){
			if($action == "update"){
				$arrVarValidate[]	=	array("oldUserPassword","text","Old Password");
			}
			$arrVarValidate[]		=	array("repeatUserPassword","text","New Password Repetition");
		}
		
		if($this->postVar['repeatUserPassword'] != ""){
			if($action == "update"){
				$arrVarValidate[]	=	array("oldUserPassword","text","Old Password");
			}
			$arrVarValidate[]		=	array("newUserPassword","text","New Password");
		}
		
		if($action == "insert"){
			$arrVarValidate[]		=	array("newUserPassword","text","New Password");
			$arrVarValidate[]		=	array("repeatUserPassword","text","New Password Repetition");
		}

		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		}
		
		return true;
		
	}
	
	public function deleteUserPartner(){

		$this->load->model('MainOperation');
		$data			=	array();
		
		$idUserPartner	=	validatePostVar($this->postVar, 'idData', true);
		$arrUpdate		=	array("STATUS" => -2);
		$updateResult	=	$this->MainOperation->updateData("m_userpartner", $arrUpdate, "IDUSERPARTNER", $idUserPartner);		
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"User data deleted"));
	
	}
	
}