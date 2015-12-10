<?php  if ( ! defined('__SITE_PATH')) exit('No direct script access allowed');  

function redirect($uri = '', $method = 'location', $http_response_code = 302)
{
	if ( ! preg_match('#^https?://#i', $uri))
	{
		$uri = site_url($uri);
	}
	
	switch($method)
	{
		case 'refresh'	: header("Refresh:0;url=".$uri);
			break;
		default			: header("Location: ".$uri, TRUE, $http_response_code);
			break;
	}
	exit;
}

function current_site_url($uri = '')
{
	$pageURL = 'http';
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"];
	}
	return $pageURL . site_url($uri);		
}

function site_url($uri = '')
{
	static $z_base_url = NULL;
	if(is_null($z_base_url))
	{
		$z_base_url = str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
	}
	
	return $z_base_url.$uri;
}

// Check varible existed or not
function df(&$value, $default = "")
{
	return empty($value) ? $default : $value;
}

function h(&$str)
{
	return isset($str) ? htmlspecialchars($str) : '';
//    	return isset($str) ? nl2br(htmlspecialchars_decode($str)) : '';
    	// Chu y : Khi su dung PDO thi no tu dong encode html khi insert, ke ca textarea cung bi thay the \n = <br/>
//     	return isset($str) ? nl2br(htmlspecialchars($str)) : '';    	
//     	return empty($str) ? '' : nl2br(htmlspecialchars($str));
}

function xh(&$str)
{
	//     	return isset($str) ? $str : '';
	//    	return isset($str) ? nl2br(htmlspecialchars_decode($str)) : '';
	// Chu y : Khi su dung PDO thi no tu dong encode html khi insert, ke ca textarea cung bi thay the \n = <br/>
	//     	return isset($str) ? nl2br(htmlspecialchars($str)) : '';
	return empty($str) ? '' : nl2br(htmlspecialchars($str));
}

function n(&$str ,$decimals = 0)
{
	return isset($str) ? number_format($str, $decimals, '.', ',') : '';
//         return empty($str) ? '' : nl2br(htmlspecialchars($str));
}

// Show data html from database
function html(&$str)
{
	return empty($str) ? '' : htmlspecialchars_decode($str);
}

function now_to_mysql()
{
	return date('Y-m-d H:i:s');
}

function mysql_to_fulldate($date)
{
	if(empty($date) || $date=='0000-00-00 00:00:00')
		return '';
	return date("Y-m-d H:i:s", strtotime($date));
}

function mysql_to_unix_timestamp($date)
{
	if(empty($date) || $date=='0000-00-00 00:00:00')
		return '';
	return strtotime($date);
}

function string_to_datetime($str_date, $str_format)
{
    // PHP 5.3 and up
    $myDateTime = DateTime::createFromFormat($str_format, $str_date);
    return $myDateTime->getTimestamp();
}

/**
* Determines if the current version of PHP is greater then the supplied value
*
* Since there are a few places where we conditionally test for PHP > 5
* we'll set a static variable.
*
* @access	public
* @param	string
* @return	bool
*/
function is_php($version = '5.0.0')
{
	static $_is_php;
	$version = (string)$version;
	
	if ( ! isset($_is_php[$version]))
	{
		$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
	}

	return $_is_php[$version];
}

// ------------------------------------------------------------------------

/**
 * Tests for file writability
 *
 * is_writable() returns TRUE on Windows servers when you really can't write to 
 * the file, based on the read-only attribute.  is_writable() is also unreliable
 * on Unix servers if safe_mode is on. 
 *
 * @access	private
 * @return	void
 */
