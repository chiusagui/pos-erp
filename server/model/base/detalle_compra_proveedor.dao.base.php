<?php
/** DetalleCompraProveedor Data Access Object (DAO) Base.
  * 
  * Esta clase contiene toda la manipulacion de bases de datos que se necesita para 
  * almacenar de forma permanente y recuperar instancias de objetos {@link DetalleCompraProveedor }. 
  * @author caffeina
  * @access private
  * @abstract
  * @package docs
  * 
  */
abstract class DetalleCompraProveedorDAOBase extends DAO
{

		private static $loadedRecords = array();

		private static function recordExists(  $id_compra_proveedor, $id_producto ){
			$pk = "";
			$pk .= $id_compra_proveedor . "-";
			$pk .= $id_producto . "-";
			return array_key_exists ( $pk , self::$loadedRecords );
		}
		private static function pushRecord( $inventario,  $id_compra_proveedor, $id_producto){
			$pk = "";
			$pk .= $id_compra_proveedor . "-";
			$pk .= $id_producto . "-";
			self::$loadedRecords [$pk] = $inventario;
		}
		private static function getRecord(  $id_compra_proveedor, $id_producto ){
			$pk = "";
			$pk .= $id_compra_proveedor . "-";
			$pk .= $id_producto . "-";
			return self::$loadedRecords[$pk];
		}
	/**
	  *	Guardar registros. 
	  *	
	  *	Este metodo guarda el estado actual del objeto {@link DetalleCompraProveedor} pasado en la base de datos. La llave 
	  *	primaria indicara que instancia va a ser actualizado en base de datos. Si la llave primara o combinacion de llaves
	  *	primarias describen una fila que no se encuentra en la base de datos, entonces save() creara una nueva fila, insertando
	  *	en ese objeto el ID recien creado.
	  *	
	  *	@static
	  * @throws Exception si la operacion fallo.
	  * @param DetalleCompraProveedor [$detalle_compra_proveedor] El objeto de tipo DetalleCompraProveedor
	  * @return Un entero mayor o igual a cero denotando las filas afectadas.
	  **/
	public static final function save( &$detalle_compra_proveedor )
	{
		if(  self::getByPK(  $detalle_compra_proveedor->getIdCompraProveedor() , $detalle_compra_proveedor->getIdProducto() ) !== NULL )
		{
			try{ return DetalleCompraProveedorDAOBase::update( $detalle_compra_proveedor) ; } catch(Exception $e){ throw $e; }
		}else{
			try{ return DetalleCompraProveedorDAOBase::create( $detalle_compra_proveedor) ; } catch(Exception $e){ throw $e; }
		}
	}


	/**
	  *	Obtener {@link DetalleCompraProveedor} por llave primaria. 
	  *	
	  * Este metodo cargara un objeto {@link DetalleCompraProveedor} de la base de datos 
	  * usando sus llaves primarias. 
	  *	
	  *	@static
	  * @return @link DetalleCompraProveedor Un objeto del tipo {@link DetalleCompraProveedor}. NULL si no hay tal registro.
	  **/
	public static final function getByPK(  $id_compra_proveedor, $id_producto )
	{
		if(self::recordExists(  $id_compra_proveedor, $id_producto)){
			return self::getRecord( $id_compra_proveedor, $id_producto );
		}
		$sql = "SELECT * FROM detalle_compra_proveedor WHERE (id_compra_proveedor = ? AND id_producto = ? ) LIMIT 1;";
		$params = array(  $id_compra_proveedor, $id_producto );
		global $conn;
		$rs = $conn->GetRow($sql, $params);
		if(count($rs)==0)return NULL;
			$foo = new DetalleCompraProveedor( $rs );
			self::pushRecord( $foo,  $id_compra_proveedor, $id_producto );
			return $foo;
	}


