<?php
session_start();

include ("conexion.php");

if (isset($_SESSION['rol'])) {
    switch ($_SESSION['rol']) {
        case 'administrador':
            header("Location: admin_dashboard.php");
            exit();
        case 'docente':
            header("Location: docente_dashboard.php");
            exit();
        case 'estudiante':
            header("Location: estudiante_dashboard.php");
            exit();
    }
}


$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acceso al sistema</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(to bottom, #1e3a8a, #4d579d, #7376b1, #9696c4, #b9b8d8, #dcdceb, #ffffff);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.login-card {
    background-color: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 0 25px rgba(30, 58, 138, 0.4);
    width: 360px;
    text-align: center;
}
.login-card .avatar img {
    width: 80px;
    margin-bottom: 20px;
}
.login-card .input-group {
    margin-bottom: 15px;
    text-align: left;
}
.login-card .input-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.login-card .input-group input,
.login-card .input-group select {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}
.login-card .options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    font-size: 0.9rem;
}
.login-card .options a {
    color: #1e3a8a;
    text-decoration: none;
}
.login-card button {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: none;
    background-color: #1e3a8a;
    color: white;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.2s;
}
.login-card button:hover {
    background-color: #4d579d;
}
.error-message {
    color: red;
    margin-bottom: 15px;
    font-weight: bold;
    text-align: center;
}
</style>
</head>
<body>

<div class="login-card">
    <div class="avatar">
        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Usuario">
    </div>

    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="validar_login.php" method="POST">
        <div class="input-group">
            <label for="correo"><i class="fa fa-envelope"></i> Email ID</label>
            <input type="email" name="correo" id="correo" required>
        </div>
        <div class="input-group">
            <label for="contrasena"><i class="fa fa-lock"></i> Contraseña</label>
            <input type="password" name="contrasena" id="contrasena" required>
        </div>
        <div class="input-group">
            <label for="rol"><i class="fa fa-user-tag"></i> Selecciona Rol</label>
            <select name="rol" id="rol" required>
                <option value="">-- Seleccionar --</option>
                <option value="administrador">Administrador</option>
                <option value="docente">Docente</option>
                <option value="estudiante">Estudiante</option>
            </select>
        </div>
        <div class="options">
            <label><input type="checkbox" name="remember"> Recordarme</label>
            <a href="#">¿Olvidaste tu contraseña?</a>
        </div>
        <button type="submit">INICIAR SESIÓN</button>
    </form>
</div>

</body>
</html>
