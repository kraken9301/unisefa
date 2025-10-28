<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['rol'] != 1){
    header("Location: ../index.php");
    exit;
}

// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "unisefa");
if($conexion->connect_error){
    die("Error en la conexión: ".$conexion->connect_error);
}
?>

<?php require_once "vistas/parte_superior.php" ?>

<section class="full-box dashboard-contentPage">

    <!-- Navbar -->
    <nav class="full-box dashboard-Navbar">  
        <ul class="full-box list-unstyled text-right">
            <li class="pull-left">
                <a href="#!" class="btn-menu-dashboard"><i class="zmdi zmdi-more-vert"></i></a>
            </li>
            <li>
                <a href="#!" class="btn-Notifications-area">
                    <i class="zmdi zmdi-notifications-none"></i>
                    <span class="badge">7</span>
                </a>
            </li>
            <li>
                <a href="#!" class="btn-search">
                    <i class="zmdi zmdi-search"></i>
                </a>
            </li>
            <li>
                <a href="#!" class="btn-modal-help">
                    <i class="zmdi zmdi-help-outline"></i>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Contenido principal -->
    <div class="container-fluid mt-4">
        <h2 class="mb-4">Gestión de Administradores</h2>
        <div class="row">
            <!-- Tabla de administradores -->
            <div class="col-lg-7 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white h4">
                        Administradores Registrados
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT id, usuario FROM usuarios WHERE id_rol = 1"; // solo admins
                                $resultado = $conexion->query($sql);

                                while ($fila = $resultado->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$fila['id']}</td>
                                        <td>{$fila['usuario']}</td>
                                        <td>
                                            <a href='editar_usuario.php?id={$fila['id']}' class='btn btn-warning btn-sm'>Editar</a>
                                            <a href='eliminar_usuario.php?id={$fila['id']}' class='btn btn-danger btn-sm'>Eliminar</a>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Formulario para crear admin -->
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white h4">
                        Crear Nuevo Administrador
                    </div>
                    <div class="card-body">
                        <form action="crear_usuario.php" method="POST">
                            <div class="form-group mb-3">
                                <label for="usuario">Usuario:</label>
                                <input type="text" name="usuario" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="contraseña">Contraseña:</label>
                                <input type="password" name="contraseña" class="form-control" required>
                            </div>
                            <input type="hidden" name="id_rol" value="1">
                            <button type="submit" class="btn btn-primary">Crear Admin</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<?php
require_once 'vistas/parte_inferior.php';
?>
