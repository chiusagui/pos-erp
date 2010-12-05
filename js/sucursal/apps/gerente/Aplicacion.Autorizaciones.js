
/**
 * @constructor
 * @throws MemoryException Si se agota la memoria
 * @return Un objeto del tipo Autorizaciones
 */
Aplicacion.Autorizaciones = function (  ){

	return this._init();
}




Aplicacion.Autorizaciones.prototype._init = function (){
    if(DEBUG){
		console.log("Autorizaciones: construyendo");
    }

	
	//funcion que crea los paneles para las nuevas autorizaciones
	this.nueva.createPanels();
	
	//crear la el panel con la lista de autorizaciones
	this.listaDeAutorizacionesPanelCreator();

	//obtener autorizaciones actuales
	this.listaDeAutorizacionesLoad();
	
	
	Aplicacion.Autorizaciones.currentInstance = this;
	
	return this;
};




Aplicacion.Autorizaciones.prototype.getConfig = function (){
	return {
	    text: 'Autorizaciones',
	    cls: 'launchscreen',
	    items: [{
		    text: 'Nueva Autorizacion',
			items: [{
		        text: 'Limite de Credito',
	        	card: Aplicacion.Autorizaciones.currentInstance.nueva.creditoPanel,
	        	leaf: true
		    }, {
	        	text: 'Merma de producto',
	        	card: Aplicacion.Autorizaciones.currentInstance.nueva.mermaPanel,
		        leaf: true
		    }, {
	        	text: 'Devoluciones',
	        	card: Aplicacion.Autorizaciones.currentInstance.nueva.devolucionesPanel,
		        leaf: true
		    }]
	    },
	    {
	        text: 'Historial',
	        card: this.listaDeAutorizacionesPanel,
	        leaf: true
	    }]
	};
};
















/* ***************************************************************************
   * Nueva autorizacion
   * 
   *************************************************************************** */

Aplicacion.Autorizaciones.prototype.nueva = {};


Aplicacion.Autorizaciones.prototype.nueva.createPanels = function ()
{
	//cargar todos los paneles de nuevos
	
	this.devolucionesPanelCreator();
	
	this.creditoPanelCreator();
	
	this.mermaPanelCreator();
};




/* ********************************************************
	Devoluciones
******************************************************** */

Aplicacion.Autorizaciones.prototype.nueva.devolucionesEditPanel = null;


Aplicacion.Autorizaciones.prototype.nueva.devolucionesEditPanelShow = function( )
{

	//buscar esta venta en la estructura
	ventas = Aplicacion.Clientes.currentInstance.listaDeCompras.lista;
	var detalleVenta;
	v = Ext.getCmp("Aurotizacion-devolucionesIdVenta").getValue();
	found = false;
	
	for (var i = ventas.length - 1; i >= 0; i--){
		if(ventas[i].id_venta == v){
			detalleVenta = ventas[i].detalle_venta;
			found = true;
			break;
		}
	};
	
	if(!found){
		Ext.Msg.alert("asfsadfadsf");
		return;
	}
	
	if(DEBUG){
		console.log("Encontre la venta a la que se ha de devolver:", detalleVenta)
	}
	
	
	dis = Aplicacion.Autorizaciones.currentInstance.nueva;

	//crear este panel on-demand ya que no sera muy utilizado
	if(!dis.devolucionesEditPanel){
		dis.devolucionesEditPanelCreator();
	}

	//poner los valores del cliente en la forma
	sink.Main.ui.setActiveItem( dis.devolucionesEditPanel , 'slide');
};






Aplicacion.Autorizaciones.prototype.nueva.devolucionesEditPanelCreator = function()
{
	this.devolucionesEditPanel = new Ext.form.FormPanel({                                                       
			items: [{
				xtype: 'fieldset',
			    title: 'Solicitud de devolucion',
			    instructions: 'Selecciones los productos que se desean devolver.',
				items: [
					{
                        xtype: 'checkboxfield',
                        name : 'cool',
                        label: 'Cool',
                        value: 'cool'
                    }
			]},
			new Ext.Button({ ui  : 'action', text: 'Pedir autorizacion', handler: Aplicacion.Autorizaciones.currentInstance.nueva.devolucionesEditPanelShow, margin : 5  })
		]});
};




