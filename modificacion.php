<?php
include "conexionPDC.php";
$id = $_GET["id"];
//Hacer una consulta a la base de datos que me traiga todos
//los datos del registro que tenga el id pasado
$sql = $conexion->query("SELECT * FROM cuenta WHERE id_cuenta=$id");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <div class="container border border-primary-light mt-5 bg-secondary-subtle text-dark">
        <div class="container d-flex mt-3">
            <a class="btn mt-3" href="plandecuentas.php"><i class="bi bi-caret-left-fill"></i></a>
            <form class="col-4 p-3 m-auto" method="POST">
                <h3 class="d-flex justify-content-center alert alert secondary"><b>Modificar cuenta</b></h3>
        </div>
        <hr class="container  d-flex justify-content-center" style="width:70%">
        </hr>
        <div class="container text-center " style="width: 30%;">
            <input type="hidden" name="id" value="<?php echo isset($_GET["id"]) ? $_GET["id"] : ''; ?>">
            <?php
            include "controlador/modificacion.php";

            if ($sql === false) {
                echo "<div class='alert alert-danger'>Error: " . $conexion->error . "</div>";
            } else {
                while ($datos = $sql->fetch_object()) { //Mientras en el registros halla datos quiero que se registren en la variable creada "datos"
                    ?>
                    <div class="mb-1">
                        <label for="exampleInputEmail1" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" value="<?= $datos->nombre ?>">
                    </div>
                    <div class="mb-1">
                        <label for="exampleInputEmail1" class="form-label">Tipo</label>
                        <input type="text" class="form-control" name="tipo" value="<?= $datos->tipo ?>">
                    </div>
                    <div class="mb-1">
                        <label for="exampleInputEmail1" class="form-label">Numero de cuenta</label>
                        <input type="number" class="form-control" name="numero_cuenta" value="<?= $datos->nro_cuenta ?>">
                    </div>
                    <div class="mb-1">
                        <label for="exampleInputEmail1" class="form-label">Recibe saldo</label>
                        <input type="number" class="form-control" name="recibe_saldo" value="<?= $datos->recibe_saldo ?>">
                    </div>
                    <?php
                }
            }
            ?>
            <button type="submit" class="btn btn-primary mb-4 mt-2" name="registrado" value="ok">Modificar
                cuenta</button>
            </form>
        </div>

    </div>
</body>

</html>