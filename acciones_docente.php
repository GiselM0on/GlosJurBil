<?php
include "conexion.php";

$id        = $_POST['idTermino'];
$accion    = $_POST['accion'];
$motivo    = $_POST['motivo'] ?? null;

session_start();
$idDocente = $_SESSION['id_Usuario']; 

$fecha = date("Y-m-d H:i:s");

if ($accion === "validar") {

    $sql1 = "INSERT INTO validacion (comentario, estado_validacion, fecha_validacion, id_Termino, id_Usuario)
             VALUES ('', 'validado', '$fecha', $id, $idDocente)";
    $conn->query($sql1);

    $sql2 = "UPDATE termino SET estado='validado' WHERE id_Termino=$id";
    $conn->query($sql2);

    header("Location: docente_revision.php");
    exit();
}

if ($accion === "rechazar") {

    if (empty(trim($motivo))) {
        die("Debes escribir un motivo para rechazar.");
    }

    $sql1 = "INSERT INTO validacion (comentario, estado_validacion, fecha_validacion, id_Termino, id_Usuario)
             VALUES ('$motivo', 'rechazado', '$fecha', $id, $idDocente)";
    $conn->query($sql1);

    $sql2 = "UPDATE termino SET estado='rechazado' WHERE id_Termino=$id";
    $conn->query($sql2);

    header("Location: docente_revision.php");
    exit();
}
