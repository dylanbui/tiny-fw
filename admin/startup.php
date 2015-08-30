<?php  if ( ! defined('__SITE_PATH')) exit('No direct script access allowed');

	// Set include path to Zend (and other) libraries
    set_include_path(__SITE_PATH . '/lib' .
        PATH_SEPARATOR . get_include_path() .
        PATH_SEPARATOR . '.'
    );
    
    require __SITE_PATH . '/lib/core/Config.class.php';
    
    // Create configure object
    $config = Config::getInstance();
    
    // Load config files. Global config file
    require __SITE_PATH . '/application/config/constants.php';    
    
	// Load helper file . Master function
	require __HELPER_PATH . '/common.helper.php';
    
	require __SITE_PATH . '/lib/core/FrontController.class.php';
	require __SITE_PATH . '/lib/core/Module.class.php';
	require __SITE_PATH . '/lib/core/BaseController.class.php';
	require __SITE_PATH . '/lib/core/Request.class.php';
	require __SITE_PATH . '/lib/core/Response.class.php';
	require __SITE_PATH . '/lib/core/View.class.php';
	require __SITE_PATH . '/lib/core/Registry.class.php';
	require __SITE_PATH . '/lib/core/MvcException.class.php';
	require __SITE_PATH . '/lib/core/Model.class.php'; /* Kieu ket noi don gian */
	require __SITE_PATH . '/lib/core/Benchmark.class.php';
	
 	/*** include the helper ***/
	helperLoader($_autoload_helpers);
	
	// Load language  
	$lang = $config->config_values['application']['language'];
	if(!empty($lang))
	{
		$file = __APP_PATH . '/lang/' . strtolower($lang) . '.lang.php';
		if(file_exists($file))
		{
			include $file;
			if (!function_exists('class_alias')) {
			    function class_alias($original, $alias) {
			        eval('abstract class ' . $alias . ' extends ' . $original . ' {}');
			    }
			}
		}
		else 
			throw new Exception("File not found : {$file}");
		// alias the lang class
		class_alias($lang,'Lang');
	/* -------------- */
	}
	
	// set the timezone
	date_default_timezone_set($config->config_values['application']['timezone']);

	/*** set error handler level to E_WARNING ***/
	error_reporting($config->config_values['application']['error_reporting']);
	set_error_handler('_exception_handler', $config->config_values['application']['error_reporting']);
