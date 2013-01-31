<?php
/**
 * 扩展使用的方法
 * Enter description here ...
 * @author df
 *
 */
class ExUse {
	
	/**
	 * 得到所有角色数组
	 * Enter description here ...
	 */
	public static function getRoleAll(){
		$role=D("Role");
		$data=$role->where('IS_DEL=0')->select();
		return $data;
	}
	/**
	 * 取用户的所有角色  以数组形式返回
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function getUserRoles($uid){
		$revArr=array();
		$urole=D("User_role");
		$data=$urole->where('U_ID='.$uid)->select();
		if($data){
			foreach ($data as $v){
				$role=D("Role");
				$TMP=$role->where('R_ID='.$v["R_ID"])->getField("R_NAME");
				if($TMP){
					$revArr[]=$TMP;
				}
				/*$TMP=$role->where('R_ID='.$v["R_ID"])->select();
				if($TMP){
					$revArr[]=$TMP[0]['R_NAME'];
				}
				*/
			}
		}
		return $revArr;
		
	}
	/**
	 * 取用户的所有角色  以数组形式返回
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function getUserRolesByArr($uid){
		$revArr=array();
		$urole=D("User_role");
		$data=$urole->where('U_ID='.$uid)->select();
		$i=0;
		if($data){
			foreach ($data as $v){
				$role=D("Role");
				$TMP=$role->where('R_ID='.$v["R_ID"])->select();
				if($TMP){
					$revArr[$i]["R_ID"]=$TMP[0]['R_ID'];
					$revArr[$i]["R_NAME"]=$TMP[0]['R_NAME'];
					$revArr[$i]["R_SHORT_NAME"]=$TMP[0]['R_SHORT_NAME'];
					$i++;
				}
			}
		}
		return $revArr;
		
	}
	/**
	 * 得到订单类型
	 * Enter description here ...
	 * @param unknown_type $type
	 */
	public static function getOrderType($type){
		$t=array(
			1=>'报装',
			2=>'报修',
		);
		if(array_key_exists($type, $t)){
			return $t[$type];
		}else{
			return $type;
		}
	}
	/**
	 * 得到所有的订单状态
	 * Enter description here ...
	 */
	public static function getOrderStateArr(){
		$s=C("ORDER_USE_STATIE");
		return $s;
	}
	/**
	 * 得到所有的客户订单状态
	 * Enter description here ...
	 */
	public static function getCROrderStateArr(){
		$s=array(
		10=>"待处理",
		20=>"处理中",
		30=>"已完成"
		);
		return $s;
	}
	/**
	 * 取订单状态
	 * Enter description here ...
	 * @param unknown_type $state
	 */
	public static function getOrderState($state){
		$s=C("ORDER_USE_STATIE");
		if(array_key_exists($state, $s)){
			return $s[$state];
		}else{
			return $state;
		}
		
	}
	/**
	 * 得到地区
	 * Enter description here ...
	 * @param unknown_type $a_id
	 */
	public static function getArea($a_id){
		$tmpData=array();
		$area=D("Area");
		$area->find($a_id);
		$tmpData[]=$area->AR_L1;
		$tmpData[]=$area->AR_L2;
		$tmpData[]=$area->AR_L3;
		/*$aData=$area->where('AR_ID='.$a_id)->select();
		
		if($aData){
			$tmpData[]=$aData[0]["AR_L1"];
			$tmpData[]=$aData[0]["AR_L2"];
			$tmpData[]=$aData[0]["AR_L3"];
		}
		*/
		return $tmpData;
	}
	public static function getArId($ar1,$ar2,$ar3){
		$area=D("Area");
		$s["AR_L1"]=$ar1;
		$s["AR_L2"]=$ar2;
		$s["AR_L3"]=$ar3;
		$data=$area->where($s)->select();
		if($data){
			return $data[0]["AR_ID"];
		}
		else{
			return false;
		}
	}
	/**
	 * 得到查询时间字典表
	 * Enter description here ...
	 */
	public static function getCreatedTime(){
		$time=C("CREATED_TIME");
		$rev=array();
		if($time && is_array($time) && count($time)){
			foreach ($time as $k=>$v){
				$rev[]=array("id"=>$k,'name'=>$v);
			}
		}
		return $rev;
	}
	/**
	 * 根据查询时间字典表的ID 得到时间段
	 * Enter description here ...
	 * @param unknown_type $t
	 */
	public static function getTimeByCT($t){
		$rev=array();
		switch ($t){
			case 10://最近一周
				$_time=self::aweek();
				$rev['s']=$_time[0];
				$rev['e']=$_time[1]." 23:59:59";
				break;
			case 20://最近一个月
				$rev['s']=date('Y-m-d', mktime(0,0,0,date('m'),1,date('Y')));//本月的第一天
				$rev['e']=date('Y-m-d H:i:s', mktime(23,59,59,date('m'),date('t'),date('Y')));//本月的最后一天
				break;
			case 30://最近三个月
				$rev['s']=date('Y-m-d', mktime(0,0,0,date('m')-3,1,date('Y')));
				$rev['e']=date('Y-m-d H:i:s', mktime(23,59,59,date('m'),date('t'),date('Y')));
				break;
			case 40://半年
				$rev['s']=date('Y-m-d', mktime(0,0,0,date('m')-6,1,date('Y')));
				$rev['e']=date('Y-m-d H:i:s', mktime(23,59,59,date('m'),date('t'),date('Y')));
				break;
			case 50://一年
				$rev['s']=date('Y-m-d', mktime(0,0,0,1,1,date('Y')));
				$rev['e']=date('Y-m-d H:i:s', mktime(23,59,59,12,31,date('Y')));
				break;
			default:
				$_time=self::aweek();
				$rev['s']=$_time[0];
				$rev['e']=$_time[1]." 23:59:59";
		}
		return $rev;
	}
	/**
	 * 功能：取得给定日期所在周的开始日期和结束日期
	 * 参数：$gdate 日期，默认为当天，格式：YYYY-MM-DD
	 *      $first 一周以星期一还是星期天开始，0为星期天，1为星期一
	 * 返回：数组array("开始日期", "结束日期");
	
	 */

	public static function aweek($gdate = "", $first = 0){

		 if(!$gdate) $gdate = date("Y-m-d");
		 $w = date("w", strtotime($gdate));//取得一周的第几天,星期天开始0-6
		 $dn = $w ? $w - $first : 6;//要减去的天数
		 //本周开始日期
		 $st = date("Y-m-d", strtotime("$gdate -".$dn." days"));
		 //本周结束日期
		 $en = date("Y-m-d", strtotime("$st +6 days"));
		 //上周开始日期
		 //$last_st = date('Y-m-d',strtotime("$st - 7 days"));
		 //上周结束日期
		 //$last_en = date('Y-m-d',strtotime("$st - 1 days"));
		 //return array($st, $en,$last_st,$last_en);//返回开始和结束日期
		 return array($st, $en);
	
	}
	/**
	 * 得到渠道信息
	 * Enter description here ...
	 */
	public static function getChannel(){
		$channel=D("Channel");
		$pp=array();
    	$cps=$channel->where('IS_DEL=0')->select();
    	
		if(is_array($cps) && count($cps)){
    		foreach ($cps as $v){
    			$pp[]=array('id'=>$v['C_ID'],'name'=>$v['C_NMAE']);
    		}
    	}
    	$pp[]=array('id'=>'0','name'=>'其他');
		return $pp;
	}
	/**
	 * 检测服务队是否存在
	 * Enter description here ...
	 * @param unknown_type $tName
	 */
	public static function checkSRVTeam($tName){
		$team=D("Srv_team");
		$search["ST_NAME"]=$tName;
		$data=$team->where($search)->select();
		if($data){
			return true;	
		}else{
			return false;
		}
	}
	/**
	 * 根据服务队ID 得到服务队名称
	 * Enter description here ...
	 * @param unknown_type $st_id
	 */
	public static function getStName($st_id){
		$team=D("Srv_team");
		$team->find($st_id);
		return $team->ST_NAME;
	}
	/**
	 * 得到结算状态
	 * Enter description here ...
	 * @param unknown_type $t
	 */
	public static function getJsState($t){
		$s=C("JIESUAN_STATIE");
		if(!empty($s)){
			return $s[$t];
		}
		return $t;
	}
	
	public static function getUname($uid,$filed=''){
		$user=D("User");
		$u=$user->where('U_ID='.$uid)->select();
		if($u){
			if($filed){
				return $u[0][$filed];
			}else{
				return $u[0]["U_RNAME"];
			}
		}
		return false;
	}
	/**
	 * 根据角色简称过滤用户
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $role
	 */
	public static function filterDataByRole($data,$role){
		if(!$role){
			return $data;
		}
		$_roles=explode(',', $role);
		
		$revRole=array();
		if(count($_roles)){
			foreach ($_roles as $v){
				$role=D("Role");
				$s["R_SHORT_NAME"]=$v;
				$rdata=$role->where($s)->select();
				$revRole[]=$rdata[0]["R_ID"];
				
			}
		}
		if(is_array($data) && count($data)){
			foreach ($data as $val){
				$_cRole=array();
				$uid=$val["U_ID"];
				$js=ExUse::getUserRolesByArr($uid);
				if(count($js)){
					foreach ($js as $j){
						$_cRole[]=$j["R_ID"];
					}
				}
				$_rs=array_intersect($_cRole, $revRole);
				if(count($_rs)){
					$rev[]=$val;
				}
			}
		}
		return $rev;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $oid  服务单ID
	 * @param unknown_type $arid 地区ID
	 */
	public static function setOrderTeam($oid,$arid){
		$rev=false;
		//根据地区找服务队
		$st=D("srv_team_areas");
		$arr["AR_ID"]=$arid;
		$data=$st->where($arr)->select();
		if($data && is_array($data) && count($data)){
			$_data=$data[0];
			$st_id=$_data["ST_ID"];
			$RP_U_ID=$_data["RP_U_ID"];
			if($st_id && $RP_U_ID){
				$order=D('Service_order');
				$order->find($oid);
				$order->ST_ID=$st_id;
				$ST_NAME=self::getStName($st_id);
				if(!$ST_NAME){
					$ST_NAME='';
				}
				$order->ST_NAME=$ST_NAME;
				$order->SRV_ID=$RP_U_ID;
				$u_name=self::getUname($RP_U_ID);
				if(!$u_name){
					$u_name='';
				}
				$order->SRV_NAME=$u_name;
				$order->O_SRV_B_TIME=time();//服务开始时间
				$order->O_STATE=C("ORDER_STATE.STATE_PRE_PROCESS");//设置状态为进行中
				$logOid=$oid;
				$logONo=$order->O_NO;
				$rev=$order->save();
				if($rev){
					$msg=$u_name."在".formatDate(time())."处理服务单。";
	   				ExLog::addLog($msg,'TYPE_ORDER_EXECUTE',2,$logOid,$logONo);
	   				self::sendSRVTeamMail($st_id,$logONo);
				}
			}
		}
		return $rev;
	}
	
	public static function sendSRVTeamMail($sid,$oNo){
	//发送邮件
   		/*$user=D("User");
   		$user->find($uid);
   		$email=$user->U_EMAIL;
   		*/
		$srvteam=D("Srv_team");
		$srvteam->find($sid);
		$email=$srvteam->ST_EMAIL;
		$mobile=$srvteam->ST_MOBILE;
		$stname=$srvteam->ST_NAME;
		$__time=formatDate(time(),'Y-m-d H:i');
		if($mobile){
   			sendSrvMobile($mobile, $stname, $__time);
   		}
   		if($email){
   			sendSrvEmail($email, $stname,$__time );
   			/*$adminUrl=$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']));
   			$adminUrl="http://".$adminUrl."?g=Home&m=Member&a=srvlogin";
   			$msg="您于 ".formatDate(time(),'Y-m-d H:i')." 收到新的服务单，单号 ".$oNo."，请及时处理。<a href='{$adminUrl}'>[点击此处进入处理平台]</a>";
   			Mail::getMail()->sendMail($email,"处理服务单",$msg);
   			*/
   			
   		}
   		
   		
	}
	/**
	 * 得到结算状态
	 * Enter description here ...
	 */
	public static function getAccountsStateArray(){
		$data=C("ACCOUNTS_STATE");
		return $data;
	}
	/**
	 * 根据键值得到状态
	 * Enter description here ...
	 * @param unknown_type $key
	 */
	public static function getAccountsState($key){
		$data=self::getAccountsStateArray();
		if(array_key_exists($key, $data)){
			return $data[$key];
		}else{
			return $key;
		}
		
	}
	public static function getAccountsStateByDict(){
		$__list=self::getAccountsStateArray();
		$rev=array();
		if(is_array($__list) && count($__list)){
			$rev=array(array('id'=>'','name'=>'全部'));
			foreach ($__list as $key=>$val){
				$tmp=array('id'=>$key,'name'=>$val);
				$rev[]=$tmp;
			}
		}
		return $rev;
	}
	
	public static function getUserArea($uid){
		$ar_id='';
		$address=D("Address");
   		$ad=array();
   		$ad["U_ID"]=$uid;
   		$addr=$address->where($ad)->order("ID desc")->select();
   		if($addr){
   			$ars=$addr[0];
   			$ar_id=$ars["AR_ID"];
   		}
   		return $ar_id;
	}
	public static function getUserAreaToArray($uid){
		$ars=array();
		$address=D("Address");
   		$ad=array();
   		$ad["U_ID"]=$uid;
   		$addr=$address->where($ad)->order("ID desc")->select();
   		if($addr){
   			$ars=$addr[0];
   		}
   		return $ars;
	}
	/**
	 * 得到字典表
	 * Enter description here ...
	 */
	public static function getDict($dict){
		$__dict=C($dict);
		return $__dict;
	}
	/**
	 * 根据键得到值
	 * Enter description here ...
	 * @param unknown_type $key
	 */
	public static function getDictByKey($dict,$key){
		$__dict=self::getDict($dict);
		if(array_key_exists($key, $__dict)){
			return $__dict[$key];
		}
		else{
			return '';
		}
	}
	/**
	 * 已字典表的形式 
	 * Enter description here ...
	 */
	public static function getDictByList($dict,$isAll=false){
		$__dict=self::getDict($dict);
		$rev=array();
		if(is_array($__dict) && count($__dict)){
			if($isAll){
				$rev=array(array('id'=>'','name'=>'全部'));
			}
			foreach ($__dict as $key=>$val){
				$tmp=array('id'=>$key,'name'=>$val);
				$rev[]=$tmp;
			}
		}
		return $rev;
	}
	/**
	 * 登录方法
	 * Enter description here ...
	 * @param unknown_type $user
	 * @param unknown_type $pwd
	 */
	public static function setUser($username,$pwd){
		
		$user=D("User");
		$search["U_NAME"]=$username;
		$data=$user->where($search)->select();
		$IS_USER_OK=false;
		if($data){
			
			$IS_USER_OK=true;
		}else{
			//echo self::USER_LOGIN_NAME_ERR;
			//exit;
			//检查是否邮箱登录
			$user=D("User");
			$search=array();
			$search["U_EMAIL"]=$username;
			$data=$user->where($search)->select();
			if($data){
				$IS_USER_OK=true;
			}else{
				//检查是否手机登录
				$user=D("User");
				$search=array();
				$search["U_MOBILE"]=$username;
				$data=$user->where($search)->select();
				if($data){
					$IS_USER_OK=true;
				}
			}
			
		}
		//检查密码
		if($IS_USER_OK && $data){
			$__pwd=$data[0]["U_PWD"];
			if($__pwd!=$pwd){
				$IS_USER_OK=false;
			}
		}else{
			$IS_USER_OK=false;
		}
		if($IS_USER_OK){
			$_user=$data[0];
			
			//取用户的角色
			$_roles=ExUse::getUserRolesByArr($_user["U_ID"]);
				
			if($_roles && is_array($_roles) && count($_roles)){
				//如果是多角色  先取一个角色使用
				$_user["roleId"]=$_roles[0]["R_ID"];
				$_user["roleName"]=$_roles[0]["R_NAME"];
				$_user["roleShortname"]=$_roles[0]["R_SHORT_NAME"];
			}
			
			ExSession::getSession()->setUserByArray($_user);
			
			ExSession::getSession()->setSessionByArrayAndPid($_user,$sid);
			
			ExLog::addLog($username."前台 自动登录", "USER_LOGIN");
			
		}
		return $IS_USER_OK;
	}
}