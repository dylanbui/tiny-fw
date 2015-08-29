<?php

class Home_ConfigSystemController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->detectModifyPermission('home/config-system');
		$this->oView->_isModify = $this->_isModify;		
	}

	public function indexAction() 
	{
		return $this->forward('home/config-system/list');
	}	
	
	public function listAction() 
	{
		$oConfigSys = new Base_ConfigureSystem();
		$data = $oConfigSys->getAllGroups();
		
		$this->oView->arrConfigData = $data;
		$this->oView->save_link = site_url('home/config-system/save');
		
		$this->renderView('home/config-system/list');
	}
	
	public function saveAction($group_id)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$post = $this->oInput->_post; 
		$oConfigSys = new Base_ConfigureSystem();
		foreach ($post as $key => $value)
		{
			$num = $oConfigSys->updateConfigSystem($group_id, $key, array("value" => $value ));
		}
		
		redirect('home/config-system/list');		
	}
	
	public function backupDbAction()
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$this->oView->box_title = "Backup Database";
		$this->oView->box_action = "List Backup Files";
		
		if ($this->oInput->isPost()) 
		{
// 			$config['database_master']['db_type'] 				= "mysql";
// 			$config['database_master']['db_hostname'] 			= "localhost";
			
			$db_type = $this->oConfig->config_values['database_master']['db_type'];
			$hostname = $this->oConfig->config_values['database_master']['db_hostname'];
			$database = $this->oConfig->config_values['database_master']['db_name'];
			$username = $this->oConfig->config_values['database_master']['db_username'];
			$password = $this->oConfig->config_values['database_master']['db_password'];
			
			$time = str_replace(array('-',':',' '), "_", now_to_mysql());
			
			$dumpSettings['compress'] = $this->oInput->post('backup_type');
			
			try {
				$dump = new MysqlDump($database, $username, $password, $hostname, $db_type, $dumpSettings, array());
				$dump->start(__SITE_PATH.'/sql/backup_'.$time.'_mysql.sql');
			} catch (Exception $e) {
				echo 'mysqldump-php error: ' . $e->getMessage();
				exit();
			}
			
			redirect('home/config-system/backup-db');
		}
		
		$this->oView->upload_dir = __SITE_PATH.'/sql';
		$this->renderView ('home/config-system/backup-db');
	
	}

	public function importDbAction($filename)
	{
		$database = $this->oConfig->config_values['database_master']['db_name'];
		$username = $this->oConfig->config_values['database_master']['db_username'];
		$password = $this->oConfig->config_values['database_master']['db_password'];
		
		$restore_file = __SITE_PATH.'/sql/'.$filename;
		
		if (preg_match("/\.gz$/i",$filename))
		{
			$source_file = __SITE_PATH.'/sql/'.$filename;
			$restore_file = str_replace('.gz', '', $source_file);;
			
			$fp = fopen($restore_file, "w");
			fwrite($fp, implode("", gzfile($source_file)));
			fclose($fp);
		}
		
		#Now restore from the .sql file
		$command = "mysql --user={$username} --password={$password} --database={$database} < ".$restore_file;
		
// 		echo "<pre>";
// 		print_r($command);
// 		echo "</pre>";
// 		exit();
		
		exec($command);
		
// 		echo "<pre>";
// 		print_r('khong lam gi ca');
// 		echo "</pre>";
// 		exit();
		
		redirect('home/config-system/backup-db');
	}
	
	
	public function deleteDbAction($filename)
	{
		unlink(__SITE_PATH.'/sql/'.$filename);
		redirect('home/config-system/backup-db');
	}
	
	
	public function dumpDbAction()
	{
		// 		$config['database_master']['db_name'] 				= "7up-haisan";
		// 		$config['database_master']['db_username'] 			= "root";
		// 		$config['database_master']['db_password'] 			= "sofresh123";
	
		$database = $this->oConfig->config_values['database_master']['db_name'];
		$username = $this->oConfig->config_values['database_master']['db_username'];
		$password = $this->oConfig->config_values['database_master']['db_password'];
		//Gzip
		$time = str_replace(array('-',':',' '), "_", now_to_mysql());
	
		try {
			$dump = new MysqlDump($database, $username, $password);
			$dump->start(__SITE_PATH.'/sql/backup_'.$time.'_mysql.sql');
		} catch (Exception $e) {
			echo 'mysqldump-php error: ' . $e->getMessage();
		}
	
		echo "<pre>";
		print_r("Xonggggg");
		echo "</pre>";
		exit();
	
	}
	
	public function runCommanderAction()
	{
		$arr = array();
		$str = exec("dir", $arr);
	
		echo "<pre>";
		print_r(__SITE_PATH);
		echo "</pre>";
	
		echo "<pre>";
		print_r($str);
		echo "</pre>";
	
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
		exit();
	
	
	}
	
	public function bigDumpAction()
	{
		$this->oView->backup_dir = __SITE_PATH.'/sql';
		
		$database = $this->oConfig->config_values['database_master']['db_name'];
		$username = $this->oConfig->config_values['database_master']['db_username'];
		$password = $this->oConfig->config_values['database_master']['db_password'];
		//Gzip
		$time = str_replace(array('-',':',' '), "_", now_to_mysql());
		
		echo "<pre>";
		print_r($_REQUEST);
		echo "</pre>";
		exit();
	
		$this->oView->database = $database;
		$this->oView->username = $username;
		$this->oView->password = $password;
	
		echo $this->oView->fetch('home/config-system/big-dump');
		exit();
	
		// 		$this->renderView ('index/search/big-dump');
	}
	

}
