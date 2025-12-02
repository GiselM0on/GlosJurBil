<?php
// get_termino.php
session_start(); // Añadir session_start() si necesitas verificar sesión

// Configurar cabeceras para JSON y UTF-8
header('Content-Type: application/json; charset=UTF-8');

// Permitir CORS si es necesario (para desarrollo)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include "conexion.php";

// Configurar charset para la conexión
if ($cn) {
    $cn->set_charset("utf8");
}

// Activar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar que el ID sea válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID inválido o no proporcionado']);
    exit();
}

$id = intval($_GET['id']);

// Verificar conexión
if (!$cn) {
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit();
}

// Preparar y ejecutar la consulta
$sql = "SELECT t.palabra, t.definicion, u.nombre AS estudiante
        FROM termino t
        INNER JOIN usuario u ON u.id_Usuario = t.id_Usuario
        WHERE t.id_Termino = ?";
        
$stmt = $cn->prepare($sql);

if (!$stmt) {
    // Registrar error para depuración
    error_log("Error preparando consulta: " . $cn->error);
    echo json_encode(['error' => 'Error al preparar la consulta']);
    exit();
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Verificar que los campos existan
    if (!isset($row['definicion'])) {
        $row['definicion'] = "No hay definición disponible";
    }
    
    if (!isset($row['palabra'])) {
        $row['palabra'] = "Sin título";
    }
    
    if (!isset($row['estudiante'])) {
        $row['estudiante'] = "Desconocido";
    }
    
    echo json_encode($row, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => 'Término no encontrado en la base de datos']);
}

$stmt->close();
$cn->close();
?>