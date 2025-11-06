<?php

//conexion a la db
include ("conexion.php");

function obtenerEstadisticasDashboard($cn) {
    $estadisticas = [];
    
    // Términos pendientes de validación
    $query_pendientes = "SELECT COUNT(*) as total FROM termino WHERE estado = 'pendiente'";
    $result_pendientes = mysqli_query($cn, $query_pendientes);
    $estadisticas['pendientes'] = $result_pendientes ? mysqli_fetch_assoc($result_pendientes)['total'] : 0;
    
    // Total de usuarios
    $query_usuarios = "SELECT COUNT(*) as total FROM usuario";
    $result_usuarios = mysqli_query($cn, $query_usuarios);
    $estadisticas['usuarios'] = $result_usuarios ? mysqli_fetch_assoc($result_usuarios)['total'] : 0;
    
    // Términos aprobados
    $query_aprobados = "SELECT COUNT(*) as total FROM termino WHERE estado = 'aprobado'";
    $result_aprobados = mysqli_query($cn, $query_aprobados);
    $estadisticas['aprobados'] = $result_aprobados ? mysqli_fetch_assoc($result_aprobados)['total'] : 0;
    
    return $estadisticas;
}

function obtenerActividadReciente($cn) {
    $actividad = [];
    $query = "SELECT t.id, t.nombreTer as termino, u.nombre as usuario, t.estado, t.fecha_creacion
              FROM termino t JOIN usuario u ON t.id_Usuario = u.id
              ORDER BY t.fecha_creacion DESC LIMIT 5";
    
    $result = mysqli_query($cn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $actividad[] = $row;
        }
    }
    return $actividad;
}



$estadisticas = obtenerEstadisticasDashboard($cn);
$actividad_reciente = obtenerActividadReciente($cn);

?>

<h1 class="mb-4 text-center">Panel de Control de Administrador</h1>

<div class="row g-4 mb-5">
    <div class="col-lg-4 col-md-6">
        <div class="card admin-card p-4 shadow-sm" onclick="window.location.href='?seccion=review_terms'">
            <div class="d-flex align-items-center">
                <i class="bi bi-patch-exclamation-fill icon-large me-3"></i>
                <div>
                    <p class="text-muted fw-bold mb-0">Pendientes de Validación</p>
                    <h2 class="display-5 fw-bold text-primary"><?php echo $estadisticas['pendientes']; ?></h2>
                </div>
            </div>
            <small class="mt-2 text-primary">Términos propuestos por usuarios.</small>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card admin-card p-4 shadow-sm" onclick="window.location.href='?seccion=manage_users'">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-gear icon-large me-3"></i>
                <div>
                    <p class="text-muted fw-bold mb-0">Total de Usuarios</p>
                    <h2 class="display-5 fw-bold text-primary"><?php echo $estadisticas['usuarios']; ?></h2>
                </div>
            </div>
            <small class="mt-2 text-primary">Gestionar cuentas (Docentes/Estudiantes).</small>
        </div>
    </div>

    <div class="col-lg-4 col-md-12">
        <div class="card admin-card p-4 shadow-sm" onclick="window.location.href='?seccion=manage_terms'">
            <div class="d-flex align-items-center">
                <i class="bi bi-journals icon-large me-3"></i>
                <div>
                    <p class="text-muted fw-bold mb-0">Términos Aprobados</p>
                    <h2 class="display-5 fw-bold text-primary"><?php echo $estadisticas['aprobados']; ?></h2>
                </div>
            </div>
            <small class="mt-2 text-primary">Crear, Editar o Eliminar cualquier término.</small>
        </div>
    </div>
</div>

<h3 class="mb-3 text-primary">Actividad Reciente (Últimas Acciones)</h3>
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th scope="col">Término</th>
                <th scope="col">Usuario</th>
                <th scope="col">Estado</th>
                <th scope="col">Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($actividad_reciente)): ?>
                <?php foreach ($actividad_reciente as $actividad): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($actividad['termino']); ?></td>
                        <td><?php echo htmlspecialchars($actividad['usuario']); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $actividad['estado'] == 'aprobado' ? 'success' : 
                                     ($actividad['estado'] == 'pendiente' ? 'warning' : 'secondary'); 
                            ?> rounded-pill">
                                <?php echo ucfirst($actividad['estado']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($actividad['fecha_creacion'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No hay actividad reciente</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>