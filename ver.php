<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros</title>
</head>

<body>
    <?php
    $conexion = mysqli_connect("localhost:3307", "root", "", "db_php") or
      die("Problemas con la conexión");
      echo 
        "<table border = '1'>
            <td>Codigo de la cuenta</td>
            <td>Codigo del asiento</td>
            <td>Debe</td>
            <td>Haber</td>
            <td>Saldo parcial</td>
            <td>Modificar</td>
        </table>";

    $registros = mysqli_query($conexion, "select *
                              from registrar_asiento") or
                              die("Problemas en el select:" . mysqli_error($conexion));
  
    while ($reg = mysqli_fetch_array($registros)) {
      echo "<tr><td>" . $reg['id_cuenta'] . "</td>";
      echo "<td>" . $reg['id_asiento'] . "</td>";
      echo "<td>" . $reg['debe'] . "</td>";
      echo "<td>" . $reg['haber'] . "</td>";
      echo "<td>" . $reg['saldo_parcial'] . "</td></tr>";
    }
    echo "</table>";
    mysqli_close($conexion);
    ?>
    
    
    
</body>
</html>