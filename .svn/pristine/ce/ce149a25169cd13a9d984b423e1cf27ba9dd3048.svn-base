<?php

final class Config
{
	/*
	* @var string $config_file
	*/
// 	private static $config_file = '/config/config.ini';	
	private static $config_file = '/config.php';

	/*
	 * @var array $config_values; 
	 */
	public $config_values = array();

	/*
	* @var object $instance
	*/
	private static $instance = null;

	/**
	 *
	 * Return Config instance or create intitial instance
	 *
	 * @access public
	 *
	 * @return object
	 *
	 */
	public static function getInstance()
	{
 		if(is_null(self::$instance))
 		{
 			self::$instance = new config;
 		}
		return self::$instance;
	}


	/**
	 *
	 * @the constructor is set to private so
	 * @so nobody can create a new instance using new
	 *
	 */
	private function __construct()
	{
// 		$this->config_values = parse_ini_file(__APP_PATH . self::$config_file, true);
		$this->config_values = require_once(__CONFIG_PATH . self::$config_file);
		
	}

	/**
	 * @get a config option by key
	 *
	 * @access public
	 *
	 * @param string $key:The configuration setting key
	 *
	 * @return string
	 *
	 */
	public function getValue($key)
	{
		return self::$config_values[$key];
	}


	/**
	 *
	 * @__clone
	 *
	 * @access private
	 *
	 */
	private function __clone()
	{
	}
}
