<?php

	define("BYPASS_INSTANCE_CHECK", false);

	require_once("../../../server/bootstrap.php");

	$page = new GerenciaComponentPage();

	$page->addComponent( new TitleComponent( "Servicios" ) );
	$page->addComponent( new MessageComponent( "Lista de servicios" ) );
	
	$r = ServiciosController::Buscar();
	
	$tabla = new TableComponent( 
		array(
			"codigo_servicio" => "Codigo de servicio",
			"nombre_servicio" => "Nombre",
			"metodo_costeo" => "Metodo de costeo",
			"precio" => "Precio",
			"activo" => "Activo"
		),
		$r["resultados"]
	);

	$tabla->addColRender("activo", "funcion_activo");

	$tabla->addOnClick( "id_servicio", "(function(a){ window.location = 'servicios.ver.php?sid=' + a; })" );

	$page->addComponent( $tabla );

	$page->render();
