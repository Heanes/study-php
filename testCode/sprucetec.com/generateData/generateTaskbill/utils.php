<?php
/**
 * @doc 一些工具方法
 * @author Heanes fang <heanes@163.com>
 * @time 2016-07-27 10:30:57 周三
 */

/**
 * @doc 生成运单号
 * @param string $start 生成运单号前缀
 * @param string $end 生成运单号后缀
 * @return string $string
 * @author Heanes fang <heanes@163.com>
 * @time 2016-07-27 11:06:53 周三
 */
function generateWaybillNo($start = '70', $end = ''){
    $MicroTimeArr = getMicroTimeArr();
    $string = $start . $MicroTimeArr[0]. str_pad($MicroTimeArr[1], 4, '0', STR_PAD_RIGHT) . rand(10 ,99) . $end;
    usleep(10);// 等待1微秒
    return $string;
}

/**
 * @doc 获得时间及微秒数组
 * @param string $type
 * @return array
 * @author Heanes fang <heanes@163.com>
 * @time 2016-07-27 11:07:06 周三
 */
function getMicroTimeArr($type = 'number'){
    switch($type){
        case 'number':
            return explode('.', microtime(true));
            break;
        case 'float':
            return explode(' ', microtime());
            break;
        default:
            return explode('.', microtime(true));
    }
}

