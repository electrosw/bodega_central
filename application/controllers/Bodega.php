<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Content-Type, Authorization");

class Bodega extends CI_Controller{
    public function __construct() {
        parent::__construct();
        /* $this->load->library('session');
		if(!$this->session->userdata('sesion_activa_rh')){ exit(1); } */
        $this->load->model('Bodega_Model');
        $this->load->model('Cobol_Model');
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

    function listarArticulosEspacio(){
        $id_espacio = $_POST['id_espacio'];
        $articulos  = $this->Bodega_Model->listarArticulosEspacio($id_espacio);
        echo json_encode($articulos);
    }

    function listarHistorialEspacio(){
        $id_espacio = $_POST['id_espacio'];
        $articulos  = $this->Bodega_Model->listarHistorialEspacio($id_espacio);
        echo json_encode($articulos);
    }

    /* function registrarEspacios(){
        $estantes = $this->Bodega_Model->listarEstantes();
        foreach($estantes as $estante){
            $estante->secciones = $this->Bodega_Model->listarSeccionesEstante($estante->est_id);
            foreach($estante->secciones as $seccion){
                if($estante->est_id <= 9 || $estante->est_id == 12 || $estante->est_id == 13){
                    $ancho = 100;
                    $alto  = 110;
                    $fondo = 120;
                    $peso  = 1200;
                    $espacios = 4;
                }
                else {
                    $ancho = 100;
                    $alto  = 110;
                    $fondo = 120;
                    $peso  = 2000;
                    $espacios = 4;
                }
                if($estante->est_id == 8){
                    $ancho = 100;
                    $alto  = 110;
                    $fondo = 120;
                    $peso  = 1200;
                    $espacios = 6;
                }
                if($estante->est_id >= 20 && $estante->est_id <= 24){
                    $ancho = 500;
                    $alto  = 600;
                    $fondo = 1200;
                    $peso  = 10000;
                    $espacios = 1;
                }
                
                $this->Bodega_Model->registrarEspacios($seccion->sec_id, $estante->est_id, $ancho, $alto, $fondo, $peso, $espacios);
            }
        }
    } */


    /* ============================================= */
    /* ||            INGRESO DE BODEGA            || */
    /* ============================================= */

    function listarSucursales(){
        $sucursales = $this->Bodega_Model->listarSucursales();
        echo json_encode($sucursales);
    }

    function listarEspaciosDisponibles(){
        $espacios   = $this->Bodega_Model->listarEspaciosDisponibles();
        foreach($espacios as $espacio){
            $espacio->esp_nombre = (string)$espacio->esp_estante.'-'.(string)$espacio->esp_seccion.'-'.(string)$espacio->esp_numero.' ('.(string)$espacio->esp_peso.'kg)';
        }
        echo json_encode($espacios);
    }

    function listarEspaciosDisponiblesPorPeso(){
        $peso       = $_POST['peso'];
        $espacios   = $this->Bodega_Model->listarEspaciosDisponibles($peso);
        echo json_encode($espacios);
    }

    function registrarIngreso(){
        $ingreso    = json_decode($_POST['ingreso']);
        $respuesta  = $this->Bodega_Model->registrarIngreso($ingreso);
        echo json_encode($respuesta);
    }

    function listarOrdenCompra(){
        $orden_compra = json_decode($_POST['orden_compra']);
        $respuesta    = $this->Cobol_Model->listarOrdenCompra($orden_compra);
        echo json_encode($respuesta);
    }

    function listarHistorialIngresos(){
        $tipo_filtro    = $_POST['tipo_filtro'];
        $fecha_desde    = $_POST['fecha_desde'];
        $fecha_hasta    = $_POST['fecha_hasta'];
        $id_ingreso     = $_POST['id_ingreso'];
        if($tipo_filtro == 1){
            $respuesta      = $this->Bodega_Model->listarHistorialIngresosPorFecha($fecha_desde, $fecha_hasta);
        }
        else if($tipo_filtro == 2){
            $respuesta      = $this->Bodega_Model->listarHistorialIngresosPorID($id_ingreso);
        }
        echo json_encode($respuesta);
    }

    function listarDetalleIngreso(){
        $id_ingreso             = $_POST['id_ingreso'];
        $respuesta              = $this->Bodega_Model->listarIngreso($id_ingreso);
        $respuesta->articulos   = $this->Bodega_Model->listarDetalleIngreso($id_ingreso);
        echo json_encode($respuesta);
    }

 
    /* ============================================= */
    /* ============================================= */


    /* ============================================= */
    /* ||            SALIDA DE BODEGA             || */
    /* ============================================= */


    function listarProductosSucursal(){
        $id_sucursal = json_decode($_POST['id_sucursal']);
        $respuesta   = $this->Bodega_Model->listarProductosSucursal($id_sucursal);
        echo json_encode($respuesta);
    }

    function listarOrdenesTraspaso(){
        $bodega     = $_POST['bodega'];
        $respuesta  = $this->Cobol_Model->listarOrdenesTraspaso($bodega);
        echo json_encode($respuesta);
    }

    function listarOrdenTraspaso(){
        $id_orden   = $_POST['id_orden'];
        $respuesta  = $this->Cobol_Model->listarOrdenTraspaso($id_orden);
        $respuesta->mensaje_nuevo = '';
        foreach($respuesta->codigos as $codigo){
            $codigo->articulo_seleccionado = '';
            $codigo->estantes = $this->Bodega_Model->listarEstantesPorArticulo($codigo->codigo);
            foreach($codigo->estantes as $estante){
                $articulos = $this->Bodega_Model->listarArticulosEspacio($estante->esp_id);
                foreach($articulos as $articulo){
                    if($articulo->ari_codigo == $codigo->codigo){
                        $estante->articulo = $articulo;
                    }
                }
            }
            if(count($codigo->estantes) == 1){
                $codigo->articulo_seleccionado = $codigo->estantes[0]->ari_id;
            }
        }
        echo json_encode($respuesta);
    }

    function registrarSalida(){
        $salida     = json_decode($_POST['salida']);
        $respuesta  = array('key'=>0, 'msj'=>'ERROR INESPERADO.');
        $reg_cobol  = $this->Cobol_Model->registrarSalida($salida);
        if($reg_cobol->key == 1){
            $id_salida = $reg_cobol->data->guia;
            $respuesta = $this->Bodega_Model->registrarSalida($salida, $id_salida);
            $respuesta = array('key'=>1);
        }
        else {
            $respuesta = $reg_cobol;
        }
        echo json_encode($respuesta);
    }

    function listarHistorialSalidas(){
        $tipo_filtro    = $_POST['tipo_filtro'];
        $fecha_desde    = $_POST['fecha_desde'];
        $fecha_hasta    = $_POST['fecha_hasta'];
        $id_salida      = $_POST['id_salida'];
        $orden_traspaso = $_POST['orden_traspaso'];
        if($tipo_filtro == 1){
            $respuesta      = $this->Bodega_Model->listarHistorialSalidasPorFecha($fecha_desde, $fecha_hasta);
        }
        else if($tipo_filtro == 2){
            $respuesta      = $this->Bodega_Model->listarHistorialSalidasPorID($id_salida);
        }
        else if($tipo_filtro == 3){
            $respuesta      = $this->Bodega_Model->listarHistorialSalidasPorOT($orden_traspaso);
        }
        echo json_encode($respuesta);
    }

    function listarDetalleSalida(){
        $id_salida              = $_POST['id_salida'];
        $respuesta              = $this->Bodega_Model->listarSalida($id_salida);
        $respuesta->articulos   = $this->Bodega_Model->listarDetalleSalida($id_salida);
        echo json_encode($respuesta);
    }

    /* ============================================= */
    /* ============================================= */


    /*  ============================================= */
    /*  ||                INFORMES                 || */
    /*  ============================================= */

    function listarInforme(){
        $informe = '';
        $tipo_informe = $_POST['tipo_informe'];
        if($tipo_informe == 1){ 
            $informe = $this->Bodega_Model->listarArticulosEnBodega();  
        }
        if($tipo_informe == 2){
            $fecha_desde = $_POST['fecha_desde'];
            $fecha_hasta = $_POST['fecha_hasta'];
            $informe = $this->Bodega_Model->listarIngresosPorFecha($fecha_desde, $fecha_hasta);     
        }
        if($tipo_informe == 3){
            $fecha_desde = $_POST['fecha_desde'];
            $fecha_hasta = $_POST['fecha_hasta']; 
            $informe = $this->Bodega_Model->listarSalidasPorFecha($fecha_desde, $fecha_hasta);      
        }
        echo json_encode($informe);
    }

    /* ============================================= */
    /* ============================================= */
    
}

?>