<?php
// crud_responsable.php - Archivo único y autocontenido (requiere conexion.php en la misma carpeta)
error_reporting(E_ALL);
ini_set('display_errors', 1);
include(__DIR__ . "/conexion.php"); // debe definir $cn (mysqli)

// helpers
function esc($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function postv($name){ return isset($_POST[$name]) ? trim($_POST[$name]) : ""; }

// validar sección permitida (evitamos inyección JS)
function valid_section($s){
    $allowed = ['responsable','listaResponsable','terminos','buscarTermino','detalleTermino','ayuda'];
    return in_array($s, $allowed) ? $s : 'responsable';
}

// ---------------------------------------------
//  Procesamiento servidor (POST) - CRUD y búsquedas
// ---------------------------------------------
$var = $var1 = $var2 = $var3 = $var4 = $var5 = $var6 = $var7 = "";

// inicializamos la sección a mostrar (se decide después)
$initialSection = 'responsable';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // escape para SQL
    function v($name){ global $cn; return isset($_POST[$name]) ? mysqli_real_escape_string($cn, trim($_POST[$name])) : ""; }

    // CRUD Responsable
    if (isset($_POST['btn1'])) {
        $btn = v('btn1');

        if ($btn === 'Buscar' && v('txtbus') !== "") {
            $bus = v('txtbus');
            $sql = "SELECT * FROM responsable WHERE cod_responsable='$bus' LIMIT 1";
            $cs = mysqli_query($cn, $sql);
            if ($cs && mysqli_num_rows($cs) > 0) {
                $res = mysqli_fetch_array($cs);
                $var  = $res[0]; $var1 = $res[1]; $var2 = $res[2]; $var3 = $res[3];
                $var4 = $res[4]; $var5 = $res[5]; $var6 = $res[6]; $var7 = $res[7];
            }
            $initialSection = 'responsable';
        }

        if ($btn === 'Agregar') {
            $sql = "INSERT INTO responsable (cod_responsable,nombre_resp,apellido_resp,direccion_resp,email_resp,telefono_resp,celular_resp,lugar_trabajo)
                    VALUES ('".v('txtcodR')."','".v('txtnom')."','".v('txtape')."','".v('txtdire')."','".v('txtema')."','".v('txttel')."','".v('txtcel')."','".v('txtluT')."')";
            if(!mysqli_query($cn,$sql)){
                $msg_error = "Error al insertar: ".mysqli_error($cn);
            } else {
                $msg_success = "Responsable agregado correctamente.";
                // limpiar variables
                $var=$var1=$var2=$var3=$var4=$var5=$var6=$var7="";
            }
            $initialSection = 'responsable';
        }

        if ($btn === 'Modificar') {
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

        if ($btn === 'Eliminar') {
            $sql = "DELETE FROM responsable WHERE cod_responsable='".v('txtcodR')."'";
            if(!mysqli_query($cn,$sql)){
                $msg_error = "Error al eliminar: ".mysqli_error($cn);
            } else {
                $msg_success = "Responsable eliminado correctamente.";
                $var=$var1=$var2=$var3=$var4=$var5=$var6=$var7="";
            }
            $initialSection = 'responsable';
        }

        if ($btn === 'Mostrar') {
            $initialSection = 'listaResponsable';
        }
    }

    // Buscar termino
    if (isset($_POST['btnBuscarTermino'])) {
        $initialSection = 'buscarTermino';
    }

    // Ver detalle termino
    if (isset($_POST['verDetalle']) && !empty($_POST['palabraDetalle'])) {
        $pal = mysqli_real_escape_string($cn, trim($_POST['palabraDetalle']));
        // cargamos datos del termino para mostrar
        $sqlD = "SELECT * FROM termino WHERE palabra='$pal' LIMIT 1";
        $resD = mysqli_query($cn, $sqlD);
        if ($resD && mysqli_num_rows($resD) > 0) {
            $detalleTermino = mysqli_fetch_assoc($resD);
        } else {
            $detalleTermino = null;
        }
        $initialSection = 'detalleTermino';
    }
}

// Si viene por GET desde panel estudiante (ej: crud_responsable.php?section=terminos)
if (isset($_GET['section'])) {
    $initialSection = valid_section(trim($_GET['section']));
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>CRUD Responsable</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <style>
    :root{
      --azul-oscuro: #0B2A64;
      --azul-medio: #1A4FA3;
      --azul-claro: #DFE6F5;
      --fondo-general: #E9EEF7;
      --blanco: #FFFFFF;
      --gris-claro: #F6F8FB;
      --texto: #333333;
    }
    *{box-sizing:border-box;font-family:'Segoe UI', Arial, sans-serif}
    body{ margin:0; padding:0; background:var(--fondo-general); color:var(--texto); display:flex; min-height:100vh; }
    .sidebar{ width:260px; background:var(--azul-oscuro); color:var(--blanco); display:flex; flex-direction:column; justify-content:space-between; padding:22px 14px; gap:12px; }
    .sidebar h2{ text-align:center; font-size:20px; margin:6px 0 12px 0; }
    .menu-section{ padding-left:8px; margin-bottom:10px; }
    .menu-section h3{ font-size:12px; color:#9cb3d1; margin:0 0 8px 0; text-transform:uppercase; }
    .menu-item{ display:block; color:white; padding:10px 12px; margin-bottom:6px; border-radius:8px; text-decoration:none; transition:background .18s, transform .15s; cursor:pointer; }
    .menu-item:hover, .menu-item.active{ background:var(--azul-medio); transform:translateX(4px); }
    .logout{ text-align:center; padding:12px; border-top:1px solid rgba(255,255,255,0.2); }
    .logout a{ color:#ffb4b4; text-decoration:none; font-weight:600; }
    .main{ flex:1; padding:28px; }
    .header{ background:var(--azul-claro); padding:14px 22px; border-radius:10px; color:var(--azul-oscuro); margin-bottom:22px; box-shadow: 0 2px 8px rgba(11,42,100,0.06); }
    .content-box{ background:white; border-radius:12px; padding:22px; margin-bottom:18px; box-shadow:0 6px 18px rgba(0,0,0,0.06); display:none; max-width:1100px; }
    .content-box.active{ display:block; }
    .content-box h1{ color:var(--azul-oscuro); margin-bottom:12px; font-size:32px; }
    .form-row{ display:flex; gap:12px; flex-wrap:wrap; margin-bottom:12px; align-items:center; }
    .form-row label{ font-weight:600; color:var(--azul-oscuro); min-width:160px; }
    .input, input[type=text], input[type=number], textarea { padding:10px 12px; border-radius:8px; border:1px solid #d7dfe9; background:#fff; min-width:260px; }
    .btn-row{ display:flex; gap:12px; flex-wrap:wrap; margin-top:10px; }
    .btn{ padding:10px 18px; border-radius:8px; border:none; cursor:pointer; font-weight:700; }
    .btn-primary{ background:#1A4FA3; color:white; }
    .btn-success{ background:#2e8b57; color:white; }
    .btn-warning{ background:#ffc107; color:#222; }
    .btn-danger{ background:#dc3545; color:white; }
    .btn-info{ background:#17a2b8; color:white; }
    .data-table{ width:100%; border-collapse:collapse; background:white; border-radius:8px; overflow:hidden; margin-top:12px; }
    .data-table th{ background:var(--azul-oscuro); color:white; padding:12px; text-align:left; }
    .data-table td{ padding:12px; border-bottom:1px solid #eee; vertical-align:top; }
    .data-table tr:nth-child(even){ background:var(--gris-claro); }
    footer{ text-align:center; color:#666; padding:18px 0; margin-top:22px; }
    @media (max-width:900px){ .sidebar{ position:fixed; left:0; top:0; bottom:0; z-index:40; width:220px; } .main{ margin-left:220px; padding:18px; } }
    .message-success{ background:#e6f7ea; color:#1b6d2b; padding:10px 14px; border-radius:8px; margin-bottom:12px; }
    .message-error{ background:#fdecea; color:#9b1b1b; padding:10px 14px; border-radius:8px; margin-bottom:12px; }
  </style>
</head>
<body>

  <!-- SIDEBAR -->
  <div class="sidebar">
    <div>
      <h2>Panel Administrativo</h2>

      <div class="menu-section">
        <h3>Responsable</h3>
        <a href="#" class="menu-item" data-section="responsable">Gestionar Responsable</a>
        <a href="#" class="menu-item" data-section="listaResponsable">Lista de Responsables</a>
      </div>

      <div class="menu-section">
        <h3>Glosario</h3>
        <a href="#" class="menu-item" data-section="terminos">Lista de Términos</a>
        <a href="#" class="menu-item" data-section="buscarTermino">Buscar Término</a>
      </div>

      <div class="menu-section">
        <h3>General</h3>
        <a href="index.php" class="menu-item">Página Principal</a>
        <a href="#" class="menu-item" data-section="ayuda">Ayuda</a>
      </div>
    </div>

    <div class="logout"><a href="#">Cerrar Sesión</a></div>
  </div>

  <!-- MAIN -->
  <div class="main">
    <div class="header">CRUD — Responsable</div>

    <?php if(!empty($msg_success)) echo "<div class='message-success'>".esc($msg_success)."</div>"; ?>
    <?php if(!empty($msg_error)) echo "<div class='message-error'>".esc($msg_error)."</div>"; ?>

    <!-- SECCIÓN: RESPONSABLE (formulario) -->
    <div class="content-box" id="responsable">
      <h1>Gestionar Responsable</h1>

      <form method="post" action="">
        <div class="form-row"><label for="txtcodR">Código:</label><input id="txtcodR" class="input" type="text" name="txtcodR" value="<?php echo esc($var); ?>"></div>
        <div class="form-row"><label for="txtnom">Nombre:</label><input id="txtnom" class="input" type="text" name="txtnom" value="<?php echo esc($var1); ?>"></div>
        <div class="form-row"><label for="txtape">Apellido:</label><input id="txtape" class="input" type="text" name="txtape" value="<?php echo esc($var2); ?>"></div>
        <div class="form-row"><label for="txtdire">Dirección:</label><input id="txtdire" class="input" type="text" name="txtdire" value="<?php echo esc($var3); ?>"></div>
        <div class="form-row"><label for="txtema">Correo:</label><input id="txtema" class="input" type="text" name="txtema" value="<?php echo esc($var4); ?>"></div>
        <div class="form-row"><label for="txttel">Teléfono:</label><input id="txttel" class="input" type="text" name="txttel" value="<?php echo esc($var5); ?>"></div>
        <div class="form-row"><label for="txtcel">Celular:</label><input id="txtcel" class="input" type="text" name="txtcel" value="<?php echo esc($var6); ?>"></div>
        <div class="form-row"><label for="txtluT">Lugar de trabajo:</label><input id="txtluT" class="input" type="text" name="txtluT" value="<?php echo esc($var7); ?>"></div>

        <div class="btn-row">
          <input type="submit" name="btn1" value="Agregar" class="btn btn-success" />
          <input type="submit" name="btn1" value="Mostrar" class="btn btn-primary" />
          <input type="submit" name="btn1" value="Modificar" class="btn btn-warning" />
          <input type="submit" name="btn1" value="Eliminar" class="btn btn-danger" />
          <input type="text" name="txtbus" placeholder="Código" style="padding:10px;border:1px solid #ccc;border-radius:8px;" />
          <input type="submit" name="btn1" value="Buscar" class="btn btn-info" />
        </div>
      </form>
    </div>

    <!-- SECCIÓN: LISTA RESPONSABLE -->
    <div class="content-box" id="listaResponsable">
      <h1>Lista de Responsables</h1>
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

    <!-- SECCIÓN: TÉRMINOS (glosario) -->
    <div class="content-box" id="terminos">
      <h1>Listado de Términos</h1>
      <?php
        $sqlT = "SELECT palabra, pronunciacion, estado FROM termino ORDER BY palabra ASC";
        $resT = mysqli_query($cn, $sqlT);
        if($resT){
          echo "<table class='data-table'><tr><th>Palabra</th><th>Pronunciación</th><th>Estado</th><th>Acción</th></tr>";
          while($row = mysqli_fetch_assoc($resT)){
            echo "<tr>
                    <td>".esc($row['palabra'])."</td>
                    <td>".esc($row['pronunciacion'])."</td>
                    <td>".esc($row['estado'])."</td>
                    <td>
                      <form method='POST' style='margin:0;'>
                        <input type='hidden' name='palabraDetalle' value=\"".esc($row['palabra'])."\">
                        <button type='submit' name='verDetalle' class='btn btn-info'>Ver</button>
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

    <!-- SECCIÓN: BUSCAR TÉRMINO -->
    <div class="content-box" id="buscarTermino">
      <h1>Buscar Término</h1>
      <form method="POST" style="margin-bottom:12px;">
        <input class="input" type="text" name="buscarPalabra" placeholder="Ingrese palabra a buscar..." />
        <button class="btn btn-primary" name="btnBuscarTermino" type="submit">Buscar</button>
      </form>

      <?php
      if(isset($_POST['btnBuscarTermino'])){
          $bus = mysqli_real_escape_string($cn, trim($_POST['buscarPalabra']));
          $sqlB = "SELECT palabra, pronunciacion, estado FROM termino WHERE palabra LIKE '%$bus%'";
          $resB = mysqli_query($cn, $sqlB);
          if($resB){
            echo "<table class='data-table'><tr><th>Palabra</th><th>Pronunciación</th><th>Estado</th><th>Acción</th></tr>";
            while($r = mysqli_fetch_assoc($resB)){
              echo "<tr>
                      <td>".esc($r['palabra'])."</td>
                      <td>".esc($r['pronunciacion'])."</td>
                      <td>".esc($r['estado'])."</td>
                      <td>
                        <form method='POST' style='margin:0;'>
                          <input type='hidden' name='palabraDetalle' value=\"".esc($r['palabra'])."\">
                          <button type='submit' name='verDetalle' class='btn btn-info'>Ver</button>
                        </form>
                      </td>
                    </tr>";
            }
            echo "</table>";
          } else {
            echo "<p>Error en búsqueda: ".esc(mysqli_error($cn))."</p>";
          }
      }
      ?>
    </div>

    <!-- SECCIÓN: DETALLE TÉRMINO -->
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

    <!-- AYUDA -->
    <div class="content-box" id="ayuda">
      <h1>Ayuda</h1>
      <p>En esta sección encontrarás información del CRUD y del glosario.</p>
    </div>

    <footer>© 2025 Sistema Administrativo</footer>
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
    // scroll top
    window.scrollTo({top:0, behavior:'smooth'});
  }

  // manejar clicks en menú (solo los que tengan data-section)
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
    const initial = "<?php echo valid_section($initialSection); ?>";
    // solo llamar si existe
    if(document.getElementById(initial)){
      showSection(initial);
    } else {
      showSection('responsable');
    }
  })();
</script>

</body>
</html>
