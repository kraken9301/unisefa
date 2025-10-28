<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['rol'] != 1){
    header("Location: ../index.php");
    exit;
}
if (isset($_GET['mensaje'])) {
    $mensaje = htmlspecialchars($_GET['mensaje']); // Evitar inyección
    echo "<div class='alert alert-info alert-dismissible fade show' role='alert'>
            $mensaje
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
}

// Incluir parte superior y conexión
require_once 'vistas/parte_superior.php'; 
require_once '../db.php'; // Ajusta la ruta según tu proyecto

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el CI de búsqueda
$ciBuscar = isset($_GET['ci']) ? $_GET['ci'] : '';
?>

<section class="full-box dashboard-contentPage">
    <!-- Navbar -->
    <nav class="full-box dashboard-Navbar">
        <!-- Aquí va tu navbar -->
    </nav>

    <div class="container-fluid mt-4">
        <div class="page-header">
            <h2 class="text-titles">Sistema <small>Maestros</small></h2>
        </div>

        <!-- Formulario de búsqueda y botón crear maestro -->
        <form method="get" action="profesor.php" class="form-inline mb-3">
            <div class="form-group mr-2">
                <input type="text" name="ci" class="form-control" placeholder="Buscar por CI" value="<?php echo htmlspecialchars($ciBuscar); ?>">
            </div>
            <button type="submit" class="btn btn-primary mr-2">Buscar</button>
            <a href="registrar_maestro.php" class="btn btn-success">Crear Maestro</a>
        </form>

        <?php
        // Solo ejecutar la consulta si hay búsqueda
        if (!empty($ciBuscar)) {
            $query = "
                SELECT m.id AS maestro_id, m.nombre, m.apellido, m.ci, m.especialidad, m.fecha_ingreso, u.usuario
                FROM maestros m
                JOIN usuarios u ON m.id_usuario = u.id
                WHERE m.ci LIKE ?
                ORDER BY m.nombre
            ";

            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }

            $searchTerm = "%$ciBuscar%";
            $stmt->bind_param("s", $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<table class="table table-striped table-bordered">';
                echo '<thead class="thead-dark"><tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Usuario</th>
                        <th>CI</th>
                        <th>Especialidad</th>
                        <th>Fecha de Ingreso</th>
                        <th>Acciones</th>
                      </tr></thead>';
                echo '<tbody>';
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['apellido']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['usuario']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['ci']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['especialidad']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['fecha_ingreso']) . '</td>';
                    echo '<td>
                            <a href="editar_maestro.php?id=' . $row['maestro_id'] . '" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="eliminar_maestro.php?id=' . $row['maestro_id'] . '" class="btn btn-danger btn-sm" 
                                onclick="return confirm(\'¿Estás seguro de eliminar este maestro?\');">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </a>
                          </td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<p>No se encontraron resultados para CI: <strong>' . htmlspecialchars($ciBuscar) . '</strong></p>';
            }

            $stmt->close();
        } else {
            echo '<p>Ingrese un CI para buscar un maestro.</p>';
        }

        $conn->close();
        ?>
    </div>
</section>

<?php require_once 'vistas/parte_inferior.php'; ?>