Aplicacion.Autorizaciones.prototype.nueva.devolucionesPanel = null;


Aplicacion.Autorizaciones.prototype.nueva.devolucionesPanelCreator = function()
{
	this.devolucionesPanel = new Ext.form.FormPanel({                                                       
			items: [{
				xtype: 'fieldset',
			    title: 'Autorizar devolucion',
			    instructions: 'Seleccione la venta de la que desea devolver',
				items: [
					new Ext.form.Text({ id: 'Aurotizacion-devolucionesIdVenta', label: 'ID Venta' })
				]},	
				new Ext.Button({ ui  : 'action', text: 'Buscar Venta', margin : 15,handler: this.devolucionesEditPanelShow })
		]});
};



/* ********************************************************
	Extender credito
******************************************************** */

Aplicacion.Autorizaciones.prototype.nueva.creditoModificarPanel = null;

Aplicacion.Autorizaciones.prototype.nueva.creditoModificarPanelShow = function( cliente )
{

	//crear este panel on-demand ya que no sera muy utilizado
	if(!this.creditoModificarPanel){
		this.creditoModificarPanelCreator();
	}

	//poner los valores del cliente en la forma
	console.log( cliente );
	Ext.getCmp("Autorizaciones-CreditoCliente").setValue(cliente.data.nombre);
	Ext.getCmp("Autorizaciones-CreditoActual").setValue( POS.currencyFormat( cliente.data.limite_credito) );
	Ext.getCmp("Autorizaciones-CreditoUsado").setValue( POS.currencyFormat( cliente.data.limite_credito - cliente.credito_restante ) );
	
	sink.Main.ui.setActiveItem( this.creditoModificarPanel , 'slide');
};






Aplicacion.Autorizaciones.prototype.nueva.creditoModificarPanelCreator = function()
{
	this.creditoModificarPanel = new Ext.form.FormPanel({                                                       
			items: [{
				xtype: 'fieldset',
			    title: 'Solicitud de limite de credito extendido',
			    instructions: 'Ingrese el nuevo limite de credito para este cliente.',
				items: [
					new Ext.form.Text({ id: 'Autorizaciones-CreditoCliente', label: 'Cliente' }),
					new Ext.form.Text({ id: 'Autorizaciones-CreditoActual', label: 'Actual' }),
					new Ext.form.Text({ id: 'Autorizaciones-CreditoUsado', label: 'Usado' }),	
					new Ext.form.Text({ id: 'Autorizaciones-CreditoNuevo', label: 'Nuevo limite' })
			]},
			new Ext.Button({ ui  : 'action', text: 'Pedir autorizacion', margin : 5  })
		]});
};




Aplicacion.Autorizaciones.prototype.nueva.creditoPanel = null;

Aplicacion.Autorizaciones.prototype.nueva.creditoPanelCreator = function()
{
	this.creditoPanel = new Ext.form.FormPanel({                                                       
			items: [{
				xtype: 'fieldset',
			    title: 'Limite de credito extendido',
			    instructions: 'Seleccione al cliente al que desea extenerle el credito',
				items: [{
					width : '100%',
					height : 350, 
					padding : 2,
					xtype: 'list',
					store: Aplicacion.Clientes.currentInstance.listaDeClientesStore,
					itemTpl: '<div class="listaDeClientesCliente"><strong>{nombre}</strong> {rfc}</div>',
					grouped: true,
					indexBar: true,
					listeners : {
						"selectionchange"  : function ( view, nodos, c ){
					
								if(nodos.length > 0){
									Aplicacion.Autorizaciones.currentInstance.nueva.creditoModificarPanelShow( nodos[0] );
								}

								//deseleccinar el cliente
								view.deselectAll();
							}
						}
					}
			]}		
		]});
};






/* ********************************************************
	Reportar merma
******************************************************** */


Aplicacion.Autorizaciones.prototype.nueva.mermaBuscarCompraBoton = function()
{
	//buscar esta compra en la estructura del inventario
	//que tiene las compras
	Ext.getCmp("Autorizacion-MermaCompraID").getValue();
	
	console.warn("TODO ! Buscar esta compra en la estrucutra del inventario" );
	
};


