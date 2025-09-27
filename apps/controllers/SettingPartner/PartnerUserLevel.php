<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PartnerUserLevel extends CI_controller {
	
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
		
	public function getDataUserLevel(){

		$this->load->model('SettingPartner/ModelPartnerUserLevel');
		$data	=	$this->ModelPartnerUserLevel->getDataUserLevel();
		$header	=	array(
						array(
							"name"		=>	"idUserLevelPartner",
							"visible"	=>	false,
							"filterable"=>	false
						),
						array(
							"name"		=>	"notifScheduleValue",
							"visible"	=>	false,
							"filterable"=>	false
						),
						array(
							"name"		=>	"notifFinanceValue",
							"visible"	=>	false,
							"filterable"=>	false
						),
						array(
							"name"		=>	"levelName",
							"title"		=>	"Level Name",
							"ellipsis"	=>	true,
							"style"		=>	array(
												"text-overflow"	=>	"ellipsis",
												"word-break"	=>	"keep-all",
												"white-space"	=>	"nowrap",
												"overflow"		=>	"hidden"
											)
						),
						array(
							"name"		=>	"notifScheduleText",
							"title"		=>	"Schedule",
							"style"		=>	array(
												"width"			=>	40,
												"maxWidth"		=>	40
											),
							"ellipsis"	=>	true,
							"style"		=>	array(
												"text-overflow"	=>	"ellipsis",
												"word-break"	=>	"keep-all",
												"white-space"	=>	"nowrap",
												"overflow"		=>	"hidden"
											)
						),
						array(
							"name"		=>	"notifFinanceText",
							"title"		=>	"Finance",
							"style"		=>	array(
												"width"			=>	60,
												"maxWidth"		=>	60
											),
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
	
	public function insertDataUserLevel(){
		
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('SettingPartner/ModelPartnerUserLevel');
		
		$data			=	array();
		$idInsert		=	0;
		$levelName		=	$this->postVar['levelName'];
		$notifSchedule	=	$this->postVar['notifScheduleValue'];
		$notifFinance	=	$this->postVar['notifFinanceValue'];
		$checkDataExists=	$this->ModelPartnerUserLevel->checkDataExists($levelName, 0);
		$arrInsertUpdate=	array(
								"LEVELNAME"		=>	$levelName,
								"NOTIFSCHEDULE"	=>	$notifSchedule,
								"NOTIFFINANCE"	=>	$notifFinance
							);
		
		if($checkDataExists){
			$idInsert		=	$checkDataExists['idData'];
			$updateResult	=	$this->MainOperation->updateData("m_userlevelpartner", $arrInsertUpdate, "IDUSERLEVELPARTNER", $idInsert);
		} else {
			$insertResult	=	$this->MainOperation->addData("m_userlevelpartner", $arrInsertUpdate);
		}
		
		if($idInsert == 0){
			if(!$insertResult['status']){
				switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
			}
			$idInsert		=	$insertResult['insertID'];		
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=> "New partner user level has been added", "idInsert"=>$idInsert));
	
	}
	
	public function updateDataUserLevel(){

		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('SettingPartner/ModelPartnerUserLevel');
		
		$idUserLevelPartner	=	$this->postVar['idUserLevelPartner'];
		$levelName			=	$this->postVar['levelName'];
		$notifSchedule		=	$this->postVar['notifScheduleValue'];
		$notifFinance		=	$this->postVar['notifFinanceValue'];
		$checkDataExists	=	$this->ModelPartnerUserLevel->checkDataExists($levelName, $idUserLevelPartner);

		if($checkDataExists){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Failed to change. The data you entered already exists."));
		}

		$arrUpdate		=	array(
								"LEVELNAME"		=>	$levelName,
								"NOTIFSCHEDULE"	=>	$notifSchedule,
								"NOTIFFINANCE"	=>	$notifFinance
							);
		$updateResult	=	$this->MainOperation->updateData("m_userlevelpartner", $arrUpdate, "IDUSERLEVELPARTNER", $idUserLevelPartner);
		
		if(!$updateResult['status']){
			switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data level has been updated"));
	
	}
	
	private function checkInputData(){
		
		$arrVarValidate	=	array(array("levelName","text","Level Name"));
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate){
			setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		}
		
		return true;
		
	}
	
}