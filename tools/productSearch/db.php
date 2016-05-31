<?php
	static $connect = null;
	if(!isset($connect)){
		$connect = mysql_connect("localhost","root","123456") or die('无法连接数据库！');
		mysql_select_db("test",$connect) or die('无法连接到指定数据库！');
		mysql_query("SET NAMES UTF8",$connect);
		
		$db = $conn = $connect;
	}

//End_php