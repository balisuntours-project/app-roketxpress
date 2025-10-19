<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function generateArrFieldInsertUpdate($postVar, $arrPostVar, $arrFieldName){
	
	$arrInsertField	=	array();
	$i				=	0;
	
	foreach($arrPostVar as $postVarKey){
		
		if(isset($postVar[$postVarKey])){
			$arrInsertField	=	array_merge(array($arrFieldName[$i]=>$postVar[$postVarKey]), $arrInsertField);
		}
		$i++;
		
	}
	
	return $arrInsertField;
	
}

function switchMySQLErrorCode($errCode, $token, $arrAdditional = array()){
	
	$mergeArray		=	array();
	switch($errCode){
		case 0		:	$mergeArray	=	array_merge(array("token"=>$token, "msg"=>"No data changes"), $arrAdditional);
						setResponseNotModified($mergeArray);
						break;
		case 1062	:	$mergeArray	=	array_merge(array("token"=>$token, "msg"=>"There is a duplication of input data"), $arrAdditional);
						setResponseConflict($mergeArray);
						break;
		case 1054	:	$mergeArray	=	array_merge(array("token"=>$token, "msg"=>"Database internal script error"), $arrAdditional);
						setResponseNoContent($mergeArray);
						break;
		default		:	$mergeArray	=	array_merge(array("token"=>$token, "msg"=>"Unkown database internal error"), array_merge($arrAdditional, array("errCode"=>$errCode)));
						setResponseUnknown($mergeArray);
						break;
	}
	
	return true;
	
}

function switchMySQLErrorCodeHttpResponse($errCode, $arrAdditional = array()){
	switch($errCode){
		case 0		:	returnHttpResponse(304, 'No data changes', $arrAdditional); break;
		case 1062	:	returnHttpResponse(409, 'There is a duplication of input data', $arrAdditional); break;
		case 1054	:	returnHttpResponse(500, 'Database internal script error', $arrAdditional); break;
		default		:	returnHttpResponse(500, 'Unkown database internal error', $arrAdditional); break;
	}
	
	return true;	
}

function createMD5Encode($string){
	
	$ci		=&	get_instance();
	$query	=	$ci->db->query("SELECT MD5('".$string."') AS VALUE");
	$row	=	$query->row_array();

	return $row['VALUE'];	
	
}

function reverseQueryResult($queryResult){
	
	$returnResult	=	array();
	$totalData		=	count($queryResult);
	for ($i = $totalData-1; $i >= 0; $i--) {
		$returnResult[]	=	$queryResult[$i];
	}
	
	return $returnResult;
	
}