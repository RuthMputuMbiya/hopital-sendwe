<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestionnaire - Hôpital Sendwe | @yield('title')</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1a4d8c;
            --primary-light: #2c6e9e;
            --primary-dark: #0e3570;
            --secondary: #d4af37;
            --secondary-light: #e8c06a;
            --secondary-dark: #b8960c;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #3498db;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --text-light: #94a3b8;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
            --radius: 16px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2f5 100%);
            color: var(--text-dark);
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* ============================================================
                   NAVIGATION BAR
                   ============================================================ */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 10px 0;
            position: sticky;
            top: 0;
            z-index: 1050;
            box-shadow: var(--shadow-md);
            min-height: 64px;
        }

        .navbar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand i {
            font-size: 1.6rem;
        }

        .navbar-brand small {
            font-size: 0.65rem;
            opacity: 0.8;
            display: block;
            font-weight: 400;
            letter-spacing: 0.5px;
        }

        .navbar-brand .brand-text {
            line-height: 1.2;
        }

        /* Bouton burger mobile */
        .navbar-toggler {
            border: none;
            padding: 8px 10px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            transition: var(--transition);
        }

        .navbar-toggler:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            transition: var(--transition);
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 10px;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-lg);
            border-radius: var(--radius);
            margin-top: 8px;
            padding: 8px;
        }

        .dropdown-item {
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 14px;
            transition: var(--transition);
        }

        .dropdown-item:hover {
            background: #eaf2f8;
            color: var(--primary);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            font-size: 16px;
            color: white;
        }

        /* ============================================================
                   SIDEBAR
                   ============================================================ */
        .sidebar {
            background: var(--bg-white);
            min-height: calc(100vh - 64px);
            box-shadow: var(--shadow);
            position: sticky;
            top: 64px;
            border-right: 1px solid var(--border);
            padding: 12px 0;
            overflow-y: auto;
            max-height: calc(100vh - 64px);
        }

        .sidebar .sidebar-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--secondary-dark);
            padding: 15px 20px 5px;
            font-weight: 700;
        }

        .sidebar .nav-link {
            color: var(--text-gray);
            padding: 10px 20px;
            border-radius: 12px;
            margin: 3px 12px;
            transition: var(--transition);
            font-weight: 500;
            font-size: 14px;
        }

        .sidebar .nav-link:hover {
            background: #eaf2f8;
            color: var(--primary);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .sidebar .nav-link.active i {
            color: white;
        }

        .sidebar .nav-link i {
            width: 24px;
            margin-right: 12px;
            font-size: 16px;
            color: var(--text-light);
            transition: var(--transition);
        }

        .sidebar .nav-link:hover i,
        .sidebar .nav-link.active i {
            color: white;
        }

        /* Sidebar mobile */
        .sidebar-mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .sidebar-mobile-overlay.show {
            display: block;
        }

        /* ============================================================
                   CARDS
                   ============================================================ */
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
            background: var(--bg-white);
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            font-weight: 600;
            font-size: 15px;
            color: var(--primary);
            border-radius: var(--radius) var(--radius) 0 0 !important;
        }

        .card-body {
            padding: 20px;
        }

        /* ============================================================
                   STAT CARDS
                   ============================================================ */
        .stat-card {
            background: var(--bg-white);
            border-radius: var(--radius);
            padding: 18px 20px;
            box-shadow: var(--shadow);
            transition: var(--transition);
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

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, rgba(26, 77, 140, 0.1), rgba(212, 175, 55, 0.1));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .stat-icon i {
            font-size: 22px;
            color: var(--primary);
        }

        .stat-number {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 2px;
        }

        .stat-label {
            color: var(--text-gray);
            font-size: 13px;
            font-weight: 500;
        }

        .stat-trend {
            font-size: 11px;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid var(--border);
        }

        .stat-trend.up { color: var(--success); }
        .stat-trend.down { color: var(--danger); }
        .stat-trend.stable { color: var(--warning); }

        /* ============================================================
                   BOUTONS
                   ============================================================ */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            border-radius: 12px;
            padding: 9px 22px;
            font-weight: 500;
            transition: var(--transition);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            color: white;
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 12px;
            padding: 7px 20px;
            font-weight: 500;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        .btn-sm {
            padding: 5px 14px;
            font-size: 12px;
            border-radius: 10px;
        }

        /* ============================================================
                   TABLES
                   ============================================================ */
        .table {
            background: var(--bg-white);
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            font-weight: 600;
            border-bottom: none;
            padding: 12px 15px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 10px 15px;
            vertical-align: middle;
            font-size: 13px;
            border-bottom: 1px solid var(--border);
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .table-responsive {
            border-radius: var(--radius);
        }

        /* ============================================================
                   BADGES
                   ============================================================ */
        .badge {
            padding: 4px 12px;
            border-radius: 30px;
            font-weight: 500;
            font-size: 11px;
        }

        .badge-success { background: #e6f7ed; color: var(--success); }
        .badge-danger { background: #fee7e7; color: var(--danger); }
        .badge-warning { background: #fff3e0; color: var(--warning); }
        .badge-info { background: #e8f4fd; color: var(--info); }
        .badge-primary { background: var(--primary); color: white; }

        /* ============================================================
                   ALERTS
                   ============================================================ */
        .alert {
            border: none;
            border-radius: var(--radius);
            padding: 14px 20px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #e6f7ed;
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: #fee7e7;
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .alert-warning {
            background: #fff3e0;
            color: var(--warning);
            border-left: 4px solid var(--warning);
        }

        /* ============================================================
                   PROGRESS
                   ============================================================ */
        .progress {
            background: #e9ecef;
            border-radius: 10px;
            height: 6px;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 10px;
        }

        /* ============================================================
                   SCROLL TOP
                   ============================================================ */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 45px;
            height: 45px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 999;
            border: none;
            box-shadow: var(--shadow-md);
        }

        .scroll-top.show {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            color: white;
        }

        /* ============================================================
                   FOOTER
                   ============================================================ */
        .footer {
            background: var(--bg-white);
            padding: 16px 0;
            margin-top: 30px;
            border-top: 1px solid var(--border);
            color: var(--text-gray);
            font-size: 12px;
            text-align: center;
        }

        /* ============================================================
                   RESPONSIVE - MOBILE
                   ============================================================ */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1rem;
            }

            .navbar-brand i {
                font-size: 1.2rem;
            }

            .navbar-brand .brand-text {
                font-size: 0.85rem;
            }

            .navbar-brand small {
                font-size: 0.55rem;
            }

            .sidebar {
                position: fixed;
                left: -300px;
                top: 64px;
                transition: var(--transition);
                z-index: 1045;
                width: 280px;
                max-height: calc(100vh - 64px);
                box-shadow: var(--shadow-lg);
                border-radius: 0 16px 16px 0;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar .nav-link {
                padding: 10px 16px;
                margin: 2px 8px;
                font-size: 13px;
            }

            .sidebar .sidebar-title {
                padding: 12px 16px 4px;
                font-size: 10px;
            }

            .stat-number {
                font-size: 20px;
            }

            .stat-icon {
                width: 38px;
                height: 38px;
            }

            .stat-icon i {
                font-size: 18px;
            }

            .card-header {
                padding: 12px 16px;
                font-size: 14px;
            }

            .card-body {
                padding: 14px;
            }

            .table thead th {
                font-size: 10px;
                padding: 8px 10px;
            }

            .table tbody td {
                font-size: 12px;
                padding: 8px 10px;
            }

            .table-responsive {
                border-radius: 12px;
            }

            .btn-sm {
                padding: 4px 10px;
                font-size: 11px;
            }

            .scroll-top {
                width: 40px;
                height: 40px;
                bottom: 20px;
                right: 20px;
            }

            .badge {
                font-size: 10px;
                padding: 3px 10px;
            }

            .container-fluid {
                padding-left: 10px !important;
                padding-right: 10px !important;
            }

            .alert {
                padding: 10px 14px;
                font-size: 13px;
            }

            .dropdown-menu {
                background: var(--bg-white);
            }

            .nav-link {
                padding: 6px 12px;
                font-size: 13px;
            }

            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 13px;
            }

            .stat-card {
                padding: 14px 16px;
            }

            .stat-card .stat-label {
                font-size: 12px;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 0.9rem;
            }

            .navbar-brand i {
                font-size: 1rem;
            }

            .navbar-brand .brand-text {
                font-size: 0.75rem;
            }

            .stat-number {
                font-size: 18px;
            }

            .sidebar {
                width: 260px;
                left: -280px;
            }

            .stat-card .stat-trend {
                font-size: 10px;
            }

            .table thead th {
                font-size: 9px;
                padding: 6px 8px;
            }

            .table tbody td {
                font-size: 11px;
                padding: 6px 8px;
            }

            .card-header {
                font-size: 13px;
                padding: 10px 12px;
            }

            .card-body {
                padding: 10px;
            }
        }

        /* ============================================================
                   PRINT
                   ============================================================ */
        @media print {
            .navbar, .sidebar, .footer, .no-print, .scroll-top {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                break-inside: avoid;
            }

            .stat-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                break-inside: avoid;
            }

            .stat-card::before {
                display: none;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- ============================================================
    NAVIGATION
    ============================================================ -->
    <nav class="navbar">
        <div class="container-fluid px-3 px-md-4">
            <a class="navbar-brand" href="{{ route('gestionnaire.dashboard') }}">
                <i class="fas fa-hospital-user"></i>
                <div class="brand-text">
                    Hôpital Sendwe
                    <small>Lubumbashi - RDC | Gestionnaire</small>
                </div>
            </a>

            <!-- === BOUTON BURGER MOBILE === -->
            <button class="navbar-toggler" type="button" 
                    onclick="document.getElementById('sidebar').classList.toggle('show');
                             document.getElementById('sidebarOverlay').classList.toggle('show')"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu desktop -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- Notifications -->
                    <li class="nav-item me-2">
                        <a class="nav-link" href="#" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="badge badge-danger" style="position: relative; top: -8px; font-size: 9px;">3</span>
                        </a>
                    </li>

                    <!-- Profil -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <span class="d-none d-md-inline">{{ auth()->user()->prenom ?? 'Admin' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2"></i> Mon profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog me-2"></i> Paramètres
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
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

    <!-- Overlay pour fermer le sidebar sur mobile -->
    <div class="sidebar-mobile-overlay" id="sidebarOverlay" 
         onclick="document.getElementById('sidebar').classList.remove('show');
                  this.classList.remove('show')">
    </div>

    <!-- ============================================================
    MAIN CONTENT
    ============================================================ -->
    <div class="container-fluid px-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar" id="sidebar">
                <div class="pt-2">
                    <div class="sidebar-title">NAVIGATION</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gestionnaire.dashboard') ? 'active' : '' }}" 
                               href="{{ route('gestionnaire.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Tableau de bord
                            </a>
                        </li>
                    </ul>

                    <div class="sidebar-title mt-2">UTILISATEURS</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gestionnaire.users.index') ? 'active' : '' }}" 
                               href="{{ route('gestionnaire.users.index') }}">
                                <i class="fas fa-users"></i> Tous les utilisateurs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gestionnaire.users.create') ? 'active' : '' }}" 
                               href="{{ route('gestionnaire.users.create') }}">
                                <i class="fas fa-user-plus"></i> Ajouter
                            </a>
                        </li>
                    </ul>

                    <div class="sidebar-title mt-2">RÔLES</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gestionnaire.roles.*') ? 'active' : '' }}" 
                               href="{{ route('gestionnaire.roles.index') }}">
                                <i class="fas fa-tags"></i> Rôles & permissions
                            </a>
                        </li>
                    </ul>

                    <div class="sidebar-title mt-2">STATISTIQUES</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gestionnaire.statistiques.*') ? 'active' : '' }}" 
                               href="{{ route('gestionnaire.statistiques.index') }}">
                                <i class="fas fa-chart-bar"></i> Statistiques
                            </a>
                        </li>
                    </ul>

                    <div class="sidebar-title mt-2">RAPPORTS</div>
                    <ul class="nav flex-column">
                       
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-3 px-md-4 py-3">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- ============================================================
    FOOTER
    ============================================================ -->
    <footer class="footer">
        <div class="container-fluid px-4">
            <p class="mb-0">
                &copy; {{ date('Y') }} Hôpital Sendwe - Lubumbashi, République Démocratique du Congo
                <span class="d-none d-md-inline">| Version 2.0</span>
            </p>
        </div>
    </footer>

    <!-- ============================================================
    SCROLL TOP
    ============================================================ -->
    <button class="scroll-top" id="scrollTop" title="Retour en haut">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- ============================================================
    SCRIPTS
    ============================================================ -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // ===== AOS =====
        AOS.init({ duration: 800, once: true });

        // ===== DataTables =====
        $(document).ready(function() {
            $('.select2').select2({ theme: 'bootstrap5', width: '100%' });

            $('.datatable').each(function() {
                var $table = $(this);
                var headerCount = $table.find('thead th').length;
                var bodyCount = $table.find('tbody tr:first td').length;

                if (headerCount > 0 && bodyCount > 0 && headerCount === bodyCount) {
                    $(this).DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
                        },
                        pageLength: 10,
                        responsive: true,
                        autoWidth: false
                    });
                }
            });
        });

        // ===== Scroll Top =====
        const scrollTop = document.getElementById('scrollTop');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollTop.classList.add('show');
            } else {
                scrollTop.classList.remove('show');
            }
        });

        scrollTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // ===== Fermer sidebar sur click extérieur =====
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggler = document.querySelector('.navbar-toggler');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggler.contains(event.target)) {
                    sidebar.classList.remove('show');
                    document.getElementById('sidebarOverlay').classList.remove('show');
                }
            }
        });

        // ===== Tooltips =====
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // ===== Gestion du redimensionnement =====
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.getElementById('sidebar').classList.remove('show');
                document.getElementById('sidebarOverlay').classList.remove('show');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>