<?php 



		define("BYPASS_INSTANCE_CHECK", false);

		require_once("../../../../server/bootstrap.php");

		$page = new GerenciaComponentPage();
		
		$page->addComponent( new TitleComponent( "Empresas" ) );

		$tabla = new TableComponent( 
			array(
				"razon_social"=> "Razon Social",
				"rfc"=> "RFC",
				"representante_legal"=> "Representante Legal",
				"fecha_alta" => "Fecha Alta",
				"activo"=> "Activa"
			),
			EmpresasController::Lista()
		);
		
		$tabla->addOnClick( "id_empresa", "(function(a){window.location = '';})", false, true );
		
		$page->addComponent( $tabla );
		
		$page->render();
