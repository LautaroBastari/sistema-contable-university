<?php
session_start();
$id_usuario = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asientos Contables</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/646ac4fad6.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <?php include './nav.php' ?>

    <div class="container py-5 my-5 bg-light text-dark rounded shadow">
        <div class="d-flex align-items-center mb-4">
            <button class="btn btn-outline-secondary me-3" onclick="history.back()">
                <i class="bi bi-caret-left-fill"></i> Volver
            </button>
            <h1 class="m-0 text-center w-100"><strong>Asientos Contables</strong></h1>
        </div>

        <hr class="my-4" style="width: 70%; margin: 0 auto;">

        <?php if (isset($_GET['success']) && $_GET['success'] === 'asiento_registrado'): ?>
            <div class="alert alert-success text-center">Registro efectuado correctamente.</div>
        <?php endif; ?>

        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="from_date" class="form-label fw-bold">Del Día</label>
                    <input type="date" id="from_date" name="from_date" value="<?= $_GET['from_date'] ?? '' ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="to_date" class="form-label fw-bold">Hasta el Día</label>
                    <input type="date" id="to_date" name="to_date" value="<?= $_GET['to_date'] ?? '' ?>" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-info">Filtrar</button>
                        <button type="submit" class="btn btn-secondary" name="clear">Limpiar filtro</button>
                    </div>
                </div>
            </div>
        </form>

        <hr class="my-4" style="width: 70%; margin: 0 auto;">

        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-info">
                    <tr>
                        <th>Fecha</th>
                        <th>Nro Asiento</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include "conexionPDC.php";

                    if (isset($_GET['clear'])) {
                        $query = "SELECT id_asiento, fecha, descripcion FROM asiento WHERE id_usuario = $id_usuario AND (id_asiento, fecha) IN (SELECT id_asiento, MIN(fecha) FROM asiento GROUP BY id_asiento) ORDER BY fecha;";
                    } elseif (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
                        $fechadesde = $_GET['from_date'];
                        $fechahasta = $_GET['to_date'];
                        $query = "SELECT id_asiento, fecha, descripcion FROM asiento WHERE id_usuario = $id_usuario AND fecha BETWEEN '$fechadesde' AND '$fechahasta' AND (id_asiento, fecha) IN (SELECT id_asiento, MIN(fecha) FROM asiento GROUP BY id_asiento) ORDER BY fecha;";
                    } else {
                        $query = "SELECT id_asiento, fecha, descripcion FROM asiento WHERE id_usuario = $id_usuario AND (id_asiento, fecha) IN (SELECT id_asiento, MIN(fecha) FROM asiento GROUP BY id_asiento) ORDER BY fecha;";
                    }

                    $sql = $conexion->query($query);

                    if ($sql) {
                        while ($datos = $sql->fetch_object()) {
                            echo "<tr>
                                    <td>$datos->fecha</td>
                                    <td>$datos->id_asiento</td>
                                    <td>$datos->descripcion</td>
                                    <td><a href='./verAsiento.php?id=$datos->id_asiento' class='btn btn-sm btn-outline-primary'><i class='bi bi-eye-fill'></i> Ver</a></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Error en la consulta: {$conexion->error}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a class="btn btn-primary" href="registrarAsiento.php">Registrar nuevo asiento</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>
