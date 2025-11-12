<?php
include("conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel del Estudiante - Sistema Universitario</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f5f7fb;
      color: #333;
      display: flex;
      height: 100vh;
      overflow: hidden;
    }

    /* --- PANEL LATERAL --- */
    .sidebar {
      width: 260px;
      background-color: #0b2a64;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 20px 0;
    }

    .sidebar h2 {
      text-align: center;
      font-size: 22px;
      margin-bottom: 25px;
    }

    .menu-section {
      padding-left: 25px;
    }

    .menu-section h3 {
      font-size: 13px;
      font-weight: normal;
      color: #9cb3d1;
      margin-bottom: 10px;
      text-transform: uppercase;
    }

    .menu-item {
      display: block;
      color: white;
      padding: 8px 10px;
      margin-bottom: 6px;
      border-radius: 6px;
      text-decoration: none;
      transition: background 0.2s;
      cursor: pointer;
    }

    .menu-item:hover, .menu-item.active {
      background-color: #1a4fa3;
    }

    .logout {
      text-align: center;
      padding: 15px;
      border-top: 1px solid rgba(255,255,255,0.2);
    }

    .logout a {
      color: #ff7777;
      text-decoration: none;
      font-weight: bold;
    }

    /* --- PANEL PRINCIPAL --- */
    .main {
      flex-grow: 1;
      background-color: #e9eef7;
      padding: 30px;
      overflow-y: auto;
    }

    .header {
      background: #dfe6f5;
      padding: 12px 25px;
      border-radius: 10px;
      font-size: 15px;
      margin-bottom: 25px;
      color: #0b2a64;
    }

    .content-box {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      margin-bottom: 25px;
      display: none; /* Ocultamos todas por defecto */
    }

    .content-box.active {
      display: block; /* Solo se mostrará la activa */
    }

    h1 {
      color: #0b2a64;
      font-size: 24px;
      margin-bottom: 10px;
    }

    form input, textarea, select {
      margin: 8px;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
      width: 200px;
    }

    textarea {
      width: 95%;
      height: 70px;
    }

    form button {
      padding: 8px 16px;
      margin: 8px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }

    .add { background: #2e8b57; color: white; }
    .show { background: #007bff; color: white; }
    .update { background: #ffc107; color: black; }
    .delete { background: #dc3545; color: white; }
    .search { background: #00bcd4; color: white; }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      border: 1px solid #ddd;
      text-align: center;
      padding: 8px;
    }

    th {
      background-color: #0b2a64;
      color: white;
    }

    .status-aprobado { color: #28a745; font-weight: bold; }
    .status-rechazado { color: #dc3545; font-weight: bold; }
    .status-pendiente { color: #ffc107; font-weight: bold; }

    footer {
      text-align: center;
      color: #777;
      margin-top: 20px;
      font-size: 13px;
    }
  </style>
</head>
<body>
  <!-- PANEL LATERAL -->
  <div class="sidebar">
    <div>
      <h2> Panel Estudiante</h2>

      <div class="menu-section">
        <h3>Perfil</h3>
        <a class="menu-item active" data-section="datos">Mis Datos</a>
        <a class="menu-item" data-section="pendientes">Pendientes</a>
      </div>

      <div class="menu-section">
        <h3>Traducciones</h3>
        <a class="menu-item" data-section="proponer">Proponer Términos</a>
        <a class="menu-item" data-section="comentarios">Comentarios del Docente</a>
      </div>

      <div class="menu-section">
        <h3>Navegación</h3>
        <a class="menu-item" data-section="principal">Página Principal</a>
      </div>
    </div>

    <div class="logout">
      <a href="#">Cerrar Sesión</a>
    </div>
  </div>

  <!-- CONTENIDO PRINCIPAL -->
  <div class="main">
    <div class="header">
      Bienvenido estudiante: aquí puedes gestionar tus datos, proponer términos y revisar el estado de tus solicitudes.
    </div>

    <!-- SECCIONES -->
    <div class="content-box active" id="datos">
      <h1>Gestión de Estudiantes</h1>
      <form method="POST">
        <input type="text" name="codigo" placeholder="Código Universitario">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido" placeholder="Apellido" required>
        <input type="number" name="edad" placeholder="Edad" required>
        <input type="text" name="carrera" placeholder="Carrera" required>
        <input type="text" name="facultad" placeholder="Facultad" required>
        <input type="number" name="anio" placeholder="Año académico" required><br>

        <button type="submit" name="agregar" class="add">Agregar</button>
        <button type="submit" name="mostrar" class="show">Mostrar</button>
        <button type="submit" name="modificar" class="update">Modificar</button>
        <button type="submit" name="eliminar" class="delete">Eliminar</button>
      </form>
    </div>

    <div class="content-box" id="pendientes">
      <h1>Estado de Términos</h1>
      <table>
        <tr>
          <th>Término (ES)</th>
          <th>Término (EN)</th>
          <th>Fecha Propuesta</th>
          <th>Estado</th>
        </tr>
        <tr>
          <td>Habeas Corpus</td>
          <td>Habeas Corpus</td>
          <td>04/11/2024</td>
          <td class="status-pendiente">Pendiente</td>
        </tr>
        <tr>
          <td>Amparo</td>
          <td>Protection Writ</td>
          <td>03/11/2024</td>
          <td class="status-aprobado">Aprobado</td>
        </tr>
        <tr>
          <td>Injuria</td>
          <td>Defamation</td>
          <td>02/11/2024</td>
          <td class="status-rechazado">Rechazado</td>
        </tr>
      </table>
    </div>

    <div class="content-box" id="proponer">
      <h1>Proponer Nuevo Término</h1>
      <form method="POST">
        <input type="text" name="termino_es" placeholder="Término en Español" required>
        <input type="text" name="termino_en" placeholder="Término en Inglés" required><br>
        <textarea name="definicion" placeholder="Escribe aquí la definición del término..."></textarea><br>
        <button type="submit" name="proponer" class="add">Enviar Propuesta</button>
      </form>
    </div>

    <div class="content-box" id="comentarios">
      <h1>Comentarios del Docente</h1>
      <table>
        <tr>
          <th>Término</th>
          <th>Comentario</th>
          <th>Fecha</th>
        </tr>
        <tr>
          <td>Habeas Corpus</td>
          <td>Buena definición, solo ajusta la traducción en contexto legal.</td>
          <td>05/11/2024</td>
        </tr>
        <tr>
          <td>Injuria</td>
          <td>Debe diferenciarse de difamación; revisa las fuentes.</td>
          <td>04/11/2024</td>
        </tr>
      </table>
    </div>

    <div class="content-box" id="principal">
      <h1>Página Principal</h1>
      <p>Bienvenido al sistema universitario. Aquí podrás acceder a tus módulos académicos, gestión de términos, y retroalimentación de tus docentes.</p>
    </div>

    <footer>© 2025-2026 Sistema Universitario | Panel del Estudiante</footer>
  </div>

  <script>
    // Script para mostrar una sola sección
    const menuItems = document.querySelectorAll('.menu-item');
    const sections = document.querySelectorAll('.content-box');

    menuItems.forEach(item => {
      item.addEventListener('click', () => {
        // Quitar clase activa de los botones
        menuItems.forEach(i => i.classList.remove('active'));
        item.classList.add('active');

        // Ocultar todas las secciones
        sections.forEach(sec => sec.classList.remove('active'));

        // Mostrar solo la seleccionada
        const sectionId = item.getAttribute('data-section');
        document.getElementById(sectionId).classList.add('active');
      });
    });
  </script>
</body>
</html>
