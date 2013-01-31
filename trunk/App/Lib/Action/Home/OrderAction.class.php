<?php
class OrderAction extends BaseAction{
	
	/**
	 * 客户看到的订单
	 * Enter description here ...
	 */
	function olist(){
		
		//每页显示多少个
		$pageSize=5;
		
		
		$order=D("Service_order");
		$search=array();
		$outSearch=array();
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
			$_ostate=$this->in("O_STATE");
			$outSearch["O_STATE"]=$_ostate;
			if($_ostate==10){
				$search["O_STATE"]=10;
			}else if($_ostate==20){
				$search["O_STATE"]=array('in',array(20,30,40,60,70,80));
			}
			else if($_ostate==30){
				$search["O_STATE"]=50;
			}
			//$search["O_STATE"]=$this->in("O_STATE");
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
		$search['CU_ID']=ExSession::getSession()->getUserId();
	
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
			$order1=D("Service_order");
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
				$v["CREATED_AT"]=date("Y-m-d H:i",$v["CREATED_AT"]);
				$v["O_STATE"]=ExUse::getOrderState($v["O_STATE"]);
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
		$types=C("ORDER_TYPE");
		$type=array();
		$type[]=array('id'=>'','name'=>'全部');
		foreach ($types as $tk=>$tv){
			$type[]=array('id'=>$tk,'name'=>$tv);
		}
		//$type=array(array('id'=>'','name'=>'全部'),array('id'=>'1','name'=>'报装'),array('id'=>'2','name'=>'保修'));
		$state=ExUse::getCROrderStateArr();
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
		$Page->setConfig('theme', "总订单数 %totalRow%    &nbsp;|&nbsp;%nowPage%/%totalPage% 页     &nbsp;&nbsp;    %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%");
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		
		$menus=getMenu();
		$this->assign('menu',$menus);
		
		//哪个菜单选中
	    $this->assign("cColor",'2');
	   	$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."查看服务单。";
   		ExLog::addLog($msg, 'TYPE_ORDER_VIEW');
		$this->display();
	}
	/**
	 * 服务队看到的订单
	 * Enter description here ...
	 */
	function srvlist(){
		$stype=$this->in("type");
		if(!$stype){
			$stype=1;
		}
		$this->assign("stype",$stype);
		$search=array();
		//$search["IS_BLOCK"]=0;//过滤锁定
		//$search["IS_DEL"]=0;//过滤
		//找到登陆者的服务队
		$s_ar_id='';
		$st_id='';
		$u_id=ExSession::getSession()->getUserId();
		$sst=D("Srv_team_members");
		$sst->find($u_id);
		if($sst->IS_BLOCK==1 || $sst->IS_DEL==1){//过滤锁定或删除
			//$search["ST_ID"]=0;
		}else{
			$st_id=$sst->ST_ID;
			if($st_id){
				/*$st=D("Srv_team");
				$st->find($st_id);
				$s_ar_id=$st->ST_AR_ID;
				*/
				$st=D("srv_team_areas");
				$st1=array();
				$st1["ST_ID"]=$st_id;
				$stas=$st->where($st1)->select();
				if($stas){
					foreach ($stas as $val){
						if($s_ar_id==''){
							$s_ar_id=$val["AR_ID"];
						}else{
							$s_ar_id=$s_ar_id.",".$val["AR_ID"];
						}
					}
				}
			}
		}
		if($stype==1){//待处理    规则：地区符合 并且没有服务队
			//$search["O_STATE"]='10';
			//$search["AR_ID"]=array('in',$s_ar_id);
			$search["ST_ID"]=$st_id;
			//$search['_string']="'AR_ID' in (".$s_ar_id.") OR  'ST_ID'='".$st_id."'";
			$search["O_STATE"]=array('in',array(10,20));
		}else if($stype==2){
			$search["O_STATE"]=array('in',array(30));
			$search["ST_ID"]=$st_id;
		}else if($stype==3){
			$search["O_STATE"]=array('in',array(40,50,60,70,80));
			$search["ST_ID"]=$st_id;
		}
		/*
		if($s_ar_id && $st_id){
			$search["_string"]="AR_ID ={$s_ar_id} or ST_ID={$st_id}";
		}else{
			$search["_string"]="AR_ID =0 and ST_ID=0";
		}
		*/
		//每页显示多少个
		$pageSize=5;
		
		
		$order=D("Service_order");
		
		$outSearch=array();
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
			$_ostate=$this->in("O_STATE");
			//$search["O_STATE"]=$_ostate;
			$outSearch["O_STATE"]=$_ostate;
			
			
			if($_ostate==10){
				$search["O_STATE"]=10;
			}else if($_ostate==20){
				$search["O_STATE"]=array('in',array(20,30,40,60,70,80));
			}
			else if($_ostate==30){
				$search["O_STATE"]=50;
			}
			
			//$search["O_STATE"]=$this->in("O_STATE");
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
			$search["CU_NAME"]=array("like","%".$this->in("CU_NAME")."%");
		}
		//用户客户ID精确查询
		/*if($this->in("CU_ID")){
			$outSearch["CU_ID"]=$this->in("CU_ID");
			$search["CU_ID"]=$this->in("CU_ID");
		}*/
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
		//$search['CU_ID']=ExSession::getSession()->getUserId();
		$search["IS_DEL"]=0;
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
				$v["CREATED_AT"]=date("Y-m-d H:i",$v["CREATED_AT"]);
				$v["O_STATE"]=ExUse::getOrderState($v["O_STATE"]);
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
		$types=C("ORDER_TYPE");
		$type=array();
		$type[]=array('id'=>'','name'=>'全部');
		foreach ($types as $tk=>$tv){
			$type[]=array('id'=>$tk,'name'=>$tv);
		}
		//$type=array(array('id'=>'','name'=>'全部'),array('id'=>'1','name'=>'报装'),array('id'=>'2','name'=>'保修'));
		$state=ExUse::getCROrderStateArr();
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
		$Page->setConfig('theme', "总订单数 %totalRow%    &nbsp;|&nbsp;%nowPage%/%totalPage% 页     &nbsp;&nbsp;    %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%");
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		
		$menus=getMenu();
		$this->assign('menu',$menus);
		$this->assign("cColor",'1');
		//哪个菜单选中
	   	//$this->assign("cColor",'0');
	   	$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."查看服务单。";
   		ExLog::addLog($msg, 'TYPE_ORDER_VIEW');
		$this->display();
	}
	
	/**
	 * 显示服务单详情
	 * Enter description here ...
	 */
	function detail(){
		$O_ID=$this->in("O_ID");
		$order=D("Service_order");
		$data=$order->where('O_ID='.$O_ID)->select();
		if($data){
			$tmpData=$data[0];
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
			if($this->in("zp")){
				//看看是否存在服务队
				$u_id=ExSession::getSession()->getUserId();
				$u_name=ExSession::getSession()->getRealName();
				$sst=D("Srv_team_members");
				$sst->find($u_id);
				$st_id=$sst->ST_ID;
				if($st_id){
					$st=D("Srv_team");
					$st->find($st_id);
					$ST_NAME=$st->ST_NAME;
					$order=D("Service_order");
					$order->find($O_ID);
					$order->ST_ID=$st_id;
					$order->ST_NAME=$ST_NAME;
					$order->SRV_ID=$u_id;
					$order->SRV_NAME=$u_name;
					$order->O_SRV_B_TIME=time();//服务开始时间
					$order->O_STATE=C("ORDER_STATE.STATE_EXECUTING");//设置状态为已派发
					$logOid=$O_ID;
					$logONo=$order->O_NO;
					$rev=$order->save();
					if($rev){
						$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."处理服务单。";
	   					ExLog::addLog($msg,'TYPE_ORDER_EXECUTE',2,$logOid,$logONo);
					}
				}
			}
			//只有服务队查看才能改变状态
			if($tmpData["O_STATE"]<30 && ExSession::getSession()->getRoleShortname()=='SRVTEAM'){
				$order=D("Service_order");
				$order->find($O_ID);
				$order->O_STATE=C("ORDER_STATE.STATE_EXECUTING");//设置状态为进行中
				if($order->save()){
					$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."处理服务单。";
	   				ExLog::addLog($msg,'TYPE_ORDER_EXECUTE',2,$tmpData["O_ID"],$tmpData["O_NO"]);
				}
				
				
			}
		}
		//取服务队的电话
		$t_id=$tmpData["ST_ID"];
		$__team=D("Srv_team");
		$__team->find($t_id);
		$ST_MOBILE=$__team->ST_MOBILE;
		$ST_PHONE=$__team->ST_PHONE;
		$tmpData["ST_MOBILE"]=$ST_MOBILE;
		$tmpData["ST_PHONE"]=$ST_PHONE;
		$menus=getMenu();
		$this->assign('menu',$menus);
		
		$this->assign("data",$tmpData);
		if(ExSession::getSession()->getRoleShortname()=='CR'){
			$this->assign("cColor",'2');
		}
		else{
			$this->assign("cColor",'1');
		}
		$this->display();
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
			$msg=ExSession::getSession()->getStName()."在".formatDate(time())."处理服务单。改变状态为:".ExUse::getOrderState($state);
   			ExLog::addLog($msg, 'TYPE_ORDER_CHANGE_STATE',2,$logOid,$logONo);
			echo "y";
		}else{
			echo "n";
		}
		exit;
	}
	/**
	 * 服务队点击完成
	 * Enter description here ...
	 */
	function finish(){
		$O_ID=$this->in("o_id");
		/*if($this->in("op")){
			$rev='n';
			$data=$this->in("data");
			if(is_array($data) && $data){
				$order=D("Service_order");
				$order->find($O_ID);
				$order->P_INSTALL_CARD=$data["hidFileID"];
				$order->P_BAR_CODE=$data["P_BAR_CODE"];
				$order->O_SRV_TIME=$data["O_SRV_TIME"]?strtotime($data["O_SRV_TIME"]):time();
				$order->O_ST_MEMO=$data["O_ST_MEMO"];
				$order->O_STATE=40;
				
				$logOid=$order->O_ID;
				$logONo=$order->O_NO;
				if($order->save()){
					$rev='y';
					$msg=ExSession::getSession()->getStName()."在".formatDate(time())."处理服务单。改变状态为:".ExUse::getOrderState(40);
	   				ExLog::addLog($msg, 'TYPE_ORDER_CHANGE_STATE',2,$logOid,$logONo);
				}
			}
			echo $rev;
			exit;
		}
		*/
		$this->assign('o_id',$O_ID);
		$extName=C("ORDER_CARD");
		$this->assign("extName",implode(';', $extName));
		$this->assign("filesize",C("SYS_UPLOAD_CARD_SIZE"));
		$this->display();
	}
	function savefh(){
		$O_ID=$this->in("o_id");
		$rev='n';
		$data=$this->in("data");
		if(is_array($data) && $data){
			$order=D("Service_order");
			$order->find($O_ID);
			$order->P_INSTALL_CARD=$data["hidFileID"];
			$order->P_BAR_CODE=$data["P_BAR_CODE"];
			$order->O_SRV_TIME=$data["O_SRV_TIME"]?strtotime($data["O_SRV_TIME"]):time();
			$order->O_ST_MEMO=$data["O_ST_MEMO"];
			$order->O_STATE=40;
			
			$logOid=$order->O_ID;
			$logONo=$order->O_NO;
			if($order->save()){
				$rev='y';
				$msg=ExSession::getSession()->getStName()."在".formatDate(time())."处理服务单。改变状态为:".ExUse::getOrderState(40);
   				ExLog::addLog($msg, 'TYPE_ORDER_CHANGE_STATE',2,$logOid,$logONo);
			}
		}
		//echo $rev;
		//exit;
		$this->assign("rev",$rev);
		$this->assign("oid",$O_ID);
		$this->display();
	}
	/**
	 * 上传安装卡
	 * Enter description here ...
	 */
	function upload(){
		
		if (isset($_FILES["resume_file"]) && is_uploaded_file($_FILES["resume_file"]["tmp_name"]) && $_FILES["resume_file"]["error"] == 0) {
			$__size=$_FILES["resume_file"]["size"]/1000;
			if(C("SYS_UPLOAD_CARD_SIZE")<$__size){
				echo "size";
				exit;
			}
			
			//import("ORG.Net.UploadFile");
			// 实例化上传类
			//$upload = new UploadFile();
			$__shijian=date("Ym",time());
			$up_url=C("SYS_UPLOAD_CARD_DIR").$__shijian;
			if(is_dir($up_url)){
				
			}else{
				mkdir($up_url,0777);
				
			}
			$_oid=$this->in("o_id");
			$order=D("Service_order");
			$order->find($O_ID);
			$o_no=$order->O_NO;
			if(!$o_no){
				$o_no=getOrderNumber();
			}
			$up_url.="/".$_oid;
			if(is_dir($up_url)){
				
			}else{
				mkdir($up_url,0777);
				
			}
			
			$_file=basename($_FILES["resume_file"]["name"]);
			
			$fileExt=getFileExt($_file);
			//$fileNotExt=getFileNoExt($__file);
			$__file=$o_no.".".$fileExt;
			$__url=$up_url."/".$__file;
			if(file_exists($__url)){
				$_rand=mt_rand(1, 10);
				$__file=$o_no."-".$_rand.".".$fileExt;
				$__url=$up_url."/".$__file;
			}
			if(move_uploaded_file($_FILES["resume_file"]["tmp_name"], $__url)){
				echo $__url;
			}
			else{
				echo "n";
			}
			
			/*
			// 设置附件上传目录
			$upload->savePath =$up_url."/";
			
			$__f_url=$up_url."/".$_FILES["resume_file"]["name"];
			//判断文件是否存在如果存在那么先删除 再上传
			// 上传错误提示错误信息
			if(!$upload->upload()) {
				//检查文件是否重名
			
				if(is_file($__f_url)){
					//echo "name";
					$__file=basename($_FILES["resume_file"]["name"]);
					$_rand=mt_rand(1, 10);
					$fileExt=getFileExt($__file);
					$fileNotExt=getFileNoExt($__file);
					$__file=$fileNotExt."-".$_rand.".".$fileExt;
					$__url=$up_url."/".$__file;
					if(move_uploaded_file($_FILES["resume_file"]["tmp_name"], $__url)){
						echo $__url;
					}
					else{
						echo "n";
					}
					
				}else{
					echo "n";
				}
			}else{
				// 上传成功 获取上传文件信息
			
				$info =  $upload->getUploadFileInfo();
				echo $info[0]["savepath"].$info[0]["savename"];
			}
			*/
		}else{
			echo "n";
		}
		exit;
	}
	
	function fh(){
		$O_ID=$this->in("o_id");
		if($this->in("op")){
			if (isset($_FILES["files"]) && is_uploaded_file($_FILES["files"]["tmp_name"]) && $_FILES["files"]["error"] == 0) {
				$__size=$_FILES["files"]["size"]/1000;
				if(C("SYS_UPLOAD_CARD_SIZE")<$__size){
					echo "size";
					exit;
				}
			}
			
			import("ORG.Net.UploadFile");
			// 实例化上传类
			$upload = new UploadFile();
			$up_url=C("SYS_UPLOAD_CARD_DIR").$O_ID;
			if(is_dir($up_url)){
				
			}else{
				mkdir($up_url,0777);
				
			}
			// 设置附件上传目录
			$f_url='';
			$upload->savePath =$up_url."/";
			if(!$upload->upload()) {
				echo "file";
				exit;
			}else{
				$info =  $upload->getUploadFileInfo();
				$f_url= $info[0]["savepath"].$info[0]["savename"];
			}
			//$__f_url=$up_url."/".$_FILES["resume_file"]["name"];
			
			$rev='n';
			$data=$this->in("data");
			if(is_array($data) && $data){
				$order=D("Service_order");
				$order->find($O_ID);
				$order->P_INSTALL_CARD=$f_url;
				$order->P_BAR_CODE=$data["P_BAR_CODE"];
				$order->O_SRV_TIME=$data["O_SRV_TIME"]?strtotime($data["O_SRV_TIME"]):time();
				$order->O_ST_MEMO=$data["O_ST_MEMO"];
				$order->O_STATE=40;
				
				$logOid=$order->O_ID;
				$logONo=$order->O_NO;
				if($order->save()){
					$rev='y';
					$msg=ExSession::getSession()->getStName()."在".formatDate(time())."处理服务单。改变状态为:".ExUse::getOrderState(40);
	   				ExLog::addLog($msg, 'TYPE_ORDER_CHANGE_STATE',2,$logOid,$logONo);
				}
			}
			echo $rev;
			exit;
			
		}
		
		$this->assign('o_id',$O_ID);
		$extName=C("ORDER_CARD");
		$this->assign("extName",implode(',', $extName));
		$this->assign("filesize",C("SYS_UPLOAD_CARD_SIZE"));
		$this->display();
	}
	
	function upfile(){
		$O_ID=$this->in("o_id");
		$out='n';
		if($this->in("op")){
			if (isset($_FILES["files"]) && is_uploaded_file($_FILES["files"]["tmp_name"]) && $_FILES["files"]["error"] == 0) {
				$__size=$_FILES["files"]["size"]/1000;
				if(C("SYS_UPLOAD_CARD_SIZE")<$__size){
					echo "size";
					exit;
				}
			}
			
			import("ORG.Net.UploadFile");
			// 实例化上传类
			$upload = new UploadFile();
			$up_url=C("SYS_UPLOAD_CARD_DIR").$O_ID;
			if(is_dir($up_url)){
				
			}else{
				mkdir($up_url,0777);
				
			}
			// 设置附件上传目录
			$f_url='';
			$upload->savePath =$up_url."/";
			if(!$upload->upload()) {
				echo "file";
				exit;
			}else{
				$info =  $upload->getUploadFileInfo();
				$f_url= $info[0]["savepath"].$info[0]["savename"];
			}
			//$__f_url=$up_url."/".$_FILES["resume_file"]["name"];
			
			$rev='n';
			$data=$this->in("data");
			if(is_array($data) && $data){
				$order=D("Service_order");
				$order->find($O_ID);
				$order->P_INSTALL_CARD=$f_url;
				$order->P_BAR_CODE=$data["P_BAR_CODE"];
				$order->O_SRV_TIME=$data["O_SRV_TIME"]?strtotime($data["O_SRV_TIME"]):time();
				$order->O_ST_MEMO=$data["O_ST_MEMO"];
				$order->O_STATE=40;
				
				$logOid=$order->O_ID;
				$logONo=$order->O_NO;
				if($order->save()){
					$rev='y';
					$msg=ExSession::getSession()->getStName()."在".formatDate(time())."处理服务单。改变状态为:".ExUse::getOrderState(40);
	   				ExLog::addLog($msg, 'TYPE_ORDER_CHANGE_STATE',2,$logOid,$logONo);
				}
			}
			echo $rev;
			exit;
			
		}
		$this->display();
	}
	
	function obreak(){
		$O_ID=$this->in("o_id");
		if($this->in("op")){
			$rev='n';
			$data=$this->in("data");
			if(is_array($data) && $data){
				$order=D("Service_order");
				$order->find($O_ID);
				$__list=$data["P_BREAK_TYPE"];
				$order->P_BREAK_TYPE=implode(",", $__list);
				
				$order->O_ST_MEMO=$data["O_ST_MEMO"];
				$order->O_STATE=60;
				
				$logOid=$order->O_ID;
				$logONo=$order->O_NO;
				if($order->save()){
					$rev='y';
					$msg=ExSession::getSession()->getStName()."在".formatDate(time())."处理服务单。改变状态为:".ExUse::getOrderState(60);
	   				ExLog::addLog($msg, 'TYPE_ORDER_CHANGE_STATE',2,$logOid,$logONo);
				}
			}
			echo $rev."|".$O_ID;
			exit;
		}
		$this->assign("BRAK_TYPE",getDictArray("ORDER_BREAK_TYPE"));
		$this->assign("o_id",$O_ID);
		$this->display();
	}
	
	function showImage(){
		$O_ID=$this->in("o_id");
		$order=D("Service_order");
		$order->find($O_ID);
		$P_INSTALL_CARD=$order->P_INSTALL_CARD;
		$this->assign("imageURL",$P_INSTALL_CARD);
		$this->display();
	}
	/**
	 * 打印服务单详情
	 * Enter description here ...
	 */
	function out(){
		$O_ID=$this->in("O_ID");
		$order=D("Service_order");
		$data=$order->where('O_ID='.$O_ID)->select();
		if($data){
			$tmpData=$data[0];
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
			if($this->in("zp")){
				//看看是否存在服务队
				$u_id=ExSession::getSession()->getUserId();
				$u_name=ExSession::getSession()->getRealName();
				$sst=D("Srv_team_members");
				$sst->find($u_id);
				$st_id=$sst->ST_ID;
				if($st_id){
					$st=D("Srv_team");
					$st->find($st_id);
					$ST_NAME=$st->ST_NAME;
					$order=D("Service_order");
					$order->find($O_ID);
					$order->ST_ID=$st_id;
					$order->ST_NAME=$ST_NAME;
					$order->SRV_ID=$u_id;
					$order->SRV_NAME=$u_name;
					$order->O_SRV_B_TIME=time();//服务开始时间
					$order->O_STATE=C("ORDER_STATE.STATE_EXECUTING");//设置状态为已派发
					$logOid=$O_ID;
					$logONo=$order->O_NO;
					$rev=$order->save();
					if($rev){
						$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."处理服务单。";
	   					ExLog::addLog($msg,'TYPE_ORDER_EXECUTE',2,$logOid,$logONo);
					}
				}
			}
			//只有服务队查看才能改变状态
			if($tmpData["O_STATE"]<30 && ExSession::getSession()->getRoleShortname()=='SRVTEAM'){
				$order=D("Service_order");
				$order->find($O_ID);
				$order->O_STATE=C("ORDER_STATE.STATE_EXECUTING");//设置状态为进行中
				if($order->save()){
					$msg=ExSession::getSession()->getRealName()."在".formatDate(time())."处理服务单。";
	   				ExLog::addLog($msg,'TYPE_ORDER_EXECUTE',2,$tmpData["O_ID"],$tmpData["O_NO"]);
				}
				
				
			}
		}
		
		$menus=getMenu();
		$this->assign('menu',$menus);
		
		$this->assign("data",$tmpData);
		$this->display();
	}
	/**
	 * 服务队填写预约时间
	 * Enter description here ...
	 */
	function yytime(){
		$o_id=$this->in("o_id");
		if($this->in("op")){
			$rev1="n";
			$info='预约上门时间提交失败';
			$__time=$this->in("O_SRV_APPOINT_TIME");
			$order=D("Service_order");
			$order->find($o_id);
			$order->O_SRV_APPOINT_TIME=$__time?strtotime($__time):'';
			$rev=$order->save();
			if($rev){
				$rev1="y";
				$info='预约上门时间提交成功';
			}
			$arr=array("info"=>$info,"status"=>$rev1,'oid'=>$o_id);
			echo json_encode($arr);
			exit;
		}
		$this->assign("o_id",$o_id);
		$order=D("Service_order");
		$order->find($o_id);
		$o_time=$order->O_SRV_APPOINT_TIME;
		if($o_time){
		$this->assign("O_SRV_APPOINT_TIME",$o_time);
		}
		$this->display();
	}
	
}