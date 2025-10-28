<?php
$host = "localhost";
$usuario = "root";
$contraseña = "";
$base_datos = "unisefa";

// Crear la conexión
$conn = new mysqli($host, $usuario, $contraseña, $base_datos);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Mensaje de depuración

?>
