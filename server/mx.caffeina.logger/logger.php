<?php
/**
* Archivo de configuración para el <i>logging</i> del sistema.
*
* Usa el <i>framework</i> <code>Log</code> para realizar el <i>log</i> del sistema,
* este archivo solo configura la clase, hace una instancia y deja el objeto listo
* para ser usado.
*
*
* Archivo donde se guardara el <i>log</i>.
*
* @internal El archivo debe existir y el usuario donde esta ejecutandose
* el navegador, p.ej. <code>www-data</code> debe ser capaz de escribir 
* en el archivo. Comunmente
* <code># touch /var/log/php/app.log && chmod 777 /var/log/php/app.log</code>
* son suficientes.
*
* Aplica al segundo parametro de {@link Log::singleton()}.
*
*
* Identificación primaria del sistema en el archivo de <i>log</i>.
*
* Se puede especificar otra identificación usando el método {@link Log::setIdent()}
*
* Aplica el tercer parametro de {@link Log::singleton()}
*
*
* Parametros de configuración para la línea de log.
*
* Parameter     |       Type    | 	Default                 | 	Description
* ==============|===============|===============================|===============================================================
* append 	|       Boolean |	True                    |       Should new log entries be append to an existing log file, or should the a new log file overwrite an existing one?
* mode          |       Integer |	0644                    |       Octal representation of the log file's permissions mode.
* eol           |       String 	|       OS default 	        |       The end-on-line character sequence.
* lineFormat    |       String 	|       %1$s %2$s [%3$s] %4$s   | 	Log line format specification.
* timeFormat    |       String 	|       %b %d %H:%M:%S 	        |       Time stamp format (for strftime).
*
* Donde lineFormat acepta los siguientes parametros.
*
* Token         |       Alternate       | 	Description
*===============|=======================|==================================================
* %{timestamp}  | 	%1$s            |       Timestamp. This is often configurable.
* %{ident} 	|       %2$s 	        |       The log handler's identification string.
* %{priority} 	|       %3$s 	        |       The log event's priority.
* %{message} 	|       %4$s    	|       The log event's message text.
* %{file} 	|       %5$s 	        |       The full filename of the logging file.
* %{line} 	|       %6$s 	        |       The line number on which the event occured.
* %{function} 	|       %7$s 	        |       The function from which the event occurred.
* %{class} 	|       %8$s 	        |       The class in which the event occurred.
*
* Aplica al cuarto parametro de {@link Log::singleton()}
*
*
* Prioridad del <i>log</i>.
*
* Establece el nivel de <i>logging</i> en el sistema. Usa los mismos
* niveles de prioridad que {@link http://www.indelible.org/php/Log/guide.html#log-levels <code>Log</code>}.
*
* Level                 |       Description
* ----------------------|-------------------------
* PEAR_LOG_EMERG        |       System is unusable
* PEAR_LOG_ALERT 	|       Immediate action required
* PEAR_LOG_CRIT 	|       Critical conditions
* PEAR_LOG_ERR 	        |       Error conditions
* PEAR_LOG_WARNING 	|       Warning conditions
* PEAR_LOG_NOTICE 	|       Normal but significant
* PEAR_LOG_INFO 	|       Informational
* PEAR_LOG_DEBUG 	|       Debug-level messages
*
* Esta tabla esta ordenada de alta (<code>PEAR_LOG_EMERG</code>) a baja (<code>PEAR_LOG_DEBUG</code>) prioridad.
*
* Aplica al quinto parametro de {@link Log::singleton()}.
*
*
* @package com.hdclass.logger
* @link http://pear.php.net/package/Log Log
* @author Manuel Alejandro Gómez Nicasio <alejandro.gomez@alejandrogomez.org>
* @example logger.ex.php Uso de la clase <code>Logger</code>.
*/

/**
 * Framework <code>Log</code>
 *
 * @link http://pear.php.net/package/Log/ Log
 */
require_once('Log.php');

$conf = array('lineFormat' => '%1$s %2$s [%3$s] %4$s in %5$s on line %6$s');

$logger = &Log::singleton('file', '/var/log/php/app.log', 'SYSTEM CORE', $conf, PEAR_LOG_DEBUG);
