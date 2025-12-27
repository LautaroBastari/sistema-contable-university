<?php
include '../conexionPDC.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_POST['registrado'])) {
    // Redirigir si 'registrado' no está presente
    header("Location: ../notaDebito.php?id_venta=" . $_POST['id_venta'] . "&error=Formulario incompleto");
    exit();
}

$id_venta = $_POST['id_venta'];
$tipo_nota = $_POST['tipo_nota'];
$registrado = $_POST['registrado'];


if ($registrado == 'ok') {
    $id_usuario=$_SESSION['user_id'];
    $coste_total=0;
    $sql_consultar_id_nota_debito = "SELECT id_nota_debito FROM nota_debito ORDER BY id_nota_debito DESC LIMIT 1";
    $stmt = $conexion->prepare($sql_consultar_id_nota_debito);
    $stmt->execute();
    $resultado_id = $stmt->get_result();
    $row = $resultado_id->fetch_assoc();
    $id_nota_debito = ($row['id_nota_debito'] ?? 0) + 1; 

    $sql_consultar_nota_total = $conexion ->query ("SELECT tipo_nota FROM nota_debito WHERE id_venta = '$id_venta'");
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
        $sql_insertar_productos = "INSERT INTO nota_debito (id_nota_debito, id_venta, producto, cantidad, tipo_nota, fecha) VALUES (?, ?, ?, ?, 'total', NOW())";
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
                $stmt->bind_param("iisi", $id_nota_debito, $id_venta, $producto, $cantidad);
                if (!$stmt->execute()) {
                    echo "Error al registrar la nota de débito: " . $stmt->error;
                    exit();
                }
            }
        }
        $stmt->close();
        
    } elseif($tipo_nota_registrada === 'total'){
        header("Location: ../ventas.php?error=Ya hay una nota de debito total registrada.");
        exit();
    }elseif ($tipo_nota === 'parcial') {
        // Consultar nota débito si ya hay una existente
        $sql_consultar_id_nota_debito = $conexion->prepare("SELECT id_nota_debito FROM nota_debito WHERE id_venta = ?");
        $sql_consultar_id_nota_debito->bind_param("i", $id_venta);
        $sql_consultar_id_nota_debito->execute();
        $resultado = $sql_consultar_id_nota_debito->get_result();
    
        if ($resultado->num_rows > 0) {
            // Existe una nota de débito para el id_venta especificado
            $nota_debito = $resultado->fetch_assoc();
            $id_nota_debito = $nota_debito['id_nota_debito'];
    
            // Nota de débito parcial
            $producto = $_POST['producto'];
            $cantidad = $_POST['cantidad'];
    
            $sql_consulta_precio = $conexion->query("SELECT valor_venta FROM stock WHERE nombre = '$producto'");
            if ($sql_consulta_precio) {
                $sql_consulta_precio = $sql_consulta_precio->fetch_assoc();
                $coste_individual = $sql_consulta_precio["valor_venta"];
                $coste_individual_iva = $coste_individual + $coste_individual * 0.21;
                $coste_total = $coste_individual_iva * $cantidad;
            } else {
                echo "Error al consultar el precio de venta: " . $conexion->error;
                exit();
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
                header("Location: ../notaDebito.php?id_venta=$id_venta&error=Cantidad insertada no disponible. Cantidad vendida disponible: $cantidad_vendida");
                exit();
            }
    
            // Insertar nota de débito parcial
            $sql_nota_debito = "INSERT INTO nota_debito (id_nota_debito, id_venta, tipo_nota, producto, cantidad, fecha) VALUES (?, ?, 'parcial', ?, ?, CURRENT_TIMESTAMP)";
            $stmt = $conexion->prepare($sql_nota_debito);
            $stmt->bind_param("iisi", $id_nota_debito, $id_venta, $producto, $cantidad);
    
            if (!$stmt->execute()) {
                echo "Error al registrar la nota de débito: " . $stmt->error;
                exit();
            }
            $stmt->close();
        } else {
            // No existe una nota de débito para el id_venta especificado
            // Nota de débito parcial
            $producto = $_POST['producto'];
            $cantidad = $_POST['cantidad'];
    
            $sql_consulta_precio = $conexion->query("SELECT valor_venta FROM stock WHERE nombre = '$producto'");
            if ($sql_consulta_precio) {
                $sql_consulta_precio = $sql_consulta_precio->fetch_assoc();
                $coste_individual = $sql_consulta_precio["valor_venta"];
                $coste_individual_iva = $coste_individual + $coste_individual * 0.21;
                $coste_total = $coste_individual_iva * $cantidad;
            } else {
                echo "Error al consultar el precio de venta: " . $conexion->error;
                exit();
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
                header("Location: ../notaDebito.php?id_venta=$id_venta&error=Cantidad insertada no disponible. Cantidad vendida disponible: $cantidad_vendida");
                exit();
            }
    
            // Insertar nota de débito parcial
            $sql_nota_debito = "INSERT INTO nota_debito (id_nota_debito, id_venta, tipo_nota, producto, cantidad, fecha) VALUES (?, ?, 'parcial', ?, ?, CURRENT_TIMESTAMP)";
            $stmt = $conexion->prepare($sql_nota_debito);
            $stmt->bind_param("iisi", $id_nota_debito, $id_venta, $producto, $cantidad);
    
            if (!$stmt->execute()) {
                echo "Error al registrar la nota de débito: " . $stmt->error;
                exit();
            }
            $stmt->close();
        }
    }
    

    $saldo_parcial_cuentas_por_pagar = 0;
    $saldo_parcial_mercaderia = 0;
    // Insertar líneas en la tabla lineas_asiento para la nota de débito
    $sql_insertar_lineas_asiento1 = $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (131, $coste_total, 0)");
    $sql_insertar_lineas_asiento2 = $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (20, 0, $coste_total)");

    // Obtener el ID del último registro insertado
    

    $sql_insertar_asiento = $conexion -> query("INSERT INTO asiento (fecha ,descripcion , id_usuario) values ( NOW(), 'Nota de Debito', $id_usuario)");
    if (!$sql_insertar_asiento) {
        echo "Error al insertar en asientos: " . $conexion->error;
        exit();
    }else{
            $id_asiento_insertado = $conexion->insert_id;
            echo "id_asiento_insertado: $id_asiento_insertado";
        
    }
    // Consultar y actualizar el saldo final para la cuenta con id_cuenta = 131 (Cuentas por Cobrar)
    $sql_consultar_saldo_final_cuentas_por_cobrar = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta = 131");
    if ($sql_consultar_saldo_final_cuentas_por_cobrar) {
        $saldo_final = $sql_consultar_saldo_final_cuentas_por_cobrar->fetch_assoc();
        $saldo_parcial = $saldo_final["saldo_final"];
        $saldo_parcial_cuentas_por_pagar = $coste_total + $saldo_parcial;
        $sql_actualizar_saldo_cuentas_por_cobrar = $conexion->query("UPDATE cuenta SET saldo_final = $saldo_parcial_cuentas_por_pagar WHERE id_cuenta = 131");
    }else{
        echo "Error al consultar el saldo final de la cuenta con id_cuenta = 131".$conexion->error;
    }

    // Consultar y actualizar el saldo final para la cuenta con id_cuenta = 120 (Mercaderías)
    $sql_consultar_saldo_final_mercaderia = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta = 20");
    if ($sql_consultar_saldo_final_mercaderia) {
        $saldo_final = $sql_consultar_saldo_final_mercaderia->fetch_assoc();
        $saldo_parcial = $saldo_final["saldo_final"];
        $saldo_parcial_mercaderia = $saldo_parcial - $coste_total;
        $sql_actualizar_saldo_mercaderia = $conexion->query("UPDATE cuenta SET saldo_final = $saldo_parcial_mercaderia WHERE id_cuenta = 20");
    }else{
        echo "Error al consultar el saldo final de la cuenta con id_cuenta = 120".$conexion->error;
    }


    // Registrar el asiento contable en la tabla registro_asiento
    $registrar_asiento_contable1 = $conexion->query("INSERT INTO registrar_asiento (id_cuenta, id_asiento, debe, haber, saldo_parcial) VALUES (131, $id_asiento_insertado, $coste_total, 0, $saldo_parcial_cuentas_por_pagar)");
    $registrar_asiento_contable2 = $conexion->query("INSERT INTO registrar_asiento (id_cuenta, id_asiento, debe, haber, saldo_parcial) VALUES (20, $id_asiento_insertado, 0, $coste_total, $saldo_parcial_mercaderia)");
    if(!$registrar_asiento_contable2){
        echo "Error en 2:".$conexion->error;
    }
    // Vaciar la tabla lineas_asiento si es necesario
    $sql_truncate = "TRUNCATE TABLE lineas_asiento";
    if ($conexion->query($sql_truncate) !== TRUE) {
        echo "Error al eliminar los registros de la tabla lineas_asiento: " . $conexion->error;
    }
    
    // Redirigir o mostrar mensaje de éxito
    header("Location: ../ventas.php?exito=Nota de débito registrada correctamente.");
    exit();
}
?>
