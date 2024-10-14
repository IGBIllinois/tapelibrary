<?php
require_once 'includes/header.inc.php';
require_once 'includes/session.inc.php';

?>

<h3>About</h3>
<div class='row'>
<div class='col-md-8 col-lg-8 col-xl-8'>
<table class='table table-bordered table-condensed'>
<tr><td>Code Website</td></td><td><a href='<?php echo settings::get_website_url(); ?>' target='_blank'><?php echo settings::get_website_url(); ?></a></td></tr>
<tr><td>App Version</td><td><?php echo settings::get_version(); ?></td></tr>
<tr><td>Webserver Version</td><td><?php echo \IGBIllinois\Helper\functions::get_webserver_version(); ?></td></tr>
<tr><td>MySQL Version</td><td><?php echo $db->get_version(); ?></td>
<tr><td>PHP Version</td><td><?php echo phpversion(); ?></td></tr>
<tr><td>PHP Extensions</td><td><?php 
$extensions_string = "";
foreach (\IGBIllinois\Helper\functions::get_php_extensions() as $row) {
	$extensions_string .= implode(", ",$row) . "<br>";
}
echo $extensions_string;
 ?></td></tr>

</table>
</div>
</div>
<div class='row'>
<h3>Settings</h3>
<div class='col-md-8 col-lg-8 col-xl-8'>
<table class='table table-bordered table-condensed'>
<tr><td>LDAP_HOST</td><td><?php echo settings::get_ldap_host(); ?></td></tr>
<tr><td>LDAP_BASE_DN</td><td><?php echo settings::get_ldap_base_dn(); ?></td></tr>
<tr><td>LDAP_ADMIN_GROUP</td><td><?php echo settings::get_ldap_admin_group(); ?></td></tr>
<tr><td>LDAP_PORT</td><td><?php echo settings::get_ldap_port(); ?></td></tr>
<tr><td>LDAP_SSL</td><td><?php echo settings::get_ldap_ssl() ? 'true' : 'false';  ?></td></tr>
<tr><td>LDAP_TLS</td><td><?php echo settings::get_ldap_tls() ? 'true' : 'false'; ?></td></tr>
<tr><td>TIMEZONE</td><td><?php echo settings::get_timezone(); ?></td></tr>
<tr><td>DB_HOST</td><td><?php echo settings::get_mysql_host(); ?></td></tr>
<tr><td>DB_NAME</td><td><?php echo settings::get_mysql_database(); ?></td></tr>
<tr><td>TITLE</td><td><?php echo settings::get_title(); ?></td></tr>
<tr><td>SESSION_TIMEOUT (seconds)</td><td><?php echo settings::get_session_timeout(); ?></td></tr>
</table>
</div>
</div>
<?php
require_once 'includes/footer.inc.php';

?>

