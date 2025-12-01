<?php
// crud_responsable.php
// Requiere conexion.php en la misma carpeta (que defina $cn)

error_reporting(E_ALL);
ini_set('display_errors', 1);
include(__DIR__ . "/conexion.php"); // <-- debe definir $cn (mysqli)

// Helpers
function esc($s){ return htmlspecialchars($s ?? "", ENT_QUOTES, 'UTF-8'); }
function postv($n){ return isset($_POST[$n]) ? trim($_POST[$n]) : ""; }
function valid_section($s){
    $allowed = ['responsable','listaResponsable','terminos','buscarTermino','detalleTermino','comentarioDetalle','ayuda'];
    return in_array($s, $allowed) ? $s : 'responsable';
}

// Iniciales
$var = $var1 = $var2 = $var3 = $var4 = $var5 = $var6 = $var7 = "";
$detalleTermino = null;
$detalleComentario = null;
$msg_error = $msg_success = "";
$initialSection = 'responsable';

// POST handling
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // función para escapar SQL
    function v($name){ global $cn; return isset($_POST[$name]) ? mysqli_real_escape_string($cn, trim($_POST[$name])) : ""; }

    // CRUD Responsable (botones en el formulario de responsables)
    if(isset($_POST['btn1'])){
        $btn = v('btn1');

        if($btn === 'Buscar' && v('txtbus') !== ""){
            $bus = v('txtbus');
            $sql = "SELECT * FROM responsable WHERE cod_responsable='$bus' LIMIT 1";
            $cs = mysqli_query($cn,$sql);
            if($cs && mysqli_num_rows($cs) > 0){
                $res = mysqli_fetch_array($cs);
                $var  = $res[0]; $var1 = $res[1]; $var2 = $res[2]; $var3 = $res[3];
                $var4 = $res[4]; $var5 = $res[5]; $var6 = $res[6]; $var7 = $res[7];
            } else {
                $msg_error = "Responsable no encontrado.";
            }
            $initialSection = 'responsable';
        }

        if($btn === 'Agregar'){
            $sql = "INSERT INTO responsable (cod_responsable,nombre_resp,apellido_resp,direccion_resp,email_resp,telefono_resp,celular_resp,lugar_trabajo)
                    VALUES ('".v('txtcodR')."','".v('txtnom')."','".v('txtape')."','".v('txtdire')."','".v('txtema')."','".v('txttel')."','".v('txtcel')."','".v('txtluT')."')";
            if(!mysqli_query($cn,$sql)){
                $msg_error = "Error al insertar: ".mysqli_error($cn);
            } else {
                $msg_success = "Responsable agregado correctamente.";
                $var=$var1=$var2=$var3=$var4=$var5=$var6=$var7="";
            }
            $initialSection = 'responsable';
        }

        if($btn === 'Modificar'){
            $sql = "UPDATE responsable SET
                    nombre_resp='".v('txtnom')."',
                    apellido_resp='".v('txtape')."',
                    direccion_resp='".v('txtdire')."',
                    email_resp='".v('txtema')."',
                    telefono_resp='".v('txttel')."',
                    celular_resp='".v('txtcel')."',
                    lugar_trabajo='".v('txtluT')."'
                    WHERE cod_responsable='".v('txtcodR')."'";
            if(!mysqli_query($cn,$sql)){
                $msg_error = "Error al modificar: ".mysqli_error($cn);
            } else {
                $msg_success = "Responsable modificado correctamente.";
            }
            $initialSection = 'responsable';
        }

        if($btn === 'Eliminar'){
            $sql = "DELETE FROM responsable WHERE cod_responsable='".v('txtcodR')."'";
            if(!mysqli_query($cn,$sql)){
                $msg_error = "Error al eliminar: ".mysqli_error($cn);
            } else {
                $msg_success = "Responsable eliminado correctamente.";
                $var=$var1=$var2=$var3=$var4=$var5=$var6=$var7="";
            }
            $initialSection = 'responsable';
        }

        if($btn === 'Mostrar'){
            $initialSection = 'listaResponsable';
        }
    } // fin btn1

    // Buscar termino (desde sección Buscar)
    if(isset($_POST['btnBuscarTermino'])){
        $initialSection = 'buscarTermino';
    }

    // Ver detalle del término (botón "Ver Término")
    if(isset($_POST['verDetalle']) && !empty($_POST['palabraDetalle'])){
        $pal = mysqli_real_escape_string($cn, trim($_POST['palabraDetalle']));
        $sqlD = "SELECT * FROM termino WHERE palabra='$pal' LIMIT 1";
        $resD = mysqli_query($cn, $sqlD);
        if($resD && mysqli_num_rows($resD) > 0){
            $detalleTermino = mysqli_fetch_assoc($resD);
        } else {
            $detalleTermino = null;
            $msg_error = "No se encontró el término solicitado.";
        }
        $initialSection = 'detalleTermino';
    }

    // Ver comentario (botón "Ver Comentario") - usa id_termino
    if(isset($_POST['verComentario']) && !empty($_POST['id_termino'])){
        $idt = intval($_POST['id_termino']);
        // traemos todos los comentarios asociados (orden descendente)
        $sqlC = "SELECT id_comentario, id_termino, comentario, fecha, docente FROM comentario WHERE id_termino = $idt ORDER BY fecha DESC";
        $resC = mysqli_query($cn, $sqlC);
        if($resC && mysqli_num_rows($resC) > 0){
            $detalleComentario = [];
            while($row = mysqli_fetch_assoc($resC)){
                $detalleComentario[] = $row;
            }
        } else {
            $detalleComentario = [];
            $msg_error = "No hay comentarios para ese término.";
        }
        $initialSection = 'comentarioDetalle';
    }
} // fin POST

