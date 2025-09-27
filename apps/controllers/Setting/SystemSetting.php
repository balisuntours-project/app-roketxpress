<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SystemSetting extends CI_controller {
	
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
		
	public function getDataSystemSetting(){
		
		$this->load->model('Setting/ModelSystemSetting');
		$dataSetting	=	$this->ModelSystemSetting->getDataSystemSetting();
		
		if(!$dataSetting){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data setting found"));
		}

		setResponseOk(array("token"=>$this->newToken, "dataSetting"=>$dataSetting));

	}
		
	public function saveDataSystemSetting(){
		
		$this->load->model('MainOperation');
		$arrSystemSettingVar	=	validatePostVar($this->postVar, 'arrSystemSettingVar', true);
		
		if(!is_array($arrSystemSettingVar) || count($arrSystemSettingVar) <= 0){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid submission data. Please clear the app data and reopen the menu"));
		}
		
		$totalUpdateData		=	0;
		$msg					=	"Process completed. ";
		
		foreach($arrSystemSettingVar as $systemSettingVar){
			
			$idSystemSettingVariable	=	$systemSettingVar[0];
			$valueSystemSettingVariable	=	str_replace(",", "", $systemSettingVar[1]);
			$arrUpdateSystemSetting		=	array(
												"VALUE"	=>	$valueSystemSettingVariable
											);
			$procUpdateSystemSetting	=	$this->MainOperation->updateData("a_systemsettingvariable", $arrUpdateSystemSetting, "IDSYSTEMSETTINGVARIABLE", $idSystemSettingVariable);
			if(isset($procUpdateSystemSetting['status']) && $procUpdateSystemSetting['status']){
				$totalUpdateData++;
			}
			
			if($idSystemSettingVariable == 10){
				$this->updateDriverScheduleMonitor($valueSystemSettingVariable);
			}
			
		}
		
		if($totalUpdateData > 0){
			$msg	.=	$totalUpdateData." system setting data has been saved";
		} else {
			$msg	.=	"No data change";
		}

		setResponseOk(array("token"=>$this->newToken, "msg"=>$msg));

	}

	private function updateDriverScheduleMonitor($defaultOffQuotaUpdate){
		
		$this->load->model('MainOperation');
		$this->load->model('Setting/ModelSystemSetting');

		$dataDriverScheduleMonitor	=	$this->ModelSystemSetting->getDataDriverScheduleMonitor();
		if($dataDriverScheduleMonitor){
			foreach($dataDriverScheduleMonitor as $keyDriverScheduleMonitor){
				$this->MainOperation->calculateScheduleDriverMonitor($keyDriverScheduleMonitor->DATESCHEDULE, $defaultOffQuotaUpdate);
			}
		}
		
		return true;
		
	}
	
}