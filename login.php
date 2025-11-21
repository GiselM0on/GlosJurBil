<?php
session_start();


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

    <style>

        
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url("fondo.jpg") no-repeat center center fixed;
            background-size: cover;

            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

       
        .login-card {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.35);
            width: 380px;
            text-align: center;
        }

        /
        .login-card .avatar img {
            width: 180px;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #888;
            background-color: #f4f4f4;
        }

        button {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            background-color: #003E9B;
            color: white;
            font-size: 1rem;
        }

        button:hover {
            background-color: #002f73;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            font-weight: bold;
        }

    </style>
</head>

<body>

<div class="login-card">

    
    <div class="avatar">
        <img src="logo_uped.png" alt="UPED">
    </div>

    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="validar_login.php" method="POST">

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="correo" required>
        </div>

        <div class="input-group">
            <label>Contraseña</label>
            <input type="password" name="contrasena" required>
        </div>

        <div class="input-group">
            <label>Selecciona Rol</label>
            <select name="rol" required>
                <option value="">-- Seleccionar --</option>
                <option value="administrador">Administrador</option>
                <option value="docente">Docente</option>
                <option value="estudiante">Estudiante</option>
            </select>
        </div>

        <button type="submit">INICIAR SESIÓN</button>

    </form>
</div>

</body>
</html>
