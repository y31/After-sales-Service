<?php
return array(
		//前台使用的菜单
		'HOME_MENU'				=>array(
									'CR'=>array(
										'报装报修'	=>'?g=Home&m=Index&a=viewbg',
										'查询单据'	=>'?g=Home&m=Order&a=olist',
										'服务流程'	=>'?g=Home&m=Index&a=srvflow',
										'收费标准'	=>'',
										'购买商品'	=>'',
										'了解我们'	=>'',
										),
									'SRVTEAM'=>array(
										'查询单据'	=>'?g=Home&m=Order&a=srvlist',
										'服务流程'	=>'?g=Home&m=Index&a=srvflow',
										'收费标准'	=>'',
										'购买商品'	=>'',
										'了解我们'	=>'',
										),
		),
		//后台使用的菜单
		'ADMIN_MENU'			=>array(
									'SYS'=>array(
										'业务管理'=>array(
											'报装单管理'=>'?g=Admin&m=Order&a=bgList',
											'快速下单'=>'?g=Admin&m=Order&a=add',
											'服务队管理'=>'?g=Admin&m=Team&a=slist',
											'结算管理'=>'?g=Admin&m=Order&a=jsList',
											'产品管理'=>'?g=Admin&m=Product&a=plist',
											'渠道管理'=>'?g=Admin&m=Channel&a=clist',
										),
										'系统管理'=>array(
											'用户管理'=>'?g=Admin&m=User&a=uList',
										    '客户管理'=>'?g=Admin&m=User&a=cList',
										),
									),
									'CRS'=>array(
										'业务管理'=>array(
											'报装单管理'=>'?g=Admin&m=Order&a=bgList',
											'快速下单'=>'?g=Admin&m=Order&a=add',
											'服务队管理'=>'?g=Admin&m=Team&a=slist',
											'结算管理'=>'?g=Admin&m=Order&a=jsList',
											'产品管理'=>'?g=Admin&m=Product&a=plist',
											'渠道管理'=>'?g=Admin&m=Channel&a=clist',
										),
										'系统管理'=>array(
											'客户管理'=>'?g=Admin&m=User&a=cList',
										),
									),
		),
);