<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Navbar Mejorada</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
  <style>
    .navbar-custom {
      background: linear-gradient(90deg, #dbeafe 0%, #e0f2fe 100%);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand img {
      transition: transform 0.3s ease;
    }

    .navbar-brand img:hover {
      transform: scale(1.05);
    }

    .nav-link {
      font-weight: bold;
      color: #0d1b2a;
      transition: color 0.2s;
    }

    .nav-link:hover {
      color: #0ea5e9;
    }

    .dropdown-menu {
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .dropdown-item:hover {
      background-color: #e0f2fe;
      color: #0ea5e9;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-custom sticky-top py-0">
    <div class="container-fluid">
      <a class="navbar-brand ms-2 d-flex align-items-center" href="/php-login">
        <img src="/php-login/assets/mk-asociados-low-resolution-logo-color-on-transparent-background.png"
          alt="MK Asociados" width="200" />
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav me-3">
          <!-- Asientos Contables -->
          <li class="nav-item dropdown px-2">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              <i class="bi bi-journal-text me-1"></i> Asientos Contables
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="asientos.php">Listado de Asientos</a></li>
              <li><a class="dropdown-item" href="registrarAsiento.php">Registrar Asiento</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="plandecuentas.php">Plan de Cuentas</a></li>
            </ul>
          </li>

          <!-- Libros Contables -->
          <li class="nav-item dropdown px-2">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              <i class="bi bi-book me-1"></i> Libros Contables
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="libroDiario.php">Libro Diario</a></li>
              <li><a class="dropdown-item" href="libroMayor.php">Libro Mayor</a></li>
            </ul>
          </li>

          <!-- Sistema de Ventas -->
          <li class="nav-item dropdown px-2">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              <i class="bi bi-cart me-1"></i> Sistema de Ventas
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="ventas.php">Ventas</a></li>
              <li><a class="dropdown-item" href="stock.php">Administración de Stock</a></li>
              <li><a class="dropdown-item" href="clientes.php">Administración de Clientes</a></li>
              <li><a class="dropdown-item" href="informeVentas.php">Informe de Ventas</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
