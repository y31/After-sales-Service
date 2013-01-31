<?php
class ExSession{
	private $_userId;
	private $_userGuId;
	private $_userName;
	private $_userRealName;
	private $_roleId;
	private $_roleName;
	private $_roleShortname;
	private $_roleCount = 0;
	private $_mobilePhone;		//手机
	private $_eMail;			//Email
	private $_stName;//服务队名称
	private $_stId;//服务队ID
	private $_lastVisitPage;
	private $_lastRefererPage;
	
	
	
	private static $_ses = null;
	
	public static final function getSession()
	{
		if( !self::$_ses )
		{
			self::$_ses = new ExSession();				
		}
		return self::$_ses;
	}
	
	function __construct()
	{
		$this->_loadSessionInfo();
	}
	
	function __destruct()
	{
		$this->_saveSessionInfo();
	}
	
	private function _loadSessionInfo()
	{

		if( !isset($_SESSION) )session_start();
		$arrVars = get_object_vars($this);
		foreach ( $arrVars as $key => $val )
		{
			if( key_exists( C("SESSION_PREFIX").$key, $_SESSION ) )
			{
				if ( ereg('^_obj_', $key) )
					$this->$key = unserialize($_SESSION[C("SESSION_PREFIX").$key]);
				else
					$this->$key = $_SESSION[C("SESSION_PREFIX").$key];
			}
		}
	}

	private function _saveSessionInfo()
	{
		$arrVars = get_object_vars($this);
		foreach ( $arrVars as $key => $val )
		{
			if ( ereg('^_obj_', $key) )
				$_SESSION[C("SESSION_PREFIX").$key] = serialize($val);
			else
				$_SESSION[C("SESSION_PREFIX").$key] = $val;
		}
	}
	/**
	 * @param string $key
	 * @param mix $data
	 * @return boolean
	 */
	public function setData( $key, $data ){
		$this->_sessionData[$key] = $data;
		return true;
	}
	
	/**
	 * @param string $key
	 * @return mix
	 */
	public function getData( $key ){
		if( key_exists( $key , $this->_sessionData ) ){
			return $this->_sessionData[$key];
		}else{
			return null;
		}
	}
	
	//以数组的形式填充session
	public function setUserByArray( $user, $roleid = "" )
	{
		
		$this->_userId   = $user["U_ID"];
		$this->_userGuId   = $user["U_GU_ID"];
		$this->_userName = $user["U_NAME"];
		$this->_userRealName = $user["U_RNAME"];
		
		$this->_mobilePhone = $user["U_MOBILE"];
		$this->_eMail = $user["U_EMAIL"];
		$roles=ExUse::getUserRolesByArr($user["U_ID"]);
		if(is_array($roles) && count($roles)){
			$this->_roleName =$roles[0]["R_NAME"];
			$this->_roleShortname = $roles[0]["R_SHORT_NAME"];
			$this->_roleId =$roles[0]["R_ID"];
		}
		//如果是服务队 那么取服务队名称
		if($this->_roleShortname=='SRVTEAM'){
			$uteam=D("Srv_team_members");
			$uteam->find($this->_userId);
			$this->_stId=$uteam->ST_ID;
			$team=D("Srv_team");
			$team->find($this->_stId);
			$this->_stName=$team->ST_NAME;
		}
		
		//$this->_USER_AUTH_KEY=1;
		$_SESSION[C("USER_AUTH_KEY")]=1;
		
		
	}

	/**
	 * @param string $key
	 * @return boolean
	 */
	public function delData( $key ){
		if( key_exists( $key , $this->_sessionData ) ){
			unset($this->_sessionData[$key]);
		}
		return true;
	}
	
	public function getUserId(){return $this->_userId;}
	public function getUserGuId(){return $this->_userGuId;}
	public function getUserName(){return $this->_userName;}
	public function getRealName(){return $this->_userRealName;}
	public function getRoleId(){return $this->_roleId;}
	public function getRoleName(){return $this->_roleName;}
	public function getRoleShortname(){return $this->_roleShortname;}
	
	public function setEMail( $v ){$this->_eMail = $v;}
	public function getEMail(){return $this->_eMail;}
	
	public function setMobilePhone( $v ){$this->_mobilePhone = $v;}
	public function getMobilePhone(){return $this->_mobilePhone;}
	public function getStId(){return $this->_stId;}
	public function getStName(){return $this->_stName;}
	public function destory()
	{
		session_destroy();
	}
	
	public function setSessionByArrayAndPid($user,$sid){
		
		$_user=$user;
		$arrUser=array();
		$arrUser["U_RNAME"]=$_user["U_RNAME"];
		$arrUser["U_MOBILE"]=$_user["U_MOBILE"];
		$arrUser["U_PHONE"]=$_user["U_PHONE"];
		$arrUser["U_EMAIL"]=$_user["U_EMAIL"];
		$arrUser["U_PWD"]=$_user["U_PWD"];
		$arrUser["U_PWD1"]=$_user["U_PWD"];
		$u_id=$_user["U_ID"];
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
   			$arrUser["O_ADDRESS"]=$addr[0]["U_ADDRESS"];
   		}
   		$_SESSION[$_user["U_EMAIL"]]=$arrUser;
   		if($sid){
   			$_SESSION[$sid]=$arrUser;
   		}else{
   			//$_SESSION[$_user["U_EMAIL"]]=$arrUser;
   		}
		
	}
	/**
	 * 记录最近访问页
	 *
	 */
	public function recordCurrentVisitPage()
	{
		//$_SESSION["lastVisitPage"] = $_SERVER["REQUEST_URI"];
		$this->_lastVisitPage = $_SERVER["REQUEST_URI"];
	}
	public function recordVisitPage($uri){
		//判断URL是否是 dialog 或 debug  如果存在 那么不记录 John 2010-12-27
		if(strstr($uri,'dialog.php') || strstr($uri,'v=dialog') || strstr($uri,'debug.php'))
		{
			$this->_lastVisitPage ='';
		}
		else 
		{
			$this->_lastVisitPage = $uri;
		}
	}
	public function getListVistitPate(){
		return $this->_lastVisitPage;
	}
	public function recordRefererPage()
	{
		$this->_lastRefererPage = @$_SERVER["HTTP_REFERER"];
	}
	
	/**
	 * 获得最近的引用页
	 *
	 * @return string
	 */
	public function getLastRefererPage(){
		return $this->_lastRefererPage;
	}
	
	/**
	 * 转到最近访问页
	 * 转向后会exit
	 * 不会return
	 *
	 * @return false
	 */
	public function restoreLastVisitPage()
	{
		//if( isset( $_SESSION["lastVisitPage"] ) && trim( $_SESSION["lastVisitPage"] ) != "" )
		if( trim( $this->_lastVisitPage ) != "" )
		{
//			$to_uri = $_SESSION["lastVisitPage"];
//			unset( $_SESSION["lastVisitPage"] );

			$to_uri = $this->_lastVisitPage;
			$this->_lastVisitPage = null;
			header( "Location:".$to_uri );
			exit();
		}
		return false;
	}
}