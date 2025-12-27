<?php
include '../conexionPDC.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_POST['registrado'])) {
    // Redirigir si 'registrado' no está presente
    header("Location: ../notaCredito.php?id_venta=" . $_POST['id_venta'] . "&error=Formulario incompleto");
    exit();
}

$id_venta = $_POST['id_venta'];
$tipo_nota = $_POST['tipo_nota'];
$registrado = $_POST['registrado'];
$coste_total = 0;
$coste_individual= 0;

if ($registrado == 'ok') {
    $id_usuario=$_SESSION['user_id'];
    
    $sql_consultar_id_nota_credito = "SELECT id_nota_credito FROM nota_credito ORDER BY id_nota_credito DESC LIMIT 1";
    $stmt = $conexion->prepare($sql_consultar_id_nota_credito);
    $stmt->execute();
    $resultado_id = $stmt->get_result();
    $row = $resultado_id->fetch_assoc();
    $id_nota_credito = ($row['id_nota_credito'] ?? 0) + 1; // Incrementar el id_nota_credito

    $sql_consultar_nota_total = $conexion ->query ("SELECT tipo_nota FROM nota_credito WHERE id_venta = '$id_venta'");
    if($datos_nota=$sql_consultar_nota_total->fetch_assoc()){
        $tipo_nota_registrada = $datos_nota["tipo_nota"];
    }
    
    if ($tipo_nota === 'total' && $tipo_nota_registrada !== 'total') {
        // Consultar los productos de la venta
        $sql_consultar_productos = "SELECT producto, cantidad FROM venta WHERE id_venta = ?";
        $stmt = $conexion->prepare($sql_consultar_productos);
        $stmt->bind_param("i", $id_venta);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // Insertar productos
        $sql_insertar_productos = "INSERT INTO nota_credito (id_nota_credito, id_venta, producto, cantidad, tipo_nota, fecha) VALUES (?, ?, ?, ?, 'total', NOW())";
        $stmt = $conexion->prepare($sql_insertar_productos);

        while ($datos_venta = $resultado->fetch_assoc()) {
            $producto = $datos_venta['producto'];
            $cantidad = $datos_venta['cantidad'];
            $sql_consulta_precio = $conexion ->query ("SELECT valor_venta FROM stock WHERE nombre = '$producto'");

            if ($sql_consulta_precio){
                $sql_consulta_precio = $sql_consulta_precio->fetch_assoc();
                $coste_individual = $sql_consulta_precio["valor_venta"];
                $coste_individual_iva = $coste_individual + $coste_individual*0.21;
                $coste_total +=  $coste_individual_iva * $cantidad;
            }else{
                echo "Error al consultar el precio de venta".$conexion->error;
            }
            // Solo insertar si hay datos válidos
            if (!empty($producto) && $cantidad > 0) {
                $stmt->bind_param("iisi", $id_nota_credito, $id_venta, $producto, $cantidad);
                if (!$stmt->execute()) {
                    echo "Error al registrar la nota de crédito: " . $stmt->error;
                    exit();
                }
            }
        }
        $stmt->close();
        
    } elseif($tipo_nota_registrada === 'total'){
        header("Location: ../ventas.php?error=Ya hay una nota total registrada.");
        exit();

    }elseif ($tipo_nota === 'parcial') {
        //Consultar nota credito si ya hay una existente

        $sql_consultar_id_nota_credito = $conexion->prepare("SELECT id_nota_credito FROM nota_credito WHERE id_venta = ?");
        $sql_consultar_id_nota_credito->bind_param("i", $id_venta);
        $sql_consultar_id_nota_credito->execute();
        $resultado = $sql_consultar_id_nota_credito->get_result();

        if ($resultado->num_rows > 0) {
            // Existe una nota de crédito para el id_venta especificado
            $nota_credito = $resultado->fetch_assoc();
            $id_nota_credito = $nota_credito['id_nota_credito'];
// Nota de crédito parcial
            $producto = $_POST['producto'];
            $cantidad = $_POST['cantidad'];

            $sql_consulta_precio = $conexion ->query ("SELECT valor_venta FROM stock WHERE nombre = '$producto'");

            if ($sql_consulta_precio){
                $sql_consulta_precio = $sql_consulta_precio->fetch_assoc();
                    $coste_individual = $sql_consulta_precio["valor_venta"];
                    $coste_individual_iva = $coste_individual + $coste_individual*0.21;
                    $coste_total =  $coste_individual_iva * $cantidad;
            }

            // Validar la cantidad
            $sql_validar_cantidad = "SELECT cantidad FROM venta WHERE id_venta = ? AND producto = ?";
            $stmt = $conexion->prepare($sql_validar_cantidad);
            $stmt->bind_param("is", $id_venta, $producto);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $fila = $resultado->fetch_assoc();
            $cantidad_vendida = $fila['cantidad'];

            if ($cantidad > $cantidad_vendida) {
                // Redirigir con mensaje de error si la cantidad es mayor a la vendida
                header("Location: ../notaCredito.php?id_venta=$id_venta&error=Cantidad insertada no disponible. Cantidad vendida disponible: $cantidad_vendida");
                exit();
            }

            // Insertar nota de crédito parcial
            $sql_nota_credito = "INSERT INTO nota_credito (id_nota_credito, id_venta, tipo_nota, producto, cantidad, fecha) VALUES (?, ?, 'parcial', ?, ?, CURRENT_TIMESTAMP)";
            $stmt = $conexion->prepare($sql_nota_credito);
            $stmt->bind_param("iisi", $id_nota_credito, $id_venta, $producto, $cantidad); 

            if (!$stmt->execute()) {
                echo "Error al registrar la nota de crédito: " . $stmt->error;
                exit();
            }
            $stmt->close();
                // Aquí puedes usar $id_nota_credito según lo necesites
            } else {
                // No existe una nota de crédito para el id_venta especificado
                // Nota de crédito parcial
            $producto = $_POST['producto'];
            $cantidad = $_POST['cantidad'];

            $sql_consulta_precio = $conexion ->query ("SELECT valor_venta FROM stock WHERE nombre = '$producto'");

            if ($sql_consulta_precio){
                $sql_consulta_precio = $sql_consulta_precio->fetch_assoc();
                    $coste_individual = $sql_consulta_precio["valor_venta"];
                    $coste_individual_iva = $coste_individual + $coste_individual*0.21;
                    $coste_total =  $coste_individual_iva * $cantidad;
            }

            // Validar la cantidad
            $sql_validar_cantidad = "SELECT cantidad FROM venta WHERE id_venta = ? AND producto = ?";
            $stmt = $conexion->prepare($sql_validar_cantidad);
            $stmt->bind_param("is", $id_venta, $producto);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $fila = $resultado->fetch_assoc();
            $cantidad_vendida = $fila['cantidad'];

            if ($cantidad > $cantidad_vendida) {
                // Redirigir con mensaje de error si la cantidad es mayor a la vendida
                header("Location: ../notaCredito.php?id_venta=$id_venta&error=Cantidad insertada no disponible. Cantidad vendida disponible: $cantidad_vendida");
                exit();
            }

            // Insertar nota de crédito parcial
            $sql_nota_credito = "INSERT INTO nota_credito (id_nota_credito, id_venta, tipo_nota, producto, cantidad, fecha) VALUES (?, ?, 'parcial', ?, ?, CURRENT_TIMESTAMP)";
            $stmt = $conexion->prepare($sql_nota_credito);
            $stmt->bind_param("iisi", $id_nota_credito, $id_venta, $producto, $cantidad); 

            if (!$stmt->execute()) {
                echo "Error al registrar la nota de crédito: " . $stmt->error;
                exit();
            }
            $stmt->close();
        }
        
    }

    //Asiento contable
    $id_asiento_insertado=0;
    $saldo_parcial_mercaderia = 0;
    $saldo_parcial_cuenta_por_pagar = 0;
    $sql_insertar_lineas_asiento1 = $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (20, $coste_total, 0)");
    if (!$sql_insertar_lineas_asiento1) {
        echo "Error al insertar en lineas_asiento para cuenta 20: " . $conexion->error;
        exit();
    }

    $sql_insertar_lineas_asiento2 = $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (130, 0, $coste_total)");
    if (!$sql_insertar_lineas_asiento2) {
        echo "Error al insertar en lineas_asiento para cuenta 130: " . $conexion->error;
        exit();
    }


    $sql_insertar_asiento = $conexion -> query("INSERT INTO asiento(fecha ,descripcion , id_usuario) VALUES ( NOW(), 'Nota de Credito', $id_usuario)");

    if ($sql_insertar_asiento) {
        $id_asiento_insertado = $conexion->insert_id;
        echo "id_asiento_insertado: $id_asiento_insertado";
    }else{
        echo "Error al insertar en asientos: " . $conexion->error;
    }
    

    $sql_consultar_saldo_final = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta = 20");
    if ($sql_consultar_saldo_final) {
        $sql_consultar_saldo_final = $sql_consultar_saldo_final->fetch_assoc();
        $saldo_parcial = $sql_consultar_saldo_final["saldo_final"];
        $coste_total_sin_iva = $coste_total - $coste_total * 0.21;
        $saldo_parcial_mercaderia = $coste_total_sin_iva + $saldo_parcial;
        $actualizar_saldo_mercaderia = $conexion->query("UPDATE cuenta SET saldo_final = $saldo_parcial_mercaderia WHERE id_cuenta = 20");
        if (!$actualizar_saldo_mercaderia) {
            echo "Error al actualizar saldo para cuenta 20: " . $conexion->error;
            exit();
        }
    }

    $sql_consultar_saldo_final = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta = 130");
    if ($sql_consultar_saldo_final) {
        $sql_consultar_saldo_final = $sql_consultar_saldo_final->fetch_assoc();
        $saldo_parcial = $sql_consultar_saldo_final["saldo_final"];
        $coste_total_sin_iva = $coste_total - $coste_total * 0.21;
        $saldo_parcial_cuenta_por_pagar = $coste_total_sin_iva + $saldo_parcial;
        $actualizar_saldo_cuenta_por_pagar = $conexion->query("UPDATE cuenta SET saldo_final = $saldo_parcial_cuenta_por_pagar WHERE id_cuenta = 130");
        if (!$actualizar_saldo_cuenta_por_pagar) {
            echo "Error al actualizar saldo para cuenta 130: " . $conexion->error;
            exit();
        }
    }

    $registrar_asiento_contable1 = $conexion->query("INSERT INTO registrar_asiento (id_cuenta, id_asiento, debe, haber, saldo_parcial) VALUES (20, $id_asiento_insertado, $coste_total, 0, $saldo_parcial_mercaderia)");
    if (!$registrar_asiento_contable1) {
        echo "Error al insertar el asiento contable para cuenta 20: " . $conexion->error;
        exit();
    }

    $registrar_asiento_contable2 = $conexion->query("INSERT INTO registrar_asiento (id_cuenta, id_asiento, debe, haber, saldo_parcial) VALUES (130, $id_asiento_insertado, 0, $coste_total, $saldo_parcial_cuenta_por_pagar)");
    if (!$registrar_asiento_contable2) {
        echo "Error al insertar el asiento contable para cuenta 130: " . $conexion->error;
        exit();
    }

    $sql_truncate = "TRUNCATE TABLE lineas_asiento";
    if ($conexion->query($sql_truncate) !== TRUE) {
        echo "Error al eliminar los registros de la tabla lineas_asiento: " . $conexion->error;
    }

    // Redirigir o mostrar mensaje de éxito
    header("Location: ../ventas.php?exito=Nota de crédito registrada correctamente.");
    exit();
}
?>
