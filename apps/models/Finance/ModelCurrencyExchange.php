<?php
class ModelCurrencyExchange extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
	
	public function checkDataExists($currency, $dateStart, $idCurrencyExchange = false){
		
		$con_Id	=	isset($idCurrencyExchange) && $idCurrencyExchange != false ? "IDCURRENCYEXCHANGE != ".$idCurrencyExchange : "1=1";
		$query	=	$this->db->query("SELECT IDCURRENCYEXCHANGE FROM t_currencyexchange
									WHERE CURRENCY = '".$currency."' AND DATESTART = '".$dateStart."' AND ".$con_Id."
									LIMIT 1");
		$row	=	$query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}

	public function getDataCurrencyExchange($currency, $page, $dataPerPage= 20){
		
		$ci			=& get_instance();
		$ci->load->model('MainOperation');

		$startid	=	($page * 1 - 1) * $dataPerPage;
		$baseQuery	=	"SELECT IDCURRENCYEXCHANGE, CURRENCY, DATESTART, DATE_FORMAT(DATESTART, '%d %b %Y') AS DATESTARTSTR,
								DATE_FORMAT(DATESTART, '%d-%m-%Y') AS DATESTARTSTREDITOR, EXCHANGEVALUE, DELETEABLE
						FROM t_currencyexchange
						WHERE CURRENCY = '".$currency."'
						ORDER BY DATESTART DESC";
		$query		=	$this->db->query($baseQuery." LIMIT ".$startid.", ".$dataPerPage);
		$result		=	$query->result();
		
		if(isset($result)){
			return $ci->MainOperation->generateResultPagination($result, $baseQuery, "IDCURRENCYEXCHANGE", $page, $dataPerPage);
		}
		
		return $ci->MainOperation->generateEmptyResult();
		
	}

	public function getCurrentCurrencyExchange($currency){
		
		$baseQuery	=	"SELECT EXCHANGETOIDR FROM helper_exchangecurrency
						WHERE CURRENCY = '".$currency."'
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)){
			return $row['EXCHANGETOIDR'];
		}
		
		return 1;
		
	}
	
	public function getDataCurrencyStart($currency, $dateStart){
		
		$baseQuery	=	"SELECT DATESTART, EXCHANGEVALUE FROM t_currencyexchange
						WHERE CURRENCY = '".$currency."' AND DATE(DATESTART) < '".$dateStart."'
						ORDER BY DATESTART DESC
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)){
			return $row;
		}
		
		return false;
		
	}
	
	public function getEndDateUpdateRecord($currency, $dateStart){
		
		$baseQuery	=	"SELECT DATESTART FROM t_currencyexchange
						WHERE CURRENCY = '".$currency."' AND DATE(DATESTART) > '".$dateStart."'
						ORDER BY DATESTART ASC
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)){
			return $row['DATESTART'];
		}
		
		return false;
		
	}
	
	public function isLatestDateStart($currency, $dateStart){
		
		$baseQuery	=	"SELECT IDCURRENCYEXCHANGE FROM t_currencyexchange
						WHERE CURRENCY = '".$currency."' AND DATE(DATESTART) > '".$dateStart."'
						LIMIT 1";
		$query		=	$this->db->query($baseQuery);
		$row		=	$query->row_array();
		
		if(isset($row)){
			return false;
		}
		
		return true;
		
	}
	
}