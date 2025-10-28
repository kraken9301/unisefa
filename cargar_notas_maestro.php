<?php
// Incluir la parte superior


// Conectar a la base de datos
require_once 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileTmpPath = $_FILES['archivo_csv']['tmp_name'];

    if (is_uploaded_file($fileTmpPath)) {
        $file = fopen($fileTmpPath, 'r');

        // Saltar el encabezado si existe
        fgetcsv($file, 1000, ';');

        $id_materia = $_POST['id_materia'];
        $id_curso = $_POST['id_curso'];
        $trimestre = $_POST['trimestre'];

        $registros_procesados = 0;
        $procesado_exitoso = true; // Variable para verificar si todo se procesa correctamente

        while (($data = fgetcsv($file, 1000, ';')) !== FALSE) {
            $ci = trim($data[0]);
            $nota1 = isset($data[1]) ? intval(trim($data[1])) : null;
            $nota2 = isset($data[2]) ? intval(trim($data[2])) : null;
            $nota3 = isset($data[3]) ? intval(trim($data[3])) : null;

            $query_estudiante = "SELECT id FROM estudiantes WHERE ci = ?";
            $stmt_estudiante = $conn->prepare($query_estudiante);
            $stmt_estudiante->bind_param("s", $ci);
            $stmt_estudiante->execute();
            $result = $stmt_estudiante->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_estudiante = $row['id'];

                $query_existente = "SELECT * FROM notas WHERE id_estudiante = ? AND id_materia = ? AND id_curso = ?";
                $stmt_existente = $conn->prepare($query_existente);
                $stmt_existente->bind_param("iii", $id_estudiante, $id_materia, $id_curso);
                $stmt_existente->execute();
                $result_existente = $stmt_existente->get_result();

                if ($result_existente->num_rows > 0) {
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
                    if (!$stmt_update->execute()) {
                        $procesado_exitoso = false;
                        break;
                    }
                } else {
                    $query_insert = "INSERT INTO notas (id_estudiante, id_materia, id_curso, trimestre1, trimestre2, trimestre3) 
                                     VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($query_insert);
                    $stmt_insert->bind_param("iiiiii", $id_estudiante, $id_materia, $id_curso, $nota1, $nota2, $nota3);

                    if (!$stmt_insert->execute()) {
                        $procesado_exitoso = false;
                        break;
                    }
                }
            } else {
                $procesado_exitoso = false;
                break;
            }

            $registros_procesados++;
        }
        fclose($file);

        // Redirigir según el resultado del procesamiento
        if ($procesado_exitoso) {
            header("Location: maestro.php?mensaje=exito");
        } else {
            header("Location: maestro.php?mensaje=error");
        }
        exit;
    } else {
        echo "Error al cargar el archivo CSV.";
    }
}

$conn->close();
?>
