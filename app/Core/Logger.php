<?php
/**
 * FYLCAD — Plataforma de Topografía Digital
 * Copyright (c) 2026 Fabian Eduardo Rodriguez Hernandez
 * Todos los derechos reservados.
 * Uso no autorizado prohibido.
 */

/* =============================================
   FYLCAD — Componente de Auditoría
   Archivo: app/Core/Logger.php
   Guía Práctica N°8 — Actividad 3

   Registra eventos del sistema en audit.log
   Formato: [FECHA_HORA] [TIPO_EVENTO] [MENSAJE]
============================================= */

class Logger {

    private const RUTA_LOG = __DIR__ . "/../../logs/audit.log";

    // Tipos de evento disponibles
    const INFO    = "INFO";
    const ERROR   = "ERROR";
    const WARNING = "WARNING";
    const AUTH    = "AUTH";
    const DB      = "DB";
    const API     = "API";

    // Método principal: registrar un evento en audit.log
    public static function log(string $tipo, string $mensaje): void {

        // Crear carpeta /logs/ si no existe
        $directorio = dirname(self::RUTA_LOG);
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $ip        = $_SERVER["REMOTE_ADDR"] ?? "cli";
        $fechaHora = date("Y-m-d H:i:s");
        $linea     = "[{$fechaHora}] [{$tipo}] [IP:{$ip}] {$mensaje}\n";

        // FILE_APPEND acumula, LOCK_EX evita escrituras simultáneas
        file_put_contents(self::RUTA_LOG, $linea, FILE_APPEND | LOCK_EX);
    }

    // Métodos de acceso rápido por tipo de evento
    public static function info(string $msg): void    { self::log(self::INFO,    $msg); }
    public static function error(string $msg): void   { self::log(self::ERROR,   $msg); }
    public static function warning(string $msg): void { self::log(self::WARNING, $msg); }
    public static function auth(string $msg): void    { self::log(self::AUTH,    $msg); }
    public static function db(string $msg): void      { self::log(self::DB,      $msg); }
    public static function api(string $msg): void     { self::log(self::API,     $msg); }
}
