<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include(__DIR__ . "/conexion.php");
/* ======== INDEX DE SECCION_ESTUDIANTE ======== */
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>Sistema Administrativo - Responsable y Glosario</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>

<style>
/* ======== COLORES ======== */
:root{
  --azul-oscuro: #0B2A64;
  --azul-medio: #1A4FA3;
  --azul-claro: #DFE6F5;
  --fondo-general: #E9EEF7;
  --blanco: #FFFFFF;
  --gris-claro: #F6F8FB;
  --texto: #333333;
}

/* RESET */
*{ box-sizing:border-box; font-family:'Segoe UI', Arial, sans-serif }

/* ======== LAYOUT ======== */
body{
  margin:0; padding:0;
  background:var(--fondo-general);
  color:var(--texto);
  display:flex;
  min-height:100vh;
}

/* PANEL LATERAL */
.sidebar{
  width:260px;
  background:var(--azul-oscuro);
  color:white;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  padding:22px 14px;
}
.sidebar h2{ text-align:center; margin:0 0 15px 0; }
.menu-section h3{
  font-size:12px; color:#9cb3d1;
  margin:20px 0 8px 0;
  text-transform:uppercase;
}
.menu-item{
  display:block;
  padding:10px 12px;
  border-radius:8px;
  color:white; text-decoration:none;
  cursor:pointer;
  margin-bottom:6px;
}
.menu-item:hover, .menu-item.active{
  background:var(--azul-medio);
}

/* MAIN */
.main{ flex:1; padding:28px; }
.header{
  background:var(--azul-claro);
  padding:14px 22px;
  border-radius:10px;
  margin-bottom:22px;
}

/* SECCIONES */
.content-box{
  background:white;
  padding:22px;
  border-radius:12px;
  margin-bottom:18px;
  box-shadow:0 6px 18px rgba(0,0,0,0.08);
  display:none;
}
.content-box.active{ display:block; }

/* FORMULARIOS */
.form-row{
  display:flex;
  gap:12px;
  margin-bottom:12px;
}
.form-row label{ font-weight:600; width:160px; }
.input{
  padding:10px; border:1px solid #d7dfe9;
  border-radius:8px; min-width:220px;
}

