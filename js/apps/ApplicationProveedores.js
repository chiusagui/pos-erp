ApplicationProveedores= function ()
{
	if(DEBUG){
		console.log("ApplicationProveedores: construyendo");
	}
	
	ApplicationProveedores.currentInstance = this;	

	this._init();

	return this;
	
};

ApplicationProveedores.prototype.mainCard = null;

ApplicationProveedores.prototype.appName = null;

ApplicationProveedores.prototype.leftMenuItems = null;

ApplicationProveedores.prototype.ayuda = null;

ApplicationProveedores.prototype.ProveedoresListStore = null;

ApplicationProveedores.prototype.providers = null;

ApplicationProveedores.prototype.actualizaProveedor=null;

ApplicationProveedores.prototype.updateProviderForm= null;

ApplicationProveedores.prototype.record = null;

ApplicationProveedores.prototype.dockedItems = null;







ApplicationProveedores.prototype._init = function()
{

	//nombre de la aplicacion
	this.appName = "Proveedores";
	
	//ayuda sobre esta applicacion
	this.ayuda = "Ayuda sobre este modulo de prueba <br>, html es valido <br> :D";

	//submenues en el panel de la izquierda
	this._initToolBar();
	
	//panel principal
	this.mainCard = this.proveedoresWelcome;
	
};






ApplicationProveedores.prototype._initToolBar = function ()
{
	/*	
		Buscar
	*/
	var buscar = [{
		xtype: 'textfield',
		id:'ApplicationProveedores_searchField',
		inputCls: 'caja-buscar',
		listeners:
				{
					'render': function( ){
						//medio feo, pero bueno
						Ext.get("ApplicationProveedores_searchField").first().dom.setAttribute("onkeyup",
						 "ApplicationProveedores.currentInstance.mosaic.doSearch( this.value )");
					}
				}
		}];

		var agregar = [{
			xtype: 'button',
			text: 'Nuevo Proveedor',
			ui: 'action',
			handler: function(){
					console.log("agregar proveedor")
				}
			}];		

        this.dockedItems = [ new Ext.Toolbar({
            ui: 'dark',
            dock: 'bottom',
            items: buscar.concat({xtype:'spacer'}).concat(agregar)
        })];
    
	
	//agregar este dock a el panel principal
	this.proveedoresWelcome.addDocked( this.dockedItems );

};



ApplicationProveedores.prototype.provedores = [];




ApplicationProveedores.prototype.renderMosaico = function ()
{

	if(DEBUG){
		console.log("ApplicationProvedores: rendering " +this.provedores.length+ " items." );
	}

	var mItems = [];
	
	for(  a = 0; a < this.provedores.length; a++){
		mItems.push({
			image : 'media/truck.png',
			title : this.provedores[a].nombre,
			provedorId : this.provedores[a].id_proveedor,
			provedor : this.provedores[a],
			keywords : [ this.provedores[a].direccion, this.provedores[a].rfc ]
		});
	}
	
	
	ApplicationProveedores.currentInstance.mosaic = new Mosaico({
		renderTo : 'proveedores_mosaico',
		handler : function (item){
			ApplicationProveedores.currentInstance.doVerProvedor( item.provedor );
		},
		items: mItems
	});
	
};


ApplicationProveedores.prototype.getProvedores = function ()

{
	
	if(DEBUG){
		console.log( "ApplicationProvedores: refrescando lista de provedores"  );
	}
	
	POS.AJAXandDECODE({
		method: 'listarProveedores'
		},
		function (datos){
			ApplicationProveedores.currentInstance.provedores = datos.datos;
			ApplicationProveedores.currentInstance.renderMosaico();
		},
		function (){
			POS.aviso("Provedores","Algo anda mal con tu conexion.");	
		}
	);

};




ApplicationProveedores.prototype.proveedoresWelcome = new Ext.Panel({
		layout: 'card',
		html: '<div style="width:100%; height:100%" id="proveedores_mosaico"></div>',
		listeners : {
			'afterrender' : function (){
				ApplicationProveedores.currentInstance.getProvedores();
			}
			
		}
});
















