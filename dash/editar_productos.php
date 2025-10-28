<?php
require_once '../db.php';

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];
$imagen = $_FILES['imagen']['name'];

// Procesar imagen si se sube una nueva
if (!empty($imagen)) {
    $ruta = '../uploads/' . basename($imagen);
    move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
    $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, imagen=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $imagen, $id);
} else {
    $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $id);
}

if ($stmt->execute()) {
    header("Location: productos.php");
} else {
    echo "Error al actualizar el producto.";
}

$conn->close();
?>
