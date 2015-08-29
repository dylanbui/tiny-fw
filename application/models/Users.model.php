<?php

class Users extends Model 
{
	protected $_table_name = TB_USER;
	protected $_primary_key = 'id';
	
	public function __construct()
	{
		parent::__construct();
	}

	
}

?>