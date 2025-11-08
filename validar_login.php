<?php
session_start();
include 'conexion.php'; 

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];

$sql = "SELECT * FROM usuario WHERE correo='$correo' AND contrasena='$contrasena'";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
 
    $usuario = $resultado->fetch_assoc();


    $_SESSION['usuario'] = $usuario['nombre'];
    $_SESSION['rol'] = $usuario['rol'];

  
    if ($usuario['rol'] == 'administrador') {
        header("Location: http://localhost/gls_jur_bi/admin.php");
    } elseif ($usuario['rol'] == 'maestro') {
        header("Location: http://localhost/gls_jur_bi/maestro.php");
    } elseif ($usuario['rol'] == 'estudiante') {
        header("Location: http://localhost/gls_jur_bi/estudiante.php");
    }
    exit();
} else {
   
    header("Location: http://localhost/gls_jur_bi/index.php?error=Usuario o contraseña incorrectos");
    exit();
}
?>
