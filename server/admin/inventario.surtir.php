<?php
require_once("model/sucursal.dao.php");
require_once('model/autorizacion.dao.php');
require_once("controller/clientes.controller.php");
require_once("controller/sucursales.controller.php");
require_once("controller/inventario.controller.php");

$productos = InventarioDAO::getAll();

$iMaestro = listarInventarioMaestro(150, POS_SOLO_ACTIVOS);

if (isset($_REQUEST['aut'])) {
    $autorizacion = AutorizacionDAO::getByPK($_REQUEST['aut']);
    $autorizacionDetalles = json_decode($autorizacion->getParametros());
}

$foo = new Sucursal();
$foo->setActivo(1);
$foo->setIdSucursal(1);

$bar = new Sucursal();
$bar->setIdSucursal(99);

$sucursales = SucursalDAO::byRange($foo, $bar);
?>


<style>
    #ComposicionTabla input{
        width: 65px;
    }
</style>
<script type="text/javascript" charset="utf-8">

    var DEBUG = true;


    function toSmallUnit(unit){
        switch(unit){
            case "kilogramo" : return "Kgs";
            case "pieza" : return "Pzs";        
            case "unidad" : return "Uds";               
        }
    
    }


    function roundNumber(num) {
        var dec = 2;
        var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
        return result;
    }

    function error(title, msg, el){

	
        Ext.MessageBox.show({
            title: title,
            msg: msg,
            animEl: el,
            buttons: Ext.MessageBox.OK
        });
        /*
    var html = '<h1><img src="../media/icons/warning_32.png">&nbsp;' + title + '</h1>';
    html += msg;
    html += "<div align='center'><input type='button' value='Aceptar' onclick='jQuery(document).trigger(\"close.facebox\");'></div>";
    jQuery.facebox( html );
         */
    }




    /**
     * Arreglo con un objeto por cada sucursal
     **/
    var sucursales = [],

    /**
     * Sucursal que se esta surtiendo en este momento
     **/
    currentSuc = null,
    
    /**
     * El carrito final de productos que voy a surtir
     **/
    carrito = [],
    
    /**
     * Un objeto de la clase inventario
     **/
    inventario,
    
    tablaInventario,
    
    composicionTabla,
    
    /**
     * Un arreglo que contiene toda la informacion de las composiciones
     **/ 
    composiciones = [];




    /**
     *
     * Main
     *
     **/
    jQuery(document).ready(function(){

        if(DEBUG){
            console.log("Iniciando....");
        }

        //construir el objeto de inventario
        inventario = new InventarioMaestro();
    

    
        tablaInventario = new InventarioMaestroTabla({ 
            inventario : inventario, 
            renderTo : "InventarioMaestroTabla"
        });
    
    
    
        //seleccionar sucursal si es que se envio un id de sucursal
<?php
if (isset($_REQUEST['sid'])) {
    echo "seleccionarSucursal();";
}
?>

    
        jQuery("#MAIN_TITLE").html("Surtir sucursal");

        if(DEBUG){
            //seleccionDeProd(1 );
        }
    });

    function round( n ){
        return  Math.round(parseFloat(n)*Math.pow(10,4))/Math.pow(10,4); 
    }

    function tr(s, o){
        if(o){
            return "<tr "+o+">"+s+"</tr>";  
        }else{
            return "<tr >"+s+"</tr>";   
        }
    }

    function td(s, o){
        if(o){
            return "<td "+o+">"+s+"</td>";
        }else{
            return "<td >"+s+"</td>";
        }
    }

    function div(s, o){
        if(o){
            return "<div "+o+">"+s+"</div>";
        }else{
            return "<div >"+s+"</div>";
        }

    
    }

    /**
     * Clase InventarioMaestro 
     *
     **/
    InventarioMaestro = function( ){
    
        var estructura = [],
        z = 0;

        //revisar si existe este producto en el inventario maestro
        function existeProducto( producto ){
            //console.log( "InventarioMaestro.existeProducto("+ producto +")" );
            for( z = 0; z < estructura.length; z++ ){
                if( estructura[z].compare(producto) ){
                    return true;
                }
            }
            return false;
        }

        //agregar un producto al inventario maestro
        function agregarProducto( producto ){            
            //console.log( "InventarioMaestro.agregarProducto("+ producto +")" );
		
            if( existeProducto(producto) ){
                throw ("Este producto ya existe en el inventario maestro");
            }else{
	
                estructura.push(producto);
            }
        }

        this.getProductos = function(){
            //console.log( "InventarioMaestro.getProductos()" );
            return estructura;
        };

        this.getProducto = function (id_compra, id_producto){
            //console.log( "InventarioMaestro.getProducto("+id_compra+","+id_producto+")" );
		
            var z;
            for( z = 0; z < estructura.length; z++ ){
                if(estructura[z].id_compra_proveedor === id_compra && 
                    estructura[z].id_producto === id_producto){
                    return estructura[z];
                }
            }
            return null;
        };

        this.getProductoDesc = function(id_producto){
            var z;
            for( z = 0; z < estructura.length; z++ ){

                if( estructura[z].id_producto === id_producto){
                    return {
                        id_producto : estructura[z].id_producto,
                        descripcion : estructura[z].producto_desc,
                        escala      : estructura[z].escala,
                        agrupacion	: estructura[z].agrupacion,
                        agrupacionTam: estructura[z].agrupacionTam
                    };
                }
            }
            //no encontre el producto porque no esta en el inventario 
            //maestro, y como solo quiero la descripcion, lo sacare
            //de la lista de productos
            var other_prods = [];

<?php
$i = 0;
foreach ($productos as $prod) {
    echo " other_prods[" . $i . "] = " . json_encode($prod->asArray()) . ";\n";
    $i++;
}
?>
		
            for (var j = other_prods.length - 1; j >= 0; j--){
                if(other_prods[j].id_producto == id_producto){
                    console.log("found !", other_prods[j].descripcion)
                    return {
                        id_producto : id_producto,
                        descripcion : other_prods[j].descripcion,
                        escala      : other_prods[j].escala
                    };
                }
            };

		
		
            throw("No encontre el producto " + id_producto);
        };

        this.descontarProducto = function( prod ){

            var p = this.getProducto( prod.id_compra, prod.id_producto );

            p.existencias = p.existencias - prod.cantidad;
        
            if(prod.procesada){
                p.existencias_procesadas = p.existencias_procesadas - prod.cantidad;
            }
        
            //console.log("ya cambie el inventario maestro");
        
            /*
        id_compra   : producto.id_compra_proveedor,
        id_producto : producto.id_producto, 
        cantidad    : 0,
        desc        : producto.producto_desc,
        procesada   : false,
        precio      : producto.precio_por_kg,
        descuento   : 0*/

        };

        this.recontarProducto = function ( prod ){
            var p = this.getProducto( prod.id_compra, prod.id_producto );

            p.existencias = p.existencias + prod.cantidad;
        
            if(prod.procesada){
                p.existencias_procesadas = p.existencias_procesadas + prod.cantidad;
            }
        };

        /**
         * Constructor
         *
         *
         **/
        if(DEBUG){
            console.log("Construyendo inventario maestro.");
        }
        var foo;
<?php
foreach ($iMaestro as $i) {
    echo "  foo = new Producto (" . json_encode($i) . ") ;";
    echo "  agregarProducto( foo );";
}
?>
    


    };




    //esto ya esta definido en admin.js
    /**
     * Clase Producto
     *
     **/

    Producto = function( json_rep ){

        //compara si este producto es igual a *producto*
        this.compare = function ( producto ){
            return this.id_compra_proveedor === producto.id_compra_proveedor && 
                this.id_producto === producto.id_producto;
        };
    
    
        //constructor a partir de un json
        this.id_compra_proveedor    = parseInt( json_rep.id_compra_proveedor, 10 ); 
        this.id_producto            = parseInt( json_rep.id_producto, 10 ); 
        this.peso_origen            = parseFloat( json_rep.peso_origen, 10 );
        this.id_proveedor           = parseInt( json_rep.id_proveedor, 10 );
        this.fecha                  = json_rep.fecha;
        this.fecha_origen           = json_rep.fecha_origen;
        this.folio                  = json_rep.folio;   
        this.numero_de_viaje        = json_rep.numero_de_viaje;
        this.peso_recibido          = parseFloat( json_rep.peso_recibido );
        this.arpillas               = parseFloat( json_rep.arpillas );
        this.peso_por_arpilla       = parseFloat( json_rep.peso_por_arpilla );
        this.productor              = json_rep.productor;
        this.calidad                = json_rep.calidad;           
        this.merma_por_arpilla      = parseFloat( json_rep.merma_por_arpilla );
        this.total_origen           = parseFloat( json_rep.total_origen );
        this.existencias            = parseFloat( json_rep.existencias );
        this.existencias_procesadas = parseFloat( json_rep.existencias_procesadas );
        this.sitio_descarga         = json_rep.sitio_descarga;
        this.variedad               = json_rep.variedad;  
        this.kg                     = json_rep.kg;        
        this.precio_por_kg          = parseFloat(json_rep.precio_por_kg);
        this.producto_desc          = json_rep.producto_desc;         
        this.producto_tratamiento   = json_rep.producto_tratamiento;
        this.escala                 = json_rep.medida;
        this.sitio_descarga_desc    = json_rep.sitio_descarga_desc;
        this.costo_flete            = parseFloat( json_rep.costo_flete );
        this.agrupacion				= json_rep.agrupacion;
        this.agrupacionTam			= json_rep.agrupacionTam;
        this.precio_por_agrupacion	= json_rep.precio_por_agrupacion == "1";
    };





    /**
     * Clase para dibujar el inventario maestro
     *
     **/
    InventarioMaestroTabla = function( config ) {
    
        /**
         * 
         **/
        var inventario,

        /**
         * id del elemento donde se dibujara esta tabla
         **/
        renderTo;
    
    


        function render(){ 

        }

        function __init(config){
            if( !config.inventario instanceof InventarioMaestro ){
                throw ("Configuracion contiene algo que no es un inventario maestro");
            }
        
            inventario = config.inventario;
        
            renderTo = config.renderTo;
        
            render();
        }

        this.tomarProducto = function (id_compra, id_producto, cantidadATomar, procesadas ){
            console.log( "InventarioMaestroTabla.tomarProducto(comra="+id_compra+", producto="+id_producto+", cantidad="+cantidadATomar+", procesada="+procesadas+")" );
		
            //el producto en el inventario
            var producto = inventario.getProducto(id_compra, id_producto);

            //buscar la columna a editar
            var gridRow = MasterGrid.getStore().findBy(function(r){
                return (r.get("id_compra_proveedor") == id_compra && r.get("id_producto") == id_producto);
            }, this);
        
            if(producto.agrupacion){
			
            }

            if(procesadas){
                //las tomare procesadas
                if(producto.agrupacion){
                    MasterGrid.getStore().getAt( gridRow ).set("existencias_procesadas"	, producto.existencias_procesadas - (cantidadATomar * producto.agrupacionTam));
                }else{
                    MasterGrid.getStore().getAt( gridRow ).set("existencias_procesadas"	, producto.existencias_procesadas - cantidadATomar);
                }
			
                MasterGrid.getStore().getAt( gridRow ).set("existencias_procesadas"	, producto.existencias_procesadas - cantidadATomar);			
                MasterGrid.getStore().getAt( gridRow ).set("existencias"			, producto.existencias);			
			
            }else{
                //las tomare originales
                if(producto.agrupacion){
                    MasterGrid.getStore().getAt( gridRow ).set("existencias"			, producto.existencias - (cantidadATomar * producto.agrupacionTam));				
                }else{
                    MasterGrid.getStore().getAt( gridRow ).set("existencias"			, producto.existencias - cantidadATomar);				
                }
                MasterGrid.getStore().getAt( gridRow ).set("existencias"			, producto.existencias - cantidadATomar);	
                MasterGrid.getStore().getAt( gridRow ).set("existencias_procesadas"	, producto.existencias_procesadas);
			
            }
        };
    
        this.regresarProducto = function(prod){
            console.log("InventarioMaestroTabla.regresarProducto("+prod+")")

            this.tomarProducto( prod.id_compra, prod.id_producto, prod.cantidad * -1, prod.procesadas );
        };


        this.highlight = function (id_prod){
            if(DEBUG){
                console.log("haciendo highlight a " + id_prod);
            }
            //jQuery("#InventarioMaestroTabla tr").css("color", "rgb(68, 68, 68)");		
		
            //jQuery(".im_pid_" + id_prod).css("color", "#3F8CE9");
        };

    
        __init(config);
    
    };




    /**
     * Clase de Composicion Tabla
     *
     **/
    ComposicionTabla = function( config ){
        var renderTo,
    
        composicion = [],
        
        id_producto;
    
        function render(){
            if(DEBUG){
                console.log("Rendereando composicionTabla !");
            }
            var html = '';
            html += '<table style="width:100%">';
            html += '<tr id="ASurtirTablaHeader">';
            html += td("");
            html += td("Remision");
            html += td("Producto");
            html += td("Cantidad");
            html += td("Procesado");                        
            html += td("Precio");       
            html += td("Descuento");        
            html += td("Importe");      
            html += '</tr>';
            html += '</table>';     
            jQuery("#" + renderTo).html( html );
        }
    
        function __init( config){
            renderTo = config.renderTo;
            id_producto = config.id_producto;
            tablaInventario.highlight(id_producto);
            render();
        }
    
        /**
         * Recalcular totales.
         *
         * Recalcula los totales de un row en la tabla de composiciones. Puede hacer el recalculo basado
         * en un campo y en el nuevo valor. Los posibles campos a cambiar son:
         * proc, cantidad, precio y descuento. 
         *
         *			
         * Para ubicar que row hay que recalcular, se proporciona el id de la compra y el id del producto
         * ya que estos valores son unicos para cada row.
         **/
        this.doMath = function( id_compra, id_producto, campo, valor ){
        
            console.group("Doing some math !");
        
            //buscar este producto en el inventario
            var prod = inventario.getProducto( id_compra, id_producto ),
            i,
            comp;
        
            //buscar este producto en el arreglo de composicion
            for (i = composicion.length - 1; i >= 0; i--){
                if( composicion[i].id_compra === id_compra && composicion[i].id_producto === id_producto ){
                    comp = composicion[i];                                                                       
                    break;
                }
            }

            if(valor.length === 0){
                valor = 0;
            }

            console.log(" Producto : " + prod.producto_desc , prod);

            //depende del campo que voy a cambiar, hacer la logica del negocio
            switch( campo ){
                case "proc" : 
                    comp.procesada = (valor == "on") ;
                    console.log( "cambiando procesado a .." , comp.procesada);
                    console.log("la cantidad que yo tenia es de " + comp.cantidad );
                    this.doMath( id_compra, id_producto, "cantidad", comp.cantidad );
                    break;
				
                case "cantidad" :

                    var qty = 0;
                    console.log( "Cambiando cantidad...", valor );
							
                    if( !(prod.agrupacion && prod.precio_por_agrupacion)){
                        //no hay agrupacion, tomar la cantidad tal cual del inventario maestro
                        console.log("No hay agrupacion, voy a tomar " + valor + " " + prod.escala);
					
                        comp.cantidad = parseFloat(valor) ;
                        qty = comp.cantidad;

					
                    }else{
                        //si hay agrupacion
                        console.log("Si hay agrupacion, voy a tomar " + valor + " " + prod.agrupacion);
					
                        comp.cantidad = parseFloat(valor);
                        qty = comp.cantidad;
					
                        //ok, es agrupada, ahora, a saber si tiene proceso o si no tiene proceso
                        if(prod.producto_tratamiento === null){
                            //no hay tratamiento !
                            //la agrupacion se mide entonces con lo que hay en la base de datos
						
                            qty *= prod.agrupacionTam;
                            console.log("No se procesa, entonces voy a tomar " + qty + " " + prod.escala );
                        }else{
                            //si hay tratamiento, ver si esta procesada o no
                            if(comp.procesada){
                                console.log("Esta procesada, el tamano de agrupacion es de " + prod.agrupacionTam );
                                qty *= parseFloat(prod.agrupacionTam);
                            }else{
                                console.log("Es original, el promedio es de " + prod.peso_por_arpilla );	
                                qty *= prod.peso_por_arpilla;
                            }						
                        }


                    }
				
				
                    tablaInventario.tomarProducto( id_compra, id_producto, qty, comp.procesada );

                    break;
            
           
                case "precio" :
                    comp.precio = parseFloat( valor );
                    break;
            
                case "descuento":
				
                    comp.descuento = parseFloat(valor);

                    break;
            }


		
            var obj = {
                peso_real : 0,
                peso_a_cobrar: 0,
                importe_por_unidad : 0,
                importe_total : 0,
                escala : null
            };
        
            console.log("La composiciones actual" , composicion );


            //recorrer los items en la composicion para calcular totales, y de paso
            //calcular el importe de cada producto e insertarlo en sus respectivas 
            //cajas !
            for (i = composicion.length - 1; i >= 0; i--){


                /*
                                Asi es una composicion i-esima !
                                {
                                        agrupacion	: null
                                        cantidad	: 1
                                        desc		: "PAPA DORADA"
                                        descuento	: 0
                                        escala		: "pieza"
                                        id_compra	: 43
                                        id_producto	: 14
                                        peso_a_cobrar: 1
                                        peso_real	: 1
                                        precio		: 20
                                        precio_original: 20
                                        procesada	: false
                                }
                 */
                var prod = inventario.getProducto( 
                composicion[i].id_compra,
                composicion[i].id_producto
            );

                console.group("Composicion "+ i + " : " + composicion[i].desc, composicion[i], prod);
			
                if(obj.escala === null){
                    obj.escala = prod.escala;
                }else{
                    if(obj.escala != prod.escala){
                        obj.escala = "Diferentes escalas !";
                    }
                }        
        
                //vamos a ver si tiene una agrupacion
                if(prod.agrupacion && prod.precio_por_agrupacion){
                    //si hay agrupacion !
                    //entonces la cantidad puesta en composicion[].cantidad es
                    //la cantidad de agrupaciones, para el total en escala hay 
                    //que multiplicar eso por escalTam
                    console.log("Si tiene agrupacion !");			                        
                        
                    if(prod.producto_tratamiento === null){
                        //no hay tratamiento !
                        //usar la agrupacion que viene en agrupacionTAM
                        console.log("No hay tratamiento, usar agrupacion de " + prod.agrupacionTam);
                        composicion[i].peso_real = parseFloat(composicion[i].cantidad * prod.agrupacionTam);
                    }else{
                        //si hay tratamiento !
                        //ahora, hay que ver si este producto es original o procesado
                        if(composicion[i].procesada){
                            composicion[i].peso_real = parseFloat(composicion[i].cantidad * prod.agrupacionTam);
                        }else{
                            composicion[i].peso_real = parseFloat(composicion[i].cantidad * prod.peso_por_arpilla);
                        }
                    }

                    //el descuento es por agrupacion, kilo por arpilla, entonces es el peso real menos (cantidad * descuento)
                    composicion[i].peso_a_cobrar = parseFloat( composicion[i].peso_real - (composicion[i].cantidad * composicion[i].descuento) );                                                
                        
                }else{
                    //no hay agrupacion !
                    console.log("No tiene agrupacion !");
                    composicion[i].peso_real = parseFloat(composicion[i].cantidad);
                        
                    //composicion[i].peso_a_cobrar = parseFloat(composicion[i].cantidad - composicion[i].descuento );                                                
                    if(composicion[i].agrupacion){
                        composicion[i].peso_a_cobrar = parseFloat( composicion[i].peso_real - (parseFloat(composicion[i].cantidad/prod.agrupacionTam) * composicion[i].descuento) );                                                
                    }else{
                        composicion[i].peso_a_cobrar = parseFloat(composicion[i].cantidad - composicion[i].descuento );  
                    }
                        
                }
			
                console.log("#agrupacion-" +  id_compra + "-" + id_producto);    
                        
                console.log("peso real : " + composicion[i].peso_real);
                console.log("peso peso a cobrar : " + composicion[i].peso_a_cobrar);

                //actualizar las cajas de los importes
                // vamos a ver si el precio a cobrar es por agrupacion o por escala
                // si tiene agrupacion... 
                var importe = 0;
                if( prod.agrupacion && prod.precio_por_agrupacion ){
                    console.log("El precio es por "+ composicion[i].agrupacion  +" ! ");
				
                    //importe = composicion[i].precio * (composicion[i].cantidad - (composicion[i].cantidad * composicion[i].descuento));
				
                    //el precio es por agruacion, el descuento es por escala ! que pedo !?!?!?
				
                    //sacar el precio por escala, eso es,  precio / agrupacionTam
                    var precio_por_escala = composicion[i].precio / parseFloat(prod.agrupacionTam) ;
				
                    //composicion[i].peso_a_cobrar
				
                    //cuantas escalas son ?
                    var escalas = parseFloat(prod.agrupacionTam) * composicion[i].cantidad;
                    escalas -= (composicion[i].descuento  * composicion[i].cantidad) ;
				
                    //multiplicar escalas, por precio por escala y voila !
                    importe = escalas * precio_por_escala;
				
                }else{
                    console.log("El precio es por escala !");
                    importe = composicion[i].peso_a_cobrar * composicion[i].precio;

                }
			
                composicion[i].importe = importe;
			
                console.log("Eso es cantidad= " + composicion[i].cantidad + " precio=" +composicion[i].precio , "total = " + importe);

                jQuery( "#" + composicion[i].id_compra + "-" + composicion[i].id_producto + "-importe" ).val( cf(	importe )  );
			
                obj.importe_por_unidad 		+= importe;	
                obj.peso_real 				+= composicion[i].peso_real;
                obj.peso_a_cobrar 			+= composicion[i].peso_a_cobrar;			
            
                //termina sub-composicion i-esima
                console.groupEnd();
                    
                if( composicion[i].id_compra === id_compra && composicion[i].id_producto === id_producto ){                          
                        
                    if(composicion[i].agrupacion){
                        jQuery("#agrupacion-" +  id_compra + "-" + id_producto).html( round(parseFloat(composicion[i].cantidad/prod.agrupacionTam) ,2) + " " + composicion[i].agrupacion+ "s");
                    }else{                            
                        jQuery("#agrupacion-" +  id_compra + "-" + id_producto).html( round(parseFloat(composicion[i].cantidad),2) + " " + composicion[i].escala + "s" );
                    }

                }
                    
            }
        
            obj.importe_por_unidad /= obj.peso_a_cobrar;

            jQuery("#compuesto-peso-real").html 		( obj.peso_real.toFixed(4)  + " " + obj.escala + "s");
            jQuery("#compuesto-peso-a-cobrar").html 	( obj.peso_a_cobrar.toFixed(4) + " " + obj.escala + "s");
            jQuery("#compuesto-importe-por-unidad").html( cf(obj.importe_por_unidad) );
            jQuery("#compuesto-importe-total").html		( cf(obj.importe_por_unidad * obj.peso_a_cobrar) );      


            console.groupEnd();
            console.groupEnd();
        };
    
        this.quitarProducto = function( id_compra, id_producto ){
       
            var index = null,
            i;

            jQuery("#" +  id_compra + "-" + id_producto + "-composicion").remove();
        
            //buscar esa composicion el arreglo
            for ( i = composicion.length - 1; i >= 0; i--){
                if( composicion[i].id_compra === id_compra 
                    && composicion[i].id_producto === id_producto){
                    index = i;
                    break;
                }
            }
        
            if(composicion[index].cantidad != 0){
                this.doMath( id_compra, id_producto, "cantidad", 0 );
            }
        
            composicion.splice(index,1);
        
        };
    
        this.agregarProducto = function( id_compra, id_producto, rowIndex ){           
        
            console.log( "agregarProducto( " + id_compra + "," +  id_producto +","+ rowIndex + " )" );

            //obtener el producto
            producto = inventario.getProducto( id_compra, id_producto );

            var html = "";
        
            html += td( "<img onClick='composicionTabla.quitarProducto(" + id_compra + "," + id_producto + ")' src='../media/icons/close_16.png'>" );
            html += td( producto.folio );
            html += td( producto.producto_desc );
        
            var keyup = "onkeyup='composicionTabla.doMath(" + id_compra + "," + id_producto + ", this.name, this.value )'";                
            var click = "onClick='composicionTabla.doMath(" + id_compra + "," + id_producto + ", this.name, this.value )'";
            var escala;


            //si tiene agrupacion
            if(producto.agrupacion && producto.precio_por_agrupacion){
                escala = producto.agrupacion + "s";
			
            }else{
                escala = producto.escala + "s";

            }


            html += td( "<input name='cantidad' " + keyup + " value='0' type='text'>&nbsp;" + escala + '<div id = "agrupacion-'+id_compra+'-'+id_producto+'">&nbsp;</div>' );
                
		
            var procesadas = parseFloat( producto.existencias_procesadas );

            if( producto.producto_tratamiento !== null){
                if(procesadas > 0){
                    html += td( "<input style='width: 100px' name='proc' "+click+" type='checkbox'> " );         
                }else{
                    html += td( "<input style='width: 100px'  type='checkbox' disabled> " );
                }
            }else{
                html += td( "<input type='hidden'><i>-</i>" );         
            }

        
            var costo_flete = 0;
        
            if( parseFloat (producto.costo_flete) != 0){
                costo_flete =   producto.costo_flete / producto.peso_origen;
            }



		

            console.log("El precio original de este producto ya con flete es de " + cf(parseFloat(producto.precio_por_kg) + parseFloat(costo_flete)) + " pesos por escala.");
		
            var escala_precio;
		
	
		
            //si tiene agrupacion
            if(producto.agrupacion && producto.precio_por_agrupacion){
                escala_precio = producto.agrupacion ;
			
            }else{
                escala_precio = producto.escala ;

            }
	
            var precio_total = roundNumber( parseFloat(producto.precio_por_kg)  + parseFloat(costo_flete) );
		
            if(producto.precio_por_agrupacion){
                precio_total *= producto.agrupacionTam;
            }

            html += td( "<input name='precio'     value='"+precio_total +"' "    +keyup+"    type='text'>"
                + "&nbsp; por " + escala_precio );
            var escala_descuento;

            if(producto.agrupacion){
                escala_descuento = producto.escala + "s por " + producto.agrupacion;
            }else{
                escala_descuento = producto.escala + "s";
            }

            html += td( "<input name='descuento'  value='0'                     "   +keyup+"    type='text'>&nbsp;" + escala_descuento);
            html += td( "<input id='" +id_compra+"-"+ id_producto+ "-importe'                   type='text' disabled>" );

    
            var c = new Composicion();
            c.setIdProducto( producto.id_producto );
		
            composicion.push({
                agrupacion  : producto.agrupacion,
                id_compra   : producto.id_compra_proveedor,
                id_producto : producto.id_producto, 
                cantidad    : 0,
                desc        : producto.producto_desc,
                procesada   : false,
                escala      : producto.escala,
                precio      : precio_total, //parseFloat(producto.precio_por_kg) + parseFloat(costo_flete),
                precio_original : parseFloat(producto.precio_por_kg) + parseFloat(costo_flete) ,
                descuento   : 0,
                existencias : producto.existencias,
                existencias_procesadas : producto.existencias_procesadas
            });

            jQuery("#ASurtirTablaHeader").after( tr(html, "id='" + id_compra + "-" + id_producto + "-composicion'") );
        };
    
        this.commitMix = function( ){
            var c, total_qty = 0, i;
        
            //revisar que todo concuerde
            for ( i = composicion.length - 1; i >= 0; i--){
	
                c = composicion[i];
                
                total_qty += c.peso_total;
                
                if(c.cantidad <= 0){
                    error("Verifique la cantidad de producto", "La cantidad de producto a vender debe ser mayor que cero.");
                    return;
                }

                if(c.importe <= 0){
                    error("Descuento Excesivo", "No puede vender un producto con un descuento tan grande.");
                    return;
                }

                var el = c.id_compra + "-" + c.id_producto +  "-composicion" ;

                if(!c.procesada && c.cantidad > c.existencias){
                    error("Verifique su cantidad", "No puede vender mas producto del que en realidad posee.");
                    return;
                }
                
                if(c.procesada && c.cantidad > c.existencias_procesadas){
                    error("Verifique su cantidad", "No puede vender mas producto del que en realidad posee.");
                    return;
                }

            }

            if(composicion.length == 0){
                error("No ha agregado ningun producto", "El producto debe conmponerse de por lo menos un producto. Agregue un producto de su inventario maestro para continuar.");
                return;
            }


            if(total_qty == 0){
                error("El peso total es cero", "No puede componer un producto a surtir cuando el peso total es igual a cero. ");
                return;
            }            

            composiciones.push({
                items : composicion,
                producto : id_producto,
                procesado : (jQuery("#compuesto-procesado:checked").length == 1)
            });
    
            jQuery("#listaDeProductos").slideDown('fast', function (){

                jQuery('#InvMaestro').slideUp();
                jQuery('#ASurtir').slideUp('fast', function (){

                    //jQuery('html,body').animate({scrollTop: jQuery('#InvMaestro').position().top }, 1000);
                });     

            });
        
            for ( i = 0; i < composicion.length; i++) {
                inventario.descontarProducto( composicion[i] );         
            }

            renderFinalShip();

        };
    
        this.rollbackMixIndex = function( mix_index ){
            var i;
        
            for (i=0; i < composiciones[mix_index].items.length; i++) {
                tablaInventario.regresarProducto( composiciones[mix_index].items[i] );    
                inventario.recontarProducto( composiciones[mix_index].items[i]  );             
            }

            /*
                composicionTabla.doMath( //id_compra
                                                                composiciones[mix_index].id_compra , 
								
                                                                //id_producto
                                                                composiciones[mix_index].id_producto, 
								
                                                                //campo
                                                                "cantidad",
								
                                                                //valor
                                                                "0 ");
             */
		
		
            console.warn("Known bug #146, ya esta resuelto en mantis, pero si es cierto que ya funciona ?");
		
            jQuery("#listaDeProductos").slideDown('fast', function (){
                jQuery('#InvMaestro').slideUp();
                jQuery('#ASurtir').slideUp('fast', function (){ } );     
            });
            composiciones.splice(mix_index, 1);
            renderFinalShip();
        };

        this.rollbackMix = function(  ){
            var i;
            renderFinalShip();
        
            for (i=0; i < composicion.length; i++) {
                tablaInventario.regresarProducto( composicion[i] );         
            }
        
        
            jQuery("#listaDeProductos").slideDown('fast', function (){

                jQuery('#InvMaestro').slideUp();
                jQuery('#ASurtir').slideUp('fast', function (){ } );     

            });
        };


        if(DEBUG){
            console.log("Construyendo ComposicionTabla !");
        }
        __init(config);
    };

   
    var Composicion = function(){
	
        var unidaes,
        agrupada,
        tipoDeAgrupacion,
        tamAgrupacion,
        procesable,
        procesada;
		
        this.setIdProducto = function (id_producto){
            var p = inventario.getProductoDesc(id_producto);
        }
		
        var __init = function(  ){
		
        }
	
    }


