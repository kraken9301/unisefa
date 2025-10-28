<?php
require_once '../db.php';
require('../fpdf/fpdf.php'); // Asegúrate de tener FPDF en tu proyecto

if (!isset($_GET['curso'])) {
    die("Debe seleccionar un curso.");
}

$curso_id = intval($_GET['curso']);

// Traer el nombre del curso
$cursoRes = $conn->query("SELECT nombre FROM cursos WHERE id = $curso_id");
if ($cursoRes->num_rows == 0) die("Curso no encontrado.");
$curso = $cursoRes->fetch_assoc()['nombre'];

// Traer los pedidos del curso
$query = "
    SELECT 
        e.nombre,
        e.apellido,
        e.ci,
        p.id AS pedido_id,
        p.fecha_solicitud,
        GROUP_CONCAT(CONCAT(dp.cantidad, ' ', pr.nombre) SEPARATOR ', ') AS detalle,
        SUM(dp.subtotal) AS total_pedido
    FROM pedidos p
    JOIN estudiantes e ON p.id_estudiante = e.id
    JOIN detalle_pedidos dp ON p.id = dp.id_pedido
    JOIN productos pr ON dp.id_producto = pr.id
    WHERE e.id_curso = $curso_id
    GROUP BY p.id
    ORDER BY p.fecha_solicitud DESC
";

$result = $conn->query($query);

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();

// Logo en la esquina superior izquierda
$pdf->Image('../imagenes/logo.jpeg', 10, 10, 25);

// Título centrado
$pdf->SetFont('Arial','B',16);
$pdf->SetY(15); // Altura desde arriba para el título
$pdf->Cell(0,10,"Reporte de Pedidos - Curso $curso",0,1,'C');

// Dejar un espacio debajo del logo y el título antes de la tabla
$pdf->Ln(15);

// Encabezado de tabla
$pdf->SetFont('Arial','B',12);
$pdf->Cell(35,10,'Nombre',1);
$pdf->Cell(35,10,'Apellido',1);
$pdf->Cell(25,10,'CI',1);
$pdf->Cell(50,10,'Detalle',1);
$pdf->Cell(25,10,'Total',1);
$pdf->Cell(25,10,'Fecha',1);
$pdf->Ln();

// Datos
$pdf->SetFont('Arial','',12);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(35,8,$row['nombre'],1);
        $pdf->Cell(35,8,$row['apellido'],1);
        $pdf->Cell(25,8,$row['ci'],1);
        $pdf->Cell(50,8,$row['detalle'],1);
        $pdf->Cell(25,8,number_format($row['total_pedido'],2),1);
        $pdf->Cell(25,8,date("d/m/Y", strtotime($row['fecha_solicitud'])),1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(195,8,'No hay pedidos para este curso.',1,1,'C');
}

$pdf->Output();
