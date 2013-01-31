<?php
return array(
	
	//各个分组的权限配置  多个可以用逗号分隔(,)
	'REQUIRE_AUTH_GROUP'   =>'Admin',           // 默认需要认证分组             
	'NOT_AUTH_GROUP'       =>'',               	// 默认无需认证分组     
	'NOT_AUTH_MODULE'       =>'',         		// 默认无需认证模块
	'REQUIRE_AUTH_MODULE'   =>'',               // 默认需要认证模块             
	'NOT_AUTH_ACTION'       =>'login,checkImage,checkUser,sarea,email,test',               // 默认无需认证操作             
	'REQUIRE_AUTH_ACTION'   =>'',               // 默认需要认证操作       
	'USER_AUTH_KEY'         =>'authId',         // 用户认证SESSION标记 (后台)

	'USER_LOGIN_YZM'		=>'userLoginAuthnum',

	'NOT_LOGIN_RETURN_URL'	=>'?g=Admin&m=Main&a=login',//没有登录默认跳转的URL
	
	//授权登陆的角色
	'ROLE_LOGIN_AUTHORIZE'	=>array(
		'SYS','CRS'
	),
);
?>