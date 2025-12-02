<?php
include "conexion.php";
session_start();

if (!isset($_SESSION['id_Usuario']) || $_SESSION['rol'] !== 'estudiante') {
    echo json_encode(['error' => 'Acceso denegado']);
    exit();
}

$id = intval($_GET['id']);
$idUsuario = $_SESSION['id_Usuario'];

if (isset($_GET['razon'])) {
    // Fetch razón de rechazo
    $stmt = $conn->prepare("SELECT v.comentario
                            FROM validacion v
                            INNER JOIN termino t ON t.id_Termino = v.id_Termino
                            WHERE t.id_Termino = ? AND t.id_Usuario = ? AND t.estado = 'rechazado'");
    $stmt->bind_param("ii", $id, $idUsuario);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc() ?? ['comentario' => 'No disponible'];
    echo json_encode($data);
} else {
    
    $stmt = $conn->prepare("SELECT t.palabra, t.pronunciacion, t.definicion, t.ejemplo_aplicativo, t.referencia_bibliogr
                            FROM termino t
                            WHERE t.id_Termino = ? AND t.id_Usuario = ?");
    $stmt->bind_param("ii", $id, $idUsuario);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        echo json_encode($res->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Término no encontrado o no tuyo']);
    }
}
$stmt->close();
$conn->close();
?>