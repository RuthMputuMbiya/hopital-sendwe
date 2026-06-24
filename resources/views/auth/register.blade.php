<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inscription - Hôpital Sendwe</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #1a4d8c;
            --primary-dark: #0e3570;
            --primary-light: #2c6e9e;
            --secondary: #d4af37;
            --secondary-dark: #b8960c;
            --success: #27ae60;
            --danger: #e74c3c;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --border: #e2e8f0;
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f2f5 0%, #eef2f5 100%);
            padding: 40px 20px;
        }

        .register-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .register-card {
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
        }

        .register-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 30px;
            text-align: center;
            color: white;
            position: relative;
        }

        .back-home-btn {
            position: absolute;
            top: 20px;
            left: 25px;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s;
        }

        .back-home-btn:hover {
            background: var(--secondary);
            color: var(--primary-dark);
        }

        .register-header i {
            font-size: 48px;
            color: var(--secondary);
            margin-bottom: 15px;
        }

        .register-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .register-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .register-body {
            padding: 40px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-gray);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid var(--border);
            border-radius: 14px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary);
        }

        select.form-control {
            appearance: none;
            cursor: pointer;
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            transform: translateY(-2px);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: var(--text-gray);
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .alert-danger {
            background: #fee7e7;
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .alert-success {
            background: #e6f7ed;
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .info-matricule {
            background: #f0f7ff;
            border-left: 4px solid var(--secondary);
            padding: 12px 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .info-matricule i {
            color: var(--secondary);
            margin-right: 8px;
        }

        .info-matricule strong {
            color: var(--primary);
        }

        .password-requirements {
            margin-top: 8px;
            font-size: 11px;
            color: var(--text-gray);
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .register-body {
                padding: 25px;
            }
            .back-home-btn {
                font-size: 10px;
                padding: 5px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <a href="{{ route('welcome') }}" class="back-home-btn">
                    <i class="fas fa-arrow-left me-1"></i> Accueil
                </a>
                <i class="fas fa-user-plus"></i>
                <h1>Créer un compte</h1>
                <p>Inscrivez-vous pour accéder à la plateforme médicale</p>
            </div>
            <div class="register-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Veuillez corriger les erreurs ci-dessous.
                    </div>
                @endif

                <!-- Information matricule -->
                <div class="info-matricule">
                    <i class="fas fa-info-circle"></i>
                    <strong>Information :</strong> Votre matricule détermine automatiquement votre rôle :
                    <strong>ADM</strong> = Administrateur | <strong>DOC</strong> = Médecin | <strong>LAB</strong> = Laborantin | <strong>REC</strong> = Réceptionniste
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <!-- Champ matricule masqué pour stocker le rôle -->
                    <input type="hidden" name="role_id" id="role_id" value="">

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-id-card me-2"></i> Matricule *</label>
                            <div class="input-group">
                                <i class="fas fa-qrcode"></i>
                                <input type="text" name="matricule" id="matricule" class="form-control" 
                                       value="{{ old('matricule') }}" placeholder="Ex: DOC001, ADM001, LAB001, REC001" required>
                            </div>
                            <small class="text-muted" style="font-size: 11px;">
                                <i class="fas fa-info-circle me-1"></i> 
                                Les 3 premières lettres déterminent votre rôle
                            </small>
                            @error('matricule') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-venus-mars me-2"></i> Sexe *</label>
                            <div class="input-group">
                                <i class="fas fa-venus-mars"></i>
                                <select name="sexe" class="form-control" required>
                                    <option value="">Sélectionnez</option>
                                    <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                                    <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                                </select>
                            </div>
                            @error('sexe') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user me-2"></i> Nom *</label>
                            <div class="input-group">
                                <i class="fas fa-user"></i>
                                <input type="text" name="nom" class="form-control" 
                                       value="{{ old('nom') }}" placeholder="Nom de famille" required>
                            </div>
                            @error('nom') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user me-2"></i> Postnom *</label>
                            <div class="input-group">
                                <i class="fas fa-user"></i>
                                <input type="text" name="postnom" class="form-control" 
                                       value="{{ old('postnom') }}" placeholder="Postnom" required>
                            </div>
                            @error('postnom') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user me-2"></i> Prénom *</label>
                            <div class="input-group">
                                <i class="fas fa-user"></i>
                                <input type="text" name="prenom" class="form-control" 
                                       value="{{ old('prenom') }}" placeholder="Prénom" required>
                            </div>
                            @error('prenom') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-stethoscope me-2"></i> Spécialité</label>
                            <div class="input-group">
                                <i class="fas fa-stethoscope"></i>
                                <select name="specialite" id="specialite" class="form-control">
                                    <option value="">Non applicable</option>
                                    <option value="Médecine Générale"> Médecine Générale</option>
                                    <option value="Pédiatrie"> Pédiatrie</option>
                                    <option value="Cardiologie"> Cardiologie</option>
                                    <option value="Neurologie">Neurologie</option>
                                    <option value="Gynécologie"> Gynécologie</option>
                                    <option value="Chirurgie">Chirurgie</option>
                                    <option value="Pneumologie">Pneumologie</option>
                                    <option value="Dermatologie">Dermatologie</option>
                                </select>
                            </div>
                            <small class="text-muted" style="font-size: 11px;">Uniquement pour les médecins</small>
                            @error('specialite') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-envelope me-2"></i> Email *</label>
                            <div class="input-group">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" class="form-control" 
                                       value="{{ old('email') }}" placeholder="exemple@email.com" required>
                            </div>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-phone me-2"></i> Téléphone *</label>
                            <div class="input-group">
                                <i class="fas fa-phone"></i>
                                <input type="tel" name="telephone" class="form-control" 
                                       value="{{ old('telephone') }}" placeholder="+243 XX XXX XXXX" required>
                            </div>
                            @error('telephone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-location-dot me-2"></i> Adresse *</label>
                        <div class="input-group">
                            <i class="fas fa-location-dot"></i>
                            <input type="text" name="adresse" class="form-control" 
                                   placeholder="Lubumbashi, Quartier ..., Avenue ..." 
                                   value="{{ old('adresse') }}" required>
                        </div>
                        @error('adresse') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-lock me-2"></i> Mot de passe *</label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-lock me-2"></i> Confirmer mot de passe *</label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>
                    </div>

                    <div class="password-requirements">
                        <i class="fas fa-info-circle me-1"></i> 
                        Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-plus me-2"></i> S'inscrire
                    </button>

                    <div class="login-link">
                        Déjà un compte ? 
                        <a href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i> Se connecter
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Déterminer le rôle automatiquement selon le matricule
        function updateRole() {
            var matricule = document.getElementById('matricule').value.toUpperCase();
            var roleField = document.getElementById('role_id');
            var specialiteSelect = document.getElementById('specialite');
            
            var roleMap = {
                'ADM': 1,
                'DOC': 2,
                'REC': 3,
                'LAB': 4
            };
            
            var roleId = null;
            for (var prefix in roleMap) {
                if (matricule.startsWith(prefix)) {
                    roleId = roleMap[prefix];
                    break;
                }
            }
            
            // Définir la valeur du rôle caché
            if (roleId) {
                roleField.value = roleId;
            } else {
                roleField.value = '';
                specialiteSelect.required = false;
                return;
            }
            
            // Gérer la spécialité pour les médecins
            if (roleId == 2) {
                specialiteSelect.required = true;
                specialiteSelect.parentElement.parentElement.querySelector('label').innerHTML = '<i class="fas fa-stethoscope me-2"></i> Spécialité <span class="text-danger">*</span>';
            } else {
                specialiteSelect.required = false;
                specialiteSelect.parentElement.parentElement.querySelector('label').innerHTML = '<i class="fas fa-stethoscope me-2"></i> Spécialité <span class="text-muted">(Optionnel)</span>';
            }
        }
        
        // Écouter les changements du matricule
        document.getElementById('matricule').addEventListener('input', updateRole);
        document.getElementById('matricule').addEventListener('change', updateRole);
        
        // Initialiser au chargement
        document.addEventListener('DOMContentLoaded', function() {
            updateRole();
        });
    </script>
</body>
</html>