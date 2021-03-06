<?php

class Page_ContentController extends BaseController
{

	var $_cfg_upload_file;
	var $_cfg_thumb_image;	
	var $_confLn;
	
	var $_items_per_page;

	var $_page_id;
	var $_page_code;
	var $_content_id;
	
	var $_objPageConf;
	var $_rowPageConf;
	var $_objPageContent;
	
	public function __construct()
	{
		parent::__construct();
		$this->_confLn = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $this->_confLn;

		$this->_cfg_upload_file = array();
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_DATA_PATH;
		$this->_cfg_upload_file['allowed_types'] = 'gif|jpg|png';
		$this->_cfg_upload_file['max_size']	= '2000';
		$this->_cfg_upload_file['max_width']  = '2048';
		$this->_cfg_upload_file['max_height']  = '1536';
		
		$this->_cfg_thumb_image['create_thumb'] = TRUE;
		$this->_cfg_thumb_image['maintain_ratio'] = TRUE;
		$this->_cfg_thumb_image['width'] = 175;
		$this->_cfg_thumb_image['height'] = 150;

		$this->_items_per_page = 10;
		
		$this->_page_id = NULL;
		$this->_content_id = NULL;
		
		$this->_objPageConf = new Page_Configure();
		$this->_objPageContent = new Page_Content();
	}

	public function indexAction() 
	{
		return $this->forward("common/error/error-404");
	}
	
// 	public function listAction($page_code, $offset = 0, $by = 'sort_order', $order = 'DESC')
	public function listAction($page_code, $cat_id = 0)
	{
		$this->_loadConfigPage($page_code);
		
		$cat_id = intval($cat_id);
		$offset = $this->oInput->get('offset', 0);
		$by = $this->oInput->get('by', 'sort_order');
		$order = $this->oInput->get('order', 'DESC');
		
		if(df($this->_rowPageConf['data']['use_category'], 0) == 1)
		{
			// --- Use Category ---//
			$objCat = new Page_Category();
			$this->oView->arrMenuTree = $objCat->loadMenuTree($this->_page_id, $this->_confLn['default_lang']);
			
			$currentCategoryName = "--- Filter Category ---";
			if($cat_id != 0)
			{
				$row = $objCat->getRowDataCategory($cat_id);
				$currentCategoryName = $row['ln_cat_field']["{$this->_confLn['default_lang']}"]['name'];
			}
			$this->oView->currentCategoryName = $currentCategoryName;
		}
		
		$this->oView->add_link = site_url("page/content/add/".$page_code);
		$this->oView->delete_link = site_url("page/content/delete/".$page_code);
		$this->oView->update_link = site_url("page/content/update/".$page_code);
		$this->oView->active_link = site_url("page/content/active/".$page_code);
		
		$offset = intval($offset);
		$offset = ($offset % $this->_items_per_page != 0 ? 0 : $offset);

		$rowPageConf = $this->_objPageConf->getRow("code = ?", array($page_code));
		$page_id = $rowPageConf['id'];
		
		$rsContent = $this->_objPageContent->getListDisplay($page_id, $this->_confLn['default_lang'], $cat_id, $offset, $this->_items_per_page, "{$by} {$order}");
		$this->oView->rsContent = $rsContent;
		
		$pages = new Paginator();
		$pages->current_url = site_url("page/content/list/{$page_code}/{$cat_id}?offset=%d&by={$by}&order={$order}");
		$pages->offset = $offset;
		$pages->items_per_page = $this->_items_per_page;
		$pages->items_total = $this->_objPageContent->getTotalRow();
		$pages->mid_range = 7;
		$pages->paginate();
		
		$this->oView->pages = $pages;		
		
    	$this->renderView('page/content/list');
	}

	public function addAction($page_code)
	{
		$this->updateAction($page_code);		
	}	
	
