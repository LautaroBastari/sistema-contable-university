<?php
    $conexion= new mysqli("localhost", "root", "", "php_login_database");
    $conexion->set_charset("UTF8");
    // $conexion = mysqli_connect("localhost", "root", "", "db_php") or
    //  die("Problemas con la conexión");

    //$registros = mysqli_query($conexion, "select nombre, tipo, nro_cuenta, recibe_saldo from cuenta") or
      //                      die("Problemas en el select:" . mysqli_error($conexion));
  
    
    //mysqli_close($conexion);
    ?>