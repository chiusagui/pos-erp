<?php
/**
  * GET api/sucursal/almacen/editar
  * Edita la informacion de un almacen
  *
  * Edita la informacion de un almacen
  *
  *
  *
  **/

  class ApiSucursalAlmacenEditar extends ApiHandler {
  

	protected function DeclareAllowedRoles(){  return BYPASS;  }
	protected function CheckAuthorization() {}
	protected function GetRequest()
	{
		$this->request = array(	
			"id_almacen" => new ApiExposedProperty("id_almacen", true, GET, array( "int" )),
			"id_tipo_almacen" => new ApiExposedProperty("id_tipo_almacen", false, GET, array( "int" )),
			"nombre" => new ApiExposedProperty("nombre", false, GET, array( "string" )),
			"descripcion" => new ApiExposedProperty("descripcion", false, GET, array( "string" )),
		);
	}

	protected function GenerateResponse() {		
		try{
 		$this->response = SucursalesController::EditarAlmacen( 
 			
			
			isset($_GET['id_almacen'] ) ? $_GET['id_almacen'] : null,
			isset($_GET['id_tipo_almacen'] ) ? $_GET['id_tipo_almacen'] : null,
			isset($_GET['nombre'] ) ? $_GET['nombre'] : null,
			isset($_GET['descripcion'] ) ? $_GET['descripcion'] : null
			
			);
		}catch(Exception $e){
 			//Logger::error($e);
			throw new ApiException( $this->error_dispatcher->invalidDatabaseOperation( $e->getMessage() ) );
		}
 	}
  }
  
  
  
  
  
  