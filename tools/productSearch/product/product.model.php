<?php
require '../db.php';
class Product{
	public static function getCategoryList(){
		global $db;
		//此处为两层分类获取方法，若为多层，请参考本类底部的getSubCategories()方法使用递归获取。
		$sql = "SELECT id,name,0 AS ordering,id AS 'groupcol' FROM `ju_categories` WHERE parent=0
			UNION
			SELECT id,name,ordering,parent AS 'groupcol' FROM `ju_categories`
			WHERE parent IN(
			SELECT id FROM `ju_categories` WHERE parent=0
			) ORDER BY `groupcol`,`ordering`";
		$result = mysql_query($sql,$db);
		$categoryList = '';
		while($row = mysql_fetch_assoc($result)){
			if($row['id'] != $row['groupcol']) $pref = '-';
			else $pref = '';
			$categoryList .= '<option value="'.$row['id'].'">'.$pref.$row['name'].'</option>';
		}

		$categoryList = '<select id="categoryList"><option value="0">Root</option>' . $categoryList . '</select>';
		return $categoryList;
	}

	public static function getAttributeList($search=false){
		global $db;
		$sql = "SELECT id,name,0 AS ordering,id AS 'groupcol' FROM `ju_attributes` WHERE parent=0
			UNION
			SELECT id,name,ordering,parent AS 'groupcol' FROM `ju_attributes`
			WHERE parent IN(
			SELECT id FROM `ju_attributes` WHERE parent=0
			) ORDER BY `groupcol`,`ordering`";
		$result = mysql_query($sql,$db);
		$attributeList = '';
		if($search){
			while($row = mysql_fetch_assoc($result)){
				if($row['id'] == $row['groupcol']) {
					$attributeList .= '</ul><ul><li class="parentAttr">'.$row['name'].'</li>';
				}else {
					$attributeList .= '<li attr="'.$row['id'].'" class="searchAttr">'.$row['name'].'</li>';
				}
			}
			if(stripos($attributeList,'</ul>') === 0) $attributeList = substr($attributeList,5);	//去掉头部多余的</ul>
			$attributeList .= '</ul>';
		}else {
			while($row = mysql_fetch_assoc($result)){
				if($row['id'] == $row['groupcol']) {
					$attributeList .= '<option value="'.$row['id'].'" class="parentAttr">'.$row['name'].'</option>';
				}
				else {
					$attributeList .= '<option value="'.$row['id'].'">-'.$row['name'].'</option>';
				}
			}

			$attributeList = '<select id="attributeList"><option value="0" class="root">-请选择添加-</option>' . $attributeList . '</select>';
		}
		return $attributeList;
	}

	public static function save($data = array()){
		global $db;
		$name 	= mysql_escape_string($data['name']);
		$sku 	= mysql_escape_string($data['sku']);
		$catid 	= (int)$data['catid'];
		$origPrice 	= mysql_escape_string($data['origPrice']);
		$price 	= mysql_escape_string($data['price']);
		$stock 	= mysql_escape_string($data['stock']);
		$attrs 	= implode(',',(array)$data['attrs']);

		if(empty($name)) {
			echo '分类名不能为空！';
			exit;
		}

		$sql = "INSERT INTO `ju_products`(`id`,`name`,`sku`,`catid`,`origPrice`,`price`,`stock`,`attributes`,`created_on`)"
			." VALUES(null,'$name','$sku','$catid','$origPrice','$price','$stock','$attrs',now())";
		if(mysql_query($sql,$db)){
			$productId = mysql_insert_id($db);
			$sql = "INSERT INTO `ju_product_attributes`(`product_id`,`attribute_id`) VALUES";
			foreach($data['attrs'] as $attr){
				$sql .="('$productId','$attr'),";
			}
			$sql = rtrim($sql,',');
			mysql_query($sql,$db);
			return true;
		}else {
			return false;
		}

	}
	
	public static function getProductList(){
		global $db;
		$productTpl = <<<TPL
			<div class="product" id="product-%d">
				<div class="p_image"><img src="%s" alt="%s" width="150" height="200"/></div>
				<div class="p_title">%s</div>
				<div class="p_price">
					<span>￥%.2f</span>
					<del>￥%.2f</del>
				</div>
			</div>
TPL;
		$sql = "SELECT id,name,price,origPrice FROM `ju_products` ORDER BY id";
		$result = mysql_query($sql,$db);
		$productList = '';
		while($row = mysql_fetch_assoc($result)){
			$productList .= vsprintf($productTpl,array($row['id'],'#',$row['name'],$row['name'],$row['price'],$row['origPrice']));
		}
		return $productList;
	}
	
	public static function searchProductByCategory($catid){
		global $db;
		if(!isset($catid)) return array('status'=>0,'msg'=>'分类不能为空！');
		
		$categories = self::getSubCategories($catid);		//默认递归包含子分类
		$sql = "SELECT id FROM `ju_products` WHERE catid IN(".implode(',',$categories).") ORDER BY id";
		$result = mysql_query($sql,$db);
		$productArray = array();
		while($row = mysql_fetch_assoc($result)){
			array_push($productArray,$row['id']);
		}
		return array('status'=>1,'products'=>$productArray);
	}
	public static function searchProductByAttribute($searchString,$catid=0){
		global $db;
		if(empty($searchString)) return array('status'=>0,'msg'=>'搜索条件不能为空！');
		
		if(empty($catid)) $where = array();
		else $where = array("p.catid IN(".implode(',',self::getSubCategories((int)$catid)).")");
		$ands = explode('|',$searchString);
		foreach($ands as $and){
			$andString = "";
			$ors = explode(',',$and);
			foreach($ors as $or){
				$andString .= "LOCATE(',{$or},',pas.attribute_ids) OR ";
			}
			$andString = '('.substr($andString,0,strlen($andString)-4).')';		//-4去掉末尾“ OR ”
			$where[] = $andString;
		}
		
		$sql = "
			SELECT p.id FROM `ju_products` as p
			INNER JOIN (
			SELECT product_id,concat(',,',group_concat(attribute_id),',,') as attribute_ids FROM `ju_product_attributes` GROUP BY product_id
			) as pas ON p.id=pas.product_id
			WHERE ".implode(' AND ',$where)."
			group by p.id
		";
		$result = mysql_query($sql,$db);
		$productArray = array();
		while($row = mysql_fetch_assoc($result)){
			array_push($productArray,$row['id']);
		}
		return array('status'=>1,'products'=>$productArray,'sql'=>$sql);
	}
	
	public static function getSubCategories($pid,$recursive=true){
		global $db;
		$pid = (int)$pid;
		$sql = "SELECT id FROM `ju_categories` as cate WHERE cate.parent=".$pid;
		$result = mysql_query($sql,$db);
		$subCategories = array($pid);	//加入当前分类
		if($recursive){
			while($row = mysql_fetch_row($result)){
				$subCategories = array_merge($subCategories,self::getSubCategories($row[0]));
			}
		}
		return $subCategories;
	}
}

//End_php