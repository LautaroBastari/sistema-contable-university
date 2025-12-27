<?php 
    $conexion= new mysqli("localhost", "root", "", "php_login_database");
    $conexion->set_charset("utf8");
    //Tuvimos que agregar la conexion a la BD porque no hacia la conexion 
    //Con el include.
    if(!empty($_GET["id"])){
        $id=$_GET["id"];
        $sql = $conexion->query("DELETE FROM cuenta WHERE id_cuenta=$_GET[id]");
        if($sql==1){
            echo "<div class='alert alert-sucess text-center'>Cuenta eliminada correctamente.</div>";
            }else{
                echo "<div class='alert alert-warning'>Error al eliminar la cuenta.</div>";
            }
    }

?>