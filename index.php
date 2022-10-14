<?php

require_once './modelo/Tablero.php';
require_once './modelo/Usuario.php';
require_once './almacenamiento/Conexion.php';

$conexion = Conexion::getInstancia();
$requestMethod = $_SERVER['REQUEST_METHOD'];
$paths = $_SERVER['REQUEST_URI'];

// if ($requestMethod == 'GET') {}
// // x --> posición en la que se clicka
// // --> tamño tablero, minas --> iniciar una partida
// // op1 --> continuar juego
// // op2 --> abandonar partida
// // op3 --> estadísticas
// // op4 --> ver resultado partida
// elseif ($requestMethod == 'POST') {}    // registrar un nuevo usuario
// elseif ($requestMethod == 'PUT') {}     // modifica un usuario existente
// elseif ($requestMethod == 'DELETE') {}  // borra un usuario existente
// else {}

$usuario = new Usuario('default', '0', 0, 0);   // hice el ejercico orientado de primeras a tener varios usuarios, de modo que para simular 
$conexion->insertUsuario($usuario);             // que solo haya uno creo un usuario por defecto cuyo control está fuera del cliente.

$respuesta = [
    'tableros' => [], // devuelvo un array con los dos tableros (oculto y mostrar)
    'estado' => 0 // estado de la partida (0 -> en curso, 1 -> ganada, 2 -> perdida)
];

$verbosParametro = ['GET', 'PUT', 'DELETE'];
if (in_array($requestMethod, $verbosParametro)) {
    $args = explode('/', $paths); unset($args[0]);
}

switch ($requestMethod) {
    case 'GET':
        if (count($args) == 2) {
            if (substr($args[1], 0, 2) == 'op') {
                switch ($args[1]) {
                    case 'op1':
                        $tablero = $conexion->getPartidaNumUsuario($usuario, $usuario->getNPartidas());
                        $tablero->marcarCasilla($args[2]);
                        $conexion->updateTablero($tablero->getClave(), $args[2]);
                        $respuesta['tableros'] = $tablero->getTableros();
                        $respuesta['estado'] = $tablero->getEstado();
                        break;
                    
                    case 'op2': // pensar hacerlo con usuario y num partida
                        $tablero = $conexion->getPartidaNumUsuario($usuario, $usuario->getNPartidas);
                        $tablero->setEstado = 2;
                        $conexion->updateTablero($usuario, $tablero->getClave());
                        break;

                    case 'op3':
                        $respuesta = $conexion->getPartidasUsuario($usuario->getNickname());
                        
                        break;

                    case 'op4':
                        $respuesta = $conexion->getPartidaNumUsuario($usuario->getNickname(), $args[2]);
                        break;
                }
            }
            else {
                $respuesta = ['mensaje' => 'formato incorrecto'];
            }
        }
        else  {
            $respuesta = ['mensaje' => 'argumentos incorrectos'];
        }
        break;
    
    case 'POST':
            $usuario->setNPartidas($usuario->getNPartidas() + 1);
            $conexion->insertTablero($usuario, Tablero::generarTablero($args[0],$args[1]));
        break;

    case 'PUT':
        # code...
        break;

    case 'DELETE':
        # code...
        break;

    default:
        # code...
        break;
}