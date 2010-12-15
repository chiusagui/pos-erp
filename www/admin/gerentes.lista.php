<h1>Gerentes</h1><?php

    require_once("controller/personal.controller.php");


    



?><h2>Gerentes asignados a una sucursal</h2><?php

$gerentes = listarGerentes(true);

//render the table
$header = array(
    "id_usuario" => "ID",
    "nombre" => "Nombre",
    "RFC" => "RFC",
    "telefono" => "Telefono",
    "gerencia_sucursal_desc" => "Sucursal" );

$tabla = new Tabla( $header, $gerentes );
$tabla->addOnClick("id_usuario", "mostrarDetalles");
$tabla->render();



$gerentes = listarGerentes(false);

if(sizeof($gerentes)>0){
    ?><h2>Gerentes no asignados a ninguna sucursal</h2><?php



    //render the table
    $header = array(
        "id_usuario" => "ID",
        "nombre" => "Nombre",
        "telefono" => "Telefono",
        "RFC" => "RFC" );

    $tabla = new Tabla( $header, $gerentes );
    $tabla->addOnClick("id_usuario", "mostrarDetalles");
    $tabla->render();
}




?>
<script type="text/javascript" charset="utf-8">
	function mostrarDetalles( a ){
		window.location = "gerentes.php?action=detalles&id=" + a;
	}
</script>


