<?php 

/** Esto va mas abajo **/
            /**if ($sql_consulta_cuenta) {
              $datos_cuenta = $sql_consulta_cuenta->fetch_assoc();
              $saldo_parcial = $datos_cuenta['saldo_final'];
              echo "El saldo parcial es:". $saldo_parcial;
            }else{
              echo "Falló la consulta a la base de datos en 'SQL CONSULTA CUENTA'"; }
            $sql = $conexion -> query("insert into registrar_asiento(id_cuenta, id_asiento, debe, haber, saldo_final, monto) values($id_cuenta, $id_asiento, $monto ,0, $saldo_parcial, $monto)");
            
            /**$sql_monto_con
             * sulta = $conexion->query("SELECT debe, haber FROM registrar_asiento WHERE id_cuenta= $id_cuenta AND id_asiento =$id_asiento");
            if ($sql_monto_consulta) {
              $datos_monto = $sql_monto_consulta->fetch_assoc();
              $monto_final = $datos_monto->debe - $datos_monto->haber;
            }
            $sql_monto_insercion**/ //mucha vuelta, como mucho despues restara fuera de este if

$sql_consulta_cuenta =  $conexion->query("select saldo_final from cuenta where id_cuenta = $id_cuenta");
            if ($sql_consulta_cuenta) {
              $datos_cuenta = $sql_consulta_cuenta->fetch_assoc();
              $saldo_parcial = $datos_cuenta['saldo_final'];
              echo "El saldo parcial es:". $saldo_parcial;
            }else{
              echo "Falló la consulta a la base de datos en 'SQL CONSULTA CUENTA'"; }
            
            $sql = $conexion -> query("insert into registrar_asiento(id_cuenta, id_asiento, debe, haber, saldo_final, monto) values($id_cuenta, $id_asiento,0 , $monto, $saldo_parcial, $monto)");
                  if ($sql == 1) {
                    echo "<div class='alert alert-success'>La cuenta se ha registrado correctamente.</div>";
                  } else {
                    echo "<div class='alert alert-danger'>Ha ocurrido un error al registrar la cuenta: " . $conexion->error . "</div>";}
          
        
        //Montos
        //Conexion a registrar_asiento para los montos
        /**$sql_monto = $conexion->query("SELECT *
                              FROM registrar_asiento INNER JOIN cuenta
                              ON registrar_asiento.id_cuenta = cuenta.id_cuenta");**/

        $sql_monto = $conexion->query("SELECT *
                                      FROM registrar_asiento");

        $sql_cuenta = $conexion->query("SELECT id_cuenta, tipo, saldo_final
                                        FROM cuenta
                                        where id_cuenta = $id_cuenta");

      if ($sql_monto !== false && $sql_cuenta !== false){
          $datos_monto = $sql_monto->fetch_object();
          $datos_cuenta = $sql_cuenta->fetch_object();
          //if($datos_monto->id_cuenta === $datos_cuenta->id_cuenta){
            if($_POST["debe"]){
              echo "El tipo de cuenta es: ". $datos_cuenta->tipo;
              if(($datos_cuenta->tipo == "Ac") or ($datos_cuenta->tipo == "R-")){
                $sql_insercion_monto = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $monto WHERE id_cuenta = $id_cuenta");
                }else{
                $sql_insercion_monto = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final - $monto WHERE id_cuenta = $id_cuenta");}

            }elseif($_POST["haber"]){
              if(($datos_cuenta->tipo == "Pa") or ($datos_cuenta->tipo == "R+") or ($datos_cuenta->tipo == "Pm")){
                $sql_insercion_monto = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final + $monto WHERE id_cuenta = $id_cuenta");
                
              }else{
                $sql_insercion_monto = $conexion->query("UPDATE cuenta SET saldo_final = saldo_final - $monto WHERE id_cuenta = $id_cuenta");}  
            }
         //if id's
      }else{
        echo "Hubo un error en al menos una de las consultas.";
            if ($sql_monto === false) {
                echo "Error en la consulta de registrar_asiento: " . $conexion->error;}
            if ($sql_cuenta === false) {
                echo "Error en la consulta de cuenta: " . $conexion->error;}
            }
    
?>