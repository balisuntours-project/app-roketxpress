<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

use Ddeboer\Imap\Server;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Search\Email\From;
use Ddeboer\Imap\Search\Flag\Unseen;

class AIAgentService extends CI_controller {
	
	var $postVar;
    private $connection;
	private $dom;

    public function __construct(){
		parent::__construct();
        $this->load->helper('date');
        $this->load->helper('httpResponse');
		$functionName	=	$this->uri->segment(2);

		if($functionName == 'readKlookBadReviewMail'){
			$username			= MAILBOX_USERNAME;
			$password			= MAILBOX_PASSWORD;
			$server				= new Server('imap.gmail.com/ssl/NoValidate-Cert');
			$this->connection	= $server->authenticate($username, $password);

			libxml_use_internal_errors(false);
			$this->dom						=	new DOMDocument();
			$this->dom->strictErrorChecking	=	false;
			$this->dom->recover				=	true;
			$this->dom->preserveWhiteSpace	=	false;
		} else {
            $this->postVar	=	decodeJsonPost();
        }
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function cronEBookingCoinNonDriver(){
		$this->load->model('MainOperation');
		$this->load->model('ModelAIAgentService');
		
		$dateTimeNow 			=	new DateTime();
		$dateTimeMinus12Hours	=	clone $dateTimeNow->modify('-12 hours');
		$dateTimeMinus18Hours	=	clone $dateTimeNow->modify('-18 hours');
		$dateTimeMinus12Hours	=	$dateTimeMinus12Hours->format('Y-m-d H:i:s');
		$dateTimeMinus18Hours	=	$dateTimeMinus18Hours->format('Y-m-d H:i:s');
		
		$dataEBooking	=	$this->ModelAIAgentService->getDataEBookingEarnCoin($dateTimeMinus12Hours, $dateTimeMinus18Hours);
		if($dataEBooking){
			foreach($dataEBooking as $keyEBooking){
				$idReservation	=	$keyEBooking->IDRESERVATION;
				$bookingCode	=	$keyEBooking->BOOKINGCODE;
				$arrInsert		=	[
					'IDRESERVATION'	=>	$idReservation,
					'EXECUTETYPE'	=>	2,
					'DATETIMEINSERT'=>	date('Y-m-d H:i:s'),
					'STATUS'		=>	0
				];
				
				$this->MainOperation->addData('t_ebookingcoin', $arrInsert);
			}
		}
		
		$this->executeEBookingCoinEarned();
		echo "Done - ".date('Y-m-d H:i:s');
	}
	
	private function executeEBookingCoinEarned(){
		$dataExecuteEBooking=	$this->ModelAIAgentService->getDataExecuteEBookingCoinEarned();
		
		if($dataExecuteEBooking){
			$timeStamp		=	time();
			foreach($dataExecuteEBooking as $keyExecuteEBooking){
				$idEBookingCoin	=	$keyExecuteEBooking->IDEBOOKINGCOIN;
				$bookingCode	=	$keyExecuteEBooking->BOOKINGCODE;
				$dataJSON       =   json_encode(['booking_code'=>$bookingCode, 'timestamp'=>$timeStamp]);
				$privateKey     =   ROKET_ECOMMERCE_PRIVATE_KEY;
				$hmacSignature  =   hash_hmac('sha256', $dataJSON, $privateKey);
				$procUpdateCoin	=	$this->updateCoinBookingEcommerce($bookingCode, $hmacSignature, $timeStamp);
				$httpCode		=	$procUpdateCoin['httpCode'];
				$response		=	$procUpdateCoin['response'];
				$arrUpdateCoin	=	[
					'EXECUTETYPE'		=>	2,
					'EXECUTEBY'			=>	'Auto System',
					'DATETIMEEXECUTE'	=>	date('Y-m-d H:i:s'),
					'APIRESPONSE'		=>	$response,
					'STATUS'			=>	0
				];
				
				switch(intval($httpCode)){
					case 200	:	$arrUpdateCoin['STATUS']	=	1; break;
					case 401	:	$arrUpdateCoin['STATUS']	=	-1; break;
					case 409	:	$arrUpdateCoin['STATUS']	=	1; break;
				}
				
				$this->MainOperation->updateData('t_ebookingcoin', $arrUpdateCoin, 'IDEBOOKINGCOIN', $idEBookingCoin);
				echo $bookingCode."<br/>";
				echo $httpCode."<br/>";
				echo json_encode($response)."<br/>";
			}
		}
		
		return true;
	}
	
	private function updateCoinBookingEcommerce($bookingCode, $hmacSignature, $timeStamp){
		$response	=	"";
		$httpCode	=	500;

		try {
			$curl	=	curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL				=>	ROKET_ECOMMERCE_API_BASE_URL.'/api/customer/coin/earn-from-booking?booking_code='.$bookingCode,
			  CURLOPT_RETURNTRANSFER	=>	true,
			  CURLOPT_ENCODING			=>	'',
			  CURLOPT_MAXREDIRS			=>	10,
			  CURLOPT_TIMEOUT			=>	0,
			  CURLOPT_FOLLOWLOCATION	=>	true,
			  CURLOPT_HTTP_VERSION		=>	CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST		=>	'POST',
			  CURLOPT_HTTPHEADER		=>	array(
				'BST-Public-Key: '.ROKET_ECOMMERCE_PUBLIC_KEY,
				'BST-Signature: '.$hmacSignature,
				'BST-Timestamp: '.$timeStamp
			  ),
			));

			$response	=	curl_exec($curl);
			$httpCode	=	curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
		} catch (Exception $e) {
		}
		
