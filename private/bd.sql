-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 23-07-2010 a las 20:54:25
-- Versión del servidor: 5.1.37
-- Versión de PHP: 5.3.0

SET FOREIGN_KEY_CHECKS=0;

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de datos: `pos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identificador del cliente',
  `rfc` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'rfc del cliente si es que tiene',
  `nombre` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'nombre del cliente',
  `direccion` varchar(300) COLLATE utf8_unicode_ci NOT NULL COMMENT 'domicilio del cliente calle, no, colonia',
  `telefono` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Telefono del cliete',
  `e_mail` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT 'dias de credito para que pague el cliente',
  `limite_credito` float NOT NULL DEFAULT '0' COMMENT 'Limite de credito otorgado al cliente',
  `descuento` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Taza porcentual de descuento de 0 a 100',
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

DROP TABLE IF EXISTS `compras`;
CREATE TABLE IF NOT EXISTS `compras` (
  `id_compra` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id de la compra',
  `id_proveedor` int(11) NOT NULL COMMENT 'PROVEEDOR AL QUE SE LE COMPRO',
  `tipo_compra` enum('credito','contado') COLLATE utf8_unicode_ci NOT NULL COMMENT 'tipo de compra, contado o credito',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'fecha de compra',
  `subtotal` float NOT NULL COMMENT 'subtotal de compra',
  `iva` float NOT NULL COMMENT 'iva de la compra',
  `id_sucursal` int(11) NOT NULL COMMENT 'sucursal en que se compro',
  `id_usuario` int(11) NOT NULL COMMENT 'quien realizo la compra',
  PRIMARY KEY (`id_compra`),
  KEY `compras_proveedor` (`id_proveedor`),
  KEY `compras_sucursal` (`id_sucursal`),
  KEY `compras_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `corte`
--

