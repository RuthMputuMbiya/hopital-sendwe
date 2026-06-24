<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Réceptionniste - Hôpital Sendwe | @yield('title')</title>
    
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
            --primary: #2c6e9e;
            --primary-light: #3d8fc4;
            --primary-dark: #1a4d8c;
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
            min-height: 60px;
        }

        .navbar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-brand i {
            font-size: 1.3rem;
        }

        .navbar-brand .brand-text {
            line-height: 1.2;
        }

        .navbar-brand small {
            font-size: 0.55rem;
            opacity: 0.85;
            display: block;
            font-weight: 400;
            letter-spacing: 0.3px;
        }

        /* === BOUTON BURGER MOBILE === */
        .navbar-toggler {
            border: none;
            padding: 6px 10px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .navbar-toggler:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            width: 24px;
            height: 24px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-lg);
            border-radius: 16px;
            margin-top: 6px;
            padding: 6px;
            min-width: 180px;
        }

        .dropdown-item {
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            transition: all 0.3s ease;
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
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 6px;
            font-size: 14px;
            color: white;
        }

        /* === SIDEBAR === */
        .sidebar {
            background: var(--bg-white);
            min-height: calc(100vh - 60px);
            box-shadow: var(--shadow);
            position: sticky;
            top: 60px;
            border-right: 1px solid var(--border);
            padding: 8px 0;
            overflow-y: auto;
            max-height: calc(100vh - 60px);
            transition: all 0.3s ease;
            z-index: 1040;
        }

        /* Overlay pour mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1035;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
        }

        .sidebar .nav-link {
            color: var(--text-gray);
            padding: 10px 18px;
            border-radius: 12px;
            margin: 3px 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 13px;
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

        .sidebar .nav-link i {
            width: 22px;
            margin-right: 10px;
            font-size: 15px;
        }

        .sidebar-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--secondary-dark);
            padding: 12px 16px 4px;
            font-weight: 700;
        }

        /* ============================================================
                   CARDS
                   ============================================================ */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            background: var(--bg-white);
            margin-bottom: 16px;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            font-weight: 600;
            font-size: 15px;
            color: var(--primary);
            border-radius: 20px 20px 0 0 !important;
        }

        .card-body {
            padding: 20px;
        }

        /* ============================================================
                   STAT CARDS
                   ============================================================ */
        .stat-card {
            background: var(--bg-white);
            border-radius: 20px;
            padding: 16px;
            box-shadow: var(--shadow);
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

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, rgba(44, 110, 158, 0.1), rgba(212, 175, 55, 0.1));
            border-radius: 14px;
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
            margin-bottom: 4px;
        }

        .stat-label {
            color: var(--text-gray);
            font-size: 12px;
            font-weight: 500;
        }

        .stat-trend {
            font-size: 10px;
            margin-top: 6px;
            padding-top: 6px;
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
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
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
            padding: 8px 22px;
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
            border-radius: 16px;
            overflow: hidden;
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
        }

        .table tbody tr:hover {
            background: #eaf2f8;
        }

        .table-responsive {
            border-radius: 16px;
        }

        /* ============================================================
                   FORMS
                   ============================================================ */
        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-size: 13px;
        }

        .input-group-text {
            background: #f8fafc;
            border: 1.5px solid var(--border);
            border-right: none;
            color: var(--text-gray);
            border-radius: 12px 0 0 12px;
        }

        .input-group .form-control {
            border-radius: 0 12px 12px 0;
        }

        /* ============================================================
                   ALERTS
                   ============================================================ */
        .alert {
            border: none;
            border-radius: 14px;
            padding: 14px 20px;
            margin-bottom: 16px;
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
                   BADGES
                   ============================================================ */
        .badge {
            padding: 5px 12px;
            border-radius: 30px;
            font-weight: 500;
            font-size: 11px;
        }

        .badge-success { background: #e6f7ed; color: var(--success); }
        .badge-danger { background: #fee7e7; color: var(--danger); }
        .badge-warning { background: #fff3e0; color: var(--warning); }
        .badge-info { background: #e8f4fd; color: var(--info); }
        .badge-primary { background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; }

        /* ============================================================
                   PAGINATION
                   ============================================================ */
        .pagination .page-link {
            border: none;
            color: var(--text-gray);
            border-radius: 10px;
            margin: 0 3px;
            font-size: 13px;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary);
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
            transition: all 0.3s ease;
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
                   RESPONSIVE - MOBILE
                   ============================================================ */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1rem;
            }

            .navbar-brand i {
                font-size: 1.1rem;
            }

            .navbar-brand .brand-text {
                font-size: 0.8rem;
            }

            .navbar-brand small {
                font-size: 0.5rem;
            }

            .navbar {
                padding: 6px 0;
                min-height: 52px;
            }

            .sidebar {
                position: fixed;
                left: -280px;
                top: 52px;
                transition: all 0.3s ease;
                z-index: 1040;
                width: 270px;
                max-height: calc(100vh - 52px);
                box-shadow: var(--shadow-lg);
                border-radius: 0 16px 16px 0;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar .nav-link {
                padding: 8px 14px;
                margin: 2px 8px;
                font-size: 12px;
            }

            .sidebar .sidebar-title {
                padding: 10px 14px 2px;
                font-size: 9px;
            }

            .sidebar .nav-link i {
                width: 20px;
                font-size: 13px;
                margin-right: 8px;
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

            .stat-card {
                padding: 12px 14px;
            }

            .stat-card .stat-label {
                font-size: 11px;
            }

            .stat-card .stat-trend {
                font-size: 9px;
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

            .btn-sm {
                padding: 4px 10px;
                font-size: 11px;
            }

            .badge {
                font-size: 10px;
                padding: 3px 10px;
            }

            .container-fluid {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .alert {
                font-size: 12px;
                padding: 10px 14px;
            }

            .dropdown-menu {
                min-width: 160px;
                padding: 4px;
            }

            .dropdown-item {
                font-size: 12px;
                padding: 6px 12px;
            }

            .user-avatar {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .scroll-top {
                width: 38px;
                height: 38px;
                bottom: 20px;
                right: 20px;
                font-size: 14px;
            }

            .footer {
                font-size: 10px;
                padding: 10px 0;
            }

            .form-control, .form-select {
                font-size: 13px;
                padding: 8px 12px;
            }

            .form-label {
                font-size: 12px;
            }

            .row.g-4 {
                --bs-gutter-y: 0.75rem;
                --bs-gutter-x: 0.75rem;
            }

            .btn {
                font-size: 12px;
                padding: 8px 16px;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 0.85rem;
            }

            .navbar-brand i {
                font-size: 1rem;
            }

            .navbar-brand .brand-text {
                font-size: 0.7rem;
            }

            .stat-number {
                font-size: 17px;
            }

            .sidebar {
                width: 250px;
                left: -260px;
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
            .navbar, .sidebar, .footer, .no-print, .scroll-top, .sidebar-overlay {
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
            <a class="navbar-brand" href="{{ route('receptionniste.dashboard') }}">
                <i class="fas fa-hospital-user"></i>
                <div class="brand-text">
                    Hôpital Sendwe
                    <small>Lubumbashi - RDC | Accueil & Réception</small>
                </div>
            </a>

            <!-- === BOUTON BURGER MOBILE === -->
            <button class="navbar-toggler" type="button" 
                    onclick="toggleSidebar()"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu desktop -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                <i class="fas fa-user-friends"></i>
                            </div>
                            <span class="d-none d-md-inline">{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2"></i> Mon profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('receptionniste.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
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
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- ============================================================
    MAIN CONTENT
    ============================================================ -->
    <div class="container-fluid px-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar" id="sidebar">
                <div class="pt-2">
                    <!-- NAVIGATION PRINCIPALE -->
                    <div class="sidebar-title">NAVIGATION PRINCIPALE</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('receptionniste.dashboard') ? 'active' : '' }}" 
                               href="{{ route('receptionniste.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Tableau de bord
                            </a>
                        </li>
                    </ul>

                    <!-- GESTION DES PATIENTS -->
                    <div class="sidebar-title mt-2">GESTION DES PATIENTS</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('receptionniste.patients.index') ? 'active' : '' }}" 
                               href="{{ route('receptionniste.patients.index') }}">
                                <i class="fas fa-notes-medical"></i> Tous les patients
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('receptionniste.patients.create') ? 'active' : '' }}" 
                               href="{{ route('receptionniste.patients.create') }}">
                                <i class="fas fa-user-plus"></i> Nouveau patient
                            </a>
                        </li>
                    </ul>

                    <!-- RAPPORTS -->
                    <div class="sidebar-title mt-2">RAPPORTS</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('receptionniste.rapports.index') ? 'active' : '' }}" 
                               href="{{ route('receptionniste.rapports.index') }}">
                                <i class="fas fa-chart-line"></i> Mes rapports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('receptionniste.rapports.historique') ? 'active' : '' }}" 
                               href="{{ route('receptionniste.rapports.historique') }}">
                                <i class="fas fa-history"></i> Mon historique
                            </a>
                        </li>
                    </ul>

                    <!-- ENREGISTREMENTS -->
                    <div class="sidebar-title mt-2">ENREGISTREMENTS</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('receptionniste.enregistrements.*') ? 'active' : '' }}" 
                               href="{{ route('receptionniste.enregistrements.index') }}">
                                <i class="fas fa-archive"></i> Tous les enregistrements
                            </a>
                        </li>
                    </ul>

                    <!-- EXAMENS -->
                    <div class="sidebar-title mt-2">EXAMENS</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('receptionniste.examens.*') ? 'active' : '' }}" 
                               href="{{ route('receptionniste.examens.index') }}">
                                <i class="fas fa-microscope"></i> Examens
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-3 px-md-4 py-3">
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

                @yield('content')
            </div>
        </div>
    </div>

    <!-- ============================================================
    FOOTER
    ============================================================ -->
    <footer class="footer">
        <div class="container-fluid px-4">
            <p class="mb-0">&copy; {{ date('Y') }} Hôpital Sendwe - Lubumbashi, République Démocratique du Congo</p>
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
                        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json' },
                        pageLength: 10,
                        responsive: true
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

        // ===== SIDEBAR TOGGLE =====
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('show');
            document.getElementById('sidebarOverlay').classList.remove('show');
        }

        // ===== Fermer sidebar sur click extérieur =====
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggler = document.querySelector('.navbar-toggler');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggler.contains(event.target)) {
                    closeSidebar();
                }
            }
        });

        // ===== Fermer sidebar sur redimensionnement =====
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });

        // ===== Tooltips =====
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>

    @stack('scripts')
</body>
</html>