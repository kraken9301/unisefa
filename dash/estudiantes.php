<?php
require_once 'vistas/parte_superior.php'; 
require_once '../db.php';

$ciBuscar = isset($_GET['ci']) ? trim($_GET['ci']) : '';
?>

<section class="full-box dashboard-contentPage">
<div class="container-fluid">
    <div class="page-header">
        <h1 class="text-titles">Sistema de registro <small>estudiantes</small></h1>
    </div>

    <form method="get" action="estudiantes.php" class="form-inline mb-3">
        <div class="form-group mr-2">
            <input type="text" name="ci" class="form-control" placeholder="Buscar por CI" value="<?php echo htmlspecialchars($ciBuscar); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
        <a href="registrar_estudiante.php" class="btn btn-success ml-2">Crear Estudiante</a>
    </form>

    <?php
    if (!empty($ciBuscar)) {
        // Ejecutar búsqueda solo si se ingresó un CI
        $query = "
            SELECT e.id AS estudiante_id, e.nombre, e.apellido, e.ci, e.fecha_nacimiento, c.nombre AS curso
            FROM estudiantes e
            LEFT JOIN cursos c ON e.id_curso = c.id
            WHERE e.ci LIKE ?
            ORDER BY e.nombre
        ";
        $stmt = $conn->prepare($query);
        $searchTerm = "%$ciBuscar%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<table class="table table-striped">';
            echo '<thead><tr><th>Nombre</th><th>Apellido</th><th>CI</th><th>Fecha de Nacimiento</th><th>Curso</th><th>Acciones</th></tr></thead><tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                echo '<td>' . htmlspecialchars($row['apellido']) . '</td>';
                echo '<td>' . htmlspecialchars($row['ci']) . '</td>';
                echo '<td>' . htmlspecialchars($row['fecha_nacimiento']) . '</td>';
                echo '<td>' . htmlspecialchars($row['curso']) . '</td>';
                echo '<td>
                        <a href="editar_estudiante.php?id=' . htmlspecialchars($row['estudiante_id']) . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a> 
                        <a href="eliminar_estudiante.php?id=' . htmlspecialchars($row['estudiante_id']) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'¿Estás seguro de que quieres eliminar este estudiante?\');"><i class="fas fa-trash-alt"></i> Eliminar</a>
                      </td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>No se encontraron resultados para CI: ' . htmlspecialchars($ciBuscar) . '</p>';
        }

        $stmt->close();
    } else {
        // No mostrar nada si no hay búsqueda
        echo '<p>Ingrese un CI y presione "Buscar" para mostrar resultados.</p>';
    }

    $conn->close();
    ?>
</div>
</section>

<?php
require_once 'vistas/parte_inferior.php';
?>
