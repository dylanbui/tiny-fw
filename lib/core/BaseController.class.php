<?php
/**
 *
 * @Controller Interface
 *
 */

interface IController {}

abstract class BaseController implements IController
{
	protected $_front , $_module , $_controller , $_action , $_view, $_registry , $_layout_path = NULL , $_children = array();
	
	protected $_isModify;

	public function __construct()
	{
		$this->_front = FrontController::getInstance();
		$this->_module = $this->_front->getModule();
		$this->_controller = $this->_front->getController();
		$this->_action = $this->_front->getAction();
		$this->_registry = $this->_front->getRegistry();
		$this->_isModify = FALSE;

		// --- Set oView Params ---//
		$this->oView->oConfig = $this->oConfig;
		
	}
	
	public function __get($key) 
	{
		return $this->_registry->{$key};
	}
	
	public function __set($key, $value) 
	{
		$this->_registry->{$key} = $value;
	}

	protected function forward($route, $args = array()) 
	{
		return new Request($route, $args);
	}
	
	protected function detectModifyPermission($route)
	{
		if ($this->oAuth->hasPermission('modify',$route))
			$this->_isModify = TRUE;
		
		return $this->_isModify;
	}
	
	protected function renderView($path, $layout_path = NULL)
	{
		if (!is_null($layout_path))
			$this->_layout_path = $layout_path;
		
		foreach ($this->_children as $child) {
			$param_name = str_replace("-", "_", $child->getAction());
			$this->oView->{$param_name} = Module::run($child);
		}		

		$this->oView->main_content = $this->oView->fetch($path);
		$result = $this->oView->renderLayout($this->_layout_path);
		$this->oResponse->setOutput($result, $this->oConfig->config_values['application']['config_compression']);
	}

}
