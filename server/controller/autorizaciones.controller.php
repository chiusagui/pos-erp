<?php

/**
 *  Controller para autorizaciones
 */
 
require_once("model/autorizacion.dao.php");
require_once("model/detalle_inventario.dao.php");
require_once("model/cliente.dao.php");
require_once("model/inventario.dao.php");
require_once('model/actualizacion_de_precio.dao.php');





/**
  * Sucursal solicita autorizacion.
  * 
  * Cuando una sucursal solicita una autorizacion se pasa
  * por aqui, y se inserta tal cual.
  * 
  * 
  **/
function solicitudDeAutorizacion( $auth, $tipo ){


	
    $autorizacion = new Autorizacion();
    
    $autorizacion->setIdUsuario( $_SESSION['userid'] );
    $autorizacion->setIdSucursal( $_SESSION['sucursal'] );
    $autorizacion->setEstado('0');
    $autorizacion->setTipo( $tipo );    

    $autorizacion->setParametros( $auth );

    try{
        AutorizacionDAO::save( $autorizacion );

    }catch(Exception $e){

        Logger::log($e);
        die( '{ "success" : false , "reason" : "No se pudo enviar la autorizacion."}' );
    }

    echo '{"success": true }';
	Logger::log("Solicitud de sucursal creada satisfactoriamente.");

}//solicitudDeAutorizacion


function autorizacionesPendientes(  ){
    
    $autorizacion = new Autorizacion();
    $autorizacion->setEstado('0');

    $array =  AutorizacionDAO::search( $autorizacion );

    return $array;

}//autorizacionesPendientes


function autorizacionesSucursal( $sid ){

	$now = date ( "Y-m-d" );
	
    $foo = new Autorizacion();
    $foo->setIdSucursal( $sid );
    $foo->setFechaPeticion( $now . " 00:00:00" );

    $bar = new Autorizacion();
    $bar->setFechaPeticion($now . " 23:59:59");
    
    $autorizaciones = AutorizacionDAO::byRange($foo, $bar);

    $array_autorizaciones = array();

    foreach($autorizaciones as $autorizacion)
    {

        
        //si la autorizacion contiene productos, agregarles,
        //la descripcion
        if($autorizacion->getTipo() == 'envioDeProductosASucursal'
            || $autorizacion->getTipo() == 'solicitudDeProductos'
            ){

            $params = json_decode($autorizacion->getParametros());

            foreach($params->productos as $key => $prod){

                $foo = InventarioDAO::getByPK( $prod->id_producto );

                $params->productos[$key]->descripcion = $foo->getDescripcion();
                $params->productos[$key]->escala = $foo->getEscala();
                
            }
            
            $autorizacion->setParametros(json_encode( $params ));

        }

        $auth = $autorizacion->asArray();
        array_push( $array_autorizaciones, $auth );
    }
    return $array_autorizaciones;

}




/**
  * Surtir una sucursal dada una autorizacion.
  *
  * Surtir una autorizacion en transtio, esta funcion se llama
  * cuando el admin surte una sucursal de su inventario maestro,
  * y el cargamento ha llegado a la sucursal. Se encarga de poner
  * esa autorizacion como contestada y modificar el inventario de 
  * esa sucursal sumandole lo que viene en ese cargamento. Si uno
  * o mas productos no se encuentran ya en el inventario {DetalleInventario}
  * entonces esta funcion los insertan.
  *
  * @param id_autorizacion El id de una autorizacion existente en estado 3
  *
  **/
