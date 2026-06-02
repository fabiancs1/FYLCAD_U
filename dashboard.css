<?php
/* =============================================
   FYLCAD — Configuración de entorno
   Archivo: config/env.php

   INSTRUCCIONES PARA LA VPS:
   Edita solo los valores entre comillas
   NO subas este archivo a GitHub
============================================= */

// ── Base de datos ──────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'fylcad_db');
define('DB_USER',    'tu_usuario_mysql');   // ← CAMBIAR
define('DB_PASS',    'tu_contraseña');      // ← CAMBIAR
define('DB_CHARSET', 'utf8mb4');

// ── Entorno ────────────────────────────────
define('APP_ENV',   'production');   // 'development' o 'production'
define('APP_URL',   'https://tudominio.com');  // ← CAMBIAR

// ── Errores ────────────────────────────────
// En producción los errores no se muestran al usuario
if (APP_ENV === 'production') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
