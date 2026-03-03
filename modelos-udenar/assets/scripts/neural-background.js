// assets/scripts/neural-background.js
(function() {
    'use strict';

    // --- CONFIGURACIÓN ---
    const SETTINGS = {
        density: 0.00005,
        maxParticles: 80,
        viscosity: 0.92,
        spring: 0.004,
        connectionDist: 150,
        mouseRadius: 160,
        color: '0, 242, 255', // RGB del color cyan
    };

    let canvas, ctx, container;
    let particles = [];
    let animationFrame;
    let mouse = { x: -1000, y: -1000, active: false };
    let time = 0;

    function init() {
        container = document.querySelector('.neural-background');
        if (!container) return;

        canvas = document.createElement('canvas');
        canvas.className = 'neural-canvas';
        container.appendChild(canvas);

        ctx = canvas.getContext('2d', { alpha: true });
        if (!ctx) return;

        // Observador de cambio de tamaño
        const resizeObserver = new ResizeObserver(entries => {
            for (const entry of entries) {
                const { width, height } = entry.contentRect;
                const dpr = window.devicePixelRatio || 1;
                canvas.width = width * dpr;
                canvas.height = height * dpr;
                ctx.scale(dpr, dpr);
                syncParticles(width, height);
            }
        });
        resizeObserver.observe(container);

        // Eventos de mouse (solo cuando está dentro del contenedor)
        window.addEventListener('mousemove', handleMouseMove);
        window.addEventListener('mouseleave', () => { mouse.active = false; });

        // Iniciar animación
        render();

        // Limpieza al cerrar (opcional, pero buena práctica)
        window.addEventListener('beforeunload', () => {
            if (animationFrame) cancelAnimationFrame(animationFrame);
            resizeObserver.disconnect();
        });
    }

    function handleMouseMove(e) {
        const rect = canvas.getBoundingClientRect();
        mouse = {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top,
            active: true
        };
    }

    function syncParticles(width, height) {
        const area = width * height;
        const targetCount = Math.min(
            Math.floor(area * SETTINGS.density),
            SETTINGS.maxParticles
        );

        if (Math.abs(particles.length - targetCount) > 10 || particles.length === 0) {
            const newParticles = [];
            for (let i = 0; i < targetCount; i++) {
                const x = Math.random() * width;
                const y = Math.random() * height;
                newParticles.push({
                    x, y,
                    originX: x,
                    originY: y,
                    vx: 0,
                    vy: 0,
                    size: Math.random() * 1.5 + 0.5,
                    phase: Math.random() * Math.PI * 2,
                });
            }
            particles = newParticles;
        }
    }

    function render() {
        time += 0.015;
        const width = canvas.width / (window.devicePixelRatio || 1);
        const height = canvas.height / (window.devicePixelRatio || 1);

        ctx.clearRect(0, 0, width, height);

        const pArr = particles;

        // Dibujar conexiones
        ctx.beginPath();
        ctx.lineWidth = 0.6;
        for (let i = 0; i < pArr.length; i++) {
            for (let j = i + 1; j < pArr.length; j++) {
                const dx = pArr[i].x - pArr[j].x;
                const dy = pArr[i].y - pArr[j].y;
                const distSq = dx * dx + dy * dy;
                const limitSq = SETTINGS.connectionDist * SETTINGS.connectionDist;

                if (distSq < limitSq) {
                    const opacity = (1 - distSq / limitSq) * 0.15;
                    ctx.strokeStyle = `rgba(${SETTINGS.color}, ${opacity})`;
                    ctx.moveTo(pArr[i].x, pArr[i].y);
                    ctx.lineTo(pArr[j].x, pArr[j].y);
                }
            }
        }
        ctx.stroke();

        // Actualizar y dibujar partículas
        pArr.forEach(p => {
            // Física de retorno al origen
            p.vx += (p.originX - p.x) * SETTINGS.spring;
            p.vy += (p.originY - p.y) * SETTINGS.spring;

            // Interacción con el mouse
            if (mouse.active) {
                const dxM = p.x - mouse.x;
                const dyM = p.y - mouse.y;
                const distM = Math.sqrt(dxM * dxM + dyM * dyM);
                if (distM < SETTINGS.mouseRadius && distM > 0.01) {
                    const force = (1 - distM / SETTINGS.mouseRadius) * 0.8;
                    p.vx += (dxM / distM) * force;
                    p.vy += (dyM / distM) * force;
                }
            }

            p.vx *= SETTINGS.viscosity;
            p.vy *= SETTINGS.viscosity;
            p.x += p.vx;
            p.y += p.vy;

            // Pulsación
            const pulse = Math.sin(time + p.phase) * 0.3 + 0.7;

            // Nodo principal
            ctx.fillStyle = `rgba(${SETTINGS.color}, ${0.8 * pulse})`;
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
            ctx.fill();

            // Glow
            if (pulse > 0.8) {
                ctx.fillStyle = `rgba(${SETTINGS.color}, ${0.1 * pulse})`;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size * 5, 0, Math.PI * 2);
                ctx.fill();
            }
        });

        animationFrame = requestAnimationFrame(render);
    }

    // Iniciar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();