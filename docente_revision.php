<?php
session_start();
include "conexion.php";

// Verificar que el docente esté logueado
/*if (!isset($_SESSION['id_Usuario']) || $_SESSION['rol'] !== 'docente') {
    header("Location: login.php");
    exit();
}*/

// Obtener términos pendientes de revisión
$sql = "SELECT t.id_Termino, t.palabra, u.nombre AS estudiante, t.fecha_creacion
        FROM termino t
        INNER JOIN usuario u ON u.id_Usuario = t.id_Usuario
        WHERE t.estado = 'pendiente'
        ORDER BY t.fecha_creacion ASC";

$result = $cn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Revisión de términos - Panel Docente</title>
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
    padding: 30px;
    min-height: 100vh;
    transition: all 0.3s;
    background-color: var(--color-gris-claro);
}

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
    background-color: white;
    border: 1px solid var(--color-gris);
}

.table-responsive-container table {
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

.table-responsive-container tbody tr:hover {
    background-color: rgba(255, 160, 109, 0.1);
}

.texto-limitado {
    max-width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* ESTILOS MODAL */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    justify-content: center;
    align-items: center;
}

.modal-contenido {
    background: white;
    padding: 30px;
    width: 90%;
    max-width: 500px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    position: relative;
    border: 1px solid var(--color-gris-claro);
}

.modal-contenido h2 {
    color: var(--color-azul-oscuro);
    font-size: 20px;
    margin-bottom: 15px;
    border-bottom: 2px solid var(--color-amarillo);
    padding-bottom: 10px;
}

.modal-contenido p {
    font-size: 14px;
    margin-bottom: 10px;
    line-height: 1.5;
    color: var(--color-azul-oscuro);
}

.modal-contenido label {
    display: block;
    font-size: 14px;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--color-azul-oscuro);
}

.modal-contenido textarea {
    width: 100%;
    border: 1px solid var(--color-gris-claro);
    padding: 10px;
    border-radius: 4px;
    font-size: 14px;
    resize: vertical;
    min-height: 80px;
    margin-bottom: 20px;
}

.modal-botones {
    display: flex;
    gap: 10px;
    justify-content: space-between;
    margin-top: 20px;
}

.btn-validar {
    background: var(--color-azul-claro);
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    flex: 1;
}

.btn-validar:hover {
    background: var(--color-azul-oscuro);
}

.btn-rechazar {
    background: var(--color-naranja);
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    flex: 1;
}

.btn-rechazar:hover {
    background: #e68913;
}

.btn-rechazar:disabled {
    background: var(--color-gris);
    cursor: not-allowed;
    opacity: 0.6;
}

.btn-cerrar {
    background: var(--color-gris-claro);
    color: var(--color-azul-oscuro);
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    width: 100%;
    margin-top: 10px;
}

