<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Content-Type, Authorization");

class Valorizacion extends CI_Controller{
    public function __construct() {
        parent::__construct();
        $this->load->model('Valorizacion_Model');
        $this->load->model('Cobol_Model');
    }

    public function index(){}

    function listarOrdenCompra(){
        $orden_compra = $_POST['orden_compra'];
        /* $disponible   = $this->Valorizacion_Model->disponibleOrdenCompra($orden_compra);
        if($disponible->key == 0){
            $respuesta = (object)array(
                'key'   => 0,
                'msj'   => 'La orden de compra ya fue valorizada.'
            );
            echo json_encode($respuesta);
            return;
        } */
        // DESCOMENTAR PARA HABILITAR VALIDACIÓN DE ORDEN DE COMPRA
        $respuesta    = $this->Cobol_Model->listarOrdenCompra($orden_compra);
        echo json_encode($respuesta);
    }

    function registrarGlosa(){
        $glosa      = ucfirst(strtolower($_POST['nueva_glosa']));
        $respuesta  = $this->Valorizacion_Model->registrarGlosa($glosa);
        echo json_encode($respuesta);
    }

    function listarGlosas(){
        $respuesta = $this->Valorizacion_Model->listarGlosas();
        echo json_encode($respuesta);
    }

    function registrarValorizacion(){ // Registrar valorización nueva
        $valorizacion = json_decode($_POST['valorizacion']);
        $orden_compra = json_decode($_POST['orden_compra']);
        $finalizar    = json_decode($_POST['finalizar']);
        $respuesta    = $this->Valorizacion_Model->registrarValorizacion($valorizacion, $orden_compra, $finalizar);
        /* $respuesta = (object)array(
            'valorizacion' => $valorizacion,
            'orden_compra' => $orden_compra,
            'finalizar'    => $finalizar,
            'key'          => 0,
            'msg'          => 'Error al guardar la valorización.'
        ); */
        echo json_encode($respuesta);
    }

    function guardarValorizacion(){ // Guardar cambios en valorización existente
        $valorizacion = json_decode($_POST['valorizacion']);
        $orden_compra = json_decode($_POST['orden_compra']);
        $finalizar    = json_decode($_POST['finalizar']);
        $respuesta    = $this->Valorizacion_Model->guardarValorizacion($valorizacion, $orden_compra, $finalizar);
        echo json_encode($respuesta);
    }

    function listarValorizaciones(){
        $valorizaciones = $this->Valorizacion_Model->listarValorizaciones();
        echo json_encode($valorizaciones);
    }

    function listarValorizacion(){
        $id_valorizacion = $_GET['id_valorizacion'];
        $resp    = $this->Valorizacion_Model->listarValorizacion($id_valorizacion);
        if($resp->key == 1){
            $resp->valorizacion->items        = $this->Valorizacion_Model->listarItemsValorizacion($id_valorizacion);
            $resp->valorizacion->orden_compra = $this->Valorizacion_Model->listarOCValorizacion($id_valorizacion);
            $resp->valorizacion->orden_compra->articulos = $this->Valorizacion_Model->listarArticulosOC($resp->valorizacion->orden_compra->voc_id);
        }
        echo json_encode($resp);
    }

    /* ============================================= */
    /* ============================================= */
    
}

?>