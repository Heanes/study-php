<?php
require '../db.php';
class Comment{
	public static function getProductList(){
		global $db;
		$sql = "SELECT id,name FROM `ju_products` ORDER BY id";
		$result = mysql_query($sql,$db);
		$productList = '';
		while($row = mysql_fetch_assoc($result)){
			$productList .= '<div class="product"><input type="radio" name="product" value="'.$row['id'].'"/><span>'.$row['name'].'</span></div>';
		}
		return $productList;
	}
	
	public static function loadComments($productId,$commentId = false){
		if(empty($productId)){
			return false;
		}
		global $db;
		if($commentId) $addCommentId = " AND id=".(int)$commentId;
		else $addCommentId = "";
		$sql = "SELECT * FROM `ju_comments` WHERE product_id=".(int)$productId.$addCommentId." ORDER BY created_on";
		$result = mysql_query($sql,$db);
		$commentList = '';
		$commentTpl = <<<COMMENT
			<div class="comment">
				<div class="buyer">
					<div class="creator">%s</div>
					<div class="ip">%s</div>
				</div>
				<div class="messageBody">
					<div class="message">%s</div>
					<div class="createdOn">%s</div>
				</div>
				<div class="clr"></div>
			</div>
COMMENT;
		while($row = mysql_fetch_assoc($result)){
			$commentList .= vsprintf($commentTpl,array($row['creator'],$row['ip'],$row['message'],date('Y-m-d',strtotime($row['created_on']))));
		}
		if($commentId) return $commentList;
		else return '<div id="product-'.(int)$productId.'">'.$commentList.'</div>';
	}
	
	public static function save($data = array()){
		global $db;
		$productId 	= (int)($data['productId']);
		$message 	= mysql_escape_string($data['message']);
		$ip = $_SERVER["REMOTE_ADDR"];
		$creator = 'Zjmainstay';

		if(empty($productId) || empty($message)) {
			echo '评论不能为空！';
			exit;
		}
		
		$sql = "INSERT INTO `ju_comments`(`id`,`product_id`,`message`,`ip`,`creator`,`created_on`) VALUES(null,'$productId','$message','$ip','$creator',now())";
		if(mysql_query($sql,$db)){
			return true;
		}else {
			return false;
		}	
	}
	
}

//End_php