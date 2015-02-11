<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="bootstrap, techsupport, EF, monitoring, case management, report, knowledge base">
	<meta name="author" content="EF, TechSupport">
	<title>EC_Support - <?php echo $title?></title>

	<!-- The styles -->
	<link href="/css/bootstrap-cerulean.css" rel="stylesheet">
	<style type="text/css">
	  body {
		padding-bottom: 40px;
	  }
	  .sidebar-nav {
		padding: 9px 0;
	  }
	</style>
	<link href="/css/bootstrap-responsive.css" rel="stylesheet">
	<link href="/css/bootstrap-datepicker.css" rel="stylesheet">
	<link href="/css/daterangepicker.css" rel="stylesheet">
	<link href="/css/bootstrap-timepicker.css" rel="stylesheet">
	<link href="/css/datetimepicker.css" rel="stylesheet">
	<link href="/css/charisma-app.css" rel="stylesheet">
	<link href="/css/jquery-ui-1.8.21.custom.css" rel="stylesheet">
	<link href='/css/fullcalendar.css' rel='stylesheet'>
	<link href='/css/fullcalendar.print.css' rel='stylesheet'  media='print'>
	<link href='/css/chosen.css' rel='stylesheet'>
	<link href='/css/opa-icons.css' rel='stylesheet'>
	<link href='/css/timelinexml.sleek.css' rel='stylesheet'>
	<link href="/css/bootstrap-select.css" rel="stylesheet">

	<!-- The fav icon -->
	<link rel="shortcut icon" href="/img/favicon.ico">
		
</head>

<body>
	<?php if(!isset($no_visible_elements) || !$no_visible_elements)	{ ?>
	<!-- topbar starts -->
	<div class="navbar">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="/"> <img alt="EF Logo" src="/img/logo.png" /></a>
				
				<div class="top-nav">
					<ul class="nav navbar-nav" style = "margin-left: 40px;">
						<li><a class="ajax-link" href="/dashboard">Home</a></li>
						<li><a class="ajax-link" href="/cases">Cases</a></li>
						<li><a class="ajax-link" href="/monitoring">Monitoring</a></li>
						<li><a class="ajax-link" href="/knowledgebase">KB</a></li>
					</ul>
				</div><!--/.nav-collapse -->
				
				<!-- user dropdown starts -->
				<ul class="nav pull-right">
					<li class="notification-dropdown">
						<a href="#" class="trigger">
							<i class="icon icon-alert icon-white"></i>
							<span class="notification-count animate">0</span>
						</a>
						<div class="pop-dialog">
	                        <div class="pointer right">
	                            <div class="arrow"></div>
	                            <div class="arrow_border"></div>
	                        </div>
	                        <div class="body">
	                            <a href="#" class="close-icon"><i class="icon-remove-sign"></i></a>
	                            <div class="notifications">
	                                <h3>You have <span id="newNotificationCount">0</span> new notifications</h3>
	                                <div class="footer">
	                                    <a href="#" class="logout">View all notifications</a>
	                                </div>
	                            </div>
	                        </div>
                    	</div>
					</li>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<span class="hidden-phone">My Account</span>
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<li><a href="#">Profile</a></li>
							<li class="divider"></li>
							<li><a href="#">Logout</a></li>
						</ul>
					</li>
				</ul>
				<!-- user dropdown ends -->
			</div>
		</div>
	</div>
	<!-- topbar ends -->

	<?php } ?>
	<div class="container-fluid">
		<div class="row-fluid">
		<?php if(!isset($no_visible_elements) || !$no_visible_elements) { ?>
		
			
			
			<noscript>
				<div class="alert alert-block span10">
					<h4 class="alert-heading">Warning!</h4>
					<p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
				</div>
			</noscript>
			
			<div id="content" class="span12" style = 'margin-left: 0px;'>
			<!-- content starts -->
			<?php } ?>
