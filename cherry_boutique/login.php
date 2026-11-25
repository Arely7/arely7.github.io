<?php session_start();
include "./php/funciones.php";
if (isset($_SESSION['userData'])) {
    header("Location: dash.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Cherry Boutique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="./css/styles.css">
    /* */
</head>

<body class="auth-page">
    <div class="auth-box">
        <a href="index1.php" class="auth-logo">
            <h2 class="cherry rosa">Cherry</h2>
            <h4 class="boutique">BOUTIQUE</h4>
        </a>
        <h1>Iniciar sesión</h1>
        <p class="auth-sub">Bienvenida de vuelta, tu Cherry te espera.</p>

        <?php if (isset($_SESSION['okLogin'])) { ?>
            <div class="alerta ok"><?php echo e($_SESSION['okLogin']);
            unset($_SESSION['okLogin']); ?></div>
        <?php } ?>
        <?php if (isset($_SESSION['errorLogin'])) { ?>
            <div class="alerta error"><b>Alerta:</b>
                <?php echo e($_SESSION['errorLogin']);
                unset($_SESSION['errorLogin']); ?></div>
        <?php } ?>

        <form method="POST" action="./php/auth.php">
            <label>Correo electrónico</label>
            <input name="txtEmail" type="email" placeholder="ejemplo@correo.com" required>
            <label>Contraseña</label>
            <input name="txtPassword" type="password" placeholder="•••••••••" required>
            <button type="submit" class="btn-auth">Entrar</button>
        </form>
        <p class="auth-pie">¿Aún no eres parte de Cherry Club? <a href="registro.php">Regístrate</a></p>
        <p class="auth-pie"><a href="index1.php"><i class="fa fa-arrow-left"></i>Volver al sitio</a></p>
    </div>
</body>

</html>