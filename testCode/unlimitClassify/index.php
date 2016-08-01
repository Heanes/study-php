<?php
/**
 * 各中无极限分类方法研究
 * @author Heanes
 * @time 2014-9-4上午10:45:03
 * index.php GBK PHP
 */
header("Content-type:text/html;charset=utf-8");
//echo "7389-850=".(7389-850).'<br/>';

$db = new mysqli('localhost', 'root', '123456', 'heanes.com') ;
if(mysqli_connect_errno())
{
    echo '链接失败:'.mysqli_connect_error();
    exit();
}

/**
 * 一次执行多条sql语句
 */
/* 将三条SQL命令使用分号（;）分隔, 连接成一个字符串 */
$query='use `heanes.com`;
show databases;
show tables;
show create database `heanes.com`;';
$query .= "SELECT CURRENT_USER();";                           //从MySQL服务器获取当前用户
$query .= "SELECT * FROM `pre_article_category`;";     //从contactinfo表中读取数据
if ($db->multi_query($query)) {                       //执行多条SQL命令
	echo 'multi_query'.'<br />';
	do {
		if ($result = $db->store_result()) {                     //获取第一个结果集
			echo 'store_result'.'<br />';
			while ($row = $result->fetch_row()) {                         //遍历结果集中每条记录
				foreach($row as $data){                                      //从一行记录数组中获取每列数据
					echo $data."  ";                           //输出每列数据
				}
				echo "<br>";                                       //输出换行符号
			}
			$result->close();                               //关闭一个打开的结果集
		}
		if ($db->more_results()) {                         //判断是否还有更多的结果集
			echo "-----------------<br>";                       //输出一行分隔线
		}else {
			break;
		}
	} while ($db->next_result());                          //获取下一个结果集，并继续执行循环
}
//$db->close();
exit;


$db->query('set names utf8');
$result = $db->query('SELECT * FROM `pre_article_category`');
 
while(FALSE != ($row = $result->fetch_assoc()))
{
    // 每一行保存一个分类的id,f_id,name的信息。
    $arr[] = array('id'=>$row['id'],'parent_id' =>$row['parent_id'],'name' =>$row['name']);
}
function fenlei($arr, $id = 0)
{
    foreach ($arr as $key=>$item)
    {
        if ($item['f_id'] == $id)
        {
            fenlei($arr, $item['id']);
        }
    }
}
 

$items = array(
		0 => array('id' => 1, 'pid' => 0, 'name' => '1分类', '其他键值对的键'=>'其他键值对的值','order'=>1),
		1 => array('id' => 2, 'pid' => 0, 'name' => '2分类','order'=>1),
		2 => array('id' => 3, 'pid' => 0, 'name' => '3分类','order'=>1),
		3 => array('id' => 4, 'pid' => 1, 'name' => '1.1分类','order'=>1),
		4 => array('id' => 5, 'pid' => 1, 'name' => '1.2级分类','order'=>1),
		5 => array('id' => 6, 'pid' => 1, 'name' => '1.3级分类','order'=>1),
		6 => array('id' => 7, 'pid' => 2, 'name' => '2.1级分类','order'=>1),
		7 => array('id' => 8, 'pid' => 3, 'name' => '3.1级分类','order'=>1),
		8 => array('id' => 9, 'pid' => 3, 'name' => '3.2分类','order'=>1),
		9 => array('id' => 10, 'pid' => 0, 'name' => '4分类','order'=>1),
		10 => array('id' => 11, 'pid' => 10, 'name' => '4.1分类','order'=>1),
		11 => array('id' => 12, 'pid' => 10, 'name' => '4.2分类','order'=>1),
		12 => array('id' => 13, 'pid' => 1, 'name' => '1.4分类','order'=>1),
		13 => array('id' => 14, 'pid' => 9, 'name' => '3.2.2分类','order'=>2),
		14 => array('id' => 15, 'pid' => 9, 'name' => '3.2.1分类','order'=>1),
		15 => array('id' => 16, 'pid' => 9, 'name' => '3.2.3分类','order'=>3),
);
$items = array(
    1 => array('id' => 1, 'pid' => 0, 'name' => '1分类', '其他键值对的键'=>'其他键值对的值','order'=>3),
    2 => array('id' => 2, 'pid' => 0, 'name' => '2分类','order'=>2),
    3 => array('id' => 3, 'pid' => 0, 'name' => '3分类','order'=>1),
    4 => array('id' => 4, 'pid' => 1, 'name' => '1.1分类','order'=>2),
    5 => array('id' => 5, 'pid' => 1, 'name' => '1.2级分类','order'=>1),
		/*
    6 => array('id' => 6, 'pid' => 1, 'name' => '1.3级分类','order'=>1),
    7 => array('id' => 7, 'pid' => 2, 'name' => '2.1级分类','order'=>1),
    8 => array('id' => 8, 'pid' => 3, 'name' => '3.1级分类','order'=>1),
    9 => array('id' => 9, 'pid' => 3, 'name' => '3.2分类','order'=>1),
    10 => array('id' => 10, 'pid' => 0, 'name' => '4分类','order'=>1),
    11 => array('id' => 11, 'pid' => 10, 'name' => '4.1分类','order'=>1),
    12 => array('id' => 12, 'pid' => 10, 'name' => '4.2分类','order'=>1),
    13 => array('id' => 13, 'pid' => 1, 'name' => '1.4分类','order'=>1),
    14 => array('id' => 14, 'pid' => 9, 'name' => '3.2.2分类','order'=>2),
    15 => array('id' => 15, 'pid' => 9, 'name' => '3.2.1分类','order'=>1),
    16 => array('id' => 16, 'pid' => 9, 'name' => '3.2.3分类','order'=>3),
    */
);

