<?php

/**
 *
 * @Front Controller class
 *
 * @package Core
 *
 */

class FrontController
{

	protected $_module, $_controller, $_action, $_registry, $_curr_request;
	protected $pre_request = array();	

	public static $_instance;

	public static function getInstance()
	{
		if( ! (self::$_instance instanceof self) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function __construct()
	{
		
	}
	
	public function addPreRequest($pre_request) 
	{
		$this->pre_request[] = $pre_request;
	}
	
// 	public function dispatch()
// 	{
// // 		$request = new Request($_SERVER['QUERY_STRING']);
// 		$request = new Request($this->getUri());		
		
// 		$this->_registry->oRequest = $request;
		
// 		$this->_module = $request->getModule(); 
// 		$this->_controller = $request->getController(); 
// 		$this->_action = $request->getAction(); 
		
// 		foreach ($this->pre_request as $pre_request) 
// 		{
// 			$result = Module::run($pre_request);
					
// 			if ($result) 
// 			{
// 				$request = $result;
// 				break;
// 			}
// 		}
			
// 		while ($request) {
// 			$request = Module::run($request);
// 		}
// 	}

	private function loadPreRouter()
	{
		// Loop through the route array looking for wild-cards
		$routes = $this->_registry->oConfig->config_values['routes'];//array();
		$uri = trim($_SERVER['URL_ROUTER'],'/');
		foreach ($routes as $key => $val)
		{
			// Convert wild-cards to RegEx
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));
		
			// Does the RegEx match?
			if (preg_match('#^'.$key.'$#', $uri))
			{
				preg_match('#^'.$key.'$#', $uri, $data);
				
				// Do we have a back-reference?
				if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}
				
				$_SERVER['URL_ROUTER'] = $val;
			}
		}
	}

	public function dispatch()
	{
		// Load pre config router
		$this->loadPreRouter();
		
		$request = NULL;
		foreach ($this->pre_request as $pre_request)
		{
			$result = Module::run($pre_request);
				
			if ($result)
			{
				$request = $result;
				break;
			}
		}
		
		if (is_null($request)) 
		{
// 			$request = new Request($_SERVER['URL_ROUTER']);
			$request = $this->getCurrentRequest();
			
			$this->_registry->oRequest = $request;
				
			$this->_module = $request->getModule();
			$this->_controller = $request->getController();
			$this->_action = $request->getAction();
		}
		
		while ($request) {
			$request = Module::run($request);
		}
		
	}
	
	public function getCurrentRequest()
	{
		if (empty($this->_curr_request)) {
			$this->_curr_request = new Request($_SERVER['URL_ROUTER']);
		}
		return $this->_curr_request;
	}
	
	public function getModule()
	{
		return $this->_module;
	}

	public function getController()
	{
		return $this->_controller;
	}

	public function getAction()
	{
		return $this->_action;
	}

	public function getRegistry()
	{
		return $this->_registry;
	}

	public function setRegistry($registry)
	{
		$this->_registry = $registry;
	}
	
} // end of class
