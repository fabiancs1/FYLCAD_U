/**
 * FYLCAD — Mejoras visuales de canvas
 * Archivo: js/canvas_mejoras.js
 * Mejora: miniCanvas de Perfil + Corte/Relleno + Proyección 3D
 */
(function () {
'use strict';

/* ══════════════════════════════════════════════════════
   1. MEJORAS GLOBALES DE MINI-CANVAS
   Reemplaza mkCanvas para fondo más rico
══════════════════════════════════════════════════════ */
const _origMkCanvas = window.mkCanvas;
if (typeof _origMkCanvas === 'function') {
    window.mkCanvas = function(id, h) {
        const result = _origMkCanvas(id, h);
        if (!result) return result;
        // Fondo con gradiente oscuro estilo index
        const { ctx, W, H } = result;
        const bg = ctx.createLinearGradient(0, 0, 0, H);
        bg.addColorStop(0, '#070d1a');
        bg.addColorStop(1, '#050810');
        ctx.fillStyle = bg;
        ctx.fillRect(0, 0, W, H);
        // Grid sutil
        ctx.strokeStyle = 'rgba(0,229,192,0.04)';
        ctx.lineWidth = 0.5;
        for (let x = 0; x < W; x += 20) {
            ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, H); ctx.stroke();
        }
        for (let y = 0; y < H; y += 20) {
            ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(W, y); ctx.stroke();
        }
        return result;
    };
}

/* ══════════════════════════════════════════════════════
   2. MEJORA DEL CANVAS CORTE/RELLENO
   Animación de barras + más info visual
══════════════════════════════════════════════════════ */
const _origDrawCorte = window.drawCorteRelleno;

function drawCorteRellenoMejorado(secs) {
    const cv = document.getElementById('miniCanvasCorte');
    if (!cv || !secs || secs.length < 2) return;

    const ctx = cv.getContext('2d');
    const W = cv.offsetWidth || cv.width;
    const H = parseInt(cv.getAttribute('height')) || 110;
    cv.width = W; cv.height = H;

    const PAD = { t: 20, r: 12, b: 30, l: 12 };
    const n = secs.length;
    const mxA = Math.max(...secs.map(s => Math.abs(s.cr)), 0.1);
    const base = H / 2;
    const tX = i => PAD.l + (i / (n - 1)) * (W - PAD.l - PAD.r);
    const barMaxH = (H - PAD.t - PAD.b) / 2;

    // Fondo con gradiente
    const bg = ctx.createLinearGradient(0, 0, 0, H);
    bg.addColorStop(0, '#070d1a');
    bg.addColorStop(1, '#050810');
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, W, H);

    // Grid horizontal sutil
    ctx.strokeStyle = 'rgba(255,255,255,0.04)';
    ctx.lineWidth = 0.5;
    for (let y = PAD.t; y < H - PAD.b; y += (H - PAD.t - PAD.b) / 4) {
        ctx.beginPath(); ctx.moveTo(PAD.l, y); ctx.lineTo(W - PAD.r, y); ctx.stroke();
    }

    // Zona de corte (arriba) - fondo rojo sutil
    const zCorte = ctx.createLinearGradient(0, PAD.t, 0, base);
    zCorte.addColorStop(0, 'rgba(239,68,68,0.04)');
    zCorte.addColorStop(1, 'transparent');
    ctx.fillStyle = zCorte;
    ctx.fillRect(PAD.l, PAD.t, W - PAD.l - PAD.r, base - PAD.t);

    // Zona de relleno (abajo) - fondo verde sutil
    const zRelleno = ctx.createLinearGradient(0, base, 0, H - PAD.b);
    zRelleno.addColorStop(0, 'transparent');
    zRelleno.addColorStop(1, 'rgba(34,197,94,0.04)');
    ctx.fillStyle = zRelleno;
    ctx.fillRect(PAD.l, base, W - PAD.l - PAD.r, H - PAD.b - base);

    // Línea sub-rasante con brillo
    ctx.save();
    ctx.shadowColor = 'rgba(245,158,11,0.4)';
    ctx.shadowBlur = 4;
    ctx.strokeStyle = 'rgba(245,158,11,0.6)';
    ctx.lineWidth = 1.5;
    ctx.setLineDash([5, 4]);
    ctx.beginPath(); ctx.moveTo(PAD.l, base); ctx.lineTo(W - PAD.r, base); ctx.stroke();
    ctx.setLineDash([]);
    ctx.restore();

    // Labels laterales
    ctx.font = "8px 'DM Mono',monospace";
    ctx.fillStyle = 'rgba(239,68,68,0.6)'; ctx.textAlign = 'right';
    ctx.fillText('▲ CORTE', W - PAD.r, PAD.t + 10);
    ctx.fillStyle = 'rgba(34,197,94,0.6)';
    ctx.fillText('▼ RELLENO', W - PAD.r, H - PAD.b - 4);

    // Barras animadas
    const animDur = 600;
    const startTime = performance.now();

    function animateBar(timestamp) {
        const elapsed = timestamp - startTime;
        const progress = Math.min(elapsed / animDur, 1);
        // easeOutBack
        const ease = 1 + 2.70158 * Math.pow(progress - 1, 3) + 1.70158 * Math.pow(progress - 1, 2);
        const p = Math.max(0, Math.min(ease, 1));

        // Limpiar solo área de barras
        ctx.clearRect(PAD.l, PAD.t, W - PAD.l - PAD.r, H - PAD.t - PAD.b);

        // Re-dibujar zonas
        ctx.fillStyle = zCorte; ctx.fillRect(PAD.l, PAD.t, W - PAD.l - PAD.r, base - PAD.t);
        ctx.fillStyle = zRelleno; ctx.fillRect(PAD.l, base, W - PAD.l - PAD.r, H - PAD.b - base);

        secs.forEach((s, i) => {
            const x = tX(i);
            const bH = (Math.abs(s.cr) / mxA * barMaxH) * p;
            const isC = s.cr < 0;
            const col = isC ? '#ef4444' : '#22c55e';
            const bW = Math.max(8, (W - PAD.l - PAD.r) / n * 0.6);

            // Sombra de color
            ctx.save();
            ctx.shadowColor = col;
            ctx.shadowBlur = 8;

            const grd = ctx.createLinearGradient(0, isC ? base : base - bH, 0, isC ? base + bH : base);
            grd.addColorStop(0, col + 'cc');
            grd.addColorStop(1, col + '22');
            ctx.fillStyle = grd;

            if (ctx.roundRect) {
                ctx.beginPath();
                if (isC) ctx.roundRect(x - bW / 2, base, bW, bH, [0, 0, 3, 3]);
                else     ctx.roundRect(x - bW / 2, base - bH, bW, bH, [3, 3, 0, 0]);
                ctx.fill();
            } else {
                ctx.fillRect(x - bW / 2, isC ? base : base - bH, bW, bH);
            }
            ctx.restore();

            // Borde brillante
            ctx.strokeStyle = col + '99';
            ctx.lineWidth = 1;
            ctx.strokeRect(x - bW / 2, isC ? base : base - bH, bW, bH);

            // Valor encima/debajo
            if (p > 0.6) {
                const alpha = ((p - 0.6) / 0.4).toFixed(2);
                ctx.globalAlpha = parseFloat(alpha);
                ctx.fillStyle = col;
                ctx.font = "7px 'DM Mono',monospace"; ctx.textAlign = 'center';
                const label = (s.cr >= 0 ? '+' : '') + s.cr.toFixed(2);
                ctx.fillText(label, x, isC ? base + bH + 10 : base - bH - 4);
                ctx.fillStyle = 'rgba(255,255,255,0.4)';
                ctx.font = "7px 'DM Mono',monospace";
                ctx.fillText('N°' + s.n, x, H - 5);
                ctx.globalAlpha = 1;
            }
        });

        // Línea de terreno con brillo
        ctx.save();
        ctx.shadowColor = 'rgba(0,229,192,0.6)';
        ctx.shadowBlur = 6;
        ctx.strokeStyle = '#00e5c0';
        ctx.lineWidth = 2;
        ctx.lineJoin = 'round';
        ctx.beginPath();
        secs.forEach((s, i) => {
            const y = base - (s.cr / mxA * barMaxH * p);
            i === 0 ? ctx.moveTo(tX(i), y) : ctx.lineTo(tX(i), y);
        });
        ctx.stroke();
        ctx.restore();

        if (progress < 1) requestAnimationFrame(animateBar);
    }

    requestAnimationFrame(animateBar);
}

