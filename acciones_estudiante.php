<?php
session_start();
include "conexion.php";

// Activar errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar que el estudiante esté logueado
if (!isset($_SESSION['id_Usuario']) || $_SESSION['rol'] !== 'estudiante') {
    $_SESSION['error'] = "Debes iniciar sesión como estudiante.";
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
$pronunciacion = trim(isset($_POST['pronunciacion']) ? $_POST['pronunciacion'] : '');
$definicion = trim($_POST['definicion']);
$ejemplo = trim(isset($_POST['ejemplo']) ? $_POST['ejemplo'] : '');
$referencia = trim(isset($_POST['referencia']) ? $_POST['referencia'] : '');
$fecha = date("Y-m-d H:i:s");

// Validaciones básicas
if (empty($palabra) || empty($definicion)) {
    $_SESSION['error'] = "Palabra y definición son obligatorios.";
    header("Location: estudiante_terminos.php");
    exit();
}

// Validar longitud
if (strlen($palabra) > 150) {
    $_SESSION['error'] = "La palabra no puede tener más de 150 caracteres.";
    header("Location: estudiante_terminos.php");
    exit();
}

if ($id == 0) {
    // AGREGAR NUEVO TÉRMINO
    $stmt = $cn->prepare("INSERT INTO termino (palabra, pronunciacion, definicion, ejemplo_aplicativo, referencia_bibliogr, estado, fecha_creacion, fecha_modificacion, id_Usuario) 
                          VALUES (?, ?, ?, ?, ?, 'pendiente', ?, ?, ?)");
    
    if ($stmt === false) {
        $_SESSION['error'] = "Error al preparar la consulta: " . $cn->error;
        header("Location: estudiante_terminos.php");
        exit();
    }
    
    $stmt->bind_param("sssssssi", $palabra, $pronunciacion, $definicion, $ejemplo, $referencia, $fecha, $fecha, $idUsuario);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Término agregado exitosamente y enviado para revisión.";
    } else {
        $_SESSION['error'] = "❌ Error al guardar el término: " . $stmt->error;
    }
    
    $stmt->close();
    
} else {
    // MODIFICAR TÉRMINO EXISTENTE
    // Verificar que el término existe y pertenece al estudiante
    $stmtCheck = $cn->prepare("SELECT id_Termino FROM termino WHERE id_Termino = ? AND id_Usuario = ? AND estado != 'validado'");
    $stmtCheck->bind_param("ii", $id, $idUsuario);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    
    if ($resultCheck->num_rows == 0) {
        $_SESSION['error'] = "No puedes modificar este término (ya está validado o no te pertenece).";
        $stmtCheck->close();
        header("Location: estudiante_terminos.php");
        exit();
    }
    $stmtCheck->close();

    // Actualizar término
    $stmt = $cn->prepare("UPDATE termino SET palabra = ?, pronunciacion = ?, definicion = ?, ejemplo_aplicativo = ?, referencia_bibliogr = ?, estado = 'pendiente', fecha_modificacion = ? 
                          WHERE id_Termino = ?");
    
    $stmt->bind_param("si", $palabra, $pronunciacion, $definicion, $ejemplo, $referencia, $fecha, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Término modificado exitosamente y reenviado para revisión.";
    } else {
        $_SESSION['error'] = "Error al modificar el término: " . $stmt->error;
    }
    
    $stmt->close();
}

// Cerrar conexión
$cn->close();

// Redirigir de vuelta
header("Location: estudiante_terminos.php");
exit();
?>