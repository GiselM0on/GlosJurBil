<?php
// secciones/gestion_traducciones.php

//conexion a la db
include ("conexion.php");

// Variables para los campos
$id = "";
$palabra = "";
$pronunciacion = "";
$definicion = "";
$id_termino = "";
$id_idioma = "";
$txtbus = "";

// Procesar BÚSQUEDA (formulario separado)
if(isset($_POST["btn_buscar"]) && $_POST["btn_buscar"] == "Buscar"){
    if(isset($_POST["txtbus"]) && !empty($_POST["txtbus"])){
        $txtbus = $_POST["txtbus"];
        
        $sql = "SELECT * FROM traduccion WHERE id='$txtbus'";
        $cs = mysqli_query($cn, $sql);
        if($cs && mysqli_num_rows($cs) > 0) {
            $resul = mysqli_fetch_array($cs);
            $id = $resul[0];
            $palabra = $resul[1];
            $pronunciacion = $resul[2];
            $definicion = $resul[3];
            $id_termino = $resul[4];
            $id_idioma = $resul[5];
            echo "<script>alert('Traducción encontrada');</script>";
        } else {
            echo "<script>alert('Traducción no encontrada');</script>";
            // Limpiar campos si no se encuentra
            $id = $palabra = $pronunciacion = $definicion = $id_termino = $id_idioma = "";
        }
    } else {
        echo "<script>alert('Ingrese un ID para buscar');</script>";
    }
}

// Procesar formulario PRINCIPAL (Agregar, Modificar, Eliminar)
if(isset($_POST["btn_traducciones"])){
    $btn = $_POST["btn_traducciones"];
    
    // AGREGAR
    if($btn == "Agregar"){
        $palabra = $_POST["txtpalabra"];
        $pronunciacion = $_POST["txtpronunciacion"];
        $definicion = $_POST["txtdefinicion"];
        $id_termino = $_POST["txtid_termino"];
        $id_idioma = $_POST["txtid_idioma"];
        
        $sql = "INSERT INTO traduccion (palabra, pronunciacion, definicion, id_Termino, id_Idioma) 
                VALUES ('$palabra','$pronunciacion','$definicion','$id_termino','$id_idioma')";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Traducción agregada correctamente');</script>";
            // Limpiar campos
            $id = $palabra = $pronunciacion = $definicion = $id_termino = $id_idioma = $txtbus = "";
        } else {
            echo "<script>alert('Error al agregar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // MODIFICAR
    if($btn == "Modificar" && !empty($_POST["txtid"])){
        $id = $_POST["txtid"];
        $palabra = $_POST["txtpalabra"];
        $pronunciacion = $_POST["txtpronunciacion"];
        $definicion = $_POST["txtdefinicion"];
        $id_termino = $_POST["txtid_termino"];
        $id_idioma = $_POST["txtid_idioma"];
        
        $sql = "UPDATE traduccion SET 
                palabra='$palabra',
                pronunciacion='$pronunciacion',
                definicion='$definicion',
                id_Termino='$id_termino',
                id_Idioma='$id_idioma'
                WHERE id='$id'";
        
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Traducción modificada correctamente');</script>";
        } else {
            echo "<script>alert('Error al modificar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // ELIMINAR
    if($btn == "Eliminar" && !empty($_POST["txtid"])){
        $id = $_POST["txtid"];
        
        $sql = "DELETE FROM traduccion WHERE id='$id'";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Traducción eliminada correctamente');</script>";
            // Limpiar campos
            $id = $palabra = $pronunciacion = $definicion = $id_termino = $id_idioma = $txtbus = "";
        } else {
            echo "<script>alert('Error al eliminar: " . mysqli_error($cn) . "');</script>";
        }
    }
}
?>

<h1 class="mb-4 text-primary">Gestión de Traducciones</h1>

<div class="card p-4 shadow-sm mb-4 bg-light">
    <h3 class="card-title text-center text-dark">Formulario de Gestión de Traducciones</h3>
    
    <!-- FORMULARIO SEPARADO PARA BÚSQUEDA -->
    <form method="POST" class="mb-4 border-bottom pb-3">
        <div class="row">
            <div class="col-md-8">
                <input type="text" class="form-control" name="txtbus" placeholder="ID de la traducción a buscar" 
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
                <label class="form-label">ID Traducción</label>
                <input type="text" class="form-control" name="txtid" value="<?php echo htmlspecialchars($id); ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Palabra</label>
                <input type="text" class="form-control" name="txtpalabra" value="<?php echo htmlspecialchars($palabra); ?>" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Pronunciación</label>
                <input type="text" class="form-control" name="txtpronunciacion" value="<?php echo htmlspecialchars($pronunciacion); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">ID Término</label>
                <input type="text" class="form-control" name="txtid_termino" value="<?php echo htmlspecialchars($id_termino); ?>" required>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label">Definición</label>
                <textarea class="form-control" name="txtdefinicion" rows="3" required><?php echo htmlspecialchars($definicion); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">ID Idioma</label>
                <select class="form-select" name="txtid_idioma" required>
                    <option value="1" <?php echo $id_idioma == '1' ? 'selected' : ''; ?>>Español</option>
                    <option value="2" <?php echo $id_idioma == '2' ? 'selected' : ''; ?>>Inglés</option>
                </select>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="text-center">
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_traducciones" value="Agregar">
                <i class="bi bi-plus-lg"></i> Agregar
            </button>
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_traducciones" value="Modificar">
                <i class="bi bi-pencil"></i> Modificar
            </button>
            <button type="submit" class="btn btn-outline-primary" name="btn_traducciones" value="Eliminar" 
                    onclick="return confirm('¿Estás seguro de eliminar esta traducción?')">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </div>
    </form>
</div>

<!-- Mostrar lista de traducciones -->
<?php
$query_traduccion = "SELECT tr.*, t.nombreTer as termino, i.nombre_idioma as idioma 
                      FROM traduccion tr 
                      LEFT JOIN termino t ON tr.id_Termino = t.id 
                      LEFT JOIN idioma i ON tr.id_Idioma = i.id 
                      ORDER BY tr.id DESC";
$result_traduccion = mysqli_query($cn, $query_traduccion);
?>

<h3 class="mb-3 text-primary">Traducciones Registradas</h3>
<div class="table-responsive">
    <table class="table table-striped align-middle mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Palabra</th>
                <th>Pronunciación</th>
                <th>Término</th>
                <th>Idioma</th>
                <th>Definición</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_traduccion && mysqli_num_rows($result_traduccion) > 0): ?>
                <?php while ($traduccion = mysqli_fetch_assoc($result_traduccion)): ?>
                    <tr>
                        <td><?php echo $traduccion['id']; ?></td>
                        <td><?php echo htmlspecialchars($traduccion['palabra']); ?></td>
                        <td><?php echo htmlspecialchars($traduccion['pronunciacion']); ?></td>
                        <td><?php echo htmlspecialchars($traduccion['termino']); ?></td>
                        <td><?php echo htmlspecialchars($traduccion['idioma']); ?></td>
                        <td><?php echo htmlspecialchars(substr($traduccion['definicion'], 0, 50)) . '...'; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No hay traducciones registradas</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>