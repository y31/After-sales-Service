<?php
 return array(
 		'URL_MODEL'				=> 0,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
 		'APP_DEBUG'				=>true,
 		'DB_FIELD_CACHE'		=>false,
 		'HTML_CACHE_ON'			=>false,
 		'TMPL_CACHE_ON'   		=>false,
 		'ACTION_CACHE_ON'  		=>false,  // 默认关闭Action 缓存 
        'APP_GROUP_LIST'		=>'Admin,Home', // 分组
        'DEFAULT_GROUP'			=> 'Home',  // 默认分组
 	    'USER_AUTH_ON'          =>true,             //开启认证  
		'USER_AUTH_TYPE'        =>2,                // 默认认证类型 1 登录认证 2 实时认证
		'ADMIN_AUTH_KEY'        =>'admin',
		//'USER_AUTH_MODEL'       =>'User',           // 默认验证数据表模型           
		'AUTH_PWD_ENCODER'      =>'md5',            // 用户认证密码加密方式         
		//'USER_AUTH_GATEWAY'     =>'/Admin/Main/login',  // 默认认证网关  
		'GUEST_AUTH_ON'         =>false,            // 是否开启游客授权访问         
		'GUEST_AUTH_ID'         =>0,                // 游客的用户ID
		
 		'SHOW_RUN_TIME' 		=>  false,  //运行时间显示
		'SHOW_ADV_TIME' 		=>  false,  //显示详细的运行时间
		'SHOW_DB_TIMES' 		=>  false,  //显示数据库的操作次数
		'SHOW_CACHE_TIMES'		=>	false,  //显示缓存操作次数
		'SHOW_USE_MEM'  		=>  false,  //显示内存开销
		'SHOW_PAGE_TRACE' 		=> 	false,   // 显示页面Trace信息 由Trace文件定义和Action操作赋值
    	'SHOW_ERROR_MSG'        =>  false,    // 显示错误信息

 
		//'DB_LIKE_FIELDS'        =>'title|remark',   //开启like匹配
		//'RBAC_USER_TABLE'    =>'think_role_user',    //用户角色明细表  
		//'RBAC_ROLE_TABLE'    =>'think_role',  //角色表  
		//'RBAC_ACCESS_TABLE'  =>'think_access',    //权限表  
		//'RBAC_NODE_TABLE'    =>'think_node',  //节点表  
 
 		'APP_AUTOLOAD_PATH'         => '@.Extend.Util',//类库自动加载 自定义类存放的目录  多个用,号分隔
		'LOAD_EXT_CONFIG'			=>	'dictconfig,dbconfig,workconfig,menuconfig',		   //加载扩展配置文件
		'LOAD_EXT_FILE'				=>'myfunction,dictFunction',	//加载扩展函数
		
 		'SESSION_PREFIX'        => 'SES_', // session 前缀

 
 	    'TMPL_PARSE_STRING' 		=>array(
 			'__JS__'		=>		'Public/js',
 			'__CSS__'		=>		'Public/css',
 			'__PUBLIC__'	=>		'Public',
 		),
 		
 		'SYS_UPLOAD_DIR'	=>'Uploads/',//系统使用的上传文件目录
 		
 		//'html'				=>'@.TagLib.TagLibHtml',//定义自己的标签库
 		
    );
?>