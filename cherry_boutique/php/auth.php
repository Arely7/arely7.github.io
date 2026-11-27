<?php
session_start();
include "conexion.php";
require_once "correo.php";

if(isset($_POST['txtEmail']) && isset($_POST['txtPassword']) && $_POST['txtEmail'] !== '' && $_POST['txtPassword'] !== ''){
    $email = trim($_POST['txtEmail']);
    $hash  = hash('sha256', $_POST['txtPassword']);

    $stmt = $conexion->prepare("SELECT ID_USUARIO, NOMBRE, EMAIL, ROL, ID_CLIENTE FROM usuarios WHERE EMAIL = ? AND PASSWORD = ?");
    $stmt->bind_param("ss", $email, $hash);
    $stmt->execute();
    $res = $stmt->get_result();

    if($row = $res->fetch_assoc()){
        session_regenerate_id(true);
        $_SESSION['userData'] = [
            "id"         => $row['ID_USUARIO'],
            "name"       => $row['NOMBRE'],
            "email"      => $row['EMAIL'],
            "rol"        => $row['ROL'],
            "id_cliente" => $row['ID_CLIENTE']
        ];
        /* Correo de aviso de inicio de sesión (si falla, el login continúa igual)*/
        $_SESSION['correoEnviado'] = enviarCorreoLogin($row['EMAIL'], $row['NOMBRE']);
        header("Location: ../dash.php");
    }else{
        $_SESSION['errorLogin'] = "Credenciales incorrectas";
        header("Location: ../login.php");
    }
}else{
    $_SESSION['errorLogin'] = "Favor de llenar los campos";
    header("Location: ../login.php");
}
exit;