	public function updateAction($page_code ,$content_id = NULL)
	{
		$this->_loadConfigPage($page_code);
		
		// TODO : Check permission user
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
		
		$this->oView->cancel_link = site_url("page/content/list/".$page_code);
	
		$this->oView->arrMainDbFields = $this->_objPageConf->loadContentMainFields();
		$this->oView->arrLnDbFields = $this->_objPageConf->loadContentLnFields();
	
		$arrMainField = $arrLnField = $arrMainImage = $arrLnImage = array();
		
		$intUseCategory = 0;
		$arrMainCatField = $arrLnCatField = $arrMainCatImage = $arrLnCatImage = array();
		
		$arrGalleryField = array();
		
		$dataContent = array();
		if(!empty($content_id))
		{
			$objGallery = new Page_Gallery();
			$totalGalItem = $objGallery->getTotalRow("content_id = ? AND active = 1", array($content_id));
			$this->oView->gallery_link = site_url("page/gallery/show/".$this->_page_id."/".$content_id);
			$this->oView->totalGalItem = $totalGalItem;
			
			$this->oView->form_link = site_url("page/content/update/".$page_code."/".$content_id);
			$this->oView->page_title = "Update record";
			$this->oView->page_action = "update";
			// TODO : Check $dataContent exsit
			$dataContent = $this->_objPageContent->getRowDataContent($content_id);
			
		} else 
		{
			$this->oView->page_title = "Add new record";
			$this->oView->page_action = "add";			
			$this->oView->form_link = site_url("page/content/add/".$page_code);
			
			// Set default value
			$dataContent['main_field']['cat_id'] = 0;
			$dataContent['main_field']['sort_order'] = '';
			$dataContent['main_field']['active'] = 1;
		}
		
// 		echo "<pre>";
// 		print_r($dataContent);
// 		echo "</pre>";
// 		exit();

		if(df($this->_rowPageConf['data']['use_category'], 0) == 1)
		{
			$selected_id = $dataContent['main_field']['cat_id'];
			$objCategory = new Page_Category();
			$this->oView->htmlCat = $objCategory->loadMenuTreeOptionHtml($this->_page_id, "main_field[cat_id]", $selected_id, -1, $this->_confLn);
		}
		
		$this->oView->dataContent = $dataContent;
		
		$data = $this->_rowPageConf['data']; // unserialize($rowPageConf['data']);
		$arrMainField = $data['main_field'];
		$arrLnField = $data['ln_field'];
		$arrMainImage = $data['main_image'];
		$arrLnImage = $data['ln_image'];
		
		$intUseCategory = $data['use_category'];
		$arrMainCatField = $data['main_cat_field'];
		$arrLnCatField = $data['ln_cat_field'];
		$arrMainCatImage = $data['main_cat_image'];
		$arrLnCatImage = $data['ln_cat_image'];		

		$arrGalleryField = $data['gallery_image'];
		
		if ($this->oInput->isPost())
		{
			// Process upload data
			$arrMainImageField = $this->_upload_main_image($arrMainImage);
			$arrLnImageField = $this->_upload_ln_image($arrLnImage);
			
			$arrMainField = $this->oInput->post('main_field');
			$arrLnField = $this->oInput->post('ln_field');
			
			// --- Set current user ---//
			$currentUser = $this->oAuth->currentUser();
			$arrMainField['user_id'] = $currentUser['id'];
			
			// --- Set temp enable_seo_url variable ---//
			$arrMainField['enable_seo_url'] = df($this->_rowPageConf['enable_seo_url'], 0);
			
			$arr_temp = array();
			foreach ($this->_confLn['languages'] as $code => $row)
			{
				if (!empty($arrLnImageField))
					$arr_temp[$code] = array_merge($arrLnField[$code], $arrLnImageField[$code]);
				else 
					$arr_temp[$code] = $arrLnField[$code];
			}			
			
			$arrLnField = $arr_temp;
			
			if (!empty($arrMainImageField))
				$arrMainField = array_merge($arrMainField, $arrMainImageField);
			
			$returnUrl = 'page/content/add/'.$page_code;
			if(empty($content_id))
			{
				// Insert data
				$content_id = $this->_objPageContent->insertContent($this->_page_id, $arrMainField, $arrLnField);
				
				// TODO : Notify insert successfully !
				$this->oSession->set_flashdata('notify_msg',array('msg_title' => "Notify",
						'msg_code' => "success",
						'msg_content' => "Insert successfully !"));				
				//redirect('page/content/add/'.$page_code);
				
			} else 
			{
				// Update data
				$this->_objPageContent->updateContent($content_id, $arrMainField, $arrLnField);
				
				// TODO : Delete old upload data
// 				$dataContent['main_field']['image'];
// 				$dataContent['main_field']['icon'];
// 				$dataContent['ln_field'][$ln_code]['ln_image'];
// 				$dataContent['ln_field'][$ln_code]['ln_icon'];
				
				// TODO : Notify update successfully !
				$this->oSession->set_flashdata('notify_msg',array('msg_title' => "Notify",
						'msg_code' => "success",
						'msg_content' => "Update successfully !"));								
				$returnUrl = 'page/content/update/'.$page_code.'/'.$content_id;
			}
			
			if (df($arrGalleryField['use_gallery'],0) == 1)
			{
				if (!empty($this->oInput->_files["image_gallery"]["name"][0]))
				{
					$file_gallery_data = $this->_upload_files_gallery("image_gallery", $arrGalleryField);
					
					$objGallery = new Page_Gallery();
// 					$objGallery->processImageGallery($content_id, $file_gallery_data, $arrGalleryField);					
					foreach ($file_gallery_data as $file_data)
					{
						$objGallery->insertImageGallery($content_id, $file_data, $arrGalleryField);
					}

				}
			}
			
			redirect($returnUrl);
		}
		
		$this->oView->arrMainField = $arrMainField;
		$this->oView->arrLnField = $arrLnField;
		$this->oView->arrMainImage = $arrMainImage;
		$this->oView->arrLnImage = $arrLnImage;
		
		$this->oView->arrGalleryField = $arrGalleryField;
		
	
		$this->renderView('page/content/_form');
	}
	
	public function deleteAction($page_code, $content_id)
	{
		// Load permission
		$this->detectModifyPermission('page/content/'.$page_code);		
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');		
		
		$rowContent = $this->_objPageContent->getRow($content_id);
		if (!empty($rowContent))
		{
			$result = $this->_objPageContent->deleteContent($content_id);
			if ($result) 
			{
				// TODO : Delete image , get info from $rowContent
			}
		}
		
		redirect('page/content/list/'.$page_code);
	}

	public function activeAction($page_code, $content_id, $offset = 0)
	{
		// Load permission
		$this->detectModifyPermission('page/content/'.$page_code);
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$this->_objPageContent->setActiveField($content_id);		
		redirect('page/content/list/'.$page_code.'?offset='.$offset);	
	}

	private function _loadConfigPage($page_code)
	{
		$objPageConf = new Page_Configure();
		$rowPageConf = $objPageConf->getRow("code = ?",array($page_code));
	
		if (empty($rowPageConf))
		{
			echo "<pre>";
			print_r("Khong ton tai Page");
			echo "</pre>";
			exit();
		}
	
		// Load permission
		$this->detectModifyPermission('page/content/'.$page_code);
		
		$this->_page_id = $rowPageConf['id'];
		$rowPageConf['data'] = unserialize($rowPageConf['data']);
		$this->oView->rowPageConf = $this->_rowPageConf = $rowPageConf;
	}	
	
	private function _upload_main_image($arrMainImage)
	{
		$data = array();
		if (df($arrMainImage['choose'],0) == 1) 
		{
			// Do upload image
			if (!empty($this->oInput->_files["image"]['name']))
			{
				$file_image = $this->_do_upload_file("image", $arrMainImage['image']['width'], $arrMainImage['image']['height']);
				// Resize image
				if (df($arrMainImage['image_thumb']['choose'],0) == 1) 
				{
					$this->_create_thumb_image($file_image, $arrMainImage['image_thumb']['width'], $arrMainImage['image_thumb']['height']);					
				}
				$data['image'] = $file_image['file_name'];
			}
			// Do upload icon			
			if (!empty($this->oInput->_files["icon"]['name']))
			{
				$file_icon = $this->_do_upload_file("icon", $arrMainImage['icon']['width'], $arrMainImage['icon']['height']);
				$data['icon'] = $file_icon['file_name'];
			}						
		}
		return $data;
		// return array('icon'=>'','image'=>'');		
	}
	
	private function _upload_ln_image($arrLnImage)
	{
		$data = array();
		if (df($arrLnImage['choose'],0) == 1)
		{
			foreach ($this->_confLn['languages'] as $code => $row)
			{
// 				$sub_data = array('ln_icon'=>'','ln_image'=>'');
				$sub_data = array();
				if (!empty($this->oInput->_files["ln_image"]["name"][$code]["image"]))
				{
					$name_temp = 'ln_image_temp_'.$code;
					$_FILES[$name_temp]['name'] = $_FILES['ln_image']['name'][$code]['image'];
					$_FILES[$name_temp]['type'] = $_FILES['ln_image']['type'][$code]['image'];
					$_FILES[$name_temp]['tmp_name'] = $_FILES['ln_image']['tmp_name'][$code]['image'];
					$_FILES[$name_temp]['error'] = $_FILES['ln_image']['error'][$code]['image'];
					$_FILES[$name_temp]['size'] = $_FILES['ln_image']['size'][$code]['image'];
					
					$file_image = $this->_do_upload_file($name_temp, $arrLnImage['image']['width'], $arrLnImage['image']['height']);
					// Resize image
					if (df($arrLnImage['image_thumb']['choose'],0) == 1) 
					{
						$this->_create_thumb_image($file_image, $arrLnImage['image_thumb']['width'], $arrLnImage['image_thumb']['height']);
					}
					$sub_data['ln_image'] = $file_image['file_name'];
				}
				// Do upload icon			
				if (!empty($this->oInput->_files["ln_image"]["name"][$code]["icon"]))
				{
					$name_temp = 'ln_icon_temp_'.$code;
					$_FILES[$name_temp]['name'] = $_FILES['ln_image']['name'][$code]['icon'];
					$_FILES[$name_temp]['type'] = $_FILES['ln_image']['type'][$code]['icon'];
					$_FILES[$name_temp]['tmp_name'] = $_FILES['ln_image']['tmp_name'][$code]['icon'];
					$_FILES[$name_temp]['error'] = $_FILES['ln_image']['error'][$code]['icon'];
					$_FILES[$name_temp]['size'] = $_FILES['ln_image']['size'][$code]['icon'];
					
					$file_icon = $this->_do_upload_file($name_temp, $arrLnImage['icon']['width'], $arrLnImage['icon']['height']);
					$sub_data['ln_icon'] = $file_icon['file_name'];
				}			
				$data[$code] = $sub_data;
			}
		}
		return $data;		
		// return va $data = array("en" => array('ln_icon' => "", 'ln_image' => ""), "vn" => array('ln_icon' => "", 'ln_image' => ""));
	}