<?php
/**
 * Renderear un arreglo con las sucursales
 * 
 * */
foreach ($sucursales as $suc) {
    echo " sucursales[" . $suc->getIdSucursal() . "] = \"" . $suc->getDescripcion() . "\";";
}
?>

    function seleccionarSucursal(){


        if(currentSuc !== null){
            jQuery("#actual" + currentSuc).slideUp();
        }


<?php
if (isset($_REQUEST['aut'])) {
    echo 'jQuery("#Solicitud").slideDown();';
}
?>            

        jQuery("#actual" + jQuery('#sucursal').val()).slideDown();
        //jQuery("#InvMaestro").slideDown();
        //jQuery("#ASurtir").slideDown();
        currentSuc = jQuery('#sucursal').val();
        jQuery("#select_sucursal").slideUp();
    }

    function seleccionDeProd( id ){
    
        if(DEBUG){
            console.log("Producto seleccionado ! pid="  + id);		
        }
    
        composicionTabla = new ComposicionTabla({
            renderTo : "ComposicionTabla",
            id_producto : id
        });
    
        if(DEBUG){
            console.log("Ya cree la tabla de composicion...");
        }

        jQuery("#listaDeProductos").slideUp('fast', function (){
            jQuery("#FinalShip").slideUp();
            jQuery('#InvMaestro').slideDown();
            jQuery('#ASurtir').slideDown('fast', function (){
                jQuery('html,body').animate({scrollTop: jQuery('#InvMaestro').position().top }, 1000);
            });     
        });
    
    }

    function getEscalaCorta(escala){
        switch(escala){
            case "kilogramo": return "Kgs"; break;
            case "pieza": return "Pzas"; break;
        }
    }

    function renderFinalShip(){
    
        var global_qty = 0, global_qty_real = 0, global_importe = 0, global_cost = 0;
    
        var html = '<table style="width: 100%; padding-top: 5px;">';
        html += '<tr align=left>'
            + '<th></th>'
            + '<th>Producto</th>'
            + '<th>Peso real</th>'
            + '<th>Peso a cobrar</th>'      
            + '<th>Composicion</th>'
            + '<th>Costo</th>'
            + '<th>Importe</th>'
            + '<th>Rendimiento</th>';
            
        for (var i=0; i < composiciones.length; i++) {
        
            jQuery("#producto-" + composiciones[i].producto ).css("text-decoration", "line-through");
            jQuery("#producto-" + composiciones[i].producto ).fadeTo(250, .25);
        
            desc = inventario.getProductoDesc( composiciones[i].producto );
        
            /// totales 
            var total_qty 			= 0;
            var total_qty_with_desc = 0;        
            var total_money 		= 0;
            var composition 		= '';
            var costo_total 		= 0;

        
            for (var j = composiciones[i].items.length - 1; j >= 0; j--){
                // cada una de las i-esimas composiciones
                console.log("i-esima composcion !" , composiciones[i].items[j]);
			
                //buscar este producto
                var actual_prod = inventario.getProducto( composiciones[i].items[j].id_compra, composiciones[i].items[j].id_producto  );


                //sumar a totales !
                total_qty 			+= composiciones[i].items[j].peso_real  ;
                total_qty_with_desc += composiciones[i].items[j].peso_a_cobrar ;         
                total_money 		+= ( composiciones[i].items[j].importe );
                costo_total 		+= composiciones[i].items[j].precio_original * composiciones[i].items[j].peso_real ;

                //el html !
                // el nombre de la subcomposicion
                composition += "<b>"+ composiciones[i].items[j].desc + "</b>&nbsp;"

                //si es procesable, mostrar si es procesado o no
                if(actual_prod.producto_tratamiento){
                    //es tratable !
                    composition += (composiciones[i].items[j].procesada ? "Procesada" : "Original");
                }else{
                    //no es tratable !
                    //no hacer nada
                }
			
                composition += "<br>";
			
                //mostrar la cantidad
                composition +=  composiciones[i].items[j].cantidad.toFixed(2) + " ";
						
                //si el precio es por agrupacion mostrar la agrupacion , si no mostrar la escala
                if(actual_prod.precio_por_agrupacion){
                    composition += actual_prod.agrupacion;
                }else{
                    composition += actual_prod.escala;			
                }
			
                composition +=  "s <b> - </b>";
			
                //mostrar el descuento 
                composition += composiciones[i].items[j].descuento.toFixed(2) ;
			
                //mostrar escala + agrupacion si es que hay !
                composition += " " +  getEscalaCorta( composiciones[i].items[j].escala )+ " ";
			
                if(actual_prod.agrupacion){
                    composition += " por " + actual_prod.agrupacion;
                }else{
			
                }
			
                composition += "<br>";

            }
        
            var color = i % 2 == 0 ? 'style="background-color: #D7EAFF"' : "";

            var main_prod = inventario.getProductoDesc( composiciones[ i ].producto );

            var escala = getEscalaCorta( desc.escala  );

            html += tr(
            td( "<img src='../media/icons/basket_close_32.png' onClick='composicionTabla.rollbackMixIndex("+i+")'>" )
                + td( desc.descripcion )
                + td( total_qty.toFixed(4) +" " +  escala )
                + td( total_qty_with_desc.toFixed(4) + " " +  escala )
                + td( composition)
                + td( cf(costo_total))
                + td( cf(total_money)) 
                + td( cf(total_money-costo_total)), color)
                    
            global_qty += total_qty;
            global_qty_real += total_qty_with_desc;
            global_importe += total_money;
            global_cost += costo_total;
        };



        html += tr(
        td( "Totales", "style='padding-top: 10px'" )
            + td( "")
            + td( global_qty.toFixed(2) )
            + td( global_qty_real.toFixed(2) )     
            + td( "")
            + td( cf(global_cost.toFixed(4)))
            + td( cf(global_importe.toFixed(4) ) )
            + td( cf(global_importe.toFixed(4) - global_cost.toFixed(4)) , 
        (global_importe.toFixed(4) - global_cost.toFixed(4)) < 0 ? "style='color:red;'" : "style='color:green;'" ),
                
        "style='border-top: 1px solid #3F8CE9; font-size: 13px;'");

        html += '</html>';
    
        jQuery("#FinalShipTabla").html(html);
        jQuery("#FinalShip").fadeIn();
    
        jQuery("#compuesto-peso-real").html 		( 0 );
        jQuery("#compuesto-peso-a-cobrar").html 	( 0 );
        jQuery("#compuesto-importe-por-unidad").html( cf(0) );
        jQuery("#compuesto-importe-total").html 	( cf(0) );
    
    }


    function doSurtir()
    {

        if(!currentSuc){
            error("&iquest; A que sucursal ?", "No ha seleccionado a que sucural desea surtir este pedido.");
            return;
        }

        var readyDATA = {
            productos : composiciones,
            sucursal : currentSuc,
            conductor : jQuery('#conductor').val()
        };
    
        console.warn("ESTO ES LO QUE VOY A ENVIAR !", readyDATA);

        var debugging = false;

        //hacer ajaxaso
        jQuery.ajaxSettings.traditional = true;

        jQuery("#submitButtons").fadeOut("slow",function(){
            jQuery("#loader").fadeIn();
        
            jQuery.ajax({
                url: "../proxy.php",
                data: { 
                    action : 1005, 
                    data : jQuery.JSON.encode( readyDATA )
                },
                cache: false,
                success: function(data){
                    try{
                        response = jQuery.parseJSON(data);
                        //console.log(response, data.responseText)
                    }catch(e){
            
                        jQuery("#loader").fadeOut('slow', function(){
                            jQuery("#submitButtons").fadeIn();
                            window.scroll(0,0);                         
                            jQuery("#ajax_failure").html("Error en el servidor, porfavor intente de nuevo").show();
                            jQuery("#submitButtons").fadeIn();
                        });                
                        return;                    
                    }
    

                    if(response.success === false ){
	


                        jQuery("#loader").fadeOut('slow', function(){
                            //jQuery("#submitButtons").fadeIn();    
                            window.scroll(0,0);
                            try{
                                jQuery("#ajax_failure").html(response.reason).show();
                            }catch(e){
                                jQuery("#ajax_failure").html("Error inesperado").show();
                            }

                            jQuery("#submitButtons").fadeIn();
                        });                
                        return ;
                    }

                    if(debugging){
                        jQuery("#loader").fadeOut();
                        jQuery("#submitButtons").fadeIn();					
                        window.open ("inventario.php?action=detalleCompraSucursal&cid=" + response.compra_id + "&pp=1");
                        return;
                    }
			
                    window.location = "inventario.php?action=detalleCompraSucursal&cid=" + response.compra_id + "&pp=1";
    
                }
            });
        });
    }








