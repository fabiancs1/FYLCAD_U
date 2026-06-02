/* ═══════════════════════════════════════════════
   FYLCAD — Mejoras visuales proyecto.php
   Archivo: css/proyecto_mejoras.css
═══════════════════════════════════════════════ */

/* ── Header mejorado ── */
.header {
    background: rgba(12, 17, 32, 0.95);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(0,229,192,0.1);
    box-shadow: 0 1px 30px rgba(0,0,0,0.4);
}

.header::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(0,229,192,0.4), transparent);
}

.header-proyecto { position: relative; }

.logo {
    font-size: 20px;
    letter-spacing: 4px;
    text-shadow: 0 0 20px rgba(0,229,192,0.3);
}

.header-tag {
    font-size: 10px;
    color: rgba(0,229,192,0.5);
    letter-spacing: 2px;
    font-family: 'DM Mono', monospace;
}

.btn-nav {
    font-size: 11px;
    padding: 6px 14px;
    border-radius: 6px;
    transition: all 0.2s cubic-bezier(.34,1.56,.64,1);
}

.btn-nav:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,229,192,0.15);
}

/* ── Sidebar mejorada ── */
.sidebar {
    background: linear-gradient(180deg, #0a0f1c 0%, #080d18 100%);
    border-right: 1px solid rgba(0,229,192,0.08);
}

/* ── Panel headers ── */
.panel-header {
    background: rgba(0,0,0,0.2);
    border-bottom: 1px solid rgba(255,255,255,0.05);
    padding: 12px 16px;
}

.panel-header h2 {
    font-size: 10px;
    letter-spacing: 2px;
    color: rgba(0,229,192,0.6);
}

/* ── Drop zone mejorada ── */
.drop-zone {
    border: 1.5px dashed rgba(0,229,192,0.2);
    border-radius: 12px;
    background: rgba(0,229,192,0.03);
    transition: all 0.3s cubic-bezier(.34,1.56,.64,1);
    position: relative;
    overflow: hidden;
}

.drop-zone::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center, rgba(0,229,192,0.05) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s;
}

.drop-zone:hover::before,
.drop-zone.drag-over::before { opacity: 1; }

.drop-zone:hover,
.drop-zone.drag-over {
    border-color: rgba(0,229,192,0.5);
    transform: scale(1.01);
    box-shadow: 0 0 30px rgba(0,229,192,0.08), inset 0 0 20px rgba(0,229,192,0.04);
}

.drop-icon {
    font-size: 28px;
    animation: flotar 2.5s ease-in-out infinite;
}

@keyframes flotar {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-5px); }
}

/* ── Botón procesar mejorado ── */
.btn-primary {
    background: linear-gradient(135deg, #00e5c0, #00c4a7);
    border-radius: 10px;
    font-size: 12px;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(.34,1.56,.64,1);
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
    transition: left 0.5s;
}

.btn-primary:hover:not(:disabled)::before { left: 100%; }

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,229,192,0.35);
}

/* ── KPI cards mejoradas ── */
.kpi-card {
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.kpi-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    opacity: 0;
    transition: opacity 0.2s;
}

.kpi-blue::after  { background: #60a5fa; }
.kpi-teal::after  { background: #00e5c0; }
.kpi-amber::after { background: #fbbf24; }
.kpi-red::after   { background: #f87171; }

.kpi-card:hover::after { opacity: 1; }

.kpi-val {
    transition: transform 0.2s;
    display: block;
}

.kpi-card:hover .kpi-val { transform: scale(1.05); }

.kpi-icon {
    font-size: 14px;
    opacity: 0.5;
    transition: opacity 0.2s, transform 0.2s;
}
.kpi-card:hover .kpi-icon {
    opacity: 0.8;
    transform: scale(1.15);
}

/* ── Toolbar del visor ── */
.viewer-toolbar {
    background: rgba(10, 15, 28, 0.98);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(0,229,192,0.08);
    height: 46px;
}

.ctrl-btn {
    font-size: 10px;
    padding: 5px 13px;
    border-radius: 6px;
    transition: all 0.2s cubic-bezier(.34,1.56,.64,1);
    letter-spacing: 0.3px;
}

.ctrl-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,229,192,0.12);
}

.ctrl-btn.active {
    box-shadow: 0 0 15px rgba(0,229,192,0.15);
}

/* ── Visor canvas area ── */
.canvas-wrap {
    background: radial-gradient(ellipse at 50% 30%, rgba(0,229,192,0.02) 0%, #05080f 60%);
}

/* ── Coord readout mejorado ── */
.coord-readout {
    background: rgba(5,8,15,0.9);
    border: 1px solid rgba(0,229,192,0.12);
    backdrop-filter: blur(8px);
    border-radius: 6px;
    font-size: 10px;
    letter-spacing: 0.3px;
    transition: all 0.2s;
}

/* ── Z Legend mejorada ── */
.z-legend {
    background: rgba(5,8,15,0.9);
    border: 1px solid rgba(0,229,192,0.12);
    backdrop-filter: blur(8px);
}

.legend-bar {
    background: linear-gradient(90deg, #4ade80, #22d3ee, #00e5c0, #f59e0b, #ef4444);
    box-shadow: 0 0 8px rgba(0,229,192,0.2);
}

/* ── Tabs de cálculos ── */
.calc-tab {
    border-radius: 6px;
    transition: all 0.2s cubic-bezier(.34,1.56,.64,1);
}

.calc-tab:hover { transform: translateY(-1px); }

.calc-tab.active {
    box-shadow: 0 2px 10px rgba(0,229,192,0.12);
}

/* ── Barra de elevación ── */
.elev-bar-track {
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    height: 8px;
}

.elev-bar-mid {
    box-shadow: 0 0 10px rgba(255,255,255,0.8);
}

/* ── Métricas secundarias ── */
.msec-val {
    font-size: 11px;
    text-shadow: 0 0 8px rgba(0,229,192,0.4);
}

/* ── Botón ir a cotización ── */
.btn-cot-link {
    background: linear-gradient(135deg, rgba(0,229,192,0.08), rgba(0,0,0,0));
    transition: all 0.3s cubic-bezier(.34,1.56,.64,1);
}

.btn-cot-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0,229,192,0.15);
}

/* ── Empty state mejorado ── */
.empty-state { gap: 18px; }

.empty-icon {
    font-size: 52px;
    filter: drop-shadow(0 0 20px rgba(0,229,192,0.15));
}

/* ── Controls hint ── */
.controls-hint {
    background: rgba(5,8,15,0.7);
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.04);
    backdrop-filter: blur(6px);
}

/* ── Terrain badge ── */
.terrain-badge {
    font-size: 11px;
    padding: 3px 12px;
    letter-spacing: 0.5px;
    box-shadow: 0 0 12px rgba(0,229,192,0.1);
}

/* ── Scrollbar del sidebar ── */
.sidebar::-webkit-scrollbar { width: 4px; }
.sidebar::-webkit-scrollbar-thumb {
    background: rgba(0,229,192,0.15);
    border-radius: 4px;
}

/* ── Animación de entrada al sidebar ── */
@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-8px); }
    to   { opacity: 1; transform: translateX(0); }
}

.panel {
    animation: slideInLeft 0.3s ease forwards;
}

.panel:nth-child(2) { animation-delay: 0.05s; }
.panel:nth-child(3) { animation-delay: 0.10s; }
.panel:nth-child(4) { animation-delay: 0.15s; }
