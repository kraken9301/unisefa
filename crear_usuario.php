<?php
$conexion = new mysqli("localhost", "root", "", "rol");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $usuario = $conexion->real_escape_string($_POST['usuario']);
    $contrasena = password_hash($_POST['contraseña'], PASSWORD_DEFAULT); // Encriptar la contraseña
    $id_cargo = $conexion->real_escape_string($_POST['id_cargo']); // ID numérico del cargo

    $sql = "INSERT INTO usuarios (id,nombre, usuario, contraseña, id_cargo) 
            VALUES (null,'$nombre', '$usuario', '$contrasena', '$id_cargo')";

    if ($conexion->query($sql)) {
        echo "Usuario creado correctamente.";
    } else {
        echo "Error: " . $conexion->error;
    }

    $conexion->close();
    header('Location: administrador.php');
}
?>