function is_really_writable($file)
{	
	// If we're on a Unix server with safe_mode off we call is_writable
	if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
	{
		return is_writable($file);
	}

	// For windows servers and safe_mode "on" installations we'll actually
	// write a file then read it.  Bah...
	if (is_dir($file))
	{
		$file = rtrim($file, '/').'/'.md5(rand(1,100));

		if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		@chmod($file, DIR_WRITE_MODE);
		@unlink($file);
		return TRUE;
	}
	elseif (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
	{
		return FALSE;
	}

	fclose($fp);
	return TRUE;
}

/**
* Error Handler
*
* This function lets us invoke the exception class and
* display errors using the standard error template located
* in application/errors/errors.php
* This function will send the error page directly to the
* browser and exit.
*
* @access	public
* @return	void
*/
function show_error($message, $status_code = 500)
{
	$error = new MvcException();
	echo $error->show_error('An Error Was Encountered', $message, 'error_general', $status_code);
	exit;
}


/**
* 404 Page Handler
*
* This function is similar to the show_error() function above
* However, instead of the standard error template it displays
* 404 errors.
*
* @access	public
* @return	void
*/
function show_404($page = '')
{
	$error = new MvcException();
	$error->show_404($page);
	exit;
}


/**
* Error Logging Interface
*
* We use this as a simple mechanism to access the logging
* class and send messages to be logged.
*
* @access	public
* @return	void
*/
function log_message($level = 'error', $message, $php_error = FALSE)
{
//	static $LOG;
//	
//	$config =& get_config();
//	if ($config['log_threshold'] == 0)
//	{
//		return;
//	}
//
//	$LOG =& load_class('Log');
//	$LOG->write_log($level, $message, $php_error);
}


/**
 * Set HTTP Status Header
 *
 * @access	public
 * @param	int 	the status code
 * @param	string	
 * @return	void
 */
function set_status_header($code = 200, $text = '')
{
	$stati = array(
						200	=> 'OK',
						201	=> 'Created',
						202	=> 'Accepted',
						203	=> 'Non-Authoritative Information',
						204	=> 'No Content',
						205	=> 'Reset Content',
						206	=> 'Partial Content',

						300	=> 'Multiple Choices',
						301	=> 'Moved Permanently',
						302	=> 'Found',
						304	=> 'Not Modified',
						305	=> 'Use Proxy',
						307	=> 'Temporary Redirect',

						400	=> 'Bad Request',
						401	=> 'Unauthorized',
						403	=> 'Forbidden',
						404	=> 'Not Found',
						405	=> 'Method Not Allowed',
						406	=> 'Not Acceptable',
						407	=> 'Proxy Authentication Required',
						408	=> 'Request Timeout',
						409	=> 'Conflict',
						410	=> 'Gone',
						411	=> 'Length Required',
						412	=> 'Precondition Failed',
						413	=> 'Request Entity Too Large',
						414	=> 'Request-URI Too Long',
						415	=> 'Unsupported Media Type',
						416	=> 'Requested Range Not Satisfiable',
						417	=> 'Expectation Failed',

						500	=> 'Internal Server Error',
						501	=> 'Not Implemented',
						502	=> 'Bad Gateway',
						503	=> 'Service Unavailable',
						504	=> 'Gateway Timeout',
						505	=> 'HTTP Version Not Supported'
					);

	if ($code == '' OR ! is_numeric($code))
	{
		show_error('Status codes must be numeric', 500);
	}

	if (isset($stati[$code]) AND $text == '')
	{				
		$text = $stati[$code];
	}
	
	if ($text == '')
	{
		show_error('No status text available.  Please check your status code number or supply your own message text.', 500);
	}
	
	$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

	if (substr(php_sapi_name(), 0, 3) == 'cgi')
	{
		header("Status: {$code} {$text}", TRUE);
	}
	elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0')
	{
		header($server_protocol." {$code} {$text}", TRUE, $code);
	}
	else
	{
		header("HTTP/1.1 {$code} {$text}", TRUE, $code);
	}
}

//// underscored to upper-camelcase 
//// e.g. "this_method_name" -> "ThisMethodName" 
function upperCamelcase($string)
{
	return preg_replace('/(?:^|-)(.?)/e',"strtoupper('$1')",$string);
}

//// underscored to lower-camelcase 
//// e.g. "this_method_name" -> "thisMethodName" 
function lowerCamelcase($string)
{
	return preg_replace('/-(.?)/e',"strtoupper('$1')",$string);
}	

// camelcase (lower or upper) to hyphen 
// e.g. "thisMethodName" -> "this_method_name" 
// e.g. "ThisMethodName" -> "this_method_name"
// Of course these aren't 100% symmetric.  For example...
//  * this_is_a_string -> ThisIsAString -> this_is_astring
//  * GetURLForString -> get_urlfor_string -> GetUrlforString 
function camelcaseToHyphen($string)
{
	return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1-$2", $string));
}

/**
 * Autoload define
 *
 * @access	public
 * @param	string
 * @return	bool
 */
function _autoload($class)
{	
	if (class_exists($class, false) || interface_exists($class, false)) {
		return;
	}	

	$file = __SITE_PATH . "/application/models/" . $class .'.model.php';
	if (file_exists($file) == TRUE)
	{
		include_once $file;
		return TRUE;
	}
	
	$file = __SITE_PATH . '/lib/' . $class . '.class.php';
	if (file_exists($file) == TRUE)
	{
		include_once $file;
		return TRUE;
	}

	// Load Zend library
	$paths = explode('_', $class);
	if($paths[0] == "Zend")
	{
		$file = __SITE_PATH . '/lib/' . str_replace('_', '/', $class) . '.php';
		if (file_exists($file)) 
		{
			include_once $file;
			return TRUE;
		}    			
	}
	
	// Load Model
	for ($i = 0; $i < count($paths) - 1 ; $i++) 
		$paths[$i] = camelcaseToHyphen($paths[$i]);	

	$file = __SITE_PATH . "/application/models/" . join('/',$paths) . '.model.php';
	if (file_exists($file) == TRUE)
	{
		include_once $file;
		return TRUE;
	}
	
	return FALSE;
}
/*** registry auto load ***/
spl_autoload_register(null, FALSE);
spl_autoload_extensions('.php, .class.php, .lang.php, .model.php');
spl_autoload_register('_autoload');

/**
 * Load helper function
 *
 * @access	public
 * @param	string
 * @return	bool
 */
function helperLoader($functions)
{
	if(!is_array($functions))
		$functions = array($functions);
		
	foreach ($functions as $function)
	{
		$file_path = __HELPER_PATH . "/{$function}.helper.php";
		if(file_exists($file_path))
			include_once $file_path;
	}			
}

/**
* Exception Handler
*
* This is the custom exception handler that is declaired at the top
* of Codeigniter.php.  The main reason we use this is permit
* PHP errors to be logged in our own log files since we may
* not have access to server logs. Since this function
* effectively intercepts PHP errors, however, we also need
* to display errors based on the current error_reporting level.
* We do that with the use of a PHP error template.
*
* @access	private
* @return	void
*/
function _exception_handler($severity, $message, $filepath, $line)
{	
	 // We don't bother with "strict" notices since they will fill up
	 // the log file with information that isn't normally very
	 // helpful.  For example, if you are running PHP 5 and you
	 // use version 4 style class functions (without prefixes
	 // like "public", "private", etc.) you'll get notices telling
	 // you that these have been deprecated.
	 
	if ($severity == E_STRICT)
	{
		return;
	}

	// Should we display the error?
	// We'll get the current error_reporting level and add its bits
	// with the severity bits to find out.
	
	if (($severity & error_reporting()) == $severity)
	{
		$error = new MvcException();
		$error->show_php_error($severity, $message, $filepath, $line);
	}

	return TRUE;	
}

if ( ! function_exists('ip_address'))
{
	function ip_address()
	{
	    static $ip = FALSE;
	    
	    if( $ip ) {
	        return $ip;
	    }
	    //Get IP address - if proxy lets get the REAL IP address
	
	    if (!empty($_SERVER['REMOTE_ADDR']) AND !empty($_SERVER['HTTP_CLIENT_IP'])) {
	        $ip = $_SERVER['HTTP_CLIENT_IP'];
	    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
	        $ip = $_SERVER['REMOTE_ADDR'];
	    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	        $ip = $_SERVER['HTTP_CLIENT_IP'];
	    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    } else {
	        $ip = '0.0.0.0';
	    }
	
	    //Clean the IP and return it
	    return $ip = preg_replace('/[^0-9\.]+/', '', $ip);
	}
}

/**
 * Create a encryption string
 *
 * @return string
 */
function encryption($string ,$salt = "")
{
	return sha1($salt.$string);	
}

/**
 * Create a fairly random 32 character MD5 token
 *
 * @return string
 */
function token()
{
    return md5(str_shuffle(chr(mt_rand(32, 126)). uniqid(). microtime(TRUE)));
}

/**
 * Encode a string so it is safe to pass through the URI
 * @param string $string
 * @return string
 */
function base64_url_encode($string = NULL)
{
    return strtr(base64_encode($string), '+/=', '-_~');
}

/**
 * Decode a string passed through the URI
 *
 * @param string $string
 * @return string
 */
function base64_url_decode($string = NULL)
{
    return base64_decode(strtr($string, '-_~','+/='));
}

// ------------------------------------------------------------------------

/**
 * Function usable
 *
 * Executes a function_exists() check, and if the Suhosin PHP
 * extension is loaded - checks whether the function that is
 * checked might be disabled in there as well.
 *
 * This is useful as function_exists() will return FALSE for
 * functions disabled via the *disable_functions* php.ini
 * setting, but not for *suhosin.executor.func.blacklist* and
 * *suhosin.executor.disable_eval*. These settings will just
 * terminate script execution if a disabled function is executed.
 *
 * @link	http://www.hardened-php.net/suhosin/
 * @param	string	$function_name	Function to check for
 * @return	bool	TRUE if the function exists and is safe to call,
 *			FALSE otherwise.
 */
function function_usable($function_name)
{
	static $_suhosin_func_blacklist = NULL;

	if (function_exists($function_name))
	{
		if ( ! isset($_suhosin_func_blacklist))
		{
			if (extension_loaded('suhosin'))
			{
				$_suhosin_func_blacklist = explode(',', trim(@ini_get('suhosin.executor.func.blacklist')));

				if ( ! in_array('eval', $_suhosin_func_blacklist, TRUE) && @ini_get('suhosin.executor.disable_eval'))
				{
					$_suhosin_func_blacklist[] = 'eval';
				}
			}
			else
			{
				$_suhosin_func_blacklist = array();
			}
		}

		return ! in_array($function_name, $_suhosin_func_blacklist, TRUE);
	}

	return FALSE;
}

/**
 * Returns the MIME types array from config/mimes.php
 *
 * @return	array
 */
function &get_mimes()
{
	/*
	 | -------------------------------------------------------------------
	| MIME TYPES
	| -------------------------------------------------------------------
	| This file contains an array of mime types.  It is used by the
	| Upload class to help identify allowed file types.
	|
	*/		
	
	static $_mimes = array(
			'hqx'	=>	array('application/mac-binhex40', 'application/mac-binhex', 'application/x-binhex40', 'application/x-mac-binhex40'),
			'cpt'	=>	'application/mac-compactpro',
			'csv'	=>	array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'),
			'bin'	=>	array('application/macbinary', 'application/mac-binary', 'application/octet-stream', 'application/x-binary', 'application/x-macbinary'),
			'dms'	=>	'application/octet-stream',
			'lha'	=>	'application/octet-stream',
			'lzh'	=>	'application/octet-stream',
			'exe'	=>	array('application/octet-stream', 'application/x-msdownload'),
			'class'	=>	'application/octet-stream',
			'psd'	=>	array('application/x-photoshop', 'image/vnd.adobe.photoshop'),
			'so'	=>	'application/octet-stream',
			'sea'	=>	'application/octet-stream',
			'dll'	=>	'application/octet-stream',
			'oda'	=>	'application/oda',
			'pdf'	=>	array('application/pdf', 'application/force-download', 'application/x-download', 'binary/octet-stream'),
			'ai'	=>	array('application/pdf', 'application/postscript'),
			'eps'	=>	'application/postscript',
			'ps'	=>	'application/postscript',
			'smi'	=>	'application/smil',
			'smil'	=>	'application/smil',
			'mif'	=>	'application/vnd.mif',
			'xls'	=>	array('application/vnd.ms-excel', 'application/msexcel', 'application/x-msexcel', 'application/x-ms-excel', 'application/x-excel', 'application/x-dos_ms_excel', 'application/xls', 'application/x-xls', 'application/excel', 'application/download', 'application/vnd.ms-office', 'application/msword'),
			'ppt'	=>	array('application/powerpoint', 'application/vnd.ms-powerpoint'),
			'pptx'	=> 	array('application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/x-zip', 'application/zip'),
			'wbxml'	=>	'application/wbxml',
			'wmlc'	=>	'application/wmlc',
			'dcr'	=>	'application/x-director',
			'dir'	=>	'application/x-director',
			'dxr'	=>	'application/x-director',
			'dvi'	=>	'application/x-dvi',
			'gtar'	=>	'application/x-gtar',
			'gz'	=>	'application/x-gzip',
			'gzip'  =>	'application/x-gzip',
			'php'	=>	array('application/x-httpd-php', 'application/php', 'application/x-php', 'text/php', 'text/x-php', 'application/x-httpd-php-source'),
			'php4'	=>	'application/x-httpd-php',
			'php3'	=>	'application/x-httpd-php',
			'phtml'	=>	'application/x-httpd-php',
			'phps'	=>	'application/x-httpd-php-source',
			'js'	=>	array('application/x-javascript', 'text/plain'),
			'swf'	=>	'application/x-shockwave-flash',
			'sit'	=>	'application/x-stuffit',
			'tar'	=>	'application/x-tar',
			'tgz'	=>	array('application/x-tar', 'application/x-gzip-compressed'),
			'z'	=>	'application/x-compress',
			'xhtml'	=>	'application/xhtml+xml',
			'xht'	=>	'application/xhtml+xml',
			'zip'	=>	array('application/x-zip', 'application/zip', 'application/x-zip-compressed', 'application/s-compressed', 'multipart/x-zip'),
			'rar'	=>	array('application/x-rar', 'application/rar', 'application/x-rar-compressed'),
			'mid'	=>	'audio/midi',
			'midi'	=>	'audio/midi',
			'mpga'	=>	'audio/mpeg',
			'mp2'	=>	'audio/mpeg',
			'mp3'	=>	array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
			'aif'	=>	array('audio/x-aiff', 'audio/aiff'),
			'aiff'	=>	array('audio/x-aiff', 'audio/aiff'),
			'aifc'	=>	'audio/x-aiff',
			'ram'	=>	'audio/x-pn-realaudio',
			'rm'	=>	'audio/x-pn-realaudio',
			'rpm'	=>	'audio/x-pn-realaudio-plugin',
			'ra'	=>	'audio/x-realaudio',
			'rv'	=>	'video/vnd.rn-realvideo',
			'wav'	=>	array('audio/x-wav', 'audio/wave', 'audio/wav'),
			'bmp'	=>	array('image/bmp', 'image/x-bmp', 'image/x-bitmap', 'image/x-xbitmap', 'image/x-win-bitmap', 'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp', 'application/bmp', 'application/x-bmp', 'application/x-win-bitmap'),
			'gif'	=>	'image/gif',
			'jpeg'	=>	array('image/jpeg', 'image/pjpeg'),
			'jpg'	=>	array('image/jpeg', 'image/pjpeg'),
			'jpe'	=>	array('image/jpeg', 'image/pjpeg'),
			'png'	=>	array('image/png',  'image/x-png'),
			'tiff'	=>	'image/tiff',
			'tif'	=>	'image/tiff',
			'css'	=>	array('text/css', 'text/plain'),
			'html'	=>	array('text/html', 'text/plain'),
			'htm'	=>	array('text/html', 'text/plain'),
			'shtml'	=>	array('text/html', 'text/plain'),
			'txt'	=>	'text/plain',
			'text'	=>	'text/plain',
			'log'	=>	array('text/plain', 'text/x-log'),
			'rtx'	=>	'text/richtext',
			'rtf'	=>	'text/rtf',
			'xml'	=>	array('application/xml', 'text/xml', 'text/plain'),
			'xsl'	=>	array('application/xml', 'text/xsl', 'text/xml'),
			'mpeg'	=>	'video/mpeg',
			'mpg'	=>	'video/mpeg',
			'mpe'	=>	'video/mpeg',
			'qt'	=>	'video/quicktime',
			'mov'	=>	'video/quicktime',
			'avi'	=>	array('video/x-msvideo', 'video/msvideo', 'video/avi', 'application/x-troff-msvideo'),
			'movie'	=>	'video/x-sgi-movie',
			'doc'	=>	array('application/msword', 'application/vnd.ms-office'),
			'docx'	=>	array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword', 'application/x-zip'),
			'dot'	=>	array('application/msword', 'application/vnd.ms-office'),
			'dotx'	=>	array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword'),
			'xlsx'	=>	array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/vnd.ms-excel', 'application/msword', 'application/x-zip'),
			'word'	=>	array('application/msword', 'application/octet-stream'),
			'xl'	=>	'application/excel',
			'eml'	=>	'message/rfc822',
			'json'  =>	array('application/json', 'text/json'),
			'pem'   =>	array('application/x-x509-user-cert', 'application/x-pem-file', 'application/octet-stream'),
			'p10'   =>	array('application/x-pkcs10', 'application/pkcs10'),
			'p12'   =>	'application/x-pkcs12',
			'p7a'   =>	'application/x-pkcs7-signature',
			'p7c'   =>	array('application/pkcs7-mime', 'application/x-pkcs7-mime'),
			'p7m'   =>	array('application/pkcs7-mime', 'application/x-pkcs7-mime'),
			'p7r'   =>	'application/x-pkcs7-certreqresp',
			'p7s'   =>	'application/pkcs7-signature',
			'crt'   =>	array('application/x-x509-ca-cert', 'application/x-x509-user-cert', 'application/pkix-cert'),
			'crl'   =>	array('application/pkix-crl', 'application/pkcs-crl'),
			'der'   =>	'application/x-x509-ca-cert',
			'kdb'   =>	'application/octet-stream',
			'pgp'   =>	'application/pgp',
			'gpg'   =>	'application/gpg-keys',
			'sst'   =>	'application/octet-stream',
			'csr'   =>	'application/octet-stream',
			'rsa'   =>	'application/x-pkcs7',
			'cer'   =>	array('application/pkix-cert', 'application/x-x509-ca-cert'),
			'3g2'   =>	'video/3gpp2',
			'3gp'   =>	'video/3gp',
			'mp4'   =>	'video/mp4',
			'm4a'   =>	'audio/x-m4a',
			'f4v'   =>	'video/mp4',
			'webm'	=>	'video/webm',
			'aac'   =>	'audio/x-acc',
			'm4u'   =>	'application/vnd.mpegurl',
			'm3u'   =>	'text/plain',
			'xspf'  =>	'application/xspf+xml',
			'vlc'   =>	'application/videolan',
			'wmv'   =>	array('video/x-ms-wmv', 'video/x-ms-asf'),
			'au'    =>	'audio/x-au',
			'ac3'   =>	'audio/ac3',
			'flac'  =>	'audio/x-flac',
			'ogg'   =>	'audio/ogg',
			'kmz'	=>	array('application/vnd.google-earth.kmz', 'application/zip', 'application/x-zip'),
			'kml'	=>	array('application/vnd.google-earth.kml+xml', 'application/xml', 'text/xml'),
			'ics'	=>	'text/calendar',
			'zsh'	=>	'text/x-scriptzsh',
			'7zip'	=>	array('application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip'),
			'cdr'	=>	array('application/cdr', 'application/coreldraw', 'application/x-cdr', 'application/x-coreldraw', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr'),
			'wma'	=>	array('audio/x-ms-wma', 'video/x-ms-asf'),
			'jar'	=>	array('application/java-archive', 'application/x-java-application', 'application/x-jar', 'application/x-compressed')
	);		

	return $_mimes;
}

/*
 * XSS filter
*
* This was built from numerous sources
* (thanks all, sorry I didn't track to credit you)
*
* It was tested against *most* exploits here: http://ha.ckers.org/xss.html
* WARNING: Some weren't tested!!!
* Those include the Actionscript and SSI samples, or any newer than Jan 2011
*
*
* TO-DO: compare to SymphonyCMS filter:
* https://github.com/symphonycms/xssfilter/blob/master/extension.driver.php
* (Symphony's is probably faster than my hack)
*/

function xss_clean($data)
{
	$data = str_replace(array("\r", "\n"), "", $data);
	
	// Fix &entity\n;
	$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
	$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
	$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
	$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

	// Remove any attribute starting with "on" or xmlns
	$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

	// Remove javascript: and vbscript: protocols
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

	// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

	// Remove namespaced elements (we do not need them)
	$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
	
	

	do
	{
		// Remove really unwanted tags
		$old_data = $data;
		$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
	}
	while ($old_data !== $data);

	// we are done...
	return $data;
}	

/**
 * Strip Image Tags
 *
 * @param        string        $str
 * @return        string
 */
function strip_image_tags($str)
{
	return preg_replace(array('#<img[\s/]+.*?src\s*=\s*["\'](.+?)["\'].*?\>#', '#<img[\s/]+.*?src\s*=\s*(.+?).*?\>#'), '\\1', $str);
}

function str2url($str = NULL, $sperator = "-")
{
	if(!$str) return NULL;
	
	$str = mb_strtolower($str,'utf-8');
	$str = textToVN($str);
			
	$str = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;', '*', '/')," ",$str);
	$str = preg_replace("/[^a-zA-Z0-9- ]/", "-", $str);
	$str = preg_replace('/\s\s+/', ' ', $str );
	$str = trim($str);
	$str = preg_replace('/\s+/', $sperator, $str );
	
	$str = str_replace("----","-",$str);
	$str = str_replace("---","-",$str);
	$str = str_replace("--","-",$str);
	$str = trim($str, $sperator);
	return strtolower($str);
}

function textToVN($str)
{
	$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
	$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
	$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
	$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
	$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
	$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
	$str = preg_replace("/(đ)/", "d", $str);

	$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
	$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
	$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
	$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
	$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
	$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
	$str = preg_replace("/(Đ)/", "D", $str);

	return $str;
}

function word_limiter($str, $limit = 100, $end_char = '&#8230;')
{
	if (trim($str) == '')
	{
		return $str;
	}

	preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

	if (strlen($str) == strlen($matches[0]))
	{
		$end_char = '';
	}

	return rtrim($matches[0]).$end_char;
}

// 	http://phpgoogle.blogspot.com/2007/08/four-ways-to-generate-unique-id-by-php.html
// 	http://kvz.io/blog/2009/06/10/create-short-ids-with-php-like-youtube-or-tinyurl/
function create_uniqid($random_id_length = 10)
{
	//generate a random id encrypt it and store it in $rnd_id
	$rnd_id = crypt(uniqid(rand(),1), 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
	
	//to remove any slashes that might have come
	$rnd_id = strip_tags(stripslashes($rnd_id));
	
	//Removing any . or / and reversing the string
	$rnd_id = str_replace(".","",$rnd_id);
	$rnd_id = strrev(str_replace("/","",$rnd_id));
	
	//finally I take the first 10 characters from the $rnd_id
	$rnd_id = substr($rnd_id,0,$random_id_length);		
	
	return $rnd_id;
}

//function parse_server_uri($prefix_slash = true)
//{
//	if (isset($_SERVER['PATH_INFO']))
//	{
//		$uri = $_SERVER['PATH_INFO'];
//	}
//	elseif (isset($_SERVER['REQUEST_URI']))
//	{
//		$uri = $_SERVER['REQUEST_URI'];
//		if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
//		{
//			$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
//		}
//		elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
//		{
//			$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
//		}
//
//		// This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
//		// URI is found, and also fixes the QUERY_STRING server var and $_GET array.
//		if (strncmp($uri, '?/', 2) === 0)
//		{
//			$uri = substr($uri, 2);
//		}
//		$parts = preg_split('#\?#i', $uri, 2);
//		$uri = $parts[0];
//		if (isset($parts[1]))
//		{
//			$_SERVER['QUERY_STRING'] = $parts[1];
//			parse_str($_SERVER['QUERY_STRING'], $_GET);
//		}
//		else
//		{
//			$_SERVER['QUERY_STRING'] = '';
//			$_GET = array();
//		}
//		$uri = parse_url($uri, PHP_URL_PATH);
//	}
//	else
//	{
//		// Couldn't determine the URI, so just return false
//		return false;
//	}
//	$_SERVER['URL_ROUTER'] = ($prefix_slash ? '/' : '').str_replace(array('//', '../'), '/', trim($uri, '/'));
//	// Do some final cleaning of the URI and return it
//	return $_SERVER['URL_ROUTER'];
//}