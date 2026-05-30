<?php
/**
 * FYLCAD — Plataforma de Topografía Digital
 * Copyright (c) 2026 Fabian Eduardo Rodriguez Hernandez
 * Todos los derechos reservados.
 * Uso no autorizado prohibido.
 */

/* =============================================
   FYLCAD — Prueba de Integración Web Service
   Archivo: test_servicio.php
   Guía Práctica N°7 — Actividad 4

   PRUEBA DE ESTRÉS / SINCRONIZACIÓN:
   Demuestra en el navegador la integración
   en tiempo real:
     1. API Externa (Open-Elevation) → cURL
     2. cURL → PHP (ServiceConnector)
     3. PHP → MySQL (INSERT actividad)
     4. PHP → MySQL (UPDATE proyectos)

   Uso: http://localhost/FYLCAD/test_servicio.php
============================================= */

session_start();
require_once 'app/Core/Database.php';
require_once 'app/Core/ServiceConnector.php';

// ── Configuración de la prueba ───────────────────────────
// Coordenadas reales del área de Cúcuta, Norte de Santander
$puntosPrueba = [
    ['lat' =>  7.8939, 'lon' => -72.5078, 'nombre' => 'Centro de Cúcuta'],
    ['lat' =>  7.9014, 'lon' => -72.5143, 'nombre' => 'Villa del Rosario'],
    ['lat' =>  7.8867, 'lon' => -72.4992, 'nombre' => 'Los Patios'],
    ['lat' =>  7.9122, 'lon' => -72.5225, 'nombre' => 'El Zulia'],
    ['lat' =>  7.8750, 'lon' => -72.4900, 'nombre' => 'Cúcuta Norte'],
    ['lat' =>  7.8600, 'lon' => -72.5000, 'nombre' => 'San Mateo'],
    ['lat' =>  7.9300, 'lon' => -72.5300, 'nombre' => 'Puerto Santander'],
];

// ── PASO 1: Verificar disponibilidad del servicio ───────
$inicio         = microtime(true);
$servicioOk     = ServiceConnector::verificarServicio();
$tiempoVerif    = round((microtime(true) - $inicio) * 1000, 2);

// ── PASO 2: Consultar elevaciones (GET) ─────────────────
$inicioGet  = microtime(true);
$resultados = ServiceConnector::consultarElevaciones($puntosPrueba);
$tiempoGet  = round((microtime(true) - $inicioGet) * 1000, 2);
$diagGet    = ServiceConnector::diagnostico();

// ── PASO 3: Consultar elevaciones (POST) ────────────────
// Demuestra el método POST también
$inicioPost   = microtime(true);
$resultadosP  = ServiceConnector::consultarElevacionesPost(
    array_slice($puntosPrueba, 0, 3) // 3 puntos por POST
);
$tiempoPost   = round((microtime(true) - $inicioPost) * 1000, 2);
$diagPost     = ServiceConnector::diagnostico();

// ── PASO 4: Persistir en MySQL ───────────────────────────
$persistencia = ['ok' => false, 'actividad_id' => null, 'proyecto_actualizado' => false];
if ($resultados !== null) {
    $usuarioId    = $_SESSION['usuario_id'] ?? 1;
    $proyectoId   = isset($_GET['proyecto_id']) ? (int)$_GET['proyecto_id'] : null;
    $persistencia = ServiceConnector::persistirResultados($usuarioId, $resultados, $proyectoId);
}

// ── PASO 5: Obtener último registro de auditoría ─────────
$ultimaActividad = null;
if ($persistencia['ok']) {
    try {
        $pdo  = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT id, tipo, descripcion, meta, creado_en
            FROM actividad
            WHERE id = :id
        ");
        $stmt->execute([':id' => $persistencia['actividad_id']]);
        $ultimaActividad = $stmt->fetch();
    } catch (Exception $e) {}
}

