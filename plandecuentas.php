<?php
session_start();
$id_usuario = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan de Cuentas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/646ac4fad6.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .icon-btn:hover {
            color: #0d6efd;
            cursor: pointer;
        }
        .table thead {
            background-color: #0dcaf0;
            color: #fff;
        }
    </style>
</head>

<body class="bg-light">
    <?php include './nav.php' ?>

    <script>
        function eliminar() {
            return confirm("¿Estás seguro que deseas eliminar esta cuenta?");
        }
    </script>

    <?php
    include "conexionPDC.php";
    include "controlador/eliminar.php";
    ?>

    <div class="container my-5 p-4 bg-white shadow rounded">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a class="btn btn-outline-secondary" href="registrarAsiento.php">
                <i class="bi bi-caret-left-fill"></i> Volver
            </a>
            <h2 class="text-center flex-grow-1"><strong>Plan de Cuentas</strong></h2>
            <?php
            $sql_tipo_usuario = $conexion->query("SELECT rol FROM usuarios WHERE id_usuario = $id_usuario");
            $rol = $sql_tipo_usuario->fetch_array()["rol"];
            if ($rol === "admin") {
                echo '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Añadir Cuenta</button>';
            }
            ?>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title text-primary">Registrar Cuenta</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <?php
                            include "conexionPDC.php";
                            include "controlador/registrar.php";
                            ?>
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Número de Cuenta</label>
                                <input type="number" class="form-control" name="nro_cuenta" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <select class="form-select" name="tipo" required>
                                    <option value="Ac">Ac</option>
                                    <option value="Pa">Pa</option>
                                    <option value="R+">R+</option>
                                    <option value="R-">R-</option>
                                    <option value="Pm">Pm</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Recibe Saldo</label>
                                <select class="form-select" name="recibe_saldo">
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" name="registrar" value="Ok">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Nombre</th>
                        <th>Saldo</th>
                        <th>Tipo</th>
                        <?php if ($rol === "admin") echo "<th>Acciones</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = $conexion->query("SELECT id_cuenta, nombre, tipo, nro_cuenta, recibe_saldo FROM cuenta ORDER BY nro_cuenta ASC");
                    while ($datos = $sql->fetch_object()) {
                        echo "<tr>";
                        echo "<td>$datos->nro_cuenta</td>";
                        echo "<td>$datos->nombre</td>";
                        echo "<td>$datos->recibe_saldo</td>";
                        echo "<td>$datos->tipo</td>";
                        if ($rol === "admin") {
                            echo "<td>
                                <a href='modificacion.php?id=$datos->id_cuenta' class='icon-btn'><i class='fa-regular fa-pen-to-square'></i></a>
                                <a href='plandecuentas.php?id=$datos->id_cuenta' onclick='return eliminar()' class='icon-btn'><i class='fa-solid fa-trash'></i></a>
                            </td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>
