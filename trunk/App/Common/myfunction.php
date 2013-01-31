<?php
/**
 * 得到页面完整的URL
 * Enter description here ...
 */
function curPageURL() 
{
    $pageURL = 'http';

    if ($_SERVER["HTTPS"] == "on") 
    {
        $pageURL .= "s";
    }
    $pageURL .= "://";

    if ($_SERVER["SERVER_PORT"] != "80") 
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } 
    else 
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}
function getSysTitle(){
	return C("WEB_SYSTEM_NAME");
}
//检测内存使用情况
function my_memory_get_usage()
{
	if(function_exists("memory_get_usage")){
		return memory_get_usage();
	}
       //If its Windows
       //Tested on Win XP Pro SP2. Should work on Win 2003 Server too
       //Doesn't work for 2000
       //If you need it to work for 2000 look at http://us2.php.net/manual/en/function.memory-get-usage.php#54642
       if ( substr(PHP_OS,0,3) == 'WIN') 
       {
               $output = array();
               exec( 'tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output );
       
               return preg_replace( '/[\D]/', '', $output[5] ) * 1024;            
       }
       else{
           //We now assume the OS is UNIX
           //Tested on Mac OS X 10.4.6 and Linux Red Hat Enterprise 4
           //This should work on most UNIX systems
           $pid = getmypid();
           exec("ps -eo%mem,rss,pid | grep $pid", $output);
           $output = explode("  ", $output[0]);
           //rss is given in 1024 byte units
           return $output[1] * 1024;
       }
       /**
        * echo "<pre>";
		print_r(printf("memory usage: %01.2f MB", my_memory_get_usage()/1024/1024));
		echo "</pre>";
		exit;
        */
}
/**
 * 取文件的扩展名
 * Enter description here ...
 * @param unknown_type $file
 */
function getFileExt($file){
	return substr(strrchr($file, '.'), 1);
}
/**
 * 返回不包括扩展名的地方
 * Enter description here ...
 * @param unknown_type $file
 */
function getFileNoExt($file){
	$__end=strrpos($file, getFileExt($file));
	if($__end){
		return substr($file,0,$__end-1);
	}else{
		return $file;
	}
}

function DateFormat(  $format ,$timestamp){
		if (!$timestamp){
			return '';
		}
		if( is_numeric($timestamp) ){
			if ($format){
				if($format == 3){
					return date( "Y-m-d H:i:s", $timestamp );		
				}
				if( strlen($format) < 2 ){
					return date( "Y-m-d", $timestamp );
				}else{
					return date( $format, $timestamp );	
				}
			}
			return date( "Y-m-d H:i", $timestamp );			
		}else{
			return $timestamp;
		}
}
/**
 * 后台下单发送短信
 * Enter description here ...
 * @param unknown_type $mobile
 * @param unknown_type $product
 * @param unknown_type $pwd
 */
function sendAdminMobile($mobile,$product,$pwd){
	if($pwd){
		$__content=C("CR_ADMIN_MOBILE");
		$t=sprintf($__content,$product,$pwd);
	}else{
		$__content=C("CR_ADMIN_MOBILE_NOPWD");
		$t=sprintf($__content,$product);
	}
	Mobile::getMobile()->send($mobile,$t);
}
/**
 * 后台下单发送邮件
 * Enter description here ...
 * @param unknown_type $email
 * @param unknown_type $title
 * @param unknown_type $product
 * @param unknown_type $pwd
 */
function sendAdminMail($email,$title,$product,$pwd){
	if($pwd){
		$__content=C("CR_ADMIN_EMAIL");
		$t=sprintf($__content,$product,$pwd);
	}else{
		$__content=C("CR_HOME_EMAIL");
		$t=sprintf($__content,$product);
	}
	
	Mail::getMail()->sendMail($email,$title,$t);
}
/**
 * 指派服务队时发送短信
 * Enter description here ...
 * @param unknown_type $mobile
 * @param unknown_type $user
 * @param unknown_type $time
 */
function sendSrvMobile($mobile,$user,$time){
	$__content=C("SRV_MOBILE");
	$t=sprintf($__content,$user,$time);
	Mobile::getMobile()->send($mobile,$t);
}
/**
 * 指派服务队时发送邮件
 * Enter description here ...
 * @param unknown_type $mobile
 * @param unknown_type $user
 * @param unknown_type $time
 */
function sendSrvEmail($email,$user,$time){
	$__content=C("SRV_EMAIL");
	$t=sprintf($__content,$user,$time);
	Mail::getMail()->sendMail($email,'处理服务单',$t);
}
