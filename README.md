# Tape Library

- Keep track of Backup Tapes
- Use on https://www-app.igb.illinois.edu/tapelibrary/

# Installation

## Prerequisites
- PHP
- PHP Mysql
- PHP LDAP
- composer

1. Git clone https://github.com/IGBIllinois/tapelibrary/ or download a tagged release
```
git clone https://github.com/IGBIllinois/tapelibrary.git
```
2. Add apache config to apache configuration to point to html folder
```
Alias /tapelibrary /var/www/tapelibrary/html
<Location /accounting>
	AllowOverride None
	Require all granted
</Location>
```
3. Create mysql database
```
CREATE database tapelibrary CHARACTER SET utf8;
```
4. Run sql/tapelibrary.sql on the mysql server.
```
mysql -u root -p tapelibrary < sql/tapelibrary.sql
```
5. Create a user/password on the mysql server which has select/insert/delete/update permissions on the cluster_accounting database.
```
CREATE USER 'tapelibrary'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD';
GRANT SELECT,INSERT,DELETE,UPDATE ON tapelibrary.* to 'tapelibrary'@'localhost';
```
6.  Copy conf/settings.inc.php.dist to conf/settings.inc.php.  Edit the settings to reflect your settings.
```
cp conf/settings.inc.php.dist conf/settings.inc.php
```
5.  Run composer to install php dependencies
```composer install```

