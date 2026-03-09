<?php
/* ============================================================
   FYLCAD — Cliente de Sockets
   Archivo: socket_client.php
   Ubicación: C:/xampp/htdocs/FYLCAD/socket_client.php

   USO: php socket_client.php  (desde terminal de Ubuntu)
   Conecta al socket_server.php en el puerto 9000
============================================================ */

$serverIP   = '127.0.0.1';   // IP del servidor (misma máquina Ubuntu)
$serverPort = 9000;

echo "============================================\n";
echo "  FYLCAD — Cliente de Sockets\n";
echo "  Conectando a {$serverIP}:{$serverPort}...\n";
echo "============================================\n\n";


/* ── PRUEBA 1: Ping de conexión ──────────────────────────── */
echo "── PRUEBA 1: Handshake / Ping ──\n";
$respuesta = enviarPayload($serverIP, $serverPort, "FYLCAD|ping|test");
echo "Respuesta del servidor: {$respuesta}\n\n";


/* ── PRUEBA 2: Calcular métricas con coordenadas reales ──── */
echo "── PRUEBA 2: Cálculo topográfico ──\n";
echo "Enviando 5 puntos de un terreno en FYLCAD...\n";

// Coordenadas de ejemplo (formato X,Y,Z separados por ;)
// Representan un polígono topográfico real
$coordenadas = "100.0,200.0,1520.5;"
             . "150.0,200.0,1522.3;"
             . "150.0,250.0,1519.8;"
             . "125.0,270.0,1521.0;"
             . "100.0,250.0,1518.7";

$payload   = "FYLCAD|calcular|{$coordenadas}";
echo "Payload enviado: {$payload}\n\n";

$respuesta = enviarPayload($serverIP, $serverPort, $payload);
echo "Respuesta del servidor: {$respuesta}\n";

// Parsear y mostrar resultados de forma legible
if (strpos($respuesta, 'FYLCAD|resultado|') === 0) {
    $datos   = str_replace('FYLCAD|resultado|', '', trim($respuesta));
    $campos  = explode(';', $datos);

    echo "\n── RESULTADOS PROCESADOS ──\n";
    foreach ($campos as $campo) {
        $par = explode(':', $campo);
        if (count($par) === 2) {
            $clave  = strtoupper(str_replace('_', ' ', $par[0]));
            $valor  = $par[1];
            echo "  {$clave}: {$valor}\n";
        }
    }
}

echo "\n── PRUEBA 3: Payload inválido (manejo de errores) ──\n";
$respuesta = enviarPayload($serverIP, $serverPort, "DATOS_INVALIDOS");
echo "Respuesta del servidor: {$respuesta}\n";

echo "\n============================================\n";
echo "  Todas las pruebas completadas.\n";
echo "============================================\n";


/* ============================================================
   FUNCIÓN: Crear socket, conectar, enviar y recibir
============================================================ */
function enviarPayload(string $ip, int $puerto, string $payload): string {

    // 1. Crear el socket del cliente
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if ($socket === false) {
        return "ERROR: No se pudo crear el socket — "
             . socket_strerror(socket_last_error());
    }

    // 2. Conectar al servidor
    if (!socket_connect($socket, $ip, $puerto)) {
        socket_close($socket);
        return "ERROR: No se pudo conectar a {$ip}:{$puerto} — "
             . socket_strerror(socket_last_error());
    }

    // 3. Enviar payload (se agrega \n como delimitador de fin de mensaje)
    $mensaje = $payload . "\n";
    socket_write($socket, $mensaje, strlen($mensaje));

    // 4. Leer respuesta del servidor
    $respuesta = socket_read($socket, 4096, PHP_NORMAL_READ);

    // 5. Cerrar socket correctamente
    socket_close($socket);

    return trim($respuesta);
}