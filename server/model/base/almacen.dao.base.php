<?php
/** Almacen Data Access Object (DAO) Base.
  * 
  * Esta clase contiene toda la manipulacion de bases de datos que se necesita para 
  * almacenar de forma permanente y recuperar instancias de objetos {@link Almacen }. 
  * @author Andres
  * @access private
  * @abstract
  * @package docs
  * 
  */
abstract class AlmacenDAOBase extends DAO
{

		private static $loadedRecords = array();

		private static function recordExists(  $id_almacen ){
			$pk = "";
			$pk .= $id_almacen . "-";
			return array_key_exists ( $pk , self::$loadedRecords );
		}
		private static function pushRecord( $inventario,  $id_almacen){
			$pk = "";
			$pk .= $id_almacen . "-";
			self::$loadedRecords [$pk] = $inventario;
		}
		private static function getRecord(  $id_almacen ){
			$pk = "";
			$pk .= $id_almacen . "-";
			return self::$loadedRecords[$pk];
		}
	/**
	  *	Guardar registros. 
	  *	
	  *	Este metodo guarda el estado actual del objeto {@link Almacen} pasado en la base de datos. La llave 
	  *	primaria indicara que instancia va a ser actualizado en base de datos. Si la llave primara o combinacion de llaves
	  *	primarias describen una fila que no se encuentra en la base de datos, entonces save() creara una nueva fila, insertando
	  *	en ese objeto el ID recien creado.
	  *	
	  *	@static
	  * @throws Exception si la operacion fallo.
	  * @param Almacen [$almacen] El objeto de tipo Almacen
	  * @return Un entero mayor o igual a cero denotando las filas afectadas.
	  **/
	public static final function save( &$almacen )
	{
		if(  self::getByPK(  $almacen->getIdAlmacen() ) !== NULL )
		{
			try{ return AlmacenDAOBase::update( $almacen) ; } catch(Exception $e){ throw $e; }
		}else{
			try{ return AlmacenDAOBase::create( $almacen) ; } catch(Exception $e){ throw $e; }
		}
	}


	/**
	  *	Obtener {@link Almacen} por llave primaria. 
	  *	
	  * Este metodo cargara un objeto {@link Almacen} de la base de datos 
	  * usando sus llaves primarias. 
	  *	
	  *	@static
	  * @return @link Almacen Un objeto del tipo {@link Almacen}. NULL si no hay tal registro.
	  **/
	public static final function getByPK(  $id_almacen )
	{
		if(self::recordExists(  $id_almacen)){
			return self::getRecord( $id_almacen );
		}
		$sql = "SELECT * FROM almacen WHERE (id_almacen = ? ) LIMIT 1;";
		$params = array(  $id_almacen );
		global $conn;
		$rs = $conn->GetRow($sql, $params);
		if(count($rs)==0)return NULL;
			$foo = new Almacen( $rs );
			self::pushRecord( $foo,  $id_almacen );
			return $foo;
	}


