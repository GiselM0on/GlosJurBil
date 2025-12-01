<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include(__DIR__ . "/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8" />
<title>Panel Administrativo - Estudiantes</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
/* ================================ */
/* ======== PALETA NUEVA ========== */
/* ================================ */
:root {
    --color-amarillo: #fff06d;
    --color-azul-oscuro: #006694;
    --color-gris: #636466;
    --color-gris-claro: #f1f2f2;
    --color-naranja: #ff9a15;
    --color-azul-claro: #27a5df;
}

/* Reset */
*{
  box-sizing:border-box;
  font-family:'Inter', sans-serif;
}

/* Layout general */
body{
  margin:0;
  padding:0;
  background:var(--color-gris-claro);
  color:var(--color-azul-oscuro);
  display:flex;
  min-height:100vh;
}

/* ======================= */
/* ======= SIDEBAR ======= */
/* ======================= */
.sidebar{
  width:260px;
  background: linear-gradient(180deg, var(--color-azul-oscuro), #004466);
  color:white;
  display:flex;
  flex-direction:column;
  padding:20px 0;
  position:fixed;
  top:0; left:0;
  height:100vh;
  box-shadow:4px 0 10px rgba(0,0,0,0.1);
}

.sidebar h2{
  text-align:center;
  font-size:24px;
  font-weight:600;
  margin:0 0 20px;
}

.menu-section{
  margin-bottom:25px;
}

.menu-section h3{
  padding:0 20px;
  font-size:14px;
  text-transform:uppercase;
  color:var(--color-azul-claro);
  margin-bottom:8px;
}

.menu-item{
  padding:12px 20px;
  display:block;
  color:white;
  text-decoration:none;
  border-left:3px solid transparent;
  transition:.3s;
}

.menu-item:hover{
  background:rgba(255,255,255,0.1);
  border-left:3px solid var(--color-amarillo);
}

.menu-item.active{
  background:rgba(255,160,109,0.2);
  border-left:3px solid var(--color-amarillo);
}

/* ========================== */
/* ======== CONTENIDO ======= */
/* ========================== */

.main{
  flex:1;
  margin-left:260px;
  padding:24px;
}

.header{
  background:var(--color-amarillo);
  padding:16px 20px;
  color:var(--color-azul-oscuro);
  border-radius:10px;
  margin-bottom:22px;
  font-weight:600;
  box-shadow:0 4px 10px rgba(0,0,0,0.08);
}

/* Contenedores de secciones */
.content-box{
  display:none;
  background:white;
  padding:22px;
  border-radius:12px;
  box-shadow:0 4px 10px rgba(0,0,0,0.08);
}

.content-box.active{ display:block; }

h1{
  margin-top:0;
  border-bottom:3px solid var(--color-amarillo);
  padding-bottom:8px;
  color:var(--color-azul-oscuro);
}

/* ========================== */
/* ======== FORMULARIOS ===== */
/* ========================== */

.form-row{
  display:flex;
  gap:12px;
  margin-bottom:12px;
  align-items:center;
  flex-wrap:wrap;
}

.form-row label{
  width:180px;
  font-weight:600;
}

.input{
  padding:10px 12px;
  border-radius:8px;
  border:1px solid var(--color-gris);
  min-width:240px;
}

/* Botones */
.btn{
  padding:10px 20px;
  border-radius:8px;
  border:none;
  cursor:pointer;
  font-weight:600;
  transition:.3s;
}

.btn-success{
  background:var(--color-azul-claro);
  color:white;
}
.btn-success:hover{
  background:#1e8bc4;
}

.btn-primary{
  background:var(--color-amarillo);
  color:var(--color-azul-oscuro);
}
.btn-primary:hover{
  background:#ffdf41;
}

.btn-warning{
  background:var(--color-naranja);
  color:white;
}
.btn-warning:hover{
  background:#e68a12;
}

.btn-danger{
  background:#d9534f;
  color:white;
}
.btn-danger:hover{
  background:#c9302c;
}

.btn-info{
  background:var(--color-azul-claro);
  color:white;
}
.btn-info:hover{
  background:#1e8bc4;
}

/* ======================= */
/* ======== TABLAS ======= */
/* ======================= */

.data-table{
  width:100%;
  border-radius:10px;
  overflow:hidden;
  border:1px solid var(--color-gris);
  box-shadow:0 2px 8px rgba(0,0,0,0.05);
}

.data-table th{
  background:var(--color-azul-oscuro);
  color:white;
  padding:12px;
}

.data-table td{
  padding:12px;
  border-bottom:1px solid var(--color-gris-claro);
}

.data-table tr:hover{
  background:rgba(255,160,109,0.1);
}

/* Footer */
footer{
  text-align:center;
  margin-top:30px;
  padding:10px;
  color:var(--color-gris);
}

</style>
</head>

<body>

<!-- ================== SIDEBAR ================== -->
<div class="sidebar">
  <h2>Administración</h2>

  <div class="menu-section">
    <h3>Responsables</h3>
    <a class="menu-item active" data-section="responsable">Gestionar Responsable</a>
    <a class="menu-item" data-section="listaResponsable">Lista de Responsables</a>
  </div>

  <div class="menu-section">
    <h3>Glosario</h3>
    <a class="menu-item" data-section="terminos">Lista de Términos</a>
    <a class="menu-item" data-section="buscarTermino">Buscar Término</a>
  </div>

  <div class="menu-section">
    <h3>General</h3>
    <a class="menu-item" data-section="principal">Página Principal</a>
    <a class="menu-item" data-section="ayuda">Ayuda</a>
  </div>
</div>

<!-- ===================== MAIN ===================== -->
<div class="main">

<div class="header">Sistema Administrativo — Estudiantes</div>

<?php
/* Variables */
$var=$var1=$var2=$var3=$var4=$var5=$var6=$var7="";

/* CRUD Responsable */
if(isset($_POST["btn1"])){

    $btn = $_POST["btn1"];

    if($btn=="Buscar"){
        $bus = mysqli_real_escape_string($cn,$_POST["txtbus"]);
        $sql="SELECT * FROM responsable WHERE cod_responsable='$bus'";
        $r=mysqli_query($cn,$sql);
        if($r && mysqli_num_rows($r)>0){
            $row=mysqli_fetch_array($r);
            $var=$row[0]; $var1=$row[1]; $var2=$row[2]; $var3=$row[3];
            $var4=$row[4]; $var5=$row[5]; $var6=$row[6]; $var7=$row[7];
        }
    }

    if($btn=="Agregar"){
        mysqli_query($cn,"INSERT INTO responsable VALUES(
          '{$_POST["txtcodR"]}','{$_POST["txtnom"]}','{$_POST["txtape"]}',
          '{$_POST["txtdire"]}','{$_POST["txtema"]}','{$_POST["txttel"]}',
          '{$_POST["txtcel"]}','{$_POST["txtluT"]}'
        )");
    }

    if($btn=="Modificar"){
        mysqli_query($cn,"UPDATE responsable SET
          nombre_resp='{$_POST["txtnom"]}',
          apellido_resp='{$_POST["txtape"]}',
          direccion_resp='{$_POST["txtdire"]}',
          email_resp='{$_POST["txtema"]}',
          telefono_resp='{$_POST["txttel"]}',
          celular_resp='{$_POST["txtcel"]}',
          lugar_trabajo='{$_POST["txtluT"]}'
          WHERE cod_responsable='{$_POST["txtcodR"]}'");
    }

    if($btn=="Eliminar"){
        mysqli_query($cn,"DELETE FROM responsable WHERE cod_responsable='{$_POST["txtcodR"]}'");
    }
}
?>

<!-- ===================== FORMULARIO RESPONSABLE ===================== -->
<div class="content-box active" id="responsable">
  <h1>Gestionar Responsable</h1>

  <form method="post">

    <div class="form-row">
      <label>Código:</label>
      <input class="input" name="txtcodR" value="<?php echo $var;?>">
    </div>

    <div class="form-row">
      <label>Nombre:</label>
      <input class="input" name="txtnom" value="<?php echo $var1;?>">
    </div>

    <div class="form-row">
      <label>Apellido:</label>
      <input class="input" name="txtape" value="<?php echo $var2;?>">
    </div>

    <div class="form-row">
      <label>Dirección:</label>
      <input class="input" name="txtdire" value="<?php echo $var3;?>">
    </div>

    <div class="form-row">
      <label>Correo:</label>
      <input class="input" name="txtema" value="<?php echo $var4;?>">
    </div>

    <div class="form-row">
      <label>Teléfono:</label>
      <input class="input" name="txttel" value="<?php echo $var5;?>">
    </div>

    <div class="form-row">
      <label>Celular:</label>
      <input class="input" name="txtcel" value="<?php echo $var6;?>">
    </div>

    <div class="form-row">
      <label>Lugar Trabajo:</label>
      <input class="input" name="txtluT" value="<?php echo $var7;?>">
    </div>

    <div class="btn-row">
      <button class="btn btn-success" name="btn1" value="Agregar">Agregar</button>
      <button class="btn btn-primary" name="btn1" value="Mostrar">Mostrar</button>
      <button class="btn btn-warning" name="btn1" value="Modificar">Modificar</button>
      <button class="btn btn-danger" name="btn1" value="Eliminar">Eliminar</button>

      <input class="input" name="txtbus" placeholder="Código...">
      <button class="btn btn-info" name="btn1" value="Buscar">Buscar</button>
    </div>

  </form>
</div>

<!-- ============ LISTA RESPONSABLE ============ -->
<div class="content-box" id="listaResponsable">
  <h1>Lista de Responsables</h1>

  <?php
  $res=mysqli_query($cn,"SELECT * FROM responsable");

  echo "<table class='data-table'>
        <tr><th>Código</th><th>Nombre</th><th>Apellido</th><th>Dirección</th>
        <th>Email</th><th>Tel</th><th>Cel</th><th>Trabajo</th></tr>";

  while($r=mysqli_fetch_array($res)){
    echo "<tr>
      <td>$r[0]</td><td>$r[1]</td><td>$r[2]</td>
      <td>$r[3]</td><td>$r[4]</td><td>$r[5]</td>
      <td>$r[6]</td><td>$r[7]</td>
    </tr>";
  }
  echo "</table>";
  ?>
</div>

<!-- ============ LISTA TÉRMINOS ============ -->
<div class="content-box" id="terminos">
  <h1>Glosario Completo</h1>

  <?php
  $q=mysqli_query($cn,"SELECT palabra, pronunciacion, estado FROM termino ORDER BY palabra ASC");

  echo "<table class='data-table'>
        <tr><th>Palabra</th><th>Pronunciación</th><th>Estado</th><th>Ver</th></tr>";

  while($t=mysqli_fetch_assoc($q)){
    echo "<tr>
      <td>{$t['palabra']}</td>
      <td>{$t['pronunciacion']}</td>
      <td>{$t['estado']}</td>
      <td>
        <form method='POST'>
          <input type='hidden' name='palabraDetalle' value='{$t['palabra']}'>
          <button class='btn btn-info' name='verDetalle'>Ver</button>
        </form>
      </td>
    </tr>";
  }
  echo "</table>";
  ?>
</div>

<!-- ============ BUSCAR TÉRMINO ============ -->
<div class="content-box" id="buscarTermino">
  <h1>Buscar Término</h1>

  <form method="POST">
    <input class="input" type="text" name="buscarPalabra" placeholder="Buscar palabra...">
    <button class="btn btn-primary" name="btnBuscarTermino">Buscar</button>
  </form>

  <?php
  if(isset($_POST["btnBuscarTermino"])){
    $bus=mysqli_real_escape_string($cn,$_POST["buscarPalabra"]);
    $q=mysqli_query($cn,"SELECT palabra, pronunciacion, estado FROM termino WHERE palabra LIKE '%$bus%'");

    echo "<table class='data-table'>
          <tr><th>Palabra</th><th>Pronunciación</th><th>Estado</th><th>Ver</th></tr>";

    while($r=mysqli_fetch_assoc($q)){
      echo "<tr>
        <td>{$r['palabra']}</td>
        <td>{$r['pronunciacion']}</td>
        <td>{$r['estado']}</td>
        <td>
          <form method='POST'>
            <input type='hidden' name='palabraDetalle' value='{$r['palabra']}'>
            <button class='btn btn-info' name='verDetalle'>Ver</button>
          </form>
        </td>
      </tr>";
    }
    echo "</table>";
  }
  ?>
</div>

<!-- ============ DETALLE TÉRMINO ============ -->
<div class="content-box" id="detalleTermino">
  <h1>Detalle del Término</h1>

  <?php
  if(isset($_POST["verDetalle"])){
    $pal=mysqli_real_escape_string($cn,$_POST["palabraDetalle"]);
    $d=mysqli_fetch_assoc(mysqli_query($cn,"SELECT * FROM termino WHERE palabra='$pal'"));

    echo "<p><strong>Palabra:</strong> {$d['palabra']}</p>";
    echo "<p><strong>Pronunciación:</strong> {$d['pronunciacion']}</p>";
    echo "<p><strong>Definición:</strong><br>{$d['definicion']}</p>";
    echo "<p><strong>Ejemplo:</strong><br>{$d['ejemplo_aplicativo']}</p>";
    echo "<p><strong>Referencia:</strong><br>{$d['referencia_bibliogr']}</p>";
    echo "<p><strong>Estado:</strong> {$d['estado']}</p>";
  } else {
    echo "<p>Seleccione un término del glosario para ver su detalle.</p>";
  }
  ?>
</div>

<!-- ============ PRINCIPAL ============ -->
<div class="content-box" id="principal">
  <h1>Página Principal</h1>
  <p>Bienvenido al sistema administrativo de estudiantes.</p>
</div>

<!-- ============ AYUDA ============ -->
<div class="content-box" id="ayuda">
  <h1>Ayuda</h1>
  <p>Aquí encontrarás instrucciones de uso del sistema.</p>
</div>

<footer>© 2025 Sistema Administrativo</footer>
</div>

<script>
/* Manejo básico de secciones */
const menuItems = document.querySelectorAll('.menu-item');
const sections = document.querySelectorAll('.content-box');

function showSection(id){
  sections.forEach(s => s.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  menuItems.forEach(i => i.classList.remove('active'));
  document.querySelector(`[data-section="${id}"]`).classList.add('active');
}

menuItems.forEach(i => {
  if(i.dataset.section){
    i.addEventListener('click', () => showSection(i.dataset.section));
  }
});
</script>

</body>
</html>