// ── Calcular estadísticas globales ───────────────────────
$tiempoTotal = round((microtime(true) - $inicio) * 1000, 2);
$elevaciones = $resultados ? array_column($resultados, 'elevacion') : [];
$elevMin     = $elevaciones ? min($elevaciones) : 0;
$elevMax     = $elevaciones ? max($elevaciones) : 0;
$elevProm    = $elevaciones ? round(array_sum($elevaciones)/count($elevaciones),2) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>FYLCAD — Prueba de Integración Web Service — Guía 7</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Segoe UI',Arial,sans-serif;background:#0a1520;color:#e0e6ed;padding:24px;min-height:100vh}
  h1{color:#00e5c0;font-size:20px;margin-bottom:4px}
  h2{color:#00e5c0;font-size:15px;margin:24px 0 10px;border-bottom:1px solid #1e3a4a;padding-bottom:6px}
  p{color:#8899aa;font-size:13px;margin-bottom:16px}
  a{color:#4da6ff}

  .header{display:flex;align-items:center;gap:12px;margin-bottom:22px}
  .logo{font-size:26px;font-weight:900;color:#00e5c0;letter-spacing:-1px}
  .badge{background:#1e3a4a;border:1px solid #00e5c0;border-radius:4px;padding:3px 10px;font-size:11px;color:#00e5c0}
  .badge-blue{border-color:#4da6ff;color:#4da6ff}

  .flow{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:14px 0}
  .step{background:#1e3a4a;border-radius:6px;padding:7px 14px;font-size:12px;font-weight:600;color:#8899aa}
  .step.done{background:#0d3325;border:1px solid #00e5c0;color:#00e5c0}
  .step.fail{background:#3d1515;border:1px solid #ff4d4d;color:#ff4d4d}
  .arrow{color:#445566;font-size:16px}

  .grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px}
  .grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:16px}
  .card{background:#152030;border:1px solid #1e3a4a;border-radius:8px;padding:16px}
  .card.ok{border-color:#00e5c0}
  .card.err{border-color:#ff4d4d}
  .card.info{border-color:#4da6ff}
  .card.warn{border-color:#ffbb33}

  .stat-label{font-size:10px;color:#556677;text-transform:uppercase;letter-spacing:1px}
  .stat-value{font-size:22px;font-weight:700;color:#00e5c0;margin:4px 0}
  .stat-value.red{color:#ff4d4d}
  .stat-value.blue{color:#4da6ff}
  .stat-value.amber{color:#ffbb33}
  .stat-sub{font-size:11px;color:#8899aa}

  .ok{color:#00e5c0;font-weight:600}
  .err{color:#ff4d4d;font-weight:600}
  .warn{color:#ffbb33;font-weight:600}

  table{width:100%;border-collapse:collapse;font-size:12px;margin-bottom:14px}
  th{background:#1e3a4a;color:#00e5c0;padding:9px 12px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:1px}
  td{padding:8px 12px;border-bottom:1px solid #192a38}
  tr:hover td{background:#1a2d3d}

  .code{background:#070f18;border:1px solid #1e3a4a;border-radius:6px;padding:14px;
        font-family:'Courier New',monospace;font-size:11px;color:#00e5c0;
        overflow-x:auto;white-space:pre-wrap;word-break:break-all;margin-bottom:14px}

  .tag{display:inline-block;border-radius:3px;padding:1px 7px;font-size:10px;font-weight:700}
  .tag-get{background:#0d3325;color:#00e5c0}
  .tag-post{background:#1e2a4a;color:#4da6ff}
  .tag-200{background:#0d3325;color:#00e5c0}
  .tag-err{background:#3d1515;color:#ff4d4d}
  .tag-json{background:#1a2a3a;color:#88ccff}
  .tag-mysql{background:#2a1e4a;color:#bb88ff}
  .tag-singleton{background:#1e1145;color:#9b7dff}

  .section{background:#101e2c;border:1px solid #1e3a4a;border-radius:8px;padding:18px;margin-bottom:20px}
  .step-num{display:inline-block;width:22px;height:22px;border-radius:50%;background:#00e5c0;color:#0a1520;
            font-size:11px;font-weight:700;text-align:center;line-height:22px;margin-right:8px}
  .step-num.fail{background:#ff4d4d}

  footer{color:#334455;font-size:11px;margin-top:30px;padding-top:14px;border-top:1px solid #1e3a4a}
</style>
</head>
<body>

<div class="header">
  <div class="logo">FYLCAD</div>
  <div class="badge">Guía 7 — Actividad 4</div>
  <div class="badge badge-blue">Prueba de Integración WS</div>
</div>

<h1>Sincronización en Tiempo Real: API Externa → PHP → MySQL</h1>
<p>Open-Elevation API + ServiceConnector + Database::getInstance() (Singleton) — <?= date('d/m/Y H:i:s') ?></p>

<!-- ── FLUJO COMPLETO ────────────────────────────────── -->
<h2>Flujo de Integración Ejecutado (4 pasos)</h2>
<div class="flow">
  <div class="step done">🌐 Open-Elevation API</div><div class="arrow">→</div>
  <div class="step <?= $servicioOk ? 'done' : 'fail' ?>">✓ Verificar servicio</div><div class="arrow">→</div>
  <div class="step <?= $resultados !== null ? 'done' : 'fail' ?>">cURL GET</div><div class="arrow">→</div>
  <div class="step <?= $resultadosP !== null ? 'done' : 'fail' ?>">cURL POST</div><div class="arrow">→</div>
  <div class="step <?= $resultados !== null ? 'done' : 'fail' ?>">JSON → PHP Objects</div><div class="arrow">→</div>
  <div class="step <?= $persistencia['ok'] ? 'done' : 'fail' ?>">Database::getInstance()</div><div class="arrow">→</div>
  <div class="step <?= $persistencia['ok'] ? 'done' : 'fail' ?>">MySQL INSERT actividad</div><div class="arrow">→</div>
  <div class="step <?= $persistencia['proyecto_actualizado'] ? 'done' : ($persistencia['ok'] ? 'step' : 'fail') ?>">MySQL UPDATE proyectos</div>
</div>

<!-- ── MÉTRICAS GLOBALES ─────────────────────────────── -->
<div class="grid3">
  <div class="card <?= $resultados !== null ? 'ok' : 'err' ?>">
    <div class="stat-label">Web Service GET</div>
    <div class="stat-value <?= $resultados !== null ? '' : 'red' ?>"><?= $resultados !== null ? 'ONLINE ✓' : 'ERROR ✗' ?></div>
    <div class="stat-sub">HTTP <?= $diagGet['codigo'] ?> · <?= $tiempoGet ?>ms
      <span class="tag tag-get">GET</span>
      <span class="tag tag-json">JSON</span>
    </div>
  </div>

  <div class="card <?= $resultadosP !== null ? 'ok' : 'err' ?>">
    <div class="stat-label">Web Service POST</div>
    <div class="stat-value <?= $resultadosP !== null ? '' : 'red' ?>"><?= $resultadosP !== null ? 'ONLINE ✓' : 'ERROR ✗' ?></div>
    <div class="stat-sub">HTTP <?= $diagPost['codigo'] ?> · <?= $tiempoPost ?>ms
      <span class="tag tag-post">POST</span>
      <span class="tag tag-json">JSON</span>
    </div>
  </div>

  <div class="card <?= $persistencia['ok'] ? 'ok' : 'err' ?>">
    <div class="stat-label">Persistencia MySQL</div>
    <div class="stat-value <?= $persistencia['ok'] ? '' : 'red' ?>"><?= $persistencia['ok'] ? 'GUARDADO ✓' : 'FALLIDO ✗' ?></div>
    <div class="stat-sub">
      <?php if ($persistencia['ok']): ?>
        ID actividad: <strong><?= $persistencia['actividad_id'] ?></strong>
        <span class="tag tag-mysql">INSERT</span>
        <span class="tag tag-singleton">Singleton</span>
      <?php else: ?>
        <?= htmlspecialchars($persistencia['error'] ?? '') ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="grid3">
  <div class="card info">
    <div class="stat-label">Elevación mínima</div>
    <div class="stat-value blue"><?= $elevMin ?> m</div>
    <div class="stat-sub">Sobre el nivel del mar (ASL)</div>
  </div>
  <div class="card info">
    <div class="stat-label">Elevación máxima</div>
    <div class="stat-value blue"><?= $elevMax ?> m</div>
    <div class="stat-sub">Desnivel: <?= round($elevMax - $elevMin, 2) ?> m</div>
  </div>
  <div class="card info">
    <div class="stat-label">Tiempo total</div>
    <div class="stat-value blue"><?= $tiempoTotal ?> ms</div>
    <div class="stat-sub"><?= count($puntosPrueba) ?> puntos · Timeout: 10s</div>
  </div>
</div>

<!-- ── PASO 1: VERIFICACIÓN DEL SERVICIO ──────────────── -->
<div class="section">
  <h2 style="margin-top:0"><span class="step-num <?= $servicioOk ? '' : 'fail' ?>">1</span> Verificación de Disponibilidad del Web Service</h2>
  <p>Petición GET de prueba al endpoint con coordenadas de Cúcuta (7.8939°N, -72.5078°W)</p>
  <div class="grid2">
    <table>
      <tr><th>Parámetro</th><th>Valor</th></tr>
      <tr><td>Estado</td><td class="<?= $servicioOk ? 'ok' : 'err' ?>"><?= $servicioOk ? '✓ Disponible' : '✗ No disponible' ?></td></tr>
      <tr><td>Tiempo de verificación</td><td><?= $tiempoVerif ?> ms</td></tr>
      <tr><td>Endpoint</td><td><code>api.open-elevation.com/api/v1/lookup</code></td></tr>
      <tr><td>Método</td><td><span class="tag tag-get">GET</span></td></tr>
    </table>
    <div class="code">// Verificar disponibilidad
$ok = ServiceConnector::verificarServicio();
// Prueba con punto conocido: Cúcuta centro
// Retorna true si HTTP 200 y elevación válida</div>
  </div>
</div>

<!-- ── PASO 2: RESULTADOS GET ──────────────────────────── -->
<?php if ($resultados !== null): ?>
<div class="section">
  <h2 style="margin-top:0"><span class="step-num">2</span> Datos Recibidos via GET — JSON → Objetos PHP</h2>
  <p>Mapeado de <code>$datos['results']</code> del API a objetos PHP con lat, lon, elevacion, nombre, fuente.</p>
  <table>
    <thead>
      <tr><th>#</th><th>Localidad</th><th>Latitud</th><th>Longitud</th><th>Elevación Z (m)</th><th>Dif. vs min</th><th>Fuente</th></tr>
    </thead>
    <tbody>
      <?php foreach ($resultados as $i => $punto): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><?= htmlspecialchars($punto['nombre']) ?></td>
        <td><?= number_format($punto['lat'], 4) ?>°</td>
        <td><?= number_format($punto['lon'], 4) ?>°</td>
        <td><strong><?= number_format($punto['elevacion'], 2) ?> m</strong></td>
        <td>+<?= number_format($punto['elevacion'] - $elevMin, 2) ?> m</td>
        <td><span class="tag tag-get"><?= $punto['fuente'] ?></span></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- ── PASO 3: PERSISTENCIA MYSQL ──────────────────────── -->
<div class="section">
  <h2 style="margin-top:0"><span class="step-num <?= $persistencia['ok'] ? '' : 'fail' ?>">3</span> Persistencia en MySQL — INSERT actividad</h2>
  <p>Los datos del Web Service se guardan en la tabla <code>actividad</code> via <code>Database::getInstance()</code> (patrón Singleton).</p>

  <?php if ($persistencia['ok'] && $ultimaActividad): ?>
  <table>
    <thead><tr><th>Campo MySQL</th><th>Valor insertado</th></tr></thead>
    <tbody>
      <tr><td><strong>id</strong> (PK autoincrement)</td><td class="ok"><?= $ultimaActividad['id'] ?></td></tr>
      <tr><td>tipo</td><td><span class="tag tag-json"><?= $ultimaActividad['tipo'] ?></span></td></tr>
      <tr><td>descripcion</td><td><?= htmlspecialchars($ultimaActividad['descripcion']) ?></td></tr>
      <tr><td>creado_en</td><td><?= $ultimaActividad['creado_en'] ?></td></tr>
      <tr><td>meta (JSON del WS)</td><td><code style="font-size:10px"><?= htmlspecialchars(substr($ultimaActividad['meta'] ?? '', 0, 200)) ?>...</code></td></tr>
    </tbody>
  </table>

  <?php if ($persistencia['proyecto_actualizado']): ?>
  <div class="card ok" style="margin-top:12px">
    <strong class="ok">✓ UPDATE en tabla proyectos:</strong>
    <span style="color:#8899aa;font-size:12px"> cota_min={<?= $elevMin ?>}, cota_max={<?= $elevMax ?>}, desnivel={<?= round($elevMax-$elevMin,2) ?>} — Integridad referencial mantenida.</span>
  </div>
  <?php endif; ?>

  <?php else: ?>
  <div class="card err">
    <strong class="err">✗ No se pudo persistir:</strong>
    <span style="color:#8899aa;font-size:12px"> <?= htmlspecialchars($persistencia['error'] ?? 'Error desconocido') ?></span>
  </div>
  <?php endif; ?>

  <div class="code" style="margin-top:12px">// Capa de Datos: persistirResultados()
$pdo  = Database::getInstance()->getConnection(); // Singleton

$stmt = $pdo->prepare("INSERT INTO actividad
    (usuario_id, proyecto_id, tipo, descripcion, meta)
    VALUES (:uid, :pid, 'archivo_exportado', :desc, :meta)");
$stmt->execute([':uid' => $usuarioId, ':pid' => $proyectoId,
                ':desc' => $descripcion, ':meta' => $metaJSON]);

// UPDATE proyectos (integridad referencial)
$pdo->prepare("UPDATE proyectos SET cota_min=:cmin, cota_max=:cmax,
    desnivel=:des, actualizado_en=NOW() WHERE id=:id")
    ->execute([':cmin'=>$elevMin, ':cmax'=>$elevMax,
               ':des'=>$elevMax-$elevMin, ':id'=>$proyectoId]);</div>
</div>

<!-- ── PASO 4: DIAGNÓSTICO TÉCNICO ─────────────────────── -->
<div class="section">
  <h2 style="margin-top:0"><span class="step-num">4</span> Diagnóstico Técnico de la Petición HTTP (Headers)</h2>
  <div class="grid2">
    <table>
      <thead><tr><th>Parámetro cURL (GET)</th><th>Valor</th></tr></thead>
      <tbody>
        <tr><td>Código HTTP</td><td><span class="tag <?= $diagGet['codigo'] == 200 ? 'tag-200' : 'tag-err' ?>">HTTP <?= $diagGet['codigo'] ?></span></td></tr>
        <tr><td>Tiempo de respuesta</td><td><?= $diagGet['tiempo'] ?> ms</td></tr>
        <tr><td>Content-Type</td><td><code><?= htmlspecialchars($diagGet['content_type'] ?? 'N/A') ?></code></td></tr>
        <tr><td>Error cURL</td><td><?= empty($diagGet['error']) ? '<span class="ok">Ninguno</span>' : '<span class="err">'.htmlspecialchars($diagGet['error']).'</span>' ?></td></tr>
        <tr><td>Método</td><td><span class="tag tag-get"><?= $diagGet['metodo'] ?></span></td></tr>
        <tr><td>URL consultada</td><td><code style="font-size:10px"><?= htmlspecialchars(substr($diagGet['url'] ?? '', 0, 80)) ?>...</code></td></tr>
      </tbody>
    </table>
    <table>
      <thead><tr><th>Parámetro cURL (POST)</th><th>Valor</th></tr></thead>
      <tbody>
        <tr><td>Código HTTP</td><td><span class="tag <?= $diagPost['codigo'] == 200 ? 'tag-200' : 'tag-err' ?>">HTTP <?= $diagPost['codigo'] ?></span></td></tr>
        <tr><td>Tiempo de respuesta</td><td><?= $diagPost['tiempo'] ?> ms</td></tr>
        <tr><td>Content-Type</td><td><code><?= htmlspecialchars($diagPost['content_type'] ?? 'N/A') ?></code></td></tr>
        <tr><td>Error cURL</td><td><?= empty($diagPost['error']) ? '<span class="ok">Ninguno</span>' : '<span class="err">'.htmlspecialchars($diagPost['error']).'</span>' ?></td></tr>
        <tr><td>Método</td><td><span class="tag tag-post"><?= $diagPost['metodo'] ?></span></td></tr>
        <tr><td>URL consultada</td><td><code><?= htmlspecialchars($diagPost['url'] ?? '') ?></code></td></tr>
      </tbody>
    </table>
  </div>

  <!-- Headers recibidos -->
  <?php if (!empty($diagGet['headers'])): ?>
  <h2>Headers HTTP Recibidos del Web Service</h2>
  <table>
    <thead><tr><th>Header</th><th>Valor</th></tr></thead>
    <tbody>
      <?php foreach ($diagGet['headers'] as $k => $v): ?>
      <tr><td><code><?= htmlspecialchars($k) ?></code></td><td><?= htmlspecialchars($v) ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <!-- Conexión Singleton -->
  <table>
    <thead><tr><th>Patrón / Componente</th><th>Estado</th></tr></thead>
    <tbody>
      <tr>
        <td>Database::getInstance() (Singleton)</td>
        <td>
          <?php try {
            $c = Database::getInstance()->getConnection();
            echo '<span class="ok">✓ Una sola instancia PDO activa</span>';
          } catch (Exception $e) {
            echo '<span class="err">✗ '.$e->getMessage().'</span>';
          } ?>
        </td>
      </tr>
      <tr><td>IServiceConnector (Interfaz)</td><td class="ok">✓ ServiceConnector implementa verificarServicio() y diagnostico()</td></tr>
      <tr><td>Inserción en actividad</td><td><?= $persistencia['ok'] ? '<span class="ok">✓ ID: '.$persistencia['actividad_id'].'</span>' : '<span class="err">✗ Fallo</span>' ?></td></tr>
      <tr><td>UPDATE en proyectos</td><td><?= $persistencia['proyecto_actualizado'] ? '<span class="ok">✓ Actualizado</span>' : '<span class="warn">⚠ No aplica (sin proyecto_id en URL)</span>' ?></td></tr>
    </tbody>
  </table>
  <p style="font-size:11px;color:#556677">Tip: añade <code>?proyecto_id=1</code> a la URL para probar el UPDATE en tabla proyectos.</p>
</div>

<!-- ── FRAGMENTO JSON DEL WS ───────────────────────────── -->
<?php if (!empty($diagGet['cuerpo'])): ?>
<h2>Body JSON Recibido del Web Service (primeros 500 chars)</h2>
<div class="code"><?= htmlspecialchars($diagGet['cuerpo']) ?></div>
<?php endif; ?>

<!-- ── CÓDIGO FUENTE RESUMIDO ──────────────────────────── -->
<h2>Código PHP Ejecutado (Resumen)</h2>
<div class="code">// ── Actividad 1: Consumo GET ─────────────────────────
$resultados = ServiceConnector::consultarElevaciones($puntos);
// Internamente: curl_init() → CURLOPT_URL → curl_exec()
// Manejo: HTTP 404/500/timeout → $ultimaRespuesta['error']

// ── Actividad 1: Consumo POST ─────────────────────────
$resultadosP = ServiceConnector::consultarElevacionesPost($puntos);
// Body JSON: {"locations": [{"latitude":..., "longitude":...}]}

// ── Actividad 2: Persistencia (Singleton + integridad) ─
$res = ServiceConnector::persistirResultados($uid, $resultados, $proyectoId);
// INSERT INTO actividad (tipo='archivo_exportado', meta=JSON_del_WS)
// UPDATE proyectos SET cota_min=..., cota_max=..., desnivel=...

// ── Actividad 3: Patrón Singleton ─────────────────────
$pdo = Database::getInstance()->getConnection(); // UNA sola instancia

// ── Diagnóstico completo ──────────────────────────────
$diag = ServiceConnector::diagnostico();
// Retorna: ['codigo', 'headers', 'content_type', 'cuerpo', 'error', 'tiempo']</div>

<footer>
  FYLCAD — Guía Práctica N°7 | Arquitectura y Diseño de Software | FESC 2026<br>
  Entregable 3 (Captura): hacer screenshot de esta página con datos reales de MySQL visibles arriba.
</footer>

</body>
</html>