	/**
	  *	Obtener todas las filas.
	  *	
	  * Esta funcion leera todos los contenidos de la tabla en la base de datos y construira
	  * un vector que contiene objetos de tipo {@link DetalleCompraProveedor}. Tenga en cuenta que este metodo
	  * consumen enormes cantidades de recursos si la tabla tiene muchas filas. 
	  * Este metodo solo debe usarse cuando las tablas destino tienen solo pequenas cantidades de datos o se usan sus parametros para obtener un menor numero de filas.
	  *	
	  *	@static
	  * @param $pagina Pagina a ver.
	  * @param $columnas_por_pagina Columnas por pagina.
	  * @param $orden Debe ser una cadena con el nombre de una columna en la base de datos.
	  * @param $tipo_de_orden 'ASC' o 'DESC' el default es 'ASC'
	  * @return Array Un arreglo que contiene objetos del tipo {@link DetalleCompraProveedor}.
	  **/
	public static final function getAll( $pagina = NULL, $columnas_por_pagina = NULL, $orden = NULL, $tipo_de_orden = 'ASC' )
	{
		$sql = "SELECT * from detalle_compra_proveedor";
		if($orden != NULL)
		{ $sql .= " ORDER BY " . $orden . " " . $tipo_de_orden;	}
		if($pagina != NULL)
		{
			$sql .= " LIMIT " . (( $pagina - 1 )*$columnas_por_pagina) . "," . $columnas_por_pagina; 
		}
		global $conn;
		$rs = $conn->Execute($sql);
		$allData = array();
		foreach ($rs as $foo) {
			$bar = new DetalleCompraProveedor($foo);
    		array_push( $allData, $bar);
			//id_compra_proveedor
			//id_producto
    		self::pushRecord( $bar, $foo["id_compra_proveedor"],$foo["id_producto"] );
		}
		return $allData;
	}


	/**
	  *	Buscar registros.
	  *	
	  * Este metodo proporciona capacidad de busqueda para conseguir un juego de objetos {@link DetalleCompraProveedor} de la base de datos. 
	  * Consiste en buscar todos los objetos que coinciden con las variables permanentes instanciadas de objeto pasado como argumento. 
	  * Aquellas variables que tienen valores NULL seran excluidos en busca de criterios.
	  *	
	  * <code>
	  *  /**
	  *   * Ejemplo de uso - buscar todos los clientes que tengan limite de credito igual a 20000
	  *   {@*} 
	  *	  $cliente = new Cliente();
	  *	  $cliente->setLimiteCredito("20000");
	  *	  $resultados = ClienteDAO::search($cliente);
	  *	  
	  *	  foreach($resultados as $c ){
	  *	  	echo $c->getNombre() . "<br>";
	  *	  }
	  * </code>
	  *	@static
	  * @param DetalleCompraProveedor [$detalle_compra_proveedor] El objeto de tipo DetalleCompraProveedor
	  * @param $orderBy Debe ser una cadena con el nombre de una columna en la base de datos.
	  * @param $orden 'ASC' o 'DESC' el default es 'ASC'
	  **/
	public static final function search( $detalle_compra_proveedor , $orderBy = null, $orden = 'ASC')
	{
		$sql = "SELECT * from detalle_compra_proveedor WHERE ("; 
		$val = array();
		if( $detalle_compra_proveedor->getIdCompraProveedor() != NULL){
			$sql .= " id_compra_proveedor = ? AND";
			array_push( $val, $detalle_compra_proveedor->getIdCompraProveedor() );
		}

		if( $detalle_compra_proveedor->getIdProducto() != NULL){
			$sql .= " id_producto = ? AND";
			array_push( $val, $detalle_compra_proveedor->getIdProducto() );
		}

		if( $detalle_compra_proveedor->getVariedad() != NULL){
			$sql .= " variedad = ? AND";
			array_push( $val, $detalle_compra_proveedor->getVariedad() );
		}

		if( $detalle_compra_proveedor->getArpillas() != NULL){
			$sql .= " arpillas = ? AND";
			array_push( $val, $detalle_compra_proveedor->getArpillas() );
		}

		if( $detalle_compra_proveedor->getKg() != NULL){
			$sql .= " kg = ? AND";
			array_push( $val, $detalle_compra_proveedor->getKg() );
		}

		if( $detalle_compra_proveedor->getPrecioPorKg() != NULL){
			$sql .= " precio_por_kg = ? AND";
			array_push( $val, $detalle_compra_proveedor->getPrecioPorKg() );
		}

		if(sizeof($val) == 0){return array();}
		$sql = substr($sql, 0, -3) . " )";
		if( $orderBy !== null ){
		    $sql .= " order by " . $orderBy . " " . $orden ;
		
		}
		global $conn;
		$rs = $conn->Execute($sql, $val);
		$ar = array();
		foreach ($rs as $foo) {
			$bar =  new DetalleCompraProveedor($foo);
    		array_push( $ar,$bar);
    		self::pushRecord( $bar, $foo["id_compra_proveedor"],$foo["id_producto"] );
		}
		return $ar;
	}


