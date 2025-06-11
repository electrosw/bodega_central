<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
/***********************************************************************/
/* FUNCIÓN QUE RECIBE FECHA Y OPCIÓN PARA RETORNAR FECHA EN PALABRAS   */
/*  @fecha  :  en formatos d-m-Y  Y-m-d                                */
/*  @opcion :  1: nombre de dia y numero dia                           */
/*             2: nombre de dia, numero dia y mes                      */
/*             3: nombre de dia, numero dia, mes y año                 */
/***********************************************************************/
function fechaEnPalabras($fecha, $opcion, $min_mayus=0){
    if($min_mayus == 0){
        $dias  = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $conector = " de ";
    }
    if($min_mayus == 1){
        $dias  = array("DOMINGO", "LUNES", "MARTES", "MIÉRCOLES", "JUEVES", "VIERNES", "SÁBADO");
        $meses = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
        $conector = " DE ";
    }
    $dia_p = $dias[idate('w', strtotime(date($fecha)))];
    $mes_p = $meses[idate('m', strtotime(date($fecha)))-1];
    $dia_n = date('d', strtotime($fecha));
    $ano_n = idate('Y', strtotime(date($fecha)));
    $fecha_ff = "";
    if($opcion==1) $fecha_ff = $dia_p." ".$dia_n;
    if($opcion==2) $fecha_ff = $dia_p." ".$dia_n.$conector.$mes_p;
    if($opcion==3) $fecha_ff = $dia_p." ".$dia_n.$conector.$mes_p.$conector.$ano_n;
    return $fecha_ff;
}

function obtenerUrl(){
    return "https://rh.electrocom.cl";
}

function obtenerLocal(){
    return 0;
}

?>