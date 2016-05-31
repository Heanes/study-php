<?php
/**
 * @doc 产生验证码
 * @filesource index.php
 * @copyright heanes.com
 * @author Heanes
 * @time 2015-06-08 16:27:00
 */
$seccode = 'abHE';//makeSeccode($_GET['nchash']);
require_once 'seccode.php';
@header("Expires: -1");
@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
@header("Pragma: no-cache");

$code = new seccode();
$code->code = $seccode;
$code->width = 90;
$code->height = 26;
$code->background = 1;
$code->adulterate = 1;
$code->scatter = '';
$code->color = 1;
$code->size = 0;
$code->shadow = 1;
$code->animator = 0;
$code->datapath =  'resource/captcha/';
$code->display();