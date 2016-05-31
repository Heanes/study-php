<?php
/**
 * 生成静态html文件
 * @author Heanes
 * @time 2014-9-16 04:26:46
 * generate_html.php UTF-8 PHP
 */
require 'include/new_smarty.php';
$smarty->testInstall();//测试smarty安装是否正确
 ob_start();
 
 $title         = "title";
 $description     = "description";
 $keywords         = "keywords";
 $outfilename = "/html/test.html";

 $smarty->assign("TITLE",           $title);
 $smarty->assign("DESCRIPTION",     $description);
 $smarty->assign("KEYWORDS",        $keywords);
 $smarty->assign("TPL_LEFT",         'sdfa');
 $smarty->assign("TPL_RIGHT",         'fads');
 $smarty->assign("TPL_TOP",         'fads');
 $smarty->assign("TPL_FOOTER",         'fasd');
 $smarty->assign("TPL_CENTER",         'fsad');

 $smarty->display('main.html'); // TPL_MAIN 等常量在 include/config.php 中已经被定义

 $str = ob_get_contents();

 $fp = @fopen($outfilename, 'wa');
 if (!$fp) {
     Show_Error_Message( ERROR_WRITE_FILE );
 }
 fwrite($fp, $str);
 fclose($fp);
 ob_end_clean();
?>
