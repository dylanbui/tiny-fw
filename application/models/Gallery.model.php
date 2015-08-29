<?php

class Gallery extends Model
{
	protected $_table_name = TB_GALLERY;
	protected $_primary_key = 'id';	
	

	public function insert($group_code ,$relation_id ,$images_data)
	{
		$data['group_code'] = $group_code;
		$data['relation_id'] = $relation_id;		
		$data['create_at'] = now_to_mysql();
		
		foreach ($images_data as $image)
		{
			$data['image'] = $image['file_name'];
			parent::insert($data);
		}
	}

}

?>