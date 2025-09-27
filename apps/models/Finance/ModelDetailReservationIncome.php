<?php
class ModelDetailReservationIncome extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDataDetailReservationIncome($page, $dataPerPage= 25, $startDate, $endDate, $idSource, $idReservationType, $keywordSearch, $includeCollectPayment, $includeAdditionalCost){
		$ci	=&	get_instance();
		$ci->load->model('MainOperation');

		$startid				=	($page * 1 - 1) * $dataPerPage;
		$con_source				=	isset($idSource) && $idSource != "" && $idSource != 0 ? "A.IDSOURCE = ".$idSource : "1=1";
		$con_idReservationType	=	isset($idReservationType) && $idReservationType != "" ? "A.IDRESERVATIONTYPE = ".$idReservationType : "1=1";
		$con_keywordSearch		=	isset($keywordSearch) && $keywordSearch != "" ? "(A.BOOKINGCODE LIKE '%".$keywordSearch."%' OR A.CUSTOMERNAME LIKE '%".$keywordSearch."%' OR A.RESERVATIONTITLE LIKE '%".$keywordSearch."%' OR A.PICKUPLOCATION LIKE '%".$keywordSearch."%' OR A.HOTELNAME LIKE '%".$keywordSearch."%')" : "1=1";
		$arrIdReservation		=	[];
		
		if(isset($includeCollectPayment) && $includeCollectPayment == true){
			$arrIdReservationCollectPayment	=	$this->getArrIdReservationIncludeCollectPayment($startDate, $endDate);
			$arrIdReservation				=	array_unique(array_merge($arrIdReservation, $arrIdReservationCollectPayment));
		}
		
		if(isset($includeAdditionalCost) && $includeAdditionalCost == true){
			$arrIdReservationAdditionalCost	=	$this->getArrIdReservationIncludeAdditionalCost($startDate, $endDate);
			$arrIdReservation				=	array_unique(array_merge($arrIdReservation, $arrIdReservationAdditionalCost));
		}
		
