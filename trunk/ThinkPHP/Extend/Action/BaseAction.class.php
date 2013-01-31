<?php
class BaseAction extends Action{
	function _initialize() {  
		header('Content-Type:text/html;charset=utf-8');        
		// 用户权限检查        
		//if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) 
		if(ACTION_NAME!='login' || ACTION_NAME!='index'){
			//ExSession::getSession()->recordVisitPage(curPageURL());
		}
		if (C('USER_AUTH_ON') && !in_array(GROUP_NAME, explode(',', C('NOT_AUTH_GROUP')))) 
		{   
			/*         
			import('ORG.Util.RBAC');            
			if (!RBAC::AccessDecision()) 
			{                
				//检查认证识别号                
				if (!$_SESSION [C('USER_AUTH_KEY')]) 
				{                    
					//跳转到认证网关                    
					redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));                
				}                
				// 没有权限 抛出错误                
				if (C('RBAC_ERROR_PAGE')) 
				{                    
					// 定义权限错误页面                    
					redirect(C('RBAC_ERROR_PAGE'));                
				} 
				else 
				{                    
					if (C('GUEST_AUTH_ON')) 
					{                        
					$this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));                    
					}                    
					// 提示错误信息                    
					$this->error(L('_VALID_ACCESS_'));                
				}            
			}   
			*/
			//认证
			if(UserRbac::checkAccess()){
				if (!$_SESSION [C('USER_AUTH_KEY')]) 
				{                    
					//跳转到认证网关                    
					//redirect(PHP_FILE . C('USER_AUTH_GATEWAY')); 

					
					//redirect(C('NOT_LOGIN_RETURN_URL'));  
					echo "<script>";  
					echo "top.window.location.href='".C('NOT_LOGIN_RETURN_URL')."'";            
					echo "</script>";     
					exit;         
				}             
			}
			     
		}
	}
	
	public function in($v){
		return $_REQUEST[$v];
	}
}