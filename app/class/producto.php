<?php
class Producto {
    private $id;
    private $marca;
    private $precio;
    private $tipo;
    private $modelo;
    private $color;
    private $stock;
  

    public function __construct($marca, $precio, $tipo, $modelo, $color, $stock) {
        $this->marca = $marca;
        $this->precio = $precio;
        $this->tipo = $tipo;
        $this->modelo = $modelo;
        $this->color = $color;
        $this->stock = $stock;
        
    }

    public function getMarca() {
        return $this->marca;
    }

    public function getPrecio() {
        return $this->precio;
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