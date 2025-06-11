<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function revisarSincronizacion(){
    $respuesta = [];
    $ci =& get_instance();
    $ci->load->database();
    $sql_log    = "SELECT * FROM sincronizacion.log_cambios WHERE loc_estado = false";
    $sql_emp    = "SELECT * FROM personal.datos_sucursal LIMIT 1";    
    $registros  = $ci->db->query($sql_log)->result();
    $empresa    = $ci->db->query($sql_emp)->row();
 
    foreach($registros as $registro){
        $error = new stdClass();
        $error->key = 1;
        $cambio_enviado = json_decode(enviaCambio($registro->loc_consulta, $empresa->dts_empresa, $empresa->dts_bodega));
        if($cambio_enviado->key == 1){
            if(!actualizaLogCambio($registro->loc_id)){
                $error->key = 2;
                $error->msj = date('Y-m-d H:i:s').'  ERROR AL CAMBIAR ESTADO DE LOG. ID LOG: '.$registro->loc_id;
            }
        }
        else{
            $error->key = 0;
            $error->msj = date('Y-m-d H:i:s').'  ERROR AL ENVIAR CAMBIO AL SERVIDOR CENTRAL. ID LOG: '.$registro->loc_id. ' ERROR: '.json_encode($cambio_enviado);
        }
        if($error->key != 1){
            $respuesta[] = $error;
        }
    }
    return $respuesta;
}

function enviaCambio($consulta, $empresa, $bodega){
    $resp = array('key'=>1);
    $parametros = [
        'funcion'   => 'actualizaCambios',
        'consulta'  => $consulta,
        'empresa'   => $empresa,
        'bodega'    => $bodega
    ];

    $url = "https://bdcentral.inverluz.cl/rhumanos/index.php";
    try{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $datos = curl_exec ($ch);
        $datos = json_decode($datos);
        if(!empty($datos) && $datos != null && $datos != ''){
            if($datos->key != 1){
                $resp = $datos;
            }
        }
    }
    catch(Exception $e){
        $resp = array('key'=>0, 'msj'=>$e, 'datos'=>$datos);
    }
    return json_encode($resp);
}

function actualizaLogCambio($id_log){
    $respuesta = false;
    $ci =& get_instance();
    $ci->load->database();
    $sql = "UPDATE sincronizacion.log_cambios SET loc_estado=true WHERE loc_id=?";
    $ci->db->trans_begin();
    $ci->db->query($sql, array($id_log));
    if ($ci->db->trans_status() === FALSE){
        $ci->db->trans_rollback();
    }
    else{
        $ci->db->trans_commit();
        $respuesta = true;
    }
    return $respuesta;
}

function agregaLogCambio($consultas){
    $respuesta = true;
    $fecha_hora = date('Y-m-d H:i:s');
    $ci =& get_instance();
    $ci->load->database();
    $sql = "INSERT INTO sincronizacion.log_cambios (loc_consulta, loc_fecha_hora) VALUES(?,?)";
    $ci->db->trans_begin();

    foreach($consultas as $consulta){
        $ci->db->query($sql, array($consulta, $fecha_hora));
    }

    if ($ci->db->trans_status() === FALSE){
        $ci->db->trans_rollback();
        $respuesta = false;
    }
    else{
        $ci->db->trans_commit();
    }
    return $respuesta;
}