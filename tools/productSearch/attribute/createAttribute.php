<?php
	require 'attribute.model.php';
?>
<html>
<head>
	<title>创建属性</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="zh-CN" />
    <script type="text/javascript" src="http://files.cnblogs.com/Zjmainstay/jquery-1.6.2.min.js"></script>
</head>
<body>
<style>
.item{
	margin:3px;
}
.label{
	float: left;
    width: 85px;
}
</style>
<div id="container">
	<div class="item">
		<span class="label">属性名：</span>
		<input type="text" id="name"></input>
	</div>
	<div class="item">
		<span class="label">父分类：</span>
		<?php echo Attribute::getParentList(); ?>
	</div>
	<div class="item">
		<span class="label">排序：</span>
		<?php echo Attribute::getParentList(0,false); ?>
	</div>
	<div class="item">
		<input type="button" value="保存" id="save"></input>
	</div>
	<div class="item">
		<div id="feedback"></div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	function bindListener(){
		$("#save").unbind().click(function(){
			var name = $.trim($("#name").val());
			var desc = $.trim($("#desc").val());
			var parent = $.trim($("#parentList").val());
			var order = $.trim($("#orderList").val());
			if(name == '') return false;
			var data = {name:name,desc:desc,parent:parent,order:order,type:'save'};
			$.ajax({
				url:'createAttribute.process.php',
				type:'POST',
				data:data,
				dataType:'json',
				success:function(data){
					if(data.status == 1){
						//Reset Form
						$("#container input[type='text']").each(function(){
							$(this).val('');
						});
						var parentListParent = $("#parentList").parent();
						$("#parentList").remove();
						parentListParent.append(data.parentList);
						var orderListParent = $("#orderList").parent();
						$("#orderList").remove();
						orderListParent.append(data.orderList);
					}
					//Tips
					$("#feedback").html(data.msg);
					setTimeout(function(){
						$("#feedback").empty();
					},3000);
					bindListener();
				},
				error:function(msg){
					$("#feedback").html(result);
					setTimeout(function(){
						$("#feedback").empty();
					},5000);				
				}
			});
		});
		
		$("#parentList").unbind().change(function(){
			var pid = $.trim($(this).val());
			$.ajax({
				url:'createAttribute.process.php',
				type:'POST',
				data:{pid:pid,type:'changeOrder'},
				dataType:'json',
				success:function(order){
					var orderListParent = $("#orderList").parent();
					$("#orderList").remove();
					orderListParent.append(order);
				},
				error:function(msg){
					alert('加载排序失败！');
				}
			});
		});
	}
	bindListener();
});
</script>
</body>
</html>