ApplicationProveedores.prototype.doVerProvedor = function ( provedor )
{
	
	if(DEBUG){
		console.log("ApplicationProveedore: viendo proveedor " ,  provedor );
	}

	var newPanel = this.createPanelForProvedor( provedor );
	
	sink.Main.ui.setCard( newPanel, 'slide' );
	
};







ApplicationProveedores.prototype.renderProvedorDetalles = function ( provedor )
{
	var html = "";
	
	html += "<div class='nombre'>" 		+provedor.nombre 		+ "</div>";
	html += "<div class='direccion'>" 	+provedor.direccion 	+ "</div>";
	html += "<div class='mail'>" 		+provedor.e_mail		+ "</div>";
	html += "<div class='id_provedor'>" +provedor.id_proveedor	+ "</div>";
	html += "<div class='rfc'>"  		+provedor.rfc			+ "</div>";
	html += "<div class='telefono'>"  	+provedor.telefono		+ "</div>";


	return "<div class='ApplicationProveedores-Detalles'>"+html+"</div>";
	
};


ApplicationProveedores.prototype.createPanelForProvedor = function ( provedor )
{

	var carousel = new Ext.Carousel({

		defaults: { 
			cls: 'ApplicationProveedores-detallesProveedor'
		},
		items: [{
			html: this.renderProvedorDetalles(provedor)
		}, {
			title: 'Tab 2',
			html: '2'
		}, {
			title: 'Tab 3',
			html: '3'
		}]
	});


	var regresar = [{
			xtype: 'button',
			text: 'Regresar',
			ui: 'back',
			handler : function(){
				sink.Main.ui.setCard( 
					ApplicationProveedores.currentInstance.mainCard, {
						type: 'fade',
						duration: 500
					});
			}
		}];		


	var surtir = [{
			xtype: 'button',
			text: 'Surtir',
			ui: 'action',
			handler: function(){
				
			}
		}];
		
    var dockedItems = [ new Ext.Toolbar({
        ui: 'dark',
        dock: 'bottom',
        items: regresar.concat({xtype:'spacer'}).concat(surtir)
    })];


	var panel = new Ext.Panel({
		dockedItems : dockedItems,
		layout: {
			type: 'vbox',
			align: 'stretch'
		},
		defaults: {
	      flex: 1
		},
		items: [carousel]
	});
	

	
	return panel;
};





/*

Ext.regModel('Proveedor', {
    fields: ['nombre', 'rfc']
});


ProveedoresListStore = new Ext.data.Store({
    model: 'Proveedor',
    sorters: 'nombre',
    getGroupString : function(record) {
        return record.get('nombre')[0];
    }	
});

*/











