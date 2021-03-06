<?php

class Page_Category extends Model 
{
	protected $_table_name = TB_PAGE_CATEGORY;
	protected $_primary_key = 'id';	
	
	public $_ln_code;

	// --- Use for front end ---//
	
	public function loadMenuTreeData($page_code, $parent_id = 0, $active = NULL)
	{
		$rowPageConf = $this->getPageRow($page_code);
		return $this->loadMenuTree($rowPageConf['id'], $this->_ln_code, $parent_id, $active);
	}
	
	private function getPageRow($page_code)
	{
		$objPageConf = new Page_Configure();
		$rowPageConf = $objPageConf->getRow("code = ?",array($page_code));
	
		if (empty($rowPageConf))
		{
			print_r("Khong ton tai Page");
			exit();
		}
	
		return $rowPageConf;
	}
		
	// ---  ---//		
	public function getCategoryPath($cat_id, $default_language)
	{
		$obj = new Page_CategoryPath();
		$rs = $obj->getCatPath($cat_id);
		
		$data = array();
		foreach ($rs as $row)
		{
			$rowCat = $this->get($row['path_id']);
			
			$sql = "SELECT ci.* FROM ".TB_PAGE_CATEGORY_LN." as ci WHERE ci.id = ? AND ci.ln = ? LIMIT 0,1";
			
			$sth = $this->query($sql, array($row['path_id'], $default_language));
			$oneRow =	$sth->fetch(PDO::FETCH_ASSOC);
			
			$data[] = array_merge($rowCat, $oneRow);
		}
		
		return $data;
		
	}
	
	public function loadMenuTree($page_id, $default_language, $parent_id = 0, $active = NULL)
	{
		$arrMenuTree = array();
		
		$sql = "SELECT pr.id ,pr.uniqid ,pr.page_id ,pr.code ,pr.parent_id ,pr.active ,pr.sort_order ,pr.last_update ,ci.name FROM ".TB_PAGE_CATEGORY. " as pr INNER JOIN ".TB_PAGE_CATEGORY_LN." as ci ";
		$sql .= " ON pr.id = ci.id ";
// 		$sql .= " WHERE pr.page_id = ? AND pr.parent_id = ? AND ci.ln = ? AND pr.active = 1";
		$sql .= " WHERE pr.page_id = ? AND pr.parent_id = ? AND ci.ln = ?";
		
		if (!is_null($active))
			$sql .= " AND active = " . $active;
		
		$sql .= " ORDER BY `sort_order` ASC";		
		
		$sth = $this->query($sql, array($page_id ,$parent_id ,$default_language));
		
		$rsSubMenu = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		foreach ($rsSubMenu as $row)
		{
			$row['sub_menus'] = $this->loadMenuTree($page_id, $default_language, $row['id']);			
			$arrMenuTree[] = $row;
		}
		
		return $arrMenuTree;
	}	
	
	public function loadMenuTreeOptionHtml($page_id ,$control_name ,$selected_id ,$reject_sub_menu_id = -1 , $cfg_languages)
	{
		$str_html = "<select tabindex='1' name='{$control_name}' id='{$control_name}' data-placeholder='Choose a Category' class='span7'>";
		
		if(is_null($selected_id))
			$str_html .= "<option value=''>Please select...</option>";
		$sel = $selected_id == 0 ? "selected='selected'" : "";
		$str_html .= "<option value='0' {$sel}>ROOT</option>";			
		
		$sql = "SELECT pr.id ,pr.uniqid ,pr.page_id ,pr.parent_id ,pr.active ,pr.sort_order ,pr.last_update ,ci.name FROM ".TB_PAGE_CATEGORY. " as pr INNER JOIN ".TB_PAGE_CATEGORY_LN." as ci ";
		$sql .= " ON pr.id = ci.id ";
		$sql .= " WHERE pr.page_id = ? AND pr.parent_id = 0 AND ci.ln = ? AND pr.active = 1";
		$sql .= " ORDER BY `sort_order` ASC";
		
		$sth = $this->query($sql, array($page_id ,$cfg_languages['default_lang']));
		$rsSubMenu = $sth->fetchAll(PDO::FETCH_ASSOC);				
		
		$counter = 1;
		$padding = 20*$counter;
		
		foreach ($rsSubMenu as $row)
		{
			if ($row["id"] != $reject_sub_menu_id)
			{
				$sel = null;
				if ($row["id"] == $selected_id)
					$sel = "selected='selected'";
								
				$str_html .= "<option style='padding-left: {$padding}px' value='".$row["id"]."' {$sel}>|--&nbsp;".h($row["name"])."</option>";
				$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id ,$reject_sub_menu_id ,$row ,$counter + 1 ,$cfg_languages);
			}
		}
		
