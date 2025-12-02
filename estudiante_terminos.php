<?php
session_start();
include "conexion.php";


/*// Verificar que el usuario esté logueado y sea estudiante
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'estudiante') {
    header("Location: login.php");
    exit();
}*/



// Para insertar un término relacionado con este usuario:
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['termino'])) {
    include("conexion.php");
    
    $termino = $_POST['termino'];
    $descripcion = $_POST['descripcion'];
    
    $sql = "INSERT INTO terminos (id_usuario, termino, descripcion, fecha_creacion) 
            VALUES (?, ?, ?, NOW())";
    
    $stmt = $cn->prepare($sql);
    $stmt->bind_param("iss", $id_usuario, $termino, $descripcion);
    
    if ($stmt->execute()) {
        echo "<p>Término agregado exitosamente!</p>";
    } else {
        echo "<p>Error al agregar término: " . $stmt->error . "</p>";
    }
    
    $stmt->close();
    $cn->close();
}


// Obtener términos del estudiante
$sql = "SELECT t.id_Termino, t.palabra, t.estado
        FROM termino t
        WHERE t.id_Usuario = ?
        ORDER BY t.fecha_creacion DESC";

$stmt = $cn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'guardar') {
    $id = intval($_POST['idTermino']);
    $palabra = trim($_POST['palabra']);
    $pronunciacion = trim(isset($_POST['pronunciacion']) ? $_POST['pronunciacion'] : '');
    $definicion = trim($_POST['definicion']);
    $ejemplo = trim(isset($_POST['ejemplo']) ? $_POST['ejemplo'] : '');
    $referencia = trim(isset($_POST['referencia']) ? $_POST['referencia'] : '');
    $fecha = date("Y-m-d H:i:s");

    // Validaciones
    if (empty($palabra) || empty($definicion)) {
        $_SESSION['error'] = "Palabra y definición son obligatorios.";
        header("Location: estudiante_terminos.php");
        exit();
    }

    if ($id == 0) {
        // AGREGAR NUEVO TÉRMINO
        $stmt_insert = $cn->prepare("INSERT INTO termino (palabra, pronunciacion, definicion, ejemplo_aplicativo, referencia_bibliogr, estado, fecha_creacion, fecha_modificacion, id_Usuario) 
                                     VALUES (?, ?, ?, ?, ?, 'pendiente', ?, ?, ?)");
        
        if ($stmt_insert) {
            $stmt_insert->bind_param("sssssssi", $palabra, $pronunciacion, $definicion, $ejemplo, $referencia, $fecha, $fecha, $idUsuario);
            
            if ($stmt_insert->execute()) {
                $_SESSION['success'] = "Término agregado exitosamente y enviado para revisión.";
            } else {
                $_SESSION['error'] = "Error al guardar el término: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        } else {
            $_SESSION['error'] = "Error al preparar la consulta: " . $cn->error;
        }
        
    } else {
        // MODIFICAR TÉRMINO EXISTENTE
        // Verificar que el término existe y pertenece al estudiante
        $stmt_check = $cn->prepare("SELECT id_Termino FROM termino WHERE id_Termino = ? AND id_Usuario = ? AND estado != 'validado'");
        $stmt_check->bind_param("ii", $id, $idUsuario);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows == 0) {
            $_SESSION['error'] = "No puedes modificar este término (ya está validado o no te pertenece).";
            $stmt_check->close();
            header("Location: estudiante_terminos.php");
            exit();
        }
        $stmt_check->close();

        // Actualizar término
        $stmt_update = $cn->prepare("UPDATE termino SET palabra = ?, pronunciacion = ?, definicion = ?, ejemplo_aplicativo = ?, referencia_bibliogr = ?, estado = 'pendiente', fecha_modificacion = ? 
                                     WHERE id_Termino = ?");
        
        if ($stmt_update) {
            $stmt_update->bind_param("ssssssi", $palabra, $pronunciacion, $definicion, $ejemplo, $referencia, $fecha, $id);
            
            if ($stmt_update->execute()) {
                $_SESSION['success'] = "Término modificado exitosamente y reenviado para revisión.";
            } else {
                $_SESSION['error'] = "Error al modificar el término: " . $stmt_update->error;
            }
            $stmt_update->close();
        } else {
            $_SESSION['error'] = "Error al preparar la consulta de actualización: " . $cn->error;
        }
    }
    
    // Redirigir para evitar reenvío del formulario
    header("Location: estudiante_terminos.php");
    exit();
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

        .status-pendiente { 
            color: var(--color-naranja); 
            font-weight: bold; 
        }
        .status-validado { 
            color: green; 
            font-weight: bold; 
        }
        .status-rechazado { 
            color: red; 
            font-weight: bold; 
        }

        .form-container { 
            background: white; 
            padding: 25px; 
            border-radius: 10px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
            margin-top: 20px;
            border: 1px solid var(--color-gris);
        }

        .contenedor-tabla {
            margin-top: 2rem;
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid var(--color-gris);
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
            vertical-align: middle;
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
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-contenido {
            background-color: #fff;
            padding: 25px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
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
        
        .btn-primary {
            background-color: var(--color-amarillo);
            border-color: var(--color-amarillo);
            color: var(--color-azul-oscuro);
            font-weight: 600;
            padding: 8px 20px;
        }
        
        .btn-primary:hover {
            background-color: #ff8a3d;
            border-color: #ff8a3d;
            color: var(--color-azul-oscuro);
        }
        
        .btn-info {
            background-color: var(--color-azul-claro);
            border-color: var(--color-azul-claro);
            color: white;
            padding: 5px 15px;
            font-size: 0.875rem;
        }
        
        .btn-info:hover {
            background-color: #1e8bc4;
            border-color: #1e8bc4;
            color: white;
        }
        
        .btn-warning {
            background-color: var(--color-naranja);
            border-color: var(--color-naranja);
            color: white;
            padding: 5px 15px;
            font-size: 0.875rem;
        }
        
        .alert-success {
            background-color: rgba(0, 102, 148, 0.1);
            border-color: var(--color-azul-oscuro);
            color: var(--color-azul-oscuro);
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: #dc3545;
            color: #dc3545;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--color-azul-oscuro);
            margin-bottom: 5px;
        }
        
        .form-control, .form-select {
            border: 1px solid var(--color-gris);
            border-radius: 5px;
            padding: 8px 12px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--color-amarillo);
            box-shadow: 0 0 0 0.2rem rgba(255, 160, 109, 0.25);
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
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
                <li>
                    <a href="#agregar" class="menu-item" onclick="mostrarFormulario(0)">
                        <i class="bi bi-plus-circle"></i> <span>Agregar Término</span>
                    </a>
                </li>
                <li>
                    <a href="estudiante_terminos.php" class="menu-item active">
                        <i class="bi bi-list-check"></i> <span>Mis Términos</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="menu-section nav-section">
            <div class="section-title">NAVEGACIÓN</div>
            <ul class="menu-items">
                <li>
                    <a href="index.php" class="menu-item">
                        <i class="bi bi-house-door-fill"></i> <span>Página Principal</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-item" onclick="confirmLogout()">
                        <i class="bi bi-box-arrow-right"></i> <span>Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>

<div id="content">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h1 class="titulo-tabla-terminos">Mis Términos Enviados</h1>
    <div class="contenedor-tabla">
        <div class="table-responsive-container">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Palabra</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $row['id_Termino']; ?></strong></td>
                            <td class="texto-limitado"><?php echo htmlspecialchars($row['palabra']); ?></td>
                            <td>
                                <?php 
                                $estado_texto = '';
                                $estado_clase = '';
                                switch($row['estado']) {
                                    case 'pendiente': 
                                        $estado_texto = 'Pendiente'; 
                                        $estado_clase = 'badge bg-warning text-dark';
                                        break;
                                    case 'validado': 
                                        $estado_texto = 'Aprobado'; 
                                        $estado_clase = 'badge bg-success';
                                        break;
                                    case 'rechazado': 
                                        $estado_texto = 'Rechazado'; 
                                        $estado_clase = 'badge bg-danger';
                                        break;
                                    default: 
                                        $estado_texto = ucfirst($row['estado']);
                                        $estado_clase = 'badge bg-secondary';
                                }
                                ?>
                                <span class="<?php echo $estado_clase; ?>"><?php echo $estado_texto; ?></span>
                            </td>
                            <td>
                                <?php if ($row['estado'] == 'rechazado'): ?>
                                    <button class="btn btn-warning btn-sm" onclick="abrirModalRazon(<?php echo $row['id_Termino']; ?>)">
                                        <i class="bi bi-eye"></i> Ver Razón
                                    </button>
                                <?php endif; ?>
                                <?php if ($row['estado'] != 'validado'): ?>
                                    <button class="btn btn-info btn-sm" onclick="mostrarFormulario(<?php echo $row['id_Termino']; ?>)">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No has enviado términos aún. ¡Agrega tu primer término!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulario para Agregar/Modificar -->
    <div id="formularioTermino" class="form-container" style="display: none;">
        <h2 id="tituloFormulario" class="mb-4 text-primary">Agregar Nuevo Término</h2>
        <form method="POST" id="formTermino">
            <input type="hidden" name="idTermino" id="idTerminoForm" value="0">
            <input type="hidden" name="accion" value="guardar">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="palabra" class="form-label">Palabra *</label>
                    <input type="text" class="form-control" name="palabra" id="palabra" required 
                           placeholder="Ingresa la palabra o término">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="pronunciacion" class="form-label">Pronunciación</label>
                    <input type="text" class="form-control" name="pronunciacion" id="pronunciacion"
                           placeholder="Pronunciación fonética (opcional)">
                </div>
            </div>

            <div class="mb-3">
                <label for="definicion" class="form-label">Definición *</label>
                <textarea class="form-control" name="definicion" id="definicion" rows="3" required 
                          placeholder="Define claramente el término"></textarea>
            </div>

            <div class="mb-3">
                <label for="ejemplo" class="form-label">Ejemplo Aplicativo</label>
                <textarea class="form-control" name="ejemplo" id="ejemplo" rows="2"
                          placeholder="Ejemplo de uso en contexto (opcional)"></textarea>
            </div>

            <div class="mb-3">
                <label for="referencia" class="form-label">Referencia Bibliográfica</label>
                <input type="text" class="form-control" name="referencia" id="referencia"
                       placeholder="Fuente bibliográfica (opcional)">
            </div>

            <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">ID Usuario</label>
                        <input type="number" class="form-control form-control-sm" name="txtid_usuario" 
                               value="<?php echo htmlspecialchars($id_usuario, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Guardar y Enviar
                </button>
                <button type="button" onclick="ocultarFormulario()" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
            </div>
            
            <small class="text-muted d-block mt-3">* Campos obligatorios. El término será enviado para revisión del docente.</small>
        </form>
    </div>

    <!-- Modal para Razón de Rechazo -->
    <div id="modalRazon" class="modal">
        <div class="modal-contenido">
            <h2 class="mb-3">Razón del Rechazo</h2>
            <div class="alert alert-warning">
                <p id="razonTexto" class="mb-0">Cargando...</p>
            </div>
            <div class="text-center mt-3">
                <button onclick="cerrarModalRazon()" class="btn btn-secondary">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
    function mostrarFormulario(id) {
        document.getElementById('formularioTermino').style.display = 'block';
        document.getElementById('idTerminoForm').value = id;
        document.getElementById('tituloFormulario').innerText = id == 0 ? 'Agregar Nuevo Término' : 'Modificar Término';
        
        // Scroll al formulario
        document.getElementById('formularioTermino').scrollIntoView({ behavior: 'smooth' });

        if (id != 0) {
            // Cargar datos del término para editar
            fetch("get_termino_estudiante.php?id=" + id)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Error en la petición: ' + res.status);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        document.getElementById('palabra').value = data.palabra || '';
                        document.getElementById('pronunciacion').value = data.pronunciacion || '';
                        document.getElementById('definicion').value = data.definicion || '';
                        document.getElementById('ejemplo').value = data.ejemplo_aplicativo || '';
                        document.getElementById('referencia').value = data.referencia_bibliogr || '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos del término');
                });
        } else {
            // Limpiar para nuevo término
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
            .then(res => {
                if (!res.ok) {
                    throw new Error('Error en la petición: ' + res.status);
                }
                return res.json();
            })
            .then(data => {
                document.getElementById('razonTexto').innerText = data.comentario || 'No hay razón especificada.';
                document.getElementById('modalRazon').style.display = 'flex';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('razonTexto').innerText = 'Error al cargar la razón.';
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

    // Validación del formulario antes de enviar
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formTermino');
        if (form) {
            form.addEventListener('submit', function(e) {
                const palabra = document.getElementById('palabra').value.trim();
                const definicion = document.getElementById('definicion').value.trim();
                
                if (!palabra || !definicion) {
                    e.preventDefault();
                    alert('Por favor, completa los campos obligatorios: Palabra y Definición.');
                    return false;
                }
                
                // Confirmación para nuevo término
                const idTermino = document.getElementById('idTerminoForm').value;
                if (idTermino == 0) {
                    if (!confirm('¿Estás seguro de enviar este término para revisión?')) {
                        e.preventDefault();
                        return false;
                    }
                } else {
                    if (!confirm('¿Estás seguro de modificar y reenviar este término para revisión?')) {
                        e.preventDefault();
                        return false;
                    }
                }
                
                return true;
            });
        }
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('modalRazon');
            if (event.target === modal) {
                cerrarModalRazon();
            }
        }
    });
</script>

</body>
</html>
<?php
$stmt->close();
$cn->close();
?>