/*

ApplicationProveedores.prototype.ProveedoresList = new Ext.Panel({
//	id: 'panelProveedores',
	layout: Ext.platform.isPhone ? 'fit' : {
        type: 'vbox',
        align: 'left',
        pack: 'center'
    },	
    listeners: {
		beforeshow : function(component){
						Ext.getBody().mask(false, '<div class="demos-loading">Loading&hellip;</div>');
						POS.AJAXandDECODE({
							method: 'listarProveedores'
							},
							function (datos){//mientras responda
								this.providers = datos.datos;
								ProveedoresListStore.loadData(this.providers);							
							},
							function (){//no responde
								POS.aviso("ERROR","NO SE PUDO CARGAR LA LISTA DE PROVEEDORES  ERROR EN LA CONEXION :(");	
							}
						);//AJAXandDECODE listarProveedores
						Ext.getBody().unmask();
		}//fin beforeshow 
	},
    items: [{
        width: '100%',
        height: '100%',
        xtype: 'list',
        store: ProveedoresListStore,
        tpl: '<tpl for="."><div class="proveedor"><strong>{nombre}</strong> {rfc}</div></tpl>',
        itemSelector: 'div.proveedor',
        singleSelect: true,
        grouped: true,
        indexBar: true,
		listeners: {
			selectionchange: function(){
			try{
			if (this.getSelectionCount() > 0) {
				this.record=this.getSelectedRecords();
				
				this.addDocked(updateProviderForm = new Ext.form.FormPanel({
					scroll: 'vertical',
					id: 'updateProviderForm',
					showAnimation: true,
					fullscreen: true,
					items: [{
							xtype: 'fieldset',
							title: 'Proveedor Info',
							instructions: 'Los campos que contienen * son obligatorios',
							items: [
									idProveedorM = new Ext.form.HiddenField({
										id: 'idProveedorM',
										value: this.record[0].id_proveedor
									}),
									nombreProveedorM = new Ext.form.TextField({
										id: 'nombreProveedorM',
										label: '*Nombre',
										value: this.record[0].nombre
									}),
									rfcProveedorM = new Ext.form.TextField({
										id: 'rfcProveedorM',
										label: '*RFC',
										value: this.record[0].rfc
									}),
									direccionProveedorM = new Ext.form.TextField({
										id: 'direccionProveedorM',
										label: '*Direccion',
										value: this.record[0].direccion										
									}),
									emailProveedorM = new Ext.form.TextField({
										id: 'emailProveedorM',
										label: 'E-mail',
										value: this.record[0].e_mail
									}),
									telefonoProveedorM = new Ext.form.TextField({
										id: 'telefonoProveedorM',
										label: 'Telefono',
										value: this.record[0].telefono
									})
							]//fin items formPanel
							
						},
							{
								xtype: 'button',
								id: 'updateProveedor',
								text: 'Modificar',
								maxWidth:150,
								handler: function(event,button) {
										if( nombreProveedorM.getValue() =='' || rfcProveedorM.getValue() =='' || direccionProveedorM.getValue() ==''){
										POS.aviso("ERROR!!","LLENAR ALMENOS LOS CAMPOS CON  *");					
										}else{
											Ext.getBody().mask(false, '<div class="demos-loading">Loading&hellip;</div>');
											POS.AJAXandDECODE({
											method: 'actualizarProveedor',
											idP: idProveedorM.getValue(),
											rfcP: rfcProveedorM.getValue(),
											nombreP: nombreProveedorM.getValue(),
											direccionP: direccionProveedorM.getValue(),
											telefonoP: telefonoProveedorM.getValue(),
											e_mailP: emailProveedorM.getValue()
											},
											function (datos){//mientras responda
												if(datos.success == true){//
													POS.aviso("MODIFICACION","LOS DATOS DEL PROVEEDOR FUERON CAMBIADOS  :)");						

													POS.AJAXandDECODE({
														method: 'listarProveedores'
														},
														function (datos){//mientras responda
															this.providers = datos.datos;
															ProveedoresListStore.loadData(this.providers); 
														},
														function (){//no responde	
															POS.aviso("ERROR!!","NO SE PUDO CARGAR LA LISTA DE PROVEEDORES ERROR EN LA CONEXION :(");	
														}
													);//AJAXandDECODE refrescar lista proveedores
												}else{
													POS.aviso("ERROR!!","LOS DATOS DEL PROVEEDOR NO SE MODIFICARON :(");
												}
												rfcProveedorM.setValue('');
												nombreProveedorM.setValue('');
												direccionProveedorM.setValue('');
												telefonoProveedor.setValue('');
												emailProveedorM.setValue('');
												
												this.updateProviderForm.destroy();
												
											},
											function (){//no responde	
												POS.aviso("ERROR!!","NO SE PUDO MODIFICAR PROVEEDOR ERROR EN LA CONEXION :(");	
											}
										);//AJAXandDECODE actualizar
										Ext.getBody().unmask();
									}//else de validar vacios
								}//fin handler boton modificar
							}//fin boton updateProveedor
							,
							{
								xtype: 'button',
								id: 'cancelUpdateProveedor',
								text: 'Cancelar',
								maxWidth:150,
								handler: function(event,button) {
									Ext.getCmp('updateProviderForm').destroy();
								}
							}//fin boton Cancelar							
						]//,//fin items formpanel
					}),'flip');
				}//if selectedCount
				}catch(e){
					if(DEBUG){
						console.log("BLOQUE TRY CATCH DE LISTENER SELECTIONCHANGE EN PANEL PROVEEDORES LIST ERROR ------------> "+e);
					}
				}
			}//,
			}//fin listener selection change
    }
]//fin items proveedoresList panel
	
});
*/


