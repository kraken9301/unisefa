<?php
// eliminar_producto.php

include '../db.php'; // archivo donde configuras la conexión a la BD

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitizar el id recibido

    // Preparar consulta
    $sql = "DELETE FROM productos WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Producto eliminado correctamente'); window.location='productos.php';</script>";
    } else {
        echo "Error al eliminar el producto: " . $conn->error;
    }
} else {
    echo "ID no especificado.";
}

$conn->close();
?>