function surtirProducto($id_autorizacion){


	

    if( !( $autorizacion = AutorizacionDAO::getByPK( $id_autorizacion ) ) ){
        die('{"success": false, "reason": "La autorizacion no existe. El administrador pudo haber eliminado esta autorizacion." }');
    }

    if( $autorizacion->getEstado() != 3 ){
        die('{"success": false, "reason": "Esta autorizacion ya ha sido aprovada." }');
    }

    try{
        $data = parseJSON( $autorizacion->getParametros() );
    }catch(Exception $e){
        Logger::log($e);
        die( '{"success": false, "reason": "Parametros invalidos." }' );
    }



    $productos = $data->productos;

	DAO::transBegin();

    foreach( $productos as $producto ){

        //buscar la existencia de cada producto en la sucursal en la que voy
        //a insertar
        $p = DetalleInventarioDAO::getByPK( $producto->id_producto, $_SESSION['sucursal'] );
        
        if( !$p )
        {
            //no existe en la sucursal, voy a ver si existe en el inventario global
            $producto_inventario = InventarioDAO::getByPK( $producto->id_producto );
            
            if( !$producto_inventario ){
               Logger::log("Se ha intentado surtir un producto que no existe");
				DAO::transRollback();    
                die( '{ "success" : false , "reason" : "El producto ' . $producto->id_producto . ' no se encuentra registrado en el inventario."}' );
            }
            
            //el producto existe en el inventario global pero no en la sucursal, insertarlo
            $nuevo_detalle_producto = new DetalleInventario();
            $nuevo_detalle_producto->setIdProducto( $producto->id_producto );    
            $nuevo_detalle_producto->setIdSucursal( $_SESSION['sucursal'] );
            $nuevo_detalle_producto->setExistencias( 0 );            
            $nuevo_detalle_producto->setExistenciasProcesadas( 0 );             

            //buscar la ultima actualizacion de precio para este producto
            $foo = new ActualizacionDePrecio();
            $foo->setIdProducto($producto->id_producto);
            $actualizacion = ActualizacionDePrecioDAO::search( $foo, 'fecha', 'DESC' );
            $actualizacion = $actualizacion[0];
            
            $nuevo_detalle_producto->setPrecioVenta( $actualizacion->getPrecioVenta() );

            
            try{
                DetalleInventarioDAO::save( $nuevo_detalle_producto );

            }catch(Exception $e){

				DAO::transRollback();    
                Logger::log($e);
                die( '{ "success" : false , "reason" : "Error al surtir esta autorizacion, intente de nuevo."}' );
            }

            $p = $nuevo_detalle_producto;
        }

    
        
        if($producto->procesada){
            $existencias = $p->getExistenciasProcesadas();
            $existencias += $producto->cantidad;
            $p->setExistenciasProcesadas( $existencias );
        }else{
            $existencias = $p->getExistencias();
            $existencias += $producto->cantidad;
            $p->setExistencias( $existencias );
        }


       //guardamos los cambios
        try{
            DetalleInventarioDAO::save($p);

        }catch(Exception $e){
            Logger::log($e);        
			DAO::transRollback();    
            die( '{"success": false, "reason": "Error al surtir esta autorizacion, intente de nuevo." }' );
        }

    }

    //cambiamos el estado de la autorizacion a surtido
    $autorizacion->setEstado(4);

    try{
        AutorizacionDAO::save( $autorizacion );
    }catch(Exception $e){
		DAO::transRollback();    
        Logger::log($e);
        die( '{ "success" : false , "reason" : "Error al surtir esta autorizacion, intente de nuevo."}' );
    }

    echo '{"success" : true }';    
    Logger::log("Atuorizacion surtida satisfactoriamente");
    DAO::transEnd();
}







