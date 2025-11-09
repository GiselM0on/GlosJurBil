<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso al sistema</title>
    <link rel="stylesheet" href="login.css">
</head>
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
</style>

<body>
    <div class="login-card">
        <div class="avatar">
            <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Usuario">
        </div>
        <form action="validar_login.php" method="POST">
            <div class="input-group">
                <label for="correo"><i class="fa fa-envelope"></i> Email ID</label>
                <input type="email" name="correo" id="correo" required>
            </div>
            <div class="input-group">
                <label for="contraseña"><i class="fa fa-lock"></i> Password</label>
                <input type="password" name="contraseña" id="contraseña" required>
            </div>
            <div class="input-group">
                <label for="rol"><i class="fa fa-user-tag"></i> Select Role</label>
                <select name="rol" id="rol" required>
                    <option value="">-- Select --</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Docente">Docente</option>
                    <option value="Estudiante">Estudiante</option>
                </select>
            </div>
            <div class="options">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="#">Forgot Password?</a>
            </div>
            <button type="submit">LOGIN</button>
        </form>
    </div>

    <!-- Font Awesome para íconos -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>