<?php
/**
 * @doc 连接数据库文件
 * @filesource connect.php
 * @copyright heanes.com
 * @author Heanes
 * @time 2015-06-05 15:46:39
 */
class Db {
	//配置
	private $_config=array();
	
	//连接
	private $connect;
	
	function __construct($dbhost,$dbuser,$dbpwd,$dbname,$dbport='3306') {
		$this->_config['dbhost']=$dbhost;
		$this->_config['dbuser']=$dbuser;
		$this->_config['dbpwd']=$dbpwd;
		$this->_config['dbname']=$dbname;
		$this->_config['dbport']=$dbport;
	}
	
	public function getConnect(){
		$this->connect();
		return $this->connect;
	}
	
	public function connect() {
		$config=$this->_config;
		$this->connect=new mysqli($config['dbhost'],$config['dbuser'],$config['dbpwd'],$config['dbname'],$config['dbport']);
	}
}