<?php
echo " var inventario_maestro_extjs = " . json_encode($iMaestro) . ";";
?>

    var MasterGrid;
    //var sm = new Ext.grid.CheckboxSelectionModel();

    Ext.onReady(function(){
        Ext.QuickTips.init();

        // NOTE: This is an example showing simple state management. During development,
        // it is generally best to disable state management as dynamically-generated ids
        // can change across page loads, leading to unpredictable results.  The developer
        // should ensure that stable state ids are set for stateful components in real apps.    

        // uncomment this on deployment !!!
        // Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

        var inventario_formateado = [  ];
	
        for (var i = inventario_maestro_extjs.length - 1; i >= 0; i--){
            inventario_formateado[i] = [
                inventario_maestro_extjs[i].arpillas,
                inventario_maestro_extjs[i].calidad,
                inventario_maestro_extjs[i].chofer,
                inventario_maestro_extjs[i].costo_flete,
                inventario_maestro_extjs[i].existencias,
                inventario_maestro_extjs[i].existencias_procesadas,
                inventario_maestro_extjs[i].fecha,
                inventario_maestro_extjs[i].fecha_origen,
                inventario_maestro_extjs[i].folio,
                inventario_maestro_extjs[i].id_compra_proveedor,
                inventario_maestro_extjs[i].id_producto,
                inventario_maestro_extjs[i].id_proveedor,
                inventario_maestro_extjs[i].kg,
                inventario_maestro_extjs[i].marca_camion,
                inventario_maestro_extjs[i].medida,
                inventario_maestro_extjs[i].merma_por_arpilla,
                inventario_maestro_extjs[i].modelo_camion,
                inventario_maestro_extjs[i].numero_de_viaje,
                inventario_maestro_extjs[i].peso_origen,
                inventario_maestro_extjs[i].peso_por_arpilla,
                inventario_maestro_extjs[i].peso_recibido,
                inventario_maestro_extjs[i].placas_camion,
                inventario_maestro_extjs[i].precio_por_kg,
                inventario_maestro_extjs[i].producto_desc,
                inventario_maestro_extjs[i].producto_tratamiento,
                inventario_maestro_extjs[i].productor,
                inventario_maestro_extjs[i].sitio_descarga,
                inventario_maestro_extjs[i].sitio_descarga_desc,
                inventario_maestro_extjs[i].total_origen,
                inventario_maestro_extjs[i].variedad,
                inventario_maestro_extjs[i].agrupacion,
                inventario_maestro_extjs[i].agrupacionTam								
            ];

        };
	


        // create the data store
        function getNewStore () {
            return new Ext.data.ArrayStore({
                fields: [
                    { name : 'arpillas', 				type : 'float' },
                    { name : 'calidad', 				type : 'string' },
                    { name : 'chofer', 					type : 'string' },
                    { name : 'costo_flete', 			type : 'float' },
                    { name : 'existencias', 			type : 'float' },
                    { name : 'existencias_procesadas', 	type : 'float' },
                    { name : 'fecha', 					type : 'date', dateFormat: 'j/m/y' },
                    { name : 'fecha_origen', 			type : 'date', dateFormat: 'Y-m-d' },
                    { name : 'folio', 					type : 'string' },
                    { name : 'id_compra_proveedor', 	type : 'int' },
                    { name : 'id_producto', 			type : 'int' },
                    { name : 'id_proveedor', 			type : 'int' },
                    { name : 'kg', 						type : 'float' },
                    { name : 'marca_camion', 			type : 'string' },
                    { name : 'medida', 					type : 'string' },
                    { name : 'merma_por_arpilla', 		type : 'float' },
                    { name : 'modelo_camion', 			type : 'string' },
                    { name : 'numero_de_viaje', 		type : 'string' },
                    { name : 'peso_origen', 			type : 'float' },
                    { name : 'peso_por_arpilla', 		type : 'float' },
                    { name : 'peso_recibido', 			type : 'float' },
                    { name : 'placas_camion', 			type : 'string' },
                    { name : 'precio_por_kg', 			type : 'float' },
                    { name : 'producto_desc', 			type : 'string' },
                    { name : 'producto_tratamiento', 	type : 'string' },
                    { name : 'productor', 				type : 'string' },
                    { name : 'sitio_descarga', 			type : 'int' },
                    { name : 'sitio_descarga_desc', 	type : 'string' },
                    { name : 'total_origen', 			type : 'float' },
                    { name : 'variedad', 				type : 'string' },
                    { name : 'agrupacion', 				type : 'string' },
                    { name : 'agrupacionTam', 			type : 'float' }						
                ]
            });
        }
	
	
        var store = getNewStore();



        function toSmallUnit( unit ){
            switch(unit){
                case "kilogramo" : return "Kgs";
                case "pieza" : return "Pzas";
                case "arpilla" : return "Arps";
                case "cajas" : return "Cjs";
                case "bulto" : return "Blts";				
            }
        }

        // manually load local data
        store.loadData(inventario_formateado);
	
        // create the Grid
        MasterGrid = new Ext.grid.GridPanel({
            store: store,
            header : false,
            columns: [
                {
                    header   : 'Fecha', 
                    width    : 75, 
                    sortable : true, 
                    renderer : Ext.util.Format.dateRenderer('d/m/Y'),  
                    dataIndex: 'fecha'
                },
                {
                    header   : 'Remision', 
                    width    : 75, 
                    sortable : true, 
                    dataIndex: 'folio'
                },	
                {
                    id       :'descripcion',
                    header   : 'Producto', 
                    width    : 180, 
                    sortable : true, 
                    dataIndex: 'producto_desc'
                },
                {
                    header   : 'Variedad', 
                    width    : 85, 
                    sortable : true, 
                    dataIndex: 'variedad'
                },	
                {
                    header   : 'Promedio', 
                    width    : 85, 
                    sortable : true, 
                    align 	 : "right",	
                    renderer : function(n, c){
                        return n.toFixed(4);
                    },
                    dataIndex: 'peso_por_arpilla'
                },	
                {
                    header   : 'Merma', 
                    width    : 85, 
                    sortable : true, 
                    hidden	 : true,
                    dataIndex: 'merma_por_arpilla'
                },
                {
                    header   : 'peso_origen', 
                    width    : 85, 
                    sortable : true, 
                    hidden: true,
                    dataIndex: 'peso_origen'
                },
                {
                    header   : 'Costo', 
                    width    : 85, 
                    sortable : true, 
                    hidden: true,	
                    renderer : 'usMoney',
                    dataIndex: 'precio_por_kg'
                },			
                {
                    header   : 'productor', 
                    width    : 85, 
                    sortable : true, 
                    hidden: true,	
                    dataIndex: 'productor'
                },
                {
                    header   : 'Originales', 
                    width    : 150, 
                    sortable : true, 
                    align 	 : "right",
                    renderer : function (n, a, row ){

                        if(row.data.agrupacion.length > 0){

                            //si hay agrupacion
                            if(row.data.producto_tratamiento.length == 0){
                                /* ********************************
                                 *  agrupacion y sin tratamiento !
                                 * ********************************	*/
                                var v = (parseFloat( n / row.data.agrupacionTam )).toFixed(2) + " " +  toSmallUnit(row.data.agrupacion)
                                    +"&nbsp;(<i>" + n.toFixed(2) + " " +  toSmallUnit(row.data.medida) + "</i>)";

                            }else{
                                /* ********************************
                                 *  agrupacion y CON tratamiento !
                                 * ********************************/	
                                
                                var diferencia =  n - row.data.existencias_procesadas;
                                var cantidad_original = 0;//cantidad
                                
                                if(diferencia < 0){
                                    cantidad_original = diferencia + row.data.existencias_procesadas;
                                }else{
                                    cantidad_original = diferencia;
                                }
                                
                                var v = (parseFloat( cantidad_original / row.data.peso_por_arpilla )).toFixed(2) + " " +  toSmallUnit(row.data.agrupacion)
                                    +"&nbsp;(<i>" + cantidad_original.toFixed(2) + " " +  toSmallUnit(row.data.medida) + "</i>)";

                                //var v = (parseFloat( ( n - row.data.existencias_procesadas) / row.data.peso_por_arpilla )).toFixed(2) + " " +  toSmallUnit(row.data.agrupacion)
                                //    +"&nbsp;(<i>" + ( n - row.data.existencias_procesadas).toFixed(2) + " " +  toSmallUnit(row.data.medida) + "</i>)";
                            }

                            if(cantidad_original < 0){
                                return ( "<span style='color:red'>" + v +"</span>" );
                            }else{
                                return v;
                            }
                        }else{
                            //no hay agrupacion
                            var v = n.toFixed(2) + " " +  toSmallUnit(row.data.medida);
						
                            if(n < 0){
                                return ( "<span style='color:red'>" + v +"</span>" );
                            }else{
                                return v;
                            }
                        }

                    }, 
                    dataIndex: 'existencias'
                },
                {
                    header   : 'Procesadas', 
                    width    : 150, 
                    align 	 : "right",	
                    sortable : true, 
                    renderer : function(n,a,row){
                        if(isNaN(n)){
                            return "-";
                        }else{

                            if(row.data.agrupacion.length > 0){
                                //si hay agrupacion
                                var v = (parseFloat( n / row.data.agrupacionTam )).toFixed(2) + " " +  toSmallUnit(row.data.agrupacion)
                                    +"&nbsp;(<i>" + n.toFixed(2) + " " +  toSmallUnit(row.data.medida) + "</i>)";

                                if(n < 0){
                                    return ( "<span style='color:red'>" + v +"</span>" );
                                }else{
                                    return v;
                                }

                            }else{
                                //no hay agrupacion
                                var v = n.toFixed(2) + " " +  toSmallUnit(row.data.medida);

                                if(n < 0){
                                    return ( "<span style='color:red'>" + v +"</span>" );
                                }else{
                                    return v;
                                }
                            }


                        }
                    }, 
                    dataIndex: 'existencias_procesadas'
                },	
                {
                    header   : 'sitio_descarga_desc', 
                    width    : 95, 
                    sortable : true, 
                    hidden: true,	
                    dataIndex: 'sitio_descarga_desc'
                }
            ],
            stripeRows: true,
            autoExpandColumn: 'descripcion',
            height: "auto",
            minHeight : 300,
            width: "100%",
            /*		frame : false,
                header: false, */
            // title: 'Array Grid',
            // config options for stateful behavior
            stateful: false,
            stateId: 'grid2',
            listeners : {
                "rowclick" : function (grid, rowIndex, e){
                    var datos = grid.getStore().getAt( rowIndex );                                        
				
                    composicionTabla.agregarProducto(  datos.get("id_compra_proveedor" ), datos.get("id_producto"), rowIndex );
				
                    if(DEBUG){
                        console.log( "Row click !",  datos.get("id_compra_proveedor" ), datos.get("id_producto"), rowIndex );					
                    }
                }
            }
		
        });



        // render the grid to the specified div in the page
        MasterGrid.render('inventario-maestro-grid');

    });



    /*
function restart(){
    jQuery.facebox('<h1>Volver a comenzar</h1>Todos los cambios que ha realizado se perderan. &iquest; Esta seguro que desea comenzar de nuevo ?'
            + "<br><div align='center'>"
            + "         <input type='button' onclick=\"window.location = 'inventario.php?action=surtir'\" value='Si'>"
            + "&nbsp;   <input type='button' onclick=\"jQuery(document).trigger('close.facebox')\" value='No'></div>"
        );
}

     */
