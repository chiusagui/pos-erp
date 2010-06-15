<?php include_once("libBD.php");
	class cotizacion{
		var $id_cotizacion;	 	 	 	 	 	 	
		var $id_cliente;	 	 	 	 	 	 	
		var $fecha;	 	 	 	 	 	
		var $subtotal;	 	 	 	 	 	 	
		var $iva;
		var $bd;
		
		function __construct($id_cliente){ 	 	 	 	 	 	
			$this->id_cliente=$id_cliente;		 	 	
			$this->subtotal=0;	 	 	 	 	 	 	
			$this->iva=0;
			$this->bd=new bd_default();
		}
		function __destruct(){ 
			 return; 
		}
		
		function inserta(){
			$insert="INSERT INTO  cotizacion values(NULL,?,CURDATE( ),?,?);";
			$params=array($this->id_cliente,$this->subtotal,$this->iva);
			if($this->bd->ejecuta($insert,$params)){
				$query="select max(id_cotizacion) from cotizacion;";
				$this->id_cotizacion=$this->bd->select_un_campo($query,array());
				return true;
			}else return false;
		}
		function actualiza(){
			$update="UPDATE  cotizacion SET `id_cliente`=?, `fecha`=curdate(), `subtotal`=?, `iva`=? where id_cotizacion=?";
			$params=array($this->id_cliente,$this->subtotal,$this->iva,$this->id_cotizacion);
			return $this->bd->ejecuta($update,$params);
		}
		function json(){
			$query="SELECT id_cotizacion ,id_cliente,fecha,subtotal,iva,(iva + subtotal) as total FROM `cotizacion` where id_cotizacion =?;";
			$params=array($this->id_cotizacion);
			return $this->bd->select_json($query,$params);
		}
		function borra (){
			$query="delete from cotizacion where id_cotizacion=?;";
			$params=array($this->id_cotizacion);
			return $this->bd->ejecuta($query,$params);
		}
		function obtener_datos($id){
			$query="SELECT id_cotizacion ,id_cliente,fecha,subtotal,iva,(iva + subtotal) as total FROM `cotizacion`  where id_cotizacion=?;";
			$params=array($id);
			$datos=$this->bd->select_uno($query,$params);
			$this->id_cotizacion=$datos[id_cotizacion];	
			$this->id_cliente=$datos[id_cliente];	
			$this->fecha=$datos[fecha];	
			$this->subtotal=$datos[subtotal];	
			$this->iva=$datos[iva];
		}
		function detalle_cotizacion($id){
			$query = "select * from detalle_cotizacion where id_cotizacion=?;";
			$params=array($id);
			return 	$productos=$this->bd->select_arr($query,$params);
		}
		function existe(){
			$query="select id_cotizacion from cotizacion where id_cotizacion=?;";
			$params=array($this->id_cotizacion);
			return $this->bd->existe($query,$params);
		}
		function productos(){
			$query="SELECT id_producto FROM detalle_cotizacion where id_cotizacion=?";
			$params=array($this->id_cotizacion);
			return $this->bd->select_arr($query,$params);
		}
	}
	class cotizacion_vacio extends cotizacion {      
		public function __construct() {
			$this->bd=new bd_default();
		}
	}
	class cotizacion_existente extends cotizacion {
		public function __construct($id) {
			$this->bd=new bd_default();
			$this->obtener_datos($id);
		}
	}
?>