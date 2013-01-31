<?php
/**
 * 角色
 * Enter description here ...
 * @author transn
 *
 */
class RoleAction extends BaseAction{
	
	/**
	 * 角色列表
	 * Enter description here ...
	 */
	function rlist(){
		$role=D("Role");
		//每页显示多少个
		$pageSize=20;
		
		//检索条件
		$search=array();
		if($this->in("R_NAME")){
			$this->assign('R_NAME',$this->in("R_NAME"));
			$search["R_NAME"]=array("like","%".$this->in("R_NAME")."%");
		}
		if($this->in("R_SHORT_NAME")){
			$this->assign('R_SHORT_NAME',$this->in("R_SHORT_NAME"));
			$search["R_SHORT_NAME"]=array("like","%".$this->in("R_SHORT_NAME")."%");
		}
		
		$search["IS_DEL"]=0;
		if($_GET['p']){
			$list=$role->where($search)->page($_GET['p'].','.$pageSize)->select();
		}else{
			$list=$role->where($search)->limit('0,'.$pageSize)->select();
		}
		$this->assign('rolelist',$list);
		import('ORG.Util.Page');
		$count      = $role->count();// 查询满足要求的总记录数
		$Page       = new Page($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	function add(){
		$data=$this->in("data");
		$rev="n";
		$info='角色添加失败';
		if($data){
			$role=D("Role");
			$data["CREATED_AT"]=time();
			$data["UPDATED_AT"]=time();
			$data["R_SHORT_NAME"]=strtoupper($data["R_SHORT_NAME"]);
			$rev=$role->add($data);
			if($rev){
				$rev='y';
				$info='角色添加成功';
			}
			$arr=array("info"=>$info,"status"=>$rev);
			echo json_encode($arr);
			exit;
			
		}
		$this->assign("action",'?g=Admin&m=Role&a=add');
		$this->display();
	}
	/**
	 * 删除角色
	 * Enter description here ...
	 */
	function del(){
		$r_id=$this->in("r_id");
		
		$u_list=explode(',', $r_id);
		if(is_array($u_list) && count($u_list)){
			foreach ($u_list as $key=>$uid){
				$user=D("Role");
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
	 * 编辑角色
	 * Enter description here ...
	 */
	function edit(){
		$r_id=$this->in("r_id");
		$data=$this->in("data");
		if($data){
			$rev="n";
			$info='角色编辑失败';
			$role=D("Role");
			$role->find($data['R_ID']);
			$role->R_NAME=$data["R_NAME"];
			$role->R_SHORT_NAME=strtoupper($data["R_SHORT_NAME"]);
			$role->R_HOME_URL=$data["R_HOME_URL"];
			$role->R_MEMO=$data["R_MEMO"];
			$role->UPDATED_AT=time();
			$rev=$role->save();
			if($rev){
				$rev="y";
				$info='角色编辑成功';
			}
			$arr=array("info"=>$info,"status"=>$rev);
			echo json_encode($arr);
			exit;
		}
		if($r_id){
			$user=D("Role");
			$uData=$user->where('R_ID='.$r_id)->select();
			
			$this->assign('uData',$uData[0]);
			
		}
		$this->assign("action",'?g=Admin&m=Role&a=edit');
		$this->assign("edit",'y');//说明是编辑
		
		$this->display();
	}
	function checkrole(){
		$param=$this->in('param');
		$name=$this->in('name');
		if($param && $name){
			$role=D("Role");
			$search=array();
			if($name=='data[R_NAME]'){
				$search['R_NAME']=$param;
			}else if($name=="data[R_SHORT_NAME]"){
				$search['R_SHORT_NAME']=strtoupper($param);
			}
			$data=$role->where($search)->count();
			if($data){
				if($name=='data[R_NAME]'){
					$rev="角色名称已存在";
				}else if($name=="data[R_SHORT_NAME]"){
					$rev="角色简称已存在";
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
}