.btn-cerrar:hover {
    background: #e0e0e0;
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
</style>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <h1><i class="bi bi-shield-lock-fill"></i> <span> Panel Docente </span></h1>
    </div>
    
    <nav class="sidebar-nav">
        <div class="menu-section">
            <div class="section-title">VALIDACIÓN</div>
            <ul class="menu-items">
                <li>
                    <a href="docente_revision.php" class="menu-item active">
                        <i class="bi bi-clipboard-check"></i> <span>Revisión de Términos</span>
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
    <h1 class="titulo-tabla-terminos">Términos pendientes de revisión</h1>
    
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
    
    <div class="contenedor-tabla">
        <div class="table-responsive-container">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Palabra</th>
                        <th>Estudiante</th>
                        <th>Fecha de Envío</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No hay términos pendientes de revisión.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id_Termino']; ?></td>
                            <td class="texto-limitado"><?php echo htmlspecialchars($row['palabra']); ?></td>
                            <td><?php echo htmlspecialchars($row['estudiante']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_creacion'])); ?></td>
                            <td>
                                <button class="btn btn-info btn-sm revisar-btn" 
                                        data-id="<?php echo $row['id_Termino']; ?>">
                                    <i class="bi bi-eye"></i> Revisar
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL DE REVISIÓN -->
<div id="modalRevision" class="modal">
    <div class="modal-contenido">
        <h2 id="tituloTermino">Cargando...</h2>
        
        <div class="mb-3">
            <strong>Definición:</strong>
            <p id="descTermino" style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 5px;"></p>
        </div>
        
        <div class="mb-3">
            <strong>Enviado por:</strong> 
            <span id="nombreEstudiante" class="badge bg-secondary">Cargando...</span>
        </div>

        <form action="acciones_docente.php" method="POST" id="formValidacion">
            <input type="hidden" name="idTermino" id="idTerminoInput">

            <div class="mb-3">
                <label for="motivo"><b>Motivo del rechazo</b> (obligatorio si rechaza):</label>
                <textarea id="motivo" name="motivo" rows="3"
                        placeholder="Escribe aquí el motivo del rechazo..."
                        class="form-control"></textarea>
            </div>

            <div class="modal-botones">
                <button type="submit" name="accion" value="validar" class="btn-validar">
                    <i class="bi bi-check-circle"></i> Validar
                </button>
                <button type="submit" name="accion" value="rechazar" id="btnRechazar" class="btn-rechazar" disabled>
                    <i class="bi bi-x-circle"></i> Rechazar
                </button>
            </div>
            
            <button type="button" onclick="cerrarModal()" class="btn-cerrar">
                <i class="bi bi-x-lg"></i> Cancelar
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
// Función para abrir el modal
function abrirModal(id) {
    console.log("Abriendo modal para término ID:", id);
    
    // Mostrar el modal
    document.getElementById("modalRevision").style.display = "flex";
    document.getElementById("tituloTermino").innerText = "Cargando...";
    document.getElementById("descTermino").innerText = "";
    document.getElementById("nombreEstudiante").innerText = "Cargando...";
    document.getElementById("idTerminoInput").value = id;
    
    // Limpiar textarea
    document.getElementById("motivo").value = "";
    document.getElementById("btnRechazar").disabled = true;
    
    // Hacer la petición AJAX para obtener detalles
    fetch("get_termino.php?id=" + id)
        .then(res => {
            if (!res.ok) {
                throw new Error('Error en la petición: ' + res.status);
            }
            return res.json();
        })
        .then(data => {
            console.log("Datos recibidos:", data);
            
            if (data.error) {
                alert("Error: " + data.error);
                cerrarModal();
                return;
            }
            
            // Actualizar el modal con los datos
            document.getElementById("tituloTermino").innerText = data.palabra || "Sin título";
            document.getElementById("descTermino").innerText = data.definicion || "Sin definición";
            document.getElementById("nombreEstudiante").innerText = data.estudiante || "Desconocido";
        })
        .catch(error => {
            console.error("Error al cargar término:", error);
            alert("Error al cargar el término. Verifica la consola para más detalles.");
            document.getElementById("tituloTermino").innerText = "Error";
            document.getElementById("descTermino").innerText = "No se pudo cargar la información.";
        });
}

// Función para cerrar el modal
function cerrarModal() {
    document.getElementById("modalRevision").style.display = "none";
    document.getElementById("motivo").value = "";
    document.getElementById("btnRechazar").disabled = true;
}

// Habilitar botón de rechazar cuando se escribe en el textarea
document.addEventListener('DOMContentLoaded', function() {
    const motivoTextarea = document.getElementById('motivo');
    if (motivoTextarea) {
        motivoTextarea.addEventListener('input', function() {
            const texto = this.value.trim();
            document.getElementById("btnRechazar").disabled = texto === "";
        });
    }
    
    // Asignar evento a todos los botones de revisar
    const botones = document.querySelectorAll('.revisar-btn');
    botones.forEach(boton => {
        boton.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            abrirModal(id);
        });
    });
    
    // Cerrar modal al hacer clic fuera
    const modal = document.getElementById("modalRevision");
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            cerrarModal();
        }
    });
    
    // Cerrar con ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModal();
        }
    });
});

// Función para cerrar sesión
function confirmLogout() {
    if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
        window.location.href = 'logout.php';
    }
}
</script>

</body>
</html>
<?php
$cn->close();
?>