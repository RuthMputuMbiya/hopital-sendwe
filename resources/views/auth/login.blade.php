<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - Hôpital Sendwe | Lubumbashi</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e8f0f9 0%, #dce5f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            animation: fadeInUp 0.6s ease-out;
        }

        .login-card {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #1a4d8c 0%, #0e3570 100%);
            padding: 32px 28px;
            text-align: center;
            color: white;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .logo-icon i {
            font-size: 32px;
            color: #d4af37;
        }

        .login-header h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .login-header p {
            font-size: 13px;
            opacity: 0.85;
        }

        .login-body {
            padding: 32px 28px;
        }

        .alert {
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 24px;
            border: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #e6f7ed;
            color: #2d6a4f;
            border-left: 4px solid #2d6a4f;
        }

        .alert-danger {
            background: #fee7e7;
            color: #c0392b;
            border-left: 4px solid #c0392b;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f1f5f9;
            padding: 8px 16px;
            border-radius: 30px;
            color: #1a4d8c;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-home:hover {
            background: #e2e8f0;
            color: #0e3570;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
            font-size: 13px;
            display: block;
        }

        .form-label i {
            margin-right: 6px;
            color: #1a4d8c;
        }

        .input-group-custom {
            position: relative;
        }

        .input-group-custom i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
            z-index: 1;
        }

        .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px 16px 12px 42px;
            font-size: 14px;
            transition: all 0.2s ease;
            background: #f8fafc;
            width: 100%;
        }

        .form-control:focus {
            border-color: #1a4d8c;
            background: white;
            box-shadow: 0 0 0 3px rgba(26, 77, 140, 0.1);
            outline: none;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #1a4d8c;
        }

        .checkbox span {
            font-size: 13px;
            color: #475569;
        }

        .forgot-link {
            font-size: 13px;
            color: #1a4d8c;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn-login {
            background: linear-gradient(135deg, #1a4d8c 0%, #0e3570 100%);
            border: none;
            border-radius: 14px;
            padding: 14px;
            font-weight: 600;
            font-size: 14px;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(26, 77, 140, 0.25);
        }

        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 24px 0;
        }

        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .separator span {
            padding: 0 12px;
            color: #94a3b8;
            font-size: 12px;
        }

        .btn-register {
            border-radius: 14px;
            padding: 12px;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #f8fafc;
            color: #1a4d8c;
            border: 1px solid #e2e8f0;
        }

        .btn-register:hover {
            background: #eef2ff;
            transform: translateY(-2px);
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
        }

        .login-footer a {
            color: #1a4d8c;
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 480px) {
            .login-body {
                padding: 24px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-icon">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <h2>Hôpital Sendwe</h2>
                <p>Lubumbashi - RDC</p>
            </div>
            
            <div class="login-body">
                <a href="{{ route('welcome') }}" class="back-home">
                    <i class="fas fa-arrow-left"></i> Retour à l'accueil
                </a>
                
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-id-card"></i> Matricule ou Email</label>
                        <div class="input-group-custom">
                            <i class="fas fa-user"></i>
                            <input type="text" name="login" class="form-control" value="{{ old('login') }}" required placeholder="Votre matricule ou email">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-lock"></i> Mot de passe</label>
                        <div class="input-group-custom">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <label class="checkbox">
                            <input type="checkbox" name="remember">
                            <span>Se souvenir de moi</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-link">Mot de passe oublié ?</a>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> Se connecter
                    </button>
                </form>
                
                <div class="separator">
                    <span>Nouveau sur la plateforme ?</span>
                </div>
                
                <a href="{{ route('register') }}" class="btn-register">
                    <i class="fas fa-user-plus"></i> Créer un compte
                </a>
                
                <div class="login-footer">
                    <p>© {{ date('Y') }} Hôpital Sendwe - Lubumbashi, République Démocratique du Congo</p>
                    <p><small>Application de gestion des dossiers médicaux</small></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>