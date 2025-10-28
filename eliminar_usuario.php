<?php
$conexion = new mysqli("localhost", "root", "", "rol");

if (isset($_GET['id'])) {
    $id = $conexion->real_escape_string($_GET['id']);

    // Consulta SQL para eliminar el usuario
    $sql = "DELETE FROM usuarios WHERE id = $id";

    if ($conexion->query($sql)) {
        echo "Usuario eliminado correctamente.";
    } else {
        echo "Error: " . $conexion->error;
    }

    $conexion->close();
    header('Location: administrador.php'); // Redirige a la página del administrador
}
?>
