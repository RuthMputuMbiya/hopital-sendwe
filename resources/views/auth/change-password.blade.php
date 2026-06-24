<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer mon mot de passe - Hôpital Sendwe</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f2b3d 0%, #1a4d8c 50%, #2c6e9e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .change-password-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo i {
            font-size: 48px;
            color: #1a4d8c;
            background: rgba(26, 77, 140, 0.1);
            padding: 20px;
            border-radius: 20px;
        }

        .logo h2 {
            color: #1a4d8c;
            font-weight: 700;
            margin-top: 15px;
        }

        .logo p {
            color: #64748b;
            font-size: 14px;
        }

        .alert-warning {
            background: #fff3e0;
            border: none;
            border-left: 4px solid #f39c12;
            border-radius: 12px;
            padding: 15px 18px;
        }

        .form-label {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }

        .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #1a4d8c;
            box-shadow: 0 0 0 3px rgba(26, 77, 140, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #1a4d8c, #2c6e9e);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(26, 77, 140, 0.3);
            color: white;
        }

        .password-requirements {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            margin-top: 10px;
        }

        .password-requirements li {
            list-style: none;
            font-size: 13px;
            color: #64748b;
            padding: 4px 0;
        }

        .password-requirements li i {
            margin-right: 8px;
            font-size: 12px;
        }

        .password-requirements li.valid {
            color: #27ae60;
        }

        .password-requirements li.invalid {
            color: #64748b;
        }

        .user-info {
            background: #eaf2f8;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .user-info i {
            color: #1a4d8c;
            margin-right: 10px;
        }

        .user-info strong {
            color: #1a4d8c;
        }

        @media (max-width: 576px) {
            .change-password-card {
                padding: 24px;
            }

            .logo i {
                font-size: 36px;
                padding: 15px;
            }

            .logo h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="change-password-card">
        <div class="logo">
            <i class="fas fa-hospital-user"></i>
            <h2>Hôpital Sendwe</h2>
            <p>🔒 Changer mon mot de passe</p>
        </div>

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="user-info">
            <i class="fas fa-user"></i>
            <strong>{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</strong>
            <span class="text-muted ms-2">| {{ auth()->user()->matricule }}</span>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Mot de passe actuel -->
            <div class="mb-3">
                <label for="current_password" class="form-label">
                    <i class="fas fa-key me-1"></i> Mot de passe actuel
                </label>
                <input id="current_password" name="current_password" type="password" 
                       class="form-control @error('current_password') is-invalid @enderror"
                       placeholder="Entrez votre mot de passe actuel" required>
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Nouveau mot de passe -->
            <div class="mb-3">
                <label for="new_password" class="form-label">
                    <i class="fas fa-lock me-1"></i> Nouveau mot de passe
                </label>
                <input id="new_password" name="new_password" type="password" 
                       class="form-control @error('new_password') is-invalid @enderror"
                       placeholder="Entrez votre nouveau mot de passe" required>
                @error('new_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirmation -->
            <div class="mb-3">
                <label for="new_password_confirmation" class="form-label">
                    <i class="fas fa-check-circle me-1"></i> Confirmer le mot de passe
                </label>
                <input id="new_password_confirmation" name="new_password_confirmation" type="password" 
                       class="form-control"
                       placeholder="Confirmez votre nouveau mot de passe" required>
            </div>

            <!-- Exigences mot de passe -->
            <div class="password-requirements">
                <p class="mb-2 fw-bold" style="font-size: 13px; color: #1e293b;">
                    <i class="fas fa-info-circle me-1"></i> Exigences :
                </p>
                <ul class="mb-0">
                    <li id="req-length"><i class="fas fa-circle"></i> Minimum 8 caractères</li>
                    <li id="req-upper"><i class="fas fa-circle"></i> Une majuscule</li>
                    <li id="req-lower"><i class="fas fa-circle"></i> Une minuscule</li>
                    <li id="req-number"><i class="fas fa-circle"></i> Un chiffre</li>
                    <li id="req-symbol"><i class="fas fa-circle"></i> Un symbole (@, #, $, etc.)</li>
                </ul>
            </div>

            <button type="submit" class="btn-primary mt-3">
                <i class="fas fa-save me-2"></i> Changer mon mot de passe
            </button>

            <div class="text-center mt-3">
                <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">
                    <i class="fas fa-arrow-left me-1"></i> Retour au tableau de bord
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Validation en temps réel du mot de passe
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('new_password');
            const reqLength = document.getElementById('req-length');
            const reqUpper = document.getElementById('req-upper');
            const reqLower = document.getElementById('req-lower');
            const reqNumber = document.getElementById('req-number');
            const reqSymbol = document.getElementById('req-symbol');

            password.addEventListener('input', function() {
                const val = this.value;
                
                if (val.length >= 8) {
                    reqLength.className = 'valid';
                    reqLength.innerHTML = '<i class="fas fa-check-circle text-success"></i> Minimum 8 caractères';
                } else {
                    reqLength.className = 'invalid';
                    reqLength.innerHTML = '<i class="fas fa-circle"></i> Minimum 8 caractères';
                }
                
                if (/[A-Z]/.test(val)) {
                    reqUpper.className = 'valid';
                    reqUpper.innerHTML = '<i class="fas fa-check-circle text-success"></i> Une majuscule';
                } else {
                    reqUpper.className = 'invalid';
                    reqUpper.innerHTML = '<i class="fas fa-circle"></i> Une majuscule';
                }
                
                if (/[a-z]/.test(val)) {
                    reqLower.className = 'valid';
                    reqLower.innerHTML = '<i class="fas fa-check-circle text-success"></i> Une minuscule';
                } else {
                    reqLower.className = 'invalid';
                    reqLower.innerHTML = '<i class="fas fa-circle"></i> Une minuscule';
                }
                
                if (/[0-9]/.test(val)) {
                    reqNumber.className = 'valid';
                    reqNumber.innerHTML = '<i class="fas fa-check-circle text-success"></i> Un chiffre';
                } else {
                    reqNumber.className = 'invalid';
                    reqNumber.innerHTML = '<i class="fas fa-circle"></i> Un chiffre';
                }
                
                if (/[!@#$%^&*(),.?":{}|<>]/.test(val)) {
                    reqSymbol.className = 'valid';
                    reqSymbol.innerHTML = '<i class="fas fa-check-circle text-success"></i> Un symbole';
                } else {
                    reqSymbol.className = 'invalid';
                    reqSymbol.innerHTML = '<i class="fas fa-circle"></i> Un symbole';
                }
            });
        });
    </script>
</body>
</html>