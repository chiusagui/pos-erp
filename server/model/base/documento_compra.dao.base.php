<?php
/** DocumentoCompra Data Access Object (DAO) Base.
  * 
  * Esta clase contiene toda la manipulacion de bases de datos que se necesita para 
  * almacenar de forma permanente y recuperar instancias de objetos {@link DocumentoCompra }. 
  * @author Andres
  * @access private
  * @abstract
  * @package docs
  * 
  */
abstract class DocumentoCompraDAOBase extends DAO
{

		private static $loadedRecords = array();

		private static function recordExists(  $id_documento, $id_compra ){
			$pk = "";
			$pk .= $id_documento . "-";
			$pk .= $id_compra . "-";
			return array_key_exists ( $pk , self::$loadedRecords );
		}
		private static function pushRecord( $inventario,  $id_documento, $id_compra){
			$pk = "";
			$pk .= $id_documento . "-";
			$pk .= $id_compra . "-";
			self::$loadedRecords [$pk] = $inventario;
		}
		private static function getRecord(  $id_documento, $id_compra ){
			$pk = "";
			$pk .= $id_documento . "-";
			$pk .= $id_compra . "-";
			return self::$loadedRecords[$pk];
		}
	/**
	  *	Guardar registros. 
	  *	
	  *	Este metodo guarda el estado actual del objeto {@link DocumentoCompra} pasado en la base de datos. La llave 
	  *	primaria indicara que instancia va a ser actualizado en base de datos. Si la llave primara o combinacion de llaves
	  *	primarias describen una fila que no se encuentra en la base de datos, entonces save() creara una nueva fila, insertando
	  *	en ese objeto el ID recien creado.
	  *	
	  *	@static
	  * @throws Exception si la operacion fallo.
	  * @param DocumentoCompra [$documento_compra] El objeto de tipo DocumentoCompra
	  * @return Un entero mayor o igual a cero denotando las filas afectadas.
	  **/
	public static final function save( &$documento_compra )
	{
		if(  self::getByPK(  $documento_compra->getIdDocumento() , $documento_compra->getIdCompra() ) !== NULL )
		{
			try{ return DocumentoCompraDAOBase::update( $documento_compra) ; } catch(Exception $e){ throw $e; }
		}else{
			try{ return DocumentoCompraDAOBase::create( $documento_compra) ; } catch(Exception $e){ throw $e; }
		}
	}


	/**
	  *	Obtener {@link DocumentoCompra} por llave primaria. 
	  *	
	  * Este metodo cargara un objeto {@link DocumentoCompra} de la base de datos 
	  * usando sus llaves primarias. 
	  *	
	  *	@static
	  * @return @link DocumentoCompra Un objeto del tipo {@link DocumentoCompra}. NULL si no hay tal registro.
	  **/
	public static final function getByPK(  $id_documento, $id_compra )
	{
		if(self::recordExists(  $id_documento, $id_compra)){
			return self::getRecord( $id_documento, $id_compra );
		}
		$sql = "SELECT * FROM documento_compra WHERE (id_documento = ? AND id_compra = ? ) LIMIT 1;";
		$params = array(  $id_documento, $id_compra );
		global $conn;
		$rs = $conn->GetRow($sql, $params);
		if(count($rs)==0)return NULL;
			$foo = new DocumentoCompra( $rs );
			self::pushRecord( $foo,  $id_documento, $id_compra );
			return $foo;
	}


