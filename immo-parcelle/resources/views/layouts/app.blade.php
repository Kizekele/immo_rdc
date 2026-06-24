<!DOCTYPE html>
<html lang="fr" class="h-full antialiased selection:bg-emerald-500 selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMMO.RDC | Immobilier Premium en RDC</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --nav-bg: rgba(10, 16, 36, 0.75);
            --card-bg: rgba(15, 23, 48, 0.6);
            --border: rgba(255, 255, 255, 0.06);
            --border-hover: rgba(255, 255, 255, 0.12);
            --emerald: #10b981;
            --emerald-dim: rgba(16, 185, 129, 0.15);
            --amber: #f59e0b;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #080d1e;
            color: #e2e8f0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ─── Fond ambiant ─── */
        .bg-orbs {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .bg-orbs::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -15%;
            width: 60vw;
            height: 60vw;
            background: radial-gradient(circle, rgba(16,185,129,0.07) 0%, transparent 70%);
            border-radius: 50%;
            animation: orb-drift 18s ease-in-out infinite alternate;
        }
        .bg-orbs::after {
            content: '';
            position: absolute;
            bottom: -10%;
            right: -10%;
            width: 45vw;
            height: 45vw;
            background: radial-gradient(circle, rgba(139,92,246,0.05) 0%, transparent 70%);
            border-radius: 50%;
            animation: orb-drift 24s ease-in-out infinite alternate-reverse;
        }
        @keyframes orb-drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(3%, 4%) scale(1.06); }
        }

        /* Grille subtile */
        .bg-grid {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.022) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.022) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        /* ─── Navigation ─── */
        .nav-outer {
            position: sticky;
            top: 0;
            z-index: 50;
            padding: 14px 16px 0;
        }
        .nav-inner {
            max-width: 1280px;
            margin: 0 auto;
            background: var(--nav-bg);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: border-color 0.3s;
        }
        .nav-inner:hover { border-color: var(--border-hover); }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .nav-logo-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -1px;
            flex-shrink: 0;
            box-shadow: 0 0 16px rgba(16,185,129,0.3);
        }
        .nav-logo-text {
            font-size: 18px;
            font-weight: 800;
            color: #f1f5f9;
            letter-spacing: -0.5px;
        }
        .nav-logo-text em {
            font-style: normal;
            color: var(--amber);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .nav-link {
            position: relative;
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            text-decoration: none;
            padding: 7px 14px;
            border-radius: 10px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .nav-link:hover {
            color: #e2e8f0;
            background: rgba(255,255,255,0.05);
        }
        .nav-link.active {
            color: #10b981;
            background: rgba(16,185,129,0.08);
        }
        .nav-link .active-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 6px #10b981;
            display: none;
        }
        .nav-link.active .active-dot { display: block; }

        .nav-admin-btn {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #0f172a;
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            padding: 8px 18px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 14px rgba(16,185,129,0.25);
        }
        .nav-admin-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16,185,129,0.35);
        }
        .nav-admin-btn:active { transform: translateY(0); }
        .nav-admin-btn.is-active {
            background: #10b981;
            box-shadow: 0 0 0 2px rgba(16,185,129,0.4), 0 4px 14px rgba(16,185,129,0.3);
        }

        .nav-logout-btn {
            font-size: 13px;
            font-weight: 600;
            color: #ef4444;
            background: none;
            border: none;
            padding: 7px 14px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .nav-logout-btn:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        /* Mobile menu toggle */
        .nav-mobile-toggle {
            display: none;
            background: none;
            border: 1px solid var(--border);
            color: #94a3b8;
            padding: 6px 10px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
        }

        /* ─── Main content ─── */
        .main-content {
            position: relative;
            z-index: 10;
            max-width: 1280px;
            margin: 0 auto;
            padding: 32px 16px 48px;
        }

        /* ─── Footer ─── */
        .site-footer {
            position: relative;
            z-index: 10;
            border-top: 1px solid var(--border);
            background: rgba(8, 13, 30, 0.8);
            backdrop-filter: blur(10px);
            padding: 24px 16px;
        }
        .footer-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: center;
        }
        @media (min-width: 640px) {
            .footer-inner { flex-direction: row; justify-content: space-between; }
        }
        .footer-copy {
            font-size: 12px;
            color: #475569;
            font-weight: 500;
        }
        .footer-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #64748b;
        }
        .footer-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse-dot 2.5s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
            50%       { opacity: 0.7; box-shadow: 0 0 0 4px rgba(16,185,129,0); }
        }

        /* ─── Page transition ─── */
        .page-wrap {
            animation: page-in 0.4s ease-out both;
        }
        @keyframes page-in {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ─── Responsive ─── */
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .nav-mobile-toggle { display: flex; align-items: center; }
            .nav-links.mobile-open {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: calc(100% + 8px);
                left: 16px;
                right: 16px;
                background: rgba(10,16,36,0.97);
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 10px;
                z-index: 100;
                backdrop-filter: blur(20px);
            }
            .nav-link { width: 100%; }
            .nav-admin-btn { width: 100%; justify-content: center; }
            .nav-logout-btn { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

    {{-- Fond --}}
    <div class="bg-orbs" aria-hidden="true"></div>
    <div class="bg-grid" aria-hidden="true"></div>

    {{-- Navigation --}}
    <header class="nav-outer">
        <nav class="nav-inner" role="navigation" aria-label="Navigation principale" style="position: relative;">

            <a href="/" class="nav-logo" aria-label="IMMO.RDC — Accueil">
                <div class="nav-logo-icon">IR</div>
                <span class="nav-logo-text">IMMO<em>.</em>RDC</span>
            </a>

            <div class="nav-links" id="nav-links">
                {{-- LIENS COMMUNS (Toujours visibles) --}}
                <a href="/"
                   class="nav-link {{ request()->is('/') || request()->is('parcelle*') ? 'active' : '' }}">
                    <span class="active-dot" aria-hidden="true"></span>
                    Fiche Parcelle
                </a>

                {{-- SI L'UTILISATEUR N'EST PAS CONNECTÉ --}}
                @guest
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->is('login*') ? 'active' : '' }}">
                        <span class="active-dot" aria-hidden="true"></span>
                        Mon Espace Client
                    </a>
                @endguest

                {{-- SI L'UTILISATEUR EST CONNECTÉ --}}
                @auth
                    {{-- Masquer l'espace client si l'utilisateur connecté est un administrateur --}}
                    @if(auth()->user()->role !== 'administrateur')
                        <a href="/dashboard/acheteur"
                        class="nav-link {{ request()->is('dashboard/acheteur*') ? 'active' : '' }}">
                            <span class="active-dot" aria-hidden="true"></span>
                            Mon Espace Client
                        </a>
                    @endif

                    {{-- Rôle vérifié sur 'administrateur' --}}
                    @if(auth()->user()->role === 'administrateur')
                        <a href="/admin/dashboard"
                        class="nav-admin-btn {{ request()->is('dashboard/admin*') ? 'is-active' : '' }}">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Espace Admin
                        </a>
                    @endif

    {{-- Formulaire propre pour la Déconnexion --}}
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="nav-logout-btn">
            Déconnexion
        </button>
    </form>
@endauth
            </div>

            <button class="nav-mobile-toggle" onclick="toggleMobileMenu()" aria-label="Ouvrir le menu" aria-expanded="false" id="mobile-toggle">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path id="icon-menu" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </nav>
    </header>

    {{-- Contenu principal --}}
    <main class="main-content" id="main-content">
        <div class="page-wrap">
            @yield('content')
        </div>
    </main>

    {{-- Pied de page --}}
    <footer class="site-footer">
        <div class="footer-inner">
            <p class="footer-copy">
                &copy; {{ date('Y') }} IMMO.RDC &mdash; Investissez dans la terre, sécurisez votre avenir.
            </p>
            <div class="footer-status">
                <span class="footer-status-dot" aria-hidden="true"></span>
                Kinshasa, République Démocratique du Congo
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const links = document.getElementById('nav-links');
            const btn = document.getElementById('mobile-toggle');
            const isOpen = links.classList.toggle('mobile-open');
            btn.setAttribute('aria-expanded', isOpen);
        }
        document.addEventListener('click', function(e) {
            const links = document.getElementById('nav-links');
            const toggle = document.getElementById('mobile-toggle');
            if (!links.contains(e.target) && !toggle.contains(e.target)) {
                links.classList.remove('mobile-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    </script>
</body>
</html>