echo "<pre>";
//var_dump(sortOut($items));
echo '<br/><hr/>';
$items=(generateTree($items,'id','pid'));
echo '<br/><hr/>';
//var_dump(generateTree2($items,'id','pid'));
var_dump(multi_array_sort($items));
echo "</pre>";

//递归方式
function sortOut($cate,$pid=0,$level=0,$html='--'){
	$tree = array();
	foreach($cate as $v){
		if($v['pid'] == $pid){
			$v['level'] = $level + 1;
			$v['html'] = str_repeat($html, $level);
			$tree[] = $v;
			$tree = array_merge($tree, sortOut($cate,$v['id'],$level+1,$html));
		}
	}
	return $tree;
}

//迭代方式
function tree(&$list,$pid=0,$level=0,$html='--'){
	static $tree = array();
	foreach($list as $v){
		if($v['pid'] == $pid){
			$v['sort'] = $level;
			$v['html'] = str_repeat($html,$level);
			$tree[] = $v;
			tree($list,$v['id'],$level+1);
		}
	}
	return $tree;
}
/**
 * 方法一：
 * 此方法由@Tonton 提供
 * @form http://levi.cg.am
 * @date 2012-12-12
 */
/**
 * @doc 生成无限分级菜单数
 * @param array $items 源数据
 * @param string $main_id 数据主ID键名称
 * @param string $parent_id 数据父ID键名称
 * @param string $sub_key_name 子分级键名称
 * @return multitype: 分级菜单
 * @author Heanes
 * @time 2015-06-12 15:46:57
 */
function generateTree(array $items ,$main_id='id' ,$parent_id='parent_id' ,$sub_key_name='_child') {
    foreach ($items as $item)
        $items[$item[$parent_id]][$sub_key_name][$item[$main_id]] = &$items[$item[$main_id]];
    if (isset($items[0][$sub_key_name])) {
    	return $items[0][$sub_key_name];
    }else {
    	return array();
    }
    
    //return isset($items[0][$sub_key_name]) ? $items[0][$sub_key_name] : array();
}
 
/**
 * 方法二：将数据格式化成树形结构
 * @author Xuefen.Tong
 * @form http://levi.cg.am
 * @param array $items
 * @return array
 */
/**
 * @doc 生成无限分级菜单数
 * @param array $items 源数据
 * @param string $main_id 数据主ID键名称
 * @param string $parent_id 数据父ID键名称
 * @param string $sub_key_name 子分级键名称
 * @return multitype: 分级菜单
 * @author Heanes
 * @time 2015-06-12 15:46:57
 */
function generateTree2(array $items ,$main_id='id' ,$parent_id='parent_id' ,$sub_key_name='_child' ,$sort_key_name='order',$sort=true,$sort_mode='asc') {
	$tree = array();    //格式化好的树
	foreach ($items as $item){
		if (isset($items[$item[$parent_id]])){
			$items[$item[$parent_id]][$sub_key_name][] = &$items[$item[$main_id]];
		}
		else{
			$tree[] = &$items[$item[$main_id]];
		}
	}
	if (boolval($sort)) {
		foreach ($tree as $key => $subTree) {
			if (is_array($subTree)) {
				$subTree=multi_array_sort($subTree,$sort_key_name);
			}else {
				;
			}
		}
		$tree=multi_array_sort($tree,$sort_key_name);
	}
	return $tree;
}

/**
 * @doc 对多维数组按某项元素的值进行排序
 * @param array $multi_array 被排序的数组
 * @param string $sort_key 排序参考元素
 * @param string $sort_type SORT_ASC-从小到大（默认）|SORT_DESC|从大到小
 * @return boolean|array 不是数组|返回排序后的数组
 * @author Heanes
 * @time 2015-06-12 18:25:56
 */
function multi_array_sort($multi_array,$sort_key='order',$sort_type=SORT_ASC){
	if(is_array($multi_array)){
		foreach ($multi_array as $key => $row_array){
			if(is_array($row_array)){
				if (is_array($row_array) && count($row_array) == count($row_array, 1)) {
					$key_array[] = $row_array[$sort_key];
				}else {
					echo '递归';
					multi_array_sort($row_array,$sort_type,$sort_type);
				}
				
			}else{
				return false;
			}
		}
	}else{
		return false;
	}
	array_multisort($key_array,$sort_type,$multi_array);
	return $multi_array;
}

$data=array(
	 0  => array( 'id' => 63,  'catid'  => '11', 'title'  => '测试一',  'updatetime'  => '1374827257'),
	 1  => array( 'id' => 202, 'catid'  => '5',  'title'  => '测试二',  'updatetime'  => '1375691656'),
	 2  => array( 'id'  => 3,  'catid'  => '6',  'title'  => '测试三',  'updatetime'  => '1375691653'),
	 3  => array( 'id'  => 4,  'catid'  => '7',  'title'  => '测试四',  'updatetime'  => '1385691656')
);
//排序处理
$sort_type='';
foreach($data as $key=>$val){
	$temp_arr[] = $val['catid'];
	//$_GET["sort_type"] === "ASC" ? $sort_type = SORT_ASC : $sort_type = SORT_DESC;
}
$sort_type = SORT_ASC;
//array_multisort($temp_arr,$sort_type,$data);
//重新打印数组$data,查看排序后的数组。
//print_r($data);


//print_r(multi_array_sort($data,'catid',SORT_DESC));
//echo 'aa'.'<br/>';
//array_multisort($temp_arr,SORT_DESC,$data);
//print_r($data);


$str='cd';
$$str='langdog';
$$str.='ok';
echo $cd;