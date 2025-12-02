<?php
include "conexion.php";

$id = $_GET['id'];

$sql = "SELECT t.palabra, t.definicion, u.nombre AS estudiante
        FROM termino t
        INNER JOIN usuario u ON u.id_Usuario = t.id_Usuario
        WHERE t.id_Termino = $id";

$res = $conn->query($sql);
$data = $res->fetch_assoc();

echo json_encode($data);