		$con_idReservationIn=	count($arrIdReservation) > 0 ? "A.IDRESERVATION IN (".implode(',', $arrIdReservation).")" : "1=1";
		$baseQuery	=	"SELECT J.RESERVATIONTYPE, B.SOURCENAME, A.INPUTTYPE, A.RESERVATIONTITLE, DATE_FORMAT(A.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
								DATE_FORMAT(A.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND, A.RESERVATIONTIMESTART, A.RESERVATIONTIMEEND, 
								A.STATUS, A.DURATIONOFDAY, A.BOOKINGCODE, A.CUSTOMERNAME, A.CUSTOMERCONTACT, A.CUSTOMEREMAIL, 
								GROUP_CONCAT(DISTINCT(CONCAT(H.STATUS, ']', I.PAYMENTMETHODNAME, ']', H.AMOUNTIDR, ']', H.IDRESERVATIONPAYMENT)) SEPARATOR '|') AS DETAILSPAYMENTFINANCE,
								GROUP_CONCAT(DISTINCT(CONCAT(E.PRODUCTTYPE, '=', D.IDRESERVATIONDETAILS)) ORDER BY D.SCHEDULEDATE, D.PRODUCTNAME SEPARATOR '|') AS DETAILSPRODUCTTYPE,
								GROUP_CONCAT(DISTINCT(CONCAT(D.PRODUCTNAME, '=', D.IDRESERVATIONDETAILS)) ORDER BY D.SCHEDULEDATE, D.PRODUCTNAME SEPARATOR '|') AS DETAILSPRODUCTNAME,
								GROUP_CONCAT(DISTINCT(CONCAT(IFNULL(F.NAME, CONCAT ('Driver ', G.DRIVERTYPE)), '=', D.IDRESERVATIONDETAILS)) ORDER BY D.SCHEDULEDATE, D.PRODUCTNAME SEPARATOR '|') AS DETAILSPRODUCTVENDORDRIVER,
								GROUP_CONCAT(DISTINCT(CONCAT(D.NOMINAL, '=', D.IDRESERVATIONDETAILS)) ORDER BY D.SCHEDULEDATE, D.PRODUCTNAME SEPARATOR '|') AS DETAILSPRODUCTCOST,
								GROUP_CONCAT(DISTINCT(CONCAT(DATE_FORMAT(D.SCHEDULEDATE, '%d %b %Y'), '=', D.IDRESERVATIONDETAILS)) ORDER BY D.SCHEDULEDATE, D.PRODUCTNAME SEPARATOR '|') AS DETAILSPRODUCTDATE,
								A.INCOMEAMOUNTIDR, A.IDRESERVATION, A.IDRESERVATIONTYPE
						FROM t_reservation A
						LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
						LEFT JOIN t_reservationdetails D ON A.IDRESERVATION = D.IDRESERVATION AND D.STATUS = 1
						LEFT JOIN m_producttype E ON D.IDPRODUCTTYPE = E.IDPRODUCTTYPE
						LEFT JOIN m_vendor F ON D.IDVENDOR = F.IDVENDOR
						LEFT JOIN m_drivertype G ON D.IDDRIVERTYPE = G.IDDRIVERTYPE
						LEFT JOIN t_reservationpayment H ON A.IDRESERVATION = H.IDRESERVATION AND H.STATUS != -1
						LEFT JOIN m_paymentmethod I ON H.IDPAYMENTMETHOD = I.IDPAYMENTMETHOD
						LEFT JOIN m_reservationtype J ON A.IDRESERVATIONTYPE = J.IDRESERVATIONTYPE
						WHERE A.RESERVATIONDATESTART BETWEEN '".$startDate."' AND '".$endDate."' AND ".$con_source." AND ".$con_idReservationType." AND ".$con_keywordSearch." AND ".$con_idReservationIn." AND A.STATUS != -1
						GROUP BY A.IDRESERVATION
						ORDER BY A.RESERVATIONDATESTART";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)){
			$resultPagination	=	$ci->MainOperation->generateResultPagination($result, $baseQuery, "IDRESERVATION", $page, $dataPerPage);
			$dataAddCosts		=	$this->getDataReservationAdditionalCost($startDate, $endDate);
			
			if($dataAddCosts){
				foreach($dataAddCosts as $keyAddCosts){
					$keyIndex	=	array_search($keyAddCosts->IDRESERVATION, array_column($resultPagination['data'], 'IDRESERVATION'));
					if($keyIndex){
						$resultPagination['data'][$keyIndex]->DETAILSPRODUCTTYPE		.=	($resultPagination['data'][$keyIndex]->DETAILSPRODUCTTYPE != "" ? "|" : "").$keyAddCosts->DETAILSPRODUCTTYPE;
						$resultPagination['data'][$keyIndex]->DETAILSPRODUCTNAME		.=	($resultPagination['data'][$keyIndex]->DETAILSPRODUCTNAME != "" ? "|" : "").$keyAddCosts->DETAILSPRODUCTNAME;
						$resultPagination['data'][$keyIndex]->DETAILSPRODUCTVENDORDRIVER.=	($resultPagination['data'][$keyIndex]->DETAILSPRODUCTVENDORDRIVER != "" ? "|" : "").$keyAddCosts->DETAILSPRODUCTVENDORDRIVER;
						$resultPagination['data'][$keyIndex]->DETAILSPRODUCTCOST		.=	($resultPagination['data'][$keyIndex]->DETAILSPRODUCTCOST != "" ? "|" : "").$keyAddCosts->DETAILSPRODUCTCOST;
						$resultPagination['data'][$keyIndex]->DETAILSPRODUCTDATE		.=	($resultPagination['data'][$keyIndex]->DETAILSPRODUCTDATE != "" ? "|" : "").$keyAddCosts->DETAILSPRODUCTDATE;
					}
				}
			}

			return $resultPagination;
		}
		
