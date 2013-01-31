<?php
$adminUrl=$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']));
$adminUrl=str_replace('admin/', '', $adminUrl);
$adminUrl=str_replace('admin', '', $adminUrl);
header("Location: http://".$adminUrl."?g=Admin&m=Main&a=login");