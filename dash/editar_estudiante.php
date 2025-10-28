<?php
require_once 'vistas/parte_superior.php'; 
require_once '../db.php';

$mensaje = '';
$id_estudiante = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener cursos disponibles
$cursos = [];
$query_cursos = "SELECT id, nombre FROM cursos ORDER BY id";
$result_cursos = $conn->query($query_cursos);
if ($result_cursos) {
    while ($row = $result_cursos->fetch_assoc()) {
        $cursos[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    $ci = $_POST['ci'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $id_curso = $_POST['id_curso'];

    if (empty($nombre) || empty($apellido) || empty($usuario) || empty($ci) || empty($fecha_nacimiento) || empty($id_curso)) {
        $mensaje = 'Todos los campos son obligatorios (excepto la contraseña si no deseas cambiarla).';
    } else {
        // Obtener el id_usuario
        $stmt_id_usuario = $conn->prepare("SELECT id_usuario FROM estudiantes WHERE id = ?");
        $stmt_id_usuario->bind_param("i", $id_estudiante);
        $stmt_id_usuario->execute();
        $result = $stmt_id_usuario->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_usuario = $row['id_usuario'];

            // Actualizar usuario
            if (!empty($contraseña)) {
                $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
                $stmt_usuario = $conn->prepare("UPDATE usuarios SET usuario = ?, contraseña = ? WHERE id = ?");
                $stmt_usuario->bind_param("ssi", $usuario, $contraseña_hash, $id_usuario);
            } else {
                $stmt_usuario = $conn->prepare("UPDATE usuarios SET usuario = ? WHERE id = ?");
                $stmt_usuario->bind_param("si", $usuario, $id_usuario);
            }
            $stmt_usuario->execute();
            $stmt_usuario->close();

            // Actualizar estudiante
            $stmt_estudiante = $conn->prepare("UPDATE estudiantes SET nombre = ?, apellido = ?, ci = ?, fecha_nacimiento = ?, id_curso = ? WHERE id = ?");
            $stmt_estudiante->bind_param("ssssii", $nombre, $apellido, $ci, $fecha_nacimiento, $id_curso, $id_estudiante);
            $stmt_estudiante->execute();
            $stmt_estudiante->close();

            $mensaje = 'Estudiante actualizado con éxito.';
        } else {
            $mensaje = 'Estudiante no encontrado.';
        }

        $stmt_id_usuario->close();
    }
} else {
    // Cargar datos del estudiante para pre-rellenar el formulario
    $stmt = $conn->prepare("SELECT e.*, u.usuario FROM estudiantes e JOIN usuarios u ON e.id_usuario = u.id WHERE e.id = ?");
    $stmt->bind_param("i", $id_estudiante);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $estudiante = $result->fetch_assoc();
    } else {
        $mensaje = 'Estudiante no encontrado.';
    }
    $stmt->close();
}

$conn->close();
?>

<section class="full-box dashboard-contentPage">
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="text-titles">Editar Estudiante</h1>
        </div>

        <?php if (!empty($mensaje)) { echo "<p class='alert alert-info'>$mensaje</p>"; } ?>

        <?php if (isset($estudiante)) { ?>
        <form method="post" action="editar_estudiante.php?id=<?php echo $id_estudiante; ?>">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo htmlspecialchars($estudiante['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" name="apellido" id="apellido" class="form-control" value="<?php echo htmlspecialchars($estudiante['apellido']); ?>" required>
            </div>
            <div class="form-group">
                <label for="ci">CI:</label>
                <input type="text" name="ci" id="ci" class="form-control" value="<?php echo htmlspecialchars($estudiante['ci']); ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" value="<?php echo $estudiante['fecha_nacimiento']; ?>" required>
            </div>
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" class="form-control" value="<?php echo htmlspecialchars($estudiante['usuario']); ?>" required>
            </div>
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" name="contraseña" id="contraseña" class="form-control" placeholder="Dejar en blanco para no cambiarla">
            </div>
            <div class="form-group">
                <label for="id_curso">Curso:</label>
                <select name="id_curso" id="id_curso" class="form-control" required>
                    <option value="">Seleccione un curso</option>
                    <?php foreach($cursos as $curso): ?>
                        <option value="<?php echo $curso['id']; ?>" <?php if($curso['id']==$estudiante['id_curso']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($curso['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Estudiante</button>
        </form>
        <?php } ?>
    </div>
</section>

<?php
require_once 'vistas/parte_inferior.php';
?>
