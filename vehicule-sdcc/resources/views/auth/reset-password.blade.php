<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser Mot de Passe - SDCC</title>
    <link rel="stylesheet" href="{{ asset('vendor/font-awesome/css/all.min.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #f1f8f4 0%, #fff3e0 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 60px 50px;
            max-width: 500px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 12px;
        }

        .header p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #333;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }

        .form-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input-icon {
            position: absolute;
            left: 16px;
            color: #4CAF50;
            font-size: 16px;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }

        .form-input:focus {
            outline: none;
            border-color: #4CAF50;
            background: white;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
        }

        .submit-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 16px;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
        }

        .back-link {
            text-align: center;
            margin-top: 24px;
        }

        .back-link a {
            color: #1e3a5f;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 12px 16px;
            border-left: 4px solid #c62828;
            border-radius: 4px;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-section">
            <div class="logo-icon">
                <img src="{{ asset('images/logo-sdcc-2.png') }}" alt="Logo SDCC">
            </div>
        </div>
        <div class="header">
            <h1><i class="fas fa-lock"></i> Réinitialiser Mot de Passe</h1>
            <p>Entrez votre nouveau mot de passe</p>
        </div>

        @if ($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ request('token') }}">

            <div class="form-group">
                <label class="form-label">Adresse Email</label>
                <div class="form-input-wrapper">
                    <i class="fas fa-envelope form-input-icon"></i>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="votre.email@sdcc.ma"
                        value="{{ old('email', request('email')) }}"
                        required
                        autofocus
                    >
                </div>
                @error('email')
                    <small style="color: #c62828; margin-top: 4px; display: block;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nouveau Mot de Passe</label>
                <div class="form-input-wrapper">
                    <i class="fas fa-lock form-input-icon"></i>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="••••••••"
                        required
                    >
                </div>
                @error('password')
                    <small style="color: #c62828; margin-top: 4px; display: block;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirmer Mot de Passe</label>
                <div class="form-input-wrapper">
                    <i class="fas fa-lock form-input-icon"></i>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        class="form-input" 
                        placeholder="••••••••"
                        required
                    >
                </div>
                @error('password_confirmation')
                    <small style="color: #c62828; margin-top: 4px; display: block;">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="submit-button">
                <i class="fas fa-sync"></i> Réinitialiser
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Retour à la Connexion</a>
        </div>
    </div>
</body>
</html>
