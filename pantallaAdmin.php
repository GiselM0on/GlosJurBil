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



// Obtener sección activa
$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla Admin - Glosario Jurídico Bilingüe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
:root {
    --color-amarillo: #fff06dff;
    --color-azul-oscuro: #006694;
    --color-gris: #636466;
    --color-gris-claro: #f1f2f2;
    --color-naranja: #ff9a15;
    --color-azul-claro: #27a5df;
}

body {
    background-color: var(--color-gris-claro);
    color: var(--color-azul-oscuro);
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
    margin: 0;
    padding: 0;
}

.sidebar {
    width: 260px;
    background: linear-gradient(180deg, var(--color-azul-oscuro) 0%, #004466 100%);
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
    color:white;
}

.menu-section {
    margin-bottom: 25px;
}

.section-title {
    padding: 0 20px 10px;
    font-size: 14px;
    color: var(--color-azul-claro);
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
    color: var(--color-gris-claro);
    border-left: 3px solid transparent;
}

.menu-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-left: 3px solid var(--color-amarillo);
    color: white;
}

.menu-item.active {
    background-color: rgba(255, 160, 109, 0.2);
    border-left: 3px solid var(--color-amarillo);
    color: white;
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
    background-color: var(--color-gris-claro);
}

.admin-card {
    border-radius: 1rem;
    background-color: white;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
    min-height: 180px;
    border: 1px solid var(--color-gris);
    color: var(--color-azul-oscuro);
    border-left: 5px solid var(--color-amarillo);
}

.admin-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(255, 160, 109, 0.2);
    border-color: var(--color-amarillo);
}

.icon-large {
    font-size: 3rem;
    color: var(--color-amarillo);
    opacity: 0.9;
}

.table-responsive {
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    background-color: white;
    border: 1px solid var(--color-gris);
}

.table thead {
    background-color: var(--color-azul-oscuro);
    color: white;
}

.table th, .table td {
    vertical-align: middle;
    color: var(--color-azul-oscuro);
}

.table tbody tr:hover {
    background-color: rgba(255, 160, 109, 0.1);
}

/* BOTONES CON AMARILLO DESTACADO */
.btn-primary {
    background-color: var(--color-amarillo);
    border-color: var(--color-amarillo);
    color: var(--color-azul-oscuro);
    font-weight: 600;
}

.btn-primary:hover {
    background-color: #ff8a3d;
    border-color: #ff8a3d;
    color: var(--color-azul-oscuro);
}

.btn-warning {
    background-color: var(--color-naranja);
    border-color: var(--color-naranja);
    color: white;
    font-weight: 600;
}

.btn-warning:hover {
    background-color: #e88a10;
    border-color: #e88a10;
    color: white;
}

.btn-info {
    background-color: var(--color-azul-claro);
    border-color: var(--color-azul-claro);
    color: white;
}

.btn-info:hover {
    background-color: #1e8bc4;
    border-color: #1e8bc4;
}

.alert-info {
    background-color: rgba(255, 160, 109, 0.1);
    border-color: var(--color-amarillo);
    color: var(--color-azul-oscuro);
}

.alert-warning {
    background-color: rgba(255, 154, 21, 0.1);
    border-color: var(--color-naranja);
    color: var(--color-azul-oscuro);
}

.alert-success {
    background-color: rgba(0, 102, 148, 0.1);
    border-color: var(--color-azul-oscuro);
    color: var(--color-azul-oscuro);
}

.badge-primary {
    background-color: var(--color-amarillo);
    color: var(--color-azul-oscuro);
}

.badge-warning {
    background-color: var(--color-naranja);
    color: white;
}

.badge-info {
    background-color: var(--color-azul-claro);
    color: white;
}

.text-primary {
    color: var(--color-azul-oscuro) !important;
}

.text-warning {
    color: var(--color-amarillo) !important;
}

.text-info {
    color: var(--color-azul-claro) !important;
}

.card {
    border: 1px solid var(--color-gris);
    background-color: white;
}

.card-header {
    background-color: var(--color-azul-oscuro);
    color: white;
    border-bottom: 1px solid var(--color-gris);
}

.form-control:focus {
    border-color: var(--color-amarillo);
    box-shadow: 0 0 0 0.2rem rgba(255, 160, 109, 0.25);
}

.nav-pills .nav-link.active {
    background-color: var(--color-amarillo);
    color: var(--color-azul-oscuro);
}

.pagination .page-item.active .page-link {
    background-color: var(--color-amarillo);
    border-color: var(--color-amarillo);
    color: var(--color-azul-oscuro);
}

.pagination .page-link {
    color: var(--color-azul-oscuro);
}

.pagination .page-link:hover {
    color: var(--color-amarillo);
}

/* ESTILOS ADICIONALES PARA BOTONES DE ACCIÓN */
.btn-action {
    background-color: var(--color-amarillo);
    border-color: var(--color-amarillo);
    color: var(--color-azul-oscuro);
    font-weight: 600;
    transition: all 0.3s;
}

