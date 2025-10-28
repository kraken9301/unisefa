<?php
require_once '../db.php';

// Validar que se envió un ID válido
$id_maestro = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_maestro > 0) {
    // Obtener el id_usuario relacionado con el maestro
    $query_usuario = "SELECT id_usuario FROM maestros WHERE id = ?";
    if ($stmt = $conn->prepare($query_usuario)) {
        $stmt->bind_param("i", $id_maestro);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_usuario = $row['id_usuario'];

            // Eliminar el registro de la tabla maestros
            if ($stmt_delete_maestro = $conn->prepare("DELETE FROM maestros WHERE id = ?")) {
                $stmt_delete_maestro->bind_param("i", $id_maestro);
                $stmt_delete_maestro->execute();
                $stmt_delete_maestro->close();
            }

            // Eliminar el registro de la tabla usuarios
            if ($stmt_delete_usuario = $conn->prepare("DELETE FROM usuarios WHERE id = ?")) {
                $stmt_delete_usuario->bind_param("i", $id_usuario);
                $stmt_delete_usuario->execute();
                $stmt_delete_usuario->close();
            }

            $mensaje = "Maestro eliminado con éxito.";
        } else {
            $mensaje = "Maestro no encontrado.";
        }

        $stmt->close();
    } else {
        $mensaje = "Error al preparar la consulta: " . $conn->error;
    }
} else {
    $mensaje = "ID de maestro no válido.";
}

$conn->close();

// Redirigir a la lista de maestros con mensaje opcional
header("Location: profesor.php?mensaje=" . urlencode($mensaje));
exit();
?>
