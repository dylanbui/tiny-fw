<?php

class Base_User extends Model 
{
	protected $_table_name = TB_USER;
	protected $_primary_key = 'id';
	
	public function __construct()
	{
		parent::__construct();
	}

//     public function checkLogin($username, $password)
//     {
//     	$row = $this->getRow('username = ? AND password = ?',array($username,$password));
//     	return $row;
//     }

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

    public function getAllUser()
    {
    	$sql = "SELECT * FROM ".TB_USER;
    	$result = $this->query($sql);
    	return $result->fetchAll();
    }
	
}

?>