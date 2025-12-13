<?php
// secciones/gestion_paises.php

//conexion del db
include ("conexion.php");


// Configurar charset para la conexión
if (isset($cn) && is_object($cn)) {
    mysqli_set_charset($cn, "utf8mb4");
}

// Variables para los campos
$id_pais = "";
$nombre_pais = ""; // 
$txtbus = "";

// Procesar BÚSQUEDA (formulario separado)
if(isset($_POST["btn_buscar"]) && $_POST["btn_buscar"] == "Buscar"){
    if(isset($_POST["txtbus"]) && !empty($_POST["txtbus"])){
        $txtbus = $_POST["txtbus"];
        
        // Uso correcto de la variable $nombre_pais
        $sql = "SELECT * FROM pais WHERE id_pais='$txtbus'";
        $cs = mysqli_query($cn, $sql);
        if($cs && mysqli_num_rows($cs) > 0) {
            $resul = mysqli_fetch_array($cs);
            $id_pais = $resul[0];
            $nombre_pais = $resul[1]; 
            echo "<script>alert('País encontrado');</script>";
        } else {
            echo "<script>alert('País no encontrado');</script>";
            // Limpiar campos si no se encuentra
            $id_pais = $nombre_pais = "";
        }
    } else {
        echo "<script>alert('Ingrese un ID para buscar');</script>";
    }
}

// Procesar formulario PRINCIPAL de CRUD
if(isset($_POST["btn_paises"])){
    $btn = $_POST["btn_paises"];
    
    // AGREGAR
    if($btn == "Agregar"){
        $id_pais = $_POST["txtid_pais"]; 
        $nombre_pais = $_POST["txtnombre_pais"]; 
        
        
        $sql = "INSERT INTO pais (id_pais, nombre_pais) VALUES ('$id_pais', '$nombre_pais')";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('País agregado correctamente');</script>";
            // Limpiar campos
            $id_pais = $nombre_pais = $txtbus = "";
        } else {
            echo "<script>alert('Error al agregar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // MODIFICAR
    // Se usa 'txtid_pais' del formulario y se verifica que no esté vacío
    if($btn == "Modificar" && !empty($_POST["txtid_pais"])){
        $id_pais = $_POST["txtid_pais"]; 
        $nombre_pais = $_POST["txtnombre_pais"]; 
        
        
        $sql = "UPDATE pais SET nombre_pais='$nombre_pais' WHERE id_pais='$id_pais'";
        
        // <<-- CORREGIDO: Se usa la variable de conexión correcta $cn
        $cs = mysqli_query($cn, $sql); 
        if($cs) {
            echo "<script>alert('País modificado correctamente');</script>";
             // Limpiar campos
            $id_pais = $nombre_pais = $txtbus = "";
        } else {
            echo "<script>alert('Error al modificar: " . mysqli_error($cn) . "');</script>";
        }
    }
    
    // ELIMINAR
    if($btn == "Eliminar" && !empty($_POST["txtid_pais"])){
        $id_pais = $_POST["txtid_pais"]; // 
        
      
        $sql = "DELETE FROM pais WHERE id_pais='$id_pais'";
        $cs = mysqli_query($cn, $sql);
        if($cs) {
            echo "<script>alert('País eliminado correctamente');</script>";
            // Limpiar campos
            $id_pais = $nombre_pais = $txtbus = "";
        } else {
            echo "<script>alert('Error al eliminar: " . mysqli_error($cn) . "');</script>";
        }
    }
}
?>

<h1 class="mb-4 text-primary">Gestión de Países</h1>

<div class="card p-4 shadow-sm mb-4 bg-light">
    <h3 class="card-title text-center text-dark">Formulario de Gestión de Países</h3>
    
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

    <form method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">ID País</label>
                <input type="text" class="form-control" name="txtid_pais" value="<?php echo htmlspecialchars($id_pais); ?>"readonly 
                       style="background-color: #e9ecef; cursor: not-allowed;" >
            </div>
            <div class="col-md-6">
                <label class="form-label">Nombre del País</label>
                <input type="text" class="form-control" name="txtnombre_pais" value="<?php echo htmlspecialchars($nombre_pais); ?>" >
            </div>
        </div>

       <div class="text-center">
    <button type="submit" class="btn btn-outline-primary me-2" name="btn_paises" value="Agregar">
        <i class="bi bi-plus-lg"></i> Agregar
    </button>
    <button type="submit" class="btn btn-outline-primary me-2" name="btn_paises" value="Mostrar">
        <i class="bi bi-eye"></i> Mostrar
    </button>
    <button type="submit" class="btn btn-outline-primary me-2" name="btn_paises" value="Modificar">
        <i class="bi bi-pencil"></i> Modificar
    </button>
    <button type="submit" class="btn btn-outline-primary" name="btn_paises" value="Eliminar" 
             onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
        <i class="bi bi-trash"></i> Eliminar
    </button>
</div>
    </form>
</div>

<div class="data-container mt-4">
    <?php
    if(isset($_POST["btn_paises"]) && $_POST["btn_paises"] == "Mostrar"){
        $sql="SELECT * FROM pais";
        $cs=mysqli_query($cn,$sql);
        if($cs && mysqli_num_rows($cs) > 0) {
            echo "<div class='contenedor-tabla'>";
            echo "<h3 class='titulo-tabla-terminos mb-4 text-primary'>Lista de Países</h3>";
            echo "<div class='table-responsive-container'>";
            echo "<table class='table table-hover mb-0'>";
            echo "<thead>
                    <tr>
                        <th width='100'>ID</th>
                        <th width='200'>Nombre del País</th>
                    </tr>
                </thead>";
            echo "<tbody>";
            while($resul=mysqli_fetch_array($cs)){
                $id_pais = $resul[0];
                $nombre_pais = $resul[1]; // <<-- CORREGIDO
                
                echo "<tr>
                <td data-label='ID'><strong>$id_pais</strong></td>
                <td data-label='Nombre del País'><strong>$nombre_pais</strong></td>
            </tr>";
            }

            echo "</tbody>";
            echo "</table>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class='alert alert-info text-center'>No hay países registrados</div>";
        }
    }
    ?>
</div>