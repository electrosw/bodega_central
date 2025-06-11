<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Fpdf_master {
        
    public function __construct() {
        
        require_once APPPATH.'controllers/fpdf/fpdf.php';
        //require_once APPPATH.'controllers/fpdf/fpdf_protection.php';
        
        $pdf = new FPDF();
        
        $pdf->AddPage();
        
        $CI =& get_instance();
        $CI->fpdf = $pdf;
        
        //require_once APPPATH.'libraries/Fpdf_protection.php';
    }
    
}