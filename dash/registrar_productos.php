<?php
require_once '../db.php';
ini_set('display_errors',1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $precio = (float) $_POST['precio'];
    $descripcion = trim($_POST['descripcion']);
    $imagen = "";

    // Validación básica
    if ($nombre === '' || $precio <= 0) {
        die("Nombre y precio son obligatorios y el precio debe ser mayor a 0.");
    }

    // Subir imagen si existe (validación simple)
    if (!empty($_FILES['imagen']['name'])) {
        $allowed = ['jpg','jpeg','png','gif'];
        $fileName = basename($_FILES["imagen"]["name"]);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            die("Tipo de imagen no permitido. Solo jpg, png, gif.");
        }
        if ($_FILES['imagen']['size'] > 2 * 1024 * 1024) { // 2MB
            die("La imagen es demasiado grande. Máx 2MB.");
        }

        $targetDir = "../uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imagen = time() . "_" . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $fileName);
        $targetFile = $targetDir . $imagen;

        if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $targetFile)) {
            die("Error al subir la imagen.");
        }
    }

    // Insertar en la base
    $query = "INSERT INTO productos (nombre, precio, descripcion, imagen) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error en la preparación: " . $conn->error);
    }
    $stmt->bind_param("sdss", $nombre, $precio, $descripcion, $imagen);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // Volver a productos.php
        header("Location: productos.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
