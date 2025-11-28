<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'docente' && $_SESSION['rol'] !== 'administrador')) {
    header("Location: login.php");
    exit();
}

$current_user = [
    'id' => $_SESSION['id_Usuario'],
    'rol' => $_SESSION['rol']
];

if ($_POST && isset($_POST['accion'])) {
    $id_Termino = $_POST['id_Termino'];
    $accion = $_POST['accion'];
    $comentario = isset($_POST['comentario']) ? $_POST['comentario'] : '';

    $sql_valid = "INSERT INTO gls_jur_bil_validacion (comentario, estado_validacion, fecha_validacion, id_Termino, id_Usuario) 
                  VALUES (?, ?, NOW(), ?, ?)";
    $stmt_valid = $cn->prepare($sql_valid);

    $estado_valid = ($accion == 'aprobar') ? 'aprobado' : 'rechazado';
    if ($accion == 'rechazar' && empty($comentario)) {
        echo "<script>alert('Comentario requerido para rechazar.');</script>";
        exit();
    }
    $stmt_valid->bind_param("ssii", $comentario, $estado_valid, $id_Termino, $current_user['id']);
    $stmt_valid->execute();

    $sql_update = "UPDATE gls_jur_bil_termino SET estado = ?, fecha_modificacion = NOW() WHERE id_Termino = ?";
    $stmt_update = $cn->prepare($sql_update);
    $stmt_update->bind_param("si", $estado_valid, $id_Termino);
    $stmt_update->execute();

    header("Location: docente_revision.php");
}

$sql = "SELECT t.id_Termino, t.ejemplo_aplicativo, t.referencia_bibliogr, t.fecha_creacion,
        tes.palabra AS espanol, tes.definicion AS def_es,
        ten.palabra AS ingles, ten.definicion AS def_en,
        u.nombre AS propuesto_por
        FROM gls_jur_bil_termino t
        JOIN gls_jur_bil_usuario u ON t.id_Usuario = u.id_Usuario
        LEFT JOIN gls_jur_bil_traduccion tes ON t.id_Termino = tes.id_Termino AND tes.id_Idioma = 1
        LEFT JOIN gls_jur_bil_traduccion ten ON t.id_Termino = ten.id_Termino AND ten.id_Idioma = 2
        WHERE t.estado = 'pendiente'
        ORDER BY t.fecha_creacion DESC";
$result = $cn->query($sql);
$terms = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $terms[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisión Docente - Glosario Jurídico Bilingüe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --color-primary: #006694;
            --color-secondary: #27a5df;
            --color-accent1: #ffa606;
            --color-accent2: #ff9a15;
            --color-neutral: #636466;
            --color-light: #f1f2f2;
        }
        body {
            background: linear-gradient(to bottom, var(--color-accent1), var(--color-primary), var(--color-neutral), var(--color-accent2), var(--color-secondary), var(--color-light));
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--color-primary) 0%, darken(var(--color-primary), 20%) 100%);
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
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h1><i class="bi bi-shield-lock-fill"></i> <span>Docente Panel</span></h1>
        </div>
       
        <nav class="sidebar-nav">
            <!-- Dashboard -->
            <div class="menu-section">
                <a href="?seccion=dashboard" class="menu-item <?php echo $seccion == 'dashboard' ? 'active' : ''; ?>">
                    <i class="bi bi-grid-fill"></i> <span>Panel</span>
                </a>
            </div>
           
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
            <div class="menu-section">
                <div class="section-title">VALIDACIÓN</div>
                <ul class="menu-items">
                    <a href="?seccion=revision_terminos" class="menu-item <?php echo $seccion == 'revision_terminos' ? 'active' : ''; ?>">
                        <i class="bi bi-patch-check-fill"></i> <span>Revisar Términos</span>
                    </a>
                    <a href="?seccion=manage_validations" class="menu-item <?php echo $seccion == 'manage_validations' ? 'active' : ''; ?>">
                        <i class="bi bi-clipboard-check"></i> <span>Validaciones</span>
                    </a>
                </ul>
            </div>
           
            <!-- Navegación -->
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
    
    <!-- Contenido principal -->
    <div id="content">
        <div id="main-content-area">
            <?php
            switch($seccion) {
                case 'revision_terminos':
                    ?>
                    <h1 class="mb-4 text-center">Revisión de Términos - Docente</h1>
                    
                    <div class="alert alert-primary mb-4">
                        <i class="bi bi-info-circle"></i> Modo de revisión: Como docente, puedes aprobar o rechazar términos propuestos por alumnos.
                    </div>
                    
                    <h3 class="mb-3 text-primary">Lista de Términos Pendientes</h3>
                    <?php if (empty($terms)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No hay términos pendientes de revisión.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Término (ES)</th>
                                        <th>Término (EN)</th>
                                        <th>Propuesto por</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($terms as $row): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($row['espanol']) ?></strong>
                                            <br><small><?= htmlspecialchars($row['def_es']) ?></small>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($row['ingles']) ?></strong>
                                            <br><small><?= htmlspecialchars($row['def_en']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($row['propuesto_por']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['fecha_creacion'])) ?></td>
                                        <td>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="id_Termino" value="<?= $row['id_Termino'] ?>">
                                                <input type="hidden" name="accion" value="aprobar">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="bi bi-check"></i> Aprobar
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rechazoModal<?= $row['id_Termino'] ?>">
                                                <i class="bi bi-x"></i> Rechazar
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <div class="modal fade" id="rechazoModal<?= $row['id_Termino'] ?>">
                                        <div class="modal-dialog">
                                            <form method="POST">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Rechazar Término</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_Termino" value="<?= $row['id_Termino'] ?>">
                                                        <input type="hidden" name="accion" value="rechazar">
                                                        <p><strong><?= htmlspecialchars($row['espanol']) ?></strong> (<?= htmlspecialchars($row['ingles']) ?>)</p>
                                                        <div class="mb-3">
                                                            <label class="form-label"><strong>Razón del rechazo (obligatorio):</strong></label>
                                                            <textarea name="comentario" class="form-control" rows="3" placeholder="Ej: Error en definición, duplicado..." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-danger">Rechazar</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    <?php
                    break;
                
                default:
                    echo '<div class="alert alert-danger">Sección no encontrada.</div>';
            }
            ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmLogout() {
            if (confirm('¿Estás seguro de cerrar sesión?')) {
                // Lógica real de logout
            }
        }
    </script>
</body>
</html>