<?php session_start();
include "./php/conexion.php";
include "./php/seguridad.php";
?>
<?php
$orden = $_GET['orden'] ?? 'nuevo';
$orderSql = ['nuevo' => 'p.ID_PRODUCTO DESC', 'asc' => 'p.PRECIO ASC', 'desc' => 'p.PRECIO DESC'][$orden] ?? 'p.ID_PRODUCTO DESC';
$catSel = isset($_GET['cat']) && is_array($_GET['cat']) ? array_map('intval', $_GET['cat']) : [];
$sql = "SELECT p.*, c.NOMBRE_CATEGORIA FROM productos p JOIN categorias c ON c.ID_CATEGORIA = p.ID_CATEGORIA";
if (count($catSel) > 0) {
    $sql .= " WHERE p.ID_CATEGORIA IN (" . implode(',', $catSel) . ")";
}
$sql .= " ORDER BY " . $orderSql;
$res = $conexion->query($sql);
$total = $res->num_rows;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Colecciones de Cherry Boutique: ropa, zapatos, bolsas y accesorios para mujeres auténticas">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css"
        integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/media_querys.css">
    <link rel="stylesheet" href="./css/extra.css">
    <title>Colecciones</title>
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
                    <li class="m1"><a href="index.php">Inicio</a></li>
                    <li class="rosa m1"><a href="index2.php">Colecciones</a></li>
                    <li class="m1"><a href="index3.php">Comprar</a></li>
                    <li class="m1"><a href="index4.php">Nosotros</a></li>
                </ul>
            </nav>
        </div>
        <div class="iconos">
            <a href="index2.php" aria-label="Buscar productos"><i class="fa fa-magnifying-glass ic"></i></a>
            <a href="index2.php" aria-label="Ver favoritos"><i class="fa fa-heart ic"></i></a>
            <a href="index3.php" aria-label="Ver carrito"><i class="fa fa-bag-shopping ic"></i></a>
            <?php if (estaLogueado()) { ?>
                <a href="dash.php" aria-label="Mi cuenta" title="<?php echo e($_SESSION['userData']['name']); ?>"><i
                        class="fa fa-user ic"></i></a>
                <a href="php/logout.php" aria-label="Cerrar sesión" title="Cerrar sesión"><i
                        class="fa fa-right-from-bracket ic"></i></a>
            <?php } else { ?>
                <a href="login.php" aria-label="Iniciar sesión" title="Iniciar sesión"><i class="fa fa-user ic"></i></a>
            <?php } ?>
        </div>
    </header>
    <section id="colecciones">
        <div class="centrar">
            <div>
                <h1 class="cl">Colecciones</h1>
                <p class="encuentra">Encuentra todo lo que amas, en un solo lugar.</p>
                <button class="buscador">
                    <span>Buscar productos...</span>
                    <i class="fa fa-magnifying-glass"></i>
                </button>
            </div>
        </div>
    </section>
    <section id="categorias">
        <div class="circulo cu"><i class="fa fa-shopping-bag rosa"></i></div>
        <div class="circulo cu"><i class="fa fa-star rosa"></i></div>
        <div class="circulo cu"><i class="fa fa-briefcase rosa"></i></div>
        <div class="circulo cu"><i class="fa fa-diamond rosa"></i></div>
    </section>
    <section id="catalogo">
        <div class="lado">
            <div class="filtros">
                <h2>Filtros</h2>
                <h3>Categoría</h3>
                <form method="GET" id="formcat">
                    <?php
                    $catSel = isset($_GET['cat']) && is_array($_GET['cat']) ? array_map('intval', $_GET['cat']) : [];
                    $rc = $conexion->query("SELECT ID_CATEGORIA, NOMBRE_CATEGORIA FROM categorias WHERE ID_CATEGORIA <= 4 ORDER BY ID_CATEGORIA");
                    while ($c = $rc->fetch_assoc()) { ?>
                        <label><input type="checkbox" name="cat[]" value="<?php echo $c['ID_CATEGORIA']; ?>" <?php echo in_array((int) $c['ID_CATEGORIA'], $catSel) ? 'checked' : ";" ?>
                                onchange="document.getElementById('formcat').submit()"><?php echo e($c['NOMBRE_CATEGORIA']); ?></label>
                    <?php } ?>
                </form>
                <h3>Subcategoría</h3>
                <label><input type="checkbox">Tops</label>
                <label><input type="checkbox">Vestidos</label>
                <label><input type="checkbox">Pantalones</label>
                <label><input type="checkbox">Faldas</label>
                <label><input type="checkbox">Suéteres</label>
                <label><input type="checkbox">Chamarras</label>
                <label><input type="checkbox">Conjuntos</label>
                <h3>Talla</h3>
                <label><input type="checkbox">XS</label>
                <label><input type="checkbox">S</label>
                <label><input type="checkbox">M</label>
                <label><input type="checkbox">L</label>
                <label><input type="checkbox">XL</label>
                <h3>Color</h3>
                <div class="colores">
                    <span class="col rosa-bg"></span>
                    <span class="col rosa2-bg"></span>
                    <span class="col linea-bg"></span>
                    <span class="col blanco-bg"></span>
                </div>
                <div class="colores">
                    <span class="col negro"></span>
                    <span class="col guinda-bg"></span>
                    <span class="col cafe-bg"></span>
                    <span class="col rosa3-bg"></span>
                    <span class="mas">Más...</span>
                </div>
                <h3>Precio</h3>
                <label><input type="checkbox">$0.00-$499.00</label>
                <label><input type="checkbox">$500.00-$999.00</label>
                <label><input type="checkbox">$1,000.00-$1,499.00</label>
                <label><input type="checkbox">$1,500.00+</label>
            </div>
            <img class="globos" src="img/Rectangle24.png" alt="Bolsa de regalo con globos rosa">
        </div>
        <div class="productos">
            <div class="barra">
                <div>
                    <label for="orden">Ordenar por:</label>
                    <select id="orden"
                        onchange="var u=new URL(location);u.searchParams.set('orden',this.value);location=u;">
                        <option value="nuevo" <?php echo $orden === 'nuevo' ? 'selected' : ''; ?>>Más nuevos</option>
                        <option value="asc" <?php echo $orden === 'asc' ? 'selected' : ''; ?>>Precio: menor a mayor</option>
                        <option value="desc" <?php echo $orden === 'desc' ? 'selected' : ''; ?>>Precio: mayor a menor</option>
                    </select>
                </div>
                <p class="rosa">Mostrando <?php echo $total; ?> productos</p>
            </div>
            <div class="lista">
                <?php while ($p = $res->fetch_assoc()) { ?>
                    <div class="prod">
                        <div class="foto">
                            <i class="fa-regular fa-heart cz-col"></i>
                            <img src="img/<?php echo e($p['IMAGEN']); ?>" alt="<?php echo e($p['NOMBRE']); ?>">
                        </div>
                        <p class="nom"><?php echo e($p['NOMBRE']); ?></p>
                        <p class="nom"><?php echo dinero($p['PRECIO']); ?></p>
                        <div class="colores">
                            <span class="<?php echo claseColor($p['COLOR']); ?>"></span>
                        </div>
                        <p class="tallas">
                            <?php echo (strtolower((string)$p['TALLA']) === 'unica' || empty($p['TALLA'])) ? 'Talla: Única' : 'Talla: ' . e($p['TALLA']); ?>
                        </p>
                        <button class="btn" type="button" onclick="location='index3.php'">Agregar al carrito</button>
                    </div>
                <?php } ?>
                <?php if ($total === 0) {
                    echo '<p>No encontramos productos en esta categoría.</p>';
                } ?>
            </div>
        </div>
    </section>
    <section id="looks">
        <h2>Looks que inspiran</h2>
        <p>Ideas para combinar y hacer tuyo cada estilo</p>
        <div class="lista-looks">
            <div class="look">
                <img src="img/romantico.png" alt="Romántico">
                <div class="pie">
                    <p>Romántico</p>
                </div>
            </div>
            <div class="look">
                <img src="img/casual.png" alt="Casual Chic">
                <div class="pie">
                    <p>Casual Chic</p>
                </div>
            </div>
            <div class="look">
                <img src="img/night.png" alt="Night Out">
                <div class="pie">
                    <p>Night Out</p>
                </div>
            </div>
            <div class="look">
                <img src="img/coquette.png" alt="Coquette">
                <div class="pie">
                    <p>Coquette</p>
                </div>
            </div>
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
            <a href="index.php">Inicio</a>
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