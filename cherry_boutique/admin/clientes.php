<?php
require __DIR__ . "/../php/seguridad.php";
requerirAdmin();
include "../php/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    try {
        if ($accion === 'insertar') {
            $st = $conexion->prepare("INSERT INTO CLIENTES (NOMBRE,AP_PATERNO,AP_MATERNO,EMAIL,TELEFONO,DIRECCION,FECHA_REGISTRO) VALUES (?,?,?,?,?,?,CURDATE())");
            $st->bind_param("ssssss", $_POST['nombre'], $_POST['paterno'], $_POST['materno'], $_POST['email'], $_POST['telefono'], $_POST['direccion']);
            $st->execute();
            flash('ok', "Clienta agregada");
        } elseif ($accion === 'editar') {
            $st = $conexion->prepare("UPDATE CLIENTES SET NOMBRE=?,AP_PATERNO=?,AP_MATERNO=?,EMAIL=?,TELEFONO=?,DIRECCION=? WHERE ID_CLIENTE=?");
            $id = (int) $_POST['id'];
            $st->bind_param("ssssssi", $_POST['nombre'], $_POST['paterno'], $_POST['materno'], $_POST['email'], $_POST['telefono'], $_POST['direccion'], $id);
            $st->execute();
            flash('ok', "Clienta actualizada");
        } elseif ($accion === 'eliminar') {
            $id = (int) $_POST['id'];
            $st = $conexion->prepare("DELETE FROM CLIENTES WHERE ID_CLIENTE=?");
            $st->bind_param("i", $id);
            $st->execute();
            flash('ok', "Clienta eliminada");
        }
    } catch (mysqli_sql_exception $ex) {
        if ($ex->getCode() == 1451) {
            flash('error', "No se puede eliminar: la clienta tiene pedidos");
        } else {
            flash('error', "Error: revisa los datos (correo máx. 30, teléfono máx. 10, dirección máx. 30)");
        }
    }
    header("Location: clientes.php");
    exit;
}

$edit = null;
if (isset($_GET['editar'])) {
    $st = $conexion->prepare("SELECT * FROM CLIENTES WHERE ID_CLIENTE=?");
    $id = (int) $_GET['editar'];
    $st->bind_param("i", $id);
    $st->execute();
    $edit = $st->get_result()->fetch_assoc();
}
// JOIN con pedidos para saber cuántos tiene cada clienta
$lista = $conexion->query("SELECT c.*, COUNT(p.ID_PEDIDO) AS NUM_PEDIDOS
                           FROM CLIENTES c LEFT JOIN PEDIDOS p ON p.ID_CLIENTE = c.ID_CLIENTE
                           GROUP BY c.ID_CLIENTE ORDER BY c.ID_CLIENTE");
$ini = "clientes";
$tituloPagina = "Clientas";
include "../php/admin_header.php";
?>
<h1 class="adm-tit">Clientas</h1>
<?php mostrarFlash(); ?>
<section class="adm-card">
    <h2><?php echo $edit ? 'Editar clienta #' . $edit['ID_CLIENTE'] : 'Agregar clienta'; ?></h2>
    <form method="POST" class="form-adm">
        <input type="hidden" name="accion" value="<?php echo $edit ? 'editar' : 'insertar'; ?>">
            <?php if ($edit) { ?><input type="hidden" name="id" value="<?php echo $edit['ID_CLIENTE']; ?>"><?php } ?>
        <div><label>Nombre</label><input name="nombre" maxlength="30" required
                value="<?php echo e($edit['NOMBRE'] ?? ''); ?>"></div>
        <div><label>Apellido paterno</label><input name="paterno" maxlength="20" required
                value="<?php echo e($edit['AP_PATERNO'] ?? ''); ?>"></div>
        <div><label>Apellido materno</label><input name="materno" maxlength="20"
                value="<?php echo e($edit['AP_MATERNO'] ?? ''); ?>"></div>
        <div><label>Correo</label><input name="email" type="email" maxlength="30" required
                value="<?php echo e($edit['EMAIL'] ?? ''); ?>"></div>
        <div><label>Teléfono</label><input name="telefono" maxlength="10"
                value="<?php echo e($edit['TELEFONO'] ?? ''); ?>"></div>
        <div><label>Dirección</label><input name="direccion" maxlength="30"
                value="<?php echo e($edit['DIRECCION'] ?? ''); ?>"></div>
        <div class="acciones-form">
            <button class="btn-adm" type="submit"><?php echo $edit ? 'Guardar cambios' : 'Agregar'; ?></button>
                  <?php if ($edit) { ?><a class="btn-adm sec" href="clientes.php">Cancelar</a><?php } ?>
        </div>
    </form>
</section>
<section class="adm-card">
    <h2>Listado</h2>
    <div class="tabla-wrap">
        <table class="tabla">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Registro</th>
                <th>Pedidos</th>
                <th>Acciones</th>
            </tr>
                    <?php while ($c = $lista->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $c['ID_CLIENTE']; ?></td>
                    <td><?php echo e($c['NOMBRE'] . ' ' . $c['AP_PATERNO'] . ' ' . $c['AP_MATERNO']); ?></td>
                    <td><?php echo e($c['EMAIL']); ?></td>
                    <td><?php echo e($c['TELEFONO']); ?></td>
                    <td><?php echo e($c['DIRECCION']); ?></td>
                    <td><?php echo e($c['FECHA_REGISTRO']); ?></td>
                    <td><?php echo $c['NUM_PEDIDOS']; ?></td>
                    <td class="acc">
                        <a class="btn-adm peq" href="clientes.php?editar=<?php echo $c['ID_CLIENTE']; ?>">Editar</a>
                        <form method="POST" onsubmit="return confirm('¿Eliminar a esta clienta?');">
                            <input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id"
                                value="<?php echo $c['ID_CLIENTE']; ?>">
                            <button class="btn-adm peq del" type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
                    <?php } ?>
        </table>
    </div>
</section>
<?php include "../php/admin_footer.php"; ?>