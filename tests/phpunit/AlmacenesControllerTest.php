<?php

date_default_timezone_set ( "America/Mexico_City" );

if(!defined("BYPASS_INSTANCE_CHECK"))
	define("BYPASS_INSTANCE_CHECK", false);

$_GET["_instance_"] = 71;

require_once("../../server/bootstrap.php");
require_once("Utils.php");


class AlmacenControllerTest extends PHPUnit_Framework_TestCase {

	

	protected function setUp(){
		Logger::log("-----------------------------");
		//$r = SesionController::Iniciar(123, 1, true);

	}

    /**
     * Nuevo Tipo de Almacen
     */
	public function testTipoNuevo(){

		$a = AlmacenesController::NuevoTipo("Nuevo_Tipo_Almacen_" . time());	
		$this->assertInternalType("int", $a["id_tipo_almacen"]);

	}

    /**
     * Editar Tipo de Almacen
     */
	public function testTipoEditar(){

        $target = "descripcion_editada_" . time();

		$a = AlmacenesController::NuevoTipo("Editar_Tipo_Almacen" . time());	
		$this->assertInternalType("int", $a["id_tipo_almacen"]);

        AlmacenesController::EditarTipo(
            $id_tipo_almacen = $a["id_tipo_almacen"], 
		    $descripcion = $target
        );

        $tipo_almacen = TipoAlmacenDAO::getByPK( $a["id_tipo_almacen"] );       

        $this->assertEquals($target, $tipo_almacen->getDescripcion(), "Error al editar la descripcion del tipo de almacen");

	}

    /**
     * Desactivar Tipo de Almacen
     */
	public function testTipoDesactivar(){        

		$a = AlmacenesController::NuevoTipo("Desactivar_Tipo_Almacen" . time());

        AlmacenesController::DesactivarTipo(
            $id_tipo_almacen = $a["id_tipo_almacen"]
        );

        $tipo_almacen = TipoAlmacenDAO::getByPK( $a["id_tipo_almacen"] );         
        
        $this->assertEquals(0, $tipo_almacen->getActivo(), "Error al desactivar el tipo de almacen");

	}

    /**
     * Buscar Tipo de Almacen
     */
	public function testTipoBuscar(){

        //creamos una sucursal para fines del experimento

        $a = AlmacenesController::NuevoTipo("Buscar_Tipo_Almacen" . time());

        
        //realizamos una busqueda general y verificamso que contenga los parametros de respuesta
        $busqueda = AlmacenesController::Buscar();

        $this->assertArrayHasKey('resultados', $busqueda);
        $this->assertArrayHasKey('numero_de_resultados', $busqueda);

        $this->assertInternalType('int', $busqueda['numero_de_resultados']);
        $this->assertInternalType('array', $busqueda['resultados']);

        $this->assertGreaterThanOrEqual(0, $busqueda['numero_de_resultados']);

        //probamos la busqueda por activo, al menos debe de haber una, ya que cuando se cree esta sucursal estara activa  
        $busqueda = AlmacenesController::Buscar();
        $this->assertGreaterThanOrEqual(1, $busqueda["numero_de_resultados"]);

        //probamos busqueda por query
        $busqueda = AlmacenesController::Buscar($query = "Buscar");
        $this->assertGreaterThanOrEqual(0, $busqueda["numero_de_resultados"]);

	}

    /**
     * Nuevo Almacen
     */
    public function testNuevoAlmacen(){
        
        $tipo_almacen = AlmacenesController::NuevoTipo("Nuevo_Tipo_Almacen__" . time());	

        $almacen = AlmacenesController::Nuevo(
		    $id_empresa = 1, 
		    $id_sucursal = 1, 
		    $id_tipo_almacen = $tipo_almacen["id_tipo_almacen"], 
		    $nombre = "Almacen_" . time(), 
		    $descripcion = "Almacen de prueba " . time()
	    );
    
    }

    /**
     * Editar Almacen
     */
    public function testEditarAlmacen(){
        
        $tipo_almacen = AlmacenesController::NuevoTipo("Nuevo_Tipo_Almacen___" . time());	

        $almacen = AlmacenesController::Nuevo(
		    $id_empresa = 1, 
		    $id_sucursal = 1, 
		    $id_tipo_almacen = $tipo_almacen["id_tipo_almacen"], 
		    $nombre = "Almacen_" . time(), 
		    $descripcion = "Almacen de prueba_ " . time()
	    );
    
    }



//--------------------------------------------------


	/**
	*
	*
	*	Almacenes
	*
	**/

	
	//Imprime la lista de tipos de almacen
	/*public function testTipoBuscarYDesactivar(){

		$r = AlmacenesController::BuscarTipo();

		$this->assertInternalType("int", $r["numero_de_resultados"]);

		$this->assertEquals( $r["numero_de_resultados"], count($r["resultados"]) );

		if($r["numero_de_resultados"] == 0){
			return;
		}


		foreach ($r["resultados"] as $tipo) {
			$tipo = $tipo->asArray();
			if($tipo["descripcion"] == "1dee80c7d5ab2c1c90aa8d2f7dd47256"){
				//ya existe este tipo para testing, hay que desactivarlo

				Logger::testerLog("Ya encontre el repetido, procedo a desactivar");
				$d = AlmacenesController::DesactivarTipo( $tipo["id_tipo_almacen"] );
			}
		}

		//volvamos a buscar y esperemos que ya no exista
		$r = AlmacenesController::BuscarTipo();

		$found = false;

		foreach ($r["resultados"] as $tipo) {
			$tipo = $tipo->asArray();
			if($tipo["descripcion"] == "1dee80c7d5ab2c1c90aa8d2f7dd47256"){
				//ya existe este tipo para testing, hay que desactivarlo
				$found = true;
			}
		}

		$this->assertFalse($found);
	}*/




	



	/**
     * @expectedException BusinessLogicException
     */
	/*public function testTipoNuevoRepetido(){
		$a = AlmacenesController::NuevoTipo("1dee80c7d5ab2c1c90aa8d2f7dd47256");	
	}


	public function testBuscar(){
		
	}

	public function testDesactivar(){
		
	}

	public function testEditar(){
		
	}*/


	/**
	*
	*
	*	Lotes
	*
	**/
	/*public function testLoteNuevo(){
		//AlamacenController::NuevoLote(  );
	}

	public function testLoteEntrada(){
		
	}



	public function testLoteSalida(){
		
	}

	public function testLoteTraspasoBuscar(){
		
	}

	public function testLoteTraspasoCancelar(){
		
	}

	public function testLoteTraspasoEditar(){
		
	}

	public function testLoteTraspasoEnviar(){
		
	}

	public function testLoteTraspasoProgramar(){
		
	}

	public function testLoteTraspasoRecibir(){
		
	}

	public function testNuevo(){
		
	}*/


	/*public function testTipoBuscar(){}
	public function testTipoDesactivar(){}
	public function testTipoEditar(){}
	public function testTipoNuevo(){}*/
		
}