// Hook al botón de calcular corte/relleno
function hookCorteRelleno() {
    const btn = document.getElementById('btnCalcCorteRelleno');
    if (!btn || btn._hooked) return;
    btn._hooked = true;
    const orig = btn.onclick;
    btn.addEventListener('click', () => {
        setTimeout(() => {
            const secs = [];
            document.querySelectorAll('#crSecciones .cr-sec-row').forEach(row => {
                const n = parseInt(row.querySelector('.cr-pto')?.value);
                const zp = parseFloat(row.querySelector('.cr-zp')?.value);
                const pt = window.getByN ? window.getByN(n) : null;
                if (pt && !isNaN(zp)) secs.push({ n: pt.n, cr: zp - pt.z });
            });
            if (secs.length >= 2) drawCorteRellenoMejorado(secs);
        }, 100);
    });
}

/* ══════════════════════════════════════════════════════
   3. MEJORAS VISOR 3D PRINCIPAL
   Niebla de profundidad + resplandor en aristas TIN
══════════════════════════════════════════════════════ */
function mejorarVisor3D() {
    const canvas = document.getElementById('visor3D');
    if (!canvas) return;

    // Inyectar estilos extra al canvas
    canvas.style.imageRendering = 'crisp-edges';

    // Post-procesado: viñeta sutil sobre el canvas
    const wrap = canvas.parentElement;
    if (!wrap || document.getElementById('visor-vignette')) return;
    const vig = document.createElement('div');
    vig.id = 'visor-vignette';
    Object.assign(vig.style, {
        position: 'absolute',
        inset: '0',
        pointerEvents: 'none',
        background: 'radial-gradient(ellipse at 50% 50%, transparent 55%, rgba(5,8,15,0.55) 100%)',
        zIndex: '2',
        borderRadius: 'inherit',
    });
    wrap.appendChild(vig);

    // Overlay de opciones 3D extra
    const opts = document.createElement('div');
    opts.id = 'visor-extra-opts';
    opts.innerHTML = `
        <button class="ctrl-btn" id="btnNiebla" title="Niebla de profundidad">🌫 Niebla</button>
        <button class="ctrl-btn" id="btnBrillo" title="Brillo en aristas">✨ Brillo</button>
        <button class="ctrl-btn" id="btnNoche" title="Modo nocturno">🌙 Noche</button>
    `;
    Object.assign(opts.style, {
        position: 'absolute',
        top: '10px',
        left: '12px',
        display: 'flex',
        gap: '4px',
        zIndex: '5',
    });
    wrap.appendChild(opts);

    // Variables de efectos
    window.VISOR_FX = { niebla: false, brillo: false, noche: false };

    document.getElementById('btnNiebla')?.addEventListener('click', function() {
        window.VISOR_FX.niebla = !window.VISOR_FX.niebla;
        this.classList.toggle('active', window.VISOR_FX.niebla);
        if (typeof draw === 'function') draw();
    });

    document.getElementById('btnBrillo')?.addEventListener('click', function() {
        window.VISOR_FX.brillo = !window.VISOR_FX.brillo;
        this.classList.toggle('active', window.VISOR_FX.brillo);
        if (typeof draw === 'function') draw();
    });

    document.getElementById('btnNoche')?.addEventListener('click', function() {
        window.VISOR_FX.noche = !window.VISOR_FX.noche;
        this.classList.toggle('active', window.VISOR_FX.noche);
        document.documentElement.style.setProperty('--bg', window.VISOR_FX.noche ? '#010306' : '#05080f');
        if (typeof draw === 'function') draw();
    });

    // Interceptar draw() para añadir efectos post-proceso
    const _origDraw = window.draw;
    if (typeof _origDraw === 'function') {
        window.draw = function() {
            _origDraw.apply(this, arguments);
            applyPostFX();
        };
    }

    function applyPostFX() {
        const ctx = canvas.getContext('2d');
        const W = canvas.width, H = canvas.height;

        // Efecto niebla de profundidad (modo 3D)
        if (window.VISOR_FX?.niebla && window.MODO === '3D') {
            const fog = ctx.createRadialGradient(W/2, H/2, H*0.2, W/2, H/2, H*0.8);
            fog.addColorStop(0, 'transparent');
            fog.addColorStop(1, 'rgba(5,8,15,0.45)');
            ctx.fillStyle = fog;
            ctx.fillRect(0, 0, W, H);
        }

        // Brillo en los bordes del canvas
        if (window.VISOR_FX?.brillo) {
            ctx.save();
            ctx.strokeStyle = 'rgba(0,229,192,0.15)';
            ctx.lineWidth = 3;
            ctx.strokeRect(2, 2, W - 4, H - 4);
            ctx.restore();
        }
    }
}

