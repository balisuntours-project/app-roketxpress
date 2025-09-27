<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserLevel extends CI_controller {
	
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
		$this->load->model('Setting/ModelUserLevel');

		$data	=	$this->ModelUserLevel->getDataUserLevel();
		$header	=	array(
			array(
				"name"		=>	"idUserLevel",
				"visible"	=>	false,
				"filterable"=>	false
			),
			array(
				"name"		=>	"notifMailValue",
				"visible"	=>	false,
				"filterable"=>	false
			),
			array(
				"name"		=>	"notifReservationValue",
				"visible"	=>	false,
				"filterable"=>	false
			),
			array(
				"name"		=>	"notifScheduleDriverValue",
				"visible"	=>	false,
				"filterable"=>	false
			),
			array(
				"name"		=>	"notifScheduleCarValue",
				"visible"	=>	false,
				"filterable"=>	false
			),
			array(
				"name"		=>	"notifAdditionalCostValue",
				"visible"	=>	false,
				"filterable"=>	false
			),
			array(
				"name"		=>	"notifAdditionalIncomeValue",
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
				"name"		=>	"notes",
				"title"		=>	"Notes",
				"style"		=>	array(
					"width"			=>	160,
					"maxWidth"		=>	160
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
				"name"		=>	"notifMailText",
				"title"		=>	"Mail",
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
				"name"		=>	"notifReservationText",
				"title"		=>	"Reservation",
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
			),
			array(
				"name"		=>	"notifScheduleDriverText",
				"title"		=>	"Driver Schedule",
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
			),
			array(
				"name"		=>	"notifScheduleCarText",
				"title"		=>	"Car Schedule",
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
			),
			array(
				"name"		=>	"notifAdditionalCostText",
				"title"		=>	"Additional Cost",
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
			),
			array(
				"name"		=>	"notifAdditionalIncomeText",
				"title"		=>	"Additional Cost",
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
			),
			array(
				"name"		=>	"notifFinanceText",
				"title"		=>	"Finance",
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
				"name"		=>	"PMSADDDRIVERSCHEDULE",
				"visible"	=>	false,
				"filterable"=>	false
			),
			array(
				"name"		=>	"PMSDELETEDRIVERSCHEDULE",
				"visible"	=>	false,
				"filterable"=>	false
			),
		);

		setResponseOk(array("token"=>$this->newToken, "header"=>$header, "data"=>$data));
	}
	
	public function insertDataUserLevel(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Setting/ModelUserLevel');
		
		$data		=	array();
		$idInsert	=	0;
		
		$levelName					=	$this->postVar['levelName'];
		$notes						=	$this->postVar['notes'];
		$notifMail					=	$this->postVar['notifMailValue'];
		$notifReservation			=	$this->postVar['notifReservationValue'];
		$notifScheduleDriver		=	$this->postVar['notifScheduleDriverValue'];
		$notifScheduleCar			=	$this->postVar['notifScheduleCarValue'];
		$notifAdditionalCost		=	$this->postVar['notifAdditionalCostValue'];
		$notifAdditionalIncome		=	$this->postVar['notifAdditionalIncomeValue'];
		$notifFinance				=	$this->postVar['notifFinanceValue'];
		$PMSADDDRIVERSCHEDULE		=	$this->postVar['PMSADDDRIVERSCHEDULE'];
		$PMSDELETEDRIVERSCHEDULE	=	$this->postVar['PMSDELETEDRIVERSCHEDULE'];
		$checkDataExists			=	$this->ModelUserLevel->checkDataExists($levelName, 0);
		$arrInsertUpdate			=	array(
			"LEVELNAME"					=>	$levelName,
			"NOTES"						=>	$notes,
			"NOTIFMAIL"					=>	$notifMail,
			"NOTIFRESERVATION"			=>	$notifReservation,
			"NOTIFSCHEDULEDRIVER"		=>	$notifScheduleDriver,
			"NOTIFSCHEDULEVENDOR"		=>	$notifScheduleCar,
			"NOTIFADDITIONALCOST"		=>	$notifAdditionalCost,
			"NOTIFADDITIONALINCOME"		=>	$notifAdditionalIncome,
			"NOTIFFINANCE"				=>	$notifFinance,
			"PMSADDDRIVERSCHEDULE"		=>	$PMSADDDRIVERSCHEDULE,
			"PMSDELETEDRIVERSCHEDULE"	=>	$PMSDELETEDRIVERSCHEDULE
		);
		
		if($checkDataExists){
			$idInsert		=	$checkDataExists['idData'];
			$updateResult	=	$this->MainOperation->updateData("m_userlevel", $arrInsertUpdate, "IDUSERLEVEL", $idInsert);
		} else {
			$insertResult	=	$this->MainOperation->addData("m_userlevel", $arrInsertUpdate);
		}
		
		if($idInsert == 0){
			if(!$insertResult['status']) switchMySQLErrorCode($insertResult['errCode'], $this->newToken);
			$idInsert		=	$insertResult['insertID'];		
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=> "New user level has been added", "idInsert"=>$idInsert));
	}
	
	public function updateDataUserLevel(){
		$this->checkInputData();
		$this->load->model('MainOperation');
		$this->load->model('Setting/ModelUserLevel');
		
		$idUserLevel			=	$this->postVar['idUserLevel'];
		$levelName				=	$this->postVar['levelName'];
		$notes					=	$this->postVar['notes'];
		$notifMail				=	$this->postVar['notifMailValue'];
		$notifReservation		=	$this->postVar['notifReservationValue'];
		$notifScheduleDriver	=	$this->postVar['notifScheduleDriverValue'];
		$notifScheduleCar		=	$this->postVar['notifScheduleCarValue'];
		$notifAdditionalCost	=	$this->postVar['notifAdditionalCostValue'];
		$notifAdditionalIncome	=	$this->postVar['notifAdditionalIncomeValue'];
		$notifFinance			=	$this->postVar['notifFinanceValue'];
		$PMSADDDRIVERSCHEDULE	=	$this->postVar['PMSADDDRIVERSCHEDULE'];
		$PMSDELETEDRIVERSCHEDULE=	$this->postVar['PMSDELETEDRIVERSCHEDULE'];
		$checkDataExists		=	$this->ModelUserLevel->checkDataExists($levelName, $idUserLevel);

		if($checkDataExists) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>"Failed to change. The data you entered already exists."));

		$arrUpdate			=	array(
			"LEVELNAME"					=>	$levelName,
			"NOTES"						=>	$notes,
			"NOTIFMAIL"					=>	$notifMail,
			"NOTIFRESERVATION"			=>	$notifReservation,
			"NOTIFSCHEDULEDRIVER"		=>	$notifScheduleDriver,
			"NOTIFSCHEDULEVENDOR"		=>	$notifScheduleCar,
			"NOTIFADDITIONALCOST"		=>	$notifAdditionalCost,
			"NOTIFADDITIONALINCOME"		=>	$notifAdditionalIncome,
			"NOTIFFINANCE"				=>	$notifFinance,
			"PMSADDDRIVERSCHEDULE"		=>	$PMSADDDRIVERSCHEDULE,
			"PMSDELETEDRIVERSCHEDULE"	=>	$PMSDELETEDRIVERSCHEDULE
		);
		$updateResult	=	$this->MainOperation->updateData("m_userlevel", $arrUpdate, "IDUSERLEVEL", $idUserLevel);
		
		if(!$updateResult['status']) switchMySQLErrorCode($updateResult['errCode'], $this->newToken);
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Data level has been updated"));
	}
	
	private function checkInputData(){
		$arrVarValidate	=	array(array("levelName","text","Level Name"));
		$errorValidate	=	validateVar($this->postVar, $arrVarValidate);
		
		if($errorValidate) setResponseBadRequest(array("token"=>$this->newToken, "msg"=>$errorValidate));
		return true;		
	}
	
}