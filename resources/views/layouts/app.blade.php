<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hôpital Sendwe - Lubumbashi | @yield('title')</title>
    
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
    
    <style>
        :root {
            --primary: #1a4d8c;
            --primary-light: #2c6e9e;
            --primary-dark: #0e3570;
            --secondary: #d4af37;
            --secondary-light: #e8c06a;
            --secondary-dark: #b8960c;
            --accent: #3b82f6;
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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f2f5 0%, #eef2f5 100%);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* ========== NAVIGATION ========== */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow-md);
        }

        .navbar-brand {
            color: white;
            font-weight: 800;
            font-size: 1.4rem;
        }

        .navbar-brand small {
            font-size: 0.65rem;
            opacity: 0.85;
            display: block;
            letter-spacing: 1px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--secondary);
            transform: translateY(-2px);
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-lg);
            border-radius: 16px;
            margin-top: 8px;
        }

        .dropdown-item {
            padding: 10px 20px;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: #eaf2f8;
            color: var(--primary);
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
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            background: var(--bg-white);
            min-height: calc(100vh - 64px);
            box-shadow: var(--shadow);
            position: sticky;
            top: 64px;
            border-right: 1px solid var(--border);
        }

        .sidebar .nav-link {
            color: var(--text-gray);
            padding: 12px 20px;
            border-radius: 12px;
            margin: 4px 12px;
            transition: all 0.3s ease;
            font-weight: 500;
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
            width: 24px;
            margin-right: 12px;
            font-size: 16px;
        }

        .sidebar-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--secondary);
            padding: 15px 20px 5px;
            font-weight: 700;
        }

        /* ========== CARDS ========== */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            background: var(--bg-white);
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-bottom: 1px solid var(--border);
            padding: 20px 24px;
            font-weight: 600;
            font-size: 16px;
            color: var(--primary);
        }

        .card-body {
            padding: 24px;
        }

        /* ========== STAT CARDS ========== */
        .stat-card {
            background: var(--bg-white);
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
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
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgba(26, 77, 140, 0.1), rgba(212, 175, 55, 0.1));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .stat-icon i {
            font-size: 24px;
            color: var(--primary);
        }

        .stat-number {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 5px;
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

        /* ========== BUTTONS ========== */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 12px;
            padding: 8px 22px;
            font-weight: 500;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        .btn-success { background: var(--success); border: none; border-radius: 12px; }
        .btn-danger { background: var(--danger); border: none; border-radius: 12px; }
        .btn-warning { background: var(--warning); border: none; border-radius: 12px; color: white; }

        /* ========== TABLES ========== */
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
            padding: 15px;
            font-size: 13px;
        }

        .table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            font-size: 13px;
        }

        .table tbody tr:hover {
            background: #eaf2f8;
        }

        /* ========== FORMS ========== */
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

        /* ========== ALERTS ========== */
        .alert {
            border: none;
            border-radius: 14px;
            padding: 14px 20px;
            margin-bottom: 20px;
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

        /* ========== BADGES ========== */
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

        /* ========== FOOTER ========== */
        .footer {
            background: var(--bg-white);
            padding: 20px 0;
            margin-top: 40px;
            border-top: 1px solid var(--border);
            color: var(--text-gray);
            font-size: 12px;
        }

        /* ========== SCROLL TOP ========== */
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
        }

        .scroll-top.show {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top:hover {
            background: var(--secondary);
            transform: translateY(-3px);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                transition: all 0.3s ease;
                z-index: 1050;
            }
            .sidebar.show {
                left: 0;
            }
            .stat-number {
                font-size: 22px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>

    @yield('content')

    <button class="scroll-top" id="scrollTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
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
        
        const scrollTop = document.getElementById('scrollTop');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) scrollTop.classList.add('show');
            else scrollTop.classList.remove('show');
        });
        scrollTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
        
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
    
    @stack('scripts')
</body>
</html>