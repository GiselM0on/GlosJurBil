<?php
//session_start();



// Conexión a la base de datos
include("conexion.php");

// Configurar charset para la conexión
if (isset($cn) && is_object($cn)) {
    mysqli_set_charset($cn, "utf8mb4");
}

// Función para sanitizar inputs
function sanitize($input, $connection) {
    if (is_string($input)) {
        return trim($connection->real_escape_string($input));
    }
    return $input;
}

// Variables
$id_termino = $palabra = $pronunciacion = $definicion = $ejemplo_aplicativo = $referencia_bibliogr = "";
$estado = $fecha_creacion = $fecha_modificacion = $id_usuario = $txtbus = "";

if(isset($_POST["btn1"])){
    $btn = $_POST["btn1"];
    
    // BÚSQUEDA
    if($btn == "Buscar" && isset($_POST["txtbus"])){
        $txtbus = sanitize($_POST["txtbus"], $cn);
        
        $sql = "SELECT * FROM termino WHERE id_termino='$txtbus'"; 
        $cs = mysqli_query($cn, $sql);
        if($cs && mysqli_num_rows($cs) > 0) {
            while($resul = mysqli_fetch_array($cs)){
                list($id_termino, $palabra, $pronunciacion, $definicion, $ejemplo_aplicativo, 
                     $referencia_bibliogr, $estado, $fecha_creacion, $fecha_modificacion, $id_usuario) = $resul;
            }
        } else {
            echo "<script>alert('No se encontró ningún término con ese ID');</script>";
        }
    }
    
    // AGREGAR
    if($btn == "Agregar"){
        // Sanitizar todos los inputs
        $id_termino = sanitize($_POST["txtid_termino"], $cn);
        $palabra = sanitize($_POST["txtpalabra"], $cn);
        $pronunciacion = sanitize($_POST["txtpronunciacion"], $cn);
        $definicion = sanitize($_POST["txtdefinicion"], $cn);
        $ejemplo_aplicativo = sanitize($_POST["txtejemplo"], $cn);
        $referencia_bibliogr = sanitize($_POST["txtreferencia"], $cn);
        $estado = sanitize($_POST["txtestado"], $cn);
        $id_usuario = sanitize($_POST["txtid_usuario"], $cn);
        
        // Validación
        if (empty($palabra) || empty($definicion)) {
            echo "<script>alert('Palabra y definición son obligatorios');</script>";
        } else {
            $sql = "INSERT INTO termino (id_termino, palabra, pronunciacion, definicion, ejemplo_aplicativo, 
                    referencia_bibliogr, estado, fecha_creacion, fecha_modificacion, id_Usuario) 
                    VALUES ('$id_termino','$palabra','$pronunciacion','$definicion','$ejemplo_aplicativo',
                    '$referencia_bibliogr','$estado', NOW(), NOW(), '$id_usuario')";
            
            $cs = mysqli_query($cn, $sql);
            if($cs) {
                echo "<script>alert('Término agregado correctamente');</script>";
                // Limpiar campos
                $id_termino = $palabra = $pronunciacion = $definicion = $ejemplo_aplicativo = 
                $referencia_bibliogr = $estado = $fecha_creacion = $fecha_modificacion = $id_usuario = $txtbus = "";
            } else {
                echo "<script>alert('Error al agregar: " . addslashes(mysqli_error($cn)) . "');</script>";
            }
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
                               value="<?php echo htmlspecialchars($txtbus, ENT_QUOTES, 'UTF-8'); ?>">
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
                               value="<?php echo htmlspecialchars($id_termino, ENT_QUOTES, 'UTF-8'); ?>" readonly 
                       style="background-color: #e9ecef; cursor: not-allowed;"
                               >
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Palabra</label>
                        <input type="text" class="form-control form-control-sm" name="txtpalabra" 
                               value="<?php echo htmlspecialchars($palabra, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Pronunciación</label>
                        <input type="text" class="form-control form-control-sm" name="txtpronunciacion" 
                               value="<?php echo htmlspecialchars($pronunciacion, ENT_QUOTES, 'UTF-8'); ?>">
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
                        <label class="form-label">Fecha Creación</label>
                        <input type="date" class="form-control" name="txtfecha_creacion" 
                               value="<?php echo htmlspecialchars($fecha_creacion, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha Modificación</label>
                        <input type="date" class="form-control" name="txtfecha_modificacion" 
                               value="<?php echo htmlspecialchars($fecha_modificacion, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <!-- Segunda fila -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Definición</label>
                        <textarea class="form-control" name="txtdefinicion" rows="3" placeholder="Ingrese la definición del término"><?php echo htmlspecialchars($definicion, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ejemplo Aplicativo</label>
                        <textarea class="form-control" name="txtejemplo" rows="3" placeholder="Ingrese un ejemplo de uso"><?php echo htmlspecialchars($ejemplo_aplicativo, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <!-- Tercera fila -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Referencia Bibliográfica</label>
                        <input type="text" class="form-control" name="txtreferencia" 
                               value="<?php echo htmlspecialchars($referencia_bibliogr, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ID Usuario</label>
                        <input type="number" class="form-control form-control-sm" name="txtid_usuario" 
                               value="<?php echo htmlspecialchars($id_usuario, ENT_QUOTES, 'UTF-8'); ?>">
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
                    <td data-label='ID'><strong>" . htmlspecialchars($id_termino, ENT_QUOTES, 'UTF-8') . "</strong></td>
                    <td data-label='Palabra'><strong>" . htmlspecialchars($palabra, ENT_QUOTES, 'UTF-8') . "</strong></td>
                    <td data-label='Pronunciación'><em>" . htmlspecialchars($pronunciacion, ENT_QUOTES, 'UTF-8') . "</em></td>
                    <td data-label='Definición'><div class='texto-limitado'>" . htmlspecialchars($definicion, ENT_QUOTES, 'UTF-8') . "</div></td>
                    <td data-label='Ejemplo'><div class='texto-limitado'>" . htmlspecialchars($ejemplo_aplicativo, ENT_QUOTES, 'UTF-8') . "</div></td>
                    <td data-label='Referencia'><small>" . htmlspecialchars($referencia_bibliogr, ENT_QUOTES, 'UTF-8') . "</small></td>
                    <td data-label='Estado'><span class='status-badge $badge_class'>" . ucfirst(htmlspecialchars($estado, ENT_QUOTES, 'UTF-8')) . "</span></td>
                    <td data-label='Fecha Creación'><small>" . htmlspecialchars($fecha_creacion, ENT_QUOTES, 'UTF-8') . "</small></td>
                    <td data-label='Fecha Modificación'><small>" . htmlspecialchars($fecha_modificacion, ENT_QUOTES, 'UTF-8') . "</small></td>
                    <td data-label='Usuario'>" . htmlspecialchars($id_usuario, ENT_QUOTES, 'UTF-8') . "</td>
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