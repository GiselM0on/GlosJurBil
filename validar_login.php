<?php
session_start();
include 'conexion.php'; // asegúrate de que conexion.php esté en la misma carpeta

// Captura los datos del formulario
$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];

// Consulta en la base de datos
$sql = "SELECT * FROM usuario WHERE correo='$correo' AND contrasena='$contrasena'";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    // Usuario encontrado
    $usuario = $resultado->fetch_assoc();

    // Guardar datos en sesión
    $_SESSION['usuario'] = $usuario['nombre'];
    $_SESSION['rol'] = $usuario['rol'];

    // Redirigir según rol usando URL absoluta
    if ($usuario['rol'] == 'administrador') {
        header("Location: http://localhost/gls_jur_bi/admin.php");
    } elseif ($usuario['rol'] == 'maestro') {
        header("Location: http://localhost/gls_jur_bi/maestro.php");
    } elseif ($usuario['rol'] == 'estudiante') {
        header("Location: http://localhost/gls_jur_bi/estudiante.php");
    }
    exit();
} else {
    // Usuario no encontrado → volver a login con mensaje
    header("Location: http://localhost/gls_jur_bi/index.php?error=Usuario o contraseña incorrectos");
    exit();
}
?>
