<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laborantin - Hôpital Sendwe | @yield('title', 'Tableau de bord')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            background: #f0f2f5; 
            min-height: 100vh; 
        }

        .laborantin-sidebar {
            position: fixed; 
            left: 0; 
            top: 0; 
            width: 280px; 
            height: 100vh;
            background: linear-gradient(180deg, #0f2b3d 0%, #1a4d6b 100%);
            color: white; 
            transition: all 0.3s ease; 
            z-index: 1000; 
            overflow-y: auto;
            box-shadow: 2px 0 20px rgba(0,0,0,0.08);
        }
        
        .laborantin-sidebar::-webkit-scrollbar { width: 5px; }
        .laborantin-sidebar::-webkit-scrollbar-track { background: rgba(255,255,255,0.1); }
        .laborantin-sidebar::-webkit-scrollbar-thumb { background: #00bcd4; border-radius: 10px; }
        
        .sidebar-header { 
            padding: 30px 20px; 
            text-align: center; 
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-logo { font-size: 22px; font-weight: 700; letter-spacing: 0.5px; }
        .sidebar-logo span { color: #00bcd4; }
        .sidebar-logo small { 
            font-size: 10px; 
            display: block; 
            color: rgba(255,255,255,0.5); 
            margin-top: 8px; 
        }
        
        .sidebar-user { 
            padding: 25px 20px; 
            text-align: center; 
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .user-avatar { 
            width: 70px; 
            height: 70px; 
            background: linear-gradient(135deg, #00bcd4 0%, #0097a7 100%); 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0 auto 15px; 
            font-size: 28px; 
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .user-name { font-weight: 600; margin-bottom: 5px; font-size: 16px; }
        .user-role { 
            font-size: 11px; 
            color: rgba(255,255,255,0.6); 
            text-transform: uppercase; 
            letter-spacing: 1px;
            background: rgba(255,255,255,0.1);
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
        }
        
        .sidebar-nav { padding: 20px 15px; }
        .nav-title { 
            font-size: 11px; 
            text-transform: uppercase; 
            letter-spacing: 1.5px; 
            color: rgba(255,255,255,0.4); 
            padding: 0 15px; 
            margin-bottom: 15px; 
        }
        .sidebar-nav .nav-link { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 12px 18px; 
            color: rgba(255,255,255,0.8); 
            text-decoration: none; 
            border-radius: 12px; 
            margin-bottom: 5px; 
            transition: all 0.3s; 
            font-size: 14px; 
            font-weight: 500; 
        }
        .sidebar-nav .nav-link i { width: 22px; text-align: center; font-size: 16px; }
        .sidebar-nav .nav-link:hover { 
            background: rgba(255,255,255,0.1); 
            transform: translateX(5px); 
            color: white;
        }
        .sidebar-nav .nav-link.active { 
            background: linear-gradient(135deg, #00bcd4 0%, #0097a7 100%); 
            color: white; 
            box-shadow: 0 4px 15px rgba(0,188,212,0.3);
        }
        
        .sidebar-footer { 
            position: absolute; 
            bottom: 0; 
            left: 0; 
            right: 0; 
            padding: 20px; 
            border-top: 1px solid rgba(255,255,255,0.08); 
            background: rgba(0,0,0,0.1);
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
            transition: all 0.3s;
        }
        .logout-btn:hover { 
            background: #ef4444; 
            color: white; 
            transform: translateX(5px);
        }

        .laborantin-main { 
            margin-left: 280px; 
            padding: 25px 30px; 
            min-height: 100vh; 
            transition: all 0.3s; 
        }
        
        .top-bar { 
            background: white; 
            border-radius: 16px; 
            padding: 20px 25px; 
            margin-bottom: 25px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        .page-title h2 { 
            font-size: 20px; 
            font-weight: 700; 
            margin: 0; 
            color: #2c3e50; 
        }
        .page-title p { 
            font-size: 13px; 
            margin: 5px 0 0 0; 
            color: #6c757d; 
        }
        .date-badge { 
            background: #f8f9fa; 
            padding: 8px 20px; 
            border-radius: 30px; 
            font-size: 13px; 
            font-weight: 500; 
            color: #495057;
        }
        .date-badge i { color: #00bcd4; margin-right: 8px; }

        .alert-custom { 
            border-radius: 12px; 
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
            background: #d4edda; 
            border-left: 4px solid #28a745; 
            color: #155724; 
        }
        .alert-error { 
            background: #f8d7da; 
            border-left: 4px solid #dc3545; 
            color: #721c24; 
        }
        .alert-custom button { 
            background: transparent; 
            border: none; 
            font-size: 20px; 
            cursor: pointer; 
            margin-left: auto;
            opacity: 0.6;
        }
        .alert-custom button:hover { opacity: 1; }

        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: #1a4d6b;
            border: none;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 12px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            text-align: center;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            background: #e3f2fd;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        .stat-icon i { font-size: 24px; color: #1a4d6b; }
        .stat-number { font-size: 28px; font-weight: 700; color: #2c3e50; margin-bottom: 5px; }
        .stat-label { color: #6c757d; font-size: 13px; font-weight: 500; }
        .stat-trend { font-size: 11px; margin-top: 10px; padding-top: 10px; border-top: 1px solid #e9ecef; }
        .stat-trend.up { color: #28a745; }
        .stat-trend.down { color: #dc3545; }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
            border-bottom: 2px solid #f0f2f5;
            padding: 20px 24px;
            font-weight: 600;
            font-size: 16px;
            color: #1a4d6b;
        }
        .card-body { padding: 24px; }

        .table thead th {
            background: #1a4d6b;
            color: white;
            font-weight: 600;
            border-bottom: none;
            padding: 12px 15px;
            font-size: 13px;
        }
        .table tbody tr:hover { background: #f8f9fa; }

        .btn-primary {
            background: #1a4d6b;
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #0f3b53;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26,77,107,0.3);
        }
        .btn-outline-primary {
            border: 2px solid #1a4d6b;
            color: #1a4d6b;
            border-radius: 10px;
            padding: 8px 22px;
            font-weight: 500;
        }
        .btn-outline-primary:hover {
            background: #1a4d6b;
            color: white;
        }

        .badge { padding: 5px 12px; border-radius: 20px; font-weight: 500; font-size: 11px; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-info { background: #d1ecf1; color: #0c5460; }

        @media (max-width: 768px) {
            .mobile-menu-toggle { display: flex; align-items: center; justify-content: center; }
            .laborantin-sidebar { transform: translateX(-100%); }
            .laborantin-sidebar.mobile-open { transform: translateX(0); }
            .laborantin-main { margin-left: 0; padding: 80px 15px 20px 15px; }
            .top-bar { flex-direction: column; gap: 15px; text-align: center; }
        }
    </style>

    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }">

    <button class="mobile-menu-toggle" @click="sidebarOpen = !sidebarOpen">
        <i class="fas fa-bars"></i>
    </button>

    <div class="laborantin-sidebar" :class="{'mobile-open': sidebarOpen}">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-hospital-user"></i> Hôpital <span>Sendwe</span>
                <small>Lubumbashi - RDC</small>
            </div>
        </div>
        
        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="fas fa-microscope"></i>
            </div>
            <div class="user-name">{{ Auth::user()->prenom ?? 'Technicien' }} {{ Auth::user()->nom ?? 'Laborantin' }}</div>
            <div class="user-role">Laborantin</div>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-title">
                <i class="fas fa-circle"></i> Navigation principale
            </div>
            <a href="{{ route('laborantin.dashboard') }}" class="nav-link {{ request()->routeIs('laborantin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Tableau de bord
            </a>
            
            <div class="nav-title">
                <i class="fas fa-circle"></i> Bons d'analyse
            </div>
            <a href="{{ route('laborantin.bons-analyse.index') }}" class="nav-link {{ request()->routeIs('laborantin.bons-analyse.*') ? 'active' : '' }}">
                <i class="fas fa-file-medical"></i> Bons reçus
            </a>
           
            <div class="nav-title">
                <i class="fas fa-circle"></i> Rapports & Analyses
            </div>
            <a href="{{ route('laborantin.statistiques.index') }}" class="nav-link {{ request()->routeIs('laborantin.statistiques.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Statistiques
            </a>
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

    <div class="laborantin-main" @click="sidebarOpen = false" @click.away="sidebarOpen = false">
        <div class="top-bar">
            <div class="page-title">
                <h2>@yield('page-title', 'Laboratoire d\'analyses médicales')</h2>
                <p>@yield('page-subtitle', 'Espace Laborantin - Hôpital Sendwe')</p>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        AOS.init({ duration: 800, once: true });
        
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    document.querySelector('.laborantin-sidebar')?.classList.remove('mobile-open');
                }
            });
        });
        
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
    </script>
    @stack('scripts')
</body>
</html>