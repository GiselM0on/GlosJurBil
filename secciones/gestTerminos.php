<?php
// secciones/gestion_terminos.php

// Conexión a la base de datos
include("conexion.php");

// Variables para los campos
$id_termino = "";
$palabra = "";
$pronunciacion = "";
$definicion = "";
$ejemplo_aplicativo = "";
$referencia_bibliogr = "";
$estado = "";
$fecha_creacion = "";
$fecha_modificacion = "";
$id_usuario = "";
$txtbus = "";

// Procesar todas las acciones con un solo botón
if(isset($_POST["btn1"])){
    $btn = $_POST["btn1"];
    
    // BÚSQUEDA
    if($btn == "Buscar" && isset($_POST["txtbus"])){
        $txtbus = $_POST["txtbus"];
        
        $sql = "SELECT * FROM termino WHERE id_termino='$txtbus'"; 
        $cs = mysqli_query($cn, $sql);
        if($cs && mysqli_num_rows($cs) > 0) {
            while($resul = mysqli_fetch_array($cs)){
                $id_termino = $resul[0];
                $palabra = $resul[1];
                $pronunciacion = $resul[2];
                $definicion = $resul[3];
                $ejemplo_aplicativo = $resul[4];
                $referencia_bibliogr = $resul[5];
                $estado = $resul[6];
                $fecha_creacion = $resul[7];
                $fecha_modificacion = $resul[8];
                $id_usuario = $resul[9];
            }
        } else {
            echo "<script>alert('No se encontró ningún término con ese ID');</script>";
        }
    }
    
    // AGREGAR
    if($btn == "Agregar"){
        $id_termino = $_POST["txtid_termino"];
        $palabra = $_POST["txtpalabra"];
        $pronunciacion = $_POST["txtpronunciacion"];
        $definicion = $_POST["txtdefinicion"];
        $ejemplo_aplicativo = $_POST["txtejemplo"];
        $referencia_bibliogr = $_POST["txtreferencia"];
        $estado = $_POST["txtestado"];
        $fecha_creacion = $_POST["txtfecha_creacion"];
        $fecha_modificacion = $_POST["txtfecha_modificacion"];
        $id_usuario = $_POST["txtid_usuario"];
        
        $sql = "INSERT INTO termino (id_termino, palabra, pronunciacion, definicion, ejemplo_aplicativo, referencia_bibliogr, estado, fecha_creacion, fecha_modificacion, id_Usuario) 
                VALUES ('$id_termino','$palabra','$pronunciacion','$definicion','$ejemplo_aplicativo','$referencia_bibliogr','$estado', NOW(), NOW(), '$id_usuario')";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Término agregado correctamente');</script>";
            // Limpiar campos
            $id_termino = $palabra = $pronunciacion = $definicion = $ejemplo_aplicativo = $referencia_bibliogr = $estado = $fecha_creacion = $fecha_modificacion = $id_usuario = $txtbus = "";
        } else {
            echo "<script>alert('Error al agregar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // MODIFICAR
    if($btn == "Modificar"){
        $id_termino = $_POST["txtid_termino"];
        $palabra = $_POST["txtpalabra"];
        $pronunciacion = $_POST["txtpronunciacion"];
        $definicion = $_POST["txtdefinicion"];
        $ejemplo_aplicativo = $_POST["txtejemplo"];
        $referencia_bibliogr = $_POST["txtreferencia"];
        $estado = $_POST["txtestado"];
        $fecha_modificacion = $_POST["txtfecha_modificacion"];
        $id_usuario = $_POST["txtid_usuario"];
        
        $sql = "UPDATE termino SET 
                palabra='$palabra',
                pronunciacion='$pronunciacion',
                definicion='$definicion',
                ejemplo_aplicativo='$ejemplo_aplicativo',
                referencia_bibliogr='$referencia_bibliogr',
                estado='$estado',
                fecha_modificacion=NOW(),
                id_Usuario='$id_usuario' 
                WHERE id_termino='$id_termino'";
        
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Término modificado correctamente');</script>";
        } else {
            echo "<script>alert('Error al modificar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // ELIMINAR
    if($btn == "Eliminar"){
        $id_termino = $_POST["txtid_termino"];
        
        $sql = "DELETE FROM termino WHERE id_termino='$id_termino'";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Término eliminado correctamente');</script>";
            // Limpiar campos después de eliminar
            $id_termino = $palabra = $pronunciacion = $definicion = $ejemplo_aplicativo = $referencia_bibliogr = $estado = $fecha_creacion = $fecha_modificacion = $id_usuario = $txtbus = "";
        } else {
            echo "<script>alert('Error al eliminar: " . mysqli_error($cn) . "');</script>";
        }
    }
}
?>

<div class="container-fluid">
    <h1 class="mb-4 text-primary">Gestión de Términos</h1>

    <div class="card p-4 shadow-sm mb-4 bg-light">
        <h3 class="card-title text-center text-dark">Formulario de Gestión de Términos</h3>
        
        <div class="card-body p-4">
            <!-- FORMULARIO SEPARADO PARA BÚSQUEDA -->
            <div class="search-section mb-4 p-3 bg-light rounded">
                <form method="POST" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="txtbus" placeholder="ID del término a buscar" 
                               value="<?php echo htmlspecialchars($txtbus); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-primary w-90" name="btn1" value="Buscar">
                            <i class="bi bi-search me-2"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>

            <!-- FORMULARIO PRINCIPAL -->
            <form method="POST">
                <div class="row g-3">
                    <!-- Primera fila -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">ID Término</label>
                        <input type="text" class="form-control form-control-sm" name="txtid_termino" 
                               value="<?php echo htmlspecialchars($id_termino); ?>" style="font-size: 0.875rem;">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Palabra</label>
                        <input type="text" class="form-control form-control-sm" name="txtpalabra" 
                               value="<?php echo htmlspecialchars($palabra); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Pronunciación</label>
                        <input type="text" class="form-control form-control-sm" name="txtpronunciacion" 
                               value="<?php echo htmlspecialchars($pronunciacion); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Estado</label>
                        <select class="form-select form-select-sm" name="txtestado">
                            <option value="">Seleccionar</option>
                            <option value="pendiente" <?php echo $estado == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="aprobado" <?php echo $estado == 'aprobado' ? 'selected' : ''; ?>>Aprobado</option>
                            <option value="rechazado" <?php echo $estado == 'rechazado' ? 'selected' : ''; ?>>Rechazado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label ">Fecha Creación</label>
                        <input type="date" class="form-control name="txtfecha_creacion" 
                               value="<?php echo htmlspecialchars($fecha_creacion); ?>" >
                    </div>
                    <div class="col-md-2">
                        <label class="form-label ">Fecha Modificación</label>
                        <input type="date" class="form-control" name="txtfecha_modificacion" 
                               value="<?php echo htmlspecialchars($fecha_modificacion); ?>" >
                    </div>

                    <!-- Segunda fila -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Definición</label>
                        <textarea class="form-control" name="txtdefinicion" rows="3" placeholder="Ingrese la definición del término"><?php echo htmlspecialchars($definicion); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ejemplo Aplicativo</label>
                        <textarea class="form-control" name="txtejemplo" rows="3" placeholder="Ingrese un ejemplo de uso"><?php echo htmlspecialchars($ejemplo_aplicativo); ?></textarea>
                    </div>

                    <!-- Tercera fila -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Referencia Bibliográfica</label>
                        <input type="text" class="form-control" name="txtreferencia" 
                               value="<?php echo htmlspecialchars($referencia_bibliogr); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ID Usuario</label>
                        <input type="number" class="form-control form-control-sm" name="txtid_usuario" 
                               value="<?php echo htmlspecialchars($id_usuario); ?>">
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="text-center mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-outline-primary me-2" name="btn1" value="Agregar">
                        <i class="bi bi-plus-lg me-2"></i> Agregar
                    </button>
                    <button type="submit" class="btn btn-outline-primary me-2" name="btn1" value="Mostrar">
                        <i class="bi bi-eye me-2"></i> Mostrar
                    </button>
                    <button type="submit" class="btn btn-outline-primary me-2" name="btn1" value="Modificar"
                     onclick="return confirm('¿Estás seguro de modificar este término?')">
                        <i class="bi bi-pencil me-2"></i> Modificar
                    </button>
                    <button type="submit" class="btn btn-outline-primary me-2" name="btn1" value="Eliminar" 
                            onclick="return confirm('¿Estás seguro de eliminar este término?')">
                        <i class="bi bi-trash me-2"></i> Eliminar
                    </button>
                    
                </div>
            </form>
        </div>
    </div>

   <!-- SECCIÓN PARA MOSTRAR LOS TÉRMINOS CON DISEÑO MEJORADO -->
<div class="data-container mt-4">
    <?php
    if(isset($_POST["btn1"])){
        $btn=$_POST["btn1"];
    
        if($btn=="Mostrar"){
            $sql="SELECT * FROM termino";
            $cs=mysqli_query($cn,$sql);
            if($cs && mysqli_num_rows($cs) > 0) {
               echo "<div class='contenedor-tabla'>";
                echo "<h3 class='titulo-tabla-terminos mb-4 text-primary'>Lista de Términos</h3>";
                echo "<div class='table-responsive-container'>";
                echo "<table class='table table-hover mb-0'>";
                echo "<thead>
                        <tr>
                            <th width='80'>ID</th>
                            <th width='120'>Palabra</th>
                            <th width='120'>Pronunciación</th>
                            <th width='200'>Definición</th>
                            <th width='200'>Ejemplo</th>
                            <th width='150'>Referencia</th>
                            <th width='100'>Estado</th>
                            <th width='100'>Fecha Creación</th>
                            <th width='100'>Fecha Modificación</th>
                            <th width='80'>Usuario</th>
                        </tr>
                    </thead>";
                echo "<tbody>";
                while($resul=mysqli_fetch_array($cs)){
                    $id_termino = $resul[0];
                    $palabra = $resul[1];
                    $pronunciacion = $resul[2];
                    $definicion = substr($resul[3], 0, 100) . (strlen($resul[3]) > 100 ? '...' : '');
                    $ejemplo_aplicativo = substr($resul[4], 0, 100) . (strlen($resul[4]) > 100 ? '...' : '');
                    $referencia_bibliogr = $resul[5];
                    $estado = $resul[6];
                    $fecha_creacion = date('d/m/Y', strtotime($resul[7]));
                    $fecha_modificacion = date('d/m/Y', strtotime($resul[8]));
                    $id_usuario = $resul[9];
                    
                    // Determinar clase del badge según el estado
                    $badge_class = '';
                    if($estado == 'aprobado') $badge_class = 'status-active';
                    elseif($estado == 'pendiente') $badge_class = 'status-pending';
                    elseif($estado == 'rechazado') $badge_class = 'badge-estado-rechazado';
                    
                    echo "<tr>
                    <td data-label='ID'><strong>$id_termino</strong></td>
                    <td data-label='Palabra'><strong>$palabra</strong></td>
                    <td data-label='Pronunciación'><em>$pronunciacion</em></td>
                    <td data-label='Definición'><div class='texto-limitado'>$definicion</div></td>
                    <td data-label='Ejemplo'><div class='texto-limitado'>$ejemplo_aplicativo</div></td>
                    <td data-label='Referencia'><small>$referencia_bibliogr</small></td>
                    <td data-label='Estado'><span class='status-badge $badge_class'>" . ucfirst($estado) . "</span></td>
                    <td data-label='Fecha Creación'><small>$fecha_creacion</small></td>
                    <td data-label='Fecha Modificación'><small>$fecha_modificacion</small></td>
                    <td data-label='Usuario'>$id_usuario</td>
                </tr>";
                }

                echo "</tbody>";
                echo "</table>";
                echo "</div>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-info text-center'>No hay términos registrados</div>";
            }
        }
    }
    ?>
</div>