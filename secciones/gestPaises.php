<?php
// secciones/gestion_paises.php

//conexion del db
include ("conexion.php");

// Variables para los campos
$id_pais = "";
$nombre_pas = "";
$txtbus = "";

// Procesar BÚSQUEDA (formulario separado)
if(isset($_POST["btn_buscar"]) && $_POST["btn_buscar"] == "Buscar"){
    if(isset($_POST["txtbus"]) && !empty($_POST["txtbus"])){
        $txtbus = $_POST["txtbus"];
        
        $sql = "SELECT * FROM pais WHERE id_pais='$txtbus'";
        $cs = mysqli_query($cn, $sql);
        if($cs && mysqli_num_rows($cs) > 0) {
            $resul = mysqli_fetch_array($cs);
            $id_pais = $resul[0];
            $nombre_pas = $resul[1];
            echo "<script>alert('País encontrado');</script>";
        } else {
            echo "<script>alert('País no encontrado');</script>";
            // Limpiar campos si no se encuentra
            $id_pais = $nombre_pas = "";
        }
    } else {
        echo "<script>alert('Ingrese un ID para buscar');</script>";
    }
}

// Procesar formulario PRINCIPAL (Agregar, Modificar, Eliminar)
if(isset($_POST["btn_paises"])){
    $btn = $_POST["btn_paises"];
    
    // AGREGAR
    if($btn == "Agregar"){
        $nombre_pas = $_POST["txtnombre_pas"];
        
        $sql = "INSERT INTO pais (id_pais,nombre_pas) VALUES ('$id_pais,$nombre_pas')";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('País agregado correctamente');</script>";
            // Limpiar campos
            $id_pais = $nombre_pas = $txtbus = "";
        } else {
            echo "<script>alert('Error al agregar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // MODIFICAR
    if($btn == "Modificar" && !empty($_POST["txtid_pais"])){
        $id_pais = $_POST["txtid"];
        $nombre_pas = $_POST["txtnombre_pas"];
        
        $sql = "UPDATE pais SET nombre_pas='$nombre_pas' WHERE id='$id_pais'";
        
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('País modificado correctamente');</script>";
        } else {
            echo "<script>alert('Error al modificar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // ELIMINAR
    if($btn == "Eliminar" && !empty($_POST["txtid_pais"])){
        $id_pais = $_POST["txtid"];
        
        $sql = "DELETE FROM pais WHERE id='$id_pais'";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('País eliminado correctamente');</script>";
            // Limpiar campos
            $id_pais = $nombre_pas = $txtbus = "";
        } else {
            echo "<script>alert('Error al eliminar: " . mysqli_error($cn) . "');</script>";
        }
    }
}
?>

<h1 class="mb-4 text-primary">Gestión de Países</h1>

<div class="card p-4 shadow-sm mb-4 bg-light">
    <h3 class="card-title text-center text-dark">Formulario de Gestión de Países</h3>
    
    <!-- FORMULARIO SEPARADO PARA BÚSQUEDA -->
    <form method="POST" class="mb-4 border-bottom pb-3">
        <div class="row">
            <div class="col-md-8">
                <input type="text" class="form-control" name="txtbus" placeholder="ID del país a buscar" 
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
                <label class="form-label">ID País</label>
                <input type="text" class="form-control" name="txtid_pais" value="<?php echo htmlspecialchars($id_pais); ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nombre del País</label>
                <input type="text" class="form-control" name="txtnombre_pas" value="<?php echo htmlspecialchars($nombre_pas); ?>" required>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="text-center">
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_paises" value="Agregar">
                <i class="bi bi-plus-lg"></i> Agregar
            </button>
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_paises" value="Modificar">
                <i class="bi bi-pencil"></i> Modificar
            </button>
            <button type="submit" class="btn btn-outline-primary" name="btn_paises" value="Eliminar" 
                    onclick="return confirm('¿Estás seguro de eliminar este país?')">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </div>
    </form>
</div>

<!-- Mostrar lista de países -->
<?php
$query_paises = "SELECT * FROM pais ORDER BY id DESC";
$result_paises = mysqli_query($cn, $query_paises);
?>

<h3 class="mb-3 text-primary">Países Registrados</h3>
<div class="table-responsive">
    <table class="table table-striped align-middle mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del País</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_paises && mysqli_num_rows($result_paises) > 0): ?>
                <?php while ($pais = mysqli_fetch_assoc($result_paises)): ?>
                    <tr>
                        <td><?php echo $pais['id']; ?></td>
                        <td><?php echo htmlspecialchars($pais['nombre_pas']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" class="text-center">No hay países registrados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>