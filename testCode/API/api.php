<?php
/**
 * @doc API测试
 * @filesource api.php
 * @copyright heanes.com
 * @author Heanes
 * @time 2015-06-05 15:46:06
 */
require 'connect.php';
$connect=new mysqli('localhost', 'root', '123456', 'heanes.com');
$query="select * from pre_article";
$result=$connect->query($query);
while ($row=$result->fetch_array()) {
	$json[]=$row;
}
echo json_encode($json);