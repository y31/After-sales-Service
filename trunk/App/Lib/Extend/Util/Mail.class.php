<?php
/**
 * 邮件类
 * Enter description here ...
 * @author df
 *
 */
class Mail{
	
	private static $__mail=null;//邮箱类对象
	private $__PMAIL=NULL;
	function __construct(){
		vendor('PhpMailer.class#phpmailer');
		$this->__PMAIL=new PHPMailer();
		$this->__PMAIL->IsSMTP(); 
		$this->__PMAIL->Host=C("MAIL_SMTP");; 
		$this->__PMAIL->Username=C("MAIL_LOGINNAME");
		$this->__PMAIL->Password=C("MAIL_PASSWORD");
		$this->__PMAIL->SMTPAuth=C("MAIL_AUTH");
		$this->__PMAIL->From=C("MAIL_ADDRESS");
	}
	public static function  getMail(){
		if(!self::$__mail){
			self::$__mail=new Mail();
		}
		return self::$__mail;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $toAddress   //收件人地址,多个可用,好分隔
	 * @param unknown_type $fromName    //发件人名字
	 * @param unknown_type $title       //标题
	 * @param unknown_type $content     //内容
	 * @param unknown_type $isHTML      //是否以HTML格式发送
	 * @param unknown_type $charset     //编码
	 * @param unknown_type $replyto     //收件人地址
	 * @param unknown_type $attachment  //附件
	 */
	public function sendMail($toAddress,$title='',$content='',$isHTML=TRUE,$charset='utf-8',$replyto='',$fromName='',$attachment=''){
		$this->__PMAIL->IsHTML($isHTML);
		$this->__PMAIL->CharSet=$charset;
		if(strstr($toAddress,',')){
			$__list=explode(',', $toAddress);
			foreach ($__list as $v){
				$this->__PMAIL->AddAddress($v,$v);
			}
		}else{
			$this->__PMAIL->AddAddress($toAddress,$toAddress);
		}
		
		
		$this->__PMAIL->FromName=$fromName;
		$this->__PMAIL->Subject=$title;
		$this->__PMAIL->Body=$content;
		if($replyto){
			$this->__PMAIL->AddReplyTo($replyto);
		}
		if($attachment){
			$this->__PMAIL->AddAttachment($attachment,$attachment);
		}
		return $this->__PMAIL->Send();
	}
}