//responder autorizacion surtir (admin)
function respuestaAutorizacionSurtir( $args ){

    //SUPER IMPORTANTE QUE DATA TENGA PARENTESIS CUADRADO 
    //data=[{"id_pruducto":"1","cantidad":"55.5"},{"id_pruducto":"1","cantidad":"2"}]

    //estado puede valer:
    // 0->sin revisar
    // 1->aprovado
    // 2->denegado                               <------ESTO ES LO NORMAL PARA EL CASO DE RESPUESTA DE SURTIR
    // 3->aprovado y sin surtir por el gerente   <------ESTO ES LO NORMAL PARA EL CASO DE RESPUESTA DE SURTIR
    // 4->aprovado y surtido por el gerente

    //FORMATO DE UNA AUTORIZACION DE SURTIR

    /*  {
            "id_autorizacion":"1",
            "id_usuario":"38",
            "id_sucursal":"54",
            "fecha_peticion":"2010-12-17 19:33:56",
            "fecha_respuesta":"2010-12-17 22:38:14",
            "estado":"2",
            "parametros":"{
                \"clave\":\"209\",
                \"descripcion\":\"Solicitud de producto\",
                \"productos\":[
                    {
                        \"id_producto\":\"1\",
                        \"cantidad\":\"55.5\"
                    },
                    {
                        \"id_producto\":\"1\",
                        \"cantidad\":\"2\"
                    }
                ]
            }"
        }  */




    if(!isset($args['data']) || !isset($args['id_autorizacion']) || !isset($args['estado']))
    {
        die('{"success": false, "reason": "Faltan parametros." }');
    }

	
    try
    {
        $data = parseJSON( $args['data'] );
    }
    catch(Exception $e)
    {
        die( '{"success": false, "reason": "Parametros invalidos." }' );
    }

    if( !( $autorizacion = AutorizacionDAO::getByPK( $args['id_autorizacion'] ) ) )
    {
        die('{"success": false, "reason": "Verifique que exista la autorizacion ' . $args['id_autorizacion'] . '." }');
    }

    //establecemos el nuevo estado
    $autorizacion->setEstado( $args['estado'] );

    $parametros = json_encode(array(
        'clave'=>'209',
        'descripcion'=>'Respuesta de producto',
        'productos'=>$data
    ));


    //definimos los nuevos parametros
    $autorizacion->setParametros( $parametros );

    try
    {
        if( AutorizacionDAO::save( $autorizacion ) > 0 )
        {
            printf( '{ "success" : true }' );
        }
        else
        {
            die( '{ "success" : false , "reason" : "Error al cambiar el estado de la autorización."}' );
        }
    }
    catch(Exception $e)
    {
        die( '{ "success" : false , "reason" : "Exception al cambiar estado de la autorización."}' );
    }

}


//funcion que regresa el detalle de una autorizacion
function detalleAutorizacion( $args ){

    if( !isset ($args['id_autorizacion'] ) )
    {
        die('{"success": false, "reason": "Faltan parametros." }');
    }

    if( !( $autorizacion = AutorizacionDAO::getByPK( $args['id_autorizacion'] ) ) )
    {
        die('{"success": false, "reason": "Verifique que exista la autorizacion ' . $args['id_autorizacion'] . '." }');
    }

    return $autorizacion;

}

//el admin puede surtir productos de la nada a las sucursales
function surtirProductosSucursal( $args ){

    DAO::transBegin();

    if( !isset($args['data']) || !isset($args['id_sucursal']) )
    {
        Logger::log("Faltan parametros para surtir sucursal.");
        DAO::transRollback();
        die('{"success": false, "reason": "Faltan parametros." }');
    }

    try
    {
	    $data = parseJSON( $args['data'] );
    }
    catch(Exception $e)
    {
        Logger::log("JSON invalido para surtir sucursal.");
        DAO::transRollback();
        die( '{"success": false, "reason": "Parametros invalidos." }' );
    }

    if( isset($args['responseToAut']) && $args['responseToAut'] !== "null"){
        $autorizacion = AutorizacionDAO::getByPK( $args['responseToAut'] );
    }else{
        $autorizacion = new Autorizacion();        
    }


    $autorizacion->setIdUsuario	( $_SESSION['userid'] );
    $autorizacion->setEstado	( '3' );
    $autorizacion->setIdSucursal( $args['id_sucursal'] );

    $time = strftime( "%Y-%m-%d-%H-%M-%S", time() );

    $autorizacion->setFechaPeticion( $time  );
    $autorizacion->setFechaRespuesta( $time );

    $parametros = json_encode(array(
        'clave'=>'209',
        'descripcion'=>'Envio de productos',
        'productos'=>$data
    ));

    $autorizacion->setParametros( $parametros );

    try
    {
        AutorizacionDAO::save( $autorizacion );
        printf( '{ "success" : true }' );

    }
    catch(Exception $e)
    {

        DAO::transRollback();
        Logger::log("Imposible guardar autorizacion: " . $e);
        die( '{ "success" : false , "reason" : "Exception al cambiar estado de la autorización."}' );
    }


    DAO::transEnd();
    Logger::log("Surtir sucursal exitoso. Autorizacion=" . $autorizacion->getIdAutorizacion() );
}


