<?php
// secciones/gestion_idiomas.php

//conexion del db
include ("conexion.php");

// Variables para los campos
$id_idioma = "";
$nombre_idioma = "";
$txtbus = "";

// Procesar BÚSQUEDA (formulario separado)
if(isset($_POST["btn_buscar"]) && $_POST["btn_buscar"] == "Buscar"){
    if(isset($_POST["txtbus"]) && !empty($_POST["txtbus"])){
        $txtbus = $_POST["txtbus"];
        
        $sql = "SELECT * FROM idioma WHERE id_idioma='$txtbus'";
        $cs = mysqli_query($cn, $sql);
        if($cs && mysqli_num_rows($cs) > 0) {
            $resul = mysqli_fetch_array($cs);
            $id_idioma = $resul[0];
            $nombre_idioma = $resul[1];
            echo "<script>alert('Idioma encontrado');</script>";
        } else {
            echo "<script>alert('Idioma no encontrado');</script>";
            // Limpiar campos si no se encuentra
            $id_idioma = $nombre_idioma = "";
        }
    } else {
        echo "<script>alert('Ingrese un ID para buscar');</script>";
    }
}

// Procesar formulario PRINCIPAL (Agregar, Modificar, Eliminar)
if(isset($_POST["btn_idiomas"])){
    $btn = $_POST["btn_idiomas"];
    
    // AGREGAR
    if($btn == "Agregar"){
        $nombre_idioma = $_POST["txtnombre_idioma"];
        
        $sql = "INSERT INTO idioma (id_idioma,nombre_idioma) VALUES ('$id_idioma,$nombre_idioma')";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Idioma agregado correctamente');</script>";
            // Limpiar campos
            $id_idioma = $nombre_idioma = $txtbus = "";
        } else {
            echo "<script>alert('Error al agregar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // MODIFICAR
    if($btn == "Modificar" && !empty($_POST["txtid_idioma"])){
        $id_idioma = $_POST["txtid"];
        $nombre_idioma = $_POST["txtnombre_idioma"];
        
        $sql = "UPDATE idioma SET id_idioma = '$id_idioma',nombre_idioma='$nombre_idioma' WHERE id='$id_idioma'";
        
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Idioma modificado correctamente');</script>";
        } else {
            echo "<script>alert('Error al modificar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // ELIMINAR
    if($btn == "Eliminar" && !empty($_POST["txtid_idioma"])){
        $id_idioma = $_POST["txtid"];
        
        $sql = "DELETE FROM idioma WHERE id_idioma ='$id_idioma'";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('Idioma eliminado correctamente');</script>";
            // Limpiar campos
            $id_idioma = $nombre_idioma = $txtbus = "";
        } else {
            echo "<script>alert('Error al eliminar: " . mysqli_error($cn) . "');</script>";
        }
    }
}
?>

<h1 class="mb-4 text-primary">Gestión de Idiomas</h1>

<div class="card p-4 shadow-sm mb-4 bg-light">
    <h3 class="card-title text-center text-dark">Formulario de Gestión de Idiomas</h3>
    
    <!-- FORMULARIO SEPARADO PARA BÚSQUEDA -->
    <form method="POST" class="mb-4 border-bottom pb-3">
        <div class="row">
            <div class="col-md-8">
                <input type="text" class="form-control" name="txtbus" placeholder="ID del idioma a buscar" 
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
                <label class="form-label">ID Idioma</label>
                <input type="text" class="form-control" name="txtid_idioma" value="<?php echo htmlspecialchars($id_idioma); ?>" >
            </div>
            <div class="col-md-6">
                <label class="form-label">Nombre del Idioma</label>
                <input type="text" class="form-control" name="txtnombre_idioma" value="<?php echo htmlspecialchars($nombre_idioma); ?>" required>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="text-center">
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_idiomas" value="Agregar">
                <i class="bi bi-plus-lg"></i> Agregar
            </button>
            <button type="submit" class="btn btn-outline-primary me-2" name="btn_idiomas" value="Modificar">
                <i class="bi bi-pencil"></i> Modificar
            </button>
            <button type="submit" class="btn btn-outline-primary" name="btn_idiomas" value="Eliminar" 
                    onclick="return confirm('¿Estás seguro de eliminar este idioma?')">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </div>
    </form>
</div>

<!-- Mostrar lista de idiomas -->
<?php
$query_idiomas = "SELECT * FROM idioma ORDER BY id DESC";
$result_idiomas = mysqli_query($cn, $query_idiomas);
?>

