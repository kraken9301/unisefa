<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION['mensaje_pedido'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
         . htmlspecialchars($_SESSION['mensaje_pedido']) .
         '<button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
         </div>';
    unset($_SESSION['mensaje_pedido']);
}

require_once 'db.php';
$usuario = $_SESSION['usuario'];

$queryUsuario = "SELECT u.id AS id_usuario, m.id AS id_maestro FROM usuarios u 
                 JOIN maestros m ON u.id = m.id_usuario WHERE u.usuario = ?";
$stmtUsuario = $conn->prepare($queryUsuario);
$stmtUsuario->bind_param("s", $usuario);
$stmtUsuario->execute();
$resultUsuario = $stmtUsuario->get_result();

if ($resultUsuario->num_rows > 0) {
    $fila = $resultUsuario->fetch_assoc();
    $idMaestro = $fila['id_maestro'];
} else {
    die("Maestro no encontrado.");
}

$queryProductos = "SELECT * FROM productos ORDER BY nombre";
$resultProductos = $conn->query($queryProductos);

$queryPedido = "SELECT * FROM pedidos WHERE id_maestro = ?";
$stmtPedido = $conn->prepare($queryPedido);
$stmtPedido->bind_param("i", $idMaestro);
$stmtPedido->execute();
$resultPedido = $stmtPedido->get_result();
$pedido = $resultPedido->fetch_assoc();
$idPedido = $pedido ? $pedido['id'] : 0;

$detallePedido = [];
$totalPedido = 0;
if ($idPedido) {
    $queryDetalle = "SELECT dp.*, p.nombre, p.precio FROM detalle_pedidos dp 
                     JOIN productos p ON dp.id_producto = p.id WHERE dp.id_pedido = ?";
    $stmtDetalle = $conn->prepare($queryDetalle);
    $stmtDetalle->bind_param("i", $idPedido);
    $stmtDetalle->execute();
    $resultDetalle = $stmtDetalle->get_result();
    while ($fila = $resultDetalle->fetch_assoc()) {
        $detallePedido[] = $fila;
        $totalPedido += $fila['subtotal'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos Maestro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
         body {
            min-height: 100vh;
            background: linear-gradient(135deg, #001f3f, #7b0000);
            font-family: 'Poppins', sans-serif;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .card-producto {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .card-producto img {
            height: 160px;
            object-fit: cover;
        }
        .card-body-producto {
            padding: 10px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-body-producto h5 {
            font-size: 1rem;
            margin-bottom: 5px;
        }
        .card-body-producto p {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        .form-inline-custom {
            display: flex;
            gap: 5px;
            align-items: center;
            margin-top: 8px;
        }
        .form-inline-custom select,
        .form-inline-custom input {
            font-size: 0.8rem;
            padding: 4px;
        }
        .btn-agregar {
            width: 100%;
            font-size: 0.85rem;
            margin-top: 8px;
        }
        .barra-superior {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="barra-superior d-flex justify-content-between align-items-center mb-4">
    <h2>Hola Maestro, <?php echo htmlspecialchars($usuario); ?></h2>
    <div>
        <button class="btn btn-success mr-2" data-toggle="modal" data-target="#modalPedido">
            <i class="fas fa-shopping-cart"></i> Ver Mi Pedido (<?php echo number_format($totalPedido,2); ?> Bs)
        </button>
        <a href="logout.php" class="btn btn-danger">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </a>
    </div>
</div>

    <div class="row">
        <?php while ($producto = $resultProductos->fetch_assoc()): ?>
        <div class="col-md-3 mb-4">
            <div class="card card-producto">
                <?php if ($producto['imagen']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" class="card-img-top" alt="Producto">
                <?php else: ?>
                    <img src="img/default.png" class="card-img-top" alt="Sin imagen">
                <?php endif; ?>
                <div class="card-body card-body-producto">
                    <h5><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <p><strong><?php echo number_format($producto['precio'],2); ?> Bs</strong></p>
                    <form method="post" action="agregar_pedido_maestro.php">
                        <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                        <input type="hidden" name="id_maestro" value="<?php echo $idMaestro; ?>">
                        <div class="form-inline-custom">
                            <select name="talla" class="form-control" required>
                                <option value="">Talla</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                            </select>
                        <input type="number" name="cantidad" class="form-control" min="1" placeholder="Cantidad" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-agregar">Agregar al Pedido</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Modal del pedido -->
    <div class="modal fade" id="modalPedido" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Mi Pedido</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <?php if ($detallePedido): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Talla</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detallePedido as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($item['talla']); ?></td>
                            <td><?php echo htmlspecialchars($item['cantidad']); ?></td>
                            <td><?php echo number_format($item['subtotal'],2); ?> Bs</td>
                            <td>
                                <a href="eliminar_detalle_mae.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Eliminar este producto del pedido?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <h5>Total: <?php echo number_format($totalPedido,2); ?> Bs</h5>
                <a href="imprimir_pedido_mae.php?id=<?php echo $idPedido; ?>" target="_blank" class="btn btn-info">Imprimir PDF</a>
            <?php else: ?>
                <p>No tienes productos en tu pedido.</p>
            <?php endif; ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
</div>

<script>
setTimeout(function() {
    let alert = document.querySelector('.alert');
    if(alert) alert.classList.remove('show');
}, 3000);
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome