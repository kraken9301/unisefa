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
$mensaje = '';

// Obtener el ID del maestro desde la URL
$id_maestro = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombre_usuario = $_POST['nombre_usuario'];
    $apellido_usuario = $_POST['apellido_usuario'];
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    $ci = $_POST['ci'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $especialidad = $_POST['especialidad'];
    $numero_titulo = $_POST['numero_titulo'];

    // Validar campos
    if (empty($nombre_usuario) || empty($apellido_usuario) || empty($usuario) || empty($contraseña) || empty($ci) || empty($fecha_ingreso) || empty($especialidad) || empty($numero_titulo)) {
        $mensaje = 'Todos los campos son obligatorios.';
    } else {
        // Consultar el ID de usuario asociado
        $query_id_usuario = "SELECT id_usuario FROM maestros WHERE id = ?";
        $stmt_id_usuario = $conn->prepare($query_id_usuario);
        $stmt_id_usuario->bind_param("i", $id_maestro);
        $stmt_id_usuario->execute();
        $result = $stmt_id_usuario->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_usuario = $row['id_usuario'];

            // Actualizar usuario
            $query_update_usuario = "UPDATE usuarios SET nombre = ?, apellido = ?, usuario = ?, contraseña = ? WHERE id = ?";
            $stmt_update_usuario = $conn->prepare($query_update_usuario);
            $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT); // Seguridad en la contraseña
            $stmt_update_usuario->bind_param("ssssi", $nombre_usuario, $apellido_usuario, $usuario, $contraseña_hash, $id_usuario);
            $stmt_update_usuario->execute();

            // Actualizar maestro
            $query_update_maestro = "UPDATE maestros SET ci = ?, fecha_ingreso = ?, especialidad = ?, numero_titulo = ? WHERE id = ?";
            $stmt_update_maestro = $conn->prepare($query_update_maestro);
            $stmt_update_maestro->bind_param("ssssi", $ci, $fecha_ingreso, $especialidad, $numero_titulo, $id_maestro);
            $stmt_update_maestro->execute();

            $mensaje = 'Profesor actualizado con éxito.';
        } else {
            $mensaje = 'Profesor no encontrado.';
        }

        $stmt_id_usuario->close();
    }
} else {
    // Cargar datos del profesor para pre-rellenar el formulario
    $query_maestro = "SELECT m.*, u.nombre AS nombre_usuario, u.apellido AS apellido_usuario, u.usuario, u.contraseña FROM maestros m JOIN usuarios u ON m.id_usuario = u.id WHERE m.id = ?";
    $stmt_maestro = $conn->prepare($query_maestro);
    $stmt_maestro->bind_param("i", $id_maestro);
    $stmt_maestro->execute();
    $result = $stmt_maestro->get_result();

    if ($result->num_rows > 0) {
        $maestro = $result->fetch_assoc();
    } else {
        $mensaje = 'Profesor no encontrado.';
    }
    $stmt_maestro->close();
}

$conn->close();
?>

<!-- Contenido principal -->
<section class="full-box dashboard-contentPage">
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="text-titles">Editar Profesor</h1>
        </div>

        <!-- Mensaje de confirmación o error -->
        <?php if (!empty($mensaje)) { echo "<p class='alert alert-info'>$mensaje</p>"; } ?>

        <!-- Formulario de edición -->
        <?php if (isset($maestro)) { ?>
        <form method="post" action="editar_profesor.php?id=<?php echo $id_maestro; ?>">
            <div class="form-group">
                <label for="nombre_usuario">Nombre:</label>
                <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" value="<?php echo $maestro['nombre_usuario']; ?>" required>
            </div>
            <div class="form-group">
                <label for="apellido_usuario">Apellido:</label>
                <input type="text" name="apellido_usuario" id="apellido_usuario" class="form-control" value="<?php echo $maestro['apellido_usuario']; ?>" required>
            </div>
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" class="form-control" value="<?php echo $maestro['usuario']; ?>" required>
            </div>
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" name="contraseña" id="contraseña" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="ci">CI:</label>
                <input type="text" name="ci" id="ci" class="form-control" value="<?php echo $maestro['ci']; ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_ingreso">Fecha de Ingreso:</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" value="<?php echo $maestro['fecha_ingreso']; ?>" required>
            </div>
            <div class="form-group">
                <label for="especialidad">Especialidad:</label>
                <input type="text" name="especialidad" id="especialidad" class="form-control" value="<?php echo $maestro['especialidad']; ?>" required>
            </div>
            <div class="form-group">
                <label for="numero_titulo">Número de Título:</label>
                <input type="text" name="numero_titulo" id="numero_titulo" class="form-control" value="<?php echo $maestro['numero_titulo']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Profesor</button>
        </form>
        <?php } ?>
    </div>
</section>

<?php
// Incluir la parte inferior
require_once 'vistas/parte_inferior.php';
?>
