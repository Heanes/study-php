<?php
/**
 * @doc 配置参数
 * @author Heanes fang <heanes@163.com>
 * @time 2016-07-27 15:57:38 周三
 */

return [
    // 目标，1-运单，2-任务单
    'target' => 1,
    // 运单个数
    'waybillTotal' => 10,
    // 任务单个数
    'taskbillTotal' => 10,
    // 城市ID
    'cityId' => 7,
    // 仓库ID
    'warehouseId' => 1,
    // 站区ID
    'stationRegionId' => 3899,
    // 配送区域ID
    'deliveryAreaId' => 3899,
    // 站点ID
    'siteId' => 1,
    // 站点类型
    'siteType' => 0,
    // 配送日期， 为空表示为明天，否则填yyyy-mm-dd格式
    'deliveryTime' => '2016-07-26',
    // 配送类型，10,-"仓库直送"，20-"站点配送"，30-"大宗"，40-"仓间调拨"，50-"包车"
    'deliveryType' => '10',
    // 配送服务
    'distServiceId' => 1,
    // 批次
    'batchId' => 2,
    // 销售人ID
    'sellerId' => 2,
    // 销售人名字
    'sellerName' => '方刚-造单脚本(php)',
    // 销售电话
    'sellerPhone' => '15010691715',
    
    //  门店ID，对应waybill_receive_info运单收货信息表的收货客户ID
    'companyId' => 1,
    //  门店名称，对应waybill_receive_info运单收货信息表的收货客户名称
    'companyName' => '门店名称-造单脚本(php)',
];