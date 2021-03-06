package mx.caffeina.pos.Bascula;

import java.util.List;

import giovynet.nativelink.SerialPort;
import giovynet.serial.Baud;
import giovynet.serial.Com;
import giovynet.serial.Parameters;
import mx.caffeina.pos.Logger;

public class Bascula{
	
	
	private SerialPort serialPort ;
	private List<String> portsFree;
	private Com com;




	public static List<String> getFreePorts ( ) throws UnsatisfiedLinkError, Exception {
		
		Logger.log("Instanciando SerialPort para buscar puertos libres !!");
		return new SerialPort().getFreeSerialPort();	
	}
	


	/**
	* 
	* 
	* */
	public Bascula( String port ) throws UnsatisfiedLinkError, Exception {
		
		Logger.log("Instanciando bascula con el puerto ("+port+")!");
		
		serialPort = new SerialPort();
		portsFree = serialPort.getFreeSerialPort();
		
		// If there are free ports, use the first found. 
        if ( !(portsFree != null && portsFree.size() > 0) ) {
			Logger.warn("---- No Free ports !!! ----");
			throw new Exception("No free ports !");
		}
	
		int portNeededIndex = -1, i = -1;
		for (String free : portsFree)
		{
			i++;
			if(free.equals(port)){
				portNeededIndex = i;
			}
			//Logger.log("Free port["+i+"]: " + free);
		}
		
		if(portNeededIndex == -1){
			Logger.warn("Needed port does not exist !");
			throw new Exception("Needed port does not exist !");
		}
		
		
		/****Open the port.****/
        Parameters parameters = null;
       	parameters = new Parameters();			

		parameters.setPort(portsFree.get(portNeededIndex));
		
		parameters.setBaudRate(Baud._9600);
		parameters.setByteSize("8");
		parameters.setParity("N");
		parameters.setStopBits("1");
		//parameters.setMinDelayWrite(1250);
		
		Logger.log("Open port: " + portsFree.get(portNeededIndex));

		com = new Com(parameters);			

	}
	
	/**
	* 
	* 
	* */
	public void sendCommand(String command) throws Exception {
		com.sendSingleData( command );
	}
	
	
	
	/**
	* 
	* 
	* */
	public String getRawData(){
		getRawData(1);
		return getRawData(10);
	}
	
	
	/**
	* 
	* 
	* */
	public String getRawData(int bytes){
		
		try{

			
			/* * * * * * */
			String rd = com.receiveToString(bytes);
			rd = rd.replaceAll("\\p{Cntrl}", "");
			rd = rd.replaceAll("[^\\p{ASCII}]", "");

			StringBuilder filtered = new StringBuilder(rd.length());

			for (int i = 0; i < rd.length(); i++) 
			{
				char current = rd.charAt(i);
				if (current >= 0x20 && current <= 0x7e) 
				{
					filtered.append(current);
				}
			}

		    return filtered.toString();
			/* * * * * * */


		}catch(Exception e){
			Logger.error(e);
			return null;

		}

	}
	
	
	/**
	* 
	* 
	* */	
	public void close(){
		try{
			com.close();
		}catch(Exception e){
			Logger.error(e);
			return;
		}
	}



}//class Bascula