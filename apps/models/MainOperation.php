<?php
class MainOperation extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function addData($table, $arrFieldValue){
		try {
			$this->db->insert($table, $arrFieldValue);
			$insert_id 		= $this->db->insert_id();
			$affectedRows	= $this->db->affected_rows();

			if($insert_id > 0 || $affectedRows > 0){
				return array("status"=>true, "errCode"=>false, "insertID"=>$insert_id);
			}
			return array("status"=>false, "errCode" => 0);
		}  catch (Exception $e) {
			$error		=	$this->db->error();
			$errorCode	=	$error['code'];
			return array("status"=>false, "errCode"=>$errorCode);
		}
	}
	
	public function updateData($table, $arrFieldValue, $fieldWhere, $idWhere = null){
		try {
			if(!is_array($fieldWhere)){
				$this->db->where($fieldWhere, $idWhere);
			} else {
				foreach($fieldWhere as $field => $value){
					$this->db->where($field, $value);
				}
			}
			
			$this->db->update($table, $arrFieldValue);
			$affectedRows = $this->db->affected_rows();

			if($affectedRows > 0){
				return array("status"=>true, "errCode"=>false);
			}
			
			return array("status"=>false, "errCode" => 0);
		}  catch (Exception $e) {
			$error		=	$this->db->error();
			$errorCode	=	$error['code'];
			return array("status"=>false, "errCode"=>$errorCode);
		}
	}
	
	public function updateDataWhereCustomString($table, $arrFieldValue, $whereCustomString){
		try {
			foreach($arrFieldValue as $field => $value){
				$paramBool	=	substr($value, 0, 6) == "FALSE-" ? FALSE : TRUE;
				$value		=	substr($value, 0, 6) == "FALSE-" ? substr($value, 6, strlen($value)) : $value;
				$this->db->set($field, $value, $paramBool);
			}
			
			$this->db->where($whereCustomString);
			$this->db->update($table);
			$affectedRows	=	$this->db->affected_rows();

			if($affectedRows > 0){
				return array("status"=>true, "errCode"=>false);
			}
			return array("status"=>false, "errCode"=>0);
		}  catch (Exception $e) {
			$error		=	$this->db->error();
			$errorCode	=	$error['code'];
			return array("status"=>false, "errCode"=>$errorCode);
		}
	}
	
	public function updateDataIn($table, $arrFieldValue, $fieldWhere, $arrWhere = null){
		try {
			$this->db->where_in($fieldWhere, $arrWhere);
			$this->db->update($table, $arrFieldValue);
			$affectedRows = $this->db->affected_rows();

			if($affectedRows > 0){
				return array("status"=>true, "errCode"=>false);
			}
			return array("status"=>false, "errCode"=>0);
		}  catch (Exception $e) {
			$error		=	$this->db->error();
			$errorCode	=	$error['code'];
			return array("status"=>false, "errCode"=>$errorCode);
		}
	}
	
	public function deleteData($table, $arrWhere){
		try {
			$this->db->delete($table, $arrWhere);
			$affectedRows = $this->db->affected_rows();

			if($affectedRows > 0){
				return array("status"=>true, "errCode"=>false);
			}
			return array("status"=>false, "errCode"=>0);
		}  catch (Exception $e) {
			$error		=	$this->db->error();
			$errorCode	=	$error['code'];
			return array("status"=>false, "errCode"=>$errorCode);
		}
	}
	
	public function customQuery($stringQuery){
		$this->db->query($stringQuery);
		return true;
	}
	
	public function generateResultPagination($result, $basequery, $keyfield, $page, $dataperpage){
		$startid	=	($page * 1 - 1) * $dataperpage;
		$datastart	=	$startid + 1;
		$dataend	=	$datastart + $dataperpage - 1;
		$query		=	$this->db->query("SELECT IFNULL(COUNT(".$keyfield."),0) AS TOTAL FROM (".$basequery.") AS A");
		
		$row		=	$query->row_array();
		$datatotal	=	$row['TOTAL'];
		$pagetotal	=	ceil($datatotal/$dataperpage);
		$datastart	=	$pagetotal == 0 ? 0 : $startid + 1;
		$startnumber=	$pagetotal == 0 ? 0 : ($page-1) * $dataperpage + 1;
		$dataend	=	$dataend > $datatotal ? $datatotal : $dataend;
		
		return array("status"=>200, "data"=>$result ,"dataStart"=>$datastart, "dataEnd"=>$dataend, "dataTotal"=>$datatotal, "pageTotal"=>$pagetotal, "startNumber"=>$startnumber);
    }

	public function generateEmptyResult(){
		return array("data"=>array(), "datastart"=>0, "dataend"=>0, "datatotal"=>0, "pagetotal"=>0);
	}
	
	public function insertLogDataSend($dataSend){
		$this->db->insert(
			'log_datasend',
			array(
				'URL'			=>"[".$_SERVER['REQUEST_METHOD']."] ".$_SERVER['REQUEST_URI'],
				'DATASEND'		=>$dataSend,
				'TANGGALWAKTU'	=>date('Y-m-d H:i:s')
			)
		);
		return true;
	}

	public function getLevelUser($token){
		$query	=	$this->db->query(
						"SELECT LEVEL FROM m_useradmin
						WHERE TOKEN1 = '".$token."' OR TOKEN2 = '".$token."'
						LIMIT 1"
					);
		$result	=	$query->row_array();
		
		if(isset($result)) return $result['LEVEL'];
		return 0;
	}

	public function getIDUserAdmin($token){
		$query	=	$this->db->query(
						"SELECT IDUSERADMIN FROM m_useradmin
						WHERE TOKEN1 = '".$token."' OR TOKEN2 = '".$token."'
						LIMIT 1"
					);
		$result	=	$query->row_array();

		if(isset($result)) return $result['IDUSERADMIN'];
		return 0;
	}
	
	public function getDataReservationType(){
		$query	=	$this->db->query(
						"SELECT IDRESERVATIONTYPE, RESERVATIONTYPE, TITLEKEYWORDS, TITLEKEYWORDSEXCLUTION, EMAILADDRESS FROM m_reservationtype
						ORDER BY IDRESERVATIONTYPE DESC"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return false;
	}
	
	public function getDataSenderEmailSource(){
		$query	=	$this->db->query(
						"SELECT IDSOURCE, MAILSENDERNAME, EMAILSENDER1, EMAILSENDER2, EMAILSENDER3, DEFAULTCURRENCY FROM m_source
						WHERE MAILSENDERNAME != '' AND (EMAILSENDER1 != '' OR EMAILSENDER2 != '' OR EMAILSENDER3 != '') AND STATUS = 1
						ORDER BY IDSOURCE"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return false;
	}

	public function getDataUserAdmin($token){
		$query	=	$this->db->query(
						"SELECT A.IDUSERADMIN, A.IDRESERVATIONTYPE, A.NAME, A.EMAIL, A.LEVEL, B.PMSADDDRIVERSCHEDULE, B.PMSDELETEDRIVERSCHEDULE
						FROM m_useradmin A
						LEFT JOIN m_userlevel B ON A.LEVEL = B.IDUSERLEVEL
						WHERE A.TOKEN1 = '".$token."' OR A.TOKEN2 = '".$token."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return array(
			"IDUSERADMIN"				=>	0,
			"IDRESERVATIONTYPE"			=>	0,
			"NAME"						=>	"",
			"EMAIL"						=>	"",
			"LEVEL"						=>	0,
			"PMSADDDRIVERSCHEDULE"		=>	0,
			"PMSDELETEDRIVERSCHEDULE"	=>	0
		);
	}

	public function getDataExchangeCurrency(){
		$query	=	$this->db->query(
						"SELECT CURRENCY, EXCHANGETOIDR FROM helper_exchangecurrency
						ORDER BY CURRENCY"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return array();
	}

	public function getDataDriver($idDriver){
		$query	=	$this->db->query(
						"SELECT B.IDUSERMOBILE, A.NAME, A.ADDRESS, A.PHONE, A.EMAIL, IFNULL(B.TOKENFCM, '') AS TOKENFCM, C.DRIVERTYPE, A.PARTNERSHIPTYPE, A.RTDBREFCODE
						FROM m_driver A
						LEFT JOIN m_usermobile B ON A.IDDRIVER = B.IDPARTNER AND B.IDPARTNERTYPE = 2 AND B.TOKENFCM != ''
						LEFT JOIN m_drivertype C ON A.IDDRIVERTYPE = C.IDDRIVERTYPE
						WHERE A.IDDRIVER = '".$idDriver."'
						ORDER BY B.LASTACTIVITY DESC
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return array(	
			"IDUSERMOBILE"		=>	0,
			"NAME"				=>	"",
			"ADDRESS"			=>	"",
			"PHONE"				=>	"",
			"EMAIL"				=>	"",
			"TOKENFCM"			=>	"",
			"DRIVERTYPE"		=>	"",
			"PARTNERSHIPTYPE"	=>	3
		);
	}
	
	public function getDataVendor($idVendor){
		$query	=	$this->db->query(
						"SELECT B.IDUSERMOBILE, A.RTDBREFCODE, A.NAME, A.ADDRESS, A.PHONE, A.EMAIL, IFNULL(B.TOKENFCM, '') AS TOKENFCM, A.NEWFINANCESCHEME, A.RTDBREFCODE
						FROM m_vendor A
						LEFT JOIN m_usermobile B ON A.IDVENDOR = B.IDPARTNER AND B.IDPARTNERTYPE = 1 AND B.TOKENFCM != ''
						WHERE A.IDVENDOR = '".$idVendor."'
						ORDER BY B.LASTACTIVITY DESC
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return array(	
			"IDUSERMOBILE"	=>	0,
			"RTDBREFCODE"	=>	"-",
			"NAME"			=>	"",
			"ADDRESS"		=>	"",
			"PHONE"			=>	"",
			"EMAIL"			=>	"",
			"TOKENFCM"		=>	""
		);
	}

	public function getDataVendorCar($idCarVendor){
		$query	=	$this->db->query(
						"SELECT A.BRAND, A.MODEL, A.PLATNUMBER, A.YEAR, A.TRANSMISSION, C.TOKENFCM
						FROM t_carvendor A
						LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
						LEFT JOIN m_usermobile C ON A.IDVENDOR = C.IDPARTNER AND C.IDPARTNERTYPE = 1 AND C.IDPARTNERTYPE = 2 AND C.TOKENFCM != ''
						WHERE A.IDCARVENDOR = '".$idCarVendor."'
						ORDER BY C.LASTACTIVITY DESC
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return array(	
			"BRAND"			=>	"",
			"MODEL"			=>	"",
			"PLATNUMBER"	=>	"",
			"YEAR"			=>	"",
			"TRANSMISSION"	=>	"",
			"TOKENFCM"		=>	""
		);
	}
	
	public function getDataPlayerIdOneSignal($notificationType){
		$dateMin=	date('Y-m-d H:i:s', strtotime('-2 day'));
		$query	=	$this->db->query(
						"SELECT C.OSUSERID, B.IDUSERADMIN FROM m_userlevel A
						LEFT JOIN m_useradmin B ON A.IDUSERLEVEL = B.LEVEL
						LEFT JOIN (SELECT * FROM t_usernotifsignal
								   WHERE LASTACTIVITY > '".$dateMin."'
								   ORDER BY LASTACTIVITY DESC) C ON B.IDUSERADMIN = C.IDUSERADMIN
						WHERE A.".$notificationType." = 1 AND C.OSUSERID IS NOT NULL AND C.OSUSERID != ''"
					);
		$result	=	$query->result();

		if(isset($result)){
			$arrOSUserId	=	$arrIdUserAdmin	=	array();
			foreach($result as $keyData){
				$arrOSUserId[]		=	$keyData->OSUSERID;
				$arrIdUserAdmin[]	=	$keyData->IDUSERADMIN;
			}

			return array("arrOSUserId"=>$arrOSUserId, "arrIdUserAdmin"=>$arrIdUserAdmin);
		}
		return false;
	}
	
	public function insertAdminMessage($idMessageAdminType, $arrIdUserAdmin, $title, $message, $arrData){
		$paramList	=	json_encode($arrData);
		if(is_array($arrIdUserAdmin) && count($arrIdUserAdmin) > 0){
			$arrIdUserAdmin	=	array_unique($arrIdUserAdmin);
			foreach($arrIdUserAdmin as $idUserAdmin){
				$arrInsert	=	array(
					"IDMESSAGEADMINTYPE"=>	$idMessageAdminType,
					"IDUSERADMIN"		=>	$idUserAdmin,
					"TITLE"				=>	$title,
					"MESSAGE"			=>	$message,
					"PARAMLIST"			=>	$paramList,
					"DATETIMEINSERT"	=>	date('Y-m-d H:i:s'),
					"DATETIMEREAD"		=>	'0000-00-00 00:00:00',
					"STATUS"			=>	0
				);
				$this->addData("t_messageadmin", $arrInsert);
			}
		}
		return true;
	}

	public function checkMailDriverVendor($email, $idPrimary = 0){
		$query	=	$this->db->query(
						"SELECT IDPARTNER FROM (
							SELECT IDDRIVER AS IDPARTNER FROM m_driver
							WHERE EMAIL = '".$email."' AND IDDRIVER != ".$idPrimary."
							UNION ALL
							SELECT IDVENDOR AS IDPARTNER FROM m_vendor
							WHERE EMAIL = '".$email."' AND IDVENDOR != ".$idPrimary."
						) AS A
						GROUP BY IDPARTNER"
					);
		$row	=	$query->row_array();

		if(isset($row)) return true;
		return false;
	}

	public function getIdVendorByIdCarVendor($idCarVendor){
		$query	=	$this->db->query(
						"SELECT IDVENDOR FROM t_carvendor
						WHERE IDCARVENDOR = ".$idCarVendor."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['IDVENDOR'];
		return 0;
	}

	public function getDataMessageType($idMessageType){
		$query	=	$this->db->query(
						"SELECT MESSAGEPARTNERTYPE, ACTIVITY FROM m_messagepartnertype
						WHERE IDMESSAGEPARTNERTYPE = ".$idMessageType."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return array("MESSAGEPARTNERTYPE"=>"", "ACTIVITY"=>"Dashboard");
	}
	
	public function getBookingCode(){
		$query	= $this->db->query("SELECT BOOKINGCODE FROM temp_bookingcode
									WHERE STATUS = 0
									LIMIT 1");
		$row	= $query->row_array();

		if(isset($row)) return $row;
		return false;
	}
	
	public function getIdSourceAutoCreatePayment(){

		$query	= $this->db->query("SELECT IDSOURCE FROM m_source WHERE AUTOCREATEPAYMENT = 1");
		$result	= $query->result();

		if(isset($result)){
			$arrReturn	=	array();
			foreach($result as $keyResult){
				$arrReturn[]	=	$keyResult->IDSOURCE;
			}
			return $arrReturn;
		}
		
		return array();

	}
	
	public function getStrArrIdPaymentMethodAllowUpselling(){
		$query	=	$this->db->query("SELECT GROUP_CONCAT(IDPAYMENTMETHOD) AS STRARRIDPAYMENTMETHOD FROM m_paymentmethod WHERE ALLOWUPSELLING = 1");
		$row	=	$query->row_array();

		if(isset($row)) return '['.$row['STRARRIDPAYMENTMETHOD'].']';
		return '[]';
	}
	
	public function getStrArrIdPaymentMethodCollectPayment(){

		$query	=	$this->db->query("SELECT GROUP_CONCAT(IDPAYMENTMETHOD) AS STRARRIDPAYMENTMETHOD FROM m_paymentmethod WHERE ISCOLLECTPAYMENT = 1");
		$row	=	$query->row_array();

		if(isset($row)) return $row['STRARRIDPAYMENTMETHOD'];
		
		return false;

	}
	
	public function getReservationTypeById($idReservationType){

		$query	= $this->db->query("SELECT RESERVATIONTYPE FROM m_reservationtype WHERE IDRESERVATIONTYPE = ".$idReservationType);
		$row	= $query->row_array();

		if(isset($row)){
			return $row['RESERVATIONTYPE'];
		}
		
		return "-";

	}
	
	public function getSourceNameById($idSource){

		$query	= $this->db->query("SELECT SOURCENAME FROM m_source WHERE IDSOURCE = ".$idSource);
		$row	= $query->row_array();

		if(isset($row)){
			return $row['SOURCENAME'];
		}
		
		return "-";

	}
	
	public function getSourceDetailById($idSource){

		$query	= $this->db->query("SELECT SOURCENAME, SOURCETYPE, UPSELLINGTYPE, LOGO, STATUS FROM m_source WHERE IDSOURCE = ".$idSource);
		$row	= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return array(
					"SOURCENAME"	=>	"",
					"SOURCETYPE"	=>	1,
					"UPSELLINGTYPE"	=>	0,
					"LOGO"			=>	"",
					"STATUS"		=>	""
				);

	}
	
	public function getDriverTypeById($idDriverType){

		$query	= $this->db->query("SELECT DRIVERTYPE FROM m_drivertype WHERE IDDRIVERTYPE = ".$idDriverType);
		$row	= $query->row_array();

		if(isset($row)){
			return $row['DRIVERTYPE'];
		}
		
		return "-";

	}
	
	public function getDriverNameById($idDriver){

		$query	= $this->db->query("SELECT NAME FROM m_driver WHERE IDDRIVER = ".$idDriver);
		$row	= $query->row_array();

		if(isset($row)){
			return $row['NAME'];
		}
		
		return "-";

	}
	
	public function getAllDriverList(){

		$query	= $this->db->query("SELECT IDDRIVER FROM m_driver WHERE STATUS = 1");
		$result	= $query->result();

		if(isset($result)){
			return $result;
		}
		
		return false;

	}
	
	public function getVendorTypeById($idVendorType){

		$query	= $this->db->query("SELECT VENDORTYPE FROM m_vendortype WHERE IDVENDORTYPE = ".$idVendorType);
		$row	= $query->row_array();

		if(isset($row)){
			return $row['VENDORTYPE'];
		}
		
		return "-";

	}
	
	public function getVendorNameById($idVendor){

		$query	= $this->db->query("SELECT NAME FROM m_vendor WHERE IDVENDOR = ".$idVendor);
		$row	= $query->row_array();

		if(isset($row)){
			return $row['NAME'];
		}
		
		return "-";

	}
	
	public function getProductTypeById($idProductType){

		$query	= $this->db->query("SELECT PRODUCTTYPE FROM m_producttype WHERE IDPRODUCTTYPE = ".$idVendor);
		$row	= $query->row_array();

		if(isset($row)){
			return $row['PRODUCTTYPE'];
		}
		
		return "-";

	}
	
	public function getPaymentMethodById($idPaymentMethod){

		$query	= $this->db->query("SELECT PAYMENTMETHODNAME FROM m_paymentmethod WHERE IDPAYMENTMETHOD = ".$idPaymentMethod);
		$row	= $query->row_array();

		if(isset($row)){
			return $row['PAYMENTMETHODNAME'];
		}
		
		return "-";

	}
	
	public function getTotalUnprocessedReservationMail(){
		
		$baseQuery	=	sprintf("SELECT COUNT(IDMAILBOX) AS TOTALUNPROCESSRESERVATIONMAIL
								FROM t_mailbox
								WHERE STATUS = 0
								LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return 0;
		}
		
		return $row['TOTALUNPROCESSRESERVATIONMAIL'];
		
	}
	
	public function getTotalUnreadThreadReconfirmation(){
		
		$baseQuery	=	sprintf("SELECT COUNT(A.IDRESERVATIONRECONFIRMATION) AS TOTALUNREADTHREADRECONFIRMATION
								FROM t_reservationreconfirmation A
								LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
								WHERE A.STATUSREADTHREAD = 0 AND B.STATUS != -1
								LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return 0;
		}
		
		return $row['TOTALUNREADTHREADRECONFIRMATION'];
		
	}
	
	public function getTotalUnprocessedReservation(){
		
		$baseQuery	=	sprintf("SELECT COUNT(IDRESERVATION) AS TOTALUNPROCESSRESERVATION
								FROM t_reservation
								WHERE STATUS = 0
								LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return 0;
		}
		
		return $row['TOTALUNPROCESSRESERVATION'];
		
	}
	
	public function getTotalUndeterminedSchedule(){
		
		$minHourChangeDate	=	$this->getValueSystemSettingVariable(7) * 1;
		$intHourNow			=	date("H") * 1;
		$dateTomorrow		=	new DateTime('tomorrow');
		$dateTomorrow		=	$dateTomorrow->format('Y-m-d');
		$dateStart			=	date('Y-m-d');
		$dateEnd			=	$intHourNow > $minHourChangeDate ? $dateTomorrow : $dateStart;
		$baseQuery			=	"SELECT COUNT(A.IDRESERVATIONDETAILS) AS TOTALUNDETEMINEDSCHEDULE
								FROM t_reservationdetails A
								LEFT JOIN t_scheduledriver B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
								WHERE A.IDDRIVERTYPE != 0 AND B.IDRESERVATIONDETAILS IS NULL AND
									  A.SCHEDULEDATE BETWEEN '".$dateStart."' AND '".$dateEnd."'
								LIMIT 1";
		$query				=	$this->db->query($baseQuery);
		$row				=	$query->row_array();
		
		if(!$row){
			return 0;
		}
		
		return $row['TOTALUNDETEMINEDSCHEDULE'];
		
	}
	
	public function getValueSystemSettingVariable($idSystemSettingVariable){
		
		$baseQuery	=	"SELECT VALUE FROM a_systemsettingvariable
						WHERE IDSYSTEMSETTINGVARIABLE = ".$idSystemSettingVariable."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return 0;
		}
		
		return $row['VALUE'];
		
	}
	
	public function getListNotificationTypeUserLevel($idUserLevel){
		
		$baseQuery	=	"SELECT NOTIFMAIL, NOTIFRESERVATION, NOTIFSCHEDULEDRIVER, NOTIFSCHEDULEVENDOR, NOTIFADDITIONALCOST, NOTIFADDITIONALINCOME, NOTIFFINANCE
						FROM m_userlevel
						WHERE IDUSERLEVEL = ".$idUserLevel."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return array(
						"NOTIFMAIL"				=>	0,
						"NOTIFRESERVATION"		=>	0,
						"NOTIFSCHEDULEDRIVER"	=>	0,
						"NOTIFSCHEDULEVENDOR"	=>	0,
						"NOTIFADDITIONALCOST"	=>	0,
						"NOTIFADDITIONALINCOME"	=>	0,
						"NOTIFFINANCE"			=>	0
					);
		}
		
		return $row;
		
	}
	
	public function getDataAutoScheduleSetting($strArrIdSystemSettingIn){
		
		$query	= $this->db->query("SELECT IDSYSTEMSETTINGVARIABLE, VALUE
									FROM a_systemsettingvariable
									WHERE IDSYSTEMSETTINGVARIABLE IN (".$strArrIdSystemSettingIn.")");
		$result	= $query->result();

		if (isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDataAreaTags(){
		
		$query	= $this->db->query("SELECT IDAREA, AREATAGS FROM m_area");
		$result	= $query->result();

		if (isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getNewFinanceSchemeDriver($idDriver){
		
		$baseQuery	=	"SELECT NEWFINANCESCHEME FROM m_driver
						WHERE IDDRIVER = ".$idDriver."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return 0;
		}
		
		return $row['NEWFINANCESCHEME'];
		
	}
	
	public function getNewFinanceSchemeVendor($idVendor){
		
		$baseQuery	=	"SELECT NEWFINANCESCHEME FROM m_vendor
						WHERE IDVENDOR = ".$idVendor."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row){
			return 0;
		}
		
		return $row['NEWFINANCESCHEME'];
		
	}
	
	public function getCurrencyExchangeByDate($currency, $date){
		
		$query	= $this->db->query("SELECT EXCHANGEVALUE FROM t_currencyexchange
									WHERE CURRENCY = '".$currency."' AND DATESTART < '".$date."'
									ORDER BY DATESTART DESC
									LIMIT 1");
		$row	= $query->row_array();

		if(isset($row)){
			return $row['EXCHANGEVALUE'];
		}
		
		return 1;
		
	}
	
	public function getPartnerCode($idPartnerType, $idPartner){
		
		$baseQuery	=	"";
		if($idPartnerType == 1){
			$baseQuery	=	"SELECT CONCAT('TV', LPAD(IDVENDOR, 4, '0')) AS PARTNERCODE
							FROM m_vendor WHERE IDVENDOR = ".$idPartner."
							LIMIT 1";
		} else {
			$baseQuery	=	"SELECT CONCAT(
										'D',
										CASE IDDRIVERTYPE
											WHEN 1 THEN 'S'
											WHEN 2 THEN 'T'
											ELSE 'C'
										END,
										LPAD(IDDRIVER, 4, '0')
									) AS PARTNERCODE
							FROM m_driver WHERE IDDRIVER = ".$idPartner."
							LIMIT 1";			
		}
		
		$query	=	$this->db->query($baseQuery);
		$row	=	$query->row_array();

		if(isset($row)){
			return $row['PARTNERCODE'];
		}
		
		return "-";

	}
	
	public function calculateScheduleDriverMonitor($dateSchedule, $defaultOffQuotaUpdate = false){
		
		$totalSchedule		=	$this->getTotalSchedulePerDate($dateSchedule);
		$totalDriverDefault	=	$this->getTotalDriverDefault();
		$totalOffDriver		=	$this->getTotalOffDriver($dateSchedule);
		$totalActiveDriver	=	$totalDriverDefault - $totalOffDriver;
		$isDataMonitorExist	=	$this->isDataMonitorExist($dateSchedule);
		$status				=	$totalSchedule <= $totalActiveDriver ? 1 : -1;
		$arrInsUpdMonitor	=	array(
									"DATESCHEDULE"		=>	$dateSchedule,
									"TOTALSCHEDULE"		=>	$totalSchedule,
									"TOTALACTIVEDRIVER"	=>	$totalActiveDriver,
									"TOTALOFFDRIVER"	=>	$totalOffDriver,
									"STATUS"			=>	$status
								);
		if(!$isDataMonitorExist){
			$defaultOffQuota						=	$this->getValueSystemSettingVariable(10);
			$arrInsUpdMonitor['TOTALDAYOFFQUOTA']	=	$defaultOffQuota;
			$this->addData("t_scheduledrivermonitor", $arrInsUpdMonitor);
		} else {
			if($defaultOffQuotaUpdate != false){
				$arrInsUpdMonitor['TOTALDAYOFFQUOTA']	=	$defaultOffQuotaUpdate;
			}
			$this->updateData("t_scheduledrivermonitor", $arrInsUpdMonitor, "IDSCHEDULEDRIVERMONITOR", $isDataMonitorExist['IDSCHEDULEDRIVERMONITOR']);
		}
		
		return true;

	}
	
	private function getTotalSchedulePerDate($dateSchedule){

		$totalSchedule	=	0;
		$baseQuery		=	"SELECT COUNT(IDRESERVATIONDETAILS) AS TOTALSCHEDULE
							FROM t_reservationdetails
							WHERE SCHEDULEDATE = '".$dateSchedule."' AND STATUS = 1 AND IDDRIVERTYPE != 0
							LIMIT 1";
		$query			=	$this->db->query($baseQuery);
		$row			=	$query->row_array();

		if(isset($row)) $totalSchedule	=	$row['TOTALSCHEDULE'];
		return $totalSchedule;

	}
	
	private function getTotalDriverDefault(){

		$totalDriverDefault	=	0;
		$baseQuery			=	"SELECT SUM(DRIVERQUOTA) AS TOTALDEFAULTDRIVER
								FROM m_driver
								WHERE STATUS = 1 AND IDDRIVER NOT IN (9, 49)
								LIMIT 1";
		$query				=	$this->db->query($baseQuery);
		$row				=	$query->row_array();

		if(isset($row)) $totalDriverDefault	=	$row['TOTALDEFAULTDRIVER'];
		return $totalDriverDefault;

	}
	
	private function getTotalOffDriver($dateSchedule){

		$totalOffDriver	=	0;
		$baseQuery		=	"SELECT COUNT(A.IDDAYOFF) AS TOTALOFFDRIVER
							FROM t_dayoff A
							LEFT JOIN m_driver B ON A.IDDRIVER = B.IDDRIVER
							WHERE A.DATEDAYOFF = '".$dateSchedule."' AND (B.PARTNERSHIPTYPE = 1 OR B.PARTNERSHIPTYPE = 4)
							LIMIT 1";
		$query			=	$this->db->query($baseQuery);
		$row			=	$query->row_array();

		if(isset($row)) $totalOffDriver	=	$row['TOTALOFFDRIVER'];
		return $totalOffDriver;

	}
	
	private function isDataMonitorExist($dateSchedule){

		$baseQuery		=	"SELECT IDSCHEDULEDRIVERMONITOR FROM t_scheduledrivermonitor
							WHERE DATESCHEDULE = '".$dateSchedule."'
							LIMIT 1";
		$query			=	$this->db->query($baseQuery);
		$row			=	$query->row_array();

		if(isset($row)) return $row;
		return false;

	}
	
	public function getDataDriverMonitor($date){

		$baseQuery	=	"SELECT TOTALDAYOFFQUOTA, TOTALOFFDRIVER FROM t_scheduledrivermonitor
						WHERE DATESCHEDULE = '".$date."'
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();

		if(isset($row)) return $row;
		return array(
			"TOTALDAYOFFQUOTA"	=>	0,
			"TOTALOFFDRIVER"	=>	0
		);

	}
	
	public function getBSTManualBookingCode(){
		
		$dateBookingCode=	date('ymd');
		$i				=	1;
		
		while(true){
			
			$orderNumber		=	str_pad($i, 3, "0", STR_PAD_LEFT);
			$bookingCode		=	"BST-".$dateBookingCode.$orderNumber;
			$isBookingCodeExist	=	$this->isBookingCodeExist($bookingCode);
			
			if(!$isBookingCodeExist){
				break;
			}
			$i++;
			
		}
		
		return $bookingCode;
		
	}
	
	public function isBookingCodeExist($bookingCode, $idReservation = 0){
		
		$query	= $this->db->query("SELECT IDRESERVATION FROM t_reservation
									WHERE BOOKINGCODE = '".$bookingCode."' AND IDRESERVATION != ".$idReservation."
									LIMIT 1");
		$row	= $query->row_array();

		if(isset($row)){
			return true;
		}
		
		return false;
		
	}
	
	public function getTotalUnconfirmedReservationPartner($idPartnerType, $idPartner){

        $fieldCount =   $idPartnerType == 1 ? "IDSCHEDULEVENDOR" : "IDSCHEDULEDRIVER";
        $tableName  =   $idPartnerType == 1 ? "t_schedulevendor" : "t_scheduledriver";
        $fieldWhere =   $idPartnerType == 1 ? "IDVENDOR" : "IDDRIVER";
		$baseQuery	=	"SELECT COUNT(".$fieldCount.") AS TOTALUNCONFIRMEDRESERVATION
						FROM ".$tableName."
						WHERE STATUSCONFIRM = 0 AND ".$fieldWhere." = ".$idPartner."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();

		if(isset($row)) return $row['TOTALUNCONFIRMEDRESERVATION'];
		return 0;

	}
	
	public function getTotalActiveCollectPayment($idPartnerType, $idPartner){
        $fieldWhere =   $idPartnerType == 1 ? "IDVENDOR" : "IDDRIVER";
		$baseQuery	=	"SELECT COUNT(IDCOLLECTPAYMENT) AS TOTALACTIVECOLLECTPAYMENT
						FROM t_collectpayment
						WHERE STATUS = 0 AND ".$fieldWhere." = ".$idPartner."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();

		if(isset($row)) return $row['TOTALACTIVECOLLECTPAYMENT'];
		return 0;
	}
	
	public function getTotalActiveWithdrawalPartner($idPartnerType, $idPartner){
        $fieldWhere =   $idPartnerType == 1 ? "IDVENDOR" : "IDDRIVER";
		$baseQuery	=	"SELECT COUNT(IDWITHDRAWALRECAP) AS TOTALACTIVEWITHDRAWAL
						FROM t_withdrawalrecap
						WHERE STATUSWITHDRAWAL IN (0,1) AND ".$fieldWhere." = ".$idPartner."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();

		if(isset($row)) return $row['TOTALACTIVEWITHDRAWAL'];
		return 0;
	}
	
	public function getArrIdDriverReviewBonusPeriod(){
		$baseQuery	=	"SELECT GROUP_CONCAT(A.IDDRIVERREVIEWBONUSPERIOD) AS ARRIDDRIVERREVIEWBONUSPERIOD
						FROM t_driverreviewbonusperiod A
						LEFT JOIN (
							SELECT IDDRIVERREVIEWBONUSPERIOD, IFNULL(SUM(IF(IDWITHDRAWALRECAP != 0, 1, 0)), 0) AS TOTALWITDRAWN
							FROM t_driverreviewbonus
							GROUP BY IDDRIVERREVIEWBONUSPERIOD
						) AS B ON A.IDDRIVERREVIEWBONUSPERIOD = B.IDDRIVERREVIEWBONUSPERIOD
						WHERE B.TOTALWITDRAWN = 0
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();

		if(isset($row)) return explode(",", $row['ARRIDDRIVERREVIEWBONUSPERIOD']);
		return [];
	}
	
	public function getDetailBank($idBank){
		$baseQuery	=	"SELECT BANKNAME, CONCAT('".URL_BANK_LOGO."', BANKLOGO) AS BANKLOGO
						FROM m_bank
						WHERE IDBANK = ".$idBank."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();

		if(isset($row)) return $row;
		return [
			"BANKNAME"	=>	"-",
			"BANKLOGO"	=>	URL_BANK_LOGO."default.png"
		];
	}
	
	public function getMaxStatusProcess($idPartnerType){
		$baseQuery	=	"SELECT MAX(IDSTATUSPROCESSVENDOR) AS MAXSTATUSPROCESS
						FROM m_statusprocessvendor
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();

		if(isset($row)) return $row['MAXSTATUSPROCESS'];
		return 1;
	}
	
	public function getDataCountryCode(){
		$query	=	$this->db->query(
						"SELECT IDCOUNTRY, COUNTRYPHONECODE
						FROM ".APP_WHATSAPP_DATABASE_NAME.".m_country
						ORDER BY LENGTH(COUNTRYPHONECODE) DESC"
					);
		$result	=	$query->result();

		if (isset($result)) return $result;
		return false;		
	}
	
	public function getDataNameTitle(){
		$query	=	$this->db->query("SELECT IDNAMETITLE, NAMETITLE, KEYWORDSEARCH FROM ".APP_WHATSAPP_DATABASE_NAME.".m_nametitle");
		$result	=	$query->result();

		if (isset($result)) return $result;
		return false;		
	}
	
	public function getDataSystemSettingAppWhatsapp($idSystemSetting){
		$query	=	$this->db->query(
						"SELECT NAME, DESCRIPTION, DATASETTING
						FROM ".APP_WHATSAPP_DATABASE_NAME.".a_systemsettings
						WHERE IDSYSTEMSETTINGS = ".$idSystemSetting."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if (isset($row)) return $row;
		return false;		
	}
	
	public function getIDUserAdminWhatsapp($idUserAdmin){
		$query	=	$this->db->query(
						"SELECT IDUSERADMIN FROM ".APP_WHATSAPP_DATABASE_NAME.".m_useradmin
						WHERE IDUSERADMININTERNAL = ".$idUserAdmin."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if (isset($row)) return $row['IDUSERADMIN'];
		return 0;		
	}
}