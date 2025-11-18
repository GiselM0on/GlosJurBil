<?php
include('conexion.php');

// Verificar si la conexión se estableció correctamente
if (!isset($cn)) {
    die("Error: No se pudo establecer la conexión a la base de datos");
}

// Asumimos que el estudiante está logueado
$id_usuario_estudiante = 1;

// Inicializar variables de mensajes
$mensaje_exito = "";
$mensaje_error = "";

// Procesar formulario para proponer término
if (isset($_POST['proponer_termino'])) {
    $termino_es = trim($_POST['termino_es']);
    $termino_en = trim($_POST['termino_en']);
    $definicion = trim($_POST['definicion']);
    $ejemplo = trim($_POST['ejemplo']);
    $referencia = trim($_POST['referencia']);
    $pronunciacion = trim($_POST['pronunciacion']);
    
    // Validar campos requeridos
    if (empty($termino_es) || empty($termino_en) || empty($definicion) || empty($ejemplo)) {
        $mensaje_error = "Por favor complete todos los campos requeridos";
    } else {
        // Iniciar transacción
        mysqli_begin_transaction($cn);
        
        try {
            // 1. Insertar en bi_termino
            $sql_termino = "INSERT INTO termino (ejemplo_aplicativo, deferencia_bibliogr, estado, fecha_creacion, fecha_modificacion, id_Usuario) 
                           VALUES (?, ?, 'pendiente', NOW(), NOW(), ?)";
            $stmt_termino = mysqli_prepare($cn, $sql_termino);
            if (!$stmt_termino) {
                throw new Exception("Error preparando consulta de término: " . mysqli_error($cn));
            }
            mysqli_stmt_bind_param($stmt_termino, "ssi", $ejemplo, $referencia, $id_usuario_estudiante);
            mysqli_stmt_execute($stmt_termino);
            $id_termino = mysqli_insert_id($cn);
            
            // 2. Insertar traducción en español (idioma 1)
            $sql_traduccion_es = "INSERT INTO traduccion (palabra, pronunciacion, definicion, id_Termino, id_Idoma) 
                                 VALUES (?, ?, ?, ?, 1)";
            $stmt_traduccion_es = mysqli_prepare($cn, $sql_traduccion_es);
            if (!$stmt_traduccion_es) {
                throw new Exception("Error preparando consulta de traducción español: " . mysqli_error($cn));
            }
            mysqli_stmt_bind_param($stmt_traduccion_es, "sssi", $termino_es, $pronunciacion, $definicion, $id_termino);
            mysqli_stmt_execute($stmt_traduccion_es);
            
            // 3. Insertar traducción en inglés (idioma 2)
            $sql_traduccion_en = "INSERT INTO traduccion (palabra, pronunciacion, definicion, id_Termino, id_Idoma) 
                                 VALUES (?, '', ?, ?, 2)";
            $stmt_traduccion_en = mysqli_prepare($cn, $sql_traduccion_en);
            if (!$stmt_traduccion_en) {
                throw new Exception("Error preparando consulta de traducción inglés: " . mysqli_error($cn));
            }
            mysqli_stmt_bind_param($stmt_traduccion_en, "ssi", $termino_en, $definicion, $id_termino);
            mysqli_stmt_execute($stmt_traduccion_en);
            
            // 4. Insertar validación inicial
            $sql_validacion = "INSERT INTO valdacion (comenario, estado_valdacion, fecha_valdacion, id_Termino, id_Usuario) 
                              VALUES ('', 'pendiente', NOW(), ?, ?)";
            $stmt_validacion = mysqli_prepare($cn, $sql_validacion);
            if (!$stmt_validacion) {
                throw new Exception("Error preparando consulta de validación: " . mysqli_error($cn));
            }
            mysqli_stmt_bind_param($stmt_validacion, "ii", $id_termino, $id_usuario_estudiante);
            mysqli_stmt_execute($stmt_validacion);
            
            mysqli_commit($cn);
            $mensaje_exito = "Término propuesto correctamente. Será revisado por los docentes.";
        } catch (Exception $e) {
            mysqli_rollback($cn);
            $mensaje_error = "Error al proponer el término: " . $e->getMessage();
        }
    }
}

