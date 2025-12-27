<?php
session_start();
$id_usuario = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Ventas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <?php include './nav.php' ?>
    <div class="container border border-primary-light pt-2 pb-2 mb-5 mt-5 bg-light text-dark">
        <div class="container d-flex mt-3">
            <button type="submit" name="button" class="btn" onclick="goBack()"><i
                    class="bi bi-caret-left-fill"></i></button>
            <script>
                function goBack() {
                    window.history.go(-1);
                }
            </script>
            <div class="container d-flex justify-content-center mt-2">
                <h1><b>Registro de Ventas</b></h1>
            </div>


            <?php
            include "conexionPDC.php";
            $sql_tipo_usuario = $conexion->query("SELECT rol FROM usuarios WHERE id_usuario = $id_usuario");
            $datos_tipo_rol = $sql_tipo_usuario->fetch_array();
            $rol = $datos_tipo_rol["rol"];
            if ($rol === "admin") {
                echo '<div class="mt-3">';
                echo '<form method="post">';
                echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">';
                echo 'Registrar Venta';
                echo '</button>';
                echo '</form>';
                echo '</div>';
            }
            ?>


            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="text-center text-secondary">Registrar Venta</h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <?php
                                include "conexionPDC.php";
                                ?>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Fecha</label>
                                    <input type="text" class="form-control" name="fecha">
                                </div>
                                <div class="mb-3">
                                    <label for="condicion" class="form-label">Cliente</label>
                                    <select class="form-control" name="cliente" id="cliente">

                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Producto</label>
                                    <select class="form-control" name="producto" id="producto">

                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" name="cantidad">
                                </div>

                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Precio</label>
                                    <input type="number" class="form-control" name="precio">
                                </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="agregar" value="Ok">Agregar factura</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <hr class="container mb-2 d-flex justify-content-center" style="width:70%"></hr>
        <div class="col-11 pt-2 container text-center">

            <table class="table table-bordered">

                <thead class="table-info">
                    <tr>
                        <th scope="col">Número</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Producto</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Coste Unitario</th>
                        <th scope="col">Coste Total</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>

            </table>
        </div>
    </div>
</body>

</html>