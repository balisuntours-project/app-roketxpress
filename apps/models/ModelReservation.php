<?php
class ModelReservation extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
		
	public function getProductSelfDriveVendor(){
		$baseQuery	=	"SELECT A.IDVENDOR, A.IDCARTYPE, CONCAT(A.IDVENDOR, '-', A.IDCARTYPE) AS VALUE,
								CONCAT('[', C.NAME, '] ', B.CARTYPE) AS OPTIONTEXT, C.NAME AS VENDORNAME,
								B.CARTYPE AS PRODUCTNAME
						 FROM t_carselfdrivefee A
						 LEFT JOIN m_cartype B ON A.IDCARTYPE = B.IDCARTYPE
						 LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
						 GROUP BY A.IDVENDOR, A.IDCARTYPE
						 ORDER BY C.NAME, B.CARTYPE";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;
	}
		
	public function getProductSelfDriveDuration(){
		$baseQuery	=	"SELECT IDCARSELFDRIVEFEE, IDVENDOR, IDCARTYPE, DURATION, CONCAT(DURATION, ' Hours') AS OPTIONTEXT, NOMINALFEE, NOTES
						 FROM t_carselfdrivefee";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;	
	}
		
	public function getProductTicketVendor($idVendor = false){
		$con_vendor	=	$idVendor && $idVendor != 0 && $idVendor != '' ? "A.IDVENDOR = ".$idVendor : "1=1";
		$baseQuery	=	"SELECT A.IDVENDOR AS VALUE, A.VOUCHERSTATUS, A.PRICEADULT, A.PRICECHILD, A.PRICEINFANT, A.NOTES, C.NAME AS VENDORNAME,
								CASE
								WHEN A.MINPAX = A.MAXPAX THEN CONCAT('[', C.NAME, '] ', B.PRODUCTNAME, ' (Must ', A.MINPAX, ' pax)')
								WHEN A.MINPAX = 1 AND A.MAXPAX != 999 THEN CONCAT('[', C.NAME, '] ', B.PRODUCTNAME, ' (Max. ', A.MAXPAX, ' Pax)')
								WHEN A.MINPAX != 1 AND A.MAXPAX = 999 THEN CONCAT('[', C.NAME, '] ', B.PRODUCTNAME, ' (Min. ', A.MINPAX, ' Pax)')
								WHEN A.MINPAX != 1 AND A.MAXPAX != 999 THEN CONCAT('[', C.NAME, '] ', B.PRODUCTNAME, ' (', A.MINPAX, '-', A.MAXPAX, ' pax)')
								ELSE CONCAT('[', C.NAME, '] ', B.PRODUCTNAME)
								END AS OPTIONTEXT,
								B.PRODUCTNAME
						 FROM t_vendorticketprice A
						 LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
						 LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
						 WHERE B.STATUS = 1 AND ".$con_vendor."
						 ORDER BY C.NAME, B.PRODUCTNAME, A.MINPAX";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;	
	}
		
	public function getProductTransportVendor(){
		$baseQuery	=	"SELECT A.IDDRIVERFEE AS VALUE, CONCAT('[', C.DRIVERTYPE, '] ', IF(A.IDSOURCE = 0, '', CONCAT(' [', E.SOURCENAME, '] ')), B.PRODUCTNAME) AS OPTIONTEXT,
								A.FEENOMINAL, A.ADDITIONALINFO, C.DRIVERTYPE AS VENDORNAME, CONCAT(IF(A.IDSOURCE = 0, '', CONCAT('[', E.SOURCENAME, '] ')), B.PRODUCTNAME) AS PRODUCTNAME,
								D.IDPRODUCTTYPE, A.IDDRIVERTYPE, A.SCHEDULETYPE, A.JOBTYPE, A.JOBRATE, A.PRODUCTRANK, A.COSTTICKETTYPE, A.COSTPARKINGTYPE, A.COSTMINERALWATERTYPE,
								A.COSTBREAKFASTTYPE, A.COSTLUNCHTYPE, A.BONUSTYPE, A.COSTTICKET, A.COSTPARKING, A.COSTMINERALWATER, A.COSTBREAKFAST, A.COSTLUNCH, A.BONUS,
								IF(A.IDSOURCE = 0, 0, 1) AS STATUSSOURCE
						 FROM t_driverfee A
						 LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
						 LEFT JOIN m_drivertype C ON A.IDDRIVERTYPE = C.IDDRIVERTYPE
						 LEFT JOIN m_productdetailtype D ON B.IDPRODUCT = D.IDPRODUCT
						 LEFT JOIN m_source E ON A.IDSOURCE = E.IDSOURCE
						 WHERE (A.FEENOMINAL > 0 OR A.OWNGUEST = 1) AND B.STATUS = 1
						 GROUP BY A.IDDRIVERFEE
						 ORDER BY C.DRIVERTYPE, STATUSSOURCE, B.PRODUCTNAME";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;	
	}
	
	public function getStrArrIdReservationByIdVendorCar($idVendorCar, $startDate, $endDate){
		$baseQuery	=	"SELECT GROUP_CONCAT(A.IDRESERVATION SEPARATOR ',') AS STRARRIDRESERVATION
						 FROM t_reservationdetails A
						 LEFT JOIN t_carvendor B ON A.IDVENDOR = B.IDCARVENDOR
						 WHERE A.IDVENDOR = ".$idVendorCar." AND A.SCHEDULEDATE BETWEEN '".$startDate."' AND '".$endDate."'
						 GROUP BY A.IDVENDOR";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return "0";
		return $row['STRARRIDRESERVATION'];	
	}
	
	public function getStrArrIdReservationByIdVendorTicket($idVendorTicket, $startDate, $endDate){
		$baseQuery	=	"SELECT GROUP_CONCAT(IDRESERVATION SEPARATOR ',') AS STRARRIDRESERVATION
						 FROM t_reservationdetails
						 WHERE IDVENDOR = ".$idVendorTicket." AND SCHEDULEDATE BETWEEN '".$startDate."' AND '".$endDate."'
						 GROUP BY IDVENDOR";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return "0";
		return $row['STRARRIDRESERVATION'];	
	}
	
	public function getStrArrIdReservationByIdDriver($idDriver, $startDate, $endDate){
		$baseQuery	=	"SELECT GROUP_CONCAT(B.IDRESERVATION SEPARATOR ',') AS STRARRIDRESERVATION
						 FROM t_scheduledriver A
						 LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						 WHERE A.IDDRIVER = ".$idDriver." AND B.SCHEDULEDATE BETWEEN '".$startDate."' AND '".$endDate."'
						 GROUP BY A.IDDRIVER";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return "0";
		return $row['STRARRIDRESERVATION'];	
	}
		
	public function getDataReservation($page, $dataPerPage, $idReservation, $arrDates, $status, $idSource, $bookingCode, $customerName, $locationName, $startDate, $endDate, $strArrIdReservation, $transportStatus, $reservationTitle, $orderBy, $orderType, $idPartner, $idVendorType, $collectPaymentStatus, $year, $idReservationType){
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$dataPerPage		=	!isset($dataPerPage) || $dataPerPage == 0 ? 25 : $dataPerPage;
		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_or_date		=	"";
		$con_idReservation	=	$con_reservationType = $con_status = $con_source = $con_bookingCode = $con_customerName = $con_date = $con_location = $con_transportStatus = $con_reservationTitle = $con_idReservationIn = "1=1";
		$having_collectPaymentStatus	=	"";
		$order_query		=	$orderBy == "1" ? "A.IDRESERVATION" : "A.RESERVATIONDATESTART ".$orderType.", A.RESERVATIONTIMESTART";

		if($idReservation == 0){
			$con_status				=	!isset($status) || $status == "" ? "1=1" : "A.STATUS = ".$status;
			$con_status				=	isset($status) && $status == "-4" ? "A.STATUS >= 0" : $con_status;
			$con_source				=	!isset($idSource) || $idSource == "" ? "1=1" : "A.IDSOURCE = ".$idSource;
			$con_bookingCode		=	!isset($bookingCode) || $bookingCode == "" ? "1=1" : "A.BOOKINGCODE LIKE '%".$bookingCode."%'";
			$con_customerName		=	!isset($customerName) || $customerName == "" ? "1=1" : "A.CUSTOMERNAME LIKE '%".$customerName."%'";
			$con_transportStatus	=	!isset($transportStatus) || $transportStatus == "" ? "1=1" : ($transportStatus == -1 ? "A.IDAREA = '".$transportStatus."'" : "A.IDAREA != -1");
			$con_reservationTitle	=	!isset($reservationTitle) || $reservationTitle == "" ? "1=1" : "A.RESERVATIONTITLE LIKE '%".$reservationTitle."%'";
			$con_location			=	!isset($locationName) || $locationName == "" ? "1=1" : "(A.HOTELNAME LIKE '%".$locationName."%' OR A.PICKUPLOCATION LIKE '%".$locationName."%' OR A.DROPOFFLOCATION LIKE '%".$locationName."%' )";
			$con_idReservationIn	=	!isset($strArrIdReservation) || $strArrIdReservation == "" ? "1=1" : "A.IDRESERVATION IN (".$strArrIdReservation.")";
			$con_reservationType	=	!isset($idReservationType) || $idReservationType == ""  || $idReservationType == "0" ? "1=1" : "A.IDRESERVATIONTYPE = ".$idReservationType;

			if(count($arrDates) > 0){
				foreach($arrDates as $date){
					$con_or_date	.=	" OR '".$date."' BETWEEN A.RESERVATIONDATESTART AND A.RESERVATIONDATEEND";
				}
				
				$con_date			=	"(
										   A.RESERVATIONDATESTART BETWEEN '".$startDate."' AND '".$endDate."' OR
										   A.RESERVATIONDATEEND BETWEEN '".$startDate."' AND '".$endDate."'
										   ".$con_or_date."
										  )";
			}
			
			if(isset($collectPaymentStatus) && $collectPaymentStatus != ""){
				$having_collectPaymentStatus	=	$collectPaymentStatus == "1" ? "HAVING COUNT(E.IDCOLLECTPAYMENT) > 0" : "HAVING COUNT(E.IDCOLLECTPAYMENT) = 0";
			}
			
		} else {
			$strArrIdReservationIn	=	is_array($idReservation) ? implode(",", $idReservation) : $idReservation;
			$con_idReservation		=	"A.IDRESERVATION IN (".$strArrIdReservationIn.")";
		}
		
		$con_mergeTitlePartner		=	$con_reservationTitle." AND ".$con_idReservationIn;
		if($idPartner == "" || $idPartner == 0){
			if(isset($reservationTitle) && $reservationTitle != ""){
				$idVendorTicket				=	$this->getIdVendorTicketByName($reservationTitle);
				if($idVendorTicket){
					$strArrIdReservation	=	$this->getStrArrIdReservationByIdVendorTicket($idVendorTicket, $startDate, $endDate);
					$con_orIdReservationIn	=	$strArrIdReservation != "" ? "OR A.IDRESERVATION IN (".$strArrIdReservation.")" : "";
					$con_mergeTitlePartner	=	"(A.RESERVATIONTITLE LIKE '%".$reservationTitle."%' ".$con_orIdReservationIn.")";
				}
			}
		} else if(!isset($reservationTitle) || $reservationTitle == "") {
			if($idPartner != "" && $idPartner != 0 && $idVendorType == 2){
				$expIdPartner				=	explode("-", $idPartner);
				$idVendorTicket				=	$expIdPartner[2];
				$vendorName					=	$ci->MainOperation->getVendorNameById($idVendorTicket);
				$con_orIdReservationIn		=	$strArrIdReservation != "" ? "OR A.IDRESERVATION IN (".$strArrIdReservation.")" : "";
				$con_mergeTitlePartner		=	"(A.RESERVATIONTITLE LIKE '%".$vendorName."%' ".$con_orIdReservationIn.")";
			}
		}
		
		$baseQuery	=	"SELECT C.RESERVATIONTYPE, B.SOURCENAME, A.INPUTTYPE, A.RESERVATIONTITLE, A.DURATIONOFDAY, DATE_FORMAT(A.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
								DATE_FORMAT(A.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND, LEFT(A.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART,
								LEFT(A.RESERVATIONTIMEEND, 5) AS RESERVATIONTIMEEND, A.CUSTOMERNAME, A.CUSTOMERCONTACT, A.CUSTOMEREMAIL, A.HOTELNAME, A.PICKUPLOCATION, A.DROPOFFLOCATION,
								IF(A.IDAREA = -1, 'Without Transfer', IFNULL(D.AREANAME, '-')) AS AREANAME, A.NUMBEROFADULT, A.NUMBEROFCHILD, A.NUMBEROFINFANT, A.BOOKINGCODE,
								A.INCOMEAMOUNTCURRENCY, A.INCOMEAMOUNT, A.REMARK, A.TOURPLAN, A.SPECIALREQUEST, A.STATUS, A.IDRESERVATION,  A.IDRESERVATIONTYPE, '' AS PARTNERHANDLE,
								A.USERINPUT, DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT, A.USERLASTUPDATE, IFNULL(DATE_FORMAT(A.DATETIMELASTUPDATE, '%d %b %Y %H:%i'), '-') AS DATETIMELASTUPDATE,
								A.STATUSDRIVER, A.STATUSTICKET, A.STATUSCAR, 0 AS TOTALVOUCHERSTATUS, 0 AS TOTALVOUCHER, IF(COUNT(E.IDCOLLECTPAYMENT) > 0, 1, 0) AS STATUSINCLUDECOLLECT,
								A.REFUNDTYPE, A.RESERVATIONDATESTART AS RESERVATIONDATESTARTDB, A.ISSELFDRIVE
						FROM t_reservation PARTITION (p_".$year.") A
						LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
						LEFT JOIN m_reservationtype C ON A.IDRESERVATIONTYPE = C.IDRESERVATIONTYPE
						LEFT JOIN m_area D ON A.IDAREA = D.IDAREA
						LEFT JOIN t_collectpayment E ON A.IDRESERVATION = E.IDRESERVATION
						WHERE ".$con_idReservation." AND ".$con_reservationType." AND ".$con_status." AND ".$con_source." AND
							  ".$con_bookingCode." AND ".$con_customerName." AND ".$con_date." AND ".$con_location."  AND
							  ".$con_transportStatus." AND ".$con_mergeTitlePartner." 
						GROUP BY A.IDRESERVATION
						".$having_collectPaymentStatus."
						ORDER BY ".$order_query." ".$orderType;
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDRESERVATION", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();		
	}
	
	public function getReservationHandleDriver($idReservation){
		$baseQuery	=	sprintf(
							"SELECT CONCAT(D.DRIVERTYPE, ' Driver') AS PARTNERTYPE, C.NAME AS PARTNERNAME, DATE_FORMAT(A.SCHEDULEDATE, '%s') AS SCHEDULEDATE, A.NOMINAL, 
									B.DRIVERPHONENUMBER, B.CARBRANDMODEL, B.CARNUMBERPLATE
							 FROM t_reservationdetails A
							 LEFT JOIN t_scheduledriver B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
							 LEFT JOIN m_driver C ON B.IDDRIVER = C.IDDRIVER
							 LEFT JOIN m_drivertype D ON C.IDDRIVERTYPE = D.IDDRIVERTYPE
							 WHERE A.IDRESERVATION = ".$idReservation." AND B.IDSCHEDULEDRIVER IS NOT NULL AND A.STATUS = 1
							 ORDER BY D.DRIVERTYPE, C.NAME"
							, '%d %b %Y'
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;	
	}
	
	public function getReservationHandleVendorCar($idReservation){
		$baseQuery	=	sprintf(
							"SELECT 'Car Vendor' AS PARTNERTYPE, D.NAME AS PARTNERNAME,
									DATE_FORMAT(A.SCHEDULEDATE, '%s') AS SCHEDULEDATE, A.NOMINAL
							 FROM t_reservationdetails A
							 LEFT JOIN t_schedulecar B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
							 LEFT JOIN t_carvendor C ON B.IDCARVENDOR = C.IDCARVENDOR
							 LEFT JOIN m_vendor D ON A.IDVENDOR = D.IDVENDOR
							 WHERE A.IDRESERVATION = ".$idReservation." AND A.STATUS = 1 AND A.IDCARTYPE != 0
							 ORDER BY D.NAME, C.MODEL"
							, '%d %b %Y'
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;
	}
	
	public function getReservationHandleVendorTicket($idReservation){
		$baseQuery	=	sprintf(
							"SELECT 'Ticket Vendor' AS PARTNERTYPE, B.NAME AS PARTNERNAME, B.ADDRESS, DATE_FORMAT(A.SCHEDULEDATE, '%s') AS SCHEDULEDATE, A.NOMINAL, A.VOUCHERSTATUS
							 FROM t_reservationdetails A
							 LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
							 LEFT JOIN m_vendortype C ON B.IDVENDORTYPE = C.IDVENDORTYPE
							 WHERE A.IDRESERVATION = ".$idReservation." AND A.IDVENDOR IS NOT NULL AND
								   A.IDVENDOR != 0 AND C.IDVENDORTYPE = 2 AND A.STATUS = 1
							 ORDER BY B.NAME"
							, '%d %b %Y'
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;	
	}
	
	public function getDetailReservation($idReservation){
		$query	=	$this->db->query(
						"SELECT A.IDRESERVATIONTYPE, A.IDSOURCE, A.RESERVATIONTITLE, DATE_FORMAT(A.RESERVATIONDATESTART, '%d-%m-%Y') AS RESERVATIONDATESTART,
								DATE_FORMAT(A.RESERVATIONDATEEND, '%d-%m-%Y') AS RESERVATIONDATEEND, SUBSTRING(A.RESERVATIONTIMESTART, 1, 2) AS RESERVATIONHOUR,
								SUBSTRING(A.RESERVATIONTIMEEND, 1, 2) AS RESERVATIONHOUREND, SUBSTRING(A.RESERVATIONTIMESTART, 4, 2) AS RESERVATIONMINUTE,
								SUBSTRING(A.RESERVATIONTIMEEND, 4, 2) AS RESERVATIONMINUTEEND, A.DURATIONOFDAY, A.CUSTOMERNAME, A.CUSTOMERCONTACT, A.CUSTOMEREMAIL,
								A.IDAREA, A.HOTELNAME, A.PICKUPLOCATION, A.DROPOFFLOCATION, B.SOURCENAME, A.NUMBEROFADULT, A.NUMBEROFCHILD, A.NUMBEROFINFANT, A.BOOKINGCODE,
								A.INCOMEAMOUNTCURRENCY, A.RESERVATIONDATESTART AS RESERVATIONDATEVALUE, SUBSTRING_INDEX(SUBSTRING_INDEX(A.INCOMEAMOUNT, '.', 1), '.', -1) AS INCOMEAMOUNTINTEGER,
								SUBSTRING_INDEX(SUBSTRING_INDEX(A.INCOMEAMOUNT, '.', 2), '.', -1) AS INCOMEAMOUNTDECIMAL, A.INCOMEEXCHANGECURRENCY, A.INCOMEAMOUNTIDR,
								A.SPECIALREQUEST, A.REMARK, A.TOURPLAN, A.SPECIALREQUEST, A.ADDITIONALINFOLIST, A.URLDETAILPRODUCT, A.URLPICKUPLOCATION, A.STATUS,
								A.REFUNDTYPE, A.ISSELFDRIVE
						FROM t_reservation A
						LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
						WHERE IDRESERVATION = '".$idReservation."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function getDetailStrReservation($idReservation){
		$query	=	$this->db->query(
						"SELECT C.RESERVATIONTYPE, B.SOURCENAME, A.RESERVATIONTITLE, A.DURATIONOFDAY,
								DATE_FORMAT(A.RESERVATIONDATESTART, '%a, %d %b %Y') AS RESERVATIONDATESTART, DATE_FORMAT(A.RESERVATIONDATEEND, '%a, %d %b %Y') AS RESERVATIONDATEEND,
								LEFT(A.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART, LEFT(A.RESERVATIONTIMEEND, 5) AS RESERVATIONTIMEEND,
								A.CUSTOMERNAME, A.CUSTOMERCONTACT, A.CUSTOMEREMAIL, A.HOTELNAME, IFNULL(A.PICKUPLOCATION, '-') AS PICKUPLOCATION,
								IFNULL(A.DROPOFFLOCATION, '-') AS DROPOFFLOCATION, A.NUMBEROFADULT, A.NUMBEROFCHILD, A.NUMBEROFINFANT, A.BOOKINGCODE,
								A.INCOMEAMOUNTCURRENCY, A.INCOMEAMOUNT, A.INCOMEEXCHANGECURRENCY, A.INCOMEAMOUNTIDR, A.REMARK, A.TOURPLAN,
								A.IDAREA, IF(A.IDAREA = -1, 'Without Transfer', IFNULL(CONCAT(D.AREANAME, ' (', D.AREATAGS, ')'), '-')) AS AREANAME,
								A.RESERVATIONDATESTART AS RESERVATIONDATEVALUE, A.RESERVATIONDATEEND AS RESERVATIONDATEENDVALUE, A.IDRESERVATIONTYPE, A.USERINPUT,
								DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT, A.USERLASTUPDATE, IFNULL(DATE_FORMAT(A.DATETIMELASTUPDATE, '%d %b %Y %H:%i'), '-') AS DATETIMELASTUPDATE,
								A.SPECIALREQUEST, A.ADDITIONALINFOLIST, IFNULL(COUNT(E.IDRESERVATION), 0) AS TOTALRECONFIRMATION, A.IDSOURCE, A.ISSELFDRIVE
						FROM t_reservation A
						LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
						LEFT JOIN m_reservationtype C ON A.IDRESERVATIONTYPE = C.IDRESERVATIONTYPE
						LEFT JOIN m_area D ON A.IDAREA = D.IDAREA
						LEFT JOIN t_reservationreconfirmation E ON A.IDRESERVATION = E.IDRESERVATION AND E.STATUS >= 0
						WHERE A.IDRESERVATION = '".$idReservation."'
						GROUP BY A.IDRESERVATION
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;
	}
	
	public function getIdReservationDetails($idReservation, $idProductType, $idVendor, $idDriverType, $idCarType, $durationSelfDrive, $dateSchedule){
		$query	=	$this->db->query(
						"SELECT IDRESERVATIONDETAILS FROM t_reservationdetails
						WHERE IDRESERVATION = '".$idReservation."' AND IDPRODUCTTYPE = ".$idProductType." AND
							  IDVENDOR = ".$idVendor." AND IDDRIVERTYPE = ".$idDriverType." AND IDCARTYPE = ".$idCarType." AND
							  DURATION = ".$durationSelfDrive." AND SCHEDULEDATE = '".$dateSchedule."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['IDRESERVATIONDETAILS'];
		return false;
	}
	
	public function getListReservationDetails($idReservation){
		$query	=	$this->db->query(
						"SELECT A.IDRESERVATIONDETAILS, A.IDPRODUCTTYPE, B.PRODUCTTYPE, C.NAME AS VENDORNAME, A.VOUCHERSTATUS, 
								IFNULL(CONCAT('Driver ', D.DRIVERTYPE), '') AS DRIVERTYPE, E.CARTYPE, A.DURATION, A.PRODUCTNAME,
								A.NOMINAL, A.NOTES, DATE_FORMAT(A.SCHEDULEDATE, '%d %b %Y') AS DATESCHEDULE,
								A.USERINPUT, DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i:%s') AS DATETIMEINPUT,
								A.IDVENDOR, A.VOUCHERSTATUS, A.IDDRIVERTYPE
						FROM t_reservationdetails A
						LEFT JOIN m_producttype B ON A.IDPRODUCTTYPE = B.IDPRODUCTTYPE
						LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
						LEFT JOIN m_drivertype D ON A.IDDRIVERTYPE = D.IDDRIVERTYPE
						LEFT JOIN m_cartype E ON A.IDCARTYPE = E.IDCARTYPE
						WHERE A.IDRESERVATION = '".$idReservation."' AND A.STATUS = 1
						ORDER BY A.SCHEDULEDATE, B.PRODUCTTYPE"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return array();
	}
	
	public function isReservationDetailExist($idReservation, $idProductType, $idVendor, $idDriverType, $idCarType, $durationSelfDrive, $dateSchedule, $productName){
		$query	=	$this->db->query(
						"SELECT F.CUSTOMERNAME, F.RESERVATIONTITLE, DATE_FORMAT(F.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
								B.PRODUCTTYPE, A.PRODUCTNAME, C.NAME AS VENDORNAME, CONCAT('Driver ', D.DRIVERTYPE) AS DRIVERTYPE,
								E.CARTYPE, A.DURATION, A.PRODUCTNAME, A.STATUS
						FROM t_reservationdetails A
						LEFT JOIN m_producttype B ON A.IDPRODUCTTYPE = B.IDPRODUCTTYPE
						LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
						LEFT JOIN m_drivertype D ON A.IDDRIVERTYPE = D.IDDRIVERTYPE
						LEFT JOIN m_cartype E ON A.IDCARTYPE = E.IDCARTYPE
						LEFT JOIN t_reservation F ON A.IDRESERVATION = F.IDRESERVATION
						WHERE A.IDRESERVATION = '".$idReservation."' AND A.IDPRODUCTTYPE = ".$idProductType." AND A.IDVENDOR = ".$idVendor." AND
							  A.IDDRIVERTYPE = ".$idDriverType." AND A.IDCARTYPE = ".$idCarType." AND A.DURATION = ".$durationSelfDrive." AND
							  A.PRODUCTNAME = '".$productName."' AND A.SCHEDULEDATE = '".$dateSchedule."' AND A.STATUS = 1
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row))	return $row;
		return false;		
	}
	
	public function getReservationDetailsTicket($idReservationDetails){
		$query	=	$this->db->query(
						"SELECT B.IDVENDOR, C.SOURCENAME, A.BOOKINGCODE, A.RESERVATIONTITLE, A.CUSTOMERNAME, A.CUSTOMERCONTACT, A.CUSTOMEREMAIL,
								A.NUMBEROFADULT, A.NUMBEROFCHILD, A.NUMBEROFINFANT, DATE_FORMAT(B.SCHEDULEDATE, '%d %M %Y') AS SCHEDULEDATE, B.PRODUCTNAME,
								B.NOMINAL, B.NOTES, IFNULL(D.CORRECTIONNOTES, '') AS CORRECTIONNOTES
						FROM t_reservation A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN m_source C ON A.IDSOURCE = C.IDSOURCE
						LEFT JOIN t_fee D ON B.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS
						WHERE B.IDVENDOR != 0 AND B.IDRESERVATIONDETAILS = ".$idReservationDetails."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function getDetailReservationTicket($idReservationDetails){
		$query	=	$this->db->query(
						"SELECT PAXADULT, PAXCHILD, PAXINFANT, PRICEPERPAXADULT, PRICEPERPAXCHILD, PRICEPERPAXINFANT, PRICETOTALADULT, PRICETOTALCHILD, PRICETOTALINFANT
						FROM t_reservationdetailsticket
						WHERE IDRESERVATIONDETAILS = ".$idReservationDetails."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return array(
			"PAXADULT"			=>	0,
			"PAXCHILD"			=>	0,
			"PAXINFANT"			=>	0,
			"PRICEPERPAXADULT"	=>	0,
			"PRICEPERPAXCHILD"	=>	0,
			"PRICEPERPAXINFANT"	=>	0,
			"PRICETOTALADULT"	=>	0,
			"PRICETOTALCHILD"	=>	0,
			"PRICETOTALINFANT"	=>	0
		);
	}
	
	public function getReservationDetailsTransport($idReservationDetails){
		$query	=	$this->db->query(
						"SELECT A.PRODUCTNAME, A.NOMINAL, A.NOTES, B.CORRECTIONNOTES FROM t_reservationdetails A
						LEFT JOIN t_fee B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						WHERE A.IDRESERVATIONDETAILS = ".$idReservationDetails."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function getDetailPayment($idReservation){
		$query	=	$this->db->query(
						"SELECT IDRESERVATIONPAYMENT, DESCRIPTION FROM t_reservationpayment
						WHERE IDRESERVATION = '".$idReservation."' AND IDPAYMENTMETHOD = 1
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function getTotalReservationDetails($idReservation){
		$query	=	$this->db->query(
						"SELECT COUNT(IDRESERVATIONDETAILS) AS TOTALDETAILS,
							   SUM(IF(IDPRODUCTTYPE = 1, 1, 0)) AS TOTALTICKET,
							   SUM(IF(IDPRODUCTTYPE = 2, 1, 0)) AS TOTALDRIVER,
							   SUM(IF(IDPRODUCTTYPE = 3, 1, 0)) AS TOTALCAR
						FROM t_reservationdetails
						WHERE IDRESERVATION = '".$idReservation."' AND STATUS = 1
						GROUP BY IDRESERVATION"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return array(
			"TOTALDETAILS"	=>	0,
			"TOTALDRIVER"	=>	0,
			"TOTALTICKET"	=>	0,
			"TOTALCAR"		=>	0
		);
	}
	
	public function isDriverScheduleExists($idReservationDetails){
		$query	=	$this->db->query(
						"SELECT IDSCHEDULEDRIVER FROM t_scheduledriver
						WHERE IDRESERVATIONDETAILS = '".$idReservationDetails."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return true;
		return false;		
	}
	
	public function isCarScheduleExists($idReservationDetails){
		$query	=	$this->db->query(
						"SELECT IDSCHEDULECAR FROM t_schedulecar
						WHERE IDRESERVATIONDETAILS = '".$idReservationDetails."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row))	return true;
		return false;		
	}

	public function getTotalDetailsTicketReservation($idReservation){
		$query	=	$this->db->query(
						"SELECT SUM(IF(IDPRODUCTTYPE = 1, 1, 0)) AS TOTALTICKET
						FROM t_reservationdetails
						WHERE IDRESERVATION = '".$idReservation."' AND STATUS = 1
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['TOTALTICKET'];
		return 0;		
	}
	
	public function getTotalUnscheduleDriver($idReservation){
		$query	=	$this->db->query(
						"SELECT COUNT(A.IDRESERVATIONDETAILS) TOTALUNSCHEDULEDRIVER FROM t_reservationdetails A
						LEFT JOIN t_scheduledriver B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						WHERE A.IDRESERVATION = '".$idReservation."' AND A.STATUS = 1 AND A.IDPRODUCTTYPE = 2 AND
							  B.IDRESERVATIONDETAILS IS NULL
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['TOTALUNSCHEDULEDRIVER'];
		return 0;		
	}
	
	public function getDataDateReservation($idReservation){
		$query	=	$this->db->query(
						"SELECT A.RESERVATIONDATESTART, A.RESERVATIONDATEEND, COUNT(B.IDRESERVATIONDETAILS) AS TOTALDETAILS, A.IDAREA
						FROM t_reservation A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATION = B.IDRESERVATION AND B.STATUS = 1
						WHERE A.IDRESERVATION = ".$idReservation."
						GROUP BY A.IDRESERVATION
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return array(
			"RESERVATIONDATESTART"	=> "",
			"RESERVATIONDATEEND"	=> "",
			"TOTALDETAILS"			=> 0,
			"IDAREA"				=> 0
		);		
	}
	
	public function getDetailReservationCheck($idReservation){
		$query	=	$this->db->query(
						"SELECT A.DURATIONOFDAY, (A.NUMBEROFADULT + A.NUMBEROFCHILD + A.NUMBEROFINFANT) AS TOTALPAX,
							   A.SPECIALREQUEST, B.UPSELLINGTYPE
						FROM t_reservation A
						LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
						WHERE A.IDRESERVATION = ".$idReservation."
						GROUP BY A.IDRESERVATION
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function getIdAutoDetailsTemplate($reservationTitle){
		$query	=	$this->db->query(
						"SELECT IDAUTODETAILSTEMPLATE FROM t_autodetailstitlekeyword
						WHERE TITLEKEYWORD = '".$reservationTitle."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row))	return $row['IDAUTODETAILSTEMPLATE'];
		return false;		
	}
	
	public function getItemAutoDetailsTemplate($idAutoDetailsTemplate){
		$query	=	$this->db->query(
						"SELECT IDPRODUCTTYPE, IDPRODUCTFEE
						FROM t_autodetailstemplateitem
						WHERE IDAUTODETAILSTEMPLATE = '".$idAutoDetailsTemplate."'
						ORDER BY IDPRODUCTTYPE"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return false;		
	}
	
	public function getDetailProductTransportVendor($idDriverFee){
		$baseQuery	=	sprintf(
							"SELECT CONCAT(IF(A.IDSOURCE = 0, '', CONCAT('[', D.SOURCENAME, '] ')), B.PRODUCTNAME) AS PRODUCTNAME, A.IDDRIVERTYPE, A.IDSOURCE,
									A.FEENOMINAL, A.COSTTICKETTYPE, A.COSTPARKINGTYPE, A.COSTMINERALWATERTYPE, A.IDAREA, A.COSTBREAKFASTTYPE, A.COSTLUNCHTYPE,
									A.BONUSTYPE, A.COSTTICKET, A.COSTPARKING, A.COSTMINERALWATER, A.COSTBREAKFAST, A.COSTLUNCH, A.BONUS, A.ADDITIONALINFO,
									A.SCHEDULETYPE, A.JOBTYPE, A.JOBRATE, A.PRODUCTRANK, C.DRIVERTYPE AS VENDORNAME
							 FROM t_driverfee A
							 LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
							 LEFT JOIN m_drivertype C ON A.IDDRIVERTYPE = C.IDDRIVERTYPE
							 LEFT JOIN m_source D ON A.IDSOURCE = D.IDSOURCE
							 WHERE A.FEENOMINAL > 0 AND B.STATUS = 1 AND A.IDDRIVERFEE = ".$idDriverFee."
							 LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;	
	}
	
	public function getDetailProductTicketVendor($idVendorTicketPrice, $totalPax){
		$baseQuery	=	sprintf(
							"SELECT A.IDVENDOR, B.PRODUCTNAME, C.NAME AS VENDORNAME, A.VOUCHERSTATUS, A.MINPAX, A.MAXPAX, A.PRICEADULT,
									A.PRICECHILD, A.PRICEINFANT, A.NOTES
							 FROM t_vendorticketprice A
							 LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
							 LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
							 WHERE B.STATUS = 1 AND A.IDVENDORTICKETPRICE = ".$idVendorTicketPrice." AND
								   ".$totalPax." BETWEEN A.MINPAX AND A.MAXPAX
							 LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;	
	}
	
	private function getIdVendorTicketByName($strNameVendor){
		$baseQuery	=	sprintf(
							"SELECT IDVENDOR FROM m_vendor
							 WHERE IDVENDORTYPE = 2 AND NAME = '".$strNameVendor."'
							 LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row['IDVENDOR'];	
	}
	
	public function getTotalReservationVoucher($idReservation){
		$query	=	$this->db->query(
						"SELECT COUNT(IDRESERVATIONVOUCHER) AS TOTALVOUCHER
						FROM t_reservationvoucher
						WHERE IDRESERVATION = '".$idReservation."' AND STATUS = 1
						GROUP BY IDRESERVATION
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['TOTALVOUCHER'];
		return 0;		
	}

	public function getListVoucherReservation($idReservation){
		$query	=	$this->db->query(
						"SELECT A.IDRESERVATIONVOUCHER, B.NAME AS VENDORNAME, A.VOUCHERCODE, A.SERVICENAME,
							   A.SERVICEDATE, A.GUESTNAME, CONCAT('".URL_RESEVATION_VOUCHER_FILE."', A.FILENAME) AS URLPDFFILEVOUCHER
						FROM t_reservationvoucher A
						LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
						WHERE A.IDRESERVATION = '".$idReservation."' AND A.STATUS = 1
						ORDER BY A.VOUCHERCODE"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return array();		
	}

	public function getDataVoucherCode($yearMonth){
		$baseQuery	=	sprintf(
							"SELECT VOUCHERNUMBER FROM t_reservationvoucher
							 WHERE YEARMONTH = '".$yearMonth."'
							 ORDER BY VOUCHERNUMBER DESC
							 LIMIT 1"
						);
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		$arrReturn	=	array(
			"voucherNumber"	=>	1,
			"voucherCode"	=>	"BST-V".str_replace("-", "", $yearMonth).str_pad("1", 4, "0", STR_PAD_LEFT)
		);
		
		if($row){
			$newNumber	=	$row['VOUCHERNUMBER'] * 1 + 1;
			$arrReturn	=	array(
				"voucherNumber"	=>	$newNumber,
				"voucherCode"	=>	"BST-V".str_replace("-", "", $yearMonth).str_pad($newNumber, 4, "0", STR_PAD_LEFT)
			);
		}
		
		return $arrReturn;
	}
	
	public function getReservationPaymentList($idReservation){
		$query	=	$this->db->query(
						"SELECT A.IDRESERVATIONPAYMENT, A.IDPAYMENTMETHOD, B.PAYMENTMETHODNAME, A.DESCRIPTION, A.AMOUNTCURRENCY,
							   A.AMOUNT, A.EXCHANGECURRENCY, A.AMOUNTIDR, A.USERINPUT, DATE_FORMAT(A.DATETIMEINPUT, '%d %b %Y %H:%i') AS DATETIMEINPUT,
							   A.STATUS, DATE_FORMAT(A.DATETIMEUPDATE, '%d %b %Y %H:%i') AS DATETIMEUPDATE, A.USERUPDATE, A.EDITABLE, A.DELETABLE,
							   IFNULL(C.IDVENDOR, 0) AS IDVENDORCOLLECT, IFNULL(C.IDDRIVER, 0) AS IDDRIVERCOLLECT,
							   IFNULL(C.DATECOLLECT, '') AS DATECOLLECT, A.ISUPSELLING
						FROM t_reservationpayment A
						LEFT JOIN m_paymentmethod B ON A.IDPAYMENTMETHOD = B.IDPAYMENTMETHOD
						LEFT JOIN t_collectpayment C ON A.IDRESERVATIONPAYMENT = C.IDRESERVATIONPAYMENT
						WHERE A.IDRESERVATION = '".$idReservation."'
						ORDER BY A.DATETIMEINPUT"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return array();		
	}
	
	public function getDetailReservationPayment($idReservationPayment){
		$query	=	$this->db->query(
						"SELECT A.IDRESERVATIONPAYMENT, A.IDPAYMENTMETHOD, A.DESCRIPTION, A.AMOUNTCURRENCY, A.AMOUNT, A.EXCHANGECURRENCY,
							   A.AMOUNTIDR, A.STATUS, A.EDITABLE, A.DELETABLE, A.IDRESERVATION, IFNULL(B.IDCOLLECTPAYMENT, 0) AS IDCOLLECTPAYMENT,
							   IFNULL(IF(B.IDPARTNERTYPE = 1, C.NEWFINANCESCHEME, D.NEWFINANCESCHEME), 0) AS NEWFINANCESCHEME
						FROM t_reservationpayment A
						LEFT JOIN t_collectpayment B ON A.IDRESERVATIONPAYMENT = B.IDRESERVATIONPAYMENT
						LEFT JOIN m_vendor C ON B.IDVENDOR = C.IDVENDOR
						LEFT JOIN m_driver D ON B.IDDRIVER = D.IDDRIVER
						WHERE A.IDRESERVATIONPAYMENT = '".$idReservationPayment."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function getListReservationByKeyword($reservationKeyword){
		$query	=	$this->db->query(
						"SELECT B.SOURCENAME, A.RESERVATIONTITLE, A.DURATIONOFDAY, DATE_FORMAT(A.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
								DATE_FORMAT(A.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND, A.CUSTOMERNAME, A.CUSTOMERCONTACT, A.CUSTOMEREMAIL,
								IFNULL(A.HOTELNAME, '-') AS HOTELNAME, IFNULL(A.PICKUPLOCATION, '-') AS PICKUPLOCATION, A.DROPOFFLOCATION, A.NUMBEROFADULT,
								A.NUMBEROFCHILD, A.NUMBEROFINFANT, A.BOOKINGCODE, A.IDRESERVATION, '' AS ARRDATESCHEDULE, A.RESERVATIONDATESTART AS RESERVATIONDATEVALUE
						FROM t_reservation A
						LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
						WHERE A.RESERVATIONDATEEND >= '".date('Y-m-d')."' AND A.STATUS >= 0 AND 
							  (A.CUSTOMERNAME LIKE '%".$reservationKeyword."%' OR A.RESERVATIONTITLE LIKE '%".$reservationKeyword."%' OR A.CUSTOMERCONTACT LIKE '%".$reservationKeyword."%'
							  OR A.CUSTOMEREMAIL LIKE '%".$reservationKeyword."%' OR A.HOTELNAME LIKE '%".$reservationKeyword."%' OR A.PICKUPLOCATION LIKE '%".$reservationKeyword."%'
							  OR A.DROPOFFLOCATION LIKE '%".$reservationKeyword."%' OR A.BOOKINGCODE LIKE '%".$reservationKeyword."%')
						ORDER BY A.RESERVATIONDATESTART, A.IDRESERVATION"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return false;		
	}
	
	public function isCollectPaymentExist($idReservationPymnt){
		$query	=	$this->db->query(
						"SELECT IDCOLLECTPAYMENT FROM t_collectpayment
						WHERE IDRESERVATIONPAYMENT = '".$idReservationPymnt."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['IDCOLLECTPAYMENT'];
		return false;
	}
	
	public function checkDataDriverSchedule($idReservation, $date){
		$query	=	$this->db->query(
						"SELECT A.IDDRIVER, C.NAME AS PARTNERNAME, A.STATUS, C.NEWFINANCESCHEME FROM t_scheduledriver A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						LEFT JOIN m_driver C ON A.IDDRIVER = C.IDDRIVER
						WHERE B.IDRESERVATION = '".$idReservation."' AND B.SCHEDULEDATE = '".$date."' AND B.STATUS = 1
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function checkDataVendorSchedule($idReservation){
		$query	=	$this->db->query(
						"SELECT A.IDVENDOR, B.NAME AS PARTNERNAME FROM t_reservationdetails A
						LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
						WHERE A.IDRESERVATION = '".$idReservation."' AND A.STATUS = 1 AND A.IDVENDOR != 0
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}

	public function getDataCollectPaymentVendor($idReservation, $dateSchedule){
		$query	=	$this->db->query(
						"SELECT IDCOLLECTPAYMENT FROM t_collectpayment
						WHERE IDRESERVATION = ".$idReservation." AND DATECOLLECT = '".$dateSchedule."' AND
							  IDPARTNERTYPE = 1 AND IDVENDOR = 0"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return false;		
	}

	public function getIdCollectPaymentByIdReservationPayment($idReservationPayment){
		$query	=	$this->db->query(
						"SELECT IDCOLLECTPAYMENT FROM t_collectpayment
						WHERE IDRESERVATIONPAYMENT = ".$idReservationPayment."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['IDCOLLECTPAYMENT'];
		return 0;		
	}

	public function isAutoCostKeywordExist($reservationTitle){
		$query	=	$this->db->query(
						"SELECT IDAUTODETAILSTITLEKEYWORD, IDAUTODETAILSTEMPLATE FROM t_autodetailstitlekeyword
						WHERE TITLEKEYWORD = '".$reservationTitle."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['IDAUTODETAILSTEMPLATE'];
		return false;
	}
	
	public function getListAutoCostTemplate(){
		$query	=	$this->db->query(
						"SELECT IDAUTODETAILSTEMPLATE, AUTODETAILSTEMPLATENAME FROM t_autodetailstemplate
						ORDER BY AUTODETAILSTEMPLATENAME"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return false;		
	}
	
	public function getDetailCollectPayment($idCollectPayment){
		$query	=	$this->db->query(
						"SELECT A.IDRESERVATION, B.CUSTOMERNAME, B.RESERVATIONTITLE, C.DESCRIPTION, C.AMOUNT, C.AMOUNTCURRENCY, C.EXCHANGECURRENCY,
							   C.AMOUNTIDR, DATE_FORMAT(A.DATECOLLECT, '%d %b %Y') AS DATECOLLECTSTR, A.DATECOLLECT AS DATECOLLECTDB, A.STATUSSETTLEMENTREQUEST,
							   A.IDDRIVER, A.IDVENDOR
						FROM t_collectpayment A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN t_reservationpayment C ON A.IDRESERVATIONPAYMENT = C.IDRESERVATIONPAYMENT
						WHERE A.IDCOLLECTPAYMENT = '".$idCollectPayment."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;
	}
	
	public function getIdReservationDetailsDriver($idReservation, $idDriver, $date){
		$query	=	$this->db->query(
						"SELECT A.IDRESERVATIONDETAILS FROM t_reservationdetails A
						LEFT JOIN t_scheduledriver B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
						WHERE A.IDRESERVATION = '".$idReservation."' AND A.SCHEDULEDATE = '".$date."' AND B.IDDRIVER = ".$idDriver."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['IDRESERVATIONDETAILS'];
		return false;		
	}
	
	public function getIdReservationDetailsVendor($idReservation, $idVendor, $date){
		$query	=	$this->db->query(
						"SELECT IDRESERVATIONDETAILS FROM t_reservationdetails
						WHERE IDRESERVATION = '".$idReservation."' AND SCHEDULEDATE = '".$date."' AND IDVENDOR = ".$idVendor."
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row['IDRESERVATIONDETAILS'];
		return false;		
	}
	
	public function getDataCollectPaymentReservation($idReservation){
		$query	=	$this->db->query(
						"SELECT IDRESERVATIONPAYMENT FROM t_reservationpayment
						WHERE IDRESERVATION = '".$idReservation."' AND IDPAYMENTMETHOD IN (2,7)"
					);
		$result	=	$query->result();

		if(isset($result)) return $result;
		return false;		
	}

	public function getCollectPaymentVendorReservation($idReservation, $idVendor){
		$baseQuery	=	"SELECT B.IDCOLLECTPAYMENT, B.IDVENDOR, DATE_FORMAT(B.DATECOLLECT, '%d %b %Y') AS DATECOLLECT,
								A.AMOUNTCURRENCY, A.AMOUNT, A.AMOUNTIDR
						FROM t_reservationpayment A
						LEFT JOIN t_collectpayment B ON A.IDRESERVATIONPAYMENT = B.IDRESERVATIONPAYMENT
						WHERE A.IDRESERVATION = ".$idReservation." AND B.IDPARTNERTYPE = 1 AND
							  (B.IDVENDOR = 0 OR B.IDVENDOR = ".$idVendor.") AND A.IDPAYMENTMETHOD = 7
						ORDER BY B.DATECOLLECT";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return false;
		return $result;		
	}

	public function getDetailFeeVendor($idVendor, $idReservationDetails){
		$baseQuery	=	"SELECT IDFEE, IDWITHDRAWALRECAP FROM t_fee
						WHERE IDRESERVATIONDETAILS = ".$idReservationDetails." AND IDVENDOR = ".$idVendor."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;		
	}
	
	public function getDetailFeeDriver($idReservationDetails){
		$baseQuery	=	"SELECT IDFEE, IDWITHDRAWALRECAP FROM t_fee
						WHERE IDRESERVATIONDETAILS = ".$idReservationDetails." AND IDDRIVER != 0
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;		
	}

	public function getDetailReservationVendor($idReservationDetails){
		$baseQuery	=	"SELECT DATE_FORMAT(A.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATESTR, B.CUSTOMERNAME, B.RESERVATIONTITLE,
								A.PRODUCTNAME, A.IDVENDOR
						FROM t_reservationdetails A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						WHERE A.IDRESERVATIONDETAILS = ".$idReservationDetails."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!$row) return false;
		return $row;		
	}
	
	public function getDetailReservationMailRating($idReservation){
		$query	=	$this->db->query(
						"SELECT A.IDSOURCE, CONCAT(A.RESERVATIONDATESTART, ' ', A.RESERVATIONTIMESTART) AS RESERVATIONDATETIMESTART, COUNT(B.IDRESERVATIONDETAILS) AS TOTALRESERVATIONDETAILS,
							   A.CUSTOMEREMAIL, IFNULL(MAX(C.DURATIONHOUR), 0) AS MAXDURATIONHOUR, IFNULL(GROUP_CONCAT(IFNULL(C.DURATIONHOUR, 0)), '') AS ARRDURATIONHOUR,
							   IFNULL(GROUP_CONCAT(IFNULL(D.PRODUCTTITLE, '-') SEPARATOR '|'), '') AS ARRPRODUCTTITLE, IFNULL(GROUP_CONCAT(IFNULL(D.PRODUCTURL, '".MAILREVIEW_URL_DEFAULT."')), '') AS ARRPRODUCTURL
						FROM t_reservation A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATION = B.IDRESERVATION
						LEFT JOIN m_product C ON REPLACE(B.PRODUCTNAME, '[Klook] ', '') = C.PRODUCTNAME
						LEFT JOIN m_productreview D ON C.IDPRODUCTREVIEW = D.IDPRODUCTREVIEW
						WHERE A.IDRESERVATION = '".$idReservation."'
						GROUP BY A.IDRESERVATION
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
	
	public function isDataScheduleReviewExist($idReservation){
		$query	=	$this->db->query(
						"SELECT STATUSSEND FROM t_reservationmailreview
						WHERE IDRESERVATION = '".$idReservation."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}

	public function getDetailReservationByBookingCode($bookingCode){
		$query	=	$this->db->query(
						"SELECT IDRESERVATION, RESERVATIONTITLE AS reservationTitle, RESERVATIONDATESTART AS reservationDate, RESERVATIONTIMESTART AS reservationTime, DURATIONOFDAY AS durationOfDay,
								CUSTOMERNAME AS customerName, CUSTOMERCONTACT AS customerContact, CUSTOMEREMAIL AS customerEmail, HOTELNAME AS hotelName, PICKUPLOCATION AS pickupLocation,
								DROPOFFLOCATION AS dropoffLocation, NUMBEROFADULT AS numberOfAdult, NUMBEROFCHILD AS numberOfChild, NUMBEROFINFANT AS numberOfInfant, SPECIALREQUEST AS specialRequest,
								REMARK AS remark, TOURPLAN AS tourPlan, URLDETAILPRODUCT AS urlDetailProduct, URLPICKUPLOCATION AS urlPickupLocation, '[]' AS handleDriver, '[]' AS handleVendorTicket,
								IFNULL(IF(IDAREA = -1, 0, 1), 0) AS transportStatus, '' AS transportType, DETAILLUGGAGE AS detailLuggage, DETAILFLIGHT AS detailFlight
						FROM t_reservation
						WHERE BOOKINGCODE = '".$bookingCode."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}

	public function getReservationRawDetail($bookingCode){
		$query	=	$this->db->query(
						"SELECT * FROM t_reservation
						WHERE BOOKINGCODE = '".$bookingCode."'
						LIMIT 1"
					);
		$row	=	$query->row_array();

		if(isset($row)) return $row;
		return false;		
	}
}