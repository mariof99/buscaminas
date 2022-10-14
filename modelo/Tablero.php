<?php

class Tablero { // tabOculto (> 1 ==> nº minas cerca / 0 ==> nada / -1 ==> mina)
    private $clave;
    private $tableros;
    private $estado; // 0-> en curso / 1-> ganada / 2-> perdida

    public static function generarTablero($tamano, $minas) {
        $tabOc = array_fill(0,$tamano, 0);
        $tabM = array_fill(0, $tamano, false);
        $posicionesOcupadas = [];

        for ($i = 0; $i < $minas; $i++) {
            do {
                $index = rand(0, $tamano - 1);
            } while (in_array($index, $posicionesOcupadas));
            $posicionesOcupadas[] = $index;
        }

        foreach ($posicionesOcupadas as $in) {
            $tabOc[$in] = -1;
        }

        self::calcularMinasCerca($tabOc);
        return new Tablero($tabOc, $tabM, 0);
    }

    private static function calcularMinasCerca(&$tab) {
        foreach ($tab as $key => $casilla) {
            // echo 'key ==> '.$key.'   ';

            $minasCerca = 0;
            if ($casilla != -1) {
                if (($key == 0 && $tab[$key + 1] == -1) ||
                ($key == count($tab) - 1 && $tab[$key - 1] == -1)) {
                    $minasCerca++;
            }
            else {
                for ($i = -1; $i <= 1; $i += 2) {
                    if ($tab[$key + $i] == -1) $minasCerca++; 
                }
            }
            $tab[$key] = $minasCerca;       // no funciona asignando el valor a $casilla. No entiendo
            // echo ' casillaFnal  '.$casilla;
            }
        }
    }

    function __construct(&$tabOculto, &$tableroMostrar, $estado) {
        $this->tableros = []; // [0]-> tableroOculto [1]->tableroMostrar
        $this->tableros[] = $tabOculto; $this->tableros[] = $tableroMostrar;
        $this->estado = $estado;
        $this->clave = getdate()[0]; // no es aplicable a un caso real
    }    
    

    private function marcarCasilla($pos) {
        $agua = true;

        if ($this->tableros[0][$pos] >= 0) {
            $this->tableros[1][$pos] = 1;
        }
        elseif ($this->tabOculto[$pos] == -1) {
            $this->tableros[1] = array_fill_keys(array_keys($this->tableros[1]), 1);
            $this->estado = 2;
            $agua = false;
        }        

        return $agua;
    }


    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
        return $this;
    }


    public function getTableros() {
        return $this->tableros;
    }

    public function getTableroOculto() {
        return $this->tableros[0];
    }


    public function getClave() {
        return $this->clave;
    }
}