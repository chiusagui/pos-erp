<?php 

require_once('../server/model/inventario.dao.php');
require_once('../server/model/detalle_inventario.dao.php');


/*
 * listar las existencias para ESTA sucursal
 * */
function listarInventario(  ){
	
	$q = new DetalleInventario();
	$q->setIdSucursal( $_SESSION["sucursal"] ); 
	
	$results = DetalleInventarioDAO::search( $q );
	
	$json = array();
	
	foreach( $results as $producto ){
		$productoData = InventarioDAO::getByPK( $producto->getIdProducto() );
		
		Array_push( $json , array(
			"productoID" => $productoData->getIdProducto(),
			"descripcion" => $productoData->getDescripcion(),
			"precioVenta" => $producto->getPrecioVenta(),
			"existenciasMinimas" => $producto->getMin(),
			"existencias" => $producto->getExistencias(),
			"medida" => $productoData->getMedida(),
			"precioIntersucursal" => $productoData->getPrecioIntersucursal()
		));
	}
	
	printf('{ "success": true, "datos": %s }',  json_encode($json));

}

function detalleProductoSucursal( $args ){

    if( !isset( $args['id_producto'] ) )
    {
        die('{"success": false, "reason": "No hay parametros para ingresar." }');
    }

    $detalleInventario = new DetalleInventario();
    $detalleInventario->setIdProducto( $args['id_producto'] );
    $detalleInventario->setIdSucursal( $_SESSION['sucursal'] );
    
    $producto = DetalleInventarioDAO::search( $detalleInventario, true );

    printf('{ "success": true, "datos": %s }',  $producto);

}

switch($args['action']){
	case 400:
		listarInventario(  );
	break;

    case 401://regresa el detalle del producto en la sucursal actual
        detalleProductoSucursal( $args );
    break;

    case 402:
        //solicitarProducto(  );
    break;

    case 403:

    break;

    case 404:

    break;

    case 405:

    break;

}
