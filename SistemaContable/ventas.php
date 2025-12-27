<?php
session_start();
$id_usuario = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Ventas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/646ac4fad6.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <?php include './nav.php'; ?>
    <?php include 'conexionPDC.php'; ?>

    <div class="container my-5">
        <div class="card shadow rounded">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    
                    <button class="btn btn-outline-secondary" onclick="window.history.back();">
                        <i class="bi bi-caret-left-fill"></i>
                    </button>
                    <h2 class="text-center flex-grow-1 m-0">Registro de Ventas</h2>

                    <?php
                    $sql_tipo_usuario = $conexion->query("SELECT rol FROM usuarios WHERE id_usuario = $id_usuario");
                    $rol = $sql_tipo_usuario->fetch_array()['rol'];

                    if ($rol === "admin") {
                        echo '<a class="btn btn-primary" href="productos.php">Registrar venta</a>';
                    }
                    ?>
                </div>

                <hr class="mb-4">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered text-center align-middle">
                        <thead class="table-info">
                            <tr>
                                <th>Número</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Producto</th>
                                <th>Coste Total</th>
                                <th>Factura</th>
                                <th class="text-success">Nota Débito</th>
                                <th class="text-danger">Nota Crédito</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $consulta_venta = $conexion->query("SELECT id_venta, fecha, cliente, producto, coste_total FROM venta");
                            $last_id_venta = null;

                            while ($datos = $consulta_venta->fetch_assoc()) {
                                $numero_venta = $datos["id_venta"];
                                $fecha = $datos["fecha"];
                                $cliente = $datos["cliente"];
                                $producto = $datos["producto"];
                                $coste_total = $datos["coste_total"];
                                $iva = $coste_total * 0.21;
                                $total_con_iva = $coste_total + $iva;
                                ?>
                                <tr>
                                    <td><?= $numero_venta ?></td>
                                    <td><?= $fecha ?></td>
                                    <td><?= ($last_id_venta !== $numero_venta) ? $cliente : '' ?></td>
                                    <td><?= $producto ?></td>
                                    <td>$<?= number_format($total_con_iva, 2, ',', '.') ?></td>
                                    <td>
                                        <?php if ($last_id_venta !== $numero_venta): ?>
                                            <a href="facturaA.php?id=<?= $numero_venta ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($last_id_venta !== $numero_venta): ?>
                                            <a class="btn btn-success btn-sm" href="notaDebito.php?id_venta=<?= $numero_venta ?>">D</a>
                                            <a class="btn btn-warning btn-sm" href="debito.php?id_venta=<?= $numero_venta ?>">
                                                <i class="bi bi-card-text"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($last_id_venta !== $numero_venta): ?>
                                            <a class="btn btn-danger btn-sm" href="notaCredito.php?id_venta=<?= $numero_venta ?>">C</a>
                                            <a class="btn btn-warning btn-sm" href="credito.php?id_venta=<?= $numero_venta ?>">
                                                <i class="bi bi-card-text"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                                $last_id_venta = $numero_venta;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <?php
                if (isset($_GET['error'])) {
                    echo "<div class='alert alert-warning mt-3'>" . urldecode($_GET['error']) . "</div>";
                }
                if (isset($_GET['exito'])) {
                    echo "<div class='alert alert-success mt-3'>" . urldecode($_GET['exito']) . "</div>";
                }
                if (isset($_GET['creditos_vacios'])) {
                    echo "<div class='alert alert-warning alert-dismissible fade show mt-3' role='alert'>
                            No se encontraron registros en la nota de crédito.
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                          </div>";
                }
                if (isset($_GET['debitos_vacios'])) {
                    echo "<div class='alert alert-warning alert-dismissible fade show mt-3' role='alert'>
                            No se encontraron registros en la nota de débito.
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                          </div>";
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>
