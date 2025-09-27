<?php
class ModelDetailFeeVendor extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function getDetailFeeVendorCar($page, $dataPerPage= 25, $idVendorCar, $yearMonth){
		
		$ci					=&	get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_idVendorCar	=	!isset($idVendorCar) || $idVendorCar == "" ? "1=1" : "B.IDVENDOR = ".$idVendorCar;
		$baseQuery			=	"SELECT A.IDSCHEDULECAR, C.NAME, DATE_FORMAT(D.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATE,
										CONCAT(B.BRAND, ' ', B.MODEL, '<br/>[', B.PLATNUMBER, ']') AS CARDETAILS, F.IDRESERVATIONTYPE,
										F.RESERVATIONTYPE, G.SOURCENAME, E.INPUTTYPE, E.RESERVATIONTITLE, E.CUSTOMERNAME,
										D.PRODUCTNAME, A.USERINPUT, D.NOTES, D.NOMINAL
								FROM t_schedulecar A
								LEFT JOIN t_carvendor B ON A.IDCARVENDOR = B.IDCARVENDOR
								LEFT JOIN m_vendor C ON B.IDVENDOR = C.IDVENDOR
								LEFT JOIN t_reservationdetails D ON A.IDRESERVATIONDETAILS = D.IDRESERVATIONDETAILS
								LEFT JOIN t_reservation E ON D.IDRESERVATION = E.IDRESERVATION
								LEFT JOIN m_reservationtype F ON E.IDRESERVATIONTYPE = F.IDRESERVATIONTYPE
								LEFT JOIN m_source G ON E.IDSOURCE = G.IDSOURCE
								WHERE LEFT(D.SCHEDULEDATE,7) = '".$yearMonth."' AND D.STATUS = 1 AND ".$con_idVendorCar."
								ORDER BY C.NAME, D.SCHEDULEDATE";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDSCHEDULECAR", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}

	public function getDetailFeeVendorTicket($page, $dataPerPage= 25, $idVendorTicket, $startDate, $endDate){
		
		$ci					=&	get_instance();
		$ci->load->model('MainOperation');

		$startid			=	($page * 1 - 1) * $dataPerPage;
		$con_idVendorTicket	=	!isset($idVendorTicket) || $idVendorTicket == "" ? "1=1" : "A.IDVENDOR = ".$idVendorTicket;
		$baseQuery			=	"SELECT A.IDRESERVATIONDETAILS, B.NAME, DATE_FORMAT(A.SCHEDULEDATE, '%d %b %Y') AS SCHEDULEDATE,
										C.IDRESERVATIONTYPE, D.RESERVATIONTYPE, E.SOURCENAME, C.INPUTTYPE, C.RESERVATIONTITLE, C.CUSTOMERNAME,
										A.PRODUCTNAME, A.USERINPUT, A.NOTES, A.NOMINAL, F.PAXADULT, F.PAXCHILD, F.PAXINFANT, F.PRICEPERPAXADULT,
										F.PRICEPERPAXCHILD, F.PRICEPERPAXINFANT, F.PRICETOTALADULT, F.PRICETOTALCHILD, F.PRICETOTALINFANT
								FROM t_reservationdetails A
								LEFT JOIN m_vendor B ON A.IDVENDOR = B.IDVENDOR
								LEFT JOIN t_reservation C ON A.IDRESERVATION = C.IDRESERVATION
								LEFT JOIN m_reservationtype D ON C.IDRESERVATIONTYPE = D.IDRESERVATIONTYPE
								LEFT JOIN m_source E ON C.IDSOURCE = E.IDSOURCE
								LEFT JOIN t_reservationdetailsticket F ON A.IDRESERVATIONDETAILS = F.IDRESERVATIONDETAILS
								WHERE A.SCHEDULEDATE BETWEEN '".$startDate."' AND'".$endDate."' AND A.STATUS = 1 AND B.IDVENDORTYPE = 2 AND ".$con_idVendorTicket."
								ORDER BY B.NAME, A.SCHEDULEDATE";
		$query				=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result				=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDRESERVATIONDETAILS", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}
	
}