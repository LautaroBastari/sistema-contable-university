<?php
error_reporting(E_ERROR | E_PARSE); // Desactivar los avisos
session_start();
$conexion = mysqli_connect("localhost", "root", "", "php_login_database") or die("Problemas con la conexión");

if (!empty($_POST["cuenta"]) && !empty($_POST["monto"])) {
  $id_usuario = $_SESSION['user_id'];
  $nombre_cuenta = $_POST["cuenta"];
  $monto = $_POST["monto"];
  $permitir = true;

  $nombre_cuenta = mysqli_real_escape_string($conexion, $nombre_cuenta);
  $sql_nombre_cuenta = $conexion->query("select id_cuenta from cuenta where nombre='$nombre_cuenta'");

  if ($sql_nombre_cuenta) {
    $fila = $sql_nombre_cuenta->fetch_assoc();
    if ($fila) {
      $id_cuenta = $fila['id_cuenta'];
      echo "<p>El ID de la cuenta es: $id_cuenta</p>";
    } else {
      echo "<p>No se encontró la cuenta.</p>";
    }
  } else {
    echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
  }

  echo "<p>El nombre de la cuenta es: $nombre_cuenta</p>";

  $sql_extraer_numero_cuenta = $conexion->query("SELECT nro_cuenta FROM cuenta WHERE nombre='$nombre_cuenta'");

  $sql_consulta_cuenta = $conexion->query("SELECT nro_cuenta, id_cuenta FROM cuenta WHERE nombre='$nombre_cuenta'");

  if ($sql_consulta_cuenta) {
    $datos_cuenta = $sql_consulta_cuenta->fetch_assoc();

    if ($datos_cuenta) {
      $nro_cuenta = $datos_cuenta['nro_cuenta'];
      $id_cuenta = $datos_cuenta['id_cuenta'];
      $tipo = $datos_cuenta['tipo'];
      $saldo_final = $datos_cuenta['saldo_final'];
      $accion = $_POST["accion"];
      if (($tipo == "Ac" || $tipo == "R-") && ($accion === "inputHaber" && $monto > $saldo_final)) {
        $permitir = false;
      } elseif (($tipo == "Pa" || $tipo == "R+" || $tipo == "Pm") && ($accion === "inputDebe" && $monto > $saldo_final)) {
        $permitir = false;
      } else {
        echo "<p>No se encontró la cuenta.</p>";
      }
    }
  } else {
    echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
  }

  if ($permitir == true) {
    $sql_extraer_id_usuario = $conexion->query("select id_usuario from usuarios where id_usuario='$id_usuario'");
    if ($sql_extraer_id_usuario) {
      $datos_id_usuario = $sql_extraer_id_usuario->fetch_assoc();
      if ($datos_id_usuario) {
        $id_usuario = $datos_id_usuario['id_usuario'];
        echo "<p>El ID del usuario es: $id_usuario</p>";
      } else {
        echo "<p>No se encontró el ID de la cuenta.</p>";
      }


      if (!empty($_POST["accion"])) {
        $accion = $_POST["accion"];

        if ($accion === "inputDebe") {

          $sql_consulta_cuenta = $conexion->query("select saldo_final from cuenta where id_cuenta = $id_cuenta");
          $sql = $conexion->query("INSERT INTO lineas_asiento(id_cuenta, debe, haber) VALUES ($id_cuenta, $monto ,0)");
          if ($sql == 1) {
            echo "<div class='alert alert-success'>Linea ingresada correctamente</div>";
          } else {
            echo "<div class='alert alert-danger'>Ha ocurrido un error al registrar el movimiento: " . $conexion->error . "</div>";
          }

        } elseif ($accion === "inputHaber") {

          $sql = $conexion->query("INSERT INTO lineas_asiento(id_cuenta, debe, haber) VALUES ($id_cuenta,0 ,$monto)");
          if ($sql == 1) {
            echo "<div class='alert alert-success'>Linea ingresada correctamente</div>";
          } else {
            echo "<div class='alert alert-danger'>Ha ocurrido un error al registrar el movimiento: " . $conexion->error . "</div>";
          }
        }
      }
    }
  }
  header("Location: ../registrarAsiento.php");
} elseif (!empty($_POST["cuenta"]) || !empty($_POST["monto"])) {
  echo "<div class='alert alert-danger'>Existen campos vacios<a href='registrarAsiento.php'>Volver atrás</a></div>";
} elseif ($_POST["monto"] < 0) {
  echo "<div class='alert alert-danger'>El saldo no puede ser negativo</div>";
}


?>