	/**
	  *	Actualizar registros.
	  *	
	  * Este metodo es un metodo de ayuda para uso interno. Se ejecutara todas las manipulaciones
	  * en la base de datos que estan dadas en el objeto pasado.No se haran consultas SELECT 
	  * aqui, sin embargo. El valor de retorno indica cuántas filas se vieron afectadas.
	  *	
	  * @internal private information for advanced developers only
	  * @return Filas afectadas o un string con la descripcion del error
	  * @param DetalleCompraProveedor [$detalle_compra_proveedor] El objeto de tipo DetalleCompraProveedor a actualizar.
	  **/
	private static final function update( $detalle_compra_proveedor )
	{
		$sql = "UPDATE detalle_compra_proveedor SET  variedad = ?, arpillas = ?, kg = ?, precio_por_kg = ? WHERE  id_compra_proveedor = ? AND id_producto = ?;";
		$params = array( 
			$detalle_compra_proveedor->getVariedad(), 
			$detalle_compra_proveedor->getArpillas(), 
			$detalle_compra_proveedor->getKg(), 
			$detalle_compra_proveedor->getPrecioPorKg(), 
			$detalle_compra_proveedor->getIdCompraProveedor(),$detalle_compra_proveedor->getIdProducto(), );
		global $conn;
		try{$conn->Execute($sql, $params);}
		catch(Exception $e){ throw new Exception ($e->getMessage()); }
		return $conn->Affected_Rows();
	}


	/**
	  *	Crear registros.
	  *	
	  * Este metodo creara una nueva fila en la base de datos de acuerdo con los 
	  * contenidos del objeto DetalleCompraProveedor suministrado. Asegurese
	  * de que los valores para todas las columnas NOT NULL se ha especificado 
	  * correctamente. Despues del comando INSERT, este metodo asignara la clave 
	  * primaria generada en el objeto DetalleCompraProveedor dentro de la misma transaccion.
	  *	
	  * @internal private information for advanced developers only
	  * @return Un entero mayor o igual a cero identificando las filas afectadas, en caso de error, regresara una cadena con la descripcion del error
	  * @param DetalleCompraProveedor [$detalle_compra_proveedor] El objeto de tipo DetalleCompraProveedor a crear.
	  **/
	private static final function create( &$detalle_compra_proveedor )
	{
		$sql = "INSERT INTO detalle_compra_proveedor ( id_compra_proveedor, id_producto, variedad, arpillas, kg, precio_por_kg ) VALUES ( ?, ?, ?, ?, ?, ?);";
		$params = array( 
			$detalle_compra_proveedor->getIdCompraProveedor(), 
			$detalle_compra_proveedor->getIdProducto(), 
			$detalle_compra_proveedor->getVariedad(), 
			$detalle_compra_proveedor->getArpillas(), 
			$detalle_compra_proveedor->getKg(), 
			$detalle_compra_proveedor->getPrecioPorKg(), 
		 );
		global $conn;
		try{$conn->Execute($sql, $params);}
		catch(Exception $e){ throw new Exception ($e->getMessage()); }
		$ar = $conn->Affected_Rows();
		if($ar == 0) return 0;
		/* save autoincremented value on obj */   /*  */ 
		return $ar;
	}


