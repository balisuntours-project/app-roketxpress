<?php
class ModelMasterDriver extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExists($driverName, $phone, $email, $idDriver = 0){
		
		$idDriver	= $idDriver == "" ? 0 : $idDriver;
		$query		= $this->db->query("SELECT NAME, PHONE, EMAIL FROM m_driver
										WHERE (NAME = '".$driverName."' OR PHONE = '".$phone."' OR EMAIL = '".$email."') AND
											  IDDRIVER <> ".$idDriver."
										LIMIT 1");
		$row		= $query->row_array();

		if(isset($row)){
			$field	=	$value	=	"";
			
			if($driverName == $row['NAME']){
				$field	=	"Name";
				$value	=	$row['NAME'];
			}
			
			if($phone == $row['PHONE']){
				$field	=	"Phone Number";
				$value	=	$row['PHONE'];
			}
			
			if($email == $row['EMAIL']){
				$field	=	"Email";
				$value	=	$row['EMAIL'];
			}
			
			return array($field, $value);
		}
		
		return false;
		
	}
		
	public function getDataDriver($arrCondition, $page, $dataPerPage= 10){
		$ci			=& get_instance();
		$ci->load->model('MainOperation');

		$condition	=	"1=1 AND ";
		$startid	=	($page * 1 - 1) * $dataPerPage;
		foreach($arrCondition as $key=>$value){
			switch($key){
				case "keywordSearch":	$condition	.=	"(B.DRIVERTYPE LIKE '%".$value."%' OR A.NAME LIKE '%".$value."%' OR A.ADDRESS LIKE '%".$value."%' OR A.PHONE LIKE '%".$value."%' OR A.EMAIL LIKE '%".$value."%') AND ";
										break;
				default				:	$condition	.=	"1=1 AND ";
										break;
			}
		}
		
		$condition	=	substr($condition, 0, -4);
		$baseQuery	=	"SELECT A.IDDRIVER AS IDDATA, A.RANKNUMBER, B.DRIVERTYPE, A.SCHEDULETYPE, A.NAME, IFNULL(A.NAMEFULL, '-') AS NAMEFULL, A.ADDRESS, A.PHONE, A.EMAIL, A.CARRENTALDRIVERSTATUS,
								IFNULL(DATE_FORMAT(E.LASTLOGIN, '%d %b %Y %H:%i'), '-') AS LASTLOGIN, IFNULL(DATE_FORMAT(E.LASTACTIVITY, '%d %b %Y %H:%i'), '-') AS LASTACTIVITY,
								IF(E.TEMPOTP = 0 OR E.TEMPOTP IS NULL, '-', E.TEMPOTP) AS TEMPOTP, A.STATUS, IFNULL(CONCAT(C.MINCAPACITY, ' - ', C.MAXCAPACITY), '-') AS CAPACITYDETAIL,
								IFNULL(C.CARCAPACITYNAME, '-') AS CARCAPACITYNAME, A.CARNUMBERPLATE, A.CARBRAND, A.CARMODEL, IFNULL(D.DRIVERAREA, '-') AS DRIVERAREA, A.DRIVERQUOTA,
								A.PARTNERSHIPTYPE
						FROM m_driver A
						LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
						LEFT JOIN m_carcapacity C ON A.IDCARCAPACITY = C.IDCARCAPACITY
						LEFT JOIN (SELECT DA.IDDRIVER,
										  GROUP_CONCAT(CONCAT(DB.AREANAME, ' (', DB.AREATAGS, ')') ORDER BY DA.ORDERNUMBER SEPARATOR '<br/>') AS DRIVERAREA
								   FROM t_driverareaorder DA
								   LEFT JOIN m_area DB ON DA.IDAREA = DB.IDAREA
								   GROUP BY DA.IDDRIVER
								   ORDER BY DA.ORDERNUMBER
								   ) AS D ON A.IDDRIVER = D.IDDRIVER
						LEFT JOIN (SELECT * FROM m_usermobile
								   WHERE IDPARTNERTYPE = 2
								   GROUP BY IDPARTNERTYPE, IDPARTNER
								   ORDER BY LASTLOGIN DESC) E ON A.IDDRIVER = E.IDPARTNER
						WHERE ".$condition."
						GROUP BY A.IDDRIVER
						ORDER BY FIELD(A.PARTNERSHIPTYPE, 4, 1, 2, 3), FIELD(A.IDDRIVERTYPE, 1, 3, 2), A.RANKNUMBER, A.NAME";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDDATA", $page, $dataPerPage);		
		return $ci->MainOperation->generateEmptyResult();		
	}
		
	public function getDataDriverById($idData){
		$baseQuery	=	"SELECT IDDRIVER AS IDDATA, IDDRIVERTYPE, IDCARCAPACITY, SCHEDULETYPE, NAME, IFNULL(NAMEFULL, '-') AS NAMEFULL, ADDRESS,
								REPLACE(PHONE, '+62', '') AS PHONE, EMAIL, PASSWORDPLAIN, SECRETPINSTATUS, DATE_FORMAT(SECRETPINLASTUPDATE, '%d %b %Y %H:%i') AS SECRETPINLASTUPDATE,
								NEWFINANCESCHEME, DRIVERQUOTA, PARTNERSHIPTYPE, REVIEWBONUSPUNISHMENT, CARNUMBERPLATE, CARBRAND, CARMODEL
						FROM m_driver
						WHERE IDDRIVER = ".$idData."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row;		
		return array(
			"IDDATA"				=>	0,
			"IDDRIVERTYPE"			=>	0,
			"IDCARCAPACITY"			=>	0,
			"SCHEDULETYPE"			=>	1,
			"NAME"					=>	"",
			"NAMEFULL"				=>	"",
			"ADDRESS"				=>	"",
			"PHONE"					=>	"",
			"EMAIL"					=>	"",
			"PASSWORDPLAIN"			=>	"",
			"SECRETPINSTATUS"		=>	2,
			"SECRETPINLASTUPDATE"	=>	"-",
			"NEWFINANCESCHEME"		=>	0,
			"DRIVERQUOTA"			=>	1,
			"PARTNERSHIPTYPE"		=>	1,
			"REVIEWBONUSPUNISHMENT"	=>	0,
			"CARNUMBERPLATE"		=>	'-',
			"CARBRAND"				=>	'-',
			"CARMODEL"				=>	'-'
		);
	}
		
	public function getDataDriverRank($idDriverType, $partnershipType){
		$con_driverType		=	$idDriverType != false ? "IDDRIVERTYPE = ".$idDriverType : "1=1";
		$con_partnershipType=	$partnershipType != false ? "PARTNERSHIPTYPE = ".$partnershipType : "1=1";
		$baseQuery			=	"SELECT IDDRIVER, NAME, TOTALPOINT FROM m_driver
								WHERE ".$con_driverType." AND ".$con_partnershipType." AND STATUS = 1
								ORDER BY RANKNUMBER, NAME";
		$query				=	$this->db->query($baseQuery);
		$result				=	$query->result();
		
		if(isset($result)) return $result;
		return array();		
	}
		
	public function getDataDriverAreaOrder($idDriver){
		$baseQuery	=	"SELECT A.IDAREA, CONCAT(A.AREANAME, ' (', A.AREATAGS, ')') AS AREANAME,
								IFNULL(B.IDDRIVERAREAORDER, 0) AS IDDRIVERAREAORDER
						 FROM m_area A
						 LEFT JOIN t_driverareaorder B ON A.IDAREA = B.IDAREA AND B.IDDRIVER = ".$idDriver."
						 WHERE A.NONAREA = 0
						 ORDER BY B.ORDERNUMBER, AREANAME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return array();		
	}
	
	public function getDataPointDriver($dateStart){
		$baseQuery	=	"SELECT A.IDDRIVER, IF(A.PARTNERSHIPTYPE != 1, A.PARTNERSHIPTYPE + 2, A.IDDRIVERTYPE) AS IDDRIVERTYPE, IFNULL(SUM(B.POINT), 0) + A.BASICPOINT AS TOTALPOINT
						FROM m_driver A
						LEFT JOIN t_driverratingpoint B ON A.IDDRIVER = B.IDDRIVER AND B.DATERATINGPOINT >= '".$dateStart."'
						GROUP BY A.IDDRIVER
						ORDER BY IDDRIVERTYPE, A.STATUS DESC, TOTALPOINT DESC, A.NAME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}
	
	public function getDataDriverScheduleMonitor(){		
		$query	=	$this->db->query(
						"SELECT DATESCHEDULE FROM t_scheduledrivermonitor
						WHERE DATESCHEDULE >= '".date('Y-m-d')."'"
					);
		$result	=	$query->result();

		if (isset($result)) return $result;		
		return false;
	}
	
	public function getDataOffDriverFreelance($idDriver){
		$query	=	$this->db->query(
						"SELECT GROUP_CONCAT(IDDAYOFF) AS STRARRIDOFFDRIVER
						FROM t_dayoff
						WHERE DATEDAYOFF > '".date('Y-m-d')."' AND REASON = 'Default Off' AND IDDRIVER = ".$idDriver."
						GROUP BY IDDRIVER"
					);
		$row 	=	$query->row_array();

		if (isset($row)) return $row;	
		return false;
	}
	
	public function getDataLoanType(){
		$query	=	$this->db->query(
						"SELECT IDLOANTYPE, STATUSPERMISSION FROM m_loantype"
					);
		$result	=	$query->result();

		if (isset($result)) return $result;	
		return false;
	}
	
	public function getDataLastAgreementDriver(){
		$query	=	$this->db->query(
						"SELECT IDDRIVERAGREEMENTMASTER FROM m_driveragreementmaster
						ORDER BY DATEDETERMINATION DESC
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if (isset($row)) return $row;	
		return false;
	}
}