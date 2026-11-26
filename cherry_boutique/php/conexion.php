<?php
$servidor = "127.0.0.1";
$nombreBD = "cherry_boutique";
$user     = "root";
$password = "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli($servidor, $user, $password, $nombreBD);
    $conexion->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("No se pudo conectar a la base de datos");
}