// GET section (desde panel estudiante)
if(isset($_GET['section'])){
    $initialSection = valid_section(trim($_GET['section']));
}

?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8" />
<title>Sistema Estudiantes</title>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<style>
/* Palette A adaptada (resumen de estilos) */
:root{
  --color-amarillo: #fff06dff;
  --color-azul-oscuro: #006694;
  --color-gris: #636466;
  --color-gris-claro: #f1f2f2;
  --color-naranja: #ff9a15;
  --color-azul-claro: #27a5df;
}
*{box-sizing:border-box;font-family:'Inter', system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif}
body{ background:var(--color-gris-claro); color:var(--color-azul-oscuro); margin:0; display:flex; min-height:100vh; }
/* Sidebar */
.sidebar{ width:260px; background:linear-gradient(180deg,var(--color-azul-oscuro) 0%, #004466 100%); color:white; height:100vh; position:fixed; top:0; left:0; padding:20px 0; box-shadow:4px 0 10px rgba(0,0,0,0.1); display:flex; flex-direction:column; }
.logo{ padding:0 20px 20px; border-bottom:1px solid rgba(255,255,255,0.08); margin-bottom:10px }
.logo h1{ margin:0; text-align:center; color:white; font-size:18px; }
.menu-section{ padding:0 18px; margin-bottom:14px; }
.section-title{ color:var(--color-azul-claro); font-size:12px; text-transform:uppercase; margin-bottom:8px; }
.menu-item{ display:block; padding:10px 12px; color:#e6eef6; text-decoration:none; border-left:3px solid transparent; border-radius:6px; margin-bottom:6px; cursor:pointer; }
.menu-item:hover{ background:rgba(255,255,255,0.05); border-left:3px solid var(--color-amarillo); color:white; }
.menu-item.active{ background:rgba(255,160,109,0.12); border-left:3px solid var(--color-amarillo); color:white; }

/* Main */
.main{ margin-left:260px; padding:28px; flex:1; }
.header{ background:white; padding:14px 20px; border-radius:10px; margin-bottom:18px; box-shadow:0 4px 10px rgba(0,0,0,0.04); color:var(--color-azul-oscuro); }

/* Boxes */
.content-box{ background:white; border-radius:10px; padding:18px; margin-bottom:16px; display:none; box-shadow:0 6px 18px rgba(0,0,0,0.06); max-width:1100px; }
.content-box.active{ display:block; }
.content-box h1{ margin-top:0; color:var(--color-azul-oscuro); }

/* Tables & buttons */
.table-responsive-container{ border-radius:8px; overflow:hidden; background:white; border:1px solid var(--color-gris); }
.data-table{ width:100%; border-collapse:collapse; min-width:700px; }
.data-table th{ background:var(--color-azul-oscuro); color:white; padding:12px; text-align:left; }
.data-table td{ padding:12px; border-bottom:1px solid #f0f0f0; color:var(--color-azul-oscuro); vertical-align:top; }
.data-table tr:nth-child(even){ background:#fafafa; }
.btn{ padding:8px 12px; border-radius:6px; border:none; cursor:pointer; font-weight:700; }
.btn-info{ background:var(--color-azul-claro); color:white; margin-left:6px; }
.btn-orange{ background:var(--color-naranja); color:white; margin-left:6px; }

/* messages */
.message-success{ background: rgba(0,102,148,0.08); color:var(--color-azul-oscuro); padding:10px 12px; border-radius:8px; margin-bottom:12px; }
.message-error{ background:#fdecea; color:#9b1b1b; padding:10px 12px; border-radius:8px; margin-bottom:12px; }

/* responsive */
@media (max-width:900px){ .sidebar{ width:70px } .main{ margin-left:70px; padding:18px } .menu-section{ display:none } .logo{ display:none } }
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <div class="logo"><h1>Sistema — Estudiantes</h1></div>

  <div class="menu-section">
    <div class="section-title">Responsable</div>
    <a href="#" class="menu-item" data-section="responsable">Gestionar Responsable</a>
    <a href="#" class="menu-item" data-section="listaResponsable">Lista de Responsables</a>
  </div>

  <div class="menu-section">
    <div class="section-title">Glosario</div>
    <a href="#" class="menu-item" data-section="terminos">Lista de Términos</a>
    <a href="#" class="menu-item" data-section="buscarTermino">Buscar Término</a>
  </div>

  <div class="menu-section">
    <div class="section-title">General</div>
   <a href="index.php" class="menu-item">Página Principal</a>
    <a href="#" class="menu-item" data-section="ayuda">Ayuda</a>
  </div>

  <div style="margin-top:auto;padding:12px 18px;">
    <a href="#" class="menu-item" onclick="alert('Cerrar sesión (impleméntalo)')">Cerrar Sesión</a>
  </div>
</div>

<!-- MAIN -->
<div class="main">
  <div class="header">Sistema de terminos para estudiantes</div>

  <?php if(!empty($msg_success)) echo "<div class='message-success'>".esc($msg_success)."</div>"; ?>
  <?php if(!empty($msg_error)) echo "<div class='message-error'>".esc($msg_error)."</div>"; ?>

  <!-- Gestionar Responsable -->
  <div class="content-box" id="responsable">
    <h1>Gestionar Responsable</h1>
    <form method="post" action="">
      <div style="display:flex;flex-wrap:wrap;gap:12px;">
        <div style="flex:1 1 240px;"><label>Código:</label><input class="input" type="text" name="txtcodR" value="<?php echo esc($var); ?>"></div>
        <div style="flex:1 1 240px;"><label>Nombre:</label><input class="input" type="text" name="txtnom" value="<?php echo esc($var1); ?>"></div>
        <div style="flex:1 1 240px;"><label>Apellido:</label><input class="input" type="text" name="txtape" value="<?php echo esc($var2); ?>"></div>
      </div>
      <div style="margin-top:10px;">
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
          <input class="btn btn-info" type="submit" name="btn1" value="Agregar">
          <input class="btn btn-info" type="submit" name="btn1" value="Mostrar">
          <input class="btn btn-info" type="submit" name="btn1" value="Modificar">
          <input class="btn btn-info" type="submit" name="btn1" value="Eliminar">
          <input type="text" name="txtbus" placeholder="Código" style="padding:8px;border-radius:6px;border:1px solid #ddd;">
          <input class="btn btn-info" type="submit" name="btn1" value="Buscar">
        </div>
      </div>
    </form>
  </div>

  <!-- Lista de Responsables -->
  <div class="content-box" id="listaResponsable">
    <h1>Lista de Responsables</h1>
    <div class="table-responsive-container">
    <?php
      $sql = "SELECT * FROM responsable";
      $cs = mysqli_query($cn, $sql);
      if($cs){
          echo "<table class='data-table'><tr><th>Código</th><th>Nombre</th><th>Apellido</th><th>Dirección</th><th>Email</th><th>Tel</th><th>Cel</th><th>Trabajo</th></tr>";
          while($r = mysqli_fetch_array($cs)){
              echo "<tr>
                      <td>".esc($r[0])."</td>
                      <td>".esc($r[1])."</td>
                      <td>".esc($r[2])."</td>
                      <td>".esc($r[3])."</td>
                      <td>".esc($r[4])."</td>
                      <td>".esc($r[5])."</td>
                      <td>".esc($r[6])."</td>
                      <td>".esc($r[7])."</td>
                    </tr>";
          }
          echo "</table>";
      } else {
          echo "<p>Error al leer responsables: " . esc(mysqli_error($cn)) . "</p>";
      }
    ?>
    </div>
  </div>

  <!-- LISTA DE TERMINOS -->
  <div class="content-box" id="terminos">
    <h1>Listado de Términos</h1>
    <div class="table-responsive-container">
    <?php
      // traemos id_termino para poder ver comentarios por ID
      $sqlT = "SELECT id_termino, palabra, pronunciacion, estado FROM termino ORDER BY palabra ASC";
      $resT = mysqli_query($cn, $sqlT);
      if($resT){
        echo "<table class='data-table'><tr><th>Palabra</th><th>Pronunciación</th><th>Estado</th><th>Acciones</th></tr>";
        while($row = mysqli_fetch_assoc($resT)){
          echo "<tr>
                  <td>".esc($row['palabra'])."</td>
                  <td>".esc($row['pronunciacion'])."</td>
                  <td>".esc($row['estado'])."</td>
                  <td>
                    <form method='POST' style='display:inline;margin:0;'>
                      <input type='hidden' name='palabraDetalle' value=\"".esc($row['palabra'])."\">
                      <button type='submit' name='verDetalle' class='btn btn-info'>Ver Término</button>
                    </form>

                    <form method='POST' style='display:inline;margin:0 0 0 8px;'>
                      <input type='hidden' name='id_termino' value=\"".intval($row['id_termino'])."\">
                      <button type='submit' name='verComentario' class='btn btn-orange'>Ver Comentario</button>
                    </form>
                  </td>
                </tr>";
        }
        echo "</table>";
      } else {
        echo "<p>Error al leer términos: ".esc(mysqli_error($cn))."</p>";
      }
    ?>
    </div>
  </div>

  <!-- BUSCAR TERMINO -->
  <div class="content-box" id="buscarTermino">
    <h1>Buscar Término</h1>
    <form method="POST" style="margin-bottom:12px;">
      <input style="padding:8px;border-radius:6px;border:1px solid #ddd;width:60%" type="text" name="buscarPalabra" placeholder="Ingrese palabra a buscar..." />
      <button class="btn btn-info" name="btnBuscarTermino" type="submit">Buscar</button>
    </form>

    <?php
    if(isset($_POST['btnBuscarTermino'])){
        $bus = mysqli_real_escape_string($cn, trim($_POST['buscarPalabra']));
        $sqlB = "SELECT id_termino, palabra, pronunciacion, estado FROM termino WHERE palabra LIKE '%$bus%'";
        $resB = mysqli_query($cn, $sqlB);
        if($resB){
          echo "<div class='table-responsive-container'><table class='data-table'><tr><th>Palabra</th><th>Pronunciación</th><th>Estado</th><th>Acción</th></tr>";
          while($r = mysqli_fetch_assoc($resB)){
            echo "<tr>
                    <td>".esc($r['palabra'])."</td>
                    <td>".esc($r['pronunciacion'])."</td>
                    <td>".esc($r['estado'])."</td>
                    <td>
                      <form method='POST' style='display:inline;margin:0;'>
                        <input type='hidden' name='palabraDetalle' value=\"".esc($r['palabra'])."\">
                        <button type='submit' name='verDetalle' class='btn btn-info'>Ver Término</button>
                      </form>
                      <form method='POST' style='display:inline;margin:0 0 0 8px;'>
                        <input type='hidden' name='id_termino' value=\"".intval($r['id_termino'])."\">
                        <button type='submit' name='verComentario' class='btn btn-orange'>Ver Comentario</button>
                      </form>
                    </td>
                  </tr>";
          }
          echo "</table></div>";
        } else {
          echo "<p>Error en búsqueda: ".esc(mysqli_error($cn))."</p>";
        }
    }
    ?>
  </div>

  <!-- DETALLE TERMINO -->
  <div class="content-box" id="detalleTermino">
    <h1>Detalle del Término</h1>
    <?php
    if(isset($detalleTermino) && $detalleTermino !== null){
        echo "<p><strong>Palabra:</strong> ".esc($detalleTermino['palabra'])."</p>";
        echo "<p><strong>Pronunciación:</strong> ".esc($detalleTermino['pronunciacion'])."</p>";
        echo "<p><strong>Definición:</strong><br>".nl2br(esc($detalleTermino['definicion']))."</p>";
        echo "<p><strong>Ejemplo:</strong><br>".nl2br(esc($detalleTermino['ejemplo_aplicativo']))."</p>";
        echo "<p><strong>Referencia:</strong><br>".nl2br(esc($detalleTermino['referencia_bibliogr']))."</p>";
        echo "<p><strong>Estado:</strong> ".esc($detalleTermino['estado'])."</p>";
    } else {
        echo "<p>Seleccione un término para ver el detalle.</p>";
    }
    ?>
  </div>

  <!-- DETALLE COMENTARIO -->
  <div class="content-box" id="comentarioDetalle">
    <h1>Comentarios del Docente</h1>
    <?php
    if(isset($detalleComentario) && is_array($detalleComentario) && count($detalleComentario)>0){
        foreach($detalleComentario as $c){
            echo "<div style='border:1px solid #eee;padding:12px;border-radius:8px;margin-bottom:8px;'>";
            echo "<p style='margin:0 0 6px 0;'><strong>Docente:</strong> ".esc($c['docente'])." <small style='color:#666;margin-left:8px;'>".esc($c['fecha'])."</small></p>";
            echo "<p style='margin:0;'>".nl2br(esc($c['comentario']))."</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No hay comentarios para mostrar.</p>";
    }
    ?>
  </div>

  <!-- AYUDA -->
  <div class="content-box" id="ayuda">
    <h1>Ayuda</h1>
    <p>En esta sección encontrarás información del CRUD y del glosario.</p>
  </div>

  <footer style="margin-top:18px;">© 2025 Sistema Administrativo</footer>
</div>

<script>
// Navegación entre secciones basada en data-section
const menuItems = document.querySelectorAll('.menu-item');
const sections = document.querySelectorAll('.content-box');

function showSection(id){
  sections.forEach(s => s.classList.remove('active'));
  const el = document.getElementById(id);
  if(el) el.classList.add('active');
  menuItems.forEach(i => i.classList.remove('active'));
  const mi = document.querySelector('.menu-item[data-section="'+id+'"]');
  if(mi) mi.classList.add('active');
  window.scrollTo({top:0, behavior:'smooth'});
}

// Activar clicks
menuItems.forEach(item => {
  const target = item.dataset.section;
  if(target){
    item.addEventListener('click', function(e){
      e.preventDefault();
      showSection(target);
    });
  }
});

// Activar sección inicial desde PHP
(function(){
  const initial = "<?php echo esc(valid_section($initialSection)); ?>";
  if(document.getElementById(initial)){
    showSection(initial);
  } else {
    showSection('responsable');
  }
})();
</script>

</body>
</html>
