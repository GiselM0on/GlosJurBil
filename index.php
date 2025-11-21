<?php 
session_start();

//conexion db
$conn = include ("conexion.php");       

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

echo "<!-- Iniciando consulta de términos -->";

// Consulta SIMPLIFICADA - obtener datos básicos primero
$sql_simple = "SELECT 
                tr.palabra,
                tr.definicion,
                tr.pronunciacion,
                i.nombre_idioma as idioma
            FROM traduccion tr
            JOIN idioma i ON tr.id_Idioma = i.id_idioma
            LIMIT 20";

$result = $conn->query($sql_simple);

$terms = array();
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $terms[] = $row;
    }
    echo "<!-- Términos encontrados: " . count($terms) . " -->";
} else {
    echo "<!-- No se encontraron términos. Error: " . ($conn->error ? $conn->error : 'Sin datos') . " -->";
    // Si hay error, intentemos una consulta aún más simple
    $sql_test = "SHOW TABLES";
    $test_result = $conn->query($sql_test);
    if ($test_result) {
        echo "<!-- Tablas disponibles: -->";
        while($table = $test_result->fetch_array()) {
            echo "<!-- - " . $table[0] . " -->";
        }
    }
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
    --color-amarillo: #ffa06;
    --color-azul-oscuro: #006694;
    --color-gris: #636466;
    --color-naranja: #ff9a15;
    --color-azul-claro: #27a5df;
    --color-blanco: #f1f1f1f2f2;
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
    background-color: #ff8a3d; /* Amarillo más intenso en hover */
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 160, 109, 0.3);
    color: var(--color-azul-oscuro);
}

.term-card {
    border-radius: 0.75rem;
    background-color: white;
    border-left: 5px solid var(--color-amarillo); /* Borde amarillo en lugar de azul */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s;
    border: 1px solid var(--color-gris);
}

.term-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(255, 160, 109, 0.2); /* Sombra amarilla */
    border-left: 5px solid var(--color-amarillo);
}

.term-title {
    color: var(--color-azul-oscuro);
    font-weight: bold;
}

.lang-tag {
    font-size: 0.75rem;
    padding: 0.3em 0.6em;
    border-radius: 0.5rem;
    font-weight: 600;
}

/* ETIQUETAS DE IDIOMA CON AMARILLO MÁS DESTACADO */
.lang-tag.es {
    background-color: var(--color-amarillo); /* Amarillo en lugar de azul */
    color: var(--color-azul-oscuro);
}

.lang-tag.en {
    background-color: var(--color-naranja); /* Naranja secundario */
    color: white;
}

.pronunciation {
    font-style: italic;
    color: var(--color-gris);
    font-size: 0.9rem;
}