</script>




<!-- 
		SELECCIONAR SUCURSAL
-->
<?php if (!isset($_REQUEST['sid'])) { ?>
    <div id="select_sucursal">
        <h2>Seleccione la sucursal que desea surtir</h2>
        <form id="newClient">
            <table border="0" cellspacing="5" cellpadding="5">
                <tr><td>Sucursal</td>
                    <td>
                        <select id="sucursal"> 
                            <?php
                            foreach ($sucursales as $suc) {
                                echo "<option value='" . $suc->getIdSucursal() . "' >" . $suc->getDescripcion() . "</option>";
                            }
                            ?>

                        </select>
                    </td>
                    <td><input type="button" onClick="seleccionarSucursal()" value="Seleccionar"/> </td>
                </tr>
            </table>
        </form>
    </div>
<?php } else { ?>
    <input type="hidden" value="<?php echo $_REQUEST['sid']; ?>" id="sucursal" />
<?php } ?>





<?php
//get sucursales
$sucursales = listarSucursales();

function renderProducto($val, $row) {
    return "<b>" . number_format($val, 2) . "</b>&nbsp;" . $row['medida'] . "s";
}

function renderProductoSmall($val, $row) {

    $medida = "";

    switch ($row['medida']) {
        case "kilogramo" : $medida = "Kgs";
            break;
        case "pieza" : $medida = "Pzas";
            break;
    }

    return "<b>" . number_format((float) $val, 2) . "</b>&nbsp;" . $medida;
}

