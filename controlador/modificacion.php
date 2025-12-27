<?php
if (!empty($_POST["registrado"])){ //Validar que se presiono el boton
    //if (!empty($_POST["nombre"]) && !empty($_POST["tipo"]) && !empty($_POST["numero_cuenta"]) && !empty($_POST["recibe_saldo"])){    
           //Validar que los campos de la BD no esten vacios
            //En el caso que no esten vacios, voy a almacenarlos para editarlos en la BD
    $permitir=true;
    $id=$_POST["id"];
    $nombre=$_POST["nombre"];
    $tipo=$_POST["tipo"];
    $numero_cuenta=$_POST["numero_cuenta"];
    if (($_POST["recibe_saldo"]==0) or ($_POST["recibe_saldo"]==1)){
        $recibe_saldo=$_POST["recibe_saldo"];
        }else{
            echo "Solo puede ingresar el numero 0 o 1";
            $permitir=false;
    }
    $sql=$conexion->query("UPDATE cuenta SET nombre='$nombre', tipo='$tipo', nro_cuenta=$numero_cuenta, recibe_saldo=$recibe_saldo WHERE id_cuenta=$id ");  //Con comillas simples los arrays/caracteres
    if ($sql==1){
        header("Location: plandecuentas.php"); //redirigir
    }else{
        echo "<div class='alert alert-danger text-center'>Error al modificar la cuenta</div>";
    }
}
//}

?>