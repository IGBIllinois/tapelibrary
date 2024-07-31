<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$include_paths = array(__DIR__ . '/../../libs');
set_include_path(get_include_path() . ":" implode(':',$include_paths));

require_once __DIR__ . '/../../conf/app.inc.php';
require_once __DIR__ . '/../../conf/settings.inc.php';
require_once __DIR__ . '/../../vendor/autoload.php';

function my_autoloader($class_name) {
	if(file_exists(__DIR__ . "/../../libs/" . $class_name . ".class.inc.php")) {
		require_once $class_name . '.class.inc.php';
	}
}
spl_autoload_register('my_autoloader');

$ldap = new \IGBIllinois\ldap(__LDAP_HOST__,
                        __LDAP_BASE_DN__,
                        __LDAP_PORT__,
                       __LDAP_SSL__);

$db = new \IGBIllinois\db(mysql_host,
			mysql_database,
			mysql_user,
			mysql_password);

// These lines allow a user to hit the Back button and return to a previously
// submitted form
ini_set('session.cache_limiter','public');
session_cache_limiter(false);

?>
