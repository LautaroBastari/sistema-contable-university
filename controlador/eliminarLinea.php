<?php 
    include "../conexionPDC.php";
    echo $_GET["nro_asiento"];
    if(!empty($_GET["nro_asiento"])){
        $nro_asiento=$_GET["nro_asiento"];
        echo "Nro asiento: ".$nro_asiento. "<br>";
        $sql = $conexion->query("DELETE FROM lineas_asiento WHERE nro_asiento = $nro_asiento");
        if($sql==1){
            echo "<div class='alert alert-sucess text-center'>Cuenta eliminada correctamente.</div>";
            }else{
                echo "<div class='alert alert-warning'>Error al eliminar la cuenta.". $conexion->error."</div>";
            }
            header("Location: ../registrarAsiento.php");
            exit;
    }
?>