<?php
// get_termino.php
include "conexion.php";

// Activar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID inválido']);
    exit();
}

$id = intval($_GET['id']);

echo "<!-- DEPURACIÓN: ID recibido: $id -->\n";

$stmt = $cn->prepare("SELECT t.palabra, t.definicion, u.nombre AS estudiante
                       FROM termino t
                       INNER JOIN usuario u ON u.id_Usuario = t.id_Usuario
                       WHERE t.id_Termino = ?");
                       
if (!$stmt) {
    echo json_encode(['error' => 'Error al preparar consulta: ' . $cn->error]);
    exit();
}

$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

echo "<!-- DEPURACIÓN: Filas encontradas: " . $res->num_rows . " -->\n";

if ($res->num_rows > 0) {
    $data = $res->fetch_assoc();
    echo "<!-- DEPURACIÓN: Datos - Palabra: " . $data['palabra'] . " -->\n";
    echo "<!-- DEPURACIÓN: Datos - Estudiante: " . $data['estudiante'] . " -->\n";
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Término no encontrado']);
}

$stmt->close();
$cn->close();
?>