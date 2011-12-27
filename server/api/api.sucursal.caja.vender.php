<?php
/**
  * POST api/sucursal/caja/vender
  * Venta de productos desde el mostrador de una sucursal.
  *
  * Vender productos desde el mostrador de una sucursal. Cualquier producto vendido aqui sera descontado del inventario de esta sucursal. La fecha ser? tomada del servidor, el usuario y la sucursal ser?n tomados del servidor. La ip ser? tomada de la m?quina que manda a llamar al m?todo. El valor del campo liquidada depender? de los campos total y pagado. La empresa se tomara del alamcen de donde salieron los productos
  *
  *
  *
  **/

  class ApiSucursalCajaVender extends ApiHandler {
  

	protected function DeclareAllowedRoles(){  return BYPASS;  }
	protected function GetRequest()
	{
		$this->request = array(	
			"descuento" => new ApiExposedProperty("descuento", true, POST, array( "float" )),
			"id_comprador" => new ApiExposedProperty("id_comprador", true, POST, array( "int" )),
			"impuesto" => new ApiExposedProperty("impuesto", true, POST, array( "float" )),
			"retencion" => new ApiExposedProperty("retencion", true, POST, array( "float" )),
			"subtotal" => new ApiExposedProperty("subtotal", true, POST, array( "float" )),
			"tipo_venta" => new ApiExposedProperty("tipo_venta", true, POST, array( "string" )),
			"total" => new ApiExposedProperty("total", true, POST, array( "float" )),
			"billetes_cambio" => new ApiExposedProperty("billetes_cambio", false, POST, array( "json" )),
			"billetes_pago" => new ApiExposedProperty("billetes_pago", false, POST, array( "json" )),
			"cheques" => new ApiExposedProperty("cheques", false, POST, array( "json" )),
			"detalle_orden" => new ApiExposedProperty("detalle_orden", false, POST, array( "json" )),
			"detalle_paquete" => new ApiExposedProperty("detalle_paquete", false, POST, array( "json" )),
			"detalle_producto" => new ApiExposedProperty("detalle_producto", false, POST, array( "json" )),
			"id_caja" => new ApiExposedProperty("id_caja", false, POST, array( "int" )),
			"id_sucursal" => new ApiExposedProperty("id_sucursal", false, POST, array( "int" )),
			"id_venta_caja" => new ApiExposedProperty("id_venta_caja", false, POST, array( "int" )),
			"saldo" => new ApiExposedProperty("saldo", false, POST, array( "float" )),
			"tipo_pago" => new ApiExposedProperty("tipo_pago", false, POST, array( "string" )),
		);
	}

	protected function GenerateResponse() {		
		try{
 		$this->response = SucursalesController::VenderCaja( 
 			
			
			isset($_POST['descuento'] ) ? $_POST['descuento'] : null,
			isset($_POST['id_comprador'] ) ? $_POST['id_comprador'] : null,
			isset($_POST['impuesto'] ) ? $_POST['impuesto'] : null,
			isset($_POST['retencion'] ) ? $_POST['retencion'] : null,
			isset($_POST['subtotal'] ) ? $_POST['subtotal'] : null,
			isset($_POST['tipo_venta'] ) ? $_POST['tipo_venta'] : null,
			isset($_POST['total'] ) ? $_POST['total'] : null,
			isset($_POST['billetes_cambio'] ) ? $_POST['billetes_cambio'] : null,
			isset($_POST['billetes_pago'] ) ? $_POST['billetes_pago'] : null,
			isset($_POST['cheques'] ) ? $_POST['cheques'] : null,
			isset($_POST['detalle_orden'] ) ? $_POST['detalle_orden'] : null,
			isset($_POST['detalle_paquete'] ) ? $_POST['detalle_paquete'] : null,
			isset($_POST['detalle_producto'] ) ? $_POST['detalle_producto'] : null,
			isset($_POST['id_caja'] ) ? $_POST['id_caja'] : null,
			isset($_POST['id_sucursal'] ) ? $_POST['id_sucursal'] : null,
			isset($_POST['id_venta_caja'] ) ? $_POST['id_venta_caja'] : null,
			isset($_POST['saldo'] ) ? $_POST['saldo'] : null,
			isset($_POST['tipo_pago'] ) ? $_POST['tipo_pago'] : null
			
			);
		}catch(Exception $e){
 			//Logger::error($e);
			throw new ApiException( $this->error_dispatcher->invalidDatabaseOperation( $e->getMessage() ) );
		}
 	}
  }
  
  
  
  
  
  
