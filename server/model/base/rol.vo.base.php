<?php
/** Value Object file for table rol.
  * 
  * VO does not have any behaviour except for storage and retrieval of its own data (accessors and mutators).
  * @author Andres
  * @access public
  * @package docs
  * 
  */

class Rol extends VO
{
	/**
	  * Constructor de Rol
	  * 
	  * Para construir un objeto de tipo Rol debera llamarse a el constructor 
	  * sin parametros. Es posible, construir un objeto pasando como parametro un arreglo asociativo 
	  * cuyos campos son iguales a las variables que constituyen a este objeto.
	  * @return Rol
	  */
	function __construct( $data = NULL)
	{ 
		if(isset($data))
		{
			if( isset($data['id_rol']) ){
				$this->id_rol = $data['id_rol'];
			}
			if( isset($data['nombre']) ){
				$this->nombre = $data['nombre'];
			}
			if( isset($data['descripcion']) ){
				$this->descripcion = $data['descripcion'];
			}
			if( isset($data['descuento']) ){
				$this->descuento = $data['descuento'];
			}
			if( isset($data['salario']) ){
				$this->salario = $data['salario'];
			}
		}
	}

	/**
	  * Obtener una representacion en String
	  * 
	  * Este metodo permite tratar a un objeto Rol en forma de cadena.
	  * La representacion de este objeto en cadena es la forma JSON (JavaScript Object Notation) para este objeto.
	  * @return String 
	  */
	public function __toString( )
	{ 
		$vec = array( 
			"id_rol" => $this->id_rol,
			"nombre" => $this->nombre,
			"descripcion" => $this->descripcion,
			"descuento" => $this->descuento,
			"salario" => $this->salario
		); 
	return json_encode($vec); 
	}
	
	/**
	  * id_rol
	  * 
	  *  [Campo no documentado]<br>
	  * <b>Llave Primaria</b><br>
	  * <b>Auto Incremento</b><br>
	  * @access public
	  * @var int(11)
	  */
	public $id_rol;

	/**
	  * nombre
	  * 
	  * Nombre del rol<br>
	  * @access public
	  * @var varchar(30)
	  */
	public $nombre;

	/**
	  * descripcion
	  * 
	  * descripcion larga de este rol<br>
	  * @access public
	  * @var varchar(255)
	  */
	public $descripcion;

	/**
	  * descuento
	  * 
	  * Porcentaje del descuento del que gozaran los usuarios de este rol<br>
	  * @access public
	  * @var float
	  */
	public $descuento;

	/**
	  * salario
	  * 
	  * Si los usuarios con dicho rol contaran con un salario<br>
	  * @access public
	  * @var float
	  */
	public $salario;

	/**
	  * getIdRol
	  * 
	  * Get the <i>id_rol</i> property for this object. Donde <i>id_rol</i> es  [Campo no documentado]
	  * @return int(11)
	  */
	final public function getIdRol()
	{
		return $this->id_rol;
	}

	/**
	  * setIdRol( $id_rol )
	  * 
	  * Set the <i>id_rol</i> property for this object. Donde <i>id_rol</i> es  [Campo no documentado].
	  * Una validacion basica se hara aqui para comprobar que <i>id_rol</i> es de tipo <i>int(11)</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * <br><br>Esta propiedad se mapea con un campo que es de <b>Auto Incremento</b> !<br>
	  * No deberias usar setIdRol( ) a menos que sepas exactamente lo que estas haciendo.<br>
	  * <br><br>Esta propiedad se mapea con un campo que es una <b>Llave Primaria</b> !<br>
	  * No deberias usar setIdRol( ) a menos que sepas exactamente lo que estas haciendo.<br>
	  * @param int(11)
	  */
	final public function setIdRol( $id_rol )
	{
		$this->id_rol = $id_rol;
	}

	/**
	  * getNombre
	  * 
	  * Get the <i>nombre</i> property for this object. Donde <i>nombre</i> es Nombre del rol
	  * @return varchar(30)
	  */
	final public function getNombre()
	{
		return $this->nombre;
	}

	/**
	  * setNombre( $nombre )
	  * 
	  * Set the <i>nombre</i> property for this object. Donde <i>nombre</i> es Nombre del rol.
	  * Una validacion basica se hara aqui para comprobar que <i>nombre</i> es de tipo <i>varchar(30)</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param varchar(30)
	  */
	final public function setNombre( $nombre )
	{
		$this->nombre = $nombre;
	}

	/**
	  * getDescripcion
	  * 
	  * Get the <i>descripcion</i> property for this object. Donde <i>descripcion</i> es descripcion larga de este rol
	  * @return varchar(255)
	  */
	final public function getDescripcion()
	{
		return $this->descripcion;
	}

	/**
	  * setDescripcion( $descripcion )
	  * 
	  * Set the <i>descripcion</i> property for this object. Donde <i>descripcion</i> es descripcion larga de este rol.
	  * Una validacion basica se hara aqui para comprobar que <i>descripcion</i> es de tipo <i>varchar(255)</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param varchar(255)
	  */
	final public function setDescripcion( $descripcion )
	{
		$this->descripcion = $descripcion;
	}

	/**
	  * getDescuento
	  * 
	  * Get the <i>descuento</i> property for this object. Donde <i>descuento</i> es Porcentaje del descuento del que gozaran los usuarios de este rol
	  * @return float
	  */
	final public function getDescuento()
	{
		return $this->descuento;
	}

	/**
	  * setDescuento( $descuento )
	  * 
	  * Set the <i>descuento</i> property for this object. Donde <i>descuento</i> es Porcentaje del descuento del que gozaran los usuarios de este rol.
	  * Una validacion basica se hara aqui para comprobar que <i>descuento</i> es de tipo <i>float</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param float
	  */
	final public function setDescuento( $descuento )
	{
		$this->descuento = $descuento;
	}

	/**
	  * getSalario
	  * 
	  * Get the <i>salario</i> property for this object. Donde <i>salario</i> es Si los usuarios con dicho rol contaran con un salario
	  * @return float
	  */
	final public function getSalario()
	{
		return $this->salario;
	}

	/**
	  * setSalario( $salario )
	  * 
	  * Set the <i>salario</i> property for this object. Donde <i>salario</i> es Si los usuarios con dicho rol contaran con un salario.
	  * Una validacion basica se hara aqui para comprobar que <i>salario</i> es de tipo <i>float</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param float
	  */
	final public function setSalario( $salario )
	{
		$this->salario = $salario;
	}

}