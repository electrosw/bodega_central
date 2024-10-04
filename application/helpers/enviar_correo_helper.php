<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

function revisaCorreos(){
    $respuesta = true;
    $ci =& get_instance();
    $ci->load->database();
    $ci->load->helper('constantes');
    $esquema = esquema();

    $sql = "SELECT * FROM ".$esquema.".log_correos WHERE lco_enviado = 0";
    $query = $ci->db->query($sql);
    $correos = $query->result();
    foreach($correos as $correo){
        if($correo->lco_adjunto != null && $correo->lco_adjunto != ''){
            $correo_enviado = enviarCorreo($correo->lco_titulo, $correo->lco_subtitulo, $correo->lco_contenido, $correo->lco_destino, 1, $correo->lco_adjunto, $correo->lco_nom_adjunto);
        }
        else{
            $correo_enviado = enviarCorreo($correo->lco_titulo, $correo->lco_subtitulo, $correo->lco_contenido, $correo->lco_destino);
        }
        //$correo_enviado = enviarCorreo($correo->lco_titulo, $correo->lco_subtitulo, $correo->lco_contenido, $correo->lco_destino);
        if($correo_enviado){
            if(!actualizaLogCorreo($correo->lco_id)){
                $respuesta = false;
            }
        }
        else{
            $respuesta = false;
        }
    }
    return $respuesta;
}

function enviarCorreo($titulo, $subtitulo, $contenido, $destino, $tiene_archivo=0, $archivo='', $nom_archivo=''){
    $respuesta = false;
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'correo.electrocom.cl';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    //$mail->SMTPSecure = "tls";
    $mail->CharSet = 'UTF-8';
    $mail->Username = 'asistencias@electrocom.cl';
    $mail->Password = 'Apl790*asis';
    $mail->setFrom('asistencias@electrocom.cl', 'Recursos Humanos');
    $mail->addAddress($destino);
    $mail->Subject  = $titulo;
    $mail->Body     = $contenido;

    if($tiene_archivo==1){
        $mail->addAttachment($archivo, $nom_archivo);//ACA SE ADJUNTA ALGUN ARCHIVO
    }
    $mail->IsHTML(true); 
    $mail->AltBody  = $subtitulo;
    
    
    if (!$mail->send()) {
        $error = $mail->ErrorInfo;
    }
    else {
        $respuesta = true;
    }
    return $respuesta;
}

function actualizaLogCorreo($id_correo){
    $respuesta = false;
    $ci =& get_instance();
    $ci->load->database();
    $ci->load->helper('constantes');
    $esquema = esquema();
    $sql = "UPDATE ".$esquema.".log_correos SET lco_enviado = 1 WHERE lco_id = ?";
    $ci->db->trans_begin();
    $ci->db->query($sql, array($id_correo));
    if ($ci->db->trans_status() === FALSE){
        $ci->db->trans_rollback();
    }
    else{
        $ci->db->trans_commit();
        $respuesta = true;
    }
    return $respuesta;
}