	private function _upload_files_gallery($field, $arrGalleryField)
	{
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_GALLERY_PATH;
		$this->_cfg_upload_file['file_name']  = 'img_'.strtolower(create_uniqid(5)).'_'.time();
		
		$uploadLib = new UploadLib($this->_cfg_upload_file);
		$returnValue = $uploadLib->do_multi_upload($field);
			
		if (empty($returnValue))
		{
			echo $uploadLib->display_errors();
			exit();
		}
		
		return $returnValue;
	}	
	
	private function _do_upload_file($field, $s_width, $s_height)
	{
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_DATA_PATH;
		$this->_cfg_upload_file['file_name']  = 'img_'.strtolower(create_uniqid(5)).'_'.time();
		$uploadLib = new UploadLib($this->_cfg_upload_file);
	
		if ( ! $uploadLib->do_upload($field))
		{
			echo $uploadLib->display_errors();
			exit();
		}
		else
		{
			$this->_cfg_thumb_image['create_thumb']	= FALSE;			
			$this->_cfg_thumb_image['source_image']	= $uploadLib->data('full_path');
			$this->_cfg_thumb_image['width'] = $s_width;
			$this->_cfg_thumb_image['height'] = $s_height;			
	
			$imageLib = new ImageLib($this->_cfg_thumb_image);
			if($s_width !== 0 && $s_height !== 0)
			{
				$image_config["source_image"] = $uploadLib->data('full_path');
				$image_config['create_thumb'] = FALSE;
				$image_config['new_image'] = $uploadLib->data('full_path');
				$image_config['maintain_ratio'] = FALSE;
				$image_config['quality'] = "100%";
				$image_config['width'] = $s_width;
				$image_config['height'] = $s_height;				
			
				$imageLib->clear();
				$imageLib->initialize($image_config);
				if(!$imageLib->resize_and_crop())
				{
					print_r($imageLib->display_errors());
					exit();
				}
			} else
			{
				if(!$imageLib->resize())
				{
					print_r($imageLib->display_errors());
					exit();
				}
					
			}			
			
			// Thay doi chuc nang moi 17/01/2014
// 			if ( ! $imageLib->resize())
// 			{
// 				echo $imageLib->display_errors();
// 				exit();
// 			}
			// Thum image : $imageLib->thumb_marker.$imageLib->source_image
		}
	
		return  $uploadLib->data();
	}

	private function _create_thumb_image($file_image, $s_width, $s_height)
	{
		$this->_cfg_thumb_image['create_thumb']	= TRUE;
		$this->_cfg_thumb_image['source_image']	= $file_image['full_path'];
		$this->_cfg_thumb_image['width'] = $s_width;
		$this->_cfg_thumb_image['height'] = $s_height;
		
		$imageLib = new ImageLib($this->_cfg_thumb_image);
		
		if($s_width !== 0 && $s_height !== 0)
		{
			$image_config["source_image"] = $file_image['full_path'];
			$image_config['create_thumb'] = FALSE;
			$image_config['new_image'] = $file_image["file_path"] . $imageLib->thumb_marker . $file_image['file_name'];;
			$image_config['maintain_ratio'] = FALSE;
			$image_config['quality'] = "100%";
			$image_config['width'] = $s_width;
			$image_config['height'] = $s_height;			
			
			$imageLib->clear();
			$imageLib->initialize($image_config);
			if(!$imageLib->resize_and_crop())
			{
				print_r($imageLib->display_errors());
				exit();
			}
		} else
		{
			if(!$imageLib->resize())
			{
				print_r($imageLib->display_errors());
				exit();
			}
				
		}		
		
		// Thay doi chuc nang moi 17/01/2014
// 		if ( ! $imageLib->resize())
// 		{
// 			echo $imageLib->display_errors();
// 			exit();
// 		}		
	}
	
}
