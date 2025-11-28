<?php
try
{
$host = "localhost";
$usuario = "root";
$password = "";
$basedatos = "gls_jur_bil";
 
$cn = mysqli_connect($host,$usuario,$password,$basedatos);
 
return($cn);
 
// Verificar la conexión
if (!$cn) {
    die("Error de conexión: " . mysqli_connect_error());
}
 
}catch(Exepcion $e)
{
	echo "Error en Db".$e;
}

//codigo para mostrar caracteres especiales como la 'ñ'
$cn->set_charset("utf8mb4");

// Retornar la conexión
return $cn;
?>