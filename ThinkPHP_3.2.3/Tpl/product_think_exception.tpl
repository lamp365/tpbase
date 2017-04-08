<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>这个.. 页面没有找到！！！</title>

	<style type="text/css">
		body{ margin:0; padding:0; background:#efefef; font-family:Georgia, Times, Verdana, Geneva, Arial, Helvetica, sans-serif; }
		div#mother{ margin:140px auto auto; width:943px; height:572px; position:relative; }
		div#errorBox{ background: url('./Public/Home/error404/404_bg.png') no-repeat top left; width:943px; height:572px; margin:auto; }
		div#errorText{ color:#33416E; padding:230px 0 0 446px;font-weight: bolder; }
		div#errorText p{ width:303px; font-size:14px; line-height:26px; }
		div.link{ /*background:#f90;*/ height:50px; width:145px; float:left; }
		div#home{ margin:20px 0 0 444px;background: url('./Public/Home/error404/return.png')}
		div#contact{ margin:20px 0 0 25px;background: url('./Public/Home/error404/connect.png')}
		h1{ font-size:40px; margin-bottom:35px; }
	</style>



</head>
<body>
<div id="mother">
	<div id="errorBox">
		<div id="errorText">
			<p>
				似乎你所寻找的网页，已移动或丢失了。
			<p>或者也许你只是键入，错误的一些东西。</p>
			如果该资源很重要，请联系管理员。
			</p>

			<p>
				火星不太安全呢，我用时光送你回地球。
			</p>
		</div>
		<a href="./index.php" title="返回首页">
			<div class="link" id="home"></div>
		</a>
		<a href="#" title="联系管理员">
			<div class="link" id="contact"></div>
		</a>
	</div>
</div>
</body>
</html>