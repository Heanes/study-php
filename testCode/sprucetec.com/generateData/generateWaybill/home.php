<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8"/>
	<title></title>
	<style type="text/css">
		body {
			font-family: "微软雅黑";
		}
		ul li {
			list-style-type: none;
			line-height: 40px;
			margin: 10px auto;
		}
		ul li input,ul li select {
			font-size: 18px;
			height: 30px;
			width: 200px;
			-webkit-border-radius: 5px;
		}
		ul li label {
			font-size: 18px;
			display: inline-block;
			width: 200px;
		}
		ul li .btn {
			margin: 20px 0 0 200px;
			color: #eee;
			width: 200px;
			height: 50px;
			background-color: dodgerblue;
			-webkit-border-radius: 10px;
		}
		#result {
			margin: 20px auto;
			width: auto;
			height: 200px;
			font-size: 20px;
			color: #666;
			background-color: #eee;
		}
	</style>
</head>
<body>
<form action="/dataGen.php" method="post">
<ul>
	<li><label for="day_count">每天条数</label><input required type="number" name="conf[day_count]" id="day_count" value="1"/></li>
	<li><label for="station_region_id">station_region_id</label><input required type="number" name="conf[station_region_id]" id="station_region_id" value="1"/></li>
	<li><label for="delivery_time">delivery_time</label><input required type="date" name="conf[delivery_time]" id="delivery_time" value="<?php echo date("Y-m-d")?>"/></li>
	<li><label for="city_id">city_id</label><input required type="number" name="conf[city_id]" id="city_id" value="1"/></li>
	<li><label for="receive_customer_id">receive_customer_id</label><input required type="number" name="conf[receive_customer_id]" id="receive_customer_id" value="1"/></li>
	<li><label for="warehouse_id">warehouse_id</label><input required type="number" name="conf[warehouse_id]" id="warehouse_id" value="1"/></li>
	<li><label for="dist_service_id">dist_service_id</label><input required type="number" name="conf[dist_service_id]" id="dist_service_id" value="1"/></li>
	<li><label for="batch_id">batch_id</label><input required type="number" name="conf[batch_id]" id="batch_id" value="24"/></li>
	<li><label for="distribution_type">distribution_type</label><input required type="number" name="conf[distribution_type]" id="distribution_type" value="10"/></li>
	<li><label for="waybill_status">waybill_status</label><input required type="number" name="conf[waybill_status]" id="waybill_status" value="100"/></li>
	<li><label for="route_id">route_id</label><input required type="number" name="conf[route_id]" id="route_id" value="3"/></li>
	<li><label for="seq">seq</label><input required type="number" name="conf[seq]" id="seq" value="0"/></li>
	<li><label for="prefix">运单前缀</label><input required type="text" name="conf[prefix]" id="prefix" value="w_"/></li>
	<li><label for="days">持续天数</label><input required type="number" name="conf[days]" id="days" value="1"/></li>
	<li>
		<label for="db">db</label>
		<select id="db" name="conf[db]">
		<option value="tmc">dev</option>
		<option value="tmc_test">test</option>
		</select>
	</li>
	<li><input class="btn" type="submit" name="" id="" value="提交" /></li>
</ul>
</form>

<pre id="result"></pre>

<script type="text/javascript">
	var result = document.getElementById("result")
	var form = document.querySelector("form")
	form.addEventListener("submit", function(event) {
		event.preventDefault()
		if(!this.checkValidity()) {
			return false
		}

		var fdata = new FormData(this)
		var xhr = new XMLHttpRequest()
		xhr.open(this.method, this.action)
		xhr.onload = function(e) {
			if(xhr.status == 200 && xhr.responseText) {
				result.innerHTML = xhr.responseText
			} else {
				result.innerHTML = "FAIL"
			}
		}.bind(this)
		xhr.send(fdata)
	}, false)
</script>
</body>
</html>
