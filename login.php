<?php
session_start();
include("conexion.php");

$correo = $_POST['correo'];
$contrasena = $_POST['contraseña'];
$rol = $_POST['rol'];

$sql = "SELECT * FROM usuario WHERE correo = ? AND rol = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $correo, $rol);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    if ($usuario['contrasena'] === $contrasena) {
        $_SESSION['id_Usuario'] = $usuario['id_Usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        switch ($rol) {
            case "Administrador":
                header("Location: panelAdministrador.php");
                break;
            case "Docente":
                header("Location: panelDocente.php");
                break;
            case "Estudiante":
                header("Location: panelEstudiante.php");
                break;
        }
    } else {
        echo " Contraseña incorrecta.";
    }
} else {
    echo " Usuario no encontrado o rol incorrecto.";
}
?>
