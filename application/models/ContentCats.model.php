<?php

class ContentCats extends Model 
{
	
	protected $_table_name = TB_CONTENT_CATS;
	protected $_primary_key = 'id';	
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function loadMenuTree($rowMenu = NULL)
	{
		$arrMenuTree = array();
		$parent_id = is_null($rowMenu) ? 0 : $rowMenu['id'];
		$rsSubMenu = $this->getRowset("parent_id = ?", array($parent_id), "`sort_order` ASC");
		foreach ($rsSubMenu as $row)
		{
			$row['sub_menus'] = $this->loadMenuTree($row);
			$arrMenuTree[] = $row;
		}
		return $arrMenuTree;
	}	
	
	public function loadMenuTreeOptionHtml($control_name , $selected_id, $cfg_languages)
	{
		$str_html = "<select tabindex='1' name='{$control_name}' id='{$control_name}' data-placeholder='Choose a Category' class='span6'>";
		$str_html .= "<option value=''>Please select...</option>";
		$sel = $selected_id == 0 ? "selected='selected'" : "";
		$str_html .= "<option value='0' {$sel}>ROOT</option>";
		$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id, NULL, 0, $cfg_languages);
		$str_html .= "</select>";
		return $str_html;
	}
	
	private function recur_loadMenuTreeOptionHtml($selected_id, $rowMenu = NULL, $counter, $cfg_languages)
	{
		$lang_code = $cfg_languages['default_lang'];
		$padding = 20*$counter;
		$sel = "";		
		$str_html = "";
		
		$parent_id = is_null($rowMenu) ? 0 : $rowMenu['id'];
		$rsSubMenu = $this->getRowset("parent_id = ?", array($parent_id), "`sort_order` ASC");
		foreach ($rsSubMenu as $row)
		{
			if ($row["id"] == $selected_id)
				$sel = "selected='selected'";
			$str_html .= "<option style='padding-left: {$padding}px' value='".$row["id"]."' {$sel}>|--&nbsp;".h($row["name_{$lang_code}"])."</option>";
			$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id, $row, $counter + 1, $cfg_languages);			
		}
		return $str_html;		
	}
	
}

?>