		return $ci->MainOperation->generateEmptyResult();
	}
	
	private function getArrIdReservationIncludeCollectPayment($startDate, $endDate){
		$ci				=& get_instance();
		$ci->load->model('MainOperation');

		$strArrIdPaymentMethod	=	$ci->MainOperation->getStrArrIdPaymentMethodCollectPayment();
		
		if(!$strArrIdPaymentMethod) return [];
			
		$baseQuery	=	"SELECT A.IDRESERVATION FROM t_reservationpayment A
						LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
						WHERE B.RESERVATIONDATESTART BETWEEN '".$startDate."' AND '".$endDate."' AND A.IDPAYMENTMETHOD IN (".$strArrIdPaymentMethod.") AND A.STATUS >= 0
						GROUP BY A.IDRESERVATION";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) {
			$arrReturn	=	[-1];
			foreach($result as $keyResult){
				$arrReturn[]	=	$keyResult->IDRESERVATION;
			}
			return $arrReturn;
		}
		
		return [-1];		
	}
	
	private function getArrIdReservationIncludeAdditionalCost($startDate, $endDate){
		$baseQuery	=	"SELECT B.IDRESERVATION FROM t_reservationadditionalcost A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS AND B.STATUS = 1
						LEFT JOIN t_reservation C ON B.IDRESERVATION = C.IDRESERVATION
						WHERE C.RESERVATIONDATESTART BETWEEN '".$startDate."' AND '".$endDate."'
						GROUP BY B.IDRESERVATION";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) {
			$arrReturn	=	[-1];
			foreach($result as $keyResult){
				$arrReturn[]	=	$keyResult->IDRESERVATION;
			}
			return $arrReturn;
		}
		return [-1];		
	}
	
	public function getDataReservationAdditionalCost($startDate, $endDate){
		$baseQuery	=	"SELECT B.IDRESERVATION, GROUP_CONCAT('Additional Cost' SEPARATOR '|') AS DETAILSPRODUCTTYPE,
								GROUP_CONCAT(A.DESCRIPTION ORDER BY B.SCHEDULEDATE SEPARATOR '|') AS DETAILSPRODUCTNAME,
								GROUP_CONCAT(D.NAME ORDER BY B.SCHEDULEDATE SEPARATOR '|') AS DETAILSPRODUCTVENDORDRIVER,
								GROUP_CONCAT(A.NOMINAL ORDER BY B.SCHEDULEDATE SEPARATOR '|') AS DETAILSPRODUCTCOST,
								GROUP_CONCAT(DATE_FORMAT(B.SCHEDULEDATE, '%d %b %Y') ORDER BY B.SCHEDULEDATE SEPARATOR '|') AS DETAILSPRODUCTDATE
						FROM t_reservationadditionalcost A
						LEFT JOIN t_reservationdetails B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS AND B.STATUS = 1
						LEFT JOIN t_reservation C ON B.IDRESERVATION = C.IDRESERVATION
						LEFT JOIN m_driver D ON A.IDDRIVER = D.IDDRIVER
						WHERE C.RESERVATIONDATESTART BETWEEN '".$startDate."' AND '".$endDate."' AND C.STATUS != -1 AND A.STATUSAPPROVAL = 1
						GROUP BY B.IDRESERVATION
						ORDER BY C.RESERVATIONDATESTART";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;		
	}
	
	public function getDataRecapReservationIncome($page, $dataPerPage= 25, $reportType, $idSource, $startDate, $endDate){
		$ci	=&	get_instance();
		$ci->load->model('MainOperation');

		$startid		=	($page * 1 - 1) * $dataPerPage;
		$startDateDT	=	DateTime::createFromFormat('d-m-Y', $startDate);
		$startDate		=	$startDateDT->format('Y-m-d');
		$startDateStr	=	$startDateDT->format('d M Y');
		$startYearMonth	=	$startDateDT->format('Ym');
		$endDateDT		=	DateTime::createFromFormat('d-m-Y', $endDate);
		$endDate		=	$endDateDT->format('Y-m-d');
		$endDateStr		=	$endDateDT->format('d M Y');
		$endYearMonth	=	$endDateDT->format('Ym');

		$fieldPeriod=	$reportType == 1 ? "'".$startDateStr." - ".$endDateStr."'" : "DATE_FORMAT(A.RESERVATIONDATESTART, '%M %Y')";
		$con_source	=	isset($idSource) && $idSource != "" && $idSource != 0 ? "A.IDSOURCE = ".$idSource : "1=1";
		$con_date	=	$reportType == 1 ? "A.RESERVATIONDATESTART BETWEEN '".$startDate."' AND '".$endDate."'" : "REPLACE(LEFT(A.RESERVATIONDATESTART, 7), '-', '') BETWEEN '".$startYearMonth."' AND '".$endYearMonth."'";
		$groupBy	=	$reportType == 1 ? "A.IDSOURCE" : "A.IDSOURCE, LEFT(A.RESERVATIONDATESTART, 7)";
		$orderBy	=	$reportType == 1 ? "B.SOURCENAME" : "LEFT(A.RESERVATIONDATESTART, 7), B.SOURCENAME";
		$baseQuery	=	"SELECT A.IDSOURCE, ".$fieldPeriod." AS PERIOD, B.SOURCENAME, COUNT(A.IDRESERVATION) AS TOTALRESERVATION, SUM(A.INCOMEAMOUNTIDR) AS TOTALINCOME
						FROM t_reservation A
						LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
						WHERE ".$con_source." AND ".$con_date." AND A.STATUS != -1
						GROUP BY ".$groupBy."
						ORDER BY ".$orderBy."";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)) return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDSOURCE", $page, $dataPerPage);
		return $ci->MainOperation->generateEmptyResult();
	}
	
	public function getDataRecapPerYear($year, $idSource){
		$con_source	=	isset($idSource) && $idSource != "" && $idSource != 0 ? "A.IDSOURCE = ".$idSource : "1=1";
		$baseQuery	=	"SELECT DATE_FORMAT(A.RESERVATIONDATESTART, '%M %Y') AS PERIOD, COUNT(DISTINCT(A.IDRESERVATION)) AS TOTALRESERVATION, SUM(A.INCOMEAMOUNTIDR) AS TOTALINCOMERESERVATION,
								SUM(C.AMOUNTIDR) AS TOTALINCOMEFINANCE, SUM(D.NOMINAL) AS TOTALCOST, 0 AS MARGIN
						FROM t_reservation PARTITION (p_".$year.") A
						LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
						LEFT JOIN (
								SELECT IDRESERVATION, SUM(AMOUNTIDR) AS AMOUNTIDR
								FROM t_reservationpayment
								WHERE STATUS != -1
								GROUP BY IDRESERVATION
							) AS C ON A.IDRESERVATION = C.IDRESERVATION
						LEFT JOIN (
								SELECT DA.IDRESERVATION, SUM(DA.NOMINAL + IFNULL(DB.NOMINAL, 0)) AS NOMINAL
								FROM t_reservationdetails DA
								LEFT JOIN (
										SELECT IDRESERVATIONDETAILS, NOMINAL
										FROM t_reservationadditionalcost
										WHERE STATUSAPPROVAL = 1
									) AS DB ON DA.IDRESERVATIONDETAILS = DB.IDRESERVATIONDETAILS
								WHERE DA.STATUS = 1
								GROUP BY DA.IDRESERVATION
							) AS D ON A.IDRESERVATION = D.IDRESERVATION
						WHERE ".$con_source." AND A.STATUS != -1
						GROUP BY LEFT(A.RESERVATIONDATESTART, 7)
						ORDER BY LEFT(A.RESERVATIONDATESTART, 7)";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)) return $result;
		return false;
	}
}