<?php

class Page_BaseModel extends Model
{
	var $lang;
		
	function Base_BaseModel()
	{
		parent::__construct();
	}

	function setLang($module = 0, $typeid = 0, $configure_mod)
	{
		if($configure_mod[$module][$typeid]['languages'])
			return $configure_mod['default_lang'];
		return $configure_mod['default_global_lang'];		
	}
	
	function setCharset($charset = "UTF8")
	{
		$this->query("SET NAMES '$charset'");
	}
	

}

?>