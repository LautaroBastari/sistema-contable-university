<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require 'database.php';

$message = "";

if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['confirm_password'])) { //Si no estan vacios,agregar
    if ($_POST['password'] == $_POST['confirm_password']) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $rol = "cliente";
        $sql = "INSERT INTO usuarios (email, password, rol) VALUES (:email, :password, :rol)";
        $statement = $conn->prepare($sql);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':password', $password);
        $statement->bindParam(':rol', $rol);
        if ($statement->execute()) {
            $message = 'El usuario se ha registrado correctamente';
        } else {
            $message = 'Ha ocurrido un error a la hora de registra el usuario. Reintentar';
        }
    } else {
        $message = 'La contraseña de confirmacion es incorrecta, reeintentar.';
    }
}elseif(!empty($_POST['email']) || !empty($_POST['password']) || !empty($_POST['confirm_password'])) {
    echo "<div class='alert alert-danger'>Existen campos vacios</div>";
} 

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>
        Registrate gratuitamente.
    </title>
    <link href="https://fonts.googleapis.com" rel="preconnect">
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
    <div class="container-lg border border-primary-dark text-center bg-primary-subtle" style="width:50%" >  
        <?php if (!empty($message)): ?>
            <p>
                <?= $message ?>
            </p>
        <?php endif; ?>


        <h1 class="mt-4"><b>Registrarme</b></h1>
        <span> o <a href="login.php">Inicia Sesión</a></span>
        
        <hr class="container mt-4 mb-4 d-flex justify-content-center" style="width:70%" >
        </hr>

        <form action="signup.php" method="post">
            <input type="text" name="email" placeholder="Ingresa tu email">
            <input type="password" name="password" placeholder="Ingresa tu contraseña">
            <input type="password" name="confirm_password" placeholder="Confirmar contraseña">
            <input type="submit" value="Enviar">
        </form>
    </div>
</body>

</html>