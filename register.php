<?php
/**
 * FYLCAD — Plataforma de Topografía Digital
 * Copyright (c) 2026 Fabian Eduardo Rodriguez Hernandez
 * Todos los derechos reservados.
 * Uso no autorizado prohibido.
 */

/* =============================================
   FYLCAD — Registro de usuario
   Archivo: register.php
============================================= */
session_start();
require_once 'config/db.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errores = [];
$exito   = false;
$nombre  = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre']   ?? '');
    $email     = trim($_POST['email']    ?? '');
    $password  = trim($_POST['password'] ?? '');
    $confirmar = trim($_POST['confirmar']?? '');

    // Validaciones
    if (empty($nombre) || strlen($nombre) < 2) {
        $errores[] = "El nombre debe tener al menos 2 caracteres.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido.";
    }
    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }
    if ($password !== $confirmar) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (empty($errores)) {
        $db = getDB();

        // Verificar si el email ya existe
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errores[] = "Ya existe una cuenta con ese email.";
        } else {
            // Crear usuario
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare(
                "INSERT INTO usuarios (nombre, email, password, plan) VALUES (?, ?, ?, 'free')"
            );
            $stmt->execute([$nombre, $email, $hash]);

            $exito = true;
            $nombre = $email = '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>FYLCAD — Crear cuenta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link referrerpolicy="no-referrer" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" crossorigin>
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/global_mejoras.css">
</head>
<body>

<div class="auth-bg">
    <!-- Curvas topográficas decorativas -->
    <svg class="topo-svg" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <ellipse cx="600" cy="400" rx="480" ry="240" fill="none" stroke="#00e5c0" stroke-width="0.6" opacity="0.08"/>
        <ellipse cx="600" cy="400" rx="380" ry="180" fill="none" stroke="#00e5c0" stroke-width="0.6" opacity="0.08"/>
        <ellipse cx="600" cy="400" rx="280" ry="125" fill="none" stroke="#00e5c0" stroke-width="0.6" opacity="0.08"/>
        <ellipse cx="600" cy="400" rx="180" ry="78"  fill="none" stroke="#00e5c0" stroke-width="0.6" opacity="0.08"/>
        <ellipse cx="600" cy="400" rx="90"  ry="38"  fill="none" stroke="#00e5c0" stroke-width="0.6" opacity="0.08"/>
        <ellipse cx="100" cy="700" rx="300" ry="150" fill="none" stroke="#00e5c0" stroke-width="0.6" opacity="0.05"/>
        <ellipse cx="100" cy="700" rx="200" ry="100" fill="none" stroke="#00e5c0" stroke-width="0.6" opacity="0.05"/>
    </svg>
    <!-- Glow -->
    <div class="bg-glow"></div>
</div>

<div class="auth-wrapper">

    <!-- Logo -->
    <a href="index.php" class="auth-logo">FYL<span>CAD</span></a>

    <!-- Card -->
    <div class="auth-card">

        <div class="auth-card-header">
            <h1>Crear cuenta</h1>
            <p>Empieza gratis — sin tarjeta de crédito</p>
        </div>

        <!-- Alerta de éxito -->
        <?php if ($exito): ?>
        <div class="alert alert-success">
            <span class="alert-icon">✓</span>
            <div>
                <strong>¡Cuenta creada!</strong>
                <p>Ya puedes <a href="login.php">iniciar sesión</a>.</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Alertas de error -->
        <?php if (!empty($errores)): ?>
        <div class="alert alert-error">
            <span class="alert-icon">!</span>
            <div>
                <?php foreach ($errores as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form method="POST" action="register.php" class="auth-form" id="formRegister">

            <div class="form-group">
                <label for="nombre">Nombre completo</label>
                <div class="input-wrap">
                    <span class="input-icon">👤</span>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        placeholder="Tu nombre"
                        value="<?= htmlspecialchars($nombre) ?>"
                        autocomplete="name"
                        required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <div class="input-wrap">
                    <span class="input-icon">✉</span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="correo@ejemplo.com"
                        value="<?= htmlspecialchars($email) ?>"
                        autocomplete="email"
                        required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrap">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Mínimo 8 caracteres"
                        autocomplete="new-password"
                        required>
                    <button type="button" class="toggle-pass" data-target="password">👁</button>
                </div>
                <div class="pass-strength" id="passStrength">
                    <div class="pass-bar" id="passBar"></div>
                </div>
                <span class="pass-label" id="passLabel"></span>
            </div>

            <div class="form-group">
                <label for="confirmar">Confirmar contraseña</label>
                <div class="input-wrap">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password"
                        id="confirmar"
                        name="confirmar"
                        placeholder="Repite tu contraseña"
                        autocomplete="new-password"
                        required>
                    <button type="button" class="toggle-pass" data-target="confirmar">👁</button>
                </div>
            </div>

            <!-- Plan -->
            <div class="plan-selector">
                <div class="plan-option active" data-plan="free">
                    <div class="plan-name">Free</div>
                    <div class="plan-desc">Hasta 50 puntos por archivo</div>
                    <div class="plan-price">Gratis</div>
                </div>
                <div class="plan-option" data-plan="premium">
                    <div class="plan-badge">Próximamente</div>
                    <div class="plan-name">Premium</div>
                    <div class="plan-desc">Puntos ilimitados + exportar PDF</div>
                    <div class="plan-price">$9.99/mes</div>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="btnSubmit">
                <span class="btn-text">Crear cuenta gratuita</span>
                <span class="btn-arrow">→</span>
            </button>

        </form>

        <div class="auth-footer">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
        </div>

    </div>

</div>

<script>
// Toggle mostrar/ocultar contraseña
document.querySelectorAll(".toggle-pass").forEach(btn => {
    btn.addEventListener("click", () => {
        const input = document.getElementById(btn.dataset.target);
        input.type  = input.type === "password" ? "text" : "password";
        btn.textContent = input.type === "password" ? "👁" : "🙈";
    });
});

// Indicador de fuerza de contraseña
const passInput = document.getElementById("password");
const passBar   = document.getElementById("passBar");
const passLabel = document.getElementById("passLabel");

passInput.addEventListener("input", () => {
    const v = passInput.value;
    let score = 0;
    if (v.length >= 8)          score++;
    if (/[A-Z]/.test(v))        score++;
    if (/[0-9]/.test(v))        score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;

    const niveles = [
        { label: "",          color: "transparent", w: "0%" },
        { label: "Débil",     color: "#ef4444",     w: "25%" },
        { label: "Regular",   color: "#f59e0b",     w: "50%" },
        { label: "Buena",     color: "#3b82f6",     w: "75%" },
        { label: "Excelente", color: "#00e5c0",     w: "100%" }
    ];
    const n = niveles[score];
    passBar.style.width      = n.w;
    passBar.style.background = n.color;
    passLabel.textContent    = n.label;
    passLabel.style.color    = n.color;
});

// Selector de plan (solo visual por ahora)
document.querySelectorAll(".plan-option").forEach(opt => {
    opt.addEventListener("click", () => {
        if (opt.dataset.plan === "premium") return; // bloqueado
        document.querySelectorAll(".plan-option").forEach(o => o.classList.remove("active"));
        opt.classList.add("active");
    });
});
</script>

    <!-- Chatbot FYLCAD -->
    <link rel="stylesheet" href="css/chatbot.css">
    <script src="js/chatbot.js" defer></script>
</body>
</html>
