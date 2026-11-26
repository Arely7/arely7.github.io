<?php
require __DIR__ . "/php/seguridad.php";
requerirLogin();                       
include "./php/conexion.php";

$u   = $_SESSION['userData'];
$ini = "dash";
$tituloPagina = "Mi cuenta";

/* Datos completos de la clienta (si el usuario ya esta a nombre de un cliente)*/
$cliente = null;
if($u['id_cliente']){
    $st = $conexion->prepare("SELECT * FROM CLIENTES WHERE ID_CLIENTE = ?");
    $st->bind_param("i", $u['id_cliente']); $st->execute();
    $cliente = $st->get_result()->fetch_assoc();
}
include "./php/admin_header.php";
?>
<h1 class="adm-tit">Hola, <?php echo e($u['name']); ?> <i class="fa fa-heart rosa"></i></h1>
<?php mostrarFlash(); ?>
<?php if(isset($_SESSION['correoEnviado'])){ ?>
  <div class="alerta <?php echo $_SESSION['correoEnviado'] ? 'ok' : 'aviso'; ?>">
    <?php echo $_SESSION['correoEnviado'] ? 'Te enviamos un correo avisando de tu inicio de sesión.' : 'No se pudo enviar el correo de inicio de sesión (revisa php/config_correo.php).'; ?>
  </div>
<?php unset($_SESSION['correoEnviado']); } ?>

<div class="adm-grid">
  <section class="adm-card">
    <h2>Mis datos</h2>
    <p><b>Nombre:</b> <?php echo e($u['name']); ?></p>
    <p><b>Correo:</b> <?php echo e($u['email']); ?></p>
    <p><b>Tipo de cuenta:</b> <?php echo $u['rol'] === 'admin' ? 'Administradora' : 'Clienta Cherry Club'; ?></p>
    <?php if($cliente){ ?>
      <p><b>Teléfono:</b> <?php echo e($cliente['TELEFONO']); ?></p>
      <p><b>Dirección:</b> <?php echo e($cliente['DIRECCION']); ?></p>
      <p><b>Miembro desde:</b> <?php echo e($cliente['FECHA_REGISTRO']); ?></p>
    <?php } ?>
  </section>

<?php if(esAdmin()){
    $tot = [];
    foreach(['PRODUCTOS','CATEGORIAS','CLIENTES','PEDIDOS'] as $t){
        $tot[$t] = $conexion->query("SELECT COUNT(*) FROM $t")->fetch_row()[0];
    }
    $ventas = $conexion->query("SELECT IFNULL(SUM(TOTAL),0) FROM PEDIDOS")->fetch_row()[0];
?>
  <section class="adm-card">
    <h2>Resumen de la tienda</h2>
    <div class="stats">
      <div><b><?php echo $tot['PRODUCTOS']; ?></b><span>Productos</span></div>
      <div><b><?php echo $tot['CATEGORIAS']; ?></b><span>Categorías</span></div>
      <div><b><?php echo $tot['CLIENTES']; ?></b><span>Clientas</span></div>
      <div><b><?php echo $tot['PEDIDOS']; ?></b><span>Pedidos</span></div>
    </div>
    <p><b>Ventas registradas:</b> <?php echo dinero($ventas); ?></p>
  </section>
</div>

<section class="adm-card">
  <h2>Últimos movimientos (bitácora de disparadores)</h2>
  <div class="tabla-wrap"><table class="tabla">
    <tr><th>Fecha</th><th>Tabla</th><th>Acción</th><th>Detalle</th></tr>
    <?php $r = $conexion->query("SELECT * FROM BITACORA ORDER BY ID_BITACORA DESC LIMIT 8");
    if($r->num_rows === 0){ echo '<tr><td colspan="4">Aún no hay movimientos</td></tr>'; }
    while($b = $r->fetch_assoc()){ ?>
    <tr><td><?php echo e($b['FECHA']); ?></td><td><?php echo e($b['TABLA']); ?></td><td><?php echo e($b['ACCION']); ?></td><td><?php echo e($b['DETALLE']); ?></td></tr>
    <?php } ?>
  </table></div>
</section>

<?php } else { ?>
</div>
<section class="adm-card">
  <h2>Mis pedidos</h2>
  <div class="tabla-wrap"><table class="tabla">
    <tr><th>Pedido</th><th>Fecha</th><th>Producto</th><th>Cant.</th><th>Método de pago</th><th>Estado</th><th>Total</th></tr>
    <?php
    // JOIN de 4 tablas
    $sql = "SELECT p.ID_PEDIDO, p.FECHA_PEDIDO, p.ESTADO, p.TOTAL, m.NOMBRE_METODO, pr.NOMBRE AS PRODUCTO, d.CANTIDAD
            FROM PEDIDOS p
            JOIN METODO_PAGO m     ON m.ID_METODO = p.ID_METODO
            JOIN DETALLES_PEDIDO d ON d.ID_PEDIDO = p.ID_PEDIDO
            JOIN PRODUCTOS pr      ON pr.ID_PRODUCTO = d.ID_PRODUCTO
            WHERE p.ID_CLIENTE = ? ORDER BY p.FECHA_PEDIDO DESC";
    $st = $conexion->prepare($sql);
    $idc = (int)$u['id_cliente'];
    $st->bind_param("i", $idc); $st->execute();
    $r = $st->get_result();
    if($r->num_rows === 0){ echo '<tr><td colspan="7">Todavía no tienes pedidos. <a href="index2.php" class="rosa">Ver colecciones</a></td></tr>'; }
    while($p = $r->fetch_assoc()){ ?>
    <tr><td>#<?php echo $p['ID_PEDIDO']; ?></td><td><?php echo e($p['FECHA_PEDIDO']); ?></td><td><?php echo e($p['PRODUCTO']); ?></td><td><?php echo $p['CANTIDAD']; ?></td><td><?php echo e($p['NOMBRE_METODO']); ?></td><td><?php echo e($p['ESTADO']); ?></td><td><?php echo dinero($p['TOTAL']); ?></td></tr>
    <?php } ?>
  </table></div>
</section>
<?php } ?>
<?php include "./php/admin_footer.php"; ?>
