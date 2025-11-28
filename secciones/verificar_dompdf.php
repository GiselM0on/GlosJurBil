<?php
include ("conexion.php");

$sql = "SELECT id_termino, palabra FROM termino";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Términos</title>
</head>
<body>

<h2>Selecciona los términos para descargar en PDF</h2>

<form action="reporte.php" method="POST">
<?php
while ($row = $result->fetch_assoc()) {
    echo '
    <div>
        <input type="checkbox" name="terminos[]" value="'.$row['id_termino'].'">
        '.$row['palabra'].'
    </div>';
}
?>
    <br>
    <button type="submit">Generar PDF</button>
</form>

</body>
</html>
