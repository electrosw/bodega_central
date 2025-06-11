<?php

class Valorizacion_Model extends CI_Model{
    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
        session_start();
        $this->usuario = $_SESSION['IluzIntranet']['usu']['logname'];
        $this->load->model('Cobol_Model');
        date_default_timezone_set('America/Santiago');
    }

    function registrarGlosa($glosa){
        $data = array(
            'glo_glosa' => $glosa
        );
        $query = $this->db->insert('bodega_central.valorizacion_glosas', $data);
        if($query){
            $respuesta = array('key' => 1);
        }else{
            $respuesta = array('key' => 0, 'msj'=> 'Error al registrar la glosa en la BD.');
        }
        return $respuesta;
    }

    function listarGlosas(){
        $query      = $this->db->get('bodega_central.valorizacion_glosas');
        $respuesta  = array('glosas'=>$query->result(), 'key'=>1);
        return $respuesta;
    }

    function disponibleOrdenCompra($orden_compra){
        $sql = 'SELECT voc_id_valorizacion FROM bodega_central.valorizacion_oc WHERE voc_oc = ?';
        $query = $this->db->query($sql, array($orden_compra));
        if($query->num_rows() > 0){
            $respuesta = array('key'=>0, 'msj'=>'La orden de compra ya fue valorizada.');
        }else{
            $respuesta = array('key'=>1);
        }
        return (object) $respuesta;
    }

    function registrarValorizacion($valorizacion, $orden_compra, $finalizar){
        $flag_total_gastos              = 0;
        $flag_tipo_cambio               = 0;
        $flag_porcentaje_costos         = 0;
        $flag_tipo_cambio_reajustado    = 0;

        foreach($valorizacion->items as $item){
            $flag_total_gastos += floatval(str_replace('.','',$item->monto));
        }
        $flag_tipo_cambio            = floatval(str_replace('.','',$valorizacion->costo_pagado)) / floatval(str_replace(',','.',str_replace('.','',$valorizacion->costo_importacion))); 
        $flag_porcentaje_costos      = ($flag_total_gastos + floatval(str_replace('.','',$valorizacion->comision_banco))) / floatval(str_replace('.','',$valorizacion->costo_pagado));
        $flag_tipo_cambio_reajustado = $flag_tipo_cambio + ($flag_tipo_cambio * $flag_porcentaje_costos);

        if($flag_total_gastos != floatval($valorizacion->total_gastos)){
            $respuesta = array('key' => 0, 'msj'=> 'El total de costos no coincide con el monto de la valorización. '); 
            return $respuesta;
        }
        if($flag_tipo_cambio != floatval($valorizacion->tipo_cambio)){
            $respuesta = array('key' => 0, 'msj'=> 'El tipo de cambio no coincide con el monto de la valorización.'); 
            return $respuesta;
        }
        if($flag_porcentaje_costos != floatval($valorizacion->porcentaje_costos)){
            $respuesta = array('key' => 0, 'msj'=> 'El porcentaje de costos no coincide con el monto de la valorización.'); 
            return $respuesta;
        }
        if($flag_tipo_cambio_reajustado != floatval($valorizacion->tipo_cambio_reajustado)){
            $respuesta = array('key' => 0, 'msj'=> 'El tipo de cambio reajustado no coincide con el monto de la valorización.'); 
            return $respuesta;
        }
        

        $sql_val    = 'INSERT INTO bodega_central.valorizaciones (val_factura, val_din, val_rut_carpeta, val_comision_banco, val_costo_importacion, val_costo_pagado, val_tipo_cambio, val_porcentaje_costos, val_tipo_cambio_reajustado, val_fecha_hora, val_finalizada) VALUES (?,?,?,?,?,?,?,?,?,?,?) RETURNING val_id';
        $sql_items  = 'INSERT INTO bodega_central.valorizacion_costos (vco_id_valorizacion, vco_id_glosa, vco_monto, vco_observacion) VALUES (?,?,?,?)';
        $sql_oc     = 'INSERT INTO bodega_central.valorizacion_oc (voc_id_valorizacion, voc_fecha, voc_bodega, voc_glosa, voc_mensaje, voc_oc) VALUES (?,?,?,?,?,?) RETURNING voc_id';
        $sql_art_oc = 'INSERT INTO bodega_central.valorizacion_articulos_oc (vao_id_valorizacion_oc, vao_codigo, vao_ver, vao_nombre, vao_unidad_medida, vao_cantidad, vao_valor_m_extranjera, vao_valor_pesos) VALUES (?,?,?,?,?,?,?,?)';

        $this->db->trans_begin();
        
        $query_val = $this->db->query($sql_val, array(
            $valorizacion->factura,
            $valorizacion->din,
            $valorizacion->rut_carpeta,
            str_replace('.','',$valorizacion->comision_banco),
            str_replace(',','.',str_replace('.','',$valorizacion->costo_importacion)),
            str_replace('.','',$valorizacion->costo_pagado),
            round($valorizacion->tipo_cambio, 2),
            round($valorizacion->porcentaje_costos, 2),
            round($valorizacion->tipo_cambio_reajustado, 2),
            date('Y-m-d H:i:s'),
            $finalizar
        ));
        
        if($query_val){
            $valorizacion_id = $query_val->row()->val_id;
            foreach($valorizacion->items as $item){
                $query = $this->db->query($sql_items, array(
                    $valorizacion_id,
                    $item->glosa,
                    str_replace('.','',$item->monto),
                    $item->observacion
                ));
            }
            $query_oc = $this->db->query($sql_oc, array(
                $valorizacion_id,
                $orden_compra->fecha,
                $orden_compra->bodega,
                $orden_compra->glosa,
                $orden_compra->mensaje,
                $orden_compra->oc
            ));
            if($query_oc){
                $voc_id = $query_oc->row()->voc_id;
                foreach($orden_compra->articulos as $item){
                    $query = $this->db->query($sql_art_oc, array(
                        $voc_id,
                        $item->codigo,
                        $item->ver,
                        $item->nomart,
                        $item->umed,
                        $item->cant,
                        round(floatval(str_replace([ '.', ',' ], [ '', '.' ], $item->valorextra)), 2),
                        round(floatval($item->valorpesos), 2)
                    ));
                }
            }
        }
        else{ 
            $respuesta = array('key' => 0, 'msj'=> 'Error al registrar la valorización.'); 
        }
        
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $respuesta = array('key' => 0, 'msj'=> 'Error al registrar la valorización.'); 
        }
        else{
            $this->registrarLog(1, 4, $valorizacion_id); // Log de registro de valorización
            $this->db->trans_commit();
            $respuesta = array('key' => 1, 'msj'=> 'Valorización registrada correctamente.');        
        }
        return $respuesta;
    }

    function guardarValorizacion($valorizacion, $orden_compra, $finalizar){
        $flag_total_gastos              = 0;
        $flag_tipo_cambio               = 0;
        $flag_porcentaje_costos         = 0;
        $flag_tipo_cambio_reajustado    = 0;

        foreach($valorizacion->items as $item){
            $flag_total_gastos += floatval(str_replace('.','',$item->vco_monto));
        }
        $flag_tipo_cambio            = floatval(str_replace('.','',$valorizacion->val_costo_pagado)) / floatval(str_replace(',','.',str_replace('.','',$valorizacion->val_costo_importacion)));
        $flag_porcentaje_costos      = ($flag_total_gastos + floatval(str_replace('.','',$valorizacion->val_comision_banco))) / floatval(str_replace('.','',$valorizacion->val_costo_pagado));
        $flag_tipo_cambio_reajustado = $flag_tipo_cambio + ($flag_tipo_cambio * $flag_porcentaje_costos);

        if($flag_total_gastos != floatval($valorizacion->val_total_gastos)){
            $respuesta = array('key' => 0, 'msj'=> 'El total de costos no coincide con el monto de la valorización. '); 
            return $respuesta;
        }
        if($flag_tipo_cambio != floatval($valorizacion->val_tipo_cambio)){
            $respuesta = array('key' => 0, 'msj'=> 'El tipo de cambio no coincide con el monto de la valorización.'); 
            return $respuesta;
        }
        if($flag_porcentaje_costos != floatval($valorizacion->val_porcentaje_costos)){
            $respuesta = array('key' => 0, 'msj'=> 'El porcentaje de costos no coincide con el monto de la valorización.'); 
            return $respuesta;
        }
        if($flag_tipo_cambio_reajustado != floatval($valorizacion->val_tipo_cambio_reajustado)){
            $respuesta = array('key' => 0, 'msj'=> 'El tipo de cambio reajustado no coincide con el monto de la valorización.'); 
            return $respuesta;
        }
        

        $sql_val    = 'UPDATE bodega_central.valorizaciones SET val_factura=?, val_din=?, val_rut_carpeta=?, val_comision_banco=?, val_costo_importacion=?, val_costo_pagado=?, val_tipo_cambio=?, val_porcentaje_costos=?, val_tipo_cambio_reajustado=?, val_finalizada=? WHERE  val_id=?';
        $sql_del_items = 'DELETE FROM bodega_central.valorizacion_costos WHERE vco_id_valorizacion = ?';
        $sql_ins_items  = 'INSERT INTO bodega_central.valorizacion_costos (vco_id_valorizacion, vco_id_glosa, vco_monto, vco_observacion) VALUES (?,?,?,?)';
        $sql_upd_art_oc = 'UPDATE bodega_central.valorizacion_articulos_oc SET vao_valor_m_extranjera = ?, vao_valor_pesos = ? WHERE vao_id = ?';

        $this->db->trans_begin();
        
        $query_val = $this->db->query($sql_val, array(
            $valorizacion->val_factura,
            $valorizacion->val_din,
            $valorizacion->val_rut_carpeta,
            str_replace('.','',$valorizacion->val_comision_banco),
            str_replace(',','.',str_replace('.','',$valorizacion->val_costo_importacion)),
            str_replace('.','',$valorizacion->val_costo_pagado),
            round($valorizacion->val_tipo_cambio, 2),
            round($valorizacion->val_porcentaje_costos, 2),
            round($valorizacion->val_tipo_cambio_reajustado, 2),
            $finalizar,
            $valorizacion->val_id
        ));
        
        if($query_val){
            $query_del_items = $this->db->query($sql_del_items, array($valorizacion->val_id));
            if(!$query_del_items){
                $respuesta = array('key' => 0, 'msj'=> 'Error al eliminar los items de la valorización.'); 
                return $respuesta;
            }
            foreach($valorizacion->items as $item){
                $query = $this->db->query($sql_ins_items, array(
                    $valorizacion->val_id,
                    $item->vco_id_glosa,
                    str_replace('.','',$item->vco_monto),
                    $item->vco_observacion
                ));
            }
            foreach($orden_compra->articulos as $item){
                $query = $this->db->query($sql_upd_art_oc, array(
                    round(floatval(str_replace([ '.', ',' ], [ '', '.' ], $item->vao_valor_m_extranjera)), 2),
                    round(floatval($item->vao_valor_pesos), 2),
                    $item->vao_id
                ));
            }
            
        }
        else{ 
            $respuesta = array('key' => 0, 'msj'=> 'Error al registrar la valorización.'); 
        }
        
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $respuesta = array('key' => 0, 'msj'=> 'Error al registrar la valorización.'); 
        }
        else{
            $this->registrarLog(2, 4, $valorizacion->val_id); // Log de modificación de valorización
            $this->db->trans_commit();
            $respuesta = array('key' => 1, 'msj'=> 'Valorización registrada correctamente.');        
        }
        return $respuesta;
    }


    function listarValorizaciones(){
        $sql = 'SELECT *, (SELECT voc_oc FROM bodega_central.valorizacion_oc WHERE voc_id_valorizacion = val_id LIMIT 1) AS val_oc FROM bodega_central.valorizaciones ORDER BY val_fecha_hora DESC';
        $query = $this->db->query($sql);
        $respuesta = array('valorizaciones'=>$query->result(), 'key'=>1);
        return $respuesta;
    }

    function listarValorizacion($id_valorizacion){
        $sql = "SELECT *, trim(to_char(val_costo_importacion, '9G999G999G990D99')) as val_costo_importacion, trim(to_char(val_comision_banco, '9G999G999G999')) as val_comision_banco, trim(to_char(val_costo_pagado, '9G999G999G999')) as val_costo_pagado FROM bodega_central.valorizaciones WHERE val_id = ?";
        $query = $this->db->query($sql, array($id_valorizacion));
        if($query->num_rows() > 0){
            $respuesta = (object) array('valorizacion'=>$query->row(), 'key'=>1);
        }else{
            $respuesta = (object) array('key'=>0, 'msj'=>'No se encontró la valorización.');
        }
        return $respuesta;
    }

    function listarItemsValorizacion($id_valorizacion){
        $sql        = "SELECT *, trim(to_char(vco_monto, '9G999G999G999')) AS vco_monto FROM bodega_central.valorizacion_costos WHERE vco_id_valorizacion = ?";
        $query      = $this->db->query($sql, array($id_valorizacion));
        $respuesta  = $query->result();
        return $respuesta;
    }

    function listarOCValorizacion($id_valorizacion){
        $sql        = 'SELECT * FROM bodega_central.valorizacion_oc WHERE voc_id_valorizacion = ?';
        $query      = $this->db->query($sql, array($id_valorizacion));
        $respuesta  = $query->row();
        return $respuesta;
    }

    function listarArticulosOC($voc_id){
        $sql        = "SELECT *, trim(to_char(vao_valor_m_extranjera, '9G999G999G990D99')) as vao_valor_m_extranjera, (vao_valor_m_extranjera * vao_cantidad) as vao_total_extra, (vao_valor_pesos * vao_cantidad) as vao_total_pesos FROM bodega_central.valorizacion_articulos_oc WHERE vao_id_valorizacion_oc = ?";
        $query      = $this->db->query($sql, array($voc_id));
        $respuesta  = $query->result();
        return $respuesta;
    }

    function registrarLog($tipo_log, $tipo_reg, $id_reg){
        // Tipos de log:
            // 1: Registro 
            // 2: Modificación
            // 3: Eliminación
        // Tipos de registro:
            // 1: Ingreso bodega
            // 2: Salida bodega
            // 3: Certificado
            // 4: Valorización
        $sql = 'INSERT INTO bodega_central.log (log_tipo, log_usuario, log_fecha_hora, log_tipo_reg, log_id_reg) VALUES (?,?,?,?,?)';
        $query = $this->db->query($sql, array(
            $tipo_log,
            $this->usuario,
            date('Y-m-d H:i:s'),
            $tipo_reg,
            $id_reg 
        ));
    }

    /* ============================================= */
    /* ============================================= */

}

?>