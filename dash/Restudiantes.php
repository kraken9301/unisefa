<?php
require_once 'vistas/parte_superior.php'; 
require_once '../db.php';
?>

<section class="full-box dashboard-contentPage">
<div class="container-fluid">
    <div class="page-header">
        <h1 class="text-titles">Reporte de Pedidos <small>Estudiantes por Curso</small></h1>
    </div>

    <!-- Formulario de búsqueda -->
    <form method="GET" class="mb-4">
        <div class="form-group row">
            <label for="curso" class="col-sm-2 col-form-label">Seleccionar Curso:</label>
            <div class="col-sm-4">
                <select name="curso" id="curso" class="form-control" required>
                    <option value="">-- Seleccione un curso --</option>
                    <?php
                    // Traer cursos desde la BD
                    $cursos = $conn->query("SELECT id, nombre FROM cursos ORDER BY nombre ASC");
                    while ($curso = $cursos->fetch_assoc()) {
                        $selected = (isset($_GET['curso']) && $_GET['curso'] == $curso['id']) ? 'selected' : '';
                        echo "<option value='{$curso['id']}' $selected>{$curso['nombre']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-sm-6">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <?php
                if (isset($_GET['curso']) && !empty($_GET['curso'])) {
                    // Botón para imprimir reporte solo del curso seleccionado
                    $curso_id = intval($_GET['curso']);
                    echo ' <a href="imprimir_reporte_curso.php?curso=' . $curso_id . '" target="_blank" class="btn btn-info">
                            <i class="fas fa-file-pdf"></i> Reporte Curso
                          </a>';
                }
                ?>
                <!-- Botón reporte general -->
                <a href="imprimir_reporte_general.php" target="_blank" class="btn btn-success">
                    <i class="fas fa-file-pdf"></i> Reporte General
                </a>
            </div>
        </div>
    </form>

    <?php
    if (isset($_GET['curso']) && !empty($_GET['curso'])) {
        $curso_id = intval($_GET['curso']);

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

        if (!$result) {
            die("<div class='alert alert-danger'>Error en la consulta: " . $conn->error . "</div>");
        }

        if ($result->num_rows > 0) {
            echo '<table class="table table-bordered">';
            echo '<thead><tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>CI</th>
                    <th>Detalle del Pedido</th>
                    <th>Total (Bs)</th>
                    <th>Fecha del Pedido</th>
                  </tr></thead><tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                echo '<td>' . htmlspecialchars($row['apellido']) . '</td>';
                echo '<td>' . htmlspecialchars($row['ci']) . '</td>';
                echo '<td>' . htmlspecialchars($row['detalle']) . '</td>';
                echo '<td>' . number_format($row['total_pedido'], 2) . '</td>';
                echo '<td>' . date("d/m/Y", strtotime($row['fecha_solicitud'])) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-warning">No hay pedidos registrados para este curso.</div>';
        }
    }
    ?>
</div>
</section>

<?php
require_once 'vistas/parte_inferior.php';
?>
