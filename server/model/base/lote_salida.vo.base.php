<?php
/** Value Object file for table lote_salida.
  * 
  * VO does not have any behaviour except for storage and retrieval of its own data (accessors and mutators).
  * @author Anonymous
  * @access public
  * @package docs
  * 
  */

class LoteSalida extends VO
{
	/**
	  * Constructor de LoteSalida
	  * 
	  * Para construir un objeto de tipo LoteSalida debera llamarse a el constructor 
	  * sin parametros. Es posible, construir un objeto pasando como parametro un arreglo asociativo 
	  * cuyos campos son iguales a las variables que constituyen a este objeto.
	  * @return LoteSalida
	  */
	function __construct( $data = NULL)
	{ 
		if(isset($data))
		{
			if( isset($data['id_lote_salida']) ){
				$this->id_lote_salida = $data['id_lote_salida'];
			}
			if( isset($data['id_lote']) ){
				$this->id_lote = $data['id_lote'];
			}
			if( isset($data['id_usuario']) ){
				$this->id_usuario = $data['id_usuario'];
			}
			if( isset($data['id_documento']) ){
				$this->id_documento = $data['id_documento'];
			}
			if( isset($data['fecha_registro']) ){
				$this->fecha_registro = $data['fecha_registro'];
			}
			if( isset($data['motivo']) ){
				$this->motivo = $data['motivo'];
			}
		}
	}

	/**
	  * Obtener una representacion en String
	  * 
	  * Este metodo permite tratar a un objeto LoteSalida en forma de cadena.
	  * La representacion de este objeto en cadena es la forma JSON (JavaScript Object Notation) para este objeto.
	  * @return String 
	  */
	public function __toString( )
	{ 
		$vec = array( 
			"id_lote_salida" => $this->id_lote_salida,
			"id_lote" => $this->id_lote,
			"id_usuario" => $this->id_usuario,
			"id_documento" => $this->id_documento,
			"fecha_registro" => $this->fecha_registro,
			"motivo" => $this->motivo
		); 
	return json_encode($vec); 
	}
	
	/**
	  * id_lote_salida
	  * 
	  *  [Campo no documentado]<br>
	  * <b>Llave Primaria</b><br>
	  * <b>Auto Incremento</b><br>
	  * @access public
	  * @var int(11)
	  */
	public $id_lote_salida;

	/**
	  * id_lote
	  * 
	  * Id del almacen del cual sale producto<br>
	  * @access public
	  * @var int(11)
	  */
	public $id_lote;

	/**
	  * id_usuario
	  * 
	  * Id del usuario que registra<br>
	  * @access public
	  * @var int(11)
	  */
	public $id_usuario;

	/**
	  * id_documento
	  * 
	  * Id del documento que genero esta entrada<br>
	  * @access public
	  * @var int(11)
	  */
	public $id_documento;

	/**
	  * fecha_registro
	  * 
	  * Fecha en que se registra el movimiento<br>
	  * @access public
	  * @var int(11)
	  */
	public $fecha_registro;

	/**
	  * motivo
	  * 
	  * motivo por le cual sale producto del almacen<br>
	  * @access public
	  * @var varchar(255)
	  */
	public $motivo;

	/**
	  * getIdLoteSalida
	  * 
	  * Get the <i>id_lote_salida</i> property for this object. Donde <i>id_lote_salida</i> es  [Campo no documentado]
	  * @return int(11)
	  */
	final public function getIdLoteSalida()
	{
		return $this->id_lote_salida;
	}

	/**
	  * setIdLoteSalida( $id_lote_salida )
	  * 
	  * Set the <i>id_lote_salida</i> property for this object. Donde <i>id_lote_salida</i> es  [Campo no documentado].
	  * Una validacion basica se hara aqui para comprobar que <i>id_lote_salida</i> es de tipo <i>int(11)</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * <br><br>Esta propiedad se mapea con un campo que es de <b>Auto Incremento</b> !<br>
	  * No deberias usar setIdLoteSalida( ) a menos que sepas exactamente lo que estas haciendo.<br>
	  * <br><br>Esta propiedad se mapea con un campo que es una <b>Llave Primaria</b> !<br>
	  * No deberias usar setIdLoteSalida( ) a menos que sepas exactamente lo que estas haciendo.<br>
	  * @param int(11)
	  */
	final public function setIdLoteSalida( $id_lote_salida )
	{
		$this->id_lote_salida = $id_lote_salida;
	}

