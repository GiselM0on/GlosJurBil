<?php
// secciones/gestion_terminos.php

//conexion del db
include ("./conexion.php");

// Variables para los campos
$id_termino = "";
$ejemplo_aplicativo = "";
$referencia_bibliogr = "";
$estado = "";
$fecha_creacion = "";
$fecha_modificacion = "";
$id_usuario = "";
$txtbus = "";

// Procesar BÚSQUEDA (formulario separado)
if(isset($_POST["btn_buscar"]) && $_POST["btn_buscar"] == "Buscar"){
    if(isset($_POST["txtbus"]) && !empty($_POST["txtbus"])){
        $txtbus = $_POST["txtbus"];
    
        $sql = "SELECT * FROM termino WHERE id_termino='$txtbus'"; // CORREGIDO: usar 'id' en lugar de 'id_Termino'
        $cs = mysqli_query($cn, $sql);
        if($cs && mysqli_num_rows($cs) > 0) {
            $resul = mysqli_fetch_array($cs);
            $id_termino = $resul[0];
            $ejemplo_aplicativo = $resul[1];
            $referencia_bibliogr = $resul[2];
            $estado = $resul[3];
            $fecha_creacion = $resul[4];
            $fecha_modificacion = $resul[5];
            $id_usuario = $resul[6];
            echo "<script>alert('Término encontrado');</script>"; // CORREGIDO: mensaje
        } else {
            echo "<script>alert('Término no encontrado');</script>"; // CORREGIDO: mensaje
            // Limpiar campos si no se encuentra
            $id_termino = $ejemplo_aplicativo = $referencia_bibliogr = $estado = $fecha_creacion = $fecha_modificacion = $id_usuario = "";
        }
    } else {
        echo "<script>alert('Ingrese un ID para buscar');</script>";
    }
}

// Procesar formulario PRINCIPAL (Agregar, Modificar, Eliminar)
if(isset($_POST["btn_termino"])){ // CORREGIDO: nombre del botón
    $btn = $_POST["btn_termino"]; // CORREGIDO: nombre del botón
    
    // AGREGAR
    if($btn == "Agregar"){
        // CORREGIDO: usar los nombres correctos de los campos del formulario
        $ejemplo_aplicativo = $_POST["txtejemplo"];
        $referencia_bibliogr = $_POST["txtreferencia"];
        $estado = $_POST["txtestado"];
        $fecha_creacion = $POST["txtfecha_creacion"];
        $fecha_modificacion = $POST["txtfecha_modificacion"];
        $id_usuario = $_POST["txtid_usuario"];
        
        $sql = "INSERT INTO termino (ejemplo_aplicativo, referencia_bibliogr, estado, fecha_creacion, fecha_modificacion, id_Usuario) 
                VALUES ('$ejemplo_aplicativo','$referencia_bibliogr','$estado', NOW(), NOW(), '$id_usuario')";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Término agregado correctamente');</script>";
            // Limpiar campos
            $id_termino = $ejemplo_aplicativo = $referencia_bibliogr = $estado = $fecha_creacion = $fecha_modificacion = $id_usuario = $txtbus = "";
        } else {
            echo "<script>alert('Error al agregar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // MODIFICAR
    if($btn == "Modificar" && !empty($_POST["txtid_termino"])){ // CORREGIDO: nombre del campo
        $id_termino = $_POST["txtid"]; // CORREGIDO: nombre del campo
        $ejemplo_aplicativo = $_POST["txtejemplo"];
        $referencia_bibliogr = $_POST["txtreferencia"];
        $estado = $_POST["txtestado"];
        $id_usuario = $_POST["txtid_usuario"];
        
        $sql = "UPDATE termino SET
                id_termino = '$id_termino',
                ejemplo_aplicativo='$ejemplo_aplicativo',
                referencia_bibliogr='$referencia_bibliogr',
                estado='$estado',
                fecha_modificacion=NOW(),
                id_Usuario='$id_usuario'
                WHERE id='$id_termino'"; // CORREGIDO: usar 'id' en lugar de 'id_termino'
        
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Término modificado correctamente');</script>";
        } else {
            echo "<script>alert('Error al modificar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // ELIMINAR
    if($btn == "Eliminar" && !empty($_POST["txtid_termino"])){ // CORREGIDO: nombre del campo
        $id_termino = $_POST["txtid"]; // CORREGIDO: nombre del campo
        
        $sql = "DELETE FROM termino WHERE id='$id_termino'"; // CORREGIDO: usar 'id' en lugar de 'id_termino'
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Término eliminado correctamente');</script>";
            // Limpiar campos
            $id_termino = $ejemplo_aplicativo = $referencia_bibliogr = $estado = $fecha_creacion = $fecha_modificacion = $id_usuario = $txtbus = "";
        } else {
            echo "<script>alert('Error al eliminar: " . mysqli_error($cn) . "');</script>";
        }
    }
}
?>

<h1 class="mb-4 text-primary">Gestión de Términos</h1>

<div class="card p-4 shadow-sm mb-4 bg-light">
    <h3 class="card-title text-center text-dark">Formulario de Gestión de Términos</h3>
    
    <!-- FORMULARIO SEPARADO PARA BÚSQUEDA -->
    <form method="POST" class="mb-4 border-bottom pb-3">
        <div class="row">
            <div class="col-md-8">
                <input type="text" class="form-control" name="txtbus" placeholder="ID del término a buscar" 
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
                <label class="form-label">ID Término</label>
                <input type="text" class="form-control" name="txtid_termino" value="<?php echo htmlspecialchars($id_termino); ?>" >
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Ejemplo Aplicativo</label>
                <textarea class="form-control" name="txtejemplo" rows="3" required><?php echo htmlspecialchars($ejemplo_aplicativo); ?></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Referencia Bibliográfica</label>
                <input type="text" class="form-control" name="txtreferencia" value="<?php echo htmlspecialchars($referencia_bibliogr); ?>" >
            </div>
            <div class="col-md-6">
                <label class="form-label">Estado</label>
                <select class="form-select" name="txtestado" required>
                    <option value="pendiente" <?php echo $estado == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="aprobado" <?php echo $estado == 'aprobado' ? 'selected' : ''; ?>>Aprobado</option>
                    <option value="rechazado" <?php echo $estado == 'rechazado' ? 'selected' : ''; ?>>Rechazado</option>
                </select>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label">ID Usuario</label>
                <input type="number" class="form-control" name="txtid_usuario" value="<?php echo htmlspecialchars($id_usuario); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Fecha Creación</label>
                <input type="date" class="form-control" name="fecha_creacion" 
                       value="<?php echo htmlspecialchars($fecha_creacion); ?>">
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="text-center">
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_termino" value="Agregar">
                <i class="bi bi-plus-lg"></i> Agregar
            </button>
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_termino" value="Modificar">
                <i class="bi bi-pencil"></i> Modificar
            </button>
            <button type="submit" class="btn btn-outline-primary" name="btn_termino" value="Eliminar" 
                    onclick="return confirm('¿Estás seguro de eliminar este término?')">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </div>
    </form>
</div>

<!-- Mostrar lista de términos -->
<?php
$query_terminos = "SELECT t.*, u.nombre as usuario_nombre FROM termino t 
                   LEFT JOIN usuario u ON t.id_Usuario = u.id 
                   ORDER BY t.id DESC";
$result_terminos = mysqli_query($cn, $query_terminos);
?>

