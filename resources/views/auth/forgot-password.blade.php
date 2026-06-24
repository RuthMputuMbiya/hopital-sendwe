<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Hôpital Sendwe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #1a4d8c;
            --primary-dark: #0e3570;
            --secondary: #d4af37;
            --success: #27ae60;
            --danger: #e74c3c;
            --text-gray: #64748b;
            --border: #e2e8f0;
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f2f5 0%, #eef2f5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .forgot-container {
            max-width: 500px;
            width: 100%;
        }

        .forgot-card {
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
        }

        .forgot-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 30px;
            text-align: center;
            color: white;
        }

        .forgot-header i {
            font-size: 48px;
            color: var(--secondary);
            margin-bottom: 15px;
        }

        .forgot-header h1 {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .forgot-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 8px;
            color: var(--text-gray);
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

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 77, 140, 0.3);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
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
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-card">
            <div class="forgot-header">
                <i class="fas fa-key"></i>
                <h1>Mot de passe oublié ?</h1>
                <p>Nous allons vous aider à le réinitialiser</p>
            </div>
            <div class="forgot-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                        <label><i class="fas fa-envelope me-2"></i> Email</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" class="form-control" 
                                   placeholder="votre@email.com" required autofocus>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane me-2"></i> Envoyer le lien de réinitialisation
                    </button>

                    <div class="back-link">
                        <a href="{{ route('login') }}">
                            <i class="fas fa-arrow-left me-2"></i> Retour à la connexion
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>