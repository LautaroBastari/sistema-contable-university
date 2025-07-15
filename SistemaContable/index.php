<?php
session_start();
require 'database.php';

if (isset($_SESSION['user_id'])) {
    $records = $conn->prepare('SELECT id_usuario, email, password 
                                FROM usuarios 
                                WHERE id_usuario = :id_usuario');
    $records->bindParam(':id_usuario', $_SESSION['user_id']);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);

    $user = null;
    if (is_array($results) && count($results) > 0) {
        $user = $results;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap y otros recursos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .hover-shadow:hover {
            transform: scale(1.03);
            transition: 0.3s ease;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body class="bg-light text-dark">
    <?php if (!empty($user)): ?>
        <?php include './nav.php'; ?>
        <div class="container py-5">
            <div class="text-center mb-5">
                <img src="/php-login/assets/mk-asociados-low-resolution-logo-color-on-transparent-background.png"
                     alt="MK Asociados" width="250" class="mb-3">
                <h1 class="display-6">Bienvenido <strong><?= htmlspecialchars($user['email']) ?></strong></h1>
                <p class="lead text-muted">Has ingresado correctamente. Puedes realizar estas operaciones:</p>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <!-- Reutilizá este bloque cambiando icono/texto/enlace -->
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm hover-shadow text-center">
                        <div class="card-body">
                            <i class="bi bi-table fs-2 text-primary mb-2"></i>
                            <h5 class="card-title">Asientos contables</h5>
                            <a href="asientos.php" class="stretched-link"></a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm hover-shadow text-center">
                        <div class="card-body">
                            <i class="bi bi-table fs-2 text-info mb-2"></i>
                            <h5 class="card-title">Plan de cuentas</h5>
                            <a href="plandecuentas.php" class="stretched-link"></a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm hover-shadow text-center">
                        <div class="card-body">
                            <i class="bi bi-book fs-2 text-success mb-2"></i>
                            <h5 class="card-title">Libro Diario</h5>
                            <a href="libroDiario.php" class="stretched-link"></a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm hover-shadow text-center">
                        <div class="card-body">
                            <i class="bi bi-journal fs-2 text-warning mb-2"></i>
                            <h5 class="card-title">Libro Mayor</h5>
                            <a href="libroMayor.php" class="stretched-link"></a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm hover-shadow text-center">
                        <div class="card-body">
                            <i class="bi bi-briefcase fs-2 text-danger mb-2"></i>
                            <h5 class="card-title">Registro de Ventas</h5>
                            <a href="ventas.php" class="stretched-link"></a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm hover-shadow text-center">
                        <div class="card-body">
                            <i class="bi bi-people fs-2 text-secondary mb-2"></i>
                            <h5 class="card-title">Clientes</h5>
                            <a href="clientes.php" class="stretched-link"></a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm hover-shadow text-center">
                        <div class="card-body">
                            <i class="bi bi-box-seam fs-2 text-primary mb-2"></i>
                            <h5 class="card-title">Stock</h5>
                            <a href="stock.php" class="stretched-link"></a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm hover-shadow text-center">
                        <div class="card-body">
                            <i class="bi bi-bar-chart-line fs-2 text-success mb-2"></i>
                            <h5 class="card-title">Informe de Ventas</h5>
                            <a href="informeVentas.php" class="stretched-link"></a>
                        </div>
                    </div>
                </div>

                <!-- Cierre sesión -->
                <div class="col">
                    <div class="card h-100 border border-danger shadow-sm text-center">
                        <div class="card-body">
                            <i class="bi bi-box-arrow-right fs-2 text-danger mb-2"></i>
                            <h5 class="card-title">Cerrar Sesión</h5>
                            <a href="logout.php" class="stretched-link"></a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    <?php else: ?>
        <div class="container py-5 text-center">
            <div class="alert alert-warning shadow-sm">
                <h2 class="mb-3">¡Bienvenido a nuestro Sistema Contable!</h2>
                <p class="mb-3">Por favor, ingresa a tu cuenta o registrate si aún no tenés una.</p>
                <a href="login.php" class="btn btn-primary me-2">Ingresar</a>
                <a href="signup.php" class="btn btn-outline-primary">Registrarme</a>
            </div>
        </div>
    <?php endif; ?>

</body>
</html>
