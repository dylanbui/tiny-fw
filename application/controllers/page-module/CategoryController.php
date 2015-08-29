<?php

class PageModule_CategoryController extends BaseController
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
		echo "<pre>";
		print_r($this->oInput->_get);
		echo "</pre>";

		exit();
		
		
		$this->renderView('page-module/category/list');
	}
	

}
