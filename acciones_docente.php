<?php
// acciones_docente.php
session_start();
include "conexion.php";

// Verificación de seguridad mejorada
if (!isset($_SESSION['id_Usuario']) || !isset($_SESSION['rol'])) {
    $_SESSION['error'] = "Debes iniciar sesión para realizar esta acción.";
    header("Location: login.php");
    exit();
}

if ($_SESSION['rol'] !== 'docente') {
    $_SESSION['error'] = "Acceso denegado. Solo los docentes pueden realizar esta acción.";
    
    // Redirigir según el rol
    $rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';
    switch ($rol) {
        case 'estudiante':
            header("Location: estudiante_terminos.php");
            break;
        case 'administrador':
            header("Location: pantallaAdmin.php");
            break;
        default:
            header("Location: login.php");
            break;
    }
    exit();
}

// Configurar charset para la conexión
if ($cn) {
    $cn->set_charset("utf8");
}

try {
    // Configurar manejo de errores (para versiones de PHP que lo soporten)
    if (function_exists('mysqli_report')) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }
    
    // Obtener datos con validación
    $id = isset($_POST['idTermino']) ? intval($_POST['idTermino']) : 0;
    $accion = isset($_POST['accion']) ? trim($_POST['accion']) : '';
    $motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
    $idDocente = $_SESSION['id_Usuario'];
    $fecha = date("Y-m-d H:i:s");

    // Validar datos básicos
    if ($id <= 0) {
        throw new Exception("ID de término inválido.");
    }
    
    if (!in_array($accion, ['validar', 'rechazar'])) {
        throw new Exception("Acción no válida.");
    }
    
    if ($accion === "rechazar" && empty($motivo)) {
        throw new Exception("Debes escribir una razón para rechazar.");
    }

    // Verificar que el término existe y está pendiente
    $stmtCheck = $cn->prepare("SELECT id_Termino FROM termino WHERE id_Termino = ? AND estado = 'pendiente'");
    $stmtCheck->bind_param("i", $id);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows == 0) {
        throw new Exception("El término no existe o ya fue revisado.");
    }
    $stmtCheck->close();

    // Realizar la operación correspondiente
    if ($accion === "validar") {
        validarTermino($cn, $id, $idDocente, $fecha);
        $_SESSION['success'] = "Término validado exitosamente.";
    } elseif ($accion === "rechazar") {
        rechazarTermino($cn, $id, $motivo, $idDocente, $fecha);
        $_SESSION['success'] = "✅ Término rechazado exitosamente. El estudiante podrá ver el motivo.";
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

// Cerrar conexión
if (isset($cn)) {
    $cn->close();
}

// Redirigir de vuelta
header("Location: docente_revision.php");
exit();

// =================== FUNCIONES ===================

function validarTermino($cn, $id, $idDocente, $fecha) {
    try {
        // Iniciar transacción si está disponible
        if (method_exists($cn, 'begin_transaction')) {
            $cn->begin_transaction();
        }
        
        // Registrar la validación
        $stmt1 = $cn->prepare("
            INSERT INTO validadon (comentario, estado_validadon, fecha_validadon, id_Termino, id_Usuario)
            VALUES (?, 'validado', ?, ?, ?)
        ");
        $comentario = 'Término validado correctamente';
        $stmt1->bind_param("ssii", $comentario, $fecha, $id, $idDocente);
        
        if (!$stmt1->execute()) {
            throw new Exception("Error al registrar validación: " . $stmt1->error);
        }
        $stmt1->close();

        // Actualizar el estado del término
        $stmt2 = $cn->prepare("UPDATE termino SET estado = 'validado' WHERE id_Termino = ?");
        $stmt2->bind_param("i", $id);
        
        if (!$stmt2->execute()) {
            throw new Exception("Error al actualizar término: " . $stmt2->error);
        }
        $stmt2->close();

        // Confirmar la transacción si se inició
        if (method_exists($cn, 'commit')) {
            $cn->commit();
        }
        
    } catch (Exception $e) {
        // Revertir cambios si se inició transacción
        if (method_exists($cn, 'rollback')) {
            $cn->rollback();
        }
        throw new Exception("Error al validar el término: " . $e->getMessage());
    }
}

function rechazarTermino($cn, $id, $motivo, $idDocente, $fecha) {
    try {
        // Iniciar transacción si está disponible
        if (method_exists($cn, 'begin_transaction')) {
            $cn->begin_transaction();
        }
        
        // Registrar el rechazo con el motivo
        $stmt1 = $cn->prepare("
            INSERT INTO validadon (comentario, estado_validadon, fecha_validadon, id_Termino, id_Usuario)
            VALUES (?, 'rechazado', ?, ?, ?)
        ");
        $stmt1->bind_param("ssii", $motivo, $fecha, $id, $idDocente);
        
        if (!$stmt1->execute()) {
            throw new Exception("Error al registrar rechazo: " . $stmt1->error);
        }
        $stmt1->close();

        // Actualizar el estado del término
        $stmt2 = $cn->prepare("UPDATE termino SET estado = 'rechazado' WHERE id_Termino = ?");
        $stmt2->bind_param("i", $id);
        
        if (!$stmt2->execute()) {
            throw new Exception("Error al actualizar término: " . $stmt2->error);
        }
        $stmt2->close();

        // Confirmar la transacción si se inició
        if (method_exists($cn, 'commit')) {
            $cn->commit();
        }
        
    } catch (Exception $e) {
        // Revertir cambios si se inició transacción
        if (method_exists($cn, 'rollback')) {
            $cn->rollback();
        }
        throw new Exception("Error al rechazar el término: " . $e->getMessage());
    }
}
?>