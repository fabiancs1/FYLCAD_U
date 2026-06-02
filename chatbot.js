/* ═══════════════════════════════════════════════
   FYLCAD — Responsive Global
   Archivo: css/responsive.css
   Se incluye en todas las páginas
═══════════════════════════════════════════════ */

/* ── Dashboard ── */
@media (max-width: 480px) {

    /* Topbar */
    .topbar { padding: 0 14px; gap: 14px; }
    .topbar-logo { font-size: 20px; letter-spacing: 3px; }
    .topbar-user span { display: none; }

    /* Bento cards */
    .bento-wrap { padding: 10px; gap: 10px; }
    .bento-row  { gap: 10px; }
    .bento-card { padding: 14px; border-radius: 14px; }

    /* Stats */
    .stat-num { font-size: 26px !important; }
    .stat-lbl { font-size: 11px; }
    .stat-ico  { width: 32px; height: 32px; font-size: 15px; }

    /* Card bienvenida */
    .card-welcome h2 { font-size: 18px !important; }
    .card-welcome p  { font-size: 12px; }

    /* Proyectos recientes */
    .proj-area, .proj-pts { display: none; }
    .proy-row { padding: 10px 12px; }

    /* Actividad */
    .act-desc { font-size: 12px; }
    .act-time { font-size: 10px; }
}

/* ── Módulo 3D (proyecto.php) ── */
@media (max-width: 860px) {

    /* Layout principal */
    .layout { flex-direction: column !important; }
    .sidebar {
        width: 100% !important;
        height: auto !important;
        max-height: 280px;
        overflow-y: auto;
        border-right: none !important;
        border-bottom: 1px solid var(--border, rgba(255,255,255,0.08));
    }

    /* Canvas 3D */
    .canvas-wrap { height: 55vw; min-height: 250px; }
    #visor3D { width: 100% !important; }

    /* Mini canvas de análisis */
    .mini-canvas { height: 80px !important; }

    /* Paneles laterales */
    .panel-tabs { overflow-x: auto; white-space: nowrap; }
    .tab-btn    { padding: 6px 12px; font-size: 11px; }
}

@media (max-width: 480px) {
    .sidebar { max-height: 220px; }
    .canvas-wrap { height: 60vw; }
    .resultado-grid { grid-template-columns: 1fr 1fr !important; gap: 8px; }
    .res-val  { font-size: 16px !important; }
    .res-lbl  { font-size: 10px; }
}

/* ── Cotización ── */
@media (max-width: 768px) {
    .cotizacion-grid,
    .cot-grid { grid-template-columns: 1fr !important; }

    .apu-table { font-size: 11px; }
    .apu-table th,
    .apu-table td { padding: 6px 8px; }

    /* Ocultar columnas menos importantes */
    .col-rendimiento,
    .col-cuadrilla { display: none; }
}

@media (max-width: 480px) {
    .cot-header h1 { font-size: 20px; }
    .cot-total { font-size: 22px !important; }
}

/* ── Mis Proyectos ── */
@media (max-width: 768px) {
    .proyectos-grid { grid-template-columns: 1fr !important; }
    .proj-card { padding: 14px; }
    .proj-stats { flex-wrap: wrap; gap: 8px; }
}

@media (max-width: 480px) {
    .page-header { flex-direction: column; align-items: flex-start; gap: 10px; }
    .page-header h1 { font-size: 20px; }
    .filtros-bar { flex-wrap: wrap; }
    .filtros-bar select,
    .filtros-bar input { width: 100%; }
}

/* ── Perfil ── */
@media (max-width: 768px) {
    .perfil-grid { grid-template-columns: 1fr !important; }
    .perfil-avatar-wrap { text-align: center; }
}

@media (max-width: 480px) {
    .perfil-nombre { font-size: 20px; }
    .perfil-form { padding: 16px; }
    .form-row { flex-direction: column; }
    .form-row .form-group { width: 100%; }
}

/* ── Planes ── */
@media (max-width: 480px) {
    .plans-hero h1 { font-size: 26px !important; }
    .plans-hero p  { font-size: 13px; }
    .plan-price    { font-size: 32px !important; }
    .compare-table { font-size: 12px; }
    .compare-table th,
    .compare-table td { padding: 8px 6px; }
}

/* ── Auth (login, register) ── */
@media (max-width: 480px) {
    .auth-card { padding: 24px 18px; border-radius: 14px; }
    .auth-logo  { font-size: 24px; }
    .auth-title { font-size: 20px; }
    .btn-auth   { height: 44px; font-size: 13px; }
}

/* ── Nav global (index.php) ── */
@media (max-width: 768px) {
    nav { padding: 16px 20px !important; }
    .nav-right { gap: 16px; }
    .nav-link  { display: none; }
}

@media (max-width: 480px) {
    .hero h1   { font-size: 32px !important; }
    .hero p    { font-size: 14px; }
    .hero-btns { flex-direction: column; width: 100%; }
    .hero-btns a { width: 100%; text-align: center; }
    .features-grid,
    .steps-grid { grid-template-columns: 1fr !important; }
}

/* ── Utilidades globales ── */
@media (max-width: 480px) {
    /* Tablas en móvil: scroll horizontal */
    .table-wrap,
    .tabla-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    table { min-width: 480px; }

    /* Botones más grandes para touch */
    button, .btn, a.btn {
        min-height: 40px;
        touch-action: manipulation;
    }

    /* Inputs más cómodos */
    input, select, textarea {
        font-size: 16px !important; /* evita zoom en iOS */
    }
}
