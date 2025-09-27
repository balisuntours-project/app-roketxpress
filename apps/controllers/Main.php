<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	
	public function __construct(){
        parent::__construct();
    }
	
	public function index(){
		$this->load->helper('url');
		$this->load->view('main');
	}
	
	public function logout($token){
		$this->load->helper('url');
		$this->load->model('MainOperation');
		$this->MainOperation->updateData('m_useradmin', array("TOKEN1" => '-', "TOKEN2" => '-'), array("TOKEN1" => $token));

		redirect(base_url(), 'auto');
		die();
	}
	
	public function loginPage(){
		$this->load->helper('url');
		$this->load->view('login');
	}
	
	public function mainPage(){
		$this->load->model('MainOperation');
		$this->load->model('ModelLogin');
		$this->load->helper('url');
		
		$this->postVar	=	decodeJsonPost();
		$this->token	=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array());
		$this->newToken	=	isLoggedIn($this->token, true);
		$lastPageAlias	=	isset($this->postVar['lastPageAlias']) ? $this->postVar['lastPageAlias'] : "";
		
		$nameUser		=	isset($this->postVar['NAME']) ? $this->postVar['NAME'] : setResponseBadRequest(array());
		$email			=	isset($this->postVar['EMAIL']) ? $this->postVar['EMAIL'] : setResponseBadRequest(array());
		$level			=	isset($this->postVar['LEVEL']) ? $this->postVar['LEVEL'] : setResponseBadRequest(array());
		$levelName		=	isset($this->postVar['LEVELNAME']) ? $this->postVar['LEVELNAME'] : setResponseBadRequest(array());
		$allowNotifList	=	$this->MainOperation->getListNotificationTypeUserLevel($level);
		$menuResult		=	$this->ModelLogin->UserMenu($level);
		$menuGroup		=	$this->ModelLogin->UserGroupMenu($level);

		$menuElement	=	$this->menuBuilder($menuResult, $lastPageAlias, $menuGroup);
		$data			=	array(
			"nameUser"		=>	$nameUser,
			"email"			=>	$email,
			"levelName"		=>	$levelName,
			"menuElement"	=>	$menuElement,
			"optionMonth"	=>	OPTION_MONTH,
			"optionYear"	=>	OPTION_YEAR,
			"allowNotifList"=>	json_encode($allowNotifList)
		);
		
		$htmlRes		=	$this->load->view('mainpage', $data, TRUE);
		setResponseOk(array("token"=>$this->newToken, "htmlRes"=>$htmlRes));
	}
	
	private function menuBuilder($menuList, $lastPageAlias, $menuGroup){
		if($menuList == "" || !is_array($menuList) || empty($menuList)){
			return "<li><center>No Menu</center></li>";
		} else {
			$groupActive	=	0;
			$arrGroupCek	=	array();
			$i				=	0;
			$menuElement	=	$groupActiveName	=	"";
				
			foreach($menuGroup as $keyGroup){
				$arrGroupCek[]	=	$keyGroup->GROUPNAME;
			}
			
			foreach($menuList as $key){
				if(!in_array($key->GROUPNAME, $arrGroupCek)){
					if($groupActive == 1){
						$groupActive	=	0;
						$menuElement	.=	"</ul></li>";
					}

					$active			=	$lastPageAlias == $key->MENUALIAS ? "active" : "";
					$menuElement	.=	"<li id='menu".$key->MENUALIAS."' class='menu-item ".$active."' data-alias='".$key->MENUALIAS."' data-url='".$key->URL."'>
											<a href='#'><i class='fa ".$key->ICON."'></i> <span>".$key->DISPLAYNAME."</span></a>";
				} else {
					if($groupActiveName != $key->GROUPNAME && $groupActiveName != "" && $groupActive == 1){
						$menuElement	.=	"</ul></li>";
					}
					
					if($groupActive == 0 || $groupActiveName != $key->GROUPNAME){
						$menuElement	.=	"<li class='has-sub-menu'><a href='#'><i class='fa ".$key->ICON."'></i> <span id='groupMenu".str_replace(" ", "", $key->GROUPNAME)."'>".$key->GROUPNAME."</span><span class='menu-expand'><i class='fa fa-chevron-down'></i></span></a><ul class='side-header-sub-menu' style='display: block;'>";
						$groupActive	=	1;
					}
					
					$menuElement	.=	"<li id='menu".$key->MENUALIAS."' class='menu-item' data-alias='".$key->MENUALIAS."' data-url='".$key->URL."'><a href='#'><span>".$key->DISPLAYNAME."</span></a></li>";
					$groupActiveName=	$key->GROUPNAME;
				}
				$i++;
			}
			return $menuElement."</ul>";
		}
	}

	public function organization(){
		$file	=	PATH_STORAGE.'bst_organization.pdf';
		if (file_exists($file)) {
			header('Content-Type: application/pdf');
			header('Content-Disposition: inline; filename="' . basename($file) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Accept-Ranges: bytes');
			@readfile($file);
		} else {
			echo 'File not found.';
			die();
		}
	}

	public function privacyPolicy(){
		$this->load->view('utils/privacy_policy', array());
	}
}