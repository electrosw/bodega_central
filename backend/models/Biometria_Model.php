<?php

class Biometria_Model extends CI_Model{
    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
    }

    function listarHuellasUsuario($usuario){
        $sql = "SELECT usb_huella1, usb_huella2 FROM personal.usuarios_biometria
                INNER JOIN personal.usuarios_login ON usl_rut = usb_rut WHERE usl_logname = ?";
        $query  = $this->db->query($sql, array($usuario));
        $result = $query->row();
        return $result;
    }

}

?>