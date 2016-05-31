<?php
/**
 * @file createDemoDataSql.php
 * @doc 生成示例数据的原生sql语句
 * @author 方刚
 * @time 2015/10/20 16:44
 */
$sql='';
$sql='insert into `meixiansong`.`mxs_user`(
`user_id`, `create_time`, `id_card`, `bank`, `bank_account`, `account_name`, `user_sex`, `user_birthday`, `user_email`, `user_tel`, `user_province`, `user_city`, `user_district`, `user_address`, `user_contact`, `create_ip`, `user_memo`
)';

//日期节点分散
$today=date('Y-m-d');