// Obtener las propuestas del estudiante
$propuestas = [];
$error_propuestas = "";
try {
    $sql_propuestas = "SELECT t.id_Termino, 
                              tes.palabra as termino_es, 
                              ten.palabra as termino_en,
                              t.fecha_crescón as fecha_propuesta,
                              v.estado_valdacion as estado,
                              v.comenario as comentario
                       FROM termino t
                       LEFT JOIN traduccion tes ON t.id_Termino = tes.id_Termino AND tes.id_Idoma = 1
                       LEFT JOIN traduccion ten ON t.id_Termino = ten.id_Termino AND ten.id_Idoma = 2
                       LEFT JOIN valdacion v ON t.id_Termino = v.id_Termino
                       WHERE t.id_Usuario = ?
                       ORDER BY t.fecha_crescón DESC";
    $stmt_propuestas = mysqli_prepare($cn, $sql_propuestas);
    if ($stmt_propuestas) {
        mysqli_stmt_bind_param($stmt_propuestas, "i", $id_usuario_estudiante);
        mysqli_stmt_execute($stmt_propuestas);
        $result_propuestas = mysqli_stmt_get_result($stmt_propuestas);
        $propuestas = mysqli_fetch_all($result_propuestas, MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    $error_propuestas = "Error al cargar las propuestas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel del Estudiante - Glosario Jurídico Bilingüe</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      background-color: #b6b9cd;
      color: #333;
      min-height: 100vh;
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
    }

    .sidebar {
      width: 260px;
      background: linear-gradient(180deg, #1e3a8a 0%, #15255e 100%);
      color: white;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1000;
      box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      padding: 20px 0;
    }

    .logo {
      padding: 0 20px 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: 20px;
    }

    .logo h1 {
      font-size: 24px;
      font-weight: 600;
      text-align: center;
      margin: 0;
    }

    .menu-section {
      margin-bottom: 25px;
    }

    .section-title {
      padding: 0 20px 10px;
      font-size: 14px;
      color: #8b9af0;
      text-transform: uppercase;
      letter-spacing: 1px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .menu-items {
      list-style: none;
      padding: 10px 0;
      margin: 0;
    }

    .menu-item {
      padding: 12px 20px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      text-decoration: none;
      color: #dcdbeb;
      border-left: 3px solid transparent;
    }

    .menu-item:hover {
      background-color: rgba(255, 255, 255, 0.1);
      border-left: 3px solid #4d579d;
    }

    .menu-item.active {
      background-color: rgba(77, 87, 157, 0.3);
      border-left: 3px solid #4d579d;
    }

    .menu-item i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }

    .nav-section {
      margin-top: 30px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding-top: 20px;
    }

    .sidebar-nav {
      flex: 1;
      overflow-y: auto;
      padding: 10px 0;
    }

    .sidebar-footer {
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding: 15px 0;
      margin-top: auto;
    }

    #content {
      margin-left: 260px;
      padding: 20px;
      min-height: 100vh;
      transition: all 0.3s;
    }

    .content-section {
      display: none;
    }

    .content-section.active {
      display: block;
    }

    .admin-card {
      border-radius: 1rem;
      background-color: #dcdbeb;
      transition: transform 0.3s, box-shadow 0.3s;
      cursor: pointer;
      min-height: 180px;
    }

    .admin-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2);
    }

    .icon-large {
      font-size: 3rem;
      color: #1e3a8a;
      opacity: 0.8;
    }

    .table-responsive {
      border-radius: 0.75rem;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      background-color: white;
    }

    .table thead {
      background-color: #4d579d;
      color: white;
    }

    .table th, .table td {
      vertical-align: middle;
    }

    .status-pendiente { 
      color: #ffc107; 
      font-weight: bold; 
    }
    
    .status-aprobado { 
      color: #28a745; 
      font-weight: bold; 
    }
    
    .status-rechazado { 
      color: #dc3545; 
      font-weight: bold; 
    }

    .form-container {
      background-color: white;
      border-radius: 0.75rem;
      padding: 25px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      margin-bottom: 25px;
    }

    @media (max-width: 992px) {
      .sidebar {
        width: 70px;
      }
      .sidebar .menu-item span,
      .sidebar-section {
        display: none;
      }
      .sidebar .menu-item {
        text-align: center;
        padding: 15px 5px;
        justify-content: center;
      }
      .logo span {
        display: none;
      }
      #content {
        margin-left: 70px;
      }
    }
  </style>
