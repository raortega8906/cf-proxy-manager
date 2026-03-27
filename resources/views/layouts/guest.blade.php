<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', config('app.name', 'CF Proxy Manager'))</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('./images/logo.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('./images/logo.ico') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #060910; --bg2: #0a0f1a; --bg3: #0d1520;
            --border: #1a2a3a; --border2: #1e3a5f;
            --text: #e2e8f0; --muted: #4a6285; --muted2: #94a3b8;
            --cyan: #00d4ff; --orange: #ff6b35; --green: #00ff88;
            --yellow: #ffd60a; --white: #ffffff; --red: #ff4444;
        }
        body {
            background: var(--bg); color: var(--text);
            font-family: 'DM Mono', monospace; min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 24px;
        }
        body::before {
            content: '';
            position: fixed; inset: 0; pointer-events: none;
            background-image:
                linear-gradient(rgba(26,42,58,0.3) 1px, transparent 1px),
                linear-gradient(90deg, rgba(26,42,58,0.3) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 80% 60% at 50% 0%, black 40%, transparent 100%);
        }
        body::after {
            content: '';
            position: fixed; top: 15%; left: 50%; transform: translateX(-50%);
            width: 700px; height: 400px; pointer-events: none;
            background: radial-gradient(ellipse, rgba(0,212,255,0.06) 0%, transparent 70%);
        }
        .guest-logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; margin-bottom: 32px; position: relative; z-index: 1;
        }
        /* ICONS */
        .nav-logo { display: flex; align-items: center; gap: 12px; font-family: 'Syne', sans-serif; font-weight: 800; font-size: 15px; color: var(--white); text-decoration: none; padding-bottom: 20px;}
        .nav-logo-icon { width: 32px; height: 32px; background: linear-gradient(135deg, var(--cyan), #0066ff); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px; }
        .guest-logo-text {
            font-family: 'Syne', sans-serif; font-weight: 800;
            font-size: 15px; color: var(--white);
        }
        .guest-card {
            width: 100%; max-width: 420px;
            background: var(--bg2); border: 1px solid var(--border);
            border-radius: 16px; padding: 36px;
            position: relative; z-index: 1;
            box-shadow: 0 40px 80px rgba(0,0,0,0.4);
        }
        .guest-card-title {
            font-family: 'Syne', sans-serif; font-weight: 800;
            font-size: 20px; color: var(--white); margin-bottom: 6px;
        }
        .guest-card-sub {
            font-size: 11px; color: var(--muted); margin-bottom: 28px; line-height: 1.6;
        }
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
        .alert-status { font-size: 11px; color: var(--green); background: rgba(0,255,136,0.07); border: 1px solid rgba(0,255,136,0.2); border-radius: 7px; padding: 10px 14px; margin-bottom: 20px; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 20px; border-radius: 8px; border: none;
            font-family: 'DM Mono', monospace; font-size: 11px;
            letter-spacing: 0.06em; text-transform: uppercase;
            cursor: pointer; text-decoration: none; transition: all 0.2s; font-weight: 500;
        }
        .btn-primary { background: var(--cyan); color: #060910; }
        .btn-primary:hover { background: #00eeff; box-shadow: 0 0 20px rgba(0,212,255,0.35); }
        .btn-ghost { background: rgba(255,255,255,0.05); color: var(--muted2); border: 1px solid var(--border); }
        .btn-ghost:hover { background: rgba(255,255,255,0.1); }
        .btn-full { width: 100%; }
        .form-check { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; cursor: pointer; }
        .form-check input[type=checkbox] { accent-color: var(--cyan); width: 14px; height: 14px; }
        .form-check-label { font-size: 11px; color: var(--muted2); }
        .guest-link { font-size: 11px; color: var(--muted2); text-decoration: none; transition: color 0.2s; }
        .guest-link:hover { color: var(--cyan); }
        .divider { height: 1px; background: var(--border); margin: 20px 0; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-end { justify-content: flex-end; }
        .gap-2 { gap: 8px; }
        .mt-4 { margin-top: 16px; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <a href="{{ url('/') }}" class="nav-logo">
        <div class="nav-logo-icon">☁</div>
        <div class="guest-logo-text">{{ config('app.name', 'CF Proxy Manager') }}</div>
    </a>

    <div class="guest-card">
        @yield('content')
    </div>
</body>
</html>