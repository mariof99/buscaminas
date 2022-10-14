<?php

require_once 'Constantes.php';
require_once __DIR__.'/../modelo/Tablero.php';

class Conexion {
    private static $instancia = null;

    private $conexion;

    private function __construct() {
        $this->abrirConexion();
    }

    public static function getInstancia() {
        if (self::$instancia == null) {
            self::$instancia = new Conexion();
        }

        return self::$instancia;
    }



    private function abrirConexion() {
        try {
            $this->conexion = new mysqli(Constantes::$host, Constantes::$usr, Constantes::$passwd, Constantes::$db);
        }
        catch (Exception $e){
            die();
        }
    }

    private function cerrarConexion() {
        unset($this->conexion);
    }

    //------------------------------------------------------------------
    //          GET
    //------------------------------------------------------------------

    public function getUsuario($nickname) {
        $this->abrirConexion();

        $query = 'SELECT * FROM '.Constantes::$tablaUsuarios.' WHERE nickname LIKE ?';

        try {
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param('s', $nickname);
            $stmt->execute();
            $resultados = $stmt->get_result();            
            $fila = $resultados->fetch_array();
        }
        catch (Exception $e) {            
        }

        
        return new Usuario($fila['nickname'], $fila['passwd'], $fila['ganadas'], $fila['jugadas']);
    }

    // public function loginUser($nickname, $passwd) {

    // }

    //------------------------------------------------------------------
    
    public function getTablero($clave) { // clave de partida //puedo crear el campo partida actual en la bd en la tabla usuario
        $this->abrirConexion();

        $tableros = [];

        $query = 'SELECT pos, contenidoOculto, contenidoMostrado FROM '.
            Constantes::$tablaTableros.' WHERE clave LIKE ?';

        // try {
            echo'1';
            $stmt = $this->conexion->prepare($query); 
            echo '2';
            $stmt->bind_param('s', $clave);
            $stmt->execute();
            $resultados = $stmt->get_result();

            $tableroMostrar = []; $tableroOculto = [];
            if ($resultados->num_rows > 0) {
                while($fila = $resultados->fetch_array()) {
                    $pos = $fila['pos'];
                    $tableroMostrar[$pos] = $fila['contenidoMostrado'];
                    $tableroOculto[$pos] = $fila['contenidoOculto'];
                    echo 'x';
                }
            }
        // }
        // catch (Exception $e) {
        //         $tableros = $e->errno;
        // }

        // $resultados->free_result();
        
        $this->cerrarConexion();
        $tableros[] = $tableroOculto; $tableros[] = $tableroMostrar;

        echo 'aaaaaaa';
        print_r($tableroOculto); print_r($tableroMostrar);

        return new Tablero($tableros[0], $tableros[1], 0);  // ausmo que la partida está en curso ya que será
    }                                                       // el caso la mayoría de las veces. Este método se
                                                            // reutiliza en otros de la clase, por lo que si la 
                                                            // partida obtenida no está en curso utilizaré un 
                                                            // setter para corregir el estado.

    //------------------------------------------------------------------

    public function getPartidasUsuario($nickname) { //FUNCIONA
        $this->abrirConexion();

        $partidas = [];
        $query = 'SELECT claveTablero, estado FROM '.Constantes::$tablaUsuTab.' WHERE nickname LIKE ?';

        try {
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param('s', $nickname);
            $stmt->execute();
            $resultados = $stmt->get_result();

            while ($fila = $resultados->fetch_array()) {
                $partidas[] = $this->getTablero($fila['claveTablero'])->setEstado($fila['estado']);
            }
        }
        catch (Exception $e) {
            $partidas[] = $e->errno;
        }

        // $resultados->free_result();
        $this->cerrarConexion();
        return $partidas;
    }

    //------------------------------------------------------------------

    public function getPartidaNumUsuario($nickname, $nPartida) {
        $this->abrirConexion();

        $query = 'SELECT claveTablero FROM '.Constantes::$tablaUsuTab.
            ' WHERE nickname LIKE ? AND nPartida = ?';
        try {
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param('si', $nickname, $nPartida);
            $stmt->execute();

            $resultados = $stmt->get_result();
            $fila = $resultados->fetch_array();
            $tablero = $this->getTablero($fila['claveTablero'])->setEstado($fila['estado']);
        }
        catch (Exception $e) {
            $tablero = $e->errno;
        }

        return $tablero;
    }

    //------------------------------------------------------------------
    //          POST
    //------------------------------------------------------------------

    public function insertUsuario($usuario) { // FUNCIONA
        $this->abrirConexion();
        $cod = 0;

        $query = 'INSERT INTO '.Constantes::$tablaUsuarios.' (nickname, passwd, ganadas, jugadas)
            VALUES (?, ?, ?, ?)';

        // try {
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param('ssii', $usuario->getNickname(), $usuario->getPasswd(), 
                $usuario->getNGanadas(), $usuario->getNPartidas());
            $stmt->execute();
        // }
        // catch (Exception $e) {
        //     $cod = $e->errno;
        // }

        $this->cerrarConexion();
        return $cod;
    }

