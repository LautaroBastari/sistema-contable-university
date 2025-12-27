<?php
include 'conexionPDC.php';

$id_venta = $_GET['id_venta'];

$sql_consultar_productos = "SELECT producto, cantidad FROM venta WHERE id_venta = $id_venta";
$resultado = $conexion->query($sql_consultar_productos);

$tipo_nota = isset($_POST['tipo_nota']) ? $_POST['tipo_nota'] : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota de Crédito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/646ac4fad6.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <div class="container border border-primary-light mt-5 bg-secondary-subtle text-dark">
        <div class="container d-flex mt-3">
            <a class="btn mt-3" href="ventas.php"><i class="bi bi-caret-left-fill"></i></a>
            <div class="col-4 p-3 m-auto">
                <h3 class="d-flex justify-content-center alert alert-secondary"><b>Añadir Nota Crédito</b></h3>
            </div>
        </div>
        <hr class="container d-flex justify-content-center" style="width:70%">

        <div class="container text-center" style="width: 30%;">
            <form method="POST" action="">
                <input type="hidden" name="id_venta" value="<?= $id_venta ?>">

                <!-- Tipo de Nota -->
                <div class="mb-3">
                    <label for="tipo_nota" class="form-label">Tipo de Nota</label>
                    <select name="tipo_nota" class="form-select" onchange="this.form.submit()">
                        <option value="">Seleccione el tipo de nota</option>
                        <option value="total" <?= $tipo_nota === 'total' ? 'selected' : '' ?>>Total</option>
                        <option value="parcial" <?= $tipo_nota === 'parcial' ? 'selected' : '' ?>>Parcial</option>
                    </select>
                </div>
            </form>

            <?php if ($tipo_nota === 'parcial'): ?>
            <form method="POST" action="controlador/registrarCredito.php">
                <input type="hidden" name="id_venta" value="<?= $id_venta ?>">
                <input type="hidden" name="tipo_nota" value="parcial">

                <!-- Selección de Producto y Cantidad -->
                <div class="mb-3">
                    <label for="producto" class="form-label">Producto</label>
                    <select name="producto" class="form-select">
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <option value="<?= $fila['producto'] ?>">
                                <?= $fila['producto'] ?> - Cantidad: <?= $fila['cantidad'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required>
                </div>

                <button type="submit" class="btn btn-primary mb-4 mt-2" name="registrado" value="ok">Añadir Nota Crédito</button>
            </form>
            <?php elseif ($tipo_nota === 'total'): ?>
            <form method="POST" action="controlador/registrarCredito.php">
                <input type="hidden" name="id_venta" value="<?= $id_venta ?>">
                <input type="hidden" name="tipo_nota" value="total">

                <button type="submit" class="btn btn-success mb-4 mt-2" name="registrado" value="ok">Añadir Nota Crédito Total</button>

            </form>
            <?php endif; 
            if (isset($_GET['error'])) {
                $error_message = $_GET['error'];
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <strong>Error:</strong> $error_message
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
            }?>
            
        </div>
    </div>
</body>

</html>