		$str_html .= "</select>";
		return $str_html;
	}
	
	private function recur_loadMenuTreeOptionHtml($selected_id ,$reject_sub_menu_id = -1 ,$rowMenu = NULL, $counter, $cfg_languages)
	{
		$padding = 20*$counter;
		$str_html = "";
		
		$sql = "SELECT pr.id ,pr.uniqid ,pr.page_id ,pr.parent_id ,pr.active ,pr.sort_order ,pr.last_update ,ci.name FROM ".TB_PAGE_CATEGORY. " as pr INNER JOIN ".TB_PAGE_CATEGORY_LN." as ci ";
		$sql .= " ON pr.id = ci.id ";
		$sql .= " WHERE pr.parent_id = ? AND ci.ln = ? AND pr.active = 1";
		$sql .= " ORDER BY `sort_order` ASC";
		
		$sth = $this->query($sql, array($rowMenu['id'] ,$cfg_languages['default_lang']));
		$rsSubMenu = $sth->fetchAll(PDO::FETCH_ASSOC);		
		
		foreach ($rsSubMenu as $row)
		{
			if ($row["id"] != $reject_sub_menu_id)
			{
				$sel = NULL;
				if ($row["id"] == $selected_id)
					$sel = "selected='selected'";
				
				$str_html .= "<option style='padding-left: {$padding}px' value='".$row["id"]."' {$sel}>|--&nbsp;".h($row["name"])."</option>";
				$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id ,$reject_sub_menu_id ,$row ,$counter + 1 ,$cfg_languages);
			}			
		}
		return $str_html;		
	}
	
	public function loadRowSetCat($parent_id, $ln_code)
	{
		$sql = "SELECT pr.id ,pr.code ,pr.uniqid ,pr.active ,pr.sort_order ,pr.last_update ,pr.create_at ,ci.name FROM ".TB_PAGE_CATEGORY. " as pr INNER JOIN ".TB_PAGE_CATEGORY_LN." as ci ";
		$sql .= " ON pr.id = ci.id ";
		$sql .= " WHERE pr.parent_id = ? AND ci.ln = ? ";
		$sql .= " ORDER BY `sort_order` ASC";
		
		$sth = $this->query($sql, array($parent_id, $ln_code));
		
		return $sth->fetchAll(PDO::FETCH_ASSOC);		
	}
	
	function getRowDataCategory($cat_id)
	{
		$rowCat = $this->get($cat_id);
	
		$sql = "SELECT ci.* FROM ".TB_PAGE_CATEGORY_LN." as ci WHERE ci.id = ?";
	
		$sth = $this->query($sql, array($cat_id));
		$arr =	$sth->fetchAll(PDO::FETCH_ASSOC);
		
		$rowLnCat = array();
		foreach ($arr as $row)
		{
			$rowLnCat["{$row['ln']}"] = $row;
		}
	
		$data = array();
		$data['main_cat_field'] = $rowCat;
		$data['ln_cat_field'] = $rowLnCat;
		
		return $data;
	}	
	
	function insertCategory($page_id, $arrMainField ,$arrLnField)
	{
		// --- Enable SEO URL ---//
		$enable_seo_url = 0;
		if(isset($arrMainField['enable_seo_url']))
		{
			$enable_seo_url = $arrMainField['enable_seo_url'];
			unset($arrMainField['enable_seo_url']);
		}		
		
		$arrMainField['page_id'] = $page_id;
		$arrMainField['uniqid'] = create_uniqid(10);
		$arrMainField['create_at'] = now_to_mysql();
		$id = $this->insert($arrMainField);
	
		// Update sort_order
		$this->update($id, array('sort_order' => $id));
	
		$this->_table_name = TB_PAGE_CATEGORY_LN;
		$objUrlAlias = new Base_UrlAlias();
		foreach ($arrLnField as $ln => $arrData)
		{
			// --- Create SLUG name ---//
			if (!empty($arrData['name']) && $enable_seo_url == 1)
			{
				$slug_title = str2url($arrData['name']); // Tao slug name
				$objUrlAlias->replaceUrlAlias(array("query"=>'page_cat_id='.$id.'&page_ln='.$ln, "keyword"=>$slug_title));				
			}
			// --------------------------
					
			$arrData['id'] = $id;
			$arrData['ln'] = $ln;
			$arrData['create_at'] = now_to_mysql();
			$this->insert($arrData);
		}
		$this->_table_name = TB_PAGE_CATEGORY;
		
		// --- Update Category Path ---//
		$objCatPath = new Page_CategoryPath();
		$objCatPath->insertCatPath($arrMainField['parent_id'], $id);
		// ------------------------		
		
		return $id;
	}
	
	function updateCategory($cat_id, $arrMainField ,$arrLnField)
	{
		// --- Enable SEO URL ---//
		$enable_seo_url = 0;
		if(isset($arrMainField['enable_seo_url']))
		{
			$enable_seo_url = $arrMainField['enable_seo_url'];
			unset($arrMainField['enable_seo_url']);
		}		
		
		$this->update($cat_id, $arrMainField);
		
		$this->_table_name = TB_PAGE_CATEGORY_LN;
		$objUrlAlias = new Base_UrlAlias();
		foreach ($arrLnField as $ln => $arrData)
		{
			// --- Create SLUG name ---//
			if (!empty($arrData['name']) && $enable_seo_url == 1)
			{
				$slug_title = str2url($arrData['name']); // Tao slug name
				$objUrlAlias->replaceUrlAlias(array("query"=>'page_cat_id='.$cat_id.'&page_ln='.$ln, "keyword"=>$slug_title));				
			}
			// --------------------------
						
			$this->updateWithCondition("id = ? AND ln = ?", array($cat_id, $ln), $arrData);
		}
		$this->_table_name = TB_PAGE_CATEGORY;
		
		// --- Update Category Path ---//		
		$objCatPath = new Page_CategoryPath();
// 		$objCatPath->insertCatPath($arrMainField['parent_id'], $cat_id);
		$objCatPath->updateCatPath($arrMainField['parent_id'], $cat_id);
		// ------------------------		
	}
	
	function deleteImage($action, $cat_id, $ln_code)
	{
		switch ($action) {
			case 'main-image':
				$row = $this->get($cat_id);
				@unlink(__UPLOAD_DATA_PATH.$row['image']);
				@unlink(__UPLOAD_DATA_PATH.'thumb_'.$row['image']);
				$this->update($cat_id,array('image'=>''));
				break;				
			
			case 'main-icon':
				$row = $this->get($cat_id);
				@unlink(__UPLOAD_DATA_PATH.$row['icon']);
				$this->update($cat_id,array('icon'=>''));
				break;
				
			case 'ln-image':
				$sql = "SELECT ci.* FROM ".TB_PAGE_CATEGORY_LN." as ci WHERE ci.id = ? AND ci.ln = ?";
				$sth = $this->query($sql, array($cat_id, $ln_code));
				$arr =	$sth->fetchAll(PDO::FETCH_ASSOC);
				
				if(empty($arr[0]))
					break;
				$row = $arr[0];
				
				@unlink(__UPLOAD_DATA_PATH.$row['ln_image']);
				@unlink(__UPLOAD_DATA_PATH.'thumb_'.$row['ln_image']);
				
				$this->_table_name = TB_PAGE_CATEGORY_LN;
				$this->updateWithCondition("id = ? AND ln = ?", array($cat_id, $ln_code), array('ln_image'=>''));
				$this->_table_name = TB_PAGE_CATEGORY;
				break;
					
			case 'ln-icon':
				$sql = "SELECT ci.* FROM ".TB_PAGE_CATEGORY_LN." as ci WHERE ci.id = ? AND ci.ln = ?";
				$sth = $this->query($sql, array($cat_id, $ln_code));
				$arr =	$sth->fetchAll(PDO::FETCH_ASSOC);
				
				if(empty($arr[0]))
					break;
				$row = $arr[0];
				
				@unlink(__UPLOAD_DATA_PATH.$row['ln_icon']);
				
				$this->_table_name = TB_PAGE_CATEGORY_LN;
				$this->updateWithCondition("id = ? AND ln = ?", array($cat_id, $ln_code), array('ln_icon'=>''));				
				$this->_table_name = TB_PAGE_CATEGORY;
				break;				
			
			default:
				;
			break;
		}
		
	}
	
	function deleteCategory($cat_id)
	{
		if($cat_id != 0)
		{
			// --- Delete child cat ---//
			$rs = $this->getRowset('parent_id = ?', array($cat_id));
			foreach ($rs as $row)
				$this->deleteCategory($row['id']);
			
			// --- Delete current cat content---//			
			$objContent = new Page_Content();
			$rsContent = $objContent->getRowset("cat_id = ?", array($cat_id));
			foreach ($rsContent as $row)
				$objContent->deleteContent($row['id']);			 

			// --- Delete current cat ln ---//
			$sql = "DELETE FROM ".TB_PAGE_CATEGORY_LN;
			$sql .= " WHERE id = ?";
			$this->query($sql, array($cat_id));
			
			// --- Delete current cat ln ---//			
			$this->delete($cat_id);
		}
	}
	
	
	function activeCategory($cat_id)
	{
		$this->setActiveField($cat_id);
		$rowCurrent = $this->get($cat_id);		
		$rs = $this->getRowset('parent_id = ?', array($cat_id));
		foreach ($rs as $row)
			$this->recur_activeCategory($row['id'], $rowCurrent['active']);
	}	

	// --- recur_activeCategory ---//
	private function recur_activeCategory($cat_id, $value)
	{
		$this->update($cat_id, array('active' => $value));
		$rs = $this->getRowset('parent_id = ?', array($cat_id));
		foreach ($rs as $row)
			$this->recur_activeCategory($row['id'], $value);
	}
	
