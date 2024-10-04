<?php

class Usuarios extends CI_Controller{
    public function __construct() {
        parent::__construct();
        /* $this->load->library('session');
		if(!$this->session->userdata('sesion_activa_rh')){ exit(1); } */
        $this->load->model('Usuarios_Model');
    }

    public function index(){}


    function listarUsuarios(){
        $usuarios = $this->Usuarios_Model->listarUsuarios();
        echo json_encode(array('usuarios'=>$usuarios));
    }

    function registrarUsuario(){
        $usuario     = json_decode($_POST['usuario']);
        //$usuario->dv = explode($usuario->rut, '-')[1];
        $resp        = $this->Usuarios_Model->registrarUsuario($usuario);
        echo json_encode($resp);
    }
}

?>