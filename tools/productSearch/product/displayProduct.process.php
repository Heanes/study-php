<?php
require 'product.model.php';
switch($_POST['type']){
	case 'attr':
		echo json_encode(Product::searchProductByAttribute(mysql_escape_string($_POST['searchString']),(int)$_POST['catid']));
		break;
		
	case 'cate':
		echo json_encode(Product::searchProductByCategory((int)$_POST['catid']));
		break;
	default:
		echo json_encode(array('status'=>0,'msg'=>'非法查询类型！'));
		break;
}
exit;
