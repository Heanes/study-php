<?php 
/**
 * @name PHPTree 
 * @author crazymus < QQ:291445576 >
 * @des PHP生成无限多级分类
 * @version 1.1.0
 * @Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * @updated 2015-08-18

 */
class PHPTree{	

	protected static $config = array(
		//主键
		'primary_key' 	=> 'id',
		//父键
		'parent_key'  	=> 'parent_id',
		//是否展开子节点
		'expanded'    	=> false,
		//是否显示根节点
		'root_visible'	=> true 
	);
	/**
	 * @name 生成树形结构
	 * @param array 二维数组
	 * @return mixed 多维数组
	 */
	public static function makeTree($data,$options=array() ){
		
		$config = array_merge(self::$config,$options);
		self::$config = $config;
		extract($config);
		
		//数据归类
		$dataset = array();
		foreach($data as $item){
			$id = $item[$primary_key];
			$parent_id = $item[$parent_key];
			$dataset[$parent_id][$id] = $item;
		}	
		$r['children'] = self::makeTreeCore(0,$dataset);
		$r = $root_visible?$r:$r['children'];
		return $r;
	}
	
	private static function makeTreeCore($index,$data)
	{
		foreach($data[$index] as $id=>$item)
		{
			if(isset($data[$id]))
			{
				$item['expanded']= self::$config['expanded'];//展开所有分类
				$item['children']= self::makeTreeCore($id,$data);
			}
			else
			{
				$item['leaf']= true; //叶子节点
			}
			$arr[]=$item;
		}
		return $arr;
	}
}


?>