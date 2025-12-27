<?php 

    if(!empty($_POST["registrar"])){
        if(!empty($_POST["nombre"]) and !empty($_POST["nro_cuenta"])){
            $permitir=true;
            $nombre =$_POST["nombre"];
            $tipo = $_POST["tipo"];
            $nro_cuenta=$_POST["nro_cuenta"];
            $recibe_saldo=$_POST["recibe_saldo"];
            $sql = $conexion -> query("select nro_cuenta from cuenta");
            while ($datos = $sql->fetch_object()){
                if ($datos->nro_cuenta == $nro_cuenta){
                    $permitir=false;
                }
            }
            if ($permitir==true){
                $sql=$conexion->query("insert into cuenta(nombre, tipo, nro_cuenta, recibe_saldo) values('$nombre','$tipo',$nro_cuenta,$recibe_saldo);");
                if($sql==1){
                    echo "<div class='alert alter-success'>La cuenta se ha registrado correctamente.</div>";
                    }else{
                        echo "<div class='alert alter-danger'>Ha ocurrido un error al registrar la cuenta.</div>";
                    }
            }else{
                echo "<div class='alert alter-waning text-center'>El numero de cuenta ya existe, reingresar.</div>";
            }
        }else{
            echo "<div class='alert alter-waning text-center'>Hay campos vacios.</div>";
            }
    }

    /**<div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Tipo</label>
                <input type="text" class="form-control" name="tipo">
            </div>**/

    /**<div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Recibe saldo</label>
                <input type="number" class="form-control" name="recibe_saldo">
            </div>**/
      
?>