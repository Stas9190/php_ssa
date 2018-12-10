<!-- Base -->
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title></title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		
		<link rel="stylesheet" href="static/AdminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="static/AdminLTE/bower_components/font-awesome/css/font-awesome.min.css">
		<link rel="stylesheet" href="static/AdminLTE/bower_components/Ionicons/css/ionicons.min.css">
		<link rel="stylesheet" href="static/AdminLTE/dist/css/AdminLTE.min.css">
		<link rel="stylesheet" href="static/AdminLTE/dist/css/skins/skin-green.min.css">
		<link rel="stylesheet" href="static/AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
		<link rel="stylesheet" href="static/AdminLTE/bower_components/select2/dist/css/select2.min.css">
		<link rel="stylesheet" href="static/AdminLTE/dist/css/skins/_all-skins.min.css">
		<!-- собственные стили -->
		<link rel="stylesheet" href="static/css/mycss.css">
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<div class="wrapper">
			<header class="main-header">
				<!-- Logo -->
				<a href="index.php" class="logo">
					<!-- mini logo for sidebar mini 50x50 pixels -->
					<span class="logo-mini"></span>
					<!-- logo for regular state and mobile devices -->
					<span class="logo-lg"></span>
				</a>
				<!-- Header Navbar -->
				<nav class="navbar navbar-static-top" role="navigation">
					<!-- Sidebar toggle button-->
					<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
						<span class="sr-only">Toggle navigation</span>
					</a>
				</nav>
			</header>
			<aside class="main-sidebar">
				<section class="sidebar">
					<!-- Sidebar Menu -->
					<ul class="sidebar-menu" data-widget="tree">
						<li class="header">ОСНОВНОЕ МЕНЮ</li>
						<!-- Optionally, you can add icons to the links -->
						<li class="active"><a href="?act=base"><i class="fa fa-tasks"></i> <span>Таблица данных</span></a></li>
						<li class="active treeview menu-open">
							<a href=""><i class="fa fa-book"></i><span>Справочники</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu">
								<li><a href="?act=modules"><i class="fa fa-gears"></i> Модули</a></li>
								<li><a href="?act=objects"><i class="fa fa-object-group"></i> Объекты контроля</a></li>
								<li><a href="?act=executors"><i class="fa fa-user"></i> Исполнители</a></li>
							</ul>
						</li>
						<li class="active"><a href="?act=formation"><i class="fa fa-file-pdf-o"></i> <span>Формировать PDF</span></a></li>
						
					</ul>
					<!-- /.sidebar-menu -->
				</section>
			<!-- /.sidebar -->
			</aside>
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<!-- Main content -->
				<section class="content container-fluid">
				<?php 
					if ($CONTENT != "") 
					{
						if ($TYPE_CONTENT == "html")
							echo $CONTENT;
						else if ($TYPE_CONTENT == "file")
							require $CONTENT;
						else
							echo 'Тип содержимого контента не установлен!';
					}
				?>
				</section>
			<!-- /.content -->
			</div>
			<!-- /.content-wrapper -->
			<!-- Main Footer -->
			<footer class="main-footer">
			</footer>
		</div>
		<script src="static/AdminLTE/bower_components/jquery/dist/jquery.js"></script>
		<script src="static/AdminLTE/bower_components/jquery-ui/jquery-ui.min.js"></script>
		<script src="static/AdminLTE/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
		<script src="static/AdminLTE/dist/js/adminlte.min.js"></script>
		<script src="static/AdminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
		<script src="static/AdminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
		<script src="static/AdminLTE/bower_components/select2/dist/js/select2.full.min.js"></script>
		<script src="static/Scripts/dt.js"></script>
	</body>
</html>