<?php 
include "conexionPDC.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!empty($_POST["nombre"]) && !empty($_POST["rubro"]) && !empty($_POST["cantidad_act"]) && !empty($_POST["cantidad_max"]) && !empty($_POST["valor_unitario"])&& !empty($_POST["valor_venta"])) {
    $id_usuario = $_SESSION['user_id'];
    $nombre = $_POST["nombre"];
    $fecha= $_POST["fecha"];
    $rubro= $_POST["rubro"];
    $cantidad_act= $_POST["cantidad_act"];
    $cantidad_max = $_POST["cantidad_max"];
    $valor_unitario= $_POST["valor_unitario"];
    $valor_venta= $_POST["valor_venta"];
    $lineas_insertadas=false;
    $saldo_disponible = true;
    $valor_inventario = $valor_unitario * $cantidad_act;
    $sql_consultar_dinero_disponible = $conexion->query("SELECT saldo_final FROM cuenta where id_cuenta = 12"); //Consultar saldo de la caja
    if($datos_dinero = $sql_consultar_dinero_disponible->fetch_assoc()){
        $dinero_disponible = $datos_dinero['saldo_final'];
        if ($dinero_disponible < $valor_inventario){
            $saldo_disponible = false;
        }
    }

    if ($cantidad_act < $cantidad_max && $saldo_disponible == true){
        $sql_registrar_articulo = $conexion->query("INSERT INTO stock (nombre, fecha, rubro, cantidad_actual, cantidad_maxima, valor_unitario, valor_venta, valor_inventario) VALUES ('$nombre', '$fecha' , '$rubro', $cantidad_act, $cantidad_max, $valor_unitario, $valor_venta, $valor_inventario)");
        if($sql_registrar_articulo){
            $id_articulo = $conexion->insert_id;
            echo "<div class='alert alert-success'>Asiento añadido exitosamente. Numero de articulo: ". $id_articulo."</div>";

            //Aca estaba el if del metodo de pago
                $sql_insertar_lineas_debe = $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (20, $valor_inventario, 0)");
                $sql_insertar_lineas_haber = $conexion->query("INSERT INTO lineas_asiento (id_cuenta, debe, haber) VALUES (12, 0, $valor_inventario)");

                //INSERTAR ASIENTO
                $sql_insertar_asiento = $conexion->query("INSERT INTO asiento (fecha, descripcion, id_usuario) VALUES ('$fecha', 'Compra de mercaderia', $id_usuario)");
                if ($sql_insertar_asiento) {
                    // Obtener el ID automático generado
                    $id_asiento = $conexion->insert_id;
                } else {
                    echo "Error al insertar el asiento: " . $conexion->error;
                }
            //Validar que la caja no este vacia o negativa.
                if ($sql_insertar_lineas_debe && $sql_insertar_lineas_haber && $sql_insertar_asiento){
                    $sql_consulta_lineas_asiento = $conexion -> query("SELECT * FROM lineas_asiento");
                    $contador = 0;
                    
                    while($datos_lineas=$sql_consulta_lineas_asiento->fetch_assoc()){
                        
                        $id_cuenta = $datos_lineas['id_cuenta'];
                        $debe = $datos_lineas['debe'];
                        $haber = $datos_lineas['haber'];
                        $sql_insertar_registro_asiento = $conexion->query("INSERT INTO registrar_asiento(id_cuenta, id_asiento, debe, haber) VALUES ($id_cuenta, $id_asiento, $debe, $haber)");

                        //Actualizamos los saldos de las cuentas
                        if($sql_insertar_registro_asiento){
                            if ($contador == 0){
                                $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $valor_inventario WHERE id_cuenta = 20");
                                $sql_obtener_saldo = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta = 20");
                                if ($sql_obtener_saldo){
                                    $datos_saldo = $sql_obtener_saldo->fetch_assoc();
                                    $saldo = $datos_saldo['saldo_final'];

                                    $sql_actualizar_saldo = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo WHERE id_asiento = $id_asiento AND id_cuenta = 20");
                                }else{
                                    echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
                                }
                                $contador++;
                            }else if ($contador == 1){
                                //validar salgo no negativo
                                $sql_validacion_saldo_positivo = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta = 12");
                                if ($sql_validacion_saldo_positivo){
                                    $datos_saldo = $sql_validacion_saldo_positivo->fetch_assoc();
                                    $saldo = $datos_saldo['saldo_final'];
                                    $saldo_total =  $saldo - $valor_inventario;
                                    if ($saldo_total >= 0){
                                        $sql_actualizar_cuenta = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final - $valor_inventario WHERE id_cuenta = 12");
                                        $sql_obtener_saldo = $conexion->query("SELECT saldo_final FROM cuenta WHERE id_cuenta = 12");
                                        if ($sql_obtener_saldo){
                                            $datos_saldo = $sql_obtener_saldo->fetch_assoc();
                                            $saldo = $datos_saldo['saldo_final'];
                                            $sql_actualizar_saldo = $conexion->query("UPDATE registrar_asiento SET saldo_parcial = $saldo WHERE id_asiento = $id_asiento AND id_cuenta = 12");
                                            echo "<div class='alert alert-success'>El saldo ha quedado de: ". $saldo_total ."</div>";
                                    }else{
                                        echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";}
                                }else{
                                    echo "<div class='alert alert-warning'>El saldo ha quedado negativo y no es posible efectuar esta accion por falta de dinero disponible.</div>";}
                            }else{
                                echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
                            }
                                $contador=0;
                                $lineas_insertadas=true;
                                if ($lineas_insertadas){
                                    $sql_eliminar_lineas = $conexion->query("DELETE FROM lineas_asiento");}


                            }
                    }else{
                        echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
                    }
                    }


            
                

            }else{
                echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
            }
        }else{
            echo "La cantidad actual es mayor a la cantidad maxima.";
        }
    }elseif($cantidad_act > $cantidad_max){
        echo "<div class='alert alert-warning'>Hay campos vacios.</div>";
    }else{
        echo "<div class='alert alert-warning'>Saldo insuficiente en la caja para efectuar esta compra.</div>";
    }
}
?>