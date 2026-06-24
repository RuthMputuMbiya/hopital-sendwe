<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Médecin - Hôpital Sendwe | @yield('title', 'Tableau de bord')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Bootstrap 5 (pour compatibilité) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <style>
        /* ========================================== */
        /* RESET & BASE - THÈME MÉDECIN */
        /* ========================================== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%); 
            min-height: 100vh; 
        }

        /* ========================================== */
        /* SIDEBAR - THÈME BLEU MÉDICAL */
        /* ========================================== */
        .medecin-sidebar {
            position: fixed; 
            left: 0; 
            top: 0; 
            width: 280px; 
            height: 100vh;
            background: linear-gradient(180deg, #1a4d8c 0%, #0e3570 100%);
            color: white; 
            transition: all 0.3s ease; 
            z-index: 1000; 
            overflow-y: auto;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }
        
        /* Scrollbar personnalisé */
        .medecin-sidebar::-webkit-scrollbar { width: 5px; }
        .medecin-sidebar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 10px; }
        .medecin-sidebar::-webkit-scrollbar-thumb { background: #d4af37; border-radius: 10px; }
        
        /* Header Sidebar */
        .sidebar-header { 
            padding: 25px 20px; 
            text-align: center; 
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-logo { font-size: 22px; font-weight: 800; }
        .sidebar-logo span { color: #d4af37; }
        .sidebar-logo small { 
            font-size: 10px; 
            display: block; 
            color: rgba(255,255,255,0.5); 
            margin-top: 6px; 
        }
        
        /* Profil utilisateur */
        .sidebar-user { 
            padding: 20px; 
            text-align: center; 
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .user-avatar { 
            width: 65px; 
            height: 65px; 
            background: linear-gradient(135deg, #d4af37 0%, #b8960c 100%); 
            border-radius: 20px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0 auto 12px; 
            font-size: 26px; 
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .user-name { font-weight: 600; margin-bottom: 4px; font-size: 15px; }
        .user-role { 
            font-size: 11px; 
            color: rgba(255,255,255,0.6); 
            text-transform: uppercase; 
            letter-spacing: 1px;
        }
        
        /* Navigation */
        .sidebar-nav { padding: 20px 15px; }
        .nav-title { 
            font-size: 10px; 
            text-transform: uppercase; 
            letter-spacing: 1.5px; 
            color: rgba(255,255,255,0.4); 
            padding: 0 15px; 
            margin-bottom: 15px; 
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .nav-title i { font-size: 6px; color: #d4af37; }
        .sidebar-nav .nav-link { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 12px 18px; 
            color: rgba(255,255,255,0.8); 
            text-decoration: none; 
            border-radius: 12px; 
            margin-bottom: 6px; 
            transition: all 0.2s; 
            font-size: 14px; 
            font-weight: 500; 
        }
        .sidebar-nav .nav-link i { width: 22px; text-align: center; font-size: 16px; }
        .sidebar-nav .nav-link:hover { 
            background: rgba(255,255,255,0.1); 
            transform: translateX(3px); 
            color: white;
        }
        .sidebar-nav .nav-link.active { 
            background: linear-gradient(135deg, #d4af37 0%, #b8960c 100%); 
            color: #1a4d8c; 
            box-shadow: 0 4px 12px rgba(212,175,55,0.3);
        }
        .sidebar-nav .nav-link.active i { color: #1a4d8c; }
        
        /* Footer Sidebar */
        .sidebar-footer { 
            position: absolute; 
            bottom: 0; 
            left: 0; 
            right: 0; 
            padding: 20px; 
            border-top: 1px solid rgba(255,255,255,0.1); 
            background: rgba(0,0,0,0.2);
        }
        .logout-btn { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 12px 18px; 
            color: rgba(255,255,255,0.8); 
            text-decoration: none; 
            border-radius: 12px; 
            background: none; 
            border: none; 
            width: 100%; 
            cursor: pointer; 
            font-size: 14px; 
            font-weight: 500;
            transition: all 0.2s;
        }
        .logout-btn:hover { 
            background: rgba(239,68,68,0.8); 
            color: white; 
            transform: translateX(3px);
        }

        /* ========================================== */
        /* MAIN CONTENT */
        /* ========================================== */
        .medecin-main { 
            margin-left: 280px; 
            padding: 20px; 
            min-height: 100vh; 
            transition: all 0.3s; 
        }
        
        /* Top Bar */
        .top-bar { 
            background: white; 
            border-radius: 20px; 
            padding: 16px 25px; 
            margin-bottom: 25px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.03);
        }
        .page-title h2 { 
            font-size: 18px; 
            font-weight: 700; 
            margin: 0; 
            color: #1e293b; 
        }
        .page-title p { 
            font-size: 12px; 
            margin: 4px 0 0 0; 
            color: #64748b; 
        }
        .date-badge { 
            background: #f8fafc; 
            padding: 8px 18px; 
            border-radius: 40px; 
            font-size: 13px; 
            font-weight: 500; 
            border: 1px solid #e2e8f0;
        }
        .date-badge i { color: #d4af37; margin-right: 8px; }

        /* Alert Messages */
        .alert-custom { 
            border-radius: 16px; 
            padding: 15px 20px; 
            margin-bottom: 25px; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            animation: slideIn 0.3s ease; 
        }
        @keyframes slideIn { 
            from { transform: translateY(-20px); opacity: 0; } 
            to { transform: translateY(0); opacity: 1; } 
        }
        .alert-success { 
            background: #ecfdf5; 
            border-left: 4px solid #10b981; 
            color: #065f46; 
        }
        .alert-error { 
            background: #fef2f2; 
            border-left: 4px solid #ef4444; 
            color: #991b1b; 
        }
        .alert-custom button { 
            background: transparent; 
            border: none; 
            font-size: 20px; 
            cursor: pointer; 
            margin-left: auto;
            opacity: 0.6;
            transition: opacity 0.2s;
        }
        .alert-custom button:hover { opacity: 1; }

        /* Menu Mobile Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: #1a4d8c;
            border: none;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 12px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* ========================================== */
        /* STAT CARDS */
        /* ========================================== */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #1a4d8c, #d4af37);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgba(26,77,140,0.1), rgba(212,175,55,0.1));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .stat-icon i { font-size: 24px; color: #1a4d8c; }
        .stat-number { font-size: 28px; font-weight: 800; color: #1a4d8c; margin-bottom: 5px; }
        .stat-label { color: #64748b; font-size: 13px; font-weight: 500; }
        .stat-trend { font-size: 11px; margin-top: 8px; padding-top: 8px; border-top: 1px solid #e2e8f0; }
        .stat-trend.up { color: #10b981; }
        .stat-trend.down { color: #ef4444; }
        .stat-trend.stable { color: #f59e0b; }

        /* ========================================== */
        /* CARDS GÉNÉRAUX */
        /* ========================================== */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 24px;
            font-weight: 600;
            font-size: 16px;
            color: #1a4d8c;
        }
        .card-body { padding: 24px; }

        /* ========================================== */
        /* TABLES */
        /* ========================================== */
        .table thead th {
            background: linear-gradient(135deg, #1a4d8c, #2c6e9e);
            color: white;
            font-weight: 600;
            border-bottom: none;
            padding: 15px;
            font-size: 13px;
        }
        .table tbody tr:hover { background: #eaf2f8; }

        /* ========================================== */
        /* BOUTONS */
        /* ========================================== */
        .btn-primary {
            background: linear-gradient(135deg, #1a4d8c, #2c6e9e);
            border: none;
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0e3570, #1a4d8c);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26,77,140,0.3);
        }
        .btn-outline-primary {
            border: 2px solid #1a4d8c;
            color: #1a4d8c;
            border-radius: 12px;
            padding: 8px 22px;
            font-weight: 500;
        }
        .btn-outline-primary:hover {
            background: #1a4d8c;
            color: white;
        }

        /* ========================================== */
        /* BADGES */
        /* ========================================== */
        .badge { padding: 5px 12px; border-radius: 30px; font-weight: 500; font-size: 11px; }
        .badge-success { background: #ecfdf5; color: #10b981; }
        .badge-danger { background: #fef2f2; color: #ef4444; }
        .badge-warning { background: #fffbeb; color: #f59e0b; }
        .badge-info { background: #e0f2fe; color: #0ea5e9; }

        /* ========================================== */
        /* RESPONSIVE */
        /* ========================================== */
        @media (max-width: 768px) {
            .mobile-menu-toggle { display: flex; align-items: center; justify-content: center; }
            .medecin-sidebar { transform: translateX(-100%); }
            .medecin-sidebar.mobile-open { transform: translateX(0); }
            .medecin-main { margin-left: 0; padding-top: 70px; }
            .top-bar { flex-direction: column; gap: 15px; text-align: center; }
        }
    </style>

    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }">

    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" @click="sidebarOpen = !sidebarOpen">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="medecin-sidebar" :class="{'mobile-open': sidebarOpen}">
        <div class="sidebar-header">
            <div class="sidebar-logo">Hôpital <span>Sendwe</span><small>Lubumbashi - RDC</small></div>
        </div>
        
        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="user-name">{{ Auth::user()->prenom ?? 'Dr' }} {{ Auth::user()->nom ?? 'Médecin' }}</div>
            <div class="user-role">Médecin</div>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-title">
                <i class="fas fa-circle"></i> Navigation principale
            </div>
            <a href="{{ route('medecin.dashboard') }}" class="nav-link {{ request()->routeIs('medecin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Tableau de bord
            </a>
            <a href="{{ route('medecin.patients.index') }}" class="nav-link {{ request()->routeIs('medecin.patients.*') ? 'active' : '' }}">
                <i class="fas fa-notes-medical"></i> Mes patients
            </a>
            <a href="{{ route('medecin.consultations.create') }}" class="nav-link {{ request()->routeIs('medecin.consultations.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i> Nouvelle consultation
            </a>
            
            <div class="nav-title mt-3">
                <i class="fas fa-circle"></i> Gestion médicale
            </div>
            <a href="{{ route('medecin.consultations.index') }}" class="nav-link {{ request()->routeIs('medecin.consultations.*') ? 'active' : '' }}">
                <i class="fas fa-stethoscope"></i> Consultations
            </a>
            <a href="{{ route('medecin.maladies.index') }}" class="nav-link {{ request()->routeIs('medecin.maladies.*') ? 'active' : '' }}">
                <i class="fas fa-disease"></i> Maladies
    
        </div>
        
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="medecin-main" @click="sidebarOpen = false" @click.away="sidebarOpen = false">
        <div class="top-bar">
            <div class="page-title">
                <h2>@yield('page-title', 'Tableau de bord')</h2>
                <p>@yield('page-subtitle', 'Espace Médecin - Hôpital Sendwe')</p>
            </div>
            <div class="date-badge">
                <i class="fas fa-calendar-alt"></i> {{ now()->translatedFormat('l d F Y') }}
            </div>
        </div>

        @if(session('success'))
        <div class="alert-custom alert-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button @click="show = false">&times;</button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert-custom alert-error" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button @click="show = false">&times;</button>
        </div>
        @endif

        @yield('content')
    </div>

    <script>
        // Initialisation AOS
        AOS.init({ duration: 800, once: true });
        
        // Fermer le sidebar mobile lors du clic sur un lien
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    document.querySelector('.medecin-sidebar')?.classList.remove('mobile-open');
                }
            });
        });
        
        // DataTables et Select2
        $(document).ready(function() {
            $('.select2').select2({ theme: 'bootstrap5', width: '100%' });
            $('.datatable').each(function() {
                $(this).DataTable({
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json' },
                    pageLength: 10,
                    responsive: true
                });
            });
        });
        
        // Scroll to top
        window.addEventListener('scroll', () => {
            const scrollTop = document.querySelector('.scroll-top');
            if (scrollTop) {
                if (window.scrollY > 300) scrollTop.classList.add('show');
                else scrollTop.classList.remove('show');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>