function toUnit($e, $row) {
    //$row["tratamiento"]
    switch ($row["medida"]) {
        case "kilogramo" : $escala = "Kgs";
            break;
        case "pieza" : $escala = "Pzas";
            break;
    }

    return "<b>" . number_format($e / 60, 2) . "</b>Arp. / <b>" . number_format($e, 2) . "</b>" . $escala;
}

function toUnitProc($e, $row) {
    if ($row["tratamiento"] == null) {
        return "<i>NA</i>";
    }

    switch ($row["medida"]) {
        case "kilogramo" : $escala = "Kgs";
            break;
        case "pieza" : $escala = "Pzas";
            break;
    }

    return "<b>" . number_format($e / 60, 2) . "</b>Arp. / <b>" . number_format($e, 2) . "</b>" . $escala;
}

foreach ($sucursales as $sucursal) {

    print ("<div id='actual" . $sucursal["id_sucursal"] . "' style='display: none'>");
    print ("<h2>Inventario actual de " . $sucursal["descripcion"] . "</h2>");

    //obtener los clientes del controller de clientes
    $inventario = listarInventario($sucursal["id_sucursal"]);



    //render the table
    $header = array(
        "productoID" => "ID",
        "descripcion" => "Descripcion",
        "precioVenta" => "Precio a la venta",
        "existenciasOriginales" => "Originales",
        "existenciasProcesadas" => "Procesadas");



    $tabla = new Tabla($header, $inventario);
    $tabla->addColRender("precioVenta", "moneyFormat");
    $tabla->addColRender("existenciasOriginales", "toUnit");
    $tabla->addColRender("existenciasProcesadas", "toUnitProc");
    $tabla->addNoData("Esta sucursal no tiene nigun registro de productos en su inventario");
    $tabla->render();
    printf("</div>");
}
?>

