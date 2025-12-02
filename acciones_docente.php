<?php
session_start();
include "conexion.php";

// Activar errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar que el docente esté logueado
if (!isset($_SESSION['id_Usuario']) || $_SESSION['rol'] !== 'docente') {
    $_SESSION['error'] = "Debes iniciar sesión como docente.";
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
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
$idDocente = $_SESSION['id_Usuario'];
$fecha = date("Y-m-d H:i:s");

// Validar que el término existe
$stmtCheck = $cn->prepare("SELECT id_Termino FROM termino WHERE id_Termino = ? AND estado = 'pendiente'");
$stmtCheck->bind_param("i", $id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows == 0) {
    $_SESSION['error'] = "El término no existe o ya fue revisado.";
    $stmtCheck->close();
    header("Location: docente_revision.php");
    exit();
}
$stmtCheck->close();

if ($accion === "validar") {
    // VALIDAR TÉRMINO
    $stmt1 = $cn->prepare("INSERT INTO validadon (comentario, estado_validadon, fecha_validadon, id_Termino, id_Usuario) 
                           VALUES (?, 'validado', ?, ?, ?)");
    $comentarioVacio = 'Término validado correctamente.';
    $stmt1->bind_param("ssii", $comentarioVacio, $fecha, $id, $idDocente);
    
    if (!$stmt1->execute()) {
        $_SESSION['error'] = "Error al registrar validación: " . $stmt1->error;
        $stmt1->close();
        header("Location: docente_revision.php");
        exit();
    }
    $stmt1->close();
    
    // Actualizar estado del término
    $stmt2 = $cn->prepare("UPDATE termino SET estado = 'validado' WHERE id_Termino = ?");
    $stmt2->bind_param("i", $id);
    
    if ($stmt2->execute()) {
        $_SESSION['success'] = "Término validado exitosamente.";
    } else {
        $_SESSION['error'] = "Error al actualizar término: " . $stmt2->error;
    }
    $stmt2->close();
    
} elseif ($accion === "rechazar") {
    // RECHAZAR TÉRMINO
    if (empty($motivo)) {
        $_SESSION['error'] = "Debes escribir una razón para rechazar.";
        header("Location: docente_revision.php");
        exit();
    }
    
    // Registrar validación con motivo
    $stmt1 = $cn->prepare("INSERT INTO validadon (comentario, estado_validadon, fecha_validadon, id_Termino, id_Usuario) 
                           VALUES (?, 'rechazado', ?, ?, ?)");
    $stmt1->bind_param("ssii", $motivo, $fecha, $id, $idDocente);
    
    if (!$stmt1->execute()) {
        $_SESSION['error'] = "Error al registrar rechazo: " . $stmt1->error;
        $stmt1->close();
        header("Location: docente_revision.php");
        exit();
    }
    $stmt1->close();
    
    // Actualizar estado del término
    $stmt2 = $cn->prepare("UPDATE termino SET estado = 'rechazado' WHERE id_Termino = ?");
    $stmt2->bind_param("i", $id);
    
    if ($stmt2->execute()) {
        $_SESSION['success'] = "✅ Término rechazado exitosamente. El estudiante podrá ver el motivo.";
    } else {
        $_SESSION['error'] = "Error al actualizar término: " . $stmt2->error;
    }
    $stmt2->close();
    
} else {
    $_SESSION['error'] = "Acción no reconocida.";
}

// Cerrar conexión
$cn->close();

// Redirigir de vuelta
header("Location: docente_revision.php");
exit();
?>