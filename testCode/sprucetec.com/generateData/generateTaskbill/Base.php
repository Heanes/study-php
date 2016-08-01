<?php
/**
 * @doc 创建任务单
 * @author Heanes fang <heanes@163.com>
 * @time 2016-07-26 12:10:23 周二
 */

class Base{
    
    /**
     * @var mysqli 数据库连接
     */
    public $connect;
    
    /**
     * @var array 数据库连接配置
     */
    public $dbConfig = [];
    
    /**
     * @doc 获取配置
     * @param $param
     * @author Heanes fang <heaens@163.com>
     * @time 2016-07-26 16:07:22 周二
     */
    public function getConfig($param) {
        ;
    }
    
    /**
     * @doc 连接数据库
     * @param $config
     * @author Heanes fang <heaens@163.com>
     * @time 2016-07-26 15:58:23 周二
     */
    public function connectDb($config){
        $this->connect = mysqli_connect($config['db_server'], $config['db_user'], $config['db_password'], $config['db_database'], $config['db_port']);
    }
    
    /**
     * @doc 获取配置参数
     * @author Heanes fang <heaens@163.com>
     * @time 2016-07-26 15:58:47 周二
     */
    public function getArgs(){
        return require_once 'args.php';
    }
    
    
}