<?php

	require_once("includes/checkSession.php");
	require_once("includes/static.php");	
	require_once("../../server/config.php");	
	require_once("db/DBConnection.php");
?>
<!DOCTYPE html>
<html lang="es"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Punto de venta | Inventario</title>
	


	<link rel="stylesheet" type="text/css" href="./../getResource.php?mod=shared&type=css">
	<script type="text/javascript" src="./../getResource.php?mod=admin&type=js"></script>

    <link href="./resources/v2.css" rel="stylesheet">
    <link href="./resources/base.css" rel="stylesheet">


    <script src="./resources/core.js"></script>
    <script src="./resources/toolbar.js"></script>

  </head>
  <body>
    <div class="g-doc-800" id="g-doc">
        
    <?php include_once("includes/mainMenu.php"); ?>
	
	<div class="g-section g-tpl-160 main">
	
	<?php 
		switch( $_GET["action"] )
		{
			case "lista" : require_once("inventario.lista.php"); break;
			case "nuevo" : require_once("inventario.nuevo.php"); break;
			case "surtir" : require_once("inventario.surtir.php"); break;			
			case "detalle" : require_once("inventario.detalle.php"); break;
			case "transit" : require_once("inventario.transit.php"); break;
			default : echo "<h1>Error</h1><p>El sitio ha encontrado un error, porfavor intente de nuevo usando el menu en la parte de arriba.</p>";
		} 
	?>
    </div>
	<?php include_once("includes/footer.php"); ?>
    </div>
  
</body>
</html>
