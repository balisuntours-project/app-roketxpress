<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Foto extends CI_controller {
	
	public function __construct(){
        parent::__construct();
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}
	
	public function sourceLogo($fileName){
		
		$explodeImage	=	explode(".", $fileName);
		$extension		=	end($explodeImage);
		$loc			=	PATH_SOURCE_LOGO.$fileName;
		
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
	
	public function bankLogo($fileName){
		
		$explodeImage	=	explode(".", $fileName);
		$extension		=	end($explodeImage);
		$loc			=	PATH_BANK_LOGO.$fileName;
		
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
			$this->defaultlogobank();
		}

	}
	
	public function transferReceipt($fileName){
		
		$explodeImage	=	explode(".", $fileName);
		$extension		=	end($explodeImage);
		$loc			=	PATH_TRANSFER_RECEIPT.$fileName;

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
	
	public function carCostReceipt($fileName){
		
		$explodeImage	=	explode(".", $fileName);
		$extension		=	end($explodeImage);
		$loc			=	PATH_CAR_COST_RECEIPT.$fileName;

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
	
	public function reimbursement($fileName){
		
		$explodeImage	=	explode(".", $fileName);
		$extension		=	end($explodeImage);
		$loc			=	PATH_REIMBURSEMENT_RECEIPT.$fileName;

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
	
	private function defaultlogobank(){

		$namefile	=	PATH_BANK_LOGO."default.png";
		$image 		= 	@imagecreatefrompng($namefile) or $image = 'brokenimage';
		
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
		
		$image 		= 	@imagecreatefrompng($loc) or $image = 'brokenimage';
		if($image	==	'brokenimage') {
			$this->noimage('brokenimage');	
			die();
		}
	
	}

}