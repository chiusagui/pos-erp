<?php
/**
  * POST api/cliente/nuevo
  * Insertar un nuevo cliente.
  *
  * Crear un nuevo cliente. Para los campos de Fecha_alta y Fecha_ultima_modificacion se usar? la fecha actual del servidor. El campo Agente y Usuario_ultima_modificacion ser?n tomados de la sesi?n activa. Para el campo Sucursal se tomar? la sucursal activa donde se est? creando el cliente. 

Al crear un cliente se le creara un usuario para la interfaz de cliente y pueda ver sus facturas y eso, si tiene email. Al crearse se le enviara un correo electronico con el url.
  *
  *
  *
  **/

  class ApiClienteNuevo extends ApiHandler {
  

	protected function DeclareAllowedRoles(){  return BYPASS;  }
	protected function GetRequest()
	{
		$this->request = array(	
			"clasificacion_cliente" => new ApiExposedProperty("clasificacion_cliente", true, POST, array( "int" )),
			"codigo_cliente" => new ApiExposedProperty("codigo_cliente", true, POST, array( "string" )),
			"password" => new ApiExposedProperty("password", true, POST, array( "string" )),
			"razon_social" => new ApiExposedProperty("razon_social", true, POST, array( "string" )),
			"calle" => new ApiExposedProperty("calle", false, POST, array( "string" )),
			"codigo_postal" => new ApiExposedProperty("codigo_postal", false, POST, array( "string" )),
			"colonia" => new ApiExposedProperty("colonia", false, POST, array( "string" )),
			"cuenta_de_mensajeria" => new ApiExposedProperty("cuenta_de_mensajeria", false, POST, array( "string" )),
			"curp" => new ApiExposedProperty("curp", false, POST, array( "string" )),
			"denominacion_comercial" => new ApiExposedProperty("denominacion_comercial", false, POST, array( "string" )),
			"descuento" => new ApiExposedProperty("descuento", false, POST, array( "float" )),
			"direccion_web" => new ApiExposedProperty("direccion_web", false, POST, array( "string" )),
			"email" => new ApiExposedProperty("email", false, POST, array( "string" )),
			"id_ciudad" => new ApiExposedProperty("id_ciudad", false, POST, array( "int" )),
			"impuestos" => new ApiExposedProperty("impuestos", false, POST, array( "json" )),
			"limite_credito" => new ApiExposedProperty("limite_credito", false, POST, array( "float" )),
			"mensajeria" => new ApiExposedProperty("mensajeria", false, POST, array( "bool" )),
			"moneda_del_cliente" => new ApiExposedProperty("moneda_del_cliente", false, POST, array( "int" )),
			"numero_exterior" => new ApiExposedProperty("numero_exterior", false, POST, array( "string" )),
			"numero_interior" => new ApiExposedProperty("numero_interior", false, POST, array( "string" )),
			"representante_legal" => new ApiExposedProperty("representante_legal", false, POST, array( "string" )),
			"retenciones" => new ApiExposedProperty("retenciones", false, POST, array( "json" )),
			"rfc" => new ApiExposedProperty("rfc", false, POST, array( "string" )),
			"telefono1" => new ApiExposedProperty("telefono1", false, POST, array( "string" )),
			"telefono2" => new ApiExposedProperty("telefono2", false, POST, array( "string" )),
			"telefono_personal1" => new ApiExposedProperty("telefono_personal1", false, POST, array( "string" )),
			"telefono_personal2" => new ApiExposedProperty("telefono_personal2", false, POST, array( "string" )),
			"texto_extra" => new ApiExposedProperty("texto_extra", false, POST, array( "string" )),
		);
	}

	protected function GenerateResponse() {		
		try{
 		$this->response = ClientesController::Nuevo( 
 			
			
			isset($_POST['clasificacion_cliente'] ) ? $_POST['clasificacion_cliente'] : null,
			isset($_POST['codigo_cliente'] ) ? $_POST['codigo_cliente'] : null,
			isset($_POST['password'] ) ? $_POST['password'] : null,
			isset($_POST['razon_social'] ) ? $_POST['razon_social'] : null,
			isset($_POST['calle'] ) ? $_POST['calle'] : null,
			isset($_POST['codigo_postal'] ) ? $_POST['codigo_postal'] : null,
			isset($_POST['colonia'] ) ? $_POST['colonia'] : null,
			isset($_POST['cuenta_de_mensajeria'] ) ? $_POST['cuenta_de_mensajeria'] : null,
			isset($_POST['curp'] ) ? $_POST['curp'] : null,
			isset($_POST['denominacion_comercial'] ) ? $_POST['denominacion_comercial'] : null,
			isset($_POST['descuento'] ) ? $_POST['descuento'] : null,
			isset($_POST['direccion_web'] ) ? $_POST['direccion_web'] : null,
			isset($_POST['email'] ) ? $_POST['email'] : null,
			isset($_POST['id_ciudad'] ) ? $_POST['id_ciudad'] : null,
			isset($_POST['impuestos'] ) ? json_decode($_POST['impuestos']) : null,
			isset($_POST['limite_credito'] ) ? $_POST['limite_credito'] : null,
			isset($_POST['mensajeria'] ) ? $_POST['mensajeria'] : null,
			isset($_POST['moneda_del_cliente'] ) ? $_POST['moneda_del_cliente'] : null,
			isset($_POST['numero_exterior'] ) ? $_POST['numero_exterior'] : null,
			isset($_POST['numero_interior'] ) ? $_POST['numero_interior'] : null,
			isset($_POST['representante_legal'] ) ? $_POST['representante_legal'] : null,
			isset($_POST['retenciones'] ) ? json_decode($_POST['retenciones']) : null,
			isset($_POST['rfc'] ) ? $_POST['rfc'] : null,
			isset($_POST['telefono1'] ) ? $_POST['telefono1'] : null,
			isset($_POST['telefono2'] ) ? $_POST['telefono2'] : null,
			isset($_POST['telefono_personal1'] ) ? $_POST['telefono_personal1'] : null,
			isset($_POST['telefono_personal2'] ) ? $_POST['telefono_personal2'] : null,
			isset($_POST['texto_extra'] ) ? $_POST['texto_extra'] : null
			
			);
		}catch(Exception $e){
 			//Logger::error($e);
			throw new ApiException( $this->error_dispatcher->invalidDatabaseOperation( $e->getMessage() ) );
		}
 	}
  }
  
  
  
  
  
  
