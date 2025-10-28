<?php
require_once '../db.php';
require_once '../fpdf/fpdf.php';

// Consulta para obtener pedidos de maestros con sus detalles
$query = "
    SELECT 
        m.nombre, 
        m.apellido, 
        m.ci, 
        p.fecha_solicitud,
        GROUP_CONCAT(CONCAT(dp.cantidad, ' ', pr.nombre) SEPARATOR ', ') AS detalle,
        SUM(dp.subtotal) AS total_pedido
    FROM pedidos p
    JOIN maestros m ON p.id_maestro = m.id
    JOIN detalle_pedidos dp ON p.id = dp.id_pedido
    JOIN productos pr ON dp.id_producto = pr.id
    WHERE p.id_maestro IS NOT NULL
    GROUP BY p.id
    ORDER BY p.fecha_solicitud DESC
";

$result = $conn->query($query);

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();

// Logo
$pdf->Image('../imagenes/logo.jpeg', 10, 10, 25);

// Título
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'UNISEFA', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode('Reporte de Pedidos Realizados por Maestros'), 0, 1, 'C');
$pdf->Ln(10);

// Encabezado de tabla
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(50, 8, 'Nombre', 1);
$pdf->Cell(30, 8, 'CI', 1);
$pdf->Cell(60, 8, 'Detalle', 1);
$pdf->Cell(25, 8, 'Total (Bs)', 1);
$pdf->Cell(25, 8, 'Fecha', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

while ($row = $result->fetch_assoc()) {
    $nombre = utf8_decode($row['nombre'] . ' ' . $row['apellido']);
    $ci = utf8_decode($row['ci']);
    $detalle = utf8_decode($row['detalle']);
    $total = number_format($row['total_pedido'], 2);
    $fecha = date("d/m/Y", strtotime($row['fecha_solicitud']));

    $pdf->Cell(50, 8, $nombre, 1);
    $pdf->Cell(30, 8, $ci, 1);
    $pdf->Cell(60, 8, $detalle, 1);
    $pdf->Cell(25, 8, $total, 1);
    $pdf->Cell(25, 8, $fecha, 1);
    $pdf->Ln();
}

$pdf->Output('I', 'reporte_pedidos_maestros.pdf');
?>
