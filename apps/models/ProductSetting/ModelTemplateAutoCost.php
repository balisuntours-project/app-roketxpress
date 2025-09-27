<?php
class ModelTemplateAutoCost extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}

	public function checkDataDetailCostExists($idAutoDetailsTemplate, $idCostType, $idTicketProduct, $idTranportProduct){
		
		$idProductFee	=	isset($idCostType) && $idCostType == 1 ? $idTicketProduct : $idTranportProduct;
		$baseQuery		=	"SELECT IDAUTODETAILSTEMPLATEITEM FROM t_autodetailstemplateitem
							WHERE IDAUTODETAILSTEMPLATE = ".$idAutoDetailsTemplate." AND IDPRODUCTTYPE = ".$idCostType." AND IDPRODUCTFEE = ".$idProductFee."
							LIMIT 1";
		$query			=	$this->db->query($baseQuery);
		$row			=	$query->row_array();
		
		if(!isset($row)){
			return false;
		}
		
		return $row;
		
	}
		
	public function checkDataExists($idProduct, $idVendor, $idVendorTemplateAutoCostPrice=false){
		
		$idVendorTemplateAutoCostPrice	=	!isset($idVendorTemplateAutoCostPrice) || $idVendorTemplateAutoCostPrice == "" ? 0 : $idVendorTemplateAutoCostPrice;
		$baseQuery				=	"SELECT A.IDVENDORTICKETPRICE, B.PRODUCTNAME, C.NAME AS VENDORNAME
									FROM t_vendorticketprice A
									LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
									LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
									WHERE A.IDPRODUCT = ".$idProduct." AND A.IDVENDOR = ".$idVendor." AND A.IDVENDORTICKETPRICE != ".$idVendorTemplateAutoCostPrice."
									LIMIT 1";
		$query					=	$this->db->query($baseQuery);
		$row					=	$query->row_array();
		
		if(!isset($row)){
			return false;
		}
		
		return $row;
		
	}
		
	public function getProductTicket($arrExceptionTicket){
		
		$con_exception	=	$arrExceptionTicket == "" ? "1=1" : "A.IDVENDORTICKETPRICE NOT IN (".$arrExceptionTicket.")";
		$baseQuery		=	sprintf("SELECT A.IDVENDORTICKETPRICE AS VALUE,
											CASE
											WHEN A.MINPAX = A.MAXPAX THEN CONCAT('[', C.NAME, '] ', B.PRODUCTNAME, ' (Must ', A.MINPAX, ' pax)')
											WHEN A.MINPAX = 1 AND A.MAXPAX != 999 THEN CONCAT('[', C.NAME, '] ', B.PRODUCTNAME, ' (Max. ', A.MAXPAX, ' Pax)')
											WHEN A.MINPAX != 1 AND A.MAXPAX = 999 THEN CONCAT('[', C.NAME, '] ', B.PRODUCTNAME, ' (Min. ', A.MINPAX, ' Pax)')
											WHEN A.MINPAX != 1 AND A.MAXPAX != 999 THEN CONCAT('[', C.NAME, '] ', B.PRODUCTNAME, ' (', A.MINPAX, '-', A.MAXPAX, ' pax)')
											ELSE CONCAT('[', C.NAME, '] ', B.PRODUCTNAME)
											END AS OPTIONTEXT
									 FROM t_vendorticketprice A
									 LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
									 LEFT JOIN m_vendor C ON A.IDVENDOR = C.IDVENDOR
									 WHERE B.STATUS = 1 AND ".$con_exception."
									 ORDER BY C.NAME, B.PRODUCTNAME"
							);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
		
	public function getProductTransport($arrExceptionTransport){
		
		$con_exception	=	$arrExceptionTransport == "" ? "1=1" : "A.IDDRIVERFEE NOT IN (".$arrExceptionTransport.")";
		$baseQuery		=	sprintf("SELECT A.IDDRIVERFEE AS VALUE, CONCAT('[', C.DRIVERTYPE, '] ', IF(A.IDSOURCE = 0, '', CONCAT(' [', D.SOURCENAME, '] ')), B.PRODUCTNAME) AS OPTIONTEXT
									 FROM t_driverfee A
									 LEFT JOIN m_product B ON A.IDPRODUCT = B.IDPRODUCT
									 LEFT JOIN m_drivertype C ON A.IDDRIVERTYPE = C.IDDRIVERTYPE
									 LEFT JOIN m_source D ON A.IDSOURCE = D.IDSOURCE
									 WHERE A.FEENOMINAL > 0 AND B.STATUS = 1 AND ".$con_exception."
									 GROUP BY A.IDDRIVERFEE
									 ORDER BY C.DRIVERTYPE, B.PRODUCTNAME"
							);
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!$result){
			return array();
		}
		
		return $result;
	
	}
	
	public function checkDataTemplateNameExists($templateAutoCostName){
		
		$baseQuery	=	"SELECT IDAUTODETAILSTEMPLATE FROM t_autodetailstemplate
						WHERE AUTODETAILSTEMPLATENAME = '".$templateAutoCostName."'
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)){
			return false;
		}
		
		return $row;
		
	}
	
	public function checkDataKeywordExists($keyword){
		
		$baseQuery	=	"SELECT A.IDAUTODETAILSTEMPLATE, B.AUTODETAILSTEMPLATENAME
						FROM t_autodetailstitlekeyword A
						LEFT JOIN t_autodetailstemplate B ON A.IDAUTODETAILSTEMPLATE = B.IDAUTODETAILSTEMPLATE
						WHERE A.TITLEKEYWORD = '".$keyword."'
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)){
			return false;
		}
		
		return $row;
		
	}
	
	public function getDataTemplateAutoCost($keyword){

		if(!isset($keyword) || $keyword == ""){
			$con_like	=	"1=1";
		} else {
			$con_like	=	"A.AUTODETAILSTEMPLATENAME LIKE '%".$keyword."%' OR
							 B.TITLEKEYWORD LIKE '%".$keyword."%' OR 
							 E.VENDORNAME LIKE '%".$keyword."%' OR 
							 E.PRODUCTNAME LIKE '%".$keyword."%' OR 
							 F.DRIVERTYPE LIKE '%".$keyword."%' OR 
							 F.PRODUCTNAME LIKE '%".$keyword."%'";
		}
		
		$baseQuery		=	"SELECT A.IDAUTODETAILSTEMPLATE, A.AUTODETAILSTEMPLATENAME,
									GROUP_CONCAT(DISTINCT(CONCAT(B.IDAUTODETAILSTITLEKEYWORD, '|', B.TITLEKEYWORD)) ORDER BY B.TITLEKEYWORD SEPARATOR '&&') AS KEYWORDLIST,
									GROUP_CONCAT(DISTINCT(CONCAT(C.IDAUTODETAILSTEMPLATEITEM, '|', '(Ticket) ', E.VENDORNAME, ' : ', E.PRODUCTNAME, '|', C.IDPRODUCTFEE)) ORDER BY E.VENDORNAME, E.PRODUCTNAME SEPARATOR '&&') AS TICKETPRICELIST,
									GROUP_CONCAT(DISTINCT(CONCAT(D.IDAUTODETAILSTEMPLATEITEM, '|', '(Transport) ', IF(F.IDSOURCE = 0, '', CONCAT(' [', F.SOURCENAME, '] ')), F.DRIVERTYPE, ' : ', F.PRODUCTNAME, '|', D.IDPRODUCTFEE)) ORDER BY F.DRIVERTYPE, F.SOURCENAME, F.PRODUCTNAME SEPARATOR '&&') AS DRIVERFEELIST
							FROM t_autodetailstemplate A
							LEFT JOIN t_autodetailstitlekeyword B ON A.IDAUTODETAILSTEMPLATE = B.IDAUTODETAILSTEMPLATE
							LEFT JOIN t_autodetailstemplateitem C ON A.IDAUTODETAILSTEMPLATE = C.IDAUTODETAILSTEMPLATE AND C.IDPRODUCTTYPE = 1
							LEFT JOIN t_autodetailstemplateitem D ON A.IDAUTODETAILSTEMPLATE = D.IDAUTODETAILSTEMPLATE AND D.IDPRODUCTTYPE = 2
							LEFT JOIN (
										SELECT EA.IDVENDORTICKETPRICE, EB.PRODUCTNAME, EC.NAME AS VENDORNAME
										FROM t_vendorticketprice EA
										LEFT JOIN m_product EB ON EA.IDPRODUCT = EB.IDPRODUCT
										LEFT JOIN m_vendor EC ON EA.IDVENDOR = EC.IDVENDOR
									  ) AS E ON C.IDPRODUCTFEE = E.IDVENDORTICKETPRICE
							LEFT JOIN (
										SELECT FA.IDDRIVERFEE, FA.IDSOURCE, FC.DRIVERTYPE, FB.PRODUCTNAME, FD.SOURCENAME
										FROM t_driverfee FA
										LEFT JOIN m_product FB ON FA.IDPRODUCT = FB.IDPRODUCT
										LEFT JOIN m_drivertype FC ON FA.IDDRIVERTYPE = FC.IDDRIVERTYPE
										LEFT JOIN m_source FD ON FA.IDSOURCE = FD.IDSOURCE
									  ) AS F ON D.IDPRODUCTFEE = F.IDDRIVERFEE
							WHERE ".$con_like."
							GROUP BY A.IDAUTODETAILSTEMPLATE
							ORDER BY A.AUTODETAILSTEMPLATENAME";
		$query			=	$this->db->query($baseQuery);
		$result			=	$query->result();
		
		if(!isset($result)){
			return false;
		}

		return $result;

	}
		
	public function getTotalTemplateAutoCostItem($idAutoDetailsTemplate){
		
		$baseQuery	=	"SELECT COUNT(IDAUTODETAILSTEMPLATEITEM) AS TOTALITEM
						FROM t_autodetailstemplateitem
						WHERE IDAUTODETAILSTEMPLATE = ".$idAutoDetailsTemplate."
						GROUP BY IDAUTODETAILSTEMPLATE
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)){
			return 0;
		}
		
		return $row['TOTALITEM'];
		
	}
		
	public function getTotalKeywordTemplate($idAutoDetailsTemplate){
		
		$baseQuery	=	"SELECT COUNT(IDAUTODETAILSTITLEKEYWORD) AS TOTALKEYWORD
						FROM t_autodetailstitlekeyword
						WHERE IDAUTODETAILSTEMPLATE = ".$idAutoDetailsTemplate."
						GROUP BY IDAUTODETAILSTEMPLATE
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(!isset($row)){
			return 0;
		}
		
		return $row['TOTALKEYWORD'];
		
	}
	
}