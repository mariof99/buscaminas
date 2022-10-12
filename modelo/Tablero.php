<?php

class Tablero { // tabOculto (> 1 ==> nº minas cerca / 0 ==> nada / -1 ==> mina)
    private $tableros;

    public static function generarTablero($tamano, $minas) { // PREGUNTAR A FERNANDO!!!
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
        return new Tablero($tabOc, $tabM);
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

    function __construct(&$tabOculto, &$tableroMostrar) {
        $this->tableros = []; // [0]-> tableroOculto [1]->tableroMostrar
        $this->tableros[] = $tabOculto; $this->tableros[] = $tableroMostrar;
    }    
    

    private function marcarCasilla($pos) {
        if ($this->tableros[0][$pos] >= 0) {
            $this->tableros[1][$pos] = true;
        }
        elseif ($this->tabOculto[$pos] == -1) {
            $this->tableros[1] = array_fill_keys(array_keys($this->tableros[1]), true);
        }
        
        return $this->tableros;
    }
}