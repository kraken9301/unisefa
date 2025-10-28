<?php
require_once '../db.php';
require('../fpdf/fpdf.php'); // Asegúrate de tener FPDF en tu proyecto

$pdf = new FPDF();
$pdf->AddPage();

// Logo y título general
$pdf->Image('../imagenes/logo.jpeg', 10, 10, 25);
$pdf->SetFont('Arial','B',16);
$pdf->SetY(15);
$pdf->Cell(0,10,"Reporte General de Pedidos - Todos los Cursos",0,1,'C');
$pdf->Ln(15);

// Traer todos los cursos que tengan pedidos
$queryCursos = "
    SELECT DISTINCT c.id, c.nombre
    FROM cursos c
    JOIN estudiantes e ON e.id_curso = c.id
    JOIN pedidos p ON p.id_estudiante = e.id
    JOIN detalle_pedidos dp ON dp.id_pedido = p.id
";

$cursos = $conn->query($queryCursos);

if ($cursos->num_rows > 0) {
    while ($curso = $cursos->fetch_assoc()) {
        $curso_id = $curso['id'];
        $curso_nombre = $curso['nombre'];

        // Título del curso
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0,10,"Curso: $curso_nombre",0,1,'L');

        // Encabezado de tabla
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(35,10,'Nombre',1);
        $pdf->Cell(35,10,'Apellido',1);
        $pdf->Cell(25,10,'CI',1);
        $pdf->Cell(50,10,'Detalle',1);
        $pdf->Cell(25,10,'Total',1);
        $pdf->Cell(25,10,'Fecha',1);
        $pdf->Ln();

        // Pedidos del curso
        $queryPedidos = "
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

        $resultPedidos = $conn->query($queryPedidos);

        $pdf->SetFont('Arial','',12);
        if ($resultPedidos->num_rows > 0) {
            while ($row = $resultPedidos->fetch_assoc()) {
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

        $pdf->Ln(10); // espacio antes del siguiente curso
    }
} else {
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(0,10,'No hay pedidos registrados en ningún curso.',0,1,'C');
}

$pdf->Output();
