<?php
require __DIR__ . "/../php/seguridad.php";
requerirAdmin();
include "../php/conexion.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $accion = $_POST['accion'] ?? '';
    try{
        if($accion === 'insertar'){
            // Procedimiento almacenado: crea pedido + detalle y el disparador descuenta el stock
            $st=$conexion->prepare("CALL sp_registrar_pedido(?,?,?,?)");
            $cl=(int)$_POST['id_cliente']; $me=(int)$_POST['id_metodo']; $pr=(int)$_POST['id_producto']; $ca=(int)$_POST['cantidad'];
            $st->bind_param("iiii",$cl,$me,$pr,$ca); $st->execute();
            flash('ok',"Pedido registrado (la existencia se descontó automáticamente)");
        }elseif($accion === 'editar'){
            $st=$conexion->prepare("UPDATE PEDIDOS SET ESTADO=?, ID_METODO=? WHERE ID_PEDIDO=?");
            $id=(int)$_POST['id']; $me=(int)$_POST['id_metodo'];
            $st->bind_param("sii",$_POST['estado'],$me,$id); $st->execute();
            flash('ok',"Pedido actualizado");
        }elseif($accion === 'eliminar'){
            // Procedimiento almacenado: borra detalles y pedido (el disparador devuelve el stock)
            $st=$conexion->prepare("CALL sp_eliminar_pedido(?)");
            $id=(int)$_POST['id']; $st->bind_param("i",$id); $st->execute();
            flash('ok',"Pedido eliminado");
        }
    }catch(mysqli_sql_exception $ex){
        // Los SIGNAL del procedimiento llegan aquí con su mensaje (código 1644)
        flash('error', $ex->getCode() == 1644 ? $ex->getMessage() : "Error en la base de datos");
    }
    header("Location: pedidos.php"); exit;
}

$estados = ['En proceso','En camino','Entregado','Cancelado'];
$clientes = $conexion->query("SELECT ID_CLIENTE, CONCAT(NOMBRE,' ',AP_PATERNO) AS N FROM CLIENTES ORDER BY NOMBRE")->fetch_all(MYSQLI_ASSOC);
$metodos  = $conexion->query("SELECT * FROM METODO_PAGO")->fetch_all(MYSQLI_ASSOC);
$prods    = $conexion->query("SELECT ID_PRODUCTO, NOMBRE, EXISTENCIA FROM PRODUCTOS ORDER BY NOMBRE")->fetch_all(MYSQLI_ASSOC);

$edit = null;
if(isset($_GET['editar'])){
    $st=$conexion->prepare("SELECT * FROM PEDIDOS WHERE ID_PEDIDO=?");
    $id=(int)$_GET['editar']; $st->bind_param("i",$id); $st->execute();
    $edit=$st->get_result()->fetch_assoc();
}

