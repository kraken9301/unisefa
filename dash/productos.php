<?php
require_once 'vistas/parte_superior.php'; 
require_once '../db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Obtener productos
$query_productos = "SELECT * FROM productos ORDER BY id DESC";
$result_productos = $conn->query($query_productos);
?>

<section class="full-box dashboard-contentPage">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h1 class="text-titles">Gestión de Productos</h1>
            <button class="btn btn-success" data-toggle="modal" data-target="#modalAgregarProducto">
                <i class="fas fa-plus"></i> Agregar Producto
            </button>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_productos && $result_productos->num_rows > 0) { 
                        while ($producto = $result_productos->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td style="max-width:400px;"><?php
                                    $desc = trim($producto['descripcion']);
                                    echo htmlspecialchars(strlen($desc) > 120 ? mb_substr($desc,0,120).'…' : $desc);
                                ?></td>
                                <td><?php echo number_format($producto['precio'], 2); ?> Bs</td>
                                <td>
                                    <?php if (!empty($producto['imagen'])) { ?>
                                        <img src="../uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" width="60" height="60" class="img-thumbnail">
                                    <?php } else { ?>
                                        <span class="text-muted">Sin imagen</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <button 
                                        class="btn btn-warning btn-sm btnEditarProducto" 
                                        data-toggle="modal" 
                                        data-target="#modalEditarProducto"
                                        data-id="<?php echo $producto['id']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                        data-descripcion="<?php echo htmlspecialchars($producto['descripcion']); ?>"
                                        data-precio="<?php echo $producto['precio']; ?>"
                                    >
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres eliminar este producto?');">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php } 
                    } else { ?>
                        <tr><td colspan="5" class="text-center">No hay productos registrados.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Modal para agregar producto -->
<div class="modal fade" id="modalAgregarProducto" tabindex="-1" role="dialog" aria-labelledby="modalAgregarProductoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="registrar_productos.php" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAgregarProductoLabel">Agregar Producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="nombre">Nombre del Producto</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label for="precio">Precio (Bs)</label>
            <input type="number" step="0.01" name="precio" id="precio" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="imagen">Imagen</label>
            <input type="file" name="imagen" id="imagen" class="form-control" accept="image/*">
            <small class="text-muted">(jpg, png; max 2MB recomendado)</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar Producto</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal para editar producto -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1" role="dialog" aria-labelledby="modalEditarProductoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="editar_productos.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" id="editar_id">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarProductoLabel">Editar Producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="editar_nombre">Nombre del Producto</label>
            <input type="text" name="nombre" id="editar_nombre" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editar_descripcion">Descripción</label>
            <textarea name="descripcion" id="editar_descripcion" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label for="editar_precio">Precio (Bs)</label>
            <input type="number" step="0.01" name="precio" id="editar_precio" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editar_imagen">Imagen (opcional)</label>
            <input type="file" name="imagen" id="editar_imagen" class="form-control" accept="image/*">
            <small class="text-muted">(jpg, png; max 2MB recomendado)</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Actualizar Producto</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const botonesEditar = document.querySelectorAll('.btnEditarProducto');
    botonesEditar.forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('editar_id').value = this.dataset.id;
            document.getElementById('editar_nombre').value = this.dataset.nombre;
            document.getElementById('editar_descripcion').value = this.dataset.descripcion;
            document.getElementById('editar_precio').value = this.dataset.precio;
        });
    });
});
</script>

<?php
$conn->close();
require_once 'vistas/parte_inferior.php';
?>
