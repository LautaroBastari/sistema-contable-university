<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <?php
    include "conexionPDC.php";
    $id=$_GET['id'];
    $consulta_venta = $conexion ->query("SELECT id_venta, fecha FROM venta WHERE id_venta = $id");
    if ($datos_venta = $consulta_venta -> fetch_assoc()){
        $fecha = $datos_venta['fecha'];
    }

    $consulta_factura = $conexion->query("SELECT cliente, producto, cantidad FROM factura WHERE id_venta=$id");
    if ($consulta_factura){
        if ($consulta_factura->num_rows > 0) {
            $primer_dato = $consulta_factura->fetch_assoc();
            $cliente = $primer_dato['cliente'];

            $consulta_cliente = $conexion -> query("SELECT codigo, dni, cuit, cond_fiscal, direccion, email, telefono
                                                    FROM clientes
                                                    WHERE nombre = '$cliente'");
                                                    
            if ($consulta_cliente){
                if ($datos_cliente = $consulta_cliente -> fetch_assoc()){
                    $codigo = $datos_cliente['codigo'];
                    $dni = $datos_cliente['dni'];
                    $cuit = $datos_cliente['cuit'];
                    $cond_fiscal = $datos_cliente['cond_fiscal'];
                    $domicilio = $datos_cliente['direccion'];
                    $email = $datos_cliente['email'];
                    $telefono = $datos_cliente['telefono'];
                } else {
                    echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
                }
            } else {
                echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>No se encontraron registros en la factura.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
    }
    echo "<div class='alert alert-info'>Factura N°: $cond_fiscal</div>";
    $es_factura_a = ($cond_fiscal === 'Monotributista' || $cond_fiscal === 'Responsable inscripto' || $cond_fiscal === 'Consumidor Final' );
    ?>
    <div class="container mt-2" style="width: 700px;">
        <div class="d-flex ">

            <div class="container text-center border ">
                <h2 class="mt-3"><b>MK Asociados</b></h2>
                <p>Condicion Fiscal</p>
                <p>Responsable inscripto</p>
            </div>

            <div class="border border-info bg-light" style="position: absolute; width: 50px; height: 50px; left: calc(50% - 25px); top: 2%; text-align: center;">
                <h1><?= $es_factura_a ? 'A' : 'B' ?></h1>
            </div>

            <div class="container text-center border ">
                <h2 class="mt-3"><b>FACTURA</b></h2>
                <p>Número Factura: <?= $id ?></p>
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
            <div class="col-1"><strong>Código</strong></div>
            <div class="col-1"><strong>Descripción</strong></div>
            <div class="col-1"><strong>Cantidad</strong></div>
            <div class="col-1"><strong>Precio venta</strong></div>
            <?php if ($es_factura_a): ?>
            <div class="col-1"><strong>Porc. IVA</strong></div>
            <?php endif; ?>
            <div class="col-2"><strong>Precio Total</strong></div>
        </div>

        <?php
        $consulta_factura = $conexion -> query("SELECT cliente, producto, cantidad FROM factura WHERE id_venta=$id");
        $importe_neto = 0;
        $total_final = 0;

        while ($datos_factura = $consulta_factura->fetch_assoc()){
            $producto = $datos_factura["producto"];
            $cantidad = $datos_factura["cantidad"];
            
            $consulta_producto = $conexion ->query("SELECT id_articulo, nombre, valor_venta FROM stock WHERE nombre = '$producto'");
            if ($datos_producto = $consulta_producto -> fetch_assoc()){
                $id_articulo = $datos_producto["id_articulo"];
                $precio_unitario = $datos_producto["valor_venta"];
                $precio_total_sin_iva = $precio_unitario * $cantidad;
                $precio_con_iva = $precio_unitario * 1.21;

                if ($es_factura_a) {
                    $importe_neto += $precio_total_sin_iva;
                    $total_final += $precio_total_sin_iva * 1.21;
                } else {
                    $importe_neto += $precio_con_iva * $cantidad;
                    $total_final += $precio_con_iva * $cantidad;
                }
        ?>
        <div class="d-flex border border-success justify-content-around">
            <div class="col-1"><?= $id_articulo ?></div>
            <div class="col-1"><?= $producto ?></div>
            <div class="col-1"><?= $cantidad ?></div>
            <div class="col-1">$<?= $es_factura_a ? $precio_unitario : number_format($precio_con_iva, 2) ?></div>
            <?php if ($es_factura_a): ?>
            <div class="col-1">$<?= number_format($precio_unitario * 0.21, 2) ?></div>
            <?php endif; ?>
            <div class="col-2">$<?= $es_factura_a ? number_format($precio_total_sin_iva * 1.21, 2) : number_format($precio_con_iva * $cantidad, 2) ?></div>
        </div>
        <?php
            }
        }
        ?>

        <div class="d-flex flex-column align-items-end border border-danger">
            <div class="me-4"><p>Importe Neto Gravado: $<?= number_format($importe_neto, 2) ?></p></div>
            <?php if ($es_factura_a): ?>
            <div class="me-4"><p>IVA 21%: $<?= number_format($importe_neto * 0.21, 2) ?></p></div>
            <?php endif; ?>
            <div class="me-4"><p>Total: $<?= number_format($total_final, 2) ?></p></div>
        </div>
        
        <div class="d-flex justify-content-around border border-secondary">
            <div class=""><p>Comprobante Autorizado</p></div>
            <div class="">
                <div><p>CAE N°:</p></div>
                <div><p>Fecha de Vto. de CAE:</p></div>
            </div>
        </div>
    </div>
</body>
</html>
