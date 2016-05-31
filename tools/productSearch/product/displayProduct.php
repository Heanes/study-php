<?php
	require 'product.model.php';
?>
<!DOCTYPE>
<html>
<head>
	<title>产品展示搜索</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="zh-CN" />
    <script type="text/javascript" src="http://files.cnblogs.com/Zjmainstay/jquery-1.6.2.min.js"></script>
</head>
<body>
<style>
.product{float:left;width: 20%;}
body{font-size:14px;}
.p_price span{color:#FF0000;}
del{color:#C0C0FF;}
li{list-style:none;}
#searchProduct ul{clear:left;float:left;margin: 3px 0;}
.parentAttr{font-size:16px;font-weight:bold;float: left;margin-right: 10px;width: 100px;}
.searchAttr{border:1px solid #CFCFCF;height:20px;line-height:20px;float:left;cursor:pointer;margin: 0 3px;padding: 0 3px;}
#productList{margin: 0 auto;width: 960px;clear:left;}
.selectedAttr{background-color:#ABC;}
#searchBar{clear:left;float:left;margin-left: 40px;}
#categories span{margin-left:40px;}
#emptyProduct{display:none;}
</style>
<div id="container">
	<!-- 显示分类 -->
	<div id="categories">
		<span class="parentAttr">产品分类</span>
		<?php echo Product::getCategoryList(); ?>
	</div>
	<!-- 显示多属性搜索选项 -->
	<div id="searchProduct">
		<?php echo Product::getAttributeList(true); ?>
		<input type="button" id="searchBar" value="搜索" />
	</div>
	<!-- 显示搜索结果 -->
	<div id="productList">
		<?php echo Product::getProductList(); ?>
		<div id="emptyProduct">没有搜索到结果！</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	// 搜索选项选中
	$(".searchAttr").click(function(){
		$(this).toggleClass('selectedAttr');
	});
	//分类选定触发搜索
	$("#categoryList").change(function(){
		var catid = $.trim($(this).val());
		if(isNaN(catid)) return false;
		
		$.ajax({
			url:'displayProduct.process.php',
			type:'POST',
			data:{catid:catid,type:'cate'},
			dataType:'json',
			success:function(data){
				if(data.status == 1){
					$(".product").hide();				//先将所有产品隐藏，后面在通过搜索结果显示相应的产品
					if(data.products.length == 0){		//如果搜索结果为空
						$("#emptyProduct").show();		//显示“没有搜索到结果！”
					}else {								//否则，隐藏“没有搜索到结果！”，并逐个显示搜索结果中的产品
						$("#emptyProduct").hide();
						$.each(data.products,function(i){
							$("#product-"+data.products[i]).show();
						});
					}
				}else {
					alert(data.msg);
				}
			},
			error:function(msg){
				alert(msg);
			}
		});
	});
	//搜索按钮触发搜索
	$("#searchBar").click(function(){
		if($(".selectedAttr").length == 0) {
			$("#categoryList").change();			//若搜索属性为空，则仅根据分类进行搜索（清除所有选中属性的情况）
			return false;
		}
		
		//进行搜索属性拼接，同级属性(OR)用','分割，不同级属性（AND）用'|'分割
		var searchString = '';
		var searchArray = [];
		$("#searchProduct ul").each(function(){
			$(".selectedAttr",$(this)).each(function(){
				var attr = $.trim($(this).attr('attr'));
				if(!isNaN(attr)) {
					searchString += attr + ',';
					searchArray.push(attr);
				}
			})
			searchString = searchString.substr(0,searchString.length-1) + '|';
		});
		searchString = searchString.substr(0,searchString.length-1);
		
		if(searchString == '') return false;
		
		var catid = $.trim($("#categoryList").val());
		if(isNaN(catid)) catid = 0;
		
		$.ajax({
			url:'displayProduct.process.php',
			type:'POST',
			data:{searchString:searchString,catid:catid,type:'attr'},
			dataType:'json',
			success:function(data){
				if(data.status == 1){
					$(".product").hide();                   //先将所有产品隐藏，后面在通过搜索结果显示相应的产品
					if(data.products.length == 0){          //如果搜索结果为空
						$("#emptyProduct").show();          //显示“没有搜索到结果！”
					}else {                                 //否则，隐藏“没有搜索到结果！”，并逐个显示搜索结果中的产品
						$("#emptyProduct").hide();
						$.each(data.products,function(i){
							$("#product-"+data.products[i]).show();
						});
					}
				}else {
					alert(data.msg);
				}
			},
			error:function(msg){
				alert(msg);
			}
		});
	});
});
</script>
</body>
</html>