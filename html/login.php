<?php
include_once 'includes/main.inc.php';


if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
    $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $redirect");
}

 
$session = new session(__SESSION_NAME__);
$message = "";
$webpage = $dir = dirname($_SERVER['PHP_SELF']) . "/view_backupsets.php";
if ($session->get_var('webpage') != "") {
	$webpage = $session->get_var('webpage');
}

if (isset($_POST['login'])) {

	$username = trim(rtrim($_POST['username']));
	$password = $_POST['password'];

	$error = false;
	if ($username == "") {
		$error = true;
		$message .= html::error_message("Please enter your username.");
	}
	if ($password == "") {
		$error = true;
		$message .= html::error_message("Please enter your password.");
	}
	if ($error == false) {
// 		$ldap = new ldap(__LDAP_HOST__,__LDAP_SSL__,__LDAP_PORT__,__LDAP_BASE_DN__);
		$login_user = new user($ldap,$username);
		$success = $login_user->authenticate($password);
		if ($success==0) {
			$session_vars = array('login'=>true,
                'username'=>$username,
                'password'=>$password,
                'timeout'=>time(),
                'ipaddress'=>$_SERVER['REMOTE_ADDR']
        	);
            $session->set_session($session_vars);
            $ldap->set_bind_user($login_user->get_user_rdn());
            $ldap->set_bind_pass($password);


			$location = "https://" . $_SERVER['SERVER_NAME'] . $webpage;
        	header("Location: " . $location);
		}
		else {
			$message .= html::error_message("Invalid username or password. Please try again.");
		}
	}
}



?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo __TITLE__; ?></title>
		<link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css" type="text/css">
		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	</head>
	<body OnLoad="document.login.username.focus();">
		<nav class="navbar navbar-inverse navbar-static-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<div class="navbar-brand">
						<?php echo __TITLE__; ?>
					</div>
				</div>
			</div>
		</nav>
		
		<div class="container-fluid">

			<div class='row'>
				<div class='col-md-3 col-md-offset-4'>
					<form action='login.php' method='post' name='login'>
						<div class="form-group">
							<label for="username">Username: </label>
							<div class="input-group">
								<input class='form-control' type='text' name='username' id="username" tabindex='1' placeholder='Username' value='<?php if (isset($username)) { echo $username; } ?>'> 
								<span class="input-group-addon"><span class='glyphicon glyphicon-user'></span></span>
							</div>
						</div>
						<div class="form-group">
							<label>Password: </label>
							<div class="input-group">
								<input class='form-control' type='password' name='password' placeholder='Password' tabindex='2'>
								<span class="input-group-addon"><span class='glyphicon glyphicon-lock'></span></span>
							</div>
						</div>
						<button type='submit' name='login' class='btn btn-primary'>Login</button>
					</form>
	
	
					<?php if (isset($message)) { 
						echo $message;
                                        } ?>
	<br>
					<em>&copy 2018 University of Illinois Board of Trustees</em>
				</div>
			</div>
		</div>
