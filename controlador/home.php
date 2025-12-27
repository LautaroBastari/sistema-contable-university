<?php
session_start(); // Inicia la sesión (si aún no está iniciada)

if (isset($_POST['button'])) {
    $message = urlencode("After clicking the button, the form will submit to home.php. When the page home.php loads, the previous page index.php is redirected.");

    // Almacena la URL de la página actual en una variable de sesión
    $_SESSION['previous_page'] = $_SERVER['PHP_SELF'];

    // Redirige al usuario a la página de inicio o cualquier otra página deseada
    header("Location: index.php?message=" . $message);
    exit; // Asegura que el script se detenga después de la redirección
}

?>