DROP TABLE IF EXISTS `corte`;
CREATE TABLE IF NOT EXISTS `corte` (
  `num_corte` int(11) NOT NULL AUTO_INCREMENT COMMENT 'numero de corte',
  `anio` year(4) NOT NULL COMMENT 'año del corte',
  `inicio` date NOT NULL COMMENT 'año del corte',
  `fin` date NOT NULL COMMENT 'fecha de fin del corte',
  `ventas` float NOT NULL COMMENT 'ventas al contado en ese periodo',
  `abonosVentas` float NOT NULL COMMENT 'pagos de abonos en este periodo',
  `compras` float NOT NULL COMMENT 'compras realizadas en ese periodo',
  `AbonosCompra` float NOT NULL COMMENT 'pagos realizados en ese periodo',
  `gastos` float NOT NULL COMMENT 'gastos echos en ese periodo',
  `ingresos` float NOT NULL COMMENT 'ingresos obtenidos en ese periodo',
  `gananciasNetas` float NOT NULL COMMENT 'ganancias netas dentro del periodo',
  PRIMARY KEY (`num_corte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion`
--

DROP TABLE IF EXISTS `cotizacion`;
CREATE TABLE IF NOT EXISTS `cotizacion` (
  `id_cotizacion` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id de la cotizacion',
  `id_cliente` int(11) NOT NULL COMMENT 'id del cliente',
  `fecha` date NOT NULL COMMENT 'fecha de cotizacion',
  `subtotal` float NOT NULL COMMENT 'subtotal de la cotizacion',
  `iva` float NOT NULL COMMENT 'iva sobre el subtotal',
  `id_sucursal` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id_cotizacion`),
  KEY `cotizacion_cliente` (`id_cliente`),
  KEY `fk_cotizacion_1` (`id_sucursal`),
  KEY `fk_cotizacion_2` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

DROP TABLE IF EXISTS `detalle_compra`;
CREATE TABLE IF NOT EXISTS `detalle_compra` (
  `id_compra` int(11) NOT NULL COMMENT 'id de la compra',
  `id_producto` int(11) NOT NULL COMMENT 'id del producto',
  `cantidad` float NOT NULL COMMENT 'cantidad comprada',
  `precio` float NOT NULL COMMENT 'costo de compra',
  PRIMARY KEY (`id_compra`,`id_producto`),
  KEY `detalle_compra_producto` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_corte`
--

DROP TABLE IF EXISTS `detalle_corte`;
CREATE TABLE IF NOT EXISTS `detalle_corte` (
  `num_corte` int(11) NOT NULL COMMENT 'id del corte al que hace referencia',
  `nombre` varchar(100) NOT NULL COMMENT 'nombre del encargado de sucursal al momento del corte',
  `total` float NOT NULL COMMENT 'total que le corresponde al encargado al momento del corte',
  `deben` float NOT NULL COMMENT 'lo que deben en la sucursal del encargado al momento del corte',
  PRIMARY KEY (`num_corte`,`nombre`),
  KEY `corte_detalleCorte` (`num_corte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_cotizacion`
--

DROP TABLE IF EXISTS `detalle_cotizacion`;
CREATE TABLE IF NOT EXISTS `detalle_cotizacion` (
  `id_cotizacion` int(11) NOT NULL COMMENT 'id de la cotizacion',
  `id_producto` int(11) NOT NULL COMMENT 'id del producto',
  `cantidad` float NOT NULL COMMENT 'cantidad cotizado',
  `precio` float NOT NULL COMMENT 'precio al que cotizo el producto',
  PRIMARY KEY (`id_cotizacion`,`id_producto`),
  KEY `detalle_cotizacion_producto` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_inventario`
--

DROP TABLE IF EXISTS `detalle_inventario`;
CREATE TABLE IF NOT EXISTS `detalle_inventario` (
  `id_producto` int(11) NOT NULL COMMENT 'id del producto al que se refiere',
  `id_sucursal` int(11) NOT NULL COMMENT 'id de la sucursal',
  `precio_venta` float NOT NULL COMMENT 'precio al que se vendera al publico',
  `min` float NOT NULL DEFAULT '0' COMMENT 'cantidad minima que debe de haber del producto en almacen de esta sucursal',
  `existencias` float NOT NULL DEFAULT '0' COMMENT 'cantidad de producto que hay actualmente en almacen de esta sucursal',
  PRIMARY KEY (`id_producto`,`id_sucursal`),
  KEY `id_sucursal` (`id_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

DROP TABLE IF EXISTS `detalle_venta`;
CREATE TABLE IF NOT EXISTS `detalle_venta` (
  `id_venta` int(11) NOT NULL COMMENT 'venta a que se referencia',
  `id_producto` int(11) NOT NULL COMMENT 'producto de la venta',
  `cantidad` float NOT NULL COMMENT 'cantidad que se vendio',
  `precio` float NOT NULL COMMENT 'precio al que se vendio',
  PRIMARY KEY (`id_venta`,`id_producto`),
  KEY `detalle_venta_producto` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encargado`
--

DROP TABLE IF EXISTS `encargado`;
CREATE TABLE IF NOT EXISTS `encargado` (
  `id_usuario` int(11) NOT NULL COMMENT 'Este id es el del usuario encargado de su sucursal',
  `porciento` float NOT NULL COMMENT 'este es el porciento de las ventas que le tocan al encargado',
  PRIMARY KEY (`id_usuario`),
  KEY `fk_encargado_1` (`id_usuario`),
  KEY `usuario_encargado` (`id_usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura_compra`
--

DROP TABLE IF EXISTS `factura_compra`;
CREATE TABLE IF NOT EXISTS `factura_compra` (
  `folio` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `id_compra` int(11) NOT NULL COMMENT 'COMPRA A LA QUE CORRESPONDE LA FACTURA',
  PRIMARY KEY (`folio`),
  KEY `factura_compra_compra` (`id_compra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura_venta`
--

DROP TABLE IF EXISTS `factura_venta`;
CREATE TABLE IF NOT EXISTS `factura_venta` (
  `folio` varchar(15) COLLATE utf8_unicode_ci NOT NULL COMMENT 'folio que tiene la factura',
  `id_venta` int(11) NOT NULL COMMENT 'venta a la cual corresponde la factura',
  PRIMARY KEY (`folio`),
  KEY `factura_venta_venta` (`id_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

DROP TABLE IF EXISTS `gastos`;
CREATE TABLE IF NOT EXISTS `gastos` (
  `id_gasto` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id para identificar el gasto',
  `concepto` varchar(100) NOT NULL COMMENT 'concepto en lo que se gasto',
  `monto` float NOT NULL COMMENT 'lo que costo este gasto',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'fecha del gasto',
  `id_sucursal` int(11) NOT NULL COMMENT 'sucursal en la que se hizo el gasto',
  `id_usuario` int(11) NOT NULL COMMENT 'usuario que registro el gasto',
  PRIMARY KEY (`id_gasto`),
  KEY `fk_gastos_1` (`id_usuario`),
  KEY `usuario_gasto` (`id_usuario`),
  KEY `sucursal_gasto` (`id_sucursal`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

DROP TABLE IF EXISTS `grupos`;
CREATE TABLE IF NOT EXISTS `grupos` (
  `id_grupo` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  PRIMARY KEY (`id_grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos_permisos`
--

DROP TABLE IF EXISTS `grupos_permisos`;
CREATE TABLE IF NOT EXISTS `grupos_permisos` (
  `id_grupo` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  PRIMARY KEY (`id_grupo`,`id_permiso`),
  KEY `fk_grupos_permisos_1` (`id_permiso`),
  KEY `fk_grupos_permisos_2` (`id_grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos_usuarios`
--

DROP TABLE IF EXISTS `grupos_usuarios`;
CREATE TABLE IF NOT EXISTS `grupos_usuarios` (
  `id_grupo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id_grupo`,`id_usuario`),
  KEY `fk_grupos_usuarios_1` (`id_grupo`),
  KEY `fk_grupos_usuarios_2` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuesto`
--

DROP TABLE IF EXISTS `impuesto`;
CREATE TABLE IF NOT EXISTS `impuesto` (
  `id_impuesto` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `valor` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  PRIMARY KEY (`id_impuesto`),
  KEY `fk_impuesto_1` (`id_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

DROP TABLE IF EXISTS `ingresos`;
CREATE TABLE IF NOT EXISTS `ingresos` (
  `id_ingreso` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id para identificar el ingreso',
  `concepto` varchar(100) NOT NULL COMMENT 'concepto en lo que se ingreso',
  `monto` float NOT NULL COMMENT 'lo que costo este ingreso',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'fecha del ingreso',
  `id_sucursal` int(11) NOT NULL COMMENT 'sucursal en la que se hizo el ingreso',
  `id_usuario` int(11) NOT NULL COMMENT 'usuario que registro el ingreso',
  PRIMARY KEY (`id_ingreso`),
  KEY `fk_ingresos_1` (`id_usuario`),
  KEY `usuario_ingreso` (`id_usuario`),
  KEY `sucursal_ingreso` (`id_sucursal`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

DROP TABLE IF EXISTS `inventario`;
CREATE TABLE IF NOT EXISTS `inventario` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id del producto',
  `nombre` varchar(90) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Descripcion o nombre del producto',
  `denominacion` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'es lo que se le mostrara a los clientes',
  PRIMARY KEY (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_compra`
--

DROP TABLE IF EXISTS `pagos_compra`;
CREATE TABLE IF NOT EXISTS `pagos_compra` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identificador del pago',
  `id_compra` int(11) NOT NULL COMMENT 'identificador de la compra a la que pagamos',
  `fecha` date NOT NULL COMMENT 'fecha en que se abono',
  `monto` float NOT NULL COMMENT 'monto que se abono',
  PRIMARY KEY (`id_pago`),
  KEY `pagos_compra_compra` (`id_compra`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_venta`
--

DROP TABLE IF EXISTS `pagos_venta`;
CREATE TABLE IF NOT EXISTS `pagos_venta` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id de pago del cliente',
  `id_venta` int(11) NOT NULL COMMENT 'id de la venta a la que se esta pagando',
  `fecha` date NOT NULL COMMENT 'fecha de pago',
  `monto` float NOT NULL COMMENT 'total de credito del cliente',
  PRIMARY KEY (`id_pago`),
  KEY `pagos_venta_venta` (`id_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

DROP TABLE IF EXISTS `permisos`;
CREATE TABLE IF NOT EXISTS `permisos` (
  `id_permiso` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  PRIMARY KEY (`id_permiso`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_proveedor`
--

DROP TABLE IF EXISTS `productos_proveedor`;
CREATE TABLE IF NOT EXISTS `productos_proveedor` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identificador del producto',
  `clave_producto` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'clave de producto para el proveedor',
  `id_proveedor` int(11) NOT NULL COMMENT 'clave del proveedor',
  `id_inventario` int(11) NOT NULL COMMENT 'clave con la que entra a nuestro inventario',
  `descripcion` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Descripcion del producto que nos vende el proveedor',
  `precio` int(11) NOT NULL COMMENT 'precio al que se compra el producto (sin descuento)',
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `clave_producto` (`clave_producto`,`id_proveedor`),
  UNIQUE KEY `id_proveedor` (`id_proveedor`,`id_inventario`),
  KEY `productos_proveedor_proveedor` (`id_proveedor`),
  KEY `productos_proveedor_producto` (`id_inventario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

DROP TABLE IF EXISTS `proveedor`;
CREATE TABLE IF NOT EXISTS `proveedor` (
  `id_proveedor` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identificador del proveedor',
  `rfc` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'rfc del proveedor',
  `nombre` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'nombre del proveedor',
  `direccion` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'direccion del proveedor',
  `telefono` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'telefono',
  `e_mail` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'email del provedor',
  PRIMARY KEY (`id_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

DROP TABLE IF EXISTS `sucursal`;
CREATE TABLE IF NOT EXISTS `sucursal` (
  `id_sucursal` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de cada sucursal',
  `descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'nombre o descripcion de sucursal',
  `direccion` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'direccion de la sucursal',
  PRIMARY KEY (`id_sucursal`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identificador del usuario',
  `nombre` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'nombre del empleado',
  `usuario` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `contrasena` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `id_sucursal` int(11) NOT NULL COMMENT 'Id de la sucursal a que pertenece',
  PRIMARY KEY (`id_usuario`),
  KEY `fk_usuario_1` (`id_sucursal`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE IF NOT EXISTS `ventas` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id de venta',
  `id_cliente` int(11) NOT NULL COMMENT 'cliente al que se le vendio',
  `tipo_venta` enum('credito','contado') COLLATE utf8_unicode_ci NOT NULL COMMENT 'tipo de venta, contado o credito',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'fecha de venta',
  `subtotal` float NOT NULL COMMENT 'subtotal de la venta',
  `iva` float NOT NULL COMMENT 'iva agregado por la venta',
  `id_sucursal` int(11) NOT NULL COMMENT 'sucursal de la venta',
  `id_usuario` int(11) NOT NULL COMMENT 'empleado que lo vendio',
  PRIMARY KEY (`id_venta`),
  KEY `ventas_cliente` (`id_cliente`),
  KEY `ventas_sucursal` (`id_sucursal`),
  KEY `ventas_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


/******************************************************************
*	SCRIPT PARA GENERAR LAS VISTAS PARA LOS REPORTES Y GRAFICAS
*	
******************************************************************/




/********************************************************
*	VISTA DE VENTAS
********************************************************/


CREATE OR REPLACE VIEW view_ventas
AS SELECT `v`.`id_venta`, `c`.`nombre` AS `cliente`, `v`.`id_cliente`, `v`.`tipo_venta`, `v`.`fecha`, `v`.`subtotal`, `v`.`iva`, `s`.`descripcion` AS `sucursal`, `v`.`id_sucursal`, `u`.`nombre` AS `usuario`, `v`.`id_usuario`
FROM `ventas` AS v
INNER JOIN ( `cliente` AS c, `sucursal` AS s, `usuario` AS u)
ON  (`v`.`id_cliente` = `c`.`id_cliente`
AND `v`.`id_sucursal` = `s`.`id_sucursal`
AND `v`.`id_usuario` = `u`.`id_usuario`);



/********************************************************
*	VISTA DE COMPRAS
********************************************************/

CREATE OR REPLACE VIEW view_compras
AS SELECT `c`.`id_compra`, `p`.`nombre` AS `proveedor`, `p`.`id_proveedor`,`c`.`tipo_compra`, `c`.`fecha`, `c`.`subtotal`, `c`.`iva`, `s`.`descripcion` AS `sucursal`, `c`.`id_sucursal`, `u`.`nombre` AS `usuario`, `c`.`id_usuario`
FROM `compras` AS c
INNER JOIN ( `proveedor` AS p, `sucursal` AS s, `usuario` AS u)
ON  (`c`.`id_proveedor` = `p`.`id_proveedor`
AND `c`.`id_sucursal` = `s`.`id_sucursal`
AND `c`.`id_usuario` = `u`.`id_usuario`);


/********************************************************
*	VISTA DE GASTOS
********************************************************/

CREATE OR REPLACE VIEW view_gastos
AS SELECT `g`.`id_gasto`, `g`.`monto`, `g`.`fecha`, `s`.`descripcion` AS `sucursal`, `g`.`id_sucursal`, `u`.`nombre` AS `usuario`
FROM `gastos` AS g
INNER JOIN ( `sucursal` AS s, `usuario` AS u)
ON ( `g`.`id_sucursal` = `s`.`id_sucursal`
AND `g`.`id_usuario` = `u`.`id_usuario` );



/********************************************************
*	VISTA DE INGRESOS
********************************************************/

CREATE OR REPLACE VIEW view_ingresos
AS SELECT `i`.`id_ingreso`, `i`.`monto`, `i`.`fecha`, `s`.`descripcion` AS `sucursal`, `i`.`id_sucursal`, `u`.`nombre` AS `usuario`
FROM `ingresos` AS i
INNER JOIN ( `sucursal` AS s, `usuario` AS u)
ON ( `i`.`id_sucursal` = `s`.`id_sucursal`
AND `i`.`id_usuario` = `u`.`id_usuario` );




/********************************************************
*	VISTA DE DETALLES DE VENTA
********************************************************/

CREATE OR REPLACE VIEW view_detalle_venta
AS SELECT `d`.`id_venta`, `d`.`id_producto`,`i`.`denominacion`, `d`.`cantidad`, `d`.`precio`, `v`.`fecha`, `v`.`tipo_venta`, `v`.`id_sucursal`
FROM `detalle_venta` AS d
INNER JOIN ( `inventario` AS i, `ventas` AS v)
ON ( `d`.`id_venta` = `v`.`id_venta`
AND `d`.`id_producto` = `i`.`id_producto` );


/********************************************************
*	VISTA DE DETALLES DE VENTA
********************************************************/

CREATE OR REPLACE VIEW view_detalle_compra
AS SELECT `d`.`id_compra`, `d`.`id_producto`,`i`.`denominacion`, `d`.`cantidad`, `d`.`precio`, `c`.`fecha`, `c`.`tipo_compra`, `c`. `id_sucursal`
FROM `detalle_compra` AS d
INNER JOIN ( `inventario` AS i, `compras` AS c)
ON ( `d`.`id_compra` = `c`.`id_compra`
AND `d`.`id_producto` = `i`.`id_producto` );



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
