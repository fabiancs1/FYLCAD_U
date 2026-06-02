/**
 * FYLCAD — Chatbot sin API (mejorado)
 * Archivo: js/chatbot.js
 */

(function () {
    'use strict';

    const CONFIG = {
        endpoint: '/FYLCAD/chatbot.php',
        sugerencias: [
            '¿Qué es FYLCAD?',
            '¿Cómo cargo un CSV?',
            '¿Qué planes hay?',
            '¿Cómo genero una cotización?',
            '¿Qué es la triangulación Delaunay?',
            '¿Cómo contacto proveedores?',
        ],
    };

    let abierto  = false;
    let cargando = false;
    let mensajesCount = 0;

    function crearWidget() {
        const w = document.createElement('div');
        w.id = 'fylbot-widget';
        w.innerHTML = `
            <div id="fylbot-chat" aria-live="polite">
                <div id="fylbot-header">
                    <div id="fylbot-avatar">📐</div>
                    <div id="fylbot-info">
                        <h4>Asistente FYLCAD</h4>
                        <span id="fylbot-status">
                            <span class="fylbot-dot"></span> En línea
                        </span>
                    </div>
                    <button id="fylbot-close" aria-label="Cerrar chat">✕</button>
                </div>
                <div id="fylbot-mensajes"></div>
                <div id="fylbot-sugerencias"></div>
                <div id="fylbot-input-area">
                    <textarea id="fylbot-input" rows="1" placeholder="¿En qué te puedo ayudar?" aria-label="Mensaje"></textarea>
                    <button id="fylbot-enviar" aria-label="Enviar">➤</button>
                </div>
            </div>
            <button id="fylbot-btn" title="Asistente FYLCAD" aria-label="Abrir asistente">
                <span id="fylbot-btn-icon">📐</span>
                <span id="fylbot-badge" style="display:none">!</span>
            </button>
        `;
        document.body.appendChild(w);
    }

    function toggleChat() {
        abierto = !abierto;
        const chat = document.getElementById('fylbot-chat');
        const btn  = document.getElementById('fylbot-btn');
        const badge = document.getElementById('fylbot-badge');

        if (abierto) {
            chat.classList.remove('fylbot-closing');
            chat.style.display = 'flex';
            // Forzar reflow para que la animación arranque
            void chat.offsetWidth;
            chat.classList.add('fylbot-open');
            badge.style.display = 'none';
            btn.classList.add('active');

            const msgs = document.getElementById('fylbot-mensajes');
            if (msgs.children.length === 0) {
                agregarMensaje('👋 ¡Hola! Soy el asistente de **FYLCAD**.\n\nPuedo ayudarte con topografía, uso de la plataforma, cotizaciones y planes.\n\n¿Sobre qué quieres saber? 📐', 'bot');
                setTimeout(mostrarSugerencias, 400);
            }
            setTimeout(() => document.getElementById('fylbot-input').focus(), 300);
        } else {
            chat.classList.remove('fylbot-open');
            chat.classList.add('fylbot-closing');
            btn.classList.remove('active');
            setTimeout(() => {
                chat.style.display = 'none';
                chat.classList.remove('fylbot-closing');
            }, 250);
        }
    }

    function mostrarSugerencias() {
        const cont = document.getElementById('fylbot-sugerencias');
        cont.innerHTML = '';
        CONFIG.sugerencias.forEach((texto, i) => {
            const btn = document.createElement('button');
            btn.className   = 'fylbot-sugerencia';
            btn.textContent = texto;
            btn.style.animationDelay = `${i * 60}ms`;
            btn.classList.add('fylbot-sugerencia-anim');
            btn.onclick = () => {
                cont.innerHTML = '';
                enviar(texto);
            };
            cont.appendChild(btn);
        });
    }

    function agregarMensaje(texto, tipo, categoria) {
        const cont = document.getElementById('fylbot-mensajes');
        const div  = document.createElement('div');
        div.className = `fylbot-msg ${tipo} fylbot-msg-anim`;
        div.innerHTML = texto
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>')
            .replace(/• /g, '<span class="fylbot-bullet">•</span> ')
            .replace(/```[\s\S]*?```/g, m =>
                `<code class="fylbot-code">${m.replace(/```/g, '')}</code>`
            );
        cont.appendChild(div);

        if (categoria && categoria !== 'Sin resultado') {
            const cat = document.createElement('div');
            cat.className   = 'fylbot-categoria';
            cat.textContent = '📁 ' + categoria;
            cont.appendChild(cat);
        }

        mensajesCount++;
        cont.scrollTop = cont.scrollHeight;

        // Mostrar badge si el chat está cerrado
        if (!abierto && tipo === 'bot') {
            document.getElementById('fylbot-badge').style.display = 'flex';
        }
    }

    function mostrarTyping() {
        const cont = document.getElementById('fylbot-mensajes');
        const div  = document.createElement('div');
        div.className = 'fylbot-typing';
        div.id        = 'fylbot-typing';
        div.innerHTML = '<span></span><span></span><span></span>';
        cont.appendChild(div);
        cont.scrollTop = cont.scrollHeight;
    }

    function ocultarTyping() {
        const t = document.getElementById('fylbot-typing');
        if (t) t.remove();
    }

    async function enviar(texto) {
        texto = (texto || document.getElementById('fylbot-input').value).trim();
        if (!texto || cargando) return;

        document.getElementById('fylbot-input').value = '';
        document.getElementById('fylbot-input').style.height = 'auto';
        agregarMensaje(texto, 'usuario');

        cargando = true;
        document.getElementById('fylbot-enviar').disabled = true;
        document.getElementById('fylbot-enviar').classList.add('sending');

        // Delay realista antes de mostrar typing
        await new Promise(r => setTimeout(r, 200));
        mostrarTyping();

        // Tiempo mínimo de "pensando" para que se sienta natural
        const [resp] = await Promise.all([
            fetch(CONFIG.endpoint, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ mensaje: texto }),
            }).then(r => r.json()).catch(() => ({
                respuesta: '❌ No pude conectar con el servidor. Intenta de nuevo.',
                categoria: ''
            })),
            new Promise(r => setTimeout(r, 600))
        ]);

        ocultarTyping();
        agregarMensaje(resp.respuesta, 'bot', resp.categoria);

        // Mostrar sugerencias de nuevo si no hubo resultado claro
        if (resp.categoria === 'Sin resultado') {
            setTimeout(mostrarSugerencias, 300);
        }

        cargando = false;
        document.getElementById('fylbot-enviar').disabled = false;
        document.getElementById('fylbot-enviar').classList.remove('sending');
        document.getElementById('fylbot-input').focus();
    }

    function init() {
        crearWidget();
        document.getElementById('fylbot-btn').addEventListener('click', toggleChat);
        document.getElementById('fylbot-close').addEventListener('click', toggleChat);
        document.getElementById('fylbot-enviar').addEventListener('click', () => enviar());
        document.getElementById('fylbot-input').addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); enviar(); }
        });
        document.getElementById('fylbot-input').addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // Mostrar badge de bienvenida después de 3 segundos
        setTimeout(() => {
            if (!abierto) {
                document.getElementById('fylbot-badge').style.display = 'flex';
            }
        }, 3000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
