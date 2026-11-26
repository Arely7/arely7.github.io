<?php
session_start();
include "./php/funciones.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro – Cherry Boutique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/extra.css">
</head>

<body class="auth-page">
    <div class="auth-box ancho">
        <a href="index.php" class="auth-logo">
            <h2 class="cherry rosa">Cherry</h2>
            <h4 class="boutique">BOUTIQUE</h4>
        </a>
        <h1>Únete a Cherry Club</h1>
        <p class="auth-sub">Crea tu cuenta y empieza a acumular beneficios.</p>

        <?php if (isset($_SESSION['errorLogin'])) { ?>
            <div class="alerta error"><b>Alerta:</b>
                <?php echo e($_SESSION['errorLogin']);
                unset($_SESSION['errorLogin']); ?></div>
        <?php } ?>

        <form method="POST" action="./php/addUser.php">
            <div class="dos-col">
                <div><label>Nombre</label><input name="txtName" type="text" maxlength="30" required></div>
                <div><label>Apellido paterno</label><input name="txtPaterno" type="text" maxlength="20" required></div>
            </div>
            <div class="dos-col">
                <div><label>Apellido materno</label><input name="txtMaterno" type="text" maxlength="20"></div>
                <div><label>Teléfono (10 dígitos)</label><input name="txtPhone" type="tel" maxlength="10"
                        pattern="[0-9]{10}" required></div>
            </div>
            <label>Correo electrónico</label>
            <input name="txtEmail" type="email" maxlength="30" placeholder="ejemplo@correo.com" required>
            <label>Dirección</label>
            <input name="txtAddress" type="text" maxlength="30" placeholder="Calle y número">
            <div class="dos-col">
                <div><label>Contraseña</label><input name="txtPassword" type="password" required></div>
                <div><label>Confirmar contraseña</label><input name="txtConfirm" type="password" required></div>
            </div>
            <button type="submit" class="btn-auth">Registrarme</button>
        </form>
        <p class="auth-pie">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</body>

</html>