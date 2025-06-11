<?php

class Cobol_Model extends CI_Model{
    public function __construct() {
        parent::__construct();
        $this->db      = $this->load->database('default', TRUE);
        $this->server  = "https://local7.inverluz.cl";
        $this->usuario = $_SESSION['IluzIntranet']['usu']['logname'];
    }

    # FUNCION USADA PARA EL ORDENAMIENTO DE OBJETOS.
    function object_sorter($clave,$orden=null) {
        return function ($a, $b) use ($clave,$orden) {
              $result=  ($orden=="DESC") ? strnatcmp($b->$clave, $a->$clave) :  strnatcmp($a->$clave, $b->$clave);
              return $result;
        };
    }

    function listarOrdenCompra($orden_compra){
        $url  = $this->server."/cgi-bin/bodega/capebodega.sh?opcion=1&ocompra=".$orden_compra;
        $resp = $this->conexionCurl($url);
        if($resp->status == 200 && $resp->data->Cod == 200){
            $resp->data->fecha_despacho     = '';
            $resp->data->doc_asociado       = '';
            $resp->data->tipo_doc_asociado  = '';

            foreach($resp->data->codigos as $articulo){
                $articulo->cant_recibida    = 0;
                $articulo->estantes_usar    = 0;
                $articulo->certificacion    = false;
                $articulo->estantes[]       = (Object)array('cantidad'=>0, 'estante'=>0);
                $articulo->nomart           = ucfirst(strtolower($articulo->nomart));
            }
            $articulos = $resp->data->codigos;
            #ordenamiento
            usort($articulos, $this->object_sorter('saldo','DESC'));
            $resp->data->codigos = $articulos;

        }
        return $resp;
    }

    function validarDocAsociado($doc, $tipo){
        if($tipo == 33){
            $factura    = $doc;
            $gdespacho  = '';
        }
        else if($tipo == 52){
            $gdespacho  = $doc;
            $factura    = '';
        }
        $url = $this->server."/cgi-bin/bodega/capebodega.sh?opcion=5&factura=$factura&gdesp=$gdespacho";
        $resp = $this->conexionCurl($url);
        if($resp->status == 200 && $resp->data->Cod == 200){
            $resp = (Object)array('key'=>1, 'msj'=>'Documento no digitado previamente.');
        }
        else {
            $resp = (Object)array('key'=>2, 'msj'=>'Documento digitado previamente.');
        }
        return $resp;
    }

    function registrarIngreso($ingreso){
        $orden_compra  = $ingreso->oc;
        $guia_despacho = '';
        $factura       = '';
        if($ingreso->tipo_doc_asociado == 33){
            $factura = $ingreso->doc_asociado;
        }
        else {
            $guia_despacho = $ingreso->doc_asociado;
        }
        $observacion   = str_replace(' ', '+', $ingreso->observacion);
        $codigos       = '';
        foreach($ingreso->articulos as $articulo){
            if($articulo->cant_recibida > 0){
                if(empty(explode('.', $articulo->cant_recibida)[1])){
                    $cantidad = $articulo->cant_recibida.'.00';
                }
                
                // codigo: 00000      cantidad: 000000.00
                $codigoConCeros   = str_pad($articulo->codigo, 5, "0", STR_PAD_LEFT);
                $cant_entera      = explode('.', $cantidad)[0];
                $cant_decimal     = explode('.', $cantidad)[1];
                $cantidadConCeros = str_pad($cant_entera, 6, "0", STR_PAD_LEFT) . str_pad($cant_decimal, 2, "0", STR_PAD_RIGHT);
                $codigos .= $codigoConCeros . $cantidadConCeros;
            }
        }
        $url  = $this->server."/cgi-bin/bodega/capebodega.sh?opcion=2&ocompra=$orden_compra&codigos=$codigos&gdesp=$guia_despacho&factura=$factura&glosa=$observacion&trut=$ingreso->rut_transportista&foflete=$ingreso->fecha_transporte&kgflete=$ingreso->peso_transporte&oflete=$ingreso->orden_transporte&usuario=$this->usuario";
        
        $resp = $this->conexionCurl($url);
        if($resp->status == 200 && $resp->data->Cod == 200){
            $resp = (Object)array('key'=>1, 'oc'=>$orden_compra, 'codigos'=>$codigos, 'url'=>$url, 'data'=>$resp->data);
        }
        else {
            $resp = (Object)array('key'=>0, 'oc'=>$orden_compra, 'codigos'=>$codigos, 'url'=>$url, 'data'=>$resp->data);
        }
        //$data = '';
        //$resp = (Object)array('key'=>1, 'oc'=>$orden_compra, 'codigos'=>$codigos, 'url'=>$url);
        return $resp;
    }

