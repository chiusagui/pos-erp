<?php
/**
  * GET api/ventas/cancelar
  * Cancelar una venta
  *
  * Metodo que cancela una venta
  *
  *
  *
  **/

  class ApiVentasCancelar extends ApiHandler {
  

	protected function DeclareAllowedRoles(){  return BYPASS;  }
	protected function CheckAuthorization() {}
	protected function GetRequest()
	{
		$this->request = array(	
			"id_venta" => new ApiExposedProperty("id_venta", true, GET, array( "string" )),
		);
	}

	protected function GenerateResponse() {		
		try{
 		$this->response = VentasController::Cancelar( 
 			
			
			isset($_GET['id_venta'] ) ? $_GET['id_venta'] : null
			
			);
		}catch(Exception $e){
 			Logger::error($e);
			throw new ApiException( $this->error_dispatcher->invalidDatabaseOperation() );
		}
 	}
  }
  
  
  
  
  
  