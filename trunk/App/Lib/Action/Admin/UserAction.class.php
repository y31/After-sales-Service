<?php
/**
 * 用户管理类
 * Enter description here ...
 * @author df
 *
 */
class UserAction extends BaseAction{
	
	/**
	 * 用户列表
	 * Enter description here ...
	 */
	function uList(){
		
		$this->assign('title','用户列表');
		//每页显示多少个
		$pageSize=20;
		//检索条件
		$search=array();
		if($this->in("U_NAME")){
			$this->assign('U_NAME',$this->in("U_NAME"));
			$search["U_NAME"]=array("like","%".$this->in("U_NAME")."%");
		}
		if($this->in("U_RNAME")){
			$this->assign('U_RNAME',$this->in("U_RNAME"));
			$search["U_RNAME"]=array("like","%".$this->in("U_RNAME")."%");
		}
		$search["IS_DEL"]=0;
		$user=D('User');
		if($_GET['p']){
			if(count($search)){
				$list=$user->where($search)->page($_GET['p'].','.$pageSize)->select();
			}else{
				$list=$user->page($_GET['p'].','.$pageSize)->select();
			}
			
		}else{
			if(count($search)){
				$list=$user->where($search)->limit('0,'.$pageSize)->select();
			}else{
				$list=$user->limit('0,'.$pageSize)->select();
			}
		}
		//page使用的总数
		if(count($search)){
			//$pageList=$user->where($search)->select();
			$user1=D('User');
			$pageList=$user1->where($search)->count('U_ID');
		}
		else{
			//$pageList=$user->select();
			$user1=D('User');
			$pageList=$user1->count('U_ID');
		}
		//$list=$user->page($_GET['p'].','.$pageSize)->select();
		$revList=array();
		
		//取用户角色
		if($list){
			if(is_array($list) && count($list)){
				foreach ($list as $val){
					$uid=$val["U_ID"];
					$roles=ExUse::getUserRoles($uid);
					if(count($roles)){
						$val["u_role_list"]=implode(',', $roles);
					}else{
						$val["u_role_list"]='';
					}
					$revList[]=$val;
					
				}
			}
		}
		
		$this->assign('userList',$revList);
		import('ORG.Util.Page');
		//$count      = $user->count();// 查询满足要求的总记录数
		//$count      = count($pageList);// 查询满足要求的总记录数
		$count      = $pageList;// 查询满足要求的总记录数
		$Page       = new Page($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		
		$this->display('uList');
	}
	/**
	 * 添加用户
	 * Enter description here ...
	 */
	function add(){
		
		$data=$this->in("data");
		if($data){
			$rev1="n";
			$info='用户添加失败';
			$user=D("User");
			$pwd=$data["U_PWD"];
			$data["U_PWD"]=md5($pwd);
			$data["CREATED_AT"]=time();
			$data["UPDATED_AT"]=time();
			$data["U_CREATER"]=ExSession::getSession()->getUserId();
			$data["U_GU_ID"]=getGuId();
			$rev=$user->add($data);
			if($rev){
				$s_role=$this->in("role");
				$uRole=D("User_role");
				if($s_role){
					foreach ($s_role as $r){
						$search['U_ID']=$rev;
						$search['R_ID']=$r;
						$isC=$uRole->where($search)->select();
						if(!$isC){
							$uRole->add($search);
						}
					}
				}
				//echo "y";
				$rev1='y';
				$info='用户添加成功';
			}
			$arr=array("info"=>$info,"status"=>$rev1);
			echo json_encode($arr);
			exit;
		}
		$roles=ExUse::getRoleAll();
		$this->assign('roles',$roles);
		$this->assign("action","?g=Admin&m=User&a=add");
		
		$this->display();
	}
	/**
	 * 编辑用户
	 * Enter description here ...
	 */
	function edit(){
		
		$data=$this->in("data");
		$u_id=$this->in("uid");
		if(!$u_id){
			$u_id=ExSession::getSession()->getUserId();
		}
		if($data){
			$rev1="n";
			$info='用户编辑失败';
			$user=D("User");
			$user->find($data['U_ID']);
			$user->U_RNAME=$data["U_RNAME"];
			//判断手机号是否改变 如果改变判断是否重复
			if($user->U_MOBILE!=$data["U_MOBILE"]){
				$user1=D("User");
				
				$_s=array();
		   		$_s["U_MOBILE"]=$data["U_MOBILE"];
		   		$uData=$user1->where($_s)->count('U_ID');
		   		if($uData){
		   			$rev1='n';
					$info='手机号码重复';
					$arr=array("info"=>$info,"status"=>$rev1);
					echo json_encode($arr);
					exit;
		   		}
			}
			
			//判断邮箱是否改变 如果改变判断是否重复
			if($user->U_EMAIL!=$data["U_EMAIL"]){
				$user2=D("User");
				
				$_s=array();
		   		$_s["U_EMAIL"]=$data["U_EMAIL"];
		   		$uData=$user2->where($_s)->count('U_ID');
		   		if($uData){
		   			$rev1='n';
					$info='邮箱重复';
					$arr=array("info"=>$info,"status"=>$rev1);
					echo json_encode($arr);
					exit;
		   		}
			}
			
			$user->U_MOBILE=$data["U_MOBILE"];
			$user->U_EMAIL=$data["U_EMAIL"];
			$user->U_PHONE=$data["U_PHONE"];
			$user->UPDATED_AT=time();
			$rev=$user->save();
			if($rev){
				$s_role=$this->in("role");
				$s['U_ID']=$data['U_ID'];
				$uRole=D("User_role");
				//先删除再添加
				$uRole->where($s)->delete();
				if($s_role){
					foreach ($s_role as $r){
						$search['U_ID']=$data['U_ID'];
						$search['R_ID']=$r;
						$isC=$uRole->where($search)->select();
						if(!$isC){
							$uRole->add($search);
						}
					}
				}
				//echo "y";
				$rev1='y';
				$info='用户编辑成功';
			}
			$arr=array("info"=>$info,"status"=>$rev1);
			echo json_encode($arr);
			exit;
		}
		if($u_id){
			$user=D("User");
			$uData=$user->where('U_ID='.$u_id)->select();
			
			$this->assign('uData',$uData[0]);
			
			//找用户的角色
			
			$roleUser=D("User_role");
			$rs["U_ID"]=$u_id;
			$rluser=$roleUser->where($rs)->select();
			if($rluser){
				$rols=array();
				foreach ($rluser as $rl){
					$rols[$rl["R_ID"]]=$rl["R_ID"];
				}
				$this->assign('rluser',implode(',', $rols));
				//$this->assign('rluser',$rols);
			}
		}
		
		$this->assign("edit",'y');//说明是编辑
		$roles=ExUse::getRoleAll();
		$this->assign('roles',$roles);
		$this->assign("action","?g=Admin&m=User&a=edit");
		$this->display();
	}
	/**
	 * 删除用户
	 * Enter description here ...
	 */
	function del(){
		$u_id=$this->in("uid");
		
		$u_list=explode(',', $u_id);
		if(is_array($u_list) && count($u_list)){
			foreach ($u_list as $key=>$uid){
				$user=D("User");
				$user->find($uid);
				$user->IS_DEL=1;
				$user->UPDATED_AT=time();
				$rev=$user->save();
			}
		}
		
		if($rev){
			echo "y";
		}else{
			echo "n";
		}
		exit;
	}
	/**
	 * 停用用户
	 * Enter description here ...
	 */
	function lock(){
		$u_id=$this->in("uid");
		$lock=$this->in('lock');
		if(!$lock){
			$lock=0;
		}
		$u_list=explode(',', $u_id);
		if(is_array($u_list) && count($u_list)){
			foreach ($u_list as $key=>$uid){
				$user=D("User");
				$user->find($uid);
				$user->IS_BLOCK=$lock;
				$user->UPDATED_AT=time();
				$rev=$user->save();
			}
		}
		
		if($rev){
			echo "y";
		}else{
			echo "n";
		}
		exit;
	}
	/**
	 * 查询用户使用的列表
	 * Enter description here ...
	 */
	function slist(){
		$type=$this->in('t');//查询类型     t=c //客户   t=u //用户  默认是用户
		$f1=$this->in("f1");//字段1  存放ID
		$f2=$this->in("f2");//字段2 存放name
		$role=$this->in('role');
		
		$this->assign('f1',$f1);
		$this->assign('f2',$f2);
		//每页显示多少个
		$pageSize=10;
		$m=M();
		if(!$type){
			$type='u';
		}
		if($type=='c'){
			$sql="select as_user.U_ID,U_NAME,U_RNAME,U_EMAIL,U_MOBILE from as_customer
			inner join as_user on as_customer.U_ID=as_user.U_ID
			where as_customer.IS_DEL=0 and as_customer.IS_BLOCK=0";
		}else{
			if($role){
				$sql="select as_user.U_ID,as_user.U_NAME,as_user.U_RNAME,as_user.U_EMAIL,as_user.U_MOBILE from  as_user
				inner join as_user_role on as_user_role.U_ID=as_user.U_ID
				where as_user.IS_DEL=0 and as_user.IS_BLOCK=0 AND as_user_role.R_ID in (".$role.")";
			}else{
				$sql="select U_ID,U_NAME,U_RNAME,U_EMAIL,U_MOBILE from as_user where IS_DEL=0 and IS_BLOCK=0";
			}
		}
		if($this->in("U_NAME")){
			$this->assign('U_NAME',$this->in("U_NAME"));
			$sql.=" and U_NAME like '%".$this->in("U_NAME")."%' ";
		}
		if($this->in("U_RNAME")){
			$this->assign('U_RNAME',$this->in("U_RNAME"));
			$sql.=" and U_RNAME like '%".$this->in("U_RNAME")."%' ";
		}
		$__COUNT="select COUNT(U_ID) as count FROM (".$sql.") as _user";
		
		$list=$m->query($__COUNT);//使用总数
		/*if($role){
			$list=ExUse::filterDataByRole($list,$role);
		}*/
		if($_GET['p']){
			$p=$_GET['p'];
			$s=($p-1)*$pageSize;
			$sql.=" limit {$s},{$pageSize}";
		}
		else{
			$sql.=" limit 0,{$pageSize}";
		}
		
		$data=$m->query($sql);
		/*
		if($role){
			$data=ExUse::filterDataByRole($data,$role);
		}*/
		$this->assign("userList",$data);
		import('ORG.Util.Page');
		//$count      = count($list);// 查询满足要求的总记录数
		$count      = $list[0]["count"];// 查询满足要求的总记录数
		$Page       = new Page($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	/**
	 * 用户修改密码
	 * Enter description here ...
	 */
	function changepwd(){
		$u_id=$this->in("uid");
		if(!$u_id){
			$u_id=ExSession::getSession()->getUserId();
		}
		if($this->in("op")){
			$data=$this->in("data");
			$user=D("User");
			$user->find($u_id);
			$_pwd=$user->U_PWD;
			if(md5($data["U_PWD_OLD"])!=$_pwd){
				echo "p";
				exit;
			}
			$user->U_PWD=md5($data["U_PWD"]);
			$rev=$user->save();
			if($rev){
				echo 'y';
			}else{
				echo "n";
			}
			exit;
		}
		$this->assign("uid",$u_id);
		$this->display();
	}
	
	/**
	 * 客户列表
	 * Enter description here ...
	 */
	function cList(){
		
		//每页显示多少个
		$pageSize=20;
		$m=M();
		if(!$type){
			$type='u';
		}
		$sql="select as_user.U_ID,U_NAME,U_RNAME,U_EMAIL,U_MOBILE,as_user.IS_BLOCK from as_customer
			inner join as_user on as_customer.U_ID=as_user.U_ID
			where as_user.IS_DEL=0 ";
		if($this->in("U_NAME")){
			$this->assign('U_NAME',$this->in("U_NAME"));
			$sql.=" and U_NAME like '%".$this->in("U_NAME")."%' ";
		}
		if($this->in("U_RNAME")){
			$this->assign('U_RNAME',$this->in("U_RNAME"));
			$sql.=" and U_RNAME like '%".$this->in("U_RNAME")."%' ";
		}
		
		$__COUNT="select COUNT(U_ID) as count FROM (".$sql.") as _user";
		
		$list=$m->query($__COUNT);//使用总数
		/*if($role){
			$list=ExUse::filterDataByRole($list,$role);
		}*/
		if($_GET['p']){
			$p=$_GET['p'];
			$s=($p-1)*$pageSize;
			$sql.=" limit {$s},{$pageSize}";
		}
		else{
			$sql.=" limit 0,{$pageSize}";
		}
		
		$data=$m->query($sql);
		/*
		if($role){
			$data=ExUse::filterDataByRole($data,$role);
		}*/
		$this->assign("userList",$data);
		import('ORG.Util.Page');
		//$count      = count($list);// 查询满足要求的总记录数
		$count      = $list[0]["count"];// 查询满足要求的总记录数
		$Page       = new Page($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	/**
	 * 编辑客户信息
	 * Enter description here ...
	 */
	function cedit(){
		$data=$this->in("data");
		$u_id=$this->in("uid");
		
		if($data){
			$rev="n";
			$info='客户编辑失败';
			$user=D("User");
			$user->find($data['U_ID']);
			$user->U_RNAME=$data["U_RNAME"];
			
			if($user->U_NAME!=$data["U_NAME"]){
				$user3=D("User");
				
				$_s=array();
		   		$_s["U_NAME"]=$data["U_NAME"];
		   		$uData=$user3->where($_s)->count('U_ID');
		   		if($uData){
		   			$rev1='n';
					$info='会员名重复';
					$arr=array("info"=>$info,"status"=>$rev1);
					echo json_encode($arr);
					exit;
		   		}
			}
			
			$user->U_NAME=$data["U_NAME"];
			
			//判断手机号是否改变 如果改变判断是否重复
			if($user->U_MOBILE!=$data["U_MOBILE"]){
				$user1=D("User");
				
				$_s=array();
		   		$_s["U_MOBILE"]=$data["U_MOBILE"];
		   		$uData=$user1->where($_s)->count('U_ID');
		   		if($uData){
		   			$rev1='n';
					$info='手机号码重复';
					$arr=array("info"=>$info,"status"=>$rev1);
					echo json_encode($arr);
					exit;
		   		}
			}
			
			//判断邮箱是否改变 如果改变判断是否重复
			if($user->U_EMAIL!=$data["U_EMAIL"]){
				$user2=D("User");
				
				$_s=array();
		   		$_s["U_EMAIL"]=$data["U_EMAIL"];
		   		$uData=$user2->where($_s)->count('U_ID');
		   		if($uData){
		   			$rev1='n';
					$info='邮箱重复';
					$arr=array("info"=>$info,"status"=>$rev1);
					echo json_encode($arr);
					exit;
		   		}
			}
			
			$user->U_MOBILE=$data["U_MOBILE"];
			$user->U_EMAIL=$data["U_EMAIL"];
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
   			if($ar_id && $u_id){
   				$address=D("Address");
   				//删除再添加吧
   				$ad=array();
   				$ad["U_ID"]=$u_id;
   				//$ad["AR_ID"]=$ar_id;
   				$addr=$address->where($ad)->delete();
   				$ads=array();
   					$address1=D("Address");
   					$ads["U_ID"]=$u_id;
   					$ads["AR_ID"]=$ar_id;
   					$ads["U_ADDRESS"]=$data["U_ADDRESS"];
   					$ads["U_PHONE"]=$data["U_PHONE"];
   					$_revAddr=$address1->add($ads);
   			}
			$rev=$user->save();
			if($rev || $_revAddr){
				$rev="y";
				$info='客户编辑成功';
			}
			$arr=array("info"=>$info,"status"=>$rev);
			echo json_encode($arr);
			exit;
		}
		if($u_id){
			$user=D("User");
			$uData=$user->where('U_ID='.$u_id)->select();
			
			$this->assign('uData',$uData[0]);
			
			//得到地区
	        $area=getAreaFiled(array(),'AR_L1');
	        $this->assign('AR_L1',$area);
	        //取用户自己的地址 (最新的)
	       
	        $ARIDs=ExUse::getUserAreaToArray($u_id);
	       
	        if(is_array($ARIDs)&& count($ARIDs)) {
	        	$areas=ExUse::getArea($ARIDs["AR_ID"]);
	        	if(is_array($areas) && count($areas)){
	        		$__info["AR_L1"]=$areas[0];
	        		$__info["AR_L2"]=$areas[1];
	        		$__info["AR_L3"]=$areas[2];
	        	}
	        	$__info["U_ADDRESS"]=$ARIDs["U_ADDRESS"];
	        }
			$this->assign("userInfo",$__info);
		}
		$this->assign("uid",$u_id);
		$this->assign("edit",'y');//说明是编辑
		$roles=ExUse::getRoleAll();
		$this->assign('roles',$roles);
		$this->assign('action','?g=Admin&m=User&a=cedit');
		
		$this->display();
	}
	function cadd(){
		$data=$this->in("data");
		if($data){
			$rev="n";
			$info='客户添加失败';
			$user=D("User");
			$search=array();
			$search["U_NAME"]=$data["U_NAME"];
			$search["U_RNAME"]=$data["U_RNAME"];
			$search["U_PWD"]=md5($data["U_PWD"]);
			$search["U_MOBILE"]=$data["U_MOBILE"];
			$search["U_EMAIL"]=$data["U_EMAIL"];
			$search["U_PHONE"]=$data["U_PHONE"];
			$search["CREATED_AT"]=time();
   			$search["UPDATED_AT"]=time();
   			$search["U_WRONG_TIMES"]=time();
   			$search["U_LOG_TIMES"]=time();
   			$search["G_ID"]=1;
   			$data["U_CREATER"]=ExSession::getSession()->getUserId();
			$data["U_GU_ID"]=getGuId();
   			$u_id=$user->add($search);
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
   			if($ar_id && $u_id){
   				$address=D("Address");
   				$ads=array();
   				$ads["U_ID"]=$u_id;
   				$ads["AR_ID"]=$ar_id;
   				$ads["U_ADDRESS"]=$data["U_ADDRESS"];
   				$ads["U_PHONE"]=$data["U_PHONE"];
   				$_revAddr=$address->add($ads);
   			}
			
			if($u_id){
   				$Customer=D("Customer");
   				$cData["U_ID"]=$u_id;
   				$cData["CS_REG_TIME"]=time();
   				$Customer->add($cData);
   				
				$uRole=D("User_role");
				$search=array();
	   			$search['U_ID']=$u_id;
				$search['R_ID']=4;
				$uRole->add($search);
   			}
   			
			if($u_id){
				$rev="y";
				$info='客户编辑成功';
			}
			$arr=array("info"=>$info,"status"=>$rev);
			echo json_encode($arr);
			exit;
		}
		$this->assign('action','?g=Admin&m=User&a=cadd');
		//得到地区
        $area=getAreaFiled(array(),'AR_L1');
        $this->assign('AR_L1',$area);
		$this->display();
	}
	function checkUInfo(){
		$param=$this->in('param');
		$name=$this->in('name');
		if($param && $name){
			$role=D("User");
			$search=array();
			if($name=='data[U_MOBILE]'){
				$search['U_MOBILE']=$param;
			}else if($name=="data[U_EMAIL]"){
				$search['U_EMAIL']=$param;
			}else if($name=="data[U_NAME]"){
				$search['U_NAME']=$param;
			}
			$data=$role->where($search)->count();
			if($data){
				if($name=='data[U_MOBILE]'){
					$rev="手机号码已存在";
				}else if($name=="data[U_EMAIL]"){
					$rev="邮箱已存在";
				}else if($name=="data[U_NAME]"){
					$rev="会员名已存在";
				}
				echo $rev;
			}else{
				echo "y";
			}
		}else{
			echo "必填项不能为空";
		}
		exit;
	}
	
	
	/**
	 * 查询用户使用的列表
	 * Enter description here ...
	 */
	function cslist(){
		$type=$this->in('t');//查询类型     t=c //客户   t=u //用户  默认是用户
		$role=$this->in('role');
		
		$this->assign('f1',$f1);
		$this->assign('f2',$f2);
		//每页显示多少个
		$pageSize=10;
		$m=M();
		if(!$type){
			$type='u';
		}
		if($type=='c'){
			$sql="select as_user.U_ID,U_NAME,U_RNAME,U_EMAIL,U_MOBILE from as_customer
			inner join as_user on as_customer.U_ID=as_user.U_ID
			where as_customer.IS_DEL=0 and as_customer.IS_BLOCK=0";
		}else{
			if($role){
				$sql="select as_user.U_ID,as_user.U_NAME,as_user.U_RNAME,as_user.U_EMAIL,as_user.U_MOBILE from  as_user
				inner join as_user_role on as_user_role.U_ID=as_user.U_ID
				where as_user.IS_DEL=0 and as_user.IS_BLOCK=0 AND as_user_role.R_ID in (".$role.")";
			}else{
				$sql="select U_ID,U_NAME,U_RNAME,U_EMAIL,U_MOBILE from as_user where IS_DEL=0 and IS_BLOCK=0";
			}
		}
		if($this->in("U_NAME")){
			$this->assign('U_NAME',$this->in("U_NAME"));
			$sql.=" and U_NAME like '%".$this->in("U_NAME")."%' ";
		}
		if($this->in("U_RNAME")){
			$this->assign('U_RNAME',$this->in("U_RNAME"));
			$sql.=" and U_RNAME like '%".$this->in("U_RNAME")."%' ";
		}
		$__COUNT="select COUNT(U_ID) as count FROM (".$sql.") as _user";
		
		$list=$m->query($__COUNT);//使用总数
		/*if($role){
			$list=ExUse::filterDataByRole($list,$role);
		}*/
		if($_GET['p']){
			$p=$_GET['p'];
			$s=($p-1)*$pageSize;
			$sql.=" limit {$s},{$pageSize}";
		}
		else{
			$sql.=" limit 0,{$pageSize}";
		}
		
		$data=$m->query($sql);
		/*
		if($role){
			$data=ExUse::filterDataByRole($data,$role);
		}*/
		$this->assign("userList",$data);
		import('ORG.Util.Page');
		//$count      = count($list);// 查询满足要求的总记录数
		$count      = $list[0]["count"];// 查询满足要求的总记录数
		$Page       = new Page($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	/**
	 * 发送短信
	 * Enter description here ...
	 */
	function sendsms(){
		$uid=$this->in("uid");
		if($this->in("op")){
			$rev1="n";
			$info='发送短信失败';
			$O_CONTENT=$this->in("O_CONTENT");
			$mobile=$this->in("mobile");
			if($mobile){
				$rev=Mobile::getMobile()->send($mobile,$O_CONTENT);
				//$rev=true;
				if($rev){
					$rev1="y";
					$info='发送短信成功';
				}
			}
			
			$arr=array("info"=>$info,"status"=>$rev1);
			echo json_encode($arr);
			exit;
		}
		$mobiles=array();
		if($uid){
			$__list=explode(",", $uid);
			if(is_array($__list) && count($__list)){
				foreach ($__list as $v){
					$user=D("User");
					$user->find($v);
					$mobiles[]=$user->U_MOBILE;
				}
			}
		}
		$__mobile='';
		if(count($mobiles)){
			$mobiles=array_unique($mobiles);
			$__mobile=implode(',', $mobiles);
		}
		$this->assign("mobile",$__mobile);
		$this->assign("uid",$uid);
		$this->display();
	}
	
}