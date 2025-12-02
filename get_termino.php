<?php
include "conexion.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID inválido']);
    exit();
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT t.palabra, t.definicion, u.nombre AS estudiante
                       FROM termino t
                       INNER JOIN usuario u ON u.id_Usuario = t.id_Usuario
                       WHERE t.id_Termino = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $data = $res->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Término no encontrado']);
}

$stmt->close();
$conn->close();
?>
