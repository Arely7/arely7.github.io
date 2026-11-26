<?php
require __DIR__ . "/../php/seguridad.php";
requerirAdmin();
include "../php/conexion.php";

/* INSERT, UPDATE, DELETE*/
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $accion = $_POST['accion'] ?? '';
    try{
        if($accion === 'insertar' || $accion === 'editar'){
            $nom=trim($_POST['nombre']); $des=trim($_POST['descripcion']); $pre=(float)$_POST['precio'];
            $exi=(int)$_POST['existencia']; $col=trim($_POST['color']); $tal=trim($_POST['talla']);
            $cat=(int)$_POST['id_categoria']; $img=trim($_POST['imagen']); $dest=isset($_POST['destacado']) ? 1 : 0;
            if($nom === ''){ throw new Exception("El nombre es obligatorio"); }
            if($accion === 'insertar'){
                $st=$conexion->prepare("INSERT INTO PRODUCTOS (NOMBRE,DESCRIPCION,PRECIO,EXISTENCIA,COLOR,TALLA,ID_CATEGORIA,IMAGEN,DESTACADO) VALUES (?,?,?,?,?,?,?,?,?)");
                $st->bind_param("ssdissisi",$nom,$des,$pre,$exi,$col,$tal,$cat,$img,$dest);
                $st->execute(); flash('ok',"Producto agregado");
            }else{
                $id=(int)$_POST['id'];
                $st=$conexion->prepare("UPDATE PRODUCTOS SET NOMBRE=?,DESCRIPCION=?,PRECIO=?,EXISTENCIA=?,COLOR=?,TALLA=?,ID_CATEGORIA=?,IMAGEN=?,DESTACADO=? WHERE ID_PRODUCTO=?");
                $st->bind_param("ssdissisii",$nom,$des,$pre,$exi,$col,$tal,$cat,$img,$dest,$id);
                $st->execute(); flash('ok',"Producto actualizado");
            }
        }elseif($accion === 'eliminar'){
            $id=(int)$_POST['id'];
            $st=$conexion->prepare("DELETE FROM PRODUCTOS WHERE ID_PRODUCTO=?");
            $st->bind_param("i",$id); $st->execute();
            flash('ok',"Producto eliminado");
        }
    }catch(mysqli_sql_exception $ex){
        if($ex->getCode() == 1451){ flash('error',"No se puede eliminar: el producto ya está en pedidos"); }
        else{ flash('error',"Error en la base de datos: datos demasiado largos o inválidos"); }
    }catch(Exception $ex){ flash('error',$ex->getMessage()); }
    header("Location: productos.php"); exit;
}

/*Datos para la pantalla*/
$edit = null;
if(isset($_GET['editar'])){
    $st=$conexion->prepare("SELECT * FROM PRODUCTOS WHERE ID_PRODUCTO=?");
    $id=(int)$_GET['editar']; $st->bind_param("i",$id); $st->execute();
    $edit=$st->get_result()->fetch_assoc();
}
$cats = $conexion->query("SELECT ID_CATEGORIA, NOMBRE_CATEGORIA FROM CATEGORIAS ORDER BY NOMBRE_CATEGORIA");
$listaCats = $cats->fetch_all(MYSQLI_ASSOC);
/*JOIN productos + categorías*/
$lista = $conexion->query("SELECT p.*, c.NOMBRE_CATEGORIA FROM PRODUCTOS p LEFT JOIN CATEGORIAS c ON c.ID_CATEGORIA = p.ID_CATEGORIA ORDER BY p.ID_PRODUCTO");

$ini="productos"; $tituloPagina="Productos";
include "../php/admin_header.php";
?>
<h1 class="adm-tit">Productos</h1>
<?php mostrarFlash(); ?>

