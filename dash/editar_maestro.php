<?php
require_once 'vistas/parte_superior.php'; 
require_once '../db.php';

$id_maestro = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Datos del formulario
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $ci = $_POST['ci'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $especialidad = $_POST['especialidad'];

    // Validar campos
    if (empty($usuario) || empty($nombre) || empty($apellido) || empty($ci) || empty($fecha_ingreso) || empty($especialidad)) {
        $mensaje = 'Todos los campos son obligatorios.';
    } else {
        // Obtener id_usuario del maestro
        $stmt = $conn->prepare("SELECT id_usuario FROM maestros WHERE id = ?");
        $stmt->bind_param("i", $id_maestro);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_usuario = $row['id_usuario'];

            // Actualizar usuarios
            if (!empty($contraseña)) {
                $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
                $stmt_usuario = $conn->prepare("UPDATE usuarios SET usuario = ?, contraseña = ? WHERE id = ?");
                $stmt_usuario->bind_param("ssi", $usuario, $contraseña_hash, $id_usuario);
            } else {
                $stmt_usuario = $conn->prepare("UPDATE usuarios SET usuario = ? WHERE id = ?");
                $stmt_usuario->bind_param("si", $usuario, $id_usuario);
            }
            $stmt_usuario->execute();

            // Actualizar maestros
            $stmt_maestro = $conn->prepare("UPDATE maestros SET nombre = ?, apellido = ?, ci = ?, fecha_ingreso = ?, especialidad = ? WHERE id = ?");
            $stmt_maestro->bind_param("sssssi", $nombre, $apellido, $ci, $fecha_ingreso, $especialidad, $id_maestro);
            $stmt_maestro->execute();

            $mensaje = 'Maestro actualizado con éxito.';
        } else {
            $mensaje = 'Maestro no encontrado.';
        }
    }
} else {
    // Cargar datos del maestro
    $stmt = $conn->prepare("SELECT m.*, u.usuario FROM maestros m JOIN usuarios u ON m.id_usuario = u.id WHERE m.id = ?");
    $stmt->bind_param("i", $id_maestro);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $maestro = $result->fetch_assoc();
    } else {
        $mensaje = 'Maestro no encontrado.';
    }
}

$conn->close();
?>

<section class="full-box dashboard-contentPage">
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="text-titles">Editar Maestro</h1>
        </div>

        <?php if (!empty($mensaje)) { echo "<p class='alert alert-info'>$mensaje</p>"; } ?>

        <?php if (isset($maestro)) { ?>
        <form method="post" action="editar_maestro.php?id=<?php echo $id_maestro; ?>">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" class="form-control" value="<?php echo $maestro['usuario']; ?>" required>
            </div>
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" name="contraseña" id="contraseña" class="form-control" placeholder="Dejar en blanco para no cambiarla">
            </div>
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo $maestro['nombre']; ?>" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" name="apellido" id="apellido" class="form-control" value="<?php echo $maestro['apellido']; ?>" required>
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
            <button type="submit" class="btn btn-primary">Actualizar Maestro</button>
        </form>
        <?php } ?>
    </div>
</section>

<?php
require_once 'vistas/parte_inferior.php';
?>
