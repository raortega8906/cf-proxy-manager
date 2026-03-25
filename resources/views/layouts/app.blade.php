<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CF Proxy Manager')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('./images/logo.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('./images/logo.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #060910; --bg2: #0a0f1a; --bg3: #0d1520;
            --border: #1a2a3a; --border2: #1e3a5f;
            --text: #e2e8f0; --muted: #4a6285; --muted2: #94a3b8;
            --cyan: #00d4ff; --orange: #ff6b35; --green: #00ff88;
            --yellow: #ffd60a; --white: #ffffff; --red: #ff4444;
        }
        html { scroll-behavior: smooth; }
        body { background: var(--bg); color: var(--text); font-family: 'DM Mono', monospace; min-height: 100vh; display: flex; }

        /* SIDEBAR */
        .sidebar {
            width: 220px; min-height: 100vh; flex-shrink: 0;
            background: var(--bg2); border-right: 1px solid var(--border);
            display: flex; flex-direction: column; padding: 24px 0;
            position: fixed; top: 0; left: 0; bottom: 0;
        }
        .sidebar-logo {
            display: flex; align-items: center; gap: 10px;
            padding: 0 20px 28px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
        }
        .logo-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, var(--cyan), #0066ff);
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; box-shadow: 0 0 16px rgba(0,212,255,0.25);
        }
        .logo-text {
            font-family: 'Syne', sans-serif; font-weight: 800;
            font-size: 13px; color: var(--white); line-height: 1.2;
        }
        .logo-sub { font-size: 9px; color: var(--muted); letter-spacing: 0.1em; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 20px; font-size: 12px; color: var(--muted);
            text-decoration: none; transition: all 0.2s;
            border-left: 2px solid transparent;
            letter-spacing: 0.05em;
        }
        .nav-item:hover { color: var(--text); background: rgba(255,255,255,0.03); }
        .nav-item.active { color: var(--cyan); border-left-color: var(--cyan); background: rgba(0,212,255,0.05); }
        .nav-section {
            font-size: 9px; letter-spacing: 0.15em; text-transform: uppercase;
            color: var(--muted); padding: 16px 20px 6px;
        }
        .sidebar-footer {
            margin-top: auto; padding: 16px 20px;
            border-top: 1px solid var(--border);
        }
        .scheduler-badge {
            display: flex; align-items: center; gap: 8px;
            font-size: 10px; color: var(--green);
        }
        .pulse { width: 6px; height: 6px; border-radius: 50%; background: var(--green); animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.7)} }

        /* MAIN */
        .main { margin-left: 220px; flex: 1; min-height: 100vh; }
        .topbar {
            padding: 20px 32px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .page-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 22px; color: var(--white); }
        .page-sub { font-size: 11px; color: var(--muted); margin-top: 2px; }
        .content { padding: 32px; }

        /* ALERTS */
        .alert {
            padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;
            font-size: 12px; display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: rgba(0,255,136,0.08); border: 1px solid rgba(0,255,136,0.2); color: var(--green); }
        .alert-error   { background: rgba(255,68,68,0.08); border: 1px solid rgba(255,68,68,0.2); color: var(--red); }

        /* CARDS */
        .card {
            background: var(--bg3); border: 1px solid var(--border);
            border-radius: 12px; padding: 24px;
        }
        .card-title {
            font-family: 'Syne', sans-serif; font-weight: 700;
            font-size: 14px; color: var(--white); margin-bottom: 16px;
        }

        /* BUTTONS */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px; border: none;
            font-family: 'DM Mono', monospace; font-size: 11px;
            letter-spacing: 0.06em; text-transform: uppercase;
            cursor: pointer; text-decoration: none; transition: all 0.2s; font-weight: 500;
        }
        .btn-primary { background: var(--cyan); color: #060910; }
        .btn-primary:hover { background: #00eeff; box-shadow: 0 0 20px rgba(0,212,255,0.35); }
        .btn-danger { background: rgba(255,68,68,0.12); color: var(--red); border: 1px solid rgba(255,68,68,0.25); }
        .btn-danger:hover { background: rgba(255,68,68,0.22); }
        .btn-warning { background: rgba(255,107,53,0.12); color: var(--orange); border: 1px solid rgba(255,107,53,0.25); }
        .btn-warning:hover { background: rgba(255,107,53,0.22); }
        .btn-ghost { background: rgba(255,255,255,0.05); color: var(--muted2); border: 1px solid var(--border); }
        .btn-ghost:hover { background: rgba(255,255,255,0.1); }
        .btn-sm { padding: 5px 11px; font-size: 10px; }

        /* FORMS */
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
        .form-control {
            width: 100%; background: var(--bg); border: 1px solid var(--border);
            border-radius: 7px; padding: 10px 14px; color: var(--text);
            font-family: 'DM Mono', monospace; font-size: 13px; outline: none; transition: border-color 0.2s;
        }
        .form-control:focus { border-color: var(--cyan); }
        .form-control.is-invalid { border-color: var(--red); }
        .invalid-feedback { font-size: 11px; color: var(--red); margin-top: 4px; }
        .form-check { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; cursor: pointer; }
        .form-check input[type=checkbox] { accent-color: var(--cyan); width: 14px; height: 14px; }
        .form-check-label { font-size: 12px; color: var(--muted2); }

        /* TABLES */
        .table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .table th { font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); padding: 10px 12px; border-bottom: 1px solid var(--border); text-align: left; }
        .table td { padding: 13px 12px; border-bottom: 1px solid rgba(26,42,58,0.5); color: var(--muted2); vertical-align: middle; }
        .table tr:last-child td { border-bottom: none; }
        .table tr:hover td { background: rgba(255,255,255,0.015); }

        /* BADGES */
        .badge { display: inline-block; font-size: 9px; letter-spacing: 0.08em; text-transform: uppercase; padding: 3px 9px; border-radius: 4px; }
        .badge-ok      { background: rgba(0,255,136,0.1); color: var(--green); }
        .badge-off     { background: rgba(255,107,53,0.1); color: var(--orange); }
        .badge-warning { background: rgba(255,214,10,0.1); color: var(--yellow); }
        .badge-pending   { background: rgba(255,214,10,0.1); color: var(--yellow); }
        .badge-active    { background: rgba(0,212,255,0.1); color: var(--cyan); }
        .badge-completed { background: rgba(0,255,136,0.1); color: var(--green); }
        .badge-failed    { background: rgba(255,68,68,0.1); color: var(--red); }
        .badge-laliga  { background: rgba(255,107,53,0.1); color: var(--orange); }
        .badge-ssl     { background: rgba(0,212,255,0.1); color: var(--cyan); }
        .badge-manual  { background: rgba(167,139,250,0.1); color: #a78bfa; }

        /* TOGGLE */
        .toggle-wrap { display: flex; align-items: center; gap: 8px; }
        .toggle {
            width: 42px; height: 22px; border-radius: 11px; border: none;
            cursor: pointer; position: relative; transition: all 0.3s; flex-shrink: 0;
        }
        .toggle-on  { background: var(--cyan); box-shadow: 0 0 10px rgba(0,212,255,0.3); }
        .toggle-off { background: var(--border2); }
        .toggle-knob {
            position: absolute; top: 3px; width: 16px; height: 16px;
            border-radius: 50%; background: white; transition: all 0.3s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.4);
        }
        .toggle-on  .toggle-knob { left: 23px; }
        .toggle-off .toggle-knob { left: 3px; }

        /* GRID */
        .grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; }
        .grid-2 { display: grid; grid-template-columns: repeat(2,1fr); gap: 16px; }
        @media(max-width:900px) { .grid-4 { grid-template-columns: repeat(2,1fr); } }

        /* STAT */
        .stat-val { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 28px; }
        .stat-lbl { font-size: 10px; color: var(--muted); letter-spacing: 0.1em; text-transform: uppercase; margin-top: 4px; }

        /* PAGINATION */
        .pagination { display: flex; gap: 6px; margin-top: 20px; justify-content: center; }
        .pagination a, .pagination span {
            padding: 6px 12px; border-radius: 6px; font-size: 11px;
            background: var(--bg3); border: 1px solid var(--border); color: var(--muted2); text-decoration: none;
        }
        .pagination .active span { background: var(--cyan); color: #060910; border-color: var(--cyan); }

        /* LISTS */
        ul, ol {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        /* UTILS */
        .text-cyan   { color: var(--cyan); }
        .text-green  { color: var(--green); }
        .text-orange { color: var(--orange); }
        .text-yellow { color: var(--yellow); }
        .text-muted  { color: var(--muted2); }
        .text-white  { color: var(--white); }
        .mt-4  { margin-top: 16px; }
        .mt-6  { margin-top: 24px; }
        .mb-4  { margin-bottom: 16px; }
        .flex  { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }

        /* INPUT DATE */
        input[type="date"], input[type="datetime-local"] {
            color-scheme: dark;
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<aside class="sidebar">
    <div class="sidebar-logo">
        <a href="/" style="text-decoration:none;">
            <div class="logo-icon">☁</div>
        </a>
        <div>
            <div class="logo-text">CF Proxy</div>
            <div class="logo-sub">MANAGER</div>
        </div>
    </div>

    <span class="nav-section">Principal</span>
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        📊 Dashboard
    </a>
    <a href="{{ route('sites.index') }}" class="nav-item {{ request()->routeIs('sites.*') ? 'active' : '' }}">
        🌐 Sitios
    </a>
    <a href="{{ route('schedules.index') }}" class="nav-item {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
        📅 Schedules
    </a>
    <a href="{{ route('logs.index') }}" class="nav-item {{ request()->routeIs('logs.*') ? 'active' : '' }}">
        📋 Logs
    </a>

    {{-- Profile --}}
    <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        👤 Perfil
    </a>

    <div class="sidebar-footer">
        <div class="scheduler-badge">
            <div class="pulse"></div>
            Cloudflare activo
        </div>
    </div>
</aside>

{{-- MAIN --}}
<main class="main">
    <div class="topbar">
        <div>
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
            <div class="page-sub">@yield('page-sub', 'CF Proxy Manager')</div>
        </div>
        <div class="flex gap-2">
            @yield('topbar-actions')
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost">
                    🚪 Cerrar sesión
                </button>
            </form>
        </div>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">✗ {{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</main>

@stack('scripts')
</body>
</html>
