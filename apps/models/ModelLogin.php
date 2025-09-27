<?php
class ModelLogin extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function CheckLastActivity($token){
		
		$query	= $this->db->query("SELECT LASTACTIVITY FROM m_useradmin
									WHERE (TOKEN1 = '".$token."' OR TOKEN2 = '".$token."') AND TOKEN1 IS NOT NULL AND TOKEN2 IS NOT NULL
									LIMIT 0,1");
		$row	= $query->row_array();

		if (isset($row)){
			return $row['LASTACTIVITY'];
		}
		
		return false;
	}

	public function GetTokenExpired($token){
		
		$query	= $this->db->query("SELECT TOKENEXPIRED FROM m_useradmin
									WHERE (TOKEN1 = '".$token."' OR TOKEN2 = '".$token."') AND TOKEN1 IS NOT NULL AND TOKEN2 IS NOT NULL
									LIMIT 0,1");
		$row	= $query->row_array();

		if (isset($row)){
			return substr($row['TOKENEXPIRED'],0,19);
		}
		
		return false;
	}

	public function GetNewestToken($token){
		
		$query	= $this->db->query("SELECT TOKEN1 AS TOKEN FROM m_useradmin
									WHERE (TOKEN1 = '".$token."' OR TOKEN2 = '".$token."') AND TOKEN1 IS NOT NULL AND TOKEN2 IS NOT NULL
									LIMIT 0,1");
		$row	= $query->row_array();
		
		if(!isset($row)){
			return "";
		}

		return $row['TOKEN'];

	}
	
	public function UpdateLastActivityNewToken($token, $newToken, $newLogin = false, $idUserAdmin = false){

		$token2				=	$token;
		$tokenExpiredTime	=	date("Y-m-d H:i:s", time() + LOGIN_TOKEN_MAXAGE_SECONDS);
		if($newLogin) $token2 = $newToken;
		
		if($newLogin) $this->db->set('TOKENEXPIRED', $tokenExpiredTime);
		$this->db->set('LASTACTIVITY', date('Y-m-d H:i:s'));
		$this->db->set('TOKEN1', $newToken);
		$this->db->set('TOKEN2', $token2);
		
		if($idUserAdmin){
			$this->db->where('IDUSERADMIN', $idUserAdmin);
		} else {
			$this->db->where('TOKEN1', $token);
			$this->db->or_where('TOKEN2', $token);
		}
		
		$this->db->update('m_useradmin');
		
		return true;
		
	}
	
	public function UpdateLastActivity($token){

		$this->db->set('LASTACTIVITY', date('Y-m-d H:i:s'));
		$this->db->where('TOKEN1',$token);
		$this->db->or_where('TOKEN2',$token);
		$this->db->update('m_useradmin');
		
		return true;
		
	}
	
	public function UpdateTokenExpired($token, $tokenExpired){

		$this->db->set('TOKENEXPIRED', $tokenExpired);
		$this->db->where('TOKEN1',$token);
		$this->db->or_where('TOKEN2',$token);
		$this->db->update('m_useradmin');
		
		return true;
		
	}
	
	public function UserLogin($username, $password){
		
		$query	= $this->db->query("SELECT A.NAME, A.LEVEL, A.TOKEN1 AS TOKEN, A.EMAIL, B.LEVELNAME, A.IDUSERADMIN
									FROM m_useradmin A
									LEFT JOIN m_userlevel B ON A.LEVEL = B.IDUSERLEVEL
									WHERE A.USERNAME = '".$username."' AND A.PASSWORD = MD5('".$password."')
									LIMIT 0,1");
		$row	= $query->row_array();

		if (isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	public function UserMenu($level){
		
		$con_superAdmin	= $level == 1 ? "1=1" : "B.SUPERADMIN = 0";
		$query			= $this->db->query("SELECT B.GROUPNAME, B.DISPLAYNAME, B.MENUALIAS, B.URL, B.ICON
											FROM m_menulevel A
											LEFT JOIN m_menu B ON A.IDMENU = B.IDMENU
											WHERE A.LEVEL = ".$level." AND A.OPEN = 1 AND ".$con_superAdmin."
											ORDER BY B.ORDERGROUP, B.ORDERMENU");
		return $query->result();
		
	}
	
	public function UserGroupMenu($level){
		
		$con_superAdmin	= $level == 1 ? "1=1" : "B.SUPERADMIN = 0";
		$query			= $this->db->query("SELECT B.GROUPNAME FROM m_menulevel A
											LEFT JOIN m_menu B ON A.IDMENU = B.IDMENU
											WHERE A.LEVEL = ".$level." AND A.OPEN = 1 AND ".$con_superAdmin."
											GROUP BY B.GROUPNAME
											HAVING COUNT(B.IDMENU) > 1
											ORDER BY ORDERGROUP");
		return $query->result();
		
	}

	public function GetIDUserByToken($token){
		
		$query	= $this->db->query("SELECT IDUSERADMIN FROM m_useradmin
									WHERE (TOKEN1 = '".$token."' OR TOKEN2 = '".$token."') AND TOKEN1 IS NOT NULL AND TOKEN2 IS NOT NULL
									LIMIT 0,1");
		$row	= $query->row_array();
		
		if(!isset($row)){
			return 0;
		}

		return $row['IDUSERADMIN'];

	}
	
	public function SetLogout($token){

		$this->db->set('TOKEN1', null);
		$this->db->set('TOKEN2', null);
		$this->db->where('TOKEN1',$token);
		$this->db->or_where('TOKEN2',$token);
		$this->db->update('m_useradmin');
		
		return true;
		
	}

	public function isOldPasswordCorrect($idUser, $oldPassword){

		$baseQuery		=	"SELECT IDUSERADMIN FROM m_useradmin
							 WHERE IDUSERADMIN = ".$idUser." AND PASSWORD = MD5('".$oldPassword."')
							 LIMIT 1";
		$query	= $this->db->query($baseQuery);
		$row	= $query->row_array();

		if(isset($row)){
			return true;
		}
		
		return false;
	}

	public function isNotifSignalExist($idUserAdmin, $OSUserId){

		$baseQuery	=	"SELECT B.IDUSERNOTIFSIGNAL FROM m_useradmin A
						 LEFT JOIN t_usernotifsignal B ON A.IDUSERADMIN = B.IDUSERADMIN
						 WHERE B.IDUSERADMIN = ".$idUserAdmin." AND B.OSUSERID = '".$OSUserId."'
						 LIMIT 1";
		$query		= $this->db->query($baseQuery);
		$row		= $query->row_array();

		if(isset($row)){
			return $row['IDUSERNOTIFSIGNAL'];
		}
		
		return false;
	}
	
	public function getUnreadNotificationList($idUserAdmin){
		
		$query	= $this->db->query("SELECT A.IDMESSAGEADMIN, A.IDMESSAGEADMINTYPE, B.MESSAGEADMINTYPE, B.ICON, A.TITLE, A.MESSAGE, A.PARAMLIST,
										   DATE_FORMAT(A.DATETIMEINSERT, '%d %b %Y %H:%i') AS DATETIMEINSERT
									FROM t_messageadmin A
									LEFT JOIN m_messageadmintype B ON A.IDMESSAGEADMINTYPE = B.IDMESSAGEADMINTYPE
									WHERE A.IDUSERADMIN = ".$idUserAdmin." AND A.STATUS = 0
									ORDER BY A.DATETIMEINSERT DESC");
		$result	= $query->result();
	
		if (isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
}