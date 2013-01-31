<?php
/**
 * 权限
 * Enter description here ...
 * @author transn
 *
 */
class PvlAction extends BaseAction{
	
	/**
	 * 权限列表
	 * Enter description here ...
	 */
	function plist(){
		$role=D("Privilege");
		//每页显示多少个
		$pageSize=20;
		
		$search=array();
		if($this->in("P_NAME")){
			$this->assign('P_NAME',$this->in("P_NAME"));
			$search["P_NAME"]=array("like","%".$this->in("P_NAME")."%");
		}
		if($this->in("P_CODE")){
			$this->assign('P_CODE',$this->in("P_CODE"));
			$search["P_CODE"]=array("like","%".$this->in("P_CODE")."%");
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
		$info='权限添加失败';
		if($data){
			//先判断权限代码是否存在
			$pvl=D("Privilege");
			$_search["P_CODE"]=$data["P_CODE"];
			$_count=$pvl->where($_search)->count();
			if($_count){
				$info='权限代码重复,权限添加失败';
			}
			else{
				$role=D("Privilege");
				$data["CREATED_AT"]=time();
				$data["UPDATED_AT"]=time();
				$rev=$role->add($data);
				if($rev){
					$rev='y';
					$info='权限添加成功';
				}
			}
			$arr=array("info"=>$info,"status"=>$rev);
			echo json_encode($arr);
			exit;
			
		}
		$this->assign("type",ExUse::getDictByList("PRIVILEGE_TYPE"));
		$this->assign("action",'?g=Admin&m=Pvl&a=add');
		$this->display();
	}
	/**
	 * 删除角色
	 * Enter description here ...
	 */
	function del(){
		$p_id=$this->in("p_id");
		
		$u_list=explode(',', $p_id);
		if(is_array($u_list) && count($u_list)){
			foreach ($u_list as $key=>$uid){
				$user=D("Privilege");
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
		$p_id=$this->in("p_id");
		$data=$this->in("data");
		if($data){
			$rev="n";
			$info='权限编辑失败';
			$role=D("Privilege");
			$role->find($data['P_ID']);
			$role->P_NAME=$data["P_NAME"];
			if($role->P_CODE != $data["P_CODE"]){
				$pvl=D("Privilege");
				$_search["P_CODE"]=$data["P_CODE"];
				$_count=$pvl->where($_search)->count();
				if($_count){
					$info='权限代码重复,权限编辑失败';
					$arr=array("info"=>$info,"status"=>$rev);
					echo json_encode($arr);
					exit;
				}
			}
			
			$role->P_CODE=$data["P_CODE"];
			$role->P_TYPE=$data["P_TYPE"];
			$role->P_DESC=$data["P_DESC"];
			$role->UPDATED_AT=time();
			$rev=$role->save();
			if($rev){
				$rev="y";
				$info='权限编辑成功';
			}
			$arr=array("info"=>$info,"status"=>$rev);
			echo json_encode($arr);
			exit;
		}
		if($p_id){
			$user=D("Privilege");
			$uData=$user->where('P_ID='.$p_id)->select();
			$this->assign('uData',$uData[0]);
		}
		$this->assign("type",ExUse::getDictByList("PRIVILEGE_TYPE"));
		$this->assign("action",'?g=Admin&m=Pvl&a=edit');
		$this->assign("edit",'y');//说明是编辑
		
		$this->display();
	}
	function checkCode(){
		$param=$this->in('param');
		
		if($param){
			$role=D("Privilege");
			$search=array();
			$search['P_CODE']=$param;
			$data=$role->where($search)->count();
			if($data){
				
				echo "权限代码已存在";
			}else{
				echo "y";
			}
		}else{
			echo "必填项不能为空";
		}
		exit;
	}
	function lock(){
		$p_id=$this->in("p_id");
		$state=$this->in("state");
		$u_list=explode(',', $p_id);
		if(is_array($u_list) && count($u_list)){
			foreach ($u_list as $key=>$uid){
				$user=D("Privilege");
				$user->find($uid);
				$user->P_STATE=$state;
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
}