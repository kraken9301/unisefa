<?php
// Incluir la parte superior
require_once 'vistas/parte_superior.php'; 

// Conectar a la base de datos
require_once '../db.php'; 

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inicializar variables
$cursos = [];

// Obtener los cursos
$query_cursos = "SELECT * FROM cursos";
$result_cursos = $conn->query($query_cursos);

if ($result_cursos->num_rows > 0) {
    while ($row = $result_cursos->fetch_assoc()) {
        $cursos[] = $row;
    }
}

// Verificar si se seleccionó un curso
$curso_seleccionado = isset($_POST['curso']) ? $_POST['curso'] : '';

// Obtener estudiantes del curso seleccionado
$estudiantes = [];
if (!empty($curso_seleccionado)) {
    $query_estudiantes = "SELECT e.*, u.nombre, u.apellido FROM estudiantes e JOIN usuarios u ON e.id_usuario = u.id WHERE e.id_curso = ?";
    $stmt_estudiantes = $conn->prepare($query_estudiantes);
    $stmt_estudiantes->bind_param("i", $curso_seleccionado);
    $stmt_estudiantes->execute();
    $result_estudiantes = $stmt_estudiantes->get_result();

    if ($result_estudiantes->num_rows > 0) {
        while ($row = $result_estudiantes->fetch_assoc()) {
            $estudiantes[] = $row;
        }
    }
    $stmt_estudiantes->close();
}

$conn->close();
?>
<!-- Contenido principal -->
<section class="full-box dashboard-contentPage">
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="text-titles">Cursos</h1>
        </div>
        <a href="sin_cursos.php" class="btn btn-info">Ver Estudiantes Sin Curso</a>
        <!-- Formulario de selección de curso -->
        <form method="post" action="">
            <div class="form-group">
                <label for="curso">Seleccionar Curso:</label>
                <select name="curso" id="curso" class="form-control" required>
                    <option value="">-- Seleccionar Curso --</option>
                    <?php foreach ($cursos as $curso) { ?>
                        <option value="<?php echo $curso['id']; ?>"><?php echo $curso['grado'] . ' ' . $curso['paralelo']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Mostrar Estudiantes</button>
        </form>

        <!-- Tabla de estudiantes -->
        <?php if (!empty($estudiantes)) { ?>
            <h2>Estudiantes en el Curso <?php echo $curso_seleccionado; ?></h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>CI</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudiantes as $estudiante) { ?>
                        <tr>
                            <td><?php echo $estudiante['nombre'] . ' ' . $estudiante['apellido']; ?></td>
                            <td><?php echo $estudiante['ci']; ?></td>
                            <td><?php echo $estudiante['fecha_nacimiento']; ?></td>
                            <td>
                                <a href="cambiar_paralelo.php?id=<?php echo $estudiante['id']; ?>" class="btn btn-warning">Cambiar Paralelo</a>
                                <a href="promover_estudiante.php?id=<?php echo $estudiante['id']; ?>" class="btn btn-success">Promover</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</section>

<?php
// Incluir la parte inferior
require_once 'vistas/parte_inferior.php';
?>
