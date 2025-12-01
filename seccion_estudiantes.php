<?php
include(__DIR__ . "/conexion.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

// =========================
// VARIABLES
// =========================
$id_termino = $palabra = $pronunciacion = $definicion = "";
$ejemplo = $referencia = $estado = $id_usuario = "";
$mensaje = "";

// =========================
// BOTONES DEL FORMULARIO
// =========================

if(isset($_POST['btnGuardar'])){
    $palabra = mysqli_real_escape_string($cn, $_POST['palabra']);
    $pronunciacion = mysqli_real_escape_string($cn, $_POST['pronunciacion']);
    $definicion = mysqli_real_escape_string($cn, $_POST['definicion']);
    $ejemplo = mysqli_real_escape_string($cn, $_POST['ejemplo']);
    $referencia = mysqli_real_escape_string($cn, $_POST['referencia']);

    $sql = "INSERT INTO termino (palabra, pronunciacion, definicion, ejemplo_aplicativo, referencia_bibliogr, estado)
            VALUES ('$palabra','$pronunciacion','$definicion','$ejemplo','$referencia','Pendiente')";

    if(mysqli_query($cn, $sql)){
        $mensaje = "✔ Término enviado correctamente";
    } else {
        $mensaje = "❌ Error al enviar: " . mysqli_error($cn);
    }
}

if(isset($_POST['btnEditar'])){
    $id = mysqli_real_escape_string($cn, $_POST['id_termino']);
    if($id == ""){
        $mensaje = "⚠ Debes ingresar el ID para editar.";
    } else {
        $sql = "UPDATE termino SET
                palabra='{$_POST['palabra']}',
                pronunciacion='{$_POST['pronunciacion']}',
                definicion='{$_POST['definicion']}',
                ejemplo_aplicativo='{$_POST['ejemplo']}',
                referencia_bibliogr='{$_POST['referencia']}'
                WHERE id_termino='$id'";

        if(mysqli_query($cn, $sql)){
            $mensaje = "✔ Término actualizado correctamente";
        } else {
            $mensaje = "❌ Error al actualizar: " . mysqli_error($cn);
        }
    }
}

if(isset($_POST['btnLimpiar'])){
    $id_termino = $palabra = $pronunciacion = $definicion = "";
    $ejemplo = $referencia = "";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Estudiante</title>

<style>
:root{
    --color-amarillo: #fff06dff;
    --color-azul-oscuro: #006694;
    --color-gris-claro: #f1f2f2;
    --color-azul-claro: #27a5df;
    --color-naranja: #ff9a15;
}

body{
    margin:0;
    padding:0;
    background:var(--color-gris-claro);
    font-family:'Segoe UI', Arial, sans-serif;
    display:flex;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    background:var(--color-azul-oscuro);
    color:white;
    height:100vh;
    padding:20px;
    position:fixed;
    left:0;
    top:0;
}
.sidebar a{
    display:block;
    padding:12px;
    margin-bottom:6px;
    text-decoration:none;
    color:white;
    border-radius:8px;
}
.sidebar a:hover{
    background:#004466;
}
.sidebar a.active{
    background:var(--color-azul-claro);
}

/* MAIN */
.main{
    flex:1;
    margin-left:260px;
    padding:25px;
}
.header{
    background:var(--color-amarillo);
    padding:18px;
    font-size:20px;
    border-radius:10px;
}

/* FORMULARIOS */
.content-box{
    background:white;
    padding:25px;
    border-radius:10px;
    margin-top:20px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
    display:none;
}
.content-box.active{ display:block; }

input, textarea{
    width:100%;
    padding:12px;
    border-radius:8px;
    border:1px solid #ccc;
    margin-bottom:12px;
}

.btn{
    padding:12px 20px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    margin-right:8px;
}
.btn-success{ background:var(--color-azul-claro); color:white; }
.btn-warning{ background:var(--color-naranja); color:white; }
.btn-info{ background:var(--color-amarillo); color:black; }

.mensaje{
    background:#e7f7ff;
    padding:10px;
    border-radius:8px;
    margin-bottom:12px;
    color:#006694;
}
</style>

</head>
<body>

<!-- ====================== SIDEBAR ====================== -->
<div class="sidebar">
    <h2>Panel Estudiante</h2>

    <a class="menu-link active" data-section="datos">Mis Datos</a>
    <a class="menu-link" data-section="pendientes">Pendientes</a>

    <h3>Traducciones</h3>
    <a class="menu-link" data-section="proponer">Proponer Término</a>
    <a class="menu-link" data-section="comentarios">Comentarios</a>

    <h3>Consultas</h3>
<a href="gestion_terminos_estudiantes.php?section=terminos">📘 Ver Términos</a>
<a href="gestion_terminos_estudiantes.php?section=listaResponsable">👤 Ver Responsables</a>
</div>

<!-- ====================== MAIN ====================== -->
<div class="main">
    <div class="header">Bienvenido estudiante</div>

    <?php if($mensaje!="") echo "<div class='mensaje'>$mensaje</div>"; ?>

    <!-- ================== MIS DATOS ================== -->
    <div class="content-box active" id="datos">
        <h1>Mis Datos</h1>
        <p>Aquí puedes mostrar los datos del estudiante si deseas.</p>
    </div>

    <!-- ================== PENDIENTES ================== -->
    <div class="content-box" id="pendientes">
        <h1>Pendientes</h1>
    </div>

    <!-- ================== PROPONER TERMINO (DISEÑO REAL) ================== -->
    <div class="content-box" id="proponer">
        <h1>Proponer Término</h1>

        <form method="POST">

            <label>Palabra:</label>
            <input type="text" name="palabra" required>

            <label>Pronunciación:</label>
            <input type="text" name="pronunciacion">

            <label>Definición:</label>
            <textarea name="definicion" rows="3"></textarea>

            <label>Ejemplo Aplicativo:</label>
            <textarea name="ejemplo" rows="3"></textarea>

            <label>Referencia Bibliográfica:</label>
            <textarea name="referencia" rows="2"></textarea>

            <button class="btn btn-success" name="btnGuardar">Enviar</button>
            <button class="btn btn-warning" name="btnEditar">Editar</button>
            <button class="btn btn-info" name="btnLimpiar">Limpiar</button>

        </form>
    </div>

    <!-- ================== COMENTARIOS ================== -->
    <div class="content-box" id="comentarios">
        <h1>Comentarios del Docente</h1>

        <?php
        $q = mysqli_query($cn, "SELECT * FROM comentario ORDER BY fecha DESC");
        if(mysqli_num_rows($q)==0){
            echo "<p>No hay comentarios aún.</p>";
        }else{
            while($c = mysqli_fetch_assoc($q)){
                echo "<div class='mensaje'>
                        <strong>".$c['docente']."</strong><br>
                        ".$c['comentario']."<br>
                        <small>".$c['fecha']."</small>
                      </div>";
            }
        }
        ?>
    </div>

</div>

<script>
// Control de secciones
document.querySelectorAll(".menu-link").forEach(btn=>{
    btn.addEventListener("click", ()=>{
        document.querySelectorAll(".menu-link").forEach(a=>a.classList.remove("active"));
        btn.classList.add("active");

        document.querySelectorAll(".content-box").forEach(box=>box.classList.remove("active"));
        document.getElementById(btn.dataset.section).classList.add("active");
    });
});
</script>

</body>
</html>
