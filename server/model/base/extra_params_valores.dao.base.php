<?php
/** ExtraParamsValores Data Access Object (DAO) Base.
  * 
  * Esta clase contiene toda la manipulacion de bases de datos que se necesita para 
  * almacenar de forma permanente y recuperar instancias de objetos {@link ExtraParamsValores }. 
  * @author Anonymous
  * @access private
  * @abstract
  * @package docs
  * 
  */
abstract class ExtraParamsValoresDAOBase extends DAO
{

	/**
	  *	Guardar registros. 
	  *	
	  *	Este metodo guarda el estado actual del objeto {@link ExtraParamsValores} pasado en la base de datos. La llave 
	  *	primaria indicara que instancia va a ser actualizado en base de datos. Si la llave primara o combinacion de llaves
	  *	primarias describen una fila que no se encuentra en la base de datos, entonces save() creara una nueva fila, insertando
	  *	en ese objeto el ID recien creado.
	  *	
	  *	@static
	  * @throws Exception si la operacion fallo.
	  * @param ExtraParamsValores [$extra_params_valores] El objeto de tipo ExtraParamsValores
	  * @return Un entero mayor o igual a cero denotando las filas afectadas.
	  **/
	public static final function save( &$extra_params_valores )
	{
		if( ! is_null ( self::getByPK(  $extra_params_valores->getIdExtraParamsValores() ) ) )
		{
			try{ return ExtraParamsValoresDAOBase::update( $extra_params_valores) ; } catch(Exception $e){ throw $e; }
		}else{
			try{ return ExtraParamsValoresDAOBase::create( $extra_params_valores) ; } catch(Exception $e){ throw $e; }
		}
	}


	/**
	  *	Obtener {@link ExtraParamsValores} por llave primaria. 
	  *	
	  * Este metodo cargara un objeto {@link ExtraParamsValores} de la base de datos 
	  * usando sus llaves primarias. 
	  *	
	  *	@static
	  * @return @link ExtraParamsValores Un objeto del tipo {@link ExtraParamsValores}. NULL si no hay tal registro.
	  **/
	public static final function getByPK(  $id_extra_params_valores )
	{
		$sql = "SELECT * FROM extra_params_valores WHERE (id_extra_params_valores = ? ) LIMIT 1;";
		$params = array(  $id_extra_params_valores );
		global $conn;
		$rs = $conn->GetRow($sql, $params);
		if(count($rs)==0)return NULL;
			$foo = new ExtraParamsValores( $rs );
			return $foo;
	}


	/**
	  *	Obtener todas las filas.
	  *	
	  * Esta funcion leera todos los contenidos de la tabla en la base de datos y construira
	  * un vector que contiene objetos de tipo {@link ExtraParamsValores}. Tenga en cuenta que este metodo
	  * consumen enormes cantidades de recursos si la tabla tiene muchas filas. 
	  * Este metodo solo debe usarse cuando las tablas destino tienen solo pequenas cantidades de datos o se usan sus parametros para obtener un menor numero de filas.
	  *	
	  *	@static
	  * @param $pagina Pagina a ver.
	  * @param $columnas_por_pagina Columnas por pagina.
	  * @param $orden Debe ser una cadena con el nombre de una columna en la base de datos.
	  * @param $tipo_de_orden 'ASC' o 'DESC' el default es 'ASC'
	  * @return Array Un arreglo que contiene objetos del tipo {@link ExtraParamsValores}.
	  **/
	public static final function getAll( $pagina = NULL, $columnas_por_pagina = NULL, $orden = NULL, $tipo_de_orden = 'ASC' )
	{
		$sql = "SELECT * from extra_params_valores";
		if( ! is_null ( $orden ) )
		{ $sql .= " ORDER BY " . $orden . " " . $tipo_de_orden;	}
		if( ! is_null ( $pagina ) )
		{
			$sql .= " LIMIT " . (( $pagina - 1 )*$columnas_por_pagina) . "," . $columnas_por_pagina; 
		}
		global $conn;
		$rs = $conn->Execute($sql);
		$allData = array();
		foreach ($rs as $foo) {
			$bar = new ExtraParamsValores($foo);
    		array_push( $allData, $bar);
			//id_extra_params_valores
		}
		return $allData;
	}


