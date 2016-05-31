<?php
header("Content-type: text/html; charset=utf-8"); 
if(!class_exists('Category')) require 'category.model.php';

$pid = (int)$_POST['pid'];
echo Category::getParentList($pid,false);
exit;

//End_php