//responder autorizacion de gasto (admin)
function respuestaAutorizacionGasto( $args ){

    if( !isset( $args['reply'] ) || !isset( $args['id_autorizacion'] )  )
    {
        die( '{"success": false, "reason": "Faltan parametros." }' );
    }

    if( !( $args['reply'] == 1 || $args['reply'] == 2 ) )
    {
        die( '{"success": false, "reason": "Parametros invalidos." }' );
    }

    $autorizacion = AutorizacionDAOBase::getByPK( $args['id_autorizacion'] );
    $autorizacion->setFechaRespuesta( strftime( "%Y-%m-%d-%H-%M-%S", time() ) );
    $autorizacion->setEstado( $args['reply'] );

    try
    {
        if( AutorizacionDAO::save( $autorizacion ) > 0 )
        {
            printf( '{ "success" : true }' );
        }
        else
        {
            die( '{ "success" : false , "reason" : "No se pudo responder la autorizacion."}' );
        }
    }
    catch(Exception $e)
    {
        die( '{ "success" : false , "reason" : "Exception al cambiar estado de la autorizacion."}' );
    }

}


function respuestaAutorizacionDevolucion( $args ){

    //pido los datos en data, para ahorrarle carga al servidor de decodearlos al buscar por id, la autorizacion en un getByPK

    if( !isset( $args['data'] ) )
    {
        die('{"success": false, "reason": "Faltan parametros." }');
    }

    try
    {
	    $data = parseJSON( $args['data'] );
    }
    catch(Exception $e)
    {
        die( '{"success": false, "reason": "Parametros invalidos." }' );
    }



    if( !isset( $data->reply ) || !isset( $data->id_autorizacion ) || !isset( $data->id_producto ) || !isset( $data->cantidad ) || !isset( $data->id_venta ) || !isset( $data->id_sucursal ) )
    {
        die( '{"success": false, "reason": "Faltan parametros." }' );
    }

    if( !( $data->reply == 1 || $data->reply == 2 ) )
    {
        die( '{"success": false, "reason": "Parametros invalidos." }' );
    }
    
    

    $autorizacion = AutorizacionDAOBase::getByPK( $data->id_autorizacion );
    $autorizacion->setFechaRespuesta( strftime( "%Y-%m-%d-%H-%M-%S", time() ) );
    $autorizacion->setEstado( $data->reply );

    try
    {
        if( AutorizacionDAO::save( $autorizacion ) > 0 )
        {
            //aqui entra en caso de qeu se haya cambiado el estado de la autorizacion
            if($data->reply == 1)
            {
                //aqui entra en caso de que se haya aprobado la autorizacion de merma
                
                //hay que modificar el limite de credito del cliente
                if( !( $detalle_inventario = DetalleInventarioDAO::getByPK( $data->id_producto, $data->id_sucursal ) ) )
                {
                    die('{"success": false, "reason": "Verifique que exista el producto ' . $data->id_producto . ' en la sucursal ' . $data->id_sucursal . '." }');
                }
                
                //cambiamos las existencias en el inventario
                $detalle_inventario->setExistencias( $detalle_inventario->getExistencias() + $data->cantidad );
                
                
                try
                {
                    if( DetalleInventarioDAO::save( $detalle_inventario ) > 0 )
                    {
                        //modificamos el detalle de la venta
                        if( !( $detalle_venta = DetalleCompraDAO::getByPK( $data->id_venta, $data->id_producto ) ) )
                        {
                            die('{"success": false, "reason": "Verifique que exista el producto ' . $data->id_producto . ' en la venta' . $data->id_venta . '." }');
                        }
                        
                        //cambiamos las existencias en el detalle compra
                        $detalle_venta->setCantidad( $detalle_venta->getCantidad() - $data->cantidad );
                        
                        
                        try
                        {
                            if( DetalleVentaDAO::save( $detalle_venta ) > 0 )
                            {
                                //modificamos el total de la venta
                                if( !( $venta = ComprasDAO::getByPK( $data->id_venta ) ) )
                                {
                                    die('{"success": false, "reason": "Verifique que exista la venta ' . $data->id_venta . '." }');
                                }
                                
                                //recalculamos el total de la venta
                                $valor_mercancia_devuelta =  $detalle_venta->getPrecio() * $data->cantidad;
                                $venta->setSubtotal( $venta->getSubtotal() - $valor_mercancia_devuelta );
                                $venta->setTotal( ($venta->getSubtotal() + $venta->getIva() ) * $venta->getDescuento() );
                                
                                try
                                {
                                    if( VentasDAO::save( $venta ) > 0 )
                                    {
                                        printf( '{ "success" : true }' );
                                    }
                                    else
                                    {
                                        die( '{ "success" : false , "reason" : "No se pudo modificar el total de la venta."}' );
                                    }
                                }
                                catch(Exception $e)
                                {
                                    die( '{ "success" : false , "reason" : "Exception al modificar el valor de la venta."}' );
                                }
                                
                                
                            }
                            else
                            {
                                die( '{ "success" : false , "reason" : "No se pudo modificar el detalle de la venta."}' );
                            }
                        }
                        catch(Exception $e)
                        {
                            die( '{ "success" : false , "reason" : "Exception al modificar el detalle de la venta."}' );
                        }
 
                        
                    }
                    else
                    {
                        die( '{ "success" : false , "reason" : "No se pudo modificar las existencias en el inventario."}' );
                    }
                }
                catch(Exception $e)
                {
                    die( '{ "success" : false , "reason" : "Exception al modificar las existencias en el inventario."}' );
                }
                   
            }
            else
            {
                //entra si no se aprovo el cambio de limite de credito
                printf( '{ "success" : true }' );
            }
            
        }
        else
        {
            die( '{ "success" : false , "reason" : "No se pudo responder la autorizacion."}' );
        }
    }
    catch(Exception $e)
    {
        die( '{ "success" : false , "reason" : "Exception al cambiar estado de la autorizacion."}' );
    }

}


