<?php

class Tablero { // tabOculto (> 1 ==> nº minas cerca / 0 ==> nada / -1 ==> mina)
    private $tabOculto;
    private $mostrado;
    private $partidaPerdida;

    function __construct(&$tabOculto, &$mostrado) {
        $this->tabOculto = $tabOculto;
        $this->mostrado = $mostrado;
        $this->partidaPerdida = false;
    }
    
    private function darPutaso($pos) {
        switch (true) {
            case $this->tabOculto[$pos] >= 1:
                $this->mostrado[$pos] = true;
                break;

            case $this->tabOculto[$pos] == 0:
                $this->partidaPerdida = true;

            
            default:
                # code...
                break;
        }
    }
}