<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Infirmier - Hôpital Sendwe | @yield('title')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2c6e9e;
            --primary-light: #3d8fc4;
            --primary-dark: #1a4d8c;
            --secondary: #d4af37;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #3498db;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-white: #ffffff;
            --border: #e2e8f0;
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --radius: 16px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2f5 100%);
            color: var(--text-dark);
            overflow-x: hidden;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #0f2b3d 0%, #1a4d8c 50%, #2c6e9e 100%);
            padding: 8px 0;
            position: sticky;
            top: 0;
            z-index: 1050;
            box-shadow: var(--shadow-md);
            min-height: 60px;
        }

        .navbar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .navbar-brand i { font-size: 1.3rem; }
        .navbar-brand .brand-text { line-height: 1.2; }
        .navbar-brand small { font-size: 0.55rem; opacity: 0.85; display: block; font-weight: 400; }

        .navbar-toggler {
            border: none;
            padding: 6px 10px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            cursor: pointer;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            width: 24px;
            height: 24px;
            display: inline-block;
        }

        .nav-link {
            color: rgba(255,255,255,0.9);
            transition: all 0.3s ease;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            text-decoration: none;
        }

        .nav-link:hover { color: white; background: rgba(255,255,255,0.1); }

        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-lg);
            border-radius: var(--radius);
            margin-top: 6px;
            padding: 6px;
            min-width: 200px;
            background: white;
        }

        .dropdown-item {
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 13px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
            background: transparent;
            border: none;
            width: 100%;
            cursor: pointer;
        }

        .dropdown-item:hover { background: #eaf2f8; color: var(--primary); }
        .dropdown-item i { width: 20px; text-align: center; }

        .dropdown-item.text-danger:hover {
            background: #fee7e7;
            color: var(--danger);
        }

        .dropdown-divider {
            height: 1px;
            margin: 4px 0;
            background: var(--border);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 6px;
            font-size: 14px;
            color: white;
            flex-shrink: 0;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1035;
        }

        .sidebar-overlay.show { display: block; }

        .sidebar {
            background: var(--bg-white);
            min-height: calc(100vh - 60px);
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 60px;
            border-right: 1px solid var(--border);
            padding: 8px 0;
            overflow-y: auto;
            max-height: calc(100vh - 60px);
            transition: all 0.3s ease;
            z-index: 1040;
        }

        .sidebar .sidebar-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--secondary);
            padding: 12px 16px 4px;
            font-weight: 700;
        }

        .sidebar .nav-link {
            color: var(--text-gray);
            padding: 8px 16px;
            border-radius: 10px;
            margin: 2px 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 13px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background: #eaf2f8;
            color: var(--primary);
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            box-shadow: var(--shadow-md);
        }

        .sidebar .nav-link.active i { color: white; }

        .sidebar .nav-link i {
            width: 22px;
            margin-right: 10px;
            font-size: 15px;
            color: var(--text-gray);
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover i,
        .sidebar .nav-link.active i { color: white; }

        .main-content { padding: 12px 12px 80px 12px; min-height: calc(100vh - 60px); }

        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            background: var(--bg-white);
            margin-bottom: 16px;
        }

        .card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }

        .card-header {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-bottom: 1px solid var(--border);
            padding: 12px 16px;
            font-weight: 600;
            font-size: 14px;
            color: var(--primary);
            border-radius: var(--radius) var(--radius) 0 0 !important;
        }

        .card-body { padding: 16px; }

        .stat-card {
            background: var(--bg-white);
            border-radius: var(--radius);
            padding: 14px 16px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, rgba(44,110,158,0.1), rgba(212,175,55,0.1));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .stat-icon i { font-size: 18px; color: var(--primary); }

        .stat-number { font-size: 22px; font-weight: 800; color: var(--primary); margin-bottom: 2px; }
        .stat-label { color: var(--text-gray); font-size: 12px; font-weight: 500; }

        .stat-trend {
            font-size: 10px;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid var(--border);
        }

        .stat-trend.up { color: var(--success); }
        .stat-trend.down { color: var(--danger); }
        .stat-trend.stable { color: var(--warning); }

        .btn {
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 13px;
            padding: 8px 18px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #219a52;
            transform: translateY(-2px);
            color: white;
        }

        .btn-warning {
            background: var(--warning);
            color: white;
        }

        .btn-warning:hover {
            background: #d68910;
            transform: translateY(-2px);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
            color: white;
        }

        .btn-outline-danger {
            background: transparent;
            color: var(--danger);
            border: 2px solid var(--danger);
        }

        .btn-outline-danger:hover {
            background: var(--danger);
            color: white;
            transform: translateY(-2px);
        }

        .btn-sm { padding: 4px 12px; font-size: 11px; border-radius: 8px; }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 10px;
            display: inline-block;
        }

        .badge-warning { background: #fff3e0; color: var(--warning); }
        .badge-success { background: #e6f7ed; color: var(--success); }
        .badge-danger { background: #fee7e7; color: var(--danger); }
        .badge-info { background: #e8f4fd; color: var(--info); }
        .badge-primary { background: var(--primary); color: white; }

        .table {
            background: var(--bg-white);
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 0;
            width: 100%;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            font-weight: 600;
            border-bottom: none;
            padding: 10px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 8px 12px;
            vertical-align: middle;
            font-size: 12px;
            border-bottom: 1px solid var(--border);
            word-break: break-word;
        }

        .table tbody tr:hover { background: #f8fafc; }
        .table-responsive { border-radius: var(--radius); }

        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 13px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44,110,158,0.1);
            outline: none;
        }

        .form-label { font-weight: 600; color: var(--text-dark); margin-bottom: 4px; font-size: 12px; }

        .input-group-text {
            background: #f8fafc;
            border: 1.5px solid var(--border);
            border-right: none;
            color: var(--text-gray);
            font-size: 13px;
            border-radius: 10px 0 0 10px;
        }

        .input-group .form-control { border-radius: 0 10px 10px 0; }

        .alert {
            border: none;
            border-radius: var(--radius);
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .alert-success { background: #e6f7ed; color: var(--success); border-left: 4px solid var(--success); }
        .alert-danger { background: #fee7e7; color: var(--danger); border-left: 4px solid var(--danger); }
        .alert-warning { background: #fff3e0; color: var(--warning); border-left: 4px solid var(--warning); }
        .alert-info { background: #e8f4fd; color: var(--info); border-left: 4px solid var(--info); }

        .scroll-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 42px;
            height: 42px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
            border: none;
            box-shadow: var(--shadow-md);
        }

        .scroll-top.show { opacity: 1; visibility: visible; }
        .scroll-top:hover { background: var(--secondary); transform: translateY(-3px); color: white; }

        .footer {
            background: var(--bg-white);
            padding: 12px 0;
            margin-top: 20px;
            border-top: 1px solid var(--border);
            color: var(--text-gray);
            font-size: 11px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                top: 60px;
                transition: all 0.3s ease;
                z-index: 1040;
                width: 260px;
                max-height: calc(100vh - 60px);
                box-shadow: var(--shadow-lg);
                border-radius: 0 16px 16px 0;
            }

            .sidebar.show { left: 0; }

            .sidebar .nav-link { padding: 6px 14px; margin: 1px 8px; font-size: 12px; }
            .sidebar .sidebar-title { padding: 8px 14px 2px; font-size: 9px; }
            .sidebar .nav-link i { width: 20px; font-size: 13px; margin-right: 8px; }

            .navbar-brand { font-size: 0.95rem; }
            .navbar-brand i { font-size: 1.1rem; }
            .navbar-brand .brand-text { font-size: 0.8rem; }
            .navbar-brand small { font-size: 0.5rem; }

            .main-content { padding: 8px 8px 70px 8px; }

            .stat-number { font-size: 18px; }
            .stat-icon { width: 34px; height: 34px; }
            .stat-icon i { font-size: 15px; }
            .stat-card { padding: 10px 12px; }
            .stat-card .stat-label { font-size: 10px; }

            .card-header { padding: 10px 12px; font-size: 13px; }
            .card-body { padding: 10px; }

            .table thead th { font-size: 9px; padding: 6px 8px; white-space: nowrap; }
            .table tbody td { font-size: 11px; padding: 6px 8px; }

            .btn { font-size: 12px; padding: 6px 14px; }
            .btn-sm { font-size: 10px; padding: 3px 8px; }

            .badge { font-size: 9px; padding: 2px 8px; }

            .scroll-top { width: 36px; height: 36px; bottom: 15px; right: 15px; font-size: 14px; }
            .footer { font-size: 10px; padding: 8px 0; }

            .container-fluid { padding-left: 6px !important; padding-right: 6px !important; }
            .row.g-4 { --bs-gutter-y: 0.75rem; --bs-gutter-x: 0.75rem; }
        }

        @media print {
            .navbar, .sidebar, .footer, .no-print, .scroll-top, .sidebar-overlay { display: none !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; }
            .stat-card { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; }
            .stat-card::before { display: none; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <nav class="navbar">
        <div class="container-fluid px-3">
            <a class="navbar-brand" href="{{ route('infirmier.dashboard') }}">
                <i class="fas fa-hospital-user"></i>
                <div class="brand-text">
                    Hôpital Sendwe
                    <small>Lubumbashi - RDC | Infirmier</small>
                </div>
            </a>

            <button class="navbar-toggler" type="button" onclick="toggleSidebar()" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                <i class="fas fa-user-nurse"></i>
                            </div>
                            <span class="d-none d-md-inline">{{ auth()->user()->prenom ?? 'Infirmier' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2"></i> Mon profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('password.change') }}">
                                    <i class="fas fa-key me-2"></i> Changer mot de passe
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="container-fluid px-0">
        <div class="row g-0">
            <div class="col-md-3 col-lg-2 sidebar" id="sidebar">
                <div class="pt-2">
                    <div class="sidebar-title">NAVIGATION</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('infirmier.dashboard') ? 'active' : '' }}" 
                               href="{{ route('infirmier.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Tableau de bord
                            </a>
                        </li>
                    </ul>

                    <div class="sidebar-title mt-2">PATIENTS</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('infirmier.patients.attente') ? 'active' : '' }}" 
                               href="{{ route('infirmier.patients.attente') }}">
                                <i class="fas fa-clock"></i> En attente
                            </a>
                        </li>
                    </ul>


                    <hr class="my-2 mx-3">
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
                                @csrf
                                <button type="submit" class="nav-link text-danger" style="background:none; border:none; width:100%; text-align:left; cursor:pointer;">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-9 col-lg-10 ms-sm-auto main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid px-4">
            <p class="mb-0">&copy; {{ date('Y') }} Hôpital Sendwe - Lubumbashi, République Démocratique du Congo</p>
        </div>
    </footer>

    <button class="scroll-top" id="scrollTop" title="Retour en haut">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        AOS.init({ duration: 600, once: true });

        const scrollTop = document.getElementById('scrollTop');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) scrollTop.classList.add('show');
            else scrollTop.classList.remove('show');
        });
        scrollTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('show');
            document.getElementById('sidebarOverlay').classList.remove('show');
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) closeSidebar();
        });

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggler = document.querySelector('.navbar-toggler');
            if (window.innerWidth <= 768 && !sidebar.contains(event.target) && !toggler.contains(event.target)) {
                closeSidebar();
            }
        });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // ✅ CONFIRMATION DE DÉCONNEXION
        document.querySelectorAll('#logout-form, #sidebar-logout-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                    e.preventDefault();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>