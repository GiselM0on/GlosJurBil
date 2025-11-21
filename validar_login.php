<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}


$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
$rol_seleccionado = isset($_POST['rol']) ? trim($_POST['rol']) : '';


if ($correo === '' || $contrasena === '' || $rol_seleccionado === '') {
    header("Location: login.php?error=Por favor completa todos los campos");
    exit();
}


include("conexion.php");
if (!isset($conn)) {
    die("Error: no se encontró la conexión a la base de datos.");
}


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

   
    $rol_bd = isset($usuario['rol']) ? strtolower(trim($usuario['rol'])) : '';
    if ($rol_bd !== strtolower(trim($rol_seleccionado))) {
        header("Location: login.php?error=El rol seleccionado no coincide con el usuario");
        exit();
    }

   
    $hash_bd = isset($usuario['contrasena']) ? $usuario['contrasena'] : '';
    $password_ok = false;

    
    if (!empty($hash_bd) && password_verify($contrasena, $hash_bd)) {
        $password_ok = true;
    } else {
    
        if ($contrasena === $hash_bd) {
            $password_ok = true;
        }
    }

    if ($password_ok) {

        if (isset($usuario['nombre']) && !empty($usuario['nombre'])) {
            $_SESSION['usuario'] = $usuario['nombre'];
        } else {
            $_SESSION['usuario'] = $usuario['correo'];
        }

        $_SESSION['rol'] = $rol_bd;

       
        switch ($rol_bd) {
            case 'administrador':
                header("Location: admin_dashboard.php");
                break;
            case 'docente':
                header("Location: docente_dashboard.php");
                break;
            case 'estudiante':
                header("Location: estudiante_dashboard.php");
                break;
            default:
                header("Location: login.php");
                break;
        }

        exit();

    } else {
        header("Location: login.php?error=Contraseña incorrecta");
        exit();
    }

} else {
    header("Location: login.php?error=Usuario no encontrado");
    exit();
}

// Cerrar recursos
if (isset($stmt) && $stmt) $stmt->close();
if (isset($conn) && $conn) $conn->close();
?>
