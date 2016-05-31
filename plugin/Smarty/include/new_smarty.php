<?php
/**
 * 实例化smarty对象
 * @author 方刚
 * Time 2014.08.18
 */

require 'smarty/Smarty.class.php';
$smarty = new Smarty;
//$smarty->force_compile = true;
$smarty->debugging = false;
$smarty->caching = false;
$smarty->cache_lifetime = 120;

$ROOT_PATH=$_SERVER['DOCUMENT_ROOT'];
// 自定义主题功能
$Theme='default';
$Theme='heanes';

$smarty->setTemplateDir($ROOT_PATH.'/theme/'.$Theme);//注意此处目录设为绝对路径比较好
$smarty->setCompileDir($ROOT_PATH.'/compile/templates_c/');
$smarty->setConfigDir($ROOT_PATH.'/include/smarty_conf/');
$smarty->setCacheDir($ROOT_PATH.'/compile/cache/');

//$smarty->testInstall();//测试smarty安装是否正确

/*
$smarty->assign("Name", "Fred Irving Johnathan Bradley Peppergill", true);
$smarty->assign("FirstName", array("John", "Mary", "James", "Henry"));
$smarty->assign("LastName", array("Doe", "Smith", "Johnson", "Case"));
$smarty->assign("Class", array(array("A", "B", "C", "D"), array("E", "F", "G", "H"),
                               array("I", "J", "K", "L"), array("M", "N", "O", "P")));

$smarty->assign("contacts", array(array("phone" => "1", "fax" => "2", "cell" => "3"),
                                  array("phone" => "555-4444", "fax" => "555-3333", "cell" => "760-1234")));

$smarty->assign("option_values", array("NY", "NE", "KS", "IA", "OK", "TX"));
$smarty->assign("option_output", array("New York", "Nebraska", "Kansas", "Iowa", "Oklahoma", "Texas"));
$smarty->assign("option_selected", "OK");

$smarty->assign("salary",5500-5500/22*2-4754.7-50);
$smarty->assign("onedaysalary",4000/22);//181.8

$smarty->display('index.tpl');
*/