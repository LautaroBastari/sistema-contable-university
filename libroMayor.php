<?php
session_start();
$id_usuario = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro Mayor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">

    <?php include './nav.php'; ?>

    <div class="container my-5">
        <div class="card shadow-sm rounded">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <button class="btn btn-outline-secondary me-3" onclick="window.history.back();">
                        <i class="bi bi-caret-left-fill"></i>
                    </button>
                    <h2 class="m-0 text-center flex-grow-1">Libro Mayor</h2>
                </div>
                <hr>

                <form action="" method="get" class="text-center mb-4">
                    <label for="busqueda" class="form-label">Seleccionar cuenta:</label>
                    <div class="d-flex justify-content-center">
                        <select class="form-select w-auto" name="busqueda" id="busqueda" required>
                            <option value="">Seleccione...</option>
                            <?php
                            include "conexionPDC.php";
                            $sql = $conexion->query("SELECT nombre, nro_cuenta, tipo, recibe_saldo FROM cuenta ORDER BY nro_cuenta ASC");
                            while ($datos = $sql->fetch_object()) {
                                if ($datos->recibe_saldo == 1 && ($datos->tipo == "Ac" || $datos->tipo == "Pa")) {
                                    echo '<option value="' . $datos->nombre . '">' . $datos->nombre . ' - N°: ' . $datos->nro_cuenta . ' - Tipo: ' . $datos->tipo . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn btn-primary ms-3" name="enviar">Buscar</button>
                    </div>
                </form>

                <?php
                if (isset($_GET['enviar']) && !empty($_GET['busqueda'])) {
                    $busqueda = $_GET['busqueda'];
                    $sql_cuenta = $conexion->query("SELECT id_cuenta, saldo_final FROM cuenta WHERE nombre LIKE '%$busqueda'");
                    if ($sql_cuenta && $sql_cuenta->num_rows > 0) {
                        $datos_cuenta = $sql_cuenta->fetch_object();
                        $id_cuenta = $datos_cuenta->id_cuenta;

                        $sql_nombre_cuenta = $conexion->query("SELECT nombre FROM cuenta WHERE id_cuenta = $id_cuenta");
                        $nombre_cuenta = $sql_nombre_cuenta->fetch_object()->nombre;

                        echo "<h5 class='text-center mb-4'><span class='text-muted'>Cuenta:</span> <strong>$nombre_cuenta</strong></h5>";

                        $sql_cruce_tablas = $conexion->query("
                            SELECT a.id_asiento, a.descripcion, r.debe, r.haber, r.saldo_parcial
                            FROM asiento a
                            INNER JOIN registrar_asiento r ON a.id_asiento = r.id_asiento
                            WHERE r.id_cuenta = $id_cuenta
                        ");

                        echo '<div class="table-responsive">';
                        echo '<table class="table table-bordered text-center align-middle">';
                        echo '<thead class="table-info">';
                        echo '<tr>';
                        echo '<th>Nro de Asiento</th>';
                        echo '<th>Descripción</th>';
                        echo '<th>Debe</th>';
                        echo '<th>Haber</th>';
                        echo '<th>Total</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        while ($datos = $sql_cruce_tablas->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$datos['id_asiento']}</td>";
                            echo "<td>{$datos['descripcion']}</td>";
                            echo "<td class='text-end'>$" . number_format($datos['debe'], 2, ',', '.') . "</td>";
                            echo "<td class='text-end'>$" . number_format($datos['haber'], 2, ',', '.') . "</td>";
                            echo "<td class='text-end'>$" . number_format($datos['saldo_parcial'], 2, ',', '.') . "</td>";
                            echo "</tr>";
                        }

                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                    } else {
                        echo "<div class='alert alert-warning text-center'>Cuenta no encontrada.</div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

</body>

</html>
