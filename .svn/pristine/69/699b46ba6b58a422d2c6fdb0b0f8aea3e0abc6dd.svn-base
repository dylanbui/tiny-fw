<?php

class ExModule_ContentController extends BaseController
{

	
	public function __construct()
	{
		parent::__construct();
		
	}

	public function indexAction() 
	{
// 	    $this->renderView('site/home/index');
	}

	public function listAction()
	{
		$this->renderView('ex-module/content/list');
	}
	
	public function denyAction($title_deny)
	{
		$this->oView->title_deny = $title_deny;
		$this->display('site/home/deny');
	}	

	private function display($path)
	{
		foreach ($this->children as $child) {
			$param_name = str_replace("-", "_", $child->getAction());
			$this->oView->{$param_name} = Module::run($child);
		}		
		
		$this->oView->main_content = $this->oView->fetch($path);
		$result = $this->oView->renderLayout($this->_layout_path);
		$this->oResponse->setOutput($result, $this->oConfig->config_values['application']['config_compression']);		
	}
	

}
