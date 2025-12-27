<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <?php 

    ?>
    <h1 class="text-center p-3"> Registro</h1>
    <div class="container-fluid row justify-content-center my-5">
        <form class="col-4 p-3" method="POST">
            <h3 class="text-center text-secondary">Registro de cuentas</h3>
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
                    <option value="Pn">Pn</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="tipo" class="form-label">Recibe saldo</label>
                <select class="form-control" name="recibe_saldo" id="recibe_saldo">
                    <option value="0">0</option>
                    <option value="1">1</option>
                </select>
            </div>

            
            <button type="submit" class="btn btn-primary" name="registrar" value="Ok">Registrar cuenta</button>
        </form>
    </div>
</body>
</html>
    
