<?php
include "conexionPDC.php";

if (!empty($_POST["nombre"]) && !empty($_POST["codigo"]) && !empty($_POST["dni"]) && !empty($_POST["cuit"]) && !empty($_POST["cond_fiscal"]) && !empty($_POST["direccion"]) && !empty($_POST["email"]) && !empty($_POST["telefono"])) {
    $nombre = $_POST["nombre"];
    $codigo = $_POST["codigo"];
    $dni = $_POST["dni"];
    $cuit = $_POST["cuit"];
    $cond_fiscal = $_POST["cond_fiscal"];
    $direccion = $_POST["direccion"];
    $email = $_POST["email"];
    $telefono = $_POST["telefono"];
    $saldo = $_POST["saldo"];
    $sql_registrar_cliente = $conexion -> query ("INSERT INTO clientes (nombre, codigo,dni,cuit,cond_fiscal,direccion, email, telefono, saldo, limite) VALUES ('$nombre',$codigo,$dni,$cuit,'$cond_fiscal','$direccion','$email','$telefono', $saldo, 40000)");
    if ($sql_registrar_cliente){
        $conexion->insert_id;
        echo "<div class='alert alert-success'>Cliente registrado correctamente.</div>";
    }else{
        echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
    }
}
?>