</head>
<body>

  <!-- MENÚ LATERAL -->
  <div class="sidebar">
    <div class="logo">
      <h1><i class="bi bi-person-circle"></i> <span> Panel Estudiante </span></h1>
    </div>
    
    <nav class="sidebar-nav">
      <!-- Términos -->
      <div class="menu-section">
        <div class="section-title">Términos</div>
        <ul class="menu-items">
          <a href="#" class="menu-item active" data-section="proponer-termino">
            <i class="bi bi-plus-circle"></i> <span>Proponer Término</span>
          </a>
          <a href="#" class="menu-item" data-section="mis-propuestas">
            <i class="bi bi-list-check"></i> <span>Mis Propuestas</span>
          </a>
        </ul>
      </div>
      
      <!-- Navegación -->
      <div class="menu-section nav-section">
        <div class="section-title">Navegación</div>
        <ul class="menu-items">
           <a href="index.php" class="menu-item">
                        <i class="bi bi-house-door-fill me-2"></i> <span>Página Principal</span>
                    </a>
        </ul>
      </div>
    </nav>

    <div class="sidebar-footer">
      <a href="#" class="menu-item">
        <i class="bi bi-box-arrow-right"></i> <span>Cerrar Sesión</span>
      </a>
    </div>
  </div>

  <!-- CONTENIDO PRINCIPAL -->
  <div id="content">
    <!-- Sección: Proponer Término -->
    <div class="content-section active" id="proponer-termino">
      <h2 class="mb-4">Proponer Nuevo Término Jurídico</h2>
      
      <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php echo $mensaje_exito; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo $mensaje_error; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      
      <div class="form-container">
        <form method="POST" id="form-proponer-termino">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="termino_es" class="form-label">Término en Español *</label>
              <input type="text" class="form-control" id="termino_es" name="termino_es" required>
            </div>
            <div class="col-md-6">
              <label for="termino_en" class="form-label">Término en Inglés *</label>
              <input type="text" class="form-control" id="termino_en" name="termino_en" required>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="definicion" class="form-label">Definición *</label>
              <textarea class="form-control" id="definicion" name="definicion" rows="3" required></textarea>
            </div>
            <div class="col-md-6">
              <label for="ejemplo" class="form-label">Ejemplo Aplicativo *</label>
              <textarea class="form-control" id="ejemplo" name="ejemplo" rows="3" required></textarea>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="referencia" class="form-label">Referencias Bibliográficas</label>
              <textarea class="form-control" id="referencia" name="referencia" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label for="pronunciacion" class="form-label">Pronunciación (IPA)</label>
              <input type="text" class="form-control" id="pronunciacion" name="pronunciacion">
              <div class="form-text">Opcional: Transcripción fonética internacional</div>
            </div>
          </div>
          
          <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary" name="proponer_termino">
              <i class="bi bi-send"></i> Enviar Propuesta
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Sección: Mis Propuestas -->
    <div class="content-section" id="mis-propuestas">
      <h2 class="mb-4">Estado de Mis Propuestas</h2>
      
      <?php if (!empty($error_propuestas)): ?>
        <div class="alert alert-warning">
          <?php echo $error_propuestas; ?>
        </div>
      <?php endif; ?>
      
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Término (ES)</th>
              <th>Término (EN)</th>
              <th>Fecha Propuesta</th>
              <th>Estado</th>
              <th>Comentarios</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($propuestas)): ?>
              <?php foreach($propuestas as $propuesta): ?>
                <tr>
                  <td><?php echo htmlspecialchars($propuesta['termino_es']); ?></td>
                  <td><?php echo htmlspecialchars($propuesta['termino_en']); ?></td>
                  <td><?php echo date('d/m/Y', strtotime($propuesta['fecha_propuesta'])); ?></td>
                  <td class="status-<?php echo strtolower($propuesta['estado']); ?>">
                    <?php 
                      $estado = $propuesta['estado'];
                      if ($estado == 'pendiente') echo 'Pendiente';
                      elseif ($estado == 'aprobado') echo 'Aprobado';
                      elseif ($estado == 'rechazado') echo 'Rechazado';
                      else echo $estado;
                    ?>
                  </td>
                  <td><?php echo htmlspecialchars($propuesta['comentario'] ?: '-'); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center">No has propuesto ningún término aún.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Script para cambiar entre secciones
    document.addEventListener('DOMContentLoaded', function() {
      const menuItems = document.querySelectorAll('.menu-item[data-section]');
      const contentSections = document.querySelectorAll('.content-section');
      
      menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Quitar clase activa de todos los elementos del menú
          menuItems.forEach(i => i.classList.remove('active'));
          
          // Agregar clase activa al elemento clickeado
          this.classList.add('active');
          
          // Ocultar todas las secciones de contenido
          contentSections.forEach(section => section.classList.remove('active'));
          
          // Mostrar la sección correspondiente
          const sectionId = this.getAttribute('data-section');
          document.getElementById(sectionId).classList.add('active');
        });
      });
    });
  </script>
</body>
</html>

<?php
// Cerrar conexión si está abierta
if (isset($cn)) {
    mysqli_close($cn);
}
?>