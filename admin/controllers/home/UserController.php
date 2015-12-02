<?php

class Home_UserController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->detectModifyPermission('home/user');
		$this->oView->_isModify = $this->_isModify;
	}

	public function indexAction() 
	{
	    return $this->forward('home/user/list');
	}	
	
	public function listAction() 
	{
		$objUser = new Base_User();
		$rsUsers = $objUser->getRowset();
		$this->oView->rsUsers = $rsUsers;
		$this->renderView('home/user/list');
	}
	
	public function addAction()
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$this->oView->box_title = "Add New User";
		$this->oView->link_url = site_url('home/user/add');		
		$this->oView->cancel_url = site_url('home/user/list');
		
		$objGroup = new Base_Group();
		$rsGroups = $objGroup->getRowset();
		$this->oView->rsGroups = $rsGroups;

		if ($this->oInput->isPost()) 
		{
			// TODO : Check validate
			$group_id = $this->oInput->post("group_id",array());
			$group_id = implode(",", $group_id);
			
			$data = array(
				"username" => $this->oInput->post("username",""),					
				"display_name" => $this->oInput->post("display_name",""),		
				"email" => $this->oInput->post("email",""),
				"password" => encryption($this->oInput->post("pw","")),
				"group_id" => $group_id,
				"active" => $this->oInput->post("active",0)					
			);
			
			// TODO : Notify save successfully
			$oUser = new Base_User();
			$oUser->insert($data);			
		}
		
		$this->renderView('home/user/add');
	}
	
	public function editAction($user_id)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$this->oView->box_title = "Update User";
		$this->oView->link_url = site_url('home/user/edit/'.$user_id);
		$this->oView->cancel_url = site_url('home/user/list');
	
		$objGroup = new Base_Group();
		$rsGroups = $objGroup->getRowset();
		$this->oView->rsGroups = $rsGroups;
		
		$oUser = new Base_User();
		
		if ($this->oInput->isPost())
		{
			// TODO : Check validate
			$group_id = $this->oInput->post("group_id",array());
			$group_id = implode(",", $group_id);
			
			$data = array(
				"username" => $this->oInput->post("username",""),					
				"display_name" => $this->oInput->post("display_name",""),
				"email" => $this->oInput->post("email",""),
				"group_id" => $group_id,
				"active" => $this->oInput->post("active",0)
			);			
			
			$reset_password = $this->oInput->post('reset_password',NULL);
			if ($reset_password != NULL)
			{
				// TODO : Check validate password
				$data['password'] = encryption($this->oInput->post("pw",""));
			}
				
			// TODO : Notify save successfully
			$oUser->update($user_id,$data);
			
			$currentUser = $this->oAuth->currentUser();
			
			if ($reset_password != NULL || $data['username'] != $currentUser['username'])
			{
				// Reseted pw or change username
				redirect("home/user/edit/".$user_id);
			}			
		}		
		
		$rowUser = $oUser->get($user_id);
		
		$this->oView->rowUser = $rowUser;
		$this->oView->arrGroupIds = explode(",",$rowUser['group_id']);
		
		$this->renderView('home/user/edit');
	}
	
	public function deleteAction($user_id)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');

        // -- Cannot delete current user --
        $currentUser = $this->oAuth->currentUser();
        if($user_id == $currentUser['id'])
            redirect("home/user/list");

		$oUser = new Base_User();
		$rowAffected = $oUser->delete($user_id);
        if(!empty($rowAffected))
            // TODO : Notify delete success
            echo "success";
        else
            // TODO : Notify delete error
            echo "error";

		redirect("home/user/list");
	}
	
	public function activeAction($user_id)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');

		$oUser = new Base_User();
		$oUser->setActiveField($user_id);
// 		// TODO : Notify save successfully
		redirect("home/user/list");	
	}	

}
