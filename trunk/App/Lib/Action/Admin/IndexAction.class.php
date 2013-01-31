<?php
/**
 * 常用类
 * Enter description here ...
 * @author df
 *
 */
class IndexAction extends BaseAction {
    public function index(){
       $this->redirect('?g=Admin&m=Main&a=login');
    }
    /**
     * 查询区域使用
     * Enter description here ...
     */
    function sarea(){
    	
    	$f1=$this->in("f1");//字段1  存放ID
		$f2=$this->in("f2");//字段2 存放name
		
		$this->assign('f1',$f1);
		$this->assign('f2',$f2);
		//每页显示多少个
		$pageSize=10;
		$m=M();
		if(!$type){
			$type='u';
		}
		$sql="select AR_ID,AR_L1,AR_L2,AR_L3,AR_PINYIN from as_area where 1=1 ";
		if($this->in("AR_L1")){
			$this->assign('AR_L1',$this->in("AR_L1"));
			$sql.=" and AR_L1 like '%".$this->in("AR_L1")."%'";
		}
		if($this->in("AR_L2")){
			$this->assign('AR_L2',$this->in("AR_L2"));
			$sql.=" and AR_L2 like '%".$this->in("AR_L2")."%'";
		}
    	if($this->in("AR_L3")){
			$this->assign('AR_L3',$this->in("AR_L3"));
			$sql.=" and AR_L3 like '%".$this->in("AR_L3")."%'";
		}
		$list=$m->query($sql);//使用总数
		if($_GET['p']){
			$p=$_GET['p'];
			$s=($p-1)*$pageSize;
			$sql.=" limit {$s},{$pageSize}";
		}
		else{
			$sql.=" limit 0,{$pageSize}";
		}
		$data=$m->query($sql);
		$revData=array();
		if($data){
			foreach ($data as $v){
				$v["AR_ADDRESS"]=$v["AR_L1"]."　".$v["AR_L2"]."　".$v["AR_L3"];
				$revData[]=$v;
			}
		}
		$this->assign("userList",$revData);
		import('ORG.Util.Page');
		$count      = count($list);// 查询满足要求的总记录数
		$Page       = new Page($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
    }
    
    function test(){
    	set_time_limit(0);
    	//生成20W用户
    	for($i=324;$i<=200000;$i++){
    		$data=array();
    		$user=D("User");
    		$data["U_PWD"]=md5('123');
			$data["CREATED_AT"]=time();
			$data["UPDATED_AT"]=time();
			$data["U_CREATER"]='';
			$data["U_GU_ID"]=getGuId();
			$data["U_NAME"]="test-".$i."@163.com";
			$data["U_RNAME"]="test-".$i;
			$data["U_EMAIL"]="test-".$i."@163.com";
			$data["U_MOBILE"]="11111111111";
			$data["U_PHONE"]='01011111111';
			$data["G_ID"]='1';
    		$rev=$user->add($data);
			if($rev){
				//写入角色表
				$s_role=4;
				$uRole=D("User_role");
				$search=array();
				$search['U_ID']=$rev;
				$search['R_ID']=$s_role;
				$uRole->add($search);
				
				//写入客户表
				$Customer=D("Customer");
   				$cData["U_ID"]=$rev;
   				$cData["CS_REG_TIME"]=time();
   				$Customer->add($cData);
   				
   				//生成订单
   				$order=D('Service_order');
   				$s["O_NO"]=getOrderNumber();
	   			$s["O_NAME"]="报装单";
	   			$s["O_TYPE"]="1";
	   			$s["O_STATE"]='20';
	   			$s["CU_ID"]=$rev;
	   			$s["CU_NAME"]=$data["U_RNAME"];
	   			
	   			//写到地址表中
	   			$CU_ID=$rev;
	   			$ar_id=18;//默认是 北京 线  密云县
	   			if($ar_id && $CU_ID){
	   				$address=D("Address");
	   				$ad=array();
	   				$ad["U_ID"]=$CU_ID;
	   				$ad["AR_ID"]=$ar_id;
	   				$addr=$address->where($ad)->select();
	   				if($addr){
	   					$ID=$addr[0]["ID"];
	   					$address->find($ID);
	   					$address->U_ADDRESS=$data["O_ADDRESS"];
	   					$address->U_PHONE=$data["U_PHONE"];
	   					$address->save();
	   				}else{
	   					$ads["U_ID"]=$CU_ID;
	   					$ads["AR_ID"]=$ar_id;
	   					$ads["U_ADDRESS"]=$data["O_ADDRESS"];
	   					$ads["U_PHONE"]=$data["U_PHONE"];
	   					$address->add($ads);
	   				}
	   			}
	   			$s["AR_ID"]=$ar_id;
	   			$s["O_ADDRESS"]="密云1#";
	   			$s["O_PHONE"]=$data["U_PHONE"];
	   			$s["O_MOBILE"]=$data["U_MOBILE"];
	   			$s["O_EMAIL"]=$data["U_EMAIL"];
	   			
	   			$product=D("Product");
	   			
	   			$s["P_ID"]=12;
	   			$s["P_BRAND"]="万家乐";;
	   			$s["P_CATEGORY"]="燃气热水器";
	   			$s["P_MODEL"]="JSQ24-12Z3";
	   			$s["P_BUY_TIME"]=time();
	   			$s["O_REQ_TIME"]=time();
	   			
	   			$s["P_CHANNEL_ID"]="京东";
	   			$s["P_CHANNEL"]="京东";
	   			$s["SO_ID"]="11111111";
	   			$s["O_MEMO"]="周末上门服务";
	   			$s["ST_ID"]=2;
	   			$s["ST_NAME"]="test2";
	   			$s["CREATED_AT"]=time();
	   			$s["UPDATED_AT"]=time();
	   			$rev1=$order->add($s);
					
			}
    	}
    }
 /**
     * 得到品牌型号数据  Json格式返回
     * Enter description here ...
     */
   function getProduct(){
   		$status=$this->in("st");//得到需要检索的项、
   		$stName=$this->in("stName");//得到需要检索的项、
   		$filed=$this->in("fl");//需要返回的字段
   		if(strstr($stName, '|')){
   			$t=explode('|', $stName);
   			$v=explode('|', $status);
   			
   			$s[$t[0]]=$v[0];
   			$s[$t[1]]=$v[1];
   		}else{
   			$s[$stName]=$status;
   		}
   		
   		$pp=getProductFiled($s,$filed);
   		if(is_array($pp) && count($pp)){
   			echo json_encode(array('sucess'=>'y','fl'=>$filed,'data'=>$pp));
   		}else{
   			echo json_encode(array('sucess'=>'n','fl'=>$filed,'data'=>$pp));
   		}
   		exit;
   }
 /**
     * 得到品牌型号数据  Json格式返回
     * Enter description here ...
     */
   function getArea(){
   		$status=$this->in("st");//得到需要检索的项、
   		$stName=$this->in("stName");//得到需要检索的项、
   		$filed=$this->in("fl");//需要返回的字段
   		if(strstr($stName, '|')){
   			$t=explode('|', $stName);
   			$v=explode('|', $status);
   			
   			$s[$t[0]]=$v[0];
   			$s[$t[1]]=$v[1];
   		}else{
   			$s[$stName]=$status;
   		}
   		$pp=getAreaFiled($s,$filed);
   		       
   		if(is_array($pp) && count($pp)){
   			echo json_encode(array('sucess'=>'y','fl'=>$filed,'data'=>$pp));
   		}else{
   			echo json_encode(array('sucess'=>'n','fl'=>$filed,'data'=>$pp));
   		}
   		exit;
   }
}