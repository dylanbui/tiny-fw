<?php

Class Common_SeoUrlController Extends BaseController 
{
	
	public function __construct()
	{
		parent::__construct();
		
// 		$configure_mod['default_global_lang'] = $lang;
// 		$registry->oConfigureSystem = $configure_mod;

// 		echo "<pre>";
// 		print_r($this->oConfigureSystem['default_global_lang']);
// 		echo "</pre>";
// 		exit();
		
	}
	
	public function firstAction()
	{
		echo "<pre>";
		print_r("firstAction");
		echo "</pre>";
		exit();
	}
	

	public function indexAction() 
	{
		// Add rewrite to url class
		// Check neu ko co config thi ko can lam chuyen nay
		$this->oSeoUrl = $this;
		$this->oView->oSeoUrl = $this;
		
		$_route = $_SERVER['URL_ROUTER'];
		
		if (!empty($_route)) 
		{
			$parts = explode('/', $_route);
				
			$objUrlAlias = new Base_UrlAlias();
			foreach ($parts as $part) 
			{
				$row = $objUrlAlias->getRow("keyword = ?", array($part));
				if (!empty($row)) 
				{
					$url = $this->parseGetParams($row['query']);
// 					$url = explode('=', $row['query']);
					
					if ($url[0] == 'ex_content_id')
						$this->oInput->_get['ex_content_id'] = $url[1];
						
					if ($url[0] == 'ex_category_id') 
					{
						$this->oInput->_get['ex_category_id'] = $url[1];
						
						if (!isset($this->oInput->_get['ex_path'])) 
						{
							$this->oInput->_get['ex_path'] = $url[1];
						} else 
						{
							$this->oInput->_get['ex_path'] .= '_' . $url[1];
						}
					}
					
					// --- Page Module ---//
					if ($url[0] == 'page_content_id')
					{						
						$this->oInput->_get['page_content_id'] = $url[1];
						if (isset($url[2]))
							$this->oInput->_get['page_ln'] = $url[3];
					}
					
					if ($url[0] == 'page_cat_id')
					{
						$this->oInput->_get['page_cat_id'] = $url[1];
					
						if (!isset($this->oInput->_get['page_path']))
							$this->oInput->_get['page_path'] = $url[1];
						else
							$this->oInput->_get['page_path'] .= '_' . $url[1];
						
						if (isset($url[2]))
							$this->oInput->_get['page_ln'] = $url[3];						
					}
					// --- ---------- ---//
					
				} 
			}
			
			$forward_url = NULL; 
			if (isset($this->oInput->_get['ex_content_id'])) {
				$forward_url = 'ex-module/content/list';
			} elseif (isset($this->oInput->_get['path'])) {
				$forward_url =  'ex-module/category/list';
			}
			
			// --- Page Module ---//			
			if (isset($this->oInput->_get['page_content_id'])) {
				$forward_url = 'page-module/content/list';
			} elseif (isset($this->oInput->_get['page_path'])) {
				$forward_url =  'page-module/category/list';
			}			
			// --- ---------- ---//				
			
			if (!is_null($forward_url)) {
				return $this->forward($forward_url);
			}
			
		}

	}
	
	// --- Dung chinh thang nay de hien thi url ---//
	public function rewrite($link, $get_data = NULL) 
	{
		$objUrlAlias = new Base_UrlAlias();
		$url = "";
		foreach ($get_data as $key => $value)
		{
			if ($link == 'ex-module/content/list' && $key == 'ex_content_id')
			{
				$row = $objUrlAlias->getRow("query = ?", array($key . '=' . (int)$value));
				if ($row)
				{
					$url .= '/' . $row['keyword'];
					unset($get_data[$key]);
				}
		
			} elseif ($key == 'ex_path')
			{
				$categories = explode('_', $value);
				foreach ($categories as $category)
				{
					$row = $objUrlAlias->getRow("query = ?", array('ex_category_id=' . (int)$category));
					if ($row)
					{
						$url .= '/' . $row['keyword'];
					}
				}
				unset($get_data[$key]);
			}
			
			// --- PAGE MODULE ---//
			if ($link == 'page-module/content/list' && $key == 'page_content_id')
			{
				$query = $key . '=' . (int)$value.'&page_ln='.$this->oConfigureSystem['default_global_lang'];
				$row = $objUrlAlias->getRow("query = ?", array($query));
				if ($row)
				{
					$url .= '/' . $row['keyword'];
					unset($get_data[$key]);
				}
			
			} elseif ($key == 'page_path')
			{
				$categories = explode('_', $value);
				foreach ($categories as $category)
				{
					$query = 'page_cat_id=' . (int)$category.'&page_ln='.$this->oConfigureSystem['default_global_lang'];
					$row = $objUrlAlias->getRow("query = ?", array($query));
					if ($row)
					{
						$url .= '/' . $row['keyword'];
					}
				}
				unset($get_data[$key]);
			}
			// --- ------------ ---//
				

		}
		
		if ($url)
			$link = trim($url,'/');

		$query = '';
		if ($get_data)
		{
			foreach ($get_data as $key => $value)
				$query .= '&' . $key . '=' . $value;
		}
		
		if ($query)
			$link .= '?' . trim($query, '&');
		
		return site_url($link);		
		
		
// 		if (strpos($link, "ex-module/content/list/") == 0
// 				|| strpos($link, "ex-module/category/list/") == 0
// 				) 
// 		{
// 			$parts = explode('/', $link);
// 			if (isset($parts[4])) 
// 			{
// 				$cat_id = $parts[4];

				
// 				$objCatsPath = new Ex_ContentCatsPath();
// 				$rs = $objCatsPath->getRowset("cat_id", array($cat_id), 'level ASC');
				
// 				$link = "";
// 				$objUrlAlias = new Base_UrlAlias();
// 				foreach ($rs as $row)
// 				{
// 					$data = $objUrlAlias->getRow("query = ?", array("ex_category_id=".$row['cat_id']));
// 					if ($data)
// 						$link .= '/' . $data['keyword'];
// 				}
// 			}
			
// 			if (isset($parts[5]))
// 			{
// 				$content_id = $parts[5];
// 				$data = $objUrlAlias->getRow("query = ?", array("ex_content_id=".$content_id));
// 				if ($data)
// 					$link .= '/' . $data['keyword'];				
// 			}
// 		}
		
// 		if ($get_data) {
// 			foreach ($get_data as $key => $value) {
// 				$query .= '&' . $key . '=' . $value;
// 			}
		
// 			if ($query) {
// 				$link = '?' . trim($query, '&');
// 			}
// 		}
		
// 		return site_url($link);
	}
		
		
		
// 		$url_info = parse_url(str_replace('&amp;', '&', $link));
	
// 		$url = ''; 
		
// 		$data = array();
		
// 		parse_str($url_info['query'], $data);
		
// 		foreach ($data as $key => $value) 
// 		{
// 			if (isset($data['route'])) 
// 			{
// 				if (($data['route'] == 'product/product' && $key == 'product_id') 
// 						|| (($data['route'] == 'product/manufacturer/info' 
// 								|| $data['route'] == 'product/product') && $key == 'manufacturer_id') 
// 						|| ($data['route'] == 'information/information' && $key == 'information_id')) 
// 				{
// 					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");
				
// 					if ($query->num_rows) 
// 					{
// 						$url .= '/' . $query->row['keyword'];
						
// 						unset($data[$key]);
// 					}
										
// 				} elseif ($key == 'path') 
// 				{
// 					$categories = explode('_', $value);
					
// 					foreach ($categories as $category) 
// 					{
// 						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int)$category . "'");
				
// 						if ($query->num_rows) 
// 						{
// 							$url .= '/' . $query->row['keyword'];
// 						}							
// 					}
					
// 					unset($data[$key]);
// 				}
// 			}
// 		}
	
// 		if ($url) {
// 			unset($data['route']);
		
// 			$query = '';
		
// 			if ($data) {
// 				foreach ($data as $key => $value) {
// 					$query .= '&' . $key . '=' . $value;
// 				}
				
// 				if ($query) {
// 					$query = '?' . trim($query, '&');
// 				}
// 			}

// 			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
// 		} else {
// 			return $link;
// 		}
// 	}	

	private function parseGetParams($get_string)
	{
// 		$d = $this->parseGetParams('aaa=123');
// 		$d = $this->parseGetParams('aaa=123&bbb=tre');
// 		$d = $this->parseGetParams('aaa=123&bbb=tre&ccc=321');
		
		$arrParam = explode('&', $get_string);

		$data = array();
		$i = 0;
		foreach ($arrParam as $row)
		{
			$params = explode('=', $row);
			$data[$i] = $params[0];
			$i += 1; 
			$data[$i] = $params[1];
			$i++;
		}
		return $data;		
	}
	
	
	private function getUri($prefix_slash = true)
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
	
		// Do some final cleaning of the URI and return it
		return ($prefix_slash ? '/' : '').str_replace(array('//', '../'), '/', trim($uri, '/'));
	}
	
	
}

?>