<!--
	Seleccion de producto a surtir
-->
<div id="listaDeProductos">
    <h2>Productos disponibles para surtir</h2>
    <h3>&iquest; Que productos desea surtir ?</h3>
    <?php
    echo "<table border=0 style='width: 100%; font-size: 14px; cursor: pointer;'>";
    echo "<tr>";
    for ($a = 0; $a < sizeof($productos); $a++) {

        //buscar su precio sugerido actual
        $act = new ActualizacionDePrecio( );
        $act->setIdProducto($productos[$a]->getIdProducto());
        $res = ActualizacionDePrecioDAO::search($act, "fecha", "desc");
        $lastOne = $res[0];

        //buscar todas las existencias
        $totals = 0;
        for ($i = 0; $i < sizeof($iMaestro); $i++) {
            if ($iMaestro[$i]['id_producto'] == $productos[$a]->getIdProducto()) {
                $totals += $iMaestro[$i]['existencias'];
            }
        }
        if ($a % 5 == 0) {
            echo "</tr><tr>";
        }

        echo "<td class='prod rounded' id='producto-" . $productos[$a]->getIdProducto() . "'  onClick='seleccionDeProd( " . $productos[$a]->getIdProducto() . " )' \">";
        echo "<img style='float:left;' src='../media/icons/basket_32.png'><div align=center ><b>" . $productos[$a]->getDescripcion() . "</b></div>";
        echo "<div align=center style='padding-right:20px'>";
        echo moneyFormat($lastOne->getPrecioVenta());
        echo "</div>";
        echo "</td>";
    }
    echo "</tr>";
    echo "</table>";
    ?>