function respuestaAutorizacionMerma( $args ){
    
    //pido los datos en data, para ahorrarle carga al servidor de decodearlos al buscar por id, la autorizacion en un getByPK

    if( !isset( $args['data'] ) )
    {
        die('{"success": false, "reason": "Faltan parametros." }');
    }

    try
    {
        $data = parseJSON( $args['data'] );

    }
    catch(Exception $e)
    {
        die( '{"success": false, "reason": "Parametros invalidos." }' );
    }



    if( !isset( $data->reply ) || !isset( $data->id_autorizacion ) || !isset( $data->id_producto ) || !isset( $data->cantidad ) || !isset( $data->id_compra ) || !isset( $data->id_sucursal ) )
    {
        die( '{"success": false, "reason": "Faltan parametros." }' );
    }

    if( !( $data->reply == 1 || $data->reply == 2 ) )
    {
        die( '{"success": false, "reason": "Parametros invalidos." }' );
    }
    
    

    $autorizacion = AutorizacionDAOBase::getByPK( $data->id_autorizacion );
    $autorizacion->setFechaRespuesta( strftime( "%Y-%m-%d-%H-%M-%S", time() ) );
    $autorizacion->setEstado( $data->reply );

    try
    {
        if( AutorizacionDAO::save( $autorizacion ) > 0 )
        {
            //aqui entra en caso de qeu se haya cambiado el estado de la autorizacion
            if($data->reply == 1)
            {
                //aqui entra en caso de que se haya aprobado la autorizacion de merma
                
                //hay que modificar el limite de credito del cliente
                if( !( $detalle_inventario = DetalleInventarioDAO::getByPK( $data->id_producto, $data->id_sucursal ) ) )
                {
                    die('{"success": false, "reason": "Verifique que exista el producto ' . $data->id_producto . ' en la sucursal ' . $data->id_sucursal . '." }');
                }
                
                //cambiamos las existencias en el inventario
                $detalle_inventario->setExistencias( $detalle_inventario->getExistencias() + $data->cantidad );
                
                
                try
                {
                    if( DetalleInventarioDAO::save( $detalle_inventario ) > 0 )
                    {
                        //modificamos el detalle de la compra
                        if( !( $detalle_compra = DetalleCompraDAO::getByPK( $data->id_compra, $data->id_producto ) ) )
                        {
                            die('{"success": false, "reason": "Verifique que exista el producto ' . $data->id_producto . ' en la compra' . $data->id_compra . '." }');
                        }
                        
                        //cambiamos las existencias en el detalle compra
                        $detalle_compra->setCantidad( $detalle_compra->getCantidad() - $data->cantidad );
                        
                        
                        try
                        {
                            if( DetalleCompraDAO::save( $detalle_compra ) > 0 )
                            {
                                //modificamos el total de la compra
                                if( !( $compra = ComprasDAO::getByPK( $data->id_compra ) ) )
                                {
                                    die('{"success": false, "reason": "Verifique que exista la compra ' . $data->id_compra . '." }');
                                }
                                
                                //recalculamos el total de la compra
                                $valor_mercancia_devuelta =  $detalle_compra->getPrecio() * $data->cantidad;
                                $compra->setSubtotal( $compra->getSubtotal() - $valor_mercancia_devuelta );
                                
                                
                                try
                                {
                                    if( ComprasDAO::save( $compra ) > 0 )
                                    {
                                        printf( '{ "success" : true }' );
                                    }
                                    else
                                    {
                                        die( '{ "success" : false , "reason" : "No se pudo modificar el total de la compra."}' );
                                    }
                                }
                                catch(Exception $e)
                                {
                                    die( '{ "success" : false , "reason" : "Exception al modificar el valor de la comrpa."}' );
                                }
                                
                                
                            }
                            else
                            {
                                die( '{ "success" : false , "reason" : "No se pudo modificar el detalle de la compra."}' );
                            }
                        }
                        catch(Exception $e)
                        {
                            die( '{ "success" : false , "reason" : "Exception al modificar el detalle de la compra."}' );
                        }
 
                        
                    }
                    else
                    {
                        die( '{ "success" : false , "reason" : "No se pudo modificar las existencias en el inventario."}' );
                    }
                }
                catch(Exception $e)
                {
                    die( '{ "success" : false , "reason" : "Exception al modificar las existencias en el inventario."}' );
                }
                   
            }
            else
            {
                //entra si no se aprovo el cambio de limite de credito
                printf( '{ "success" : true }' );
            }
            
        }
        else
        {
            die( '{ "success" : false , "reason" : "No se pudo responder la autorizacion."}' );
        }
    }
    catch(Exception $e)
    {
        die( '{ "success" : false , "reason" : "Exception al cambiar estado de la autorizacion."}' );
    }

}



