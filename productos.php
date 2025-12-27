<?php
session_start();
$id_usuario = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://kit.fontawesome.com/646ac4fad6.js" crossorigin="anonymous"></script>
</head>

<body class="bg-light">

<?php include './nav.php'; ?>

<script>
    function goBack() {
        window.history.back();
    }
</script>

<div class="container my-5 p-4 bg-white border rounded shadow-sm">
    <div class="d-flex align-items-center mb-4">
        <button class="btn btn-outline-primary me-3" onclick="goBack()">
            <i class="bi bi-caret-left-fill"></i>
        </button>
        <h2 class="m-0">Registro de Ventas</h2>
    </div>

    <hr>

    <div class="row g-4 mb-4">

        <!-- Formulario Productos -->
        <div class="col-md-6">
            <div class="card border-secondary">
                <div class="card-body">
                    <h5 class="card-title text-secondary">Agregar Producto</h5>
                    <form method="POST" action="controlador/registrarProducto.php">
                        <div class="mb-3">
                            <label class="form-label">Producto</label>
                            <select class="form-select" name="producto" required>
                                <option selected disabled>Seleccionar producto</option>
                                <?php
                                include "conexionPDC.php";
                                $sql = $conexion->query("SELECT DISTINCT nombre, cantidad_actual FROM stock ORDER BY nombre ASC");
                                while ($datos = $sql->fetch_object()) {
                                    if ($datos->cantidad_actual > 0) {
                                        echo '<option value="' . $datos->nombre . '">' . $datos->nombre . ' (' . $datos->cantidad_actual . ' disponibles)</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" class="form-control" name="cantidad" required min="1">
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Agregar producto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Formulario Venta -->
        <div class="col-md-6">
            <div class="card border-secondary">
                <div class="card-body">
                    <h5 class="card-title text-secondary">Finalizar Venta</h5>
                    <form method="POST" action="controlador/registrarVenta.php">
                        <div class="mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" name="fecha" max="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cliente</label>
                            <select class="form-select" name="cliente" required>
                                <option selected disabled>Seleccionar cliente</option>
                                <?php
                                $sql = $conexion->query("SELECT nombre FROM clientes ORDER BY nombre ASC");
                                while ($datos = $sql->fetch_object()) {
                                    echo '<option value="' . $datos->nombre . '">' . $datos->nombre . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Método de Pago</label>
                            <select class="form-select" name="metodo_pago" required>
                                <option value="Caja">Efectivo</option>
                                <option value="Banco c/c">Cheque Banco</option>
                                <option value="Deudores por ventas">Deudores</option>
                                <option value="Documentos a cobrar">Cheques de terceros</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="ventas.php" class="btn btn-outline-secondary">Cerrar</a>
                            <button type="submit" class="btn btn-primary" name="agregar" value="Ok">Agregar Venta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <hr>

    <!-- Tabla productos agregados -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-info text-center">
                <tr>
                    <th># Venta</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Precio total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_productos = $conexion->query("SELECT nro_venta, id_articulo, cantidad, precio_total FROM lineas_venta ORDER BY nro_venta ASC");
                while ($datos_venta = $sql_productos->fetch_object()) {
                    $id_articulo = $datos_venta->id_articulo;
                    $sql_stock = $conexion->query("SELECT nombre, valor_venta FROM stock WHERE id_articulo = $id_articulo");
                    $datos_stock = $sql_stock->fetch_assoc();
                    ?>
                    <tr>
                        <td class="text-center"><?= $datos_venta->nro_venta ?></td>
                        <td><?= $datos_stock['nombre'] ?></td>
                        <td class="text-center"><?= $datos_venta->cantidad ?></td>
                        <td class="text-end">$<?= $datos_stock['valor_venta'] ?></td>
                        <td class="text-end">$<?= $datos_venta->precio_total ?></td>
                        <td class="text-center">
                            <a onclick="return confirm('¿Eliminar producto?')" href="controlador/eliminarProducto.php?nro_venta=<?= $datos_venta->nro_venta ?>" class="text-danger">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Alertas -->
    <?php
    foreach (['error', 'exito', 'error_eliminacion', 'exito_eliminacion'] as $tipo) {
        if (isset($_GET[$tipo])) {
            $msg = urldecode($_GET[$tipo]);
            $class = str_starts_with($tipo, 'error') ? 'alert-warning' : 'alert-success';
            echo "<div class='alert $class mt-3'>$msg</div>";
        }
    }
    ?>

</div>

</body>

</html>
