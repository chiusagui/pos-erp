<?php

/*  ****************************************************
	*	Detalles de la instancia
	**************************************************** */
	$rs = $core_conn->Execute("SELECT * FROM instances where instance_id = ". stripslashes( $_REQUEST["iid"] ) .";");

	if(count($rs)==0){
		die ("Esta instancia no existe !");
	}

	$results = $rs->GetArray();


/*  ****************************************************
	*	Vaciar la BD a valores default
	**************************************************** */
	if(isset( $_GET["reset_bd"] ))
	{
		Logger::log("Reseteando BD !!!");
		
		$link = @mysql_connect($results[0]["DB_HOST"],$results[0]["DB_USER"],$results[0]["DB_PASSWORD"], $results[0]["DB_NAME"]);	
		
		/*
		 * -- Adminer 3.3.1 MySQL dump

		SET NAMES utf8;
		SET foreign_key_checks = 0;
		SET time_zone = 'SYSTEM';
		SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

		CREATE TABLE `actualizacion_de_precio` (
		  `id_actualizacion` int(12) NOT NULL AUTO_INCREMENT COMMENT 'id de actualizacion de precio',
		  `id_producto` int(11) NOT NULL,
		  `id_usuario` int(11) NOT NULL,
		  `precio_venta` float NOT NULL,
		  `precio_venta_procesado` float NOT NULL,
		  `precio_intersucursal` float NOT NULL,
		  `precio_intersucursal_procesado` float NOT NULL,
		  `precio_compra` float NOT NULL DEFAULT '0' COMMENT 'precio al que se le compra al publico este producto en caso de ser POS_COMPRA_A_CLIENTES',
		  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id_actualizacion`),
		  KEY `id_producto` (`id_producto`),
		  KEY `id_usuario` (`id_usuario`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Actualizaciones de precios';


		CREATE TABLE `autorizacion` (
		  `id_autorizacion` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `id_usuario` int(11) NOT NULL COMMENT 'Usuario que solicito esta autorizacion',
		  `id_sucursal` int(11) NOT NULL COMMENT 'Sucursal de donde proviene esta autorizacion',
		  `fecha_peticion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha cuando se genero esta autorizacion',
		  `fecha_respuesta` timestamp NULL DEFAULT NULL COMMENT 'Fecha de cuando se resolvio esta aclaracion',
		  `estado` int(11) NOT NULL COMMENT 'Estado actual de esta aclaracion',
		  `parametros` varchar(2048) NOT NULL COMMENT 'Parametros en formato JSON que describen esta autorizacion',
		  `tipo` enum('envioDeProductosASucursal','solicitudDeProductos','solicitudDeMerma','solicitudDeCambioPrecio','solicitudDeDevolucion','solicitudDeCambioLimiteDeCredito','solicitudDeGasto') NOT NULL COMMENT 'El tipo de autorizacion',
		  PRIMARY KEY (`id_autorizacion`),
		  KEY `id_usuario` (`id_usuario`),
		  KEY `id_sucursal` (`id_sucursal`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		CREATE TABLE `cliente` (
		  `id_cliente` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identificador del cliente',
		  `rfc` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'rfc del cliente si es que tiene',
		  `razon_social` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'razon social del cliente',
		  `calle` varchar(300) COLLATE utf8_unicode_ci NOT NULL COMMENT 'calle del domicilio fiscal del cliente',
		  `numero_exterior` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'numero exteriror del domicilio fiscal del cliente',
		  `numero_interior` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'numero interior del domicilio fiscal del cliente',
		  `colonia` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'colonia del domicilio fiscal del cliente',
		  `referencia` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'referencia del domicilio fiscal del cliente',
		  `localidad` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Localidad del domicilio fiscal',
		  `municipio` varchar(55) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Municipio de este cliente',
		  `estado` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Estado del domicilio fiscal del cliente',
		  `pais` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Pais del domicilio fiscal del cliente',
		  `codigo_postal` varchar(15) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Codigo postal del domicilio fiscal del cliente',
		  `telefono` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Telefono del cliete',
		  `e_mail` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'dias de credito para que pague el cliente',
		  `limite_credito` float NOT NULL DEFAULT '0' COMMENT 'Limite de credito otorgado al cliente',
		  `descuento` float NOT NULL DEFAULT '0' COMMENT 'Taza porcentual de descuento de 0.0 a 100.0',
		  `activo` tinyint(2) NOT NULL DEFAULT '1' COMMENT 'Indica si la cuenta esta activada o desactivada',
		  `id_usuario` int(11) NOT NULL COMMENT 'Identificador del usuario que dio de alta a este cliente',
		  `id_sucursal` int(11) NOT NULL COMMENT 'Identificador de la sucursal donde se dio de alta este cliente',
		  `fecha_ingreso` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha cuando este cliente se registro en una sucursal',
		  `password` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'el pass para que este cliente entre a descargar sus facturas',
		  `last_login` timestamp NULL DEFAULT NULL,
		  `grant_changes` tinyint(1) DEFAULT '0' COMMENT 'verdadero cuando el cliente ha cambiado su contrasena y puede hacer cosas',
		  PRIMARY KEY (`id_cliente`),
		  KEY `id_usuario` (`id_usuario`),
		  KEY `id_sucursal` (`id_sucursal`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `compra_cliente` (
		  `id_compra` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id de compra',
		  `id_cliente` int(11) NOT NULL COMMENT 'cliente al que se le compro',
		  `tipo_compra` enum('credito','contado') COLLATE utf8_unicode_ci NOT NULL COMMENT 'tipo de compra, contado o credito',
		  `tipo_pago` enum('efectivo','cheque','tarjeta') COLLATE utf8_unicode_ci DEFAULT 'efectivo' COMMENT 'tipo de pago para esta compra en caso de ser a contado',
		  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'fecha de compra',
		  `subtotal` float DEFAULT NULL COMMENT 'subtotal de la compra, puede ser nulo',
		  `impuesto` float DEFAULT '0' COMMENT 'impuesto agregado por la compra, depende de cada sucursal',
		  `descuento` float NOT NULL DEFAULT '0' COMMENT 'descuento aplicado a esta compra',
		  `total` float NOT NULL DEFAULT '0' COMMENT 'total de esta compra',
		  `id_sucursal` int(11) NOT NULL COMMENT 'sucursal de la compra',
		  `id_usuario` int(11) NOT NULL COMMENT 'empleado que lo vendio',
		  `pagado` float NOT NULL DEFAULT '0' COMMENT 'porcentaje pagado de esta compra',
		  `cancelada` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'verdadero si esta compra ha sido cancelada, falso si no',
		  `ip` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip de donde provino esta compra',
		  `liquidada` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Verdadero si esta compra ha sido liquidada, falso si hay un saldo pendiente',
		  PRIMARY KEY (`id_compra`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `compra_proveedor` (
		  `id_compra_proveedor` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identificador de la compra',
		  `peso_origen` float NOT NULL,
		  `id_proveedor` int(11) NOT NULL COMMENT 'id del proveedor a quien se le hizo esta compra',
		  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'fecha de cuando se recibio el embarque',
		  `fecha_origen` date NOT NULL COMMENT 'fecha de cuando se envio este embarque',
		  `folio` varchar(11) CHARACTER SET latin1 COLLATE latin1_general_cs DEFAULT NULL COMMENT 'folio de la remision',
		  `numero_de_viaje` varchar(11) CHARACTER SET latin1 COLLATE latin1_general_cs DEFAULT NULL COMMENT 'numero de viaje',
		  `peso_recibido` float NOT NULL COMMENT 'peso en kilogramos reportado en la remision',
		  `arpillas` float NOT NULL COMMENT 'numero de arpillas en el camion',
		  `peso_por_arpilla` float NOT NULL COMMENT 'peso promedio en kilogramos por arpilla',
		  `productor` varchar(64) NOT NULL,
		  `calidad` tinyint(3) unsigned DEFAULT NULL COMMENT 'Describe la calidad del producto asignando una calificacion en eel rango de 0-100',
		  `merma_por_arpilla` float NOT NULL,
		  `total_origen` float DEFAULT NULL COMMENT 'Es lo que vale el embarque segun el proveedor',
		  PRIMARY KEY (`id_compra_proveedor`),
		  KEY `id_proveedor` (`id_proveedor`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		CREATE TABLE `compra_proveedor_flete` (
		  `id_compra_proveedor` int(11) NOT NULL,
		  `chofer` varchar(64) NOT NULL,
		  `marca_camion` varchar(64) DEFAULT NULL,
		  `placas_camion` varchar(64) NOT NULL,
		  `modelo_camion` varchar(64) DEFAULT NULL,
		  `costo_flete` float NOT NULL,
		  PRIMARY KEY (`id_compra_proveedor`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		CREATE TABLE `compra_proveedor_fragmentacion` (
		  `id_fragmentacion` int(11) NOT NULL AUTO_INCREMENT,
		  `id_compra_proveedor` int(11) NOT NULL COMMENT 'La compra a proveedor del producto',
		  `id_producto` int(11) NOT NULL COMMENT 'El id del producto',
		  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'la fecha de esta operacion',
		  `descripcion` varchar(16) NOT NULL COMMENT 'la descripcion de lo que ha sucedido, vendido, surtido, basura... etc.',
		  `cantidad` double NOT NULL DEFAULT '0' COMMENT 'cuanto fue consumido o agregado !!! en la escala que se tiene de este prod',
		  `procesada` tinyint(1) NOT NULL COMMENT 'si estamos hablando de producto procesado, debera ser true',
		  `precio` int(11) NOT NULL COMMENT 'a cuanto se vendio esta porcion del producto, si es el resultado de algun proceso sera 0 por ejemplo',
		  `descripcion_ref_id` int(11) DEFAULT NULL COMMENT 'si se refiere a una venta, se puede poner el id de esa venta, si fue de surtir, el id de la compra, etc..',
		  PRIMARY KEY (`id_fragmentacion`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;


		CREATE TABLE `compra_sucursal` (
		  `id_compra` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id de la compra',
		  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'fecha de compra',
		  `subtotal` float NOT NULL COMMENT 'subtotal de compra',
		  `id_sucursal` int(11) NOT NULL COMMENT 'sucursal en que se compro',
		  `id_usuario` int(11) NOT NULL COMMENT 'quien realizo la compra',
		  `id_proveedor` int(11) DEFAULT NULL COMMENT 'En caso de ser una compra a un proveedor externo, contendra el id de ese proveedor, en caso de ser una compra a centro de distribucion este campo sera null.',
		  `pagado` float NOT NULL DEFAULT '0' COMMENT 'total de pago abonado a esta compra',
		  `liquidado` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'indica si la cuenta ha sido liquidada o no',
		  `total` float NOT NULL,
		  PRIMARY KEY (`id_compra`),
		  KEY `compras_sucursal` (`id_sucursal`),
		  KEY `compras_usuario` (`id_usuario`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `corte` (
		  `id_corte` int(12) NOT NULL AUTO_INCREMENT COMMENT 'identificador del corte',
		  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'fecha de este corte',
		  `id_sucursal` int(12) NOT NULL COMMENT 'sucursal a la que se realizo este corte',
		  `total_ventas` float NOT NULL COMMENT 'total de activo realizado en ventas para esta sucursal incluyendo ventas a credito y ventas a contado aunque no esten saldadas',
		  `total_ventas_abonado` float NOT NULL COMMENT 'total de efectivo adquirido gracias a ventas, incluye ventas a contado y los abonos de las ventas a credito',
		  `total_ventas_saldo` float NOT NULL COMMENT 'total de dinero que se le debe a esta sucursal por ventas a credito',
		  `total_compras` float NOT NULL COMMENT 'total de gastado en compras',
		  `total_compras_abonado` float NOT NULL COMMENT 'total de abonado en compras',
		  `total_gastos` float NOT NULL COMMENT 'total de gastos con saldo o sin salgo',
		  `total_gastos_abonado` float NOT NULL COMMENT 'total de gastos pagados ya',
		  `total_ingresos` float NOT NULL COMMENT 'total de ingresos para esta sucursal desde el ultimo corte',
		  `total_ganancia_neta` float NOT NULL COMMENT 'calculo de ganancia neta',
		  PRIMARY KEY (`id_corte`),
		  KEY `id_sucursal` (`id_sucursal`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		CREATE TABLE `detalle_compra_cliente` (
		  `id_compra` int(11) NOT NULL COMMENT 'compra a que se referencia',
		  `id_producto` int(11) NOT NULL COMMENT 'producto de la compra',
		  `cantidad` float NOT NULL COMMENT 'cantidad que se compro',
		  `precio` float NOT NULL COMMENT 'precio al que se compro',
		  `descuento` float unsigned DEFAULT '0' COMMENT 'indica cuanto producto original se va a descontar de ese producto en esa compra',
		  PRIMARY KEY (`id_compra`,`id_producto`),
		  KEY `detalle_compra_producto` (`id_producto`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `detalle_compra_proveedor` (
		  `id_compra_proveedor` int(11) NOT NULL,
		  `id_producto` int(11) NOT NULL,
		  `variedad` varchar(64) NOT NULL,
		  `arpillas` int(11) NOT NULL,
		  `kg` int(11) NOT NULL,
		  `precio_por_kg` float NOT NULL,
		  PRIMARY KEY (`id_compra_proveedor`,`id_producto`),
		  KEY `id_producto` (`id_producto`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		CREATE TABLE `detalle_compra_sucursal` (
		  `id_detalle_compra_sucursal` int(11) NOT NULL AUTO_INCREMENT,
		  `id_compra` int(11) NOT NULL COMMENT 'id de la compra',
		  `id_producto` int(11) NOT NULL COMMENT 'id del producto',
		  `cantidad` float NOT NULL COMMENT 'cantidad comprada',
		  `precio` float NOT NULL COMMENT 'costo de compra',
		  `descuento` int(11) NOT NULL,
		  `procesadas` tinyint(1) NOT NULL COMMENT 'verdadero si este detalle se refiere a compras procesadas (limpias)',
		  PRIMARY KEY (`id_detalle_compra_sucursal`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `detalle_inventario` (
		  `id_producto` int(11) NOT NULL COMMENT 'identificador del proudcto en inventario',
		  `id_sucursal` int(11) NOT NULL COMMENT 'id de la sucursal',
		  `precio_venta` float NOT NULL COMMENT 'precio al que se vendera al publico',
		  `precio_venta_procesado` float NOT NULL DEFAULT '0' COMMENT 'cuando este producto tiene tratamiento este sera su precio de venta al estar procesado',
		  `existencias` float NOT NULL DEFAULT '0' COMMENT 'cantidad de producto que hay actualmente en almacen de esta sucursal (originales+procesadas)',
		  `existencias_procesadas` float NOT NULL COMMENT 'cantidad de producto solamente procesado !',
		  `precio_compra` float NOT NULL DEFAULT '0' COMMENT 'El precio de compra para este producto en esta sucursal, siempre y cuando este punto de venta tenga el modulo POS_COMPRA_A_CLIENTES',
		  PRIMARY KEY (`id_producto`,`id_sucursal`),
		  KEY `id_sucursal` (`id_sucursal`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		CREATE TABLE `detalle_venta` (
		  `id_venta` int(11) NOT NULL COMMENT 'venta a que se referencia',
		  `id_producto` int(11) NOT NULL COMMENT 'producto de la venta',
		  `cantidad` float NOT NULL COMMENT 'cantidad que se vendio',
		  `cantidad_procesada` float NOT NULL,
		  `precio` float NOT NULL COMMENT 'precio al que se vendio',
		  `precio_procesada` float NOT NULL COMMENT 'el precio de los articulos procesados en esta venta',
		  `descuento` float unsigned DEFAULT '0' COMMENT 'indica cuanto producto original se va a descontar de ese producto en esa venta',
		  PRIMARY KEY (`id_venta`,`id_producto`),
		  KEY `detalle_venta_producto` (`id_producto`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `documento` (
		  `id_documento` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id del documento',
		  `numero_de_impresiones` int(11) NOT NULL COMMENT 'numero de veces que se tiene que imprmir este documento',
		  `identificador` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'identificador con el cual se le conocera en el sistema',
		  `descripcion` varchar(256) COLLATE utf8_unicode_ci NOT NULL COMMENT 'descripcion breve del documento',
		  PRIMARY KEY (`id_documento`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contiene informaci?n acerca todos de los documentos que se i';


		CREATE TABLE `equipo` (
		  `id_equipo` int(6) NOT NULL AUTO_INCREMENT COMMENT 'el identificador de este equipo',
		  `token` varchar(128) DEFAULT NULL COMMENT 'el token de seguridad que identifica a este equipo unicamente, representado generalmente por un user-agent modificado',
		  `full_ua` varchar(256) NOT NULL COMMENT 'String de user-agent para este cliente',
		  `descripcion` varchar(254) NOT NULL COMMENT 'descripcion de este equipo',
		  `locked` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'si este equipo esta lockeado para prevenir los cambios',
		  PRIMARY KEY (`id_equipo`),
		  UNIQUE KEY `full_ua` (`full_ua`),
		  UNIQUE KEY `token` (`token`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		CREATE TABLE `equipo_sucursal` (
		  `id_equipo` int(6) NOT NULL COMMENT 'identificador del equipo ',
		  `id_sucursal` int(6) NOT NULL COMMENT 'identifica una sucursal',
		  PRIMARY KEY (`id_equipo`),
		  KEY `id_sucursal` (`id_sucursal`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		CREATE TABLE `factura_compra` (
		  `folio` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
		  `id_compra` int(11) NOT NULL COMMENT 'COMPRA A LA QUE CORRESPONDE LA FACTURA',
		  PRIMARY KEY (`folio`),
		  KEY `factura_compra_compra` (`id_compra`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `factura_venta` (
		  `id_folio` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'folio que tiene la factura',
		  `id_venta` int(11) NOT NULL COMMENT 'venta a la cual corresponde la factura',
		  `id_usuario` int(10) NOT NULL COMMENT 'Id del usuario que hiso al ultima modificacion a la factura',
		  `xml` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'xml en bruto',
		  `lugar_emision` int(11) NOT NULL COMMENT 'id de la sucursal donde se emitio la factura',
		  `tipo_comprobante` enum('ingreso','egreso') COLLATE utf8_unicode_ci NOT NULL,
		  `activa` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 indica que la factura fue emitida y esta activa, 0 que la factura fue emitida y posteriormente fue cancelada',
		  `sellada` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica si el WS ha timbrado la factura',
		  `forma_pago` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		  `fecha_emision` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `version_tfd` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
		  `folio_fiscal` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
		  `fecha_certificacion` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `numero_certificado_sat` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
		  `sello_digital_emisor` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
		  `sello_digital_sat` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
		  `cadena_original` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
		  PRIMARY KEY (`id_folio`),
		  KEY `factura_venta_venta` (`id_venta`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `gastos` (
		  `id_gasto` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id para identificar el gasto',
		  `folio` varchar(22) NOT NULL COMMENT 'El folio de la factura para este gasto',
		  `concepto` varchar(100) NOT NULL COMMENT 'concepto en lo que se gasto',
		  `monto` float unsigned NOT NULL COMMENT 'lo que costo este gasto',
		  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'fecha del gasto',
		  `fecha_ingreso` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Fecha que selecciono el empleado en el sistema',
		  `id_sucursal` int(11) NOT NULL COMMENT 'sucursal en la que se hizo el gasto',
		  `id_usuario` int(11) NOT NULL COMMENT 'usuario que registro el gasto',
		  `nota` varchar(512) NOT NULL COMMENT 'nota adicional para complementar la descripcion del gasto',
		  PRIMARY KEY (`id_gasto`),
		  KEY `id_sucursal` (`id_sucursal`),
		  KEY `id_usuario` (`id_usuario`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		CREATE TABLE `grupos` (
		  `id_grupo` int(11) NOT NULL,
		  `nombre` varchar(45) NOT NULL COMMENT 'Nombre del Grupo',
		  `descripcion` varchar(256) NOT NULL,
		  PRIMARY KEY (`id_grupo`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		INSERT INTO `grupos` (`id_grupo`, `nombre`, `descripcion`) VALUES
		(0,	'Ingeniero',	'Ingeniero'),
		(1,	'Administrador',	'Administrador'),
		(2,	'Gerente',	'Gerente'),
		(3,	'Cajero',	'Cajero');

		CREATE TABLE `grupos_usuarios` (
		  `id_grupo` int(11) NOT NULL,
		  `id_usuario` int(11) NOT NULL,
		  PRIMARY KEY (`id_usuario`),
		  KEY `fk_grupos_usuarios_1` (`id_grupo`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		INSERT INTO `grupos_usuarios` (`id_grupo`, `id_usuario`) VALUES
		(0,	77);

		CREATE TABLE `impresiones` (
		  `id_impresora` int(11) NOT NULL COMMENT 'id de la impresora',
		  `id_documento` int(11) NOT NULL COMMENT 'id del documento',
		  PRIMARY KEY (`id_impresora`,`id_documento`),
		  KEY `id_documento` (`id_documento`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='contiene la relaci?n impresora-documento-sucursal';


		CREATE TABLE `impresora` (
		  `id_impresora` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id de la impresora',
		  `id_sucursal` int(11) NOT NULL COMMENT 'id de la sucursal donde se encuentra esta impresora',
		  `descripcion` varchar(256) COLLATE utf8_unicode_ci NOT NULL COMMENT 'descripcion breve de la impresora',
		  `identificador` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'es el nombre de como esta dada de alta la impresora en la sucursal',
		  PRIMARY KEY (`id_impresora`),
		  KEY `id_sucursal` (`id_sucursal`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contiene informaci?n acerca de todas las impresoras de todas';


		CREATE TABLE `ingresos` (
		  `id_ingreso` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id para identificar el ingreso',
		  `concepto` varchar(100) NOT NULL COMMENT 'concepto en lo que se ingreso',
		  `monto` float NOT NULL COMMENT 'lo que costo este ingreso',
		  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'fecha del ingreso',
		  `fecha_ingreso` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Fecha que selecciono el empleado en el sistema',
		  `id_sucursal` int(11) NOT NULL COMMENT 'sucursal en la que se hizo el ingreso',
		  `id_usuario` int(11) NOT NULL COMMENT 'usuario que registro el ingreso',
		  `nota` varchar(512) NOT NULL COMMENT 'nota adicional para complementar la descripcion del ingreso',
		  PRIMARY KEY (`id_ingreso`),
		  KEY `usuario_ingreso` (`id_usuario`),
		  KEY `sucursal_ingreso` (`id_sucursal`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		CREATE TABLE `inventario` (
		  `id_producto` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id del producto',
		  `descripcion` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'descripcion del producto',
		  `escala` enum('kilogramo','pieza','litro','unidad') COLLATE utf8_unicode_ci NOT NULL,
		  `tratamiento` enum('limpia') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Tipo de tratatiento si es que existe para este producto.',
		  `agrupacion` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'La agrupacion de este producto',
		  `agrupacionTam` float DEFAULT NULL COMMENT 'El tamano de cada agrupacion',
		  `activo` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'si este producto esta activo o no en el sistema',
		  `precio_por_agrupacion` tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id_producto`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `inventario_maestro` (
		  `id_producto` int(11) NOT NULL,
		  `id_compra_proveedor` int(11) NOT NULL,
		  `existencias` float NOT NULL,
		  `existencias_procesadas` float NOT NULL,
		  `sitio_descarga` int(11) NOT NULL,
		  PRIMARY KEY (`id_producto`,`id_compra_proveedor`),
		  KEY `id_compra_proveedor` (`id_compra_proveedor`),
		  KEY `sitio_descarga` (`sitio_descarga`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		CREATE TABLE `pago_prestamo_sucursal` (
		  `id_pago` int(11) NOT NULL AUTO_INCREMENT COMMENT 'El identificador de este pago',
		  `id_prestamo` int(11) NOT NULL COMMENT 'El id del prestamo al que pertenece este prestamo',
		  `id_usuario` int(11) NOT NULL COMMENT 'El usurio que recibe este dinero',
		  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'La fecha cuando se realizo este pago',
		  `monto` float NOT NULL COMMENT 'El monto a abonar',
		  PRIMARY KEY (`id_pago`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;


		CREATE TABLE `pagos_compra` (
		  `id_pago` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identificador del pago',
		  `id_compra` int(11) NOT NULL COMMENT 'identificador de la compra a la que pagamos',
		  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'fecha en que se abono',
		  `monto` float NOT NULL COMMENT 'monto que se abono',
		  PRIMARY KEY (`id_pago`),
		  KEY `pagos_compra_compra` (`id_compra`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		CREATE TABLE `pagos_venta` (
		  `id_pago` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id de pago del cliente',
		  `id_venta` int(11) NOT NULL COMMENT 'id de la venta a la que se esta pagando',
		  `id_sucursal` int(11) NOT NULL COMMENT 'Donde se realizo el pago',
		  `id_usuario` int(11) NOT NULL COMMENT 'Quien cobro este pago',
		  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha en que se registro el pago',
		  `monto` float NOT NULL COMMENT 'total de credito del cliente',
		  `tipo_pago` enum('efectivo','cheque','tarjeta') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'efectivo' COMMENT 'tipo de pago para este abono',
		  PRIMARY KEY (`id_pago`),
		  KEY `pagos_venta_venta` (`id_venta`),
		  KEY `id_sucursal` (`id_sucursal`),
		  KEY `id_usuario` (`id_usuario`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `pos_config` (
		  `opcion` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		  `value` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
		  PRIMARY KEY (`opcion`),
		  UNIQUE KEY `opcion` (`opcion`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `prestamo_sucursal` (
		  `id_prestamo` int(11) NOT NULL AUTO_INCREMENT COMMENT 'El identificador de este prestamo',
		  `prestamista` int(11) NOT NULL COMMENT 'La sucursal que esta prestando',
		  `deudor` int(11) NOT NULL COMMENT 'La sucursal que esta recibiendo',
		  `monto` float NOT NULL COMMENT 'El monto prestado',
		  `saldo` float NOT NULL COMMENT 'El saldo pendiente para liquidar',
		  `liquidado` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Bandera para buscar rapidamente los prestamos que aun no estan saldados',
		  `concepto` varchar(256) NOT NULL COMMENT 'El concepto de este prestamo',
		  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'fecha en la que se registro el gasto',
		  PRIMARY KEY (`id_prestamo`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;


		CREATE TABLE `proveedor` (
		  `id_proveedor` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identificador del proveedor',
		  `rfc` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'rfc del proveedor',
		  `nombre` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'nombre del proveedor',
		  `direccion` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'direccion del proveedor',
		  `telefono` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'telefono',
		  `e_mail` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'email del provedor',
		  `activo` tinyint(2) NOT NULL DEFAULT '1' COMMENT 'Indica si la cuenta esta activada o desactivada',
		  `tipo_proveedor` enum('admin','sucursal','ambos') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'admin' COMMENT 'si este proveedor surtira al admin, a las sucursales o a ambos',
		  PRIMARY KEY (`id_proveedor`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `sucursal` (
		  `id_sucursal` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de cada sucursal',
		  `gerente` int(11) DEFAULT NULL COMMENT 'Gerente de esta sucursal',
		  `descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'nombre o descripcion de sucursal',
		  `razon_social` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'razon social de la sucursal',
		  `rfc` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'El RFC de la sucursal',
		  `calle` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'calle del domicilio fiscal',
		  `numero_exterior` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'nuemro exterior del domicilio fiscal',
		  `numero_interior` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'numero interior del domicilio fiscal',
		  `colonia` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'colonia del domicilio fiscal',
		  `localidad` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'localidad del domicilio fiscal',
		  `referencia` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'referencia del domicilio fiscal',
		  `municipio` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'municipio del domicilio fiscal',
		  `estado` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'estado del domicilio fiscal',
		  `pais` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'pais del domicilio fiscal',
		  `codigo_postal` varchar(15) COLLATE utf8_unicode_ci NOT NULL COMMENT 'codigo postal del domicilio fiscal',
		  `telefono` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'El telefono de la sucursal',
		  `token` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Token de seguridad para esta sucursal',
		  `letras_factura` char(1) COLLATE utf8_unicode_ci NOT NULL,
		  `activo` tinyint(1) NOT NULL DEFAULT '1',
		  `fecha_apertura` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de apertura de esta sucursal',
		  `saldo_a_favor` float NOT NULL DEFAULT '0' COMMENT 'es el saldo a favor que tiene la sucursal encuanto a los abonos de sus compras',
		  PRIMARY KEY (`id_sucursal`),
		  UNIQUE KEY `letras_factura` (`letras_factura`),
		  KEY `gerente` (`gerente`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		CREATE TABLE `usuario` (
		  `id_usuario` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identificador del usuario',
		  `RFC` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT 'RFC de este usuario',
		  `nombre` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'nombre del empleado',
		  `contrasena` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
		  `id_sucursal` int(11) DEFAULT NULL COMMENT 'Id de la sucursal a que pertenece',
		  `activo` tinyint(1) NOT NULL COMMENT 'Guarda el estado de la cuenta del usuario',
		  `finger_token` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Una cadena que sera comparada con el token que mande el scanner de huella digital',
		  `salario` float NOT NULL COMMENT 'Salario actual',
		  `direccion` varchar(512) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Direccion del empleado',
		  `telefono` varchar(16) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Telefono del empleado',
		  `fecha_inicio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha cuando este usuario comenzo a laborar',
		  PRIMARY KEY (`id_usuario`),
		  KEY `fk_usuario_1` (`id_sucursal`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

		INSERT INTO `usuario` (`id_usuario`, `RFC`, `nombre`, `contrasena`, `id_sucursal`, `activo`, `finger_token`, `salario`, `direccion`, `telefono`, `fecha_inicio`) VALUES
		(77,	'',	'Alan Gonzalez',	'202cb962ac59075b964b07152d234b70',	NULL,	1,	NULL,	0,	'',	'',	'2011-08-04 14:14:27');

		CREATE TABLE `ventas` (
		  `id_venta` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id de venta',
		  `id_venta_equipo` int(11) DEFAULT NULL,
		  `id_equipo` int(11) DEFAULT NULL,
		  `id_cliente` int(11) NOT NULL COMMENT 'cliente al que se le vendio',
		  `tipo_venta` enum('credito','contado') COLLATE utf8_unicode_ci NOT NULL COMMENT 'tipo de venta, contado o credito',
		  `tipo_pago` enum('efectivo','cheque','tarjeta') COLLATE utf8_unicode_ci DEFAULT 'efectivo' COMMENT 'tipo de pago para esta venta en caso de ser a contado',
		  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'fecha de venta',
		  `subtotal` float DEFAULT NULL COMMENT 'subtotal de la venta, puede ser nulo',
		  `iva` float DEFAULT '0' COMMENT 'iva agregado por la venta, depende de cada sucursal',
		  `descuento` float NOT NULL DEFAULT '0' COMMENT 'descuento aplicado a esta venta',
		  `total` float NOT NULL DEFAULT '0' COMMENT 'total de esta venta',
		  `id_sucursal` int(11) NOT NULL COMMENT 'sucursal de la venta',
		  `id_usuario` int(11) NOT NULL COMMENT 'empleado que lo vendio',
		  `pagado` float NOT NULL DEFAULT '0' COMMENT 'porcentaje pagado de esta venta',
		  `cancelada` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'verdadero si esta venta ha sido cancelada, falso si no',
		  `ip` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip de donde provino esta compra',
		  `liquidada` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Verdadero si esta venta ha sido liquidada, falso si hay un saldo pendiente',
		  PRIMARY KEY (`id_venta`),
		  KEY `ventas_cliente` (`id_cliente`),
		  KEY `ventas_sucursal` (`id_sucursal`),
		  KEY `ventas_usuario` (`id_usuario`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


		-- 2011-08-04 14:16:58*/
		
		
	}



	?>
	<h1><?php echo $results[0]["desc"]; ?></h1>
	
	<h2>Base de datos</h2>
	<table>
		<tr><td>DB_USER</td>		<td><?php echo $results[0]["DB_USER"]; ?></td></tr>
		<tr><td>DB_PASSWORD</td>	<td><?php echo $results[0]["DB_PASSWORD"]; ?></td></tr>
		<tr><td>DB_NAME</td>		<td><?php echo $results[0]["DB_NAME"]; ?></td></tr>
		<tr><td>DB_DRIVER</td>		<td><?php echo $results[0]["DB_DRIVER"]; ?></td></tr>
		<tr><td>DB_HOST</td>		<td><?php echo $results[0]["DB_HOST"]; ?></td></tr>
		<tr><td>DB_DEBUG</td>		<td><?php echo $results[0]["DB_DEBUG"]; ?></td></tr>
	</table>
	 
	<h2>Resetear base de datos</h2>
	<form method="GET">
		<input type="hidden" name="reset_bd" value="1">
		<input type="hidden" name="action" value="detalles">
		<input type="hidden" name="iid" value="<?php echo $_REQUEST["iid"]; ?>">
		<input type="submit"  value="RESETEAR LA BD A VALORES DEFAULT" >
	</form>	
			
				
					
						
							