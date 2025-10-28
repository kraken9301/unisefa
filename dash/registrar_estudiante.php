<?php
require_once 'vistas/parte_superior.php'; 
require_once '../db.php';

$mensaje = '';

// Obtener cursos disponibles
$cursos = [];
$query_cursos = "SELECT id, nombre FROM cursos ORDER BY id";
$result_cursos = $conn->query($query_cursos);
if ($result_cursos) {
    while ($row = $result_cursos->fetch_assoc()) {
        $cursos[] = $row;
    }
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $ci = $_POST['ci'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    $id_curso = $_POST['id_curso'];

    if (empty($nombre) || empty($apellido) || empty($ci) || empty($fecha_nacimiento) || empty($usuario) || empty($contraseña) || empty($id_curso)) {
        $mensaje = 'Todos los campos son obligatorios.';
    } else {
        // Insertar usuario
        $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
        $id_rol = 3; // Estudiante
        $stmt_usuario = $conn->prepare("INSERT INTO usuarios (usuario, contraseña, id_rol) VALUES (?, ?, ?)");
        $stmt_usuario->bind_param("ssi", $usuario, $contraseña_hash, $id_rol);

        if ($stmt_usuario->execute()) {
            $id_usuario = $stmt_usuario->insert_id;

            // Insertar estudiante
            $stmt_estudiante = $conn->prepare("INSERT INTO estudiantes (nombre, apellido, ci, fecha_nacimiento, id_usuario, id_curso) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_estudiante->bind_param("ssssii", $nombre, $apellido, $ci, $fecha_nacimiento, $id_usuario, $id_curso);

            if ($stmt_estudiante->execute()) {
                $mensaje = 'Estudiante registrado con éxito.';
            } else {
                $mensaje = 'Error al registrar estudiante: ' . $conn->error;
            }
            $stmt_estudiante->close();
        } else {
            $mensaje = 'Error al registrar usuario: ' . $conn->error;
        }
        $stmt_usuario->close();
    }
}
?>

<section class="full-box dashboard-contentPage">
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="text-titles">Registrar Estudiante y Usuario</h1>
        </div>

        <?php if (!empty($mensaje)) { echo "<p class='alert alert-info'>$mensaje</p>"; } ?>

        <form method="post" action="registrar_estudiante.php">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" name="apellido" id="apellido" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="ci">CI:</label>
                <input type="text" name="ci" id="ci" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" name="contraseña" id="contraseña" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="id_curso">Curso:</label>
                <select name="id_curso" id="id_curso" class="form-control" required>
                    <option value="">Seleccione un curso</option>
                    <?php foreach($cursos as $curso): ?>
                        <option value="<?php echo $curso['id']; ?>"><?php echo htmlspecialchars($curso['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Registrar Estudiante</button>
        </form>
    </div>
</section>

<?php
require_once 'vistas/parte_inferior.php';
?>
