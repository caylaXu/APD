<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>404 Page Not Found</title>
<style type="text/css">

::selection { background-color: black; color: white; }
::-moz-selection { background-color: black; color: white; }

body {
	padding: 0;
	margin: 0;
	background-color: white;
	font: 14px Arial, "microsoft yahei", sans-serif;
	color: black;
}

a {
	color: inherit;
	text-decoration: none;
}
a:hover{
	text-decoration: underline;
}
p{
	margin: 0;
}

.director{
	text-align: center;
	padding-top: 200px;
}
.text{
	margin-top: -105px;
}
.link{
	margin-top: 50px;
}
.icon{
	display: inline-block;
	vertical-align: middle;
	background-repeat: no-repeat;
	background-position: center;
	background-size: auto;
	position: relative;
	top: -1px;
}
.icon-back{
	background-image: url(/resource/asset/img/icon/back.png);
    width: 13px;
    height: 13px;
	top: -3px;
}
.text-foot{
	margin-left: 5px;
}
.bold{
	font-weight: bold;
}


</style>
</head>
<body>
	<div id="container">
		<div class="no-data">
			<div class="director">
				<img src="/resource/asset/img/404.png" alt="" />
				<p class="text bold">哎呀~迷路啦，这里风景也不错哦~</p>
				<p class="link">
					<a href="/">返回首页<span class="icon icon-back text-foot"></span></a>
				</p>
			</div>
		</div>
	</div>
</body>
</html>
