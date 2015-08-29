<?php

class Products extends Model
{
	protected $_table_name = TB_PRODUCTS;
	protected $_primary_key = 'id';	
	
	function __construct()
	{
		parent::__construct();
	}
		
}

?>