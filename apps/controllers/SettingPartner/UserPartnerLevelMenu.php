<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserPartnerLevelMenu extends CI_controller {
	
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
		
	public function getDataLevelMenu(){
		$this->load->model('SettingPartner/ModelUserPartnerLevelMenu');
		$idUserPartnerLevel	=	validatePostVar($this->postVar, 'idUserPartnerLevel', true);
		$data				=	$this->ModelUserPartnerLevelMenu->getDataLevelMenu($idUserPartnerLevel);

		setResponseOk(array("token"=>$this->newToken, "data"=>$data));
	}
	
	public function saveDataLevelMenu(){
		
		$this->load->model('MainOperation');
		$this->load->model('SettingPartner/ModelUserPartnerLevelMenu');
		
		$userPartnerLevel	=	$this->postVar['userPartnerLevel'];
		$arrMenu			=	$this->postVar['arrIdMenu'];
		
		foreach($arrMenu as $idMenu => $open){
		
			$checkDataExists	=	$this->ModelUserPartnerLevelMenu->checkMenuIsExists($idMenu, $userPartnerLevel);

			if($checkDataExists){
				$arrUpdate		=	array("OPEN"	=>	$open);
				$updateResult	=	$this->MainOperation->updateData("m_menulevelpartner", $arrUpdate, "IDMENULEVELPARTNER", $checkDataExists['IDMENULEVELPARTNER']);
			} else {
				$arrInsert		=	array("IDMENUPARTNER"		=>	$idMenu,
										  "IDUSERLEVELPARTNER"	=>	$userPartnerLevel,
										  "OPEN"				=>	$open
										  );
				$insertResult	=	$this->MainOperation->addData("m_menulevelpartner", $arrInsert);
			}
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=> "Menu level setting data saved", "idUserPartnerLevel"=>$userPartnerLevel));
	
	}

}