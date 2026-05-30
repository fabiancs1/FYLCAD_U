/**
 * FYLCAD — Animación de carga del terreno
 * Archivo: js/visor_animacion.js
 * Se carga después de proyecto.js
 */
(function () {
    'use strict';

    // ── Esperar a que proyecto.js esté listo ──────────────────
    function waitForVisor(cb, intentos = 0) {
        if (typeof draw === 'function' && typeof PTS !== 'undefined') {
            cb();
        } else if (intentos < 50) {
            setTimeout(() => waitForVisor(cb, intentos + 1), 100);
        }
    }

    // ── Overlay de progreso ───────────────────────────────────
    function crearOverlay() {
        if (document.getElementById('fylcad-overlay')) return;

        const o = document.createElement('div');
        o.id = 'fylcad-overlay';
        o.innerHTML = `
            <div id="fylcad-overlay-inner">
                <div id="fylcad-spinner"></div>
                <div id="fylcad-overlay-txt">Procesando terreno…</div>
                <div id="fylcad-progress-bar">
                    <div id="fylcad-progress-fill"></div>
                </div>
                <div id="fylcad-overlay-sub"></div>
            </div>
        `;

        // Posicionarlo sobre el canvas
        const canvas = document.getElementById('visor3D');
        if (canvas) {
            const wrap = canvas.parentElement;
            wrap.style.position = 'relative';
            wrap.appendChild(o);
        } else {
            document.body.appendChild(o);
        }

        // Estilos inline para que funcionen sin CSS externo
        Object.assign(o.style, {
            position:       'absolute',
            inset:          '0',
            background:     'rgba(6, 10, 18, 0.88)',
            backdropFilter: 'blur(4px)',
            display:        'flex',
            alignItems:     'center',
            justifyContent: 'center',
            zIndex:         '50',
            borderRadius:   'inherit',
            opacity:        '0',
            transition:     'opacity 0.3s ease',
        });

        const inner = document.getElementById('fylcad-overlay-inner');
        Object.assign(inner.style, {
            display:       'flex',
            flexDirection: 'column',
            alignItems:    'center',
            gap:           '14px',
            textAlign:     'center',
        });

        const spinner = document.getElementById('fylcad-spinner');
        Object.assign(spinner.style, {
            width:       '52px',
            height:      '52px',
            border:      '3px solid rgba(0,229,192,0.15)',
            borderTop:   '3px solid #00e5c0',
            borderRadius:'50%',
            animation:   'fylcadSpin 0.9s linear infinite',
        });

        const txt = document.getElementById('fylcad-overlay-txt');
        Object.assign(txt.style, {
            color:      '#00e5c0',
            fontSize:   '15px',
            fontWeight: '600',
            fontFamily: 'inherit',
        });

        const bar = document.getElementById('fylcad-progress-bar');
        Object.assign(bar.style, {
            width:        '200px',
            height:       '4px',
            background:   'rgba(0,229,192,0.15)',
            borderRadius: '4px',
            overflow:     'hidden',
        });

        const fill = document.getElementById('fylcad-progress-fill');
        Object.assign(fill.style, {
            height:           '100%',
            width:            '0%',
            background:       'linear-gradient(90deg, #00e5c0, #7c3aed)',
            borderRadius:     '4px',
            transition:       'width 0.3s ease',
        });

        const sub = document.getElementById('fylcad-overlay-sub');
        Object.assign(sub.style, {
            color:     'rgba(255,255,255,0.4)',
            fontSize:  '12px',
            minHeight: '16px',
        });

        // Inyectar keyframe del spinner
        if (!document.getElementById('fylcad-keyframes')) {
            const style = document.createElement('style');
            style.id = 'fylcad-keyframes';
            style.textContent = `
                @keyframes fylcadSpin {
                    to { transform: rotate(360deg); }
                }
                @keyframes fylcadFadeIn {
                    from { opacity: 0; transform: translateY(8px); }
                    to   { opacity: 1; transform: translateY(0); }
                }
            `;
            document.head.appendChild(style);
        }
    }

    function mostrarOverlay(texto, sub, progreso) {
        crearOverlay();
        const o    = document.getElementById('fylcad-overlay');
        const txt  = document.getElementById('fylcad-overlay-txt');
        const fill = document.getElementById('fylcad-progress-fill');
        const subEl= document.getElementById('fylcad-overlay-sub');

        if (txt)  txt.textContent  = texto || 'Procesando…';
        if (subEl) subEl.textContent = sub || '';
        if (fill && progreso !== undefined) fill.style.width = progreso + '%';

        if (o) {
            o.style.display = 'flex';
            requestAnimationFrame(() => { o.style.opacity = '1'; });
        }
    }

    function ocultarOverlay() {
        const o = document.getElementById('fylcad-overlay');
        if (!o) return;
        o.style.opacity = '0';
        setTimeout(() => { o.style.display = 'none'; }, 320);
    }

    // ── Animación de puntos apareciendo ──────────────────────
    function animarPuntos(ptsTotal, drawFn) {
        const DURACION = Math.min(1800, 600 + ptsTotal * 1.2);
        const inicio   = performance.now();

        // Guardar referencia a los puntos reales
        const ptsCopy = [...window.PTS];

        function tick(now) {
            const elapsed  = now - inicio;
            const progress = Math.min(elapsed / DURACION, 1);

            // easeOutCubic
            const ease = 1 - Math.pow(1 - progress, 3);
            const n    = Math.floor(ease * ptsCopy.length);

            // Mostrar solo n puntos ordenados por Z (de menor a mayor)
            window.PTS = ptsCopy.slice(0, n);

            if (window.PTS.length > 0) drawFn();

            // Actualizar barra
            const fill = document.getElementById('fylcad-progress-fill');
            if (fill) fill.style.width = Math.floor(ease * 100) + '%';

            const sub = document.getElementById('fylcad-overlay-sub');
            if (sub) sub.textContent = `${n} / ${ptsCopy.length} puntos`;

            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                // Restaurar todos los puntos y dibujar final
                window.PTS = ptsCopy;
                drawFn();
                setTimeout(ocultarOverlay, 200);
            }
        }

        requestAnimationFrame(tick);
    }

    // ── Interceptar el evento de carga del CSV ────────────────
    function interceptarCarga() {
        const fileInp = document.getElementById('fileInp') ||
                        document.querySelector('input[type="file"]');
        const dropZone = document.getElementById('dropZone') ||
                         document.querySelector('.drop-zone, .dropzone, [class*="drop"]');

        if (!fileInp) return;

        // Monkey-patch: interceptar init() global
        const initOriginal = window.init;
        if (typeof initOriginal !== 'function') return;

        window.init = function () {
            // Mostrar overlay antes de que empiece
            mostrarOverlay('Cargando puntos…', '', 0);

            // Paso 1: Calculando
            setTimeout(() => {
                mostrarOverlay('Calculando triangulación TIN…', '', 20);
            }, 100);

            // Correr el init original
            initOriginal.apply(this, arguments);

            // Paso 2: después del TIN (el setTimeout de 20ms en init)
            setTimeout(() => {
                mostrarOverlay('Generando curvas de nivel…', '', 55);
            }, 150);

            setTimeout(() => {
                mostrarOverlay('Construyendo modelo 3D…', '', 80);

                // Ordenar puntos por Z para que aparezcan de abajo arriba
                if (window.PTS && window.PTS.length > 0) {
                    const ptsCopy = [...window.PTS].sort((a, b) => a.z - b.z);
                    window.PTS = ptsCopy;

                    setTimeout(() => {
                        mostrarOverlay('Renderizando terreno…', '', 90);
                        animarPuntos(ptsCopy.length, () => {
                            if (typeof draw === 'function') draw();
                        });
                    }, 100);
                } else {
                    ocultarOverlay();
                }
            }, 300);
        };
    }

    // ── Init ─────────────────────────────────────────────────
    waitForVisor(interceptarCarga);

})();
