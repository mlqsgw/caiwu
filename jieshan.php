<?php
date_default_timezone_set('PRC');

$host="127.0.0.1";			//mysql数据库服务器
$userName="root";				//mysql数据库用户名
$passwrod="root";			//mysql数据库密码

$database = "zhibodb";			//mysql数据库名

$connection=mysql_connect("$host","$userName","$passwrod");
mysql_query("set names 'utf8'");//编码转化
if (!$connection) {
    die("could not connect to the database.\n" . mysql_error());//诊断连接错误
}
$selectedDb = mysql_select_db($database);//选择数据库

if (!$selectedDb) {
    die("could not to the database\n" . mysql_error());
}


