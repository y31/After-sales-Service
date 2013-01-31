<?php
/**
 * 日志类
 * Enter description here ...
 * @author transn
 *	使用方法:ExLog::addLog("测试", 'USER_TEST');
 */
class ExLog{
	
	//日志级别
	const LEVEL_SYSTEM	=	1;	//系统级
	const LEVEL_INFO	=	2;  //业务级
	
	private static $__Loger;
	
	/**
	 * 添加日志
	 * Enter description here ...
	 * @param string $msg
	 * @param int $level
	 * @param string $type
	 * @param string $o_id
	 * @param string $o_number
	 */
	public static function addLog($msg,$type,$level=1,$o_id='',$o_number=''){
		//判断是否开启系统日志
		if(C("LOG_SYS_OPEN")){
			self::getloger()->addSysLog($msg,$level,$type);
		}
		if($level>=self::LEVEL_INFO){
			//判断是否开启业务日志
			if(C("LOG_WORK_OPEN")){
				self::getloger()->addWorkLog($msg,$level,$type,$o_id,$o_number);
			}
		}
		
	}
	
	public static function getloger(){
		if(!is_object(self::$__Loger)){
			self::$__Loger=new ExLog();
		}
		return self::$__Loger;
	}
	/**
	 * 增加系统日志
	 * Enter description here ...
	 */
	private function addSysLog($msg,$level,$type){
		$rev=array();
		
		$rev["U_ID"]=ExSession::getSession()->getUserId();
		$rev["U_NAME"]=ExSession::getSession()->getRealName();
		$rev["R_ID"]=ExSession::getSession()->getRoleId();
		$rev["R_NAME"]=ExSession::getSession()->getRoleName();
		$rev["LG_LEVEL"]=$level;
		$rev["LG_TYPE"]=$type;
		$rev["LG_IP"]=get_client_ip();
		$rev["LG_AGENT"]='';
		$rev["LG_REF"]='';
		$rev["LG_CONTENT"]=$msg;
		$rev["LG_URL"]=curPageURL();
		$rev["CREATED_AT"]=time();
		$log=D("Op_log");
		$log->add($rev);
		
	}
	/**
	 * 增加业务日志
	 * Enter description here ...
	 */
	private function addWorkLog($msg,$level,$type,$o_id,$o_number){
		$rev=array();
		
		$rev["U_ID"]=ExSession::getSession()->getUserId();
		$rev["U_NAME"]=ExSession::getSession()->getRealName();
		$rev["R_ID"]=ExSession::getSession()->getRoleId();
		$rev["R_NAME"]=ExSession::getSession()->getRoleName();
		$rev["LG_LEVEL"]=$level;
		$rev["LG_TYPE"]=$type;
		$rev["LG_IP"]=get_client_ip();
		$rev["LG_AGENT"]='';
		$rev["LG_REF"]='';
		$rev["LG_CONTENT"]=$msg;
		$rev["LG_URL"]=curPageURL();
		$rev["O_NO"]=$o_number;
		$rev["O_ID"]=$o_id;
		$rev["CREATED_AT"]=time();
		$log=D("Order_log");
		$log->add($rev);
	}
	
}