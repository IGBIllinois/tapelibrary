<?php

ini_set('display_errors',1);

set_include_path(get_include_path().";../libs;../conf;");


function my_autoloader($class_name) {
	if(file_exists("../libs/" . $class_name . ".class.inc.php")) {

		require_once "../libs/" .$class_name . '.class.inc.php';
	}

}
spl_autoload_register('my_autoloader');

include_once('../conf/settings.inc.php');

require_once '../vendor/autoload.php';


$ldap = new ldap(__LDAP_HOST__,__LDAP_SSL__,__LDAP_PORT__,__LDAP_BASE_DN__);
$db = new db(mysql_host,mysql_database,mysql_user,mysql_password);

// These lines allow a user to hit the Back button and return to a previously
// submitted form
ini_set('session.cache_limiter','public');
session_cache_limiter(false);

?>
