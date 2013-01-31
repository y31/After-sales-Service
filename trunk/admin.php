<?php
/**
 * 后台管理程序的入口
 */
$adminUrl=$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']));
header("Location: http://".$adminUrl."?g=Admin&m=Main&a=login");