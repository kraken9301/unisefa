<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['rol'] != 1){
    header("Location: ../index.php");
    exit;
}

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "unisefa");
if($conexion->connect_error){
    die("Error en la conexión: ".$conexion->connect_error);
}

// Verificar que los datos llegaron desde el formulario
if(isset($_POST['usuario'], $_POST['contraseña'], $_POST['id_rol'])) {
    
    $usuario = $conexion->real_escape_string($_POST['usuario']);
    $contraseña = $conexion->real_escape_string($_POST['contraseña']);
    $id_rol = (int)$_POST['id_rol']; // 1 = admin
    
    // Encriptar la contraseña
    $hash = password_hash($contraseña, PASSWORD_DEFAULT);
    
    // Insertar en la base de datos
    $sql = "INSERT INTO usuarios (usuario, contraseña, id_rol) VALUES ('$usuario', '$hash', $id_rol)";
    
    if($conexion->query($sql)){
        // Éxito, redirigir de vuelta a admin.php
        header("Location: admin.php?mensaje=creado");
        exit;
    } else {
        echo "Error al crear usuario: " . $conexion->error;
    }
} else {
    echo "Debe completar todos los campos del formulario.";
}

$conexion->close();
?>
