<?php 
//Controlador
include "conexionPDC.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_POST["cliente"]) && !empty($_POST["fecha"]) && !empty($_POST["metodo_pago"])) {
    $id_usuario = $_SESSION['user_id'];
    $fecha = $_POST["fecha"];
    $cliente= $_POST["cliente"];
    $metodo_pago = $_POST["metodo_pago"];
    $articulos= "";
    $articulos_agregados = []; 
    $cantidad_total=0;
    $costo_total=0;
    $coste_de_mercaderia=0;
    $cmv_total = 0;
    $saldo_valido = true;
    $limite = 0;
    //Verificar saldo en deudores por ventas
    $sql_consultar_credito = $conexion->query("SELECT limite FROM clientes where nombre = '$cliente'");
    if ($limite = $sql_consultar_credito->fetch_assoc()) {
        $limite = $limite['limite'];
    }
    $consulta_lineas_venta = $conexion->query("SELECT SUM(cantidad) AS cantidad, SUM(precio_total) AS precio_total
                                                FROM lineas_venta");

    $consulta_lineas_venta_individual = $conexion->query("SELECT id_articulo, SUM(cantidad) AS cantidad, SUM(precio_total) AS precio_total
                                                                FROM lineas_venta
                                                                GROUP BY id_articulo");

    while ($datos_articulos = $consulta_lineas_venta_individual->fetch_assoc()) {
        $id_articulo = $datos_articulos['id_articulo'];
        $cantidad = $datos_articulos['cantidad'];
        $precio_de_venta = $datos_articulos['precio_total'];


        //Calcular el CMV
        $sql_consulta_articulo = $conexion -> query("SELECT valor_unitario, valor_venta
                                                    FROM stock
                                                    WHERE id_articulo = $id_articulo");

        if ($sql_consulta_articulo) {
            $articulo = $sql_consulta_articulo->fetch_assoc();
            $coste_de_mercaderia = $articulo['valor_unitario'] * $cantidad;
            $CMV = $precio_de_venta - $coste_de_mercaderia;
            $cmv_total = $cmv_total + $CMV;
            }else{
                echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
            }
       }
        
    if($metodo_pago == "Caja" && ($datos_lineas = $consulta_lineas_venta->fetch_assoc())){
        $cantidad = $datos_lineas['cantidad'];
        $costo_total = $datos_lineas['precio_total'];
        $iva = $costo_total * 0.21;
        //Registrar lineas asiento
        $sql_insertar_lineas_debe= $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (12, $costo_total + $iva, 0)");
        $sql_insertar_lineas_debe= $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (30, 0 , $iva)");
        $sql_insertar_lineas_haber= $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (38, 0 , $costo_total)");
        //Registrar asiento contable
        $sql_insertar_asiento = $conexion->query("INSERT INTO asiento (fecha, descripcion, id_usuario) VALUES ('$fecha', 'Venta de mercaderia', $id_usuario)");
        if ($sql_insertar_asiento) {
            $id_insertado = $conexion->insert_id;
        }else{
            echo "Error al insertar el asiento: " . $conexion->error;}
        $sql_registrar_asientos = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (12, $id_insertado, $costo_total + $iva, 0)");
        $sql_registrar_asientos = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (30, $id_insertado, 0 , $iva)");
        $sql_registrar_asientos = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (38, $id_insertado, 0, $costo_total)");
        $sql_registrar_cmv = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (42, $id_insertado, $cmv_total, 0)");
        $sql_registrar_mercaderia = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (20, $id_insertado, 0, $cmv_total)");
        //Actualizar cuenta de plan de cuentas
        $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $costo_total+$iva WHERE id_cuenta = 12");           //Caja
        $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $iva WHERE id_cuenta = 30");                   //Impuesto a pagar
        $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $costo_total WHERE id_cuenta = 38");      //Venta de mercaderia
        $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $cmv_total WHERE id_cuenta = 42");      //CMV
        $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final - $cmv_total WHERE id_cuenta = 20");      //Venta de mercaderia
        //Actualizar saldo parcial
        $sql_obtener_cuenta_caja = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=12");
        if ($sql_obtener_cuenta_caja){
            $datos_cuenta_caja= $sql_obtener_cuenta_caja->fetch_assoc();
            $saldo_final_caja = $datos_cuenta_caja['saldo_final'];
            $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_caja WHERE id_asiento = $id_insertado AND id_cuenta = 12");
        }

        $sql_obtener_cuenta_impuesto = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=30");
        if ($sql_obtener_cuenta_impuesto){
            $datos_cuenta_impuesto= $sql_obtener_cuenta_impuesto->fetch_assoc();
            $saldo_final_impuesto = $datos_cuenta_impuesto['saldo_final'];
            $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_impuesto WHERE id_asiento = $id_insertado AND id_cuenta = 30");
        }

        $sql_obtener_cuenta_venta = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=38");
        if ($sql_obtener_cuenta_venta){
            $datos_cuenta_venta= $sql_obtener_cuenta_venta->fetch_assoc();
            $saldo_final_venta = $datos_cuenta_venta['saldo_final'];
            $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_venta WHERE id_asiento = $id_insertado AND id_cuenta = 38");
        }

        $sql_obtener_cuenta_cmv = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=42");
        if ($sql_obtener_cuenta_cmv){
            $datos_cuenta_cmv= $sql_obtener_cuenta_cmv->fetch_assoc();
            $saldo_final_cmv = $datos_cuenta_cmv['saldo_final'];
            $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_cmv WHERE id_asiento = $id_insertado AND id_cuenta = 42");
        }

        $sql_obtener_cuenta_mercaderia = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=20");
        if ($sql_obtener_cuenta_mercaderia){
            $datos_cuenta_mercaderia= $sql_obtener_cuenta_mercaderia->fetch_assoc();
            $saldo_final_mercaderia = $datos_cuenta_mercaderia['saldo_final'];
            $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_mercaderia WHERE id_asiento = $id_insertado AND id_cuenta = 20");
        }
        
        }


        if($metodo_pago == "Banco c/c" && ($datos_lineas = $consulta_lineas_venta->fetch_assoc())){
            $cantidad = $datos_lineas['cantidad'];
            $costo_total = $datos_lineas['precio_total'];
            $iva = $costo_total * 0.21;
            $limite_nuevo = 0;
            //Registrar lineas asiento
            $sql_insertar_lineas_debe= $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (14, $costo_total + $iva, 0)");
            $sql_insertar_lineas_debe= $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (30, 0 , $iva)");
            $sql_insertar_lineas_haber= $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (38, 0 , $costo_total)");
            //Registrar asiento contable
            $sql_insertar_asiento = $conexion->query("INSERT INTO asiento (fecha, descripcion, id_usuario) VALUES ('$fecha', 'Venta de mercaderia', $id_usuario)");
            if ($sql_insertar_asiento) {
                $id_insertado = $conexion->insert_id;
            }else{
                echo "Error al insertar el asiento: " . $conexion->error;}
            $sql_registrar_asientos = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (14, $id_insertado, $costo_total + $iva, 0)");
            $sql_registrar_asientos = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (30, $id_insertado, 0 , $iva)");
            $sql_registrar_asientos = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (38, $id_insertado, 0, $costo_total)");
            $sql_registrar_cmv = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (42, $id_insertado, $cmv_total, 0)");
            $sql_registrar_mercaderia = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (20, $id_insertado, 0, $cmv_total)");
            
            //Actualizar cuenta de plan de cuentas
            $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $costo_total+$iva WHERE id_cuenta = 14");           //Caja
            $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $iva WHERE id_cuenta = 30");                   //Impuesto a pagar
            $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $costo_total WHERE id_cuenta = 38");      //Venta de mercaderia
            $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $cmv_total WHERE id_cuenta = 42");      //CMV
            $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final - $cmv_total WHERE id_cuenta = 20");      //Venta de mercaderia
            //Actualizar saldo parcial
            $sql_obtener_cuenta_caja = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=14");
            if ($sql_obtener_cuenta_caja){
                $datos_cuenta_caja= $sql_obtener_cuenta_caja->fetch_assoc();
                $saldo_final_caja = $datos_cuenta_caja['saldo_final'];
    
                $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_caja WHERE id_asiento = $id_insertado AND id_cuenta = 14");
            }
    
            $sql_obtener_cuenta_impuesto = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=30");
            if ($sql_obtener_cuenta_impuesto){
                $datos_cuenta_impuesto= $sql_obtener_cuenta_impuesto->fetch_assoc();
                $saldo_final_impuesto = $datos_cuenta_impuesto['saldo_final'];
                $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_impuesto WHERE id_asiento = $id_insertado AND id_cuenta = 30");
            }
    
            $sql_obtener_cuenta_venta = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=38");
            if ($sql_obtener_cuenta_venta){
                $datos_cuenta_venta= $sql_obtener_cuenta_venta->fetch_assoc();
                $saldo_final_venta = $datos_cuenta_venta['saldo_final'];
                $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_venta WHERE id_asiento = $id_insertado AND id_cuenta = 38");
            }
    
            $sql_obtener_cuenta_cmv = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=42");
            if ($sql_obtener_cuenta_cmv){
                $datos_cuenta_cmv= $sql_obtener_cuenta_cmv->fetch_assoc();
                $saldo_final_cmv = $datos_cuenta_cmv['saldo_final'];
                $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_cmv WHERE id_asiento = $id_insertado AND id_cuenta = 42");
            }

            $sql_obtener_cuenta_mercaderia = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=20");
            if ($sql_obtener_cuenta_mercaderia){
                $datos_cuenta_mercaderia= $sql_obtener_cuenta_mercaderia->fetch_assoc();
                $saldo_final_mercaderia = $datos_cuenta_mercaderia['saldo_final'];
                $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_mercaderia WHERE id_asiento = $id_insertado AND id_cuenta = 20");
            }
            
            
            }

            if($metodo_pago == "Deudores por ventas" && ($datos_lineas = $consulta_lineas_venta->fetch_assoc())){
                $cantidad = $datos_lineas['cantidad'];
                $costo_total = $datos_lineas['precio_total'];
                $iva = $costo_total * 0.21;
                if($limite > $costo_total+$iva){
                //Registrar lineas asiento
                $sql_insertar_lineas_debe= $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (16, $costo_total + $iva, 0)");
                $sql_insertar_lineas_debe= $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (30, 0 , $iva)");
                $sql_insertar_lineas_haber= $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (38, 0 , $costo_total)");
                //Registrar asiento contable
                $sql_insertar_asiento = $conexion->query("INSERT INTO asiento (fecha, descripcion, id_usuario) VALUES ('$fecha', 'Venta de mercaderia', $id_usuario)");
                if ($sql_insertar_asiento) {
                    $id_insertado = $conexion->insert_id;
                }else{
                    echo "Error al insertar el asiento: " . $conexion->error;}
                $sql_registrar_asientos = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (16, $id_insertado, $costo_total + $iva, 0)");
                $sql_registrar_asientos = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (30, $id_insertado, 0 , $iva)");
                $sql_registrar_asientos = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (38, $id_insertado, 0, $costo_total)");
                $sql_registrar_cmv = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (42, $id_insertado, $cmv_total, 0)");
                $sql_registrar_mercaderia = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (20, $id_insertado, 0, $cmv_total)");
                
                //Actualizar cuenta de plan de cuentas
                $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $costo_total+$iva WHERE id_cuenta = 16");           //Caja
                $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $iva WHERE id_cuenta = 30");                        //Impuesto a pagar
                $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $costo_total WHERE id_cuenta = 38");                //Venta de mercaderia
                $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $cmv_total WHERE id_cuenta = 42");                  //CMV
                $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final - $cmv_total WHERE id_cuenta = 20");                  //Venta de mercaderia
                //Actualizar saldo parcial  
                $sql_obtener_cuenta_caja = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=16");
                if ($sql_obtener_cuenta_caja){
                    $datos_cuenta_caja= $sql_obtener_cuenta_caja->fetch_assoc();
                    $saldo_final_caja = $datos_cuenta_caja['saldo_final'];
        
                    $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_caja WHERE id_asiento = $id_insertado AND id_cuenta = 16");
                }
        
                $sql_obtener_cuenta_impuesto = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=30");
                if ($sql_obtener_cuenta_impuesto){
                    $datos_cuenta_impuesto= $sql_obtener_cuenta_impuesto->fetch_assoc();
                    $saldo_final_impuesto = $datos_cuenta_impuesto['saldo_final'];
                    $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_impuesto WHERE id_asiento = $id_insertado AND id_cuenta = 30");
                }
        
                $sql_obtener_cuenta_venta = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=38");
                if ($sql_obtener_cuenta_venta){
                    $datos_cuenta_venta= $sql_obtener_cuenta_venta->fetch_assoc();
                    $saldo_final_venta = $datos_cuenta_venta['saldo_final'];
                    $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_venta WHERE id_asiento = $id_insertado AND id_cuenta = 38");
                }
                $sql_obtener_cuenta_cmv = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=42");
                if ($sql_obtener_cuenta_cmv){
                    $datos_cuenta_cmv= $sql_obtener_cuenta_cmv->fetch_assoc();
                    $saldo_final_cmv = $datos_cuenta_cmv['saldo_final'];
                    $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_cmv WHERE id_asiento = $id_insertado AND id_cuenta = 42");
                }

                $sql_obtener_cuenta_mercaderia = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=20");
                if ($sql_obtener_cuenta_mercaderia){
                    $datos_cuenta_mercaderia= $sql_obtener_cuenta_mercaderia->fetch_assoc();
                    $saldo_final_mercaderia = $datos_cuenta_mercaderia['saldo_final'];
                    $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_mercaderia WHERE id_asiento = $id_insertado AND id_cuenta = 20");
                }
                
                $limite_nuevo = $limite - ($costo_total+$iva);
                $sql_actualizar_limite = $conexion->query("UPDATE clientes SET limite = $limite_nuevo WHERE nombre = '$cliente'");

                
                }else{
                    echo "<div class='alert alert-warning'>El credito es insuficiente para realizar la compra. </div>";
                }
            }

                if($metodo_pago == "Documentos a cobrar" && ($datos_lineas = $consulta_lineas_venta->fetch_assoc())){

                    $cantidad = $datos_lineas['cantidad'];
                    $costo_total = $datos_lineas['precio_total'];
                    $iva = $costo_total * 0.21;
                    //Registrar lineas asiento
                    $sql_insertar_lineas_debe= $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (17, $costo_total + $iva, 0)");
                    $sql_insertar_lineas_debe= $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (30, 0 , $iva)");
                    $sql_insertar_lineas_haber= $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (38, 0 , $costo_total)");
                    //Registrar asiento contable
                    $sql_insertar_asiento = $conexion->query("INSERT INTO asiento (fecha, descripcion, id_usuario) VALUES ('$fecha', 'Venta de mercaderia', $id_usuario)");
                    if ($sql_insertar_asiento) {
                        $id_insertado = $conexion->insert_id;
                    }else{
                        echo "Error al insertar el asiento: " . $conexion->error;}
                    $sql_registrar_asientos = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (17, $id_insertado, $costo_total + $iva, 0)");
                    $sql_registrar_asientos = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (30, $id_insertado, 0 , $iva)");
                    $sql_registrar_asientos = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (38, $id_insertado, 0, $costo_total)");
                    $sql_registrar_cmv = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (42, $id_insertado, $cmv_total, 0)");
                    $sql_registrar_mercaderia = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES (20, $id_insertado, 0, $cmv_total)");
                    //Actualizar cuenta de plan de cuentas
                    $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $costo_total+$iva WHERE id_cuenta = 17");           //Caja
                    $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $iva WHERE id_cuenta = 30");                   //Impuesto a pagar
                    $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $costo_total WHERE id_cuenta = 38");      //Venta de mercaderia
                    $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $cmv_total WHERE id_cuenta = 42");      //CMV
                    $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final - $cmv_total WHERE id_cuenta = 20");      //Venta de mercaderia
                    //Actualizar saldo parcial
                    $sql_obtener_cuenta_caja = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=17");
                    if ($sql_obtener_cuenta_caja){
                        $datos_cuenta_caja= $sql_obtener_cuenta_caja->fetch_assoc();
                        $saldo_final_caja = $datos_cuenta_caja['saldo_final'];
            
                        $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_caja WHERE id_asiento = $id_insertado AND id_cuenta = 17");
                    }
            
                    $sql_obtener_cuenta_impuesto = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=30");
                    if ($sql_obtener_cuenta_impuesto){
                        $datos_cuenta_impuesto= $sql_obtener_cuenta_impuesto->fetch_assoc();
                        $saldo_final_impuesto = $datos_cuenta_impuesto['saldo_final'];
                        $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_impuesto WHERE id_asiento = $id_insertado AND id_cuenta = 30");
                    }
            
                    $sql_obtener_cuenta_venta = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=38");
                    if ($sql_obtener_cuenta_venta){
                        $datos_cuenta_venta= $sql_obtener_cuenta_venta->fetch_assoc();
                        $saldo_final_venta = $datos_cuenta_venta['saldo_final'];
                        $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_venta WHERE id_asiento = $id_insertado AND id_cuenta = 38");
                    }

                    $sql_obtener_cuenta_cmv = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=42");
                    if ($sql_obtener_cuenta_cmv){
                        $datos_cuenta_cmv= $sql_obtener_cuenta_cmv->fetch_assoc();
                        $saldo_final_cmv = $datos_cuenta_cmv['saldo_final'];
                        $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_cmv WHERE id_asiento = $id_insertado AND id_cuenta = 42");
                    }

                    $sql_obtener_cuenta_mercaderia = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta=20");
                    if ($sql_obtener_cuenta_mercaderia){
                        $datos_cuenta_mercaderia= $sql_obtener_cuenta_mercaderia->fetch_assoc();
                        $saldo_final_mercaderia = $datos_cuenta_mercaderia['saldo_final'];
                        $sql_actualizar_saldo_asientos = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo_final_mercaderia WHERE id_asiento = $id_insertado AND id_cuenta = 20");
                    }
                    

                    
                    }


       
    $consulta_lineas_venta = $conexion->query("SELECT id_articulo, SUM(cantidad) AS cantidad_total
                                                            FROM lineas_venta
                                                            GROUP BY id_articulo");

       

    while ($datos_articulos = $consulta_lineas_venta->fetch_assoc()) {
        $id_articulo_consulta = $datos_articulos['id_articulo'];
        $cantidad_total = $datos_articulos['cantidad_total'];
        
        $consulta_stock_nombre = $conexion->query("SELECT nombre
                                                FROM stock
                                                WHERE id_articulo = $id_articulo_consulta");
        
        if ($consulta_stock_nombre) {
            $nombre = $consulta_stock_nombre->fetch_assoc()['nombre'];

            if (!in_array($nombre, $articulos_agregados)) {
                $articulos .= $nombre . ', ';
                $articulos_agregados[] = $nombre; 
            }
        }

    }

    $articulos = rtrim($articulos, ', ');

    //CANTIDAD TOTAL
    $consulta_cantidades = $conexion->query("SELECT SUM(cantidad) AS total_cantidad 
                                             FROM lineas_venta");
    if($consulta_cantidades){
        $resultado=$consulta_cantidades->fetch_assoc();
        $cantidad_total= $resultado["total_cantidad"];
    } else {
        echo "<div class='alert alert-warning'>Error al ejecutar la consulta de cantidad: " . $conexion->error . "</div>";
    }

    //PRECIO TOTAL
    $consulta_precio_total = $conexion->query("SELECT nro_venta, id_articulo, cantidad, precio_total 
                                               FROM lineas_venta");
    //Conseguir el id_venta anterior ingresado de la tabla, sumarle 1 y asignarlo a las tablas

    $sql_consultar_id_venta = $conexion -> query ("SELECT id_venta FROM venta ORDER BY id_venta DESC LIMIT 1");
    if($sql_consultar_id_venta){
        $id_ultima_venta = $sql_consultar_id_venta->fetch_assoc()["id_venta"];
        $id_ultima_venta++;
    }

    while($resultado = $consulta_precio_total->fetch_assoc()){ 
        $nro_venta = $resultado["nro_venta"];
        $costo_total = $resultado["precio_total"];
        $cantidad = $resultado["cantidad"];
        $id_articulo = $resultado["id_articulo"];
        $sql_nombre_articulo = $conexion -> query("SELECT nombre FROM stock WHERE id_articulo = $id_articulo");
        if($sql_nombre_articulo){
            $nombre_articulo = $sql_nombre_articulo->fetch_assoc()["nombre"];
            $insertar_registro_venta= $conexion->query("INSERT INTO venta(id_venta, fecha, cliente, id_producto, producto, cantidad, coste_total) VALUES ($id_ultima_venta, '$fecha', '$cliente', $id_articulo, '$nombre_articulo', $cantidad, $costo_total);");  
        }
    }

    
    if ($insertar_registro_venta){
        $consulta_ultimo_id = $conexion->query("SELECT MAX(id_venta) AS ultimo_id FROM venta");
    
        if ($consulta_ultimo_id) {
            $id_venta = $consulta_ultimo_id->fetch_assoc()["ultimo_id"];
            echo "<div class='alert alert-success'>La consulta ha sido exitosa. Último ID de venta: $id_venta</div>";
        

        $consulta_lineas_venta_id_articulos = $conexion->query("SELECT id_articulo, SUM(cantidad) AS cantidad_total
                                                        FROM lineas_venta
                                                        GROUP BY id_articulo");
        
        while($datos_articulos_factura = $consulta_lineas_venta_id_articulos->fetch_assoc()){
            $id_articulo_consulta = $datos_articulos_factura['id_articulo'];
            $cantidad_total = $datos_articulos_factura['cantidad_total'];
            $consulta_stock_nombre = $conexion->query("SELECT nombre
                                                     FROM stock
                                                     WHERE id_articulo=$id_articulo_consulta");
            if ($nombre = $consulta_stock_nombre->fetch_assoc()){
                $nombre_articulo = $nombre['nombre'];
                $insertar_factura = $conexion->query("INSERT INTO factura(id_venta, cliente, producto, cantidad) VALUES ($id_venta, '$cliente','$nombre_articulo', $cantidad_total)");
                }
            }
        }
    } else {
        echo "<div class='alert alert-warning'>Error al insertar en venta: " . $conexion->error . "</div>";
    }
} else {
    $mensaje_error = urlencode("Hay campos vacíos. Reingresar datos.");
    header("Location: ../ventas.php?error=$mensaje_error");
    exit();
}
$sql_vaciar_lineas = $conexion->query("DELETE FROM lineas_asiento");
$sql_vaciar_lineas_venta = $conexion->query("DELETE FROM lineas_venta");
$sql_reset_auto_increment = $conexion->query("ALTER TABLE lineas_venta AUTO_INCREMENT = 0");
$sql_actualizar_saldo = $conexion->query("UPDATE clientes SET saldo = saldo + ($costo_total+$iva) WHERE nombre = '$cliente'");

$mensaje_exito = urlencode("Venta registrada correctamente.");
header("Location: ../ventas.php?exito=$mensaje_exito");
exit();

?>
