<?php
include "conexion.php";
session_start();

// Configurar charset para la conexión
if (isset($cn) && is_object($cn)) {
    mysqli_set_charset($cn, "utf8mb4");
}

if (!isset($_SESSION['id_Usuario']) || $_SESSION['rol'] !== 'docente') {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['idTermino']) || !isset($_POST['accion'])) {
    $_SESSION['error'] = "Datos inválidos.";
    header("Location: docente_revision.php");
    exit();
}

$id = intval($_POST['idTermino']);
$accion = $_POST['accion'];
$motivo = $_POST['motivo'] ?? '';
$idDocente = $_SESSION['id_Usuario'];
$fecha = date("Y-m-d H:i:s");

if ($accion === "validar") {
    $stmt1 = $cn->prepare("INSERT INTO validacion (comentario, estado_validacion, fecha_validacion, id_Termino, id_Usuario)
                             VALUES (?, 'validado', ?, ?, ?)");
    $empty = '';
    $stmt1->bind_param("ssii", $empty, $fecha, $id, $idDocente);
    $stmt1->execute();

    $stmt2 = $cn->prepare("UPDATE termino SET estado='validado' WHERE id_Termino=?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();

    $_SESSION['success'] = "Término validado exitosamente.";
    header("Location: docente_revision.php");
    exit();
}

if ($accion === "rechazar") {
    if (empty(trim($motivo))) {
        $_SESSION['error'] = "Debes escribir una razón para rechazar.";
        header("Location: docente_revision.php");
        exit();
    }

    $stmt1 = $cn->prepare("INSERT INTO validacion (comentario, estado_validacion, fecha_validacion, id_Termino, id_Usuario)
                             VALUES (?, 'rechazado', ?, ?, ?)");
    $stmt1->bind_param("ssii", $motivo, $fecha, $id, $idDocente);
    $stmt1->execute();

    $stmt2 = $cn->prepare("UPDATE termino SET estado='rechazado' WHERE id_Termino=?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();

    $_SESSION['success'] = "Término rechazado exitosamente.";
    header("Location: docente_revision.php");
    exit();
}
?>