/* ══════════════════════════════════════════════════════
   4. MEJORA DEL MUÑECO (TOPO CHARACTER)
   Fondo con partículas + estética dark sci-fi del index
══════════════════════════════════════════════════════ */
function mejorarTopoCanvas() {
    const el = document.getElementById('topoCanvas');
    if (!el) return;

    // Mejorar el fondo del canvas wrapper
    const wrap = el.parentElement;
    if (!wrap) return;

    Object.assign(wrap.style, {
        background: 'linear-gradient(135deg, #070d1a 0%, #0a0f1c 50%, #070d1a 100%)',
        borderRadius: '16px',
        border: '1px solid rgba(0,229,192,0.1)',
        boxShadow: '0 0 30px rgba(0,229,192,0.05), inset 0 0 20px rgba(0,0,0,0.3)',
        position: 'relative',
        overflow: 'hidden',
    });

    // Partículas de fondo SVG animadas
    const particlesBg = document.createElement('div');
    particlesBg.id = 'topo-particles-bg';
    particlesBg.innerHTML = `
        <svg width="100%" height="100%" viewBox="0 0 220 260" xmlns="http://www.w3.org/2000/svg" style="position:absolute;inset:0;pointer-events:none;opacity:0.4;">
            <defs>
                <radialGradient id="glow1" cx="50%" cy="80%">
                    <stop offset="0%" stop-color="#00e5c0" stop-opacity="0.15"/>
                    <stop offset="100%" stop-color="#00e5c0" stop-opacity="0"/>
                </radialGradient>
            </defs>
            <!-- Suelo con brillo -->
            <ellipse cx="110" cy="240" rx="80" ry="12" fill="url(#glow1)"/>
            <!-- Líneas de grid perspectiva -->
            <line x1="10" y1="260" x2="110" y2="200" stroke="rgba(0,229,192,0.06)" stroke-width="0.5"/>
            <line x1="210" y1="260" x2="110" y2="200" stroke="rgba(0,229,192,0.06)" stroke-width="0.5"/>
            <line x1="10" y1="260" x2="210" y2="260" stroke="rgba(0,229,192,0.06)" stroke-width="0.5"/>
            <line x1="40" y1="260" x2="110" y2="200" stroke="rgba(0,229,192,0.04)" stroke-width="0.5"/>
            <line x1="180" y1="260" x2="110" y2="200" stroke="rgba(0,229,192,0.04)" stroke-width="0.5"/>
            <!-- Partículas flotantes -->
            <circle cx="30" cy="80" r="1.5" fill="#00e5c0" opacity="0.3">
                <animate attributeName="cy" values="80;60;80" dur="4s" repeatCount="indefinite"/>
                <animate attributeName="opacity" values="0.3;0.6;0.3" dur="4s" repeatCount="indefinite"/>
            </circle>
            <circle cx="190" cy="120" r="1" fill="#00e5c0" opacity="0.2">
                <animate attributeName="cy" values="120;100;120" dur="5s" repeatCount="indefinite"/>
                <animate attributeName="opacity" values="0.2;0.5;0.2" dur="5s" repeatCount="indefinite"/>
            </circle>
            <circle cx="160" cy="50" r="2" fill="#7c3aed" opacity="0.2">
                <animate attributeName="cy" values="50;30;50" dur="6s" repeatCount="indefinite"/>
            </circle>
            <circle cx="50" cy="180" r="1.5" fill="#00e5c0" opacity="0.15">
                <animate attributeName="cy" values="180;160;180" dur="3.5s" repeatCount="indefinite"/>
            </circle>
            <!-- Estrellas pequeñas -->
            <text x="15" y="40" font-size="8" fill="rgba(0,229,192,0.2)" font-family="monospace">✦</text>
            <text x="200" y="70" font-size="6" fill="rgba(0,229,192,0.15)" font-family="monospace">⊹</text>
            <text x="170" y="170" font-size="7" fill="rgba(124,58,237,0.2)" font-family="monospace">◦</text>
        </svg>
    `;
    Object.assign(particlesBg.style, {
        position: 'absolute',
        inset: '0',
        pointerEvents: 'none',
        zIndex: '0',
    });

    wrap.style.position = 'relative';
    wrap.insertBefore(particlesBg, wrap.firstChild);
    el.style.position = 'relative';
    el.style.zIndex = '1';

    // Label de nivel mejorado
    const hint = wrap.nextElementSibling;
    if (hint && hint.classList.contains('topo-canvas-hint')) {
        Object.assign(hint.style, {
            color: 'rgba(0,229,192,0.5)',
            fontSize: '10px',
            letterSpacing: '1px',
            textAlign: 'center',
            fontFamily: "'DM Mono', monospace",
        });
        hint.textContent = '⟳ clic para interactuar';
    }

    // Panel de stats del personaje debajo del canvas
    const statsPanel = document.createElement('div');
    statsPanel.id = 'topo-quick-stats';
    statsPanel.style.cssText = `
        display: flex; gap: 8px; margin-top: 10px; justify-content: center;
    `;
    statsPanel.innerHTML = `
        <div style="text-align:center; background: rgba(0,229,192,0.06); border: 1px solid rgba(0,229,192,0.12); border-radius: 8px; padding: 6px 12px;">
            <div style="font: 700 14px 'DM Mono',monospace; color: #00e5c0;" id="topoStatXP">—</div>
            <div style="font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">XP Total</div>
        </div>
        <div style="text-align:center; background: rgba(124,58,237,0.06); border: 1px solid rgba(124,58,237,0.12); border-radius: 8px; padding: 6px 12px;">
            <div style="font: 700 14px 'DM Mono',monospace; color: #a78bfa;" id="topoStatDias">—</div>
            <div style="font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">Días activo</div>
        </div>
    `;
    wrap.parentElement?.appendChild(statsPanel);

    // Llenar stats desde variables PHP ya inyectadas
    setTimeout(() => {
        const xpEl = document.getElementById('topoStatXP');
        const diasEl = document.getElementById('topoStatDias');
        const xpNum = document.getElementById('topoXpNum');
        if (xpEl && xpNum) xpEl.textContent = xpNum.textContent || '0 XP';
        if (diasEl) {
            // Leer de dataset si existe
            const diasReg = document.querySelector('[data-dias]')?.dataset?.dias || '—';
            diasEl.textContent = diasReg !== '—' ? diasReg + ' días' : diasEl.textContent;
        }
    }, 800);
}

