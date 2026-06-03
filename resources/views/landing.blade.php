<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colegio Los Angeles - Sistema de Gestion Escolar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
    <header class="navbar-section">
        <div class="container">
            <nav>
                <div class="logo">
                    <div class="logo-icon">🎓</div>
                    <div class="logo-text">Colegio Los Angeles <span>| Gestion Escolar</span></div>
                </div>
                <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">Iniciar Sesion</a>
            </nav>
        </div>
    </header>

    <section class="hero-section">
        <div class="container">
            <div class="hero">
                <div class="badge">
                    <span class="badge-dot"></span>
                    Plataforma de Gestion Educativa
                </div>

                <h1>
                    Sistema de Gestion<br>
                    <span class="highlight">Escolar Integral</span>
                </h1>

                <p class="subtitle">
                    Administra alumnos, profesores, horarios, calificaciones y toda la gestion academica del Colegio "Los Angeles" en un solo lugar.
                </p>

                <div class="hero-buttons">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        Ingresar al Sistema →
                    </a>
                    <a href="#modulos" class="btn btn-secondary">
                        Ver Modulos
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section" id="modulos">
        <div class="container">
            <div class="features">
                <div class="feature-card">
                    <div class="feature-icon blue">👨‍🎓</div>
                    <h3>Gestion de Alumnos</h3>
                    <p>Registro, edicion y administracion completa de datos estudiantiles.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green">👩‍🏫</div>
                    <h3>Gestion de Profesores</h3>
                    <p>Administra el personal docente, permisos y asignaciones de materias.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon purple">📅</div>
                    <h3>Horarios y Turnos</h3>
                    <p>Organizacion de horarios por profesor, curso y paralelo.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon orange">📊</div>
                    <h3>Calificaciones</h3>
                    <p>Consulta de notas por trimestre con evaluacion integral.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon pink">📋</div>
                    <h3>Bitacora de Accesos</h3>
                    <p>Registro completo de actividad y accesos al sistema.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon teal">⚙️</div>
                    <h3>Configuracion</h3>
                    <p>Gestiones academicas, niveles y configuracion general del sistema.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="roles-section">
        <div class="container">
            <div class="roles">
                <h2>Acceso por Roles</h2>
                <p class="roles-subtitle">Cada usuario tiene acceso a las herramientas que necesita</p>

                <div class="roles-grid">
                    <div class="role-item">
                        <div class="role-emoji">🔧</div>
                        <h4>Administrador</h4>
                        <p>Control total del sistema</p>
                    </div>
                    <div class="role-item">
                        <div class="role-emoji">👨‍🏫</div>
                        <h4>Profesor</h4>
                        <p>Horarios y asignaturas</p>
                    </div>
                    <div class="role-item">
                        <div class="role-emoji">👪</div>
                        <h4>Apoderado</h4>
                        <p>Consulta de notas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>© {{ date('Y') }} <strong>Colegio "Los Angeles"</strong> — Sistema de Gestion Escolar</p>
        </div>
    </footer>
</body>
</html>