<section class="adm-card">
  <h2><?php echo $edit ? 'Editar producto #'.$edit['ID_PRODUCTO'] : 'Agregar producto'; ?></h2>
  <form method="POST" class="form-adm">
    <input type="hidden" name="accion" value="<?php echo $edit ? 'editar' : 'insertar'; ?>">
    <?php if($edit){ ?><input type="hidden" name="id" value="<?php echo $edit['ID_PRODUCTO']; ?>"><?php } ?>
    <div><label>Nombre</label><input name="nombre" maxlength="30" required value="<?php echo e($edit['NOMBRE'] ?? ''); ?>"></div>
    <div><label>Descripción</label><input name="descripcion" maxlength="50" value="<?php echo e($edit['DESCRIPCION'] ?? ''); ?>"></div>
    <div><label>Precio</label><input name="precio" type="number" step="0.01" min="0" required value="<?php echo e($edit['PRECIO'] ?? ''); ?>"></div>
    <div><label>Existencia</label><input name="existencia" type="number" min="0" required value="<?php echo e($edit['EXISTENCIA'] ?? ''); ?>"></div>
    <div><label>Color</label><input name="color" maxlength="15" placeholder="Rosa, Negro, Blanco..." value="<?php echo e($edit['COLOR'] ?? ''); ?>"></div>
    <div><label>Talla</label><input name="talla" maxlength="5" value="<?php echo e($edit['TALLA'] ?? ''); ?>"></div>
    <div><label>Categoría</label>
      <select name="id_categoria" required>
        <?php foreach($listaCats as $c){ ?>
        <option value="<?php echo $c['ID_CATEGORIA']; ?>" <?php echo (($edit['ID_CATEGORIA'] ?? '') == $c['ID_CATEGORIA']) ? 'selected' : ''; ?>><?php echo e($c['NOMBRE_CATEGORIA']); ?></option>
        <?php } ?>
      </select></div>
    <div><label>Imagen (archivo en /img)</label><input name="imagen" maxlength="60" placeholder="Blusarosa.png" value="<?php echo e($edit['IMAGEN'] ?? ''); ?>"></div>
    <div class="chk"><label><input type="checkbox" name="destacado" <?php echo !empty($edit['DESTACADO']) ? 'checked' : ''; ?>> Mostrar en destacados del inicio</label></div>
    <div class="acciones-form">
      <button class="btn-adm" type="submit"><?php echo $edit ? 'Guardar cambios' : 'Agregar'; ?></button>
      <?php if($edit){ ?><a class="btn-adm sec" href="productos.php">Cancelar</a><?php } ?>
    </div>
  </form>
</section>

<section class="adm-card">
  <h2>Listado</h2>
  <div class="tabla-wrap"><table class="tabla">
    <tr><th>ID</th><th>Imagen</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Exist.</th><th>Color</th><th>Talla</th><th>Dest.</th><th>Acciones</th></tr>
    <?php while($p = $lista->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $p['ID_PRODUCTO']; ?></td>
      <td><?php if($p['IMAGEN']){ ?><img class="mini" src="../img/<?php echo e($p['IMAGEN']); ?>" alt=""><?php } ?></td>
      <td><?php echo e($p['NOMBRE']); ?></td>
      <td><?php echo e($p['NOMBRE_CATEGORIA']); ?></td>
      <td><?php echo dinero($p['PRECIO']); ?></td>
      <td><?php echo $p['EXISTENCIA']; ?></td>
      <td><?php echo e($p['COLOR']); ?></td>
      <td><?php echo e($p['TALLA']); ?></td>
      <td><?php echo $p['DESTACADO'] ? '<i class="fa fa-heart rosa"></i>' : ''; ?></td>
      <td class="acc">
        <a class="btn-adm peq" href="productos.php?editar=<?php echo $p['ID_PRODUCTO']; ?>">Editar</a>
        <form method="POST" onsubmit="return confirm('¿Eliminar este producto?');">
          <input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?php echo $p['ID_PRODUCTO']; ?>">
          <button class="btn-adm peq del" type="submit">Eliminar</button>
        </form>
      </td>
    </tr>
    <?php } ?>
  </table></div>
</section>
<?php include "../php/admin_footer.php"; ?>
