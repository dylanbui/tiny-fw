<?php 

return array(
		
// 	"dashboard_user" => array(
				
// 			"name" => "Users",
// 			"icon" => "icon-user",
				
// 			"sub_menus" => array(
// 					array(
// 							"name" => "List Users",
// 							"link" => "home/user/list",
// 					),
// 					array(
// 							"name" => "Add User",
// 							"link" => "home/user/add",
// 					)					
// 			),
// 	),		

// 	"amway_category" => array(

// 			"name" => "Phân loại câu hỏi",
// 			"icon" => "icon-file-alt",

// 			"sub_menus" => array(
// 					array(
// 							"key" => "amway/category",
// 							"name" => "Danh sách",
// 							"icon" => "icon-file-alt",
// 							"link" => "amway/category/list",
// 					),
// 					array(
// 							"key" => "amway/category",
// 							"name" => "Thêm mới",
// 							"icon" => "icon-file-alt",
// 							"link" => "amway/category/add",
// 					)
// 			),
// 	),
		
	"ex-module" => array(
	
			"name" => "Example Module",
			"icon" => "icon-question",
            "dashboard-item-color" => "yellow", // Item Dashboard
            // primary, green, red, yellow
			"sub_menus" => array(
					array(
							"key" => "ex-module/category",
							"name" => "Category",
							"icon" => "icon-question",
							"link" => "ex-module/category/list"
					),
					array(
							"key" => "ex-module/category",
							"name" => "Category Add",
							"icon" => "icon-file-alt",
							"link" => "ex-module/category/add"
					),
					array(
							"key" => "ex-module/content",
							"name" => "Content",
							"icon" => "icon-question",
							"link" => "ex-module/content/list"
					),
					array(
							"key" => "ex-module/content",
							"name" => "Content Add",
							"icon" => "icon-file-alt",
							"link" => "ex-module/content/add"
					)
			),
	),
				

	"home/contact" => array(
	
			"name" => "Contact",
			"icon" => "icon-file-alt",
            "dashboard-item-color" => "yellow", // Item Dashboard
	
			"sub_menus" => array(
					array(
                        "key" => "home/contact",
                        "name" => "Contact List",
                        "icon" => "icon-file-alt",
                        "link" => "home/contact/list"
					)
			),
	),
		
	"config-system" => array(
				
			"name" => "Configure System",
			"icon" => "icon-gears",
            "dashboard-item-color" => "primary",
            // primary, green, red, yellow
			"sub_menus" => array(
					array(
							"key" => "home/member",
							"name" => "Fb Members",
							"dashboard_btn" => "btn-primary",
							"icon" => "icon-facebook",
							"link" => "home/member/list",
                            "dashboard-item-color" => "green" // Item Dashboard
					),
					array(
							"key" => "home/user",
							"name" => "Users",
							"dashboard_btn" => "btn-primary",
							"icon" => "icon-user",
							"link" => "home/user/list",
                            "dashboard-item-color" => "red" // Item Dashboard
					),
					array(
							"key" => "home/group",
							"name" => "Groups",
							"dashboard_btn" => "btn-primary",
							"icon" => "icon-group",
							"link" => "home/group/list",
                            "dashboard-item-color" => "red" // Item Dashboard
					),										
					array(
							"key" => "page/configure",
							"name" => "Configure Page",
							"dashboard_btn" => "btn-primary",
							"icon" => "icon-gears",
							"link" => "page/configure/list",
                            "dashboard-item-color" => "yellow" // Item Dashboard
					),
					array(
							"key" => "home/config-system",
							"name" => "Backup Database",
							"dashboard_btn" => "btn-primary",
							"icon" => "icon-cloud-download",
							"link" => "home/config-system/backup-db",
                            "dashboard-item-color" => "primary" // Item Dashboard
					),					
					array(
							"key" => "home/config-system",
							"name" => "Configure System",
							"dashboard_btn" => "btn-primary",
							"icon" => "icon-gears",
							"link" => "home/config-system/list",
                            "dashboard-item-color" => "red" // Item Dashboard
					)										
			),			
			
	)		
		
		
);