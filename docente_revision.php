<?php
include "conexion.php";

// Obtener términos pendientes
$sql = "SELECT t.id_Termino, t.palabra, u.nombre AS estudiante
        FROM termino t
        INNER JOIN usuario u ON u.id_Usuario = t.id_Usuario
        WHERE t.estado = 'pendiente'";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Revisión de términos</title>
<link rel="stylesheet" href="libreria/estilos.css">
<style>
:root {
    --color-amarillo: #fff06dff;
    --color-azul-oscuro: #006694;
    --color-gris: #636466;
    --color-gris-claro: #f1f2f2;
    --color-naranja: #ff9a15;
    --color-azul-claro: #27a5df;
}

body {
    background-color: var(--color-gris-claro);
    color: var(--color-azul-oscuro);
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
    margin: 0;
    padding: 0;
}

.sidebar {
    width: 260px;
    background: linear-gradient(180deg, var(--color-azul-oscuro) 0%, #004466 100%);
    color: white;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    padding: 20px 0;
}

.menu-section {
    margin-bottom: 25px;
}

.section-title {
    padding: 0 20px 10px;
    font-size: 14px;
    color: var(--color-azul-claro);
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.menu-items {
    list-style: none;
    padding: 10px 0;
    margin: 0;
}

.menu-item {
    padding: 12px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    text-decoration: none;
    color: var(--color-gris-claro);
    border-left: 3px solid transparent;
}

.menu-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-left: 3px solid var(--color-amarillo);
    color: white;
}

.menu-item.active {
    background-color: rgba(255, 160, 109, 0.2);
    border-left: 3px solid var(--color-amarillo);
    color: white;
}

.menu-item i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.nav-section {
    margin-top: 30px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 20px;
}

.sidebar-nav {
    flex: 1;
    overflow-y: auto;
    padding: 10px 0;
}

.sidebar-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 15px 0;
    margin-top: auto;
}

#content {
    margin-left: 260px;
    padding: 20px;
    min-height: 100vh;
    transition: all 0.3s;
    background-color: var(--color-gris-claro);
}

.modal {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
}
.modal-contenido {
    background: white;
    padding: 20px;
    width: 500px;
    border-radius: 12px;
}
</style>
</head>
<body>

<h1>Términos pendientes de revisión</h1>

<table border="1" cellpadding="10">
<tr>
    <th>Palabra</th>
    <th>Estudiante</th>
    <th>Acción</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['palabra'] ?></td>
    <td><?= $row['estudiante'] ?></td>
    <td><button onclick="abrirModal(<?= $row['id_Termino'] ?>)">Revisar</button></td>
</tr>
<?php } ?>
</table>

<!-- MODAL -->
<div id="modalRevision" class="modal">
    <div class="modal-contenido">
        
        <h2 id="tituloTermino"></h2>
        <p id="descTermino"></p>
        <p><strong>Enviado por:</strong> <span id="nombreEstudiante"></span></p>

        <form action="acciones_docente.php" method="POST">

            <input type="hidden" name="idTermino" id="idTermino">

            <label><b>Motivo del rechazo</b> (obligatorio si rechaza):</label>
            <textarea id="motivo" name="motivo" rows="3"
            placeholder="Escribe aquí el motivo del rechazo..."
            oninput="habilitar()"></textarea>

            <br><br>

            <button type="submit" name="accion" value="validar">Validar</button>
            <button type="submit" name="accion" value="rechazar" id="btnRechazar" disabled>Rechazar</button>

        </form>

        <br>
        <button onclick="cerrarModal()">Cerrar</button>
    </div>
</div>

<script>
function abrirModal(id) {
    fetch("get_termino.php?id=" + id)
        .then(res => res.json())
        .then(data => {
            document.getElementById("tituloTermino").innerText = data.palabra;
            document.getElementById("descTermino").innerText = data.definicion;
            document.getElementById("nombreEstudiante").innerText = data.estudiante;
            document.getElementById("idTermino").value = id;
            document.getElementById("modalRevision").style.display = "flex";
        });
}

function cerrarModal() {
    document.getElementById("modalRevision").style.display = "none";
}

function habilitar() {
    let texto = document.getElementById("motivo").value.trim();
    document.getElementById("btnRechazar").disabled = texto === "";
}
</script>

</body>
</html>