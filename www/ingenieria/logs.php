<?php
require_once("../../server/bootstrap.php");	
require_once("ingenieria/includes/checkSession.php");
require_once("admin/includes/static.php");


 	if(isset($_GET["action"]) && is_file("../../server/ingenieria/logs." . $_GET["action"] . ".php") && ( $_GET['action'] == "descargar" )){
		require_once("ingenieria/logs." . $_GET["action"] . ".php");
		die();
	}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

	
<head>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<title>POS | Configuracion</title>
	<script src="http://api.caffeina.mx/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>

	<script>
		$.noConflict();
	</script>
	
	<script type="text/javascript" charset="utf-8" src="http://api.caffeina.mx/prototype/prototype.js"></script>		

	<script src="http://api.caffeina.mx/uniform/jquery.uniform.min.js" type="text/javascript" charset="utf-8"></script> 
	<link rel="stylesheet" href="http://api.caffeina.mx/uniform/css/uniform.default.css" type="text/css" media="screen">
	<script type="text/javascript" charset="utf-8">jQuery(function(){jQuery("input, select").uniform();});</script>
		
	<link rel="stylesheet" type="text/css" href="./../getResource.php?mod=admin&type=css">
	<script type="text/javascript" src="./../getResource.php?mod=admin&type=js"></script>

</head>


<body class="sub">
  <div id="wrapper">

    <div id="header" class="control" >
      
      <div id="top-bar">
        
        <?php include_once("ingenieria/includes/mainMenu.php"); ?>
            
      </div> 
      <!-- /top-bar -->

      <div id="header-main">
		<h1 id="MAIN_TITLE">Logs</h1> 
      </div>
    </div>
    
    <div id="content">
	<?php
    if(isset($_REQUEST['success'])){

        if($_REQUEST['success'] == 'true'){
            echo "<div class='success'>" . $_REQUEST['reason'] . "</div>";
        }else{
            echo "<div class='failure'>". $_REQUEST['reason'] ."</div>";
        }
    }
    ?>

	<div id="ajax_failure" class="failure" style="display: none;"></div>
	<?php
	 	if(isset($_GET["action"]) && is_file("../../server/ingenieria/logs." . $_GET["action"] . ".php")){
    		require_once("ingenieria/logs." . $_GET["action"] . ".php");
		}else{
    		echo "<h1>Error</h1><p>El sitio ha encontrado un error, porfavor intente de nuevo usando el menu en la parte de arriba.</p>";
    		echo '<script>document.getElementById("MAIN_TITLE").innerHTML = "Error";</script>';
		}
	?> 
	
    <?php include_once("admin/includes/footerInge.php"); ?>
    </div> 
    <!-- /content -->
    
    
  </div> 
  <!-- /wrapper -->

</body></HTML>