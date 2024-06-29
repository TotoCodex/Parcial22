<?php
class Venta {
    private $mail;
    private $marca;
    private $tipo;
    private $modelo;
    private $color;
    private $stock;
  

    public function __construct($mail, $marca, $tipo, $modelo, $color, $stock) {
        $this->mail = $mail;
        $this->marca = $marca;
        $this->tipo = $tipo;
        $this->modelo = $modelo;
        $this->color = $color;
        $this->stock = $stock;
        
    }

    public function getMail() {
        return $this->mail;
    }

    public function getMarca() {
        return $this->marca;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getModelo() {
        return $this->modelo;
    }

    public function getColor() {
        return $this->color;
    }

    public function getStock() {
        return $this->stock;
    }



}

?>