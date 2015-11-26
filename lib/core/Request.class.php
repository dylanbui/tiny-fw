<?php

final class Request 
{
	protected $file;
	protected $class;
	protected $method;
	protected $dir_template;
	protected $args = array();
	
	protected $module;
	protected $controller;
	protected $action;

	public function __construct($route = NULL, $args = array()) 
	{
		$this->parseUri($route);
		
		$moduleDir = __APP_PATH . '/controllers/' . $this->module;
		$controllerFile = __APP_PATH.'/controllers/'.$this->module.'/'.upperCamelcase($this->controller).'Controller.php';
		$controllerClass =  upperCamelcase($this->module).'_'.upperCamelcase($this->controller).'Controller';;
		
		$this->method = lowerCamelcase($this->action).'Action';
		$this->args = array_merge($this->args,$args); 
		
		if(!is_dir($moduleDir))
		{
			throw new MvcException("Module not found : {$moduleDir}");
		}
		
		if(is_file($controllerFile))
		{
			$this->file = $controllerFile;
			$this->class = $controllerClass;
			$this->dir_template = __VIEW_PATH . "/" . $this->module . '/' . $this->controller;
		}
		else 
		{
			throw new MvcException("Controller not found : {$controllerFile}");
		}
		
	}
	
	private function parseUri($route)
	{
		$config = Config::getInstance();
		
		// removes the trailing slash
//		$route = preg_replace("/\/$/", '', $route);
// 		/this/that/theother/ => this/that/theother
		$route = trim($route, '/');
		
		// get the default uri
		if(empty($route))
			$route = $config->config_values['application']['default_uri'];
			
		$path = '';
		$parts = explode('/', str_replace('../', '', $route));
		
		$i = 0;		
		foreach ($parts as $part) 
		{
			$path .= $part;
			if($i == 0)
			{
				$this->module = $path;
				$path .= '/';
				array_shift($parts);
				$i++;
				continue;
			}
			$this->controller = $part;
			array_shift($parts);
			break;
		}

		// Neu controller la rong . Route co dang [module]/
		if(empty($this->controller))
		{
			$this->controller = 'index';
		}

		$method = array_shift($parts);
				
		if ($method) {
			$this->action = $this->method = $method;
		} else {
			$this->action = $this->method = 'index';
		}
		
// 		$this->args = $parts;
		$this->args = $this->clearArgs($parts);
	}
	
	public function getFile() {
		return $this->file;
	}
	
	public function getClass() {
		return $this->class;
	}
	
	public function getMethod() {
		return $this->method;
	}
	
	public function getDirTemplate() {
		return $this->dir_template;
	}
	
	public function getFileTemplate() {
		return $this->dir_template  . '/' . $this->method . '.phtml';
	}	
	
	public function getArgs() {
		return $this->args;
	}
	
	public function getRouter()	{
		return "{$this->module}/{$this->controller}/$this->action";		
	}
	
	public function getModule() {
		return $this->module;
	}

	public function getController() {
		return $this->controller;
	}

	public function getAction() {
		return $this->action;
	}
	
	private function clearArgs($args)
	{
		$clear_args = array();
		foreach ($args as $arg)
		{
// 			$str = @trim($str);
// 			$str = htmlspecialchars($arg);			
// 			if(get_magic_quotes_gpc()) {
// 				$str = stripslashes($str);
// 			}
// 			$str =  mysql_real_escape_string($str);
// 			$clear_args[] = $str;
			
			$clear_args[] = htmlspecialchars($arg);
		}
		return $clear_args;
	}

    // -- Fixed DucBui : 24/11/2015  --
    public static function staticRun($request)
    {
        if(!$request instanceof Request)
            $request = new Request($request);

        $returnVal = $request->run();
        while ($returnVal instanceof Request)
            $returnVal = $returnVal->run();

        return $returnVal;
    }

    public function run()
    {
        $file   = $this->getFile();
        $class  = $this->getClass();
        $method = $this->getMethod();
        $args   = $this->getArgs();

        if (file_exists($file))
        {
            require_once($file);

            $rc = new ReflectionClass($class);
            // if the controller exists and implements IController
//			if($rc->implementsInterface('IController'))
            if($rc->isSubclassOf('BaseController'))
            {
                try {
                    $controller = $rc->newInstance();
                    $classMethod = $rc->getMethod($method);
                    return $classMethod->invokeArgs($controller,$args);
                }
                catch (ReflectionException $e)
                {
                    throw new MvcException($e->getMessage());
                }
            }
            else
            {
//				throw new MvcException("Interface iController must be implemented");
                throw new MvcException("abstract class BaseController must be extended");
            }
        }
        else
        {
            throw new MvcException("Controller file not found");
        }
    }


}
?>