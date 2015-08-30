<?php

final class Config
{
	/*
	 * @var object $instance
	*/
	private static $instance = null;
		
	/*
	* @var string $config_file
	*/	
	private static $config_file = '/config.php';

	/*
	 * @var array $config_values; 
	 */
	public $config_values = array();

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
		$this->config_values = require_once(__CONFIG_PATH . self::$config_file);
		
	}

	/**
	 *
	 * @__clone
	 *
	 * @access private
	 *
	 */
	private function __clone() {}	

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
}
