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