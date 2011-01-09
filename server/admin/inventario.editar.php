<h1>Editar producto</h1><?php


    require_once('model/inventario.dao.php');
    require_once('model/actualizacion_de_precio.dao.php');
    require_once('model/detalle_inventario.dao.php');




    if(isset($_REQUEST['editar_detalles'])){

        //cambiar los detalles en el inventario maestro
        $prod = InventarioDAO::getByPK($_REQUEST['id']);
        $prod->setDescripcion( $_REQUEST['descripcion'] );

        try{

            InventarioDAO::save($prod);

            echo "<div class='success'>Descripcion de producto actualizada correctamente.</div>";

        }catch(Exception $e){
            DAO::transRollback();
            echo "<div class='failure'>Error al actualizar: ". $e." </div>";
        }

    }


    if(isset($_REQUEST['editar'])){
        //cambiar el precio y costo del producto
        $na = new ActualizacionDePrecio();
        $na->setIdProducto($_REQUEST['id']);
        $na->setIdUsuario($_SESSION['userid']);
        $na->setPrecioVenta($_REQUEST['venta']);
        $na->setPrecioIntersucursal($_REQUEST['compra']);
        
        //cambiar todos los detalles inventario        
        $di = new DetalleInventario();
        $di->setIdProducto( $_REQUEST['id'] );
        $inventariosSucursales = DetalleInventarioDAO::search( $di );

        foreach ($inventariosSucursales as $i)
        {
            $i->setPrecioVenta( $_REQUEST['venta'] );
        }


        try{
            DAO::transBegin();

            ActualizacionDePrecioDAO::save( $na );

            foreach ($inventariosSucursales as $inv)
            {
                DetalleInventarioDAO::save($inv);
            }

            DAO::transEnd();
            echo "<div class='success'>Precio actualizado correctamente.</div>";
        }catch(Exception $e){
            DAO::transRollback();
            echo "<div class='failure'>Error al actualizar: ". $e." </div>";
        }

    }


    $producto = InventarioDAO::getByPK($_REQUEST['id']);

    //obtener la ultima actualizacion de precio para este producto
    //esos son sus detalles "generales"
    $a = new ActualizacionDePrecio();
    $a->setIdProducto($producto->getIdProducto());
    $actualizaciones = ActualizacionDePrecioDAO::search($a, 'fecha', 'desc');

    if(sizeof($actualizaciones) <= 0){
        Logger::log("No hay registro de actualizacion de precio para producto :" . $producto->getIdProducto());
    }
    
    $general = $actualizaciones[0];



?>



<script src="../frameworks/jquery/jquery-1.4.2.min.js" type="text/javascript" charset="utf-8"></script>
<script src="../frameworks/uniform/jquery.uniform.min.js" type="text/javascript" charset="utf-8"></script> 
<link rel="stylesheet" href="../frameworks/uniform/css/uniform.default.css" type="text/css" media="screen">
<script type="text/javascript" charset="utf-8">$(function(){$("input, select").uniform();});</script>


<h2>Editar descripcion</h2>
<form action="inventario.php?action=editar&id=<?php echo $general->getIdProducto(); ?>" method="POST">
<input type="hidden" name="editar_detalles" value="1">
<table border="0" cellspacing="5" cellpadding="5">
	<tr><td>Descripcion</td><td><input type="text" value="<?php echo $producto->getDescripcion();?>" size="40" name="descripcion"/></td></tr>
	<tr><td></td><td><input type="submit" value="Guardar" size="40"/></td></tr>
</table>
</form>


<h2>Editar Precio y Costo</h2>
<form action="inventario.php?action=editar&id=<?php echo $general->getIdProducto(); ?>" method="POST">
<input type="hidden" name="editar" value="1">
<table border="0" cellspacing="5" cellpadding="5">
	<tr><td>Precio intersucursal </td><td>  <input type="text" name="compra" value="<?php echo $general->getPrecioIntersucursal();?>" size="40"/></td></tr>
	<tr><td>Precio a la venta</td><td>      <input type="text" name="venta" value="<?php echo $general->getPrecioVenta(); ?>" size="40"/></td></tr>
	<tr><td></td><td>                       <input type="submit" value="Guardar" size="40"/></td></tr>
</table>
</form>	





