<?php
session_start();

// Aumentar límite de memoria para evitar errores
ini_set('memory_limit', '256M');

// ---------------------------------------------
// 1. CONEXIÓN A LA BASE DE DATOS
// ---------------------------------------------
include "conexion.php";

// ---------------------------------------------
// 2. CARGAR DOMPDF - RUTA CORREGIDA
// ---------------------------------------------
$dompdf_loaded = false;
$dompdf_paths = [
    __DIR__ . "/libreria/dompdf/autoload.inc.php",
    __DIR__ . "/dompdf/autoload.inc.php",
    "C:/xampp/htdocs/GlosJurBil/libreria/dompdf/autoload.inc.php"
];

foreach ($dompdf_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $dompdf_loaded = true;
        break;
    }
}

if (!$dompdf_loaded) {
    die("❌ Error: No se pudo cargar la librería dompdf. Verifica que esté instalada en la carpeta libreria/");
}

use Dompdf\Dompdf;

// ---------------------------------------------
// 3. VALIDAR SELECCIÓN
// ---------------------------------------------
if (!isset($_POST['terminos']) || empty($_POST['terminos'])) {
    die("<h2 style='text-align:center;color:red;'>❌ No seleccionaste términos para generar el reporte.</h2>");
}

$ids = $_POST['terminos'];
$ids_limpios = array_map('intval', $ids);
$lista_ids = implode(",", $ids_limpios);

// ---------------------------------------------
// 4. CONSULTA CON UTF-8
// ---------------------------------------------
// Establecer charset UTF-8 para la conexión
$cn->set_charset("utf8");

$sql = "SELECT palabra, definicion, ejemplo_aplicativo 
        FROM termino 
        WHERE id_termino IN ($lista_ids)";

$result = $cn->query($sql);

if (!$result || $result->num_rows === 0) {
    die("❌ No se encontraron términos en la base de datos.");
}


$html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Reporte de Términos Jurídicos</title>
    <style>
        @page { margin: 30px; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #006694;
            padding-bottom: 15px;
        }

        .logo-container {
            flex: 0 0 auto;
        }

        .logo {
            max-width: 100px;
            height: auto;
        }

        .header-center {
            flex: 1;
            text-align: center;
        }

        .titulo-general {
            color: #006694;
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }

        .fecha {
            color: #636466;
            font-size: 12px;
            margin: 0;
            text-align: right;
        }

        .termino {
            margin-bottom: 15px;
            padding: 0;
            page-break-inside: avoid;
        }

        .palabra {
            font-size: 16px;
            font-weight: bold;
            color: #006694;
            margin-bottom: 5px;
        }

        .definicion {
            font-size: 13px;
            margin-bottom: 5px;
            color: #333;
            line-height: 1.4;
        }

        .ejemplo {
            font-size: 12px;
            color: #666;
            font-style: italic;
            margin-bottom: 5px;
        }

        .separador {
            border: none;
            height: 1px;
            background: #ddd;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class='header'>
        <div class='logo-container'></div>
        <div class='header-center'>
            <h1 class='titulo-general'>Reporte de términos jurídicos</h1>
        </div>
        <div class='fecha'>" . date('d/m/Y') . "</div>
    </div>
";

// ---------------------------------------------
// 6. AGREGAR LOS TÉRMINOS COMO LISTADO CON CARACTERES ESPECIALES
// ---------------------------------------------
$counter = 0;
while ($row = $result->fetch_assoc()) {
    $counter++;
    
    // Usar htmlspecialchars con UTF-8 para caracteres especiales
    $palabra = htmlspecialchars($row['palabra'], ENT_QUOTES, 'UTF-8');
    $definicion = htmlspecialchars($row['definicion'], ENT_QUOTES, 'UTF-8');
    $ejemplo = htmlspecialchars($row['ejemplo_aplicativo'], ENT_QUOTES, 'UTF-8');

    $html .= "
    <div class='termino'>
        <div class='palabra'>$counter. $palabra</div>
        <div class='definicion'>$definicion</div>
        " . ($ejemplo ? "<div class='ejemplo'>Ejemplo: $ejemplo</div>" : "") . "
        " . ($counter < $result->num_rows ? "<hr class='separador'>" : "") . "
    </div>
    ";
}

$html .= "
</body>
</html>";

// ---------------------------------------------
// 7. GENERAR PDF CON UTF-8
// ---------------------------------------------
$dompdf = new Dompdf();
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');

// Configurar opciones para mejor soporte de caracteres
$options = $dompdf->getOptions();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf->render();

// ---------------------------------------------
// 8. DESCARGAR PDF
// ---------------------------------------------
$filename = "reporte_terminos_" . date('Y-m-d') . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;
?>