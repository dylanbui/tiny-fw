<?php
try
{
	// define the site path __SITE_PATH : c:\xampp\htdocs\adv_mvc
	define ('__SITE_PATH', realpath(dirname(dirname(__FILE__))));
	// __SITE_URL : /adv_mvc/
//  	define ('__SITE_URL', dirname(str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME'])).'/');
 	define ('__SITE_URL', str_replace('//','/', dirname(str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME'])).'/'));
	// __BASE_URL : /adv_mvc/admin/
 	define ('__BASE_URL', str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));
	// Co thu muc public_html 	
 	define ('__PUBLIC_HTML', __SITE_URL.'public_html/');

 	// ---- Khong Thay Doi ---- //
 	define ('__ASSET_URL', __PUBLIC_HTML.'assets/');
 	define ('__TEMPLATE_URL', __PUBLIC_HTML.'admin/flaty_template/');
 	
 	define ('__IMAGE_URL', __PUBLIC_HTML.'admin/images/');
 	define ('__CSS_URL', __PUBLIC_HTML.'admin/stylesheets/');
 	define ('__JS_URL', __PUBLIC_HTML.'admin/javascripts/');

	// Tam thoi bo wa 	
//  	define ('__PUBLIC_JS_URL', __ASSET_URL.'js/');
//  	define ('__PUBLIC_IMG_URL', __ASSET_URL.'images/');
//  	define ('__PUBLIC_CSS_URL', __ASSET_URL.'css/');
 	
	// the application directory path 
	define ('__APP_PATH', __SITE_PATH.'/admin');	
	define ('__VIEW_PATH', __APP_PATH.'/views');	
	define ('__LAYOUT_PATH', __SITE_PATH.'/layouts');	
	define ('__HELPER_PATH', __SITE_PATH.'/application/helpers');
	define ('__CONFIG_PATH', __SITE_PATH.'/application/config');
	
	define ('__UPLOAD_DATA_PATH', __SITE_PATH.'/public_html/data/upload/');	
	define ('__UPLOAD_DATA_URL', __PUBLIC_HTML . 'data/upload/');
	
	define ('__UPLOAD_GALLERY_PATH', __UPLOAD_DATA_PATH . 'gallery/');
	define ('__UPLOAD_GALLERY_URL', __UPLOAD_DATA_URL . 'gallery/');

// 	$const = get_defined_constants(true);
// 	echo "<pre>";
// 	print_r($const['user']);
// 	echo "</pre>";
// 	exit();
		
	/*** include the helper ***/
 	$_autoload_helpers = array('form','admin_func');
 	$lang = NULL;
 	$config = NULL;
	
	require __SITE_PATH . '/admin/startup.php';
	
	// Load URI
	parse_server_uri();	
	
	$config->config_values['application']['default_uri'] = "common/home/login";
	
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

	// Parameter
// 	$parameter = new Parameter();
// 	$registry->oParams = $parameter;
	
	// Input
	$input = new Input();
	$registry->oInput = $input;	
	
	$view = new View('admin');
	$registry->oView = $view;

	// Auth
	$registry->oAuth = new Auth($registry);
	
	// Initialize the FrontController
	$front = FrontController::getInstance();
	$front->setRegistry($registry);
	
	/*
		// Cau hinh cho cac action nay chay dau tien 
	$front->addPreRequest(new Request('run/first/action')); 
	$front->addPreRequest(new Request('run/second/action'));
	*/

	$front->addPreRequest(new Request('common/common/check-login'));
	$front->addPreRequest(new Request('common/common/check-permission'));

	// Run dispatch()
	$front->dispatch();
	
	// Output
	$response->output();	
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
