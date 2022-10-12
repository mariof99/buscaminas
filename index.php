<?php

require_once './modelo/Tablero.php';

// $requestMethod = $_SERVER('REQUEST_METHOD');
// $paths = $_SERVER('REQUEST_PATHS');

// if ($requestMethod == 'GET') {}
// // x, y --> posición en la que se clicka
// // nada --> iniciar una partida
// // 0 --> continuar juego
// // 1 --> cerrar sesión (varios usuarios)
// // 2 --> abandonar partida
// // 3 --> estadísticas
// // 4 -> ver resultado partida
// elseif ($requestMethod == 'POST') {}    // registrar un nuevo usuario
// elseif ($requestMethod == 'PUT') {}     // modifica un usuario existente
// elseif ($requestMethod == 'DELETE') {}  // borra un usuario existente
// else {}

print_r(Tablero::generarTablero(10, 4));