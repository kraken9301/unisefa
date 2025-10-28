<?php
$conexion = new mysqli("localhost", "root", "", "rol");

if (isset($_GET['id'])) {
    $id = $conexion->real_escape_string($_GET['id']);

    // Obtener los datos actuales del usuario
    $sql = "SELECT * FROM usuarios WHERE id = $id";
    $resultado = $conexion->query($sql);
    $usuario = $resultado->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Editar Usuario</h2>
    <form action="actualizar_usuario.php" method="POST">
        <!-- Campo oculto para pasar el ID -->
        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

        <!-- Campo Nombre -->
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?php echo $usuario['nombre']; ?>" required>
        </div>

        <!-- Campo Usuario -->
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" name="usuario" class="form-control" value="<?php echo $usuario['usuario']; ?>" required>
        </div>

        <!-- Campo Contraseña -->
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" name="contrasena" class="form-control">
            <small class="form-text text-muted">Deja este campo vacío si no deseas cambiar la contraseña.</small>
        </div>

        <!-- Campo Cargo -->
        <div class="mb-3">
            <label for="id_cargo" class="form-label">Cargo</label>
            <select name="id_cargo" class="form-control" required>
                <?php
                // Obtener cargos de la tabla "cargo"
                $sql_cargos = "SELECT id, descripcion FROM cargo";
                $resultado_cargos = $conexion->query($sql_cargos);

                while ($cargo = $resultado_cargos->fetch_assoc()) {
                    $selected = ($cargo['id'] == $usuario['id_cargo']) ? "selected" : "";
                    echo "<option value='{$cargo['id']}' $selected>{$cargo['descripcion']}</option>";
                }
                ?>
            </select>
        </div>

        <!-- Botón de enviar -->
        <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
        <a href="administrador.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
