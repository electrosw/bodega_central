<?php

class Bodega_Model extends CI_Model{
    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
        session_start();
        $this->usuario = $_SESSION['IluzIntranet']['usu']['logname'];
        $this->load->model('Cobol_Model');
    }

    function listarEstantes(){
        $sql    = 'SELECT * FROM bodega_central.estantes ORDER BY est_posicion';
        $query  = $this->db->query($sql);
        return $query->result();
    }

    function listarSeccionesEstante($id_estante){
        $sql    = 'SELECT * FROM bodega_central.secciones WHERE sec_estante=? ORDER BY sec_id';
        $query  = $this->db->query($sql, array($id_estante));
        return $query->result();
    }
    
    function listarEspaciosSeccion($id_seccion, $id_estante){
        $sql    = 'SELECT * FROM bodega_central.espacios WHERE esp_seccion=? AND esp_estante=? ORDER BY esp_id DESC';
        $query  = $this->db->query($sql, array($id_seccion, $id_estante));
        return $query->result();
    }

    function certificarArticulo($id_articulo, $archivo){
        $sql_art    = 'UPDATE bodega_central.articulos_ingreso SET ari_certificado=true WHERE ari_id=?';
        $sql_crt    = 'INSERT INTO bodega_central.certificados (crt_articulo, crt_archivo, crt_fecha_hora, crt_usuario) VALUES(?,?,?,?)';
        $this->db->trans_begin();
        $this->db->query($sql_art, array($id_articulo));
        $this->db->query($sql_crt, array($id_articulo, $archivo, date('Y-m-d H:i:s'), $this->usuario));
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $respuesta = array('key'=>0, 'msj'=>'ERROR AL CERTIFICAR ARTICULO.');
        }
        else {
            $this->db->trans_commit();
            $respuesta = array('key'=>1, 'msj'=>'ARTICULO CERTIFICADO.');
        }
        return $respuesta;
    }

    function registrarEspacios($id_seccion, $id_estante, $ancho, $alto, $fondo, $peso, $espacios){
        $sql    = 'INSERT INTO bodega_central.espacios (esp_numero,esp_seccion, esp_estante, esp_ancho, esp_alto, esp_fondo, esp_peso) VALUES(?,?,?,?,?,?,?)';
        $this->db->trans_begin();
        for($i=1; $i<=$espacios; $i++){
            if($id_estante == 8 && ($i == 1 || $i == 2 || $i == 3)){
                if($id_seccion != 13){
                    $this->db->query($sql, array($i, $id_seccion, $id_estante, $ancho, 60, $fondo, 200));
                }
            }
            else{
                $this->db->query($sql, array($i, $id_seccion, $id_estante, $ancho, $alto, $fondo, $peso));
            }
        }
        
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        }
        else{
            $this->db->trans_commit();
        }
    }

    function listarArticulosEspacio($id_espacio){
        /* $sql0 = "SELECT *,
                CASE WHEN ars_cantidad IS NOT NULL THEN (ari_cantidad-ars_cantidad) 
                ELSE ari_cantidad END AS ari_cantidad
                FROM bodega_central.articulos_ingreso 
                INNER JOIN bodega_central.ingresos ON ing_id=ari_ingreso
                LEFT JOIN bodega_central.articulos_salida ON ari_id=ars_articulo_ingreso
                WHERE ari_espacio=? AND ((ari_cantidad-ars_cantidad) > 0 OR ars_cantidad IS NULL)"; */

        $sql   = "SELECT *, (ari_cantidad - (SELECT COALESCE(SUM(ars_cantidad), 0) FROM bodega_central.articulos_salida WHERE ars_articulo_ingreso=ari_id) ) as ari_cantidad
                FROM bodega_central.articulos_ingreso 
                INNER JOIN bodega_central.ingresos ON ing_id=ari_ingreso
                LEFT JOIN bodega_central.certificados ON ari_id=crt_articulo
                WHERE ari_espacio=? AND (ari_cantidad > (SELECT COALESCE(SUM(ars_cantidad), 0) FROM bodega_central.articulos_salida WHERE ars_articulo_ingreso=ari_id))";
        $query  = $this->db->query($sql,  array($id_espacio));
        return $query->result();
    } 

    function listarHistorialEspacio($id_espacio){
        $sql = "SELECT * FROM bodega_central.articulos_salida
                INNER JOIN bodega_central.articulos_ingreso ON ari_id=ars_articulo_ingreso 
                INNER JOIN bodega_central.ingresos ON ari_ingreso=ing_id
                INNER JOIN bodega_central.salidas ON ars_salida=sld_id
                WHERE ari_espacio=?";
        $query  = $this->db->query($sql,  array($id_espacio));
        return $query->result();
    }



    /* ============================================= */
    /* ||            INGRESO DE BODEGA            || */
    /* ============================================= */

    function listarSucursales(){
        $sql = "SELECT * FROM bodega_central.sucursales";
        $query  = $this->db->query($sql);
        return $query->result();
    }

    function listarEspaciosDisponibles(){
        $sql = "SELECT * FROM bodega_central.espacios WHERE esp_ocupado=false ORDER BY esp_estante ASC, esp_seccion ASC, esp_id ASC";
        $query  = $this->db->query($sql);
        return $query->result();
    }

    function listarEspaciosDisponiblesPorPeso($peso){
        $sql = "SELECT * FROM bodega_central.espacios WHERE esp_peso >= ? AND esp_ocupado=false ORDER BY esp_estante ASC, esp_seccion ASC, esp_id ASC";
        $query  = $this->db->query($sql, $peso);
        return $query->result();
    }

    function registrarIngreso($ingreso){
        $registro_cobol = $this->Cobol_Model->registrarIngreso($ingreso);
        if($registro_cobol->key == 1){

            if($ingreso->tipo_doc_asociado          == '') $ingreso->tipo_doc_asociado          = null;
            if($ingreso->doc_asociado               == '') $ingreso->doc_asociado               = null;
            if($ingreso->fecha_estimada_despacho    == '') $ingreso->fecha_estimada_despacho    = null;
            if($ingreso->orden_transporte           == '') $ingreso->orden_transporte           = null;
            if($ingreso->fecha_transporte           == '') $ingreso->fecha_transporte           = null;
            if($ingreso->peso_transporte            == '') $ingreso->peso_transporte            = null;
            
            $id_ingreso     = $registro_cobol->data->guia;
            $fecha_ingreso  = date('Y-m-d H:i:s');
            $respuesta      = array('key'=>1, 'msj'=>'REGISTRADO.', 'registro_cobol'=>$registro_cobol);

            $sql_ing   = "INSERT INTO bodega_central.ingresos (ing_id, ing_orden_compra, ing_tipo_documento, ing_documento, ing_despacho_estimado, ing_fecha_ingreso, ing_rut_transportista, ing_dv_transportista, ing_orden_transporte, ing_fecha_transporte, ing_peso_transporte, ing_observacion) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"; 
            $sql_art   = 'INSERT INTO bodega_central.articulos_ingreso (ari_codigo, ari_ver, ari_nombre, ari_cantidad, ari_unidad_medida, ari_espacio, ari_ingreso, ari_certificado) VALUES (?,?,?,?,?,?,?,?)';
            $sql_esp   = 'UPDATE bodega_central.espacios SET esp_ocupado=true WHERE esp_id=?';

            $this->db->trans_begin();
            $this->db->query($sql_ing, array($id_ingreso, $ingreso->oc, $ingreso->tipo_doc_asociado, $ingreso->doc_asociado, $ingreso->fecha_estimada_despacho, $fecha_ingreso, $ingreso->rut_transportista, $ingreso->dv_transportista, $ingreso->orden_transporte, $ingreso->fecha_transporte, $ingreso->peso_transporte, $ingreso->observacion));

            foreach($ingreso->articulos as $articulo){
                if($articulo->cant_recibida > 0){
                    if($articulo->certificacion         == true  ){ $articulo->certificacion = false; }
                    else if($articulo->certificacion    == false ){ $articulo->certificacion = null;  }
                    foreach($articulo->estantes as $estante){
                        $this->db->query($sql_art, array($articulo->codigo, $articulo->ver, $articulo->nomart, $estante->cantidad, $articulo->umed, $estante->estante, $id_ingreso, $articulo->certificacion));
                        $this->db->query($sql_esp, array($estante->estante));
                    }
                }
            }
            
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $respuesta = array('key'=>0, 'msj'=>'ERROR AL REGISTRAR INGRESO EN BD.');
            }
            else {
                $this->db->trans_commit(); 
            }
        }
        else {
            $respuesta = array('key'=>0, 'msj'=>'ERROR AL REGISTRAR INGRESO EN COBOL.', 'data'=>$registro_cobol);
        }

        return $respuesta;
    }

    function listarHistorialIngresosPorFecha($fecha_desde, $fecha_hasta){
        $sql   = "SELECT * FROM bodega_central.ingresos WHERE DATE(ing_fecha_ingreso) >= ? AND DATE(ing_fecha_ingreso) <= ? ORDER BY ing_fecha_ingreso DESC";
        $query = $this->db->query($sql, array($fecha_desde, $fecha_hasta));
        return $query->result();
    }

    function listarHistorialIngresosPorId($id_ingreso){
        $sql   = "SELECT * FROM bodega_central.ingresos WHERE ing_id=? ORDER BY ing_fecha_ingreso DESC";
        $query = $this->db->query($sql, array($id_ingreso));
        return $query->result();
    }

    function listarIngreso($id_ingreso){
        $sql   = "SELECT * FROM bodega_central.ingresos WHERE ing_id=?";
        $query = $this->db->query($sql, array($id_ingreso));
        return $query->row();
    }

    function listarDetalleIngreso($id_ingreso){
        $sql   = "SELECT * FROM bodega_central.articulos_ingreso 
                INNER JOIN bodega_central.espacios ON ari_espacio = esp_id WHERE ari_ingreso=?";
        $query = $this->db->query($sql, array($id_ingreso));
        return $query->result();
    }

    /* ============================================= */
    /* ============================================= */


    /* ============================================= */
    /* ||            SALIDA DE BODEGA             || */
    /* ============================================= */

    function listarProductosSucursal($id_sucursal){
        $sql   = "SELECT *, false AS seleccionado FROM bodega_central.articulos_ingreso
                  INNER JOIN bodega_central.espacios ON esp_id = ari_espacio
                  WHERE ari_fecha_despacho IS NULL AND ari_sucursal=?";
        $query = $this->db->query($sql, $id_sucursal);
        return $query->result();
    }

    function listarEstantesPorArticulo($codigo){
        $sql   = "SELECT *, (ari_cantidad - (SELECT COALESCE(SUM(ars_cantidad), 0) FROM bodega_central.articulos_salida WHERE ars_articulo_ingreso=ari_id) ) as ari_cantidad
                FROM bodega_central.articulos_ingreso 
                INNER JOIN bodega_central.ingresos ON ing_id=ari_ingreso
                INNER JOIN bodega_central.espacios ON esp_id=ari_espacio
                WHERE ari_codigo=? AND ari_certificado<>false AND (ari_cantidad > (SELECT COALESCE(SUM(ars_cantidad), 0) FROM bodega_central.articulos_salida WHERE ars_articulo_ingreso=ari_id))";
        $query = $this->db->query($sql, $codigo);
        return $query->result();
    }

    function registrarSalida($salida, $id_salida){
        $respuesta      = array('key'=>0, 'msj'=>'ERROR AL REGISTRAR SALIDA EN BD.');
        $sql_salida     = "INSERT INTO bodega_central.salidas (sld_id, sld_fecha_salida, sld_bodega_destino, sld_observacion, sld_orden_traspaso) VALUES (?,?,?,?,?)";
        $sql_art_salida = "INSERT INTO bodega_central.articulos_salida (ars_articulo_ingreso, ars_cantidad, ars_salida) VALUES (?,?,?)";

        $sql_id_espacio = "SELECT ari_espacio FROM bodega_central.articulos_ingreso WHERE ari_id=?";
        $sql_art_por_espacio   = "SELECT COUNT(ari_cantidad) AS cantidad FROM bodega_central.articulos_ingreso AS ing
                                WHERE (ing.ari_cantidad > (SELECT COALESCE(SUM(ars_cantidad), 0) FROM bodega_central.articulos_salida WHERE ars_articulo_ingreso=ing.ari_id))
                                AND ing.ari_espacio = ?";
        $sql_upd_espacio = "UPDATE bodega_central.espacios SET esp_ocupado=false WHERE esp_id=?";

        $this->db->trans_begin();
        $this->db->query($sql_salida, array($id_salida, date('Y-m-d H:i:s'), $salida->bod, $salida->detalle->mensaje_nuevo, $salida->documento));

        foreach($salida->detalle->codigos as $articulo){
            foreach($articulo->estantes_seleccionados as $estante){
                $this->db->query($sql_art_salida, array($estante->id_articulo_ingreso, $estante->cantidad, $id_salida));
                $id_espacio = $this->db->query($sql_id_espacio, array($estante->id_articulo_ingreso))->row()->ari_espacio;

                $articulos_espacio  = $this->db->query($sql_art_por_espacio,  array($id_espacio))->row()->cantidad;
                if($articulos_espacio == 0){
                    $this->db->query($sql_upd_espacio, array($id_espacio));
                }
            }
        }

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        }
        else {
            $this->db->trans_commit(); 
            $respuesta = array('key'=>1, 'msj'=>'REGISTRADO CORRECTAMENTE.');
        }
        return $respuesta;
    }

    function listarHistorialSalidasPorFecha($fecha_desde, $fecha_hasta){
        $sql   = "SELECT * FROM bodega_central.salidas WHERE DATE(sld_fecha_salida) >= ? AND DATE(sld_fecha_salida) <= ? ORDER BY sld_fecha_salida DESC";
        $query = $this->db->query($sql, array($fecha_desde, $fecha_hasta));
        return $query->result();
    }

    function listarHistorialSalidasPorId($id_salida){
        $sql   = "SELECT * FROM bodega_central.salidas WHERE sld_id=? ORDER BY sld_fecha_salida DESC";
        $query = $this->db->query($sql, array($id_salida));
        return $query->result();
    }

    function listarHistorialSalidasPorOT($id_salida){
        $sql   = "SELECT * FROM bodega_central.salidas WHERE sld_orden_traspaso=? ORDER BY sld_fecha_salida DESC";
        $query = $this->db->query($sql, array($id_salida));
        return $query->result();
    }

    function listarSalida($id_salida){
        $sql   = "SELECT * FROM bodega_central.salidas WHERE sld_id=?";
        $query = $this->db->query($sql, array($id_salida));
        return $query->row();
    }

    function listarDetalleSalida($id_salida){
        $sql   = "SELECT * FROM bodega_central.articulos_salida 
                INNER JOIN bodega_central.articulos_ingreso ON ari_id = ars_articulo_ingreso
                INNER JOIN bodega_central.espacios ON ari_espacio = esp_id
                WHERE ars_salida=?";
        $query = $this->db->query($sql, array($id_salida));
        return $query->result();
    }

    /* ============================================= */
    /* ============================================= */


    /*  ============================================= */
    /*  ||                INFORMES                 || */
    /*  ============================================= */

    function listarArticulosEnBodega(){
        /* $sql   = "SELECT * FROM bodega_central.articulos_ingreso
                  INNER JOIN bodega_central.espacios ON esp_id = ari_espacio
                  INNER JOIN bodega_central.ingresos ON ing_id = ari_ingreso
                  WHERE ari_salida IS NULL"; */
                  $sql = "SELECT *,
                  CASE WHEN ars_cantidad IS NOT NULL THEN (ari_cantidad-ars_cantidad) 
                  ELSE ari_cantidad END AS ari_cantidad
                  FROM bodega_central.articulos_ingreso 
                  INNER JOIN bodega_central.ingresos ON ing_id=ari_ingreso
                  INNER JOIN bodega_central.espacios ON esp_id = ari_espacio
                  LEFT JOIN bodega_central.articulos_salida ON ari_id=ars_articulo_ingreso
                  WHERE ((ari_cantidad-ars_cantidad) > 0 OR ars_cantidad IS NULL)";
        $articulos = $this->db->query($sql)->result();
        foreach($articulos as $articulo){
            $articulo->ing_fecha_ingreso = date('d/m/Y H:i', strtotime($articulo->ing_fecha_ingreso));
            $articulo->ing_despacho_estimado = date('d/m/Y', strtotime($articulo->ing_despacho_estimado));
        }
        return $articulos;
    }

    function listarIngresosPorFecha($fecha_desde, $fecha_hasta){
        $sql   = "SELECT * FROM bodega_central.articulos_ingreso
                  INNER JOIN bodega_central.espacios ON esp_id = ari_espacio
                  INNER JOIN bodega_central.ingresos ON ing_id = ari_ingreso
                  WHERE ing_fecha_ingreso::timestamp::date >= ? and ing_fecha_ingreso::timestamp::date <= ?";
        $articulos = $this->db->query($sql, array($fecha_desde, $fecha_hasta))->result();
        foreach($articulos as $articulo){
            $articulo->ari_nombre            = ucfirst(strtolower($articulo->ari_nombre));
            $articulo->ing_fecha_ingreso     = date('d/m/Y H:i', strtotime($articulo->ing_fecha_ingreso));
            $articulo->ing_despacho_estimado = date('d/m/Y', strtotime($articulo->ing_despacho_estimado));
        }
        return $articulos;
    }

    function listarSalidasPorFecha($fecha_desde, $fecha_hasta){
        $sql   = "SELECT * FROM bodega_central.articulos_salida
                  INNER JOIN bodega_central.articulos_ingreso ON ari_id = ars_articulo_ingreso
                  INNER JOIN bodega_central.ingresos ON ing_id = ari_ingreso
                  INNER JOIN bodega_central.espacios ON esp_id = ari_espacio
                  INNER JOIN bodega_central.salidas ON sld_id = ars_salida
                  WHERE sld_fecha_salida::timestamp::date >= ? and sld_fecha_salida::timestamp::date <= ?";
        $articulos = $this->db->query($sql, array($fecha_desde, $fecha_hasta))->result();
        foreach($articulos as $articulo){
            $articulo->ari_nombre           = ucfirst(strtolower($articulo->ari_nombre));
            $articulo->ing_fecha_ingreso    = date('d/m/Y H:i', strtotime($articulo->ing_fecha_ingreso));
            $articulo->sld_fecha_salida     = date('d/m/Y H:i', strtotime($articulo->sld_fecha_salida));
        }
        return $articulos;
    }

    /* ============================================= */
    /* ============================================= */

}

?>