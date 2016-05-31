<?php
	require 'product.model.php';
?>
<html>
<head>
	<title>创建产品</title>
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
    width: 185px;
}
.del{
	cursor:pointer;
	color:red;
}
</style>
<div id="container">
	<div class="item">
		<span class="label">产品名：</span>
		<input type="text" id="name"></input>
	</div>
	<div class="item">
		<span class="label">产品编号：</span>
		<input type="text" id="sku"></input>
	</div>
	<div class="item">
		<span class="label">分类：</span>
		<?php echo Product::getCategoryList(); ?>
	</div>
	<div class="item">
		<span class="label">价格（原价）：</span>
		<input type="text" id="origPrice"></input>
	</div>
	<div class="item">
		<span class="label">价格（现价）：</span>
		<input type="text" id="price"></input>
	</div>
	<div class="item">
		<span class="label">库存：</span>
		<input type="text" id="stock"></input>
	</div>
	<div class="item">
		<span class="label">产品属性：</span>
		<?php echo Product::getAttributeList(); ?>
		<div id="selfAttributes"></div>
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
	var attrTpl = '<div class="attr"><input type="hidden" name="attrValue" value="{value}"/><span>{attr}</span><span class="del">x</span></div>';
	function bindListener(){
		$("#save").unbind().click(function(){
			var name = $.trim($("#name").val());
			var sku = $.trim($("#sku").val());
			var catid = $.trim($("#categoryList").val());
			var origPrice = $.trim($("#origPrice").val());
			var price = $.trim($("#price").val());
			var stock = $.trim($("#stock").val());
			var attrs = [];
			$('input[name=attrValue]').each(function(){
				attrs.push($.trim($(this).val()));
			});
			if(name == '') return false;
			
			var data = {name:name,sku:sku,catid:catid,origPrice:origPrice,price:price,stock:stock,attrs:attrs};
			$.ajax({
				url:'createProduct.process.php',
				type:'POST',
				data:data,
				dataType:'json',
				success:function(data){
					if(data.status == 1){
						//Reset Form
						$("#container input[type='text']").each(function(){
							$(this).val('');
						});
						$("#container select").each(function(){
							$(this).get(0).selectedIndex = 0;
						});
						$("#selfAttributes").empty();
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
		
		$("#attributeList").unbind().change(function(){
			var selected = $(this).find("option:selected");
			if(selected.hasClass('root')) return false;
			if(selected.hasClass('parentAttr')) {
				var option = selected;
				do{
					option = option.next('option');
					if(option.length && !option.hasClass('parentAttr')){
						var value = $.trim(option.val());
						var attr = $.trim(option.text()).replace('-','');
						$("#selfAttributes").append(attrTpl.replace('{value}',value).replace('{attr}',attr));
					}
				}while(option.length && !option.hasClass('parentAttr'));
			}else {
				var value = $.trim(selected.val());
				var attr = $.trim(selected.text()).replace('-','');
				if($("input[name=attrValue][value="+value+"]").length != 0) return false;
				$("#selfAttributes").append(attrTpl.replace('{value}',value).replace('{attr}',attr));
			}
			bindListener();
		});
		
		$(".del").unbind().click(function(){
			$(this).parent().remove();
		});
	}
	bindListener();
});
</script>
</body>
</html>