	/**
	  *	Buscar por rango.
	  *	
	  * Este metodo proporciona capacidad de busqueda para conseguir un juego de objetos {@link DetalleCompraProveedor} de la base de datos siempre y cuando 
	  * esten dentro del rango de atributos activos de dos objetos criterio de tipo {@link DetalleCompraProveedor}.
	  * 
	  * Aquellas variables que tienen valores NULL seran excluidos en la busqueda. 
	  * No es necesario ordenar los objetos criterio, asi como tambien es posible mezclar atributos.
	  * Si algun atributo solo esta especificado en solo uno de los objetos de criterio se buscara que los resultados conicidan exactamente en ese campo.
	  *	
	  * <code>
	  *  /**
	  *   * Ejemplo de uso - buscar todos los clientes que tengan limite de credito 
	  *   * mayor a 2000 y menor a 5000. Y que tengan un descuento del 50%.
	  *   {@*} 
	  *	  $cr1 = new Cliente();
	  *	  $cr1->setLimiteCredito("2000");
	  *	  $cr1->setDescuento("50");
	  *	  
	  *	  $cr2 = new Cliente();
	  *	  $cr2->setLimiteCredito("5000");
	  *	  $resultados = ClienteDAO::byRange($cr1, $cr2);
	  *	  
	  *	  foreach($resultados as $c ){
	  *	  	echo $c->getNombre() . "<br>";
	  *	  }
	  * </code>
	  *	@static
	  * @param DetalleCompraProveedor [$detalle_compra_proveedor] El objeto de tipo DetalleCompraProveedor
	  * @param DetalleCompraProveedor [$detalle_compra_proveedor] El objeto de tipo DetalleCompraProveedor
	  * @param $orderBy Debe ser una cadena con el nombre de una columna en la base de datos.
	  * @param $orden 'ASC' o 'DESC' el default es 'ASC'
	  **/
	public static final function byRange( $detalle_compra_proveedorA , $detalle_compra_proveedorB , $orderBy = null, $orden = 'ASC')
	{
		$sql = "SELECT * from detalle_compra_proveedor WHERE ("; 
		$val = array();
		if( (($a = $detalle_compra_proveedorA->getIdCompraProveedor()) != NULL) & ( ($b = $detalle_compra_proveedorB->getIdCompraProveedor()) != NULL) ){
				$sql .= " id_compra_proveedor >= ? AND id_compra_proveedor <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a || $b ){
			$sql .= " id_compra_proveedor = ? AND"; 
			$a = $a == NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		if( (($a = $detalle_compra_proveedorA->getIdProducto()) != NULL) & ( ($b = $detalle_compra_proveedorB->getIdProducto()) != NULL) ){
				$sql .= " id_producto >= ? AND id_producto <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a || $b ){
			$sql .= " id_producto = ? AND"; 
			$a = $a == NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		if( (($a = $detalle_compra_proveedorA->getVariedad()) != NULL) & ( ($b = $detalle_compra_proveedorB->getVariedad()) != NULL) ){
				$sql .= " variedad >= ? AND variedad <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a || $b ){
			$sql .= " variedad = ? AND"; 
			$a = $a == NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		if( (($a = $detalle_compra_proveedorA->getArpillas()) != NULL) & ( ($b = $detalle_compra_proveedorB->getArpillas()) != NULL) ){
				$sql .= " arpillas >= ? AND arpillas <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a || $b ){
			$sql .= " arpillas = ? AND"; 
			$a = $a == NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		if( (($a = $detalle_compra_proveedorA->getKg()) != NULL) & ( ($b = $detalle_compra_proveedorB->getKg()) != NULL) ){
				$sql .= " kg >= ? AND kg <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a || $b ){
			$sql .= " kg = ? AND"; 
			$a = $a == NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		if( (($a = $detalle_compra_proveedorA->getPrecioPorKg()) != NULL) & ( ($b = $detalle_compra_proveedorB->getPrecioPorKg()) != NULL) ){
				$sql .= " precio_por_kg >= ? AND precio_por_kg <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a || $b ){
			$sql .= " precio_por_kg = ? AND"; 
			$a = $a == NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		$sql = substr($sql, 0, -3) . " )";
		if( $orderBy !== null ){
		    $sql .= " order by " . $orderBy . " " . $orden ;
		
		}
		global $conn;
		$rs = $conn->Execute($sql, $val);
		$ar = array();
		foreach ($rs as $foo) {
    		array_push( $ar, new DetalleCompraProveedor($foo));
		}
		return $ar;
	}


	/**
	  *	Eliminar registros.
	  *	
	  * Este metodo eliminara la informacion de base de datos identificados por la clave primaria
	  * en el objeto DetalleCompraProveedor suministrado. Una vez que se ha suprimido un objeto, este no 
	  * puede ser restaurado llamando a save(). save() al ver que este es un objeto vacio, creara una nueva fila 
	  * pero el objeto resultante tendra una clave primaria diferente de la que estaba en el objeto eliminado. 
	  * Si no puede encontrar eliminar fila coincidente a eliminar, Exception sera lanzada.
	  *	
	  *	@throws Exception Se arroja cuando el objeto no tiene definidas sus llaves primarias.
	  *	@return int El numero de filas afectadas.
	  * @param DetalleCompraProveedor [$detalle_compra_proveedor] El objeto de tipo DetalleCompraProveedor a eliminar
	  **/
	public static final function delete( &$detalle_compra_proveedor )
	{
		if(self::getByPK($detalle_compra_proveedor->getIdCompraProveedor(), $detalle_compra_proveedor->getIdProducto()) === NULL) throw new Exception('Campo no encontrado.');
		$sql = "DELETE FROM detalle_compra_proveedor WHERE  id_compra_proveedor = ? AND id_producto = ?;";
		$params = array( $detalle_compra_proveedor->getIdCompraProveedor(), $detalle_compra_proveedor->getIdProducto() );
		global $conn;

		$conn->Execute($sql, $params);
		return $conn->Affected_Rows();
	}


}
