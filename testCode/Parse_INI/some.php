<?php
/**
 * @doc 
 * @filesource some.php
 * @copyright heanes.com
 * @author Heanes
 * @time 2015年4月16日下午9:58:45
 */
require 'index.php';

$ini=new parse_ini('config.ini');
$ini->set_ini_file('other.ini');
$some_arr=$ini->get_parse_ini();
var_dump($some_arr);
$ini->print_arr($some_arr);

foreach ($some_arr as $key => &$value) {
	$value=str_replace('"', '', $value);
}
var_dump($some_arr);
$ini->print_arr($some_arr);

