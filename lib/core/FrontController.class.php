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
		// Load URI
		$this->parseServerUri();
	}
	
	public function addPreRequest($pre_request) 
	{
		$this->pre_request[] = $pre_request;
	}

	private function loadPreRouter()
	{
		// Loop through the route array looking for wild-cards
		$routes = $this->_registry->oConfig->config_values['routes'];//array();
		$uri = trim($_SERVER['URL_ROUTER'],'/');
		foreach ($routes as $key => $val)
		{
			// Convert wildcards to RegEx
			$key = str_replace(array(':any', ':num'), array('.+', '[0-9]+'), $key);			
			// Does the RegEx match?
			if (preg_match('#^'.$key.'$#', $uri, $matches))
			{
				// Do we have a back-reference?
				if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}
				$_SERVER['URL_ROUTER'] = $val;
                // -- Lay cai match dau tien --
                return;
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
            $result = $pre_request->run();
				
			if ($result)
			{
				$request = $result;
				break;
			}
		}
		
		if (is_null($request)) 
		{
			$request = $this->getCurrentRequest();
			
			$this->_registry->oRequest = $request;
				
			$this->_module = $request->getModule();
			$this->_controller = $request->getController();
			$this->_action = $request->getAction();
		}
		
		while ($request) {
            $request = $request->run();
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

    private function parseServerUri($prefix_slash = true)
    {
        if (isset($_SERVER['PATH_INFO']))
        {
            $uri = $_SERVER['PATH_INFO'];
        }
        elseif (isset($_SERVER['REQUEST_URI']))
        {
            $uri = $_SERVER['REQUEST_URI'];
            if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
            {
                $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
            }
            elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
            {
                $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            }

            // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
            // URI is found, and also fixes the QUERY_STRING server var and $_GET array.
            if (strncmp($uri, '?/', 2) === 0)
            {
                $uri = substr($uri, 2);
            }
            $parts = preg_split('#\?#i', $uri, 2);
            $uri = $parts[0];
            if (isset($parts[1]))
            {
                $_SERVER['QUERY_STRING'] = $parts[1];
                parse_str($_SERVER['QUERY_STRING'], $_GET);
            }
            else
            {
                $_SERVER['QUERY_STRING'] = '';
                $_GET = array();
            }
            $uri = parse_url($uri, PHP_URL_PATH);
        }
        else
        {
            // Couldn't determine the URI, so just return false
            return false;
        }
        $_SERVER['URL_ROUTER'] = ($prefix_slash ? '/' : '').str_replace(array('//', '../'), '/', trim($uri, '/'));
        // Do some final cleaning of the URI and return it
        return $_SERVER['URL_ROUTER'];
    }
	
} // end of class
