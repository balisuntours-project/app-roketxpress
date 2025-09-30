<?php
class ModelOptionHelper extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
		$ci			=& get_instance();		
	}
	
	public function getDataOptionHelperUserLevel(){
		
		$baseQuery		=	sprintf("SELECT IDUSERLEVEL AS ID, LEVELNAME AS VALUE
									 FROM m_userlevel
									 ORDER BY LEVELNAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperUserPartnerLevel(){
		
		$baseQuery		=	sprintf("SELECT IDUSERLEVELPARTNER AS ID, LEVELNAME AS VALUE
									 FROM m_userlevelpartner
									 ORDER BY LEVELNAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperArea(){
		
		$baseQuery		=	sprintf("SELECT IDAREA AS ID, CONCAT(AREANAME, ' (', AREATAGS, ')') AS VALUE
									 FROM m_area
									 ORDER BY AREANAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperAreaNonTBA(){
		
		$baseQuery		=	sprintf("SELECT IDAREA AS ID, CONCAT(AREANAME, ' (', AREATAGS, ')') AS VALUE
									 FROM m_area
									 WHERE IDAREA != 0
									 ORDER BY AREANAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperReservationType(){
		
		$baseQuery		=	sprintf("SELECT IDRESERVATIONTYPE AS ID, RESERVATIONTYPE AS VALUE
									 FROM m_reservationtype
									 WHERE STATUS = 1
									 ORDER BY URUTAN"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperProductType(){
		
		$baseQuery		=	sprintf("SELECT IDPRODUCTTYPE AS ID, PRODUCTTYPE AS VALUE
									 FROM m_producttype
									 ORDER BY PRODUCTTYPE"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperProductTicket(){
		
		$baseQuery		=	sprintf("SELECT B.IDPRODUCT AS ID, B.PRODUCTNAME AS VALUE
									 FROM m_productdetailtype A
									 LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
									 WHERE A.IDPRODUCTTYPE = 1 AND B.STATUS = 1
									 ORDER BY B.PRODUCTNAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperDriverType(){
		
		$baseQuery		=	sprintf("SELECT IDDRIVERTYPE AS ID, DRIVERTYPE AS VALUE
									 FROM m_drivertype
									 ORDER BY DRIVERTYPE"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperDriver(){
		
		$baseQuery		=	sprintf("SELECT IDDRIVER AS ID, NAME AS VALUE, IDDRIVERTYPE AS PARENTVALUE
									 FROM m_driver
									 ORDER BY IDDRIVERTYPE, NAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperDriverCarRental(){
		
		$baseQuery		=	sprintf("SELECT IDDRIVER AS ID, NAME AS VALUE, IDDRIVERTYPE AS PARENTVALUE
									 FROM m_driver
									 WHERE CARRENTALDRIVERSTATUS = 1
									 ORDER BY IDDRIVERTYPE, NAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperDriverNewFinanceScheme(){
		
		$baseQuery		=	sprintf("SELECT IDDRIVER AS ID, NAME AS VALUE, IDDRIVERTYPE AS PARENTVALUE
									 FROM m_driver
									 WHERE NEWFINANCESCHEME = 1
									 ORDER BY IDDRIVERTYPE, NAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperDriverReview(){
		$baseQuery	=	sprintf(
							"SELECT IDDRIVER AS ID, NAME AS VALUE, IDDRIVERTYPE AS PARENTVALUE
							 FROM m_driver
							 WHERE REVIEWBONUSPUNISHMENT = 1
							 ORDER BY IDDRIVERTYPE, NAME"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return [];
		return $result;
	}
	
	public function getDataOptionHelperCarType(){
		
		$baseQuery		=	sprintf("SELECT IDCARTYPE AS ID, CARTYPE AS VALUE
									 FROM m_cartype
									 ORDER BY CARTYPE"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperCarCapacity(){
		
		$baseQuery		=	sprintf("SELECT IDCARCAPACITY AS ID, CONCAT(CARCAPACITYNAME, ' (', MINCAPACITY, ' - ', MAXCAPACITY, ')') AS VALUE
									 FROM m_carcapacity
									 ORDER BY MINCAPACITY ASC, MAXCAPACITY ASC"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperCarDayOffType(){
		$baseQuery	=	sprintf(
							"SELECT IDCARDAYOFFTYPE AS ID, DAYOFFTYPE AS VALUE, ISNEEDCOST
							FROM m_cardayofftype
							ORDER BY DAYOFFTYPE ASC"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;	
	}
	
	public function getDataOptionHelperCarCostType(){
		$baseQuery	=	sprintf(
							"SELECT IDCARCOSTTYPE AS ID, CARCOSTTYPE AS VALUE
							FROM m_carcosttype
							ORDER BY CARCOSTTYPE ASC"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;	
	}
	
	public function getDataOptionHelperVendorType(){
		
		$baseQuery		=	sprintf("SELECT IDVENDORTYPE AS ID, VENDORTYPE AS VALUE
									 FROM m_vendortype
									 WHERE STATUS = 1
									 ORDER BY VENDORTYPE"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperSource(){
		
		$baseQuery		=	sprintf("SELECT IDSOURCE AS ID, SOURCENAME AS VALUE
									 FROM m_source
									 WHERE STATUS = 1
									 ORDER BY SOURCENAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperSourceAutoRating(){
		
		$baseQuery		=	sprintf("SELECT IDSOURCE AS ID, SOURCENAME AS VALUE
									 FROM m_source
									 WHERE STATUS = 1 AND AUTOINPUTRATING = 1
									 ORDER BY SOURCENAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperSourceAutoPayment(){
		
		$baseQuery		=	sprintf("SELECT IDSOURCE AS ID, SOURCENAME AS VALUE
									 FROM m_source
									 WHERE STATUS = 1 AND AUTOINPUTPAYMENT = 1
									 ORDER BY SOURCENAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperSourceImportOTA(){
		$baseQuery	=	sprintf(
							"SELECT IDSOURCE AS ID, SOURCENAME AS VALUE
							 FROM m_source
							 WHERE STATUS = 1 AND IMPORTOTA = 1
							 ORDER BY SOURCENAME"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;
	}
	
	public function getDataOptionHelperSourceOTA(){
		
		$baseQuery		=	sprintf("SELECT IDSOURCE AS ID, SOURCENAME AS VALUE
									 FROM m_source
									 WHERE STATUS = 1 AND ISOTA = 1
									 ORDER BY SOURCENAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperVendor(){
		
		$baseQuery		=	sprintf("SELECT IDVENDOR AS ID, NAME AS VALUE, IDVENDORTYPE AS PARENTVALUE
									 FROM m_vendor
									 ORDER BY NAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperVendorNewFinanceScheme(){
		
		$baseQuery		=	sprintf("SELECT IDVENDOR AS ID, NAME AS VALUE, IDVENDORTYPE AS PARENTVALUE
									 FROM m_vendor
									 WHERE NEWFINANCESCHEME = 1
									 ORDER BY NAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperVendorCar(){
		
		$baseQuery		=	sprintf("SELECT IDVENDOR AS ID, NAME AS VALUE
									 FROM m_vendor
									 WHERE IDVENDORTYPE = 1
									 ORDER BY NAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperVendorTicket(){
		
		$baseQuery		=	sprintf("SELECT IDVENDOR AS ID, NAME AS VALUE
									 FROM m_vendor
									 WHERE IDVENDORTYPE = 2
									 ORDER BY NAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperVendorAndDriver($status = ''){
		$con_status	=	isset($status) && $status != '' ? 'A.STATUS = '.$status : '1=1';
		$baseQuery	=	sprintf("(SELECT CONCAT('2-', A.IDDRIVER) AS ID, CONCAT('[', B.DRIVERTYPE, ' Driver] ', A.NAME) AS VALUE,
										'Driver' AS PARTNERTYPE, B.DRIVERTYPE AS SUBPARTNERTYPE
								 FROM m_driver A
								 LEFT JOIN m_drivertype B ON A.IDDRIVERTYPE = B.IDDRIVERTYPE
								 WHERE ".$con_status.")
								 UNION ALL
								 (SELECT CONCAT('1-', A.IDVENDORTYPE, '-', A.IDVENDOR) AS ID, CONCAT('[', B.VENDORTYPE, '] ', A.NAME) AS VALUE,
										'Vendor' AS PARTNERTYPE, B.VENDORTYPE AS SUBPARTNERTYPE
								 FROM m_vendor A
								 LEFT JOIN m_vendortype B ON A.IDVENDORTYPE = B.IDVENDORTYPE
								 WHERE ".$con_status.")
								 ORDER BY PARTNERTYPE, SUBPARTNERTYPE, VALUE"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;	
	}
	
	public function getDataOptionHelperPaymentMethod(){
		
		$baseQuery		=	sprintf("SELECT IDPAYMENTMETHOD AS ID, PAYMENTMETHODNAME AS VALUE
									 FROM m_paymentmethod
									 WHERE HIDDEN = 0
									 ORDER BY PAYMENTMETHODNAME"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperRatingPoint(){
		
		$baseQuery		=	sprintf("SELECT RATING, POINT FROM m_driverpoint
									 ORDER BY RATING ASC"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperMessageAdminType(){
		
		$baseQuery		=	sprintf("SELECT IDMESSAGEADMINTYPE AS ID, MESSAGEADMINTYPE AS VALUE
									 FROM m_messageadmintype
									 ORDER BY MESSAGEADMINTYPE"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperLoanType(){
		
		$baseQuery		=	sprintf("SELECT IDLOANTYPE AS ID, LOANTYPE AS VALUE
									 FROM m_loantype
									 ORDER BY LOANTYPE"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperAdditionalCostType(){
		
		$baseQuery		=	sprintf("SELECT IDADDITIONALCOSTTYPE AS ID, ADDITIONALCOSTTYPE AS VALUE
									 FROM m_additionalcosttype
									 ORDER BY ADDITIONALCOSTTYPE"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function getDataOptionHelperBank(){
		$baseQuery	=	sprintf(
							"SELECT IDBANK AS ID, BANKNAME AS VALUE
							FROM m_bank
							ORDER BY ORDERPRIORITY, BANKNAME"
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;	
	}
	
	public function getDataOptionHelperPartner(){
		
		$baseQuery		=	sprintf("SELECT ID, VALUE
									 FROM (
										 SELECT 1 AS IDPARTNERTYPE, CONCAT('1-', IDVENDOR) AS ID, CONCAT('[Vendor] ', NAME) AS VALUE
										 FROM m_vendor
										 WHERE NEWFINANCESCHEME = 1 AND STATUS = 1
										 UNION ALL
										 SELECT 2 AS IDPARTNERTYPE, CONCAT('2-', IDDRIVER) AS ID, CONCAT('[Driver] ', NAME) AS VALUE
										 FROM m_driver
										 WHERE NEWFINANCESCHEME = 1 AND PARTNERSHIPTYPE = 3 AND STATUS = 1
									 ) AS A
									 ORDER BY IDPARTNERTYPE, VALUE"
									);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
}