<?php
session_start();
require_once 'db.php';

// Verificar si se recibió el ID del detalle
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$idDetalle = intval($_GET['id']);

// Eliminar el detalle del pedido
$queryEliminar = "DELETE FROM detalle_pedidos WHERE id = ?";
$stmt = $conn->prepare($queryEliminar);
$stmt->bind_param("i", $idDetalle);

if ($stmt->execute()) {
    $_SESSION['mensaje_pedido'] = "Producto eliminado del pedido correctamente.";
} else {
    $_SESSION['mensaje_pedido'] = "Error al eliminar el producto.";
}

$conn->close();

// Redirigir de vuelta a la página de productos
header("Location: estudiante.php");
exit();
?>
