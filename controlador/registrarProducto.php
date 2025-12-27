<?php 
include "conexionPDC.php";


if (!empty($_POST["producto"]) && !empty($_POST["cantidad"]) && $_POST["producto"]!="stock") {
    $producto= $_POST["producto"];
    $cantidad= $_POST["cantidad"];
    $cantidad_actual=0;
    $nueva_cantidad_actual=0;

    $sql_consulta_stock = $conexion->query("SELECT id_articulo, valor_unitario, valor_venta, cantidad_actual
                                            FROM stock 
                                            WHERE nombre='$producto' AND cantidad_actual > 0
                                            ORDER BY fecha ASC
                                            LIMIT 1");


    if ($sql_consulta_stock){
        $articulo = $sql_consulta_stock->fetch_assoc();
        $id_articulo = $articulo['id_articulo'];
        $valor_unitario = $articulo['valor_unitario'];
        $valor_venta = $articulo['valor_venta'];
        $cantidad_actual= $articulo['cantidad_actual'];
    }else{
        echo "<div class='alert alert-warning'> Error al ejecutar la consulta: " . $conexion->error . "</div>";
    }
    if ($cantidad <= $cantidad_actual && $cantidad_actual > 0){
        $nueva_cantidad_actual = $cantidad_actual - $cantidad;
        echo "<div class='alert alert-success'> Cantidad disponible: ".$nueva_cantidad_actual."</div>";
        $sql_actualizar_stock = $conexion->query("UPDATE stock SET cantidad_actual = $nueva_cantidad_actual WHERE id_articulo = $id_articulo");

        $precio_total = $cantidad * $valor_venta;
        $sql_registrar_producto = $conexion->query("INSERT INTO lineas_venta (id_articulo, cantidad, precio_total) VALUES ($id_articulo, $cantidad, $precio_total)");
        if ($sql_registrar_producto){
            $conexion->insert_id;
            $mensaje_exito = urlencode("Producto registrado correctamente.");
            header("Location: ../productos.php?exito=$mensaje_exito");
            exit();
        }else{
            echo "<div class='alert alert-warning'> Error al ejecutar la consulta: " . $conexion->error . "</div>";
        }
        }else{     
            $mensaje_error = urlencode("La cantidad de productos pedidos excede la cantidad posible.");
            header("Location: ../productos.php?error=$mensaje_error");
            exit();
        }
} else {
    $mensaje_error = urlencode("Hay campos vacíos. Reingresar datos.");
    header("Location: ../productos.php?error=$mensaje_error");
    exit();
}

?>