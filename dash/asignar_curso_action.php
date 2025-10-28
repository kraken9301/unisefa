<?php
// Incluir la conexión a la base de datos
require_once '../db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_estudiante = $_POST['id_estudiante'];
    $id_curso = $_POST['id_curso'];

    if (!empty($id_estudiante) && !empty($id_curso)) {
        // Prepara y ejecuta la actualización del curso
        $stmt = $conn->prepare("UPDATE estudiantes SET id_curso = ? WHERE id = ?");
        $stmt->bind_param("ii", $id_curso, $id_estudiante);

        if ($stmt->execute()) {
            // Redirigir con éxito
            header("Location: sin_cursos.php?mensaje=asignado");
        } else {
            // Mostrar mensaje de error
            echo "Error al asignar curso.";
        }
    } else {
        echo "Datos incompletos.";
    }
    
    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>
