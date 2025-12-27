<?php
include "./conexionPDC.php";

$id = $_GET["id"];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/646ac4fad6.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <button type="submit" name="button" class="btn" onclick="goBack()"><i
                        class="bi bi-caret-left-fill"></i></button>
                <script>
                    function goBack() {
                        window.history.go(-1);
                    }
                </script>
    <div class="col-10  container d-flex justify-content-center mt-5">
        
        <table class="table table-bordered">

            <thead class="table-info">
                <tr>
                    <th scope="col">Nro de asiento</th>
                    <th scope="col">Nombre de cuenta</th>
                    <th scope="col">Debe</th>
                    <th scope="col">Haber</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_asientos_registrados = $conexion->query("SELECT * FROM registrar_asiento WHERE id_asiento = $id");
                while ($datos = $sql_asientos_registrados->fetch_object()) { ?>
                    <tr>
                        <td>
                            <?= $datos->id_asiento ?>
                        </td>
                        <td>
                        <?php $id_cuenta_sql = $datos->id_cuenta ;
                                $sql_cuenta = $conexion -> query("SELECT nombre from cuenta where id_cuenta = $id_cuenta_sql");
                                if ($sql_cuenta) {
                                    $datos_cuenta = $sql_cuenta->fetch_assoc();
                                    $nombre_cuenta = $datos_cuenta["nombre"];
                                    }else{
                                        echo "Error consulta cuenta:". $conexion->error."<br>";
                                    }
                                echo $nombre_cuenta;?>
                        </td>
                        <td>
                            <?= $datos->debe ?>
                        </td>
                        <td>
                            <?= $datos->haber ?>
                        </td>
                    </tr>

                <?php
                }
                ?>
            </tbody>
        </table>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>