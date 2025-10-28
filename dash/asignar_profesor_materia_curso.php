<!-- asignar_profesor_materia_curso.php -->
<?php
// Incluir la parte superior
require_once 'vistas/parte_superior.php'; 

// Conectar a la base de datos
require_once '../db.php'; 

// Obtener profesores, materias y cursos para los selectores
$query_profesores = "
    SELECT m.*, u.nombre, u.apellido 
    FROM maestros m 
    JOIN usuarios u ON m.id_usuario = u.id
";
$result_profesores = $conn->query($query_profesores);


$query_materias = "SELECT * FROM materias";
$result_materias = $conn->query($query_materias);

$query_cursos = "SELECT * FROM cursos";
$result_cursos = $conn->query($query_cursos);

// Cerrar conexión
$conn->close();
?>

<section class="full-box dashboard-contentPage">
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="text-titles">Asignar Profesor a Materia y Curso</h1>
        </div>

        <form method="post" action="asignar_action.php">
        <div class="form-group">
    <label for="id_profesor">Seleccionar Profesor:</label>
    <select name="id_profesor" id="id_profesor" class="form-control" required>
        <option value="">Seleccione un profesor</option>
        <?php while ($profesor = $result_profesores->fetch_assoc()) { ?>
            <option value="<?php echo $profesor['id']; ?>">
                <?php echo $profesor['nombre'] . ' ' . $profesor['apellido']; ?>
            </option>
        <?php } ?>
    </select>
</div>


            <div class="form-group">
                <label for="id_materia">Seleccionar Materia:</label>
                <select name="id_materia" id="id_materia" class="form-control" required>
                    <option value="">Seleccione una materia</option>
                    <?php while ($materia = $result_materias->fetch_assoc()) { ?>
                        <option value="<?php echo $materia['id']; ?>"><?php echo $materia['nombre']; ?></option>
                    <?php } ?>
                </select>
            </div>

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
</section>

<?php
// Incluir la parte inferior
require_once 'vistas/parte_inferior.php';
?>
