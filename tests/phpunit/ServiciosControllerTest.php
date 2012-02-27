<?php


date_default_timezone_set ( "America/Mexico_City" );

if(!defined("BYPASS_INSTANCE_CHECK"))
	define("BYPASS_INSTANCE_CHECK", false);

$_GET["_instance_"] = 71;

require_once("../../server/bootstrap.php");





class ServiciosControllerTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp(){
		Logger::log("-----------------------------");
		$r = SesionController::Iniciar(123, 1, true);
	}
	
	
	
	public function testBuscarEliminarYNuevo(){
		
		//buscar el servicio que se llama prestamo

		$servs = ServiciosController::Buscar();
		
		$this->assertEquals( $servs["numero_de_resultados"], sizeof($servs["resultados"]) );
		
		for ($i=0; $i < $servs["numero_de_resultados"]; $i++) { 
			$s = $servs["resultados"][$i]->asArray();
			if(($s["nombre_servicio"] == "prestamo") && ($s["activo"] == 1) ){
				ServiciosController::Eliminar( $s["id_servicio"] );
			}
		}
		
		$s = ServiciosController::Nuevo(
			"CA01", 
			false, 
			0, 
			"precio", 
			"prestamo",
			true, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			0
		);
		
		$servs = ServiciosController::Buscar();
		
		$this->assertGreaterThan(0, $servs["numero_de_resultados"]);
	}



	/**
     * @expectedException BusinessLogicException
     */
	public function testInsertarNombreRepetido(){
		$s = ServiciosController::Nuevo(
			"CA01", 
			false, 
			0, 
			"precio", 
			"prestamo",
			true, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			0
		);
	}
	
	public function testBuscarSoloActivos(){
		$servs_todos = ServiciosController::Buscar();
		$servs_activos = ServiciosController::Buscar(true);
		
		$this->assertGreaterThan($servs_activos["numero_de_resultados"] , $servs_todos["numero_de_resultados"] );
	}
	
	public function testBuscarEliminarYNuevaCategoria(){
		//buscar la categoria
		
		//eliminarla si existe
		
		//insertarla de nuevo
		
		//buscar la categoria
	}
	

	

	public function testNuevaOrden(){
		$servs = ServiciosController::Buscar();
		
		$this->assertEquals( $servs["numero_de_resultados"], sizeof($servs["resultados"]) );
		/*
		for ($i=0; $i < $servs["numero_de_resultados"]; $i++) { 
			$s = $servs["resultados"][$i]->asArray();
			if($s["nombre_servicio"] == "F9c4cf2db944589ec4c13ed61fbaeb33"){
				Logger::testerLog("el servicio F9c4cf2db944589ec4c13ed61fbaeb33 ya existe, desactivando...");
				try{
					ServiciosController::Eliminar( $s["id_servicio"] );
					// si lo pude eliminar, voy a crearlo de nuevo
					
					
				}catch(Exception $e){
					
				}
			}
		}*/
		

		Logger::testerLog("Creando servicio: F9c4cf2db94458" . time());
		
		$s = ServiciosController::Nuevo(
			"F9c4cf2db94458" . time(), 
			false, 
			0, 
			"precio", 
			"F9c4cf2db94458" . time(),
			true, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			0
		);
	
		
		Logger::testerLog("Creando cliente:  c9c4cf2db94458" . time());
		$c = ClientesController::nuevo(		"c9c4cf2db94458" . time());


		
		Logger::testerLog("Nueva orde de servicio (" . $c["id_cliente"] .", ". $s["id_servicio"] ." )");
		
		$o = ServiciosController::NuevaOrden(
				$c["id_cliente"], 
				$s["id_servicio"]  );
			
	    $this->assertInternalType("int", $o["id_orden"]);
		$this->assertInternalType("int", $o["id_venta"]);

		define("_pos_phpunit_servicios_id_cliente", $c["id_cliente"]);	
		define("_pos_phpunit_servicios_id_servicio", $s["id_servicio"]);
		
		
		//ok ya que se hizo el servicio, ver que se halla creado
		//una venta a credito a este cliente
		
		$lista_de_ventas = VentasController::Lista();
		
		//buscar la venta para el cliente `$c["id_cliente"]`
		
		$found = false;
		
		for ($i=0; $i < $lista_de_ventas["numero_de_resultados"]; $i++) { 
			if($lista_de_ventas["resultados"][$i]["cliente"]["id_cliente"] == $c["id_cliente"]){
				$found = true;
			}
		}
		
		$this->assertTrue($found);
		
		//hacerle un abono
		CargosYAbonosController::NuevoAbono( 
			$c["id_cliente"], 
			500, 
			"efectivo",
			null, 
			null, 
			null, 
			null, 
			$o["id_venta"] );
	}
	
	
	/**
     * @expectedException BusinessLogicException
     */
	public function testElimnarServicioConOrdenes(){
		//no se debe poder eliminar un id_servicio ya que hay una orden activa en ese servicio
        ServiciosController::Eliminar(_pos_phpunit_servicios_id_servicio);

		
	}
	
	
	
	public function testListaYNuevoSeguimiento(){
		
		
		//buscar la ultima orden de servicio
		$ordns = ServiciosController::ListaOrden();
		
		$this->assertInternalType("int", $ordns["numero_de_resultados"]);
		$orden_de_servicio_id = null;
		
		for ($i=0; $i < $ordns["numero_de_resultados"]; $i++) { 
			
			$o = $ordns["resultados"][$i]->asArray();
			
			//Logger::log($o["id_usuario_venta"] ."=======". $cliente["id_usuario"]);
			
			if($o["id_usuario_venta"] == _pos_phpunit_servicios_id_cliente){

				$orden_de_servicio_id = $o["id_orden_de_servicio"];
			}
		}
		define("_pos_phpunit_servicios_orden_de_servicio" , $o["id_orden_de_servicio"]);
		
		$this->assertFalse( is_null($orden_de_servicio_id) );
		
		ServiciosController::SeguimientoOrden($orden_de_servicio_id, null, null, "nota para la orden");
		
		
	}
	
	
	
	public function testBuscarSeguimientosDeOrden(){
		$lista = ServiciosController::ListaOrden(  );
		
		$this->assertInternalType('int', $lista["numero_de_resultados"]);
		
		//debe haber por lo menos un resultado
		$this->assertGreaterThan( 0, $lista["numero_de_resultados"] );

		$old = $lista["numero_de_resultados"];

		//insertar un seguimiento...
		ServiciosController::SeguimientoOrden(_pos_phpunit_servicios_orden_de_servicio, null, null, "nota para la orden 2");
		
		
		//$lista = ServiciosController::ListaOrden(  );
		
		//$this->assertEquals( $lista["numero_de_resultados"], $old + 1);
	}
	
	

	
}