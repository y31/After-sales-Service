<?php
	function userUcfrist($str){
		return ucfirst($str);
	}
	function objectToArray($object)
	{
	        $result = array();
	        $object = is_object($object) ? get_object_vars($object) : $object;
	        foreach ($object as $key => $val) {
	            $val = (is_object($val) || is_array($val)) ? objectToArray($val) : $val;
	            $result[$key] = $val;
	        }
	        return $result;
	}
	function curlPost( $url, $params ){

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, 1.0);
		curl_setopt($ch, CURLOPT_POST, true);
		if( isArrayParam( $params ) ){	
			curl_setopt($ch, CURLOPT_POSTFIELDS, data_encode($params));
		}else{
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}
		ob_start();
		curl_exec($ch);
		$data=ob_get_contents();
		ob_end_clean();
		curl_close ($ch);
		return $data;		
	}
	function data_encode($data, $keyprefix = "", $keypostfix = "") {
		assert( is_array($data) );
		$vars=null;
		foreach($data as $key=>$value) {
			if(is_array($value)){
				$vars .= data_encode($value, $keyprefix.$key.$keypostfix.("["), ("]"));
			}else{
				$vars .= $keyprefix.$key.$keypostfix."=".urlencode($value)."&";
			}
		}
		return $vars;
	}
	function isArrayParam($param){
		if( is_array($param) ){
			foreach ($param as $key => $val) {
				if( !is_array( $val ) ){
					if( substr( $val, 0, 1 ) == '@' ){
						//如果是file域，则不认为是数组
						return false;
					}					
				}
			}
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 得到所有产品
	 * Enter description here ...
	 */
	function getProductAll(){
		$product=D("Product");
    	return $product->where('IS_DEL=0')->select();
	}
	/**
	 * 得到某个产品
	 * Enter description here ...
	 * @param unknown_type $search
	 * @param unknown_type $filed
	 */
	function getProductFiled($search,$filed){
		if($_SESSION["product"]){
			$product=$_SESSION["product"];
		}else{
			$product=$_SESSION["product"]=getProductAll();
		}
		$pp=array();
		$sFileds=array_keys($search);
		if(is_array($product) && count($product) ){
			if(count($search)>1){
				foreach ($product as $val){
					if($val[$sFileds[0]]==$search[$sFileds[0]] && $val[$sFileds[1]]==$search[$sFileds[1]]){
						$pp[$val[$filed]]=$val[$filed];
					}
				}
			}else if(count($search)>0 && count($search)<2){
				foreach ($product as $val){
					if($val[$sFileds[0]]==$search[$sFileds[0]]){
						$pp[$val[$filed]]=$val[$filed];
					}
				}
			}else{
				foreach ($product as $val){
					$pp[$val[$filed]]=$val[$filed];
				}
			}
		}
		return $pp;
		/*$product=D("Product");
		$search['IS_DEL']=0;
		$pp=array();
    	$cps=$product->where($search)->select();
		if(is_array($cps) && count($cps)){
    		foreach ($cps as $v){
    			$pp[$v[$filed]]=$v[$filed];
    		}
    	}
		return $pp;
		*/
	}
	
	/**
	 * 得到所有地区
	 * Enter description here ...
	 */
	function getAreaAll(){
		$Area=D("Area");
    	return $Area->select();
	}
	/**
	 * 得到某个地区
	 * Enter description here ...
	 * @param unknown_type $search
	 * @param unknown_type $filed
	 */
	function getAreaFiled($search,$filed){
		if($_SESSION["area"]){
			$product=$_SESSION["area"];
		}else{
			$product=$_SESSION["area"]=getAreaAll();
		}
		$pp=array();
		$sFileds=array_keys($search);
		if(is_array($product) && count($product) ){
			if(count($search)>1){
				foreach ($product as $val){
					if($val[$sFileds[0]]==$search[$sFileds[0]] && $val[$sFileds[1]]==$search[$sFileds[1]]){
						$pp[$val[$filed]]=$val[$filed];
					}
				}
			}else if(count($search)>0 && count($search)<2){
				foreach ($product as $val){
					if($val[$sFileds[0]]==$search[$sFileds[0]]){
						$pp[$val[$filed]]=$val[$filed];
					}
				}
			}else{
				foreach ($product as $val){
					$pp[$val[$filed]]=$val[$filed];
				}
			}
		}
		return $pp;
		/*
		$Area=D("Area");
		$pp=array();
    	$cps=$Area->where($search)->select();
		if(is_array($cps) && count($cps)){
    		foreach ($cps as $v){
    			$pp[$v[$filed]]=$v[$filed];
    		}
    	}
		return $pp;
		*/
	}
	
	/**
	 * 得到订单编号 （YYMMDDHHIISSMISNNN）
	 * Enter description here ...
	 * @param unknown_type $Prefix     生成编号前缀
	 */
	
	function getOrderNumber($Prefix='Z'){
		$t=time();
		list($usec, $sec) = explode(" ", microtime());
        $msec=round($usec*1000);
		return $Prefix.date("ymdHis").$msec.mt_rand(100, 999);
	}
	/**
	 * 得到前台菜单
	 * Enter description here ...
	 */
	function getMenu($role){
	   $_menu=C("HOME_MENU");
	   if($role){
	   		$menu=$_menu[$role];
	   }else{
		   if(ExSession::getSession()->getRoleShortname()){
		   	 $menu=$_menu[ExSession::getSession()->getRoleShortname()];
		   }else{
		   	 $menu=$_menu['CR'];
		   }
	   }
	   $menus=array();
	   if(is_array($menu) && count($menu)){
	   		$i=1;
	   		foreach ($menu as $key=>$v){
	   			
	   			$menus[]=array('id'=>$i,'name'=>$key,'url'=>$v);
	   			$i++;
	   		}
	   }
	   return $menus;
	}
	function getAdminMenu($role){
		$_menu=C("ADMIN_MENU");
	   if($role){
	   		$menu=$_menu[$role];
	   }else{
		   if(ExSession::getSession()->getRoleShortname()){
		   	 $menu=$_menu[ExSession::getSession()->getRoleShortname()];
		   }else{
		   	 $menu=$_menu['CRS'];
		   }
	   }
	   return $menu;
	   /*
	   $menus=array();
	   if(is_array($menu) && count($menu)){
	   		$i=1;
	   		foreach ($menu as $key=>$v){
	   			$menus[]=array('id'=>$i,'name'=>$key,'url'=>$v,'parent'=>'y');
	   			if(is_array($v) &&count($v)){
	   				$j=0;
	   				foreach ($v as $k=>$v1){
	   					$menus[]=array('id'=>$j,'name'=>$k,'url'=>$v1,'parent'=>$key);
	   				}
	   				$j++;
	   			}
	   			$i++;
	   		}
	   }
	   return $menus;
	   */
	}
	function getGuId(){
		return create_guid();
	}
	function create_guid()
	{
	    $microTime = microtime();
		list($a_dec, $a_sec) = explode(" ", $microTime);
	
		$dec_hex = sprintf("%x", $a_dec* 1000000);
		$sec_hex = sprintf("%x", $a_sec);
	
		ensure_length($dec_hex, 5);
		ensure_length($sec_hex, 6);
	
		$guid = "";
		$guid .= $dec_hex;
		$guid .= create_guid_section(3);
		$guid .= '-';
		$guid .= create_guid_section(4);
		$guid .= '-';
		$guid .= create_guid_section(4);
		$guid .= '-';
		$guid .= create_guid_section(4);
		$guid .= '-';
		$guid .= $sec_hex;
		$guid .= create_guid_section(6);
	
		return $guid;
	
	}
	
	function create_guid_section($characters)
	{
		$return = "";
		for($i=0; $i<$characters; $i++)
		{
			$return .= sprintf("%x", mt_rand(0,15));
		}
		return $return;
	}
	
	function ensure_length(&$string, $length)
	{
		$strlen = strlen($string);
		if($strlen < $length)
		{
			$string = str_pad($string,$length,"0");
		}
		else if($strlen > $length)
		{
			$string = substr($string, 0, $length);
		}
	}
	/**
	 * 格式化时间
	 * Enter description here ...
	 * @param int $timestamp
	 * @param string $format
	 */
	function formatDate( $timestamp, $format="" ){
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
	 * 自定义输出信息
	 * Enter description here ...
	 */
	function myOutput($ct){
		$tmp=nl2br($ct);
		return $tmp;
	}
	