// 	public function insert($values)
// 	{
// 		// 		$data = $values;
// 		// 		unset($values['slug_title']);
// 		$last_id = parent::insert($values);
	
// 		if (!empty($values['name']))
// 		{
// 			$slug_title = $values['name']; // Tao slug name
// 			$this->replaceUrlAlias($last_id, $slug_title);
// 		}
	
// 		$objCatPath = new Ex_ContentCatsPath();
// 		$objCatPath->insertCatPath($data['parent_id'], $last_id);
	
// 		return $last_id;
// 	}
	
// 	public function update($id, $values)
// 	{
// 		$data = $values;
// 		unset($values['slug_title']);
// 		$result = parent::update($id, $values);
	
// 		if (!empty($data['slug_title'])) {
// 			$this->replaceUrlAlias($id, $data['slug_title']);
// 		}
	
// 		$objCatPath = new Ex_ContentCatsPath();
// 		$objCatPath->insertCatPath($data['parent_id'], $id);
	
// 		return $result;
// 	}
	
// 	private function replaceUrlAlias($id ,$ln ,$slug_title)
// 	{
// 		$objUrlAlias = new Base_UrlAlias();
// 		$objUrlAlias->replaceUrlAlias(array("query"=>'page_cat_id='.$id.'&page_ln='.$ln, "keyword"=>$slug_title));
// 	}
	
	
	
}

?>