<?php
require_once 'db.php';
require_once 'fpdf/fpdf.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de pedido inválido.");
}

$idPedido = intval($_GET['id']);

if (!$conn || $conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener datos del pedido, estudiante y curso
$queryPedido = "SELECT p.id, p.fecha_solicitud, e.nombre, e.apellido, c.nombre AS curso
                FROM pedidos p
                JOIN estudiantes e ON p.id_estudiante = e.id
                JOIN cursos c ON e.id_curso = c.id
                WHERE p.id = ?";
$stmtPedido = $conn->prepare($queryPedido);
if (!$stmtPedido) {
    die("Error en la consulta de pedido: " . $conn->error);
}
$stmtPedido->bind_param("i", $idPedido);
$stmtPedido->execute();
$resultPedido = $stmtPedido->get_result();

if ($resultPedido->num_rows === 0) {
    die("Pedido no encontrado.");
}

$pedido = $resultPedido->fetch_assoc();
$nombreCompleto = $pedido['nombre'] . ' ' . $pedido['apellido'];

// Obtener detalles del pedido
$queryDetalle = "SELECT dp.*, pr.nombre, pr.precio
                 FROM detalle_pedidos dp
                 JOIN productos pr ON dp.id_producto = pr.id
                 WHERE dp.id_pedido = ?";
$stmtDetalle = $conn->prepare($queryDetalle);
if (!$stmtDetalle) {
    die("Error en la consulta de detalles: " . $conn->error);
}
$stmtDetalle->bind_param("i", $idPedido);
$stmtDetalle->execute();
$resultDetalle = $stmtDetalle->get_result();

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();

// Logo
$pdf->Image('imagenes/logo.jpeg', 10, 10, 25);

// Título del sistema
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'UNISEFA', 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode(' Sistema de Gestión de Uniformes del Colegio Josefa Manuela Gandarillas'), 0, 1, 'C');

$pdf->Ln(5);

// Encabezado del reporte
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Resumen del Pedido', 0, 1, 'C');
$pdf->Ln(5);

// Datos del pedido
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, 'Estudiante: ' . utf8_decode($nombreCompleto), 0, 1);
$pdf->Cell(0, 8, 'Curso: ' . utf8_decode($pedido['curso']), 0, 1);
$pdf->Cell(0, 8, 'Fecha: ' . date("d/m/Y", strtotime($pedido['fecha_solicitud'])), 0, 1);
$pdf->Ln(5);

// Encabezado de tabla
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(60, 8, 'Producto', 1);
$pdf->Cell(20, 8, 'Talla', 1);
$pdf->Cell(20, 8, 'Cant.', 1);
$pdf->Cell(30, 8, 'Precio', 1);
$pdf->Cell(30, 8, 'Subtotal', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 11);
$total = 0;
while ($item = $resultDetalle->fetch_assoc()) {
    $pdf->Cell(60, 8, utf8_decode($item['nombre']), 1);
    $pdf->Cell(20, 8, $item['talla'], 1);
    $pdf->Cell(20, 8, $item['cantidad'], 1);
    $pdf->Cell(30, 8, number_format($item['precio'], 2) . ' Bs', 1);
    $pdf->Cell(30, 8, number_format($item['subtotal'], 2) . ' Bs', 1);
    $pdf->Ln();
    $total += $item['subtotal'];
}

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Total: ' . number_format($total, 2) . ' Bs', 0, 1, 'R');

// Salida del PDF
$pdf->Output('I', 'pedido_' . $idPedido . '.pdf');
?>