	/**
	  * getIdLote
	  * 
	  * Get the <i>id_lote</i> property for this object. Donde <i>id_lote</i> es Id del almacen del cual sale producto
	  * @return int(11)
	  */
	final public function getIdLote()
	{
		return $this->id_lote;
	}

	/**
	  * setIdLote( $id_lote )
	  * 
	  * Set the <i>id_lote</i> property for this object. Donde <i>id_lote</i> es Id del almacen del cual sale producto.
	  * Una validacion basica se hara aqui para comprobar que <i>id_lote</i> es de tipo <i>int(11)</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param int(11)
	  */
	final public function setIdLote( $id_lote )
	{
		$this->id_lote = $id_lote;
	}

	/**
	  * getIdUsuario
	  * 
	  * Get the <i>id_usuario</i> property for this object. Donde <i>id_usuario</i> es Id del usuario que registra
	  * @return int(11)
	  */
	final public function getIdUsuario()
	{
		return $this->id_usuario;
	}

	/**
	  * setIdUsuario( $id_usuario )
	  * 
	  * Set the <i>id_usuario</i> property for this object. Donde <i>id_usuario</i> es Id del usuario que registra.
	  * Una validacion basica se hara aqui para comprobar que <i>id_usuario</i> es de tipo <i>int(11)</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param int(11)
	  */
	final public function setIdUsuario( $id_usuario )
	{
		$this->id_usuario = $id_usuario;
	}

	/**
	  * getIdDocumento
	  * 
	  * Get the <i>id_documento</i> property for this object. Donde <i>id_documento</i> es Id del documento que genero esta entrada
	  * @return int(11)
	  */
	final public function getIdDocumento()
	{
		return $this->id_documento;
	}

	/**
	  * setIdDocumento( $id_documento )
	  * 
	  * Set the <i>id_documento</i> property for this object. Donde <i>id_documento</i> es Id del documento que genero esta entrada.
	  * Una validacion basica se hara aqui para comprobar que <i>id_documento</i> es de tipo <i>int(11)</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param int(11)
	  */
	final public function setIdDocumento( $id_documento )
	{
		$this->id_documento = $id_documento;
	}

	/**
	  * getFechaRegistro
	  * 
	  * Get the <i>fecha_registro</i> property for this object. Donde <i>fecha_registro</i> es Fecha en que se registra el movimiento
	  * @return int(11)
	  */
	final public function getFechaRegistro()
	{
		return $this->fecha_registro;
	}

	/**
	  * setFechaRegistro( $fecha_registro )
	  * 
	  * Set the <i>fecha_registro</i> property for this object. Donde <i>fecha_registro</i> es Fecha en que se registra el movimiento.
	  * Una validacion basica se hara aqui para comprobar que <i>fecha_registro</i> es de tipo <i>int(11)</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param int(11)
	  */
	final public function setFechaRegistro( $fecha_registro )
	{
		$this->fecha_registro = $fecha_registro;
	}

	/**
	  * getMotivo
	  * 
	  * Get the <i>motivo</i> property for this object. Donde <i>motivo</i> es motivo por le cual sale producto del almacen
	  * @return varchar(255)
	  */
	final public function getMotivo()
	{
		return $this->motivo;
	}

	/**
	  * setMotivo( $motivo )
	  * 
	  * Set the <i>motivo</i> property for this object. Donde <i>motivo</i> es motivo por le cual sale producto del almacen.
	  * Una validacion basica se hara aqui para comprobar que <i>motivo</i> es de tipo <i>varchar(255)</i>. 
	  * Si esta validacion falla, se arrojara... algo. 
	  * @param varchar(255)
	  */
	final public function setMotivo( $motivo )
	{
		$this->motivo = $motivo;
	}

}
