<?php
/* =============================================
   FYLCAD — Conexión a la base de datos
   Archivo: config/db.php
============================================= */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Usuario por defecto en XAMPP
define('DB_PASS', '');            // Contraseña vacía por defecto en XAMPP
define('DB_NAME', 'fylcad_db');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST
             . ";dbname="    . DB_NAME
             . ";charset="   . DB_CHARSET;

        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        } catch (PDOException $e) {
            // En producción nunca mostrar el error real
            http_response_code(500);
            die(json_encode(['error' => 'No se pudo conectar a la base de datos.']));
        }
    }

    return $pdo;
}