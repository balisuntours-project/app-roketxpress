<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';
use Dompdf\Adapter\CPDF;
use Dompdf\Dompdf;
use Dompdf\Exception;
use Dompdf\Options;

class Pdf extends Dompdf{
	
    public $filename;
    public function __construct(){
        parent::__construct();
        $this->filename = "report-preview.pdf";
    }

    protected function ci(){
        return get_instance();
    }

    public function load_view($view, $data = array()){
        $html		= $this->ci()->load->view($view, $data, TRUE);
		$options	= new Options();
		$options->setIsRemoteEnabled(true);
        $this->load_html($html);
        $this->render();
		ob_end_clean();
	    $this->stream($this->filename, array("Attachment" => false));
		exit();
    }

    public function save_pdf($view, $data = array()){
        $html		= $this->ci()->load->view($view, $data, TRUE);
		$options	= new Options();
		$options->setIsRemoteEnabled(true);
        $this->load_html($html);
        $this->render();
		$output		= $this->output();
		file_put_contents($this->filename, $output);
    }
}