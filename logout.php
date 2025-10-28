<?php
// Iniciar la sesión
session_start();

// Destruir todas las sesiones
session_unset(); // Limpia todas las variables de sesión
session_destroy(); // Destruye la sesión

// Redirigir al usuario al login (index.html)
header("Location: index.html");
exit();
?>
