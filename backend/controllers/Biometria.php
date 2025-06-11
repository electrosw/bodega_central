<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Content-Type, Authorization");

class Biometria extends CI_Controller{
    public function __construct() {
        parent::__construct();
        $this->load->model('Biometria_Model');
        session_start();
        $this->usuario = $_SESSION['IluzIntranet']['usu']['logname'];
    }

    public function index(){}

    function listarEstantes(){
        $estantes = $this->Bodega_Model->listarEstantes();
        foreach($estantes as $estante){
            $estante->secciones = $this->Bodega_Model->listarSeccionesEstante($estante->est_id);
            foreach($estante->secciones as $seccion){
                $seccion->espacios = $this->Bodega_Model->listarEspaciosSeccion($seccion->sec_id, $estante->est_id);
            }
        }
        echo json_encode(array('estantes'=>$estantes));
    }

    function listarHuellasUsuario(){
        $huellas = $this->Biometria_Model->listarHuellasUsuario($this->usuario);
        if($huellas){
            $respuesta = array('key'=>1, 'msj'=>'OK', 'huellas'=>$huellas);
        }
        else{
            $respuesta = array('key'=>0, 'msj'=>'ERROR AL LISTAR HUELLAS.');
        }
        echo json_encode($respuesta);
    }
    
}

?>