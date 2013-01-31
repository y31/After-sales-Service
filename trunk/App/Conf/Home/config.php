<?php
return array(
	
	//各个分组的权限配置 多个可以用逗号分隔(,)
	'REQUIRE_AUTH_GROUP'   =>'',               	// 默认需要认证分组             
	'NOT_AUTH_GROUP'       =>'',            // 默认无需认证分组     
	'NOT_AUTH_MODULE'       =>'',         		// 默认无需认证模块
	'REQUIRE_AUTH_MODULE'   =>'Order',               // 默认需要认证模块             
	'NOT_AUTH_ACTION'       =>'',               // 默认无需认证操作             
	'REQUIRE_AUTH_ACTION'   =>'olist,srvlist,detail',               // 默认需要认证操作 
	
	'NOT_LOGIN_RETURN_URL'	=>'?g=Home&m=Member&a=login',//没有登录默认跳转的URL
	
	'USER_AUTH_KEY'         =>'homeAuthId',         // 用户认证SESSION标记 (前台)
	
	'SYS_UPLOAD_CARD_DIR'	=>'Uploads/AZK/',//安装卡文件上传目录  改为 777
	'SYS_UPLOAD_CARD_SIZE'	=>'500', //单位KB
	
	//客户登陆授权角色
	'ROLE_LOGIN_AUTHORIZE_1'	=>array(
		'CR'
	),
	//服务队登录授权角色
	'ROLE_LOGIN_AUTHORIZE_2'	=>array(
		'SRVTEAM'
	),
);
?>