function respuestaAutorizacionLimiteCredito( $args ){

    //pido los datos en data, para ahorrarle carga al servidor de decodearlos al buscar por id, la autorizacion en un getByPK

    if( !isset( $args['data'] ) )
    {
        die('{"success": false, "reason": "Faltan parametros." }');
    }

    try
    {
        $data = parseJSON( $args['data'] );
    }
    catch(Exception $e)
    {
        die( '{"success": false, "reason": "Parametros invalidos." }' );
    }



    if( !isset( $data->reply ) || !isset( $data->id_autorizacion ) || !isset( $data->limite_credito ) || !isset( $data->id_cliente ) )
    {
        die( '{"success": false, "reason": "Faltan parametros." }' );
    }

    if( !( $data->reply == 1 || $data->reply == 2 ) )
    {
        die( '{"success": false, "reason": "Parametros invalidos." }' );
    }

    $autorizacion = AutorizacionDAOBase::getByPK( $data->id_autorizacion );
    $autorizacion->setFechaRespuesta( strftime( "%Y-%m-%d-%H-%M-%S", time() ) );
    $autorizacion->setEstado( $data->reply );

    try
    {
        if( AutorizacionDAO::save( $autorizacion ) > 0 )
        {
            //aqui entra en caso de qeu se haya cambiado el estado de la autorizacion
            if($data->reply == 1)
            {
                //aqui estra en caso de que se haya aprobado el cambio de limite de credito 
                
                //hay que modificar el limite de credito del cliente
                if( !( $cliente = ClienteDAO::getByPK( $data->id_cliente ) ) )
                {
                    die('{"success": false, "reason": "Verifique que exista el cliente ' . $data->id_cliente . '." }');
                }
                
                $cliente->setLimiteCredito( $data->limite_credito );
                
                try
                {
                    if( ClienteDAO::save( $cliente ) > 0 )
                    {
                        printf( '{ "success" : true }' );
                    }
                    else
                    {
                        die( '{ "success" : false , "reason" : "No se pudo responder la autorizacion."}' );
                    }
                }
                catch(Exception $e)
                {
                    die( '{ "success" : false , "reason" : "Exception al cambiar estado de la autorizacion."}' );
                }
                   
            }
            else
            {
                //entra si no se aprovo el cambio de limite de credito
                printf( '{ "success" : true }' );
            }
            
        }
        else
        {
            die( '{ "success" : false , "reason" : "No se pudo responder la autorizacion."}' );
        }
    }
    catch(Exception $e)
    {
        die( '{ "success" : false , "reason" : "Exception al cambiar estado de la autorizacion."}' );
    }

}
	/**Cancelar autorizacion.
	  *
	  * Este metodo cambia el estado actual de la autorizacion cuyo id se pasa como argumento. Si dicha autorizacion no
	  * existe se regresara success false en JSON. Si la autorizacion existe se cambiara el estado a 5 y se regresara 
	  * success true en JSON.
	  *
	  * @param int(11) id_autorizacion
	  * @return void
	  **/

