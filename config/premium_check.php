<?php
/**
 * FYLCAD — Plataforma de Topografía Digital
 * Copyright (c) 2026 Fabian Eduardo Rodriguez Hernandez
 * Todos los derechos reservados.
 * Uso no autorizado prohibido.
 *
 * Helper para verificar acceso premium
 */

function esPremium(): bool {
    return ($_SESSION['usuario_plan'] ?? 'free') === 'premium';
}

function requirePremium(string $redirigir = 'planes.php'): void {
    if (!esPremium()) {
        $_SESSION['upgrade_msg'] = 'Esta función es exclusiva del plan Premium.';
        header("Location: $redirigir");
        exit;
    }
}

function bloquearSiFree(string $mensaje = ''): void {
    if (!esPremium()) {
        $msg = $mensaje ?: 'Necesitas el plan Premium para acceder a esta función.';
        http_response_code(403);
        echo json_encode(['error' => $msg, 'upgrade' => true]);
        exit;
    }
}
