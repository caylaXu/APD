<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>APD管理后台</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.5 -->
	<link rel="stylesheet" href="{$CommonUrl}/public/bootstrap/css/bootstrap.min.css">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="{$CommonUrl}/public/adminlte/plugins/colorpicker/bootstrap-colorpicker.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="{$CommonUrl}/public/adminlte/plugins/fontawesome/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="{$CommonUrl}/public/adminlte/plugins/ionicons/css/ionicons.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="{$CommonUrl}/public/adminlte/css/AdminLTE.min.css">
	<!-- DataTables -->
	<link rel="stylesheet" href="{$CommonUrl}/public/adminlte/plugins/datatables/dataTables.bootstrap.css">
	<!-- AdminLTE Skins. Choose a skin from the css/skins
		 folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="{$CommonUrl}/public/adminlte/css/skins/_all-skins.min.css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="{$CommonUrl}/public/respondjs/html5shiv.min.js"></script>
	<script src="{$CommonUrl}/public/respondjs/respond.min.js"></script>
	<![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

	<header class="main-header">
		<!-- Logo -->
		<a href="index2.html" class="logo">
			<!-- mini logo for sidebar mini 50x50 pixels -->
			<span class="logo-mini"><b>APD</b></span>
			<!-- logo for regular state and mobile devices -->
			<span class="logo-lg"><b>APD</b>管理后台</span>
		</a>
		<!-- Header Navbar: style can be found in header.less -->
		<nav class="navbar navbar-static-top" role="navigation">
			<!-- Sidebar toggle button-->
			<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
				<span class="sr-only">Toggle navigation</span>
			</a>
			<div class="navbar-custom-menu">
				<ul class="nav navbar-nav">
					<!-- User Account: style can be found in dropdown.less -->
					<li class="dropdown user user-menu">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<img src="{$CommonUrl}/public/adminlte/img/user2-160x160.jpg" class="user-image" alt="User Image">
							<span class="hidden-xs">{$UserName}</span>
						</a>
						<ul class="dropdown-menu">
							<!-- User image -->
							<li class="user-header">
								<img src="{$CommonUrl}/public/adminlte/img/user2-160x160.jpg" class="img-circle" alt="User Image">
								<p>
									Alexander Pierce - Web Developer
									<small>Member since Nov. 2012</small>
								</p>
							</li>
							<!-- Menu Footer-->
							<li class="user-footer">
								<div class="pull-right">
									<a href="/backend/Login/logout" class="btn btn-default btn-flat">Sign out</a>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>
	</header>
	<!-- Left side column. contains the logo and sidebar -->
	<aside class="main-sidebar">
		<!-- sidebar: style can be found in sidebar.less -->
		<section class="sidebar">
			<!-- sidebar menu: : style can be found in sidebar.less -->
			<ul class="sidebar-menu">
				<li class="treeview">
					<a href="#">
						<i class="fa fa-dashboard"></i>
						<span>安装包管理</span>
						<i class="fa fa-angle-left pull-right"></i>
					</a>
					<ul class="treeview-menu">
						<li><a href="/admin/upload/index"><i class="fa fa-circle-o"></i> 查看历史</a></li>
						<li><a href="/admin/upload/upload"><i class="fa fa-circle-o"></i> 上传APP</a></li>
					</ul>
				</li>
				<li class="treeview">
					<a href="#">
						<i class="fa fa-files-o"></i>
						<span>主题管理</span>
						<i class="fa fa-angle-left pull-right"></i>
					</a>
					<ul class="treeview-menu">
						<li><a href="/admin/theme/index"><i class="fa fa-circle-o"></i> 查看主题</a></li>
						<li><a href="/admin/theme/add"><i class="fa fa-circle-o"></i> 添加主题</a></li>
					</ul>
				</li>
			</ul>
		</section>
		<!-- /.sidebar -->
	</aside>

	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">