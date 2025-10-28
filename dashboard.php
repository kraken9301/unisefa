<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="cargarContenido('usuarios.php')">Gestionar Usuarios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="cargarContenido('alumnos.php')">Gestionar Alumnos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="cargarContenido('notas.php')">Registrar Notas</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div id="contenido-dinamico">
                    <!-- Aquí se cargará el contenido dinámico -->
                    <h2>Bienvenido al Dashboard</h2>
                    <p>Seleccione una opción del menú para empezar.</p>
                </div>
            </main>
        </div>
    </div>

    <script>
        function cargarContenido(url) {
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                document.getElementById("contenido-dinamico").innerHTML = this.responseText;
            }
            xhttp.open("GET", url, true);
            xhttp.send();
        }
    </script>
</body>
</html>
