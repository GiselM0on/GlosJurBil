<?php
session_start();

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit();
}

// Recoger campos (sin usar '??' para compatibilidad)
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
$rol_seleccionado = isset($_POST['rol']) ? trim($_POST['rol']) : '';

// Validar campos obligatorios
if ($correo === '' || $contrasena === '' || $rol_seleccionado === '') {
    echo "<script>alert('Por favor completa todos los campos.'); window.location='login.html';</script>";
    exit();
}

// Conexión (conexion.php debe definir $conn)
include("conexion.php");
if (!isset($conn)) {
    die("Error: no se encontró la conexión a la base de datos.");
}

// Buscar usuario por correo
$sql = "SELECT * FROM usuario WHERE correo = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la consulta: " . $conn->error);
}

$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    // Comprobar que el rol seleccionado coincide con el rol de la BD (sin importar mayúsculas)
    $rol_bd = isset($usuario['rol']) ? strtolower(trim($usuario['rol'])) : '';
    if ($rol_bd !== strtolower(trim($rol_seleccionado))) {
        echo "<script>alert('El rol seleccionado no coincide con el usuario.'); window.location='login.html';</script>";
        exit();
    }

    $hash_bd = isset($usuario['contrasena']) ? $usuario['contrasena'] : '';

    $password_ok = false;
    // Intentar verificación con password_verify (si está hasheada)
    if (!empty($hash_bd) && password_verify($contrasena, $hash_bd)) {
        $password_ok = true;
    } else {
        // Fallback: comparar texto plano (solo para pruebas; NO recomendado en producción)
        if ($contrasena === $hash_bd) {
            $password_ok = true;
        }
    }

    if ($password_ok) {
        // Guardar en sesión: nombre y rol (rol en minúsculas para consistencia)
        $_SESSION['usuario'] = isset($usuario['nombre']) ? $usuario['nombre'] : $usuario['correo'];
        $_SESSION['rol'] = $rol_bd;

        // Redirigir al glosario (misma pantalla para todos)
        header("Location: glosario.php");
        exit();
    } else {
        echo "<script>alert('Contraseña incorrecta'); window.location='login.html';</script>";
        exit();
    }
} else {
    echo "<script>alert('Usuario no encontrado'); window.location='login.html';</script>";
    exit();
}

// Cerrar recursos
if (isset($stmt) && $stmt) $stmt->close();
if (isset($conn) && $conn) $conn->close();
?>

