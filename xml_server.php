/**
 * FYLCAD — Animaciones del Dashboard
 * Agregar justo antes de </body> en dashboard.php
 */
(function () {
    'use strict';

    // ── 1. Fade + slide escalonado en las cards ──────────────
    function animarCards() {
        const cards = document.querySelectorAll('.bento-card');
        cards.forEach((card, i) => {
            card.style.opacity    = '0';
            card.style.transform  = 'translateY(22px)';
            card.style.transition = `opacity 0.45s ease ${i * 70}ms, transform 0.45s ease ${i * 70}ms`;
            // Forzar reflow
            void card.offsetWidth;
            requestAnimationFrame(() => {
                card.style.opacity   = '1';
                card.style.transform = 'translateY(0)';
            });
        });
    }

    // ── 2. Contador animado en los números de stats ──────────
    function animarContadores() {
        const nums = document.querySelectorAll('.stat-num');
        nums.forEach(el => {
            const raw    = el.textContent.replace(/[^0-9.]/g, '');
            const target = parseFloat(raw);
            if (isNaN(target) || target === 0) return;

            const isFloat   = raw.includes('.');
            const decimales = isFloat ? (raw.split('.')[1] || '').length : 0;
            const duracion  = Math.min(1200, 400 + target * 2);
            const inicio    = performance.now();

            function tick(now) {
                const elapsed  = now - inicio;
                const progress = Math.min(elapsed / duracion, 1);
                // easeOutExpo
                const ease     = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                const valor    = target * ease;

                el.textContent = isFloat
                    ? valor.toFixed(decimales)
                    : Math.floor(valor).toLocaleString('es-CO');

                if (progress < 1) requestAnimationFrame(tick);
                else el.textContent = isFloat
                    ? target.toFixed(decimales)
                    : target.toLocaleString('es-CO');
            }
            requestAnimationFrame(tick);
        });
    }

    // ── 3. Highlight de fila activa en actividad reciente ────
    function animarActividad() {
        const filas = document.querySelectorAll('.act-row, .activity-item, [class*="act-"]');
        filas.forEach((fila, i) => {
            fila.style.opacity   = '0';
            fila.style.transform = 'translateX(-10px)';
            setTimeout(() => {
                fila.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
                fila.style.opacity    = '1';
                fila.style.transform  = 'translateX(0)';
            }, 500 + i * 80);
        });
    }

    // ── 4. Barra de progreso animada si existe ───────────────
    function animarBarras() {
        const barras = document.querySelectorAll('[class*="progress"], [class*="bar-fill"], [class*="prog-"]');
        barras.forEach(barra => {
            const ancho = barra.style.width || barra.getAttribute('data-width');
            if (!ancho) return;
            barra.style.width      = '0%';
            barra.style.transition = 'width 0.8s cubic-bezier(.22,1,.36,1) 0.3s';
            requestAnimationFrame(() => {
                setTimeout(() => barra.style.width = ancho, 100);
            });
        });
    }

    // ── Init ─────────────────────────────────────────────────
    function init() {
        animarCards();
        setTimeout(animarContadores, 300);
        animarActividad();
        animarBarras();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
