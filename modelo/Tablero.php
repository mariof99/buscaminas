<?php

class Tablero {
    private $usr;
    private $contenido;
    private $mostrado;

    function __construct($usr, &$contenido, &$mostrado) {
        $this->usr = $usr;
        $this->contenido = $contenido;
        $this->mostrado = $mostrado;
    }
    
}