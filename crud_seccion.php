<?php
include(__DIR__ . "/conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Estudiante</title>

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

/* Reset */
*{box-sizing:border-box;font-family:'Segoe UI', Arial, sans-serif}

/* Layout */
body{
  margin:0;
  padding:0;
  background:var(--fondo-general);
  color:var(--texto);
  display:flex;
  min-height:100vh;
}


/* actualizacion */
/* Sidebar */
.sidebar{
  width:260px;
  background:var(--azul-oscuro);
  color:var(--blanco);
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  padding:22px 14px;
  gap:12px;
}
.sidebar h2{ text-align:center; font-size:20px; margin:6px 0 12px 0; }
.menu-section{ padding-left:8px; margin-bottom:10px; }
.menu-section h3{ font-size:12px; color:#9cb3d1; margin:0 0 8px 0; text-transform:uppercase; }

.menu-item{
  display:block;
  color:white;
  padding:10px 12px;
  margin-bottom:6px;
  border-radius:8px;
  text-decoration:none;
  transition:background .18s, transform .15s;
  cursor:pointer;
}
.menu-item:hover, .menu-item.active{
  background:var(--azul-medio);
  transform:translateX(4px);
}

/* Main */
.main{
  flex:1;
  padding:28px;
}
.header{
  background:var(--azul-claro);
  padding:14px 22px;
  border-radius:10px;
  color:var(--azul-oscuro);
  margin-bottom:22px;
  box-shadow: 0 2px 8px rgba(11,42,100,0.06);
}

/* Boxes */
.content-box{
  background:white;
  border-radius:12px;
  padding:22px;
  margin-bottom:18px;
  box-shadow:0 6px 18px rgba(0,0,0,0.08);
  display:none;
}
.content-box.active{ display:block; }

.content-box h1{
  color:var(--azul-oscuro);
  margin-bottom:12px;
}

/* Form */
.form-row{
  display:flex;
  gap:12px;
  flex-wrap:wrap;
  margin-bottom:12px;
}
.form-row label{ font-weight:600; color:var(--azul-oscuro); min-width:160px; }

.input, input[type=text], input[type=number], textarea {
  padding:10px 12px;
  border-radius:8px;
  border:1px solid #d7dfe9;
  background:#fff;
  min-width:220px;
}

/* Buttons */
.btn-row{ display:flex; gap:12px; flex-wrap:wrap; margin-top:10px; }
.btn{
  padding:10px 18px;
  border-radius:8px;
  border:none;
  cursor:pointer;
  font-weight:700;
}
.btn-primary{ background:#1A4FA3; color:white; }
.btn-success{ background:#2e8b57; color:white; }
.btn-warning{ background:#ffc107; color:#222; }
.btn-danger{ background:#dc3545; color:white; }

/* Table */
.data-table{
  width:100%;
  border-collapse:collapse;
  background:white;
  border-radius:8px;
  overflow:hidden;
}
.data-table th{
  background:var(--azul-oscuro);
  color:white;
  padding:12px;
  text-align:left;
}
.data-table td{
  padding:12px;
  border-bottom:1px solid #eee;
}

/* Footer */
footer{
  text-align:center;
  color:#666;
  padding:18px 0;
  margin-top:22px;
}
</style>
</head>

<body>

<!-- ====== BARRA LATERAL ====== -->
<div class="sidebar">
  <div>
    <h2>Panel Estudiante</h2>

    <div class="menu-section">
      <h3>Perfil</h3>
      <a class="menu-item active" data-section="datos">Mis Datos</a>
      <a class="menu-item" data-section="pendientes">Pendientes</a>
    </div>

    <div class="menu-section">
      <h3>Traducciones</h3>
      <a class="menu-item" data-section="proponer">Proponer Términos</a>
      <a class="menu-item" data-section="comentarios">Comentarios</a>
    </div>

    <div class="menu-section">
      <h3>Navegación</h3>
      <a class="menu-item" data-section="principal">Página Principal</a>
    </div>

    <div class="menu-section">
      <h3>Consultas</h3>

      <!-- LINKS DIRECTOS A CRUD RESPONSABLE -->
      <a class="menu-item" href="crud_responsable.php?section=terminos">
        📘 Ver Términos
      </a>

      <a class="menu-item" href="crud_responsable.php?section=listaResponsable">
        👤 Ver Responsables
      </a>

    </div>

  </div>

  <div class="logout">
    <a href="#">Cerrar Sesión</a>
  </div>
</div>

<!-- ====== CONTENIDO PRINCIPAL ====== -->
<div class="main">
  <div class="header">Bienvenido estudiante: aquí puedes gestionar tus datos y revisar tus solicitudes.</div>

  <!-- DATOS -->
  <div class="content-box active" id="datos">
    <h1>Gestión de Estudiantes</h1>

    <form method="POST">

      <div class="form-row">
        <label>Código:</label>
        <input class="input" type="text" name="codigo">
      </div>

      <div class="form-row">
        <label>Nombre:</label>
        <input class="input" type="text" name="nombre" required>
      </div>

      <div class="form-row">
        <label>Apellido:</label>
        <input class="input" type="text" name="apellido" required>
      </div>

      <div class="form-row">
        <label>Edad:</label>
        <input class="input" type="number" name="edad" required>
      </div>

      <div class="form-row">
        <label>Carrera:</label>
        <input class="input" type="text" name="carrera" required>
      </div>

      <div class="form-row">
        <label>Facultad:</label>
        <input class="input" type="text" name="facultad" required>
      </div>

      <div class="form-row">
        <label>Año Académico:</label>
        <input class="input" type="number" name="anio" required>
      </div>

      <div class="btn-row">
        <button type="submit" name="agregar" class="btn btn-success">Agregar</button>
        <button type="submit" name="mostrar" class="btn btn-primary">Mostrar</button>
        <button type="submit" name="modificar" class="btn btn-warning">Modificar</button>
        <button type="submit" name="eliminar" class="btn btn-danger">Eliminar</button>
      </div>

    </form>
  </div>

  <!-- PENDIENTES -->
  <div class="content-box" id="pendientes">
    <h1>Estado de Términos</h1>
    <p>Aquí aparecerán los términos pendientes de revisión.</p>
  </div>

  <!-- PROPONER -->
  <div class="content-box" id="proponer">
    <h1>Proponer Nuevo Término</h1>
    <form method="POST">

      <div class="form-row">
        <label>Término Español:</label>
        <input class="input" type="text" name="termino_es" required>
      </div>

      <div class="form-row">
        <label>Término Inglés:</label>
        <input class="input" type="text" name="termino_en" required>
      </div>

      <div class="form-row">
        <label>Definición:</label>
        <textarea class="input" name="definicion"></textarea>
      </div>

      <button type="submit" name="proponer" class="btn btn-success">Enviar Propuesta</button>

    </form>
  </div>

  <!-- COMENTARIOS -->
  <div class="content-box" id="comentarios">
    <h1>Comentarios del Docente</h1>
    <p>Aquí aparecerán los comentarios sobre tus términos propuestos.</p>
  </div>

  <!-- PRINCIPAL -->
  <div class="content-box" id="principal">
    <h1>Página Principal</h1>
    <p>Bienvenido al sistema universitario.</p>
  </div>

  <footer>© 2025-2026 Sistema Universitario</footer>
</div>


<!-- ====== JAVASCRIPT ====== -->
<script>
const menuItems = document.querySelectorAll('.menu-item[data-section]');
const sections = document.querySelectorAll('.content-box');

menuItems.forEach(item => {
  item.addEventListener('click', () => {

    if (!item.dataset.section) return;

    menuItems.forEach(i => i.classList.remove('active'));
    item.classList.add('active');

    sections.forEach(sec => sec.classList.remove('active'));

    document.getElementById(item.dataset.section).classList.add('active');
  });
});
</script>

</body>
</html>
