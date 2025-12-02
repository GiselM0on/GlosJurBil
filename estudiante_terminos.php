<?php
session_start();
include "conexion.php";

/*if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'estudiante' || !isset($_SESSION['id_Usuario'])) {
     header("Location: login.php");
   exit();
}*/

//$idUsuario = $_SESSION['id_Usuario'];

// Obtener términos del estudiante
$sql = "SELECT t.id_Termino, t.palabra, t.estado
        FROM termino t
        WHERE t.id_Usuario = ?";

$stmt = $cn->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}
$stmt->bind_param("i", $idUsuario);
if (!$stmt->execute()) {
    die("Error al ejecutar la consulta: " . $stmt->error);
}
$result = $stmt->get_result();
if ($result === false) {
    die("Error al obtener el resultado: " . $stmt->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Términos - Panel Estudiante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            color: white;
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

        #content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
            background-color: var(--color-gris-claro);
        }

        .status-pendiente { color: var(--color-gris); font-weight: bold; }
        .status-validado { color: green; font-weight: bold; }
        .status-rechazado { color: red; font-weight: bold; cursor: pointer; }

        .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }

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

        .table-responsive-container thead {
            background-color: var(--color-azul-oscuro);
        }

        .table-responsive-container th {
            color: white;
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

        .texto-limitado {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-contenido {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            .sidebar .menu-item span,
            .section-title {
                display: none;
            }
            .menu-item {
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

<div class="sidebar">
    <div class="logo">
        <h1><i class="bi bi-book-half"></i> <span>Panel Estudiante</span></h1>
    </div>
    
    <nav class="sidebar-nav">
        <div class="menu-section">
            <div class="section-title">TÉRMINOS</div>
            <ul class="menu-items">
                <a href="#agregar" class="menu-item" onclick="mostrarFormulario(0)">  <!-- 0 para nuevo -->
                    <i class="bi bi-plus-circle"></i> <span>Agregar Término</span>
                </a>
                <a href="estudiante_terminos.php" class="menu-item active">
                    <i class="bi bi-list-check"></i> <span>Mis Términos</span>
                </a>
            </ul>
        </div>
        
        <div class="menu-section nav-section">
            <div class="section-title">NAVEGACIÓN</div>
            <ul class="menu-items">
                <a href="index.php" class="menu-item">
                    <i class="bi bi-house-door-fill"></i> <span>Página Principal</span>
                </a>
                <a href="#" class="menu-item" onclick="confirmLogout()">
                    <i class="bi bi-box-arrow-right"></i> <span>Cerrar Sesión</span>
                </a>
            </ul>
        </div>
    </nav>
</div>

<div id="content">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-warning"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <h1 class="titulo-tabla-terminos">Mis Términos Enviados</h1>
    <div class="contenedor-tabla">
        <div class="table-responsive-container">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Palabra</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="texto-limitado"><?php echo htmlspecialchars($row['palabra']); ?></td>
                            <td class="status-<?php echo $row['estado']; ?>"><?php echo ucfirst($row['estado']); ?></td>
                            <td>
                                <?php if ($row['estado'] == 'rechazado'): ?>
                                    <button class="btn btn-warning btn-sm" onclick="abrirModalRazon(<?php echo $row['id_Termino']; ?>)">Ver Razón</button>
                                <?php endif; ?>
                                <?php if ($row['estado'] != 'validado'): ?>
                                    <button class="btn btn-info btn-sm" onclick="mostrarFormulario(<?php echo $row['id_Termino']; ?>)">Editar</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="no-results">No has enviado términos aún.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulario para Agregar/Modificar -->
    <div id="formularioTermino" class="form-container" style="display: none; margin-top: 20px;">
        <h2 id="tituloFormulario">Formulario de Gestión de Términos</h2>
        <form action="acciones_estudiante.php" method="POST">
            <input type="hidden" name="idTermino" id="idTerminoForm" value="0">

            <div class="mb-3">
                <label for="palabra">Palabra</label>
                <input type="text" class="form-control" name="palabra" id="palabra" required>
            </div>

            <div class="mb-3">
                <label for="pronunciacion">Pronunciación</label>
                <input type="text" class="form-control" name="pronunciacion" id="pronunciacion">
            </div>

            <div class="mb-3">
                <label for="definicion">Definición</label>
                <textarea class="form-control" name="definicion" id="definicion" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="ejemplo">Ejemplo Aplicativo</label>
                <textarea class="form-control" name="ejemplo" id="ejemplo" rows="2"></textarea>
            </div>

            <div class="mb-3">
                <label for="referencia">Referencia Bibliográfica</label>
                <input type="text" class="form-control" name="referencia" id="referencia">
            </div>

            <button type="submit" name="accion" value="guardar" class="btn btn-primary">Guardar</button>
            <button type="button" onclick="ocultarFormulario()" class="btn btn-secondary">Cancelar</button>
        </form>
    </div>

    <!-- Modal para Razón de Rechazo -->
    <div id="modalRazon" class="modal">
        <div class="modal-contenido">
            <h2>Razón del Rechazo</h2>
            <p id="razonTexto"></p>
            <button onclick="cerrarModalRazon()">Cerrar</button>
        </div>
    </div>
</div>

<script>
    function mostrarFormulario(id) {
        document.getElementById('formularioTermino').style.display = 'block';
        document.getElementById('idTerminoForm').value = id;
        document.getElementById('tituloFormulario').innerText = id == 0 ? 'Agregar Término' : 'Modificar Término';

        if (id != 0) {
            fetch("get_termino_estudiante.php?id=" + id)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        document.getElementById('palabra').value = data.palabra;
                        document.getElementById('pronunciacion').value = data.pronunciacion;
                        document.getElementById('definicion').value = data.definicion;
                        document.getElementById('ejemplo').value = data.ejemplo_aplicativo;
                        document.getElementById('referencia').value = data.referencia_bibliogr;
                    }
                });
        } else {
            // Limpiar para nuevo
            document.getElementById('palabra').value = '';
            document.getElementById('pronunciacion').value = '';
            document.getElementById('definicion').value = '';
            document.getElementById('ejemplo').value = '';
            document.getElementById('referencia').value = '';
        }
    }

    function ocultarFormulario() {
        document.getElementById('formularioTermino').style.display = 'none';
    }

    function abrirModalRazon(id) {
        fetch("get_termino_estudiante.php?id=" + id + "&razon=1")
            .then(res => res.json())
            .then(data => {
                document.getElementById('razonTexto').innerText = data.comentario || 'No hay razón especificada.';
                document.getElementById('modalRazon').style.display = 'flex';
            });
    }

    function cerrarModalRazon() {
        document.getElementById('modalRazon').style.display = 'none';
    }

    function confirmLogout() {
        if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
            window.location.href = 'logout.php';
        }
    }
</script>

</body>
</html>
<?php
$stmt->close();
$cn->close();
?>