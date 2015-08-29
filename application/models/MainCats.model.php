<?php

class MainCats extends Model 
{
	protected $_table_name = TB_MAIN_CATS;
	protected $_primary_key = 'id';	
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function loadMenuTree($code_root)
	{
		$arrMenuTree = array();
		$rsSubMenu = $this->getRowset("code = ?", array($code_root), "`sort_order` ASC");
		
		foreach ($rsSubMenu as $row)
		{
			$row['sub_menus'] = $this->recur_loadMenuTree($row);
			$arrMenuTree[] = $row;
		}
		return $arrMenuTree;
	}	
	
	public function recur_loadMenuTree($rowMenu = NULL)
	{
		$arrMenuTree = array();
		$parent_id = is_null($rowMenu) ? 0 : $rowMenu['id'];
		$rsSubMenu = $this->getRowset("parent_id = ?", array($parent_id), "`sort_order` ASC");
		foreach ($rsSubMenu as $row)
		{
			$row['sub_menus'] = $this->recur_loadMenuTree($row);
			$arrMenuTree[] = $row;
		}
		return $arrMenuTree;
	}	
	
	public function loadMenuTreeOptionHtml($code_root ,$control_name ,$selected_id ,$reject_sub_menu_id = -1 , $cfg_languages)
	{
		$str_html = "<select tabindex='1' name='{$control_name}' id='{$control_name}' data-placeholder='Choose a Category' class='span7'>";
// 		$str_html .= "<option value=''>Please select...</option>";
		$sel = $selected_id == 0 ? "selected='selected'" : "";
		$str_html .= "<option value='0' {$sel}>ROOT</option>";
		
		$lang_code = $cfg_languages['default_lang'];
		$counter = 1;
		$padding = 20*$counter;
		$rsSubMenu = $this->getRowset("code = ?", array($code_root), "`sort_order` ASC");
		
		foreach ($rsSubMenu as $row)
		{
			if ($row["id"] != $reject_sub_menu_id)
			{
				$sel = null;
				if ($row["id"] == $selected_id)
					$sel = "selected='selected'";
								
				$str_html .= "<option style='padding-left: {$padding}px' value='".$row["id"]."' {$sel}>|--&nbsp;".h($row["name_{$lang_code}"])."</option>";
				$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id ,$reject_sub_menu_id ,$row ,$counter + 1 ,$cfg_languages);
			}
			
// 			if(!is_null($sel))
// 			{
// 				if ($load_sub_menu_selected == TRUE) 
// 				{
// 					$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id ,$load_sub_menu_selected ,$row, $counter + 1, $cfg_languages);
// 				}
// 			} else 
// 			{
// 				$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id ,$load_sub_menu_selected ,$row ,$counter + 1 ,$cfg_languages);
// 			}
			
// 			$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id ,$load_sub_menu_selected ,$row ,$counter + 1 ,$cfg_languages);			
			
		}
		
		$str_html .= "</select>";
		return $str_html;
	}
	
	private function recur_loadMenuTreeOptionHtml($selected_id ,$reject_sub_menu_id = -1 ,$rowMenu = NULL, $counter, $cfg_languages)
	{
		$lang_code = $cfg_languages['default_lang'];
		$padding = 20*$counter;
		$str_html = "";
		
		$parent_id = is_null($rowMenu) ? 0 : $rowMenu['id'];
		$rsSubMenu = $this->getRowset("parent_id = ?", array($parent_id), "`sort_order` ASC");
		foreach ($rsSubMenu as $row)
		{
			if ($row["id"] != $reject_sub_menu_id)
			{
				$sel = NULL;
				if ($row["id"] == $selected_id)
					$sel = "selected='selected'";
				
				$str_html .= "<option style='padding-left: {$padding}px' value='".$row["id"]."' {$sel}>|--&nbsp;".h($row["name_{$lang_code}"])."</option>";
				$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id ,$reject_sub_menu_id ,$row ,$counter + 1 ,$cfg_languages);
			}			
			
// 			if(!is_null($sel))
// 			{
// 				if ($load_sub_menu_selected == TRUE)
// 				{
// 					$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id ,$load_sub_menu_selected, $row, $counter + 1, $cfg_languages);
// 				}
// 			} else
// 			{
// 				$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id ,$load_sub_menu_selected, $row, $counter + 1, $cfg_languages);
// 			}			
			
// 			$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id ,$load_sub_menu_selected, $row, $counter + 1, $cfg_languages);			
		}
		return $str_html;		
	}
	
}

?>