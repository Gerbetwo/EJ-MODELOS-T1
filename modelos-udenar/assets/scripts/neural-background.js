// assets/scripts/neural-background.js
(function () {
  'use strict';

  const SETTINGS = {
    density: 0.00004, // Un poco menos denso para no sobrecargar
    maxParticles: 70,
    viscosity: 0.94,
    spring: 0.005,
    connectionDist: 160,
    mouseRadius: 180,
    // Sincronizado con _variables.css
    colorPrimary: { r: 88, g: 101, b: 242 },     // Púrpura Nebula
    colorSecondary: { r: 0, g: 175, b: 244 },    // Cian Eléctrico
  };

  let canvas, ctx, container;
  let particles = [];
  let animationFrame;
  let mouse = { x: -1000, y: -1000, active: false };
  let time = 0;

  function init() {
    container = document.querySelector('.neural-background');
    if (!container) return;

    // Limpiamos el contenedor por si acaso hay un canvas previo
    container.innerHTML = '';
    canvas = document.createElement('canvas');
    canvas.style.position = 'absolute'; // Importante para evitar que empuje el layout
    canvas.style.top = '0';
    canvas.style.left = '0';
    container.appendChild(canvas);

    ctx = canvas.getContext('2d', { alpha: true });

    const updateSize = () => {
      // Usamos el tamaño de la ventana para evitar el bug de crecimiento infinito
      const width = window.innerWidth;
      const height = window.innerHeight;
      const dpr = window.devicePixelRatio || 1;

      canvas.width = width * dpr;
      canvas.height = height * dpr;
      canvas.style.width = width + 'px';
      canvas.style.height = height + 'px';

      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      syncParticles(width, height);
    };

    window.addEventListener('resize', updateSize);
    updateSize();

    window.addEventListener('mousemove', handleMouseMove);
    window.addEventListener('mouseleave', () => { mouse.active = false; });

    render();
  }

  function handleMouseMove(e) {
    mouse = { x: e.clientX, y: e.clientY, active: true };
  }

  function syncParticles(width, height) {
    const targetCount = Math.min(Math.floor((width * height) * SETTINGS.density), SETTINGS.maxParticles);

    // Si la diferencia es mucha, regeneramos
    if (Math.abs(particles.length - targetCount) > 15 || particles.length === 0) {
      particles = [];
      for (let i = 0; i < targetCount; i++) {
        const x = Math.random() * width;
        const y = Math.random() * height;
        particles.push({
          x, y, originX: x, originY: y,
          vx: 0, vy: 0,
          size: Math.random() * 2 + 1,
          phase: Math.random() * Math.PI * 2
        });
      }
    }
  }

  function render() {
    time += 0.01;
    const width = window.innerWidth;
    const height = window.innerHeight;

    ctx.clearRect(0, 0, width, height);

    const { r: r1, g: g1, b: b1 } = SETTINGS.colorPrimary;
    const { r: r2, g: g2, b: b2 } = SETTINGS.colorSecondary;

    // 1. Dibujar conexiones (Dorado elegante)
    ctx.beginPath();
    for (let i = 0; i < particles.length; i++) {
      for (let j = i + 1; j < particles.length; j++) {
        const dx = particles[i].x - particles[j].x;
        const dy = particles[i].y - particles[j].y;
        const distSq = dx * dx + dy * dy;
        const limitSq = SETTINGS.connectionDist * SETTINGS.connectionDist;

        if (distSq < limitSq) {
          const opacity = (1 - distSq / limitSq) * 0.15;
          ctx.strokeStyle = `rgba(${r2}, ${g2}, ${b2}, ${opacity})`;
          ctx.lineWidth = 0.5;
          ctx.moveTo(particles[i].x, particles[i].y);
          ctx.lineTo(particles[j].x, particles[j].y);
        }
      }
    }
    ctx.stroke();

    // 2. Actualizar Partículas
    particles.forEach(p => {
      p.vx += (p.originX - p.x) * SETTINGS.spring;
      p.vy += (p.originY - p.y) * SETTINGS.spring;

      if (mouse.active) {
        const dxM = p.x - mouse.x;
        const dyM = p.y - mouse.y;
        const distM = Math.sqrt(dxM * dxM + dyM * dyM);
        if (distM < SETTINGS.mouseRadius) {
          const force = (1 - distM / SETTINGS.mouseRadius) * 1.2;
          p.vx += (dxM / distM) * force;
          p.vy += (dyM / distM) * force;
        }
      }

      p.vx *= SETTINGS.viscosity;
      p.vy *= SETTINGS.viscosity;
      p.x += p.vx;
      p.y += p.vy;

      const pulse = Math.sin(time + p.phase) * 0.4 + 0.6;

      // Brillo dorado exterior (solo si está "activo")
      if (pulse > 0.8) {
        ctx.shadowBlur = 10 * pulse;
        ctx.shadowColor = `rgba(${r2}, ${g2}, ${b2}, 0.3)`;
      } else {
        ctx.shadowBlur = 0;
      }

      // El núcleo es Rojo
      ctx.fillStyle = `rgba(${r1}, ${g1}, ${b1}, ${0.8 * pulse})`;
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
      ctx.fill();
    });

    animationFrame = requestAnimationFrame(render);
  }

  init();
})();