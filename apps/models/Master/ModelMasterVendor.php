<?php
class ModelMasterVendor extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExists($vendorName, $phone, $email, $idVendor = 0){
		$idVendor	= $idVendor == "" ? 0 : $idVendor;
		$sql		= "SELECT NAME, PHONE, EMAIL FROM m_vendor
						WHERE (NAME = '".$vendorName."' OR PHONE = '".$phone."' OR EMAIL = '".$email."') AND
							  IDVENDOR <> ".$idVendor."
						LIMIT 1";
		$query		= $this->db->query($sql);
		$row		= $query->row_array();

		if(isset($row)){
			$field	=	$value	=	"";
			
			if($vendorName == $row['NAME']){
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

	public function getDataVendor($arrCondition, $page, $dataPerPage= 10){
		$ci			=& get_instance();
		$ci->load->model('MainOperation');

		$condition	=	"1=1 AND ";
		$startid	=	($page * 1 - 1) * $dataPerPage;
		foreach($arrCondition as $key=>$value){
			switch($key){
				case "keywordSearch":	$condition	.=	"(B.VENDORTYPE LIKE '%".$value."%' OR A.NAME LIKE '%".$value."%' OR A.ADDRESS LIKE '%".$value."%' OR A.PHONE LIKE '%".$value."%' OR A.EMAIL LIKE '%".$value."%') AND ";
										break;
				default				:	$condition	.=	"1=1 AND ";
										break;
			}
		}
		
		$condition	=	substr($condition, 0, -4);
		$baseQuery	=	"SELECT A.IDVENDOR AS IDDATA, B.VENDORTYPE, A.NAME, A.ADDRESS, A.PHONE, A.EMAIL, A.FINANCESCHEMETYPE, A.TRANSPORTSERVICE, A.STATUS, A.AUTOREDUCECOLLECTPAYMENT
						FROM m_vendor A
						LEFT JOIN m_vendortype B ON A.IDVENDORTYPE = B.IDVENDORTYPE
						WHERE ".$condition."
						ORDER BY B.VENDORTYPE, A.NAME";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDDATA", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}
		
	public function getDataVendorById($idData){
		$baseQuery	=	"SELECT IDVENDOR AS IDDATA, IDVENDORTYPE, NAME, ADDRESS, REPLACE(PHONE, '+62', '') AS PHONE,
								EMAIL, SECRETPINSTATUS, DATE_FORMAT(SECRETPINLASTUPDATE, '%d %b %Y %H:%i') AS SECRETPINLASTUPDATE,
								NEWFINANCESCHEME, FINANCESCHEMETYPE, AUTOREDUCECOLLECTPAYMENT
						FROM m_vendor
						WHERE IDVENDOR = ".$idData."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)) return $row;
		return array(
			"IDDATA"				=>	0,
			"IDPARTNERRTYPE"		=>	0,
			"NAME"					=>	"",
			"ADDRESS"				=>	"",
			"PHONE"					=>	"",
			"EMAIL"					=>	"",
			"SECRETPINSTATUS"		=>	2,
			"SECRETPINLASTUPDATE"	=>	"-"
		);
	}
	
	public function getDataScheduleVendor($idPartner, $lastScheduleWithdrawal){
		$baseQuery	=	"SELECT IDRESERVATIONDETAILS FROM t_reservationdetails
						WHERE IDVENDOR = ".$idPartner." AND SCHEDULEDATE > '".$lastScheduleWithdrawal."'
						ORDER BY SCHEDULEDATE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;		
	}
	
	public function getDataCollectPaymentVendor($idPartner, $lastScheduleWithdrawal){
		$baseQuery	=	"SELECT IDCOLLECTPAYMENT FROM t_collectpayment
						WHERE IDVENDOR = ".$idPartner." AND DATECOLLECT > '".$lastScheduleWithdrawal."'
						ORDER BY DATECOLLECT";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
	
	public function getDataLastReservationVendor($idVendor, $lastDateWithdrawal){
		$baseQuery	=	"SELECT B.IDSCHEDULEVENDOR, IFNULL(C.IDFEE, 0) AS IDFEE, IFNULL(C.IDWITHDRAWALRECAP, 0) AS IDWITHDRAWALRECAPFEE,
								IFNULL(D.IDCOLLECTPAYMENT, 0) AS IDCOLLECTPAYMENT, IFNULL(D.IDWITHDRAWALRECAP, 0) AS IDWITHDRAWALRECAPCOLLECTPAYMENT
						FROM t_reservationdetails A
						LEFT JOIN t_schedulevendor B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						LEFT JOIN (
									SELECT IDFEE, IDRESERVATIONDETAILS, IDWITHDRAWALRECAP
									FROM t_fee 
									WHERE IDVENDOR = ".$idVendor."
								  ) AS C ON A.IDRESERVATIONDETAILS = C.IDRESERVATIONDETAILS
						LEFT JOIN (
									SELECT IDCOLLECTPAYMENT, IDRESERVATION, IDWITHDRAWALRECAP
									FROM t_collectpayment
									WHERE IDVENDOR = ".$idVendor."
									GROUP BY IDCOLLECTPAYMENT
								  ) AS D ON A.IDRESERVATION = D.IDRESERVATION
						WHERE A.IDVENDOR = ".$idVendor." AND A.SCHEDULEDATE <= '".$lastDateWithdrawal."' AND B.IDSCHEDULEVENDOR IS NOT NULL
						ORDER BY A.SCHEDULEDATE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;		
	}
}