/* ══════════════════════════════════════════════════════
   5. ESTILOS EXTRA INYECTADOS
══════════════════════════════════════════════════════ */
function inyectarEstilos() {
    const s = document.createElement('style');
    s.textContent = `
        /* Mini canvas mejorados */
        .mini-canvas {
            border-radius: 8px !important;
            border: 1px solid rgba(0,229,192,0.08) !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.4) !important;
        }

        /* Opciones extra del visor */
        #visor-extra-opts .ctrl-btn {
            font-size: 10px !important;
            padding: 4px 10px !important;
            backdrop-filter: blur(8px) !important;
            background: rgba(5,8,15,0.8) !important;
        }

        /* Canvas topo mejorado */
        #topoCanvas {
            border-radius: 12px !important;
        }

        #topo-quick-stats {
            animation: fadeIn 0.5s ease 0.8s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Brillo en canvas cuando está activo */
        #topoCanvas:hover {
            box-shadow: 0 0 20px rgba(0,229,192,0.1) !important;
            transition: box-shadow 0.3s ease;
        }

        /* Estilos de la curva de masa */
        #miniCanvasMasa {
            border-radius: 8px !important;
            border: 1px solid rgba(0,229,192,0.08) !important;
        }
    `;
    document.head.appendChild(s);
}

/* ── Init ── */
function init() {
    inyectarEstilos();
    mejorarVisor3D();
    mejorarTopoCanvas();
    hookCorteRelleno();

    // Re-dibujar curva de masa con mejor estética si ya existe
    const cvMasa = document.getElementById('miniCanvasMasa');
    if (cvMasa && cvMasa.width > 0) {
        const ctx = cvMasa.getContext('2d');
        const bg = ctx.createLinearGradient(0, 0, 0, cvMasa.height);
        bg.addColorStop(0, '#070d1a');
        bg.addColorStop(1, '#050810');
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

})();
