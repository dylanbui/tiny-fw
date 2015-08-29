<?php

/**
 *
 * @Class logger
 *
 * @Purpose: Logs text to a log file specified in config
 *
 * @example usage
 * $log = Logger::debugLog( "This is a debug log message", 210, __FILE__, __LINE__ );
 * $log = Logger::auditLog( "This is an audit log message", 220, __FILE__, __LINE__ );
 *
 *
 */

class Logger
{

	/**
	 *
	 * @Constructor is set to private to stop instantiation
	 *
	 */
	private function __construct()
	{
	}

	/**
	 *
	 * @write to the logfile
	 *
	 * @access public
	 *
	 * @param	string	$function The name of the function called
	 * @param 	array	$args	The array of args passed
	 * @return	int	The number of bytes written, false other wise
	 *
	 */
	public static function __callStatic($function, $args)
	{
		// args[0] constains the error message
		// args[1] contains the log level
		// args[2] constains the filename
		// args[3] constains the line number
		$config = Config::getInstance();
		if( $args[1] <= $config->config_values['logging']['log_level'] )
		{
			$line = array(
				'log_function'	=> $function,
				'log_message' 	=> $args[0],
				'log_level'	=> $args[1],
				'log_file'	=> $args[2],
				'log_line'	=> $args[3]);

			switch( $config->config_values['logging']['log_handler'] )
			{
				case 'file':
					// set the log date/time
					$line['log_time'] = date( DATE_ISO8601 );
					// encode the line
					$json = json_encode( $line )."\n";
	
					if ($handle = fopen( __SITE_PATH . $config->config_values['logging']['log_file'], "a+") )
					{
						if( !fwrite( $handle, $json ) )
						{
							throw new Exception("Unable to write to log file");
						}
						fclose( $handle );
					}
				break;

				case 'database':
					/* Xu ly insert log vao database*/
				break;

				default:
				throw new Exception("Invalid Log Option");
			}
		}
	}

	/**
	 *
	 * Clone is set to private to stop cloning
	 *
	 */
	private function __clone()
	{
	}
	
} // end of log class

?>
