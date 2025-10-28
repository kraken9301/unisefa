<?php
require_once 'vistas/parte_superior.php'; 
require_once '../db.php';
?>

<section class="full-box dashboard-contentPage">
<div class="container-fluid">
    <div class="page-header">
        <h1 class="text-titles">Reporte de Pedidos <small>Maestros</small></h1>
         <div class="mb-3">
  
         <a href="imprimir_reporte_maestros.php" target="_blank" class="btn btn-info">
            <i class="fas fa-file-pdf"></i> Imprimir Reporte
        </a>
    </div>
    </div>

    <?php
    // Consulta para obtener pedidos de maestros con sus detalles
    $query = "
        SELECT 
            m.nombre, 
            m.apellido, 
            m.ci, 
            p.id AS pedido_id, 
            p.fecha_solicitud,
            SUM(dp.cantidad) AS total_items,
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

    if ($result->num_rows > 0) {
        echo '<table class="table table-bordered">';
        echo '<thead><tr>
                <th>Nombre</th>
                <th>CI</th>
                <th>Detalle del Pedido</th>
                <th>Total (Bs)</th>
                <th>Fecha del Pedido</th>
              </tr></thead><tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . '</td>';
            echo '<td>' . htmlspecialchars($row['ci']) . '</td>';
            echo '<td>' . htmlspecialchars($row['detalle']) . '</td>';
            echo '<td>' . number_format($row['total_pedido'], 2) . '</td>';
            echo '<td>' . date("d/m/Y", strtotime($row['fecha_solicitud'])) . '</td>';
           
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No hay pedidos registrados por maestros.</p>';
    }

    $conn->close();
    ?>
</div>
</section>

<?php
require_once 'vistas/parte_inferior.php';
?>
