
<h2>Abrir una sucursal</h2><?php

	require_once("model/sucursal.dao.php");
	require_once("model/grupos_usuarios.dao.php");
	require_once("controller/clientes.controller.php");


?>

<script type="text/javascript" charset="utf-8">

    function validar(){
		/*
        if(jQuery('#descripcion').val().length < 8){
            jQuery('#descripcion_helper').html("La descricpcion es muy corta." );
            return;
        }else{
            jQuery('#descripcion_helper').html( "" );
        }


        if(jQuery('#direccion').val().length < 10){
            jQuery('#direccion_helper').html("La direccion es muy corta. Sea mas especifico");
            return;
        }else{
            jQuery('#direccion_helper').html("");
        }


        if(jQuery('#telefono').val().length < 7){
            jQuery('#telefono_helper').html("El telefono es muy corto.");
            return;
        }else{
            jQuery('#telefono_helper').html("");
        }
		*/

        guardar();
        

    }

    function limpiar(){
		jQuery("#descripcion" ).val("");
		jQuery("#calle" ).val("");
		jQuery("#numero_exterior" ).val("");
		jQuery("#numero_interior" ).val("");
		jQuery("#colonia" ).val("");
		jQuery("#localidad" ).val("");
		jQuery("#municipio" ).val("");	
		jQuery("#referencia" ).val("");
		jQuery("#estado" ).val("");
		jQuery("#codigo_postal" ).val("");
		jQuery("#telefono" ).val("");
		jQuery("#rfc").val("");
		jQuery("#letras_factura").val("");
    }
    

    function guardar(){

		datos = {
			descripcion: 		jQuery("#descripcion" ).val(),
			calle: 				jQuery("#calle" ).val(),
			numero_exterior: 	jQuery("#numero_exterior" ).val(),
			numero_interior: 	jQuery("#numero_interior" ).val(),
			colonia: 			jQuery("#colonia" ).val(),
			localidad: 			jQuery("#localidad" ).val(),
			municipio: 			jQuery("#municipio" ).val(),	
			referencia: 		jQuery("#referencia" ).val(),
			estado: 			jQuery("#estado" ).val(),
			codigo_postal: 		jQuery("#codigo_postal" ).val(),
			telefono: 			jQuery("#telefono" ).val(),
			rfc: 				jQuery("#rfc").val(),
			prefijo_factura: 	jQuery("#letras_factura").val(),
			gerente : 			jQuery("#gerente").val()
        };
        
	    jQuery.ajax({
	      url: "../proxy.php",
	      data: { 
            action : 701, 
            data : jQuery.JSON.encode( datos )

           },
	      cache: false,
	      success: function(data){
		        response = jQuery.parseJSON(data);

                if(!response.success){
					jQuery('html,body').animate({scrollTop: 0 }, 1000);
                    return jQuery("#ajax_failure").html(response.reason).show();
                }
                reason = "La nueva sucursal se ha creado con exito.";
                window.location = "sucursales.php?action=lista&success=true&reason=" + reason;
	      }
	    });

    }

</script>



<?php
    $posiblesGerentes = 0;
    $html = "";
	$grp = new GruposUsuarios();
    $grp->setIdGrupo("2");

    $gerentes = GruposUsuariosDAO::search( $grp );

	foreach( $gerentes as $usuario ){

        //ya tengo el gerente
        $gerente = UsuarioDAO::getByPK( $usuario->getIdUsuario() );

        //reviar que siga en activo
        if($gerente->getActivo() == 0){
            continue;                    
        }

        //revisar que no sea gerente ya de una sucursal
        $suc = new Sucursal();
        $suc->setGerente( $gerente->getIdUsuario() );

        if( sizeof(SucursalDAO::search( $suc )) > 0 ){
            continue;
        }

        $posiblesGerentes++;
		$html .= "<option value='" . $gerente->getIdUsuario() . "' >" .  $gerente->getNombre()  . "</option>";
	}


    if($posiblesGerentes > 0 ){

        ?><form id="gerencia">
        <table border="0" cellspacing="2" cellpadding="2">
	        <tr><td>Gerente&nbsp;&nbsp;</td>
		        <td>
			        <select id="gerente"> 
	                    <?php echo $html; ?>
	                </select>
		        </td>
	        </tr>
        </table>
        </form>
        <?php

    }else{

        ?>No existe  ningun gerente disponible para abrir la sucursal, para abrir una sucursal primero haga click <a href="gerentes.php?action=nuevo">aqui</a> para crear un nuevo gerente.<?php
    
    }

?>





<?php

if($posiblesGerentes > 0 ){
    ?>
	<form id="detalles">
	<table border="0" cellspacing="5" cellpadding="5">
		<tr><td>Descripcion</td><td><input type="text" 		id="descripcion" size="40"/></td><td><div id="descripcion_helper"></div></td></tr>
		<tr><td>Calle</td><td><input type="text" 			id="calle" size="40"/></td><td><div id="direccion_helper"></div></td></tr>
		<tr><td>Numero Exterior</td><td><input type="text" 	id="numero_exterior" size="40"/></td><td><div id=""></div></td></tr>
		<tr><td>Numero Interior</td><td><input type="text" 	id="numero_interior" size="40"/></td><td><div id="direccion_helper"></div></td></tr>
		<tr><td>Colonia</td><td><input type="text" 			id="colonia" size="40"/></td><td><div id="direccion_helper"></div></td></tr>
		<tr><td>Localidad</td><td><input type="text" 		id="localidad" size="40"/></td><td><div id="direccion_helper"></div></td></tr>	
		<tr><td>Municipio</td><td><input type="text" 		id="municipio" size="40"/></td><td><div id="direccion_helper"></div></td></tr>	
		<tr><td>Referencia</td><td><input type="text" 		id="referencia" size="40"/></td><td><div id="direccion_helper"></div></td></tr>		
		<tr><td>Estado</td><td><input type="text" 			id="estado" size="40"/></td><td><div id="direccion_helper"></div></td></tr>
		<tr><td>Codigo Postal</td><td><input type="text" 	id="codigo_postal" size="40"/></td><td><div id="direccion_helper"></div></td></tr>
		
		<tr><td>Telefono</td><td><input type="text" 		id="telefono" size="40"/></td><td><div id="telefono_helper"></div></td></tr>
		<tr><td>RFC</td><td><input type="text" 				id="rfc" size="40"/></td><td><div id="rfc_helper"></div></td></tr>
		<tr><td>Prefijo Factura</td><td><input type="text" 	id="letras_factura" size="40"/></td><td><div id="letras_factura_helper"></div></td></tr>

	</table>
	</form>
	<div align="center">
		<h4><input type="button" onClick="validar()" value="Abrir esta sucursal"/></h4>
	</div>
<?php
}
