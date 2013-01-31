<?php
/**
 * 发送短信类
 * Enter description here ...
 * @author admin
 *
 */
class Mobile{
	private $__url="http://service.winic.org:8009/sys_port/gateway/?id=y31x&pwd=654321&to=%s&content=%s&time=";
	private static $__mobile=null;
	function __construct(){
	
	}
	
	public static function getMobile(){
		if(!self::$__mobile){
			self::$__mobile=new Mobile();
		}
		return self::$__mobile;
	}
	
	public function send($mobile,$content){
		$url=sprintf($this->__url,$mobile,urlencode(iconv('UTF-8', 'GBK', $content)));
		//$url=sprintf($this->__url,$mobile,$content);
		try{
			$data=curlPost($url);
			ExLog::addLog($mobile."|".$url, "MOBILE");
			return $data;
		}catch (Exception $e){
			return false;
		}
		
	}
	
}