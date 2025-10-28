<?php
// Iniciar sesión
session_start();

// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "unisefa");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}

// Obtener los datos del formulario
$usuario = $conexion->real_escape_string($_POST['usuario']);
$contraseña = $_POST['contraseña']; // No hace falta escapar la contraseña antes de password_verify

// Consulta para verificar el usuario
$consulta = "SELECT * FROM usuarios WHERE usuario='$usuario'";
$resultado = $conexion->query($consulta);

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();

    // Verificar la contraseña usando password_verify
    if (password_verify($contraseña, $fila['contraseña'])) {
        // Guardar datos en sesión
        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['id_usuario'] = $fila['id'];
        $_SESSION['rol'] = $fila['id_rol'];

        // Redirigir según el rol
        switch ($fila['id_rol']) {
            case 1: // Administrador
                header("Location: dash/home.php");
                break;
            case 2: // Maestro
                header("Location: maestro.php");
                break;
            case 3: // Estudiante
                header("Location: estudiante.php");
                break;
            default:
                echo "Rol no reconocido";
        }
        exit();
    } else {
        echo "<h1 class='bad'>Contraseña incorrecta</h1>";
    }
} else {
    echo "<h1 class='bad'>Usuario no encontrado</h1>";
}

// Cerrar la conexión
$conexion->close();
?>
