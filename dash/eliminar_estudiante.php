<?php
// Incluir la parte superior
require_once 'vistas/parte_superior.php'; 

// Conectar a la base de datos
require_once '../db.php'; 

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del estudiante desde la URL
$id_estudiante = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_estudiante > 0) {
    // Consultar el ID de usuario asociado
    $query_id_usuario = "SELECT id_usuario FROM estudiantes WHERE id = ?";
    $stmt_id_usuario = $conn->prepare($query_id_usuario);
    $stmt_id_usuario->bind_param("i", $id_estudiante);
    $stmt_id_usuario->execute();
    $result = $stmt_id_usuario->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_usuario = $row['id_usuario'];

        // Eliminar el estudiante
        $query_delete_estudiante = "DELETE FROM estudiantes WHERE id = ?";
        $stmt_delete_estudiante = $conn->prepare($query_delete_estudiante);
        $stmt_delete_estudiante->bind_param("i", $id_estudiante);
        $stmt_delete_estudiante->execute();

        // Eliminar el usuario
        $query_delete_usuario = "DELETE FROM usuarios WHERE id = ?";
        $stmt_delete_usuario = $conn->prepare($query_delete_usuario);
        $stmt_delete_usuario->bind_param("i", $id_usuario);
        $stmt_delete_usuario->execute();
        
        $mensaje = 'Estudiante eliminado con éxito.';
    } else {
        $mensaje = 'Estudiante no encontrado.';
    }

    $stmt_id_usuario->close();
    $conn->close();
} else {
    $mensaje = 'ID inválido.';
}
?>

<!-- Mensaje de confirmación -->
<section class="full-box dashboard-contentPage">
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="text-titles">Eliminar Estudiante</h1>
        </div>
        <?php if (!empty($mensaje)) { echo "<p class='alert alert-info'>$mensaje</p>"; } ?>
    </div>
</section>

<?php
// Incluir la parte inferior
require_once 'vistas/parte_inferior.php';
?>
