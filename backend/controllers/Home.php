<?php
 defined('BASEPATH') OR exit('No direct script access allowed');    
 class Home extends CI_Controller {  
	public function __construct() {
		parent::__construct();
		$this->load->helper('constantes_helper');
    }    

 	public function index(){  
		//$this->load->view('home');
		$url_login = base_url();
		if(!$this->validaSesion() && $url_login != 'https://dev-bodega-central.inverluz.cl'){
			header("Location: $url_login");
			die();
		}
		else {
			$this->load->view('home');    
		}
	} 
	 
	function validaSesion(){
		//return true;
		$respuesta = false;
		session_start();
		if(isset($_SESSION['IluzIntranet'])){ 
			$respuesta = true;
		}
		return $respuesta;
	}

	function estadoSesion(){
		if($this->validaSesion()){
			$resp = json_encode(array('key'=>1,'msg'=>'Sesion iniciada.'));
		}
		else{
			$resp = json_encode(array('key'=>0,'msg'=>'Sesion no iniciada.'));
		}
		echo $resp;
	}
 }