function cancelarAutorizacion($args){

	$autorizacion=AutorizacionDAO::getByPK($args);
	
	$existe=$autorizacion==NULL?die('{"success"	:	false	,	"reason"	:	"No existe esa autorizacion"}'):"";
	
	$autorizacion->setEstado(5);
	
	try{	$result=AutorizacionDAO::save($autorizacion);	echo '{"success"	:	true	}';
	
	}catch(Exception $e){	
	
		Logger::log($e);
		die('{"success"	:	false	,	"reason"	:	"No se realizo el cambio de estado, intente de nuevo."}');
	}

}


if( isset( $args['action'] ) ){

    switch( $args['action'] ){

        case 201://solicitud de autorizacion de gasto (gerente)

            if( !isset( $args['concepto'] ) || !isset( $args['monto'] ) )
            {
                die( '{ "success" : false , "reason" : "Faltan datos" }' );
            }
        
            if( !is_numeric( $args['monto'] ) )
            {
                die( '{ "success" : false , "reason" : "No es una cantidad valida." }' );
            }
        
            $descripcion = json_encode(array(
                'clave'=>$args['action'],
                'descripcion'=>'Autorización de gasto',
                'concepto'=>$args['concepto'],
                'monto'=>$args['monto']
            ));
        
//            solicitudDeAutorizacion( $descripcion,"" );
            solicitudDeAutorizacion( $descripcion, "solicitudDeGasto" );

        break;

        case 202://solicitud de autorizacion de cambio de limite de credito (gerente)

            if( !isset( $args['id_cliente'] ) || !isset( $args['cantidad'] ) )
            {
                die( '{ "success" : false , "reason" : "Faltan datos" }' );
            }

            if( !is_numeric( $args['cantidad'] ) )
            {
                die( '{ "success" : false , "reason" : "No es una cantidad valida." }' );
            }

            $descripcion = json_encode(array(
                'clave'=>$args['action'],
                'descripcion'=>'Autorización de limite de crédito',
                'id_cliente'=>$args['id_cliente'],
                'cantidad'=>$args['cantidad']
            ));

//            solicitudDeAutorizacion( $descripcion,"" );
            solicitudDeAutorizacion( $descripcion, "solicitudDeCambioLimiteDeCredito" );

        break;

        case 203://solicitud de autorizacion de devolucion (gerente)

            if( !isset($args['data']) )
            {
                die('{"success": false, "reason": "No hay parametros para ingresar." }');
            }
            
            try
            {
 
                $data = parseJSON( $args['data'] );
            }
            catch(Exception $e)
            {
                die( '{"success": false, "reason": "Parametros invalidos." }' );
            }

            if(!is_numeric( $data -> cantidad ))
            {
                die( '{ "success" : false , "reason" : "No es una cantidad valida." }' );
            }

            $descripcion = json_encode(array(
                'clave'=>$args['action'],
                'descripcion'=>'Autorización de devolución',
                'id_venta'=>$data -> id_venta,
                'id_producto'=>$data -> id_producto,
                'cantidad'=>$data -> cantidad
            ));

//            solicitudDeAutorizacion( $descripcion );
            solicitudDeAutorizacion( $descripcion, "solicitudDeDevolucion" );

        break;

        case 204://solicitud de autorizacion de cambio de precio (gerente)
            
            if( !isset( $args['id_producto'] ) || !isset( $args['precio'] ) )
            {
                die( '{ "success" : false , "reason" : "Faltan datos" }' );
            }

            if( !is_numeric( $args['precio'] ) )
            {
                die( '{ "success" : false , "reason" : "No es una cantidad valida." }' );
            }

            $descripcion = json_encode(array(
                'clave'=>$args['action'],
                'descripcion'=>'Autorización de cambio de precio',
                'id_producto'=>$args['id_producto'],
                'precio'=>$args['precio']
            ));

//            solicitudDeAutorizacion( $descripcion );
            solicitudDeAutorizacion( $descripcion, "solicitudDeCambioPrecio" );


        break;

        case 205:////solicitud de autorizacion de merma (gerente)

            if( !isset($args['data']) )
            {
                die('{"success": false, "reason": "No hay parametros para ingresar." }');
            }
            
            try
            {
				$data = parseJSON( $args['data'] );
            }
            catch(Exception $e)
            {
                die( '{"success": false, "reason": "Parametros invalidos." }' );
            }

            if(!is_numeric( $data -> cantidad ))
            {
                die( '{ "success" : false , "reason" : "No es una cantidad valida." }' );
            }

            $descripcion = json_encode(array(
                'clave'=>$args['action'],
                'descripcion'=>'Autorización de merma',
                'id_compra'=>$data -> id_compra,
                'id_producto'=>$data -> id_producto,
                'cantidad'=>$data -> cantidad
            ));

//            solicitudDeAutorizacion( $descripcion );
            solicitudDeAutorizacion( $descripcion, "solicitudDeMerma" );

        break;

        case 206://ver autorizaciones pendientes de todas las sucursales (admin)
            autorizacionesPendientes( );
        break;

        case 207://ver autorizaciones de su sucursal (gerente)
            $json = json_encode( autorizacionesSucursal( $_SESSION['sucursal'] ) );
            
            if(isset($args['hashCheck'])){
                //revisar hashes
                if(md5( $json ) == $args['hashCheck'] ){
                    return;
                }
            }

	    	printf('{ "success": true, "hash" : "%s" , "payload": %s }',  md5($json), $json );
        break;

        case 208:
        
        break;

        case 209://solicitud de uno o mas productos (gerente)

            //SUPER IMPORTANTE QUE DATA TENGA PARENTESIS CUADRADO
            //data=[{"id_producto":"1","cantidad":"55.5"},{"id_producto":"1","cantidad":"2"}]

            if(!isset($args['data']))
            {
                die('{"success": false, "reason": "No hay parametros para ingresar." }');
            }

            try
            {
				$data = parseJSON( $args['data'] );
            }
            catch(Exception $e)
            {
                die( '{"success": false, "reason": "Parametros invalidos." }' );
            }

            $descripcion = json_encode(array(
                'clave'=> 210,
                'descripcion'=>'Solicitud de producto',
                'productos'=>$data
            ));

            solicitudDeAutorizacion( $descripcion, "solicitudDeProductos" );

        break;

        case 210://respuesta de solicitud de producto (admin)
            respuestaAutorizacionSurtir( $args );
        break;

        case 211://surtir producto (gerente)
            //ya que el admin envio la mercancia a la sucursal y esta ha llegado a la
            //sucursal del gerente, este debera de verificar que conicida con lo enviado
            //por el admin, cuando todo este aclarado y listo, el gerente debera de liberar
            //la solicitud de producto para que se cargue al inventario lo que surtio el
            //admin.
            
            if(!(isset($args['id_autorizacion']))){
                die('{"success" : false , "reason" : "Parametros invalidos" }');
            }

            surtirProducto( $args['id_autorizacion'] );

        break;

 

        case 213://detalle de autorizacion (admin)
            detalleAutorizacion( $args );
        break;

        case 214://surtir productos sucursal (admin)
            surtirProductosSucursal( $args );
        break;
        
        case 215://respuesta de autorizacion de gasto
            respuestaAutorizacionGasto( $args );
        break;

        case 216://respuesta de devolucion
            respuestaAutorizacionDevolucion( $args );
        break;
        
        case 217://respuesta de merma
            respuestaAutorizacionMerma( $args );
        break;
        
        case 218://respuesta de limite de credito
            respuestaAutorizacionLimiteCredito( $args );
        break;
        
        case 219://cancelar autorizacion, cambiar a estado 5
        	cancelarAutorizacion($args["id_autorizacion"]);
        
        break;


    }
}
//sigue inventario

?>
