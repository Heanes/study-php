<?php
set_time_limit(0);
declare(ticks = 1);
// ob_start();
require __DIR__ . "/boot.php";

function random($length = 8, $numeric = false) {
	$seed = base_convert(md5(uniqid(mt_rand(), true)), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

function getname(){
	static $nameArr = ["吴明","阴姬","燕南天","李商","柴玉关","木道人","邀月宫主","西门吹雪","展白","俞佩玉","夜帝","原随云","逍遥侯","王夫人","怜星宫主","燕十三","日后","高莫静","玉罗刹","叶孤城","孙白发","上官金虹","金童","玉女","宫九","李寻欢","楚留香","陆小凤","沈浪","江小鱼","谢晓峰","傅红雪","萧十一郎","阿飞","叶开","铁中棠","柳长街","孙玉伯","南宫平","方宝玉","萧泪血","花无缺","芮玮","仇恕","蓝天","萧王孙","东郭先生","独孤剑","卓东来","荆无命","萧东楼","地藏","薛衣人","萧伯贤","萧仲忍","龙啸天","陆上龙王","柳祟厚","碧玉夫","魏无牙","司空摘星","花满楼","杨凡","卜鹰","王怜花","白衣人","韩棠","姬悲情","老实和尚","胡不归","赵无忌","熊猫儿","郭地灭","东郭高","铁凌","董千里","龙五",];
	return "\"" . $nameArr[mt_rand(0, 76)] . "\t\"";
}

function getCities() {
	$sql = "SELECT id, `name` FROM `city` WHERE is_deleted = 0";
	$st = _::app()->db("basic")->prepare($sql);
	if($st->execute()) {
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}
	return [];
}

function getSellerId()
{
	return mt_rand(1000, 10000000);
}

function getsellerPhone() {
	return mt_rand(10000000000, 18999999999);
}

function getWarehouse($cityId) {
	$sql = <<<SQL
	SELECT
		w.id,w.`name`,w.`code`
	FROM
		`warehouse_city` wc,
		`warehouse` w
	WHERE
		wc.warehouse_id = w.id AND wc.is_deleted = 0 AND w.is_deleted = 0 AND wc.city_id = {$cityId}
SQL;
	$st = _::app()->db("basic")->prepare($sql);
	if($st->execute()) {
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}
	return [];
}

function createOneDayWaybill(array $conf, $tmc) {
	$default = [
		"day_count"				=> 1,
		"city_id" 				=> 1,
		"station_region_id" 	=> 1,
		"warehouse_id"	 		=> 1,
		"dist_service_id"       => 1,
		"batch_id"	 			=> 1,
		"distribution_type" 	=> 10,
		"waybill_status"	 	=> 100,
		"route_id"	 			=> 3,
		"seq" 					=> 0,
		"prefix"	 			=> "w_",
		"delivery_time" 		=> strtotime(date("Y-m-d")), // today
		"receive_customer_id"	=> 1,
	];

	$packing_name = ['箱', '个', '斤', '吨', '箱','个', '斤', '吨', '箱', '个', '斤', '吨', '箱', '个', '斤', '吨'];

	$conf += $default;
	$day_count = $conf["day_count"];
	$city_id = $conf["city_id"];
	$station_region_id = $conf["station_region_id"];
	$warehouse_id = $conf["warehouse_id"];
	$dist_service_id = $conf["dist_service_id"];
	$batch_id = $conf["batch_id"];
	$distribution_type = $conf["distribution_type"];
	$waybill_status = $conf["waybill_status"];
	$route_id = $conf["route_id"];
	$seq = $conf["seq"];
	$delivery_time = $conf["delivery_time"];
	$prefix = $conf["prefix"];

	$valuesWaybills = [];
	$valuesStatus = [];
	$valuesOis = [];
	$valuesPacking = [];
	$valuesRecieves = [];
	$valuesHistories = [];

	$omc_item_id = time() * 1000;
	for ($i=0; $i < $day_count; $i++) {
		$waybill_no = time() . random(); // var
		$saller_Id = getSellerId();
		$sellerPhone = getsellerPhone();
		$expect_receive_time = "09:10-10:10";

		// $class = mt_rand(1, 9);
		$receive_longitude = mt_rand(116200, 116400) / 1000; // var
		$receive_latitude = mt_rand(39800, 39999) / 1000; // var
		$receive_customer_id = /*$i*/$conf["receive_customer_id"]; // var

		$valuesWaybills[] = "(
			'" . $waybill_no . "', 
			$delivery_time, 
			$dist_service_id, 
			$batch_id,
			$distribution_type,
			$warehouse_id, 
			$city_id, 
			$station_region_id, 
			$saller_Id,
			'" . getname() . "',
			$sellerPhone,
			'" . $expect_receive_time . "'
			)";

		$valuesStatus[] = "('" . $waybill_no . "', $waybill_status, $city_id)";

		$count = mt_rand(1, 10);
		for ($j=1; $j < $count; $j++) {
			$class1_id = $j; // var
			$price = $j; // var
			$omc_item_id = $omc_item_id + 1;
			$valuesOis[] = "('" . $waybill_no . "', $city_id, $class1_id, $price, $omc_item_id)";
			$packing_id = mt_rand(1,1000);
		    
            $valuesPacking[] = "('" . $waybill_no . "',  $city_id, $omc_item_id, $packing_id, '" . $packing_name[$j] . "')";
		}

		$valuesRecieves[] = sprintf("('" . $waybill_no . "', $receive_customer_id, %s, %s, %s, %s, %s, %.5f, %.5f, $city_id)",
			getname(), getname(), getname(), getname(), getname(), $receive_longitude, $receive_latitude);
		$valuesHistories[] = "($receive_customer_id, $route_id, $seq, $city_id)";
	}


	$waybillSql = <<<SQL
	INSERT INTO waybill (
		waybill_no,
		delivery_time,
		dist_service_id,
		batch_id,
		distribution_type,
		warehouse_id,
		city_id,
		station_region_id,
		saller_id,
		saller_name,
		saller_phone,
		expect_receive_time
	)
	VALUES
SQL;
	$waybillStatusSql = "INSERT INTO `waybill_status` (waybill_no, waybill_status, city_id) VALUES ";

	$oiSql = "INSERT INTO `waybill_oi_detail` (waybill_no, city_id, class1_id, price, omc_item_id) VALUES ";

	$receiveSql = <<<SQL
	INSERT INTO  `waybill_receive_info` (
		waybill_no,
		receive_customer_id,
		receive_customer_name,
		gov_province_name,
		gov_city_name,
		gov_county_name,
		gov_address,
		receive_longitude,
		receive_latitude,
		city_id
	) VALUES
SQL;

$packingSql = <<<SQL
INSERT INTO  `waybill_packing` (
		waybill_no,
		city_id,
		omc_order_id,
		packing_id,
		packing_name
	) VALUES
SQL;

	// $tmc ->beginTransaction();
	// try {
		// 运单
		$waybillChunk = array_chunk($valuesWaybills, 1000);

		foreach ($waybillChunk as $waybills) {
			$tmc->prepare($waybillSql . implode($waybills, ","))->execute();	
		}

		// 运单状态
		$statusArr = array_chunk($valuesStatus, 1000);  

	    foreach ($statusArr as $status) {
			$tmc->prepare($waybillStatusSql . implode($status, ","))->execute();	
		}

        
		// PACKING
		$packingChunk = array_chunk($valuesPacking, 1000);
		foreach ($packingChunk as $packings) {
			$tmc->prepare($packingSql . implode($packings, ","))->execute();	
		}

		// receive
		$recievesChunk = array_chunk($valuesRecieves, 1000);
		foreach ($recievesChunk as $recieves) {
			$tmc->prepare($receiveSql . implode($recieves, ","))->execute();
		}

		// oi
        $oisChunk = array_chunk($valuesOis, 1000);
		foreach ($oisChunk as $ois) {
			$tmc->prepare($oiSql . implode($ois, ","))->execute();	
		}
		// $tmc->commit();
	// } catch (Exception $e) {
	// 	_::log($e->getMessage());
	// 	// $tmc->rollBack();
	// }

	return $day_count;
}