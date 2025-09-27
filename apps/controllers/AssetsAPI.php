<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AssetsAPI extends CI_controller {
	
	public function __construct(){
        parent::__construct();
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function mail($fileName, $uniqueId){
		
		$this->load->model('MainOperation');

		$this->MainOperation->updateData('t_reservationmailreview', ['DATETIMEREAD' => date('Y-m-d H:i:s'), 'STATUSREAD' => 1], 'IDUNIQUE', $uniqueId);
		$namefile	=	PATH_ASSETS.'mail/'.$fileName;
		$image 		= 	@imagecreatefrompng($namefile) or $image = 'brokenimage';
		
		if($image	==	'brokenimage') {
			$this->noimage('brokenimage');	
			die();
		}

		header	("Content-Type: image/png");
		imagepng		($image,NULL);
		imagedestroy	($image);

	}

	public function mailReview($fileName, $uniqueId){
		
		$this->load->model('MainOperation');

		$this->MainOperation->updateData('t_reservationmailreview', ['DATETIMEREAD' => date('Y-m-d H:i:s'), 'STATUSREAD' => 1], 'IDUNIQUE', $uniqueId);
		$namefile	=	PATH_ASSETS.'mail/'.$fileName;
		$image 		= 	@imagecreatefrompng($namefile) or $image = 'brokenimage';
		
		if($image	==	'brokenimage') {
			$this->noimage('brokenimage');	
			die();
		}

		header	("Content-Type: image/png");
		imagepng		($image,NULL);
		imagedestroy	($image);

	}

	public function mailReconfirmation($fileName, $uniqueId){
		
		$this->load->model('MainOperation');
		
		if($uniqueId != 'nodata'){
			try {
				$idReservationReconfirmation	=	decodeStringKeyFunction(base64_decode($uniqueId), DEFAULT_KEY_ENCRYPTION);
				$this->MainOperation->updateData('t_reservationreconfirmation', ['DATETIMEREAD' => date('Y-m-d H:i:s'), 'STATUSREAD' => 1], 'IDRESERVATIONRECONFIRMATION', $idReservationReconfirmation);
			} catch (Exception $e) {
			}
		}
		
		$namefile	=	PATH_ASSETS.'mail/'.$fileName;
		$image 		= 	@imagecreatefrompng($namefile) or $image = 'brokenimage';
		
		if($image	==	'brokenimage') {
			$this->noimage('brokenimage');	
			die();
		}

		header	("Content-Type: image/png");
		imagepng		($image,NULL);
		imagedestroy	($image);

	}

	public function mailReconfirmationAttachment($fileName){	
		$explodeImage	=	explode(".", $fileName);
		$extension		=	end($explodeImage);
		$loc			=	PATH_EMAIL_RECONFIRMATION_THREAD_ATTACHMENT.$fileName;
		
		if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG" || $extension == "JPEG"){				
			$image 		= 	@imagecreatefromjpeg($loc) or $image = 'brokenimage';
			if($image	==	'brokenimage') {
				$this->noimage('brokenimage');	
				die();
			}

			header	("Content-Type: image/jpeg");
			imagejpeg		($image,NULL);
			imagedestroy	($image);
		} else if($extension == "png" || $extension == "PNG"){
			$image 		= 	@imagecreatefrompng($loc) or $image = 'brokenimage';
			if($image	==	'brokenimage') {
				$this->noimage('brokenimage');	
				die();
			}
			
			$background = imagecolorallocatealpha($image,0,0,0,127);
			imagecolortransparent($image, $background);
			imagealphablending($image, false);
			imagesavealpha($image, true);

			header	("Content-Type: image/png");
			imagepng		($image,NULL);
			imagedestroy	($image);		
		} else {
			$this->noimage("noimage");
		}
	}
	
	private function noimage($jenis){

		header	("Content-Type: image/jpeg");
		$namefile	=	$jenis == "noimage" ? PATH_STORAGE."noimage.jpg" : PATH_STORAGE."errimage.jpg";
		$image 		= 	imagecreatefromjpeg($namefile);
		imagejpeg		($image,NULL);
		imagedestroy	($image);
		
	}

}