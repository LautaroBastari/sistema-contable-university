<?php
session_start();
$id_usuario = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administración de Clientes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">
  <?php include './nav.php'; ?>

  <div class="container my-5 p-4 bg-white border rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <button class="btn btn-outline-primary" onclick="window.history.back();">
          <i class="bi bi-caret-left-fill"></i>
        </button>
      </div>
      <h2 class="text-center flex-grow-1">Administración de Clientes</h2>
      <div>
        <?php
        include "conexionPDC.php";
        $rol = $conexion->query("SELECT rol FROM usuarios WHERE id_usuario = $id_usuario")
                        ->fetch_array()['rol'];
        if ($rol === "admin") {
          echo '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarCliente">Añadir Cliente</button>';
        }
        ?>
      </div>
    </div>

    <hr>

    <!-- Modal Agregar Cliente -->
    <div class="modal fade" id="modalAgregarCliente" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Registro de Cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <form method="POST">
            <div class="modal-body">
              <?php
              include "controlador/registrarCliente.php";
              ?>

              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label">Nombre</label>
                  <input type="text" class="form-control" name="nombre" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Código de Cliente</label>
                  <input type="number" class="form-control" name="codigo" required>
                </div>
              </div>

              <div class="row g-2 mt-2">
                <div class="col-md-6">
                  <label class="form-label">DNI</label>
                  <input type="number" class="form-control" name="dni" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">CUIT</label>
                  <input type="number" class="form-control" name="cuit" required>
                </div>
              </div>

              <div class="mt-2">
                <label class="form-label">Condición Fiscal</label>
                <select class="form-select" name="cond_fiscal" required>
                  <option value="Responsable Inscripto">Responsable Inscripto</option>
                  <option value="Monotributista">Monotributista</option>
                  <option value="Exento al IVA">Exento al IVA</option>
                  <option value="Consumidor Final">Consumidor Final</option>
                </select>
              </div>

              <div class="mt-2">
                <label class="form-label">Dirección</label>
                <input type="text" class="form-control" name="direccion">
              </div>

              <div class="row g-2 mt-2">
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" name="email">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Teléfono</label>
                  <input type="number" class="form-control" name="telefono">
                </div>
              </div>

              <div class="mt-2">
                <label class="form-label">Saldo Inicial</label>
                <input type="number" class="form-control" name="saldo">
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary" name="agregar" value="Ok">Agregar Cliente</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Tabla Clientes -->
    <div class="table-responsive mt-4">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-info text-center">
          <tr>
            <th>Nombre</th>
            <th>Código</th>
            <th>DNI</th>
            <th>CUIT</th>
            <th>Cond. Fiscal</th>
            <th>Dirección</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Saldo</th>
            <th>Límite Crédito</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = $conexion->query("SELECT * FROM clientes");
          while ($cliente = $sql->fetch_object()) {
            echo "<tr class='text-center'>";
            echo "<td>$cliente->nombre</td>";
            echo "<td>$cliente->codigo</td>";
            echo "<td>$cliente->dni</td>";
            echo "<td>$cliente->cuit</td>";
            echo "<td>$cliente->cond_fiscal</td>";
            echo "<td>$cliente->direccion</td>";
            echo "<td>$cliente->email</td>";
            echo "<td>$cliente->telefono</td>";
            echo "<td>$$cliente->saldo</td>";
            echo "<td>$$cliente->limite</td>";
            echo "</tr>";
          }
          ?>
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
