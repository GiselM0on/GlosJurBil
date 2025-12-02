<?php 
session_start();

// Conexión db
$conn = include "conexion.php";       

// Verificar si la conexión se estableció correctamente
if (!isset($conn) || $conn->connect_error) {
    die("Error: No se pudo establecer conexión con la base de datos");
}


// Verificar si hay un usuario logueado
$usuarioLogueado = false;
$rolUsuario = '';
$nombreUsuario = '';

if (isset($_SESSION['usuario']) && isset($_SESSION['rol'])) {
    $usuarioLogueado = true;
    $rolUsuario = $_SESSION['rol'];
    $nombreUsuario = $_SESSION['usuario'];
}
// Configurar charset para la conexión
if (isset($cn) && is_object($cn)) {
    mysqli_set_charset($cn, "utf8mb4");
}

?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glosario Jurídico Bilingüe - Consulta</title>
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
            padding: 5rem 0;
            color: white;
            border-radius: 0 0 1.5rem 1.5rem;
            margin-bottom: 2rem;
        }

        .search-container {
            background-color: white;
            border-radius: 1rem;
            padding: 10px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            margin-top: -3rem;
            border: 1px solid var(--color-gris);
        }

        .search-input {
            border: none;
            box-shadow: none;
            height: 50px;
            font-size: 1.1rem;
            color: var(--color-azul-oscuro);
        }

        .search-input:focus {
            border-color: var(--color-azul-claro);
            box-shadow: 0 0 0 0.2rem rgba(39, 165, 223, 0.25);
        }

        .search-button {
            background-color: var(--color-amarillo);
            border: none;
            height: 50px;
            padding: 0 25px;
            transition: all 0.3s;
            color: var(--color-azul-oscuro);
            font-weight: 700;
        }

        .search-button:hover {
            background-color: #ff8a3d;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 160, 109, 0.3);
            color: var(--color-azul-oscuro);
        }

        /* Estilos adicionales para el formato de lista */
        .terms-container {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .term-item {
            border-left: 4px solid var(--color-amarillo) !important;
            transition: all 0.2s ease;
            background-color: #f8f9fa;
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 0.25rem;
        }

        .term-item:hover {
            background-color: rgba(255, 160, 109, 0.1) !important;
            transform: translateX(5px);
        }

        .term-title {
            font-size: 1.1rem;
            color: var(--color-azul-oscuro);
            font-weight: bold;
        }

        .lang-tag {
            font-size: 0.75rem;
            padding: 0.3em 0.6em;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .lang-tag.es {
            background-color: var(--color-amarillo);
            color: var(--color-azul-oscuro);
        }

        .lang-tag.en {
            background-color: var(--color-naranja);
            color: white;
        }

        .pronunciation {
            font-style: italic;
            color: var(--color-gris);
            font-size: 0.9rem;
            margin-left: 0 !important;
        }

        .highlight {
            background-color: rgba(255, 160, 109, 0.3);
            padding: 2px 4px;
            border-radius: 3px;
            color: var(--color-azul-oscuro);
            font-weight: 500;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            color: var(--color-gris);
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
        .report-btn {
    background-color: var(--color-naranja);
    border: none;
    transition: all 0.3s;
    color: white;
    font-weight: 700;
    padding: 8px 20px;
    border-radius: 0.5rem;
}

        .report-btn:hover {
            background-color: #e68914;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 154, 21, 0.3);
            color: white;
        }
        .dashboard-btn:hover {
            background-color: #ff8a3d;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 160, 109, 0.3);
            color: var(--color-azul-oscuro);
        }

        .footer-logo-img {
            width: 95%;
            max-width: 90px; 
            height: auto;
            margin: 0 auto 10px auto; 
            display: block;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .search-info {
            color: var(--color-gris);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-book-half me-2"></i> GLOSARIO JURÍDICO
            </a>
               
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <?php if ($usuarioLogueado): ?>
                        <li class="nav-item">
                            <span class="user-welcome d-none d-lg-block">
                                <i class="bi bi-person-circle"></i> 
                                <?php echo htmlspecialchars($nombreUsuario); ?> 
                                (<?php echo htmlspecialchars($rolUsuario); ?>)
                            </span>
                        </li>
                        <li class="nav-item">
                            <?php if ($rolUsuario === 'admin'): ?>
                                <a href="pantallaAdmin.php" class="btn dashboard-btn rounded-pill ms-lg-3">
                                    <i class="bi bi-speedometer2"></i> Panel Admin
                                </a>
                            <?php elseif ($rolUsuario === 'docente'): ?>
                                <a href="docente_revision.php" class="btn dashboard-btn rounded-pill ms-lg-3">
                                    <i class="bi bi-speedometer2"></i> Panel Docente
                                </a>
                            <?php endif; ?>
                        </li>
                     
                        <li class="nav-item">
                            <a href="logout.php" class="btn btn-outline-light rounded-pill ms-lg-2">
                                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="login.php" class="btn btn-outline-light rounded-pill ms-lg-3">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- En la navbar, después del botón del dashboard -->
                    <li class="nav-item">
                        <a href="listar_terminos.php" class="btn report-btn rounded-pill ms-lg-2">
                            <i class="bi bi-graph-up"></i> Reportes
                        </a>
                    </li>
        </div>
    </nav>
    

    <div class="container-fluid hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <h1 class="display-4 fw-light mb-3">Glosario Bilingüe de Conceptos Jurídicos</h1>
                    <p class="lead">Busca términos en español o inglés.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="search-container">
                    <div class="input-group">
                        <input type="text" class="form-control search-input" id="searchTerm" 
                               placeholder="Buscar término ..." 
                               aria-label="Buscar término">
                        <button class="btn search-button" type="button" onclick="searchTerms()">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="container mt-5 pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary mb-0" id="resultsTitle">Todos los Términos</h2>
            <span class="search-info" id="searchInfo"></span>
        </div>
        
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Buscando...</span>
            </div>
            <p class="mt-2">Buscando términos...</p>
        </div>
        
        <div class="row" id="termsList">
            <div class="col-12">
                <!-- Los términos se cargarán aquí en formato lista -->
                <?php 
                
                $sql = "SELECT DISTINCT
                        t.palabra,
                        t.definicion,
                        t.pronunciacion
                    FROM termino t
                    ORDER BY t.palabra";

                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    echo '<div class="terms-container">';
                    
                    while($row = $result->fetch_assoc()) {
                        echo '
                        <div class="term-item">
                            <div class="d-flex align-items-start mb-2">
                                <strong class="term-title me-2">' . 
                                    htmlspecialchars($row['palabra']) . 
                                '</strong>';
                        
                        echo '</div>'; // Cierre del flex container
                        
                        // Mostrar pronunciación si existe
                        if (!empty($row['pronunciacion'])) {
                            echo '<p class="pronunciation mb-1"><em>' . 
                                 htmlspecialchars($row['pronunciacion']) . 
                                 '</em></p>';
                        }
                        
                        // Mostrar definición
                        if (!empty($row['definicion'])) {
                            echo '<p class="mb-0"><strong>Definición:</strong> ' . 
                                 htmlspecialchars($row['definicion']) . 
                                 '</p>';
                        }
                        
                        echo '</div>'; // Cierre del term-item
                    }
                    
                    echo '</div>'; // Cierre del terms-container
                    
                } else {
                    echo "<!-- Error: " . ($conn->error ? $conn->error : 'No hay datos') . " -->";
                    echo '<div class="col-12">
                            <div class="no-results">
                                <i class="bi bi-exclamation-triangle display-1 text-danger mb-3"></i>
                                <h3 class="text-danger">Error al cargar términos</h3>
                                <p class="text-muted">No se pudieron cargar los términos de la base de datos</p>';
                    
                    if ($conn->error) {
                        echo '<p class="text-danger small">Error: ' . htmlspecialchars($conn->error) . '</p>';
                    }
                    
                    echo '</div>
                        </div>';
                }
                
                $conn->close();
                ?>
            </div>
        </div>
    </main>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <script>
        // Función para buscar términos (búsqueda del lado del cliente)
        function searchTerms() {
            const searchTerm = document.getElementById('searchTerm').value.trim().toLowerCase();
            const resultsTitle = document.getElementById('resultsTitle');
            const searchInfo = document.getElementById('searchInfo');
            const termsList = document.getElementById('termsList');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const allTermItems = termsList.getElementsByClassName('term-item');
            
            let foundResults = 0;
            
            // Mostrar spinner de carga
            loadingSpinner.style.display = 'block';
            
            // Pequeño delay para que se vea el spinner
            setTimeout(() => {
                if (searchTerm.length > 0) {
                    resultsTitle.textContent = `Resultados de búsqueda`;
                    
                    // Buscar en los términos ya cargados
                    for (let item of allTermItems) {
                        const termTitle = item.querySelector('.term-title').textContent.toLowerCase();
                        const termDefinition = item.querySelector('p:last-child')?.textContent.toLowerCase() || '';
                        const termPronunciation = item.querySelector('.pronunciation')?.textContent.toLowerCase() || '';
                        
                        const matches = termTitle.includes(searchTerm) || 
                                       termDefinition.includes(searchTerm) ||
                                       termPronunciation.includes(searchTerm);
                        
                        if (matches) {
                            item.style.display = 'block';
                            foundResults++;
                            
                            // Resaltar el texto encontrado
                            highlightText(item, searchTerm);
                        } else {
                            item.style.display = 'none';
                        }
                    }
                    
                    // Actualizar información de búsqueda
                    if (foundResults > 0) {
                        searchInfo.textContent = `Encontrados ${foundResults} término(s)`;
                        searchInfo.style.color = 'var(--color-azul-oscuro)';
                    } else {
                        searchInfo.textContent = 'No se encontraron resultados';
                        searchInfo.style.color = 'var(--color-gris)';
                    }
                    
                } else {
                    // Si no hay término de búsqueda, mostrar todos
                    resultsTitle.textContent = "Todos los Términos";
                    searchInfo.textContent = `Mostrando ${allTermItems.length} términos`;
                    searchInfo.style.color = 'var(--color-gris)';
                    
                    for (let item of allTermItems) {
                        item.style.display = 'block';
                        removeHighlights(item);
                    }
                    foundResults = allTermItems.length;
                }
                
                // Ocultar spinner
                loadingSpinner.style.display = 'none';
                
            }, 300);
        }
        
        // Función para resaltar texto
        function highlightText(element, searchTerm) {
            removeHighlights(element); // Limpiar highlights anteriores
            
            const textNodes = getTextNodes(element);
            const regex = new RegExp(searchTerm, 'gi');
            
            textNodes.forEach(node => {
                const parent = node.parentNode;
                const text = node.textContent;
                const highlightedText = text.replace(regex, match => 
                    `<span class="highlight">${match}</span>`
                );
                
                if (highlightedText !== text) {
                    const newSpan = document.createElement('span');
                    newSpan.innerHTML = highlightedText;
                    parent.replaceChild(newSpan, node);
                }
            });
        }
        
        // Función para quitar resaltados
        function removeHighlights(element) {
            const highlights = element.querySelectorAll('.highlight');
            highlights.forEach(highlight => {
                const parent = highlight.parentNode;
                parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
                parent.normalize();
            });
        }
        
        // Función auxiliar para obtener nodos de texto
        function getTextNodes(element) {
            const textNodes = [];
            const walker = document.createTreeWalker(
                element,
                NodeFilter.SHOW_TEXT,
                null,
                false
            );
            
            let node;
            while (node = walker.nextNode()) {
                textNodes.push(node);
            }
            
            return textNodes;
        }
        
        // Agregar evento para buscar al presionar Enter
        document.getElementById('searchTerm').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchTerms();
            }
        });
        
        // Búsqueda en tiempo real
        document.getElementById('searchTerm').addEventListener('input', function() {
            const searchTerm = this.value.trim();
            searchTerms();
        });
        
        // Mostrar contador inicial al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const allTermItems = document.getElementById('termsList').getElementsByClassName('term-item');
            const searchInfo = document.getElementById('searchInfo');
            searchInfo.textContent = `Mostrando ${allTermItems.length} términos`;
        });
    </script>
</body>
</html>