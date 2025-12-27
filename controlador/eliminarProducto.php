<?php 
    include "../conexionPDC.php";
    echo $_GET["nro_venta"];
    if(!empty($_GET["nro_venta"])){
        $nro_venta = $_GET["nro_venta"];
        $sql_obtener_articulo = $conexion->query("SELECT id_articulo, cantidad FROM lineas_venta WHERE nro_venta = $nro_venta");
        if($datos_articulo = $sql_obtener_articulo->fetch_assoc()){
            $id_articulo = $datos_articulo['id_articulo'];
            $cantidad = $datos_articulo['cantidad'];
            $sql_actualizar_stock = $conexion->query("UPDATE stock SET cantidad_actual = cantidad_actual + $cantidad WHERE id_articulo = $id_articulo");
        }
        $sql = $conexion->query("DELETE FROM lineas_venta WHERE nro_venta = $nro_venta");
        if($sql == 1){
            $mensaje = urlencode("Línea eliminada correctamente.");
            header("Location: ../productos.php?exito_eliminacion=$mensaje");
        } else {
            $mensaje = urlencode("Error al eliminar la línea: " . $conexion->error);
            header("Location: ../productos.php?error_eliminacion=$mensaje");
        }
        exit;
    }
?>