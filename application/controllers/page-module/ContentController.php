<?php

class PageModule_ContentController extends BaseController
{

	
	public function __construct()
	{
		parent::__construct();
		$this->oView->title = "Page Module";
	}

	public function indexAction() 
	{

	}
	
	public function loadDataTestAction()
	{
		// --- Load data test from page module ---//
		$this->renderView('page-module/content/load-data-test');			  
	}

	public function listAction()
	{
		// Chay thu test data
		
// 		$obj = new Page_Category();
// 		$data = $obj->getCategoryPath(1, $this->oInput->varGet('page_ln'));
		
// 		echo "<pre>";
// 		print_r($data);
// 		echo "</pre>";
		
// 		$data = $obj->getCategoryPath(6, $this->oInput->varGet('page_ln'));
		
// 		echo "<pre>";
// 		print_r($data);
// 		echo "</pre>";
		
// 		echo "<pre>";
// 		print_r($this->oInput->varGet('page_content_id'));
// 		echo "</pre>";
		
// 		echo "<pre>";
// 		print_r($this->oInput->varGet('page_path'));
// 		echo "</pre>";
		
// 		echo "<pre>";
// 		print_r($this->oInput->get);
// 		echo "</pre>";		
				
// 		exit();
		
// 		Array
// 		(
// 				[offset] => 2
// 				[page] => 3
// 				[page_cat_id] => 6
// 				[page_path] => 1_6
// 				[page_ln] => en
// 				[page_content_id] => 7
// 		)		
		
		$this->renderView('page-module/content/list');
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
