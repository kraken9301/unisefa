
<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.html");
    exit();
}

// Verificar si el rol es 2 (administrador)
if ($_SESSION['rol'] != 1) {
    header("Location: ../index.html");
    exit();
}
?>


<?php require_once "vistas/parte_superior.php" ?>



<section class="full-box dashboard-contentPage">
		<!-- NavBar -->
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
		<!-- Content page -->
		<div class="container-fluid">
			<div class="page-header">
			  <h1 class="text-titles">Sistema <small></small>de registro</h1>
			</div>
		</div>
		
		<div class="container-fluid">
			<div class="page-header">
			 <h1>En esta pagina usted puede registrar, modificar y eliminar cuentas de usuarios, administradores, maestros, estudiantes ademas de agregar productos y ver reportes de pedidos por usuario, curso y global</h1>
			</div>
			


		</div>
        <div class="container-fluid">
			<div class="page-header">
            <a href="../logout.php" class="btn btn-danger">Cerrar Sesión</a>
			</div>
			


		</div>
        
	</section>

<?php require_once "vistas/parte_inferior.php" ?>