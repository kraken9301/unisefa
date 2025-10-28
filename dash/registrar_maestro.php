<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['rol'] != 1){
    header("Location: ../index.php");
    exit;
}

require_once 'vistas/parte_superior.php';
require_once '../db.php';

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $ci = $_POST['ci'];
    $especialidad = $_POST['especialidad'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];

    if (empty($nombre) || empty($apellido) || empty($ci) || empty($especialidad) || empty($fecha_ingreso) || empty($usuario) || empty($contraseña)) {
        $mensaje = 'Todos los campos son obligatorios.';
    } else {
        // Insertar usuario con id_rol
        $id_rol = 2; // maestro
        $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

        $query_usuario = "INSERT INTO usuarios (usuario, contraseña, id_rol) VALUES (?, ?, ?)";
        $stmt_usuario = $conn->prepare($query_usuario);
        if (!$stmt_usuario) die("Error al preparar la consulta: " . $conn->error);

        $stmt_usuario->bind_param("ssi", $usuario, $contraseña_hash, $id_rol);

        if ($stmt_usuario->execute()) {
            $id_usuario = $stmt_usuario->insert_id;

            // Insertar maestro
            $query_maestro = "INSERT INTO maestros (nombre, apellido, ci, especialidad, fecha_ingreso, id_usuario) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_maestro = $conn->prepare($query_maestro);
            if (!$stmt_maestro) die("Error al preparar la consulta: " . $conn->error);

            $stmt_maestro->bind_param("sssssi", $nombre, $apellido, $ci, $especialidad, $fecha_ingreso, $id_usuario);

            if ($stmt_maestro->execute()) {
                $mensaje = 'Maestro registrado con éxito.';
            } else {
                $mensaje = 'Error al registrar el maestro: ' . $conn->error;
            }
            $stmt_maestro->close();
        } else {
            $mensaje = 'Error al registrar el usuario: ' . $conn->error;
        }

        $stmt_usuario->close();
    }
}
?>

<section class="full-box dashboard-contentPage">
    <div class="container-fluid mt-4">
        <div class="page-header">
            <h2 class="text-titles">Registrar Maestro</h2>
        </div>

        <?php if (!empty($mensaje)) echo "<p class='alert alert-info'>$mensaje</p>"; ?>

        <form method="post" action="registrar_maestro.php">
            <div class="form-group mb-3">
                <label>Nombre:</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label>Apellido:</label>
                <input type="text" name="apellido" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label>CI:</label>
                <input type="text" name="ci" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label>Especialidad:</label>
                <input type="text" name="especialidad" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label>Fecha de Ingreso:</label>
                <input type="date" name="fecha_ingreso" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label>Usuario:</label>
                <input type="text" name="usuario" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label>Contraseña:</label>
                <input type="password" name="contraseña" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar Maestro</button>
        </form>
    </div>
</section>

<?php require_once 'vistas/parte_inferior.php'; ?>