		return [
			'httpCode'	=>	$httpCode,
			'response'	=>	json_encode($response)
		];
	}

	public function readKlookBadReviewMail(){
		$this->load->model('ModelAIAgentService');
		
		$search		=   new SearchExpression();
		$search->addCondition(new From(MAIL_KLOOK_BAD_REVIEW_SENDER_ADDRESS));
		$search->addCondition(new Unseen());

		$inbox		=	$this->connection->getMailbox('INBOX');
		$messages	=	$inbox->getMessages($search);

		foreach ($messages as $message) {
			$message->markAsSeen();
			
			$subject			=	$message->getSubject();
			if(strpos($subject, 'You have a new review from Klook') !== false){
				$bookingCode	=	$reviewContent	=	'';
				$reviewDateTime	=	'0000-00-00 00:00:00';
				$rating			=	1;
				$messageHTML	=	$message->getBodyHTML();
				
				@$this->dom->loadHTML($messageHTML);
				$table1			= 	$this->dom->getElementsByTagName('table')->item(0);
				$row1			=	$table1->getElementsByTagName('tr')->item(0);
				$col1			=	$row1->getElementsByTagName('td')->item(0);
				
				$table2			= 	$col1->getElementsByTagName('table')->item(0);
				$row2			=	$table2->getElementsByTagName('tr')->item(0);
				$col2			=	$row2->getElementsByTagName('td')->item(1);

				$table3			= 	$col2->getElementsByTagName('table')->item(0);
				$tableBooking	= 	$table3->getElementsByTagName('table')->item(1);
				$tableReview	= 	$table3->getElementsByTagName('table')->item(3);
				
				$rowsBooking	=	$tableBooking->getElementsByTagName('tr');
				$rowReview		=	$tableReview->getElementsByTagName('tr')->item(0);
				
				foreach($rowsBooking as $rowBooking){
					$colsBooking		=	$rowBooking->getElementsByTagName('td');
					$colsBookingTitleEl	=	$colsBooking[0]->getElementsByTagName('span');
					$colsBookingTitle	=	count($colsBookingTitleEl) > 0 ? $colsBookingTitleEl[0]->textContent : '';
					
					if($colsBookingTitle == 'Booking reference ID:'){
						$spansRemove	=	$colsBooking[0]->getElementsByTagName('span');
						for ($i = $spansRemove->length - 1; $i >= 0; $i--) {
							$spanRemove	=	$spansRemove->item($i);
							$spanRemove->parentNode->removeChild($spanRemove);
						}
						
						$bookingCode	=	trim($colsBooking[0]->textContent);
					}
				}

				$colsReview		=	$rowReview->getElementsByTagName('td')->item(1);
				$tableReview	=	$colsReview->getElementsByTagName('table')->item(0);
				$childRowsReview=	$tableReview->getElementsByTagName('tr');
				
				foreach($childRowsReview as $childRowReview){
					$colsReview			=	$childRowReview->getElementsByTagName('td');
					$colsReviewTitleEl	=	$colsReview[0]->getElementsByTagName('span');
					$colsReviewTitle	=	count($colsReviewTitleEl) > 0 ? $colsReviewTitleEl[0]->textContent : '';
					
					if($colsReviewTitle == 'Review Date:'){
						$spansRemove	=	$colsReview[0]->getElementsByTagName('span');
						for ($i = $spansRemove->length - 1; $i >= 0; $i--) {
							$spanRemove	=	$spansRemove->item($i);
							$spanRemove->parentNode->removeChild($spanRemove);
						}
						
						$reviewDateTime	=	trim($colsReview[0]->textContent);
					}
					
					if($colsReviewTitle == 'Rating:'){
						$rating	=	$colsReview[0]->getElementsByTagName('span')->item(1)->textContent;
						$rating	=	str_replace(' / 5', '', $rating);
					}
					
					if($colsReviewTitle == ''){
						$reviewContent	=	trim($colsReview[0]->textContent);
					}
					
				}
				
				if($bookingCode != ''){
					try {
						$curl			=	curl_init();
						$timeStamp		=	time();
						$dataJSON       =   json_encode(['timestamp'=>$timeStamp]);
						$privateKey     =   ROKET_CS_AI_AGENT_PRIVATE_KEY;
						$hmacSignature  =   hash_hmac('sha256', $dataJSON, $privateKey);
						$isDriverOwnCar	=	$this->ModelAIAgentService->isDriverHandleOwnCarByBookingCode($bookingCode);
						$arrPostData	=	[
							'review'		=>	$reviewContent,
							'booking_code'	=>	$bookingCode,
							'review_date'	=>	substr($reviewDateTime, 0, 10),
							'review_star'	=>	$rating,
							'driver_car'	=>	$isDriverOwnCar
						];
						
						curl_setopt_array($curl,
							array(
								CURLOPT_URL				=>	ROKET_CS_AI_AGENT_BAD_REVIEW_ANALYZE_URL,
								CURLOPT_RETURNTRANSFER	=>	true,
								CURLOPT_ENCODING		=>	'',
								CURLOPT_MAXREDIRS		=>	10,
								CURLOPT_TIMEOUT			=>	100,
								CURLOPT_FOLLOWLOCATION	=>	true,
								CURLOPT_HTTP_VERSION	=>	CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST	=>	'POST',
								CURLOPT_POSTFIELDS      =>  http_build_query($arrPostData),
								CURLOPT_HTTPHEADER		=>	array(
									'BST-Public-Key: '.ROKET_CS_AI_AGENT_PUBLIC_KEY,
									'BST-Signature: '.$hmacSignature,
									'BST-Timestamp: '.$timeStamp
								)
							)
						);

						$response	=	curl_exec($curl);
						$httpCode	=	curl_getinfo($curl, CURLINFO_HTTP_CODE);
						curl_close($curl);

						echo "URL: ".ROKET_CS_AI_AGENT_BAD_REVIEW_ANALYZE_URL."<br/>";
						echo "Response: ".json_encode($response)."<br/>";
						echo "HTTP Code: ".$httpCode."<br/>";
					} catch (Exception $e) {
						log_message('error', 'readKlookBadReviewMail -> error :: '.json_encode($e));
					}
				}
			}
		}
		
		echo "End read bad review klook - ".date("d M Y H:i");
	}

    public function updateReservationDetail(){
        $this->checkSignature();
		$bookingCode    =   $this->postVar['bookingCode'] ?? null;

        if (is_null($bookingCode) || $bookingCode === '') returnHttpResponse(406, 'Booking code is required');
        
        $this->load->model('ModelReservation');
        $reservationDetail  =   $this->ModelReservation->getDetailReservationByBookingCode($bookingCode);

        if (is_null($reservationDetail) || !$reservationDetail) returnHttpResponse(404, 'Invalid reservation code');

        $arrUpdate      =   [];
		$paxAdult       =   $this->postVar['paxAdult'] ?? null;
		$paxChild       =   $this->postVar['paxChild'] ?? null;
		$paxInfant      =   $this->postVar['paxInfant'] ?? null;
		$detailLuggage  =   $this->postVar['detailLuggage'] ?? null;
		$detailFlight   =   $this->postVar['detailFlight'] ?? null;
		$tourPlan       =   $this->postVar['tourPlan'] ?? null;
		$dropOffLocation=   $this->postVar['dropOffLocation'] ?? null;

        if (!is_null($paxAdult))        $arrUpdate['NUMBEROFADULT']     =   $paxAdult;
        if (!is_null($paxChild))        $arrUpdate['NUMBEROFCHILD']     =   $paxChild;
        if (!is_null($paxInfant))       $arrUpdate['NUMBEROFINFANT']    =   $paxInfant;
        if (!is_null($detailLuggage))   $arrUpdate['DETAILLUGGAGE']     =   $detailLuggage;
        if (!is_null($detailFlight))    $arrUpdate['DETAILFLIGHT']      =   $detailFlight;
        if (!is_null($tourPlan))        $arrUpdate['TOURPLAN']          =   $tourPlan;
        if (!is_null($dropOffLocation)) $arrUpdate['DROPOFFLOCATION']   =   $dropOffLocation;

        if (count($arrUpdate) === 0) returnHttpResponse(406, 'No data to update');

        $this->load->model('MainOperation');
        $procUpdate =   $this->MainOperation->updateData('t_reservation', $arrUpdate, array("BOOKINGCODE" => $bookingCode));

		if(!$procUpdate['status']) switchMySQLErrorCodeHttpResponse($procUpdate['errCode']);
        //ADD LOG/HISTORY RESERVATION UPDATE
        returnHttpResponse(200, 'Reservation detail updated successfully');
    }

    private function checkSignature(){
        $signatureHeader=   $this->input->get_request_header('BST-Signature');

        if (!isset($signatureHeader) || is_null($signatureHeader) || $signatureHeader === '') return returnHttpResponse(400, 'Signature header is missing');

        $timeStamp      =   new DateTime('now', new DateTimeZone('UTC'));
        $timeStamp      =   $timeStamp->getTimestamp();
        $timeStampMin   =   $timeStamp - 1200;
        $timeStampMax   =   $timeStamp + 1200;
        $isValidRequest =   false;
        $privateKey     =   ROKET_CS_AI_AGENT_WEBHOOK_PRIVATE_KEY;
        
        for($timeStampCheck = $timeStampMin; $timeStampCheck <= $timeStampMax; $timeStampCheck++) {
            $dataRequest    =   ['timestamp' => $timeStampCheck];
            $dataJSON       =   json_encode($dataRequest);
            $hmacSignature  =   hash_hmac('sha256', $dataJSON, $privateKey);

            if ($hmacSignature === $signatureHeader) {
                $isValidRequest = true;
                break;
            }
        }

        if (!$isValidRequest) return returnHttpResponse(401, 'Invalid signature', ['timestamp' => $timeStamp]);
        return true;
    }
}