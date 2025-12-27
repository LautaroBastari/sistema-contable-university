<?php
require 'database.php';

session_start();
if (!empty($_POST['email']) && !empty($_POST['password'])) {

    $records = $conn->prepare('SELECT id_usuario, email, password FROM usuarios WHERE email = :email');
    $records->bindParam(':email', $_POST['email']);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);
    $message = ''; //Linea 13

    if ($results) {
        if (($_POST['password'] == $results['password'])) {
            $_SESSION['user_id'] = $results['id_usuario'];
            $_SESSION['email'] = $results['email'];
            $user_id = $_SESSION['user_id'];
            $email_usuario = $results['email'];
            header('Location:/php-login');
            exit;
        } else {
            $message = 'La contraseña proporcionada es incorrecta.';
        }
    }
} elseif (!empty($_POST['email']) || !empty($_POST['password'])) {
    echo "<div class='alert alert-danger '>Existen campos vacios</div>";
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="assets/Css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    

</head>

<body class="bg-light">
    <div class="container mt-5 mb-5 text-center">
        <a class="navbar-brand ms-2" href="/php-login">
            <img src="/php-login/assets/mk-asociados-low-resolution-logo-color-on-transparent-background.png"
                alt="MK Asociados" href="index.php" width="250" height="50" style="margin-top:20px ">
        </a class="navbar-brand ms-2" href="/php-login">
    </div>
    <div class="container-lg border border-primary-dark text-center bg-primary-subtle" style="width:50%">
        <h1 class="mt-4"><b>Inicia Sesión</b></h1>
        <span>o <a href="signup.php">Registrate</a></span>

        <hr class="container mt-4 mb-4 d-flex justify-content-center" width="70%">
        </hr>

        <?php
        if (!empty($message)):
            ?>
            <p>
                <?= $message ?>
            </p>
            <?php
        endif;
        ?>
        <div class="container mb-5">
            <form action="login.php" method="post">
                <input type="text" name="email" placeholder="Ingresa tu email">
                <input type="password" name="password" placeholder="Ingresa tu contraseña">
                <input type="submit" name="enviar" value="Enviar">
            </form>
        </div>
    </div>
</body>

</html>