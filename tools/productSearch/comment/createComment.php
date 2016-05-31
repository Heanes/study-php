<?php
	require 'comment.model.php';
?>
<html>
<head>
	<title>创建评论</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="zh-CN" />
    <script type="text/javascript" src="http://files.cnblogs.com/Zjmainstay/jquery-1.6.2.min.js"></script>
</head>
<body>
<style>
#left{
	float:left;
	width:20%;
}
#right{
	float:left;
	width: 75%;
}
.product{
	float:left;
	clear:left;
}
.productList{
	float: left;
    width: 15%;
}
.buyer{
	float:left;
	width:20%;
}
.comment{
	border: 1px solid #CFCFCF;
	margin:3px;
}
.messageBody{
	float: left;
    width: 70%;
}
#commentCreator{
	float:left;
	clear:left;
}
.clr{
	clear:both;
}
</style>
<div id="container">
	<div id="left">
		<div id="productList">
			<?php echo Comment::getProductList(); ?>
		</div>
	</div>
	<div id="right">
	<div id="commentBoard"></div>
		<div id="commentCreator">
			<span>评论：</span>
			<textarea rows="5" cols="50" id="message"></textarea>
			<input type="button" value="提交" id="save"></input>
		</div>
		<div id="feedback"></div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	function bindListener(){
		$("#save").unbind().click(function(){
			var productId = $.trim($("input[name=product]:checked").val());
			var message = $.trim($("#message").val());
			if(productId == '') {
				$("#feedback").html('请在左侧选定产品！');
				setTimeout(function(){
					$("#feedback").empty();
				},5000);
				return false;
			}
			var data = {productId:productId,message:message,type:'save'};
			$.ajax({
				url:'createComment.process.php',
				type:'POST',
				data:data,
				dataType:'json',
				success:function(data){
					if(data.status == 1){
						//Reset Form
						$("#message").val('');
						$("#commentBoard").children("#product-"+productId).append(data.comment);
					}
					$("#feedback").html(data.msg);
					setTimeout(function(){
						$("#feedback").empty();
					},3000);
					bindListener();
				},
				error:function(msg){
					alert(msg);
				}
			});
		});
		$("input[name=product]").unbind().click(function(){
			var productId = $.trim($(this).val());
			if($("#product-"+productId).length){
				$("#commentBoard").children('div').hide();
				$("#product-"+productId).show();
				return true;
			}
			$.ajax({
				url:'createComment.process.php',
				type:'POST',
				data:{productId:productId,type:'loadComments'},
				dataType:'json',
				success:function(comments){
					$("#commentBoard").children('div').hide();
					$("#commentBoard").append(comments).show();
					bindListener();
				},
				error:function(msg){
					alert(msg);
				}
			});
		});
	}
	bindListener();
});
</script>
</body>
</html>