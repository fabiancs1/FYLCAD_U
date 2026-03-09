<?php
/* ============================================================
   FYLCAD — Servidor de Sockets
   Archivo: socket_server.php
   Ubicación: C:/xampp/htdocs/FYLCAD/socket_server.php

   USO: php socket_server.php  (desde terminal de Ubuntu)
   Puerto: 9000
   Protocolo: FYLCAD|accion|datos
============================================================ */

$host = '0.0.0.0';  
$port = 9000;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    die("ERROR socket_create: " . socket_strerror(socket_last_error()) . "\n");
}


socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);


if (!socket_bind($socket, $host, $port)) {
    die("ERROR socket_bind: " . socket_strerror(socket_last_error()) . "\n");
}

if (!socket_listen($socket, 5)) {
    die("ERROR socket_listen: " . socket_strerror(socket_last_error()) . "\n");
}

echo "============================================\n";
echo "  FYLCAD — Servidor de Sockets activo\n";
echo "  Escuchando en {$host}:{$port}\n";
echo "  Esperando conexiones de clientes...\n";
echo "============================================\n";


while (true) {

    $cliente = socket_accept($socket);
    if ($cliente === false) {
        echo "ERROR socket_accept: " . socket_strerror(socket_last_error()) . "\n";
        continue;
    }

   
    socket_getpeername($cliente, $clienteIP);
    echo "\n[HANDSHAKE OK] Cliente conectado desde: {$clienteIP}\n";

    // ── 5. Leer payload enviado por el cliente ─────────────
    $datos = socket_read($cliente, 4096, PHP_NORMAL_READ);
    $datos = trim($datos);

    echo "[RECIBIDO] {$datos}\n";

   
    $partes = explode('|', $datos);

    if (count($partes) < 3 || $partes[0] !== 'FYLCAD') {
        $respuesta = "FYLCAD|error|Payload invalido. Formato: FYLCAD|accion|datos\n";
    } else {
        $accion = strtolower(trim($partes[1]));
        $payload = trim($partes[2]);

        switch ($accion) {

            
            case 'calcular':
                $puntos = parsearPuntos($payload);
                if (count($puntos) < 3) {
                    $respuesta = "FYLCAD|error|Se necesitan al menos 3 puntos para calcular\n";
                } else {
                    $area      = calcularArea($puntos);
                    $volumen   = calcularVolumen($puntos);
                    $cotaMin   = min(array_column($puntos, 'z'));
                    $cotaMax   = max(array_column($puntos, 'z'));
                    $desnivel  = round($cotaMax - $cotaMin, 2);
                    $nPuntos   = count($puntos);

                    $respuesta = "FYLCAD|resultado|"
                        . "puntos:{$nPuntos};"
                        . "area:" . round($area, 2) . "m2;"
                        . "volumen:" . round($volumen, 2) . "m3;"
                        . "cota_min:{$cotaMin}m;"
                        . "cota_max:{$cotaMax}m;"
                        . "desnivel:{$desnivel}m\n";
                }
                break;

     
            case 'ping':
                $respuesta = "FYLCAD|pong|Servidor FYLCAD activo en puerto {$port}\n";
                break;

            default:
                $respuesta = "FYLCAD|error|Accion desconocida: {$accion}\n";
                break;
        }
    }

    // ── 7. Escribir respuesta al cliente ──────────────────
    socket_write($cliente, $respuesta, strlen($respuesta));
    echo "[ENVIADO]  {$respuesta}";

    // ── 8. Cerrar socket del cliente ──────────────────────
    socket_close($cliente);
    echo "[CERRADO]  Conexión con {$clienteIP} cerrada correctamente.\n";
}

function parsearPuntos(string $payload): array {
    $puntos = [];
    $lineas = explode(';', $payload);
    foreach ($lineas as $linea) {
        $p = explode(',', trim($linea));
        if (count($p) >= 3 && is_numeric($p[0]) && is_numeric($p[1]) && is_numeric($p[2])) {
            $puntos[] = [
                'x' => (float)$p[0],
                'y' => (float)$p[1],
                'z' => (float)$p[2],
            ];
        }
    }
    return $puntos;
}

function calcularArea(array $puntos): float {
    $n    = count($puntos);
    $area = 0.0;
    for ($i = 0; $i < $n; $i++) {
        $j     = ($i + 1) % $n;
        $area += $puntos[$i]['x'] * $puntos[$j]['y'];
        $area -= $puntos[$j]['x'] * $puntos[$i]['y'];
    }
    return abs($area) / 2.0;
}


function calcularVolumen(array $puntos): float {
    $area       = calcularArea($puntos);
    $cotaMedia  = array_sum(array_column($puntos, 'z')) / count($puntos);
    $cotaBase   = min(array_column($puntos, 'z'));
    $altura     = $cotaMedia - $cotaBase;
    return $area * max($altura, 0);
}