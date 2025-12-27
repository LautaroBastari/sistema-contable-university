<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoI`i6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<!-- Button trigger modal -->
<form method="post">
    <button class="btn btn-primary" data-id="<?= $datos->id_asiento ?> " data-bs-toggle="modal" data-bs-target="#exModal">
        Ver Asiento
    </button>
</form>
<!-- Modal -->
<div class="modal fade" id="exModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-center text-secondary">Ver Asiento</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                include "./conexionPDC.php";

                $id = $_GET["id"];

                ?>

                <table class="table table-bordered">

                    <thead class="bg-info">
                        <tr>
                            <th scope="col">Nro de asiento</th>
                            <th scope="col">Nro de cuenta</th>
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
                                    <?= $datos->id_cuenta ?>
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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-KyZXEAg3QhqLMpG8r+J9OXtoNyyFDLG6ZKweNkcpXflmUxINZw5h3mJBCGMVA8x5"
        crossorigin="anonymous"></script>
</div>
</body>
</html>