// JOIN de pedidos + clientes + método de pago + detalle + productos
$lista = $conexion->query("SELECT p.ID_PEDIDO, p.FECHA_PEDIDO, p.HORA_PEDIDO, p.ESTADO, p.TOTAL,
        CONCAT(c.NOMBRE,' ',c.AP_PATERNO) AS CLIENTA, m.NOMBRE_METODO,
        GROUP_CONCAT(CONCAT(d.CANTIDAD,' x ',pr.NOMBRE) SEPARATOR ', ') AS PRODUCTOS
    FROM PEDIDOS p
    JOIN CLIENTES c          ON c.ID_CLIENTE   = p.ID_CLIENTE
    JOIN METODO_PAGO m       ON m.ID_METODO    = p.ID_METODO
    LEFT JOIN DETALLES_PEDIDO d ON d.ID_PEDIDO = p.ID_PEDIDO
    LEFT JOIN PRODUCTOS pr   ON pr.ID_PRODUCTO = d.ID_PRODUCTO
    GROUP BY p.ID_PEDIDO ORDER BY p.ID_PEDIDO DESC");

$ini="pedidos"; $tituloPagina="Pedidos";
include "../php/admin_header.php";
?>
<h1 class="adm-tit">Pedidos</h1>
<?php mostrarFlash(); ?>

<?php if($edit){ ?>
<section class="adm-card">
  <h2>Editar pedido #<?php echo $edit['ID_PEDIDO']; ?></h2>
  <form method="POST" class="form-adm">
    <input type="hidden" name="accion" value="editar"><input type="hidden" name="id" value="<?php echo $edit['ID_PEDIDO']; ?>">
    <div><label>Estado</label><select name="estado">
      <?php foreach($estados as $s){ ?><option <?php echo trim($edit['ESTADO']) === $s ? 'selected' : ''; ?>><?php echo $s; ?></option><?php } ?>
    </select></div>
    <div><label>Método de pago</label><select name="id_metodo">
      <?php foreach($metodos as $m){ ?><option value="<?php echo $m['ID_METODO']; ?>" <?php echo $edit['ID_METODO'] == $m['ID_METODO'] ? 'selected' : ''; ?>><?php echo e($m['NOMBRE_METODO']); ?></option><?php } ?>
    </select></div>
    <div class="acciones-form"><button class="btn-adm" type="submit">Guardar cambios</button><a class="btn-adm sec" href="pedidos.php">Cancelar</a></div>
  </form>
</section>
<?php } else { ?>
<section class="adm-card">
  <h2>Registrar pedido</h2>
  <form method="POST" class="form-adm">
    <input type="hidden" name="accion" value="insertar">
    <div><label>Clienta</label><select name="id_cliente" required>
      <?php foreach($clientes as $c){ ?><option value="<?php echo $c['ID_CLIENTE']; ?>"><?php echo e($c['N']); ?></option><?php } ?></select></div>
    <div><label>Producto</label><select name="id_producto" required>
      <?php foreach($prods as $p){ ?><option value="<?php echo $p['ID_PRODUCTO']; ?>"><?php echo e($p['NOMBRE']); ?> (existencia: <?php echo $p['EXISTENCIA']; ?>)</option><?php } ?></select></div>
    <div><label>Cantidad</label><input type="number" name="cantidad" min="1" value="1" required></div>
    <div><label>Método de pago</label><select name="id_metodo" required>
      <?php foreach($metodos as $m){ ?><option value="<?php echo $m['ID_METODO']; ?>"><?php echo e($m['NOMBRE_METODO']); ?></option><?php } ?></select></div>
    <div class="acciones-form"><button class="btn-adm" type="submit">Registrar pedido</button></div>
  </form>
</section>
<?php } ?>

<section class="adm-card">
  <h2>Listado</h2>
  <div class="tabla-wrap"><table class="tabla">
    <tr><th>Pedido</th><th>Fecha</th><th>Clienta</th><th>Productos</th><th>Pago</th><th>Estado</th><th>Total</th><th>Acciones</th></tr>
    <?php while($p = $lista->fetch_assoc()){ ?>
    <tr>
      <td>#<?php echo $p['ID_PEDIDO']; ?></td>
      <td><?php echo e($p['FECHA_PEDIDO'].' '.substr($p['HORA_PEDIDO'],0,5)); ?></td>
      <td><?php echo e($p['CLIENTA']); ?></td><td><?php echo e($p['PRODUCTOS']); ?></td>
      <td><?php echo e($p['NOMBRE_METODO']); ?></td><td><?php echo e(trim($p['ESTADO'])); ?></td>
      <td><?php echo dinero($p['TOTAL']); ?></td>
      <td class="acc">
        <a class="btn-adm peq" href="pedidos.php?editar=<?php echo $p['ID_PEDIDO']; ?>">Editar</a>
        <form method="POST" onsubmit="return confirm('¿Eliminar este pedido?');">
          <input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?php echo $p['ID_PEDIDO']; ?>">
          <button class="btn-adm peq del" type="submit">Eliminar</button>
        </form>
      </td>
    </tr>
    <?php } ?>
  </table></div>
</section>
<?php include "../php/admin_footer.php"; ?>
