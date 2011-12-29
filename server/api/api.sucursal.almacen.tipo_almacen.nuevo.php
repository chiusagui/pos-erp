<?php
/**
  * GET api/sucursal/almacen/tipo_almacen/nuevo
  * Crea un nuevo tipo de almacen
  *
  * Crea un nuevo tipo de almacen
  *
  *
  *
  **/

  class ApiSucursalAlmacenTipoAlmacenNuevo extends ApiHandler {
  

	protected function DeclareAllowedRoles(){  return BYPASS;  }
	protected function CheckAuthorization() {}
	protected function GetRequest()
	{
		$this->request = array(	
			"descripcion" => new ApiExposedProperty("descripcion", true, GET, array( "string" )),
		);
	}

	protected function GenerateResponse() {		
		try{
 		$this->response = SucursalesController::NuevoTipo_almacenAlmacen( 
 			
			
			isset($_GET['descripcion'] ) ? $_GET['descripcion'] : null
			
			);
		}catch(Exception $e){
 			//Logger::error($e);
			throw new ApiException( $this->error_dispatcher->invalidDatabaseOperation( $e->getMessage() ) );
		}
 	}
  }
  
  
  
  
  
  