/*

ApplicationProveedores.prototype.agregarProveedor = function (){
	Ext.getCmp('btn_agregarProveedor').setVisible(false);
	if(DEBUG){
		console.log("ApplicacionProveedores: agregarProveedor called....");
	}
	ApplicationProveedores.currentInstance.ProveedoresList.addDocked(formAgregarProveedor = new Ext.form.FormPanel({
    scroll: 'vertical',
	id:'formAgregarProveedor',
	fullscreen:true,
    items: [{
		
        xtype: 'fieldset',
        title: 'Proveedor Info',
        instructions: 'Los campos que contienen * son obligatorios',
        items: [
				nombreProveedor = new Ext.form.TextField({
					id: 'nombreProveedor',
					label: '*Nombre'
				})
        ,
				rfcProveedor = new Ext.form.TextField({
					id: 'rfcProveedor',
					label: '*RFC'
				})
        ,
				direccionProveedor = new Ext.form.TextField({
					id: 'direccionProveedor',
					label: '*Direccion'
				})
        ,
				emailProveedor = new Ext.form.TextField({
					id: 'emailProveedor',
					label: 'E-mail'
				})
        ,
				telefonoProveedor = new Ext.form.TextField({
					id: 'telefonoProveedor',
					label: 'Telefono'
				})
       
		]//fin items form
		
    },
		{
            xtype: 'button',
            id: 'guardar',
            text: 'Guardar',
			maxWidth:100,
			handler: function(event,button) {
					
				if( nombreProveedor.getValue() =='' || rfcProveedor.getValue() =='' || direccionProveedor.getValue() ==''){
					Ext.getBody().mask(false, '<div class="demos-loading">Loading&hellip;</div>');
					POS.aviso("ERROR!!","LLENAR ALMENOS LOS CAMPOS CON  *");					
					}else{
						POS.AJAXandDECODE({
						method: 'insertarProveedor',
						rfcP: rfcProveedor.getValue(),
						nombreP: nombreProveedor.getValue(),
						direccionP: direccionProveedor.getValue(),
						telefonoP: telefonoProveedor.getValue(),
						e_mailP: emailProveedor.getValue()
						},
						function (datos){//mientras responda
							if(datos.success == true){//
								POS.aviso("NUEVO CLIENTE","LOS DATOS DEL PROVEEDOR FUERON GUARDADOS CORRECTAMENTE :)");						
								POS.AJAXandDECODE({
								method: 'listarProveedores'
								},
								function (datos){//mientras responda
									this.customers = datos.datos;
									ProveedoresListStore.loadData(this.providers); 
								},
								function (){//no responde	
									POS.aviso("ERROR!!","NO SE PUDO CARGAR LA LISTA DE PROVEEDORES ERROR EN LA CONEXION :(");	
								}
								);//AJAXandDECODE refrescar lista clientes
							}else{
								POS.aviso("ERROR!!","LOS DATOS DEL 	CLIENTE NO SE MODIFICARON :(");
							}
							rfcProveedor.setValue('');
							nombreProveedor.setValue('');
							direccionProveedor.setValue('');
							telefonoProveedor.setValue('');
							emailProveedor.setValue('');
						},
						function (){//no responde	
							POS.aviso("ERROR","NO SE PUDO INSERTAR PROVEEDOR, ERROR EN LA CONEXION :(");	
						}
					);//AJAXandDECODE insertar proveedor
					Ext.getBody().unmask();
					Ext.getCmp('formAgregarProveedor').destroy();
					Ext.getCmp('btn_agregarProveedor').setVisible(true);
					}//else de validar vacios
				}//fin handler	
        }//fin boton guardar
		,
		{
			xtype: 'button',
			id: 'cancelarGuardarProveedor',
			text: 'Cancelar',
			maxWidth:100,
			handler: function(event,button) {
				Ext.getCmp('formAgregarProveedor').destroy();
				Ext.getCmp('btn_agregarProveedor').setVisible(true);
				
			}//fin handler cancelar cliente
			
		}//fin boton cancelar
	]//,//fin items formpanel
	})//fin menu2
);
};


*/



//autoinstalar esta applicacion
AppInstaller( new ApplicationProveedores() );
