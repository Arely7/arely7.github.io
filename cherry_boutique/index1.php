<?php session_start();
include "./php/conexion.php";
include "./php/seguridad.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Cherry Boutique: moda y accesorios para mujeres auténticas. Tu estilo, nuestra inspiración.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css"
        integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/media_querys.css">
    /* */
    <title>Inicio</title>
</head>

<body>
    <header>
        <div class="titulo">
            <h2 class="cherry rosa">Cherry</h2>
            <h4 class="boutique">BOUTIQUE</h4>
        </div>
        <div class="menu">
            <nav>
                <ul class="menu2">
                    <li class="rosa m1"><a href="index1.php">Inicio</a></li>
                    <li class="m1"><a href="index2.php">Colecciones</a></li>
                    <li class="m1"><a href="index3.php">Comprar</a></li>
                    <li class="m1"><a href="index4.php">Nosotros</a></li>
                </ul>
            </nav>
        </div>
        <div class="iconos">
            <a href="index2.php" aria-label="Buscar productos"><i class="fa fa-magnifying-glass ic negro"></i></a>
            <a href="index2.php" aria-label="Ver favoritos"><i class="fa fa-heart ic negro"></i></a>
            <a href="index3.php" aria-label="Ver carrito"><i class="fa fa-bag-shopping ic negro"></i></a>
            <?php if (estaLogueado()) { ?>
                <a href="dash.php" aria-label="Mi cuenta" title="<?php echo e($_SESSION['userData']['name']); ?>"><i
                        class="fa fa-user ic negro"></i></a>
                <a href="php/logout.php" aria-label="Cerrar sesión" title="Cerrar sesión"><i
                        class="fa fa-right-from-bracket ic negro"></i></a>
            <?php } else { ?>
                <a href="login.php" aria-label="Iniciar sesión" title="Iniciar sesión"><i
                        class="fa fa-user ic negro"></i></a>
            <?php } ?>
        </div>
    </header>
    <section id="inicio">
        <main class="ini">
            <div class="inicont">
                <span class="sub">BIENVENIDA A CHERRY BOUTIQUE</span>
                <h1 class="tit">
                    Tu estilo,<br>
                    tu esencia,<br>
                    tu <span class="resaltar">Cherry.</span>
                </h1>

                <p class="descrip">
                    Ropa, accesorios y más para mujeres auténticas, <br>como tú.
                </p>
                <a href="index2.html" class="btncomprar">Comprar ahora<i class="fa fa-arrow-right"></i></a>
            </div>
            <div class="imgini">
                <img src="./img/Goodvibesonly.png" alt="Good vibes only" class="gvo">
            </div>
        </main>
    </section>
    <section id="categorias2">
        <h2 class="titsec">Categorías</h2>
        <p class="subsec">Encuentra lo que va contigo</p>

        <div class="gcat">
            <?php
            $imgCat = [1 => 'blu1.png', 2 => 'te1.png', 3 => 'bol1.png', 4 => 'acc1.png'];
            $rc = $conexion->query("SELECT ID_CATEGORIA, NOMBRE_CATEGORIA FROM CATEGORIAS WHERE ID_CATEGORIA <= 4 ORDER BY ID_CATEGORIA");
            while ($c = $rc->fetch_assoc()) { ?>
                <a href="index2.php?cat[]=<?php echo $c['ID_CATEGORIA']; ?>" class="catcarta">
                    <div class="circat">
                        <img src="./img/ <?php echo $imgCat[$c['ID_CATEGORIA']] ?? 'blu1.png'; ?>"
                            alt="<?php echo e($c['NOMBRE_CATEGORIA']); ?> ">
                    </div>
                    <p><?php echo e($c['NOMBRE_CATEGORIA']); ?> <span class="flecha">→</span></p>
                </a>
            <?php } ?>
        </div>
    </section>

    <section id="destacados">
        <div class="encdes">
            <div>
                <h2 class="titsec">Productos destacados</h2>
                <p class="subsecc">Los favoritos de nuestra comunidad</p>
            </div>
            <a href="index2.php" class="vertodo">Ver todo</a>
        </div>

        <div class="gdest">
            <?php
            $rd = $conexion->query("SELECT * FROM PRODUCTOS WHERE DESTACADO = 1 ORDER BY ID_PRODUCTO LIMIT 4");
            while ($p = $rd->fetch_assoc()) { ?>
                <div class="prodcard">
                    <i class="fa-regular fa-heart cz"></i>
                    <img src="./img/ <?php echo e($p['IMAGEN']); ?>" alt="<?php echo e($p['NOMBRE']); ?>" class="imgprod">
                    <h3 class="prodnom"><?php echo e($p['NOMBRE']); ?> </h3>
                    <p class="prodpre"><?php echo dinero($p['PRECIO']); ?> </p>
                    <div class="colores">
                        <span class="<?php echo claseColorInicio($p['COLOR']); ?>"></span>
                    </div>
                    <button class="btncarrito" type="button" onclick="location='index3.php'">Agregar al carrito</button>
                </div>
            <?php } ?>
        </div>
    </section>

    <section id="club">
        <div class="ut">
            <h2 class="cherryrosa">Cherry Club</h2>
            <p class="texp">Más que una tienda, una comunidad.</p>
            <p class="texs">Únete a nuestro club y disfruta de beneficios <br>
                exclusivos, descuentos especiales y muchas<br>
                sorpresas más.
            </p>
            <a href="index4.php" class="btnunir">Únete ahora<i class="fa fa-arrow-right"></i></a>
        </div>
        </div>
    </section>

    <section id="benef">
        <h2 class="titsec">¿Por qué elegir Cherry Boutique?</h2>

        <div class="gbenef">
            <div class="benefit">
                <i class="fa fa-truck"></i>
                <p>Envíos seguros <br>y rápidos</p>
            </div>
            <div class="benefit">
                <i class="fa fa-shield-halved"></i>
                <p>Pagos 100% <br>seguros</p>
            </div>
            <div class="benefit">
                <i class="fa fa-headset"></i>
                <p>Atención <br> personalizada</p>
            </div>
            <div class="benefit">
                <i class="fa fa-gem"></i>
                <p>Productos <br> de calidad</p>
            </div>
        </div>
    </section>
    <section id="insta">
        <div class="titinsta">
            <div>
                <h2 class="titsecins">Síguenos en Instagram</h2>
                <p class="subsecins">Forma parte de nuestra comunidad</p>
            </div>
            <a href="https://instagram.com/cherryboutique" target="_blank" rel="noopener" class="btninsta">
                <i class="fa-brands fa-instagram"></i> @cherryboutique
            </a>
        </div>
        <div class="ginsta">
            <div class="instacard"><img src="./img/rack1.png" alt="Rack de la tienda" class="imginsta"></div>
            <div class="instacard"><img src="./img/bolsa1.png" alt="Bolsa de la tienda" class="imginsta"></div>
            <div class="instacard"><img src="./img/ft1.png" alt="Foto de la colección" class="imginsta"></div>
            <div class="instacard"><img src="./img/tela1.png" alt="Tela de una prenda" class="imginsta"></div>
            <div class="instacard"><img src="./img/espejo1.png" alt="Espejo de la boutique" class="imginsta"></div>
            <div class="instacard"><img src="./img/collar1.png" alt="Collar de la colección" class="imginsta"></div>
        </div>
    </section>
    <footer>
        <div class="logo">
            <h2 class="rosa">Cherry</h2>
            <p><strong>BOUTIQUE</strong></p>
            <p>Tu estilo, nuestra inspiración</p>
        </div>
        <div class="enlaces">
            <h3>Enlaces</h3>
            <a href="index1.php">Inicio</a>
            <a href="index2.php">Colecciones</a>
            <a href="index3.php">Comprar</a>
            <a href="index4.php">Nosotros</a>
        </div>
        <div>
            <h3>Contacto</h3>
            <p><i class="fa-solid fa-phone"></i>636 XXX XX XX</p>
            <p><i class="fa-regular fa-envelope"></i>hola@cherryboutique.com</p>
            <p><i class="fa-solid fa-location-dot"></i>Nuevo Casas Grandes, Chih.</p>
        </div>
        <div>
            <h3>Síguenos</h3>
            <p class="redes">
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-tiktok"></i>
                <i class="fa-brands fa-facebook"></i>
            </p>
        </div>
    </footer>
</body>

</html>