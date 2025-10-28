<?php
$contraseña = "est123"; // la contraseña que quieres
$hash = password_hash($contraseña, PASSWORD_DEFAULT);

echo $hash;
?>
