<?php
// secciones/gestion_usuarios.php

//conexion del db
include ("conexion.php");


// Configurar charset para la conexión
if (isset($cn) && is_object($cn)) {
    mysqli_set_charset($cn, "utf8mb4");
}

// Variables para los campos
$id_usuario = "";
$nombre = "";
$correo = "";
$contrasena = "";
$rol = "";
$fecha_registro = "";
$txtbus = "";

// Procesar BÚSQUEDA (formulario separado)
if(isset($_POST["btn_buscar"]) && $_POST["btn_buscar"] == "Buscar"){
    if(isset($_POST["txtbus"]) && !empty($_POST["txtbus"])){
        $txtbus = $_POST["txtbus"];
        
        $sql = "SELECT * FROM usuario WHERE id_usuario='$txtbus'";
        $cs = mysqli_query($cn, $sql);
        if($cs && mysqli_num_rows($cs) > 0) {
            $resul = mysqli_fetch_array($cs);
            $id_usuario = $resul[0];
            $nombre = $resul[1];
            $correo = $resul[2];
            $contrasena = $resul[3];
            $rol = $resul[4];
            $fecha_registro = $resul[5];
            echo "<script>alert('Usuario encontrado');</script>";
        } else {
            echo "<script>alert('Usuario no encontrado');</script>";
            // Limpiar campos si no se encuentra
            $id_usuario = $nombre = $correo = $contrasena = $rol = $fecha_registro = "";
        }
    } else {
        echo "<script>alert('Ingrese un ID para buscar');</script>";
    }
}