.highlight {
    background-color: rgba(255, 160, 109, 0.3); /* Amarillo más intenso */
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

/* BOTÓN DASHBOARD CON AMARILLO PRINCIPAL */
.dashboard-btn {
    background-color: var(--color-amarillo);
    border: none;
    transition: all 0.3s;
    color: var(--color-azul-oscuro);
    font-weight: 700;
    padding: 8px 20px;
    border-radius: 0.5rem;
}

.dashboard-btn:hover {
    background-color: #ff8a3d; /* Amarillo más intenso */
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

/* BOTONES PRIMARIOS AHORA EN AMARILLO */
.btn-primary {
    background-color: var(--color-amarillo);
    border-color: var(--color-amarillo);
    color: var(--color-azul-oscuro);
    font-weight: 600;
}

.btn-primary:hover {
    background-color: #ff8a3d;
    border-color: #ff8a3d;
    color: var(--color-azul-oscuro);
}

.btn-warning {
    background-color: var(--color-naranja);
    border-color: var(--color-naranja);
    color: white;
    font-weight: 600;
}

.btn-warning:hover {
    background-color: #e88a10;
    border-color: #e88a10;
    color: white;
}

.text-primary {
    color: var(--color-azul-oscuro) !important;
}

.text-warning {
    color: var(--color-amarillo) !important; /* Amarillo para textos de advertencia */
}

.badge-primary {
    background-color: var(--color-amarillo); /* Badges en amarillo */
    color: var(--color-azul-oscuro);
}

.badge-warning {
    background-color: var(--color-naranja); /* Naranja para badges secundarios */
    color: white;
}

.alert-info {
    background-color: rgba(255, 160, 109, 0.1); /* Fondo alerta en amarillo suave */
    border-color: var(--color-amarillo);
    color: var(--color-azul-oscuro);
}

/* NAVBAR BRAND EN BLANCO */
.navbar-brand {
    color: white !important; /* Cambiado a blanco */
    font-weight: bold;
    font-size: 1.5rem;
}

.nav-link {
    color: rgba(255, 255, 255, 0.9) !important;
}

.nav-link:hover {
    color: var(--color-amarillo) !important; /* Hover en amarillo */
}

/* ELEMENTOS DESTACADOS ADICIONALES EN AMARILLO */
.featured-term {
    border: 2px solid var(--color-amarillo);
    background: linear-gradient(135deg, white 0%, rgba(255, 160, 109, 0.05) 100%);
}

.stat-number {
    color: var(--color-amarillo);
    font-size: 2.5rem;
    font-weight: bold;
}

.quick-action {
    background-color: var(--color-amarillo);
    color: var(--color-azul-oscuro);
    border: none;
    transition: all 0.3s;
}

.quick-action:hover {
    background-color: #ff8a3d;
    transform: scale(1.05);
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
                        <!-- Mostrar información del usuario y botón de dashboard -->
                        <li class="nav-item">
                            <span class="user-welcome d-none d-lg-block">
                                <i class="bi bi-person-circle"></i> 
                                <?php echo htmlspecialchars($nombreUsuario); ?> 
                                (<?php echo htmlspecialchars($rolUsuario); ?>)
                            </span>
                        </li>
                        <li class="nav-item">
                            <?php if ($rolUsuario === 'admin'): ?>
                                <a href="pantallaAdmin.php" class="btn btn-success dashboard-btn rounded-pill ms-lg-3">
                                    <i class="bi bi-speedometer2"></i> Panel Admin
                                </a>
                            <?php elseif ($rolUsuario === 'docente'): ?>
                                <a href="pantallaDocente.php" class="btn btn-success dashboard-btn rounded-pill ms-lg-3">
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
                        <!-- Mostrar botón de login si no hay usuario logueado -->
                        <li class="nav-item">
                            <a href="login.php" class="btn btn-outline-light rounded-pill ms-lg-3">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
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
                               placeholder="Buscar término (Ej. 'Amparo' o 'Warrant')..." 
                               aria-label="Buscar término">
                        <button class="btn search-button text-white" type="button" onclick="searchTerms()">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="container mt-5 pt-3">
        <h2 class="text-primary mb-4" id="resultsTitle">Términos Destacados</h2>
        
        <div class="row g-4" id="termsList">
            <!-- Los términos se cargarán aquí dinámicamente desde PHP -->
            <?php 
            //conexion db
            $conn = include ("conexion.php");       

            // Verificar si la conexión se estableció correctamente
            if (!isset($conn) || $conn->connect_error) {
                die("Error: No se pudo establecer conexión con la base de datos");
            }

            // Consulta SIMPLIFICADA - obtener datos básicos primero
            $sql_simple = "SELECT 
                            tr.palabra,
                            tr.definicion,
                            tr.pronunciacion,
                            i.nombre_idioma as idioma
                        FROM traduccion tr
                        JOIN idioma i ON tr.id_Idioma = i.id_idioma
                        LIMIT 20";

            $result = $conn->query($sql_simple);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '
                    <div class="col-lg-6">
                        <div class="card term-card p-4 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="term-title mb-0">' . htmlspecialchars($row['palabra']) . '</h4>
                                <span class="badge text-bg-primary lang-tag">' . htmlspecialchars($row['idioma']) . '</span>
                            </div>';
                    
                    if (!empty($row['pronunciacion'])) {
                        echo '<p class="pronunciation mb-2">Pronunciación: ' . htmlspecialchars($row['pronunciacion']) . '</p>';
                    }
                    
                    if (!empty($row['definicion'])) {
                        echo '<p class="mb-2"><strong>Definición:</strong> ' . htmlspecialchars($row['definicion']) . '</p>';
                    }
                    
                    echo '</div></div>';
                }
            } else {
                echo '<div class="col-12">
                        <div class="no-results">
                            <i class="bi bi-search display-1 text-muted mb-3"></i>
                            <h3 class="text-muted">No hay términos disponibles</h3>
                            <p class="text-muted">No se encontraron términos en la base de datos</p>
                        </div>
                    </div>';
            }
            ?>
        </div>
    </main>
<footer class="mt-5 py-4 text-center border-top" style="background-color: #f1f2f2; color: #636466;">
    <div class="container">
        
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
                <span style="color: #006694; font-weight: bold;">UPED</span>  2025
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
            const termsList = document.getElementById('termsList');
            const allTermCards = termsList.getElementsByClassName('col-lg-6');
            
            let foundResults = false;
            
            if (searchTerm.length > 0) {
                resultsTitle.textContent = `Resultados para "${searchTerm}"`;
                
                // Buscar en los términos ya cargados
                for (let card of allTermCards) {
                    const termTitle = card.querySelector('.term-title').textContent.toLowerCase();
                    const termDefinition = card.querySelector('p:last-child')?.textContent.toLowerCase() || '';
                    const termPronunciation = card.querySelector('.pronunciation')?.textContent.toLowerCase() || '';
                    
                    const matches = termTitle.includes(searchTerm) || 
                                   termDefinition.includes(searchTerm) ||
                                   termPronunciation.includes(searchTerm);
                    
                    if (matches) {
                        card.style.display = 'block';
                        foundResults = true;
                        
                        // Resaltar el texto encontrado
                        highlightText(card, searchTerm);
                    } else {
                        card.style.display = 'none';
                    }
                }
                
                if (!foundResults) {
                    termsList.innerHTML = `
                        <div class="col-12">
                            <div class="no-results">
                                <i class="bi bi-search display-1 text-muted mb-3"></i>
                                <h3 class="text-muted">No se encontraron resultados</h3>
                                <p class="text-muted">Intenta con otros términos de búsqueda</p>
                            </div>
                        </div>
                    `;
                }
            } else {
                // Si no hay término de búsqueda, mostrar todos
                resultsTitle.textContent = "Términos Destacados";
                for (let card of allTermCards) {
                    card.style.display = 'block';
                    removeHighlights(card);
                }
            }
        }
        
        // Función para resaltar texto
        function highlightText(element, searchTerm) {
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
        
        // Búsqueda en tiempo real (opcional)
        document.getElementById('searchTerm').addEventListener('input', function() {
            const searchTerm = this.value.trim();
            if (searchTerm.length >= 2 || searchTerm.length === 0) {
                searchTerms(); 
            }
        });
    </script>
</body>
</html>