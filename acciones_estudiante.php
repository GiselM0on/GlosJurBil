<?php
include "conexion.php";
session_start();

if (!isset($_SESSION['id_Usuario']) || $_SESSION['rol'] !== 'estudiante') {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['accion']) || $_POST['accion'] !== 'guardar') {
    $_SESSION['error'] = "Acción inválida.";
    header("Location: estudiante_terminos.php");
    exit();
}

$idUsuario = $_SESSION['id_Usuario'];
$id = intval($_POST['idTermino']);
$palabra = trim($_POST['palabra']);
$pronunciacion = trim($_POST['pronunciacion'] ?? '');
$definicion = trim($_POST['definicion']);
$ejemplo = trim($_POST['ejemplo'] ?? '');
$referencia = trim($_POST['referencia'] ?? '');
$fecha = date("Y-m-d H:i:s");

if (empty($palabra) || empty($definicion)) {
    $_SESSION['error'] = "Palabra y definición son obligatorios.";
    header("Location: estudiante_terminos.php");
    exit();
}

if ($id == 0) {
    // Agregar nuevo
    $stmt = $conn->prepare("INSERT INTO termino (palabra, pronunciacion, definicion, ejemplo_aplicativo, referencia_bibliogr, estado, fecha_creacion, fecha_modificacion, id_Usuario)
                            VALUES (?, ?, ?, ?, ?, 'pendiente', ?, ?, ?)");
    $stmt->bind_param("ssssssss", $palabra, $pronunciacion, $definicion, $ejemplo, $referencia, $fecha, $fecha, $idUsuario);
    $stmt->execute();
    $_SESSION['success'] = "Término agregado y enviado a revisión.";
} else {
    // Modificar 
    $stmtCheck = $conn->prepare("SELECT id_Termino FROM termino WHERE id_Termino = ? AND id_Usuario = ? AND estado != 'validado'");
    $stmtCheck->bind_param("ii", $id, $idUsuario);
    $stmtCheck->execute();
    if ($stmtCheck->get_result()->num_rows == 0) {
        $_SESSION['error'] = "No puedes modificar este término.";
        header("Location: estudiante_terminos.php");
        exit();
    }

    $stmt = $conn->prepare("UPDATE termino SET palabra=?, pronunciacion=?, definicion=?, ejemplo_aplicativo=?, referencia_bibliogr=?, estado='pendiente', fecha_modificacion=?
                            WHERE id_Termino=?");
    $stmt->bind_param("sssssss", $palabra, $pronunciacion, $definicion, $ejemplo, $referencia, $fecha, $id);
    $stmt->execute();
    $_SESSION['success'] = "Término modificado y reenviado a revisión.";
}

header("Location: estudiante_terminos.php");
exit();
?>