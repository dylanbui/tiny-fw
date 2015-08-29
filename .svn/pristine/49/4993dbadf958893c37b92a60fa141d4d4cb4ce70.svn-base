<?php

class Base_ConfigureSystem extends Base_BaseModel
{
	protected $_table_name = TB_CONFIGURE_SYSTEM;
	protected $_primary_key = 'code';
	
	function __construct()
	{
		parent::__construct();
	}
	
	function getGroups($group_id = null)
	{
		$str = empty($group_id) ? 1 : ' id = ' . $group_id;
		$result = $this->query("SELECT * FROM ".TB_CONFIGURE_SYSTEM_GROUP." WHERE $str AND active = 1 ORDER BY sort_order DESC");
	
		if(!empty($group_id))
			return $result->fetch();
		
		$data = array();
		while(false != ($row = $result->fetch()))
		{
			$data[] = $row;
		}
		return $data;
	}
	
	function getAllGroups($group_id = null)
	{
		$str = empty($group_id) ? 1 : ' id = ' . $group_id;
		$result = $this->query("SELECT * FROM ".TB_CONFIGURE_SYSTEM_GROUP." WHERE $str AND active = 1 ORDER BY sort_order DESC");
	
		$data = array();
		while(false != ($row = $result->fetch()))
		{
			$con_data = $this->getGroupConfigure($row['id']);
			$row['config_data'] = $con_data;
			$data[] = $row;
		}
		return $data;
	}
	
	function getGroupConfigure($group_id = null)
	{
		$str = empty($group_id) ? 1 : ' group_id = ' . $group_id;
// 		$result = $this->query("SELECT code, name, value, note, group_id, set_function, is_system FROM ".TB_CONFIGURE_SYSTEM." WHERE $str ORDER BY is_system DESC");
		$result = $this->query("SELECT * FROM ".TB_CONFIGURE_SYSTEM." WHERE $str ORDER BY is_system DESC");		
	
		$data = array();
		while(false != ($row = $result->fetch()))
		{
			$data[] = $row;
		}
		return $data;
	}
	
	function getGroupConfigureData($group_id = null)
	{
		$str = empty($group_id) ? 1 : ' group_id = ' . $group_id;
		$result = $this->query("SELECT * FROM ".TB_CONFIGURE_SYSTEM." WHERE $str ORDER BY is_system DESC");
	
		$data = array();
		while(false != ($row = $result->fetch(PDO::FETCH_ASSOC)))
		{
			$data[] = $row;
		}
		
		$result = $this->query("SELECT * FROM ".TB_LANGUAGE." WHERE active = 1 ORDER BY sort_order DESC");
		$result = $result->fetchAll(PDO::FETCH_ASSOC);
		
		$config_langs = array();
		
		$languages = array();
		foreach ($result as $row)
		{
			if($row['is_default'] == 1)
				$config_langs['default_lang'] = $row['code'];
				
			$languages[$row['code']] = $row;
		}
		
		$config_langs['languages'] = $languages;
		
		// Detect single language
		$config_langs['single_lang'] = 1;
		if (count($result) > 1)
			$config_langs['single_lang'] = 0;
		
		$data['configure_languages'] = $config_langs;
		
		return $data;
	}

	function updateConfigSystem($group_id, $code, $value)
	{
		$condition = " code = ? AND group_id = ?";
		return $this->updateWithCondition($condition, array($code, $group_id), $value);
	}
	
	function getConfigureData()
	{
		$result = $this->getRowset();
		$data = array();
		foreach ($result as $row)
		{
			$data[$row['code']] = $row['value'];
		}
	
		$result = $this->query("SELECT * FROM ".TB_LANGUAGE." WHERE active = 1 ORDER BY sort_order DESC");
		$result = $result->fetchAll(PDO::FETCH_ASSOC);
	
		$config_langs = array();
	
		$languages = array();
		foreach ($result as $row)
		{
			if($row['is_default'] == 1)
				$config_langs['default_lang'] = $row['code'];
	
			$languages[$row['code']] = $row;
		}
	
		$config_langs['languages'] = $languages;
	
		// Detect single language
		$config_langs['single_lang'] = 1;
		if (count($result) > 1)
			$config_langs['single_lang'] = 0;
	
		$data['configure_languages'] = $config_langs;
	
		return $data;
	}	
		

}

?>