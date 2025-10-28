<?php
// Incluir la parte superior
require_once 'vistas/parte_superior.php'; 

// Conectar a la base de datos
require_once '../db.php'; 

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener estudiantes sin curso asignado
$query_estudiantes_sin_curso = "SELECT e.*, u.nombre, u.apellido FROM estudiantes e JOIN usuarios u ON e.id_usuario = u.id WHERE e.id_curso IS NULL";
$result_estudiantes_sin_curso = $conn->query($query_estudiantes_sin_curso);

// Obtener cursos disponibles
$query_cursos = "SELECT * FROM cursos"; // Asegúrate de que la tabla cursos esté correctamente definida
$result_cursos = $conn->query($query_cursos);

// Cerrar conexión
$conn->close();
?>

<!-- Contenido principal -->
<section class="full-box dashboard-contentPage">
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="text-titles">Estudiantes Sin Curso Asignado</h1>
        </div>

        <!-- Tabla de estudiantes sin curso -->
        <?php if ($result_estudiantes_sin_curso->num_rows > 0) { ?>
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
                    <?php while ($estudiante = $result_estudiantes_sin_curso->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $estudiante['nombre'] . ' ' . $estudiante['apellido']; ?></td>
                            <td><?php echo $estudiante['ci']; ?></td>
                            <td><?php echo $estudiante['fecha_nacimiento']; ?></td>
                            <td>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#asignarCursoModal" 
                                        data-id="<?php echo $estudiante['id']; ?>" 
                                        data-nombre="<?php echo $estudiante['nombre'] . ' ' . $estudiante['apellido']; ?>">
                                    Asignar Curso
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="alert alert-warning" role="alert">
                No hay estudiantes sin curso asignado.
            </div>
        <?php } ?>
    </div>
</section>

<!-- Modal para asignar curso -->
<div class="modal fade" id="asignarCursoModal" tabindex="-1" role="dialog" aria-labelledby="asignarCursoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asignarCursoModalLabel">Asignar Curso a <span id="nombreEstudiante"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="asignar_curso_action.php"> <!-- Cambia esto a la acción correcta -->
                    <input type="hidden" name="id_estudiante" id="idEstudiante" value="">
                    <div class="form-group">
                        <label for="id_curso">Seleccionar Curso:</label>
                        <select name="id_curso" id="id_curso" class="form-control" required>
                            <option value="">Seleccione un curso</option>
                            <?php while ($curso = $result_cursos->fetch_assoc()) { ?>
                                <option value="<?php echo $curso['id']; ?>"><?php echo $curso['nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Asignar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    // Manejo de datos del estudiante al abrir el modal
    $('#asignarCursoModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Botón que activó el modal
        var idEstudiante = button.data('id'); // Extraer la ID del estudiante
        var nombreEstudiante = button.data('nombre'); // Extraer el nombre del estudiante

        // Actualizar los campos del modal
        var modal = $(this);
        modal.find('#idEstudiante').val(idEstudiante);
        modal.find('#nombreEstudiante').text(nombreEstudiante);
    });
</script>

<?php
// Incluir la parte inferior
require_once 'vistas/parte_inferior.php';
?>
