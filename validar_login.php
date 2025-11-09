<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit();
}


$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
$rol_seleccionado = isset($_POST['rol']) ? trim($_POST['rol']) : '';


if ($correo === '' || $contrasena === '' || $rol_seleccionado === '') {
    echo "<script>alert('Por favor completa todos los campos.'); window.location='login.html';</script>";
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
        echo "<script>alert('El rol seleccionado no coincide con el usuario.'); window.location='login.html';</script>";
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
     
        $_SESSION['usuario'] = isset($usuario['nombre']) ? $usuario['nombre'] : $usuario['correo'];
        $_SESSION['rol'] = $rol_bd;

       
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


if (isset($stmt) && $stmt) $stmt->close();
if (isset($conn) && $conn) $conn->close();
?>

