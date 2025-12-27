<?php
session_start();
$id_usuario = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Administración de Stock</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
  <?php include './nav.php'; include 'controlador/registrarStock.php'; ?>

  <div class="container bg-light p-4 my-5 border rounded shadow-sm">
    <div class="d-flex align-items-center mb-4">
      <button class="btn btn-outline-secondary me-3" onclick="window.history.back();">
        <i class="bi bi-caret-left-fill"></i>
      </button>
      <h2 class="mb-0">Administración de Stock</h2>
    </div>

    <?php
    include "conexionPDC.php";
    $sql_tipo_usuario = $conexion->query("SELECT rol FROM usuarios WHERE id_usuario = $id_usuario");
    $rol = $sql_tipo_usuario->fetch_array()["rol"];
    if ($rol === "admin"): ?>
      <div class="text-end mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
          <i class="bi bi-plus-circle me-1"></i> Añadir Stock
        </button>
      </div>
    <?php endif; ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST">
            <div class="modal-header">
              <h5 class="modal-title">Agregar Artículo al Stock</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <?php
              include "conexionPDC.php";
              include "controlador/registrar.php";
              $fecha_actual = date("Y-m-d");
              ?>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nombre</label>
                  <input type="text" class="form-control" name="nombre" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Fecha</label>
                  <input type="date" class="form-control" name="fecha" max="<?= $fecha_actual ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Rubro</label>
                  <select class="form-select" name="rubro" required>
                    <option disabled selected>Seleccione una opción</option>
                    <option value="Maquinaria electrica">Maquinaria eléctrica</option>
                    <option value="Hilos y cables">Hilos y cables</option>
                    <option value="Pilas y baterias">Pilas y baterías</option>
                    <option value="Equipo de iluminacion">Equipo de iluminación</option>
                    <option value="Articulos de iluminación">Artículos de iluminación</option>
                    <option value="Equipos eléctricos">Equipos eléctricos</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Cantidad actual</label>
                  <input type="number" class="form-control" name="cantidad_act" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Cantidad máxima</label>
                  <input type="number" class="form-control" name="cantidad_max" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Valor unitario</label>
                  <input type="number" class="form-control" name="valor_unitario" step="0.01" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Valor de venta</label>
                  <input type="number" class="form-control" name="valor_venta" step="0.01" required>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" name="registrar" value="Ok" class="btn btn-primary">Guardar artículo</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <hr class="my-4">

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-info text-center">
          <tr>
            <th>Nro Artículo</th>
            <th>Nombre</th>
            <th>Fecha</th>
            <th>Rubro</th>
            <th>Cant. Actual</th>
            <th>Cant. Máxima</th>
            <th>Valor Unitario</th>
            <th>Valor Venta</th>
            <th>Valor Inventario</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = $conexion->query("SELECT * FROM stock ORDER BY id_articulo ASC");
          while ($datos = $sql->fetch_object()): ?>
            <tr>
              <td><?= $datos->id_articulo ?></td>
              <td><?= $datos->nombre ?></td>
              <td><?= $datos->fecha ?></td>
              <td><?= $datos->rubro ?></td>
              <td><?= $datos->cantidad_actual ?></td>
              <td><?= $datos->cantidad_maxima ?></td>
              <td>$<?= number_format($datos->valor_unitario, 2) ?></td>
              <td>$<?= number_format($datos->valor_venta, 2) ?></td>
              <td>$<?= number_format($datos->valor_inventario, 2) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
  crossorigin="anonymous"></script>
</body>

</html>
