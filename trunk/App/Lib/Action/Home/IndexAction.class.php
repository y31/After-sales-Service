<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends BaseAction{
	
    public function index(){
    	
	   //取菜单
	   $menus=getMenu();
	   $pp=array();
       $pp=getProductFiled(array(),'P_BRAND');
       $this->assign('BRAND',$pp);
       
       //得到地区
       $area=getAreaFiled(array(),'AR_L1');
       $this->assign('AR_L1',$area);
       
       //得到渠道
       $channel=ExUse::getChannel();
       $this->assign('channel',$channel);
       
       //得到订单号 该报装单唯一
       $u_no=getOrderNumber();
       $this->assign('orderNO',$u_no);
       
	   $this->assign('menu',$menus);
	   //哪个菜单选中
	   $this->assign("cColor",'1');
	   $this->assign("ordertype",1);
	   $this->assign("PCOUNT",C("P_COUNT"));
        //输出模板
       $this->display();

    }
    /**
     * 报装保修表单
     * Enter description here ...
     */
    function bg(){
    	//品牌
    	$pp=array();
    	//取所有产品的品牌
    	$pp=getProductFiled(array(),'P_BRAND');
    	$this->assign('BRAND',$pp);
    	
    	//得到地区
       $area=getAreaFiled(array(),'AR_L1');
       $this->assign('AR_L1',$area);
    	$this->display();
    }
    /**
     * 报装
     * Enter description here ...
     */
    function viewbg(){
    	//取数据
       $sid=$this->in('sid');
       
       $islogin=$this->in("islogin");
       if(!$islogin){
       		$islogin='n';
	       if($_SESSION[C("USER_AUTH_KEY")]){
	       	$islogin='y';
	       }
       }
       if($sid){
       		 $seData=$_SESSION[$sid];
       }else{
       		if(ExSession::getSession()->getEMail()){
       			$seData=$_SESSION[ExSession::getSession()->getEMail()];
       			$islogin='y';
       		}
       }
    	 //取菜单
	   $menus=getMenu();
	   $pp=array();
       $pp=getProductFiled(array(),'P_BRAND');
       $this->assign('BRAND',$pp);
       
       //得到地区
       $area=getAreaFiled(array(),'AR_L1');
       $this->assign('AR_L1',$area);
       
       //得到渠道
       $channel=ExUse::getChannel();
       $this->assign('channel',$channel);
       
       $sid=$this->in('sid');
       $seDatas=array();
       if($sid){
       		$seDatas=$seData;
       		$u_no=$sid;
       }else{
       		//得到订单号 该报装单唯一
       		$u_no=getOrderNumber();
       		if(ExSession::getSession()->getEMail()){
       			$seDatas=$seData;
       		}
       }
      
       $this->assign('islogin',$islogin);
       $this->assign('userRealName',ExSession::getSession()->getRealName());
       $this->assign('seData',$seDatas);
       $this->assign('orderNO',$u_no);
       $this->assign("ordertype",1);
	   $this->assign('menu',$menus);
	   $this->assign("PCOUNT",C("P_COUNT"));
	   $this->assign("cColor",'1');
	   $this->display();
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
   /**
    * 提交报装单
    * Enter description here ...
    */
   function confirmOrder(){
   		$order=$this->in("data");
   		
   		$user=D("User");
   		//登录的用户不判断 手机 及邮箱是否重复
   		if($_SESSION [C('USER_AUTH_KEY')]){
   			
   		}else{
	   		//检查手机号码是否重复
	   		$search=array();
	   		$search["U_MOBILE"]=$order["U_MOBILE"];
	   		$uData=$user->where($search)->select();
	   		if($uData){
	   			echo "M";
	   			exit;
	   		}
	   		//检查邮箱是否重复
	   		$search=array();
	   		$search["U_EMAIL"]=$order["U_EMAIL"];
	   		$uData=$user->where($search)->select();
	   		if($uData){
	   			echo "E";
	   			exit;
	   		}
   		}
   		
   		$orderNO=$order["orderNO"];//$this->in("orderNO");
   		if(strstr($orderNO,C("SESSION_PREFIX"))){
   			$sId=$orderNO;
   		}else{
   			$sId=C("SESSION_PREFIX").$orderNO;
   		}
   		$_SESSION[$sId]='';
   		$_SESSION[$sId]=$order;
   		echo $sId;
   		exit;
   }
   function viewCfOrder(){
   		//取菜单
   		
	  	$menus=getMenu();
	  	$this->assign('menu',$menus);
	  	$sId=$this->in('sid');
	  	$this->assign("sid",$sId);
	  	
	  	$data=$_SESSION[$sId];
	  	$this->assign("data",$data);
	  	
	  	$__type='1';
   		if($data["O_TYPE"]){
   			$__type=$data["O_TYPE"];
   		}
   		$this->assign("ordertype",$__type);
   		$this->display();
   }
   /**
    * 保存报装单
    * Enter description here ...
    */
   function saveBgOrder(){
   		$sid=$this->in("sid");
   		$data=$_SESSION[$sid];
   		$__EMAIL='';
   		$__PRODUCT='';
   		$rev="n";
   		if($data && is_array($data) && count($data) ){
   			//先查看客户是否存在   如果存在去信息 否则添加可以信息  
   			$user=D("User");
   			$search["U_NAME"]=$data["U_EMAIL"];
   			$__EMAIL=$data["U_EMAIL"];
   			$uData=$user->where($search)->select();
   			if($uData){
   				$CU_ID=$uData[0]["U_ID"];
   				$ZCTime=$uData[0]["CREATED_AT"];
   			}else{
   				$uData["U_GU_ID"]=getGuId();
   				$uData["U_NAME"]=$data["U_EMAIL"];
   				$uData["U_RNAME"]=$data["U_RNAME"];
   				$uData["U_PWD"]=md5($data["U_PWD"]);
   				$uData["U_EMAIL"]=$data["U_EMAIL"];
   				if($data["U_MOBILE"]){
   					$uData["U_MOBILE"]=$data["U_MOBILE"];
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
   			if(strstr($sid, C("SESSION_PREFIX"))){
   				$orderNo=explode(C("SESSION_PREFIX"), $sid);
   				$orderNo=$orderNo[1];
   			}else{
   				$orderNo=$sid;
   			}
   			$s["O_NO"]=$orderNo;
   			$__otype='1';
   			$__otypename="报装单";
   			if($data["O_TYPE"]){
   				$__otype=$data["O_TYPE"];
   			}
   			if($__otype==2){
   				$__otypename="报修单";
   			}
   			$s["O_NAME"]=$__otypename;
   			
   			$s["O_TYPE"]=$__otype;
   			$s["O_STATE"]=C("ORDER_STATE.STATE_PRE_DPF");
   			$s["CU_ID"]=$CU_ID;
   			$s["CU_NAME"]=$data["U_RNAME"];
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
   			$s["P_BUY_TIME"]=strtotime($data["P_BUY_TIME"]);
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
   			$s["O_MEMO"]=$data["O_MEMO"];
   			$s["CREATED_AT"]=time();
   			$s["UPDATED_AT"]=time();
   			$s["P_COUNT"]=$data["P_COUNT"];
   			$s["O_S_TYPE"]=1;
   			$rev=$order->add($s);
   		}
   		if($rev){
   			
   			//写入用户角色 为客户
   			$uRole=D("User_role");
   			$search['U_ID']=$CU_ID;
			$search['R_ID']=4;
			$isC=$uRole->where($search)->select();
			if(!$isC){
				$uRole->add($search);
			}
   			
   			//保存完订单后自动登录
   			$search=array();
   			$user=D("User");
			//先查用户
			$search["U_ID"]=$CU_ID;
			$_udata=$user->where($search)->select();
   			ExSession::getSession()->setUserByArray($_udata[0]);
   			$_user=$_udata[0];
   			$_user["U_PWD"]=$data["U_PWD"];
   			$_user["U_PWD1"]=$data["U_PWD"];
   			ExSession::getSession()->setSessionByArrayAndPid($_user);
   			$msg=$_user["U_RNAME"]."在".formatDate(time())."生成服务单。";
   			ExLog::addLog($msg, 'TYPE_ORDER_CR',2,$rev,$orderNo);
   			//服务单根据区域自动找服务队，并发送邮件
   			ExUse::setOrderTeam($rev,$ar_id);
   			echo $orderNo;
   			sendAdminMail($__EMAIL, '', $__PRODUCT);
   		}else{
   			echo 'n';
   		}
   		//echo json_encode($rev);
   		exit;
   }
   /**
    * 导出EXCEL
    * Enter description here ...
    */
   function test(){
   /*
   	$arr=array(
   	"0"=>array("标题一","标题二"),"1"=>array("数据a","数据b"),"2"=>array("数据a","数据b")
   	);
	$path=Excel::ExportToXls($arr,'bbb');
	$this->assign('path',$path);
	$this->display();
	*/
   	/*$data=array('万家乐'=>array(
			'燃气热水器'=>array(
				"LJSQ20-12UF3",
				"LJSQ21-12UF3",
				"LJSQ18-10UF3",
				"LJSQ24-16U1",
				"LJSQ20-12U1",
				"LJSQ21-12U1",
				"LJSQ18-10U1",
				"LJSQ20-12UF1",
				"LJSQ18-10UF6",
				"JSQ20-10ZH3",
				"JSQ30-16Z3",
				"JSQ24-12Z3",
				"JSQ20-10Z3(北方)",
				"JSQ20-10Z3",
				"JSQ20-10JP",
				"JSQ20-10JP（20Y）",
				"JSQ24-12JP",
				"JSQ20-10L5",
				"JSQ16-8L2",
				"JSQ16-8L2（20Y）"
			),
			'电热水器'=>array(
				"D40-HK6F",
				"D50-HK6F",
				"D60-HK6F",
				"D40-GHF(B)",
				"D45-HG3F",
				"D55-HG3F",
				"D45-HV5F",
				"D55-HV5F"
			),
			'吸油烟机'=>array(
				"CXW-200-S3J03A",
				"CXW-200-G3W07",
				"CXW-218-IET02",
				"CXW-200-IEH01",
				"CXW-200-IGL02",
				"CXW-200-G3T01",
				"CXW-200-E6R06",
				"CXW-200-G3H01",
				"CXW-200-G3T01A",
				"CXW-218-E3W06",
				"CXW-218-ED389（05）",
				"CXW-200-S3J02"
			),
			'燃气灶'=>array(
				"JZT-QM19",
				"JZT-QM19B",
				"JZT-QM21B",
				"JZT-QL3",
				"JZY-QL3（20Y）",
				"JZT-QC3",
				"JZT-QMW01",
				"JZT-QMH01B",
				"JZT-TB1"
			)
		));
		$p=D("Product");
		$rev=array();
		foreach ($data as  $key=>$val){
			$rev["P_BRAND"]=$key;
			foreach ($val as $k1=>$v1){
				$rev["P_CATEGORY"]=$k1;
				foreach ($v1 as $v){
					$rev["P_MODEL"]=$v;
					$rev["CREATED_AT"]=time();
					$rev["UPDATED_AT"]=time();
					$p->add($rev);
				}
			}
		}
		exit;
		*/
   	
   //	Mail::getMail()->sendMail('lijx.watt@163.com','测试',"测试");
   //echo ExLog::addLog("测试", 'USER_TEST',2,1,1);
   //$d=Mobile::getMobile()->send('13641347592','测试短信内容');
   //$__pwd=mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
   //sendAdminMobile('13641347592','万家乐',$__pwd);
   ExUse::sendSRVTeamMail(2);
   	exit;
   	
   	//$this->display();
	
   }
   /**
    * 信息提示页面
    * 1:正确
    * Enter description here ...
    */
   function info(){
   		$type=$this->in("type");//类型   (是否成功)  1:成功   2:失败
   		$info=$this->in("info"); //传递过来的提示西悉尼
   		$url=$this->in("url");   //跳转的URL
   		$this->assign('type',$type);
   		$this->assign('info',$info);
   		$this->assign('url',$url);
   		$menus=getMenu();
	   	$this->assign('menu',$menus);
   		$this->display();
   		
   }
   /**
    * 退出
    * Enter description here ...
    */	
   function logout(){
   		$url="cr";
   		if(ExSession::getSession()->getRoleShortname()=="SRVTEAM"){
   			$url="srv";
   		}
   		session_destroy();
   		//setcookie("locationAutonLoginJKJ",'',time()-36000);
   		ExLog::addLog(ExSession::getSession()->getRealName()."退出登录", "USER_LOGOUT");
		echo $url;
		exit;
   }
   /**
    * 保修单
    * Enter description here ...
    */
   function viewbx(){
   		//取数据
       $sid=$this->in('sid');
       
       $islogin=$this->in("islogin");
       if(!$islogin){
       		$islogin='n';
	       if($_SESSION[C("USER_AUTH_KEY")]){
	       	$islogin='y';
	       }
       }
       if($sid){
       		 $seData=$_SESSION[$sid];
       }else{
       		if(ExSession::getSession()->getEMail()){
       			$seData=$_SESSION[ExSession::getSession()->getEMail()];
       			$islogin='y';
       		}
       }
    	 //取菜单
	   $menus=getMenu();
	   $pp=array();
       $pp=getProductFiled(array(),'P_BRAND');
       $this->assign('BRAND',$pp);
       
       //得到地区
       $area=getAreaFiled(array(),'AR_L1');
       $this->assign('AR_L1',$area);
       
       //得到渠道
       $channel=ExUse::getChannel();
       $this->assign('channel',$channel);
       
       $sid=$this->in('sid');
       $seDatas=array();
       if($sid){
       		$seDatas=$seData;
       		$u_no=$sid;
       }else{
       		//得到订单号 该报装单唯一
       		$u_no=getOrderNumber('X');
       		if(ExSession::getSession()->getEMail()){
       			$seDatas=$seData;
       		}
       }
      
       $this->assign('islogin',$islogin);
       $this->assign('userRealName',ExSession::getSession()->getRealName());
       $this->assign('seData',$seDatas);
       $this->assign('orderNO',$u_no);
       $this->assign("ordertype",2);
	   $this->assign('menu',$menus);
	   $this->assign("PCOUNT",C("P_COUNT"));
	   $this->assign("cColor",'1');
	   $this->display();
   }
   /**
    * 服务流程
    * Enter description here ...
    */
   function srvflow(){
   		//取菜单
	   	$menus=getMenu();
	   	$this->assign('menu',$menus);
   		if(ExSession::getSession()->getRoleShortname()=='SRVTEAM'){
   			$this->assign("cColor",'2');
			
		}
		else{
			$this->assign("cColor",'3');
		}
   		$this->display();
   }
}