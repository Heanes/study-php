<?php
require 'include/new_smarty.php';
$smarty->assign('post_title','文章标题');
$smarty->assign('post_content','文章内容');
$smarty->display('post.tpl');
