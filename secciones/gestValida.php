<?php
// secciones/gestion_validaciones.php

//conexion del db
include ("conexion.php");

// Variables para los campos
$id_validacion = "";
$comentario = "";
$estado_validacion = "";
$fecha_validacion = "";
$id_termino = "";
$id_usuario = "";
$txtbus = "";

// Procesar BÚSQUEDA 
if(isset($_POST["btn_buscar"]) && $_POST["btn_buscar"] == "Buscar"){
    if(isset($_POST["txtbus"]) && !empty($_POST["txtbus"])){
        $txtbus = $_POST["txtbus"];
        
        $sql = "SELECT * FROM validacion WHERE id_validacion='$txtbus'";
        $cs = mysqli_query($cn, $sql);
        if($cs && mysqli_num_rows($cs) > 0) {
            $resul = mysqli_fetch_array($cs);
            $id_validacion = $resul[0];
            $comentario = $resul[1];
            $estado_validacion = $resul[2];
            $fecha_validacion = $resul[3];
            $id_termino = $resul[4];
            $id_usuario = $resul[5];
            echo "<script>alert('Validación encontrada');</script>";
        } else {
            echo "<script>alert('Validación no encontrada');</script>";
            // Limpiar campos si no se encuentra
            $id_validacion = $comentario = $estado_validacion = $fecha_validacion = $id_termino = $id_usuario = "";
        }
    } else {
        echo "<script>alert('Ingrese un ID para buscar');</script>";
    }
}

// Procesar formulario PRINCIPAL (Agregar, Modificar, Eliminar)
if(isset($_POST["btn_validaciones"])){
    $btn = $_POST["btn_validaciones"];
    
    // AGREGAR
    if($btn == "Agregar"){
        $id_validacion = $_POST["txtid_validacion"];
        $comentario = $_POST["txtcomentario"];
        $estado_validacion = $_POST["txtestado_validacion"];
        $id_termino = $_POST["txtid_termino"];
        $id_usuario = $_POST["txtid_usuario"];
        
        $sql = "INSERT INTO validacion (id_validacion,comentario, estado_validacion, fecha_validacion, id_Termino, id_Usuario) 
                VALUES ('$id_validacion','$comentario','$estado_validacion', NOW(), '$id_termino','$id_usuario')";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Validación agregada correctamente');</script>";
            // Limpiar campos
            $id_validacion = $comentario = $estado_validacion = $fecha_validacion = $id_termino = $id_usuario = $txtbus = "";
        } else {
            echo "<script>alert('Error al agregar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // MODIFICAR
    if($btn == "Modificar" && !empty($_POST["txtid_validacion"])){
        $id_validacion = $_POST["txtid_validacion"];
        $comentario = $_POST["txtcomentario"];
        $estado_validacion = $_POST["txtestado_validacion"];
        $id_termino = $_POST["txtid_termino"];
        $id_usuario = $_POST["txtid_usuario"];
        
        $sql = "UPDATE validacion SET 
                comentario='$comentario',
                estado_validacion='$estado_validacion',
                fecha_validacion=NOW(),
                id_Termino='$id_termino',
                id_Usuario='$id_usuario'
                WHERE id='$id_validacion'";
        
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Validación modificada correctamente');</script>";
        } else {
            echo "<script>alert('Error al modificar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // ELIMINAR
    if($btn == "Eliminar" && !empty($_POST["txtid_validacion"])){
        $id_validacion = $_POST["txtid"];
        
        $sql = "DELETE FROM validacion WHERE id='$id_validacion'";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Validación eliminada correctamente');</script>";
            // Limpiar campos
            $id_validacion = $comentario = $estado_validacion = $fecha_validacion = $id_termino = $id_usuario = $txtbus = "";
        } else {
            echo "<script>alert('Error al eliminar: " . mysqli_error($cn) . "');</script>";
        }
    }
}
?>

<h1 class="mb-4 text-primary">Gestión de Validaciones</h1>

<div class="card p-4 shadow-sm mb-4 bg-light">
    <h3 class="card-title text-center text-dark">Formulario de Gestión de Validaciones</h3>
    
    <!-- FORMULARIO SEPARADO PARA BÚSQUEDA -->
    <form method="POST" class="mb-4 border-bottom pb-3">
        <div class="row">
            <div class="col-md-8">
                <input type="text" class="form-control" name="txtbus" placeholder="ID de la validación a buscar" 
                       value="<?php echo htmlspecialchars($txtbus); ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary w-100" name="btn_buscar" value="Buscar">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>
        </div>
    </form>

    <!-- FORMULARIO PRINCIPAL PARA CRUD -->
    <form method="POST">
        <!-- Campos del formulario -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">ID Validación</label>
                <input type="text" class="form-control" name="txtid_validacion" value="<?php echo htmlspecialchars($id_validacion); ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Estado Validación</label>
                 <input type="text" class="form-control" name="txtestado_validacion" value="<?php echo htmlspecialchars($estado_validacion); ?>" required>
            
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">ID Término</label>
                <input type="number" class="form-control" name="txtid_termino" value="<?php echo htmlspecialchars($id_termino); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">ID Usuario</label>
                <input type="number" class="form-control" name="txtid_usuario" value="<?php echo htmlspecialchars($id_usuario); ?>" required>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <label class="form-label">Comentario</label>
                <textarea class="form-control" name="txtcomentario" rows="3" required><?php echo htmlspecialchars($comentario); ?></textarea>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="text-center">
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_validaciones" value="Agregar">
                <i class="bi bi-plus-lg"></i> Agregar
            </button>
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_validaciones" value="Modificar">
                <i class="bi bi-pencil"></i> Modificar
            </button>
            <button type="submit" class="btn btn-outline-primary" name="btn_validaciones" value="Eliminar" 
                    onclick="return confirm('¿Estás seguro de eliminar esta validación?')">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </div>
    </form>
</div>

<!-- Mostrar lista de validaciones -->
<?php
$query_validaciones = "SELECT v.*, t.nombreTer as termino, u.nombre as usuario 
                       FROM validacion v 
                       LEFT JOIN termino t ON v.id_Termino = t.id 
                       LEFT JOIN usuario u ON v.id_Usuario = u.id 
                       ORDER BY v.id DESC";
$result_validaciones = mysqli_query($cn, $query_validaciones);
?>

<h3 class="mb-3 text-primary">Validaciones Registradas</h3>
<div class="table-responsive">
    <table class="table table-striped align-middle mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Comentario</th>
                <th>Estado</th>
                <th>Término</th>
                <th>Usuario</th>
                <th>Fecha Validación</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_validaciones && mysqli_num_rows($result_validaciones) > 0): ?>
                <?php while ($validacion = mysqli_fetch_assoc($result_validaciones)): ?>
                    <tr>
                        <td><?php echo $validacion['id']; ?></td>
                        <td><?php echo htmlspecialchars(substr($validacion['comentario'], 0, 50)) . '...'; ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $validacion['estado_validacion'] == 'aprobado' ? 'success' : 
                                     ($validacion['estado_validacion'] == 'pendiente' ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo ucfirst($validacion['estado_validacion']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($validacion['termino']); ?></td>
                        <td><?php echo htmlspecialchars($validacion['usuario']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($validacion['fecha_validacion'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No hay validaciones registradas</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>