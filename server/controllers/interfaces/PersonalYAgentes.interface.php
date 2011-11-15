<?php
/**
  *
  *
  *
  **/
	
  interface IPersonalYAgentes {
  
  
	/**
 	 *
 	 *Edita la informacion de un grupo, puede usarse para editar los permisos del mismo
 	 *
 	 * @param id_rol int Id del rol a editar
 	 * @param descuento float Descuento que se le hara a este rol
 	 * @param nombre string Nombre del grupo
 	 * @param salario float Salario base para este rol
 	 * @param descripcion string Descripcion larga del grupo
 	 **/
  static function EditarRol
	(
		$id_rol, 
		$descuento = null,
		$nombre = null,
		$salario = null,
		$descripcion = null
	);  
  
  
	
  
	/**
 	 *
 	 *Este metodo desactiva un grupo, solo se podra desactivar un grupo si no hay ningun usuario que pertenezca a ?l.
 	 *
 	 * @param id_rol int Id del grupo a eliminar
 	 **/
  static function EliminarRol
	(
		$id_rol
	);  
  
  
	
  
	/**
 	 *
 	 *Lista los roles, se puede filtrar por empresa y ordenar por sus atributos
 	 *
 	 * @param orden json Objeto que determinara el orden de la lista
 	 * @return roles json Objeto que contendra la lista de los roles
 	 **/
  static function ListaRol
	(
		$orden = null
	);  
  
  
	
  
	/**
 	 *
 	 *Crea un nuevo grupo de usuarios. Se asignaran los permisos de este grupo al momento de su creacion.
 	 *
 	 * @param nombre string Nombre del grupo. Este no puede existir en el sistema, no puede ser una cadena vacia y no puede ser mayor a 30 caracteres.
 	 * @param descripcion string Descripcion larga del grupo. La descripcion no puede ser una cadena vacia ni mayor a 256 caracteres.
 	 * @param descuento float El procentaje de descuento que este grupo gozara al comprar cualquier producto. NOTA: EN COMPRA/VENTA? EN QUE PRODUCTOS? ESTE ES UN PORCENTAJE O UNA CANTIDAD? 
 	 * @param salario float El salario de este rol.
 	 * @return id_rol int El nuero id del rol que se ha generado.
 	 **/
  static function NuevoRol
	(
		$nombre, 
		$descripcion, 
		$descuento = 0, 
		$salario = 0
	);  
  
  
	
  
	/**
 	 *
 	 *Este metodo asigna permisos a un rol. Cada vez que se llame a este metodo, se asignaran estos permisos a los usuarios que pertenezcan a este rol.
 	 *
 	 * @param id_permiso int Arreglo de ids de los permisos que seran asignados al rol
 	 * @param id_rol int Id del rol al que se le asignaran los permisos
 	 **/
  static function AsignarPermisoRol
	(
		$id_permiso, 
		$id_rol
	);  
  
  
	
  
	/**
 	 *
 	 *Regresa un alista de permisos, nombres y ids de los permisos del sistema.
 	 *
 	 **/
  static function ListaPermisoRol
	(
                $id_rol = null,
                $id_permiso = null
	);  
  
  
	
  
	/**
 	 *
 	 *Este metodo quita un permiso de un rol. Al remover este permiso de un rol, los permisos que un usuario especifico tiene gracias a una asignacion especial se mantienen. 
 	 *
 	 * @param id_permiso int Id del permiso a remover
 	 * @param id_rol int Id del rol al que se le quitaran los permisos
 	 **/
  static function RemoverPermisoRol
	(
		$id_permiso, 
		$id_rol
	);  
  
  
	
  
	/**
 	 *
 	 *Editar los detalles de un usuario.
 	 *
 	 * @param id_usuario int Usuario a editar
 	 * @param numero_exterior string Numero exterior del domicilio del usuario
 	 * @param numero_exterior_2 string Numero exterior de la direccion alterna del usuario
 	 * @param curp string CURP del usuario
 	 * @param rfc string RFC del usuario
 	 * @param dias_de_credito int Dias de credito del cliente
 	 * @param telefono2 string Otro telefono de la direccion del usuario
 	 * @param dias_de_embarque int Dias de emabrque del proveedor ( Lunes, Miercoles, etc)
 	 * @param calle_2 string Calle de la direccion alterna del usuario
 	 * @param representante_legal string Nombre del representante legal del usuario
 	 * @param correo_electronico string correo electronico del usuario
 	 * @param comision_ventas float El porcentaje que gana como comision por ventas este usuario
 	 * @param colonia string Colonia donde vive el usuario
 	 * @param nombre string Nombre del usuario
 	 * @param codigo_usuario string Codigo interno del usuario
 	 * @param id_clasificacion_proveedor int Id de la clasificacion del proveedor
 	 * @param password string Password del usuario
 	 * @param id_ciudad int Id de la ciudad del domicilio del usuario
 	 * @param numero_interior string Numero interior del domicilio del usuario
 	 * @param texto_extra string Referencia del domicilio del usuario
 	 * @param codigo_postal string Codigo Postal del domicilio del usuario
 	 * @param numero_interior_2 string Numero interior de la direccion alterna del usuario
 	 * @param calle string calle del domicilio del usuario
 	 * @param dia_de_pago string Fecha de pago del cliente
 	 * @param id_ciudad_2 int Id de la ciudad de la direccion alterna del usuario
 	 * @param saldo_del_ejercicio float Saldo del ejercicio del cliente
 	 * @param retenciones json Ids de las retenciones que afectan a este usuario
 	 * @param impuestos json Objeto que contendra los ids de los impuestos que afectan a este usuario
 	 * @param texto_extra_2 string Texto extra para ubicar la direccion alterna del usuario
 	 * @param ventas_a_credito int Ventas a credito del cliente
 	 * @param telefono_personal_2 string Telefono personal alterno del usuario
 	 * @param mensajeria bool Si el usuario tiene una cuenta de mensajeria
 	 * @param facturar_a_terceros bool Si el usuario puede facturar a terceros
 	 * @param telefono2_2 string telefono2 de la direccion alterna del usuario
 	 * @param pagina_web string Pagina web del usuario
 	 * @param limite_de_credito float Limite de credito del usuario
 	 * @param telefono_personal_1 string telefono personal del usuario
 	 * @param descuento float Descuento que se le hara al usuario al venderle
 	 * @param salario float Si el usuario contara con un salario no establecido por el rol
 	 * @param id_rol int Id rol del usuario
 	 * @param colonia_2 string Colonia de la direccion alterna del usuario
 	 * @param denominacion_comercial string Denominacion comercial del cliente
 	 * @param descuento_es_porcentaje bool Si el descuento es un porcentaje o es un valor fijo
 	 * @param id_clasificacion_cliente int Id de la clasificacion del cliente
 	 * @param cuenta_bancaria string Cuenta bancaria del usuario
 	 * @param dia_de_revision string Fecha de revision del cliente
 	 * @param cuenta_mensajeria string Cuenta de mensajeria del usuario
 	 * @param telefono1 string Telefono del usuario
 	 * @param codigo_postal_2 string Codigo postal de la direccion alterna del usuario
 	 * @param id_sucursal int Id de la sucursal en la que fue creada este usuario o donde labora.
 	 * @param telefono1_2 string Telefono de la direccion alterna del usuario
 	 * @param intereses_moratorios float Intereses moratorios del cliente
 	 **/
  static function EditarUsuario
	(
		$id_usuario, 
		$numero_exterior = null, 
		$numero_exterior_2 = null, 
		$curp = null, 
		$rfc = null, 
		$dias_de_credito = null, 
		$telefono2 = null, 
		$dias_de_embarque = null, 
		$calle_2 = null, 
		$representante_legal = null, 
		$correo_electronico = null, 
		$comision_ventas = null, 
		$colonia = null, 
		$nombre = null, 
		$codigo_usuario = null, 
		$id_clasificacion_proveedor = null, 
		$password = null, 
		$id_ciudad = null, 
		$numero_interior = null, 
		$texto_extra = null, 
		$codigo_postal = null, 
		$numero_interior_2 = null, 
		$calle = null, 
		$dia_de_pago = null, 
		$id_ciudad_2 = null, 
		$saldo_del_ejercicio = null, 
		$retenciones = null, 
		$impuestos = null, 
		$texto_extra_2 = null, 
		$ventas_a_credito = null, 
		$telefono_personal_2 = null, 
		$mensajeria = null, 
		$facturar_a_terceros = null, 
		$telefono2_2 = null, 
		$pagina_web = null, 
		$limite_de_credito = null, 
		$telefono_personal_1 = null, 
		$descuento = null, 
		$salario = null, 
		$id_rol = null, 
		$colonia_2 = null, 
		$denominacion_comercial = null, 
		$id_clasificacion_cliente = null, 
		$cuenta_bancaria = null, 
		$dia_de_revision = null, 
		$cuenta_mensajeria = null, 
		$telefono1 = null, 
		$codigo_postal_2 = null, 
		$id_sucursal = null, 
		$telefono1_2 = null, 
		$intereses_moratorios = null,
                $id_moneda = null,
                $tiempo_entrega = null
	);  
  
  
	
  
	/**
 	 *
 	 *Este metodo desactiva un usuario, usese cuando un empleado ya no trabaje para usted. Que pasa cuando el usuario tiene cuentas abiertas o ventas a credito con saldo.
 	 *
 	 * @param id_usuario int Id del usuario a eliminar
 	 **/
  static function EliminarUsuario
	(
		$id_usuario
	);  
  
  
	
  
	/**
 	 *
 	 *Listar a todos los usuarios del sistema. Se puede ordenar por los atributos del usuario y filtrar en activos e inactivos
 	 *
 	 * @param activo bool True si se mostrarn solo los usuarios activos, false si solo se mostrarn los usuarios inactivos
 	 * @param ordenar json Valor numrico que indicar la forma en que se ordenar la lista
 	 * @return usuarios json Arreglo de objetos que contendr� la informaci�n de los usuarios del sistema
 	 **/
  static function ListaUsuario
	(
		$activo = null, 
		$ordenar = null
	);  
  
  
	
  
	/**
 	 *
 	 *Insertar un nuevo usuario. El usuario que lo crea sera tomado de la sesion actual y la fecha sera tomada del servidor. Un usuario no puede tener mas de un rol en una misma sucursal de una misma empresa.
 	 *
 	 * @param nombre string Nombre del usuario
 	 * @param id_rol int Id del rol del usuario en la instnacia
 	 * @param password string Password del usuario
 	 * @param codigo_usuario string Codigo interno del usuario
 	 * @param telefono1 string Telefono principal del agente
 	 * @param numero_exterior_2 string Numero exterior de la direccion alterna del usuario
 	 * @param calle_2 string Calle de la direccion alterna del usuario
 	 * @param dias_de_credito int Dias de credito del cliente
 	 * @param denominacion_comercial string Denominacion comercial del cliente
 	 * @param telefono2 string Otro telefono del agente
 	 * @param texto_extra string Comentario sobre la direccion del agente
 	 * @param correo_electronico string Correo Electronico del agente
 	 * @param id_ciudad_2 int Id de la ciudad de la direccion alterna del usuario
 	 * @param numero_interior string Numero interior del agente
 	 * @param calle string Calle donde vive el agente
 	 * @param colonia_2 string Colonia de la direccion alterna del usuario
 	 * @param id_ciudad int ID de la ciudad donde vive el agente
 	 * @param numero_interior_2 string Numero interior de la direccion alterna del usuario
 	 * @param texto_extra_2 string Texto extra para ubicar la direccion alterna del usuario
 	 * @param codigo_postal_2 string Codigo postal de la direccion alterna del usuario
 	 * @param telefono2_2 string Telefono 2 de la direccion al terna del usuario
 	 * @param codigo_postal string Codigo postal del Agente
 	 * @param telefono1_2 string Telefono de la direccion alterna del usuario
 	 * @param telefno_personal2 string Telefono personal del usuario
 	 * @param limite_credito float El limite de credito del usuario
 	 * @param pagina_web string Pagina web del usuario
 	 * @param descuento float El porcentaje de descuento que se le hara al usuario al venderle
 	 * @param telefono_personal1 string Telefono personal del usuario
 	 * @param ventas_a_credito int Ventas a credito del cliente
 	 * @param intereses_moratorios float Intereses moratorios del cliente
 	 * @param salario float Si el usuario contara con un salario especial no especificado por el rol
 	 * @param saldo_del_ejercicio float Saldo del ejercicio del cliente
 	 * @param representante_legal string Nombre del representante legal del usuario
 	 * @param cuenta_bancaria string Cuenta bancaria del usuario
 	 * @param dia_de_pago string Fecha de pago del cliente
 	 * @param mpuestos json Objeto que contendra los impuestos que afectan a este usuario
 	 * @param mensajeria bool Si el cliente tiene una cuenta de mensajeria
 	 * @param id_sucursal int Id de la sucursal donde fue creado el usuario o donde labora
 	 * @param facturar_a_terceros bool Si el usuario puede facturar a terceros
 	 * @param dias_de_embarque int Dias de embarque del proveedor ( Lunes, Miercoles, Viernes, etc)
 	 * @param numero_exterior string Numero exterior del agente
 	 * @param id_clasificacion_cliente int Id de la clasificacion del cliente
 	 * @param curp string CURP del agente
 	 * @param dia_de_revision string Fecha de revision del cliente
 	 * @param cuenta_mensajeria string Cuenta de mensajeria del usuario
 	 * @param comision_ventas float El porcentaje de la comision que ganara este usuario especificamente por ventas
 	 * @param rfc string RFC del agente
 	 * @param id_clasificacion_proveedor int Id de la clasificacion del proveedor
 	 * @param colonia string Colonia donde vive el agente
 	 * @param retenciones json Ids de las retenciones que afectan a este usuario
 	 * @return id_usuario int Id generado por la inserci�n del usuario
 	 **/
  static function NuevoUsuario
	(
		$codigo_usuario,
		$password,
		$id_rol,
		$nombre,
		$curp = null,
		$dia_de_revision = null,
		$id_clasificacion_cliente = null,
		$numero_exterior = null,
		$facturar_a_terceros = null,
		$id_sucursal = null,
		$dias_de_embarque = null,
		$saldo_del_ejercicio = null, 
		$representante_legal = null,
		$dia_de_pago = null,
		$impuestos = null,
		$mensajeria = null,
		$salario = null,
		$cuenta_bancaria = null,
		$intereses_moratorios = null,
		$ventas_a_credito = null,
		$pagina_web = null,
		$telefono_personal1 = "",
		$descuento = null,
		$telefono2_2 = null,
		$limite_credito = 0,
		$telefono_personal2 = null,
		$telefono1_2 = null,
		$codigo_postal = null,
		$texto_extra_2 = null,
		$codigo_postal_2 = null,
		$calle = null,
		$numero_interior_2 = null,
		$id_ciudad = null,
		$colonia_2 = null,
		$id_ciudad_2 = null,
		$numero_interior = null,
		$correo_electronico = null,
		$telefono2 = null,
		$dias_de_credito = null,
		$texto_extra = "",
		$calle_2 = null,
		$denominacion_comercial = null,
		$numero_exterior_2 = null,
		$comision_ventas = 0,
		$telefono1 = null,
		$cuenta_mensajeria = null,
		$rfc = "",
		$id_clasificacion_proveedor = null,
		$retenciones = "",
		$colonia = "",
		$id_moneda = null,
		$tiempo_entrega = null
	);  
  
  
	
  
	/**
 	 *
 	 *Asigna uno o varios permisos especificos a un usuario. No se pueden asignar permisos que ya se tienen
 	 *
 	 * @param id_usuario int Id del usuario al que se le asignara el permiso
 	 * @param id_permiso int Id del permiso que se le asignaran a este usuario en especial
 	 **/
  static function AsignarPermisoUsuario
	(
		$id_usuario, 
		$id_permiso
	);  
  
  static function ListaPermisoUsuario
        (
                $id_usuario = null,
                $id_permiso = null
        );
	
  
	/**
 	 *
 	 *Quita uno o varios permisos a un usuario. No se puede negar un permiso que no se tiene
 	 *
 	 * @param id_permiso int Id del permiso a quitar de este usuario
 	 * @param id_usuario int Id del usuario al que se le niegan los permisos
 	 **/
  static function RemoverPermisoUsuario
	(
		$id_permiso, 
		$id_usuario
	);  
  
  
	
  }