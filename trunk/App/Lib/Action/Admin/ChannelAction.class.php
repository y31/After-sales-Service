<?php
/**
 * 渠道管理
 * Enter description here ...
 * @author admin
 *
 */
class ChannelAction extends BaseAction{
	
	/**
	 * 列表
	 * Enter description here ...
	 */
	function clist(){
		$role=D("Channel");
		//每页显示多少个
		$pageSize=20;
		
		//检索条件
		$search=array();
		if($this->in("C_NMAE")){
			$this->assign('C_NMAE',$this->in("C_NMAE"));
			$search["C_NMAE"]=array("like","%".$this->in("C_NMAE")."%");
		}
		
		if($_GET['p']){
			if(count($search)){
				$list=$role->where($search)->page($_GET['p'].','.$pageSize)->select();
			}else{
				$list=$role->page($_GET['p'].','.$pageSize)->select();
			}
			
		}else{
			if(count($search)){
				$list=$role->where($search)->limit('0,'.$pageSize)->select();
			}else{
				$list=$role->limit('0,'.$pageSize)->select();
			}
		}
		//page使用的总数
		if(count($search)){
			//$pageList=$user->where($search)->select();
			$user1=D('Channel');
			$pageList=$user1->where($search)->count('C_ID');
		}
		else{
			//$pageList=$user->select();
			$user1=D('Channel');
			$pageList=$user1->count('C_ID');
		}
		
		$this->assign('rolelist',$list);
		import('ORG.Util.Page');
		$count      = $pageList;// 查询满足要求的总记录数
		$Page       = new Page($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	/**
	 * 添加
	 * Enter description here ...
	 */
	function add(){
		if($this->in("op")){
			$C_NMAE=$this->in("C_NMAE");
			
			//检测是否存在重复
			$is_re=false;
			$rev="n";
			$__rev=false;
			if(is_array($C_NMAE) && count($C_NMAE)){
				foreach ($C_NMAE as $key=>$val){
					$area=D("Channel");
					$_s=array();
					$_s["C_NMAE"]=$val;
					
					$__count=$area->where($_s)->count('C_ID');
					if($__count>0){
						$is_re=true;
						$info="添加渠道失败,名称:".$val."已经存在！";
						break;
					}
				}
			}
			if($is_re){
				$arr=array("info"=>$info,"status"=>$rev);
				echo json_encode($arr);
				exit;
			}else{
				foreach ($C_NMAE as $key=>$val){
						$area1=D("Channel");
						$_s=array();
						$_s["C_NMAE"]=$val;
						$__rev=$area1->add($_s);
				}
			}
			if($__rev){
				$rev='y';
				$info="添加渠道成功";
			}
			$arr=array("info"=>$info,"status"=>$rev);
			echo json_encode($arr);
			exit;
			
		}
		$this->assign("action","?g=Admin&m=Channel&a=add");
		$this->display();
	}
	/**
	 * 删除
	 * Enter description here ...
	 */
	function del(){
		$r_id=$this->in("c_id");
		
		$u_list=explode(',', $r_id);
		$rev=false;
		if(is_array($u_list) && count($u_list)){
			foreach ($u_list as $key=>$uid){
				$user=D("Channel");
				$s=array();
				$s["C_ID"]=$uid;
				$rev=$user->where($s)->delete();
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
	 * 编辑
	 * Enter description here ...
	 */
	function edit(){
		$p_id=$this->in("c_id");
		$data=$this->in("data");
		if($data){
			$rev="n";
			$info='渠道编辑失败';
			$role=D("Channel");
			$role->find($data["C_ID"]);
			$role->C_NMAE=$data["C_NMAE"];
			
			$rev=$role->save();
			if($rev){
				$rev="y";
				$info='渠道编辑成功';
			}
			$arr=array("info"=>$info,"status"=>$rev);
			echo json_encode($arr);
			exit;
		}
		if($p_id){
			$user=D("Channel");
			$uData=$user->where('C_ID='.$p_id)->limit(1)->select();
			
			$this->assign('uData',$uData[0]);
			
		}
		$this->assign("c_id",$p_id);
		$this->assign("action",'?g=Admin&m=Channel&a=edit');
		
		$this->display();
	}
}