	/**
	  *	Obtener todas las filas.
	  *	
	  * Esta funcion leera todos los contenidos de la tabla en la base de datos y construira
	  * un vector que contiene objetos de tipo {@link DocumentoCompra}. Tenga en cuenta que este metodo
	  * consumen enormes cantidades de recursos si la tabla tiene muchas filas. 
	  * Este metodo solo debe usarse cuando las tablas destino tienen solo pequenas cantidades de datos o se usan sus parametros para obtener un menor numero de filas.
	  *	
	  *	@static
	  * @param $pagina Pagina a ver.
	  * @param $columnas_por_pagina Columnas por pagina.
	  * @param $orden Debe ser una cadena con el nombre de una columna en la base de datos.
	  * @param $tipo_de_orden 'ASC' o 'DESC' el default es 'ASC'
	  * @return Array Un arreglo que contiene objetos del tipo {@link DocumentoCompra}.
	  **/
	public static final function getAll( $pagina = NULL, $columnas_por_pagina = NULL, $orden = NULL, $tipo_de_orden = 'ASC' )
	{
		$sql = "SELECT * from documento_compra";
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
			$bar = new DocumentoCompra($foo);
    		array_push( $allData, $bar);
			//id_documento
			//id_compra
    		self::pushRecord( $bar, $foo["id_documento"],$foo["id_compra"] );
		}
		return $allData;
	}


	/**
	  *	Buscar registros.
	  *	
	  * Este metodo proporciona capacidad de busqueda para conseguir un juego de objetos {@link DocumentoCompra} de la base de datos. 
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
	  * @param DocumentoCompra [$documento_compra] El objeto de tipo DocumentoCompra
	  * @param $orderBy Debe ser una cadena con el nombre de una columna en la base de datos.
	  * @param $orden 'ASC' o 'DESC' el default es 'ASC'
	  **/
	public static final function search( $documento_compra , $orderBy = null, $orden = 'ASC')
	{
		$sql = "SELECT * from documento_compra WHERE ("; 
		$val = array();
		if( $documento_compra->getIdDocumento() != NULL){
			$sql .= " id_documento = ? AND";
			array_push( $val, $documento_compra->getIdDocumento() );
		}

		if( $documento_compra->getIdCompra() != NULL){
			$sql .= " id_compra = ? AND";
			array_push( $val, $documento_compra->getIdCompra() );
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
			$bar =  new DocumentoCompra($foo);
    		array_push( $ar,$bar);
    		self::pushRecord( $bar, $foo["id_documento"],$foo["id_compra"] );
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
	  * @param DocumentoCompra [$documento_compra] El objeto de tipo DocumentoCompra a actualizar.
	  **/
	private static final function update( $documento_compra )
	{
	}


	/**
	  *	Crear registros.
	  *	
	  * Este metodo creara una nueva fila en la base de datos de acuerdo con los 
	  * contenidos del objeto DocumentoCompra suministrado. Asegurese
	  * de que los valores para todas las columnas NOT NULL se ha especificado 
	  * correctamente. Despues del comando INSERT, este metodo asignara la clave 
	  * primaria generada en el objeto DocumentoCompra dentro de la misma transaccion.
	  *	
	  * @internal private information for advanced developers only
	  * @return Un entero mayor o igual a cero identificando las filas afectadas, en caso de error, regresara una cadena con la descripcion del error
	  * @param DocumentoCompra [$documento_compra] El objeto de tipo DocumentoCompra a crear.
	  **/
	private static final function create( &$documento_compra )
	{
		$sql = "INSERT INTO documento_compra ( id_documento, id_compra ) VALUES ( ?, ?);";
		$params = array( 
			$documento_compra->getIdDocumento(), 
			$documento_compra->getIdCompra(), 
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
	  * Este metodo proporciona capacidad de busqueda para conseguir un juego de objetos {@link DocumentoCompra} de la base de datos siempre y cuando 
	  * esten dentro del rango de atributos activos de dos objetos criterio de tipo {@link DocumentoCompra}.
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
	  * @param DocumentoCompra [$documento_compra] El objeto de tipo DocumentoCompra
	  * @param DocumentoCompra [$documento_compra] El objeto de tipo DocumentoCompra
	  * @param $orderBy Debe ser una cadena con el nombre de una columna en la base de datos.
	  * @param $orden 'ASC' o 'DESC' el default es 'ASC'
	  **/
	public static final function byRange( $documento_compraA , $documento_compraB , $orderBy = null, $orden = 'ASC')
	{
		$sql = "SELECT * from documento_compra WHERE ("; 
		$val = array();
		if( (($a = $documento_compraA->getIdDocumento()) !== NULL) & ( ($b = $documento_compraB->getIdDocumento()) !== NULL) ){
				$sql .= " id_documento >= ? AND id_documento <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a !== NULL|| $b !== NULL ){
			$sql .= " id_documento = ? AND"; 
			$a = $a === NULL ? $b : $a;
			array_push( $val, $a);
			
		}

		if( (($a = $documento_compraA->getIdCompra()) !== NULL) & ( ($b = $documento_compraB->getIdCompra()) !== NULL) ){
				$sql .= " id_compra >= ? AND id_compra <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( $a !== NULL|| $b !== NULL ){
			$sql .= " id_compra = ? AND"; 
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
    		array_push( $ar, new DocumentoCompra($foo));
		}
		return $ar;
	}


	/**
	  *	Eliminar registros.
	  *	
	  * Este metodo eliminara la informacion de base de datos identificados por la clave primaria
	  * en el objeto DocumentoCompra suministrado. Una vez que se ha suprimido un objeto, este no 
	  * puede ser restaurado llamando a save(). save() al ver que este es un objeto vacio, creara una nueva fila 
	  * pero el objeto resultante tendra una clave primaria diferente de la que estaba en el objeto eliminado. 
	  * Si no puede encontrar eliminar fila coincidente a eliminar, Exception sera lanzada.
	  *	
	  *	@throws Exception Se arroja cuando el objeto no tiene definidas sus llaves primarias.
	  *	@return int El numero de filas afectadas.
	  * @param DocumentoCompra [$documento_compra] El objeto de tipo DocumentoCompra a eliminar
	  **/
	public static final function delete( &$documento_compra )
	{
		if(self::getByPK($documento_compra->getIdDocumento(), $documento_compra->getIdCompra()) === NULL) throw new Exception('Campo no encontrado.');
		$sql = "DELETE FROM documento_compra WHERE  id_documento = ? AND id_compra = ?;";
		$params = array( $documento_compra->getIdDocumento(), $documento_compra->getIdCompra() );
		global $conn;

		$conn->Execute($sql, $params);
		return $conn->Affected_Rows();
	}


}