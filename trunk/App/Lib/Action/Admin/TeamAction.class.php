<?php
/**
 * 服务队
 * Enter description here ...
 * @author admin
 *
 */
class TeamAction extends BaseAction{
	
	/**
	 * 添加服务队
	 * Enter description here ...
	 */
	function add(){
		$this->assign("title","开通服务队");
		$data=$this->in("data");
		if($data){
			$srvTeam=D("Srv_team");
			$data["CREATED_AT"]=time();
			$data["UPDATED_AT"]=time();
			$ST_AR_ID=ExUse::getArId($data["AR_L1"], $data["AR_L2"], $data["AR_L3"]);
			if($ST_AR_ID){
				$data["ST_AR_ID"]=$ST_AR_ID;
			}
			$rev=$srvTeam->add($data);
			if($rev){
				
				$srv_area=$this->in("srv_area");
				if($srv_area){
					$srv=explode('|', $srv_area);
					foreach ($srv as $sv){
						$_srv=explode(',', $sv);
						$AR_ID=ExUse::getArId($_srv[0], $_srv[1], $_srv[2]);
						if($AR_ID){
							$srv_team_areas=D("Srv_team_areas");
							$sta["ST_ID"]=$rev;
							$sta["AR_ID"]=$AR_ID;
							$sta["RP_U_ID"]=$data["RP_U_ID"];
							$sta["CREATED_AT"]=time();
							$srv_team_areas->add($sta);
						}
						
					}
				}
				
				
				$srv_team_members=D("Srv_team_members");
				$stm["U_ID"]=$data["RP_U_ID"];
				$stm["ST_ID"]=$rev;
				$stm["SRV_REG_TIME"]=time();
				$stm["CREATED_AT"]=time();
				$stm["UPDATED_AT"]=time();
				$srv_team_members->add($stm);
				echo "y";
			}else{
				echo "n";
			}
			exit;
		}
		
		//得到地区
       	$area=getAreaFiled(array(),'AR_L1');
       	$this->assign('AR_L1',$area);
		$this->display();
	}
	/**
	 * 编辑服务队
	 * Enter description here ...
	 */
	function edit(){
		$this->assign("title","编辑服务队");
		$st_id=$this->in("st_id");
		$data=$this->in("data");
		if($data){
			$srvTeam=D("Srv_team");
			$srvTeam->find($st_id);
			$srvTeam->UPDATED_AT=time();
			$ST_AR_ID=ExUse::getArId($data["AR_L1"], $data["AR_L2"], $data["AR_L3"]);
			if($ST_AR_ID){
				$srvTeam->ST_AR_ID=$ST_AR_ID;
			}
			$srvTeam->ST_NAME=$data["ST_NAME"];
			$srvTeam->RP_U_ID=$data["RP_U_ID"];
			$srvTeam->ST_ADDRESS=$data["ST_ADDRESS"];
			$srvTeam->ST_POSTCODE=$data["ST_POSTCODE"];
			$srvTeam->RP_U_RNAME=$data["RP_U_RNAME"];
			$srvTeam->ST_PHONE=$data["ST_PHONE"];
			$srvTeam->ST_MOBILE=$data["ST_MOBILE"];
			$srvTeam->ST_EMAIL=$data["ST_EMAIL"];
			$srvTeam->ST_BANK_NAME=$data["ST_BANK_NAME"];
			$srvTeam->ST_ACCOUNT_NAME=$data["ST_ACCOUNT_NAME"];
			$srvTeam->ST_ACCOUNT=$data["ST_ACCOUNT"];
			$rev=$srvTeam->save();
			
			$srv_area=$this->in("srv_area");
			if($srv_area){
				//先删除原先存在的地区
				$srv_team_areas=D("Srv_team_areas");
				$sd["ST_ID"]=$st_id;
				$srv_team_areas->where($sd)->delete();
				$srv=explode('|', $srv_area);
				foreach ($srv as $sv){
					$_srv=explode(',', $sv);
					$AR_ID=ExUse::getArId($_srv[0], $_srv[1], $_srv[2]);
					if($AR_ID){
						$srv_team_areas=D("Srv_team_areas");
						$sta["ST_ID"]=$st_id;
						$sta["AR_ID"]=$AR_ID;
						$sta["RP_U_ID"]=$data["RP_U_ID"];
						$sta["CREATED_AT"]=time();
						$srv_team_areas->add($sta);
					}
				}
			}
			//检查负责人是否改变
			$srv_team_members=D("Srv_team_members");
			$stms["ST_ID"]=$st_id;
			$stmds=$srv_team_members->where($stms)->select();
			if($stmds){
				$uids=array();
				foreach ($stmds as $v1){
					$uids[]=$v1["U_ID"];
				}
				if(!in_array($data["RP_U_ID"], $uids)){
					$stm["U_ID"]=$data["RP_U_ID"];
					$stm["ST_ID"]=$st_id;
					$stm["SRV_REG_TIME"]=time();
					$stm["CREATED_AT"]=time();
					$stm["UPDATED_AT"]=time();
					$srv_team_members->add($stm);
				}
			}else{
				$stm["U_ID"]=$data["RP_U_ID"];
				$stm["ST_ID"]=$st_id;
				$stm["SRV_REG_TIME"]=time();
				$stm["CREATED_AT"]=time();
				$stm["UPDATED_AT"]=time();
				$srv_team_members->add($stm);
			}
			echo "y";
			exit;
		}
		
		$srvTeam=D("Srv_team");
		$search["ST_ID"]=$st_id;
		$ds=$srvTeam->where($search)->select();
		if($ds){
			$uData=$ds[0];
			$RP_U_ID=$uData["RP_U_ID"];
			$user=D("User");
			$user->find($RP_U_ID);
			$uName=$user->U_RNAME;
			$uData["RP_U"]=$uName;
			$ar_id=$uData["ST_AR_ID"];
			$ARS=ExUse::getArea($ar_id);
			if($ARS){
				$uData["AR_L1"]=$ARS[0];
				$uData["AR_L2"]=$ARS[1];
				$uData["AR_L3"]=$ARS[2];
			}
			$this->assign("uData",$uData);
			//取服务队服务地区
			$sArea=D("Srv_team_areas");
			$ss["ST_ID"]=$st_id;
			$sds=$sArea->where($ss)->select();
			$slist=array();
			if($sds){
				foreach ($sds as $v){
					$_id=$v["AR_ID"];
					$_ars=ExUse::getArea($_id);
					if($_ars){
						$slist[]=implode(',', $_ars);
					}
				}
			}
			if(count($slist)){
				$this->assign('sList',$slist);
				$this->assign("sText",implode('|', $slist));
			}
			
		}
		//得到地区
       	$area=getAreaFiled(array(),'AR_L1');
       	$this->assign('AR_L1',$area);
		$this->display();
	}
	/**
	 * 服务队列表
	 * Enter description here ...
	 */
	function slist(){
		$this->assign('title','服务队列表');
		//每页显示多少个
		$pageSize=20;
		//检索条件
		$search=array();
		if($this->in("ST_NAME")){
			$this->assign('ST_NAME',$this->in("ST_NAME"));
			$search["ST_NAME"]=array("like","%".$this->in("ST_NAME")."%");
		}
		if($this->in("RP_U_RNAME")){
			$this->assign('RP_U_RNAME',$this->in("RP_U_RNAME"));
			$search["RP_U_RNAME"]=array("like","%".$this->in("RP_U_RNAME")."%");
		}
		
		if($this->in("AR_ADDRESS")){
			$this->assign('AR_ADDRESS',$this->in("AR_ADDRESS"));
			
		}
		if($this->in("AR_ID")){
			$this->assign('AR_ID',$this->in("AR_ID"));
			$search["AR_ID"]=$this->in("AR_ID");
		}
		$search["IS_DEL"]=0;
		$srvTeam=D("Srv_team");
		if($_GET['p']){
			if(count($search)){
				$list=$srvTeam->where($search)->page($_GET['p'].','.$pageSize)->select();
			}else{
				$list=$srvTeam->page($_GET['p'].','.$pageSize)->select();
			}
			
		}else{
			if(count($search)){
				$list=$srvTeam->where($search)->limit('0,'.$pageSize)->select();
			}else{
				$list=$srvTeam->limit('0,'.$pageSize)->select();
			}
		}
		//page使用的总数
		if(count($search)){
			//$pageList=$srvTeam->where($search)->select();
			$pageList=$srvTeam->where($search)->count();
		}
		else{
			//$pageList=$srvTeam->select();
			$pageList=$srvTeam->count();
		}
		
		//$list=$user->page($_GET['p'].','.$pageSize)->select();
		$revList=array();
		
		//整理数据
		if($list){
			if(is_array($list) && count($list)){
				foreach ($list as $val){
					$arid=$val["AR_ID"];
					$ars=ExUse::getArea($arid);
					if($ars && count($ars)){
						$val["ST_AREA"]=implode('　', $ars);
					}else{
						$val["ST_AREA"]='';
					}
					$val["CREATED_AT"]=formatDate($val["CREATED_AT"]);
					$revList[]=$val;
					
				}
			}
		}
		
		$this->assign('teamList',$revList);
		import('ORG.Util.Page');
		//$count      = $user->count();// 查询满足要求的总记录数
		//$count      = count($pageList);// 查询满足要求的总记录数
		$count      = $pageList;// 查询满足要求的总记录数
		$Page       = new Page($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	/**
	 * 删除服务队
	 * Enter description here ...
	 */
	function del(){
		$st_id=$this->in("st_id");
		
		$u_list=explode(',', $st_id);
		if(is_array($u_list) && count($u_list)){
			foreach ($u_list as $key=>$uid){
				$user=D("Srv_team");
				$user->find($uid);
				$cu_id=$user->RP_U_ID;
				$user->IS_DEL=1;
				$user->UPDATED_AT=time();
				$rev=$user->save();
				if($rev){
					if($cu_id){
						$srv_team_members=D("Srv_team_members");
						$srv_team_members->find($cu_id);
						$srv_team_members->IS_DEL=1;
						$srv_team_members->UPDATED_AT=time();
						$srv_team_members->save();
					}
				}
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
	 * 停用服务队
	 * Enter description here ...
	 */
	function lock(){
		$st_id=$this->in("st_id");
		$lock=$this->in('lock');
		if(!$lock){
			$lock=0;
		}
		$u_list=explode(',', $st_id);
		if(is_array($u_list) && count($u_list)){
			foreach ($u_list as $key=>$uid){
				$user=D("Srv_team");
				$user->find($uid);
				$cu_id=$user->RP_U_ID;
				$user->IS_BLOCK=$lock;
				$user->UPDATED_AT=time();
				$rev=$user->save();
				if($rev){
					if($cu_id){
						$srv_team_members=D("Srv_team_members");
						$srv_team_members->find($cu_id);
						$srv_team_members->IS_BLOCK=$lock;
						$srv_team_members->UPDATED_AT=time();
						$srv_team_members->save();
					}
				}
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
	 * 测试服务队是否存在
	 * Enter description here ...
	 */
	function checkteam(){
		//$_POST["param"] 获取文本框的值;
		//$_POST["name"]  获取文本框的name属性值，通过该值来判断是哪个文本框请求处理，这样当有多个实时验证请求时可以指定同一个文件处理;
		
		$param=$this->in('param');
		$is=ExUse::checkSRVTeam($param);
		if($is){
			echo "服务队名称已经存在";
		}else{
			echo "y";
		}
		exit;
	}
	/**
	 * 检查服务地区是否存在
	 * Enter description here ...
	 */
	function checkSrvTeam(){
		$ar=$this->in("area");
		$a1=explode(',', $ar);
		$d=ExUse::getArId($a1[0], $a1[1], $a1[2]);
		
		if($d){
			$srv_team_areas=D("Srv_team_areas");
			$s["AR_ID"]=$d;
			$data=$srv_team_areas->where($s)->select();
			if($data){
				echo 'y';
			}else{
				echo 'n';
			}
			
		}else{
			echo  'n';
		}
		exit;
		
	}
	
/**
	 * 服务队列表(dialog使用)
	 * Enter description here ...
	 */
	function dslist(){
		$o_id=$this->in("o_id");
		//每页显示多少个
		$pageSize=10;
		//检索条件
		$search=array();
		if($this->in("ST_NAME")){
			$this->assign('ST_NAME',$this->in("ST_NAME"));
			$search["ST_NAME"]=array("like","%".$this->in("ST_NAME")."%");
		}
		if($this->in("RP_U_RNAME")){
			$this->assign('RP_U_RNAME',$this->in("RP_U_RNAME"));
			$search["RP_U_RNAME"]=array("like","%".$this->in("RP_U_RNAME")."%");
		}
		
		if($this->in("AR_ADDRESS")){
			$this->assign('AR_ADDRESS',$this->in("AR_ADDRESS"));
			
		}
		if($this->in("AR_ID")){
			$this->assign('AR_ID',$this->in("AR_ID"));
			$search["AR_ID"]=$this->in("AR_ID");
		}
		$search["IS_DEL"]=0;
		$search["IS_BLOCK"]=0;
		$srvTeam=D("Srv_team");
		if($_GET['p']){
			if(count($search)){
				$list=$srvTeam->where($search)->page($_GET['p'].','.$pageSize)->select();
			}else{
				$list=$srvTeam->page($_GET['p'].','.$pageSize)->select();
			}
			
		}else{
			if(count($search)){
				$list=$srvTeam->where($search)->limit('0,'.$pageSize)->select();
			}else{
				$list=$srvTeam->limit('0,'.$pageSize)->select();
			}
		}
		//page使用的总数
		if(count($search)){
			$pageList=$srvTeam->where($search)->select();
		}
		else{
			$pageList=$srvTeam->select();
		}
		
		//$list=$user->page($_GET['p'].','.$pageSize)->select();
		$revList=array();
		
		//整理数据
		if($list){
			if(is_array($list) && count($list)){
				foreach ($list as $val){
					$arid=$val["AR_ID"];
					$ars=ExUse::getArea($arid);
					if($ars && count($ars)){
						$val["ST_AREA"]=implode('　', $ars);
					}else{
						$val["ST_AREA"]='';
					}
					$val["CREATED_AT"]=formatDate($val["CREATED_AT"]);
					$revList[]=$val;
					
				}
			}
		}
		
		$this->assign('teamList',$revList);
		import('ORG.Util.Page');
		//$count      = $user->count();// 查询满足要求的总记录数
		$count      = count($pageList);// 查询满足要求的总记录数
		$Page       = new Page($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		
		$this->assign("o_id",$o_id);
		$this->display();
	}
}