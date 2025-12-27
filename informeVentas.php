<?php
session_start();
$id_usuario = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Informe de Ventas</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
  <style>
    h1.titulo {
      text-align: center;
      font-weight: 700;
      margin-bottom: 1rem;
      border-bottom: 2px solid #0d6efd;
      display: inline-block;
      padding-bottom: 0.2rem;
    }

    .btn-volver {
      border: none;
      background-color: transparent;
      font-size: 1.5rem;
      margin-right: 1rem;
      color: #0d6efd;
    }

    .btn-volver:hover {
      color: #084298;
    }

    .table th,
    .table td {
      vertical-align: middle;
    }

    .table-info {
      background-color: #d1ecf1 !important;
    }

    tbody tr:hover {
      background-color: #f1f1f1;
    }

    .resumen {
      background-color: #f8f9fa;
      font-weight: bold;
    }

    .text-azul {
      color: #0d6efd;
    }
  </style>
</head>

<body class="bg-light">
  <?php include './nav.php'; ?>

  <div class="container bg-white p-4 mt-5 mb-5 rounded shadow">
    <div class="d-flex align-items-center mb-3">
      <button class="btn-volver" onclick="history.back()">
        <i class="bi bi-caret-left-fill"></i>
      </button>
      <h1 class="titulo mx-auto">Informe de Ventas</h1>
    </div>

    <div class="table-responsive mt-4">
      <table class="table table-bordered table-hover text-center align-middle">
        <thead class="table-info">
          <tr>
            <th>Rubro</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad (hoy)</th>
            <th>Presupuesto (hoy)</th>
            <th>Cantidad (mes)</th>
            <th>Presupuesto (mes)</th>
            <th>Cumplimiento% (mes)</th>
            <th>Importe Vendido</th>
          </tr>
        </thead>
        <tbody>
          <?php
          include "conexionPDC.php";
          $consulta_rubros = $conexion->query("SELECT DISTINCT rubro FROM stock");

          $presupuesto_mensual = 1000;
          $presupuesto_diario = $presupuesto_mensual / 30;

          $presupuesto_diario_total = 0;
          $cantidad_total = 0;
          $importe_total = 0;
          $presupuesto_total = 0;
          $importe_vendido_total = 0;
          $cantidad_total_hoy = 0;
          $importe_total_hoy = 0;

          if ($consulta_rubros) {
            while ($datos_rubros = $consulta_rubros->fetch_assoc()) {
              $rubro = $datos_rubros['rubro'];
              $ventas_por_producto = [];
              $ventas_por_producto_hoy = [];

              $consulta_producto = $conexion->query("SELECT v.producto, v.id_producto, v.cantidad, v.coste_total, s.valor_venta
                    FROM venta v
                    INNER JOIN stock s ON v.id_producto = s.id_articulo
                    WHERE s.rubro = '$rubro' AND MONTH(v.fecha) = MONTH(CURRENT_DATE()) AND YEAR(v.fecha) = YEAR(CURRENT_DATE())");

              $consulta_dia = $conexion->query("SELECT v.producto, v.id_producto, v.cantidad, v.coste_total, s.valor_venta
                    FROM venta v
                    INNER JOIN stock s ON v.id_producto = s.id_articulo
                    WHERE s.rubro = '$rubro' AND DAY(v.fecha) = DAY(CURRENT_DATE()) AND MONTH(v.fecha) = MONTH(CURRENT_DATE()) AND YEAR(v.fecha) = YEAR(CURRENT_DATE())");

              while ($datos_ventas = $consulta_producto->fetch_assoc()) {
                $producto = $datos_ventas['producto'];
                $precio_venta = $datos_ventas['valor_venta'];
                $cantidad = $datos_ventas['cantidad'];
                $precio_total = $datos_ventas['coste_total'];

                $clave = $producto . "_" . $precio_venta;

                if (!isset($ventas_por_producto[$clave])) {
                  $ventas_por_producto[$clave] = [
                    'producto' => $producto,
                    'precio_venta' => $precio_venta,
                    'cantidad' => 0,
                    'precio_total' => 0
                  ];
                  $presupuesto_total += $presupuesto_mensual;
                  $presupuesto_diario_total += $presupuesto_diario;
                }

                $ventas_por_producto[$clave]['cantidad'] += $cantidad;
                $ventas_por_producto[$clave]['precio_total'] += $precio_total;
                $cantidad_total += $cantidad;
                $importe_vendido_total += $precio_total;
                $importe_total += $precio_venta;
              }

              while ($datos_ventas_hoy = $consulta_dia->fetch_assoc()) {
                $producto_hoy = $datos_ventas_hoy['producto'];
                $precio_venta_hoy = $datos_ventas_hoy['valor_venta'];
                $cantidad_hoy = $datos_ventas_hoy['cantidad'];
                $precio_total_hoy = $datos_ventas_hoy['coste_total'];

                $clave_hoy = $producto_hoy . "_" . $precio_venta_hoy;

                if (!isset($ventas_por_producto_hoy[$clave_hoy])) {
                  $ventas_por_producto_hoy[$clave_hoy] = [
                    'producto' => $producto_hoy,
                    'precio_venta' => $precio_venta_hoy,
                    'cantidad' => 0,
                    'precio_total' => 0
                  ];
                }

                $ventas_por_producto_hoy[$clave_hoy]['cantidad'] += $cantidad_hoy;
                $ventas_por_producto_hoy[$clave_hoy]['precio_total'] += $precio_total_hoy;
                $cantidad_total_hoy += $cantidad_hoy;
                $importe_total_hoy += $precio_total_hoy;
              }

              $first_row = true;
              foreach ($ventas_por_producto as $clave => $data) {
                $producto = $data['producto'];
                $precio_venta = $data['precio_venta'];
                $cantidad = $data['cantidad'];
                $precio_total = $data['precio_total'];
                $cantidad_hoy = $ventas_por_producto_hoy[$clave]['cantidad'] ?? 0;
                $precio_total_hoy = $ventas_por_producto_hoy[$clave]['precio_total'] ?? 0;
                $cumplimiento = ($precio_total / $presupuesto_mensual) * 100;
                ?>
                <tr>
                  <?php if ($first_row): ?>
                    <td rowspan="<?= count($ventas_por_producto) ?>"><?= $rubro ?></td>
                    <?php $first_row = false;
                  endif; ?>
                  <td><?= $producto ?></td>
                  <td><?= $precio_venta ?></td>
                  <td><?= $cantidad_hoy ?></td>
                  <td><?= $presupuesto_diario ?></td>
                  <td><?= $cantidad ?></td>
                  <td><?= $presupuesto_mensual ?></td>
                  <td><?= number_format($cumplimiento, 2) . "%" ?></td>
                  <td><?= $precio_total ?></td>
                </tr>
              <?php
              }
            }
          } else {
            echo "<div class='alert alert-warning'>Error al ejecutar la consulta: " . $conexion->error . "</div>";
          }

          $resultado = ($presupuesto_total != 0) ? ($importe_vendido_total / $presupuesto_total) * 100 : 0;
          ?>
          <tr class="resumen">
            <td class="text-azul">Total productos:</td>
            <td>-</td>
            <td>-</td>
            <td><?= $cantidad_total_hoy ?></td>
            <td>-</td>
            <td><?= $cantidad_total ?></td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
          </tr>
          <tr class="resumen">
            <td class="text-azul">Total importe:</td>
            <td>-</td>
            <td><?= $importe_total ?></td>
            <td>-</td>
            <td><?= $presupuesto_diario_total ?></td>
            <td>-</td>
            <td><?= $presupuesto_total ?></td>
            <td><?= round($resultado, 2) . "%" ?></td>
            <td><?= $importe_vendido_total ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
