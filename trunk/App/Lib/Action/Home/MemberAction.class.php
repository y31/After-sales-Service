<?php
class MemberAction extends BaseAction{
	
	const USER_LOGIN_OK			=	1;		//成功
	const USER_LOGIN_YZM_ERR	=	2;		// 验证码错误
	const USER_LOGIN_NAME_ERR	=	3;		// 用户名错误
	const USER_LOGIN_PWD_ERR	=	4;		// 密码错误
	const USER_LOGIN_AUTHORIZE_ERR	=	5;		// 没有登陆的授权
	/**
	 * 客户前台登陆
	 * Enter description here ...
	 */
	function login(){
		$sid=$this->in('sid');
		//$sid 说明来源是工单  登录成功后再回到工单
		if($sid){
			$url="?g=Home&m=Index&a=viewbg&islogin=y&sid=".$sid;
		}else{
			$url="?g=Home&m=Order&a=olist";
		}
		$this->assign('sid',$sid);
		$this->assign('returnURL',$url);
		//取菜单
	   	$menus=getMenu();
	   	$this->assign('menu',$menus);
	   	//判断是否自动登录
	   	if($_COOKIE["locationAutonLoginJKJ"]){
	   		
	   		$__userinfo=explode('|', $_COOKIE["locationAutonLoginJKJ"]);
	   		$rev=ExUse::setUser($__userinfo[0],$__userinfo[1]);
	   		if($rev){
	   			$this->redirect("?g=Home&m=Index&a=viewbg");
	   		}
	   	}
	   
	   	if($_COOKIE["locationUserJKJ"]){
	   		$__s=$_COOKIE["locationUserJKJ"];
	   		$this->assign("LOCATIONUSER",$__s);
	   	}
	   	
		$this->display();
	}
	function loginform(){
		$this->display();
	}
	function checkuser(){
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
		$sid=$this->in("sid");
		$role=$this->in("role");
		$useUser=$this->in("useUser");
		$autologin=$this->in("autologin");
		$user=D("User");
		//会员名/邮箱  
		$IS_USER_OK=false;
		$search["U_NAME"]=$userName;
		$data=$user->where($search)->select();
		if($data){
			/*$pwd=$data[0]["U_PWD"];
			if($pwd!=md5($userPwd)){
				echo self::USER_LOGIN_PWD_ERR;
				exit;
			}
			*/
			$IS_USER_OK=true;
		}else{
			//echo self::USER_LOGIN_NAME_ERR;
			//exit;
			//检查是否邮箱登录
			$user=D("User");
			$search=array();
			$search["U_EMAIL"]=$userName;
			$data=$user->where($search)->select();
			if($data){
				$IS_USER_OK=true;
			}else{
				//检查是否手机登录
				$user=D("User");
				$search=array();
				$search["U_MOBILE"]=$userName;
				$data=$user->where($search)->select();
				if($data){
					$IS_USER_OK=true;
				}
			}
			
		}
		//检查密码
		if($IS_USER_OK && $data){
			$pwd=$data[0]["U_PWD"];
			if($pwd!=md5($userPwd)){
				
				$arr=array("info"=>'',"status"=>self::USER_LOGIN_PWD_ERR);
				echo json_encode($arr);
				exit;
				/*echo self::USER_LOGIN_PWD_ERR;
				exit;*/
			}
		}else{
			$arr=array("info"=>'',"status"=>self::USER_LOGIN_NAME_ERR);
			echo json_encode($arr);
			exit;
			/*
			echo self::USER_LOGIN_NAME_ERR;
			exit;*/
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
			$arr=array("info"=>'',"status"=>self::USER_LOGIN_AUTHORIZE_ERR);
			echo json_encode($arr);
			exit;
			/*echo self::USER_LOGIN_AUTHORIZE_ERR;
			exit;*/
		}
		if($role=='srv'){
			if($_user["roleShortname"]){
				$__ah=C("ROLE_LOGIN_AUTHORIZE_2");
				//先只对服务队登录限制吧
				if(!in_array($_user["roleShortname"], $__ah)){
					$arr=array("info"=>'',"status"=>self::USER_LOGIN_AUTHORIZE_ERR);
					echo json_encode($arr);
					exit;
					/*
					echo self::USER_LOGIN_AUTHORIZE_ERR;
					exit;
					*/
				}
				
			}else{
				
				$arr=array("info"=>'',"status"=>self::USER_LOGIN_AUTHORIZE_ERR);
				echo json_encode($arr);
				exit;
				/*
				echo self::USER_LOGIN_AUTHORIZE_ERR;
				exit;*/
			}
		}
		//客户登录界面 服务队不能登录
		if($role=='cr'){
			if($_user["roleShortname"]){
				$__ah=C("ROLE_LOGIN_AUTHORIZE_2");
				//客户登录判断是否服务队角色 
				if(in_array($_user["roleShortname"], $__ah)){
					$arr=array("info"=>'',"status"=>self::USER_LOGIN_AUTHORIZE_ERR);
					echo json_encode($arr);
					exit;
					/*echo self::USER_LOGIN_AUTHORIZE_ERR;
					exit;*/
				}
				
			}
		}
		
		//写入COOKIE
		if($useUser=='y'){
			setcookie("locationUserJKJ",$userName,time()+86400*30);
		}else{
			//删除COOKIE
			setcookie("locationUserJKJ",$userName,time()-36000);
		}
		//$arr=array("USER"=>$userName,"PWD"=>md5($userPwd));
		$__str=$userName."|".md5($userPwd);
		if($autologin=='y'){
			
			setcookie("locationAutonLoginJKJ",$__str,time()+86400*30);
		}else{
			//删除COOKIE
			setcookie("locationAutonLoginJKJ",$__str,time()-36000);
		}
		//写入session
		ExSession::getSession()->setUserByArray($_user);
		$_user["U_PWD"]=$userPwd;
		$_user["U_PWD1"]=$userPwd;
		ExSession::getSession()->setSessionByArrayAndPid($_user,$sid);
		/*
		$arrUser=array();
		$arrUser["U_NAME"]=$_user["U_NAME"];
		$arrUser["U_MOBILE"]=$_user["U_MOBILE"];
		$arrUser["U_PHONE"]=$_user["U_PHONE"];
		$arrUser["U_EMAIL"]=$_user["U_EMAIL"];
		$arrUser["U_PWD"]=$userPwd;
		$arrUser["U_PWD1"]=$userPwd;
		$u_id=$_user["U_ID"];
		$address=D("Address");
   		$ad=array();
   		$ad["U_ID"]=$u_id;
   		$addr=$address->where($ad)->select();
   		if($addr){
   			$ar_id=$addr[0]["AR_ID"];
   			if($ar_id){
	   			$area=ExUse::getArea($ar_id);
				if(is_array($area) && count($area)){
					$arrUser["AR_L1"]=$area[0];
					$arrUser["AR_L2"]=$area[1];
					$arrUser["AR_L3"]=$area[2];
				}
   				
   			}
   		}
		$_SESSION[$sid]=$arrUser;
		*/
		//echo self::USER_LOGIN_OK;
		ExLog::addLog($userName."前台登录", "USER_LOGIN");
		
		
		$arr=array("info"=>'',"status"=>self::USER_LOGIN_OK);
		echo json_encode($arr);
		exit;
	}
	/**
	 * 服务队角色登录
	 * Enter description here ...
	 */
	function srvlogin(){
		//取菜单
	   	$menus=getMenu('SRVTEAM');
	   	$this->assign('menu',$menus);
	   	$url="?g=Home&m=Order&a=srvlist";
	   	$this->assign('returnURL',$url);
	   	
		//判断是否自动登录
	   	if($_COOKIE["locationAutonLoginJKJ"]){
	   		
	   		$__userinfo=explode('|', $_COOKIE["locationAutonLoginJKJ"]);
	   		$rev=ExUse::setUser($__userinfo[0],$__userinfo[1]);
	   		if($rev){
	   			$this->redirect("?g=Home&m=Index&a=viewbg");
	   		}
	   	}
	   
	   	if($_COOKIE["locationUserJKJ"]){
	   		$__s=$_COOKIE["locationUserJKJ"];
	   		$this->assign("LOCATIONUSER",$__s);
	   	}
		$this->display();
	}
	/**
	 * 用户信息  
	 * Enter description here ...
	 */
	function uinfo(){
		
		$uid=$this->in("uid");
		if(!$uid){
			$uid=ExSession::getSession()->getUserId();
		}
		if(!$uid){
			redirect(C("NOT_LOGIN_RETURN_URL"));
			exit;
		}
		if($this->in("op")){
			$data=$this->in("data");
			//邮箱改变   那么判断邮箱是否已经存在
			if($data["OLD_U_EMAIL"]!=$data["U_EMAIL"]){
				//检查是否邮箱登录
				$user=D("User");
				$search=array();
				$search["U_EMAIL"]=$data["U_EMAIL"];
				$__data=$user->where($search)->select();
				if($__data){
					//邮箱已经存在
					echo "E";
					exit;
				}
			}
			//手机改变  判断手机是否存在
			if($data["OLD_U_MOBILE"]!=$data["U_MOBILE"]){
				
				$user=D("User");
				$search=array();
				$search["U_MOBILE"]=$data["U_MOBILE"];
				$__data=$user->where($search)->select();
				if($__data){
					//手机已经存在
					echo "M";
					exit;
				}
			}
			
			$user=D("User");
			$user->find($uid);
			$user->U_NAME=$data["U_EMAIL"];
			$user->U_RNAME=$data["U_RNAME"];
			$user->U_EMAIL=$data["U_EMAIL"];
			$user->U_MOBILE=$data["U_MOBILE"];
			$user->U_PHONE=$data["U_PHONE"];
			$user->UPDATED_AT=time();
			$area=D("Area");
   			$_as=array();
   			$_as["AR_L1"]=$data["AR_L1"];
   			$_as["AR_L2"]=$data["AR_L2"];
   			$_as["AR_L3"]=$data["AR_L3"];
   			$cArea=$area->where($_as)->select();
   			$ar_id='';
   			if($cArea){
   				$ar_id=$cArea[0]["AR_ID"];
   			}
   			//写到地址表中
   			$_revAddr=false;
   			if($ar_id && $uid){
   				/*$address=D("Address");
   				$ad=array();
   				$ad["U_ID"]=$uid;
   				$ad["AR_ID"]=$ar_id;
   				$addr=$address->where($ad)->select();
   				if($addr){
   					$ID=$addr[0]["ID"];
   					$address->find($ID);
   					$address->U_ADDRESS=$data["U_ADDRESS"];
   					$address->U_PHONE=$data["U_PHONE"];
   					$_revAddr=$address->save();
   				}else{
   					$ads["U_ID"]=$uid;
   					$ads["AR_ID"]=$ar_id;
   					$ads["U_ADDRESS"]=$data["U_ADDRESS"];
   					$ads["U_PHONE"]=$data["U_PHONE"];
   					$_revAddr=$address->add($ads);
   				}
   				*/
   				$address=D("Address");
   				$ad=array();
   				$ad["U_ID"]=$uid;
   				$addr=$address->where($ad)->delete();
   				if($addr){
   					$ads["U_ID"]=$uid;
   					$ads["AR_ID"]=$ar_id;
   					$ads["U_ADDRESS"]=$data["U_ADDRESS"];
   					$ads["U_PHONE"]=$data["U_PHONE"];
   					$_revAddr=$address->add($ads);
   				}
   			}
			
			$rev=$user->save();
			if($rev || $_revAddr){
				if(ExSession::getSession()->getRoleShortname()){
					echo ExSession::getSession()->getRoleShortname();
				}else{
					echo "CR";
				}
				
			}else{
				echo "n";
			}
			exit;
			
		}
		$this->assign("uid",$uid);
		$user=D("User");
		$search["U_ID"]=$uid;
		$data=$user->where($search)->select();
		$__info=$data[0];
		$this->assign('menu',getMenu());
		//得到地区
        $area=getAreaFiled(array(),'AR_L1');
        $this->assign('AR_L1',$area);
        //取用户自己的地址 (最新的)
       
        $ARID=ExUse::getUserAreaToArray($uid);
        if($ARID){
        	$areas=ExUse::getArea($ARID["AR_ID"]);
        	if(is_array($areas) && count($areas)){
        		$__info["AR_L1"]=$areas[0];
        		$__info["AR_L2"]=$areas[1];
        		$__info["AR_L3"]=$areas[2];
        	}
        	$__info["U_ADDRESS"]=$ARID["U_ADDRESS"];
        }
		$this->assign("userInfo",$__info);
		$this->display();
	}
	/**
	 * 用户信息  (密码/其他)
	 * Enter description here ...
	 */
	function upwd(){
		$uid=$this->in("uid");
		if(!$uid){
			$uid=ExSession::getSession()->getUserId();
		}
		if(!$uid){
			redirect(C("NOT_LOGIN_RETURN_URL"));
			exit;
		}
		if($this->in("op")){
			$data=$this->in("data");
			$user=D("User");
			$user->find($uid);
			$_pwd=$user->U_PWD;
			if(md5($data["U_PWD_OLD"])!=$_pwd){
				echo "p";
				exit;
			}
			$user->U_PWD=md5($data["U_PWD"]);
			$rev=$user->save();
			if($rev){
				echo ExSession::getSession()->getRoleShortname();
			}else{
				echo "n";
			}
			exit;
			
		}
		$this->assign("uid",$uid);
		
		$this->assign('menu',getMenu());
		$this->display();
	}
	/**
	 * 检测验证码是否正确
	 * Enter description here ...
	 */
	function checkYzm(){
		$param=$this->in('param');
		if(strtolower($_SESSION["userLoginAuthnum"])!=strtolower($param)){
			$rev="验证码输入错误";
		}else{
			$rev='y';
		}
		echo $rev;
		exit;
	}
	/**
	 * 找回密码
	 * Enter description here ...
	 */
	function getPwd(){
		if($this->in("op")){
			$U_NAME=$this->in("U_NAME");
			$bz='';
			$user=D("User");
			//会员名/邮箱  
			$IS_USER_OK=false;
			$user=D("User");
			$search=array();
			$search["U_EMAIL"]=$U_NAME;
			$data=$user->where($search)->select();
			if($data){
				$IS_USER_OK=true;
				$bz="email";
			}else{
				//检查是否手机登录
				$user=D("User");
				$search=array();
				$search["U_MOBILE"]=$U_NAME;
				$data=$user->where($search)->select();
				if($data){
					$IS_USER_OK=true;
					$bz="phone";
				}
			}
			$__out='n';
			if($IS_USER_OK){
				//生成随机密码
				$__pwd=mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9); 
				$uid=$data[0]["U_ID"];
				$user=D("User");
				$user->find($uid);
				$user->U_PWD=md5($__pwd);
				$rev=$user->save();
				if($rev){
					$__content=C("GETPWD_EMAIL");
					$t=sprintf($__content,$__pwd);
					if($bz=="email"){
						Mail::getMail()->sendMail($U_NAME,'密码修改',$t);
					}
					if($bz=="phone"){
						Mobile::getMobile()->send($U_NAME,$t);
					}
					$__out='y';
				}
			}
			echo $__out;
			exit;
		}
		$this->assign('menu',getMenu());
		$this->display();
		
	}
}