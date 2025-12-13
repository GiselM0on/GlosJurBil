<?php
// get_termino_estudiante.php

include "conexion.php";
session_start();

if (!isset($_SESSION['id_Usuario']) || $_SESSION['rol'] !== 'estudiante') {
    echo json_encode(['error' => 'Acceso denegado']);
    exit();
}

$id = intval($_GET['id']);
$idUsuario = $_SESSION['id_Usuario'];

if (isset($_GET['razon'])) {
    // Obtener razón de rechazo
    $stmt = $cn->prepare("SELECT v.comentario
                            FROM validadon v
                            INNER JOIN termino t ON t.id_Termino = v.id_Termino
                            WHERE t.id_Termino = ? AND t.id_Usuario = ? AND t.estado = 'rechazado'
                            ORDER BY v.fecha_validadon DESC LIMIT 1");
    $stmt->bind_param("ii", $id, $idUsuario);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        $data = $res->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(['comentario' => 'No hay razón especificada.']);
    }
    
} else {
    // Obtener datos del término para edición
    $stmt = $cn->prepare("SELECT palabra, pronunciacion, definicion, ejemplo_aplicativo, referencia_bibliogr
                            FROM termino
                            WHERE id_Termino = ? AND id_Usuario = ?");
    $stmt->bind_param("ii", $id, $idUsuario);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        echo json_encode($res->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Término no encontrado o no tienes permisos']);
    }
}

$stmt->close();
$cn->close();
?>