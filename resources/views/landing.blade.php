<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CF Proxy Manager — Automatiza Cloudflare</title>
  <!-- Favicon -->
  <link rel="icon" href="{{ asset('images/logo-v2.png') }}" type="image/x-icon">
  <link rel="shortcut icon" href="{{ asset('images/logo-v2.ico') }}" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --bg: #060910; --bg2: #0a0f1a; --bg3: #0d1520;
      --border: #1a2a3a; --border2: #1e3a5f;
      --text: #e2e8f0; --muted: #4a6285; --muted2: #94a3b8;
      --cyan: #00d4ff; --orange: #ff6b35; --green: #00ff88;
      --yellow: #ffd60a; --white: #ffffff;
    }
    html { scroll-behavior: smooth; }
    body { background: var(--bg); color: var(--text); font-family: 'DM Mono', monospace; overflow-x: hidden; line-height: 1.6; }

    nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      display: flex; align-items: center; justify-content: space-between;
      padding: 18px 48px;
      background: rgba(6,9,16,0.88); backdrop-filter: blur(16px);
      border-bottom: 1px solid rgba(26,42,58,0.6);
    }
    .nav-logo { display: flex; align-items: center; gap: 12px; font-family: 'Syne', sans-serif; font-weight: 800; font-size: 15px; color: var(--white); text-decoration: none; }
    .nav-logo-icon { width: 32px; height: 32px; background: linear-gradient(135deg, var(--cyan), #0066ff); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px; }
    .nav-links { display: flex; align-items: center; gap: 32px; }
    .nav-links a { font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); text-decoration: none; transition: color 0.2s; }
    .nav-links a:hover { color: var(--text); }
    .nav-cta { display: flex; align-items: center; gap: 10px; }
    .btn { display: inline-flex; align-items: center; justify-content: center; padding: 9px 20px; border-radius: 8px; border: none; font-family: 'DM Mono', monospace; font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; cursor: pointer; text-decoration: none; transition: all 0.2s; font-weight: 500; }
    .btn-outline { background: transparent; border: 1px solid var(--border2); color: var(--muted2); }
    .btn-outline:hover { border-color: var(--cyan); color: var(--cyan); }
    .btn-primary { background: var(--cyan); color: #060910; }
    .btn-primary:hover { background: #00eeff; }
    .btn-lg { padding: 14px 32px; font-size: 13px; border-radius: 10px; }
    .btn-xl { padding: 16px 40px; font-size: 14px; border-radius: 12px; }
                                                                                                                                                          /* antes 80 bottom */
    .hero { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 120px 24px 0px; position: relative; overflow: hidden; }
    .hero-bg { position: absolute; inset: 0; pointer-events: none; }
    .hero-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(26,42,58,0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(26,42,58,0.3) 1px, transparent 1px); background-size: 60px 60px; mask-image: radial-gradient(ellipse 80% 60% at 50% 0%, black 40%, transparent 100%); }
    .hero-glow-1 { position: absolute; top: 15%; left: 50%; transform: translateX(-50%); width: 700px; height: 400px; background: radial-gradient(ellipse, rgba(0,212,255,0.08) 0%, transparent 70%); }
    .hero-glow-2 { position: absolute; bottom: 10%; left: 20%; width: 400px; height: 300px; background: radial-gradient(ellipse, rgba(255,107,53,0.06) 0%, transparent 70%); }
    .hero-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(0,212,255,0.08); border: 1px solid rgba(0,212,255,0.2); border-radius: 100px; padding: 6px 16px; font-size: 11px; color: var(--cyan); letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 28px; }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--cyan); animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.7)} }
    .hero h1 { font-family: 'Syne', sans-serif; font-weight: 800; font-size: clamp(36px, 6vw, 68px); line-height: 1.05; letter-spacing: -0.02em; color: var(--white); max-width: 780px; margin-bottom: 24px; }
    .hero h1 .accent { color: var(--cyan); }
    .hero h1 .accent-orange { color: var(--orange); }
    .hero-sub { font-size: clamp(13px, 1.5vw, 15px); color: var(--muted2); max-width: 500px; line-height: 1.8; margin-bottom: 40px; }
    .hero-actions { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; justify-content: center; margin-bottom: 60px; }
    .hero-stats { display: flex; align-items: center; gap: 40px; flex-wrap: wrap; justify-content: center; padding: 20px 40px; background: rgba(13,21,32,0.8); border: 1px solid var(--border); border-radius: 14px; margin-bottom: 64px; margin-left: 25px; margin-right: 25px; width: 1000px;}
    .hero-stat-value { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 24px; color: var(--white); }
    .hero-stat-label { font-size: 10px; color: var(--muted); letter-spacing: 0.1em; text-transform: uppercase; margin-top: 2px; }
    .hero-stat-divider { width: 1px; height: 40px; background: var(--border); }

    .mockup-wrap { max-width: 780px; width: 100%; }
    .mockup-frame { background: var(--bg3); border: 1px solid var(--border2); border-radius: 14px; overflow: hidden; box-shadow: 0 40px 80px rgba(0,0,0,0.5); }
    .mockup-bar { background: #080c12; padding: 12px 16px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; }
    .mockup-dot { width: 10px; height: 10px; border-radius: 50%; }
    .mockup-url { flex: 1; text-align: center; font-size: 11px; color: var(--muted); }
    .mockup-body { padding: 24px; display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 12px; }
    .mockup-stat { background: #080c12; border: 1px solid var(--border); border-radius: 8px; padding: 14px; }
    .mockup-stat-val { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 22px; margin-bottom: 4px; }
    .mockup-stat-lbl { font-size: 9px; color: var(--muted); letter-spacing: 0.1em; text-transform: uppercase; }
    .mockup-list { padding: 0 24px 24px; }
    .mockup-row { display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: #080c12; border: 1px solid var(--border); border-radius: 8px; margin-bottom: 8px; }
    .mockup-dot-status { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .mockup-domain { font-size: 12px; color: var(--cyan); flex: 1; }
    .mockup-tag { font-size: 9px; padding: 2px 8px; border-radius: 4px; letter-spacing: 0.08em; text-transform: uppercase; }
    .toggle-mini { width: 32px; height: 16px; border-radius: 8px; position: relative; }
    .toggle-mini-knob { position: absolute; top: 2px; width: 12px; height: 12px; border-radius: 50%; background: white; }

    .section { padding: 100px 24px; }
    .container { max-width: 1000px; margin: 0 auto; }
    .section-label { font-size: 10px; letter-spacing: 0.2em; text-transform: uppercase; color: var(--cyan); margin-bottom: 14px; }
    .section-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: clamp(28px, 4vw, 46px); line-height: 1.1; color: var(--white); margin-bottom: 16px; }
    .section-sub { font-size: 13px; color: var(--muted2); max-width: 560px; line-height: 1.8; }

    section#problema {
    padding-top: 50px !important;
    }

    .problem-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 52px; }
    .problem-card { background: var(--bg3); border: 1px solid var(--border); border-radius: 12px; padding: 24px; position: relative; overflow: hidden; transition: border-color 0.3s; }
    .problem-card:hover { border-color: var(--border2); }
    .problem-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, var(--orange), transparent); }
    .problem-icon { font-size: 28px; margin-bottom: 14px; }
    .problem-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 15px; color: var(--white); margin-bottom: 8px; }
    .problem-desc { font-size: 12px; color: var(--muted2); line-height: 1.8; }

    .blocker-wrap { margin-top: 40px; background: var(--bg3); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .blocker-bar { background: #080c12; padding: 10px 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border); }
    .blocker-bar-label { font-size: 10px; color: #e74c3c; }
    .blocker-bar-url { font-size: 10px; color: var(--muted); margin-left: auto; }
    .blocker-content { padding: 28px 32px; display: flex; gap: 24px; align-items: flex-start; }
    .blocker-icon-big { font-size: 36px; flex-shrink: 0; }
    .blocker-text { font-family: Arial, sans-serif; font-size: 13px; color: #333; background: #f8f8f8; padding: 20px 24px; border-radius: 8px; line-height: 1.7; margin-bottom: 12px; flex: 1; }
    .blocker-url-pill { background: #e8f0fe; border-radius: 6px; padding: 10px 14px; font-family: monospace; font-size: 10px; color: #1558d6; word-break: break-all; }
    .blocker-caption { font-size: 10px; color: var(--muted); text-align: center; padding: 12px 0 16px; letter-spacing: 0.06em; }

    .detection-section { padding: 100px 24px; background: var(--bg2); }
    .detection-box { background: var(--bg3); border: 1px solid rgba(0,255,136,0.2); border-radius: 14px; padding: 28px 32px; margin-top: 40px; position: relative; overflow: hidden; }
    .detection-box::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, var(--green), transparent); }
    .detection-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 16px; color: var(--white); margin-bottom: 8px; }
    .detection-desc { font-size: 12px; color: var(--muted2); line-height: 1.8; margin-bottom: 24px; max-width: 700px; }
    .detection-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .detection-case { border-radius: 10px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 12px; }
    .detection-case.blocked { background: rgba(255,68,68,0.06); border: 1px solid rgba(255,68,68,0.2); }
    .detection-case.safe { background: rgba(0,255,136,0.05); border: 1px solid rgba(0,255,136,0.15); }
    .detection-case-icon { font-size: 20px; flex-shrink: 0; margin-top: 2px; }
    .detection-case-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 12px; margin-bottom: 5px; }
    .detection-case.blocked .detection-case-title { color: #ff4444; }
    .detection-case.safe .detection-case-title { color: var(--green); }
    .detection-case-desc { font-size: 11px; color: var(--muted2); line-height: 1.7; }

    .features-section { padding: 100px 24px; }
    .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 56px; }
    .feature-card { background: var(--bg3); border: 1px solid var(--border); border-radius: 14px; padding: 28px; transition: all 0.3s; position: relative; overflow: hidden; }
    .feature-card:hover { border-color: var(--border2); transform: translateY(-2px); }
    .feature-card::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(0,212,255,0.2), transparent); opacity: 0; transition: opacity 0.3s; }
    .feature-card:hover::after { opacity: 1; }
    .feature-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 18px; }
    .feature-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 15px; color: var(--white); margin-bottom: 10px; }
    .feature-desc { font-size: 12px; color: var(--muted2); line-height: 1.8; }

    .how-section { padding: 100px 24px; background: var(--bg2); }
    .steps { margin-top: 60px; position: relative; }
    .steps-line { position: absolute; left: 23px; top: 0; bottom: 0; width: 1px; background: linear-gradient(180deg, var(--cyan), var(--orange), transparent); }
    .step { display: flex; gap: 28px; margin-bottom: 44px; position: relative; }
    .step-num { width: 46px; height: 46px; border-radius: 50%; flex-shrink: 0; background: var(--bg3); border: 1px solid var(--border2); display: flex; align-items: center; justify-content: center; font-family: 'Syne', sans-serif; font-weight: 800; font-size: 16px; color: var(--cyan); position: relative; z-index: 1; }
    .step-content { padding-top: 8px; }
    .step-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 16px; color: var(--white); margin-bottom: 8px; }
    .step-desc { font-size: 12px; color: var(--muted2); line-height: 1.8; max-width: 520px; }
    .step-tag { display: inline-block; margin-top: 10px; font-size: 10px; letter-spacing: 0.08em; text-transform: uppercase; padding: 3px 10px; border-radius: 4px; }

    .api-box { background: var(--bg3); border: 1px solid var(--border2); border-radius: 14px; padding: 24px 28px; margin-top: 40px; }
    .api-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 10px; }
    .api-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 14px; color: var(--white); }
    .api-badge { font-size: 10px; color: var(--cyan); background: rgba(0,212,255,0.1); padding: 3px 10px; border-radius: 4px; letter-spacing: 0.06em; }
    .api-desc { font-size: 11px; color: var(--muted2); line-height: 1.7; margin-bottom: 14px; }
    .api-endpoint { background: var(--bg); border: 1px solid var(--border); border-radius: 8px; padding: 12px 16px; font-size: 11px; color: var(--cyan); font-family: 'DM Mono', monospace; margin-bottom: 14px; word-break: break-all; }
    .api-tags { display: flex; gap: 8px; flex-wrap: wrap; }
    .api-tag { font-size: 10px; padding: 3px 10px; border-radius: 4px; }
    .api-tag.green { background: rgba(0,255,136,0.08); color: var(--green); }
    .api-tag.yellow { background: rgba(255,214,10,0.08); color: var(--yellow); }
    .api-tag.cyan { background: rgba(0,212,255,0.08); color: var(--cyan); }

    .tech-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-top: 32px; }
    .tech-card { background: var(--bg3); border: 1px solid var(--border); border-radius: 10px; padding: 16px; text-align: center; }
    .tech-icon { font-size: 24px; margin-bottom: 8px; }
    .tech-name { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 12px; color: var(--white); margin-bottom: 3px; }
    .tech-desc { font-size: 10px; color: var(--muted); }

    .cta-section { padding: 120px 24px; text-align: center; position: relative; overflow: hidden; border-top: 1px solid var(--border); }
    .cta-glow { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 600px; height: 400px; background: radial-gradient(ellipse, rgba(0,212,255,0.07) 0%, transparent 70%); pointer-events: none; }
    .cta-section h2 { font-family: 'Syne', sans-serif; font-weight: 800; font-size: clamp(28px, 4vw, 52px); color: var(--white); margin-bottom: 16px; line-height: 1.1; position: relative; z-index: 1; }
    .cta-section p { font-size: 14px; color: var(--muted2); margin-bottom: 40px; position: relative; z-index: 1; }
    .cta-actions { display: flex; justify-content: center; gap: 14px; flex-wrap: wrap; position: relative; z-index: 1; }

    footer { border-top: 1px solid var(--border); padding: 32px 48px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
    footer p { font-size: 11px; color: var(--muted); }
    footer a { font-size: 11px; color: var(--muted); text-decoration: none; transition: color 0.2s; }
    footer a:hover { color: var(--cyan); }

    @media (max-width: 768px) {
      nav { padding: 14px 20px; }
      .nav-links { display: none; }
      .problem-grid, .features-grid, .detection-grid, .tech-grid { grid-template-columns: 1fr; }
      .hero-stat-divider { display: none; }
      .hero-stats { gap: 20px; padding: 16px 24px; }
      .mockup-body { grid-template-columns: 1fr 1fr; }
      .blocker-content { flex-direction: column; }
      footer { flex-direction: column; text-align: center; }
    }

    /* SVG ICONS */
    svg.size-2 {
      width: 12%;
    }
  </style>
</head>
<body>

<nav>
  <a href="/" class="nav-logo">
    <div class="nav-logo-icon">☁</div>
    CF Proxy Manager
  </a>
  <div class="nav-links">
      <a href="#problema">El problema</a>
      <a href="#features">Funcionalidades</a>
      <a href="#como-funciona">Cómo funciona</a>
  </div>
  <div class="nav-cta">
    @auth
      <a href="{{ route('dashboard') }}" class="btn btn-primary">
        📊 Dashboard →
      </a>
    @else
      <a href="{{ route('login') }}" class="btn btn-outline">Iniciar sesión</a>
    @endauth
  </div>
</nav>

<section class="hero">
  <div class="hero-bg">
    <div class="hero-grid"></div>
    <div class="hero-glow-1"></div>
    <div class="hero-glow-2"></div>
  </div>
  <div class="hero-badge">
    <div class="badge-dot"></div>
    Automatización Cloudflare · LaLiga & SSL
  </div>
  <h1>Tu proxy Cloudflare,<br/><span class="accent">siempre en el momento</span><br/><span class="accent-orange">correcto.</span></h1>
  <p class="hero-sub">Automatiza la activación y desactivación del proxy de Cloudflare cuando hay partidos de LaLiga o renovaciones SSL. Sin intervención manual, sin cortes inesperados.</p>
  <div class="hero-actions">
    @auth
      <a href="{{ route('dashboard') }}" class="btn btn-primary">📊 Ir al dashboard →</a>
    @else
      <a href="{{ route('login') }}" class="btn btn-primary">Acceder al panel →</a>
    @endauth
    <a href="#como-funciona" class="btn btn-outline">Ver cómo funciona</a>
  </div>
  <div class="hero-stats">
    <img style="width: 100%" src="{{ asset('./images/cf-proxy-manager.png') }}">
  </div>
</section>

<section class="section" id="problema">
  <div class="container">
    <div class="section-label">⚠ El problema</div>
    <h2 class="section-title">Diversas webs<br/>bloqueadas</h2>
    <p class="section-sub">Durante los partidos de la Liga Nacional de Fútbol Profesional, múltiples webs que no tienen nada que ver con el fútbol caen bloqueadas.</p>
    <div class="problem-grid">
      <div class="problem-card">
        <div class="problem-icon">⚽</div>
        <div class="problem-title">Bloqueos en cada partido</div>
        <div class="problem-desc">Cualquier dominio detrás del proxy de Cloudflare puede quedar inaccesible en España durante los partidos, sin importar su contenido.</div>
      </div>
      <div class="problem-card">
        <div class="problem-icon">🔒</div>
        <div class="problem-title">SSL que no renueva</div>
        <div class="problem-desc">El proxy intercepta el reto HTTP-01 de Let's Encrypt. Si está activo cuando el servidor renueva, el certificado caduca silenciosamente.</div>
      </div>
      <div class="problem-card">
        <div class="problem-icon">⏰</div>
        <div class="problem-title">Horarios impredecibles</div>
        <div class="problem-desc">Partidos entre semana, fines de semana, a distintas horas. Estar pendiente de cada jornada para entrar manualmente es insostenible.</div>
      </div>
      <div class="problem-card">
        <div class="problem-icon">😰</div>
        <div class="problem-title">El olvido cuesta caro</div>
        <div class="problem-desc">Una web caída durante un partido clave, un cliente llamando el sábado por la tarde. El coste de olvidarse siempre supera al de automatizar.</div>
      </div>
    </div>
    <div class="blocker-wrap">
      <div class="blocker-bar">
        <div class="blocker-bar-label">🔴 No es seguro</div>
        <div class="blocker-bar-url">https://tudominio.es</div>
      </div>
      <div class="blocker-content">
        <div class="blocker-icon-big">🚫</div>
        <div style="flex:1">
          <div class="blocker-text">El acceso a la presente dirección IP ha sido bloqueado en cumplimiento de lo dispuesto en la Sentencia de 18 de diciembre de 2024, dictada por el Juzgado de lo Mercantil nº 6 de Barcelona en el marco del procedimiento ordinario (Materia mercantil art. 249.1.4)-1005/2024-H instado por la Liga Nacional de Fútbol Profesional y por Telefónica Audiovisual Digital, S.L.U.</div>
          <div class="blocker-url-pill">https://www.laliga.com/noticias/nota-informativa-en-relacion-con-el-bloqueo-de-ips-durante-las-ultimas-jornadas-de-laliga-ea-sports-vinculadas-a-las-practicas-ilegales-de-cloudflare</div>
        </div>
      </div>
      <div class="blocker-caption">Esto ven tus usuarios en España durante un partido si tu proxy está activo</div>
    </div>
  </div>
</section>

<section class="detection-section">
  <div class="container">
    <div class="section-label">🛡 Detección inteligente</div>
    <h2 class="section-title">Solo desactiva lo que<br/>realmente está bloqueado</h2>
    <p class="section-sub">Antes de tocar cualquier dominio, el sistema comprueba si realmente está siendo bloqueado. Si responde con normalidad, el proxy permanece activo.</p>
    <div class="detection-box">
      <div class="detection-title">¿Cómo funciona la comprobación?</div>
      <div class="detection-desc">Justo antes de ejecutar un schedule, CF Proxy Manager hace una petición HTTP a cada dominio y analiza la respuesta. Si detecta el bloqueo lo desactiva. Si el dominio responde con normalidad, lo omite y lo registra en logs. La desactivación manual desde el dashboard siempre está disponible independientemente del resultado de la petición.</div>
      <div class="detection-grid">
        <div class="detection-case blocked">
          <div class="detection-case-icon">🔴</div>
          <div>
            <div class="detection-case-title">Dominio bloqueado — se desactiva</div>
            <div class="detection-case-desc">La petición detecta el texto de la sentencia de LaLiga. El proxy se desactiva automáticamente y se registra con razón y timestamp.</div>
          </div>
        </div>
        <div class="detection-case safe">
          <div class="detection-case-icon">🟢</div>
          <div>
            <div class="detection-case-title">Dominio accesible — se omite</div>
            <div class="detection-case-desc">La web responde con normalidad. El proxy permanece activo, el dominio mantiene toda la protección de Cloudflare. La omisión queda registrada.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="features-section" id="features">
  <div class="container">
    <div class="section-label">✨ Funcionalidades</div>
    <h2 class="section-title">Todo lo que necesitas,<br/>nada que no necesitas</h2>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon" style="background:rgba(255,107,53,0.1)">⚽</div>
        <div class="feature-title">Schedules LaLiga</div>
        <div class="feature-desc">Conexión a la API de football-data.org, consulta los partidos del día cada madrugada y crea schedules automáticos con margen antes y después de cada partido.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="background:rgba(0,212,255,0.1)">🔒</div>
        <div class="feature-title">SSL automático</div>
        <div class="feature-desc">Programa ventanas para que Let's Encrypt complete el reto HTTP-01 sin interferencias. El siguiente ciclo se programa automáticamente cada 90 días.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="background:rgba(0,255,136,0.1)">☁</div>
        <div class="feature-title">API Cloudflare</div>
        <div class="feature-desc">Conexión directa vía PATCH a la API oficial. Solo cambia el campo proxied del registro DNS, sin tocar ningún otro parámetro del dominio.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="background:rgba(255,214,10,0.1)">📊</div>
        <div class="feature-title">Dashboard en tiempo real</div>
        <div class="feature-desc">Estado del proxy sincronizado con Cloudflare. Toggle manual instantáneo desde el panel cuando necesitas intervenir sin esperar al scheduler.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="background:rgba(167,139,250,0.1)">📋</div>
        <div class="feature-title">Logs y exportación</div>
        <div class="feature-desc">Historial completo de cada cambio con acción, razón, estado y timestamp. Exporta a XLSX con un clic.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="background:rgba(0,212,255,0.08)">🔐</div>
        <div class="feature-title">Cifrado</div>
        <div class="feature-desc">Zone IDs y Record IDs cifrados con AES-256-CBC. Los tokens de API solo viven en variables de entorno, nunca en la base de datos.</div>
      </div>
    </div>
  </div>
</section>

<section class="how-section" id="como-funciona">
  <div class="container">
    <div class="section-label">⚙ Cómo funciona</div>
    <h2 class="section-title">Configura una vez,<br/>olvídate para siempre</h2>
    <div class="steps">
      <div class="steps-line"></div>
      <div class="step">
        <div class="step-num">1</div>
        <div class="step-content">
          <div class="step-title">Conecta tu cuenta de Cloudflare</div>
          <div class="step-desc">Genera un API Token con permisos Zone:DNS:Edit y Zone:Zone:Read. El sistema detecta automáticamente tus zonas y registros DNS al añadir cada dominio.</div>
          <span class="step-tag" style="background:rgba(0,212,255,0.08);color:var(--cyan)">5 minutos de configuración</span>
        </div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div class="step-content">
          <div class="step-title">Añade tus dominios</div>
          <div class="step-desc">Para cada dominio indica si está afectado por LaLiga y si tiene renovación SSL automática. El sistema descubre el DNS Record ID automáticamente.</div>
          <span class="step-tag" style="background:rgba(255,107,53,0.08);color:var(--orange)">⚽ LaLiga &nbsp;·&nbsp; 🔒 SSL</span>
        </div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div class="step-content">
          <div class="step-title">La API de football-data.org hace el trabajo</div>
          <div class="step-desc">Cada madrugada a las 00:00 el sistema consulta football-data.org. Si hay partidos hoy, crea el schedule automáticamente. Si no hay partidos, no hace nada.</div>
          <span class="step-tag" style="background:rgba(0,255,136,0.08);color:var(--green)">API football-data.org · Plan gratuito</span>
        </div>
      </div>
      <div class="step">
        <div class="step-num">4</div>
        <div class="step-content">
          <div class="step-title">El scheduler actúa solo</div>
          <div class="step-desc">Cada minuto comprueba si hay acciones pendientes. Cuando llega la hora, verifica qué dominios están bloqueados y solo desactiva esos. Al terminar el partido, reactiva todo.</div>
          <span class="step-tag" style="background:rgba(255,214,10,0.08);color:var(--yellow)">Cron job cada minuto</span>
        </div>
      </div>
    </div>
</section>

<section class="cta-section">
  <div class="cta-glow"></div>
  <h2>Sin más sábados<br/>mirando Cloudflare.</h2>
  <p>Configura tus dominios una vez. El sistema hace el resto.</p>
  <div class="cta-actions">
    @auth
      <a href="{{ route('dashboard') }}" class="btn btn-primary">📊 Ir al dashboard →</a>
    @else
      <a href="{{ route('login') }}" class="btn btn-primary btn-xl">Acceder al panel →</a>
      <a href="#como-funciona" class="btn btn-outline btn-lg">Ver cómo funciona</a>
    @endauth
  </div>
</section>

<footer>
  <div class="nav-logo" style="font-size:13px">
    <div class="nav-logo-icon" style="width:26px;height:26px;font-size:12px">☁</div>
    CF Proxy Manager
  </div>
    <p>© {{ date("Y") }} CF Proxy Manager · Developed by <a href="https://github.com/raortega8906/" target="_blank" rel="noopener noreferrer">Rafael A. Ortega</a></p>
  <div style="display:flex;gap:20px">
    <a href="https://github.com/raortega8906/cf-proxy-manager" target="_blank" rel="noopener noreferrer">GitHub</a>
  </div>
</footer>

</body>
</html>