// Procesar formulario PRINCIPAL (Agregar, Modificar, Eliminar, Mostrar)
if(isset($_POST["btn_usuarios"])){
    $btn = $_POST["btn_usuarios"];
    
    // AGREGAR
    if($btn == "Agregar"){
        $id_usuario = $_POST["txtid_usuario"];
        $nombre = $_POST["txtnombre"];
        $correo = $_POST["txtcorreo"];
        $contrasena = $_POST["txtcontrasena"];
        $rol = $_POST["txtrol"];
        
        $sql = "INSERT INTO usuario (id_usuario,nombre, correo, contrasena, rol, fecha_registro) 
                VALUES ('$id_usuario','$nombre','$correo','$contrasena','$rol', NOW())";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Usuario agregado correctamente');</script>";
            // Limpiar campos
            $id_usuario = $nombre = $correo = $contrasena = $rol = $fecha_registro = $txtbus = "";
        } else {
            echo "<script>alert('Error al agregar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // MODIFICAR
    if($btn == "Modificar" ){
        $id_usuario = $_POST["txtid_usuario"];
        $nombre = $_POST["txtnombre"];
        $correo = $_POST["txtcorreo"];
        $contrasena = $_POST["txtcontrasena"];
        $rol = $_POST["txtrol"];
        
        $sql = "UPDATE usuario SET 
                nombre='$nombre',
                correo='$correo',
                contrasena='$contrasena',
                rol='$rol'
                WHERE id_usuario='$id_usuario'";
        
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Usuario modificado correctamente');</script>";
        } else {
            echo "<script>alert('Error al modificar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // ELIMINAR
    if($btn == "Eliminar" && !empty($_POST["txtid_usuario"])){
        $id_usuario = $_POST["txtid_usuario"];
        
        $sql = "DELETE FROM usuario WHERE id_usuario='$id_usuario'";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Usuario eliminado correctamente');</script>";
            // Limpiar campos
            $id_usuario = $nombre = $correo = $contrasena = $rol = $fecha_registro = $txtbus = "";
        } else {
            echo "<script>alert('Error al eliminar: " . mysqli_error($cn) . "');</script>";
        }
    }
}
?>

<h1 class="mb-4 text-primary">Gestión de Usuarios</h1>

<div class="card p-4 shadow-sm mb-4 bg-light">
    <h3 class="card-title text-center text-dark">Formulario de Gestión de Usuarios</h3>
    
    <!-- FORMULARIO SEPARADO PARA BÚSQUEDA -->
    <form method="POST" class="mb-4 border-bottom pb-3">
        <div class="row">
            <div class="col-md-8">
                <input type="text" class="form-control" name="txtbus" placeholder="ID del usuario a buscar" 
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
                <label class="form-label">ID Usuario</label>
                <input type="text" class="form-control" name="txtid_usuario" value="<?php echo htmlspecialchars($id_usuario); ?>" >
            </div>
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="txtnombre" value="<?php echo htmlspecialchars($nombre); ?>" >
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Correo</label>
                <input type="email" class="form-control" name="txtcorreo" value="<?php echo htmlspecialchars($correo); ?>" >
            </div>
            <div class="col-md-6">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="txtcontrasena" value="<?php echo htmlspecialchars($contrasena); ?>" >
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label">Rol</label>
                <select class="form-select" name="txtrol" required>
                    <option value="admin" <?php echo $rol == 'admin' ? 'selected' : ''; ?>>Administrador (Admin)</option>
                    <option value="docente" <?php echo $rol == 'docente' ? 'selected' : ''; ?>>Docente</option>
                    <option value="estudiante" <?php echo $rol == 'estudiante' ? 'selected' : ''; ?>>Estudiante</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Fecha Registro</label>
                <input type="date" class="form-control" name="fecha_registro" 
                       value="<?php echo htmlspecialchars($fecha_registro); ?>">
            </div>
        </div>

         <!-- Botones de acción -->
        <div class="text-center">
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_usuarios" value="Agregar">
                <i class="bi bi-plus-lg"></i> Agregar
            </button>
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_usuarios" value="Mostrar">
                <i class="bi bi-eye"></i> Mostrar
            </button>
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_usuarios" value="Modificar">
                <i class="bi bi-pencil"></i> Modificar
            </button>
            <button type="submit" class="btn btn-outline-primary" name="btn_usuarios" value="Eliminar" 
                    onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </div>

<!-- SECCIÓN PARA MOSTRAR LOS USUARIOS -->
<div class="data-container mt-4">
    <?php
    if(isset($_POST["btn_usuarios"]) && $_POST["btn_usuarios"] == "Mostrar"){
        $sql="SELECT * FROM usuario";
        $cs=mysqli_query($cn,$sql);
        if($cs && mysqli_num_rows($cs) > 0) {
           echo "<div class='contenedor-tabla'>";
            echo "<h3 class='titulo-tabla-terminos mb-4 text-primary'>Lista de Usuarios</h3>";
            echo "<div class='table-responsive-container'>";
            echo "<table class='table table-hover mb-0'>";
            echo "<thead>
                    <tr>
                        <th width='80'>ID</th>
                        <th width='150'>Nombre</th>
                        <th width='200'>Correo</th>
                        <th width='120'>Rol</th>
                        <th width='120'>Fecha Registro</th>
                    </tr>
                </thead>";
            echo "<tbody>";
            while($resul=mysqli_fetch_array($cs)){
                $id_usuario = $resul[0];
                $nombre = $resul[1];
                $correo = $resul[2];
                $rol = $resul[4];
                $fecha_registro = date('d/m/Y', strtotime($resul[5]));
                
                // Determinar clase del badge según el rol
                $badge_class = '';
                if($rol == 'admin') $badge_class = 'status-active';
                elseif($rol == 'docente') $badge_class = 'status-pending';
                elseif($rol == 'estudiante') $badge_class = 'badge-estado-rechazado';
                
                echo "<tr>
                <td data-label='ID'><strong>$id_usuario</strong></td>
                <td data-label='Nombre'><strong>$nombre</strong></td>
                <td data-label='Correo'>$correo</td>
                <td data-label='Rol'><span class='status-badge $badge_class'>" . ucfirst($rol) . "</span></td>
                <td data-label='Fecha Registro'><small>$fecha_registro</small></td>
            </tr>";
            }

            echo "</tbody>";
            echo "</table>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class='alert alert-info text-center'>No hay usuarios registrados</div>";
        }
    }
    ?>
</div>