<?php
try
{
	header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
		
	// define the site path __SITE_PATH : c:\xampp\htdocs\adv_mvc
	define ('__SITE_PATH', realpath(dirname(__FILE__)));
	// __SITE_URL : /adv_mvc/
 	define ('__SITE_URL', str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));
	// __BASE_URL : /adv_mvc/
 	define ('__BASE_URL', __SITE_URL);
 	// Co thu muc public_html 	
 	define ('__PUBLIC_HTML', __SITE_URL.'public_html/');
 	
 	// ---- Khong Thay Doi ---- // 	
 	define ('__ASSET_URL', __PUBLIC_HTML.'assets/');
 	define ('__IMAGE_URL', __ASSET_URL.'images/');
 	define ('__CSS_URL', __ASSET_URL.'css/');
 	define ('__JS_URL', __ASSET_URL.'js/');
 	
	// the application directory path 
	define ('__APP_PATH', __SITE_PATH.'/application');
	define ('__VIEW_PATH', __APP_PATH.'/views');	
	define ('__LAYOUT_PATH', __SITE_PATH.'/layouts');
	define ('__HELPER_PATH', __APP_PATH.'/helpers');
	define ('__CONFIG_PATH', __APP_PATH.'/config');

	define ('__UPLOAD_DATA_PATH', __SITE_PATH.'/public_html/data/upload/');
	define ('__UPLOAD_DATA_URL', __PUBLIC_HTML . 'data/upload/');
	
	define ('__DATA_PATH', __SITE_PATH . '/public_html/data/');
	define ('__DATA_URL', __PUBLIC_HTML . 'data/');

// 	$const = get_defined_constants(true);
// 	echo "<pre>";
// 	print_r($const['user']);
// 	echo "</pre>";
// 	exit();	
	
	/*** include the helper ***/
 	$_autoload_helpers = array();
 	$lang = NULL;
 	$config = NULL;
	
	require __SITE_PATH . '/admin/startup.php';
	
	$oBenchmark = new Benchmark();
	$oBenchmark->mark('code_start');	
	
	// Load facebook api
	require __SITE_PATH . '/lib/facebookapi/facebook.php';	
	
 	/*** a new registry object ***/
 	$registry = new Registry();
 	
 	// Session
 	$oSession = new Session();
 	$registry->oSession = $oSession;
 	
 	$configSystem = new Base_ConfigureSystem();
 	$configure_mod = $configSystem->getConfigureData();
 	$configure_mod['default_global_lang'] = $lang;
 	$registry->oConfigureSystem = $configure_mod; 	
 	
	// Response
	$response = new Response();
	$response->addHeader('Content-Type: text/html; charset=utf-8');
	$registry->oResponse = $response; 

	// Config
	$registry->oConfig = $config; 
	
	// Input
	$input = new Input();
	$registry->oInput = $input;	
	
	// Cache
	$cache = new Cache($config->config_values['cache']);
	$registry->oCache = $cache;	
	
	// Parameter
	$view = new View();
	$registry->oView = $view;

	// Initialize the FrontController
	$front = FrontController::getInstance();
	$front->setRegistry($registry);
	
	/*
		// Cau hinh cho cac action nay chay dau tien 
	$front->addPreRequest(new Request('run/first/action')); 
	$front->addPreRequest(new Request('run/second/action'));
	*/
	if ($config->config_values['application']['enable_seo_url'])
		$front->addPreRequest(new Request('common/seo-url/index'));
	
	$front->dispatch();
	
	// Output
	$response->output();

	if($config->config_values['application']['show_benchmark'])
	{
		$oBenchmark->mark('code_end');
		echo "<br>".$oBenchmark->elapsed_time('code_start', 'code_end');
	}	
}
catch(MvcException $e)
{
	if($config->config_values['application']['display_errors'])
		show_error($e->getMessage());
	else 				
		//show a 404 page here
		show_404();
}
catch(Exception $e)
{
	die('FATAL : '.$e->getMessage().' : '.$e->getLine());
}
