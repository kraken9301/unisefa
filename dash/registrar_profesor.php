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

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombre_usuario = $_POST['nombre_usuario'];
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    $ci = $_POST['ci'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $especialidad = $_POST['especialidad']; // Nuevo campo
    $numero_titulo = $_POST['numero_titulo']; // Nuevo campo

    // Validar campos
    if (empty($nombre_usuario) || empty($usuario) || empty($contraseña) || empty($ci) || empty($fecha_ingreso) || empty($especialidad) || empty($numero_titulo)) {
        $mensaje = 'Todos los campos son obligatorios.';
    } else {
        // Insertar usuario en la base de datos
        $query_usuario = "INSERT INTO usuarios (nombre, usuario, contraseña, id_cargo) VALUES (?, ?, ?, ?)";
        $stmt_usuario = $conn->prepare($query_usuario);

        // Verifica si la preparación fue exitosa
        if (!$stmt_usuario) {
            die("Error al preparar la consulta: " . $conn->error);
        }

        $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT); // Seguridad en la contraseña
        $id_cargo = 2; // Establecer el cargo de maestro
        $stmt_usuario->bind_param("sssi", $nombre_usuario, $usuario, $contraseña_hash, $id_cargo);

        if ($stmt_usuario->execute()) {
            $id_usuario = $stmt_usuario->insert_id; // Obtener el ID del usuario insertado

            // Insertar maestro en la base de datos
            $query_profesor = "INSERT INTO maestros (ci, fecha_ingreso, especialidad, numero_titulo, id_usuario) VALUES (?, ?, ?, ?, ?)";
            $stmt_profesor = $conn->prepare($query_profesor);

            // Verifica si la preparación fue exitosa
            if (!$stmt_profesor) {
                die("Error al preparar la consulta: " . $conn->error);
            }

            $stmt_profesor->bind_param("ssssi", $ci, $fecha_ingreso, $especialidad, $numero_titulo, $id_usuario);

            if ($stmt_profesor->execute()) {
                $mensaje = 'Profesor registrado con éxito.';
            } else {
                $mensaje = 'Error al registrar el profesor: ' . $conn->error;
            }
            $stmt_profesor->close();
        } else {
            $mensaje = 'Error al registrar el usuario: ' . $conn->error;
        }
        $stmt_usuario->close();
    }
}
?>

<!-- Contenido principal -->
<section class="full-box dashboard-contentPage">
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="text-titles">Registrar Profesor y Usuario</h1>
        </div>

        <!-- Mensaje de confirmación o error -->
        <?php if (!empty($mensaje)) { echo "<p class='alert alert-info'>$mensaje</p>"; } ?>

        <!-- Formulario de registro -->
        <form method="post" action="registrar_profesor.php">
            <div class="form-group">
                <label for="nombre_usuario">Nombre:</label>
                <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" required>
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
                <label for="ci">CI:</label>
                <input type="text" name="ci" id="ci" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="fecha_ingreso">Fecha de Ingreso:</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="especialidad">Especialidad:</label>
                <input type="text" name="especialidad" id="especialidad" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="numero_titulo">Número de Título:</label>
                <input type="text" name="numero_titulo" id="numero_titulo" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar Profesor</button>
        </form>
    </div>
</section>

<?php
// Incluir la parte inferior
require_once 'vistas/parte_inferior.php';
?>
