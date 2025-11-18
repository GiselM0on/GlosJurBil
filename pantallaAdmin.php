<?php

// Usuario simulado para pruebas
$current_user = [
    'id' => 1,
    'nombre' => 'Admin',
    'correo' => 'admin@glosario.com',
    'rol' => 'admin'
];

// Obtener sección activa
$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : '';
?>

<?php
session_start();
//conexion a la db
include ("conexion.php");

// Usuario simulado para pruebas
$current_user = [
    'id' => 1,
    'nombre' => 'Admin',
    'correo' => 'admin@glosario.com',
    'rol' => 'admin'
];

// Obtener sección activa
$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla Admin - Glosario Jurídico Bilingüe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #b6b9cd;
            color: #333;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1e3a8a 0%, #15255e 100%);
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            padding: 20px 0;
        }

        .logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .logo h1 {
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            margin: 0;
        }

        .menu-section {
            margin-bottom: 25px;
        }

        .section-title {
            padding: 0 20px 10px;
            font-size: 14px;
            color: #8b9af0;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .menu-items {
            list-style: none;
            padding: 10px 0;
            margin: 0;
        }

        .menu-item {
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #dcdbeb;
            border-left: 3px solid transparent;
        }

        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 3px solid #4d579d;
        }

        .menu-item.active {
            background-color: rgba(77, 87, 157, 0.3);
            border-left: 3px solid #4d579d;
        }

        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .nav-section {
            margin-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 0;
            margin-top: auto;
        }

        #content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        .admin-card {
            border-radius: 1rem;
            background-color: #dcdbeb;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            min-height: 180px;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2);
        }

        .icon-large {
            font-size: 3rem;
            color: #1e3a8a;
            opacity: 0.8;
        }

        .table-responsive {
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            background-color: white;
        }

        .table thead {
            background-color: #4d579d;
            color: white;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            .sidebar .menu-item span,
            .sidebar-section {
                display: none;
            }
            .sidebar .menu-item {
                text-align: center;
                padding: 15px 5px;
                justify-content: center;
            }
            .logo span {
                display: none;
            }
            #content {
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>

    <!--  MENÚ LATERAL   -->
    <div class="sidebar">
        <div class="logo">
            <h1><i class="bi bi-shield-lock-fill"></i> <span> Panel Admin </span></h1>
        </div>
        
        <nav class="sidebar-nav">
            <!-- Términos -->
            <div class="menu-section">
                <div class="section-title">Términos</div>
                <ul class="menu-items">
                    <a href="?seccion=manage_translations" class="menu-item <?php echo $seccion == 'manage_translations' ? 'active' : ''; ?>">
                        <i class="bi bi-translate"></i> <span>Traducciones</span>
                    </a>
                </ul>
            </div>
            
            <!-- Validación -->
            <?php if ($current_user['rol'] === 'admin' || $current_user['rol'] === 'docente'): ?>
            <div class="menu-section">
                <div class="section-title">VALIDACIÓN</div>
                <ul class="menu-items">
                    <a href="?seccion=manage_terms" class="menu-item <?php echo $seccion == 'manage_terms' ? 'active' : ''; ?>">
                        <i class="bi bi-patch-check-fill"></i> <span>Términos</span>
                    </a>
                    <a href="?seccion=manage_validations" class="menu-item <?php echo $seccion == 'manage_validations' ? 'active' : ''; ?>">
                        <i class="bi bi-clipboard-check"></i> <span>Validaciones</span>
                    </a>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Administración -->
            <?php if ($current_user['rol'] === 'admin'): ?>
            <div class="menu-section">
                <div class="section-title">ADMINISTRACIÓN</div>
                <ul class="menu-items">
                    <a href="?seccion=manage_users" class="menu-item <?php echo $seccion == 'manage_users' ? 'active' : ''; ?>">
                        <i class="bi bi-people-fill"></i> <span>Usuarios</span>
                    </a>
                    <a href="?seccion=manage_countries" class="menu-item <?php echo $seccion == 'manage_countries' ? 'active' : ''; ?>">
                        <i class="bi bi-globe-americas"></i> <span>Países</span>
                    </a>
                    <a href="?seccion=manage_languages" class="menu-item <?php echo $seccion == 'manage_languages' ? 'active' : ''; ?>">
                        <i class="bi bi-translate"></i> <span>Idiomas</span>
                    </a>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Navegación - MANTENIENDO LOS ELEMENTOS ORIGINALES -->
            <div class="menu-section nav-section">
                <div class="section-title">NAVEGACIÓN</div>
                <ul class="menu-items">
                    <a href="index.php" class="menu-item">
                        <i class="bi bi-house-door-fill me-2"></i> <span>Página Principal</span>
                    </a>
                    <a href="#" class="menu-item" onclick="confirmLogout()">
                        <i class="bi bi-box-arrow-right me-2"></i> <span>Cerrar Sesión</span>
                    </a>
                </ul>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div id="content">
        <div id="main-content-area">
            <?php
            // Incluir la sección correspondiente
            switch($seccion) {
                case 'manage_users':
                    include 'secciones/gestUsuarios.php';
                    break;
                case 'manage_terms':
                    include 'secciones/gestTerminos.php';
                    break;
                case 'manage_translations':
                    include 'secciones/gestTraduc.php';
                    break;
                case 'manage_countries':
                    include 'secciones/gestPaises.php';
                    break;
                case 'manage_languages':
                    include 'secciones/gestIdiomas.php';
                    break;
                case 'review_terms':
                case 'manage_validations':
                    include 'secciones/gestValida.php';
                    break;
                default:
                    // Por defecto, mostrar la sección de traducciones
                    include 'secciones/gestTraduc.php';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para cerrar sesión
        function confirmLogout() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                // Aquí iría la lógica real de logout
                alert('Función de logout - En una aplicación real aquí se redirigiría al login');
                // window.location.href = 'logout.php';
            }
        }

        // Función para mostrar alertas
        function showAlert(message, type = 'info') {
            // Crear alerta de Bootstrap
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insertar al inicio del content
            const content = document.getElementById('main-content-area');
            content.insertBefore(alertDiv, content.firstChild);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Confirmación para eliminar
        function confirmDelete(action) {
            return confirm(`¿Estás seguro de que quieres ${action}? Esta acción no se puede deshacer.`);
        }
    </script>
</body>
</html>