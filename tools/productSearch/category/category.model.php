<?php
require '../db.php';
class Category{
	public static function getParentList($pid = 0,$cascade = true){
		global $db;
		$sql = "SELECT id,name,ordering FROM `ju_categories` WHERE parent=".(int)$pid." ORDER BY ordering";
		$result = mysql_query($sql,$db);
		if($cascade){
			$id = ' id="parentList"';
		}else {
			$id = ' id="orderList"';
		}
		$parentList = '';
		while($row = mysql_fetch_assoc($result)){
			$value = $cascade ? $row['id'] : $row['ordering'];
			$parentList .= '<option value="'.$value.'">'.$row['name'].'</option>';
		}
		
		$parentList = '<select'.$id.'><option value="0">Root</option>' . $parentList . '</select>';
		return $parentList;
	}
	
	public static function saveCategory($data = array()){
		global $db;
		$name 	= mysql_escape_string($data['name']);
		$desc 	= mysql_escape_string($data['desc']);
		$parent = (int)$data['parent'];
		$order 	= (int)$data['order']+1;

		if(empty($name)) {
			echo '分类名不能为空！';
			exit;
		}
		
		$sql = "INSERT INTO `ju_categories`(`id`,`name`,`desc`,`parent`,`ordering`) VALUES(null,'$name','$desc','$parent','$order')";
		if(mysql_query($sql,$db)){
			return true;
		}else {
			return false;
		}
	
	}
	
}

//End_php