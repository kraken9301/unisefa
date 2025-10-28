<?php
// Incluir la parte superior
require_once 'vistas/parte_superior.php'; 

// Conectar a la base de datos
require_once '../db.php'; 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Borrar los datos existentes en la tabla notas (opcional, solo si deseas limpiar la tabla antes de cada carga)
// $query_borrar = "DELETE FROM notas";
// if ($conn->query($query_borrar) === TRUE) {
//     echo "Datos existentes borrados correctamente.<br>";
// } else {
//     echo "Error al borrar los datos: " . $conn->error . "<br>";
// }

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Asumiendo que el archivo CSV se envía correctamente
    $fileTmpPath = $_FILES['archivo_csv']['tmp_name'];

    if (is_uploaded_file($fileTmpPath)) {
        // Leer el archivo CSV
        $file = fopen($fileTmpPath, 'r');

        // Saltar el encabezado si existe
        fgetcsv($file, 1000, ';');

        // Obtener el ID de materia, curso y trimestre
        $id_materia = $_POST['id_materia'];
        $id_curso = $_POST['id_curso'];
        $trimestre = $_POST['trimestre'];

        // Contador de registros procesados
        $registros_procesados = 0;

        // Recorrer cada línea del archivo
        while (($data = fgetcsv($file, 1000, ';')) !== FALSE) {
            $ci = trim($data[0]); // CI del estudiante
            $nota1 = isset($data[1]) ? intval(trim($data[1])) : null;
            $nota2 = isset($data[2]) ? intval(trim($data[2])) : null;
            $nota3 = isset($data[3]) ? intval(trim($data[3])) : null;

            // Consultar el id_estudiante en la tabla de estudiantes usando el CI
            $query_estudiante = "SELECT id FROM estudiantes WHERE ci = ?";
            $stmt_estudiante = $conn->prepare($query_estudiante);
            $stmt_estudiante->bind_param("s", $ci);
            $stmt_estudiante->execute();
            $result = $stmt_estudiante->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_estudiante = $row['id'];

                // Verificar si ya existe un registro para el mismo estudiante, materia y curso
                $query_existente = "SELECT * FROM notas WHERE id_estudiante = ? AND id_materia = ? AND id_curso = ?";
                $stmt_existente = $conn->prepare($query_existente);
                $stmt_existente->bind_param("iii", $id_estudiante, $id_materia, $id_curso);
                $stmt_existente->execute();
                $result_existente = $stmt_existente->get_result();

                if ($result_existente->num_rows > 0) {
                    // Actualizar las notas existentes
                    switch ($trimestre) {
                        case "1":
                            $query_update = "UPDATE notas SET trimestre1 = ? WHERE id_estudiante = ? AND id_materia = ? AND id_curso = ?";
                            $stmt_update = $conn->prepare($query_update);
                            $stmt_update->bind_param("iiii", $nota1, $id_estudiante, $id_materia, $id_curso);
                            break;
                        case "2":
                            $query_update = "UPDATE notas SET trimestre2 = ? WHERE id_estudiante = ? AND id_materia = ? AND id_curso = ?";
                            $stmt_update = $conn->prepare($query_update);
                            $stmt_update->bind_param("iiii", $nota2, $id_estudiante, $id_materia, $id_curso);
                            break;
                        case "3":
                            $query_update = "UPDATE notas SET trimestre3 = ? WHERE id_estudiante = ? AND id_materia = ? AND id_curso = ?";
                            $stmt_update = $conn->prepare($query_update);
                            $stmt_update->bind_param("iiii", $nota3, $id_estudiante, $id_materia, $id_curso);
                            break;
                    }

                    if ($stmt_update->execute()) {
                        echo "Notas para CI $ci actualizadas correctamente.<br>";
                    } else {
                        echo "Error al actualizar las notas para CI $ci: " . $stmt_update->error . "<br>";
                    }
                } else {
                    // Insertar nuevas notas
                    $query_insert = "INSERT INTO notas (id_estudiante, id_materia, id_curso, trimestre1, trimestre2, trimestre3) 
                                     VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($query_insert);
                    $stmt_insert->bind_param("iiiiii", $id_estudiante, $id_materia, $id_curso, $nota1, $nota2, $nota3);

                    if ($stmt_insert->execute()) {
                        echo "Notas para CI $ci ingresadas correctamente.<br>";
                    } else {
                        echo "Error al insertar las notas para CI $ci: " . $stmt_insert->error . "<br>";
                    }
                }
            } else {
                echo "Estudiante con CI $ci no encontrado.<br>";
            }

            $registros_procesados++;
        }
        fclose($file);
        echo "Total de registros procesados: $registros_procesados<br>";
    } else {
        echo "Error al cargar el archivo CSV.";
    }
}

$conn->close();
?>
