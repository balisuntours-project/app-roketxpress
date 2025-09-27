<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserLevelMenu extends CI_controller {
	
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
		
		$this->load->model('Setting/ModelUserLevelMenu');
		$idUserLevel	=	validatePostVar($this->postVar, 'idUserLevel', true);
		$data			=	$this->ModelUserLevelMenu->getDataLevelMenu($idUserLevel);

		setResponseOk(array("token"=>$this->newToken, "data"=>$data));
	
	}
	
	public function saveDataLevelMenu(){
		
		$this->load->model('MainOperation');
		$this->load->model('Setting/ModelUserLevelMenu');
		
		$userLevel			=	$this->postVar['userLevel'];
		$arrMenu			=	$this->postVar['arrIdMenu'];
		
		foreach($arrMenu as $idMenu => $open){
		
			$checkDataExists	=	$this->ModelUserLevelMenu->checkMenuIsExists($idMenu, $userLevel);
			
			if($checkDataExists){
				$arrUpdate		=	array("OPEN"	=>	$open);
				$updateResult	=	$this->MainOperation->updateData("m_menulevel", $arrUpdate, "IDMENULEVEL", $checkDataExists['IDMENULEVEL']);
			} else {
				$arrInsert		=	array("IDMENU"	=>	$idMenu,
										  "LEVEL"	=>	$userLevel,
										  "OPEN"	=>	$open
										  );
				$insertResult	=	$this->MainOperation->addData("m_menulevel", $arrInsert);
			}
		}
		
		setResponseOk(array("token"=>$this->newToken, "msg"=> "Menu level setting data saved", "idUserLevel"=>$userLevel));
	
	}

}