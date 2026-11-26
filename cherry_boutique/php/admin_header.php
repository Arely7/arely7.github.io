<?php
// Cabecera común del panel (dash.php y /admin/*). Antes de incluirla define $ini y $tituloPagina.
$base = raizSitio();
function activo($n){ global $ini; return $ini === $n ? 'activo' : ''; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($tituloPagina); ?> – Cherry Boutique</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="<?php echo $base; ?>/css/styles.css">
<link rel="stylesheet" href="<?php echo $base; ?>/css/extra.css">
</head>
<body class="adm">
<header class="adm-head">
  <a href="<?php echo $base; ?>/index1.php" class="titulo">
    <h2 class="cherry rosa">Cherry</h2><h4 class="boutique">BOUTIQUE</h4>
  </a>
  <nav class="adm-nav">
    <a class="<?php echo activo('dash'); ?>" href="<?php echo $base; ?>/dash.php">Mi cuenta</a>
    <?php if(esAdmin()){ ?>
    <a class="<?php echo activo('productos'); ?>"  href="<?php echo $base; ?>/admin/productos.php">Productos</a>
    <a class="<?php echo activo('categorias'); ?>" href="<?php echo $base; ?>/admin/categorias.php">Categorías</a>
    <a class="<?php echo activo('clientes'); ?>"   href="<?php echo $base; ?>/admin/clientes.php">Clientas</a>
    <a class="<?php echo activo('pedidos'); ?>"    href="<?php echo $base; ?>/admin/pedidos.php">Pedidos</a>
    <?php } ?>
    <a href="<?php echo $base; ?>/index1.php">Ver sitio</a>
    <a href="<?php echo $base; ?>/php/logout.php" class="salir"><i class="fa fa-right-from-bracket"></i> Salir</a>
  </nav>
</header>
<main class="adm-main"></main>