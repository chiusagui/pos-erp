<?php

date_default_timezone_set ( "America/Mexico_City" );

define("BYPASS_INSTANCE_CHECK", false);

$_GET["_instance_"] = 123;

require_once("../../server/bootstrap.php");





class EmpresasControllerTest extends PHPUnit_Framework_TestCase {
	
	

	public function testNuevo(){
		
		SesionController::Iniciar(123, 1, true);		
		
		$direccion = Array(
			"calle"  			=> "Monte Balcanes",
	        "numero_exterior"   => "107",
	        "colonia"  			=> "Arboledas",
	        "id_ciudad"  		=> 334,
	        "codigo_postal"  	=> "38060",
	        "numero_interior"  	=> null,
	        "texto_extra"  		=> "Calle cerrada",
	        "telefono1"  		=> "4616149974",
	        "telefono2"			=> "45*451*454"
		);
		
		$id_moneda = 1;
		
		$impuestos_compra = Array();
		
		$impuestos_venta = Array();
		
		$razon_social = "Caffeina Software";

		$rfc  = "GOHA8801317";
		
		$eid = EmpresasController::Nuevo(
				$direccion, 
				$id_moneda, 
				$impuestos_compra, 
				$impuestos_venta, 
				$razon_social, 
				$rfc
			);
	}
		

}
