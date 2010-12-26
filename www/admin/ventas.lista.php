<?php

require_once("controller/ventas.controller.php");
require_once('model/cliente.dao.php');
require_once('model/sucursal.dao.php');

?>


<script src="../frameworks/jquery/jquery-1.4.2.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="../frameworks/jquery-datepicker/js/jquery-ui-1.7.1.custom.min.js"></script>
<script type="text/javascript" src="../frameworks/jquery-datepicker/js/daterangepicker.jQuery.js"></script>
<link rel="stylesheet" href="../frameworks/jquery-datepicker/css/ui.daterangepicker.css" type="text/css" /> 
<link rel="stylesheet" href="../frameworks/jquery-datepicker/css/redmond/jquery-ui-1.7.1.custom.css" type="text/css" title="ui-theme" /> 
<script src="../frameworks/uniform/jquery.uniform.js" type="text/javascript" charset="utf-8"></script> 
<link rel="stylesheet" href="../frameworks/uniform/css/uniform.default.css" type="text/css" media="screen">

<script>
    function mostrarDetallesVenta (vid){
        window.location = "ventas.php?action=detalles&id=" + vid;
    }
	$(function(){

        $("input, select").uniform();

		$('#datePicker').daterangepicker({
                arrows:true,
                onChange: function(a,b){
                    dateStart = a;
                    dateEnd = b;
                },
                earliestDate: Date.parse('-2years'),     //incio de la sucursales
                dateStart : Date.parse('today')
             }); 
	 });

    var dateStart;
    var dateEnd;
    sucursales = [];
    var clientes;
    var venta;

    function checkSucursal(sid){
        found = -1;

        for(var i =0; i < sucursales.length; i++){
            if( sucursales[i] == sid ){
                found = i;
                break;
            }
        }

        if(found == -1){
            sucursales.push( sid );
        }else{
            sucursales.splice(found, 1);
        }
        //console.log(found, sid)
    }
    
    function checkClientes(tipo)
    {
        clientes = tipo;
    }

    function checkVenta(tipo)
    {
        venta = tipo;
    }


    function reload(){

        url = "ventas.php?action=lista&start=" + dateStart + "&end=" + dateEnd ;
        url += "&sucursales=" + sucursales.join(',');
        url += "&clientes=" + clientes;
        url += "&tipoVenta=" + venta;
        window.location = url;
    }
</script>





<h1>Ventas</h1>



<h2>Busqueda avanzada</h2>
<table>
<tr>
    <td>Fecha</td><td><input style='background: white;' type="text" value="4/23/99" id="datePicker" /></td>
    <td>Clientes</td><td>
        <input type='radio' name='sclientes' onClick='clientes=this.value' value='todos' checked="checked">Todos<br>
        <input type='radio' name='sclientes' onClick='clientes=this.value' value='clientes' >Clientes registrados<br>
        <input type='radio' name='sclientes' onClick='clientes=this.value' value='caja' >Caja Comun<br>
    </td>
</tr>

<tr>
    <td>Sucursales</td><td>
	    <?php
	    $sucursales = SucursalDAO::getAll();

	    foreach( $sucursales as $suc ){
		    echo "<input type='checkbox' onclick='checkSucursal(this.value)' checked='checked' value='" . $suc->getIdSucursal() . "' >" .  $suc->getDescripcion()  . "<br>";
	    }
	    ?>
    </td>
    <td>Tipo de venta</td><td>
        <input type='radio' name='stventa' onclick='venta=this.value' value='todas' checked="checked">Todas<br>
        <input type='radio' name='stventa' onclick='venta=this.value' value='credito' >Credito<br>
        <input type='radio' name='stventa' onclick='venta=this.value' value='contado' >Contado<br>
    </td>
</tr>



<tr>
    <td></td><td><input onclick='reload()' type='button' value='Buscar'></td>
</tr>
</table>







<h2>Ultimas ventas</h2> <?php
//obtener los clientes del controller de clientes

$ventas = VentasDAO::getAll (1, 50, 'fecha', 'desc');

//render the table
$header = array(
	"id_venta"=>  "Venta",
	"id_sucursal"=>  "Sucursal",
	"id_cliente"=>  "Cliente",
	"tipo_venta"=>  "Tipo",
	"fecha"=>  "Fecha",
	"subtotal"=>  "Subtotal",
	//"iva"=>  "IVA",
	"descuento"=>  "Descuento",
	"total"=>  "Total",

	//"pagado"=>  "Pagado" 
    );




function getNombrecliente($id)
{
    if($id < 0){
         return "Caja Comun";
    }
    return ClienteDAO::getByPK( $id )->getNombre();
}




function getDescSuc($sid)
{

    return SucursalDAO::getByPK( $sid )->getDescripcion();

}

function setTipocolor($tipo)
{
        if($tipo =="credito")
            return "<b>Credito</b>";
        return "Contado";
}




$tabla = new Tabla( $header, $ventas );
$tabla->addColRender( "subtotal", "moneyFormat" ); 
$tabla->addColRender( "saldo", "moneyFormat" ); 
$tabla->addColRender( "total", "moneyFormat" ); 
$tabla->addColRender( "pagado", "moneyFormat" ); 
$tabla->addColRender( "tipo_venta", "setTipoColor" ); 
$tabla->addColRender( "id_cliente", "getNombreCliente" ); 
$tabla->addColRender( "id_sucursal", "getDescSuc" ); 
$tabla->addOnClick("id_venta", "mostrarDetallesVenta");
$tabla->addColRender( "descuento", "percentFormat" );
$tabla->render();
