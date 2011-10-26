<?php
/**
  * GET api/sucursal/caja/cerrar
  * Hace un corte en los flujos de dinero de esta caja
  *
  * Hace un corte en los flujos de dinero de la sucursal. El Id de la sucursal se tomara de la sesion actual. La fehca se tomara del servidor.
  *
  *
  *
  **/

  class ApiSucursalCajaCerrar extends ApiHandler {
  

	protected function DeclareAllowedRoles(){  return BYPASS;  }
	protected function CheckAuthorization() {}
	protected function GetRequest()
	{
		$this->request = array(	
			"saldo_real" => new ApiExposedProperty("saldo_real", true, GET, array( "float" )),
			"billetes" => new ApiExposedProperty("billetes", true, GET, array( "json" )),
			"id_cajero" => new ApiExposedProperty("id_cajero", false, GET, array( "int" )),
		);
	}

	protected function GenerateResponse() {		
		try{
 		$this->response = SucursalesController::CerrarCaja( 
 			
			
			isset($_GET['saldo_real'] ) ? $_GET['saldo_real'] : null,
			isset($_GET['billetes'] ) ? $_GET['billetes'] : null,
			isset($_GET['id_cajero'] ) ? $_GET['id_cajero'] : null
			
			);
		}catch(Exception $e){
 			Logger::error($e);
			throw new ApiException( $this->error_dispatcher->invalidDatabaseOperation() );
		}
 	}
  }
  
  
  
  
  
  