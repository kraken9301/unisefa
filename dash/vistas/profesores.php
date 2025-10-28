<?php
// Incluir la parte superior
require_once 'vistas/parte_superior.php'; 

// Conectar a la base de datos
require_once '../db.php'; // Asegúrate de que esta ruta sea correcta

// Obtener el CI de búsqueda
$ciBuscar = isset($_GET['ci']) ? $_GET['ci'] : '';
?>

<!-- Contenido principal -->
<section class="full-box dashboard-contentPage">
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
			  <h1 class="text-titles">System <small>Maestros</small></h1>
			</div>
		</div>
		
		<div class="container-fluid">
			<div class="page-header">
			 <h1>aqui va el contenido bb</h1>
			</div>
			


		</div>
   
</section>

<?php
// Incluir la parte inferior
require_once 'vistas/parte_inferior.php';
?>

