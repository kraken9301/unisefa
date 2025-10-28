<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idMaestro = intval($_POST['id_maestro']);
    $idProducto = intval($_POST['id_producto']);
    $cantidad = intval($_POST['cantidad']);
    $talla = $_POST['talla'];

    // Obtener precio del producto
    $stmtProducto = $conn->prepare("SELECT precio FROM productos WHERE id = ?");
    $stmtProducto->bind_param("i", $idProducto);
    $stmtProducto->execute();
    $resultProducto = $stmtProducto->get_result();
    if ($resultProducto->num_rows === 0) {
        die("Producto no encontrado.");
    }
    $producto = $resultProducto->fetch_assoc();
    $precio = $producto['precio'];
    $subtotal = $precio * $cantidad;

    // Verificar si el maestro ya tiene un pedido
    $stmtPedido = $conn->prepare("SELECT id FROM pedidos WHERE id_maestro = ?");
    $stmtPedido->bind_param("i", $idMaestro);
    $stmtPedido->execute();
    $resultPedido = $stmtPedido->get_result();

    if ($resultPedido->num_rows > 0) {
        $pedido = $resultPedido->fetch_assoc();
        $idPedido = $pedido['id'];
    } else {
        // Crear pedido
        $fecha = date('Y-m-d');
        $stmtCrearPedido = $conn->prepare("INSERT INTO pedidos (id_maestro, fecha_solicitud) VALUES (?, ?)");
        $stmtCrearPedido->bind_param("is", $idMaestro, $fecha);
        $stmtCrearPedido->execute();
        $idPedido = $conn->insert_id;
    }

    // Verificar si ya existe el producto con la misma talla en el detalle
    $stmtDetalle = $conn->prepare("SELECT id, cantidad, subtotal FROM detalle_pedidos WHERE id_pedido = ? AND id_producto = ? AND talla = ?");
    $stmtDetalle->bind_param("iis", $idPedido, $idProducto, $talla);
    $stmtDetalle->execute();
    $resultDetalle = $stmtDetalle->get_result();

    if ($resultDetalle->num_rows > 0) {
        // Actualizar cantidad y subtotal
        $detalle = $resultDetalle->fetch_assoc();
        $nuevaCantidad = $detalle['cantidad'] + $cantidad;
        $nuevoSubtotal = $detalle['subtotal'] + $subtotal;
        $stmtActualizar = $conn->prepare("UPDATE detalle_pedidos SET cantidad = ?, subtotal = ? WHERE id = ?");
        $stmtActualizar->bind_param("idi", $nuevaCantidad, $nuevoSubtotal, $detalle['id']);
        $stmtActualizar->execute();
    } else {
        // Insertar nuevo detalle
        $stmtInsertDetalle = $conn->prepare("INSERT INTO detalle_pedidos (id_pedido, id_producto, cantidad, subtotal, talla) VALUES (?, ?, ?, ?, ?)");
        $stmtInsertDetalle->bind_param("iiids", $idPedido, $idProducto, $cantidad, $subtotal, $talla);
        $stmtInsertDetalle->execute();
    }

    $conn->close();
    $_SESSION['mensaje_pedido'] = "Producto agregado al pedido correctamente.";

    // Redirigir
    header("Location: maestro.php");
    exit();
}
?>
