<?php

class Base_User extends Model 
{
	protected $_table_name = TB_USER;
	protected $_primary_key = 'id';
	
	public function __construct()
	{
		parent::__construct();
	}

	public function isAdmin($str_group_id)
	{
		$objGroup = new Base_Group();
		$rsGroups = $objGroup->getRowset("id IN ({$str_group_id})");
		
		foreach ($rsGroups as $rowGroup)
		{
			if ($rowGroup['is_admin'] == 1) 
			{
				return TRUE;
			}
		}
		return FALSE;		
	}

}

?>