</div>

<style>
    .prod {
        background:#fff;
        color:#333;
        text-decoration:none;
        padding:5px 10px;
        border:1px solid #fff;


        /* Add the transition properties! */
        -webkit-transition-property: background-color, color, border; 
        -webkit-transition-duration: 300ms;

        /* you can control the acceleration curve here */
        -webkit-transition-timing-function: ease-in-out; 
    }

    .prod:hover {
        background:#D7EAFF;
        color:#000;
        border:1px solid #3F8CE9;
    }
</style>




<!-- 
		MOSTRAR INVENTARIO MAESTRO
-->
<div id="InvMaestro" style="display: none;">
    <h2>Inventario Maestro</h2>
    <h3>&iquest; Como se conformara este producto ?</h3>
    <div id="InventarioMaestroTabla"></div>
    <div id="inventario-maestro-grid" style="padding: 5px;"></div> 	
</div>





<!-- 
		SELECCIONAR PRODUCTOS A SURTIR
-->
<div id="ASurtir" style="display: none;">
    <h2>Composicion del producto</h2>

    <div id="ComposicionTabla"></div>

    <h2>Detalles del producto a surtir</h2>
    <table width=100% >
        <tr style="background-color: #f0f0f0;  border-color: gray; border-top-color: white; border: 1px solid;"><td>Enviar producto procesado</td><td style="width:50%">
                <input style="width: 100px; margin: 5px;" id="compuesto-procesado" type="checkbox">
            </td></tr>		
        <tr ><td>Peso real</td><td>
                <div style=" margin: 5px;" id="compuesto-peso-real" >0.00</div>

            </td></tr>
        <tr style="background-color: #f0f0f0;  border-color: gray; border-top-color: white; border: 1px solid;"><td>Peso a cobrar</td><td>
                <div style=" margin: 5px;" id="compuesto-peso-a-cobrar" >0.00</div>

            </td></tr>		
        <tr><td>Importe por unidad</td><td>
                <div style=" margin: 5px;" id="compuesto-importe-por-unidad" >$0.00</div>

            </td></tr>
        <tr style="background-color: #f0f0f0;  border-color: gray; border-top-color: white; border: 1px solid;"><td>Importe total por este producto</td><td>
                <div style=" margin: 5px;" id="compuesto-importe-total" >$0.00</div>


            </td></tr>
    </table>
    <h4 >
        <input type="button" value="Aceptar" onclick="composicionTabla.commitMix()">
        <input type="button" value="Cancelar" onclick="composicionTabla.rollbackMix()">
    </h4>


</div>




<div id="FinalShip" style="display: none;">
    <h2>Productos a surtir</h2>

    <div id="FinalShipTabla"></div>

    <!--
	    Seleccion de chofer
	-->
    <div id="select_sucursal">
        <h2>Indique quien entragara el embarque</h2>
        <form >
            <table border="0" cellspacing="5" cellpadding="5">
                <tr><td>Conductor</td>
                    <td>
                        <input type="text" value="" id="conductor" />
                    </td>
                    <td> </td>
                </tr>
            </table>
        </form>
    </div>

    <h4 id="submitButtons" align='center'  >
        <input type="button" value="Surtir" onclick="doSurtir()">
        <!-- <input type="button" value="Volver a comenzar" onclick="restart()"> -->
    </h4>

    <div id="loader" 		style="display: none;" align="center"  >
        <img src="../media/loader.gif">
    </div>


</div>


<style>
    table{
        font-size: 11px;
    }
</style>
