
<?php
    session_start();
    $conexion = mysqli_connect("localhost", "root", "", "php_login_database") or die("Problemas con la conexión");

    if (!empty($_POST["fecha"]) && !empty($_POST["descripcion"])) {
        $id_usuario=$_SESSION['user_id'];
        $desc = $_POST["descripcion"];
        $fecha= $_POST["fecha"];
        $permitir_actualizacion = true;
        //Eliminar las operaciones sobre cuentas del codigo main, verificar el saldo de la cuenta porque cuando se excede del saldo hace la operacion igual, 
        //tener en cuenta los tipos de cuenta y sumar o restar
        //El update de las cuentas tendria que ir fuera del while, para verificar primero que se puedan hacer todas en el while, cuando salga, que saldrá porque no tiro error
        //Actualizar la cuenta.
        $sql_saldo_asiento = $conexion->query("SELECT id_cuenta,
                                                    SUM(debe) AS suma_debe,
                                                    SUM(haber) AS suma_haber,
                                                    SUM(debe) - SUM(haber) AS saldo_total
                                                    FROM lineas_asiento");
        if ($sql_saldo_asiento) {                                                                                               //Actualizar la tabla cuenta y asiento
          $datos_asientos = $sql_saldo_asiento->fetch_assoc();
          $saldo_asiento_total = $datos_asientos["saldo_total"];
          if($saldo_asiento_total==0){
            $sql_debe_haber = $conexion->query("SELECT id_cuenta,
                                                SUM(debe) AS suma_debe,
                                                SUM(haber) AS suma_haber
                                                FROM lineas_asiento 
                                                GROUP BY id_cuenta;");
              
            if ($sql_debe_haber === false) {
              die("Error en la consulta SQL: " . $conexion->error);}

              $conexion->autocommit(false);
              try {
                $valid = true;
            
                while ($sumas_total = $sql_debe_haber->fetch_assoc()){
                    $id_cuenta_suma = $sumas_total['id_cuenta'];
                    $total_debe_cuenta = $sumas_total['suma_debe'];
                    $total_haber_cuenta = $sumas_total['suma_haber'];
            
                    $sql_consulta_cuenta = $conexion->query("SELECT tipo, saldo_final FROM cuenta WHERE id_cuenta = $id_cuenta_suma");
                    
                    if ($sql_consulta_cuenta){
                        $datos_tipo = $sql_consulta_cuenta->fetch_assoc();
                        $saldo_final_cuenta = $datos_tipo["saldo_final"];
                        $tipo = $datos_tipo["tipo"];
            
                        if(($tipo == "Ac") or ($tipo == "R-")){
                            $total_saldo = $total_debe_cuenta - $total_haber_cuenta;
                            $saldo_final_cuenta += $total_saldo;
            
                            if($saldo_final_cuenta >= 0){
                                // Actualizar el saldo
                                $sql_insercion_monto = $conexion->query("UPDATE cuenta SET saldo_final = $saldo_final_cuenta WHERE id_cuenta = $id_cuenta_suma");
                                if(!$sql_insercion_monto) {
                                    $valid = false;
                                    throw new Exception("Error en la inserción: " . $conexion->error);
                                }
                            } else {
                                $valid = false;
                                throw new Exception("Saldo negativo en la cuenta: $id_cuenta_suma");
                            }
                        } elseif(($tipo == "Pa") || ($tipo == "R+") || ($tipo == "Pm")) {
                            $total_saldo = $total_haber_cuenta - $total_debe_cuenta;
                            $saldo_final_cuenta += $total_saldo;
            
                            if($saldo_final_cuenta >= 0){
                                // Actualizar el saldo
                                $sql_insercion_monto = $conexion->query("UPDATE cuenta SET saldo_final = $saldo_final_cuenta WHERE id_cuenta = $id_cuenta_suma");
                                if(!$sql_insercion_monto) {
                                    $valid = false;
                                    throw new Exception("Error en la inserción: " . $conexion->error);
                                }
                            } else {
                                $valid = false;
                                throw new Exception("Saldo negativo en la cuenta: $id_cuenta_suma");
                            }
                        }
                    } else {
                        $valid = false;
                        throw new Exception("Error en la consulta hacia la cuenta: " . $conexion->error);
                    }
                }
            
                // Si todas las operaciones se realizaron con éxito, confirmar la transacción
                if($valid) {
                    $conexion->commit();
                    echo "<div class='alert alert-success'>Todas las cuentas han sido actualizadas correctamente</div>";
                }
            
            } catch (Exception $e) {
                // Si ocurre algún error, deshacer todas las operaciones
                $conexion->rollback();
                header("Location: ../registrarAsiento.php?error=saldo_negativo");
                exit();
            }
            $conexion->autocommit(true);
              $id_asiento=0;
              if($sql_insercion_monto){                                                                                                               //Actualizar la tabla asiento
                $sql_insertar_asiento = $conexion->query("INSERT INTO asiento (fecha, descripcion, id_usuario) VALUES ('$fecha', '$desc', $id_usuario)");
                if($sql_insertar_asiento){
                  $id_asiento = $conexion->insert_id;
                  echo "Asiento añadido exitosamente. Numero de asiento: ". $id_asiento."<br>";
                  }else{
                    echo "Error al añadir asiento:". $conexion->error;}       
                }
                  
            }elseif($saldo_asiento_total!=0){
              header("Location: ../registrarAsiento.php?error=balance_incorrecto");
              exit();}
                
          }else{
            echo "Hubo un error en la consulta de saldo asiento.";}

          if ($saldo_asiento_total==0 && $sql_insercion_monto===true){                                                                    //Actualizar el la tabla registrar_asiento
            $sql_lineas_asiento_saldo_parcial = $conexion->query("SELECT * FROM lineas_asiento");
            while ($datos_de_linea = $sql_lineas_asiento_saldo_parcial->fetch_assoc()) {
              $id_cuenta_linea = $datos_de_linea["id_cuenta"];
              $sql_tipo_cuenta = $conexion->query("SELECT saldo_final, tipo 
                                                    FROM cuenta 
                                                    WHERE id_cuenta = $id_cuenta_linea");
              $tipo_cuenta = $sql_tipo_cuenta->fetch_assoc();
              $sql_saldo_parcial = $conexion->query("SELECT saldo_parcial 
                                                      FROM registrar_asiento 
                                                      where id_cuenta = $id_cuenta_linea");
              $datos_saldo_parcial = $sql_saldo_parcial->fetch_assoc();
              $saldo_parcial = $datos_saldo_parcial;
              $tipo = $tipo_cuenta["tipo"];
              $saldo_final = $tipo_cuenta["saldo_final"];
              echo "El saldo final es de: ".$saldo_final. " de la cuenta ID: ". $id_cuenta_linea ."<br>";
              $saldo_debe = $datos_de_linea["debe"];
              $saldo_haber = $datos_de_linea["haber"];
              if ($saldo_haber==0){                                   //Si es en el debe suma

                if($tipo=="Ac" || $tipo=="R-"){                       //Si es activo R- //Primero verificar si es 0 o no.
                  if($saldo_final == 0){
                    $saldo_parcial = 0;
                  }elseif($saldo_final != 0){
                    $saldo_parcial = $saldo_final;}
                  $sql_registrar_asiento = $conexion->query("INSERT INTO registrar_asiento (id_asiento, id_cuenta, debe, haber, saldo_parcial) VALUES ($id_asiento,$id_cuenta_linea, $saldo_debe, $saldo_haber, $saldo_parcial)"); 
                }else{

                  if($saldo_final==0){
                    $saldo_parcial = 0;
                  }elseif($saldo_final!=0){
                    $saldo_parcial = $saldo_final;}
                  $sql_registrar_asiento = $conexion->query("INSERT INTO registrar_asiento (id_asiento, id_cuenta, debe, haber, saldo_parcial) VALUES ($id_asiento,$id_cuenta_linea, $saldo_debe, $saldo_haber, $saldo_parcial)"); 
                }                                        

              }elseif($saldo_debe==0){                     //Si es en el haber resta
                if($tipo=="Pa" || $tipo=="R+" || $tipo=="Pm"){ //Si es pasivo R+ Pm
                  if($saldo_final==0){
                    $saldo_parcial = 0;
                  }elseif($saldo_final!=0){
                    $saldo_parcial = $saldo_final;}
                  $sql_registrar_asiento = $conexion->query("INSERT INTO registrar_asiento (id_asiento, id_cuenta, debe, haber, saldo_parcial) VALUES ($id_asiento,$id_cuenta_linea, $saldo_debe, $saldo_haber, $saldo_parcial)"); 
                }else{
                  if($saldo_final==0){
                    $saldo_parcial = 0;
                  }elseif($saldo_final!=0){
                    $saldo_parcial = $saldo_final;}
                  $sql_registrar_asiento = $conexion->query("INSERT INTO registrar_asiento (id_asiento, id_cuenta, debe, haber, saldo_parcial) VALUES ($id_asiento,$id_cuenta_linea, $saldo_debe, $saldo_haber, $saldo_parcial)"); 
                }
              }
              else{
                if($saldo_final==0){
                  $saldo_parcial = 0;               //Aca puede quedar negativo?
                }elseif($saldo_final!=0){
                  $saldo_parcial = $saldo_final;}
                $sql_registrar_asiento = $conexion->query("INSERT INTO registrar_asiento (id_asiento, id_cuenta, debe, haber, saldo_parcial) VALUES ($id_asiento,$id_cuenta_linea, $saldo_debe, $saldo_haber, $saldo_parcial)");
              }

              if ($sql_registrar_asiento){
                echo "El asiento ha sido registrado correctamente / ID de asiento:". $id_asiento. "<br>";
                $eliminar_lineas = $conexion->query("DELETE FROM lineas_asiento");
              }else{
                echo "Hubo un error a la hora de insertar el asiento: ID de cuenta: ". $id_cuenta_linea. "<br>";
              }
              
        } 
            }
            header("Location: ../asientos.php?success=asiento_registrado");
            exit();
          }elseif(!empty($_POST["fecha"]) || !empty($_POST["descripcion"])){
            header("Location: ../registrarAsiento.php?error=campos_vacios");
            exit();}
          
            //Situacion ilogica sumar debe y haber
?>