	/**
	  *	Buscar registros.
	  *	
	  * Este metodo proporciona capacidad de busqueda para conseguir un juego de objetos {@link ExtraParamsValores} de la base de datos. 
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
	  * @param ExtraParamsValores [$extra_params_valores] El objeto de tipo ExtraParamsValores
	  * @param $orderBy Debe ser una cadena con el nombre de una columna en la base de datos.
	  * @param $orden 'ASC' o 'DESC' el default es 'ASC'
	  **/
	public static final function search( $extra_params_valores , $orderBy = null, $orden = 'ASC')
	{
		$sql = "SELECT * from extra_params_valores WHERE ("; 
		$val = array();
		if( ! is_null( $extra_params_valores->getIdExtraParamsValores() ) ){
			$sql .= " `id_extra_params_valores` = ? AND";
			array_push( $val, $extra_params_valores->getIdExtraParamsValores() );
		}

		if( ! is_null( $extra_params_valores->getIdExtraParamsEstructura() ) ){
			$sql .= " `id_extra_params_estructura` = ? AND";
			array_push( $val, $extra_params_valores->getIdExtraParamsEstructura() );
		}

		if( ! is_null( $extra_params_valores->getIdPkTabla() ) ){
			$sql .= " `id_pk_tabla` = ? AND";
			array_push( $val, $extra_params_valores->getIdPkTabla() );
		}

		if( ! is_null( $extra_params_valores->getVal() ) ){
			$sql .= " `val` = ? AND";
			array_push( $val, $extra_params_valores->getVal() );
		}

		if(sizeof($val) == 0){return self::getAll(/* $pagina = NULL, $columnas_por_pagina = NULL, $orden = NULL, $tipo_de_orden = 'ASC' */);}
		$sql = substr($sql, 0, -3) . " )";
		if( ! is_null ( $orderBy ) ){
		    $sql .= " order by " . $orderBy . " " . $orden ;
		
		}
		global $conn;
		$rs = $conn->Execute($sql, $val);
		$ar = array();
		foreach ($rs as $foo) {
			$bar =  new ExtraParamsValores($foo);
    		array_push( $ar,$bar);
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
	  * @param ExtraParamsValores [$extra_params_valores] El objeto de tipo ExtraParamsValores a actualizar.
	  **/
	private static final function update( $extra_params_valores )
	{
		$sql = "UPDATE extra_params_valores SET  `id_extra_params_estructura` = ?, `id_pk_tabla` = ?, `val` = ? WHERE  `id_extra_params_valores` = ?;";
		$params = array( 
			$extra_params_valores->getIdExtraParamsEstructura(), 
			$extra_params_valores->getIdPkTabla(), 
			$extra_params_valores->getVal(), 
			$extra_params_valores->getIdExtraParamsValores(), );
		global $conn;
		try{$conn->Execute($sql, $params);}
		catch(Exception $e){ throw new Exception ($e->getMessage()); }
		return $conn->Affected_Rows();
	}


	/**
	  *	Crear registros.
	  *	
	  * Este metodo creara una nueva fila en la base de datos de acuerdo con los 
	  * contenidos del objeto ExtraParamsValores suministrado. Asegurese
	  * de que los valores para todas las columnas NOT NULL se ha especificado 
	  * correctamente. Despues del comando INSERT, este metodo asignara la clave 
	  * primaria generada en el objeto ExtraParamsValores dentro de la misma transaccion.
	  *	
	  * @internal private information for advanced developers only
	  * @return Un entero mayor o igual a cero identificando las filas afectadas, en caso de error, regresara una cadena con la descripcion del error
	  * @param ExtraParamsValores [$extra_params_valores] El objeto de tipo ExtraParamsValores a crear.
	  **/
	private static final function create( &$extra_params_valores )
	{
		$sql = "INSERT INTO extra_params_valores ( `id_extra_params_valores`, `id_extra_params_estructura`, `id_pk_tabla`, `val` ) VALUES ( ?, ?, ?, ?);";
		$params = array( 
			$extra_params_valores->getIdExtraParamsValores(), 
			$extra_params_valores->getIdExtraParamsEstructura(), 
			$extra_params_valores->getIdPkTabla(), 
			$extra_params_valores->getVal(), 
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
	  * Este metodo proporciona capacidad de busqueda para conseguir un juego de objetos {@link ExtraParamsValores} de la base de datos siempre y cuando 
	  * esten dentro del rango de atributos activos de dos objetos criterio de tipo {@link ExtraParamsValores}.
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
	  * @param ExtraParamsValores [$extra_params_valores] El objeto de tipo ExtraParamsValores
	  * @param ExtraParamsValores [$extra_params_valores] El objeto de tipo ExtraParamsValores
	  * @param $orderBy Debe ser una cadena con el nombre de una columna en la base de datos.
	  * @param $orden 'ASC' o 'DESC' el default es 'ASC'
	  **/
	public static final function byRange( $extra_params_valoresA , $extra_params_valoresB , $orderBy = null, $orden = 'ASC')
	{
		$sql = "SELECT * from extra_params_valores WHERE ("; 
		$val = array();
		if( ( !is_null (($a = $extra_params_valoresA->getIdExtraParamsValores()) ) ) & ( ! is_null ( ($b = $extra_params_valoresB->getIdExtraParamsValores()) ) ) ){
				$sql .= " `id_extra_params_valores` >= ? AND `id_extra_params_valores` <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( !is_null ( $a ) || !is_null ( $b ) ){
			$sql .= " `id_extra_params_valores` = ? AND"; 
			$a = is_null ( $a ) ? $b : $a;
			array_push( $val, $a);
			
		}

		if( ( !is_null (($a = $extra_params_valoresA->getIdExtraParamsEstructura()) ) ) & ( ! is_null ( ($b = $extra_params_valoresB->getIdExtraParamsEstructura()) ) ) ){
				$sql .= " `id_extra_params_estructura` >= ? AND `id_extra_params_estructura` <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( !is_null ( $a ) || !is_null ( $b ) ){
			$sql .= " `id_extra_params_estructura` = ? AND"; 
			$a = is_null ( $a ) ? $b : $a;
			array_push( $val, $a);
			
		}

		if( ( !is_null (($a = $extra_params_valoresA->getIdPkTabla()) ) ) & ( ! is_null ( ($b = $extra_params_valoresB->getIdPkTabla()) ) ) ){
				$sql .= " `id_pk_tabla` >= ? AND `id_pk_tabla` <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( !is_null ( $a ) || !is_null ( $b ) ){
			$sql .= " `id_pk_tabla` = ? AND"; 
			$a = is_null ( $a ) ? $b : $a;
			array_push( $val, $a);
			
		}

		if( ( !is_null (($a = $extra_params_valoresA->getVal()) ) ) & ( ! is_null ( ($b = $extra_params_valoresB->getVal()) ) ) ){
				$sql .= " `val` >= ? AND `val` <= ? AND";
				array_push( $val, min($a,$b)); 
				array_push( $val, max($a,$b)); 
		}elseif( !is_null ( $a ) || !is_null ( $b ) ){
			$sql .= " `val` = ? AND"; 
			$a = is_null ( $a ) ? $b : $a;
			array_push( $val, $a);
			
		}

		$sql = substr($sql, 0, -3) . " )";
		if( !is_null ( $orderBy ) ){
		    $sql .= " order by " . $orderBy . " " . $orden ;
		
		}
		global $conn;
		$rs = $conn->Execute($sql, $val);
		$ar = array();
		foreach ($rs as $foo) {
    		array_push( $ar, new ExtraParamsValores($foo));
		}
		return $ar;
	}


	/**
	  *	Eliminar registros.
	  *	
	  * Este metodo eliminara la informacion de base de datos identificados por la clave primaria
	  * en el objeto ExtraParamsValores suministrado. Una vez que se ha suprimido un objeto, este no 
	  * puede ser restaurado llamando a save(). save() al ver que este es un objeto vacio, creara una nueva fila 
	  * pero el objeto resultante tendra una clave primaria diferente de la que estaba en el objeto eliminado. 
	  * Si no puede encontrar eliminar fila coincidente a eliminar, Exception sera lanzada.
	  *	
	  *	@throws Exception Se arroja cuando el objeto no tiene definidas sus llaves primarias.
	  *	@return int El numero de filas afectadas.
	  * @param ExtraParamsValores [$extra_params_valores] El objeto de tipo ExtraParamsValores a eliminar
	  **/
	public static final function delete( &$extra_params_valores )
	{
		if( is_null( self::getByPK($extra_params_valores->getIdExtraParamsValores()) ) ) throw new Exception('Campo no encontrado.');
		$sql = "DELETE FROM extra_params_valores WHERE  id_extra_params_valores = ?;";
		$params = array( $extra_params_valores->getIdExtraParamsValores() );
		global $conn;

		$conn->Execute($sql, $params);
		return $conn->Affected_Rows();
	}


}
