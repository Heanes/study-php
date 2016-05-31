<?php
/**
 * @file sortTools.php
 * @doc 十万个随机数进行排序
 * @author 方刚
 * @time 2015/11/30 10:19
 */

$list = [];
foreach (range(1, 100000) as $i) {
    $list[$i] = $i;
}

file_put_contents('./sort.num.1', join(',', $list));

shuffle($list);

file_put_contents('./sort.num.2', join(',', $list));

$t = microtime();
sort($list);
echo microtime() - $t;

file_put_contents('./sort.num.3', join(',', $list));