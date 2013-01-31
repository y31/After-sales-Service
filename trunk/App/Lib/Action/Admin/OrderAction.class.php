<?php
/**
 * 工单类
 * Enter description here ...
 * @author admin
 *
 */
class OrderAction extends BaseAction{
	
	/**
	 * 报装列表
	 * Enter description here ...
	 */
	function bgList(){
		$this->assign('title','报装单列表');
		//每页显示多少个
		$pageSize=10;
		$otype=$this->in('type');
		if(!$otype){
			$otype='all';
		}
		
		
		$order=D("Service_order");
		$search=array();
		$outSearch=array();
		
		if($otype!='all'){
			$search["O_STATE"]=$otype;
		}
		
		//检索条件
		if($this->in("O_NO")){
			//$this->assign('O_NO',$this->in("O_NO"));
			$outSearch["O_NO"]=$this->in("O_NO");
			$search["O_NO"]=array("like","%".$this->in("O_NO")."%");
		}
		if($this->in("O_TYPE")){
			$outSearch["O_TYPE"]=$this->in("O_TYPE");
			$search["O_TYPE"]=$this->in("O_TYPE");
		}
		if($this->in("O_STATE")){
			$outSearch["O_STATE"]=$this->in("O_STATE");
			$search["O_STATE"]=$this->in("O_STATE");
		}
		if($this->in("CREATED_AT")){
			$outSearch["CREATED_AT"]=$this->in("CREATED_AT");
			$_time=ExUse::getTimeByCT($this->in("CREATED_AT"));
			$search["CREATED_AT"]=array('between',array(strtotime($_time['s']),strtotime($_time['e'])));
		}else{//默认显示最近一周
			$_time=ExUse::getTimeByCT(10);
			
			$search["CREATED_AT"]=array('between',array(strtotime($_time['s']),strtotime($_time['e'])));
			//$search["CREATED_AT"]=array(array('egt',strtotime($_time['s'])),array('elt',strtotime($_time['e'])));
		}
		if($this->in("CU_NAME")){
			$outSearch["CU_NAME"]=$this->in("CU_NAME");
			//$search["CU_NAME"]=array("like","%".$this->in("CU_NAME")."%");
		}
		//用户客户ID精确查询
		if($this->in("CU_ID")){
			$outSearch["CU_ID"]=$this->in("CU_ID");
			$search["CU_ID"]=$this->in("CU_ID");
		}
		if($this->in("O_MOBILE")){
			$outSearch["O_MOBILE"]=$this->in("O_MOBILE");
			$search["O_MOBILE"]=array("like","%".$this->in("O_MOBILE")."%");
		}
		if($this->in("AR_ADDRESS")){
			$outSearch["AR_ADDRESS"]=$this->in("AR_ADDRESS");
			//$search["O_MOBILE"]=array("like","%".$this->in("O_MOBILE")."%");
		}
		if($this->in("AR_ID")){
			$outSearch["AR_ID"]=$this->in("AR_ID");
			$search["AR_ID"]=$this->in("AR_ID");
		}
		if($this->in("ST_NAME")){
			$outSearch["ST_NAME"]=$this->in("ST_NAME");
			$search["ST_NAME"]=array("like","%".$this->in("ST_NAME")."%");
		}
		$this->assign('outSearch',$outSearch);
		
		
		if($_GET['p']){
			if(count($search)){
				$list=$order->where($search)->page($_GET['p'].','.$pageSize)->select();
			}else{
				$list=$order->page($_GET['p'].','.$pageSize)->select();
			}
			
		}else{
			if(count($search)){
				$list=$order->where($search)->limit('0,'.$pageSize)->select();
			}else{
				$list=$order->limit('0,'.$pageSize)->select();
			}
		}
		//page使用的总数
		if(count($search)){
			$order1=D("Service_order");
			//$pageList=$order->where($search)->select();
			$pageList=$order1->where($search)->count('O_ID');
		}
		else{
			//$pageList=$order->select();
			$order1=D("Service_order");
			$pageList=$order1->count('O_ID');
		}
		
		$out=array();
		if($list && count($list)){
			foreach ($list as $v){
				//$v["CREATED_AT"]=date("Y-m-d",$v["CREATED_AT"]);
				$O_STATE_ID=$v["O_STATE"];
				$v["O_STATE"]=ExUse::getOrderState($O_STATE_ID);
				$v["O_STATE_ID"]=$O_STATE_ID;
				$v["O_TYPE"]=ExUse::getOrderType($v["O_TYPE"]);
				//去地区
				$AR_ID=$v["AR_ID"];
				$tmpData=ExUse::getArea($AR_ID);
				
				$STR=implode(' ',$tmpData);
				$v["O_AREA"]=$STR.' '.$v["O_ADDRESS"];
				$v["O_PRODUCT"]=$v["P_BRAND"].' '.$v["P_CATEGORY"].' '.$v["P_MODEL"];
				
				$out[]=$v;
			}
		}
		$this->assign('orderList',$out);
		//得到类型
		$type=array(array('id'=>'','name'=>'全部'),array('id'=>'1','name'=>'报装'),array('id'=>'2','name'=>'保修'));
		$state=ExUse::getOrderStateArr();
		$states=array();
		$states[]=array("id"=>'','name'=>'全部');
		foreach ($state as $key=>$val){
			$states[]=array("id"=>$key,'name'=>$val);
		}
		
		$this->assign('type',$type);
		$this->assign('state',$states);
		
		//取创建时间
		$createTime=ExUse::getCreatedTime();
		$this->assign('createTime',$createTime);
		
		import('ORG.Util.Page');
		//$count      = count($pageList);// 查询满足要求的总记录数
		$count      = $pageList;// 查询满足要求的总记录数
		$Page       = new Page($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."查看服务单列表。";
		ExLog::addLog($msg, 'TYPE_ORDER_VIEW');
		$this->display('bgList');
	}
	/**
	 * 显示详情
	 * Enter description here ...
	 */
	function viewBgXg(){
		$O_ID=$this->in("O_ID");
		$order=D("Service_order");
		$data=$order->where('O_ID='.$O_ID)->select();
		$tmpData=$data[0];
		$AR_ID=$tmpData["AR_ID"];
		if($AR_ID){
			$area=D("Area");
			$aData=$area->where('AR_ID='.$AR_ID)->select();
			if($aData){
				$tmpData["AR_L1"]=$aData[0]["AR_L1"];
				$tmpData["AR_L2"]=$aData[0]["AR_L2"];
				$tmpData["AR_L3"]=$aData[0]["AR_L3"];
			}
		}
		$P_CHANNEL_ID=$tmpData["P_CHANNEL_ID"];
		if($P_CHANNEL_ID){
			$channel=D("Channel");
			$aData=$channel->where('P_CHANNEL_ID='.$P_CHANNEL_ID)->select();
			if($aData){
				$tmpData["P_CHANNEL_ID"]=$aData[0]["C_NMAE"];
				
			}else{
				$tmpData["P_CHANNEL_ID"]='';
			}
		}else{
			$tmpData["P_CHANNEL_ID"]='';
		}
		$this->assign("data",$tmpData);
		$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."查看服务单详情。";
		ExLog::addLog($msg, 'TYPE_ORDER_VIEW');
		$this->display();
	}
	/**
	 * 报装单导出
	 * Enter description here ...
	 */
	function dataExport(){
		$order=D("Service_order");
		//检索条件
	//检索条件
		if($this->in("O_NO")){
			$search["O_NO"]=array("like","%".$this->in("O_NO")."%");
		}
		if($this->in("O_TYPE")){
			$search["O_TYPE"]=$this->in("O_TYPE");
		}
		if($this->in("O_STATE")){
			$search["O_STATE"]=$this->in("O_STATE");
		}
		if($this->in("CREATED_AT")){
			$_time=ExUse::getTimeByCT($this->in("CREATED_AT"));
			$search["CREATED_AT"]=array('between',array(strtotime($_time['s']),strtotime($_time['e'])));
		}else{//默认显示最近一周
			$_time=ExUse::getTimeByCT(10);
			
			$search["CREATED_AT"]=array('between',array(strtotime($_time['s']),strtotime($_time['e'])));
		}
		
		//用户客户ID精确查询
		if($this->in("CU_ID")){
			$search["CU_ID"]=$this->in("CU_ID");
		}
		if($this->in("O_MOBILE")){
			$search["O_MOBILE"]=array("like","%".$this->in("O_MOBILE")."%");
		}
		
		if($this->in("AR_ID")){
			$search["AR_ID"]=$this->in("AR_ID");
		}
		if($this->in("ST_NAME")){
			$search["ST_NAME"]=array("like","%".$this->in("ST_NAME")."%");
		}
		if($this->in("stype")){
			$stype=$this->in("stype");
			if($stype==1){
				$__stype=0;
			}else{
				$__stype=$stype;
			}
			$search["O_F_STATE"]=$__stype;
		}
		
		//$search["O_TYPE"]=1;// 1为报装
		$list=$order->where($search)->select();
		$export=array();
		$title=array('编号','类型','结算状态','状态','地区','客户','手机','固定电话','邮箱','产品','服务队','购买渠道','购买时间','创建时间','备注');
		$export[0]=$title;
		
		if($list){
			foreach ($list as $val){
				$data=array();
				
				$O_STATE=ExUse::getOrderState($val["O_STATE"]);
				$O_TYPE=ExUse::getOrderType($val["O_TYPE"]);
				$O_F_STATE=ExUse::getAccountsState($val["O_F_STATE"]);
				//去地区
				$AR_ID=$val["AR_ID"];
				$tmpData=ExUse::getArea($AR_ID);
				
				$STR=implode('　',$tmpData);
				$O_AREA=$STR.'　'.$val["O_ADDRESS"];
				$O_PRODUCT=$val["P_BRAND"].'　'.$val["P_CATEGORY"].'　'.$val["P_MODEL"];
				
				$data[]=$val["O_NO"];
				$data[]=$O_TYPE;
				$data[]=$O_F_STATE;
				$data[]=$O_STATE;
				$data[]=$O_AREA;
				$data[]=$val["CU_NAME"];
				$data[]=$val["O_MOBILE"];
				$data[]=$val["O_PHONE"];
				$data[]=$val["O_EMAIL"];
				$data[]=$O_PRODUCT;
				$data[]=$val["ST_NAME"];
				//取渠道
				$channels='';
				/*$P_CHANNEL_ID=$val["P_CHANNEL_ID"];
				if($P_CHANNEL_ID){
					$channel=D("Channel");
					$channel->find($P_CHANNEL_ID);
					
					$channels=$channel->C_NMAE;
				}
				$data[]=$channels.$val["P_CHANNEL"];
				*/
				$data[]=$val["P_CHANNEL"];
				$data[]=formatDate($val["P_BUY_TIME"],'Y-m-d');
				$data[]=formatDate($val["CREATED_AT"],'Y-m-d');
				$data[]=$val["O_MEMO"];
				$export[]=$data;
			}
		}
		
		$path=Excel::ExportToXls($export,'bg');
		$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."导出服务单详情。";
		ExLog::addLog($msg, 'TYPE_ORDER_EXPORT');
		echo $path;
		exit;
	}
	
	/**
	 * 报装单导出
	 * Enter description here ...
	 */
	function dataExport1(){
		$order=D("Service_order");
		//检索条件
		$search["O_TYPE"]=1;// 1为报装
		$list=$order->where($search)->select();
		$export=array();
		$title=array('订单号','姓名','手机','固定电话','邮箱','地址','品牌','品类','型号','购买渠道','购买时间','创建时间','备注');
		$export[0]=$title;
		
		if($list){
			foreach ($list as $val){
				$data=array();
				$data[]=$val["O_NO"];
				$data[]=$val["O_NAME"];
				$data[]=$val["O_MOBILE"];
				$data[]=$val["O_PHONE"];
				$data[]=$val["O_EMAIL"];
				$address='';
				//取地址
				$AR_ID=$val["AR_ID"];
				if($AR_ID){
					$area=D("Area");
					$aData=$area->where('AR_ID='.$AR_ID)->select();
					if($aData){
						$address.=$aData[0]["AR_L1"];
						$address.=$aData[0]["AR_L2"];
						$address.=$aData[0]["AR_L3"];
					}
				}
				$address.=$val["O_ADDRESS"];;
				$data[]=$address;
				$data[]=$val["P_BRAND"];
				$data[]=$val["P_CATEGORY"];
				$data[]=$val["P_MODEL"];
				
				//取渠道
				$channels='';
				/*$P_CHANNEL_ID=$val["P_CHANNEL_ID"];
				if($P_CHANNEL_ID){
					$channel=D("Channel");
					$aData=$channel->where('P_CHANNEL_ID='.$P_CHANNEL_ID)->select();
					if($aData){
						$channels=$aData[0]["C_NMAE"];
						
					}
				}*/
				//$data[]=$channels.$val["P_CHANNEL"];
				$data[]=$val["P_CHANNEL"];
				$data[]=formatDate($val["P_BUY_TIME"],'Y-m-d');
				$data[]=formatDate($val["CREATED_AT"],'Y-m-d');
				$data[]=$val["O_MEMO"];
				$export[]=$data;
			}
		}
		
		$path=Excel::ExportToXls($export,'bg');
		$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."导出服务单详情。";
		ExLog::addLog($msg, 'TYPE_ORDER_EXPORT');
		echo $path;
		exit;
	}
	/**
	 * 导出报表
	 * Enter description here ...
	 */
	function export(){
		$path=$this->in("path");
		if($path){
			$this->assign('path',$path);
		}else{
			$this->assign('path','n');
		}
		
		$this->display();
	}
	/**
	 * 显示服务单详情
	 * Enter description here ...
	 */
	function viewdetail(){
		$O_ID=$this->in("O_ID");
		$order=D("Service_order");
		$data=$order->where('O_ID='.$O_ID)->select();
		$ostatie=10;
		if($data){
			$tmpData=$data[0];
			$ostatie=$tmpData["O_STATE"];
			$tmpData["O_STATE"]=$tmpData["O_STATE"];
			$tmpData["O_STATE_NAME"]=ExUse::getOrderState($tmpData["O_STATE"]);
			
			$tmpData["O_TYPE"]=ExUse::getOrderType($tmpData["O_TYPE"]);
			
			$tmpData["O_F_STATE"]=ExUse::getJsState($tmpData["O_F_STATE"]);
			
			$tmpData["O_REQ_TIME"]=formatDate($tmpData["O_REQ_TIME"]);
			$tmpData["O_SRV_B_TIME"]=formatDate($tmpData["O_SRV_B_TIME"]);
			$tmpData["O_SRV_TIME"]=formatDate($tmpData["O_SRV_TIME"]);
			
			//去地区
			$AR_ID=$tmpData["AR_ID"];
			$tData=ExUse::getArea($AR_ID);
			
			$STR=implode(' ',$tData);
			$tmpData["O_AREA"]=$STR.' '.$tmpData["O_ADDRESS"];
			$tmpData["O_PRODUCT"]=$tmpData["P_BRAND"].'，'.$tmpData["P_CATEGORY"].'，'.$tmpData["P_MODEL"];
			$tmpData["P_BUY_TIME"]=formatDate($tmpData["P_BUY_TIME"],'Y-m-d');
			$uname=ExUse::getUname($tmpData["CU_ID"],'U_NAME');
			$tmpData["CU_ID_Name"]='';
			if($uname){
				$tmpData["CU_ID_Name"]=$uname;
			}
			
		}
		$this->assign("ostatie",$ostatie);
		
		$this->assign("data",$tmpData);
		//取订单日志并显示
		$order_log=D("Order_log");
		$ol["O_ID"]=$O_ID;
		$logs=$order_log->where($ol)->select();
		$this->assign("logs",$logs);
		
		$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."查看服务单详情。";
		$this->display();
	}
	/**
	 * 指派服务队
	 * Enter description here ...
	 */
	function saveTeam(){
		$O_ID=$this->in("o_id");
		$st_id=$this->in("st_id");
		$rev=0;
		if($st_id){
			$srvTeam=D("Srv_team");
			$srvTeam->find($st_id);
			$ST_NAME=$srvTeam->ST_NAME;
			$RP_U_ID=$srvTeam->RP_U_ID;
			$uname=ExUse::getUname($RP_U_ID,'U_RNAME');
			$SRV_NAME='';
			if($uname){
				$SRV_NAME=$uname;
			}
			if($O_ID){
				$order=D("Service_order");
				$order->find($O_ID);
				$order->ST_ID=$st_id;
				$order->ST_NAME=$ST_NAME;
				$order->SRV_ID=$RP_U_ID;
				$order->SRV_NAME=$SRV_NAME;
				$order->O_SRV_B_TIME=time();//服务开始时间
				$order->O_STATE=C("ORDER_STATE.STATE_PRE_PROCESS");//设置状态为进行中
				$logOid=$order->O_ID;
				$logONo=$order->O_NO;
				$rev=$order->save();
				if($rev){
					$msg=$ST_NAME."在".formatDate(time())."处理服务单。";
	   				ExLog::addLog($msg, 'TYPE_ORDER_EXECUTE',2,$logOid,$logONo);
	   				ExUse::sendSRVTeamMail($st_id, $logONo);
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
	 * 保存订单状态
	 * Enter description here ...
	 */
	function saveState(){
		$O_ID=$this->in("o_id");
		$state=$this->in("state");
		$order=D("Service_order");
		$order->find($O_ID);
		$order->O_STATE=$state;
		$logOid=$order->O_ID;
		$logONo=$order->O_NO;
		$rev=$order->save();
		if($rev){
			$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."处理服务单。改变状态为:".ExUse::getOrderState($state);
   			ExLog::addLog($msg, 'TYPE_ORDER_CHANGE_STATE',2,$logOid,$logONo);
			echo "y";
		}else{
			echo "n";
		}
		exit;
	}
	/**
	 * 确认中断 、违规   需要的操作
	 * Enter description here ...
	 */
	function statey(){
		$O_ID=$this->in("o_id");
		
		if($this->in("op")){
			$state=$this->in("state");
			$data=$this->in("data");
			$order=D("Service_order");
			$order->find($O_ID);
			$order->O_STATE=$state;
			$order->O_CS_MEMO=$data["O_CS_MEMO"];
			$logOid=$order->O_ID;
			$logONo=$order->O_NO;
			$rev=$order->save();
			if($rev){
				$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."处理服务单。改变状态为:".ExUse::getOrderState($state);
	   			ExLog::addLog($msg, 'TYPE_ORDER_CHANGE_STATE',2,$logOid,$logONo);
				echo "y";
			}else{
				echo "n";
			}
			exit;
		}
		$this->assign("o_id",$O_ID);
		$this->assign("state",$state);
		$this->display();
	}
	
	/**
	 * 结算列表
	 * Enter description here ...
	 */
	function jsList(){
		
		//每页显示多少个
		$pageSize=10;
		$otype=$this->in('stype');
		if(!$otype){
			$otype='1';//未结算
		}
		$this->assign("stype",$otype);
		
		$order=D("Service_order");
		$search=array();
		$outSearch=array();
		
		if($otype!='all'){
			if($otype==1){
				$fstate=0;
			}else{
				$fstate=$otype;
			}
			$search["O_F_STATE"]=$fstate;
		}
		
		//检索条件
		if($this->in("O_NO")){
			//$this->assign('O_NO',$this->in("O_NO"));
			$outSearch["O_NO"]=$this->in("O_NO");
			$search["O_NO"]=array("like","%".$this->in("O_NO")."%");
		}
		if($this->in("O_F_STATE")){
			$outSearch["O_F_STATE"]=$this->in("O_F_STATE");
			$search["O_F_STATE"]=$this->in("O_F_STATE");
		}
		if($this->in("O_STATE")){
			$outSearch["O_STATE"]=$this->in("O_STATE");
			$search["O_STATE"]=$this->in("O_STATE");
		}
		if($this->in("CREATED_AT")){
			$outSearch["CREATED_AT"]=$this->in("CREATED_AT");
			$_time=ExUse::getTimeByCT($this->in("CREATED_AT"));
			$search["CREATED_AT"]=array('between',array(strtotime($_time['s']),strtotime($_time['e'])));
		}else{//默认显示最近一周
			$_time=ExUse::getTimeByCT(10);
			
			$search["CREATED_AT"]=array('between',array(strtotime($_time['s']),strtotime($_time['e'])));
		}
		if($this->in("CU_NAME")){
			$outSearch["CU_NAME"]=$this->in("CU_NAME");
			//$search["CU_NAME"]=array("like","%".$this->in("CU_NAME")."%");
		}
		//用户客户ID精确查询
		if($this->in("CU_ID")){
			$outSearch["CU_ID"]=$this->in("CU_ID");
			$search["CU_ID"]=$this->in("CU_ID");
		}
		if($this->in("O_MOBILE")){
			$outSearch["O_MOBILE"]=$this->in("O_MOBILE");
			$search["O_MOBILE"]=array("like","%".$this->in("O_MOBILE")."%");
		}
		if($this->in("AR_ADDRESS")){
			$outSearch["AR_ADDRESS"]=$this->in("AR_ADDRESS");
			//$search["O_MOBILE"]=array("like","%".$this->in("O_MOBILE")."%");
		}
		if($this->in("AR_ID")){
			$outSearch["AR_ID"]=$this->in("AR_ID");
			$search["AR_ID"]=$this->in("AR_ID");
		}
		if($this->in("ST_NAME")){
			$outSearch["ST_NAME"]=$this->in("ST_NAME");
			$search["ST_NAME"]=array("like","%".$this->in("ST_NAME")."%");
		}
		$this->assign('outSearch',$outSearch);
		
		
		if($_GET['p']){
			if(count($search)){
				$list=$order->where($search)->page($_GET['p'].','.$pageSize)->select();
			}else{
				$list=$order->page($_GET['p'].','.$pageSize)->select();
			}
			
		}else{
			if(count($search)){
				$list=$order->where($search)->limit('0,'.$pageSize)->select();
			}else{
				$list=$order->limit('0,'.$pageSize)->select();
			}
		}
		
		//page使用的总数
		if(count($search)){
			//$pageList=$order->where($search)->select();
			$pageList=$order->where($search)->count();
		}
		else{
			//$pageList=$order->select();
			$pageList=$order->count();
		}
		
		
		$out=array();
		if($list && count($list)){
			foreach ($list as $v){
				$v["CREATED_AT"]=date("Y-m-d",$v["CREATED_AT"]);
				$O_STATE_ID=$v["O_STATE"];
				$v["O_STATE"]=ExUse::getOrderState($O_STATE_ID);
				$v["O_STATE_ID"]=$O_STATE_ID;
				$v["O_F_STATE"]=$v["O_F_STATE"];
				$v["O_F_STATE_NAME"]=ExUse::getAccountsState($v["O_F_STATE"]);
				$v["O_TYPE"]=ExUse::getOrderType($v["O_TYPE"]);
				//去地区
				$AR_ID=$v["AR_ID"];
				$tmpData=ExUse::getArea($AR_ID);
				
				$STR=implode(' ',$tmpData);
				$v["O_AREA"]=$STR.' '.$v["O_ADDRESS"];
				$v["O_PRODUCT"]=$v["P_BRAND"].' '.$v["P_CATEGORY"].' '.$v["P_MODEL"];
				
				$out[]=$v;
			}
		}
		
		$this->assign('orderList',$out);
		//得到类型
		$type=array(array('id'=>'','name'=>'全部'),array('id'=>'1','name'=>'报装'),array('id'=>'2','name'=>'保修'));
		$state=ExUse::getOrderStateArr();
		$states=array();
		$states[]=array("id"=>'','name'=>'全部');
		foreach ($state as $key=>$val){
			$states[]=array("id"=>$key,'name'=>$val);
		}
		
		$this->assign('type',$type);
		$this->assign('state',$states);
		//得到结算状态
		$this->assign("astate",ExUse::getAccountsStateByDict());
		
		//取创建时间
		$createTime=ExUse::getCreatedTime();
		$this->assign('createTime',$createTime);
		
		import('ORG.Util.Page');
		//$count      = count($pageList);// 查询满足要求的总记录数
		$count      = $pageList;// 查询满足要求的总记录数
		$Page       = new Page($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."查看结算单列表。";
		$this->display();
	}
	/**
	 * 结算
	 * Enter description here ...
	 */
	function changeState(){
		$oids=$this->in("oids");
		$rev='n';
		if($oids){
			$__oids=explode(",", $oids);
			if(is_array($__oids)){
				try{
					foreach ($__oids as $oid){
						$order=D("Service_order");
						$order->find($oid);
						$order->O_F_STATE=10;
						$order->save();
					}
					$rev='y';
				}
				catch(Exception $e){
				}
			}
		}
		echo $rev;
		exit;
	}
	/**
	 * 将7天的数据发送到指定邮箱
	 */
	function email(){
		$__time=$this->in("time");
		if($__time){
			$__time=7;
		}
		$cTime=time()-7*86400;
		$sTime=strtotime(date("Y-m-d",$cTime)." 00:00:01");
		$eTime=time();
		$order=D("Service_order");
		$search["CREATED_AT"]=array('between',array($sTime,$eTime));
		$list=$order->where($search)->select();
		$export=array();
		$title=array('编号','类型','结算状态','状态','地区','客户','手机','固定电话','邮箱','产品','服务队','购买渠道','购买时间','创建时间','备注');
		$export[0]=$title;
		
		if($list){
			foreach ($list as $val){
				$data=array();
				
				$O_STATE=ExUse::getOrderState($val["O_STATE"]);
				$O_TYPE=ExUse::getOrderType($val["O_TYPE"]);
				$O_F_STATE=ExUse::getAccountsState($val["O_F_STATE"]);
				//去地区
				$AR_ID=$val["AR_ID"];
				$tmpData=ExUse::getArea($AR_ID);
				
				$STR=implode('　',$tmpData);
				$O_AREA=$STR.'　'.$val["O_ADDRESS"];
				$O_PRODUCT=$val["P_BRAND"].'　'.$val["P_CATEGORY"].'　'.$val["P_MODEL"];
				
				$data[]=$val["O_NO"];
				$data[]=$O_TYPE;
				$data[]=$O_F_STATE;
				$data[]=$O_STATE;
				$data[]=$O_AREA;
				$data[]=$val["CU_NAME"];
				$data[]=$val["O_MOBILE"];
				$data[]=$val["O_PHONE"];
				$data[]=$val["O_EMAIL"];
				$data[]=$O_PRODUCT;
				$data[]=$val["ST_NAME"];
				//取渠道
				$channels='';
				/*$P_CHANNEL_ID=$val["P_CHANNEL_ID"];
				if($P_CHANNEL_ID){
					$channel=D("Channel");
					$channel->find($P_CHANNEL_ID);
					
					$channels=$channel->C_NMAE;
				}*/
				//$data[]=$channels.$val["P_CHANNEL"];
				$data[]=$val["P_CHANNEL"];
				$data[]=formatDate($val["P_BUY_TIME"],'Y-m-d');
				$data[]=formatDate($val["CREATED_AT"],'Y-m-d');
				$data[]=$val["O_MEMO"];
				$export[]=$data;
			}
		}
		$__starttime=formatDate($sTime,'Ymd');
		$__endtime=formatDate($eTime,'Ymd');
		$__xlsname="OrderList-".$__starttime."-".$__endtime;
		$path=Excel::ExportToXls($export,$__xlsname,true);
		$msg="在".formatDate(time())."发送".formatDate($sTime)."--".formatDate($eTime)."时间段的服务单。";
		ExLog::addLog($msg, 'TYPE_ORDER_SENDMAIL');
		$__content="您好:<br />请查收".formatDate($sTime,'Y年m月d日')."至".formatDate($eTime,'Y年m月d日')."时间段的服务单，具体内容请看附件。";
		if($path){
			Mail::getMail()->sendMail(C("SYS_ORDER_EMAIL"),iconv( "utf-8", "GBK","请查收服务单"),iconv( "utf-8", "GBK",$__content),true,utf-8,'','',$path);
		}
		exit;
	}
	/**
	 * 客服下单
	 * Enter description here ...
	 */
	function add(){
		if($this->in("u_id")){
			$u_id=$this->in("u_id");
			$user=D("User");
			$user->find($u_id);
			$arrUser["U_ID"]=$u_id;
			$arrUser["U_RNAME"]=$user->U_RNAME;
			$arrUser["U_MOBILE"]=$user->U_MOBILE;
			$arrUser["U_PHONE"]=$user->U_PHONE;
			$arrUser["U_EMAIL"]=$user->U_EMAIL;
			$address=D("Address");
	   		$ad=array();
	   		$ad["U_ID"]=$u_id;
	   		$addr=$address->where($ad)->order('U_ID desc')->select();
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
	   			$arrUser["U_ADDRESS"]=$addr[0]["U_ADDRESS"];
	   		}
	   		$this->assign("uData",$arrUser);
	   		$this->assign("edit","y");
		}
		
	   $pp=getProductFiled(array(),'P_BRAND');
       $this->assign('BRAND',$pp);
       
       //得到地区
       $area=getAreaFiled(array(),'AR_L1');
       $this->assign('AR_L1',$area);
       
       //得到渠道
       $channel=ExUse::getChannel();
       $this->assign('channel',$channel);
       //得到安装方式
       $__installMode=ExUse::getDictByList("INSTALL_MODE");
       $this->assign("installMode",$__installMode);
       $__type=ExUse::getDictByList("ORDER_TYPE");
       $this->assign("otype",$__type);
       
		$this->display();
	}
	/**
	 * 保存客户下单信息
	 * Enter description here ...
	 */
	function saveAdd(){
		$data=$this->in("data");
		$rev=0;
		$rev1='n';
		$info='下单失败';
		$__EMAIL='';
		$__MOBILE='';
		$__PRODUCT='';
		$__SPWD='';
		if($data){
			$user=D("User");
			if($data["U_ID"]){
				$user->find($data["U_ID"]);
				$CU_ID=$data["U_ID"];
   				$ZCTime=$user->CREATED_AT;
   				$__EMAIL=$user->U_EMAIL;
   				$__MOBILE=$user->U_MOBILE;
			}else{
				$uData["U_GU_ID"]=getGuId();
				if($data["U_EMAIL"]){
					$__EMAIL=$data["U_EMAIL"];
					$uData["U_NAME"]=$data["U_EMAIL"];
				}else{
					if($data["U_MOBILE"]){
						$uData["U_NAME"]=$data["U_MOBILE"];
					}
				}
   				$uData["U_RNAME"]=$data["U_RNAME"]?$data["U_RNAME"]:'';
   				$__pwd=mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9); 
   				$__SPWD=$__pwd;
   				$uData["U_PWD"]=md5($__pwd);
   				$uData["U_EMAIL"]=$data["U_EMAIL"]?$data["U_EMAIL"]:'';
   				if($data["U_MOBILE"]){
   					$uData["U_MOBILE"]=$data["U_MOBILE"];
   					$__MOBILE=$data["U_MOBILE"];
   				}else{
   					$uData["U_MOBILE"]=$data["U_EMAIL"];
   				}
   				$uData["U_PHONE"]=$data["U_PHONE"];
   				$uData["CREATED_AT"]=time();
   				$uData["UPDATED_AT"]=time();
   				$uData["U_WRONG_TIMES"]=time();
   				$uData["U_LOG_TIMES"]=time();
   				$uData["G_ID"]=1;
   				$CU_ID=$user->add($uData);
   				
   				$ZCTime=time();
			}
			if($CU_ID){
   				$Customer=D("Customer");
   				$cData=array();
   				$sear1["U_ID"]=$CU_ID;
   				$_cData=$Customer->where($sear1)->select();
   				if($_cData){
   					
   				}else{
   					$cData["U_ID"]=$CU_ID;
   					$cData["CS_REG_TIME"]=$ZCTime;
   					$Customer->add($cData);
   				}
				
   				
   			}
   			$order=D('Service_order');
   			$s=array();
   			
			$__otype='1';
   			$__otypename="报装单";
   			if($data["O_TYPE"]){
   				$__otype=$data["O_TYPE"];
   			}
   			$orderNo=getOrderNumber();
   			if($__otype==2){
   				$orderNo=getOrderNumber('X');
   				$__otypename="报修单";
   			}
   			$s["O_NO"]=$orderNo;
   			$s["O_S_TYPE"]=$data["O_S_TYPE"];
   			
   			$s["P_COUNT"]=$data["P_COUNT"];
   			$s["O_NAME"]=$__otypename;
   			$s["O_TYPE"]=$__otype;
   			$s["O_STATE"]=C("ORDER_STATE.STATE_PRE_DPF");
   			$s["CU_ID"]=$CU_ID;
   			$s["CU_NAME"]=$data["U_RNAME"]?$data["U_RNAME"]:'';
   			//查找地区ID
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
   			$s["O_ADDRESS"]=$data["O_ADDRESS"];
   			$s["O_PHONE"]=$data["U_PHONE"];
   			$s["O_MOBILE"]=$data["U_MOBILE"];
   			$s["O_EMAIL"]=$data["U_EMAIL"];
   			
   			$product=D("Product");
   			$_ps=array();
   			$_ps["P_BRAND"]=$data["P_BRAND"];
   			$_ps["P_CATEGORY"]=$data["P_CATEGORY"];
   			$_ps["P_MODEL"]=$data["P_MODEL"];
   			$pdata=$product->where($_ps)->select();
   			$P_ID='';
   			if($pdata){
   				$P_ID=$pdata[0]["P_ID"];
   			}
   			
   			$s["P_ID"]=$P_ID;
   			$s["P_BRAND"]=$data["P_BRAND"];
   			$s["P_CATEGORY"]=$data["P_CATEGORY"];
   			$s["P_MODEL"]=$data["P_MODEL"];
   			$s["P_BUY_TIME"]=$data["P_BUY_TIME"]?strtotime($data["P_BUY_TIME"]):'';
   			$s["O_REQ_TIME"]=$data["O_REQ_TIME"]?strtotime($data["O_REQ_TIME"]):'';
   			
   			$__PRODUCT=$data["P_BRAND"].$data["P_CATEGORY"];
   			/*
   			$Channel=D("Channel");
   			$_cs=array();
   			$_cs["C_NMAE"]=$data["P_CHANNEL_ID"];
   			$cdata=$Channel->where($_cs)->select();
   			$C_ID='';
   			if($cdata){
   				$C_ID=$cdata[0]["C_ID"];
   			}
   			
   			$s["P_CHANNEL_ID"]=$C_ID;
   			*/
   			$s["P_CHANNEL_ID"]=$data["P_CHANNEL_ID"];
   			$s["P_CHANNEL"]=$data["P_CHANNEL"];
   			$s["SO_ID"]=$data["SO_ID"];
   			$s["O_MEMO"]=$data["O_MEMO"];;
   			$s["CREATED_AT"]=time();
   			$s["UPDATED_AT"]=time();
   			$rev=$order->add($s);
   			
   			if($rev){
	   			//写入用户角色 为客户
	   			$uRole=D("User_role");
	   			$search['U_ID']=$CU_ID;
				$search['R_ID']=4;
				$isC=$uRole->where($search)->select();
				if(!$isC){
					$uRole->add($search);
				}
				
				$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."生成服务单。";
	   			ExLog::addLog($msg, 'TYPE_ORDER_CRS',2,$rev,$orderNo);
	   			//服务单根据区域自动找服务队，并发送邮件
	   			$isTeam=ExUse::setOrderTeam($rev,$ar_id);
	   			$rev1='y';
				$info='下单成功';
				
				//发送短信及邮件
				if($__MOBILE){
					sendAdminMobile($__MOBILE,$__PRODUCT,$__SPWD);
				}
				//发送邮件
				if($__EMAIL){
					sendAdminMail($__EMAIL, '', $__PRODUCT, $__SPWD);
				}
				
   			}
   			
   			
		}
		$arr=array("info"=>$info,"status"=>$rev1,"O_ID"=>$rev);
		echo json_encode($arr);
		exit;
	}
	/**
	 * 编辑服务单
	 * Enter description here ...
	 */
	function edit(){
		$o_id=$this->in("o_id");
		if($o_id){
			$order=D('Service_order');
			$data=$order->where("O_ID=".$o_id)->select();
			
			$this->assign("uData",$data[0]);
		}
		 $pp=getProductFiled(array(),'P_BRAND');
       $this->assign('BRAND',$pp);
       
       //得到地区
       $area=getAreaFiled(array(),'AR_L1');
       $this->assign('AR_L1',$area);
       
       //得到渠道
       $channel=ExUse::getChannel();
       $this->assign('channel',$channel);
       //得到安装方式
       $__installMode=ExUse::getDictByList("INSTALL_MODE");
       $this->assign("installMode",$__installMode);
       $__type=ExUse::getDictByList("ORDER_TYPE");
       $this->assign("otype",$__type);
       $this->assign("o_id",$o_id);
		$this->display();
	}
	/**
	 * 保存编辑的服务单
	 * Enter description here ...
	 */
	function saveEdit(){
		$data=$this->in("data");
		$rev=0;
		$rev1='n';
		$info='编辑失败';
		if($data){
			$order=D('Service_order');
			$order->find($data["O_ID"]);
			$order->O_S_TYPE=$data["O_S_TYPE"];
   			$order->P_COUNT=$data["P_COUNT"];
   			$product=D("Product");
   			$_ps=array();
   			$_ps["P_BRAND"]=$data["P_BRAND"];
   			$_ps["P_CATEGORY"]=$data["P_CATEGORY"];
   			$_ps["P_MODEL"]=$data["P_MODEL"];
   			$pdata=$product->where($_ps)->select();
   			$P_ID='';
   			if($pdata){
   				$P_ID=$pdata[0]["P_ID"];
   			}
   			
   			$order->P_ID=$P_ID;
   			$order->P_BRAND=$data["P_BRAND"];
   			$order->P_CATEGORY=$data["P_CATEGORY"];
   			$order->P_MODEL=$data["P_MODEL"];
   			$order->P_BUY_TIME=$data["P_BUY_TIME"]?strtotime($data["P_BUY_TIME"]):'';
   			$order->P_CHANNEL_ID=$data["P_CHANNEL_ID"];
   			$order->P_CHANNEL=$data["P_CHANNEL"];
   			$order->SO_ID=$data["SO_ID"];
   			$order->O_MEMO=$data["O_MEMO"];
   			$order->UPDATED_AT=time();
   			$ono=$order->O_NO;
   			$rev=$order->save();
   			if($rev){
   				$rev1='y';
				$info='编辑成功';
				$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."修改服务单。";
	   			ExLog::addLog($msg, 'TYPE_ORDER_CRS',2,$data["O_ID"],$ono);
   			}
   			
		}
		$arr=array("info"=>$info,"status"=>$rev1);
		echo json_encode($arr);
		exit;
	}
}