Aplicacion.Autorizaciones.prototype.nueva.mermaPanel = null;
Aplicacion.Autorizaciones.prototype.nueva.mermaPanelCreator = function ()
{
	this.mermaPanel = new Ext.form.FormPanel({                                                       
			items: [{
				xtype: 'fieldset',
			    title: 'Reportar merma en compra',
			    instructions: 'Ingrese el ID de compra.',
				items: [
					new Ext.form.Text({ id: 'Autorizacion-MermaCompraID', label: 'ID Compra' })
				]},	
				new Ext.Button({ ui  : 'action', text: 'Buscar Compra', margin : 15,  handler : this.mermaBuscarCompraBoton })
		]});

};







/* ***************************************************************************
   * Historial de autorizacones
   * Es una lista con las distintas autorizaciones, que pueden ordenarse por 
   * sus distintas caracteristicas
   *************************************************************************** */

/**
 * Registra el model para listaDeAutorizaciones
 */
Ext.regModel('listaDeAutorizacionesModel', {
	fields: [
		{ name: 'id_autorizacion',     type: 'string'}
	]
});





/**
 * Contiene un objeto con la lista de autorizaciones actual, para no estar
 * haciendo peticiones a cada rato
 */
Aplicacion.Autorizaciones.prototype.listaDeAutorizaciones = {
	lista : null,
	lastUpdate : null
};




/**
 * Leer la lista de autorizaciones del servidor mediante AJAX
 */

Aplicacion.Autorizaciones.prototype.listaDeAutorizacionesLoad = function (){
	
	if(DEBUG){
		console.log("Actualizando lista de autorizaciones ....");
	}
	
	Ext.Ajax.request({
		url: 'proxy.php',
		scope : this,
		params : {
			action : 207
		},
		success: function(response, opts) {
			try{
				autorizaciones = Ext.util.JSON.decode( response.responseText );				
			}catch(e){
				return POS.error(e);
			}
			
			if( !autorizaciones.success ){
				//volver a intentar
				return POS.error(autorizaciones);
			}
			
			
			this.listaDeAutorizaciones.lista = autorizaciones.payload;
			this.listaDeAutorizaciones.lastUpdate = Math.round(new Date().getTime()/1000.0);
			
			//agregarlo en el store
			this.listaDeAutorizacionesStore.loadData( autorizaciones.payload );

		},
		failure: function( response ){
			POS.error( response );
		}
	});

};








/**
 * Es el Store que contiene la lista de autorizaciones cargada con una peticion al servidor.
 * Recibe como parametros un modelo y una cadena que indica por que se va a sortear (ordenar) 
 * en este caso ese filtro es dado por 
 * @return Ext.data.Store
 */
Aplicacion.Autorizaciones.prototype.listaDeAutorizacionesStore = new Ext.data.Store({
    model: 'listaDeAutorizacionesModel',
    sorters: 'id_autorizacion',
           
    getGroupString : function(record) {
        return record.get('id_autorizacion')[0];
    }
});




/*
 * Contiene el panel con la lista de autorizaciones
 */
Aplicacion.Autorizaciones.prototype.listaDeAutorizacionesPanel = null;


/**
 * Pone un panel en listaDeAutorizacionesPanel
 */
Aplicacion.Autorizaciones.prototype.listaDeAutorizacionesPanelCreator = function (){
	this.listaDeAutorizacionesPanel =  new Ext.Panel({
        layout: Ext.is.Phone ? 'fit' : {
            type: 'vbox',
            align: 'center',
            pack: 'center'
        },
        
        items: [{
			
			width : '100%',
			height: '100%',
			xtype: 'list',
			store: this.listaDeAutorizacionesStore,
			itemTpl: '<div class="listaDeAutorizacionesAutorizacion"><strong>{nombre}</strong> {rfc}</div>',
			grouped: true,
			indexBar: true,
			listeners : {
				"selectionchange"  : function ( view, nodos, c ){
					
					if(nodos.length > 0){
						//nodos[0]
					}

					//deseleccinar
					view.deselectAll();
				}
			}
			
        }]
	});
};

















POS.Apps.push( new Aplicacion.Autorizaciones() );






