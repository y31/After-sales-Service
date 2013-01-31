<?php
/**
 * 后台管理主页面类
 * Enter description here ...
 * @author df
 *
 */
class MainAction extends BaseAction {
	
	const USER_LOGIN_OK				=	1;		//成功
	const USER_LOGIN_YZM_ERR		=	2;		// 验证码错误
	const USER_LOGIN_NAME_ERR		=	3;		// 用户名错误
	const USER_LOGIN_PWD_ERR		=	4;		// 密码错误
	const USER_LOGIN_AUTHORIZE_ERR	=	5;		// 没有登陆的授权
	/**
	 * 主页面
	 * Enter description here ...
	 */
	function index(){
		$this->display('index');
	}
	/**
	 * 登录页面
	 * Enter description here ...
	 */
	function login(){
		
		$this->display('login');
	}
	/**
	 * login提交保存的方法
	 * Enter description here ...
	 */
	function checkUser(){
		/**
		 * 1: 登陆正确
		 * 2：验证码错误
		 * 3：用户错误
		 * 4：密码错误
		 * Enter description here ...
		 * @var unknown_type
		 */
		$userName=$this->in("userName");
		$userPwd=$this->in("userPwd");
		$yzm=$this->in("yzm");
		if(!$userName){
			echo self::USER_LOGIN_NAME_ERR;
			exit;
		}
		if(!$userPwd){
			echo self::USER_LOGIN_PWD_ERR;
			exit;
		}
		if(!$yzm){
			echo self::USER_LOGIN_YZM_ERR;
			exit;
		}
		/**
		 * 先检测验证码是否正确
		 */
		if(strtolower($_SESSION["userLoginAuthnum"])!=strtolower($yzm)){
			echo self::USER_LOGIN_YZM_ERR;
			exit;
		}
		$user=D("User");
		//先查用户
		$search["U_NAME"]=$userName;
		$data=$user->where($search)->select();
		if($data){
			$pwd=$data[0]["U_PWD"];
			if($pwd!=md5($userPwd)){
				echo self::USER_LOGIN_PWD_ERR;
				exit;
			}
		}else{
			echo self::USER_LOGIN_NAME_ERR;
			exit;
		}
		
		$_user=$data[0];
		//取用户的角色
		try{
			$_roles=ExUse::getUserRolesByArr($_user["U_ID"]);
			
			if($_roles && is_array($_roles) && count($_roles)){
				//如果是多角色  先取一个角色使用
				$_user["roleId"]=$_roles[0]["R_ID"];
				$_user["roleName"]=$_roles[0]["R_NAME"];
				$_user["roleShortname"]=$_roles[0]["R_SHORT_NAME"];
			}
		}
		catch (Exception $e){
			/*echo "<pre>";
			print_r($e);
			echo "</pre>";
			*/
			echo self::USER_LOGIN_AUTHORIZE_ERR;
			exit;
		}
		
		if($_user["roleShortname"]){
			$__ah=C("ROLE_LOGIN_AUTHORIZE");
			if(!in_array($_user["roleShortname"], $__ah)){
				echo self::USER_LOGIN_AUTHORIZE_ERR;
				exit;
			}
		}else{
			echo self::USER_LOGIN_AUTHORIZE_ERR;
			exit;
		}
		//写入session
		ExSession::getSession()->setUserByArray($_user);
		
		echo self::USER_LOGIN_OK;
		ExLog::addLog($userName."后台登录", "USER_LOGIN");
		exit;
	}
	/**
	 * 验证码
	 * Enter description here ...
	 */
	function checkImage(){
		$an = new Authnum(); 
		$an->ext_num_type=''; 
		$an->ext_pixel = true; //干扰点 
		$an->ext_line = true; //干扰线 
		$an->ext_rand_y= true; //Y轴随机 
		$an->green = 245; 
		$image=$an->create(); 
		echo $image;
		exit;
		
	}
	function top(){
		//$this->assign('username',$_SESSION["user_name"]);
		//获取IP
		$ip = get_client_ip();

		$this->assign("IP",$ip);
		$this->assign("COMPANY_NAME",C("COMPANY_NAME"));
		$this->assign("userName",ExSession::getSession()->getUserName());
		$this->assign("roleName",ExSession::getSession()->getRoleName());
		$this->display("top");
	}
	function left(){
		$menu=getAdminMenu();
		$this->assign("menu",$menu);
		$this->display("left");
	}
	function right(){
		$this->display("right");
	}
	function down(){
		$this->display("down");
	}
	function center(){
		$this->display("center");
	}
	/**
	 * 退出系统
	 * Enter description here ...
	 */
	function logout(){
		//直接销毁会话信息
		session_destroy();
		ExLog::addLog(ExSession::getSession()->getRealName()."后台退出", "USER_LOGOUT");
		redirect('?g=Admin&m=Main&a=login');    
	}
	
	function addlog(){
		$o_id=$this->in("o_id");
		$o_no=$this->in("o_no");
		
		if($this->in("op")){
			$ct=$this->in("content");
			if($ct){
				ExLog::addLog($ct, "USER_ADD_LOG",2,$o_id,$o_no);
				echo "y";
			}
			else{
				echo 'n';
			}
			exit;
		}
		$this->assign("o_id",$o_id);
		$this->assign("o_no",$o_no);
		$this->display();
	}
}