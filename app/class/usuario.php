<?php
class Usuario {
    private $mail;
    private $usuario;
    private $contraseña;
    private $perfil;

    public function __construct($mail, $usuario, $contraseña, $perfil) {
        $this->mail = $mail;
        $this->usuario = $usuario;
        $this->contraseña = $contraseña;
        $this->perfil = $perfil;
    }

    public function getMail() {
        return $this->mail;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function getContraseña() {
        return $this->contraseña;
    }

    public function getPerfil() {
        return $this->perfil;
    }
}
?>