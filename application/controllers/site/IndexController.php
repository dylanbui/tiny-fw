<?php

class Site_IndexController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->oView->title = 'Welcome to Bui Van Tien Duc MVC';		
	}

	public function indexAction() 
	{
//		$_SESSION['test'] = 12;
		$this->oSession->userdata['test'] = 12;
	    $this->oView->title = 'Welcome to Bui Van Tien Duc MVC';
	    $this->renderView('site/home/index');
	}
	
	public function captchaAction()
	{
		// Load helper
		helperLoader("captcha");
		
		$vals = array(
    		'img_path'	 => __DATA_PATH,
    		'img_url'	 => __DATA_URL,
			'font_path'	 =>	__DATA_PATH.'font/monofont.ttf',				
			'length'	 => 6,
    		'img_width'	 => 150,
    		'img_height' => 40,
    		'expiration' => 3600
    	);

		$this->oView->cap = create_captcha($vals);
		$this->renderView('site/index/captcha');
	}
	
	public function linksAction()
	{
		$this->oView->func_get_args = func_get_args();
		$this->renderView('site/index/links');
	}
	
	public function runCommanderAction()
	{
		$arr = exec("dir");
		
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
		exit();
		
		
	}
	
	
	public function testAction() 
	{
		$arr[] = "thong---tin|| cong; ty";
		$arr[] = "!-thong---tin|| cong; ty-=+";
		$arr[] = "thong-+-tin|| cong; ty";
		$arr[] = "thong---tin|| -------- cong; ty &*";
		$arr[] = "@@thong---tin|| +=- cong; ty+=-";
		$arr[] = "thong---bùi văn tiến đức-------------cong; ty";
		
		foreach ($arr as $r)
		{
			echo "<pre>";
			print_r(str2url($r));
			echo "</pre>";
		}
		
		exit();
		
//		echo "<pre>";
//		print_r(print rand(1, 1000000));
//		echo "</pre>";
//		exit();
		
//echo "<pre>";
//print_r((rand() * rand()) / (getrandmax() * getrandmax()));
//echo "</pre>";
//exit();
//
//echo "<pre>";
//print_r(abs((rand()%150)-50) );
//echo "</pre>";
//exit();
//
//		$x = rand(0,1) ? rand(1,100) : rand(1,50);

		$i = 90;
		
		$r = rand(1, 100);
		$p = rand(1, $i);
		
		echo ($p - $r > 0) ? "OK" : "ERROR";
		echo "<br>";		   		
		print("Winner! -- Hit refresh on your browser to play again");
      	exit;
	}
	
	public function changeAction()
	{
	    $this->renderView('site/index/change');
 

	}
	
	
}
