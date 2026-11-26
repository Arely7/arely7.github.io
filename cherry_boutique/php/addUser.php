<?php
session_start();
include "conexion.php";

$campos = ['txtName', 'txtPaterno', 'txtEmail', 'txtPhone', 'txtPassword', 'txtConfirm'];
foreach ($campos as $c) {
    if (!isset($_POST[$c]) || trim($_POST[$c]) === '') {
        $_SESSION['errorLogin'] = "Favor de llenar los campos";
        header("Location: ../registro.php");
        exit;
    }
}
$nombre = trim($_POST['txtName']);
$paterno = trim($_POST['txtPaterno']);
$materno = trim($_POST['txtMaterno'] ?? '');
$email = trim($_POST['txtEmail']);
$tel = trim($_POST['txtPhone']);
$dir = trim($_POST['txtAddress'] ?? '');

if ($_POST['txtPassword'] !== $_POST['txtConfirm']) {
    $_SESSION['errorLogin'] = "Las contraseñas no coinciden";
    header("Location: ../registro.php");
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['errorLogin'] = "Correo no válido";
    header("Location: ../registro.php");
    exit;
}

// ¿ya existe el correo?
$s = $conexion->prepare("SELECT 1 FROM USUARIOS WHERE EMAIL = ?");
$s->bind_param("s", $email);
$s->execute();
if ($s->get_result()->num_rows > 0) {
    $_SESSION['errorLogin'] = "Ese correo ya está registrado";
    header("Location: ../registro.php");
    exit;
}

$hash = hash('sha256', $_POST['txtPassword']);
$conexion->begin_transaction();
try {
    // INSERT en CLIENTES (el disparador trg_cliente_nuevo escribe en la bitácora)
    $c = $conexion->prepare("INSERT INTO CLIENTES (NOMBRE,AP_PATERNO,AP_MATERNO,EMAIL,TELEFONO,DIRECCION,FECHA_REGISTRO) VALUES (?,?,?,?,?,?,CURDATE())");
    $c->bind_param("ssssss", $nombre, $paterno, $materno, $email, $tel, $dir);
    $c->execute();
    $idCliente = $conexion->insert_id;

    // INSERT en USUARIOS
    $nombreCompleto = $nombre . ' ' . $paterno;
    $u = $conexion->prepare("INSERT INTO USUARIOS (NOMBRE,EMAIL,PASSWORD,ROL,ID_CLIENTE) VALUES (?,?,?,'cliente',?)");
    $u->bind_param("sssi", $nombreCompleto, $email, $hash, $idCliente);
    $u->execute();
    $conexion->commit();
} catch (mysqli_sql_exception $ex) {
    $conexion->rollback();
    $_SESSION['errorLogin'] = "No se pudo registrar: revisa los datos (correo máx. 30 caracteres, teléfono 10)";
    header("Location: ../registro.php");
    exit;
}
$_SESSION['okLogin'] = "¡Registro exitoso! Ya puedes iniciar sesión";
header("Location: ../login.php");
exit;