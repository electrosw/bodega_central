<?php

class Usuarios_Model extends CI_Model{
    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
    }


    function listarUsuarios(){
        $sql = "SELECT usr_rut, usr_dv, usr_nombres, usr_apellido_p, usr_apellido_m FROM cables.usuarios";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function registrarUsuario($usuario){
        $respuesta = array('key'=>0);
        $sql = "INSERT INTO cables.usuarios (usr_rut, usr_dv, usr_nombres, usr_apellido_p, usr_apellido_m) VALUES (?,?,?,?,?)";
        $this->db->trans_begin();
        $this->db->query($sql, array($usuario->rut, $usuario->dv, $usuario->nombres, $usuario->apellido_p, $usuario->apellido_m));
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        }
        else{
            $this->db->trans_commit();
            $respuesta = array('key'=>1);
        }
        return $respuesta;
    }

}

?>