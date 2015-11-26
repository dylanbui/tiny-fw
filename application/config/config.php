<?php  if ( ! defined('__SITE_PATH')) exit('No direct script access allowed');
// --- -------------------------------------------------------------------------------------------------- ---//
// --- APPLICATION ---//

$config['application']['default_uri'] 				= "site/home/index";
$config['application']['admin_default_uri'] 		= "home/dashboard/show";
$config['application']['admin_header_title'] 		= "FLATY Admin";
$config['application']['admin_footer_title'] 		= "2015 © FLATY Admin Template.";
$config['application']['display_error_404'] 		= FALSE;
// $config['application']['error_reporting'] 			= 0; // Neu = 0 : Khong hien thi bat cu thong bao nao
// $config['application']['error_reporting'] 			= E_ALL; // Neu = 0 : Khong hien thi bat cu thong bao nao
$config['application']['error_reporting'] 			= E_ALL ^ E_DEPRECATED; // Hien thi thong bao tat ca cac loi tru cac ham DEPRECATED
$config['application']['language'] 					= "en";
$config['application']['timezone'] 					= "Asia/Ho_Chi_Minh";
$config['application']['currency'] 					= "USD";
$config['application']['config_compression'] 		= 0; //; config_compression = 0 -> 9
$config['application']['show_benchmark']			= TRUE;
$config['application']['enable_seo_url'] 			= TRUE;

// --- -------------------------------------------------------------------------------------------------- ---//
// --- MASTER DATABASE ---//

$config['database_master']['db_driver'] 			= "pdo"; // mysqli
$config['database_master']['db_hostname'] 			= "localhost";
$config['database_master']['db_name'] 				= "z-cms";
$config['database_master']['db_username'] 			= "root";
$config['database_master']['db_password'] 			= "";
$config['database_master']['db_port'] 				= 3306;
$config['database_master']['db_prefix'] 			= "z__";

// --- -------------------------------------------------------------------------------------------------- ---//
// --- SLAVE DATABASE ---//

$config['database_slave']['db_driver'] 				= "pdo"; // mysqli
$config['database_slave']['db_hostname'] 			= "localhost";
$config['database_slave']['db_name'] 				= "none-db";
$config['database_slave']['db_username'] 			= "root";
$config['database_slave']['db_password'] 			= "";
$config['database_slave']['db_port'] 				= 3306;
$config['database_slave']['db_prefix'] 				= "z__";

// --- -------------------------------------------------------------------------------------------------- ---//
// --- SESSION ---//

$config['session']['match_ip'] 						= FALSE;
$config['session']['match_fingerprint'] 			= TRUE;
$config['session']['match_token'] 					= FALSE;
$config['session']['session_name'] 					= "simple_mvc_session";
$config['session']['cookie_path'] 					= "/";
$config['session']['cookie_domain'] 				= NULL;
$config['session']['cookie_secure'] 				= NULL;
$config['session']['cookie_httponly'] 				= NULL;
$config['session']['regenerate'] 					= 300;
$config['session']['expiration'] 					= 7200;
$config['session']['gc_probability'] 				= 100;
$config['session']['session_database'] 				= TRUE; //FALSE;
$config['session']['table_name'] 					= $config['database_master']['db_prefix']."sessions";
$config['session']['primary_key'] 					= "session_id";

// --- -------------------------------------------------------------------------------------------------- ---//
// --- MAIL ---//

$config['mail']['mailer_type'] 						= "system";
$config['mail']['smtp_enable'] 						= TRUE;
$config['mail']['smtp_auth'] 						= TRUE;
$config['mail']['smtp_server'] 						= "mail.example.com";
$config['mail']['smtp_port'] 						= 25;
$config['mail']['smtp_timeout'] 					= 30;
$config['mail']['smtp_usr'] 						= "username";
$config['mail']['smtp_psw'] 						= "password";
$config['mail']['smtp_from_email'] 					= "admin@example.com";
$config['mail']['smtp_from_name'] 					= "Duc Bui";
$config['mail']['smtp_reply_email'] 				= "admin@example.com";
$config['mail']['smtp_reply_name'] 					= "Duc Bui";

// --- -------------------------------------------------------------------------------------------------- ---//
// --- LOGGER ---//

$config['logging']['log_level'] 					= 200;
$config['logging']['log_handler'] 					= "file";
$config['logging']['log_file'] 						= "/tmp/ngukho.log";

/*
|--------------------------------------------------------------------------
| CACHE Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| [__SITE_PATH]/cache/ folder.  Use a full server path with trailing slash.
|
*/

$config['cache']['cache_path'] 						= '/public_html/cache/';
$config['cache']['life_time'] 						= 60;

/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
|
|
*/

// $config['routes'][''] 								= "";
// $config['routes']['(:any)'] = "router/$1";
// $config['routes']['products/(:any)'] = "category/$1";
// $config['routes']['products/([a-z]+)/(\d+).html'] = "$1/abc_$2";
// $config['routes']['links/([a-zA-Z0-9_-]+)'] = "site/index/links/$1";

// khi su dung phai dung : func_get_args() , de lay cac bien trong chuoi
$config['routes']['links/(:any)'] = "site/index/links/$1";
// $config['routes']['links/(.*?)'] = "site/index/links/$1";
 
// $config['routes']['journals'] 						= "blogs";
// $config['routes']['blog/joe'] 						= "blogs/users/34";
// $config['routes']['product/(:any)/(:any)'] 			= "catalog/product_lookup/$1/$2";
// $config['routes']['product/(:num)'] 				= "catalog/product_lookup_by_id/$1";


return $config;