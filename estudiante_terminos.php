<?php
session_start();
include "conexion.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'estudiante') {
    header("Location: login.php");
    exit();
}

$idUsuario = $_SESSION['id_Usuario'];

// Obtener términos del estudiante
$sql = "SELECT t.id_Termino, t.palabra, t.estado
        FROM termino t
        WHERE t.id_Usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
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
        
        .status-pendiente { color: var(--color-gris); font-weight: bold; }
        .status-validado { color: green; font-weight: bold; }
        .status-rechazado { color: red; font-weight: bold; cursor: pointer; }
    
        .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
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
                    document.getElementById('palabra').value = data.palabra;
                    document.getElementById('pronunciacion').value = data.pronunciacion;
                    document.getElementById('definicion').value = data.definicion;
                    document.getElementById('ejemplo').value = data.ejemplo_aplicativo;
                    document.getElementById('referencia').value = data.referencia_bibliogr;
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