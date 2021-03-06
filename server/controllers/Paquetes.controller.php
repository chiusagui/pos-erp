<?php
require_once("interfaces/Paquetes.interface.php");
/**
  *
  *
  *
  **/
	
  class PaquetesController implements IPaquetes{
  
        //Metodo para pruebas que simula la obtencion del id de la sucursal actual
        private static function getSucursal()
        {
            return 1;
        }
        
        //metodo para pruebas que simula la obtencion del id de la caja actual
        private static function getCaja()
        {
            return 1;
        }
      
        
        /*
         *Se valida que un string tenga longitud en un rango de un maximo inclusivo y un minimo exclusvio.
         *Regresa true cuando es valido, y un string cuando no lo es.
         */
          private static function validarString($string, $max_length, $nombre_variable,$min_length=0)
	{
		if(strlen($string)<=$min_length||strlen($string)>$max_length)
		{
		    return "La longitud de la variable ".$nombre_variable." proporcionada (".$string.") no esta en el rango de ".$min_length." - ".$max_length;
		}
		return true;
        }


        /*
         * Se valida que un numero este en un rango de un maximo y un minimo inclusivos
         * Regresa true cuando es valido, y un string cuando no lo es
         */
	private static function validarNumero($num, $max_length, $nombre_variable, $min_length=0)
	{
	    if($num<$min_length||$num>$max_length)
	    {
	        return "La variable ".$nombre_variable." proporcionada (".$num.") no esta en el rango de ".$min_length." - ".$max_length;
	    }
	    return true;
	}
        
        /*
         * Valida los parametros de la tabla paquete. Regresa un string con el error si se encuentra
         * alguno. De lo contrario regresa verdadero
         */
        private static function validarParametrosPaquete
        (
                $id_paquete = null,
                $nombre = null,
                $descripcion = null,
                $foto_paquete = null,
                $costo_estandar = null,
                $precio = null,
                $activo = null
        )
        {
            //valida que el paquete exista y este activo
            if(!is_null($id_paquete))
            {
                $paquete = PaqueteDAO::getByPK($id_paquete);
                if(is_null($paquete))
                    return "El paquete ".$id_paquete." no existe";
                
                if(!$paquete->getActivo())
                    return "El paquete ".$id_paquete." no esta activo";
            }
            
            //valida que el nombre este en rango y que no se repita
            if(!is_null($nombre))
            {
                $e = self::validarString($nombre, 100, "nombre");
                if(is_string($e))
                    return $e;
                if(!is_null($id_paquete))
                {
                    $paquetes = array_diff(PaqueteDAO::search( new Paquete( array( "nombre" => trim($nombre) ) ) ), array(PaqueteDAO::getByPK($id_paquete)));
                }
                else
                {
                    $paquetes = PaqueteDAO::search( new Paquete( array( "nombre" => trim($nombre) ) ) );
                }
                foreach($paquetes as $paquete)
                {
                    if($paquete->getActivo())
                        return "El nombre (".$nombre.") ya esta siendo usado por el paquete ".$paquete->getIdPaquete();
                }
            }
            
            //valida que la descripcion este en rango
            if(!is_null($descripcion))
            {
                $e = self::validarString($descripcion, 255, "descripcion");
                if(is_string($e))
                    return $e;
            }
            
            //valida que la foto del paquete este en rango
            if(!is_null($foto_paquete))
            {
                $e = self::validarString($foto_paquete, 255, "foto del paquete");
                if(is_string($e))
                    return $e;
            }
            
            //valida que el costo estandar este en rango
            if(!is_null($costo_estandar))
            {
                $e = self::validarNumero($costo_estandar, 1.8e200, "costo estandar");
                if(is_string($e))
                    return $e;
            }
            
            //valida que el precio este en rango
            if(!is_null($precio))
            {
                $e = self::validarNumero($precio,1.8e200,"precio");
                if(is_string($e))
                    return $e;
            }
            
            //valida el boleano activo
            if(!is_null($activo))
            {
                $e = self::validarNumero($activo, 1, "activo");
                if(is_string($e))
                    return $e;
            }
            
            //No se encontro error
            return true;
        }
        
        /*
         * Valida los parametros de la tabla paquete_empresa. Regresa un string con el error
         * si es que se encontro uno, de lo contrario regresa verdadero
         */
        private static function validarParametrosPaqueteEmpresa
        (
                $id_empresa = null
        )
        {
            //valida que la empresa exista y qe este activa
            if(!is_null($id_empresa))
            {
                $empresa = EmpresaDAO::getByPK($id_empresa);
                if(is_null($empresa))
                    return "La empresa ".$id_empresa." no existe";
                
                if(!$empresa->getActivo())
                    return "La empresa ".$id_empresa." no esta activa";
            }
            
            //no se encontro error
            return true;
        }
        
        /*
         * Valida los parametros de la tabla paquete_sucursal. Regresa un string con el error
         * si es que se encontro uno, de lo contrario regresa verdadero
         */
        private static function validarParametrosPaqueteSucursal
        (
                $id_sucursal = null
        )
        {
            //valida que la sucursal exista y que este activa
            if(!is_null($id_sucursal))
            {
                $sucursal = SucursalDAO::getByPK($id_sucursal);
                if(is_null($sucursal))
                    return "La sucursal ".$id_sucursal." no existe";
                
                if(!$sucursal->getActiva())
                    return "La sucursal ".$id_sucursal." no esta activa";
            }
            
            //no se encontro error
            return true;
        }
        
        /*
         * Valida los parametros de la tabla producto_paquete. Regresa un string con el error
         * en caso de encontrarse alguno, de lo contrario regresa verdadero
         */
        private static function validarParametrosProductoPaquete
        (
                $id_producto = null,
                $id_unidad = null,
                $cantidad = null
        )
        {
            //valida que el producto exista y este activo
            if(!is_null($id_producto))
            {
                $producto = ProductoDAO::getByPK($id_producto);
                if(is_null($producto))
                    return "El producto ".$id_producto." no existe";
                
                if(!$producto->getActivo())
                    return "El producto ".$id_producto." no esta activo";
            }
            
            //valida que la unidad exista y este activa
            if(!is_null($id_unidad))
            {
                $unidad = UnidadDAO::getByPK($id_unidad);
                if(is_null($unidad))
                    return "La unidad ".$id_unidad." no existe";
                
                if(!$unidad->getActiva())
                    return "La unidad ".$id_unidad." no existe";
            }
            
            //valida que la cantida este en rango y vaya de acuerdo a la unidad
            if(!is_null($cantidad))
            {
                $e = self::validarNumero($cantidad, 1.8e200, "cantidad");
                if(is_string($e))
                    return $e;
                
                if( ($unidad->getEsEntero() &&  is_float($cantidad)))
                    return "LA unidad que se maneja no acepta flotantes y se obtuvo una cantidad flotante (".$cantidad.")";
            }
            
            //no se encontro error
            return true;
        }
        
        
        
        
        
      
  
	/**
 	 *
 	 *Agrupa productos y/o servicios en un paquete
 	 *
 	 * @param nombre string Nombre del paquete
 	 * @param empresas json Ids de empresas en las que se ofrecera este paquete
 	 * @param sucursales json Ids de sucursales en las que se ofrecera este paquete
 	 * @param productos json Objeto que contendra los ids de los productos que se incluiran en el paquete con sus cantidades respectivas.
 	 * @param sericios json Objeto que contendra los ids de los servicios que se incluiran en el paquete con sus cantidades respectivas.
 	 * @param descripcion string Descripcion larga del paquete
 	 * @param margen_utilidad float Margen de utilidad que se obtendra al vender este paquete
 	 * @param descuento float Descuento que aplicara a este paquete
 	 * @param foto_paquete string Url de la foto del paquete
 	 * @return id_paquete int Id autogenerado por la insercion
 	 **/
	public static function Nuevo
	(
		$empresas, 
		$nombre, 
		$sucursales, 
		$costo_estandar = null, 
		$descripcion = null, 
		$foto_paquete = null, 
		$precio = null, 
		$productos = null, 
		$servicios = null
	)
	{  
            Logger::log("Creando nuevo paquete");
            
            //valida los parametros recibidos
            $validar = self::validarParametrosPaquete(null,$nombre,$descripcion,$foto_paquete,$costo_estandar,$precio);
            if(is_string($validar))
            {
                Logger::error($validar);
                throw new Exception($validar);
            }
            
            //valida que las empresas sean correctas
            $empresas = object_to_array($empresas);
            
            if(!is_array($empresas))
            {
                Logger::error("Las empresas son invalidas");
                throw new Exception("Las empresas son invalidas",901);
            }
            
            //valida que las sucursales sean correctas
            $sucursales = object_to_array($sucursales);
            
            if(!is_array($sucursales))
            {
                Logger::error("Las sucursales son invalidas");
                throw new Exception("Las sucursales son invalidas",901);
            }
            
            //Se inicializa el objeto
            $paquete = new Paquete( array( 
                                            "nombre"            => $nombre,
                                            "descripcion"       => $descripcion,
                                            "foto_paquete"      => $foto_paquete,
                                            "costo_estandar"    => $costo_estandar,
                                            "precio"            => $precio,
                                            "activo"            => 1
                                            )
                                    );
            
            //Se almacena el nuevo paquete y se validan y guardan los registros correspondientes a las tablas paquete_empresa,
            //paquete_sucursal, producto_paquete y orden_de_servicio_paquete
            DAO::transBegin();
            try
            {
                PaqueteDAO::save($paquete);
                foreach($empresas as $empresa)
                {
                    
                    $validar = self::validarParametrosPaqueteEmpresa($empresa);
                    if(is_string($validar))
                        throw new Exception($validar,901);
                    
                   $paquete_empresa = new PaqueteEmpresa( array(  
                       
                                                                "id_paquete"        => $paquete->getIdPaquete(),
                                                                "id_empresa"        => $empresa,
                       
                                                                ) 
                                                               );
                    PaqueteEmpresaDAO::save($paquete_empresa);
                }/* Fin foreach de empresas */
                
                foreach($sucursales as $sucursal)
                {
                    
                    $validar = self::validarParametrosPaqueteSucursal($sucursal);
                    if(is_string($validar))
                        throw new Exception($validar,901);
                    
                   $paquete_sucursal = new PaqueteSucursal( array(  
                       
                                                                "id_paquete"        => $paquete->getIdPaquete(),
                                                                "id_sucursal"       => $sucursal
                       
                                                                ) 
                                                               );
                    PaqueteSucursalDAO::save($paquete_sucursal);
                }/* Fin foreach de sucursales */
                
                if(!is_null($productos))
                {
                    
                    $productos = object_to_array($productos);
                    
                    if(!is_array($productos))
                    {
                        throw new Exception("Los productos son invalidos",901);
                    }
                    
                    $producto_paquete = new ProductoPaquete( array( "id_paquete" => $paquete->getIdPaquete() ) );
                    foreach($productos as $producto)
                    {
                        
                        if
                        (
                                !array_key_exists("id_producto", $producto)         ||
                                !array_key_exists("id_unidad", $producto)           ||
                                !array_key_exists("cantidad", $producto)
                        )
                        {
                            throw new Exception("Los productos no cuentan con todos los parametros",901);
                        }
                        
                        $validar = self::validarParametrosProductoPaquete($producto["id_producto"], $producto["id_unidad"], $producto["cantidad"]);
                        if(is_string($validar))
                            throw new Exception($validar,901);
                        
                        $producto_paquete->setIdProducto($producto["id_producto"]);
                        $producto_paquete->setIdUnidad($producto["id_unidad"]);
                        $producto_paquete->setCantidad($producto["cantidad"]);
                        ProductoPaqueteDAO::save($producto_paquete);
                    }
                }/* Fin if de productos */
                
                if(!is_null($servicios))
                {
                    
                    $servicios = object_to_array($servicios);
                    
                    if(!is_array($servicios))
                    {
                        throw new Exception("Los servicios son invalidos",901);
                    }
                    
                    $orden_de_servicio_paquete = new OrdenDeServicioPaquete( array( "id_paquete" => $paquete->getIdPaquete() ) );
                    foreach($servicios as $servicio)
                    {
                        
                        if
                        (
                                !array_key_exists("id_servicio", $servicio)         ||
                                !array_key_exists("cantidad", $servicio)
                        )
                        {
                            throw new Exception("Los servicios no tienen los parametros necesarios",901);
                        }
                        
                        $serv = ServicioDAO::getByPK($servicio["id_servicio"]);
                        if(is_null($serv))
                            throw new Exception("El servicio ".$servicio["id_servicio"]." no existe",901);
                        
                        if(!$serv->getActivo())
                            throw new Exception("El servicio ".$servicio["id_servicio"]." no esta activo",901);
                        
                        if(is_string($validar = self::validarNumero($servicio["cantidad"], 1.8e200, "cantidad")))
                                throw new Exception($validar,901);
                        
                        $orden_de_servicio_paquete->setIdServicio($servicio["id_servicio"]);
                        $orden_de_servicio_paquete->setCantidad($servicio["cantidad"]);
                        OrdenDeServicioPaqueteDAO::save($orden_de_servicio_paquete);
                    }
                }/* Fin if de servicios */
            }
            catch(Exception $e)
            {
                DAO::transRollback();
                Logger::error("No se pudo crear el nuevo paquete ".$e);
                if($e->getCode()==901)
                    throw new Exception("No se pudo crear el nuevo paquete: ".$e->getMessage (),901);
                throw new Exception("No se pudo crear el nuevo paquete",901);
            }
            DAO::transEnd();
            Logger::log("paquete creado exitosamente");
            return array( "id_paquete" => $paquete->getIdPaquete() );
            
	}
  
	/**
 	 *
 	 *Edita la informacion de un paquete
 	 *
 	 * @param id_paquete int ID del paquete a editar
 	 * @param foto_paquete string Url de la foto del paquete
 	 * @param productos json Objeto que contendra los ids de los productos contenidos en el paquete con sus cantidades respectivas
 	 * @param descuento float Descuento que sera aplicado a este paquete
 	 * @param servicios json Objeto que contendra los ids de los servicios contenidos en el paquete con sus cantidades respectivas
 	 * @param nombre string Nombre del paquete
 	 * @param margen_utilidad float Margen de utilidad que se ganara al vender este paquete
 	 * @param descripcion string Descripcion larga del paquete
 	 **/
	public static function Editar
	(
		$id_paquete, 
		$costo_estandar = null, 
		$descripcion = null, 
		$empresas = null, 
		$foto_paquete = null, 
		$nombre = null, 
		$precio = null, 
		$productos = null, 
		$servicios = null, 
		$sucursales = null
	)
	{  
            Logger::log("Editando paquete ".$id_paquete);
            
            //se validan los parametros recibidos
            $validar = self::validarParametrosPaquete($id_paquete,$nombre,$descripcion,$foto_paquete,$costo_estandar,$precio);
            if(is_string($validar))
            {
                Logger::error($validar);
                throw new Exception($validar);
            }
            
            //Los parametros que no sean nulos seran tomados como actualizacion
            $paquete = PaqueteDAO::getByPK($id_paquete);
            
            if(!is_null($foto_paquete))
            {
                $paquete->setFotoPaquete($foto_paquete);
            }
            if(!is_null($nombre))
            {
                $paquete->setNombre($nombre);
            }
            if(!is_null($descripcion))
            {
                $paquete->setDescripcion($descripcion);
            }
            if(!is_null($costo_estandar))
            {
                $paquete->setCostoEstandar($costo_estandar);
            }
            if(!is_null($precio))
            {
                $paquete->setPrecio($precio);
            }
            
            //Se realiza la actualizacion al paquete, si se recibio una lista de productos y/o servicios
            //Se actualizan o agregan los recibidos en la base de datos y despues se traen aquellos que esten
            //en la base de datos y se buscan en la lista recibida. Si no se encuentran, son eliminados.
            DAO::transBegin();
            try
            {
                PaqueteDAO::save($paquete);
                if(!is_null($empresas))
                {
                    //valida que las empresas sean correctas
                    $empresas = object_to_array($empresas);

                    if(!is_array($empresas))
                    {
                        throw new Exception("Las empresas son invalidas",901);
                    }
                    
                    foreach($empresas as $empresa)
                    {

                        $validar = self::validarParametrosPaqueteEmpresa($empresa);
                        if(is_string($validar))
                            throw new Exception($validar,901);

                       $paquete_empresa = new PaqueteEmpresa( array(  

                                                                    "id_paquete"        => $paquete->getIdPaquete(),
                                                                    "id_empresa"        => $empresa

                                                                    ) 
                                                                   );
                        PaqueteEmpresaDAO::save($paquete_empresa);
                    }/* Fin foreach de empresas */
                    
                    $paquetes_empresa = PaqueteEmpresaDAO::search( new PaqueteEmpresa( array ( "id_paquete" => $id_paquete ) ) );
                    foreach($paquetes_empresa as $p_e)
                    {
                        $encontrado = false;
                        foreach($empresas as $empresa)
                        {
                            if($empresa==$p_e->getIdEmpresa())
                            {
                                $encontrado = true;
                            }
                        }
                        if(!$encontrado)
                            PaqueteEmpresaDAO::delete ($p_e);
                    }
                }/* Fin if de empresas */
                
                if(!is_null($sucursales))
                {
                     //valida que las sucursales sean correctas
                    $sucursales = object_to_array($sucursales);

                    if(!is_array($sucursales))
                    {
                        throw new Exception("Las sucursales son invalidas",901);
                    }
                    
                    foreach($sucursales as $sucursal)
                    {

                        $validar = self::validarParametrosPaqueteSucursal($sucursal);
                        if(is_string($validar))
                            throw new Exception($validar,901);

                       $paquete_sucursal = new PaqueteSucursal( array(  

                                                                    "id_paquete"        => $paquete->getIdPaquete(),
                                                                    "id_sucursal"       => $sucursal

                                                                    ) 
                                                                   );
                        PaqueteSucursalDAO::save($paquete_sucursal);
                    }/* Fin foreach de sucursales */
                    
                    $paquetes_sucursal = PaqueteSucursalDAO::search( new PaqueteSucursal( array ( "id_paquete" => $id_paquete ) ) );
                    foreach($paquetes_sucursal as $p_e)
                    {
                        $encontrado = false;
                        foreach($sucursales as $sucursal)
                        {
                            if($sucursal==$p_e->getIdSucursal())
                            {
                                $encontrado = true;
                            }
                        }
                        if(!$encontrado)
                            PaqueteSucursalDAO::delete ($p_e);
                    }
                }/* Fin if de sucursales */
                    
                if(!is_null($productos))
                {
                    
                    $productos = object_to_array($productos);
                    
                    if(!is_array($productos))
                    {
                        throw new Exception("Los productos son invalidos",901);
                    }
                    
                    $producto_paquete = new ProductoPaquete( array( "id_paquete" => $paquete->getIdPaquete() ) );
                    foreach($productos as $producto)
                    {
                        
                        if
                        (
                                !array_key_exists("id_producto", $producto)         ||
                                !array_key_exists("id_unidad", $producto)           ||
                                !array_key_exists("cantidad", $producto)
                        )
                        {
                            throw new Exception("Los productos no cuentan con todos los parametros",901);
                        }
                        
                        $validar = self::validarParametrosProductoPaquete($producto["id_producto"], $producto["id_unidad"], $producto["cantidad"]);
                        if(is_string($validar))
                            throw new Exception($validar,901);
                        
                        $producto_paquete->setIdProducto($producto["id_producto"]);
                        $producto_paquete->setIdUnidad($producto["id_unidad"]);
                        $producto_paquete->setCantidad($producto["cantidad"]);
                        ProductoPaqueteDAO::save($producto_paquete);
                    }
                    
                    $productos_paquete = ProductoPaqueteDAO::search( new ProductoPaquete( array( "id_paquete" => $id_paquete ) ) );
                    foreach($productos_paquete as $p_p)
                    {
                        $encontrado = false;
                        foreach($productos as $producto)
                        {
                            if($producto["id_producto"] == $p_p->getIdProducto() && $producto["id_unidad"] == $p_p->getIdUnidad())
                                $encontrado=true;
                        }
                        if(!$encontrado)
                            ProductoPaqueteDAO::delete($p_p);
                    }
                }/* Fin if de productos */
                
                if(!is_null($servicios))
                {
                    
                    $servicios = object_to_array($servicios);
                    
                    if(!is_array($servicios))
                    {
                        throw new Exception("Los servicios son invalidos",901);
                    }
                    
                    $orden_de_servicio_paquete = new OrdenDeServicioPaquete( array( "id_paquete" => $paquete->getIdPaquete() ) );
                    foreach($servicios as $servicio)
                    {
                        
                        if
                        (
                                !array_key_exists("id_servicio", $servicio)         ||
                                !array_key_exists("cantidad", $servicio)
                        )
                        {
                            throw new Exception("Los servicios no tienen los parametros necesarios",901);
                        }
                        
                        $serv = ServicioDAO::getByPK($servicio["id_servicio"]);
                        if(is_null($serv))
                            throw new Exception("El servicio ".$servicio["id_servicio"]." no existe",901);
                        
                        if(!$serv->getActivo())
                            throw new Exception("El servicio ".$servicio["id_servicio"]." no esta activo",901);
                        
                        if(is_string($validar = self::validarNumero($servicio["cantidad"], 1.8e200, "cantidad")))
                                throw new Exception($validar,901);
                        
                        $orden_de_servicio_paquete->setIdServicio($servicio["id_servicio"]);
                        $orden_de_servicio_paquete->setCantidad($servicio["cantidad"]);
                        OrdenDeServicioPaqueteDAO::save($orden_de_servicio_paquete);
                    }
                    
                    $servicios_paquete = OrdenDeServicioPaqueteDAO::search( new OrdenDeServicioPaquete( array( "id_paquete" => $paquete->getIdPaquete() ) ) );
                    foreach($servicios_paquete as $s_p)
                    {
                        $encontrado = false;
                        foreach($servicios as $servicio)
                        {
                            if($servicio["id_servicio"] == $s_p->getIdServicio())
                                $encontrado = true;
                        }
                        if(!$encontrado)
                            OrdenDeServicioPaqueteDAO::delete ($s_p);
                    }
                }/* Fin if de servicios */
            }
            catch(Exception $e)
            {
                DAO::transRollback();
                Logger::error("No se pudo editar el paquete ".$e);
                if($e->getCode()==901)
                    throw new Exception("No se pudo editar el paquete: ".$e->getMessage(),901);
                throw new Exception("No se pudo editar el paquete",901);
            }
            DAO::transEnd();
            Logger::log("El paquete ha sido editado exitosamente");
	}
  
	/**
 	 *
 	 *Desactiva un paquete.
 	 *
 	 * @param id_paquete int Id del paquete a desactivar
 	 **/
	public static function Eliminar
	(
		$id_paquete
	)
	{  
            Logger::log("Eliminando el paquete ".$id_paquete);
            
            ///valida que el paquete exista y que este activo
            $validar = self::validarParametrosPaquete($id_paquete);
            if(is_string($validar))
            {
                Logger::error($validar);
                throw new Exception($validar);
            }
            
            $paquete = PaqueteDAO::getByPK($id_paquete);
            $paquete->setActivo(0);
            
            //Se desactiva el paquete
            DAO::transBegin();
            try
            {
                PaqueteDAO::save($paquete);
            }
            catch(Exception $e)
            {
                DAO::transRollback();
                Logger::error("El paquete no ha podido ser eliminado: ".$id_paquete);
                throw new Exception("El paquete no ha podido ser eliminado");
            }
            DAO::transEnd();
            Logger::log("Paquete eliminado exitosamente");
	}
  
	/**
 	 *
 	 *Activa un paquete previamente desactivado
 	 *
 	 * @param id_paquete int Id del paquete a activar
 	 **/
	public static function Activar
	(
		$id_paquete
	)
	{  
            Logger::log("Activando el paquete ".$id_paquete);
            
            //valida que el paquete exista y este desactivado
            $paquete = PaqueteDAO::getByPK($id_paquete);
            if(is_null($paquete))
            {
                Logger::error("El paquete ".$id_paquete." esta desactivado");
                throw new Exception("El paquete ".$id_paquete." esta desactivado");
            }
            if($paquete->getActivo())
            {
                Logger::warn("El paquete ".$id_paquete." ya esta activo");
                throw new Exception("El paquete ".$id_paquete." ya esta activo");
            }
            
            $paquete->setActivo(1);
            DAO::transBegin();
            try
            {
                PaqueteDAO::save($paquete);
            }
            catch(Exception $e)
            {
                DAO::transRollback();
                Logger::error("No se ha podido activar el paquete ".$id_paquete." : ".$e);
                throw new Exception("No se ha podudo activar el paquete");   
            }
            DAO::transEnd();
            Logger::log("El paquete ha sido activado exitosamente");
	}
  
	/**
 	 *
 	 *Lista los paquetes, se puede filtrar por empresa, por sucursal, por producto, por servicio y se pueden ordenar por sus atributos
 	 *
 	 * @param id_empresa int Id de la empresa de la cual se listaran los paquetes
 	 * @param id_sucursal int Id de la sucursal de la cual se listaran sus paquetes
 	 * @param id_producto int Se listaran los paquetes que contengan dicho producto
 	 * @param id_servicio int Se listaran los paquetes que contengan dicho servicio
 	 * @param activo bool Si este valor no es obtenido, se listaran paquetes tanto activos como inactivos, si es verdadera, se listaran solo paquetes activos, si es falso, se listaran paquetes inactivos
 	 * @return paquetes json Lista de apquetes
 	 **/
	public static function Lista
	(
		$activo = null, 
		$id_empresa = null, 
		$id_sucursal = null
	)
	{  
            Logger::log("Listando paquetes");
            
            //El resultado de la busqueda sera la interseccion de 3 arreglos diferentes, cada uno
            //tendra una lista de paquetes que cumple la especificacion de cada parametro. Si uno de 
            //los parametros no es recibido sera sustituido por la lista de todos los paquetes para no afectar
            //la interseccion.
            
            $paquetes = array();
            $paquetes_1 = array();
            $paquetes_2 = array();
            $paquetes_3 = array();
            
            if(!is_null($id_empresa))
            {
               $paquetes_empresa = PaqueteEmpresaDAO::search( new PaqueteEmpresa( array( "id_empresa" => $id_empresa ) ) );
               foreach($paquetes_empresa as $paquete_empresa)
               {
                   array_push($paquetes_1,  PaqueteDAO::getByPK($paquete_empresa->getIdPaquete()));
               }
            }
            else
            {
                $paquetes_1 = PaqueteDAO::getAll();
            }
            
            if(!is_null($id_sucursal))
            {
                $paquetes_sucursal = PaqueteSucursalDAO::search( new PaqueteSucursal( array( "id_sucursal" => $id_sucursal ) ) );
                foreach($paquetes_sucursal as $paquete_sucursal)
                {
                    array_push($paquetes_2,PaqueteDAO::getByPK($paquete_sucursal->getIdPaquete()));
                }
            }
            else
            {
                $paquetes_2 = $paquetes_1;
            }
            
            if(!is_null($activo))
            {
                $paquetes_3 = PaqueteDAO::search( new Paquete( array( "activo" => $activo ) ) );
            }
            else
            {
                $paquetes_3 = $paquetes_2;
            }
            
            $paquetes = array_intersect($paquetes_1, $paquetes_2, $paquetes_3);
            Logger::log("Lista obtenida exitosamente con ".count($paquetes)." elementos");
            return $paquetes;
	}
  
	/**
 	 *
 	 *Muestra los productos y/o servicios englobados en este paquete as? como las sucursales y las empresas donde lo ofrecen
 	 *
 	 * @param id_paquete int Id del paquete a visualizar sus detalles
 	 * @return detalle_paquete json Informacion del detalle del paquete
 	 **/
	public static function Detalle
	(
		$id_paquete
	)
	{  
            Logger::log("consiguiendo los detalles del paquete");
            
            //valida que el paquete exista
            $paquete = PaqueteDAO::getByPK($id_paquete);
            if(is_null($paquete))
            {
                Logger::error("El paquete ".$id_paquete." no existe");
                throw new Exception("El paquete ".$id_paquete." no existe");
            }
            
            //En el primer campo de un arreglo se almacena el paquete en sí, en el segundo las empresas en las que esta disponible el paquete,
            //en el tercero las sucursales en las que esta disponible, en el cuarto los productos que contiene y en el quinto los servicios
            
            $detalle_paquete = array();
            
            array_push($detalle_paquete,$paquete);
            
            $empresas = array();
            $paquetes_empresa = PaqueteEmpresaDAO::search( new PaqueteEmpresa( array( "id_paquete" => $id_paquete ) ));
            foreach($paquetes_empresa as $paquete_empresa)
            {
                array_push($empresas,  EmpresaDAO::getByPK($paquete_empresa->getIdEmpresa()));
            }
            array_push($detalle_paquete,$empresas);
            
            $sucursales = array();
            $paquetes_sucursal = PaqueteSucursalDAO::search( new PaqueteSucursal( array( "id_paquete" => $id_paquete ) ) );
            foreach($paquetes_sucursal as $paquete_sucursal)
            {
                array_push($sucursales,  SucursalDAO::getByPK($paquete_sucursal->getIdSucursal()));
            }
            array_push($detalle_paquete,$sucursales);
            
            $productos = array();
            $productos_paquete = ProductoPaqueteDAO::search( new ProductoPaquete( array( "id_paquete" => $id_paquete ) ) );
            foreach($productos_paquete as $producto_paquete)
            {
                array_push($productos, ProductoDAO::getByPK($producto_paquete->getIdProducto()));
            }
            array_push($detalle_paquete,$productos);
            
            $servicios = array();
            $servicios_paquete = OrdenDeServicioPaqueteDAO::search( new OrdenDeServicioPaquete( array( "id_paquete" => $id_paquete ) ) );
            foreach($servicios_paquete as $servicio_paquete)
            {
                array_push($servicios, ServicioDAO::getByPK($servicio_paquete->getIdServicio()));
            }
            array_push($detalle_paquete,$servicios);
            
            Logger::log("Detalle de paquete ".$id_paquete." conseguido exitosamente con ".count($empresas)."
                empresas, ".count($sucursales)." sucursales, ".count($productos)." productos y ".count($servicios)." servicios");
            
            return $detalle_paquete;
	}
  }
