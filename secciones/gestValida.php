<?php
// secciones/gestion_validaciones.php

//conexion del db
include ("conexion.php");


// Configurar charset para la conexión
if (isset($cn) && is_object($cn)) {
    mysqli_set_charset($cn, "utf8mb4");
}

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
    // CAMBIO 1: Se usa txtid_validacion para la clave
    if($btn == "Modificar" && !empty($_POST["txtid_validacion"])){
        $id_validacion = $_POST["txtid_validacion"]; // <-- Usar el ID del formulario para identificar
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
                 WHERE id_validacion='$id_validacion'"; // <-- CLÁUSULA WHERE CORREGIDA: Usar id_validacion
        
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Validación modificada correctamente');</script>";
            // Limpiar campos
            $id_validacion = $comentario = $estado_validacion = $fecha_validacion = $id_termino = $id_usuario = $txtbus = "";
        } else {
            echo "<script>alert('Error al modificar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // ELIMINAR
    // CAMBIO 2: Se usa txtid_validacion para la clave
    if($btn == "Eliminar" && !empty($_POST["txtid_validacion"])){
        $id_validacion = $_POST["txtid_validacion"]; // <-- Usar el ID del formulario para identificar
        
        $sql = "DELETE FROM validacion WHERE id_validacion='$id_validacion'"; // <-- CLÁUSULA WHERE CORREGIDA: Usar id_validacion
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

    <form method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">ID Validación</label>
                <input type="text" class="form-control" name="txtid_validacion" value="<?php echo htmlspecialchars($id_validacion); ?>" 
                readonly  style="background-color: #e9ecef; cursor: not-allowed;">
            </div>
            <div class="col-md-6">
                <label class="form-label">Estado Validación</label>
                 <input type="text" class="form-control" name="txtestado_validacion" value="<?php echo htmlspecialchars($estado_validacion); ?>" >
            
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">ID Término</label>
                <input type="number" class="form-control" name="txtid_termino" value="<?php echo htmlspecialchars($id_termino); ?>" >
            </div>
            <div class="col-md-6">
                <label class="form-label">ID Usuario</label>
                <input type="number" class="form-control" name="txtid_usuario" value="<?php echo htmlspecialchars($id_usuario); ?>" >
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <label class="form-label">Comentario</label>
                <textarea class="form-control" name="txtcomentario" rows="3" ><?php echo htmlspecialchars($comentario); ?></textarea>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_validaciones" value="Agregar">
                <i class="bi bi-plus-lg"></i> Agregar
            </button>
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_validaciones" value="Mostrar">
                <i class="bi bi-eye"></i> Mostrar
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

<div class="data-container mt-4">
    <?php
    if(isset($_POST["btn_validaciones"]) && $_POST["btn_validaciones"] == "Mostrar"){
        $sql="SELECT v.*, t.palabra as termino, u.nombre as usuario 
              FROM validacion v 
              LEFT JOIN termino t ON v.id_Termino = t.id_termino 
              LEFT JOIN usuario u ON v.id_Usuario = u.id_usuario 
              ORDER BY v.id_validacion DESC";
        $cs=mysqli_query($cn,$sql);
        if($cs && mysqli_num_rows($cs) > 0) {
           echo "<div class='contenedor-tabla'>";
            echo "<h3 class='titulo-tabla-terminos mb-4 text-primary'>Lista de Validaciones</h3>";
            echo "<div class='table-responsive-container'>";
            echo "<table class='table table-hover mb-0'>";
            echo "<thead>
                    <tr>
                        <th width='80'>ID</th>
                        <th width='200'>Comentario</th>
                        <th width='120'>Estado</th>
                        <th width='120'>Fecha</th>
                        <th width='120'>Término</th>
                        <th width='120'>Usuario</th>
                    </tr>
                </thead>";
            echo "<tbody>";
            while($resul=mysqli_fetch_array($cs)){
                $id_validacion = $resul[0];
                $comentario = substr($resul[1], 0, 100) . (strlen($resul[1]) > 100 ? '...' : '');
                $estado_validacion = $resul[2];
                $fecha_validacion = date('d/m/Y', strtotime($resul[3]));
                
                // Corregido: usando isset() en lugar de ??
                $termino = isset($resul['termino']) ? $resul['termino'] : 'N/A';
                $usuario = isset($resul['usuario']) ? $resul['usuario'] : 'N/A';
                
                // Determinar clase del badge según el estado
                $badge_class = '';
                if($estado_validacion == 'aprobado') $badge_class = 'status-active';
                elseif($estado_validacion == 'pendiente') $badge_class = 'status-pending';
                elseif($estado_validacion == 'rechazado') $badge_class = 'badge-estado-rechazado';
                
                echo "<tr>
                <td data-label='ID'><strong>$id_validacion</strong></td>
                <td data-label='Comentario'><div class='texto-limitado'>$comentario</div></td>
                <td data-label='Estado'><span class='status-badge $badge_class'>" . ucfirst($estado_validacion) . "</span></td>
                <td data-label='Fecha'><small>$fecha_validacion</small></td>
                <td data-label='Término'>$termino</td>
                <td data-label='Usuario'>$usuario</td>
            </tr>";
            }

            echo "</tbody>";
            echo "</table>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class='alert alert-info text-center'>No hay validaciones registradas</div>";
        }
    }
    ?>
</div>