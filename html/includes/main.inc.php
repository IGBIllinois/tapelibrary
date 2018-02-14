<?php

	ini_set('display_errors',1);
        //echo(get_include_path());
set_include_path(get_include_path().";../libs;../conf;");
include_once('../conf/settings.inc.php');
function my_autoloader($class_name) {
	if(file_exists("../libs/" . $class_name . ".class.inc.php")) {
            //echo("class = $class_name<BR>");
		require_once "../libs/" .$class_name . '.class.inc.php';
	}

}

spl_autoload_register('my_autoloader');


$ldap = new ldap(__LDAP_HOST__,__LDAP_SSL__,__LDAP_PORT__,__LDAP_BASE_DN__);
$db = new db(mysql_host,mysql_database,mysql_user,mysql_password);

//$adldap = new ldap(__AD_LDAP_HOST__,false,__AD_LDAP_PORT__,__AD_LDAP_PEOPLE_OU__);
ini_set('session.cache_limiter','public');
session_cache_limiter(false);

?>
