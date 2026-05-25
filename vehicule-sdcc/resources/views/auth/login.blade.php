<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDCC - Gestion des Véhicules de Service</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/all.min.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow: auto;
            background: white;
        }

        /* Background with smooth green-orange blended gradient - enhanced visibility */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #388E3C 0%, #4CAF50 25%, #FFE082 50%, #FF6F00 75%, #D84315 100%);
            z-index: -2;
        }

        /* Left side panel - soft green accent - enhanced */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 40%;
            height: 100%;
            background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 40%, #4CAF50 100%);
            background-image: 
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 900"><defs><pattern id="cars" x="0" y="0" width="120" height="120" patternUnits="userSpaceOnUse" opacity="0.08"><g stroke="white" stroke-width="1.2" fill="none"><circle cx="60" cy="60" r="35"/><path d="M 35 50 L 85 50 L 85 70 L 35 70 Z" stroke-width="1"/><circle cx="45" cy="75" r="5"/><circle cx="75" cy="75" r="5"/></g></pattern></defs><rect width="400" height="900" fill="url(%23cars)"/></svg>');
            background-size: 200px 200px;
            background-position: 0 0;
            background-repeat: repeat;
            z-index: 0;
            clip-path: polygon(0 0, 100% 0, 92% 100%, 0 100%);
        }

        /* Right side panel - soft orange accent - enhanced */
        .bg-right {
            position: fixed;
            top: 0;
            right: 0;
            width: 40%;
            height: 100%;
            background: linear-gradient(225deg, #D84315 0%, #FF6F00 40%, #FFB84D 100%);
            background-image: 
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 900"><defs><pattern id="fuel" x="0" y="0" width="120" height="120" patternUnits="userSpaceOnUse" opacity="0.08"><g stroke="white" stroke-width="1.2" fill="none"><rect x="40" y="30" width="40" height="55" rx="4"/><circle cx="60" cy="20" r="5"/><line x1="60" y1="85" x2="60" y2="95" stroke-width="1.5"/></g></pattern></defs><rect width="400" height="900" fill="url(%23fuel)"/></svg>');
            background-size: 200px 200px;
            background-position: 0 0;
            background-repeat: repeat;
            z-index: 0;
            clip-path: polygon(8% 0, 100% 0, 100% 100%, 0 100%);
        }

        /* Decorative side shapes */
        .side-shape-left {
            position: fixed;
            left: -80px;
            top: 5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(76, 175, 80, 0.35) 0%, rgba(76, 175, 80, 0) 70%);
            border-radius: 50%;
            z-index: 1;
            box-shadow: inset 0 0 60px rgba(76, 175, 80, 0.22);
            animation: float 18s infinite ease-in-out;
        }

        .side-shape-right {
            position: fixed;
            right: -100px;
            bottom: 8%;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(255, 111, 0, 0.32) 0%, rgba(255, 111, 0, 0) 70%);
            border-radius: 50%;
            z-index: 1;
            box-shadow: inset 0 0 60px rgba(255, 111, 0, 0.18);
            animation: float 22s infinite ease-in-out reverse;
        }

        /* Car icon on left side */
        .left-icon {
            position: fixed;
            left: 7%;
            top: 25%;
            font-size: 140px;
            color: rgba(76, 175, 80, 0.38);
            z-index: 1;
            animation: float 16s infinite ease-in-out;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        /* Fuel pump icon on right side */
        .right-icon {
            position: fixed;
            right: 6%;
            bottom: 18%;
            font-size: 130px;
            color: rgba(255, 111, 0, 0.35);
            z-index: 1;
            animation: float 20s infinite ease-in-out reverse;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        /* Center accent line */
        .center-accent {
            position: fixed;
            left: 50%;
            top: 0;
            width: 1px;
            height: 100%;
            background: linear-gradient(180deg, transparent 0%, rgba(76, 175, 80, 0.35) 25%, rgba(255, 111, 0, 0.35) 75%, transparent 100%);
            transform: translateX(-1px);
            z-index: 1;
        }

        /* Content wrapper - keeps form centered and on top */
        .content-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 450px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.12), 0 15px 25px rgba(0, 0, 0, 0.08);
            padding: 70px 55px;
            width: 100%;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 45px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
        }

        .logo-icon {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
            margin: 0;
            padding: 0;
            font-size: 0;
        }

        .logo-icon img {
            width: 200px;
            height: auto;
            display: block;
            margin: 0;
            padding: 0;
            font-size: initial;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }

        .login-title {
            font-size: 32px;
            font-weight: 800;
            color: #1a1a1a;
            background: linear-gradient(135deg, #2E7D32 0%, #FF9800 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #666;
            margin: 0;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .form-section {
            margin-top: 35px;
        }

        .form-group {
            margin-bottom: 28px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            color: #2E7D32;
            margin-bottom: 10px;
            letter-spacing: 1.2px;
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
            font-size: 18px;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .form-input {
            width: 100%;
            padding: 15px 16px 15px 48px;
            border: 2px solid #e8e8e8;
            border-radius: 14px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f9fafb;
            color: #1a1a1a;
            font-weight: 500;
        }

        .form-input:focus {
            outline: none;
            border-color: #4CAF50;
            background: white;
            box-shadow: 0 0 0 5px rgba(76, 175, 80, 0.1);
        }

        .form-input:focus ~ .form-input-icon {
            color: #FF9800;
            transform: scale(1.1);
        }

        .form-input::placeholder {
            color: #aaa;
            font-weight: 400;
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 25%, #FF9800 75%, #FFB74D 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
            box-shadow: 0 8px 24px rgba(76, 175, 80, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .login-button:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(76, 175, 80, 0.45);
        }

        .login-button:active {
            transform: translateY(-1px);
        }

        .login-button i {
            font-size: 18px;
        }

        .error-message {
            background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
            color: #c62828;
            padding: 14px 16px;
            border-left: 4px solid #c62828;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 13px;
            display: none;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(198, 40, 40, 0.12);
        }

        .error-message.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        .success-message {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            color: #2e7d32;
            padding: 14px 16px;
            border-left: 4px solid #4CAF50;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 13px;
            display: none;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.12);
        }

        .success-message.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Forgot password link styling */
        a[href*="password"] {
            color: #2E7D32;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        a[href*="password"]:hover {
            color: #FF9800;
        }

        /* Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            /* Hide side panels on tablet and below */
            body::after {
                display: none;
            }

            .bg-right {
                display: none;
            }

            .side-shape-left,
            .side-shape-right,
            .left-icon,
            .right-icon,
            .center-accent {
                display: none;
            }

            body {
                background: linear-gradient(135deg, #4CAF50 0%, #A5D6A7 28%, #FFF3E0 50%, #FFB74D 72%, #FF6F00 100%);
            }
        }

        @media (max-width: 600px) {
            .login-container {
                padding: 40px 30px;
                border-radius: 16px;
            }

            .logo-icon img {
                width: 140px;
            }

            .login-title {
                font-size: 20px;
            }

            .logo-section {
                margin-bottom: 35px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-input {
                font-size: 14px;
                padding: 12px 14px 12px 40px;
            }

            .login-button {
                padding: 14px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Right side panel (fuel/energy themed) -->
    <div class="bg-right"></div>

    <!-- Decorative side shapes -->
    <div class="side-shape-left"></div>
    <div class="side-shape-right"></div>

    <!-- Icon elements on sides -->
    <div class="left-icon"><i class="fas fa-car"></i></div>
    <div class="right-icon"><i class="fas fa-gas-pump"></i></div>

    <!-- Center accent line -->
    <div class="center-accent"></div>

    <!-- Content Wrapper - Login Form -->
    <div class="content-wrapper">
        <div class="login-container">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon">
                    <img src="{{ asset('images/logo-sdcc.png') }}" alt="Logo SDCC">
                </div>
                <p class="login-subtitle">Gestion des Véhicules de Service</p>
            </div>

            <!-- Form Section -->
            <div class="form-section">
                @if ($errors->any())
                    <div class="error-message show">
                        <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <div class="form-input-wrapper">
                            <i class="fas fa-envelope form-input-icon"></i>
                            <input 
                                type="email" 
                                name="email" 
                                class="form-input" 
                                placeholder="Entrez votre email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Mot de Passe
                        </label>
                        <div class="form-input-wrapper">
                            <i class="fas fa-lock form-input-icon"></i>
                            <input 
                                type="password" 
                                name="password" 
                                class="form-input" 
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                        </div>
                    </div>

                    <div style="text-align: right; margin-bottom: 8px;">
                        <a href="{{ route('password.request') }}" style="color: #2E7D32; font-size: 13px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.color='#FF9800'" onmouseout="this.style.color='#2E7D32'"><i class="fas fa-question-circle"></i> Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="login-button"><i class="fas fa-sign-in-alt"></i> Se Connecter</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Email validation for @sdcc.ma domain
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.querySelector('input[name="email"]');
            const form = document.querySelector('form');

            if (emailInput && form) {
                // Real-time email validation
                emailInput.addEventListener('blur', function() {
                    const email = this.value.trim();
                    
                    if (email && !email.endsWith('@sdcc.ma')) {
                        // Show error styling
                        this.style.borderColor = '#c62828';
                        this.style.background = '#ffebee';
                    } else if (email) {
                        // Reset to normal styling if valid
                        this.style.borderColor = '#e8e8e8';
                        this.style.background = '#f9fafb';
                    }
                });

                // Form submission validation
                form.addEventListener('submit', function(e) {
                    const email = emailInput.value.trim();
                    
                    if (!email.endsWith('@sdcc.ma')) {
                        e.preventDefault();
                        
                        // Show error styling
                        emailInput.style.borderColor = '#c62828';
                        emailInput.style.background = '#ffebee';
                        emailInput.focus();
                        
                        // Scroll to email field
                        emailInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        // Show alert
                        alert('Only email addresses with @sdcc.ma are allowed.');
                        
                        return false;
                    }
                });

                // Clear error styling on focus
                emailInput.addEventListener('focus', function() {
                    if (this.value.trim().endsWith('@sdcc.ma') || !this.value.trim()) {
                        this.style.borderColor = '';
                        this.style.background = '';
                    }
                });
            }
        });
    </script>
</body>
</html>