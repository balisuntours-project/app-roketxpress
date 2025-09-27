<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File extends CI_controller {
	
	public function __construct(){
        parent::__construct();
    }
	
	public function index(){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden!';
		die();
	}

	public function reservationInvoice($fileName){
		echo file_get_contents(PATH_INVOICE_HTML_FILE.$fileName);	
	}
	
	public function reservationVoucher($fileName){
		
		$this->load->helper('file');
		$loc			=	PATH_VOUCHER_FILE.$fileName;
		
		if (file_exists($loc)) {
			
			$data = file_get_contents($loc);
			$mime = get_mime_by_extension($loc);

			header('Content-type:application/pdf');
			header('Content-disposition: inline; filename="'.$fileName.'"');
			header('content-Transfer-Encoding:binary');
			header('Accept-Ranges:bytes');
			@ readfile($loc);
								
		} else {
			$this->noFileFound();
		}
		
	}
	
	public function xlsxTransferList($fileName){
		
		$this->load->helper('file');
		$loc			=	PATH_EXCEL_TRANSFER_LIST_FILE.$fileName;
		
		if (file_exists($loc)) {
			
			$data	=	file_get_contents($loc);
			$mime	=	get_mime_by_extension($loc);
		
			header('Content-Type: application/vnd.ms-excel');
			header('Content-disposition: inline; filename="'.$fileName.'"');
			header('content-Transfer-Encoding:binary');
			header('Accept-Ranges:bytes');
			header('Cache-Control: max-age=0');
			@ readfile($loc);
								
		} else {
			$this->noFileFound();
		}
		
	}
	
	public function transferReceiptHTML($fileName){
		$fullFileNamePath	=	PATH_HTML_TRANSFER_RECEIPT.$fileName;
		if (file_exists($fullFileNamePath)) {
			$fileContent		=	file_get_contents($fullFileNamePath);
		} else {
			$fileContent		=	file_get_contents(PATH_HTML_TRANSFER_RECEIPT."unavailable.html");
		}
		
		echo $fileContent;
	}
	
	public function knowledge($fileName){
		$this->load->helper('file');
		$locFile	=	PATH_KNOWLEDGE_FILE.$fileName;
		
		if (file_exists($locFile)) {
			header('Content-Type: application/pdf');
			header('Content-Disposition: inline; filename="' . basename($locFile) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Accept-Ranges: bytes');
			@readfile($locFile);
		} else {
			$this->noFileFound();
		}
	}

	private function noFileFound(){
		echo "File not found";
		die();
	}
}