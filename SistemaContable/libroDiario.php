<?php
session_start();
$id_usuario = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro Diario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">

    <?php include './nav.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="card shadow-sm rounded">
            <div class="card-body">

                <div class="d-flex align-items-center mb-4">
                    <button class="btn btn-outline-secondary me-3" onclick="window.history.back();">
                        <i class="bi bi-caret-left-fill"></i>
                    </button>
                    <h2 class="m-0 text-center flex-grow-1">Libro Diario</h2>
                </div>

                <hr class="mb-4">

                <div class="table-responsive">
                    <table class="table table-bordered align-middle table-hover">
                        <thead class="table-info text-center">
                            <tr>
                                <th>N° Asiento</th>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Cuenta</th>
                                <th>Debe</th>
                                <th>Haber</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "conexionPDC.php";
                            $sql = $conexion->query("SELECT * 
                                FROM asiento a 
                                INNER JOIN registrar_asiento r ON a.id_asiento = r.id_asiento");

                            while ($datos = $sql->fetch_object()) {
                                $id_cuenta = $datos->id_cuenta;
                                $sql_cuenta = $conexion->query("SELECT nombre FROM cuenta WHERE id_cuenta = $id_cuenta");
                                $nombre_cuenta = $sql_cuenta->fetch_assoc();
                            ?>
                                <tr>
                                    <td class="text-center"><?= $datos->id_asiento ?></td>
                                    <td class="text-center"><?= $datos->fecha ?></td>
                                    <td><?= $datos->descripcion ?></td>
                                    <td><?= $nombre_cuenta["nombre"] ?></td>
                                    <td class="text-end"><?= number_format($datos->debe, 2, ',', '.') ?></td>
                                    <td class="text-end"><?= number_format($datos->haber, 2, ',', '.') ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</body>

</html>
