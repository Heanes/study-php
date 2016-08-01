<?php
set_time_limit(0);
ini_set("memory_limit", "2048M");

require __DIR__ . "/waybill.php";

function main($dayCount, $deliveryTime, $batchId)
{
	$conf = [
		"day_count"				=> $dayCount,
		"city_id" 				=> 1,
		"station_region_id" 	=> 1,
		"warehouse_id"	 		=> 1,
		"dist_service_id"       => 1,
		"batch_id"	 			=> $batchId,
		"distribution_type" 	=> 10,
		"waybill_status"	 	=> 100,
		"route_id"	 			=> 3,
		"delivery_time" 		=> strtotime($deliveryTime), // today
		"receive_customer_id"	=> 1,
		"db" 					=> "tmc",
	];

	$tmc = _::app()->db($conf["db"]);
	return createOneDayWaybill($conf, $tmc);
}

/**
 * 1. argv[1] 执行数量
 * 2. argv[2] 配送日期 Y-m-d
 * 3. argv[2] 批次ID
 */

$dayCount = isset($argv[1]) ? $argv[1] : 1;
$deliveryTime = isset($argv[2]) ? $argv[2] : date("Y-m-d");
$batchId = isset($argv[3]) ? $argv[3] : 1;

echo "StartTime:" . time() . "\n";
$succ = main($dayCount, $deliveryTime, $batchId);
echo "EndTime:" . time() . "\n";
print_r($succ);