<?php

class Home_ContactController extends BaseController
{
	
	public function __construct()
	{
		parent::__construct();
		
	}

	public function indexAction() 
	{
		$this->listAction();		
	}	
	
	public function listAction() 
	{
		$this->oView->box_title = "Contact";
		$this->oView->box_action = "Contact";
		
		$objContact = new Contact();
		$this->oView->rsContact = $objContact->getRowset();
		
// 		$this->oSession->set_flashdata('notify_msg',array('msg_title' => "Thong bao",
// 				'msg_code' => "success",
// 				'msg_content' => array("Thong bao content", "Thong bao content","Thong bao content")));
		
		$this->renderView('home/contact/list');
	}
	
	public function addAction()
	{
		$this->oView->box_title = "Contact";
		$this->oView->box_action = "Add Contact";		
		$this->oView->link_url = site_url('home/contact/add');
		$this->oView->rowContact = array();
		
		if ($this->oInput->isPost())
		{
			$objContact = new Contact();
			
			$data['active'] = $this->oInput->post('active' ,0);

			$data["fullname"] = $this->oInput->post("fullname");
			$data["email"] = $this->oInput->post("email", NULL);
			$data["cellphone"] = $this->oInput->post("cellphone");			
			
			$data["title"] = $this->oInput->post("title");
			$data["content"] = $this->oInput->post("content", NULL);
			$data["answer"] = $this->oInput->post("answer", NULL);
			
			$data["data"] = $this->oInput->post("data");
			
			$data['create_at'] = now_to_mysql();
			$last_id = $objContact->insert($data);
			
			if ($data['active'] == 1 && !empty($data["answer"]) || !empty($data["email"]))
			{
				$this->sendEmailNotifyAction($data);
			}
			
			redirect("home/contact/list");
		}

		$this->renderView('home/contact/_form');
	}
	
	public function editAction($content_id)
	{
		$this->oView->box_title = "Contact";
		$this->oView->box_action = "Edit Contact";
		$this->oView->link_url = site_url('home/contact/edit/'.$content_id);
		$this->oView->content_id = $content_id;
		
		$objContact = new Contact();
		
		if ($this->oInput->isPost())
		{
			$data['active'] = $this->oInput->post('active' ,0);

			$data["fullname"] = $this->oInput->post("fullname");
			$data["email"] = $this->oInput->post("email", NULL);
			$data["cellphone"] = $this->oInput->post("cellphone");			
			
			$data["title"] = $this->oInput->post("title");
			$data["content"] = $this->oInput->post("content", NULL);
			$data["answer"] = $this->oInput->post("answer", NULL);
			
			$data["data"] = $this->oInput->post("data");
						
			$objContact->update($content_id,$data);
			
			if ($data['active'] == 1 && !empty($data["answer"]) || !empty($data["email"]))
			{
				$this->sendEmailNotifyAction($data);
			}

			redirect("home/contact/list");			
		}

		$rowContact = $objContact->get($content_id);
		$this->oView->rowContact = $rowContact;		
		
		$this->renderView('home/contact/_form');
	}
	
	public function deleteAction($contact_id)
	{
		$objContact = new Contact();
		$objContact->delete($contact_id);
				
		redirect("home/contact/list");
	}
	
	public function sendEmailNotifyAction($data)
	{
		$arrVal['data'] = $data;
	
		$oConfigSys = new Base_ConfigureSystem();
		$rs = $oConfigSys->getGroupConfigure(2);

		foreach ($rs as $row)
			$configEmail[$row['code']] = $row['value'];
		
		$email_view = new View();
		$body = $email_view->parser(__LAYOUT_PATH."/email_template/notify_contact.phtml",$arrVal);
			
// 		print_r($body);
// 		exit();

		$email = new Email($data['email'],"Trả lời từ Amway",$body);
		$email->connect($configEmail);
		$result = $email->Send();
	}
	

}
