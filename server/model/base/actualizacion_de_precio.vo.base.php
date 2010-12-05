<?php
/** Value Object file for table actualizacion_de_precio.
  * 
  * VO does not have any behaviour except for storage and retrieval of its own data (accessors and mutators).
  * @author Alan Gonzalez <alan@caffeina.mx> 
  * @access public
  * 
  */

class ActualizacionDePrecio extends VO
{
	/**
	  * Constructor de ActualizacionDePrecio
	  * 
	  * Para construir un objeto de tipo ActualizacionDePrecio debera llamarse a el constructor 
	  * sin parametros. Es posible, construir un objeto pasando como parametro un arreglo asociativo 
	  * cuyos campos son iguales a las variables que constituyen a este objeto.
	  * @return ActualizacionDePrecio
	  */
	function __construct( $data = NULL)
	{ 
		if(isset($data))
		{
			$this->id_producto = $data['id_producto'];
			$this->id_usuario = $data['id_usuario'];
			$this->precio_venta = $data['precio_venta'];
			$this->precio_compra = $data['precio_compra'];
			$this->precio_intersucursal = $data['precio_intersucursal'];
			$this->fecha = $data['fecha'];
		}
	}

	/**
	  * Obtener una representacion en String
	  * 
	  * Este metodo permite tratar a un objeto ActualizacionDePrecio en forma de cadena.
	  * La representacion de este objeto en cadena es la forma JSON (JavaScript Object Notation) para este objeto.
	  * @return String 
	  */
	  public function __toString( )
	  { 
		$vec = array( 
		"id_producto" => $this->id_producto,
		"id_usuario" => $this->id_usuario,
		"precio_venta" => $this->precio_venta,
		"precio_compra" => $this->precio_compra,
		"precio_intersucursal" => $this->precio_intersucursal,
		"fecha" => $this->fecha
		); 
	return json_encode($vec); 
	}
	/**
	  * id_producto
	  * 
	  * Campo no documentado<br>
	  * <b>Llave Primaria</b><br>
	  * @access protected
	  * @var int(11)
	  */
	protected $id_producto;

	/**
	  * id_usuario
	  * 
	  * Campo no documentado<br>
	  * @access protected
	  * @var int(11)
	  */
	protected $id_usuario;

	/**
	  * precio_venta
	  * 
	  * Campo no documentado<br>
	  * @access protected
	  * @var float
	  */
	protected $precio_venta;

	/**
	  * precio_compra
	  * 
	  * Campo no documentado<br>
	  * @access protected
	  * @var float
	  */
	protected $precio_compra;

	/**
	  * precio_intersucursal
	  * 
	  * Campo no documentado<br>
	  * @access protected
	  * @var float
	  */
	protected $precio_intersucursal;

	/**
	  * fecha
	  * 
	  * Campo no documentado<br>
	  * @access protected
	  * @var timestamp
	  */
	protected $fecha;

	/**
	  * getIdProducto
	  * 
	  * Get the <i>id_producto</i> property for this object. Donde <i>id_producto</i> es Campo no documentado
	  * @return int(11)
	  */
	final public function getIdProducto()
	{
		return $this->id_producto;
	}

	/**
	  * setIdProducto( $id_producto )
	  * 
	  * Set the <i>id_producto</i> property for this object. Donde <i>id_producto</i> es Campo no documentado.
	  * Una validacion basica se hara aqui para comprobar que <i>id_producto</i> es de tipo <i>int(11)</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * <br><br>Esta propiedad se mapea con un campo que es una <b>Llave Primaria</b> !<br>
	  * No deberias usar setIdProducto( ) a menos que sepas exactamente lo que estas haciendo.<br>
	  * @param int(11)
	  */
	final public function setIdProducto( $id_producto )
	{
		$this->id_producto = $id_producto;
	}

	/**
	  * getIdUsuario
	  * 
	  * Get the <i>id_usuario</i> property for this object. Donde <i>id_usuario</i> es Campo no documentado
	  * @return int(11)
	  */
	final public function getIdUsuario()
	{
		return $this->id_usuario;
	}

	/**
	  * setIdUsuario( $id_usuario )
	  * 
	  * Set the <i>id_usuario</i> property for this object. Donde <i>id_usuario</i> es Campo no documentado.
	  * Una validacion basica se hara aqui para comprobar que <i>id_usuario</i> es de tipo <i>int(11)</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param int(11)
	  */
	final public function setIdUsuario( $id_usuario )
	{
		$this->id_usuario = $id_usuario;
	}

	/**
	  * getPrecioVenta
	  * 
	  * Get the <i>precio_venta</i> property for this object. Donde <i>precio_venta</i> es Campo no documentado
	  * @return float
	  */
	final public function getPrecioVenta()
	{
		return $this->precio_venta;
	}

	/**
	  * setPrecioVenta( $precio_venta )
	  * 
	  * Set the <i>precio_venta</i> property for this object. Donde <i>precio_venta</i> es Campo no documentado.
	  * Una validacion basica se hara aqui para comprobar que <i>precio_venta</i> es de tipo <i>float</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param float
	  */
	final public function setPrecioVenta( $precio_venta )
	{
		$this->precio_venta = $precio_venta;
	}

	/**
	  * getPrecioCompra
	  * 
	  * Get the <i>precio_compra</i> property for this object. Donde <i>precio_compra</i> es Campo no documentado
	  * @return float
	  */
	final public function getPrecioCompra()
	{
		return $this->precio_compra;
	}

	/**
	  * setPrecioCompra( $precio_compra )
	  * 
	  * Set the <i>precio_compra</i> property for this object. Donde <i>precio_compra</i> es Campo no documentado.
	  * Una validacion basica se hara aqui para comprobar que <i>precio_compra</i> es de tipo <i>float</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param float
	  */
	final public function setPrecioCompra( $precio_compra )
	{
		$this->precio_compra = $precio_compra;
	}

	/**
	  * getPrecioIntersucursal
	  * 
	  * Get the <i>precio_intersucursal</i> property for this object. Donde <i>precio_intersucursal</i> es Campo no documentado
	  * @return float
	  */
	final public function getPrecioIntersucursal()
	{
		return $this->precio_intersucursal;
	}

	/**
	  * setPrecioIntersucursal( $precio_intersucursal )
	  * 
	  * Set the <i>precio_intersucursal</i> property for this object. Donde <i>precio_intersucursal</i> es Campo no documentado.
	  * Una validacion basica se hara aqui para comprobar que <i>precio_intersucursal</i> es de tipo <i>float</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param float
	  */
	final public function setPrecioIntersucursal( $precio_intersucursal )
	{
		$this->precio_intersucursal = $precio_intersucursal;
	}

	/**
	  * getFecha
	  * 
	  * Get the <i>fecha</i> property for this object. Donde <i>fecha</i> es Campo no documentado
	  * @return timestamp
	  */
	final public function getFecha()
	{
		return $this->fecha;
	}

	/**
	  * setFecha( $fecha )
	  * 
	  * Set the <i>fecha</i> property for this object. Donde <i>fecha</i> es Campo no documentado.
	  * Una validacion basica se hara aqui para comprobar que <i>fecha</i> es de tipo <i>timestamp</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param timestamp
	  */
	final public function setFecha( $fecha )
	{
		$this->fecha = $fecha;
	}

}
