<?php
require_once 'includes/main.inc.php';
require_once 'includes/session.inc.php';
        
if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
	$redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: $redirect");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?php echo settings::get_title() . (isset($title)?" - $title":''); ?></title>
                		
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script type='text/javascript' language='javascript' src='vendor/components/jquery/jquery.min.js'></script>
	<script type='text/javascript' language='javascript' src='vendor/datatables/media/js/jquery.dataTables.min.js'></script>
	<link rel="stylesheet" type="text/css" href="vendor/datatables/media/css/jquery.dataTables.css">

	<!-- Bootstrap -->
	<link rel="stylesheet" type="text/css" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
                
	<!-- Local javascript methods -->
	<script type="text/javascript" language='javascript' src="includes/script.js"></script>

	<!-- Icon -->
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon"/>

</head>
<body>
<nav class="navbar navbar-inverse navbar-static-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#archive-accounting-nav-collapse" aria-expanded="false">
		        	<span class="sr-only">Toggle navigation</span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="navbar-brand"><?php echo settings::get_title(); ?></div>
			<div class="collapse navbar-collapse pull-right">
				<a type="button" class="btn btn-danger btn-sm navbar-btn navbar-right hidden-xs" style="margin-right:0" href="logout.php">Logout</a>
			</div>
	</div>
</nav>
		
<div class="container-fluid">
	<div class="row">
		<div class="col-md-2">
			<div class="sidebar-nav">
				<ul class="nav nav-pills nav-stacked">
					<li><a href='index.php'>Home</a></li>
					<li><a href='add_tape.php'>Add Tapes</a></li>
					<li><a href='view_tapes.php'>View Tapes</a></li>
					<li><a href='edit_tapes.php'>Edit Tapes</a></li>
					<hr>
					<li><a href='add_container.php'>Add Containers</a></li>
					<li><a href='view_all_containers.php'>View Containers</a></li>
					<li><a href='edit_containers.php'>Edit Containers</a></li>
					<hr>
					<li><a href='add_backupset.php'>Add Backup Set</a></li>
					<li><a href='view_backupsets.php'>View Backup Sets</a></li>
					<hr>
					<li><a href='add_container_type.php'>Add Container and Tape Types</a></li>
					<li><a href='edit_types.php'>Edit Container and Tape Types</a></li>
					<li><a href='add_program.php'>Add Program</a></li>
					<hr>
					<li><a href='about.php'>About</a></li>
				</ul>
			</div>
		</div>
		<div class="col-md-10">