.btn-action:hover {
    background-color: #ff8a3d;
    border-color: #ff8a3d;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 160, 109, 0.3);
}

/* ESTILOS ESPECÍFICOS PARA TABLA RESPONSIVA */
.table-responsive-container {
    position: relative;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 0.75rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    background-color: white;
    border: 1px solid var(--color-gris);
}

.table-responsive-container table {
    min-width: 800px;
    width: 100%;
    margin-bottom: 0;
    font-size: 0.875rem;
}

/* ENCABEZADOS VISIBLES SIEMPRE */
.table-responsive-container thead {
    background-color: var(--color-azul-oscuro);
}

.table-responsive-container th {
    color: var (--color-azul-claro);
    font-weight: 600;
    padding: 0.75rem 0.5rem;
    border-bottom: 2px solid var(--color-azul-claro);
    white-space: nowrap;
}

.table-responsive-container td {
    padding: 0.75rem 0.5rem;
    border-bottom: 1px solid var(--color-gris-claro);
    color: var(--color-azul-oscuro);
    vertical-align: top;
}

.table-responsive-container tbody tr:hover {
    background-color: rgba(255, 160, 109, 0.1);
}

/* ESTILOS MEJORADOS PARA LA TABLA DE TÉRMINOS */
.contenedor-tabla {
    margin-top: 2rem;
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.titulo-tabla-terminos {
    color: var(--color-azul-oscuro);
    font-weight: 600;
    border-bottom: 3px solid var(--color-amarillo);
    padding-bottom: 0.75rem;
    margin-bottom: 1.5rem !important;
}

/* ESTADOS SIMPLIFICADOS - SOLO COLOR DE TEXTO */
.status-badge {
    padding: 0.4em 0.8em;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.75rem;
    display: inline-block;
    text-align: center;
    min-width: 80px;
}

/* Solo color de texto para los estados */
.status-active {
    color: var(--color-azul-claro); /* Un solo azul */
    font-weight: bold;
}

.status-pending {
    color: var(--color-naranja);
    font-weight: bold;
}

.badge-estado-rechazado {
    color: #dc3545;
    font-weight: bold;
}

/* Para textos largos */
.texto-limitado {
    max-width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* MEJORAS RESPONSIVE - ENCABEZADOS SIEMPRE VISIBLES */
@media (max-width: 768px) {
    .table-responsive-container {
        border-radius: 0.5rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        font-size: 0.8rem;
    }
    
    .table-responsive-container table {
        min-width: 100%;
    }
    
    /* ENCABEZADOS COMPACTOS EN MÓVIL PERO VISIBLES */
    .table-responsive-container th {
        padding: 0.5rem 0.3rem;
        font-size: 0.75rem;
    }
    
    .table-responsive-container td {
        padding: 0.5rem 0.3rem;
    }
    
    .texto-limitado {
        max-width: 150px;
        font-size: 0.75rem;
    }
    
    .status-badge {
        min-width: 60px;
        font-size: 0.7rem;
        padding: 0.3em 0.6em;
    }
    
    .contenedor-tabla {
        padding: 1rem;
        margin: 1rem -0.5rem;
        border-radius: 0.5rem;
    }
}

/* Para pantallas muy pequeñas */
@media (max-width: 576px) {
    .table-responsive-container {
        font-size: 0.75rem;
    }
    
    .table-responsive-container th,
    .table-responsive-container td {
        padding: 0.4rem 0.2rem;
    }
    
    .texto-limitado {
        max-width: 120px;
    }
}

/* Para pantallas grandes - efectos hover */
@media (min-width: 769px) {
    .texto-limitado:hover {
        white-space: normal;
        overflow: visible;
        position: relative;
        z-index: 10;
        background: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        padding: 5px;
        border-radius: 4px;
    }
}

/* Asegurar que los badges se vean bien en móviles */
.table-responsive-container .badge {
    font-size: 0.75rem;
    padding: 0.4em 0.8em;
}

/* Mejoras para la paginación en móviles */
@media (max-width: 768px) {
    .d-flex.justify-content-between.align-items-center.mt-3 {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
}

/* Estilos para fechas y IDs */
.table-responsive-container td small {
    color: var(--color-gris);
    font-size: 0.8rem;
}

.table-responsive-container td strong {
    color: var(--color-azul-oscuro);
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
</style>
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
                   <a href="?seccion=manage_terms" class="menu-item <?php echo $seccion == 'manage_terms' ? 'active' : ''; ?>">
                        <i class="bi bi-patch-check-fill"></i> <span>Términos</span>
                    </a>
                </ul>
            </div>
            
            <!-- Validación -->
            <?php if ($current_user['rol'] === 'admin' || $current_user['rol'] === 'docente'): ?>
            <div class="menu-section">
                <div class="section-title">VALIDACIÓN</div>
                <ul class="menu-items">
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
                    include 'secciones/gestTerminos.php';
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