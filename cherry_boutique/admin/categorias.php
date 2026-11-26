<?php
require __DIR__ . "/../php/seguridad.php";
requerirAdmin();
include "../php/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    try {
        if ($accion === 'insertar') {
            $st = $conexion->prepare("INSERT INTO CATEGORIAS (NOMBRE_CATEGORIA, DESCRIPCION) VALUES (?,?)");
            $st->bind_param("ss", $_POST['nombre'], $_POST['descripcion']);
            $st->execute();
            flash('ok', 'Categoría agregada');
        } elseif ($accion === 'editar') {
            $st = $conexion->prepare("UPDATE CATEGORIAS SET NOMBRE_CATEGORIA=?, DESCRIPCION=? WHERE ID_CATEGORIA=?");
            $id = (int) $_POST['id'];
            $st->bind_param("ssi", $_POST['nombre'], $_POST['descripcion'], $id);
            $st->execute();
            flash('ok', "Categoría actualizada");
        } elseif ($accion === 'eliminar') {
            $st = $conexion->prepare("DELETE FROM CATEGORIAS WHERE ID_CATEGORIA=?");
            $id = (int) $_POST['id'];
            $st->bind_param("i", $id);
            $st->execute();
            flash('ok', "Categoría eliminada");
        }
    } catch (mysqli_sql_exception $ex) {
        if ($ex->getCode() == 1451) {
            flash('error', "No se puede eliminar: la categoría tiene productos");
        } else {
            flash('error', "Error: revisa la longitud de los datos (nombre máx. 20, descripción máx. 100)");
        }
    }
    header("Location: categorias.php");
    exit;
}

$edit = null;
if (isset($_GET['editar'])) {
    $st = $conexion->prepare("SELECT * FROM CATEGORIAS WHERE ID_CATEGORIA=?");
    $id = (int) $_GET['editar'];
    $st->bind_param("i", $id);
    $st->execute();
    $edit = $st->get_result()->fetch_assoc();
}
//JOIN con conteo de productos
$lista = $conexion->query("SELECT c.*, COUNT(p.ID_PRODUCTO) AS TOTAL_PROD
                            FROM CATEGORIAS c LEFT JOIN PRODUCTOS p ON p.ID_CATEGORIA = c.ID_CATEGORIA
                            GROUP BY c.ID_CATEGORIA ORDER BY c.ID_CATEGORIA");
$ini = "categorias";
$tituloPagina = "Categorías";
include "../php/admin_header.php";
?>
<h1 class="adm-tit">Categorías</h1>
<?php mostrarFlash(); ?>
<section class="adm-card">
    <h2><?php echo $edit ? 'Editar categoría #' . $edit['ID_CATEGORIA'] : 'Agregar categoría'; ?></h2>
    <form method="POST" class="form-adm">
        <input type="hidden" name="accion" value="<?php echo $edit ? 'editar' : 'insertar'; ?>">
        <?php if ($edit) { ?><input type="hidden" name="id" value="<?php echo $edit['ID_CATEGORIA']; ?>"><?php } ?>
        <div><label>Nombre</label><input name="nombre" maxlength="20" required
                value="<?php echo e($edit['NOMBRE_CATEGORIA'] ?? ''); ?>"></div>
        <div><label>Descripción</label><input name="descripcion" maxlength="100"
                value="<?php echo e($edit['DESCRIPCION'] ?? ''); ?>"></div>
        <div class="acciones-form">
            <button class="btn-adm" type="submit"><?php echo $edit ? 'Guardar cambios' : 'Agregar'; ?></button>
            <?php if ($edit) { ?><a class="btn-adm sec" href="categorias.php">Cancelar</a><?php } ?>
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
                <th>Descripción</th>
                <th>Productos</th>
                <th>Acciones</th>
            </tr>
            <?php while ($c = $lista->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $c['ID_CATEGORIA']; ?></td>
                    <td><?php echo e($c['NOMBRE_CATEGORIA']); ?></td>
                    <td><?php echo e($c['DESCRIPCION']); ?></td>
                    <td><?php echo $c['TOTAL_PROD']; ?></td>
                    <td class="acc">
                        <a class="btn-adm peq" href="categorias.php?editar=<?php echo $c['ID_CATEGORIA']; ?>">Editar</a>
                        <form method="POST" onsubmit="return confirm('¿Eliminar esta categoría?');">
                            <input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id"
                                value="<?php echo $c['ID_CATEGORIA']; ?>">
                            <button class="btn-adm peq del" type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</section>
<?php include "../php/admin_footer.php"; ?>