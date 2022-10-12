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
    public function getTablero($clave) { // clave de partida //puedo crear el campo partida actual en la bd en la tabla usuario
        $this->abrirConexion();

        $tableros = [];

        $query = 'SELECT pos, contenidoOculto, contenidoMostrado FROM'.
            Constantes::$tablaTableros.'WHERE clave LIKE ?';

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param('s', $clave);
        $resultados = $stmt->get_result();

        $tableroMostrar = []; $tableroOculto = [];
        if ($resultados->num_rows > 0) {
            while($fila = $resultados->fetch_array()) {
                $pos = $fila['pos'];
                $tableroMostrar[$pos] = $fila['contenidoMostrado'];
                $tableroOculto[$pos] = $fila['contenidoOculto'];
            }
        }

        $tableros[] = $tableroOculto; $tableros[] = $tableroMostrar;

        return $tableros;
    }

    public function insertTablero ($tableros) {

    }
}