<?php
session_start();
include "conexionPDC.php";
$id_usuario=$_SESSION['user_id'];
$sql_cuenta = $conexion -> query("SELECT * FROM cuenta");
$sql_registrar_asiento = $conexion -> query("SELECT * FROM registrar_asiento");
$saldo_final_sumado= 0; //No puede dar negativo
$saldo_debe_final=0;
$saldo_haber_final=0;

if ($sql_cuenta !== false && $sql_registrar_asiento !== false){  //Confirmar que los saldos no sean negativos
    while($datos_cuenta = $sql_cuenta->fetch_assoc()){
        $saldo_final  =  $datos_cuenta["saldo_final"];
        $saldo_final_sumado += $saldo_final;
    }
    if ($saldo_final_sumado < 0){
        echo "El saldo final negativo, no se puede efectuar el registro";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
        }else{
    }
    while($datos = $sql_registrar_asiento->fetch_assoc()){
        if($datos["debe"] == 1){
            $saldo_debe_final += $datos["saldo_parcial"];
        }

        elseif($datos["haber"] == 1){
            $saldo_haber_final += $datos["saldo_parcial"];
        }

    }
    $saldo_final_restado=$saldo_debe_final-$saldo_haber_final;
    echo "Saldo debe final: ". $saldo_debe_final;
    echo "Saldo haber final: ". $saldo_haber_final;
    echo "Saldo final final: ". $saldo_final_restado;
    if ($saldo_final_restado==0){
        echo "<div class='alert alert-success'>El asiento se ha registrado correctamente.</div>";
        $sql_registrar_asiento_delete = $conexion -> query("DELETE FROM registrar_asiento");
    }else{
        echo "<div class='alert alert-danger'>El asiento no está correctamente balanceado </div>";
    }

}else{
    echo "Error en la consulta de verificar asientos";
}


//Restar debe y haber
?>