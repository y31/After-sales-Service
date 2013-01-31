<?php

function getDictArray($d){
	$__dict=C($d);
	if($__dict && is_array($__dict)){
		return $__dict;
	}else{
		return false;
	}
}
/**
 * 返回某个字典表的值
 * @param string $dict
 * @param int	 $key
 */
function getDictValueByKey($dict,$key){
	$__dict=getDictArray($dict);
	if($__dict){
		if(array_key_exists($key, $__dict)){
			return $__dict[$key];
		}
	}
	return $key;
}