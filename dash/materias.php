<?php
// Incluir la parte superior
require_once 'vistas/parte_superior.php'; 

// Conectar a la base de datos
require_once '../db.php'; 

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener asignaciones de la base de datos (profesor-materia-curso)
$query_asignaciones = "
    SELECT a.id, 
           CONCAT(u.nombre, ' ', u.apellido) AS profesor, 
           m.nombre AS materia, 
           c.nombre AS curso 
    FROM profesor_materia_curso a 
    JOIN maestros p ON a.id_profesor = p.id  
    JOIN usuarios u ON p.id_usuario = u.id 
    JOIN materias m ON a.id_materia = m.id
    JOIN cursos c ON a.id_curso = c.id
";

$result_asignaciones = $conn->query($query_asignaciones);

// Verificar si hay un error en la consulta
if (!$result_asignaciones) {
    die("Error en la consulta: " . $conn->error);
}
?>

<section class="full-box dashboard-contentPage">
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="text-titles">Verificar Asignaciones</h1>
        </div>

        <!-- Botón para ir a la página de asignación -->
        <a href="asignar_profesor_materia_curso.php" class="btn btn-success">Asignar Profesor a Materia y Curso</a>

        <!-- Tabla de asignaciones -->
        <?php if ($result_asignaciones->num_rows > 0) { ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Profesor</th>
                        <th>Materia</th>
                        <th>Curso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($asignacion = $result_asignaciones->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $asignacion['profesor']; ?></td>
                            <td><?php echo $asignacion['materia']; ?></td>
                            <td><?php echo $asignacion['curso']; ?></td>
                            <td>
                                <a href="editar_asignacion.php?id=<?php echo $asignacion['id']; ?>" class="btn btn-warning">Editar</a>
                                <a href="eliminar_asignacion.php?id=<?php echo $asignacion['id']; ?>" class="btn btn-danger">Eliminar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="alert alert-warning" role="alert">
                No hay asignaciones registradas.
            </div>
        <?php } ?>
    </div>
</section>

<?php
// Cerrar conexión
$conn->close();
require_once 'vistas/parte_inferior.php';
?>
