<?php
/**
  * GET api/precio/paquete/editar_precio_rol
  * Edita la relacion de precio de uno o varios paquetes con un rol
  *
  * Edita la relacion de precio de uno o varios paquetes con un rol
  *
  *
  *
  **/

  class ApiPrecioPaqueteEditarPrecioRol extends ApiHandler {
  

	protected function DeclareAllowedRoles(){  return BYPASS;  }
	protected function GetRequest()
	{
		$this->request = array(	
			"id_rol" => new ApiExposedProperty("id_rol", true, GET, array( "int" )),
			"paquetes_precios_utilidad" => new ApiExposedProperty("paquetes_precios_utilidad", true, GET, array( "json" )),
		);
	}

	protected function GenerateResponse() {		
		try{
 		$this->response = PrecioController::Editar_precio_rolPaquete( 
 			
			
			isset($_GET['id_rol'] ) ? $_GET['id_rol'] : null,
			isset($_GET['paquetes_precios_utilidad'] ) ? $_GET['paquetes_precios_utilidad'] : null
			
			);
		}catch(Exception $e){
 			//Logger::error($e);
			throw new ApiException( $this->error_dispatcher->invalidDatabaseOperation( $e->getMessage() ) );
		}
 	}
  }
  
  
  
  
  
  
