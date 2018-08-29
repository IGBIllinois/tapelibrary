<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo __TITLE__.(isset($title)?" - $title":''); ?></title>
		<link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css" type="text/css"/>
		<link rel="stylesheet" href="includes/select2/css/select2.css" type="text/css" />
		<link rel="stylesheet" href="includes/main.inc.css" type="text/css"/>
		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon"/>
		<script type="text/javascript" src="includes/select2/js/select2.full.js"></script>
		<script type="text/javascript" src="includes/main.inc.js"></script>

<?php

require_once '../conf/settings.inc.php';
require_once '../libs/db.class.inc.php';


?>