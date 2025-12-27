<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Débito</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <?php
    include "conexionPDC.php";
    $id = $_GET['id_venta'];
    $consulta_venta = $conexion->query("SELECT id_venta, fecha, cliente FROM venta WHERE id_venta = $id");
    if ($datos_venta = $consulta_venta->fetch_assoc()){
        $fecha = $datos_venta['fecha'];
        $cliente = $datos_venta['cliente'];
    }

    $consulta_nota_debito = $conexion->query("SELECT producto, cantidad FROM nota_debito WHERE id_venta=$id");
    if ($consulta_nota_debito){
        if ($consulta_nota_debito->num_rows > 0) {
            $primer_dato = $consulta_nota_debito->fetch_assoc();
            $producto = $primer_dato['producto'];
            $cantidad = $primer_dato['cantidad'];

            // CLIENTE
            $consulta_cliente = $conexion->query("SELECT codigo, dni, cuit, cond_fiscal, direccion, email, telefono
                                                    FROM clientes
                                                    WHERE nombre = '$cliente'");

            if ($consulta_cliente){
                if ($datos_cliente = $consulta_cliente->fetch_assoc()){
                    $codigo = $datos_cliente['codigo'];
                    $dni = $datos_cliente['dni'];
                    $cuit = $datos_cliente['cuit'];
                    $cond_fiscal = $datos_cliente['cond_fiscal'];
                    $domicilio = $datos_cliente['direccion'];
                    $email = $datos_cliente['email'];
                    $telefono = $datos_cliente['telefono'];
                }
                else{
                    echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
                }
            }else{
                echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
            }

        } else {
            $mensaje = urlencode("No se encontraron registros en la nota de débito.");
            header("Location: ventas.php?debitos_vacios=1");
            exit();
        }
        
    } else {
        echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
    }
    ?>
    <div class="container mt-2" style="width: 700px;">
        <div class="d-flex ">

            <div class="container text-center border ">
                <h2 class="mt-3"><b>MK Asociados</b></h2>
                <p>Condición Fiscal:</p>
                <p>Responsable Inscripto</p>
            </div>

            <div class="container text-center border ">
                <h2 class="mt-3"><b>NOTA DE DÉBITO</b></h2>
                <p>Número Nota: <?= $id ?></p>
                <p>Fecha de emisión: <?= $fecha ?></p>
            </div>
        </div>

        <div class="d-flex border border-primary">
            <div class="col-6 ps-2">
                <div><p>Nombre: <?= $cliente ?> </p></div>
                <div><p>Código de Cliente: <?= $codigo ?></p></div>
                <div><p>Domicilio: <?= $domicilio ?></p></div>
                <div><p>Condición IVA: <?= $cond_fiscal ?></p></div>
            </div>
            <div class="col-6 ps-2">
                <div><p>DNI: <?= $dni ?> </p></div>
                <div><p>CUIT: <?= $cuit ?> </p></div>
                <div><p>Email: <?= $email ?></p></div>
                <div><p>Teléfono: <?= $telefono ?></p></div>
            </div>
        </div>

        <div class="d-flex border border-success justify-content-around">
            <div class="col-1"><strong>Cantidad</strong></div>
            <div class="col-1"><strong>Producto</strong></div>
            <div class="col-1"><strong>Precio Unitario</strong></div>
            <?php if ($cond_fiscal !== 'Consumidor Final' && $cond_fiscal !== 'Exento al IVA') { ?>
                <div class="col-1"><strong>Porcentaje IVA</strong></div>
            <?php } ?>
            <div class="col-2"><strong>Precio Total</strong></div>
        </div>

        <?php
        $consulta_nota_debito = $conexion->query("SELECT producto, cantidad FROM nota_debito WHERE id_venta=$id");
        $precio_final = 0;
        $total = 0;
        $importe_neto = 0;
        $iva = 0;
        while ($datos_nota_debito = $consulta_nota_debito->fetch_assoc()){
            $producto = $datos_nota_debito["producto"];
            $cantidad = $datos_nota_debito["cantidad"];
            
            $consulta_producto = $conexion->query("SELECT id_articulo, nombre, valor_unitario, valor_venta FROM stock WHERE nombre = '$producto'");
            if ($datos_producto = $consulta_producto->fetch_assoc()){
                $id_articulo = $datos_producto["id_articulo"];
                $precio_unitario = $datos_producto["valor_venta"];
                $precio_total = $precio_unitario * $cantidad;
                $precio_final_producto = $precio_total + $precio_total * 0.21; // Nota de débito: incremento IVA
                $precio_final += $precio_final_producto;
                $importe_neto += $precio_total;
                $iva += $precio_total * 0.21;
                $total = $importe_neto + $iva;
        ?>
        <div class="d-flex border border-success justify-content-around">
            <div class="col-1"><?= $cantidad ?></div>
            <div class="col-1"><?= $producto ?></div>
            <div class="col-1">
                <?php 
                if ($cond_fiscal === 'Consumidor Final' || $cond_fiscal === 'Exento al IVA') {
                    // Mostrar el precio unitario con IVA incluido
                    echo "$" . number_format($precio_unitario * 1.21, 2);
                } else {
                    // Mostrar el precio unitario sin IVA
                    echo "$" . number_format($precio_unitario, 2);
                }
                ?>
            </div>
            <?php if ($cond_fiscal === 'Responsable inscripto' || $cond_fiscal === 'Monotributista') { ?>
                <div class="col-1">$<?= number_format($precio_unitario * 0.21, 2) ?></div>
            <?php } ?>
            <div class="col-2">$<?= number_format($precio_final_producto, 2) ?></div>
        </div>
        <?php
            }
        }
        ?>

        <div class="d-flex flex-column align-items-end border border-danger">
            <?php if ($cond_fiscal !== 'Consumidor final' && $cond_fiscal !== 'Exento al IVA') { ?>
                <div class="me-4"><p>Importe Neto Gravado: $<?= number_format($importe_neto, 2) ?></p></div>
                <div class="me-4"><p>IVA 21%: $<?= number_format($iva, 2) ?></p></div>
            <?php } ?>
            <div class="me-4"><p><strong>Total: $<?= number_format($total, 2) ?></strong></p></div> <!-- Total con IVA incluido -->
        </div>
        
        <div class="d-flex justify-content-around border border-secondary">
            <div class=""><p>Comprobante Autorizado</p></div>
            
            <img src="https://auth.afip.gob.ar/contribuyente_/resources/frameworkAFIP/v1/img/logo_afip.png" style="width: 100px;">
        </div>
    </div>
</body>
</html>
