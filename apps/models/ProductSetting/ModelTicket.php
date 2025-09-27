<?php
class ModelTicket extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function checkDataExists($idProduct, $idVendor, $paxRangeMin, $paxRangeMax, $idVendorTicketPrice=false){
		
		$idVendorTicketPrice	=	!isset($idVendorTicketPrice) || $idVendorTicketPrice == "" ? 0 : $idVendorTicketPrice;
		$baseQuery				=	"SELECT A.IDVENDORTICKETPRICE, B.PRODUCTNAME, C.NAME AS VENDORNAME
									FROM t_vendorticketprice A
									LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
									LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
									WHERE A.IDPRODUCT = ".$idProduct." AND A.IDVENDOR = ".$idVendor." AND A.IDVENDORTICKETPRICE != ".$idVendorTicketPrice." AND
										  A.MINPAX = ".$paxRangeMin." AND A.MAXPAX = ".$paxRangeMax."
									LIMIT 1";
		$query					=	$this->db->query($baseQuery);
		$row					=	$query->row_array();
		
		if(!isset($row)){
			return false;
		}
		
		return $row;
		
	}
	
	public function getDataTicketVendorPrice($ticketName, $idVendor){

		$con_ticketName	=	!isset($ticketName) || $ticketName == "" ? "1=1" : "B.PRODUCTNAME LIKE '%".$ticketName."%'";
		$con_idVendor	=	!isset($idVendor) || $idVendor == "" ? "1=1" : "A.IDVENDOR = ".$idVendor;
		$baseQuery		=	"SELECT B.PRODUCTNAME, C.NAME AS VENDORNAME, A.VOUCHERSTATUS, A.MINPAX, A.MAXPAX, A.PRICEADULT, A.PRICECHILD, A.PRICEINFANT,
									A.NOTES, A.IDVENDORTICKETPRICE
							FROM t_vendorticketprice A
							LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
							LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
							WHERE ".$con_ticketName." AND ".$con_idVendor." AND B.STATUS = 1
							ORDER BY B.PRODUCTNAME, C.NAME, A.MINPAX";
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!isset($result)){
			return false;
		}

		return $result;

	}
		
	public function getDetailTicketVendorPrice($idVendorTicketPrice){
		
		$baseQuery	=	"SELECT IDPRODUCT, IDVENDOR, VOUCHERSTATUS, MINPAX, MAXPAX, PRICEADULT, PRICECHILD, PRICEINFANT, NOTES
						FROM t_vendorticketprice
						WHERE IDVENDORTICKETPRICE = ".$idVendorTicketPrice."
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)){
			return false;
		}
		
		return $row;
		
	}
	
}