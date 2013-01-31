<?php
/**
 * 产品管理
 * Enter description here ...
 * @author df
 *
 */
class ProductAction extends BaseAction{
	/**
	 * 列表
	 * Enter description here ...
	 */
	function plist(){
		$role=D("Product");
		//每页显示多少个
		$pageSize=20;
		
		//检索条件
		$search=array();
		if($this->in("P_BRAND")){
			$this->assign('P_BRAND',$this->in("P_BRAND"));
			$search["P_BRAND"]=array("like","%".$this->in("P_BRAND")."%");
		}
		if($this->in("P_CATEGORY")){
			$this->assign('P_CATEGORY',$this->in("P_CATEGORY"));
			$search["P_CATEGORY"]=array("like","%".$this->in("P_CATEGORY")."%");
		}
		if($this->in("P_MODEL")){
			$this->assign('P_MODEL',$this->in("P_MODEL"));
			$search["P_MODEL"]=array("like","%".$this->in("P_MODEL")."%");
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
			$user1=D('Product');
			$pageList=$user1->where($search)->count('P_ID');
		}
		else{
			//$pageList=$user->select();
			$user1=D('Product');
			$pageList=$user1->count('P_ID');
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
			$P_BRAND=$this->in("P_BRAND");
			$P_CATEGORY=$this->in("P_CATEGORY");
			$P_MODEL=$this->in("P_MODEL");
			
			//检测是否存在重复
			$is_re=false;
			$rev="n";
			$__rev=false;
			if(is_array($P_BRAND) && count($P_BRAND)){
				foreach ($P_BRAND as $key=>$val){
					$area=D("Product");
					$_s=array();
					$_s["P_BRAND"]=$val;
					$_s["P_CATEGORY"]=$P_CATEGORY[$key];
					$_s["P_MODEL"]=$P_MODEL[$key];
					$__count=$area->where($_s)->count('P_ID');
					if($__count>0){
						$is_re=true;
						$info="添加产品失败,品牌:".$val.",产品类型:".$P_CATEGORY[$key].",产品型号:".$P_MODEL[$key]."　数据重复！";
						break;
					}
				}
			}
			if($is_re){
				$arr=array("info"=>$info,"status"=>$rev);
				echo json_encode($arr);
				exit;
			}else{
				foreach ($P_BRAND as $key=>$val){
						$area1=D("Product");
						$_s=array();
						$_s["P_BRAND"]=$val;
						$_s["P_CATEGORY"]=$P_CATEGORY[$key];
						$_s["P_MODEL"]=$P_MODEL[$key];
						$_s["CREATED_AT"]=time();
						$_s["UPDATED_AT"]=time();
						$__rev=$area1->add($_s);
				}
			}
			if($__rev){
				$rev='y';
				$info="添加产品成功";
			}
			$arr=array("info"=>$info,"status"=>$rev);
			echo json_encode($arr);
			exit;
			
		}
		$this->assign("action","?g=Admin&m=Product&a=add");
		$this->display();
	}
	/**
	 * 删除
	 * Enter description here ...
	 */
	function del(){
		$r_id=$this->in("p_id");
		
		$u_list=explode(',', $r_id);
		$rev=false;
		if(is_array($u_list) && count($u_list)){
			foreach ($u_list as $key=>$uid){
				$user=D("Product");
				$s=array();
				$s["P_ID"]=$uid;
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
		$p_id=$this->in("p_id");
		$data=$this->in("data");
		if($data){
			$rev="n";
			$info='产品编辑失败';
			$role=D("Product");
			$role->find($data["P_ID"]);
			$role->P_BRAND=$data["P_BRAND"];
			$role->P_MODEL=$data["P_MODEL"];
			$role->P_CATEGORY=$data["P_CATEGORY"];
			$role->UPDATED_AT=time();
			$rev=$role->save();
			if($rev){
				$rev="y";
				$info='产品编辑成功';
			}
			$arr=array("info"=>$info,"status"=>$rev);
			echo json_encode($arr);
			exit;
		}
		if($p_id){
			$user=D("Product");
			$uData=$user->where('P_ID='.$p_id)->limit(1)->select();
			
			$this->assign('uData',$uData[0]);
			
		}
		$this->assign("p_id",$p_id);
		$this->assign("action",'?g=Admin&m=Product&a=edit');
		
		$this->display();
	}
	
}