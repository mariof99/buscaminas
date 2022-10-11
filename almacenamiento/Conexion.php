<?php

class Conexion {
    private static $instancia = null;

    private $conexion;

    private function __construct() {
        $this->abrirConexion();
    }

    public function getInstancia() {
        if (self::$instancia == null) {
            self::$instancia = new Conexion();
        }

        return self::$instancia;
    }

    public function abrirConexion() {
        try {
            $this->conexion = new mysqli(Constantes::$host, Constantes::$usr, Constantes::$passwd, Constantes::$db);
        }
        catch (Exception $e){
            die();
        }
    }

    public function cerrarConexion() {
        unset($this->conexion);
    }

    // devuelve un array con los tableros [0]oculto y [1]mostrar
    public function getTablero($usr, $nPartida) {
        $this->abrirConexion();

        $tableros = [];

        $query = 'SELECT x, y, contenidoOculto, contenidoMostrado FROM'.
            Constantes::$tablaTableros.'WHERE nickname LIKE ? and nPartida LIKE ?';

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param('ss', $usr, $nPartida);
        $resultados = $stmt->get_result();

        $tableroMostrar = []; $tableroOculto = [];
        if ($resultados->num_rows > 0) {
            while($fila = $resultados->fetch_array()) {
                $x = $fila['x']; $y = $fila['y'];
                $tableroMostrar[$x][$y] = $fila['contenidoMostrado'];
                $tableroOculto[$x][$y] = $fila['contenidoOculto'];
            }
        }

        $tableros[] = $tableroOculto; $tableros[] = $tableroMostrar;

        return $tableros;
    }
}