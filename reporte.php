<?php
session_start();

// Conexión a la base de datos
$conn = include "conexion.php";

if (!isset($conn) || $conn->connect_error) {
    die("Error: No se pudo establecer conexión con la base de datos");
}


// Variable para controlar si mostrar el reporte o la página normal
$mostrarReporte = isset($_GET['generar_reporte']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $mostrarReporte ? 'Reporte de Términos' : 'Reportes - Glosario Jurídico'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --color-amarillo: #ffa06d;
            --color-azul-oscuro: #006694;
            --color-gris: #636466;
            --color-naranja: #ff9a15;
            --color-azul-claro: #27a5df;
            --color-blanco: #f1f2f2;
        }

        body {
            background-color: var(--color-blanco);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: var(--color-azul-oscuro);
        }

        .navbar {
            background-color: var(--color-azul-oscuro);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--color-azul-claro) 0%, var(--color-azul-oscuro) 100%);
            padding: 3rem 0;
            color: white;
            border-radius: 0 0 1.5rem 1.5rem;
            margin-bottom: 2rem;
        }

        .report-card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-left: 5px solid var(--color-amarillo);
            transition: transform 0.3s ease;
            margin-bottom: 2rem;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .report-icon {
            font-size: 3rem;
            color: var(--color-azul-oscuro);
            margin-bottom: 1rem;
        }

        .btn-report {
            background-color: var(--color-naranja);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-report:hover {
            background-color: #e68914;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 154, 21, 0.3);
            color: white;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--color-azul-claro) 0%, var(--color-azul-oscuro) 100%);
            color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .user-welcome {
            color: white;
            margin-right: 15px;
        }

        .dashboard-btn {
            background-color: var(--color-amarillo);
            border: none;
            transition: all 0.3s;
            color: var(--color-azul-oscuro);
            font-weight: 700;
            padding: 8px 20px;
            border-radius: 0.5rem;
        }

        /* Estilos para el reporte */
        .report-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 1200px;
        }

        .report-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #006694;
        }

        .report-header h1 {
            color: #006694;
            font-size: 2.2em;
            margin-bottom: 10px;
        }

        .stats-container {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .stat-card-report {
            background: linear-gradient(135deg, #27a5df 0%, #006694 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            min-width: 150px;
            flex: 1;
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .info-box {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            border-left: 5px solid #27a5df;
        }

        .section-title {
            color: #ffa06d;
            font-size: 1.5em;
            margin: 30px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #ffa06d;
        }

        .termino {
            background: #f8f9fa;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 8px;
            border-left: 5px solid #ffa06d;
        }

        .termino-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .palabra {
            font-weight: bold;
            color: #006694;
            font-size: 1.2em;
        }

        .badge {
            background: #ffa06d;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .definicion {
            color: #555;
            font-size: 1em;
        }

        .print-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .print-btn {
            background: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            margin: 10px;
        }

        .print-btn:hover {
            background: #218838;
        }

        .footer-logo-img {
            width: 95%;
            max-width: 90px; 
            height: auto;
            margin: 0 auto 10px auto; 
            display: block;
        }

        .back-btn {
            background-color: var(--color-azul-claro);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }

        .back-btn:hover {
            background-color: var(--color-azul-oscuro);
            color: white;
        }

        @media print {
            .print-section, .navbar, .back-btn { display: none; }
            body { background: white; }
            .report-container { box-shadow: none; margin: 0; padding: 15px; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-book-half me-2"></i> GLOSARIO JURÍDICO
            </a>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                   
                
                </ul>
            </div>
        </div>
    </nav>

    <?php if ($mostrarReporte): ?>
        <!-- MOSTRAR REPORTE -->
        <div class="report-container">
            <div class="report-header">
                <h1>REPORTE DE TÉRMINOS DESTACADOS</h1>
                <p class="subtitle">Glosario Jurídico Bilingüe</p>
                
            </div>

            <?php
            // Obtener estadísticas
            $sql_total = "SELECT COUNT(*) as total FROM termino";
            $result_total = $conn->query($sql_total);
            $total_terminos = $result_total ? $result_total->fetch_assoc()['total'] : 0;
            
            $sql_espanol = "SELECT COUNT(*) as total FROM termino t 
                           JOIN idioma i ON i.id_Idioma = i.id_idioma 
                           WHERE i.nombre_idioma = 'español'";
            $result_espanol = $conn->query($sql_espanol);
            $total_espanol = $result_espanol ? $result_espanol->fetch_assoc()['total'] : 0;
            
            $sql_ingles = "SELECT COUNT(*) as total FROM termino t 
                          JOIN idioma i ON i.id_Idioma = i.id_idioma 
                          WHERE i.nombre_idioma = 'inglés'";
            $result_ingles = $conn->query($sql_ingles);
            $total_ingles = $result_ingles ? $result_ingles->fetch_assoc()['total'] : 0;
            ?>

            <div class="stats-container">
                <div class="stat-card-report">
                    <span class="stat-number"><?php echo $total_terminos; ?></span>
                    <span class="stat-label">Total de Términos</span>
                </div>
                <div class="stat-card-report">
                    <span class="stat-number"><?php echo $total_espanol; ?></span>
                    <span class="stat-label">En Español</span>
                </div>
                <div class="stat-card-report">
                    <span class="stat-number"><?php echo $total_ingles; ?></span>
                    <span class="stat-label">En Inglés</span>
                </div>
            </div>

            <div class="info-box">
                <strong>📊 Información del Reporte:</strong><br>
                • Este reporte incluye los términos más recientes del glosario<br>
                • Organizado por idioma para fácil consulta<br>
                • Total de <strong><?php echo $total_terminos; ?> términos</strong> en el sistema<br>
                
            </div>

            <div class="section-title">📝 TÉRMINOS EN ESPAÑOL</div>
            <?php
            $sql_espanol = "SELECT 
                            t.palabra,
                            t.definicion,
                            t.pronunciacion,
                            i.nombre_idioma as idioma
                        FROM termino t
                        JOIN idioma i ON i.id_Idioma = i.id_idioma
                        WHERE i.nombre_idioma = 'español'
                        ORDER BY t.fecha_creacion DESC
                        LIMIT 20";
            
            $result_espanol = $conn->query($sql_espanol);
            
            if ($result_espanol && $result_espanol->num_rows > 0) {
                while($row = $result_espanol->fetch_assoc()) {
                    echo '
                    <div class="termino">
                        <div class="termino-header">
                            <div class="palabra">' . htmlspecialchars($row['palabra']) . '</div>
                            <span class="badge">ESPAÑOL</span>
                        </div>
                        <div class="definicion">' . htmlspecialchars($row['definicion']) . '</div>
                    </div>
                    ';
                }
            } else {
                echo '<p style="text-align: center; color: #666; padding: 20px;">No hay términos en español</p>';
            }
            ?>

            <div class="section-title">📘 TÉRMINOS EN INGLÉS</div>
            <?php
            $sql_ingles = "SELECT 
                            t.palabra,
                            t.definicion,
                            t.pronunciacion,
                            i.nombre_idioma as idioma
                        FROM termino t
                        JOIN idioma i ON i.id_Idioma = i.id_idioma
                        WHERE i.nombre_idioma = 'inglés'
                        ORDER BY t.fecha_creacion DESC
                        LIMIT 20";
            
            $result_ingles = $conn->query($sql_ingles);
            
            if ($result_ingles && $result_ingles->num_rows > 0) {
                while($row = $result_ingles->fetch_assoc()) {
                    echo '
                    <div class="termino">
                        <div class="termino-header">
                            <div class="palabra">' . htmlspecialchars($row['palabra']) . '</div>
                            <span class="badge">INGLÉS</span>
                        </div>
                        <div class="definicion">' . htmlspecialchars($row['definicion']) . '</div>
                    </div>
                    ';
                }
            } else {
                echo '<p style="text-align: center; color: #666; padding: 20px;">No hay términos en inglés</p>';
            }
            ?>

            <div class="print-section">
                <button class="print-btn" onclick="window.print()">
                    🖨️ Imprimir o Guardar como PDF
                </button>
                <a href="reporte.php" class="back-btn">
                    ← Volver a Reportes
                </a>
            </div>

            <footer class="mt-5 py-4 text-center border-top" style="background-color: #f1f2f2; color: #636466;">
                <div class="container d-flex flex-column flex-md-row justify-content-center align-items-center">
                    <div class="mb-3 mb-md-0 me-md-3"> 
                        <img src="img/LogoFCDING.png" 
                             alt="Logo Facultad de Ingeniería" 
                             class="footer-logo-img">
                    </div>
                    <div class="text-center text-md-start">
                        <p class="mb-1">
                           &copy; Sistema Desarrollado por estudiantes de la 
                            <span style="color: #006694; font-weight: bold;">UPED</span> 2025
                        </p>
                    </div>
                </div>
            </footer>
        </div>

    <?php else: ?>
        <!-- MOSTRAR PÁGINA NORMAL DE REPORTES -->
        <div class="container-fluid hero-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 text-center">
                        <h1 class="display-5 fw-light mb-3">Reportes del Glosario</h1>
                        <p class="lead">Genera reportes en PDF de los términos más destacados</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <!-- Estadísticas -->
            <div class="row mb-5">
                <div class="col-md-4 mb-3">
                    <div class="stat-card">
                        <?php
                        $sql_total = "SELECT COUNT(*) as total FROM termino";
                        $result_total = $conn->query($sql_total);
                        $total_terminos = $result_total ? $result_total->fetch_assoc()['total'] : 0;
                        ?>
                        <div class="stat-number"><?php echo $total_terminos; ?></div>
                        <div>Total de Términos</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stat-card">
                        <?php
                        $sql_espanol = "SELECT COUNT(*) as total FROM termino t 
                                       JOIN idioma i ON i.id_Idioma = i.id_idioma 
                                       WHERE i.nombre_idioma = 'español'";
                        $result_espanol = $conn->query($sql_espanol);
                        $total_espanol = $result_espanol ? $result_espanol->fetch_assoc()['total'] : 0;
                        ?>
                        <div class="stat-number"><?php echo $total_espanol; ?></div>
                        <div>Términos en Español</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stat-card">
                        <?php
                        $sql_ingles = "SELECT COUNT(*) as total FROM termino t 
                                      JOIN idioma i ON i.id_Idioma = i.id_idioma 
                                      WHERE i.nombre_idioma = 'inglés'";
                        $result_ingles = $conn->query($sql_ingles);
                        $total_ingles = $result_ingles ? $result_ingles->fetch_assoc()['total'] : 0;
                        ?>
                        <div class="stat-number"><?php echo $total_ingles; ?></div>
                        <div>Términos en Inglés</div>
                    </div>
                </div>
            </div>

            <!-- Reportes Disponibles -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card p-4 h-100">
                        <div class="text-center">
                            <i class="bi bi-file-earmark-pdf report-icon"></i>
                            <h3 class="text-primary">Reporte de Términos Destacados</h3>
                            <p class="text-muted mb-4">
                                Genera un reporte completo con los términos más recientes en español e inglés. 
                                Incluye definiciones completas y está listo para imprimir o compartir.
                            </p>
                            <a href="?generar_reporte=1" class="btn btn-report">
                                <i class="bi bi-download me-2"></i> Generar Reporte
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="report-card p-4 h-100">
                        <div class="text-center">
                            <i class="bi bi-graph-up report-icon"></i>
                            <h3 class="text-primary">Estadísticas Completas</h3>
                            <p class="text-muted mb-4">
                                Próximamente: Reporte detallado con estadísticas de uso, 
                                términos más buscados y análisis de tendencias del glosario.
                            </p>
                            <button class="btn btn-secondary" disabled>
                                <i class="bi bi-clock me-2"></i> Próximamente
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h4><i class="bi bi-info-circle me-2"></i> Información Importante</h4>
                        <ul class="mb-0">
                            <li>Los reportes se generan en tiempo real con la información más actualizada</li>
                            <li>Puedes generar reportes ilimitados sin restricciones</li>
                            <li>Los reportes incluyen fecha de generación automática</li>
                            <li>Compatible con todos los dispositivos y navegadores</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <footer class="mt-5 py-4 text-center border-top" style="background-color: #f1f2f2; color: #636466;">
            <div class="container">
                <p class="mb-0">
                    &copy; Sistema Desarrollado por estudiantes de la 
                    <span style="color: #006694; font-weight: bold;">UPED</span> 2025
                </p>
            </div>
        </footer>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
<?php $conn->close(); ?>