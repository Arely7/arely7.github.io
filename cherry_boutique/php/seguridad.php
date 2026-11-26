<?php
if(session_status() === PHP_SESSION_NONE){ session_start(); }
require_once __DIR__ . "/funciones.php";

function estaLogueado(){ return isset($_SESSION['userData']); }
function esAdmin(){ return estaLogueado() && $_SESSION['userData']['rol'] === 'admin'; }

function raizSitio(){
    $dir = dirname($_SERVER['SCRIPT_NAME']);
    if(basename($dir) === 'admin'){ $dir = dirname($dir); }
    return rtrim($dir, '/');
}

function requerirLogin(){
    if(!estaLogueado()){
        $_SESSION['errorLogin'] = "Inicia sesión para entrar a esta pantalla";
        header("Location: " . raizSitio() . "/login.php");
        exit;
    }
}
function requerirAdmin(){
    requerirLogin();
    if(!esAdmin()){
        header("Location: " . raizSitio() . "/dash.php");
        exit;
    }
}