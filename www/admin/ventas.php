<?php

	require_once("includes/checkSession.php");
	require_once("includes/static.php");	
	require_once("../../server/config.php");	
	require_once("db/DBConnection.php");
?>
<!DOCTYPE html>
<html lang="es"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Punto de venta | Ventas</title>
	
	<link rel="stylesheet" type="text/css" href="./../getResource.php?mod=admin&type=css">
	<script type="text/javascript" src="./../getResource.php?mod=admin&type=js"></script>

  </head>
  <body>
    <div class="g-doc-800" id="g-doc">
        
    <?php include_once("includes/mainMenu.php"); ?>

	<div class="g-section g-tpl-160 main"> 
    <?php
    if(isset($_REQUEST['success'])){

        if($_REQUEST['success'] == 'true'){
            echo "<div class='success'>" . $_REQUEST['reason'] . "</div>";
        }else{
            echo "<div class='failure'>". $_REQUEST['reason'] ."</div>";
        }
    }
    ?>
	<?php 
		switch( $_GET["action"] )
		{
			case "lista" : require_once("ventas.lista.php"); break;
			case "detalles" : require_once("ventas.detalles.php"); break;
			case "porProducto" : require_once("ventas.porProducto.php"); break;
			case "porEmpleado" : require_once("ventas.porEmpleado.php"); break;
			case "proyecciones" : require_once("ventas.proyecciones.php"); break;
			default : echo "<h1>Error</h1><p>El sitio ha encontrado un error, porfavor intente de nuevo usando el menu en la parte de arriba.</p>";
		} 
	?>

	</div>
	<?php include_once("includes/footer.php"); ?>
    </div>
  
</body>
</html>
