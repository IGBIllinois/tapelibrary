<?php

$include_paths = array(__DIR__ . '/../../libs');
set_include_path(get_include_path() . ":" . implode(':',$include_paths));

require_once __DIR__ . '/../../conf/app.inc.php';
require_once __DIR__ . '/../../conf/settings.inc.php';
require_once __DIR__ . '/../../vendor/autoload.php';

function my_autoloader($class_name) {
	if(file_exists(__DIR__ . "/../../libs/" . $class_name . ".class.inc.php")) {
		require_once $class_name . '.class.inc.php';
	}
}
spl_autoload_register('my_autoloader');

if (settings::get_debug()) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
}

date_default_timezone_set(settings::get_timezone());


$db = new \IGBIllinois\db(settings::get_mysql_host(),
			settings::get_mysql_database(),
			settings::get_mysql_user(),
			settings::get_mysql_password(),
			settings::get_mysql_ssl(),
			settings::get_mysql_port()
			);

$ldap = new \IGBIllinois\ldap(settings::get_ldap_host(),
                        settings::get_ldap_base_dn(),
                        settings::get_ldap_port(),
                        settings::get_ldap_ssl(),
                        settings::get_ldap_tls());
if (settings::get_ldap_bind_user() != "") {
	$ldap->bind(settings::get_ldap_bind_user(),settings::get_ldap_bind_password());
}

// These lines allow a user to hit the Back button and return to a previously
// submitted form
ini_set('session.cache_limiter','public');
session_cache_limiter(false);

?>