    function listarOrdenesTraspaso($bodega){
        $url  = $this->server."/cgi-bin/bodega/lisotrasp.sh?bodega=".$bodega;
        $resp = $this->conexionCurl($url);
        if($resp->status == 200){
            $resp = $resp->data;
            $resp->status = 200;
        }
        return $resp;
    }

    function listarOrdenTraspaso($id_orden){
        $url  = $this->server."/cgi-bin/bodega/capebodega.sh?opcion=3&ocompra=".$id_orden;
        $resp = $this->conexionCurl($url);
        if($resp->status == 200){
            $resp = $resp->data;
            $resp->status = 200;
            foreach($resp->codigos as $codigo){
                $codigo->cant_salida = $codigo->cant;
            }
        }
        return $resp;
    }

    function registrarSalida($salida){
        $orden_traspaso = $salida->documento;

        $observacion   = str_replace(' ', '+', $salida->detalle->mensaje_nuevo);
        $codigos       = '';
        foreach($salida->detalle->codigos as $articulo){
            if($articulo->cant_salida > 0){
                if(empty(explode('.', $articulo->cant_salida)[1])){
                    $cantidad = $articulo->cant_salida.'.00';
                }
                
                // codigo: 00000      cantidad: 000000.00
                $codigoConCeros   = str_pad($articulo->codigo, 5, "0", STR_PAD_LEFT);
                $cant_entera      = explode('.', $cantidad)[0];
                $cant_decimal     = explode('.', $cantidad)[1];
                $cantidadConCeros = str_pad($cant_entera, 6, "0", STR_PAD_LEFT) . str_pad($cant_decimal, 2, "0", STR_PAD_RIGHT);
                $codigos .= $codigoConCeros . $cantidadConCeros;
            }
        }
        $url  = $this->server."/cgi-bin/bodega/capebodega.sh?opcion=4&ocompra=$orden_traspaso&codigos=$codigos&glosa=$observacion&usuario=$this->usuario";
        
        $resp = $this->conexionCurl($url);
        if($resp->status == 200 && !empty($resp->data->Cod)){
            if($resp->data->Cod == 200){
               $resp = (Object)array('key'=>1, 'ot'=>$orden_traspaso, 'codigos'=>$codigos, 'url'=>$url, 'data'=>$resp->data); 
            }
        }
        else {
            $resp = (Object)array('key'=>0, 'ot'=>$orden_traspaso, 'codigos'=>$codigos, 'url'=>$url, 'data'=>$resp->data, 'msj'=>'Error en registro Cobol.');
        }
        //$data = '';
        //$resp = (Object)array('key'=>1, 'ot'=>$orden_traspaso, 'codigos'=>$codigos, 'url'=>$url);
        return $resp;
    }

    function conexionCurl($url, $metodo='GET', $parametros=null, $header=null){
		$curl 		= curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if($metodo == 'POST'){
            curl_setopt($curl, CURLOPT_POST, 1);
        }
        if($parametros){
           curl_setopt($curl, CURLOPT_POSTFIELDS, $parametros ); 
        }
        if($header){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header );
        }

		$data   = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if($status == 200){
            $data = json_decode($data);
        }
		curl_close($curl);
        $respuesta = (Object) array('data'=>$data, 'status'=> $status);

        return $respuesta;
    }
    
}

?>