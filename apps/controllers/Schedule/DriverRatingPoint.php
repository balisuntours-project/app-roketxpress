<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DriverRatingPoint extends CI_controller {
	
	var $postVar;
	var $token;
	var $newToken;
	var $subtract30Days;
	
	public function __construct(){
        parent::__construct();

		$functionName	=	$this->uri->segment(3);
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->postVar			=	decodeJsonPost();
			$this->token			=	isset($this->postVar['token']) ? $this->postVar['token'] : setResponseBadRequest(array("msg"=>"Invalid submission data"));
			$this->newToken			=	isLoggedIn($this->token, true);
			$this->subtract30Days	=	date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
		} else {
			if($functionName != "apiCalculateBonusPunishmentReview" && $functionName != "fixDataReviewBonus") $this->index();
		}
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function getDataDriverRatingPoint(){

		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverRatingPoint');
		
		$page		=	validatePostVar($this->postVar, 'page', true);
		$keyword	=	validatePostVar($this->postVar, 'keyword', false);
		$dataTable	=	$this->ModelDriverRatingPoint->getDataDriverRatingPoint($page, $keyword);
		
		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"Please change your keyword input to search data"));
		}

		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
	public function getDataHistoryRatingPoint(){

		$this->load->model('Schedule/ModelDriverRatingPoint');
		
		$idDriver	=	validatePostVar($this->postVar, 'idDriver', true);
		$dataTable	=	$this->ModelDriverRatingPoint->getDataHistoryRatingPoint($idDriver, $this->subtract30Days);
		
		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable));
	
	}
	
	public function getDataDriverRatingByDate(){
	
		$this->load->model('Schedule/ModelDriverRatingPoint');
		
		$searchKeyword	=	validatePostVar($this->postVar, 'searchKeyword', false);
		$dateRating		=	validatePostVar($this->postVar, 'dateRating', true);
		$dateRating		=	DateTime::createFromFormat('d-m-Y', $dateRating);
		$dateRatingStr	=	$dateRating->format('d M Y');
		$dateRating		=	$dateRating->format('Y-m-d');
		$dataTable		=	$this->ModelDriverRatingPoint->getDataDriverRatingByDate($dateRating, $searchKeyword);
	
		if(!$dataTable){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "dateRatingStr"=>$dateRatingStr));
		}
		
		setResponseOk(array("token"=>$this->newToken, "result"=>$dataTable, "dateRatingStr"=>$dateRatingStr));
	
	}
	
	public function saveDataDriverRatingPoint(){
		
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverRatingPoint');
		
		$userInput	=	validatePostVar($this->postVar, 'NAME', false);
		$idDriver	=	validatePostVar($this->postVar, 'idDriver', true);
		$idSource	=	validatePostVar($this->postVar, 'idSource', true);
		$rating		=	validatePostVar($this->postVar, 'rating', true);
		$point		=	validatePostVar($this->postVar, 'point', true);
		$dateRating	=	validatePostVar($this->postVar, 'dateRating', true);
		$dateRating	=	DateTime::createFromFormat('d-m-Y', $dateRating);
		$dateRating	=	$dateRating->format('Y-m-d');
		
		if($dateRating < $this->subtract30Days){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Maximum period of rating input is <b>30 days</b> ago"));
		}
		
		if($dateRating > date('Y-m-d')){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Cannot input data beyond today's date"));
		}
		
		$arrInsert	=	array(
							"IDDRIVER"			=>	$idDriver,
							"IDSOURCE"			=>	$idSource,
							"DATERATINGPOINT"	=>	$dateRating,
							"RATING"			=>	$rating,
							"POINT"				=>	$point,
							"USERINPUT"			=>	$userInput,
							"DATETIMEINPUT"		=>	date('Y-m-d H:i:s')
						);
		$procInsert	=	$this->MainOperation->addData("t_driverratingpoint", $arrInsert);

		if(!$procInsert['status']){
			switchMySQLErrorCode($procInsert['errCode'], $this->newToken);
		}
		
		$this->setPointRankDriver();
		$this->calculateBonusPunishmentReview();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"New driver rating & point data saved"));
	
	}
	
	public function saveDataSettingRatingPoint(){
		
		$this->load->model('MainOperation');
		
		$arrRatingPoint	=	validatePostVar($this->postVar, 'arrRatingPoint', true);
		foreach($arrRatingPoint as $keyRatingPoint){
			$arrUpdate	=	array("POINT" => $keyRatingPoint[1]);
			$this->MainOperation->updateData("m_driverpoint", $arrUpdate, "RATING", $keyRatingPoint[0]);			
		}
		
		$optionHelper	=	$this->getArrDataOptionHelper();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Setting rating & point has been saved", "optionHelper"=>$optionHelper));
	
	}
	
	public function deleteDriverRatingPoint(){
		
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverRatingPoint');
		
		$idDriverRatingPoint=	validatePostVar($this->postVar, 'idDriverRatingPoint', true);
		$dateRating			=	validatePostVar($this->postVar, 'dateRating', true);
		
		if($dateRating < $this->subtract30Days){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Maximum data that can be deleted is <b>30 days</b> ago"));
		}
		
		$deleteResult		=	$this->MainOperation->deleteData("t_driverratingpoint", array("IDDRIVERRATINGPOINT"=>$idDriverRatingPoint));		
		if(!$deleteResult['status']){
			switchMySQLErrorCode($deleteResult['errCode'], $this->newToken);
		}

		$this->setPointRankDriver();
		$this->calculateBonusPunishmentReview();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver rating & point data has been deleted"));
	
	}
	
	public function apiSetPointRankDriver(){
		try {
			$this->setPointRankDriver();
			setResponseOk(array("token"=>$this->newToken, "msg"=>"Done proccess for set point rank driver"));
		} catch(Exception $e) {
			setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed. Try again later"));
		}
	}

	private function setPointRankDriver(){
		
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverRatingPoint');
		
		$dataPointDriver	=	$this->ModelDriverRatingPoint->getDataPointDriver($this->subtract30Days);
		
		if($dataPointDriver){
			$rankDriver		=	1;
			$typeDriver		=	0;
			foreach($dataPointDriver as $pointDriver){
				$rankDriver	=	$typeDriver != $pointDriver->IDDRIVERTYPE ? 1 : $rankDriver;
				$totalPoint	=	$pointDriver->TOTALPOINT;
				$arrUpdate	=	array(
									"TOTALPOINT" => $totalPoint * 1,
									"RANKNUMBER" => $rankDriver
								);
				$this->MainOperation->updateData("m_driver", $arrUpdate, "IDDRIVER", $pointDriver->IDDRIVER);
				$typeDriver	=	$pointDriver->IDDRIVERTYPE;
				$rankDriver++;
			}
		}
		
		return true;
	
	}
	
	public function saveDriverBasicPoint(){
		
		$this->load->model('MainOperation');
		
		$idDriver	=	validatePostVar($this->postVar, 'idDriver', true);
		$basicPoint	=	validatePostVar($this->postVar, 'basicPoint', true);
		$procUpdate	=	$this->MainOperation->updateData("m_driver", array("BASICPOINT"=>$basicPoint), "IDDRIVER", $idDriver);

		if(!$procUpdate['status']){
			switchMySQLErrorCode($procUpdate['errCode'], $this->newToken);
		}

		$this->setPointRankDriver();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver basic point has been saved"));
	
	}
	
	private function getArrDataOptionHelper(){

		$this->load->model('ModelOptionHelper');
		$optionHelper	=	$this->ModelOptionHelper->getDataOptionHelperRatingPoint();
		
		return $optionHelper;

	}
	
	public function scanInputRatingPointAuto(){
		
		$this->load->model('Schedule/ModelDriverRatingPoint');
		
		$idSource		=	validatePostVar($this->postVar, 'idSource', true);
		$jsonData		=	validatePostVar($this->postVar, 'jsonData', false);
		
		if($jsonData == ""){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"JSON data input cannot be empty"));
		}
		
		json_decode($jsonData);
		$isJSONValid	=	json_last_error() == JSON_ERROR_NONE;
		
		if(!$isJSONValid){
			setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Invalid JSON data input"));
		}
		
		$jsonDataDecode		=	json_decode($jsonData);		
		$arrRatingPointTable=	array();
		
		if($idSource == 1){
			if(!isset($jsonDataDecode->result->review_list)){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select a valid source or input valid JSON data"));
			} else {
				if($jsonDataDecode->result->total <= 0){
					setResponseForbidden(array("token"=>$this->newToken, "msg"=>"No review data found"));
				}
		
				foreach($jsonDataDecode->result->review_list as $keyJSONData){
					
					$bookingCode			=	$keyJSONData->booking_no;
					$rating					=	$keyJSONData->stars;
					$dateRating				=	substr($keyJSONData->review_time, 0, 10);
					$dateRatingStr			=	DateTime::createFromFormat('Y-m-d', $dateRating);
					$dateRatingStr			=	$dateRatingStr->format('d M Y');
					$reviewTitle			=	$keyJSONData->activity_name;
					$reviewContent			=	$keyJSONData->review;
					$reviewContentLength	=	strlen($reviewContent);
					$dataDrivers			=	$this->ModelDriverRatingPoint->getDataDriverByBookingCode($bookingCode);
					
					if($dataDrivers){
						foreach($dataDrivers as $keyDrivers){
							$arrRatingPointTable[]	=	array(
															"driverId"				=>	$keyDrivers->IDDRIVER,
															"driverName"			=>	$keyDrivers->DRIVERNAME,
															"bookingCode"			=>	$bookingCode,
															"reservationTitle"		=>	$keyDrivers->RESERVATIONTITLE,
															"dateRating"			=>	$dateRating,
															"dateRatingStr"			=>	$dateRatingStr,
															"rating"				=>	$rating,
															"reviewTitle"			=>	$reviewTitle,
															"reviewContent"			=>	$reviewContent,
															"reviewContentLength"	=>	$reviewContentLength
														);
						}
					}
					
				}
			}
		} else if($idSource == 2){
			if(!isset($jsonDataDecode->reviewsMap)){
				setResponseForbidden(array("token"=>$this->newToken, "msg"=>"Please select a valid source or input valid JSON data"));
			} else {
				if(count($jsonDataDecode->reviewIds) <= 0){
					setResponseForbidden(array("token"=>$this->newToken, "msg"=>"No review data found"));
				}
				
				foreach($jsonDataDecode->reviewsMap as $bookingCode => $objectReview){
					
					$rating					=	$objectReview->rating;
					$dateRating				=	$objectReview->reviewDate;
					$dateRatingStr			=	DateTime::createFromFormat('Y-m-d', $dateRating);
					$dateRatingStr			=	$dateRatingStr->format('d M Y');
					$reviewTitle			=	$objectReview->reviewTitle;
					$reviewContent			=	$objectReview->reviewContent;
					$reviewContentLength	=	strlen($reviewContent);
					$isBookingCodeExist		=	$this->ModelDriverRatingPoint->isBookingCodeViatorExist($bookingCode);
					
					if(!$isBookingCodeExist){
						$arrRatingPointTable[]	=	array(
														"driverId"				=>	0,
														"driverName"			=>	"",
														"reservationTitle"		=>	"-",
														"bookingCode"			=>	$bookingCode,
														"dateRating"			=>	$dateRating,
														"dateRatingStr"			=>	$dateRatingStr,
														"rating"				=>	$rating,
														"reviewTitle"			=>	$reviewTitle,
														"reviewContent"			=>	$reviewContent,
														"reviewContentLength"	=>	$reviewContentLength
													);
					}
				}
			}
		}

		setResponseOk(array("token"=>$this->newToken, "arrRatingPointTable"=>$arrRatingPointTable));
	
	}
	
	public function saveInputRatingPointAuto(){
		
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverRatingPoint');
		
		$userInput			=	validatePostVar($this->postVar, 'NAME', false);
		$idSource			=	validatePostVar($this->postVar, 'idSource', true);
		$arrRatingPointInput=	validatePostVar($this->postVar, 'arrRatingPointInput', true);
		$totalProcess		=	0;
		
		foreach($arrRatingPointInput as $ratingPointInput){
			
			$arrIdDriver	=	json_decode($ratingPointInput[1]);
			if(is_array($arrIdDriver) && count($arrIdDriver) > 0){
				foreach($arrIdDriver as $idDriver){
					$isDataExist	=	$this->ModelDriverRatingPoint->isRatingPointExistBookingCode($ratingPointInput[4], $idDriver);
					if(!$isDataExist){
						$arrInsert		=	array(
												"IDDRIVER"			=>	$idDriver,
												"IDSOURCE"			=>	$idSource,
												"BOOKINGCODE"		=>	$ratingPointInput[4],
												"DATERATINGPOINT"	=>	$ratingPointInput[0],
												"RATING"			=>	$ratingPointInput[2],
												"POINT"				=>	$ratingPointInput[3],
												"REVIEWTITLE"		=>	str_replace("'", "`", preg_replace('/[^\00-\255]+/u', '', $ratingPointInput[5])),
												"REVIEWCONTENT"		=>	str_replace("'", "`", preg_replace('/[^\00-\255]+/u', '', $ratingPointInput[6])),
												"INPUTTYPE"			=>	2,
												"USERINPUT"			=>	$userInput,
												"DATETIMEINPUT"		=>	date('Y-m-d H:i:s')
											);
						$procInsert		=	$this->MainOperation->addData("t_driverratingpoint", $arrInsert);

						if($procInsert['status']){
							$totalProcess++;
						}
					}
				}
			}
			
		}
		
		$this->setPointRankDriver();
		$this->calculateBonusPunishmentReview();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"All driver rating & point data saved. Total data : ".$totalProcess));

	}
	
	public function refreshDriverPoint(){
		$this->setPointRankDriver();
		$this->calculateBonusPunishmentReview();
		setResponseOk(array("token"=>$this->newToken, "msg"=>"Driver point has been refreshed"));
	}
	
	public function getDataRatingCalendar(){
		
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverRatingPoint');
		
		$month			=	validatePostVar($this->postVar, 'month', true);
		$year			=	validatePostVar($this->postVar, 'year', true);
		$idDriverType	=	validatePostVar($this->postVar, 'idDriverType', false);
		$idDriver		=	validatePostVar($this->postVar, 'idDriver', false);
		$idSource		=	validatePostVar($this->postVar, 'idSource', false);
		$orderField		=	validatePostVar($this->postVar, 'orderField', false);
		$orderType		=	validatePostVar($this->postVar, 'orderType', false);
		$yearMonth		=	$year."-".$month;
		$firstDate		=	$yearMonth."-01";
		$totalDays		=	date("t", strtotime($firstDate));
		$dataDriver		=	$this->ModelDriverRatingPoint->getDataAllDriver($idDriverType, $idDriver, $orderField, $orderType);
		$arrDates		=	$arrDataRating	=	$arrDriver	=	array();
		
		for($i=1; $i<=$totalDays; $i++){
			$arrDates[]			=	$i;
			$arrDataRating[]	=	array();
		}
		
		if(!$dataDriver){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No data found", "arrDates"=>$arrDates));
		}
		
		foreach($dataDriver as $keyDriver){
			$keyDriver->DATARATING	=	$arrDataRating;
			$arrDriver[]			=	$keyDriver->IDDRIVER;
		}

		$strArrDriver	=	implode(",", $arrDriver);
		$dataRating		=	$this->ModelDriverRatingPoint->getDataDriverRatingMonth($yearMonth, $strArrDriver, $idSource);

		if($dataRating){
			foreach($dataRating as $keyRating){
				$idDriverDB		=	$keyRating->IDDRIVER;
				$sourceInitialDB=	$keyRating->SOURCEINITIAL;
				$ratingDB		=	$keyRating->RATING;
				$pointDB		=	$keyRating->POINT;
				$dateRatinDB	=	$keyRating->DATERATING * 1;
				$bookingCode	=	$keyRating->BOOKINGCODE;
				$idxDate		=	$dateRatinDB - 1;
				
				foreach($dataDriver as $keyDriver){
					if($keyDriver->IDDRIVER == $idDriverDB){
						$keyDriver->DATARATING[$idxDate][]	=	array($sourceInitialDB, $ratingDB, $bookingCode);
						break;
					}
				}
			}
		}
		
		setResponseOk(array("token"=>$this->newToken, "dataDriver"=>$dataDriver, "arrDates"=>$arrDates));
		
	}
	
	public function getDetailReviewContent(){
	
		$this->load->model('Schedule/ModelDriverRatingPoint');
		
		$bookingCode	=	validatePostVar($this->postVar, 'bookingCode', true);
		$detailData		=	$this->ModelDriverRatingPoint->getDetailReviewContentByBookingCode($bookingCode);
	
		if(!$detailData){
			setResponseNotFound(array("token"=>$this->newToken, "msg"=>"No details found for this review"));
		}
		
		setResponseOk(array("token"=>$this->newToken, "detailData"=>$detailData));
	
	}
	
	public function apiCalculateBonusPunishmentReview($base64JsonData){
		$jsonData						=	base64_decode($base64JsonData);
		$arrData						=	json_decode($jsonData);
		$arrIdDriverReviewBonusPeriod	=	isset($arrData->arrIdDriverReviewBonusPeriod) ? $arrData->arrIdDriverReviewBonusPeriod : [];
		$deleteByIdDriver				=	isset($arrData->deleteByIdDriver) ? $arrData->deleteByIdDriver : false;
		
		try {
			if(count($arrIdDriverReviewBonusPeriod) > 0){
				foreach($arrIdDriverReviewBonusPeriod as $idDriverReviewBonusPeriod){
					$this->calculateBonusPunishmentReview($idDriverReviewBonusPeriod, $deleteByIdDriver);
				}
			} else {
				$this->calculateBonusPunishmentReview($idDriverReviewBonusPeriod, $deleteByIdDriver);
			}
			setResponseOk(array("token"=>$this->newToken, "msg"=>"Done proccess for calculate bonus punishment review"));
		} catch(Exception $e) {
			setResponseInternalServerError(array("token"=>$this->newToken, "msg"=>"Failed. Try again later"));
		}
	}
	
	private function calculateBonusPunishmentReview($idDriverReviewBonusPeriod = false, $deleteByIdDriver = false){
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverRatingPoint');

		$dataSourceReviewBonus		=	$this->ModelDriverRatingPoint->getDataSourceBonusReview();
		$dataPeriodReviewBonus		=	$this->ModelDriverRatingPoint->getDataPeriodBonusReview($idDriverReviewBonusPeriod);
		$dataDriverReviewBonus		=	$this->ModelDriverRatingPoint->getDataDriverBonusReview();
		
		if($dataSourceReviewBonus){
			$strArrIdSourceReviewBonus	=	$dataSourceReviewBonus['STRARRIDSOURCEREVIEWBONUS'];
			
			if($dataPeriodReviewBonus){
				$idDriverReviewBonusPeriod	=	$dataPeriodReviewBonus['IDDRIVERREVIEWBONUSPERIOD'];
				$dateStartBonusPeriod		=	$dataPeriodReviewBonus['PERIODDATESTART'];
				$dateEndBonusPeriod			=	$dataPeriodReviewBonus['PERIODDATEEND'];
				$rateBonusPeriod			=	$dataPeriodReviewBonus['BONUSRATE'];
				$targetBonusPeriod			=	$dataPeriodReviewBonus['TOTALTARGET'];
				
				if(isset($deleteByIdDriver) && $deleteByIdDriver != false && $deleteByIdDriver != 0){
					$isDriverReviewBonusExist	=	$this->ModelDriverRatingPoint->isDriverReviewBonusExist($deleteByIdDriver, $idDriverReviewBonusPeriod);
					if($isDriverReviewBonusExist){
						$idWithdrawalRecap		=	$isDriverReviewBonusExist['IDWITHDRAWALRECAP'];
						
						if($idWithdrawalRecap == 0){
							$idDriverReviewBonus=	$isDriverReviewBonusExist['IDDRIVERREVIEWBONUS'];
							$this->MainOperation->deleteData('t_driverreviewbonus', ['IDDRIVERREVIEWBONUS' => $idDriverReviewBonus]);
						}
					}
				}
				
				if($dataDriverReviewBonus){
					foreach($dataDriverReviewBonus as $driverReviewBonus){
						$idDriver					=	$driverReviewBonus->IDDRIVER;
						$isDriverReviewBonusExist	=	$this->ModelDriverRatingPoint->isDriverReviewBonusExist($idDriver, $idDriverReviewBonusPeriod);
						$idDriverReviewBonus		=	$isDriverReviewBonusExist && $isDriverReviewBonusExist['IDWITHDRAWALRECAP'] == 0 ? $isDriverReviewBonusExist['IDDRIVERREVIEWBONUS'] : false;
						$dataBonusReview			=	$this->ModelDriverRatingPoint->getDataBonusReview($strArrIdSourceReviewBonus, $idDriver, $dateStartBonusPeriod, $dateEndBonusPeriod, $idDriverReviewBonus);
						$totalReviewPoint			=	0;
						$arrIdDriverRatingPoint		=	[];
						
						if($dataBonusReview){
							$totalReviewPoint		=	$dataBonusReview['TOTALREVIEWPOINT'];
							$arrIdDriverRatingPoint	=	explode(",", $dataBonusReview['STRARRIDDRIVERRATINGPOINT']);
						}
						
						$targetBonusPeriodFinal		=	$isDriverReviewBonusExist && $isDriverReviewBonusExist['TARGETEXCEPTION'] != -1 ? $isDriverReviewBonusExist['TARGETEXCEPTION'] : $targetBonusPeriod;
						$nominalBonus				=	$totalReviewPoint * $rateBonusPeriod;
						$totalTargetPunishment		=	$targetBonusPeriodFinal - $totalReviewPoint;
						$nominalPunishment			=	$totalTargetPunishment <= 0 ? 0 : $totalTargetPunishment * $rateBonusPeriod;
						$arrInsUpdDriverReviewBonus	=	[
							"IDDRIVERREVIEWBONUSPERIOD"	=>	$idDriverReviewBonusPeriod,
							"IDDRIVER"					=>	$idDriver,
							"TOTALREVIEWPOINT"			=>	$totalReviewPoint,
							"NOMINALBONUS"				=>	$nominalBonus,
							"NOMINALPUNISHMENT"			=>	$nominalPunishment,
							"NOMINALRESULT"				=>	($nominalBonus - $nominalPunishment)
						];
						
						$idDriverReviewBonus	=	0;
						if($isDriverReviewBonusExist){
							$idWithdrawalRecap		=	$isDriverReviewBonusExist['IDWITHDRAWALRECAP'];
							
							if($idWithdrawalRecap == 0){
								$idDriverReviewBonus=	$isDriverReviewBonusExist['IDDRIVERREVIEWBONUS'];
								$this->MainOperation->updateData('t_driverreviewbonus', $arrInsUpdDriverReviewBonus, 'IDDRIVERREVIEWBONUS', $idDriverReviewBonus);
							}
						} else {
							$procInsertReviewBonus	=	$this->MainOperation->addData('t_driverreviewbonus', $arrInsUpdDriverReviewBonus);
							if($procInsertReviewBonus['status']){
								$idDriverReviewBonus	=	$procInsertReviewBonus['insertID'];
							}
						}
						
						if($idDriverReviewBonus != 0 && count($arrIdDriverRatingPoint) > 0){
							$this->MainOperation->updateDataIn('t_driverratingpoint', ['IDDRIVERREVIEWBONUS' => $idDriverReviewBonus], 'IDDRIVERRATINGPOINT', $arrIdDriverRatingPoint);
						}
					}
				}
			}
		}
		return true;
	}

	public function fixDataReviewBonus($dateInput){
		$this->load->model('MainOperation');
		$this->load->model('Schedule/ModelDriverRatingPoint');

		$dataRatingPoint	=	$this->ModelDriverRatingPoint->getDataRatingPointByDateInput($dateInput);
		
		if($dataRatingPoint){
			foreach($dataRatingPoint as $keyRatingPoint){
				$idDriverRatingPoint=	$keyRatingPoint->IDDRIVERRATINGPOINT;
				$idDriver			=	$keyRatingPoint->IDDRIVER;
				$idSource			=	$keyRatingPoint->IDSOURCE;
				$dateTimeInput		=	$keyRatingPoint->DATETIMEINPUT;
				$idDriverReviewBonus=	$keyRatingPoint->IDDRIVERREVIEWBONUS;
				
				///checking data
				$idDriverReviewBonusFix	=	$this->ModelDriverRatingPoint->getIdReviewBonus($idDriver, $dateTimeInput);
				
				if($idDriverReviewBonusFix){
					if($idDriverReviewBonus != $idDriverReviewBonusFix) {
						$this->MainOperation->updateData('t_driverratingpoint', ['IDDRIVERREVIEWBONUS' => $idDriverReviewBonusFix], 'IDDRIVERRATINGPOINT', $idDriverRatingPoint);
						echo $idDriver." - ".$dateTimeInput." - ".$idDriverReviewBonus." != ".$idDriverReviewBonusFix."<br/>";
					}
				}
			}
		}
		
		echo "OK";
	}
}