/* ═══════════════════════════════════════════════
   FYLCAD — Mejoras visuales cotizacion.php
   Archivo: css/cotizacion_mejoras.css
═══════════════════════════════════════════════ */

/* ── Header mejorado ── */
header {
    background: rgba(10, 17, 32, 0.98) !important;
    backdrop-filter: blur(12px);
    box-shadow: 0 1px 30px rgba(0,0,0,0.5);
}

.logo {
    letter-spacing: 2px;
    text-shadow: 0 0 20px rgba(0,229,192,0.2);
}

.hbadge {
    font-size: 10px;
    animation: badgePulse 3s ease-in-out infinite;
    border-radius: 20px;
    padding: 3px 12px;
}

@keyframes badgePulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(0,229,192,0); }
    50%       { box-shadow: 0 0 0 3px rgba(0,229,192,0.08); }
}

/* ── Sidebar ── */
.sidebar {
    background: linear-gradient(180deg, #080d18 0%, #060a12 100%) !important;
}

/* ── Tabs sidebar ── */
.stab {
    font-size: 10px;
    letter-spacing: 0.3px;
    transition: all 0.2s;
    position: relative;
}

.stab.on::after {
    content: '';
    position: absolute;
    bottom: 0; left: 20%; right: 20%;
    height: 2px;
    background: var(--acc);
    border-radius: 2px 2px 0 0;
    box-shadow: 0 0 8px rgba(0,229,192,0.5);
}

/* ── Card info proyecto ── */
.proy-card {
    background: linear-gradient(135deg, rgba(0,229,192,0.05), transparent) !important;
    border-bottom: 1px solid rgba(0,229,192,0.08) !important;
    padding: 14px 16px !important;
}

.proy-nombre {
    font-size: 14px !important;
    letter-spacing: -0.3px;
    color: #f0f6ff !important;
}

/* ── KPIs sidebar ── */
.kpis {
    border-bottom: 1px solid rgba(0,229,192,0.06) !important;
    animation-delay: 0.05s;
}

.kpi {
    padding: 10px 12px !important;
    transition: background 0.2s;
}

.kpi:hover { background: rgba(0,229,192,0.03); }

.kpi-v {
    font-size: 13px !important;
    text-shadow: 0 0 10px rgba(0,229,192,0.3);
}

/* ── Zona section ── */
.zbtn {
    transition: all 0.2s cubic-bezier(.34,1.56,.64,1);
    border-radius: 6px;
}

.zbtn:hover { transform: translateY(-1px); }
.zbtn.on { box-shadow: 0 2px 10px rgba(0,229,192,0.12); }

.zrow {
    border-radius: 5px;
    transition: background 0.15s;
}
.zrow:hover { background: rgba(0,229,192,0.04) !important; }

/* ── Inputs mejorados ── */
.inp {
    background: rgba(255,255,255,0.04) !important;
    border: 1px solid rgba(255,255,255,0.07) !important;
    border-radius: 7px !important;
    transition: all 0.2s !important;
    font-size: 12px !important;
}

.inp:focus {
    background: rgba(255,255,255,0.07) !important;
    border-color: rgba(0,229,192,0.4) !important;
    box-shadow: 0 0 0 3px rgba(0,229,192,0.06) !important;
}

/* ── APU items ── */
.apu-item {
    border-radius: 6px;
    padding: 6px 4px !important;
    transition: background 0.15s;
}

.apu-item:hover { background: rgba(0,229,192,0.03); }

.apu-hdr {
    border-bottom: 1px solid rgba(0,229,192,0.1) !important;
}

.apu-num {
    box-shadow: 0 0 8px rgba(0,229,192,0.1);
}

.ai-inp {
    border-radius: 5px !important;
    transition: all 0.2s !important;
}

.ai-inp:focus {
    box-shadow: 0 0 0 2px rgba(0,229,192,0.1) !important;
}

/* ── Factor de complejidad ── */
.factor-row {
    background: rgba(245,158,11,0.04);
    border-radius: 8px;
    padding: 10px 12px !important;
    border: 1px solid rgba(245,158,11,0.1);
}

.factor-input {
    border-radius: 6px !important;
    transition: all 0.2s !important;
}

.factor-input:focus {
    box-shadow: 0 0 0 3px rgba(245,158,11,0.1) !important;
}

/* ── Barra total ── */
.totbar {
    background: linear-gradient(0deg, rgba(0,0,0,0.4), rgba(10,17,32,0.98)) !important;
    border-top: 1px solid rgba(0,229,192,0.1) !important;
    padding: 10px 16px !important;
}

.ti-v.big {
    font-size: 15px !important;
    text-shadow: 0 0 12px rgba(0,229,192,0.4);
}

/* ── Tabs principales ── */
.mtab {
    font-size: 11px;
    letter-spacing: 0.3px;
    transition: all 0.2s;
    padding: 11px 16px;
}

.mtab:hover { color: var(--txt) !important; }

.mtab.on {
    color: var(--acc) !important;
    text-shadow: 0 0 10px rgba(0,229,192,0.3);
}

/* ── Toolbar del plano ── */
.plano-toolbar {
    background: rgba(8,13,24,0.98) !important;
    border-bottom: 1px solid rgba(0,229,192,0.07) !important;
    padding: 7px 14px !important;
}

.ptool {
    font-size: 10px;
    border-radius: 6px;
    transition: all 0.2s cubic-bezier(.34,1.56,.64,1);
}

.ptool:hover { transform: translateY(-1px); }

.ptool.on {
    box-shadow: 0 2px 10px rgba(0,229,192,0.15);
}

/* ── Canvas del plano ── */
.plano-wrap {
    background: radial-gradient(ellipse at 50% 40%, rgba(0,229,192,0.02) 0%, #060a12 65%);
}

/* ── Overlay zona info ── */
.zona-ov {
    border-radius: 10px !important;
    padding: 10px 14px !important;
    box-shadow: 0 8px 30px rgba(0,0,0,0.4);
}

.zona-ov-title {
    color: var(--acc) !important;
    text-shadow: 0 0 8px rgba(0,229,192,0.3);
}

/* ── Resumen tab ── */
.res-header {
    background: linear-gradient(135deg, rgba(0,229,192,0.06), transparent);
    border-bottom: 1px solid rgba(0,229,192,0.08);
    padding: 16px 20px;
}

/* ── Tabla de resumen ── */
.res-table th {
    background: rgba(0,0,0,0.2);
    color: rgba(0,229,192,0.7);
    font-size: 10px;
    letter-spacing: 1px;
}

.res-table tr:hover td {
    background: rgba(0,229,192,0.03);
}

/* ── Total grande ── */
.cot-grand-val {
    text-shadow: 0 0 20px rgba(0,229,192,0.4);
    font-size: 28px !important;
}

.cot-grand-total {
    border-radius: 10px !important;
    position: relative;
    overflow: hidden;
}

.cot-grand-total::before {
    content: '';
    position: absolute;
    top: -50%; left: -50%;
    width: 200%; height: 200%;
    background: radial-gradient(circle at center, rgba(0,229,192,0.05) 0%, transparent 60%);
    pointer-events: none;
}

/* ── Botones de acción ── */
.btn {
    transition: all 0.2s cubic-bezier(.34,1.56,.64,1) !important;
    border-radius: 7px !important;
}

.btn:hover { transform: translateY(-1px); }

.btn-acc:hover {
    box-shadow: 0 4px 15px rgba(0,229,192,0.3) !important;
}

.btn-guardar-cot {
    transition: all 0.2s cubic-bezier(.34,1.56,.64,1) !important;
}

.btn-guardar-cot:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0,229,192,0.2);
}

/* ── Toggle moneda ── */
.mbtn {
    transition: all 0.2s;
    border-radius: 4px !important;
}

.mbtn.on {
    box-shadow: 0 0 8px rgba(0,229,192,0.15);
}

/* ── Scrollbars ── */
.spane::-webkit-scrollbar { width: 4px; }
.spane::-webkit-scrollbar-thumb {
    background: rgba(0,229,192,0.15);
    border-radius: 4px;
}

.apu-wrap::-webkit-scrollbar { width: 4px; }
.apu-wrap::-webkit-scrollbar-thumb {
    background: rgba(0,229,192,0.15);
    border-radius: 4px;
}

/* ── Animaciones de entrada ── */
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}

.proy-card,
.kpis,
.zona-sec,
.id-sec,
.apu-wrap { animation: fadeSlideUp 0.3s ease forwards; }

/* animation-delay merged into .kpis above */
.zona-sec   { animation-delay: 0.10s; }
.id-sec     { animation-delay: 0.15s; }

/* ── Modo impresión ── */
@media print {
    header, .sidebar, .mtabs, .plano-toolbar { display: none !important; }
    .app { grid-template-columns: 1fr !important; }
    .main { overflow: visible !important; }
    .mpane { position: static !important; display: block !important; }
}