	/**
	  *	Obtener todas las filas.
	  *	
	  * Esta funcion leera todos los contenidos de la tabla en la base de datos y construira
	  * un vector que contiene objetos de tipo {@link Almacen}. Tenga en cuenta que este metodo
	  * consumen enormes cantidades de recursos si la tabla tiene muchas filas. 
	  * Este metodo solo debe usarse cuando las tablas destino tienen solo pequenas cantidades de datos o se usan sus parametros para obtener un menor numero de filas.
	  *	
	  *	@static
	  * @param $pagina Pagina a ver.
	  * @param $columnas_por_pagina Columnas por pagina.
	  * @param $orden Debe ser una cadena con el nombre de una columna en la base de datos.
	  * @param $tipo_de_orden 'ASC' o 'DESC' el default es 'ASC'
	  * @return Array Un arreglo que contiene objetos del tipo {@link Almacen}.
	  **/
	public static final function getAll( $pagina = NULL, $columnas_por_pagina = NULL, $orden = NULL, $tipo_de_orden = 'ASC' )
	{
		$sql = "SELECT * from almacen";
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
			$bar = new Almacen($foo);
    		array_push( $allData, $bar);
			//id_almacen
    		self::pushRecord( $bar, $foo["id_almacen"] );
		}
		return $allData;
	}


	/**
	  *	Buscar registros.
	  *	
	  * Este metodo proporciona capacidad de busqueda para conseguir un juego de objetos {@link Almacen} de la base de datos. 
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
	  * @param Almacen [$almacen] El objeto de tipo Almacen
	  * @param $orderBy Debe ser una cadena con el nombre de una columna en la base de datos.
	  * @param $orden 'ASC' o 'DESC' el default es 'ASC'
	  **/
	public static final function search( $almacen , $orderBy = null, $orden = 'ASC')
	{
		$sql = "SELECT * from almacen WHERE ("; 
		$val = array();
		if( $almacen->getIdAlmacen() != NULL){
			$sql .= " id_almacen = ? AND";
			array_push( $val, $almacen->getIdAlmacen() );
		}

		if( $almacen->getIdSucursal() != NULL){
			$sql .= " id_sucursal = ? AND";
			array_push( $val, $almacen->getIdSucursal() );
		}

		if( $almacen->getIdEmpresa() != NULL){
			$sql .= " id_empresa = ? AND";
			array_push( $val, $almacen->getIdEmpresa() );
		}

		if( $almacen->getIdTipoAlmacen() != NULL){
			$sql .= " id_tipo_almacen = ? AND";
			array_push( $val, $almacen->getIdTipoAlmacen() );
		}

		if( $almacen->getNombre() != NULL){
			$sql .= " nombre = ? AND";
			array_push( $val, $almacen->getNombre() );
		}

		if( $almacen->getDescripcion() != NULL){
			$sql .= " descripcion = ? AND";
			array_push( $val, $almacen->getDescripcion() );
		}

		if( $almacen->getActivo() != NULL){
			$sql .= " activo = ? AND";
			array_push( $val, $almacen->getActivo() );
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
			$bar =  new Almacen($foo);
    		array_push( $ar,$bar);
    		self::pushRecord( $bar, $foo["id_almacen"] );
		}
		return $ar;
	}


	/**
	  *	Actualizar registros.
	  *	
	  * Este metodo es un metodo de ayuda para uso interno. Se ejecutara todas las manipulaciones
	  * en la base de datos que estan dadas en el objeto pasado.No se haran consultas SELECT 
	  * aqui, sin embargo. El valor de retorno indica cu�ntas filas se vieron afectadas.
	  *	
	  * @internal private information for advanced developers only
	  * @return Filas afectadas o un string con la descripcion del error
	  * @param Almacen [$almacen] El objeto de tipo Almacen a actualizar.
	  **/
	private static final function update( $almacen )
	{
		$sql = "UPDATE almacen SET  id_sucursal = ?, id_empresa = ?, id_tipo_almacen = ?, nombre = ?, descripcion = ?, activo = ? WHERE  id_almacen = ?;";
		$params = array( 
			$almacen->getIdSucursal(), 
			$almacen->getIdEmpresa(), 
			$almacen->getIdTipoAlmacen(), 
			$almacen->getNombre(), 
			$almacen->getDescripcion(), 
			$almacen->getActivo(), 
			$almacen->getIdAlmacen(), );
		global $conn;
		try{$conn->Execute($sql, $params);}
		catch(Exception $e){ throw new Exception ($e->getMessage()); }
		return $conn->Affected_Rows();
	}


	/**
	  *	Crear registros.
	  *	
	  * Este metodo creara una nueva fila en la base de datos de acuerdo con los 
	  * contenidos del objeto Almacen suministrado. Asegurese
	  * de que los valores para todas las columnas NOT NULL se ha especificado 
	  * correctamente. Despues del comando INSERT, este metodo asignara la clave 
	  * primaria generada en el objeto Almacen dentro de la misma transaccion.
	  *	
	  * @internal private information for advanced developers only
	  * @return Un entero mayor o igual a cero identificando las filas afectadas, en caso de error, regresara una cadena con la descripcion del error
	  * @param Almacen [$almacen] El objeto de tipo Almacen a crear.
	  **/
	private static final function create( &$almacen )
	{
		$sql = "INSERT INTO almacen ( id_almacen, id_sucursal, id_empresa, id_tipo_almacen, nombre, descripcion, activo ) VALUES ( ?, ?, ?, ?, ?, ?, ?);";
		$params = array( 
			$almacen->getIdAlmacen(), 
			$almacen->getIdSucursal(), 
			$almacen->getIdEmpresa(), 
			$almacen->getIdTipoAlmacen(), 
			$almacen->getNombre(), 
			$almacen->getDescripcion(), 
			$almacen->getActivo(), 
		 );
		global $conn;
		try{$conn->Execute($sql, $params);}
		catch(Exception $e){ throw new Exception ($e->getMessage()); }
		$ar = $conn->Affected_Rows();
		if($ar == 0) return 0;
		/* save autoincremented value on obj */  $almacen->setIdAlmacen( $conn->Insert_ID() ); /*  */ 
		return $ar;
	}


	/**
	  *	Buscar por rango.
	  *	
	  * Este metodo proporciona capacidad de busqueda para conseguir un juego de objetos {@link Almacen} de la base de datos siempre y cuando 
	  * esten dentro del rango de atributos activos de dos objetos criterio de tipo {@link Almacen}.
	  * 
	  * Aquellas variables que tienen valores NULL seran excluidos en la busqueda (los valores 0 y false no son tomados como NULL) .
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
	  * @param Almacen [$almacen] El objeto de tipo Almacen
	  * @param Almacen [$almacen] El objeto de tipo Almacen
	  * @param $orderBy Debe ser una cadena con el nombre de una columna en la base de datos.
	  * @param $orden 'ASC' o 'DESC' el default es 'ASC'
	  **/
	public static final function byRange( $almacenA , $almacenB , $orderBy = null, $orden = 'ASC')
	{
		$sql = "SELECT * from almacen WHERE ("; 
		$val = array();
		if( (($a = $almacenA->getIdAlmacen()) !== NULL) & ( ($b = $almacenB->getIdAlmacen()) !== NULL) ){
				$sql .= " id_almacen >= ? AND id_almacen <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a !== NULL|| $b !== NULL ){
			$sql .= " id_almacen = ? AND"; 
			$a = $a === NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		if( (($a = $almacenA->getIdSucursal()) !== NULL) & ( ($b = $almacenB->getIdSucursal()) !== NULL) ){
				$sql .= " id_sucursal >= ? AND id_sucursal <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a !== NULL|| $b !== NULL ){
			$sql .= " id_sucursal = ? AND"; 
			$a = $a === NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		if( (($a = $almacenA->getIdEmpresa()) !== NULL) & ( ($b = $almacenB->getIdEmpresa()) !== NULL) ){
				$sql .= " id_empresa >= ? AND id_empresa <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a !== NULL|| $b !== NULL ){
			$sql .= " id_empresa = ? AND"; 
			$a = $a === NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		if( (($a = $almacenA->getIdTipoAlmacen()) !== NULL) & ( ($b = $almacenB->getIdTipoAlmacen()) !== NULL) ){
				$sql .= " id_tipo_almacen >= ? AND id_tipo_almacen <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a !== NULL|| $b !== NULL ){
			$sql .= " id_tipo_almacen = ? AND"; 
			$a = $a === NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		if( (($a = $almacenA->getNombre()) !== NULL) & ( ($b = $almacenB->getNombre()) !== NULL) ){
				$sql .= " nombre >= ? AND nombre <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a !== NULL|| $b !== NULL ){
			$sql .= " nombre = ? AND"; 
			$a = $a === NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		if( (($a = $almacenA->getDescripcion()) !== NULL) & ( ($b = $almacenB->getDescripcion()) !== NULL) ){
				$sql .= " descripcion >= ? AND descripcion <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a !== NULL|| $b !== NULL ){
			$sql .= " descripcion = ? AND"; 
			$a = $a === NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		if( (($a = $almacenA->getActivo()) !== NULL) & ( ($b = $almacenB->getActivo()) !== NULL) ){
				$sql .= " activo >= ? AND activo <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a !== NULL|| $b !== NULL ){
			$sql .= " activo = ? AND"; 
			$a = $a === NULL ? $b : $a;
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
    		array_push( $ar, new Almacen($foo));
		}
		return $ar;
	}


	/**
	  *	Eliminar registros.
	  *	
	  * Este metodo eliminara la informacion de base de datos identificados por la clave primaria
	  * en el objeto Almacen suministrado. Una vez que se ha suprimido un objeto, este no 
	  * puede ser restaurado llamando a save(). save() al ver que este es un objeto vacio, creara una nueva fila 
	  * pero el objeto resultante tendra una clave primaria diferente de la que estaba en el objeto eliminado. 
	  * Si no puede encontrar eliminar fila coincidente a eliminar, Exception sera lanzada.
	  *	
	  *	@throws Exception Se arroja cuando el objeto no tiene definidas sus llaves primarias.
	  *	@return int El numero de filas afectadas.
	  * @param Almacen [$almacen] El objeto de tipo Almacen a eliminar
	  **/
	public static final function delete( &$almacen )
	{
		if(self::getByPK($almacen->getIdAlmacen()) === NULL) throw new Exception('Campo no encontrado.');
		$sql = "DELETE FROM almacen WHERE  id_almacen = ?;";
		$params = array( $almacen->getIdAlmacen() );
		global $conn;

		$conn->Execute($sql, $params);
		return $conn->Affected_Rows();
	}


}