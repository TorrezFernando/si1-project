<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion — Colegio Los Angeles</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(160deg, #0b132b 0%, #14274e 25%, #1e3a8a 55%, #2563eb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(ellipse at 20% 30%, rgba(96, 165, 250, 0.12) 0%, transparent 55%),
                radial-gradient(ellipse at 80% 70%, rgba(37, 99, 235, 0.1) 0%, transparent 55%);
            pointer-events: none;
        }
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 2rem;
        }
        .login-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-logo .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.25);
            margin-bottom: 0.8rem;
        }
        .login-logo h4 {
            font-weight: 800;
            color: #1e293b;
            font-size: 1.3rem;
            letter-spacing: -0.02em;
        }
        .login-logo h4 span {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .login-card label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 0.4rem;
        }
        .login-card .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: #f8fafc;
        }
        .login-card .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: #ffffff;
        }
        .login-card .btn-login {
            width: 100%;
            padding: 0.8rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            color: #fff;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
            transition: all 0.3s;
            margin-top: 0.5rem;
        }
        .login-card .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.4);
        }
        .login-card .forgot-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #64748b;
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        .login-card .forgot-link:hover {
            color: #2563eb;
        }
        .login-card .alert {
            border-radius: 10px;
            font-size: 0.85rem;
        }
        .login-card .invalid-feedback {
            font-size: 0.8rem;
            font-weight: 500;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <div class="logo-icon">🎓</div>
                <h4>Colegio <span>Los Angeles</span></h4>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="username">Usuario</label>
                    <input id="username" type="text"
                           class="form-control @error('username') is-invalid @enderror"
                           name="username" value="{{ old('username') }}"
                           placeholder="Ingresa tu nombre de usuario"
                           required autofocus>
                    @error('username')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password">Contraseña</label>
                    <div class="input-group">
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password"
                               placeholder="Ingresa tu contraseña"
                               required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-color: #e2e8f0;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesion
                </button>
            </form>

            <a href="{{ route('admin.password.request') }}" class="forgot-link">
                ¿Olvidaste tu contraseña?
            </a>
        </div>

        <a href="{{ url('/') }}" class="back-link">
            <i class="fas fa-arrow-left mr-1"></i> Volver al inicio
        </a>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const pwd = document.getElementById('password');
            const icon = this.querySelector('i');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    </script>
</body>
</html>
