<?php
session_start();
$id_usuario = $_SESSION['user_id'];
?>

<html>

<head>
    <title>Registro de asiento.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/646ac4fad6.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</head>

<body>
    <?php include './nav.php' ?>
    <div id="incluirNav"></div>
    <?php
    include "conexionPDC.php";
    ?>

    <div class="container border border-primary-light pt-2 pb-2 mb-5 mt-5 bg-light text-dark">
        <div class="container d-flex mt-3">
            <a type="submit" name="button" class="btn" href="asientos.php"><i class="bi bi-caret-left-fill"></i></a>
            <script>
                function goBack() {
                    window.history.go(-1);
                }
            </script>
            <div class="container d-flex justify-content-center mt-2">
                <h1><b>Registro de Asientos Contables</b></h1>
            </div>
        </div>
        <hr class="container mb-5 d-flex justify-content-center" style="width:70%">
        </hr>

        <div class="row d-flex justify-content-between">
            <div class="col-7 container d-flex border border-secondary pt-2">
            
                <div>
                    <input type="hidden" name="id" value="Ok">
                    <form action="controlador/registrarLinea.php" method="post">
                        <div class="mb-3">
                            <label for="tipo" class="form-label"><b>Seleccionar cuenta</b></label>
                            <select class="form-control" name="cuenta" id="cuenta">
                                <option value="cuenta">
                                    <?php
                                    include "conexionPDC.php";
                                    $sql = $conexion->query("SELECT nombre, nro_cuenta,recibe_saldo from cuenta ORDER BY nro_cuenta ASC");
                                    while ($datos = $sql->fetch_object()) {
                                        if ($datos->recibe_saldo == 1) {
                                            echo '<option value="' . $datos->nombre . '">' . $datos->nombre . '  > -Numero de cuenta: ' . $datos->nro_cuenta . '</option>';
                                            $nombre_cuenta2 = $datos->nombre;
                                        }
                                    }
                                    ?>
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label"><b>Monto:</b></label>
                            <input type="number" class="form-control" name="monto">
                            <?php
                            $monto = "monto";
                            ?>
                            <small>Atencion, aqui solo pueden ir numeros positivos.</small>

                        </div>
                        <div class="input-group mb-3 d-flex justify-content-center">
                            <div class="input-group-append">
                                <div class="form-check form-check-inline">
                                    <input type="radio" name="accion" value="inputDebe">
                                    Debe
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" name="accion" value="inputHaber">
                                    Haber
                                </div>
                            </div>
                        </div>
                </div>

                <?php
                include "controlador/registrarLinea.php";
                include "controlador/registrarAsiento.php";
                include "conexionPDC.php";
                $sql = $conexion->query("select id_cuenta, nombre, tipo, nro_cuenta, recibe_saldo from cuenta");
                while ($datos = $sql->fetch_object())
                ?>
                <br>
                <div class="col-4 text-center ms-5 pt-4">
                    <form method="post" action="controlador/registrarLinea.php">
                        <input type="hidden" name="id" value="Ok">
                        <button type="submit" class="btn btn-primary ms-3 mb-3" name="submit" value="Ok">Añadir
                            asiento</button>
                    </form>

                    <?php
                    include "conexionPDC.php";
                    $sql_tipo_usuario = $conexion->query("SELECT rol FROM usuarios WHERE id_usuario = $id_usuario");
                    $datos_tipo_rol = $sql_tipo_usuario->fetch_array();
                    $rol = $datos_tipo_rol["rol"];
                    if ($rol === "admin") {
                        // Mostrar el botón "Añadir Cuenta" solo si el rol no es "cliente"
                        echo '<form method="post" action="registrar.php">';
                        echo '<button type="button" class="btn btn-success ms-3" data-bs-toggle="modal" data-bs-target="#exampleModal">';
                        echo 'Añadir Cuenta';
                        echo '</button>';
                        echo '</form>';
                    }
                    /**<form method="post" action="registrar.php">
                        <button type="button" class="btn btn-outline-success ms-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Añadir Cuenta
                        </button>
                    </form> */
                    ?>
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="text-center text-secondary">Registro de cuentas</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST">
                                        <?php
                                        include "conexionPDC.php";
                                        include "controlador/registrar.php";
                                        ?>
                                        <div class="mb-3">
                                            <label for="exampleInputEmail1" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" name="nombre">
                                        </div>

                                        <div class="mb-3">
                                            <label for="exampleInputEmail1" class="form-label">Numero de cuenta</label>
                                            <input type="number" class="form-control" name="nro_cuenta">
                                        </div>

                                        <div class="mb-3">
                                            <label for="tipo" class="form-label">Tipo</label>
                                            <select class="form-control" name="tipo" id="tipo">
                                                <option value="Ac">Ac</option>
                                                <option value="Pa">Pa</option>
                                                <option value="R+">R+</option>
                                                <option value="R-">R-</option>
                                                <option value="Pm">Pm</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="tipo" class="form-label">Recibe saldo</label>
                                            <select class="form-control" name="recibe_saldo" id="recibe_saldo">
                                                <option value="0">0</option>
                                                <option value="1">1</option>
                                            </select>
                                        </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" name="registrar" value="Ok">Registrar
                                        cuenta</button>
                                </div>
                                </form>

                            </div>
                        </div>
                    </div>
                    <a class="btn btn-outline-success mb-3 ms-3" href="plandecuentas.php" role="button">Plan de
                        Cuentas</a>
                </div>
            </div>

            <div class="col-4 me-4 border border-secondary pt-2">
                <form method="post" action="controlador/registrarAsiento.php">
                    <div class="mb-3">
                        <?php
                        function obtenerFechaActual()
                        {
                            return date("Y-m-d", time());
                        }
                        $fecha_actual = obtenerFechaActual();
                        $fecha_max = $fecha_actual;
                        ?>
                        <label for="exampleInputEmail1" class="form-label"><b>Fecha</b></label>
                        <input type="date" name="fecha" max="<?= $fecha_max ?>">
                    </div>

                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label"><b>Descripcion</b></label>
                        <input type="text" class="form-control" name="descripcion">
                    </div>
                    <a class="btn btn-danger" href="asientos.php" role="button">Cancelar</a>
                    <input type="hidden" name="registrar" value="Ok">
                    <button type="submit" class="btn btn-primary" name="registrar" value="Ok">Registrar
                        asiento</button>
                </form>
            </div>


            </form>
        </div>
        <hr class="container mb-5 d-flex justify-content-center" style="width:70%">
        </hr>
        <div class=" col-8 text-center mx-auto">
            <table class="table table-bordered">
                <thead class="table-info">
                    <tr>
                        <th scope="col">Nombre de cuenta</th>
                        <th scope="col">Debe</th>
                        <th scope="col">Haber</th>
                        <th scope="col"></th>
                    </tr>

                </thead>

                <tbody>
                    <script>
                        function eliminar() {
                            var respuesta = confirm("¿Estas seguro que deseas eliminar esta linea de asiento?");
                            return respuesta
                        }
                    </script>
                    <?php
                    include "controlador/eliminarLinea.php";
                    $sql = $conexion->query("SELECT l.nro_asiento, c.nombre, l.debe, l.haber FROM lineas_asiento l inner join cuenta c where l.id_cuenta = c.id_cuenta");
                    while ($datos = $sql->fetch_object()) {
                        ?>
                        <tr>
                            <td>
                                <?php
                                if ($datos->debe != 0) {
                                    echo $datos->nombre;
                                } elseif ($datos->debe == 0) { ?>
                                    <div class="text-end">
                                        <?php echo $datos->nombre; ?>
                                    </div>
                                <?php }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($datos->debe != 0) {
                                    echo $datos->debe;
                                } else {
                                    echo 0;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($datos->haber != 0) {
                                    echo $datos->haber;
                                } else {
                                    echo 0;
                                }
                                ?>
                            </td>
                            <td>
                                <a onclick="return eliminar()"
                                    href="controlador/eliminarLinea.php?nro_asiento=<?= $datos->nro_asiento ?>"><i
                                        class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
            if (isset($_GET['error'])) {
                $error = urldecode($_GET['error']);
                if ($error == 'campos_vacios') {
                    echo "<div class='alert alert-warning'>Por favor, complete todos los campos.</div>";
                } else {
                    echo "<div class='alert alert-warning'>$error</div>";
                }
            }
            ?>
        </div>
        </form>
        <?php
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
                if ($error == 'balance_incorrecto') {
                    echo "<div class='alert alert-danger text-center'>No está balanceada la cuenta. El debe y el haber deben ser iguales.</div>";
                } elseif ($error == 'campos_vacios') {
                    echo "<div class='alert alert-danger text-center'>Hay campos vacíos. Por favor, completa todos los campos.</div>";
                }
                elseif ($error == 'saldo_negativo'){
                    echo "<div class='alert alert-danger text-center'>No hay suficiente saldo en las cuentas para efectuar esta operacion.</div>";
                }
            }
            ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>