<?php include_once("libBD.php");
	class venta{
		var $id_venta;	 	 	 	 	 	 	
		var $id_cliente;	 	 	 	 	 	 	 
		var $tipo_venta;	 	 	 	 	 	 	 
		var $fecha; 	 	 	 	 	 	 
		var $subtotal;	 	 	 	 	 	 	 
		var $iva;	 	 	 	 	 	 	 
		var $sucursal;	 	 
		var $id_usuario;
		var $bd;
		
		function __construct($id_cliente,$tipo_venta,$sucursal,$id_usuario){ 	 	 	 	 	 	
				$this->id_cliente=$id_cliente;	 	 	 	 	 	 	 
				$this->tipo_venta=$tipo_venta;	 	 	 	 	 	 
				$this->sucursal=$sucursal;	 	 
				$this->id_usuario=$id_usuario;
				$this->subtotal=0;
				$this->iva=0;
				$this->bd=new bd_default();
		}
		function __destruct(){ 
			 return; 
		}
		
		function inserta(){
			$insert="INSERT INTO  ventas values(NULL,?,?,null,?,?,?,?);";
			$params=array($this->id_cliente,$this->tipo_venta,$this->subtotal,$this->iva,$this->sucursal,$this->id_usuario);
			if($this->bd->ejecuta($insert,$params)){
				$query="select max(id_venta) from ventas;";
				$this->id_venta=$this->bd->select_un_campo($query,array());
				return true;
			}else return false;
		}
		function actualiza(){
			$update="UPDATE  ventas SET  `id_cliente`=?,`tipo_venta` =?,`fecha` =CURRENT_TIMESTAMP,`subtotal` =?,`iva` =?,`sucursal` =?, `id_usuario`=? where id_venta=?;";
			$params=array($this->id_cliente,$this->tipo_venta,$this->subtotal,$this->iva,$this->sucursal,$this->id_usuario,$this->id_venta);
			return $this->bd->ejecuta($update,$params);
		}
		function json(){
			$query="select * from ventas where id_venta =?;";
			$params=array($this->id_venta);
			return $this->bd->select_json($query,$params);
		}
		function borra (){
			$query="delete from ventas where id_venta=?;";
			$params=array($this->id_venta);
			return $this->bd->ejecuta($query,$params);
		}
		function obtener_datos($id){
			$query="select * from ventas where id_venta=?;";
			$params=array($id);
			$datos=$this->bd->select_uno($query,$params);
			$this->id_venta=$datos[id_venta];	 	 	 		
			$this->id_cliente=$datos[id_cliente];	 	 	 		
			$this->tipo_venta=$datos[tipo_venta];	 	 	 		
			$this->fecha=$datos[fecha];	 	 	 		
			$this->subtotal=$datos[subtotal];	 	 	 		
			$this->iva=$datos[iva];	 	 	 		
			$this->sucursal=$datos[sucursal];	 	 	 		
			$this->id_usuario=$datos[id_usuario];	 		
		}
		function existe(){
			$query="select id_venta from ventas where id_venta=?;";
			$params=array($this->id_venta);
			return $this->bd->existe($query,$params);
		}
	}
	class venta_vacio extends venta {      
		public function __construct() {
			$this->bd=new bd_default();
		}
	}
	class venta_existente extends venta {
		public function __construct($id) {
			$this->bd=new bd_default();
			$this->obtener_datos($id);
		}
	}
?>