/* BOTONES */
.btn-row{ display:flex; gap:12px; margin-top:10px; }
.btn{ padding:10px 18px; border:none; border-radius:8px; cursor:pointer; font-weight:700; }
.btn-success{ background:#2e8b57; color:white; }
.btn-primary{ background:#1A4FA3; color:white; }
.btn-warning{ background:#ffc107; }
.btn-danger{ background:#dc3545; color:white; }
.btn-info{ background:#17a2b8; color:white; }

/* TABLAS */
.data-table{ width:100%; border-collapse:collapse; }
.data-table th{
  background:var(--azul-oscuro); color:white; padding:12px;
}
.data-table td{ padding:12px; border-bottom:1px solid #eee; }
.data-table tr:nth-child(even){ background:var(--gris-claro); }

footer{ text-align:center; padding:20px; }
</style>
</head>
<body>

<!-- ========================================= -->
<!-- ============ SIDEBAR ===================== -->
<!-- ========================================= -->
<div class="sidebar">
  <div>
    <h2>Panel Administrativo</h2>

    <div class="menu-section">
      <h3>Responsable</h3>
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
</div>

<!-- ========================================= -->
<!-- ============ MAIN ======================== -->
<!-- ========================================= -->
<div class="main">

<div class="header">Sistema Administrativo</div>

<?php
/* VARIABLES INICIALES */
$var=$var1=$var2=$var3=$var4=$var5=$var6=$var7="";

/* ========== CRUD RESPONSABLE ========== */
if(isset($_POST["btn1"])){
  $btn = $_POST["btn1"];

  if($btn=="Buscar"){
    $bus=mysqli_real_escape_string($cn,$_POST["txtbus"]);
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

<!-- ========================================= -->
<!-- ============ FORM RESPONSABLE ========== -->
<!-- ========================================= -->
<div class="content-box active" id="responsable">
  <h1>Gestionar Responsable</h1>

  <form method="post">
    <div class="form-row"><label>Código:</label> <input class="input" name="txtcodR" value="<?php echo $var;?>"></div>
    <div class="form-row"><label>Nombre:</label> <input class="input" name="txtnom" value="<?php echo $var1;?>"></div>
    <div class="form-row"><label>Apellido:</label> <input class="input" name="txtape" value="<?php echo $var2;?>"></div>
    <div class="form-row"><label>Dirección:</label> <input class="input" name="txtdire" value="<?php echo $var3;?>"></div>
    <div class="form-row"><label>Correo:</label> <input class="input" name="txtema" value="<?php echo $var4;?>"></div>
    <div class="form-row"><label>Teléfono:</label> <input class="input" name="txttel" value="<?php echo $var5;?>"></div>
    <div class="form-row"><label>Celular:</label> <input class="input" name="txtcel" value="<?php echo $var6;?>"></div>
    <div class="form-row"><label>Lugar Trabajo:</label> <input class="input" name="txtluT" value="<?php echo $var7;?>"></div>
    
    <div class="btn-row">
      <button class="btn btn-success" name="btn1" value="Agregar">Agregar</button>
      <button class="btn btn-primary" name="btn1" value="Mostrar">Mostrar</button>
      <button class="btn btn-warning" name="btn1" value="Modificar">Modificar</button>
      <button class="btn btn-danger" name="btn1" value="Eliminar">Eliminar</button>

      <input type="text" class="input" name="txtbus" placeholder="Código...">
      <button class="btn btn-info" name="btn1" value="Buscar">Buscar</button>
    </div>
  </form>
</div>

<!-- ========================================= -->
<!-- ============ LISTA RESPONSABLE ========== -->
<!-- ========================================= -->
<div class="content-box" id="listaResponsable">
  <h1>Lista de Responsables</h1>

  <?php
  $res=mysqli_query($cn,"SELECT * FROM responsable");

  echo "<table class='data-table'>
        <tr><th>Código</th><th>Nombre</th><th>Apellido</th><th>Dirección</th><th>Email</th><th>Tel</th><th>Cel</th><th>Trabajo</th></tr>";

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

<!-- ========================================= -->
<!-- ============ LISTA DE TÉRMINOS ========== -->
<!-- ========================================= -->
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

<!-- ========================================= -->
<!-- ============ BUSCAR TÉRMINO ============= -->
<!-- ========================================= -->
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

<!-- ========================================= -->
<!-- ============ DETALLE TÉRMINO =========== -->
<!-- ========================================= -->
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

<!-- ========================================= -->
<!-- ============ PRINCIPAL ================== -->
<!-- ========================================= -->
<div class="content-box" id="principal">
  <h1>Página Principal</h1>
  <p>Bienvenido al sistema administrativo.</p>
</div>

<!-- ========================================= -->
<!-- ============ AYUDA ====================== -->
<!-- ========================================= -->
<div class="content-box" id="ayuda">
  <h1>Ayuda</h1>
  <p>Aquí podrás encontrar instrucciones de uso.</p>
</div>

<footer>© 2025 Sistema Universitario</footer>
</div>

<script>
/* MANEJO DE SECCIONES */
const menuItems=document.querySelectorAll('.menu-item');
const sections=document.querySelectorAll('.content-box');

function showSection(id){
  sections.forEach(s=>s.classList.remove('active'));
  document.getElementById(id).classList.add('active');

  menuItems.forEach(i=>i.classList.remove('active'));
  document.querySelector(`[data-section="${id}"]`).classList.add('active');
}

menuItems.forEach(i=>{
  i.addEventListener('click',()=>showSection(i.dataset.section));
});

/* ABRIR SECCIONES SEGÚN ACCIÓN POST */
<?php
if(isset($_POST["btn1"])){
  if($_POST["btn1"]=="Mostrar") echo "showSection('listaResponsable');";
  else echo "showSection('responsable');";
}

if(isset($_POST["btnBuscarTermino"])) echo "showSection('buscarTermino');";
if(isset($_POST["verDetalle"])) echo "showSection('detalleTermino');";
?>
</script>

</body>
</html>