    //------------------------------------------------------------------

    public function insertTablero($usuario, $tablero) { //FUNCIONA
        $this->abrirConexion();
        $cod = 0;

        $query1 = 'INSERT INTO '.Constantes::$tablaTableros.' (claveTablero , pos, contenidoOculto, 
        contenidoMostrado) VALUES(?, ?, ?, ?)';
        $query2 = 'INSERT INTO '.Constantes::$tablaUsuTab.' (nickname, claveTablero, nPartida, estado)'
            .'VALUES(?, ?, ?, ?)';
        
        $tableros = $tablero->getTableros();

        // try {
            for ($i = 0; $i < count($tableros[0]); $i++) { 
                $stmt = $this->conexion->prepare($query1);
                $stmt->bind_param('iiii', $$tablero->getClave(), $i, $tableros[0][$i], $tableros[1][$i]);
                $stmt->execute();
            }

            $stmt = $this->conexion->prepare($query2);
            $estado = 0;
            $stmt->bind_param('siii', $usuario->getNickname(), $tablero->getClave(), $usuario->getNPartidas(), $estado);
            $stmt->execute();
        // }
        // catch (Exception $e) {
        //     $cod = $e->errno;
        // }

        $this->cerrarConexion();

        return $cod;
    }

    //------------------------------------------------------------------
    //          PUT
    //------------------------------------------------------------------

    // $op => (0 -> mod passwd, 1 -> mod ganadas, 2 -> mod jugadas)
    public function updateUsuario($op, $usuario) { // funciona porque no se puede modificar el nick
        $this->abrirConexion();
        $cod = 0;

        $args = []; $tipos = '';
        $args[] = &$tipos;

        $query = 'UPDATE '.Constantes::$tablaUsuarios.'SET ';
        switch ($op) {
            case '0':
                $query .= 'passwd = ?';
                $args[] = $usuario->getPasswd();
                $tipos = 's';
                break;
            
            case '1':
                $query .= 'ganadas = ?, jugadas = ?';
                $args[] = $usuario->getNGanadas(); $args[] = $usuario->getNPartidas();
                $tipos = 'ss';
                break;

            case '2':
                $query .= 'jugadas = ?';
                $args[] = $usuario->getNPartidas();
                $tipos = 's';
                break;
        }

        $query .= " WHERE nickname LIKE '".$usuario->getNickname()."'";

        try {
            $stmt = $this->conexion->prepare($query);
            call_user_func_array(array($stmt, 'bind_param'), $args);
            $stmt->execute();
        }
        catch (Exception $e) {
            $cod = $e->errno;
        }

        $this->cerrarConexion();
        return $cod;
    }

    //------------------------------------------------------------------

    // $op => (0 -> mod pos mostrar, 1 -> mod estado de la partida)
    public function updateTablero($op, $clave, $pos) {
        $this->abrirConexion();
        $cod = 0;

        $args = [];
        switch ($op) {
            case '0':
                $query = 'UPDATE '.Constantes::$tablaTableros.' SET contenidoMostrado = 1 
                    WHERE claveTablero = ? AND pos = ?';
                array_push($args[], 's', $clave, $pos); 
                break;
            
            case '1':
                $query = 'UPDATE '.Constantes::$tablaUsuTab.' SET estado = // SOBRECARGAR MÉTODO 
                    WHERE claveTablero = ? AND pos = ?';
                break;
        }
    }

    public function modificarTablero($clave, $pos) {    // en la db no necesito modificar el tablero de pos mostradas ya que
        $this->abrirConexion();                         // al recuperarlo para un histórico el resultado de la partida es
        $cod = 0;                                       // indiferente, ya que se mostrará el tablero completo.

        $query = 'UPDATE '.Constantes::$tablaTableros.' SET contenidoMostrado = 1 
            WHERE claveTablero = ? AND pos = ?';
        
        try {
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param('ii', $pos, $clave);
            $stmt->execute();
        }
        catch (Exception $e) {
            $cod = $e->errno;
        }
        
        $this->cerrarConexion();
        return $cod;
    }

    //------------------------------------------------------------------
    //          DELETE
    //------------------------------------------------------------------

    public function deleteTablero($clave) {
        $this->abrirConexion();
        
        $cod = 0;
        $queries = [];

        $queries[] = 'DELETE FROM '.Constantes::$tablaUsuTab.' WHERE claveTablero = ?';
        $queries[] = 'DELETE FROM '.Constantes::$tablaTableros.' WHERE claveTablero = ?'; 
    
        try {
            foreach ($queries as $query) {
                $stmt = $this->conexion->prepare($query);
                $stmt->bind_param('i', $clave);

                $stmt->execute();
                if ($stmt->affected_rows == 0) $cod = 1; 
            }
        }
        catch (Exception $e) {
            $cod = $e->errno;
        }

        $this->cerrarConexion();
        return $cod;
    }
}