<?php
// Incluir conexión a la base de datos
require_once '../db.php';

// Verificar si se enviaron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $id_profesor = $_POST['id_profesor'];
    $id_materia = $_POST['id_materia'];
    $id_curso = $_POST['id_curso'];

    // Validar que no estén vacíos
    if (!empty($id_profesor) && !empty($id_materia) && !empty($id_curso)) {
        // Consulta para insertar la asignación
        $query_asignacion = "INSERT INTO profesor_materia_curso (id_profesor, id_materia, id_curso) VALUES (?, ?, ?)";

        // Preparar la consulta
        if ($stmt = $conn->prepare($query_asignacion)) {
            // Vincular parámetros
            $stmt->bind_param("iii", $id_profesor, $id_materia, $id_curso);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Redirigir o mostrar un mensaje de éxito
                echo "Asignación registrada exitosamente.";
            } else {
                echo "Error al registrar la asignación: " . $stmt->error;
            }

            // Cerrar la declaración
            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conn->error;
        }
    } else {
        echo "Por favor, complete todos los campos.";
    }
}

// Cerrar conexión
$conn->close();
?>
