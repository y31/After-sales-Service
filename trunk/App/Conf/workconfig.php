<?php
 return array(
 		'COMPANY_NAME'		=>	'XXXXX',	//公司名称
 		'WEB_SYSTEM_NAME'	=>'佳空间家电报装报修系统',
 		'SYS_ORDER_EMAIL'	=>'lijx.watt@163.com',//系统设置的订单发送邮箱
 		//邮箱配置
 		'MAIL_ADDRESS'		=>	'xtx20121211@163.com', // 邮箱地址
	    'MAIL_SMTP'			=>	'smtp.163.com', // 邮箱SMTP服务器
	    'MAIL_LOGINNAME'	=>	'xtx20121211@163.com', // 邮箱登录帐号
	    'MAIL_PASSWORD'		=>	'xtx123456', // 邮箱密码
	    'MAIL_AUTH'			=>	true, // 是否校验
 		
 		//日志
 		'LOG_WORK_OPEN'		=>	true,	//是否开启业务日志  默认是开启的
 		'LOG_SYS_OPEN'		=>	true,	//是否开启系统日志 默认是开启的
 		
 		//日志类型
 		'LOG_TYPE'			=> array(
 								'USER_LOGIN', //系统
 								'TYPE_ORDER_CR',//客户下单
 							),
 		'P_COUNT'			=>99,//设定服务单数量上限
 							
 		'CR_ADMIN_MOBILE'	=>"您所购%s已生成安装单，可使用手机号登录http://jiakongjian.com跟踪安装进度，初始密码%s",
 		'CR_ADMIN_MOBILE_NOPWD'	=>"您所购%s已生成安装单，可使用手机号登录http://jiakongjian.com跟踪安装进度。",
 		'CR_ADMIN_EMAIL'	=>"您所购%s已生成安装单，可使用邮箱登录<a href='http://jiakongjian.com'>http://jiakongjian.com</a>跟踪安装进度。初始密码%s，登录后请尽快修改。 【佳空间】",
 		'CR_HOME_EMAIL'		=>"您所购%s已生成安装单，可使用邮箱登录<a href='http://jiakongjian.com'>http://jiakongjian.com</a>跟踪安装进度。 【佳空间】",
 							
 		'SRV_EMAIL'		=>"%s您好，您于%s收到安装单，请及时登录<a href='http://jiakongjian.com'>http://jiakongjian.com</a>处理。 【佳空间】",
 		'SRV_MOBILE'	=>"%s您好，您于%s收到安装单，请及时登录http://jiakongjian.com处理",
 							
 		'GETPWD_EMAIL'	=>"您好，新密码为：%s，请及时登录http://jiakongjian.com尽快修改。",
 		
    );
?>