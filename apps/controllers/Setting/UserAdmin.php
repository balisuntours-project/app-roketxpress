<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserAdmin extends CI_controller {
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
		
	public function getDataUserAdmin(){
		$this->load->model('Setting/ModelUserAdmin');
		$data	=	$this->ModelUserAdmin->getDataUserAdmin();
		$header	=	array(
						array(
							"name"		=>	"idUser",
							"visible"	=>	false,
							"filterable"=>	false
						),
						array(
							"name"		=>	"idUserLevel",
							"visible"	=>	false,
							"filterable"=>	false
						),
						array(
							"name"		=>	"idReservationType",
							"visible"	=>	false,
							"filterable"=>	false
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
							"name"		=>	"reservationType",
							"title"		=>	"Preferred Reservation Type",
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
						),
						array(
							"name"		=>	"partnerContact",
							"title"		=>	"Partner Contact",
							"ellipsis"	=>	true,
							"style"		=>	array(
								"text-overflow"	=>	"ellipsis",
								"word-break"	=>	"keep-all",
								"white-space"	=>	"nowrap",
								"overflow"		=>	"hidden"
							)
						),
						array(
							"name"		=>	"statusPartnerContact",
							"visible"	=>	false,
							"filterable"=>	false
						),
						array(
							"name"		=>	"partnerContactNumber",
							"title"		=>	"Whatsapp Number",
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
	
	public function insertDataUserAdmin(){
		$this->checkInputData("insert");
		$this->load->model('MainOperation');
		$this->load->model('Setting/ModelUserAdmin');
		
		$data					=	array();
		$idInsert				=	0;
		$nameUser				=	$this->postVar['nameUser'];
		$userEmail				=	$this->postVar['userEmail'];
		$levelUserAdmin			=	$this->postVar['levelUserAdmin'];
		$idReservationType		=	$this->postVar['idReservationType'];
		$username				=	$this->postVar['username'];
		$statusPartnerContact	=	$this->postVar['statusPartnerContact'];
		$partnerContactNumber	=	$this->postVar['partnerContactNumber'];
		$partnerContactNumber	=	numberValidator($partnerContactNumber);
		$newUserPassword		=	$this->postVar['newUserPassword'];
		$repeatUserPassword		=	$this->postVar['repeatUserPassword'];
		$passwordEncrypt		=	createMD5Encode($newUserPassword);
		
		if($newUserPassword != $repeatUserPassword) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Invalid password repetition"));
		if($statusPartnerContact == 1 && ($partnerContactNumber == '' || strlen($partnerContactNumber) <= 7)) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please input a valid whatsapp number"));

		$checkDataExists	=	$this->ModelUserAdmin->checkDataExists($username);
		$arrInsertUpdate	=	array(
			"IDRESERVATIONTYPE"		=>	$idReservationType,
			"STATUS"				=>	1,
			"NAME"					=>	$nameUser,
			"EMAIL"					=>	$userEmail,
			"USERNAME"				=>	$username,
			"STATUSPARTNERCONTACT"	=>	$statusPartnerContact,
			"PARTNERCONTACTNUMBER"	=>	$partnerContactNumber,
			"PASSWORD"				=>	$passwordEncrypt,
			"LEVEL"					=>	$levelUserAdmin,
			"TOKEN1"				=>	"",
			"TOKEN2"				=>	"",
			"LASTACTIVITY"			=>	"0000-00-00 00:00:00"
		);
		$newData			=	0;
		
		if($checkDataExists){
			$idInsert		=	$checkDataExists['idData'];
			$updateResult	=	$this->MainOperation->updateData("m_useradmin", $arrInsertUpdate, "IDUSERADMIN", $idInsert);
			$msg			=	"The data already exists. Saved data update";
		} else {
			$insertResult	=	$this->MainOperation->addData("m_useradmin", $arrInsertUpdate);
			$newData		=	1;
			$msg			=	"New data saved";
		}
		
		if($idInsert == 0){
			if(!$insertResult['status']) switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
			$idInsert		=	$insertResult['insertID'];		
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=> $msg, "idInsert"=>$idInsert, "newData"=>$newData));
	}
	
	public function updateDataUserAdmin(){
		$this->checkInputData("update");
		$this->load->model('MainOperation');
		$this->load->model('Setting/ModelUserAdmin');
		
		$idUser					=	$this->postVar['idUser'];
		$nameUser				=	$this->postVar['nameUser'];
		$userEmail				=	$this->postVar['userEmail'];
		$levelUserAdmin			=	$this->postVar['levelUserAdmin'];
		$idReservationType		=	$this->postVar['idReservationType'];
		$username				=	$this->postVar['username'];
		$statusPartnerContact	=	$this->postVar['statusPartnerContact'];
		$partnerContactNumber	=	$this->postVar['partnerContactNumber'];
		$partnerContactNumber	=	numberValidator($partnerContactNumber);
		$oldUserPassword		=	$this->postVar['oldUserPassword'];
		$newUserPassword		=	$this->postVar['newUserPassword'];
		$repeatUserPassword		=	$this->postVar['repeatUserPassword'];
		$passwordEncrypt		=	createMD5Encode($newUserPassword);
		$oldPasswordEncrypt		=	createMD5Encode($oldUserPassword);
		$checkDataExists		=	$this->ModelUserAdmin->checkDataExists($username, $idUser);

		if($checkDataExists) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Failed to change. The data you enter already exists. Solution: insert data with the same value"));
		if($statusPartnerContact == 1 && ($partnerContactNumber == '' || strlen($partnerContactNumber) <= 7)) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Please input a valid whatsapp number"));

		$arrUpdate	=	array(
			"IDRESERVATIONTYPE"		=>	$idReservationType,
			"STATUS"				=>	1,
			"NAME"					=>	$nameUser,
			"EMAIL"					=>	$userEmail,
			"USERNAME"				=>	$username,
			"STATUSPARTNERCONTACT"	=>	$statusPartnerContact,
			"PARTNERCONTACTNUMBER"	=>	$partnerContactNumber,
			"LEVEL"					=>	$levelUserAdmin,
			"TOKEN1"				=>	"",
			"TOKEN2"				=>	"",
			"LASTACTIVITY"			=>	"0000-00-00 00:00:00"
		);
		
		if($oldUserPassword != ""){
			$checkLastPassword	=	$this->ModelUserAdmin->checkLastPassword($idUser, $oldPasswordEncrypt);
			if(!$checkLastPassword) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Failed to change. The old password you entered is invalid"));
			if($newUserPassword != $repeatUserPassword) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"New password repetition is invalid"));
			$arrUpdate['PASSWORD']	=	$passwordEncrypt;
		}

		$updateResult	=	$this->MainOperation->updateData("m_useradmin", $arrUpdate, "IDUSERADMIN", $idUser);
		
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
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
			if($action == "update") $arrVarValidate[]	=	array("oldUserPassword","text","Old Password");
			$arrVarValidate[]	=	array("repeatUserPassword","text","New Password Repetition");
		}
		
		if($this->postVar['repeatUserPassword'] != ""){
			if($action == "update") $arrVarValidate[]	=	array("oldUserPassword","text","Old Password");
			$arrVarValidate[]	=	array("newUserPassword","text","New Password");
		}
		
		if($action == "insert"){
			$arrVarValidate[]	=	array("newUserPassword","text","New Password");
			$arrVarValidate[]	=	array("repeatUserPassword","text","New Password Repetition");
		}

		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;		
	}
	
	public function deleteUserAdmin(){
		$this->load->model('MainOperation');

		$data			=	array();
		$idUser			=	validatePostVar($this->postVar, 'idData', true);
		$arrUpdate		=	array("STATUS" => -2);
		$updateResult	=	$this->MainOperation->updateData("m_useradmin", $arrUpdate, "IDUSERADMIN", $idUser);		
		
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"User data deleted"));	
	}
}